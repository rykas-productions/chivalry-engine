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
echo "<h3><i class='game-icon game-icon-trade'></i> Item Market</h3><hr />";
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
    global $db, $userid, $api;
    echo "
	<form>
		<div class='row'>
			<div class='col-12 col-sm-2 col-md-1'>
				Search
			</div>
			<div class='col'>
				" . itemmarket_dropdown('item') . "
			</div>
			<div class='col-sm-3 col'>
				<input type='submit' value='Search' class='btn btn-primary btn-block'>
			</div>
		</div>
	</form>
	<hr />
	[<a href='?action=add'>Add Your Own Listing</a>]
   ";
   if (isset($_GET['item']))
   {
	   $_GET['item'] = (isset($_GET['item']) && is_numeric($_GET['item'])) ? abs($_GET['item']) : '';
	   if ($_GET['item'] > 0)
	   {
	   $q =
        $db->query(
            "/*qc=on*/SELECT `imPRICE`, `imQTY`, `imCURRENCY`, `imADDER`,
                     `imID`, `i`.*, `userid`,`username`, `itmdesc`,
                     `itmtypename`
                     FROM `itemmarket` AS `im`
                     INNER JOIN `items` AS `i`
                     ON `im`.`imITEM` = `i`.`itmid`
                     INNER JOIN `users` AS `u`
                     ON `u`.`userid` = `im`.`imADDER`
                     INNER JOIN `itemtypes` AS `it`
                     ON `i`.`itmtype` = `it`.`itmtypeid`
					 WHERE `imITEM` = {$_GET['item']}
                     ORDER BY `i`.`itmtype`, `i`.`itmname`, `u`.`username` ASC");
	   }
	   else
	   {
		   $q =
			$db->query(
            "/*qc=on*/SELECT `imPRICE`, `imQTY`, `imCURRENCY`, `imADDER`,
                     `imID`, `i`.*, `userid`,`username`, `itmdesc`,
                     `itmtypename`
                     FROM `itemmarket` AS `im`
                     INNER JOIN `items` AS `i`
                     ON `im`.`imITEM` = `i`.`itmid`
                     INNER JOIN `users` AS `u`
                     ON `u`.`userid` = `im`.`imADDER`
                     INNER JOIN `itemtypes` AS `it`
                     ON `i`.`itmtype` = `it`.`itmtypeid`
                     ORDER BY `i`.`itmtype`, `i`.`itmname`, `u`.`username` ASC");
	   }
   }
   else
   {
		$q =
        $db->query(
            "/*qc=on*/SELECT `imPRICE`, `imQTY`, `imCURRENCY`, `imADDER`,
                     `imID`, `i`.*, `userid`,`username`, `itmdesc`,
                     `itmtypename`
                     FROM `itemmarket` AS `im`
                     INNER JOIN `items` AS `i`
                     ON `im`.`imITEM` = `i`.`itmid`
                     INNER JOIN `users` AS `u`
                     ON `u`.`userid` = `im`.`imADDER`
                     INNER JOIN `itemtypes` AS `it`
                     ON `i`.`itmtype` = `it`.`itmtypeid`
                     ORDER BY `i`.`itmtype`, `i`.`itmname`, `u`.`username` ASC");
   }
    $cblah = 1;
    $lt = "";
	echo "<div class='accordion' id='inventoryAccordian'>";
    while ($r = $db->fetch_row($q)) 
	{
        if ($lt != $r['itmtypename']) 
		{
            $lt = $r['itmtypename'];
            echo "<div class='card'>
					<div class='card-body' id='heading{$r['itmid']}'>
						<h4 class='mb-0'>
							{$lt}
						</h4>
					</div>
				</div>";
        }
        $ctprice = ($r['imPRICE'] * $r['imQTY']);
        if ($r['imCURRENCY'] == 'primary') {
            $price = shortNumberParse($r['imPRICE']) . " " . loadImageAsset("menu/coin-copper.svg");
            $tprice = shortNumberParse($ctprice) . " " . loadImageAsset("menu/coin-copper.svg");
        } else {
            $price = shortNumberParse($r['imPRICE']) . " " . loadImageAsset("menu/coin-chivalry.svg");
            $tprice = shortNumberParse($ctprice) . " " . loadImageAsset("menu/coin-chivalry.svg");
        }
        if ($r['imADDER'] == $userid) {
            $link =
                "<div class='col'>
					<a class='btn btn-primary btn-block' href='?action=remove&ID={$r['imID']}'><i class='far fa-trash-alt'></i></a>
				</div>";
        } else {
            $link =
                "<div class='col'>
					<a class='btn btn-primary btn-block' href='?action=buy&ID={$r['imID']}'><i class='fas fa-dollar-sign'></i></a>
                </div>
				<div class='col'>
					<a class='btn btn-primary btn-block' href='?action=gift&ID={$r['imID']}'><i class='fas fa-gift'></i></a>
				</div>";
        }
				$r['itmdesc'] = htmlentities($r['itmdesc'], ENT_QUOTES);
				$rcon = returnIcon($r['itmid'],2);
				$r['username'] = parseUsername($r['userid']);
				$displaypic = "<img src='" . parseDisplayPic($r['userid']) . "' height='75' alt='Display picture.' title='Display picture'>";
				$armory=$db->fetch_single($db->query("/*qc=on*/SELECT SUM(`gaQTY`) FROM `guild_armory` WHERE `gaITEM` = {$r['itmid']} AND `gaGUILD` != 1"));
				$rnvent=$db->fetch_single($db->query("/*qc=on*/SELECT SUM(`inv_qty`) FROM `inventory` WHERE `inv_itemid` = {$r['itmid']} AND `inv_userid` != 1"));
				$market=$db->fetch_single($db->query("/*qc=on*/SELECT SUM(`imQTY`) FROM `itemmarket` WHERE `imITEM` = {$r['itmid']}"));
				$primary=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`equip_primary`) FROM `users` WHERE `equip_primary` = {$r['itmid']} AND `userid` != 1"));
				$secondary=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`equip_secondary`) FROM `users` WHERE `equip_secondary` = {$r['itmid']} AND `userid` != 1"));
				$armor=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`equip_armor`) FROM `users` WHERE `equip_armor` = {$r['itmid']} AND `userid` != 1"));
				$badge=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`equip_badge`) FROM `users` WHERE `equip_badge` = {$r['itmid']} AND `userid` != 1"));
				$trink=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`equip_slot`) FROM `user_equips` WHERE `itemid` = {$r['itmid']}"));
				$total=$rnvent+$armory+$market+$primary+$secondary+$armor+$badge+$trink;
				echo "
				<div class='card'>
					<div class='card-header' id='heading{$r['imID']}'>
						<h2 class='mb-0'>
							<button class='btn btn-block btn-block text-left' type='button' data-toggle='collapse' data-target='#collapse{$r['imID']}' aria-expanded='true' aria-controls='collapse{$r['imID']}'>
								<div class='row'>
									<div class='col'>
										{$rcon}
									</div>
									<div class='col-10 col-sm-9 col-md-3'>";
										if ($r['imQTY'] > 1)
										{
											echo shortNumberParse($r['imQTY']) . " x ";
										}
										echo "{$r['itmname']}
									</div>
									<div class='col-12 col-md-3'>
										<div class='row'>
											<div class='col-12'>
												<b>Price</b>
											</div>
											<div class='col-6'>
												<small>{$price}</small>
											</div>
											<div class='col-6'>
												<small>{$tprice}</small>
											</div>
										</div>
									</div>
									<div class='col-12 col-md-3'>
										<div class='row'>
											<div class='col-4 hidden-md-down'>
												{$displaypic}
											</div>
											<div class='col'>
												<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
											</div>
										</div>
									</div>
									<div class='col'>
										<div class='row'>
											{$link}
										</div>
									</div>
								</div>
							</button>
						</h2>
					</div>
					<div id='collapse{$r['imID']}' class='collapse' aria-labelledby='heading{$r['itmid']}' data-parent='#inventoryAccordian'>
					<div class='card-body'>
						<div class='row'>
							<div class='col-md-1'>
								" . returnIcon($r['itmid'],3.5) . "
							</div>
							<div class='col-md-8 text-left'>
								<b><a href='iteminfo.php?ID={$r['itmid']}'>{$r['itmname']}</a></b> is a {$lt} item.<br />
								<i>{$r['itmdesc']}</i>";
								$start=0;
								for ($enum = 1; $enum <= 3; $enum++) 
								{
									if ($r["effect{$enum}_on"] == 'true') 
									{
										if ($start == 0)
										{
											echo "<br /><b>Effect</b> ";
											$start = 1;
										}
										$einfo = unserialize($r["effect{$enum}"]);
										$einfo['inc_type'] = ($einfo['inc_type'] == 'percent') ? '%' : '';
										$einfo['dir'] = ($einfo['dir'] == 'pos') ? '+' : '-';
										$stats =
											array("energy" => "Energy", "will" => "Will",
												"brave" => "Bravery", "level" => "Level",
												"hp" => "Health", "strength" => "Strength",
												"agility" => "Agility", "guard" => "Guard",
												"labor" => "Labor", "iq" => "IQ",
												"infirmary" => "Infirmary minutes", "dungeon" => "Dungeon minutes",
											    "primary_currency" => loadImageAsset("menu/coin-copper.svg"), "secondary_currency"
											    => loadImageAsset("menu/coin-chivalry.svg"), "crimexp" => "Experience", "vip_days" =>
												"VIP Days", "luck" => "Luck", "premium_currency" => "Mutton");
										$statformatted = $stats["{$einfo['stat']}"];
										echo "{$einfo['dir']}" . number_format($einfo['inc_amount']) . "{$einfo['inc_type']} {$statformatted}.";
									}
								}
					echo "</div>";
					echo"	
					</div>
					</div>
				</div>
			</div>";
            }
            $db->free_result($q);
            echo "</div>";
}

