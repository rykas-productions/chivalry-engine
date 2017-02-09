<?php
session_name('CENGINE');
session_start();
if (!isset($_SESSION['started']))
{
    session_regenerate_id();
    $_SESSION['started'] = true;
}
require_once('globals_nonauth.php');
if (isset($_SESSION['userid']))
{
    $sessid = (int) $_SESSION['userid'];
    if (isset($_SESSION['attacking']) && $_SESSION['attacking'] > 0)
    {
        require_once('globals_nonauth.php');
        alert("warning","{$lang['ERROR_GENERIC']}","{$lang['MENU_XPLOST']}");
		$db->query("UPDATE `users` SET `xp` = 0, `attacking` = 0 WHERE `userid` = {$sessid}");
		$_SESSION['attacking'] = 0;
        session_regenerate_id(true);
        session_unset();
        session_destroy();
		$api->SystemLogsAdd($sessid,'login',"Successfully logged out and lost experience.");
		header("Refresh:3; url=login.php");
		exit;
    }
}
$api->SystemLogsAdd($sessid,'login',"Successfully logged out.");
session_regenerate_id(true);
session_unset();
session_destroy();
$login_url = 'login.php';
header("Location: {$login_url}");