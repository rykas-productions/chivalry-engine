<?php
/*
	File:		slots.php
	Created: 	4/5/2016 at 12:26AM Eastern Time
	Info: 		Allows players to play slots for a chance at getting
				more Copper Coins.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require_once('globals.php');
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
	alert('danger',"Uh Oh!","You cannot use the slots while in the infirmary or dungeon.",true,'index.php');
	die($h->endpage());
}
$tresder = (Random(100, 999));
$maxbet = calculateUserMaxBet($userid);
$_GET['tresde'] = (isset($_GET['tresde']) && is_numeric($_GET['tresde'])) ? abs($_GET['tresde']) : 0;
if (!isset($_SESSION['tresde'])) {
    $_SESSION['tresde'] = 0;
}
if ($ir['winnings_this_hour'] >= (($maxbet*15)*20))
{
	alert('danger', "Uh Oh!", "The casino's run out of cash to give you. Come back in an hour.", true, "explore.php");
    die($h->endpage());
}
if (($_SESSION['tresde'] == $_GET['tresde']) || $_GET['tresde'] < 100) {
    alert('danger', "Uh Oh!", "Please do not refresh while playing slots.", true, "?tresde={$tresder}");
    die($h->endpage());
}
$_SESSION['tresde'] = $_GET['tresde'];
echo "<div class='row'>";
if (isset($_POST['bet']) && is_numeric($_POST['bet'])) {
    $_POST['bet'] = abs($_POST['bet']);
    if ($_POST['bet'] > $ir['primary_currency'])
    {
        alert('danger', "Uh Oh!", "You may not bet " . shortNumberParse($_POST['bet']) . " Copper Coins as 
                you only have " . shortNumberParse($ir['primary_currency']) . " Copper Coins on you at
            this time.", true, "?tresde={$tresder}");
        die($h->endpage());
    }
    else if ($_POST['bet'] > $maxbet) 
    {
        alert('danger', "Uh Oh!", "You are restricted to a maximum bet of " . shortNumberParse($maxbet) . " Copper
        Coins. You may not bet " . shortNumberParse($_POST['bet']) . " Copper Coins at this time.", true,
            "?tresde={$tresder}");
        die($h->endpage());
    } 
    else if ($_POST['bet'] < 0) 
    {
        alert('danger', "Uh Oh!", "You must bet at least 1 Copper Coin.", true, "?tresde={$tresder}");
        die($h->endpage());
    }
    $slot = array();
	$slot[1] = Random(0, 9);
	$slot[2] = Random(0, 9);
	$slot[3] = Random(0, 9);
	$gain = 0;
	$phrase = "You insert your bet of " . shortNumberParse($_POST['bet']) . " Copper Coins and pull the lever. Round and round the slots go. ";
    if ($slot[1] == $slot[2] && $slot[2] == $slot[3]) 
    {
        $gain = $_POST['bet'] * 25;
        $phrase .= "All three line up. Jackpot! You win an extra " . shortNumberParse($gain) . " Copper Coins!";
    } 
    else if ($slot[1] == $slot[2] || $slot[2] == $slot[3] || $slot[1] == $slot[3]) 
    {
        $gain = $_POST['bet'] * 12.5;
        $phrase .= "Two slots line up. Awesome! You win an extra " . shortNumberParse($gain) . " Copper Coins!";
    } 
    else 
    {
        $gain = -$_POST['bet'];
        $phrase .= "How unlucky, none of them line up! You've lost your bet of " . shortNumberParse($_POST['bet']) . " Copper Coins.";
		
    }
    echo "
        <div class='col-12'>
            <div class='card'>
                <div class='card-header'>
                    Slots Display
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-4'>
                            <input type='number' value='{$slot[1]}' class='form-control' disabled='1'>
                        </div>
                        <div class='col-12 col-sm-4'>
                            <input type='number' value='{$slot[2]}' class='form-control' disabled='1'>
                        </div>
                        <div class='col-12 col-sm-4'>
                            <input type='number' value='{$slot[3]}' class='form-control' disabled='1'>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-12'><br />";
                            alert('info',"",$phrase,false);
                            echo"
                        </div>
                    </div>
                </div>
            </div>
            <br />
        </div>";
    addToEconomyLog('Gambling', 'copper', $gain);
    $db->query("UPDATE `user_settings` SET `winnings_this_hour` = `winnings_this_hour` + ({$gain}) WHERE `userid` = {$userid}");
    $db->query("UPDATE `settings` SET `setting_value` = `setting_value` + ({$gain}) WHERE `setting_name` = 'casino_give'");
    $db->query("UPDATE `settings` SET `setting_value` = `setting_value` + {$_POST['bet']} WHERE `setting_name` = 'casino_take'");
    $db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + ({$gain}) WHERE `userid` = {$userid}");
    $api->SystemLogsAdd($userid, 'gambling', "Bet {$_POST['bet']} and won {$gain} in slots.");
    $tresder = Random(100, 999);
}
    echo "
        <div class='col-12'>
            <form action='?tresde={$tresder}' method='post'>
            <div class='card'>
                <div class='card-header'>
                    Slots
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12'>
                            You may place Copper Coins inside the slot machines and pull the lever. You may get rich quickly. Note, 
                            the maximum you may bet at this time is " . shortNumberParse($maxbet) . " Copper Coins.
                        </div>
                        <div class='col-12 col-sm-6'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Bet</b></small>
                                </div>
                                <div class='col-12'>
                                    <input type='number' class='form-control' name='bet' min='1' max='{$maxbet}' value='{$maxbet}' />
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><br /></small>
                                </div>
                                <div class='col-12'>
                                    <input class='btn btn-primary btn-block' type='submit' value='Place Bet' />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>";
$h->endpage();