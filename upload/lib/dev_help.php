<?php
/*
	File: lib/dev_help.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: A small script that outputs all stored POST, GET, COOKIE and SESSION variables.
	Author: TheMasterGeneral
	Website: http://mastergeneral156.pcriot.com/
*/

//Set to true to enable developement help!
define('DEV', false);
if (DEV)
{
	echo "<div class='alert alert-warning' role='alert'><strong>Warning!</strong> You have development mode on. Please be sure to turn this off when you launch the game.</div>";
	echo "<pre class='pre-scrollable'>";
	echo "Dumping all variables stored in POST.<br />";
	var_dump($_POST);
	echo "<br />Dumping all variables stored in GET.<br />";
	var_dump($_GET);
	echo "<br />Dumping all variables stored in COOKIE.<br />";
	var_dump($_COOKIE);
	echo "<br />Dumping all variables stored in SESSION.<br />";
	var_dump($_SESSION);
	echo "</pre>";
}