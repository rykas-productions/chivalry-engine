<?php
/*
	File:		bank.php
	Created: 	4/4/2016 at 11:53PM Eastern Time
	Info: 		The game bank players can store their currency in for safety.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
$bank_cost = $set['bank_cost'];
$bank_maxfee = $set['bank_maxfee'];
$bank_feepercent = $set['bank_feepercent'];
echo "<h3>Bank</h3>";
if ($ir['bank'] > -1)
{
    if (!isset($_GET['action']))
    {
        $_GET['action'] = '';
    }
    switch ($_GET['action'])
    {
    case "deposit":
        deposit();
        break;
    case "withdraw":
        withdraw();
        break;
    default:
        index();
        break;
    }
}
else
{
    if (isset($_GET['buy']))
    {
        if ($ir['primary_currency'] >= $bank_cost)
        {
            
			alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['BANK_SUCCESS']} " . number_format($bank_cost),true,'bank.php');
			$api->UserTakeCurrency($userid,'primary',$bank_cost);
			$api->UserInfoSet($userid,"bank",0);
        }
        else
        {
            alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['BANK_FAIL']} " . number_format($bank_cost),true,'bank.php');
        }
    }
    else
    {
        echo "{$lang['BANK_BUY1']}" . number_format($bank_cost) . " {$lang['INDEX_PRIMCURR']}!<br /> <a href='bank.php?buy'>{$lang['BANK_BUYYES']}</a>";
		$api->SystemLogsAdd($userid,'bank','Purchased bank account');
	}
}
function index()
{
    global $lang,$ir,$bank_maxfee,$bank_feepercent,$api;
    echo "<b>{$lang['BANK_HOME']}" . number_format($ir['bank'])
            . " {$lang['BANK_HOME1']}</b><br />
				{$lang['BANK_HOME2']}<br />
				<table class='table table-bordered'>
					<tr>
						<td width='50%'>
							{$lang['BANK_DEPOSIT_WARNING']} 
							{$bank_feepercent}% 
							{$lang['BANK_DEPOSITE_WARNING1']} " . number_format($bank_maxfee) . ".
							<form action='bank.php?action=deposit' method='post'>
								<b>{$lang['BANK_AMOUNT']}</b><br />
								<input type='number' min='1' max='{$ir['primary_currency']}' class='form-control' required='1' name='deposit' value='{$ir['primary_currency']}'>
								<input type='submit' value='{$lang['BANK_DEPOSIT']}' class='btn btn-secondary'>
							</form>
						</td>
						<td>
							{$lang['BANK_WITHDRAW_WARNING']}
							<form action='bank.php?action=withdraw' method='post'>
								<b>{$lang['BANK_AMOUNT']}</b><br />
								<input type='number' min='1' max='{$ir['bank']}' class='form-control' required='1' name='withdraw' value='{$ir['bank']}'>
								<input type='submit' value='{$lang['BANK_WITHDRAW']}' class='btn btn-secondary'>
							</form>
						</td>
					</tr>
				</table>";
}
function deposit()
{
    global $db,$ir,$userid,$lang,$bank_maxfee,$bank_feepercent,$api;
    $_POST['deposit'] = abs($_POST['deposit']);
    if ($_POST['deposit'] > $ir['primary_currency'])
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['BANK_D_ERROR'],true,'bank.php');
    }
    else
    {
        $fee = ceil($_POST['deposit'] * $bank_feepercent / 100);
        if ($fee > $bank_maxfee)
        {
            $fee = $bank_maxfee;
        }
        $gain = $_POST['deposit'] - $fee;
        $ir['bank'] += $gain;
		$api->UserTakeCurrency($userid,'primary',$_POST['deposit']);
		$api->UserInfoSetStatic($userid,"bank",$ir['bank']);
		alert('success',$lang['ERROR_SUCCESS'],"{$lang['BANK_D_SUCCESS']} " . number_format($_POST['deposit']) . "{$lang['BANK_D_SUCCESS1']}" . number_format($fee) . "{$lang['BANK_D_SUCCESS2']} " . number_format($gain) . "{$lang['BANK_D_SUCCESS3']} " . number_format($ir['bank']) . "{$lang['BANK_D_SUCCESS4']}",true,'bank.php');
		$api->SystemLogsAdd($userid,'bank',"Deposited " . number_format($_POST['deposit']) . " Primary Currency.");
	}
}
function withdraw()
{
	global $db, $ir, $lang, $userid, $h, $api;
	$_POST['withdraw'] = abs($_POST['withdraw']);
	if ($_POST['withdraw'] > $ir['bank'])
    {
		alert('danger',$lang['ERROR_GENERIC'],$lang['BANK_W_FAIL'],true,'bank.php');
    }
	else
	{
		$gain = $_POST['withdraw'];
		$ir['bank'] -= $gain;
		$api->UserGiveCurrency($userid,'primary',$_POST['withdraw']);
		$api->UserInfoSetStatic($userid,"bank",$ir['bank']);
		alert('success',$lang['ERROR_SUCCESS'],"{$lang['BANK_W_SUCCESS']} " . number_format($_POST['withdraw']) . " {$lang['INDEX_PRIMCURR']} {$lang['BANK_W_SUCCESS1']} " . $ir['bank'] . " {$lang['INDEX_PRIMCURR']} {$lang['BANK_W_SUCCESS2']}",true,'bank.php');
		$api->SystemLogsAdd($userid,'bank',"Withdrew " . number_format($_POST['withdraw']) . " Primary Currency.");
	}
}
$h->endpage();