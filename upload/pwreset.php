<?php
/*
	File:		pwreset.php
	Created: 	4/5/2016 at 12:23AM Eastern Time
	Info: 		Allows players to reset their password if they have
				forgotten it. Please fill in the $from field below.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
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
    global $db, $from, $set, $api;
    require('lib/bbcode_engine.php');
    $AnnouncementQuery = $db->query("/*qc=on*/SELECT `ann_text`,`ann_time` FROM `announcements` ORDER BY `ann_time` desc LIMIT 1");
    $ANN = $db->fetch_row($AnnouncementQuery);
    $ANN['ann_text']=substr($ANN['ann_text'], 0, 330);
    //$parser->parse($ANN['ann_text']);
    $last24hr=time()-86400;
    $totalplayers=$db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users`"));
    $playersonline=$db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `laston` > {$last24hr}"));
    $signups=$db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `registertime` > {$last24hr}"));
    
    if (isset($_POST['email'])) {
        if (!isset($_POST['email']) || !valid_email(stripslashes($_POST['email']))) {
            alert('danger', "Uh Oh!", "You input an invalid email address.", false);
            require("footer.php");
            exit;
        }
        $e_email = $db->escape(stripslashes($_POST['email']));
        $IP = $db->escape($_SERVER['REMOTE_ADDR']);
        $email = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `email` = '{$e_email}'"));
        $token = randomizer();
        if ($email > 0) {
			$username=$db->fetch_single($db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `email` = '{$e_email}'"));
            $to = $e_email;
            $subject = "{$set['WebsiteName']} Password Recovery";
			$body = "Greetings {$username}!<br />
			It appears that at around " . date('l, F j, Y g:i:s a') . " Chivalry is Dead time, a 
			request was made to reset your in-game password. Before we do, we need to make sure its
			you who made the request. If it is, great, click 
			<a href='http://" . determine_game_urlbase() . "/pwreset.php?step=two&code={$token}'>here</a> to 
			get the password reset process started.<br />
			If it was not you, please log into the game and change your password immediately, as your 
			account may be compromised.<br />
			<br />
			If you cannot click the link: (http://" . determine_game_urlbase() . "/pwreset.php?step=two&code={$token})<br />
			<br />
			The password reset link will be valid for 30 minutes.";
            $api->SystemSendEmail($to, $body, $subject, $from);
            $expire = time() + 1800;
            $db->query("UPDATE `users` SET `force_logout` = 'true' WHERE `email` = '{$e_email}'");
            $db->query("INSERT INTO `pw_recovery` (`pwr_ip`, `pwr_email`, `pwr_code`, `pwr_expire`) VALUES ('{$IP}', '{$e_email}', '{$token}', '{$expire}')");
        }
        alert('success', "Success!", "If there is an account associated to the email address you input, you will be
		    emailed with steps on how to start the password reset process.", false);
    } else {
        echo "<div class='row'>
        <div class='col-md-6 col-lg-5 col-xl-4 col-xxl-3'>
        <div class='card'>
        <div class='card-header'>
        <div class='row'>
        <div class='col'>
        Sign In
        </div>
        <div class='col'>
        <a href='login.php'>Sign In!</a>
        </div>
        </div>
        </div>
        <div class='card-body'>";
        alert('info', "", "Please enter the email adress tied to your account so we can send information on how to reset your password. Please be sure to check your junk folder.", false);
        echo "
        <form method='post'>
        <input type='email' name='email' class='form-control' required='true' placeholder='Your email address'><br />
        <input type='submit' class='btn btn-primary btn-block' value='Recover Password'><br />
        New here? <a href='register.php'>Sign up</a> for an account!
        </form>
        </div>
            </div>
            <br />
            </div>
            <div class='col-md-6 col-lg-7 col-xl-8 col-xxl-4'>
            <div class='card'>
            <div class='card-header'>
            Warrior with no empathy
            </div>
            <div class='card-body'>
            {$set['Website_Description']}
            </div>
            </div>
            </div>
            <br />
            <div class='col-md-6 col-lg-4 col-xxl-5'>
            <div class='card'>
            <div class='card-header'>
            Highest Ranked Players
            </div>
            <div class='card-body'>";
                $Rank = 0;
                $RankPlayerQuery =
                    $db->query("SELECT u.`userid`, `level`, `username`,
                    `strength`, `agility`, `guard`, `labor`, `IQ`
                    FROM `users` AS `u`
                    INNER JOIN `userstats` AS `us`
                    ON `u`.`userid` = `us`.`userid`
                    WHERE `u`.`user_level` != 'Admin' AND `u`.`user_level` != 'NPC'
                        ORDER BY (`strength` + `agility` + `guard` + `labor` + `IQ`)
                        DESC, `u`.`userid` ASC
                        LIMIT 10");
                while ($pdata = $db->fetch_row($RankPlayerQuery)) {
                    $Rank = $Rank + 1;
                    echo "<div class='row'>
                    <div class='col col-md-2'>
                    {$Rank}
                    </div>
                    <div class='col'>
                    " . parseUsername($pdata['userid']) . " [{$pdata['userid']}]
                    </div>
                    <div class='col col-4'>
                    Level " . number_format($pdata['level']) . "
                        </div>
                        </div>";
                }
                echo"
                        </div>
                        </div>
                        <br />
                        </div>
                        <div class='col-md-6 col-lg-7 col-xl-8 col-xxl-4'>
                        <div class='card'>
                        <div class='card-header'>
                        Gameplay
                        </div>
                        <div class='card-body'>
                        Players must get stronger in order to defeat those who oppose them. How they get there is entirely up to them!
                        Players may commit crimes and become a crimelord, or hit the training grounds to become a well rounded warrior.
                        Estates are available purchase to accomodate yourself growing as the warrior they claim to be. <br />
                        Other warriors will attempt you from reaching the number one warrior spot... but are you really going to be stopped?
                        </div>
                        </div>
                        </div>
                        <br />
                        <div class='col-md-6 col-lg-5 col-xl-4 col-xxl-3'>
                        <div class='card'>
                        <div class='card-header'>
                        No Installation Required!
                        </div>
                        <div class='card-body'>
                        " . number_format($playersonline) . " Players Online Today<br />
                        " . number_format($totalplayers) . " Total Players<br />
                        " . number_format($signups) . " New Players Today<br />
                        Most Users Online: " . number_format($set['mostUsersOn']) . " Users<br />" . DateTime_Parse($set['mostUsersOnTime']) . "
                            </div>
                            </div>
                            </div>
                            <div class='col-md-6 col-lg-7 col-xl-8 col-xxl-5'>
                            <div class='card'>
                            <div class='card-header'>
                            Latest Announcement
                            </div>
                            <div class='card-body'>
                            " . $parser->getAsHtml() . "<br />
                            <small>" . DateTime_Parse($ANN['ann_time']) . "</small>
                                </div>
                                </div>
                                </div>
                                </div>";
    }
}

function two()
{
    global $db, $from, $set, $api;
    if (isset($_GET['code'])) {
        $token = $db->escape(stripslashes($_GET['code']));
        if ($db->num_rows($db->query("/*qc=on*/SELECT `pwr_id` FROM `pw_recovery` WHERE `pwr_code` = '{$token}'")) == 0) {
            alert('danger', "Uh Oh!", "Invalid token.", false);
        } else if ($db->fetch_single($db->query("/*qc=on*/SELECT `pwr_expire` FROM `pw_recovery` WHERE `pwr_code` = '{$token}'")) < time()) {
            alert('danger', "Uh Oh!", "Token has expired.", false);
        } else {
            $pwr = $db->fetch_row($db->query("/*qc=on*/SELECT * FROM `pw_recovery` WHERE `pwr_code` = '{$token}'"));
            $pw = substr(randomizer(), 0, 16);
            $to = $pwr['pwr_email'];
            $subject = "{$set['WebsiteName']} Password Recovery";
            $body = "Your password has been successfully updated to {$pw}
			<br /> Please use this to log in from now on. We highly recommend changing your password as soon as you log in.";
            $api->SystemSendEmail($to, $body, $subject, $from);
            $db->query("UPDATE `users` SET `force_logout` = 'true' WHERE `email` = '{$pwr['pwr_email']}'");
            $e_pw = encode_password($pw);
            $db->query("UPDATE `users` SET `password` = '{$e_pw}' WHERE `email` = '{$pwr['pwr_email']}'");
			$newuserid=$db->fetch_single($db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `email` = '{$to}'"));
			$randomizer=randomizer();
            $db->query("UPDATE `user_settings` SET `security_key` = '{$randomizer}' WHERE `userid` = {$newuserid}");
            $db->query("DELETE FROM `pw_recovery` WHERE `pwr_code` = '{$token}'");
            alert('success', "Success!", "Your new password has been emailed to you. If you were previously logged in,
			    your session has been terminated.", true, 'login.php', 'Login');
        }
    } else {
        alert('danger', "Uh Oh!", "Please specify a recovery token.", false);
    }
}

$h->endpage();