<?php
/*
	File:		usersonline.php
	Created: 	4/5/2016 at 12:31AM Eastern Time
	Info: 		Lists players on within the time period set. The GET
				can be set to any integer value, and it'll check that
				number minutes ago.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');

//Different options for different time periods. The GET is in minutes.
echo "<h3><i class='fas fa-toggle-on'></i> Users Online</h3><hr />
<div class='row'>
	<div class='col-3'>
		<a href='?act=5' class='btn btn-primary'>5 minutes</a>
	</div>
	<div class='col-3'>
		<a href='?act=15' class='btn btn-primary'>15 minutes</a>
	</div>
	<div class='col-3'>
		<a href='?act=60' class='btn btn-primary'>1 hour</a>
	</div>
	<div class='col-3'>
		<a href='?act=1440' class='btn btn-primary'>24 hours</a>
	</div>
</div>
<br />
<div class='row'>
	<div class='col-3'>
		<a href='?act=10080' class='btn btn-primary'>1 Week</a>
	</div>
	<div class='col-3'>
		<a href='?act=43200' class='btn btn-primary'>1 Month</a>
	</div>
	<div class='col-3'>
		<a href='?act=131400' class='btn btn-primary'>90 Days</a>
	</div>
	<div class='col-3'>
		<a href='?act=525600' class='btn btn-primary'>1 Year</a>
	</div>
</div>
<hr />";

//Time period isn't set, so set it to 15.
if (!isset($_GET['act'])) {
    $_GET['act'] = 15;
}
$_GET['act'] = (isset($_GET['act']) && is_numeric($_GET['act'])) ? abs($_GET['act']) : 15;
$last_on = time() - ($_GET['act'] * 60);

//Select all players on in the time period set in the GET.
$q = $db->query("/*qc=on*/SELECT * FROM `users` WHERE `laston` > {$last_on} ORDER BY `laston` DESC");
while ($r = $db->fetch_row($q)) 
{
    $r['username'] = parseUsername($r['userid']);
	$un = $api->SystemUserIDtoName($r['userid']);
	$displaypic = "<img src='" . parseDisplayPic($r['userid']) . "' height='75' alt='{$un}&#39;s Display picture.' title='{$un}&#39;s Display picture'>";
	$active = ($r['laston'] > time() - 300) ? "<span class='text-success'>Online</span>" : "<span class='text-danger'>Offline</span>";
	$infirm = (user_infirmary($r['userid'])) ? "<span class='text-danger'>Infirmary</span>" : "<span class='text-success'>Healthy</span>";
	$dung = (user_dungeon($r['userid'])) ? "<span class='text-danger'>Dungeon</span>" : "<span class='text-success'>Free</span>";
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
						<div class='col'>
							Copper Coins<br />
							" . number_format($r['primary_currency']) . "
						</div>
						<div class='col'>
							{$active}
						</div>
						<div class='col hidden-sm-down'>
							{$infirm}
						</div>
						<div class='col hidden-sm-down'>
							{$dung}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>";
}
echo "</table>";
$h->endpage();