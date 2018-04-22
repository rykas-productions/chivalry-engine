<?php
require('globals.php');
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "ticket":
        ticket();
        break;
    case "chuck":
        chuck();
        break;
	case "tnt":
        tnt();
        break;
    default:
        alert('danger',"Uh Oh!","Please specify an action.",true,'index.php');
        break;
}
function ticket()
{
	global $h,$db,$api,$userid;
	if (!$api->UserHasItem($userid,63,1))
	{
		alert('danger',"Uh Oh!","You need a 2017 Halloween Scratch Ticket to be here.",true,'inventory.php');
		die($h->endpage());
	}
	if (isset($_GET['scratch']))
	{
		$rng=Random(1,6);
		if ($rng == 1)
		{
			$cash=Random(500,10000);
			alert("success","Success!","You scratch this spot off and you win {$cash} Copper Coins. Congratulations!",true,'inventory.php');
			$api->UserGiveCurrency($userid,'primary',$cash);
		}
		elseif ($rng == 2)
		{
			$cash=Random(50,500);
			alert("success","Success!","You scratch this spot off and you win {$cash} Chivalry Tokens. Congratulations!",true,'inventory.php');
			$api->UserGiveCurrency($userid,'secondary',$cash);
		}
		elseif ($rng == 3)
		{
			$cash=Random(10,25);
			alert("success","Success!","You scratch this spot off and you win {$cash} pieces of Halloween Candy. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,66,$cash);
		}
		elseif ($rng == 4)
		{
			$cash=Random(5,15);
			alert("success","Success!","You scratch this spot off and you win {$cash} Chivalry Gym Scrolls. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,18,$cash);
		}
		elseif ($rng == 5)
		{
			$cash=Random(10,30);
			alert("success","Success!","You scratch this spot off and you win {$cash} Small Explosives. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,28,$cash);
		}
		else
		{
			$cash=Random(1,3);
			alert("success","Success!","You scratch this spot off and you win {$cash} VIP Days. Congratulations!",true,'inventory.php');
			$db->query("UPDATE `users` SET `vip_days` = `vip_days` + {$cash} WHERE `userid` = {$userid}");
		}
		$api->UserTakeItem($userid,63,1);
	}
	else
	{
		echo "/*qc=on*/SELECT the spot you wish to scratch off. You shall receive rewards.<br />
		<div class='row'>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='assets/img/pumpkin-halloween.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='assets/img/pumpkin-halloween.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='assets/img/pumpkin-halloween.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='assets/img/pumpkin-halloween.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='assets/img/pumpkin-halloween.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='assets/img/pumpkin-halloween.png' class='img-fluid'></a>
			</div>
		</div>";
	}
}
$h->endpage();