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
echo "
<div class='table-responsive'>
<table class='table table-bordered'>
	<tr>
		<td>
			<a href='inbox.php'>Inbox</a>
		</td>
		<td>
			<a href='?action=outbox'>Outbox</a>
		</td>
		<td>
			<a href='?action=compose'>Compose</a>
		</td>
		<td>
			<a href='?action=delall'>Delete All</a>
		</td>
		<td>
			<a href='?action=archive'>Archive</a>
		</td>
		<td>
			<a href='contacts.php'>Contacts</a>
		</td>
	</tr>
</table>
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
    global $db, $userid, $parser;
    echo "<table class='table table-bordered table-striped'>
	<tr>
		<th>
			Sender Info
		</th>
		<th width='50%'>
			Message Preview
		</th>
		<th width='10%'>
			Actions
		</th>
	</tr>";
    //Select last 15 messages that were sent to the current player and display to the player.
    $MailQuery = $db->query("SELECT * FROM `mail` WHERE `mail_to` = '{$userid}' ORDER BY `mail_time` desc LIMIT 15");
    while ($r = $db->fetch_row($MailQuery)) {
        //Select sender's username and display picture.
        $un1 = $db->fetch_row($db->query("SELECT `username`,`display_pic` FROM `users` WHERE `userid` = {$r['mail_from']}"));
        //Bind their picture to a variable... if they have one.
        $pic = (empty($un1['display_pic'])) ? "" :
            "<center><img src='{$un1['display_pic']}' class='img-fluid hidden-xs' width='75'></center>";
        //Bind if the message has been previously read or not.
        $status = ($r['mail_status'] == 'unread') ?
            "<span class='badge badge-pill badge-danger'>Unread</span>" :
            "<span class='badge badge-pill badge-success'>Read</span>";
        //Grab the first 50 characters of the message for the message preview.
        $msgtxt = substr($r['mail_text'], 0, 50);
        //BBCode parse the preview.
        $parser->parse($msgtxt);
        echo "<tr>
				<td>
					{$pic}
					<a href='profile.php?user={$r['mail_from']}'>
						{$un1['username']}
					</a> 
					[{$r['mail_from']}]<br />
						Sent At: " . date('F j, Y g:i:s a', $r['mail_time']) . "<br />
					Status: {$status}
				</td>
				<td>
					<b>{$r['mail_subject']}</b> ";
        echo $parser->getAsHtml();
        echo "...
				</td>
				<td>
					<a href='?action=read&msg={$r['mail_id']}'>Read</a><br />
					<a href='playerreport.php'>Report</a><br />
					<a href='?action=delete&msg={$r['mail_id']}'>Delete</a><br />
				</td>
			</tr>";
    }
    echo "</table>
	<form action='?action=markread' method='post'>
	<input type='submit' class='btn btn-primary' value='Mark All as Read'>
	</form>";
}

function read()
{
    global $db, $userid, $h, $parser;
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
    if ($db->num_rows($db->query("SELECT `mail_id` FROM `mail` WHERE `mail_id` = {$_GET['msg']} AND `mail_to` = {$userid}")) == 0) {
        alert("danger", "Uh Oh!", "This message is non-existent, or does not belong to you.", true, 'inbox.php');
        die($h->endpage());
    }
    //Grab all message data from the database for this message
    $msg = $db->fetch_row($db->query("SELECT * FROM `mail` WHERE `mail_id` = {$_GET['msg']}"));
    //Grab sending player's username and display picture.
    $un1 = $db->fetch_row($db->query("SELECT `username`,`display_pic` FROM `users` WHERE `userid` = {$msg['mail_from']}"));
    //Update message to reflect that it has been read.
    $db->query("UPDATE `mail` SET `mail_status` = 'read' WHERE `mail_id` = {$_GET['msg']}");
    //BBCode parse the message.
    $parser->parse($msg['mail_text']);
    //Show sender's picture... if they have one.
    $pic = (empty($un1['display_pic'])) ? "" :
        "<center><img src='{$un1['display_pic']}' class='img-fluid hidden-xs' width='75'></center>";
    echo "<table class='table table-bordered'>
	<tr>
		<th width='33%'>
			Sender Info
		</th>
		<th>
			Subject: {$msg['mail_subject']}
		</th>
	</tr>
	<tr>
		<td>
			{$pic}
			<b>From:</b> <a href='profile.php?user={$msg['mail_from']}'>{$un1['username']}</a><br />
			<b>Sent:</b> " . date('F j, Y g:i:s a', $msg['mail_time']) . "
		</td>
		<td>";
    echo $parser->getAsHtml();
    echo "
		</td>
	</tr>
	</table>
	<hr />";
    //Permission check to see if the current player can reply to messages. If they can, show the reply form.
    if (permission('CanReplyMail', $userid)) {
        echo "Quick Reply Form<br />
		<form method='post' action='?action=send'>
		<table class='table table-bordered'>
		<tr>
			<th>
				To
			</th>
			<td>
				<input type='text' class='form-control' readonly='1' name='sendto' required='1' value='{$un1['username']}'>
			</td>
		</tr>
		<tr>
			<th>
				Subject
			</th>
			<td>
				<input type='text' class='form-control' maxlength='50' name='subject' value='{$msg['mail_subject']}'>
			</td>
		</tr>
		<tr>
			<th>
				Message
			</th>
			<td>
				<textarea class='form-control' rows='5' required='1' maxlength='65655' name='msg'></textarea>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<input type='submit' class='btn btn-primary'  value='Reply to {$un1['username']}'>
			</td>
		</tr>
		</table>
		<input type='hidden' name='verf' value='{$code}' />
		</form>";
    }
}

