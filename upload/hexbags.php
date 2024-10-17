<?php
/*
	File:		hexbags
	Created: 	10/18/2017 at 2:41PM Eastern Time
	Info: 		Round and round the wheel goes.
	Author:		TheMasterGeneral
	Website: 	http://chivalryisdead.x10.mx/
*/
$macropage = ('hexbags.php');
$multipler=1.0;
require('globals.php');
if (isHoliday())
    $multipler *= 2.0;
if (currentMonth() == 9)
    $multipler *= 2.0;
if (reachedMonthlyDonationGoal())
    $multipler = $multipler + 0.5;
$_GET['tresde'] = (isset($_GET['tresde']) && is_numeric($_GET['tresde'])) ? abs($_GET['tresde']) : 0;
if (!isset($_SESSION['tresde'])) {
    $_SESSION['tresde'] = 0;
}
$left=$ir['hexbags']-1;
if ($ir['hexbags'] == 0)
{
    alert('danger',"Uh Oh!","You've already opened all your hexbags for the day. Go vote or come back in " . TimeUntil_Parse(getNextDayReset()) .".",true,'explore.php');
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
$string="";
if ($chance <= 35)
{
    $cash=Random(500,3500)*$multipler;
	$cash=round($cash+($cash*levelMultiplier($ir['level'])));
	$string = "Thou dost open this hexbag and draw forth " . shortNumberParse($cash) . " Copper Coins.";
    $api->UserGiveCurrency($userid,'primary',$cash);
    $api->SystemLogsAdd($userid,"hexbags","Received " . shortNumberParse($cash) . " Copper Coins.");
	addToEconomyLog('Hexbags', 'copper', $cash);
}
elseif (($chance > 35) && ($chance <= 46))
{
    $cash=Random(5,20)*$multipler;
    $specialnumber=((getUserSkill($userid, 10) * getSkillBonus(10))/100);
	$cash=round($cash+($cash*$specialnumber));
	$cash=round($cash+($cash*levelMultiplier($ir['level'], $ir['reset'])));
	$string = "In haste, thou openest this hexbag and pull out " . shortNumberParse($cash) . " Chivalry Tokens.";
	$api->UserGiveCurrency($userid,'secondary',$cash);
    $api->SystemLogsAdd($userid,"hexbags","Received " . shortNumberParse($cash) . " Chivalry Tokens.");
	addToEconomyLog('Hexbags', 'token', $cash);
}
elseif (($chance > 45) && ($chance <= 50))
{
    $cash=Random(5,15)*$multipler;
    $cash=round($cash+($cash*levelMultiplier($ir['level'], $ir['reset'])));
    $string =  "Thou art a greedy knave! In thy folly, thou dost attempt to seize a handful of hexbags and flee. Alas, thou art caught and taken to the dungeon.";
    $api->UserStatusSet($userid,'dungeon',$cash,"Hexbag Theft");
    $api->SystemLogsAdd($userid,"hexbags","Received " . number_format($cash) . " Dungeon minutes.");
}
elseif (($chance > 50) && ($chance <= 55))
{
    $cash=Random(5,15)*$multipler;
    $cash=round($cash+($cash*levelMultiplier($ir['level'], $ir['reset'])));
    $string =  "Reaching blindly into this hexbag, thou dost prick thyself upon a foul needle. To the infirmary with thee!";
    $api->UserStatusSet($userid,'infirmary',$cash,"Dirty Needle");
    $api->SystemLogsAdd($userid,"hexbags","Received " . number_format($cash) . " Infirmary minutes.");
}
elseif (($chance > 55) && ($chance <= 60))
{
	$rng=Random(2,5)*$multipler;
	$rng=round($rng+($rng*levelMultiplier($ir['level'], $ir['reset'])));
	$string =  "Thou openest the hexbag and discoverest " . number_format($rng) . " Leeches within.";
    $api->UserGiveItem($userid,5,$rng);
    $api->SystemLogsAdd($userid,"hexbags","Received " . number_format($rng) . " Leeches.");
}
elseif (($chance > 60) && ($chance <= 65))
{
	$rng=Random(2,5)*$multipler;
	$rng=round($rng+($rng*levelMultiplier($ir['level'], $ir['reset'])));
	$string =  "Thou openest the hexbag and findeth " . number_format($rng) . " lockpicks within.";
    $api->UserGiveItem($userid,29,$rng);
    $api->SystemLogsAdd($userid,"hexbags","Received " . number_format($rng) . " Lockpicks.");
}
elseif (($chance > 65) && ($chance <= 68))
{
    $gain=(Random(1,10)*$ir['level'])*$multipler;
    $gain=round($gain+($gain*levelMultiplier($ir['level'], $ir['reset'])));
	$string =  "With a mighty tear, thou dost rend the hexbag in twain. Thy strength increaseth by " . shortNumberParse($gain) . ".";
    $db->query("UPDATE `userstats` SET `strength` = `strength` + {$gain} WHERE `userid` = {$userid}");
    $api->SystemLogsAdd($userid,"hexbags","Received " . shortNumberParse($gain) . " Strength.");
}
elseif (($chance > 68) && ($chance <= 71))
{
    $gain=(Random(1,10)*$ir['level'])*$multipler;
    $gain=round($gain+($gain*levelMultiplier($ir['level'], $ir['reset'])));
	$string =  "In swift motion, thou dost open the hexbag. Thy agility increaseth by " . shortNumberParse($gain) . ".";
    $db->query("UPDATE `userstats` SET `agility` = `agility` + {$gain} WHERE `userid` = {$userid}");
    $api->SystemLogsAdd($userid,"hexbags","Received " . shortNumberParse($gain) . " Agility.");
}
elseif (($chance > 71) && ($chance <= 74))
{
    $gain=(Random(1,10)*$ir['level'])*$multipler;
    $gain=round($gain+($gain*levelMultiplier($ir['level'], $ir['reset'])));
	$string =  "Thou openest the hexbag and suffer a paper cut, yet thou dost shrug off the pain. Thy fortitude increaseth by " . shortNumberParse($gain) . ".";
    $db->query("UPDATE `userstats` SET `guard` = `guard` + {$gain} WHERE `userid` = {$userid}");
    $api->SystemLogsAdd($userid,"hexbags","Received " . shortNumberParse($gain) . " Guard.");
}
elseif (($chance > 74) && ($chance <= 80))
{
    $rocks=Random(1,10)*$multipler;
    $rocks=round($rocks+($rocks*levelMultiplier($ir['level'], $ir['reset'])));
    $string =  "Within this hexbag thou dost find " . number_format($rocks) . " Heavy Rock(s). They are now in thine inventory.";
    $api->UserGiveItem($userid,2,$rocks);
    $api->SystemLogsAdd($userid,"hexbags","Received " . number_format($rocks) . " Heavy Rocks.");
}
elseif (($chance > 80) && ($chance <= 86))
{
    $rocks=Random(2,10)*$multipler;
    $rocks=round($rocks+($rocks*levelMultiplier($ir['level'], $ir['reset'])));
    $string = "Within this hexbag thou dost find " . number_format($rocks) . " Sharpened Sticks. They are now in thine inventory.";
    $api->UserGiveItem($userid,1,$rocks);
    $api->SystemLogsAdd($userid,"hexbags","Received " . number_format($rocks) . " Sharpened Sticks.");
}
elseif (($chance > 86) && ($chance <= 93))
{
    $bor=Random(2,15)*$multipler;
    $bor=round($bor+($bor*levelMultiplier($ir['level'], $ir['reset'])));
    $string = "Thou dost open the hexbag and uncover " . number_format($bor) . " Boxes of Random. They are now in thine inventory.";
    $api->UserGiveItem($userid,33,$bor);
    $api->SystemLogsAdd($userid,"hexbags","Received " . number_format($bor) . " Boxes of Random.");
}
elseif ($chance == 94)
{
    $string = "Within the hexbag lies an assassination note. It is now in thine inventory.";
    $api->UserGiveItem($userid,222,1);
    $api->SystemLogsAdd($userid,"hexbags","Received Assassination Note.");
}
elseif ($chance == 95)
{
    $string = "Thou dost open the hexbag and discover a bucket of water, full to the brim. How curious! It is now in thine inventory.";
    $api->UserGiveItem($userid,296,1);
    $api->SystemLogsAdd($userid,"hexbags","Received Bucket of Water.");
}
else
{
    $string = "Reaching into the hexbag, thou feelest something warm and unsettling. Perhaps it is best left undisturbed... for now.";
    $api->SystemLogsAdd($userid,"hexbags","Received nothing.");
	if (Random(1,25) == 10)
	{
		$api->GameAddNotification($userid,"You were given the Hexbags Badge!", "fas fa-poo-storm", "#a17445");
		$api->UserGiveItem($userid,274,1);
	}
}
echo " <b></b><hr />
<div class='row'>
    <div class='col-12'>
        <div class='card'>
            <div class='card-header'>
                {$left} Hexbags remaining
            </div>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-12'>
                        {$string}
                    </div>
                    <div class='col-12'>
                        <div class='row'>
                            <div class='col-12 col-sm-6'>
                                <a href='hexbags.php' class='btn btn-primary btn-block'>Open Another</a><br />
                            </div>
                            <div class='col-12 col-sm-6'>
                                <a href='explore.php' class='btn btn-danger btn-block'>Explore</a><br />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>";
$h->endpage();