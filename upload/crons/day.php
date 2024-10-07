<?php
/*
	File: crons/day.php
	Created: 6/15/2016 at 2:43PM Eastern Time
	Info: Runs the queries below when the server hits midnight.
	Add queries of your own to have queries executed at midnight
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
$menuhide=1;
require_once(__DIR__ .'/../globals_nonauth.php');
if (!isset($_GET['code']) || $_GET['code'] !== $_CONFIG['code'])
{
    exit;
}
//Delete things from more than 30 days ago
$last24 = time() - 86400;

$db->query("UPDATE `users` SET `vip_days`=`vip_days`-1 WHERE `vip_days` > 0");

$db->query("UPDATE `users` SET `hexbags` = 100, `bor` = 1000");
$db->query("UPDATE `user_settings` SET `att_dg` = 0");

$db->query("UPDATE `users` SET `dayslogged` = 0 WHERE `laston` < {$last24}");
$db->query("UPDATE `users` SET `dayslogged` = `dayslogged` + 1 WHERE `laston` > {$last24}");
$db->query("UPDATE `users` SET `rewarded` = 0");
$db->query("UPDATE `userstats` SET `luck` = 100");

$db->query("UPDATE `settings` SET `setting_value` = 0 WHERE `setting_name` = 'casino_give'");
$db->query("UPDATE `settings` SET `setting_value` = 0 WHERE `setting_name` = 'casino_take'");

$db->query("UPDATE `bank_investments` SET `days_left` = `days_left` - 1");
//Guild daily interest.
$db->query("UPDATE `guild` SET `guild_primcurr`=`guild_primcurr`+(`guild_primcurr`/20) WHERE `guild_primcurr`>0");
$biq=$db->query("/*qc=on*/SELECT * FROM `bank_investments` WHERE `days_left` = 0");
while ($riq = $db->fetch_row($biq))
{
	$add=$riq['amount']*($riq['interest']/100);
	$investment=$riq['amount']+$add;
	$db->query("UPDATE `users` SET `bank`=`bank`+{$investment} WHERE `userid` = {$riq['userid']}");
	$api->GameAddNotification($riq['userid'],"Your bank investment of " . shortNumberParse($riq['amount']) . " Copper Coins has finished. " . shortNumberParse($investment) . " Copper Coins have been added to your bank account.");
	$db->query("DELETE FROM `bank_investments` WHERE `userid` = {$riq['userid']} AND `invest_id` = {$riq['invest_id']}");
	addToEconomyLog('Bank Investments', 'copper', $add);
}
$fiveday=Random(3,9);
$tenday=Random(7,20);
$twentyday=Random(16,48);
$thirtyday=Random(35,64);
$db->query("UPDATE `settings` SET `setting_value` = '{$fiveday}' WHERE `setting_name` = '5day'");
$db->query("UPDATE `settings` SET `setting_value` = '{$tenday}' WHERE `setting_name` = '10day'");
$db->query("UPDATE `settings` SET `setting_value` = '{$twentyday}' WHERE `setting_name` = '20day'");
$db->query("UPDATE `settings` SET `setting_value` = '{$thirtyday}' WHERE `setting_name` = '30day'");
$db->query("TRUNCATE TABLE `votes`");
//doDailyDistrictTick();
doDailyGuildFee();
//Banks daily interest
doDailyBankInterest();
doDailyFedBankInterest();
doDailyVaultBankInterest();


//Random player showcase
/*$cutoff = time() - 86400;
$uq=$db->query("SELECT `userid` FROM `users` WHERE `userid` != 1 AND `laston` > {$cutoff} ORDER BY RAND() LIMIT 1");
$ur=$db->fetch_single($uq);
//$api->GameAddNotification($ur,"You have been chosen as the Player of the Day! Your profile will be displayed on the login page, and you've received a unique badge in your inventory.");
item_add($ur,154,1);*/
runMarketTick(1);   //low risk market
purgeOldLogs();
addAutoBountyListing();

$db->query("UPDATE `user_settings` SET `winnings_this_hour` = 0");

$month = currentMonth();
$day = currentDay();
$hour = currentHour();
$year = currentYear();

