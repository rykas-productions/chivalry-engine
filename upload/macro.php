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
$time = time();
$page = stripslashes(strip_tags($_POST['page']));
$data = array(
    'secret' => $set['reCaptcha_private'],
    'response' => $_POST['h-captcha-response']
);
$verify = curl_init();
curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
curl_setopt($verify, CURLOPT_POST, true);
curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($verify);

$responseData = json_decode($response);
if($responseData->success) 
{
    $db->query("UPDATE users SET `last_verified`={$time}, `need_verify` = 0 WHERE userid={$userid}");
    $api->SystemLogsAdd($userid, 'verify', "Verified successfully.");
    header("Location: {$page}");
    die($h->endpage());
}
else
{
    alert('danger', "Uh Oh!", "You have failed to pass the ReCaptcha check. Go back and try again. Redirecting in 5 seconds...", true, $page);
    $api->SystemLogsAdd($userid, 'verify', "Verified unsuccessfully.");
    header("refresh:5;url={$page}");
    die($h->endpage());
}