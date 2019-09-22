<?php
/*
	File:		functions/func_startup.php
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
/*
	Function to check if the file currently being accessed, is 
	being directly accessed or not. (Via include or URL)
	Returns true if its being directly accessed.
	Returns false if it is not being directly accessed.
*/
function checkDirectAccess($file)
{
	if (strpos($_SERVER['PHP_SELF'], $file) !== false)
		return false;
	else
		return true;
}
function setSession($sessionName)
{
	session_name($sessionName);
	@session_start();
	if (!isset($_SESSION['started'])) 
	{
		session_regenerate_id();
		$_SESSION['started'] = true;
	}
	ob_start();
}
function enableErrorOutput()
{
	ini_set('display_startup_errors',1); 
	ini_set('display_errors',1);
	error_reporting(-1);
}
function shutdown()
{
	
}