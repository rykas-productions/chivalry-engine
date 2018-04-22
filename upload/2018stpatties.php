<?php
require('globals.php');
echo "<h3>2018 St. Patrick's Day Event</h3><hr />";
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "exchange":
        exchange();
        break;
    case "doexchange1":
        doexchange(1,25);
    case "doexchange2":
        doexchange(5,200);
    case "doexchange3":
        doexchange(10,500);
    case "doexchange4":
        doexchange(25,1500);
    case "doexchange5":
        doexchange(100,5000);
    case "ticket":
        ticket();
        break;
    default:
        alert('danger',"Uh Oh!","Please specify an action.",true,'index.php');
        break;
}
function exchange()
{
    echo "Leprechauns are tricky folks, for sure, but, they're reasonable. They'll barter their freedom from you, you just list your price.<br />
    <a href='?action=doexchange1'>1 Leprechauns = 25 Chivalry Tokens</a><br />
    <a href='?action=doexchange2'>5 Leprechauns = 200 Chivalry Tokens</a><br />
    <a href='?action=doexchange3'>10 Leprechauns = 500 Chivalry Tokens</a><br />
    <a href='?action=doexchange4'>25 Leprechauns = 1,500 Chivalry Tokens</a><br />
    <a href='?action=doexchange5'>100 Leprechauns = 5,000 Chivalry Tokens</a><br />";
}
function doexchange($needed,$payout)
{
    global $db,$ir,$userid,$api,$h;
    if ($api->UserHasItem($userid,136,$needed))
    {
        $api->UserGiveCurrency($userid,'secondary',$payout);
        $api->UserTakeItem($userid,136,$needed);
        alert('success',"Success!","You've let {$needed} Leprechauns free in-exchange for {$payout} Chivalry Tokens.",false);
    }
    else
    {
        alert('danger',"Uh Oh!","You do not have enough Leprechauns for this deal.",false);
    }
    exchange();
    die($h->endpage());
}
function ticket()
{
	global $h,$db,$api,$userid;
	if (!$api->UserHasItem($userid,137,1))
	{
		alert('danger',"Uh Oh!","You need a 2018 St. Patties Day Scratch Ticket to be here.",true,'inventory.php');
		die($h->endpage());
	}
	if (isset($_GET['scratch']))
	{
		$rng=Random(1,6);
		if ($rng == 1)
		{
			$cash=Random(50000,1000000);
			alert("success","Success!","You scratch this spot off and you win {$cash} Copper Coins. Congratulations!",true,'inventory.php');
			$api->UserGiveCurrency($userid,'primary',$cash);
		}
		elseif ($rng == 2)
		{
			$cash=Random(500,5000);
			alert("success","Success!","You scratch this spot off and you win {$cash} Chivalry Tokens. Congratulations!",true,'inventory.php');
			$api->UserGiveCurrency($userid,'secondary',$cash);
		}
		elseif ($rng == 3)
		{
			alert("success","Success!","You scratch this spot off and you win 3 Invisibility Potions. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,68,3);
		}
		elseif ($rng == 4)
		{
			$cash=Random(15,45);
			alert("success","Success!","You scratch this spot off and you win {$cash} Chivalry Gym Scrolls. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,18,$cash);
		}
		elseif ($rng == 5)
		{
			$cash=Random(5,10);
			alert("success","Success!","You scratch this spot off and you win {$cash} Medium Explosives. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,61,$cash);
		}
		else
		{
			$cash=Random(1,3);
			alert("success","Success!","You scratch this spot off and you win {$cash} VIP Days. Congratulations!",true,'inventory.php');
			$db->query("UPDATE `users` SET `vip_days` = `vip_days` + {$cash} WHERE `userid` = {$userid}");
		}
		$api->UserTakeItem($userid,137,1);
	}
	else
	{
		echo "/*qc=on*/SELECT the spot you wish to scratch off. You shall receive rewards.<br />
		<div class='row'>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1521221237/green-shamrock-hi.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1521221237/green-shamrock-hi.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1521221237/green-shamrock-hi.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1521221237/green-shamrock-hi.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1521221237/green-shamrock-hi.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1521221237/green-shamrock-hi.png' class='img-fluid'></a>
			</div>
		</div>";
	}
}
$h->endpage();