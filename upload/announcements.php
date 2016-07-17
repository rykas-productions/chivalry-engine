<?php
require("globals.php");
$AnnouncementCount = $ir['announcements'];
$q =
        $db->query(
                "SELECT * FROM `announcements` ORDER BY `ann_time` DESC");
echo "<table class='table table-bordered table-hover table-responsive'>
<thead>
	<tr>
		<td width='30%'>Time</td>
		<td>Announcement Text</td>
	</tr>
</thead>
<tbody>";
while ($r = $db->fetch_row($q))
{
    if ($AnnouncementCount > 0)
    {
        $AnnouncementCount--;
        $new = "<br /><small><span class='label label-danger'>Unread</span></small>";
    }
    else
    {
        $new = "<br /><small><span class='label label-success'>Previously Read</span></small>";
    }
	$PosterQuery=$db->query("SELECT `username` FROM `users` WHERE `userid` = {$r['ann_poster']}");
	$Poster=$db->fetch_single($PosterQuery);
	$AnnouncementTime=date('F j Y, g:i:s a', $r['ann_time']);
    $r['ann_text'] = nl2br($r['ann_text']);
	echo "<tr>
		<td>{$AnnouncementTime}<br />Posted by: <a href='viewuser.php?user={$r['ann_poster']}'>{$Poster}</a>{$new}</td>
		<td>{$r['ann_text']}</td>
	</tr>";
}
$db->free_result($q);
echo"</table>";
if ($ir['announcements'] > 0)
{
    $db->query(
            "UPDATE `users` SET `announcements` = 0 WHERE `userid` = '{$userid}'");
}
$h->endpage();