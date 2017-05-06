<?php
/*
	File:		slots.php
	Created: 	4/5/2016 at 12:26AM Eastern Time
	Info: 		Allows players to play slots for a chance at getting
				more primary currency.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require_once('globals.php');
$tresder = (Random(100, 999));
$maxbet = $ir['level'] * 500;
$_GET['tresde'] = (isset($_GET['tresde']) && is_numeric($_GET['tresde'])) ? abs($_GET['tresde']) : 0;
if (!isset($_SESSION['tresde']))
{
    $_SESSION['tresde'] = 0;
}
if (($_SESSION['tresde'] == $_GET['tresde']) || $_GET['tresde'] < 100)
{
    alert('danger',$lang['ERROR_GENERIC'],$lang['SLOTS_NOREFRESH'],true,"?tresde={$tresder}");
	die($h->endpage());
}
$_SESSION['tresde'] = $_GET['tresde'];
echo "<h3>{$lang['SLOTS_TITLE']}</h3><hr />";
if (isset($_POST['bet']) && is_numeric($_POST['bet']))
{
	$_POST['bet'] = abs($_POST['bet']);
    if ($_POST['bet'] > $ir['primary_currency'])
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['ROULETTE_ERROR1'],true,"?tresde={$tresder}");
		die($h->endpage());
    }
	else if ($_POST['bet'] > $maxbet)
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['ROULETTE_ERROR2'],true,"?tresde={$tresder}");
		die($h->endpage());
    }
	else if ($_POST['bet'] < 0)
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['ROULETTE_ERROR4'],true,"?tresde={$tresder}");
		die($h->endpage());
    }
	$slot = array();
    $slot[1] = Random(0, 9);
	$slot[2] = Random(0, 9);
	$slot[3] = Random(0, 9);
	if ($slot[1] == $slot[2] && $slot[2] == $slot[3])
	{
        $gain = $_POST['bet'] * 79;
		$title="{$lang['ERROR_SUCCESS']}";
		$alerttype='success';
		$win=1;
		$phrase="{$lang['ROULETTE_WIN']} " . number_format($gain);
		$api->SystemLogsAdd($userid,'gambling',"Bet {$_POST['bet']} and won {$gain} in slots.");
	}
	else if ($slot[1] == $slot[2] || $slot[2] == $slot[3]
            || $slot[1] == $slot[3])
    {
        $gain = $_POST['bet'] * 50;
		$title="{$lang['ERROR_SUCCESS']}";
		$alerttype='success';
		$win=1;
		$phrase="{$lang['ROULETTE_WIN']} " . number_format($gain);
		$api->SystemLogsAdd($userid,'gambling',"Bet {$_POST['bet']} and won {$gain} in slots.");
    }
	else
	{

		$title="{$lang['ERROR_GENERIC']}";
		$alerttype='danger';
		$win=0;
        $gain = -$_POST['bet'];
		$phrase="{$lang['ROULETTE_LOST']}";
		$api->SystemLogsAdd($userid,'gambling',"Lost {$_POST['bet']} in slots.");
	}
	alert($alerttype,$title,"{$lang['ROULETTE_START']} {$slot[1]}, {$slot[2]}, {$slot[3]}{$phrase}",true,"?tresde={$tresder}");
	$db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + ({$gain}) WHERE `userid` = {$userid}");
	$tresder = Random(100, 999);
	echo "<br />
	<form action='?tresde={$tresder}' method='post'>
    	<input type='hidden' name='bet' value='{$_POST['bet']}' />
    	<input type='submit' class='btn btn-default' value='{$lang['ROULETTE_BTN2']}' />
    </form>
	<a href='?tresde={$tresder}'>{$lang['ROULETTE_BTN3']}</a><br />
	<a href='explore.php'>{$lang['ROULETTE_BTN4']}</a>";
}
else
{
	echo "
	<form action='?tresde={$tresder}' method='post'>
	<table class='table table-bordered'>
		<tr>
			<th colspan='2'>
				{$lang['SLOTS_INFO']} " . number_format($maxbet) . " {$lang['INDEX_PRIMCURR']}.
			</th>
		</tr>
		<tr>
			<th>
				{$lang['SLOTS_TABLE1']}
			</th>
			<td>
				<input type='number' class='form-control' name='bet' min='0' max='{$maxbet}' value='5' />
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<input class='btn btn-default' type='submit' value='{$lang['SLOTS_BTN']}' />
			</td>
		</tr>
	</table>
	</form>";
}
$h->endpage();