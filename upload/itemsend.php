<?php
/*
	File:		itemsend.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows a player to send an item to another player.
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
$_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs($_GET['ID']) : '';
$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : '';
$_POST['qty'] = (isset($_POST['qty']) && is_numeric($_POST['qty'])) ? abs($_POST['qty']) : '';
if (!empty($_POST['qty']) && !empty($_POST['user'])) {
    $id = $db->query("SELECT `inv_qty`, `inv_itemid`, `itmname`, `itmid`
                     FROM `inventory` AS `iv` INNER JOIN `items` AS `it`
                     ON `iv`.`inv_itemid` = `it`.`itmid` WHERE `iv`.`inv_id` = {$_GET['ID']}
                     AND iv.`inv_userid` = {$userid}
                     LIMIT 1");
    if ($db->num_rows($id) == 0) {
        alert('danger', "Uh Oh!", "You are trying to send an item you do not have, or does not exist.", true, 'inventory.php');
        die($h->endpage());
    } else {
        $r = $db->fetch_row($id);
        $m = $db->query("SELECT `lastip`,`username` FROM `users` WHERE `userid` = {$_POST['user']} LIMIT 1");
        if (!isset($_POST['verf']) || !checkCSRF("senditem_{$_GET['ID']}", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Form requests expire quickly. Go back and try again!");
            die($h->endpage());
        } elseif ($_POST['qty'] > $r['inv_qty']) {
            alert('danger', "Uh Oh!", "You are trying to send more of this item than you currently have.");
            die($h->endpage());
        } else if ($db->num_rows($m) == 0) {
            alert('danger', "Uh Oh!", "You are trying to send this item to a non-existent user.");
            die($h->endpage());
        } else if ($userid == $_POST['user']) {
            alert('danger', "Uh Oh!", "You cannot send yourself items.", true, 'inventory.php');
            die($h->endpage());
        } else if ($api->user->checkIP($userid, $_POST['user'])) {
            alert('danger', "Uh Oh!", "You cannot send an item to someone on the same IP Address as you.", true, 'inventory.php');
            die($h->endpage());
        } else {
            $rm = $db->fetch_row($m);
            takeItem($userid, $r['inv_itemid'], $_POST['qty']);
            addItem($_POST['user'], $r['inv_itemid'], $_POST['qty']);
            alert('success', "Success!", "You have successfully send {$_POST['qty']} {$r['itmname']}(s) to
			    {$rm['username']}.", true, 'inventory.php');
            addNotification($_POST['user'], "You have been sent {$_POST['qty']} {$r['itmname']}(s)
                from <a href='profile.php?user=$userid'>{$ir['username']}</a>.");
            $log = $db->escape("Sent {$_POST['qty']} {$r['itmname']}(s) to {$rm['username']} [{$_POST['user']}].");
            $api->game->addLog($userid, 'itemsend', $log);
        }
        $db->free_result($m);
    }
    $db->free_result($id);
} elseif (!empty($_GET['ID'])) {
    $id = $db->query("SELECT `inv_qty`, `inv_itemid`, `itmname`, `itmid`
                     FROM `inventory` AS `iv` INNER JOIN `items` AS `it`
                     ON `iv`.`inv_itemid` = `it`.`itmid` WHERE `iv`.`inv_id` = {$_GET['ID']}
                     AND iv.`inv_userid` = {$userid}
                     LIMIT 1");
    if ($db->num_rows($id) == 0) {
        alert('danger', "Uh Oh!", "You are trying to send an item you do not have, or doesn't exist.", true, 'inventory.php');
        die($h->endpage());
    } else {
        $r = $db->fetch_row($id);
        $code = getCodeCSRF("senditem_{$_GET['ID']}");
        echo "
		<form action='?ID={$_GET['ID']}' method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						Enter who you would wish to send your {$r['itmname']}(s) to. You currently have " . number_format($r['inv_qty']) . ".
					</th>
				</tr>
				<tr>
					<th>
						User
					</th>
					<td>
						" . dropdownUser('user') . "
					</td>
				</tr>
				<tr>
					<th>
						Quantity
					</th>
					<td>
						<input type='number' min='1' max='{$r['inv_qty']}' class='form-control' name='qty' value='{$r['inv_qty']}' />
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-primary' value='Send Items'>
					</td>
				</tr>
			</table>
			<input type='hidden' name='verf' value='{$code}' />
		</form>";
    }
    $db->free_result($id);
} else {
    alert('danger', "Uh Oh!", "Please select an item to send next time.", true, 'inventory.php');
    die($h->endpage());
}
$h->endpage();