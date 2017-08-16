<?php
/*
	File:		secmarket.php
	Created: 	4/5/2016 at 4:44PM Eastern Time
	Info: 		Allows players to sell their secondary currency at
				their own prices, and to buy offers on the market.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
echo "<h3>{$lang['EXPLORE_SCMARKET']}</h3><hr />";
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
	case 'add':
		add();
		break;
	case 'remove':
		remove();
		break;
	case 'buy':
		buy();
		break;
	default:
		home();
		break;
}
function home()
{
	global $db,$lang,$api,$userid;
	echo "<a href='?action=add'>{$lang['SMARKET_ADD']}</a><hr />
	<table class='table table-bordered table-striped'>
		<tr>
			<th>
				{$lang['SMARKET_TH']}
			</th>
			<th>
				{$lang['INDEX_SECCURR']}
			</th>
			<th>
				{$lang['SMARKET_TH1']} ({$lang['INDEX_PRIMCURR']})
			</th>
			<th>
				{$lang['SMARKET_TH2']}
			</th>
		</tr>";
		$q=$db->query("SELECT * FROM `sec_market` ORDER BY `sec_cost` ASC");
		while ($r = $db->fetch_row($q))
		{
			$totalcost = $r['sec_total']*$r['sec_cost'];
			if ($r['sec_user'] == $userid)
			{
				$a="[<a href='?action=remove&id={$r['sec_id']}'>{$lang['SMARKET_TD']}</a>]";
			}
			else
			{
				$a="[<a href='?action=buy&id={$r['sec_id']}'>{$lang['SMARKET_TD1']}</a>]";
			}
			echo "<tr>
				<td>
					<a href='profile.php?user={$r['sec_user']}'>{$api->SystemUserIDtoName($r['sec_user'])}</a> [{$r['sec_user']}]
				</td>
				<td>
					" . number_format($r['sec_total']) . "
				</td>
				<td>
					" . number_format($r['sec_cost']) . " (" . number_format($totalcost) . ")
				</td>
				<td>
					{$a}
				</td>
			</tr>";
		}
		echo"</table>";
}
function buy()
{
	global $db,$h,$userid,$api,$lang,$ir;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
	if (empty($_GET['id']))
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['SMARKET_ERR'],true,'secmarket.php');
		die($h->endpage());
	}
	$q=$db->query("SELECT * FROM `sec_market` WHERE `sec_id` = {$_GET['id']}");
	if ($db->num_rows($q) == 0)
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['SMARKET_ERR2'],true,'secmarket.php');
		die($h->endpage());
	}
	$r=$db->fetch_row();
	if ($r['sec_user'] == $userid)
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['SMARKET_ERR1'],true,'secmarket.php');
		die($h->endpage());
	}
	$totalcost = $r['sec_cost']*$r['sec_total'];
	if ($api->UserHasCurrency($userid,'primary',$totalcost) == false)
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['SMARKET_ERR3'],true,'secmarket.php');
		die($h->endpage());
	}
	$api->SystemLogsAdd($userid,'secmarket',"Bought {$r['sec_total']} Secondary Currency from the market for {$totalcost} Primary Currency.");
	$api->UserGiveCurrency($userid,'secondary',$r['sec_total']);
	$api->UserTakeCurrency($userid,'primary',$totalcost);
	$api->UserGiveCurrency($r['sec_user'],'primary',$totalcost);
	$api->GameAddNotification($r['sec_user'],"<a href='profile.php?user={$userid}'>{$ir['username']}</a> has bought your {$r['sec_total']} Secondary Currency offer from the market for a total of {$totalcost}.");
	$db->query("DELETE FROM `sec_market` WHERE `sec_id` = {$_GET['id']}");
	alert('success',$lang['ERROR_SUCCESS'],$lang['SMARKET_SUCC'],true,'secmarket.php');
	die($h->endpage());
}
function remove()
{
	global $db,$h,$userid,$api,$lang,$ir;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
	if (empty($_GET['id']))
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['SMARKET_BERR'],true,'secmarket.php');
		die($h->endpage());
	}
	$q=$db->query("SELECT * FROM `sec_market` WHERE `sec_id` = {$_GET['id']}");
	if ($db->num_rows($q) == 0)
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['SMARKET_BERR2'],true,'secmarket.php');
		die($h->endpage());
	}
	$r=$db->fetch_row();
	if (!($r['sec_user'] == $userid))
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['SMARKET_BERR1'],true,'secmarket.php');
		die($h->endpage());
	}
	$api->SystemLogsAdd($userid,'secmarket',"Removed {$r['sec_total']} Secondary Currency from the market.");
	$api->UserGiveCurrency($userid,'secondary',$r['sec_total']);
	$db->query("DELETE FROM `sec_market` WHERE `sec_id` = {$_GET['id']}");
	alert('success',$lang['ERROR_SUCCESS'],$lang['SMARKET_SUCC1'],true,'secmarket.php');
	die($h->endpage());
}
function add()
{
	global $db,$h,$userid,$api,$lang,$ir;
	if (isset($_POST['qty']) && isset($_POST['cost']))
	{
		$_POST['qty'] = (isset($_POST['qty']) && is_numeric($_POST['qty'])) ? abs($_POST['qty']) : '';
		$_POST['cost'] = (isset($_POST['cost']) && is_numeric($_POST['cost'])) ? abs($_POST['cost']) : '';
		if (empty($_POST['qty']) || empty($_POST['cost']))
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['SMARKET_AERR']);
			die($h->endpage());
		}
		if (!($api->UserHasCurrency($userid,'secondary',$_POST['qty'])))
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['SMARKET_AERR1']);
			die($h->endpage());
		}
		$db->query("INSERT INTO `sec_market` (`sec_user`, `sec_cost`, `sec_total`) 
					VALUES ('{$userid}', '{$_POST['cost']}', '{$_POST['qty']}');");
		$api->UserTakeCurrency($userid,'secondary',$_POST['qty']);
		$api->SystemLogsAdd($userid,'secmarket',"Added {$_POST['qty']} to the secondary market for {$_POST['cost']} Primary Currency each.");
		alert('success',$lang['ERROR_SUCCESS'],$lang['SMARKET_SUCC2'],true,'secmarket.php');
	die($h->endpage());
	}
	else
	{
		alert('info',$lang['ERROR_INFO'],$lang['SMARKET_INFO'],false);
		echo "
		<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th>
						{$lang['INDEX_SECCURR']}
					</th>
					<td>
						<input type='number' name='qty' class='form-control' required='1' min='1' max='{$ir['secondary_currency']}'>
					</td>
				</tr>
				<tr>
					<th>
						{$lang['SMARKET_TH']}
					</th>
					<td>
						<input type='number' name='cost' class='form-control' required='1' min='1' value='200'>
					</td>
				<tr>
				
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-primary' value='{$lang['SMARKET_BTN']}'>
					</td>
				</tr>
			</table>
		</form>";
	}
}
$h->endpage();