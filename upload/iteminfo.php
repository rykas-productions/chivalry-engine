<?php
/*
	File:		iteminfo.php
	Created: 	4/5/2016 at 12:14AM Eastern Time
	Info: 		Displays detailed information about the item inputted.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
$ID = filter_input(INPUT_GET, 'ID', FILTER_SANITIZE_NUMBER_INT) ?: 0;
$itmid = $ID;
if (!$itmid) {
    alert('danger', 'Uh Oh!', 'Invalid or non-existent Item ID.', true, 'inventory.php');
} else {
    $q =
        $db->query(
            "SELECT `i`.*, `itmtypename`
                     FROM `items` AS `i`
                     INNER JOIN `itemtypes` AS `it`
                     ON `i`.`itmtype` = `it`.`itmtypeid`
                     WHERE `i`.`itmid` = {$itmid}
                     LIMIT 1");
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
        $iterations=count(json_decode($id['itmeffects_toggle']));
        $toggle=json_decode($id['itmeffects_toggle']);
        $stat=json_decode($id['itmeffects_stat']);
        $dir=json_decode($id['itmeffects_dir']);
        $type=json_decode($id['itmeffects_type']);
        $amount=json_decode($id['itmeffects_amount']);
        $usecount=0;
        while ($usecount != $iterations)
        {
            if ($toggle[$usecount] == 1)
            {
                $type[$usecount] = ($type[$usecount] == 'percent') ? '%' : '';
                $dir[$usecount] = ($dir[$usecount] == 'pos') ? 'Increases' : 'Decreases';
                $stats =
                    array("energy" => "Energy", "will" => "Will",
                        "brave" => "Bravery", "level" => "Level",
                        "hp" => "Health", "strength" => "Strength",
                        "agility" => "Agility", "guard" => "Guard",
                        "labor" => "Labor", "iq" => "IQ",
                        "infirmary" => "Infirmary Time", "dungeon" => "Dungeon Time",
                        "primary_currency" => "{$_CONFIG['primary_currency']}", 
                        "secondary_currency" => "Secondary Currency", 
                        "xp" => "Experience", "vip_days" =>
                        "VIP Days");
                $statformatted = $stats["{$stat[$usecount]}"];
                 echo "
				<tr>
					<th>
						Item Effect
					</th>
					<td>
					{$dir[$usecount]} {$statformatted} by " . number_format($amount[$usecount]) . "{$type[$usecount]}.
					</td>
				</tr>";
            }
            $usecount=$usecount+1;
        }
        if ($id['weapon']) {
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