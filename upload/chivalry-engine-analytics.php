<?php
$menuhide=1;
require('globals_nonauth.php');
$gameName = (isset($_POST['gamename'])) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])*$/i", $_POST['gamename']) ? $db->escape(strip_tags(stripslashes($_POST['gamename']))) : '';
$gameURL = (isset($_POST['domain'])) ? $db->escape(stripslashes(strip_tags($_POST['domain']))) : "";
$gameVersion = (isset($_POST['version'])) ? $db->escape(stripslashes(strip_tags($_POST['version']))) : "";
$gameDB = (isset($_POST['dbtype'])) ? $db->escape(stripslashes(strip_tags($_POST['dbtype']))) : "";
$gameInstall = (isset($_POST['install'])) && is_numeric($_POST['install']) ? abs($_POST['install']) : time();
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
$lastseen = time();
if ($error == 0)
{
    if (isset($_POST['update']))
    {
        $q = $db->query("SELECT * FROM `ce_anal` WHERE `url` = '{$gameURL}'");
        if ($db->num_rows($q) > 0)
        {
            $db->query("UPDATE `ce_anal` SET
                            `version`='{$gameVersion}', 
                            `gamename`='{$gameName}', 
                            `dbtype`='{$gameDB}', 
                            `lastseen`='{$lastseen}'
                            WHERE `url` = '{$gameURL}'");
        }
        else
        {
            $db->query("INSERT INTO `ce_anal`
    				(`url`, `installtime`, `version`, `gamename`, `dbtype`, `lastseen`)
    				VALUES
    				('{$gameURL}', '0', '{$gameVersion}', '{$gameName}', '{$gameDB}', '{$lastseen}')");
        }
    }
    else
    {
    	$db->query("INSERT INTO `ce_anal` 
    				(`url`, `installtime`, `version`, `gamename`, `dbtype`, `lastseen`) 
    				VALUES 
    				('{$gameURL}', '{$gameInstall}', '{$gameVersion}', '{$gameName}', '{$gameDB}', '{$lastseen}')");
    }
}