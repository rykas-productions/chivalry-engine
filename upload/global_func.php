<?php
/*
	File:		global_func.php
	Created: 	4/5/2016 at 12:04AM Eastern Time
	Info: 		Functions used all over the game.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
//Load additional function files from /func/ dir
$dir = scandir(dirname(__FILE__) . '/func/');
foreach ($dir as $func)
{
    if (preg_match('/\.php$/', $func)) 
    {
        require_once dirname(__FILE__) . "/func/" . $func;
    }
}

//Load our constants from the /const/ dir
$dir = scandir(dirname(__FILE__) . '/const/');
foreach ($dir as $func)
{
    if (preg_match('/\.php$/', $func))
    {
        require_once dirname(__FILE__) . "/const/" . $func;
    }
}
/*
	Parses the time since the timestamp given.
	@param int $time_stamp for time since.
	@param boolean $ago to display the "ago" after the string. (Default = true)
*/

function reachedMonthlyDonationGoal()
{
    global $set, $_CONFIG;
    if ($set['MonthlyDonationGoal'] >= $_CONFIG['donationGoal'])
        return true;
    else
        return false;
}
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


/**
 * Shortens the number input to a readable short number. (IE 1.2B) Will create a hoverable 
 * HTML object that reveals the original number.
 * @param int $n original number
 * @return string Html containing shortened number and hoverable original number.
 */
function shortNumberParse($n)
{
    if ($n < 1000)
        $n_format = number_format($n);
    elseif ($n < 10000)
        $n_format = number_format($n / 1000, 2) . "K";
    elseif ($n < 1000000)
        $n_format = number_format($n / 1000, 1) . "K";
    elseif ($n < 1000000000)
        $n_format = number_format($n / 1000000, 1) . "M";
    elseif ($n < 1000000000000)
        $n_format = number_format($n / 1000000000, 1) . "B";
    elseif ($n < 1000000000000000)
        $n_format = number_format($n / 1000000000000, 1) . "T";
    elseif ($n < 1000000000000000000)
        $n_format = number_format($n / 1000000000000000, 1) . " Quadrillion";
    elseif ($n < 1000000000000000000000)
        $n_format = number_format($n / 1000000000000000000, 1) . " Sextillion";
    elseif ($n < 1000000000000000000000000)
        $n_format = number_format($n / 1000000000000000000000, 1) . " Sextillion";
    else
        $n_format = number_format($n);
    return "<span data-toggle='tooltip' data-placement='top' title='" . number_format($n) . "'>{$n_format}</span>";
}

function numberToByteParse($n)
{
    $kb = 1024;
    $mb = $kb * 1024;
    $gb = $mb * 1024;
    $tb = $gb * 1024;
    if ($n < $kb)
        $n_format = number_format($n);
    elseif ($n < $mb)
        $n_format = number_format($n / $kb, 2) . "K";
    elseif ($n < $gb)
        $n_format = number_format($n / $mb, 1) . "M";
    elseif ($n < $tb)
        $n_format = number_format($n / $gb, 1) . "G";
    elseif ($n > $tb)
        $n_format = number_format($n / $tb, 2) . "T";
    return "<span data-toggle='tooltip' data-placement='top' title='" . number_format($n) . " bytes'>{$n_format}B</span>";
}

/**
 * Parses the input timestamp into a human readable number.
 * @param int $time Unix timestamp (time())
 * @return string
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

/**
 * Test if input email is valid. Does not test if exists, but rather if it fits standard email formatting.
 * @param string $email Email address to test
 * @return bool
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
    $time = time();
    $db->query("INSERT INTO `notifications` VALUES(NULL, {$userid}, {$time}, 'unread', '{$text}', '{$icon}', '{$color}')");
    return true;
}

/**
 * Internal function used to update the current auth'd user's information,
 * usually to make sure they aren't over their max, and other small things.
 */
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


