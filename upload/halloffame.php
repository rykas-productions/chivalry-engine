<?php
/*
	File:		halloffame.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Shows the top 20 players in varying categories.
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
require('globals.php');
echo "<h3>Hall of Fame</h3><hr />";
//Add stats to this array.
$StatArray = array('total', 'level', 'strength', 'agility', 'guard', 'labor', 'iq',
    'primary_currency', 'mining_level', 'secondary_currency');
//Stat is not chosen, set to level.
if (!isset($_GET['stat'])) {
    $_GET['stat'] = 'level';
}
//Stat chosen is not a valid stat.
if (!in_array($_GET['stat'], $StatArray)) {
    $_GET['stat'] = 'level';
}
//Sanitize and escape the GET.
$_GET['stat'] = $db->escape(strip_tags(stripslashes($_GET['stat'])));
//The GET wants user's total stats ranked.
if ($_GET['stat'] == 'total') {
    $q = $db->query("SELECT `u`.*, `us`.*
                    FROM `users` `u` 
                    INNER JOIN `userstats` AS `us`
                    ON `u`.`userid` = `us`.`userid`
                    WHERE `user_level` != 'Admin' AND `user_level` != 'NPC'
                    ORDER BY (`strength` + `agility` + `guard` + `labor` + `iq`) DESC
                    LIMIT 20");
} //The GET wants mining levels ranked.
elseif ($_GET['stat'] == 'mining_level') {
    $q = $db->query("SELECT `u`.*, `m`.*
                    FROM `users` `u` 
                    INNER JOIN `mining` AS `m`
                    ON `u`.`userid` = `m`.`userid`
					WHERE `user_level` != 'Admin' AND `user_level` != 'NPC'
                    ORDER BY `mining_level` DESC
                    LIMIT 20");
} //GET wants anything else ranked.
else {
    $q = $db->query("SELECT `u`.*, `us`.*
                    FROM `users` `u` 
                    INNER JOIN `userstats` AS `us`
                    ON `u`.`userid` = `us`.`userid`
                    WHERE `user_level` != 'Admin' AND `user_level` != 'NPC'
                    ORDER BY `{$_GET['stat']}` DESC
                    LIMIT 20");
}
echo "<a href='?stat=level'>Level</a>
        || <a href='?stat=primary_currency'>" . constant("primary_currency") . "</a>
        || <a href='?stat=secondary_currency'>" . constant("secondary_currency") . "</a>
		|| <a href='?stat=mining_level'>Mining Level</a>";
echo "<br />";
echo "<a href='?stat=strength'>" . constant("stat_strength") . "</a>
		|| <a href='?stat=agility'>" . constant("stat_agility") . "</a>
        || <a href='?stat=guard'>" . constant("stat_guard") . "</a>
        || <a href='?stat=labor'>" . constant("stat_labor") . "</a>
		|| <a href='?stat=iq'>" . constant("stat_iq") . "</a>
        || <a href='?stat=total'>Total Stats</a>";
echo "<br />Listing the 20 players with the highest {$_GET['stat']}.";
echo "<table class='table table-bordered'>
<tr>
    <th width='10%'>
        Rank
    </th>
    <th width='45%'>
        User
    </th>";
if ($_GET['stat'] == 'level' || $_GET['stat'] == 'primary_currency' || $_GET['stat'] == 'secondary_currency'
    || $_GET['stat'] == 'mining_level'
) {
    echo "<th width='45%'>
                Value
               </th>";
}
echo "
</tr>";
$rank = 1;
//Loop through the top 20 users.
while ($r = $db->fetch_row($q)) {
    echo "
    <tr>
        <td>
            {$rank}
        </td>
        <td>
            <a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
        </td>";
    if ($_GET['stat'] == 'level' || $_GET['stat'] == 'primary_currency' || $_GET['stat'] == 'secondary_currency'
        || $_GET['stat'] == 'mining_level'
    ) {
        echo "<td>
                    " . number_format($r[$_GET['stat']]) . "
                   </td>";
    }
    echo "
    </tr>";
    $rank++;
}
echo "</table>";
$h->endpage();