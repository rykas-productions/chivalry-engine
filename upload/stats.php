<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<?php
/*
	File:		stats.php
	Created: 	4/5/2016 at 12:27AM Eastern Time
	Info: 		Allows players to view statistics about the game.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");

//Everything's in this file.
require("stats/stats.php");

//This is... messy.
echo "<h3>Statistics Center</h3><hr />
<script>
google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
		
		var data2 = google.visualization.arrayToDataTable([
          ['Users', 'Amount'],
          ['Male',     {$Male}],
          ['Female',      {$Female}]
        ]);
		
		var data3 = google.visualization.arrayToDataTable([
          ['Users', 'Amount'],
          ['Warrior',     {$Warrior}],
          ['Rogue',      {$Rogue}],
		  ['Defender',      {$Defender}]
        ]);
		
		var options2 = {
          title: 'Gender Ratio'
        };
		
		var options3 = {
          title: 'Class Ratio',
		  colors: ['#FF0000', '#0000FF', '#00FF00']
        };

		var chart2 = new google.visualization.PieChart(document.getElementById('gender'));
		var chart3 = new google.visualization.PieChart(document.getElementById('class'));

		chart2.draw(data2, options2);
		chart3.draw(data3, options3);
      }
</script>
	<table width='100%' class='table table-bordered'>
		<tr>
			<td width='50%'>
				<div id='gender'></div>
			</td>
			<td width='50%'>
				<div id='class'></div>
			</td>
		</tr>
	</table>
	<table width='50%' class='table table-bordered table-hover table-striped'>
		<thead>
			<tr>
				<th>
					Statistic
				</th>
				<th width='33%'>
					Value
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					Registered Players
				</td>
				<td>
					" . number_format($TotalUserCount) . "
				</td>
			</tr>
			<tr>
				<td>
					{$_CONFIG['primary_currency']} Withdrawn
				</td>
				<td>
					" . number_format($TotalPrimaryCurrency) . "
				</td>
			</tr>
			<tr>
				<td>
					{$_CONFIG['primary_currency']} Banked
				</td>
				<td>
					" . number_format($TotalBank) . "
				</td>
			</tr>
			<tr>
				<td>
					Total {$_CONFIG['primary_currency']}
				</td>
				<td>
					" . number_format($TotalBankandPC) . "
				</td>
			</tr>
			<tr>
				<td>
					{$_CONFIG['secondary_currency']} in Circulation
				</td>
				<td>
					" . number_format($TotalSecondaryCurrency) . "
				</td>
			</tr>
			<tr>
				<td>
					Average {$_CONFIG['primary_currency']} per Player
				</td>
				<td>
					" . number_format($AveragePrimaryCurrencyPerPlayer) . "
				</td>
			</tr>
			<tr>
				<td>
					Average {$_CONFIG['secondary_currency']} per Player
				</td>
				<td>
					" . number_format($AverageSecondaryCurrencyPerPlayer) . "
				</td>
			</tr>
			<tr>
				<td>
					Average Bank Balance per Players
				</td>
				<td>
					" . number_format($AverageBank) . "
				</td>
			</tr>
			<tr>
				<td>
					Registered Guilds
				</td>
				<td>
					" . number_format($TotalGuildCount) . "
				</td>
			</tr>
		</tbody>
	</table>";
$h->endpage();
?>