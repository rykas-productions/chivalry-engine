<?php
/*
	File:		preferences.php
	Created: 	4/5/2016 at 12:22AM Eastern Time
	Info: 		Allows players to change settings about their account.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'namechange':
        name_change();
        break;
    case 'pwchange':
        pw_change();
        break;
    case 'picchange':
        pic_change();
        break;
    case 'sigchange':
        sigchange();
        break;
    case 'sexchange':
        sexchange();
        break;
    case 'emailchange':
        emailchange();
        break;
	case 'descchange':
        descchange();
        break;
	case 'quicklink':
        quicklinks();
        break;
	case 'userdropdown':
        userdropdown();
        break;
	case 'forumalert':
        forumalert();
        break;
    case 'tuttoggle':
        tuttoggle();
        break;
	case 'analytics':
        analytics();
        break;
    case 'webnotif':
        webnotif();
        break;
    case 'classreset':
        classreset();
        break;
    case 'changeemail':
        changeemail();
        break;
    case 'themechange':
        themechange();
        break;
	case 'icontoggle':
        icontoggle();
        break;
    case 'steamlink':
        steamlink();
        break;
	case 'reset':
        resetacc();
        break;
	case 'loginlogs':
        loginlogs();
        break;
	case 'newui':
		newui();
        break;
	case 'counthome':
		homecount();
        break;
	case 'sounds':
	    prefsound();
	    break;
    default:
        prefs_home();
        break;
}
function prefs_home()
{
    global $ir;
    alert('info','',"Welcome to your account settings, <b>{$ir['username']} [{$ir['userid']}]</b>! Here you can change many options concerning your account.",false);
    echo "
		<div class='row'>
			<div class='col-6 col-md-6 col-lg-4 col-xxl-3 col-xxxl-2'>
				<a class='btn btn-primary btn-block' href='?action=namechange'>Change Name</a>
                <br />
			</div>
			<div class='col-6 col-md-6 col-lg-4 col-xxl-3 col-xxxl-2'>
				<a class='btn btn-danger btn-block' href='?action=pwchange'>Change Password</a>
                <br />
			</div>
			<div class='col-12 col-sm-6 col-md-12 col-lg-4 col-xxl-3 col-xxxl-2'>
				<a class='btn btn-danger btn-block' href='?action=changeemail'>Change Email Address</a>
                <br />
			</div>
			<div class='col-12 col-sm-6 col-md-12 col-lg-6 col-xl-4 col-xxl-3 col-xxxl-2'>
				<a class='btn btn-primary btn-block' href='?action=emailchange'>Change Email Opt-Setting</a>
                <br />
			</div>
			<div class='col-12 col-sm-6 col-md-12 col-lg-6 col-xl-4 col-xxl-3 col-xxxl-2'>
				<a class='btn btn-primary btn-block' href='?action=picchange'>Change Display Picture</a>
                <br />
			</div>
			<div class='col-12 col-sm-6 col-md-12 col-lg-6 col-xl-4 col-xxl-3 col-xxxl-2'>
				<a class='btn btn-primary btn-block' href='?action=sexchange'>Change Gender</a>
                <br />
			</div>
			<div class='col-12 col-sm-6 col-md-12 col-lg-6 col-xl-4 col-xxl-3 col-xxxl-2'>
				<a class='btn btn-primary btn-block' href='?action=sigchange'>Change Forum Signature</a>
                <br />
			</div>
			<div class='col-12 col-sm-6 col-md-12 col-lg-6 col-xl-4 col-xxl-3 col-xxxl-2'>
				<a class='btn btn-primary btn-block' href='?action=descchange'>Change Player Description</a>
                <br />
			</div>
			<div class='col-12 col-sm-6 col-md-12 col-lg-6 col-xl-4 col-xxl-3 col-xxxl-2'>
				<a class='btn btn-primary btn-block' href='?action=quicklink'>Change Quick-Use Items</a>
                <br />
			</div>
			<div class='col-6 col-md-6 col-xl-4 col-xxl-3 col-xxxl-2'>
				<a class='btn btn-primary btn-block' href='?action=forumalert'>Forum Notifications</a>
                <br />
			</div>
			<div class='col-6 col-xl-4 col-xxl-3 col-xxxl-2'>
				<a class='btn btn-primary btn-block' href='?action=userdropdown'>User Input Setting</a>
                <br />
			</div>
			<div class='col-6 col-md-6 col-lg-4 col-xxl-3 col-xxxl-2'>
				<a class='btn btn-primary btn-block' href='?action=themechange'>Change Theme</a>
                <br />
			</div>
			<div class='col-6 col-md-6 col-lg-4 col-xxl-3 col-xxxl-2'>
				<a class='btn btn-primary btn-block' href='?action=classreset'>Class Reset</a>
                <br />
			</div>
			<div class='col-6 col-md-6 col-lg-4 col-xxl-3 col-xxxl-2'>
				<a class='btn btn-info btn-block' href='?action=tuttoggle'>Tutorial Toggle</a>
                <br />
			</div>
			<div class='col-6 col-xl-4 col-xxl-3 col-xxxl-2'>
				<a class='btn btn-primary btn-block' href='?action=icontoggle'>Toggle Item Icons</a>
                <br />
			</div>
			<div class='col-6 col-xl-4 col-xxl-3 col-xxxl-2'>
				<a class='btn btn-danger btn-block' href='?action=steamlink'>Link Steam Account</a>
                <br />
			</div>
			<div class='col-6 col-md-6 col-lg-4 col-xxl-3 col-xxxl-2'>
				<a class='btn btn-info btn-block' href='?action=loginlogs'>Login Logs</a>
                <br />
			</div>
			<div class='col-6 col-md-6 col-lg-4 col-xxl-3 col-xxxl-2'>
				<a class='btn btn-danger btn-block' href='?action=reset'>Account Reset</a>
                <br />
			</div>
			<div class='col-6 col-md-6 col-lg-4 col-xxl-3 col-xxxl-2'>
				<a class='btn btn-primary btn-block' href='?action=counthome'>Change Counts</a>
                <br />
			</div>
            <div class='col-6 col-md-6 col-lg-4 col-xxl-3 col-xxxl-2'>
				<a class='btn btn-primary btn-block' href='?action=sounds'>Audio Settings</a>
                <br />
			</div>
		</div>";
}

function name_change()
{
    global $db, $ir, $userid, $h, $api;
    if (empty($_POST['newname'])) {
        $csrf = request_csrf_html('prefs_namechange');
        echo "<br />
		<h3>Username Change</h3>
		Here you can change your name that is shown throughout the game. It will cost you 5 Chivalry Tokens to change your name.<br />
		<div class='form-group'>
		<form method='post'>
			<input type='text' class='form-control' minlength='3' maxlength='20' id='username' required='1' value='{$ir['username']}' name='newname' onkeyup='CheckUsername(this.value);' />
			<br />
			{$csrf}
			<input type='submit' class='btn btn-primary' value='Change Username' />
			</div>
            <div id='usernameresult' class='invalid-feedback'></div>
		</form>";
    } else {
        if (!isset($_POST['verf']) || !verify_csrf_code('prefs_namechange', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your action was blocked for security reasons. Fill out the form quicker next time.");
            die($h->endpage());
        }
        $_POST['newname'] = (isset($_POST['newname']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])*$/i", $_POST['newname'])) ? $db->escape(strip_tags(stripslashes($_POST['newname']))) : '';
        if (empty($_POST['newname'])) {
            alert('danger', "Uh Oh!", "Invalid username specified. Please fill out the form and try again.");
            die($h->endpage());
        } elseif (((strlen($_POST['newname']) > 20) OR (strlen($_POST['newname']) < 3))) {
            alert('danger', "Uh Oh!", "Usernames must be at least 3 characters in length, and a maximum of 20.");
            die($h->endpage());
        }
        $check_ex = $db->query('SELECT `userid` FROM `users` WHERE `username` = "' . $db->escape($_POST['newname']) . '"');
        if ($db->num_rows($check_ex) > 0) {
            alert('danger', "Uh Oh!", "The username you chose is already in use.");
            die($h->endpage());
        }
        if (!$api->UserHasCurrency($userid,'secondary',5))
        {
            alert('danger',"Uh Oh!","You do nto have enough Chivalry Tokens to change your name.");
            die($h->endpage());
        }
		addToEconomyLog('Misc', 'token', -5);
        $api->UserTakeCurrency($userid,'secondary',5);
        $_POST['newname'] = $db->escape(htmlentities($_POST['newname'], ENT_QUOTES, 'ISO-8859-1'));
        $db->query("UPDATE `users` SET `username` = '{$_POST['newname']}'  WHERE `userid` = $userid");
        alert('success', "Success!", "You have changed your username to {$_POST['newname']}.", true, 'preferences.php');
        $api->SystemLogsAdd($userid, 'preferences', "Changed username from {$ir['username']} to {$_POST['newname']}.");
    }
}

function pw_change()
{
    global $db, $ir, $h, $api, $userid;
	if (isset($_GET['newkey']))
	{
		if ($_GET['csrf'] != $_SESSION['newkeycsrf'])
		{
			alert('danger',"Uh Oh!","Invalid CSRF token. Encryption key not regenerated.",false);
		}
		else
		{
			$nrandomizer=randomizer();
            $db->query("UPDATE `user_settings` SET `security_key` = '{$nrandomizer}' WHERE `userid` = {$ir['userid']}");
			alert('success',"Success!","New encryption key generated.",false);
            $api->SystemLogsAdd($userid, 'preferences', "Generated new encryption key.");
		}
	}
    if (empty($_POST['oldpw'])) {
        $csrf = request_csrf_html('prefs_changepw');
		$_SESSION['newkeycsrf']=randomizer();
        echo "
	<h3>Password Change</h3>
	<hr />
	Remember that changing your password will make all your previously sent and received messages unreadable. You 
	may click <a href='?action=pwchange&newkey=1&csrf={$_SESSION['newkeycsrf']}'>here</a> if you just wish to regenerate your message encryption key.
	<form method='post'>
	<table class='table table-bordered'>
	<tr>
		<th>
			Current Password
		</th>
		<td>
			<input type='password' required='1' class='form-control' name='oldpw' />
		</td>
	</tr>
	<tr>
		<th>
			New Password
		</th>
		<td>
			<input type='password' id='password' required='1' class='form-control' onkeyup='CheckPasswords(this.value);' name='newpw' />
            <div id='passwordresult'></div>
        </td>
	</tr>
	<tr>
		<th>
			Confirm Password
		</th>
		<td>
			<input type='password' required='1' class='form-control' name='newpw2' />
		</td>
	</tr>
	<tr>
		<td colspan='2'>
			<input type='submit' class='btn btn-primary' value='Update Password' />
		</td>
	</tr>
    	{$csrf}
    	
	</form>
	</table>
   	";
    } else {
        if (!isset($_POST['verf']) || !verify_csrf_code('prefs_changepw', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your action was blocked for security reasons. Fill out the form quicker next time.");
            die($h->endpage());
        }
        $oldpw = stripslashes($_POST['oldpw']);
        $newpw = stripslashes($_POST['newpw']);
        $newpw2 = stripslashes($_POST['newpw2']);
        if (!verify_user_password($oldpw, $ir['password'])) {
            alert('danger', "Uh Oh!", "Invalid old password.");
        } else if ($newpw !== $newpw2) {
            alert('danger', "Uh Oh!", "New password and confirmation did not match.");
        } else {
            // Re-encode password
            $new_psw = $db->escape(encode_password($newpw));
            $db->query("UPDATE `users` SET `password` = '{$new_psw}' WHERE `userid` = {$ir['userid']}");
            $randomizer=randomizer();
            $db->query("UPDATE `user_settings` SET `security_key` = '{$randomizer}' WHERE `userid` = {$ir['userid']}");
            alert('success', "Success!", "You password was updated successfully.", true, 'preferences.php');
            $api->SystemLogsAdd($userid, 'preferences', "Changed password.");
			$api->SystemSendEmail($ir['email'], "This email is to let you know that your password has been changed. If this by your doing, you may ignore this email. If not, someone may now have access to your account. Use the password reset form on the login page to reset your password.", "Chivalry is Dead Password Change");
        }
    }
}

function pic_change()
{
    global $db, $h, $userid, $ir, $api;
    if (!isset($_POST['newpic'])) {
        $csrf = request_csrf_html('prefs_changepic');
        echo "
		<h3>Change Display Picture</h3>
		<hr />
		Your images must be externally hosted.<br />
		New Picture Link<br />
		<form method='post'>
			<input type='url' name='newpic' class='form-control' value='{$ir['display_pic']}' />
				{$csrf}
			<br />
			<input type='submit' class='btn btn-primary' value='Change Display Picture' />
		</form>
		";
    } else {
        if (!isset($_POST['verf']) || !verify_csrf_code('prefs_changepic', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your action was blocked for security reasons. Fill out the form quicker next time.");
            die($h->endpage());
        }
        $npic = (isset($_POST['newpic']) && is_string($_POST['newpic'])) ? stripslashes($_POST['newpic']) : '';
        if (!empty($npic)) {
            $sz = get_filesize_remote($npic);
            if ($sz <= 0 || $sz >= 15000000) {
                alert('danger', "Uh Oh!", "You picture's file size is too big. At maximum, picture file size can be 15MB.");
                $h->endpage();
                exit;
            }
            $image = (@isImage($npic));
            if (!$image) {
                alert('danger', "Uh Oh!", "The link you've input is not an image.");
                die($h->endpage());
            }
        }
        $img = htmlentities($_POST['newpic'], ENT_QUOTES, 'ISO-8859-1');
        alert('success', "Success!", "You have successfully updated your display picture to what's shown below.", true, 'preferences.php');
        echo "<img src='{$img}' width='250' height='250' class='img-thumbnail img-fluid'>";
        $api->SystemLogsAdd($userid, 'preferences', "Changed display picture.");
        $db->query("UPDATE `users` SET `display_pic` = '" . $db->escape($npic) . "' WHERE `userid` = {$userid}");
    }
}

function sigchange()
{
    global $db, $ir, $userid, $h, $api;
    if (isset($_POST['sig'])) {
        $_POST['sig'] = $db->escape(str_replace("\n", "<br />", strip_tags(stripslashes($_POST['sig']))));
        if (!isset($_POST['verf']) || !verify_csrf_code('prefs_changesig', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your action was blocked for security reasons. Fill out the form quicker next time.");
            die($h->endpage());
        }
        if (strlen($_POST['sig']) > 1024) {
            alert('danger', "Uh Oh!", "Your signature can only be, at maximum, 1,024 characters.");
            die($h->endpage());
        }
        $db->query("UPDATE `users` SET `signature` = '{$_POST['sig']}' WHERE `userid` = {$userid}");
        alert('success', "Success!", "Your signature has been updated successfully.", true, 'preferences.php');
        $api->SystemLogsAdd($userid, 'preferences', "Changed forum signature.");
    } else {
        $ir['signature'] = strip_tags(stripslashes($ir['signature']));
        $csrf = request_csrf_html('prefs_changesig');
        echo "<form method='post'>
		<table class='table-bordered table'>
			<tr>
				<th colspan='2'>
					You can change your forum signature here. BBCode is allowable.
				</th>
			</tr>
			<tr>
				<th>
					Your Signature
				</th>
				<td>
					<textarea class='form-control' rows='4' name='sig'>{$ir['signature']}</textarea>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='Change Signature' class='btn btn-primary'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>";
    }
}

function sexchange()
{
    global $db, $userid, $ir, $h, $api;
    if (isset($_POST['gender'])) {
        if (!isset($_POST['verf']) || !verify_csrf_code('prefs_changesex', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your action was blocked for security reasons. Fill out the form quicker next time.");
            die($h->endpage());
        }
        if (!isset($_POST['gender']) || ($_POST['gender'] != 'Male' && $_POST['gender'] != 'Female' && $_POST['gender'] != 'Other')) {
            alert('danger', "Uh Oh!", "You cannot change into an invalid gender.");
            die($h->endpage());
        }
        if ($ir['gender'] == $_POST['gender']) {
            alert('danger', "Uh Oh!", "You cannot turn yourself back into your current gender.");
            die($h->endpage());
        }
        $e_gender = $db->escape(stripslashes($_POST['gender']));
        $db->query("UPDATE `users` SET `gender` = '{$e_gender}' WHERE `userid` = {$userid}");
        alert('success', "Success!", "You have successfully changed your gender into {$_POST['gender']}.", true, 'preferences.php');
        $api->SystemLogsAdd($userid, 'preferences', "Changed gender to {$_POST['gender']}.");
    } else {
        $g = "<option value='Male'>Male</option>
				<option value='Female'>Female</option>
				<option value='Other'>Other</option>";
        $csrf = request_csrf_html('prefs_changesex');
        echo "<table class='table table-bordered'>
		<form method='post'>
		<tr>
			<th colspan='2'>
				Use this form to change your gender. You currently identify as {$ir['gender']}.
			</th>
		</tr>
		<tr>
			<th>
				Gender
			</th>
			<td>
				<select name='gender' class='form-control' type='dropdown'>
					{$g}
				</select>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<input type='submit' value='Change Gender' class='btn btn-primary'>
			</td>
		</tr>
		{$csrf}
		</form>
		</table>";
    }
}

function emailchange()
{
    global $db, $userid, $ir, $h, $api;
    if (isset($_POST['opt'])) {
        $_POST['opt'] = (isset($_POST['opt']) && is_numeric($_POST['opt'])) ? abs($_POST['opt']) : 0;
        if (!isset($_POST['verf']) || !verify_csrf_code('prefs_changeopt', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your action was blocked for security reasons. Fill out the form quicker next time.");
            die($h->endpage());
        }
        if (!isset($_POST['opt']) || ($_POST['opt'] != 1 && $_POST['opt'] != 0)) {
            alert('danger', "Uh Oh!", "Invalid opt setting specified.");
            die($h->endpage());
        }
        $db->query("UPDATE `user_settings` SET `email_optin` = {$_POST['opt']} WHERE `userid` = {$userid}");
        alert('success', "Success!", "You have changed your email opt setting.", true, 'preferences.php');
        $api->SystemLogsAdd($userid, 'preferences', "Changed email opt setting.");
    } else {
        $g = ($ir['email_optin'] == 0) ?
            $g = "	<option value='1'>Opt-In</option>
					<option value='0'>Opt-Out</option>" :
            $g = "	<option value='0'>Opt-Out</option>
					<option value='1'>Opt-In</option>";
        $csrf = request_csrf_html('prefs_changeopt');
        $optsetting = ($ir['email_optin'] == 1) ? "Opt-in" : "Opt-out";
        echo "<table class='table table-bordered'>
		<form method='post'>
		<tr>
			<th colspan='2'>
				Use this form to opt-in or out of emails from the game. You are currently {$optsetting} for game emails.
			</th>
		</tr>
		<tr>
			<th>
				Opt-Setting
			</th>
			<td>
				<select name='opt' class='form-control' type='dropdown'>
					{$g}
				</select>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<input type='submit' value='Change Opt-Setting' class='btn btn-primary'>
			</td>
		</tr>
		{$csrf}
		</form>
		</table>";
    }
}

function changeemail()
{
    global $db,$userid,$api,$h,$ir,$set;
    if (isset($_POST['email']))
    {
        //Verify CSRF check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("email_form_pref", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }
        if (!isset($_POST['email']) || !valid_email(stripslashes($_POST['email']))) {
            alert('danger', "Uh Oh!", "It appears you have input an invalid or non-existent email address. Go back and try again.");
            die($h->endpage());
        }
        if ($ir['email'] == $_POST['email'])
        {
            alert('danger',"Uh Oh!","You cannot change your email to your current email address.");
            die($h->endpage());
        }
        $e_email = $db->escape(stripslashes($_POST['email']));
        $q2 = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `email` = '{$e_email}'");
        if ($db->fetch_single($q2) != 0)
        {
            alert('danger', "Uh Oh!", "The email you've chosen is already in use by another player.");
            die($h->endpage());
        }
        $url=determine_game_urlbase();
        $WelcomeMSGEmail="This is an automated email to let you know that your account in Chivalry is Dead, {$ir['username']} [{$userid}], has had its linked email address changed from {$ir['email']} to {$e_email}. If you have any questions or concerns, please contact a staff member in-game!<br /> -{$set['WebsiteName']}<br /><a href='https://{$url}'>https://{$url}</a>";
        $api->SystemSendEmail($ir['email'],$WelcomeMSGEmail,"{$set['WebsiteName']} Email Change",$set['sending_email']);
        $db->query("UPDATE `users` SET `email` = '{$e_email}', `force_logout` = 'true' WHERE `userid` = {$userid}");
        alert('success',"Success!","You have successfully changed your email from {$ir['email']} to {$e_email}. Please log in again.",true,'logout.php');
        die($h->endpage());
    } else {
        $csrf=request_csrf_html('email_form_pref');
        echo "<h3>Changing Account Email</h3><hr />
        Use this form to change your account's email address. Please input a valid email address. Please contact the administration if you wish to lock your email address to changes.<br />
        <form method='post'>
            {$csrf}
            <input type='email' name='email' value='{$ir['email']}' class='form-control' required='1' id='email' onkeyup='CheckEmail(this.value);'>
            <div id='emailresult' class='invalid-feedback'></div>
            <input type='submit' name='Change Email' class='btn btn-primary'>
        </form>";
    }
}

function descchange()
{
	global $db, $h, $userid, $ir, $api;
    if (isset($_POST['desc'])) {

        //Verify CSRF check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("pref_changedesc", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }

        //Make sure the POST is safe to work with.
        $ament = $db->escape(str_replace("\n", "<br />", strip_tags(htmlentities(stripslashes($_POST['desc'])))));
		
		$length=strlen($ament);
		if ($length > 1000)
		{
			alert('danger', "Uh Oh!", "Your player description may only be 1,000 characters at maximum. You entered {$length}.", true, 'preferences.php');
			die($h->endpage());
		}

        //Update the guild's announcement.
        $db->query("UPDATE `users` SET `description` = '{$ament}' WHERE `userid` = {$userid}");
        alert('success', "Success!", "You have updated your profile's description.", true, 'preferences.php');
        $api->SystemLogsAdd($userid, 'preferences', "Changed profile description.");
    } else {
        //Escape the announcement for safety reasons.
        $am_for_area = strip_tags($ir['description']);
        $csrf = request_csrf_html('pref_changedesc');
        echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					You may change your profile description here. Text only. No BBCode. 1,000 character maximum.
				</th>
			</tr>
			<tr>
				<th>
					Your Description
				</th>
				<td>
					<textarea class='form-control' name='desc'>{$am_for_area}</textarea>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='Change Description' class='btn btn-primary'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>";
    }
}

function quicklinks()
{
	global $db,$userid,$ir,$h,$api;
	if (isset($_POST['dungeon']))
	{
		$dungeon = (isset($_POST['dungeon']) && is_numeric($_POST['dungeon'])) ? abs($_POST['dungeon']) : 1;
		$infirmary = (isset($_POST['infirmary']) && is_numeric($_POST['infirmary'])) ? abs($_POST['infirmary']) : 1;
		if ($dungeon < 1 || $dungeon > 6)
		{
			alert("danger","Uh Oh!","You have selected an invalid dungeon item.");
			die($h->endpage());
		}
		if ($infirmary < 1 || $infirmary > 8)
		{
			alert("danger","Uh Oh!","You have selected an invalid infirmary item.");
			die($h->endpage());
		}
		$db->query("UPDATE `user_settings` SET `ditem` = {$dungeon}, `iitem` = {$infirmary} WHERE `userid` = {$userid}");
		alert('success',"Success!","You have successfully updated your infirmary and dungeon quick links",true,'preferences.php');
        $api->SystemLogsAdd($userid, 'preferences', "Updated quick links.");
	}
	else
	{
		echo "Please select the items you wish to use for your dungeon and infirmary quick links.<br />
		<form method='post'>
			<div class='row'>
				<div class='col-md-6'>
					Dungeon Item
					<select name='dungeon' class='form-control'>
						<option value='6'>Bail Self</option>
						<option value='5'>Bust Self</option>
						<option value='1'>Lockpick</option>
						<option value='2'>Dungeon Key</option>
						<option value='3'>Dungeon Key Set</option>
                        <option value='4'>Negative Begone</option>
					</select>
				</div>
				<div class='col-md-6'>
					Infirmary Item
					<select name='infirmary' class='form-control'>
						<option value='1'>Leech</option>
						<option value='2'>Linen Wrap</option>
                        <option value='3'>Acupuncture Needle</option>
                        <option value='4'>Med-go-bye</option>
                        <option value='5'>Priority Voucher</option>
                        <option value='6'>Negative Begone</option>
						<option value='7'>Medical Package</option>
                        <option value='8'>Infirmary Heal</option>
					</select>
				</div>
			</div>
			<br />
			<input type='submit' value='Change Quick Items' class='btn btn-primary'>
		</form>";
	}
}

function userdropdown()
{
	global $db,$h,$ir,$api,$userid;
	if (isset($_POST['do']))
    {
		if ($_POST['do'] == 'list')
		{
			$db->query("UPDATE `user_settings` SET `dropdown` = 0 WHERE `userid` = {$userid}");
			alert('success',"Success!","You have set your user input to the dropdown.",true,'preferences.php');
            $api->SystemLogsAdd($userid, 'preferences', "Updated User Input to Dropdown.");
		}
		else
		{
			$db->query("UPDATE `user_settings` SET `dropdown` = 1 WHERE `userid` = {$userid}");
			alert('success',"Success!","You have set your user input to number input.",true,'preferences.php');
            $api->SystemLogsAdd($userid, 'preferences', "Updated User Input to Numbers.");
		}
    }
    else
    {
        echo "You can choose your user input method of choice here. Dropdown is default.<br />
        <form method='post'>
            <input type='hidden' value='list' name='do'>
            <input type='submit' class='btn btn-primary' value='Dropdown'>
        </form>
		<form method='post'>
            <input type='hidden' value='input' name='do'>
            <input type='submit' class='btn btn-primary' value='User ID Input'>
        </form>";
    }
}

function forumalert()
{
	global $db,$userid,$api,$h;
    if (isset($_POST['do']))
    {
		if ($_POST['do'] == 'disable')
		{
			$db->query("UPDATE `user_settings` SET `forum_alert` = 0 WHERE `userid` = {$userid}");
			alert('success',"Success!","You have successfully disabled forum notifications.",true,'preferences.php');
            $api->SystemLogsAdd($userid, 'preferences', "Disabled forum notifications.");
		}
		else
		{
			$db->query("UPDATE `user_settings` SET `forum_alert` = 1 WHERE `userid` = {$userid}");
			alert('success',"Success!","You have successfully enabled forum notifications.",true,'preferences.php');
            $api->SystemLogsAdd($userid, 'preferences', "Enabled forum notifications.");
		}
    }
    else
    {
        echo "You can choose to receive notifications if players respond to your forum threads. You will not get notifications 
		if you respond to your own threads. By default, this is off. You <i>must</i> opt-in to receive notifications.<br />
        <form method='post'>
            <input type='hidden' value='disable' name='do'>
            <input type='submit' class='btn btn-primary' value='Disable Notifications'>
        </form>
		<form method='post'>
            <input type='hidden' value='enable' name='do'>
            <input type='submit' class='btn btn-primary' value='Enable Notifications'>
        </form>";
    }
}

function classreset()
{
    global $db,$userid,$api,$h,$ir;
    echo "<h3>Class Change</h3><hr />";
    if (isset($_POST['class']))
    {
        if ($_POST['class'] == $ir['class'])
        {
            alert('danger',"Uh Oh!","Why would you want to waste your only class reset to select the class you already are?");
            die($h->endpage());
        }
        if (($_POST['class'] != 'Guardian') && ($_POST['class'] != 'Warrior') && ($_POST['class'] != 'Rogue'))
        {
            alert('danger',"Uh Oh!","Invalid class selected.");
            die($h->endpage());
        }
        if ($ir['iq'] < 50000)
        {
            alert('danger',"Uh Oh!","You need at least 50,000 IQ to change your class.");
            die($h->endpage());
        }
		$tq=$db->query("SELECT * FROM `user_equips` WHERE `userid` = {$ir['userid']}");
		if ($db->num_rows($tq) > 0)
		{
			alert('danger',"Uh Oh!", "Please remove any and all trinkets before using this.");
            die($h->endpage());
		}
        if (($ir['equip_primary'] + $ir['equip_secondary'] + $ir['equip_armor']) != 0)
        {
            alert('danger',"Uh Oh!", "Please remove your equipment before using this feature.");
            die($h->endpage());
        }
        if ($ir['class'] == 'Guardian')
        {
            if ($_POST['class'] == 'Warrior')
            {
                $strength=$ir['guard']-($ir['guard']*0.25);
                $agility=$ir['strength']-($ir['strength']*0.25);
                $guard=$ir['agility']-($ir['agility']*0.25);
            }
            if ($_POST['class'] == 'Rogue')
            {
                $strength=$ir['agility']-($ir['agility']*0.25);
                $agility=$ir['guard']-($ir['guard']*0.25);
                $guard=$ir['strength']-($ir['strength']*0.25);
            }
        }
        if ($ir['class'] == 'Rogue')
        {
            if ($_POST['class'] == 'Warrior')
            {
                $strength=$ir['agility']-($ir['agility']*0.25);
                $agility=$ir['guard']-($ir['guard']*0.25);
                $guard=$ir['strength']-($ir['strength']*0.25);
            }
            if ($_POST['class'] == 'Guardian')
            {
                $strength=$ir['guard']-($ir['guard']*0.25);
                $agility=$ir['strength']-($ir['strength']*0.25);
                $guard=$ir['agility']-($ir['agility']*0.25);
            }
        }
        if ($ir['class'] == 'Warrior')
        {
            if ($_POST['class'] == 'Rogue')
            {
                $strength=$ir['guard']-($ir['guard']*0.25);
                $agility=$ir['strength']-($ir['strength']*0.25);
                $guard=$ir['agility']-($ir['agility']*0.25);
            }
            if ($_POST['class'] == 'Guardian')
            {
                $strength=$ir['agility']-($ir['agility']*0.25);
                $agility=$ir['guard']-($ir['guard']*0.25);
                $guard=$ir['strength']-($ir['strength']*0.25);
            }
        }
        $strength=round($strength);
        $agility=round($agility);
        $guard=round($guard);
        $db->query("UPDATE `userstats` 
                    SET `strength` = {$strength}, 
                    `agility` = {$agility}, 
                    `guard` = {$guard}, 
                    `iq` = `iq` - 50000 
                    WHERE `userid` = {$userid}");
        $db->query("UPDATE `users` SET `class` = '{$_POST['class']}' WHERE `userid` = {$userid}");
        $api->SystemLogsAdd($userid, 'preferences', "Changed class to {$_POST['class']}.");
        alert('success',"Success!","You have successfully changed your class to {$_POST['class']}.",true,'preferences.php');
    }
    else
    {
        echo "Don't like the class you chose at registration? No problem! Here you may change your class. However, 
        there are a few catches. Your stats will be changed to reflect the class you change yourself into. If you're becoming a 
        Warrior from a Guardian, Your Strength will become your Agility, your Agility will become your Guard, and your Guard will become your 
        Strength. If this seems confusing to you, please contact staff before you act. You will also receive a 25% reduction on 
        your stats. You must also have at least 50,000 IQ to use this feature. You must also have no armor or weapons equipped.
        <br /> <b>You are currently part of the {$ir['class']} class.</b><br />
        Warrior: Extra Strength, Normal Agility, Less Guard<br />
        Rogue: Less Strength, Extra Agility, Normal Guard<br />
        Guardian: Normal Strength, Less Agility, Extra Guard<br />
        <form method='post'>
            <select name='class' id='class' class='form-control' type='dropdown'>
                <option value='Warrior'>Warrior</option>
                <option value='Rogue'>Rogue</option>
                <option value='Guardian'>Guardian</option>
            </select>
            <input type='submit' value='Change Class' class='btn btn-primary'>
        </form>";
    }
}

function tuttoggle()
{
	global $db,$userid,$api,$h;
	$userCount=getCurrentUserPref('tutorialToggle', 'true');
	if (isset($_POST['topics'])) {
			if (($_POST['topics'] != 'true') && ($_POST['topics'] != 'false'))
			{
				alert('danger', "Uh Oh!", "Invalid tutorial toggle setting.");
				die($h->endpage());
			}
            alert('success', "Success!", "You have successfully set your tutorial toggle to {$_POST['topics']}.", true, 'preferences.php');
            setCurrentUserPref('tutorialToggle',$_POST['topics']);
            $api->SystemLogsAdd($userid, 'preferences', "Changed tutorial toggle to {$_POST['topics']}.");
            die($h->endpage());
    } else {
		echo "Here you may toggle the tutorial. By default, its enabled. You have set your tutorial toggle to {$userCount}.
		<div class='row'>
			<div class='col-md'>
				<form method='post'>
					<input type='hidden' name='topics' value='true' class='form-control'>
					<input type='submit' class='btn btn-danger' value='Enable Tutorial'>
				</form>
			</div>
			<div class='col-md'>
				<form method='post'>
					<input type='hidden' name='topics' value='false' class='form-control'>
					<input type='submit' class='btn btn-success' value='Disable Tutorial'>
				</form>
			</div>
		</div>";
	}
}

function icontoggle()
{
	global $db,$userid,$api,$h;
    if (isset($_POST['do']))
    {
		if ($_POST['do'] == 'disable')
		{
			$db->query("UPDATE `user_settings` SET `icons` = 0 WHERE `userid` = {$userid}");
			alert('success',"Success!","You have successfully disabled item icons.",true,'preferences.php');
            $api->SystemLogsAdd($userid, 'preferences', "Disabled item icons.");
		}
		else
		{
			$db->query("UPDATE `user_settings` SET `icons` = 1 WHERE `userid` = {$userid}");
			alert('success',"Success!","You have successfully enabled item icons.",true,'preferences.php');
            $api->SystemLogsAdd($userid, 'preferences', "Enabled item icons.");
		}
    }
    else
    {
        echo "Here you may disable in-game item icons. Doing so may allow the game to load quicker on lower-end machines.<br />
        <form method='post'>
            <input type='hidden' value='disable' name='do'>
            <input type='submit' class='btn btn-primary' value='Disable Icons'>
        </form>
		<form method='post'>
            <input type='hidden' value='enable' name='do'>
            <input type='submit' class='btn btn-primary' value='Enable Icons'>
        </form>";
    }
}

function themechange()
{
    global $db, $userid, $h, $ir, $api;
    if (isset($_POST['theme'])) {
        $_POST['theme'] = (isset($_POST['theme']) && is_numeric($_POST['theme'])) ? abs($_POST['theme']) : 1;
        if ($_POST['theme'] < 1 || $_POST['theme'] > 8) {
            alert('danger', "Uh Oh!", "The theme you wish to load is not valid.");
            die($h->endpage());
        }
		else {
            alert('success', "Success!", "You have successfully changed your theme.", true, 'preferences.php');
            $db->query("UPDATE `user_settings` SET `theme` = {$_POST['theme']} WHERE `userid` = {$userid}");
            $api->SystemLogsAdd($userid, 'preferences', "Changed theme to Theme ID {$_POST['theme']}.");
            die($h->endpage());
        }
    } else {
        echo "Select the theme you wish to see as you play Chivalry is Dead. Not a fan of your UI? You can change it <a href='?action=newui'>here</a>.
		<hr />
		<div class='row'>
			<div class='col-md'>
				Original<br />
				<img src='https://res.cloudinary.com/dydidizue/image/upload/v1590464837/themes/2020-default.png' class='img-thumbnail img-responsive'>
				<form method='post'>
					<input type='hidden' value='1' name='theme'>
					<input type='submit' class='btn btn-primary' value='Original'>
				</form>
			</div>
			<div class='col-md'>
				Darkly<br />
				<img src='https://res.cloudinary.com/dydidizue/image/upload/v1590464838/themes/2020-darkly.png' class='img-thumbnail img-responsive'>
				<form method='post'>
					<input type='hidden' value='2' name='theme'>
					<input type='submit' class='btn btn-primary' value='Darkly'>
				</form>
			</div>
			<div class='col-md'>
				Cerulean<br />
				<img src='https://res.cloudinary.com/dydidizue/image/upload/v1590464837/themes/2020-cerulean.png' class='img-thumbnail img-responsive'>
					<form method='post'>
						<input type='hidden' value='6' name='theme'>
						<input type='submit' class='btn btn-primary' value='Cerulean'>
					</form>
			</div>
		</div>
		<hr />
		<div class='row'>
			<div class='col-md'>
				Cyborg<br />
				<img src='https://res.cloudinary.com/dydidizue/image/upload/v1590464837/themes/2020-cyborg.png' class='img-thumbnail img-responsive'>
					<form method='post'>
						<input type='hidden' value='4' name='theme'>
						<input type='submit' class='btn btn-primary' value='Cyborg'>
					</form>
			</div>
			<div class='col-md'>
				United<br />
				<img src='https://res.cloudinary.com/dydidizue/image/upload/v1590464839/themes/2020-united.png' class='img-thumbnail img-responsive'>
					<form method='post'>
						<input type='hidden' value='5' name='theme'>
						<input type='submit' class='btn btn-primary' value='United'>
					</form>
			</div>
			<div class='col-md'>
				Slate<br />
				<img src='https://res.cloudinary.com/dydidizue/image/upload/v1590464839/themes/2020-slate.png' class='img-thumbnail img-responsive'>
					<form method='post'>
						<input type='hidden' value='3' name='theme'>
						<input type='submit' class='btn btn-primary' value='Slate'>
					</form>
			</div>
		</div>
		<hr />
		<div class='row'>
			<div class='col-md'>
				Castle<br />
				<img src='https://res.cloudinary.com/dydidizue/image/upload/v1590464839/themes/2020-castle.png' class='img-thumbnail img-responsive'>
					<form method='post'>
						<input type='hidden' value='7' name='theme'>
						<input type='submit' class='btn btn-primary' value='Castle'>
					</form>
			</div>
			<div class='col-md'>
				Sunset<br />
				<img src='https://res.cloudinary.com/dydidizue/image/upload/v1590464841/themes/2020-sunset.png' class='img-thumbnail img-responsive'>
					<form method='post'>
						<input type='hidden' value='8' name='theme'>
						<input type='submit' class='btn btn-primary' value='Sunset'>
					</form>
			</div>
		</div>";
    }
}

function steamlink()
{
    global $db,$userid,$api,$h;
    require ('lib/steamauth/steamauth.php');
    $q=$db->query("/*qc=on*/SELECT * FROM `steam_account_link` WHERE `steam_linked` = {$userid}");
    if ($db->num_rows($q) != 0)
    {
        alert("danger","Uh Oh!","You already have a Steam Account linked to your game account.",true,'preferences.php?action=menu');
        die($h->endpage());
    }
    if(!isset($_SESSION['steamid'])) {
        loginbutton2();
    }
    else
    {
        include ('lib/steamauth/userInfo.php');
        $check=$db->query("/*qc=on*/SELECT * FROM `steam_account_link` WHERE `steam_id` = {$steamprofile['steamid']}");
        if ($db->num_rows($check) != 0)
        {
            alert('danger',"Uh Oh!","That Steam Account has already been linked to a Chivalry is Dead account. Try again with another Steam Account.");
            die($h->endpage());
        }
		$api->SystemLogsAdd($userid, 'preferences', "Linked Steam Account ID: {$steamprofile['steamid']}}");
        $db->query("INSERT INTO `steam_account_link` (`steam_linked`, `steam_id`) VALUES ('{$userid}', '{$steamprofile['steamid']}')");
        alert("success","Success!","You have successfully linked Steam Account ID: <b>{$steamprofile['steamid']}</b> to your Chivalry is Dead account. You may now use it to log in.",true,'preferences.php?action=menu');
    }
}

