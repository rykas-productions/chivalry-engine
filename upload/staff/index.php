<?php
/*
	File: staff/index.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: Landing page for the staff panel. Will show all avaliable staff actions.
	Author: TheMasterGeneral
	Website: http://mastergeneral156.pcriot.com/
*/
require('sglobals.php');
if (($ir['user_level']) == 'Admin')
{
	$versq = $db->query("SELECT VERSION()");
    $MySQLIVersion = $db->fetch_single($versq);
    $db->free_result($versq);
	$PHPVersion=phpversion();
	echo"<h2>Admin Area</h2>
	<hr />
	<big>Game Info</big>
	<table class='table table-bordered table-hover'>
		<tbody>
			<tr>
				<th>PHP Version Detected</th>
				<td>{$PHPVersion}</td>
			</tr>
			<tr>
				<th>Database Version</th>
				<td>{$MySQLIVersion}</td>
			</tr>
			<tr>
				<th>Chivalry Engine Version</th>
				<td>{$set['Version_Number']} (Build Number: {$set['BuildNumber']})</td>
			</tr>
			<tr>
				<th>Chivalry Engine Update?</th>
				<td><iframe width='100%' height='35' style='border:none' src='http://mastergeneral156.pcriot.com/update-checker.php?version={$set['BuildNumber']}'>Your browser does not support iframes...</iframe></td>
			</tr>
		</tbody>
	</table>
	<hr />
	<table class='table table-bordered'>
		<tbody>
			<tr>
				<td><a href='staff_settings.php'>Admin</a></td>
				<td><a>Modules</a></td>
			</tr>
			<tr>
				<td><a href='staff_users.php'>Users</a></td>
				<td><a href='staff_logs.php'>Logs</a></td>
			</tr>
			<tr>
				<td><a href='staff_items.php'>Items</a></td>
				<td><a>Shops</a></td>
			</tr>
			<tr>
				<td><a>Academy</a></td>
				<td><a>Jobs</a></td>
			</tr>
			<tr>
				<td><a href='staff_forums.php'>Forums</a></td>
				<td><a>Punishments</a></td>
			</tr>
			<tr>
				<td><a href='staff_perms.php'>Permissions</a></td>
				<td><a href='staff_polling.php'>Polling</a></td>
			</tr>
		</tbody>
	</table>
	        <hr />
        <h3>Last 20 Staff Actions</h3><hr />
        <table class='table table-bordered table-hover'>
        		<thead>
				<tr>
        			<th>Staff</th>
        			<th>Action</th>
        			<th>Time</th>
        			<th>IP</th>
				</tr>
				</thead>
				<tbody>";
		$q =
                $db->query(
                        "SELECT `user`, `action`, `time`, `ip`, `username`
                         FROM `stafflogs` AS `s`
                         INNER JOIN `users` AS `u`
                         ON `s`.`user` = `u`.`userid`
                         ORDER BY `s`.`time` DESC
                         LIMIT 20");
        while ($r = $db->fetch_row($q))
        {
            echo "
        	<tr>
        		<td><a href='../profile.php?user={$r['user']}'>{$r['username']}</a> [{$r['user']}]</td>
        		<td>{$r['action']}</td>
        		<td>" . date('F j Y g:i:s a', $r['time'])
                    . "</td>
        		<td>{$r['ip']}</td>
        	</tr>
           	";
        }
        $db->free_result($q);
        echo '</tbody></table><hr />';
}
$h->endpage();