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
$fg = json_decode(get_fg_cache("cache/{$r['lastip']}.json", "{$r['lastip']}", 65655), true);
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
		$ring=$api->SystemItemIDtoName($mt['proposer_ring']);
	}
	else
	{
		$p1=$db->fetch_row($db->query("/*qc=on*/SELECT * FROM `users` WHERE `userid` = {$mt['proposer_id']}"));
		$p2=$ir;
		$event=$p1;
		$ring=$api->SystemItemIDtoName($mt['proposed_ring']);
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
		$dc=$db->query("/*qc=on*/SELECT * FROM `comments` WHERE `cID` = {$comment} AND `cRECEIVE` = {$userid}");
		if ($db->num_rows($dc) == 0)
		{
			alert('danger',"Uh Oh!","This comment does not exist, or does not belong to you.",false);
		}
		else
		{
			$db->query("DELETE FROM `comments` WHERE `cID` = {$comment}");
			alert('success',"Success!","Comment has been deleted successfully.",false);
		}
	}
}
//Some default settings for the profile...
$attbutton = 0;
$statusTitle = "Perfectly fine.";
$statusColor = "text-success";
$statusDetail = "";
$statusIcon = "check-mark";
if (user_infirmary($r['userid']))
{
	$attbutton = 1;
	$statusIcon = "health-increase";
	$statusColor = "text-danger";
	$statusTitle="In the infirmary for " . TimeUntil_Parse($r['infirmary_out']) . ".";
	$statusDetail = "{$r['infirmary_reason']}<br />
	[<a href='infirmary.php?action=heal&user={$r['userid']}'>Heal</a>]";
}
elseif (user_infirmary($userid))
	$attbutton = 2;
elseif (user_dungeon($r['userid']))
{
	$statusIcon = "padlock";
	$attbutton = 3;
	$statusColor = "text-danger";
	$statusTitle="In the dungeon for " . TimeUntil_Parse($r['dungeon_out']) . ".";
	$statusDetail = "{$r['dungeon_reason']}<br />
	[<a href='dungeon.php?action=bust&user={$r['userid']}'>Bust</a>]";
}
elseif (user_dungeon($userid))
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
$activeText = "Offline";
$activeColor = "text-danger";
if ($active == 1)
{
	$activeText = "Online";
	$activeColor = "text-success";
}
elseif ($active == 2)
{
	$activeText = "Idle";
	$activeColor = "text-warning";
}

