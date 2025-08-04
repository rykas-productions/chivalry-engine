<?php
/*
	File:		macro.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		ReCaptcha-based bot detection.
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
$page = stripslashes(strip_tags($_POST['page']));
//Check if the reCaptcha response has been received from the player.
if (isset($_POST['g-recaptcha-response'])) {
    $captcha = $_POST['g-recaptcha-response'];
    //Did not get a reCaptcha response from the user.
    if (!$captcha) {
        alert('danger', "Uh Oh!", "ReCaptcha response returned empty. Go back and try again.", true, $page);
        $api->game->addLog($userid, 'verify', "ReCaptcha returned empty.");
        header("refresh:5;url={$page}");
        die($h->endpage());
    }
    //Response gets decoded since its in json.
    $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$set['reCaptcha_private']}&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']), true);
    //User did not successfully verify themselves with reCaptcha.
    if ($response['success'] == false) {
        alert('danger', "Uh Oh!", "You have failed to pass the ReCaptcha check. Go back and try again.", true, $page);
        $api->game->addLog($userid, 'verify', "Verified unsuccessfully.");
        header("refresh:5;url={$page}");
        die($h->endpage());
    } //Use has been verified! :D
    else {
        $time = time();
        $db->query("UPDATE users SET `last_verified`={$time}, `need_verify` = 0 WHERE userid={$userid}");
        $api->game->addLog($userid, 'verify', "Verified successfully.");
        header("Location: {$page}");
        die($h->endpage());
    }
} //reCaptcha response has not been received from the player.
else {
    $api->game->addLog($userid, 'verify', "Did not receive ReCaptcha response.");
    alert('danger', "Uh Oh!", "Go back, please.", true, $page);
    header("refresh:5;url={$page}");
    die($h->endpage());
}