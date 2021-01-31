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
    $query = $db->query("/*qc=on*/SELECT * FROM `infirmary` WHERE `infirmary_out` > {$CurrentTime} ORDER BY `infirmary_out` DESC");
    while ($Infirmary = $db->fetch_row($query)) 
	{
		$displaypic = "<img src='" . parseImage(parseDisplayPic($Infirmary['infirmary_user'])) . "' height='75' alt='' title=''>";
		echo "
		<div class='card'>
			<div class='card-body'>
				<div class='row'>
					<div class='col-6 col-sm-4 col-md-3 col-lg-2'>
						{$displaypic}
					</div>
					<div class='col-6 col-sm-4 col-md-3'>
						<a href='profile.php?user={$Infirmary['infirmary_user']}'> " . parseUsername($Infirmary['infirmary_user']) . " </a> 
						[{$Infirmary['infirmary_user']}]
					</div>
					<div class='col-12 col-md-6 col-lg'>
						<div class='row'>
							<div class='col-12 col-lg-6'>
								Reason: <i>{$Infirmary['infirmary_reason']}</i><br />
								Release: " . TimeUntil_Parse($Infirmary['infirmary_out']) . "
							</div>
							<div class='col col-lg-6'>
								<a class='btn btn-primary btn-block' href='?action=heal&user={$Infirmary['infirmary_user']}'>Heal {$api->SystemUserIDtoName($Infirmary['infirmary_user'])}</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>";
    }
}

function heal()
{
    global $api, $h, $userid, $ir, $db;
    $_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : 0;
	if (empty($_GET['user'])) 
	{
		alert('danger', "Uh Oh!", "You are attempting to heal an invalid or non-existent user.", true, 'infirmary.php');
		die($h->endpage());
	}
	if (!$api->UserStatus($_GET['user'], 'infirmary')) 
	{
		alert('danger', "Uh Oh!", "You are attempting to heal out a player who's not even in the infirmary.", true, 'infirmary.php');
		die($h->endpage());
	}
	$outtime = $db->fetch_single($db->query("/*qc=on*/SELECT `infirmary_out` FROM `infirmary` WHERE `infirmary_user` = {$_GET['user']}"));
	$time_difference = $outtime - time();
	$mins = $time_difference / 60;
	$sets = ceil($mins / 30);
	$cost = $sets * 5;
	if (!$api->UserHasCurrency($userid, 'secondary', $cost))
	{
		alert('danger', "Uh Oh!", "You need " . number_format($cost) . " Chivlary Tokens to heal out {$api->SystemUserIDtoName($_GET['user'])}.", true, 'infirmary.php');
		die($h->endpage());
    }
	$api->UserStatusSet($_GET['user'], 'infirmary', (($sets * 30) * -1), 'Not read');
	addToEconomyLog('Infirmary', 'token', ($cost)*-1);
	$api->UserTakeCurrency($userid, 'secondary', $cost);
	if ($_GET['user'] != $userid)
	   $api->GameAddNotification($_GET['user'], "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has spent " . number_format($cost) . " Chivlary Tokens to discharge you from the infirmary.");
    $api->SystemLogsAdd($userid, 'heal', "Spent " . number_format($cost) . " Chivalry Tokens to heal {$api->SystemUserIDtoName($_GET['user'])}.");
	alert('success', "Success!", "You have spent " . number_format($cost) . " Chivalry Tokens to heal out {$api->SystemUserIDtoName($_GET['user'])} of the infirmary.", true, 'index.php');
}

function getTokensNeeded()
{
	
}
$h->endpage();