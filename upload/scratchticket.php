<?php
require('globals.php');
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "cidticket":
        cidticket();
        break;
	case "2ndyearann":
        secondyearann();
        break;
    default:
        alert('danger',"Uh Oh!","Please specify an action.",true,'index.php');
		$h->endpage();
        break;
}
function cidticket()
{
	global $h,$db,$api,$userid;
	if (!$api->UserHasItem($userid,210,1))
	{
		alert('danger',"Uh Oh!","You need a CID Scratch Ticket to be here.",true,'inventory.php');
		die($h->endpage());
	}
	if (isset($_GET['scratch']))
	{
		$rng=Random(1,6);
		if ($rng == 1)
		{
			$cash=Random(3,20);
			alert("success","Success!","You scratch this spot off and you win {$cash} CID Admin Gym Access Scrolls. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,205,$cash);
		}
		elseif ($rng == 2)
		{
			$cash=Random(12,50);
			alert("success","Success!","You scratch this spot off and you win {$cash} Chivalry Gym Scrolls. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,18,$cash);
		}
		elseif ($rng == 3)
		{
			alert("success","Success!","You scratch this spot off and you win 1 Invisibility Potion. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,68,1);
		}
		elseif ($rng == 4)
		{
			alert("success","Success!","You scratch this spot off and you win a Tome of Wealth. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,198,1);
		}
		elseif ($rng == 5)
		{
			alert("success","Success!","You scratch this spot off and you win a Tome of Experience. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,148,1);
		}
		else
		{
			$cash=Random(2,7);
			alert("success","Success!","You scratch this spot off and you win {$cash} VIP Days. Congratulations!",true,'inventory.php');
			$db->query("UPDATE `users` SET `vip_days` = `vip_days` + {$cash} WHERE `userid` = {$userid}");
		}
		$api->UserTakeItem($userid,210,1);
	}
	else
	{
		echo "Select the spot you wish to scratch off. You shall receive rewards.<br />
		<div class='row'>
			<div class='col-sm'>
				<a href='?action=cidticket&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1520819511/logo.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=cidticket&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1520819511/logo.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=cidticket&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1520819511/logo.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=cidticket&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1520819511/logo.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=cidticket&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1520819511/logo.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=cidticket&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1520819511/logo.png' class='img-fluid'></a>
			</div>
		</div>";
	}
	$h->endpage();
}
function secondyearann()
{
	global $h,$db,$api,$userid;
	if (!$api->UserHasItem($userid,268,1))
	{
		alert('danger',"Uh Oh!","You need a 2nd Year Anniversary Scratch Ticket to be here.",true,'inventory.php');
		die($h->endpage());
	}
	if (isset($_GET['scratch']))
	{
		$rng=Random(1,6);
		if ($rng == 1)
		{
			$cash=Random(5,20);
			alert("success","Success!","You scratch this spot off and you win {$cash} CID Admin Gym Access Scrolls. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,205,$cash);
		}
		elseif ($rng == 2)
		{
			alert("success","Success!","You scratch this spot off and you win a Cheap Travel Voucher! Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,269,1);
		}
		elseif ($rng == 3)
		{
			alert("success","Success!","You scratch this spot off and you win 30 Maximum Mining Energy. Congratulations!",true,'inventory.php');
			$db->query("UPDATE `mining` SET `max_miningpower` = `max_miningpower` + 30 WHERE `userid` = {$userid}");
		}
		elseif ($rng == 4)
		{
			$rng=Random(1,2);
			alert("success","Success!","You scratch this spot off and you win {$rng} Will Stimulant Potion(s). Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,263,$rng);
		}
		elseif ($rng == 5)
		{
			alert("success","Success!","You scratch this spot off and you win a Tome of Experience. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,148,1);
		}
		else
		{
			$cash=Random(2,7);
			alert("success","Success!","You scratch this spot off and you win {$cash} VIP Days. Congratulations!",true,'inventory.php');
			$db->query("UPDATE `users` SET `vip_days` = `vip_days` + {$cash} WHERE `userid` = {$userid}");
		}
		$api->UserTakeItem($userid,268,1);
	}
	else
	{
		echo "Select the spot you wish to scratch off. You shall receive rewards.<br />
		<div class='row'>
			<div class='col-sm'>
				<a href='?action=2ndyearann&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1520819511/logo.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=2ndyearann&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1520819511/logo.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=2ndyearann&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1520819511/logo.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=2ndyearann&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1520819511/logo.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=2ndyearann&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1520819511/logo.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=2ndyearann&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1520819511/logo.png' class='img-fluid'></a>
			</div>
		</div>";
	}
	$h->endpage();
}