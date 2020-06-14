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
$q = $db->query("/*qc=on*/SELECT `vip_days`, `username`, `userid`, `primary_currency`, `level`, `fedjail`, `vipcolor`, `display_pic`, `laston`, `user_level`
 				 FROM `users`
 				 WHERE `user_level` IN('Admin', 'Forum Moderator', 'Assistant')
 				 ORDER BY `userid` ASC");
while ($r = $db->fetch_row($q)) {
    $staff[$r['userid']] = $r;
}
$db->free_result($q);
echo "
<div class='card'>
	<div class='card-header'>
		<h3>
			Admins
		</h3>
	</div>
</div>";
foreach ($staff as $r) 
{
    if ($r['user_level'] == 'Admin') 
	{
		$r['username'] = parseUsername($r['userid']);
		$un = $api->SystemUserIDtoName($r['userid']);
		$displaypic = "<img src='" . parseDisplayPic($r['userid']) . "' height='75' alt='{$un}&#39;s Display picture.' title='{$un}&#39;s Display picture'>";
		$active = ($r['laston'] > time() - 300) ? "<span class='text-success'>Online</span>" : "<span class='text-danger'>Offline</span>";
		echo "
		<div class='card'>
			<div class='card-body'>
				<div class='row'>
					<div class='col-sm-2'>
						{$displaypic}
					</div>
					<div class='col-sm-2'>
						<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
					</div>
					<div class='col-sm'>
						<div class='row'>
							<div class='col'>
								Level<br />
								" . number_format($r['level']) . "<br />
							</div>
							<div class='col hidden-sm-down'>
								Copper Coins<br />
								" . number_format($r['primary_currency']) . "
							</div>
							<div class='col'>
								{$active}
							</div>
							<div class='col'>
								<a href='inbox.php?action=compose&user={$r['userid']}'>Send Message</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>";
    }
}
echo "<br />
<div class='card'>
	<div class='card-header'>
		<h3>
			Assistants
		</h3>
	</div>
</div>";
foreach ($staff as $r) 
{
    if ($r['user_level'] == 'Assistant') 
	{
        $r['username'] = parseUsername($r['userid']);
		$un = $api->SystemUserIDtoName($r['userid']);
		$displaypic = "<img src='" . parseDisplayPic($r['userid']) . "' height='75' alt='{$un}&#39;s Display picture.' title='{$un}&#39;s Display picture'>";
		$active = ($r['laston'] > time() - 300) ? "<span class='text-success'>Online</span>" : "<span class='text-danger'>Offline</span>";
		echo "
		<div class='card'>
			<div class='card-body'>
				<div class='row'>
					<div class='col-sm-2'>
						{$displaypic}
					</div>
					<div class='col-sm-2'>
						<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
					</div>
					<div class='col-sm'>
						<div class='row'>
							<div class='col'>
								Level<br />
								" . number_format($r['level']) . "<br />
							</div>
							<div class='col hidden-sm-down'>
								Copper Coins<br />
								" . number_format($r['primary_currency']) . "
							</div>
							<div class='col'>
								{$active}
							</div>
							<div class='col'>
								<a href='inbox.php?action=compose&user={$r['userid']}'>Send Message</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>";
    }
}
echo "<br />
<div class='card'>
	<div class='card-header'>
		<h3>
			Forum Moderators
		</h3>
	</div>
</div>";
foreach ($staff as $r) 
{
    if ($r['user_level'] == 'Forum Moderator') 
	{
        $r['username'] = parseUsername($r['userid']);
		$un = $api->SystemUserIDtoName($r['userid']);
		$displaypic = "<img src='" . parseDisplayPic($r['userid']) . "' height='75' alt='{$un}&#39;s Display picture.' title='{$un}&#39;s Display picture'>";
		$active = ($r['laston'] > time() - 300) ? "<span class='text-success'>Online</span>" : "<span class='text-danger'>Offline</span>";
		echo "
		<div class='card'>
			<div class='card-body'>
				<div class='row'>
					<div class='col-sm-2'>
						{$displaypic}
					</div>
					<div class='col-sm-2'>
						<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
					</div>
					<div class='col-sm'>
						<div class='row'>
							<div class='col'>
								Level<br />
								" . number_format($r['level']) . "<br />
							</div>
							<div class='col hidden-sm-down'>
								Copper Coins<br />
								" . number_format($r['primary_currency']) . "
							</div>
							<div class='col'>
								{$active}
							</div>
							<div class='col'>
								<a href='inbox.php?action=compose&user={$r['userid']}'>Send Message</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>";
    }
}
$h->endpage();