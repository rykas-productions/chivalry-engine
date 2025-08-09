<?php
/*
	File:		globals_nonauth.php
	Created: 	4/5/2016 at 12:05AM Eastern Time
	Info: 		Calls all internal files/settings for when a user
				is not logged in.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
//If this file is opened directly.
if (strpos($_SERVER['PHP_SELF'], "globals_nonauth.php") !== false) {
    exit;
}

// Set secure session parameters
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);  // Changed from 1 to 0 to allow HTTP
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Lax');

// Set secure cookie parameters for theme
if (!isset($_COOKIE['theme'])) {
    setcookie('theme', 1, [
        'expires' => time() + 86400,
        'path' => '/',
        'domain' => '',
        'secure' => false,  // Changed from true to false to allow HTTP
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    $_COOKIE['theme'] = 1;
}

$time = time();
session_name('CENGINE');
session_start();

// Set security headers
header('X-Frame-Options: DENY'); 
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.google.com https://ajax.googleapis.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://code.jquery.com; img-src 'self' data: https:; font-src 'self' https://cdnjs.cloudflare.com; connect-src 'self';");
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), camera=(), microphone=()');

// Generate secure session ID if not started
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

ob_start();
//Require the error handler.
require "lib/basic_error_handler.php";
set_error_handler('error_php');

set_exception_handler(function($exception) {
    error_critical(
        'An unexpected error occurred.',
        $exception->getMessage(),
        'Exception in ' . $exception->getFile() . ' on line ' . $exception->getLine(),
        ['stack_trace' => $exception->getTraceAsString()]
        );
});

//Require styling.
require "header_nonauth.php";
include "config.php";
define("MONO_ON", 1);
//Connect to database.
require "class/class_db_{$_CONFIG['driver']}.php";
require_once('global_func.php');
$db = new database;
$db->configure($_CONFIG['hostname'], $_CONFIG['username'],
    $_CONFIG['password'], $_CONFIG['database'], $_CONFIG['persistent']);
$db->connect();
$c = $db->connection_id;
//Update data in-game externally.
check_data();
//Include API file.
include("class/class_api.php");
$api = new api;
$set = array();
$settq = $db->query("SELECT *
					 FROM `settings`");
//Settings in friendly variables.
while ($r = $db->fetch_row($settq)) {
    $set[$r['setting_name']] = $r['setting_value'];
}
//Parse the headers.
$h = new headers;
$h->startheaders();
//Run the crons if possible.
foreach (glob("crons/*.php") as $filename) {
    include $filename;
}