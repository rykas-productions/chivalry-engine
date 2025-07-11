<?php
/*
	File:		notifications.php
	Created: 	4/5/2016 at 12:20AM Eastern Time
	Info: 		Allows players to view their notifications.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
$viewCount=getCurrentUserPref('notifView', 15);
if (!isset($_GET['delete'])) {
    $_GET['delete'] = 0;
}
if (!isset($_GET['deleteall'])) {
    $_GET['deleteall'] = 0;
}
$_GET['delete'] = abs($_GET['delete']);
if ($_GET['delete'] > 0) {
    $d_c = $db->query("/*qc=on*/SELECT COUNT(`notif_user`)
                      FROM `notifications`
                      WHERE `notif_id` = {$_GET['delete']}
                      AND `notif_user` = {$userid}");
    if ($db->fetch_single($d_c) == 0) {
        alert('danger', "Uh Oh!", "You cannot delete a notification that doesn't exist, or doesn't belong to you.", false);
    } else {
        $db->query("DELETE FROM `notifications`
                 WHERE `notif_id` = {$_GET['delete']}
                 AND `notif_user` = {$userid}");
        alert('success', "Success!", "Notification has been deleted successfully.", false);
    }
    $db->free_result($d_c);
}
if ($_GET['deleteall'] > 0) {
    $db->query("DELETE FROM `notifications`
                 WHERE `notif_user` = {$userid}");
    alert('success', "Success!", "You have successfully deleted all your notifications.", false);
}
$query = $db->query("/*qc=on*/SELECT *
                FROM `notifications`
                WHERE `notif_user` = $userid
        		ORDER BY `notif_time` DESC
        		LIMIT {$viewCount}");
echo "<div class='card'>
        <div class='card-header'>
            Last {$viewCount} notifications
        </div>
        <div class='card-body'>";
while ($notif = $db->fetch_row($query)) {
    $NotificationTime = DateTime_Parse($notif['notif_time']);
    if ($notif['notif_status'] == 'unread') {
        $Status = "❌";
    } else {
        $Status = "✔";
    }
	if (empty($notif['notif_icon']))
	{
		$icon= "<i class='fas fa-question' style='font-size:3rem;'></i>";
	}
	else
	{
		if (!empty($notif['notif_color']))
		{
			$icon = "<i class='{$notif['notif_icon']}' style='font-size:3rem; color: {$notif['notif_color']};'></i>";
		}
		else
		{
			$icon = "<i class='{$notif['notif_icon']}' style='font-size:3rem;'></i>";
		}
		
	}
    echo "
			<div class='row'>
				<div class='col-12'>
					" . stripslashes($notif['notif_text']) . "
                </div>
                <div class='col-12 col-sm-1'>
                    {$Status}
                </div>
                <div class='col-12 col-sm'>
                    <i>{$NotificationTime}</i>
                </div>
                <div class='col-12 col-sm-2'>
                    <a class='btn btn-danger btn-sm' href='?delete={$notif['notif_id']}'>🗑️</a>
                </div>
                <div class='col-12'>
					&nbsp;
                </div>
			</div>";
}
$db->query(
    "UPDATE `notifications`
    		 SET `notif_status` = 'read'
    		 WHERE `notif_user` = {$userid}");
echo "
<a class='btn btn-primary btn-block' href='?deleteall=1'>Delete All Notifications</a></div>";
$h->endpage();