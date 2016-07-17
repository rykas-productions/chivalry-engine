<?php
require("globals.php");
$st =
        (isset($_GET['st']) && is_numeric($_GET['st']))
                ? abs(intval($_GET['st'])) : 0;
$allowed_by = array('userid', 'username', 'level', 'money');
$by =
        (isset($_GET['by']) && in_array($_GET['by'], $allowed_by, true))
                ? $_GET['by'] : 'userid';
$allowed_ord = array('asc', 'desc', 'ASC', 'DESC');
$ord =
        (isset($_GET['ord']) && in_array($_GET['ord'], $allowed_ord, true))
                ? $_GET['ord'] : 'ASC';
echo "<h3>Userlist</h3>";
$cnt = $db->query("SELECT COUNT(`userid`)
				   FROM `users`");
$membs = $db->fetch_single($cnt);
$db->free_result($cnt);
$pages = (int) ($membs / 100) + 1;
if ($membs % 100 == 0)
{
    $pages--;
}
echo "Pages: ";
for ($i = 1; $i <= $pages; $i++)
{
    $stl = ($i - 1) * 100;
    echo "<a href='?st=$stl&amp;by=$by&amp;ord=$ord'>$i</a>&nbsp;";
}
echo "<br />
Order By:
	<a href='?st=$st&by=userid&ord=$ord'>User ID</a>&nbsp;|
	<a href='?st=$st&by=username&ord=$ord'>Username</a>&nbsp;|
	<a href='?st=$st&by=level&ord=$ord'>Level</a>&nbsp;|
	<a href='?st=$st&by=money&ord=$ord'>Money</a>
<br />
<a href='?st=$st&by=$by&ord=asc'>Ascending</a>&nbsp;|
<a href='?st=$st&by=$by&ord=desc'>Descending</a>
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
Showing users $no1 to $no2 by order of $by $ord.
<table class='table table-responsive table-bordered table-hover'>
		<thead>
		<tr>
			<th>User ID</th>
			<th>Name</th>
			<th>Money</th>
			<th>Level</th>
			<th>Gender</th>
			<th>Online?</th>
		</tr>
		</thead>
		<tbody>
   ";
while ($r = $db->fetch_row($q))
{
	$r['username'] = ($r['vip_days']) ? "<span style='color:red; font-weight:bold;'>{$r['username']}</span> <span class='glyphicon glyphicon-star' data-toggle='tooltip' title='{$r['username']} has {$r['vip_days']} VIP Days remaining.'></span>" : $r['username'];
    echo "<tr>
			<td>
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
			<td>
				{$r['gender']}
			</td>
			<td>
			" . (($r['laston'] >= $_SERVER['REQUEST_TIME'] - 15 * 60)
                    ? '<span style="color: green; font-weight:bold;">Online</span>'
                    : '<span style="color: red; font-weight:bold;">Offline</span>')
            . "
			</td>
			</tr>
			</tbody>";
}
$db->free_result($q);
echo '</table>';
$h->endpage();