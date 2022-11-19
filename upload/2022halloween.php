<?php
/*	File:		2022halloween.php
	Created: 	Oct 22, 2022; 7:59:05 PM
	Info: 		
	Author:		Ryan
	Website: 	https://chivalryisdeadgame.com/
*/
require('globals.php');						//uncomment if user needs to be auth'd.
if (!isset($_GET['action'])) 
{
    $_GET['action'] = '';
}
switch ($_GET['action']) 
{
    case "ticket":
        ticket();
        break;
    case "pumpkin":
        pumpkin();
        break;
    case "event":
        event();
        break;
    default:
        alert('danger',"Uh Oh!","Please specify a valid action.",true,'inventory.php');
        $h->endpage();
        break;
}
function ticket()
{
    global $h,$db,$api,$userid,$ir;
    $itemToHave = 449;
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
        echo "Select the spot you wish to scratch off. You shall receive rewards.<br />
		<div class='row'>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='./assets/img/pumpkin-halloween.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='./assets/img/pumpkin-halloween.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='./assets/img/pumpkin-halloween.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='./assets/img/pumpkin-halloween.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='./assets/img/pumpkin-halloween.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='./assets/img/pumpkin-halloween.png' class='img-fluid'></a>
			</div>
		</div>";
    }
    die($h->endpage());
}

