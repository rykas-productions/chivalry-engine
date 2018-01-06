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
echo "Welcome back, {$ir['username']}!<br />";
echo "Your last visit was on {$lv}.";
echo "<table class='table table-hover table-bordered'>
<tbody>
	<tr>
		<td>
		    Level: " . number_format($ir['level']) . "
		</td>
		<td>
		    Experience: " . number_format($ir['xp'], 2) . " / " . number_format($ir['xp_needed'], 2) . " ({$experc}%)
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
            Copper Coins: " . number_format($ir['primary_currency']) . "
		</td>
		<td>
            Chivalry Tokens: " . number_format($ir['secondary_currency']) . "
		</td>
	</tr>
	<tr>
		<td>
		    Health: " . number_format($ir['hp']) . " / " . number_format($ir['maxhp']) . " ({$hpperc}%)
		</td>
		<td>
            Energy: " . number_format($ir['energy']) . " / " . number_format($ir['maxenergy']) . " ({$enperc}%)
		</td>
	</tr>
	<tr>
		<td>
		    Will: " . number_format($ir['will']) . " / " . number_format($ir['maxwill']) . " ({$wiperc}%)
		</td>
		<td>
		    Bravery: " . number_format($ir['brave']) . " / " . number_format($ir['maxbrave']) . " ({$brperc}%)
		</td>
	</tr>
	<tr>
		<td>
		    Kills/Deaths: {$ir['kills']} / {$ir['deaths']}
		</td>
		<td>
		    Busts: {$ir['busts']}
		</td>
	</tr>
</tbody>";

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

echo "</table>
<h3>Stats</h3>";
echo "
<table class='table table-bordered'>
    <tr>
        <th width='25%'>
            <i class='ra ra-muscle-up'></i> Strength
        </th>
        <td>
            {$StrengthFormat} (Ranked: {$StrengthRank})
        </td>
    </tr>
    <tr>
        <th width='25%'>
            <i class='ra ra-player-dodge'></i> Agility
        </th>
        <td>
            {$AgilityFormat} (Ranked: {$AgilityRank})
        </td>
    </tr>
    <tr>
        <th width='25%'>
            <i class='ra ra-player-pain'></i> Guard
        </th>
        <td>
            {$GuardFormat} (Ranked: {$GuardRank})
        </td>
    </tr>
    <tr>
        <th width='25%'>
            <i class='ra ra-player-teleport'></i> Labor
        </th>
        <td>
            {$LaborFormat} (Ranked: {$LaborRank})
        </td>
    </tr>
    <tr>
        <th width='25%'>
            <i class='ra ra-aware'></i> IQ
        </th>
        <td>
            {$IQFormat} (Ranked: {$IQRank})
        </td>
    </tr>
    <tr>
        <th width='25%'>
            <i class='ra ra-player-king'></i> Total Stats
        </th>
        <td>
            {$AllFourFormat} (Ranked: {$AllStatRank})
        </td>
    </tr>
</table>
<form method='post'>
    <div class='form-group'>
        <label for='pn_update'>Personal Notepad</label>
        <textarea class='form-control' rows='5' name='pn_update' id='pn_update'>{$ir['personal_notes']}</textarea>
    </div>
    <button type='submit' class='btn btn-primary'>Update Notepad</button>
</form>";
$h->endpage();