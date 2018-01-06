<?php
require('globals.php');
if (!$api->UserHasItem($userid,89,1))
{
	alert('danger',"Uh Oh!","You need a VIP Scratch ticket to use one.",true,'inventory.php');
	die($h->endpage());
}

$rng=Random(1,6);
if ($rng == 1)
{
	$cash=Random(5000,10000);
	alert("success","Success!","You scratch this spot off and you win {$cash} Copper Coins. Congratulations!",true,'inventory.php');
	$api->UserGiveCurrency($userid,'primary',$cash);
}
elseif ($rng == 2)
{
	$cash=Random(250,500);
	alert("success","Success!","You scratch this spot off and you win {$cash} Chivalry Tokens. Congratulations!",true,'inventory.php');
	$api->UserGiveCurrency($userid,'secondary',$cash);
}
elseif ($rng == 3)
{
	$cash=Random(10,50);
	alert("success","Success!","You scratch this spot off and you win {$cash} Linen Wraps. Congratulations!",true,'inventory.php');
	$api->UserGiveItem($userid,6,$cash);
}
elseif ($rng == 4)
{
	$cash=Random(10,50);
	alert("success","Success!","You scratch this spot off and you win {$cash} Keys. Congratulations!",true,'inventory.php');
	$api->UserGiveItem($userid,30,$cash);
}
elseif ($rng == 5)
{
	$cash=Random(10,20);
	alert("success","Success!","You scratch this spot off and you win {$cash} Chivalry Gym Scrolls. Congratulations!",true,'inventory.php');
	$api->UserGiveItem($userid,18,$cash);
}
elseif ($rng == 6)
{
	$cash=Random(1,3);
	alert("success","Success!","You scratch this spot off and you win {$cash} Invisibility Potion(s). Congratulations!",true,'inventory.php');
	$api->UserGiveItem($userid,68,$cash);
}
$api->UserTakeItem($userid,89,1);
$h->endpage();