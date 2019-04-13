<?php
/*
	File:		staff.php
	Created: 	4/5/2016 at 12:27AM Eastern Time
	Info: 		Lists the game staff, and give a friendly link to message
				them.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
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