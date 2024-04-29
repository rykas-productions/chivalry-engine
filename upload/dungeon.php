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
    global $db, $h, $api, $userid;
	if (isset($_POST['rant']))
	{
		if (postToForumThread($_POST['rant']))
		{
			alert('success',"Success!","You have successfully posted your rant.",false);
		}
		else
		{
			alert('danger',"Uh Oh!","Your rant could not be posted at this time.",false);
		}
	}
    $CurrentTime = time();
    //Count how many users are in the dungeon.
    $PlayerCount = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`dungeon_user`) FROM `dungeon` WHERE `dungeon_out` > {$CurrentTime}"));
    $query = $db->query("/*qc=on*/SELECT * FROM `dungeon` WHERE `dungeon_out` > {$CurrentTime} ORDER BY `dungeon_out` DESC");
    echo "<div class='card'>
            <div class='card-header'>
                <div class='row'>
                    <div class='col-auto'>
                        <i class='game-icon game-icon-cage'></i>
                    </div>
                    <div class='col-auto'>
                        There's currently " . shortNumberParse($PlayerCount) . " players in the dungeon.
                    </div>
                </div>
            </div>
            <div class='card-body'>";
	while ($Infirmary = $db->fetch_row($query)) 
	{
		$displaypic = "<img src='" . parseImage(parseDisplayPic($Infirmary['dungeon_user'])) . "' height='75' alt='' title=''>";
		echo "
        <div class='row'>
            <div class='col-12'>
                <div class='row'>
                    <div class='col-12'>
                        <small><b>Player</b></small>
                    </div>
                    <div class='col-12'>
                        <a href='profile.php?user={$Infirmary['dungeon_user']}'> " . parseUsername($Infirmary['dungeon_user']) . " </a> 
                        " . parseUserID($Infirmary['dungeon_user']) . "
                    </div>
                </div>
            </div>
        </div>
		<div class='card'>
			<div class='card-body'>
				<div class='row'>
					<div class='col-6 col-sm-4 col-md-3 col-lg-2'>
						{$displaypic}
					</div>
					<div class='col-6 col-sm-4 col-md-3'>
						<a href='profile.php?user={$Infirmary['dungeon_user']}'> " . parseUsername($Infirmary['dungeon_user']) . " </a> 
						[{$Infirmary['dungeon_user']}]
					</div>
					<div class='col-12 col-md-6 col-lg'>
						<div class='row'>
							<div class='col-12 col-lg-4'>
								Reason: <i>{$Infirmary['dungeon_reason']}</i><br />
								Release: " . TimeUntil_Parse($Infirmary['dungeon_out']) . "
							</div>
							<div class='col col-lg-8'>
								<div class='row'>
									<div class='col'>
										<a class='btn btn-primary btn-block' href='?action=bust&user={$Infirmary['dungeon_user']}'>Bust {$api->SystemUserIDtoName($Infirmary['dungeon_user'])}</a>
									</div>
									<div class='col'>
										<a class='btn btn-primary btn-block' href='?action=bail&user={$Infirmary['dungeon_user']}'>Bail {$api->SystemUserIDtoName($Infirmary['dungeon_user'])}</a>
									</div>
								</div>
						
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>";
    }
    echo "</div></div>";
	if ($api->UserStatus($userid, 'dungeon'))
	{
		echo "<hr />
		<form method='post'>
			<div class='row'>
				<div class='col-2'>
					Prisoner Rant
				</div>
				<div class='col'>
					<input type='text' class='form-control' name='rant' required='1' placeholder='Post your rant.'>
				</div>
				<div class='col-3'>
					<input type='submit' class='btn btn-primary'>
				</div>
			</div>
		</form>";
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
        if (($api->UserStatus($userid, 'dungeon')) && ($_GET['user'] != $userid))
		{
            alert('danger', "Uh Oh!", "You are already in the dungeon, so you cannot bail others others out.", true, 'dungeon.php');
            die($h->endpage());
        }
        $cost = round(175 + (175 * levelMultiplier($api->UserInfoGet($_GET['user'], 'level', false), getUserResetCount($_GET['user']))));
        //User does not have enough Copper Coins to bail this user out.
        if ($api->UserHasCurrency($userid, 'primary', $cost) == false) {
            alert('danger', "Uh Oh!", "You do not have enough Copper Coins to bail this user out. You need
			    " . shortNumberParse($cost) . " Copper Coins. You only have " . shortNumberParse($ir['primary_currency']) . " Copper Coins.", true, 'dungeon.php');
            die($h->endpage());
        }
        //Person specified is bailed out. Take user's currency, log the action, and tell the person what happened.
        $api->UserTakeCurrency($userid, 'primary', $cost);
		if (($api->UserStatus($userid, 'dungeon')) && ($_GET['user'] != $userid))
		{
			$api->GameAddNotification($_GET['user'], "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has paid off your dungeon bail of " . shortNumberParse($cost) . " Copper Coins.");
		}
        alert('success', "Success!", "You have successfully bailed out {$api->SystemUserIDtoName($_GET['user'])} for " . shortNumberParse($cost) . " Copper Coins.", true, 'dungeon.php');
        $db->query("UPDATE `dungeon` SET `dungeon_out` = 0 WHERE `dungeon_user` = {$_GET['user']}");
		addToEconomyLog('Dungeon Bail', 'copper', ($cost)*-1);
        die($h->endpage());
    } else {
        alert('danger', "Uh Oh!", "You must select a person to bail out.", true, 'dungeon.php');
    }
}

