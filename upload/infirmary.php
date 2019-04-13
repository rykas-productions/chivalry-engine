<?php
/*
	File:		infirmary.php
	Created: 	4/5/2016 at 12:11AM Eastern Time
	Info: 		Lists the players currently in the infirmary, and allows
				them to heal those players out using secondary currency.
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
    $PlayerCount = $db->fetch_single($db->query("SELECT COUNT(`infirmary_user`) FROM `infirmary` WHERE `infirmary_out` > {$CurrentTime}"));
    //List them out now.
    echo "<h3>The Infirmary</h3><hr />
	<small>There's currently " . number_format($PlayerCount) . " users in the infirmary.</small>
	<hr />
	<table class='table table-hover table-bordered'>
		<thead>
			<tr>
				<th>
					User
				</th>
				<th>
					Reason
				</th>
				<th>
					Check-out
				</th>
				<th>
					Actions
				</th>
			</tr>
		</thead>
		<tbody>";
    $query = $db->query("SELECT * FROM `infirmary` WHERE `infirmary_out` > {$CurrentTime} ORDER BY `infirmary_out` DESC");
    while ($Infirmary = $db->fetch_row($query)) {
        echo "
			<tr>
				<td>
					<a href='profile.php?user={$Infirmary['infirmary_user']}'>
						{$api->user->getNamefromID($Infirmary['infirmary_user'])}
					</a> [{$Infirmary['infirmary_user']}]
				</td>
				<td>
					{$Infirmary['infirmary_reason']}
				</td>
				<td>
					" . timeUntilParse($Infirmary['infirmary_out']) . "
				</td>
				<td>
					[<a href='?action=heal&user={$Infirmary['infirmary_user']}'>Heal User</a>]
				</td>
			</tr>";
    }
    echo "</tbody></table>";
}

function heal()
{
    global $api, $h, $userid, $ir;
    //User is specified in the GET
    if (isset($_GET['user'])) {
        //Sanitize the user ID input.
        $user = filter_input(INPUT_GET, 'user', FILTER_SANITIZE_NUMBER_INT) ?: 0;
        //GET is empty/truncated after sanitation
        if (empty($user) || $user == 0) {
            alert('danger', "Uh Oh!", "You are attempting to heal an invalid or non-existent user.", true, 'infirmary.php');
            die($h->endpage());
        }
        //User to heal is not in the infirmary.
        if (!$api->user->inInfirmary($user)) {
            alert('danger', "Uh Oh!", "You are attempting to heal out a player who's not even in the infirmary.", true, 'infirmary.php');
            die($h->endpage());
        }
        //Make sure the user is specifying how many times to heal.
        if (isset($_GET['times'])) {
            //Sanitize how many times the user wishes to heal.
            $times = filter_input(INPUT_GET, 'times', FILTER_SANITIZE_NUMBER_INT) ?: 0;
            //Healing times is truncated/empty after sanitation.
            if (empty($times)) {
                alert('danger', "Uh Oh!", "You are attempting to heal an invalid or non-existent user.", true, 'infirmary.php');
                die($h->endpage());
            }
            //Cost = 25 Secondary Currenxy x Times to Heal
            //Times = 30 Minutes x Times to Heal
            $cost = 25 * $times;
            $time = 30 * $times;
            //User does not have enough secondary currency to heal that many times.
            if ($ir['secondary_currency'] < $cost) {
                alert('danger', "Uh Oh!", "You do not have enough {$_CONFIG['secondary_currency']} to heal {$_GET['times']} sets.", true, 'infirmary.php');
                die($h->endpage());
            } else {
                //Healed successfully!
                $api->user->setInfirmary($user, $time * -1, 'Not read');
                //Take current user's secondary currency
                $api->user->takeCurrency($userid, 'secondary', $cost);
                //Add a friendly note.
                $api->user->addNotification($user, "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has healed you {$times} times.");
                //Log it!
                $api->game->addLog($userid, 'heal', "Healed {$api->user->getNamefromID($user)} {$_GET['times']} times.");
                alert('success', "Success!", "You have healed {$api->user->getNamefromID($user)} {$times}
                times, costing you {$cost} {$_CONFIG['secondary_currency']}.", true, 'index.php');
            }
        } else {
            echo "How many times do you wish to heal {$api->user->getNamefromID($user)}?<br />
            1 Set = 30 minutes<br />
            1 Set = 25 {$_CONFIG['secondary_currency']}<br />
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