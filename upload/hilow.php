<?php
/*
	File:		hilow.php
	Created: 	4/5/2016 at 12:08AM Eastern Time
	Info: 		A game players can play, by guessing if the next drawn
				number will be greater than, or less than, the currently
				shown number.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
//Random number generator for anti-refreshing.
$tresder = (Random(100, 999));
//User's max bet is their level * 500, capping out at 10,000
$maxbet = (10000 < $ir['level'] * 500) ? 10000 : $ir['level'] * 500; 
$_GET['tresde'] = (isset($_GET['tresde']) && is_numeric($_GET['tresde'])) ? abs($_GET['tresde']) : 0;
//Anti-refresh bound isn't bound to SESSION, so bind 0 to it.
if (!isset($_SESSION['tresde']))
{
    $_SESSION['tresde'] = 0;
}
//User has less primary currency than their maximum bet.
if ($ir['primary_currency'] < $maxbet)
{
	alert('danger',"Uh Oh!","You do not have enough Primary Currency to place your bet. You need " . number_format($maxbet),true,'explore.php');
	$_SESSION['number']=0;
	die($h->endpage());
}
//The RNG received from GET does not equal RNG in SESSION, or is less than 100
if (($_SESSION['tresde'] == $_GET['tresde']) || $_GET['tresde'] < 100)
{
    alert('danger',"Uh Oh!","Do not refresh while playing High/Low.",true,"hilow.php?tresde={$tresder}");
	$_SESSION['number']=0;
	die($h->endpage());
}
//Bind RNG from GET to SESSION
$_SESSION['tresde'] = $_GET['tresde'];
echo "<h3>High/Low</h3><hr />";
if (isset($_POST['change']) && in_array($_POST['change'], array('higher','lower')))
{
    //Player did not select a number.
	if (!isset($_SESSION['number']))
	{
		alert('danger',"Uh Oh!","Your last number wasn't saved in session... weird.",true,"hilow.php?tresde={$tresder}");
		die($h->endpage());
	}
	else
	{
        //Bind guessed number from SESSION into a variable.
		$guessed = (isset($_SESSION['number']) && is_numeric($_SESSION['number'])) ? abs($_SESSION['number']) : Random(1,100);
		$numb=Random(1,100);
        //Take the player's better.
		$api->UserTakeCurrency($userid,'primary',$maxbet);
        //Change is suspected to be higher, but new number is lower than original number.
		if ($guessed > $numb && $_POST['change'] == 'higher')
		{
			alert('danger',"Uh Oh!","You guessed the game operator would show a number higher than {$guessed}.
			    The number revealed is {$numb}. You have lost this bet.",false);
			$gain=0;
			$api->SystemLogsAdd($userid,'gambling',"Bet higher number in High/Low and lost {$maxbet}");
		}
        //Change is suspected to be higher, and user is correct.
		elseif ($guessed < $numb && $_POST['change'] == 'higher')
		{
			alert('success',"Success!","You guessed the game operator would show a number higher than {$guessed}.
			    The number revealed is {$numb}. You won this bet!",false);
			$gain=$maxbet*1.5;
			$api->SystemLogsAdd($userid,'gambling',"Bet higher number in High/Low and won {$gain}");
		}
        //Change is suspected to be lower, and user is correct.
		elseif ($guessed > $numb && $_POST['change'] == 'lower')
		{
			alert('success',"Success!","You guessed the game operator would show a number lower than {$guessed}.
			    The number revealed is {$numb}. You have won this bet!",false);
			$gain=$maxbet*1.5;
			$api->SystemLogsAdd($userid,'gambling',"Bet lower number in High/Low and won {$gain}");
		}
        //Change is suspected to be lower, but the new number is higher than the original.
		elseif ($guessed < $numb && $_POST['change'] == 'lower')
		{
			alert('danger',"Uh Oh!","You guessed the game operator would show a number less than {$guessed}. The number
			    revealed is {$numb}. You lose this round.",false);
			$gain=0;
			$api->SystemLogsAdd($userid,'gambling',"Bet lower number in High/Low and lost {$maxbet}");
		}
        //The new number is the same as the old number.
		else
		{
			alert('success',"Success!","The number drawn is the same nubmer as last time. You've lost nothing.",false);
			$gain=$maxbet;
			$api->SystemLogsAdd($userid,'gambling',"Number tied in high/low.");
		}
        //Give the user their winnings, if possible.
		$api->UserGiveCurrency($userid,'primary',$gain);
        //Bind 0 to SESSION to not have abuse.
		$_SESSION['number']=0;
	}
}
else
{
    //Generate starting number and bind it to SESSION.
	$numb=Random(1,100);
	$_SESSION['number']=$numb;
	echo "Welcome to High/Low. Here you will place a bet whether or not the next drawn number will be higher or lower
        than the currently drawn number. The number range is 1 through 100.<br />
	<table class='table table-bordered'>
		<tr>
			<th colspan='2'>
				The operator draws {$numb}. What do you think the next number will be?
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