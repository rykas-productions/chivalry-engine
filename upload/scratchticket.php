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
	case "24halloween":
	    halloween24();
	    break;
    default:
        alert('danger',"Uh Oh!","Please specify an action.",true,'index.php');
		$h->endpage();
        break;
}
function cidticket()
{
	global $h,$db,$api,$userid;
	$openTime = getCurrentUserPref('cidScratchTime', 0);
	$lootJSON = "items/scratch/cid_ticket";
	if (!$api->UserHasItem($userid,210,1))
	{
		alert('danger',"Uh Oh!","You need a CID Scratch Ticket to be here.",true,'inventory.php');
		die($h->endpage());
	}
	if (time() <= $openTime + 1400)
	{
	    alert('danger',"Uh Oh!","Slow down there buddy, there's a cooldown when scratching {$api->SystemItemIDtoName(210)}s. Try again in " . TimeUntil_Parse($openTime + 1400) . ".",true,'inventory.php');
	    die($h->endpage());
	}
	if (isset($_GET['scratch']))
	{
	    $loot = giveUserLoot($userid, $lootJSON);
	    alert("success","Success!","You begin to scratch this spot off on a {$api->SystemItemIDtoName(210)}. {$loot} Congratulations!",true,'inventory.php');
		setCurrentUserPref("cidScratchTime", time());
		$api->UserTakeItem($userid,210,1);
	}
	else
	{
		echo "
        <div class='card'>
            <div class='card-header'>
                Scratching off a {$api->SystemItemIDtoName(210)}...
            </div>
            <div class='card-body'>
        		<div class='row'>
        			<div class='col-sm'>
        				<a href='?action=cidticket&scratch=1'><img src='". returnAssetDir() ."img/logo/logo512.png' class='img-fluid'></a>
        			</div>
        			<div class='col-sm'>
        				<a href='?action=cidticket&scratch=1'><img src='". returnAssetDir() ."img/logo/logo512.png' class='img-fluid'></a>
        			</div>
        			<div class='col-sm'>
        				<a href='?action=cidticket&scratch=1'><img src='". returnAssetDir() ."img/logo/logo512.png' class='img-fluid'></a>
        			</div>
        			<div class='col-sm'>
        				<a href='?action=cidticket&scratch=1'><img src='". returnAssetDir() ."img/logo/logo512.png' class='img-fluid'></a>
        			</div>
        			<div class='col-sm'>
        				<a href='?action=cidticket&scratch=1'><img src='". returnAssetDir() ."img/logo/logo512.png' class='img-fluid'></a>
        			</div>
        			<div class='col-sm'>
        				<a href='?action=cidticket&scratch=1'><img src='". returnAssetDir() ."img/logo/logo512.png' class='img-fluid'></a>
        			</div>
        		</div>
                <div class='row'>
                    " . parseLootTableOdds($lootJSON) . "
                </div>
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
function halloween24()
{
    global $h,$db,$api,$userid,$ir;
    $itemToHave = 514;
    if (!$api->UserHasItem($userid,$itemToHave,1))
    {
        alert('danger',"Uh Oh!","You need a {$api->SystemItemIDtoName($itemToHave)} to be here.",true,'inventory.php');
        die($h->endpage());
    }
    //free pumpkins baby!
    $pumpkins = round(Random(15,55) * levelMultiplier($ir['level']));
    $api->UserGiveItem($userid, 64, $pumpkins);
    if (isset($_GET['scratch']))
    {
        $rng=Random(1,6);
        //copper award
        if ($rng == 1)
        {
            $cash = round(Random(500000,1000000) * levelMultiplier($ir['level']));
            alert("success","Success!","You scratch this spot off and you win " . shortNumberParse($cash) . "
                    Copper Coins along with " . shortNumberParse($pumpkins) . " compliementary Pumpkins!
                    Happy Halloween!",true,'inventory.php');
            $api->UserGiveCurrency($userid,'primary',$cash);
        }
        //token awward
        elseif ($rng == 2)
        {
            $cash=round(Random(8500,17500) * levelMultiplier($ir['level']));
            alert("success","Success!","You scratch this spot off and you win " . shortNumberParse($cash) . "
                    Chivalry Tokens along with " . shortNumberParse($pumpkins) . " compliementary Pumpkins!
                    Happy Halloween!",true,'inventory.php');
            $api->UserGiveCurrency($userid,'secondary',$cash);
        }
        //cid admin gym scroll award
        elseif ($rng == 4)
        {
            $cash=round(Random(15,50) * levelMultiplier($ir['level']));
            alert("success","Success!","You scratch this spot off and you win {$cash} CID Admin Gym
                    Access Scrolls along with " . shortNumberParse($pumpkins) . " compliementary
                    Pumpkins! Happy Halloween!",true,'inventory.php');
            $api->UserGiveItem($userid,205,$cash);
        }
        //lrg boom
        elseif ($rng == 5)
        {
            $cash=round(Random(7,15) * levelMultiplier($ir['level']));
            alert("success","Success!","You scratch this spot off and you win {$cash} Large Explosives
                    along with " . shortNumberParse($pumpkins) . " compliementary Pumpkins! Happy
                    Halloween!",true,'inventory.php');
            $api->UserGiveItem($userid,62,$cash);
        }
        //vip pack
        else
        {
            alert("success","Success!","You scratch this spot off and you win a free $3 VIP Pack
                    along with " . shortNumberParse($pumpkins) . " compliementary Pumpkins! Happy
                    Halloween!",true,'inventory.php');
            $api->UserGiveItem($userid,421,1);
        }
        $api->UserTakeItem($userid, $itemToHave, 1);
        
    }
    else
    {
        echo "
        <div class='card'>
            <div class='card-header'>
                Hope you have a wonderful 2024 Halloween season! Scratch this off for prizes!
            </div>
            <div class='card-body'>
        		<div class='row'>
        			<div class='col-12 col-sm-4 col-xl'>
        				<a href='?action=24halloween&scratch=1'><img src='https://cdn.chivalryisdeadgame.com//assets/img/pumpkin-halloween.png' class='img-fluid'></a>
        			</div>
        			<div class='col-12 col-sm-4 col-xl'>
        				<a href='?action=24halloween&scratch=1'><img src='https://cdn.chivalryisdeadgame.com//assets/img/pumpkin-halloween.png' class='img-fluid'></a>
        			</div>
        			<div class='col-12 col-sm-4 col-xl'>
        				<a href='?action=24halloween&scratch=1'><img src='https://cdn.chivalryisdeadgame.com//assets/img/pumpkin-halloween.png' class='img-fluid'></a>
        			</div>
        			<div class='col-12 col-sm-4 col-xl'>
        				<a href='?action=24halloween&scratch=1'><img src='https://cdn.chivalryisdeadgame.com//assets/img/pumpkin-halloween.png' class='img-fluid'></a>
        			</div>
        			<div class='col-12 col-sm-4 col-xl'>
        				<a href='?action=24halloween&scratch=1'><img src='https://cdn.chivalryisdeadgame.com//assets/img/pumpkin-halloween.png' class='img-fluid'></a>
        			</div>
        			<div class='col-12 col-sm-4 col-xl'>
        				<a href='?action=24halloween&scratch=1'><img src='https://cdn.chivalryisdeadgame.com//assets/img/pumpkin-halloween.png' class='img-fluid'></a>
        			</div>
        		</div>
            </div>";
    }
    die($h->endpage());
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