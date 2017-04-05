<?php
/*
	File:		itemsell.php
	Created: 	4/5/2016 at 12:15AM Eastern Time
	Info: 		Allows players to instantly sell their item back
				to the game for a reduced price.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
$_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs(intval($_GET['ID'])) : '';
$_POST['qty'] = (isset($_POST['qty']) && is_numeric($_POST['qty'])) ? abs(intval($_POST['qty'])) : '';
echo "<h3>{$lang['ITEM_SELL_INFO']}</h3><hr />";
if (permission('CanSellToGame',$userid) == true)
{
	if (!empty($_POST['qty']) && !empty($_GET['ID']))
	{
		$id =
				$db->query(
						"SELECT `inv_qty`, `itmsellprice`, `itmid`, `itmname`
						 FROM `inventory` AS `iv`
						 INNER JOIN `items` AS `it`
						 ON `iv`.`inv_itemid` = `it`.`itmid`
						 WHERE `iv`.`inv_id` = {$_GET['ID']}
						 AND `iv`.`inv_userid` = {$userid}
						 LIMIT 1");
		if ($db->num_rows($id) == 0)
		{
			alert('danger',$lang['ITEM_SELL_ERROR1_TITLE'],$lang['ITEM_SELL_ERROR1'],true,'inventory.php');
		}
		else
		{
			$r = $db->fetch_row($id);
			if (!isset($_POST['verf']) || !verify_csrf_code("sellitem_{$_GET['ID']}", stripslashes($_POST['verf'])))
			{
				alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
				die($h->endpage());
			}
			if ($_POST['qty'] > $r['inv_qty'])
			{
				alert('danger',$lang['ERROR_INVALID'],$lang['ITEM_SELL_BAD_QTY']);
			}
			else
			{
				$price = $r['itmsellprice'] * $_POST['qty'];
				$api->UserTakeItem($userid, $r['itmid'], $_POST['qty']);
				$api->UserGiveCurrency($userid,'primary',$price);
				$priceh = number_format($price);
				alert('success',$lang['ERROR_SUCCESS'],"{$lang['ITEM_SELL_SUCCESS1']} {$_POST['qty']} {$r['itmname']}{$lang['ITEM_SELL_SUCCESS2']} {$priceh} {$lang['INDEX_PRIMCURR']}.",true,'inventory.php');
				$is_log =  $db->escape("{$ir['username']} sold {$_POST['qty']} {$r['itmname']}(s) for {$priceh}");
				$api->SystemLogsAdd($userid,'itemsell',$is_log);
			}
		}
		$db->free_result($id);
	}
	else if (!empty($_GET['ID']) && empty($_POST['qty']))
	{
		$id =
				$db->query(
						"SELECT `inv_qty`, `itmname`
						 FROM `inventory` AS `iv`
						 INNER JOIN `items` AS `it`
						 ON `iv`.`inv_itemid` = `it`.`itmid`
						 WHERE `iv`.`inv_id` = {$_GET['ID']}
						 AND `iv`.`inv_userid` = {$userid}
						 LIMIT 1");
		if ($db->num_rows($id) == 0)
		{
			alert('danger',$lang['ITEM_SELL_ERROR1_TITLE'],$lang['ITEM_SELL_ERROR1'],true,'inventory.php');
		}
		else
		{
			$r = $db->fetch_row($id);
			$code = request_csrf_code("sellitem_{$_GET['ID']}");
			echo "
			<b>{$lang['ITEM_SELL_FORM1']} {$r['itmname']}(s) {$lang['ITEM_SELL_FORM2']} {$r['inv_qty']} {$lang['ITEM_SELL_FORM3']}</b>
			<br />
			<form action='?ID={$_GET['ID']}' method='post'>
				<table class='table table-bordered'>
					<tr>
						<th>
							{$lang['STAFF_ITEM_GIVE_FORM_QTY']}
						</th>
						<td>
							<input type='text' class='form-control' name='qty' value='{$r['inv_qty']}' />
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<input type='submit' class='btn btn-default' value='{$lang['ITEM_SELL_BTN']}' />
						</td>
					</tr>
					<input type='hidden' name='verf' value='{$code}' />
				</table>
			</form>
			";
		}
		$db->free_result($id);
	}
	else
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['GEN_IUOF'],true,'inventory.php');
	}
}
$h->endpage();