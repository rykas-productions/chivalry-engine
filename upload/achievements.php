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
	case 'level6':
        level(50,49);
        break;
	case 'level7':
        level(150,50);
        break;
	case 'level8':
        level(500,51);
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
        refers(5,25);
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
	case 'crimec1':
        crimecopper(500000,29);
        break;
	case 'crimec2':
        crimecopper(1000000,30);
        break;
	case 'crimec3':
        crimecopper(5000000,31);
        break;
	case 'crimec4':
        crimecopper(25000000,32);
        break;
	case 'crimec5':
        crimecopper(100000000,33);
        break;
	case 'travel1':
        travel(1,34);
        break;
	case 'travel2':
        travel(5,35);
        break;
	case 'travel3':
        travel(10,36);
        break;
	case 'travel4':
        travel(50,37);
        break;
	case 'travel5':
        travel(100,38);
        break;
	case 'dam1':
        damage(1000,39);
        break;
	case 'dam2':
        damage(25000,40);
        break;
	case 'dam3':
        damage(100000,41);
        break;
	case 'dam4':
        damage(1000000,42);
        break;
	case 'dam5':
        damage(25000000,43);
        break;
	case 'worth1':
        worth(500000,44);
        break;
	case 'worth2':
        worth(5000000,45);
        break;
	case 'worth3':
        worth(50000000,46);
        break;
	case 'worth4':
        worth(500000000,47);
        break;
	case 'worth5':
        worth(1000000000,48);
        break;
	case 'posts1':
        posts(5,52);
        break;
	case 'posts2':
        posts(25,53);
        break;
	case 'posts3':
        posts(75,54);
        break;
	case 'posts4':
        posts(500,55);
        break;
	case 'posts5':
        posts(1000,56);
        break;
    case 'dayslogged1':
        dayslogged(7,57);
        break;
    case 'dayslogged2':
        dayslogged(14,58);
        break;
    case 'dayslogged3':
        dayslogged(30,59);
        break;
    case 'dayslogged4':
        dayslogged(120,60);
        break;
    case 'dayslogged5':
        dayslogged(365,61);
        break;
    default:
        home();
        break;
}
function home()
{
	global $h;
	echo "Here's a list of in-game achievements. Click on an achievement to be rewarded with it. You may only 
	be rewarded once per achievement. Each achievement you complete will get you 1 skill point.";
	$count=1;
	while ($count != 62)
	{
		$class[$count]= (userHasAchievement($count)) ? "class='text-success'" : "class='text-danger font-weight-bold'" ;
		$count=$count+1;
	}
	echo "
	<div class='row'>
		<div class='col-sm'>
			<u><b>Level</b></u><br />
			<a {$class[1]} href='?action=level1'>Level 5</a><br />
			<a {$class[2]} href='?action=level2'>Level 25</a><br />
			<a {$class[49]} href='?action=level6'>Level 50</a><br />
			<a {$class[3]} href='?action=level3'>Level 100</a><br />
			<a {$class[50]} href='?action=level7'>Level 150</a><br />
			<a {$class[4]} href='?action=level4'>Level 200</a><br />
			<a {$class[5]} href='?action=level5'>Level 300</a><br />
			<a {$class[51]} href='?action=leve8'>Level 500</a><br />
		</div>
		<div class='col-sm'>
			<u><b>Mining Level</b></u><br />
			<a {$class[11]} href='?action=mine1'>Mining Level 10</a><br />
			<a {$class[12]} href='?action=mine2'>Mining Level 20</a><br />
			<a {$class[13]} href='?action=mine3'>Mining Level 50</a><br />
		</div>
		<div class='col-sm'>
			<u><b>Busts</b></u><br />
			<a {$class[6]} href='?action=bust1'>25 Busts</a><br />
			<a {$class[7]} href='?action=bust2'>100 Busts</a><br />
			<a {$class[8]} href='?action=bust3'>250 Busts</a><br />
			<a {$class[9]} href='?action=bust4'>500 Busts</a><br />
			<a {$class[10]} href='?action=bust5'>1,000 Busts</a><br />
		</div>
	</div>
	<div class='row'>
		<div class='col-sm'>
			<u><b>Kills</b></u><br />
			<a {$class[14]} href='?action=kill1'>10 Kills</a><br />
			<a {$class[15]} href='?action=kill2'>50 Kills</a><br />
			<a {$class[16]} href='?action=kill3'>100 Kills</a><br />
			<a {$class[17]} href='?action=kill4'>500 Kills</a><br />
			<a {$class[18]} href='?action=kill5'>1,000 Kills</a><br />
		</div>
		<div class='col-sm'>
			<u><b>Deaths</b></u><br />
			<a {$class[19]} href='?action=death1'>10 Deaths</a><br />
			<a {$class[20]} href='?action=death2'>50 Deaths</a><br />
			<a {$class[21]} href='?action=death3'>100 Deaths</a><br />
			<a {$class[22]} href='?action=death4'>500 Deaths</a><br />
			<a {$class[23]} href='?action=death5'>1,000 Deaths</a><br />
		</div>
		<div class='col-sm'>
			<u><b>Referrals</b></u><br />
			<a {$class[24]} href='?action=refer1'>1 Referral</a><br />
			<a {$class[25]} href='?action=refer2'>5 Referrals</a><br />
			<a {$class[26]} href='?action=refer3'>10 Referrals</a><br />
			<a {$class[27]} href='?action=refer4'>50 Referrals</a><br />
			<a {$class[28]} href='?action=refer5'>100 Referrals</a><br />
		</div>
	</div>
	<div class='row'>
		<div class='col-sm'>
			<u><b>Copper Coins from Crimes</b></u><br />
			<a {$class[29]} href='?action=crimec1'>500,000</a><br />
			<a {$class[30]} href='?action=crimec2'>1,000,000</a><br />
			<a {$class[31]} href='?action=crimec3'>5,000,000</a><br />
			<a {$class[32]} href='?action=crimec4'>25,000,000</a><br />
			<a {$class[33]} href='?action=crimec5'>100,000,000</a><br />
		</div>
		<div class='col-sm'>
			<u><b>Travel</b></u><br />
			<a {$class[34]} href='?action=travel1'>1 Time</a><br />
			<a {$class[35]} href='?action=travel2'>5 Times</a><br />
			<a {$class[36]} href='?action=travel3'>10 Times</a><br />
			<a {$class[37]} href='?action=travel4'>50 Times</a><br />
			<a {$class[38]} href='?action=travel5'>100 Times</a><br />
		</div>
		<div class='col-sm'>
			<u><b>Total Attack Damage</b></u><br />
			<a {$class[39]} href='?action=dam1'>1,000 Damage</a><br />
			<a {$class[40]} href='?action=dam2'>25,000 Damage</a><br />
			<a {$class[41]} href='?action=dam3'>100,000 Damage</a><br />
			<a {$class[42]} href='?action=dam4'>1,000,000 Damage</a><br />
			<a {$class[43]} href='?action=dam5'>25,000,000 Damage</a><br />
		</div>
	</div>
	<div class='row'>
		<div class='col-sm'>
			<u><b>Net Worth</b></u><br />
			<a {$class[44]} href='?action=worth1'>500,000</a><br />
			<a {$class[45]} href='?action=worth2'>5,000,000</a><br />
			<a {$class[46]} href='?action=worth3'>50,000,000</a><br />
			<a {$class[47]} href='?action=worth4'>500,000,000</a><br />
			<a {$class[48]} href='?action=worth5'>1,000,000,000</a><br />
		</div>
		<div class='col-sm'>
			<u><b>Forum Posts</b></u><br />
			<a {$class[52]} href='?action=posts1'>5 Posts</a><br />
			<a {$class[53]} href='?action=posts2'>25 Posts</a><br />
			<a {$class[54]} href='?action=posts3'>75 Posts</a><br />
			<a {$class[55]} href='?action=posts4'>500 Posts</a><br />
			<a {$class[56]} href='?action=posts5'>1,000 Posts</a><br />
		</div>
		<div class='col-sm'>
            <u><b>Consecutive Days Logged In</b></u><br />
			<a {$class[57]} href='?action=dayslogged1'>7 Days</a><br />
			<a {$class[58]} href='?action=dayslogged2'>14 Days</a><br />
			<a {$class[59]} href='?action=dayslogged3'>30 Days</a><br />
			<a {$class[60]} href='?action=dayslogged4'>120 Days</a><br />
			<a {$class[61]} href='?action=dayslogged5'>365 Days</a><br />
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
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	alert('success',"Success!","You have successfully achieved the Level {$level} achievement and were rewarded {$tokens} Copper Coins.",true,'achievements.php');
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
		alert('danger',"Uh Oh!","Your bust count is too low to receive this achievement. You need to have at least {$level} busts. You only have {$ir['busts']}.",true,'achievements.php');
		die($h->endpage());
	}
	$db->query("INSERT INTO `achievements_done` (`userid`, `achievement`) VALUES ('{$userid}', '{$id}')");
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for total busts {$level}.");
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
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
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	alert('success',"Success!","You have successfully achieved the Mining Level {$level} achievement and were rewarded {$tokens} Copper Coins.",true,'achievements.php');
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
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	alert('success',"Success!","You have successfully achieved the {$level} kills achievement and were rewarded {$tokens} Copper Coins.",true,'achievements.php');
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
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	alert('success',"Success!","You have successfully achieved the {$level} deaths achievement and were rewarded {$tokens} Copper Coins.",true,'achievements.php');
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
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	alert('success',"Success!","You have successfully achieved the {$level} referrals achievement and were rewarded {$tokens} Copper Coins.",true,'achievements.php');
	$h->endpage();
}
function crimecopper($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	$crimecopper=$db->fetch_single($db->query("SELECT SUM(`crimecopper`) FROM `crime_logs` WHERE `userid` = {$userid}"));
	if ($crimecopper < $level)
	{
		alert('danger',"Uh Oh!","You haven't gained enough Copper Coins from crimes yet to receive this achievement. You've only stole {$crimecopper}",true,'achievements.php');
		die($h->endpage());
	}
	$db->query("INSERT INTO `achievements_done` (`userid`, `achievement`) VALUES ('{$userid}', '{$id}')");
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for copper crimers {$level}.");
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	alert('success',"Success!","You have successfully achieved the stolen {$level} Copper Coins achievement and were rewarded {$tokens} Copper Coins.",true,'achievements.php');
	$h->endpage();
}
function travel($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	$crimecopper=$db->fetch_single($db->query("SELECT SUM(`value`) FROM `user_logging` WHERE `userid` = {$userid} AND `log_name` = 'travel'"));
	if (empty($crimecopper))
		$crimecopper=0;
	if ($crimecopper < $level)
	{
		alert('danger',"Uh Oh!","You haven't traveled enough yet to receive this achievement. You've only traveled {$crimecopper} times.",true,'achievements.php');
		die($h->endpage());
	}
	$db->query("INSERT INTO `achievements_done` (`userid`, `achievement`) VALUES ('{$userid}', '{$id}')");
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for traveling {$level} times.");
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	alert('success',"Success!","You have successfully achieved the Travel {$level} times achievement and were rewarded {$tokens} Copper Coins.",true,'achievements.php');
	$h->endpage();
}
function damage($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	$crimecopper=$db->fetch_single($db->query("SELECT SUM(`value`) FROM `user_logging` WHERE `userid` = {$userid} AND `log_name` = 'dmgdone'"));
	if (empty($crimecopper))
		$crimecopper=0;
	if ($crimecopper < $level)
	{
		alert('danger',"Uh Oh!","You haven't dealt out enough damage yet to receive this achievement. You've only dealt out {$crimecopper} damage.",true,'achievements.php');
		die($h->endpage());
	}
	$db->query("INSERT INTO `achievements_done` (`userid`, `achievement`) VALUES ('{$userid}', '{$id}')");
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for {$level} damage dealt.");
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	alert('success',"Success!","You have successfully achieved the {$level} damage dealt achievement and were rewarded {$tokens} Copper Coins.",true,'achievements.php');
	$h->endpage();
}
function worth($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	$worth=$ir['primary_currency']+$ir['bank']+$ir['bigbank']+($ir['secondary_currency']*1000)+($ir['tokenbank']*1000);
	if (empty($worth))
		$worth=0;
	if ($worth < $level)
	{
		alert('danger',"Uh Oh!","Your worth is too low to recieve this achievement. You only have {$worth} net worth. Reminder this achievement only counts your Copper Coins and Chivalry Tokens.",true,'achievements.php');
		die($h->endpage());
	}
	$db->query("INSERT INTO `achievements_done` (`userid`, `achievement`) VALUES ('{$userid}', '{$id}')");
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for {$level} Net Worth.");
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	alert('success',"Success!","You have successfully achieved the {$level} Net Worth achievement and were rewarded {$tokens} Copper Coins.",true,'achievements.php');
	$h->endpage();
}
function posts($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	$posts=$db->fetch_single($db->query("SELECT COUNT('fp_id') FROM `forum_posts` WHERE `fp_poster_id`={$userid}"));
	if (empty($posts))
		$posts=0;
	if ($posts < $level)
	{
		alert('danger',"Uh Oh!","You haven't posted enough to receive this achievement. You've only posted {$posts} times.",true,'achievements.php');
		die($h->endpage());
	}
	$db->query("INSERT INTO `achievements_done` (`userid`, `achievement`) VALUES ('{$userid}', '{$id}')");
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for {$level} forum posts.");
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	alert('success',"Success!","You have successfully achieved the {$level} forum posts achievement and were rewarded {$tokens} Copper Coins.",true,'achievements.php');
	$h->endpage();
}
function dayslogged($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	$posts=$ir['dayslogged'];
	if (empty($posts))
		$posts=0;
	if ($posts < $level)
	{
		alert('danger',"Uh Oh!","You do not have enough days logged in consecutively to receive this award. Your current record is {$posts} days.",true,'achievements.php');
		die($h->endpage());
	}
	$db->query("INSERT INTO `achievements_done` (`userid`, `achievement`) VALUES ('{$userid}', '{$id}')");
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for {$level} days logged in consecutively.");
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	alert('success',"Success!","You have successfully achieved the {$level} days logged in consecutively achievement and were rewarded {$tokens} Copper Coins.",true,'achievements.php');
	$h->endpage();
}
function givePoint($userid)
{
	global $db,$userid;
	$db->query("UPDATE `user_settings` SET `skill_points` = `skill_points` + 1 WHERE `userid` = {$userid}");
}
function userHasAchievement($id)
{
	global $db,$userid;
	$achieved=$db->query("SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
		return true;
}
