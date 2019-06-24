<?php
/*
	File:		slots.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows players to play slots, in hopes of getting more 
				Primary Currency.
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
$maxbet = $ir['level'] * 500;
$_GET['tresde'] = (isset($_GET['tresde']) && is_numeric($_GET['tresde'])) ? abs($_GET['tresde']) : 0;
if (!isset($_SESSION['tresde'])) {
    $_SESSION['tresde'] = 0;
}
if (($_SESSION['tresde'] == $_GET['tresde']) || $_GET['tresde'] < 100) {
    alert('danger', "Uh Oh!", "Please do not refresh while playing slots.", true, "?tresde={$tresder}");
    die($h->endpage());
}
$_SESSION['tresde'] = $_GET['tresde'];
echo "<h3>Slots Machine</h3><hr />";
if (isset($_POST['bet']) && is_numeric($_POST['bet'])) {
    $_POST['bet'] = abs($_POST['bet']);
    if ($_POST['bet'] > $ir['primary_currency']) {
        alert('danger', "Uh Oh!", "You cannot bet more than you currently have.", true, "?tresde={$tresder}");
        die($h->endpage());
    } else if ($_POST['bet'] > $maxbet) {
        alert('danger', "Uh Oh!", "You cannot bet more than your max bet of {$maxbet}.", true, "?tresde={$tresder}");
        die($h->endpage());
    } else if ($_POST['bet'] < 0) {
        alert('danger', "Uh Oh!", "You must specify a bet.", true, "?tresde={$tresder}");
        die($h->endpage());
    }
    $slot = array();
    $slot[1] = randomNumber(0, 9);
    $slot[2] = randomNumber(0, 9);
    $slot[3] = randomNumber(0, 9);
    if ($slot[1] == $slot[2] && $slot[2] == $slot[3]) {
        $gain = $_POST['bet'] * 79;
        $title = "Success!";
        $alerttype = 'success';
        $win = 1;
        $phrase = "All three line up. Jack pot! You win an extra " . number_format($gain);
        $api->game->addLog($userid, 'gambling', "Bet {$_POST['bet']} and won {$gain} in slots.");
    } else if ($slot[1] == $slot[2] || $slot[2] == $slot[3]
        || $slot[1] == $slot[3]
    ) {
        $gain = $_POST['bet'] * 50;
        $title = "Success!";
        $alerttype = 'success';
        $win = 1;
        $phrase = "Two slots line up. Awesome! You win an extra " . number_format($gain);
        $api->game->addLog($userid, 'gambling', "Bet {$_POST['bet']} and won {$gain} in slots.");
    } else {

        $title = "Uh Oh!";
        $alerttype = 'danger';
        $win = 0;
        $gain = -$_POST['bet'];
        $phrase = "Round and round the slots go. Unlucky! None of them line up!";
        $api->game->addLog($userid, 'gambling', "Lost {$_POST['bet']} in slots.");
    }
    alert($alerttype, $title, "You pull down the handle and slots begin to spin. They show {$slot[1]}, {$slot[2]}, {$slot[3]}. {$phrase}", true, "?tresde={$tresder}");
    $db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + ({$gain}) WHERE `userid` = {$userid}");
    $tresder = randomNumber(100, 999);
    echo "<br />
	<form action='?tresde={$tresder}' method='post'>
    	<input type='hidden' name='bet' value='{$_POST['bet']}' />
    	<input type='submit' class='btn btn-primary' value='Again, Same Bet!' />
    </form>
	<a href='?tresde={$tresder}'>Again, Different Bet!</a><br />
	<a href='explore.php'>Go Home</a>";
} else {
    echo "
	<form action='?tresde={$tresder}' method='post'>
	<table class='table table-bordered'>
		<tr>
			<th colspan='2'>
				Welcome to the slots machine. Bet some of your hard earned cash for a slim chance to win big! At your
				level, we've imposed a betting restriction of " . number_format($maxbet) . " {$_CONFIG['primary_currency']}.
			</th>
		</tr>
		<tr>
			<th>
				Bet
			</th>
			<td>
				<input type='number' class='form-control' name='bet' min='1' max='{$maxbet}' value='5' />
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<input class='btn btn-primary' type='submit' value='Spin Baby, Spin!' />
			</td>
		</tr>
	</table>
	</form>";
}
$h->endpage();