function bust()
{
    global $db, $userid, $ir, $h, $api;
    $bustBrave = 25;
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
        if (($api->UserStatus($userid, 'dungeon')) && ($_GET['user'] != $userid))
		{
            alert('danger', "Uh Oh!", "You are already in the dungeon, so you cannot bust others out.", true, 'dungeon.php');
            die($h->endpage());
        }
		//User is in the dungeon.
        if ($api->UserStatus($userid, 'infirmary'))
		{
            alert('danger', "Uh Oh!", "You are being treated in the the dungeon's infirmary ward and cannot bust others out now.", true, 'dungeon.php');
            die($h->endpage());
        }
        //User does not have 10% brave.
		$brave=$api->UserInfoGet($userid, 'brave', false);
		if ($brave < $bustBrave) {
		    alert('danger', "Uh Oh!", "You need at least " . shortNumberParse($bustBrave) . " Bravery to bust someone out of the dungeon. You only have
			    " . shortNumberParse($brave) . " Bravery.", true, 'dungeon.php');
            die($h->endpage());
        }
        //Update user's info.
        $api->UserInfoSet($userid, 'brave', $bustBrave*-1, false);
		$lvl=$api->UserInfoGet($_GET['user'], 'level');
		$mult = Random($lvl+($lvl/2),$lvl*$lvl);
        $chance = min(($ir['level'] / $mult) * 50 + 1, 95);
        //User is successful.
        if (Random(1, 100) < $chance) {
            //Add notification, and tell the user.
			if ($_GET['user'] != $userid)
			{
				$api->GameAddNotification($_GET['user'], "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has
					successfully busted you out of the dungeon.");
			}
            alert('success', "Success!", "You have successfully busted {$api->SystemUserIDtoName($_GET['user'])} out of the dungeon, and got a little XP too.", true, 'dungeon.php');
            $db->query("UPDATE `dungeon` SET `dungeon_out` = 0 WHERE `dungeon_user` = {$_GET['user']}");
            $db->query("UPDATE `users` SET `busts` = `busts` + 1 WHERE `userid` = {$userid}");
			$xpgained=($ir['xp_needed']/100)*Random(2,8);
            $db->query("UPDATE `users` SET `xp` = `xp` + {$xpgained} WHERE `userid` = {$userid}");
			die($h->endpage());
        } //User failed. Tell person and throw user in dungeon.
        else {
            $time = min($mult, Random(100,$lvl+100));
            $reason = $db->escape("Caught trying to bust out {$api->SystemUserIDtoName($_GET['user'])}");
			if ($_GET['user'] != $userid)
			{
				$api->GameAddNotification($_GET['user'], "<a href='profile.php?user={$userid}'>{$ir['username']}</a> was caught trying to bust you out of the dungeon.");
			}
            alert('danger', "Uh Oh!", "While trying to bust your friend out, you were spotted by a guard. He drags you in with your friend. You now have a {$time} minute sentence.", true, 'dungeon.php');
            $api->UserStatusSet($userid, 'dungeon', $time, $reason);
            die($h->endpage());
        }
    } else {
        alert('danger', "Uh Oh!", "You need to specify a user you wish to bust out of the dungeon.", true, 'dungeon.php');
    }
}

function postToForumThread($reply)
{
	global $db,$api,$userid,$ir;
	$topicID=195;
	$reply = $db->escape(str_replace("\n", "<br />", strip_tags(htmlentities(stripslashes($reply)))));
	if (empty($reply))
		return false;
	elseif (!permission("CanReplyForum",$userid))
		return false;
	elseif ((strlen($reply) > 65535))
		 return false;
	else
	{
		$lastfivemins=time()-300;
		$postcount=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`fp_id`) FROM `forum_posts` WHERE `fp_poster_id` = {$userid} AND `fp_time` > {$lastfivemins}"));
		if ($postcount == 3)
		{
			$api->SystemLogsAdd(1, 'staff', "Forum Warned <a href='../profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}] for 'Spamming'.");
			$api->SystemLogsAdd(1, 'forumwarn', "Forum Warned <a href='../profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}] for 'Spamming'.");
			$api->GameAddNotification($userid, "You have automatically been received a forum warning for the following reason: Spamming.");
			staffnotes_entry($userid,"Forum warned for 'Spamming'.");
		}
		if ($postcount >= 5)
		{
			$endtime = time() + (3 * 86400);
			$db->query("INSERT INTO `forum_bans` VALUES(NULL,  {$userid}, 1, {$endtime}, 'Spamming')");
			$api->SystemLogsAdd(1, 'staff', "Forum banned <a href='../profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}] for 3 days for Spamming.");
			$api->SystemLogsAdd(1, 'forumban', "Forum banned <a href='../profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}] for 3 days for Spamming.");
			$api->GameAddNotification($userid, "You were automtically forum banned you for 3 days for the following reason: 'Spamming'.");
			staffnotes_entry($userid,"Automatically forum banned for 3 days, with reason 'Spamming'.");
		}
		$post_time = time();
		$db->query("
			INSERT INTO `forum_posts` 
			(`fp_poster_id`, `fp_time`, 
			`fp_topic_id`, `fp_editor_id`, 
			`fp_edit_count`, `fp_editor_time`, 
			`fp_text`, `ff_id`) VALUES 
			('{$userid}', '{$post_time}', '{$topicID}', '0', '0', '0', '{$reply}', '2');");
		$db->query(
			"UPDATE `forum_topics`
				 SET `ft_last_id` = {$userid},
				 `ft_last_time` = {$post_time}, 
				 `ft_posts` = `ft_posts` + 1
				 WHERE `ft_id` = {$topicID}");
		$db->query(
			"UPDATE `forum_forums`
				 SET `ff_lp_time` = {$post_time},
				 `ff_lp_poster_id` = {$userid},
				 `ff_lp_t_id` = {$topicID}
				 WHERE `ff_id` = 2");
		return true;
	}
}