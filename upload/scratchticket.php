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
	case "2020bang":
        bang2020();
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
function bang2020()
{
	global $h,$db,$api,$userid;
	$ticketid=352;
	echo "<h3>{$api->SystemItemIDtoName($ticketid)}</h3><hr />";
	if (!$api->UserHasItem($userid,$ticketid,1))
	{
		alert('danger',"Uh Oh!","You need a {$api->SystemItemIDtoName($ticketid)} to be here.",true,'inventory.php');
		die($h->endpage());
	}
	if (isset($_GET['scratch']))
	{
		$bombRand=Random(1,9);
		$bombGive = Random(20,400);
		$bombTxt='';
		$bombItem = '';
		$bombType='';
		if ($bombRand < 6)
		{
			$bombItem = 28;
			$bombType='small';
			doDonate('small', $bombGive);
		}
		elseif (($bombRand > 5) && ($bombRand < 9))
		{
			$bombGive = round($bombGive / 4);
			$bombItem = 61;
			$bombType='medium';
			doDonate('medium', $bombGive);
		}
		else
		{
			$bombGive = round($bombGive / 20);
			$bombItem = 62;
			$bombType='large';
			doDonate('large', $bombGive);
		}
		$bombTxt = "" . number_format($bombGive) . " {$api->SystemItemIDtoName($bombItem)}(s) were added to the event's stockpile.";
		$rng=Random(1,6);
		if (Random(1, 100) == 81)
		{
			$api->UserGiveItem($userid,354,1);
			$bombTxt .= " You got lucky and also received a {$api->SystemItemIDtoName(354)}.";
			
		}
		if ($rng == 1)
		{
			//good
			$cash=Random(5,15);
			alert("success","Success!","You scratch this spot off and you win " . number_format($cash) . " {$api->SystemItemIDtoName(353)}s. {$bombTxt}",true,'inventory.php');
			$api->UserGiveItem($userid,353,$cash);
		}
		elseif ($rng == 2)
		{
			//good
			$random=Random(10,40);
			alert("success","Success!","You scratch this spot off and you win {$random} {$api->SystemItemIDtoName(61)}. {$bombTxt}",true,'inventory.php');
			$api->UserGiveItem($userid,61,$random);
		}
		elseif ($rng == 3)
		{
			//good
			$random=Random(10,50);
			alert("success","Success!","You scratch this spot off and you win {$random} Maximum Mining Energy. {$bombTxt}",true,'inventory.php');
			$db->query("UPDATE `mining` SET `max_miningpower` = `max_miningpower` + {$random} WHERE `userid` = {$userid}");
		}
		elseif ($rng == 4)
		{
			//okay
			$rng=Random(1,2);
			alert("success","Success!","You scratch this spot off and you win {$rng} Will Potion(s) . {$bombTxt}",true,'inventory.php');
			$api->UserGiveItem($userid,17,$rng);
		}
		elseif ($rng == 5)
		{
			//good
			alert("success","Success!","You scratch this spot off and you win a Tome of Experience. {$bombTxt}",true,'inventory.php');
			$api->UserGiveItem($userid,148,1);
		}
		else
		{
			//good
			$vouchers = Random(75,250);
			alert("success","Success!","You scratch this spot off and you win {$vouchers} {$api->SystemItemIDtoName(207)}s. {$bombTxt}",true,'inventory.php');
			$api->UserGiveItem($userid,207,$vouchers);
		}
		$api->UserTakeItem($userid,$ticketid,1);
	}
	else
	{
		echo "Select a spot to scratch off on this ticket. You'll receive a reward... and some bombs donated to the cache.<br />
		<div class='row'>
			<div class='col-sm'>
				<a href='?action=2020bang&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1590266160/items/20-bigbang-ticket-spot.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=2020bang&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1590266160/items/20-bigbang-ticket-spot.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=2020bang&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1590266160/items/20-bigbang-ticket-spot.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=2020bang&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1590266160/items/20-bigbang-ticket-spot.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=2020bang&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1590266160/items/20-bigbang-ticket-spot.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=2020bang&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1590266160/items/20-bigbang-ticket-spot.png' class='img-fluid'></a>
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
			$rng=Random(2,4);
			alert("success","Success!","You scratch this spot off and you win {$rng} Will Potions. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,17,$rng);
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
//Donate bombs
function doDonate($type,$count)
{
    global $db,$userid,$ir,$api;
    if ($type == 'small')
        $id=28;
    if ($type == 'medium')
        $id=61;
    if ($type == 'large')
        $id=62;
    $q=$db->query("SELECT * FROM `2019_bigbang` WHERE `userid` = {$userid}");
    if ($db->num_rows($q) == 0)
    {
        $db->query("INSERT INTO `2019_bigbang` (`userid`, `small`, `medium`, `large`) VALUES ('{$userid}', '0', '0', '0')");
    }
    $db->query("UPDATE `2019_bigbang` SET `{$type}` = `{$type}` + {$count} WHERE `userid` = {$userid}");
	return true;
}