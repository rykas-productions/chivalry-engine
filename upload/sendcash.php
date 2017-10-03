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
    if (!$api->SystemUserIDtoName($_GET['user'])) {
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
        if (!$api->SystemUserIDtoName($_POST['user'])) {
            alert('danger', 'Uh Oh!', 'Please specify an existing user to send cash to.', true, 'index.php');
            die($h->endpage());
        }
        if ($userid == $_POST['user']) {
            alert('danger', 'Uh Oh!', 'You cannot send cash to yourself.', true, 'index.php');
            die($h->endpage());
        }
        if ($_POST['send'] > $ir['primary_currency']) {
            alert('danger', 'Uh Oh!', 'You cannot send more Primary Currency than you currently have.', true, 'index.php');
            die($h->endpage());
        }
        if ($api->SystemCheckUsersIPs($userid, $_POST['user'])) {
            alert('danger', 'Uh Oh!', 'You cannot send Primary Currency to anyone who has the same IP Address as you.', true, 'index.php');
            die($h->endpage());
        }
        $userformat = "<a href='profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}]";
        $user2format = "<a href='profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a> [{$_POST['user']}]";
        $cashformat = number_format($_POST['send']);
        $api->GameAddNotification($_POST['user'], "{$userformat} has sent you {$cashformat} Primary Currency.");
        $api->UserGiveCurrency($_POST['user'], 'primary', $_POST['send']);
        $api->UserTakeCurrency($userid, 'primary', $_POST['send']);
        $api->SystemLogsAdd($userid, 'sendcash', "Sent {$cashformat} Primary Currency to {$user2format}.");
        alert("success", "Success!", "You have successfully sent {$user2format} {$cashformat} Primary Currency.", true, "profile.php?user={$_GET['user']}");

    } else {
        echo "You are attempting to send Primary Currency to {$api->SystemUserIDtoName($_GET['user'])}. You have
        " . number_format($ir['primary_currency']) . " Primary Currency you can send. How much do you wish to send?";
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