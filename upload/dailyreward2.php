<?php
/*
	File:		dailyreward.php
	Created: 	10/24/2017 at 1:44PM Eastern Time
	Info: 		Daily rewards
	Author:		TheMasterGeneral
	Website: 	http://chivalryisdead.x10.mx
*/
require('globals.php');
if ($ir['rewarded'] == 0)
{
    $tokenreward=$ir['level']*Random(300,1500);
	$medReward=Random(5,15);
    $scrollreward=Random(10,20);
	$luck=Random(10,35);
    $reward=Random(1,100);
	$copper=1000000+(1000000*levelMultiplier($ir['level']));
	//Give skill point.
	if ($reward == 99 || $reward == 100)
	{
		$db->query("UPDATE `user_settings` SET `skill_points` = `skill_points` + 1 WHERE `userid` = {$userid}");
		$api->GameAddNotification($userid,"You have gained one skill point for logging into Chivalry is Dead today. Thank you!");
	}
	if ($reward <= 98 && $reward > 90)
	{
		$api->UserGiveItem($userid,207,$medRward);
		$api->GameAddNotification($userid,"While logging into Chivalry is Dead today, you were gifted {$medReward} Priority Vouchers, used at the infirmary. Consider it a gift from us. 😉");
	}
	if ($reward <= 89 && $reward > 85)
	{
		$api->UserGiveItem($userid,284,1);
		$api->GameAddNotification($userid,"Overnight, your guild had robbed a local shop. They've returned and your gift was an Amulet of Criminal Mischief. Thank you for playing Chivalry is Dead!");
	}
	if ($reward <= 84 && $reward > 75)
	{
		$db->query("UPDATE `user_settings` SET `rickroll` = {$userid} WHERE `userid` = {$userid}");
		$api->UserGiveItem($userid,33,1000);
		$api->GameAddNotification($userid,"You may have just been rickrolled, but we've given you 1,000 Boxes of Random. Just cuz! Thanks for playing Chivalry is Dead.");
	}
	if ($reward <= 74 && $reward > 70)
	{
		$api->UserGiveItem($userid,259,1);
		$api->GameAddNotification($userid,"For logging into Chivalry is Dead today, we gave you a Marriage Rose. Hint, hint. :) We love you {$ir['username']}. We love you. ❤");
	}
	if ($reward <= 69 && $reward > 65)
	{
		$api->UserGiveItem($userid,205,$scrollreward);
		$api->GameAddNotification($userid,"To hopefully jumpstart some training sessions, we've gifted you {$scrollreward} CID Admin Gym Access Scrolls. Thx 4 playing Chivalry is Dead!");
	}
	if ($reward <= 64 && $reward > 60)
	{
		$endtime=time()+(35*60);
		$db->query("UPDATE `user_settings` SET `protection` = {$endtime} WHERE `userid` = {$userid}");
		$api->GameAddNotification($userid,"We got your back {$ir['username']}! Someone was about to hit on you, so we bought you some protection. Its your login gift for the day. Thanks for playing CID!");
	}
	if ($reward <= 59 && $reward > 53)
	{
		$db->query("UPDATE `user_settings` SET `autohex` = `autohex` + 100 WHERE `userid` = {$userid}");
		$api->GameAddNotification($userid,"For your dedication of logging into Chivalry is Dead today, we've given you 100 Auto Hexbags for you to use. We appreciate you playing.");
	}
	if ($reward <= 52 && $reward > 50)
	{
		f ($ir['will_overcharge'] < time())
			$startTime=time();
		else
			$startTime=$ir['will_overcharge'];
		$newTime=$startTime + (60*60)*0.25;
		$db->query("UPDATE `user_settings` SET `will_overcharge` = {$newTime} WHERE `userid` = {$userid}");
		$api->GameAddNotification($userid,"Go bulk up now!! For logging in, we've given you 15 minutes of free Will Stimulant use. Go train! Thank us later!");
	}
	if ($reward <= 49 && $reward > 47)
	{
		$api->UserGiveItem($userid,285,1);
		$api->GameAddNotification($userid,"You've received a Potion of Permanent Strength for logging into Chivalry is Dead today. Thanks a lot!");
	}
	if ($reward <= 46 && $reward > 44)
	{
		$api->UserGiveItem($userid,286,1);
		$api->GameAddNotification($userid,"You've received a Potion of Everlasting Speed for logging into Chivalry is Dead today. Thanks a lot!");
	}
	if ($reward <= 43 && $reward > 41)
	{
		$api->UserGiveItem($userid,287,1);
		$api->GameAddNotification($userid,"You've received a Potion of Youthful Tolerance for logging into Chivalry is Dead today. Thanks a lot!");
	}
	if ($reward <= 40 && $reward > 38)
	{
		$api->UserGiveItem($userid,148,1);
		$api->GameAddNotification($userid,"I'm sorry for the crappy gift... but all we have is this old Tome of Experience we don't have a use for... so here, have it. Thanks for playing CID. :/");
	}
	if ($reward <= 37 && $reward > 26)
	{
		$api->UserGiveCurrency($userid,'secondary',$tokenreward);
		$api->GameAddNotification($userid,"We've given you " . number_format($tokenreward) . " Chivalry Tokens for logging into Chivalry is Dead today. Thanks~");
	}
	if ($reward <= 25 && $reward > 23)
	{
		$db->query("UPDATE `users` SET `vip_days` = `vip_days` + 1 WHERE `userid` = {$userid}");
		$api->GameAddNotification($userid,"Enjoy the free VIP Day {$ir['username']}! Consider it a thanks from us at Chivalry is Dead.");
	}
	if ($reward <= 22 && $reward > 11)
	{
		$api->UserGiveCurrency($userid,'primary',$copper);
		$api->GameAddNotification($userid,"Here's a flat " . number_format($copper) . " Copper Coins just for logging into Chivalry is Dead today. Thanks babe!");
	}
	if ($reward <= 10 && $reward > 5)
	{
		$db->query("UPDATE `users` SET `tokenbank`=`tokenbank`+(`tokenbank`/50) WHERE `tokenbank`>0 AND `userid` = {$userid}");
		$api->GameAddNotification($userid,"We <i>accidentally</i> ran 2% interest on your Chivalry Token account. Sorry! However, for the mess up, we'll allow you to keep the extra Chivalry Tokens in the account. Thanks for playing Chivalry is Dead!");
	}
	if ($reward <= 4 && $reward > 1)
	{
		$db->query("INSERT INTO `russian_roulette` (`challengee`, `challenger`, `reward`) VALUES ('{$userid}', '1', '125000');");
		$db->query("INSERT INTO `russian_roulette` (`challengee`, `challenger`, `reward`) VALUES ('{$userid}', '1', '250000');");
		$db->query("INSERT INTO `russian_roulette` (`challengee`, `challenger`, `reward`) VALUES ('{$userid}', '1', '500000');");
		$db->query("INSERT INTO `russian_roulette` (`challengee`, `challenger`, `reward`) VALUES ('{$userid}', '1', '1000000');");
		$api->GameAddNotification($userid,"We've issued you a handful of Russian Roulette challenges. If you win, you get the Copper Coins! Consider this a fun, risky login reward.");
	}
	
}