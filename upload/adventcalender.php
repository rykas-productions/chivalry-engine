<?php
require('globals.php');
if (time() < 1575176400)
{
	$until=TimeUntil_Parse(1575176400);
	alert("danger","Uh Oh!","The advent calender cannot be used for another {$until}.",true,'explore.php');
	die($h->endpage());
}
if (time() > 1577336400)
{
	alert("danger","Uh Oh!","The advent calender cannot be used after Christmas.",true,'explore.php');
	die($h->endpage());
}
if (!isset($_GET['day'])) {
    $_GET['day'] = '';
}
switch ($_GET['day']) {
    case "1":
        day(1);
        break;
	case "2":
        day(2);
        break;
	case "3":
        day(3);
        break;
	case "4":
        day(4);
        break;
	case "5":
        day(5);
        break;
	case "6":
        day(6);
        break;
	case "7":
        day(7);
        break;
	case "8":
        day(8);
        break;
	case "9":
        day(9);
        break;
	case "10":
        day(10);
        break;
	case "11":
        day(11);
        break;
	case "12":
        day(12);
        break;
	case "13":
        day(13);
        break;
	case "14":
        day(14);
        break;
	case "15":
        day(15);
        break;
	case "16":
        day(16);
        break;
	case "17":
        day(17);
        break;
	case "18":
        day(18);
        break;
	case "19":
        day(19);
        break;
	case "20":
        day(20);
        break;
	case "21":
        day(21);
        break;
	case "22":
        day(22);
        break;
	case "23":
        day(23);
        break;
	case "24":
        day(24);
        break;
	case "25":
        day(25);
        break;
    default:
        home();
        break;
}
function home()
{
	global $h;
	echo "Find today's date, and open the spot for your prize!
	<br />
	<table height='600' width='996' class='table table-bordered' background='https://farmeramaaid.files.wordpress.com/2011/11/advent-calendar-wallpaper.jpg'>
		<tr>
			<td>
			<a href='?day=11'>11th</a>
			</td>
			<td>
			<a href='?day=17'>17th</a>
			</td>
			<td>
			<a href='?day=21'>21st</a>
			</td>
			<td>
			<a href='?day=12'>12th</a>
			</td>
			<td>
			<a href='?day=4'>4th</a>
			</td>
		</tr>
		<tr>
			<td>
			<a href='?day=23'>23rd</a>
			</td>
			<td>
			<a href='?day=8'>8th</a>
			</td>
			<td>
			<a href='?day=13'>13th</a>
			</td>
			<td>
			<a href='?day=10'>10th</a>
			</td>
			<td>
			<a href='?day=25'>25th</a>
			</td>
		</tr>
		<tr>
			<td>
			<a href='?day=22'>22nd</a>
			</td>
			<td>
			<a href='?day=20'>20th</a>
			</td>
			<td>
			<a href='?day=7'>7th</a>
			</td>
			<td>
			<a href='?day=5'>5th</a>
			</td>
			<td>
			<a href='?day=3'>3rd</a>
			</td>
		</tr>
		<tr>
			<td>
			<a href='?day=2'>2nd</a>
			</td>
			<td>
			<a href='?day=18'>18th</a>
			</td>
			<td>
			<a href='?day=14'>14th</a>
			</td>
			<td>
			<a href='?day=15'>15th</a>
			</td>
			<td>
			<a href='?day=1'>1st</a>
			</td>
		</tr>
		<tr>
			<td>
			<a href='?day=6'>6th</a>
			</td>
			<td>
			<a href='?day=24'>24th</a>
			</td>
			<td>
			<a href='?day=19'>19th</a>
			</td>
			<td>
			<a href='?day=16'>16th</a>
			</td>
			<td>
			<a href='?day=9'>9th</a>
			</td>
		</tr>
	</table>";
	$h->endpage();
}
function day($today)
{
	global $db,$h,$userid,$api;
	$day=Date('j', time());
	if ($day != $today)
	{
		alert('danger',"Uh Oh!","You cannot open this calender spot today.",true,'adventcalender.php');
		die($h->endpage());
	}
	//Already got today's prize?
	$q=$db->query("/*qc=on*/SELECT * FROM `advent_calender` WHERE `userid` = {$userid} and `day` = {$day}");
	if ($db->num_rows($q) != 0)
	{
		alert('danger',"Uh Oh!","It appears you've already taken today's reward. Come back tomorrow!",true,'adventcalender.php');
		die($h->endpage());
	}
	$db->query("INSERT INTO `advent_calender` (`userid`, `day`) VALUES ('{$userid}', '{$day}')");
	if ($day == 1)
	{
		alert('success',"Success!","You open up today's calender spot and receive 25 Pumpkin Pie.",true,'adventcalender.php');
		$api->UserGiveItem($userid,60,25);
	}
	if ($day == 2)
	{
		alert('success',"Success!","You open up today's calender spot and receive 50 Dungeon Key Sets.",true,'adventcalender.php');
		$api->UserGiveItem($userid,31,50);
	}
	if ($day == 3)
	{
		alert('success',"Success!","You open up today's calender spot and receive 10 Medium Explosives.",true,'adventcalender.php');
		$api->UserGiveItem($userid,61,10);
	}
	if ($day == 4)
	{
		alert('success',"Success!","You open up today's calender spot and receive 1 Large Explosive.",true,'adventcalender.php');
		$api->UserGiveItem($userid,62,1);
	}
	if ($day == 5)
	{
		alert('success',"Success!","You open up today's calender spot and receive 10,000,000 Copper Coins.",true,'adventcalender.php');
		$api->UserGiveCurrency($userid,'primary',10000000);
	}
	if ($day == 6)
	{
		alert('success',"Success!","You open up today's calender spot and receive 7,500 Chivalry Tokens.",true,'adventcalender.php');
		$api->UserGiveCurrency($userid,'secondary',7500);
	}
	if ($day == 7)
	{
		alert('success',"Success!","You open up today's calender spot and receive 3 Will Stimulant Potions.",true,'adventcalender.php');
		$api->UserGiveItem($userid,263,3);
	}
	if ($day == 8)
	{
		alert('success',"Success!","You open up today's calender spot and receive 1,000 Iron Flakes.",true,'adventcalender.php');
		$api->UserGiveItem($userid,57,1000);
	}
	if ($day == 9)
	{
		alert('success',"Success!","You open up today's calender spot and receive 5 CID Admin Gym Access Scroll.",true,'adventcalender.php');
		$api->UserGiveItem($userid,205,5);
	}
	if ($day == 10)
	{
		alert('success',"Success!","You open up today's calender spot and receive 50 Candy Canes.",true,'adventcalender.php');
		$api->UserGiveItem($userid,201,50);
	}
	if ($day == 11)
	{
		alert('success',"Success!","You open up today's calender spot and receive 75 Flawed Rubies.",true,'adventcalender.php');
		$api->UserGiveItem($userid,45,75);
	}
	if ($day == 12)
	{
		alert('success',"Success!","You open up today's calender spot and receive 5 Small Sapphires.",true,'adventcalender.php');
		$api->UserGiveItem($userid,162,5);
	}
	if ($day == 13)
	{
		alert('success',"Success!","You open up today's calender spot and receive 7 Mining Energy Potions.",true,'adventcalender.php');
		$api->UserGiveItem($userid,227,7);
	}
	if ($day == 14)
	{
		alert('success',"Success!","You open up today's calender spot and receive 14 Turkey Legs.",true,'adventcalender.php');
		$api->UserGiveItem($userid,87,14);
	}
	if ($day == 15)
	{
		alert('success',"Success!","You open up today's calender spot and receive a Dapper Doge badge.",true,'adventcalender.php');
		$api->UserGiveItem($userid,262,1);
	}
	if ($day == 16)
	{
		alert('success',"Success!","You open up today's calender spot and receive 5 Rickity Bombs.",true,'adventcalender.php');
		$api->UserGiveItem($userid,149,5);
	}
	if ($day == 17)
	{
		alert('success',"Success!","You open up today's calender spot and receive 250 Acupuncture Needles.",true,'adventcalender.php');
		$api->UserGiveItem($userid,100,250);
	}
	if ($day == 18)
	{
		alert('success',"Success!","You open up today's calender spot and receive 50 Medical Packages.",true,'adventcalender.php');
		$api->UserGiveItem($userid,216,50);
	}
	if ($day == 19)
	{
		alert('success',"Success!","You open up today's calender spot and receive 5 Large Health Potions.",true,'adventcalender.php');
		$api->UserGiveItem($userid,9,5);
	}
	if ($day == 20)
	{
		alert('success',"Success!","You open up today's calender spot and receive 5 Herbs of the Enlightened Miner.",true,'adventcalender.php');
		$api->UserGiveItem($userid,177,5);
	}
	if ($day == 21)
	{
		alert('success',"Success!","You open up today's calender spot and receive a Voucher for Cheaper Travel.",true,'adventcalender.php');
		$api->UserGiveItem($userid,269,1);
	}
	if ($day == 22)
	{
		alert('success',"Success!","You open up today's calender spot and receive a VIP Scratch Ticket.",true,'adventcalender.php');
		$api->UserGiveItem($userid,89,1);
	}
	if ($day == 23)
	{
		alert('success',"Success!","You open up today's calender spot and receive a Baetylus.",true,'adventcalender.php');
		$api->UserGiveItem($userid,152,1);
	}
	if ($day == 24)
	{
		alert('success',"Success!","You open up today's calender spot and receive 5,000 Boxes of Random.",true,'adventcalender.php');
		$api->UserGiveItem($userid,33,5000);
	}
	if ($day == 25)
	{
		alert('success',"Success!","You open up today's calender spot and receive two $1 VIP Packs. Merry Christmas!",true,'adventcalender.php');
		$api->UserGiveItem($userid,12,2);
	}
	$h->endpage();
}
