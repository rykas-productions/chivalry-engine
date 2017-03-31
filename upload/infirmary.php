<?php
require("globals.php");
$CurrentTime=time();
$PlayerCount=$db->fetch_single($db->query("SELECT COUNT(`infirmary_user`) FROM `infirmary` WHERE `infirmary_out` = {$CurrentTime}"));
echo "<h3>{$lang['DUNGINFIRM_TITLE1']}</h3><hr />
<small>{$lang['DUNGINFIRM_INFO']} " . number_format($PlayerCount) . " {$lang['DUNGINFIRM_INFO2']}</small>
<hr />
<table class='table table-hover table-bordered'>
	<thead>
		<tr>
			<th>
				{$lang['DUNGINFIRM_TD1']}
			</th>
			<th>
				{$lang['DUNGINFIRM_TD2']}
			</th>
			<th>
				{$lang['DUNGINFIRM_TD3']}
			</th>
			<th>
				{$lang['DUNGINFIRM_TD4']}
			</th>
		</tr>
	</thead>
	<tbody>";
$query = $db->query("SELECT * FROM `infirmary` WHERE `infirmary_out` > {$CurrentTime} ORDER BY `infirmary_out` DESC");
while ($Infirmary=$db->fetch_row($query))
{
	$UserName=$db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$Infirmary['infirmary_user']}"));
	echo "
		<tr>
			<td>
				<a href='profile.php?user={$Infirmary['infirmary_user']}'>{$UserName}</a> [{$Infirmary['infirmary_user']}]
			</td>
			<td>
				{$Infirmary['infirmary_reason']}
			</td>
			<td>
				" . DateTime_Parse($Infirmary['infirmary_in']) . "
			</td>
			<td>
				" . TimeUntil_Parse($Infirmary['infirmary_out']) . "
			</td>
		</tr>";
}
echo "</tbody></table>";
$h->endpage();