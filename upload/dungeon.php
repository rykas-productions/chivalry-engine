<?php
require("globals.php");
$CurrentTime=time();
$PlayerCount=$db->fetch_single($db->query("SELECT COUNT(`dungeon_user`) FROM `dungeon` WHERE `dungeon_out` = {$CurrentTime}"));
echo "<h3>The Dungeon</h3><hr />
<small>There are currently " . number_format($PlayerCount) . " players in the dungeon.</small>
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
$query = $db->query("SELECT * FROM `dungeon` WHERE `dungeon_out` > {$CurrentTime} ORDER BY `dungeon_out` DESC");
while ($Infirmary=$db->fetch_row($query))
{
	$UserName=$db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$Infirmary['dungeon_user']}"));
	echo "
		<tr>
			<td>
				<a href='profile.php?user={$Infirmary['dungeon_user']}'>{$UserName}</a> [{$Infirmary['dungeon_user']}]
			</td>
			<td>
				{$Infirmary['dungeon_reason']}
			</td>
			<td>
				" . date("F j, Y, g:i:s a", $Infirmary['dungeon_in']) . "
			</td>
			<td>
				" . date("F j, Y, g:i:s a", $Infirmary['dungeon_out']) . "
			</td>
		</tr>";
}
echo "</tbody></table>";
$h->endpage();