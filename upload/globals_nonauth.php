<?php
/*
	File:		globals_nonauth.php
	Created: 	9/22/2019 at 4:17PM Eastern Time
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
require('./lib/basic_debug_handler.php');
require('./functions/func_startup.php');
register_shutdown_function('shutdown');
enableErrorOutput();    //Comment out to disable raw PHP errors.
if (!checkDirectAccess('./globals_nonauth.php'))
{
	die('This file may not be accessed directly.');
}
setSession('CEV3'); //Change to whatever session name you'd like.
require('./lib/basic_error_handler.php');
set_error_handler('error_php');
//Require styling.
require('./headers_nonauth.php');
require('./config.php');
define('MONO_ON', 1);
require("./class/class_db_mysqli.php");
require("./class/class_style.php");
require('./functions/global_functions.php');
$db = new database;
$db->configure(constant("db_host"), constant("db_username"), constant("db_password"), constant("db_database"), 0);
$db->connect();
$dbConnectionID = $db->connection_id;
$h = new headers;
$styl = new style();
$h->startHeaders();