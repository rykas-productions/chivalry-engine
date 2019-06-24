<?php
/*
	File: 		staff/index.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Landing page for the staff panel.
	Author: 	TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine/
	
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
require('sglobals.php');
echo "<h2>Staff Panel Index</h2>
	<hr />";
if ($api->user->getStaffLevel($userid, 'admin')) {
    $versq = $db->query("SELECT VERSION()");
    $MySQLIVersion = $db->fetch_single($versq);
    $db->free_result($versq);
    echo "
<div class='row'>
		<div class='col-sm'>
		    <h4>PHP Version</h4>
		</div>
		<div class='col-sm'>
		    <h4>Database Version</h4>
		</div>
		<div class='col-sm'>
		    <h4>Chivalry Engine Version</h4>
		</div>
		<div class='col-sm'>
		    <h4>Update Checker</h4>
		</div>
		<div class='col-sm'>
		    <h4>CE API Version</h4>
		</div>
</div><hr />
<div class='row'>
		<div class='col-sm'>
		    " . phpversion() . "
		</div>
		<div class='col-sm'>
		    " . $MySQLIVersion . "
		</div>
		<div class='col-sm'>
		    " . $set['Version_Number'] . "
		</div>
		<div class='col-sm'>
		    " . getEngineVersion() . "
		</div>
		<div class='col-sm'>
		    " . $api->game->returnAPIVersion() . "
		</div>
</div><hr />";
}
echo "
</div>
	<div class='col-md-4'>
		<ul class='nav nav-pills flex-column'>";
if ($api->user->getStaffLevel($userid, 'assistant')) {
    echo "
			<li class='nav-item'>
				<a class='nav-link' data-toggle='tab' href='#LOGS'>Logs</a>
			</li>
			<li class='nav-item'>
				<a class='nav-link' data-toggle='tab' href='#POLL'>Polls</a>
			</li>
			<li class='nav-item'>
				<a class='nav-link' data-toggle='tab' href='#PERMISSION'>Permissions</a>
			</li>";
}
echo "
			<li class='nav-item'>
				<a class='nav-link' data-toggle='tab' href='#PUNISH'>Punishments</a>
			</li>
			<li class='nav-item'>
				<a class='nav-link' data-toggle='tab' href='#FORUMS'>Forums</a>
			</li>
		</ul>
	</div>
	<div class='col-md-8'>
		<div class='tab-content'>";
if ($api->user->getStaffLevel($userid, 'assistant')) {
    echo "
				<div id='POLL' class='tab-pane'>
					<div class='card'>
						<div class='card-body'>
							<a href='staff_polling.php?action=addpoll'>Create Poll</a><br />
							<a href='staff_polling.php?action=closepoll'>End Poll</a><br />
						</div>
					</div>
				</div>
				<div id='LOGS' class='tab-pane'>
					<div class='card'>
						<div class='card-body'>
							<table class='table table-sm'>
							<tr>
								<td>
									<a href='staff_logs.php?action=alllogs'>General Logs</a>
								</td>
								<td>
									<a href='staff_logs.php?action=mail'>Mail Logs</a>
								</td>
							</tr>
							<tr>
								<td>
									<a href='staff_logs.php?action=userlogs'>User Action Logs</a>
								</td>
								<td>
									<a href='staff_logs.php?action=traininglogs'>Training Logs</a>
								</td>
							</tr>
								<td>
									<a href='staff_logs.php?action=attackinglogs'>Attack Logs</a>
								</td>
								<td>
									<a href='staff_logs.php?action=loginlogs'>Login Logs</a>
								</td>
							</tr>
								<td>
									<a href='staff_logs.php?action=equiplogs'>Equipping Logs</a>
								</td>
								<td>
									<a href='staff_logs.php?action=banklogs'>Bank Logs</a>
								</td>
							</tr>
								<td>
									<a href='staff_logs.php?action=crimelogs'>Crime Logs</a>
								</td>
								<td>
									<a href='staff_logs.php?action=itemuselogs'>Item Use Logs</a>
								</td>
							</tr> 
							</tr>
								<td>
									<a href='staff_logs.php?action=itembuylogs'>Item Buy Logs</a>
								</td>
								<td>
									<a href='staff_logs.php?action=itemselllogs'>Item Sell Logs</a>
								</td>
							</tr> 
							</tr>
								<td>
									<a href='staff_logs.php?action=itemmarketlogs'>Item Market Logs</a>
								</td>
								<td>
                                    <a href='staff_logs.php?action=itemsendlogs'>Item Send Logs</a>
								</td>
							</tr> 
							</tr>
								<td>
									<a href='staff_logs.php?action=verifylogs'>ReCaptcha Logs</a>
								</td>
								<td>
									<a href='staff_logs.php?action=spylogs'>Spy Logs</a>
								</td>
							</tr> 
							</tr>
								<td>
									<a href='staff_logs.php?action=gamblinglogs'>Gambling Logs</a>
								</td>
								<td>
									<a href='staff_logs.php?action=pokes'>Poke Logs</a>
								</td>
							</tr>
							</tr>
								<td>
									<a href='staff_logs.php?action=guilds'>Guild Logs</a>
								</td>
								<td>
									<a href='staff_logs.php?action=guildvault'>Guild Vault Logs</a>
								</td>
							</tr>
							</tr>
								<td>
									<a href='staff_logs.php?action=level'>Leveling Logs</a>
								</td>
								<td>
									<a href='staff_logs.php?action=temple'>Temple Logs</a>
								</td>
							</tr>
							</tr>
								<td>
									<a href='staff_logs.php?action=secmarket'>{$_CONFIG['secondary_currency']} Market Logs</a>
								</td>
								<td>
									<a href='staff_logs.php?action=mining'>Mining Logs</a>
								</td>
							</tr>
							</tr>
								<td>
									<a href='staff_logs.php?action=rrlogs'>Russian Roulette Logs</a>
								</td>
								<td>
                                    <a href='staff_logs.php?action=travellogs'>Travel Logs</a>
								</td>
							</tr>
							</tr>
								<td>
									<a href='staff_logs.php?action=primsend'>{$_CONFIG['primary_currency']} Xfer Logs</a>
								</td>
								<td>

								</td>
							</tr>";
    if ($api->user->getStaffLevel($userid, 'admin')) {
        echo "
								<tr>
									<td>
										<a href='staff_logs.php?action=stafflogs'>Staff Logs</a>
									</td>
									<td>
										<a href='staff_logs.php?action=fedjaillogs'>Federal Dungeon Logs</a>
									</td>
								</tr>
								<tr>
									<td>
										<a href='staff_logs.php?action=forumwarn'>Forum Warn Logs</a>
									</td>
									<td>
										<a href='staff_logs.php?action=forumban'>Forum Ban Logs</a>
									</td>
								</tr>
								<tr>
									<td>
										<a href='staff_logs.php?action=donatelogs'>Donation Logs</a>
									</td>
									<td>
										<a href='staff_settings.php?action=errlog'>Game Error Logs</a>
									</td>
								</tr>
								";
    }
    echo "</table>
						</div>
					</div>
				</div>
				<div id='PERMISSION' class='tab-pane'>
					<div class='card'>
						<div class='card-body'>
							<a href='staff_perms.php?action=viewperm'>View User's Permissions</a><br />
							<a href='staff_perms.php?action=resetperm'>Reset User's Permissions'</a><br />
							<a href='staff_perms.php?action=editperm'>Edit User's Permissions</a>
						</div>
					</div>
				</div>";
}
echo "
			<div id='PUNISH' class='tab-pane'>
					<div class='card'>
						<div class='card-body'>
							<a href='staff_punish.php?action=fedjail'>Federal Dungeon</a><br />
							<a href='staff_punish.php?action=editfedjail'>Edit Federal Dungeon</a><br />
							<a href='staff_punish.php?action=unfedjail'>Remove Federal Dungeon</a><br />
							<a href='staff_punish.php?action=mailban'>Mail Ban User</a><br />
							<a href='staff_punish.php?action=unmailban'>Un-Mail Ban User</a><br />
							<a href='staff_punish.php?action=forumwarn'>Forum Warn User</a><br />
							<a href='staff_punish.php?action=forumban'>Forum Ban User</a><br />
							<a href='staff_punish.php?action=unforumban'>Un-Forum Ban User</a><br />
							<a href='staff_punish.php?action=ipsearch'>IP Search</a><br />
							<a href='staff_punish.php?action=massmail'>Send Mass Mail</a><br />";
if ($api->user->getStaffLevel($userid, 'admin')) {
    echo "<a href='staff_punish.php?action=massemail'>Send Mass Email</a><br />
									<a href='staff_punish.php?action=banip'>Ban IP Address</a><br />
									<a href='staff_punish.php?action=unbanip'>Pardon IP Address</a><br />";
}
echo "
						</div>
					</div>
			</div>
			<div id='FORUMS' class='tab-pane'>
				<div class='card'>
					<div class='card-body'>
						<a href='staff_forums.php?action=addforum'>Add Forum Category</a><br />
						<a href='staff_forums.php?action=editforum'>Edit Forum Category</a><br />
						<a href='staff_forums.php?action=delforum'>Delete Forum Category</a>
					</div>
				</div>
			</div>
		</div>
	</div>";
if ($api->user->getStaffLevel($userid, 'admin')) {
    echo "
    <div class='row'>
            <div class='col-sm'>
                <h4>Timestamp</h4>
            </div>
            <div class='col-sm'>
                <h4>Staff</h4>
            </div>
            <div class='col-sm'>
                <h4>Action</h4>
            </div>
            <div class='col-sm'>
                <h4>IP Address</h4>
            </div>
    </div><hr />";
    $q =
        $db->query(
            "SELECT `log_user`, `log_text`, `log_time`, `log_ip`, `username`
							 FROM `logs` AS `s`
							 INNER JOIN `users` AS `u`
							 ON `s`.`log_user` = `u`.`userid`
							 WHERE `log_type` = 'staff'
							 ORDER BY `s`.`log_time` DESC
							 LIMIT 15");
    while ($r = $db->fetch_row($q)) {
        echo "
         <div class='row'>
            <div class='col-sm'>
                " . dateTimeParse($r['log_time']) . "
            </div>
            <div class='col-sm'>
                <a href='../profile.php?user={$r['log_user']}'>{$r['username']}</a> [{$r['log_user']}]
            </div>
            <div class='col-sm'>
                {$r['log_text']}
            </div>
            <div class='col-sm'>
                {$r['log_ip']}
            </div>
        </div><hr />";
    }
    $db->free_result($q);
}
$h->endpage();