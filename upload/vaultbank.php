<?php
/*
	File:		bank.php
	Created: 	4/4/2016 at 11:53PM Eastern Time
	Info: 		The game bank players can store their currency in for safety.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
if ($ir['level'] < 175)
{
    alert('danger',"Uh Oh!","You need to be at least level 175 to use the Vault Bank.",true,'explore.php');
    die($h->endpage());
}
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
	alert('danger',"Uh Oh!","You cannot visit the bank while in the infirmary or dungeon.",true,'index.php');
	die($h->endpage());
}
$bank_cost = 50000000;
$bank_maxfee = 5000000;
$bank_feepercent = 15;
echo "<h3><i class='game-icon game-icon-bank'></i> Vault Bank</h3>";
//User has purchased a bank account.
if ($ir['vaultbank'] > -1) {
    if (!isset($_GET['action'])) {
        $_GET['action'] = '';
    }
    switch ($_GET['action']) {
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
} //User needs to purchase bank account.
else {
    if (isset($_GET['buy'])) {
        //Player has the Copper Coins required to buy an account.
        if ($ir['primary_currency'] >= $bank_cost) {

            alert('success', "Success!", "You have successfully bought a vault bank account for " . number_format($bank_cost), true, 'vaultbank.php');
            $api->UserTakeCurrency($userid, 'primary', $bank_cost);
            $api->UserInfoSet($userid, "vaultbank", 0);
			item_add($userid,182,1);
        } //Player is too poor to afford account.
        else {
            alert('danger', "Uh oh!", "You do not have enough cash to buy a vault bank account. You need at least
                " . number_format($bank_cost), true, 'vaultbank.php');
        }
    } else {
        echo "Do you wish to buy a Vault Bank Account? It'll cost you " . number_format($bank_cost) . "!<br />
            <a href='vaultbank.php?buy'>Yes, please!</a>";
    }
}
function index()
{
    global $ir, $bank_maxfee, $bank_feepercent, $db, $userid;
    if ($ir['vip_days'] == 0)
        $interest=2;
    else
        $interest=5;
    echo "<b>You currently have " . number_format($ir['vaultbank']) . " Copper Coins in your Vault Bank Account.</b><br />
				At the end of each and everyday, your balance will increase by {$interest}%. You will not gain interest if 
				your balance is over 300,000,000 Copper Coins. You must be active within the past 24 hours for this to 
				effect you.<br />
				<table class='table table-bordered'>
					<tr>
						<td width='50%'>
							It'll cost you {$bank_feepercent}% of the money you deposit. (Max " . number_format($bank_maxfee) . ")
							<form action='?action=deposit' method='post'>
								<b>Copper Coins</b><br />
								<input type='number' min='1' max='{$ir['primary_currency']}' class='form-control' required='1' name='deposit' value='{$ir['primary_currency']}'><br />
								<input type='submit' value='Deposit' class='btn btn-primary'>
							</form>
						</td>
						<td>
							It doesn't cost you anything to withdraw from your account.
							<form action='?action=withdraw' method='post'>
								<b>Account Balance</b><br />
								<input type='number' min='1' max='{$ir['vaultbank']}' class='form-control' required='1' name='withdraw' value='{$ir['vaultbank']}'><br />
								<input type='submit' value='Withdraw' class='btn btn-primary'>
							</form>
						</td>
					</tr>
				</table>";
}

function deposit()
{
    global $ir, $userid, $bank_maxfee, $bank_feepercent, $api;
    $_POST['deposit'] = abs($_POST['deposit']);
    //User is trying to deposit more than they have.
    if ($_POST['deposit'] > $ir['primary_currency']) {
        alert('danger', "Uh Oh!", "You are trying to deposit more cash than you current have!", true, 'vaultbank.php');
    } else {
        $fee = ceil($_POST['deposit'] * $bank_feepercent / 100);
        if ($fee > $bank_maxfee) {
            $fee = $bank_maxfee;
        }
        //$gain is amount put into account after the fee is taken.
        $gain = $_POST['deposit'] - $fee;
        $ir['vaultbank'] += $gain;
        //Update user's bank and Copper Coins info.
        $api->UserTakeCurrency($userid, 'primary', $_POST['deposit']);
        $api->UserInfoSetStatic($userid, "vaultbank", $ir['vaultbank']);
        alert('success', "Success!", "You hand over " . number_format($_POST['deposit']) . " to be deposited. After the
		    fee (" . number_format($fee) . " Copper Coins) is taken from your deposit, " . number_format($gain) . " is added to your
		    bank account. You now have " . number_format($ir['vaultbank']) . " in your account.", true, 'vaultbank.php');
        //Log bank transaction.
        $api->SystemLogsAdd($userid, 'vaultbank', "Deposited " . number_format($_POST['deposit']) . ".");
    }
}

function withdraw()
{
    global $ir, $userid, $api;
    $_POST['withdraw'] = abs($_POST['withdraw']);
    //User is trying to withdraw more than they have stored.
    if ($_POST['withdraw'] > $ir['vaultbank']) {
        alert('danger', "Uh Oh!", "You are trying to withdraw more cash than you currently have available in your account.", true, 'bank.php');
    } else {
        $gain = $_POST['withdraw'];
        $ir['vaultbank'] -= $gain;
        //Update user's info.
        $api->UserGiveCurrency($userid, 'primary', $_POST['withdraw']);
        $api->UserInfoSetStatic($userid, "vaultbank", $ir['vaultbank']);
        alert('success', "Success!", "You have successfully withdrew " . number_format($_POST['withdraw']) . " from your
		    bank account. You have now have " . number_format($ir['vaultbank']) . " left in your account.", true, 'bank.php');
        //Log transaction.
        $api->SystemLogsAdd($userid, 'vaultbank', "Withdrew " . number_format($_POST['withdraw']) . ".");
    }
}
if ($ir['vip_days'] == 0)
    include('ads/ad_bank.php');
$h->endpage();