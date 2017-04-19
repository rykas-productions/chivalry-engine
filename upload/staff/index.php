<?php
/*
	File: staff/index.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: Landing page for the staff panel. Will show all avaliable staff actions.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/
require('sglobals.php');
echo"<h2>{$lang['STAFF_IDX_TITLE']}</h2>
	<hr />";
$dir= substr(__DIR__, 0, strpos(__DIR__, "\staff"));
if ($api->UserMemberLevelGet($userid,'admin'))
{
	$versq = $db->query("SELECT VERSION()");
    $MySQLIVersion = $db->fetch_single($versq);
    $db->free_result($versq);
	$PHPVersion=phpversion();
	echo"
	<table class='table table-bordered table-hover'>
		<tbody>
			<tr>
				<th>
					{$lang['STAFF_IDX_PHP']}
				</th>
				<td>
					{$PHPVersion}
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_IDX_DB']}
				</th>
				<td>
					{$MySQLIVersion}
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_IDX_CENGINE']}
				</th>
				<td>
					{$set['Version_Number']}
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_IDX_CE_UP']}
				</th>
				<td>
					" . get_cached_file("http://mastergeneral156.pcriot.com/update-checker.php?version={$set['BuildNumber']}",$dir . '\cache\update_check.txt') . "
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_IDX_API']}
				</th>
				<td>
					{$api->SystemReturnAPIVersion()}
				</td>
			</tr>
		</tbody>
	</table>
	<hr />";
		echo"{$lang['STAFF_IDX_ADMIN_TITLE']}<br />
		<ul class='nav nav-tabs nav-justified'>
			<li>
				<a data-toggle='tab' href='#ADMIN'>{$lang['STAFF_IDX_ADMIN_LI']}</a>
			</li>
			<li>
				<a data-toggle='tab' href='#MODULES'>{$lang['STAFF_IDX_ADMIN_LI1']}</a>
			</li>
			<li>
				<a data-toggle='tab' href='#USERS'>{$lang['STAFF_IDX_ADMIN_LI2']}</a>
			</li>
			<li>
				<a data-toggle='tab' href='#ITEMS'>{$lang['STAFF_IDX_ADMIN_LI3']}</a>
			</li>
			<li>
				<a data-toggle='tab' href='#SHOPS'>{$lang['STAFF_IDX_ADMIN_LI4']}</a>
			</li>
			<li>
				<a data-toggle='tab' href='#BOTS'>{$lang['STAFF_IDX_ADMIN_LI6']}</a>
			</li>
			<li>
				<a data-toggle='tab' href='#JOBS'>{$lang['STAFF_IDX_ADMIN_LI7']}</a>
			</li>
			<li>
				<a data-toggle='tab' href='#POLL'>{$lang['STAFF_IDX_ADMIN_LI8']}</a>
			</li>
			<li>
				<a data-toggle='tab' href='#TOWN'>{$lang['STAFF_IDX_ADMIN_LI9']}</a>
			</li>
			<li>
				<a data-toggle='tab' href='#ESTATES'>{$lang['STAFF_IDX_ADMIN_LI10']}</a>
			</li>
		</ul>
		<div class='tab-content'>
			<div id='ADMIN' class='tab-pane fade in'>
				<div class='panel panel-default'>
					<div class='panel-body'>
						<a href='staff_settings.php?action=basicset'>{$lang['STAFF_IDX_ADMIN_TAB1']}</a><br />
						<a href='staff_settings.php?action=announce'>{$lang['STAFF_IDX_ADMIN_TAB2']}</a><br />
						<a href='staff_settings.php?action=diagnostics'>{$lang['STAFF_IDX_ADMIN_TAB3']}</a><br />
						<a href='staff_settings.php?action=errlog'>{$lang['STAFF_IDX_ADMIN_TAB5']}</a><br />
						<a href='staff_settings.php?action=restore'>{$lang['STAFF_IDX_ADMIN_TAB4']}</a><br />
						<a href='staff_settings.php?action=staff'>{$lang['STAFF_IDX_ADMIN_TAB6']}</a><br />
					</div>
				</div>
			</div>
			<div id='MODULES' class='tab-pane fade in'>
				<div class='panel panel-default'>
					<div class='panel-body'>
						<a href='staff_criminal.php'>{$lang['STAFF_IDX_MODULES_TAB1']}</a>
					</div>
				</div>
			</div>
			<div id='USERS' class='tab-pane fade in'>
				<div class='panel panel-default'>
					<div class='panel-body'>
						<a href='staff_users.php?action=createuser'>{$lang['STAFF_IDX_USERS_TAB1']}</a><br />
						<a href='staff_users.php?action=edituser'>{$lang['STAFF_IDX_USERS_TAB2']}</a><br />
						<a href='staff_users.php?action=deleteuser'>{$lang['STAFF_IDX_USERS_TAB3']}</a><br />
						<a href='staff_users.php?action=logout'>{$lang['STAFF_IDX_USERS_TAB4']}</a><br />
						<a href='staff_users.php?action=changepw'>{$lang['STAFF_IDX_USERS_TAB5']}</a><br />
					</div>
				</div>
			</div>
			<div id='ITEMS' class='tab-pane fade in'>
				<div class='panel panel-default'>
					<div class='panel-body'>
						<a href='staff_items.php?action=createitmgroup'>{$lang['STAFF_IDX_ITEMS_TAB1']}</a><br />
						<a href='staff_items.php?action=create'>{$lang['STAFF_IDX_ITEMS_TAB2']}</a><br />
						<a href='staff_items.php?action=delete'>{$lang['STAFF_IDX_ITEMS_TAB3']}</a><br />
						<a href='staff_items.php?action=edit'>{$lang['STAFF_IDX_ITEMS_TAB4']}</a><br />
						<a href='staff_items.php?action=giveitem'>{$lang['STAFF_IDX_ITEMS_TAB5']}</a><br />
					</div>
				</div>
			</div>
			<div id='SHOPS' class='tab-pane fade in'>
				<div class='panel panel-default'>
					<div class='panel-body'>
						<a href='staff_shops.php?action=newshop'>{$lang['STAFF_IDX_SHOPS_TAB1']}</a><br />
						<a href='staff_shops.php?action=delshop'>{$lang['STAFF_IDX_SHOPS_TAB2']}</a><br />
						<a href='staff_shops.php?action=newitem'>{$lang['STAFF_IDX_SHOPS_TAB3']}</a><br />
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
						<a href='staff_academy.php?action=addacademy'>{$lang['STAFF_IDX_NPC_TAB1']}</a><br />
						<a href='staff_academy.php?action=delacademy'>{$lang['STAFF_IDX_NPC_TAB2']}</a><br />
					</div>
				</div>
			</div>
			<div id='BOTS' class='tab-pane fade in'>
				<div class='panel panel-default'>
					<div class='panel-body'>
						<a href='staff_bots.php?action=addbot'>{$lang['STAFF_BOTS_ADD']}</a><br />
						<a href='staff_bots.php?action=delbot'>{$lang['STAFF_BOTS_DEL']}</a><br />
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
			<div id='TOWN' class='tab-pane fade in'>
				<div class='panel panel-default'>
					<div class='panel-body'>
						<a href='staff_towns.php?action=addtown'>{$lang['STAFF_TRAVEL_ADD']}</a><br />
						<a href='staff_towns.php?action=edittown'>{$lang['STAFF_TRAVEL_EDIT']}</a><br />
						<a href='staff_towns.php?action=deltown'>{$lang['STAFF_TRAVEL_DEL']}</a><br />
					</div>
				</div>
			</div>
			<div id='ESTATES' class='tab-pane fade in'>
				<div class='panel panel-default'>
					<div class='panel-body'>
						<a href='staff_estates.php?action=addestate'>{$lang['STAFF_ESTATE_ADD']}</a><br />
						<a href='staff_estates.php?action=editestate'>{$lang['STAFF_ESTATE_EDIT']}</a><br />
						<a href='staff_estates.php?action=delestate'>{$lang['STAFF_ESTATE_DEL']}</a><br />
					</div>
				</div>
			</div>
		</div>
		<hr />";
	}
	if ($api->UserMemberLevelGet($userid,'assistant'))
	{
		echo "{$lang['STAFF_IDX_ASSIST_TITLE']}<br />
		<ul class='nav nav-tabs nav-justified'>
			<li>
				<a data-toggle='tab' href='#LOGS'>{$lang['STAFF_IDX_ASSIST_LI']}</a>
			</li>
			<li>
				<a data-toggle='tab' href='#PERMISSION'>{$lang['STAFF_IDX_ASSIST_LI1']}</a>
			</li>
			<li>
				<a data-toggle='tab' href='#MINES'>{$lang['STAFF_IDX_ASSIST_LI2']}</a>
			</li>
			<li>
				<a data-toggle='tab' href='#SMELT'>{$lang['STAFF_IDX_SMELT_LIST']}</a>
			</li>
		</ul>
		<div class='tab-content'>
			<div id='LOGS' class='tab-pane fade in'>
				<div class='panel panel-default'>
					<div class='panel-body'>
						<a href='staff_logs.php?action=alllogs'>{$lang['STAFF_IDX_LOGS_TAB1']}</a><br />
						<a href='staff_logs.php?action=maillogs'>{$lang['STAFF_IDX_LOGS_TAB26']}</a><br />
						<a href='staff_logs.php?action=userlogs'>{$lang['STAFF_IDX_LOGS_TAB2']}</a><br />
						<a href='staff_logs.php?action=traininglogs'>{$lang['STAFF_IDX_LOGS_TAB3']}</a><br />
						<a href='staff_logs.php?action=attacklogs'>{$lang['STAFF_IDX_LOGS_TAB4']}</a><br />
						<a href='staff_logs.php?action=loginlogs'>{$lang['STAFF_IDX_LOGS_TAB5']}</a><br />
						<a href='staff_logs.php?action=equiplogs'>{$lang['STAFF_IDX_LOGS_TAB6']}</a><br />
						<a href='staff_logs.php?action=banklogs'>{$lang['STAFF_IDX_LOGS_TAB7']}</a><br />
						<a href='staff_logs.php?action=crimelogs'>{$lang['STAFF_IDX_LOGS_TAB8']}</a><br />
						<a href='staff_logs.php?action=itemuselogs'>{$lang['STAFF_IDX_LOGS_TAB9']}</a><br />
						<a href='staff_logs.php?action=itembuylogs'>{$lang['STAFF_IDX_LOGS_TAB10']}</a><br />
						<a href='staff_logs.php?action=itemselllogs'>{$lang['STAFF_IDX_LOGS_TAB17']}</a><br />
						<a href='staff_logs.php?action=itemmarketlogs'>{$lang['STAFF_IDX_LOGS_TAB11']}</a><br />
						<a href='staff_logs.php?action=stafflogs'>{$lang['STAFF_IDX_LOGS_TAB12']}</a><br />
						<a href='staff_logs.php?action=travellogs'>{$lang['STAFF_IDX_LOGS_TAB13']}</a><br />
						<a href='staff_logs.php?action=verifylogs'>{$lang['STAFF_IDX_LOGS_TAB14']}</a><br />
						<a href='staff_logs.php?action=spylogs'>{$lang['STAFF_IDX_LOGS_TAB15']}</a><br />
						<a href='staff_logs.php?action=gamblinglogs'>{$lang['STAFF_IDX_LOGS_TAB16']}</a><br />
						<a href='staff_logs.php?action=fedjaillogs'>{$lang['STAFF_IDX_LOGS_TAB18']}</a><br />
						<a href='staff_logs.php?action=pokes'>{$lang['STAFF_IDX_LOGS_TAB19']}</a><br />
						<a href='staff_logs.php?action=guilds'>{$lang['STAFF_IDX_LOGS_TAB20']}</a><br />
						<a href='staff_logs.php?action=guildvault'>{$lang['STAFF_IDX_LOGS_TAB21']}</a><br />
						<a href='staff_logs.php?action=level'>{$lang['STAFF_IDX_LOGS_TAB22']}</a><br />
						<a href='staff_logs.php?action=temple'>{$lang['STAFF_IDX_LOGS_TAB23']}</a><br />
						<a href='staff_logs.php?action=secmarket'>{$lang['STAFF_IDX_LOGS_TAB24']}</a><br />
						<a href='staff_logs.php?action=mining'>{$lang['STAFF_IDX_LOGS_TAB25']}</a><br />
					</div>
				</div>
			</div>
			<div id='PERMISSION' class='tab-pane fade in'>
				<div class='panel panel-default'>
					<div class='panel-body'>
						<a href='staff_perms.php?action=viewperm'>{$lang['STAFF_IDX_PERM_TAB1']}</a><br />
						<a href='staff_perms.php?action=resetperm'>{$lang['STAFF_IDX_PERM_TAB2']}</a><br />
						<a href='staff_perms.php?action=editperm'>{$lang['STAFF_IDX_PERM_TAB3']}</a>
					</div>
				</div>
			</div>
			<div id='MINES' class='tab-pane fade in'>
				<div class='panel panel-default'>
					<div class='panel-body'>
						<a href='staff_mine.php?action=addmine'>{$lang['STAFF_IDX_MINE_TAB1']}</a><br />
						<a href='staff_mine.php?action=editmine'>{$lang['STAFF_IDX_MINE_TAB2']}</a><br />
						<a href='staff_mine.php?action=delmine'>{$lang['STAFF_IDX_MINE_TAB3']}</a>
					</div>
				</div>
			</div>
			<div id='SMELT' class='tab-pane fade in'>
				<div class='panel panel-default'>
					<div class='panel-body'>
						<a href='staff_smelt.php?action=add'>{$lang['STAFF_IDX_SMELT_TAB1']}</a><br />
						<a href='staff_smelt.php?action=del'>{$lang['STAFF_IDX_SMELT_TAB2']}</a>
					</div>
				</div>
			</div>
		</div>
		<hr />";
	}
	if ($api->UserMemberLevelGet($userid,'forum moderator'))
	{
		echo "{$lang['STAFF_IDX_FM_TITLE']}<br />
		<ul class='nav nav-tabs nav-justified'>
		<li><a data-toggle='tab' href='#PUNISH'>{$lang['STAFF_IDX_FM_LI']}</a></li>
		<li><a data-toggle='tab' href='#FORUMS'>{$lang['STAFF_IDX_FM_LI1']}</a></li>
		</ul>
		<div class='tab-content'>
			<div id='PUNISH' class='tab-pane fade in'>
					<div class='panel panel-default'>
						<div class='panel-body'>
							<a href='staff_punish.php?action=fedjail'>{$lang['STAFF_PUNISHED_FED']}</a><br />
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
		</div>";
	}
	if ($api->UserMemberLevelGet($userid,'admin'))
	{
		echo"
				<hr />
			<h3>{$lang['STAFF_IDX_ACTIONS']}</h3><hr />
			<table class='table table-bordered table-hover'>
					<thead>
					<tr>
						<th>
							{$lang['STAFF_IDX_ACTIONS_TH']}
						</th>
						<th>
							{$lang['STAFF_IDX_ACTIONS_TH1']}
						</th>
						<th>
							{$lang['STAFF_IDX_ACTIONS_TH2']}
						</th>
						<th class='hidden-xs'>
							{$lang['STAFF_IDX_ACTIONS_TH3']}
						</th>
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
							 LIMIT 15");
			while ($r = $db->fetch_row($q))
			{
				echo "
				<tr>
					<td>
						" . DateTime_Parse($r['log_time']) . "
					</td>
					<td>
						<a href='../profile.php?user={$r['log_user']}'>{$r['username']}</a> [{$r['log_user']}]
					</td>
					<td>
						{$r['log_text']}
					</td>
					<td class='hidden-xs'>
						{$r['log_ip']}
					</td>
				</tr>
				";
			}
			$db->free_result($q);
			echo '</tbody></table><hr />';
	}
$h->endpage();