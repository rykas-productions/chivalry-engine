<?php
require('globals.php');

?><link rel="stylesheet" href="css/profile2.css"><?php
//Include BBCode Engine. Allow players to make pretty!
require('lib/bbcode_engine.php');
$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : '';
if (!$_GET['user']) {
    alert("danger", "Uh Oh!", "Please specify a user you wish to view.", true, 'index.php');
} else {
	if (isMobile())
	{
		header("Location: profile2.php?user={$_GET['user']}");
		exit;
	}
    $q =
        $db->query(
            "/*qc=on*/SELECT `u`.`userid`, `user_level`, `laston`, `last_login`,
                    `registertime`, `vip_days`, `username`, `gender`,
					`primary_currency`, `secondary_currency`, `level`, `class`,
					`display_pic`, `hp`, `maxhp`, `guild`,
                    `fedjail`, `bank`, `lastip`, `lastip`,
                    `loginip`, `registerip`, `staff_notes`, `town_name`,
                    `house_name`, `guild_name`, `fed_out`, `fed_reason`, `equip_badge`, 
					`infirmary_reason`, `infirmary_out`, `dungeon_reason`, `dungeon_out`,
					`browser`, `os`, `description`, `location`, `vipcolor`, `town_min_level`
                    FROM `users` `u`
                    INNER JOIN `town` AS `t`
                    ON `u`.`location` = `t`.`town_id`
					LEFT JOIN `infirmary` AS `i`
					ON `u`.`userid` = `i`.`infirmary_user`
					LEFT JOIN `dungeon` AS `d`
					ON `u`.`userid` = `d`.`dungeon_user`
                    INNER JOIN `estates` AS `e`
                    ON `u`.`maxwill` = e.`house_will`
                    LEFT JOIN `guild` AS `g`
                    ON `g`.`guild_id` = `u`.`guild`
                    LEFT JOIN `fedjail` AS `f`
                    ON `f`.`fed_userid` = `u`.`userid`
					LEFT JOIN `userdata` AS `ud`
                    ON `ud`.`userid` = `u`.`userid`
                    WHERE `u`.`userid` = {$_GET['user']}");

    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        alert("danger", "Uh Oh!", "The user you are trying to view does not exist, or has an account issue.", true, 'index.php');
    }
	else
	{
		$r = $db->fetch_row($q);
        $db->free_result($q);
        $lon = ($r['laston'] > 0) ? date('F j, Y g:i:s a', $r['laston']) : "Never";
        $ula = ($r['laston'] == 0) ? 'Never' : DateTime_Parse($r['laston']);
        $ull = ($r['last_login'] == 0) ? 'Never' : DateTime_Parse($r['last_login']);
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
        $displaypic = ($r['display_pic']) ? "<img src='" . parseImage(parseDisplayPic($r['userid'])) . "' class='img-thumbnail img-fluid' height='350' alt='{$r['username']}&#39;s display picture' title='{$r['username']}&#39;s display picture'>" : '';
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

        $rhpperc = round($r['hp'] / $r['maxhp'] * 100);
        echo "<h3>{$user_name}'s Profile</h3>
		<div class='row bordered'>
			<div class='col-sm'>
				<h5><u>Basic Info</u></h5>
				{$r['username']} [{$r['userid']}]<br />
				Rank: {$r['user_level']}<br />
				Class: {$r['class']}<br />
				Gender: {$r['gender']}<hr />
				
				Register Date: {$sup}<br />
				Last Active: {$ula}<br />
				Last Login: {$ull}<hr />
				
				Age: {$r['daysold']}<br />
				Location: <a href='travel.php?to={$r['location']}' data-toggle='tooltip' data-placement='bottom' title='Minimum Level: {$r['town_min_level']}'>{$r['town_name']}</a><br />
			</div>
			<div class='col-sm'>
				<h5><u>Financial Info</u></h5>
					Copper Coins: " . number_format($r['primary_currency']) . "<br />
					Chivalry Tokens: " . number_format($r['secondary_currency']) . "<br />
					Estate: {$r['house_name']}<br />
					Referrals: " . number_format($ref) . "<br />
					Friends: " . number_format($friend) . "<br />
					Enemies: " . number_format($enemy) . "<br />
			</div>
			<div class='col-sm'>
				<h5><u>Avatar</u></h5>
				{$displaypic}
			</div>
		</div>
		<div class='row bordered'>
			<div class='col-sm'>
				<h5><u>Physical Info</u></h5>
				Level: " . number_format($r['level']) . "<br />";
				echo "Health: " . number_format($r['hp']) . "/" . number_format($r['maxhp']) . "<br />
				Married: {$married}<br />";
				if (isset($ring))
				{
					echo "Ring: {$ring}<br />";
				}
				echo ($r['guild']) ? "Guild: <a href='guilds.php?action=view&id={$r['guild']}'>{$r['guild_name']}</a><br />" : '';
				if (user_infirmary($r['userid'])) {
					echo "<p class='text-danger'>In the infirmary for " . TimeUntil_Parse($r['infirmary_out']) . ".<br />
								{$r['infirmary_reason']}<br />
								[<a href='infirmary.php?action=heal&user={$r['userid']}'>Heal User</a>]</p>";
				}
				if (user_dungeon($r['userid'])) {
					echo "<p class='text-danger'>In the dungeon for " . TimeUntil_Parse($r['dungeon_out']) . ".<br />
								{$r['dungeon_reason']}<br />
								[<a href='dungeon.php?action=bail&user={$r['userid']}'>Bail Out</a>]
								[<a href='dungeon.php?action=bust&user={$r['userid']}'>Bust Out</a>]</p>";
				}
				if ($r['fedjail']) {
					echo "<p class='text-danger'>In the federal dungeon for " . TimeUntil_Parse($r['fed_out']) . ".<br />
								{$r['fed_reason']}</p>";
				}
				echo "<br />
				<b><a href='hirespy.php?user={$r['userid']}' class='btn btn-primary'><i class='fas fa-user-secret'></i> Spy On {$r['username']}</a></b>
			</div>
			<div class='col-sm'>
				<h5><u>Links</u></h5>
					<a href='inbox.php?action=compose&user={$r['userid']}' class='btn btn-primary'><i class='game-icon game-icon-envelope'></i> Message {$r['username']}</a>
                    <br />
				    <br />
				    <a href='sendcash.php?user={$r['userid']}' class='btn btn-primary'><i class='game-icon game-icon-expense'></i> Send {$r['username']} Cash</a>
				    <br />
				    <br />
					<a href='attack.php?user={$r['userid']}' class='btn btn-danger'><i class='game-icon game-icon-crossed-swords'></i> Attack {$r['username']}</a>
					<br />
					<br />
					<a href='theft.php?user={$r['userid']}' class='btn btn-primary'><i class='game-icon game-icon-profit'></i> Rob {$r['username']}</a>
					<br />
					<br />
					<a href='contacts.php?action=add&user={$r['userid']}' class='btn btn-primary'><i class='fas fa-address-card'></i> Add {$r['username']} to Contact List</a>
                    <br />
					<br />
					<a href='poke.php?user={$r['userid']}' class='btn btn-primary'><i class='fas fa-hand-point-right'></i> Poke {$r['username']}</a>
			</div>
			<div class='col-sm'>
				<h5><u>Badge</u></h5>
				" . returnIcon($r['equip_badge'],8) . "
					<br />
					<a href='iteminfo.php?ID={$r['equip_badge']}' data-toggle='tooltip' data-placement='bottom' title='{$r['username']}&#39;s Profile Badge.'>{$api->SystemItemIDtoName($r['equip_badge'])}</a>
			</div>
		</div>";
		if ($r['description']) {
			echo"
		<div class='row bordered'>
			<div class='col-sm'>
				<h5><u>Profile Description</u></h5>";
				//BBCode parse the message.
				$r['description']=$parser->parse($r['description']);
				$r['description']=$parser->getAsHtml();
				echo $r['description'];
				echo"
			</div>
		</div>";
		}
		echo "<div class='row bordered'>
			<div class='col-sm'>
				<h5><u>Profile Comments</u></h5>";
				$cq=$db->query("/*qc=on*/SELECT * FROM `comments` WHERE `cRECEIVE` = {$_GET['user']}  ORDER BY `cTIME` DESC LIMIT 5");
				while ($cr = $db->fetch_row($cq))
				{
					$ci['username']=parseUsername($cr['cSEND']);
					$dp = "<img src='" . parseImage(parseDisplayPic($cr['cSEND'])) . "' class='img-thumbnail img-responsive' width='50' height='50'>";
					echo "<div class='row bordered'><div class='col-sm-3'>
						{$dp}
						<a href='profile.php?user={$cr['cSEND']}'>{$ci['username']}</a><br />
						<small>" . DateTime_Parse($cr['cTIME']) . "</small></div>";
						echo "<div class='col-sm'>" .html_entity_decode($cr['cTEXT']) . "</div>";
						if ($userid == $_GET['user'])
						{
							echo "<div class='col-sm'><a href='profile.php?user={$userid}&del={$cr['cID']}'>Delete</a></div>";
						}
						echo"</div>";
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
                    echo"</td></tr>";
				}
				echo"
			</div>
		</div>";
		if (!in_array($ir['user_level'], array('Member', 'NPC'))) {
			echo"
			<div class='row bordered'>
				<div class='col-sm'>
					<h5><u>Staff Actions</u></h5>";
					$fg = json_decode(get_fg_cache("cache/{$r['lastip']}.json", "{$r['lastip']}", 65655), true);
					$log = $db->fetch_single($db->query("/*qc=on*/SELECT `log_text` FROM `logs` WHERE `log_user` = {$r['userid']} ORDER BY `log_id` DESC"));
					echo"<table class='table table-bordered'>
							<tr>
								<th width='33%'>Data</th>
								<th>Output</th>
							</tr>
							<tr>
								<td>Location</td>
								<td>{$fg['state']}, {$fg['country']}</td>
							</tr>
							<tr>
								<td>Risk Level</td>
								<td>" . parse_risk($fg['risk_level']) . "</td>
							</tr>
							<tr>
								<td>Last Hit</td>
								<td>{$r['lastip']}</td>
							</tr>
							<tr>
								<td>Last Login</td>
								<td>{$r['loginip']}</td>
							</tr>
							<tr>
								<td>Sign Up</td>
								<td>{$r['registerip']}</td>
							</tr>
							<tr>
								<td>
									Last Action
								</td>
								<td>
									{$log}
								</td>
							</tr>
							<tr>
								<td>
									Browser/OS
								</td>
								<td>
									{$r['browser']}/{$r['os']}
								</td>
							</tr>
					</table>
					<form action='staff/staff_punish.php?action=staffnotes' method='post'>
						Staff Notes
						<br />
						<textarea class='form-control' name='staffnotes'>"
                . htmlentities($r['staff_notes'], ENT_QUOTES, 'ISO-8859-1')
                . "</textarea>
						<br />
						<input type='hidden' name='ID' value='{$_GET['user']}' />
						<input type='submit' class='btn btn-primary' value='Update Notes' />
					</form>
					<br />
					<a href='staff/staff_punish.php?action=fedjail&user={$r['userid']}' class='btn btn-primary'>Fedjail</a>
					<a href='staff/staff_punish.php?action=forumban&user={$r['userid']}' class='btn btn-primary'>Forum Ban</a>
				</div>
			</div>";
		}
	}
}
function parse_risk($risk_level)
{
    switch ($risk_level) {
        case 2:
            return "Spam";
        case 3:
            return "Open Public Proxy";
        case 4:
            return "Tor Node";
        case 5:
            return "Honeypot / Botnet / DDOS Attack";
        default:
            return "No Risk";
    }
}
$h->endpage();