<?php
if (strpos($_SERVER['PHP_SELF'], "globals_nonauth.php") !== false)
{
    exit;
}
session_name('CENGINE');
@session_start();
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
switch ($lang) {
  case 'en':
  $lang_file = 'en_us.php';
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
include_once 'lang/'.$lang_file;
if (!isset($_SESSION['started']))
{
    session_regenerate_id();
    $_SESSION['started'] = true;
}
ob_start();
if (function_exists("get_magic_quotes_gpc") == false)
{

    function get_magic_quotes_gpc()
    {
        return 0;
    }
}
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
require "lib/basic_error_handler.php";
set_error_handler('error_php');
include "config.php";
define("MONO_ON", 1);
require "class/class_db_mysqli.php";
require_once('global_func.php');
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
