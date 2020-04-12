<?php
/*
	File:		macro.php
	Created: 	4/5/2016 at 12:18AM Eastern Time
	Info: 		Verifies if the player is botting or not. Setup
				reCaptcha in the staff panel!
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require_once('globals.php');
$page = stripslashes(strip_tags($_POST['page']));
//Check if the reCaptcha response has been received from the player.
if (isset($_POST['g-recaptcha-response'])) {
    $captcha = $_POST['g-recaptcha-response'];
    //Did not get a reCaptcha response from the user.
    if (!$captcha) {
        alert('danger', "Uh Oh!", "ReCaptcha response returned empty. Go back and try again.", true, $page);
        $api->SystemLogsAdd($userid, 'verify', "ReCaptcha returned empty.");
        header("refresh:5;url={$page}");
        die($h->endpage());
    }
    //Response gets decoded since its in json.
    $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$set['reCaptcha_private']}&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']), true);
    //User did not successfully verify themselves with reCaptcha.
    if ($response['success'] == false) {
        alert('danger', "Uh Oh!", "You have failed to pass the ReCaptcha check. Go back and try again.", true, $page);
        $api->SystemLogsAdd($userid, 'verify', "Verified unsuccessfully.");
        header("refresh:5;url={$page}");
        die($h->endpage());
    } //Use has been verified! :D
    else {
        $time = time();
        $db->query("UPDATE users SET `last_verified`={$time}, `need_verify` = 0 WHERE userid={$userid}");
        $api->SystemLogsAdd($userid, 'verify', "Verified successfully.");
        header("Location: {$page}");
        die($h->endpage());
    }
} //reCaptcha response has not been received from the player.
else {
    $api->SystemLogsAdd($userid, 'verify', "Did not receive ReCaptcha response.");
    alert('danger', "Uh Oh!", "We didn't get a ReCaptcha response from you... Weird. Make sure you wait until ReCaptcha is complete before clicking submit.", true, $page);
    header("refresh:5;url={$page}");
    die($h->endpage());
}