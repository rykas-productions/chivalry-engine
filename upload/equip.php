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
    case 'potion':
        potion();
        break;
	case 'badge':
        badge();
        break;
	case 'ring':
        ring();
        break;
	case 'necklace':
        necklace();
        break;
	case 'pendant':
        pendant();
        break;
    default:
        alert('danger',"Uh Oh!","Please specific an action.",true,'inventory.php');
        die($h->endpage());
        break;
}
function weapon()
{
    global $db, $h, $userid, $ir, $api;
    //Make sure the Item ID is safe for database use.
    $_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs($_GET['ID']) : 0;
    //Select all its info.
    $id = $db->query("/*qc=on*/SELECT `weapon`, `itmid`, `itmname`, `effect1`, `effect2`, 
					`effect3`,  `effect1_on`, `effect2_on`, `effect3_on`
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
        if (!in_array($_POST['type'], array(slot_prim_wep, slot_second_wep), true)) 
        {
            alert('danger', "Uh Oh!", "You cannot equip a weapon to an invalid slot.", true, 'inventory.php');
            die($h->endpage());
        }
        if ($_POST['type'] == slot_prim_wep)
        {
            if (userHasEffect($userid, effect_injure_prim_wep))
            {
                $remainTime = TimeUntil_Parse(returnEffectDone($userid, effect_injure_prim_wep));
                alert('danger',"Uh Oh!","Your primary hand is injured and will not be usable for another {$remainTime}.", true, 'inventory.php');
                die($h->endpage());
            }
        }
        if ($_POST['type'] == slot_second_wep)
        {
            if (userHasEffect($userid, effect_injure_sec_wep))
            {
                $remainTime = TimeUntil_Parse(returnEffectDone($userid, effect_injure_sec_wep));
                alert('danger',"Uh Oh!","Your secondary hand is injured and will not be usable for another {$remainTime}.", true, 'inventory.php');
                die($h->endpage());
            }
        }
        //Check to see if the chosen slot has a weapon equipped to it already. If true, give them their item back, and
        //log the unequip.
		$slot = equipSlotParser($_POST['type']);
        if ($ir[$_POST['type']] > 0) 
			unequipUserSlot($userid, $_POST['type']);
		equipUserSlot($userid, $_POST['type'], $r['itmid']);
        alert('success', "Success!", "You have successfully equipped {$r['itmname']} as your {$slot}.", true, 'inventory.php', 'Back', true);
    } else {
        //Form to select what slot to equip the weapon to.
        echo "<h3>Equip a Weapon Form</h3>
		<hr />
		What slot do you want to equip your {$r['itmname']} in? If you have a weapon already equipped in that slot,
		it'll be moved to your inventory.<br />
		<form action='?slot=weapon&ID={$_GET['ID']}' method='post'>
			<select name='type' class='form-control' type='dropdown'>
				<option value='equip_primary'>Primary Weapon</option>
				<option value='equip_secondary'>Secondary Weapon</option>
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
            "/*qc=on*/SELECT `armor`, `itmid`, `itmname`, `effect1`, `effect2`, 
					`effect3`,  `effect1_on`, `effect2_on`, `effect3_on`
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
		$slot = equipSlotParser($_POST['type']);
        if ($ir['equip_armor'] > 0) 
			unequipUserSlot($userid, $_POST['type']);
		equipUserSlot($userid, $_POST['type'], $r['itmid']);
        alert('success', "Success!", "You have successfully equipped {$r['itmname']} as your {$slot}.", true, 'inventory.php', 'Back', true);
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
function potion()
{
    global $db,$api,$h,$userid,$ir;
    //Make sure the Item ID is safe for database work.
    $_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs($_GET['ID']) : 0;
    //Select the Item's info from the database.
    $id =
        $db->query(
            "/*qc=on*/SELECT `itmid`, `itmname`, `effect1`, `effect2`, 
					`effect3`,  `effect1_on`, `effect2_on`, `effect3_on`, `itmtype`
					FROM `inventory` AS `iv`
					LEFT JOIN `items` AS `it`
					ON `iv`.`inv_itemid` = `it`.`itmid`
					WHERE `iv`.`inv_id` = {$_GET['ID']}
					AND `iv`.`inv_userid` = $userid
					LIMIT 1");
    //Check that the item actually exists, if not, stop them.
    if ($db->num_rows($id) == 0) {
        $db->free_result($id);
        alert('danger', "Uh Oh!", "The potion you're trying to equip does not exist.", true, 'inventory.php');
        die($h->endpage());
    } else {
        $r = $db->fetch_row($id);
        $db->free_result($id);
    }
    if (($r['itmtype'] != 8) && ($r['itmtype'] != 7))
    {
        alert('danger', "Uh Oh!", "Cannot equip this item to your potion slot.", true, 'inventory.php');
        die($h->endpage());
    }
    if (isset($_POST['type']))
    {
        if ($_POST['type'] !== 'equip_potion') {
            alert('danger', "Uh Oh!", "You cannot equip potions to an invalid slot.", true, 'inventory.php');
            die($h->endpage());
        }
        
        if (!$r['effect1_on'] && !$r['effect2_on'] && !$r['effect3_on']) {
            alert('danger', "Uh Oh!", "You cannot equip this potion as it has no effects.", true, 'inventory.php');
            die($h->endpage());
        }
        if (($r['itmtype'] != 8) && ($r['itmtype'] != 7))
        {
            alert('danger', "Uh Oh!", "Cannot equip this item to your potion slot.", true, 'inventory.php');
            die($h->endpage());
        }
        //Potion equipping.
        $potionexclusion=array(17,123,68,138,95,96,148,177);
        if (in_array($r['itmid'],$potionexclusion))
        {
            alert('danger', "Uh Oh!", "You may not equip this item in your potion slot.", true, 'inventory.php');
            die($h->endpage());
        }
		$slot = equipSlotParser($_POST['type']);
		if ($ir[$_POST['type']] > 0) 
			unequipUserSlot($userid, $_POST['type']);
		equipUserSlot($userid, $_POST['type'], $r['itmid']);
        alert('success',"Success!","You have successfully equipped {$r['itmname']} as your {$slot}.",true,'inventory.php');
        die($h->endpage());
    }
    else
    {
        echo "<h3>Equip Potion Form</h3><hr />
        <form method='post' action='?slot=potion&ID={$_GET['ID']}'>
            You are attempting to equip your {$r['itmname']} as your potion for use in combat.
            <input type='hidden' name='type' value='equip_potion'  /><br />
            <input type='submit' class='btn btn-primary' value='Equip Potion' />
        </form>";
    }
    $h->endpage();
}
function badge()
{
    global $db,$api,$h,$userid,$ir;
    //Make sure the Item ID is safe for database work.
    $_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs($_GET['ID']) : 0;
    //Select the Item's info from the database.
    $id =
        $db->query(
            "/*qc=on*/SELECT `itmid`, `itmname`, `itmtype`
					FROM `inventory` AS `iv`
					LEFT JOIN `items` AS `it`
					ON `iv`.`inv_itemid` = `it`.`itmid`
					WHERE `iv`.`inv_id` = {$_GET['ID']}
					AND `iv`.`inv_userid` = $userid
					LIMIT 1");
    //Check that the item actually exists, if not, stop them.
    if ($db->num_rows($id) == 0) {
        $db->free_result($id);
        alert('danger', "Uh Oh!", "The badge you're trying to equip does not exist.", true, 'inventory.php');
        die($h->endpage());
    } else {
        $r = $db->fetch_row($id);
        $db->free_result($id);
    }
	if ($r['itmtype'] != 13)
	{
		alert('danger', "Uh Oh!", "Cannot equip this item to your badge slot.", true, 'inventory.php');
		die($h->endpage());
	}
    if (isset($_POST['type']))
    {
        if ($_POST['type'] !== 'equip_badge') {
            alert('danger', "Uh Oh!", "You cannot equip badges to an invalid slot.", true, 'inventory.php');
            die($h->endpage());
        }
        
        if ($r['itmtype'] != 13)
		{
			alert('danger', "Uh Oh!", "Cannot equip this item to your badge slot.", true, 'inventory.php');
			die($h->endpage());
		}
		$slot = equipSlotParser($_POST['type']);
		if ($ir[$_POST['type']] > 0) 
			unequipUserSlot($userid, $_POST['type']);
		equipUserSlot($userid, $_POST['type'], $r['itmid']);
        alert('success',"Success!","You have successfully equipped {$r['itmname']} as your {$slot}.",true,'inventory.php');
        die($h->endpage());
    }
    else
    {
        echo "<h3>Equip Badge Form</h3><hr />
        <form method='post' action='?slot=badge&ID={$_GET['ID']}'>
            You are attempting to equip your {$r['itmname']} as your badge. Badges are purely cosmetic items that 
			are shown off on your profile.
            <input type='hidden' name='type' value='equip_badge'  /><br />
            <input type='submit' class='btn btn-primary' value='Equip Badge' />
        </form>";
    }
    $h->endpage();
}
function ring()
{
    global $db, $h, $userid, $ir, $api;
    //Make sure the Item ID is safe for database use.
    $_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs($_GET['ID']) : 0;
    //Select all its info.
    $id = $db->query("/*qc=on*/SELECT `weapon`, `itmid`, `itmname`, `effect1`, `effect2`, 
					`effect3`,  `effect1_on`, `effect2_on`, `effect3_on`, `itmtype`
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
	$itname=$db->fetch_single($db->query("SELECT `itmtypename` FROM `itemtypes` WHERE `itmtypeid` = {$r['itmtype']}"));
    //Check that the item can be used as a weapon. If not, stop them here.
    if ($itname != 'Rings') {
        alert('danger', "Uh Oh!", "The item you are trying to equip is not a ring.", true, 'inventory.php');
        die($h->endpage());
    }
    //Check to be sure the user is trying to equip the item.
    if (isset($_POST['type'])) 
    {
        //Check that the equipment slot is a valid slot. If not, lets stop them.
        if (!in_array($_POST['type'], array(slot_prim_ring, slot_second_ring, slot_wed_ring), true)) 
        {
            alert('danger', "Uh Oh!", "You cannot equip a ring to an invalid slot.", true, 'inventory.php');
            die($h->endpage());
        }
        if ($_POST['type'] == slot_wed_ring)
        {
            if (!isUserMarried($userid))
            {
                alert('danger', "Uh Oh!", "You cannot wear a wedding ring when you are not married. Go back and try again.", true, 'inventory.php');
                die($h->endpage());
            }
            if (returnMarriageHappiness($userid) < 10)
            {
                alert('danger', "Uh Oh!", "You must have at least 10 marriage happiness before you can equip a wedding ring.", true, 'inventory.php');
                die($h->endpage());
            }
        }
		$eir=$db->fetch_row($db->query("SELECT * FROM `user_equips` WHERE `userid` = {$userid} AND `equip_slot` = '{$_POST['type']}'"));
        //Check to see if the chosen slot has a weapon equipped to it already. 
        //If true, give them their item back, and log the unequip.
		$slot = equipSlotParser($_POST['type']);
        if ($eir['itemid'] > 0) 
            unequipUserSlot($userid, $_POST['type']);
        equipUserSlot($userid, $_POST['type'], $r['itmid']);
        alert('success', "Success!", "You have successfully equipped {$api->SystemItemIDtoName($r['itmid'])} as your {$slot}.", true, 'inventory.php', 'Back', true);
    } 
    else 
    {
        $form = "<option value='equip_ring_primary'>Primary Ring</option>
				<option value='equip_ring_secondary'>Secondary Ring</option>";
        if (isUserMarried($userid))
        {
            $form .= "<option value='equip_wedding_ring'>Wedding Ring</option>";
        }
        //Form to select what slot to equip the weapon to.
        echo "<h3>Equip Ring Form</h3>
		<hr />
		What slot do you want to equip your {$r['itmname']} in? If you have a ring already equipped in that slot,
		it'll be moved to your inventory.<br />
		<form action='?slot=ring&ID={$_GET['ID']}' method='post'>
			<select name='type' class='form-control' type='dropdown'>
				{$form}
			</select>
			<input type='submit' value='Equip Ring' class='btn btn-primary'>
		</form>
		";
    }
    $h->endpage();
}
function necklace()
{
    global $db, $h, $userid, $ir, $api;
    //Make sure the Item ID is safe for database use.
    $_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs($_GET['ID']) : 0;
    //Select all its info.
    $id = $db->query("/*qc=on*/SELECT `weapon`, `itmid`, `itmname`, `effect1`, `effect2`, 
					`effect3`,  `effect1_on`, `effect2_on`, `effect3_on`, `itmtype`
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
	$itname=$db->fetch_single($db->query("SELECT `itmtypename` FROM `itemtypes` WHERE `itmtypeid` = {$r['itmtype']}"));
    //Check that the item can be used as a weapon. If not, stop them here.
    if ($itname != 'Necklaces') {
        alert('danger', "Uh Oh!", "The item you are trying to equip is not a necklace.", true, 'inventory.php');
        die($h->endpage());
    }
    //Check to be sure that the player is trying to equip to a slot.
    if (isset($_POST['type'])) {
        //Check that the user is trying to equip the item as an armor.
        if ($_POST['type'] !== 'equip_necklace') {
            alert('danger', "Uh Oh!", "You cannot equip a necklace to an invalid slot.", true, 'inventory.php');
            die($h->endpage());
        }
        $eir=$db->fetch_row($db->query("SELECT * FROM `user_equips` WHERE `userid` = {$userid} AND `equip_slot` = '{$_POST['type']}'"));
        //Check to see if the chosen slot has a weapon equipped to it already. If true, give them their item back, and
        //log the unequip.
        $slot = equipSlotParser($_POST['type']);
        if ($eir['itemid'] > 0) 
            unequipUserSlot($userid, $_POST['type']);
        equipUserSlot($userid, $_POST['type'], $r['itmid']);
        alert('success', "Success!", "You have successfully equipped {$api->SystemItemIDtoName($r['itmid'])} as your {$slot}.", true, 'inventory.php', 'Back', true);
    } else {
        //Equip armor form.
        echo "<h3>Equip Necklace Form</h3><hr />
	<form action='?slot=necklace&ID={$_GET['ID']}' method='post'>
	You are attempting to equip your {$r['itmname']} as your necklace. If you have a necklace on now, it'll be moved to your
	inventory.<br />
	<input type='hidden' name='type' value='equip_necklace'  />
	<input type='submit' class='btn btn-primary' value='Equip Necklace' />
	</form>";
    }
    $h->endpage();
}
function pendant()
{
    global $db, $h, $userid, $ir, $api;
    //Make sure the Item ID is safe for database use.
    $_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs($_GET['ID']) : 0;
    //Select all its info.
    $id = $db->query("/*qc=on*/SELECT `weapon`, `itmid`, `itmname`, `effect1`, `effect2`, 
					`effect3`,  `effect1_on`, `effect2_on`, `effect3_on`, `itmtype`
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
	$itname=$db->fetch_single($db->query("SELECT `itmtypename` FROM `itemtypes` WHERE `itmtypeid` = {$r['itmtype']}"));
    //Check that the item can be used as a weapon. If not, stop them here.
    if ($itname != 'Pendants') {
        alert('danger', "Uh Oh!", "The item you are trying to equip is not a pendant.", true, 'inventory.php');
        die($h->endpage());
    }
    //Check to be sure that the player is trying to equip to a slot.
    if (isset($_POST['type'])) {
        //Check that the user is trying to equip the item as an armor.
        if ($_POST['type'] !== 'equip_pendant') {
            alert('danger', "Uh Oh!", "You cannot equip a pendant to an invalid slot.", true, 'inventory.php');
            die($h->endpage());
        }
        $eir=$db->fetch_row($db->query("SELECT * FROM `user_equips` WHERE `userid` = {$userid} AND `equip_slot` = '{$_POST['type']}'"));
        //Check to see if the chosen slot has a weapon equipped to it already. If true, give them their item back, and
        //log the unequip.
        $slot = equipSlotParser($_POST['type']);
        if ($eir['itemid'] > 0) 
            unequipUserSlot($userid, $_POST['type']);
        equipUserSlot($userid, $_POST['type'], $r['itmid']);
        alert('success', "Success!", "You have successfully equipped {$api->SystemItemIDtoName($r['itmid'])} as your {$slot}.", true, 'inventory.php', 'Back', true);
    } else {
        //Equip armor form.
        echo "<h3>Equip Pendant Form</h3><hr />
	<form action='?slot=pendant&ID={$_GET['ID']}' method='post'>
	You are attempting to equip your {$r['itmname']} as your pendant. If you have a pendant on now, it'll be moved to your
	inventory.<br />
	<input type='hidden' name='type' value='equip_pendant'  />
	<input type='submit' class='btn btn-primary' value='Equip Pendant' />
	</form>";
    }
    $h->endpage();
}