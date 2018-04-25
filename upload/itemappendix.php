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
    [<a href='?view=all'>All</a>] [<a href='?view=weapon'>Weapons Only</a>] [<a href='?view=armor'>Armors Only</a>] [<a href='?view=badge'>Badges Only</a>]";
if (!isset($_GET['view']))
    $_GET['view'] = '';
if ($_GET['view'] == 'weapon') {
    //Select all the in-game weapons
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `weapon` != 0 AND `itmbuyable` = 'true' ORDER BY `weapon` ASC");
} elseif ($_GET['view'] == 'armor') {
    //Select all the in-game armor
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `armor` != 0 AND `itmbuyable` = 'true' ORDER BY `armor` ASC");
} elseif ($_GET['view'] == 'badge') {
    //Select all the in-game armor
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmtype` = 13 AND `itmbuyable` = 'true' ORDER BY `itmname` ASC");
} else {
    //Select all the game items.
    $q = $db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmbuyable` = 'true' ORDER BY `itmname` ASC");
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