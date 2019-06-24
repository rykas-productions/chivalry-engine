<?php
/*
	File:		itemmarket.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows players to buy or sell items to other players.
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
echo "<h3>Item Market</h3><hr />";
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "buy":
        buy();
        break;
    case "gift":
        gift();
        break;
    case "remove":
        remove();
        break;
    case "add":
        add();
        break;
    default:
        index();
        break;
}
function index()
{
    global $db, $userid, $api, $_CONFIG;
    echo "[<a href='?action=add'>Add Your Own Listing</a>]
	<br />
	<table class='table table-bordered table-hover table-striped'>
		<tr>
			<th>Listing Owner</th>
			<th>Item x Quantity</th>
			<th>Price/Item</th>
			<th>Total Price</th>
			<th>Links</th>
		</tr>
   ";

    $q =
        $db->query(
            "SELECT `imPRICE`, `imQTY`, `imCURRENCY`, `imADDER`,
                     `imID`, `itmid`, `itmname`, `userid`,`username`, `itmdesc`,
                     `itmtypename`
                     FROM `itemmarket` AS `im`
                     INNER JOIN `items` AS `i`
                     ON `im`.`imITEM` = `i`.`itmid`
                     INNER JOIN `users` AS `u`
                     ON `u`.`userid` = `im`.`imADDER`
                     INNER JOIN `itemtypes` AS `it`
                     ON `i`.`itmtype` = `it`.`itmtypeid`
                     ORDER BY `i`.`itmtype`, `i`.`itmname`, `u`.`username` ASC");
    $cblah = 1;
    $lt = "";
    while ($r = $db->fetch_row($q)) {
        if ($lt != $r['itmtypename']) {
            $lt = $r['itmtypename'];
            echo "<tr>
					<td colspan='5' align='center'><b>{$lt}</b></td>
				</tr>";
        }
        $ctprice = ($r['imPRICE'] * $r['imQTY']);
        if ($r['imCURRENCY'] == 'primary') {
            $price = number_format($r['imPRICE']) . " {$_CONFIG['primary_currency']}";
            $tprice = number_format($ctprice) . " {$_CONFIG['primary_currency']}";
        } else {
            $price = number_format($r['imPRICE']) . " {$_CONFIG['secondary_currency']}";
            $tprice = number_format($ctprice) . " {$_CONFIG['secondary_currency']}";
        }
        if ($r['imADDER'] == $userid) {
            $link =
                "[<a href='?action=remove&ID={$r['imID']}'>Remove</a>]";
        } else {
            $link =
                "[<a href='?action=buy&ID={$r['imID']}'>Buy</a>]
                    [<a href='?action=gift&ID={$r['imID']}'>Gift</a>]";
        }
        $r['itmdesc'] = htmlentities($r['itmdesc'], ENT_QUOTES);
        echo "
		<tr>
			<td>
				<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
			</td>
			<td>
				<a href='iteminfo.php?ID={$r['itmid']}' data-toggle='tooltip' data-placement='right' title='{$r['itmdesc']}'>{$r['itmname']}</a>";
        if ($r['imQTY'] > 1) {
            echo " x {$r['imQTY']}";
        }
        echo "</td>
			<td>
				{$price}
			</td>
			<td>
				{$tprice}
			</td>
			<td>
				{$link}
			</td>
		</tr>";
    }
    $db->free_result($q);
    echo "</table>";
}

function remove()
{
    global $db, $userid, $h, $api;
    $ID = filter_input(INPUT_GET, 'ID', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    if (empty($ID)) {
        alert('danger', "Uh Oh!", "Please specify the offer you wish to remove.", true, 'itemmarket.php');
        die($h->endpage());
    }
    $q = $db->query("SELECT `imITEM`, `imQTY`, `imADDER`, `imID`, `itmname`
                    FROM `itemmarket` AS `im` INNER JOIN `items` AS `i`
                    ON `im`.`imITEM` = `i`.`itmid`  WHERE `im`.`imID` = {$ID}
                    AND `im`.`imADDER` = {$userid}");
    if ($db->num_rows($q) == 0) {
        alert('danger', "Uh Oh!", "You are trying to remove a non-existent offer, or an offer that does not belong to you."
            , true, 'itemmarket.php');
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
    addItem($userid, $r['imITEM'], $r['imQTY']);
    $db->query("DELETE FROM `itemmarket` WHERE `imID` = {$_GET['ID']}");
    $imr_log = $db->escape("Removed {$r['itmname']} x {$r['imQTY']} from the item market.");
    $api->game->addLog($userid, 'imarket', $imr_log);
    alert('success', "Success!", "You have removed your offer successfully. Your item(s) have returned to your inventory."
        , true, 'itemmarket.php');
}

function buy()
{
    global $db, $ir, $userid, $h, $api, $_CONFIG;
    $ID = filter_input(INPUT_GET, 'ID', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    $QTY = filter_input(INPUT_POST, 'QTY', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    if ($ID && !$QTY) {
        $q = $db->query("SELECT `imADDER`, `imCURRENCY`, `imPRICE`, `imQTY`,
                         `imITEM`, `imID`, `itmname` FROM `itemmarket` AS `im`
                         INNER JOIN `items` AS `i` ON `i`.`itmid` = `im`.`imITEM`
                         WHERE `im`.`imID` = {$ID}");
        if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "You are trying to buy an non-existent offer.", true, 'itemmarket.php');
            die($h->endpage());
        }
        $r = $db->fetch_row($q);
        $db->free_result($q);
        $csrf = getHtmlCSRF("imbuy_{$ID}");
        echo "<form method='post' action='?action=buy&ID={$ID}'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Enter however many {$r['itmname']}(s) you wish to purchase. There's currently {$r['imQTY']} in this listing.
				</th>
			</tr>
			<tr>
				<th>
					Quantity
				</th>
				<td>
					<input type='text' name='QTY' class='form-control' value='{$r['imQTY']}'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Buy Offer'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>";
    } elseif (!$ID) {
        alert('danger', "Uh Oh!", "Please specify an offer you wish to buy.", true, 'itemmarket.php');
    } else {
        $q = $db->query("SELECT `imADDER`, `imCURRENCY`, `imPRICE`, `imQTY`,
						`imITEM`, `imID`, `itmname` FROM `itemmarket` AS `im`
						INNER JOIN `items` AS `i` ON `i`.`itmid` = `im`.`imITEM`
						WHERE `im`.`imID` = {$ID}");
        if (!$db->num_rows($q)) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "Please specify an offer you wish to manipulate.");
            die($h->endpage());
        }
        $r = $db->fetch_row($q);
        $db->free_result($q);
        if (!isset($_POST['verf']) || !checkCSRF("imbuy_{$ID}", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Form requests expire fairly quickly. Go back and fill in the form faster next time.");
            die($h->endpage());
        }
        if ($r['imADDER'] == $userid) {
            alert('danger', "Uh Oh!", "You cannot buy your own offer, silly.", true, 'itemmarket.php');
            die($h->endpage());
        }
        if ($api->user->checkIP($userid, $r['imADDER'])) {
            alert('danger', "Uh Oh!", "You cannot buy an offer from someone who shares your IP Address.", true, 'itemmarket.php');
            die($h->endpage());
        }
        $curr = ($r['imCURRENCY'] == 'primary') ? 'primary_currency' : 'secondary_currency';
        $curre = ($r['imCURRENCY'] == 'primary') ? $_CONFIG['primary_currency'] : $_CONFIG['secondary_currency'];
        $final_price = $r['imPRICE'] * $_POST['QTY'];
        if ($final_price > $ir[$curr]) {
            alert('danger', "Uh Oh!", "You do not have enough currency on-hand to buy this offer.");
            die($h->endpage());
        }
        if ($QTY > $r['imQTY']) {
            alert('danger', "Uh Oh!", "You are trying to buy more than there's currently available in this listing.");
            die($h->endpage());
        }
        addItem($userid, $r['imITEM'], $QTY);
        if ($_POST['QTY'] == $r['imQTY']) {
            $db->query("DELETE FROM `itemmarket` WHERE `imID` = {$ID}");
        } elseif ($_POST['QTY'] < $r['imQTY']) {
            $db->query("UPDATE `itemmarket` SET `imQTY` = `imQTY` - {$QTY} WHERE `imID` = {$ID}");
        }
        $db->query("UPDATE `users` SET `$curr` = `$curr` - {$final_price} WHERE `userid` = $userid");
        $db->query("UPDATE `users` SET `$curr` = `$curr` + {$final_price} WHERE `userid` = {$r['imADDER']}");
        addNotification($r['imADDER'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> bought
                {$_POST['QTY']} {$r['itmname']}(s) from the market for " . number_format($final_price) . " {$curre}.");
        $imb_log = $db->escape("Bought {$r['itmname']} x {$QTY} from the item market for
			    " . number_format($final_price) . " {$curre} from user ID {$r['imADDER']}");
        alert('success', "Success!", "You have successfully bought {$r['itmname']} x {$QTY} from the item
			    market for " . number_format($final_price) . " {$curre}", true, 'itemmarket.php');
        $api->game->addLog($userid, 'imarket', $imb_log);
    }
}

function gift()
{
    global $db, $ir, $userid, $h, $api, $_CONFIG;
    $ID = filter_input(INPUT_GET, 'ID', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    $QTY = filter_input(INPUT_POST, 'QTY', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    $user = filter_input(INPUT_POST, 'user', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    $POST_ID = filter_input(INPUT_POST, 'ID', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    if (!$ID) {
        alert('danger', "Uh Oh!", "Please specify a listing you wish to gift.", true, 'itemmarket.php');
    } elseif (!empty($user)) {
        if ((empty($POST_ID) || empty($QTY))) {
            alert('danger', "Uh Oh!", "Please fill out the previous form completely before submitting it.");
            die($h->endpage());
        }
        if (!isset($_POST['verf']) || !checkCSRF("imgift_{$ID}", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Form requests expire fairly quickly. Go back and fill in the form faster next time.");
            die($h->endpage());
        }
        $query_user_exist = $db->query("SELECT COUNT(`userid`) FROM `users` WHERE `userid` = {$user}");
        if ($db->fetch_single($query_user_exist) == 0) {
            $db->free_result($query_user_exist);
            alert('danger', "Uh Oh!", "You are trying to gift this listing to a non-existent user.");
            die($h->endpage());
        }
        $db->free_result($query_user_exist);
        $q = $db->query("SELECT `imADDER`, `imCURRENCY`, `imPRICE`, `imQTY`,
						`imITEM`, `imID`, `itmname` FROM `itemmarket` AS `im`
						INNER JOIN `items` AS `i` ON `i`.`itmid` = `im`.`imITEM`
						WHERE `im`.`imID` = {$POST_ID}");
        if ($db->num_rows($q) == 0) {
            alert('danger', "Uh Oh!", "You are trying to gift a non-existent listing.", true, 'itemmarket.php');
            die($h->endpage());
        }
        $r = $db->fetch_row($q);
        $db->free_result($q);
        if ($r['imADDER'] == $userid) {
            alert('danger', "Uh Oh!", "You cannot buy your own offer, silly.", true, 'itemmarket.php');
            die($h->endpage());
        }
        if ($api->user->checkIP($userid, $r['imADDER'])) {
            alert('danger', "Uh Oh!", "You cannot buy an offer from someone who shares your IP Address.", true, 'itemmarket.php');
            die($h->endpage());
        }
        if ($api->user->checkIP($userid, $user)) {
            alert('danger', "Uh Oh!", "You cannot gift an offer to someone who shares your IP Address.", true, 'itemmarket.php');
            die($h->endpage());
        }
        $curr = ($r['imCURRENCY'] == 'primary') ? 'primary_currency' : 'secondary_currency';
        $curre = ($r['imCURRENCY'] == 'primary') ? $_CONFIG['primary_currency'] : $_CONFIG['secondary_currency'];
        $final_price = $api->SystemReturnTax($r['imPRICE'] * $_POST['QTY']);
        if ($final_price > $ir[$curr]) {
            alert('danger', "Uh Oh!", "You do not have enough currency on-hand to buy this offer.");
            die($h->endpage());
        }
        if ($QTY > $r['imQTY']) {
            alert('danger', "Uh Oh!", "You are trying to buy more than there's currently available in this listing.");
            die($h->endpage());
        }
        if ($user == $r['imADDER']) {
            alert('danger', "Uh Oh!", "You cannot gift this listing to the listing owner.");
            die($h->endpage());
        }
        addItem($user, $r['imITEM'], $QTY);
        if ($QTY == $r['imQTY']) {
            $db->query(
                "DELETE FROM `itemmarket`
					 WHERE `imID` = {$POST_ID}");
        } elseif ($QTY < $r['imQTY']) {
            $db->query("UPDATE `itemmarket` SET `imQTY` = `imQTY` - {$_POST['QTY']} WHERE `imID` = {$_POST['ID']}");
        }
        $db->query("UPDATE `users` SET `{$curr}` = `{$curr}` - {$final_price} WHERE `userid`= {$userid}");
        $db->query("UPDATE `users` SET `{$curr}` = `{$curr}` + {$final_price} WHERE `userid` = {$r['imADDER']}");
        addNotification($POST_ID, "<a href='profile.php?user=$userid'>{$ir['username']}</a> bought you
            {$QTY} {$r['itmname']}(s) from the market.");
        addNotification($r['imADDER'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> bought
            {$QTY} {$r['itmname']}(s) from the market for " . number_format($final_price) . " {$curre}.");
        $imb_log = $db->escape("Bought {$r['itmname']} x{$_POST['QTY']} from the item market for
		    " . number_format($final_price) . " {$curre} from User ID {$r['imADDER']} and gifted to User ID {$_POST['user']}");
        $api->game->addLog($userid, 'imarket', $imb_log);
        alert('success', "Success!", "You have bought {$r['itmname']} x {$QTY} from the item market for
		    " . number_format($final_price) . " {$curre} from User ID {$r['imADDER']} and gifted to User ID
		    {$user}", true, 'index.php');

    } else {
        $q = $db->query("SELECT `imADDER`, `imCURRENCY`, `imPRICE`, `imQTY`,
                         `imITEM`, `imID`, `itmname` FROM `itemmarket` AS `im`
                         INNER JOIN `items` AS `i` ON `i`.`itmid` = `im`.`imITEM`
                         WHERE `im`.`imID` = {$ID}");
        if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "Please specify an offer you wish to manipulate.", true, 'itemmarket.php');
            die($h->endpage());
        }
        $r = $db->fetch_row($q);
        $db->free_result($q);
        $csrf = getHtmlCSRF("imgift_{$ID}");
        echo "<form method='post' action='?action=gift&ID={$ID}'>
		<input type='hidden' name='ID' value='{$ID}' />
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					You are attempting to gift the listing for {$r['itmname']}. There's currently {$r['imQTY']}
					available. Fill out the form below.
				</th>
			</tr>
			<tr>
				<th>
					Gift To
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
					<input type='text' name='QTY' class='form-control' value='{$r['imQTY']}'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Gift Listing'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>";
    }
}

function add()
{
    global $userid, $db, $h, $api, $_CONFIG;
    $QTY = filter_input(INPUT_POST, 'QTY', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    $id = filter_input(INPUT_POST, 'ID', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    $_POST['currency'] = (isset($_POST['currency']) && in_array($_POST['currency'], array('primary', 'secondary'))) ? $_POST['currency'] : 'primary';
    if ($price && $QTY && $id) {
        if (!isset($_POST['verf']) || !checkCSRF("imadd_form", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Form requests expire fairly quickly. Go back and fill in the form faster next time.");
            die($h->endpage());
        }
        $haveitem=$api->UserHasItem($userid,$id,$QTY);
        if (!$haveitem) {
            alert('danger', "Uh Oh!", "You are trying to add an item you do not have, or trying to add more than you have.", true, 'inventory.php');
            die($h->endpage());
        } else {
            $checkq = $db->query("SELECT `imID` FROM `itemmarket` WHERE  `imITEM` =
								{$ID} AND  `imPRICE` = {$price}
								AND  `imADDER` = {$userid} AND `imCURRENCY` = '{$_POST['currency']}'");
            if ($db->num_rows($checkq) > 0) {
                $cqty = $db->fetch_row($checkq);
                $db->query("UPDATE `itemmarket` SET `imQTY` = `imQTY` + {$QTY} WHERE `imID` = {$cqty['imID']}");
            } else {
                $db->query("INSERT INTO `itemmarket` VALUES  (NULL,
							'{$id}', {$userid}, {$price},
							'{$_POST['currency']}', {$QTY})");
            }
            $db->free_result($checkq);
            takeItem($userid, $id, $qty);
            $itemname=$api->game->getItemNameFromID($id);
            $imadd_log = $db->escape("Listed {$QTY} {$itemname}(s) on the item market for {$price} {$_POST['currency']}");
            $api->game->addLog($userid, 'imarket', $imadd_log);
            alert('success', "Success!", "You have successfully listed {$QTY} {$itemname}(s) on the item
			    market for {$price} {$_POST['currency']}.", true, 'itemmarket.php');
        }
    } else {
        $csrf = getHtmlCSRF("imadd_form");
        echo "<form method='post' action='?action=add'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Select the item you wish to add to the item market.
				</th>
			</tr>
			<tr>
				<th>
					Item
				</th>
				<td>
					" . dropdownInventory('ID') . "
				</th>
			</tr>
			<tr>
				<th>
					Quantity
				</th>
				<td>
					<input type='number' min='1' required='1' class='form-control' name='QTY'>
				</th>
			</tr>
			<tr>
				<th>
					Price per Item
				</th>
				<td>
					<input  type='number' min='1' required='1' class='form-control' name='price' />
				</td>
			</tr>
			<tr>
				<th>
					Currency Type
				</th>
				<td>
					<select name='currency' type='dropdown' class='form-control'>
						<option value='primary'>{$_CONFIG['primary_currency']}</option>
						<option value='secondary'>{$_CONFIG['secondary_currency']}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Add Listing'
				</td>
			</tr>
			{$csrf}
		</table>
		</form>";
    }
}
$h->endpage();