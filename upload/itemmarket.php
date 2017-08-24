<?php
/*
	File:		itemmarket.php
	Created: 	4/5/2016 at 12:15AM Eastern Time
	Info: 		Lists items placed on the market by other players,
				allows players to buy/gift those items, and sell
				their own items.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
echo "<h3>Item Market</h3>";
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
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
	global $db,$userid,$api;
	echo "
	<br />
	<table class='table table-responsive table-bordered table-hover table-striped'>
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
    while ($r = $db->fetch_row($q))
    {
        if ($lt != $r['itmtypename'])
        {
            $lt = $r['itmtypename'];
            echo "<tr>
					<td colspan='5' align='center'><b>{$lt}</b></td>
				</tr>";
        }
        $ctprice = ($r['imPRICE'] * $r['imQTY']);
        if ($r['imCURRENCY'] == 'primary')
        {
            $price = number_format($api->SystemReturnTax($r['imPRICE'])) . " Primary Currency";
            $tprice = number_format($api->SystemReturnTax($ctprice)) . " Primary Currency";
        }
        else
        {
            $price = number_format($api->SystemReturnTax($r['imPRICE'])) . " Secondary Currency";
            $tprice = number_format($api->SystemReturnTax($ctprice)) . " Secondary Currency";
        }
        if ($r['imADDER'] == $userid)
        {
            $link =
                    "[<a href='?action=remove&ID={$r['imID']}'>Remove</a>]";
        }
        else
        {
            $link =
                    "[<a href='?action=buy&ID={$r['imID']}'>Buy</a>]
                    [<a href='?action=gift&ID={$r['imID']}'>Gift</a>]";
        }
		$r['itmdesc']=htmlentities($r['itmdesc'],ENT_QUOTES);
		echo "
		<tr>
			<td>
				<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
			</td>
			<td>
				<a href='iteminfo.php?ID={$r['itmid']}' data-toggle='tooltip' data-placement='right' title='{$r['itmdesc']}'>{$r['itmname']}</a>";
				if ($r['imQTY'] > 1)
				{
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
	global $db,$userid,$h,$api;
	$_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs($_GET['ID']) : '';
	if (empty($_GET['ID']))
    {
        alert('danger',"Uh Oh!","Please specify the offer you wish to remove.",true,'itemmarket.php');
        die($h->endpage());
    }
	$q = $db->query("SELECT `imITEM`, `imQTY`, `imADDER`, `imID`, `itmname`
                    FROM `itemmarket` AS `im` INNER JOIN `items` AS `i`
                    ON `im`.`imITEM` = `i`.`itmid`  WHERE `im`.`imID` = {$_GET['ID']}
                    AND `im`.`imADDER` = {$userid}");
	if ($db->num_rows($q) == 0)
    {
        alert('danger',"Uh Oh!","You are trying to remove a non-existent offer, or an offer that does not belong to you."
            ,true,'itemmarket.php');
        die($h->endpage());
    }
	$r = $db->fetch_row($q);
    item_add($userid, $r['imITEM'], $r['imQTY']);
	$db->query("DELETE FROM `itemmarket` WHERE `imID` = {$_GET['ID']}");
	$imr_log = $db->escape("Removed {$r['itmname']} x {$r['imQTY']} from the item market.");
	$api->SystemLogsAdd($userid,'imarket',$imr_log);
	alert('success',"Success!","You have removed your offer successfully. Your item(s) have returned to your inventory."
        ,true,'itemmarket.php');
}
function buy()
{
	global $db,$ir,$userid,$h,$api;
	$_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs($_GET['ID']) : '';
    $_POST['QTY'] = (isset($_POST['QTY']) && is_numeric($_POST['QTY'])) ? abs($_POST['QTY']) : '';
	if ($_GET['ID'] && !$_POST['QTY'])
    {
        $q = $db->query("SELECT `imADDER`, `imCURRENCY`, `imPRICE`, `imQTY`,
                         `imITEM`, `imID`, `itmname` FROM `itemmarket` AS `im`
                         INNER JOIN `items` AS `i` ON `i`.`itmid` = `im`.`imITEM`
                         WHERE `im`.`imID` = {$_GET['ID']}");
        if ($db->num_rows($q) == 0)
        {
            $db->free_result($q);
            alert('danger',"Uh Oh!","You are trying to buy an non-existent offer.",true,'itemmarket.php');
            die($h->endpage());
        }
        $r = $db->fetch_row($q);
        $db->free_result($q);
		$csrf=request_csrf_html("imbuy_{$_GET['ID']}");
		echo "<form method='post' action='?action=buy&ID={$_GET['ID']}'>
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
    }
	elseif (!$_GET['ID'])
    {
        alert('danger',"Uh Oh!","Please specify an offer you wish to buy.",true,'itemmarket.php');
    }
	else
	{
		$q = $db->query("SELECT `imADDER`, `imCURRENCY`, `imPRICE`, `imQTY`,
						`imITEM`, `imID`, `itmname` FROM `itemmarket` AS `im`
						INNER JOIN `items` AS `i` ON `i`.`itmid` = `im`.`imITEM`
						WHERE `im`.`imID` = {$_GET['ID']}");
			if (!$db->num_rows($q))
			{
				$db->free_result($q);
				alert('danger',"Uh Oh!","Please specify an offer you wish to manipulate.");
				die($h->endpage());
			}
			$r = $db->fetch_row($q);
			$db->free_result($q);
			if (!isset($_POST['verf']) || !verify_csrf_code("imbuy_{$_GET['ID']}", stripslashes($_POST['verf'])))
			{
				alert('danger',"Action Blocked!","Form requests expire fairly quickly. Go back and fill in the form faster next time.");
				die($h->endpage());
			}
			if ($r['imADDER'] == $userid)
			{
				alert('danger',"Uh Oh!","You cannot buy your own offer, silly.",true,'itemmarket.php');
				die($h->endpage());
			}
			if ($api->SystemCheckUsersIPs($userid,$r['imADDER']))
			{
				alert('danger',"Uh Oh!","You cannot buy an offer from someone who shares your IP Address.",true,'itemmarket.php');
				die($h->endpage());
			}
			$curr = ($r['imCURRENCY'] == 'primary') ? 'primary_currency' : 'secondary_currency';
			$curre = ($r['imCURRENCY'] == 'primary') ? 'Primary Currency' : 'Secondary Currency';
			$final_price = $api->SystemReturnTax($r['imPRICE']*$_POST['QTY']);
			if ($final_price > $ir[$curr])
			{
				alert('danger',"Uh Oh!","You do not have enough currency on-hand to buy this offer.");
				die($h->endpage());
			}
			if ($_POST['QTY'] > $r['imQTY'])
			{
				alert('danger',"Uh Oh!","You are trying to buy more than there's currently available in this listing.");
				die($h->endpage());
			}
			item_add($userid, $r['imITEM'], $_POST['QTY']);
			if ($_POST['QTY'] == $r['imQTY'])
			{
				$db->query("DELETE FROM `itemmarket` WHERE `imID` = {$_GET['ID']}");
			}
			elseif ($_POST['QTY'] < $r['imQTY'])
			{
				$db->query("UPDATE `itemmarket` SET `imQTY` = `imQTY` - {$_POST['QTY']} WHERE `imID` = {$_GET['ID']}");
			}
			$db->query("UPDATE `users` SET `$curr` = `$curr` - {$final_price} WHERE `userid` = $userid");
			$db->query("UPDATE `users` SET `$curr` = `$curr` + {$final_price} WHERE `userid` = {$r['imADDER']}");
			notification_add($r['imADDER'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> bought
                {$_POST['QTY']} {$r['itmname']}(s) from the market for " . number_format($final_price) . " {$curre}.");
			$imb_log = $db->escape("Bought {$r['itmname']} x{$_POST['QTY']} from the item market for
			    " . number_format($final_price) . " {$curre} from user ID {$r['imADDER']}");
			alert('success',"Success!","You have successfully bought {$r['itmname']} x{$_POST['QTY']} from the item
			    market for " . number_format($final_price) . " {$curre}",true,'itemmarket.php');
			$api->SystemLogsAdd($userid,'imarket',$imb_log);
			if ($r['imCURRENCY'] == 'primary')
			{
				$api->SystemCreditTax($api->SystemReturnTaxOnly($final_price),1,-1);
			}
			else
			{
				$api->SystemCreditTax($api->SystemReturnTaxOnly($final_price),2,-1);
			}
	}
}
function gift()
{
	global $db,$ir,$userid,$h,$api;
	$_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs($_GET['ID']) : '';
	$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : '';
	$_POST['QTY'] = (isset($_POST['QTY']) && is_numeric($_POST['QTY'])) ? abs($_POST['QTY']) : '';
	$_POST['ID'] = (isset($_POST['ID']) && is_numeric($_POST['ID'])) ? abs($_POST['ID']) : '';
	if (!$_GET['ID'])
    {
        alert('danger',"Uh Oh!","Please specify a listing you wish to gift.",true,'itemmarket.php');
    }
	elseif (!empty($_POST['user']))
	{
		if ((empty($_POST['ID']) || empty($_POST['QTY'])))
		{
			alert('danger',"Uh Oh!","Please fill out the previous form completely before submitting it.");
			die($h->endpage());
		}
		if (!isset($_POST['verf']) || !verify_csrf_code("imgift_{$_GET['ID']}", stripslashes($_POST['verf'])))
		{
			alert('danger',"Action Blocked!","Form requests expire fairly quickly. Go back and fill in the form faster next time.");
			die($h->endpage());
		}
		$query_user_exist = $db->query("SELECT COUNT(`userid`) FROM `users` WHERE `userid` = {$_POST['user']}");
		if ($db->fetch_single($query_user_exist) == 0)
		{
			$db->free_result($query_user_exist);
			alert('danger',"Uh Oh!","You are trying to gift this listing to a non-existent user.");
			die($h->endpage());
		}
		$db->free_result($query_user_exist);
		$q = $db->query("SELECT `imADDER`, `imCURRENCY`, `imPRICE`, `imQTY`,
						`imITEM`, `imID`, `itmname` FROM `itemmarket` AS `im`
						INNER JOIN `items` AS `i` ON `i`.`itmid` = `im`.`imITEM`
						WHERE `im`.`imID` = {$_POST['ID']}");
		if ($db->num_rows($q) == 0)
		{
			alert('danger',"Uh Oh!","You are trying to gift a non-existent listing.",true,'itemmarket.php');
			die($h->endpage());
		}
		$r = $db->fetch_row($q);
		$db->free_result($q);
		if ($r['imADDER'] == $userid)
		{
			alert('danger',"Uh Oh!","You cannot buy your own offer, silly.",true,'itemmarket.php');
			die($h->endpage());
		}
		if ($api->SystemCheckUsersIPs($userid,$r['imADDER']))
		{
			alert('danger',"Uh Oh!","You cannot buy an offer from someone who shares your IP Address.",true,'itemmarket.php');
			die($h->endpage());
		}
		if ($api->SystemCheckUsersIPs($userid,$_POST['user']))
		{
				alert('danger',"Uh Oh!","You cannot gift an offer to someone who shares your IP Address.",true,'itemmarket.php');
				die($h->endpage());
		}
		$curr = ($r['imCURRENCY'] == 'primary') ? 'primary_currency' : 'secondary_currency';
		$curre = ($r['imCURRENCY'] == 'primary') ? 'Primary Currency' : 'Secondary Currency';
		$final_price = $api->SystemReturnTax($r['imPRICE']*$_POST['QTY']);
		if ($final_price > $ir[$curr])
		{
			alert('danger',"Uh Oh!","You do not have enough currency on-hand to buy this offer.");
			die($h->endpage());
		}
		if ($_POST['QTY'] > $r['imQTY'])
		{
			alert('danger',"Uh Oh!","You are trying to buy more than there's currently available in this listing.");
			die($h->endpage());
		}
		if ($_POST['user'] == $r['imADDER'])
		{
			alert('danger',"Uh Oh!","You cannot gift this listing to the listing owner.");
			die($h->endpage());
		}
		item_add($_POST['user'], $r['imITEM'], $_POST['QTY']);
		if ($_POST['QTY'] == $r['imQTY'])
		{
			$db->query(
					"DELETE FROM `itemmarket`
					 WHERE `imID` = {$_POST['ID']}");
		}
		elseif ($_POST['QTY'] < $r['imQTY'])
		{
			$db->query("UPDATE `itemmarket` SET `imQTY` = `imQTY` - {$_POST['QTY']} WHERE `imID` = {$_POST['ID']}");
		}
		$db->query("UPDATE `users` SET `{$curr}` = `{$curr}` - {$final_price} WHERE `userid`= {$userid}");
		$db->query("UPDATE `users` SET `{$curr}` = `{$curr}` + {$final_price} WHERE `userid` = {$r['imADDER']}");
		notification_add($_POST['user'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> bought you
            {$_POST['QTY']} {$r['itmname']}(s) from the market.");
		notification_add($r['imADDER'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> bought
            {$_POST['QTY']} {$r['itmname']}(s) from the market for " . number_format($final_price) . " {$curre}.");
		$imb_log = $db->escape("Bought {$r['itmname']} x{$_POST['QTY']} from the item market for
		    " . number_format($final_price) . " {$curre} from User ID {$r['imADDER']} and gifted to User ID {$_POST['user']}");
		$api->SystemLogsAdd($userid,'imarket',$imb_log);
		alert('success',"Success!","You have bought {$r['itmname']} x{$_POST['QTY']} from the item market for
		    " . number_format($final_price) . " {$curre} from User ID {$r['imADDER']} and gifted to User ID
		    {$_POST['user']}",true,'index.php');
		if ($r['imCURRENCY'] == 'primary')
		{
			$api->SystemCreditTax($api->SystemReturnTaxOnly($final_price),1,-1);
		}
		else
		{
			$api->SystemCreditTax($api->SystemReturnTaxOnly($final_price),2,-1);
		}
		
	}
	else
	{
		$q = $db->query("SELECT `imADDER`, `imCURRENCY`, `imPRICE`, `imQTY`,
                         `imITEM`, `imID`, `itmname` FROM `itemmarket` AS `im`
                         INNER JOIN `items` AS `i` ON `i`.`itmid` = `im`.`imITEM`
                         WHERE `im`.`imID` = {$_GET['ID']}");
        if ($db->num_rows($q) == 0)
        {
            $db->free_result($q);
            alert('danger',"Uh Oh!","Please specify an offer you wish to manipulate.",true,'itemmarket.php');
            die($h->endpage());
        }
        $r = $db->fetch_row($q);
        $db->free_result($q);
		$csrf=request_csrf_html("imgift_{$_GET['ID']}");
		echo "<form method='post' action='?action=gift&ID={$_GET['ID']}'>
		<input type='hidden' name='ID' value='{$_GET['ID']}' />
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
					" . user_dropdown('user') . "
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
	global $userid,$db,$h,$api;
	$_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs($_GET['ID']) : '';
	$_POST['price'] = (isset($_POST['price']) && is_numeric($_POST['price'])) ? abs($_POST['price']) : '';
	$_POST['QTY'] = (isset($_POST['QTY']) && is_numeric($_POST['QTY'])) ? abs($_POST['QTY']) : '';
	$_POST['currency'] = (isset($_POST['currency']) && in_array($_POST['currency'], array('primary', 'secondary'))) ? $_POST['currency'] : 'primary';
	if (empty($_GET['ID']))
	{
		alert('danger',"Uh Oh!","Please specify the item ID you wish to add to the market.",true,'inventory.php');
		die($h->endpage());
	}
	$q = $db->query("SELECT `inv_qty`, `inv_itemid`, `inv_id`, `itmname`, `itmbuyprice`
						FROM `inventory` AS `iv` INNER JOIN `items` AS `i`
						ON `iv`.`inv_itemid` = `i`.`itmid` WHERE `inv_id` = {$_GET['ID']}
						AND `inv_userid` = $userid");
	if ($_POST['price'] && $_POST['QTY'] && $_GET['ID'])
	{
		if (!isset($_POST['verf']) || !verify_csrf_code("imadd_{$_GET['ID']}", stripslashes($_POST['verf'])))
		{
			alert('danger',"Action Blocked!","Form requests expire fairly quickly. Go back and fill in the form faster next time.");
			die($h->endpage());
		}
		if ($db->num_rows($q) == 0)
		{
			$db->free_result($q);
			alert('danger',"Uh Oh!","You do not have this ite to add to the market.",true,'inventory.php');
			die($h->endpage());
		}
		else
		{
			$r = $db->fetch_row($q);
			$db->free_result($q);
			if ($r['inv_qty'] < $_POST['QTY'])
			{
				alert('danger',"Uh Oh!","You are trying to add more of this item to the market than you currently have in your inventory.");
				die($h->endpage());
			}
			$checkq = $db->query("SELECT `imID` FROM `itemmarket` WHERE  `imITEM` = 
								{$r['inv_itemid']} AND  `imPRICE` = {$_POST['price']} 
								AND  `imADDER` = {$userid} AND `imCURRENCY` = '{$_POST['currency']}'");
			if ($db->num_rows($checkq) > 0)
			{
				$cqty = $db->fetch_row($checkq);
				$db->query("UPDATE `itemmarket` SET `imQTY` = `imQTY` + {$_POST['QTY']} WHERE `imID` = {$cqty['imID']}");
			}
			else
			{
				$db->query("INSERT INTO `itemmarket` VALUES  (NULL, 
							'{$r['inv_itemid']}', {$userid}, {$_POST['price']}, 
							'{$_POST['currency']}', {$_POST['QTY']})");
			}
			$db->free_result($checkq);
			item_remove($userid, $r['inv_itemid'], $_POST['QTY']);
			$imadd_log = $db->escape("Listed {$r['itmname']} x{$_POST['QTY']} on the item market for {$_POST['price']} {$_POST['currency']}");
			$api->SystemLogsAdd($userid,'imarket',$imadd_log);
			alert('success',"Success!","You have successfully listed {$_POST['QTY']} {$r['itmname']}(s) on the item
			    market for {$_POST['price']} {$_POST['currency']}",true,'itemmarket.php');
		}
	}
	else
	{
		$r = $db->fetch_row($q);
		$db->free_result($q);
		$csrf=request_csrf_html("imadd_{$_GET['ID']}");
		echo "<form method='post' action='?action=add&ID={$_GET['ID']}'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					You are trying to add your {$api->SystemItemIDtoName($r['inv_itemid'])} to the Item Market.
				</th>
			</tr>
			<tr>
				<th>
					Quantity
				</th>
				<td>
					<input type='number' min='1' required='1' class='form-control' name='QTY' value='{$r['inv_qty']}'>
				</th>
			</tr>
			<tr>
				<th>
					Price per Item
				</th>
				<td>
					<input  type='number' min='1' required='1' class='form-control' name='price' value='{$r['itmbuyprice']}' />
				</td>
			</tr>
			<tr>
				<th>
					Currency Type
				</th>
				<td>
					<select name='currency' type='dropdown' class='form-control'>
						<option value='primary'>Primary Currency</option>
						<option value='secondary'>Secondary Currency</option>
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