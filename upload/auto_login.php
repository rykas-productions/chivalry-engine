<?php
/*
    Auto-login for CEV3 testing
    Sets all required session variables
*/

// Start session with correct name
session_name('CEV3');
session_start();

// Set ALL required session variables
$_SESSION['userid'] = 1;
$_SESSION['loggedin'] = 1;
$_SESSION['last_login'] = time();

// Update database to match session
include('config.php');
$connection = mysqli_connect(db_host, db_username, db_password, db_database);
if ($connection) {
    $current_time = time();
    mysqli_query($connection, "UPDATE users SET last_login = {$current_time}, laston = {$current_time} WHERE userid = 1");
    mysqli_close($connection);
}

// Redirect to logged in page
header("Location: loggedin.php");
exit;
?>