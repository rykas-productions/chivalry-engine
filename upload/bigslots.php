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
if ($ir['level'] < 50)
{
    alert('danger',"Uh Oh!","You need to be at least level 50 to use the Federal Slots.",true,'explore.php');
    die($h->endpage());
}
$tresder = (Random(100, 999));
$maxbet = $ir['level'] * 750;
$minbet = $ir['level'] * 250;
if ($maxbet > 250000)
	$maxbet = 250000;
$specialnumber=((getSkillLevel($userid,29)*25)/100);
$maxbet=round($maxbet+($maxbet*$specialnumber));
$_GET['tresde'] = (isset($_GET['tresde']) && is_numeric($_GET['tresde'])) ? abs($_GET['tresde']) : 0;
if (!isset($_SESSION['tresde'])) {
    $_SESSION['tresde'] = 0;
}
if ($ir['winnings_this_hour'] >= (($maxbet*15)*5))
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
    alert('danger', "Uh Oh!", "Please do not refresh while playing slots.", true, "?tresde={$tresder}");
    die($h->endpage());
}
$_SESSION['tresde'] = $_GET['tresde'];
echo "<h3><i class='game-icon game-icon-pokecog'></i> Federal Slots</h3><hr />";
if (isset($_POST['bet']) && is_numeric($_POST['bet'])) {
    $_POST['bet'] = abs($_POST['bet']);
    if ($_POST['bet'] > $ir['primary_currency']) {
        alert('danger', "Uh Oh!", "You cannot bet more than you currently have.", true, "?tresde={$tresder}");
        die($h->endpage());
    } else if ($_POST['bet'] > $maxbet) {
        alert('danger', "Uh Oh!", "You cannot bet more than your max bet of " . number_format($maxbet) . ".", true, "?tresde={$tresder}");
        die($h->endpage());
    } else if ($_POST['bet'] < 0) {
        alert('danger', "Uh Oh!", "You must specify a bet.", true, "?tresde={$tresder}");
        die($h->endpage());
    }
    else if ($_POST['bet'] < $minbet) {
        alert('danger', "Uh Oh!", "You must specify a bet higher than " . number_format($minbet) . " Copper Coins.", true, "?tresde={$tresder}");
        die($h->endpage());
    }
    $slot = array();
    $count = array();
    $c=0;
    $slot[1] = Random(0, 9);
    $slot[2] = Random(0, 9);
    $slot[3] = Random(0, 9);
    $slot[4] = Random(0, 9);
    $slot[5] = Random(0, 9);
    while ($c < 6)
    {
        $count[$c]=0;
        $c=$c+1;
    }
    foreach ($slot as $s)
    {
        if ($s == 1)
            $count[1]=$count[1]+1;
        if ($s == 2)
            $count[2]=$count[2]+1;
        if ($s == 3)
            $count[3]=$count[3]+1;
        if ($s == 4)
            $count[4]=$count[4]+1;
        if ($s == 5)
            $count[5]=$count[5]+1;
        
    }
    $win=false;
    foreach ($count as $d)
    {
        if ($d > 2)
            $win=true;
    }
    if ($slot[1] == $slot[2] && $slot[2] == $slot[3] && $slot[3] == $slot[4] && $slot[4] == $slot[5]) {
        $gain = $_POST['bet'] * 20;
        $title = "Success!";
        $alerttype = 'success';
        $win = 1;
        $phrase = "All five line up. Jack pot! You win an extra " . number_format($gain);
		addToEconomyLog('Gambling', 'copper', $gain);
        $api->SystemLogsAdd($userid, 'gambling', "Bet {$_POST['bet']} and won {$gain} in federal slots.");
		$db->query("UPDATE `user_settings` SET `winnings_this_hour` = `winnings_this_hour` + {$gain} WHERE `userid` = {$userid}");
		$db->query("UPDATE `settings` SET `setting_value` = `setting_value` + {$gain} WHERE `setting_name` = 'casino_give'");
		$db->query("UPDATE `settings` SET `setting_value` = `setting_value` + {$_POST['bet']} WHERE `setting_name` = 'casino_take'");
    } elseif ($win) {
        $gain = $_POST['bet'] * 7.5;
        $title = "Success!";
        $alerttype = 'success';
        $win = 1;
        $phrase = "Three numbers line up. Jack pot! You win an extra " . number_format($gain);
		addToEconomyLog('Gambling', 'copper', $gain);
        $api->SystemLogsAdd($userid, 'gambling', "Bet {$_POST['bet']} and won {$gain} in federal slots.");
		$db->query("UPDATE `user_settings` SET `winnings_this_hour` = `winnings_this_hour` + {$gain} WHERE `userid` = {$userid}");
		$db->query("UPDATE `settings` SET `setting_value` = `setting_value` + {$gain} WHERE `setting_name` = 'casino_give'");
		$db->query("UPDATE `settings` SET `setting_value` = `setting_value` + {$_POST['bet']} WHERE `setting_name` = 'casino_take'");
    } else {

        $title = "Uh Oh!";
        $alerttype = 'danger';
        $win = 0;
        $gain = -$_POST['bet'];
		addToEconomyLog('Gambling', 'copper', $gain*-1);
        $phrase = "Round and round the slots go. Unlucky! None of them line up!";
		$db->query("UPDATE `user_settings` SET `winnings_this_hour` = `winnings_this_hour` - {$_POST['bet']} WHERE `userid` = {$userid}");
        $api->SystemLogsAdd($userid, 'gambling', "Lost {$_POST['bet']} in  federal slots.");
		$db->query("UPDATE `settings` SET `setting_value` = `setting_value` + {$_POST['bet']} WHERE `setting_name` = 'casino_take'");
    }
    alert($alerttype, $title, "You pull down the handle and slots begin to spin. They show {$slot[1]}, {$slot[2]}, {$slot[3]}, {$slot[4]}, {$slot[5]}. {$phrase}", false);
    $db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + ({$gain}) WHERE `userid` = {$userid}");
    $tresder = Random(100, 999);
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
				Welcome to the Federal Slots. Bet some of your hard earned cash for a slim chance to win big! At your
				level, we've imposed a betting restriction of " . number_format($maxbet) . " Copper Coins. You must bet at least 
                " . number_format($minbet) . " Copper Coins to bet here. All five numbers lining up gets you the best prize, where three gets you a small prize.
			</th>
		</tr>
		<tr>
			<th>
				Bet
			</th>
			<td>
				<input type='number' class='form-control' name='bet' min='{$minbet}' max='{$maxbet}' value='{$maxbet}' />
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