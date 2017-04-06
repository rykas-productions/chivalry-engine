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
while ($r = $db->fetch_row($q))
{
    $staff[$r['userid']] = $r;
}
$db->free_result($q);
echo "<h3>{$lang['STAFFLIST_ADMIN']}</h3>
<table class='table table-bordered table-hover table-striped'>
	<tr>
		<th>
			{$lang['USERLIST_ORDER2']} [{$lang['USERLIST_ORDER1']}]
		</th>
		<th>
			{$lang['STAFFLIST_LS']}
		</th>
		<th>
			{$lang['STAFFLIST_CONTACT']}
		</th>
	</tr>";
foreach ($staff as $r)
{
    if ($r['user_level'] == 'Admin')
    {
		echo "<tr>
				<td>
					<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
				</td>
				<td>
					" . DateTime_Parse($r['laston']) . "
				</td>
				<td>
					<a href='inbox.php?action=compose&user={$r['userid']}'>{$lang['MAIL_SENDMSG']} {$lang['MAIL_SENDTO']} {$r['username']}</a>
				</td>
				</tr>";
    }
}
echo '</table>';
echo "<h3>{$lang['STAFFLIST_ASSIST']}</h3>
<table class='table table-bordered table-hover table-striped'>
	<tr>
		<th>
			{$lang['USERLIST_ORDER2']} [{$lang['USERLIST_ORDER1']}]
		</th>
		<th>
			{$lang['STAFFLIST_LS']}
		</th>
		<th>
			{$lang['STAFFLIST_CONTACT']}
		</th>
	</tr>";
foreach ($staff as $r)
{
    if ($r['user_level'] == 'Assistant')
    {
        echo "<tr>
				<td>
					<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
				</td>
				<td>
					" . DateTime_Parse($r['laston']) . "
				</td>
				<td>
					<a href='inbox.php?action=compose&user={$r['userid']}'>{$lang['MAIL_SENDMSG']} {$lang['MAIL_SENDTO']} {$r['username']}</a>
				</td>
				</tr>";
    }
}
echo '</table>';
echo "<h3>{$lang['STAFFLIST_MOD']}</h3>
<table class='table table-bordered table-hover table-striped'>
	<tr>
		<th>
			{$lang['USERLIST_ORDER2']} [{$lang['USERLIST_ORDER1']}]
		</th>
		<th>
			{$lang['STAFFLIST_LS']}
		</th>
		<th>
			{$lang['STAFFLIST_CONTACT']}
		</th>
	</tr>";
foreach ($staff as $r)
{
    if ($r['user_level'] == 'Forum Moderator')
    {
        echo "<tr>
				<td>
					<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
				</td>
				<td>
					" . DateTime_Parse($r['laston']) . "
				</td>
				<td>
					<a href='inbox.php?action=compose&user={$r['userid']}'>{$lang['MAIL_SENDMSG']} {$lang['MAIL_SENDTO']} {$r['username']}</a>
				</td>
			</tr>";
    }
}
echo '</table>';
$h->endpage();