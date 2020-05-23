<?php
/*
	File:		donatordone.php
	Created: 	4/4/2016 at 11:58PM Eastern Time
	Info: 		End page player is greeted with after accepting/declining Paypal charge.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require_once('globals.php');
//Action isn't specified.
if (!isset($_GET['action'])) {
    ob_get_clean();
    header('HTTP/1.1 400 Bad Request');
    exit;
}
//User cancels the donation.
if ($_GET['action'] == "cancel") {
    alert("success", "Success!", "You have chosen to not donate to {$set['WebsiteName']}. Maybe next time? :)");
} //User's donation is complete. Waiting on the IPN to kick in.
else if ($_GET['action'] == "done") {
	$msg="Dear {$ir['username']} [{$userid}]<br />
	We just wanted to let you know we've received your donation, and it should be automatically processed within 24-48 hours. If not, please contact CID Admin [1] in-game as soon as possible.<br />
	Your donation will be used to fund the game costs, which typically include hosting, domain costs, and advertising campaigns. We do appreciate it!!";
	$api->SystemSendEmail($ir['email'], $msg, "Your Chivalry is Dead Donation", $set['sending_email']);
	
    alert("success", "Thank you for Donating to {$set['WebsiteName']}", "We greatly appreciate your donation. Your pack will
        be credited to you in the next 24 hours. If not, please contact an admin so your order can be double checked.");
}
$h->endpage();
