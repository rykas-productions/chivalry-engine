<?php
$menuhide=1;
require('globals_nonauth.php');
$gameName = (isset($_GET['gamename'])) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])*$/i", $_GET['gamename']) ? $db->escape(strip_tags(stripslashes($_GET['gamename']))) : '';
$gameURL = (isset($_GET['domain'])) ? $db->escape(stripslashes(strip_tags($_GET['domain']))) : "";
$gameVersion = (isset($_GET['version'])) ? $db->escape(stripslashes(strip_tags($_GET['version']))) : "";
$gameDB = (isset($_GET['dbtype'])) ? $db->escape(stripslashes(strip_tags($_GET['dbtype']))) : "";
$gameInstall = (isset($_GET['install'])) && is_numeric($_GET['install']) ? abs($_GET['install']) : '';
$validDB = array('pdo', 'mysqli');
if (!in_array($gameDB, $validDB))
	$gameDB = "Unknown";
$error = 0;
if (empty($gameName))
	$error++;
if (empty($gameURL))
	$error++;
if (empty($gameVersion))
	$error++;
if (empty($gameDB))
	$error++;
if (empty($gameInstall))
	$error++;
if ($error == 0)
{
	$db->query("INSERT INTO `ce_anal` 
				(`url`, `installtime`, `version`, `gamename`, `dbtype`) 
				VALUES 
				('{$gameURL}', '{$gameInstall}', '{$gameVersion}', '{$gameName}', '{$gameDB}')");
}
var_dump($_GET);