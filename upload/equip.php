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
	global $db,$lang,$h,$userid,$ir;
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
		}
		if ($_POST['type'] == "equip_primary")
		{
			$slot_name=$lang['EQUIP_WEAPON_SLOT1'];
		}
		else
		{
			$slot_name=$lang['EQUIP_WEAPON_SLOT2'];
		}
		item_remove($userid, $r['itmid'], 1);
		$db->query("UPDATE `users` SET `{$_POST['type']}` = {$r['itmid']} WHERE `userid` = {$userid}");
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