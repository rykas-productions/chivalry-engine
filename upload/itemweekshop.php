<?php
$macropage = ('itemweekshop.php');
require('globals.php');
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
	alert('danger',"Uh Oh!","You cannot visit the Item of the Week Shop while in the dungeon and infirmary.",true,'explore.php');
	die($h->endpage());
}
echo "<h4>Item of the Week</h4><hr />
Here you may buy the three Items of the Week for a 20% discount. This should change weekly, so check back often.<br />";
$item2=$db->fetch_row($db->query("SELECT * FROM `items` WHERE `itmid` = {$set['itemweek2']}"));
$item1=$db->fetch_row($db->query("SELECT * FROM `items` WHERE `itmid` = {$set['itemweek1']}"));
$item3=$db->fetch_row($db->query("SELECT * FROM `items` WHERE `itmid` = {$set['itemweek3']}"));
$item1price=$item1['itmbuyprice']-($item1['itmbuyprice']/5);
$item2price=$item2['itmbuyprice']-($item2['itmbuyprice']/5);
$item3price=$item3['itmbuyprice']-($item3['itmbuyprice']/5);
if (!isset($_POST['item']))
	$_POST['item']=0;
if (isset($_POST['qty']))
	$_POST['qty'] = (isset($_POST['qty']) && is_numeric($_POST['qty'])) ? abs($_POST['qty']) : '';
if ($_POST['item'] == 1)
{
	$totalcost=$item1price*$_POST['qty'];
    if ($api->UserHasCurrency($userid,'primary',$totalcost))
    {
        $api->UserTakeCurrency($userid,'primary',$totalcost);
        $api->UserGiveItem($userid,$item1['itmid'],$_POST['qty']);
		addToEconomyLog('Game Shops', 'copper', $totalcost);
        alert('success',"Success!","You have successfully bought {$_POST['qty']} {$item1['itmname']}(s) for " . number_format($totalcost) . " Copper Coins.",false);
    }
    else
    {
        alert('danger',"Uh Oh!","You do not have enough Copper Coins to buy {$_POST['qty']} {$item1['itmname']}(s).",false);
    }
}
if ($_POST['item'] == 2)
{
	$totalcost=$item2price*$_POST['qty'];
    if ($api->UserHasCurrency($userid,'primary',$totalcost))
    {
        $api->UserTakeCurrency($userid,'primary',$totalcost);
        $api->UserGiveItem($userid,$item2['itmid'],$_POST['qty']);
        alert('success',"Success!","You have successfully bought {$_POST['qty']} {$item2['itmname']}(s) for " . number_format($totalcost) . " Copper Coins.",false);
    }
    else
    {
        alert('danger',"Uh Oh!","You do not have enough Copper Coins to buy {$_POST['qty']} {$item2['itmname']}(s).",false);
    }
}
if ($_POST['item'] == 3)
{
	$totalcost=$item3price*$_POST['qty'];
    if ($api->UserHasCurrency($userid,'primary',$totalcost))
    {
        $api->UserTakeCurrency($userid,'primary',$totalcost);
        $api->UserGiveItem($userid,$item3['itmid'],$_POST['qty']);
        alert('success',"Success!","You have successfully bought {$_POST['qty']} {$item3['itmname']}(s) for " . number_format($totalcost) . " Copper Coins.",false);
    }
    else
    {
        alert('danger',"Uh Oh!","You do not have enough Copper Coins to buy buy {$_POST['qty']} {$item3['itmname']}(s).",false);
    }
}
echo "<div class='row'>";
echo "	<div class='col-md'>
			<div class='card'>
				<div class='card-header'>
					<div class='row'>
						<div class='col'>
							Price - " . shortNumberParse($item1price) . " Copper Coins
						</div>
					</div>
				</div>
				<div class='card-body'>
					<h3><a href='iteminfo.php?ID={$item1['itmid']}'>{$item1['itmname']}</a></h3>";
						 $uhoh = 0;
						//List the item's effects.
						for ($enum = 1; $enum <= 3; $enum++) {
							if ($item1["effect{$enum}_on"] == 'true') {
								//Lets make the item's effects more user friendly to read, eh.
								$einfo = unserialize($item1["effect{$enum}"]);
								$einfo['inc_type'] = ($einfo['inc_type'] == 'percent') ? '%' : '';
								$einfo['dir'] = ($einfo['dir'] == 'pos') ? "+" : "-";
								$stats =
									array("energy" => "Energy", "will" => "Will",
										"brave" => "Bravery", "level" => "Level",
										"hp" => "Health", "strength" => "Strength",
										"agility" => "Agility", "guard" => "Guard",
										"labor" => "Labor", "iq" => "IQ",
										"infirmary" => "Infirmary Time", "dungeon" => "Dungeon Time",
										"primary_currency" => "Copper Coins", "secondary_currency"
									=> "Chivalry Tokens", "crimexp" => "Experience", "vip_days" =>
										"VIP Days" , "premium_currency" => "Mutton", "luck" => "Luck");
								$statformatted = $stats["{$einfo['stat']}"];
								echo "{$einfo['dir']}" . number_format($einfo['inc_amount']) . "{$einfo['inc_type']} {$statformatted}. ";
							} //If item has no effects, lets list the description instead.
							else {
								$uhoh++;
							}
							if ($uhoh == 3) {
								echo "{$item1['itmdesc']}";
							}
						}
					echo"<br />
					<form method='post'>
						<div class='row'>
							<div class='col'>
								<input type='number' name='qty' name='qty' min='1' placeholder='Quantity' class='form-control'>
								<input type='hidden' name='item' value='1'>
							</div>
							<div class='col-3'>
								<input type='submit' value='Buy' class='btn btn-primary'>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>";
