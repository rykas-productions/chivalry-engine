<?php
/*	File:		newroulette.php
	Created: 	Feb 4, 2022; 6:22:31 PM
	Info: 		
	Author:		Ryan
	Website: 	https://chivalryisdeadgame.com/
*/
require('globals.php');		//uncomment if user needs to be auth'd.
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
    alert('danger',"Uh Oh!","You cannot use the roulette table while in the infirmary or dungeon.",true,'index.php');
    die($h->endpage());
}
$blacks = array(2,4,6,8,10,11,13,15,17,20,22,24,26,28,29,31,33,35);
$reds = array(1,3,5,7,9,12,14,16,18,19,21,23,25,27,30,32,34,36);
$oneeighteen = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18);
$twoeighteen = array(19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36);
$odds = array(1,3,5,7,9,11,13,15,17,19,21,23,25,27,29,31,33,35);
$evens = array(2,4,6,8,10,12,14,16,18,20,22,24,26,28,30,32,34,36);
$first12 = array(1,2,3,4,5,6,7,8,9,10,11,12);
$second12 = array(13,14,15,16,17,18,19,20,21,22,23,24);
$third12 = array(25,26,27,28,29,30,31,32,33,34,35,36);

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
if (($_SESSION['tresde'] == $_GET['tresde']) || $_GET['tresde'] < 100) 
{
    alert('danger', "Uh Oh!", "Please do not refresh while playing roulette, thank you.", true, "roulette.php?tresde={$tresder}");
    die($h->endpage());
}
$_SESSION['tresde'] = $_GET['tresde'];
$currentBetValue = ($ir['primary_currency'] > $maxbet) ? $maxbet : $ir['primary_currency'];
$display = "";
if (isset($_POST['bet']) && is_numeric($_POST['bet']))
{
    $_POST['bet'] = abs($_POST['bet']);
    $_POST['number'] = abs($_POST['number']);
    if (!isset($_POST['number'])) 
    {
        $_POST['number'] = 0;
    }
    //player has enough for bet
    if ($_POST['bet'] > $ir['primary_currency'])
    {
        alert('danger', "Uh Oh!", "You may not bet " . shortNumberParse($_POST['bet']) . " Copper Coins at this 
            time, as you only have " . shortNumberParse($ir['primary_currency']) . " Copper Coins on you at 
            this time.", true, "roulette.php?tresde={$tresder}");
        die($h->endpage());
    }
    //player bets more than their max bet
    else if ($_POST['bet'] > $maxbet) 
    {
        alert('danger', "Uh Oh!", "You are restricted to a maximum bet of " . shortNumberParse($maxbet) . " Copper 
        Coins. You may not bet " . shortNumberParse($_POST['bet']) . " Copper Coins at this time.", true,
            "roulette.php?tresde={$tresder}");
        die($h->endpage());
    }
    //player doesn't bet anything
    else if ($_POST['bet'] <= 0) 
    {
        alert('danger', "Uh Oh!", "You must bet at least 1 Copper Coin.", true, "roulette.php?tresde={$tresder}");
        die($h->endpage());
    }
    //player doesn't input a valid option to bet upon
    elseif ((empty($_POST['number'])) && (empty($_POST['altbet'])))
    {
        alert('danger', "Uh Oh!", "Please input what you are betting on the roulette table.", true, 
            "roulette.php?tresde={$tresder}");
        die($h->endpage());
    }
    elseif ((!empty($_POST['number'])) && (!empty($_POST['altbet'])))
    {
        alert('danger', "Uh Oh!", "Please only bet on a number, or an option from the previous dropdown menu, not both.", 
            true, "roulette.php?tresde={$tresder}");
        die($h->endpage());
    }
    $slot = array();
    $slot[1] = Random(0, 36);
    $gain = 0;
    $phrase = "";
    //straight bet, no bs.
    if (empty($_POST['altbet']))
    {
        $phrase .= "You place a " . shortNumberParse($_POST['bet']) . " Copper Coin bet on {$_POST['number']}.";
        if ($slot[1] == $_POST['number'])
        {
            $gain = $_POST['bet'] * 35;
            $phrase .= " Lucky you! The table stopped on {$slot[1]}! You have gained an extra " . shortNumberParse($gain) . " Copper Coins.";
        }
        else
        {
            $gain = -$_POST['bet'];
            $phrase .= " Unlucky, the roulette table has stopped on {$slot[1]}. You have lost your bet of " . shortNumberParse($_POST['bet']) . " Copper Coins.";
        }
    }
    else
    {
        //player bets on reds
        if ($_POST['altbet'] == 'red')
        {
            $phrase .= "You place a " . shortNumberParse($_POST['bet']) . " Copper Coin bet on reds.";
            if (in_array($slot[1], $reds))
            {
                $gain = $_POST['bet'];
                $phrase .= " Lucky you! The table stopped on {$slot[1]}, a red spot! You have gained an extra " . shortNumberParse($gain) . " Copper Coins.";
            }
            else
            {
                $gain = -$_POST['bet'];
                $phrase .= " Unlucky, the roulette table has stopped on {$slot[1]}, a black spot. You have lost your bet of " . shortNumberParse($_POST['bet']) . " Copper Coins.";
            }
        }
        //player bets on blacks
        elseif ($_POST['altbet'] == 'black')
        {
            $phrase .= "You place a " . shortNumberParse($_POST['bet']) . " Copper Coin bet on blacks.";
            if (in_array($slot[1], $blacks))
            {
                $gain = $_POST['bet'];
                $phrase .= " Lucky you! The table stopped on {$slot[1]}, a black spot! You have gained an extra " . shortNumberParse($gain) . " Copper Coins.";
            }
            else
            {
                $gain = -$_POST['bet'];
                $phrase .= " Unlucky, the roulette table has stopped on {$slot[1]}, a red spot. You have lost your bet of " . shortNumberParse($_POST['bet']) . " Copper Coins.";
            }
        }
        //player bets on evens
        elseif ($_POST['altbet'] == 'even')
        {
            $phrase .= "You place a " . shortNumberParse($_POST['bet']) . " Copper Coin bet on evens.";
            if (in_array($slot[1], $evens))
            {
                $gain = $_POST['bet'];
                $phrase .= " Lucky you! The table stopped on {$slot[1]}, an even number! You have gained an extra " . shortNumberParse($gain) . " Copper Coins.";
            }
            else
            {
                $gain = -$_POST['bet'];
                $phrase .= " Unlucky, the roulette table has stopped on {$slot[1]}, an odd number. You have lost your bet of " . shortNumberParse($_POST['bet']) . " Copper Coins.";
            }
        }
        //player bets on odds
        elseif ($_POST['altbet'] == 'odd')
        {
            $phrase .= "You place a " . shortNumberParse($_POST['bet']) . " Copper Coin bet on odds.";
            if (in_array($slot[1], $odds))
            {
                $gain = $_POST['bet'];
                $phrase .= " Lucky you! The table stopped on {$slot[1]}, an odd number! You have gained an extra " . shortNumberParse($gain) . " Copper Coins.";
            }
            else
            {
                $gain = -$_POST['bet'];
                $phrase .= " Unlucky, the roulette table has stopped on {$slot[1]}, an even number. You have lost your bet of " . shortNumberParse($_POST['bet']) . " Copper Coins.";
            }
        }
        elseif ($_POST['altbet'] == 'tophalf')
        {
            $phrase .= "You place a " . shortNumberParse($_POST['bet']) . " Copper Coin bet on lows.";
            if (in_array($slot[1], $oneeighteen))
            {
                $gain = $_POST['bet'];
                $phrase .= " Lucky you! The table stopped on {$slot[1]}, a low number! You have gained an extra " . shortNumberParse($gain) . " Copper Coins.";
            }
            else
            {
                $gain = -$_POST['bet'];
                $phrase .= " Unlucky, the roulette table has stopped on {$slot[1]}, a high number. You have lost your bet of " . shortNumberParse($_POST['bet']) . " Copper Coins.";
            }
        }
        elseif ($_POST['altbet'] == 'bottomhalf')
        {
            $phrase .= "You place a " . shortNumberParse($_POST['bet']) . " Copper Coin bet on highs.";
            if (in_array($slot[1], $twoeighteen))
            {
                $gain = $_POST['bet'];
                $phrase .= " Lucky you! The table stopped on {$slot[1]}, a high number! You have gained an extra " . shortNumberParse($gain) . " Copper Coins.";
            }
            else
            {
                $gain = -$_POST['bet'];
                $phrase .= " Unlucky, the roulette table has stopped on {$slot[1]}, a low number. You have lost your bet of " . shortNumberParse($_POST['bet']) . " Copper Coins.";
            }
        }
        elseif ($_POST['altbet'] == 'first12')
        {
            $phrase .= "You place a " . shortNumberParse($_POST['bet']) . " Copper Coin bet on first dozen.";
            if (in_array($slot[1], $first12))
            {
                $gain = $_POST['bet'] * 2;
                $phrase .= " Lucky you! The table stopped on {$slot[1]}, a number part of the first dozen! You have gained an extra " . shortNumberParse($gain) . " Copper Coins.";
            }
            else
            {
                $gain = -$_POST['bet'];
                $phrase .= " Unlucky, the roulette table has stopped on {$slot[1]}, a number not part of the first dozen. You have lost your bet of " . shortNumberParse($_POST['bet']) . " Copper Coins.";
            }
        }
        elseif ($_POST['altbet'] == 'second12')
        {
            $phrase .= "You place a " . shortNumberParse($_POST['bet']) . " Copper Coin bet on second dozen.";
            if (in_array($slot[1], $second12))
            {
                $gain = $_POST['bet'] * 2;
                $phrase .= " Lucky you! The table stopped on {$slot[1]}, a number part of the second dozen! You have gained an extra " . shortNumberParse($gain) . " Copper Coins.";
            }
            else
            {
                $gain = -$_POST['bet'];
                $phrase .= " Unlucky, the roulette table has stopped on {$slot[1]}, a number not part of the second dozen. You have lost your bet of " . shortNumberParse($_POST['bet']) . " Copper Coins.";
            }
        }
        elseif ($_POST['altbet'] == 'third12')
        {
            $phrase .= "You place a " . shortNumberParse($_POST['bet']) . " Copper Coin bet on third dozen.";
            if (in_array($slot[1], $third12))
            {
                $gain = $_POST['bet'] * 2;
                $phrase .= " Lucky you! The table stopped on {$slot[1]}, a number part of the third dozen! You have gained an extra " . shortNumberParse($gain) . " Copper Coins.";
            }
            else
            {
                $gain = -$_POST['bet'];
                $phrase .= " Unlucky, the roulette table has stopped on {$slot[1]}, a number not part of the third dozen. You have lost your bet of " . shortNumberParse($_POST['bet']) . " Copper Coins.";
            }
        }
    }
    addToEconomyLog('Gambling', 'copper', $gain);
    $db->query("UPDATE `user_settings` SET `winnings_this_hour` = `winnings_this_hour` - {$_POST['bet']} WHERE `userid` = {$userid}");
    $api->SystemLogsAdd($userid, 'gambling', "Bet " . shortNumberParse($_POST['bet']) . " in roulette. (Gained " . shortNumberParse($gain) . " Copper Coins)");
    $db->query("UPDATE `settings` SET `setting_value` = `setting_value` + {$_POST['bet']} WHERE `setting_name` = 'casino_take'");
    $db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + ({$gain}) WHERE `userid` = {$userid}");
    $tresder = Random(100, 999);
    $display = alert('info',"",$phrase,false);
}
echo "<form method='post' action='?tresde={$tresder}'>
<div class='row'>
    <div class='col-12'>
        <div class='card'>
            <div class='card-header'>
                Roulette Table
            </div>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-12'>
                        {$display}
                    </div>
                    <div class='col-12'>
                        Welcome to the roulette table, {$ir['username']}. Your bets may only be, at maximum, " . shortNumberParse($maxbet) . " Copper Coins.
                    </div>
                </div>
                <div class='row'>
                    <div class='col-12 col-md-4 col-xxl-3'>
                        <div class='row'>
                            <div class='col-12'>
                                <b>Current Bet</b>
                            </div>
                            <div class='col-12'>
                                    <input type='number' class='form-control' name='bet' min='1' max='{$maxbet}' value='{$currentBetValue}' />
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm-6 col-md-4 col-xxl-3'>
                        <div class='row'>
                            <div class='col-12'>
                                <b>Betting On #</b>
                            </div>
                            <div class='col-12'>
                                <input type='number' class='form-control' name='number' min='0' max='36' placeholder='18' />
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm-6 col-md-4 col-xxl-3'>
                        <div class='row'>
                            <div class='col-12'>
                                <b>Alternative Bet</b>
                            </div>
                            <div class='col-12'>
                                <select name='altbet' class='form-control' type='dropdown'>
                					<option value=''>None</option>
                					<option value='black'>Blacks</option>
                					<option value='red'>Reds</option>
                					<option value='even'>Evens</option>
                                    <option value='odd'>Odds</option>
                                    <option value='tophalf'>Low (1-18)</option>
                                    <option value='bottomhalf'>High (19-36)</option>
                                    <option value='first12'>First Dozen (1-12)</option>
                                    <option value='second12'>Second Dozen (13-24)</option>
                                    <option value='third12'>Third Dozen (25-36)</option>
                				</select>
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-xxl-3'>
                        <div class='row'>
                            <div class='col-12'>
                                 <br /><input type='submit' value='Play Roulette' class='btn btn-primary btn-block'>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
<div class='row'>
    <div class='col-12 col-md-8 col-xl-6'>
        <br />
        <div class='card'>
            <div class='card-header'>
                Roulette Table
            </div>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-12'>
                        <img src='./assets/img/menu/roulette/roulette-table.jpg' class='img-thumbnail img-responsive'>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class='col-12 col-md-4 col-xl-6'>
        <br />
        <div class='card'>
            <div class='card-header'>
                Roulette Payout
            </div>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-12 col-xl-6 col-xxl-4 col-xxxl-3'>
                        Singles - 35:1
                    </div>
                    <div class='col-12 col-xl-6 col-xxl-4 col-xxxl-3'>
                        Dozens - 2:1
                    </div>
                    <div class='col-12 col-xl-6 col-xxl-4 col-xxxl-3'>
                        Reds / Blacks - 1:1
                    </div>
                    <div class='col-12 col-xl-6 col-xxl-4 col-xxxl-3'>
                        Odds / Evens - 1:1
                    </div>
                    <div class='col-12 col-xl-6 col-xxl-4 col-xxxl-3'>
                        Highs / Lows - 1:1
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>";
$h->endpage();