/**
 * Internal function used to check academic courses, and reward players 
 * who have completed them.
 */
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
            $ev .= "; " . shortNumberParse($coud['ac_str']) . " Strength";
        }
        //Course credits guard, so add onto the query.
        if ($coud['ac_grd'] > 0) 
		{
            $upd .= ", us.guard = us.guard + {$coud['ac_grd']}";
            $ev .= "; " . shortNumberParse($coud['ac_grd']) . " Guard";
        }
        //Course credits labor, so add onto the query.
        if ($coud['ac_lab'] > 0) 
		{
            $upd .= ", us.labor = us.labor + {$coud['ac_lab']}";
            $ev .= "; " . shortNumberParse($coud['ac_lab']) . " Labor";
        }
        //Course credits agility, so add onto the query.
        if ($coud['ac_agl'] > 0) 
		{
            $upd .= ", us.agility = us.agility + {$coud['ac_agl']}";
            $ev .= "; " . shortNumberParse($coud['ac_agl']) . " Agility";
        }
        //Course credits IQ, so add onto the query.
        if ($coud['ac_iq'] > 0) 
		{
            $upd .= ", us.IQ = us.IQ + {$coud['ac_iq']}";
            $ev .= "; " . shortNumberParse($coud['ac_iq']) . " IQ";
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

/**
 * Internal function. Called directly by game. Place extra function in 
 * this function to be called each page load.
 */
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
	doBlacksmithCheck();
	
	//If user is auth'd, run max Will Check
	if (isset($ir))
	{
		maxWillCheck();
	}
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
 * @return string Randomly generated string.
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
    $pw = PASSWORD_DEFAULT;
    //Set the password cost via settings.
	if ($lvl == 'Member')
		$options = ['cost' => $set['Password_Effort'],];
	else
		$options = ['cost' => $set['Password_Effort']+1,];
    //Return the generated password.
		return password_hash(base64_encode(hash('sha256', $password, true)), $pw, $options);
}

/**
 * Easily outputs an alert to the client. May be broken into smaller functions...
 * @param string $type Type of alert. [Valid: danger, success, info, warning, primary, secondary, light, dark]
 * @param string $title Title of the alert.
 * @param string $text = Text to be display in the alert.
 * @param bool $doredirect = Redirect to a new page when link is clicked? [Default = true]
 * @param string $redirect = URL to go on alert click. [Default = back] ('back' will reload current page)
 * @param string $redirecttext = Redirect link text [Default = Back]
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
    $domain = (!empty($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : "";
    $turi =(!empty($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : "";
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



/**
 * @deprecated Please use $api->SystemLogsAdd($user, $logtype, $input);
 * Adds a log into the game logging system. 
 */
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

/**
 * Generate a random number, using the ranges input. Will be updated to use more  
 * functions later, so use this function for random number generation.
 * @param int $min = Minimum number to be picked randomly. [Default = 0]
 * @param int $max = Maximum number to be picked randomly. [Default = PHP_INT_MAX]
 * @return int
 */
function Random($min = 0, $max = PHP_INT_MAX)
{
        return random_int($min, $max);
}

/**
 * Generate a random decimal, using the ranges input. Uses the Random() function to generate
 * the numbers.
 * @param number $min = Minimum number to be picked randomly. [Default = 0]
 * @param number $max = Maximum number to be picked randomly. [Default = PHP_INT_MAX]
 * @return float
 */
function randomDecimal($min = 0, $max = PHP_INT_MAX, $decimalPlaces = 1)
{
    $loop = 0;
    $start = 1;
    while ($loop != $decimalPlaces)
    {
        $start *= 10;
        $loop++;
    }
    return Random($min * $start, $max * $start) / $start;
}

/**
 * Generate a random float, using the ranges input.
 * @return float
 */
function randomFloat()
{
    return Random() / mt_getrandmax();
}

/**
 * Round a float to whichever number of decimal places defined.
 * @param float $float Original float.
 * @param int $decimal Number of digitals in final float. [Default = 1]
 * @return float
 */
function roundFloat($float, $decimal = 1)
{
    return round($float, $decimal);
}

/**
 * Reads/grabs data from a URL input.
 * @param string $url URL to view and cache.
 * @return string Content from URL.
 */
function curlOpenFile($url)
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

/**
 * Test if the input URL is actually an image.
 * @param string $url URL of image to test.
 * @return bool
 */
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

