<?php
/*
	File:		global_func.php
	Created: 	4/5/2016 at 12:04AM Eastern Time
	Info: 		Functions used all over the game.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('global_func_user.php');
require('global_func_dropdown.php');
require('global_func_guild.php');
require('global_func_trinket.php');
require('global_func_district.php');
require('global_func_item.php');
require('global_func_estates.php');
require('global_func_email.php');
require('global_func_stock.php');

//Constants
require('const/const_effect.php');
/*
	Parses the time since the timestamp given.
	@param int $time_stamp for time since.
	@param boolean $ago to display the "ago" after the string. (Default = true)
*/
function DateTime_Parse($time_stamp, $ago = true, $override = false)
{
    //Check if $time_stamp is 0, if true, return N/A
    if ($time_stamp == 0) {
        return "N/A";
    }
    //Time difference is $time_stamp subtracted from current unix time.
    $time_difference = (time() - $time_stamp);
    //If the time difference is less than 1 day, OR if $override is set to true. This will display how long ago the
    //timestamp was in seconds/minutes/hours/days/etc.
    if ($time_difference < 86400 || $override == true) {
        $unit = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade', 'century');
        $lengths = array(60, 60, 24, 7, 4.35, 12, 10, 10);
        //Go to the largest unit of time as possible.
        for ($i = 0; $time_difference >= $lengths[$i]; $i++) {
            $time_difference = $time_difference / $lengths[$i];
        }
        //For added precision, lets go over 2 decimal places.
        $time_difference = round($time_difference);
        //If $ago is true, lets add "ago" after our string.
        if ($ago == true) {
            $date = $time_difference . ' ' . $unit[$i] . (($time_difference > 1 OR $time_difference < 1) ? 's' : '') . ' ago';
        } else {
            $date = $time_difference . ' ' . $unit[$i] . (($time_difference > 1 OR $time_difference < 1) ? 's' : '') . '';
        }
    } //If we just want the timestamp in a date format.
    else {
        $date = date('F j, Y, g:i:s a', $time_stamp);
    }
    //Return whatever is output.
    return $date;
}

/*
	Parses how much time until the timestamp given.
	$param int $time_stamp for the timestamp.
*/
function TimeUntil_Parse($time_stamp)
{
    //Time difference is Unix Timestamp subtracted from $time_stamp.
    $time_difference = $time_stamp - time();
    $unit = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade', 'century');
    $lengths = array(60, 60, 24, 7, 4.35, 12, 10, 10);
    //Get to the biggest unit type as possible.
    for ($i = 0; $time_difference >= $lengths[$i]; $i++) {
        $time_difference = $time_difference / $lengths[$i];
    }
    //For added precision, lets round to the 2nd decimal place.
    $time_difference = round($time_difference);
	if ($time_difference < 0)
		$time_difference = 0;
    //Add an 's' if needed.
    $date = $time_difference . ' ' . $unit[$i] . (($time_difference > 1 OR $time_difference < 1) ? 's' : '') . '';
    //Return $date
    return $date;
}

function shortNumberParse($n)
{
    if ($n < 1000)
        $n_format = number_format($n);
    elseif ($n < 10000)
        $n_format = number_format($n / 1000, 2) . "K";
    elseif ($n < 100000)
        $n_format = number_format($n / 1000, 1) . "K";
    elseif ($n < 1000000)
        $n_format = number_format($n / 1000, 1) . "K";
    elseif ($n < 1000000000)
        $n_format = number_format($n / 1000000, 1) . "M";
    elseif ($n < 1000000000000)
        $n_format = number_format($n / 1000000000, 1) . "B";
    elseif ($n < 1000000000000000)
        $n_format = number_format($n / 1000000000000, 1) . "T";
        return "<span data-toggle='tooltip' data-placement='top' title='" . number_format($n) . "'>{$n_format}</span>";
}

/*
	Parses the timestamp into a human friendly number.
*/
function ParseTimestamp($time)
{
    $unit = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade');
    $lengths = array(60, 60, 24, 7, 4.35, 12, 10);
    //Cycle through unit types until we get to the biggest and cannot go any bigger.
    for ($i = 0; $time >= $lengths[$i]; $i++) {
        $time = $time / $lengths[$i];
    }
    //Round to the second decimal place
    $time = round($time, 2);
    //Add an 's' if needed.
    $date = $time . ' ' . $unit[$i] . (($time > 1 OR $time < 1) ? 's' : '') . '';
    //Return date.
    return $date;
}

/*
	The function for testing for a valid email.
	@param text $email The email to test for.
*/
function valid_email($email)
{
    return (filter_var($email, FILTER_VALIDATE_EMAIL) === $email);
}

/**
 * Sends a user a notification, given their ID and the text.
 * @param int $userid The user ID to be sent the notification
 * @param string $text The notification's text. This should be fully sanitized for HTML, but not pre-escaped for database insertion.
 * @return true
 */
