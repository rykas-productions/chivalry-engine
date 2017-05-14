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
$tresder = (Random(100, 999));
$maxbet = (10000 < $ir['level'] *500) ? 10000 : $ir['level'] * 500; 
$_GET['tresde'] = (isset($_GET['tresde']) && is_numeric($_GET['tresde'])) ? abs($_GET['tresde']) : 0;
if (!isset($_SESSION['tresde']))
{
    $_SESSION['tresde'] = 0;
}
if ($ir['primary_currency'] < $maxbet)
{
	alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['HILOW_NOBET']} " . number_format($maxbet),true,'explore.php');
	$_SESSION['number']=0;
	die($h->endpage());
}

if (($_SESSION['tresde'] == $_GET['tresde']) || $_GET['tresde'] < 100)
{
    alert('danger',$lang['ERROR_GENERIC'],$lang['HILOW_NOREFRESH'],true,"hilow.php?tresde={$tresder}");
	$_SESSION['number']=0;
	die($h->endpage());
}

$_SESSION['tresde'] = $_GET['tresde'];
echo "<h3>{$lang['EXPLORE_HILO']}</h3><hr />";
if (isset($_POST['change']) && in_array($_POST['change'], array('higher','lower')))
{
	if (!isset($_SESSION['number']))
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['HILOW_UNDEFINEDNUMBER'],true,"hilow.php?tresde={$tresder}");
		die($h->endpage());
	}
	else
	{
		$guessed = (isset($_SESSION['number']) && is_numeric($_SESSION['number'])) ? abs($_SESSION['number']) : Random(1,100);
		$numb=Random(1,100);
		$api->UserTakeCurrency($userid,'primary',$maxbet);
		if ($guessed > $numb && $_POST['change'] == 'higher')
		{
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['HIGHLOW_HIGH']} {$guessed}. {$lang['HIGHLOW_REVEAL']} {$numb}. {$lang['HIGHLOW_LOSE']}",false);
			$gain=0;
			$api->SystemLogsAdd($userid,'gambling',"Bet higher number in High/Low and lost {$maxbet}");
		}
		elseif ($guessed < $numb && $_POST['change'] == 'higher')
		{
			alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['HIGHLOW_HIGH']} {$guessed}. {$lang['HIGHLOW_REVEAL']} {$numb}. {$lang['HIGHLOW_WIN']}",false);
			$gain=$maxbet*1.5;
			$api->SystemLogsAdd($userid,'gambling',"Bet higher number in High/Low and won {$gain}");
		}
		elseif ($guessed > $numb && $_POST['change'] == 'lower')
		{
			alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['HIGHLOW_LOWER']} {$guessed}. {$lang['HIGHLOW_REVEAL']} {$numb}. {$lang['HIGHLOW_WIN']}",false);
			$gain=$maxbet*1.5;
			$api->SystemLogsAdd($userid,'gambling',"Bet lower number in High/Low and won {$gain}");
		}
		elseif ($guessed < $numb && $_POST['change'] == 'lower')
		{
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['HIGHLOW_LOWER']} {$guessed}. {$lang['HIGHLOW_REVEAL']} {$numb}. {$lang['HIGHLOW_LOSE']}",false);
			$gain=0;
			$api->SystemLogsAdd($userid,'gambling',"Bet lower number in High/Low and lost {$maxbet}");
		}
		else
		{
			alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['HIGHLOW_TIE']}",false);
			$gain=$maxbet;
			$api->SystemLogsAdd($userid,'gambling',"Number tied in high/low.");
		}
		$api->UserGiveCurrency($userid,'primary',$gain);
		$_SESSION['number']=0;
	}
}
else
{
	$numb=Random(1,100);
	$_SESSION['number']=$numb;
	echo "{$lang['HILOW_INFO']}<br />
	<table class='table table-bordered'>
		<tr>
			<th colspan='2'>
				{$lang['HILOW_SHOWN']} {$numb}. {$lang['HILOW_WATDO']}
			</th>
		</tr>
		<tr>
			<td>
				<form action='?tresde={$tresder}' method='post'>
					<input type='hidden' name='change' value='lower'>
					<input type='submit' value='{$lang['HILOW_LOWER']}' class='btn btn-default'>
				</form>
			</td>
			<td>
				<form action='?tresde={$tresder}' method='post'>
					<input type='hidden' name='change' value='higher'>
					<input type='submit' value='{$lang['HILOW_HIGHER']}' class='btn btn-default'>
				</form>
			</td>
		</tr>
	</table>";
}
$h->endpage();