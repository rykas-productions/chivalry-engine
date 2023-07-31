<?php
require('globals.php');
$bank_cost = $set['bank_cost'];
$bank_maxfee = $set['bank_maxfee'];
$bank_feepercent = $set['bankfee_percent'];
if ($ir['bank'] == -1)
{
	alert('danger',"Uh Oh!","Please purchase a City Bank account before using this feature.",true,'explore.php');
    die($h->endpage());
}
if ($ir['primary_currency'] == 0)
{
    alert('danger',"Uh Oh!","You do not have any Copper Coins to deposit.",true,'bank.php');
    die($h->endpage());
}
else
{
    //$gain is amount put into account after the fee is taken.
	$gain = $ir['primary_currency'];
	$ir['bank'] += $gain;
	//Update user's bank and Copper Coins info.
	$api->UserTakeCurrency($userid, 'primary', $ir['primary_currency']);
	$api->UserInfoSetStatic($userid, "bank", $ir['bank']);
	alert('success', "Success!", "You hand over " . shortNumberParse($ir['primary_currency']) . " Copper Coins to be deposited. You now have " . shortNumberParse($ir['bank']) . " Copper Coins 
	in your City Bank.", true, 'bank.php');
	//Log bank transaction.
	$api->SystemLogsAdd($userid, 'bank', "[City Bank] Deposited " . shortNumberParse($ir['primary_currency']) . " Copper Coins.");
	 die($h->endpage());
}