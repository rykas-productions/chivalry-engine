<?php
/*
	File:		itemsell.php
	Created: 	4/5/2016 at 12:15AM Eastern Time
	Info: 		Allows players to instantly sell their item back
				to the game for a reduced price.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
$_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs(intval($_GET['ID'])) : '';
$_POST['qty'] = (isset($_POST['qty']) && is_numeric($_POST['qty'])) ? abs(intval($_POST['qty'])) : '';
if (permission('CanSellToGame', $userid) == true) {
    if (!empty($_POST['qty']) && !empty($_GET['ID'])) {
        $id =
            $db->query(
                "/*qc=on*/SELECT `inv_qty`, `itmsellprice`, `itmid`, `itmname`
						 FROM `inventory` AS `iv`
						 INNER JOIN `items` AS `it`
						 ON `iv`.`inv_itemid` = `it`.`itmid`
						 WHERE `iv`.`inv_id` = {$_GET['ID']}
						 AND `iv`.`inv_userid` = {$userid}
						 LIMIT 1");
        if ($db->num_rows($id) == 0) {
            alert('danger', "Uh Oh!", "You do not have this item to sell.", true, 'inventory.php');
        } else {
            $r = $db->fetch_row($id);
            if (!isset($_POST['verf']) || !verify_csrf_code("sellitem_{$_GET['ID']}", stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "We have blocked your action. Please fill out the form quickly.");
                die($h->endpage());
            }
            if ($_POST['qty'] > $r['inv_qty']) {
                alert('danger', "Uh Oh!", "You are trying to sell more items than you currently have.");
            } else {
                $price = $r['itmsellprice'] * $_POST['qty'];
                //Scammer skill
                $specialnumber = ((getUserSkill($userid, 15) * getSkillBonus(15)) / 100);
                $price = $price+($price*$specialnumber);
                $api->UserTakeItem($userid, $r['itmid'], $_POST['qty']);
                $api->UserGiveCurrency($userid, 'primary', $price);
                $priceh = shortNumberParse($price);
                alert('success', "Success!", "You have successfully sold " . shortNumberParse($_POST['qty']) . " {$r['itmname']}(s) back to the
				    game for {$priceh} Copper Coins.", true, 'inventory.php');
                $is_log = $db->escape("{$ir['username']} sold " . shortNumberParse($_POST['qty']) . " {$r['itmname']}(s) for {$priceh} Copper Coins.");
                $api->SystemLogsAdd($userid, 'itemsell', $is_log);
				addToEconomyLog('Item Sell to Game', 'copper', $price);
            }
        }
        $db->free_result($id);
    } else if (!empty($_GET['ID']) && empty($_POST['qty'])) {
        $id =
            $db->query(
                "/*qc=on*/SELECT `inv_qty`, `itmname`
						 FROM `inventory` AS `iv`
						 INNER JOIN `items` AS `it`
						 ON `iv`.`inv_itemid` = `it`.`itmid`
						 WHERE `iv`.`inv_id` = {$_GET['ID']}
						 AND `iv`.`inv_userid` = {$userid}
						 LIMIT 1");
        if ($db->num_rows($id) == 0) {
            alert('danger', "Uh Oh!", "You are trying to sell an invalid or non-existent item.", true, 'inventory.php');
        } else {
            $r = $db->fetch_row($id);
            $code = request_csrf_code("sellitem_{$_GET['ID']}");
            echo "<form action='?ID={$_GET['ID']}' method='post'>
            <div class='card'>
                <div class='card-header'>
                    Selling your {$r['itmname']}(s)
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12'>
                            You may sell your " . shortNumberParse($r['inv_qty']) . " {$r['itmname']}(s) back to the game for their sell price.
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Quantity</b></small>
                                </div>
                                <div class='col-12'>
                                    <input type='text' class='form-control' name='qty' value='{$r['inv_qty']}' max='{$r['inv_qty']}' />
                                </div>
                            </div>
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Confirm</b></small>
                                </div>
                                <div class='col-12'>
                                    <input type='submit' class='btn btn-success btn-block' value='Sell' />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type='hidden' name='verf' value='{$code}' />
            </form>";
        }
        $db->free_result($id);
    } else {
        alert('danger', "Uh Oh!", "Please select an item you wish to sell.", true, 'inventory.php');
    }
}
$h->endpage();