function notification_add($userid, $text, $icon='', $color='')
{
    global $db;
    $text = $db->escape($text);
    $db->query(
        "INSERT INTO `notifications`
             VALUES(NULL, $userid, " . time() . ", 'unread', '$text', '{$icon}', '{$color}')");
    return true;
}

/*
	Internal Function: Used to update all sorts of things around the game
*/

//anti f5
function resetAttackStatus()
{
  global $userid, $api, $ir;
  $_SESSION['attacking'] = 0;
  $_SESSION['attack_scroll'] = 0;
  $ir['attacking'] = 0;
  $api->UserInfoSetStatic($userid, "attacking", 0);
}

function updateStats()
{
	global $db, $time, $ir;
	if ($ir['hp'] > $ir['maxhp'])
		$db->query("UPDATE `users` SET `hp` = `maxhp` WHERE `userid` = {$ir['userid']}");
	if ($ir['energy'] > $ir['maxenergy'])
		$db->query("UPDATE `users` SET `energy` = `maxenergy` WHERE `userid` = {$ir['userid']}");
	if ($ir['brave'] > $ir['maxbrave'])
		$db->query("UPDATE `users` SET `brave` = `maxbrave` WHERE `userid` = {$ir['userid']}");
	if (($ir['will'] > $ir['maxwill']) && ($ir['will_overcharge'] < time()))
		$db->query("UPDATE `users` SET `will` = `maxwill` WHERE `userid` = {$ir['userid']}");
    $q1 = $db->query("/*qc=on*/SELECT `fed_userid` FROM `fedjail` WHERE `fed_out` < {$time}");
    //Remove players from federal jail, if needed.
    if ($db->num_rows($q1) > 0) {
        $q2 = $db->fetch_single($q1);
        $db->query("DELETE FROM `fedjail` WHERE `fed_out` < {$time}");
        $db->query("UPDATE `users` SET `fedjail` = 0 WHERE `userid` = {$q2}");
    }
    //Remove players forum bans if needed.
    $db->query("DELETE FROM `forum_bans` WHERE `fb_time` < {$time}");

    //Remove players' mail bans if needed.
    $db->query("DELETE FROM `mail_bans` WHERE `mbTIME` < {$time}");
    
    //Delete from newspaper when needed.
    $db->query("DELETE FROM `newspaper_ads` WHERE `news_end` < {$time}");
	$db->query("DELETE FROM `bounty_hunter` WHERE `bh_time` < {$time}");
}



function updateAcademy()
{
	global $db, $time, $ir;
	//Assign the Unix Timestamp to a variable.
    $time = time();
    //Select a User's ID and Course ID if their completion time is less than the Unix Timestamp, and they still have
    //not been credited from their completion.
    $coursedone = $db->query("SELECT `userid`,`course` FROM `users` WHERE `course` > 0 AND `course_complete` < {$time} LIMIT 1");
    $course_cache = array();
    //Loop until no more users have courses left.
    while ($r = $db->fetch_row($coursedone)) {
        //If the course in question is not stored in cache, lets store it.
        if (!array_key_exists($r['course'], $course_cache)) {
            $cd = $db->query("/*qc=on*/SELECT `ac_str`, `ac_agl`, `ac_grd`, `ac_lab`, `ac_iq`, `ac_name`
							 FROM `academy`
							 WHERE `ac_id` = {$r['course']}");
            $coud = $db->fetch_row($cd);
            $db->free_result($cd);
            $course_cache[$r['course']] = $coud;
        } //Store in cache anyway.
        else {
            $coud = $course_cache[$r['course']];
        }
        //Mark user as have completed this course.
        $db->query("INSERT INTO `academy_done` VALUES({$r['userid']}, {$r['course']})");
        $upd = "";
        $ev = "";
        //Course credits strength, so add onto the query.
        if ($coud['ac_str'] > 0) 
		{
            $upd .= ", us.strength = us.strength + {$coud['ac_str']}";
            $ev .= "; " . number_format($coud['ac_str']) . " Strength";
        }
        //Course credits guard, so add onto the query.
        if ($coud['ac_grd'] > 0) 
		{
            $upd .= ", us.guard = us.guard + {$coud['ac_grd']}";
            $ev .= "; " . number_format($coud['ac_grd']) . " Guard";
        }
        //Course credits labor, so add onto the query.
        if ($coud['ac_lab'] > 0) 
		{
            $upd .= ", us.labor = us.labor + {$coud['ac_lab']}";
            $ev .= "; " . number_format($coud['ac_lab']) . " Labor";
        }
        //Course credits agility, so add onto the query.
        if ($coud['ac_agl'] > 0) 
		{
            $upd .= ", us.agility = us.agility + {$coud['ac_agl']}";
            $ev .= "; " . number_format($coud['ac_agl']) . " Agility";
        }
        //Course credits IQ, so add onto the query.
        if ($coud['ac_iq'] > 0) 
		{
            $upd .= ", us.IQ = us.IQ + {$coud['ac_iq']}";
            $ev .= "; " . number_format($coud['ac_iq']) . " IQ";
        }
        //Merge all $ev into a comma seperated event.
        $ev = substr($ev, 1);
        //Update the user's stats as needed, set their course to 0, and course completion time to 0.
        $db->query("UPDATE `users` AS `u` INNER JOIN `userstats` AS `us` ON `u`.`userid` = `us`.`userid`
		SET `u`.`course` = 0, `course_complete` = 0{$upd} WHERE `u`.`userid` = {$r['userid']}");
        //Give the user a notification saying they've completed their course.
        notification_add($r['userid'], "Congratulations, you completed the {$coud['ac_name']} course and gained {$ev}!");
    }
}

function check_data()
{
    global $ir;
	updateStats();
	updateGuildWars();
	updateAcademy();
	checkGuildCrimes();
	missionCheck();
	checkGuildVault();
	removeOldEffects();
	checkGuildDebt();
	if (isset($ir))
	{
		maxWillCheck();
	}
}

/**
 * Sends a guild a notification, given their ID and the text.
 * @param int $guild_id The guild ID to be sent the notification
 * @param string $text The notification's text. This should be fully sanitized for HTML, but not pre-escaped for database insertion.
 * @return true
 */
function guildnotificationadd($guild_id, $text)
{
    global $db;
    $text = $db->escape($text);
    $db->query(
        "INSERT INTO `guild_notifications`
             VALUES(NULL, {$guild_id}, " . time() . ", '{$text}')");
    return true;
}

/**
 * Request that an anti-CSRF verification code be issued for a particular form in the game.
 * @param string $formid A unique string used to identify this form to match up its submission with the right token.
 * @return string The code issued to be added to the form.
 */
function request_csrf_code($formid)
{
	global $db;
    //Assign Unix Timestamp to a variable.
    $time = time();
    //Generate the token from the randomizer function, and hash it with sha512.
    $token = randomizer();
	$user_agent = $db->escape(strip_tags(stripslashes($_SERVER['HTTP_USER_AGENT'])));
    //Store the CSRF Form into $_SESSION.
    $_SESSION["csrf_{$formid}"] = array('token' => $token, 'issued' => $time, 'useragent' => $user_agent);
    //Return the token.
    return $token;
}

/**
 * Request a randomly generated phrase.
 * Returns the randomly generated phrase.
 */
function randomizer()
{
	return bin2hex(random_bytes(32));
}

/**
 * Request that an anti-CSRF verification code be issued for a particular form in the game, and return the HTML to be placed in the form.
 * @param string $formid A unique string used to identify this form to match up its submission with the right token.
 * @return string The HTML for the code issued to be added to the form.
 */
function request_csrf_html($formid)
{
    return "<input type='hidden' name='verf' value='" . request_csrf_code($formid) . "' />";
}

/**
 * Check the CSRF code we received against the one that was registered for the form - return false if the request shouldn't be processed...
 * @param string $formid A unique string used to identify this form to match up its submission with the right token.
 * @param string $code The code the user's form input returned.
 * @param int $expiry The amount of time the CSRF is valid for. Default 300 seconds.
 * @return boolean Whether the user provided a valid code or not
 */
function verify_csrf_code($formid, $code, $expiry = 300)
{
	global $db;
    //User does not have a CSRF Session started for $formid, or its missing information.
    if (!isset($_SESSION["csrf_{$formid}"]) || !is_array($_SESSION["csrf_{$formid}"])) {
        return false;
    } else {
		$IP = $db->escape($_SERVER['REMOTE_ADDR']);
		$user_agent = $db->escape(strip_tags(stripslashes($_SERVER['HTTP_USER_AGENT'])));
        //Set verified to false until we can be sure they have verified successfully.
        $verified = false;
        //Assign the CSRF $formid to a variable.
        $token = $_SESSION["csrf_{$formid}"];
        //Check to see if the token is still valid.
        if ($token['useragent'] == $user_agent)
        {
            if ($token['issued'] + $expiry > time()) {
                //User becomes verified if the code matches the token that was stored in $_SESSION
                $verified = ($token['token'] === $code);
            }
        }
        //Unset the CSRF $formid from $_SESSION
        unset($_SESSION["csrf_{$formid}"]);
        //Return if the user has verified successfully or not.
        return $verified;
    }
}

/**
 * Given a password input given by the user and their actual details,
 * determine whether the password entered was correct.
 *
 * @param string $input The input password given by the user.
 *                        Should be without slashes.
 * @param string $pass The user's encrypted password
 *
 * @return boolean    true for equal, false for not (login failed etc)
 *
 */
function verify_user_password($input, $pass)
{
    //Check that the password matches or not.
    $return = (password_verify(base64_encode(hash('sha256', $input, true)), $pass)) ? true : false;
    return $return;
}

/**
 * Given a password and a salt, encode them to the form which is stored in
 * the game's database.
 *
 * @param string $password The password to be encoded
 *
 * @return string    The resulting encoded password.
 */
function encode_password($password,$lvl='Member')
{
    global $set;
    //Set the password cost via settings.
	if ($lvl == 'Member')
		$options = ['cost' => $set['Password_Effort'],];
	else
		$options = ['cost' => $set['Password_Effort']+1,];
    //Return the generated password.
    return password_hash(base64_encode(hash('sha256', $password, true)), PASSWORD_DEFAULT, $options);
}

/**
 * Easily outputs an alert to the client.
 * Text $type = Alert type. [Valid: danger, success, info, warning, primary, secondary, light, dark]
 * Text $title = Alert Title.
 * Text $text = Alert text.
 * Boolean $doredirect = Whether or not to actually redirect. [Default = true]
 * Text $redirect = File Name to redirect to. [Default = back] [back will reload current page]
 * Text $redirecttext = Text to be shown on the redirect link. [Default = Back]
 */

function alert($type, $title, $text, $doredirect = true, $redirect = 'back', $redirecttext = 'Back', $mute=false)
{
    //This function is a horrible mess dude..
	if ($type == 'danger') {
		$icon = "exclamation-triangle";
		$js='error';
	}
	elseif ($type == 'success') {
		$icon = "check-circle";
		$js='log';
	}
	elseif ($type == 'info') {
		$icon = 'info-circle';
		$js='info';
	}
	else
	{
		$icon = 'exclamation-circle';
		$js='log';
	}
	if ((empty($title)) && ($doredirect))
	{
		echo "<div class='alert alert-{$type}' role='alert'>
						{$text} > <a href='{$redirect}' class='alert-link updateHoverBtn'>{$redirecttext}</a>
				</div>";
	}
	elseif (empty($title))
	{
        echo "<div class='alert alert-{$type}' role='alert'>{$text}</div>";
    }
    elseif ($doredirect) 
	{
        $redirect = ($redirect == 'back') ? $_SERVER['REQUEST_URI'] : $redirect;
        echo "<div class='alert alert-{$type}' role='alert'>
				<h5 class='alert-heading'><i class='fa fa-{$icon}' aria-hidden='true'></i>
					{$title}</h5> 
						{$text} > <a href='{$redirect}' class='alert-link updateHoverBtn'>{$redirecttext}</a>
				</div>";
    }
	else 
	{
        echo "<div class='alert alert-{$type}' role='alert'>
                    <h5 class='alert-heading'><i class='fa fa-{$icon}' aria-hidden='true'></i>
					{$title}</h5> 
					        {$text}
                </div>";
    }
	cslog($js,$text);
}

/**
 *
 * @return string The URL of the game.
 */
function determine_game_urlbase()
{
    $domain = $_SERVER['HTTP_HOST'];
    $turi = $_SERVER['REQUEST_URI'];
    $turiq = '';
    for ($t = strlen($turi) - 1; $t >= 0; $t--) {
        if ($turi[$t] != '/') {
            $turiq = $turi[$t] . $turiq;
        } else {
            break;
        }
    }
    $turiq = '/' . $turiq;
    if ($turiq == '/') {
        $domain .= substr($turi, 0, -1);
    } else {
        $domain .= str_replace($turiq, '', $turi);
    }
    return $domain;
}

/**
 * Check to see if this request was made via XMLHttpRequest.
 * Uses variables supported by most JS frameworks.
 *
 * @return boolean Whether the request was made via AJAX or not.
 **/

function is_ajax()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && is_string($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Get the file size in bytes of a remote file, if we can.
 *
 * @param string $url The url to the file
 *
 * @return int            The file's size in bytes, or 0 if we could
 *                        not determine its size.
 */

function get_filesize_remote($url)
{
    // Retrieve headers
    if (strlen($url) < 8) {
        return 0; // no file
    }
    $is_ssl = false;
    if (substr($url, 0, 7) == 'http://') {
        $port = 80;
    } else if (substr($url, 0, 8) == 'https://' && extension_loaded('openssl')) {
        $port = 443;
        $is_ssl = true;
    } else {
        return 0; // bad protocol
    }
    // Break up url
    $url_parts = explode('/', $url);
    $host = $url_parts[2];
    unset($url_parts[2]);
    unset($url_parts[1]);
    unset($url_parts[0]);
    $path = '/' . implode('/', $url_parts);
    if (strpos($host, ':') !== false) {
        $host_parts = explode(':', $host);
        if (count($host_parts) == 2 && ctype_digit($host_parts[1])) {
            $port = (int)$host_parts[1];
            $host = $host_parts[0];
        } else {
            return 0; // malformed host
        }
    }
    $request =
        "HEAD {$path} HTTP/1.1\r\n" . "Host: {$host}\r\n"
        . "Connection: Close\r\n\r\n";
    $fh = fsockopen(($is_ssl ? 'ssl://' : '') . $host, $port);
    if ($fh === false) {
        return 0;
    }
    fwrite($fh, $request);
    $headers = array();
    $total_loaded = 0;
    while (!feof($fh) && $line = fgets($fh, 1024)) {
        if ($line == "\r\n") {
            break;
        }
        if (strpos($line, ':') !== false) {
            list($key, $val) = explode(':', $line, 2);
            $headers[strtolower($key)] = trim($val);
        } else {
            $headers[] = strtolower($line);
        }
        $total_loaded += strlen($line);
        if ($total_loaded > 50000) {
            // Stop loading garbage!
            break;
        }
    }
    fclose($fh);
    if (!isset($headers['content-length'])) {
        return 0;
    }
    return (int)$headers['content-length'];
}

/*
	Gets the contents of a file if it exists, otherwise grabs and caches 
*/
function get_fg_cache($file, $ip, $hours = 1)
{
    $current_time = time();
    $expire_time = $hours * 60 * 60;
    if (!(filter_var($ip, FILTER_VALIDATE_IP)))
        $ip='127.0.0.1';
    if (file_exists($file)) {
        $file_time = filemtime($file);
        if ($current_time - $expire_time < $file_time) {
            return file_get_contents($file);
        } else {
            $content = update_fg_info($ip);
            file_put_contents($file, $content);
            return $content;
        }
    } else {
        $content = update_fg_info($ip);
        file_put_contents($file, $content);
        return $content;
    }
}

/* 
	Gets content from a URL via curl 
*/
function update_fg_info($ip)
{
    global $set;
    if (!(filter_var($ip, FILTER_VALIDATE_IP)))
        $ip='127.0.0.1';
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.fraudguard.io/ip/$ip",
        CURLOPT_USERPWD => "{$set['FGUsername']}:{$set['FGPassword']}",
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true));
    $content = curl_exec($curl);
    curl_close($curl);
    return $content;
}

/*
   Gets the user's operating system and inserts it into the database.
   @param string $uagent	User agent to test with.
*/
function getOS($uagent)
{
    global $db, $userid, $ir;
	$uagent = $db->escape(strip_tags(stripslashes($uagent)));
	$os_platform = "Unknown OS Platform";
	$os_array = array(
		'/windows nt 10/i' => 'Windows 10',
		'/windows nt 6.3/i' => 'Windows 8.1',
		'/windows nt 6.2/i' => 'Windows 8',
		'/windows nt 6.1/i' => 'Windows 7',
		'/windows nt 6.0/i' => 'Windows Vista',
		'/windows nt 5.1/i' => 'Windows XP',
		'/windows phone 8.0/i' => 'Windows Phone',
		'/windows xp/i' => 'Windows XP',
		'/macintosh|mac os x/i' => 'Mac OS X',
		'/mac_powerpc/i' => 'Mac OS 9',
		'/linux/i' => 'Linux',
		'/ubuntu/i' => 'Ubuntu',
		'/iphone/i' => 'iPhone',
		'/ipod/i' => 'iPod',
		'/ipad/i' => 'iPad',
		'/android/i' => 'Android',
		'/blackberry/i' => 'BlackBerry',
		'/cros/i' => 'Chrome OS',
		'/playstation 4/i' => 'Playstation 4',
		'/webos/i' => 'Mobile'
	);

	foreach ($os_array as $regex => $value) {
		if (preg_match($regex, $uagent)) {
			$os_platform = $value;
		}
	}
    $count = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `userdata` WHERE `userid` = {$userid}"));
    if ($count == 0)
        $db->query("INSERT INTO `userdata` (`userid`, `useragent`, `screensize`, `os`, `browser`) VALUES ({$userid}, '{$uagent}', '', '{$os_platform}', '')");
    else
        $db->query("UPDATE `userdata` SET `useragent` = '{$uagent}', `os` = '{$os_platform}' WHERE `userid` = {$userid}");
	return $os_platform;
}

/*
  Gets the user's browser and inserts it into the database.
  @param string $uagent	User agent to test with.
*/
function getBrowser($uagent)
{
    global $db, $userid, $ir;
	$user_agent = $db->escape(strip_tags(stripslashes($uagent)));
	$browser = "Unknown Browser";
	$browser_array = array(
		'/msie/i' => 'Internet Explorer',
		'/trident/i' => 'Internet Explorer',
		'/firefox/i' => 'Firefox',
		'/safari/i' => 'Safari',
		'/chrome/i' => 'Chrome',
		'/edge/i' => 'Edge',
		'/opera/i' => 'Opera',
		'/netscape/i' => 'Netscape',
		'/maxthon/i' => 'Maxthon',
		'/konqueror/i' => 'Konqueror',
		'/opr/i' => 'Opera',
		'/mobile/i' => 'Handheld Browser',
		'/playstation 4/i' => 'Playstation 4 Browser',
		'/CEngine-App/i' => 'App'
	);
	foreach ($browser_array as $regex => $value) {
		if (preg_match($regex, $user_agent)) {
			$browser = $value;
		}
	}
    $count = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `userdata` WHERE `userid` = {$userid}"));
    if ($count == 0)
        $db->query("INSERT INTO `userdata` (`userid`, `useragent`, `browser`) VALUES ({$userid}, '{$uagent}', '{$broswer}')");
    else
        $db->query("UPDATE `userdata` SET `useragent` = '{$user_agent}', `browser` = '{$browser}' WHERE `userid` = {$userid}");
	return $browser;
}

//Please use $api->SystemLogsAdd(); instead
function SystemLogsAdd($user, $logtype, $input)
{
    global $db;
    $time = time();
    $IP = $db->escape($_SERVER['REMOTE_ADDR']);
    $user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
    $input = $db->escape(str_replace("\n", "<br />", strip_tags(stripslashes($input))));
    $logtype = $db->escape(str_replace("\n", "<br />", strip_tags(stripslashes(strtolower($logtype)))));
    $db->query("INSERT INTO `logs`
				(`log_id`, `log_type`, `log_user`, `log_time`, `log_text`, `log_ip`) 
				VALUES 
				(NULL, '{$logtype}', '{$user}', '{$time}', '{$input}', '{$IP}');");
}

//Fall back for PHP 7 functions on a PHP < 7 versions.
function Random($min = 0, $max = PHP_INT_MAX)
{
        return random_int($min, $max);
}
/*
	Gets the contents of a file if it exists, otherwise grabs and caches 
*/
function get_cached_file($url, $file, $hours = 1)
{
    $current_time = time();
    $expire_time = $hours * 60 * 60;
    if (file_exists($file)) {
        $file_time = filemtime($file);
        if ($current_time - $expire_time < $file_time) {
            return file_get_contents($file);
        } else {
            $content = update_file($url, $file);
            file_put_contents($file, $content);
            return $content;
        }
    } else {
        $content = update_file($url, $file);
        file_put_contents($file, $content);
        return $content;
    }
}

/* 
	Gets content from a URL via curl 
*/
function update_file($url)
{
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "{$url}",
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true));
    $content = curl_exec($curl);
    curl_close($curl);
    return $content;
}

/*
	Function to recache the specified forum topic
*/
function recache_topic($topic)
{
    global $db;
    $topic = abs((int)$topic);
    if ($topic <= 0) {
        return;
    }
    echo "Recaching Topic ID #{$topic} ... ";
    $q =
        $db->query(
            "/*qc=on*/SELECT `fp_poster_id`, `fp_poster_id`, `fp_time`
                     FROM `forum_posts`
                     WHERE `fp_topic_id` = {$topic}
                     ORDER BY `fp_time` DESC
                     LIMIT 1");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        $db->query(
            "UPDATE `forum_topics`
                 SET `ft_last_id` = 0, `ft_last_time` = 0, `ft_posts` = 0
                 WHERE `ft_id` = {$topic}");
    } else {
        $r = $db->fetch_row($q);
        $db->free_result($q);
        $posts_q =
            $db->query(
                "/*qc=on*/SELECT COUNT(`fp_id`)
        					   FROM `forum_posts`
        					   WHERE `fp_topic_id` = {$topic}");
        $posts = $db->fetch_single($posts_q);
        $db->free_result($posts_q);
        $db->query(
            "UPDATE `forum_topics`
                 SET `ft_last_id` = {$r['fp_poster_id']},
                 `ft_last_time` = {$r['fp_time']}, `ft_last_id` = '{$r['fp_poster_id']}',
                 `ft_posts` = {$posts}
                 WHERE `ft_id` = {$topic}");
    }
    echo " ... Recaching completed.<br />";
}

/*
	Function to recache the specified forum
*/
function recache_forum($forum)
{
    global $db;
    $forum = abs((int)$forum);
    if ($forum <= 0) {
        return;
    }
    echo "Recaching Forum ID #{$forum} ... ";
    $q =
        $db->query(
					"/*qc=on*/SELECT `p`.*, `t`.*
                     FROM `forum_posts` AS `p`
                     LEFT JOIN `forum_topics` AS `t`
                     ON `p`.`fp_topic_id` = `t`.`ft_id`
                     WHERE `p`.`ff_id` = {$forum}
                     ORDER BY `p`.`fp_time` DESC
                     LIMIT 1");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        $db->query(
            "UPDATE `forum_forums`
                 SET `ff_lp_time` = 0, `ff_lp_poster_id` = 0, `ff_lp_t_id` = 0,
                 `ff_lp_t_id` = 0
                  WHERE `ff_id` = {$forum}");
    } else {
        $r = $db->fetch_row($q);
        $db->free_result($q);
        $db->query(
            "UPDATE `forum_forums`
                 SET `ff_lp_time` = {$r['fp_time']},
                 `ff_lp_poster_id` = {$r['fp_poster_id']},
				 `ff_lp_t_id` = {$r['ft_id']}
                 WHERE `ff_id` = {$forum}");
    }
    echo " ... Recaching completed.<br />";
}

function isImage($url)
{
    $params = array('http' => array(
        'method' => 'HEAD'
    ));
    $ctx = stream_context_create($params);
    $fp = @fopen($url, 'rb', false, $ctx);
    if (!$fp)
        return false;  // Problem with url

    $meta = stream_get_meta_data($fp);
    if ($meta === false) {
        fclose($fp);
        return false;  // Problem reading data from url
    }

    $wrapper_data = $meta["wrapper_data"];
    if (is_array($wrapper_data)) {
        foreach (array_keys($wrapper_data) as $hh) {
            if (substr($wrapper_data[$hh], 0, 19) == "Content-Type: image") // strlen("Content-Type: image") == 19
            {
                fclose($fp);
                return true;
            }
        }
    }

    fclose($fp);
    return false;
}

/*
 * Function to fetch current version of Chivalry Engine
 */
function version_json($url = 'https://raw.githubusercontent.com/MasterGeneral156/Version/master/chivalry-engine.json')
{
    global $set;
    $engine_version = $set['Version_Number'];
    $json = json_decode(get_cached_file($url, __DIR__ . "/cache/update_check.txt"), true);
    if (is_null($json))
        return "Update checker failed.";
    if (version_compare($engine_version, $json['latest']) == 0 || version_compare($engine_version, $json['latest']) == 1)
        return "Chivalry Engine is up to date.";
    else
        return "Chivalry Engine update available. Download it <a href='{$json['download-latest']}'>here</a>.";
}

function pagination($perpage, $total, $currentpage, $url)
{
    global $db;
    $pages = ceil($total / $perpage);
    $output = "<ul class='pagination justify-content-center'>";
    if ($currentpage <= 0) {
        $output .= "<li class='page-item disabled'><a class='page-link'>&laquo;</a></li>";
        $output .= "<li class='page-item disabled'><a class='page-link'>Back</a></li>";
    } else {
        $link = $currentpage - $perpage;
        $output .= "<li class='page-item'><a class='page-link' href='{$url}0'>&laquo;</a></li>";
        $output .= "<li class='page-item'><a class='page-link' href='{$url}{$link}'>Back</a></li>";
    }
    for ($i = 1; $i <= $pages; $i++) {
        $s = ($i - 1) * $perpage;
        if (!((($currentpage - 3 * $perpage) > $s) || (($currentpage + 3 * $perpage) < $s))) {
            if ($s == $currentpage) {
                $output .= "<li class='page-item active'>";
            } else {
                $output .= "<li class='page-item'>";
            }
            $output .= "<a class='page-link' href='{$url}{$s}'>{$i}</li></a>";
        }
    }
    $maxpage = ($pages * $perpage) - $perpage;
    if ($currentpage >= $maxpage) {
        $output .= "<li class='page-item disabled'><a class='page-link'>Next</a></li>";
        $output .= "<li class='page-item disabled'><a class='page-link'>&raquo;</a></li>";
    } else {
        $link = $currentpage + $perpage;
        $output .= "<li class='page-item'><a class='page-link' href='{$url}{$link}'>Next</a></li>";
        $output .= "<li class='page-item'><a class='page-link' href='{$url}{$maxpage}'>&raquo;</a></li>";
    }
    $output .= "</ul></nav>";
    return $output;
}

function cslog($type='log',$txt)
{
	echo "<script>console.{$type}('{$txt}');</script>";
}

function encrypt_message($msg,$sender,$receiver)
{
    global $db;
    $senderkey=$db->fetch_single($db->query("/*qc=on*/SELECT `security_key` from `user_settings` WHERE `userid` = {$sender}"));
    $receiverkey=$db->fetch_single($db->query("/*qc=on*/SELECT `security_key` from `user_settings` WHERE `userid` = {$receiver}"));
    $key = hash("sha512","{$senderkey}.{$receiverkey}");
	if (openssl_encrypt($msg,"AES-256-ECB",$key))
		return openssl_encrypt($msg,"AES-256-ECB",$key);
	else
		return "<span class='text-danger'>Failed to encrypt message.</span>";
}

function decrypt_message($msg,$sender,$receiver)
{
    global $db;
    $senderkey=$db->fetch_single($db->query("/*qc=on*/SELECT `security_key` from `user_settings` WHERE `userid` = {$sender}"));
    $receiverkey=$db->fetch_single($db->query("/*qc=on*/SELECT `security_key` from `user_settings` WHERE `userid` = {$receiver}"));
    $key = hash("sha512","{$senderkey}.{$receiverkey}");
	if (openssl_decrypt($msg,"AES-256-ECB",$key))
		return stripslashes(str_replace(array("\\n\\r", "\\n", "\\r"), "", openssl_decrypt($msg,"AES-256-ECB",$key)));
	else
		return "<span class='text-danger'>Failed to decrypt message. This is likely due to either the sender or recipient changing their password.</span>";
}

function staffnotes_entry($user,$text,$whodo=-1)
{
	global $db,$api,$userid,$ir;
	$user = (isset($user) && is_numeric($user)) ? abs($user) : 0;
	$text = (isset($text) && !is_array($text)) ? $db->escape(strip_tags(stripslashes($text))) : '';
    if (empty($user) || !isset($text)) {
        return false;
    }
    $q = $db->query("/*qc=on*/SELECT `staff_notes` FROM `users` WHERE `userid` = {$user}");
    if ($db->num_rows($q) == 0) {
        return false;
    }
	if ($whodo == 0)
	{
		$r['username']="CID Admin";
		$whodo=1;
	}
	elseif ($whodo == -1)
	{
		$r['username']=$ir['username'];
		$whodo=$userid;
	}
	else 
	{
		$r['username']=$api->SystemUserIDtoName($whodo);
	}
	$notes=$db->escape($db->fetch_single($q));
	$date=date('m/d/Y');
	$date.=" at ";
	$date.=date('g:iA');
	$text = "{$date}: {$text} -{$r['username']} [{$whodo}]

";
	$sql="{$text}{$notes}";
	$db->query("UPDATE `users` SET `staff_notes` = '{$sql}' WHERE `userid` = {$user}");
}

function parseImage($url)
{
	if (strpos($url, 'https://') !== false) 
	{
		return $url;
	}
	else
	{
		$url=removeFrontTag($url);
		return "https://images.weserv.nl/?url={$url}&errorredirect=ssl:{$url}";
	}
}

function removeFrontTag($url)
{
	$url=str_replace("http://","",$url);
	$url=str_replace("https://","",$url);
	$url=str_replace("www.","",$url);
	return $url;
}

function user_log($user,$logname,$value=1)
{
	global $db;
	$q=$db->query("/*qc=on*/SELECT * FROM `user_logging` WHERE `userid` = {$user} AND `log_name` = '{$logname}'");
	if ($db->num_rows($q) == 0)
	{
		$db->query("INSERT INTO `user_logging` (`userid`, `log_name`, `value`) VALUES ('{$user}', '{$logname}', '{$value}')");
	}
	else
	{
		$db->query("UPDATE `user_logging` SET `value` = `value` + {$value} WHERE `userid` = {$user} and `log_name` = '{$logname}'");
	}
}

function isApp()
{
    global $ir;
    if ($ir['browser'] == 'App')
        if ($ir['os'] == 'Android')
            return true;
}

function isMobile()
{
	global $ir;
	if ($ir['os'] == 'Android')
		return true;
	elseif ($ir['os'] == 'iPhone')
		return true;
	elseif ($ir['os'] == 'iPad')
		return true;
	elseif ($ir['os'] == 'iPod')
		return true;
	elseif ($ir['os'] == 'Mobile')
		return true;
	else
		return false;
}

function missionCheck()
{
	global $db, $api;
	$time=time();
	$q=$db->query("/*qc=on*/SELECT * FROM `missions` WHERE `mission_end` < {$time}");
	while ($r=$db->fetch_row($q))
	{
		if ($r['mission_kill_count'] < $r['mission_kills'])
		{
			notification_add($r['mission_userid'],"You have completely failed your mission. Better luck next time.");
		}
		else
		{
			notification_add($r['mission_userid'],"You have successfully completed your mission. You have been credited " . number_format($r['mission_reward']) . " Copper Coins.");
			$db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + {$r['mission_reward']} WHERE `userid` = {$r['mission_userid']}");
		}
		$db->query("DELETE FROM `missions` WHERE `mission_id` = {$r['mission_id']}");
	}
}

function toast($title,$txt,$time=-1,$icon='https://res.cloudinary.com/dydidizue/image/upload/v1520819511/logo-optimized.png')
{
    if ($time == -1)
        $time=time();
    echo "<div class='toast' role='alert' aria-live='assertive' aria-atomic='true' id='toast' data-delay='3000' style='z-index: 1000; position: absolute; top: 0; right: 0;'>
            <div class='toast-header'>
                <img src='{$icon}' class='rounded mr-2'>
                    <strong class='mr-auto'>{$title}</strong>
                    <small>" . DateTime_Parse($time) . "</small>
                    <button type='button' class='ml-2 mb-1 close' data-dismiss='toast' aria-label='Close'>
                        <span aria-hidden='true'>&times;</span>
                    </button>
            </div>
          <div class='toast-body'>
            {$txt}
          </div>
        </div>";
}

function addToEconomyLog($type = 'Misc', $curr = 'copper', $change = 0)
{
	global $db;
	$todayLogID = date('Ymd');
	$monthLogID = date('Ym--');
	$yearLogID = date('Y----');
	$q=$db->query("SELECT *
					FROM `economy_log` 
					WHERE `ecDate` = '{$todayLogID}' 
					AND `ecSource` = '{$type}' 
					AND `ecCurrency` = '{$curr}' 
					LIMIT 1");
	$q2=$db->query("SELECT *
					FROM `economy_log` 
					WHERE `ecDate` = '{$monthLogID}' 
					AND `ecSource` = '{$type}' 
					AND `ecCurrency` = '{$curr}' 
					LIMIT 1");
	$q3=$db->query("SELECT *
					FROM `economy_log` 
					WHERE `ecDate` = '{$yearLogID}' 
					AND `ecSource` = '{$type}' 
					AND `ecCurrency` = '{$curr}' 
					LIMIT 1");
	//Insert today
	if ($db->num_rows($q) == 0)
	{
		$db->query("INSERT INTO `economy_log` (`ecDate`, `ecSource`, `ecCurrency`, `ecChange`) VALUES 
		('{$todayLogID}', '{$type}', '{$curr}', '{$change}')");
	}
	else
	{
		$db->query("UPDATE `economy_log` 
					SET `ecChange` = `ecChange` + '{$change}' 
					WHERE `ecDate` = '{$todayLogID}' 
					AND `ecSource` = '{$type}' 
					AND `ecCurrency` = '{$curr}' 
					LIMIT 1");
	}
	//Insert month
	if ($db->num_rows($q) == 0)
	{
		$db->query("INSERT INTO `economy_log` (`ecDate`, `ecSource`, `ecCurrency`, `ecChange`) VALUES 
		('{$monthLogID}', '{$type}', '{$curr}', '{$change}')");
	}
	else
	{
		$db->query("UPDATE `economy_log` 
					SET `ecChange` = `ecChange` + '{$change}' 
					WHERE `ecDate` = '{$monthLogID}' 
					AND `ecSource` = '{$type}' 
					AND `ecCurrency` = '{$curr}' 
					LIMIT 1");
	}
	//Insert year
	if ($db->num_rows($q) == 0)
	{
		$db->query("INSERT INTO `economy_log` (`ecDate`, `ecSource`, `ecCurrency`, `ecChange`) VALUES 
		('{$yearLogID}', '{$type}', '{$curr}', '{$change}')");
	}
	else
	{
		$db->query("UPDATE `economy_log` 
					SET `ecChange` = `ecChange` + '{$change}' 
					WHERE `ecDate` = '{$yearLogID}' 
					AND `ecSource` = '{$type}' 
					AND `ecCurrency` = '{$curr}' 
					LIMIT 1");
	}
}

function addToEconomyLogDate($type = 'Misc', $curr = 'copper', $change = 0, $date)
{
	global $db;
	$todayLogID = date('Ymd', $date);
	$q=$db->query("SELECT *
					FROM `economy_log` 
					WHERE `ecDate` = '{$todayLogID}' 
					AND `ecSource` = '{$type}' 
					AND `ecCurrency` = '{$curr}' 
					LIMIT 1");
	if ($db->num_rows($q) == 0)
	{
		$db->query("INSERT INTO `economy_log` (`ecDate`, `ecSource`, `ecCurrency`, `ecChange`) VALUES 
		('{$todayLogID}', '{$type}', '{$curr}', '{$change}')");
	}
	else
	{
		$db->query("UPDATE `economy_log` 
					SET `ecChange` = `ecChange` + '{$change}' 
					WHERE `ecDate` = '{$todayLogID}' 
					AND `ecSource` = '{$type}' 
					AND `ecCurrency` = '{$curr}' 
					LIMIT 1");
	}
}

function backupDatabase()
{
	global $_CONFIG;
	$filename='cid_backup-'.date('y-m-d').'-'.date('H-i-s').'.sql';
	$result=exec("mysqldump {$_CONFIG['database']} --password={$_CONFIG['password']} --user={$_CONFIG['username']} --single-transaction >/var/www/mysql/".$filename,$output);
}

function isDevEnv()
{
	if (determine_game_urlbase() != "chivalryisdeadgame.com")
		return true;
}

function removeOldEffects()
{
	global $db;
	$time = time();
	$db->query("DELETE FROM `users_effects` WHERE `effectTimeOut` < {$time}");
}

function clamp($currentValue, $minValue, $maxValue)
{
    return max($minValue, (min($maxValue, $currentValue)));
}

function statParser($stat)
{
    $statNamesArray = array("maxenergy" => "Maximum Energy", "maxwill" => "Maximum Will",
        "maxbrave" => "Maximum Bravery", "level" => "Level",
        "maxhp" => "Maximum Health", "strength" => "Strength",
        "agility" => "Agility", "guard" => "Guard",
        "labor" => "Labor", "iq" => "IQ",
        "infirmary" => "Infirmary Time", "dungeon" => "Dungeon Time",
        "primary_currency" => "Copper Coins", "secondary_currency"
        => "Chivalry Tokens", "crimexp" => "Experience", "vip_days" =>
        "VIP Days");
        return $statNamesArray[$stat];
}

function equipSlotParser($slot)
{
    $slotNamesArray = array("equip_primary" => "Primary Weapon",
        "equip_secondary" => "Secondary Weapon",
        "equip_armor" => "Armor",
        "equip_potion" => "Combat Potion",
        "equip_badge" => "Profile Badge",
        "equip_ring_primary" => "Primary Ring",
        "equip_ring_secondary" => "Secondary Ring",
        "equip_pendant" => "Pendant",
        "equip_necklace" => "Necklace");
    return $slotNamesArray[$slot];
}

function doDailyBankInterest()
{
    global $db;
    $last72 = time() - (86400*3);
    $bankQuery=$db->query("SELECT `userid`, `bank`, `vip_days`, `laston` FROM `users` WHERE `bank` > 0 AND `laston` > '{$last72}'");
    while ($r = $db->fetch_row($bankQuery))
    {
        $maxBank = returnMaxInterest($r['userid']);
        if ($r['bank'] <= ($maxBank+1))
        {
            $cutoff = $last72;
            $perc = 20;
            if ($r['vip_days'] == 0)
            {
                $perc = 50;
                $cutoff = $cutoff - (86400*2);
            }
            if ($r['laston'] > $cutoff)
            {
                $addedAmount = $r['bank'] / $perc;
                $db->query("UPDATE `users` SET `bank` = `bank` + {$addedAmount} WHERE `userid` = {$r['userid']}");
                addToEconomyLog('Bank Interest', 'copper', $addedAmount);
            }
                    
        }
    }
}

function doDailyFedBankInterest()
{
    global $db;
    $last72 = time() - (86400*3);
    $bankQuery=$db->query("SELECT `userid`, `bigbank`, `vip_days`, `laston` FROM `users` WHERE `bigbank` > 0 AND `laston` > '{$last72}'");
    while ($r = $db->fetch_row($bankQuery))
    {
        $maxBank = returnMaxInterest($r['userid'])*10;
        if ($r['bigbank'] <= ($maxBank+1))
        {
            $cutoff = $last72;
            $perc = 20;
            if ($r['vip_days'] == 0)
            {
                $perc = 50;
                $cutoff = $cutoff - (86400*2);
            }
            if ($r['laston'] > $cutoff)
            {
                $addedAmount = $r['bigbank'] / $perc;
                $db->query("UPDATE `users` SET `bigbank` = `bigbank` + {$addedAmount} WHERE `userid` = {$r['userid']}");
                addToEconomyLog('Bank Interest', 'copper', $addedAmount);
            }
                    
        }
    }
}

function doDailyVaultBankInterest()
{
    global $db;
    $last72 = time() - (86400*3);
    $bankQuery=$db->query("SELECT `userid`, `vaultbank`, `vip_days`, `laston` FROM `users` WHERE `vaultbank` > 0 AND `laston` > '{$last72}'");
    while ($r = $db->fetch_row($bankQuery))
    {
        $maxBank = returnMaxInterest($r['userid'])*50;
        if ($r['vaultbank'] <= ($maxBank+1))
        {
            $cutoff = $last72;
            $perc = 20;
            if ($r['vip_days'] == 0)
            {
                $perc = 50;
                $cutoff = $cutoff - (86400*2);
            }
            if ($r['laston'] > $cutoff)
            {
                $addedAmount = $r['vaultbank'] / $perc;
                $db->query("UPDATE `users` SET `vaultbank` = `vaultbank` + {$addedAmount} WHERE `userid` = {$r['userid']}");
                addToEconomyLog('Bank Interest', 'copper', $addedAmount);
            }
                    
        }
    }
}

function purgeOldLogs()
{
    global $db;
    $ThirtyDaysAgo = time() - 2592000;
    $db->query("DELETE FROM `logs` WHERE `log_time` < {$ThirtyDaysAgo}");
    $db->query("DELETE FROM `mail` WHERE `mail_time` < {$ThirtyDaysAgo}");
    $db->query("DELETE FROM `notifications` WHERE `notif_time` < {$ThirtyDaysAgo}");
    $db->query("DELETE FROM `guild_notifications` WHERE `gn_time` < {$ThirtyDaysAgo}");
    $db->query("DELETE FROM `comments` WHERE `cTIME` < {$ThirtyDaysAgo}");
    $db->query("DELETE FROM `fedjail_appeals` WHERE `fja_time` < {$ThirtyDaysAgo}");
    $db->query("DELETE FROM `guild_crime_log` WHERE `gclTIME` < {$ThirtyDaysAgo}");
    $db->query("DELETE FROM `login_attempts` WHERE `timestamp` < {$ThirtyDaysAgo}");
    $db->query("DELETE FROM `attack_logs` WHERE `attack_time` < {$ThirtyDaysAgo}");
}

function getCurrentPage()
{
    return $_SERVER['REQUEST_URI'];
}

function loadImageAsset($img, $size = 1)
{   
    return "<img src='./assets/img/{$img}' style='width:{$size}rem;'></img>";
}

function getNextDayReset()
{
    return strtotime("tomorrow");
}

function returnGameTitle()
{
    global $set;
    $prefix = "";
    $url = determine_game_urlbase();
    $devDomains = array("192.168.128.151/cid", "127.0.0.1", "localhost");   //add your directory to this list
    if (in_array($url, $devDomains))
        $prefix = "[DEV]";
    elseif ($url != "chivalryisdeadgame.com")
        $prefix = "[UNSUPPORTED]";
    return $prefix . " " . $set['WebsiteName'];
    
}