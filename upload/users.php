<?php
require("globals.php");
$st = (isset($_GET['st']) && is_numeric($_GET['st'])) ? abs($_GET['st']) : 0;
$allowed_by = array('userid', 'username', 'level', 'primary_currency');
$by = (isset($_GET['by']) && in_array($_GET['by'], $allowed_by, true)) ? $_GET['by'] : 'userid';
$allowed_ord = array('asc', 'desc', 'ASC', 'DESC');
$ord = (isset($_GET['ord']) && in_array($_GET['ord'], $allowed_ord, true)) ? $_GET['ord'] : 'ASC';
echo "<h3>{$lang['USERLIST_TITLE']}</h3>";
$cnt = $db->query("SELECT COUNT(`userid`)
				   FROM `users`");
$membs = $db->fetch_single($cnt);
$db->free_result($cnt);
$pages = round($membs / 100) + 1;
if ($membs % 100 == 0)
{
    $pages--;
}
echo "{$lang['USERLIST_PAGE']} ";
for ($i = 1; $i <= $pages; $i++)
{
    $stl = ($i - 1) * 100;
    echo "<a href='?st={$stl}&by={$by}&ord={$ord}'>{$i}</a> ";
}
echo "<br />
{$lang['USERLIST_ORDERBY']}:
	<a href='?st={$st}&by=userid&ord={$ord}'>{$lang['USERLIST_ORDER1']}</a>&nbsp;|
	<a href='?st={$st}&by=username&ord={$ord}'>{$lang['USERLIST_ORDER2']}</a>&nbsp;|
	<a href='?st={$st}&by=level&ord={$ord}'>{$lang['USERLIST_ORDER3']}</a>&nbsp;|
	<a href='?st={$st}&by=primary_currency&ord={$ord}'>{$lang['USERLIST_ORDER4']}</a>
<br />
<a href='?st={$st}&by={$by}&ord=asc'>{$lang['USERLIST_ORDER5']}</a> | 
<a href='?st={$st}&by={$by}&ord=desc'>{$lang['USERLIST_ORDER6']}</a>
<br /><br />";
$q =
        $db->query(
                "SELECT `vip_days`, `username`, `userid`, `primary_currency`, `level`,
                `gender`, `laston`
                FROM `users`
                ORDER BY `$by` $ord
                LIMIT $st, 100");
$no1 = $st + 1;
$no2 = min($st + 100, $membs);
echo "
Showing users {$no1} to {$no2} by order of {$by} {$ord}.
<table class='table table-responsive table-bordered table-hover'>
		<thead>
			<tr>
				<th width='10%' class='hidden-xs'>
					{$lang['USERLIST_ORDER1']}
				</th>
				<th>
					{$lang['USERLIST_ORDER2']}
				</th>
				<th width='20%'>
					{$lang['USERLIST_ORDER4']}
				</th>
				<th width='10%'>
					{$lang['USERLIST_ORDER3']}
				</th>
				<th width='10%' class='hidden-xs'>
					{$lang['USERLIST_TH1']}
				</th>
				<th width='10%'>
					{$lang['USERLIST_TH2']}
				</th>
			</tr>
		</thead>
		<tbody>
   ";
while ($r = $db->fetch_row($q))
{
	$r['username'] = ($r['vip_days']) ? "<span style='color:red; font-weight:bold;'>{$r['username']}</span> <span class='glyphicon glyphicon-star' data-toggle='tooltip' title='{$r['username']} has {$r['vip_days']} VIP Days remaining.'></span>" : $r['username'];
    echo "	<tr>
				<td class='hidden-xs'>
					{$r['userid']}
				</td>
				<td>
					<a href='profile.php?user={$r['userid']}'>{$r['username']}</a>
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
				<td>
				" . (($r['laston'] >= $_SERVER['REQUEST_TIME'] - 15 * 60) ? $lang['GEN_ONLINE'] : $lang['GEN_OFFLINE']) . "
				</td>
			</tr>
	</tbody>";
}
$db->free_result($q);
echo '</table>';
$h->endpage();