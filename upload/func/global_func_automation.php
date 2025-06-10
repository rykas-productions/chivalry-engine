<?php
//Called daily
function addTokenMarketListing()
{
    global $db;
    $totalcost      = $db->fetch_single($db->query("SELECT SUM(`token_total`) FROM `token_market_avg`"));
    $totaltokens    = $db->fetch_single($db->query("SELECT SUM(`token_sold`) FROM `token_market_avg`"));
    $avgprice       = $totalcost / $totaltokens;
    $listprice      = $avgprice * (Random(80,115) / 100);   //Randomize the price of this listing between 80-105% of market price
    $tokens = Random(round(75000/(Random(2,4))), round(75000*(Random(2,4))));
    $db->query("DELETE FROM `sec_market` WHERE `sec_user` = 0");
    $db->query("INSERT INTO `sec_market` (`sec_user`, `sec_cost`, `sec_total`, `sec_deposit`) VALUES ('0', '{$listprice}', '{$tokens}', 'false')");
}

//Called daily
function addAutoBountyListing()
{
    global $db;
    $db->query("DELETE FROM `bounty_hunter` WHERE `bh_creator` = 0");
    
    $targetPlayer = getRandomDailyActiveUserID();
    $expired = time() + 259200;
    $bounty = Random(250000,250000000);
    $db->query("INSERT INTO `bounty_hunter` (`bh_creator`, `bh_user`, `bh_time`, `bh_bounty`) VALUES ('0', '{$targetPlayer}', '{$expired}', '{$bounty}')");
}

//Called hourly
function giveNPCsMoney()
{
    global $db;
    $q = $db->query("SELECT * FROM `users` WHERE `user_level` = 'NPC'");
    while ($r = $db->fetch_row($q))
    {
        $monies = round(Random(100000000/Random(2,4),100000000*Random(2,4)) * levelMultiplier($r['level']));
        $db->query("UPDATE `users` SET `primary_currency` = {$monies} WHERE `userid` = {$r['userid']}");
    }
}

function send_mass_email_batch($batchSize = 50)
{
    global $db, $api, $set;
    
    $q = $db->query("SELECT * FROM `mass_email_queue`
                     WHERE `status` = 'pending'
                     ORDER BY `queued_at` ASC
                     LIMIT {$batchSize}");
    
    $sent = 0;
    
    while ($email = $db->fetch_row($q)) {
        $r = $db->fetch_single($db->query("SELECT `email` FROM `users` WHERE `userid` = {$email['userid']}"));
        $success = $api->SystemSendEmail(
            $r,
            $email['message'],
            $email['subject'],
            $set['sending_email']
            );
        
        $status = $success ? 'sent' : 'failed';
        $sentAt = $success ? time() : 0;
        
        $db->query("UPDATE `mass_email_queue`
                    SET `status` = '{$status}', `sent_at` = {$sentAt}
                    WHERE `id` = {$email['id']}");
        
        $sent++;
    }
    
    if ($sent > 0)
        $api->GameAddNotification(1, "{$sent} emails sent successfully.");
}