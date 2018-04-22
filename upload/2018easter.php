<?php
require('globals.php');
echo "<h3>2018 Easter Event</h3><hr />";
$colored=$api->UserCountItem($userid,140)+$api->UserCountItem($userid,141)+$api->UserCountItem($userid,142)+$api->UserCountItem($userid,143)+$api->UserCountItem($userid,144);
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
    case "eggcoin1":
        egg_to_currency(140);
        break;
    case "eggcoin2":
        egg_to_currency(141);
        break;
    case "eggcoin3":
        egg_to_currency(142);
        break;
    case "eggcoin4":
        egg_to_currency(143);
        break;
    case "eggcoin5":
        egg_to_currency(144);
        break;
    default:
        alert('danger',"Uh Oh!","Please specify an action.",true,'index.php');
        break;
}
function ticket()
{
	global $h,$db,$api,$userid;
	if (!$api->UserHasItem($userid,147,1))
	{
		alert('danger',"Uh Oh!","You need a 2018 Easter Egg Squish to be here.",true,'inventory.php');
		die($h->endpage());
	}
	if (isset($_GET['scratch']))
	{
		$rng=Random(1,6);
		if ($rng == 1)
		{
			$cash=Random(250000,500000);
			alert("success","Success!","You chuck your egg on the ground and find a voucher for {$cash} Copper Coins.",true,'inventory.php');
			$api->UserGiveCurrency($userid,'primary',$cash);
		}
		elseif ($rng == 2)
		{
			$cash=Random(850,3000);
			alert("success","Success!","You chuck your egg at your enemy and find a voucher for {$cash} Chivalry Tokens.",true,'inventory.php');
			$api->UserGiveCurrency($userid,'secondary',$cash);
		}
		elseif ($rng == 3)
		{
            $cash = Random(3,10);
			alert("success","Success!","You crush your egg in your hand and find a voucher for {$cash} Chocolate Bars.",true,'inventory.php');
			$api->UserGiveItem($userid,139,$cash);
		}
		elseif ($rng == 4)
		{
			$cash=Random(15,30);
			alert("success","Success!","Your draw your bow and arrow and shoot at your egg. You receive a voucher for {$cash} Chivalry Gym Scrolls.",true,'inventory.php');
			$api->UserGiveItem($userid,18,$cash);
		}
		elseif ($rng == 5)
		{
			$cash=Random(2,5);
			alert("success","Success!","Your rig your egg with a small explosive and set if off. You find a voucher for {$cash} Rickity Bombs in the aftermath.",true,'inventory.php');
			$api->UserGiveItem($userid,149,$cash);
		}
		else
		{
			$cash=Random(1,3);
			alert("success","Success!","You crack open your egg like a civilized person and find a voucher for {$cash} VIP Days inside!",true,'inventory.php');
			$db->query("UPDATE `users` SET `vip_days` = `vip_days` + {$cash} WHERE `userid` = {$userid}");
		}
		$api->UserTakeItem($userid,147,1);
	}
	else
	{
		echo "/*qc=on*/SELECT egg you wish to squish. You will find a voucher for your reward in the mess that remains.<br />
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
    echo "Welcome to the 2018 Easter Shop. You may exchange your found Easter Eggs for goodies here. You currently have 
    {$api->UserCountItem($userid,150)} Egg Coins, and {$api->UserCountItem($userid,145)} Golden Eggs. How do you wish to spend them today?
    <br />
    <a href='?action=eggcoin1' class='btn btn-primary'>Egg Coin - 1 Red Easter Egg</a><br /><br />
    <a href='?action=eggcoin2' class='btn btn-primary'>Egg Coin - 1 Magenta Easter Egg</a><br /><br />
    <a href='?action=eggcoin3' class='btn btn-primary'>Egg Coin - 1 Yellow Easter Egg</a><br /><br />
    <a href='?action=eggcoin4' class='btn btn-primary'>Egg Coin - 1 Lime Easter Egg</a><br /><br />
    <a href='?action=eggcoin5' class='btn btn-primary'>Egg Coin - 1 Blue Easter Egg</a><br /><br />
    <hr />
    <a href='?action=buymaster' class='btn btn-primary'>Master Egg - 25 Egg Coins</a><br /><br />
    <a href='?action=buygold' class='btn btn-primary'>Golden Egg - 5 Egg Coins</a><br /><br />
    <a href='?action=buytokens' class='btn btn-primary'>500 Chivalry Tokens - 10 Egg Coins</a><br /><br />
    <a href='?action=buycopper' class='btn btn-primary'>500,000 Copper Coins - 10 Egg Coins</a><br /><br />
    <a href='?action=buybomb' class='btn btn-primary'>Rickity Bomb - 3 Golden Eggs</a><br /><br />
    <a href='?action=buyxp' class='btn btn-primary'>Tome of Experience - 15 Golden Eggs</a><br /><br />";
}
function egg_to_currency($itemid)
{
    global $db,$api,$userid,$ir,$h;
    if (!$api->UserHasItem($userid,$itemid,1))
    {
        alert('danger',"Uh Oh!","You do not have enough {$api->SystemItemIDtoName($itemid)} to make into Egg Coins.",true,'?action=shop');
        die($h->endpage());
    }
    else
    {
        $api->UserTakeItem($userid,$itemid,1);
        $api->UserGiveItem($userid,150,1);
        alert('success',"Success!","You have exchanged one {$api->SystemItemIDtoName($itemid)} for 1 Egg Coin.",true,'?action=shop');
    }
}
function buymaster()
{
    global $ir,$api,$h,$db,$userid,$colored;
    if ($api->UserCountItem($userid,150) < 25)
    {
        alert('danger',"Uh Oh!","You do not have enough Egg Coins to buy a Master Egg.",true,'?action=shop');
        die($h->endpage());
    }
    else
    {
        removeColoredEggs(25);
        $api->UserGiveItem($userid,146,1);
        alert('success',"Success!","You have successfully traded 25 Colored Easter Eggs for a Master Easter Egg.",true,'?action=shop');
    }
}
function buygold()
{
    global $ir,$api,$h,$db,$userid,$colored;
    if ($api->UserCountItem($userid,150) < 5)
    {
        alert('danger',"Uh Oh!","You do not have enough Egg Coins to buy a Golden Egg.",true,'?action=shop');
        die($h->endpage());
    }
    else
    {
        $api->UserTakeItem($userid,150,5);
        $api->UserGiveItem($userid,145,1);
        alert('success',"Success!","You have successfully traded 5 Colored Easter Eggs for a Golden Easter Egg.",true,'?action=shop');
    }
}
function buytokens()
{
    global $ir,$api,$h,$db,$userid,$colored;
    if ($api->UserCountItem($userid,150) < 10)
    {
        alert('danger',"Uh Oh!","You do not have enough Egg Coins to buy 500 Chivalry Tokens.",true,'?action=shop');
        die($h->endpage());
    }
    else
    {
        $api->UserTakeItem($userid,150,10);
        $api->UserGiveCurrency($userid,'secondary',500);
        alert('success',"Success!","You have successfully traded 10 Colored Easter Eggs for 500 Chivalry Tokens.",true,'?action=shop');
    }
}
function buycopper()
{
    global $ir,$api,$h,$db,$userid,$colored;
    if ($api->UserCountItem($userid,150) < 10)
    {
        alert('danger',"Uh Oh!","You do not have enough Egg Coins to buy 500,000 Copper Coins.",true,'?action=shop');
        die($h->endpage());
    }
    else
    {
        $api->UserTakeItem($userid,150,10);
        $db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + 500000 WHERE `userid` = {$userid}");
        alert('success',"Success!","You have successfully traded 10 Colored Easter Eggs for 500,000 Copper Coins.",true,'?action=shop');
    }
}
function buybomb()
{
    global $ir,$api,$h,$db,$userid,$colored;
    if ($api->UserCountItem($userid,145) < 3)
    {
        alert('danger',"Uh Oh!","You do not have enough golden eggs to buy a Rickity Bomb.",true,'?action=shop');
        die($h->endpage());
    }
    else
    {
        $api->UserTakeItem($userid,145,3);
        $api->UserGiveItem($userid,149,1);
        alert('success',"Success!","You have successfully traded 3 Golden Easter Eggs for a Rickity Bomb.",true,'?action=shop');
    }
}
function buyxp()
{
    global $ir,$api,$h,$db,$userid,$colored;
    if ($api->UserCountItem($userid,145) < 15)
    {
        alert('danger',"Uh Oh!","You do not have enough golden eggs to buy a Tome of Knowledge.",true,'?action=shop');
        die($h->endpage());
    }
    else
    {
        $api->UserTakeItem($userid,145,15);
        $api->UserGiveItem($userid,148,1);
        alert('success',"Success!","You have successfully traded 15 Golden Easter Eggs for a Tome of Knowledge.",true,'?action=shop');
    }
}
$h->endpage();