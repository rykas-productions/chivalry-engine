<?php
/*
	File: js//script/checkun.php
	Created: 4/4/2017 at 7:10PM Eastern Time
	Info: PHP file for checking a user's inputted username
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
$menuhide = 1;
$nohdr=true;
require_once('../../globals.php');
if (isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD'])) {
    if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
        // Ignore a GET request
        header('HTTP/1.1 400 Bad Request');
        exit;
    }
}
//require_once('../../global_func.php');
if (!is_ajax()) {
    header('HTTP/1.1 400 Bad Request');
    exit;
}
//Select count of user's unread messages.
$ir['mail'] = $db->fetch_single(
					$db->query("
							/*qc=on*/SELECT COUNT(`mail_id`) 
							FROM `mail` 
							WHERE `mail_to` = {$ir['userid']} 
							AND `mail_status` = 'unread'"));
//Select count of user's unread notifications.

$ir['notifications'] = $db->fetch_single(
							$db->query("
								SELECT COUNT(`notif_id`) 
								FROM `notifications` 
								WHERE `notif_user` = {$ir['userid']} 
								AND `notif_status` = 'unread'"));
//Select count of user's unread notifications.
$ir['announcements'] = $db->fetch_single(
							$db->query("
								SELECT `announcements` 
								FROM `users` 
								WHERE `userid` = {$ir['userid']}"));
?>
	<script>
		var div = document.getElementById("socialRow");
		div.innerHTML = "";
	</script>
<?php
if ($ir['mail'] > 0) 
{
	echo "<div class='col-md'>";
		alert('info', "", "You have " . number_format($ir['mail']) . " unread messages.", true, 'inbox.php', "View");
	echo "</div>";
	?>
	<script>
		var div = document.getElementById('inboxTop');
		div.innerHTML = <?php echo "' " . number_format($ir['mail']) . "'"; ?>
	</script>
	<?php
}
//Tell user they have unread notifcations when they do.
if ($ir['notifications'] > 0) 
{
	echo "<div class='col-md'>";
		alert('info', "", "You have " . number_format($ir['notifications']) . " unread notifications.", true, 'notifications.php', "View");
	echo "</div>";
	?>
	<script>
		var div = document.getElementById('notifTop');
		div.innerHTML = <?php echo "' " . number_format($ir['notifications']) . "'"; ?>
	</script>
	<?php
}
//Tell user they have unread game announcements when they do.
if ($ir['announcements'] > 0) 
{
	echo "<div class='col-md'>";
		alert('info', "", "You have {$ir['announcements']} unread announcements.", true, 'announcements.php', "View");
	echo "</div>";
}