<?php
require("globals.php");
$staff = array();
$q =
        $db->query(
                "SELECT `userid`, `laston`, `username`, `user_level`
 				 FROM `users`
 				 WHERE `user_level` IN('Admin', 'Forum Moderator', 'Assistant')
 				 ORDER BY `userid` ASC");
while ($r = $db->fetch_row($q))
{
    $staff[$r['userid']] = $r;
}
$db->free_result($q);
echo "<h3>Admins</h3>
<br />
<table class='table table-bordered table-hober'>
<thead>
	<th>Username [ID]</th>
	<th>Last Seen</th>
	<th>Online?</th>
	<th>Contact</th>
</thead>
<tbody>";
foreach ($staff as $r)
{
    if ($r['user_level'] == 'Admin')
    {
        $on =
                ($r['laston'] >= ($_SERVER['REQUEST_TIME'] - 900))
                        ? '<span style="color: green; font-weight:bold;">Online</span>'
                        : '<span style="color: green; font-weight:bold;">Offline</span>';
		echo "<tr>
				<td>
					<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
				</td>
				<td>
					" . date("F j, Y, g:i:s a", $r['laston']) . "
				</td>
				<td>
					{$on}
				</td>
				<td>
					<a href='inbox.php?action=compose&user={$r['userid']}'>Send {$r['username']} a Message</a>
				</td>";
    }
}
echo '</table>';
echo "<h3>Assistants</h3>
<br />
<table class='table table-bordered table-hober'>
<thead>
	<th>Username [ID]</th>
	<th>Last Seen</th>
	<th>Online?</th>
	<th>Contact</th>
</thead>
<tbody>";
foreach ($staff as $r)
{
    if ($r['user_level'] == 'Assistant')
    {
        $on =
                ($r['laston'] >= ($_SERVER['REQUEST_TIME'] - 900))
                        ? '<span style="color: green; font-weight:bold;">Online</span>'
                        : '<span style="color: green; font-weight:bold;">Offline</span>';
		echo "<tr>
				<td>
					<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
				</td>
				<td>
					" . date("F j, Y, g:i:s a", $r['laston']) . "
				</td>
				<td>
					{$on}
				</td>
				<td>
					<a href='inbox.php?action=compose&user={$r['userid']}'>Send {$r['username']} a Message</a>
				</td>";
    }
}
echo '</table>';
echo "<h3>Forum Moderators</h3>
<br />
<table class='table table-bordered table-hober'>
<thead>
	<th>Username [ID]</th>
	<th>Last Seen</th>
	<th>Online?</th>
	<th>Contact</th>
</thead>
<tbody>";
foreach ($staff as $r)
{
    if ($r['user_level'] == 'Forum Moderator')
    {
        $on =
                ($r['laston'] >= ($_SERVER['REQUEST_TIME'] - 900))
                        ? '<span style="color: green; font-weight:bold;">Online</span>'
                        : '<span style="color: green; font-weight:bold;">Offline</span>';
		echo "<tr>
				<td>
					<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
				</td>
				<td>
					" . date("F j, Y, g:i:s a", $r['laston']) . "
				</td>
				<td>
					{$on}
				</td>
				<td>
					<a href='inbox.php?action=compose&user={$r['userid']}'>Send {$r['username']} a Message</a>
				</td>";
    }
}
echo '</table>';
$h->endpage();