<?php
/*
	File:		hexbags
	Created: 	10/18/2017 at 2:41PM Eastern Time
	Info: 		Round and round the wheel goes.
	Author:		TheMasterGeneral
	Website: 	http://chivalryisdead.x10.mx/
*/
require('globals.php');
$tresder = Random(100, 999);
echo "<h3><i class='game-icon game-icon-open-treasure-chest'></i> Hexbags</h3><hr />";
$_GET['tresde'] = (isset($_GET['tresde']) && is_numeric($_GET['tresde'])) ? abs($_GET['tresde']) : 0;
if (!isset($_SESSION['tresde'])) {
    $_SESSION['tresde'] = 0;
}
if (($_SESSION['tresde'] == $_GET['tresde']) || $_GET['tresde'] < 100)
{
    alert("danger", "Uh Oh!", "Please do not refresh while opening hexbags. Thank you!", true, "?tresde={$tresder}");
    die($h->endpage());
}
$_SESSION['tresde'] = $_GET['tresde'];
$left=$ir['hexbags']-1;
if ($ir['hexbags'] == 0)
{
    alert('danger',"Uh Oh!","You've already opened all your hexbags for the day. Go vote or come back tomorrow.",true,'explore.php');
    die($h->endpage());
}
if ($api->UserStatus($userid,'dungeon'))
{
    alert('danger',"Uh Oh!","You cannot open hexbags while in the dungeon.",true,'explore.php');
    die($h->endpage());
}
if ($api->UserStatus($userid,'infirmary'))
{
    alert('danger',"Uh Oh!","You cannot open hexbags while in the infirmary.",true,'explore.php');
    die($h->endpage());
}
$db->query("UPDATE `users` SET `hexbags` = `hexbags` - 1 WHERE `userid` = {$userid}");
$chance=Random(1,100);
if ($chance <= 25)
{
    $cash=Random(50,1000);
    echo "You open this hexbag and pull out {$cash} Copper Coins. Cool!";
    $api->UserGiveCurrency($userid,'primary',$cash);
}
elseif (($chance > 25) && ($chance <= 40))
{
    $cash=Random(1,5);
    echo "You quickly open this hexbag and pull out {$cash} Chivalry Tokens. Neat!";
	$api->UserGiveCurrency($userid,'secondary',$cash);
}
elseif (($chance > 40) && ($chance <= 50))
{
    $cash=Random(5,25);
    echo "You greedy bastard. You attempt to snatch a handful of hexbags and run. You get stopped and escorted to the
    dungeon. Nice try, bud.";
    $api->UserStatusSet($userid,'dungeon',$cash,"Hexbag Theft");
}
elseif (($chance > 50) && ($chance <= 60))
{
    $cash=Random(5,25);
    echo "You reach your hand into this hexbag without looking and stick yourself with a dirty needle. Oops. To the infirmary
    you go.";
    $api->UserStatusSet($userid,'infirmary',$cash,"Dirty Needle");
}
elseif (($chance > 70) && ($chance <= 75))
{
	$rng=Random(1,4);
    echo "You open this hexbag and find yourself {$rng} Leech(es)! Nifty!";
    $api->UserGiveItem($userid,5,$rng);
}
elseif (($chance > 75) && ($chance <= 80))
{
	$rng=Random(1,4);
    echo "You open this hexbag and find yourself {$rng} Lockpick(s)! Nifty!";
    $api->UserGiveItem($userid,29,$rng);
}
elseif (($chance > 80) && ($chance <= 83))
{
    $gain=Random(1,5)*$ir['level'];
    echo "You open this hexbag and rip it in half. I guess your strength needs increased by {$gain}";
    $db->query("UPDATE `userstats` SET `strength` = `strength` + {$gain} WHERE `userid` = {$userid}");
}
elseif (($chance > 83) && ($chance <= 86))
{
    $gain=Random(1,5)*$ir['level'];
    echo "You open this hexbag quickly. I guess your agility needs increased by {$gain}";
    $db->query("UPDATE `userstats` SET `agility` = `agility` + {$gain} WHERE `userid` = {$userid}");
}
elseif (($chance > 86) && ($chance <= 89))
{
    $gain=Random(1,5)*$ir['level'];
    echo "You open this hexbag and get a paper cut. You don't shed a tear! How manly. I guess your guard needs increased by {$gain}";
    $db->query("UPDATE `userstats` SET `guard` = `guard` + {$gain} WHERE `userid` = {$userid}");
}
elseif ($chance >= 93)
{
    $bor=Random(1,10);
    echo "You open this hexbag and find {$bor} of Boxes of Randoms. They're in your inventory.";
    $api->UserGiveItem($userid,33,$bor);
}
else
{
    echo "You reach into this hexbag and feel something warm and squishy. You decide its best to keep it in there for now.";
}
echo " You have {$left} Hexbags remaining for today.<hr />
<a href='?tresde={$tresder}'>Open Another</a><br />
<a href='explore.php'>Back to Town</a>";
$h->endpage();