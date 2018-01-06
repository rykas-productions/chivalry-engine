<?php
require("globals.php");
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'level1':
        level(5,1);
        break;
	case 'level2':
        level(25,2);
        break;
	case 'level3':
        level(100,3);
        break;
	case 'level4':
        level(200,4);
        break;
	case 'level5':
        level(300,5);
        break;
	case 'bust1':
        busts(25,6);
        break;
	case 'bust2':
        busts(100,7);
        break;
	case 'bust3':
        busts(250,8);
        break;
	case 'bust4':
        busts(500,9);
        break;
	case 'bust5':
        busts(1000,10);
        break;
	case 'mine1':
        mine(10,11);
        break;
	case 'mine2':
        mine(20,12);
        break;
	case 'mine3':
        mine(50,13);
        break;
	case 'kill1':
        kills(10,14);
        break;
	case 'kill2':
        kills(50,15);
        break;
	case 'kill3':
        kills(100,16);
        break;
	case 'kill4':
        kills(500,17);
        break;
	case 'kill5':
        kills(1000,18);
        break;
	case 'death1':
		deaths(10,19);
        break;
	case 'death2':
        deaths(50,20);
        break;
	case 'death3':
        deaths(100,21);
        break;
	case 'death4':
        deaths(500,22);
        break;
	case 'death5':
        deaths(1000,23);
        break;
	case 'refer1':
		refers(1,24);
        break;
	case 'refer2':
        refers(5,26);
        break;
	case 'refer3':
        refers(10,26);
        break;
	case 'refer4':
        refers(50,27);
        break;
	case 'refer5':
        refers(100,28);
        break;
    default:
        home();
        break;
}
function home()
{
	global $h;
	echo "Here's a list of in-game achievements. Click on an achievement to be rewarded with it. You may only 
	be rewarded once per achievement.";
	echo "
	<div class='row'>
		<div class='col-sm'>
			<u><b>Level</b></u><br />
			<a href='?action=level1'>Level 5</a><br />
			<a href='?action=level2'>Level 25</a><br />
			<a href='?action=level3'>Level 100</a><br />
			<a href='?action=level4'>Level 200</a><br />
			<a href='?action=level5'>Level 300</a><br />
		</div>
		<div class='col-sm'>
			<u><b>Mining Level</b></u><br />
			<a href='?action=mine1'>Mining Level 10</a><br />
			<a href='?action=mine2'>Mining Level 20</a><br />
			<a href='?action=mine3'>Mining Level 50</a><br />
		</div>
		<div class='col-sm'>
			<u><b>Busts</b></u><br />
			<a href='?action=bust1'>25 Busts</a><br />
			<a href='?action=bust2'>100 Busts</a><br />
			<a href='?action=bust3'>250 Busts</a><br />
			<a href='?action=bust4'>500 Busts</a><br />
			<a href='?action=bust5'>1,000 Busts</a><br />
		</div>
	</div>
	<div class='row'>
		<div class='col-sm'>
			<u><b>Kills</b></u><br />
			<a href='?action=kill1'>10 Kills</a><br />
			<a href='?action=kill2'>50 Kills</a><br />
			<a href='?action=kill3'>100 Kills</a><br />
			<a href='?action=kill4'>500 Kills</a><br />
			<a href='?action=kill5'>1,000 Kills</a><br />
		</div>
		<div class='col-sm'>
			<u><b>Deaths</b></u><br />
			<a href='?action=death1'>10 Deaths</a><br />
			<a href='?action=death2'>50 Deaths</a><br />
			<a href='?action=death3'>100 Deaths</a><br />
			<a href='?action=death4'>500 Deaths</a><br />
			<a href='?action=death5'>1,000 Deaths</a><br />
		</div>
		<div class='col-sm'>
			<u><b>Referrals</b></u><br />
			<a href='?action=refer1'>1 Referral</a><br />
			<a href='?action=refer2'>5 Referrals</a><br />
			<a href='?action=refer3'>10 Referrals</a><br />
			<a href='?action=refer4'>50 Referrals</a><br />
			<a href='?action=refer5'>100 Referrals</a><br />
		</div>
	</div>";
	$h->endpage();
}
function level($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	if ($ir['level'] < $level)
	{
		alert('danger',"Uh Oh!","Your level is too low to receive this achievement. You need to be level {$level}. You are level {$ir['level']}.",true,'achievements.php');
		die($h->endpage());
	}
	$db->query("INSERT INTO `achievements_done` (`userid`, `achievement`) VALUES ('{$userid}', '{$id}')");
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for level {$level}.");
	$tokens=Random(1,5)*$level;
	$api->UserGiveCurrency($userid,'secondary',$tokens);
	alert('success',"Success!","You have successfully achieved the Level {$level} achievement and were rewarded {$tokens} Chivalry Tokens.",true,'achievements.php');
	$h->endpage();
}
function busts($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	if ($ir['busts'] < $level)
	{
		alert('danger',"Uh Oh!","Your bust count is too low to receive this achievement. You need to have at least {$level} busts. You only have {$ir['level']}.",true,'achievements.php');
		die($h->endpage());
	}
	$db->query("INSERT INTO `achievements_done` (`userid`, `achievement`) VALUES ('{$userid}', '{$id}')");
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for total busts {$level}.");
	$tokens=Random(100,500)*$level;
	$api->UserGiveCurrency($userid,'primary',$tokens);
	alert('success',"Success!","You have successfully achieved the Total Busts {$level} achievement and were rewarded {$tokens} Copper Coins.",true,'achievements.php');
	$h->endpage();
}
function mine($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	$minelvl=$db->fetch_single($db->query("SELECT `mining_level` FROM `mining` WHERE `userid` = {$userid}"));
	if ($minelvl < $level)
	{
		alert('danger',"Uh Oh!","Your mining level is too low to receive this achievement. You need to be mining level {$level}. You have a mining level of {$minelvl}.",true,'achievements.php');
		die($h->endpage());
	}
	$db->query("INSERT INTO `achievements_done` (`userid`, `achievement`) VALUES ('{$userid}', '{$id}')");
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for mining level {$level}.");
	$tokens=Random(1,5)*$level;
	$api->UserGiveCurrency($userid,'secondary',$tokens);
	alert('success',"Success!","You have successfully achieved the Mining Level {$level} achievement and were rewarded {$tokens} Chivalry Tokens.",true,'achievements.php');
	$h->endpage();
}
function kills($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	if ($ir['kills'] < $level)
	{
		alert('danger',"Uh Oh!","Your kill count is too low to receive this achievement. You need to have at least {$level} kills. You only have {$ir['kills']}.",true,'achievements.php');
		die($h->endpage());
	}
	$db->query("INSERT INTO `achievements_done` (`userid`, `achievement`) VALUES ('{$userid}', '{$id}')");
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for {$level} kills.");
	$tokens=Random(1,5)*$level;
	$api->UserGiveCurrency($userid,'secondary',$tokens);
	alert('success',"Success!","You have successfully achieved the {$level} kills achievement and were rewarded {$tokens} Chivalry Tokens.",true,'achievements.php');
	$h->endpage();
}
function deaths($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	if ($ir['deaths'] < $level)
	{
		alert('danger',"Uh Oh!","Your death count is too low to receive this achievement. You need to have at least {$level} deaths. You only have {$ir['deaths']}.",true,'achievements.php');
		die($h->endpage());
	}
	$db->query("INSERT INTO `achievements_done` (`userid`, `achievement`) VALUES ('{$userid}', '{$id}')");
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for {$level} deaths.");
	$tokens=Random(1,5)*$level;
	$api->UserGiveCurrency($userid,'secondary',$tokens);
	alert('success',"Success!","You have successfully achieved the {$level} deaths achievement and were rewarded {$tokens} Chivalry Tokens.",true,'achievements.php');
	$h->endpage();
}
function refers($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	$ref=$db->fetch_single($db->query("SELECT COUNT(`referalid`) FROM `referals` WHERE `referal_userid` = {$userid}"));
	if ($ref < $level)
	{
		alert('danger',"Uh Oh!","Your referral count is too low to receive this achievement. You need to have at least {$level} referral. You only have {$ref}.",true,'achievements.php');
		die($h->endpage());
	}
	$db->query("INSERT INTO `achievements_done` (`userid`, `achievement`) VALUES ('{$userid}', '{$id}')");
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for {$level} referrals.");
	$tokens=Random(1,5)*$level;
	$api->UserGiveCurrency($userid,'secondary',$tokens);
	alert('success',"Success!","You have successfully achieved the {$level} referrals achievement and were rewarded {$tokens} Chivalry Tokens.",true,'achievements.php');
	$h->endpage();
}