function resetacc()
{
    global $db,$userid,$api,$h,$ir;
    if ($ir['reset'] == 6)
	{
		alert('danger',"Uh Oh!","You can only reset your account 5 times.",true,'preferences.php');
		die($h->endpage());
	}
	if ($ir['level'] < 500)
	{
		alert('danger',"Uh Oh!","You can only reset your account when you are over level 500.",true,'preferences.php');
		die($h->endpage());
	}
	if (($ir['equip_armor'] + $ir['equip_primary'] + $ir['equip_secondary']) != 0) 
	{
		alert('danger',"Uh Oh!","You can only use this feature when you have no equipment on you.",true,'preferences.php');
		die($h->endpage());
	}
	$tq=$db->query("SELECT * FROM `user_equips` WHERE `userid` = {$ir['userid']}");
	if ($db->num_rows($tq) > 0)
	{
		alert('danger',"Uh Oh!", "Please remove any and all trinkets before using this.");
		die($h->endpage());
	}
	if (isset($_POST['reset']))
	{
		alert('info',"","Chivalry is Dead is attempting to reset your account... if you run into errors please contact staff.",false);
		$accquery="UPDATE `users` SET `xp` = 0, `level` = 1, `will` = 100, `maxwill` = 100, `hp` = 100, `maxhp` = 100,
		`energy` = 24, `maxenergy` = 24, `brave` = 10, `maxbrave` = 10, `vip_days` = `vip_days` + 3, `job` = 0, `jobrank` = 0, 
		`primary_currency` = 0, `secondary_currency` = 0, `location` = 1, `course` = 0, `course_complete` = 0, `busts` = 0, `deaths` = 0,
		`kills` = 0, `tokenbank` = -1, `bigbank` = -1, `bank` = -1, `vaultbank` = -1, `equip_potion` = 0
		WHERE `userid` = {$userid}";
		$statquery="UPDATE `userstats` SET `strength` = 1000, `agility` = 1000, `guard` = 1000, `iq` = 100, `labor` = 1000 WHERE `userid` = {$userid}";
		$mail="Welcome to Chivalry is Dead, {$ir['username']}. We hope you stay a while and hang out. To get started,
        check out the Explore page and visit the [url=hexbags.php]Hexbags[/url] under the Gambling tab. Here you will gain many awesome starter items. Should
        your fortune be unkind, your inventory has 50 Dungeon Keys and 50 Linen Wraps to get you out of the Infirmary and Dungeon when needed.";
		$randophrase=randomizer();
		/*$api->UserGiveItem();
		$api->UserGiveItem();
		$api->UserGiveItem();*/
		$hex=$ir['autohex'];
		$bor=$ir['autobor'];
		$reset=$ir['reset'];
		$bum=$ir['autobum'];
		echo "Deleting your inventory... ";
			if ($db->query("DELETE FROM `inventory` WHERE `inv_userid` = {$userid}"))
				echo "...inventory deleted.";
			else
				echo "...failed to remove inventory.";
		echo "<br />Deleting item market offers... ";
			if ($db->query("DELETE FROM `itemmarket` WHERE `imADDER` = {$userid}"))
				echo "...item market offers deleted.";
			else
			echo "...failed to delete item market offers.";
		echo "<br />Deleting item request offers... ";
			if ($db->query("DELETE FROM `itemrequest` WHERE `irUSER` = {$userid}"))
				echo "...item request offers deleted.";
			else
				echo "...failed to remove item request offers.";
		echo "<br />Deleting Chivalry Token market offers... ";
			if ($db->query("DELETE FROM `sec_market` WHERE `sec_user` = {$userid}"))
				echo "...Chivalry Token Market offers deleted.";
			else
				echo "...failed to remove Chivalry Token Market offers.";
		echo "<br />Updating account details... ";
			if ($db->query($accquery))
				echo "...done.";
			else
				echo "...fail.";
		echo "<br />Updating user stats... ";
			if ($db->query($statquery))
				echo "...done.";
			else
				echo "...fail.";
		echo "<br />Updating user settings table to default... ";
		$db->query("DELETE FROM `user_settings` WHERE `userid` = {$userid}");
		$db->query("DELETE FROM `user_logging` WHERE `userid` = {$userid}");
		$db->query("INSERT INTO `user_settings` (`userid`) VALUES ('{$userid}')");
        $db->query("UPDATE `user_settings` SET `security_key` = '{$randophrase}', `theme` = 7, `autobor` = {$bor} + 3000, `autohex` = {$hex} + 300, `autobum` = {$bum} WHERE `userid` = {$userid}");
		echo "...done<br />Giving starter items... ";
			//Give starter items.
			$api->UserGiveItem($userid,6,50);
			$api->UserGiveItem($userid,30,50);
			$api->UserGiveItem($userid,33,3000);
			$api->UserGiveCurrency($userid,'primary',10000);
			$api->UserGiveCurrency($userid,'secondary',50);
		echo "...starter items given successfully.";
		echo "<br />Deleting skills tree... ";
			if ($db->query("DELETE FROM `user_skills` WHERE `userid` = {$userid}"))
				echo "...deleted successfully.";
			else
				echo "...failed.";
		echo "<br />Resetting player's achievements... ";
			if ($db->query("DELETE FROM `achievements_done` WHERE `userid` = {$userid}"))
				echo "...success!";
			else
				echo "...failed";
		echo "<br />Removing active bank investments... ";
			if ($db->query("DELETE FROM `bank_investments` WHERE `userid` = {$userid}"))
				echo "...success!";
			else
				echo "...failed";
		echo "<br />Resetting academy courses... ";
			if ($db->query("DELETE FROM `academy_done` WHERE `userid` = {$userid}"))
				echo "...success!";
			else
				echo "...failed";
		echo "<br />Finishing up... we'll just be a moment.";
			$api->GameAddMail($userid,"Welcome to Chivalry is Dead",$mail,1);
			$db->query("DELETE FROM `mining` WHERE `userid` = {$userid}");
			$db->query("UPDATE `user_settings` SET `reset` = {$reset} + 1 WHERE `userid` = {$userid}");
		alert('success',"Success!","Your account has been reset. Please log in to continue.",true,'logout.php');
		$api->SystemLogsAdd($userid, 'preferences', "Successfully reset account. (Reset # {$reset})");
		$realReset = $reset - 1;
		if ($realReset == 1)
		{
			$api->UserGiveItem($userid,346,1);
		}
		if ($realReset == 2)
		{
			$api->UserGiveItem($userid,346,1);
			$api->UserGiveItem($userid,347,1);
		}
		if ($realReset == 3)
		{
			$api->UserGiveItem($userid,346,1);
			$api->UserGiveItem($userid,347,1);
			$api->UserGiveItem($userid,348,1);
		}
		if ($realReset == 4)
		{
			$api->UserGiveItem($userid,346,1);
			$api->UserGiveItem($userid,347,1);
			$api->UserGiveItem($userid,348,1);
			$api->UserGiveItem($userid,349,1);
		}
		if ($realReset == 3)
		{
			$api->UserGiveItem($userid,346,1);
			$api->UserGiveItem($userid,347,1);
			$api->UserGiveItem($userid,348,1);
			$api->UserGiveItem($userid,349,1);
			$api->UserGiveItem($userid,350,1);
		}
		die();
	}
	else
	{
		echo "Everything about your account will reset. Items on the market will be removed. Your inventory will be wiped. Your stats will be reset. You will be cleared of any currency you may have. Consider it a fresh start.<br />
		Would you like to go through with it? This cannot be undone for whatever reason.
		<form method='post'>
			<input type='hidden' value='yes' name='reset'>
			<input type='submit' class='btn btn-danger' value='Yes, reset!'>
		</form>";
	}
}

