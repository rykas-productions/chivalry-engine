<?php
/*
	File:		bank.php
	Created: 	4/4/2016 at 11:53PM Eastern Time
	Info: 		The game bank players can store their currency in for safety.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
	alert('danger',"Uh Oh!","You cannot visit the bank while in the infirmary or dungeon.",true,'explore.php');
	die($h->endpage());
}
$bank_cost = $set['bank_cost'];
$bank_maxfee = $set['bank_maxfee'];
$bank_feepercent = $set['bankfee_percent'];
echo "<h3><i class='game-icon game-icon-bank'></i> City Bank</h3>";
//User has purchased a bank account.
if ($ir['bank'] > -1) {
    if (!isset($_GET['action'])) {
        $_GET['action'] = '';
    }
    switch ($_GET['action']) {
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

            alert('success', "Success!", "You have successfully bought a bank account for " . number_format($bank_cost) . " Copper Coins!", true, 'bank.php');
            $api->UserTakeCurrency($userid, 'primary', $bank_cost);
            $api->UserInfoSet($userid, "bank", 0);
			$api->SystemLogsAdd($userid, 'bank', "[City Bank] Purchased account for " . number_format($bank_cost) . " Copper Coins.");
			addToEconomyLog('Bank Fees', 'copper', ($bank_cost)*-1);
			item_add($userid,157,1);
        } //Player is too poor to afford account.
        else {
            alert('danger', "Uh oh!", "You do not have enough Copper Coins to buy a bank account. You need at least
                " . number_format($bank_cost) . " Copper Coins.", true, 'bank.php');
        }
    } else {
        echo "Do you wish to buy a bank account? It'll cost you " . number_format($bank_cost) . " Copper Coins.<br />
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
    echo "<b>You currently have <span id='bankacc2'>" . number_format($ir['bank']) . "</span> in your City Bank account.</b><br />
				At the end of each and everyday, your balance will increase by {$interest}%. You will not gain interest if 
				your balance is over " . number_format(returnMaxInterest($userid)) . " Copper Coins. You must be active within the past 24 hours for this to 
				effect you.
				<div id='banksuccess'></div>
				<div class='row'>
					<div class='col-lg'>
						<div class='card'>
							<div class='card-header'>
								Deposit (<span id='wallet'>" . number_format($ir['primary_currency']) . " Copper Coins</span>)
							</div>
							<div class='card-body'>
								<form method='post' id='cityBankDeposit' name='cityBankDeposit'>
									<div class='row'>
										<div class='col'>
											<input type='number' min='1' max='{$ir['primary_currency']}' class='form-control' id='form_bank_wallet' required='1' name='deposit' value='{$ir['primary_currency']}'>
										</div>
										<div class='col-5 col-sm-4 col-md-3'>
											<input type='submit' value='Deposit' class='btn btn-primary' id='cityDeposit'>
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
								Withdraw (<span id='bankacc'>" . number_format($ir['bank']) . " Copper Coins</span>)
							</div>
							<div class='card-body'>
								<form method='post' id='cityBankWithdraw' name='cityBankWithdraw'>
									<div class='row'>
										<div class='col'>
											<input type='number' min='1' max='{$ir['bank']}' class='form-control' required='1' id='form_bank_acc' name='withdraw' value='{$ir['bank']}'>
										</div>
										<div class='col-6 col-sm-4 col-md-3'>
											<input type='submit' value='Withdraw' class='btn btn-primary' id='cityWithdraw'>
										</div>
									</div>
								</form>
							</div>
						</div>
						<br />
					</div>
				</div>";
	$q=$db->query("/*qc=on*/SELECT * FROM `bank_investments` WHERE `userid` = {$userid}");
	$calculatedMax = ceil(1 * levelMultiplier($ir['level']));
	while ($r = $db->fetch_row($q))
	{
		echo "
		<div class='row'>
			<div class='col-lg'>
				<div class='card'>
					<div class='card-body'>
						<div class='row'>
							<div class='col-8 col-sm-6 col-md-4'>
								" . number_format($r['amount']) . " Copper Coins
							</div>
							<div class='col-4 col-sm-2'>
								{$r['interest']}%
							</div>
							<div class='col-12 col-sm-4 col-md-3'>
								{$r['days_left']} Days Left
							</div>
							<div class='col-12 col-md-3'>
								<a href='investment.php?terminate={$r['invest_id']}' class='btn btn-danger btn-block'>Terminate</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>";
	}
	if ($db->num_rows($q) < $calculatedMax)
	{
		echo "<hr /><a href='investment.php' class='btn btn-success btn-block'>Start Investment</a>";
	}
}
$h->endpage();