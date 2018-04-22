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
        if (!in_array($_POST['type'], array("equip_primary", "equip_secondary"), true)) {
            alert('danger', "Uh Oh!", "You cannot equip a weapon to an invalid slot.", true, 'inventory.php');
            die($h->endpage());
        }
        //Check to see if the chosen slot has a weapon equipped to it already. If true, give them their item back, and
        //log the unequip.
        if ($ir[$_POST['type']] > 0) {
            $api->UserGiveItem($userid, $ir[$_POST['type']], 1);
            $slot = ($_POST['type'] == 'equip_primary') ? 'Primary Weapon' : 'Secondary Weapon';
			$sbq=$db->query("/*qc=on*/SELECT * FROM `equip_gains` WHERE `userid` = {$userid} and `slot` = '{$_POST['type']}'");
			$statloss='';
			if ($db->num_rows($sbq) > 0)
			{
				while ($sbr=$db->fetch_row($sbq))
				{
                    $stats =
					array("maxenergy" => "Maximum Energy", "maxwill" => "Maximum Will",
						"maxbrave" => "Maximum Bravery", "level" => "Level",
						"maxhp" => "Maximum Health", "strength" => "Strength",
						"agility" => "Agility", "guard" => "Guard",
						"labor" => "Labor", "iq" => "IQ",
						"infirmary" => "Infirmary Time", "dungeon" => "Dungeon Time",
						"primary_currency" => "Copper Coins", "secondary_currency"
					=> "Chivalry Tokens", "crimexp" => "Experience", "vip_days" =>
						"VIP Days");
					if ($sbr['direction'] == 'pos')
					{
						if (in_array($sbr['stat'], array('strength', 'agility', 'guard', 'labour', 'iq'))) {
							$db->query("UPDATE `userstats` SET `{$sbr['stat']}` = `{$sbr['stat']}` - {$sbr['number']} WHERE `userid` = {$userid}");
						} elseif (!(in_array($sbr['stat'], array('dungeon', 'infirmary')))) {
							$db->query("UPDATE `users` SET `{$sbr['stat']}` = `{$sbr['stat']}` - {$sbr['number']} WHERE `userid` = {$userid}");
						}
                        $mod='lost';
						$ir[$sbr['stat']] = $ir[$sbr['stat']]-$sbr['number'];
					}
					else
					{
						if (in_array($sbr['stat'], array('strength', 'agility', 'guard', 'labour', 'iq'))) {
							$db->query("UPDATE `userstats` SET `{$sbr['stat']}` = `{$sbr['stat']}` + {$sbr['number']} WHERE `userid` = {$userid}");
						} elseif (!(in_array($sbr['stat'], array('dungeon', 'infirmary')))) {
							$db->query("UPDATE `users` SET `{$sbr['stat']}` = `{$sbr['stat']}` + {$sbr['number']} WHERE `userid` = {$userid}");
						}
                        $mod='gained';
						$ir[$sbr['stat']] = $ir[$sbr['stat']]+$sbr['number'];
					}
                    if (empty($statloss))
                        $statloss .= "{$mod} " . number_format($sbr['number']) . " {$stats[$sbr['stat']]}";
                    else
                        $statloss .= ", {$mod} " . number_format($sbr['number']) . " {$stats[$sbr['stat']]}";
					$db->query("DELETE FROM `equip_gains` WHERE `userid` = {$userid} AND `stat` = '{$sbr['stat']}' AND `slot` = '{$_POST['type']}'");
				}
				alert('info',"Information!","You have {$statloss} when you unequipped your weapon.",false);
			}
            $weapname = $db->fetch_single($db->query("/*qc=on*/SELECT `itmname` FROM `items` WHERE `itmid` = {$ir[$_POST['type']]}"));
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
		$txt='';
		for ($enum = 1; $enum <= 3; $enum++) {
            if ($r["effect{$enum}_on"] == 'true') {
                $stats =
					array("maxenergy" => "Maximum Energy", "maxwill" => "Maximum Will",
						"maxbrave" => "Maximum Bravery", "level" => "Level",
						"maxhp" => "Maximum Health", "strength" => "Strength",
						"agility" => "Agility", "guard" => "Guard",
						"labor" => "Labor", "iq" => "IQ",
						"infirmary" => "Infirmary Time", "dungeon" => "Dungeon Time",
						"primary_currency" => "Copper Coins", "secondary_currency"
					=> "Chivalry Tokens", "crimexp" => "Experience", "vip_days" =>
						"VIP Days");
                $einfo = unserialize($r["effect{$enum}"]);
                if ($einfo['inc_type'] == "percent") {
                    if (in_array($einfo['stat'], array('energy', 'will', 'brave', 'hp'))) {
                        $inc = round($ir['max' . $einfo['stat']] / 100 * $einfo['inc_amount']);
						$einfo['stat'] = 'max' . $einfo['stat'];
                    }
					else {
                        $inc = round($ir[$einfo['stat']] / 100 * $einfo['inc_amount']);
                    }
                } else {
                    $inc = $einfo['inc_amount'];
                }
                if ($einfo['dir'] == "pos") {
                    if (in_array($einfo['stat'], array('energy', 'will', 'brave', 'hp'))) {
                        $ir['max' . $einfo['stat']] = $ir['max' . $einfo['stat']] + $einfo['inc_amount'];
						$einfo['stat'] = 'max' . $einfo['stat'];
                    } else {
                        $ir[$einfo['stat']] += $inc;
                    }
                } else {
						if (in_array($einfo['stat'], array('energy', 'will', 'brave', 'hp'))) {
							$ir[$einfo['stat']] = min($ir[$einfo['stat']] + $inc, $ir['max' . $einfo['stat']]);
							$einfo['stat'] = 'max' . $einfo['stat'];
						}
						else
						{
							$ir[$einfo['stat']] = max($ir[$einfo['stat']] - $inc, 0);
						}
                    }
                if (!(in_array($einfo['stat'], array('dungeon', 'infirmary')))) {
                    $upd = $ir[$einfo['stat']];
                }
                if (in_array($einfo['stat'], array('strength', 'agility', 'guard', 'labour', 'iq'))) {
                    $db->query("UPDATE `userstats` SET `{$einfo['stat']}` = '{$upd}' WHERE `userid` = {$userid}");
                } elseif (!(in_array($einfo['stat'], array('dungeon', 'infirmary')))) {
                    $db->query("UPDATE `users` SET `{$einfo['stat']}` = '{$upd}' WHERE `userid` = {$userid}");
                }
				$db->query("INSERT INTO `equip_gains` VALUES ('{$userid}', '{$einfo['stat']}', '{$einfo['dir']}', '{$inc}', '{$_POST['type']}')");
				$dir= ($einfo['dir'] == 'pos') ? "gained" : "lost" ;
                if (empty($txt))
                    $txt.=" {$dir} " . number_format($inc) . " {$stats[$einfo['stat']]}";
                else
                    $txt.=", {$dir} " . number_format($inc) . " {$stats[$einfo['stat']]}";
			}
        }
        //Remove the item from their inventory, and equip it! Lets log that they equipped it, and give the user a friendly
        //event saying they equipped their item as a weapon.
        $api->UserTakeItem($userid, $r['itmid'], 1);
        $db->query("UPDATE `users` SET `{$_POST['type']}` = {$r['itmid']} WHERE `userid` = {$userid}");
        $api->SystemLogsAdd($userid, 'equip', "Equipped {$r['itmname']} as their {$slot}.");
        alert('success', "Success!", "You have successfully equipped {$r['itmname']} as your weapon in your {$slot_name}
		    slot. You have {$txt} while you have this weapon equipped.", true, 'inventory.php', 'Back', true);
    } else {
        //Form to select what slot to equip the weapon to.
        echo "<h3>Equip a Weapon Form</h3>
		<hr />
		What slot do you want to equip your {$r['itmname']} in? If you have a weapon already equipped in that slot,
		it'll be moved to your inventory.<br />
		<form action='?slot=weapon&ID={$_GET['ID']}' method='post'>
			<input type='radio' class='form-control' name='type' value='equip_primary' checked='checked' />
			    Equip as Primary Weapon<br />
		<input type='radio' class='form-control' name='type' value='equip_secondary' />
		    Equip as Secondary Weapon<br />
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
        if ($ir['equip_armor'] > 0) {
            $api->UserGiveItem($userid, $ir['equip_armor'], 1);
			$sbq=$db->query("/*qc=on*/SELECT * FROM `equip_gains` WHERE `userid` = {$userid} and `slot` = '{$_POST['type']}'");
			$statloss='';
			$armorname = $db->fetch_single($db->query("/*qc=on*/SELECT `itmname` FROM `items` WHERE `itmid` = {$ir['equip_armor']}"));
			if ($db->num_rows($sbq) > 0)
			{
				while ($sbr=$db->fetch_row($sbq))
				{
                    $stats =
					array("maxenergy" => "Maximum Energy", "maxwill" => "Maximum Will",
						"maxbrave" => "Maximum Bravery", "level" => "Level",
						"maxhp" => "Maximum Health", "strength" => "Strength",
						"agility" => "Agility", "guard" => "Guard",
						"labor" => "Labor", "iq" => "IQ",
						"infirmary" => "Infirmary Time", "dungeon" => "Dungeon Time",
						"primary_currency" => "Copper Coins", "secondary_currency"
					=> "Chivalry Tokens", "crimexp" => "Experience", "vip_days" =>
						"VIP Days");
					if ($sbr['direction'] == 'pos')
					{
						if (in_array($sbr['stat'], array('strength', 'agility', 'guard', 'labour', 'iq'))) {
							$db->query("UPDATE `userstats` SET `{$sbr['stat']}` = `{$sbr['stat']}` - {$sbr['number']} WHERE `userid` = {$userid}");
						} elseif (!(in_array($sbr['stat'], array('dungeon', 'infirmary')))) {
							$db->query("UPDATE `users` SET `{$sbr['stat']}` = `{$sbr['stat']}` - {$sbr['number']} WHERE `userid` = {$userid}");
						}
                        $mod='lost';
                        $ir[$sbr['stat']] = $ir[$sbr['stat']]-$sbr['number'];
					}
					else
					{
						if (in_array($sbr['stat'], array('strength', 'agility', 'guard', 'labour', 'iq'))) {
							$db->query("UPDATE `userstats` SET `{$sbr['stat']}` = `{$sbr['stat']}` + {$sbr['number']} WHERE `userid` = {$userid}");
						} elseif (!(in_array($sbr['stat'], array('dungeon', 'infirmary')))) {
							$db->query("UPDATE `users` SET `{$sbr['stat']}` = `{$sbr['stat']}` + {$sbr['number']} WHERE `userid` = {$userid}");
						}
                        $mod='gained';
                        $ir[$sbr['stat']] = $ir[$sbr['stat']]+$sbr['number'];
					}
					if (empty($statloss))
                        $statloss .= "{$mod} " . number_format($sbr['number']) . " {$stats[$sbr['stat']]}";
                    else
                        $statloss .= ", {$mod} " . number_format($sbr['number']) . " {$stats[$sbr['stat']]}";
					$db->query("DELETE FROM `equip_gains` WHERE `userid` = {$userid} AND `stat` = '{$sbr['stat']}' AND `slot` = '{$_POST['type']}'");
				}
				alert('info',"Information!","You have {$statloss} when you unequipped your {$armorname}.",false);
			}
            $api->SystemLogsAdd($userid, 'equip', "Unequipped {$armorname} as their armor.");
        }
		$txt='';
		for ($enum = 1; $enum <= 3; $enum++) {
            if ($r["effect{$enum}_on"] == 'true') {
                $stats =
					array("maxenergy" => "Maximum Energy", "maxwill" => "Maximum Will",
						"maxbrave" => "Maximum Bravery", "level" => "Level",
						"maxhp" => "Maximum Health", "strength" => "Strength",
						"agility" => "Agility", "guard" => "Guard",
						"labor" => "Labor", "iq" => "IQ",
						"infirmary" => "Infirmary Time", "dungeon" => "Dungeon Time",
						"primary_currency" => "Copper Coins", "secondary_currency"
					=> "Chivalry Tokens", "crimexp" => "Experience", "vip_days" =>
						"VIP Days");
                $einfo = unserialize($r["effect{$enum}"]);
                if ($einfo['inc_type'] == "percent") {
                    if (in_array($einfo['stat'], array('energy', 'will', 'brave', 'hp'))) {
                        $inc = round($ir['max' . $einfo['stat']] / 100 * $einfo['inc_amount']);
						$einfo['stat'] = 'max' . $einfo['stat'];
                    }
					else {
                        $inc = round($ir[$einfo['stat']] / 100 * $einfo['inc_amount']);
                    }
                } else {
                    $inc = $einfo['inc_amount'];
                }
                if ($einfo['dir'] == "pos") {
                    if (in_array($einfo['stat'], array('energy', 'will', 'brave', 'hp'))) {
                        $ir['max' . $einfo['stat']] = $ir['max' . $einfo['stat']] + $einfo['inc_amount'];
						$einfo['stat'] = 'max' . $einfo['stat'];
                    } else {
                        $ir[$einfo['stat']] += $inc;
                    }
                } else {
						if (in_array($einfo['stat'], array('energy', 'will', 'brave', 'hp'))) {
							$ir[$einfo['stat']] = min($ir[$einfo['stat']] + $inc, $ir['max' . $einfo['stat']]);
							$einfo['stat'] = 'max' . $einfo['stat'];
						}
						else
						{
							$ir[$einfo['stat']] = max($ir[$einfo['stat']] - $inc, 0);
						}
                    }
                if (!(in_array($einfo['stat'], array('dungeon', 'infirmary')))) {
                    $upd = $ir[$einfo['stat']];
                }
                if (in_array($einfo['stat'], array('strength', 'agility', 'guard', 'labour', 'iq'))) {
                    $db->query("UPDATE `userstats` SET `{$einfo['stat']}` = '{$upd}' WHERE `userid` = {$userid}");
                } elseif (!(in_array($einfo['stat'], array('dungeon', 'infirmary')))) {
                    $db->query("UPDATE `users` SET `{$einfo['stat']}` = '{$upd}' WHERE `userid` = {$userid}");
                }
				$db->query("INSERT INTO `equip_gains` VALUES ('{$userid}', '{$einfo['stat']}', '{$einfo['dir']}', '{$inc}', '{$_POST['type']}')");
				$dir= ($einfo['dir'] == 'pos') ? "gained" : "lost" ;
				if (empty($txt))
                    $txt.=" {$dir} " . number_format($inc) . " {$stats[$einfo['stat']]}";
                else
                    $txt.=", {$dir} " . number_format($inc) . " {$stats[$einfo['stat']]}";
			}
        }
        //Take the item from their inventory, equip it, log that it was equipped, and give a sucecss message to the player.
        $api->UserTakeItem($userid, $r['itmid'], 1);
        $db->query("UPDATE `users`
				  SET `equip_armor` = {$r['itmid']}
				  WHERE `userid` = {$userid}");
        $api->SystemLogsAdd($userid, 'equip', "Equipped {$r['itmname']} as their armor.");
        alert('success', "Success!", "You have equipped your {$r['itmname']} into your armor slot. You have {$txt} while you have this armor equipped.", true, 'inventory.php', 'Back', true);
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
        $potionexclusion=array(17,123,68,138,95,96,148);
        if (in_array($r['itmid'],$potionexclusion))
        {
            alert('danger', "Uh Oh!", "You may not equip this item in your potion slot.", true, 'inventory.php');
            die($h->endpage());
        }
        $db->query("UPDATE `users`
				  SET `equip_potion` = {$r['itmid']}
				  WHERE `userid` = {$userid}");
        $api->SystemLogsAdd($userid, 'equip', "Equipped {$r['itmname']} as their potion.");
        alert('success',"Success!","You have successfully equipped {$r['itmname']} as your combat potion.",true,'inventory.php');
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