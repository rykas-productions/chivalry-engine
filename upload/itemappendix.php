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
echo "<h3>{$lang['ID_TITLE']}</h3><hr />{$lang['ID_INFO']}<hr />";
//Select all the game items.
$q=$db->query("SELECT * FROM `items` ORDER BY `itmname` ASC");
echo "
<table class='table table-bordered'>
    <tr>
        <th>
            {$lang['ID_TH']}
        </th>
        <th>
            {$lang['ID_TH1']}
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