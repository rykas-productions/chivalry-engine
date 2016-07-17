<?php
require("globals.php");
//$tq= $db->query("SELECT `itmtypename`,`itmtype` FR");
$inv = $db->query("SELECT `inv_qty`, `itmsellprice`, `itmid`, `inv_id`,
                 `effect1_on`, `effect2_on`, `effect3_on`, `itmname`,
                 `weapon`, `armor`, `itmtypename`, `itmdesc`, `itmtypeid`
                 FROM `inventory` AS `iv`
                 INNER JOIN `items` AS `i`
                 ON `iv`.`inv_itemid` = `i`.`itmid`
                 INNER JOIN `itemtypes` AS `it`
                 ON `i`.`itmtype` = `it`.`itmtypeid`
                 WHERE `iv`.`inv_userid` = {$userid}
                 ORDER BY `i`.`itmtype` ASC, `i`.`itmname` ASC");
if ($db->num_rows($inv) == 0)
{
    echo "<div class='alert alert-info'> <strong>No Items Found!</strong> By the looks of it, you have no items. You can get items by exploring around in {$set['WebsiteName']}, or buying them from shops.</div>";
}
else
{
	$lt='';
	echo "<b>Your items are listed below.</b><br />
	<div class='container'>
		<ul class='nav nav-tabs'>";
		while ($tab = $db->fetch_row($inv))
		{
			$lt = $tab['itmtypename'];
			echo "	<li>
						<a data-toggle='tab' href='#{$tab['itmtypeid']}'>$lt</a>
					</li>";
					
		}
		echo "</ul></div>";
		while ($tab = $db->fetch_row($inv))
		{
			echo "tetast";
			echo "<div class='tab-content'>
		<div id='#{$tab['itmtypeid']}' class='tab-pane fade'>
		<table class='table table-bordered'>
			<tr>
				<th>
					Item Name x Qty
				</th>
				<th>
					Item Cost (Total)
				</th>
				<th>
					Actions
				</th>
			</tr>";
			if ($i['weapon'])
        {
            $i['itmname'] =
                    "{$i['itmname']} <span style='color:red;'>*</span>";
        }
        if ($i['armor'])
        {
            $i['itmname'] =
                    "{$i['itmname']} <span style='color:green;'>*</span>";
        }
        echo "<tr>
        		<td><a href='iteminfo.php?ID={$i['itmid']}' data-toggle='tooltip' title='{$i['itmdesc']}'>{$i['itmname']}</a>";
        if ($i['inv_qty'] > 1)
        {
            echo "&nbsp;x{$i['inv_qty']}";
        }
        echo "</td>
        	  <td>" . number_format($i['itmsellprice']) . " (" . number_format($i['itmsellprice'] * $i['inv_qty']) . ") </td>
        	  <td>
        	  	[<a href='itemsend.php?ID={$i['inv_id']}'>Send</a>]
        	  	[<a href='itemsell.php?ID={$i['inv_id']}'>Sell</a>]
        	  	[<a href='imadd.php?ID={$i['inv_id']}'>Add To Market</a>]";
        if ($i['effect1_on'] || $i['effect2_on'] || $i['effect3_on'])
        {
            echo " [<a href='itemuse.php?ID={$i['inv_id']}'>Use</a>]";
        }
        if ($i['weapon'] > 0)
        {
            echo " [<a href='equip_weapon.php?ID={$i['inv_id']}'>Equip as Weapon</a>]";
        }
        if ($i['armor'] > 0)
        {
            echo " [<a href='equip_armor.php?ID={$i['inv_id']}'>Equip as Armor</a>]";
        }
        echo "</td>
        </tr>";
    }
    echo "</table></div></div>";
    $db->free_result($inv);
}

$h->endpage();