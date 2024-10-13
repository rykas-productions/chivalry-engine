<?php
/*
	File:		globals_nonauth.php
	Created: 	4/5/2016 at 12:05AM Eastern Time
	Info: 		Calls all internal files/settings for when a user
				is not logged in.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
//Profiler start time
include('forms/include_top.php');
@ini_set('zlib.output_compression', 1);
//Require the error handler.
require "lib/basic_error_handler.php";
set_error_handler('error_php');
set_exception_handler("exception_handler");
ob_implicit_flush(true);
//Set user's timezone.
date_default_timezone_set("America/New_York");
//If this file is opened directly.
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
$time = time();
//Set session name and start it.
session_name('CENGINE');
@session_start();
header('X-Frame-Options: SAMEORIGIN');
header("X-DNS-Prefetch-Control: off");
//If session is not started, regenerate ID and load it.
if (!isset($_SESSION['started'])) {
    session_regenerate_id();
    $_SESSION['started'] = true;
}
ob_start();
//Require styling.
if (!isset($_GET['othermenu']))
	require "header_nonauth.php";
else
	require "header_nonauth2.php";
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
//Include API file.
include("class/class_api.php");
$api = new api;
$set = array();
$settq = $db->query("/*qc=on*/SELECT *
					 FROM `settings`");
//Settings in friendly variables.
while ($r = $db->fetch_row($settq)) {
    $set[$r['setting_name']] = $r['setting_value'];
}
//Parse the headers.
if (!isset($hidehdr))
{
	$h = new headers;
	$h->startheaders();
}
require_once 'lib/DiscordMsg/Msg.php';
require_once 'lib/DiscordMsg/DiscordMsg.php';
//Update data in-game externally.
check_data();
//Run the crons if possible.
/*foreach (glob("crons/*.php") as $filename) {
    include $filename;
}*/