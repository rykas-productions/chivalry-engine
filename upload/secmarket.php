<?php
/*
	File:		secmarket.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows players to view, buy and list their Secondary Currency for 
				sale, in hopes to receive Primary Currency.
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
echo "<h3>{$_CONFIG['secondary_currency']} Market</h3><hr />";
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'add':
        add();
        break;
    case 'remove':
        remove();
        break;
    case 'buy':
        buy();
        break;
    default:
        home();
        break;
}
function home()
{
    global $db, $api, $userid;
    echo "<a href='?action=add'>Add Your Own Listing</a><hr />
	<table class='table table-bordered table-striped'>
		<tr>
			<th>
				Listing Owner
			</th>
			<th>
				For Sale
			</th>
			<th>
				Cost (Total)
			</th>
			<th>
				Actions
			</th>
		</tr>";
    $q = $db->query("SELECT * FROM `sec_market` ORDER BY `sec_cost` ASC");
    while ($r = $db->fetch_row($q)) {
        $totalcost = $r['sec_total'] * $r['sec_cost'];
        if ($r['sec_user'] == $userid) {
            $a = "[<a href='?action=remove&id={$r['sec_id']}'>Remove Listing</a>]";
        } else {
            $a = "[<a href='?action=buy&id={$r['sec_id']}'>Buy</a>]";
        }
        echo "<tr>
				<td>
					<a href='profile.php?user={$r['sec_user']}'>{$api->user->getNamefromID($r['sec_user'])}</a> [{$r['sec_user']}]
				</td>
				<td>
					" . number_format($r['sec_total']) . "
				</td>
				<td>
					" . number_format($r['sec_cost']) . " (" . number_format($totalcost) . ")
				</td>
				<td>
					{$a}
				</td>
			</tr>";
    }
    echo "</table>";
}

function buy()
{
    global $db, $h, $userid, $api, $ir, $_CONFIG;
    $_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
    if (empty($_GET['id'])) {
        alert('danger', "Uh Oh!", "Please specify a listing you wish to buy.", true, 'secmarket.php');
        die($h->endpage());
    }
    $q = $db->query("SELECT * FROM `sec_market` WHERE `sec_id` = {$_GET['id']}");
    if ($db->num_rows($q) == 0) {
        alert('danger', "Uh Oh!", "Please specify an existent listing to buy.", true, 'secmarket.php');
        die($h->endpage());
    }
    $r = $db->fetch_row();
    if ($r['sec_user'] == $userid) {
        alert('danger', "Uh Oh!", "You cannot buy your own listing.", true, 'secmarket.php');
        die($h->endpage());
    }
    $totalcost = $r['sec_cost'] * $r['sec_total'];
    if ($api->user->hasCurrency($userid, 'primary', $totalcost) == false) {
        alert('danger', "Uh Oh!", "You do not have enough {$_CONFIG['primary_currency']} to buy this listing.", true, 'secmarket.php');
        die($h->endpage());
    }
    $api->game->addLog($userid, 'secmarket', "Bought {$r['sec_total']} {$_CONFIG['secondary_currency']} from the market for {$totalcost} {$_CONFIG['primary_currency']}.");
    $api->user->giveCurrency($userid, 'secondary', $r['sec_total']);
    $api->user->takeCurrency($userid, 'primary', $totalcost);
    $api->user->giveCurrency($r['sec_user'], 'primary', $totalcost);
    $api->user->addNotification($r['sec_user'], "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has bought your
        {$r['sec_total']} {$_CONFIG['secondary_currency']} offer from the market for a total of {$totalcost}.");
    $db->query("DELETE FROM `sec_market` WHERE `sec_id` = {$_GET['id']}");
    alert('success', "Success!", "You have bought {$r['sec_total']} {$_CONFIG['secondary_currency']} for {$totalcost} {$_CONFIG['primary_currency']}", true, 'secmarket.php');
    die($h->endpage());
}

function remove()
{
    global $db, $h, $userid, $api;
    $_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
    if (empty($_GET['id'])) {
        alert('danger', "Uh Oh!", "Please specify a listing you wish to remove.", true, 'secmarket.php');
        die($h->endpage());
    }
    $q = $db->query("SELECT * FROM `sec_market` WHERE `sec_id` = {$_GET['id']}");
    if ($db->num_rows($q) == 0) {
        alert('danger', "Uh Oh!", "You are trying to remove a non-existent listing.", true, 'secmarket.php');
        die($h->endpage());
    }
    $r = $db->fetch_row();
    if (!($r['sec_user'] == $userid)) {
        alert('danger', "Uh Oh!", "You are trying to remove a lising you do not own.", true, 'secmarket.php');
        die($h->endpage());
    }
    $api->game->addLog($userid, 'secmarket', "Removed {$r['sec_total']} {$_CONFIG['secondary_currency']} from the market.");
    $api->user->giveCurrency($userid, 'secondary', $r['sec_total']);
    $db->query("DELETE FROM `sec_market` WHERE `sec_id` = {$_GET['id']}");
    alert('success', "Success!", "You have removed your listing for {$r['sec_total']} {$_CONFIG['secondary_currency']} from the market.", true, 'secmarket.php');
    die($h->endpage());
}

function add()
{
    global $db, $h, $userid, $api, $ir;
    if (isset($_POST['qty']) && isset($_POST['cost'])) {
        $_POST['qty'] = (isset($_POST['qty']) && is_numeric($_POST['qty'])) ? abs($_POST['qty']) : '';
        $_POST['cost'] = (isset($_POST['cost']) && is_numeric($_POST['cost'])) ? abs($_POST['cost']) : '';
        if (empty($_POST['qty']) || empty($_POST['cost'])) {
            alert('danger', "Uh Oh!", "Please fill out the previous form completely before submitting it.");
            die($h->endpage());
        }
        if (!($api->user->hasCurrency($userid, 'secondary', $_POST['qty']))) {
            alert('danger', "Uh Oh!", "You are trying to add more {$_CONFIG['secondary_currency']} than you currently have.");
            die($h->endpage());
        }
        $db->query("INSERT INTO `sec_market` (`sec_user`, `sec_cost`, `sec_total`)
					VALUES ('{$userid}', '{$_POST['cost']}', '{$_POST['qty']}');");
        $api->user->takeCurrency($userid, 'secondary', $_POST['qty']);
        $api->game->addLog($userid, 'secmarket', "Added {$_POST['qty']} to the secondary market for {$_POST['cost']} {$_CONFIG['primary_currency']} each.");
        alert('success', "Success!", "You have added your {$_POST['qty']} {$_CONFIG['secondary_currency']} to the market for
		    {$_POST['cost']} {$_CONFIG['primary_currency']} each.", true, 'secmarket.php');
        die($h->endpage());
    } else {
        alert('info', "Information!", "Fill out this form completely to add your {$_CONFIG['secondary_currency']} to the market.", false);
        echo "
		<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th>
						Selling
					</th>
					<td>
						<input type='number' name='qty' class='form-control' required='1' min='1' value='{$ir['secondary_currency']}' max='{$ir['secondary_currency']}'>
					</td>
				</tr>
				<tr>
					<th>
						Price (Each)
					</th>
					<td>
						<input type='number' name='cost' class='form-control' required='1' min='1' value='200'>
					</td>
				<tr>
				
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-primary' value='Add Listing'>
					</td>
				</tr>
			</table>
		</form>";
    }
}

$h->endpage();