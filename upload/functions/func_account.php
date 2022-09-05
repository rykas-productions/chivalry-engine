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

/**
 * Generates a secure password for database insertion.
 * @param string $plainTextPassword
 * @return string
 */
function generatePassword(string $plainTextPassword)
{
	return password_hash(base64_encode(hash('sha256', $plainTextPassword, true)), PASSWORD_ARGON2ID);
}

function updateAccountPassword(string $uuid, string $rawPW)
{
    global $db;
    $encryptPW = generatePassword($rawPW);
    $db->query("UPDATE `users_core` SET `password` = '{$encryptPW}' WHERE `userid` = '{$uuid}'");
}

/**
 * @desc            Create a user easily.
 * @param string    $username Name of the new user
 * @param string    $password Password for the new user
 * @param string    $email Email for the new user
 * @internal        Chivalry Engine Internal Function.
 */
function createAccount(string $username, string $password, string $email)
{
	global $db;
	$UUID=getValidUUID();
	$encodedPassword=generatePassword($password);
	$db->query("INSERT INTO `users_core` 
				(`userid`, `username`, `email`, `password`) 
				VALUES 
				('{$UUID}', '{$username}', '{$email}', '{$encodedPassword}')");
	createUserData($UUID);
	createUserStats($UUID);
}

/**
 * @desc            Check if input email address is not already in use.
 * @param string    $email Email to test
 * @return boolean  Email in use
 */
function checkUsableEmail(string $email)
{
	global $db;
	$q=$db->query("SELECT `userid` FROM `users_core` WHERE `email` = '{$email}'");
	if ($db->num_rows($q) == 0)
		return true;
	else
		return false;
}

/**
 * @desc            Check if input username is not already in use.
 * @param string    $username Username to test
 * @return boolean  Username is in use
 */
function checkUsableUsername(string $username)
{
	global $db;
	$q=$db->query("SELECT `userid` FROM `users_core` WHERE `username` = '{$username}'");
	if ($db->num_rows($q) == 0)
		return true;
	else
		return false;
	
}

/**
 * @desc            Check that $password and $passwordConfirm are equal.
 * @param string    $password First password
 * @param string    $passwordConfirm Password confirmation
 * @return boolean  Passwords are the same
 */
function checkConfirmedPassword(string $password, string $passwordConfirm)
{
		return $password == $passwordConfirm;
}

/** 
 * @desc            Create's User Account Data.
 * @internal        Chivalry Engine internal function and you should never need to use it.
 * @param string    $uuid User UUID to create basic data for
 */
function createUserData(string $uuid)
{
	global $db;
	$time=returnUnixTimestamp();
	$IP=getUserIP();
	$db->query("INSERT INTO `users_account_data` VALUES ('{$uuid}', '0', '{$time}', '{$time}', '', '1', '127.0.0.1', '{$IP}', '{$IP}', '0')");
}

/**
 * @desc            Creates the User Stat data.
 * @internal        Chivalry Engine internal function and you should never need to use it.
 * @param string    $uuid User UUID to create basic stats for
 */
function createUserStats(string $uuid)
{
	global $db;
	$db->query("INSERT INTO `users_stats` 
		(`userid`, `level`, `experience`, `strength`, `agility`, 
		`guard`, `labor`, `iq`, `energy`, `maxEnergy`, `will`, 
		`maxWill`, `brave`, `maxBrave`, `hp`, `maxHP`, 
		`primaryCurrencyHeld`, `primaryCurrencyBank`) 
		VALUES ('{$uuid}', '1', '0', '10', '10', '10', '10', 
		'10', '10', '10', '100', '100', '5', '5', '100', '100', 
		'100', '-1')");
}

/**
 * @desc Get the current user's IP address.
 * @return string IP Address
 */
function getUserIP()
{
	return stripAll($_SERVER['REMOTE_ADDR']);
}

/**
 * @desc            Check if the user is authenticated or not.
 * @internal        Chivalry Engine internal function and you should never need to use it.
 */
function autoSessionCheck()
{
	if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == 0) 
	{
	    session_regenerate_id(true);
	    session_unset();
	    session_destroy();
		headerRedirect('./login.php');
		exit;
	}
	if (isset($_SESSION['last_active']) && ((returnUnixTimestamp() - $_SESSION['last_active']) > sessionTimeoutSeconds)) 
	{
	    session_regenerate_id(true);
	    session_unset();
	    session_destroy();
		headerRedirect('./login.php');
		exit;
	}
}

/**
 * @desc            Returns all database stored information for the current user.
 * @internal        Chivalry Engine internal function and you should never need to use it.
 * @param string    $uuid
 * @return $ir      User Data
 */
function returnCurrentUserData(string $uuid)
{
	global $db;
	return $db->fetch_row($db->query("SELECT `u`.*, `ud`.*, `us`.*
                     FROM `users_core` AS `u`
                     INNER JOIN `users_account_data` AS `ud`
                     ON `u`.`userid` = `ud`.`userid`
					 INNER JOIN `users_stats` AS `us`
                     ON `u`.`userid` = `us`.`userid`
                     WHERE `u`.`userid` = '{$uuid}'
                     LIMIT 1"));
}

/**
 * @desc Returns unread mails for UUID $uuid
 * @param string $uuid
 * @return int Unread Mail
 */
function returnUnreadMailCount(string $uuid = '')
{
	global $db, $userid;
	if (empty($uuid))
	    $uuid = $userid;
	return $db->num_rows($db->query("SELECT `mailID` FROM `mail` WHERE `mailTo` = '{$uuid}' AND `mailReadTime` = 0"));
}

/**
 * @desc            Check if $uuid is in the infirmary.
 * @param string    $uuid User UUID
 * @return boolean  User is in the infirmary
 */
 function checkInfirmary(string $uuid = '') 
 {
	global $db, $userid;
	if (empty($uuid))
		$uuid = $userid;
	$q=$db->query("SELECT `infirmaryOut` FROM `users_infirmary` WHERE `infirmaryUserid` = '{$uuid}'");
	if ($db->fetch_single($q) < returnUnixTimestamp())
		return false;
	else
		return true;
}

/**
 * @desc            Returns how many seconds $uuid has in the infirmary.
 *                  If $uuid is undefined, will default to current user's UUID.
 * @param string    $uuid User's UUID
 * @return number   Time remaining in seconds
 */
function returnRemainingInfirmaryTime(string $uuid = '')
{
	global $db, $userid;
	if (empty($uuid))
		$uuid = $userid;
	$q=$db->query("SELECT `infirmaryOut` FROM `users_infirmary` WHERE `infirmaryUserid` = '{$uuid}'");
	$r=$db->fetch_single($q);
	return $r - returnUnixTimestamp();
}

/**
 * @desc            Logs the current player's current IP address. This 
 *                  function will update its current use time.
 * @internal        Chivalry Engine internal function. Do not call directly.
 */
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

/**
 * @desc            Create a UUID
 * @internal        This function is internal. Do not edit.
 * @return string   Randomly generated UUID
 */
function createUserUUID() 
{
    return sprintf( '%04x%04x%04x',
        returnRandomNumber( 0, 0xffff ), returnRandomNumber( 0, 0xffff ),
        returnRandomNumber( 0, 0xffff ),
        returnRandomNumber( 0, 0x0fff ) | 0x4000,
        returnRandomNumber( 0, 0x3fff ) | 0x8000,
        returnRandomNumber( 0, 0xffff ), returnRandomNumber( 0, 0xffff ), returnRandomNumber( 0, 0xffff )
    );
}

/**
 * @desc Attempt to get a valid, unused User UUID.
 * @internal Chivalry Engine Internal function.
 * @return string Valid User UUID
 */
//@TODO: Make this function work for all tables.
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