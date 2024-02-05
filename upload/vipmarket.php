<?php
/*
	File:		vipmarket.php
	Created: 	5/19/2016 at 4:12PM Eastern Time
	Info: 		Lists VIP Days offers and allows players to 
                buy or list their own offer.
	Author:		TheMasterGeneral
	Website: 	https://chivalryisdeadgame.com/
*/
require('globals.php');
echo "<h3>VIP Days Market</h3><hr />";
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "buy":
        buy();
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
    global $db, $userid, $api, $h;
    $q=$db->query("SELECT * FROM `vip_market`");
   if ($db->num_rows($q) == 0)
   {
       alert('danger',"Uh Oh!","There's no VIP Day offers on the market at this time. Maybe you should <a href='?action=add'>add your own</a>?",true,'explore.php');
       die($h->endpage());
   }
    echo "
	[<a href='?action=add'>Add Your Own Listing</a>]
	<br />
	<table class='table table-bordered table-hover table-striped'>
		<tr>
			<th>Listing Owner</th>
			<th>VIP Days</th>
			<th>Price/Day</th>
			<th>Total Price</th>
			<th>Links</th>
		</tr>";
    while ($r=$db->fetch_row($q))
    {
        if ($r['vip_user'] == $userid) {
            $link =
                "<a class='btn btn-primary btn-sm' href='?action=remove&ID={$r['vip_id']}'><i class='far fa-trash-alt'></i></a>";
        } else {
            $link =
                "<a class='btn btn-primary btn-sm' href='?action=buy&ID={$r['vip_id']}'><i class='fas fa-dollar-sign'></i></a>";
        }
        $total=$r['vip_days']*$r['vip_cost'];
        echo "<tr>
            <td>
                <a href='profile.php?user={$r['vip_user']}'>{$api->SystemUserIDtoName($r['vip_user'])}</a> " . parseUserID($r['vip_user']) . "
            </td>
            <td>
                " . shortNumberParse($r['vip_days']) . " Day(s)
            </td>
            <td>
                " . copperParse($r['vip_cost']) . "
            </td>
            <td>
                " . copperParse($total) . " Copper Coins
            </td>
            <td>
                {$link}
            </td>
        </tr>";
    }
    echo "</table>";
    $h->endpage();
}
function add()
{
    global $userid, $db, $h, $api, $ir;
    $_POST['price'] = (isset($_POST['price']) && is_numeric($_POST['price'])) ? abs($_POST['price']) : '';
    $_POST['QTY'] = (isset($_POST['QTY']) && is_numeric($_POST['QTY'])) ? abs($_POST['QTY']) : '';
	$_POST['deposit'] = (isset($_POST['deposit']) && in_array($_POST['deposit'], array('false', 'true'))) ? $_POST['deposit'] : 'false';
    if ($_POST['price'] && $_POST['QTY']) {
        if (!isset($_POST['verf']) || !verify_csrf_code("vmadd_form", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Form requests expire fairly quickly. Go back and fill in the form faster next time.");
            die($h->endpage());
        }
        if ($ir['vip_days'] < $_POST['QTY']) {
            alert('danger', "Uh Oh!", "You do not have " . number_format($_POST['QTY']) . " VIP Days to sell. You only have " . number_format($ir['vip_days']) . " VIP Days", true, 'inventory.php');
            die($h->endpage());
        } else {
            if ($_POST['price'] < 250000)
            {
                alert('danger','Uh Oh!',"You cannot charge less than " . copperParse(250000) . " per VIP Day.");
                die($h->endpage());
            }
            if ($_POST['price'] > 10000000)
            {
                alert('danger','Uh Oh!',"You cannot charge more than " . copperParse(10000000) . " Copper Coins per VIP Day.");
                die($h->endpage());
            }
            $db->query("INSERT INTO `vip_market` (`vip_user`, `vip_cost`, `vip_days`, `vip_deposit`) VALUES ('{$userid}', '{$_POST['price']}', '{$_POST['QTY']}', '{$_POST['deposit']}')");
            $db->query("UPDATE `users` SET `vip_days` = `vip_days` - {$_POST['QTY']} WHERE `userid` = {$userid}");
            $imadd_log = $db->escape("Listed " . shortNumberParse($_POST['QTY']) . " VIP Days(s) on the item market for " . copperParse($_POST['price']));
            $api->SystemLogsAdd($userid, 'vipmarket', $imadd_log);
            $num_format=copperParse($_POST['price']);
            alert('success', "Success!", "You have successfully listed " . shortNumberParse($_POST['QTY']) . " VIP Days(s) on the VIP
			    market for {$num_format}.", true, 'vipmarket.php');
                $h->endpage();
        }
    } else {
        $csrf = request_csrf_html("vmadd_form");
        echo "<form method='post' action='?action=add'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Input how many VIP Days you wish to sell, and a price per day. Note, your VIP Days on your offers will decrease each day, so don't expect to use this system to get unlimited VIP Days.
				</th>
			</tr>
			<tr>
				<th>
					VIP Days<br />
					<small>To be listed on the market.</small>
				</th>
				<td>
					<input type='number' min='1' required='1' max='{$ir['vip_days']}' value='{$ir['vip_days']}' class='form-control' name='QTY'>
				</th>
			</tr>
			<tr>
				<th>
					" . loadImageAsset("menu/coin-copper.svg") . "/VIP Day<br />
					<small>Subject to a 2% market fee.</small>
				</th>
				<td>
					<input  type='number' min='250000' required='1' max='10000000' value='1000000' class='form-control' name='price' />
				</td>
			</tr>
			<tr>
					<th>
						Deposit Location<br />
						<small>Automatic bank deposits have a 5% fee.</small>
					</th>
					<td>
						<select name='deposit' type='dropdown' class='form-control'>
							<option value='false'>Wallet</option>
							<option value='true'>Bank</option>
						</select>
					</td>
				</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary btn-block' value='Add Listing'
				</td>
			</tr>
			{$csrf}
		</table>
		</form>
		*=2% will be taken from this number for market fees.";
    }
}

function buy()
{
    global $db, $ir, $userid, $h, $api;
    $_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs($_GET['ID']) : '';
    if (empty($_GET['ID'])) {
        alert('danger', "Uh Oh!", "Please specify a listing you wish to buy.", true, 'vipmarket.php');
        die($h->endpage());
    }
    $q = $db->query("/*qc=on*/SELECT * FROM `vip_market` WHERE `vip_id` = {$_GET['ID']}");
    if ($db->num_rows($q) == 0) {
        alert('danger', "Uh Oh!", "Please specify an existent listing to buy.", true, 'vipmarket.php');
        die($h->endpage());
    }
    $r = $db->fetch_row();
    if ($r['vip_user'] == $userid) {
        alert('danger', "Uh Oh!", "You cannot buy your own listing.", true, 'vipmarket.php');
        die($h->endpage());
    }
	if ($api->SystemCheckUsersIPs($userid, $r['vip_user'])) 
	{
		alert('danger', "Uh Oh!", "You cannot buy an offer from someone who shares your IP Address.", true, 'itemmarket.php');
		die($h->endpage());
	}
    $totalcost = $r['vip_cost'] * $r['vip_days'];
	$remove = 0.02;
	if ($r['vip_deposit'] == 'true')
		$remove = $remove + 0.05;
	$taxed=$totalcost-($totalcost*$remove);
	addToEconomyLog('Market Fees', 'copper', ($totalcost*$remove)*-1);
    if ($api->UserHasCurrency($userid, 'primary', $totalcost) == false) {
        alert('danger', "Uh Oh!", "You need " . copperParse($totalcost) . " to buy this listing.", true, 'vipmarket.php');
        die($h->endpage());
    }
    $api->SystemLogsAdd($userid, 'vipmarket', "Bought " . shortNumberParse($r['vip_days']) . " VIP Days from the market for " . copperParse($totalcost));
    $api->UserGiveCurrency($userid, 'secondary', $r['vip_days']);
    $db->query("UPDATE `users` SET `vip_days` = `vip_days` + {$r['vip_days']} WHERE `userid` = {$userid}");
    $api->UserTakeCurrency($userid, 'primary', $totalcost);
    $api->UserGiveCurrency($r['vip_user'], 'primary', $taxed);
    $api->GameAddNotification($r['vip_user'], "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has bought your
        " . shortNumberParse($r['vip_days']) . " VIP Day(s) offer from the market for a total of " . copperParse($taxed) . " Copper Coins.");
    $db->query("DELETE FROM `vip_market` WHERE `vip_id` = {$_GET['ID']}");
    alert('success', "Success!", "You have bought " . shortNumberParse($r['vip_days']) . " VIP Day(s) for " . copperParse($totalcost), true, 'vipmarket.php');
    die($h->endpage());
}

function remove()
{
    global $db, $h, $userid, $api;
    $_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs($_GET['ID']) : '';
    if (empty($_GET['ID'])) {
        alert('danger', "Uh Oh!", "Please specify a listing you wish to remove.", true, 'vipmarket.php');
        die($h->endpage());
    }
    $q = $db->query("/*qc=on*/SELECT * FROM `vip_market` WHERE `vip_id` = {$_GET['ID']}");
    if ($db->num_rows($q) == 0) {
        alert('danger', "Uh Oh!", "You are trying to remove a non-existent listing.", true, 'vipmarket.php');
        die($h->endpage());
    }
    $r = $db->fetch_row();
    if (!($r['vip_user'] == $userid)) {
        alert('danger', "Uh Oh!", "You are trying to remove a lising you do not own.", true, 'vipmarket.php');
        die($h->endpage());
    }
    $api->SystemLogsAdd($userid, 'vipmarket', "Removed " . shortNumberParse($r['vip_days']) . " VIP Days from the market.");
    $db->query("UPDATE `users` SET `vip_days` = `vip_days` + {$r['vip_days']} WHERE `userid` = {$userid}");
    $db->query("DELETE FROM `vip_market` WHERE `vip_id` = {$_GET['ID']}");
    alert('success', "Success!", "You have removed your listing for " . shortNumberParse($r['vip_days']) . " VIP Days from the market.", true, 'vipmarket.php');
    die($h->endpage());
}