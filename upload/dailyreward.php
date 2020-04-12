<?php
/*
	File:		dailyreward.php
	Created: 	10/24/2017 at 1:44PM Eastern Time
	Info: 		Daily rewards
	Author:		TheMasterGeneral
	Website: 	http://chivalryisdead.x10.mx
*/
if ($ir['rewarded'] == 0)
{
	$rng=Random(1,5);
    $tokenreward=(1500*$rng)+((1500*$rng)*levelMultiplier($ir['level']));
	$medReward=Random(5,15);
    $scrollreward=Random(10,20);
	$luck=Random(10,35);
    $reward=Random(1,100);
	$copper=1000000+(1000000*levelMultiplier($ir['level']));
	$bor=Random(500,1025);
	$guardTime=Random(15,40);
	$autoHex=Random(50,105);
	$rrBet=Random(50000,1250000);
	//Give skill point.
	if ($reward == 99 || $reward == 100)
	{
		$db->query("UPDATE `user_settings` SET `skill_points` = `skill_points` + 1 WHERE `userid` = {$userid}");
		$api->GameAddNotification($userid,"You have gained one skill point for logging into Chivalry is Dead today. Thank you!");
		$api->SystemLogsAdd($userid, "loginreward", "Received 1 Skill Point.");
	}
	if ($reward <= 98 && $reward > 90)
	{
		$api->UserGiveItem($userid,207,$medReward);
		$api->GameAddNotification($userid,"While logging into Chivalry is Dead today, you were gifted {$medReward} Priority Vouchers, used at the infirmary. Consider it a gift from us. 😉");
		$api->SystemLogsAdd($userid, "loginreward", "Received {$medReward} Priority Vouchers.");
	}
	if ($reward <= 89 && $reward > 85)
	{
		$api->UserGiveItem($userid,284,1);
		$api->GameAddNotification($userid,"Overnight, your guild had robbed a local shop. They've returned and your gift was an Amulet of Criminal Mischief. Thank you for playing Chivalry is Dead!");
		$api->SystemLogsAdd($userid, "loginreward", "Received Amulet of Crminal Mischief.");
	}
	if ($reward <= 84 && $reward > 75)
	{
		$db->query("UPDATE `user_settings` SET `rickroll` = {$userid} WHERE `userid` = {$userid}");
		$api->UserGiveItem($userid,33,$bor);
		$api->GameAddNotification($userid,"You may have just been rickrolled, but we've given you " . number_format($bor) . " Boxes of Random. Just cuz! Thanks for playing Chivalry is Dead.");
		$api->SystemLogsAdd($userid, "loginreward", "Received " . number_format($bor) . " Boxes of Random and a Rick Roll.");
	}
	if ($reward <= 74 && $reward > 70)
	{
		$api->UserGiveItem($userid,259,1);
		$api->GameAddNotification($userid,"For logging into Chivalry is Dead today, we gave you a Marriage Rose. Hint, hint. :) We love you {$ir['username']}. We love you. ❤");
		$api->SystemLogsAdd($userid, "loginreward", "Received Marriage Rose.");
	}
	if ($reward <= 69 && $reward > 65)
	{
		$api->UserGiveItem($userid,205,$scrollreward);
		$api->GameAddNotification($userid,"To hopefully jumpstart some training sessions, we've gifted you {$scrollreward} CID Admin Gym Access Scrolls. Thanks for playing Chivalry is Dead!");
		$api->SystemLogsAdd($userid, "loginreward", "Received {$scrollreward} CID Admin Gym Access Scrolls.");
	}
	if ($reward <= 64 && $reward > 60)
	{
		$endtime=time()+($guardTime*60);
		$db->query("UPDATE `user_settings` SET `protection` = {$endtime} WHERE `userid` = {$userid}");
		$api->GameAddNotification($userid,"We got your back {$ir['username']}! Someone was about to hit on you, so we bought you some protection. Its your login gift for the day. Thanks for playing CID!");
		$api->SystemLogsAdd($userid, "loginreward", "Received {$guardTime} minutes of Protection.");
	}
	if ($reward <= 59 && $reward > 53)
	{
		$db->query("UPDATE `user_settings` SET `autohex` = `autohex` + {$autoHex} WHERE `userid` = {$userid}");
		$api->GameAddNotification($userid,"For your dedication of logging into Chivalry is Dead today, we've given you {$autoHex} Auto Hexbags for you to use. We appreciate you playing.");
		$api->SystemLogsAdd($userid, "loginreward", "Received {$autoHex} Automatic Hexbags.");
	}
	if ($reward <= 52 && $reward > 50)
	{
		if ($ir['will_overcharge'] < time())
			$startTime=time();
		else
			$startTime=$ir['will_overcharge'];
		$newTime=$startTime + (60*60)*0.25;
		$db->query("UPDATE `user_settings` SET `will_overcharge` = {$newTime} WHERE `userid` = {$userid}");
		$api->GameAddNotification($userid,"Go bulk up now!! For logging in, we've given you 15 minutes of free Will Stimulant use. Go train! Thank us later!");
		$api->SystemLogsAdd($userid, "loginreward", "Received 15 minutes Will Stimulant.");
	}
	if ($reward <= 49 && $reward > 47)
	{
		$api->UserGiveItem($userid,285,1);
		$api->GameAddNotification($userid,"You've received a Potion of Permanent Strength for logging into Chivalry is Dead today. Thanks a lot!");
		$api->SystemLogsAdd($userid, "loginreward", "Received Potion of Permanent Strength.");
	}
	if ($reward <= 46 && $reward > 44)
	{
		$api->UserGiveItem($userid,286,1);
		$api->GameAddNotification($userid,"You've received a Potion of Everlasting Speed for logging into Chivalry is Dead today. Thanks a lot!");
		$api->SystemLogsAdd($userid, "loginreward", "Received Potion of Everlastin Speed.");
	}
	if ($reward <= 43 && $reward > 41)
	{
		$api->UserGiveItem($userid,287,1);
		$api->GameAddNotification($userid,"You've received a Potion of Youthful Tolerance for logging into Chivalry is Dead today. Thanks a lot!");
		$api->SystemLogsAdd($userid, "loginreward", "Received Potion of Youthful Tolerance.");
	}
	if ($reward <= 40 && $reward > 38)
	{
		$api->UserGiveItem($userid,148,1);
		$api->GameAddNotification($userid,"I'm sorry for the crappy gift... but all we have is this old Tome of Experience we don't have a use for... so here, have it. Thanks for playing CID. :/");
		$api->SystemLogsAdd($userid, "loginreward", "Received Tome of Experience.");
	}
	if ($reward <= 37 && $reward > 26)
	{
		$api->UserGiveCurrency($userid,'secondary',$tokenreward);
		$api->GameAddNotification($userid,"We've given you " . number_format($tokenreward) . " Chivalry Tokens for logging into Chivalry is Dead today. Thanks~");
		$api->SystemLogsAdd($userid, "loginreward", "Received " . number_format($tokenreward) . " Chivalry Tokens.");
	}
	if ($reward <= 25 && $reward > 23)
	{
		$db->query("UPDATE `users` SET `vip_days` = `vip_days` + 1 WHERE `userid` = {$userid}");
		$api->GameAddNotification($userid,"Enjoy the free VIP Day {$ir['username']}! Consider it a thanks from us at Chivalry is Dead.");
		$api->SystemLogsAdd($userid, "loginreward", "Received VIP Day.");
	}
	if ($reward <= 22 && $reward > 11)
	{
		$api->UserGiveCurrency($userid,'primary',$copper);
		$api->GameAddNotification($userid,"Here's a flat " . number_format($copper) . " Copper Coins just for logging into Chivalry is Dead today. Thanks babe!");
		$api->SystemLogsAdd($userid, "loginreward", "Received " . number_format($copper) . " Copper Coins.");
		addToEconomyLog('Daily Reward', 'copper', $copper);
	}
	if ($reward <= 10 && $reward > 8)
	{
		$newAddToken = $ir['tokenbank']/50; 
		$db->query("UPDATE `users` SET `tokenbank`=`tokenbank`+(`tokenbank`/50) WHERE `tokenbank`>0 AND `userid` = {$userid}");
		$api->GameAddNotification($userid,"We <i>accidentally</i> ran 2% interest on your Chivalry Token account. Sorry! However, for the mess up, we'll allow you to keep the extra Chivalry Tokens in the account. Thanks for playing Chivalry is Dead!");
		$api->SystemLogsAdd($userid, "loginreward", "Received 2% Token Bank account interest.");
		addToEconomyLog('Daily Reward', 'token', $newAddToken);
	}
	if ($reward <= 7 && $reward > 1)
	{
		$db->query("INSERT INTO `russian_roulette` (`challengee`, `challenger`, `reward`) VALUES ('{$userid}', '1', '{$rrBet}');");
		$api->GameAddNotification($userid,"We've issued you a Russian Roulette challenge. If you win, you get the Copper Coins! Consider this a fun, risky login reward.");
		$api->SystemLogsAdd($userid, "loginreward", "Received Russian Roulette challenges.");
		addToEconomyLog('Daily Reward', 'copper', $rrBet);
	}
	$db->query("UPDATE `users` SET `rewarded` = 1 WHERE `userid` = {$userid}");
	
}