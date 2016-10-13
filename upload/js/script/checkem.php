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
$email = isset($_POST['email']) ? stripslashes($_POST['email']) : '';
if (empty($email))
{
    echo "<script>document.getElementById('email').style.backgroundColor = '#f2dede';</script>";
	die("<div class='alert alert-danger' role='alert'><strong>Error!</strong> 
	 You must enter an email address!</div>");
}
if (!valid_email($email))
{
    echo "<script>document.getElementById('email').style.backgroundColor = '#f2dede';</script>";
	die("<div class='alert alert-danger' role='alert'><strong>Error!</strong> 
	 The email you entered is in a bad format. Valid email addresses are written using @domain.com</div>");
}
$e_email = $db->escape($email);
$q =
        $db->query(
                "SELECT COUNT(`userid`) FROM users WHERE `email` = '{$e_email}'");
if ($db->fetch_single($q) != 0)
{
    echo "<script>document.getElementById('email').style.backgroundColor = '#f2dede';</script>";
	echo "<div class='alert alert-danger' role='alert'><strong>Error!</strong> 
	 The email you chose is already taken. Please try again.</div>";
}
else
{
    echo "<script>document.getElementById('email').style.backgroundColor = '#dff0d8';</script>";
}
$db->free_result($q);
