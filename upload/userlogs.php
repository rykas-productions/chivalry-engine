<?php
require('globals.php');
if ($ir['vip_days'] == 0)
{
	alert('danger',"Uh Oh!","You may only be here if you have VIP Days.");
	die($h->endpage());
}
$vipLogCount=getCurrentUserPref('vipLogView',5);
$q=$db->query("/*qc=on*/SELECT * FROM `logs` WHERE `log_type` = 'attacking' AND `log_user` = {$userid} ORDER BY `log_id` DESC LIMIT {$vipLogCount}");
$q2=$db->query("/*qc=on*/SELECT * FROM `logs` WHERE `log_type` = 'training' AND `log_user` = {$userid} ORDER BY `log_id` DESC LIMIT {$vipLogCount}");
$q3=$db->query("/*qc=on*/SELECT * FROM `logs` WHERE `log_type` = 'mining' AND `log_user` = {$userid} ORDER BY `log_id` DESC LIMIT {$vipLogCount}");
echo "
<div class='row'>
	<div class='col-auto'>
		<div class='card'>
			<div class='card-header'>
				<h5>Last {$vipLogCount} attack results.</h5>
			</div>
			<div class='card-body'>";
		while ($r = $db->fetch_row($q))
		{
			echo "
				<div class='row'>
					<div class='col'>
						{$r['log_text']}
						<p class='text-muted'><small>" . DateTime_Parse($r['log_time']) . "</small></p>
					</div>
				</div>";
		}
		echo "
		</div>
		</div>
	</div>
	<div class='col-auto'>
		<div class='card'>
			<div class='card-header'>
				<h5>Last {$vipLogCount} mining attempts.</h5>
			</div><div class='card-body'>";
		while ($r = $db->fetch_row($q3))
		{
			echo "
				<div class='row'>
					<div class='col'>
						{$r['log_text']}
						<p class='text-muted'><small>" . DateTime_Parse($r['log_time']) . "</small></p>
					</div>
				</div>";
		}
		echo "
		</div>
		</div>
	</div>
	<div class='col-auto'>
		<div class='card'>
			<div class='card-header'>
				<h5>Last {$vipLogCount} training results.</h5>
			</div><div class='card-body'>";
		while ($r = $db->fetch_row($q2))
		{
			echo "
				<div class='row'>
					<div class='col'>
						{$r['log_text']}
						<p class='text-muted'><small>" . DateTime_Parse($r['log_time']) . "</small></p>
					</div>
				</div>";
		}
		echo "
		</div>
		</div>
	</div>";
$q=$db->query("/*qc=on*/SELECT * FROM `logs` WHERE `log_type` = 'xp_gain' AND `log_user` = {$userid} ORDER BY `log_id` DESC LIMIT {$vipLogCount}");
$q2=$db->query("/*qc=on*/SELECT * FROM `logs` WHERE `log_type` = 'bank' AND `log_user` = {$userid} ORDER BY `log_id` DESC LIMIT {$vipLogCount}");
//$q3=$db->query("/*qc=on*/SELECT * FROM `logs` WHERE `log_type` = 'mining' AND `log_user` = {$userid} ORDER BY `log_id` DESC LIMIT {$vipLogCount}");
echo "
	<div class='col-auto'>
		<div class='card'>
			<div class='card-header'>
				<h5>
					Last {$vipLogCount} exp changes.
				</h5>
			</div>
			<div class='card-body'>";
				while ($r = $db->fetch_row($q))
				{
					echo "
						<div class='row'>
							<div class='col'>
								{$r['log_text']}
								<p class='text-muted'><small>" . DateTime_Parse($r['log_time']) . "</small></p>
							</div>
						</div>";
				}
		echo "
			</div>
		</div>
	</div>
	<div class='col-auto'>
		<div class='card'>
			<div class='card-header'>
				<h5>
					Last {$vipLogCount} bank transactions.
				</h5>
			</div>
			<div class='card-body'>";
				while ($r = $db->fetch_row($q2))
				{
					echo "
						<div class='row'>
							<div class='col'>
								{$r['log_text']}
								<p class='text-muted'><small>" . DateTime_Parse($r['log_time']) . "</small></p>
							</div>
						</div>";
				}
		echo "
			</div>
		</div>
	</div>
</div>";
$h->endpage();