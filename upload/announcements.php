<?php
/*
	File:		announcements.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Lists the staff posted announcements for players to view.
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
//How many announcements the user hasn't read.
$AnnouncementCount = $ir['announcements'];
//Select all data from the announcements data table.
$q = $db->query("SELECT * FROM `announcements` ORDER BY `ann_time` DESC");
while ($r = $db->fetch_row($q)) {
    //If announcements unread is greater than 0, show unread badge.
    if ($AnnouncementCount > 0) {
        $AnnouncementCount--;
        $new = "<span class='badge badge-pill badge-danger'>New!</span>";
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
    $AnnouncementTime = dateTimeParse($r['ann_time']);
    //Make the announcement text safe for the users to read, in case of staff panel compromise.
    $r['ann_text'] = nl2br($r['ann_text']);
    
        echo "
        <div class='card'>
            <div class='card-header'>
                Posted By <a href='profile.php?user={$r['ann_poster']}'>{$Poster}</a> {$AnnouncementTime} {$new}
            </div>
            <div class='card-body'>
                <p class='card-text'>{$r['ann_text']}</p>
            </div>
        </div><br />";
}
$db->free_result($q);
//If the user's unread announcements are greater than 0, set back to 0.
if ($ir['announcements'] > 0) {
    $db->query("UPDATE `users` SET `announcements` = 0 WHERE `userid` = '{$userid}'");
}
$h->endpage();