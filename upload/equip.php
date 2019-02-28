<?php
/*
	File:		equip.php
	Created: 	4/4/2016 at 11:59PM Eastern Time
	Info: 		Allows players to equip weapons and armor.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
if (!isset($_GET['slot'])) {
    $_GET['slot'] = '';
}
switch ($_GET['slot']) {
    case 'weapon':
        weapon();
        break;
    case 'armor':
        armor();
        break;
    default:
        die();
        break;
}
function weapon()
{
    global $db, $h, $userid, $ir, $api;
    //Make sure the Item ID is safe for database use.
    $_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs($_GET['ID']) : 0;
    //Select all its info.
    $id = $db->query("SELECT `weapon`, `itmid`, `itmname`
					FROM `inventory` AS `iv`
					LEFT JOIN `items` AS `it`
					ON `iv`.`inv_itemid` = `it`.`itmid`
					WHERE `iv`.`inv_id` = {$_GET['ID']}
					AND `iv`.`inv_userid` = {$userid}
					LIMIT 1");
    //Check that the item exists. If not, stop them here.
    if ($db->num_rows($id) == 0) {
        $db->free_result($id);
        alert('danger', "Uh Oh!", "This item does not exist.", true, 'inventory.php');
        die($h->endpage());
    } else {
        $r = $db->fetch_row($id);
        $db->free_result($id);
    }
    //Check that the item can be used as a weapon. If not, stop them here.
    if (!$r['weapon']) {
        alert('danger', "Uh Oh!", "The item you are trying to equip is not a weapon.", true, 'inventory.php');
        die($h->endpage());
    }
    //Check to be sure the user is trying to equip the item.
    if (isset($_POST['type'])) {
        //Check that the equipment slot is a valid slot. If not, lets stop them.
        if (!in_array($_POST['type'], array("equip_primary", "equip_secondary"), true)) {
            alert('danger', "Uh Oh!", "You cannot equip a weapon to an invalid slot.", true, 'inventory.php');
            die($h->endpage());
        }
        //Check to see if the chosen slot has a weapon equipped to it already. If true, give them their item back, and
        //log the unequip.
        if ($ir[$_POST['type']] > 0) {
			$api->user->giveItem($userid, $ir[$_POST['type']], 1);
            $slot = ($_POST['type'] == 'equip_primary') ? 'Primary Weapon' : 'Secondary Weapon';
            $weapname = $db->fetch_single($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$ir[$_POST['type']]}"));
            $api->SystemLogsAdd($userid, 'equip', "Unequipped {$weapname} as their {$slot}");
        }
        //Make the slot name friendly for the logger and user.
        if ($_POST['type'] == "equip_primary") {
            $slot_name = "Primary Weapon";
            $slot = 'Primary Weapon';
        } else {
            $slot_name = "Secondary Weapon";
            $slot = 'Secondary Weapon';
        }
        //Remove the item from their inventory, and equip it! Lets log that they equipped it, and give the user a friendly
        //event saying they equipped their item as a weapon.
		$api->user->takeItem($userid, $r['itmid'], 1);
        $db->query("UPDATE `users` SET `{$_POST['type']}` = {$r['itmid']} WHERE `userid` = {$userid}");
        $api->SystemLogsAdd($userid, 'equip', "Equipped {$r['itmname']} as their {$slot}.");
        alert('success', "Success!", "You have successfully equipped {$r['itmname']} as your weapon in your {$slot_name}
		    slot. If you had a previous weapon there, it was moved to your inventory.", true, 'inventory.php');
    } else {
        //Form to select what slot to equip the weapon to.
        echo "<h3>Equip a Weapon Form</h3>
		<hr />
		What slot do you want to equip your {$r['itmname']} in? If you have a weapon already equipped in that slot,
		it'll be moved to your inventory.<br />
		<form action='?slot=weapon&ID={$_GET['ID']}' method='post'>
            <select name='type' class='form-control' type='dropdown'>
                <option value='equip_primary'>Equip as Primary</option>
                <option value='equip_secondary'>Equip as Secondary</option>
            </select>
		<input type='submit' value='Equip Weapon' class='btn btn-primary'>
		</form>
		";
    }
    $h->endpage();
}

function armor()
{
    global $db, $h, $userid, $ir, $api;
    //Make sure the Item ID is safe for database work.
    $_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs($_GET['ID']) : 0;
    //Select the Item's info from the database.
    $id =
        $db->query(
            "SELECT `armor`, `itmid`, `itmname`
					FROM `inventory` AS `iv`
					LEFT JOIN `items` AS `it`
					ON `iv`.`inv_itemid` = `it`.`itmid`
					WHERE `iv`.`inv_id` = {$_GET['ID']}
					AND `iv`.`inv_userid` = $userid
					LIMIT 1");
    //Check that the item actually exists, if not, stop them.
    if ($db->num_rows($id) == 0) {
        $db->free_result($id);
        alert('danger', "Uh Oh!", "The item you're trying to equip does not exist.", true, 'inventory.php');
        die($h->endpage());
    } else {
        $r = $db->fetch_row($id);
        $db->free_result($id);
    }
    //Check if the item can actually be equipped as an armor. If not, stop here.
    if (!$r['armor']) {
        alert('danger', "Uh Oh!", "The item you're trying to equip cannot be equipped as armor.", true, 'inventory.php');
        die($h->endpage());
    }
    //Check to be sure that the player is trying to equip to a slot.
    if (isset($_POST['type'])) {
        //Check that the user is trying to equip the item as an armor.
        if ($_POST['type'] !== 'equip_armor') {
            alert('danger', "Uh Oh!", "You cannot equip an armor to an invalid slot.", true, 'inventory.php');
            die($h->endpage());
        }
        //Check that the user has an armor already equipped. If true, give them their old armor back, and log that it
        //was unequipped.
        if ($ir['equip_armor'] > 0) {
			$api->user->giveItem($userid, $ir['equip_armor'], 1);
            $armorname = $db->fetch_single($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$ir['equip_armor']}"));
            $api->SystemLogsAdd($userid, 'equip', "Unequipped {$armorname} as their armor.");
        }
        //Take the item from their inventory, equip it, log that it was equipped, and give a sucecss message to the player.
		$api->user->takeItem($userid, $r['itmid'], 1);
        $db->query("UPDATE `users`
				  SET `equip_armor` = {$r['itmid']}
				  WHERE `userid` = {$userid}");
        $api->SystemLogsAdd($userid, 'equip', "Equipped {$r['itmname']} as their armor.");
        alert('success', "Success!", "You have equipped your {$r['itmname']} into your armor slot. If you had an armor
		    there previously, it's been moved to your inventory.", true, 'inventory.php');
    } else {
        //Equip armor form.
        echo "<h3>Equip Armor Form</h3><hr />
	<form action='?slot=armor&ID={$_GET['ID']}' method='post'>
	You are attempting to equip your {$r['itmname']} as armor. If you have an armor on now, it'll be moved to your
	inventory.<br />
	<input type='hidden' name='type' value='equip_armor'  />
	<input type='submit' class='btn btn-primary' value='Equip Armor' />
	</form>";
    }
    $h->endpage();
}