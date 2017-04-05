<?php
/*
	File:		donator_ipn.php
	Created: 	4/4/2016 at 11:57PM Eastern Time
	Info: 		Paypal Instant Payment Notification system. Allows for
				automated crediting of purchased donator items.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require_once('globals_nonauth.php');

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value)
{
    $value = urlencode(stripslashes($value));
    $req .= "&$key=$value";
}

// post back to PayPal system to validate
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen('www.paypal.com', 80, $errno, $errstr, 30);

// assign posted variables to local variables
$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$payment_amount = sprintf("%0.2f",$_POST['mc_gross']);
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];

if (!$fp)
{
    // HTTP ERROR
}
else
{
    fputs($fp, $header . $req);
    while (!feof($fp))
    {
        $res = fgets($fp, 1024);
        if (strcmp($res, "VERIFIED") == 0)
        {
            $txn_db = $db->escape(stripslashes($txn_id));
            // check the payment_status is Completed
            if ($payment_status != "Completed")
            {
                fclose($fp);
                die("");
            }
            $dp_check =
                    $db->query(
                            "SELECT COUNT(`vipID`)
                             FROM `vips_accepted`
                             WHERE `vipTXN` = '{$txn_db}'");
            if ($db->fetch_single($dp_check) > 0)
            {
                $db->free_result($dp_check);
                fclose($fp);
                die("");
            }
            $db->free_result($dp_check);
            // check that txn_id has not been previously processed
            // check that receiver_email is your Primary PayPal email
            if ($receiver_email != $set['paypal'])
            {
                fclose($fp);
                die("");
            }
            // check that payment_amount/payment_currency are correct
            if ($payment_currency != "USD")
            {
                fclose($fp);
                die("");
            }
            // parse for pack
            $packr = explode('|', $item_name);
            if (str_replace("www.", "", $packr[0])
                    != str_replace("www.", "", $_SERVER['HTTP_HOST']))
            {
                fclose($fp);
                die("");
            }
            if ($packr[1] != "VIP")
            {
                fclose($fp);
                die("");
            }
            $pack = $packr[2];
			$packq=$db->escape($pack);
			$pi=$db->query("SELECT * FROM `vip_listing` WHERE `vip_id` = {$packq}");
			if ($db->num_rows($pi) == 0)
            {
                fclose($fp);
                die("");
            }
			$fpi=$db->fetch_row($pi);
            if ($fpi['vip_cost'] != $payment_amount)
            {
                fclose($fp);
                die("");
            }
            // grab IDs
            $buyer = abs((int) $packr[3]);
            $for = $buyer;
            // all seems to be in order, credit it.
            item_add($for,$fpi['vip_item'],1);
            // process payment
            notification_add($for, "Your \${$payment_amount} donation for an " . $api->SystemItemIDtoName($fpi['vip_item']) . " has been successfully credited to you.");
            $db->query("INSERT INTO `vips_accepted` VALUES(NULL, {$buyer}, {$for}, {$packq}, " . time() . ", '$txn_db')");
        }
        else if (strcmp($res, "INVALID") == 0)
        {
        }
    }

    fclose($fp);
}
