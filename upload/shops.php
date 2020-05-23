<?php
/*
	File:		shops.php
	Created: 	4/5/2016 at 12:25AM Eastern Time
	Info: 		Allows players to visit shops, and buy items from the
				shop's inventory.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
	alert('danger',"Uh Oh!","You cannot visit the shops while in the infirmary or dungeon.",true,'index.php');
	die($h->endpage());
}
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
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
    global $db, $ir;
    echo "You begin looking through town to see the shops that interest you. You find a small handful.<br />";
    $q = $db->query("/*qc=on*/SELECT `shopID`, `shopNAME`, `shopDESCRIPTION` FROM `shops` WHERE `shopLOCATION` = {$ir['location']}");
    if ($db->num_rows($q) == 0) {
        echo "This town doesn't have any shops, funny enough.";
    } else {
        echo "<table class='table table-bordered'>
			<tr>
				<th>
					Shop's Name
				</th>
				<th>
					Shop's Description
				</th>
			</tr>";
        while ($r = $db->fetch_row($q)) {
            echo "<tr>
						<td>
							<a href='?action=shop&shop={$r['shopID']}'>{$r['shopNAME']}</a>
						</td>
						<td>{$r['shopDESCRIPTION']}</td>
					  </tr>";
        }
        echo "</table>
		<img src='https://res.cloudinary.com/dydidizue/image/upload/v1520819462/shop.jpg' class='img-thumbnail img-responsive'>";
        $db->free_result($q);
    }
}

function shop()
{
    global $db, $ir, $api, $userid;
    $_GET['shop'] = abs($_GET['shop']);
    $sd = $db->query("/*qc=on*/SELECT `shopLOCATION`, `shopNAME` FROM `shops` WHERE `shopID` = {$_GET['shop']}");
    if ($db->num_rows($sd) > 0) {
        $shopdata = $db->fetch_row($sd);
        if ($shopdata['shopLOCATION'] == $ir['location']) {
			$specialnumber=((getSkillLevel($userid,13)*5)/100);
            echo "You begin browsing the stock at {$shopdata['shopNAME']}<br />
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>Item</th>
					<th>Price</th>
					<th width='25%'>Buy</th>
				</tr>";
            $qtwo =
                $db->query(
                    "/*qc=on*/SELECT `itmtypename`, `itmname`, `itmdesc`, `itmid`,
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
            while ($r = $db->fetch_row($qtwo)) {
				$r['itmbuyprice']=$r['itmbuyprice']-($r['itmbuyprice']*$specialnumber);
                if ($lt != $r['itmtypename']) {
                    $lt = $r['itmtypename'];
                    echo "<tr>
                    			<td colspan='4'><b>{$lt}</b></td>
                    		</tr>";
                }
				$icon = returnIcon($r['itmid'],3);
                echo "<tr>
                            <td>
                            {$icon}
                            </td>
                			<td><a href='iteminfo.php?ID={$r['itmid']}' data-toggle='tooltip'"; ?> title="<?php echo $r['itmdesc']; ?>" <?php echo ">{$r['itmname']}</a></td>
                			<td>" . number_format($api->SystemReturnTax($r['itmbuyprice'])) . "</td>
                            <td>
                            	<form action='?action=buy&ID={$r['sitemID']}' method='post'>
                            		Quantity <input class='form-control' type='number' min='1' name='qty' value='1' />
                            		<input class='btn btn-primary' type='submit' value='Buy' />
                            	</form>
                            </td>
                        </tr>";
            }
            $db->free_result($qtwo);
            echo "</table>";
        } else {
            alert('danger', "Uh Oh!", "You are not in the same location as this shop, and thus, cannot view its stock.", true, "shops.php");
        }
    } else {
        alert('danger', "Uh Oh!", "This shop does not exist.", true, "shops.php");
    }
    $db->free_result($sd);
}

function buy()
{
    global $db, $userid, $ir, $api, $h;
    $_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs(($_GET['ID'])) : '';
    $_POST['qty'] = (isset($_POST['qty']) && is_numeric($_POST['qty'])) ? abs(($_POST['qty'])) : '';
    if (permission('CanBuyFromGame', $userid) == true) {
        if (empty($_GET['ID']) OR empty($_POST['qty'])) {
            alert('danger', "Uh Oh!", "Please fill out the form completely before submitting it.", true, "shops.php");
        } else {
            $q = $db->query("/*qc=on*/SELECT `itmid`, `itmbuyprice`, `itmname`, `itmbuyable`, `shopLOCATION`
							FROM `shopitems` AS `si`
							INNER JOIN `shops` AS `s`
							ON `si`.`sitemSHOP` = `s`.`shopID`
							INNER JOIN `items` AS `i`
							ON `si`.`sitemITEMID` = `i`.`itmid`
							WHERE `sitemID` = {$_GET['ID']}");
            if ($db->num_rows($q) == 0) {
                alert('danger', "Uh Oh!", "You are trying to buy from a non-existent shop.", true, "shops.php");
            } else {
                $itemd = $db->fetch_row($q);
				$specialnumber=((getSkillLevel($userid,13)*5)/100);
				$itemd['itmbuyprice']=$itemd['itmbuyprice']-($itemd['itmbuyprice']*$specialnumber);
                if ($ir['primary_currency'] < ($api->SystemReturnTax($itemd['itmbuyprice']) * $_POST['qty'])) {
                    alert('danger', "Uh Oh!", "You do not have enough Copper Coins to buy {$_POST['qty']} {$itemd['itmname']}(s).", true, "shops.php");
                    die($h->endpage());
                }
                if ($itemd['itmbuyable'] == 'false') {
                    alert('danger', "Uh Oh!", "You cannot buy {$itemd['itmname']}s this way.", true, "shops.php");
                    die($h->endpage());
                }
                if ($itemd['shopLOCATION'] != $ir['location']) {
                    alert('danger', "Uh Oh!", "You are not in the same town as this shop and cannot buy from it.", true, "shops.php");
                    die($h->endpage());
                }

                $price = ($api->SystemReturnTax($itemd['itmbuyprice']) * $_POST['qty']);
				addToEconomyLog('Game Shops', 'copper', (($itemd['itmbuyprice'] * $_POST['qty'])*-1));
                item_add($userid, $itemd['itmid'], $_POST['qty']);
                $db->query(
                    "UPDATE `users`
						 SET `primary_currency` = `primary_currency` - $price
						 WHERE `userid` = $userid");
                $ib_log = $db->escape("{$ir['username']} bought {$_POST['qty']} {$itemd['itmname']}(s) for {$price}");
                alert('success', "Success!", "You have bought {$_POST['qty']} {$itemd['itmname']}(s) for {$price} Copper Coins.", true, "shops.php");
                $api->SystemLogsAdd($userid, 'itembuy', $ib_log);
                $api->SystemCreditTax($api->SystemReturnTaxOnly($itemd['itmbuyprice']* $_POST['qty']), 1, -1);
            }
            $db->free_result($q);
        }
    }
}

$h->endpage();