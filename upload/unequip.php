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
$validSlots = array("equip_primary", "equip_secondary", 
					"equip_armor", "equip_potion",
					"equip_badge", "equip_ring_primary", 
					"equip_ring_secondary", "equip_necklace", 
					"equip_pendant");
if (!isset($_GET['type']) || !in_array($_GET['type'], $validSlots, true)) {
    alert('danger', "Uh Oh!", "You are trying to unequip from an invalid slot.", true, 'inventory.php');
    die($h->endpage());
}
//User doesn't have anything equipped in this slot.
$itmid = getUserItemEquippedSlot($userid, $_GET['type']);
if ($itmid == 0)
{
    alert('danger', "Uh Oh!", "You do not have an item equipped in this slot.", true, 'inventory.php');
    die($h->endpage());
}
unequipUserSlot($userid, $_GET['type']);
$slot = equipSlotParser($_GET['type']);
alert('success', "Success!", "You have successfully unequipped your {$api->SystemItemIDtoName($itmid)} as your {$slot}.", true, 'inventory.php');
$h->endpage();