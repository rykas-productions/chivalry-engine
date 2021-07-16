<?php
/*
	File:		itemappendix.php
	Created: 	8/19/2017 at 6:42PM Eastern Time
	Info: 		Displays all the in-game items, along with the quantity
                of those items in circulation.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
echo "<h3><i class='fas fa-list'></i> Item Appendix</h3><hr />This page lists all the items in the game, along with how many are in circulation.
    This may be useful for players who do item flipping, or those who are just plain old curious. Hovering over the
    item will give you its description. Tapping its name will take you to its info page<br />
	<small><a href='?view=all'>View All Items</a></small><hr />
    [<a href='?view=weapon'>Weapons</a>] [<a href='?view=armor'>Armor</a>] [<a href='?view=vip'>VIP</a>] 
	[<a href='?view=infirmary'>Infirmary</a>] [<a href='?view=dungeon'>Dungeon</a>] [<a href='?view=material'>Materials</a>] [<a href='?view=seed'>Seeds</a>] [<a href='?view=food'>Food</a>] 
	[<a href='?view=potions'>Potions</a>] [<a href='?view=holiday'>Holiday</a>] [<a href='?view=scrolls'>Scrolls</a>] [<a href='?view=rings'>Trinkets</a>] 
	[<a href='?view=badge'>Badges</a>] [<a href='?view=other'>Other</a>]";
if (!isset($_GET['view']))
    $_GET['view'] = 'weapon';
if ($_GET['view'] == 'weapon') {
    //Select all the in-game weapons
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 1 AND `itmbuyable` = 'true' ORDER BY `weapon` ASC");
} elseif ($_GET['view'] == 'armor') {
    //Select all the in-game armor
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 2 AND `itmbuyable` = 'true' ORDER BY `armor` ASC");
} elseif ($_GET['view'] == 'vip') {
    //Select all the in-game VIP Packs
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 3 AND `itmbuyable` = 'true' ORDER BY `itmname` ASC");
} elseif ($_GET['view'] == 'infirmary') {
    //Select all the in-game Infirmary Items
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 4 AND `itmbuyable` = 'true' ORDER BY `itmname` ASC");
} elseif ($_GET['view'] == 'dungeon') {
    //Select all the in-game Dungeon Items
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 5 AND `itmbuyable` = 'true' ORDER BY `itmname` ASC");
} 
elseif ($_GET['view'] == 'material') {
    //Select all the in-game materials
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 6 AND `itmbuyable` = 'true' ORDER BY `itmname` ASC");
}
elseif ($_GET['view'] == 'food') {
    //Select all the in-game food
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 7 AND `itmbuyable` = 'true' ORDER BY `itmname` ASC");
}
elseif ($_GET['view'] == 'potions') {
    //Select all the in-game potions
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 8 AND `itmbuyable` = 'true' ORDER BY `itmname` ASC");
} 
elseif ($_GET['view'] == 'other') {
    //Select all the in-game other items.
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 9 AND `itmbuyable` = 'true' ORDER BY `itmname` ASC");
} 
elseif ($_GET['view'] == 'holiday') {
    //Select all the in-game holiday items.
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 10 AND `itmbuyable` = 'true' ORDER BY `itmname` ASC");
} 
elseif ($_GET['view'] == 'scrolls') {
    //Select all the in-game scrolls
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 11 AND `itmbuyable` = 'true' ORDER BY `itmname` ASC");
} 
elseif ($_GET['view'] == 'rings') {
	$itid=$db->fetch_single($db->query("SELECT `itmtypeid` FROM `itemtypes` WHERE `itmtypename` = 'Rings'"));
	$ncid=$db->fetch_single($db->query("SELECT `itmtypeid` FROM `itemtypes` WHERE `itmtypename` = 'Necklaces'"));
	$pnid=$db->fetch_single($db->query("SELECT `itmtypeid` FROM `itemtypes` WHERE `itmtypename` = 'Pendants'"));
    //Select all the in-game rings
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 12 OR `itmtype` = {$itid} OR `itmtype` = {$ncid} OR `itmtype` = {$pnid} AND `itmbuyable` = 'true' ORDER BY `itmname` ASC");
} elseif ($_GET['view'] == 'badge') {
    //Select all the in-game badges
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 13 AND `itmbuyable` = 'true' ORDER BY `itmname` ASC");
} elseif ($_GET['view'] == 'seed') {
    //Select all the in-game seeds
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 14 AND `itmbuyable` = 'true' ORDER BY `itmname` ASC");
} elseif ($_GET['view'] == 'all') {
    //Select all the in-game seeds
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmbuyable` = 'true' ORDER BY `itmname` ASC");
} else {
    alert('danger',"Uh Oh!","Please select a valid item category type.",true,'explore.php');
	die($h->endpage());
}
echo "
<div class='accordion' id='inventoryAccordian'>";
while ($r = $db->fetch_row($q)) 
{
	$type = $db->fetch_single($db->query("SELECT `itmtypename` FROM `itemtypes` WHERE `itmtypeid` = {$r['itmtype']}"));
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
	$totalbuy=$total*$r['itmbuyprice'];
	$totalsell=$total*$r['itmsellprice'];
    echo "
			<div class='card'>
				<div class='card-header' id='heading{$r['itmid']}'>
					<h2 class='mb-0'>
						<button class='btn btn-block btn-block text-left' type='button' data-toggle='collapse' data-target='#collapse{$r['itmid']}' aria-expanded='true' aria-controls='collapse{$r['itmid']}'>
							<div class='row'>
								<div class='col-md-1'>
									{$rcon}
								</div>
								<div class='col-md'>
									{$r['itmname']}
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
								<b><a href='iteminfo.php?ID={$r['itmid']}'>{$r['itmname']}</a></b> is a {$type} item.<br />
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
											$statformatted = statParser($einfo['stat']);
										echo "{$einfo['dir']}" . number_format($einfo['inc_amount']) . "{$einfo['inc_type']} {$statformatted}.";
									}
								}
					echo "	</div>
						</div>
						<hr />
						<div class='row'>
							<div class='col-md'>
								<b>Buy Value</b><br />
								<small>" . number_format($totalbuy) . " Copper Coins</small>
							</div>
							<div class='col-md'>
								<b>Sell Value</b><br />
								<small>" . number_format($totalsell) . " Copper Coins</small>
							</div>
							<div class='col-md'>
								<b>Circulating</b><br />
								<small>" . number_format($total) . "</small>
							</div>
						</div>
						<hr />
						<div class='row'>";
							if ($r['weapon'] > 0)
							{
								echo "
								<div class='col-md'>
									<b>Weapon</b><br />
									<small>" . number_format($r['weapon']) . "</small>
								</div>";
							}
							if ($r['ammo'] > 0)
							{
								echo "
								<div class='col-md'>
									<b>Projectile</b><br />
									<small><a href='iteminfo.php?ID={$r['ammo']}'>{$api->SystemItemIDtoName($r['ammo'])}</a></small>
								</div>";
							}
							if ($r['armor'] > 0)
							{
								echo "
								<div class='col-md'>
									<b>Armor</b><br />
									<small>" . number_format($r['armor']) . "</small>
								</div>";
							}
							echo "
						</div>
					</div>
				</div>
			</div>";
}
echo "</div>";
$h->endpage();