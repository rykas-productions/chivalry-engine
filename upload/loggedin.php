<?php
/*
	File:		loggedin.php
	Created: 	4/5/2016 at 12:17AM Eastern Time
	Info: 		The landing page after a user logs in successfully.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
$housequery = 1;
require_once('globals.php');
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/installer.php') && $ir['user_level'] == 'Admin') {
    alert('danger', "Security Error!", "Installer file detected and not locked. Please delete the installer immediately!");
}
if (isset($_POST['pn_update'])) {
    $_POST['pn_update'] = (isset($_POST['pn_update'])) ? strip_tags(stripslashes($_POST['pn_update'])) : '';
    if (strlen($_POST['pn_update']) > 65655) {
        alert('danger', "Uh Oh!", "You can only store 65,655 characters in your personal notepad.", false);
    } else {
        $pn_update_db = $db->escape($_POST['pn_update']);
        $db->query("UPDATE `users`
        			SET `personal_notes` = '{$pn_update_db}'
        			WHERE `userid` = {$userid}");
        $ir['personal_notes'] = $_POST['pn_update'];
        alert('success', "Success!", "You have successfully updated your personal notepad.", false);
    }
}
echo "Welcome back, {$ir['username']}!<br />";
echo "Your last visit was on {$lv}.";
echo "<table class='table table-hover table-bordered'>
<tbody>
	<tr>
		<td>
		Level: " . number_format($ir['level']) . "
		</td>
		<td>
		Experience: " . number_format($ir['xp']) . " / " . number_format($ir['xp_needed']) . "
		</td>
	</tr>
	<tr>
		<td>
		Class: {$ir['class']}
		</td>
		<td>
		VIP Days: " . number_format($ir['vip_days']) . "
		</td>
	</tr>
	<tr>
		<td>
		{$_CONFIG['primary_currency']}: " . number_format($ir['primary_currency']) . "
		</td>
		<td>
		{$_CONFIG['secondary_currency']}: " . number_format($ir['secondary_currency']) . "
		</td>
	</tr>
	<tr>
		<td>
		Health: " . number_format($ir['hp']) . " / " . number_format($ir['maxhp']) . "
		</td>
		<td>
		Energy: " . number_format($ir['energy']) . " / " . number_format($ir['maxenergy']) . "
		</td>
	</tr>
	<tr>
		<td>
		Will: " . number_format($ir['will']) . " / " . number_format($ir['maxwill']) . "
		</td>
		<td>
		Bravery: " . number_format($ir['brave']) . " / " . number_format($ir['maxbrave']) . "
		</td>
	</tr>
</tbody>";

$StrengthRank = getRank($ir['strength'], 'strength');
$StrengthFormat = number_format($ir['strength']);
$AgilityRank = getRank($ir['agility'], 'agility');
$AgilityFormat = number_format($ir['agility']);
$GuardRank = getRank($ir['guard'], 'guard');
$GuardFormat = number_format($ir['guard']);
$IQRank = getRank($ir['iq'], 'iq');
$IQFormat = number_format($ir['iq']);
$LaborRank = getRank($ir['labor'], 'labor');
$LaborFormat = number_format($ir['labor']);
$AllFourFormat = number_format($ir['strength'] + $ir['agility'] + $ir['guard'] + $ir['labor'] + $ir['iq']);

echo "</table>
<h3>Stats</h3>";
echo "
<table class='table table-bordered'>
    <tr>
        <th width='25%'>
            Strength
        </th>
        <td>
            {$StrengthFormat} (Ranked: {$StrengthRank})
        </td>
    </tr>
    <tr>
        <th width='25%'>
            Agility
        </th>
        <td>
            {$AgilityFormat} (Ranked: {$AgilityRank})
        </td>
    </tr>
    <tr>
        <th width='25%'>
            {$_CONFIG['guard_stat']}
        </th>
        <td>
            {$GuardFormat} (Ranked: {$GuardRank})
        </td>
    </tr>
    <tr>
        <th width='25%'>
            {$_CONFIG['labor_stat']}
        </th>
        <td>
            {$LaborFormat} (Ranked: {$LaborRank})
        </td>
    </tr>
    <tr>
        <th width='25%'>
            {$_CONFIG['iq_stat']}
        </th>
        <td>
            {$IQFormat} (Ranked: {$IQRank})
        </td>
    </tr>
    <tr>
        <th width='25%'>
            Total Stats
        </th>
        <td>
            {$AllFourFormat} (Ranked: {$IQRank})
        </td>
    </tr>
</table>
<form method='post'>
    <div class='form-group'>
        <label for='pn_update'>Your Personal Notepad</label>
        <textarea class='form-control' rows='5' name='pn_update' id='pn_update'>{$ir['personal_notes']}</textarea>
    </div>
    <button type='submit' class='btn btn-primary'>Update Notepad</button>
</form>";
$h->endpage();