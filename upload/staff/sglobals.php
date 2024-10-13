<?php
/*
	File: staff/sglobals.php
	Created: 6/21/2016 at 2:02PM Eastern Time
	Info: Functions for staff-only actions.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/
include('../forms/include_top.php');
@ini_set('zlib.output_compression', 1);
require "../lib/basic_error_handler.php";
set_error_handler('error_php');
set_exception_handler("exception_handler");
ob_implicit_flush(true);
if (strpos($_SERVER['PHP_SELF'], "sglobals.php") !== false) {
    exit;
}
if (strpos($_SERVER['PHP_SELF'], "globals_nonauth.php") !== false) {
    exit;
}
if (isset($_SERVER['HTTP_PURPOSE']) && $_SERVER['HTTP_PURPOSE'] == 'prefetch') {
    // This is a prefetch request, so avoid triggering critical operations
    exit();
} elseif (isset($_SERVER['HTTP_X_PURPOSE']) && $_SERVER['HTTP_X_PURPOSE'] == 'preview') {
    // This is a prerender request
    exit();
}
session_name('CENGINE');
session_start();
$time = time();
header('X-Frame-Options: SAMEORIGIN');
header("X-DNS-Prefetch-Control: off");
if (!isset($_SESSION['started'])) {
    session_regenerate_id();
    $_SESSION['started'] = true;
}
ob_start();
require "../lib/dev_help.php";
require "../global_func.php";
$domain = determine_game_urlbase();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == 0) {
    $login_url = "../login.php";
    header("Location: {$login_url}");
    exit;
}
if (isset($_SESSION['last_active']) && (time() - $_SESSION['last_active'] > 1800)) {
    header("Location: ../logout.php");
    exit;
}
$_SESSION['last_active'] = time();
$userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : 0;
require "sheader.php";
include "../config.php";
global $_CONFIG;
define("MONO_ON", 1);
require "../class/class_db_{$_CONFIG['driver']}.php";
$db = new database;
$db->configure($_CONFIG['hostname'], $_CONFIG['username'], $_CONFIG['password'], $_CONFIG['database'], $_CONFIG['persistent']);
$db->connect();
$c = $db->connection_id;
$set = array();
$settq = $db->query("/*qc=on*/SELECT * FROM `settings`");
while ($r = $db->fetch_row($settq)) {
    $set[$r['setting_name']] = $r['setting_value'];
}
global $jobquery, $housequery;
if (isset($jobquery) && $jobquery) {
    $is = $db->query("/*qc=on*/SELECT `u`.*, `us`.*, `j`.*, `jr`.*
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid`=`us`.`userid`
					 INNER JOIN `user_settings` AS `uas`
                     ON `u`.`userid`=`uas`.`userid`
                     LEFT JOIN `jobs` AS `j` ON `j`.`jRANK` = `u`.`job`
                     LEFT JOIN `job_ranks` AS `jr`
                     ON `jr`.`jrID` = `u`.`jobrank`
                     WHERE `u`.`userid` = '{$userid}'
                     LIMIT 1");
} else if (isset($housequery) && $housequery) {
    $is = $db->query("/*qc=on*/SELECT `u`.*, `us`.*, `h`.*
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid`=`us`.`userid`
					 INNER JOIN `user_settings` AS `uas`
                     ON `u`.`userid`=`uas`.`userid`
                     LEFT JOIN `houses` AS `h` ON `h`.`hWILL` = `u`.`maxwill`
                     WHERE `u`.`userid` = '{$userid}'
                     LIMIT 1");
} else {
    $is = $db->query(
            "/*qc=on*/SELECT `u`.*, `us`.*, `uas`.*
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid`=`us`.`userid`
					 INNER JOIN `user_settings` AS `uas`
                     ON `u`.`userid`=`uas`.`userid`
                     WHERE `u`.`userid` = {$userid}
                     LIMIT 1");
}
$ir = $db->fetch_row($is);
if ($ir['force_logout'] != 'false') {
    $db->query("UPDATE `users` SET `force_logout` = 'false' WHERE `userid` = {$userid}");
    session_unset();
    session_destroy();
    $login_url = "../login.php";
    header("Location: {$login_url}");
    exit;
}
if (($ir['last_login'] > $_SESSION['last_login']) && !($ir['last_login'] == $_SESSION['last_login'])) {
    session_unset();
    session_destroy();
    $login_url = "../login.php";
    header("Location: {$login_url}");
    exit;
}
include("../class/class_api.php");
$api = new api;
//Load game sound system
include('../class/class_audio.php');
$sound = new sound;
if (!$api->UserMemberLevelGet($userid, 'forum moderator')) {
    $index = ('../index.php');
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
if ($atkpage) {
    $h->userdata($ir, 0);
} else {
    $h->userdata($ir);
}
/*foreach (glob("../crons/*.php") as $filename) {
    include $filename;
} */