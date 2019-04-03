<?php
require('globals.php');
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
	alert('danger',"Uh Oh!","You cannot visit the bank while in the infirmary or dungeon.",true,'index.php');
	die($h->endpage());
}
if ($ir['secondary_currency'] == 0)
{
    alert('danger',"Uh Oh!","You don not have any Chivalry Tokens to deposit.",true,'tokenbank.php');
    die($h->endpage());
}
else
{
	$gain = $ir['secondary_currency'];
	$ir['tokenbank'] += $gain;
	//Update user's bank and Copper Coins info.
	$api->UserTakeCurrency($userid, 'secondary', $gain);
	$api->UserInfoSetStatic($userid, "tokenbank", $ir['tokenbank']);
	alert('success', "Success!", "You hand over " . number_format($gain) . " Chivalry Tokens to be
	deposited. You now have " . number_format($ir['tokenbank']) . " in your account.", true, 'tokenbank.php');
	//Log bank transaction.
	$api->SystemLogsAdd($userid, 'tokenbank', "Deposited " . number_format($gain) . " Chivalry tokens.");
	$h->endpage();
}