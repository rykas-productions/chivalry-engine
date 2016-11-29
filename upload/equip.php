<?php
require('globals.php');
if (!isset($_GET['slot']))
{
    $_GET['slot'] = '';
}
switch ($_GET['slot'])
{
case 'weapon':
    weapon();
    break;
case 'armor':
    armor();
    break;
default:
    alert('danger',"404","");
    break;
}
function weapon()
{
	global $db,$lang,$h,$userid,$ir,$api;
	$_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs((int) $_GET['ID']) : 0;
	$id = $db->query("SELECT `weapon`, `itmid`, `itmname`
					FROM `inventory` AS `iv`
					LEFT JOIN `items` AS `it`
					ON `iv`.`inv_itemid` = `it`.`itmid`
					WHERE `iv`.`inv_id` = {$_GET['ID']}
					AND `iv`.`inv_userid` = {$userid}
					LIMIT 1");
	if ($db->num_rows($id) == 0)
	{
		$db->free_result($id);
		alert('danger',"{$lang['EQUIP_NOITEM_TITLE']}","{$lang['EQUIP_NOITEM']}");
		die($h->endpage());
	}
	else
	{
		$r = $db->fetch_row($id);
		$db->free_result($id);
	}
	if (!$r['weapon'])
	{
		alert('danger',"{$lang['EQUIP_NOTWEAPON_TITLE']}","{$lang['EQUIP_NOTWEAPON']}");
		die($h->endpage());
	}
	if (isset($_POST['type']))
	{
		if (!in_array($_POST['type'], array("equip_primary", "equip_secondary"), true))
		{
			alert('danger',"{$lang['EQUIP_NOSLOT_TITLE']}","{$lang['EQUIP_NOSLOT']}");
			die($h->endpage());
		}
		if ($ir[$_POST['type']] > 0)
		{
			item_add($userid, $ir[$_POST['type']], 1);
			$slot = ($_POST['type'] == 'equip_primary') ? 'Primary Weapon' : 'Secondary Weapon';
			$weapname=$db->fetch_single($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$ir[$_POST['type']]}"));
			$api->SystemLogsAdd($userid,'equip',"Unequipped {$weapname} as their {$slot}");
		}
		if ($_POST['type'] == "equip_primary")
		{
			$slot_name=$lang['EQUIP_WEAPON_SLOT1'];
			$slot='Primary Weapon';
		}
		else
		{
			$slot_name=$lang['EQUIP_WEAPON_SLOT2'];
			$slot='Secondary Weapon';
		}
		item_remove($userid, $r['itmid'], 1);
		$db->query("UPDATE `users` SET `{$_POST['type']}` = {$r['itmid']} WHERE `userid` = {$userid}");
		$api->SystemLogsAdd($userid,'equip',"Equipped {$r['itmname']} as their {$slot}.");
		alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['EQUIP_WEAPON_SUCCESS1']} {$r['itmname']} {$lang['EQUIP_WEAPON_SUCCESS2']} {$slot_name}.");
	}
	else
	{
		echo "<h3>{$lang['EQUIP_WEAPON_TITLE']}</h3>
		<hr />
		{$lang['EQUIP_WEAPON_TEXT_FORM_1']} {$r['itmname']} {$lang['EQUIP_WEAPON_TEXT_FORM_2']}<br />
		<form action='?slot=weapon&ID={$_GET['ID']}' method='post'>
			<input type='radio' class='form-control' 
			name='type' value='equip_primary' 
			checked='checked' />{$lang['EQUIP_WEAPON_EQUIPAS']} {$lang['EQUIP_WEAPON_SLOT1']}<br />
		<input type='radio' class='form-control' name='type' value='equip_secondary' />{$lang['EQUIP_WEAPON_EQUIPAS']} {$lang['EQUIP_WEAPON_SLOT2']}<br />
		<input type='submit' value='{$lang['EQUIP_WEAPON_TITLE']}' class='btn btn-default'>
		</form>
		";
	}
	$h->endpage();
}
function armor()
{
	global $db,$lang,$h,$userid,$ir,$api;
	$_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs((int) $_GET['ID']) : 0;
	$id =
			$db->query(
					"SELECT `armor`, `itmid`, `itmname`
					FROM `inventory` AS `iv`
					LEFT JOIN `items` AS `it`
					ON `iv`.`inv_itemid` = `it`.`itmid`
					WHERE `iv`.`inv_id` = {$_GET['ID']}
					AND `iv`.`inv_userid` = $userid
					LIMIT 1");
	if ($db->num_rows($id) == 0)
	{
		$db->free_result($id);
		alert('danger',"{$lang['EQUIP_NOITEM_TITLE']}","{$lang['EQUIP_NOITEM']}");
		die($h->endpage());
	}
	else
	{
		$r = $db->fetch_row($id);
		$db->free_result($id);
	}
	if (!$r['armor'])
	{
		alert('danger',"{$lang['EQUIP_NOTARMOR_TITLE']}","{$lang['EQUIP_NOTARMOR']}");
		die($h->endpage());
	}
	if (isset($_POST['type']))
	{
		if ($_POST['type'] !== 'equip_armor')
		{
			alert('danger',"{$lang['EQUIP_NOSLOT_TITLE']}","{$lang['EQUIP_NOSLOT']}");
			die($h->endpage());
		}
		if ($ir['equip_armor'] > 0)
		{
			item_add($userid, $ir['equip_armor'], 1);
			$armorname=$db->fetch_single($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$ir['equip_armor']}"));
			$api->SystemLogsAdd($userid,'equip',"Unequipped {$armorname} as their armor.");
		}
		item_remove($userid, $r['itmid'], 1);
		$db->query(
				"UPDATE `users`
				 SET `equip_armor` = {$r['itmid']}
				 WHERE `userid` = {$userid}");
		$api->SystemLogsAdd($userid,'equip',"Equipped {$r['itmname']} as their armor.");
		alert('success',"","{$lang['EQUIP_WEAPON_SUCCESS1']} {$r['itmname']}.");
	}
	else
	{
		echo "<h3>{$lang['EQUIP_ARMOR_TITLE']}</h3><hr />
	<form action='?slot=armor&ID={$_GET['ID']}' method='post'>
	{$lang['EQUIP_ARMOR_TEXT_FORM_1']} {$r['itmname']} {$lang['EQUIP_ARMOR_TEXT_FORM_2']}<br />
	<input type='hidden' name='type' value='equip_armor'  />
	<input type='submit' class='btn btn-default' value='Equip Armor' />
	</form>";
	}
	$h->endpage();
}