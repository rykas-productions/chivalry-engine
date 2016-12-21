<?php
/*
	File: staff/sglobals.php
	Created: 6/21/2016 at 2:02PM Eastern Time
	Info: Functions for staff-only actions.
	Author: TheMasterGeneral
	Website: http://mastergeneral156.pcriot.com/
*/
function staff_csrf_error($goBackTo)
{
    global $h,$lang;
	alert("danger","{$lang['ERROR_SECURITY']}","The action you have done has been blocked for security reasons. Please <a href='{$goBackTo}'>Try again</a>.");
    $h->endpage();
    exit;
}

/**
 * Check the CSRF code we received against the one that was registered for the form - using default code properties ($_POST['verf']).
 * If verification fails, end execution immediately.
 * If not, continue.
 * @param string $formid A unique string used to identify this form to match up its submission with the right token.
 * @param string $code The code the user's form input returned.
 * @return boolean Whether the user provided a valid code or not
 */
if (strpos($_SERVER['PHP_SELF'], "sglobals.php") !== false)
{
    exit;
}
session_name('CENGINE');
session_start();
header('Cache-control: private'); // IE 6 FIX

if(isSet($_GET['lang']))
{
$lang = $_GET['lang'];
 
// register the session and set the cookie
$_SESSION['lang'] = $lang;
 
setcookie('lang', $lang, time() + (3600 * 24 * 30));
}
else if(isSet($_SESSION['lang']))
{
$lang = $_SESSION['lang'];
}
else if(isSet($_COOKIE['lang']))
{
$lang = $_COOKIE['lang'];
}
else
{
$lang = 'en';
}
 
switch ($lang) 
{
	case 'en':
		$lang_file = 'en_us.php';
		break;
	case 'fr':
		$lang_file = 'fr_fr.php';
		break;
	case 'ger':
		$lang_file = 'ger.php';
		break;
	case 'es':
		$lang_file = 'es.php';
		break;
	default:
		$lang_file = 'en_us.php';
 
}
include_once '../lang/'.$lang_file;
if (!isset($_SESSION['started']))
{
    session_regenerate_id();
    $_SESSION['started'] = true;
}
if (!isset($_SESSION['started']))
{
    session_regenerate_id();
    $_SESSION['started'] = true;
}
ob_start();
if (get_magic_quotes_gpc() == 0)
{
    foreach ($_POST as $k => $v)
    {
        $_POST[$k] = addslashes($v);
    }
    foreach ($_GET as $k => $v)
    {
        $_GET[$k] = addslashes($v);
    }
}
require "../lib/basic_error_handler.php";
require "../lib/dev_help.php";
set_error_handler('error_php');
require "../global_func.php";
$domain = determine_game_urlbase();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == 0)
{
    $login_url = "../login.php";
    header("Location: {$login_url}");
    exit;
}
$userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : 0;
require "sheader.php";

include "../config.php";
global $_CONFIG;
define("MONO_ON", 1);
require "../class/class_db_{$_CONFIG['driver']}.php";
$db = new database;
$db->configure($_CONFIG['hostname'], $_CONFIG['username'],
        $_CONFIG['password'], $_CONFIG['database'], $_CONFIG['persistent']);
$db->connect();
$c = $db->connection_id;
$set = array();
$settq = $db->query("SELECT *
					 FROM `settings`");
while ($r = $db->fetch_row($settq))
{
    $set[$r['setting_name']] = $r['setting_value'];
}
global $jobquery, $housequery;
if (isset($jobquery) && $jobquery)
{
    $is =
            $db->query(
                    "SELECT `u`.*, `us`.*, `j`.*, `jr`.*
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid`=`us`.`userid`
                     LEFT JOIN `jobs` AS `j` ON `j`.`jID` = `u`.`job`
                     LEFT JOIN `jobranks` AS `jr`
                     ON `jr`.`jrID` = `u`.`jobrank`
                     WHERE `u`.`userid` = '{$userid}'
                     LIMIT 1");
}
else if (isset($housequery) && $housequery)
{
    $is =
            $db->query(
                    "SELECT `u`.*, `us`.*, `h`.*
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid`=`us`.`userid`
                     LEFT JOIN `houses` AS `h` ON `h`.`hWILL` = `u`.`maxwill`
                     WHERE `u`.`userid` = '{$userid}'
                     LIMIT 1");
}
else
{
    $is =
            $db->query(
                    "SELECT `u`.*, `us`.*
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid`=`us`.`userid`
                     WHERE `u`.`userid` = '{$userid}'
                     LIMIT 1");
}
$ir = $db->fetch_row($is);
if ($ir['force_logout'] != 'false')
{
    $db->query(
            "UPDATE `users`
    		 SET `force_logout` = 'false'
    		 WHERE `userid` = {$userid}");
    session_unset();
    session_destroy();
    $login_url = "../login.php";
    header("Location: {$login_url}");
    exit;
}
include("../class/class_api.php");
$api = new api;
if (!$api->UserMemberLevelGet($userid,'forum moderator'))
{
    $index=('../index.php');
	header("Location: {$index}");
}
check_level();
check_data();
getOS($_SERVER['HTTP_USER_AGENT']);
getBrowser($_SERVER['HTTP_USER_AGENT']);
$h = new headers;
$h->startheaders();
$fm = number_format($ir['primary_currency']);
$cm = number_format($ir['secondary_currency']);
$lv = date('F j, Y, g:i a', $ir['laston']);
global $atkpage;
$staffpage = 1;
if ($atkpage)
{
    $h->userdata($ir, $lv, $fm, $cm, 0);
}
else
{
    $h->userdata($ir, $lv, $fm, $cm);
}
foreach (glob("../crons/*.php") as $filename) 
{ 
    include $filename; 
} 