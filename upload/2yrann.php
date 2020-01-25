<?php
require('globals.php');
if (time() < 1571544000)
{
	$until=TimeUntil_Parse(1571544000);
	alert("danger","Uh Oh!","This event cannot be used for another {$until}.",true,'explore.php');
	die($h->endpage());
}
if (time() > 1572148800)
{
	alert("danger","Uh Oh!","This event is over.",true,'explore.php');
	die($h->endpage());
}
echo "<h3>2nd Year of Chivlary is Dead!</h3><hr />";
$day=date('j');
if (!isset($_GET['day'])) {
    $_GET['day'] = '';
}
switch ($_GET['day']) {
    case "1":
        day(20);
        break;
	case "2":
        day(21);
        break;
	case "3":
        day(22);
        break;
	case "4":
        day(23);
        break;
	case "5":
        day(24);
        break;
	case "6":
        day(25);
        break;
	case "7":
        day(26);
        break;
    default:
        home();
        break;
}
function home()
{
	echo "You like free things? So do we! In celebration of Chivalry is Dead reaching its 2nd year of life, we'll be giving away 
	freebies to players who log in each and every day. Rewards are sweet, I promise!<br />
	To wet that appetite of yours, we're going to be nice and list you the items you 
	can get! Come back and claim your prizes each and every day! Prizes may be claimed 
	only once.<hr />
	<a href='?day=1'>Oct 20th - 2nd Year Here Badge</a><br />
	<a href='?day=2'>Oct 21th - CID Scratch Ticket</a><br />
	<a href='?day=3'>Oct 22th - 5 CID Admin Gym Scrolls</a><br />
	<a href='?day=4'>Oct 23th - 25 Further Attack Scrolls</a><br />
	<a href='?day=5'>Oct 24th - 3 Will Stimulant Potions</a><br />
	<a href='?day=6'>Oct 25th - Tome of Experience</a><br />
	<a href='?day=7'>Oct 26th - Mystery Item!</a>";
}
function day($input)
{
	global $db,$userid,$api,$h,$day,$ir;
	if ($input != $day)
	{
		alert('danger',"Uh Oh!","You may not claim a reward for a day that is not today.",true,'1yrann.php');
		die($h->endpage());
	}
	if ($ir['holiday'] != 0)
	{
		alert('danger',"Uh Oh!","You've already claimed today's reward.",true,'1yrann.php');
		die($h->endpage());
	}
	$qty=1;
	if ($input == 20)
		$itmid=267;
	if ($input == 21)
		$itmid=210;
	if ($input == 22)
	{
		$itmid=205;
		$qty=5;
	}
	if ($input == 23)
	{
		$itmid=247;
		$qty=25;
	}
	if ($input == 24)
	{
		$itmid=263;
		$qty=3;
	}
	if ($input == 25)
		$itmid=148;
	if ($input == 26)
		$itmid=266;
	$db->query("UPDATE `user_settings` SET `holiday` = 1 WHERE `userid` = {$userid}");
	$api->UserGiveItem($userid,$itmid,$qty);
	alert('success',"Success!","You have claimed your {$api->SystemItemIDtoName($itmid)}!",true,'explore.php');
}
$h->endpage();