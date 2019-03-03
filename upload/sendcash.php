<?php
/*
	File:		sendcash.php
	Created: 	10/03/2017 at 11:57AM Eastern Time
	Info: 		Send cash to your friends! Wowza!
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
if (isset($_GET['user'])) {
    $_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : 0;
    if (empty($_GET['user'])) {
        alert('danger', 'Uh Oh!', 'Please specify a valid user to send cash to.', true, 'index.php');
        die($h->endpage());
    }
    if (!$api->user->getNamefromID($_GET['user'])) {
        alert('danger', 'Uh Oh!', 'Please specify an existing user to send cash to.', true, 'index.php');
        die($h->endpage());
    }
    if ($userid == $_GET['user']) {
        alert('danger', 'Uh Oh!', 'You cannot send cash to yourself.', true, 'index.php');
        die($h->endpage());
    }
    if (isset($_POST['user'])) {
        $_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;
        $_POST['send'] = (isset($_POST['send']) && is_numeric($_POST['send'])) ? abs($_POST['send']) : 0;
        if (empty($_POST['user'])) {
            alert('danger', 'Uh Oh!', 'Please specify a valid user to send cash to.', true, 'index.php');
            die($h->endpage());
        }
        if (!$api->user->getNamefromID($_POST['user'])) {
            alert('danger', 'Uh Oh!', 'Please specify an existing user to send cash to.', true, 'index.php');
            die($h->endpage());
        }
        if ($userid == $_POST['user']) {
            alert('danger', 'Uh Oh!', 'You cannot send cash to yourself.', true, 'index.php');
            die($h->endpage());
        }
        if ($_POST['send'] > $ir['primary_currency']) {
            alert('danger', 'Uh Oh!', 'You cannot send more {$_CONFIG['primary_currency']} than you currently have.', true, 'index.php');
            die($h->endpage());
        }
        if ($api->user->checkIP($userid, $_POST['user'])) {
            alert('danger', 'Uh Oh!', 'You cannot send {$_CONFIG['primary_currency']} to anyone who has the same IP Address as you.', true, 'index.php');
            die($h->endpage());
        }
        $userformat = "<a href='profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}]";
        $user2format = "<a href='profile.php?user={$_POST['user']}'>{$api->user->getNamefromID($_POST['user'])}</a> [{$_POST['user']}]";
        $cashformat = number_format($_POST['send']);
        $api->user->addNotification($_POST['user'], "{$userformat} has sent you {$cashformat} {$_CONFIG['primary_currency']}.");
        $api->user->giveCurrency($_POST['user'], 'primary', $_POST['send']);
        $api->user->takeCurrency($userid, 'primary', $_POST['send']);
        $api->game->addLog($userid, 'sendcash', "Sent {$cashformat} {$_CONFIG['primary_currency']} to {$user2format}.");
        alert("success", "Success!", "You have successfully sent {$user2format} {$cashformat} {$_CONFIG['primary_currency']}.", true, "profile.php?user={$_GET['user']}");
        $h->endpage();
    } else {
        echo "You are attempting to send {$_CONFIG['primary_currency']} to {$api->user->getNamefromID($_GET['user'])}. You have
        " . number_format($ir['primary_currency']) . " {$_CONFIG['primary_currency']} you can send. How much do you wish to send?";
        $csrf = request_csrf_html("sendcash_{$_GET['user']}");
        echo "<form method='post' action='?user={$_GET['user']}'>
            <input type='hidden' value='{$_GET['user']}' name='user'>
            <input type='number' min='1' max='{$ir['primary_currency']}' class='form-control' required='1' name='send'>
            {$csrf}
            <br />
            <input type='submit' value='Send Cash' class='btn btn-primary'>
        </form>";
        $h->endpage();
    }
} else {
    alert('danger', "Uh Oh!", "Please specify the user you wish to send cash to.", true, 'index.php');
    die($h->endpage());
}