<?php
/*
	File:		announcements.php
	Created: 	4/4/2016 at 11:51PM Eastern Time
	Info: 		Lists the game announcements for players to read.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
//How many announcements the user hasn't read.
$AnnouncementCount = $ir['announcements'];
//Select all data from the announcements data table.
$q = $db->query("SELECT * FROM `announcements` ORDER BY `ann_time` DESC");
echo "<table class='table table-bordered table-hover'>
<thead>
	<tr>
		<th width='33%'>Info</th>
		<th>Announcement Text</th>
	</tr>
</thead>
<tbody>";
while ($r = $db->fetch_row($q)) {
    //If announcements unread is greater than 0, show unread badge.
    if ($AnnouncementCount > 0) {
        $AnnouncementCount--;
        $new = "<br /><span class='badge badge-pill badge-danger'>Unread</span>";
    } //Else... show the read badge.
    else {
        $new = "<br /><span class='badge badge-pill badge-success'>Read</span>";
    }
    //Select announcement poster's name.
    $PosterQuery = $db->query("SELECT `username`
                                FROM `users` 
                                WHERE `userid` = {$r['ann_poster']}");
    $Poster = $db->fetch_single($PosterQuery);
    //Parse the announcement time into a user friendly timestamp.
    $AnnouncementTime = DateTime_Parse($r['ann_time']);
    //Make the announcement text safe for the users to read, in case of staff panel compromise.
    $r['ann_text'] = nl2br($r['ann_text']);
    echo "<tr>
		<td>
		    {$AnnouncementTime}<br />
		    Posted By <a href='profile.php?user={$r['ann_poster']}'>{$Poster}</a>{$new}
		    </td>
		<td>
		    {$r['ann_text']}
        </td>
	</tr>";
}
$db->free_result($q);
echo "</table>";
//If the user's unread announcements are greater than 0, set back to 0.
if ($ir['announcements'] > 0) {
    $db->query("UPDATE `users` SET `announcements` = 0 WHERE `userid` = '{$userid}'");
}
$h->endpage();