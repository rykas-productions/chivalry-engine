<?php
/*
	File:		dungeon.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Shows the players currently in the in-game dungeon, 
				and allows the player to attempt to bust them out, or 
				bail them out.
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
require("globals.php");
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'bust':
        bust();
        break;
    case 'bail':
        bail();
        break;
    default:
        home();
        break;
}
function home()
{
    global $db, $h, $api;
    $CurrentTime = time();
    //Count how many users are in the dungeon.
    $PlayerCount = $db->fetch_single($db->query("SELECT COUNT(`dungeon_user`) FROM `dungeon` WHERE `dungeon_out` > {$CurrentTime}"));
    echo "<h3>The Dungeon</h3><hr />
	<small>There's current " . number_format($PlayerCount) . " players in the dungeon.</small>
	<hr />
	<div class='cotainer'>
        <div class='row'>
            <div class='col-sm'>
                <h4>User</h4>
            </div>
            <div class='col-sm'>
                <h4>Reason</h4>
            </div>
            <div class='col-sm'>
                <h4>Time Remaining</h4>
            </div>
             <div class='col-sm'>
                <h4>Actions</h4>
            </div>
    </div>
    <hr />";
    //List users in the dungeon.
    $query = $db->query("SELECT * FROM `dungeon` WHERE `dungeon_out` > {$CurrentTime} ORDER BY `dungeon_out` DESC");
    while ($Infirmary = $db->fetch_row($query)) {
        echo "
			<div class='row'>
				<div class='col-sm'>
					<a href='profile.php?user={$Infirmary['dungeon_user']}'>
						{$api->user->getNameFromID($Infirmary['dungeon_user'])}
					</a>
				</div>
				<div class='col-sm'>
					{$Infirmary['dungeon_reason']}
				</div>
				<div class='col-sm'>
					" . timeUntilParse($Infirmary['dungeon_out']) . "
				</div>
				<div class='col-sm'>
					[<a href='?action=bail&user={$Infirmary['dungeon_user']}'>Bail Out</a>]
					[<a href='?action=bust&user={$Infirmary['dungeon_user']}'>Bust Out</a>]
				</div>
			</div>
			<hr />";
    }
    echo "</tbody></table>";
    $h->endpage();
}

function bail()
{
    global $db, $userid, $ir, $h, $api;
    if (isset($_GET['user'])) {
		$get_user = filter_input(INPUT_GET, 'user', FILTER_SANITIZE_NUMBER_INT) ?: 0;
        //Specified user is invalid or empty.
        if (empty($get_user) || $get_user == 0) {
            alert('danger', "Uh Oh!", "You must select a user you wish to bail out.", true, 'dungeon.php');
            die($h->endpage());
        }
        //Specified user is not in the dungeon.
        if (!$api->user->inDungeon($get_user)) {
            alert('danger', "Uh Oh!", "The user you wish to bail out is not in the dungeon.", true, 'dungeon.php');
            die($h->endpage());
        }
        $cost = 250 * $api->user->getInfo($get_user, 'level');
        //User does not have enough primary currency to bail this user out.
        if (!$api->user->hasCurrency($userid, 'primary', $cost)) {
            alert('danger', "Uh Oh!", "You do not have enough cash to bail this user out. You need
			    " . number_format($cost) . ".", true, 'dungeon.php');
            die($h->endpage());
        }
        //Person specified is bailed out. Take user's currency, log the action, and tell the person what happened.
        $api->user->takeCurrency($userid, 'primary', $cost);
        $api->user->addNotification($get_user, "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has
            successfully bailed you out of the dungeon.");
        alert('success', "Success!", "You have successfully bailed out {$api->user->getNameFromID($get_user)}", true, 'dungeon.php');
        $db->query("UPDATE `dungeon` SET `dungeon_out` = 0 WHERE `dungeon_user` = {$get_user}");
        die($h->endpage());
    } else {
        alert('danger', "Uh Oh!", "You must select a person to bail out.", true, 'dungeon.php');
    }
}

function bust()
{
    global $db, $userid, $ir, $h, $api;
    if (isset($_GET['user'])) {
		$get_user = filter_input(INPUT_GET, 'user', FILTER_SANITIZE_NUMBER_INT) ?: 0;
        //Person input is invalid or empty.
        if (empty($get_user) || $get_user == 0) {
            alert('danger', "Uh Oh!", "You must select a person to bust out of the dungeon.", true, 'dungeon.php');
            die($h->endpage());
        }
        //Person not in the dungeon.
        if (!$api->user->inDungeon($get_user)) {
            alert('danger', "Uh Oh!", "This person is not in the dungeon, so you cannot bust them out.", true, 'dungeon.php');
            die($h->endpage());
        }
        //User is in the dungeon.
        if (!$api->user->inDungeon($userid)) {
            alert('danger', "Uh Oh!", "You are already in the dungeon, so you cannot bust others out.", true, 'dungeon.php');
            die($h->endpage());
        }
        //User does not have 10% brave.
        if ($api->user->infoGetPercent($userid, 'brave') < 10) {
            alert('danger', "Uh Oh!", "You are not brave enough to bust someone out. You need 10 Brave, you only have
			    {$ir['brave']}.", true, 'dungeon.php');
            die($h->endpage());
        }
        //User does not have 25% will.
        if ($api->user->infoGetPercent($userid, 'will') < 25) {
            alert('danger', "Uh Oh!", "You do not have enough will to bust someone out. You need at least 25%.", true, 'dungeon.php');
            die($h->endpage());
        }
        //Update user's info.
        $api->user->infoSetPercent($userid, 'will', -25);
        $api->user->infoSetPercent($userid, 'brave', -10);
        $mult = $api->user->infoGet($get_user, 'level') * $api->user->infoGet($get_user, 'level');
        $chance = min(($ir['level'] / $mult) * 50 + 1, 95);
        //User is successful.
        if (randomNumber(1, 100) < $chance) {
            //Add notification, and tell the user.
            $api->user->addNotification($get_user, "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has
                successfully busted you out of the dungeon.");
            alert('success', "Success!", "You have successfully busted them out of the dungeon.", true, 'dungeon.php');
            $db->query("UPDATE `dungeon` SET `dungeon_out` = 0 WHERE `dungeon_user` = {$get_user}");
            die($h->endpage());
        } //User failed. Tell person and throw user in dungeon.
        else {
            $time = min($mult, 100);
            $reason = $db->escape("Caught trying to bust out {$api->user->getNameFromID($get_user)}");
            $api->user->addNotification($get_user, "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has
                failed to bust you out of the dungeon.");
            alert('danger', "Uh Oh!", "While trying to bust your friend out, you were spotted by a guard.", true, 'dungeon.php');
            $api->user->setDungeon($userid, $time, $reason);
            die($h->endpage());
        }
    } else {
        alert('danger', "Uh Oh!", "You need to specify a user you wish to bust out of the dungeon.", true, 'dungeon.php');
    }
}