<?php
/*
	File:		itemappendix.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Shows a list of all in-game items, along with the quantity 
				in-game.
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
echo "<h3>Item Appendix</h3><hr />This page lists all the items in the game, along with how many are in circulation.
    This may be useful for players who do item flipping, or those who are just plain old curious. Hovering over the
    item will give you its description. Tapping its name will take you to its info page<hr />
    [<a href='?view=all'>All</a>][<a href='?view=weapon'>Weapons Only</a>][<a href='?view=armor'>Armors Only</a>]";
if (!isset($_GET['view']))
    $_GET['view'] = '';
if ($_GET['view'] == 'weapon') {
    //Select all the in-game weapons
    $q = $db->query("SELECT * FROM `items` WHERE `weapon` != 0 ORDER BY `weapon` ASC");
} elseif ($_GET['view'] == 'armor') {
    //Select all the in-game armor
    $q = $db->query("SELECT * FROM `items` WHERE `armor` != 0 ORDER BY `armor` ASC");
} else {
    //Select all the game items.
    $q = $db->query("SELECT * FROM `items` ORDER BY `itmname` ASC");
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
while ($r = $db->fetch_row($q)) {
    //Select game item count. This only accounts for items in an user's inventory. Nothing else.
    $q2 = $db->query("SELECT SUM(`inv_qty`) FROM `inventory` WHERE `inv_itemid` = {$r['itmid']}");
    $r2 = $db->fetch_single();
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
echo "</table>";
$h->endpage();