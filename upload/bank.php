<?php
$bank_cost = 50000;
$bank_maxfee = 3000;
$bank_feepercent = 15;
require("globals.php");
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
            
			alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['BANK_SUCCESS']} " . number_format($bank_cost) . "<br />
			<a href='bank.php'>{$lang['BANK_SUCCESS1']} </a>!");
            $db->query("UPDATE `users` SET `primary_currency` = `primary_currency` - {$bank_cost}, `bank` = 0 WHERE `userid` = {$userid}");
        }
        else
        {
            alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['BANK_FAIL']} " . number_format($bank_cost));
        }
    }
    else
    {
        echo "{$lang['BANK_BUY1']}" . number_format($bank_cost)
                . " {$lang['INDEX_PRIMCURR']}!<br />
<a href='bank.php?buy'>{$lang['BANK_BUYYES']}</a>";
    }
}
function index()
{
    global $lang,$ir,$bank_maxfee,$bank_feepercent;
    echo "<b>{$lang['BANK_HOME']}" . number_format($ir['bank'])
            . " {$lang['BANK_HOME1']}</b><br />
				{$lang['BANK_HOME2']}<br />
				<table class='table table-bordered'>
					<tr>
						<td width='50%'>
{$lang['BANK_DEPOSIT_WARNING']} {$bank_feepercent}% {$lang['BANK_DEPOSITE_WARNING1']} " . number_format($bank_maxfee) . ".
							<form action='bank.php?action=deposit' method='post'>
								<b>{$lang['BANK_AMOUNT']}</b><br />
								<input type='number' min='1' max='{$ir['primary_currency']}' class='form-control' required='1' name='deposit' value='{$ir['primary_currency']}'>
								<input type='submit' value='{$lang['BANK_DEPOSIT']}' class='btn btn-default'>
							</form>
						</td>
						<td>
							{$lang['BANK_WITHDRAW_WARNING']}
							<form action='bank.php?action=withdraw' method='post'>
								<b>{$lang['BANK_AMOUNT']}</b><br />
								<input type='number' min='1' max='{$ir['bank']}' class='form-control' required='1' name='withdraw' value='{$ir['bank']}'>
								<input type='submit' value='{$lang['BANK_WITHDRAW']}' class='btn btn-default'>
							</form>
						</td>
					</tr>
				</table>";
}
function deposit()
{
    global $db,$ir,$userid,$lang,$bank_maxfee,$bank_feepercent;
    $_POST['deposit'] = abs((int) $_POST['deposit']);
    if ($_POST['deposit'] > $ir['primary_currency'])
    {
        alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['BANK_D_ERROR']}");
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
        $db->query("UPDATE `users` SET `bank` = `bank` + {$gain}, `primary_currency` = `primary_currency` - {$_POST['deposit']} WHERE `userid` = {$userid}");
		alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['BANK_D_SUCCESS']} " . number_format($_POST['deposit']) . "{$lang['BANK_D_SUCCESS1']}" . number_format($fee) . "{$lang['BANK_D_SUCCESS2']} " . number_format($gain) . "{$lang['BANK_D_SUCCESS3']} " . number_format($ir['bank']) . "{$lang['BANK_D_SUCCESS4']}");
    }
}
$h->endpage();