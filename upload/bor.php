<?php
/*
	File:		bor.php
	Created: 	10/18/2017 at 4:02PM Eastern Time
	Info: 		Random items and whatnot!
	Author:		TheMasterGeneral
	Website: 	http://chivalryisdead.x10.mx/
*/
require('globals.php');
$tresder = Random(100, 999);
echo "<h3>Box of Random</h3><hr />";
$_GET['tresde'] = (isset($_GET['tresde']) && is_numeric($_GET['tresde'])) ? abs($_GET['tresde']) : 0;
if (!isset($_SESSION['tresde'])) {
    $_SESSION['tresde'] = 0;
}
if (($_SESSION['tresde'] == $_GET['tresde']) || $_GET['tresde'] < 100)
{
    alert("danger", "Uh Oh!", "Please do not refresh while opening Boxes of Random. Thank you!", true, "?tresde={$tresder}");
    die($h->endpage());
}
$_SESSION['tresde'] = $_GET['tresde'];
$left=$ir['bor']-1;
if ($ir['bor'] == 0)
{
    alert('danger',"Uh Oh!","You cannot open anymore Boxes of Random today.",true,'explore.php');
    die($h->endpage());
}
if ($api->UserStatus($userid,'dungeon'))
{
    alert('danger',"Uh Oh!","You cannot open Boxes of Random while in the dungeon.",true,'explore.php');
    die($h->endpage());
}
if ($api->UserStatus($userid,'infirmary'))
{
    alert('danger',"Uh Oh!","You cannot open Boxes of Random while in the infirmary.",true,'explore.php');
    die($h->endpage());
}
if (!$api->UserHasItem($userid,33,1))
{
    alert('danger',"Uh Oh!","You need at least one Box of Random to open Boxes of Random.",true,'explore.php');
    die($h->endpage());
}
$db->query("UPDATE `users` SET `bor` = `bor` - 1 WHERE `userid` = {$userid}");
$api->UserTakeItem($userid,33,1);
$chance=Random(1,105);
if ($chance <= 30)
{
    $cash=Random(500,2500);
    echo "You open this Box of Random and pull out " . number_format($cash) . " Copper Coins. Cool!";
    $api->UserGiveCurrency($userid,'primary',$cash);
}
elseif (($chance > 30) && ($chance <= 45))
{
    $cash=Random(5,10);
    echo "You quickly open this Box of Random and pull out {$cash} Chivalry Tokens. Neat!";
	$api->UserGiveCurrency($userid,'secondary',$cash);
}
elseif (($chance > 45) && ($chance <= 55))
{
    $cash=Random(5,20);
    echo "Tick, tock. Ka-boom!";
    $api->UserStatusSet($userid,'infirmary',$cash,"Ticking Box");
}
elseif (($chance > 55) && ($chance <= 60))
{
    echo "You open this Box of Random and pull out a piece of bread. Yum!";
    $api->UserGiveItem($userid,19,1);
}
elseif (($chance > 65) && ($chance <= 70))
{
    echo "You open this Box of Random and pull out a piece of venison. Yum!";
    $api->UserGiveItem($userid,20,1);
}
elseif (($chance > 70) && ($chance <= 75))
{
    echo "You open this Box of Random and get a Small Health Potion.";
    $api->UserGiveItem($userid,7,1);
}
elseif (($chance > 75) && ($chance <= 80))
{
	$rng=Random(2,4);
    echo "You open this Box of Random and find yourself {$rng} Linen Wraps! Nifty!";
    $api->UserGiveItem($userid,6,$rng);
}
elseif (($chance > 80) && ($chance <= 85))
{
	$rng=Random(2,4);
    echo "You open this Box of Random and find yourself {$rng} Dungeon Keys! Nifty!";
    $api->UserGiveItem($userid,30,$rng);
}
elseif (($chance > 85) && ($chance <= 93))
{
	$rng=Random(1,2);
    echo "You open this Box of Random and find yourself {$rng} Explosives.";
    $api->UserGiveItem($userid,28,$rng);
}
elseif (($chance == 98) || ($chance == 99))
{
    echo "You open this Box of Random and find a Chivalry Gym Scroll. Congratulations!";
    $api->UserGiveItem($userid,18,1);
}
elseif (($chance > 99) && ($chance <= 103))
{
	echo "You open this Box of Random and find a Distant Attack Scroll. Congratulations!";
    $api->UserGiveItem($userid,90,1);
}
else
{
    echo "You open this Box of Random and get a bunch of junk.";
}
echo " You can open another {$left} Boxes of Random today.<hr />
<a href='?tresde={$tresder}'>Open Another</a><br />
<a href='explore.php'>Back to Town</a>";
$h->endpage();