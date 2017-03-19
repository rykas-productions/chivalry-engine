<?php
require("globals.php");
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case 'shop':
    shop();
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
	global $lang,$db,$h,$ir;
	echo "{$lang['SHOPS_HOME_INTRO']}<br />";
	$q = $db->query("SELECT `shopID`, `shopNAME`, `shopDESCRIPTION` FROM `shops` WHERE `shopLOCATION` = {$ir['location']}");
	if ($db->num_rows($q) == 0)
	{
		echo $lang['SHOPS_HOME_OH'];
	}
	else
	{
		echo "<table class='table table-bordered'>
			<tr>
				<th>
					{$lang['SHOPS_HOME_TH_1']}
				</th>
				<th>
					{$lang['SHOPS_HOME_TH_2']}
				</th>
			</tr>";
			while ($r = $db->fetch_row($q))
			{
				echo "<tr>
						<td>
							<a href='?action=shop&shop={$r['shopID']}'>{$r['shopNAME']}</a>
						</td>
						<td>{$r['shopDESCRIPTION']}</td>
					  </tr>";
			}
		echo "</table>";
		$db->free_result($q);
	}
}
function shop()
{
	global $db,$ir,$lang,$h,$api;
	$_GET['shop'] = abs($_GET['shop']);
	$sd = $db->query("SELECT `shopLOCATION`, `shopNAME` FROM `shops` WHERE `shopID` = {$_GET['shop']}");
    if ($db->num_rows($sd) > 0)
    {
        $shopdata = $db->fetch_row($sd);
        if ($shopdata['shopLOCATION'] == $ir['location'])
        {
            echo "{$lang['SHOPS_SHOP_INFO']} <b>{$shopdata['shopNAME']}...</b><br />
			<table class='table table-bordered'>
				<tr>
					<th>{$lang['SHOPS_SHOP_TH_1']}</th>
					<th>{$lang['SHOPS_SHOP_TH_2']}</th>
					<th>{$lang['SHOPS_SHOP_TH_3']}</th>
				</tr>";
            $qtwo =
                    $db->query(
                            "SELECT `itmtypename`, `itmname`, `itmdesc`, `itmid`,
                             `itmbuyprice`, `itmsellprice`, `sitemID`
                             FROM `shopitems` AS `si`
                             INNER JOIN `items` AS `i`
                             ON `si`.`sitemITEMID` = `i`.`itmid`
                             INNER JOIN `itemtypes` AS `it`
                             ON `i`.`itmtype` = `it`.`itmtypeid`
                             WHERE `si`.`sitemSHOP` = {$_GET['shop']}
                             ORDER BY `itmtype` ASC, `itmbuyprice` ASC,
                             `itmname` ASC");
            $lt = "";
            while ($r = $db->fetch_row($qtwo))
            {
                if ($lt != $r['itmtypename'])
                {
                    $lt = $r['itmtypename'];
                    echo "<tr>
                    			<th colspan='5'>{$lt}</th>
                    		</tr>";
                }
                echo "<tr>
                			<td><a href='iteminfo.php?ID={$r['itmid']}' data-toggle='tooltip'"; ?> title="<?php echo $r['itmdesc']; ?>" <?php echo ">{$r['itmname']}</a></td>
                			<td>" . number_format($api->SystemReturnTax($r['itmbuyprice'])) . "</td>
                            <td>
                            	<form action='?action=buy&ID={$r['sitemID']}' method='post'>
                            		{$lang['SHOPS_SHOP_TD_1']} <input class='form-control' type='number' min='1' name='qty' value='1' />
                            		<input class='btn btn-default' type='submit' value='Buy' />
                            	</form>
                            </td>
                        </tr>";
            }
            $db->free_result($qtwo);
            echo "</table>";
        }
        else
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['SHOPS_SHOP_ERROR1'],true,"shops.php");
        }
    }
    else
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['SHOPS_SHOP_ERROR2'],true,"shops.php");
    }
    $db->free_result($sd);
}
function buy()
{
	global $db,$lang,$userid,$ir,$api,$h;
	$_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs(($_GET['ID'])) : '';
	$_POST['qty'] = (isset($_POST['qty']) && is_numeric($_POST['qty'])) ? abs(($_POST['qty'])) : '';
	if (permission('CanBuyFromGame',$userid) == true)
	{
		if (empty($_GET['ID']) OR empty($_POST['qty']))
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['SHOPS_BUY_ERROR1'],true,"shops.php");
		}
		else
		{
			$q = $db->query("SELECT `itmid`, `itmbuyprice`, `itmname`, `itmbuyable`, `shopLOCATION`
							FROM `shopitems` AS `si`
							INNER JOIN `shops` AS `s`
							ON `si`.`sitemSHOP` = `s`.`shopID`
							INNER JOIN `items` AS `i`
							ON `si`.`sitemITEMID` = `i`.`itmid`
							WHERE `sitemID` = {$_GET['ID']}");
			if ($db->num_rows($q) == 0)
			{
				alert('danger',$lang['ERROR_GENERIC'],$lang['SHOPS_BUY_ERROR2']},true,"shops.php");
			}
			else
			{
				$itemd = $db->fetch_row($q);
				if ($ir['primary_currency'] < ($api->SystemReturnTax($itemd['itmbuyprice']) * $_POST['qty']))
				{
					alert('danger',$lang['ERROR_GENERIC'],"{$lang['SHOPS_BUY_ERROR3']} {$_POST['qty']} {$itemd['itmname']}(s).",true,"shops.php");
					die($h->endpage());
				}
				if ($itemd['itmbuyable'] == 'false')
				{
					alert('danger',$lang['ERROR_GENERIC'],$lang['SHOPS_BUY_ERROR4'],true,"shops.php");
					die($h->endpage());
				}
				if ($itemd['shopLOCATION'] != $ir['location'])
				{
					alert('danger',$lang['ERROR_GENERIC'],$lang['SHOPS_BUY_ERROR5'],true,"shops.php");
					die($h->endpage());
				}

				$price = ($api->SystemReturnTax($itemd['itmbuyprice']) * $_POST['qty']);
				item_add($userid, $itemd['itmid'], $_POST['qty']);
				$db->query(
						"UPDATE `users`
						 SET `primary_currency` = `primary_currency` - $price
						 WHERE `userid` = $userid");
				$ib_log = $db->escape("{$ir['username']} bought {$_POST['qty']} {$itemd['itmname']}(s) for {$price}");
				alert('success',$lang['ERROR_SUCCESS'],"{$lang['SHOPS_BUY_SUCCESS']} {$_POST['qty']} {$itemd['itmname']}(s) {$lang['GEN_FOR']} {$price}.",true,"shops.php");
				$api->SystemLogsAdd($userid,'itembuy',$ib_log);
				$api->SystemCreditTax($api->SystemReturnTaxOnly($itemd['itmbuyprice']),1,-1);
			}
		$db->free_result($q);
		}
	}
}
$h->endpage();