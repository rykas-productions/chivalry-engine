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
echo "<h3>{$lang['UOL_TITLE']}</h3><hr />
[<a href='?act=5'>{$lang['UOL_ACT']}</a>] 
[<a href='?act=15'>{$lang['UOL_ACT1']}</a>] 
[<a href='?act=60'>{$lang['UOL_ACT2']}</a>] 
[<a href='?act=1440'>{$lang['UOL_ACT3']}</a>]<hr />";
if (!isset($_GET['act']))
{
    $_GET['act'] = 15;
}
$_GET['act'] =  (isset($_GET['act']) && is_numeric($_GET['act']))  ? abs($_GET['act']) : 15;
$last_on=time() - ($_GET['act']*60);
$q=$db->query("SELECT * FROM `users` WHERE `laston` > {$last_on}");
echo "<table class='table table-bordered table-striped'>
	<tr>
		<th>
			{$lang['UOL_TH']}
		</th>
		<th>
			{$lang['UOL_TH1']}
		</th>
	</tr>";
while ($r = $db->fetch_row($q))
{
	echo "<tr>
		<td>
			<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
		</td>
		<td>
			" . DateTime_Parse($r['laston']) . "
		</td>
	</tr>";
}
echo "</table>";
$h->endpage();