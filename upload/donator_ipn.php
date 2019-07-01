<?php namespace Listener;
/*
	File:		donator_ipn.php
	Created: 	4/4/2016 at 11:57PM Eastern Time
	Info: 		Paypal Instant Payment Notification system. Allows for
				automated crediting of purchased donator items.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
$menuhide=1;
$percentoff=0.5;
require_once('globals_nonauth.php');
require('class/PaypalIPN.php');
$wantedcurrency = "USD";

use PaypalIPN;

$ipn = new PaypalIPN();
// Use the sandbox endpoint during testing.
//$ipn->useSandbox();
$verified = $ipn->verifyIPN();
if ($verified) {
    // assign posted variables to local variables
    $item_name = $_POST['item_name'];
    $item_number = $_POST['item_number'];
    $payment_status = $_POST['payment_status'];
    $payment_amount = $_POST['mc_gross'];
    $payment_currency = $_POST['mc_currency'];
    $txn_id = $_POST['txn_id'];
    $receiver_email = $_POST['receiver_email'];
    $payer_email = $_POST['payer_email'];
	$friendly_number = $_POST['mc_gross']-$_POST['mc_fee'];
	$qty=abs((int)$_POST['quantity']);
    //Parse the item name
    $packr = explode('|', $item_name);
    //Grab IDs
    $buyer = abs((int)$packr[3]);
	$payer = abs((int)$packr[4]);
    $for = $buyer;

    //Is payment completed?
    if ($payment_status != "Completed") {
        $api->SystemLogsAdd($buyer, 'donate', "Attempted to donate, but their payment status was not complete.");
        exit;
    }

    //Check to see if transaction has already been processed.
    $dp_check = $db->query("/*qc=on*/SELECT COUNT(`vipID`) FROM `vips_accepted` WHERE `vipTXN` = '{$txn_id}'");
    if ($db->fetch_single($dp_check) > 0) {
        $db->free_result($dp_check);
        $api->SystemLogsAdd($buyer, 'donate', "Attempted to donate, but their transaction ID, {$txn_id}, was already processed.");
        exit;
    }
    //Check to see if the receiver of the cash is the email set in the settings.
    if ($receiver_email != $set['PaypalEmail']) {
        $api->SystemLogsAdd($buyer, 'donate', "Attempted to donate, but sent their cash to {$receiver_email}.");
        exit;
    }
    //Check if the donator gave you the right currency.
    if ($payment_currency != $wantedcurrency) {
        $api->SystemLogsAdd($buyer, 'donate', "Attempted to donate, but sent their cash in {$payment_currency}, not {$wantedcurrency}.");
        exit;
    }
    //Check to see if the donation is for the right game.
    if (str_replace("www.", "", $packr[0]) != str_replace("www.", "", $_SERVER['HTTP_HOST'])) {
        $api->SystemLogsAdd($buyer, 'donate', "Attempted to donate, but sent their donation to {$packr[0]}.");
        exit;
    }
    //Check to see if they're donating for a VIP package of sorts.
    if ($packr[1] != "VIP") {
        $api->SystemLogsAdd($buyer, 'donate', "Attempted to donate, but sent their donation was not for a VIP Pack.");
        exit;
    }
    //Pack ID to fetch from DB
    $pack = abs((int)$packr[2]);
    $pi = $db->query("/*qc=on*/SELECT * FROM `vip_listing` WHERE `vip_id` = {$pack}");
    //Check if pack is real.
    if ($db->num_rows($pi) == 0) {
        $api->SystemLogsAdd($buyer, 'donate', "Attempted to donate, but attempted to buy a non-existent pack, (Pack # {$packr[2]} .");
        exit;
    }
    $fpi = $db->fetch_row($pi);
    //Make sure the user paid the correct amount.
	$adjcost=($fpi['vip_cost']*$qty)*$percentoff;
    if ($adjcost != $payment_amount) {
        $api->SystemLogsAdd($buyer, 'donate', "Attempted to donate for {$qty} x VIP pack #{$packr[2]}(s), but only paid \${$payment_amount}. (Pack Costs \${$adjcost})");
        exit;
    }
	$vipticketgiven=floor($adjcost);
    //Everything checks out... so lets credit the pack.
	$fpi['vip_qty']=$fpi['vip_qty']*$qty;
    item_add($for, $fpi['vip_item'], $fpi['vip_qty']);
    item_add($for, 128, 1);
    item_add($payer, 89, $vipticketgiven);
    //Log everything
	$db->query("UPDATE `settings` SET `setting_value` = `setting_value` + {$friendly_number} WHERE `setting_name` = 'MonthlyDonationGoal'");
    $db->query("INSERT INTO `vips_accepted` VALUES(NULL, {$buyer}, {$for}, {$pack}, " . time() . ", '{$txn_id}')");
	//$api->UserGiveItem($buyer,89,$vipticketgiven);
    $api->SystemLogsAdd($payer, 'donate', "{$payer_email} donated \${$payment_amount} for {$fpi['vip_qty']} x " . $api->SystemItemIDtoName($fpi['vip_item']) . ".");
	$mailtext="{$api->SystemUserIDtoName($payer)} [{$payer}] spent \${$payment_amount} for {$fpi['vip_qty']} x " . $api->SystemItemIDtoName($fpi['vip_item']) . "(s) and sent them to you! You should go thank them!";
	$api->GameAddNotification($payer,"Your \${$payment_amount} donation for your {$fpi['vip_qty']} x " . $api->SystemItemIDtoName($fpi['vip_item']) . " item has been successfully credited to {$api->SystemUserIDtoName($for)} [{$for}].");
	if ($payer != $for)
		$api->GameAddMail($for,"Chivalry is Dead Donation",$mailtext,1);
}
// Reply with an empty 200 response to indicate to PayPal the IPN was received correctly.
header("HTTP/1.1 200 OK");
