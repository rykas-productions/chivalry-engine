<?php
require('globals.php');
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
	alert('danger',"Uh Oh!","You cannot visit the bank while in the infirmary or dungeon.",true,'index.php');
	die($h->endpage());
}
if (($ir['equip_primary'] + $ir['equip_secondary']) == 0)
{
    alert('danger',"Uh Oh!","Due to a recent robbery, the City Bank requires that every customer come in with at least one weapon equipped.",true,'explore.php');
    die($h->endpage());
}
if ($ir['equip_armor'] == 0)
{
    alert('danger',"Uh Oh!","Due to a recent robbery, the City Bank requires that every customer come in with armor equipped.",true,'explore.php');
    die($h->endpage());
}
$bank_cost = $set['bank_cost'];
$bank_maxfee = $set['bank_maxfee'];
$bank_feepercent = $set['bankfee_percent'];
if ($ir['primary_currency'] == 0)
{
    alert('danger',"Uh Oh!","You don not have any Copper Coins to deposit.",true,'bank.php');
    die($h->endpage());
}
else
{
    $fee = ceil($ir['primary_currency'] * $bank_feepercent / 100);
    if ($fee > $bank_maxfee) {
        $fee = $bank_maxfee;
    }
    //$gain is amount put into account after the fee is taken.
    $gain = $ir['primary_currency'] - $fee;
    $ir['bank'] += $gain;
    //Update user's bank and Copper Coins info.
    $api->UserTakeCurrency($userid, 'primary', $ir['primary_currency']);
    $api->UserInfoSetStatic($userid, "bank", $ir['bank']);
    alert('success', "Success!", "You hand over " . number_format($ir['primary_currency']) . " to be deposited. After the
        fee (" . number_format($fee) . " Copper Coins) is taken from your deposit, " . number_format($gain) . " is added to your
        bank account. You now have " . number_format($ir['bank']) . " in your account.", true, 'bank.php');
    //Log bank transaction.
    $api->SystemLogsAdd($userid, 'bank', "Deposited " . number_format($ir['primary_currency']) . ".");
    $h->endpage();
}