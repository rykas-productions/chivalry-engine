<?php
/*
	File:		halloffame.php
	Created: 	6/23/2017 at 12:18AM Eastern Time
	Info: 		Lists the top 20 users based on input.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
$hofCount=getCurrentUserPref('hofView',20);
echo "<h3><i class='game-icon game-icon-crown'></i> Hall of Fame</h3><hr />";
//Add stats to this array.
$StatArray = array('total', 'level', 'strength', 'agility', 'guard', 'labor', 'iq',
    'primary_currency', 'mining_level', 'secondary_currency', 'busts', 'kills', 'deaths', 'richest', 'farm_level');
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
    $q = $db->query("/*qc=on*/SELECT `u`.*, `us`.*
                    FROM `users` `u` 
                    INNER JOIN `userstats` AS `us`
                    ON `u`.`userid` = `us`.`userid`
                    WHERE `user_level` != 'Admin' AND `user_level` != 'NPC' AND `fedjail` = 0
                    ORDER BY (`strength` + `agility` + `guard` + `labor` + `iq`) DESC
                    LIMIT {$hofCount}");
} //The GET wants mining levels ranked.
elseif ($_GET['stat'] == 'mining_level') {
    $q = $db->query("/*qc=on*/SELECT `u`.*, `m`.*
                    FROM `users` `u` 
                    INNER JOIN `mining` AS `m`
                    ON `u`.`userid` = `m`.`userid`
					WHERE `user_level` != 'Admin' AND `user_level` != 'NPC' AND `fedjail` = 0
                    ORDER BY `mining_level` DESC
                    LIMIT {$hofCount}");
}
elseif ($_GET['stat'] == 'farm_level') 
{
    $q = $db->query("/*qc=on*/SELECT `u`.*, `f`.*
                    FROM `users` `u` 
                    INNER JOIN `farm_users` AS `f`
                    ON `u`.`userid` = `f`.`userid`
					WHERE `user_level` != 'Admin' AND `user_level` != 'NPC' AND `fedjail` = 0
                    ORDER BY `farm_level` DESC
                    LIMIT {$hofCount}");
}
elseif ($_GET['stat'] == 'richest')
{
	$q = $db->query("/*qc=on*/SELECT `u`.*, `us`.*
                    FROM `users` `u` 
                    INNER JOIN `userstats` AS `us`
                    ON `u`.`userid` = `us`.`userid`
                    WHERE `user_level` != 'Admin' AND `user_level` != 'NPC' AND `fedjail` = 0");
}
 //GET wants anything else ranked.
else {
    $q = $db->query("/*qc=on*/SELECT `u`.*, `us`.*
                    FROM `users` `u` 
                    INNER JOIN `userstats` AS `us`
                    ON `u`.`userid` = `us`.`userid`
                    WHERE `user_level` != 'Admin' AND `user_level` != 'NPC' AND `fedjail` = 0
                    ORDER BY `{$_GET['stat']}` DESC
                    LIMIT {$hofCount}");
}
echo "<a href='?stat=level'>Level</a>
        || <a href='?stat=primary_currency'>Copper Coins</a>
        || <a href='?stat=secondary_currency'>Chivalry Tokens</a>
		|| <a href='?stat=mining_level'>Mining Level</a>
		|| <a href='?stat=busts'>Busts</a>
		|| <a href='?stat=kills'>Kills</a>
		|| <a href='?stat=deaths'>Deaths</a>";
echo "<br />";
echo "<a href='?stat=farm_level'>Farming Level</a>
		|| <a href='?stat=strength'>Strength</a>
		|| <a href='?stat=agility'>Agility</a>
        || <a href='?stat=guard'>Guard</a>
        || <a href='?stat=labor'>Labor</a>
		|| <a href='?stat=iq'>IQ</a>
        || <a href='?stat=total'>Total Stats</a>";
echo "<br />Listing the {$hofCount} players with the highest {$_GET['stat']}.";
echo "<table class='table table-bordered'>
<tr>
    <th width='10%'>
        Rank
    </th>
    <th width='45%'>
        User
    </th>";
if ($_GET['stat'] == 'level' || $_GET['stat'] == 'primary_currency' || $_GET['stat'] == 'secondary_currency'
    || $_GET['stat'] == 'mining_level' || $_GET['stat'] == 'busts' || $_GET['stat'] == 'kills'
    || $_GET['stat'] == 'deaths' || $_GET['stat'] == 'richest' || $_GET['stat'] == 'farm_level'
) {
    echo "<th width='45%'>
                Value
               </th>";
}
echo "
</tr>";
$rank = 1;
if ($_GET['stat'] != 'crypto')
{
    //Loop through the top 20 users.
    while ($r = $db->fetch_row($q)) {
        echo "
        <tr>
            <td>
                {$rank}
            </td>
            <td>
                <a href='profile.php?user={$r['userid']}'>" . parseUsername($r['userid']) . "</a> [{$r['userid']}]
            </td>";
        if ($_GET['stat'] == 'level' || $_GET['stat'] == 'primary_currency' || $_GET['stat'] == 'secondary_currency'
            || $_GET['stat'] == 'mining_level' || $_GET['stat'] == 'busts' || $_GET['stat'] == 'kills'
            || $_GET['stat'] == 'deaths' || $_GET['stat'] == 'richest' || $_GET['stat'] == 'farm_level'
        ) {
            echo "<td>
                    " . number_format($r[$_GET['stat']]) . "
               </td>";
        }
        echo "
        </tr>";
        $rank++;
    }
}
echo "</table>";
$h->endpage();