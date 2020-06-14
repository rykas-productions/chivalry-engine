<?php
/*
	File:		dungeon.php
	Created: 	4/4/2016 at 11:58PM Eastern Time
	Info: 		Lists players currently in the dungeon, and allows players
				to bust or bail them out.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
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
    $PlayerCount = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`dungeon_user`) FROM `dungeon` WHERE `dungeon_out` > {$CurrentTime}"));
    echo "<h3><i class='game-icon game-icon-cage'></i> The Dungeon</h3><hr />
	<small>There's current " . number_format($PlayerCount) . " players in the dungeon.</small>
	<hr />";
    echo "<div class='row'>
			<div class='col-md-4'>
				<h3>Player</h3>
			</div>
			<div class='col-md-4'>
				<h3>Dungeon Status</h3>
			</div>
			<div class='col-md-4'>
				<h3>Actions</h3>
			</div>
		</div>
		<hr />";
    $query = $db->query("/*qc=on*/SELECT * FROM `dungeon` WHERE `dungeon_out` > {$CurrentTime} ORDER BY `dungeon_out` DESC");
    while ($Infirmary = $db->fetch_row($query)) 
	{
		$displaypic = "<img src='" . parseImage(parseDisplayPic($Infirmary['dungeon_user'])) . "' height='75' alt='' title=''>";
		echo "<div class='row'>
			<div class='col-md-4'>
				<div class='row'>
					<div class='col-md'>
						{$displaypic}
					</div>
					<div class='col-md'>
						<a href='profile.php?user={$Infirmary['dungeon_user']}'> " . parseUsername($Infirmary['dungeon_user']) . " </a> 
						[{$Infirmary['dungeon_user']}]
					</div>
				</div>
			</div>
			<div class='col-md-4'>
				Reason: <i>{$Infirmary['dungeon_reason']}</i><br />
				Release: " . TimeUntil_Parse($Infirmary['dungeon_out']) . "
			</div>
			<div class='col-md-4'>
				<div class='row'>
					<div class='col-md'>
						<a class='btn btn-primary' href='?action=bust&user={$Infirmary['dungeon_user']}'>Bust {$api->SystemUserIDtoName($Infirmary['dungeon_user'])}</a>
					</div>
					<div class='col-md'>
						<a class='btn btn-primary' href='?action=bail&user={$Infirmary['dungeon_user']}'>Bail {$api->SystemUserIDtoName($Infirmary['dungeon_user'])}</a>
					</div>
				</div>
			</div>
		</div>
		<hr />";
    }
    $h->endpage();
}

function bail()
{
    global $db, $userid, $ir, $h, $api;
    if (isset($_GET['user'])) {
        $_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : 0;
        //Specified user is invalid or empty.
        if (empty($_GET['user']) || $_GET['user'] == 0) {
            alert('danger', "Uh Oh!", "You must select a user you wish to bail out.", true, 'dungeon.php');
            die($h->endpage());
        }
        //Specified user is not in the dungeon.
        if ($api->UserStatus($_GET['user'], 'dungeon') == false) {
            alert('danger', "Uh Oh!", "The user you wish to bail out is not in the dungeon.", true, 'dungeon.php');
            die($h->endpage());
        }
		//User is in the dungeon.
        if ($api->UserStatus($userid, 'dungeon')) {
            alert('danger', "Uh Oh!", "You are already in the dungeon, so you cannot bust others out.", true, 'dungeon.php');
            die($h->endpage());
        }
        $cost = 250 * $api->UserInfoGet($_GET['user'], 'level', false);
        //User does not have enough Copper Coins to bail this user out.
        if ($api->UserHasCurrency($userid, 'primary', $cost) == false) {
            alert('danger', "Uh Oh!", "You do not have enough cash to bail this user out. You need
			    " . number_format($cost) . ".", true, 'dungeon.php');
            die($h->endpage());
        }
        //Person specified is bailed out. Take user's currency, log the action, and tell the person what happened.
        $api->UserTakeCurrency($userid, 'primary', $cost);
        $api->GameAddNotification($_GET['user'], "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has
            successfully bailed you out of the dungeon.");
        alert('success', "Success!", "You have successfully bailed out {$api->SystemUserIDtoName($_GET['user'])} for {$cost} Copper Coins.", true, 'dungeon.php');
        $db->query("UPDATE `dungeon` SET `dungeon_out` = 0 WHERE `dungeon_user` = {$_GET['user']}");
        die($h->endpage());
    } else {
        alert('danger', "Uh Oh!", "You must select a person to bail out.", true, 'dungeon.php');
    }
}

function bust()
{
    global $db, $userid, $ir, $h, $api;
    if (isset($_GET['user'])) {
        $_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : 0;
        //Person input is invalid or empty.
        if (empty($_GET['user']) || $_GET['user'] == 0) {
            alert('danger', "Uh Oh!", "You must select a person to bust out of the dungeon.", true, 'dungeon.php');
            die($h->endpage());
        }
        //Person not in the dungeon.
        if ($api->UserStatus($_GET['user'], 'dungeon') == false) {
            alert('danger', "Uh Oh!", "This person is not in the dungeon, so you cannot bust them out.", true, 'dungeon.php');
            die($h->endpage());
        }
        //User is in the dungeon.
        if ($api->UserStatus($userid, 'dungeon')) {
            alert('danger', "Uh Oh!", "You are already in the dungeon, so you cannot bust others out.", true, 'dungeon.php');
            die($h->endpage());
        }
        //User does not have 10% brave.
		$brave=$api->UserInfoGet($userid, 'brave', true);
        if ($brave < 10) {
            alert('danger', "Uh Oh!", "You are not brave enough to bust someone out. You need 10% Brave, and you only have
			    {$brave}%.", true, 'dungeon.php');
            die($h->endpage());
        }
        //Update user's info.
        $api->UserInfoSet($userid, 'brave', -10, true);
		$lvl=$api->UserInfoGet($_GET['user'], 'level');
		$mult = Random($lvl+($lvl/2),$lvl*$lvl);
        $chance = min(($ir['level'] / $mult) * 50 + 1, 95);
        //User is successful.
        if (Random(1, 100) < $chance) {
            //Add notification, and tell the user.
            $api->GameAddNotification($_GET['user'], "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has
                successfully busted you out of the dungeon.");
            alert('success', "Success!", "You have successfully busted {$api->SystemUserIDtoName($_GET['user'])} out of the dungeon, and got a little XP too.", true, 'dungeon.php');
            $db->query("UPDATE `dungeon` SET `dungeon_out` = 0 WHERE `dungeon_user` = {$_GET['user']}");
            $db->query("UPDATE `users` SET `busts` = `busts` + 1 WHERE `userid` = {$userid}");
			$xpgained=($ir['xp_needed']/100)*2;
            $db->query("UPDATE `users` SET `xp` = `xp` + {$xpgained} WHERE `userid` = {$userid}");
			die($h->endpage());
        } //User failed. Tell person and throw user in dungeon.
        else {
            $time = min($mult, Random(100,$lvl+100));
            $reason = $db->escape("Caught trying to bust out {$api->SystemUserIDtoName($_GET['user'])}");
            $api->GameAddNotification($_GET['user'], "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has
                failed to bust you out of the dungeon.");
            alert('danger', "Uh Oh!", "While trying to bust your friend out, you were spotted by a guard.", true, 'dungeon.php');
            $api->UserStatusSet($userid, 'dungeon', $time, $reason);
            die($h->endpage());
        }
    } else {
        alert('danger', "Uh Oh!", "You need to specify a user you wish to bust out of the dungeon.", true, 'dungeon.php');
    }
}