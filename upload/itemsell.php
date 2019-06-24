<?php
/*
	File:		itemsell.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows players to sell items back to the game for Primary Currency.
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
		if (!isset($_POST['verf']) || !checkCSRF("sellitem_{$id}", stripslashes($_POST['verf']))) {
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
		$code = getCodeCSRF("sellitem_{$id}");
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