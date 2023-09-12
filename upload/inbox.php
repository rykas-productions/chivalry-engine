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
//Include BBCode Engine. Allow players to make pretty!
require('lib/bbcode_engine.php');

//See if user is mail-banned
$q2 = $db->query("/*qc=on*/SELECT * FROM `mail_bans` WHERE `mbUSER` = {$userid}");
if ($db->num_rows($q2) != 0) {
    $r = $db->fetch_row($q2);
    $r['days'] = TimeUntil_Parse($r['mbTIME']);
    alert('danger', "Uh Oh!", "You've been mail-banned for {$r['days']}. Reason: {$r['mbREASON']}", true, 'index.php');
    die($h->endpage());
}
echo "
<div class='row'>
    <div class='col-auto'>
        <a href='inbox.php' class='updateHoverBtn btn btn-primary btn-block'><i class='fas fa-fw fa-inbox'></i> Inbox</a>
        <br />
    </div>
    <div class='col-auto'>
        <a href='inbox.php?action=outbox' class='updateHoverBtn btn btn-warning btn-block'><i class='fas fa-fw fa-envelope'></i> Outbox</a>
        <br />
    </div>
    <div class='col-auto'>
        <a href='inbox.php?action=compose' class='updateHoverBtn btn btn-success btn-block'><i class='fas fa-fw fa-file'></i> Compose</a>
        <br />
    </div>
    <div class='col-auto'>
        <a href='blocklist.php' class='updateHoverBtn btn btn-secondary btn-block'><i class='fas fa-fw fa-ban'></i> Blocklist</a>
        <br />
    </div>
    <div class='col-auto'>
        <a href='inbox.php?action=delall' class='updateHoverBtn btn btn-danger btn-block'><i class='fas fa-fw fa-trash-alt'></i> Delete All</a>
        <br />
    </div>
    <div class='col-auto'>
        <a href='inbox.php?action=archive' class='updateHoverBtn btn btn-dark btn-block'><i class='fas fa-fw fa-save'></i> Archive</a>
        <br />
    </div>
    <div class='col-auto'>
        <a href='contacts.php' class='updateHoverBtn btn btn-info btn-block'><i class='fas fa-fw fa-address-book'></i> Contacts</a>
        <br />
    </div>
</div>";
//GET is empty. Bind it to view the main inbox.
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
//Switch to list all possible actions.
switch ($_GET['action']) {
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
    case 'delete':
        delete();
        break;
    default:
        home();
        break;
}
//Main inbox.
function home()
{
    global $db, $userid, $ir;
	//Select last 15 messages that were sent to the current player and display to the player.
	$viewCount=getCurrentUserPref('mailView', 15);
	if ($ir['mail'] == 0)
	{
		$MailQuery = $db->query("/*qc=on*/SELECT * FROM `mail` WHERE `mail_to` = '{$userid}' ORDER BY `mail_time` desc LIMIT {$viewCount}");
		$info="Showing your {$viewCount} latest messages.";
	}
	else
	{
		$MailQuery = $db->query("/*qc=on*/SELECT * FROM `mail` WHERE `mail_to` = '{$userid}' AND `mail_status` = 'Unread' ORDER BY `mail_time` desc");
		$info="Showing your unread messages.";
	}
	echo "<b>{$info}</b>";
    while ($r = $db->fetch_row($MailQuery)) {
        //Bind their picture to a variable... if they have one.
        //Bind if the message has been previously read or not.
        $status = ($r['mail_status'] == 'unread') ? $unread=1 : $unread=0;
        $un1['username'] = parseUsername($r['mail_from']);
		if ($status == 1)
		{
			$sub = ($r['mail_subject']) ? "<b>{$r['mail_subject']}</b><br />" : "<b><i>No Subject</i></b>";
		}
		else
		{
			$sub = ($r['mail_subject']) ? "{$r['mail_subject']}<br />" : "<i>No Subject</i>";
		}
        //Grab the first 50 characters of the message for the message preview.
        //BBCode parse the preview.
		echo "<div class='card'>
			<div class='card-header bg-transparent'>
				<div class='row'>
					<div class='col-md-2 col-sm-4 col-6'>
						<a href='profile.php?user={$r['mail_from']}' class='updateHoverBtn'>{$un1['username']}</a> [{$r['mail_from']}]
					</div>
					<div class='col-md-3 text-muted hidden-sm-down'>
						" . DateTime_Parse($r['mail_time']) . "
					</div>
					<div class='col-6 col-sm-4'>
						<i><a href='?action=read&msg={$r['mail_id']}' class='updateHoverBtn'>{$sub}</a></i>
					</div>
					<div class='col-md-3 col-sm-4 col-6 hidden-xs-down'>
						<div class='row'>
							<div class='col'>
								<a class='btn btn-primary btn-sm updateHoverBtn' href='?action=read&msg={$r['mail_id']}'><i class='far fa-envelope-open'></i></a>
							</div>
							<div class='col'>
								<a class='btn btn-primary btn-sm updateHoverBtn' href='playerreport.php?userid={$r['mail_from']}'><i class='fas fa-flag'></i></a>
							</div>
							<div class='col'>
								<a class='btn btn-primary btn-sm updateHoverBtn' href='?action=delete&msg={$r['mail_id']}'><i class='fas fa-trash-alt'></i></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>";
    }
    echo "<br /><form action='?action=markread' method='post'>
	<input type='submit' class='btn btn-primary btn-block' value='Mark All as Read'>
	</form>";
}

