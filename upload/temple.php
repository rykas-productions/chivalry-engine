<?php
require('globals.php');
echo "<h3>{$lang['TEMPLE_TITLE']}</h3><hr />";
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
	case 'energy':
		energy();
		break;
	case 'brave':
		brave();
		break;
	case 'will':
		will();
		break;
	case 'iq':
		iq();
		break;
	default:
		home();
		break;
}
function home()
{
	global $lang,$db,$ir,$set;
	echo $lang['TEMPLE_INTRO'];
	echo "<br />
	<a href='?action=energy'>{$lang['TEMPLE_ENERGY']}" . number_format($set['energy_refill_cost']) . " {$lang['INDEX_SECCURR']}</a><br />
	<a href='?action=brave'>{$lang['TEMPLE_BRAVE']}" . number_format($set['brave_refill_cost']) . " {$lang['INDEX_SECCURR']}</a><br />
	<a href='?action=will'>{$lang['TEMPLE_WILL']}" . number_format($set['will_refill_cost']) . " {$lang['INDEX_SECCURR']}</a><br />
	<a href='?action=will'>{$lang['TEMPLE_IQ']}" . number_format($set['will_refill_cost']) . " {$lang['INDEX_SECCURR']}</a><br />";
}
function energy()
{
	global $db,$api,$lang,$userid,$ir,$h,$set;
	if ($api->UserHasCurrency($userid,'secondary',$set['energy_refill_cost']))
	{
		if ($api->UserInfoGet($userid,'energy',true) == 100)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['TEMPLE_ENERGY_ERR1'],true,'temple.php');
		}
		else
		{
			$api->UserInfoSet($userid,'energy',100,true);
			$api->UserTakeCurrency($userid,'secondary',$set['energy_refill_cost']);
			alert('success',$lang['ERROR_SUCCESS'],$lang['TEMPLE_ENERGY_SUCC'],true,'temple.php');
		}
	}
	else
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['TEMPLE_ENERGY_ERR'],true,'temple.php');
	}
}
function brave()
{
	global $db,$api,$lang,$userid,$ir,$h,$set;
	if ($api->UserHasCurrency($userid,'secondary',$set['brave_refill_cost']))
	{
		if ($api->UserInfoGet($userid,'brave',true) == 100)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['TEMPLE_BRAVE_ERR1'],true,'temple.php');
		}
		else
		{
			$api->UserInfoSet($userid,'brave',5,true);
			$api->UserTakeCurrency($userid,'secondary',$set['brave_refill_cost']);
			alert('success',$lang['ERROR_SUCCESS'],$lang['TEMPLE_BRAVE_SUCC'],true,'temple.php');
		}
	}
	else
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['TEMPLE_BRAVE_ERR'],true,'temple.php');
	}
}
function will()
{
	global $db,$api,$lang,$userid,$ir,$h,$set;
	if ($api->UserHasCurrency($userid,'secondary',$set['will_refill_cost']))
	{
		if ($api->UserInfoGet($userid,'will',true) == 100)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['TEMPLE_WILL_ERR1'],true,'temple.php');
		}
		else
		{
			$api->UserInfoSet($userid,'will',5,true);
			$api->UserTakeCurrency($userid,'secondary',$set['will_refill_cost']);
			alert('success',$lang['ERROR_SUCCESS'],$lang['TEMPLE_WILL_SUCC'],true,'temple.php');
		}
	}
	else
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['TEMPLE_WILL_ERR'],true,'temple.php');
	}
}
function iq()
{
	global $db,$api,$lang,$userid,$ir,$h,$set;
	if (isset($_POST['iq']))
	{
		$_POST['iq'] = (isset($_POST['iq']) && is_numeric($_POST['iq'])) ? abs($_POST['iq']) : '';
		if (empty($_POST['iq']))
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['TEMPLE_IQ_ERR']);
			die($h->endpage());
		}
		$totalcost = $_POST['iq'] * $set['iq_per_sec'];
		if ($api->UserHasCurrency($userid,'secondary',$_POST['iq']) == false)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['TEMPLE_IQ_ERR1']);
			die($h->endpage());
		}
		$api->UserTakeCurrency($userid,'secondary',$_POST['iq']);
		$db->query("UPDATE `userstats` SET `iq` = `iq` + {$totalcost} WHERE `userid` = {$userid}");
		alert('success',$lang['ERROR_SUCCESS'],"{$lang['TEMPLE_IQ_SUCC']} " . number_format($_POST['iq']) . " {$lang['INDEX_SECCURR']} {$lang['GEN_FOR_S']} " . number_format($totalcost) . " {$lang['GEN_IQ']}.",true,'temple.php');
	}
	else
	{
		alert('info',$lang['ERROR_INFO'],"{$lang['TEMPLE_IQ_INFO']}" . number_format($ir['secondary_currency']) . " " . $lang['INDEX_SECCURR'], false);
		echo "<table class='table table-bordered'>
			<form method='post'>
			<tr>
				<th>
					{$lang['TEMPLE_IQ_TH']}
				</th>
				<td>
					<input type='number' class='form-control' name='iq' min='1' max='{$ir['secondary_currency']}' required='1'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-default' value='{$lang['TEMPLE_IQ_BTN']}'>
				</td>
			</tr>
			</form>
		</table>";
	}
}
$h->endpage();