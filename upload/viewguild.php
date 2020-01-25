<?php
/*
	File:		viewguild.php
	Created: 	4/5/2016 at 12:32AM Eastern Time
	Info: 		Allows users to view their guild and do various actions.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
$voterquery = 1;
$multi = 1.0;
require('globals.php');
if (!$ir['guild']) {
    alert('danger', "Uh Oh!", "You are not in a guild.", true, 'index.php');
} else {
    $gq = $db->query("/*qc=on*/SELECT * FROM `guild` WHERE `guild_id` = {$ir['guild']}");
    if ($db->num_rows($gq) == 0) {
        alert('danger', "Uh Oh!", "Your guild's data could not be selected. Please contact an admin immediately.");
        die($h->endpage());
    }
    $gd = $db->fetch_row($gq);
    $db->free_result($gq);
    $wq = $db->query("/*qc=on*/SELECT COUNT(`gw_id`) FROM `guild_wars` WHERE (`gw_declarer` = {$ir['guild']} OR `gw_declaree` = {$ir['guild']}) AND `gw_winner` = 0");
    if ($db->fetch_single($wq) > 0) {
        alert('warning', "Guild Wars in Progress", "Your guild is in {$db->fetch_single($wq)} wars. View active wars <a href='?action=warview'>here</a>.", false);
    }
	$gd['xp_needed'] = round(($gd['guild_level'] + 1) * ($gd['guild_level'] + 1) * ($gd['guild_level'] + 1) * 2.2);
	if ($gd['guild_primcurr'] < 0)
		alert('info',"Guild Debt!","Your guild is in debt. If your debt is not paid off in " . TimeUntil_Parse($gd['guild_debt_time']) . " your guild will be dissolved.",false);
    echo "
	<h3><u>Your Guild, {$gd['guild_name']}</u></h3>
   	";
    if (!isset($_GET['action'])) {
        $_GET['action'] = '';
    }
    switch ($_GET['action']) {
        case 'summary':
            summary();
            break;
        case 'donate':
            donate();
            break;
        case "members":
            members();
            break;
        case "kick":
            staff_kick();
            break;
        case "leave":
            leave();
            break;
        case "atklogs":
            atklogs();
            break;
        case "staff":
            staff();
            break;
        case "warview":
            warview();
            break;
        case "armory":
            armory();
            break;
		case "gym":
            gym();
            break;
        case "adonate":
            adonate();
            break;
        case "crimes":
            crimes();
            break;
		case "forums":
            guild_forums();
            break;
		case "viewpolls":
			guild_polls();
			break;
		case "oldpolls":
			guild_oldpolls();
			break;
		case "donatexp":
			guild_donatexp();
			break;
        default:
            home();
            break;
    }
}
function home()
{
    global $db, $userid, $ir, $gd;
    //The main guild index.
	if (!empty($gd['guild_pic']))
	{
		echo 
		"<div class='container'>
			<div class='row'>
				<div class='col-lg-6 mx-auto'>
					<img src='" . parseImage($gd['guild_pic']) . "' placeholder='The {$gd['guild_name']} guild picture.' width='300' class='img-fluid' title='The {$gd['guild_name']} guild picture.'>
				</div>
			</div>
		</div>";
	}
    echo "
    <table class='table table-bordered'>
    		<tr>
    			<td>
    			    <a href='?action=summary'>Summary</a>
                </td>
    			<td>
    			    <a href='?action=donate'>Donate</a>
                </td>
    		</tr>
    		<tr>
    			<td>
    			    <a href='?action=members'>Members</a>
                </td>
    			<td>
    			    <a href='?action=crimes'>Crimes</a>
                </td>
    		</tr>
    		<tr>
    			<td>
    			    <a href='?action=leave'>Leave Guild</a>
                </td>
				<td>
				    <a href='?action=atklogs'>Attack Logs</a>
                </td>
    		</tr>
    		<tr>
    			<td>
    			    <a href='?action=armory'>Armory</a>
                </td>
    			<td>
					<a href='?action=forums'>Forums</a>
				</td>
			</tr>
			<tr>
				<td>
					<a href='?action=viewpolls'>Guild Polls</a>
				</td>
				<td>
					<a href='?action=gym'>Guild Gym</a>
				</td>
			</tr>";
			if ($gd['guild_owner'] == $userid || $gd['guild_coowner'] == $userid)
			{
					echo "
				<tr>
					<td>
						<a href='?action=staff&act2=idx'>Staff Room</a>
					</td>
					<td>
					</td>
				</tr>";
			}
				echo"
	</table>
	<br />
	<table class='table table-bordered'>
		<tr class='table-secondary'>
			<th>
			    Guild Announcement
			</th>
		</tr>
		<tr>
			<td>
			    {$gd['guild_announcement']}
			</td>
		</tr>
	</table>
	<br />
	<b>Last 10 Guild Notifications</b>
	<br />
   	";
    $q = $db->query("/*qc=on*/SELECT * FROM `guild_notifications` WHERE `gn_guild` = {$ir['guild']} ORDER BY `gn_time` DESC  LIMIT 10");
    echo "
	<table class='table table-bordered'>
		<tr align='left'>
			<th>
			    Notification Info
            </th>
			<th>
			    Notification Content
            </th>
		</tr>
   	";
    while ($r = $db->fetch_row($q)) {
        echo "
		<tr align='left'>
			<td>
			    " . DateTime_Parse($r['gn_time']) . "
            </td>
			<td>
			    {$r['gn_text']}
            </td>
		</tr>
   		";
    }
    $db->free_result($q);
    echo "</table>
";
}

