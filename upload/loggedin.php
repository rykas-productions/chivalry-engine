<?php
/*
	File:		loggedin.php
	Created: 	4/5/2016 at 12:10AM Eastern Time
	Info: 		Main directory file. Will redirect players to login
				if they're not logged in, otherwise will show players
				their stats and other useful information.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require_once('globals.php');
$citybank = ($ir['bank'] > -1) ? number_format($ir['bank']) : "<span class='text-danger'>N/A</span>";
$fedbank = ($ir['bigbank'] > -1) ? number_format($ir['bigbank']) : "<span class='text-danger'>N/A</span>";
$vaultbank = ($ir['vaultbank'] > -1) ? number_format($ir['vaultbank']) : "<span class='text-danger'>N/A</span>";
$tokenbank = ($ir['tokenbank'] > -1) ? number_format($ir['tokenbank']) : "<span class='text-danger'>N/A</span>";

if (!isset($MUS))
    $MUS = ($db->fetch_row($db->query("/*qc=on*/SELECT * FROM `mining` WHERE `userid` = {$userid} LIMIT 1")));
    
    //Get the stat ranks. Players like this apparently.
    $StrengthRank = get_rank($ir['strength'], 'strength');
    $StrengthFormat = number_format($ir['strength']);
    $AgilityRank = get_rank($ir['agility'], 'agility');
    $AgilityFormat = number_format($ir['agility']);
    $GuardRank = get_rank($ir['guard'], 'guard');
    $GuardFormat = number_format($ir['guard']);
    $IQRank = get_rank($ir['iq'], 'iq');
    $IQFormat = number_format($ir['iq']);
    $LaborRank = get_rank($ir['labor'], 'labor');
    $LaborFormat = number_format($ir['labor']);
    $AllStatRank= get_rank($ir['strength'] + $ir['agility'] + $ir['guard'] + $ir['labor'] + $ir['iq'], 'all');
    $AllFourFormat = number_format($ir['strength'] + $ir['agility'] + $ir['guard'] + $ir['labor'] + $ir['iq']);
    //Player is attempting to update their personal notepad.
    if (isset($_POST['pn_update'])) {
        //Sanitize the notepad entry
        $_POST['pn_update'] = (isset($_POST['pn_update'])) ? strip_tags(stripslashes($_POST['pn_update'])) : '';
        //Notepad update is too large for the database storage
        if (strlen($_POST['pn_update']) > max_unsign_short) {
            alert('danger', "Uh Oh!", "Your notepad is too big to update.", false);
        } else {
            //Update the notepad after escaping the data entered.
            $pn_update_db = $db->escape($_POST['pn_update']);
            $db->query("UPDATE `users`
        			SET `personal_notes` = '{$pn_update_db}'
        			WHERE `userid` = {$userid}");
            $ir['personal_notes'] = $_POST['pn_update'];
            alert('success', "Success!", "Your notepad has been successfully updated.", false);
        }
    }
    echo "<div class='row'>
	<div class='col-lg'>";
    alert('info','',"Welcome back, {$ir['username']}!!",false);
    echo"
	</div>
	<div class='col-lg'>";
    alert('info','',"You were last active {$lv}!",false);
    echo"
	</div>
</div>";
    echo "
	<div class='row'>
		<div class='col-md'>
			<div class='card'>
				<div class='card-header'>
					General Info
				</div>
				<div class='card-body text-left'>
					<div class='row'>
                        <div class='col-12 col-sm-4 col-xxl-3 col-xxxl-2'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Level</b></small>
                                </div>
                                <div class='col-12'>
                                    " . number_format($ir['level']) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-4 col-xxl-3 col-xxxl-2'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>VIP Days</b></small>
                                </div>
                                <div class='col-12'>
                                    " . number_format($ir['vip_days']) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-4 col-xxl-3 col-xxxl-2'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Class</b></small>
                                </div>
                                <div class='col-12'>
                                    {$ir['class']}
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-4 col-xxl-3 col-xxxl-2'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Busts</b></small>
                                </div>
                                <div class='col-12'>
                                    " . shortNumberParse($ir['busts']) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-4 col-lg'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Kills:Deaths</b></small>
                                </div>
                                <div class='col-12'>
                                    " . shortNumberParse($ir['kills']) . ":" . shortNumberParse($ir['deaths']) . "
                                </div>
                            </div>
                        </div>
					</div>
					<div class='row'>
						<div class='col'>
							<a href='skills.php' class='btn btn-primary btn-block'>Skills</a>
						</div>
						<div class='col'>
							<a href='achievements.php' class='btn btn-primary btn-block'>Achievements</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<br />
		<div class='col-md'>
			<div class='card'>
				<div class='card-header'>
					Finances
				</div>
				<div class='card-body text-left'>
					<div class='row'>
                        <div class='col-12 col-sm-4 col-md-6 col-xl-4 col-xxxl-3'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Copper Coins</b></small>
                                </div>
                                <div class='col-12'>
                                    " . number_format($ir['primary_currency']) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-4 col-md-6 col-xl-4 col-xxxl-3'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Chivalry Tokens</b></small>
                                </div>
                                <div class='col-12'>
                                    " . number_format($ir['secondary_currency']) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-4 col-md-6 col-xl-4 col-xxxl-3'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>City Bank</b></small>
                                </div>
                                <div class='col-12'>
                                    {$citybank}
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-4 col-md-6 col-xl-4 col-xxxl-3'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Federal Bank</b></small>
                                </div>
                                <div class='col-12'>
                                    {$fedbank}
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-4 col-md-6 col-xl-4 col-xxxl-3'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Vault Bank</b></small>
                                </div>
                                <div class='col-12'>
                                    {$vaultbank}
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-4 col-md-6 col-xl-4 col-xxxl-3'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Token Bank</b></small>
                                </div>
                                <div class='col-12'>
                                    {$tokenbank}
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</div>
	<br />
	<div class='row'>
		<div class='col-md'>
			<div class='card'>
				<div class='card-header'>
					Regenerative Stats
				</div>
				<div class='card-body'>
					<div class='row'>
                        <div class='col-12 col-sm-6 col-md-12 col-xl-6'>
                            <div class='row'>
                                <div class='col-12 col-md col-xl-12'>
                                    <small><b>Energy</b></small>
                                </div>
                                <div class='col-12 col-md-9 col-lg-8 col-xl-12'>
                                     " . scaledColorProgressBar($ir['energy'], 0, $ir['maxenergy']) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6 col-md-12 col-xl-6'>
                            <div class='row'>
                                <div class='col-12 col-md col-xl-12'>
                                    <small><b>Will</b></small>
                                </div>
                                <div class='col-12 col-md-9 col-lg-8 col-xl-12'>
                                     " . scaledColorProgressBar($ir['will'], 0, $ir['maxwill']) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6 col-md-12 col-xl-6'>
                            <div class='row'>
                                <div class='col-12 col-md col-xl-12'>
                                    <small><b>Brave</b></small>
                                </div>
                                <div class='col-12 col-md-9 col-lg-8 col-xl-12'>
                                     " . scaledColorProgressBar($ir['brave'], 0, $ir['maxbrave']) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6 col-md-12 col-xl-6'>
                            <div class='row'>
                                <div class='col-12 col-md col-xl-12'>
                                    <small><b>Health</b></small>
                                </div>
                                <div class='col-12 col-md-9 col-lg-8 col-xl-12'>
                                     " . scaledColorProgressBar($ir['hp'], 0, $ir['maxhp']) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6 col-md-12 col-xl-6'>
                            <div class='row'>
                                <div class='col-12 col-md col-xl-12'>
                                    <small><b>XP</b></small>
                                </div>
                                <div class='col-12 col-md-9 col-lg-8 col-xl-12'>
                                     " . scaledColorProgressBar($ir['xp'], 0, $ir['xp_needed']) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6 col-md-12 col-xl-6'>
                            <div class='row'>
                                <div class='col-12 col-md col-xl-12'>
                                    <small><b>Mining</b></small>
                                </div>
                                <div class='col-12 col-md-9 col-lg-8 col-xl-12'>
                                     " . scaledColorProgressBar($MUS['miningpower'], 0, $MUS['max_miningpower']) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6 col-md-12 col-xl-6'>
                            <div class='row'>
                                <div class='col-12 col-md col-xl-12'>
                                    <small><b>Luck</b></small>
                                </div>
                                <div class='col-12 col-md-9 col-lg-8 col-xl-12'>
                                     " . scaledColorProgressBar($ir['luck'], 0, 100) . "
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
			</div>
		</div>
		<div class='col-md'>
			<div class='card'>
				<div class='card-header'>
					Combat Stats
				</div>
				<div class='card-body text-left'>
                    <div class='row'>
                        <div class='col-12 col-sm-6 col-xxl-4'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Strength</b> (Rank {$StrengthRank})</small>
                                </div>
                                <div class='col-12'>
                                     {$StrengthFormat}
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6 col-xxl-4'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Agility</b> (Rank {$AgilityRank})</small>
                                </div>
                                <div class='col-12'>
                                     {$AgilityFormat}
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6 col-xxl-4'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Guard</b> (Rank {$GuardRank})</small>
                                </div>
                                <div class='col-12'>
                                     {$GuardFormat}
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6 col-xxl-4'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Labor</b> (Rank {$LaborRank})</small>
                                </div>
                                <div class='col-12'>
                                     {$LaborFormat}
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6 col-xxl-4'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>IQ</b> (Rank {$IQRank})</small>
                                </div>
                                <div class='col-12'>
                                     {$IQFormat}
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6 col-xxl-4'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Overall</b> (Rank {$AllStatRank})</small>
                                </div>
                                <div class='col-12'>
                                     {$AllFourFormat}
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
			</div>
		</div>
		<br />
	</div>
	<br />
<div class='row'>
    <div class='col-12'>
        <div class='card'>
            <div class='card-header'>
                Personal Notepad
            </div>
            <form method='post'>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-12'>
                        <textarea class='form-control' name='pn_update' id='pn_update'>{$ir['personal_notes']}</textarea>
                        <br />
                    </div>
                    <div class='col-12'>
                        <div class='row'>
                            <div class='col-12 col-sm'>
                                <a href='notepad.php' class='btn btn-danger btn-block'>View Other Notes</a>
                            </div>
                            <div class='col-12 col-sm'>
                                <input type='submit' class='btn btn-primary btn-block' value='Update Notepad'>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>";
$h->endpage();