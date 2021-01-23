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
<div class='row'>
	<div class='col-md'>
		<div class='card'>
			<div class='card-header'>
					Player Stats
			</div>
			<div class='card-body text-left'>
				<div class='row'>
					<div class='col'>
						Registered Players
					</div>
					<div class='col'>
						" . number_format($TotalUserCount) . "
					</div>
				</div>
				<div class='row'>
					<div class='col'>
						Males
					</div>
					<div class='col'>
						" . number_format($Male) . "
					</div>
				</div>
				<div class='row'>
					<div class='col'>
						Females
					</div>
					<div class='col'>
						" . number_format($Female) . "
					</div>
				</div>
				<div class='row'>
					<div class='col'>
						Other
					</div>
					<div class='col'>
						" . number_format($OtherGender) . "
					</div>
				</div>
				<div class='row'>
					<div class='col'>
						Warriors
					</div>
					<div class='col'>
						" . number_format($Warrior) . "
					</div>
				</div>
				<div class='row'>
					<div class='col'>
						Rogues
					</div>
					<div class='col'>
						" . number_format($Rogue) . "
					</div>
				</div>
				<div class='row'>
					<div class='col'>
						Guardian
					</div>
					<div class='col'>
						" . number_format($Defender) . "
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class='col-md'>
		<div class='card'>
			<div class='card-header'>
					Game Stats
			</div>
			<div class='card-body text-left'>
				<div class='row'>
					<div class='col'>
						Notifications
					</div>
					<div class='col'>
						" . number_format($TotalNotif) . "
					</div>
				</div>
				<div class='row'>
					<div class='col'>
						Messages
					</div>
					<div class='col'>
						" . number_format($TotalMail) . "
					</div>
				</div>
				<div class='row'>
					<div class='col'>
						Guilds
					</div>
					<div class='col'>
						" . number_format($TotalGuildCount) . "
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class='row'>
	<div class='col-md'>
		<div class='card'>
			<div class='card-header'>
					Copper Coins
			</div>
			<div class='card-body text-left'>
				<div class='row'>
					<div class='col'>
						Withdrawn Copper Coins
					</div>
					<div class='col'>
						" . number_format($TotalPrimaryCurrency) . "
					</div>
				</div>
				<div class='row'>
					<div class='col'>
						Copper in City Bank
					</div>
					<div class='col'>
						" . number_format($TotalBank) . "
					</div>
				</div>
				<div class='row'>
					<div class='col'>
						Copper in Federal Bank
					</div>
					<div class='col'>
						" . number_format($TotalBigBank) . "
					</div>
				</div>
				<div class='row'>
					<div class='col'>
						Copper in Vault
					</div>
					<div class='col'>
						" . number_format($TotalVaultBank) . "
					</div>
				</div>
				<div class='row'>
					<div class='col'>
						Average Copper
					</div>
					<div class='col'>
						" . number_format($AveragePrimaryCurrencyPerPlayer) . "
					</div>
				</div>
				<div class='row'>
					<div class='col'>
						Average City Bank Balance
					</div>
					<div class='col'>
						" . number_format($AverageBank) . "
					</div>
				</div>
				<div class='row'>
					<div class='col'>
						Average Federal Bank Balance
					</div>
					<div class='col'>
						" . number_format($AverageBigBank) . "
					</div>
				</div>
				<div class='row'>
					<div class='col'>
						Average Vault Balance
					</div>
					<div class='col'>
						" . number_format($AverageVaultBank) . "
					</div>
				</div>
				<div class='row'>
					<div class='col'>
						Total Copper Coins
					</div>
					<div class='col'>
						" . number_format($TotalBankandPC) . "
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class='col-md'>
		<div class='card'>
			<div class='card-header'>
					Chivalry Tokens
			</div>
			<div class='card-body text-left'>
				<div class='row'>
					<div class='col'>
						Withdrawn Chivalry Tokens
					</div>
					<div class='col'>
						" . number_format($TotalSecondaryCurrency) . "
					</div>
				</div>
				<div class='row'>
					<div class='col'>
						Banked Chivalry Tokens
					</div>
					<div class='col'>
						" . number_format($TotalBankToken) . "
					</div>
				</div>
				<div class='row'>
					<div class='col'>
						Average Chivalry Tokens
					</div>
					<div class='col'>
						" . number_format($AverageSecondaryCurrencyPerPlayer) . "
					</div>
				</div>
				<div class='row'>
					<div class='col'>
						Average Chivalry Tokens Banked
					</div>
					<div class='col'>
						" . number_format($AverageTokenBank) . "
					</div>
				</div>
				<div class='row'>
					<div class='col'>
						Total Chivalry Tokens
					</div>
					<div class='col'>
						" . number_format($TotalBankandSC) . "
					</div>
				</div>
				<div class='row'>
					<div class='col'>
						Average Sell Price
					</div>
					<div class='col'>
						{$avgprice}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>";
$h->endpage();
?>