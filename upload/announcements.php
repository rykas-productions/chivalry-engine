<?php
/*
	File:		announcements.php
	Created: 	4/4/2016 at 11:51PM Eastern Time
	Info: 		Lists the game announcements for players to read.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
$AnnouncementCount = $ir['announcements'];
$q = $db->query("SELECT * FROM `announcements` ORDER BY `ann_time` DESC");
echo "<table class='table table-bordered table-hover table-responsive'>
<thead>
	<tr>
		<td width='30%'>{$lang['ANNOUNCEMENTS_TIME']}</td>
		<td>{$lang['ANNOUNCEMENTS_TEXT']}</td>
	</tr>
</thead>
<tbody>";
while ($r = $db->fetch_row($q))
{
    if ($AnnouncementCount > 0)
    {
        $AnnouncementCount--;
        $new = "<br /><small><span class='label label-danger'>{$lang['ANNOUNCEMENTS_UNREAD']}</span></small>";
    }
    else
    {
        $new = "<br /><small><span class='label label-success'>{$lang['ANNOUNCEMENTS_READ']}</span></small>";
    }
	$PosterQuery=$db->query("SELECT `username` FROM `users` WHERE `userid` = {$r['ann_poster']}");
	$Poster=$db->fetch_single($PosterQuery);
	$AnnouncementTime=DateTime_Parse($r['ann_time']);
    $r['ann_text'] = nl2br($r['ann_text']);
	echo "<tr>
		<td>{$AnnouncementTime}<br />{$lang['ANNOUNCEMENTS_POSTED']} <a href='profile.php?user={$r['ann_poster']}'>{$Poster}</a>{$new}</td>
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