<?php
/*
	File:		hilow.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		An in-game minigame allowing players to guess if the next 
				number shown will be less or greater than the currently displayed 
				one. Allows players to bet Primary Currency.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
	MIT License

	Copyright (c) 2019 TheMasterGeneral

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
*/
//@todo functionality check before placed in game
require("globals.php");
//randomNumber number generator for anti-refreshing.
$tresder = (Random(100, 999));
//Assign max bet to var
$maxbet = calculateUserMaxBet($userid);
$tresde = filter_input(INPUT_GET, 'tresde', FILTER_SANITIZE_NUMBER_INT) ?: 0;
//Anti-refresh bound isn't bound to SESSION, so bind 0 to it.
if (!isset($_SESSION['tresde'])) {
    $_SESSION['tresde'] = 0;
}
//User has less primary currency than their maximum bet.
if ($ir['primary_currency'] < $maxbet) 
{
    alert('danger', "Uh Oh!", "You do not have enough Copper Coins to place your bet. You only have " . shortNumberParse($ir['primary_currency']) . " Copper Coins.", true, 'explore.php');
    $_SESSION['number'] = 0;
    die($h->endpage());
}
//The RNG received from GET does not equal RNG in SESSION, or is less than 100
if (($_SESSION['tresde'] == $tresde) || $tresde < 100) {
    alert('danger', "Uh Oh!", "Do not refresh while playing High/Low.", true, "?tresde={$tresder}");
    $_SESSION['number'] = 0;
    die($h->endpage());
}
//Bind RNG from GET to SESSION
$_SESSION['tresde'] = $tresde;
echo "<h3>High/Low</h3><hr />";
if (isset($_POST['change']) && in_array($_POST['change'], array('higher', 'lower'))) 
{
    //Player did not select a number.
    if (!isset($_SESSION['number'])) 
    {
        alert('danger', "Uh Oh!", "Your last number wasn't saved in session... weird.", true, "?tresde={$tresder}");
        die($h->endpage());
    } 
    else 
    {
        //Bind guessed number from SESSION into a variable.
        $guessed = (isset($_SESSION['number']) && is_numeric($_SESSION['number'])) ? abs($_SESSION['number']) : Random(1, 100);
        $numb = Random(1, 100);
        //Take the player's bet.
        $api->UserTakeCurrency($userid, 'primary', $maxbet);
        
        //Change is suspected to be higher, but new number is lower than original number.
        if ($guessed > $numb && $_POST['change'] == 'higher') 
        {
            alert('danger', "Uh Oh!", "You guessed the game operator would show a number higher than {$guessed}.
			    The number revealed is {$numb}. You have lost this bet and lost your bet of " . shortNumberParse($maxbet) . " Copper Coins.", false);
            $gain = 0;
            $api->SystemLogsAdd($userid, 'gambling', "Bet higher number in High/Low and lost " . shortNumberParse($maxbet) . " Copper Coins.");
        } 
        //Change is suspected to be higher, and user is correct.
        elseif ($guessed < $numb && $_POST['change'] == 'higher') 
        {
            $gain = $maxbet * 1.05;
            
            alert('success', "Success!", "You guessed the game operator would show a number higher than {$guessed}.
			    The number revealed is {$numb}. You won this bet!", false);
            $api->SystemLogsAdd($userid, 'gambling', "Bet higher number in High/Low and won " . shortNumberParse($gain) . " Copper Coins.");
        } 
        //Change is suspected to be lower, and user is correct.
        elseif ($guessed > $numb && $_POST['change'] == 'lower') 
        {
            alert('success', "Success!", "You guessed the game operator would show a number lower than {$guessed}.
			    The number revealed is {$numb}. You have won this bet!", false);
            $gain = $maxbet * 1.05;
            $api->SystemLogsAdd($userid, 'gambling', "Bet lower number in High/Low and won " . shortNumberParse($gain) . " Copper Coins.");
        } 
        //Change is suspected to be lower, but the new number is higher than the original.
        elseif ($guessed < $numb && $_POST['change'] == 'lower') 
        {
            alert('danger', "Uh Oh!", "You guessed the game operator would show a number less than {$guessed}. The number
			    revealed is {$numb}. You lose this round and lost your bet of " . shortNumberParse($maxbet) . " Copper Coins.", false);
            $gain = 0;
            $api->SystemLogsAdd($userid, 'gambling', "Bet lower number in High/Low and lost " . shortNumberParse($maxbet) . " Copper Coins.");
        } //The new number is the same as the old number.
        else 
        {
            alert('success', "Success!", "The number drawn is the same nubmer as last time. You've been refunded your bet of " . shortNumberParse($maxbet) . " Copper Coins.", false);
            $gain = $maxbet;
            $api->SystemLogsAdd($userid, 'gambling', "Number tied in high/low. Bet of " . shortNumberParse($maxbet) . " Copper Coins refunded.");
        }
        
        //Give the user their winnings, if possible.
        $api->UserGiveCurrency($userid, 'primary', $gain);
        
        //Bind 0 to SESSION to not have abuse.
        $_SESSION['number'] = 0;
    }
} else {
    //Generate starting number and bind it to SESSION.
    $numb = Random(1, 100);
    $_SESSION['number'] = $numb;
    //@todo grid breakpoints and card
    echo "
        <div class='card'>
            <div class='card-header'>
                High/Low
            </div>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-12'>
                        Place a bet of " . shortNumberParse($maxbet) . " Copper Coins on whether the next drawn 
                        number will be higher or lower than the currrently displayed one. Number range is limited 
                        to 1 - 100. If you're correct, you will win 5% of your bet as bonus. Wrong, you lose it all!
                        On the off chance the numbers are the same, you'll just be refunded your bet.
                    </div>
                </div>
            </div>
        </div>
Welcome to High/Low. Here you will place a bet whether or not the next drawn number will be higher or lower
        than the currently drawn number. The number range is 1 through 100.<br />
	<table class='table table-bordered'>
		<tr>
			<th colspan='2'>
				The operator displays {$numb}. Do you believe the next number will be higher or lower? 
                Numbers will only be drawn 1 - 100. Select your choice. Bets are limited to 
                " . shortNumberParse($maxbet) . " Copper Coins exactly.
			</th>
		</tr>
		<tr>
			<td>
				<form action='?tresde={$tresder}' method='post'>
					<input type='hidden' name='change' value='lower'>
					<input type='submit' value='Lower' class='btn btn-primary'>
				</form>
			</td>
			<td>
				<form action='?tresde={$tresder}' method='post'>
					<input type='hidden' name='change' value='higher'>
					<input type='submit' value='Higher' class='btn btn-primary'>
				</form>
			</td>
		</tr>
	</table>";
}
$h->endpage();