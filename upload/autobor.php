<?php
$macropage = ('autobor.php');
$multipler=1.0;
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
	$lvlmultiplier=levelMultiplier($ir['level']);
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
	$hexbags=0;
	$rickitybomb=0;
	$herbofminer=0;
	$nothing=0;
	while($number < $_POST['open'])
	{
		$number=$number+1;
		$chance=Random(1,87);
		if ($chance <= 35)
		{
			$cash=Random(750,2250)*$multipler;
			$cash=round($cash+($cash*$lvlmultiplier));
			$copper=$copper+$cash;
		}
		elseif (($chance > 35) && ($chance <= 40))
		{
			$cash=Random(5,20)*$multipler;
			$specialnumber=((getSkillLevel($userid,11)*5)/100);
			$cash=round($cash+($cash*$specialnumber));
			$cash=round($cash+($cash*$lvlmultiplier));
			$tokens=$tokens+$cash;
		}
		elseif (($chance > 45) && ($chance <= 50))
		{
			$cash=Random(1,3)*$multipler;
			$cash=round($cash+($cash*$lvlmultiplier));
			$infirmary=$infirmary+$cash;
		}
		elseif (($chance > 50) && ($chance <= 55))
		{
			$bread=$bread+1;
		}
		elseif (($chance > 55) && ($chance <= 60))
		{
			$venison=$venison+1;
		}
		elseif (($chance > 60) && ($chance <= 65))
		{
			$potion=$potion+1;
		}
		elseif (($chance > 65) && ($chance <= 70))
		{
			$rng=Random(2,4)*$multipler;
			$rng=round($rng+($rng*$lvlmultiplier));
			$wraps=$wraps+$rng;
		}
		elseif (($chance > 70) && ($chance <= 75))
		{
			$rng=Random(2,4)*$multipler;
			$rng=round($rng+($rng*$lvlmultiplier));
			$keys=$keys+$rng;
		}
		elseif (($chance > 75) && ($chance <= 78))
		{
			$rng=Random(1,2)*$multipler;
			$rng=round($rng+($rng*$lvlmultiplier));
			$explosives=$explosives+$rng;
		}
		elseif (($chance > 78) && ($chance <= 80))
		{
			$gymscroll=$gymscroll+1;
		}
		elseif (($chance > 80) && ($chance <= 81))
		{
			$attackscroll=$attackscroll+1;
		}
        elseif (($chance > 81) && ($chance <= 82))
		{
			$mystery=$mystery+1;
		}
        elseif (($chance > 82) && ($chance <= 83))
		{
			$needle=$needle+1;
		}
		elseif ($chance == 84)
		{
			if (Random(1,10) == 9)
			{
				$rickitybomb=$rickitybomb+1;
			}
			else
			{
				$nothing=$nothing+1;
			}
		}
		elseif ($chance == 85)
		{
			if (Random(1,10) == 9)
			{
				$hexbags=$hexbags+Random(1,3);
			}
			else
			{
				$nothing=$nothing+1;
			}
		}
		elseif ($chance == 86)
		{
			$herbofminer=$herbofminer+1;
		}
		else
		{
			$nothing=$nothing+1;
		}
	}
	$db->query("UPDATE `users` SET `bor` = `bor` - {$_POST['open']} WHERE `userid` = {$userid}");
	$db->query("UPDATE `user_settings` SET `autobor` = `autobor` - {$_POST['open']} WHERE `userid` = {$userid}");
	alert('success',"Success!","You've opened " . number_format($_POST['open']) . " Boxes of Random in an automated fashion and receieved the following items.",false);
	echo "<div class='row'>";
	if ($copper > 0)
	{
		echo "<div class='col-md-3'>
			<div class='card'>
			<div class='card-body'>
				" . returnIcon(157, 8) . "<br />
				" . shortNumberParse($copper) . " Copper Coins.
			</div>
			</div>
		</div>
		<br />";
		$api->UserGiveCurrency($userid,'primary',$copper);
		$api->SystemLogsAdd($userid,"bor","Received {$copper} Copper Coins.");
		addToEconomyLog('BOR', 'copper', $copper);
	}
	if ($tokens > 0)
	{
		echo "<div class='col-md-3'>
		<div class='card'>
			<div class='card-body'>
				" . returnIcon(156, 8) . "<br />
				" . shortNumberParse($tokens) . " Chivalry Tokens.
			</div>
			</div>
		</div>
		<br />";
		$api->UserGiveCurrency($userid,'secondary',$tokens);
		$api->SystemLogsAdd($userid,"bor","Received {$tokens} Chivalry Tokens.");
		addToEconomyLog('BOR', 'token', $tokens);
	}
	if ($infirmary > 0)
	{
		echo "<div class='col-md-3'>
		<div class='card'>
			<div class='card-body'>
				" . returnIcon(207, 8) . "<br />
				" . number_format($infirmary) . " minutes in the infirmary.
			</div>
			</div>
		</div>
		<br />";
		$api->UserStatusSet($userid,'infirmary',$infirmary,"Ticking Box");
		$api->SystemLogsAdd($userid,"bor","Received {$infirmary} infirmary.");
	}
	if ($wraps > 0)
	{
		echo "<div class='col-md-3'>
		<div class='card'>
			<div class='card-body'>
				" . returnIcon(6, 8) . "<br />
				" . number_format($wraps) . " Linen Wraps
			</div>
			</div>
		</div>
		<br />";
		$api->UserGiveItem($userid,6,$wraps);
		$api->SystemLogsAdd($userid,"bor","Received {$wraps} Linen Wraps.");
	}
	if ($keys > 0)
	{
		echo "<div class='col-md-3'>
		<div class='card'>
			<div class='card-body'>
				" . returnIcon(30, 8) . "<br />
				" . number_format($keys) . " Dungeon Keys.
			</div>
			</div>
		</div>
		<br />";
		$api->UserGiveItem($userid,30,$keys);
		$api->SystemLogsAdd($userid,"bor","Received {$keys} Dungeon Keys.");
	}
	if ($bread > 0)
	{
		echo "<div class='col-md-3'>
		<div class='card'>
			<div class='card-body'>
				" . returnIcon(19, 8) . "<br />
				" . number_format($bread) . " Bread.
			</div>
			</div>
		</div>
		<br />";
		$api->UserGiveItem($userid,19,$bread);
		$api->SystemLogsAdd($userid,"bor","Received {$bread} Bread.");
	}
	if ($venison > 0)
	{
		echo "<div class='col-md-3'>
		<div class='card'>
			<div class='card-body'>
				" . returnIcon(20, 8) . "<br />
				" . number_format($venison) . " Venison.
			</div>
			</div>
		</div>
		<br />";
		$api->UserGiveItem($userid,20,$venison);
		$api->SystemLogsAdd($userid,"bor","Received {$venison} Venison.");
	}
	if ($potion > 0)
	{
		echo "<div class='col-md-3'>
		<div class='card'>
			<div class='card-body'>
				" . returnIcon(7, 8) . "<br />
				" . number_format($potion) . " Small Health Potions.
			</div>
			</div>
		</div>
		<br />";
		$api->UserGiveItem($userid,7,$potion);
		$api->SystemLogsAdd($userid,"bor","Received {$potion} Small Health Potion(s).");
	}
	if ($explosives > 0)
	{
		echo "<div class='col-md-3'>
		<div class='card'>
			<div class='card-body'>
				" . returnIcon(28, 8) . "<br />
				" . number_format($explosives) . " Small Explosives.
			</div>
			</div>
		</div>
		<br />";
		$api->UserGiveItem($userid,28,$explosives);
		$api->SystemLogsAdd($userid,"bor","Received {$explosives} Small Explosives.");
	}
	if ($gymscroll > 0)
	{
		echo "<div class='col-md-3'>
		<div class='card'>
			<div class='card-body'>
				" . returnIcon(18, 8) . "<br />
				" . number_format($gymscroll) . " Chivalry Gym Scrolls.
			</div>
			</div>
		</div>
		<br />";
		$api->UserGiveItem($userid,18,$gymscroll);
		$api->SystemLogsAdd($userid,"bor","Received {$gymscroll} Chivalry Gym Scrolls.");
	}
	if ($attackscroll > 0)
	{
		echo "<div class='col-md-3'>
		<div class='card'>
			<div class='card-body'>
				" . returnIcon(90, 8) . "<br />
				" . number_format($attackscroll) . " Distant Attack Scrolls.
			</div>
			</div>
		</div>
		<br />";
		$api->UserGiveItem($userid,90,$attackscroll);
		$api->SystemLogsAdd($userid,"bor","Received {$attackscroll} Distant Attack Scrolls.");
	}
	if ($needle > 0)
	{
		echo "<div class='col-md-3'>
		<div class='card'>
			<div class='card-body'>
				" . returnIcon(90, 8) . "<br />
				" . number_format($needle) . " Acupuncture Needles.
			</div>
			</div>
		</div>
		<br />";
		$api->UserGiveItem($userid,100,$needle);
		$api->SystemLogsAdd($userid,"bor","Received {$needle} Acupuncture Needles.");
	}
	if ($mystery > 0)
	{
		echo "<div class='col-md-3'>
		<div class='card'>
			<div class='card-body'>
				" . returnIcon(123, 8) . "<br />
				" . number_format($mystery) . " Mysterious Potions.
			</div>
			</div>
		</div>
		<br />";
		$api->UserGiveItem($userid,123,$mystery);
		$api->SystemLogsAdd($userid,"bor","Received {$mystery} Mysterious Potions.");
	}
	if ($hexbags > 0)
	{
		echo "<div class='col-md-3'>
		<div class='card'>
			<div class='card-body'>
				<i class='game-icon game-icon-open-treasure-chest' style='font-size:8rem;'></i><br />
				" . number_format($hexbags) . " extra Hexbags.
			</div>
		</div>
		</div>
		<br />";
		$db->query("UPDATE `users` SET `hexbags` = `hexbags` + {$hexbags} WHERE `userid` = {$userid}");
		$api->SystemLogsAdd($userid,"bor","Received {$hexbags} Hexbags.");
	}
	if ($rickitybomb > 0)
	{
		echo "<div class='col-md-3'>
		<div class='card'>
			<div class='card-body'>
				" . returnIcon(149, 8) . "<br />
				" . number_format($rickitybomb) . " Rickity Bombs.
			</div>
			</div>
		</div>
		<br />";
		$api->UserGiveItem($userid,149,$rickitybomb);
		$api->SystemLogsAdd($userid,"bor","Received {$rickitybomb} Rickity Bomb(s).");
	}
	if ($herbofminer > 0)
	{
		echo "<div class='col-md-3'>
			<div class='card'>
			<div class='card-body'>
				" . returnIcon(177, 8) . "<br />
				" . number_format($herbofminer) . " Herbs of the Enlightened Miner.
			</div>
			</div>
		</div>
		<br />";
		$api->UserGiveItem($userid,177,$herbofminer);
		$api->SystemLogsAdd($userid,"bor","Received {$herbofminer} Herbs of the Enlightened Miner.");
	}
	if ($nothing > 0)
	{
		echo "<div class='col-md-3'>
			<div class='card'>
			<div class='card-body'>
				" . number_format($nothing) . " boxes of random had no contents.
			</div>
			</div>
		</div>
		<br />";
	}
	echo "</div>";
	$api->UserTakeItem($userid,33,$_POST['open']);
	//Logs here
	
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