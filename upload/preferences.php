<?php
require("globals.php");
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
function csrf_error($goBackTo)
{
    global $h,$lang;
	echo "<div class='alert alert-danger'> <strong>{$lang['CSRF_ERROR_TITLE']}</strong> 
	{$lang['CSRF_ERROR_TEXT']} {$lang['CSRF_PREF_MENU']} <a href='preferences.php?action={$goBackTo}'>{$lang['GEN_HERE']}.</div>";
    $h->endpage();
    exit;
}
switch ($_GET['action'])
{
case 'namechange':
    name_change();
    break;
case 'timechange':
    time_change();
    break;
case 'langchange':
    lang_change();
    break;
case 'pwchange':
    pw_change();
    break;
case 'picchange':
    pic_change();
    break;
default:
    prefs_home();
    break;
}
function prefs_home()
{
	global $ir,$h,$lang;
	if (!empty($_GET['lang']))
	{
		alert('success',"{$lang['LANG_UPDATE']}","{$lang['LANG_UPDATE2']}");
	}
echo "{$lang['PREF_WELCOME_1']} {$ir['username']}{$lang['PREF_WELCOME_2']}<br />
<table class='table table-bordered'>
	<tbody>
		<tr>
			<td><a href='?action=namechange'>{$lang['PREF_CNAME']}</a></td>
			<td><a href='?action=pwchange'>{$lang['PREF_CPASSWORD']}</a></td>
		</tr>
		<tr>
			<td><a href='?action=timechange'>{$lang['PREF_CTIME']}</a></td>
			<td><a href='?action=langchange'>{$lang['PREF_CLANG']}</a></td>
		</tr>
		<tr>
			<td><a href='?action=picchange'>{$lang['PREF_CPIC']}</a></td>
			<td></td>
		</tr>
	</tbody>
</table>";
}
function name_change()
{
	global $db,$ir,$userid,$h,$lang;
	$code = request_csrf_code('prefs_namechange');
	if (empty($_POST['newname']))
    {
    echo "<div id='usernameresult'></div><br />
	<h3>{$lang['UNC_TITLE']}</h3>
	{$lang['UNC_INTRO']}<br />
	<div class='form-group'>
	<form method='post'>
		<input type='text' class='form-control' minlength='3' maxlength='20' id='username' required='1' name='newname' />
    	<br />
		<input type='hidden' name='verf' value='{$code}' />
    	<input type='submit' class='btn btn-default' value='{$lang['UNC_BUTTON']}' />
		</div>
	</form>";
	}
	else
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('prefs_namechange', stripslashes($_POST['verf'])))
		{
			csrf_error('namechange');
		}
		$_POST['newname'] =
            (isset($_POST['newname']) && is_string($_POST['newname']))
                    ? stripslashes($_POST['newname']) : '';
		if (empty($_POST['newname']))
		{
			alert('danger',$lang['ERROR_EMPTY'],"{$lang['UNC_ERROR_1']}{$lang['GEN_HERE']}{$lang['UNC_ERROR_2']}");
			die($h->endpage());
		}
		elseif (((strlen($_POST['newname']) > 20) OR (strlen($_POST['newname']) < 3)))
		{
			alert('danger',$lang['ERROR_LENGTH'],$lang['UNC_LENGTH_ERROR']);
			die($h->endpage());
		}
		if (!preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $_POST['newname']))
		{
			alert('danger',$lang['ERROR_INVALID'],$lang['UNC_INVALIDCHARCTERS']);
			die($h->endpage());
		}
		$check_ex = $db->query('SELECT `userid` FROM `users` WHERE `username` = "' . $db->escape($_POST['newname']) . '"');
		if ($db->num_rows($check_ex) > 0)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['UNC_INUSE']);
			die($h->endpage());
		}
		$_POST['newname'] = $db->escape(htmlentities($_POST['newname'], ENT_QUOTES, 'ISO-8859-1'));
		$db->query("UPDATE `users` SET `username` = '{$_POST['newname']}'  WHERE `userid` = $userid");
		alert('success',$lang['ERROR_SUCCESS'],$lang['UNC_GOOD']);
	}
}
function time_change()
{
	global $db,$userid,$h,$ir;
	$DefaultTimeZone=('(GMT) Greenwich Mean Time'); //Set to whatever timezone is default.
	echo "<h3>Timezone Change</h3>";
	// Much thanks to Tamas Pap from Stack Overflow for the list <3
	// https://stackoverflow.com/questions/4755704/php-timezone-list
	if (!isset($_POST['timezone']))
	{
		echo"Here, you may change your timezone. This will change all dates on the game for you. This won't speed up any processes. 
		The default timezone is <u>{$DefaultTimeZone}</u>. All game-wide announcements and features will be based on this timezone.
		<br />
		<form method='post'>
		<select name='timezone' class='form form-control' type='dropdown'>
			<option value='Pacific/Tongatapu'>(GMT+13:00) Nuku'alofa</option>
			<option value='Pacific/Auckland'>(GMT+12:00) Auckland</option>
			<option value='Asia/Magadan'>(GMT+11:00) Magadan</option>
			<option value='Australia/Sydney'>(GMT+10:00) Sydney</option>
			<option value='Australia/Darwin'>(GMT+9:30) Darwin</option>
			<option value='Asia/Tokyo'>(GMT+9:00) Tokyo</option>
			<option value='Australia/Perth'>(GMT+8:00) Perth</option>
			<option value='Asia/Bangkok'>(GMT+7:00) Bangkok</option>
			<option value='Asia/Rangoon'>(GMT+6:30) Rangoon</option>
			<option value='Asia/Novosibirsk'>(GMT+6:00) Novosibirsk</option>
			<option value='Asia/Katmandu'>(GMT+5:45) Kathmandu</option>
			<option value='Asia/Calcutta'>(GMT+5:30) Chennai</option>
			<option value='Asia/Karachi'>(GMT+5:00) Karachi</option>
			<option value='Asia/Kabul'>(GMT+4:30) Kabul</option>
			<option value='Asia/Muscat'>(GMT+4:00) Muscat</option>
			<option value='Asia/Tehran'>(GMT+3:30) Tehran</option>
			<option value='Europe/Moscow'>(GMT+3:00) Moscow</option>
			<option value='Europe/Bucharest'>(GMT+2:00) Bucharest</option>
			<option value='Europe/Berlin'>(GMT+1:00) Berlin</option>
			<option value='Europe/London'>(GMT) Greenwich Mean Time</option>
			<option value='Atlantic/Cape_Verde'>(GMT-1:00) Cape Verde Islands</option>
			<option value='America/Noronha'>(GMT-2:00) Mid-Atlantic</option>
			<option value='America/Godthab'>(GMT-3:00) Greenland</option>
			<option value='America/St_Johns'>(GMT-3:30) Newfoundland</option>
			<option value='America/Halifax'>(GMT-4:00) Atlantic Time</option>
			<option value='America/New_York'>(GMT-5:00) Eastern Time</option>
			<option value='America/Chicago'>(GMT-6:00) Central Time</option>
			<option value='America/Denver'>(GMT-7:00) Mountain Time</option>
			<option value='America/Los_Angeles'>(GMT-8:00) Pacific Time</option>
			<option value='America/Anchorage'>(GMT-9:00) Alaska</option>
			<option value='America/Adak'>(GMT-10:00) Hawaii</option>
			<option value='Pacific/Apia'>(GMT-11:00) Midway Island</option>
			<option value='Pacific/Wake'>(GMT-12:00) International Date Line West</option>
		</select>
		<br />
		<input type='submit' class='btn btn-default' value='Change Timezone'>";
	}
	else
	{
		$TimeZoneArray=[ "Pacific/Wake", "Pacific/Apia", "America/Adak", "America/Anchorage", "America/Los_Angeles",
		"America/Denver", "America/Chicago", "America/New_York", "America/Halifax", "America/Godthab", "America/Noronha",
		"Atlantic/Cape_Verde", "Europe/London", "Europe/Berlin", "Europe/Bucharest", "Europe/Moscow", "Asia/Tehran",
		"Asia/Muscat", "Asia/Kabul", "Asia/Karachi", "Asia/Calcutta", "Asia/Katmandu", "Asia/Novosibirsks",
		"America/Godthab", "Asia/Rangoon", "Asia/Bangkok", "Australia/Perth", "Asia/Tokyo", "Australia/Darwin",
		"Australia/Sydney", "Asia/Magadan", "Pacific/Auckland", "Pacific/Tongatapu"
		
		];
		if (!in_array($_POST['timezone'],$TimeZoneArray))
		{
			echo "You specified an invalid timezone. Go back and try again.";
		}
		else
		{
			echo "You have successfully updated your timezome from {$ir['timezone']} to {$_POST['timezone']}.";
			$db->query("UPDATE `users` SET `timezone` = '{$_POST['timezone']}' WHERE `userid` = {$userid}");
		}
	}
}
function lang_change()
{
	global $db,$h,$lang;
	echo "<h2>Language Change</h2>";
	if (empty($_POST['lang']))
	{
		echo "{$lang['LANG_INTRO']}<br />
		<form method='post' action='?action=langchange'>
		<select name='lang' class='form form-control' type='dropdown'>
			<option value='en'>English</option>
			<option value='es'>Español</option>
			<option value='ger'>Deutsche</option>
			<option value='fr'>Français</option>
		<input type='submit' class='btn btn-default' value='{$lang['LANG_BUTTON']}'>
		</form>";
	}
	else
	{
		$LangArray=["en","es","ger","fr"];
		if (!in_array($_POST['lang'],$LangArray))
		{
			echo "You specified an invalid Language. Go back and try again.";
		}
		else
		{
			echo "You have successfully updated your language to {$_POST['lang']}.";
		}
	}
}
function pw_change()
{
	global $db,$ir,$lang;
	if (empty($_POST['oldpw']))
	{
		$code = request_csrf_code('prefs_pwchange');
		echo "
	<h3>{$lang['PW_TITLE']}</h3>
	<hr />
	<form method='post'>
	<table class='table table-bordered'>
	<tr>
		<th>
			{$lang['PW_CP']}
		</th>
		<td>
			<input type='password' required='1' class='form-control' name='oldpw' />
		</td>
	</tr>
	<tr>
		<th>
			{$lang['PW_NP']}
		</th>
		<td>
			<input type='password' required='1' class='form-control' name='newpw' />
		</td>
	</tr>
	<tr>
		<th>
			{$lang['PW_CNP']}
		</th>
		<td>
			<input type='password' required='1' class='form-control' name='newpw2' />
		</td>
	</tr>
	<tr>
		<td colspan='2'>
			<input type='submit' class='btn btn-default' value='{$lang['PW_BUTTON']}' />
		</td>
	</tr>
    	<input type='hidden' name='verf' value='{$code}' />
    	
	</form>
	</table>
   	";
	}
	else
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('prefs_pwchange', stripslashes($_POST['verf'])))
		{
			csrf_error('pwchange');
		}
		$oldpw = stripslashes($_POST['oldpw']);
		$newpw = stripslashes($_POST['newpw']);
		$newpw2 = stripslashes($_POST['newpw2']);
		if (!verify_user_password($oldpw, $ir['password']))
		{
			alert('danger',$lang['ERROR_INVALID'],$lang['PW_INCORRECT']);
		}
		else if ($newpw !== $newpw2)
		{
			alert('danger',$lang['ERROR_INVALID'],$lang['PW_NOMATCH']);
		}
		else
		{
			// Re-encode password
			$new_psw = $db->escape(encode_password($newpw));
			$db->query("UPDATE `users` SET `password` = '{$new_psw}' WHERE `userid` = {$ir['userid']}");
			alert('success',$lang['ERROR_SUCCESS'],$lang['PW_DONE']);
		}
	}
}
function pic_change()
{
	global $db,$h,$lang,$userid,$ir;
	if (!isset($_POST['newpic']))
	{
		$code = request_csrf_code('prefs_picchange');
		echo "
		<h3>{$lang['PIC_TITLE']}</h3>
		<hr />
		{$lang['PIC_NOTE']}
		<br />
		{$lang['PIC_NOTE2']}<br />
		{$lang['PIC_NEWPIC']}<br />
		<form method='post'>
			<input type='url' required='1' name='newpic' class='form-control' value='{$ir['display_pic']}' />
			<input type='hidden' name='verf' value='{$code}' />
			<br />
			<input type='submit' class='btn btn-default' value='Change Picture' />
		</form>
		";
	}
	else
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('prefs_picchange', stripslashes($_POST['verf'])))
		{
			csrf_error('picchange');
		}
		$npic = (isset($_POST['newpic']) && is_string($_POST['newpic'])) ? stripslashes($_POST['newpic']) : '';
		if (!empty($npic))
		{
			if (isImage($npic) == false)
			{
				alert('danger',"{$lang['ERROR_INVALID']}","{$lang['PIC_NOIMAGE']}");
				die($h->endpage());
			}
			$sz = get_filesize_remote($npic);
			if ($sz <= 0 || $sz >= 1048576)
			{
				alert('danger',"{$lang['PIC_TOOBIG']}","{$lang['PIC_TOOBIG2']}");
				$h->endpage();
				exit;
			}
		}
		$img=htmlentities($_POST['newpic'], ENT_QUOTES, 'ISO-8859-1');
		alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['PIC_SUCCESS']}");
		echo "<img src='{$img}' width='250' height='250' class='img-thumbnail img-responsive'>";
		$db->query(
            'UPDATE `users`
             SET `display_pic` = "' . $db->escape($npic)
                    . '"
             WHERE `userid` = ' . $userid);
	}
}
$h->endpage();