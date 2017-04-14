<?php
/*
	File: js//script/checkun.php
	Created: 4/4/2017 at 7:10PM Eastern Time
	Info: PHP file for checking a user's inputted username
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
if (isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD']))
{
    if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST')
    {
        // Ignore a GET request
        header('HTTP/1.1 400 Bad Request');
        exit;
    }
}
require_once('../../global_func.php');
if (!is_ajax())
{
    header('HTTP/1.1 400 Bad Request');
    exit;
}
require_once('../../globals_nonauth.php');
$username = isset($_POST['username']) ? stripslashes($_POST['username']) : '';
if (!$username)
{
    echo "<script>document.getElementById('unerror').className = 'has-error';</script>";
	die(alert('danger',$lang['ERROR_GENERIC'],$lang['SCRIPT_ERR3'],false));
	 
}
if ((strlen($username) < 3))
{
    echo "<script>document.getElementById('unerror').className = 'has-error';</script>";
	die(alert('danger',$lang['ERROR_GENERIC'],$lang['SCRIPT_ERR4'],false));
}
if ((strlen($username) > 21))
{
    echo "<script>document.getElementById('unerror').className = 'has-error';</script>";
	die(alert('danger',$lang['ERROR_GENERIC'],$lang['SCRIPT_ERR5'],false));
}
$e_username = $db->escape($username);
$q = $db->query("SELECT COUNT(`userid`) FROM users WHERE username = '{$e_username}'");
if ($db->fetch_single($q))
{
	 echo "<script>document.getElementById('unerror').className = 'has-error';</script>";
	 die(alert('danger',$lang['ERROR_GENERIC'],$lang['SCRIPT_ERR6'],false));
}
else
{
    echo "<script>document.getElementById('unerror').className = 'has-success';</script>";
}
$db->free_result($q);