function summary()
{
    global $db, $gd, $set, $ir, $api;

    //List all the guild's information
    echo "
	<table class='table table-bordered'>
	<tr>
		<th colspan='2'>
			{$gd['guild_name']} [{$gd['guild_id']}] Information
		</th>
	</tr>
	<tr align='left'>
		<th>
			Leader
		</th>
		<td>
       ";
    $ldrnm = parseUsername($gd['guild_owner']);
        echo "<a href='profile.php?user={$gd['guild_owner']}'> {$ldrnm} </a>";
    echo "</td>
	</tr>
	<tr align='left'>
		<th>
			Co-Leader
		</th>
		<td>";
   $vldrnm = parseUsername($gd['guild_coowner']);
        echo "<a href='profile.php?user={$gd['guild_coowner']}'> {$vldrnm} </a>";
    echo "</td>
	</tr>";
    $cnt = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `guild` = {$gd['guild_id']}");
    echo "
	<tr align='left'>
		<th>
			Members
		</th>
		<td>
			" . $db->fetch_single($cnt) . " / " . $gd['guild_level'] * 5 . "
		</td>
	</tr>
	<tr align='left'>
		<th>
			Level
		</th>
		<td>
			{$gd['guild_level']}
		</td>
	</tr>
	<tr align='left'>
		<th>
			Experience
		</th>
		<td>
			" . number_format($gd['guild_xp']) . " / " . number_format($gd['xp_needed']) . " [<a href='?action=donatexp'>Donate Experience</a>]
		</td>
	</tr>
	<tr align='left'>
		<th>
			Copper Coins*
		</th>
		<td>
			" . number_format($gd['guild_primcurr']) . " / " . number_format((($gd['guild_level'] * $set['GUILD_PRICE']) * 20)) . "
		</td>
	</tr>
	<tr align='left'>
		<th>
			Chivalry Tokens
		</th>
		<td>
			" . number_format($gd['guild_seccurr']) . "
		</td>
	</tr>
	<tr align='left'>
		<th>
			Allies
		</th>
		<td>";
			$q=$db->query("/*qc=on*/SELECT * 
							FROM `guild_alliances` 
							WHERE (`alliance_a` = {$ir['guild']} OR `alliance_b` = {$ir['guild']})
							AND `alliance_true` = 1");
			while ($r=$db->fetch_row($q))
			{
				$type = ($r['alliance_type'] == 1) ? "Traditional" : "Non-aggressive";
				if ($r['alliance_a'] == $ir['guild'])
					$otheralliance=$r['alliance_b'];
				else
					$otheralliance=$r['alliance_a'];
				echo "<a href='?action=view&id={$otheralliance}'>{$api->GuildFetchInfo($otheralliance,'guild_name')}</a><br />";
			}
		
		echo"</td>
	</tr>
      </table>
	  * = Increased every night by 2%.
	  <a href='viewguild.php'>Go Back</a>";
}

function guild_donatexp()
{
	global $db, $gd, $set, $ir, $api, $h, $userid;
	$xpformula = $ir['xp_needed']/65;
	if ($xpformula < 1000)
		$xpformula = 1000;
	$xpformula=round($xpformula);
	
	if (isset($_POST['xp']))
	{
		$_POST['xp'] = (isset($_POST['xp']) && is_numeric($_POST['xp'])) ? abs(intval($_POST['xp'])) : 0;
		if (empty($_POST['xp']))
		{
			alert('danger',"Uh Oh!","You need to input the amount of experience you wish to donate.");
			die($h->endpage());
		}
		if ($_POST['xp'] < $xpformula)
		{
			alert('danger',"Uh Oh!","You need to donate, at minimum, " . number_format($xpformula) . " experience points.");
			die($h->endpage());
		}
		$points=floor($_POST['xp']/$xpformula);
		$xprequired=$points*$xpformula;
		if ($ir['xp'] < $xprequired)
		{
			alert('danger',"Uh Oh!","You do not have the required experience to donate that much.");
			die($h->endpage());
		}
		$db->query("UPDATE `users` SET `xp` = `xp` - {$xprequired} WHERE `userid` = {$userid}");
		updateDonations($gd['guild_id'],$userid,'xp',$xprequired);
		$db->query("UPDATE `guild` SET `guild_xp` = `guild_xp` + {$points} WHERE `guild_id` = {$gd['guild_id']}");
		$event = "<a href='profile.php?user={$userid}'>{$ir['username']}</a> exchanged " . number_format($xprequired) . " experience for {$points} guild experience.";
		$api->GuildAddNotification($gd['guild_id'], $event);
		alert('success',"Success!","You have successfully traded " . number_format($xprequired) . " experience for {$points} guild experience.");
	}
	else
	{
		echo "Here you may donate your experience points to your guild at a ratio of " . number_format($xpformula) . " experience points for 1 Guild 
		Experience Point. You currently have " . number_format($ir['xp']) . " experience points which you can donate. <b>This tool will only take even 
		amounts of experience (Only in groups of {$xpformula}.)</b> How many do you wish to donate to your guild? Experience points donate cannot be given back.<br />
		<form method='post'>
			<input type='number' name='xp' min='{$xpformula}' value='{$xpformula}' class='form-control'>
			<input type='submit' class='btn btn-primary' value='Donate XP'>
		</form>";
	}
}

function guild_forums()
{
	global $db, $ir, $userid, $gd;
    $q = $db->query(
                    "/*qc=on*/SELECT *
                     FROM `forum_forums`
                     WHERE `ff_auth` = 'guild'
                     AND `ff_owner` = {$ir['guild']}");
	if ($db->num_rows($q) == 0)
    {
        $gd['guild_name'] = $db->escape($gd['guild_name']);
        $db->query("INSERT INTO `forum_forums` VALUES (NULL, '{$gd['guild_name']}', '', '0', '0', 'guild', '0', '{$ir['guild']}')");
        $r = array();
        $r['ff_id'] = $db->insert_id();
    }
    else
    {
        $r = $db->fetch_row($q);
        if ($r['ff_name'] != $gd['guild_name'])
        {
            $gd['guild_name'] = $db->escape($gd['guild_name']);
            $db->query(
                    "UPDATE `forum_forums`
                     SET `ff_name` = '{$gd['guild_name']}'
                     WHERE `ff_id` = {$r['ff_id']}");
        }
    }
    $db->free_result($q);
	ob_get_clean();
    $forum_url = "forums.php?viewforum={$r['ff_id']}";
    header("Location: {$forum_url}");
    exit;
}

function donate()
{
    global $db, $userid, $ir, $gd, $api, $h, $set;
    if (isset($_POST['primary'])) {

        //Make sure the POST is safe to work with.
        $_POST['primary'] = (isset($_POST['primary']) && is_numeric($_POST['primary'])) ? abs(intval($_POST['primary'])) : 0;
        $_POST['secondary'] = (isset($_POST['secondary']) && is_numeric($_POST['secondary'])) ? abs(intval($_POST['secondary'])) : 0;

        //Verify we passed the CSRF check.
        if (!isset($_POST['verf']) || !verify_csrf_code('guild_donate', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }

        //Make sure the form is filled out.
        if (empty($_POST['primary']) && empty($_POST['secondary'])) {
            alert('danger', "Uh Oh!", "Please fill out the previous form before submitting.");
            die($h->endpage());
        }

        //Trying to donate more primary than user has.
        if ($_POST['primary'] > $ir['primary_currency']) {
            alert('danger', "Uh Oh!", "You are trying to donate more Copper Coins than you currently have.");
            die($h->endpage());
            //Trying to donate more secondary than user has.
        } else if ($_POST['secondary'] > $ir['secondary_currency']) {
            alert('danger', "Uh Oh!", "You are trying to donate more Chivalry Tokens than you currently have.");
            die($h->endpage());
            //Donation amount would fill up the guild's vault.
        } else if ($_POST['primary'] + $gd['guild_primcurr'] > (($gd['guild_level'] * $set['GUILD_PRICE']) * 20)) {
            alert('danger', "Uh Oh!", "Your guild's vault can only hold " . $gd['guild_level'] * $set['GUILD_PRICE'] . " Copper Coins.");
            die($h->endpage());
        } else {
            //Donate the currencies!
            $api->UserTakeCurrency($userid, 'primary', $_POST['primary']);
            $api->UserTakeCurrency($userid, 'secondary', $_POST['secondary']);
			updateDonations($gd['guild_id'],$userid,'copper',$_POST['primary']);
			updateDonations($gd['guild_id'],$userid,'tokens',$_POST['secondary']);
            $db->query("UPDATE `guild`
                        SET `guild_primcurr` = `guild_primcurr` + {$_POST['primary']},
					    `guild_seccurr` = `guild_seccurr` + {$_POST['secondary']}
					    WHERE `guild_id` = {$gd['guild_id']}");
            $my_name = htmlentities($ir['username'], ENT_QUOTES, 'ISO-8859-1');
            $event = "<a href='profile.php?user={$userid}'>{$my_name}</a> donated
									" . number_format($_POST['primary']) . " Copper Coins and/or
									" . number_format($_POST['secondary']) . " Chivalry Tokens to the guild.";
            $api->GuildAddNotification($gd['guild_id'], $event);
            $api->SystemLogsAdd($userid, 'guild_vault', "Donated " . number_format($_POST['primary']) . " Primary
                Currency and/or " . number_format($_POST['secondary']) . " Chivalry Tokens to their guild.");
            alert('success', "Success!", "You have successfully donated " . number_format($_POST['primary']) . " Primary
			Currency and/or " . number_format($_POST['secondary']) . " Chivalry Tokens to your guild.", true, 'viewguild.php');
        }
    } else {
        $csrf = request_csrf_html('guild_donate');
        echo "
		<form action='?action=donate' method='post'>
			<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Enter the amount of currency you wish to donate to your guild " . number_format($ir['primary_currency']) . "
					Copper Coins and " . number_format($ir['secondary_currency']) . " Chivalry Tokens
				</th>
			</tr>
    		<tr>
    			<td>
    				<b>Copper Coins</b><br />
    				<input type='number' name='primary' value='0' required='1' max='{$ir['primary_currency']}' class='form-control' min='0' />
    			</td>
    			<td>
    				<b>Chivalry Tokens</b><br />
    				<input type='number' name='secondary' required='1' max='{$ir['secondary_currency']}' class='form-control' value='0' min='0' />
    			</td>
    		</tr>
    		<tr>
    			<td colspan='2' align='center'>
    			    {$csrf}
    				<input type='submit' class='btn btn-primary' value='Donate' />
    			</td>
    		</tr>
    	</table>
		</form>
		<a href='viewguild.php'>Go Back</a>";
    }
}

function members()
{
    global $db, $userid, $gd, $api;
    //List all the guild members. ^_^
    echo "
    <table class='table table-bordered table-striped'>
		<tr align='left'>
    		<th width='20%'>
				User
			</th>
    		<th>
				Level
			</th>
			<th>
				Donations*
			</th>
    		<th>
				&nbsp;
			</th>
    	</tr>";
    $q = $db->query("/*qc=on*/SELECT `userid`, `username`, `level`, `display_pic`, `primary_currency` FROM `users` WHERE `guild` = {$gd['guild_id']} ORDER BY `level` DESC");
    $csrf = request_csrf_html('guild_kickuser');
    while ($r = $db->fetch_row($q)) {
		$r['status'] = '';
		if ($api->UserStatus($r['userid'], 'infirmary'))
			$r['status'] .= "In Infirmary<br />";
		if ($api->UserStatus($r['userid'], 'dungeon'))
			$r['status'] .= "In Dungeon<br />";
		if ((!$api->UserStatus($r['userid'], 'dungeon')) && (!$api->UserStatus($r['userid'], 'infirmary')))
			$r['status'] .= "Perfectly Fine<br />";
        $r['username2']=parseUsername($r['userid']);
        $r['display_pic']=parseImage(parseDisplayPic($r['userid']));
		$r2=$db->fetch_row($db->query("SELECT * FROM `guild_donations` WHERE `userid` = {$r['userid']} AND `guildid` = {$gd['guild_id']}"));
        echo "
		<tr>
        	<td>
				<img src='{$r['display_pic']}' class='img-fluid'><br />
				<a href='profile.php?user={$r['userid']}'>{$r['username2']}</a><br />
				{$r['status']}
			</td>
        	<td>
				{$r['level']}
			</td>
			<td>
			Copper Coins: " . number_format($r2['copper']) . "<br />
			Chivalry Tokens: " . number_format($r2['tokens']) . "<br />
			Experience: " . number_format($r2['xp']) . "
			</td>
        	<td>
           ";
        if ($gd['guild_owner'] == $userid || $gd['guild_coowner'] == $userid) {
            echo "
					<form action='?action=kick' method='post'>
						<input type='hidden' name='ID' value='{$r['userid']}' />
						{$csrf}
						<input type='submit' class='btn btn-primary' value='Kick {$r['username']}' />
					</form>";
        } else {
            echo "&nbsp;";
        }
        echo "
			</td>
		</tr>
   		";
    }
    $db->free_result($q);
    echo "
	</table>
	<small>*=Since 10/7/2018 at 5:21PM</small>
	<br />
	<a href='viewguild.php'>Go Back</a>
   	";
}

function staff_kick()
{
    global $db, $userid, $ir, $gd, $api, $h, $wq;
    //Current user is either owner or co-owner
    if ($gd['guild_owner'] == $userid || $gd['guild_coowner'] == $userid) {

        //Verify CSRF check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_kickuser", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.", true, '?action=members');
            die($h->endpage());
        }

        //Make sure POST is safe to work with.
        $_POST['ID'] = (isset($_POST['ID']) && is_numeric($_POST['ID'])) ? abs(intval($_POST['ID'])) : 0;
        $who = $_POST['ID'];

        //Trying to kick the owner.
        if ($who == $gd['guild_owner']) {
            alert('danger', "Uh Oh!", "You cannot kick the guild leader.", true, '?action=members');
            //Trying to kick the co-owner
        } else if ($who == $gd['guild_coowner']) {
            alert('danger', "Uh Oh!", "You cannot kick the guild co-leader.", true, '?action=members');
            //Trying to kick themselves.
        } else if ($who == $userid) {
            alert('danger', "Uh Oh!", "You cannot kick yourself from the guild.", true, '?action=members');
            //Trying to kick while at war.
        } else if ($db->fetch_single($wq) > 0) {
            alert('danger', "Uh Oh!", "You cannot kick members from your guild while you are at war.", true, '?action=members');
        } else {
            //User to be kicked exists and is in the guild.
            $q = $db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `userid` = $who AND `guild` = {$gd['guild_id']}");
            if ($db->num_rows($q) > 0) {
                //Kick the user and add the notification.
                $kdata = $db->fetch_row($q);
                $db->query("UPDATE `users` SET `guild` = 0 WHERE `userid` = {$who}");
                $d_username = htmlentities($kdata['username'], ENT_QUOTES, 'ISO-8859-1');
                $d_oname = htmlentities($ir['username'], ENT_QUOTES, 'ISO-8859-1');
                alert('success', "Success!", "You have kicked {$kdata['username']} from the guild.", true, '?action=members');
                $their_event = "You were kicked out of the {$gd['guild_name']} guild by <a href='profile.php?user={$userid}'>{$d_oname}</a>.";
                $api->GameAddNotification($who, $their_event);
                $event = "<a href='profile.php?user={$who}'>{$d_username}</a> was kicked out of the guild by <a href='profile.php?user={$userid}'>{$d_oname}</a>.";
                $api->GuildAddNotification($gd['guild_id'], $event);
            } else {
                alert('danger', "Uh Oh!", "User does not exist, or is not in the guild.", true, '?action=members');
            }
            $db->free_result($q);
        }
    } else {
        alert('danger', "Uh Oh!", "You do not have permission to kick people from the guild.", true, 'viewguild.php');
    }
}

function leave()
{
    global $db, $userid, $ir, $gd, $api, $h, $wq;
    //Make sure person leaving is not a guild owner/co-owner.
    if ($gd['guild_owner'] == $userid || $gd['guild_coowner'] == $userid) {
        alert('danger', "Uh Oh!", "You cannot leave the guild as the leader or co-leader.", true, 'viewguild.php');
        die($h->endpage());
    }
    //Player *does* want to leave.
    if (isset($_POST['submit']) && $_POST['submit'] == 'yes') {

        //Verify CSRF Check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_leave", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }

        //Make sure no deserters during war times.
        if ($db->fetch_single($wq) > 0) {
            alert('danger', "Uh Oh!", "You cannot leave your guild while at war.", true, 'viewguild.php');
            die($h->endpage());
        }

        //Allow player to leave.
        $db->query("UPDATE `users` SET `guild` = 0  WHERE `userid` = {$userid}");
        $api->GuildAddNotification($ir['guild'], "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has left the guild.");
        alert('success', "Success!", "You have successfully left your guild.", true, 'index.php');

        //Player *does not* want to leave
    } elseif (isset($_POST['submit']) && $_POST['submit'] == 'no') {
        alert('success', "Success!", "You have chosen to stay in your guild.", true, 'viewguild.php');
    } else {
        $csrf = request_csrf_html('guild_leave');
        echo "Do you really want to leave your guild?
        <form action='?action=leave' method='post'>
            {$csrf}
			<input type='hidden' name='submit' value='yes'>
        	<input type='submit' class='btn btn-primary' value='Yes, leave.' />
		</form><br />
		<form action='?action=leave' method='post'>
			{$csrf}
			<input type='hidden' name='submit' value='no'>
        	<input type='submit' class='btn btn-primary' value='No, stay.' />
        </form>
		<a href='viewguild.php'>Go Back</a>";
    }
}

function atklogs()
{
    global $db, $ir, $api;
    //Select the last 50 attacks involving someone in the guild.
    $atks = $db->query("/*qc=on*/SELECT `a`.*, `u1`.*, `u2`.*
                        FROM `attack_logs` AS `a`
                        INNER JOIN `users` AS `u1`
                        ON `attacker` = `u1`.`userid`
                        INNER JOIN `users` AS `u2`
                        ON `attacked` = `u2`.`userid`
                        WHERE `result` != 'lost'
                        AND (`u1`.`guild` = {$ir['guild']} 
                        OR `u2`.`guild` = {$ir['guild']})
                        ORDER BY `attack_time` DESC
                        LIMIT 50");
    echo "<b>Last 50 attacks involving anyone in your guild</b><br />
	<table class='table table-bordered'>
		<tr align='left'>
			<th>Time</th>
			<th>Attack Info</th>
		</tr>";
    while ($r = $db->fetch_row($atks)) {
        $rowcolor = ($api->UserInfoGet($r['attacker'],'guild') == $ir['guild']) ? "text-success" : "text-danger";
        $d = DateTime_Parse($r['attack_time']);
        if ($r['result'] == 'xp')
        {
            $didwhat = "<span class='{$rowcolor} font-weight-bold'>used</span> <a href='profile.php?user={$r['attacked']}'>{$api->SystemUserIDtoName($r['attacked'])}</a> <span class='{$rowcolor} font-weight-bold'>for experience</span>.";
        }
        if ($r['result'] == 'beatup')
        {
            $didwhat = "<span class='{$rowcolor} font-weight-bold'>severely beat up</span> <a href='profile.php?user={$r['attacked']}'>{$api->SystemUserIDtoName($r['attacked'])}</a>.";
        }
        if ($r['result'] == 'mugged')
        {
            $didwhat = "<span class='{$rowcolor} font-weight-bold'>mugged</span> <a href='profile.php?user={$r['attacked']}'>{$api->SystemUserIDtoName($r['attacked'])}</a>.";
        }
        echo "<tr align='left'>
        		<td>$d</td>
        		<td>
                <a href='profile.php?user={$r['attacker']}'>{$api->SystemUserIDtoName($r['attacker'])}</a> {$didwhat}
        		</td>
        	  </tr>";
    }
    $db->free_result($atks);
    echo "</table>
	<a href='viewguild.php'>Go Back</a>";
}

function warview()
{
    global $db, $ir, $api;
    //Select all active wars.
    $wq = $db->query("/*qc=on*/SELECT * FROM `guild_wars` WHERE
					(`gw_declarer` = {$ir['guild']} OR `gw_declaree` = {$ir['guild']}) 
					AND `gw_winner` = 0");
    echo "<b>These are the current wars your guild is participating in.</b> It costs your guild 15,000 Copper Coins per attack your guild 
	wins, and 25,000 Copper Coins per attack your guild loses. This will be taken from your guild's vault following each attack. If your guild 
	cannot afford this, your guild will go into debt. If you fail to pay your debt off in 7 days, your guild will be dissolved.<hr />
	<table class='table table-bordered'>
		<tr align='left'>
			<th>
				Declarer
			</th>
			<th>
				Declared Upon
			</th>
			<th>
				War Concludes
			</th>
		</tr>";
    while ($r = $db->fetch_row($wq)) {
        echo "<tr align='left'>
				<td>
					<a href='guilds.php?action=view&id={$r['gw_declarer']}'>{$api->GuildFetchInfo($r['gw_declarer'],'guild_name')}</a><br />
						(Points: " . number_format($r['gw_drpoints']) . ")
				</td>
				<td>
					<a href='guilds.php?action=view&id={$r['gw_declaree']}'>{$api->GuildFetchInfo($r['gw_declaree'],'guild_name')}</a><br />
						(Points: " . number_format($r['gw_depoints']) . ")
				</td>
				<td>
					" . TimeUntil_Parse($r['gw_end']) . "
				</td>
			</tr>";
    }
    echo "</table>";
}

function armory()
{
    global $db, $gd, $h, $api, $ir;
    //Guild has not purchased the armory
    if ($gd['guild_hasarmory'] == 'false') {
        alert('danger', "Uh Oh!", "Your guild has yet to purchase an armory. Come back after your guild has purchased an armory.", true, 'viewguild.php');
        die($h->endpage());
    } else {
        //List all the armory items.
        echo "Here are the items your guild currently has stockpiled in its armory. You may donate items <a href='?action=adonate'>here</a>.<br />";
        $inv = $db->query("/*qc=on*/SELECT `gaQTY`, `itmsellprice`, `itmid`, `gaID`,
                             `weapon`, `armor`, `itmtypename`, `itmdesc`
                             FROM `guild_armory` AS `iv`
                             INNER JOIN `items` AS `i`
                             ON `iv`.`gaITEM` = `i`.`itmid`
                             INNER JOIN `itemtypes` AS `it`
                             ON `i`.`itmtype` = `it`.`itmtypeid`
                             WHERE `iv`.`gaGUILD` = {$ir['guild']}
                             ORDER BY `i`.`itmtype` ASC, `i`.`itmname` ASC");
        echo "<table class='table table-bordered table-striped'>
	    <thead>
		<tr align='left'>
			<th>
			    Item (Qty)
            </th>
			<th class='hidden-xs-down'>
			    Item Cost (Total)
            </th>
		</tr></thead>";
        $lt = "";
        while ($i = $db->fetch_row($inv)) {
            if ($lt != $i['itmtypename']) {
                $lt = $i['itmtypename'];
                echo "\n<thead><tr>
            			<th colspan='4'>
            				<b>{$lt}</b>
            			</th>
            		</tr></thead>";
            }
            $i['itmdesc'] = htmlentities($i['itmdesc'], ENT_QUOTES);
            echo "
            <tr align='left'>
        		<td>
					<a href='iteminfo.php?ID={$i['itmid']}' data-toggle='tooltip' data-placement='right' title='{$i['itmdesc']}'>
						{$api->SystemItemIDtoName($i['itmid'])}
					</a>";
            if ($i['gaQTY'] > 1) {
                echo " (" . number_format($i['gaQTY']) . ")";
            }
            echo "</td>
        	  <td class='hidden-xs-down'>" . number_format($i['itmsellprice']);
            echo "  (" . number_format($i['itmsellprice'] * $i['gaQTY']) . ")</td></tr>";
        }
        echo "</table>";
    }
}

function gym()
{
	global $db, $gd, $h, $api, $ir, $userid, $multi;
	$macropage = ('viewguild.php?action=gym');
	if ($gd['guild_bonus_time'] > time())
		$multiplier = (1.95+(($gd['guild_level']/100)*6.25)*$multi);
	else
		$multiplier = (1.25+(($gd['guild_level']/100)*6.25)*$multi);
	if ($multiplier > (2.5*$multi))
		$multiplier = (2.5*$multi);
	if ($gd['guild_level'] < 3)
	{
		alert('danger',"Uh Oh!","You guild needs to be at least level 3 to access the guild gym!",true,'viewguild.php');
		die($h->endpage());
	}
	//User is in the infirmary
	if ($api->UserStatus($ir['userid'], 'infirmary')) {
		alert("danger", "Unconscious!", "You cannot train while you're in the infirmary.", true, 'index.php');
		die($h->endpage());
	}
	//User is in the dungeon.
	if ($api->UserStatus($ir['userid'], 'dungeon')) {
		alert("danger", "Locked Up!", "You cannot train while you're in the dungeon.", true, 'index.php');
		die($h->endpage());
	}
	//Convert POST values to Stat Names.
	$statnames = array("Strength" => "strength", "Agility" => "agility", "Guard" => "guard", "Labor" => "labor", "All" => "all");
	//Training amount is not set, so set to 0.
	if (!isset($_GET["amnt"])) {
		$_GET["amnt"] = 0;
	}
	$_GET["amnt"] = abs($_GET["amnt"]);
	if (isset($_GET["stat"]) && $_GET["amnt"]) {
		//User trained stat does not exist.
		if (!isset($statnames[$_GET['stat']])) {
			alert("danger", "Uh Oh!", "The stat you've chosen to train does not exist or cannot be trained.", true, 'back');
			die($h->endpage());
		}
		$stat = $statnames[$_GET['stat']];
		//User is trying to train using more energy than they have.
		if ($_GET['amnt'] > $ir['energy']) {
			alert("danger", "Uh Oh!", "You are trying to train using more energy than you currently have.", false);
		} else {
			$gain = 0;
			$extraecho = '';
			if ($stat == 'all') {
				$gainstr = $api->UserTrain($userid, 'strength', $_GET['amnt'] / 4, $multiplier);
				$gainagl = $api->UserTrain($userid, 'agility', $_GET['amnt'] / 4, $multiplier);
				$gaingrd = $api->UserTrain($userid, 'guard', $_GET['amnt'] / 4, $multiplier);
				$gainlab = $api->UserTrain($userid, 'labor', $_GET['amnt'] / 4, $multiplier);
			} else {
				$gain = $api->UserTrain($userid, $_GET['stat'], $_GET['amnt'], $multiplier);
			}
			//Update energy left and stat's new count.
			if ($stat != 'all')
				$NewStatAmount = $ir[$stat] + $gain;
			$EnergyLeft = $ir['energy'] - $_GET['amnt'];
			//Strength is chosen stat
			if ($stat == "strength") {
				alert('success', "Success!", "You begin to lift weights. You have gained " . number_format($gain) . " Strength by completing
					{$_GET['amnt']} sets of weights. You now have " . number_format($NewStatAmount) . " Strength and {$EnergyLeft} Energy left.", false);
				//Have strength selected for the next training.
				$str_select = "/*qc=on*/SELECTed";
			} //Agility is the chosen stat.
			elseif ($stat == "agility") {
				alert('success', "Success!", "You begin to run laps. You have gained " . number_format($gain) . " Agility by completing
					{$_GET['amnt']} laps. You now have " . number_format($NewStatAmount) . " Agility and {$EnergyLeft} Energy left.", false);
				//Have agility selected for the next training.
				$agl_select = "/*qc=on*/SELECTed";
			} //Guard is the chosen stat.
			elseif ($stat == "guard") {
				alert('success', "Success!", "You begin swimming in the pool. You have gained " . number_format($gain) . " Guard by swimming for
					{$_GET['amnt']} minutes. You now have " . number_format($NewStatAmount) . " Guard and {$EnergyLeft} left.", false);
				//Have guard selected for the next training.
				$grd_select = "/*qc=on*/SELECTed";
			} //Labor is the chosen stat.
			elseif ($stat == "labor") {
				alert('success', "Success!", "You begin moving boxes around the gym. You have gained " . number_format($gain) . " Labor by moving
					{$_GET['amnt']} sets of boxes. You now have " . number_format($NewStatAmount) . " and {$EnergyLeft} Energy left.", false);
				//Have guard selected for the next training.
				$lab_select = "/*qc=on*/SELECTed";
			} elseif ($stat == "all") {
				alert('success', "Success!", "You begin training your Strength, Agility, Guard and Labor all at once. You
					have gained {$gainstr} Strength, {$gainagl} Agility, {$gaingrd} Guard and {$gainlab} Labor. You have
					{$EnergyLeft} Energy left.");
				$all_select = "/*qc=on*/SELECTed";
			}
			//Log the user's training attempt.
			$api->SystemLogsAdd($userid, 'training', "Trained {$stat} {$_GET['amnt']} times and gained " . number_format($gain) . ".");
			echo "<hr />";
			$ir['energy'] -= $_GET['amnt'];
			if ($stat != 'all')
				$ir[$stat] += $gain;
			}
		}
		//Small logic to keep the last trained stat selected.
		if (!isset($str_select)) {
			$str_select = '';
		}
		if (!isset($agl_select)) {
			$agl_select = '';
		}
		if (!isset($grd_select)) {
			$grd_select = '';
		}
		if (!isset($lab_select)) {
			$lab_select = '';
		}
		if (!isset($all_select)) {
			$all_select = '';
		}
		//Grab the user's stat ranks.
		$ir['strank'] = get_rank($ir['strength'], 'strength');
		$ir['agirank'] = get_rank($ir['agility'], 'agility');
		$ir['guarank'] = get_rank($ir['guard'], 'guard');
		$ir['labrank'] = get_rank($ir['labor'], 'labor');
		$ir['all_four'] = ($ir['labor'] + $ir['strength'] + $ir['agility'] + $ir['guard']);
		$ir['af_rank'] = get_rank($ir['all_four'], 'all');
		echo "Choose the stat you wish to train, and enter how many times you wish to train it. You can train up to
		" . number_format($ir['energy']) . " times. <br />
		The guild gym will give you " . number_format($multiplier*100) . "% the stats you'd gain at the Normal Gym.
		<table class='table table-bordered'>
			<tr>
				<form method='get'>
					<input type='hidden' name='action' value='gym'>
					<th>
						Stat
					</th>
					<td>
						<select type='dropdown' name='stat' class='form-control'>
							<option {$str_select} value='Strength'>
								Strength (Have " . number_format($ir['strength']) . "; Ranked: {$ir['strank']})
							</option>
							<option {$agl_select} value='Agility'>
								Agility (Have " . number_format($ir['agility']) . "; Ranked: {$ir['agirank']})
							</option>
							<option {$grd_select} value='Guard'>
								Guard (Have " . number_format($ir['guard']) . "; Ranked: {$ir['guarank']})
							</option>
							<option {$lab_select} value='Labor'>
								Labor (Have " . number_format($ir['labor']) . "; Ranked: {$ir['labrank']})
							</option>
							<option {$all_select} value='All'>
								All Four (Have " . number_format($ir['all_four']) . "; Ranked: {$ir['af_rank']})
							</option>
						</select>
					</td>
			</tr>
			<tr>
				<th>
					Training Duration
				</th>
				<td>
					<input type='number' class='form-control' min='1' max='{$ir['energy']}' name='amnt' value='{$ir['energy']}' />
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Train' />
				</td>
			</tr>
			<tr>
				<td>
					<a href='temple.php?action=energy' class='btn btn-primary'>Refill Energy</a>
				</td>
				<td>
					<a href='temple.php?action=will' class='btn btn-primary'>Regen Will</a>
				</td>
			</tr>
				</form>
		</table>";
}

function adonate()
{
    global $api, $userid, $h, $ir, $gd;
    if ($gd['guild_hasarmory'] == 'false') {
        alert('danger', "Uh Oh!", "Your guild has yet to purchase an armory. Come back after your guild has purchased an armory.", true, 'viewguild.php');
        die($h->endpage());
    } else {
        if (isset($_POST['item'])) {
            //Secure the POST
            $_POST['item'] = (isset($_POST['item']) && is_numeric($_POST['item'])) ? abs($_POST['item']) : 0;
            $_POST['qty'] = (isset($_POST['qty']) && is_numeric($_POST['qty'])) ? abs($_POST['qty']) : 0;

            //Verify CSRF Check has passed.
            if (!isset($_POST['verf']) || !verify_csrf_code("guild_armory_donate", stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
                die($h->endpage());
            }

            //Verify item exists
            if (!$api->SystemItemIDtoName($_POST['item'])) {
                alert('danger', "Uh Oh!", "You are trying to donate a non-existent item.");
                die($h->endpage());
            }

            //Verify user has the item/quantity
            if (!$api->UserHasItem($userid, $_POST['item'], $_POST['qty'])) {
                alert('danger', "Uh Oh!", "You are trying to donate an item you don't have, or an amount you don't have.");
                die($h->endpage());
            }

            //Donation successful!, log everything.
            $item = $api->SystemItemIDtoName($_POST['item']);
            $api->UserTakeItem($userid, $_POST['item'], $_POST['qty']);
            $api->GuildAddItem($gd['guild_id'], $_POST['item'], $_POST['qty']);
            $api->SystemLogsAdd($userid, 'guilds', "Donated {$_POST['qty']} {$item}(s) to their guild's armory.");
            $api->GuildAddNotification($ir['guild'], "{$ir['username']} has donated {$_POST['qty']} {$item}(s) to the guild's armory.");
            alert("success", "Success!", "You have successfully donated {$_POST['qty']} $item}(s) to your guild's armory.", true, "?action=armory");
        } else {
            $csrf = request_csrf_html('guild_armory_donate');
            echo "<form method='post'>
            Fill out the form completely to donate an item to your guild.<br />
            " . inventory_dropdown() . "<br />
            <input type='number' name='qty' placeholder='Quantity' class='form-control'>
            <br />
            {$csrf}
            <input type='submit' value='Donate Item' class='btn btn-primary'>
            </form>";
        }
    }
}

function crimes()
{
    global $gd, $db;
    if ($gd['guild_crime'] > 0) {
        $ttc = TimeUntil_Parse($gd['guild_crime_done']);
        $gcname = $db->fetch_single($db->query("/*qc=on*/SELECT `gcNAME` from `guild_crimes` WHERE `gcID` = {$gd['guild_crime']}"));
        echo "Your guild will be attempting to commit the {$gcname} crime. It will begin in {$ttc}.";
    } else {
        echo "Your guild is not currently planning on committing a crime. Contact your guild's leadership to stage one.";
    }
}

function guild_polls()
{
	global $db, $userid, $ir, $h;
    echo "Cast your vote today!<br />";

    $_POST['poll'] = (isset($_POST['poll']) && is_numeric($_POST['poll'])) ? abs($_POST['poll']) : '';
    $_POST['choice'] = (isset($_POST['choice']) && is_numeric($_POST['choice'])) ? abs($_POST['choice']) : '';
    $ir['voted'] = unserialize($ir['voted']);
    if (!$_POST['choice'] || !$_POST['poll']) {
        echo "<a href='?action=oldpolls'>View Closed Polls</a>";
    }
    if ($_POST['choice'] && $_POST['poll']) {
        if (isset($ir['voted'][$_POST['poll']])) {
            alert('danger', "Uh Oh!", "You have already voted in this poll.");
            die($h->endpage());
        }
        $check_q = $db->query("/*qc=on*/SELECT COUNT(`id`) FROM `polls`  WHERE `active` = '1' AND `id` = {$_POST['poll']} AND `visibility` = {$ir['guild']}");
        if ($db->fetch_single($check_q) == 0) {
            $db->free_result($check_q);
            alert('danger', "Uh Oh!", "Poll does not exist, or is no longer active.");
            die($h->endpage());
        }
        $db->free_result($check_q);
        $ir['voted'][$_POST['poll']] = $_POST['choice'];
        $ser = $db->escape(serialize($ir['voted']));
        $db->query(
            "UPDATE `uservotes`
				 SET `voted` = '$ser'
				 WHERE `userid` = $userid");
        $db->query("UPDATE `polls` SET `voted{$_POST['choice']}` = `voted{$_POST['choice']}` + 1 WHERE `active` = '1' AND `id` = {$_POST['poll']}");
        alert('success', "Success!", "You have successfully submitted your vote.", true, '?action=viewpolls');
    } else {
        $q = $db->query("/*qc=on*/SELECT * FROM `polls` WHERE `active` = '1' AND `visibility` = {$ir['guild']}");
        if (!$db->num_rows($q)) {
            echo "<br />There's no polls open at this time.";
        } else {
            while ($r = $db->fetch_row($q)) {
                $r['votes'] = $r['voted1'] + $r['voted2'] + $r['voted3'] + $r['voted4'] + $r['voted5'] + $r['voted6'] + $r['voted7'] + $r['voted8'] + $r['voted9'] + $r['voted10'];
                if (isset($ir['voted'][$r['id']])) {
                    echo "<br />
					<table class='table table-bordered'>
						<tr>
							<th width='40%'>Polling Options</th>
							<th width='10%'>Votes</th>
							<th>Percentage</th>
						</tr>
						<tr>
							<th colspan='3'>Polling Question: {$r['question']} (Already Voted!)</th>
						</tr>";
                    if (!$r['hidden']) {
                        for ($i = 1; $i <= 10; $i++) {
                            if ($r['choice' . $i]) {
                                $k = 'choice' . $i;
                                $ke = 'voted' . $i;
                                if ($r['votes'] != 0) {
                                    $perc = round(($r[$ke] / $r['votes'] * 100));
                                } else {
                                    $perc = 0;
                                }
                                echo "<tr>
									<td>{$r[$k]}</td>
									<td>{$r[$ke]}</td>
									<td>
										<div class='progress'>
											<div class='progress-bar' role='progressbar' aria-valuenow='{$perc}' aria-valuemin='0' aria-valuemax='100' style='width:{$perc}%'>
												{$perc}%
											</div>
										</div>
									</td>
								  </tr>";
                            }
                        }
                    } else {
                        echo "<tr>
							<td colspan='4' align='center'>
								Results are hidden until the poll ends.
							</td>
						  </tr>";
                    }
                    $myvote = $r['choice' . $ir['voted'][$r['id']]];
                    echo "<tr>
						<th colspan='2'>You Voted: {$myvote}</th>
						<th colspan='2'>Total Votes " . number_format($r['votes']) . "</th>
					  </tr>
				</table>";
                } else {
                    echo "<br />
				<form method='post'>
					<input type='hidden' name='poll' value='{$r['id']}' />
					<table class='table table-bordered'>
						<tr>
							<th>Polling Options</th>
							<th>Select</th>
						</tr>
						<tr>
							<th colspan='2'>Polling Question: {$r['question']} (Not Voted)</th>
						</tr>";
                    for ($i = 1; $i <= 10; $i++) {
                        if ($r['choice' . $i]) {
                            $k = 'choice' . $i;
                            if ($i == 1) {
                                $c = "checked='checked'";
                            } else {
                                $c = "";
                            }
                            echo "<tr>
								<td>{$r[$k]}</td>
								<td><input type='radio' class='form-control' name='choice' value='$i' $c /></td>
							  </tr>";
                        }
                    }
                    echo "<tr>
						<td colspan='2'><input type='submit' class='btn btn-primary' value='Cast Vote' /></td>
					  </tr>
				</table></form>";
                }
            }
        }
		echo "<br /><a href='viewguild.php'>Go Back</a>";
        $db->free_result($q);
    }
}

function guild_oldpolls()
{
	global $db, $ir;
    echo "<a href='?action=viewpolls'>Cast Your Vote!</a><br />";
    $q =
        $db->query("/*qc=on*/SELECT * FROM `polls` WHERE `active` = '0' AND `visibility` = {$ir['guild']} ORDER BY `id` DESC");
    if (!$db->num_rows($q)) {
        alert('danger', "Uh Oh!", "There are no closed polls.", true, '?action=viewpolls');
    } else {
        while ($r = $db->fetch_row($q)) {
            $r['votes'] = $r['voted1'] + $r['voted2'] + $r['voted3'] + $r['voted4'] + $r['voted5'] + $r['voted6'] + $r['voted7'] + $r['voted8'] + $r['voted9'] + $r['voted10'];
            echo "<table class='table table-bordered'>
					<tr>
						<th width='40%'>Polling Options</th>
						<th width='10%'>Votes</th>
						<th>Percentage</th>
					</tr>
					<tr>
						<th colspan='4'>Polling Question: {$r['question']}</th>
					</tr>";
            for ($i = 1; $i <= 10; $i++) {
                if ($r['choice' . $i]) {
                    $k = 'choice' . $i;
                    $ke = 'voted' . $i;
                    if ($r['votes'] != 0) {
                        $perc = round($r[$ke] / $r['votes'] * 100);
                    } else {
                        $perc = 0;
                    }
                    echo "<tr>
							<td>{$r[$k]}</td>
							<td>{$r[$ke]}</td>
							<td>
								<div class='progress'>
											<div class='progress-bar' role='progressbar' aria-valuenow='{$perc}' aria-valuemin='0' aria-valuemax='100' style='width:{$perc}%'>
												{$perc}%
											</div>
										</div>
							</td>
						  </tr>";
                }
            }
            echo "<tr>
					<th colspan='4'>Total Votes: {$r['votes']}</th>
				  </tr>
			</table><br />
			<a href='viewguild.php'>Go Back</a>";
        }
    }
    $db->free_result($q);
}

function staff()
{
    global $userid, $gd, $h;
    if ($gd['guild_owner'] == $userid || $gd['guild_coowner'] == $userid) {
        if (!isset($_GET['act2'])) {
            $_GET['act2'] = 'idx';
        }
        switch ($_GET['act2']) {
            case "idx":
                staff_idx();
                break;
            case "apps":
                staff_apps();
                break;
            case "vault":
                staff_vault();
                break;
            case "coowner":
                staff_coowner();
                break;
            case "ament":
                staff_announcement();
                break;
			case "pic":
                staff_pic();
                break;
            case "massmail":
                staff_massmail();
                break;
            case "masspay":
                staff_masspayment();
                break;
            case "desc":
                staff_desc();
                break;
            case "leader":
                staff_leader();
                break;
            case "name":
                staff_name();
                break;
            case "town":
                staff_town();
                break;
            case "untown":
                staff_untown();
                break;
            case "declarewar":
                staff_declare();
                break;
            case "levelup":
                staff_levelup();
                break;
            case "tax":
                staff_tax();
                break;
            case "dissolve":
                staff_dissolve();
                break;
            case "armory":
                staff_armory();
                break;
            case "crimes":
                staff_crimes();
                break;
			case "intromsg":
                staff_intromsg();
                break;
			case "blockapps":
                staff_blockapps();
                break;
			case "doally":
                staff_ally();
                break;
			case "viewrally":
                staff_view_alliance_request();
                break;
			case "viewallies":
                staff_view_alliances();
                break;
			case "addpoll":
                add_poll();
                break;
			case "endpoll":
                end_poll();
                break;
			case "boost":
                staff_bonus();
                break;
            default:
                staff_idx();
                break;
        }
    } else {
        alert('danger', "Uh Oh!", "You have no permission to be here.", true, 'viewguild.php');
        die($h->endpage());
    }
}

function staff_idx()
{
    global $db, $userid, $gd;
    echo "<table class='table table-bordered'>
	<tr align='left'>
		<td>
			<b>Guild Co-Leader</b><br />
			<a href='?action=staff&act2=apps'>Application Management</a><br />
			<a href='?action=staff&act2=intromsg'>Introductory Message</a><br />
			<a href='?action=staff&act2=blockapps'>Block Applications</a><br />
			<a href='?action=staff&act2=vault'>Vault Management</a><br />
			<a href='?action=staff&act2=armory'>Armory Management</a><br />
			<a href='?action=staff&act2=coowner'>Transfer Co-Leader</a><br />
			<a href='?action=staff&act2=ament'>Change Guild Announcement</a><br />
			<a href='?action=staff&act2=pic'>Change Guild Picture</a><br />
			<a href='?action=staff&act2=massmail'>Mass Mail Guild</a><br />
			<a href='?action=staff&act2=masspay'>Mass Pay Guild</a><br />
			<a href='?action=staff&act2=levelup'>Level Up Guild</a><br />
			<a href='?action=staff&act2=crimes'>Guild Crimes</a><br />
			<a href='?action=staff&act2=addpoll'>Start Poll</a><br />
			<a href='?action=staff&act2=endpoll'>End Poll</a><br />
			<a href='?action=staff&act2=boost'>Enable Boost</a><br />
		</td>";
    if ($gd['guild_owner'] == $userid) {
        echo "
		<td>
			<b>Guild Leader</b><br />
			<a href='?action=staff&act2=leader'>Transfer Leader</a><br />
			<a href='?action=staff&act2=name'>Change Guild Name</a><br />
			<a href='?action=staff&act2=desc'>Change Guild Description</a><br />
			<a href='?action=staff&act2=town'>Change Guild Town</a><br />";
        if ($db->fetch_single($db->query("/*qc=on*/SELECT `town_id` FROM `town` WHERE `town_guild_owner` = {$gd['guild_id']}")) > 0) {
            echo "<a href='?action=staff&act2=untown'>Surrender Guild Town</a><br />
				<a href='?action=staff&act2=tax'>Change Town Tax</a><br />";
        }
        echo "<a href='?action=staff&act2=doally'>Declare Alliance</a><br />
		<a href='?action=staff&act2=viewrally'>View Alliance Requests</a><br />
		<a href='?action=staff&act2=viewallies'>View Allies</a><br />
		<a href='?action=staff&act2=declarewar'>Declare War</a><br />
		<a href='?action=staff&act2=dissolve'>Dissolve Guild</a><br />
		</td>";
    }
    echo "</tr></table>
	<a href='viewguild.php'>Go Back</a>";
}

function add_poll()
{
	global $db, $h, $userid, $api, $ir;
    if (isset($_POST['question'])) {
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_startpoll', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "We have blocked this action for your security. Please fill out the form quickly next time.");
            die($h->endpage());
        }
        $question = (isset($_POST['question'])) ? $db->escape(strip_tags(stripslashes($_POST['question']))) : '';
        $choice1 = (isset($_POST['choice1'])) ? $db->escape(strip_tags(stripslashes($_POST['choice1']))) : '';
        $choice2 = (isset($_POST['choice2'])) ? $db->escape(strip_tags(stripslashes($_POST['choice2']))) : '';
        $choice3 = (isset($_POST['choice3'])) ? $db->escape(strip_tags(stripslashes($_POST['choice3']))) : '';
        $choice4 = (isset($_POST['choice4'])) ? $db->escape(strip_tags(stripslashes($_POST['choice4']))) : '';
        $choice5 = (isset($_POST['choice5'])) ? $db->escape(strip_tags(stripslashes($_POST['choice5']))) : '';
        $choice6 = (isset($_POST['choice6'])) ? $db->escape(strip_tags(stripslashes($_POST['choice6']))) : '';
        $choice7 = (isset($_POST['choice7'])) ? $db->escape(strip_tags(stripslashes($_POST['choice7']))) : '';
        $choice8 = (isset($_POST['choice8'])) ? $db->escape(strip_tags(stripslashes($_POST['choice8']))) : '';
        $choice9 = (isset($_POST['choice9'])) ? $db->escape(strip_tags(stripslashes($_POST['choice9']))) : '';
        $choice10 = (isset($_POST['choice10'])) ? $db->escape(strip_tags(stripslashes($_POST['choice10']))) : '';
        $hidden = (isset($_POST['hidden']) && is_numeric($_POST['hidden'])) ? abs(intval($_POST['hidden'])) : '';
        if (empty($question) || empty($choice1) || empty($choice2)) {
            alert('danger', "Uh Oh!", "Please be sure to fill out the question, and two polling options. Thank you.");
            die($h->endpage());
        }
        $db->query("INSERT INTO `polls` (`active`, `question`, `choice1`,
					`choice2`, `choice3`,`choice4`, `choice5`, `choice6`, 
					`choice7`, `choice8`,`choice9`, `choice10`, `hidden`, 
					`visibility`)
                     VALUES
					 ('1', '$question', '$choice1', '$choice2',
                     '$choice3', '$choice4', '$choice5', '$choice6',
                     '$choice7', '$choice8', '$choice9' ,'$choice10',
                     '{$_POST['hidden']}', '{$ir['guild']}')");
        alert('success', "Success!", "You have successfully created a poll for your guild.", true, 'index.php');
        $q = $db->query("/*qc=on*/SELECT `userid`, `username` FROM `users` WHERE `guild` = {$ir['guild']}");
        while ($r = $db->fetch_row($q)) {
            notification_add($r['userid'], "Your guild has started a poll. Vote in it <a href='viewguild.php?action=viewpolls'>here</a>.");
        }
        die($h->endpage());
    } else {
        echo "Start a Poll";
        $csrf = request_csrf_html('staff_startpoll');
        echo "<hr />
		<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th width='33%'>
					Question
				</th>
				<td>
					<input type='text' required='1' class='form-control' name='question' />
				</td>
			</tr>
			<tr>
				<th>
					Choice 1
				</th>
				<td>
					<input type='text' required='1' class='form-control' name='choice1' />
				</td>
			</tr>
			<tr>
				<th>
					Choice 2
				</th>
				<td>
					<input type='text' required='1' class='form-control' name='choice2' />
				</td>
			</tr>
			<tr>
				<th>
					Choice 3
				</th>
				<td>
					<input type='text' class='form-control' name='choice3' />
				</td>
			</tr>
			<tr>
				<th>
					Choice 4
				</th>
				<td>
					<input type='text' class='form-control' name='choice4' />
				</td>
			</tr>
			<tr>
				<th>
					Choice 5
				</th>
				<td>
					<input type='text' class='form-control' name='choice5' />
				</td>
			</tr>
			<tr>
				<th>
					Choice 6
				</th>
				<td>
					<input type='text' class='form-control' name='choice6' />
				</td>
			</tr>
			<tr>
				<th>
					Choice 7
				</th>
				<td>
					<input type='text' class='form-control' name='choice7' />
				</td>
			</tr>
			<tr>
				<th>
					Choice 8
				</th>
				<td>
					<input type='text' class='form-control' name='choice8' />
				</td>
			</tr>
			<tr>
				<th>
					Choice 9
				</th>
				<td>
					<input type='text' class='form-control' name='choice9' />
				</td>
			</tr>
			<tr>
				<th>
					Choice 10
				</th>
				<td>
					<input type='text' class='form-control' name='choice10' />
				</td>
			</tr>
			<tr>
				<th>
					Hide results until poll is closed?
				</th>
				<td>
					<select name='hidden' class='form-control' type='dropdown'>
						<option value='0'>No</option>
						<option value='1'>Yes</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Create Poll'>
				</td>
			</tr>
		</table>
		{$csrf}
		</form>
		<a href='?action=staff&act2=idx'>Go Back</a>";
    }
}

function end_poll()
{
	global $db, $h, $api, $userid, $ir;
    $_POST['poll'] = (isset($_POST['poll']) && is_numeric($_POST['poll'])) ? abs(intval($_POST['poll'])) : '';
    if (empty($_POST['poll'])) {
        $csrf = request_csrf_html('staff_endpoll');
        echo "
        Select the poll you wish to end.
        <br />
        <form method='post'>
           ";
        $q =
            $db->query(
                "/*qc=on*/SELECT `id`, `question`
                         FROM `polls`
                         WHERE `active` = '1' 
						 AND `visibility` = {$ir['guild']}");
        echo "<select name='poll' class='form-control' type='dropdown'>";
        while ($r = $db->fetch_row($q)) {
            echo "<option value='{$r['id']}'>Poll ID: {$r['id']} - {$r['question']}</option>";
        }
        $db->free_result($q);
        echo "</select>" . $csrf . "
			<br /><input type='submit' class='btn btn-primary' value='End Poll' />
		</form>
   		<a href='?action=staff&act2=idx'>Go Back</a>";
    } else {
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_endpoll', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "We have blocked this action for your security. Please fill out the form quickly next time.");
            die($h->endpage());
        }
        $q = $db->query("/*qc=on*/SELECT COUNT(`id`) FROM `polls` WHERE `id` = {$_POST['poll']} AND `visibility` = {$ir['guild']}");
        if ($db->fetch_single($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "This poll does not exist, and thus, cannot be ended.");
            die($h->endpage());
        }
        $db->free_result($q);
        $db->query("UPDATE `polls` SET `active` = '0' WHERE `id` = {$_POST['poll']}");
        alert('success', "Success!", "You have closed this poll to responses.", true, 'index.php');
        $q = $db->query("/*qc=on*/SELECT `userid`, `username` FROM `users` WHERE `guild` = {$ir['guild']}");
        while ($r = $db->fetch_row($q)) {
            notification_add($r['userid'], "Your guild has closed a poll. View the results <a href='viewguild.php?action=viewpolls'>here</a>.");
        }
        die($h->endpage());
    }
}

function staff_apps()
{
    global $db, $userid, $ir, $gd, $api, $h;
    $_POST['app'] = (isset($_POST['app']) && is_numeric($_POST['app'])) ? abs(intval($_POST['app'])) : '';
    $what = (isset($_POST['what']) && in_array($_POST['what'], array('accept', 'decline'), true)) ? $_POST['what'] : '';
    if (!empty($_POST['app']) && !empty($what)) {

        //Verify that the CSRF check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_apps", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }

        //Verify the application exists and belongs to this guild.
        $aq = $db->query("/*qc=on*/SELECT `ga_user`
                         FROM `guild_applications`
                         WHERE `ga_id` = {$_POST['app']}
                         AND `ga_guild` = {$gd['guild_id']}");

        //Application does exist and belong to this guild.
        if ($db->num_rows($aq) > 0) {
            $appdata = $db->fetch_row($aq);

            //User declines the application. Delete the application, and alert the applicant they were declined.
            if ($what == 'decline') {
                $db->query("DELETE FROM `guild_applications` WHERE `ga_id` = {$_POST['app']}");
                $api->GameAddNotification($appdata['ga_user'], "We regret to inform you that your application to join the {$gd['guild_name']} guild was declined.");
                $event = "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has declined <a href='profile.php?user={$appdata['ga_user']}'> " . $api->SystemUserIDtoName($appdata['ga_user']) . "</a>'s  application to join the guild.";
                //Add to guild notifications.
                $api->GuildAddNotification($gd['guild_id'], $event);
                alert('success', "Success!", "You have denied " . $api->SystemUserIDtoName($appdata['ga_user']) . "'s application to join the guild.");
            } else {
                //User is accepted, yay!

                //Make sure the guild has enough capacity to accept this member.
                $cnt = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `guild` = {$gd['guild_id']}");
				$gd['guild_capacity']=$gd['guild_level']*5;
                //Guild does not have enough capacity to accept another member.s
                if ($gd['guild_capacity'] <= $db->fetch_single($cnt)) {
                    $db->free_result($cnt);
                    alert('danger', "Uh Oh!", "Your guild does not have the capacity for another member. Please level up your guild.");
                    die($h->endpage());

                    //Applicant has joined another guild. =/
                } else if ($api->UserInfoGet($appdata['ga_user'], 'guild') != 0) {
                    $db->free_result($cnt);
                    alert('danger', "Uh Oh!", "The applicant has already joined another guild.");
                    die($h->endpage());
                }

                //Select the town level if the guild's got one.
                $townlevel = $db->fetch_single($db->query("/*qc=on*/SELECT `town_min_level` FROM `town` WHERE `town_guild_owner` = {$gd['guild_id']}"));

                //Applicant cannot reach the town the guild owns.
                if ($townlevel > $api->UserInfoGet($appdata['ga_user'], 'level') && $townlevel > 0) {
                    alert('danger', "Uh Oh!", "The applicant cannot reach your guild's town because their level is too low.");
                    die($h->endpage());
                }
                $db->free_result($cnt);

                //Delete the application and put the applicant inside the guild! Woo!
                $db->query("DELETE FROM `guild_applications` WHERE `ga_id` = {$_POST['app']}");
                $api->GameAddNotification($appdata['ga_user'], "Your application to join the {$gd['guild_name']} guild was accepted.");
                $event = "<a href='profile.php?user={$userid}'>{$ir['username']}</a> 
									has accepted <a href='profile.php?user={$appdata['ga_user']}'>
									" . $api->SystemUserIDtoName($appdata['ga_user']) . "</a>'s 
									application to join the guild.";
                $api->GuildAddNotification($gd['guild_id'], $event);
				if (!empty($gd['guild_intromsg']))
				{
					$api->GameAddMail($appdata['ga_user'],"Your New Guild",$gd['guild_intromsg'],$userid);
				}
                $db->query("UPDATE `users` SET `guild` = {$gd['guild_id']} WHERE `userid` = {$appdata['ga_user']}");
                alert('success', "Success!", "You have accepted " . $api->SystemUserIDtoName($appdata['ga_user']) . "'s applicantion to join the guild.");
            }
        } else {
            alert('danger', "Uh Oh!", "You are trying to accept a non-existent application.");
        }
        $db->free_result($aq);
    } else {
        echo "
        <b>Application Management</b>
        <br />
        <table class='table table-bordered table-striped'>
        		<tr align='left'>
        			<th>Filing Time</th>
        			<th>Applicant</th>
					<th>Level</th>
        			<th>Application</th>
        			<th>Actions</th>
        		</tr>
   		";
        $q =
            $db->query(
                "/*qc=on*/SELECT *
                         FROM `guild_applications`
                         WHERE `ga_guild` = {$gd['guild_id']}
						 ORDER BY `ga_time` DESC");
        $csrf = request_csrf_html('guild_staff_apps');
        while ($r = $db->fetch_row($q)) {
            $r['ga_text'] = htmlentities($r['ga_text'], ENT_QUOTES, 'ISO-8859-1', false);
            echo "
            <tr align='left'>
            	<td>
					" . DateTime_Parse($r['ga_time']) . "
            	</td>
            	<td>
					<a href='profile.php?user={$r['ga_user']}'>" . $api->SystemUserIDtoName($r['ga_user']) . "</a>
            		[{$r['ga_user']}]
				</td>
            	<td>
					" . $api->UserInfoGet($r['ga_user'], 'level') . "
				</td>
				<td>
					{$r['ga_text']}
				</td>
            	<td>
            		<form action='?action=staff&act2=apps' method='post'>
            			<input type='hidden' name='app' value='{$r['ga_id']}' />
            			<input type='hidden' name='what' value='accept' />
            			{$csrf}
            			<input class='btn btn-success' type='submit' value='Accept' />
            		</form>
					<br />
            		<form action='?action=staff&act2=apps' method='post'>
            			<input type='hidden' name='app' value='{$r['ga_id']}' />
            			<input type='hidden' name='what' value='decline' />
            			{$csrf}
            			<input class='btn btn-danger' type='submit' value='Decline' />
            		</form>
            	</td>
            </tr>
               ";
        }
        echo "</table>
		<a href='?action=staff&act2=idx'>Go Back</a>";
    }
}

function staff_vault()
{
    global $db, $userid, $gd, $api, $h, $wq;
	if ($gd['guild_primcurr'] < 0)
	{
		alert('danger',"Uh Oh!","You cannot take money from your guild's vault until your debt is paid off.",true,"?action=staff&act2=idx");
		die($h->endpage());
	}
    if (isset($_POST['primary']) || isset($_POST['secondary'])) {

        //Verify CSRF check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_vault", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }
        //Make sure the POST is safe to work with.
        $_POST['primary'] = (isset($_POST['primary']) && is_numeric($_POST['primary'])) ? abs($_POST['primary']) : 0;
        $_POST['secondary'] = (isset($_POST['secondary']) && is_numeric($_POST['secondary'])) ? abs($_POST['secondary']) : 0;
        $_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;

        //Attempting to give more Copper Coins than the guild currently has.
        if ($_POST['primary'] > $gd['guild_primcurr']) {
            alert('danger', "Uh Oh!", "You are trying to give out more Copper Coins than your guild has in its vault.");
            die($h->endpage());
        }

        //Attempting to give more Chivalry Tokens than the guild currently has.
        if ($_POST['secondary'] > $gd['guild_seccurr']) {
            alert('danger', "Uh Oh!", "You are trying to give out more Chivalry Tokens than your guild has in its vault.");
            die($h->endpage());
        }

        //Didn't fill out how much currency they wanted to give out.
        if ($_POST['primary'] == 0 && $_POST['secondary'] == 0) {
            alert('danger', "Uh Oh!", "Please fill out the form completely before submitting.");
            die($h->endpage());
        }

        //Recipient is on the same IP Address as the sender... stop.
        if ($api->SystemCheckUsersIPs($userid, $_POST['user'])) {
            alert('danger', "Uh Oh!", "You cannot give from the guild's vault if you share the same IP address as the recipient.");
            die($h->endpage());
        }

        //Check that the user to receive the cash is in the guild and/or exists.
        $q = $db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `userid` = {$_POST['user']} AND `guild` = {$gd['guild_id']}");
        if ($db->num_rows($q) == 0) {
            alert('danger', "Uh Oh!", "You are trying to give to a user that does not exist, or is not in the guild.");
            die($h->endpage());
        }

        //Do not allow the transaction to continue if at war.
        if ($db->fetch_single($wq) > 0) {
            alert('danger', "Uh Oh!", "You cannot withdraw from your guild's vault while at war.");
            die($h->endpage());
        }
        $db->free_result($q);
        //Give the currency and log everything.
        $api->UserGiveCurrency($_POST['user'], 'primary', $_POST['primary']);
        $api->UserGiveCurrency($_POST['user'], 'secondary', $_POST['secondary']);
        $db->query("UPDATE `guild` SET `guild_primcurr` = `guild_primcurr` - {$_POST['primary']},
                      `guild_seccurr` = `guild_seccurr` - {$_POST['secondary']} WHERE `guild_id` = {$gd['guild_id']}");
        $api->GameAddNotification($_POST['user'], "You were given " . number_format($_POST['primary']) . " Primary
            Currency and/or " . number_format($_POST['secondary']) . " Chivalry Tokens from your guild's vault.");
        $api->GuildAddNotification($gd['guild_id'], "<a href='profile.php?user={$userid}'>
            {$api->SystemUserIDtoName($userid)}</a> has given <a href='profile.php?user={$_POST['user']}'>
            {$api->SystemUserIDtoName($_POST['user'])}</a> " . number_format($_POST['primary']) . "
            Copper Coins and/or " . number_format($_POST['secondary']) . " Chivalry Tokens from the guild's
            vault.");
        alert('success', "Success!", "You have given {$api->SystemUserIDtoName($_POST['user'])} ", true, '?action=staff&act2=idx');
        $api->SystemLogsAdd($userid, "guild_vault", "Gave <a href='profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a> " . number_format($_POST['primary']) . " Copper Coins and/or " . number_format($_POST['secondary']) . " Chivalry Tokens from their guild's vault.");
    } else {
        $csrf = request_csrf_html('guild_staff_vault');
        echo "<form method='post'>
        <table class='table table-bordered'>
            <tr>
                <th colspan='2'>
                    You may give out currency from your guild's vault. Your vault currently has " . number_format($gd['guild_primcurr']) . " Copper Coins and
                    " . number_format($gd['guild_seccurr']) . " Chivalry Tokens.
                </th>
            </tr>
            <tr>
                <th>
                    User
                </th>
                <td>
                    " . guild_user_dropdown('user', $gd['guild_id']) . "
                </td>
            </tr>
            <tr>
                <th>
                    Copper Coins
                </th>
                <td>
                    <input type='number' class='form-control' min='0' max='{$gd['guild_primcurr']}' name='primary'>
                </td>
            </tr>
            <tr>
                <th>
                    Chivalry Tokens
                </th>
                <td>
                    <input type='number' class='form-control' min='0' max='{$gd['guild_seccurr']}' name='secondary'>
                </td>
            </tr>
            <tr>
                <td colspan='2'>
                    <input type='submit' class='btn btn-primary' value='Give'>
                </td>
            </tr>
            {$csrf}
        </table>
        </form>
		<a href='?action=staff&act2=idx'>Go Back</a>";
    }

}

function staff_coowner()
{
    global $db, $userid, $api, $h, $gd;
    if (isset($_POST['user'])) {

        //Verify CSRF check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_coleader", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }

        //Make sure POST is safe to work with.
        $_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;

        //Verify the user chosen is existent and is in the guild.
        $q = $db->query("/*qc=on*/SELECT `userid`, `username` FROM `users` WHERE `userid` = {$_POST['user']} AND `guild` = {$gd['guild_id']}");
        if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "You cannot give co-leadership abilities to someone that does not exist, or is not
			    in the guild.");
            die($h->endpage());
        }
        $db->free_result($q);

        //Update the guild's leader.
        $db->query("UPDATE `guild` SET `guild_coowner` = {$_POST['user']} WHERE `guild_id` = {$gd['guild_id']}");
        $api->GameAddNotification($_POST['user'], "<a href='profile.php?user={$userid}'>{$api->SystemUserIDtoName($userid)}</a> has transferred you co-leader privileges for the {$gd['guild_name']} guild.");
        $api->GuildAddNotification($gd['guild_id'], "<a href='profile.php?user={$userid}'>{$api->SystemUserIDtoName($userid)}</a> has transferred co-leader privileges to <a href='profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a>.");
        alert('success', "Success!", "You have successfully transferred co-leadership privileges to {$api->SystemUserIDtoName($_POST['user'])}.", true, '?action=staff&act2=idx');
    } else {
        $csrf = request_csrf_html('guild_staff_coleader');
        echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Select the user you wish to give your co-leadership privileges to.
				</th>
			</tr>
			<tr>
				<th>
					User
				</th>
				<td>
					" . guild_user_dropdown('user', $gd['guild_id'], $gd['guild_coowner']) . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Transfer Co-Leader'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>
		<a href='?action=staff&act2=idx'>Go Back</a>";
    }
}

function staff_announcement()
{
    global $gd, $db, $h;
    if (isset($_POST['ament'])) {

        //Verify CSRF check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_ament", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }

        //Make sure the POST is safe to work with.
        $ament = $db->escape(nl2br(htmlentities(stripslashes($_POST['ament']), ENT_QUOTES, 'ISO-8859-1')));

        //Update the guild's announcement.
        $db->query("UPDATE `guild` SET `guild_announcement` = '{$ament}' WHERE `guild_id` = {$gd['guild_id']}");
        alert('success', "Success!", "You have updated your guild's announcement.", true, '?action=staff&act2=idx');
    } else {
        //Escape the announcement for safety reasons.
        $am_for_area = strip_tags($gd['guild_announcement']);
        $csrf = request_csrf_html('guild_staff_ament');
        echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					You may change your guild's announcement here.
				</th>
			</tr>
			<tr>
				<th>
					Announcement
				</th>
				<td>
					<textarea class='form-control' name='ament'>{$am_for_area}</textarea>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='Change Announcement' class='btn btn-primary'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>
		<a href='?action=staff&act2=idx'>Go Back</a>";
    }
}

function staff_massmail()
{
    global $db, $api, $userid, $h, $gd;
    if (isset($_POST['text'])) {

        //Verify the CSRF check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_massmail", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }

        //Escape the message.
        $_POST['text'] = (isset($_POST['text'])) ? $db->escape(htmlentities(stripslashes($_POST['text']), ENT_QUOTES, 'ISO-8859-1')) : '';
        $subj = 'Guild Mass Mail';
        $q = $db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `guild` = {$gd['guild_id']}");
        //Send the mail out to everyone in the guild.
        while ($r = $db->fetch_row($q)) {
            $api->GameAddMail($r['userid'], $subj, $_POST['text'], $userid);
        }
        alert('success', "Success!", "Mass mail has been sent successfully.", true, '?action=staff&act2=idx');
    } else {
        $csrf = request_csrf_html('guild_staff_massmail');
        echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Use this form to send a mass mail to each member of your guild.
				</th>
			</tr>
			<tr>
				<th>
					Message
				</th>
				<td>
					<textarea class='form-control' name='text' rows='7'></textarea>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='Mass Mail' class='btn btn-primary'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>
		<a href='?action=staff&act2=idx'>Go Back</a>";
    }
}

function staff_masspayment()
{
    global $db, $api, $userid, $gd, $h, $wq;
	if ($gd['guild_primcurr'] < 0)
	{
		alert('danger',"Uh Oh!","You cannot take money from your guild's vault until your debt is paid off.",true,"?action=staff&act2=idx");
		die($h->endpage());
	}
    if (isset($_POST['payment'])) {

        //Verify the CSRF check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_masspay", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }

        //Make sure the POST is safe to work with.
        $_POST['payment'] = (isset($_POST['payment']) && is_numeric($_POST['payment'])) ? abs($_POST['payment']) : 0;
        $cnt = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `guild` = {$gd['guild_id']}"));

        //Make sure there's enough Copper Coins to pay each member of the guild the amount specified.
        if (($_POST['payment'] * $cnt) > $gd['guild_primcurr']) {
            alert('danger', "Uh Oh!", "You do not have enough currency in your vault to give out that much to each member.");
            die($h->endpage());

            //Check that the guild is not at war.
        } else if ($db->fetch_single($wq) > 0) {
            alert('danger', "Uh Oh!", "You cannot mass pay your guild while at war.");
            die($h->endpage());
        } else {
            $q = $db->query("/*qc=on*/SELECT `userid`, `username` FROM `users` WHERE `guild` = {$gd['guild_id']}");
            //Pay each member.
            while ($r = $db->fetch_row($q)) {
                //User shares an IP with the user being paid... stop this.
                if ($api->SystemCheckUsersIPs($userid, $r['userid'])) {
                    alert('danger', "Uh Oh!", "{$r['username']} could not receive their Mass Payment because they share an IP Address with you.");
                } else {
                    //Pay everyone.
                    $gd['guild_primcurr'] -= $_POST['payment'];
                    $api->GameAddNotification($r['userid'], "You were given a mass-payment of {$_POST['payment']} Copper Coins from your guild.");
                    $api->UserGiveCurrency($r['userid'], 'primary', $_POST['payment']);
                    alert('success', "Success!", "{$r['username']} was paid {$_POST['payment']} Copper Coins.");
                }
            }
            //Notify the user of the success and log everything.
            $db->query("UPDATE `guild` SET `guild_primcurr` = {$gd['guild_primcurr']} WHERE `guild_id` = {$gd['guild_id']}");
            $notif = $db->escape("A mass payment of " . number_format($_POST['payment']) . " Copper Coins was sent out to the members of the guild.");
            $api->GuildAddNotification($gd['guild_id'], $notif);
            $api->SystemLogsAdd($userid, 'guilds', "Sent a mass payment of " . number_format($_POST['payment']) . "to their guild.");
            alert('success', "Success!", "Mass payment complete.", true, '?action=staff&act2=idx');
        }
    } else {
        $csrf = request_csrf_html('guild_staff_masspay');
        echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					You can pay each and every member of your guild using this form
				</th>
			</tr>
			<tr>
				<th>
					Payment
				</th>
				<td>
					<input type='number' min='1' max='{$gd['guild_primcurr']}' class='form-control' name='payment'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='Mass Pay' class='btn btn-primary'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>
		<a href='?action=staff&act2=idx'>Go Back</a>";
    }
}

function staff_desc()
{
    global $gd, $db, $userid, $h;
    //Verify the current user is the guild owner.
    if ($userid == $gd['guild_owner']) {
        if (isset($_POST['desc'])) {

            //Verify CSRF check has passed.
            if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_desc", stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
                die($h->endpage());
            }

            //Make sure the POST is safe to work with.
            $desc = $db->escape(nl2br(htmlentities(stripslashes($_POST['desc']), ENT_QUOTES, 'ISO-8859-1')));

            //Update guild's description.
            $db->query("UPDATE `guild` SET `guild_desc` = '{$desc}' WHERE `guild_id` = {$gd['guild_id']}");
            alert('success', "Success!", "You have updated your guild's description", true, '?action=staff&act2=idx');
        } else {
            //Escape the description for safety reasons.
            $am_for_area = strip_tags($gd['guild_desc']);
            $csrf = request_csrf_html('guild_staff_desc');
            echo "<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						You can use this form to change your guild's description.
					</th>
				</tr>
				<tr>
					<th>
						Description
					</th>
					<td>
						<textarea class='form-control' name='desc' rows='7'>{$am_for_area}</textarea>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' value='Update Description' class='btn btn-primary'>
					</td>
				</tr>
				{$csrf}
			</table>
			</form>
			<a href='?action=staff&act2=idx'>Go Back</a>";
        }
    } else {
        alert('danger', "Uh Oh!", "You can only be here if you're the guild's leader.");
    }
}

function staff_leader()
{
    global $gd, $db, $userid, $api, $h;
    //Verify the current user is the guild owner.
    if ($userid == $gd['guild_owner']) {
        if (isset($_POST['user'])) {

            //Verify CSRF check has passed.
            if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_leader", stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
                die($h->endpage());
            }

            //Make the POST safe to work with it.
            $_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;

            //Select the user from database.
            $q = $db->query("/*qc=on*/SELECT `userid`, `username` FROM `users` WHERE `userid` = {$_POST['user']} AND `guild` = {$gd['guild_id']}");

            //User does not exist, or is not in the guild.
            if ($db->num_rows($q) == 0) {
                $db->free_result($q);
                alert('danger', "Uh Oh!", "You cannot give leadership privileges to a user not in your guild, or that doesn't exist.");
                die($h->endpage());
            }
            $db->free_result($q);

            //Update the guild's leader and log everything.
            $db->query("UPDATE `guild` SET `guild_owner` = {$_POST['user']} WHERE `guild_id` = {$gd['guild_id']}");
            $api->GameAddNotification($_POST['user'], "<a href='profile.php?user={$userid}'>{$api->SystemUserIDtoName($userid)}</a> has transferred you leader privileges for the {$gd['guild_name']} guild.");
            $api->GuildAddNotification($gd['guild_id'], "<a href='profile.php?user={$userid}'>{$api->SystemUserIDtoName($userid)}</a> has transferred leader privileges to <a href='profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a>.");
            alert('success', "Success!", "You have transferred your leadership privileges over to {$api->SystemUserIDtoName($_POST['user'])}.", true, '?action=staff&act2=idx');
        } else {
            $csrf = request_csrf_html('guild_staff_leader');
            echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Select a user to give them your leadership privileges.
				</th>
			</tr>
			<tr>
				<th>
					User
				</th>
				<td>
					" . guild_user_dropdown('user', $gd['guild_id'], $gd['guild_owner']) . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Transfer Leader'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>
		<a href='?action=staff&act2=idx'>Go Back</a>";
        }
    } else {
        alert('danger', "Uh Oh!", "You can only be here if you're the guild's leader.", true, '?action=staff&act2=idx');
    }
}

function staff_name()
{
    global $gd, $db, $userid, $h;

    //Verify the current user is the guild owner.
    if ($userid == $gd['guild_owner']) {
        if (isset($_POST['name'])) {

            //Check that the CSRF check has passed.
            if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_name", stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
                die($h->endpage());
            }
            //Make sure the POST is safe to work with.
            $name = $db->escape(nl2br(htmlentities(stripslashes($_POST['name']), ENT_QUOTES, 'ISO-8859-1')));

            //Select guilds with the same name.
            $cnt = $db->query("/*qc=on*/SELECT `guild_id` FROM `guild` WHERE `guild_name` = '{$name}' AND `guild_id` != {$gd['guild_id']}");

            //If there's a guild with the same name, disallow the name change.
            if ($db->num_rows($cnt) > 0) {
                alert('danger', "Uh Oh!", "The name you have chosen is already in use by another guild.");
                die($h->endpage());
            }

            //Update the guild's name.
            $db->query("UPDATE `guild` SET `guild_name` = '{$name}' WHERE `guild_id` = {$gd['guild_id']}");
            alert('success', "Success!", "You have changed your guild's name to {$name}.", true, '?action=staff&act2=idx');
        } else {
            $am_for_area = strip_tags($gd['guild_name']);
            $csrf = request_csrf_html('guild_staff_name');
            echo "<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						You can change your guild's name here.
					</th>
				</tr>
				<tr>
					<th>
						Name
					</th>
					<td>
						<input type='text' required='1' value='{$am_for_area}' class='form-control' name='name'>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' value='Change Guild Name' class='btn btn-primary'>
					</td>
				</tr>
				{$csrf}
			</table>
			</form>
			<a href='?action=staff&act2=idx'>Go Back</a>";
        }
    } else {
        alert('danger', "Uh Oh!", "You can only be here if you're the guild's leader.", true, '?action=staff&act2=idx');
    }
}

function staff_town()
{
    global $db, $gd, $api, $h, $userid, $wq;

    //Verify current user is the guild owner.
    if ($userid == $gd['guild_owner']) {
        if (isset($_POST['town'])) {

            //Verify CSRF
            if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_town", stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
                die($h->endpage());
            }
            //Make sure POST is safe to work with.
            $town = (isset($_POST['town']) && is_numeric($_POST['town'])) ? abs($_POST['town']) : 0;

            //Make sure current guild doesn't already have a town.
            $cnt = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`town_id`) FROM `town` WHERE `town_guild_owner` = {$gd['guild_id']}"));
            if ($cnt > 0) {
                alert('danger', "Uh Oh!", "Your guild already owns a town. Surrender your current town to own a new one.");
                die($h->endpage());
            }

            //Make sure town claimed exists.
            if ($db->num_rows($db->query("/*qc=on*/SELECT `town_id` FROM `town` WHERE `town_id` = {$town}")) == 0) {
                alert('danger', "Uh Oh!", "The town you wish to own does not exist.");
                die($h->endpage());
            }

            //Check to see if the town is unowned.
            if ($db->fetch_single($db->query("/*qc=on*/SELECT `town_guild_owner` FROM `town` WHERE `town_id` = {$town}")) > 0) {
                alert('danger', "Uh Oh!", "The town you wish to own is already owned by another guild. If you want this town, declare war on them!");
                die($h->endpage());
            }

            //Check to see if current guild is at war, if so, stop them.
            if ($db->fetch_single($wq) > 0) {
                alert('danger', "Uh Oh!", "You may not change your guild's town while at war.");
                die($h->endpage());
            }
            $lowestlevel = $db->fetch_single($db->query("/*qc=on*/SELECT `level` FROM `users` WHERE `guild` = {$gd['guild_id']} ORDER BY `level` ASC LIMIT 1"));
            $townlevel = $db->fetch_single($db->query("/*qc=on*/SELECT `town_min_level` FROM `town` WHERE `town_id` = {$town}"));

            //Verify that everyone in the guild can reach the city.
            if ($townlevel > $lowestlevel) {
                alert('danger', "Uh Oh!", "You cannot own this town as there are members in your guild who cannot access it.");
                die($h->endpage());
            }

            //Update everything. City is now the guild's.
            $db->query("UPDATE `town` SET `town_guild_owner` = {$gd['guild_id']} WHERE `town_id` = {$town}");
            $api->GuildAddNotification($gd['guild_id'], "Your guild has successfully claimed {$api->SystemTownIDtoName($town)}.");
            alert('success', "Success!", "You have successfully claimed {$api->SystemTownIDtoName($town)} for your guild.", true, '?action=staff&act2=idx');
        } else {
            $csrf = request_csrf_html('guild_staff_town');
            echo "
			<form method='post'>
				<table class='table table-bordered'>
					<tr>
						<th colspan='2'>
							You can claim a town for your guild here. This town must be unowned, and must be accessible
							to all your guild members. If it is currently owned, you must declare war on the owning
							guild to get a chance to claim the town as yours.
						</th>
					</tr>
					<tr>
						<th>
							Town
						</th>
						<td>
							" . location_dropdown('town') . "
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<input type='submit' value='Claim Town' class='btn btn-primary'>
						</td>
					</tr>
					{$csrf}
				</table>
			</form>
			<a href='?action=staff&act2=idx'>Go Back</a>";
        }
    } else {
        alert('danger', "Uh Oh!", "You can only be here if you're the guild's leader.", true, '?action=staff&act2=idx');
    }
}

function staff_untown()
{
    global $db, $gd, $api, $h, $userid, $wq;
    //Verify current user is the guild's owner.
    if ($userid == $gd['guild_owner']) {

        //Check to be sure the guild has a town under their control
        $townowned = $db->query("/*qc=on*/SELECT `town_id` FROM `town` WHERE `town_guild_owner` = {$gd['guild_id']}");
        if ($db->num_rows($townowned) == 0) {
            alert('danger', "Uh Oh!", "Your guild doesn't have a town to surrender.", true, '?action=staff&act2=idx');
            die($h->endpage());

            //Check that the guild is not at war.
        } else if ($db->fetch_single($wq) > 0) {
            alert('danger', "Uh Oh!", "You cannot surrender your town while at war.", true, '?action=staff&act2=idx');
            die($h->endpage());
        } elseif (isset($_POST['confirm'])) {

            //Surrender the town.
            $r = $db->fetch_single($townowned);
            alert('success', "Success!", "You have surrendered your guild's town.", true, '?action=staff&act2=idx');
            $db->query("UPDATE `town`
                        SET `town_guild_owner` = 0, `town_tax` = 0 
                        WHERE `town_id` = {$r}");
            $api->GuildAddNotification($gd['guild_id'], "Your guild has willingly given up their town.");
            $api->SystemLogsAdd($userid, 'guilds', "Willingly surrendered {$gd['guild_name']}'s town, {$api->SystemTownIDtoName($r)}.");
        } else {
            echo "Are you sure you wish to surrender your guild's town? This is not reversible.<br />
			<form method='post'>
				<input type='hidden' name='confirm' value='yes'>
				<input type='submit' class='btn btn-success' value='Yes'>
			</form>
			<a href='?action=staff&act2=idx'>Go Back</a>";
        }
    } else {
        alert('danger', "Uh Oh!", "You can only be here if you're the guild's leader.", true, '?action=staff&act2=idx');
    }
}

function staff_declare()
{
    global $db, $gd, $api, $h, $userid, $ir;
    //Verify current user is the guild owner.
    if ($userid == $gd['guild_owner']) {
        if (isset($_POST['guild'])) {

            //Verify POST is safe to work with.
            $_POST['guild'] = (isset($_POST['guild']) && is_numeric($_POST['guild'])) ? abs($_POST['guild']) : 0;

            //Verify CSRF check has passed.
            if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_declarewar", stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
                die($h->endpage());
            }
			
			if ($gd['guild_primcurr'] < 500000)
			{
				alert('danger',"Uh Oh!","Your guild does not have enough Copper Coins to declare war.");
				die($h->endpage());
			}

            //Check if the declared guild is the current guild, and stop them if that's the case.
            if ($_POST['guild'] == $gd['guild_id']) {
                alert('danger', "Uh Oh!", "You cannot declare war on your own guild.");
                die($h->endpage());
            }
			
			if ($_POST['guild'] == 1) {
				alert('danger', "Uh Oh!", "You cannot declare war on the admin guild.");
				die($h->endpage());
			}

            //Verify that the declared guild exists.
            $data_q = $db->query("/*qc=on*/SELECT `guild_name`,`guild_owner`
                                  FROM `guild`
                                  WHERE `guild_id` = {$_POST['guild']}");
            if ($db->num_rows($data_q) == 0) {
                $db->free_result($data_q);
                alert('danger', "Uh Oh!", "You cannot declare war on a non-existent guild.");
                die($h->endpage());
            }

            //Make sure the two guilds are not at war already.
            $time = time();
            $iswarredon = $db->query("/*qc=on*/SELECT `gw_id`
                                      FROM `guild_wars`
                                      WHERE `gw_declarer` = {$gd['guild_id']}
                                      AND `gw_declaree` = {$_POST['guild']}
                                      AND `gw_end` > {$time}");
            if ($db->num_rows($iswarredon) > 0) {
                alert('danger', "Uh Oh!", "You cannot declare war on this guild as you are already at war!");
                die($h->endpage());
            }

            //Make sure the two guilds are not at war already.
            $iswarredon1 = $db->query("/*qc=on*/SELECT `gw_id`
                                        FROM `guild_wars`
                                        WHERE `gw_declaree` = {$gd['guild_id']}
                                        AND `gw_declarer` = {$_POST['guild']}
                                        AND `gw_end` > {$time}");
            if ($db->num_rows($iswarredon1) > 0) {
                alert('danger', "Uh Oh!", "You cannot declare war on this guild as you are already at war!");
                die($h->endpage());
            }

            //Check to see if its been a week since the last war.
            $lastweek = $time - 604800;
            $istoosoon = $db->fetch_single($db->query("/*qc=on*/SELECT `gw_end`
                                                        FROM `guild_wars`
                                                        WHERE `gw_declarer` = {$gd['guild_id']}
                                                        AND `gw_declaree` = {$_POST['guild']}
                                                        ORDER BY `gw_id`DESC
                                                        LIMIT 1"));
            if ($istoosoon > $lastweek) {
                alert('danger', "Uh Oh!", "You cannot declare war on this guild as its been less than a week since the last war concluded.");
                die($h->endpage());
            }

            //Check to see if its been a week since the last war.
            $istoosoon1 = $db->fetch_single($db->query("/*qc=on*/SELECT `gw_end`
                                                        FROM `guild_wars`
                                                        WHERE `gw_declaree` = {$gd['guild_id']}
                                                        AND `gw_declarer` = {$_POST['guild']}
                                                        ORDER BY `gw_id` DESC
                                                        LIMIT 1"));
            if ($istoosoon1 > $lastweek) {
                alert('danger', "Uh Oh!", "You cannot declare war on this guild as its been less than a week since the last war concluded.");
                die($h->endpage());
            }
            $yourcount = $db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `guild` = {$ir['guild']}");
            $theircount = $db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `guild` = {$_POST['guild']}");

            //Current guild does not have 5 members.
            if ($db->num_rows($yourcount) < 5) {
                alert('danger', "Uh Oh!", "You cannot declare war on another guild if you've got less than 5 members in your own guild.");
                die($h->endpage());
            }

            //Current guild does not have 5 members.
            if ($db->num_rows($theircount) < 5) {
                alert('danger', "Uh Oh!", "You cannot declare war on this guild, as they do not have 5 members currently in their guild.");
                die($h->endpage());
            }
			
			//Are you guys allies?
			$cfaq=$db->query("/*qc=on*/SELECT * 
								FROM `guild_alliances` 
								WHERE 
								(`alliance_a` = {$ir['guild']} AND `alliance_b` = {$_POST['guild']}) 
								OR 
								(`alliance_b` = {$ir['guild']} AND `alliance_a` = {$_POST['guild']})");
			if ($db->num_rows($cfaq) != 0)
			{
				alert('danger',"Uh Oh!","You cannot declare war on an allied guild.");
				die($h->endpage());
			}
            $r = $db->fetch_row($data_q);
            $endtime = time() + 259200;

            //Start the war, and notify all parties involved.
            $db->query("INSERT INTO `guild_wars` VALUES (NULL, {$gd['guild_id']}, {$_POST['guild']}, 0, 0, {$endtime}, 0)");
            $api->GameAddNotification($r['guild_owner'], "The {$gd['guild_name']} guild has declared war on your guild.");
            $api->GuildAddNotification($_POST['guild'], "The {$gd['guild_name']} guild has declared war on your guild.");
            $api->GuildAddNotification($gd['guild_id'], "Your guild has declared war on {$r['guild_name']}.");
			
			$allyq=$db->query("/*qc=on*/SELECT * FROM `guild_alliances` WHERE (`alliance_a` = {$ir['guild']} OR `alliance_b` = {$ir['guild']})");
			while ($ar=$db->fetch_row($allyq))
			{
				if ($ar['alliance_a'] == $ir['guild'])
					$otheralliance=$ar['alliance_b'];
				else
					$otheralliance=$ar['alliance_a'];
				if ($ar['alliance_type'] == 2)
				{
					$api->GuildAddNotification($ir['guild'],"Your guild has broken the alliance with {$api->GuildFetchInfo($otheralliance)} by declaring war.");
					$api->GuildAddNotification($otheralliance,"{$gd['guild_name']} has broken the alliance with your guild by declaring war.");
					$db->query("DELETE FROM `guild_alliances` WHERE `alliance_id` = {$ar['alliance_id']}");
				}
				else
				{
					$api->GuildAddNotification($otheralliance,"{$gd['guild_name']} has declared war on {$r['guild_name']}, bringing in your guild to help fight.");
					$api->GameAddNotification($r['guild_owner'], "The {$api->GuildFetchInfo($otheralliance)} guild has declared war on your guild.");
					$api->GuildAddNotification($_POST['guild'], "The {$api->GuildFetchInfo($otheralliance)} guild has declared war on your guild.");
					$db->query("INSERT INTO `guild_wars` VALUES (NULL, {$otheralliance}, {$_POST['guild']}, 0, 0, {$endtime}, 0)");
				}
			}
			
			$allyq2=$db->query("/*qc=on*/SELECT * FROM `guild_alliances` WHERE (`alliance_a` = {$_POST['guild']} OR `alliance_b` = {$_POST['guild']})");
			while ($ar=$db->fetch_row($allyq2))
			{
				if ($ar['alliance_a'] == $_POST['guild'])
					$otheralliance=$ar['alliance_b'];
				else
					$otheralliance=$ar['alliance_a'];
				$api->GuildAddNotification($otheralliance,"{$r['guild_name']} had war declared upon them by {$gd['guild_name']}, bringing in your guild to help fight.");
				$api->GameAddNotification($userid, "The {$api->GuildFetchInfo($otheralliance)} guild has declared war on your guild.");
				$api->GuildAddNotification($ir['guild'], "The {$api->GuildFetchInfo($otheralliance)} guild has declared war on your guild.");
				$db->query("INSERT INTO `guild_wars` VALUES (NULL, {$otheralliance}, {$ir['guild']}, 0, 0, {$endtime}, 0)");
			}
			$db->query("UPDATE `guild` SET `guild_primcurr` = `guild_primcurr` - 500000 WHERE `guild_id` = {$ir['guild']}");
            $api->SystemLogsAdd($userid, 'guilds', "Declared war on {$r['guild_name']} [{$_POST['guild']}]");
            alert('success', "Success!", "You have declared war on {$r['guild_name']}.", true, '?action=staff&act2=idx');
        } else {
            $csrf = request_csrf_html('guild_staff_declarewar');
            echo "
			<table class='table table-bordered'>
			<form method='post'>
				<tr>
					<th colspan='2'>
						It costs 500,000 Copper Coins to declare war on another guild. If you have allies, they will come to your aid. Note, however, 
						if they have allies, they will declare war on your guild to protect their alliance.
					</th>
				</tr>
				<tr>
					<th>
						Guild
					</th>
					<td>
						" . guilds_dropdown() . "
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-primary' value='Declare War'>
					</td>
				</tr>
			{$csrf}
			</form>
			</table>
			<a href='?action=staff&act2=idx'>Go Back</a>";
        }
    } else {
        alert('danger', "Uh Oh!", "You can only be here if you're the guild's leader.", true, '?action=staff&act2=idx');
    }
}

function staff_levelup()
{
    global $db, $gd, $api, $userid;
    //Experience required set to variable
    $xprequired = $gd['xp_needed'];
    if (isset($_POST['do'])) {

        //Guild does not have enough experience to level up.
        if ($gd['guild_xp'] < $xprequired) {
            alert('danger', "Uh Oh!", "Your guild does not have enough experience to level up. You can get more experience by going to war.");
        } else {
            //Level the guild up.
            $db->query("UPDATE `guild` SET `guild_level` = `guild_level` + 1,
			`guild_xp` = `guild_xp` - {$xprequired} WHERE `guild_id` = {$gd['guild_id']}");
            alert('success', "Success!", "You have successfully leveled up your guild.", true, '?action=staff&act2=idx');
            $api->SystemLogsAdd($userid, 'guilds', "Leveled up the {$gd['guild_name']} guild.");
            $api->GuildAddNotification($gd['guild_id'], "Your guild has leveled up!");
        }
    } else {
        echo "You may level up your guild. Your guild will need the minimum required Experience to do this. You may gain
        guild Experience by going to war with another guild and gaining points in war. At your guild's level, your
        guild will need " . number_format($xprequired) . " Guild Experience to level up. Your guild currently has
        {$gd['guild_xp']} Experience. Do you wish to attempt to level up?<br />
		<form method='post'>
			<input type='hidden' name='do' value='yes'>
			<input type='submit' value='Level Up' class='btn btn-success'>
		</form>
		<a href='?action=staff&act2=idx'>Go Back</a>";
    }
}

function staff_tax()
{
    global $db, $gd, $api, $h, $userid;
    //Check if the user is the owner of the guild.
    if ($userid == $gd['guild_owner']) {
        //Guild does not own a town, so tell them so.
        if (!$db->fetch_single($db->query("/*qc=on*/SELECT `town_id` FROM `town` WHERE `town_guild_owner` = {$gd['guild_id']}")) > 0) {
            alert('danger', "Uh Oh!", "Your guild does not own a town to set a tax rate on.", true, '?action=staff&act2=idx');
            die($h->endpage());
        }
        if (isset($_POST['tax'])) {
            //Verify the variables are safe to work with.
            $_POST['tax'] = (isset($_POST['tax']) && is_numeric($_POST['tax'])) ? abs($_POST['tax']) : 0;

            //Verify CSRF check has passed.
            if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_tax", stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
                die($h->endpage());
            }

            //Make sure tax rate is between 0-10%
            if ($_POST['tax'] < 0 || $_POST['tax'] > 10) {
                alert('danger', "Uh Oh!", "You can only set a tax rate between 0% and 10%");
                die($h->endpage());
            }
            //Update town's tax rate.
            $town_id = $db->fetch_single($db->query("/*qc=on*/SELECT `town_id` FROM `town` WHERE `town_guild_owner` = {$gd['guild_id']}"));
            $db->query("UPDATE `town` SET `town_tax` = {$_POST['tax']} WHERE `town_guild_owner` = {$gd['guild_id']}");
            $api->SystemLogsAdd($userid, 'tax', "Set tax rate to {$_POST['tax']}% in {$api->SystemTownIDtoName($town_id)}.");
            alert('success', "Success!", "You have set the tax rate of {$api->SystemTownIDtoName($town_id)} to {$_POST['tax']}%.", true, '?action=staff&act2=idx');
        } else {
            $csrf = request_csrf_html('guild_staff_tax');
            $current_tax = $db->fetch_single($db->query("/*qc=on*/SELECT `town_tax` FROM `town` WHERE `town_guild_owner` = {$gd['guild_id']}"));
            echo "
			<table class='table table-bordered'>
			<form method='post'>
				<tr>
					<th colspan='2'>
						You may change the tax rate for the town your guild owns here. Please do not type the percent sign.
					</th>
				</tr>
				<tr>
					<th>
						Tax Rate (Percent)
					</th>
					<td>
						<input type='number' name='tax' class='form-control' value='{$current_tax}' min='0' max='20' required='1'>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-primary' value='Change Tax'>
					</td>
				</tr>
			{$csrf}
			</form>
			</table>
			<a href='?action=staff&act2=idx'>Go Back</a>";
        }

    } else {
        alert('danger', "Uh Oh!", "You can only be here if you're the guild's leader.", true, '?action=staff&act2=idx');
    }
}

function staff_dissolve()
{
    global $db, $gd, $api, $h, $userid, $ir, $wq;
    //Check if user is the owner of the guild.
    if ($userid == $gd['guild_owner']) {
        if (isset($_POST['do'])) {
            //Verify CSRF check has passed.
            if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_dissolve", stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
                die($h->endpage());
            }
            //Make sure guild is not at war.
            if ($db->fetch_single($wq) > 0) {
                alert('danger', "Uh Oh!", "You cannot dissolve your guild when you are at war.");
                die($h->endpage());
            }

            //Select all guild members, and tell them what happened to their guild via notification.
            $q = $db->query("/*qc=on*/SELECT `userid`,`username` FROM `users` WHERE `guild` = {$ir['guild']}");
            while ($r = $db->fetch_row($q)) {
                $api->GameAddNotification($r['userid'], "Your guild, {$gd['guild_name']}, has been dissolved by <a href='profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}].");
            }
            //Log the guild being deleted.
            $api->SystemLogsAdd($userid, 'guilds', "Dissolved Guild ID {$ir['guild']}");

            //Delete everything.
            $db->query("DELETE FROM `guild_applications` WHERE `ga_guild` = {$ir['guild']}");
            $db->query("DELETE FROM `guild_armory` WHERE `gaGUILD` = {$ir['guild']}");
            $db->query("DELETE FROM `guild_notifications` WHERE `gn_guild` = {$ir['guild']}");
            $db->query("DELETE FROM `guild_wars` WHERE `gw_declarer` = {$ir['guild']}");
            $db->query("DELETE FROM `guild_wars` WHERE `gw_declaree` = {$ir['guild']}");
            $db->query("DELETE FROM `guild` WHERE `guild_id` = {$ir['guild']}");
			$db->query("DELETE FROM `guild_alliances` WHERE `alliance_a` = {$ir['guild']}");
			$db->query("DELETE FROM `guild_alliances` WHERE `alliance_b` = {$ir['guild']}");
            $db->query("UPDATE `town` SET `town_guild_owner` = 0 WHERE `town_guild_owner` = {$ir['guild']}");
            $db->query("UPDATE `users` SET `guild` = 0 WHERE `guild` = {$ir['guild']}");
            alert("success", "Success!", "You have successfully dissolved your guild.", true, 'index.php');
        } else {
            $csrf = request_csrf_html('guild_staff_dissolve');
            echo "Are you sure you wish to dissolve your guild? This action cannot be undone. Everything in the guild's
            armory and vault will be removed from the game entirely.<br />
            <form method='post'>
            {$csrf}
            <input type='hidden' name='do' value='do'>
            <input type='submit' class='btn btn-primary' value='Dissolve Guild'>
            </form>";
        }
    } else {
        alert('danger', "Uh Oh!", "You can only be here if you're the guild's leader.", true, '?action=staff&act2=idx');
    }

}

function staff_armory()
{
    global $db, $gd, $api, $h, $set, $userid, $ir;
    //Check to see if the guild has bought the armory.
    if ($gd['guild_hasarmory'] == 'false') {

        //Set the cost to varaible for ease of use.
        $cost = $set['GUILD_PRICE'] * 4;
        if (isset($_GET['buy'])) {

            //Guild does not have enough Copper Coins to buy the armory.
            if ($gd['guild_primcurr'] < $cost) {
                alert('danger', "Uh Oh!", "Your guild does not have enough Copper Coins to buy an armory.", true, '?action=staff&act2=idx');
                die($h->endpage());
            }
            //Buy the armory and remove the currency.
            $db->query("UPDATE `guild`
                        SET `guild_hasarmory` = 'true',
                        `guild_primcurr` = `guild_primcurr` - {$cost}
                        WHERE `guild_id` = {$gd['guild_id']}");
            //Log
            alert('success', 'Success!', "You have successfully purchased an armory for your guild.");
            $api->SystemLogsAdd($userid, 'guilds', "Purchased guild armory.");
        } else {
            echo "Your guild does not have an armory. It will cost your guild " . number_format($cost) . " Primary
            Currency to purchase an armory. Do you wish to purchase an armory for your guild?<br />
            <a href='?action=staff&act2=armory&buy=yes' class='btn btn-success'>Yes</a>
            <a href='?action=staff&act2=idx' class='btn btn-danger'>No</a>";
        }
    } else {
        if (isset($_POST['user'])) {
            //Make sure every variable is safe to work with.
            $_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;
            $_POST['item'] = (isset($_POST['item']) && is_numeric($_POST['item'])) ? abs($_POST['item']) : 0;
            $_POST['qty'] = (isset($_POST['qty']) && is_numeric($_POST['qty'])) ? abs($_POST['qty']) : 0;

            //Verify CSRF check is successful.
            if (!isset($_POST['verf']) || !verify_csrf_code("guild_give_item", stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
                die($h->endpage());
            }

            //Verify user chosen is in the guild.
            $q = $db->query("/*qc=on*/SELECT `userid`, `username` FROM `users` WHERE `userid` = {$_POST['user']} AND `guild` = {$gd['guild_id']}");
            if ($db->num_rows($q) == 0) {
                $db->free_result($q);
                alert('danger', "Uh Oh!", "You cannot give items to someone not in your guild.");
                die($h->endpage());
            }
            $db->free_result($q);

            //Verify the item chosen exists.
            $q = $db->query("/*qc=on*/SELECT `itmname` FROM `items` WHERE `itmid` = {$_POST['item']}");
            if ($db->num_rows($q) == 0) {
                $db->free_result($q);
                alert('danger', "Uh Oh!", "You cannot give out non-existent items.");
                die($h->endpage());
            }
            $db->free_result($q);

            //Verify the user is giving at least one item.
            if ($_POST['qty'] <= 0) {
                alert('danger', "Uh Oh!", "You must give out at least one item.");
                die($h->endpage());
            }

            //Check users' IP Address. Returns false if not and/or same user
            if ($api->SystemCheckUsersIPs($userid, $_POST['user'])) {
                alert('danger', "Uh Oh!", "You cannot give items to players who share the same IP Address as you.");
                die($h->endpage());
            }

            //Give items and whatnot.
            $api->GuildRemoveItem($ir['guild'], $_POST['item'], $_POST['qty']);
            $api->UserGiveItem($_POST['user'], $_POST['item'], $_POST['qty']);

            //Resolve item to variable
            $item = $api->SystemItemIDtoName($_POST['item']);
            $user = $api->SystemUserIDtoName($_POST['user']);

            //Notification
            $api->GameAddNotification($_POST['user'], "You have been given {$_POST['qty']} {$item}(s) from your guild's armory.");
            $api->GuildAddNotification($ir['guild'], "{$ir['username']} has given {$_POST['qty']} {$item}(s) from your guild's armory to {$user}.");
            alert('success', "Success!", "You have successfully given {$_POST['qty']} {$item}(s) from your guild's armory to {$user}.", true, "?action=staff&act2=idx");
            $api->SystemLogsAdd($userid, 'guilds', "Gave {$user} {$_POST['qty']} {$item}(s) from their armory.");
        } else {
            //Giving item form.
            $csrf = request_csrf_html('guild_give_item');
            echo "Fill out the form below completely to give out items from your armory.<br />
            <form method='post'>
            " . guild_user_dropdown('user', $ir['guild']) . "<br />
            " . armory_dropdown() . "<br />
            <input type='number' required='1' min='1' name='qty' placeholder='Quantity' class='form-control'><br />
            <input type='submit' value='Give Item' class='btn btn-primary'>
            {$csrf}
            </form>";
        }
    }
}
function staff_bonus()
{
	global $db,$gd,$userid,$api,$ir,$h,$set;
	$cost = $set['GUILD_PRICE'] * 4;
	if (isset($_POST['action']))
	{
		if ($_POST['action'] == 'enable')
		{
			if ($gd['guild_bonus_time'] > time())
			{
				alert('danger',"Uh Oh!","Your guild already has boost activated.");
				die($h->endpage());
			}
			if ($gd['guild_primcurr'] < $cost)
			{
				alert('danger',"Uh Oh!","Your guild does not have enough Copper Coins to enable training Boost.");
				die($h->endpage());
			}
			$bonustime=time()+((60*60)*3);
			$db->query("UPDATE `guild` SET `guild_bonus_time` = {$bonustime}, `guild_primcurr` = `guild_primcurr` - {$cost} WHERE `guild_id` = {$gd['guild_id']}");
			$api->GuildAddNotification($gd['guild_id'],"<a href='profile.php?user={$userid}'>{$ir['username']}</a> has paid " . number_format($cost) . " Copper Coins to enable training boost in your guild's gym for the next 3 hours.");
			alert('success',"Success!","You successfully enabled training boost for all your guild members that use your guild's gym. Reminder, that this boost is only affective in your guild's gym, and will end 3 hours from now.",true,'?action=staff&act2=idx');
		}
	}
	else
	{
		echo "Enable training boost for your guild? It'll cost your guild " . number_format($cost) . " Copper Coins. The Training Boost is only affective in 
		your Guild's Gym. This boost will also only last for 3 hours.
		<form method='post'>
			<input type='hidden' name='action' value='enable'>
			<input type='submit' class='btn-primary btn' value='Enable Boost'>
		</form>
		<a href='?action=staff&act2=idx'>Go Back</a>";
	}
}
function staff_crimes()
{
    global $db, $userid, $api, $h, $ir, $gd;
    //Select the guild's member count.
    $cnt = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `guild` = {$ir['guild']}");
    $membs = $db->fetch_single($cnt);
    $db->free_result($cnt);
    if (isset($_POST['crime'])) {
        //Make the POST safe to work with.
        $_POST['crime'] = (isset($_POST['crime']) && is_numeric($_POST['crime'])) ? abs(intval($_POST['crime'])) : 0;

        //Verify CSRF check is successful.
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_crimes", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }

        //Check that the guild isn't already planning a crime.
        if ($gd['guild_crime'] != 0) {
            alert('danger', "Uh Oh!", "You guild is already planning a crime.");
            die($h->endpage());
        }
        //Verify crime exists.
        $cq = $db->query("/*qc=on*/SELECT `gcUSERS` from `guild_crimes` WHERE `gcID` = {$_POST['crime']}");
        if ($db->num_rows($cq) == 0) {
            alert('danger', "Uh Oh!", "You cannot commit a non-existent crime.");
            die($h->endpage());
        }
        //Verify guild has enough members to commit this crime.
        $cr = $db->fetch_single($cq);
        if ($cr > $membs) {
            alert('danger', "Uh Oh!", "You cannot commit this crime as you need {$cr} guild members. You only have {$membs}.");
            die($h->endpage());
        }
        //Select time 24 hours from now.
        $ttc = time() + 86400;

        //Set guild's crime.
        $db->query("UPDATE `guild`
                    SET `guild_crime` = {$_POST['crime']},
                    `guild_crime_done` = {$ttc}
                    WHERE `guild_id` = {$ir['guild']}");
        alert('success', "Success!", "You have started to plan this crime. It will take 24 hours to commit.", true, '?action=staff&act2=idx');
    } else {
        //Select the crimes from database, based on how many members the guild has.
        $q = $db->query("/*qc=on*/SELECT *
                         FROM `guild_crimes`
                         WHERE `gcUSERS` <= $membs");

        //If there's crimes the guild can commit.
        if ($db->num_rows($q) > 0) {
            $csrf = request_csrf_html('guild_staff_crimes');
            echo "/*qc=on*/SELECT the crime you wish your guild to commit.<br />
            <form method='post'>
                <select name='crime' type='dropdown' class='form-control'>";
            while ($r = $db->fetch_row($q)) {
                echo "<option value='{$r['gcID']}'>{$r['gcNAME']}
                		({$r['gcUSERS']} members needed)</option>\n";
            }

            echo "</select><br />
                <input type='submit' value='Plan Crime' class='btn btn-primary'>
                {$csrf}
            </form>";
        } //Guild has no crimes they can commit.
        else {
            alert('danger', "Uh Oh!", "You guild cannot commit any crimes at this time.", true, '?action=staff&act2=idx');
        }
    }
}
function staff_pic()
{
	global $db, $userid, $api, $h, $ir, $gd;
	if (isset($_POST['newpic']))
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('guild_changepic', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your action was blocked for security reasons. Fill out the form quicker next time.");
            die($h->endpage());
        }
        $npic = (isset($_POST['newpic']) && is_string($_POST['newpic'])) ? stripslashes($_POST['newpic']) : '';
        if (!empty($npic)) {
            $sz = get_filesize_remote($npic);
            if ($sz <= 0 || $sz >= 1048576) {
                alert('danger', "Uh Oh!", "You picture's file size is too big. At maximum, picture file size can be 1MB.");
                $h->endpage();
                exit;
            }
            $image = (@isImage($npic));
            if (!$image) {
                alert('danger', "Uh Oh!", "The link you've input is not an image.");
                die($h->endpage());
            }
        }
		$img = htmlentities($_POST['newpic'], ENT_QUOTES, 'ISO-8859-1');
        alert('success', "Success!", "You have successfully updated your display picture to what's shown below.", true, '?action=staff&act2=idx');
        echo "<img src='{$img}' width='250' height='250' class='img-fluid'>";
        $db->query("UPDATE `guild` SET `guild_pic` = '" . $db->escape($npic) . "' WHERE `guild_id` = {$gd['guild_id']}");
	}
	else
	{
		$csrf = request_csrf_html('guild_changepic');
        echo "
		<h3>Change Guild Picture</h3>
		<hr />
		Your images must be externally hosted. Any images that are not 500x500 will be scaled accordingly.<br />
		New Picture Link<br />
		<form method='post'>
			<input type='url' required='1' name='newpic' class='form-control' value='{$gd['guild_pic']}' />
				{$csrf}
			<br />
			<input type='submit' class='btn btn-primary' value='Change Guild Pic' />
		</form>
		";
	}
}

function staff_intromsg()
{
	global $gd, $db, $h;
    if (isset($_POST['ament'])) {

        //Verify CSRF check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_intromsg", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }

        //Make sure the POST is safe to work with.
        $ament = $db->escape(nl2br(htmlentities(stripslashes($_POST['ament']), ENT_QUOTES, 'ISO-8859-1')));

        //Update the guild's announcement.
        $db->query("UPDATE `guild` SET `guild_intromsg` = '{$ament}' WHERE `guild_id` = {$gd['guild_id']}");
        alert('success', "Success!", "You have updated your guild's introduction message.", true, '?action=staff&act2=idx');
    } else {
        //Escape the announcement for safety reasons.
        $am_for_area = strip_tags($gd['guild_intromsg']);
        $csrf = request_csrf_html('guild_staff_intromsg');
        echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					This message will be sent to every applicant that gets accepted into the guild.
				</th>
			</tr>
			<tr>
				<th>
					Introductory Message
				</th>
				<td>
					<textarea class='form-control' name='ament'>{$am_for_area}</textarea>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='Change Introductory Message' class='btn btn-primary'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>
		<a href='?action=staff&act2=idx'>Go Back</a>";
    }
}

function staff_blockapps()
{
	global $db,$gd,$userid,$api,$ir,$h;
	if (isset($_POST['action']))
	{
		if ($_POST['action'] == 'block')
		{
			if ($gd['guild_ba'] == 1)
			{
				alert('danger',"Uh Oh!","Your guild is already blocking applications.");
				die($h->endpage());
			}
			$db->query("UPDATE `guild` SET `guild_ba` = 1 WHERE `guild_id` = {$gd['guild_id']}");
			$api->GuildAddNotification($gd['guild_id'],"<a href='profile.php?user={$userid}'>{$ir['username']}</a> has set the guild to no longer accept applications.");
			alert('success',"Success!","You have successfully set your guild to block all incoming applications.",true,'?action=staff&act2=idx');
		}
		else
		{
			if ($gd['guild_ba'] == 0)
			{
				alert('danger',"Uh Oh!","Your guild is already allowing applications.");
				die($h->endpage());
			}
			$db->query("UPDATE `guild` SET `guild_ba` = 0 WHERE `guild_id` = {$gd['guild_id']}");
			$api->GuildAddNotification($gd['guild_id'],"<a href='profile.php?user={$userid}'>{$ir['username']}</a> has set the guild to accept applications.");
			alert('success',"Success!","You have successfully set your guild to allow applications once again.",true,'?action=staff&act2=idx');
		}
	}
	else
	{
		echo "Here you may block guild applications. Players will not be able to send in a guild application unless disabled.
		<form method='post'>
			<input type='hidden' name='action' value='block'>
			<input type='submit' class='btn-outline-danger btn' value='Block Applications'>
		</form>
		<form method='post'>
			<input type='hidden' name='action' value='allow'>
			<input type='submit' class='btn-outline-primary btn' value='Allow Applications'>
		</form>
		<a href='?action=staff&act2=idx'>Go Back</a>";
	}
}
function staff_ally()
{
	global $db,$gd,$userid,$api,$ir,$h;
	//Check if user is the owner of the guild.
    if ($userid == $gd['guild_owner']) 
	{
        if (isset($_POST['guild']))
		{
			$_POST['guild'] = (isset($_POST['guild']) && is_numeric($_POST['guild'])) ? abs($_POST['guild']) : 0;

            //Verify CSRF check has passed.
            if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_ally_request", stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
                die($h->endpage());
            }
			if ($_POST['guild'] == $ir['guild'])
			{
				alert('danger',"Uh Oh!","You cannot send an alliance request to your own guild.");
				die($h->endpage());
			}
			$gexist=$db->query("/*qc=on*/SELECT * FROM `guild` WHERE `guild_id` = {$_POST['guild']}");
			if ($db->num_rows($gexist) == 0)
			{
				alert('danger',"Uh Oh!","You cannot send an ally request to a non-existent guild.");
				die($h->endpage());
			}
			$exist=$db->query("/*qc=on*/SELECT * 
								FROM `guild_alliances` 
								WHERE 
								(`alliance_a` = {$ir['guild']} OR `alliance_b` = {$ir['guild']}) 
								AND 
								(`alliance_a` = {$_POST['guild']} OR `alliance_b` = {$_POST['guild']})");
			if ($db->num_rows($exist) != 0)
			{
				alert('danger',"Uh Oh!","It appears you already have an outstanding alliance request, or are already allied with this guild.");
				die($h->endpage());
			}
			$type = ($_POST['type'] == 'traditional') ? 1 : 2;
			$api->GuildAddNotification($ir['guild'],"Your guild has sent an alliance request to {$api->GuildFetchInfo($_POST['guild'],'guild_name')}.");
			$api->GuildAddNotification($_POST['guild'],"{$gd['guild_name']} has sent an alliance request.");
			$db->query("INSERT INTO `guild_alliances` (`alliance_a`, `alliance_b`, `alliance_type`, `alliance_true`) VALUES ('{$ir['guild']}', '{$_POST['guild']}', '{$type}', '0')");
			alert('success',"Success!","Alliance request has been sent successfully.",true,'?action=staff&act2=idx');
		}
		else
		{
			$csrf = request_csrf_html('guild_staff_ally_request');
			echo "/*qc=on*/SELECT the guild you wish to form an alliance with, then select the alliance 
			type. Traditional alliance is an alliance where if either guild declares war, 
			the other guild will come to its aid. Non-aggressive alliances are when guilds 
			will only help when their alliance guild has war declared upon it. The alliance will 
			break in an Non-aggressive alliance if either party declares war on a third party.<br />
			<form method='post'>
				" . guilds_dropdown() . "
				<select name='type' class='form-control'>
					<option value='traditional'>Tradtional</option>
					<option value='nap'>Non-aggressive</option>
					{$csrf}
					<input type='submit' value='Request Alliance' class='btn btn-primary'>
				</select>
			</form>
			<a href='?action=staff&act2=idx'>Go Back</a>";
		}
    } 
	else 
	{
        alert('danger', "Uh Oh!", "You can only be here if you're the guild's leader.", true, '?action=staff&act2=idx');
    }
}
function staff_view_alliance_request()
{
	global $db,$gd,$userid,$api,$ir,$h;
	//Check if user is the owner of the guild.
    if ($userid == $gd['guild_owner']) 
	{
		if (isset($_GET['accept']))
		{
			$_GET['accept'] = (isset($_GET['accept']) && is_numeric($_GET['accept'])) ? abs($_GET['accept']) : 0;
			$exist=$db->query("/*qc=on*/SELECT * 
								FROM `guild_alliances` 
								WHERE `alliance_id` = {$_GET['accept']}
								AND `alliance_b` = {$ir['guild']}
								AND `alliance_true` = 0");
			if ($db->num_rows($exist) == 0)
			{
				alert('danger',"Uh Oh!","Request does not exist or does not belong to you.",false);
			}
			else
			{
				$re=$db->fetch_row($exist);
				alert('success',"Success!","Alliance request accepted successfully.",false);
				$db->query("UPDATE `guild_alliances` SET `alliance_true` = 1 WHERE `alliance_id` = {$_GET['accept']}");
				$api->GuildAddNotification($ir['guild'],"Your guild has accepted {$api->GuildFetchInfo($re['alliance_a'],'guild_name')}'s alliance request.");
				$api->GuildAddNotification($re['alliance_a'],"{$gd['guild_name']} has accepted your guild's alliance request.");
			}
		}
		if (isset($_GET['decline']))
		{
			$_GET['decline'] = (isset($_GET['decline']) && is_numeric($_GET['decline'])) ? abs($_GET['decline']) : 0;
			$exist=$db->query("/*qc=on*/SELECT * 
								FROM `guild_alliances` 
								WHERE `alliance_id` = {$_GET['decline']}
								AND `alliance_b` = {$ir['guild']}
								AND `alliance_true` = 0");
			if ($db->num_rows($exist) == 0)
			{
				alert('danger',"Uh Oh!","Request does not exist or does not belong to you.",false);
			}
			else
			{
				$re=$db->fetch_row($exist);
				alert('success',"Success!","Alliance request declined successfully.",false);
				$db->query("DELETE FROM `guild_alliances` WHERE `alliance_id` = {$_GET['decline']}");
				$api->GuildAddNotification($ir['guild'],"Your guild has declined {$api->GuildFetchInfo($re['alliance_a'],'guild_name')}'s alliance request.");
				$api->GuildAddNotification($re['alliance_a'],"{$gd['guild_name']} has declined your guild's alliance request.");
			}
		}
		if (isset($_GET['delete']))
		{
			$_GET['delete'] = (isset($_GET['delete']) && is_numeric($_GET['delete'])) ? abs($_GET['delete']) : 0;
			$exist=$db->query("/*qc=on*/SELECT * 
								FROM `guild_alliances` 
								WHERE `alliance_id` = {$_GET['delete']}
								AND `alliance_a` = {$ir['guild']}
								AND `alliance_true` = 0");
			if ($db->num_rows($exist) == 0)
			{
				alert('danger',"Uh Oh!","Request does not exist or does not belong to you.",false);
			}
			else
			{
				$re=$db->fetch_row($exist);
				alert('success',"Success!","Alliance request was successfully withdrawn.",false);
				$db->query("DELETE FROM `guild_alliances` WHERE `alliance_id` = {$_GET['delete']}");
				$api->GuildAddNotification($ir['guild'],"Your guild has withdrawn their alliance request to {$api->GuildFetchInfo($re['alliance_a'],'guild_name')}.");
				$api->GuildAddNotification($re['alliance_b'],"{$gd['guild_name']} has withdrawn their alliance request.");
			}
		}
		echo "These are the alliance requests your guild has received. Traditional alliance 
		is an alliance where if either guild declares war,the other guild will come to its aid. 
		Non-aggressive alliances are when guilds will only help when their alliance guild has 
		war declared upon it. The alliance will break in an Non-aggressive alliance if either 
		party declares war on a third party.
		<table class='table table-bordered'>
			<tr>
				<th>
					Guild Name
				</th>
				<th>
					Alliance Type
				</th>
				<th>
					Links
				</th>
			</tr>";
			$q=$db->query("/*qc=on*/SELECT * FROM `guild_alliances` WHERE `alliance_b` = {$ir['guild']} AND `alliance_true` = 0");
			while ($r=$db->fetch_row($q))
			{
				$type = ($r['alliance_type'] == 1) ? "Traditional" : "Non-aggressive";
				echo "
				<tr>
					<td>
						{$api->GuildFetchInfo($r['alliance_a'],'guild_name')}
					</td>
					<td>
						{$type}
					</td>
					<td>
						[<a href='?action=staff&act2=viewrally&accept={$r['alliance_id']}'>Accept</a>] || 
						[<a href='?action=staff&act2=viewrally&decline={$r['alliance_id']}'>Decline</a>]
					</td>
				</tr>";
			}
		echo "</table>
		<br />
		These are the alliance requests your guild has sent out.<table class='table table-bordered'>
			<tr>
				<th>
					Guild Name
				</th>
				<th>
					Alliance Type
				</th>
				<th>
					Links
				</th>
			</tr>";
		$q=$db->query("/*qc=on*/SELECT * FROM `guild_alliances` WHERE `alliance_a` = {$ir['guild']} AND `alliance_true` = 0");
		while ($r=$db->fetch_row($q))
		{
			$type = ($r['alliance_type'] == 1) ? "Traditional" : "Non-aggressive";
			echo "
			<tr>
				<td>
					{$api->GuildFetchInfo($r['alliance_b'],'guild_name')}
				</td>
				<td>
					{$type}
				</td>
				<td>
					[<a href='?action=staff&act2=viewrally&delete={$r['alliance_id']}'>Delete</a>]
				</td>
			</tr>";
		}
		echo"</table>
		<a href='?action=staff&act2=idx'>Go Back</a>";
    } 
	else 
	{
        alert('danger', "Uh Oh!", "You can only be here if you're the guild's leader.", true, '?action=staff&act2=idx');
    }
}
function staff_view_alliances()
{
	global $db,$gd,$userid,$api,$ir,$h;
	//Check if user is the owner of the guild.
    if ($userid == $gd['guild_owner']) 
	{
		if (isset($_GET['delete']))
		{
			$_GET['delete'] = (isset($_GET['delete']) && is_numeric($_GET['delete'])) ? abs($_GET['delete']) : 0;
			$exist=$db->query("/*qc=on*/SELECT * 
								FROM `guild_alliances` 
								WHERE `alliance_id` = {$_GET['delete']}
								AND (`alliance_a` = {$ir['guild']} OR `alliance_b` = {$ir['guild']})
								AND `alliance_true` = 1");
			if ($db->num_rows($exist) == 0)
			{
				alert('danger',"Uh Oh!","This alliance does not exist, or does not belong to you.",false);
			}
			else
			{
				$re=$db->fetch_row($exist);
				if ($re['alliance_a'] == $ir['guild'])
				{
					$broked=$re['alliance_b'];
				}
				else
				{
					$broked=$re['alliance_a'];
				}
				alert('success',"Success!","Alliance was successfully destroyed.",false);
				$db->query("DELETE FROM `guild_alliances` WHERE `alliance_id` = {$_GET['delete']}");
				$api->GuildAddNotification($ir['guild'],"Your guild has broke their alliance with {$api->GuildFetchInfo($re['alliance_a'],'guild_name')}.");
				$api->GuildAddNotification($broked,"{$gd['guild_name']} has broke their alliance with your guild.");
			}
		}
		echo "These are the alliances your guild has. Traditional alliance 
		is an alliance where if either guild declares war,the other guild will come to its aid. 
		Non-aggressive alliances are when guilds will only help when their alliance guild has 
		war declared upon it. The alliance will break in an Non-aggressive alliance if either 
		party declares war on a third party.
		<table class='table table-bordered'>
			<tr>
				<th>
					Guild Name
				</th>
				<th>
					Alliance Type
				</th>
				<th>
					Links
				</th>
			</tr>";
			$q=$db->query("/*qc=on*/SELECT * 
								FROM `guild_alliances` 
								WHERE (`alliance_a` = {$ir['guild']} OR `alliance_b` = {$ir['guild']})
								AND `alliance_true` = 1");
			while ($r=$db->fetch_row($q))
			{
				$type = ($r['alliance_type'] == 1) ? "Traditional" : "Non-aggressive";
				if ($r['alliance_a'] == $ir['guild'])
					$otheralliance=$r['alliance_b'];
				else
					$otheralliance=$r['alliance_a'];
				echo "
				<tr>
					<td>
						{$api->GuildFetchInfo($otheralliance,'guild_name')}
					</td>
					<td>
						{$type}
					</td>
					<td>
						[<a href='?action=staff&act2=viewallies&delete={$r['alliance_id']}'>Break Alliance</a>]
					</td>
				</tr>";
			}
		echo "</table>
		<a href='?action=staff&act2=idx'>Go Back</a>";
    } 
	else 
	{
        alert('danger', "Uh Oh!", "You can only be here if you're the guild's leader.", true, '?action=staff&act2=idx');
    }
}
function updateDonations($guildid,$userid,$type,$increase)
{
	global $db;
	$q=$db->query("SELECT * FROM `guild_donations` WHERE `guildid` = {$guildid} AND `userid` = {$userid}");
	if ($db->num_rows($q) == 0)
	{
		$db->query("INSERT INTO `guild_donations` (`userid`, `guildid`, `copper`, `tokens`, `xp`) VALUES ('{$userid}', '{$guildid}', '0', '0', '0')");
	}
	$db->query("UPDATE `guild_donations` SET `{$type}` = `{$type}` + {$increase} WHERE `userid` = {$userid} AND `guildid` = {$guildid}");
}
$h->endpage();