function read()
{
    global $db, $userid, $h, $parser, $ir;
    //Request CSRF code for if the user wishes to send a reply.
    $code = request_csrf_code('inbox_send');
    //Grab the message ID from GET.
    $_GET['msg'] = (isset($_GET['msg']) && is_numeric($_GET['msg'])) ? abs($_GET['msg']) : 0;
    //Message ID is empty.
    if (empty($_GET['msg'])) {
        alert('danger', "Uh Oh!", "This message is non-existent, or does not belong to you.", true, 'inbox.php');
        die($h->endpage());
    }
    //Message does not exist, or does not belong to the current player.
    if ($db->num_rows($db->query("/*qc=on*/SELECT `mail_id` FROM `mail` WHERE `mail_id` = {$_GET['msg']} AND `mail_to` = {$userid}")) == 0) {
        alert("danger", "Uh Oh!", "This message is non-existent, or does not belong to you.", true, 'inbox.php');
        die($h->endpage());
    }
    //Grab all message data from the database for this message
    $msg = $db->fetch_row($db->query("/*qc=on*/SELECT * FROM `mail` WHERE `mail_id` = {$_GET['msg']}"));
	$lstmsg=$db->fetch_row($db->query("/*qc=on*/SELECT * FROM `mail` WHERE `mail_from` = {$userid} AND `mail_to` = {$msg['mail_from']} AND `mail_time` < {$msg['mail_time']} ORDER BY `mail_id` desc LIMIT 1"));
    //Grab sending player's username and display picture.
    $un1 = $db->fetch_row($db->query("/*qc=on*/SELECT `username`,`display_pic`,`vip_days`,`vipcolor` FROM `users` WHERE `userid` = {$msg['mail_from']}"));
    $un1['usernames'] = parseUsername($msg['mail_from']);
    $ir['username'] = parseUsername($userid);
    //Update message to reflect that it has been read.
    $db->query("UPDATE `mail` SET `mail_status` = 'read' WHERE `mail_id` = {$_GET['msg']}");
    //BBCode parse the message.
    $currentmsg=$parser->parse(decrypt_message($msg['mail_text'],$msg['mail_from'],$userid));
	$currentmsg=$parser->getAsHtml();
	$urmsg=$parser->parse(html_entity_decode(decrypt_message($lstmsg['mail_text'],$userid,$msg['mail_from'])));
	$urmsg=$parser->getAsHtml();
    //Show sender's picture... if they have one.
	$pic = "<img src='" . parseDisplayPic($msg['mail_from']) . "' height='75' alt='{$un1['username']}&#39;s Display picture.' title='{$un1['username']}&#39;s Display picture'>";
	echo "
	<div class='card'>
		<div class='card-header bg-transparent'>
			<div class='row'>
				<div class='col-md-1 col-4'>
					{$pic}
				</div>
				<div class='col-md-3 col-8'>
					<a href='profile.php?user={$msg['mail_from']}' class='updateHoverBtn'>{$un1['usernames']}</a> [{$msg['mail_from']}]<br />
					" . DateTime_Parse($msg['mail_time']) . "
				</div>
				<div class='col-md-8'>
					<hr class='hidden-md-up'>
					<b>Subject: {$msg['mail_subject']}</b><br />
					{$currentmsg}
				</div>
			</div>";
			if (permission('CanReplyMail', $userid)) 
			{
				echo"
				<form method='post' action='?action=send'>
				<hr />
				<div class='row'>
					<div class='col-md-2 col-sm-3 col-6'>
						<b>Recipient</b>
					</div>
					<div class='col-md-10 col-sm-9'>
						<input type='text' class='form-control' readonly='1' name='sendto' required='1' value='{$un1['username']}'>
					</div>
				</div>
				<br />
				<div class='row'>
					<div class='col-md-2 col-sm-3 col-6'>
						<b>Subject</b>
					</div>
					<div class='col-md-10 col-sm-9'>
						<input type='text' class='form-control' maxlength='50' name='subject' value='{$msg['mail_subject']}'>
					</div>
				</div>
				<br />
				<div class='row'>
					<div class='col-md-2 col-sm-3 col-6'>
						<b>Response</b>
					</div>
					<div class='col-md-10 col-sm-9'>
						<textarea class='form-control' required='1' maxlength='65655' name='msg'></textarea>
					</div>
				</div>
				<br />
				<div class='row'>
					<div class='col'>
						<button class='btn btn-primary btn-block' type='submit'><i class='fas fa-reply'></i> Reply to {$un1['username']}</button>
					</div>
				</div>
				<input type='hidden' name='verf' value='{$code}' />
				</form>";
			}
			echo"
		</div>
	</div>";
}

