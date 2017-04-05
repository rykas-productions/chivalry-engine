<?php
/*
	File:		viewguild.php
	Created: 	4/5/2016 at 12:32AM Eastern Time
	Info: 		Allows users to view their guild and do various actions.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
if (!$ir['guild'])
{
    alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_ERROR1'],true,'index.php');
}
else
{
	$gq = $db->query("SELECT * FROM `guild` WHERE `guild_id` = {$ir['guild']}");
    if ($db->num_rows($gq) == 0)
    {
        alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['VIEWGUILD_ERROR2']}");
        die($h->endpage());
    }
    $gd = $db->fetch_row($gq);
    $db->free_result($gq);
    echo "
	<h3><u>{$lang['VIEWGUILD_TITLE']} {$gd['guild_name']}.</u></h3>
   	";
	if (!isset($_GET['action']))
	{
		$_GET['action'] = '';
	}
	switch ($_GET['action'])
	{
	case 'summary':
		summary();
		break;
	case 'donate':
		donate();
		break;
	case "members":
        members();
        break;
	case "gym":
        gym();
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
	default:
		home();
		break;
	}
}
function home()
{
	global $db,$userid,$ir,$gd,$lang,$api;
	echo "
    <table class='table table-bordered'>
    		<tr>
    			<td><a href='?action=summary'>{$lang['VIEWGUILD_HOME_SUMMARY']}</a></td>
    			<td><a href='?action=donate'>{$lang['VIEWGUILD_HOME_DONATE']}</a></td>
    		</tr>
    		<tr>
    			<td><a href='?action=members'>{$lang['VIEWGUILD_HOME_USERS']}</a></td>
    			<td><a href='?action=crimes'>{$lang['VIEWGUILD_HOME_CRIME']}</a></td>
    		</tr>
    		<tr>
    			<td><a href='?action=leave'>{$lang['VIEWGUILD_HOME_LEAVE']}</a></td>
				<td><a href='?action=atklogs'>{$lang['VIEWGUILD_HOME_ATKLOG']}</a></td>
    		</tr>
    		<tr>
    			<td><a href='?action=armory'>{$lang['VIEWGUILD_HOME_ARMORY']}</a></td>
    			<td><a href='?action=gym'>{$lang['VIEWGUILD_GYM_LINK']}</a>
       ";
    if ($gd['guild_owner'] == $userid || $gd['guild_coowner'] == $userid)
    {
        echo "</tr><tr><td><a href='?action=staff&act2=idx'>{$lang['VIEWGUILD_HOME_STAFF']}</a>";
    }
    else
    {
        echo "&nbsp;";
    }
    echo "
				</td>
			</tr>
	</table>
	<br />
	<table class='table table-bordered'>
		<tr>
			<td>{$lang['VIEWGUILD_HOME_ANNOUNCE']}</td>
		</tr>
		<tr>
			<td>{$gd['guild_announcement']}</td>
		</tr>
	</table>
	<br />
	<b>{$lang['VIEWGUILD_HOME_EVENT']}</b>
	<br />
   	";
    $q = $db->query("SELECT * FROM `guild_notifications` WHERE `gn_guild` = {$ir['guild']} ORDER BY `gn_time` DESC  LIMIT 10");
    echo "
	<table class='table table-bordered'>
		<tr>
			<th>{$lang['VIEWGUILD_HOME_EVENTTIME']}</th>
			<th>{$lang['VIEWGUILD_HOME_EVENTTEXT']}</th>
		</tr>
   	";
    while ($r = $db->fetch_row($q))
    {
        echo "
		<tr>
			<td>" . date('F j Y, g:i:s a', $r['gn_time'])
                . "</td>
			<td>{$r['gn_text']}</td>
		</tr>
   		";
    }
    $db->free_result($q);
    echo "</table>";
}
function summary()
{
	global $db,$userid,$ir,$gd,$lang,$api;
	echo "
	<table class='table table-bordered'>
	<tr>
		<th colspan='2'>
			{$lang['VIEWGUILD_SUMMARY_TITLE']}
		</th>
	</tr>
	<tr>
		<th>
			{$lang['VIEWGUILD_SUMMARY_OWNER']}
		</th>
		<td>
       ";
    $pq = $db->query("SELECT `username` FROM `users` WHERE `userid` = {$gd['guild_owner']}");
    if ($db->num_rows($pq) > 0)
    {
        $ldrnm = $db->fetch_single($pq);
        echo "<a href='profile.php?user={$gd['guild_owner']}'> {$ldrnm} </a>";
    }
    else
    {
        echo "{$lang['VIEWGUILD_NA']}";
    }
	echo"</td>
	</tr>
	<tr>
		<th>
			{$lang['VIEWGUILD_SUMMARY_COOWNER']}
		</th>
		<td>";
    $db->free_result($pq);
    $vpq = $db->query("SELECT `username` FROM `users` WHERE `userid` = {$gd['guild_coowner']}");
    if ($db->num_rows($vpq) > 0)
    {
        $vldrnm = $db->fetch_single($vpq);
        echo "<a href='profile.php?user={$gd['guild_coowner']}'> {$vldrnm} </a>";
    }
    else
    {
        echo "{$lang['VIEWGUILD_NA']}";
    }
	echo"</td>
	</tr>";
    $db->free_result($vpq);
    $cnt = $db->query("SELECT COUNT(`userid`) FROM `users` WHERE `guild` = {$gd['guild_id']}");
    echo "
	<tr>
		<th>
			{$lang['VIEWGUILD_SUMMARY_MEM']}
		</th>
		<td>
			" . $db->fetch_single($cnt) . "/ {$gd['guild_capacity']}
		</td>
	</tr>
	<tr>
		<th>
			{$lang['VIEWGUILD_SUMMARY_LVL']}
		</th>
		<td>
			{$gd['guild_level']}
		</td>
	</tr>
	<tr>
		<th>
			{$lang['INDEX_PRIMCURR']}
		</th>
		<td>
			" . number_format($gd['guild_primcurr']) . " / " . number_format($gd['guild_level'] * 1500000) . "
		</td>
	</tr>
	<tr>
		<th>
			{$lang['INDEX_SECCURR']}
		</th>
		<td>
			" . number_format($gd['guild_seccurr']) . "
		</td>
	</tr>
      </table>";
}
function donate()
{
	global $db,$userid,$ir,$gd,$lang,$api,$h;
	if (isset($_POST['primary']))
	{
		$_POST['primary'] = (isset($_POST['primary']) && is_numeric($_POST['primary'])) ? abs(intval($_POST['primary'])) : 0;
		$_POST['secondary'] = (isset($_POST['secondary']) && is_numeric($_POST['secondary'])) ? abs(intval($_POST['secondary'])) : 0;
		if (!isset($_POST['verf']) || !verify_csrf_code('guild_donate', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		if (empty($_POST['primary']) && empty($_POST['secondary']))
		{
			alert('danger',$lang["ERROR_GENERIC"],$lang['VIEWGUILD_DONATE_ERR1']);
			die($h->endpage());
		}
		if ($_POST['primary'] > $ir['primary_currency'])
		{
			alert('danger',$lang["ERROR_GENERIC"],$lang['VIEWGUILD_DONATE_ERR2']);
			die($h->endpage());
		}
		else if ($_POST['secondary'] > $ir['secondary_currency'])
		{
			alert('danger',$lang["ERROR_GENERIC"],$lang['VIEWGUILD_DONATE_ERR3']);
			die($h->endpage());
		}
		else if ($_POST['primary']+$gd['guild_primcurr'] > $gd['guild_level']*1500000)
		{
			alert('danger',$lang["ERROR_GENERIC"],"{$lang['VIEWGUILD_DONATE_ERR4']}" . $gd['guild_level']*1500000);
			die($h->endpage());
		}
		else
		{
			$api->UserTakeCurrency($userid,'primary',$_POST['primary']);
			$api->UserTakeCurrency($userid,'secondary',$_POST['secondary']);
			$db->query("UPDATE `guild` 
					SET `guild_primcurr` = `guild_primcurr` + {$_POST['primary']},
					`guild_seccurr` = `guild_seccurr` + {$_POST['secondary']}
					WHERE `guild_id` = {$gd['guild_id']}");
			$my_name = htmlentities($ir['username'], ENT_QUOTES, 'ISO-8859-1');
			$event =$db->escape("<a href='profile.php?user={$userid}'>{$my_name}</a> donated 
									" . number_format($_POST['primary']) . " Primary Currency
								and " . number_format($_POST['secondary']) . " Secondary Currency to the guild.");
			 $db->query("INSERT INTO `guild_notifications`
						VALUES(NULL, {$gd['guild_id']}, " . time() . ", '{$event}')");
			alert('success',$lang["ERROR_SUCCESS"],$lang['VIEWGUILD_DONATE_SUCC'],true,'viewguild.php');
		}
	}
	else
	{
		$csrf = request_csrf_html('guild_donate');
		echo "
		<form action='?action=donate' method='post'>
			<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					{$lang['VIEWGUILD_DONATE_TITLE']} " . number_format($ir['primary_currency']) . "
					{$lang['INDEX_PRIMCURR']} and " . number_format($ir['secondary_currency']) . " {$lang['INDEX_SECCURR']}
				</th>
			</tr>
    		<tr>
    			<td>
    				<b>{$lang['INDEX_PRIMCURR']}</b><br />
    				<input type='number' name='primary' value='0' required='1' class='form-control' min='0' />
    			</td>
    			<td>
    				<b>{$lang['INDEX_SECCURR']}</b><br />
    				<input type='number' name='secondary' required='1' class='form-control' value='0' min='0' />
    			</td>
    		</tr>
    		<tr>
    			<td colspan='2' align='center'>
    			    {$csrf}
    				<input type='submit' class='btn btn-default' value='{$lang['VIEWGUILD_DONATE_BTN']}' />
    			</td>
    		</tr>
    	</table>
		</form>";
	}
}
function members()
{
	global $db,$userid,$ir,$gd,$lang,$api,$h;
	echo "
    <table class='table table-bordered table-striped'>
		<tr>
    		<th>
				{$lang['VIEWGUILD_MEMBERS_TH1']}
			</th>
    		<th>
				{$lang['VIEWGUILD_MEMBERS_TH1']}
			</th>
    		<th>
				&nbsp;
			</th>
    	</tr>";
    $q = $db->query("SELECT `userid`, `username`, `level`, `display_pic` FROM `users` WHERE `guild` = {$gd['guild_id']} ORDER BY `level` DESC");
    $csrf = request_csrf_html('guild_kickuser');
    while ($r = $db->fetch_row($q))
    {
        echo "
		<tr>
        	<td>
				<img src='{$r['display_pic']}' width='64' height='64'><br />
				<a href='profile.php?user={$r['userid']}'>{$r['username']}</a>
			</td>
        	<td>
				{$r['level']}
			</td>
        	<td>
           ";
				if ($gd['guild_owner'] == $userid || $gd['guild_coowner'] == $userid)
				{
					echo "
					<form action='?action=kick' method='post'>
						<input type='hidden' name='ID' value='{$r['userid']}' />
						{$csrf}
						<input type='submit' class='btn btn-default' value='{$lang['VIEWGUILD_MEMBERS_BTN']} {$r['username']}' />
					</form>";
				}
				else
				{
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
	<br />
	&gt; <a href='?action=home'>{$lang['VIEWGUILD_IDX']}</a>
   	";
}
function staff_kick()
{
    global $db,$userid,$ir,$gd,$lang,$api,$h;
    if ($gd['guild_owner'] == $userid || $gd['guild_coowner'] == $userid)
    {
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_kickuser", stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"],true,'viewguild.php?action=members');
			die($h->endpage());
		}
        $_POST['ID'] = (isset($_POST['ID']) && is_numeric($_POST['ID'])) ? abs(intval($_POST['ID'])) : 0;
        $who = $_POST['ID'];
        if ($who == $gd['guild_owner'])
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_KICK_ERR'],true,'viewguild.php?action=members');
        }
        else if ($who == $userid)
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_KICK_ERR1'],true,'viewguild.php?action=members');
        }
        else
        {
            $q = $db->query("SELECT `username` FROM `users` WHERE `userid` = $who AND `guild` = {$gd['guild_id']}");
            if ($db->num_rows($q) > 0)
            {
                $kdata = $db->fetch_row($q);
                $db->query("UPDATE `users` SET `guild` = 0 WHERE `userid` = {$who}");
                $d_username = htmlentities($kdata['username'], ENT_QUOTES, 'ISO-8859-1');
                $d_oname = htmlentities($ir['username'], ENT_QUOTES, 'ISO-8859-1');
                alert('success',$lang['ERROR_SUCCESS'],$lang['VIEWGUILD_KICK_SUCCESSS'],true,'viewguild.php?action=members');
                $their_event = "You were kicked out of the {$gd['guild_name']} guild by <a href='profile.php?user={$userid}'>{$d_oname}</a>.";
                $api->GameAddNotification($who, $their_event);
                $gang_event =  $db->escape("<a href='profile.php?user={$who}'>{$d_username}</a> was kicked out of the guild by <a href='profile.php?user={$userid}'>{$d_oname}</a>.");
                $db->query("INSERT INTO `guild_notifications` VALUES(NULL, {$gd['guild_id']}, " . time() . ", '{$gang_event}');");
            }
            else
            {
                alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_KICK_ERR2'],true,'viewguild.php?action=members');
            }
            $db->free_result($q);
        }
    }
    else
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_KICK_ERR3'],true,'viewguild.php');
    }
}
function leave()
{
	global $db,$userid,$ir,$gd,$lang,$api,$h;
	if ($gd['guild_owner'] == $userid || $gd['guild_coowner'] == $userid)
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_LEAVE_ERR'],true,'viewguild.php');
        die($h->endpage());
    }
	if (isset($_POST['submit']) && $_POST['submit'] == 'yes')
    {
		if (!isset($_POST['verf']) || !verify_csrf_code("guild_leave", stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		$db->query("UPDATE `users` SET `guild` = 0  WHERE `userid` = {$userid}");
		$api->GuildAddNotification($ir['guild'],"<a href='profile.php?user={$userid}'>{$ir['username']}</a> has left the guild.");
		alert('success',$lang['ERROR_SUCCESS'],$lang['VIEWGUILD_LEAVE_SUCC'],true,'index.php');
	}
	elseif (isset($_POST['submit']) && $_POST['submit'] == 'no')
	{
		alert('success',$lang['ERROR_SUCCESS'],$lang['VIEWGUILD_LEAVE_SUCC1'],true,'viewguild.php');
	}
	else
	{
		$csrf = request_csrf_html('guild_leave');
        echo "{$lang['VIEWGUILD_LEAVE_INFO']}
        <form action='?action=leave' method='post'>
            {$csrf}
			<input type='hidden' name='submit' value='yes'>
        	<input type='submit' class='btn btn-default' value='{$lang['VIEWGUILD_LEAVE_BTN']}' />
		</form><br />
		<form action='?action=leave' method='post'>
			{$csrf}
			<input type='hidden' name='submit' value='no'>
        	<input type='submit' class='btn btn-default' value='{$lang['VIEWGUILD_LEAVE_BTN1']}' />
        </form>";
	}
}
function atklogs()
{
	global $db,$userid,$ir,$gd,$lang,$api,$h;
	$atks =
            $db->query("SELECT `l`.*, `u`.`guild`, `u`.`userid` 
			FROM `logs` as `l`
			INNER JOIN `users` as `u`
			ON `l`.`log_user` = `u`.`userid`
			WHERE (`u`.`guild` = {$ir['guild']}) AND log_type = 'attacking'
			ORDER BY `log_time` DESC
			LIMIT 50");
    echo "<b>{$lang['VIEWGUILD_ATKLOGS_INFO']}</b><br />
	<table class='table table-bordered'>
		<tr>
			<th>{$lang['VIEWGUILD_ATKLOGS_TD1']}</th>
			<th>{$lang['VIEWGUILD_ATKLOGS_TD2']}</th>
		</tr>";
    while ($r = $db->fetch_row($atks))
    {
        $d = DateTime_Parse($r['log_time']);
        echo "<tr>
        		<td>$d</td>
        		<td>
					" . $api->SystemUserIDtoName($r['log_user']) . " {$r['log_text']}
        		</td>
        	  </tr>";
    }
    $db->free_result($atks);
    echo "</table>";
}
function staff()
{
	global $db,$userid,$ir,$gd,$lang,$api,$h;
	if ($gd['guild_owner'] == $userid || $gd['guild_coowner'] == $userid)
    {
        if (!isset($_GET['act2']))
        {
            $_GET['act2'] = 'idx';
        }
        switch ($_GET['act2'])
        {
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
			default:
				staff_idx();
				break;
        }
    }
    else
    {
        alert('danger',$lang['ERROR_NOPERM'],$lang['VIEWGUILD_STAFF_ERROR'],true,'viewguild.php');
        die($h->endpage());
    }
}
function staff_idx()
{
	global $db,$userid,$ir,$gd,$lang,$api,$h;
	echo "<table class='table table-bordered'>
	<tr>
		<td>
			<b>{$lang['VIEWGUILD_SUMMARY_COOWNER']}</b><br />
			<a href='?action=staff&act2=apps'>{$lang['VIEWGUILD_STAFF_IDX_APP']}</a><br />
			<a href='?action=staff&act2=vault'>{$lang['VIEWGUILD_STAFF_IDX_VAULT']}</a><br />
			<a href='?action=staff&act2=coowner'>{$lang['VIEWGUILD_STAFF_IDX_COOWNER']}</a><br />
			<a href='?action=staff&act2=ament'>{$lang['VIEWGUILD_STAFF_IDX_AMENT']}</a><br />
			<a href='?action=staff&act2=massmail'>{$lang['VIEWGUILD_STAFF_IDX_MM']}</a><br />
			<a href='?action=staff&act2=masspay'>{$lang['VIEWGUILD_STAFF_IDX_MP']}</a><br />
		</td>";
	if ($gd['guild_owner'] == $userid)
	{
		echo "
		<td>
			<b>{$lang['VIEWGUILD_SUMMARY_OWNER']}</b><br />
			<a href='?action=staff&act2=leader'>{$lang['VIEWGUILD_STAFF_IDX_LEADER']}</a><br />
			<a href='?action=staff&act2=name'>{$lang['VIEWGUILD_STAFF_IDX_NAME']}</a><br />
			<a href='?action=staff&act2=desc'>{$lang['VIEWGUILD_STAFF_IDX_DESC']}</a><br />
			<a href='?action=staff&act2=town'>{$lang['VIEWGUILD_STAFF_IDX_TOWN']}</a><br />
		</td>";
	}
	echo "</tr></table>";
}
function staff_apps()
{
	global $db,$userid,$ir,$gd,$lang,$api,$h;
    $_POST['app'] = (isset($_POST['app']) && is_numeric($_POST['app'])) ? abs(intval($_POST['app'])) : '';
    $what = (isset($_POST['what']) && in_array($_POST['what'], array('accept', 'decline'), true)) ? $_POST['what'] : '';
    if (!empty($_POST['app']) && !empty($what))
    {
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_apps", stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
        $aq =
                $db->query(
                        "SELECT `ga_user`
                         FROM `guild_applications`
                         WHERE `ga_id` = {$_POST['app']}
                         AND `ga_guild` = {$gd['guild_id']}");
        if ($db->num_rows($aq) > 0)
        {
            $appdata = $db->fetch_row($aq);
            if ($what == 'decline')
            {
                $db->query("DELETE FROM `guild_applications` WHERE `ga_id` = {$_POST['app']}");
                $api->GameAddNotification($appdata['ga_user'],"We regret to inform you that your application to join the {$gd['guild_name']} guild was declined.");
				$event = $db->escape("<a href='profile.php?user={$userid}'>{$ir['username']}</a> 
									has declined <a href='profile.php?user={$appdata['ga_user']}'>
									" . $api->SystemUserIDtoName($appdata['ga_user']) . "</a>'s 
									application to join the guild.");
                $db->query(
                        "INSERT INTO `guild_notifications`
                         VALUES (NULL, {$gd['guild_id']}, " . time() . ", '{$event}')");
                alert('success',$lang['ERROR_SUCCESS'],$lang['VIEWGUILD_STAFF_APP_DENY_TEXT']);
            }
            else
            {
                $cnt = $db->query("SELECT COUNT(`userid`) FROM `users` WHERE `guild` = {$gd['guild_id']}");
                if ($gd['guild_capacity'] <= $db->fetch_single($cnt))
                {
                    $db->free_result($cnt);
                    alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_STAFF_APP_ACC_ERR']);
                    die($h->endpage());
                }
                else if ($api->UserInfoGet($appdata['ga_user'],'guild') != 0)
                {
                    $db->free_result($cnt);
                    alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_STAFF_APP_ACC_ERR1']);
                    die($h->endpage());
                }
				$townlevel=$db->fetch_single($db->query("SELECT `town_min_level` FROM `town` WHERE `town_guild_owner` = {$gd['guild_id']}"));
				if ($townlevel > $api->UserInfoGet($appdata['ga_user'],'level') && $townlevel > 0)
				{
					alert('danger',$lang["ERROR_GENERIC"],$lang['VIEWGUILD_STAFF_APP_ACC_ERR2']);
					die($h->endpage());
				}
                $db->free_result($cnt);
                $db->query("DELETE FROM `guild_applications` WHERE `ga_id` = {$_POST['app']}");
                $api->GameAddNotification($appdata['ga_user'], "Your application to join the {$gd['guild_name']} guild was accepted.");
                $event = $db->escape("<a href='profile.php?user={$userid}'>{$ir['username']}</a> 
									has accepted <a href='profile.php?user={$appdata['ga_user']}'>
									" . $api->SystemUserIDtoName($appdata['ga_user']) . "</a>'s 
									application to join the guild.");
                $db->query("INSERT INTO `guild_notifications` 
							VALUES (NULL, {$gd['guild_id']}, " . time() . ", '{$event}')");
                $db->query(
                        "UPDATE `users`
                         SET `guild` = {$gd['guild_id']}
                         WHERE `userid` = {$appdata['ga_user']}");
                alert('success',$lang['ERROR_SUCCESS'],$lang['VIEWGUILD_STAFF_APP_ACC_SUCC']);
            }
        }
        else
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_STAFF_APP_WOT']);
        }
        $db->free_result($aq);
    }
    else
    {
        echo "
        <b>{$lang['VIEWGUILD_STAFF_IDX_APP']}</b>
        <br />
        <table class='table table-bordered table-striped'>
        		<tr>
        			<th>{$lang['VIEWGUILD_STAFF_APP_TH0']}</th>
        			<th>{$lang['VIEWGUILD_STAFF_APP_TH1']}</th>
					<th>{$lang['VIEWGUILD_STAFF_APP_TH2']}</th>
        			<th>{$lang['VIEWGUILD_STAFF_APP_TH3']}</th>
        			<th>{$lang['VIEWGUILD_STAFF_APP_TH4']}</th>
        		</tr>
   		";
        $q =
                $db->query(
                        "SELECT *
                         FROM `guild_applications`
                         WHERE `ga_guild` = {$gd['guild_id']}
						 ORDER BY `ga_time` DESC");
        $csrf = request_csrf_html('guild_staff_apps');
        while ($r = $db->fetch_row($q))
        {
            $r['ga_text'] = htmlentities($r['ga_text'], ENT_QUOTES, 'ISO-8859-1', false);
            echo "
            <tr>
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
            			<input class='btn btn-success' type='submit' value='{$lang['VIEWGUILD_STAFF_APP_BTN']}' />
            		</form>
					<br />
            		<form action='?action=staff&act2=apps' method='post'>
            			<input type='hidden' name='app' value='{$r['ga_id']}' />
            			<input type='hidden' name='what' value='decline' />
            			{$csrf}
            			<input class='btn btn-danger' type='submit' value='{$lang['VIEWGUILD_STAFF_APP_BTN1']}' />
            		</form>
            	</td>
            </tr>
               ";
        }
        echo "</table>";
    }
}
function gym()
{
	global $db,$lang,$api,$h,$userid,$ir,$gd;
	if ($gd['guild_level'] < 3)
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_GYM_ERR'],true,'viewguild.php');
		die($h->endpage());
	}
	else
	{
		if ($api->UserStatus($ir['userid'],'infirmary') == true)
		{
			alert("danger",$lang["GEN_INFIRM"],$lang['GYM_INFIRM'],true,'index.php');
			die($h->endpage());
		}
		if ($api->UserStatus($ir['userid'],'dungeon') == true)
		{
			alert("danger",$lang["GEN_DUNG"],$lang['GYM_DUNG'],true,'index.php');
			die($h->endpage());
		}
		$statnames =  array("Strength" => "strength", "Agility" => "agility", "Guard" => "guard", "Labor" => "labor");
		if (!isset($_POST["amnt"]))
		{
			$_POST["amnt"] = 0;
		}
		$_POST["amnt"] = abs((int) $_POST["amnt"]);
		if (isset($_POST["stat"]) && $_POST["amnt"])
		{
			if (!isset($statnames[$_POST['stat']]))
			{
				alert("danger",$lang['ERROR_INVALID'],$lang['GYM_INVALIDSTAT'],true,'viewguild.php?action=gym');
				die($h->endpage());
			}
			if (!isset($_POST['verf']) || !verify_csrf_code('guildgym_train', stripslashes($_POST['verf'])))
			{
				alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"],true,'viewguild.php?action=gym');
				die($h->endpage());
			}
			$stat = $statnames[$_POST['stat']];
			if ($_POST['amnt'] > $ir['energy'])
			{
				alert("danger",$lang['GYM_NEG'],$lang['GYM_NEG_DETAIL'],false);
			}
			else
			{
				$gain = 0;
				$extraecho='';
				for ($i = 0; $i < $_POST['amnt']; $i++)
				{
					$gain += (Random(1, 4) / Random(600, 1000) * Random(500, 1000) * (($ir['will'] + 25) / 175));
					$ir['will'] -= Random(1, 3);
					if ($ir['will'] < 0)
					{
						$ir['will'] = 0;
					}
				}
				if ($ir['class'] == 'Warrior')
				{
					if ($stat == 'strength')
					{
						$gain *= 2;
					}
					if ($stat == 'guard')
					{
						$gain /= 2;
					}
				}
				if ($ir['class'] == 'Rogue')
				{
					if ($stat == 'agility')
					{
						$gain *= 2;
					}
					if ($stat == 'strength')
					{
						$gain /= 2;
					}
				}
				if ($ir['class'] == 'Defender')
				{
					if ($stat == 'guard')
					{
						$gain *= 2;
					}
					if ($stat == 'agility')
					{
						$gain /= 2;
					}
				}
				$gain=($gain*(($gd['guild_level']*0.02)+0.94));
				$gain=floor($gain);
				$db->query(
						"UPDATE `userstats`
						 SET `{$stat}` = `{$stat}` + $gain
						 WHERE `userid` = $userid");
				$db->query(
						"UPDATE `users`
						 SET `will` = {$ir['will']},
						 `energy` = `energy` - {$_POST['amnt']}
						 WHERE `userid` = $userid");
				$NewStatAmount = $ir[$stat] + $gain;
				$EnergyLeft = $ir['energy'] - $_POST['amnt'];
				if ($stat == "strength")
				{
					alert('success',$lang['ERROR_SUCCESS'],"{$lang['GYM_STR']} {$gain} {$lang['GEN_STR']} {$lang['GYM_STR1']} {$_POST['amnt']} {$lang['GYM_STR2']} {$NewStatAmount} {$lang['GEN_STR']} {$lang['GEN_AND']} {$EnergyLeft} {$lang['GYM_STR3']}",false);
					$str_select="selected";
				}
				elseif ($stat == "agility")
				{
					alert('success',$lang['ERROR_SUCCESS'],"{$lang['GYM_AGL']} {$gain} {$lang['GEN_AGL']} {$lang['GYM_STR1']} {$_POST['amnt']} {$lang['GYM_AGL1']} {$lang['GYM_YNH']} {$NewStatAmount} {$lang['GEN_AGL']} {$lang['GEN_AND']} {$EnergyLeft} {$lang['GYM_STR3']}",false);
					$agl_select="selected";
				}
				elseif ($stat == "guard")
				{
					alert('success',$lang['ERROR_SUCCESS'],"{$lang['GYM_GRD']} {$gain} {$lang['GEN_GRD']} {$lang['GYM_STR1']} {$_POST['amnt']} {$lang['GYM_GRD1']} {$lang['GYM_YNH']} {$NewStatAmount} {$lang['GEN_GRD']} {$lang['GEN_AND']} {$EnergyLeft} {$lang['GYM_STR3']}",false);
					$grd_select="selected";
				}
				elseif ($stat == "labor")
				{
					alert('success',$lang['ERROR_SUCCESS'],"{$lang['GYM_LAB']} {$gain} {$lang['GEN_LAB']} {$lang['GYM_STR1']} {$_POST['amnt']} {$lang['GYM_LAB1']} {$lang['GYM_YNH']} {$NewStatAmount} {$lang['GEN_LAB']} {$lang['GEN_AND']} {$EnergyLeft} {$lang['GYM_STR3']}",false);
					$lab_select="selected";
				}
				$api->SystemLogsAdd($userid,'training',"Trained their {$stat} in their guild and gained {$gain}.");
				echo "<hr />";
				$ir['energy'] -= $_POST['amnt'];
				$ir[$stat] += $gain;
			}
		}
		if (!isset($str_select))
		{
			$str_select='';
		}
		if (!isset($agl_select))
		{
			$agl_select='';
		}
		if (!isset($grd_select))
		{
			$grd_select='';
		}
		if (!isset($lab_select))
		{
			$lab_select='';
		}
		$ir['strank'] = get_rank($ir['strength'], 'strength');
		$ir['agirank'] = get_rank($ir['agility'], 'agility');
		$ir['guarank'] = get_rank($ir['guard'], 'guard');
		$ir['labrank'] = get_rank($ir['labor'], 'labor');
		$code = request_csrf_html('guildgym_train');
		echo "{$lang['GYM_FRM1']} {$ir['energy']} {$lang['GYM_FRM2']}<hr />
		<table class='table table-bordered'>
			<tr>
				<form action='?action=gym' method='post'>
					<th>{$lang['GYM_TH']}</th>
					<td><select type='dropdown' name='stat' class='form-control'>
		<option {$str_select} value='Strength'>{$lang['GEN_STR']} ({$lang['GEN_HAVE']} {$ir['strength']}, {$lang['GEN_RANK']} {$ir['strank']})
		<option {$agl_select} value='Agility'>{$lang['GEN_AGL']} ({$lang['GEN_HAVE']} {$ir['agility']}, {$lang['GEN_RANK']} {$ir['agirank']})
		<option {$grd_select} value='Guard'>{$lang['GEN_GRD']} ({$lang['GEN_HAVE']} {$ir['guard']}, {$lang['GEN_RANK']} {$ir['guarank']})
		<option {$lab_select} value='Labor'>{$lang['GEN_LAB']} ({$lang['GEN_HAVE']} {$ir['labor']}, {$lang['GEN_RANK']} {$ir['labrank']})
		</select></td>
			</tr>
			<tr>
				<th>{$lang['GYM_TH1']}</th>
				<td><input type='number' class='form-control' min='1' max='{$ir['energy']}' name='amnt' value='{$ir['energy']}' /></td>
			</tr>
			<tr>
				<td colspan='2'><input type='submit' class='btn btn-default' value='{$lang['GYM_BTN']}' /></td>
			</tr>
			{$code}
			</form>
		</table>";
	}
}
function staff_vault()
{
    global $db,$userid,$gd,$lang,$api,$h;
    if (isset($_POST['primary']) || isset($_POST['secondary']))
    {
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_vault", stripslashes($_POST['verf'])))
        {
            alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
            die($h->endpage());
        }
        $_POST['primary'] = (isset($_POST['primary']) && is_numeric($_POST['primary'])) ? abs($_POST['primary']) : 0;
        $_POST['secondary'] = (isset($_POST['secondary']) && is_numeric($_POST['secondary'])) ? abs($_POST['secondary']) : 0;
        $_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;
        if ($_POST['primary'] > $gd['guild_primcurr'])
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_STAFF_VAULT_ERR']);
            die($h->endpage());
        }
        if ($_POST['secondary'] > $gd['guild_seccurr'])
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_STAFF_VAULT_ERR1']);
            die($h->endpage());
        }
        if ($_POST['primary'] == 0 && $_POST['secondary'] == 0)
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_STAFF_VAULT_ERR2']);
            die($h->endpage());
        }
        if ($api->SystemCheckUsersIPs($userid,$_POST['user']))
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_STAFF_VAULT_ERR4']);
            die($h->endpage());
        }
        $q=$db->query("SELECT `username` FROM `users` WHERE `userid` = {$_POST['user']} AND `guild` = {$gd['guild_id']}");
        if ($db->num_rows($q) == 0)
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_STAFF_VAULT_ERR3']);
            die($h->endpage());
        }
        $name = htmlentities($db->fetch_single($q), ENT_QUOTES, 'ISO-8859-1');
        $db->free_result($q);
        $api->UserGiveCurrency($_POST['user'],'primary',$_POST['primary']);
        $api->UserGiveCurrency($_POST['user'],'secondary',$_POST['secondary']);
        $db->query("UPDATE `guild` SET `guild_primcurr` = `guild_primcurr` - {$_POST['primary']},
                      `guild_seccurr` = `guild_seccurr` - {$_POST['secondary']} WHERE `guild_id` = {$gd['guild_id']}");
        $api->GameAddNotification($_POST['user'],"You were given " . number_format($_POST['primary']) . " Primary Currency and/or " . number_format($_POST['secondary']) . " Secondary Currency from your guild's vault.");
        $api->GuildAddNotification($gd['guild_id'],"<a href='profile.php?user={$userid}'>{$api->SystemUserIDtoName($userid)}</a> has given <a href='profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a> " . number_format($_POST['primary']) . " Primary Currency and/or " . number_format($_POST['secondary']) . " Secondary Currency from the guild's vault.");
        alert('success',$lang['ERROR_SUCCESS'],$lang['VIEWGUILD_STAFF_VAULT_SUCC'],true,'viewguild.php?action=staff&act2=idx');
        $api->SystemLogsAdd($userid,"guild_vault","Gave <a href='profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a> " . number_format($_POST['primary']) . " Primary Currency and/or " . number_format($_POST['secondary']) . " Secondary Currency from their guild's vault.");
    }
    else
    {
        $csrf = request_csrf_html('guild_staff_vault');
        echo "<form method='post'>
        <table class='table table-bordered'>
            <tr>
                <th colspan='2'>
                    {$lang['VIEWGUILD_STAFF_VAULT']} " . number_format($gd['guild_primcurr']) . " {$lang['INDEX_PRIMCURR']} {$lang['GEN_AND']} " . number_format($gd['guild_seccurr']) . " {$lang['INDEX_SECCURR']}.
                </th>
            </tr>
            <tr>
                <th>
                    {$lang['VIEWGUILD_STAFF_VAULT1']}
                </th>
                <td>
                    " . user3_dropdown() . "
                </td>
            </tr>
            <tr>
                <th>
                    {$lang['INDEX_PRIMCURR']}
                </th>
                <td>
                    <input type='number' class='form-control' min='0' max='{$gd['guild_primcurr']}' name='primary'>
                </td>
            </tr>
            <tr>
                <th>
                    {$lang['INDEX_SECCURR']}
                </th>
                <td>
                    <input type='number' class='form-control' min='0' max='{$gd['guild_seccurr']}' name='secondary'>
                </td>
            </tr>
            <tr>
                <td colspan='2'>
                    <input type='submit' class='btn btn-default' value='{$lang['VIEWGUILD_STAFF_VAULT_BTN']}'>
                </td>
            </tr>
            {$csrf}
        </table>
        </form>";
    }

}
function staff_coowner()
{
	global $db,$userid,$api,$h,$lang,$gd;
	if (isset($_POST['user']))
	{
		if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_coleader", stripslashes($_POST['verf'])))
        {
            alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
            die($h->endpage());
        }
		$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;
		$q = $db->query("SELECT `userid`, `username` FROM `users` WHERE `userid` = {$_POST['user']} AND `guild` = {$gd['guild_id']}");
		if ($db->num_rows($q) == 0)
		{
			$db->free_result($q);
			alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_STAFF_COLEADER_ERR']);
			die($h->endpage());
		}
		$db->free_result($q);
		$db->query("UPDATE `guild` SET `guild_coowner` = {$_POST['user']} WHERE `guild_id` = {$gd['guild_id']}");
		$api->GameAddNotification($_POST['user'],"<a href='profile.php?user={$userid}'>{$api->SystemUserIDtoName($userid)}</a> has transferred you co-leader privledges for the {$gd['guild_name']} guild.");
		$api->GuildAddNotification($gd['guild_id'],"<a href='profile.php?user={$userid}'>{$api->SystemUserIDtoName($userid)}</a> has transferred co-leader privledges to <a href='profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a>.");
		alert('success',$lang['ERROR_SUCCESS'],$lang['VIEWGUILD_STAFF_COLEADER_SUCC'],true,'viewguild.php?action=staff&act2=idx');
	}
	else
	{
		$csrf = request_csrf_html('guild_staff_coleader');
		echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					{$lang['VIEWGUILD_STAFF_COLEADER_INFO']}
				</th>
			</tr>
			<tr>
				<th>
					{$lang['ITEM_SEND_TH']}
				</th>
				<td>
					" . user3_dropdown('user',$gd['guild_coowner']) . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-default' value='{$lang['VIEWGUILD_STAFF_IDX_COOWNER']}'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>";
	}
}
function staff_announcement()
{
	global $gd,$db,$userid,$api,$lang,$h;
	if (isset($_POST['ament']))
	{
		if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_ament", stripslashes($_POST['verf'])))
        {
            alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
            die($h->endpage());
        }
		$ament = $db->escape(nl2br(htmlentities(stripslashes($_POST['ament']), ENT_QUOTES, 'ISO-8859-1')));
		$db->query("UPDATE `guild` SET `guild_announcement` = '{$ament}' WHERE `guild_id` = {$gd['guild_id']}");
		alert('success',$lang['ERROR_SUCCESS'],$lang['VIEWGUILD_STAFF_AMENT_SUCC'],true,'viewguild.php?action=staff&act2=idx');
	}
	else
	{
		$am_for_area = strip_tags($gd['guild_announcement']);
		$csrf = request_csrf_html('guild_staff_ament');
		echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					{$lang['VIEWGUILD_STAFF_AMENT_INFO']}
				</th>
			</tr>
			<tr>
				<th>
					{$lang['VIEWGUILD_HOME_ANNOUNCE']}
				</th>
				<td>
					<textarea class='form-control' name='ament' rows='7'>{$am_for_area}</textarea>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='{$lang['VIEWGUILD_STAFF_AMENT_BTN']}' class='btn btn-default'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>";
	}
}
function staff_massmail()
{
	global $db,$lang,$api,$userid,$h,$gd;
	if (isset($_POST['text']))
	{
		if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_massmail", stripslashes($_POST['verf'])))
        {
            alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
            die($h->endpage());
        }
		$_POST['text'] = (isset($_POST['text'])) ? $db->escape(htmlentities(stripslashes($_POST['text']), ENT_QUOTES, 'ISO-8859-1')) : '';
		$subj = 'Guild Mass Mail';
		$q = $db->query("SELECT `userid` FROM `users` WHERE `guild` = {$gd['guild_id']}");
		while ($r = $db->fetch_row($q))
        {
            $api->GameAddMail($r['userid'],$subj,$_POST['text'],$userid);
        }
		alert('success',$lang['ERROR_SUCCESS'],$lang['VIEWGUILD_STAFF_MM_SUCC'],true,'viewguild.php?action=staff&act2=idx');
	}
	else
	{
		$csrf = request_csrf_html('guild_staff_massmail');
		echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					{$lang['VIEWGUILD_STAFF_MM_INFO']}
				</th>
			</tr>
			<tr>
				<th>
					{$lang['PROFILE_MSG4']}
				</th>
				<td>
					<textarea class='form-control' name='text' rows='7'></textarea>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='{$lang['PROFILE_MSG6']}' class='btn btn-default'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>";
	}
}
function staff_masspayment()
{
	global $db,$lang,$api,$userid,$gd,$h;
	if (isset($_POST['payment']))
	{
		if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_masspay", stripslashes($_POST['verf'])))
        {
            alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
            die($h->endpage());
        }
		$_POST['payment'] = (isset($_POST['payment']) && is_numeric($_POST['payment'])) ? abs($_POST['payment']) : 0;
		$cnt=$db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `guild` = {$gd['guild_id']}"));
		if (($_POST['payment'] * $cnt) > $gd['guild_primcurr'])
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_STAFF_MP_ERR']);
			die($h->endpage());
		}
		else
		{
			$q = $db->query("SELECT `userid`, `username` FROM `users` WHERE `guild` = {$gd['guild_id']}");
			while ($r = $db->fetch_row($q))
			{
				if ($api->SystemCheckUsersIPs($userid,$r['userid']) == true)
				{
					alert('danger',$lang['ERROR_GENERIC'],"{$r['username']} " . $lang['VIEWGUILD_STAFF_MM_ERR2']);
				}
				else
				{
					$gd['guild_primcurr'] -= $_POST['payment'];
					$api->GameAddNotification($r['userid'],"You were given a mass-payment of {$_POST['payment']} Primary Currency from your guild.");
					$api->UserGiveCurrency($r['userid'],'primary',$_POST['payment']);
					alert('success',$lang['ERROR_SUCCESS'],"{$r['username']} " . $lang['VIEWGUILD_STAFF_MP_SUCC2']);
				}
			}
			$db->query("UPDATE `guild` SET `guild_primcurr` = {$gd['guild_primcurr']} WHERE `guild_id` = {$gd['guild_id']}");
			$notif=$db->escape("A mass payment of " . number_format($_POST['payment']) . " Primary Currency was sent out to the members of the guild.");
			$api->GuildAddNotification($gd['guild_id'],$notif);
			alert('success',$lang['ERROR_SUCCESS'],$lang['VIEWGUILD_STAFF_MP_SUCC'],true,'viewguild.php?action=staff&act2=idx');
		}
	}
	else
	{
		$csrf = request_csrf_html('guild_staff_masspay');
		echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					{$lang['VIEWGUILD_STAFF_MP_INFO']}
				</th>
			</tr>
			<tr>
				<th>
					{$lang['VIEWGUILD_STAFF_MP_TH']}
				</th>
				<td>
					<input type='number' min='1' class='form-control' name='payment'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='{$lang['VIEWGUILD_STAFF_MP_BTN']}' class='btn btn-default'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>";
	}
}
function staff_desc()
{
	global $gd,$db,$userid,$api,$lang,$h;
	if ($userid == $gd['guild_owner'])
	{
		if (isset($_POST['desc']))
		{
			if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_desc", stripslashes($_POST['verf'])))
			{
				alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
				die($h->endpage());
			}
			$desc = $db->escape(nl2br(htmlentities(stripslashes($_POST['desc']), ENT_QUOTES, 'ISO-8859-1')));
			$db->query("UPDATE `guild` SET `guild_desc` = '{$desc}' WHERE `guild_id` = {$gd['guild_id']}");
			alert('success',$lang['ERROR_SUCCESS'],$lang['VIEWGUILD_STAFF_DESC_SUCC'],true,'viewguild.php?action=staff&act2=idx');
		}
		else
		{
			$am_for_area = strip_tags($gd['guild_desc']);
			$csrf = request_csrf_html('guild_staff_desc');
			echo "<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						{$lang['VIEWGUILD_STAFF_DESC_INFO']}
					</th>
				</tr>
				<tr>
					<th>
						{$lang['GUILD_VIEW_DESC']}
					</th>
					<td>
						<textarea class='form-control' name='desc' rows='7'>{$am_for_area}</textarea>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' value='{$lang['VIEWGUILD_STAFF_DESC_BTN']}' class='btn btn-default'>
					</td>
				</tr>
				{$csrf}
			</table>
			</form>";
		}
	}
	else
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_STAFF_LEADERONLY']);
	}
}
function staff_leader()
{
	global $gd,$db,$userid,$api,$lang,$h;
	if ($userid == $gd['guild_owner'])
	{
		if (isset($_POST['user']))
	{
		if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_leader", stripslashes($_POST['verf'])))
        {
            alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
            die($h->endpage());
        }
		$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;
		$q = $db->query("SELECT `userid`, `username` FROM `users` WHERE `userid` = {$_POST['user']} AND `guild` = {$gd['guild_id']}");
		if ($db->num_rows($q) == 0)
		{
			$db->free_result($q);
			alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_STAFF_LEADER_ERR']);
			die($h->endpage());
		}
		$db->free_result($q);
		$db->query("UPDATE `guild` SET `guild_coowner` = {$_POST['user']} WHERE `guild_id` = {$gd['guild_id']}");
		$api->GameAddNotification($_POST['user'],"<a href='profile.php?user={$userid}'>{$api->SystemUserIDtoName($userid)}</a> has transferred you leader privledges for the {$gd['guild_name']} guild.");
		$api->GuildAddNotification($gd['guild_id'],"<a href='profile.php?user={$userid}'>{$api->SystemUserIDtoName($userid)}</a> has transferred leader privledges to <a href='profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a>.");
		alert('success',$lang['ERROR_SUCCESS'],$lang['VIEWGUILD_STAFF_LEADER_SUCC'],true,'viewguild.php?action=staff&act2=idx');
	}
	else
	{
		$csrf = request_csrf_html('guild_staff_leader');
		echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					{$lang['VIEWGUILD_STAFF_LEADER_INFO']}
				</th>
			</tr>
			<tr>
				<th>
					{$lang['ITEM_SEND_TH']}
				</th>
				<td>
					" . user3_dropdown('user',$gd['guild_owner']) . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-default' value='{$lang['VIEWGUILD_STAFF_IDX_LEADER']}'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>";
	}
	}
	else
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_STAFF_LEADERONLY'],true,'viewguild.php?action=staff&act2=idx');
	}
}
function staff_name()
{
	global $gd,$db,$userid,$api,$lang,$h;
	if ($userid == $gd['guild_owner'])
	{
		if (isset($_POST['name']))
		{
			if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_name", stripslashes($_POST['verf'])))
			{
				alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
				die($h->endpage());
			}
			$name = $db->escape(nl2br(htmlentities(stripslashes($_POST['name']), ENT_QUOTES, 'ISO-8859-1')));
			$cnt=$db->query("SELECT `guild_id` FROM `guild` WHERE `guild_name` = '{$name}' AND `guild_id` != {$gd['guild_id']}");
			if ($db->num_rows($cnt) > 0)
			{
				alert('danger',$lang["ERROR_GENERIC"],$lang['VIEWGUILD_STAFF_NAME_ERR']);
				die($h->endpage());
			}
			$db->query("UPDATE `guild` SET `guild_name` = '{$name}' WHERE `guild_id` = {$gd['guild_id']}");
			alert('success',$lang['ERROR_SUCCESS'],$lang['VIEWGUILD_STAFF_NAME_SUCC'],true,'viewguild.php?action=staff&act2=idx');
		}
		else
		{
			$am_for_area = strip_tags($gd['guild_name']);
			$csrf = request_csrf_html('guild_staff_name');
			echo "<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						{$lang['VIEWGUILD_STAFF_NAME_INFO']}
					</th>
				</tr>
				<tr>
					<th>
						{$lang['VIEWGUILD_STAFF_NAME_TH']}
					</th>
					<td>
						<input type='text' required='1' value='{$am_for_area}' class='form-control' name='name'>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' value='{$lang['VIEWGUILD_STAFF_NAME_BTN']}' class='btn btn-default'>
					</td>
				</tr>
				{$csrf}
			</table>
			</form>";
		}
	}
	else
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_STAFF_LEADERONLY'],true,'viewguild.php?action=staff&act2=idx');
	}
}
function staff_town()
{
	global $db,$ir,$gd,$api,$lang,$h,$userid;
	if ($userid == $gd['guild_owner'])
	{
		if (isset($_POST['town']))
		{
			if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_town", stripslashes($_POST['verf'])))
			{
				alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
				die($h->endpage());
			}
			$town = (isset($_POST['town']) && is_numeric($_POST['town'])) ? abs($_POST['town']) : 0;
			$cnt=$db->fetch_single($db->query("SELECT COUNT(`town_id`) FROM `town` WHERE `town_guild_owner` = {$gd['guild_id']}"));
			if ($cnt > 0)
			{
				alert('danger',$lang["ERROR_GENERIC"],$lang['VIEWGUILD_STAFF_TOWN_ERR']);
				die($h->endpage());
			}
			if ($db->num_rows($db->query("SELECT `town_id` FROM `town` WHERE `town_id` = {$town}")) == 0)
			{
				alert('danger',$lang["ERROR_GENERIC"],$lang['VIEWGUILD_STAFF_TOWN_ERR1']);
				die($h->endpage());
			}
			if ($db->fetch_single($db->query("SELECT `town_guild_owner` FROM `town` WHERE `town_id` = {$town}")) > 0)
			{
				alert('danger',$lang["ERROR_GENERIC"],$lang['VIEWGUILD_STAFF_TOWN_ERR2']);
				die($h->endpage());
			}
			$lowestlevel=$db->fetch_single($db->query("SELECT `level` FROM `users` WHERE `guild` = {$gd['guild_id']} ORDER BY `level` ASC LIMIT 1"));
			$townlevel=$db->fetch_single($db->query("SELECT `town_min_level` FROM `town` WHERE `town_id` = {$town}"));
			if ($townlevel > $lowestlevel)
			{
				alert('danger',$lang["ERROR_GENERIC"],$lang['VIEWGUILD_STAFF_TOWN_ERR3']);
				die($h->endpage());
			}
			$db->query("UPDATE `town` SET `town_guild_owner` = {$gd['guild_id']} WHERE `town_id` = {$town}");
			$api->GuildAddNotification($gd['guild_id'],"Your guild has successfully claimed {$api->SystemTownIDtoName($town)}.");
			alert('success',$lang['ERROR_SUCCESS'],$lang['VIEWGUILD_STAFF_TOWN_SUCC'],true,'viewguild.php?action=staff&act2=idx');
		}
		else
		{
			$csrf = request_csrf_html('guild_staff_town');
			echo "
			<form method='post'>
				<table class='table table-bordered'>
					<tr>
						<th colspan='2'>
							{$lang['VIEWGUILD_STAFF_TOWN_INFO']}
						</th>
					</tr>
					<tr>
						<th>
							{$lang['VIEWGUILD_STAFF_TOWN_TH']}
						</th>
						<td>
							" . location_dropdown('town') . "
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<input type='submit' value='{$lang['VIEWGUILD_STAFF_TOWN_BTN']}' class='btn btn-default'>
						</td>
					</tr>
					{$csrf}
				</table>
			</form>";
		}
	}
	else
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_STAFF_LEADERONLY'],true,'viewguild.php?action=staff&act2=idx');
	}
}
$h->endpage();