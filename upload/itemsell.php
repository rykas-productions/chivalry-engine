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
$id = filter_input(INPUT_GET, 'ID', FILTER_SANITIZE_NUMBER_INT) ?: 0;
$qty= filter_input(INPUT_POST, 'qty', FILTER_SANITIZE_NUMBER_INT) ?: 0;
echo "<h3>Item Selling</h3><hr />";
if (!empty($qty) && !empty($id)) {
	$id =
		$db->query(
			"SELECT `inv_qty`, `itmsellprice`, `itmid`, `itmname`
					 FROM `inventory` AS `iv`
					 INNER JOIN `items` AS `it`
					 ON `iv`.`inv_itemid` = `it`.`itmid`
					 WHERE `iv`.`inv_id` = {$id}
					 AND `iv`.`inv_userid` = {$userid}
					 LIMIT 1");
	if ($db->num_rows($id) == 0) {
		alert('danger', "Uh Oh!", "You do not have this item to sell.", true, 'inventory.php');
	} else {
		$r = $db->fetch_row($id);
		if (!isset($_POST['verf']) || !verify_csrf_code("sellitem_{$id}", stripslashes($_POST['verf']))) {
			alert('danger', "Action Blocked!", "We have blocked your action. Please fill out the form quickly.");
			die($h->endpage());
		}
		if ($qty > $r['inv_qty']) {
			alert('danger', "Uh Oh!", "You are trying to sell more items than you currently have.");
		} else {
			$price = $r['itmsellprice'] * $qty;
			$api->user->takeItem($userid, $r['itmid'], $qty);
			$api->user->giveCurrency($userid, 'primary', $price);
			$priceh = number_format($price);
			alert('success', "Success!", "You have successfully sold {$qty} {$r['itmname']}(s) back to the
				game for {$priceh} {$_CONFIG['primary_currency']}.", true, 'inventory.php');
			$is_log = $db->escape("{$ir['username']} sold {$qty} {$r['itmname']}(s) for {$priceh} {$_CONFIG['primary_currency']}.");
			$api->game->addLog($userid, 'itemsell', $is_log);
		}
	}
	$db->free_result($id);
} else if (!empty($id) && empty($qty)) {
	$id =
		$db->query(
			"SELECT `inv_qty`, `itmname`
					 FROM `inventory` AS `iv`
					 INNER JOIN `items` AS `it`
					 ON `iv`.`inv_itemid` = `it`.`itmid`
					 WHERE `iv`.`inv_id` = {$id}
					 AND `iv`.`inv_userid` = {$userid}
					 LIMIT 1");
	if ($db->num_rows($id) == 0) {
		alert('danger', "Uh Oh!", "You are trying to sell an invalid or non-existent item.", true, 'inventory.php');
	} else {
		$r = $db->fetch_row($id);
		$code = request_csrf_code("sellitem_{$id}");
		echo "
		<b>You are attempting to sell your {$r['itmname']}(s) back to the game. You have
		" . number_format($r['inv_qty']) . " to sell. How many do you wish to sell?</b>
		<br />
		<form action='?ID={$id}' method='post'>
			<table class='table table-bordered'>
				<tr>
					<th>
						Quantity
					</th>
					<td>
						<input type='text' class='form-control' name='qty' value='{$r['inv_qty']}' />
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-primary' value='Sell' />
					</td>
				</tr>
				<input type='hidden' name='verf' value='{$code}' />
			</table>
		</form>
		";
	}
	$db->free_result($id);
} else {
	alert('danger', "Uh Oh!", "Please select an item you wish to sell.", true, 'inventory.php');
}
$h->endpage();