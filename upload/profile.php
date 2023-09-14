<?php
require('globals.php');
?><link rel="stylesheet" href="css/profile2.css"><?php
//Include BBCode Engine. Allow players to make pretty!
require('lib/bbcode_engine.php');
$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : '';
if (!$_GET['user']) 
{
    alert("danger", "Uh Oh!", "Please specify a user you wish to view.", true, 'index.php');
	die($h->endpage());
}
$q = $db->query(
            "/*qc=on*/SELECT `u`.`userid`, `user_level`, `laston`, `last_login`,
                    `registertime`, `vip_days`, `username`, `gender`,
					`primary_currency`, `secondary_currency`, `level`, `class`,
					`display_pic`, `hp`, `maxhp`, `guild`, `job`, `jobrank`, 
                    `fedjail`, `bank`, `lastip`, `lastip`, `reset`,
                    `loginip`, `registerip`, `staff_notes`, `town_name`,
                    `house_name`, `guild_name`, `fed_out`, `fed_reason`, `equip_badge`, 
					`infirmary_reason`, `infirmary_out`, `dungeon_reason`, `dungeon_out`,
					`browser`, `os`, `description`, `location`, `vipcolor`, `town_min_level`,
					`fb_reason`, `fb_time`, `mbREASON`, `mbTIME`
                    FROM `users` `u`
                    INNER JOIN `town` AS `t`
                    ON `u`.`location` = `t`.`town_id`
					LEFT JOIN `infirmary` AS `i`
					ON `u`.`userid` = `i`.`infirmary_user`
					LEFT JOIN `dungeon` AS `d`
					ON `u`.`userid` = `d`.`dungeon_user`
                    INNER JOIN `user_estates` AS `e`
                    ON `u`.`estate` = e.`ue_id`
					INNER JOIN `estates` AS `eh`
                    ON `e`.`estate` = eh.`house_id`
                    LEFT JOIN `guild` AS `g`
                    ON `g`.`guild_id` = `u`.`guild`
                    LEFT JOIN `fedjail` AS `f`
                    ON `f`.`fed_userid` = `u`.`userid`
					LEFT JOIN `mail_bans` AS `mb`
                    ON `mb`.`mbUSER` = `u`.`userid`
					LEFT JOIN `forum_bans` AS `fb`
                    ON `fb`.`fb_user` = `u`.`userid`
					LEFT JOIN `userdata` AS `ud`
                    ON `ud`.`userid` = `u`.`userid`
					LEFT JOIN `user_settings` AS `us`
                    ON `us`.`userid` = `u`.`userid`
                    WHERE `u`.`userid` = {$_GET['user']}");

if ($db->num_rows($q) == 0)
{
	$db->free_result($q);
	alert("danger", "Uh Oh!", "The user you are trying to view does not exist, or has an account issue.", true, 'index.php');
	die($h->endpage());
}
$r = $db->fetch_row($q);
$fg = json_decode(get_fg_cache($r['lastip']), true);
$db->free_result($q);
$lon = ($r['laston'] > 0) ? date('F j, Y g:i:s a', $r['laston']) : "Never";
$ula = ($r['laston'] == 0) ? 'Never' : DateTime_Parse($r['laston']);
$ull = ($r['last_login'] == 0) ? 'Never' : DateTime_Parse($r['last_login']);
$active = 0;
if ($r['laston'] > time() - 300)
	$active = 1;
elseif (($r['laston'] < time() - 300) && ($r['laston'] > time() - 900))
	$active = 2;
$sup = date('F j, Y g:i:s a', $r['registertime']);
$mi=$db->query("/*qc=on*/SELECT * FROM `marriage_tmg` WHERE (`proposer_id` = {$r['userid']} OR `proposed_id` = {$r['userid']}) AND `together` = 1");
if ($db->num_rows($mi) == 0)
{
	$married="N/A";
}
else
{
	$mt=$db->fetch_row($mi);
	if ($mt['proposer_id'] == $r['userid'])
	{
		$p1=$ir;
		$p2=$db->fetch_row($db->query("/*qc=on*/SELECT * FROM `users` WHERE `userid` = {$mt['proposed_id']}"));
		$event=$p2;
		$ring=$api->SystemItemIDtoName(getUserItemEquippedSlot($mt['proposer_id'],slot_wed_ring));
	}
	else
	{
		$p1=$db->fetch_row($db->query("/*qc=on*/SELECT * FROM `users` WHERE `userid` = {$mt['proposer_id']}"));
		$p2=$ir;
		$event=$p1;
		$ring=$api->SystemItemIDtoName(getUserItemEquippedSlot($mt['proposed_id'], slot_wed_ring));
	}
	$married="<a href='profile.php?user={$event['userid']}'>{$event['username']}</a>";
}
$displaypic = "<img src='" . parseDisplayPic($r['userid']) . "' class='img-thumbnail' alt='{$r['username']}&#39;s display picture' title='{$r['username']}&#39;s display picture'>";
$user_name = parseUsername($r['userid']);
$ref_q =
	$db->query(
		"/*qc=on*/SELECT COUNT(`referalid`)
				 FROM `referals`
				 WHERE `referal_userid` = {$r['userid']}");
$ref = $db->fetch_single($ref_q);
$db->free_result($ref_q);
$friend_q =
	$db->query(
		"/*qc=on*/SELECT COUNT(`friend_id`)
				 FROM `friends`
				 WHERE `friender` = {$r['userid']}");
$friend = $db->fetch_single($friend_q);
$db->free_result($friend_q);
$enemy_q =
	$db->query(
		"/*qc=on*/SELECT COUNT(`enemy_id`)
				 FROM `enemy`
				 WHERE `enemy_adder` = {$r['userid']}");
$enemy = $db->fetch_single($enemy_q);
$db->free_result($enemy_q);
$CurrentTime = time();
$r['daysold'] = DateTime_Parse($r['registertime'], false, true);
$mb = $db->query("/*qc=on*/SELECT * FROM `mail_bans` WHERE `mbUSER` = {$userid}");
$mbd=0;
if ($db->num_rows($mb) != 0)
	$mbd=1;

//Submit comment
if (isset($_POST['comment']))
{
	$comment = $db->escape(nl2br(strip_tags(htmlentities(stripslashes($_POST['comment'])))));
	if (empty($comment))
	{
		alert('danger',"Uh Oh!","Please fill out the comment form before submitting.",false);
	}
	elseif (strlen($comment) > 500)
	{
		alert('danger',"Uh Oh!","Comments may not be longer than 500 characters.",false);
	}
	elseif ($userid == $_GET['user'])
	{
		alert('danger',"Uh Oh!","You cannot comment on your own profile.",false);
	}
	elseif (!isset($_POST['verf']) || !verify_csrf_code("comment", stripslashes($_POST['verf']))) 
	{
		alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.",false);
	}
	elseif ($api->UserBlocked($userid,$_GET['user']))
	{
		alert('danger', "Uh Oh!", "This user has you blocked. You cannot send messages to players that have you blocked.", false);
	}
	elseif (!permission("CanComment",$userid))
	{
		alert('danger', "Uh Oh!", "You do not have permission to comment on another player's profile.", false);
	}
	elseif ($mbd == 1)
	{
		alert('danger', "Uh Oh!", "You cannot send profile messages if you are mail-banned.", false);
	}
	else
	{
		$db->query("INSERT INTO `comments` (`cRECEIVE`, `cSEND`, `cTIME`, `cTEXT`) VALUES ('{$_GET['user']}', '{$userid}', '" . time() . "', '{$comment}')");
		$api->GameAddNotification($_GET['user'],"{$ir['username']} has left a comment on your profile. Click <a href='profile.php?user={$_GET['user']}'>here</a> to view it.");
		alert('success',"Success!","Comment was posted successfully.",false);
		$justposted=1;
	}
	$fiveminago=time()-300;
	$l3c=$db->query("/*qc=on*/SELECT * FROM `comments` WHERE `cSEND` = {$userid} AND `cTIME` >= {$fiveminago}");
	$same=0;
	while ($ltr = $db->fetch_row($l3c))
	{
		if ($ltr['cTEXT'] == $comment)
			$same=$same+1;
	}
	if ($same >= 3)
	{
		$timed=time()+259200;
		$db->query("INSERT INTO `mail_bans`
					(`mbUSER`, `mbREASON`, `mbBANNER`, `mbTIME`) VALUES
					('{$userid}', 'Spamming', '1', '{$timed}')");
		$api->GameAddNotification($userid, "You have been mail-banned for 3 days for the reason: 'Spamming'.");
		staffnotes_entry($userid,"Mail banned for 3 for 'Spamming'.",0);
	}
}

//Delete comment
if (isset($_GET['del']))
{
	$comment = (isset($_GET['del']) && is_numeric($_GET['del'])) ? abs($_GET['del']) : '';
	if (empty($comment))
	{
		alert('danger',"Uh Oh!","Please select the comment you wish to delete.",false);
	}
	else
	{
		$dc=$db->query("/*qc=on*/SELECT * FROM `comments` WHERE `cID` = {$comment}");
		if ($db->num_rows($dc) == 0)
		{
			alert('danger',"Uh Oh!","This comment does not exist, or has already been deleted.",false);
		}
		$comRes = $db->fetch_row($dc);
        if ($comRes['cRECEIVE'] == $userid)
        {
            $db->query("DELETE FROM `comments` WHERE `cID` = {$comment}");
            alert('success',"Success!","Comment has been deleted successfully.",false);
        }
        elseif ($comRes['cSEND'] == $userid)
		{
		    $db->query("DELETE FROM `comments` WHERE `cID` = {$comment}");
		    alert('success',"Success!","Comment has been deleted successfully.",false);
		}
		elseif ($api->UserMemberLevelGet($userid, 'assistant'))
		{
		    $db->query("DELETE FROM `comments` WHERE `cID` = {$comment}");
		    alert('success',"Success!","Comment has been deleted successfully.",false);
		}
		else
		{
		    alert('danger',"Uh Oh!","You do not have permission to delete this comment.",false);
		}
	}
}
//Some default settings for the profile...
$attbutton = 0;
$statusTitle = "Perfectly fine.";
$statusColor = "text-success";
$statusDetail = "";
$statusIcon = "check-mark";
if (isUserInfirmary($r['userid']))
{
	$attbutton = 1;
	$statusIcon = "health-increase";
	$statusColor = "text-danger";
	$statusTitle="In the infirmary for " . TimeUntil_Parse($r['infirmary_out']) . ".";
	$statusDetail = "{$r['infirmary_reason']}<br />
	[<a href='infirmary.php?action=heal&user={$r['userid']}'>Heal</a>]";
}
elseif (isUserInfirmary($userid))
	$attbutton = 2;
elseif (isUserDungeon($r['userid']))
{
	$statusIcon = "padlock";
	$attbutton = 3;
	$statusColor = "text-danger";
	$statusTitle="In the dungeon for " . TimeUntil_Parse($r['dungeon_out']) . ".";
	$statusDetail = "{$r['dungeon_reason']}<br />
	[<a href='dungeon.php?action=bust&user={$r['userid']}'>Bust</a>]";
}
elseif (isUserDungeon($userid))
	$attbutton = 4;
elseif ($r['hp'] == 0)
	$attbutton = 5;
elseif ($ir['hp'] == 0)
	$attbutton = 6;
	
if (empty($r['description']))
	$r['description']="[i]{$r['username']} does not have a player description set yet.[/i]";

$r['description']=$parser->parse($r['description']);
$r['description']=$parser->getAsHtml();

//Active / Online button
$cardColor = "";
if ($active == 1)
    $activityBadge = createSuccessBadge("Active now!");
elseif ($active == 2)
    $activityBadge = createWarningBadge("Idle");
else
    $activityBadge = createDangerBadge("Offline");

//Gender icon / color
if ($r['gender'] == 'Male')
    $genderBadge = createInfoBadge("Male");
elseif ($r['gender'] == 'Female')
    $genderBadge = createInfoBadge("Male");
else
    $genderBadge = createSecondaryBadge("Other");
$rhpperc = round($r['hp'] / $r['maxhp'] * 100);
if ($_GET['user'] == 21)
{
	if (date('n') == 11)
	{
	    $turkeyKills=getCurrentUserPref(date('Y') . "turkeyKills",0);
		alert("info","","You have hunted " . shortNumberParse($turkeyKills) . " turkey(s) this year.",false);
	}
}
echo "<h3>{$user_name}'s Profile</h3>
<div class='row'>
	<div class='col-auto col-lg-6 col-xxxl-4'>
		<div class='card {$cardColor}'>
			<div class='card-header text-left'>
				User info
			</div>
			<div class='card-body'>
				<div class='row'>
					<div class='col-12 col-lg-6'>
						{$displaypic}
					</div>
					<div class='col-auto col-lg'>
						<div class='row'>
							<div class='col-auto col-md col-xxl-12'>
								<h6>Level</h6>
								<h2>" . shortNumberParse($r['level']) . "</h2>
							</div>
							<div class='col-auto col-md col-xxl-12'>
								<h6>Class</h6>
								<h2>{$r['class']}</h2>
							</div>
							<div class='col-auto col-md col-xxl-12'>
								<h6>Mastery Rank</h6>
								<h2>" . formatMasteryRank($r['reset']) . "</h2>
							</div>
							<div class='col-auto col-md col-xxl-12'>
								<h6>Age</h6>
								<h2>{$r['daysold']}</h2>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class='col-12 col-md-6 col-xxxl-4'>
		<div class='card'>
			<div class='card-header text-left'>
				Actions
			</div>
			<div class='card-body'>
				<div class='row'>
					<div class='col-12'>
						<div id='profileTxt' class='text-left'>What would you like to do?</div><br />
						<div class='row'>
							<div class='col-auto'>
								<a href='attack.php?user={$r['userid']}' onmouseover='profileButtonAttack(\"{$r['username']}\", {$attbutton})' class='btn btn-danger btn-block' style='font-size: 1.75rem;'>" . loadImageAsset("menu/profile/attack-btn.svg", 1.75) . "</a><br />
							</div>";
						      if (date('n') == 10)
						      {
    						       echo"<div class='col-auto'>
    								<a href='23halloween.php?action=tnt&user={$r['userid']}' onmouseover='profileButtonHalloweenVisit(\"{$r['username']}\")' class='btn btn-warning btn-block' style='font-size: 1.75rem;'>" . loadImageAsset("menu/profile/halloween-btn.svg", 1.75) . "</a><br />
    							</div>";   
						      }
						    echo"
							<div class='col-auto'>
								<a href='sendcash.php?user={$r['userid']}' onmouseover='profileButtonCash(\"{$r['username']}\")' class='btn btn-primary btn-block' style='font-size: 1.75rem;'>" . loadImageAsset("menu/profile/send-cash-btn.svg", 1.75) . "</a><br />
							</div>
							<div class='col-auto'>
								<a href='inbox.php?action=compose&user={$r['userid']}' onmouseover='profileButtonMail(\"{$r['username']}\")' class='btn btn-primary btn-block' style='font-size: 1.75rem;'>" . loadImageAsset("menu/profile/inbox-btn.svg", 1.75) . "</a><br />
							</div>
							<div class='col-auto'>
								<a href='hirespy.php?user={$r['userid']}' onmouseover='profileButtonSpy(\"{$r['username']}\")' class='btn btn-primary btn-block' style='font-size: 1.75rem;'>" . loadImageAsset("menu/profile/spy-btn.svg", 1.75) . "</a><br />
							</div>
							<div class='col-auto'>
								<a href='bounty.php?action=addbounty&user={$r['userid']}' onmouseover='profileButtonBounty(\"{$r['username']}\")' class='btn btn-primary btn-block' style='font-size: 1.75rem;'>" . loadImageAsset("explore/bounty_hunter.svg", 1.75) . "</a><br />
							</div>
							<div class='col-auto'>
								<a href='theft.php?user={$r['userid']}' onmouseover='profileButtonTheft(\"{$r['username']}\")' class='btn btn-primary btn-block' style='font-size: 1.75rem;'>" . loadImageAsset("menu/profile/rob-btn.svg", 1.75) . "</a><br />
							</div>
							<div class='col-auto'>
								<a href='poke.php?user={$r['userid']}' onmouseover='profileButtonPoke(\"{$r['username']}\")' class='btn btn-primary btn-block' style='font-size: 1.75rem;'>" . loadImageAsset("menu/profile/poke-btn.svg", 1.75) . "</a><br />
							</div>
							<div class='col-auto'>
								<a href='friends.php?action=add&ID={$r['userid']}' onmouseover='profileButtonFriend(\"{$r['username']}\")' class='btn btn-primary btn-block' style='font-size: 1.75rem;'>" . loadImageAsset("menu/profile/friend-btn.svg", 1.75) . "</a><br />
							</div>
							<div class='col-auto'>
								<a href='enemy.php?action=add&user={$r['userid']}' onmouseover='profileButtonEnemy(\"{$r['username']}\")' class='btn btn-primary btn-block' style='font-size: 1.75rem;'>" . loadImageAsset("menu/profile/enemy-btn.svg", 1.75) . "</a><br />
							</div>
							<div class='col-auto'>
								<a href='contacts.php?action=add&user={$r['userid']}' onmouseover='profileButtonContact(\"{$r['username']}\")' class='btn btn-primary btn-block' style='font-size: 1.75rem;'>" . loadImageAsset("menu/profile/contact-btn.svg", 1.75) . "</a><br />
							</div>
							<div class='col-auto'>
								<a href='blocklist.php?action=add&user={$r['userid']}' onmouseover='profileButtonBlock(\"{$r['username']}\")' class='btn btn-primary btn-block' style='font-size: 1.75rem;'>" . loadImageAsset("menu/profile/block-btn.svg", 1.75) . "</a><br />
							</div>
							<div class='col-auto'>
								<a href='playerreport.php?userid={$r['userid']}' onmouseover='profileButtonReport(\"{$r['username']}\")' class='btn btn-primary btn-block' style='font-size: 1.75rem;'>" . loadImageAsset("menu/profile/report-btn.svg", 1.75) . "</a><br />
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </div>
	<div class='col-12 col-md-6 col-xl-5 col-xxl-6 col-xxxl-4'>
		<div class='card'>
			<div class='card-header text-left'>
				Status info
			</div>
			<div class='card-body'>
				<div class='row'>
					<div class='col-sm'>
						<div class='row text-left'>
							<div class='col-auto'>
							<i class='game-icon game-icon-{$statusIcon} {$statusColor}' style='font-size:2rem;'></i>
							</div>
							<div class='col-auto'>
								<p class='{$statusColor}'>{$statusTitle}<br />
								<i>{$statusDetail}</i></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class='col-12 col-xl-7 col-xxl-6'>
		<div class='card'>
			<div class='card-header text-left'>
				Social info
			</div>
			<div class='card-body'>
				<div class='row'>
					<div class='col-12 col-md-3'>
						<a href='iteminfo.php?ID={$r['equip_badge']}' data-toggle='tooltip' data-placement='bottom' title='{$api->SystemItemIDtoName($r['equip_badge'])}'>" . returnIcon($r['equip_badge'],6) . "</a><br />";
						if (($userid == $r['userid']) && ($r['equip_badge'] > 0))
						    echo "<a class='btn btn-primary' href='unequip.php?type=equip_badge'>Unequip</a>";
						echo"
					</div>
					<div class='col-12 col-md-9'>
						{$r['description']}
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class='col-12 col-md-6'>
		<div class='card'>
			<div class='card-header text-left'>
				Basic info
			</div>
			<div class='card-body'>
				<div class='row'>
					<div class='col-auto'>
						{$activityBadge}
					</div>
					<div class='col-auto'>
                        {$genderBadge}
					</div>";
					if ($r['vip_days'] > 0)
					{
						echo "
						<div class='col-auto'>
							" . createDangerBadge("VIP Days: " . shortNumberParse($r['vip_days'])) . "
						</div>";
					}
					if ($api->UserMemberLevelGet($r['userid'], "forum moderator"))
					{
					    echo "
						<div class='col-auto'>
                            " . createBadge($r['user_level']) . "
						</div>";
					}
					if ($married != "N/A")
					{
						echo "
						<div class='col-auto'>
                            " . createPrimaryBadge("Married to {$event['username']} [{$event['userid']}]") . "
						</div>";
					}
					if ($r['job'] > 0)
					{
						$jobTitle = $db->fetch_single($db->query("SELECT `jNAME` from `jobs` WHERE `jRANK` = {$r['job']}"));
						$jobRank = $db->fetch_single($db->query("SELECT `jrRANK` from `job_ranks` WHERE `jrID` = {$r['jobrank']}"));
						echo "
						<div class='col-auto'>
                            " . createSecondaryBadge(htmlentities("{$jobRank} at {$jobTitle}", ENT_QUOTES)) . "
						</div>";
					}
					if (isUserDungeon($r['userid']))
					{
						echo "
						<div class='col-auto'>
                            " . createDangerBadge(htmlentities("Dungeon for " . TimeUntil_Parse($r['dungeon_out']) . ".", ENT_QUOTES)) . "
						</div>";
					}
					if (isUserInfirmary($r['userid']))
					{
						echo "
						<div class='col-auto'>
                            " . createDangerBadge(htmlentities("Infirmary for " . TimeUntil_Parse($r['infirmary_out']) . ".", ENT_QUOTES)) . "
						</div>";
					}
					if ($r['guild'] > 0)
					{
						$gR=$db->fetch_row($db->query("SELECT * from `guild` WHERE `guild_id` = {$r['guild']}"));
						if ($gR['guild_owner'] == $r['userid'])
							$guildRank = "Guild Leader";
						elseif ($gR['guild_coowner'] == $r['userid'])
							$guildRank = "Guild Co-Leader";
						elseif ($gR['guild_app_manager'] == $r['userid'])
							$guildRank = "Guild App Manager";
						elseif ($gR['guild_vault_manager'] == $r['userid'])
							$guildRank = "Guild Vault Manager";
						elseif ($gR['guild_crime_lord'] == $r['userid'])
							$guildRank = "Guild Crime Lord";
						else
							$guildRank = "Guild Member";
						echo "
						<div class='col-auto'>
                            " . createInfoBadge(htmlentities("{$guildRank} of {$gR['guild_name']}", ENT_QUOTES)) . "
						</div>";
					}
					if ($r['fedjail'])
					{
						echo "
						<div class='col-auto'>
						    " . createDangerBadge(htmlentities("Fed Dungeon: {$r['fed_reason']} for " . TimeUntil_Parse($r['fed_out']) . ".", ENT_QUOTES)) . "
                        </div>";
					}
					if ($r['mbTIME'])
					{
						echo "
						<div class='col-auto'>
                            " . createDangerBadge(htmlentities("Mail Banned: {$r['mbREASON']} for " . TimeUntil_Parse($r['mbTIME']) . ".", ENT_QUOTES)) . "
						</div>";
					}
					if ($r['fb_time'])
					{
						echo "
						<div class='col-auto'>
                            " . createDangerBadge(htmlentities("Forum Banned: {$r['fb_reason']} for " . TimeUntil_Parse($r['fb_time']) . ".", ENT_QUOTES)) . "
						</div>";
					}
					$guildName = ($r['guild'] == 0) ? "N/A" : "<a href='guilds.php?action=view&id={$r['guild']}'>{$gR['guild_name']}</a>";
					$ringDisp = (isset($ring)) ? " ({$ring})" : "";
					$marriageDisp = ($married == "N/A") ? "Single" : "Married to {$married}{$ringDisp}";
					echo"
				</div>
				<hr />
                    <div class='row'>
                        <div class='col-auto col-sm-6 col-md-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Name</b></small>
                                </div>
                                <div class='col-12'>
                                    " . parseUsername($r['userid']) . " " . parseUserID($r['userid']) . "
                                </div>
                            </div>
                        </div>
                    <div class='col-auto col-sm-6 col-md-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Guild</b></small>
                            </div>
                            <div class='col-12'>
                                {$guildName}
                            </div>
                        </div>
                    </div>
                    <div class='col-auto col-sm-6 col-xxl-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Health</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($r['hp']) . " / " . shortNumberParse($r['maxhp']) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto col-sm-6 col-md-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Residence</b></small>
                            </div>
                            <div class='col-12'>
                                {$r['house_name']}
                            </div>
                        </div>
                    </div>
                    <div class='col-auto col-sm-6 col-md-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Marital Status</b></small>
                            </div>
                            <div class='col-12'>
                                {$marriageDisp}
                            </div>
                        </div>
                    </div>
                    <div class='col-auto col-sm-6 col-xxl-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Achievements</b></small>
                            </div>
                            <div class='col-12'>";
                                   echo shortNumberParse($db->fetch_single($db->query("SELECT COUNT(`achievement`) FROM `achievements_done` WHERE `userid` = {$r['userid']}")));
                        echo"</div>
                        </div>
                    </div>
                    <div class='col-auto col-sm-6 col-xxl-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Friends</b></small>
                            </div>
                            <div class='col-12'>
                            " . shortNumberParse($friend) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto col-sm-6 col-xxl-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Enemies</b></small>
                            </div>
                            <div class='col-12'>
                            " . shortNumberParse($enemy) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto col-sm-6 col-xxl-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Referrals</b></small>
                            </div>
                            <div class='col-12'>
                            " . shortNumberParse($ref) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto col-sm-6 col-xxl-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Last Action</b></small>
                            </div>
                            <div class='col-12'>
                            {$ula}
                            </div>
                        </div>
                    </div>
                    <div class='col-auto col-sm-6 col-xxl-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Last Login</b></small>
                            </div>
                            <div class='col-12'>
                            {$ull}
                            </div>
                        </div>
                    </div>
                </div>
			</div>
        </div>
	</div>
	<div class='col-12 col-md-6'>
		<div class='card'>
			<div class='card-header text-left'>
				Personal info
			</div>
			<div class='card-body'>
                <div class='row'>
                    <div class='col-auto col-sm-6 col-xxl-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Copper Coins</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($r['primary_currency']) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto col-sm-6 col-xxl-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Chivalry Tokens</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($r['secondary_currency']) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto col-sm-6 col-xxl-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Town</b></small>
                            </div>
                            <div class='col-12'>
                                <a href='travel.php?to={$r['location']}' data-toggle='tooltip' data-placement='bottom' title='Minimum Level: {$r['town_min_level']}'>{$r['town_name']}</a>
                            </div>
                        </div>
                    </div>
                    <div class='col-auto col-sm-6 col-xxl-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Origin Country</b></small>
                            </div>
                            <div class='col-12'>
                                " . ucfirst($fg['country']) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto col-sm-6 col-md-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Device</b></small>
                            </div>
                            <div class='col-12'>
                                {$r['browser']}/{$r['os']}
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		</div>
      </div>
        <div class='col-12'>
		<div class='card'>
			<div class='card-header text-left'>
				Profile comments
			</div>
			<div class='card-body'>";
			$cq=$db->query("/*qc=on*/SELECT * FROM `comments` WHERE `cRECEIVE` = {$_GET['user']}  ORDER BY `cTIME` DESC LIMIT 4");
				while ($cr = $db->fetch_row($cq))
				{
					$ci['username']=parseUsername($cr['cSEND']);
					echo "<div class='row'>
                            <div class='col-12 col-sm-6 col-md-4 col-xxl-3 col-xxxl-2'>
						      <a href='profile.php?user={$cr['cSEND']}'>{$ci['username']}</a><br />
						<small>" . DateTime_Parse($cr['cTIME']);
						if (($userid == $_GET['user']) || ($cr['cSEND'] == $userid) || ($api->UserMemberLevelGet($userid, "assistant")))
						{
							echo "<br /><a href='profile.php?user={$_GET['user']}&del={$cr['cID']}'>Delete</a>";
						}
						echo "</small>
                        </div>";
						echo "<div class='col-12 col-sm-6 col-md-8 col-xxl'>" .html_entity_decode($cr['cTEXT']) . "</div>";
						echo"</div><hr />";
				}
				if ($userid != $_GET['user'] && empty($justposted))
				{
					if (!permission("CanComment",$userid))
					{
						alert('danger', "Uh Oh!", "You do not have permission to comment on another player's profile.", false);
					}
					else
					{
						$mb = $db->query("/*qc=on*/SELECT * FROM `mail_bans` WHERE `mbUSER` = {$userid}");
						if ($db->num_rows($mb) == 0)
						{
							$csrf=request_csrf_html('comment');
							echo"
							<form method='post'>
                                <textarea class='form-control' name='comment'></textarea>
								{$csrf}
								<br />
								<button class='btn btn-primary btn-block' type='submit'><i class='far fa-comment'></i> Post Comment</button>
							</form>";
						}
					}
				}
				echo"
			</div>
		</div>
	</div>
</div>
<div class='row'>
	<div class='col-12'>";
		if (!in_array($ir['user_level'], array('Member', 'NPC'))) 
		{
			//$parseRisk = parse_risk($fg['risk_level']);
			echo "<a href='#' class='btn btn-primary' data-toggle='modal' data-target='#staff_popup'>User's Staff Panel</a>";
		}
	echo "</div>
</div>";
$h->endpage();
function formatMasteryRank($rank)
{
	if ($rank > 0)
		$rank = $rank - 1;
	if ($rank == 0)
		return "N/A";
	elseif ($rank == 1)
		return "I";
	elseif ($rank == 2)
		return "II";
	elseif ($rank == 3)
		return "III";
	elseif ($rank == 4)
		return "IV";
	elseif ($rank == 5)
		return "V";
	elseif ($rank == 6)
		return "VI";
	elseif ($rank == 7)
		return "VII";
	elseif ($rank == 8)
		return "VIII";
	elseif ($rank == 9)
		return "XI";
	elseif ($rank == 10)
		return "X";
	else
		return "> X";
}
include('forms/staff_popup.php');