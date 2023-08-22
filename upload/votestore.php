<?php
require('globals.php');
$vote = array();
$vote["baseHexbags"] = 50;
$vote['baseBOR'] = 500;
$vote['baseCopper'] = 1000000;
$vote['baseTokens'] = 500;
$vote['basePower'] = 10;
$vote['singelItemMulti'] = round(1 * levelMultiplier($ir['level'], $ir['reset']));
alert('info',"","You may exchange your Vote Points for in-game rewards. Thank you for voting!",false);
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "buy":
        buy();
        break;
    default:
        home();
        break;
}
function buy()
{
    global $db,$userid,$api,$h,$ir,$vote;
    //$db->query("UPDATE `users` SET `vote_points` = 125 WHERE `userid` = {$userid}");
    $_GET['option'] = abs($_GET['option']);
    if (empty($_GET['option']))
    {
        alert("danger","Uh Oh!","You have chosen to buy a non-existent listing.",true,'votestore.php');
        die($h->endpage());
    }
    if (($_GET['option'] < 0) || ($_GET['option'] > 16))
    {
        alert("danger","Uh Oh!","You have chosen to buy a non-existent listing.",true,'votestore.php');
        die($h->endpage());
    }
	if ($_GET['option'] == 15)
    {
        alert("danger","Uh Oh!","You have chosen to buy a non-existent listing.",true,'votestore.php');
        die($h->endpage());
    }
    $cost=1;
    $cost1=array(2,3,4,1);
    $cost3=array(9);
    $cost5=array(5,11,12);
	$cost10=array(16,13);
	$cost15=array(14,10);
    $cost25=array();
    $cost50=array(6);
    $cost150=array(7,8);
    if (in_array($_GET['option'],$cost3))
        $cost=3;
    if (in_array($_GET['option'],$cost5))
        $cost=5;
	if (in_array($_GET['option'],$cost10))
        $cost=10;
    if (in_array($_GET['option'],$cost25))
        $cost=25;
    if (in_array($_GET['option'],$cost50))
        $cost=50;
    if (in_array($_GET['option'],$cost150))
        $cost=150;
	if (in_array($_GET['option'],$cost15))
        $cost=15;
    if ($ir['vote_points'] < $cost)
    {
        alert("danger","Uh Oh!","You do not have the {$cost} Vote Points required to buy this offer. You currently only have {$ir['vote_points']} Vote Points.",true,'votestore.php');
        die($h->endpage());
    }
    $rewardTxt = "No reward text...";
    $db->query("UPDATE `users` SET `vote_points` = `vote_points` - {$cost} WHERE `userid` = {$userid}");
    //100 Chivalry Tokens
    if ($_GET['option'] == 1)
    {
        $tokens = round($vote['baseTokens'] * levelMultiplier($ir['level'], $ir['reset']));
        $db->query("UPDATE `users` SET `secondary_currency` = `secondary_currency` + 500 WHERE `userid` = {$userid}");
        addToEconomyLog('Vote Rewards', 'token', $tokens);
        $rewardTxt = "You've been credited " . shortNumberParse($tokens) . " Chivalry Tokens.";
    }
    //50 Boxes of Random
    if ($_GET['option'] == 2)
    {
        $bor = round($vote['baseBOR'] * levelMultiplier($ir['level'], $ir['reset']));
        $api->UserGiveItem($userid,33,$bor);
        $rewardTxt = "You've been credited " . shortNumberParse($bor) . " {$api->SystemItemIDtoName(33)}(s).";
    }
    //250,000 Copper Coins
    if ($_GET['option'] == 3)
    {
        $copper = round($vote['baseCopper'] * levelMultiplier($ir['level'], $ir['reset']));
        $db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + 250000 WHERE `userid` = {$userid}");
		addToEconomyLog('Vote Rewards', 'copper', $copper);
		$rewardTxt = "You've been credited " . shortNumberParse($copper) . " Copper Coins.";
    }
    //25 Hexbags
    if ($_GET['option'] == 4)
    {
        $hex = round($vote['baseHexbags'] * levelMultiplier($ir['level'], $ir['reset']));
        if (($ir['hexbags'] + $hex) > PHP_INT_MAX)
        {
            alert("danger","Uh Oh!","You can only have a maximum of " . shortNumberParse(PHP_INT_MAX) . " Hexbags at a time. Use the ones you have and come back and buy more.",true,'votestore.php');
            die($h->endpage());
        }
        $db->query("UPDATE `users` SET `hexbags` = `hexbags` + {$hex} WHERE `userid` = {$userid}");
        $rewardTxt = "You've been credited " . shortNumberParse($hex) . " Hexbags.";
    }
    //CID Admin Gym Access Scroll
    if ($_GET['option'] == 5)
    {
        $api->UserGiveItem($userid,205,$vote['singelItemMulti']);
        $rewardTxt = "You've been credited " . shortNumberParse($vote['singelItemMulti']) . " x {$api->SystemItemIDtoName(205)}(s) to your inventory.";
    }
    //$1 VIP Pack
    //@todo do not add this to item multipler
    if ($_GET['option'] == 6)
    {
        $api->UserGiveItem($userid,12,1);
        $rewardTxt = "You've been credited a {$api->SystemItemIDtoName(12)} to your inventory.";
    }
    //Auto BOR Opener
    //@todo do not add this to item multipler
    if ($_GET['option'] == 7)
    {
        $api->UserGiveItem($userid,92,1);
        $rewardTxt = "You've been credited a {$api->SystemItemIDtoName(92)} to your inventory.";
    }
    //Auto Hexbag Opener
    //@todo do not add this to item multipler
    if ($_GET['option'] == 8)
    {
        $api->UserGiveItem($userid,91,1);
        $rewardTxt = "You've been credited a {$api->SystemItemIDtoName(91)} to your inventory.";
    }
    //Chivalry Gym Scroll
    if ($_GET['option'] == 9)
    {
        $api->UserGiveItem($userid,18,$vote['singelItemMulti']);
        $rewardTxt = "You've been credited " . shortNumberParse($vote['singelItemMulti']) . " x {$api->SystemItemIDtoName(18)}(s) to your inventory.";
    }
    //Mining Power
    if ($_GET['option'] == 10)
    {
        $power = round($vote['basePower'] * levelMultiplier($ir['level'], $ir['reset']));
        $db->query("UPDATE `mining` SET `max_miningpower` = `max_miningpower` + '{$power}' WHERE `userid` = {$userid}");
        $rewardTxt = "You've been credited " . shortNumberParse($power) . " maximum mining power.";
    }
    //Voting Badge
    if ($_GET['option'] == 11)
    {
        $api->UserGiveItem($userid,209,1);
        $rewardTxt = "You've been credited a {$api->SystemItemIDtoName(209)} to your inventory.";
    }
	//Book Key
    if ($_GET['option'] == 12)
    {
        $api->UserGiveItem($userid,250,$vote['singelItemMulti']);
        $rewardTxt = "You've been credited " . shortNumberParse($vote['singelItemMulti']) . " x {$api->SystemItemIDtoName(250)}(s) to your inventory.";
    }
	//Book
    if ($_GET['option'] == 13)
    {
        $api->UserGiveItem($userid,249,1);
        $rewardTxt = "You've been credited " . shortNumberParse($vote['singelItemMulti']) . " x {$api->SystemItemIDtoName(249)}(s) to your inventory.";
    }
	//Mining Energy Potion
    if ($_GET['option'] == 14)
    {
        $api->UserGiveItem($userid,227,$vote['singelItemMulti']);
        $rewardTxt = "You've been credited " . shortNumberParse($vote['singelItemMulti']) . " x {$api->SystemItemIDtoName(227)}(s) to your inventory.";
    }
	//Daily Reward Reset
    if ($_GET['option'] == 16)
    {
        $db->query("UPDATE `users` SET `rewarded` = 0 WHERE `userid` = {$userid}");
        $rewardTxt = "You've been credited an extra Daily Reward. Check your notifications!";
    }
    alert("success","Success!","You have successfully traded {$cost} Vote Points for a reward! {$rewardTxt}",true,'votestore.php');
    die($h->endpage());
}
function home()
{
    global $db,$userid,$api,$h,$ir,$vote;
    echo"<div class='row'>
    <div class='col-12 col-xxl-auto col-xxxl-7'>
    <div class='card'>
        <div class='card-header'>
            Vote Points: " . createPrimaryBadge(shortNumberParse($ir['vote_points'])) . "
        </div>
        <div class='card-body'>
            <div class='row'>
                <div class='col-auto col-lg col-sm-6 col-md-5'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Reward</b></small>
                        </div>
                        <div class='col-12'>
                            " . shortNumberParse(round($vote['baseBOR'] * levelMultiplier($ir['level'], $ir['reset']))) . " Boxes of Random
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Cost</b></small>
                        </div>
                        <div class='col-12'>
                            1 Vote Point
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <a href='?action=buy&option=2' class='btn btn-primary'>Purchase</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-auto col-lg col-sm-6 col-md-5'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Reward</b></small>
                        </div>
                        <div class='col-12'>
                            " . shortNumberParse(round($vote['baseCopper'] * levelMultiplier($ir['level'], $ir['reset']))) . " Copper Coins
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Cost</b></small>
                        </div>
                        <div class='col-12'>
                            1 Vote Point
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <a href='?action=buy&option=3' class='btn btn-primary'>Purchase</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-auto col-lg col-sm-6 col-md-5'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Reward</b></small>
                        </div>
                        <div class='col-12'>
                            " . shortNumberParse(round($vote['baseHexbags'] * levelMultiplier($ir['level'], $ir['reset']))) . " Hexbags
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Cost</b></small>
                        </div>
                        <div class='col-12'>
                            1 Vote Point
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <a href='?action=buy&option=4' class='btn btn-primary'>Purchase</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-auto col-lg col-sm-6 col-md-5'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Reward</b></small>
                        </div>
                        <div class='col-12'>
                            " . shortNumberParse(round($vote['baseTokens'] * levelMultiplier($ir['level'], $ir['reset']))) . " Chivalry Tokens
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Cost</b></small>
                        </div>
                        <div class='col-12'>
                            1 Vote Point
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <a href='?action=buy&option=1' class='btn btn-primary'>Purchase</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-auto col-lg col-sm-6 col-md-5'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Reward</b></small>
                        </div>
                        <div class='col-12'>
                            " . shortNumberParse($vote['singelItemMulti']) . " x Chivalry Gym Scroll(s)
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Cost</b></small>
                        </div>
                        <div class='col-12'>
                            3 Vote Points
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <a href='?action=buy&option=9' class='btn btn-primary'>Purchase</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-auto col-lg col-sm-6 col-md-5'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Reward</b></small>
                        </div>
                        <div class='col-12'>
                            " . shortNumberParse($vote['singelItemMulti']) . " x {$api->SystemItemIDtoName(205)}(s)
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Cost</b></small>
                        </div>
                        <div class='col-12'>
                            5 Vote Points
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <a href='?action=buy&option=5' class='btn btn-primary'>Purchase</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-auto col-lg col-sm-6 col-md-5'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Reward</b></small>
                        </div>
                        <div class='col-12'>
                            1 x Voting Badge(s)
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Cost</b></small>
                        </div>
                        <div class='col-12'>
                            5 Vote Points
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <a href='?action=buy&option=11' class='btn btn-primary'>Purchase</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-auto col-lg col-sm-6 col-md-5'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Reward</b></small>
                        </div>
                        <div class='col-12'>
                            " . shortNumberParse($vote['singelItemMulti']) . " x Locked Spell Book Key
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Cost</b></small>
                        </div>
                        <div class='col-12'>
                            5 Vote Points
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <a href='?action=buy&option=12' class='btn btn-primary'>Purchase</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-auto col-lg col-sm-6 col-md-5'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Reward</b></small>
                        </div>
                        <div class='col-12'>
                            " . shortNumberParse($vote['singelItemMulti']) . " x Locked Spell Book
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Cost</b></small>
                        </div>
                        <div class='col-12'>
                            10 Vote Points
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <a href='?action=buy&option=13' class='btn btn-primary'>Purchase</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-auto col-lg col-sm-6 col-md-5'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Reward</b></small>
                        </div>
                        <div class='col-12'>
                            Extra Daily Reward
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Cost</b></small>
                        </div>
                        <div class='col-12'>
                            10 Vote Points
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <a href='?action=buy&option=16' class='btn btn-primary'>Purchase</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-auto col-lg col-sm-6 col-md-5'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Reward</b></small>
                        </div>
                        <div class='col-12'>
                            " . shortNumberParse($vote['singelItemMulti']) . " x Mining Energy Potion
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Cost</b></small>
                        </div>
                        <div class='col-12'>
                            15 Vote Points
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <a href='?action=buy&option=14' class='btn btn-primary'>Purchase</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-auto col-lg col-sm-6 col-md-5'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Reward</b></small>
                        </div>
                        <div class='col-12'>
                            " . shortNumberParse(round($vote['basePower'] * levelMultiplier($ir['level'], $ir['reset']))) . " x Max Mining Power
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Cost</b></small>
                        </div>
                        <div class='col-12'>
                            15 Vote Points
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <a href='?action=buy&option=10' class='btn btn-primary'>Purchase</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-auto col-lg col-sm-6 col-md-5'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Reward</b></small>
                        </div>
                        <div class='col-12'>
                            1 x $1 VIP Pack
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Cost</b></small>
                        </div>
                        <div class='col-12'>
                            50 Vote Points
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <a href='?action=buy&option=6' class='btn btn-primary'>Purchase</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-auto col-lg col-sm-6 col-md-5'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Reward</b></small>
                        </div>
                        <div class='col-12'>
                            1 x Auto BOR Opener
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Cost</b></small>
                        </div>
                        <div class='col-12'>
                            150 Vote Points
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <a href='?action=buy&option=7' class='btn btn-primary'>Purchase</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-auto col-lg col-sm-6 col-md-5'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Reward</b></small>
                        </div>
                        <div class='col-12'>
                            1 x Auto Hexbag Opener
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Cost</b></small>
                        </div>
                        <div class='col-12'>
                            150 Vote Points
                        </div>
                    </div>
                </div>
                <div class='col-auto col-sm-6 col-xl-4 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <a href='?action=buy&option=8' class='btn btn-primary'>Purchase</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>";
}
$h->endpage();