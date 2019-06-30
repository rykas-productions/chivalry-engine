<?php
/*
	File:		bank.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		The in-game bank, for players to store their primary currency in.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
	MIT License

	Copyright (c) 2019 TheMasterGeneral

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
*/
require("globals.php");
$bank_cost = $set['bank_cost'];
$bank_maxfee = $set['bank_maxfee'];
$bank_feepercent = $set['bankfee_percent'];
echo "<h3>Bank</h3>";
//User has purchased a bank account.
if ($ir['bank'] > -1) {
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
        //Player has the primary currency required to buy an account.
        if ($ir['primary_currency'] >= $bank_cost) {

            alert('success', "Success!", "You have successfully bought a bank account for " . number_format($bank_cost) . " " . constant("primary_currency") . ".", true, 'bank.php');
			$api->user->takeCurrency($userid, 'primary', $bank_cost);
            $api->UserInfoSet($userid, "bank", 0);
            $api->game->addLog($userid, 'bank', 'Purchased bank account');
        } //Player is too poor to afford account.
        else {
            alert('danger', "Uh oh!", "You do not have enough cash to buy a bank account. You need at least
                " . number_format($bank_cost) . " " . constant("primary_currency") . ".", true, 'bank.php');
        }
    } else {
        echo "Do you wish to buy a bank account? It'll cost you " . number_format($bank_cost) . " " . constant("primary_currency") . ".<br />
            <a href='bank.php?buy'>Yes, please!</a>";
    }
}
function index()
{
    global $ir, $bank_maxfee, $bank_feepercent;
    echo "<b>You current have " . number_format($ir['bank']) . " in your bank account.</b><br />
				At the end of each and everyday, your bank balance will increase by 2%. You must be active within the
				past 24 hours for this to effect you.<br />
				<div class='cotainer'>
                    <div class='row'>
						<div class='col-sm'>
							It'll cost you {$bank_feepercent}% of the money you deposit. (Max " . number_format($bank_maxfee) . ".)
							<form action='bank.php?action=deposit' method='post'>
								<b>Your Cash</b><br />
								<input type='number' min='1' max='{$ir['primary_currency']}' class='form-control' required='1' name='deposit' value='{$ir['primary_currency']}'><br />
								<input type='submit' value='Deposit' class='btn btn-primary'>
							</form>
						</div>
						<div class='col-sm'>
							It doesn't cost you anything to withdraw from your account.
							<form action='bank.php?action=withdraw' method='post'>
								<b>Your Bank Balance</b><br />
								<input type='number' min='1' max='{$ir['bank']}' class='form-control' required='1' name='withdraw' value='{$ir['bank']}'><br />
								<input type='submit' value='Withdraw' class='btn btn-primary'>
							</form>
						</div>
					</div>
				</div>";
}

function deposit()
{
    global $ir, $userid, $bank_maxfee, $bank_feepercent, $api;
	$deposit = filter_input(INPUT_POST, 'deposit', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    //User is trying to deposit more than they have.
    if ($deposit > $ir['primary_currency']) {
        alert('danger', "Uh Oh!", "You are trying to deposit more cash than you current have!", true, 'bank.php');
    } else {
        $fee = ceil($_POST['deposit'] * $bank_feepercent / 100);
        if ($fee > $bank_maxfee) {
            $fee = $bank_maxfee;
        }
        //$gain is amount put into account after the fee is taken.
        $gain = $deposit - $fee;
        $ir['bank'] += $gain;
        //Update user's bank and primary currency info.
		$api->user->takeCurrency($userid, 'primary', $deposit);
        $api->user->setInfoStatic($userid, "bank", $ir['bank']);
        alert('success', "Success!", "You hand over " . number_format($deposit) . " to be deposited. After the
		    fee (" . number_format($fee) . ") is taken from your deposit, " . number_format($gain) . " is added to your
		    bank account. You now have " . number_format($ir['bank']) . " in your account.", true, 'bank.php');
        //Log bank transaction.
        $api->game->addLog($userid, 'bank', "Deposited " . number_format($deposit) . ".");
    }
}

function withdraw()
{
    global $ir, $userid, $api;
	$withdraw = filter_input(INPUT_POST, 'withdraw', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    //User is trying to withdraw more than they have stored.
    if ($withdraw > $ir['bank']) {
        alert('danger', "Uh Oh!", "You are trying to withdraw more cash than you currently have available in your account.", true, 'bank.php');
    } else {
        $gain = $withdraw;
        $ir['bank'] -= $gain;
        //Update user's info.
		$api->user->giveCurrency($userid, 'primary', $withdraw);
        $api->user->setInfoStatic($userid, "bank", $ir['bank']);
        alert('success', "Success!", "You have successfully withdrew " . number_format($withdraw) . " from your
		    bank account. You have now have " . number_format($ir['bank']) . " left in your account.", true, 'bank.php');
        //Log transaction.
        $api->game->addLog($userid, 'bank', "Withdrew " . number_format($withdraw) . ".");
    }
}

$h->endpage();