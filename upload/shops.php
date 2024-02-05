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
    if ($db->num_rows($q) == 0) 
	{
        echo "This town doesn't have any shops, funny enough.";
    } 
	else 
	{
		echo "<div class='row'>";
        while ($r = $db->fetch_row($q)) 
		{
			echo "
			<div class='col-md-4'>
				<a href='?action=shop&shop={$r['shopID']}'>
					<div class='card'>
						<div class='card-header'>
							<h2 class='mb-0'>
								<button class='btn btn-block btn-block text-left' type='button'>
									<div class='row'>
										<div class='col-md'>
											<div class='row'>
												<div class='col'>
													{$r['shopNAME']}<br />
													<small>{$r['shopDESCRIPTION']}</small>
												</div>
											</div>
										</div>
									</div>
								</button>
							</h2>
						</div>
					</div>
				</a>
			</div>";
        }
        echo "</div><br />
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
        if ($shopdata['shopLOCATION'] == $ir['location']) 
		{
		    //Bargaining Skill
			$specialnumber = ((getUserSkill($userid, 12) * getSkillBonus(12)) / 100);
            alert('info',"","You have " . shortNumberParse($ir['primary_currency']) . " " . loadImageAsset("menu/coin-copper.svg") . " to spend while you're at the {$shopdata['shopNAME']} shop.",false);
            $qtwo =
                $db->query(
                    "/*qc=on*/SELECT `i`.*, `itmtypename`, `sitemID`
                             FROM `shopitems` AS `si`
                             INNER JOIN `items` AS `i`
                             ON `si`.`sitemITEMID` = `i`.`itmid`
                             INNER JOIN `itemtypes` AS `it`
                             ON `i`.`itmtype` = `it`.`itmtypeid`
                             WHERE `si`.`sitemSHOP` = {$_GET['shop']}
                             ORDER BY `itmtype` ASC, `itmbuyprice` ASC,
                             `itmname` ASC");
            $lt = "";
			echo "
			<div class='accordion' id='inventoryAccordian'>";
            while ($r = $db->fetch_row($qtwo)) 
			{
				$r['itmbuyprice']=$r['itmbuyprice']-($r['itmbuyprice']*$specialnumber);
				$r['itmbuyprice'] = $api->SystemReturnTax($r['itmbuyprice']);
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
				$r['itmdesc'] = htmlentities($r['itmdesc'], ENT_QUOTES);
				$rcon = returnIcon($r['itmid'],2);
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
					<div class='card-header' id='heading{$r['itmid']}'>
						<h2 class='mb-0'>
							<button class='btn btn-block btn-block text-left' type='button' data-toggle='collapse' data-target='#collapse{$r['itmid']}' aria-expanded='true' aria-controls='collapse{$r['itmid']}'>
								<div class='row'>
									<div class='col-2 col-md-1'>
										{$rcon}
									</div>
									<div class='col-10 col-sm-5 col-md-7'>
										<div class='row'>
											<div class='col'>
												{$r['itmname']}
											</div>
											<div class='col'>
												<b>Price</b><br />
												<small>" . shortNumberParse($r['itmbuyprice']) . " " . loadImageAsset("menu/coin-copper.svg") . "</small>
											</div>
											<br />
										</div>
									</div>
									<div class='col'>
										<form action='?action=buy&ID={$r['sitemID']}' method='post'>
											<div class='row'>
												<div class='col'>
													<span class='sr-only'>Buy Quantity: </span>
													<input class='form-control' type='number' min='1' name='qty' placeholder='Buy quantity' aria-label='Buy Quantity' title='Input buy quantity' />
												</div>
												<div class='col-sm-3 col-4'>
														<input class='btn btn-primary btn-block' type='submit' value='Buy' />
													</form>
												</div>
											</div>
										</form>
									</div>
								</div>
							</button>
						</h2>
					</div>
					<div id='collapse{$r['itmid']}' class='collapse' aria-labelledby='heading{$r['itmid']}' data-parent='#inventoryAccordian'>
					<div class='card-body'>
						<div class='row'>
							<div class='col-md-1'>
								" . returnIcon($r['itmid'],3.5) . "
							</div>
							<div class='col-md-8 text-left'>
								<b>{$r['itmname']}</b> is a {$lt} item.<br />
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
					if ($api->UserMemberLevelGet($userid, 'Admin'))
					{
						echo "<div class='col-md'>
							<div class='row'>
								<div class='col'>
									<a class='btn btn-primary' href='staff/staff_shops.php?action=delstock&id={$r['sitemID']}'>Remove From Shop</a>
								</div>
							</div>
						</div>";
					}
					echo"	
					</div>
						<hr />
						<div class='row'>
							<div class='col'>
								<b>Sell</b><br />
								<small>" . shortNumberParse($r['itmsellprice']) . " " . loadImageAsset("menu/coin-copper.svg") . "</small>
							</div>
							<div class='col'>
								<b>Circulating</b><br />
								<small>" . shortNumberParse($total) . "</small>
							</div>";
							if ($r['weapon'] > 0)
							{
								echo "
								<div class='col'>
									<b>Weapon</b><br />
									<small>" . shortNumberParse($r['weapon']) . "</small>
								</div>";
							}
							if ($r['ammo'] > 0)
							{
								echo "
								<div class='col'>
									<b>Projectile</b><br />
									<small><a href='iteminfo.php?ID={$r['ammo']}'>{$api->SystemItemIDtoName($r['ammo'])}</a></small>
								</div>";
							}
							if ($r['armor'] > 0)
							{
								echo "
								<div class='col'>
									<b>Armor</b><br />
									<small>" . shortNumberParse($r['armor']) . "</small>
								</div>";
							}
							echo"
						</div>
						<hr />
						<div class='row'>
						</div>
					</div>
				</div>
			</div>";
            }
            $db->free_result($qtwo);
            echo "</div>";
        } 
		else {
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
                $specialnumber = ((getUserSkill($userid, 12) * getSkillBonus(12)) / 100);
				$itemd['itmbuyprice'] = $itemd['itmbuyprice'] - ($itemd['itmbuyprice'] * $specialnumber);
				$price = ($api->SystemReturnTax($itemd['itmbuyprice']) * $_POST['qty']);
				if ($ir['primary_currency'] < $price) {
				    alert('danger', "Uh Oh!", "You do not have enough Copper Coins to buy 
                            " . shortNumberParse($_POST['qty']) . " {$itemd['itmname']}(s). You need 
                            " . shortNumberParse($price) . " Copper coins, but only have ". shortNumberParse($ir['primary_currency']) . ".", true, "shops.php");
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

				addToEconomyLog('Game Shops', 'copper', (($itemd['itmbuyprice'] * $_POST['qty'])*-1));
                item_add($userid, $itemd['itmid'], $_POST['qty']);
                $db->query(
                    "UPDATE `users`
						 SET `primary_currency` = `primary_currency` - $price
						 WHERE `userid` = $userid");
                $ib_log = $db->escape("{$ir['username']} bought " . shortNumberParse($_POST['qty']) . " {$itemd['itmname']}(s) for " . shortNumberParse($price) . " Copper Coins.");
                alert('success', "Success!", "You have bought " . shortNumberParse($_POST['qty']) . " {$itemd['itmname']}(s) for " . shortNumberParse($price) . " Copper Coins.", true, "shops.php");
                $api->SystemLogsAdd($userid, 'itembuy', $ib_log);
                $api->SystemCreditTax($api->SystemReturnTaxOnly($itemd['itmbuyprice']* $_POST['qty']), 1, -1);
            }
            $db->free_result($q);
        }
    }
}

$h->endpage();