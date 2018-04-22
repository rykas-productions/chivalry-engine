<?php
require('globals.php');
if ($ir['vip_days'] == 0)
{
	alert('danger',"Uh Oh!","You may only be here if you have VIP Days.");
	die($h->endpage());
}
$q=$db->query("/*qc=on*/SELECT * FROM `logs` WHERE `log_type` = 'attacking' AND `log_user` = {$userid} ORDER BY `log_id` DESC LIMIT 15");
echo"<h3><i class='fas fa-book fa-fw'></i> VIP Logs</h3><hr />
Last 15 attacks involving you.
<div class='table-responsive'>
	<table class='table table-bordered'>
		<tr>
			<th>Time</th>
			<th>Attack Info</th>
		</tr>";
	while ($r = $db->fetch_row($q))
	{
		echo "
		<tr>
			<td>" . DateTime_Parse($r['log_time']) . "</td>
			<td>{$r['log_text']}</td>
		</tr>";
	}
echo"</table>
</div>";
$q=$db->query("/*qc=on*/SELECT * FROM `logs` WHERE `log_type` = 'training' AND `log_user` = {$userid} ORDER BY `log_id` DESC LIMIT 15");
echo"<hr />
Last 15 gains while training
<div class='table-responsive'>
	<table class='table table-bordered'>
		<tr>
			<th>Time</th>
			<th>Training Info</th>
		</tr>";
	while ($r = $db->fetch_row($q))
	{
		echo "
		<tr>
			<td>" . DateTime_Parse($r['log_time']) . "</td>
			<td>{$r['log_text']}</td>
		</tr>";
	}
echo"</table>
</div>";
$h->endpage();