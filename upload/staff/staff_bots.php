<?php
/*
	File: 		staff/staff_bots.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows staff to do actions relating to the in-game NPC Battle Tent.
	Author: 	TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine/
	
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
require('sglobals.php');
echo "<h3>Staff Bot Tent</h3>";
if ($ir['user_level'] != "Admin") {
    alert('danger', "Uh Oh!", "You do not have permission to be here.", true, 'index.php');
    die($h->endpage());
}
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "addbot":
        addbot();
        break;
    case "delbot":
        delbot();
        break;
    default:
        alert('danger', "Uh Oh!", "Please select a valid action to perform.", true, 'index.php');
        die($h->endpage());
        break;
}
function addbot()
{
    global $db, $api, $h, $userid;
    if (isset($_POST['user'])) {
        if (!isset($_POST['verf']) || !checkCSRF('staff_bot_add', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Try again, but be quicker!");
            die($h->endpage());
        } else {
            $item = (isset($_POST['item']) && is_numeric($_POST['item'])) ? abs(intval($_POST['item'])) : 0;
            $user = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs(intval($_POST['user'])) : 0;
            $cooldown = (isset($_POST['cooldown']) && is_numeric($_POST['cooldown'])) ? abs(intval($_POST['cooldown'])) : 1;
            if (empty($item) || empty($user) || empty($cooldown)) {
                alert('danger', "Uh Oh!", "Please fill out the form completely.");
                die($h->endpage());
            }
            $q = $db->query("SELECT `botid` FROM `botlist` WHERE `botuser` = {$user}");
            if ($db->num_rows($q) > 0) {
                $db->free_result($q);
                alert('danger', "Uh Oh!", "You cannot have the same bot listed twice.");
                die($h->endpage());
            }
            $db->free_result($q);
            $q = $db->fetch_single($db->query("SELECT `user_level` FROM `users` WHERE `userid` = {$user}"));
            if (!($q == 'NPC')) {
                alert('danger', "Uh Oh!", "You cannot add a non-NPC to the NPC Bot Tent.");
                die($h->endpage());
            }
            if (!$api->game->getItemNameFromID($item)) {
                alert('danger', "Uh Oh!", "The item you've chosen for this bot to drop does not exist.");
                die($h->endpage());
            }
            $db->query("INSERT INTO `botlist` (`botuser`, `botitem`, `botcooldown`) VALUES ('{$user}', '{$item}', '{$cooldown}')");
            alert('success', "Success!", "You have successfully added NPC User ID {$user} to the Bot Tent.", true, 'index.php');
            $api->game->addLog($userid, 'staff', "Added User ID {$user} to the bot list.");
            die($h->endpage());
        }
    } else {
        $csrf = getHtmlCSRF('staff_bot_add');
        echo "
		<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
                        Use this form to add bots to the game that drop items when mugged.
					</th>
				</tr>
				<tr>
					<th>
						Bot
					</th>
					<td>
						" . dropdownNPC('user') . "
					</td>
				</tr>
				<tr>
					<th>
						Item Drop
					</th>
					<td>
						" . dropdownItem('item') . "
					</td>
				</tr>
				<tr>
					<th>
						Cooldown Time (Seconds)
					</th>
					<td>
						<input required='1' type='number' name='cooldown' placeholder='3600=1 hr; 86400=1 day' class='form-control' min='1'>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' value='Add Bot' class='btn btn-primary'>
					</td>
				</tr>
			</table>
			{$csrf}
		</form>";
    }
}

function delbot()
{
    global $db, $userid, $api, $h;
    if (isset($_POST['bot'])) {
        if (!isset($_POST['verf']) || !checkCSRF('staff_bot_del', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Try again, but be quicker!");
            die($h->endpage());
        } else {
            $bot = (isset($_POST['bot']) && is_numeric($_POST['bot'])) ? abs(intval($_POST['bot'])) : 0;
            if (empty($bot)) {
                alert('danger', "Uh Oh!", "Please select a bot to delete.");
                die($h->endpage());
            }
            $q = $db->query("SELECT `botid` FROM `botlist` WHERE `botuser` = {$bot}");
            if ($db->num_rows($q) == 0) {
                $db->free_result($q);
                alert('danger', "Uh Oh!", "The NPC you've selected is not on the NPC Bot Tent, thus, cannot be removed.");
                die($h->endpage());
            }
            $db->query("DELETE FROM `botlist` WHERE `botuser` = {$bot}");
            alert('success', "Success!", "You have removed NPC ID {$bot} from the Bot Tent.", true, 'index.php');
            $api->game->addLog($userid, 'staff', "Deleted User ID {$bot} from the bot list.");
        }
    } else {
        $csrf = getHtmlCSRF('staff_bot_del');
        echo "
		<form action='?action=delbot' method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Select a bot to remove from the Bot Tent.
				</th>
			</tr>
			<tr>
				<th>
					Bot
				</th>
				<td>
					" . dropdownNPCBot() . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Delete Bot'>
				</td>
			</tr>
		</table>
		{$csrf}
		</form>";
    }
}

$h->endpage();