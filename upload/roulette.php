<?php
/*
	File:		roulette.php
	Created: 	4/5/2016 at 12:24AM Eastern Time
	Info: 		Allows players to play a game of roulette for cash.
				If they win, their winnings are double their bet.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require_once('globals.php');
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
	alert('danger',"Uh Oh!","You cannot use the roulette table while in the infirmary or dungeon.",true,'index.php');
	die($h->endpage());
}
$tresder = (Random(100, 999));
$maxbet = $ir['level'] * 500;
$specialnumber=((getSkillLevel($userid,29)*25)/100);
$maxbet=$maxbet+($maxbet*$specialnumber);
$_GET['tresde'] = (isset($_GET['tresde']) && is_numeric($_GET['tresde'])) ? abs($_GET['tresde']) : 0;
if (!isset($_SESSION['tresde'])) {
    $_SESSION['tresde'] = 0;
}
if ($ir['winnings_this_hour'] >= (($maxbet*15)*20))
{
	alert('danger', "Uh Oh!", "The casino's run out of cash to give you. Come back in an hour.", true, "explore.php");
    die($h->endpage());
}
if ($ir['winnings_this_hour'] <= ((($maxbet*15)*7.5)*-1))
{
	alert('danger', "Uh Oh!", "You are too deep in the hole. Come back next hour to try again.", true, "explore.php");
    die($h->endpage());
}
if (($_SESSION['tresde'] == $_GET['tresde']) || $_GET['tresde'] < 100) {
    alert('danger', "Uh Oh!", "Do not refresh while playing Roulette", true, "roulette.php?tresde={$tresder}");
    die($h->endpage());
}
$_SESSION['tresde'] = $_GET['tresde'];
echo "<h3><i class='game-icon game-icon-table'></i> Roulette</h3><hr />";
if (isset($_POST['bet']) && is_numeric($_POST['bet'])) {
    $_POST['bet'] = abs($_POST['bet']);
    if (!isset($_POST['number'])) {
        $_POST['number'] = 0;
    }
    $_POST['number'] = abs($_POST['number']);
    if ($_POST['bet'] > $ir['primary_currency']) {
        alert('danger', "Uh Oh!", "You are trying to bet more cash than you currently have.", true, "roulette.php?tresde={$tresder}");
        die($h->endpage());
    } else if ($_POST['bet'] > $maxbet) {
        alert('danger', "Uh Oh!", "You are trying to bet more than you're allowed to at your level.", true, "roulette.php?tresde={$tresder}");
        die($h->endpage());
    } else if ($_POST['number'] > 36 || $_POST['number'] < 0) {
        alert('danger', "Uh Oh!", "You input an invalid guess.", true, "roulette.php?tresde={$tresder}");
        die($h->endpage());
    } else if ($_POST['bet'] < 0) {
        alert('danger', "Uh Oh!", "You cannot bet less than 0 cash.", true, "roulette.php?tresde={$tresder}");
        die($h->endpage());
    }
    $slot = array();
	$accepted=false;
	/*if (isRigged())
	{
		while (!$accepted)
		{
			$slot[1] = Random(0, 36);
			if ($slot[1] != $_POST['number'])
				$accepted=true;
		}
			
	}
	else
	{
		$slot[1] = Random(0, 36);
	}*/
	$slot[1] = Random(0, 36);
    if ($slot[1] == $_POST['number']) {
        $gain = $_POST['bet'] * 25;
        $title = "Success!";
        $alerttype = 'success';
        $win = 1;
        $phrase = " and won! You keep your bet, and pocket an extra " . number_format($gain);
		addToEconomyLog('Gambling', 'copper', $gain);
        $api->SystemLogsAdd($userid, 'gambling', "Bet {$_POST['bet']} and won {$gain} in roulette.");
		$db->query("UPDATE `settings` SET `setting_value` = `setting_value` + {$gain} WHERE `setting_name` = 'casino_give'");
		$db->query("UPDATE `settings` SET `setting_value` = `setting_value` + {$_POST['bet']} WHERE `setting_name` = 'casino_take'");
		$db->query("UPDATE `user_settings` SET `winnings_this_hour` = `winnings_this_hour` + {$gain} WHERE `userid` = {$userid}");
    } else {

        $title = "Uh Oh!";
        $alerttype = 'danger';
        $win = 0;
        $gain = -$_POST['bet'];
        $phrase = ". You lose your bet. Sorry man.";
		addToEconomyLog('Gambling', 'copper', $gain*-1);
		$db->query("UPDATE `user_settings` SET `winnings_this_hour` = `winnings_this_hour` - {$_POST['bet']} WHERE `userid` = {$userid}");
        $api->SystemLogsAdd($userid, 'gambling', "Lost {$_POST['bet']} in roulette.");
		$db->query("UPDATE `settings` SET `setting_value` = `setting_value` + {$_POST['bet']} WHERE `setting_name` = 'casino_take'");
    }
    alert($alerttype, $title, "You put in your bet and pull the handle down. Around and around the wheel spins. It stops
	    and lands on {$slot[1]} {$phrase}", true, "roulette.php?tresde={$tresder}");
    $db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + ({$gain}) WHERE `userid` = {$userid}");
    $tresder = Random(100, 999);
    echo "<br />
	<form action='roulette.php?tresde={$tresder}' method='post'>
    	<input type='hidden' name='bet' value='{$_POST['bet']}' />
    	<input type='hidden' name='number' value='{$_POST['number']}' />
    	<input type='submit' class='btn btn-primary' value='Again, Same Bet' />
    </form>
	<a href='roulette.php?tresde={$tresder}'>Again, Different Bet</a><br />
	<a href='explore.php'>I'm Good</a>";
} else {
    echo "
	<form action='?tresde={$tresder}' method='post'>
	<table class='table table-bordered'>
		<tr>
			<th colspan='2'>
				Ready to test your luck? Awesome! Here at the roulette table, the house always wins. To combat players
				losing all their wealth in one go, we've put in a bet restriction. At your level, you can only bet
				" . number_format($maxbet) . " Copper Coins.
			</th>
		</tr>
		<tr>
			<th>
				Bet
			</th>
			<td>
				<input type='number' class='form-control' name='bet' min='0' max='{$maxbet}' value='5' />
			</td>
		</tr>
		<tr>
			<th>
				Pick #
			</th>
			<td>
				<input type='number' class='form-control' name='number' min='0' max='36' value='18' />
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<input class='btn btn-primary' type='submit' value='Place Bet' />
			</td>
		</tr>
	</table>
	</form>";
}
$h->endpage();