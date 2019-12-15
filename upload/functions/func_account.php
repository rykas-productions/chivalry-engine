<?php
/*
	File:		functions/func_account.php
	Created: 	9/29/2019 at 6:21PM Eastern Time
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
function generatePassword($plainTextPassword)
{
	return password_hash(base64_encode(hash('sha256', $plainTextPassword, true)), PASSWORD_BCRYPT);
}
function createAccount($username, $password, $email)
{
	global $db;
	$UUID=getValidUUID();
	$encodedPassword=generatePassword($password);
	$db->query("INSERT INTO `users_core` 
				(`userid`, `username`, `email`, `password`) 
				VALUES 
				('{$UUID}', '{$username}', '{$email}', '{$encodedPassword}')");
	$i = $db->insert_id();
	createUserData($i);
	createUserStats($i);
}
function checkUsableEmail($email)
{
	global $db;
	$q=$db->query("SELECT `userid` FROM `users_core` WHERE `email` = '{$email}'");
	if ($db->num_rows($q) == 0)
		return true;
	else
		return false;
}
function checkUsableUsername($username)
{
	global $db;
	$q=$db->query("SELECT `userid` FROM `users_core` WHERE `username` = '{$username}'");
	if ($db->num_rows($q) == 0)
		return true;
	else
		return false;
	
}
function checkConfirmedPassword($password, $passwordConfirm)
{
		return $password == $passwordConfirm;
}
function createUserData($userid)
{
	global $db;
	$time=returnUnixTimestamp();
	$IP=getUserIP();
	$db->query("INSERT INTO `users_account_data` VALUES ('{$userid}', '0', '{$time}', '{$time}', '', '1', '127.0.0.1', '{$IP}', '{$IP}', '0')");
}
function createUserStats($userid)
{
	global $db;
	$db->query("INSERT INTO `users_stats` 
		(`userid`, `level`, `experience`, `strength`, `agility`, 
		`guard`, `labor`, `iq`, `energy`, `maxEnergy`, `will`, 
		`maxWill`, `brave`, `maxBrave`, `hp`, `maxHP`, 
		`primaryCurrencyHeld`, `primaryCurrencyBank`) 
		VALUES ('{$userid}', '1', '0', '10', '10', '10', '10', 
		'10', '10', '10', '100', '100', '5', '5', '100', '100', 
		'100', '-1')");
}
function getUserIP()
{
	return makeSafeText($_SERVER['REMOTE_ADDR']);
}
function autoSessionCheck()
{
	if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == 0) 
	{
		headerRedirect('./login.php');
		exit;
	}
	if (isset($_SESSION['last_active']) && (returnUnixTimestamp() - $_SESSION['last_active'] > constant('sessionTimeoutSeconds'))) 
	{
		headerRedirect('./login.php');
		exit;
	}
}
function returnCurrentUserData($userid)
{
	global $db;
	return $db->fetch_row($db->query("SELECT `u`.*, `ud`.*, `us`.*
                     FROM `users_core` AS `u`
                     INNER JOIN `users_account_data` AS `ud`
                     ON `u`.`userid` = `ud`.`userid`
					 INNER JOIN `users_stats` AS `us`
                     ON `u`.`userid` = `us`.`userid`
                     WHERE `u`.`userid` = {$userid}
                     LIMIT 1"));
}
function returnUnreadMailCount()
{
	global $db, $userid;
	return $db->num_rows($db->query("SELECT `mailID` FROM `mail` WHERE `mailTo` = '{$userid}' AND `mailReadTime` = 0"));
}
function checkInfirmary($user = '')
{
	global $db, $userid;
	if (empty($user))
		$user = $userid;
	$q=$db->query("SELECT `infirmaryOut` FROM `users_infirmary` WHERE `infirmaryUserid` = '{$user}'");
	if ($db->fetch_single($q) < returnUnixTimestamp())
		return false;
	else
		return true;
}
function returnRemainingInfirmaryTime($user = '')
{
	global $db, $userid;
	if (empty($user))
		$user = $userid;
	$q=$db->query("SELECT `infirmaryOut` FROM `users_infirmary` WHERE `infirmaryUserid` = '{$user}'");
	$r=$db->fetch_single($q);
	return $r - returnUnixTimestamp();
}
function logUserIP()
{
	global $userid, $db;
	$userIP=getUserIP();
	$time=returnUnixTimestamp();
	$q=$db->query("SELECT * FROM `users_ips` WHERE `userID` = '{$userid}' AND `userIP` = '{$userIP}'");
	if ($db->num_rows($q) == 0)
		$db->query("INSERT INTO `users_ips` (`userID`, `userIP`, `userLastUsed`) VALUES ('{$userid}', '{$userIP}', '{$time}')");
	else
		$db->query("UPDATE `users_ips` SET `userLastUsed` = {$time} WHERE `userid` = '{$userid}' AND `userIP` = '{$userIP}'");
}
function createUserUUID() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        returnRandomNumber( 0, 0xffff ), returnRandomNumber( 0, 0xffff ),
        returnRandomNumber( 0, 0xffff ),
        returnRandomNumber( 0, 0x0fff ) | 0x4000,
        returnRandomNumber( 0, 0x3fff ) | 0x8000,
        returnRandomNumber( 0, 0xffff ), returnRandomNumber( 0, 0xffff ), returnRandomNumber( 0, 0xffff )
    );
}
function getValidUUID()
{
	global $db;
	$continueLoop=true;
	while ($continueLoop)
	{
		$UUID=createUserUUID();
		$q=$db->query("SELECT `userid` FROM `users_core` WHERE `userid` = '{$UUID}'");
		if ($db->num_rows($q) == 0)
		{
			$continueLoop=false;
			return $UUID;
		}
	}
}