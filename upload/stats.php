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
echo "<h3><i class='fas fa-chart-bar'></i> Game Statistics</h3><hr />
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
		  ['Guardian',      {$Defender}]
        ]);
		
		var data4 = google.visualization.arrayToDataTable([
          ['Users', 'Amount'],
          ['Windows 7',     {$Win7}],
		  ['Android',     {$Android}],
		  ['Windows Phone',     {$WinPho}],
		  ['Blackberry',     {$Blackberry}],
		  ['Mobile',     {$Mobile}],
		  ['Unknown',     {$UnknownOS}],
		  ['iPhone',     {$iPhone}],
		  ['iPod',     {$iPod}],
		  ['iPad',     {$iPad}],
		  ['Ubuntu',     {$Ubuntu}],
		  ['Linux',     {$Linux}],
		  ['Mac OS 9',     {$OS9}],
		  ['Windows Vista',     {$WinV}],
		  ['Windows XP',     {$WinXP}],
		  ['Mac OS X',     {$OSX}],
		  ['Windows 8.1',     {$Win81}],
		  ['Windows 10',     {$Win10}],
		  ['Chrome OS',     {$ChromeOS}],
          ['Windows 8',     {$Win8}]
        ]);
		
		var data5 = google.visualization.arrayToDataTable([
          ['Users', 'Amount'],
          ['Chrome',     {$Chrome}],
		  ['Internet Explorer',     {$IE}],
		  ['Safari',     {$Safari}],
		  ['Edge',     {$Edge}],
		  ['Opera',     {$Opera}],
		  ['Netscape',     {$NS}],
		  ['Maxthon',     {$Maxthon}],
		  ['Konqueror',     {$Konqueror}],
		  ['Mobile Browser',     {$MobileBro}],
		  ['Unknown',     {$UnknownBro}],
		  ['Mobile App', {$App}],
          ['Firefox',     {$FF}]
        ]);
		var options2 = {
          title: 'Gender Ratio'
        };
		
		var options3 = {
          title: 'Class Ratio',
		  colors: ['#FF0000', '#0000FF', '#00FF00']
        };
		
		var options4 = {
          title: 'User Operating Systems'
        };
		
		var options5 = {
          title: 'User Browser Choice'
        };

		var chart2 = new google.visualization.PieChart(document.getElementById('gender'));
		var chart3 = new google.visualization.PieChart(document.getElementById('class'));
		var chart4 = new google.visualization.PieChart(document.getElementById('os'));
		var chart5 = new google.visualization.PieChart(document.getElementById('browser'));

		chart2.draw(data2, options2);
		chart3.draw(data3, options3);
		chart4.draw(data4, options4);
		chart5.draw(data5, options5);
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
		<tr>
			<td>
				<div id='browser'></div>
			</td>
			<td>
				<div id='os'></div>
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
					Total Notifications
				</td>
				<td>
					" . number_format($TotalNotif) . "
				</td>
			</tr>
			<tr>
				<td>
					Total Messages
				</td>
				<td>
					" . number_format($TotalMail) . "
				</td>
			</tr>
			<tr>
				<td>
					Copper Coins Withdrawn
				</td>
				<td>
					" . number_format($TotalPrimaryCurrency) . "
				</td>
			</tr>
			<tr>
				<td>
					Copper Coins Banked
				</td>
				<td>
					" . number_format($TotalBank) . "
				</td>
			</tr>
			<tr>
				<td>
					Chivalry Tokens Banked
				</td>
				<td>
					" . number_format($TotalBankToken) . "
				</td>
			</tr>
			<tr>
				<td>
					Total Copper Coins
				</td>
				<td>
					" . number_format($TotalBankandPC) . "
				</td>
			</tr>
			<tr>
				<td>
					Total Chivalry Tokens
				</td>
				<td>
					" . number_format($TotalBankandSC) . "
				</td>
			</tr>
			<tr>
				<td>
					Average Copper Coins per Player
				</td>
				<td>
					" . number_format($AveragePrimaryCurrencyPerPlayer) . "
				</td>
			</tr>
			<tr>
				<td>
					Average Chivalry Tokens per Player
				</td>
				<td>
					" . number_format($AverageSecondaryCurrencyPerPlayer) . "
				</td>
			</tr>
			<tr>
				<td>
					Average Bank Balance per Player
				</td>
				<td>
					" . number_format($AverageBank) . "
				</td>
			</tr>
			<tr>
				<td>
					Average Chivalry Token Bank Account
				</td>
				<td>
					" . number_format($AverageTokenBank) . "
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