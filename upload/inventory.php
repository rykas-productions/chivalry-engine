<?php
/*
	File:		inventory.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows the player to view their items, and perform 
				varying actions with them.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
	MIT License

	Copyright (c) 2019 TheMasterGeneral

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
*/
require("globals.php");
echo "<h3>Your Equipment</h3><hr />
<div class='row'>
	<div class='col-sm-4'>
		<div class='card'>
			<div class='card-header'>
				Primary Weapon ";
if (!empty($ir['equip_primary'])) {
    echo "(<a href='unequip.php?type=equip_primary'>Unequip</a>)";
}
echo "
			</div>
			<div class='card-body'>";
if (!empty($ir['equip_primary'])) {
    echo $api->game->getItemNameFromID($ir['equip_primary']);
} else {
    echo "No Weapon";
}
echo "
			</div>
		</div>
	</div>";
echo "
	<div class='col-sm-4'>
		<div class='card'>
			<div class='card-header'>
				Secondary Weapon ";
if (!empty($ir['equip_secondary'])) {
    echo "(<a href='unequip.php?type=equip_secondary'>Unequip</a>)";
}
echo "
			</div>
			<div class='card-body'>";
if (!empty($ir['equip_secondary'])) {
    echo $api->game->getItemNameFromID($ir['equip_secondary']);
} else {
    echo "No Weapon";
}
echo "
			</div>
		</div>
	</div>";
echo "
	<div class='col-sm-4'>
		<div class='card'>
			<div class='card-header'>
				Armor ";
if (!empty($ir['equip_armor'])) {
    echo "(<a href='unequip.php?type=equip_armor'>Unequip</a>)";
}
echo "
			</div>
			<div class='card-body'>";
if (!empty($ir['equip_armor'])) {
    echo $api->game->getItemNameFromID($ir['equip_armor']);
} else {
    echo "No Armor";
}
echo "
			</div>
		</div>
	</div>
</div>";
echo "<hr />
<h3>Your Inventory</h3><hr />";
$inv =
    $db->query(
        "SELECT `inv_qty`, `itmsellprice`, `itmid`, `inv_id`,
                 `itmeffects_toggle`,
                 `weapon`, `armor`, `itmtypename`, `itmdesc`
                 FROM `inventory` AS `iv`
                 INNER JOIN `items` AS `i`
                 ON `iv`.`inv_itemid` = `i`.`itmid`
                 INNER JOIN `itemtypes` AS `it`
                 ON `i`.`itmtype` = `it`.`itmtypeid`
                 WHERE `iv`.`inv_userid` = {$userid}
                 ORDER BY `i`.`itmtype` ASC, `i`.`itmname` ASC");
echo "<b>Your items are listed below.</b><br />
<div class='cotainer'>
<div class='row'>
		<div class='col-sm'>
		    <h4>Item (Quantity)</h4>
		</div>
		<div class='col-sm'>
		    <h4>Cost (total)</h4>
		</div>
		<div class='col-sm'>
		    <h4>Actions</h4>
		</div>
</div><hr />";
$lt = "";
while ($i = $db->fetch_row($inv)) {
    if ($lt != $i['itmtypename']) {
        $lt = $i['itmtypename'];
        echo "<div class='row'>
		<div class='col-sm'>
		    <h3>{$lt}</h3>
		</div>
</div><hr />";
    }
    $i['itmdesc'] = htmlentities($i['itmdesc'], ENT_QUOTES);
    echo "<div class='row'>
        		<div class='col-sm'>
					<a href='iteminfo.php?ID={$i['itmid']}' data-toggle='tooltip' data-placement='right' title='{$i['itmdesc']}'>
						{$api->game->getItemNameFromID($i['itmid'])}
					</a>";
    if ($i['inv_qty'] > 1) {
        echo " (" . number_format($i['inv_qty']) . ")";
    }
    echo "</div>
        	  <div class='col-sm'>" . number_format($i['itmsellprice']);
    echo "  (" . number_format($i['itmsellprice'] * $i['inv_qty']) . ")";
    echo "</div>
        	  <div class='col-sm'>
        	  	[<a href='itemsend.php?ID={$i['inv_id']}'>Send</a>]
        	  	[<a href='itemsell.php?ID={$i['inv_id']}'>Sell</a>]";
    if (array_sum(json_decode($i['itmeffects_toggle'])) > 0) {
        echo " [<a href='itemuse.php?item={$i['inv_id']}'>Use</a>]";
    }
    if ($i['weapon'] > 0) {
        echo " [<a href='equip.php?slot=weapon&ID={$i['inv_id']}'>Equip Weapon</a>]";
    }
    if ($i['armor'] > 0) {
        echo " [<a href='equip.php?slot=armor&ID={$i['inv_id']}'>Equip Armor</a>]";
    }
    echo "</div>
        </div>
        <hr />";
}
echo "</div>";
$db->free_result($inv);
$h->endpage();
