<?php
require('globals.php');
if ($ir['bor'] == 0)
{
    alert('danger',"Uh Oh!","You cannot open anymore Boxes of Random today.",true,'explore.php');
    die($h->endpage());
}
if ($ir['autobor'] == 0)
{
	alert('danger',"Uh Oh!","You need to have an Auto Box of Random Opener redeemed on your account to use this feature.",true,'explore.php');
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
if (isset($_POST['open']))
{
	$_POST['open'] = abs($_POST['open']);
	if (empty($_POST['open']))
	{
		alert('danger',"Uh Oh!","Please specify how many Boxes of Random you would like to open using this system.");
		die($h->endpage());
	}
	if ($_POST['open'] > $ir['bor'])
	{
		alert('danger',"Uh Oh!","You do not have that many Boxes of Random available to you at this moment.");
		die($h->endpage());
	}
	if ($_POST['open'] > $ir['autobor'])
	{
		alert('danger',"Uh Oh!","You are trying to open more Boxes of Random than you currently have redeemed on your account.");
		die($h->endpage());
	}
	if (!$api->UserHasItem($userid,33,$_POST['open']))
	{
		alert('danger',"Uh Oh!","You do not have that many Boxes of Random in your inventory to open that many.");
		die($h->endpage());
	}
	$number=0;
	$copper=0;
	$tokens=0;
	$infirmary=0;
	$bread=0;
	$venison=0;
	$potion=0;
	$wraps=0;
	$keys=0;
	$explosives=0;
	$gymscroll=0;
	$attackscroll=0;
    $mystery=0;
    $needle=0;
	$nothing=0;
	while($number < $_POST['open'])
	{
		$number=$number+1;
		$chance=Random(1,95);
		if ($chance <= 35)
		{
			$cash=Random(650,3000);
			$cash=round($cash+($cash*levelMultiplier($ir['level'])));
			$copper=$copper+$cash;
            $api->SystemLogsAdd($userid,"bor","Received {$cash} Copper Coins.");
		}
		elseif (($chance > 35) && ($chance <= 45))
		{
			$cash=Random(10,25);
			$specialnumber=((getSkillLevel($userid,11)*5)/100);
			$cash=round($cash+($cash*$specialnumber));
			$cash=round($cash+($cash*levelMultiplier($ir['level'])));
			$tokens=$tokens+$cash;
            $api->SystemLogsAdd($userid,"bor","Received {$cash} Chivalry Tokens.");
		}
		elseif (($chance > 45) && ($chance <= 50))
		{
			$cash=Random(10,30);
			$cash=round($cash+($cash*levelMultiplier($ir['level'])));
			$infirmary=$infirmary+$cash;
            $api->SystemLogsAdd($userid,"bor","Received {$cash} Infirmary minutes.");
		}
		elseif (($chance > 50) && ($chance <= 55))
		{
			$bread=$bread+1;
            $api->SystemLogsAdd($userid,"bor","Received Bread.");
		}
		elseif (($chance > 55) && ($chance <= 60))
		{
			$venison=$venison+1;
            $api->SystemLogsAdd($userid,"bor","Received Venison.");
		}
		elseif (($chance > 60) && ($chance <= 65))
		{
			$potion=$potion+1;
            $api->SystemLogsAdd($userid,"bor","Received Small Health Potion.");
		}
		elseif (($chance > 65) && ($chance <= 70))
		{
			$rng=Random(2,4);
			$rng=round($rng+($rng*levelMultiplier($ir['level'])));
			$wraps=$wraps+$rng;
            $api->SystemLogsAdd($userid,"bor","Received {$rng} Linen Wraps.");
		}
		elseif (($chance > 70) && ($chance <= 75))
		{
			$rng=Random(2,4);
			$rng=round($rng+($rng*levelMultiplier($ir['level'])));
			$keys=$keys+$rng;
            $api->SystemLogsAdd($userid,"bor","Received {$rng} Dungeon Keys.");
		}
		elseif (($chance > 75) && ($chance <= 83))
		{
			$rng=Random(1,2);
			$rng=round($rng+($rng*levelMultiplier($ir['level'])));
			$explosives=$explosives+$rng;
            $api->SystemLogsAdd($userid,"bor","Received {$rng} Small Explosives.");
		}
		elseif (($chance > 83) && ($chance <= 85))
		{
			$gymscroll=$gymscroll+1;
            $api->SystemLogsAdd($userid,"bor","Received Chivalry Gym Pass.");
		}
		elseif (($chance > 85) && ($chance <= 88))
		{
			$attackscroll=$attackscroll+1;
            $api->SystemLogsAdd($userid,"bor","Received Distant Attack Scroll.");
		}
        elseif (($chance > 88) && ($chance <= 89))
		{
			$mystery=$mystery+1;
            $api->SystemLogsAdd($userid,"bor","Received Mysterious Potion.");
		}
        elseif (($chance > 89) && ($chance <= 92))
		{
			$needle=$needle+1;
            $api->SystemLogsAdd($userid,"bor","Received Acupuncture Needle.");
		}
		else
		{
			$nothing=$nothing+1;
            $api->SystemLogsAdd($userid,"bor","Received nothing.");
		}
	}
	$db->query("UPDATE `users` SET `bor` = `bor` - {$_POST['open']} WHERE `userid` = {$userid}");
	$db->query("UPDATE `user_settings` SET `autobor` = `autobor` - {$_POST['open']} WHERE `userid` = {$userid}");
	echo "After automatically opening {$number} Boxes of Random, you have gained the following:<br />
		{$copper} Copper Coins.<br />
		{$tokens} Chivalry Tokens.<br />
		{$infirmary} minutes in the infirmary.<br />
		{$wraps} Linen Wraps<br />
		{$keys} Dungeon Keys.<br />
		{$bread} Bread.<br />
		{$venison} Venison.<br />
		{$potion} Small Health Potion(s).<br />
		{$explosives} Small Explosive(s).<br />
		{$gymscroll} Chivalry Gym Pass(es)<br />
		{$attackscroll} Distant Attack Scroll(s)<br />
        {$needle} Acupuncture Needle(s)<br />
        {$mystery} Mysterious Potion(s)<br />
		{$nothing} Boxes of Random had nothing in them.";
	$api->UserGiveCurrency($userid,'primary',$copper);
	$api->UserGiveCurrency($userid,'secondary',$tokens);
	$api->UserStatusSet($userid,'infirmary',$infirmary,"Ticking Box");
	$api->UserGiveItem($userid,6,$wraps);
	$api->UserGiveItem($userid,30,$keys);
	$api->UserGiveItem($userid,19,$bread);
	$api->UserGiveItem($userid,20,$venison);
	$api->UserGiveItem($userid,7,$potion);
	$api->UserGiveItem($userid,28,$explosives);
	$api->UserGiveItem($userid,18,$gymscroll);
	$api->UserGiveItem($userid,90,$attackscroll);
    $api->UserGiveItem($userid,123,$mystery);
    $api->UserGiveItem($userid,100,$needle);
	$api->UserTakeItem($userid,33,$_POST['open']);
	
}
else
{
	$maxnumber = ($ir['autobor'] > $ir['bor']) ? $ir['bor'] : $ir['autobor'] ;
	echo "How many Boxes of Random would you like to open in an automated fashion? You can open {$ir['bor']} Boxes of Random today. You 
	currently have {$ir['autobor']} uses left on your Auto Box of Random Opener.
	<br />
	<form method='post'>
		<input type='number' min='1' max='{$maxnumber}' name='open' class='form-control' required='1' value='{$maxnumber}'>
		<input type='submit' value='Open Boxes of Random' class='btn btn-primary'>
	</form>";
}
$h->endpage();