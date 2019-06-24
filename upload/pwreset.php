<?php
/*
	File:		pwreset.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows users to request a password change via email.
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
require('globals_nonauth.php');
$from = $set['sending_email'];
if (!isset($_GET['step'])) {
    $_GET['step'] = '';
}
switch ($_GET['step']) {
    case 'two':
        two();
        break;
    default:
        one();
        break;
}
function one()
{
    global $db, $from, $set, $api, $h;
    if (isset($_POST['email'])) {
        if (!isset($_POST['email']) || !validEmail(stripslashes($_POST['email']))) {
            alert('danger', "Uh Oh!", "You input an invalid email address.", false);
            die($h->endpage());
        }
        $e_email = $db->escape(stripslashes($_POST['email']));
        $IP = $db->escape($_SERVER['REMOTE_ADDR']);
        $email = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `email` = '{$e_email}'"));
        $token = getrandomNumberString();
        if ($email > 0) {
            $to = $e_email;
            $subject = "{$set['WebsiteName']} Password Recovery";
            $body = "Recently, someone has attempted to reset your password. Click <a href='http://" . getGameURL() . "/pwreset.php?step=two&code={$token}'>here</a>
			to start the password reset process. If this wasn't you, do not click this link. 
			The link will expire approximately 30 minutes after the password reset process.<br />
			<br />
			If you cannot click the URL for whatever reason, please paste in http://" . getGameURL() . "/pwreset.php?step=two&code={$token} into your URL bar.";
            $api->game->sendEmail($to, $body, $subject, $from);
            $expire = time() + 1800;
            $db->query("UPDATE `users` SET `force_logout` = 'true' WHERE `email` = '{$e_email}'");
            $db->query("INSERT INTO `pw_recovery` (`pwr_ip`, `pwr_email`, `pwr_code`, `pwr_expire`) VALUES ('{$IP}', '{$e_email}', '{$token}', '{$expire}')");
        }
        alert('success', "Success!", "If there is an account associated to the email address you input, you will be
		    emailed with steps on how to start the password reset process.", false);
    } else {
        alert('info', "Information!", "Please enter the email address tied to your account so we can send information on how to reset your password. Please be sure to check your junk folder.", false);
        echo "
		<form method='post'>
			<input type='email' name='email' required='1' class='form-control'>
			<br />
			<input type='submit' class='btn btn-primary'>
		</form>";
    }
}

function two()
{
    global $db, $from, $set, $api;
    if (isset($_GET['code'])) {
        $token = $db->escape(stripslashes($_GET['code']));
        if ($db->num_rows($db->query("SELECT `pwr_id` FROM `pw_recovery` WHERE `pwr_code` = '{$token}'")) == 0) {
            alert('danger', "Uh Oh!", "Your account does not have a password recovery token linked.", false);
        } else if ($db->fetch_single($db->query("SELECT `pwr_expire` FROM `pw_recovery` WHERE `pwr_code` = '{$token}'")) < time()) {
            alert('danger', "Uh Oh!", "Your password recovery token has expired.", false);
        } else {
            $pwr = $db->fetch_row($db->query("SELECT * FROM `pw_recovery` WHERE `pwr_code` = '{$token}'"));
            $pw = substr(getrandomNumberString(), 0, 16);
            $to = $pwr['pwr_email'];
            $subject = "{$set['WebsiteName']} Password Recovery";
            $body = "Your password has been successfully updated to {$pw}
			<br /> Please use this to log in from now on. We highly recommend changing your password as soon as you log in.";
            $api->game->sendEmail($to, $body, $subject, $from);
            $db->query("UPDATE `users` SET `force_logout` = 'true' WHERE `email` = '{$pwr['pwr_email']}'");
            $e_pw = encodePassword($pw);
            $db->query("UPDATE `users` SET `password` = '{$e_pw}' WHERE `email` = '{$pwr['pwr_email']}'");
            $db->query("DELETE FROM `pw_recovery` WHERE `pwr_code` = '{$token}'");
            alert('success', "Success!", "Your new password has been emailed to you. If you were previously logged in,
			    your session has been terminated.", false);
        }
    } else {
        alert('danger', "Uh Oh!", "Please specify a recovery token.", false);
    }
}

$h->endpage();