<?php
require('globals.php');
echo "<h3>{$lang['FJ_TITLE']}</h3>
	{$lang['FJ_INFO']}";
$q = $db->query("SELECT * FROM `fedjail` ORDER BY `fed_out` ASC");
echo "<table class='table table-bordered'>
	<tr>
		<th>
			{$lang['FJ_WHO']}
		</th>
		<th>
			{$lang['FJ_TIME']}
		</th>
		<th>
			{$lang['FJ_RS']}
		</th>
		<th>
			{$lang['FJ_JAILER']}
		</th>
	</tr>";
while ($r = $db->fetch_row($q))
{
    echo "
	<tr>
    	<td>
    		<a href='profile.php?user={$r['fed_userid']}'>{$api->SystemUserIDtoName($r['fed_userid'])}</a>
    	</td>
    	<td>
			" . TimeUntil_Parse($r['fed_out']) . "
		</td>
    	<td>
			{$r['fed_reason']}
		</td>
    	<td>
    		<a href='profile.php?user={$r['fed_jailedby']}'>{$api->SystemUserIDtoName($r['fed_jailedby'])}</a>
    	</td>
    </tr>";
}
echo "</table>";
$db->free_result($q);
$h->endpage();