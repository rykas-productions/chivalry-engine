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
	global $db,$h,$userid,$api,$lang;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
	if (empty($_GET['id']))
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['SMARKET_ERR'],true,'secmarket.php');
		die($h->endpage());
	}
	$q=$db->query("SELECT * FROM `sec_market` WHERE `sec_id` = {$_GET['id']}");
	$r=$db->fetch_row();
	if ($r['sec_user'] == $userid)
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['SMARKET_ERR1'],true,'secmarket.php');
		die($h->endpage());
	}
	if (isset($_POST['currency']))
	{
		$_POST['currency'] = (isset($_POST['currency']) && is_numeric($_POST['currency'])) ? abs($_POST['currency']) : '';
		if (empty($_POST['currency']))
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['SMARKET_ERR']);
			die($h->endpage());
		}
	}
	else
	{
		alert('info',$lang['ERROR_INFO'],"{$lang['SMARKET_INFO']} {$r['sec_total']} {$lang['INDEX_SECCURR']} {$lang['GEN_FOR_S']} {$r['sec_cost']} {$lang['INDEX_PRIMCURR']} {$lang['SMARKET_INFO1']}",false);
		echo "<form method='post'>
			<input type='number' min='1' max='{$r['sec_total']}' name='currency' value='{$r['sec_total']}' class='form-control' required='1'><br />
			<input type='submit' class='btn btn-default' value='{$lang['SMARKET_BTN']}'>
		</form>";
	}
}
$h->endpage();