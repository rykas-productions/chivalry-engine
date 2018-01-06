<?php
/*
	File:		inventory.php
	Created: 	4/5/2016 at 12:14AM Eastern Time
	Info: 		Displays the player's items and equipment, along with
				actions that you can do with the items.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
$tresder = (Random(100, 999));
echo "<h3>Your Equipment</h3><hr />
<div class='row'>
	<div class='col-sm-4'>
		<div class='card'>
			<div class='card-header'>
				" . returnIcon($ir['equip_primary']) . " Primary Weapon ";
if (!empty($ir['equip_primary'])) {
    echo "(<a href='unequip.php?type=equip_primary'>Unequip</a>)";
}
echo "
			</div>
			<div class='card-body'>";
if (!empty($ir['equip_primary'])) {
	$dam=$db->fetch_single($db->query("SELECT `weapon` FROM `items` WHERE `itmid` = {$ir['equip_primary']}"));
    echo "<a href='#' data-toggle='tooltip' data-placement='right' title='Weapon Rating: {$dam}'>{$api->SystemItemIDtoName($ir['equip_primary'])}</a>";
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
				" . returnIcon($ir['equip_secondary']) . " Secondary Weapon ";
if (!empty($ir['equip_secondary'])) {
    echo "(<a href='unequip.php?type=equip_secondary'>Unequip</a>)";
}
echo "
			</div>
			<div class='card-body'>";
if (!empty($ir['equip_secondary'])) {
	$dam2=$db->fetch_single($db->query("SELECT `weapon` FROM `items` WHERE `itmid` = {$ir['equip_secondary']}"));
    echo "<a href='#' data-toggle='tooltip' data-placement='right' title='Weapon Rating: {$dam2}'>{$api->SystemItemIDtoName($ir['equip_secondary'])}</a>";
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
				" . returnIcon($ir['equip_armor']) . " Armor ";
if (!empty($ir['equip_armor'])) {
    echo "(<a href='unequip.php?type=equip_armor'>Unequip</a>)";
}
echo "
			</div>
			<div class='card-body'>";
if (!empty($ir['equip_armor'])) {
	$armor=$db->fetch_single($db->query("SELECT `armor` FROM `items` WHERE `itmid` = {$ir['equip_armor']}"));
    echo "<a href='#' data-toggle='tooltip' data-placement='right' title='Armor Rating: {$armor}'>{$api->SystemItemIDtoName($ir['equip_armor'])}</a>";
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
                 `effect1_on`, `effect2_on`, `effect3_on`,
                 `weapon`, `armor`, `itmtypename`, `itmdesc`, `icon`
                 FROM `inventory` AS `iv`
                 INNER JOIN `items` AS `i`
                 ON `iv`.`inv_itemid` = `i`.`itmid`
                 INNER JOIN `itemtypes` AS `it`
                 ON `i`.`itmtype` = `it`.`itmtypeid`
                 WHERE `iv`.`inv_userid` = {$userid}
                 ORDER BY `i`.`itmtype` ASC, `i`.`itmname` ASC");
echo "<b>Your items are listed below.</b><br />
	<table class='table table-bordered table-striped'>
	    <thead>
		<tr>
			<th>Item (Qty)</th>
			<th class='hidden-xs-down'>Item Cost (Total)</th>
			<th>Links</th>
		</tr></thead>";
$lt = "";
while ($i = $db->fetch_row($inv)) {
    if ($lt != $i['itmtypename']) {
        $lt = $i['itmtypename'];
        echo "\n<thead><tr>
            			<th colspan='4'>
            				<b>{$lt}</b>
            			</th>
            		</tr></thead>";
    }
    $i['itmdesc'] = htmlentities($i['itmdesc'], ENT_QUOTES);
	$icon = returnIcon($i['itmid'],1.2);
    echo "<tr>
        		<td>
					{$icon} <a href='iteminfo.php?ID={$i['itmid']}' data-toggle='tooltip' data-placement='right' title='{$i['itmdesc']}'>
						{$api->SystemItemIDtoName($i['itmid'])}
					</a>";
    if ($i['inv_qty'] > 1) {
        echo " (" . number_format($i['inv_qty']) . ")";
    }
    echo "</td>
        	  <td class='hidden-xs-down'>" . number_format($i['itmsellprice']);
    echo "  (" . number_format($i['itmsellprice'] * $i['inv_qty']) . ")";
    echo "</td>
        	  <td>
        	  	[<a href='itemsend.php?ID={$i['inv_id']}'>Send</a>]
        	  	[<a href='itemsell.php?ID={$i['inv_id']}'>Sell</a>]";
    if (($i['effect1_on'] == 'true' || $i['effect2_on'] == 'true' || $i['effect3_on'] == 'true') && ($i['armor'] == 0 && $i['weapon'] == 0)) {
			echo " [<a href='itemuse.php?item={$i['inv_id']}'>Use</a>]";
    }
    //Bomb
    if ($i['itmid'] == 28)
    {
        echo " [<a href='bomb.php?action=small'>Set Charge</a>]";
    }
	if ($i['itmid'] == 61)
    {
        echo " [<a href='bomb.php?action=medium'>Set Charge</a>]";
    }
	if ($i['itmid'] == 62)
    {
        echo " [<a href='bomb.php?action=large'>Set Charge</a>]";
    }
    //Box of Random
    if ($i['itmid'] == 33)
    {
        echo " [<a href='bor.php?tresde={$tresder}'>Open</a>]";
		if ($ir['autobor'] > 0)
			echo " [<a href='autobor.php'>Auto</a>]";
    }
	//2017 Halloween Scratch Ticket
    if ($i['itmid'] == 63)
    {
        echo " [<a href='2017halloween.php?action=ticket'>Scratch</a>]";
    }
	//Invis Potion
    if ($i['itmid'] == 68)
    {
        echo " [<a href='invispotion.php'>Use</a>]";
    }
	//VIP Ticket
    if ($i['itmid'] == 89)
    {
        echo " [<a href='vipticket.php'>Scratch</a>]";
    }
	//Pumpkin
    if ($i['itmid'] == 64)
    {
        echo " [<a href='bomb.php?action=pumpkin'>Toss at Player</a>]";
    }
	//Thanksgiving Scratch Ticket
    if ($i['itmid'] == 69)
    {
        echo " [<a href='2017thanksgiving.php?action=ticket'>Scratch</a>]";
    }
	//Auto Hexbag Opener
    if ($i['itmid'] == 91)
    {
        echo " [<a href='vipitem.php?item=autohex'>Redeem</a>]";
    }
	//Auto Boxes of Random Opener
    if ($i['itmid'] == 92)
    {
        echo " [<a href='vipitem.php?item=autobor'>Redeem</a>]";
    }
    if ($i['weapon'] > 0) {
        echo " [<a href='equip.php?slot=weapon&ID={$i['inv_id']}'>Equip Weapon</a>]";
    }
    if ($i['armor'] > 0) {
        echo " [<a href='equip.php?slot=armor&ID={$i['inv_id']}'>Equip Armor</a>]";
    }
    echo "</td>
        </tr>";
}
echo "</table>";
$db->free_result($inv);
$h->endpage();
