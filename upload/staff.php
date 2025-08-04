<?php
/*
	File:		staff.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Shows the list of in-game staff members, such as 
				Admins, Web Developers, Forum Moderators and Assistants.
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
$staff = array();
$q = $db->query("SELECT `userid`, `laston`, `username`, `user_level`
 				 FROM `users`
 				 WHERE `user_level` IN('Admin', 'Forum Moderator', 'Assistant')
 				 ORDER BY `userid` ASC");
while ($r = $db->fetch_row($q)) {
    $staff[$r['userid']] = $r;
}
$db->free_result($q);
echo "<h3>Admins</h3>
<table class='table table-bordered table-hover table-striped'>
	<tr>
		<th>
			User
		</th>
		<th>
			Last Seen
		</th>
		<th>
			Contact
		</th>
	</tr>";
foreach ($staff as $r) {
    if ($r['user_level'] == 'Admin') {
        echo "<tr>
				<td>
					<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
				</td>
				<td>
					" . dateTimeParse($r['laston']) . "
				</td>
				<td>
					<a href='inbox.php?action=compose&user={$r['userid']}'>Send {$r['username']} Message</a>
				</td>
				</tr>";
    }
}
echo '</table>';
echo "<h3>Assistants</h3>
<table class='table table-bordered table-hover table-striped'>
	<tr>
		<th>
			User
		</th>
		<th>
			Last Seen
		</th>
		<th>
			Contact
		</th>
	</tr>";
foreach ($staff as $r) {
    if ($r['user_level'] == 'Assistant') {
        echo "<tr>
				<td>
					<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
				</td>
				<td>
					" . dateTimeParse($r['laston']) . "
				</td>
				<td>
					<a href='inbox.php?action=compose&user={$r['userid']}'>Send {$r['username']} Message</a>
				</td>
				</tr>";
    }
}
echo '</table>';
echo "<h3>Forum Moderators</h3>
<table class='table table-bordered table-hover table-striped'>
	<tr>
		<th>
			User
		</th>
		<th>
			Last Seen
		</th>
		<th>
			Contact
		</th>
	</tr>";
foreach ($staff as $r) {
    if ($r['user_level'] == 'Forum Moderator') {
        echo "<tr>
				<td>
					<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
				</td>
				<td>
					" . dateTimeParse($r['laston']) . "
				</td>
				<td>
					<a href='inbox.php?action=compose&user={$r['userid']}'>Send {$r['username']} Message</a>
				</td>
			</tr>";
    }
}
echo '</table>';
$h->endpage();