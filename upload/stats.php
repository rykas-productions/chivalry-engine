<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<?php
/*
	File:		stats.php
	Created: 	4/5/2016 at 12:27AM Eastern Time
	Info: 		Allows players to view statistics about the game.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
$disablespeed=1;
require("globals.php");

//Everything's in this file.
require("stats/stats.php");

//This is... messy.
echo "<h3><i class='fas fa-chart-bar'></i> Game Statistics</h3><hr />
[<a href='stats.php'>All Users</a>] || [<a href='?active'>Users active in last week</a>]
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
					Males
				</td>
				<td>
					" . number_format($Male) . "
				</td>
			</tr>
			<tr>
				<td>
					Females
				</td>
				<td>
					" . number_format($Female) . "
				</td>
			</tr>
			<tr>
				<td>
					Warriors
				</td>
				<td>
					" . number_format($Warrior) . "
				</td>
			</tr>
			<tr>
				<td>
					Rogues
				</td>
				<td>
					" . number_format($Rogue) . "
				</td>
			</tr>
			<tr>
				<td>
					Guardians
				</td>
				<td>
					" . number_format($Defender) . "
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
					Copper in City Bank
				</td>
				<td>
					" . number_format($TotalBank) . "
				</td>
			</tr>
            <tr>
				<td>
					Copper in Federal Bank
				</td>
				<td>
					" . number_format($TotalBigBank) . "
				</td>
			</tr>
			<tr>
				<td>
					Copper in Vault
				</td>
				<td>
					" . number_format($TotalVaultBank) . "
				</td>
			</tr>
			<tr>
				<td>
					Chivalry Tokens Withdrawn
				</td>
				<td>
					" . number_format($TotalSecondaryCurrency) . "
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
					Average City Bank Balance
				</td>
				<td>
					" . number_format($AverageBank) . "
				</td>
			</tr>
            <tr>
				<td>
					Average Federal Bank Balance
				</td>
				<td>
					" . number_format($AverageBigBank) . "
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
            <tr>
				<td>
					Original Theme
				</td>
				<td>
					" . number_format($Default) . "
				</td>
			</tr>
            <tr>
				<td>
					Darkly Theme
				</td>
				<td>
					" . number_format($Darkly) . "
				</td>
			</tr>
            <tr>
				<td>
					Slate Theme
				</td>
				<td>
					" . number_format($Slate) . "
				</td>
			</tr>
            <tr>
				<td>
					Cyborg Theme
				</td>
				<td>
					" . number_format($Cyborg) . "
				</td>
			</tr>
			<tr>
				<td>
					United Theme
				</td>
				<td>
					" . number_format($United) . "
				</td>
			</tr>
			<tr>
				<td>
					Cerulean Theme
				</td>
				<td>
					" . number_format($Cerulean) . "
				</td>
			</tr>
			<tr>
				<td>
					Castle Theme
				</td>
				<td>
					" . number_format($Castle) . "
				</td>
			</tr>
			<tr>
				<td>
					Sunset Theme
				</td>
				<td>
					" . number_format($Sunset) . "
				</td>
			</tr>
		</tbody>
	</table>";
$h->endpage();
?>