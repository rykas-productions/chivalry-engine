<?php
/*
	File: js//script/checkem.php
	Created: 4/4/2017 at 7:09PM Eastern Time
	Info: PHP file for checking a user's inputted email
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
$email = isset($_POST['email']) ? stripslashes($_POST['email']) : '';
if (empty($email))
{
    echo "<script>document.getElementById('email').style.backgroundColor = '#f2dede';</script>";
	die(alert('danger',$lang['ERROR_GENERIC'],$lang['SCRIPT_ERR2'],false));
}
if (!valid_email($email))
{
    echo "<script>document.getElementById('email').style.backgroundColor = '#f2dede';</script>";
	die(alert('danger',$lang['ERROR_GENERIC'],$lang['SCRIPT_ERR1'],false));
}
$e_email = $db->escape($email);
$q = $db->query("SELECT COUNT(`userid`) FROM users WHERE `email` = '{$e_email}'");
if ($db->fetch_single($q) != 0)
{
    echo "<script>document.getElementById('email').style.backgroundColor = '#f2dede';</script>";
	die(alert('danger',$lang['ERROR_GENERIC'],$lang['SCRIPT_ERR'],false));
}
else
{
    echo "<script>document.getElementById('email').style.backgroundColor = '#dff0d8';</script>";
}
$db->free_result($q);
