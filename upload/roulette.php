<?php
/*
	File:		roulette.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		A roulette table mini-game.
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
require_once('globals.php');
$tresder = (randomNumber(100, 999));
$maxbet = $ir['level'] * 250;
$_GET['tresde'] = (isset($_GET['tresde']) && is_numeric($_GET['tresde'])) ? abs($_GET['tresde']) : 0;
if (!isset($_SESSION['tresde'])) {
    $_SESSION['tresde'] = 0;
}
if (($_SESSION['tresde'] == $_GET['tresde']) || $_GET['tresde'] < 100) {
    alert('danger', "Uh Oh!", "Do not refresh while playing Roulette", true, "roulette.php?tresde={$tresder}");
    die($h->endpage());
}
$_SESSION['tresde'] = $_GET['tresde'];
echo "<h3>Roulette</h3><hr />";
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
    $slot[1] = randomNumber(0, 36);
    if ($slot[1] == $_POST['number']) {
        $gain = $_POST['bet'] * 50;
        $title = "Success!";
        $alerttype = 'success';
        $win = 1;
        $phrase = " and won! You keep your bet, and pocket an extra " . number_format($gain);
        $api->game->addLog($userid, 'gambling', "Bet {$_POST['bet']} and won {$gain} in roulette.");
    } else {

        $title = "Uh Oh!";
        $alerttype = 'danger';
        $win = 0;
        $gain = -$_POST['bet'];
        $phrase = ". You lose your bet. Sorry man.";
        $api->game->addLog($userid, 'gambling', "Lost {$_POST['bet']} in roulette.");
    }
    alert($alerttype, $title, "You put in your bet and pull the handle down. Around and around the wheel spins. It stops
	    and lands on {$slot[1]} {$phrase}", true, "roulette.php?tresde={$tresder}");
    $db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + ({$gain}) WHERE `userid` = {$userid}");
    $tresder = randomNumber(100, 999);
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
				" . number_format($maxbet) . " {$_CONFIG['primary_currency']}.
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
				<input type='number' class='form-control' name='number' min='1' max='36' value='18' />
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