<?php
/*
	File:		logout.php
	Created: 	4/5/2016 at 12:18AM Eastern Time
	Info: 		Terminates the player's session and redirects them
				to the login.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
session_name('CENGINE');
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