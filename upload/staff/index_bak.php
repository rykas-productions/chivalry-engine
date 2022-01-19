<?php
/*
	File: staff/index.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: Landing page for the staff card. Will show all available staff actions.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/
require('sglobals.php');
echo "<h2>Staff Panel Index</h2>
	<hr />";
if ($api->UserMemberLevelGet($userid, 'admin')) 
{
    $versq = $db->query("/*qc=on*/SELECT VERSION()");
    $MySQLIVersion = $db->fetch_single($versq);
    $db->free_result($versq);
    echo "<div class='card'>
        <div class='card-body'>
            <div class='row'>
                <div class='col-12 col-sm-6 col-lg-4 col-xl-3'>
                    <div class='row'>
                        <div class='col-12'>
                            <small>PHP Version</small>
                        </div>
                        <div class='col-12'>
                            " . phpversion() . "
                        </div>
                    </div>
                </div>
                <div class='col-12 col-sm-6 col-lg-4 col-xl-3'>
                    <div class='row'>
                        <div class='col-12'>
                            <small>Database Version</small>
                        </div>
                        <div class='col-12'>
                            " . $MySQLIVersion . "
                        </div>
                    </div>
                </div>
                <div class='col-12 col-sm-6 col-lg-4 col-xl-3'>
                    <div class='row'>
                        <div class='col-12'>
                            <small>Apache Version</small>
                        </div>
                        <div class='col-12'>
                            " . apache_get_version() . "
                        </div>
                    </div>
                </div>
                <div class='col-12 col-sm-6 col-lg-4 col-xl-3'>
                    <div class='row'>
                        <div class='col-12'>
                            <small>Chivalry Engine Version</small>
                        </div>
                        <div class='col-12'>
                            {$set['Version_Number']}
                        </div>
                    </div>
                </div>
                <div class='col-12 col-sm-6 col-lg-4 col-xl-3'>
                    <div class='row'>
                        <div class='col-12'>
                            <small>API Version</small>
                        </div>
                        <div class='col-12'>
                            {$api->SystemReturnAPIVersion()}
                        </div>
                    </div>
                </div>
                <div class='col-12 col-sm-6 col-lg-4 col-xl-3'>
                    <div class='row'>
                        <div class='col-12'>
                            <small>Moderation Logs</small>
                        </div>
                        <div class='col-12'>
                            <a href='staff_moderation.php?action=listall'>" . number_format($db->fetch_single($db->query("SELECT COUNT(`mod_id`) FROM `staff_moderation_board`"))) . "</a>
                        </div>
                    </div>
                </div>
                <div class='col-12 col-xl-6'>
                    <div class='row'>
                        <div class='col-12'>
                            <small>Engine Updates</small>
                        </div>
                        <div class='col-12'>
                            " . version_json() . "
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>";
}
echo "
</div>
	<div class='col-md-4' align='left'>
		<ul class='nav nav-pills flex-column'>";
