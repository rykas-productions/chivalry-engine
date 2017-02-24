<?php
require('globals.php');
if (!$ir['guild'])
{
    alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['VIEWGUILD_ERROR1']}");
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
    			<td>
       ";
    if ($gd['guild_owner'] == $userid || $gd['guild_coowner'] == $userid)
    {
        echo "<a href='?action=staff&act2=idx'>{$lang['VIEWGUILD_HOME_STAFF']}</a>";
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
			" . number_format($gd['guild_primcurr']) . "
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
			alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
			die($h->endpage());
		}
		if (empty($_POST['primary']) && empty($_POST['secondary']))
		{
			alert('danger',"{$lang["ERROR_GENERIC"]}","{$lang['VIEWGUILD_DONATE_ERR1']}");
			die($h->endpage());
		}
		if ($_POST['primary'] > $ir['primary_currency'])
		{
			alert('danger',"{$lang["ERROR_GENERIC"]}","{$lang['VIEWGUILD_DONATE_ERR2']}");
			die($h->endpage());
		}
		else if ($_POST['secondary'] > $ir['secondary_currency'])
		{
			alert('danger',"{$lang["ERROR_GENERIC"]}","{$lang['VIEWGUILD_DONATE_ERR3']}");
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
			alert('success',"{$lang["ERROR_SUCCESS"]}","{$lang['VIEWGUILD_DONATE_SUCC']}");
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
			alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
			die($h->endpage());
		}
        $_POST['ID'] = (isset($_POST['ID']) && is_numeric($_POST['ID'])) ? abs(intval($_POST['ID'])) : 0;
        $who = $_POST['ID'];
        if ($who == $gd['guild_owner'])
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_KICK_ERR']);
        }
        else if ($who == $userid)
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_KICK_ERR1']);
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
                alert('success',$lang['ERROR_SUCCESS'],$lang['VIEWGUILD_KICK_SUCCESSS']);
                $their_event = "You were kicked out of the {$gd['guild_name']} guild by <a href='profile.php?user={$userid}'>{$d_oname}</a>.";
                notification_add($who, $their_event);
                $gang_event =  $db->escape("<a href='profile.php?user={$who}'>{$d_username}</a> was kicked out of the guild by <a href='profile.php?user={$userid}'>{$d_oname}</a>.");
                $db->query("INSERT INTO `guild_notifications` VALUES(NULL, {$gd['guild_id']}, " . time() . ", '{$gang_event}');");
            }
            else
            {
                alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_KICK_ERR2']);
            }
            $db->free_result($q);
        }
    }
    else
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_KICK_ERR3']);
    }
}
function leave()
{
	global $db,$userid,$ir,$gd,$lang,$api,$h;
	if ($gd['guild_owner'] == $userid || $gd['guild_coowner'] == $userid)
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_LEAVE_ERR']);
        die($h->endpage());
    }
	if (isset($_POST['submit']) && $_POST['submit'] == 'yes')
    {
		if (!isset($_POST['verf']) || !verify_csrf_code("guild_leave", stripslashes($_POST['verf'])))
		{
			alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
			die($h->endpage());
		}
		$db->query("UPDATE `users` SET `guild` = 0  WHERE `userid` = {$userid}");
		$api->GuildAddNotification($ir['guild'],"<a href='profile.php?user={$userid}'>{$ir['username']}</a> has left the guild.");
		alert('success',$lang['ERROR_SUCCESS'],$lang['VIEWGUILD_LEAVE_SUCC']);
	}
	elseif (isset($_POST['submit']) && $_POST['submit'] == 'no')
	{
		alert('success',$lang['ERROR_SUCCESS'],$lang['VIEWGUILD_LEAVE_SUCC1']);
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
			default:
				staff_idx();
				break;
        }
    }
    else
    {
        alert('danger',$lang['ERROR_NOPERM'],$lang['VIEWGUILD_STAFF_ERROR']);
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
			<a href='?action=staff&act2=apps'>{$lang['VIEWGUILD_STAFF_IDX_APP']}</a>
		</td>";
	if ($gd['guild_owner'] == $userid)
	{
		echo "
		<td>
			<b>{$lang['VIEWGUILD_SUMMARY_OWNER']}</b>
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
        /*if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_apps", stripslashes($_POST['verf'])))
		{
			alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
			die($h->endpage());
		}*/
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
                notification_add($appdata['ga_user'],"We regret to inform you that your application to join the {$gd['guild_name']} guild was declined.");
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
                else if ($appdata['guild'] != 0)
                {
                    $db->free_result($cnt);
                    alert('danger',$lang['ERROR_GENERIC'],$lang['VIEWGUILD_STAFF_APP_ACC_ERR1']);
                    die($h->endpage());
                }
                $db->free_result($cnt);
                $db->query("DELETE FROM `guild_applications` WHERE `ga_id` = {$_POST['app']}");
                notification_add($appdata['ga_user'], "Your application to join the {$gd['guild_name']} guild was accepted.");
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
$h->endpage();