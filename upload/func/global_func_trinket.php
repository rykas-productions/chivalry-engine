<?php
function isNecklaceEquipped($userid, $itemid)
{
	global $db;
	$q=$db->query("SELECT * FROM `user_equips` WHERE `equip_slot` = 'equip_necklace' AND `userid` = {$userid} AND `itemid` = {$itemid}");
	if ($db->num_rows($q) > 0)
		return true;
	else
		return false;
}
function isPendantEquipped($userid, $itemid)
{
	global $db;
	$q=$db->query("SELECT * FROM `user_equips` WHERE `equip_slot` = 'equip_pendant' AND `userid` = {$userid} AND `itemid` = {$itemid}");
	if ($db->num_rows($q) > 0)
		return true;
	else
		return false;
}
function isPrimaryRingEquipped($userid, $itemid)
{
	global $db;
	$q=$db->query("SELECT * FROM `user_equips` WHERE `equip_slot` = 'equip_ring_primary' AND `userid` = {$userid} AND `itemid` = {$itemid}");
	if ($db->num_rows($q) > 0)
		return true;
	else
		return false;
}
function isSecondaryRingEquipped($userid, $itemid)
{
	global $db;
	$q=$db->query("SELECT * FROM `user_equips` WHERE `equip_slot` = 'equip_ring_secondary' AND `userid` = {$userid} AND `itemid` = {$itemid}");
	if ($db->num_rows($q) > 0)
		return true;
	else
		return false;
}


function getEquippedPendant($userid)
{
	global $db;
	$q=$db->query("SELECT `itemid` FROM `user_equips` WHERE `equip_slot` = 'equip_pendant' AND `userid` = {$userid}");
	if ($db->num_rows($q) == 0)
		return 0;
	else
		return $db->fetch_single($q);
}

function getEquippedNecklace($userid)
{
	global $db;
	$q=$db->query("SELECT `itemid` FROM `user_equips` WHERE `equip_slot` = 'equip_necklace' AND `userid` = {$userid}");
	if ($db->num_rows($q) == 0)
		return 0;
	else
		return $db->fetch_single($q);
}

function getEquippedPrimaryRing($userid)
{
	global $db;
	$q=$db->query("SELECT `itemid` FROM `user_equips` WHERE `equip_slot` = 'equip_ring_primary' AND `userid` = {$userid}");
	if ($db->num_rows($q) == 0)
		return 0;
	else
		return $db->fetch_single($q);
}

function getEquippedSecondaryRing($userid)
{
	global $db;
	$q=$db->query("SELECT `itemid` FROM `user_equips` WHERE `equip_slot` = 'equip_ring_secondary' AND `userid` = {$userid}");
	if ($db->num_rows($q) == 0)
		return 0;
	else
		return $db->fetch_single($q);
}

//Do not use. Is fallback.
function hasNecklaceEquipped($userid, $itemid)
{
	return isNecklaceEquipped($userid, $itemid);
}
//Do not use. Is fallback.
function hasPendantEquipped($userid, $itemid)
{
	return isPendantEquipped($userid, $itemid);
}