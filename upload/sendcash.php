<?php
/*
	File:		sendcash.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows players to send Primary Currency to other players.
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
        $csrf = getHtmlCSRF("sendcash_{$_GET['user']}");
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