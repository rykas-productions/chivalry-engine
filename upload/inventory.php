<?php
require("globals.php");
$q =
        $db->query(
                "SELECT `itmid`, `itmname`
                 FROM `items`
                 WHERE `itmid`
                  IN({$ir['equip_primary']}, {$ir['equip_secondary']},
                     {$ir['equip_armor']})");
echo "<h3>Equipped Items</h3><hr />";
$equip = array();
while ($r = $db->fetch_row($q))
{
    $equip[$r['itmid']] = $r;
}
$db->free_result($q);
echo"
<div class='row'>
	<div class='col-sm-4'>
		<div class='panel panel-default'>
			<div class='panel-heading'>
				Primary Weapon ";
				if (isset($equip[$ir['equip_primary']]))
				{
					echo "(<a href='unequip.php?type=equip_primary'>Unequip {$equip[$ir['equip_primary']]['itmname']}</a>)";
				}
				echo"
			</div>
			<div class='panel-body'>";
				if (isset($equip[$ir['equip_primary']]))
				{
					echo $equip[$ir['equip_primary']]['itmname'];
				}
				else
				{
					echo "You do not have a primary weapon equipped.";
				}
				echo"
			</div>
		</div>
	</div>";
	echo"
	<div class='col-sm-4'>
		<div class='panel panel-default'>
			<div class='panel-heading'>
				Secondary Weapon ";
				if (isset($equip[$ir['equip_secondary']]))
				{
					echo "(<a href='unequip.php?type=equip_secondary'>Unequip {$equip[$ir['equip_secondary']]['itmname']}</a>)";
				}
				echo"
			</div>
			<div class='panel-body'>";
				if (isset($equip[$ir['equip_secondary']]))
				{
					echo $equip[$ir['equip_secondary']]['itmname'];
				}
				else
				{
					echo "You do not have a secondary weapon equipped.";
				}
				echo"
			</div>
		</div>
	</div>";
	echo"
	<div class='col-sm-4'>
		<div class='panel panel-default'>
			<div class='panel-heading'>
				Armor ";
				if (isset($equip[$ir['equip_armor']]))
				{
					echo "(<a href='unequip.php?type=equip_armor'>Unequip {$equip[$ir['equip_armor']]['itmname']}</a>)";
				}
				echo"
			</div>
			<div class='panel-body'>";
				if (isset($equip[$ir['equip_armor']]))
				{
					echo $equip[$ir['equip_armor']]['itmname'];
				}
				else
				{
					echo "You do not have armor equipped.";
				}
				echo"
			</div>
		</div>
	</div>
</div>";
echo"<hr />
<h3>Inventory</h3><hr />";
$inv =
        $db->query(
                "SELECT `inv_qty`, `itmsellprice`, `itmid`, `inv_id`,
                 `effect1_on`, `effect2_on`, `effect3_on`, `itmname`,
                 `weapon`, `armor`, `itmtypename`, `itmdesc`
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
    echo "<b>Your items are listed below.</b><br />
<table class='table table-bordered table-hover table-striped'>
	<tr>
		<th>Item Name x Qty</th>
		<th>Item Cost (Total)</th>
		<th>Actions</th>
	</tr>";
    $lt = "";
    while ($i = $db->fetch_row($inv))
    {
        if ($lt != $i['itmtypename'])
        {
            $lt = $i['itmtypename'];
            echo "\n<tr>
            			<th colspan='4'>
            				<b>{$lt}</b>
            			</th>
            		</tr>";
        }
        if ($i['weapon'])
        {
            $i['itmname'] =
                    "<span style='color: red;'>*</span>" . $i['itmname'];
        }
        if ($i['armor'])
        {
            $i['itmname'] =
                    "<span style='color: green;'>*</span>" . $i['itmname'];
        }
        echo "<tr>
        		<td><a href='iteminfo.php?ID={$i['itmid']}' data-toggle='tooltip'"; ?> title="<?php echo $r['itmdesc']; ?>" <?php echo ">{$i['itmname']}</a>";
        if ($i['inv_qty'] > 1)
        {
            echo " (x{$i['inv_qty']})";
        }
        echo "</td>
        	  <td>" . number_format($i['itmsellprice']);  
			  echo "  (" . number_format($i['itmsellprice'] * $i['inv_qty']) . ")";
			  echo"</td>
        	  <td>
        	  	[<a href='itemsend.php?ID={$i['inv_id']}'>Send</a>]
        	  	[<a href='itemsell.php?ID={$i['inv_id']}'>Sell</a>]
        	  	[<a href='itemmarket.php?action=add&ID={$i['inv_id']}'>Add To Market</a>]";
        if ($i['effect1_on'] || $i['effect2_on'] || $i['effect3_on'])
        {
            echo " [<a href='itemuse.php?item={$i['inv_id']}'>Use</a>]";
        }
        if ($i['weapon'] > 0)
        {
            echo " [<a href='equip.php?slot=weapon&ID={$i['inv_id']}'>Equip Weapon</a>]";
        }
        if ($i['armor'] > 0)
        {
            echo " [<a href='equip.php?slot=armor&ID={$i['inv_id']}'>Equip Armor</a>]";
        }
        echo "</td>
        </tr>";
    }
    echo "</table>";
    $db->free_result($inv);
    echo "<small><b>NB:</b> Items with a small red </small><span style='color: red;'>*</span><small> next to their name can be used as weapons in combat.<br />
Items with a small green </small><span style='color: green;'>*</span><small> next to their name can be used as armor in combat.</small>";
}
$h->endpage();
