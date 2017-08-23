<?php
/*
	File:		notifications.php
	Created: 	4/5/2016 at 12:20AM Eastern Time
	Info: 		Allows players to view their notifications.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
if (!isset($_GET['delete']))
{
    $_GET['delete'] = 0;
}
$_GET['delete'] = abs($_GET['delete']);
if ($_GET['delete'] > 0)
{
    $d_c =
            $db->query(
                    "SELECT COUNT(`notif_user`)
                     FROM `notifications`
                     WHERE `notif_id` = {$_GET['delete']}
                     AND `notif_user` = {$userid}");
    if ($db->fetch_single($d_c) == 0)
    {
        alert('danger',"Uh Oh!","You cannot delete notifications if you don't have any. :^)",false);
	}
    else
    {
        $db->query(
                "DELETE FROM `notifications`
                 WHERE `notif_id` = {$_GET['delete']}
                 AND `notif_user` = {$userid}");
        alert('success',"Success!","All your notifications have been cleared out successfully.",false);
    }
    $db->free_result($d_c);
}
echo "
<b>Last fifteen notifications</b>
<table class='table table-bordered table-hover table-striped'>
<thead>
	<tr>
		<th width='33%'>
			Notification Info
		</th>
		<th>
			Notification Content
		</th>
	<tr>
</thead>
<tbody>";
$query = $db->query("SELECT *
                FROM `notifications`
                WHERE `notif_user` = $userid
        		ORDER BY `notif_time` DESC
        		LIMIT 15");
while ($notif = $db->fetch_row($query))
{
	$NotificationTime=date('F j Y, g:i:s a', $notif['notif_time']);
	if ($notif['notif_status'] == 'unread')
	{
		$Status="<span class='badge badge-pill badge-danger'>Unread</span>";
	}
	else
	{
		$Status="<span class='badge badge-pill badge-success'>Read</span>";
	}
	echo "
	<tr>
		<td>
			{$NotificationTime}<br />
				{$Status}<br />
				[<a href='notifications.php?delete={$notif['notif_id']}'>Delete</a>]
		</td>
		<td>
			{$notif['notif_text']}
		</td>
	</tr>";
}
$db->query(
            "UPDATE `notifications`
    		 SET `notif_status` = 'read'
    		 WHERE `notif_user` = {$userid}");
echo"</tbody></table>";
$h->endpage();