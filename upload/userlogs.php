<?php
require('globals.php');
if ($ir['vip_days'] == 0)
{
	alert('danger',"Uh Oh!","You may only be here if you have VIP Days.");
	die($h->endpage());
}
$q=$db->query("/*qc=on*/SELECT * FROM `logs` WHERE `log_type` = 'attacking' AND `log_user` = {$userid} ORDER BY `log_id` DESC LIMIT 5");
$q2=$db->query("/*qc=on*/SELECT * FROM `logs` WHERE `log_type` = 'training' AND `log_user` = {$userid} ORDER BY `log_id` DESC LIMIT 5");
$q3=$db->query("/*qc=on*/SELECT * FROM `logs` WHERE `log_type` = 'mining' AND `log_user` = {$userid} ORDER BY `log_id` DESC LIMIT 5");
echo "
<div class='row'>
	<div class='col-lg text-left'>
		<div class='card'>
			<div class='card-header'>
				<h5>Last 5 attack results.</h5>
			</div>
			<div class='card-body'>";
		while ($r = $db->fetch_row($q))
		{
			echo "
				<div class='row'>
					<div class='col'>
						{$r['log_text']}
						<p class='text-muted'>" . DateTime_Parse($r['log_time']) . "</p>
					</div>
				</div>";
		}
		echo "
		</div>
		</div>
	</div>
	<div class='col-lg text-left'>
		<div class='card'>
			<div class='card-header'>
				<h5>Last 5 mining attempts.</h5>
			</div><div class='card-body'>";
		while ($r = $db->fetch_row($q3))
		{
			echo "
				<div class='row'>
					<div class='col'>
						{$r['log_text']}
						<p class='text-muted'>" . DateTime_Parse($r['log_time']) . "</p>
					</div>
				</div>";
		}
		echo "
		</div>
		</div>
	</div>
	<div class='col-lg text-left'>
		<div class='card'>
			<div class='card-header'>
				<h5>Last 5 training results.</h5>
			</div><div class='card-body'>";
		while ($r = $db->fetch_row($q2))
		{
			echo "
				<div class='row'>
					<div class='col'>
						{$r['log_text']}
						<p class='text-muted'>" . DateTime_Parse($r['log_time']) . "</p>
					</div>
				</div>";
		}
		echo "
		</div>
		</div>
	</div>
</div>
<br />";
$q=$db->query("/*qc=on*/SELECT * FROM `logs` WHERE `log_type` = 'xp_gain' AND `log_user` = {$userid} ORDER BY `log_id` DESC LIMIT 5");
//$q2=$db->query("/*qc=on*/SELECT * FROM `logs` WHERE `log_type` = 'training' AND `log_user` = {$userid} ORDER BY `log_id` DESC LIMIT 15");
//$q3=$db->query("/*qc=on*/SELECT * FROM `logs` WHERE `log_type` = 'mining' AND `log_user` = {$userid} ORDER BY `log_id` DESC LIMIT 15");
echo "
<div class='row'>
	<div class='col-lg-4 text-left'>
		<div class='card'>
			<div class='card-header'>
				<h5>Last 5 exp changes.</h5>
			</div><div class='card-body'>";
		while ($r = $db->fetch_row($q))
		{
			echo "
				<div class='row'>
					<div class='col'>
						{$r['log_text']}
						<p class='text-muted'>" . DateTime_Parse($r['log_time']) . "</p>
					</div>
				</div>";
		}
		echo "
		</div>
		</div>
	</div>
</div>";
$h->endpage();