<?php
$menuhide=1;
require_once(__DIR__ .'/../globals_nonauth.php');
if (!isset($argv))
{
    exit;
}
$_GET['code']=substr($argv[1],5);
if (!isset($_GET['code']) || $_GET['code'] !== $_CONFIG['code'])
{
    exit;
}
$q=$db->query("SELECT * FROM `vote_raffle` WHERE `userid` != 1 ORDER BY RAND() LIMIT 1");
if ($db->num_rows($q) > 0)
{
	$r=$db->fetch_row($q);
	$r['username'] = parseUsername($r['userid']);
	$announcement = "<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}] has won this month's voting raffle. They received 25 CID Admin Gym Scrolls. Thanks for voting! The raffle has been reset, and everyone now has an equal shot to win this month's raffle!!";
	$api->GameAddAnnouncement($announcement, 1);
	$api->UserGiveItem($r['userid'],205,25);
	$api->GameAddNotification($r['userid'],"You were selected as the Monthly Vote Raffle winner! You've received 25 CID Admin Gym Scrolls to your inventory!");
	$db->query("TRUNCATE TABLE `vote_raffle`");
}
$db->query("UPDATE `settings` SET `setting_value` = '0.00' WHERE `setting_name` = 'MonthlyDonationGoal'");