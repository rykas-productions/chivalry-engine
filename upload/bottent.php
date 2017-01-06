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
		</td>
		<td>
		</td>
	</tr>";
}
echo"</table>";
$h->endpage();