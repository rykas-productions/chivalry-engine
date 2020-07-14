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

            alert('success', "Success!", "You have successfully bought a vault bank account for " . number_format($bank_cost) . " Copper Coins!", true, 'vaultbank.php');
            $api->UserTakeCurrency($userid, 'primary', $bank_cost);
            $api->UserInfoSet($userid, "vaultbank", 0);
			item_add($userid,182,1);
			addToEconomyLog('Bank Fees', 'copper', ($bank_cost)*-1);
			$api->SystemLogsAdd($userid, 'bank', "[Vault Bank] Purchased account for " . number_format($bank_cost) . " Copper Coins.");
        } //Player is too poor to afford account.
        else {
            alert('danger', "Uh oh!", "You do not have enough Copper Coins to buy a vault bank account. You need at least
                " . number_format($bank_cost) . " Copper Coins.", true, 'vaultbank.php');
        }
    } else {
        echo "Do you wish to buy a Vault Bank Account? It'll cost you " . number_format($bank_cost) . " Copper Coins.<br />
            <a href='?buy'>Yes, please!</a>";
    }
}
function index()
{
    global $ir, $bank_maxfee, $bank_feepercent, $db, $userid;
    if ($ir['vip_days'] == 0)
        $interest=2;
    else
        $interest=5;
    echo "<b>You currently have <span id='bankacc2'>" . number_format($ir['vaultbank']) . "</span> Copper Coins in your Vault Bank Account.</b><br />
				At the end of each and everyday, your balance will increase by {$interest}%. You will not gain interest if 
				your balance is over " . number_format(returnMaxInterest($userid) * 50) . " Copper Coins. You must be active within the past 24 hours for this to 
				effect you.<br />
				<div id='banksuccess'></div>
				<div class='row'>
					<div class='col-lg'>
						<div class='card'>
							<div class='card-header'>
								Deposit (<span id='wallet'>" . number_format($ir['primary_currency']) . " Copper Coins</span>)
							</div>
							<div class='card-body'>
								<form method='post' id='vaultBankDeposit' name='vaultBankDeposit'>
									<div class='row'>
										<div class='col'>
											<input type='number' min='1' max='{$ir['primary_currency']}' class='form-control' id='form_bank_wallet' required='1' name='deposit' value='{$ir['primary_currency']}'>
										</div>
										<div class='col-4 col-md-3'>
											<input type='submit' value='Deposit' class='btn btn-primary' id='vaultDeposit'>
										</div>
									</div>
									<div class='row'>
										<div class='col'>
											<small><br />{$bank_feepercent}% fee, max " . number_format($bank_maxfee) . " Copper Coins</small>
										</div>
									</div>
								</form>
							</div>
						</div>
						<br />
					</div>
					<div class='col-lg'>
						<div class='card'>
							<div class='card-header'>
								Withdraw (<span id='bankacc'>" . number_format($ir['vaultbank']) . " Copper Coins</span>)
							</div>
							<div class='card-body'>
								<form method='post' id='vaultBankWithdraw' name='vaultBankWithdraw'>
									<div class='row'>
										<div class='col'>
											<input type='number' min='1' max='{$ir['vaultbank']}' class='form-control' required='1' id='form_bank_acc' name='withdraw' value='{$ir['vaultbank']}'>
										</div>
										<div class='col-4 col-md-3'>
											<input type='submit' value='Withdraw' class='btn btn-primary' id='vaultWithdraw'>
										</div>
									</div>
								</form>
							</div>
						</div>
						<br />
					</div>
				</div>";
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
		addToEconomyLog('Bank Fees', 'copper', ($fee)*-1);
        //Update user's bank and Copper Coins info.
        $api->UserTakeCurrency($userid, 'primary', $_POST['deposit']);
        $api->UserInfoSetStatic($userid, "vaultbank", $ir['vaultbank']);
        alert('success', "Success!", "You hand over " . number_format($_POST['deposit']) . " to be deposited. After the
		    fee (" . number_format($fee) . " Copper Coins) is taken from your deposit, " . number_format($gain) . " is added to your
		    bank account. You now have " . number_format($ir['vaultbank']) . " in your account.", true, 'vaultbank.php');
        //Log bank transaction.
        $api->SystemLogsAdd($userid, 'bank', "[Vault Bank] Deposited " . number_format($_POST['deposit']) . " Copper Coins.");
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
        $api->SystemLogsAdd($userid, 'bank', "[Vault Bank] Withdrew " . number_format($_POST['withdraw']) . " Copper Coins.");
    }
}
$h->endpage();