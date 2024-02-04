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
	<div class='col-12 col-lg-6 col-xxxl-3'>
		<div class='card'>
			<div class='card-header'>
				Player Stats
			</div>
			<div class='card-body text-left'>
				<div class='row'>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Known Players</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($TotalUserCount) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Males</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($Male) . "
                            </div>
                        </div>
                    </div>
					<div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
        						<small><b>Females</b></small>
        					</div>
                            <div class='col-12'>
        						" . shortNumberParse($Female) . "
        					</div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
        						<small><b>Other</b></small>
        					</div>
                            <div class='col-12'>
        						" . shortNumberParse($OtherGender) . "
        					</div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
        						<small><b>Owned Estates</b></small>
        					</div>
                            <div class='col-12'>
        						" . shortNumberParse($TotalEstatesOwned) . "
        					</div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
        						<small><b>Warriors</b></small>
        					</div>
                            <div class='col-12'>
        						" . shortNumberParse($Warrior) . "
        					</div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
        						<small><b>Rogues</b></small>
        					</div>
                            <div class='col-12'>
        						" . shortNumberParse($Rogue) . "
        					</div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
        						<small><b>Guardians</b></small>
        					</div>
                            <div class='col-12'>
        						" . shortNumberParse($Defender) . "
        					</div>
                        </div>
                    </div>
				</div>
			</div>
		</div>
        <br />
	</div>
	<div class='col-12 col-lg-6 col-xxxl-3'>
		<div class='card'>
			<div class='card-header'>
					Game Stats
			</div>
			<div class='card-body text-left'>
                <div class='row'>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Notifications</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($TotalNotif) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Messages</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($TotalMail) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Guilds</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($TotalGuildCount) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Day Reset</b></small>
                            </div>
                            <div class='col-12'>
                                " . TimeUntil_Parse(getNextDayReset()) ."
                            </div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Active Polls</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($activePolls) . "
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		</div>
        <br />
	</div>
    <div class='col-12 col-lg-6 col-xxxl-3'>
		<div class='card'>
			<div class='card-header'>
					Copper Coins
			</div>
			<div class='card-body text-left'>
                <div class='row'>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Withdrawn</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($TotalPrimaryCurrency) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Avg Withdrawn</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($TotalMail) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>City Bank</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($TotalBank) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Avg City Bank</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($AverageBank) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Federal Bank</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($TotalBigBank) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Avg Fed Bank</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($AverageBigBank) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Vault Bank</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($TotalVaultBank) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Avg Vault Bank</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($AverageVaultBank) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Estate Vaults</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($TotalEstateVault) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Total Circulating</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($TotalBankandPC) . "
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		</div>
        <br />
	</div>
    <div class='col-12 col-lg-6 col-xxxl-3'>
		<div class='card'>
			<div class='card-header'>
					Chivalry Tokens
			</div>
			<div class='card-body text-left'>
                <div class='row'>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Withdrawn</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($TotalSecondaryCurrency) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Avg Withdrawn</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($AverageSecondaryCurrencyPerPlayer) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Token Vault</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($TotalBankToken) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Avg Token Vault</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($AverageTokenBank) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Avg Market Price</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($avgprice) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Total Circulating</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($TotalBankandSC) . "
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		</div>
        <br />
	</div>
    <div class='col-12 col-lg-6 col-xxxl-3'>
		<div class='card'>
			<div class='card-header'>
					Estates Owned
			</div>
			<div class='card-body text-left'>
                <div class='row'>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Withdrawn</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($TotalSecondaryCurrency) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Avg Withdrawn</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($AverageSecondaryCurrencyPerPlayer) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Token Vault</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($TotalBankToken) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Avg Token Vault</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($AverageTokenBank) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Avg Market Price</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($avgprice) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-auto'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Total Circulating</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($TotalBankandSC) . "
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		</div>
        <br />
	</div>
</div>";
$h->endpage();
?>