/**
 * Internal function for creating lists of pages easier.
 * @param int $perpage Items displayed per page.
 * @param int $total Total items to be displayed.
 * @param int $currentpage Current page of items being viewed
 * @param string $url File being viewed.
 * @return string Pagination
 */
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

/**
 * Internal function to log in javascript
 * @param string $tyoe Type of error.
 * @param string $txt Error text.
 */
function cslog($type,$txt)
{
	echo "<script>console.{$type}('{$txt}');</script>";
}

/**
 * Internal function encrypt messages sent between players.
 * @param string $msg Message to be encrypted.
 * @param int $sender User ID of the message composer.
 * @param int $receiver USER of the message receiver.
 * @return string Encrypted message to be stored in database.
 */
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

/**
 * Internal function decrypt messages sent between players.
 * @param string $msg Message text to be decrypt
 * @param int $sender User ID of the message composer.
 * @param int $receiver USER of the message receiver.
 * @return string Decrypted message, good for displaying to client.
 */
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

/**
 * Internal function to handle staff notes being edited on a player.
 * @param int $user User ID of the player whom staff notes are being edited.
 * @param string $text Text to be placed in the user's staff notes.
 * @param int $whodo User ID of the player who edited the staff notes. [Default -1 (No one)]
 * @return string Decrypted message, good for displaying to client.
 */
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

/**
 * Internal function to handle parsing images. This basically redirects non-HTTPs
 * @param string $url URL of the image to display.
 * @return string Image URL.
 */
function parseImage($url)
{
	if (strpos($url, 'https://') !== false)
		return $url;
	else
	{
		$url=removeFrontTag($url);
		return "https://images.weserv.nl/?url={$url}&errorredirect=ssl:{$url}";
	}
}

/**
 * Internal function to remove the front tags on the URL. (IE: www;htttp;https;etc.)
 * @param string $url URL to remove from tags from.
 * @return string URL without front tags.
 */
function removeFrontTag($url)
{
	$url=str_replace("http://","",$url);
	$url=str_replace("https://","",$url);
	$url=str_replace("www.","",$url);
	return $url;
}

/**
 * Internal function to check if the current user is on the game app.
 * @return bool
 */
function isApp()
{
    global $ir;
    if ($ir['browser'] == 'App')
        if ($ir['os'] == 'Android')
            return true;
}

/**
 * Internal function to check if the current user is on a mobile device.
 * @return bool
 */
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


function toast($title,$txt,$time=-1,$icon='https://res.cloudinary.com/dydidizue/image/upload/v1520819511/logo-optimized.png')
{
    echo "<div class='alert alert-primary cidToast fade show' role='alert' data-dismiss='alert' >
		        <strong><i>{$txt}</i></strong>
            </div>";
}

