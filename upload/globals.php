<?php
if (strpos($_SERVER['PHP_SELF'], "globals.php") !== false)
{
    exit;
}
session_name('CENGINE');
session_start();
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
require "lib/dev_help.php";
set_error_handler('error_php');
require "global_func.php";
$domain = determine_game_urlbase();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == 0)
{
    $login_url = "login.php";
    header("Location: {$login_url}");
    exit;
}
if(isset($_SESSION['last_active']) && (time() - $_SESSION['last_active'] > 1800))
{
	header("Location: logout.php");
	exit;
}
$_SESSION['last_active'] = time();
$userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : 0;
require "header.php";
include "config.php";
include "template.php";
define("MONO_ON", 1);
require "class/class_db_{$_CONFIG['driver']}.php";
$db = new database;
$db->configure($_CONFIG['hostname'], $_CONFIG['username'], $_CONFIG['password'], $_CONFIG['database'], $_CONFIG['persistent']);
$db->connect();
$c = $db->connection_id;
$set = array();
$settq = $db->query("SELECT * FROM `settings`");
while ($r = $db->fetch_row($settq))
{
    $set[$r['setting_name']] = $r['setting_value'];
}
global $jobquery, $housequery, $voterquery;
if (isset($jobquery) && $jobquery)
{
    $is =
            $db->query(
                    "SELECT `u`.*, `us`.*, `j`.*, `jr`.*,
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid`=`us`.`userid`
                     LEFT JOIN `jobs` AS `j` ON `j`.`jID` = `u`.`job`
                     LEFT JOIN `jobranks` AS `jr`
                     ON `jr`.`jrID` = `u`.`jobrank`
                     WHERE `u`.`userid` = {$userid}
                     LIMIT 1");
}
else if (isset($housequery) && $housequery)
{
    $is =
            $db->query(
                    "SELECT `u`.*, `us`.*, `e`.*
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid`=`us`.`userid`
                     LEFT JOIN `estates` AS `e` ON `e`.`house_will` = `u`.`maxwill`
                     WHERE `u`.`userid` = {$userid}
                     LIMIT 1");
}
else if (isset($voterquery) && $voterquery)
{
	$UIDB=$db->query("SELECT * FROM `uservotes` WHERE `userid` = {$userid}");
	if (!($db->num_rows($UIDB)))
	{
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
}
else
{
    $is =
            $db->query(
                    "SELECT `u`.*, `us`.*
                     FROM `users` AS `u`
                     INNER JOIN `userstats` AS `us`
                     ON `u`.`userid`=`us`.`userid`
                     WHERE `u`.`userid` = {$userid}
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
    $login_url = "login.php";
    header("Location: {$login_url}");
    exit;
}
check_level();
check_data();
getOS($_SERVER['HTTP_USER_AGENT']);
getBrowser($_SERVER['HTTP_USER_AGENT']);
$h = new headers;
include("class/class_api.php");
$api = new api;
if (isset($nohdr) == false || !$nohdr)
{
    $h->startheaders();
    $fm = number_format($ir['primary_currency']);
    $cm =number_format($ir['secondary_currency']);
    $lv = date('F j, Y, g:i a', $ir['laston']);
    global $atkpage;
    if ($atkpage)
    {
        $h->userdata($ir, $lv, $fm, $cm, 0);
    }
    else
    {
        $h->userdata($ir, $lv, $fm, $cm);
    }
    global $menuhide;
}
foreach (glob("crons/*.php") as $filename) 
{ 
    include $filename; 
}
$time = time();
$get = $db->query("SELECT `sip_recipe` FROM `smelt_inprogress` WHERE `sip_user` = {$userid} AND `sip_time` < {$time}");
if($db->num_rows($get)) 
{
    $r = $db->fetch_single($get);
	$r2 = $db->fetch_row($db->query("SELECT * FROM `smelt_recipes` WHERE `smelt_id` = {$r}"));
    $api->UserGiveItem($userid,$r2['smelt_output'],$r2['smelt_qty_output']);
    notification_add($userid, "You have successfully smelted your {$r2['smelt_qty_output']} " . $api->SystemItemIDtoName($r2['smelt_output']) . "(s).");
    $db->query("DELETE FROM `smelt_inprogress` WHERE `sip_user`={$userid} AND `sip_time` < {$time}");
}