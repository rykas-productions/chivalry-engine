<?php
$macropage = ('autohex.php');
$multipler = 1.0;
require('globals.php');
if (isHoliday())
    $multipler = 2.0;
if (reachedMonthlyDonationGoal())
    $multipler += 0.5;
if (currentMonth() == 9)
    $multipler *= 2.0;
if ($ir['autohex'] == 0)
{
	alert('danger',"Uh Oh!","You need to have an Auto Hexbag Opener redeemed on your account to use this feature.",true,'explore.php');
	die($h->endpage());
}
if ($ir['hexbags'] == 0)
{
    alert('danger',"Uh Oh!","You've already opened all your Hexbags for the day. Go vote or come back in " . TimeUntil_Parse(getNextDayReset()) .".",true,'explore.php');
    die($h->endpage());
}
if ($api->UserStatus($userid,'dungeon'))
{
    alert('danger',"Uh Oh!","You cannot open Hexbags while in the dungeon.",true,'explore.php');
    die($h->endpage());
}
if ($api->UserStatus($userid,'infirmary'))
{
    alert('danger',"Uh Oh!","You cannot open Hexbags while in the infirmary.",true,'explore.php');
    die($h->endpage());
}
if (isset($_POST['open']))
{
	$_POST['open'] = abs($_POST['open']);
	if (empty($_POST['open']))
	{
		alert('danger',"Uh Oh!","Please specify how many Hexbags you would like to open using this system.");
		die($h->endpage());
	}
	if ($_POST['open'] > $ir['hexbags'])
	{
		alert('danger',"Uh Oh!","You do not have that many Hexbags available to you at this moment.");
		die($h->endpage());
	}
	if ($_POST['open'] > $ir['autohex'])
	{
		alert('danger',"Uh Oh!","You are trying to open more Hexbags than you currently have redeemed on your account.");
		die($h->endpage());
	}
	$lvlmultiplier=levelMultiplier($ir['level'], $ir['reset']);
	$number=0;
	$copper=0;
	$tokens=0;
	$dungeon=0;
	$infirmary=0;
	$leeches=0;
	$lockpicks=0;
	$strength=0;
	$agility=0;
	$guard=0;
	$borg=0;
	$nothing=0;
    $sticks=0;
    $rocks=0;
    $notes=0;
    $water=0;
	while($number < $_POST['open'])
	{
		$number=$number+1;
		$chance=Random(1,96);
		if ($chance <= 35)
		{
			$cash=Random(750,3500)*$multipler;
			$cash=round($cash+($cash*$lvlmultiplier));
			$copper=$copper+$cash;
		}
		elseif (($chance > 35) && ($chance <= 45))
		{
			$cash=Random(5,20)*$multipler;
			$specialnumber=((getUserSkill($userid, 10) * getSkillBonus(10))/100);
			$cash=round($cash+($cash*$specialnumber));
			$cash=round($cash+($cash*$lvlmultiplier));
			$tokens=$tokens+$cash;
		}
		elseif (($chance > 45) && ($chance <= 50))
		{
			$cash=Random(5,15)*$multipler;
			$cash=round($cash+($cash*$lvlmultiplier));
			$dungeon=$dungeon+$cash;
		}
		elseif (($chance > 50) && ($chance <= 55))
		{
			$cash=Random(5,15)*$multipler;
			$cash=round($cash+($cash*$lvlmultiplier));
			$infirmary=$infirmary+$cash;
		}
		elseif (($chance > 55) && ($chance <= 60))
		{
			$rng=Random(2,5)*$multipler;
			$rng=round($rng+($rng*$lvlmultiplier));
			$leeches=$leeches+$rng;
		}
		elseif (($chance > 60) && ($chance <= 65))
		{
			$rng=Random(2,5)*$multipler;
			$rng=round($rng+($rng*$lvlmultiplier));
			$lockpicks=$lockpicks+$rng;
		}
		elseif (($chance > 65) && ($chance <= 68))
		{
			$gain=(Random(1,10)*$ir['level'])*$multipler;
			$gain=round($gain+($gain*$lvlmultiplier));
			$strength=$strength+$gain;
		}
		elseif (($chance > 68) && ($chance <= 71))
		{
			$gain=(Random(1,10)*$ir['level'])*$multipler;
			$gain=round($gain+($gain*$lvlmultiplier));
			$agility=$agility+$gain;
		}
		elseif (($chance > 71) && ($chance <= 74))
		{
			$gain=(Random(1,10)*$ir['level'])*$multipler;
			$gain=round($gain+($gain*$lvlmultiplier));
			$guard=$guard+$gain;
		}
        elseif (($chance > 74) && ($chance <= 80))
        {
            $gain=Random(2,10)*$multipler;
			$gain=round($gain+($gain*$lvlmultiplier));
            $rocks=$rocks+$gain;
        }
        elseif (($chance > 80) && ($chance <= 86))
        {
            $gain=Random(2,10)*$multipler;
			$gain=round($gain+($gain*$lvlmultiplier));
            $sticks=$sticks+$gain;
        }
		elseif (($chance > 86) && ($chance <= 93))
		{
			$bor=Random(2,15)*$multipler;
			$gain=round($bor+($bor*$lvlmultiplier));
			$borg=$borg+$bor;
		}
        elseif ($chance == 94)
			$notes++;
		elseif ($chance == 95)
		    $water++;
		else
			$nothing++;
	}
	$db->query("UPDATE `users` SET `hexbags` = `hexbags` - {$_POST['open']} WHERE `userid` = {$userid}");
	$db->query("UPDATE `user_settings` SET `autohex` = `autohex` - {$_POST['open']} WHERE `userid` = {$userid}");
	echo "<div class='card'>
            <div class='card-header'>
                Opening " . shortNumberParse($number) . " Hexbags...
            </div>
            <div class='card-body'>
                <i>You've gained the following...</i>
                <div class='row'>
                    <div class='col-auto'>
                        " . shortNumberParse($copper) . " Copper Coins
                    </div>
                    <div class='col-auto'>
                        " . shortNumberParse($tokens) . " Chivalry Tokens
                    </div>
                    <div class='text-danger col-auto'>
                        " . shortNumberParse($dungeon) . " Dungeon minutes
                    </div>
                    <div class='text-danger col-auto'>
                        " . shortNumberParse($infirmary) . " Infirmary minutes
                    </div>
                    <div class='col-auto'>
                        " . shortNumberParse($leeches) . " Leeches
                    </div>
                    <div class='col-auto'>
                        " . shortNumberParse($lockpicks) . " Lockpicks
                    </div>
                    <div class='col-auto'>
                        " . shortNumberParse($rocks) . " Heavy Rocks
                    </div>
                    <div class='col-auto'>
                        " . shortNumberParse($sticks) . " Sharpened Sticks
                    </div>
                    <div class='col-auto'>
                        " . shortNumberParse($strength) . " Strength
                    </div>
                    <div class='col-auto'>
                        " . shortNumberParse($agility) . " Agility
                    </div>
                    <div class='col-auto'>
                        " . shortNumberParse($guard) . " Guard
                    </div>
                    <div class='col-auto'>
                        " . shortNumberParse($borg) . " Boxes of Random
                    </div>
                    <div class='col-auto'>
                        " . shortNumberParse($notes) . " Assassination Notes
                    </div>
                    <div class='col-auto'>
                        " . shortNumberParse($water) . " Bucket of Water(s)
                    </div>
                    <div class='text-danger col-auto'>
                        " . shortNumberParse($nothing) . " Empty Hexbags
                    </div>
                </div>
            </div>
    </div>";
	$api->UserGiveCurrency($userid,'primary',$copper);
	$api->UserGiveCurrency($userid,'secondary',$tokens);
	addToEconomyLog('Hexbags', 'copper', $copper);
	addToEconomyLog('Hexbags', 'token', $tokens);
	$api->UserStatusSet($userid,'infirmary',$infirmary,"Dirty Needle");
	$api->UserStatusSet($userid,'dungeon',$dungeon,"Hexbag Theft");
	$api->UserGiveItem($userid,5,$leeches);
	$api->UserGiveItem($userid,29,$lockpicks);
    $api->UserGiveItem($userid,2,$rocks);
    $api->UserGiveItem($userid,1,$sticks);
    $api->UserGiveItem($userid,296,$water);
	$db->query("UPDATE `userstats` 
				SET `strength` = `strength` + {$strength}, 
				`agility` = `agility` + {$agility}, 
				`guard` = `guard` + {$guard} 
				WHERE `userid` = {$userid}");
	$api->UserGiveItem($userid,33,$borg);
    $api->UserGiveItem($userid,222,$notes);
	//Logs
	$api->SystemLogsAdd($userid,"hexbags","Received " . shortNumberParse($copper) . " Copper Coins.");
	$api->SystemLogsAdd($userid,"hexbags","Received " . shortNumberParse($tokens) . " Chivalry Tokens.");
	$api->SystemLogsAdd($userid,"hexbags","Received {$dungeon} dungeon minutes.");
	$api->SystemLogsAdd($userid,"hexbags","Received {$infirmary} infirmary minutes.");
	$api->SystemLogsAdd($userid,"hexbags","Received {$leeches} Leeches.");
	$api->SystemLogsAdd($userid,"hexbags","Received {$lockpicks} Lockpicks.");
	$api->SystemLogsAdd($userid,"hexbags","Received {$rocks} Heavy Rocks.");
	$api->SystemLogsAdd($userid,"hexbags","Received {$sticks} Sharpened Sticks.");
	$api->SystemLogsAdd($userid,"hexbags","Received {$strength} Strength.");
	$api->SystemLogsAdd($userid,"hexbags","Received {$agility} Agility");
	$api->SystemLogsAdd($userid,"hexbags","Received {$guard} Guard.");
	$api->SystemLogsAdd($userid,"hexbags","Received {$borg} Boxes of Random.");
    $api->SystemLogsAdd($userid,"hexbags","Received {$notes} Assassination Notes.");
	$api->SystemLogsAdd($userid,"hexbags","Received {$nothing} nothing(s).");
}
else
{
	$maxnumber = ($ir['autohex'] > $ir['hexbags']) ? $ir['hexbags'] : $ir['autohex'] ;
	echo "<div class='card'>
            <div class='card-header'>
                Auto Hexbag Opener (" . shortNumberParse($ir['autohex']) . " remaining)
            </div>
            <div class='card-body'>
                How many Hexbags would you like to open in an automated fashion? You may open a maximum of 
                " . shortNumberParse($maxnumber) . " Hexbags at this moment.
                <form method='post'>
                <div class='row'>
                    <div class='col-12 col-sm'>
                        <input type='number' min='1' max='{$maxnumber}' name='open' class='form-control' required='1' value='{$maxnumber}'>
                    </div>
                    <div class='col-12 col-sm'>
                        <input type='submit' value='Open Hexbags' class='btn btn-primary btn-block'>
                    </div>
                </div>
                </form>
            </div>
    </div>";
}
$h->endpage();