<?php
require('globals.php');
/*if ((time() < 1555819200) || (time() > 1555905600))
{
    alert("danger","Uh Oh!","Why would you want to spend your Easter Eggs before its even Easter? Try again in " . TimeUntil_Parse(1555819200) .".",true,'explore.php');
    die($h->endpage());
}*/
echo "<h3>2019 Easter Event</h3><hr />";
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "ticket":
        ticket();
        break;
    case "buymaster":
        buymaster();
        break;
    case "buygold":
        buygold();
        break;
    case "buytokens":
        buytokens();
        break;
    case "buycopper":
        buycopper();
        break;
    case "buybomb":
        buybomb();
        break;
    case "buyxp":
        buyxp();
        break;
    case "shop":
        shop();
        break;
    default:
        alert('danger',"Uh Oh!","Please specify an action.",true,'index.php');
        break;
}
function ticket()
{
	global $h,$db,$api,$userid;
	if (!$api->UserHasItem($userid,230,1))
	{
		alert('danger',"Uh Oh!","You need a 2019 Easter Scratch Off to be here.",true,'inventory.php');
		die($h->endpage());
	}
	if (isset($_GET['scratch']))
	{
		$rng=Random(1,8);
		if ($rng == 1)
		{
			$cash=Random(2500000,5000000);
			alert("success","Success!","You scratch your ticket and find a voucher for {$cash} Copper Coins.",true,'inventory.php');
			$api->UserGiveCurrency($userid,'primary',$cash);
		}
		elseif ($rng == 2)
		{
			$cash=Random(850,3000);
			alert("success","Success!","You scratch your ticket and find a voucher for {$cash} Chivalry Tokens.",true,'inventory.php');
			$api->UserGiveCurrency($userid,'secondary',$cash);
		}
		elseif ($rng == 3)
		{
            $cash = Random(3,10);
			alert("success","Success!","You scratch your ticket and find a voucher for {$cash} Chocolate Bars.",true,'inventory.php');
			$api->UserGiveItem($userid,139,$cash);
		}
		elseif ($rng == 4)
		{
			$cash=Random(3,20);
			alert("success","Success!","You scratch this spot off and you win {$cash} CID Admin Gym Access Scrolls. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,205,$cash);
		}
		elseif ($rng == 5)
		{
			alert("success","Success!","You scratch this spot off and you win a Tome of Wealth. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,198,1);
		}
        elseif ($rng == 6)
        {
            alert("success","Success!","You scratch this spot off and you win a Tome of Experience. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,148,1);
        }
        elseif ($rng == 7)
        {
            $cash=Random(5,10);
            alert("success","Success!","You scratch this spot off and you win {$cash} Mining Energy Potions. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,227,$cash);
        }
		else
		{
			$cash=Random(2,7);
			alert("success","Success!","You scratch your ticket and find a voucher for {$cash} VIP Days inside!",true,'inventory.php');
			$db->query("UPDATE `users` SET `vip_days` = `vip_days` + {$cash} WHERE `userid` = {$userid}");
		}
		$api->UserTakeItem($userid,230,1);
	}
	else
	{
		echo "Select the spot you wish to scratch for your reward.<br />
		<div class='row'>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1522424247/EasterEgg5.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1522424247/EasterEgg5.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1522424247/EasterEgg5.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1522424247/EasterEgg5.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1522424247/EasterEgg5.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='https://res.cloudinary.com/dydidizue/image/upload/v1522424247/EasterEgg5.png' class='img-fluid'></a>
			</div>
		</div>";
	}
}

function shop()
{
    global $ir,$api,$h,$db,$userid,$colored;
    echo "Welcome to the 2019 Easter Shop. You may exchange your found Easter Eggs for goodies here. You currently have 
    {$api->UserCountItem($userid,229)} Easter Eggs.
    <br />
    <a href='?action=buygold' class='btn btn-primary'>Golden Egg - 5 Easter Eggs</a><br /><br />
    <a href='?action=buytokens' class='btn btn-primary'>500 Chivalry Tokens - 10 Easter Eggs</a><br /><br />
    <a href='?action=buycopper' class='btn btn-primary'>500,000 Copper Coins - 10 Easter Eggs</a><br /><br />
    <a href='?action=buybomb' class='btn btn-primary'>Rickity Bomb - 15 Easter Eggs</a><br /><br />
    <a href='?action=buymaster' class='btn btn-primary'>Master Egg - 25 Easter Eggs</a><br /><br />
    <a href='?action=buyxp' class='btn btn-primary'>Tome of Experience - 45 Easter Eggs</a><br /><br />";
}
function buymaster()
{
    global $ir,$api,$h,$db,$userid,$colored;
    if ($api->UserCountItem($userid,229) < 25)
    {
        alert('danger',"Uh Oh!","You do not have enough Easter Eggs to buy a Master Egg.",true,'?action=shop');
        die($h->endpage());
    }
    else
    {
        $api->UserTakeItem($userid,229,25);
        $api->UserGiveItem($userid,146,1);
        alert('success',"Success!","You have successfully traded 25 Easter Eggs for a Master Easter Egg.",true,'?action=shop');
    }
}
function buygold()
{
    global $ir,$api,$h,$db,$userid,$colored;
    if ($api->UserCountItem($userid,229) < 5)
    {
        alert('danger',"Uh Oh!","You do not have enough Easter Eggs to buy a Golden Egg.",true,'?action=shop');
        die($h->endpage());
    }
    else
    {
        $api->UserTakeItem($userid,229,5);
        $api->UserGiveItem($userid,145,1);
        alert('success',"Success!","You have successfully traded 5 Easter Eggs for a Golden Easter Egg.",true,'?action=shop');
    }
}
function buytokens()
{
    global $ir,$api,$h,$db,$userid,$colored;
    if ($api->UserCountItem($userid,229) < 10)
    {
        alert('danger',"Uh Oh!","You do not have enough Easter Eggs to buy 500 Chivalry Tokens.",true,'?action=shop');
        die($h->endpage());
    }
    else
    {
        $api->UserTakeItem($userid,229,10);
        $api->UserGiveCurrency($userid,'secondary',500);
        alert('success',"Success!","You have successfully traded 10 Easter Eggs for 500 Chivalry Tokens.",true,'?action=shop');
    }
}
function buycopper()
{
    global $ir,$api,$h,$db,$userid,$colored;
    if ($api->UserCountItem($userid,229) < 10)
    {
        alert('danger',"Uh Oh!","You do not have enough Easter Eggs to buy 500,000 Copper Coins.",true,'?action=shop');
        die($h->endpage());
    }
    else
    {
        $api->UserTakeItem($userid,229,10);
        $db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + 500000 WHERE `userid` = {$userid}");
        alert('success',"Success!","You have successfully traded 10 Colored Easter Eggs for 500,000 Copper Coins.",true,'?action=shop');
    }
}
function buybomb()
{
    global $ir,$api,$h,$db,$userid,$colored;
    if ($api->UserCountItem($userid,229) < 15)
    {
        alert('danger',"Uh Oh!","You do not have enough Easter Eggs to buy a Rickity Bomb.",true,'?action=shop');
        die($h->endpage());
    }
    else
    {
        $api->UserTakeItem($userid,229,15);
        $api->UserGiveItem($userid,149,1);
        alert('success',"Success!","You have successfully traded 15 Easter Eggs for a Rickity Bomb.",true,'?action=shop');
    }
}
function buyxp()
{
    global $ir,$api,$h,$db,$userid,$colored;
    if ($api->UserCountItem($userid,229) < 45)
    {
        alert('danger',"Uh Oh!","You do not have enough Easter Eggs to buy a Tome of Knowledge.",true,'?action=shop');
        die($h->endpage());
    }
    else
    {
        $api->UserTakeItem($userid,229,45);
        $api->UserGiveItem($userid,148,1);
        alert('success',"Success!","You have successfully traded 45 Easter Eggs for a Tome of Knowledge.",true,'?action=shop');
    }
}
$h->endpage();