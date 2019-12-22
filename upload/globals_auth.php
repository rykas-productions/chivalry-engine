<?php
/*
	File:		globals_auth.php
	Created: 	9/29/2019 at 7:28PM Eastern Time
	Author:		TheMasterGeneral
	Website: 	https://github.com/rykas-productions/chivalry-engine
	MIT License
	Copyright (c) 2019 TheMasterGeneral
	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:
	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
*/
require('functions/func_startup.php');
register_shutdown_function('shutdown');
//Comment out to disable raw PHP errors.
enableErrorOutput();
if (!checkDirectAccess('globals_nonauth.php'))
{
	die('This file may not be accessed directly.');
}
setSession('CEV3');
require('./lib/basic_error_handler.php');
set_error_handler('error_php');
//Require styling.
require('./headers_auth.php');
require('./config.php');
//Connect to database.
define('MONO_ON', 1);
require("./class/class_db_mysqli.php");
require('./functions/global_functions.php');
$db = new database;
$db->configure(constant("db_host"), constant("db_username"), constant("db_password"), constant("db_database"), 0);
$db->connect();
$dbConnectionID = $db->connection_id;
autoSessionCheck();
$_SESSION['last_active'] = returnUnixTimestamp();
$userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : 0;
$ir=returnCurrentUserData($userid);
$h = new headers;
$h->startHeaders();
if (isset($moduleID) && !empty($moduleID))
{
	if (function_exists('initialize'))
		initialize();
	else
		trigger_error("Module ID: <span class='font-weight-bold'><u>{$moduleID}</u></span> does not have the required `initialize();` function in file. Please create it.");
	$moduleConfig=getConfigForPHP($moduleID);
	if (isset($_GET['config']) && ($ir['staffLevel'] == 2))
	{
		echo "<h3>Config for {$moduleID}</h3><hr />";
		if (isset($_POST['formSubmitValue']))
		{
			$configArray = [];
			foreach ($_POST as $k => $v)
			{
				if (!($k == 'formSubmitValue'))
				{
					$configArray[$k] = makeSafeText($v);
				}
			}
			writeConfigToDB($moduleID, formatConfig($configArray));
			echo "Updated module config.";
		}
		else
		{
			$config=getConfigForPHP($moduleID);
			$formArray=array();
			foreach ($config as $k => $v)
			{
				array_push($formArray,array('text',$k,$k,$v));
			}
			createPostForm('?config',$formArray, 'Update Module Config');
		}
		die($h->endHeaders());
	}
}
logUserIP();