echo "	<div class='col-md'>
			<div class='card'>
				<div class='card-header'>
					<div class='row'>
						<div class='col'>
							Price - " . shortNumberParse($item2price) . " Copper Coins
						</div>
					</div>
				</div>
				<div class='card-body'>
					<h3><a href='iteminfo.php?ID={$item2['itmid']}'>{$item2['itmname']}</a></h3>";
						 $uhoh = 0;
						//List the item's effects.
						for ($enum = 1; $enum <= 3; $enum++) {
							if ($item2["effect{$enum}_on"] == 'true') {
								//Lets make the item's effects more user friendly to read, eh.
								$einfo = unserialize($item2["effect{$enum}"]);
								$einfo['inc_type'] = ($einfo['inc_type'] == 'percent') ? '%' : '';
								$einfo['dir'] = ($einfo['dir'] == 'pos') ? "+" : "-";
								$stats =
									array("energy" => "Energy", "will" => "Will",
										"brave" => "Bravery", "level" => "Level",
										"hp" => "Health", "strength" => "Strength",
										"agility" => "Agility", "guard" => "Guard",
										"labor" => "Labor", "iq" => "IQ",
										"infirmary" => "Infirmary Time", "dungeon" => "Dungeon Time",
										"primary_currency" => "Copper Coins", "secondary_currency"
									=> "Chivalry Tokens", "crimexp" => "Experience", "vip_days" =>
										"VIP Days" , "premium_currency" => "Mutton", "luck" => "Luck");
								$statformatted = $stats["{$einfo['stat']}"];
								echo "{$einfo['dir']}" . number_format($einfo['inc_amount']) . "{$einfo['inc_type']} {$statformatted}. ";
							} //If item has no effects, lets list the description instead.
							else {
								$uhoh++;
							}
							if ($uhoh == 3) {
								echo "{$item2['itmdesc']}";
							}
						}
					echo"<br />
					<form method='post'>
						<div class='row'>
							<div class='col'>
								<input type='number' name='qty' name='qty' min='1' placeholder='Quantity' class='form-control'>
								<input type='hidden' name='item' value='2'>
							</div>
							<div class='col-3'>
								<input type='submit' value='Buy' class='btn btn-primary'>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>";
echo "	<div class='col-md'>
			<div class='card'>
				<div class='card-header'>
					<div class='row'>
						<div class='col'>
							Price - " . shortNumberParse($item3price) . " Copper Coins
						</div>
					</div>
				</div>
				<div class='card-body'>
					<h3><a href='iteminfo.php?ID={$item3['itmid']}'>{$item3['itmname']}</a></h3>";
						 $uhoh = 0;
						//List the item's effects.
						for ($enum = 1; $enum <= 3; $enum++) {
							if ($item3["effect{$enum}_on"] == 'true') {
								//Lets make the item's effects more user friendly to read, eh.
								$einfo = unserialize($item3["effect{$enum}"]);
								$einfo['inc_type'] = ($einfo['inc_type'] == 'percent') ? '%' : '';
								$einfo['dir'] = ($einfo['dir'] == 'pos') ? "+" : "-";
								$stats =
									array("energy" => "Energy", "will" => "Will",
										"brave" => "Bravery", "level" => "Level",
										"hp" => "Health", "strength" => "Strength",
										"agility" => "Agility", "guard" => "Guard",
										"labor" => "Labor", "iq" => "IQ",
										"infirmary" => "Infirmary Time", "dungeon" => "Dungeon Time",
										"primary_currency" => "Copper Coins", "secondary_currency"
									=> "Chivalry Tokens", "crimexp" => "Experience", "vip_days" =>
										"VIP Days" , "premium_currency" => "Mutton", "luck" => "Luck");
								$statformatted = $stats["{$einfo['stat']}"];
								echo "{$einfo['dir']}" . number_format($einfo['inc_amount']) . "{$einfo['inc_type']} {$statformatted}. ";
							} //If item has no effects, lets list the description instead.
							else {
								$uhoh++;
							}
							if ($uhoh == 3) {
								echo "{$item3['itmdesc']}";
							}
						}
					echo"<br />
					<form method='post'>
						<div class='row'>
							<div class='col'>
								<input type='number' name='qty' name='qty' min='1' placeholder='Quantity' class='form-control'>
								<input type='hidden' name='item' value='3'>
							</div>
							<div class='col-3'>
								<input type='submit' value='Buy' class='btn btn-primary'>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>";
echo "</div>";
$h->endpage();