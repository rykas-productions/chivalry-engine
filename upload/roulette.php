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
$tresder = (Random(100, 999));
$maxbet = $ir['level'] * 250;
$_GET['tresde'] = (isset($_GET['tresde']) && is_numeric($_GET['tresde'])) ? abs($_GET['tresde']) : 0;
if (!isset($_SESSION['tresde']))
{
    $_SESSION['tresde'] = 0;
}
if (($_SESSION['tresde'] == $_GET['tresde']) || $_GET['tresde'] < 100)
{
    alert('danger',$lang['ERROR_GENERIC'],$lang['ROULETTE_NOREFRESH'],true,"roulette.php?tresde={$tresder}");
	die($h->endpage());
}
$_SESSION['tresde'] = $_GET['tresde'];
echo "<h3>{$lang['ROULETTE_TITLE']}</h3><hr />";
if (isset($_POST['bet']) && is_numeric($_POST['bet']))
{
	$_POST['bet'] = abs($_POST['bet']);
    if (!isset($_POST['number']))
    {
        $_POST['number'] = 0;
    }
    $_POST['number'] = abs($_POST['number']);
    if ($_POST['bet'] > $ir['primary_currency'])
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['ROULETTE_ERROR1'],true,"roulette.php?tresde={$tresder}");
		die($h->endpage());
    }
	else if ($_POST['bet'] > $maxbet)
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['ROULETTE_ERROR2'],true,"roulette.php?tresde={$tresder}");
		die($h->endpage());
    }
    else if ($_POST['number'] > 36 || $_POST['number'] < 0)
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['ROULETTE_ERROR3'],true,"roulette.php?tresde={$tresder}");
		die($h->endpage());
    }
	else if ($_POST['bet'] < 0)
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['ROULETTE_ERROR4'],true,"roulette.php?tresde={$tresder}");
		die($h->endpage());
    }
	$slot = array();
    $slot[1] = Random(0, 36);
	if ($slot[1] == $_POST['number'])
	{
        $gain = $_POST['bet'] * 50;
		$title="{$lang['ERROR_SUCCESS']}";
		$alerttype='success';
		$win=1;
		$phrase="{$lang['ROULETTE_WIN']} " . number_format($gain);
		$api->SystemLogsAdd($userid,'gambling',"Bet {$_POST['bet']} and won {$gain} in roulette.");
	}
	else
	{

		$title="{$lang['ERROR_GENERIC']}";
		$alerttype='danger';
		$win=0;
        $gain = -$_POST['bet'];
		$phrase="{$lang['ROULETTE_LOST']}";
		$api->SystemLogsAdd($userid,'gambling',"Lost {$_POST['bet']} in roulette.");
	}
	alert($alerttype,$title,"{$lang['ROULETTE_START']} {$slot[1]}{$phrase}",true,"roulette.php?tresde={$tresder}");
	$db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + ({$gain}) WHERE `userid` = {$userid}");
	$tresder = Random(100, 999);
	echo "<br />
	<form action='roulette.php?tresde={$tresder}' method='post'>
    	<input type='hidden' name='bet' value='{$_POST['bet']}' />
    	<input type='hidden' name='number' value='{$_POST['number']}' />
    	<input type='submit' class='btn btn-default' value='{$lang['ROULETTE_BTN2']}' />
    </form>
	<a href='roulette.php?tresde={$tresder}'>{$lang['ROULETTE_BTN3']}</a><br />
	<a href='explore.php'>{$lang['ROULETTE_BTN4']}</a>";
}
else
{
	echo "
	<form action='?tresde={$tresder}' method='post'>
	<table class='table table-bordered'>
		<tr>
			<th colspan='2'>
				{$lang['ROULETTE_INFO']} " . number_format($maxbet) . " {$lang['INDEX_PRIMCURR']}.
			</th>
		</tr>
		<tr>
			<th>
				{$lang['ROULETTE_TABLE1']}
			</th>
			<td>
				<input type='number' class='form-control' name='bet' min='0' max='{$maxbet}' value='5' />
			</td>
		</tr>
		<tr>
			<th>
				{$lang['ROULETTE_TABLE2']}
			</th>
			<td>
				<input type='number' class='form-control' name='number' min='1' max='36' value='18' />
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<input class='btn btn-default' type='submit' value='{$lang['ROULETTE_BTN1']}' />
			</td>
		</tr>
	</table>
	</form>";
}
$h->endpage();