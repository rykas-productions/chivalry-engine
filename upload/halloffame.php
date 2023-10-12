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
    'primary_currency', 'mining_level', 'secondary_currency', 'busts', 'kills', 
    'deaths', 'richest', 'farm_level', 'profit', date('Y') . 'halloweenCandies');
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
if ($_GET['stat'] == 'total') 
{
    $q = $db->query("/*qc=on*/SELECT `u`.*, `us`.*
                    FROM `users` `u` 
                    INNER JOIN `userstats` AS `us`
                    ON `u`.`userid` = `us`.`userid`
                    WHERE `user_level` != 'Admin' AND `user_level` != 'NPC' AND `fedjail` = 0
                    ORDER BY (`strength` + `agility` + `guard` + `labor` + `iq`) DESC
                    LIMIT {$hofCount}");
} //The GET wants mining levels ranked.
elseif ($_GET['stat'] == 'mining_level') 
{
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
elseif ($_GET['stat'] == 'profit')
{
    $q = $db->query("/*qc=on*/SELECT `u`.*, `us`.*
                    FROM `users` `u`
                    INNER JOIN `asset_market_profit` AS `us`
                    ON `u`.`userid` = `us`.`userid`
                    WHERE `user_level` != 'Admin' AND `user_level` != 'NPC' AND `fedjail` = 0
                    ORDER BY `profit` DESC
                    LIMIT {$hofCount}");
}
elseif ($_GET['stat'] == date('Y') . 'halloweenCandies')
{
    $q = $db->query("/*qc=on*/SELECT `u`.*, `us`.*
                    FROM `users` `u`
                    INNER JOIN `user_pref` AS `us`
                    ON `u`.`userid` = `us`.`userid`
                    WHERE `user_level` != 'Admin' AND `user_level` != 'NPC' AND `fedjail` = 0
                    AND `preference` = '" . date('Y') . "halloweenCandies'
                    ORDER BY `value` DESC
                    LIMIT {$hofCount}");
}
 //GET wants anything else ranked.
else 
{
    $q = $db->query("/*qc=on*/SELECT `u`.*, `us`.*
                    FROM `users` `u` 
                    INNER JOIN `userstats` AS `us`
                    ON `u`.`userid` = `us`.`userid`
                    WHERE `user_level` != 'Admin' AND `user_level` != 'NPC' AND `fedjail` = 0
                    ORDER BY `{$_GET['stat']}` DESC
                    LIMIT {$hofCount}");
}
echo "<div class='row'>
    <div class='col-auto'>
        <a href='?stat=level' class='btn btn-primary btn-block'>Level</a><br />
    </div>
    <div class='col-auto'>
        <a href='?stat=primary_currency' class='btn btn-primary btn-block'>Copper Coins</a><br />
    </div>
    <div class='col-auto'>
        <a href='?stat=secondary_currency' class='btn btn-primary btn-block'>Chivalry Tokens</a><br />
    </div>
    <div class='col-auto'>
        <a href='?stat=mining_level' class='btn btn-primary btn-block'>Mining Level</a><br />
    </div>
    <div class='col-auto'>
        <a href='?stat=busts' class='btn btn-primary btn-block'>Busts</a><br />
    </div>
    <div class='col-auto'>
        <a href='?stat=kills' class='btn btn-primary btn-block'>Kills</a><br />
    </div>
    <div class='col-auto'>
        <a href='?stat=deaths' class='btn btn-primary btn-block'>Deaths</a><br />
    </div>
    <div class='col-auto'>
        <a href='?stat=farm_level' class='btn btn-primary btn-block'>Farm Level</a><br />
    </div>
    <div class='col-auto'>
        <a href='?stat=strength' class='btn btn-primary btn-block'>Strength</a><br />
    </div>
    <div class='col-auto'>
        <a href='?stat=agility' class='btn btn-primary btn-block'>Agility</a><br />
    </div>
    <div class='col-auto'>
        <a href='?stat=guard' class='btn btn-primary btn-block'>Guard</a><br />
    </div>
    <div class='col-auto'>
        <a href='?stat=labor' class='btn btn-primary btn-block'>Labor</a><br />
    </div>
    <div class='col-auto'>
        <a href='?stat=iq' class='btn btn-primary btn-block'>IQ</a><br />
    </div>
    <div class='col-auto'>
        <a href='?stat=total' class='btn btn-primary btn-block'>Total Stats</a><br />
    </div>
    <div class='col-auto'>
        <a href='?stat=profit' class='btn btn-primary btn-block'>Asset Profit</a><br />
    </div>";
    if (date('n') >= 10)
    {
        echo " <div class='col-auto'>
        <a href='?stat=" . date('Y') . "halloweenCandies' class='btn btn-primary btn-block'>" . date('Y') . " Halloween</a><br />
        </div>";
    }
    echo"
</div>";
echo "<div class='card'>
        <div class='card-header'>
            Listing the {$hofCount} players with the highest " . parseHOFname($_GET['stat']) . "
        </div>
        <div class='card-body'>";
$rank = 1;
if ($_GET['stat'] != 'crypto')
{
    //Loop through the top 20 users.
    while ($r = $db->fetch_row($q)) {
        echo "<div class='row'>
                <div class='col-2'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Rank</b></small>
                        </div>
                        <div class='col-12'>
                            {$rank}
                        </div>
                    </div>
                </div>
                <div class='col'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Player</b></small>
                        </div>
                        <div class='col-12'>
                            <a href='profile.php?user={$r['userid']}'>" . parseUsername($r['userid']) . "</a> " . parseUserID($r['userid']) . "
                        </div>
                    </div>
                </div>";
            $showArray = array('level', 'primary_currency', 'secondary_currency', 
                            'mining_level', 'busts', 'kills', 'deaths', 'richest', 
                            'farm_level', 'profit', date('Y') . 'halloweenCandies'
            );
            if (in_array($_GET['stat'], $showArray)) 
                {
                    if ($_GET['stat'] == date('Y') . "halloweenCandies")
                        $parsed = getUserPref($r['userid'], date('Y') . "halloweenCandies", 0);
                    else
                        $parsed = $r[$_GET['stat']];
                    echo"
                    <div class='col'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>" . parseHOFname($_GET['stat']) . "</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($parsed) . "
                            </div>
                        </div>
                    </div>";
                }
                echo "</div>";
        $rank++;
    }
    echo "</div>
            </div>";
}

function parseHOFname($stat)
{
    $array = array("level" => "Level", "primary_currency" => "Copper Coins",
        "secondary_currency" => "Chivlary Tokens", "mining_level" => "Mining Level",
        "busts" => "Dungeon Busts", "kills" => "Kills", "deaths" => "Deaths", 
        "richest" => "Networth", "farm_level" => "Farming Level", "profit" => "Market ROI",
        date('Y') . "halloweenCandies" => "Halloween Candies Collected", "strength" => "Strength",
        "guard" => "Guard", "agility" => "Agility", "labor" => "Labor", "iq" => "IQ",
        "total" => "Total Stats"
    );
    return $array[$stat];
}
$h->endpage();