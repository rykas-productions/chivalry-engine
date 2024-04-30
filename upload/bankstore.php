<?php
require("globals.php");
$townName = $api->SystemTownIDtoName($ir['location']);
$townLvl = $db->fetch_single($db->query("SELECT `town_min_level` FROM `town` WHERE `town_id` = {$ir['location']}"));
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
    alert('danger',"Uh Oh!","You cannot visit the bank while in the infirmary or dungeon.",true,'explore.php');
    die($h->endpage());
}
$bank_max = 8750250 * $townLvl;
$bank_cost = round($bank_max / 8);
$bankAccount = getCurrentUserPref("storageAcc{$ir['location']}", -1);
echo "<h3>{$townName} Storage Bank</h3>";
//User has purchased a bank account.
if ($bankAccount > -1) {
    if (!isset($_GET['action'])) 
    {
        $_GET['action'] = '';
    }
    switch ($_GET['action']) 
    {
        default:
            index();
            break;
    }
} //User needs to purchase bank account.
else 
{
    if (isset($_GET['buy'])) 
    {
        //Player has the Copper Coins required to buy an account.
        if ($ir['primary_currency'] >= $bank_cost) 
        {
            
            alert('success', "Success!", "You have successfully bought a storage account in {$townName} for " . shortNumberParse($bank_cost) . " Copper Coins!", true, 'bankstore.php');
            $api->UserTakeCurrency($userid, 'primary', $bank_cost);
            setCurrentUserPref("storageAcc{$ir['location']}", 0);
            $api->SystemLogsAdd($userid, 'bank', "[{$townName} Storage] Purchased account for " . shortNumberParse($bank_cost) . " Copper Coins.");
            addToEconomyLog('Bank Fees', 'copper', ($bank_cost)*-1);
            item_add($userid,157,1);
        } //Player is too poor to afford account.
        else 
        {
            alert('danger', "Uh oh!", "You do not have enough Copper Coins to buy a bank account. You need at least
                " . shortNumberParse($bank_cost) . " Copper Coins. You're only holding " . shortNumberParse($ir['primary_currency']) . " Copper Coins.", true, 'bankstore.php');
        }
    } 
    else 
    {
        echo "Do you wish to buy a storage account in {$townName}? It'll cost you " . shortNumberParse($bank_cost) . " Copper Coins.<br />
            <a href='?buy'>Yes, please!</a>";
    }
}
function index()
{
    global $ir, $bankAccount, $bank_cost, $userid, $townName, $db, $api, $bank_max;
    if (isset($_POST['deposit']))
    {
        $deposit = abs((int) $_POST['deposit']);
        if ($deposit > $ir['primary_currency'])
        {
            alert('danger', "", "You are trying to deposit more Copper Coins than you currently have.", false);
        }
        elseif (($deposit + $bankAccount) > $bank_max)
        {
            alert('danger', "", "The {$townName} Storage can only hold a maximum of " . shortNumberParse($bank_max) . " Copper Coins. Your deposit of " . shortNumberParse($deposit) . " Copper Coins puts you over this limit.", false);
        }
        else
        {
            $gain = $deposit;
            $bankAccount += $gain;
            $ir['primary_currency'] -= $deposit;
            $api->UserTakeCurrency($userid, 'primary', $deposit);
            setCurrentUserPref("storageAcc{$ir['location']}", $bankAccount);
            //Log bank transaction.
            $api->SystemLogsAdd($userid, 'bank', "[{$townName} Storage] Deposited " . shortNumberParse($deposit) . " Copper Coins.");
            alert('success', "", "You hand over " . shortNumberParse($deposit) . " Copper Coins to be deposited. You now have " . shortNumberParse($bankAccount) . " Copper Coins in your {$townName} Storage Account.", false);
            $dojs=true;
        }
    }
    elseif (isset($_POST['withdraw']))
    {
        $withdraw = abs((int) $_POST['withdraw']);
        if ($withdraw > $bankAccount)
        {
            alert('danger', "", "You are trying to withdraw more copper than you currently have available in your account.", false);
        }
        else
        {
            $gain = $withdraw;
            $bankAccount -= $gain;
            $ir['primary_currency'] += $withdraw;
            $api->UserGiveCurrency($userid, 'primary', $withdraw);
            setCurrentUserPref("storageAcc{$ir['location']}", $bankAccount);
            $api->SystemLogsAdd($userid, 'bank', "[{$townName} Storage] Withdrew " . shortNumberParse($withdraw) . " Copper Coins.");
            alert('success', "", "You have successfully withdrew " . shortNumberParse($withdraw) . " Copper Coins from your
		    account. You have now have " . shortNumberParse($bankAccount) . " Copper Coins left in your {$townName} Storage Account.", false);
            $dojs=true;
        }
    }
            alert('info',"","You currently have " . shortNumberParse($bankAccount) . " Copper Coins in your {$townName} Storage account. The maximum stored 
                    in this account is " . shortNumberParse($bank_max) . " Copper Coins",false);
            echo "
				<div class='row'>
					<div class='col-lg'>
						<div class='card'>
							<div class='card-header'>
								Deposit (" . number_format($ir['primary_currency']) . " Copper Coins)
							</div>
							<div class='card-body'>
								<form method='post'>
									<div class='row'>
										<div class='col-12 col-sm-6 col-md-8'>
											<input type='number' min='1' max='{$ir['primary_currency']}' class='form-control' id='form_bank_wallet' required='1' name='deposit' value='{$ir['primary_currency']}'>
										    <br />
                                        </div>
										<div class='col-12 col-sm-6 col-md-4'>
											<input type='submit' value='Deposit' class='btn btn-primary btn-block'>
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
								Withdraw (" . number_format($bankAccount) . " Copper Coins)
							</div>
							<div class='card-body'>
								<form method='post'>
									<div class='row'>
										<div class='col-12 col-sm-6 col-md-8'>
											<input type='number' min='1' max='{$bankAccount}' class='form-control' required='1' id='form_bank_acc' name='withdraw' value='{$bankAccount}'><br />
										</div>
										<div class='col-12 col-sm-6 col-md-4'>
											<input type='submit' value='Withdraw' class='btn btn-primary btn-block'>
										</div>
									</div>
								</form>
							</div>
						</div>
						<br />
					</div>
				</div>";
}