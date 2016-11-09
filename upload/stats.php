<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<?php
require("globals.php");
require("stats/stats.php");
echo "<h3>Statistics Center</h3><hr />
<script>
google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Users', 'Amount'],
          ['With',     {$OwnedBank}],
          ['Without',     {$NotOwnedBank}]
        ]);
		
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
          ['Firefox',     {$FF}]
        ]);

        var options = {
          title: 'Users with a bank account'
        };
		var options2 = {
          title: 'Gender Make-up'
        };
		
		var options3 = {
          title: 'Class Make-up',
		  colors: ['#FF0000', '#0000FF', '#00FF00']
        };
		
		var options4 = {
          title: 'User Operating Systems'
        };
		
		var options5 = {
          title: 'User Browsers'
        };

        var chart = new google.visualization.PieChart(document.getElementById('bank'));
		var chart2 = new google.visualization.PieChart(document.getElementById('gender'));
		var chart3 = new google.visualization.PieChart(document.getElementById('class'));
		var chart4 = new google.visualization.PieChart(document.getElementById('os'));
		var chart5 = new google.visualization.PieChart(document.getElementById('browser'));

        chart.draw(data, options);
		chart2.draw(data2, options2);
		chart3.draw(data3, options3);
		chart4.draw(data4, options4);
		chart5.draw(data5, options5);
      }
</script>
	<table width='100%' class='table table-bordered'>
		<tr>
			<td width='33%'><div id='bank'></div></td>
			<td width='33%'><div id='gender'></div></td>
			<td width='33%'><div id='class'></div></td>
		</tr>
		<tr>
			<td>
				<div id='os'></div>
			</td>
			<td>
				<div id='browser'></div>
			</td>
		</tr>
	</table>
	<table width='50%' class='table table-bordered table-hover table-striped'>
		<thead>
			<tr>
				<th>
					Stat Name
				</th>
				<th width='25%'>
					Value
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					Total Registered Users
				</td>
				<td>
					" . number_format($TotalUserCount) . "
				</td>
			</tr>
			<tr>
				<td>
					Total Primary Currency In Circulation
				</td>
				<td>
					" . number_format($TotalPrimaryCurrency) . "
				</td>
			</tr>
			<tr>
				<td>
					Total Secondary Currency In Circulation
				</td>
				<td>
					" . number_format($TotalSecondaryCurrency) . "
				</td>
			</tr>
			<tr>
				<td>
					Average Primary Currency Per Player
				</td>
				<td>
					" . number_format($AveragePrimaryCurrencyPerPlayer) . "
				</td>
			</tr>
			<tr>
				<td>
					Average Secondary Currency Per Player
				</td>
				<td>
					" . number_format($AverageSecondaryCurrencyPerPlayer) . "
				</td>
			</tr>
			<tr>
				<td>
					Amount of Guilds
				</td>
				<td>
					" . number_format($TotalGuildCount) . "
				</td>
			</tr>
		</tbody>
	</table>";
$h->endpage();
?>