//Gender icon / color
if ($r['gender'] == 'Male')
{
	$genderIcon = "fas fa-mars";
	$genderTxt = "Male";
	$genderClr = "text-info";
}
elseif ($r['gender'] == 'Female')
{
	$genderIcon = "fas fa-venus";
	$genderTxt = "Female";
	$genderClr = "text-warning";
}
else
{
	$genderIcon = "fas fa-transgender";
	$genderTxt = "Other";
	$genderClr = "text-muted";
}
$rhpperc = round($r['hp'] / $r['maxhp'] * 100);
if ($_GET['user'] == 21)
{
	if (date('n') == 11)
	{
		$turkeyKills=getCurrentUserPref('2020turkeyKills',0);
		alert("info","","You have hunted " . number_format($turkeyKills) . " turkey this year.",false);
	}
}
echo "<h3>{$user_name}'s Profile</h3>
<div class='row'>
	<div class='col-lg-6 col-xl-7'>
		<div class='card'>
			<div class='card-header text-left'>
				User info
			</div>
			<div class='card-body'>
				<div class='row'>
					<div class='col-12 col-xl-6'>
						{$displaypic}
					</div>
					<div class='col-lg'>
						<div class='row'>
							<div class='col-6 col-xl-12'>
								<h6>Level</h6>
								<h2>" . number_format($r['level']) . "</h2>
							</div>
							<div class='col-6 col-xl-12'>
								<h6>Class</h6>
								<h2>{$r['class']}</h2>
							</div>
							<div class='col-6 col-xl-12'>
								<h6>Mastery Rank</h6>
								<h2>" . formatMasteryRank($r['reset']) . "</h2>
							</div>
							<div class='col-6 col-xl-12'>
								<h6>Age</h6>
								<h2>{$r['daysold']}</h2>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class='col-lg-6 col-xl-5'>
		<div class='card'>
			<div class='card-header text-left'>
				Actions
			</div>
			<div class='card-body'>
				<div class='row'>
					<div class='col-sm'>
						<div id='profileTxt' class='text-left'>What would you like to do?</div><br />
						<div class='row'>
							<div class='col'>
								<a href='attack.php?user={$r['userid']}' onmouseover='profileButtonAttack(\"{$r['username']}\", {$attbutton})' class='btn btn-primary' style='font-size: 1.75rem;'><i class='game-icon game-icon-swords-emblem'></i></a>
							</div>
							<div class='col'>
								<a href='sendcash.php?user={$r['userid']}' onmouseover='profileButtonCash(\"{$r['username']}\")' class='btn btn-primary ' style='font-size: 1.75rem;'><i class='game-icon game-icon-credits-currency'></i></a>
							</div>
							<div class='col'>
								<a href='inbox.php?action=compose&user={$r['userid']}' onmouseover='profileButtonMail(\"{$r['username']}\")' class='btn btn-primary ' style='font-size: 1.75rem;'><i class='game-icon game-icon-envelope'></i></a>
							</div>
							<div class='col'>
								<a href='hirespy.php?user={$r['userid']}' onmouseover='profileButtonSpy(\"{$r['username']}\")' class='btn btn-primary' style='font-size: 1.75rem;'><i class='game-icon game-icon-spy'></i></a>
							</div>
						</div>
						<br />
						<div class='row'>
							<div class='col'>
								<a href='bounty.php?action=addbounty&user={$r['userid']}' onmouseover='profileButtonBounty(\"{$r['username']}\")' class='btn btn-primary' style='font-size: 1.75rem;'><i class='game-icon game-icon-wanted-reward'></i></a>
							</div>
							<div class='col'>
								<a href='theft.php?user={$r['userid']}' onmouseover='profileButtonTheft(\"{$r['username']}\")' class='btn btn-primary' style='font-size: 1.75rem;'><i class='game-icon game-icon-profit'></i></a>
							</div>
							<div class='col'>
								<a href='poke.php?user={$r['userid']}' onmouseover='profileButtonPoke(\"{$r['username']}\")' class='btn btn-primary' style='font-size: 1.75rem;'><i class='game-icon game-icon-pointing'></i></a>
							</div>
							<div class='col'>
								<a href='friends.php?action=add&ID={$r['userid']}' onmouseover='profileButtonFriend(\"{$r['username']}\")' class='btn btn-primary' style='font-size: 1.75rem;'><i class='game-icon game-icon-thumb-up'></i></a>
							</div>
						</div>
						<br />
						<div class='row'>
							<div class='col'>
								<a href='enemy.php?action=add&user={$r['userid']}' onmouseover='profileButtonEnemy(\"{$r['username']}\")' class='btn btn-primary' style='font-size: 1.75rem;'><i class='game-icon game-icon-thumb-down'></i></a>
							</div>
							<div class='col'>
								<a href='contacts.php?action=add&user={$r['userid']}' onmouseover='profileButtonContact(\"{$r['username']}\")' class='btn btn-primary' style='font-size: 1.75rem;'><i class='game-icon game-icon-id-card'></i></a>
							</div>
							<div class='col'>
								<a href='blocklist.php?action=add&user={$r['userid']}' onmouseover='profileButtonBlock(\"{$r['username']}\")' class='btn btn-primary' style='font-size: 1.75rem;'><i class='game-icon game-icon-trash-can'></i></a>
							</div>
							<div class='col'>
								<a href='playerreport.php?userid={$r['userid']}' onmouseover='profileButtonReport(\"{$r['username']}\")' class='btn btn-primary' style='font-size: 1.75rem;'><i class='game-icon game-icon-hazard-sign'></i></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<br />
		<div class='card'>
			<div class='card-header text-left'>
				Status info
			</div>
			<div class='card-body'>
				<div class='row'>
					<div class='col-sm'>
						<div class='row text-left'>
							<div class='col-2'>
							<i class='game-icon game-icon-{$statusIcon} {$statusColor}' style='font-size:2rem;'></i>
							</div>
							<div class='col text-left'>
								<p class='{$statusColor}'>{$statusTitle}<br />
								<i>{$statusDetail}</i></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<br />
