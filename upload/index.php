<?php
/*
	File:		index.php
	Created: 	4/5/2016 at 12:10AM Eastern Time
	Info: 		Main directory file. Will redirect players to login
				if they're not logged in, otherwise will show players
				their stats and other useful information.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require_once('globals.php');
//Put stats into a friendly percentage
$enperc = round($ir['energy'] / $ir['maxenergy'] * 100);
$wiperc = round($ir['will'] / $ir['maxwill'] * 100);
$experc = round($ir['xp'] / $ir['xp_needed'] * 100);
$brperc = round($ir['brave'] / $ir['maxbrave'] * 100);
$hpperc = round($ir['hp'] / $ir['maxhp'] * 100);

$citybank = ($ir['bank'] > -1) ? number_format($ir['bank']) : "<span class='text-danger'>N/A</span>"; 
$fedbank = ($ir['bigbank'] > -1) ? number_format($ir['bigbank']) : "<span class='text-danger'>N/A</span>"; 
$vaultbank = ($ir['vaultbank'] > -1) ? number_format($ir['vaultbank']) : "<span class='text-danger'>N/A</span>"; 
$tokenbank = ($ir['tokenbank'] > -1) ? number_format($ir['tokenbank']) : "<span class='text-danger'>N/A</span>";

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
    if (strlen($_POST['pn_update']) > 65535) {
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
	<div class='col'>";
		alert('info','',"Welcome back, {$ir['username']}!!",false);
		echo"
	</div>
	<div class='col'>";
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
						<div class='col-3'>
							<b>Level</b>
						</div>
						<div class='col'>
							" . number_format($ir['level']) . "
						</div>
					</div>
					<div class='row'>
						<div class='col-3'>
							<b>XP</b>
						</div>
						<div class='col'>
							" . number_format($ir['xp'], 2) . " / " . number_format($ir['xp_needed'], 2) . " ({$experc}%)
						</div>
					</div>
					<div class='row'>
						<div class='col-3'>
							<b>VIP Days</b>
						</div>
						<div class='col'>
							" . number_format($ir['vip_days']) . "
						</div>
					</div>
					<div class='row'>
						<div class='col-3'>
							<b>Class</b>
						</div>
						<div class='col'>
							{$ir['class']}
						</div>
					</div>
					<div class='row'>
						<div class='col-3'>
							<b>Busts</b>
						</div>
						<div class='col'>
							" . number_format($ir['busts']) . "
						</div>
					</div>
					<div class='row'>
						<div class='col-3'>
							<b>KDR</b>
						</div>
						<div class='col'>
							" . number_format($ir['kills']) . ":" . number_format($ir['deaths']) . "
						</div>
					</div>
					<div class='row'>
						<div class='col-3'>
							<a href='skills.php' class='btn btn-primary'>Skills</a>
						</div>
						<div class='col'>
							<a href='achievements.php' class='btn btn-primary'>Achievements</a>
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
						<div class='col-5'>
							<b>Copper Coins</b>
						</div>
						<div class='col'>
							" . number_format($ir['primary_currency']) . "
						</div>
					</div>
					<div class='row'>
						<div class='col-5'>
							<b>Chivalry Tokens</b>
						</div>
						<div class='col'>
							" . number_format($ir['secondary_currency']) . "
						</div>
					</div>
					<div class='row'>
						<div class='col-5'>
							<b>City Bank</b>
						</div>
						<div class='col'>
							{$citybank}
						</div>
					</div>
					<div class='row'>
						<div class='col-5'>
							<b>Federal Bank</b>
						</div>
						<div class='col'>
							{$fedbank}
						</div>
					</div>
					<div class='row'>
						<div class='col-5'>
							<b>Vault Bank</b>
						</div>
						<div class='col'>
							{$vaultbank}
						</div>
					</div>
					<div class='row'>
						<div class='col-5'>
							<b>Chivalry Token Bank</b>
						</div>
						<div class='col'>
							{$tokenbank}
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
				<div class='card-body text-left'>
					<div class='row'>
						<div class='col-3'>
							<b>Health</b>
						</div>
						<div class='col'>
							" . number_format($ir['hp']) . " / " . number_format($ir['maxhp']) . " ({$hpperc}%)
						</div>
					</div>
					<div class='row'>
						<div class='col-3'>
							<b>Energy</b>
						</div>
						<div class='col'>
							" . number_format($ir['energy']) . " / " . number_format($ir['maxenergy']) . " ({$enperc}%)
						</div>
					</div>
					<div class='row'>
						<div class='col-3'>
							<b>VIP Days</b>
						</div>
						<div class='col'>
							" . number_format($ir['vip_days']) . "
						</div>
					</div>
					<div class='row'>
						<div class='col-3'>
							<b>Class</b>
						</div>
						<div class='col'>
							{$ir['class']}
						</div>
					</div>
					<div class='row'>
						<div class='col-3'>
							<b>Will</b>
						</div>
						<div class='col'>
							" . number_format($ir['will']) . " / " . number_format($ir['maxwill']) . " ({$wiperc}%)
						</div>
					</div>
					<div class='row'>
						<div class='col-3'>
							<b>Brave</b>
						</div>
						<div class='col'>
							" . number_format($ir['brave']) . " / " . number_format($ir['maxbrave']) . " ({$brperc}%)
						</div>
					</div>
					<div class='row'>
						<div class='col-3'>
							<b>Luck</b>
						</div>
						<div class='col'>
							" . number_format($ir['luck']) . "%
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
						<div class='col-4'>
							<b>Strength</b>
						</div>
						<div class='col'>
							{$StrengthFormat} (Ranked {$StrengthRank})
						</div>
					</div>
					<div class='row'>
						<div class='col-4'>
							<b>Agility</b>
						</div>
						<div class='col'>
							{$AgilityFormat} (Ranked {$AgilityRank})
						</div>
					</div>
					<div class='row'>
						<div class='col-4'>
							<b>Guard</b>
						</div>
						<div class='col'>
							{$GuardFormat} (Ranked {$GuardRank})
						</div>
					</div>
					<div class='row'>
						<div class='col-4'>
							<b>Labor</b>
						</div>
						<div class='col'>
							{$LaborFormat} (Ranked {$LaborRank})
						</div>
					</div>
					<div class='row'>
						<div class='col-4'>
							<b>IQ</b>
						</div>
						<div class='col'>
							{$IQFormat} (Ranked {$IQRank})
						</div>
					</div>
					<div class='row'>
						<div class='col-4'>
							<b>Total</b>
						</div>
						<div class='col'>
							{$AllFourFormat} (Ranked {$AllStatRank})
						</div>
					</div>
				</div>
			</div>
		</div>
		<br />
	</div>
	<br />";
echo "
<form method='post'>
    <div class='form-group'>
        <label for='pn_update'>Personal Notepad</label>
        <textarea class='form-control' rows='5' name='pn_update' id='pn_update'>{$ir['personal_notes']}</textarea>
    </div>
    <button type='submit' class='btn btn-primary'>Update Notepad</button>
</form>";
$h->endpage();