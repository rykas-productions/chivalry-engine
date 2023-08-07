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
/**
 * @desc            Internal function to test if file is being directly accessed. 
 *                  Returns true if being accessed by URL, false if not.
 * @param string    $file Name of file/dir to test if being directly accessed.
 * @return boolean
 */
function checkDirectAccess($file)
{
	if (strpos($_SERVER['PHP_SELF'], $file) !== false)
		return false;
	else
		return true;
}
/**
 * @desc            Internal function to set the current user's session and optionally
 *                  a name for the session.
 * @param string    $sessionName Name to set for the session
 */
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
/**
 * @desc            Internal function to be called whenever you wish to display PHP/Engine
 *                  errors to the client.
 */
function enableErrorOutput()
{
	ini_set('display_startup_errors',1); 
	ini_set('display_errors',1);
	error_reporting(-1);
	displayBacktrace();
}
function shutdown()
{
	echo "<br /><p class='text-muted'><i>We also have a shutdown function that's always ran, even on failure, inside of `functions\\func_startup.php`</i></p>";
}