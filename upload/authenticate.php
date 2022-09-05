<?php
/*
	File:		authenticate.php
	Created: 	9/22/2019 at 6:55PM Eastern Time
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
$safeEmail = makeSafeText($_POST['email']);
$safePassword = makeSafeText($_POST['password']);
$userid = checkValidEmail($safeEmail);
var_dump($userid);
if (empty($userid))
{
	dangerRedirect('Invalid account creditials. (No account)', 'login.php', 'Back');
	die($h->endHeaders());
}
$accountPassword=getPasswordByUUID($userid);
if (!(checkUserPassword($safePassword, $accountPassword)))
{
	dangerRedirect('Invalid account creditials. (Invalid PW)', 'login.php', 'Back');
	die($h->endHeaders());
}
setActiveSession($userid);
accountLoginUpdate($userid);
updateAccountPassword($userid, $safePassword);
successRedirect("You've successfully logged in. You will be redirected shortly. Click the following link if you are not redirected automatically.", 'loggedin.php', "Force Redirect");
headerRedirect('./loggedin.php');