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
function attemptAuth()
{
	global $db;
}
function checkValidEmail($email)
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
function checkUserPassword($rawPassword, $password)
{
	//Check that the password matches or not.
    $return = (password_verify(base64_encode(hash('sha256', $rawPassword, true)), $password)) ? true : false;
    return $return;
}
function getPasswordByUserID($userid)
{
	global $db;
	return $db->fetch_single($db->query("SELECT `password` FROM `users_core` WHERE `userid` = '{$userid}'"));
}
function accountLoginUpdate($userid)
{
	global $db;
	$time=returnUnixTimestamp();
	$ip=getUserIP();
	$db->query("UPDATE `users_account_data` 
				SET `loginTime`='{$time}', `lastActionTime`='{$time}',
				`loginIP`='{$ip}', `lastActionIP`='{$ip}' 
				WHERE `userid` = '{$userid}'");
	
}
function setActiveSession($userid)
{
	session_regenerate_id();
	$_SESSION['loggedin'] = 1;
	$_SESSION['userid'] = $userid;
	$_SESSION['last_login'] = returnUnixTimestamp();
}