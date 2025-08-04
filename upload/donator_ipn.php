<?php namespace Listener;
/*
	File:		donator_ipn.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows for instant-gifting of donated VIP Packs.
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
$menuhide=1;
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
    //Parse the item name
    $packr = explode('|', $item_name);
    //Grab IDs
    $buyer = abs((int)$packr[3]);
    $for = $buyer;

    //Is payment completed?
    if ($payment_status != "Completed") {
        $api->game->addLog($buyer, 'donate', "Attempted to donate, but their payment status was not complete.");
        exit;
    }

    //Check to see if transaction has already been processed.
    $dp_check = $db->query("SELECT COUNT(`vipID`) FROM `vips_accepted` WHERE `vipTXN` = '{$txn_id}'");
    if ($db->fetch_single($dp_check) > 0) {
        $db->free_result($dp_check);
        $api->game->addLog($buyer, 'donate', "Attempted to donate, but their transaction ID, {$txn_id}, was already processed.");
        exit;
    }
    //Check to see if the receiver of the cash is the email set in the settings.
    if ($receiver_email != $set['PaypalEmail']) {
        $api->game->addLog($buyer, 'donate', "Attempted to donate, but sent their cash to {$receiver_email}.");
        exit;
    }
    //Check if the donator gave you the right currency.
    if ($payment_currency != $wantedcurrency) {
        $api->game->addLog($buyer, 'donate', "Attempted to donate, but sent their cash in {$payment_currency}, not {$wantedcurrency}.");
        exit;
    }
    //Check to see if the donation is for the right game.
    if (str_replace("www.", "", $packr[0]) != str_replace("www.", "", $_SERVER['HTTP_HOST'])) {
        $api->game->addLog($buyer, 'donate', "Attempted to donate, but sent their donation to {$packr[0]}.");
        exit;
    }
    //Check to see if they're donating for a VIP package of sorts.
    if ($packr[1] != "VIP") {
        $api->game->addLog($buyer, 'donate', "Attempted to donate, but sent their donation was not for a VIP Pack.");
        exit;
    }
    //Pack ID to fetch from DB
    $pack = abs((int)$packr[2]);
    $pi = $db->query("SELECT * FROM `vip_listing` WHERE `vip_id` = {$pack}");
    //Check if pack is real.
    if ($db->num_rows($pi) == 0) {
        $api->game->addLog($buyer, 'donate', "Attempted to donate, but attempted to buy a non-existent pack, (Pack # {$packr[2]} .");
        exit;
    }
    $fpi = $db->fetch_row($pi);
    //Make sure the user paid the correct amount.
    if ($fpi['vip_cost'] != $payment_amount) {
        $api->game->addLog($buyer, 'donate', "Attempted to donate for VIP pack #{$packr[2]}, but only paid \${$payment_amount}. (Pack Costs \${$fpi['vip_cost']})");
        exit;
    }
    //Everything checks out... so lets credit the pack.
    addItem($for, $fpi['vip_item'], $fpi['vip_qty']);
    //Log everything
    $db->query("INSERT INTO `vips_accepted` VALUES(NULL, {$buyer}, {$for}, {$pack}, " . time() . ", '{$txn_id}')");
    $api->game->addLog($buyer, 'donate', "{$payer_email} donated \${$payment_amount} for VIP Pack #{$packr[2]}.");
    $api->user->addNotification($for, "Your \${$payment_amount} donation for your " . $api->game->getItemNameFromID($fpi['vip_item']) . " item has been successfully credited to you.");
}
// Reply with an empty 200 response to indicate to PayPal the IPN was received correctly.
header("HTTP/1.1 200 OK");
