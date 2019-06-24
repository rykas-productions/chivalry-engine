<?php
/*
	File:		logout.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Safely kills the player's session, then redirects
				the player to the login page.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
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
session_name('CEV2');
session_start();
if (!isset($_SESSION['started'])) {
    session_regenerate_id();
    $_SESSION['started'] = true;
}
require_once('globals_nonauth.php');
if (isset($_SESSION['userid'])) {
    $sessid = abs($_SESSION['userid']);
    if (isset($_SESSION['attacking']) && $_SESSION['attacking'] > 0) {
        $hosptime = randomNumber(10, 50);
        $api->user->setInfirmary($userid, $hosptime, "Ran from a fight");
        alert("warning", "Uh Oh!", "For leaving your previous fight, you were placed in the Infirmary for {$hosptime}
            minutes, and lost all your experience.", false);
        $db->query("UPDATE `users` SET `xp` = 0, `attacking` = 0 WHERE `userid` = $userid");
        $_SESSION['attacking'] = 0;
        session_regenerate_id(true);
        session_unset();
        session_destroy();
        $api->game->addLog($sessid, 'login', "Successfully logged out and lost experience.");
        header("Refresh:3; url=login.php");
        exit;
    }
}
$api->game->addLog($sessid, 'login', "Successfully logged out.");
session_regenerate_id(true);
session_unset();
session_destroy();
$login_url = 'login.php';
header("Location: {$login_url}");