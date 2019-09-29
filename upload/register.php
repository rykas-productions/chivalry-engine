<?php
/*
	File:		register.php
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
require('globals_nonauth.php');
if (isset($_POST['email']))
{
	$safeUsername = makeSafeText($_POST['username']);
	$safeEmail = makeSafeText($_POST['email']);
	$safePassword = makeSafeText($_POST['password']);
	$safeConfirmPassword = makeSafeText($_POST['cpassword']);
	if (empty($safeUsername))
	{
		die('Username not input or invalid.');
	}
	if (empty($safeEmail))
	{
		die('Email not input or invalid.');
	}
	if (empty($safePassword))
	{
		die('Password not input or invalid.');
	}
	if (empty($safeConfirmPassword))
	{
		die('Password confirmation not input or invalid.');
	}
	if (!checkUsableUsername($safeUsername))
	{
		die('Username already in use.');
	}
	if (!checkUsableEmail($safeEmail))
	{
		die('Username already in use.');
	}
	if (!checkConfirmedPassword($safePassword, $safeConfirmPassword))
	{
		die('Password and password confirmation do not match.');
	}
	createAccount($safeUsername, $safePassword, $safeEmail);
	die('Account created successfully.');
}
else
{
	echo "<h3>{$set['gameName']} Registration</h3><hr />";
	createPostForm('register.php',array(
										array('text','username','Username'),
										array('email','email','Email Address'), 
										array('password','password','Password'), 
										array('password','cpassword','Confirm Password')
										), 'Create Account');
}
$h->endHeaders();