<?php
require('globals.php');
echo "<h3>{$lang['BOTTENT_TITLE']}</h3><hr />{$lang['BOTTENT_DESC']}<hr />";
$query=$db->query("SELECT * FROM `botlist`");
echo "<table class='table table-bordered'>
<tr>
	<th>
		{$lang['BOTTENT_TH']}
	</th>
	<th>
		{$lang['BOTTENT_TH1']}
	</th>
	<th>
		{$lang['BOTTENT_TH2']}
	</th>
	<th>
		{$lang['BOTTENT_TH3']}
	</th>
	<th>
		{$lang['BOTTENT_TH4']}
	</th>
</tr>";
while ($result = $db->fetch_row($query))
{
	$timequery=$db->query("SELECT `lasthit` FROM `botlist_hits` WHERE `userid` = {$userid} && `botid` = {$result['botuser']}");
	$r2=$db->fetch_single($timequery);
	if ((time() <= ($r2 + $result['botcooldown'])) && ($r2 > 0))
	{
		$cooldown=($r2 + $result['botcooldown']) - time();
		$attack="{$lang['BOTTENT_WAIT']} " . ParseTimestamp($cooldown);
	}
	else
	{
		$attack="<form action='attack.php'>
					<input type='hidden' name='user' value='{$result['botuser']}'>
					<input type='submit' class='btn btn-danger' value='Attack " . $api->SystemUserIDtoName($result['botuser']) . "'>
					</form>";
	}
	echo "
	<tr>
		<td>
			" . $api->SystemUserIDtoName($result['botuser']) . " [{$result['botuser']}]
		</td>
		<td>
			" . $api->UserInfoGet($result['botuser'],'level') . "
		</td>
		<td>
			" . ParseTimestamp($result['botcooldown']) . "
		</td>
		<td>
			" . $api->SystemItemIDtoName($result['botitem']) . "
		</td>
		<td>
			{$attack}
		</td>
	</tr>";
}
echo"</table>";
$h->endpage();