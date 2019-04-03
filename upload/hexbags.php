<?php
/*
	File:		hexbags
	Created: 	10/18/2017 at 2:41PM Eastern Time
	Info: 		Round and round the wheel goes.
	Author:		TheMasterGeneral
	Website: 	http://chivalryisdead.x10.mx/
*/
require('globals.php');
echo "<h3><i class='game-icon game-icon-open-treasure-chest'></i> Hexbags</h3><hr />";
$_GET['tresde'] = (isset($_GET['tresde']) && is_numeric($_GET['tresde'])) ? abs($_GET['tresde']) : 0;
if (!isset($_SESSION['tresde'])) {
    $_SESSION['tresde'] = 0;
}
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
$chance=Random(1,96);
if ($chance <= 35)
{
    $cash=Random(500,3500);
	$cash=round($cash+($cash*levelMultiplier($ir['level'])));
    echo "You open this hexbag and pull out {$cash} Copper Coins.";
    $api->UserGiveCurrency($userid,'primary',$cash);
    $api->SystemLogsAdd($userid,"hexbags","Received {$cash} Copper Coins.");
}
elseif (($chance > 35) && ($chance <= 46))
{
    $cash=Random(5,20);
	$specialnumber=((getSkillLevel($userid,11)*5)/100);
	$cash=round($cash+($cash*$specialnumber));
	$cash=round($cash+($cash*levelMultiplier($ir['level'])));
    echo "You quickly open this hexbag and pull out {$cash} Chivalry Tokens.";
	$api->UserGiveCurrency($userid,'secondary',$cash);
    $api->SystemLogsAdd($userid,"hexbags","Received {$cash} Chivalry Tokens.");
}
elseif (($chance > 45) && ($chance <= 50))
{
    $cash=Random(30,60);
	$cash=round($cash+($cash*levelMultiplier($ir['level'])));
    echo "You greedy bastard. You attempt to snatch a handful of hexbags and run. You get stopped and escorted to the
    dungeon.";
    $api->UserStatusSet($userid,'dungeon',$cash,"Hexbag Theft");
    $api->SystemLogsAdd($userid,"hexbags","Received {$cash} Dungeon minutes.");
}
elseif (($chance > 50) && ($chance <= 55))
{
    $cash=Random(30,60);
	$cash=round($cash+($cash*levelMultiplier($ir['level'])));
    echo "You reach your hand into this hexbag without looking and stick yourself with a dirty needle. To the infirmary
    you go.";
    $api->UserStatusSet($userid,'infirmary',$cash,"Dirty Needle");
    $api->SystemLogsAdd($userid,"hexbags","Received {$cash} Infirmary minutes.");
}
elseif (($chance > 55) && ($chance <= 60))
{
	$rng=Random(2,5);
	$rng=round($rng+($rng*levelMultiplier($ir['level'])));
    echo "You open this hexbag and find yourself {$rng} Leeches.";
    $api->UserGiveItem($userid,5,$rng);
    $api->SystemLogsAdd($userid,"hexbags","Received {$rng} Leeches.");
}
elseif (($chance > 60) && ($chance <= 65))
{
	$rng=Random(2,5);
	$rng=round($rng+($rng*levelMultiplier($ir['level'])));
    echo "You open this hexbag and find yourself {$rng} Lockpicks.";
    $api->UserGiveItem($userid,29,$rng);
    $api->SystemLogsAdd($userid,"hexbags","Received {$rng} Lockpicks.");
}
elseif (($chance > 65) && ($chance <= 68))
{
    $gain=Random(1,10)*$ir['level'];
	$gain=round($gain+($gain*levelMultiplier($ir['level'])));
    echo "You open this hexbag and rip it in half. Your Strength increases by {$gain}.";
    $db->query("UPDATE `userstats` SET `strength` = `strength` + {$gain} WHERE `userid` = {$userid}");
    $api->SystemLogsAdd($userid,"hexbags","Received {$gain} Strength.");
}
elseif (($chance > 68) && ($chance <= 71))
{
    $gain=Random(1,10)*$ir['level'];
	$gain=round($gain+($gain*levelMultiplier($ir['level'])));
    echo "You open this hexbag quickly. Your Agility increases by {$gain}.";
    $db->query("UPDATE `userstats` SET `agility` = `agility` + {$gain} WHERE `userid` = {$userid}");
    $api->SystemLogsAdd($userid,"hexbags","Received {$gain} Agility.");
}
elseif (($chance > 71) && ($chance <= 74))
{
    $gain=Random(1,10)*$ir['level'];
	$gain=round($gain+($gain*levelMultiplier($ir['level'])));
    echo "You open this hexbag and get a paper cut and you shrug off the pain. Your Guard increases by {$gain}.";
    $db->query("UPDATE `userstats` SET `guard` = `guard` + {$gain} WHERE `userid` = {$userid}");
    $api->SystemLogsAdd($userid,"hexbags","Received {$gain} Guard.");
}
elseif (($chance > 74) && ($chance <= 80))
{
    $rocks=Random(2,10);
	$rocks=round($rocks+($rocks*levelMultiplier($ir['level'])));
    echo "You open this hexbag and find {$rocks} Heavy Rocks. They're in your inventory.";
    $api->UserGiveItem($userid,2,$rocks);
    $api->SystemLogsAdd($userid,"hexbags","Received {$rocks} Heavy Rocks.");
}
elseif (($chance > 80) && ($chance <= 86))
{
    $rocks=Random(2,10);
	$rocks=round($rocks+($rocks*levelMultiplier($ir['level'])));
    echo "You open this hexbag and find {$rocks} Sharpened Sticks. They're in your inventory.";
    $api->UserGiveItem($userid,1,$rocks);
    $api->SystemLogsAdd($userid,"hexbags","Received {$rocks} Sharpened Sticks.");
}
elseif (($chance > 86) && ($chance <= 93))
{
    $bor=Random(2,15);
	$bor=round($bor+($bor*levelMultiplier($ir['level'])));
    echo "You open this hexbag and find {$bor} of Boxes of Randoms. They're in your inventory.";
    $api->UserGiveItem($userid,33,$bor);
    $api->SystemLogsAdd($userid,"hexbags","Received {$bor} Boxes of Random.");
}
elseif ($chance == 94)
{
    echo "You open this hexbag and find an Assassination Note. Its in your inventory.";
    $api->UserGiveItem($userid,222,1);
    $api->SystemLogsAdd($userid,"hexbags","Received Assassination Note.");
}
else
{
    echo "You reach into this hexbag and feel something warm and squishy. You decide its best to keep it in there for now.";
    $api->SystemLogsAdd($userid,"hexbags","Received nothing.");
}
echo " You have {$left} Hexbags remaining for today.<hr />
<a href='hexbags.php'>Open Another</a><br />
<a href='explore.php'>Back to Town</a>";
$h->endpage();