<?php
function addTokenMarketListing()
{
    global $db;
    $totalcost      = $db->fetch_single($db->query("SELECT SUM(`token_total`) FROM `token_market_avg`"));
    $totaltokens    = $db->fetch_single($db->query("SELECT SUM(`token_sold`) FROM `token_market_avg`"));
    $avgprice       = $totalcost / $totaltokens;
    $listprice      = $avgprice * (Random(90,110) / 100);   //Randomize the price of this listing between 90-110% of market price
    $tokens = Random(round(75000/2), round(75000*1.5));
    $limit = 5;
    $q = $db->query("SELECT * FROM `sec_market` WHERE `sec_user` = 0");
    if ($db->num_rows($q) == $limit)
    {
        //delete earliest
        $db->query("DELETE FROM `sec_market` WHERE `sec_user` = 0 ORDER BY `sec_id` ASC LIMIT 1");
    }
    $db->query("INSERT INTO `sec_market` (`sec_user`, `sec_cost`, `sec_total`, `sec_deposit`) VALUES ('0', '{$listprice}', '{$tokens}', 'false')");
}

function addAutoBountyListing()
{
    global $db;
    $db->query("DELETE FROM `bounty_hunter` WHERE `bh_creator` = 0");
    
    $targetPlayer = getRandomDailyActiveUserID();
    $expired = time() + 259200;
    $bounty = Random(250000,250000000);
    $db->query("INSERT INTO `bounty_hunter` (`bh_creator`, `bh_user`, `bh_time`, `bh_bounty`) VALUES ('0', '{$targetPlayer}', '{$expired}', '{$bounty}')");
}