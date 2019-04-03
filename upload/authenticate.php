<?php
/*
	File:		authenticate.php
	Created: 	4/4/2016 at 11:53PM Eastern Time
	Info: 		Contains the authentication and login logic.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
//I wish to rewrite this mess eventually.
$menuhide = true;
require_once('globals_nonauth.php');
$IP = $db->escape($_SERVER['REMOTE_ADDR']);
$CurrentTime = time();
$email = (array_key_exists('email', $_POST) && is_string($_POST['email'])) ? $_POST['email'] : '';
$password = (array_key_exists('password', $_POST) && is_string($_POST['password'])) ? $_POST['password'] : '';
if (!isset($_POST['verf']) || !verify_csrf_code('login', stripslashes($_POST['verf']))) {
    die("<h3>{$set['WebsiteName']} Error</h3> CSRF Check failed. Please submit the form quicker next time.");
}
$QuarterHour = ($CurrentTime - 900);
$Hour = ($CurrentTime - 3600);
$Day = ($CurrentTime - 86400);
$DQuery = $db->query("/*qc=on*/SELECT `timestamp`
                    FROM `login_attempts`
                    WHERE `ip` = '{$IP}'
                    AND `timestamp` > {$Day}");
$HQuery = $db->query("/*qc=on*/SELECT `timestamp`
                    FROM `login_attempts`
                    WHERE `ip` = '{$IP}'
                    AND `timestamp` > {$Hour}");
$FTMQuery = $db->query("/*qc=on*/SELECT `timestamp`
                      FROM `login_attempts`
                      WHERE `ip` = '{$IP}'
                      AND `timestamp` > {$QuarterHour}");
//User has failed to login 9 or more times within the last day.
if ($db->num_rows($DQuery) >= 9) {
    die("<h3>{$set['WebsiteName']} Error</h3> You cannot attempt to log in anymore for the next 24 hours.");
}
//User has failed to login 6 or more times within the last hour.
if ($db->num_rows($HQuery) >= 6) {
    die("<h3>{$set['WebsiteName']} Error</h3> You cannot attempt to log in anymore for the next 1 hour.");
}
//User has failed to login 3 or more times within the last 15 minutes.
if ($db->num_rows($FTMQuery) >= 3) {
    die("<h3>{$set['WebsiteName']} Error</h3> You cannot attempt to log in anymore for the next 15 minutes.");
}
//Password or email address not specifed.
if (empty($email) || empty($password)) {
    //Log login attempt.
    $db->query("INSERT INTO `login_attempts`
              (`ip`, `userid`, `timestamp`)
              VALUES
              ('{$IP}', '0', '{$CurrentTime}');");
    die("<h3>{$set['WebsiteName']} Error</h3> Invalid Email and/or Password.<br /> <a href='login.php'>Back</a>");

}
$form_email = $db->escape(stripslashes($email));
$raw_password = stripslashes($password);
$uq = $db->query("/*qc=on*/SELECT `userid`,`password`
                FROM `users`
                WHERE `email` = '$form_email' LIMIT 1");
$UQ = $db->query("/*qc=on*/SELECT `userid`,`password`,`user_level`
                FROM `users`
                WHERE `email` = '$form_email' LIMIT 1");

$userid = $db->fetch_row($uq);

$DUNQuery = $db->query("/*qc=on*/SELECT `timestamp`
                      FROM `login_attempts`
                      WHERE `userid` = '{$userid['userid']}'
                      AND `timestamp` > {$Day}");
$HUNQuery = $db->query("/*qc=on*/SELECT `timestamp`
                      FROM `login_attempts`
                      WHERE `userid` = '{$userid['userid']}'
                      AND `timestamp` > {$Hour}");
$QHQuery = $db->query("/*qc=on*/SELECT `timestamp`
                      FROM `login_attempts`
                      WHERE `userid` = '{$userid['userid']}'
                      AND `timestamp` > {$QuarterHour}");
//Account has failed to login 9 times in the past day.
if ($db->num_rows($DUNQuery) >= 9) {
    die("<h3>{$set['WebsiteName']} Error</h3> You cannot attempt to log in anymore for the next 24 hours.");
}
//Account has failed to login 6 times in the past hour.
if ($db->num_rows($HUNQuery) >= 6) {
    die("<h3>{$set['WebsiteName']} Error</h3> You cannot attempt to log in anymore for the next 1 hour.");
}
//Account has failed to login 3 times in the past 15 minutes.
if ($db->num_rows($QHQuery) >= 3) {
    die("<h3>{$set['WebsiteName']} Error</h3> You cannot attempt to log in anymore for the next 15 minutes.");
}
//User does not exist.
if ($db->num_rows($UQ) == 0) {
    $db->free_result($uq);
    //Log the login attempt.
    $db->query("INSERT INTO `login_attempts` (`ip`, `userid`, `timestamp`) VALUES ('{$IP}', '0', '{$CurrentTime}');");
    die("<h3>{$set['WebsiteName']} Error</h3> Invalid Email and/or Password.<br /> <a href='login.php'>Back</a>");
} //User exists...
else {
    $mem = $db->fetch_row($UQ);
    $db->free_result($UQ);
    //Verify user's password, then log them in.
    $login_failed = false;
    $login_failed = !(verify_user_password($raw_password, $mem['password']));
    //Login failed
    if ($login_failed) {
        //Log login attempt.
        $db->query("INSERT INTO `login_attempts` (`ip`, `userid`, `timestamp`) VALUES ('{$IP}', '{$mem['userid']}', '{$CurrentTime}');");
        notification_add($mem['userid'], "Someone has just recently attempted to gain access to your account and failed.
		    If this was you, you do not need to do anything. However, if this was not, you should change your password
		    immediately!", 'fas fa-exclamation-circle', 'red');
        die("<h3>{$set['WebsiteName']} Error</h3> Invalid Email and/or Password.<br /> <a href='login.php'>Back</a>");

    }
    session_regenerate_id();
    $_SESSION['userid'] = $mem['userid'];
	$uade=$db->query("/*qc=on*/SELECT * FROM `user_settings` WHERE `userid` = {$mem['userid']}");
	if ($db->num_rows($uade) == 0)
	{
		$db->query("INSERT INTO `user_settings` (`userid`) VALUES ('{$mem['userid']}')");
	}
	$uadr=$db->fetch_row($uade);
	$_SESSION['loggedin'] = 1;
	$_SESSION['last_login'] = time();
	setcookie('login_expire', time() + 604800, time() + 604800);
    $invis=$db->fetch_single($db->query("/*qc=on*/SELECT `invis` FROM `user_settings` WHERE `userid` = {$mem['userid']}"));
    if ($invis < time())
    {
        $db->query("UPDATE `users`
              SET `loginip` = '{$IP}',
              `last_login` = '{$CurrentTime}',
              `laston` = '{$CurrentTime}'
               WHERE `userid` = {$mem['userid']}");
    }
    else
    {
        $db->query("UPDATE `users`
              SET `loginip` = '{$IP}'
               WHERE `userid` = {$mem['userid']}");
    }
    $encpsw = encode_password($raw_password,$mem['user_level']);
    $e_encpsw = $db->escape($encpsw);
    //Update user's password as an extra security mesaure.
    $db->query("UPDATE `users` SET `password` = '{$e_encpsw}' WHERE `userid` = {$_SESSION['userid']}");
    //Remove login attempts for this account.
    $db->query("DELETE FROM `login_attempts` WHERE `userid` = {$_SESSION['userid']}");
    $loggedin_url = 'explore.php';
    //Log that the user logged in successfully.
    $api->SystemLogsAdd($_SESSION['userid'], 'login', "Successfully logged in.");
    //Delete password recovery attempts from DB if they exist for this user.
    $db->query("DELETE FROM `pw_recovery` WHERE `pwr_email` = '{$form_email}'");
    header("Location: {$loggedin_url}");
    exit;
}