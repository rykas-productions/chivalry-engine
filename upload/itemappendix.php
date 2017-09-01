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
echo "<h3>Item Appendix</h3><hr />This page lists all the items in the game, along with how many are in circulation.
    This may be useful for players who do item flipping, or those who are just plain old curious. Hovering over the
    item will give you its description. Tapping its name will take you to its info page<hr />
    [<a href='?view=all'>All</a>][<a href='?view=weapon'>Weapons Only</a>][<a href='?view=armor'>Armors Only</a>]";
if (!isset($_GET['view']))
    $_GET['view']='';
if ($_GET['view'] == 'weapon')
{
    //Select all the in-game weapons
    $q=$db->query("SELECT * FROM `items` WHERE `weapon` != 0 ORDER BY `weapon` ASC");
}
elseif ($_GET['view'] == 'armor')
{
    //Select all the in-game armor
    $q=$db->query("SELECT * FROM `items` WHERE `armor` != 0 ORDER BY `armor` ASC");
}
else
{
    //Select all the game items.
    $q=$db->query("SELECT * FROM `items` ORDER BY `itmname` ASC");
}
echo "
<table class='table table-bordered'>
    <tr>
        <th>
            Item Name
        </th>
        <th>
            Quantity in Circulation
        </th>
    </tr>";
while ($r = $db->fetch_row($q))
{
    //Select game item count. This only accounts for items in an user's inventory. Nothing else.
    $q2=$db->query("SELECT SUM(`inv_qty`) FROM `inventory` WHERE `inv_itemid` = {$r['itmid']}");
    $r2=$db->fetch_single();
    echo "
        <tr>
            <td>
                <a href='iteminfo.php?ID={$r['itmid']}' data-toggle='tooltip' data-placement='right' title='{$r['itmdesc']}'>
                    {$r['itmname']}
                </a>
            </td>
            <td>
                " . number_format($r2) . "
            </td>
        </tr>";
}
echo"</table>";
$h->endpage();