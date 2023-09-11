<?php
/*
	File:		bank.php
	Created: 	4/4/2016 at 11:53PM Eastern Time
	Info: 		The game bank players can store their currency in for safety.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
$moduleID = "national_bank";
require("globals.php");
function initializeModule()
{
    global $moduleID;
    if (!readConfigFromDB($moduleID))
    {
        $moduleConfigArray=array(
            'moduleID' => $moduleID,
            'moduleAuthor' => 'TheMasterGeneral',
            'moduleURL' => 'https://github.com/rykas-productions/chivalry-engine',
            'moduleVersion' => 1,
            'bankOpeningFee' => 10000000,
            'bankWithdrawPercent' => 250000,
            'bankWithdrawMaxFee' => 15,
            'bankLevelRequirement' => 75
        );
        $defaultConfig = formatConfig($moduleConfigArray);
        writeConfigToDB($moduleID, $defaultConfig);
        echo "Installing default config...";
        header("bigbank.php");
    }
}
if ($ir['level'] < $moduleConfig['bankLevelRequirement'])
{
    alert('danger',"Uh Oh!","You need to be at least level {$moduleConfig['bankLevelRequirement']} to use the Federal Bank.",true,'explore.php');
    die($h->endpage());
}
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
	alert('danger',"Uh Oh!","You cannot visit the bank while in the infirmary or dungeon.",true,'index.php');
	die($h->endpage());
}
$bank_cost = $moduleConfig['bankOpeningFee'];
$bank_maxfee = $moduleConfig['bankWithdrawMaxFee'];
$bank_feepercent = $moduleConfig['bankWithdrawPercent'];
echo "<h3><i class='game-icon game-icon-bank'></i> Federal Bank</h3>";
//User has purchased a bank account.
if ($ir['bigbank'] > -1) {
    if (!isset($_GET['action'])) {
        $_GET['action'] = '';
    }
    switch ($_GET['action']) {
        default:
            index();
            break;
    }
} //User needs to purchase bank account.
else {
    if (isset($_GET['buy'])) {
        //Player has the Copper Coins required to buy an account.
        if ($ir['primary_currency'] >= $bank_cost) {

            alert('success', "Success!", "You have successfully bought a federal bank account for " . shortNumberParse($bank_cost) . " Copper Coins!", true, 'bigbank.php');
            $api->UserTakeCurrency($userid, 'primary', $bank_cost);
            $api->UserInfoSet($userid, "bigbank", 0);
			item_add($userid,155,1);
			addToEconomyLog('Bank Fees', 'copper', ($bank_cost)*-1);
			$api->SystemLogsAdd($userid, 'bank', "[Federal] Purchased account for " . shortNumberParse($bank_cost) . " Copper Coins.");
        } //Player is too poor to afford account.
        else {
            alert('danger', "Uh oh!", "You do not have enough Copper Coins to buy a federal bank account. You need at least
                " . shortNumberParse($bank_cost) . " Copper Coins.", true, 'bigbank.php');
        }
    } else {
        echo "Do you wish to buy a Federal Bank Account? It'll cost you " . shortNumberParse($bank_cost) . " Copper Coins.<br />
            <a href='?buy'>Yes, please!</a>";
    }
}
function index()
{
    global $ir, $bank_maxfee, $bank_feepercent, $db, $userid;
    $interest = 5;
    $cutoff = 72;
    if ($ir['vip_days'] == 0)
    {
        $interest=2;
        $cutoff = 24;
    }
    echo "<b>You currently have <span id='bankacc2'>" . shortNumberParse($ir['bigbank']) . "</span> Copper Coins in your Federal Bank Account.</b><br />
				At the end of each and everyday, your balance will increase by {$interest}%. You will not gain interest if 
				your balance is over " . shortNumberParse(returnMaxInterest($userid)* 10) . " Copper Coins. You must be active within the past {$cutoff} hours for this to 
				effect you.<br />
				<div id='banksuccess'></div>
				<div class='row'>
					<div class='col-lg'>
						<div class='card'>
							<div class='card-header'>
								Deposit (<span id='wallet'>" . shortNumberParse($ir['primary_currency']) . " Copper Coins</span>)
							</div>
							<div class='card-body'>
								<form method='post' id='fedBankDeposit' name='fedBankDeposit'>
									<div class='row'>
										<div class='col-12 col-sm-6 col-md-8'>
											<input type='number' min='1' max='{$ir['primary_currency']}' class='form-control' id='form_bank_wallet' required='1' name='deposit' value='{$ir['primary_currency']}'>
										    <br />
                                        </div>
										<div class='col-12 col-sm-6 col-md-4'>
											<input type='submit' value='Deposit' class='btn btn-primary btn-block' id='fedDeposit'>
										</div>
    									<div class='col-12'>
    										<small><br />{$bank_feepercent}% fee, max " . shortNumberParse($bank_maxfee) . " Copper Coins</small>
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
								Withdraw (<span id='bankacc'>" . shortNumberParse($ir['bigbank']) . " Copper Coins</span>)
							</div>
							<div class='card-body'>
								<form method='post' id='fedBankWithdraw' name='fedBankWithdraw'>
									<div class='row'>
										<div class='col-12 col-sm-6 col-md-8'>
											<input type='number' min='1' max='{$ir['bigbank']}' class='form-control' required='1' id='form_bank_acc' name='withdraw' value='{$ir['bigbank']}'>
										    <br />
                                        </div>
										<div class='col-12 col-sm-6 col-md-4'>
											<input type='submit' value='Withdraw' class='btn btn-primary btn-block' id='fedWithdraw'>
										</div>
									</div>
								</form>
							</div>
						</div>
						<br />
					</div>
				</div>";
}
$h->endpage();