function loginlogs()
{
    global $db,$userid,$api,$h;
	echo "<h3>Login Logs</h3><hr />
	This is mainly for your peace of mind. We expose the time, date, and IP Address of the action You should never show this screen to anyone, no matter what they promise.
	Chivalry is Dead Staff will never ask for this page either. They have ways to get this information using the in-game staff logs.
	<table class='table table-bordered table-hover table-striped'>
    		<tr>
    			<th>Log Time</th>
    			<th>Action</th>
				<th>Action IP</th>
    		</tr>
       ";
    $q =
        $db->query(
            "/*qc=on*/SELECT `log_user`, `log_time`, `log_text`, `log_ip`
                     FROM `logs`
					 WHERE `log_type` = 'login' 
					 AND `log_user` = {$userid} 
                     ORDER BY `log_time` DESC
                     LIMIT 20");
    while ($r = $db->fetch_row($q)) {
        $un = $db->fetch_single($db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `userid` = {$r['log_user']}"));
        echo "
		<tr>
        	<td>" . DateTime_Parse($r['log_time'])
            . "</td>
        	<td>{$r['log_text']}</td>
			<td>{$r['log_ip']}</td>
           ";
        echo '</tr>';
    }
    $db->free_result($q);
    echo "
    </table>";
	$api->SystemLogsAdd($userid, 'preferences', "Viewed their login logs.");
}

function newui()
{
    global $db,$userid,$api,$h;
	$userCount=getCurrentUserPref('oldUI',0);
	if (isset($_POST['topics'])) {
			$_POST['topics'] = (isset($_POST['topics']) && is_numeric($_POST['topics'])) ? abs($_POST['topics']) : 0;
			if (($_POST['topics'] != 1) && ($_POST['topics'] != 0))
			{
				alert('danger', "Uh Oh!", "You've specified an invalid option.", true, 'preferences.php');
				die($h->endpage());
			}
            alert('success', "Success!", "You have successfully updated this preference. Your results will update on the next page load.", true, 'preferences.php');
            setCurrentUserPref('oldUI',$_POST['topics']);
            $api->SystemLogsAdd($userid, 'preferences', "Changed guild xp auto donate to {$_POST['topics']}%.");
            die($h->endpage());
    } else {
		echo "<h3>UI Toggle</h3><hr />
		Select the UI type you wish to have. Note, that the game will be developed with the current actively developed UI.<br />
		<div class='row'>
			<div class='col-sm'>
				<form method='post'>
					<input type='hidden' name='topics' value='0'>
					<input type='submit' class='btn btn-primary' value='New UI'>
				</form>
			</div>
			<div class='col-sm'>
				<form method='post'>
					<input type='hidden' name='topics' value='1'>
					<input type='submit' class='btn btn-primary' value='Old UI (Phased out 08/20)'>
				</form>
			</div>
		</div>";
	}
	
}

function homecount()
{
	global $db,$userid,$api,$h;
	$autoDonateXP=getCurrentUserPref('autoDonateXP',0);
	$notifCount=getCurrentUserPref('notifView',15);
	$mailCount=getCurrentUserPref('mailView',15);
	$postCount=getCurrentUserPref('postView',20);
	$topicCount=getCurrentUserPref('topicView',20);
	$vipLogCount=getCurrentUserPref('vipLogView',5);
	$hofCount=getCurrentUserPref('hofView',20);
	$announceCount=getCurrentUserPref('announceView',1000);
	$guildNotifCount=getCurrentUserPref('guildNotifView',10);
	if (isset($_POST['submit']))
	{
		$_POST['mailCount'] = (isset($_POST['mailCount']) && is_numeric($_POST['mailCount'])) ? abs($_POST['mailCount']) : $mailCount;
		$_POST['notifCount'] = (isset($_POST['notifCount']) && is_numeric($_POST['notifCount'])) ? abs($_POST['notifCount']) : $notifCount;
		$_POST['guildXpCount'] = (isset($_POST['guildXpCount']) && is_numeric($_POST['guildXpCount'])) ? abs($_POST['guildXpCount']) : $autoDonateXP;
		$_POST['postCount'] = (isset($_POST['postCount']) && is_numeric($_POST['postCount'])) ? abs($_POST['postCount']) : $postCount;
		$_POST['topicCount'] = (isset($_POST['topicCount']) && is_numeric($_POST['topicCount'])) ? abs($_POST['topicCount']) : $topicCount;
		$_POST['vipLogCount'] = (isset($_POST['vipLogCount']) && is_numeric($_POST['vipLogCount'])) ? abs($_POST['vipLogCount']) : $vipLogCount;
		$_POST['hofCount'] = (isset($_POST['hofCount']) && is_numeric($_POST['hofCount'])) ? abs($_POST['hofCount']) : $hofCount;
		$_POST['announceCount'] = (isset($_POST['announceCount']) && is_numeric($_POST['announceCount'])) ? abs($_POST['announceCount']) : $announceCount;
		$_POST['guildNotifCount'] = (isset($_POST['guildNotifCount']) && is_numeric($_POST['guildNotifCount'])) ? abs($_POST['guildNotifCount']) : $guildNotifCount;
		//check mail count
		if (($_POST['mailCount'] < 0) || ($_POST['mailCount'] > 100))
		{
			alert('danger', "Uh Oh!", "Mail view count must be greater than 0, and less than 50.");
			die($h->endpage());
		}
		//check notif count
		if (($_POST['notifCount'] < 0) || ($_POST['notifCount'] > 100))
		{
			alert('danger', "Uh Oh!", "Notification view count must be greater than 0, and less than 100.");
			die($h->endpage());
		}
		//check post count
		if (($_POST['postCount'] < 0) || ($_POST['postCount'] > 100))
		{
			alert('danger', "Uh Oh!", "Forum reply view count must be greater than 0, and less than 100.");
			die($h->endpage());
		}
		//check post count
		if (($_POST['topicCount'] < 0) || ($_POST['topicCount'] > 100))
		{
			alert('danger', "Uh Oh!", "Forum topic view count must be greater than 0, and less than 100.");
			die($h->endpage());
		}
		//check guild xp auto donate
		if (($_POST['guildXpCount'] < 0) || ($_POST['guildXpCount'] > 50))
		{
			alert('danger', "Uh Oh!", "You may only donate up to 50% of your total experience to your guild.");
			die($h->endpage());
		}
		//check vip log count
		if (($_POST['mailCount'] < 0) || ($_POST['mailCount'] > 24))
		{
			alert('danger', "Uh Oh!", "VIP Logs view count must be greater than 0, and less than 24.");
			die($h->endpage());
		}
		//check hall of fame count
		if (($_POST['hofCount'] < 5) || ($_POST['hofCount'] > 100))
		{
			alert('danger', "Uh Oh!", "Hall of Fame view count must be greater than 5, and less than 100.");
			die($h->endpage());
		}
		//check announce count
		if (($_POST['announceCount'] < 1) || ($_POST['announceCount'] > 1000))
		{
			alert('danger', "Uh Oh!", "Announcements view count must be greater than 1 and less than 1,000.");
			die($h->endpage());
		}
		//check notif count
		if (($_POST['guildNotifCount'] < 0) || ($_POST['guildNotifCount'] > 100))
		{
			alert('danger', "Uh Oh!", "Guild Notification view count must be greater than 0, and less than 100.");
			die($h->endpage());
		}
		setCurrentUserPref('autoDonateXP',$_POST['guildXpCount']);
		setCurrentUserPref('mailView',$_POST['mailCount']);
		setCurrentUserPref('notifView',$_POST['notifCount']);
		setCurrentUserPref('postView',$_POST['postCount']);
		setCurrentUserPref('topicView',$_POST['topicCount']);
		setCurrentUserPref('vipLogView',$_POST['vipLogCount']);
		setCurrentUserPref('hofView',$_POST['hofCount']);
		setCurrentUserPref('announceView',$_POST['announceCount']);
		setCurrentUserPref('guildNotifView',$_POST['guildNotifCount']);
		alert('success', "Success!", "You have successfully updated your view count preference.", true, 'preferences.php');
		$api->SystemLogsAdd($userid, 'preferences', "Updated view count preference.");
	}
	else
	{
		echo "<form method='post'>";
		echo "
		<div class='row'>
			<div class='col-sm-12 col-md-6 col-xl-4 col-xxl-3 col-xxxl-2'>
				<div class='card'>
					<div class='card-body'>
						<h5 class='card-title'>Mail Count</h5>
						<input type='number' name='mailCount' value='{$mailCount}' min='1' max='100' required='1' class='form-control' placeholder='Default = 15'>
					</div>
				</div>
			</div>
            <br />
			<div class='col-sm-12 col-md-6 col-xl-4 col-xxl-3 col-xxxl-2'>
				<div class='card'>
					<div class='card-body'>
						<h5 class='card-title'>Notification Count</h5>
						<input type='number' name='notifCount' value='{$notifCount}' min='1' max='100' required='1' class='form-control' placeholder='Default = 15'>
					</div>
				</div>
			</div>
            <br />
			<div class='col-sm-12 col-md-6 col-xl-4 col-xxl-3 col-xxxl-3'>
				<div class='card'>
					<div class='card-body'>
						<h5 class='card-title'>Guild XP Auto Donate</h5>
						<input type='number' name='guildXpCount' value='{$autoDonateXP}' min='0' max='50' required='1' class='form-control' placeholder='Default = 0'>
					</div>
				</div>
			</div>
            <br />
			<div class='col-sm-12 col-md-6 col-xl-4 col-xxl-3 col-xxxl-2'>
				<div class='card'>
					<div class='card-body'>
						<h5 class='card-title'>Forum Post Count</h5>
						<input type='number' name='postCount' value='{$postCount}' min='1' max='100' required='1' class='form-control' placeholder='Default = 20'>
					</div>
				</div>
			</div>
            <br />
			<div class='col-sm-12 col-md-6 col-xl-4 col-xxl-3 col-xxxl-2'>
				<div class='card'>
					<div class='card-body'>
						<h5 class='card-title'>Forum Topic Count</h5>
						<input type='number' name='topicCount' value='{$topicCount}' min='1' max='100' required='1' class='form-control' placeholder='Default = 20'>
					</div>
				</div>
			</div>
            <br />
			<div class='col-sm-12 col-md-6 col-xl-4 col-xxl-3 col-xxxl-2'>
				<div class='card'>
					<div class='card-body'>
						<h5 class='card-title'>VIP Log Count</h5>
						<input type='number' name='vipLogCount' value='{$vipLogCount}' min='1' max='24' required='1' class='form-control' placeholder='Default = 5'>
					</div>
				</div>
			</div>
            <br />
			<div class='col-sm-12 col-md-6 col-xl-4 col-xxl-3 col-xxxl-2'>
				<div class='card'>
					<div class='card-body'>
						<h5 class='card-title'>Hall of Fame Count</h5>
						<input type='number' name='hofCount' value='{$hofCount}' min='5' max='100' required='1' class='form-control' placeholder='Default = 20'>
					</div>
				</div>
			</div>
            <br />
			<div class='col-sm-12 col-md-6 col-xl-4 col-xxl-3 col-xxxl-3'>
				<div class='card'>
					<div class='card-body'>
						<h5 class='card-title'>Announcement Count</h5>
						<input type='number' name='announceCount' value='{$announceCount}' min='1' max='1000' required='1' class='form-control' placeholder='Default = 1000'>
					</div>
				</div>
			</div>
            <br />
			<div class='col-sm-12 col-md-6 col-xl-4 col-xxl-3 col-xxxl-2'>
				<div class='card'>
					<div class='card-body'>
						<h5 class='card-title'>Guild Notif Count</h5>
						<input type='number' name='guildNotifCount' value='{$guildNotifCount}' min='1' max='1000' required='1' class='form-control' placeholder='Default = 10'>
					</div>
				</div>
			</div>
            <br />
		</div>";
		echo "
		<hr />
		<input type='submit' class='btn btn-primary btn-block' value='Change Counts'>
		<input type='hidden' value='1' name='submit'>
		</form>";
	}
}

function prefsound()
{
    global $db, $api, $userid, $set;
    $audioBGM=getCurrentUserPref('audioBGM', 15);
    $audioMaster=getCurrentUserPref('audioMaster', 100);
    alert('info',"","The {$set['WebsiteName']} Audio system is still a WIP. It doesn't work on all devices/browsers yet. {$api->SystemUserIDtoName(1)} 
    makes no guarantees that the audio system works for you.",false);
    if (isset($_POST['submit']))
    {
        $_POST['audioBGM'] = (isset($_POST['audioBGM']) && is_numeric($_POST['audioBGM'])) ? abs($_POST['audioBGM']) : $audioBGM;
        $_POST['audioMaster'] = (isset($_POST['audioMaster']) && is_numeric($_POST['audioMaster'])) ? abs($_POST['audioMaster']) : $audioMaster;
        
        $_POST['audioBGM'] = clamp($_POST['audioBGM'], 0, 100);
        $_POST['audioMaster'] = clamp($_POST['audioMaster'], 0, 100);
        
        setCurrentUserPref('audioBGM', $_POST['audioBGM']);
        setCurrentUserPref('audioMaster', $_POST['audioMaster']);
        
        alert('success', "Success!", "You have successfully updated your audio settings.", false);
        $api->SystemLogsAdd($userid, 'preferences', "Updated audio settings.");
        
        $audioBGM = $_POST['audioBGM'];
        $audioMaster = $_POST['audioMaster'];
    }
    echo "<form method='post'>
		<div class='row'>
            <div class='col-md col-lg-6 col-xl-4'>
				<div class='card'>
					<div class='card-body'>
						<h5 class='card-title'>Master Volume</h5>
						<input type='number' name='audioMaster' value='{$audioMaster}' min='1' max='100' required='1' class='form-control' placeholder='Default = 100'>
					</div>
				</div>
			</div>
			<div class='col-md col-lg-6 col-xl-4'>
				<div class='card'>
					<div class='card-body'>
						<h5 class='card-title'>Background Music</h5>
						<input type='number' name='audioBGM' value='{$audioBGM}' min='0' max='100' required='1' class='form-control' placeholder='Default = 15'>
					</div>
				</div>
			</div>
		</div>";
        echo "
		<hr />
		<input type='submit' class='btn btn-primary' value='Update Audio'>
		<input type='hidden' value='1' name='submit'>
		</form>";
}
$h->endpage();