<?php
/*
	File:		users.php
	Created: 	4/5/2016 at 12:30AM Eastern Time
	Info: 		Lists the players registered, and allows users to 
				organize them by ID, Name, Level and Currency.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
$st = (isset($_GET['st']) && is_numeric($_GET['st'])) ? abs($_GET['st']) : 0;
$allowed_by = array('userid', 'username', 'level', 'primary_currency');
$by = (isset($_GET['by']) && in_array($_GET['by'], $allowed_by, true)) ? $_GET['by'] : 'userid';
$allowed_ord = array('asc', 'desc', 'ASC', 'DESC');
$ord = (isset($_GET['ord']) && in_array($_GET['ord'], $allowed_ord, true)) ? $_GET['ord'] : 'ASC';
echo "<h3>Userlist</h3>";
$cnt = $db->query("SELECT COUNT(`userid`)
				   FROM `users`");
$membs = $db->fetch_single($cnt);
$db->free_result($cnt);
$pages = round($membs / 100) + 1;
if ($membs % 100 == 0)
{
    $pages--;
}
echo "Page ";
for ($i = 1; $i <= $pages; $i++)
{
    $stl = ($i - 1) * 100;
    echo "<a href='?st={$stl}&by={$by}&ord={$ord}'>{$i}</a> ";
}
echo "<br />
Order By:
	<a href='?st={$st}&by=userid&ord={$ord}'>User ID</a>&nbsp;|
	<a href='?st={$st}&by=username&ord={$ord}'>Username</a>&nbsp;|
	<a href='?st={$st}&by=level&ord={$ord}'>Level</a>&nbsp;|
	<a href='?st={$st}&by=primary_currency&ord={$ord}'>Primary Currency</a>
<br />
<a href='?st={$st}&by={$by}&ord=asc'>Ascending</a> |
<a href='?st={$st}&by={$by}&ord=desc'>Descending</a>
<br /><br />";
$q = $db->query("SELECT `vip_days`, `username`, `userid`, `primary_currency`, `level`, `gender`, `laston`
                FROM `users` ORDER BY `{$by}` {$ord}  LIMIT {$st}, 100");
$no1 = $st + 1;
$no2 = min($st + 100, $membs);
echo "
Showing users {$no1} to {$no2} by order of {$by} {$ord}.
<table class='table table-bordered table-hover table-striped'>
			<tr>
				<th>
					User
				</th>
				<th width='20%'>
					Primary Currency
				</th>
				<th width='10%'>
					Level
				</th>
				<th width='10%' class='hidden-xs'>
					Sex
				</th>
				<th width='10%' class='hidden-xs'>
					Active?
				</th>
			</tr>
   ";
while ($r = $db->fetch_row($q))
{
	$r['username'] = ($r['vip_days']) ? "<span style='color:red; font-weight:bold;'>{$r['username']} <i class='fa fa-shield' data-toggle='tooltip' title='{$r['username']} has {$r['vip_days']} VIP Days remaining.'></i></span>" : $r['username'];
    echo "	<tr>
				<td>
					<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
				</td>
				<td>
					" . number_format($r['primary_currency']) . "
				</td>
				<td>
					{$r['level']}
				</td>
				<td class='hidden-xs'>
					{$r['gender']}
				</td>
				<td class='hidden-xs'>
				" . (($r['laston'] >= $_SERVER['REQUEST_TIME'] - 15 * 60) ? "Online!" : "Offline") . "
				</td>
			</tr>";
}
$db->free_result($q);
echo '</table>';
$h->endpage();