<?php
/*
	File:		globals.php
	Created: 	4/5/2016 at 12:05AM Eastern Time
	Info: 		Calls all internal files/settings for when a user
				is logged in.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
//Profiler start time
$StartTime = microtime();
if (strpos($_SERVER['PHP_SELF'], "globals.php") !== false) {
    exit;
}

// Set secure session parameters
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);  // Changed from 1 to 0 to allow HTTP
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Lax');

session_name('CENGINE');
session_start();
$time = time();

// Set security headers
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.google.com https://ajax.googleapis.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://code.jquery.com; img-src 'self' data: https:; font-src 'self' https://cdnjs.cloudflare.com; connect-src 'self';");
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), camera=(), microphone=()');

// Prevent clickjacking
header('X-Frame-Options: SAMEORIGIN');

// If session has not started, regenerate session ID with proper entropy
if (!isset($_SESSION['started'])) {
    if (function_exists('random_bytes')) {
        $entropy = random_bytes(32);
    } elseif (function_exists('openssl_random_pseudo_bytes')) {
        $entropy = openssl_random_pseudo_bytes(32);
    } else {
        $entropy = mt_rand() . uniqid(mt_rand(), true);
    }
    session_id(hash('sha256', $entropy));
    session_regenerate_id(true);
    $_SESSION['started'] = true;
}

// Set secure cookie parameters
if (!isset($_COOKIE['theme'])) {
    setcookie('theme', '1', [
        'expires' => time() + 86400,
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

ob_start();
//Require the error handler and developer helper files.
require "lib/basic_error_handler.php";
require "lib/dev_help.php";
set_error_handler('error_php');

set_exception_handler(function($exception) {
    error_critical(
        'An unexpected error occurred.',
        $exception->getMessage(),
        'Exception in ' . $exception->getFile() . ' on line ' . $exception->getLine(),
        ['stack_trace' => $exception->getTraceAsString()]
        );
});

//Require main functions file.
require "global_func.php";
$domain = determine_game_urlbase();
//If user is not logged in, redirect to login page.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == 0) {
    $login_url = "login.php";
    header("Location: {$login_url}");
    exit;
}
//If user was last active over 15 minutes ago, redirect to login to keep account safe.
if (isset($_SESSION['last_active']) && ($time - $_SESSION['last_active'] > 1800)) {
    header("Location: logout.php");
    exit;
}
//Update last active time.
$_SESSION['last_active'] = $time;
$userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : 0;
require "header.php";
include "config.php";
define("MONO_ON", 1);
//Require the database wrapper and connect to database.
require "class/class_db_{$_CONFIG['driver']}.php";
$db = new database;
$db->configure($_CONFIG['hostname'], $_CONFIG['username'], $_CONFIG['password'], $_CONFIG['database'], $_CONFIG['persistent']);
$db->connect();
$c = $db->connection_id;
$set = array();
$settq = $db->query("SELECT * FROM `settings`");
//Settings get resolved to be used easily elsewhere.
while ($r = $db->fetch_row($settq)) {
    $set[$r['setting_name']] = $r['setting_value'];
}
global $jobquery, $housequery, $voterquery;
if (isset($jobquery) && $jobquery) {
    $is =
        $db->query(
            "SELECT `u`.*, `us`.*, `j`.*, `jr`.*
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid`=`us`.`userid`
                     LEFT JOIN `jobs` AS `j` ON `j`.`jRANK` = `u`.`job`
                     LEFT JOIN `job_ranks` AS `jr`
                     ON `jr`.`jrID` = `u`.`jobrank`
                     WHERE `u`.`userid` = {$userid}
                     LIMIT 1");
} else if (isset($housequery) && $housequery) {
    $is =
        $db->query(
            "SELECT `u`.*, `us`.*, `e`.*
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid`=`us`.`userid`
                     LEFT JOIN `estates` AS `e` ON `e`.`house_will` = `u`.`maxwill`
                     WHERE `u`.`userid` = {$userid}
                     LIMIT 1");
} else if (isset($voterquery) && $voterquery) {
    $UIDB = $db->query("SELECT * FROM `uservotes` WHERE `userid` = {$userid}");
    if (!($db->num_rows($UIDB))) {
        $db->query("INSERT INTO `uservotes` (`userid`, `voted`) VALUES ('{$userid}', '');");
    }
    $is =
        $db->query(
            "SELECT `u`.*, `us`.*, `uv`.*
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid`=`us`.`userid`
					 INNER JOIN `uservotes` AS `uv`
                     ON `u`.`userid`=`uv`.`userid`
                     WHERE `u`.`userid` = {$userid}
                     LIMIT 1");
} else {
    $is =
        $db->query(
            "SELECT `u`.*, `us`.*
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid`=`us`.`userid`
                     WHERE `u`.`userid` = {$userid}
                     LIMIT 1");
}
//Put user's data into friendly variable.
$ir = $db->fetch_row($is);
//Put user's current theme to cookie.
if (!isset($_COOKIE['theme'])) {
    setcookie('theme', $ir['theme'], time() + 86400);
}
//If user's account is forced to log out, close session.
if ($ir['force_logout'] != 'false') {
    $db->query("UPDATE `users` SET `force_logout` = 'false' WHERE `userid` = {$userid}");
    session_unset();
    session_destroy();
    $login_url = "login.php";
    header("Location: {$login_url}");
    exit;
}
//If the user's account has been logged in elsewhere, terminate current session.
if (($ir['last_login'] > $_SESSION['last_login']) && !($ir['last_login'] == $_SESSION['last_login'])) {
    session_unset();
    session_destroy();
    $login_url = "login.php";
    header("Location: {$login_url}");
    exit;
}
//Basic chceks around the game.
check_level();
check_data();
getOS($_SERVER['HTTP_USER_AGENT']);
getBrowser($_SERVER['HTTP_USER_AGENT']);
$h = new headers;
//Include API file.
include("class/class_api.php");
$api = new api;
//If requested file doesn't want the header hidden.
if (isset($nohdr) == false || !$nohdr) {
    $h->startheaders();
    $fm = number_format($ir['primary_currency']);
    $cm = number_format($ir['secondary_currency']);
    $lv = date('F j, Y, g:i a', $ir['laston']);
    global $atkpage;
    if ($atkpage) {
        $h->userdata($ir, 0);
    } else {
        $h->userdata($ir);
    }
    global $menuhide;
}
//Run the crons if possible.
foreach (glob("crons/*.php") as $filename) {
    include $filename;
}
$get = $db->query("SELECT `sip_recipe`,`sip_user` FROM `smelt_inprogress` WHERE `sip_time` < {$time}");
//Select completed smelting recipes and give to the user.
if ($db->num_rows($get)) {
    $r = $db->fetch_row($get);
    $r2 = $db->fetch_row($db->query("SELECT * FROM `smelt_recipes` WHERE `smelt_id` = {$r}"));
    $api->UserGiveItem($r['user'], $r2['smelt_output'], $r2['smelt_qty_output']);
    $api->GameAddNotification($r['user'], "You have successfully smelted your {$r2['smelt_qty_output']} " . $api->SystemItemIDtoName($r2['smelt_output']) . "(s).");
    $db->query("DELETE FROM `smelt_inprogress` WHERE `sip_user`={$r['user']} AND `sip_time` < {$time}");
}