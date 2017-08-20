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
if (!$itmid)
{
    echo 'Invalid item ID';
}
else
{
    $q =
            $db->query(
                    "SELECT `i`.*, `itmtypename`
                     FROM `items` AS `i`
                     INNER JOIN `itemtypes` AS `it`
                     ON `i`.`itmtype` = `it`.`itmtypeid`
                     WHERE `i`.`itmid` = {$itmid}
                     LIMIT 1");
    if ($db->num_rows($q) == 0)
    {
        echo 'Invalid item ID';
    }
    else
    {
        $id = $db->fetch_row($q);
		echo "
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					{$lang['ITEM_INFO_LUIF']} {$id['itmname']}
				</th>
			</tr>
			<tr>
				<th width='33%'>
					{$lang['ITEM_INFO_ITEM']} {$lang['ITEM_INFO_TYPE']}
				</th>
				<td>
					{$id['itmtypename']}
				</td>
			</tr>
			<tr>
				<th width='33%'>
					{$lang['ITEM_INFO_ITEM']} {$lang['ITEM_INFO_INFO']}
				</th>
				<td>
					{$id['itmdesc']}
				</td>
			</tr>
			<tr>
				<th width='33%'>
					{$lang['ITEM_INFO_ITEM']} {$lang['ITEM_INFO_BPRICE']}
				</th>
				<td>";
					if ($id['itmbuyprice'] > 0)
					{
						echo number_format($id['itmbuyprice']);
					}
					else
					{
						echo "{$lang['ITEM_INFO_BPRICE_NO']}";
					}
        echo"
				</td>
			</tr>
			<tr>
				<th width='33%'>
					{$lang['ITEM_INFO_ITEM']} {$lang['ITEM_INFO_INFO']}
				</th>
				<td>";
					if ($id['itmsellprice'])
					{
						echo number_format($id['itmsellprice']);
					}
					else
					{
						echo "{$lang['ITEM_INFO_SPRICE_NO']}";
					}
					echo"
				</td>
			</tr>";
		for ($enum = 1; $enum <= 3; $enum++)
        {
			if ($id["effect{$enum}_on"] == 'true')
            {
				$einfo = unserialize($id["effect{$enum}"]);
				$einfo['inc_type'] = ($einfo['inc_type'] == 'percent') ? '%' : '';
				$einfo['dir'] = ($einfo['dir'] == 'pos') ? 'Increases' : 'Decreases';
				$stats =
					array("energy" => "{$lang['INDEX_ENERGY']}", "will" => "{$lang['INDEX_WILL']}", 
							"brave" => "{$lang['INDEX_BRAVE']}",
							"hp" => "{$lang['INDEX_HP']}", "strength" => "{$lang['GEN_STR']}",
							"agility" => "{$lang['GEN_AGL']}", "guard" => "{$lang['GEN_GRD']}",
							"labor" => "{$lang['GEN_LAB']}", "iq" => "{$lang['GEN_IQ']}",
							"infirmary" => "{$lang['STAFF_CITEM_TH12_1']}", "dungeon" => "{$lang['STAFF_CITEM_TH12_2']}",
							"primary_currency" => "{$lang['INDEX_PRIMCURR']}", "secondary_currency" 
							=> "{$lang['INDEX_SECCURR']}", "crimexp" => "{$lang['INDEX_EXP']}", "vip_days" => 
							"{$lang['INDEX_VIP']}");
				$statformatted=$stats["{$einfo['stat']}"];
				echo "
				<tr>
					<th>
						{$lang['ITEM_INFO_EFFECT']}{$enum}
					</th>
					<td>
					{$einfo['dir']} {$statformatted} {$lang['ITEM_INFO_BY']} {$einfo['inc_amount']}{$einfo['inc_type']}.
					</td>
				</tr>";
			}
		}
		if ($id['weapon'])
		{
			echo "<tr>
				<th width='33%'>
					{$lang['ITEM_INFO_ITEM']} {$lang['ITEM_INFO_WEAPON_HURT']}
				</th>
				<td>
					" . number_format($id['weapon']) . "
				</td>
			</tr>";
		}
		if ($id['armor'])
		{
			echo "<tr>
				<th width='33%'>
					{$lang['ITEM_INFO_ITEM']} {$lang['ITEM_INFO_ARMOR_HURT']}
				</th>
				<td>
					" . number_format($id['armor']) . "
				</td>
			</tr>";
		}
		echo"
		</table>";
    $db->free_result($q);
	}
}
$h->endpage();