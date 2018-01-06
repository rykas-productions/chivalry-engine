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
    case 'notifoff':
        notifoff();
        break;
    case 'themechange':
        themechange();
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
	case '2fa':
        twofa();
        break;
    default:
        prefs_home();
        break;
}
function prefs_home()
{
    global $ir;
    echo "Welcome to your account settings, {$ir['username']}. Here you can change many options concerning your account.<br />
	<table class='table table-bordered'>
		<tbody>
			<tr>
				<td>
					<a href='?action=namechange'>Change Name</a>
				</td>
				<td>
					<a href='?action=pwchange'>Change Password</a>
				</td>
			</tr>
			<tr>
				<td>
					<a href='?action=themechange'>Change Theme</a>
				</td>
				<td>
					<a href='?action=emailchange'>Change Email Opt-Setting</a>
				</td>
			</tr>
			<tr>
				<td>
					<a href='?action=picchange'>Change Display Picture</a>
				</td>
				<td>
					<a href='?action=sexchange'>Change Sex</a>
				</td>
			</tr>
			<tr>
				<td>
					<a href='?action=sigchange'>Change Forum Signature</a>
				</td>
				<td>
                    <a href='?action=notifoff'>Disable Alerts</a>
				</td>
			</tr>
			<tr>
				<td>
					<a href='?action=descchange'>Change Player Description</a>
				</td>
				<td>
					<a href='?action=quicklink'>Change Quick-Use Items</a>
				</td>
			</tr>
			<tr>
				<td>
				    <a href='?action=forumalert'>Forum Notifications</a>
				</td>
				<td>
					<a href='?action=userdropdown'>User Input Setting</a>
				</td>
			</tr>
			<tr>
				<td>
				    <a href='?action=2fa'>Two-factor Authentication</a>
				</td>
				<td>
					
				</td>
			</tr>
		</tbody>
	</table>";
}