if (($month == 1) && ($day == 1))
{
    $api->GameAddAnnouncement("Happy new year!!<br />What better way to bring in {$year} by having a 50% combat experience event? Sounds good to me! From now until the end of the month, enjoy the bonus experience!");
}
if (($month == 2) && ($day == 1))
{
    $api->GameAddAnnouncement("Welcome to the second month of the year, February!<br />This month's event revolves around the estates. Prices at the estate management have been reduced by 50%, and resource costs to upgrade your estates have been reduced by 25%! Its a great month to get into a home you like. See you in March!");
}
if (($month == 5) && ($day == 1))
{
    $api->GameAddAnnouncement("Greetings folks!<br />Welcome to the month of May, this month's event is reduced costs at the Wood Cutter! Through now until the end of May, the cost to cut a log has been halved, stick output increased by 50% and upgrade costs are now 25% off! See you for next month's event!");
}
if (($month == 6) && ($day == 1))
{
    $api->GameAddAnnouncement("Hey folks!<br />We're welcoming in the summer with the month of June, this month's event is reduced consumption at the mines by 50%! Hope to see some major power mining this month! See you next month for whatever July has in store for us!");
}
if (($month == 7) && ($day == 1))
{
    $api->GameAddAnnouncement("Good early morning everyone!<br />The summer heat's starting to get intolerable but we're still going strong in the farmlands this July. Expect 50% faster grow times, double experience AND your Well will grow by double each level! See you in August!");
}
if (($month == 8) && ($day == 1))
{
    $api->GameAddAnnouncement("Hope everyone enjoyed the summer break, but its now time to get back into working order.<br />The month of August has us getting the daily gambling bet cap increased by 225% for the entirety of the month. Enjoy it! Don't go broke. See you for September!");
}
if (($month == 9) && ($day == 1))
{
    $api->GameAddAnnouncement("...I really didn't want to be woke up until the end of this month....<br />But fine, whatever.... for the month of September the Hexbags and Boxes of Random have had their rewards doubled! Street begging will regenerate at 50 per hour and will be capped at 200. All this through now until the end of September. See you for the Halloween festivities next month!");
}
if ($month == 10)
{
    if ($day == 1)
    {
        $api->GameAddAnnouncement("Hey folks! To kickstart the Halloween season, you may now visit a player's profile and Trick or Treat using the now-available link under the action section. You may trick or treat on a player once an hour. Doing so will grant you random candies that may be helpful on your journey. Also, on the Explore page, you will now see a link to the Pumpkin Chuck. You should participate to get the best distance and hopefully win some nice prizes! Stay tuned for more Halloween tricks as we get closer to the holiday!");
        $db->query("INSERT INTO `shopitems` (`sitemSHOP`, `sitemITEMID`) VALUES(1, 64)"); //add pumpkin to Cornrye pub
        $db->query("UPDATE `items` SET `itmbuyprice` = '500', `itmsellprice` = '250', `itmbuyable` = 'true' WHERE `itmid` = 64;");  //update pumpkin price, and make sure its buyable
    }
}

if ($month == 11) 
{
    if ($day == 1)
    {
        $api->GameAddAnnouncement("That concludes the Halloween Trick or Treat! I'll be making another announcement shortly after I tally up the prizes. Thank you to everyone who played.<br />Now that its November, however, we need to transition into turkey hunting season. Check out the new link on Explore to start stockpiling feathers and turkeys for the turkey hunt. Get enough feathers and you can build this year's Thanksgiving Armor!");
        $db->query("DELETE FROM `shopitems` WHERE `sitemSHOP` = 1 AND `sitemITEMID` = 64"); //delete pumpkin from cornrye pub
        $db->query("UPDATE `items` SET `itmbuyprice` = '500000000', `itmsellprice` = '250000000', `itmbuyable` = 'true' WHERE `itmid` = 64;");  //reset pumpkin price
    }
}

if (($month == 12) && ($day == 1))
{
    $db->query("TRUNCATE TABLE `advent_calender`");
    $api->GameAddAnnouncement("Greetings folks!<br />Thanks to everyone who participated in the Thankgiving event. That was awesome! Hope you get time to craft the armor. To start off December, your employers are now giving out 75% extra wages the entirety of the month! How nice of them! Don't forget the {$year} Advent Calendar is now live on the Explore page. Stay tuned for more Christmas events from now until the holidays!");
}
if (($day == 1) && ($hour == 12))
{
    $q=$db->query("SELECT * FROM `vote_raffle` WHERE `userid` != 1 ORDER BY RAND() LIMIT 1");
    if ($db->num_rows($q) > 0)
    {
        $r=$db->fetch_row($q);
        $r['username'] = parseUsername($r['userid']);
        $announcement = "<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}] has won this month's voting raffle and received a $3 VIP Pack. Thanks for voting! The raffle has been reset, and everyone now has an equal shot to win this month's raffle!!";
        $api->GameAddAnnouncement($announcement, 1);
        $api->UserGiveItem($r['userid'],13,1);
        $api->GameAddNotification($r['userid'],"You were selected as the Monthly Vote Raffle winner! You've received a $3 VIP Pack to your inventory!");
        $db->query("TRUNCATE TABLE `vote_raffle`");
    }
    $db->query("UPDATE `settings` SET `setting_value` = '0.00' WHERE `setting_name` = 'MonthlyDonationGoal'");
}
?>
