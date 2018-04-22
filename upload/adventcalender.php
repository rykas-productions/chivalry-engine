<?php
require('globals.php');
if (time() < 1512104400)
{
	$until=TimeUntil_Parse(1512104400);
	alert("danger","Uh Oh!","The advent calender cannot be used for another {$until}.",true,'explore.php');
	die($h->endpage());
}
if (time() > 1514264400)
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
	<table height='600' width='996' class='table table-bordered' background='assets/img/christmas/advent-calender-1110.jpg'>
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
		alert('success',"Success!","You open up today's calender spot and receive 25 Bread.",true,'adventcalender.php');
		$api->UserGiveItem($userid,19,25);
	}
	if ($day == 2)
	{
		alert('success',"Success!","You open up today's calender spot and receive 50 Lockpicks.",true,'adventcalender.php');
		$api->UserGiveItem($userid,29,50);
	}
	if ($day == 3)
	{
		alert('success',"Success!","You open up today's calender spot and receive 10 Explosives.",true,'adventcalender.php');
		$api->UserGiveItem($userid,28,10);
	}
	if ($day == 4)
	{
		alert('success',"Success!","You open up today's calender spot and receive 1 Medium Sized Explosive.",true,'adventcalender.php');
		$api->UserGiveItem($userid,61,1);
	}
	if ($day == 5)
	{
		alert('success',"Success!","You open up today's calender spot and receive 50,000 Copper Coins.",true,'adventcalender.php');
		$api->UserGiveCurrency($userid,'primary',50000);
	}
	if ($day == 6)
	{
		alert('success',"Success!","You open up today's calender spot and receive 250 Chivalry Tokens.",true,'adventcalender.php');
		$api->UserGiveCurrency($userid,'secondary',250);
	}
	if ($day == 7)
	{
		alert('success',"Success!","You open up today's calender spot and receive 10 Sharpened Sticks.",true,'adventcalender.php');
		$api->UserGiveItem($userid,1,10);
	}
	if ($day == 8)
	{
		alert('success',"Success!","You open up today's calender spot and receive 100 Copper Flakes.",true,'adventcalender.php');
		$api->UserGiveItem($userid,23,100);
	}
	if ($day == 9)
	{
		alert('success',"Success!","You open up today's calender spot and receive 2 Chivalry Gym Passes.",true,'adventcalender.php');
		$api->UserGiveItem($userid,18,2);
	}
	if ($day == 10)
	{
		alert('success',"Success!","You open up today's calender spot and receive 50 Venison.",true,'adventcalender.php');
		$api->UserGiveItem($userid,20,50);
	}
	if ($day == 11)
	{
		alert('success',"Success!","You open up today's calender spot and receive 20 Coal.",true,'adventcalender.php');
		$api->UserGiveItem($userid,22,20);
	}
	if ($day == 12)
	{
		alert('success',"Success!","You open up today's calender spot and receive 1 Flawed Sapphire.",true,'adventcalender.php');
		$api->UserGiveItem($userid,25,1);
	}
	if ($day == 13)
	{
		alert('success',"Success!","You open up today's calender spot and receive 7 Cornrye Ale.",true,'adventcalender.php');
		$api->UserGiveItem($userid,11,7);
	}
	if ($day == 14)
	{
		alert('success',"Success!","You open up today's calender spot and receive 14 Halloween Candy.",true,'adventcalender.php');
		$api->UserGiveItem($userid,66,14);
	}
	if ($day == 15)
	{
		alert('success',"Success!","You open up today's calender spot and receive 25 Heavy Rocks.",true,'adventcalender.php');
		$api->UserGiveItem($userid,2,25);
	}
	if ($day == 16)
	{
		alert('success',"Success!","You open up today's calender spot and receive a Large Sized Explosive.",true,'adventcalender.php');
		$api->UserGiveItem($userid,62,1);
	}
	if ($day == 17)
	{
		alert('success',"Success!","You open up today's calender spot and receive 50 Leeches.",true,'adventcalender.php');
		$api->UserGiveItem($userid,5,50);
	}
	if ($day == 18)
	{
		alert('success',"Success!","You open up today's calender spot and receive 50 Linen Wraps.",true,'adventcalender.php');
		$api->UserGiveItem($userid,6,50);
	}
	if ($day == 19)
	{
		alert('success',"Success!","You open up today's calender spot and receive 5 Medium Health Potions.",true,'adventcalender.php');
		$api->UserGiveItem($userid,8,5);
	}
	if ($day == 20)
	{
		alert('success',"Success!","You open up today's calender spot and receive 50 Keys.",true,'adventcalender.php');
		$api->UserGiveItem($userid,30,50);
	}
	if ($day == 21)
	{
		alert('success',"Success!","You open up today's calender spot and receive 10 Key Sets.",true,'adventcalender.php');
		$api->UserGiveItem($userid,31,10);
	}
	if ($day == 22)
	{
		alert('success',"Success!","You open up today's calender spot and receive a VIP Scratch Ticket.",true,'adventcalender.php');
		$api->UserGiveItem($userid,89,1);
	}
	if ($day == 23)
	{
		alert('success',"Success!","You open up today's calender spot and receive 5 Small Cash Boosts.",true,'adventcalender.php');
		$api->UserGiveItem($userid,27,5);
	}
	if ($day == 24)
	{
		alert('success',"Success!","You open up today's calender spot and receive 50 Boxes of Random.",true,'adventcalender.php');
		$api->UserGiveItem($userid,33,50);
	}
	if ($day == 25)
	{
		alert('success',"Success!","You open up today's calender spot and receive 1 $1 VIP Pack. Merry Christmas!",true,'adventcalender.php');
		$api->UserGiveItem($userid,12,1);
	}
	$h->endpage();
}
