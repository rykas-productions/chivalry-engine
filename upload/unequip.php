<?php
require('globals.php');
if (!isset($_GET['type']) || !in_array($_GET['type'], array("equip_primary", "equip_secondary", "equip_armor"), true))
{
	alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['EQUIP_OFF_ERROR1']}");
    die($h->endpage());
}
if ($ir[$_GET['type']] == 0)
{
    alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['EQUIP_OFF_ERROR2']}");
    die($h->endpage());
}
item_add($userid, $ir[$_GET['type']], 1);
$db->query("UPDATE `users` SET `{$_GET['type']}` = 0 WHERE `userid` = {$ir['userid']}");
$names = array('equip_primary' => "{$lang['EQUIP_WEAPON_SLOT1']}",
                'equip_secondary' => "{$lang['EQUIP_WEAPON_SLOT2']}",
                'equip_armor' => "{$lang['EQUIP_WEAPON_SLOT3']}");
$weapname=$db->fetch_single($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$ir[$_GET['type']]}"));
$api->SystemLogsAdd($userid,'equip',"Unequipped {$weapname} from their {$_GET['type']} slot.");
alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['EQUIP_OFF_SUCCESS']} {$names[$_GET['type']]} {$lang['EQUIP_OFF_SUCCESS1']}");
$h->endpage();