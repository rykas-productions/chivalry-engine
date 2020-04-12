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

//Page number
$st = (isset($_GET['st']) && is_numeric($_GET['st'])) ? abs($_GET['st']) : 0;

//Array for acceptable 'orderby' variable
$allowed_by = array('userid', 'username', 'level', 'primary_currency');

//If order by not set, set to userid.
$by = (isset($_GET['by']) && in_array($_GET['by'], $allowed_by, true)) ? $_GET['by'] : 'userid';

//Ascending or descending order?
$allowed_ord = array('asc', 'desc', 'ASC', 'DESC');

//If order not set, set to ascending
$ord = (isset($_GET['ord']) && in_array($_GET['ord'], $allowed_ord, true)) ? $_GET['ord'] : 'ASC';
echo "<h3><i class='fas fa-users'></i> Userlist</h3>";
//Select user count
$cnt = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users`");
$membs = $db->fetch_single($cnt);

//Pagination function!!
echo pagination(100, $membs, $st, "?by={$by}&ord={$ord}&st=");

//Ordering thing
echo "Order By:
<a href='?st={$st}&by=userid&ord={$ord}'>User ID</a>&nbsp;|
<a href='?st={$st}&by=username&ord={$ord}'>Username</a>&nbsp;|
<a href='?st={$st}&by=level&ord={$ord}'>Level</a>&nbsp;|
<a href='?st={$st}&by=primary_currency&ord={$ord}'>Copper Coins</a>
<br />
<a href='?st={$st}&by={$by}&ord=asc'>Ascending</a> |
<a href='?st={$st}&by={$by}&ord=desc'>Descending</a>
<br />
<a href='search.php'>Search</a>
<br /><br />";

//Select the users info
$q = $db->query("/*qc=on*/SELECT `vip_days`, `username`, `userid`, `primary_currency`, `level`, `fedjail`, `vipcolor`, `display_pic`
                FROM `users` ORDER BY `{$by}` {$ord}  LIMIT {$st}, 100");
$no1 = $st + 1;
$no2 = min($st + 100, $membs);
echo "
Showing users {$no1} to {$no2} by order of {$by} {$ord}.
<table class='table table-bordered table-hover table-striped'>
			<tr>
				<th colspan='2'>
					User
				</th>
				<th>
					Info
				</th>
			</tr>
   ";
//Display the users info.
while ($r = $db->fetch_row($q)) {
    $r['username'] = parseUsername($r['userid']);
	$displaypic = ($r['display_pic']) ? "<img src='" . parseImage(parseDisplayPic($r['userid'])) . "' height='75' alt='' title=''>" : '';
    echo "	<tr>
				<td width='15%'>
					{$displaypic}
				</td>
				<td>
					<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]<br />
				</td>
				<td>
					Level: " . number_format($r['level']) . "<br />
					Copper Coins: " . number_format($r['primary_currency']) . "
				</td>
			</tr>";
}
$db->free_result($q);
echo '</table>';
//Pagination function!
echo pagination(100, $membs, $st, "?by={$by}&ord={$ord}&st=");
$h->endpage();