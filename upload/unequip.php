<?php
/*
	File:		unequip.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Logic for handling unequipping weapons.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
	MIT License

	Copyright (c) 2019 TheMasterGeneral

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
*/
//@TODO Maybe merge this into equip.php?
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
addItem($userid, $ir[$_GET['type']], 1);
$db->query("UPDATE `users` SET `{$_GET['type']}` = 0 WHERE `userid` = {$ir['userid']}");
$names = array('equip_primary' => "Primary Weapon",
    'equip_secondary' => "Secondary Weapon",
    'equip_armor' => "Armor");
//Tell user their slot is now empty
$weapname = $db->fetch_single($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$ir[$_GET['type']]}"));
$api->game->addLog($userid, 'equip', "Unequipped {$weapname} from their {$_GET['type']} slot.");
alert('success', "Success!", "You have successfully unequipped the item in your {$names[$_GET['type']]} slot.", true, 'inventory.php');
$h->endpage();