<?php
/*
	File:		tokenbank.php
	Created: 	10/18/2017 at 1:26PM Eastern Time
	Info: 		The game bank players can store their tokens
	Author:		TheMasterGeneral
	Website: 	http://chivalryisdead.x10.mx/
*/
require("globals.php");
echo "<h3><i class='game-icon game-icon-chest'></i> Token Bank</h3>";
//User has purchased a bank account.
if ($ir['tokenbank'] > -1) {
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
        if ($api->UserHasCurrency($userid,'secondary',100))
        {
            alert('success', "Success!", "You have successfully bought a Token Bank account for 100 Chivalry Tokens", true, 'tokenbank.php');
            $api->UserTakeCurrency($userid, 'secondary', 100);
            $api->UserInfoSet($userid, "tokenbank", 0);
            $api->SystemLogsAdd($userid, 'tokenbank', 'Purchased token bank account');
        } else {
            alert('danger', "Uh oh!", "You are too poor to afford a Token Bank account. You need at least 100 Chivalry Tokens.", true, 'bank.php');
        }
    } else {
        echo "Do you wish to buy a Chivalry Token bank account? It'll cost you 100 Chivalry Tokens!<br />
            <a href='?buy'>Yes, please!</a>";
    }
}
function index()
{
    global $ir;
    echo "<b>You currently have " . number_format($ir['tokenbank']) . " Chivalry Tokens in your bank account.</b><br />
				<table class='table table-bordered'>
					<tr>
						<td width='50%'>
							<form action='?action=deposit' method='post'>
								<b>Your Tokens</b><br />
								<input type='number' min='1' max='{$ir['secondary_currency']}' class='form-control' required='1' name='deposit' value='{$ir['secondary_currency']}'><br />
								<input type='submit' value='Deposit' class='btn btn-primary'>
							</form>
						</td>
						<td>
							<form action='?action=withdraw' method='post'>
								<b>Your Bank Balance</b><br />
								<input type='number' min='1' max='{$ir['tokenbank']}' class='form-control' required='1' name='withdraw' value='{$ir['tokenbank']}'><br />
								<input type='submit' value='Withdraw' class='btn btn-primary'>
							</form>
						</td>
					</tr>
				</table>";
}

function deposit()
{
    global $ir, $userid, $api;
    $_POST['deposit'] = abs($_POST['deposit']);
    //User is trying to deposit more than they have.
    if ($_POST['deposit'] > $ir['secondary_currency']) {
        alert('danger', "Uh Oh!", "You are trying to deposit more cash than you current have!", true, 'bank.php');
    } else {
        $gain = $_POST['deposit'];
        $ir['tokenbank'] += $gain;
        //Update user's bank and Copper Coins info.
        $api->UserTakeCurrency($userid, 'secondary', $_POST['deposit']);
        $api->UserInfoSetStatic($userid, "tokenbank", $ir['tokenbank']);
        alert('success', "Success!", "You hand over " . number_format($_POST['deposit']) . " Chivalry Tokens to be
        deposited. You now have " . number_format($ir['tokenbank']) . " in your account.", true, 'tokenbank.php');
        //Log bank transaction.
        $api->SystemLogsAdd($userid, 'tokenbank', "Deposited " . number_format($_POST['deposit']) . " Chivalry tokens.");
    }
}

function withdraw()
{
    global $ir, $userid, $api;
    $_POST['withdraw'] = abs($_POST['withdraw']);
    //User is trying to withdraw more than they have stored.
    if ($_POST['withdraw'] > $ir['tokenbank']) {
        alert('danger', "Uh Oh!", "You are trying to withdraw more cash than you currently have available in your account.", true, 'bank.php');
    } else {
        $gain = $_POST['withdraw'];
        $ir['tokenbank'] -= $gain;
        //Update user's info.
        $api->UserGiveCurrency($userid, 'secondary', $_POST['withdraw']);
        $api->UserInfoSetStatic($userid, "tokenbank", $ir['tokenbank']);
        alert('success', "Success!", "You have successfully withdrew " . number_format($_POST['withdraw']) . " Chivalry Tokens from your
		    bank account. You have now have " . number_format($ir['tokenbank']) . " left in your account.", true, 'bank.php');
        //Log transaction.
        $api->SystemLogsAdd($userid, 'tokenbank', "Withdrew " . number_format($_POST['withdraw']) . " Chivalry Tokens.");
    }
}

$h->endpage();