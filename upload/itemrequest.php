<?php
/*
	File:		itemrequest.php
	Created: 	1/19/2018 at 11:53AM Eastern Time
	Info: 		Lists items placed on the market by other players,
				allows players to buy/gift those items, and sell
				their own items.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
	alert('danger',"Uh Oh!","You cannot visit the item request listing while in the infirmary or dungeon.",true,'index.php');
	die($h->endpage());
}
echo "<h3><i class='game-icon game-icon-trade'></i> Item Request Listing</h3><hr />";
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
    global $db, $userid, $api;
    echo "
	<form>
		Search Item Requests
		" . itemrequest_dropdown('item') . "
		<input type='submit' value='Search' class='btn btn-primary'>
	</form>
	<hr />
	[<a href='?action=add'>Add Request</a>]
	<br />
	<table class='table table-bordered table-hover table-striped'>
		<tr>
			<th>Listing Owner</th>
			<th>Requesting</th>
			<th>Copper/item</th>
			<th>Links</th>
		</tr>
   ";
	if (isset($_GET['item']))
   {
	   $_GET['item'] = (isset($_GET['item']) && is_numeric($_GET['item'])) ? abs($_GET['item']) : '';
	   if ($_GET['item'] > 0)
	   {
		   $q =
			$db->query(
            "SELECT `irCOST`, `irQTY`, `irITEM`, `irUSER`,
                     `irID`, `itmid`, `itmname`, `userid`,`username`, `itmdesc`,
                     `itmtypename`
                     FROM `itemrequest` AS `ir`
                     INNER JOIN `items` AS `i`
                     ON `ir`.`irITEM` = `i`.`itmid`
                     INNER JOIN `users` AS `u`
                     ON `u`.`userid` = `ir`.`irUSER`
                     INNER JOIN `itemtypes` AS `it`
                     ON `i`.`itmtype` = `it`.`itmtypeid`
					 WHERE `irITEM` = {$_GET['item']}
                     ORDER BY `i`.`itmtype`, `i`.`itmname`, `u`.`username` ASC");
	   }
	   else
	   {
		   $q =
			$db->query(
            "SELECT `irCOST`, `irQTY`, `irITEM`, `irUSER`,
                     `irID`, `itmid`, `itmname`, `userid`,`username`, `itmdesc`,
                     `itmtypename`
                     FROM `itemrequest` AS `ir`
                     INNER JOIN `items` AS `i`
                     ON `ir`.`irITEM` = `i`.`itmid`
                     INNER JOIN `users` AS `u`
                     ON `u`.`userid` = `ir`.`irUSER`
                     INNER JOIN `itemtypes` AS `it`
                     ON `i`.`itmtype` = `it`.`itmtypeid`
                     ORDER BY `i`.`itmtype`, `i`.`itmname`, `u`.`username` ASC");
	   }
   }
   else
   {
		$q =
        $db->query(
            "SELECT `irCOST`, `irQTY`, `irITEM`, `irUSER`,
                     `irID`, `itmid`, `itmname`, `userid`,`username`, `itmdesc`,
                     `itmtypename`
                     FROM `itemrequest` AS `ir`
                     INNER JOIN `items` AS `i`
                     ON `ir`.`irITEM` = `i`.`itmid`
                     INNER JOIN `users` AS `u`
                     ON `u`.`userid` = `ir`.`irUSER`
                     INNER JOIN `itemtypes` AS `it`
                     ON `i`.`itmtype` = `it`.`itmtypeid`
                     ORDER BY `i`.`itmtype`, `i`.`itmname`, `u`.`username` ASC");
   }
    $cblah = 1;
    $lt = "";
    while ($r = $db->fetch_row($q)) {
        if ($lt != $r['itmtypename']) {
            $lt = $r['itmtypename'];
            echo "<tr>
					<td colspan='5' align='center'><b>{$lt}</b></td>
				</tr>";
        }
        $ctprice = ($r['irCOST'] * $r['irQTY']);
        $price = number_format($r['irCOST']) . " Copper Coins";
        if ($r['irUSER'] == $userid) {
            $link =
                "[<a href='?action=remove&ID={$r['irID']}'>Remove</a>]";
        } else {
            $link =
                "[<a href='?action=buy&ID={$r['irID']}'>Fulfill</a>]";
        }
        $r['itmdesc'] = htmlentities($r['itmdesc'], ENT_QUOTES);
		$icon = returnIcon($r['itmid']);
        echo "
		<tr>
			<td>
				<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
			</td>
			<td>
				{$icon} <a href='iteminfo.php?ID={$r['itmid']}' data-toggle='tooltip' data-placement='right' title='{$r['itmdesc']}'>{$r['itmname']}</a>";
        if ($r['irQTY'] > 1) {
            echo " x {$r['irQTY']}";
        }
        echo "</td>
			<td>
				{$price}
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
    $_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs($_GET['ID']) : '';
    if (empty($_GET['ID'])) {
        alert('danger', "Uh Oh!", "Please specify the offer you wish to remove.", true, 'itemrequest.php');
        die($h->endpage());
    }
    $q = $db->query("SELECT *
                    FROM `itemrequest` AS `ir` INNER JOIN `items` AS `i`
                    ON `ir`.`irITEM` = `i`.`itmid`  WHERE `ir`.`irID` = {$_GET['ID']}
                    AND `ir`.`irUSER` = {$userid}");
    if ($db->num_rows($q) == 0) {
        alert('danger', "Uh Oh!", "You are trying to remove a non-existent offer, or an offer that does not belong to you."
            , true, 'itemrequest.php');
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
    $api->UserGiveCurrency($userid,'primary',$r['irCOST']*$r['irQTY']);
    $db->query("DELETE FROM `itemrequest` WHERE `irID` = {$_GET['ID']}");
    $imr_log = $db->escape("Removed request for {$r['itmname']} x {$r['irQTY']} at {$r['irCOST']} Copper Coins each.");
    $api->SystemLogsAdd($userid, 'irequest', $imr_log);
    alert('success', "Success!", "You have removed your offer successfully. Your copper has been returned to you."
        , true, 'itemrequest.php');
}

function buy()
{
    global $db, $ir, $userid, $h, $api;
    $_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs($_GET['ID']) : '';
    $_POST['QTY'] = (isset($_POST['QTY']) && is_numeric($_POST['QTY'])) ? abs($_POST['QTY']) : '';
    if ($_GET['ID'] && !$_POST['QTY']) {
        $q = $db->query("SELECT `irUSER`, `irCOST`, `irQTY`,
                         `irITEM`, `irID`, `itmname` FROM `itemrequest` AS `ir`
                         INNER JOIN `items` AS `i` ON `i`.`itmid` = `ir`.`irITEM`
                         WHERE `ir`.`irID` = {$_GET['ID']}");
        if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "You are trying to buy an non-existent offer.", true, 'itemrequest.php');
            die($h->endpage());
        }
        $r = $db->fetch_row($q);
        $db->free_result($q);
        $csrf = request_csrf_html("imbuy_{$_GET['ID']}");
        echo "<form method='post' action='?action=buy&ID={$_GET['ID']}'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Enter however many {$r['itmname']}(s) you wish to give towards this request. The listing 
					needs {$r['irQTY']} more before its completed. You will be charged a 2% processing fee upon fulfilling 
					the request.
				</th>
			</tr>
			<tr>
				<th>
					Quantity
				</th>
				<td>
					<input type='text' name='QTY' class='form-control' value='{$r['irQTY']}'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Fulfill Request'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>";
    } elseif (!$_GET['ID']) {
        alert('danger', "Uh Oh!", "Please specify an offer you wish to fulfill.", true, 'itemrequest.php');
    } else {
        $q = $db->query("SELECT `irUSER`, `irCOST`, `irQTY`,
						`irITEM`, `irID`, `itmname` FROM `itemrequest` AS `ir`
						INNER JOIN `items` AS `i` ON `i`.`itmid` = `ir`.`irITEM`
						WHERE `ir`.`irID` = {$_GET['ID']}");
        if (!$db->num_rows($q)) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "Please specify an offer you wish to manipulate.");
            die($h->endpage());
        }
        $r = $db->fetch_row($q);
        $db->free_result($q);
        if (!isset($_POST['verf']) || !verify_csrf_code("imbuy_{$_GET['ID']}", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Form requests expire fairly quickly. Go back and fill in the form faster next time.");
            die($h->endpage());
        }
        if ($r['irUSER'] == $userid) {
            alert('danger', "Uh Oh!", "You cannot fulfill your own offer, silly.", true, 'itemrequest.php');
            die($h->endpage());
        }
        if ($api->SystemCheckUsersIPs($userid, $r['irUSER'])) {
            alert('danger', "Uh Oh!", "You cannot fulfill an offer from someone who shares your IP Address.", true, 'itemrequest.php');
            die($h->endpage());
        }
        $final_price = $api->SystemReturnTax($r['irCOST'] * $_POST['QTY']);
		$taxed=$final_price-($final_price*0.02);
        if ($_POST['QTY'] > $r['irQTY']) {
            alert('danger', "Uh Oh!", "You are trying to fulfill more than the listing's request.");
            die($h->endpage());
        }
		if (!$api->UserHasItem($userid,$r['irITEM'],$_POST['QTY']))
		{
			alert('danger',"Uh Oh!","You don't even have that many to contribute!");
			die($h->endpage());
		}
        item_add($r['irUSER'], $r['irITEM'], $_POST['QTY']);
        if ($_POST['QTY'] == $r['irQTY']) {
            $db->query("DELETE FROM `itemrequest` WHERE `irID` = {$_GET['ID']}");
        } elseif ($_POST['QTY'] < $r['irQTY']) {
            $db->query("UPDATE `itemrequest` SET `irQTY` = `irQTY` - {$_POST['QTY']} WHERE `irID` = {$_GET['ID']}");
        }
		$api->UserGiveCurrency($userid,'primary',$taxed);
		$api->UserTakeItem($userid,$r['irITEM'],$_POST['QTY']);
        notification_add($r['irUSER'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> helped to fulfill your item request by giving
                {$_POST['QTY']} {$r['itmname']}(s) " . number_format($taxed) . " in exchange for {$final_price} Copper Coins.");
        $imb_log = $db->escape("Contributed {$r['itmname']} x {$_POST['QTY']} to User ID {$r['irUSER']}'s item request, in exchange for " . number_format($final_price) . " Copper Coins.");
        alert('success', "Success!", "You have contributed {$r['itmname']} x {$_POST['QTY']} towards this offer and received 
		" . number_format($taxed) . " Copper Coins.", true, 'itemrequest.php');
        $api->SystemLogsAdd($userid, 'irequest', $imb_log);
    }
}

function add()
{
    global $userid, $db, $h, $api;
    $_POST['ID'] = (isset($_POST['ID']) && is_numeric($_POST['ID'])) ? abs($_POST['ID']) : '';
    $_POST['price'] = (isset($_POST['price']) && is_numeric($_POST['price'])) ? abs($_POST['price']) : '';
    $_POST['QTY'] = (isset($_POST['QTY']) && is_numeric($_POST['QTY'])) ? abs($_POST['QTY']) : '';
    if ($_POST['price'] && $_POST['QTY'] && $_POST['ID']) {
        if (!isset($_POST['verf']) || !verify_csrf_code("imadd_form", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Form requests expire fairly quickly. Go back and fill in the form faster next time.");
            die($h->endpage());
        }
        $haveitem=$api->UserHasCurrency($userid,'primary',$_POST['QTY']*$_POST['price']);
        if (!$haveitem) {
            alert('danger', "Uh Oh!", "You are trying to set a cost that you cannot pay for. Have your Copper Coins on you when you create this offer.", true, 'inventory.php');
            die($h->endpage());
        } else {
			if (!$api->SystemItemIDtoName($_POST['ID']))
			{
				alert('danger',"Uh Oh!","The item you are requesting does not exist.");
				die($h->endpage());
			}
                $db->query("INSERT INTO `itemrequest` VALUES  (NULL,
							'{$userid}', {$_POST['ID']}, {$_POST['QTY']}, {$_POST['price']})");
            $api->UserTakeCurrency($userid,'primary',$_POST['QTY']*$_POST['price']);
            $itemname=$api->SystemItemIDtoName($_POST['ID']);
            $imadd_log = $db->escape("Requested {$_POST['QTY']} {$itemname}(s) for {$_POST['price']} Copper Coins (each).");
            $api->SystemLogsAdd($userid, 'irequest', $imadd_log);
			$num_format=number_format($_POST['price']);
            alert('success', "Success!", "You have successfully requested {$_POST['QTY']} {$itemname}(s) on the item
			    market for {$num_format} Copper Coins (each).", true, 'itemrequest.php');
        }
    } else {
        $csrf = request_csrf_html("imadd_form");
        echo "<form method='post' action='?action=add'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Complete the form to request an item from another player.
				</th>
			</tr>
			<tr>
				<th>
					Item
				</th>
				<td>
					" . item_dropdown('ID') . "
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
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Add Request'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>";
    }
}function itemrequest_dropdown($ddname = "item", $selected = -1)
{
    global $db, $userid;
    $ret = "<select name='$ddname' type='dropdown' class='form-control'>";
    $q =
        $db->query(
            "SELECT `i`.*, `it`.*
    				 FROM `itemrequest` AS `i`
    				 INNER JOIN `items` AS `it`
    				 ON `i`.`irITEM` = `it`.`itmid`
    				 ORDER BY `itmname` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['itmid']}'";
		if (!isset($count[$r['itmid']]))
			$count[$r['itmid']]=0;
		if ($count[$r['itmid']] == 0)
		{
			if ($selected == $r['itmid'] || $first == 0) {
				$ret .= " selected='selected'";
				$first = 1;
			}
			$ret .= ">{$r['itmname']}</option>";
			$count[$r['itmid']]=$count[$r['itmid']]+1;
		}
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

$h->endpage();