<?php
/*
	File: 		staff/sglobals.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Basic loading needed for the staff panel pages.
	Author: 	TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine/
	
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
if (strpos($_SERVER['PHP_SELF'], "sglobals.php") !== false) {
    exit;
}
session_name('CEV2');
session_start();
$time = time();
header('X-Frame-Options: SAMEORIGIN');
if (!isset($_SESSION['started'])) {
    session_regenerate_id();
    $_SESSION['started'] = true;
}
ob_start();
require "../lib/basic_error_handler.php";
set_error_handler('error_php');
require "../global_func.php";
$domain = getGameURL();
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
define("MONO_ON", 1);
require "../class/class_db_" . constant("db_driver") . ".php";
$db = new database;
$db->configure(constant("db_host"), constant("db_username"), constant("db_password"), constant("db_database"), 0);
$db->connect();
$c = $db->connection_id;
$set = array();
$settq = $db->query("SELECT * FROM `settings`");
while ($r = $db->fetch_row($settq)) {
    $set[$r['setting_name']] = $r['setting_value'];
}
global $jobquery, $housequery;
if (isset($jobquery) && $jobquery) {
    $is = $db->query("SELECT `u`.*, `us`.*, `j`.*, `jr`.*
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid`=`us`.`userid`
                     LEFT JOIN `jobs` AS `j` ON `j`.`jRANK` = `u`.`job`
                     LEFT JOIN `job_ranks` AS `jr`
                     ON `jr`.`jrID` = `u`.`jobrank`
                     WHERE `u`.`userid` = '{$userid}'
                     LIMIT 1");
} else if (isset($housequery) && $housequery) {
    $is = $db->query("SELECT `u`.*, `us`.*, `h`.*
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid`=`us`.`userid`
                     LEFT JOIN `houses` AS `h` ON `h`.`hWILL` = `u`.`maxwill`
                     WHERE `u`.`userid` = '{$userid}'
                     LIMIT 1");
} else {
    $is = $db->query("SELECT `u`.*, `us`.*
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid`=`us`.`userid`
                     WHERE `u`.`userid` = '{$userid}'
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
//Include API file.
include("../class/class_api.php");
$api = new api;
$api->user = new user;
$api->guild = new guild;
$api->game = new game;
if (!$api->user->getStaffLevel($userid, 'forum moderator')) {
    $index = ('../index.php');
    header("Location: {$index}");
}
checkLevel();
checkData();
$h = new headers;
$h->startheaders();
$fm = number_format($ir['primary_currency']);
$cm = number_format($ir['secondary_currency']);
$lv = date('F j, Y, g:i a', $ir['laston']);
global $atkpage;
$staffpage = 1;
if ($atkpage)
    $h->userdata($ir, 0);
else
    $h->userdata($ir);
foreach (glob("../crons/*.php") as $filename) {
    include $filename;
} 