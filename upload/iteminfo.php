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
					 //Select game item count. This only accounts for items in an user's inventory. Nothing else.
	$q2= $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`inv_qty`),
			(/*qc=on*/SELECT SUM(`imQTY`) FROM `itemmarket` WHERE `imITEM` = {$itmid}), 
			(/*qc=on*/SELECT SUM(`gaQTY`) FROM `guild_armory` WHERE `gaITEM` = {$itmid} AND `gaGUILD` != 1)
			FROM `inventory` WHERE `inv_itemid` = {$itmid} AND `inv_userid` != 1"));
	$q3=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`equip_primary`),
			(/*qc=on*/SELECT COUNT(`equip_secondary`) FROM `users` WHERE `equip_secondary` = {$itmid} AND `userid` != 1),
			(/*qc=on*/SELECT COUNT(`equip_armor`) FROM `users` WHERE `equip_armor` = {$itmid} AND `userid` != 1),
			(/*qc=on*/SELECT COUNT(`equip_potion`) FROM `users` WHERE `equip_potion` = {$itmid} AND `userid` != 1),
			(/*qc=on*/SELECT COUNT(`equip_badge`) FROM `users` WHERE `equip_badge` = {$itmid} AND `userid` != 1)
			FROM `users` WHERE `equip_primary` = {$itmid} AND `userid` != 1"));
	$total=$q2+$q3;
    if ($db->num_rows($q) == 0) {
        alert('danger', 'Uh Oh!', 'Invalid or non-existent Item ID.', true, 'inventory.php');
    } else {
        $id = $db->fetch_row($q);
        echo "
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Looking up the item information for {$id['itmname']}.
				</th>
			</tr>
			<tr>
				<td colspan='2'>
					" . returnIcon($itmid,5) . "
				</td>
			</tr>
			<tr>
				<th width='33%'>
					Item Type
				</th>
				<td>
					{$id['itmtypename']}
				</td>
			</tr>
			<tr>
				<th width='33%'>
					Item Information
				</th>
				<td>
					{$id['itmdesc']}
				</td>
			</tr>";
            $towns='';
            $sq=$db->query("/*qc=on*/SELECT `sitemSHOP` FROM `shopitems` WHERE `sitemITEMID` = {$_GET['ID']}");
            if ($db->num_rows($sq) > 0)
            {
                echo"
                <tr>
				<th width='33%'>
					Available in Town(s)
				</th>
				<td>";
                    while ($sr=$db->fetch_row($sq))
                    {
                        $shop=$db->fetch_single($db->query("/*qc=on*/SELECT `shopLOCATION` FROM `shops` WHERE `shopID` = {$sr['sitemSHOP']}"));
                        $towns.= "<a href='travel.php?to={$shop}'>{$api->SystemTownIDtoName($shop)}</a>, ";
                    }
					echo $towns;
            }
				echo"
				</td>
			</tr>";
            $towns2='';
            $sq=$db->query("/*qc=on*/SELECT `mine_location` FROM `mining_data` WHERE `mine_copper_item` = {$_GET['ID']} OR `mine_silver_item` = {$_GET['ID']} OR `mine_gold_item` = {$_GET['ID']} OR `mine_gem_item` = {$_GET['ID']}");
			if ($db->num_rows($sq) > 0)
            {
               echo" 
                <tr>
                    <th width='33%'>
                        Available in Mine(s)
                    </th>
                    <td>";
						while ($sr=$db->fetch_row($sq))
						{
							$shop2=$sr['mine_location'];
							$towns2.= "<a href='travel.php?to={$shop2}'>{$api->SystemTownIDtoName($shop2)}</a>, ";
						}
					echo $towns2;
            }
				echo"
				</td>
			</tr>
			<tr>
				<th width='33%'>
					Item Buying Price
				</th>
				<td>";
        if ($id['itmbuyprice'] > 0) {
            echo number_format($id['itmbuyprice']);
        } else {
            echo "Unpurchaseable";
        }
        echo "
				</td>
			</tr>
			<tr>
				<th width='33%'>
					Item Selling Price
				</th>
				<td>";
        if ($id['itmsellprice']) {
            echo number_format($id['itmsellprice']);
        } else {
            echo "Unsellable.";
        }
        echo "
				</td>
			</tr>";
		echo "<tr>
			<th>
				Total in Circulation
			</th>
			<td>
				" . number_format($total) . "
			</td>
		</tr>";
        for ($enum = 1; $enum <= 3; $enum++) {
            if ($id["effect{$enum}_on"] == 'true') {
                $einfo = unserialize($id["effect{$enum}"]);
                $einfo['inc_type'] = ($einfo['inc_type'] == 'percent') ? '%' : '';
                $einfo['dir'] = ($einfo['dir'] == 'pos') ? 'Increases' : 'Decreases';
                $stats =
                    array("energy" => "Energy", "will" => "Will",
                        "brave" => "Bravery", "level" => "Level",
                        "hp" => "Health", "strength" => "Strength",
                        "agility" => "Agility", "guard" => "Guard",
                        "labor" => "Labor", "iq" => "IQ",
                        "infirmary" => "Infirmary Time", "dungeon" => "Dungeon Time",
                        "primary_currency" => "Copper Coins", "secondary_currency"
                    => "Chivalry Tokens", "crimexp" => "Experience", "vip_days" =>
                        "VIP Days", "luck" => "Luck", "premium_currency" => "Mutton");
                $statformatted = $stats["{$einfo['stat']}"];
                echo "
				<tr>
					<th>
						Item Effect #{$enum}
					</th>
					<td>
					{$einfo['dir']} {$statformatted} by " . number_format($einfo['inc_amount']) . "{$einfo['inc_type']}.
					</td>
				</tr>";
            }
        }
		if ($id['ammo']) {
            echo "<tr>
				<th width='33%'>
					Required Ammo
				</th>
				<td>
					<a href='?ID={$id['ammo']}'>" . $api->SystemItemIDtoName($id['ammo']) . "</a>
				</td>
			</tr>";
        }
        if ($id['weapon']) {
			if ($_GET['ID'] == 235)
				$id['weapon']=($id['weapon']*0.25)*$ir['level'];
            echo "<tr>
				<th width='33%'>
					Item Weapon Rating
				</th>
				<td>
					" . number_format($id['weapon']) . "
				</td>
			</tr>";
        }
        if ($id['armor']) {
            echo "<tr>
				<th width='33%'>
					Item Armor Rating
				</th>
				<td>
					" . number_format($id['armor']) . "
				</td>
			</tr>";
        }
        echo "
		</table>";
        $db->free_result($q);
    }
}
$h->endpage();