function send()
{
    global $db, $userid, $h, $api;
    //Clean and sanitize the POST.
    $subj = $db->escape(str_replace("\n", "<br />", strip_tags(htmlentities(stripslashes($_POST['subject'])))));
    $msg = $db->escape(str_replace("\n", "<br />", strip_tags(htmlentities(stripslashes($_POST['msg'])))));
	$sendto = $db->escape(strip_tags(htmlentities(stripslashes($_POST['sendto']))));
    //Player failed the CSRF check... warn them to be quicker next time... or to change their password.
    if (!isset($_POST['verf']) || !verify_csrf_code('inbox_send', stripslashes($_POST['verf']))) {
        alert('danger', "Action Blocked!", "Your action has been blocked for security reasons. Form requests expire fairly quickly. Be sure to be quicker next time.");
        die($h->endpage());
    }
    //Message is empty... do not send message.
    if (empty($msg)) {
        alert('danger', "Uh Oh!", "Please enter a message before submitting the form.", true, 'inbox.php?action=compose');
        die($h->endpage());
    } //Message too long. Don't send the message.
    elseif ((strlen($msg) > 65655) || (strlen($subj) > 50)) {
        alert('danger', "Uh Oh!", "Your subject and/or message is too long. They can only be 50 and/or 65655
            characters in length, in that order.", true, 'inbox.php?action=compose');
        die($h->endpage());
    }
    //Player didn't specify another player to send this message to
    if (empty($_POST['sendto'])) {
        alert('danger', "Uh Oh!", "You are trying to send a message to an invalid or non-existent user.", true, 'inbox.php?action=compose');
        die($h->endpage());
    }
    //Grab the receiving player's information.
    $q = $db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `username` = '{$sendto}'");
    //Receiving player does not exist.
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        alert('danger', "Uh Oh!", "You are trying to send a message to an invalid or non-existent user.", true, 'inbox.php?action=compose');
        die($h->endpage());
    }
    //Bind the receiving user's ID to a variable.
    $to = $db->fetch_single($q);
    $db->free_result($q);
    $time = time();
	if ($api->UserBlocked($userid,$to))
	{
		alert('danger', "Uh Oh!", "This user has you blocked. You cannot send messages to players that have you blocked.", true, 'inbox.php?action=compose');
        die($h->endpage());
	}
	$input=$msg;
    $msg=encrypt_message($msg,$userid,$to);
    //Insert message into database so receiving player can view it later.
    $db->query("INSERT INTO `mail`
	(`mail_id`, `mail_to`, `mail_from`, `mail_status`, `mail_subject`, `mail_text`, `mail_time`) 
	VALUES (NULL, '{$to}', '{$userid}', 'unread', '{$subj}', '{$msg}', '{$time}');");
    alert('success', "Success!", "Message has been sent successfully", false);
	//Mailban the user if needed?
	$fiveminago=time()-300;
	$lastthreemsg=$db->query("/*qc=on*/SELECT * 
								FROM `mail` 
								WHERE `mail_from` = {$userid} 
								AND `mail_time` >= {$fiveminago}");
	$same=0;
	while ($ltr = $db->fetch_row($lastthreemsg))
	{
		$decrypt=decrypt_message($ltr['mail_text'],$userid,$ltr['mail_to']);
		if ($decrypt == $input)
			$same=$same+1;
	}
	if ($same >= 7)
	{
		$timed=time()+259200;
		$db->query("INSERT INTO `mail_bans`
                    (`mbUSER`, `mbREASON`, `mbBANNER`, `mbTIME`) VALUES
                    ('{$userid}', 'Spamming', '1', '{$timed}')");
		$api->GameAddNotification($userid, "You have been mail-banned for 3 days for the reason: 'Spamming'.");
		staffnotes_entry($userid,"Mail banned for 3 for 'Spamming'.",0);
	}
    home();
}

function markasread()
{
    global $db, $userid;
    //Set the current user's messages as all read.
    $db->query("UPDATE `mail` SET `mail_status` = 'read' WHERE `mail_to` = {$userid}");
    alert('success', "Success!", "All of your messages has been set to 'Read'.", false);
    home();
}

function delall()
{
    global $db, $userid;
    //Display the form to delete everything.
    if (empty($_POST['delete'])) 
	{
        echo "Are you sure you want to empty your inbox? This cannot be undone.";
        echo "<br />";
		echo "<div class='row'>
			<div class='col'>
				<form method='post'>
					<input type='submit' name='delete' class='btn btn-primary btn-block' value='Delete Inbox'>
				</form>
			</div>
			<div class='col'>
				<form method='post' action='inbox.php'>
					<input type='submit' class='btn btn-danger btn-block' value='Nevermind'>
				</form>
			</div>
		</div>";
    } 
	else 
	{
        //Delete all messages that were sent to the current player.
        $db->query("DELETE FROM `mail` WHERE `mail_to` = {$userid}");
        alert('success', "Success!", "You have successfully cleaned out your inbox.", true, 'inbox.php');
    }
}

function outbox()
{
    global $db, $userid, $parser;
    //Grab all the messages the current player has writen and display them to the user.
    $query = $db->query("/*qc=on*/SELECT * FROM `mail` WHERE `mail_from` = {$userid} ORDER BY `mail_time` desc LIMIT 15");
    while ($msg = $db->fetch_row($query)) {
        $sent = DateTime_Parse($msg['mail_time']);
        //Grab recipient's user name.
        $sentto = parseUsername($msg['mail_to']);
		$sub = ($msg['mail_subject']) ? "<b><u>Subject: {$msg['mail_subject']}</u></b><br />" : "";
        //Parse message with BBCode.
        $msg['mail_text']=decrypt_message($msg['mail_text'],$userid,$msg['mail_to']);
        $parser->parse($msg['mail_text']);
        $status = ($msg['mail_status'] == 'unread') ?
            "<span class='badge badge-pill badge-danger'>Unread</span>" :
            "<span class='badge badge-pill badge-success'>Read</span>";
	echo "
		<div class='card'>
			<div class='card-header bg-transparent'>
				<div class='row'>
					<div class='col'>
						{$sub}
						" . $parser->getAsHtml() . "
					</div>
				</div>
				<small>
				<div class='row text-muted'>
						<div class='col'>
							<a href='profile.php?user={$msg['mail_to']}'>{$sentto}</a>
						</div>
						<div class='col'>
							{$sent}
						</div>
						<div class='col'>
							{$status}
						</div>
				</div>
				</small>
			</div>
		</div>";
    }
}

function compose()
{
    global $db, $userid;
    //Sanitize GET
    $_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : 0;
    //GET is set and greater than 0, so let's fetch the username associated that's on the GET.
    $username = (isset($_GET['user']) && ($_GET['user'] > 0)) ?
        $username = $db->fetch_single($db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `userid` = {$_GET['user']}")) :
        '';
    //Permission check to see if player can send mail.
    if (permission('CanReplyMail', $userid)) {
        //Request CSRF Code and display the message composer form.
        $code = request_csrf_code('inbox_send');
		echo "
		<form method='post' action='?action=send'>
		<div class='card'>
			<div class='card-header bg-transparent'>
				<div class='row'>
					<div class='col-md-2 col-sm-3 col-6'>
						<b>Recipient</b>
					</div>
					<div class='col-md-10 col-sm-9'>
						<input type='text' class='form-control' value='{$username}' name='sendto' required='1'>
					</div>
				</div>
				<br />
				<div class='row'>
					<div class='col-md-2 col-sm-3 col-6'>
						<b>Subject</b>
					</div>
					<div class='col-md-10 col-sm-9'>
						<input type='text' class='form-control' maxlength='50' name='subject'>
					</div>
				</div>
				<br />
				<div class='row'>
					<div class='col-md-2 col-sm-3 col-6'>
						<b>Message</b>
					</div>
					<div class='col-md-10 col-sm-9'>
						<textarea class='form-control' rows='5' required='1' maxlength='65655' name='msg'></textarea>
					</div>
				</div>
				<br />
				<div class='row'>
					<div class='col'>
						<input type='submit' class='btn btn-primary btn-block'  value='Send Message'>
					</div>
				</div>
			</div>
		</div>
		<input type='hidden' name='verf' value='{$code}' />
		</form>";
    }
}

function archive()
{
	echo "
	We at Chivalry is Dead delete read messages when they're 30 days old. Here, you may save your messages for whatever reason. We don't care why.
	<div class='row'>
		<div class='col'>
			<form method='post' action='dlarchive.php'>
				<input type='hidden' name='archive' value='inbox' />
				<input type='submit' value='Inbox' class='btn btn-primary btn-block'>
			</form>
		</div>
		<div class='col'>
			<form method='post' action='dlarchive.php'>
				<input type='hidden' name='archive' value='outbox' />
				<input type='submit' value='Outbox' class='btn btn-primary btn-block'>
			</form>
		</div>
	</div>";
}

function delete()
{
    global $db, $userid, $h;
    //Sanitize the GET.
    $_GET['msg'] = (isset($_GET['msg']) && is_numeric($_GET['msg'])) ? abs($_GET['msg']) : 0;
    //Message ID isn't set.
    if (empty($_GET['msg'])) {
        alert('danger', "Uh Oh!", "This message is non-existent, or does not belong to you.", false);
        home();
        die($h->endpage());
    }
    //Message does not exist, or does not belong to the current player.
    if ($db->num_rows($db->query("/*qc=on*/SELECT `mail_id` FROM `mail` WHERE `mail_id` = {$_GET['msg']} AND `mail_to` = {$userid}")) == 0) {
        alert("danger", "Uh Oh!", "This message is non-existent, or does not belong to you.", false);
        home();
        die($h->endpage());
    }
    //Delete message.
    $db->query("DELETE FROM `mail` WHERE `mail_id` = {$_GET['msg']}");
    alert('success', "Success!", "Message has been deleted successfully.", false);
    home();
}
$h->endpage();