if ($api->UserMemberLevelGet($userid, 'admin')) 
{
    echo "
			<li class='nav-item'>
				<a class='nav-link' data-toggle='tab' href='#ADMIN'>Admin</a>
			</li>
			<li class='nav-item'>
				<a class='nav-link' data-toggle='tab' href='#MODULES'>Modules</a>
			</li>
			<li class='nav-item'>
				<a class='nav-link' data-toggle='tab' href='#SHOPS'>Shops</a>
			</li>
			<li class='nav-item'>
				<a class='nav-link' data-toggle='tab' href='#BOTS'>NPCs</a>
			</li>
			<li class='nav-item'>
				<a class='nav-link' data-toggle='tab' href='#JOBS'>Jobs</a>
			</li>
			<li class='nav-item'>
				<a class='nav-link' data-toggle='tab' href='#TOWN'>Towns</a>
			</li>
			<li class='nav-item'>
				<a class='nav-link' data-toggle='tab' href='#ESTATES'>Estates</a>
			</li>
			<li class='nav-item'>
				<a class='nav-link' data-toggle='tab' href='#MINES'>Mines</a>
			</li>
			<li class='nav-item'>
				<a class='nav-link' data-toggle='tab' href='#SMELT'>Smeltery</a>
			</li>
			<li class='nav-item'>
				<a class='nav-link' data-toggle='tab' href='#ACADEMY'>Academy</a>
			</li>
			<li class='nav-item'>
				<a class='nav-link' data-toggle='tab' href='#PROMO'>Promo Codes</a>
			</li>";
}
if ($api->UserMemberLevelGet($userid, 'assistant')) 
{
    echo "	<li class='nav-item'>
				<a class='nav-link' data-toggle='tab' href='#FEDJAIL'>Federal Dungeon Appeals</a>
			</li>
			<li class='nav-item'>
				<a class='nav-link' data-toggle='tab' href='#ITEMS'>Items</a>
			</li>
			<li class='nav-item'>
				<a class='nav-link' data-toggle='tab' href='#USERS'>Users</a>
			</li>
			<li class='nav-item'>
				<a class='nav-link' data-toggle='tab' href='#GUILDS'>Guilds</a>
			</li>
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
	<div class='col-md-8' align='left'>
		<div class='tab-content'>";
if ($api->UserMemberLevelGet($userid, 'admin')) 
{
    echo "<div id='ADMIN' class='tab-pane'>
						<div class='card'>
							<div class='card-body'>
								<a href='staff_settings.php?action=basicset'>Game Settings</a><br />
								<a href='staff_settings.php?action=announce'>Create Announcement</a><br />
								<a href='staff_settings.php?action=diagnostics'>Game Diagnostics</a><br />
								<a href='staff_donate.php?action=addpack'>Add VIP Pack</a><br />
								<a href='staff_donate.php?action=editpack'>Edit VIP Pack</a><br />
								<a href='staff_donate.php?action=delpack'>Delete VIP Pack</a><br />
							</div>
						</div>
					</div>
					<div id='MODULES' class='tab-pane'>
						<div class='card'>
							<div class='card-body'>
								<a href='staff_criminal.php'>Crimes</a>
							</div>
						</div>
					</div>
					<div id='PROMO' class='tab-pane'>
						<div class='card'>
							<div class='card-body'>
								<a href='staff_promo.php?action=addpromo'>Create Promotion Code</a><br />
								<a href='staff_promo.php?action=viewpromo'>View Promotion Codes</a><br />
							</div>
						</div>
					</div>
					<div id='SHOPS' class='tab-pane'>
						<div class='card'>
							<div class='card-body'>
								<a href='staff_shops.php?action=newshop'>Create Shop</a><br />
								<a href='staff_shops.php?action=delshop'>Delete Shop</a><br />
								<a href='staff_shops.php?action=newitem'>Add Stock to Shop</a><br />
							</div>
						</div>
					</div>
					<div id='BOTS' class='tab-pane'>
						<div class='card'>
							<div class='card-body'>
								<a href='staff_bots.php?action=addbot'>Add NPC Bot</a><br />
								<a href='staff_bots.php?action=delbot'>Delete NPC Bot</a><br />
							</div>
						</div>
					</div>
					<div id='TOWN' class='tab-pane'>
						<div class='card'>
							<div class='card-body'>
								<a href='staff_towns.php?action=addtown'>Create Town</a><br />
								<a href='staff_towns.php?action=edittown'>Edit Town</a><br />
								<a href='staff_towns.php?action=deltown'>Delete Town</a><br />
							</div>
						</div>
					</div>
					<div id='ACADEMY' class='tab-pane'>
						<div class='card'>
							<div class='card-body'>
								<a href='staff_academy.php?action=add'>Create Academy Course</a><br />
								<a href='staff_academy.php?action=edit'>Edit Academy Course</a><br />
								<a href='staff_academy.php?action=del'>Delete Academy Course</a><br />
							</div>
						</div>
					</div>
					<div id='ESTATES' class='tab-pane'>
						<div class='card'>
							<div class='card-body'>
								<a href='staff_estates.php?action=addestate'>Create Estate</a><br />
								<a href='staff_estates.php?action=editestate'>Edit Estate</a><br />
								<a href='staff_estates.php?action=delestate'>Delete Estate</a><br />
							</div>
						</div>
					</div>
					<div id='MINES' class='tab-pane'>
						<div class='card'>
							<div class='card-body'>
								<a href='staff_mine.php?action=addmine'>Create Mine</a><br />
								<a href='staff_mine.php?action=editmine'>Edit Mine</a><br />
								<a href='staff_mine.php?action=delmine'>Delete Mine</a>
							</div>
						</div>
					</div>
					<div id='SMELT' class='tab-pane'>
						<div class='card'>
							<div class='card-body'>
								<a href='staff_smelt.php?action=add'>Create Smelting Recipe</a><br />
								<a href='staff_smelt.php?action=del'>Delete Smelting Recipe</a>
							</div>
						</div>
					</div>
					<div id='JOBS' class='tab-pane'>
						<div class='card'>
							<div class='card-body'>
								<a href='staff_jobs.php?action=newjob'>Create Job</a><br />
								<a href='staff_jobs.php?action=jobedit'>Edit Job</a><br />
								<a href='staff_jobs.php?action=jobdele'>Delete Job</a><br />
								<a href='staff_jobs.php?action=newjobrank'>Create Job Rank</a><br />
								<a href='staff_jobs.php?action=jobrankedit'>Edit Job Rank</a><br />
								<a href='staff_jobs.php?action=jobrankdele'>Delete Job Rank</a><br />
							</div>
						</div>
					</div>
					";
}
if ($api->UserMemberLevelGet($userid, 'assistant')) 
{
    echo "<div id='GUILDS' class='tab-pane'>
			<div class='card'>
				<div class='card-body'>
					<a href='staff_guilds.php?action=viewguild'>View Guild</a><br />
					<a href='staff_guilds.php?action=editguild'>Edit Guild</a><br />
					<a href='staff_guilds.php?action=delguild'>Delete Guild</a><br />
					<a href='staff_guilds.php?action=creditguild'>Credit Guild</a><br />
					<a href='staff_guilds.php?action=viewwars'>View Guild Wars</a><br />
					<a href='staff_guilds.php?action=addcrime'>Create Guild Crime</a><br />
					<a href='staff_guilds.php?action=delcrime'>Delete Guild Crime</a><br />
				</div>
			</div>
		</div>
		<div id='FEDJAIL' class='tab-pane'>
			<div class='card'>
				<div class='card-body'>
					<a href='staff_fedjail.php?action=viewappeal'>View Appeals</a><br />
				</div>
			</div>
		</div>";
}
if ($api->UserMemberLevelGet($userid, 'assistant')) 
{
    echo "<div id='ITEMS' class='tab-pane'>
					<div class='card'>
						<div class='card-body'>";
    if ($api->UserMemberLevelGet($userid, 'admin')) 
    {
        echo "
			<a href='staff_items.php?action=createitmgroup'>Create Item Group</a><br />
			<a href='staff_items.php?action=create'>Create Item</a><br />
			<a href='staff_items.php?action=edit'>Edit Item</a><br />
			<a href='staff_items.php?action=delete'>Delete Item</a><br />";
    }
    echo "
							<a href='staff_items.php?action=giveitem'>Gift Item</a><br />
						</div>
					</div>
				</div>
				<div id='POLL' class='tab-pane'>
					<div class='card'>
						<div class='card-body'>
							<a href='staff_polling.php?action=addpoll'>Create Poll</a><br />
							<a href='staff_polling.php?action=closepoll'>End Poll</a><br />
						</div>
					</div>
				</div>
				<div id='USERS' class='tab-pane'>
					<div class='card'>
						<div class='card-body'>";
    if ($api->UserMemberLevelGet($userid, 'admin')) 
    {
        echo "
			<a href='staff_users.php?action=createuser'>Create User</a><br />
			<a href='staff_users.php?action=edituser'>Edit User</a><br />
			<a href='staff_users.php?action=deleteuser'>Delete User</a><br />
			<a href='staff_users.php?action=changepw'>Change User's Password</a><br />
			<a href='staff_settings.php?action=restore'>Restore Users</a><br />
			<a href='staff_settings.php?action=staff'>Set User Level</a><br />";
    }
    echo "
        <a href='staff_users.php?action=masspayment'>Send Mass Payment</a><br />
        <a href='staff_users.php?action=reports'>View Player Reports</a><br />
		<a href='staff_users.php?action=logout'>Force Logout User</a><br />
        <a href='staff_users.php?action=forcelogin'>Control Account</a><br />
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
									<a href='staff_logs.php?action=secmarket'>Chivalry Tokens Market Logs</a>
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
									<a href='staff_logs.php?action=primsend'>Copper Coins Xfer Logs</a>
								</td>
								<td>
                                    <a href='staff_logs.php?action=bomb'>Bomb Logs</a>
								</td>
							</tr>
							</tr>
								<td>
									<a href='staff_logs.php?action=tokenbank'>Token Bank Logs</a>
								</td>
								<td>
                                    <a href='staff_logs.php?action=theft'>Theft Logs</a>
								</td>
							</tr>
                            </tr>
								<td>
									<a href='staff_logs.php?action=loginrewardlogs'>Login Reward Logs</a>
								</td>
								<td>
                                    <a href='staff_logs.php?action=achievementlogs'>Achievement Logs</a>
								</td>
							</tr>
                            </tr>
								<td>
									<a href='staff_logs.php?action=heallogs'>Heal Logs</a>
								</td>
								<td>
                                    <a href='staff_logs.php?action=marriagelogs'>Marriage Logs</a>
								</td>
							</tr>
                            </tr>
								<td>
									<a href='staff_logs.php?action=itemrequestlogs'>Item Request Logs</a>
								</td>
								<td>
                                    <a href='staff_logs.php?action=taxlogs'>Tax Logs</a>
								</td>
							</tr>
                            </tr>
								<td>
									<a href='staff_logs.php?action=hexbagslogs'>Hexbags Logs</a>
								</td>
								<td>
                                    <a href='staff_logs.php?action=borlogs'>BOR Logs</a>
								</td>
							</tr>";
                        if ($api->UserMemberLevelGet($userid, 'admin')) {
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
								<tr>
									<td>
										<a href='staff_logs.php?action=cronlogs'>Reset Logs</a>
									</td>
									<td>
										<a href='staff_logs.php?action=pages'>Page Logs</a>
									</td>
								</tr>
								<tr>
									<td>
										<a href='staff_logs.php?action=votedlogs'>Voting Logs</a>
									</td>
									<td>
										<a href='staff_logs.php?action=preferenceslogs'>Preferences Logs</a>
									</td>
								</tr>";
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
						<div class='card-body'>";
							if ($api->UserMemberLevelGet($userid, 'assistant')) {
								echo "<a href='staff_punish.php?action=editfedjail'>Edit Federal Dungeon</a><br />
										<a href='staff_punish.php?action=unfedjail'>Remove Federal Dungeon</a><br />
										<a href='staff_punish.php?action=mailban'>Mail Ban User</a><br />
										<a href='staff_punish.php?action=unmailban'>Un-Mail Ban User</a><br />
										<a href='staff_punish.php?action=ipsearch'>IP Search</a><br />";
							}
							echo"
							<a href='staff_punish.php?action=fedjail'>Federal Dungeon</a><br />
							<a href='staff_punish.php?action=forumwarn'>Forum Warn User</a><br />
							<a href='staff_punish.php?action=forumban'>Forum Ban User</a><br />
							<a href='staff_punish.php?action=unforumban'>Un-Forum Ban User</a><br />
							<a href='staff_punish.php?action=spamhammer'>Spam Hammer</a><br />";
							if ($api->UserMemberLevelGet($userid, 'admin')) {
								echo "<a href='staff_punish.php?action=massemail'>Send Mass Email</a><br />
										<a href='staff_punish.php?action=massmail'>Send Mass Mail</a><br />
										<a href='staff_punish.php?action=massnotif'>Send Mass Notif</a><br />
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
if ($api->UserMemberLevelGet($userid, 'admin')) 
{
    echo "<div class='col-12'><div class='card'>
            <div class='card-header'>
                Last 15 Staff Actions
            </div>
            <div class='card-body'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `log_user`, `log_text`, `log_time`, `log_ip`, `username`
							 FROM `logs` AS `s`
							 INNER JOIN `users` AS `u`
							 ON `s`.`log_user` = `u`.`userid`
							 WHERE `log_type` = 'staff'
							 ORDER BY `s`.`log_time` DESC
							 LIMIT 15");
    while ($r = $db->fetch_row($q)) 
    {
        echo "
                <div class='row'>
                    <div class='col-12 col-sm-6 col-md-4 col-lg-3'>
                        " . DateTime_Parse($r['log_time']) . "
                    </div>
                    <div class='col-12 col-sm-6 col-md-4 col-lg-3'>
                        <div class='row'>
                            <div class='col-12'>
                                <a href='../profile.php?user={$r['log_user']}'>{$r['username']}</a> [{$r['log_user']}]
                            </div>
                            <div class='col-12'>
                                <small><span class='text-muted'>IP: {$r['log_ip']}</span></small>
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-md-4 col-lg'>
                        {$r['log_text']}
                    </div>
                </div>
                <hr />";
    }
    $db->free_result($q);
    echo '</div></div></div>';
}
$h->endpage();