function send()
{
    global $db, $userid, $h;
    //Clean and sanitize the POST.
    $subj = $db->escape(str_replace("\n", "<br />", strip_tags(stripslashes($_POST['subject']))));
    $msg = $db->escape(str_replace("\n", "<br />", strip_tags(stripslashes($_POST['msg']))));
    $sendto = (isset($_POST['sendto']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
            $_POST['sendto']) && ((strlen($_POST['sendto']) < 32) && (strlen($_POST['sendto']) >= 3))) ?
        $_POST['sendto'] : '';
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
    $q = $db->query("SELECT `userid` FROM `users` WHERE `username` = '{$sendto}'");
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
    //Insert message into database so receiving player can view it later.
    $db->query("INSERT INTO `mail`
	(`mail_id`, `mail_to`, `mail_from`, `mail_status`, `mail_subject`, `mail_text`, `mail_time`) 
	VALUES (NULL, '{$to}', '{$userid}', 'unread', '{$subj}', '{$msg}', '{$time}');");
    alert('success', "Success!", "Message has been sent successfully", false);
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
    if (empty($_POST['delete'])) {
        echo "Are you sure you want to empty your inbox? This cannot be undone.";
        echo "<br />
		<form method='post'>
			<input type='submit' name='delete' class='btn btn-primary' value='Delete Inbox'>
		</form>
		<form method='post' action='inbox.php'>
			<input type='submit' class='btn btn-danger' value='Nevermind'>
		</form>";
    } else {
        //Delete all messages that were sent to the current player.
        $db->query("DELETE FROM `mail` WHERE `mail_to` = {$userid}");
        alert('success', "Success!", "You have successfully cleaned out your inbox.", true, 'inbox.php');
    }
}

function outbox()
{
    global $db, $userid, $parser;
    echo "
    <table class='table table-bordered table-hover table-striped'>
        <thead>
            <th width='33%'>
                Message Info
            </th>
            <th>
                Subject/Message
            </th>
        </thead>
        <tbody>";
    //Grab all the messages the current player has writen and display them to the user.
    $query = $db->query("SELECT * FROM `mail` WHERE `mail_from` = {$userid} ORDER BY `mail_time` desc LIMIT 15");
    while ($msg = $db->fetch_row($query)) {
        $sent = date('F j, Y g:i:s a', $msg['mail_time']);
        //Grab recipient's user name.
        $sentto = $db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$msg['mail_to']}"));
        //Parse message with BBCode.
        $parser->parse($msg['mail_text']);
        $status = ($msg['mail_status'] == 'unread') ?
            "<span class='badge badge-pill badge-danger'>Unread</span>" :
            "<span class='badge badge-pill badge-success'>Read</span>";
        echo "
        <tr>
            <td>
                <b>To:</b> <a href='profile.php?user={$msg['mail_to']}'>{$sentto}</a><br />
                <b>Date: </b>{$sent}<br />
                <b>Status:</b> {$status}<br />
            </td>
            <td>
                <b>{$msg['mail_subject']}</b> ";
        //Parse message BBCode
        echo $parser->getAsHtml();
        echo "
            </td>
        </tr>";
    }
    echo "</tbody></table>'";
}

function compose()
{
    global $db, $userid;
    //Sanitize GET
    $_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : 0;
    //GET is set and greater than 0, so let's fetch the username associated that's on the GET.
    $username = (isset($_GET['user']) && ($_GET['user'] > 0)) ?
        $username = $db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$_GET['user']}")) :
        '';
    //Permission check to see if player can send mail.
    if (permission('CanReplyMail', $userid)) {
        //Request CSRF Code and display the message composer form.
        $code = request_csrf_code('inbox_send');
        echo "
		<form method='post' action='?action=send'>
		<table class='table table-bordered'>
		<tr>
			<th>
				Recipient
			</th>
			<td>
				<input type='text' class='form-control' value='{$username}' name='sendto' required='1'>
			</td>
		</tr>
		<tr>
			<th>
				Subject
			</th>
			<td>
				<input type='text' class='form-control' maxlength='50' name='subject'>
			</td>
		</tr>
		<tr>
			<th>
				Message
			</th>
			<td>
				<textarea class='form-control' rows='5' required='1' maxlength='65655' name='msg'></textarea>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<input type='submit' class='btn btn-primary'  value='Send Message'>
			</td>
		</tr>
		</table>
		<input type='hidden' name='verf' value='{$code}' />
		</form>";
    }
}

function archive()
{
    echo "<table class='table table-bordered'>
	<tr>
		<th colspan='2'>
			Select which archive you wish to download.
		</th>
	</tr>
	<tr>
		<td>
			<form method='post' action='dlarchive.php'>
				<input type='hidden' name='archive' value='inbox' />
				<input type='submit' value='Inbox' class='btn btn-primary'>
			</form>
		</td>
		<td>
			<form method='post' action='dlarchive.php'>
				<input type='hidden' name='archive' value='outbox' />
				<input type='submit' value='Outbox' class='btn btn-primary'>
			</form>
		</td>
	</tr>
	</table>";
}

function delete()
{
    global $db, $userid, $h;
    $_GET['msg'] = (isset($_GET['msg']) && is_numeric($_GET['msg'])) ? abs($_GET['msg']) : 0;
    if (empty($_GET['msg'])) {
        alert('danger', "Uh Oh!", "This message is non-existent, or does not belong to you.", false);
        home();
        die($h->endpage());
    }
    //Message does not exist, or does not belong to the current player.
    if ($db->num_rows($db->query("SELECT `mail_id` FROM `mail` WHERE `mail_id` = {$_GET['msg']} AND `mail_to` = {$userid}")) == 0) {
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