function name_change()
{
    global $db, $ir, $userid, $h;
    if (empty($_POST['newname'])) {
        $csrf = request_csrf_html('prefs_namechange');
        echo "<br />
		<h3>Username Change</h3>
		Here you can change your name that is shown throughout the game.<br />
		<div class='form-group'>
		<form method='post'>
			<input type='text' class='form-control' minlength='3' maxlength='20' id='username' required='1' value='{$ir['username']}' name='newname' />
			<br />
			{$csrf}
			<input type='submit' class='btn btn-primary' value='Change Username' />
			</div>
		</form>";
    } else {
        if (!isset($_POST['verf']) || !verify_csrf_code('prefs_namechange', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your action was blocked for security reasons. Fill out the form quicker next time.");
            die($h->endpage());
        }
        $_POST['newname'] = (isset($_POST['newname']) && is_string($_POST['newname'])) ? stripslashes($_POST['newname']) : '';
        if (empty($_POST['newname'])) {
            alert('danger', "Uh Oh!", "Please fill out the form and try again.");
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
        $_POST['newname'] = $db->escape(htmlentities($_POST['newname'], ENT_QUOTES, 'ISO-8859-1'));
        $db->query("UPDATE `users` SET `username` = '{$_POST['newname']}'  WHERE `userid` = $userid");
        alert('success', "Success!", "You have changed your username to {$_POST['newname']}.", true, 'preferences.php');
    }
}

function pw_change()
{
    global $db, $ir, $h, $api;
    if (empty($_POST['oldpw'])) {
        $csrf = request_csrf_html('prefs_changepw');
        echo "
	<h3>Password Change</h3>
	<hr />
	Remember that changing your password will make all your previously sent and received messages unreadable.
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
			<input type='password' required='1' class='form-control' name='newpw' />
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
			$api->SystemSendEmail($ir['email'], "This email is to let you know that your password has been changed. If this by your doing, you may ignore this email. If not, someone may now have access to your account. Use the password reset form on the login page to reset your password.", "Chivalry is Dead Password Change");
        }
    }
}

function pic_change()
{
    global $db, $h, $userid, $ir;
    if (!isset($_POST['newpic'])) {
        $csrf = request_csrf_html('prefs_changepic');
        echo "
		<h3>Change Display Picture</h3>
		<hr />
		Your images must be externally hosted. Any images that are not 250x250 will be scaled accordingly.<br />
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
            if ($sz <= 0 || $sz >= 1048576) {
                alert('danger', "Uh Oh!", "You picture's file size is too big. At maximum, picture file size can be 1MB.");
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
        $db->query("UPDATE `users` SET `display_pic` = '" . $db->escape($npic) . "' WHERE `userid` = {$userid}");
    }
}

function sigchange()
{
    global $db, $ir, $userid, $h;
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
    global $db, $userid, $ir, $h;
    if (isset($_POST['gender'])) {
        if (!isset($_POST['verf']) || !verify_csrf_code('prefs_changesex', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your action was blocked for security reasons. Fill out the form quicker next time.");
            die($h->endpage());
        }
        if (!isset($_POST['gender']) || ($_POST['gender'] != 'Male' && $_POST['gender'] != 'Female')) {
            alert('danger', "Uh Oh!", "You cannot change into an invalid sex.");
            die($h->endpage());
        }
        if ($ir['gender'] == $_POST['gender']) {
            alert('danger', "Uh Oh!", "You cannot turn yourself  back into your current sex.");
            die($h->endpage());
        }
        $e_gender = $db->escape(stripslashes($_POST['gender']));
        $db->query("UPDATE `users` SET `gender` = '{$e_gender}' WHERE `userid` = {$userid}");
        alert('success', "Success!", "You have successfully changed your sex into {$_POST['gender']}.", true, 'preferences.php');
    } else {
        $g = ($ir['gender'] == "Male") ?
            $g = "	<option value='Male'>Male</option>
					<option value='Female'>Female</option>" :
            $g = "	<option value='Female'>Female</option>
					<option value='Male'>Male</option>";
        $csrf = request_csrf_html('prefs_changesex');
        echo "<table class='table table-bordered'>
		<form method='post'>
		<tr>
			<th colspan='2'>
				Use this form to change your sex.
			</th>
		</tr>
		<tr>
			<th>
				Sex
			</th>
			<td>
				<select name='gender' class='form-control' type='dropdown'>
					{$g}
				</select>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<input type='submit' value='Change Sex' class='btn btn-primary'>
			</td>
		</tr>
		{$csrf}
		</form>
		</table>";
    }
}

function emailchange()
{
    global $db, $userid, $ir, $h;
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

function notifoff()
{
	global $db,$userid,$api,$h;
    if (isset($_POST['do']))
    {
		if ($_POST['do'] == 'disable')
		{
			$db->query("UPDATE `user_settings` SET `disable_alerts` = 1 WHERE `userid` = {$userid}");
			alert('success',"Success!","You have successfully disabled the notification alerts.",true,'preferences.php');
		}
		else
		{
			$db->query("UPDATE `user_settings` SET `disable_alerts` = 0 WHERE `userid` = {$userid}");
			alert('success',"Success!","You have successfully enabled the notification alerts.",true,'preferences.php');
		}
    }
    else
    {
        echo "You can choose to enable or disable the toast notifications. These are the things that show how many 
		unread mail or notifications you may have.<br />
        <form method='post'>
            <input type='hidden' value='disable' name='do'>
            <input type='submit' class='btn btn-primary' value='Disable Alerts'>
        </form>
		<form method='post'>
            <input type='hidden' value='enable' name='do'>
            <input type='submit' class='btn btn-primary' value='Enable Alerts'>
        </form>";
    }
}

function themechange()
{
    global $db, $userid, $h, $ir;
    if (isset($_POST['theme'])) {
        $_POST['theme'] = (isset($_POST['theme']) && is_numeric($_POST['theme'])) ? abs($_POST['theme']) : 1;
        if ($_POST['theme'] < 1 || $_POST['theme'] > 8) {
            alert('danger', "Uh Oh!", "The theme you wish to load is not valid.");
            die($h->endpage());
        }
		elseif ($_POST['theme'] == 5 && $ir['vip_days'] == 0)
		{
			alert('danger',"Uh Oh!", "The theme you've chosen is for VIPs Only.");
			die($h->endpage());
		}
		elseif ($_POST['theme'] == 6 && $ir['vip_days'] == 0)
		{
			alert('danger',"Uh Oh!", "The theme you've chosen is for VIPs Only.");
			die($h->endpage());
		}
		elseif ($_POST['theme'] == 7 && $ir['vip_days'] == 0)
		{
			alert('danger',"Uh Oh!", "The theme you've chosen is for VIPs Only.");
			die($h->endpage());
		}
		elseif ($_POST['theme'] == 8 && $ir['vip_days'] == 0)
		{
			alert('danger',"Uh Oh!", "The theme you've chosen is for VIPs Only.");
			die($h->endpage());
		}
		else {
            alert('success', "Success!", "You have successfully changed your theme.", true, 'preferences.php');
            $db->query("UPDATE `user_settings` SET `theme` = {$_POST['theme']} WHERE `userid` = {$userid}");
            die($h->endpage());
        }
    } else {
        echo "
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						Select the theme you wish to be seen as you play.
					</th>
				</tr>
				<tr>
					<td>
						Default<br />
						<img src='assets/img/themes/defaultbig227x123.jpg' class='img-thumbnail img-responsive'>
						<form method='post'>
							<input type='hidden' value='1' name='theme'>
							<input type='submit' class='btn btn-primary' value='Pick this one'>
						</form>
					</td>
					<td>
						Darkly<br />
						<img src='assets/img/themes/darklybig227x123.jpg' class='img-thumbnail img-responsive'>
						<form method='post'>
							<input type='hidden' value='2' name='theme'>
							<input type='submit' class='btn btn-primary' value='Pick this one'>
						</form>
					</td>
				</tr>
				<tr>
					<td>
						Superhero<br />
						<img src='assets/img/themes/superherobig227x123.jpg' class='img-thumbnail img-responsive'>
						<form method='post'>
							<input type='hidden' value='3' name='theme'>
							<input type='submit' class='btn btn-primary' value='Pick this one'>
						</form>
					</td>
					<td>
						Slate<br />
						<img src='assets/img/themes/slatebig227x123.jpg' class='img-thumbnail img-responsive'>
						<form method='post'>
							<input type='hidden' value='4' name='theme'>
							<input type='submit' class='btn btn-primary' value='Pick this one'>
						</form>
					</td>
				</tr>
				<tr>
					<td>
						Cerulean<br />
						<img src='assets/img/themes/ceruleanbig227x123.jpg' class='img-thumbnail img-responsive'>";
						if ($ir['vip_days'] != 0)
						{
							echo "
							<form method='post'>
								<input type='hidden' value='5' name='theme'>
								<input type='submit' class='btn btn-primary' value='Pick this one'>
							</form>
							";
						}
						else
						{
							echo "<br />VIPs only.";
						}
						echo"
					</td>
					<td>
						Minty<br />
						<img src='assets/img/themes/minty227x123.jpg' class='img-thumbnail img-responsive'>";
						if ($ir['vip_days'] != 0)
						{
							echo "
							<form method='post'>
								<input type='hidden' value='6' name='theme'>
								<input type='submit' class='btn btn-primary' value='Pick this one'>
							</form>
							";
						}
						else
						{
							echo "<br />VIPs only.";
						}
						echo"
					</td>
				</tr>
				<tr>
					<td>
						United<br />
						<img src='assets/img/themes/united227x123.jpg' class='img-thumbnail img-responsive'>
						<form method='post'>
							<input type='hidden' value='7' name='theme'>
							<input type='submit' class='btn btn-primary' value='Pick this one'>
						</form>
					</td>
					<td>
						Cyborg<br />
						<img src='assets/img/themes/cyborg227x123.jpg' class='img-thumbnail img-responsive'>
						<form method='post'>
							<input type='hidden' value='8' name='theme'>
							<input type='submit' class='btn btn-primary' value='Pick this one'>
						</form>
					</td>
				</tr>
			</table>";
    }
}
function descchange()
{
	global $db, $h, $userid, $ir;
    if (isset($_POST['desc'])) {

        //Verify CSRF check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("pref_changedesc", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }

        //Make sure the POST is safe to work with.
        $ament = $db->escape(nl2br(htmlentities(stripslashes($_POST['desc']), ENT_QUOTES, 'ISO-8859-1')));
		
		$length=strlen($ament);
		if ($length > 1000)
		{
			alert('danger', "Uh Oh!", "Your player description may only be 1,000 characters at maximum. You entered {$length}.", true, 'preferences.php');
			die($h->endpage());
		}

        //Update the guild's announcement.
        $db->query("UPDATE `users` SET `description` = '{$ament}' WHERE `userid` = {$userid}");
        alert('success', "Success!", "You have updated your profile's description.", true, 'preferences.php');
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
	global $db,$userid,$ir,$h;
	if (isset($_POST['dungeon']))
	{
		$dungeon = (isset($_POST['dungeon']) && is_numeric($_POST['dungeon'])) ? abs($_POST['dungeon']) : 1;
		$infirmary = (isset($_POST['infirmary']) && is_numeric($_POST['infirmary'])) ? abs($_POST['infirmary']) : 1;
		if ($dungeon < 1 || $dungeon > 3)
		{
			alert("danger","Uh Oh!","You have selected an invalid dungeon item.");
			die($h->endpage());
		}
		if ($infirmary < 1 || $infirmary > 2)
		{
			alert("danger","Uh Oh!","You have selected an invalid infirmary item.");
			die($h->endpage());
		}
		$db->query("UPDATE `user_settings` SET `ditem` = {$dungeon}, `iitem` = {$infirmary} WHERE `userid` = {$userid}");
		alert('success',"Success!","You have successfully updated your infirmary and dungeon quick links",true,'preferences.php');
	}
	else
	{
		echo "Select your infirmary/dungeon quick use items.<br />
		<form method='post'>
			<div class='row'>
				<div class='col-md-6'>
					Dungeon Item
					<select name='dungeon' class='form-control'>
						<option value='1'>Lockpick</option>
						<option value='2'>Key</option>
						<option value='3'>Key Set</option>
					</select>
				</div>
				<div class='col-md-6'>
					Infirmary Item
					<select name='infirmary' class='form-control'>
						<option value='1'>Leech</option>
						<option value='2'>Linen Wrap</option>
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
		}
		else
		{
			$db->query("UPDATE `user_settings` SET `dropdown` = 1 WHERE `userid` = {$userid}");
			alert('success',"Success!","You have set your user input to number input.",true,'preferences.php');
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
		}
		else
		{
			$db->query("UPDATE `user_settings` SET `forum_alert` = 1 WHERE `userid` = {$userid}");
			alert('success',"Success!","You have successfully enabled forum notifications.",true,'preferences.php');
		}
    }
    else
    {
        echo "You can choose to receive notifications if players respond to your forum threads. You will not get notifications 
		if you respond to your own threads. You will not get notifications if your thread is locked, deleted, stickied, etc. By 
		default, this is off. You <i>must</i> opt-in to receive notifications.<br />
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

function twofa()
{
	global $db,$userid,$api,$h,$set,$ir;
	include_once("lib/PHPGangsta/GoogleAuthenticator.php");
	if ($ir['2fa_on'] == 0)
	{
		$ga = new PHPGangsta_GoogleAuthenticator();
		echo "<h3>Enabling Two-factor Authentication</h3><hr />";
		if (isset($_POST['code']))
		{
			if (empty($_POST['code']))
			{
				alert('danger',"Uh Oh!","You must enter a valid code from your authenticator app. Please delete the currently stored 2fa listing in your app before trying again.");
				die($h->endpage());
			}
			$result=$ga->verifyCode($_SESSION['2fa_secret'], $_POST['code'], 10);
			if ($result) 
			{
				alert('success',"Success!","Two-factor authentication has been enabled on your account successfully.",true,'preferences.php');
				$db->query("UPDATE `user_settings` SET `2fa_on` = 1 WHERE `userid` = {$userid}");
				$db->query("INSERT INTO `2fa_table` (`userid`, `secret_key`) VALUES ({$userid}, '{$_SESSION['2fa_secret']}')");
				$_SESSION['2fa_secret']=NULL;
				$_SESSION['2fa_code']=NULL;
			} 
			else 
			{
				alert('danger',"Uh Oh!","Your code was invalid. Please delete the currently stored 2fa listing in your app before trying again.");
				die($h->endpage());
			}
		}
		else
		{
			$secret = $ga->createSecret();
			$qrCodeUrl = $ga->getQRCodeGoogleUrl($ir['username'], $secret, $set['WebsiteName']);
			echo"
			Scan this QR-Code:<br />
			<img src='{$qrCodeUrl}' /><br />
			Or, enter this key:<br />
			<b>{$secret}</b>
			<hr />
			Now verify the code you have on your authenticator app.<br />
			<form method='post'>
				<input type='number' min='0' placeholder='This is the code your authenticator app shows.' class='form-control' required='1' name='code'>
				<input type='submit' class='btn btn-primary' value='Enable 2FA'>
			</form>";
			$_SESSION['2fa_code'] = $ga->getCode($secret);
			$_SESSION['2fa_secret'] = $secret;
		}
	}
	else
	{
		if (isset($_POST['do']))
		{
			$db->query("UPDATE `user_settings` SET `2fa_on` = 0 WHERE `userid` = {$userid}");
			$db->query("DELETE FROM `2fa_table` WHERE `userid` = {$userid}");
			alert('success',"Success!","You have successfully removed two-factor authentication from your account. Please delete any keys/settings from your authenticator app.",true,'preferences.php');
		}
		else
		{
			echo "Are you sure you wish to disable two-factor authentication? All your previous codes will be invalidated.
			<br />
			<form method='post'>
				<input type='hidden' value='yes' name='do'>
				<input type='submit' class='btn btn-danger' name='Disable 2FA'>
			</form>";
		}
	}
}

$h->endpage();