<div class='row'>
	<div class='col-lg-12'>
		<div class='card'>
			<div class='card-header text-left'>
				Social info
			</div>
			<div class='card-body'>
				<div class='row'>
					<div class='col-12 col-md-3'>
						<a href='iteminfo.php?ID={$r['equip_badge']}' data-toggle='tooltip' data-placement='bottom' title='{$api->SystemItemIDtoName($r['equip_badge'])}'>" . returnIcon($r['equip_badge'],6) . "</a><br />
					</div>
					<div class='col-12 col-md-9'>
						{$r['description']}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<br />
<div class='row'>
	<div class='col-lg-7'>
		<div class='card'>
			<div class='card-header text-left'>
				Basic info
			</div>
			<div class='card-body'>
				<div class='row'>
					<div class='col'>
						<i class='fas fa-circle {$activeColor}' data-toggle='tooltip' data-placement='top' title='" . htmlentities($activeText, ENT_QUOTES, 'ISO-8859-1') . "' style='font-size: 1.75rem;'></i>
					</div>
					<div class='col'>
						<i class='{$genderIcon} {$genderClr}' data-toggle='tooltip' data-placement='top' title='" . htmlentities($genderTxt, ENT_QUOTES, 'ISO-8859-1') . "' style='font-size: 1.75rem;'></i>
					</div>";
					if ($r['vip_days'] > 0)
					{
						echo "
						<div class='col'>
							<i class='fa fa-shield-alt text-danger' data-toggle='tooltip' data-placement='top' title='" . htmlentities("Donator", ENT_QUOTES, 'ISO-8859-1') . "' style='font-size: 1.75rem;'></i>
						</div>";
					}
					if ($married != "N/A")
					{
						echo "
						<div class='col'>
							<i class='fas fa-heart' data-toggle='tooltip' data-placement='top' title='Married to {$event['username']} [{$event['userid']}]' style='font-size: 1.75rem; color: pink;'></i>
						</div>";
					}
					if ($r['job'] > 0)
					{
						$jobTitle = $db->fetch_single($db->query("SELECT `jNAME` from `jobs` WHERE `jRANK` = {$r['job']}"));
						$jobRank = $db->fetch_single($db->query("SELECT `jrRANK` from `job_ranks` WHERE `jrID` = {$r['jobrank']}"));
						if ($r['job'] == 1)
							$jobIcon = "game-icon game-icon-anvil";
						elseif ($r['job'] == 2)
							$jobIcon = "game-icon game-icon-rally-the-troops";
						elseif ($r['job'] == 3)
							$jobIcon = "game-icon game-icon-teacher";
						elseif ($r['job'] == 4)
							$jobIcon = "game-icon game-icon-shop";
						elseif ($r['job'] == 5)
							$jobIcon = "game-icon game-icon-farmer";
						elseif ($r['job'] == 6)
							$jobIcon = "game-icon game-icon-guards";
						echo "
						<div class='col'>
							<i class='{$jobIcon}' data-toggle='tooltip' data-placement='top' title='" . htmlentities("{$jobRank} at {$jobTitle}", ENT_QUOTES) . "' style='font-size: 1.75rem;'></i>
						</div>";
					}
					if (user_dungeon($r['userid']))
					{
						echo "
						<div class='col'>
							<i class='fas fa-lock text-secondary' data-toggle='tooltip' data-placement='top' title='" . htmlentities("Dungeon: {$r['dungeon_reason']} for " . TimeUntil_Parse($r['dungeon_out']) . ".", ENT_QUOTES) . "' style='font-size: 1.75rem;'></i>
						</div>";
					}
					if (user_infirmary($r['userid']))
					{
						echo "
						<div class='col'>
							<i class='fas fa-medkit text-primary' data-toggle='tooltip' data-placement='top' title='" . htmlentities("Infirmary: {$r['infirmary_reason']} for " . TimeUntil_Parse($r['infirmary_out']) . ".", ENT_QUOTES) . "' style='font-size: 1.75rem;'></i>
						</div>";
					}
					if ($r['guild'] > 0)
					{
						$gR=$db->fetch_row($db->query("SELECT * from `guild` WHERE `guild_id` = {$r['guild']}"));
						if ($gR['guild_owner'] == $r['userid'])
							$guildRank = "Leader";
						elseif ($gR['guild_coowner'] == $r['userid'])
							$guildRank = "Co-Leader";
						elseif ($gR['guild_app_manager'] == $r['userid'])
							$guildRank = "Application Manager";
						elseif ($gR['guild_vault_manager'] == $r['userid'])
							$guildRank = "Vault Manager";
						elseif ($gR['guild_crime_lord'] == $r['userid'])
							$guildRank = "Crime Lord";
						else
							$guildRank = "Member";
						echo "
						<div class='col'>
							<i class='fas fa-fist-raised' data-toggle='tooltip' data-placement='top' title='" . htmlentities("{$guildRank} of {$gR['guild_name']}.", ENT_QUOTES) . "' style='font-size: 1.75rem; color: magenta;'></i>
						</div>";
					}
					if ($r['fedjail'])
					{
						echo "
						<div class='col'>
							<i class='game-icon game-icon-closed-doors text-muted enchanted_glow' data-toggle='tooltip' data-placement='top' title='" . htmlentities("Federal Dungeon: {$r['fed_reason']} for " . TimeUntil_Parse($r['fed_out']) . ".", ENT_QUOTES) . "' style='font-size: 1.75rem;'></i>
						</div>";
					}
					if ($r['mbTIME'])
					{
						echo "
						<div class='col'>
							<i class='game-icon game-icon-banging-gavel text-success' data-toggle='tooltip' data-placement='top' title='" . htmlentities("Mail Banned: {$r['mbREASON']} for " . TimeUntil_Parse($r['mbTIME']) . ".", ENT_QUOTES) . "' style='font-size: 1.75rem;'></i>
						</div>";
					}
					if ($r['fb_time'])
					{
						echo "
						<div class='col'>
							<i class='game-icon game-icon-gavel text-danger' data-toggle='tooltip' data-placement='top' title='" . htmlentities("Forum Banned: {$r['fb_reason']} for " . TimeUntil_Parse($r['fb_time']) . ".", ENT_QUOTES) . "' style='font-size: 1.75rem;'></i>
						</div>";
					}
					echo"
				</div>
				<hr />
				<div class='row text-left'>
					<div class='col-md'>
						<b>Name</b>
					</div>
					<div class='col-md'>
						<b>" . parseUsername($r['userid']) . " [{$r['userid']}]</b>
					</div>
				</div>
				<hr />
				<div class='row text-left'>
					<div class='col'>
						<b>Rank</b>
					</div>
					<div class='col'>
						{$r['user_level']}
					</div>
				</div>
				<hr />
				<div class='row text-left'>
					<div class='col'>
						<b>Guild</b>
					</div>
					<div class='col'>";
					if ($r['guild'] == 0)
						echo "N/A";
					else
						echo "<a href='guilds.php?action=view&id={$r['guild']}'>{$gR['guild_name']}</a>";
					echo"</div>
				</div>
				<hr />
				<div class='row text-left'>
					<div class='col'>
						<b>Job</b>
					</div>
					<div class='col'>";
					if ($r['job'] == 0)
						echo "N/A";
					else
						echo $jobTitle;
					echo"</div>
				</div>
				<hr />
				<div class='row text-left'>
					<div class='col'>
						<b>Health</b>
					</div>
					<div class='col'>
						" . number_format($r['hp']) . " / " . number_format($r['maxhp']) . "
					</div>
				</div>
				<hr />
				<div class='row text-left'>
					<div class='col'>
						<b>Property</b>
					</div>
					<div class='col'>
						{$r['house_name']}
					</div>
				</div>
				<hr />
				<div class='row text-left'>
					<div class='col'>
						<b>Martial Status</b>
					</div>
					<div class='col'>";
						if ($married == "N/A")
							echo "Single";
						else
							echo "Married to {$married}";
						if (isset($ring))
							echo " ({$ring})";
						echo"
					</div>
				</div>
				<hr />
				<div class='row text-left'>
					<div class='col'>
						<b>Achievements Completed</b>
					</div>
					<div class='col'>";
					echo number_format($db->fetch_single($db->query("SELECT COUNT(`achievement`) FROM `achievements_done` WHERE `userid` = {$r['userid']}")));
					echo "</div>
				</div>
				<hr />
				<div class='row text-left'>
					<div class='col'>
						<b>Friends</b>
					</div>
					<div class='col'>
						" . number_format($friend) . "
					</div>
				</div>
				<hr />
				<div class='row text-left'>
					<div class='col'>
						<b>Enemies</b>
					</div>
					<div class='col'>
						" . number_format($enemy) . "
					</div>
				</div>
				<hr />
				<div class='row text-left'>
					<div class='col'>
						<b>Referrals</b>
					</div>
					<div class='col'>
						" . number_format($ref) . "
					</div>
				</div>
				<hr />
				<div class='row text-left'>
					<div class='col'>
						<b>Last Action</b>
					</div>
					<div class='col'>
						{$ula}
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class='col-lg-5'>
		<div class='card'>
			<div class='card-header text-left'>
				Personal info
			</div>
			<div class='card-body'>
				<div class='row text-left'>
					<div class='col'>
						<b>Copper Coins</b>
					</div>
					<div class='col'>
						" . number_format($r['primary_currency']) . "
					</div>
				</div>
				<hr />
				<div class='row text-left'>
					<div class='col'>
						<b>Chivalry Tokens</b>
					</div>
					<div class='col'>
						" . number_format($r['secondary_currency']) . "
					</div>
				</div>
				<hr />
				<div class='row text-left'>
					<div class='col'>
						<b>Location</b>
					</div>
					<div class='col'>
						<a href='travel.php?to={$r['location']}' data-toggle='tooltip' data-placement='bottom' title='Minimum Level: {$r['town_min_level']}'>{$r['town_name']}</a>
					</div>
				</div>
				<hr />
				<div class='row text-left'>
					<div class='col'>
						<b>Origin Country</b>
					</div>
					<div class='col'>
						" . ucfirst($fg['country']) . "
					</div>
				</div>
				<hr />
				<div class='row text-left'>
					<div class='col'>
						<b>Platform</b>
					</div>
					<div class='col'>
						{$r['browser']} on {$r['os']}
					</div>
				</div>
			</div>
		</div>
		<br />
		<div class='card'>
			<div class='card-header text-left'>
				Profile comments
			</div>
			<div class='card-body'>";
			$cq=$db->query("/*qc=on*/SELECT * FROM `comments` WHERE `cRECEIVE` = {$_GET['user']}  ORDER BY `cTIME` DESC LIMIT 4");
				while ($cr = $db->fetch_row($cq))
				{
					$ci['username']=parseUsername($cr['cSEND']);
					echo "<div class='row'><div class='col'>
						<a href='profile.php?user={$cr['cSEND']}'>{$ci['username']}</a><br />
						<small>" . DateTime_Parse($cr['cTIME']);
						if ($userid == $_GET['user'])
						{
							echo "<br /><a href='profile.php?user={$userid}&del={$cr['cID']}'>Delete</a>";
						}
						echo "</small></div>";
						echo "<div class='col'>" .html_entity_decode($cr['cTEXT']) . "</div>";
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
						if ($db->num_rows($mb) != 0)
						{ }
						else
						{
							$csrf=request_csrf_html('comment');
							echo"
							<form method='post'>
                                <textarea class='form-control' name='comment'></textarea>
								{$csrf}
								<br />
								<button class='btn btn-primary' type='submit'><i class='far fa-comment'></i> Post Comment</button>
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