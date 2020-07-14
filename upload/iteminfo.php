<?php
/*
	File:		iteminfo.php
	Created: 	4/5/2016 at 12:14AM Eastern Time
	Info: 		Displays detailed information about the item inputted.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
$_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs($_GET['ID']) : '';
$itmid = $_GET['ID'];
if (!$itmid) {
    alert('danger', 'Uh Oh!', 'Invalid or non-existent Item ID.', true, 'inventory.php');
} else {
    $q =
        $db->query(
            "/*qc=on*/SELECT `i`.*, `itmtypename`
                     FROM `items` AS `i`
                     INNER JOIN `itemtypes` AS `it`
                     ON `i`.`itmtype` = `it`.`itmtypeid`
                     WHERE `i`.`itmid` = {$itmid}
                     LIMIT 1");
	$armory=$db->fetch_single($db->query("/*qc=on*/SELECT SUM(`gaQTY`) FROM `guild_armory` WHERE `gaITEM` = {$itmid} AND `gaGUILD` != 1"));
	$invent=$db->fetch_single($db->query("/*qc=on*/SELECT SUM(`inv_qty`) FROM `inventory` WHERE `inv_itemid` = {$itmid} AND `inv_userid` != 1"));
	$market=$db->fetch_single($db->query("/*qc=on*/SELECT SUM(`imQTY`) FROM `itemmarket` WHERE `imITEM` = {$itmid}"));
	$primary=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`equip_primary`) FROM `users` WHERE `equip_primary` = {$itmid} AND `userid` != 1"));
	$secondary=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`equip_secondary`) FROM `users` WHERE `equip_secondary` = {$itmid} AND `userid` != 1"));
	$armor=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`equip_armor`) FROM `users` WHERE `equip_armor` = {$itmid} AND `userid` != 1"));
	$badge=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`equip_badge`) FROM `users` WHERE `equip_badge` = {$itmid} AND `userid` != 1"));
	$trink=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`equip_slot`) FROM `user_equips` WHERE `itemid` = {$itmid}"));
	$total=$invent+$armory+$market+$primary+$secondary+$armor+$badge+$trink;
    if ($db->num_rows($q) == 0) {
        alert('danger', 'Uh Oh!', 'Invalid or non-existent Item ID.', true, 'inventory.php');
    } else {
        $id = $db->fetch_row($q);
		echo "<div class='row'>
			<div class='col-lg'>
				<div class='card'>
					<div class='card-header text-left'>
						{$id['itmname']}
					</div>
					<div class='card-body'>
						<div class='row'>
							<div class='col-lg-7'>
								" . returnIcon($itmid, 8) . "
							</div>
							<div class='col-lg'>
								<h6>Type</h6>
								<h2>{$id['itmtypename']}</h2>
								<br />
								<h6>Buy (Copper Coins)</h6>
								<h2>";
								if ($id['itmbuyprice'] > 0) 
									echo number_format($id['itmbuyprice']);
								else
									echo "N/A";
								echo"</h2>
								<br />
								<h6>Sell (Copper Coins)</h6>
								<h2>";
								if ($id['itmsellprice'] > 0) 
									echo number_format($id['itmsellprice']);
								else
									echo "N/A";
								echo "</h2>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class='col-lg'>
				<div class='card text-left'>
					<div class='card-header text-left'>
						Detailed
					</div>
					<div class='card-body'>
						<div class='row'>
							<div class='col-4'>
								<b>Desc</b>
							</div>
							<div class='col'>
								{$id['itmdesc']}
							</div>
						</div>
						<br />";
						$towns='';
						$sq=$db->query("/*qc=on*/SELECT `sitemSHOP` FROM `shopitems` WHERE `sitemITEMID` = {$_GET['ID']}");
						if ($db->num_rows($sq) > 0)
						{
							echo "<div class='row'>
									<div class='col-4'>
										<b>Town Available</b>
									</div>
									<div class='col'>";
								while ($sr=$db->fetch_row($sq))
								{
									$shop=$db->fetch_single($db->query("/*qc=on*/SELECT `shopLOCATION` FROM `shops` WHERE `shopID` = {$sr['sitemSHOP']}"));
									$towns.= "<a href='travel.php?to={$shop}'>{$api->SystemTownIDtoName($shop)}</a>, ";
								}
								echo "{$towns}</div>
							</div><br />";
						}
						$towns='';
						$sq=$db->query("/*qc=on*/SELECT `mine_location` FROM `mining_data` WHERE `mine_copper_item` = {$_GET['ID']} OR `mine_silver_item` = {$_GET['ID']} OR `mine_gold_item` = {$_GET['ID']} OR `mine_gem_item` = {$_GET['ID']}");
						if ($db->num_rows($sq) > 0)
						{
							echo "<div class='row'>
									<div class='col-4'>
										<b>Mine Drop</b>
									</div>
									<div class='col'>";
								while ($sr=$db->fetch_row($sq))
								{
									$shop2=$sr['mine_location'];
									$towns.= "<a href='travel.php?to={$shop}'>{$api->SystemTownIDtoName($shop)}</a>, ";
								}
								echo "{$towns}</div>
							</div><br />";
						}
						echo"
						<div class='row'>
							<div class='col-4'>
								<b>Circulating</b>
							</div>
							<div class='col'>
								" . number_format($total) . "
							</div>
						</div><br />";
						$start=0;
						for ($enum = 1; $enum <= 3; $enum++) 
						{
							if ($id["effect{$enum}_on"] == 'true') 
							{
								if ($start == 0)
								{
									echo "<div class='row'>
											<div class='col-4'>
												<b>Effect(s)</b>
											</div>
										</div>
										<br />
										<div class='row'>";
								}
								$start = $start + 1;
								$einfo = unserialize($id["effect{$enum}"]);
								$einfo['inc_type'] = ($einfo['inc_type'] == 'percent') ? '%' : '';
								$einfo['dir'] = ($einfo['dir'] == 'pos') ? '+' : '-';
								$class = ($einfo['dir'] == '+') ? 'text-success' : 'text-danger';
								$stats =
									array("energy" => "Energy", "will" => "Will",
										"brave" => "Bravery", "level" => "Level",
										"hp" => "Health", "strength" => "Strength",
										"agility" => "Agility", "guard" => "Guard",
										"labor" => "Labor", "iq" => "IQ",
										"infirmary" => "Infirmary minutes", "dungeon" => "Dungeon minutes",
										"primary_currency" => "Copper Coins", "secondary_currency"
									=> "Chivalry Tokens", "crimexp" => "Experience", "vip_days" =>
										"VIP Days", "luck" => "Luck", "premium_currency" => "Mutton");
								$statformatted = $stats["{$einfo['stat']}"];
								echo "<div class='col {$class}'>
										{$einfo['dir']}" . number_format($einfo['inc_amount']) . "{$einfo['inc_type']} {$statformatted}
									</div>";
								if ($start == 3)
								{
									echo "
									</div><br />";
								}
							}
						}
						if ($id['weapon']) 
						{
							if ($_GET['ID'] == 235)
								$id['weapon']=($id['weapon']*0.25)*$ir['level'];
							echo "
							<div class='row'>
								<div class='col-4'>
									<b>Weapon Rating</b>
								</div>
								<div class='col'>
									" . number_format($id['weapon']) . "
								</div>
							</div><br />";
						}
						if ($id['ammo']) 
						{
							echo "
							<div class='row'>
								<div class='col-4'>
									<b>Ammo</b>
								</div>
								<div class='col'>
									<a href='?ID={$id['ammo']}'>" . $api->SystemItemIDtoName($id['ammo']) . "</a>
								</div>
							</div><br />";
						}
						if ($id['armor']) 
						{
							echo "
							<div class='row'>
								<div class='col-4'>
									<b>Armor Rating</b>
								</div>
								<div class='col'>
									" . number_format($id['armor']) . "
								</div>
							</div><br />";
						}
						echo"
					</div>
				</div>
			</div>
		</div>";
        $db->free_result($q);
    }
}
$h->endpage();