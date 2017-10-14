<?php
/*
	File:		unequip.php
	Created: 	4/5/2016 at 12:30AM Eastern Time
	Info: 		Allows players to unequip armor and weapons.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
//Make sure user is trying to unequip a valid slot.
if (!isset($_GET['type']) || !in_array($_GET['type'], array("equip_primary", "equip_secondary", "equip_armor"), true)) {
    alert('danger', "Uh Oh!", "You are trying to unequip from an invalid slot.", true, 'inventory.php');
    die($h->endpage());
}
//User doesn't have anything equipped in this slot.
if ($ir[$_GET['type']] == 0) {
    alert('danger', "Uh Oh!", "You do not have an item equipped in this slot.", true, 'inventory.php');
    die($h->endpage());
}
//Give item to user and set their slot to 0
item_add($userid, $ir[$_GET['type']], 1);
$db->query("UPDATE `users` SET `{$_GET['type']}` = 0 WHERE `userid` = {$ir['userid']}");
$names = array('equip_primary' => "Primary Weapon",
    'equip_secondary' => "Secondary Weapon",
    'equip_armor' => "Armor");
//Tell user their slot is now empty
$weapname = $db->fetch_single($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$ir[$_GET['type']]}"));
$api->SystemLogsAdd($userid, 'equip', "Unequipped {$weapname} from their {$_GET['type']} slot.");
alert('success', "Success!", "You have successfully unequipped the item in your {$names[$_GET['type']]} slot.", true, 'inventory.php');
$h->endpage();