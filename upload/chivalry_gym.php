<?php
/*
	File:		gym.php
	Created: 	4/5/2016 at 12:07AM Eastern Time
	Info: 		Allows players to train their stats at the cost of
				will and energy. Players can replenish their energy
				at the Secondary Curreny Temple, and will can be
				increased by buying new estates.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
$macropage = ('chivalry_gym.php');
require("globals.php");
if (!$api->UserHasItem($userid,18,1))
{
    alert("danger", "Uh Oh!", "You need at least one Chivalry Gym Scroll to use the Chivalry Gym.", true, 'index.php');
    die($h->endpage());
}

//User is in the infirmary
if ($api->UserStatus($ir['userid'], 'infirmary')) {
    alert("danger", "Unconscious!", "You cannot train while you're in the infirmary.", true, 'index.php');
    die($h->endpage());
}
//User is in the dungeon.
if ($api->UserStatus($ir['userid'], 'dungeon')) {
    alert("danger", "Locked Up!", "You cannot train while you're in the dungeon.", true, 'index.php');
    die($h->endpage());
}
//Convert POST values to Stat Names.
$statnames = array("Strength" => "strength", "Agility" => "agility", "Guard" => "guard", "Labor" => "labor", "All" => "all");
//Training amount is not set, so set to 0.
if (!isset($_GET["amnt"])) {
    $_GET["amnt"] = 0;
}
$_GET["amnt"] = abs($_GET["amnt"]);
echo "<h3>The Chivalry Gym</h3>";
if (isset($_GET["stat"]) && $_GET["amnt"]) {
    //User trained stat does not exist.
    if (!isset($statnames[$_GET['stat']])) {
        alert("danger", "Uh Oh!", "The stat you've chosen to train does not exist or cannot be trained.", true, 'back');
        die($h->endpage());
    }
    $stat = $statnames[$_GET['stat']];
    //User is trying to train using more energy than they have.
    if ($_GET['amnt'] > $ir['energy']) {
        alert("danger", "Uh Oh!", "You are trying to train using more energy than you currently have.", false);
    } else {
        $gain = 0;
        $extraecho = '';
        if ($stat == 'all') {
            $gainstr = $api->UserTrain($userid, 'strength', $_GET['amnt'] / 4, 4);
            $gainagl = $api->UserTrain($userid, 'agility', $_GET['amnt'] / 4, 4);
            $gaingrd = $api->UserTrain($userid, 'guard', $_GET['amnt'] / 4, 4);
            $gainlab = $api->UserTrain($userid, 'labor', $_GET['amnt'] / 4, 4);
        } else {
            $gain = $api->UserTrain($userid, $_GET['stat'], $_GET['amnt'], 4);
        }
        //Update energy left and stat's new count.
        if ($stat != 'all')
            $NewStatAmount = $ir[$stat] + $gain;
        $EnergyLeft = $ir['energy'] - $_GET['amnt'];
        //Strength is chosen stat
        if ($stat == "strength") {
            alert('success', "Success!", "You begin to lift weights. You have gained " . number_format($gain) . " Strength by completing
			    {$_GET['amnt']} sets of weights. You now have " . number_format($NewStatAmount) . " Strength and {$EnergyLeft} Energy left.", false);
            //Have strength selected for the next training.
            $str_select = "selected";
        } //Agility is the chosen stat.
        elseif ($stat == "agility") {
            alert('success', "Success!", "You beging to run laps. You have gained " . number_format($gain) . " Agility by completing
			    {$_GET['amnt']} laps. You now have " . number_format($NewStatAmount) . " Agility and {$EnergyLeft} Energy left.", false);
            //Have agility selected for the next training.
            $agl_select = "selected";
        } //Guard is the chosen stat.
        elseif ($stat == "guard") {
            alert('success', "Success!", "You begin swimming in the pool. You have gained " . number_format($gain) . " Guard by swimming for
			    {$_GET['amnt']} minutes. You now have " . number_format($NewStatAmount) . " Guard and {$EnergyLeft} left.", false);
            //Have guard selected for the next training.
            $grd_select = "selected";
        } //Labor is the chosen stat.
        elseif ($stat == "labor") {
            alert('success', "Success!", "You begin moving boxes around the gym. You have gained " . number_format($gain) . " Labor by moving
                {$_GET['amnt']} sets of boxes. You now have " . number_format($NewStatAmount) . " and {$EnergyLeft} Energy left.", false);
            //Have guard selected for the next training.
            $lab_select = "selected";
        } elseif ($stat == "all") {
            alert('success', "Success!", "You begin training your Strength, Agility, Guard and Labor all at once. You
                have gained {$gainstr} Strength, {$gainagl} Agility, {$gaingrd} Guard and {$gainlab} Labor. You have
                {$EnergyLeft} Energy left.");
            $all_select = "selected";
        }
        //Log the user's training attempt.
        $api->SystemLogsAdd($userid, 'training', "Trained their {$stat} {$_GET['amnt']} times and gained " . number_format($gain) . ".");
        echo "<hr />";
        $ir['energy'] -= $_GET['amnt'];
        if ($stat != 'all')
            $ir[$stat] += $gain;
		$api->UserTakeItem($userid,18,1);
    }
}
//Small logic to keep the last trained stat selected.
if (!isset($str_select)) {
    $str_select = '';
}
if (!isset($agl_select)) {
    $agl_select = '';
}
if (!isset($grd_select)) {
    $grd_select = '';
}
if (!isset($lab_select)) {
    $lab_select = '';
}
if (!isset($all_select)) {
    $all_select = '';
}
//Grab the user's stat ranks.
$ir['strank'] = get_rank($ir['strength'], 'strength');
$ir['agirank'] = get_rank($ir['agility'], 'agility');
$ir['guarank'] = get_rank($ir['guard'], 'guard');
$ir['labrank'] = get_rank($ir['labor'], 'labor');
$ir['all_four'] = ($ir['labor'] + $ir['strength'] + $ir['agility'] + $ir['guard']);
$ir['af_rank'] = get_rank($ir['all_four'], 'all');
echo "Choose the stat you wish to train, and enter how many times you wish to train it. You can train up to
{$ir['energy']} times. Remember, since this is the Chivalry Gym, you gains are multiplied by four.<hr />
<table class='table table-bordered'>
	<tr>
		<form method='get'>
			<th>
			    Stat
            </th>
			<td>
				<select type='dropdown' name='stat' class='form-control'>
					<option {$str_select} value='Strength'>
					    Strength (Have " . number_format($ir['strength']) . "; Ranked: {$ir['strank']})
                    </option>
					<option {$agl_select} value='Agility'>
					    Agility (Have " . number_format($ir['agility']) . "; Ranked: {$ir['agirank']})
                    </option>
					<option {$grd_select} value='Guard'>
					    Guard (Have " . number_format($ir['guard']) . "; Ranked: {$ir['guarank']})
                    </option>
					<option {$lab_select} value='Labor'>
					    Labor (Have " . number_format($ir['labor']) . "; Ranked: {$ir['labrank']})
                    </option>
					<option {$all_select} value='All'>
					    All Four (Have " . number_format($ir['all_four']) . "; Ranked: {$ir['af_rank']})
                    </option>
				</select>
			</td>
	</tr>
	<tr>
		<th>
		    Training Duration
        </th>
		<td>
		    <input type='number' class='form-control' min='1' max='{$ir['energy']}' name='amnt' value='{$ir['energy']}' />
        </td>
	</tr>
	<tr>
		<td colspan='2'>
		    <input type='submit' class='btn btn-primary' value='Train' />
        </td>
	</tr>
	<tr>
		<td>
		    <a href='temple.php?action=energy' class='btn btn-primary'>Refill Energy</a>
        </td>
        <td>
		    <a href='temple.php?action=will' class='btn btn-primary'>Regen Will</a>
        </td>
	</tr>
	    </form>
</table>";
$h->endpage();