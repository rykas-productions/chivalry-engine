<?php
/*
	File:		authenticate.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Contains the user authentication logic.
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
//I wish to rewrite this mess eventually.
$menuhide = true;
$CurrentTime = time();
require_once('globals_nonauth.php');
$IP = $db->escape($_SERVER['REMOTE_ADDR']);
$email = (array_key_exists('email', $_POST) && is_string($_POST['email'])) ? $_POST['email'] : '';
$password = (array_key_exists('password', $_POST) && is_string($_POST['password'])) ? $_POST['password'] : '';
if (!isset($_POST['verf']) || !checkCSRF('login', stripslashes($_POST['verf']))) {
    die("<h3>{$set['WebsiteName']} Error</h3> CSRF Check failed. Please submit the form quicker next time.");
}
$QuarterHour = (time() - 900);
$FTMQuery = $db->query("SELECT `timestamp`
                      FROM `login_attempts`
                      WHERE `ip` = '{$IP}'
                      AND `timestamp` > {$QuarterHour}");
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
$uq = $db->query("SELECT `userid`,`password`
                FROM `users`
                WHERE `email` = '$form_email' LIMIT 1");
$UQ = $db->query("SELECT `userid`,`password`
                FROM `users`
                WHERE `email` = '$form_email' LIMIT 1");

$userid = $db->fetch_row($uq);
$QHQuery = $db->query("SELECT `timestamp`
                      FROM `login_attempts`
                      WHERE `userid` = '{$userid['userid']}'
                      AND `timestamp` > {$QuarterHour}");
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
    $login_failed = !(checkUserPassword($raw_password, $mem['password']));
    //Login failed
    if ($login_failed) {
        //Log login attempt.
        $db->query("INSERT INTO `login_attempts` (`ip`, `userid`, `timestamp`) VALUES ('{$IP}', '{$mem['userid']}', '{$CurrentTime}');");
        addNotification($mem['userid'], "Someone has just recently attempted to gain access to your account and failed.
		    If this was you, you do not need to do anything. However, if this was not, you should change your password
		    immediately!");
        die("<h3>{$set['WebsiteName']} Error</h3> Invalid Email and/or Password.<br /> <a href='login.php'>Back</a>");

    }
    session_regenerate_id();
    $_SESSION['loggedin'] = 1;
    $_SESSION['userid'] = $mem['userid'];
    $_SESSION['last_login'] = time();
    $db->query("UPDATE `users`
              SET `loginip` = '{$IP}',
              `last_login` = '{$CurrentTime}',
              `laston` = '{$CurrentTime}'
               WHERE `userid` = {$mem['userid']}");
    $encpsw = encodePassword($raw_password);
    $e_encpsw = $db->escape($encpsw);
    //Update user's password as an extra security mesaure.
    $db->query("UPDATE `users` SET `password` = '{$e_encpsw}' WHERE `userid` = {$_SESSION['userid']}");
    //Remove login attempts for this account.
    $db->query("DELETE FROM `login_attempts` WHERE `userid` = {$_SESSION['userid']}");
    $loggedin_url = 'loggedin.php';
    //Log that the user logged in successfully.
    $api->game->addLog($_SESSION['userid'], 'login', "Successfully logged in.");
    //Delete password recovery attempts from DB if they exist for this user.
    $db->query("DELETE FROM `pw_recovery` WHERE `pwr_email` = '{$form_email}'");
    header("Location: {$loggedin_url}");
    exit;
}