function addToEconomyLog($type = 'Misc', $curr = 'copper', $change = 0)
{
	global $db;
	$todayLogID = date('Ymd');
	$q=$db->query("SELECT *
					FROM `economy_log` 
					WHERE `ecDate` = '{$todayLogID}' 
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

/**
 * Internal function to backup the game database to file.
 */
function backupDatabase()
{
	global $_CONFIG;
	$filename='cid_backup-'.date('y-m-d').'-'.date('H-i-s').'.sql';
	exec("mysqldump {$_CONFIG['database']} --password={$_CONFIG['password']} --user={$_CONFIG['username']} --single-transaction >/var/www/mysql/".$filename,$output);
	exec("mysqldump {$_CONFIG['database']} --password={$_CONFIG['password']} --user={$_CONFIG['username']} --single-transaction >/var/www/chivalryisdeadgame.com/html/cache/latest.sql",$output);
}

/**
 * Internal function to check if the game is being offline.
 * @return bool
 */
function isDevEnv()
{
	if (determine_game_urlbase() != "chivalryisdeadgame.com")
		return true;
}

/**
 * Clamps an input integer to the range specified. Because PHP 
 * doesn't have a convience function...
 * @param int $currentValue Value to be clamped.
 * @param int $minValue Minimum value.
 * @param int $maxValue Maximum value.
 * @return int
 */
function clamp($currentValue, $minValue, $maxValue)
{
    return max($minValue, (min($maxValue, $currentValue)));
}

/**
 * Internal function to parse internal stat names to their player friendly names.
 * @param string $stat Internal stat name.
 * @return string Player friendly stat name.
 */
function statParser($stat)
{
    $statNamesArray = array("maxenergy" => "Maximum Energy", "maxwill" => "Maximum Will",
        "maxbrave" => "Maximum Bravery", "level" => "Level",
        "hp" => "Health", "energy" => "Energy", 
        "maxhp" => "Maximum Health", "strength" => "Strength",
        "agility" => "Agility", "guard" => "Guard",
        "labor" => "Labor", "iq" => "IQ",
        "infirmary" => "Infirmary Time", "dungeon" => "Dungeon Time",
        "primary_currency" => "Copper Coins", "secondary_currency"
        => "Chivalry Tokens", "crimexp" => "Experience", "vip_days" =>
        "VIP Days", "will" => "Will",
        "luck" => "Luck", "brave" => "Bravery",
        "energy" => "Energy"
    );
        return $statNamesArray[$stat];
}

/**
 * Internal function to parse internal equipment slot names to their player friendly names.
 * @param string $slot Internal equipment slot name.
 * @return string Player friendly equipment slot name.
 */
function equipSlotParser($slot)
{
    $slotNamesArray = array(slot_prim_wep => "Primary Weapon",
        slot_second_wep => "Secondary Weapon",
        slot_armor => "Armor",
        slot_potion => "Combat Potion",
        slot_badge => "Profile Badge",
        slot_prim_ring => "Primary Ring",
        slot_second_ring => "Secondary Ring",
        slot_pendant => "Pendant",
        slot_necklace => "Necklace",
        slot_wed_ring => "Wedding Ring"
    );
    return $slotNamesArray[$slot];
}

/**
 * Internal function to calculate bank interest on all valid city bank accounts.
 * @internal
 */
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

/**
 * Internal function to calculate bank interest on all valid federal bank accounts.
 * @internal
 */
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

/**
 * Internal function to calculate bank interest on all valid vault bank accounts.
 * @internal
 */
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

/**
 * Internal function to delete all logs older than 30 days old.
 * @internal
 */
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

/**
 * Internal function to get the current page.
 * @internal
 */
function getCurrentPage()
{
    return $_SERVER['REQUEST_URI'];
}

/**
 * Load images directly from the '/assets/img/' directory.
 * @param string $img Name of image, including extension, to load.$this
 * @param int $size Size of the image, based in rem. [Default = 1]
 * @return string Image HTML of the asset.
 */
function loadImageAsset($img, $size = 1)
{   
    $rootAssetDir = "./assets/";
    return "<img src='{$rootAssetDir}img/{$img}' style='width:{$size}rem;'></img>";
}

/**
 * Return the time of the next daily reset.
 * @internal
 * @return int Next daily reset
 */
function getNextDayReset()
{
    return strtotime("tomorrow");
}

/**
 * Internal function to load the game's title, adding extra data to it if needed in an unsupported or dev environment.
 * @internal
 * @return string Game's page titles.
 */
function returnGameTitle()
{
    global $set;
    $prefix = "";
    $url = determine_game_urlbase();
    $devDomains = array("192.168.1.30/cid", "127.0.0.1", 
                        "localhost"
    );   //add your directory to this list
    if (in_array($url, $devDomains))
        $prefix = "[DEV]";
    elseif ($url != "chivalryisdeadgame.com")
        $prefix = "[UNSUPPORTED]";
    return $prefix . " " . $set['WebsiteName'];
    
}

/**
 * Attempt to load a module from its module id. Valid modules must have a 'initializeModule()' function 
 * or this will not work. This will return the module's configuration data.
 * @internal
 * @param string $moduleID 
 * @return int Next daily reset
 */
function attemptLoadModule($moduleID)
{
    global $ir, $h;
    if (function_exists('initializeModule'))
    {
        initializeModule();
    }
    else
    {
        trigger_error("Module ID: <span class='font-weight-bold'><u>{$moduleID}</u></span> 
        does not have the required `initializeModule();` function in file. Please create it.");
    }
    return getConfigForPHP($moduleID);
}

//This function will take a given $file and execute it directly in php.
//This code is for use within a codeigntier framework application

//It tries three methods so it should almost allways work.
//method 1: Directly via cli using mysql CLI interface. (Best choice)
//method 2: use mysqli_multi_query
//method 3: use PDO exec

//It tries them in that order and checks to make sure they WILL work based on various requirements of those options
function execute_sql($file, $db_database, $hostname, $username, $password, $driver, $connectionID)
{
    //1st method; directly via mysql
    $mysql_paths = array();
    
    //use mysql location from `which` command.
    $mysql = trim(`which mysql`);
    
    if (is_executable($mysql))
    {
        array_unshift($mysql_paths, $mysql);
    }
    
    //Default paths
    $mysql_paths[] = '/Applications/MAMP/Library/bin/mysql';  //Mac Mamp
    $mysql_paths[] = 'c:\xampp\mysql\bin\mysql.exe';//XAMPP
    
    $mysql_paths[] = '/usr/bin/mysql';  //Linux
    $mysql_paths[] = '/usr/local/mysql/bin/mysql'; //Mac
    $mysql_paths[] = '/usr/local/bin/mysql'; //Linux
    $mysql_paths[] = '/usr/mysql/bin/mysql'; //Linux
    
    $database = escapeshellarg($db_database);
    $db_hostname = escapeshellarg($hostname);
    $db_username= escapeshellarg($username);
    $db_password = escapeshellarg($password);
    $file_to_execute = escapeshellarg($file);
    foreach($mysql_paths as $mysql)
    {
        if (is_executable($mysql))
        {
            $execute_command = "\"$mysql\" --host=$db_hostname --user=$db_username --password=$db_password $database < $file_to_execute";
            $status = false;
            system($execute_command, $status);
            return $status == 0;
        }
    }
    
    if ($driver == 'mysqli')
    {
        //2nd method; using mysqli
        mysqli_multi_query($connectionID,file_get_contents($file));
        //Make sure this keeps php waiting for queries to be done
        do{} while(mysqli_more_results($connectionID) && mysqli_next_result($connectionID));
        return TRUE;
    }
    
    //3rd Method Use PDO as command. See http://stackoverflow.com/a/6461110/627473
    //Needs php 5.3, mysqlnd driver
    $mysqlnd = function_exists('mysqli_fetch_all');
    
    if ($mysqlnd && version_compare(PHP_VERSION, '5.3.0') >= 0)
    {
        $database = $db_database;
        $db_hostname = $hostname;
        $db_username= $username;
        $db_password = $password;
        
        $dsn = "mysql:dbname=$database;host=$db_hostname";
        $db = new PDO($dsn, $db_username, $db_password);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, 0);
        $sql = file_get_contents($file);
        $db->exec($sql);
        
        return TRUE;
        
    }
    
    return FALSE;
}

function doBlacksmithCheck()
{
    global $db, $api;
    $time = time();
    $q = $db->query("SELECT * FROM `smelt_inprogress` WHERE `sip_time` <= {$time}");
    if ($db->num_rows($q) > 0)
    {
        while ($r = $db->fetch_row($q))
        {
            $smeltRecipe = $db->query("/*qc=on*/SELECT * FROM `smelt_recipes` WHERE `smelt_id` = {$r['sip_recipe']}");
            $r2 = $db->fetch_row($smeltRecipe);
            $api->UserGiveItem($r['sip_user'], $r2['smelt_output'], $r2['smelt_qty_output']);
            $api->GameAddNotification($r['sip_user'], "Your {$r2['smelt_qty_output']} x " . $api->SystemItemIDtoName($r2['smelt_output']) . "(s) have finished being smelted and have been added to your inventory.");
            $db->query("DELETE FROM `smelt_inprogress` WHERE `sip_id`= {$r['sip_id']}");
        }
    }
}

function logTokenMarketAvg($bought,$total)
{
    global $db;
    $time = time();
    $db->query("INSERT INTO `token_market_avg` (`token_sold`, `token_total`, `token_time`) VALUES ('{$bought}', '{$total}', '{$time}')");
}

function logMarketAvg($qty, $cost)
{
    global $db;
    $time = time();
    $db->query("INSERT INTO `token_market_avg` (`token_sold`, `token_total`, `token_time`) VALUES ('{$qty}', '{$cost}', '{$time}')");
}