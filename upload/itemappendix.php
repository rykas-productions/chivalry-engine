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
    item will give you its description. Tapping its name will take you to its info page<hr />
    [<a href='?view=weapon'>Weapons</a>] [<a href='?view=armor'>Armor</a>] [<a href='?view=vip'>VIP Items</a>] 
	[<a href='?view=infirmary'>Infirmary Items</a>] [<a href='?view=dungeon'>Dungeon Items</a>] [<a href='?view=material'>Materials</a>] [<a href='?view=food'>Food</a>] 
	[<a href='?view=potions'>Potions</a>] [<a href='?view=holiday'>Holiday Items</a>] [<a href='?view=scrolls'>Scrolls</a>] [<a href='?view=rings'>Rings</a>] 
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
    //Select all the in-game armor
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 3 AND `itmbuyable` = 'true' ORDER BY `itmname` ASC");
} elseif ($_GET['view'] == 'infirmary') {
    //Select all the in-game armor
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 4 AND `itmbuyable` = 'true' ORDER BY `itmname` ASC");
} elseif ($_GET['view'] == 'dungeon') {
    //Select all the in-game armor
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 5 AND `itmbuyable` = 'true' ORDER BY `itmname` ASC");
} 
elseif ($_GET['view'] == 'material') {
    //Select all the in-game armor
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 6 AND `itmbuyable` = 'true' ORDER BY `itmname` ASC");
}
elseif ($_GET['view'] == 'food') {
    //Select all the in-game armor
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 7 AND `itmbuyable` = 'true' ORDER BY `itmname` ASC");
}
elseif ($_GET['view'] == 'potions') {
    //Select all the in-game armor
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 8 AND `itmbuyable` = 'true' ORDER BY `itmname` ASC");
} 
elseif ($_GET['view'] == 'other') {
    //Select all the in-game armor
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 9 AND `itmbuyable` = 'true' ORDER BY `itmname` ASC");
} 
elseif ($_GET['view'] == 'holiday') {
    //Select all the in-game armor
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 10 AND `itmbuyable` = 'true' ORDER BY `itmname` ASC");
} 
elseif ($_GET['view'] == 'scrolls') {
    //Select all the in-game armor
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 11 AND `itmbuyable` = 'true' ORDER BY `itmname` ASC");
} 
elseif ($_GET['view'] == 'rings') {
    //Select all the in-game armor
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 12 AND `itmbuyable` = 'true' ORDER BY `itmname` ASC");
} elseif ($_GET['view'] == 'badge') {
    //Select all the in-game armor
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 13 AND `itmbuyable` = 'true' ORDER BY `itmname` ASC");
} else {
    alert('danger',"Uh Oh!","Please select a valid item category type.",true,'explore.php');
	die($h->endpage());
}
echo "
<table class='table table-bordered table-striped'>
    <tr>
        <th>
            Item Name
        </th>
        <th>
            Quantity in Circulation
        </th>
    </tr>";
while ($r = $db->fetch_row($q)) {
    //Select game item count. This only accounts for items in an user's inventory. Nothing else.
	$q2= $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`inv_qty`),
			(/*qc=on*/SELECT SUM(`imQTY`) FROM `itemmarket` WHERE `imITEM` = {$r['itmid']}), 
			(/*qc=on*/SELECT SUM(`gaQTY`) FROM `guild_armory` WHERE `gaITEM` = {$r['itmid']} AND `gaGUILD` != 1)
			FROM `inventory` WHERE `inv_itemid` = {$r['itmid']} AND `inv_userid` != 1"));
	$q3=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`equip_primary`),
			(/*qc=on*/SELECT COUNT(`equip_secondary`) FROM `users` WHERE `equip_secondary` = {$r['itmid']} AND `userid` != 1),
			(/*qc=on*/SELECT COUNT(`equip_armor`) FROM `users` WHERE `equip_armor` = {$r['itmid']} AND `userid` != 1),
			(/*qc=on*/SELECT COUNT(`equip_potion`) FROM `users` WHERE `equip_potion` = {$r['itmid']} AND `userid` != 1),
			(/*qc=on*/SELECT COUNT(`equip_badge`) FROM `users` WHERE `equip_badge` = {$r['itmid']} AND `userid` != 1)
			FROM `users` WHERE `equip_primary` = {$r['itmid']} AND `userid` != 1"));
	$total=$q2+$q3;
	$icon=returnIcon($r['itmid'],2);
	$r['itmdesc'] = htmlentities($r['itmdesc'], ENT_QUOTES);
    echo "
        <tr>
            <td>
				{$icon}<br /><a href='iteminfo.php?ID={$r['itmid']}' data-toggle='tooltip' data-placement='right' title='{$r['itmdesc']}'>
                    {$r['itmname']}
                </a>
            </td>
            <td>
                " . number_format($total) . "
            </td>
        </tr>";
}
echo "</table>";
$h->endpage();