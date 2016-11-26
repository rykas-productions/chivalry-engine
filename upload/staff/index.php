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
				<th>
					PHP Version Detected
				</th>
				<td>
					{$PHPVersion}
				</td>
			</tr>
			<tr>
				<th>
					Database Version
				</th>
				<td>
					{$MySQLIVersion}
				</td>
			</tr>
			<tr>
				<th>
					Chivalry Engine Version_Number
				</th>
				<td>
					{$set['Version_Number']} (Build Number: {$set['BuildNumber']})
				</td>
			</tr>
			<tr>
				<th>
					Chivalry Engine Update?
				</th>
				<td>
					<iframe width='100%' height='35' style='border:none' src='http://mastergeneral156.pcriot.com/update-checker.php?version={$set['BuildNumber']}'>
						Your browser does not support iframes...
					</iframe>
				</td>
			</tr>
			<tr>
				<th>
					API Version
				</th>
				<td>
					{$api->SystemReturnAPIVersion()}
				</td>
			</tr>
		</tbody>
	</table>
	<hr />
	<ul class='nav nav-tabs nav-justified'>
		<li><a data-toggle='tab' href='#ADMIN'>Admin</a></li>
		<li><a data-toggle='tab' href='#MODULES'>Modules</a></li>
		<li><a data-toggle='tab' href='#USERS'>Users</a></li>
		<li><a data-toggle='tab' href='#LOGS'>Logs</a></li>
		<li><a data-toggle='tab' href='#ITEMS'>Items</a></li>
		<li><a data-toggle='tab' href='#SHOPS'>Shops</a></li>
		<li><a data-toggle='tab' href='#ACADEMY'>Academy</a></li>
		<li><a data-toggle='tab' href='#JOBS'>Jobs</a></li>
		<li><a data-toggle='tab' href='#FORUMS'>Forums</a></li>
		<li><a data-toggle='tab' href='#PUNISH'>Punishments</a></li>
		<li><a data-toggle='tab' href='#PERMISSION'>Permissions</a></li>
		<li><a data-toggle='tab' href='#POLL'>Polling</a></li>
	</ul>
	<div class='tab-content'>
		<div id='ADMIN' class='tab-pane fade in'>
			<div class='panel panel-default'>
				<div class='panel-body'>
					<a href='staff_settings.php?action=basicset'>Game Settings</a><br />
					<a href='staff_settings.php?action=announce'>Create an Announcement</a><br />
					<a href='staff_settings.php?action=diagnostics'>Game Diagnostics</a><br />
					<a href='staff_settings.php?action=restore'>Restore Users</a><br />
				</div>
			</div>
		</div>
		<div id='MODULES' class='tab-pane fade in'>
			<div class='panel panel-default'>
				<div class='panel-body'>
					<a href='staff_criminal.php'>Crimes</a>
				</div>
			</div>
		</div>
		<div id='USERS' class='tab-pane fade in'>
			<div class='panel panel-default'>
				<div class='panel-body'>
					<a href='staff_users.php?action=createuser'>Create User</a><br />
					<a href='staff_users.php?action=edituser'>Edit User</a><br />
					<a href='staff_users.php?action=deleteuser'>Delete User</a><br />
				</div>
			</div>
		</div>
		<div id='LOGS' class='tab-pane fade in'>
			<div class='panel panel-default'>
				<div class='panel-body'>
					<a href='staff_logs.php?action=trainlogs'>Training Logs</a><br />
					<a href='staff_logs.php?action=attacklogs'>Attack Logs</a><br />
				</div>
			</div>
		</div>
		<div id='ITEMS' class='tab-pane fade in'>
			<div class='panel panel-default'>
				<div class='panel-body'>
					<a href='staff_items.php?action=createitmgroup'>Create Item Group</a><br />
					<a href='staff_items.php?action=create'>Create Item</a><br />
					<a href='staff_items.php?action=delete'>Delete Item</a><br />
					<a href='staff_items.php?action=edit'>Edit Item</a><br />
					<a href='staff_items.php?action=giveitem'>Give Item</a><br />
				</div>
			</div>
		</div>
		<div id='SHOPS' class='tab-pane fade in'>
			<div class='panel panel-default'>
				<div class='panel-body'>
					N/A
				</div>
			</div>
		</div>
		<div id='JOBS' class='tab-pane fade in'>
			<div class='panel panel-default'>
				<div class='panel-body'>
					N/A
				</div>
			</div>
		</div>
		<div id='ACADEMY' class='tab-pane fade in'>
			<div class='panel panel-default'>
				<div class='panel-body'>
					N/A
				</div>
			</div>
		</div>
		<div id='FORUMS' class='tab-pane fade in'>
			<div class='panel panel-default'>
				<div class='panel-body'>
					<a href='staff_forums.php?action=addforum'>{$lang['STAFF_FORUM_ADD']}</a><br />
					<a href='staff_forums.php?action=editforum'>{$lang['STAFF_FORUM_EDIT']}</a><br />
					<a href='staff_forums.php?action=delforum'>{$lang['STAFF_FORUM_DEL']}</a>
				</div>
			</div>
		</div>
		<div id='PUNISH' class='tab-pane fade in'>
			<div class='panel panel-default'>
				<div class='panel-body'>
					N/A
				</div>
			</div>
		</div>
		<div id='PERMISSION' class='tab-pane fade in'>
			<div class='panel panel-default'>
				<div class='panel-body'>
					<a href='staff_perms.php?action=viewperm'>View User's Permissions</a><br />
					<a href='staff_perms.php?action=resetperm'>Reset User's Permissions</a><br />
					<a href='staff_perms.php?action=editperm'>Edit Permissions</a>
				</div>
			</div>
		</div>
		<div id='POLL' class='tab-pane fade in'>
			<div class='panel panel-default'>
				<div class='panel-body'>
					<a href='staff_polling.php?action=addpoll'>{$lang['STAFF_POLL_TITLES']}</a><br />
					<a href='staff_polling.php?action=closepoll'>{$lang['STAFF_POLL_TITLEE']}</a><br />
				</div>
			</div>
		</div>
	</div>
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
                        "SELECT `log_user`, `log_text`, `log_time`, `log_ip`, `username`
                         FROM `logs` AS `s`
                         INNER JOIN `users` AS `u`
                         ON `s`.`log_user` = `u`.`userid`
						 WHERE `log_type` = 'staff'
                         ORDER BY `s`.`log_time` DESC
                         LIMIT 20");
        while ($r = $db->fetch_row($q))
        {
            echo "
        	<tr>
        		<td><a href='../profile.php?user={$r['log_user']}'>{$r['username']}</a> [{$r['log_user']}]</td>
        		<td>{$r['log_text']}</td>
        		<td>" . date('F j Y g:i:s a', $r['log_time'])
                    . "</td>
        		<td>{$r['log_ip']}</td>
        	</tr>
           	";
        }
        $db->free_result($q);
        echo '</tbody></table><hr />';
}
$h->endpage();