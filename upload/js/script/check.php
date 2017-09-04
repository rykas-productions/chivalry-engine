<?php
/*
	File: js//script/check.php
	Created: 4/4/2017 at 7:09PM Eastern Time
	Info: PHP file for checking a user's password strength
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
if (isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD'])) {
    if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
        // Ignore a GET request
        header('HTTP/1.1 400 Bad Request');
        exit;
    }
}
require_once('../../global_func.php');
if (!is_ajax()) {
    header('HTTP/1.1 400 Bad Request');
    exit;
}
if (!isset($_POST['password'])) { // If they are trying to view this without ?password=password.
    die("Whats this document for?"); // Lawl what is this doccument for anyways?
} elseif (isset($_POST['password'])) { // ElseIf we cant to check the passwords strength.
    $PASS =
        stripslashes(
            strip_tags(
                htmlentities($_POST['password'], ENT_QUOTES,
                    'ISO-8859-1'))); // Cleans all nasty input from the password.
    $strength = 1; // Sets their default amount of points to 1.

    if ($PASS != NULL) { // If the current password is not NULL (empty).
        $uppercase = preg_match_all("/[A-Z]/", $_POST['password']);
        $lowercase = preg_match_all("/[a-z]/", $_POST['password']);
        $numbers = preg_match_all("/[0-9]/", $_POST['password']);
        $symbols = preg_match_all('/[-!@#$%^&*()_+|~=`{}\[\]:";<>?,.\/]/', $_POST['password']);
        $pwlength = strlen($_POST['password']);
        if ($uppercase == 0) {
            $upscore = 0;
        } elseif ($uppercase == 1) {
            $upscore = 1;
        } elseif ($uppercase >= 2) {
            $upscore = 2;
        }
        if ($lowercase == 0) {
            $lowscore = 0;
        } elseif ($lowercase == 1) {
            $lowscore = 1;
        } elseif ($lowercase >= 2) {
            $lowscore = 2;
        }
        if ($numbers == 0) {
            $numbscore = 0;
        } elseif ($numbers == 1) {
            $numbscore = 1;
        } elseif ($numbers >= 2) {
            $numbscore = 2;
        }
        if ($symbols == 0) {
            $symscore = 0;
        } elseif ($symbols == 1) {
            $symscore = 1;
        } elseif ($symbols >= 2) {
            $symscore = 2;
        }
        if ($pwlength < 8) {
            $pwscore = 0;
        } elseif ($pwlength >= 8 && $pwlength <= 15) {
            $pwscore = 1;
        } elseif ($pwlength >= 16) {
            $pwscore = 2;
        }
        $score = $lowscore + $upscore + $numbscore + $symscore + $pwscore;
        echo "<br />";
        if ($score <= 3 || $score == 0) {
            // If there total points are equal or less than 2.
            $overall = "<div class='progress-bar progress-bar-danger' role='progressbar' aria-valuenow='{$score}'
				aria-valuemin='0' aria-valuemax='10' style='width:{$score}0%'>
				{$score}0% Strong</div>";
        } elseif ($score <= 5) {
            // If there total points are equal or less than 5.
            $overall = "<div class='progress-bar progress-bar-warning' role='progressbar' aria-valuenow='{$score}'
				aria-valuemin='0' aria-valuemax='10' style='width:{$score}0%'>
				{$score}0% Strong</div>";
        } elseif ($score <= 8) {
            // If there total points are equal or less than 8.
            $overall = "<div class='progress-bar progress-bar-info' role='progressbar' aria-valuenow='{$score}'
				aria-valuemin='0' aria-valuemax='10' style='width:{$score}0%'>
				{$score}0% Strong</div>";
        } elseif ($score >= 8) {
            // If there total points are greator than 10.
            $overall = "<div class='progress-bar progress-bar-success' role='progressbar' aria-valuenow='{$score}'
				aria-valuemin='0' aria-valuemax='10' style='width:{$score}0%'>
				{$score}0% Strong</div>";
        } // End If.

        echo "<div class='progress'>{$overall}</div>"; // Tells them their passwords strength.

    } elseif ($PASS == NULL) { // ElseIf their password is NULL (empty).
        echo ''; // Dont display anything.
    } // End ElseIf.
} // End ElseIF.
