<?php
/*
	File:		globals_nonauth.php
	Created: 	4/5/2016 at 12:05AM Eastern Time
	Info: 		Calls all internal files/settings for when a user
				is not logged in.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
if (strpos($_SERVER['PHP_SELF'], "globals_nonauth.php") !== false)
{
    exit;
}
session_name('CENGINE');
@session_start();
header('X-Frame-Options: SAMEORIGIN');
if(isset($_POST['lang']))
{
	$lang = $_POST['lang'];
	$_SESSION['lang'] = $lang;
	setcookie('lang', $lang, time() + (3600 * 24 * 30));
}
else if(isset($_SESSION['lang']))
{
	$lang = $_SESSION['lang'];
}
else if(isset($_COOKIE['lang']))
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
	case 'danish':
		$lang_file = 'danish.php';
		break;
	default:
		$lang_file = 'en_us.php';
 
}
include_once 'lang/'.$lang_file;
if (!isset($_SESSION['started']))
{
    session_regenerate_id();
    $_SESSION['started'] = true;
}
ob_start();
require "lib/basic_error_handler.php";
set_error_handler('error_php');
include "config.php";
define("MONO_ON", 1);
require "class/class_db_{$_CONFIG['driver']}.php";
require_once('global_func.php');
$db = new database;
$db->configure($_CONFIG['hostname'], $_CONFIG['username'],
        $_CONFIG['password'], $_CONFIG['database'], $_CONFIG['persistent']);
$db->connect();
$c = $db->connection_id;
include("class/class_api.php");
$api = new api;
$set = array();
$settq = $db->query("SELECT *
					 FROM `settings`");
while ($r = $db->fetch_row($settq))
{
    $set[$r['setting_name']] = $r['setting_value'];
}
