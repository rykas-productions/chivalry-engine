<?php
/*
	File:		announcements.php
	Created: 	4/4/2016 at 11:51PM Eastern Time
	Info: 		Lists the game announcements for players to read.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
require('lib/bbcode_engine.php');
$viewcount=getCurrentUserPref('announceView',1000);
///How many announcements the user hasn't read.
$AnnouncementCount = $ir['announcements'];
//Select all data from the announcements data table.
$q = $db->query("SELECT * FROM `announcements` ORDER BY `ann_time` DESC LIMIT {$viewcount}");
while ($r = $db->fetch_row($q)) {
    //If announcements unread is greater than 0, show unread badge.
    if ($AnnouncementCount > 0) {
        $AnnouncementCount--;
        $new = "<span class='badge badge-pill badge-danger'>New!</span><br />";
    } //Else... show the read badge.
    else {
        $new = "";
    }
    //Select announcement poster's name.
    $PosterQuery = $db->query("SELECT `username`
                                FROM `users` 
                                WHERE `userid` = {$r['ann_poster']}");
    $Poster = $db->fetch_single($PosterQuery);
    //Parse the announcement time into a user friendly timestamp.
    $AnnouncementTime = DateTime_Parse($r['ann_time']);
    //Make the announcement text safe for the users to read, in case of staff panel compromise.
    $parser->parse($r['ann_text']);
    
        echo "
        <div class='card'>
            <div class='card-body'>
                <p class='card-text'>{$new}" . $parser->getAsHtml() . "</p>
                <p class='card-text text-muted'><small><i>
                    Posted By <a href='profile.php?user={$r['ann_poster']}'>{$Poster}</a> {$AnnouncementTime}
                    </small></i></p>
            </div>
        </div><br />";
}
$db->free_result($q);
//If the user's unread announcements are greater than 0, set back to 0.
if ($ir['announcements'] > 0) {
    $db->query("UPDATE `users` SET `announcements` = 0 WHERE `userid` = '{$userid}'");
}
$h->endpage();