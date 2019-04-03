<?php
require('globals.php');
if (time() < 1539489600)
{
	$until=TimeUntil_Parse(1539489600);
	alert("danger","Uh Oh!","This event cannot be used for another {$until}.",true,'explore.php');
	die($h->endpage());
}
if (time() > 1540094400)
{
	alert("danger","Uh Oh!","This event is over.",true,'explore.php');
	die($h->endpage());
}
echo "<h3>1 Year of Chivlary!</h3><hr />";
$day=date('j');
if (!isset($_GET['day'])) {
    $_GET['day'] = '';
}
switch ($_GET['day']) {
    case "1":
        day(14);
        break;
	case "2":
        day(15);
        break;
	case "3":
        day(16);
        break;
	case "4":
        day(17);
        break;
	case "5":
        day(18);
        break;
	case "6":
        day(19);
        break;
	case "7":
        day(20);
        break;
    default:
        home();
        break;
}
function home()
{
	echo "You like free things? So do we! So, from now until the 20th, we'll be giving away freebies to players who log in each adn every day. Rewards are sweet, I promise!<br />
	To wet that appetite of yours, we're going to be nice and list you the items you can get! Come back and claim your prizes each and every day! Prizes may be claimed only once.<hr />
	<a href='?day=1'>Oct 14th - Experience Coin</a><br />
	<a href='?day=2'>Oct 15th - Lightning Spell</a><br />
	<a href='?day=3'>Oct 16th - Baetylus</a><br />
	<a href='?day=4'>Oct 17th - Magical Aura</a><br />
	<a href='?day=5'>Oct 18th - 5 Large Explosives</a><br />
	<a href='?day=6'>Oct 19th - Tome of Experience</a><br />
	<a href='?day=7'>Oct 20th - Chivalrous First Year Badge</a>";
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
	if ($input == 14)
		$itmid=93;
	if ($input == 15)
		$itmid=180;
	if ($input == 16)
		$itmid=152;
	if ($input == 17)
		$itmid=181;
	if ($input == 18)
	{
		$itmid=62;
		$qty=5;
	}
	if ($input == 19)
		$itmid=148;
	if ($input == 20)
		$itmid=179;
	$db->query("UPDATE `user_settings` SET `holiday` = 1 WHERE `userid` = {$userid}");
	$api->UserGiveItem($userid,$itmid,$qty);
	alert('success',"Success!","You have claimed your {$api->SystemItemIDtoName($itmid)}!",true,'explore.php');
}
$h->endpage();