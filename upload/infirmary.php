<?php
require("globals.php");
$CurrentTime=time();
$PlayerCount=$db->fetch_single($db->query("SELECT COUNT(`infirmary_user`) FROM `infirmary` WHERE `infirmary_out` = {$CurrentTime}"));
echo "<h3>The Infirmary</h3><hr />
<small>There are currently " . number_format($PlayerCount) . " players in the infirmary.</small>
<hr />
<table class='table table-hover table-bordered'>
	<thead>
		<tr>
			<th>
				User [ID]
			</th>
			<th>
				Reason
			</th>
			<th>
				Check-in Time
			</th>
			<th>
				Check-out Time
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
				" . date("F j, Y, g:i:s a", $Infirmary['infirmary_in']) . "
			</td>
			<td>
				" . date("F j, Y, g:i:s a", $Infirmary['infirmary_out']) . "
			</td>
		</tr>";
}
echo "</tbody></table>";
$h->endpage();