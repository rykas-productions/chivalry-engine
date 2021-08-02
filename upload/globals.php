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
include('forms/include_top.php');
require "lib/basic_error_handler.php";
set_error_handler('error_php');
set_exception_handler("exception_handler");
if (!isset($disablespeed))
{
	@ini_set('zlib.output_compression', 1);
	ob_implicit_flush(true);
}
//Set user's timezone.
date_default_timezone_set("America/New_York");
//If file is loaded directly.
if (strpos($_SERVER['PHP_SELF'], "globals.php") !== false) {
    exit;
}
//Set session name, then start session.
session_name('CENGINE');
session_start();
$time = time();
header('X-Frame-Options: SAMEORIGIN');
//If session has not started, regenerate session ID, then start it.
if (!isset($_SESSION['started'])) {
    session_regenerate_id();
    $_SESSION['started'] = true;
}
if (!isset($_SESSION['disable_alerts']))
{
    $_SESSION['disable_alerts'] = true;
}
//Set user's theme to cookies for 30 days.
if (!isset($_COOKIE['theme'])) {
    setcookie('theme', '1', time() + 86400);
}
ob_start();
//Require the error handler and developer helper files.
require "lib/dev_help.php";
//Require main functions file.
require "global_func.php";
$domain = determine_game_urlbase();
//If user is not logged in, redirect to login page.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == 0) {
	$login_url = "login.php";
	setcookie('loginRedirect', getCurrentPage(), time() + 3600);
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
include "config.php";
define("MONO_ON", 1);
//Require the database wrapper and connect to database.
require "class/class_db_{$_CONFIG['driver']}.php";
$db = new database;
$db->configure($_CONFIG['hostname'], $_CONFIG['username'], $_CONFIG['password'], $_CONFIG['database'], $_CONFIG['persistent']);
$db->connect();
$c = $db->connection_id;
$set = array();
$settq = $db->query("/*qc=on*/SELECT * FROM `settings`");
//Settings get resolved to be used easily elsewhere.
while ($r = $db->fetch_row($settq)) {
    $set[$r['setting_name']] = $r['setting_value'];
}
global $jobquery, $housequery, $voterquery;
if (isset($jobquery) && $jobquery) {
    $is =
        $db->query(
            "/*qc=on*/SELECT `u`.*, `us`.*, `j`.*, `jr`.*, `uas`.*
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid`=`us`.`userid`
                     LEFT JOIN `jobs` AS `j` 
					 ON `j`.`jRANK` = `u`.`job`
					 LEFT JOIN `user_skills` AS `sk` 
					 ON `sk`.`userid` = `u`.`userid`
                     LEFT JOIN `job_ranks` AS `jr`
                     ON `jr`.`jrID` = `u`.`jobrank`
					 INNER JOIN `user_settings` AS `uas`
                     ON `u`.`userid`=`uas`.`userid`
                     WHERE `u`.`userid` = {$userid}
                     LIMIT 1");
} else if (isset($housequery) && $housequery) {
    $is =
        $db->query(
            "/*qc=on*/SELECT `u`.*, `us`.*, `e`.*
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid`=`us`.`userid`
					 INNER JOIN `user_settings` AS `uas`
                     ON `u`.`userid`=`uas`.`userid`
                     LEFT JOIN `estates` AS `e` 
					 ON `e`.`house_will` = `u`.`maxwill`
					 LEFT JOIN `user_skills` AS `sk` 
					 ON `sk`.`userid` = `u`.`userid`
                     WHERE `u`.`userid` = {$userid}
                     LIMIT 1");
} else if (isset($voterquery) && $voterquery) {
    $UIDB = $db->query("/*qc=on*/SELECT * FROM `uservotes` WHERE `userid` = {$userid}");
    if (!($db->num_rows($UIDB))) {
        $db->query("INSERT INTO `uservotes` (`userid`, `voted`) VALUES ('{$userid}', '');");
    }
    $is =
        $db->query(
            "/*qc=on*/SELECT `u`.*, `us`.*, `uv`.*, `uas`.*
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid`=`us`.`userid`
					 INNER JOIN `uservotes` AS `uv`
                     ON `u`.`userid`=`uv`.`userid`
					 INNER JOIN `user_settings` AS `uas`
                     ON `u`.`userid`=`uas`.`userid`
					 LEFT JOIN `user_skills` AS `sk` 
					 ON `sk`.`userid` = `u`.`userid`
                     WHERE `u`.`userid` = {$userid}
                     LIMIT 1");
} else {
    $is =
        $db->query(
            "/*qc=on*/SELECT `u`.*, `us`.*, `uas`.*
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid`=`us`.`userid`
					 INNER JOIN `user_settings` AS `uas`
                     ON `u`.`userid`=`uas`.`userid`
					 LEFT JOIN `user_skills` AS `sk` 
					 ON `sk`.`userid` = `u`.`userid`
                     WHERE `u`.`userid` = {$userid}
                     LIMIT 1");
}
//Put user's data into friendly variable.
$ir = $db->fetch_row($is);
$userUI=getCurrentUserPref('oldUI',0);
if ($userUI == 1)
	require "header_old.php";
elseif ($userUI == 0)
	require "header.php";
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
$os=getOS($_SERVER['HTTP_USER_AGENT']);
$browser=getBrowser($_SERVER['HTTP_USER_AGENT']);
$ir['os']=$os;
$ir['browser']=$browser;
$h = new headers;
//Include API file.
include("class/class_api.php");
$api = new api;
//Load game sound system
include('class/class_audio.php');
$sound = new sound;
//Include Forms file.
include("class/class_form.php");
$form = new form;
//If requested file doesn't want the header hidden.
if (isset($nohdr) == false || !$nohdr) 
{
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
cslog('log',"You are using {$browser} on {$os}.");
//Run the crons if possible.
/*foreach (glob("crons/*.php") as $filename) {
    include $filename;
}*/
$get = $db->query("/*qc=on*/SELECT `sip_recipe`,`sip_user` FROM `smelt_inprogress` WHERE `sip_time` < {$time}");
//Select completed smelting recipes and give to the user.
if ($db->num_rows($get)) {
    $r = $db->fetch_row($get);
    $r2 = $db->fetch_row($db->query("/*qc=on*/SELECT * FROM `smelt_recipes` WHERE `smelt_id` = {$r}"));
    $api->UserGiveItem($r['user'], $r2['smelt_output'], $r2['smelt_qty_output']);
    $api->GameAddNotification($r['user'], "You have successfully smelted your {$r2['smelt_qty_output']} " . $api->SystemItemIDtoName($r2['smelt_output']) . "(s).");
    $db->query("DELETE FROM `smelt_inprogress` WHERE `sip_user`={$r['user']} AND `sip_time` < {$time}");
}
$UIDB = $db->query("/*qc=on*/SELECT * FROM `mining` WHERE `userid` = {$userid}");
if (!($db->num_rows($UIDB))) {
    $db->query("INSERT INTO `mining` (`userid`, `max_miningpower`, `miningpower`, `miningxp`, `buyable_power`, `mining_level`, `mine_boost`) 
    VALUES ('{$userid}', '100', '100', '0', '1', '1', '0');");
}
include('dailyreward.php');
check_data();
updateMostUsersCount();

//For chat, maybe?
$_SESSION['userName']=$ir['username'];

if (isset($moduleID) && !empty($moduleID))
{
    $moduleConfig = attemptLoadModule($moduleID);
    if ((isset($_GET['config']) && ($ir['user_level'] == 'Admin')))
    {
        echo "<h3>Config for {$moduleID}</h3><hr />";
        if (isset($_POST['formSubmitValue']))
        {
            $configArray = [];
            foreach ($_POST as $k => $v)
            {
                if (!($k == 'formSubmitValue'))
                {
                    $configArray[$k] = $db->escape(htmlentities($v, ENT_QUOTES, 'ISO-8859-1'));
                }
            }
            writeConfigToDB($moduleID, formatConfig($configArray));
            echo "Updated module config.";
        }
        else
        {
            $config=getConfigForPHP($moduleID);
            $formArray=array();
            foreach ($config as $k => $v)
            {
                array_push($formArray,array('text',$k,$k,$v));
            }
            createPostForm('?config',$formArray, 'Update Module Config');
        }
        die($h->endpage());
    }
}