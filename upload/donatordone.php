<?php
/*
	File:		donatordone.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		End screen when a player completes their donation transaction.
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
    if (!$_POST['txn_id']) {
        die($h->endpage());
    }
    alert("success", "Thank you for Donating to {$set['WebsiteName']}", "We greatly appreciate your donation. Your pack will
        be credited to you in the next 24 hours. If not, please contact an admin so your order can be double checked.");
}
$h->endpage();
