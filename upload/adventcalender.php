<?php
//ALTER TABLE `advent_calender` ADD `date` VARCHAR(255) NOT NULL AFTER `userid`;
//ALTER TABLE `advent_calender` DROP `day`;
require('globals.php');
if (date('n') != 12)
{
	alert("danger","Uh Oh!","Its not the holidays...",true,'explore.php');
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
	alert('info',"","Find today's date, and open the spot for your prize! You may not see the image on either Castle or Sunset themes.",false);
	echo "
	<table height='600' width='996' class='table table-bordered' background='https://farmeramaaid.files.wordpress.com/2011/11/advent-calendar-wallpaper.jpg'>
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
	</table>";
	$h->endpage();
	//clearTable();
}
function day($today)
{
	global $db,$h,$userid,$api;
	$year = date('Y');
	$day = date('j');
	$adventDateFormat = "{$year}-{$day}";
	if ($day != $today)
	{
		alert('danger',"Uh Oh!","You cannot open this calender spot today.",true,'adventcalender.php');
		die($h->endpage());
	}
	//Already got today's prize?
	$q=$db->query("/*qc=on*/SELECT * FROM `advent_calender` WHERE `userid` = {$userid} and `date` = '{$adventDateFormat}'");
	if ($db->num_rows($q) != 0)
	{
		alert('danger',"Uh Oh!","It appears you've already taken today's reward. Come back tomorrow!",true,'adventcalender.php');
		die($h->endpage());
	}
	$db->query("INSERT INTO `advent_calender` (`userid`, `date`) VALUES ('{$userid}', '{$adventDateFormat}')");
	if ($day == 1)
	{
		alert('success',"Success!","You open up today's calender spot and receive a bonus 125 Will at your current estate.",true,'adventcalender.php');
		increaseMaxWill($userid, 125);
	}
	if ($day == 2)
	{
		alert('success',"Success!","You open up today's calender spot and receive 50 Dungeon Key Sets.",true,'adventcalender.php');
		$api->UserGiveItem($userid,31,50);
	}
	if ($day == 3)
	{
	    $bomb = Random(5,15);
		alert('success',"Success!","You open up today's calender spot and receive {$bomb} Medium Explosives.",true,'adventcalender.php');
		$api->UserGiveItem($userid,61,$bomb);
	}
	if ($day == 4)
	{
		alert('success',"Success!","You open up today's calender spot and receive 1 Large Explosive.",true,'adventcalender.php');
		$api->UserGiveItem($userid,62,1);
	}
	if ($day == 5)
	{
	    $copper = Random(93750000,156250000);
	    alert('success',"Success!","You open up today's calender spot and receive " . shortNumberParse($copper) . " Copper Coins.",true,'adventcalender.php');
		$api->UserGiveCurrency($userid,'primary',$copper);
	}
	if ($day == 6)
	{
	    $tokens = Random(18750,31250);
		alert('success',"Success!","You open up today's calender spot and receive " . shortNumberParse($tokens) . " Chivalry Tokens.",true,'adventcalender.php');
		$api->UserGiveCurrency($userid,'secondary',$tokens);
	}
	if ($day == 7)
	{
	    $will = Random(150,450);
		alert('success',"Success!","You open up today's calender spot and receive a bonus {$will} Will at your current estate.",true,'adventcalender.php');
		increaseMaxWill($userid, $will);
	}
	if ($day == 8)
	{
	    $will = Random(375,937);
		alert('success',"Success!","You open up today's calender spot and receive a bonus {$will} Will at your current estate.",true,'adventcalender.php');
		increaseMaxWill($userid, $will);
	}
	if ($day == 9)
	{
	    $scrolls = Random(12,39);
		alert('success',"Success!","You open up today's calender spot and receive {$scrolls} CID Admin Gym Access Scroll.",true,'adventcalender.php');
		$api->UserGiveItem($userid,205,25);
	}
	if ($day == 10)
	{
	    $candy = Random(50,150);
		alert('success',"Success!","You open up today's calender spot and receive {$candy} Candy Canes.",true,'adventcalender.php');
		$api->UserGiveItem($userid,201,100);
	}
	if ($day == 11)
	{
	    $gems = Random(87,263);
		alert('success',"Success!","You open up today's calender spot and receive {$gems} Flawed Rubies.",true,'adventcalender.php');
		$api->UserGiveItem($userid,45,175);
	}
	if ($day == 12)
	{
	    $gems = Random(7,22);
		alert('success',"Success!","You open up today's calender spot and receive {$gems} Small Sapphires.",true,'adventcalender.php');
		$api->UserGiveItem($userid,162,15);
	}
	if ($day == 13)
	{
	    $potion = Random(7,22);
		alert('success',"Success!","You open up today's calender spot and receive {$potion} Mining Energy Potions.",true,'adventcalender.php');
		$api->UserGiveItem($userid,227,15);
	}
	if ($day == 14)
	{
	    $will = Random(750,1250);
		alert('success',"Success!","You open up today's calender spot and receive a bonus " . shortNumberParse($will) . " Will at your current estate.",true,'adventcalender.php');
		increaseMaxWill($userid, $will);
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
	    $count = Random(175,325);
		alert('success',"Success!","You open up today's calender spot and receive {$count} Acupuncture Needles.",true,'adventcalender.php');
		$api->UserGiveItem($userid,100,$count);
	}
	if ($day == 18)
	{
	    $count = Random(25,75);
		alert('success',"Success!","You open up today's calender spot and receive {$count} Medical Packages.",true,'adventcalender.php');
		$api->UserGiveItem($userid,216,$count);
	}
	if ($day == 19)
	{
	    $count = Random(3,10);
		alert('success',"Success!","You open up today's calender spot and receive {$count} Large Health Potions.",true,'adventcalender.php');
		$api->UserGiveItem($userid,9,$count);
	}
	if ($day == 20)
	{
	    $count = Random(13,50);
		alert('success',"Success!","You open up today's calender spot and receive {$count} Herbs of the Enlightened Miner.",true,'adventcalender.php');
		$api->UserGiveItem($userid,177,25);
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
	    $count = Random(731,1553);
		alert('success',"Success!","You open up today's calender spot and receive a bonus " . shortNumberParse($count) . " Will at your current estate.",true,'adventcalender.php');
		increaseMaxWill($userid, $count);
	}
	if ($day == 25)
	{
		alert('success',"Success!","You open up today's calender spot and receive a $1 VIP Packs. Merry Christmas!",true,'adventcalender.php');
		$api->UserGiveItem($userid,12,1);
	}
	$h->endpage();
}

function clearTable()
{
	global $db;
	$db->query("TRUNCATE TABLE `advent_calender`");
}
