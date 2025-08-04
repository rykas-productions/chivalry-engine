<?php
/*
	File:		notifications.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Displays notifications belonging to the current player.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
	MIT License

	Copyright (c) 2019 TheMasterGeneral

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
*/
require("globals.php");
if (!isset($_GET['delete'])) {
    $_GET['delete'] = 0;
}
if (!isset($_GET['deleteall'])) {
    $_GET['deleteall'] = 0;
}
$_GET['delete'] = abs($_GET['delete']);
if ($_GET['delete'] > 0) {
    $d_c = $db->query("SELECT COUNT(`notif_user`)
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
while ($notif = $db->fetch_row($query)) {
    $NotificationTime = date('F j Y, g:i:s a', $notif['notif_time']);
    if ($notif['notif_status'] == 'unread') {
        $Status = "<span class='badge badge-pill badge-danger'>Unread</span>";
    } else {
        $Status = "<span class='badge badge-pill badge-success'>Read</span>";
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
echo "</tbody></table>
<a class='btn btn-primary' href='?deleteall=1'>Delete All Notifications</a>";
$h->endpage();