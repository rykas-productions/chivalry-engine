<?php
require('globals.php');
echo "<h3>Referral List</h3><hr />
This page lists all the players you have referred to the game. This is so you can find them easily at a later date.
You can find their name, level, when you referred them and the time they were last active. <b>For the referral contest 
ending Feburary 9th, you've recruited {$ir['ref_count']} players.</b>
<hr />";
$q=$db->query("SELECT * FROM `referals` WHERE `referal_userid` = {$userid}");
if ($db->num_rows($q) == 0)
{
	alert('danger',"Uh Oh!","You have not referred anyone to the game yet.",true,'explore.php');
	die($h->endpage());
}
echo"
<table class='table table-bordered table-striped'>
	<thead>
		<tr>
			<th>
				User
			</th>
			<th>
				Level
			</th>
			<th>
				Last Active
			</th>
			<th>
				Referral Time
			</th>
		</tr>
	</thead>
	<tbody>";
while ($r=$db->fetch_row($q))
{
	$lvl=$api->UserInfoGet($r['refered_id'],'level');
	$lastactive=$api->UserInfoGet($r['refered_id'],'laston');
	echo "
	<tr>
		<td>
			<a href='profile.php?user={$r['refered_id']}'>{$api->SystemUserIDtoName($r['refered_id'])}</a> [{$r['refered_id']}]
		</td>
		<td>
			{$lvl}
		</td>
		<td>
		" . DateTime_Parse($lastactive) . "
		</td>
		<td>
		" . DateTime_Parse($r['time']) . "
		</td>
	</tr>";
}
echo "</tbody></table>";

$h->endpage();