<?php
$macropage = ('autohex.php');
$multipler = 1.0;
require('globals.php');
if ($ir['autohex'] == 0)
{
	alert('danger',"Uh Oh!","You need to have an Auto Hexbag Opener redeemed on your account to use this feature.",true,'explore.php');
	die($h->endpage());
}
if ($ir['hexbags'] == 0)
{
    alert('danger',"Uh Oh!","You've already opened all your Hexbags for the day. Go vote or come back tomorrow.",true,'explore.php');
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
	$lvlmultiplier=levelMultiplier($ir['level']);
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
			$specialnumber=((getSkillLevel($userid,11)*5)/100);
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
		{
			$notes=$notes+1;
		}
		else
		{
			$nothing=$nothing+1;
		}
	}
	$db->query("UPDATE `users` SET `hexbags` = `hexbags` - {$_POST['open']} WHERE `userid` = {$userid}");
	$db->query("UPDATE `user_settings` SET `autohex` = `autohex` - {$_POST['open']} WHERE `userid` = {$userid}");
	echo "After automatically opening {$number} Hexbags, you have gained the following:<br />
		" . number_format($copper) . " Copper Coins<br />
		" . number_format($tokens) . " Chivalry Tokens<br />
		" . number_format($dungeon) . " minutes in the dungeon.<br />
		" . number_format($infirmary) . " minutes in the infirmary.<br />
		" . number_format($leeches) . " Leeches<br />
		" . number_format($lockpicks) . " Lockpicks.<br />
        " . number_format($rocks) . " Heavy Rocks.<br />
		" . number_format($sticks) . " Sharpened Sticks.<br />
		" . number_format($strength) . " strength.<br />
		" . number_format($agility) . " agility.<br />
		" . number_format($guard) . " guard.<br />
		" . number_format($borg) . " Boxes of Random.<br />
        " . number_format($notes) . " Assassination Notes.<br />
		" . number_format($nothing) . " Hexbags had nothing in them.";
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
	$db->query("UPDATE `userstats` 
				SET `strength` = `strength` + {$strength}, 
				`agility` = `agility` + {$agility}, 
				`guard` = `guard` + {$guard} 
				WHERE `userid` = {$userid}");
	$api->UserGiveItem($userid,33,$borg);
    $api->UserGiveItem($userid,222,$notes);
	//Logs
	$api->SystemLogsAdd($userid,"hexbags","Received " . number_format($copper) . " Copper Coins.");
	$api->SystemLogsAdd($userid,"hexbags","Received " . number_format($tokens) . " Chivalry Tokens.");
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
	echo "How many Hexbags would you like to open in an automated fashion? You can open {$ir['hexbags']} Hexbags today. You 
	currently have {$ir['autohex']} uses left on your Auto Hexbag Opener.
	<br />
	<form method='post'>
		<input type='number' min='1' max='{$maxnumber}' name='open' class='form-control' required='1' value='{$maxnumber}'>
		<input type='submit' value='Open Hexbags' class='btn btn-primary'>
	</form>";
}
$h->endpage();