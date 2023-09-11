<?php
/*
	File:		tokenbank.php
	Created: 	10/18/2017 at 1:26PM Eastern Time
	Info: 		The game bank players can store their tokens
	Author:		TheMasterGeneral
	Website: 	http://chivalryisdead.x10.mx/
*/
require("globals.php");
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
	alert('danger',"Uh Oh!","You cannot visit the Chivalry Token Bank while in the infirmary or dungeon.",true,'index.php');
	die($h->endpage());
}
echo "<h3><i class='game-icon game-icon-chest'></i> Token Bank</h3>";

if ($ir['tokenbank'] == -1) 
{
    if (isset($_GET['buy'])) 
	{
        //Player has the Copper Coins required to buy an account.
        if ($api->UserHasCurrency($userid,'secondary',100))
        {
            alert('success', "Success!", "You have successfully bought a Token Bank account for 100 Chivalry Tokens", true, 'tokenbank.php');
            $api->UserTakeCurrency($userid, 'secondary', 100);
            $api->UserInfoSet($userid, "tokenbank", 0);
            $api->SystemLogsAdd($userid, 'tokenbank', 'Purchased token bank account');
			item_add($userid,156,1);
        } 
		else 
		{
            alert('danger', "Uh oh!", "You are too poor to afford a Token Bank account. You need at least 100 Chivalry Tokens.", true, 'bank.php');
        }
    } 
	else 
	{
        echo "Do you wish to buy a Chivalry Token bank account? It'll cost you 100 Chivalry Tokens!<br />
            <a href='?buy'>Yes, please!</a>";
    }
	die($h->endpage());
}
echo "<b>You currently have <span id='bankacc2'>" . shortNumberParse($ir['tokenbank']) . "</span> Chivalry Tokens in your Chivalry Token Bank Account.</b><br />
<div id='banksuccess'></div>
<div class='row'>
	<div class='col-lg'>
		<div class='card'>
			<div class='card-header'>
				Deposit (<span id='wallet'>" . shortNumberParse($ir['secondary_currency']) . " Chivalry Tokens</span>)
			</div>
			<div class='card-body'>
				<form method='post' id='tokenBankDeposit' name='tokenBankDeposit'>
					<div class='row'>
						<div class='col'>
							<input type='number' min='1' max='{$ir['secondary_currency']}' class='form-control' id='form_bank_wallet' required='1' name='deposit' value='{$ir['secondary_currency']}'>
						</div>
						<div class='col-5 col-sm-4 col-md-3'>
							<input type='submit' value='Deposit' class='btn btn-primary' id='tokenDeposit'>
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
				Withdraw (<span id='bankacc'>" . shortNumberParse($ir['tokenbank']) . " Chivalry Tokens</span>)
			</div>
			<div class='card-body'>
				<form method='post' id='tokenBankWithdraw' name='tokenBankWithdraw'>
					<div class='row'>
						<div class='col'>
							<input type='number' min='1' max='{$ir['tokenbank']}' class='form-control' required='1' id='form_bank_acc' name='withdraw' value='{$ir['tokenbank']}'>
						</div>
						<div class='col-6 col-sm-4 col-md-3'>
							<input type='submit' value='Withdraw' class='btn btn-primary' id='tokenWithdraw'>
						</div>
					</div>
				</form>
			</div>
		</div>
		<br />
	</div>
</div>";
$h->endpage();