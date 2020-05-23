<?php
require('globals.php');
if ($ir['vip_days'] == 0)
{
	alert('danger',"Uh Oh!","You may only be here if you have VIP Days.");
	die($h->endpage());
}
$q=$db->query("/*qc=on*/SELECT * FROM `logs` WHERE `log_type` = 'attacking' AND `log_user` = {$userid} ORDER BY `log_id` DESC LIMIT 15");
$q2=$db->query("/*qc=on*/SELECT * FROM `logs` WHERE `log_type` = 'training' AND `log_user` = {$userid} ORDER BY `log_id` DESC LIMIT 15");
$q3=$db->query("/*qc=on*/SELECT * FROM `logs` WHERE `log_type` = 'mining' AND `log_user` = {$userid} ORDER BY `log_id` DESC LIMIT 15");
echo "
<div class='row'>
		<div class='col-md text-left'>
		<i>Last 15 attack results.</i>
		<hr />";
		while ($r = $db->fetch_row($q))
		{
			echo "{$r['log_text']}<br />
			<small>" . DateTime_Parse($r['log_time']) . "</small><hr />";
		}
		echo "
	</div>
	<div class='col-md text-left'>
		<i>Last 15 mining attempts.</i>
		<hr />";
		while ($r = $db->fetch_row($q3))
		{
			echo "{$r['log_text']}<br />
			<small>" . DateTime_Parse($r['log_time']) . "</small><hr />";
		}
		echo "
	</div>
	<div class='col-md text-left'>
		<i>Last 15 training results.</i>
		<hr />";
		while ($r = $db->fetch_row($q2))
		{
			echo "{$r['log_text']}<br />
			<small>" . DateTime_Parse($r['log_time']) . "</small><hr />";
		}
		echo "
	</div>
</div>";
$q=$db->query("/*qc=on*/SELECT * FROM `logs` WHERE `log_type` = 'xp_gain' AND `log_user` = {$userid} ORDER BY `log_id` DESC LIMIT 15");
//$q2=$db->query("/*qc=on*/SELECT * FROM `logs` WHERE `log_type` = 'training' AND `log_user` = {$userid} ORDER BY `log_id` DESC LIMIT 15");
//$q3=$db->query("/*qc=on*/SELECT * FROM `logs` WHERE `log_type` = 'mining' AND `log_user` = {$userid} ORDER BY `log_id` DESC LIMIT 15");
echo "
<div class='row'>
		<div class='col-md text-left'>
		<i>Last 15 experience changes.</i>
		<hr />";
		while ($r = $db->fetch_row($q))
		{
			echo "{$r['log_text']}<br />
			<small>" . DateTime_Parse($r['log_time']) . "</small><hr />";
		}
		echo "
	</div>
</div>";
$h->endpage();