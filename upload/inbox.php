<?php
/*
	File:		inbox.php
	Created: 	4/5/2016 at 12:10AM Eastern Time
	Info: 		Allows players to view their inbox, write messages
				to other players.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
require('lib/bbcode_engine.php');
echo "
<div class='table-responsive'>
<table class='table table-bordered'>
	<tr>
		<td>
			<a href='inbox.php'>{$lang['MAIL_TH1_IN']}</a>
		</td>
		<td>
			<a href='?action=outbox'>{$lang['MAIL_TH1_OUT']}</a>
		</td>
		<td>
			<a href='?action=compose'>{$lang['MAIL_TH1_COMP']}</a>
		</td>
		<td>
			<a href='?action=delall'>{$lang['MAIL_TH1_DEL']}</a>
		</td>
		<td>
			<a href='?action=archive'>{$lang['MAIL_TH1_ARCH']}</a>
		</td>
		<td>
			<a href='contacts.php'>{$lang['MAIL_TH1_CONTACTS']}</a>
		</td>
	</tr>
</table>
</div>";
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
function csrf_error($goBackTo)
{
    global $h,$lang;
	echo "<div class='alert alert-danger'> <strong>{$lang['CSRF_ERROR_TITLE']}</strong> 
	{$lang['CSRF_ERROR_TEXT']} {$lang['CSRF_PREF_MENU']} <a href='inbox.php?action={$goBackTo}'>{$lang['GEN_HERE']}.</div>";
    $h->endpage();
    exit;
}
switch ($_GET['action'])
{
	case 'compose':
		compose();
		break;
	case 'read':
		read();
		break;
	case 'send':
		send();
		break;
	case 'markread':
		markasread();
		break;
	case 'delall':
		delall();
		break;
	case 'outbox':
		outbox();
		break;
	case 'archive':
		archive();
		break;
	default:
		home();
		break;
}
function home()
{
	global $db,$userid,$ir,$lang,$parser;
	echo "<table class='table table-bordered table-striped'>
	<tr>
		<th>
			{$lang['MAIL_USERDATE']}
		</th>
		<th width='50%'>
			{$lang['MAIL_PREVIEW']}
		</th>
		<th width='10%'>
			{$lang['MAIL_ACTION']}
		</th>
	</tr>";
	$MailQuery=$db->query("SELECT * FROM `mail` WHERE `mail_to` = '{$userid}' ORDER BY `mail_time` desc LIMIT 15");
	while ($r = $db->fetch_row($MailQuery))
	{
		$un1=$db->fetch_row($db->query("SELECT `username`,`display_pic` FROM `users` WHERE `userid` = {$r['mail_from']}"));
		if (empty($un1['display_pic']))
		{
			$pic='';
		}
		else
		{
			$pic="<center><img src='{$un1['display_pic']}' class='img-responsive hidden-xs' width='75'></center>";
		}
		if ($r['mail_status'] == 'unread')
		{
			$status="<span class='badge badge-pill badge-danger'>{$lang['MAIL_MSGUNREAD']}</span>";
		}
		else
		{
			$status="<span class='badge badge-pill badge-success'>{$lang['MAIL_MSGREAD']}</span>";
		}
		$msgtxt=substr($r['mail_text'], 0, 50);
		$parser->parse($msgtxt);
		echo"<tr>
				<td>
					{$pic}
					<a href='profile.php?user={$r['mail_from']}'>
						{$un1['username']}
					</a> 
					[{$r['mail_from']}]<br />
						{$lang['MAIL_SENTAT']}: " . date('F j, Y g:i:s a', $r['mail_time']) . "<br />
					{$lang['MAIL_STATUS']}: {$status}
				</td>
				<td>
					<b>{$r['mail_subject']}</b> ";
					echo $parser->getAsHtml();
					echo"...
				</td>
				<td>
					<a href='?action=read&msg={$r['mail_id']}'>{$lang['MAIL_READ']}</a><br />
					<a href='playerreport.php'>{$lang['MAIL_REPORT']}</a><br />
					<a href='?action=delete&msg={$r['mail_id']}'>{$lang['MAIL_DELETE']}</a><br />
				</td>
			</tr>";
	}
	echo"</table>
	<form action='?action=markread' method='post'>
	<input type='submit' class='btn btn-primary' value='{$lang['MAIL_MARKREAD']}'>
	</form>";
}
function read()
{
	global $db,$ir,$userid,$lang,$h,$parser;
	$code = request_csrf_code('inbox_send');
	$_GET['msg'] = (isset($_GET['msg']) && is_numeric($_GET['msg'])) ? abs($_GET['msg']) : 0;
	if (empty($_GET['msg']))
	{
		alert('danger',$lang['ERROR_SECURITY'],$lang['ERROR_MAIL_UNOWNED'],true,'inbox.php');
		die($h->endpage());
	}
	if ($db->num_rows($db->query("SELECT `mail_id` FROM `mail` WHERE `mail_id` = {$_GET['msg']} AND `mail_to` = {$userid}")) == 0)
	{
		alert("danger",$lang['ERROR_SECURITY'],$lang['ERROR_MAIL_UNOWNED'],true,'inbox.php');
		die($h->endpage());
	}
	$msg=$db->fetch_row($db->query("SELECT * FROM `mail` WHERE `mail_id` = {$_GET['msg']}"));
	$un1=$db->fetch_row($db->query("SELECT `username`,`display_pic` FROM `users` WHERE `userid` = {$msg['mail_from']}"));
	$db->query("UPDATE `mail` SET `mail_status` = 'read' WHERE `mail_id` = {$_GET['msg']}");
	$parser->parse($msg['mail_text']);
	if (empty($un1['display_pic']))
	{
		$pic='';
	}
	else
	{
		$pic="<center><img src='{$un1['display_pic']}' class='img-responsive hidden-xs' width='75'></center>";
	}
	echo "<table class='table table-bordered'>
	<tr>
		<th width='33%'>
			{$lang['MAIL_USERINFO']}
		</th>
		<th>
			{$lang['MAIL_MSGSUB']}
		</th>
	</tr>
	<tr>
		<td>
			{$pic}
			<b>{$lang['MAIL_FROM']}:</b> <a href='profile.php?user={$msg['mail_from']}'>{$un1['username']}</a><br />
			<b>{$lang['MAIL_SENTAT']}:</b> " . date('F j, Y g:i:s a', $msg['mail_time']) . "
		</td>
		<td>
			<b>{$msg['mail_subject']}</b><br />";
				echo $parser->getAsHtml();
				echo"
		</td>
	</tr>
	</table>
	<hr />";
	if (permission('CanReplyMail',$userid) == true)
	{
		echo "{$lang['MAIL_QUICKREPLY']}<br />
		<form method='post' action='?action=send'>
		<table class='table table-bordered'>
		<tr>
			<th>
				{$lang['MAIL_SENDTO']}
			</th>
			<td>
				<input type='text' class='form-control' readonly='1' name='sendto' required='1' value='{$un1['username']}'>
			</td>
		</tr>
		<tr>
			<th>
				{$lang['MAIL_SUBJECT']}
			</th>
			<td>
				<input type='text' class='form-control' maxlength='50' name='subject' value='{$msg['mail_subject']}'>
			</td>
		</tr>
		<tr>
			<th>
				{$lang['MAIL_MESSAGE']}
			</th>
			<td>
				<textarea class='form-control' rows='5' required='1' maxlength='65655' name='msg'></textarea>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<input type='submit' class='btn btn-primary'  value='{$lang['MAIL_REPLYTO']} {$un1['username']}'>
			</td>
		</tr>
		</table>
		<input type='hidden' name='verf' value='{$code}' />
		</form>";
	}
}
function send()
{
	global $db,$lang,$ir,$userid,$h;
	$subj = $db->escape(str_replace("\n", "<br />",strip_tags(stripslashes($_POST['subject']))));
	$msg = $db->escape(str_replace("\n", "<br />",strip_tags(stripslashes($_POST['msg']))));
	$sendto = (isset($_POST['sendto']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $_POST['sendto']) && ((strlen($_POST['sendto']) < 32) && (strlen($_POST['sendto']) >= 3))) ? $_POST['sendto'] : '';
	if (!isset($_POST['verf']) || !verify_csrf_code('inbox_send', stripslashes($_POST['verf'])))
	{
		csrf_error('');
	}
	if (empty($msg))
    {
		alert('danger',$lang['ERROR_EMPTY'],$lang['MAIL_EMPTYINPUT'],true,'inbox.php?action=compose');
        die($h->endpage());
    }
	elseif ((strlen($msg) > 65655) || (strlen($subj) > 50))
    {
        alert('danger',$lang['ERROR_LENGTH'],$lang['MAIL_INPUTLNEGTH'],true,'inbox.php?action=compose');
        die($h->endpage());
    }
	 if (empty($_POST['sendto']))
    {
		alert('danger',$lang['ERROR_EMPTY'],$lang['MAIL_NOUSER'],true,'inbox.php?action=compose');
        die($h->endpage());
    }
	$q = $db->query("SELECT `userid` FROM `users` WHERE `username` = '{$sendto}'");
	if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
		alert('danger',$lang['MAIL_UDNE'],$lang['MAIL_UDNE_TEXT'],true,'inbox.php?action=compose');
        die($h->endpage());
    }
	$to = $db->fetch_single($q);
    $db->free_result($q);
	$time=time();
	$db->query("INSERT INTO `mail` 
	(`mail_id`, `mail_to`, `mail_from`, `mail_status`, `mail_subject`, `mail_text`, `mail_time`) 
	VALUES (NULL, '{$to}', '{$userid}', 'unread', '{$subj}', '{$msg}', '{$time}');");
	alert('success',$lang['ERROR_SUCCESS'],$lang['MAIL_SUCCESS'],true,'inbox.php');
}
function markasread()
{
	global $db,$h,$userid,$h,$lang;
	$db->query("UPDATE `mail` SET `mail_status` = 'read' WHERE `mail_to` = {$userid}");
	alert('success',$lang['ERROR_SUCCESS'],$lang['MAIL_READALL'],false);
	home();
}
function delall()
{
	global $db,$lang,$h,$userid;
	if (empty($_POST['delete']))
	{
		echo $lang['MAIL_DELETECONFIRM'];
		echo "<br />
		<form method='post'>
			<input type='submit' name='delete' class='btn btn-primary' value='{$lang['MAIL_DELETEYES']}'>
		</form>
		<form method='post' action='inbox.php'>
			<input type='submit' class='btn btn-danger' value='{$lang['MAIL_DELETENO']}'>
		</form>";
	}
	else
	{
		$db->query("DELETE FROM `mail` WHERE `mail_to` = {$userid}");
		alert('success',$lang['ERROR_SUCCESS'],$lang['MAIL_DELETEDONE'],true,'inbox.php');
	}
}
function outbox()
{
	global $db,$lang,$userid,$lang,$h,$parser;
	echo "	<table class='table table-bordered table-hover table-striped'>
				<thead>
					<th width='33%'>
						{$lang['MAIL_USERDATE']}
					</th>
					<th>
						{$lang['MAIL_MSGSUB']}
					</th>
				</thead>
				<tbody>";
				$query=$db->query("SELECT * FROM `mail` WHERE `mail_from` = {$userid} ORDER BY `mail_time` desc LIMIT 15");
				while ($msg = $db->fetch_row($query))
				{
					$sent=date('F j, Y g:i:s a', $msg['mail_time']);
					$sentto=$db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$msg['mail_to']}"));
					$parser->parse($msg['mail_text']);
					if ($msg['mail_status'] == 'unread')
					{
						$status="<span class='label label-danger'>{$lang['MAIL_MSGUNREAD']}</span>";
					}
					else
					{
						$status="<span class='label label-primary'>{$lang['MAIL_MSGREAD']}</span>";
					}
					echo "<tr>
							<td>
								<b>{$lang['MAIL_SENDTO']}:</b> <a href='profile.php?user={$msg['mail_to']}'>{$sentto}</a><br />
								<b>{$lang['MAIL_SENTAT']}: </b>{$sent}<br />
								<b>{$lang['MAIL_STATUS']}:</b> {$status}<br />
							</td>
							<td>
								<b>{$msg['mail_subject']}</b> ";
								echo $parser->getAsHtml();
								echo"
							</td>
					
					</tr>";
				}
			echo "</tbody></table>'";
}
function compose()
{
	global $db,$userid,$lang,$h;
	$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : 0;
	if (isset($_GET['user']) && ($_GET['user'] > 0))	
	{
		$username=$db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$_GET['user']}"));
	}
	else
	{
		$username='';
	}
	if (permission('CanReplyMail',$userid) == true)
	{
		$code = request_csrf_code('inbox_send');
		echo "
		<form method='post' action='?action=send'>
		<table class='table table-bordered'>
		<tr>
			<th>
				{$lang['MAIL_SENDTO']}
			</th>
			<td>
				<input type='text' class='form-control' value='{$username}' name='sendto' required='1'>
			</td>
		</tr>
		<tr>
			<th>
				{$lang['MAIL_SUBJECT']}
			</th>
			<td>
				<input type='text' class='form-control' maxlength='50' name='subject'>
			</td>
		</tr>
		<tr>
			<th>
				{$lang['MAIL_MESSAGE']}
			</th>
			<td>
				<textarea class='form-control' rows='5' required='1' maxlength='65655' name='msg'></textarea>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<input type='submit' class='btn btn-primary'  value='{$lang['MAIL_SENDMSG']}'>
			</td>
		</tr>
		</table>
		<input type='hidden' name='verf' value='{$code}' />
		</form>";
	}
}
function archive()
{
	global $lang;
	echo "<table class='table table-bordered'>
	<tr>
		<th colspan='2'>
			{$lang['MAIL_TH1_ARC']}
		</th>
	</tr>
	<tr>
		<td>
			<form method='post' action='dlarchive.php'>
				<input type='hidden' name='archive' value='inbox' />
				<input type='submit' value='{$lang['MAIL_TH1_ARC1']}' class='btn btn-primary'>
			</form>
		</td>
		<td>
			<form method='post' action='dlarchive.php'>
				<input type='hidden' name='archive' value='outbox' />
				<input type='submit' value='{$lang['MAIL_TH1_ARC2']}' class='btn btn-primary'>
			</form>
		</td>
	</tr>
	</table>";
}
$h->endpage();