<?php
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
$username =
        isset($_POST['username']) ? stripslashes($_POST['username']) : '';
if (!$username)
{
    echo "<script>document.getElementById('username').style.backgroundColor = '#f2dede';</script>";
	die("<div class='alert alert-danger' role='alert'><strong>Error!</strong> 
	 You need to enter a username!</div>");
}
if ((strlen($username) < 3))
{
    echo "<script>document.getElementById('username').style.backgroundColor = '#f2dede';</script>";
	die("<div class='alert alert-danger' role='alert'><strong>Error!</strong> 
	 The name you chose ($username) is too short! We require, at minimum, three characters!</div>");
}
if ((strlen($username) > 21))
{
    echo "<script>document.getElementById('username').style.backgroundColor = '#f2dede';</script>";
	die("<div class='alert alert-danger' role='alert'><strong>Error!</strong> 
	 The name you chose ($username) is too long! You can only have a username who's maximum length is 20 characters.</div>");
}
$e_username = $db->escape($username);
$q =
        $db->query(
                "SELECT COUNT(`userid`) FROM users WHERE username = '{$e_username}'");
if ($db->fetch_single($q))
{
    echo "<div class='alert alert-danger' role='alert'><strong>Error!</strong> 
	 The name you chose ($username) is already taken! We suggest adding a number to the end of it!</div>";
	 echo "<script>document.getElementById('username').style.backgroundColor = '#f2dede';</script>";
}
else
{
    echo "<script>document.getElementById('username').style.backgroundColor = '#dff0d8';</script>";
}
$db->free_result($q);