function event()
{
    global $db, $api, $userid, $h, $ir;
    $hasBeenRewarded = getCurrentUserPref('2022halloweenchuckreward', false);
    echo "<div class='card'>
            <div class='card-header'>
                2022 Annual Pumpkin Chuck
            </div>
            <div class='card-body'>";
    $q=$db->query("SELECT * FROM `2018_halloween_chuck` WHERE `userid` = {$userid}");
    if ($db->num_rows($q) == 0)
        $db->query("INSERT INTO `2018_halloween_chuck` (`userid`, `distance`, `count`) VALUES ('{$userid}', '0', '10')");
    
    $r = $db->fetch_row($db->query("SELECT * FROM `2018_halloween_chuck` WHERE `userid` = {$userid}"));
    if (isset($_GET['throw']))
    {
        $distance=Random(100,10000);
        if (date('n') != 10)
        {
            alert('danger',"Uh Oh!","You may no longer participate in the Pumpkin Chuck after Halloween...",true,'?action=event');
            die($h->endpage());
        }
        if (!$api->UserHasItem($userid,64,1))
        {
            alert('danger',"Uh Oh!","You need a pumpkin to even throw one. You may buy some from the shop in Holiday Isle.",true,'?action=event');
            die($h->endpage());
        }
        if ($r['count'] == 0)
        {
            alert('danger',"Uh Oh!","You've exhausted all your Pumpkin Chuck attempts at this time. Come back at the top of the hour to gain an additional 2 tosses.",true,'?action=event');
            die($h->endpage());
        }
        if ($distance > $r['distance'])
        {
            $db->query("UPDATE `2018_halloween_chuck` SET `distance` = {$distance} WHERE `userid` = {$userid}");
        }
        $db->query("UPDATE `2018_halloween_chuck` SET `count` = `count` - 1 WHERE `userid` = {$userid}");
        alert("success","Success!","You've successfully chucked your pumpkin and achieved a wonderful distance of " . shortNumberParse($distance) . " meters.", true, '?action=event');
        $api->UserTakeItem($userid,64,1);
        if (!$hasBeenRewarded)
        {
            $api->GameAddNotification($userid, "Hey {$ir['username']}, we've given you a 2022 Halloween Chuck Participation Badge to your inventory.");
            setCurrentUserPref('2022halloweenchuckreward', true);
            $api->UserGiveItem($userid, 451, 1);
        }
    }
    else 
    {
        $class= '';
        if (date('n') != 10)
            $class = 'disabled';
        $hq=$db->query("SELECT * FROM `2018_halloween_chuck` WHERE `distance` > 0 ORDER BY `distance` desc LIMIT 5");
        echo "Welcome to the Pumpkin Chuck, {$ir['username']}. You may chuck your pumpkin to see how far it'll go. The player 
        who throws the furthest will receive a unique prize. The two runner-ups will receive a small little prize as well! 
        Remember, your only your best throw will be put into the scorebook. Warriors begin with ten Tosses available to them at 
        the start of the event, with every hour gaining an additional two, to the maximum stored of twenty.
        <br />You may toss another {$r['count']} Pumpkins at this time. Your maximum throw distance is currently 
        " . shortNumberParse($r['distance']) . " meters.<br />
	   <br />
	   <b><u>Top 5 Scores</u></b>
        <div class='row'>";
        $place = 0;
        if ($db->num_rows($hq) == 0)
        {
            echo "<div class='col-12 text-danger font-italic'>There's currently no Pumpkin Chuck highscores at this time.</div>";
        }
        while ($r2=$db->fetch_row($hq))
        {
            $un = parseUsername($r2['userid']);
            $place++;
            echo "      <div class='col-12 col-sm-6 col-lg-4 col-xxl'>
                        <div class='col-12'>
                            {$place}) <a href='profile.php?user={$r2['userid']}'>{$un}</a> [{$r2['userid']}]
                        </div>
                        <div class='col-12 text-muted small'>
                            " . shortNumberParse($r2['distance']) . " Meters
                        </div>
                        </div>";
        }
        echo "</div>
	   <div class='row'>
            <div class='col-12 col-sm'>
                <a class='btn btn-primary btn-block {$class}' href='?action=event&throw=1'>Chuck Pumpkin</a>
            </div>
            <div class='col-12 col-sm'>
                <a class='btn btn-danger btn-block' href='explore.php'>Explore</a>
            </div>
       </div>";
    }
    $h->endpage();
}
function pumpkin()
{
    global $db, $api, $userid, $h, $ir, $set;
    $pumpCost = round(8 * levelMultiplier($ir['level'], $ir['reset']));
    if (isset($_POST['buy']))
    {
        $tobuy = (isset($_POST['buy']) && is_numeric($_POST['buy'])) ? abs($_POST['buy']) : 0;
        if (empty($tobuy)) 
        {
            alert('danger', "Uh Oh!", "Please fill out the form completely before submitting, otherwise you can't purchase 
                    any pumpkins...");
            die($h->endpage());
        }
        $costs = $pumpCost * $tobuy;
        if (!$api->UserHasCurrency($userid, 'secondary', $costs)) 
        {
            alert('danger', "Uh Oh!", "You need " . shortNumberParse($costs) . " Chivalry Tokens to buy 
                    " . shortNumberParse($tobuy) . " Pumpkins. You only have " . shortNumberParse($ir['secondary_currency']) . " 
                    Chivalry Tokens.");
            die($h->endpage());
        }
        
        addToEconomyLog('Pumpkin Patch', 'token', $costs * -1);
        $api->UserTakeCurrency($userid, 'secondary', $costs);
        $api->UserGiveItem($userid, 64, $tobuy);
        
        alert('success',"Success!", "You've successfully purchased " . shortNumberParse($tobuy) . " Pumpkins for " . shortNumberParse($costs) . " 
                    Chivalry Tokens. Check your inventory!", true, 'explore.php');
    }
    else
    {
        echo "<div class='card'>
            <div class='card-header'>
                2022 Pumpkin Patch
            </div>
            <div class='card-body'>
                <div class='row'>
                    <div class='col'>
                        Its honest work and it pays nicely. You may purchase Pumpkins straight from the {$set['WebsiteName']} Pumpkin 
                        Patches for {$pumpCost} Chivalry Tokens each.
                    </div>
                </div>
                <form method='post'>
                <div class='row'>
                    <div class='col'>
                        <div class='row'>
                            <div class='col-12 small'>
                                Purchase Qty
                            </div>
                            <div class='col-12'>
                                <input type='number' value='1' required='1' name='buy' class='form-control'>
                            </div>
                        </div>
                    </div>
                    <div class='col'>
                        <div class='row'>
                            <div class='col-12 small'>
                                 &nbsp;
                            </div>
                            <div class='col-12'>
                                <input type='submit' value='Purchase' class='btn btn-primary btn-block'>
                            </div>
                        </div>
                    </div>
                    <div class='col'>
                        <div class='row'>
                            <div class='col-12 small'>
                                 &nbsp;
                            </div>
                            <div class='col-12'>
                                <a href='explore.php' class='btn btn-danger btn-block'>Go Back</a>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>";
    }
    $h->endpage();
}