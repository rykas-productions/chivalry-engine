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
    $cash = Random(50000 * levelMultiplier($ir['level']),
        100000 * levelMultiplier($ir['level']));
	alert("success","Success!","You scratch this spot off and you win " . shortNumberParse($cash) . " Copper Coins. Congratulations!",true,'inventory.php');
	$api->UserGiveCurrency($userid,'primary',$cash);
	addToEconomyLog('Scratch Ticket', 'copper', $cash);
}
elseif ($rng == 2)
{
    $cash=Random(5000 * levelMultiplier($ir['level']),
        7500 *levelMultiplier($ir['level']));
	alert("success","Success!","You scratch this spot off and you win " . shortNumberParse($cash) . " Chivalry Tokens. Congratulations!",true,'inventory.php');
	$api->UserGiveCurrency($userid,'secondary',$cash);
	addToEconomyLog('Scratch Ticket', 'token', $cash);
}
elseif ($rng == 3)
{
    //@todo make this gift random badge
	alert("success","Success!","You scratch this spot off and you win a Salty Badge. Congratulations!",true,'inventory.php');
	$api->UserGiveItem($userid,163,1);
}
elseif ($rng == 4)
{
	alert("success","Success!","You scratch this spot off and you win a VIP Shield Badge. Congratulations!",true,'inventory.php');
	$api->UserGiveItem($userid,159,1);
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
$api->SystemLogsAdd($userid, 'itemuse', "Used VIP Scratch Ticket.");
$h->endpage();