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
	while($number < $_POST['open'])
	{
		$number=$number+1;
		$chance=Random(1,100);
		if ($chance <= 25)
		{
			$cash=Random(50,1000);
			$copper=$copper+$cash;
		}
		elseif (($chance > 25) && ($chance <= 40))
		{
			$cash=Random(1,5);
			$tokens=$tokens+$cash;
		}
		elseif (($chance > 40) && ($chance <= 50))
		{
			$cash=Random(5,25);
			$dungeon=$dungeon+$cash;
		}
		elseif (($chance > 50) && ($chance <= 60))
		{
			$cash=Random(5,25);
			$infirmary=$infirmary+$cash;
		}
		elseif (($chance > 70) && ($chance <= 75))
		{
			$rng=Random(1,4);
			$leeches=$leeches+$rng;
		}
		elseif (($chance > 75) && ($chance <= 80))
		{
			$rng=Random(1,4);
			$lockpicks=$lockpicks+$rng;
		}
		elseif (($chance > 80) && ($chance <= 83))
		{
			$gain=Random(1,5)*$ir['level'];
			$strength=$strength+$gain;
		}
		elseif (($chance > 83) && ($chance <= 86))
		{
			$gain=Random(1,5)*$ir['level'];
			$agility=$agility+$gain;
		}
		elseif (($chance > 86) && ($chance <= 89))
		{
			$gain=Random(1,5)*$ir['level'];
			$guard=$guard+$gain;
		}
		elseif ($chance >= 93)
		{
			$bor=Random(1,10);
			$borg=$borg+$bor;
		}
		else
		{
			$nothing=$nothing+1;
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