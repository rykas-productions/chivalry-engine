<?php
require('globals.php');
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "ticket":
        ticket();
        break;
    case "chuck":
        chuck();
        break;
	case "tnt":
        tnt();
        break;
    default:
        alert('danger',"Uh Oh!","Please specify an action.",true,'index.php');
        break;
}
function ticket()
{
	global $h,$db,$api,$userid;
	if (!$api->UserHasItem($userid,376,1))
	{
		alert('danger',"Uh Oh!","You need a 2020 Halloween Scratch Off to be here.",true,'inventory.php');
		die($h->endpage());
	}
	if (isset($_GET['scratch']))
	{
		$rng=Random(1,6);
		if ($rng == 1)
		{
			$cash=Random(500000,1000000);
			alert("success","Success!","You scratch this spot off and you win " . number_format($cash) . " Copper Coins. Congratulations!",true,'inventory.php');
			$api->UserGiveCurrency($userid,'primary',$cash);
		}
		elseif ($rng == 2)
		{
			$cash=Random(2500,5000);
			alert("success","Success!","You scratch this spot off and you win " . number_format($cash) . " Chivalry Tokens. Congratulations!",true,'inventory.php');
			$api->UserGiveCurrency($userid,'secondary',$cash);
		}
		elseif ($rng == 3)
		{
			$cash=Random(125,250);
			alert("success","Success!","You scratch this spot off and you win " . number_format($cash) . " pieces of Halloween Candy. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,66,$cash);
		}
		elseif ($rng == 4)
		{
			$cash=Random(5,12);
			alert("success","Success!","You scratch this spot off and you win " . number_format($cash) . " Chivalry Gym Scrolls. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,18,$cash);
		}
		elseif ($rng == 5)
		{
			$cash=Random(2,4);
			alert("success","Success!","You scratch this spot off and you win " . number_format($cash) . " Will Stimulant Potions. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,263,$cash);
		}
		else
		{
			$cash=Random(2,7);
			alert("success","Success!","You scratch this spot off and you win " . number_format($cash) . " VIP Days. Congratulations!",true,'inventory.php');
			$db->query("UPDATE `users` SET `vip_days` = `vip_days` + " . number_format($cash) . " WHERE `userid` = {$userid}");
		}
		$api->UserTakeItem($userid,376,1);
	}
	else
	{
		echo "Select the spot you wish to scratch off. You shall receive rewards.<br />
		<div class='row'>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='assets/img/pumpkin-halloween.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='assets/img/pumpkin-halloween.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='assets/img/pumpkin-halloween.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='assets/img/pumpkin-halloween.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='assets/img/pumpkin-halloween.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='assets/img/pumpkin-halloween.png' class='img-fluid'></a>
			</div>
		</div>";
	}
}
function chuck()
{
	global $db,$api,$userid,$h,$ir;
	echo "<h3>2020 Pumpkin Chuck</h3><hr />";
	$q=$db->query("SELECT * FROM `2018_halloween_chuck` WHERE `userid` = {$userid}");
	if ($db->num_rows($q) == 0)
	{
		$db->query("INSERT INTO `2018_halloween_chuck` (`userid`, `distance`, `count`) VALUES ('{$userid}', '0', '0')");
	}
	$r=$db->fetch_row($q);
	if (isset($_GET['throw']))
	{
		if (calculateLuck($userid))
			$distance=Random(100,10000);
		else
			$distance=Random(125,13275);
		if (!$api->UserHasItem($userid,64,1))
		{
			alert('danger',"Uh Oh!","You need a pumpkin to even throw one. You may buy one from the Cornrye Pub.",true,'2018halloween.php?action=chuck');
			die($h->endpage());
		}
		if ($r['count'] == 0)
			$api->UserGiveItem($userid,378,1);
		if ($r['count'] == 10)
		{
			alert('danger',"Uh Oh!","You've already given it your best shot, bud. Let others have a chance.",true,'2018halloween.php?action=chuck');
			die($h->endpage());
		}
		if ($distance > $r['distance'])
		{
			$db->query("UPDATE `2018_halloween_chuck` SET `distance` = {$distance} WHERE `userid` = {$userid}");
		}
		$db->query("UPDATE `2018_halloween_chuck` SET `count` = `count` + 1 WHERE `userid` = {$userid}");
		alert("success","Success!","You've successfully chucked your pumpkin and achieved a wonderful distance of {$distance} meters.", true, '2018halloween.php?action=chuck');
		$api->UserTakeItem($userid,64,1);
	}
	else
	{
		$hq=$db->query("SELECT * FROM `2018_halloween_chuck` WHERE `distance` > 0 ORDER BY `distance` desc LIMIT 5");
		echo "Welcome to the Pumpkin Chuck, {$ir['username']}. You may chuck your pumpkin to see how far it'll go. The player who throws the furthest will 
		receive a unique prize. The two runner-ups will receive a small little prize as well! Remember, your only your best throw will be put into the scorebook.<br />
		You have thrown {$r['count']} out of 10 times. Your max throw is " . number_format($r['distance']) . " meters.<br />
		<br />
		<b><u>Top 5 Scores</u></b>";
		while ($r2=$db->fetch_row($hq))
		{
			$r2['username'] = parseUsername($r2['userid']);
			echo "<br /><a href='profile.php?user={$r2['userid']}'>{$r2['username']}</a> [{$r2['userid']}] - " . number_format($r2['distance']) . " Meters.";
		}
		echo "<br />
		<a class='btn btn-primary' href='?action=chuck&throw=1'>Chuck Pumpkin</a>";
	}
}
$h->endpage();