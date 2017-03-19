<?php
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
        alert('danger',$lang['ERROR_GENERIC'],$lang['NOTIF_DELETE_SINGLE_FAIL'],false);
	}
    else
    {
        $db->query(
                "DELETE FROM `notifications`
                 WHERE `notif_id` = {$_GET['delete']}
                 AND `notif_user` = {$userid}");
        alert('success',$lang['ERROR_SUCCESS'],$lang['NOTIF_DELETE_SINGLE'],false);
    }
    $db->free_result($d_c);
}
echo "
<b>{$lang['NOTIF_TITLE']}</b>
<table class='table table-bordered table-hover table-striped'>
<thead>
	<tr>
		<th width='33%'>
			{$lang['NOTIF_TABLE_HEADER1']}
		</th>
		<th>
			{$lang['NOTIF_TABLE_HEADER2']}
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
		$Status="<span class='label label-danger'>{$lang['NOTIF_UNREAD']}</span>";
	}
	else
	{
		$Status="<span class='label label-primary'>{$lang['NOTIF_READ']}</span>";
	}
	echo "
	<tr>
		<td>
			{$NotificationTime}<br />
				{$Status}<br />
				[<a href='notifications.php?delete={$notif['notif_id']}'>{$lang['NOTIF_DELETE']}</a>]
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