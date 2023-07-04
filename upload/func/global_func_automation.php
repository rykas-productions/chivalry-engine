<?php
function addTokenMarketListing()
{
    global $db;
    $totalcost      = $db->fetch_single($db->query("SELECT SUM(`token_total`) FROM `token_market_avg`"));
    $totaltokens    = $db->fetch_single($db->query("SELECT SUM(`token_sold`) FROM `token_market_avg`"));
    $avgprice       = $totalcost / $totaltokens;
    $listprice      = $avgprice * (Random(90,110) / 100);   //Randomize the price of this listing between 90-110% of market price
    $q = $db->query("SELECT * FROM `sec_market` WHERE `sec_user` = 0");
    $tokens = Random(round(75000/2), round(75000*1.5));
    $db->query("DELETE FROM `sec_market` WHERE `sec_user` = 0");
    $db->query("INSERT INTO `sec_market` (`sec_user`, `sec_cost`, `sec_total`, `sec_deposit`) VALUES ('0', '{$listprice}', '{$tokens}', 'false')");
}