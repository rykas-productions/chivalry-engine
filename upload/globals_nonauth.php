<?php
/*
	File:		globals_nonauth.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Handles all the game logic when a user is not authenticated.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
	MIT License

	Copyright (c) 2019 TheMasterGeneral

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
*/
//If this file is opened directly.
if (strpos($_SERVER['PHP_SELF'], "globals_nonauth.php") !== false) {
    exit;
}
//If theme isn't stored in cookie, set it to cookie.
if (!isset($_COOKIE['theme'])) {
    setcookie('theme', 1, time() + 86400);
    $_COOKIE['theme'] = 1;
}
$time = time();
//Set session name and start it.
session_name('CEV2');
@session_start();
header('X-Frame-Options: SAMEORIGIN');
//If session is not started, regenerate ID and load it.
if (!isset($_SESSION['started'])) {
    session_regenerate_id();
    $_SESSION['started'] = true;
}
ob_start();
//Require the error handler.
require "lib/basic_error_handler.php";
set_error_handler('error_php');
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
checkData();
//Include API file.
include("class/class_api.php");
$api = new api;
$api->user = new user;
$api->guild = new guild;
$api->game = new game;
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