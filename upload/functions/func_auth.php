<?php
/*
	File:		functions/func_auth.php
	Created: 	9/22/2019 at 6:45PM Eastern Time
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
//TODO: Wrap all login actions from authenticate.php into attemptAuth();
function attemptAuth()
{
	global $db;
}
/**
 * @desc Check the email to see if its valid and then return the UUID of the player associated with it.
 * @param string $email
 * @return string UUID of the player associated with the email. If zero, no account associated.
 * @internal This is an internal function.
 */
function checkValidEmail(string $email)
{
	global $db;
	$q=$db->query("SELECT `userid` FROM `users_core` WHERE `email` = '{$email}'");
	if ($db->num_rows($q) == 1)
	{
		$r=$db->fetch_single($q);
		return $r;
	}
	else
		return 0;
}
/**
 * @desc Verify if the input password is correct based on the data stored in the database.
 * @param string $rawPassword Plain text password.
 * @param string $password User's password from the database.
 * @return boolean True if the input password is valid, false if not.
 */
function checkUserPassword(string $rawPassword, string $password)
{
	//Check that the password matches or not.
    $return = (password_verify(base64_encode(hash('sha256', $rawPassword, true)), $password)) ? true : false;
    return $return;
}

/**
 * @desc Get the password for the specified UUID.
 * @param string $uuid Player UUID
 * @return string Player's password pulled from the database.
 */
function getPasswordByUUID(string $uuid)
{
    global $db;
    return $db->fetch_single($db->query("SELECT `password` FROM `users_core` WHERE `userid` = '{$uuid}'"));
}

/**
 * @desc Internal function to update account data on a log in.
 * @param string $uuid
 */
function accountLoginUpdate(string $uuid)
{
	global $db;
	$time=returnUnixTimestamp();
	$ip=getUserIP();
	$db->query("UPDATE `users_account_data` 
				SET `loginTime`='{$time}', `lastActionTime`='{$time}',
				`loginIP`='{$ip}', `lastActionIP`='{$ip}' 
				WHERE `userid` = '{$uuid}'");
	
}

/**
 * @desc Internal function to set the correct session data.
 * @param string $uuid
 */
function setActiveSession(string $uuid)
{
	session_regenerate_id();
	$_SESSION['loggedin'] = 1;
	$_SESSION['userid'] = $uuid;
	$_SESSION['last_login'] = returnUnixTimestamp();
}