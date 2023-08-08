<?php
/*
	File:		announcements.php
	Created: 	10/06/2019 at 6:12PM Eastern Time
	Author:		TheMasterGeneral
	Website: 	https://github.com/rykas-productions/chivalry-engine
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
require('./globals_auth.php');
$q=$db->query("SELECT * FROM `game_announcements` ORDER BY `annTime` DESC");
createTwoCols("<h3>Announcement Info</h3>","<h3>Announcement Text</h3>");
echo "<hr />";
while ($r = $db->fetch_row($q))
{
	$unreadAnnouncement = '';
	if ($ir['unreadAnnouncements'] > 0)
	{
		$ir['unreadAnnouncements']--;
		$unreadAnnouncement = '<b>New!</b>';
		$db->query("UPDATE `users_account_data` SET `unreadAnnouncements` = `unreadAnnouncements` - 1 WHERE `userid` = {$userid}");
	}
	$uQ = $db->query("SELECT `username` FROM `users_core` WHERE `userid` = {$r['annUser']}");
	$userName = $db->fetch_single($uQ);
	$parseAnnouncementTime = parseDateTime($r['annTime']);
	createTwoCols("Announcement ID {$r['annId']}<br />
					Posted: {$parseAnnouncementTime}<br />
						{$userName} [{$r['annUser']}]<br />
							{$unreadAnnouncement}", 
					nl2br($r['annText']));
	echo "<hr />";
}
$db->free_result($q);
$h->endHeaders();
	