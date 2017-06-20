<?php
/*
	File:		itemsend.php
	Created: 	4/5/2016 at 12:16AM Eastern Time
	Info: 		Allows players to send another player an item.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
$_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs($_GET['ID']) : '';
$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : '';
$_POST['qty'] = (isset($_POST['qty']) && is_numeric($_POST['qty'])) ? abs($_POST['qty']) : '';
if (!empty($_POST['qty']) && !empty($_POST['user']))
{
	$id = $db->query("SELECT `inv_qty`, `inv_itemid`, `itmname`, `itmid`
                     FROM `inventory` AS `iv` INNER JOIN `items` AS `it`
                     ON `iv`.`inv_itemid` = `it`.`itmid` WHERE `iv`.`inv_id` = {$_GET['ID']}
                     AND iv.`inv_userid` = {$userid}
                     LIMIT 1");
    if ($db->num_rows($id) == 0)
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['ITEM_SEND_ERROR'],true,'inventory.php');
		die($h->endpage());
    }
	else
	{
		$r = $db->fetch_row($id);
        $m = $db->query("SELECT `lastip`,`username` FROM `users` WHERE `userid` = {$_POST['user']} LIMIT 1");
		if (!isset($_POST['verf']) || !verify_csrf_code("senditem_{$_GET['ID']}", stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		elseif ($_POST['qty'] > $r['inv_qty'])
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['ITEM_SEND_ERROR1']);
			die($h->endpage());
		}
		else if ($db->num_rows($m) == 0)
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['ITEM_SEND_ERROR2']);
			die($h->endpage());
        }
		else if ($userid == $_POST['user'])
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['ITEM_SEND_ERROR3'],true,'inventory.php');
			die($h->endpage());
		}
		else if ($api->SystemCheckUsersIPs($userid,$_POST['user']))
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['ITEM_SEND_ERROR4'],true,'inventory.php');
			die($h->endpage());
		}
		else
		{
			$rm = $db->fetch_row($m);
            item_remove($userid, $r['inv_itemid'], $_POST['qty']);
            item_add($_POST['user'], $r['inv_itemid'], $_POST['qty']);
			alert('success',$lang['ERROR_SUCCESS'],"{$lang['ITEM_SEND_SUCC']} {$_POST['qty']} {$r['itmname']}s {$lang['ITEM_SEND_SUCC1']} {$rm['username']}.",true,'inventory.php');
			notification_add($_POST['user'], "You have been sent {$_POST['qty']} {$r['itmname']}(s) from <a href='profile.php?user=$userid'>{$ir['username']}</a>.");
			$log =  $db->escape("{$ir['username']} sent {$_POST['qty']} {$r['itmname']}(s) to {$rm['username']} [{$_POST['user']}].");
			$api->SystemLogsAdd($userid,'itemsend',$log);
		}
		$db->free_result($m);
	}
	$db->free_result($id);
}
elseif (!empty($_GET['ID']))
{
	$id = $db->query("SELECT `inv_qty`, `inv_itemid`, `itmname`, `itmid`
                     FROM `inventory` AS `iv` INNER JOIN `items` AS `it`
                     ON `iv`.`inv_itemid` = `it`.`itmid` WHERE `iv`.`inv_id` = {$_GET['ID']}
                     AND iv.`inv_userid` = {$userid}
                     LIMIT 1");
    if ($db->num_rows($id) == 0)
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['ITEM_SEND_ERROR'],true,'inventory.php');
		die($h->endpage());
    }
	else
	{
		$r = $db->fetch_row($id);
        $code = request_csrf_code("senditem_{$_GET['ID']}");
		echo "
		<form action='?ID={$_GET['ID']}' method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						{$lang['ITEM_SEND_FORMTITLE']} {$r['itmname']} {$lang['ITEM_SEND_FORMTITLE1']} {$r['inv_qty']}.
					</th>
				</tr>
				<tr>
					<th>
						{$lang['ITEM_SEND_TH']}
					</th>
					<td>
						" . user_dropdown('user') . "
					</td>
				</tr>
				<tr>
					<th>
						{$lang['ITEM_SEND_TH1']}
					</th>
					<td>
						<input type='number' min='1' max='{$r['inv_qty']}' class='form-control' name='qty' value='{$r['inv_qty']}' />
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-secondary' value='{$lang['ITEM_SEND_BTN']}'>
					</td>
				</tr>
			</table>
			<input type='hidden' name='verf' value='{$code}' />
		</form>
		<form action='?ID={$_GET['ID']}' method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						{$lang['ITEM_SEND_FORMTITLE2']}
					</th>
				</tr>
				<tr>
					<th>
						{$lang['ITEM_SEND_TH']}
					</th>
					<td>
						<input type='number' min='1' class='form-control' name='user' />
					</td>
				</tr>
				<tr>
					<th>
						{$lang['ITEM_SEND_TH1']}
					</th>
					<td>
						<input type='number' min='1' max='{$r['inv_qty']}' class='form-control' name='qty' value='{$r['inv_qty']}' />
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-secondary' value='{$lang['ITEM_SEND_BTN']}'>
					</td>
				</tr>
			</table>
			<input type='hidden' name='verf' value='{$code}' />
		</form>";
	}
	$db->free_result($id);
}
else
{
    alert('danger',$lang['ERROR_GENERIC'],$lang['ITEM_SEND_ERROR'],true,'inventory.php');
	die($h->endpage());
}
$h->endpage();