function remove()
{
    global $db, $userid, $h, $api;
    $_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs($_GET['ID']) : '';
    if (empty($_GET['ID'])) {
        alert('danger', "Uh Oh!", "Please specify the offer you wish to remove.", true, 'itemmarket.php');
        die($h->endpage());
    }
    $q = $db->query("/*qc=on*/SELECT `imITEM`, `imQTY`, `imADDER`, `imID`, `itmname`
                    FROM `itemmarket` AS `im` INNER JOIN `items` AS `i`
                    ON `im`.`imITEM` = `i`.`itmid`  WHERE `im`.`imID` = {$_GET['ID']}
                    AND `im`.`imADDER` = {$userid}");
    if ($db->num_rows($q) == 0) {
        alert('danger', "Uh Oh!", "You are trying to remove a non-existent offer, or an offer that does not belong to you."
            , true, 'itemmarket.php');
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
    item_add($userid, $r['imITEM'], $r['imQTY']);
    $db->query("DELETE FROM `itemmarket` WHERE `imID` = {$_GET['ID']}");
    $imr_log = $db->escape("Removed {$r['itmname']} x {$r['imQTY']} from the item market.");
    $api->SystemLogsAdd($userid, 'imarket', $imr_log);
    alert('success', "Success!", "You have removed your offer successfully. Your item(s) have returned to your inventory."
        , true, 'itemmarket.php');
}

function buy()
{
    global $db, $ir, $userid, $h, $api;
    $_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs($_GET['ID']) : '';
    $_POST['QTY'] = (isset($_POST['QTY']) && is_numeric($_POST['QTY'])) ? abs($_POST['QTY']) : '';
    if ($_GET['ID'] && !$_POST['QTY']) {
        $q = $db->query("/*qc=on*/SELECT `imADDER`, `imCURRENCY`, `imPRICE`, `imQTY`,
                         `imITEM`, `imID`, `itmname` FROM `itemmarket` AS `im`
                         INNER JOIN `items` AS `i` ON `i`.`itmid` = `im`.`imITEM`
                         WHERE `im`.`imID` = {$_GET['ID']}");
        if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "You are trying to buy an non-existent offer.", true, 'itemmarket.php');
            die($h->endpage());
        }
        $r = $db->fetch_row($q);
        $db->free_result($q);
        $csrf = request_csrf_html("imbuy_{$_GET['ID']}");
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
    } elseif (!$_GET['ID']) {
        alert('danger', "Uh Oh!", "Please specify an offer you wish to buy.", true, 'itemmarket.php');
    } else {
        $q = $db->query("/*qc=on*/SELECT `imADDER`, `imCURRENCY`, `imPRICE`, `imQTY`, `imDEPOSIT`, 
						`imITEM`, `imID`, `itmname` FROM `itemmarket` AS `im`
						INNER JOIN `items` AS `i` ON `i`.`itmid` = `im`.`imITEM`
						WHERE `im`.`imID` = {$_GET['ID']}");
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
        if ($r['imADDER'] == $userid) {
            alert('danger', "Uh Oh!", "You cannot buy your own offer, silly.", true, 'itemmarket.php');
            die($h->endpage());
        }
        if ($api->SystemCheckUsersIPs($userid, $r['imADDER'])) {
            alert('danger', "Uh Oh!", "You cannot buy an offer from someone who shares your IP Address.", true, 'itemmarket.php');
            die($h->endpage());
        }
        $curr = ($r['imCURRENCY'] == 'primary') ? 'primary_currency' : 'secondary_currency';
        $curre = ($r['imCURRENCY'] == 'primary') ? loadImageAsset("menu/coin-copper.svg") : loadImageAsset("menu/coin-chivalry.svg");
        $final_price = $r['imPRICE'] * $_POST['QTY'];
		$remove = 0.02;
		if ($r['imDEPOSIT'] == 'true')
			$remove = $remove + 0.05;
		if ($curr == 'primary_currency')
			addToEconomyLog('Market Fees', 'copper', ($final_price*$remove)*-1);
		else
			addToEconomyLog('Market Fees', 'token', ($final_price*$remove)*-1);
		$taxed=$final_price-($final_price*$remove);
        if ($final_price > $ir[$curr]) {
            alert('danger', "Uh Oh!", "You do not have enough {$curre} to buy this offer.");
            die($h->endpage());
        }
        if ($_POST['QTY'] > $r['imQTY']) {
            alert('danger', "Uh Oh!", "You are trying to buy more than there's currently available in this listing.");
            die($h->endpage());
        }
        item_add($userid, $r['imITEM'], $_POST['QTY']);
        if ($_POST['QTY'] == $r['imQTY']) {
            $db->query("DELETE FROM `itemmarket` WHERE `imID` = {$_GET['ID']}");
        } elseif ($_POST['QTY'] < $r['imQTY']) {
            $db->query("UPDATE `itemmarket` SET `imQTY` = `imQTY` - {$_POST['QTY']} WHERE `imID` = {$_GET['ID']}");
        }
		if ($curr == 'primary_currency')
			addToEconomyLog('Market Fees', 'copper', ($final_price*0.02)*-1);
		else
			addToEconomyLog('Market Fees', 'token', ($final_price*0.02)*-1);
        $db->query("UPDATE `users` SET `$curr` = `$curr` - {$final_price} WHERE `userid` = $userid");
        $db->query("UPDATE `users` SET `$curr` = `$curr` + {$taxed} WHERE `userid` = {$r['imADDER']}");
        notification_add($r['imADDER'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> bought
                " . number_format($_POST['QTY']) . " x {$r['itmname']}(s) from the market for " . shortNumberParse($taxed) . " {$curre}.");
        $imb_log = $db->escape("Bought {$r['itmname']} x " . number_format($_POST['QTY']) . " from the item market for
			    " . number_format($final_price) . " {$curre} from User ID {$r['imADDER']}");
        alert('success', "Success!", "You have successfully bought {$r['itmname']} x " . number_format($_POST['QTY']) . " from the item
			    market for " . number_format($final_price) . " {$curre}", true, 'itemmarket.php');
        $api->SystemLogsAdd($userid, 'imarket', $imb_log);
    }
}

function gift()
{
    global $db, $ir, $userid, $h, $api;
    $_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs($_GET['ID']) : '';
    $_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : '';
    $_POST['QTY'] = (isset($_POST['QTY']) && is_numeric($_POST['QTY'])) ? abs($_POST['QTY']) : '';
    $_POST['ID'] = (isset($_POST['ID']) && is_numeric($_POST['ID'])) ? abs($_POST['ID']) : '';
    if (!$_GET['ID']) {
        alert('danger', "Uh Oh!", "Please specify a listing you wish to gift.", true, 'itemmarket.php');
    } elseif (!empty($_POST['user'])) {
        if ((empty($_POST['ID']) || empty($_POST['QTY']))) {
            alert('danger', "Uh Oh!", "Please fill out the previous form completely before submitting it.");
            die($h->endpage());
        }
        if (!isset($_POST['verf']) || !verify_csrf_code("imgift_{$_GET['ID']}", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Form requests expire fairly quickly. Go back and fill in the form faster next time.");
            die($h->endpage());
        }
        $query_user_exist = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `userid` = {$_POST['user']}");
        if ($db->fetch_single($query_user_exist) == 0) {
            $db->free_result($query_user_exist);
            alert('danger', "Uh Oh!", "You are trying to gift this listing to a non-existent user.");
            die($h->endpage());
        }
        $db->free_result($query_user_exist);
        $q = $db->query("/*qc=on*/SELECT `imADDER`, `imCURRENCY`, `imPRICE`, `imQTY`, `imDEPOSIT`, 
						`imITEM`, `imID`, `itmname` FROM `itemmarket` AS `im`
						INNER JOIN `items` AS `i` ON `i`.`itmid` = `im`.`imITEM`
						WHERE `im`.`imID` = {$_POST['ID']}");
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
        if ($api->SystemCheckUsersIPs($userid, $r['imADDER'])) {
            alert('danger', "Uh Oh!", "You cannot buy an offer from someone who shares your IP Address.", true, 'itemmarket.php');
            die($h->endpage());
        }
        if ($api->SystemCheckUsersIPs($userid, $_POST['user'])) {
            alert('danger', "Uh Oh!", "You cannot gift an offer to someone who shares your IP Address.", true, 'itemmarket.php');
            die($h->endpage());
        }
        $curr = ($r['imCURRENCY'] == 'primary') ? 'primary_currency' : 'secondary_currency';
        $curre = ($r['imCURRENCY'] == 'primary') ? loadImageAsset("menu/coin-copper.svg") : loadImageAsset("menu/coin-chivalry.svg");
        $final_price = $r['imPRICE'] * $_POST['QTY'];
        if ($final_price > $ir[$curr]) {
            alert('danger', "Uh Oh!", "You do not have enough {$curre} to buy this offer.");
            die($h->endpage());
        }
        if ($_POST['QTY'] > $r['imQTY']) {
            alert('danger', "Uh Oh!", "You are trying to buy more than there's currently available in this listing.");
            die($h->endpage());
        }
        if ($_POST['user'] == $r['imADDER']) {
            alert('danger', "Uh Oh!", "You cannot gift this listing to the listing owner.");
            die($h->endpage());
        }
        item_add($_POST['user'], $r['imITEM'], $_POST['QTY']);
        if ($_POST['QTY'] == $r['imQTY']) {
            $db->query(
                "DELETE FROM `itemmarket`
					 WHERE `imID` = {$_POST['ID']}");
        } elseif ($_POST['QTY'] < $r['imQTY']) {
            $db->query("UPDATE `itemmarket` SET `imQTY` = `imQTY` - {$_POST['QTY']} WHERE `imID` = {$_POST['ID']}");
        }
		$remove = 0.02;
		if ($r['imDEPOSIT'] == 'true')
			$remove = $remove + 0.05;
		if ($curr == 'primary_currency')
			addToEconomyLog('Market Fees', 'copper', ($final_price*$remove)*-1);
		else
			addToEconomyLog('Market Fees', 'token', ($final_price*$remove)*-1);
		$taxed=$final_price-($final_price*$remove);
        $db->query("UPDATE `users` SET `{$curr}` = `{$curr}` - {$final_price} WHERE `userid`= {$userid}");
        $db->query("UPDATE `users` SET `{$curr}` = `{$curr}` + {$taxed} WHERE `userid` = {$r['imADDER']}");
        notification_add($_POST['user'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> bought you
            {$_POST['QTY']} {$r['itmname']}(s) from the market.");
        notification_add($r['imADDER'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> bought
            {$_POST['QTY']} {$r['itmname']}(s) from the market for " . number_format($taxed) . " {$curre}.");
        $imb_log = $db->escape("Bought {$r['itmname']} x " . number_format($_POST['QTY']) . " from the item market for
		    " . number_format($final_price) . " {$curre} from User ID {$r['imADDER']} and gifted to User ID {$_POST['user']}");
        $api->SystemLogsAdd($userid, 'imarket', $imb_log);
        alert('success', "Success!", "You have bought {$r['itmname']} x " . number_format($_POST['QTY']) . " from the item market for
		    " . number_format($final_price) . " {$curre} from User ID {$r['imADDER']} and gifted to User ID
		    {$_POST['user']}", true, 'index.php');

    } else {
        $q = $db->query("/*qc=on*/SELECT `imADDER`, `imCURRENCY`, `imPRICE`, `imQTY`,
                         `imITEM`, `imID`, `itmname` FROM `itemmarket` AS `im`
                         INNER JOIN `items` AS `i` ON `i`.`itmid` = `im`.`imITEM`
                         WHERE `im`.`imID` = {$_GET['ID']}");
        if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "Please specify an offer you wish to manipulate.", true, 'itemmarket.php');
            die($h->endpage());
        }
        $r = $db->fetch_row($q);
        $db->free_result($q);
        $csrf = request_csrf_html("imgift_{$_GET['ID']}");
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
    global $userid, $db, $h, $api;
    $_POST['ID'] = (isset($_POST['ID']) && is_numeric($_POST['ID'])) ? abs($_POST['ID']) : '';
    $_POST['price'] = (isset($_POST['price']) && is_numeric($_POST['price'])) ? abs($_POST['price']) : '';
    $_POST['QTY'] = (isset($_POST['QTY']) && is_numeric($_POST['QTY'])) ? abs($_POST['QTY']) : '';
	$_POST['currency'] = (isset($_POST['currency']) && in_array($_POST['currency'], array('primary', 'secondary'))) ? $_POST['currency'] : 'primary';
	$_POST['deposit'] = (isset($_POST['deposit']) && in_array($_POST['deposit'], array('false', 'true'))) ? $_POST['deposit'] : 'false';
    if ($_POST['price'] && $_POST['QTY'] && $_POST['ID']) {
        if (!isset($_POST['verf']) || !verify_csrf_code("imadd_form", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Form requests expire fairly quickly. Go back and fill in the form faster next time.");
            die($h->endpage());
        }
        $haveitem=$api->UserHasItem($userid,$_POST['ID'],$_POST['QTY']);
        if (!$haveitem) {
            alert('danger', "Uh Oh!", "You are trying to add an item you do not have, or trying to add more than you have.", true, 'inventory.php');
            die($h->endpage());
        } else {
            $checkq = $db->query("/*qc=on*/SELECT `imID` FROM `itemmarket` WHERE  `imITEM` =
								{$_POST['ID']} AND  `imPRICE` = {$_POST['price']}
								AND  `imADDER` = {$userid} AND `imCURRENCY` = '{$_POST['currency']}'");
            if ($db->num_rows($checkq) > 0) {
                $cqty = $db->fetch_row($checkq);
                $db->query("UPDATE `itemmarket` SET `imQTY` = `imQTY` + {$_POST['QTY']} WHERE `imID` = {$cqty['imID']} AND `imCURRENCY` = '{$_POST['currency']}'");
            } else {
                $db->query("INSERT INTO `itemmarket` VALUES  (NULL,
							'{$_POST['ID']}', {$userid}, {$_POST['price']},
							'{$_POST['currency']}', {$_POST['QTY']}, '{$_POST['deposit']}')");
            }
            $db->free_result($checkq);
            item_remove($userid, $_POST['ID'], $_POST['QTY']);
            $itemname=$api->SystemItemIDtoName($_POST['ID']);
            $curre = ($_POST['currency'] == 'primary') ? loadImageAsset("menu/coin-copper.svg") : loadImageAsset("menu/coin-chivalry.svg");
			$num_format=shortNumberParse($_POST['price']);
            $imadd_log = $db->escape("Listed " . shortNumberParse($_POST['QTY']) . " {$itemname}(s) on the item market for {$num_format} {$curre}.");
            $api->SystemLogsAdd($userid, 'imarket', $imadd_log);
            alert('success', "Success!", "You have successfully listed " . shortNumberParse($_POST['QTY']) . " {$itemname}(s) on the item
			    market for {$num_format} {$curre}.", true, 'itemmarket.php');
        }
    } else {
        $csrf = request_csrf_html("imadd_form");
        $_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs($_GET['ID']) : 0;
        echo "<form method='post' action='?action=add'>
                <div class='card'>
                    <div class='card-header'>
                        Adding Item to Market
                    </div>
                    <div class='card-body'>
                        <div class='row'>
                            <div class='col-12 col-sm-6 col-xl-4 col-xxxl'>
                                <div class='row'>
                                     <div class='col-12'>
                                        <small>Item</small>
                                    </div>
                                    <div class='col-12'>
                                        " . inventory_dropdown('ID', $_GET['ID']) . "
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-xl-4 col-xxxl'>
                                <div class='row'>
                                     <div class='col-12'>
                                        <small>Sell Quantity</small>
                                    </div>
                                    <div class='col-12'>
                                        <input type='number' min='1' required='1' class='form-control' name='QTY'>
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-xl-4 col-xxxl'>
                                <div class='row'>
                                     <div class='col-12'>
                                        <small>Currency</small>
                                    </div>
                                    <div class='col-12'>
                                        <select name='currency' type='dropdown' class='form-control'>
                    						<option value='primary'>Copper Coins</option>
                    						<option value='secondary'>Chivalry Tokens</option>
                    					</select>
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-xl-4 col-xxxl'>
                                <div class='row'>
                                     <div class='col-12'>
                                        <small>Price/item</small>
                                    </div>
                                    <div class='col-12'>
                                        <input  type='number' min='1' required='1' class='form-control' name='price' />
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-xl-4 col-xxxl'>
                                <div class='row'>
                                     <div class='col-12'>
                                        <small>Deposit Location</small>
                                    </div>
                                    <div class='col-12'>
                                        <select name='deposit' type='dropdown' class='form-control'>
                    						<option value='false'>Wallet</option>
                    						<option value='true'>Bank</option>
                    					</select>
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-xl-4 col-xxxl-12'>
                                <div class='row'>
                                     <div class='col-12'>
                                        <small><br /></small>
                                    </div>
                                    <div class='col-12'>
                                        <input type='submit' class='btn btn-primary btn-block' value='Create Listing'>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {$csrf}
                </form><br />";
        alert('info',"","Bank deposits have a 5% fee. All sales are subject to a 2% market fee.",false);
    }
}
function itemmarket_dropdown($ddname = "item", $selected = -1)
{
    global $db, $userid;
    $ret = "<select name='$ddname' type='dropdown' class='form-control'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `i`.*, `it`.*
    				 FROM `itemmarket` AS `i`
    				 INNER JOIN `items` AS `it`
    				 ON `i`.`imITEM` = `it`.`itmid`
    				 ORDER BY `itmname` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        if (!isset($count[$r['itmid']]))
            $count[$r['itmid']]=0;
		if ($count[$r['itmid']] == 0)
		{
            $ret .= "\n<option value='{$r['itmid']}'";
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