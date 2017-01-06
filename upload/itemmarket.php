<?php
require('globals.php');
echo "<h3>{$lang['IMARKET_TITLE']}</h3>";
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
	global $db,$lang,$h,$userid,$api;
	echo "
	<br />
	<table class='table table-responsive table-bordered table-hover table-striped'>
		<tr>
			<th>{$lang['IMARKET_LISTING_TH1']}</th>
			<th>{$lang['IMARKET_LISTING_TH2']}</th>
			<th>{$lang['IMARKET_LISTING_TH3']}</th>
			<th>{$lang['IMARKET_LISTING_TH4']}</th>
			<th>{$lang['IMARKET_LISTING_TH5']}</th>
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
					<th colspan='5' align='center'>{$lt}</th>
				</tr>";
        }
        $ctprice = ($r['imPRICE'] * $r['imQTY']);
        if ($r['imCURRENCY'] == 'primary')
        {
            $price = number_format($api->SystemReturnTax($r['imPRICE'])) . " {$lang['INDEX_PRIMCURR']}";
            $tprice = number_format($api->SystemReturnTax($ctprice)) . " {$lang['INDEX_PRIMCURR']}";
        }
        else
        {
            $price = number_format($api->SystemReturnTax($r['imPRICE'])) . " {$lang['INDEX_SECCURR']}";
            $tprice = number_format($api->SystemReturnTax($ctprice)) . " {$lang['INDEX_SECCURR']}";
        }
        if ($r['imADDER'] == $userid)
        {
            $link =
                    "[<a href='?action=remove&ID={$r['imID']}'>{$lang['IMARKET_LISTING_TD1']}</a>]";
        }
        else
        {
            $link =
                    "[<a href='?action=buy&ID={$r['imID']}'>{$lang['IMARKET_LISTING_TD2']}</a>]
                    [<a href='?action=gift&ID={$r['imID']}'>{$lang['IMARKET_LISTING_TD3']}</a>]";
        }
		echo "
		<tr>
			<td>
				<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
			</td>
			<td>
				<a href='iteminfo.php?ID={$r['itmid']}' data-toggle='tooltip' title='{$r['itmdesc']}'>{$r['itmname']}</a>";
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
	global $db,$ir,$userid,$h,$lang,$api;
	$_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs(intval($_GET['ID'])) : '';
	if (empty($_GET['ID']))
    {
        alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['IMARKET_REMOVE_ERROR1']}");
        die($h->endpage());
    }
	$q = $db->query("SELECT `imITEM`, `imQTY`, `imADDER`, `imID`, `itmname`
                    FROM `itemmarket` AS `im` INNER JOIN `items` AS `i`
                    ON `im`.`imITEM` = `i`.`itmid`  WHERE `im`.`imID` = {$_GET['ID']}
                    AND `im`.`imADDER` = {$userid}");
	if ($db->num_rows($q) == 0)
    {
        alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['IMARKET_REMOVE_ERROR2']}");
        die($h->endpage());
    }
	$r = $db->fetch_row($q);
    item_add($userid, $r['imITEM'], $r['imQTY']);
	$db->query("DELETE FROM `itemmarket` WHERE `imID` = {$_GET['ID']}");
	$imr_log = $db->escape("Removed {$r['itmname']} x {$r['imQTY']} from the item market.");
	$api->SystemLogsAdd($userid,'imarket',$imr_log);
	alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['IMARKET_REMOVE_SUCCESS']}");
}
function buy()
{
	global $db,$ir,$userid,$h,$lang,$api;
	$_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs(intval($_GET['ID'])) : '';
    $_POST['QTY'] = (isset($_POST['QTY']) && is_numeric($_POST['QTY'])) ? abs(intval($_POST['QTY'])) : '';
	if ($_GET['ID'] && !$_POST['QTY'])
    {
        $q = $db->query("SELECT `imADDER`, `imCURRENCY`, `imPRICE`, `imQTY`,
                         `imITEM`, `imID`, `itmname` FROM `itemmarket` AS `im`
                         INNER JOIN `items` AS `i` ON `i`.`itmid` = `im`.`imITEM`
                         WHERE `im`.`imID` = {$_GET['ID']}");
        if ($db->num_rows($q) == 0)
        {
            $db->free_result($q);
            alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['IMARKET_BUY_ERROR1']}");
            die($h->endpage());
        }
        $r = $db->fetch_row($q);
        $db->free_result($q);
		$csrf=request_csrf_html("imbuy_{$_GET['ID']}");
		echo "<form method='post' action='?action=buy&ID={$_GET['ID']}'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					{$lang['IMARKET_BUY_START']} {$r['itmname']}{$lang['IMARKET_BUY_START1']} {$r['imQTY']} {$lang['IMARKET_BUY_START2']}
				</th>
			</tr>
			<tr>
				<th>
					{$lang['SHOPS_SHOP_TD_1']}
				</th>
				<td>
					<input type='text' name='QTY' class='form-control' value='{$r['imQTY']}'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-default' value='{$lang['SHOPS_SHOP_TH_3']}'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>";
    }
	elseif (!$_GET['ID'])
    {
        alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['IMARKET_REMOVE_ERROR1']}");
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
				alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['IMARKET_BUY_ERROR1']}");
				die($h->endpage());
			}
			$r = $db->fetch_row($q);
			$db->free_result($q);
			if (!isset($_POST['verf']) || !verify_csrf_code("imbuy_{$_GET['ID']}", stripslashes($_POST['verf'])))
			{
				alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
				die($h->endpage());
			}
			if ($r['imADDER'] == $userid)
			{
				alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['IMARKET_BUY_SUB_ERROR1']}");
				die($h->endpage());
			}
			$curr = ($r['imCURRENCY'] == 'primary') ? 'primary_currency' : 'secondary_currency';
			$curre = ($r['imCURRENCY'] == 'primary') ? 'Primary Currency' : 'Secondary Currency';
			$final_price = $api->SystemReturnTax($r['imPRICE']*$_POST['QTY']);
			if ($final_price > $ir[$curr])
			{
				alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['IMARKET_BUY_SUB_ERROR2']}");
				die($h->endpage());
			}
			if ($_POST['QTY'] > $r['imQTY'])
			{
				alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['IMARKET_BUY_SUB_ERROR3']}");
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
			notification_add($r['imADDER'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> bought {$_POST['QTY']} {$r['itmname']}(s) from the market for " . number_format($final_price) . " {$curre}.");
			$imb_log = $db->escape("Bought {$r['itmname']} x{$_POST['QTY']} from the item market for " . number_format($final_price) . " {$curre} from user ID {$r['imADDER']}");
			alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['IMARKET_BUY_SUB_SUCCESS']}");
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
	global $db,$ir,$userid,$h,$lang,$api;
	$_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs(intval($_GET['ID'])) : '';
	$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs(intval($_POST['user'])) : '';
	$_POST['QTY'] = (isset($_POST['QTY']) && is_numeric($_POST['QTY'])) ? abs(intval($_POST['QTY'])) : '';
	$_POST['ID'] = (isset($_POST['ID']) && is_numeric($_POST['ID'])) ? abs(intval($_POST['ID'])) : '';
	if (!$_GET['ID'])
    {
        alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['IMARKET_REMOVE_ERROR1']}");
    }
	elseif (!empty($_POST['user']))
	{
		if ((empty($_POST['ID']) || empty($_POST['QTY'])))
		{
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['IMARKET_REMOVE_ERROR1']}");
			die($h->endpage());
		}
		if (!isset($_POST['verf']) || !verify_csrf_code("imgift_{$_GET['ID']}", stripslashes($_POST['verf'])))
		{
			alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
			die($h->endpage());
		}
		$query_user_exist = $db->query("SELECT COUNT(`userid`) FROM `users` WHERE `userid` = {$_POST['user']}");
		if ($db->fetch_single($query_user_exist) == 0)
		{
			$db->free_result($query_user_exist);
			alert('danger',"{$lang["ERROR_NONUSER"]}","{$lang['IMARKET_GIFT_SUB_ERROR1']}");
			die($h->endpage());
		}
		$db->free_result($query_user_exist);
		$q = $db->query("SELECT `imADDER`, `imCURRENCY`, `imPRICE`, `imQTY`,
						`imITEM`, `imID`, `itmname` FROM `itemmarket` AS `im`
						INNER JOIN `items` AS `i` ON `i`.`itmid` = `im`.`imITEM`
						WHERE `im`.`imID` = {$_POST['ID']}");
		if ($db->num_rows($q) == 0)
		{
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['IMARKET_REMOVE_ERROR2']}");
			die($h->endpage());
		}
		$r = $db->fetch_row($q);
		$db->free_result($q);
		if ($r['imADDER'] == $userid)
		{
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['IMARKET_BUY_SUB_ERROR1']}");
			die($h->endpage());
		}
		$curr = ($r['imCURRENCY'] == 'primary') ? 'primary_currency' : 'secondary_currency';
		$curre = ($r['imCURRENCY'] == 'primary') ? 'Primary Currency' : 'Secondary Currency';
		$final_price = $api->SystemReturnTax($r['imPRICE']*$_POST['QTY']);
		if ($final_price > $ir[$curr])
		{
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['IMARKET_BUY_SUB_ERROR2']}");
			die($h->endpage());
		}
		if ($_POST['QTY'] > $r['imQTY'])
		{
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['IMARKET_BUY_SUB_ERROR3']}");
			die($h->endpage());
		}
		if ($_POST['user'] == $r['imADDER'])
		{
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['IMARKET_GIFT_SUB_ERROR2']}");
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
		notification_add($_POST['user'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> bought you {$_POST['QTY']} {$r['itmname']}(s) from the market.");
		notification_add($r['imADDER'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> bought {$_POST['QTY']} {$r['itmname']}(s) from the market for " . number_format($final_price) . " {$curre}.");
		$imb_log = $db->escape("Bought {$r['itmname']} x{$_POST['QTY']} from the item market for " . number_format($final_price) . " {$curre} from User ID {$r['imADDER']} and gifted to User ID {$_POST['user']}");
		$api->SystemLogsAdd($userid,'imarket',$imb_log);
		alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['IMARKET_GIFT_SUB_SUCCESS']}");
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
            alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['IMARKET_BUY_ERROR1']}");
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
					{$lang['IMARKET_BUY_START']} {$r['itmname']}{$lang['IMARKET_GIFT_START1']} {$r['imQTY']} {$lang['IMARKET_BUY_START2']}
				</th>
			</tr>
			<tr>
				<th>
					{$lang['IMARKET_GIFT_FORM_TH1']}
				</th>
				<td>
					" . user_dropdown('user') . "
				</td>
			</tr>
			<tr>
				<th>
					{$lang['SHOPS_SHOP_TD_1']}
				</th>
				<td>
					<input type='text' name='QTY' class='form-control' value='{$r['imQTY']}'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-default' value='{$lang['SHOPS_SHOP_TH_3']}'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>";
	}
}
function add()
{
	global $lang,$h,$userid,$db,$h,$api;
	$_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs(intval($_GET['ID'])) : '';
	$_POST['price'] = (isset($_POST['price']) && is_numeric($_POST['price'])) ? abs(intval($_POST['price'])) : '';
	$_POST['QTY'] = (isset($_POST['QTY']) && is_numeric($_POST['QTY'])) ? abs(intval($_POST['QTY'])) : '';
	$_POST['currency'] = (isset($_POST['currency']) && in_array($_POST['currency'], array('primary', 'secondary'))) ? $_POST['currency'] : 'primary';
	if (empty($_GET['ID']))
	{
		alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['IMARKET_ADD_ERROR1']}");
		die($h->endpage());
	}
	if ($_POST['price'] && $_POST['QTY'] && $_GET['ID'])
	{
		if (!isset($_POST['verf']) || !verify_csrf_code("imgift_{$_GET['ID']}", stripslashes($_POST['verf'])))
		{
			alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
			die($h->endpage());
		}
		$q = $db->query("SELECT `inv_qty`, `inv_itemid`, `inv_id`, `itmname`
						FROM `inventory` AS `iv` INNER JOIN `items` AS `i`
						ON `iv`.`inv_itemid` = `i`.`itmid` WHERE `inv_id` = {$_GET['ID']}
						AND `inv_userid` = $userid");
		if ($db->num_rows($q) == 0)
		{
			$db->free_result($q);
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['IMARKET_ADD_ERROR2']}");
			die($h->endpage());
		}
		else
		{
			$r = $db->fetch_row($q);
			$db->free_result($q);
			if ($r['inv_qty'] < $_POST['QTY'])
			{
				alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['IMARKET_ADD_ERROR3']}");
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
			alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['IMARKET_ADD_SUB_SUCCESS']}");
		}
	}
	else
	{
		$csrf=request_csrf_html("imgift_{$_GET['ID']}");
		echo "<form method='post' action='?action=add&ID={$_GET['ID']}'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					{$lang['IMARKET_ADD_TITLE']}
				</th>
			</tr>
			<tr>
				<th>
					{$lang['SHOPS_SHOP_TD_1']}
				</th>
				<td>
					<input type='number' min='1' required='1' class='form-control' name='QTY' value=''>
				</th>
			</tr>
			<tr>
				<th>
					{$lang['IMARKET_ADD_TH2']}
				</th>
				<td>
					<input  type='number' min='1' required='1' class='form-control' name='price' value='0' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['IMARKET_ADD_TH1']}
				</th>
				<td>
					<select name='currency' type='dropdown' class='form-control'>
						<option value='primary'>{$lang['INDEX_PRIMCURR']}</option>
						<option value='secondary'>{$lang['INDEX_SECCURR']}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-default' value='{$lang['IMARKET_ADD_BTN']}'
				</td>
			</tr>
			{$csrf}
		</table>
		</form>";
	}
}
$h->endpage();