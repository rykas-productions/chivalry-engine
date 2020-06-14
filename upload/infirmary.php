<?php
/*
	File:		infirmary.php
	Created: 	4/5/2016 at 12:11AM Eastern Time
	Info: 		Lists the players currently in the infirmary, and allows
				them to heal those players out using Chivalry Tokens.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
//Bind the GET action if possible. If not set, set to nothing. Nothing will redirect to the main listing.
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'heal':
        heal();
        break;
    default:
        home();
        break;
}
function home()
{
    global $db, $api;
    //Bind current unix timestamp to a variable.
    $CurrentTime = time();
    //Select player count of those in the infirmary.
    $PlayerCount = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`infirmary_user`) FROM `infirmary` WHERE `infirmary_out` > {$CurrentTime}"));
    //List them out now.
    echo "<h3><i class='game-icon game-icon-hospital-cross'></i> The Infirmary</h3><hr />
	<small>There's currently " . number_format($PlayerCount) . " users in the infirmary.</small>
	<hr />";
	
	echo "<div class='row'>
			<div class='col-md-4'>
				<h3>Player</h3>
			</div>
			<div class='col-md-4'>
				<h3>Infirmary Status</h3>
			</div>
			<div class='col-md-4'>
				<h3>Actions</h3>
			</div>
		</div>
		<hr />";
    $query = $db->query("/*qc=on*/SELECT * FROM `infirmary` WHERE `infirmary_out` > {$CurrentTime} ORDER BY `infirmary_out` DESC");
    while ($Infirmary = $db->fetch_row($query)) 
	{
		$displaypic = "<img src='" . parseImage(parseDisplayPic($Infirmary['infirmary_user'])) . "' height='75' alt='' title=''>";
		echo "<div class='row'>
			<div class='col-md-4'>
				<div class='row'>
					<div class='col-md'>
						{$displaypic}
					</div>
					<div class='col-md'>
						<a href='profile.php?user={$Infirmary['infirmary_user']}'> " . parseUsername($Infirmary['infirmary_user']) . " </a> 
						[{$Infirmary['infirmary_user']}]
					</div>
				</div>
			</div>
			<div class='col-md-4'>
				Reason: <i>{$Infirmary['infirmary_reason']}</i><br />
				Release: " . TimeUntil_Parse($Infirmary['infirmary_out']) . "
			</div>
			<div class='col-md-4'>
				<a class='btn btn-primary' href='?action=heal&user={$Infirmary['infirmary_user']}'>Heal {$api->SystemUserIDtoName($Infirmary['infirmary_user'])}</a>
			</div>
		</div>
		<hr />";
    }
}

function heal()
{
    global $api, $h, $userid, $ir;
    //User is specified in the GET
    if (isset($_GET['user'])) {
        //Sanitize the user ID input.
        $_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : 0;
        //GET is empty/truncated after sanitation
        if (empty($_GET['user']) || $_GET['user'] == 0) {
            alert('danger', "Uh Oh!", "You are attempting to heal an invalid or non-existent user.", true, 'infirmary.php');
            die($h->endpage());
        }
        //User to heal is not in the infirmary.
        if ($api->UserStatus($_GET['user'], 'infirmary') == false) {
            alert('danger', "Uh Oh!", "You are attempting to heal out a player who's not even in the infirmary.", true, 'infirmary.php');
            die($h->endpage());
        }
        //Make sure the user is specifying how many times to heal.
        if (isset($_GET['times'])) {
            //Sanitize how many times the user wishes to heal.
            $_GET['times'] = (isset($_GET['times']) && is_numeric($_GET['times'])) ? abs($_GET['times']) : 0;
            //Healing times is truncated/empty after sanitation.
            if (empty($_GET['times'])) {
                alert('danger', "Uh Oh!", "You are attempting to heal an invalid or non-existent user.", true, 'infirmary.php');
                die($h->endpage());
            }
            //Cost = 25 Secondary Currenxy x Times to Heal
            //Times = 30 Minutes x Times to Heal
            $cost = 5 * $_GET['times'];
            $time = 30 * $_GET['times'];
            //User does not have enough Chivalry Tokens to heal that many times.
            if ($ir['secondary_currency'] < $cost) {
                alert('danger', "Uh Oh!", "You do not have enough Chivalry Tokens to heal {$_GET['times']} sets.", true, 'infirmary.php');
                die($h->endpage());
            } else {
                //Healed successfully!
                $api->UserStatusSet($_GET['user'], 'infirmary', $time * -1, 'Not read');
                //Take current user's Chivalry Tokens
                $api->UserTakeCurrency($userid, 'secondary', $cost);
				addToEconomyLog('Infirmary', 'token', ($cost)*-1);
                //Add a friendly note.
                $api->GameAddNotification($_GET['user'], "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has healed you {$_GET['times']} times.",  "game-icon game-icon-healing", "green");
                //Log it!
                $api->SystemLogsAdd($userid, 'heal', "Healed {$api->SystemUserIDtoName($_GET['user'])} {$_GET['times']} times.");
                alert('success', "Success!", "You have healed {$api->SystemUserIDtoName($_GET['user'])} {$_GET['times']}
                times, costing you {$cost} Chivalry Tokens.", true, 'index.php');
            }
        } else {
            echo "How many times do you wish to heal {$api->SystemUserIDtoName($_GET['user'])}?<br />
            1 Set = 30 minutes<br />
            1 Set = 5 Chivalry Tokens<br />
            <form>
                <input type='hidden' name='user' value='{$_GET['user']}'>
                <input type='hidden' name='action' value='heal'>
                <input type='number' required='1' name='times' class='form-control'>
                <input type='submit' class='btn btn-primary' value='Heal'>
            </form>";
        }
    } else {
        alert('danger', "Uh Oh!", "Please specify a user you wish to heal out.", true, 'infirmary.php');
    }
}

$h->endpage();