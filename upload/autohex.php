<?php
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
	while($number < $_POST['open'])
	{
		$number=$number+1;
		$chance=Random(1,95);
		if ($chance <= 35)
		{
			$cash=Random(750,3500);
			$cash=round($cash+($cash*levelMultiplier($ir['level'])));
			$copper=$copper+$cash;
            $api->SystemLogsAdd($userid,"hexbags","Received {$cash} Copper Coins.");
		}
		elseif (($chance > 35) && ($chance <= 45))
		{
			$cash=Random(5,20);
			$specialnumber=((getSkillLevel($userid,11)*5)/100);
			$cash=round($cash+($cash*$specialnumber));
			$cash=round($cash+($cash*levelMultiplier($ir['level'])));
			$tokens=$tokens+$cash;
            $api->SystemLogsAdd($userid,"hexbags","Received {$cash} Chivalry Tokens.");
		}
		elseif (($chance > 45) && ($chance <= 50))
		{
			$cash=Random(30,60);
			$cash=round($cash+($cash*levelMultiplier($ir['level'])));
			$dungeon=$dungeon+$cash;
            $api->SystemLogsAdd($userid,"hexbags","Received {$cash} Dungeon minutes.");
		}
		elseif (($chance > 50) && ($chance <= 55))
		{
			$cash=Random(30,60);
			$cash=round($cash+($cash*levelMultiplier($ir['level'])));
			$infirmary=$infirmary+$cash;
            $api->SystemLogsAdd($userid,"hexbags","Received {$cash} Infirmary minutes.");
		}
		elseif (($chance > 55) && ($chance <= 60))
		{
			$rng=Random(2,5);
			$rng=round($rng+($rng*levelMultiplier($ir['level'])));
			$leeches=$leeches+$rng;
            $api->SystemLogsAdd($userid,"hexbags","Received {$rng} Leeches.");
		}
		elseif (($chance > 60) && ($chance <= 65))
		{
			$rng=Random(2,5);
			$rng=round($rng+($rng*levelMultiplier($ir['level'])));
			$lockpicks=$lockpicks+$rng;
            $api->SystemLogsAdd($userid,"hexbags","Received {$rng} Lockpicks.");
		}
		elseif (($chance > 65) && ($chance <= 68))
		{
			$gain=Random(1,10)*$ir['level'];
			$gain=round($gain+($gain*levelMultiplier($ir['level'])));
			$strength=$strength+$gain;
            $api->SystemLogsAdd($userid,"hexbags","Received {$gain} Strength.");
		}
		elseif (($chance > 68) && ($chance <= 71))
		{
			$gain=Random(1,10)*$ir['level'];
			$gain=round($gain+($gain*levelMultiplier($ir['level'])));
			$agility=$agility+$gain;
            $api->SystemLogsAdd($userid,"hexbags","Received {$gain} Agility.");
		}
		elseif (($chance > 71) && ($chance <= 74))
		{
			$gain=Random(1,10)*$ir['level'];
			$gain=round($gain+($gain*levelMultiplier($ir['level'])));
			$guard=$guard+$gain;
            $api->SystemLogsAdd($userid,"hexbags","Received {$gain} Guard.");
		}
        elseif (($chance > 74) && ($chance <= 80))
        {
            $gain=Random(2,10);
			$gain=round($gain+($gain*levelMultiplier($ir['level'])));
            $rocks=$rocks+$gain;
            $api->SystemLogsAdd($userid,"hexbags","Received {$gain} Heavy Rocks.");
        }
        elseif (($chance > 80) && ($chance <= 86))
        {
            $gain=Random(2,10);
			$gain=round($gain+($gain*levelMultiplier($ir['level'])));
            $sticks=$sticks+$gain;
            $api->SystemLogsAdd($userid,"hexbags","Received {$gain} Sharpened Sticks.");
        }
		elseif (($chance > 86) && ($chance <= 93))
		{
			$bor=Random(2,15);
			$gain=round($bor+($bor*levelMultiplier($ir['level'])));
			$borg=$borg+$bor;
            $api->SystemLogsAdd($userid,"hexbags","Received {$bor} Boxes of Random.");
		}
		else
		{
			$nothing=$nothing+1;
            $api->SystemLogsAdd($userid,"hexbags","Received nothing.");
		}
	}
	$db->query("UPDATE `users` SET `hexbags` = `hexbags` - {$_POST['open']} WHERE `userid` = {$userid}");
	$db->query("UPDATE `user_settings` SET `autohex` = `autohex` - {$_POST['open']} WHERE `userid` = {$userid}");
	echo "After automatically opening {$number} Hexbags, you have gained the following:<br />
		{$copper} Copper Coins<br />
		{$tokens} Chivalry Tokens<br />
		{$dungeon} minutes in the dungeon.<br />
		{$infirmary} minutes in the infirmary.<br />
		{$leeches} Leeches<br />
		{$lockpicks} Lockpicks.<br />
        {$rocks} Heavy Rocks.<br />
		{$sticks} Sharpened Sticks.<br />
		{$strength} strength.<br />
		{$agility} agility.<br />
		{$guard} guard.<br />
		{$borg} Boxes of Random.<br />
		{$nothing} Hexbags had nothing in them.";
	$api->UserGiveCurrency($userid,'primary',$copper);
	$api->UserGiveCurrency($userid,'secondary',$tokens);
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