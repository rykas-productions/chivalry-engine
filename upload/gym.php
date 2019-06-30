<?php
/*
	File:		gym.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows the player to train their Strength, Agility, Guard 
				and labor stats.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
	MIT License

	Copyright (c) 2019 TheMasterGeneral

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
*/
$macropage = ('gym.php');
require("globals.php");
//User is in the infirmary
if ($api->user->inInfirmary($ir['userid'])) {
    alert("danger", "Unconscious!", "You cannot train while you're in the infirmary.", true, 'index.php');
    die($h->endpage());
}
//User is in the dungeon.
if ($api->user->inDungeon($ir['userid'])) {
    alert("danger", "Locked Up!", "You cannot train while you're in the dungeon.", true, 'index.php');
    die($h->endpage());
}
//Convert POST values to Stat Names.
$statnames = array("Strength" => "strength", "Agility" => "agility", "Guard" => "guard", "Labor" => "labor", "All" => "all");
//Training amount is not set, so set to 0.
if (!isset($_POST["amnt"])) {
    $_POST["amnt"] = 0;
}
$amnt = filter_input(INPUT_POST, 'amnt', FILTER_SANITIZE_NUMBER_INT) ?: 0;
echo "<h3>The Gym</h3>";
if (isset($_POST["stat"]) && $amnt) {
    //User trained stat does not exist.
    if (!isset($statnames[$_POST['stat']])) {
        alert("danger", "Uh Oh!", "The stat you've chosen to train does not exist or cannot be trained.", true, 'back');
        die($h->endpage());
    }
    //User fails CSRF check.
    if (!isset($_POST['verf']) || !checkCSRF('gym_train', stripslashes($_POST['verf']))) {
        alert('danger', "Action Blocked!", "The action you were trying to do was blocked. It was blocked because you loaded
            another page on the game. If you have not loaded a different page during this time, change your password
            immediately, as another person may have access to your account!", true, 'index.php');
        die($h->endpage());
    }
    $stat = $statnames[$_POST['stat']];
    //User is trying to train using more energy than they have.
    if ($amnt > $ir['energy']) {
        alert("danger", "Uh Oh!", "You are trying to train using more energy than you currently have.", false);
    } else {
        $gain = 0;
        $extraecho = '';
        if ($stat == 'all') {
            $gainstr = $api->user->train($userid, 'strength', $amnt / 4);
            $gainagl = $api->user->train($userid, 'agility', $amnt / 4);
            $gaingrd = $api->user->train($userid, 'guard', $amnt / 4);
            $gainlab = $api->user->train($userid, 'labor', $amnt / 4);
        } else {
            $gain = $api->user->train($userid, $_POST['stat'], $amnt);
        }
        //Update energy left and stat's new count.
        if ($stat != 'all')
            $NewStatAmount = $ir[$stat] + $gain;
        $EnergyLeft = $ir['energy'] - $amnt;
        //Strength is chosen stat
        if ($stat == "strength") {
            alert('success', "Success!", "You begin to lift weights. You have gained {$gain} " . constant("stat_strength") . " by completing
			    {$amnt} sets of weights. You now have {$NewStatAmount} " . constant("stat_strength") . " and {$EnergyLeft} Energy left.", false);
            //Have {$_CONFIG['strength_stat']} selected for the next training.
            $str_select = "selected";
        } //Agility is the chosen stat.
        elseif ($stat == "agility") {
            alert('success', "Success!", "You beging to run laps. You have gained {$gain} " . constant("stat_agility") . " by completing
			    {$amnt} laps. You now have {$NewStatAmount} " . constant("stat_agility") . " and {$EnergyLeft} Energy left.", false);
            //Have agility selected for the next training.
            $agl_select = "selected";
        } //Guard is the chosen stat.
        elseif ($stat == "guard") {
            alert('success', "Success!", "You begin swimming in the pool. You have gained {$gain} " . constant("stat_guard") . " by swimming for
			    {$amnt} minutes. You now have {$NewStatAmount} " . constant("stat_guard") . " and {$EnergyLeft} left.", false);
            //Have guard selected for the next training.
            $grd_select = "selected";
        } //Labor is the chosen stat.
        elseif ($stat == "labor") {
            alert('success', "Success!", "You begin moving boxes around the gym. You have gained {$gain} " . constant("stat_labor") . " by moving
                {$amnt} sets of boxes. You now have {$NewStatAmount} " . constant("stat_labor") . " and {$EnergyLeft} Energy left.", false);
            //Have guard selected for the next training.
            $lab_select = "selected";
        } elseif ($stat == "all") {
            alert('success', "Success!", "You begin training your " . constant("stat_strength") . ", " . constant("stat_agility") . ", 
				" . constant("stat_guard") . " and " . constant("stat_labor") . " all at once. You have gained {$gainstr} 
				" . constant("stat_strength") . ", {$gainagl} " . constant("stat_agility") . ", {$gaingrd} " . constant("stat_guard") . " and 
				{$gainlab} " . constant("stat_labor") . ". You have {$EnergyLeft} Energy left.");
            $all_select = "selected";
        }
        //Log the user's training attempt.
        $api->game->addLog($userid, 'training', "Trained their " . constant("stat_{$stat}") . " {$_POST['amnt']} times and gained {$gain}.");
        echo "<hr />";
        $ir['energy'] -= $amnt;
        if ($stat != 'all')
            $ir[$stat] += $gain;
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
$ir['strank'] = getRank($ir['strength'], 'strength');
$ir['agirank'] = getRank($ir['agility'], 'agility');
$ir['guarank'] = getRank($ir['guard'], 'guard');
$ir['labrank'] = getRank($ir['labor'], 'labor');
$ir['all_four'] = ($ir['labor'] + $ir['strength'] + $ir['agility'] + $ir['guard']);
$ir['af_rank'] = getRank($ir['all_four'], 'all');
//Request CSRF code.
$code = getHtmlCSRF('gym_train');
echo "Choose the stat you wish to train, and enter how many times you wish to train it. You can train up to {$ir['energy']} times.<hr />
<form method='post'>
<div class='container'>
	<div class='row'>
			<div class='col-sm-3'>
			    <h4>Stat</h4>
            </div>
			<div class='col-sm'>
				<select type='dropdown' name='stat' class='form-control'>
					<option {$str_select} value='Strength'>
					    " . constant("stat_strength") . " (Have {$ir['strength']}, Ranked: {$ir['strank']})
                    </option>
					<option {$agl_select} value='Agility'>
					    " . constant("stat_agility") . " (Have {$ir['agility']}, Ranked: {$ir['agirank']})
                    </option>
					<option {$grd_select} value='Guard'>
					    " . constant("stat_guard") . " (Have {$ir['guard']}, Ranked: {$ir['guarank']})
                    </option>
					<option {$lab_select} value='Labor'>
					    " . constant("stat_labor") . " (Have {$ir['labor']}, Ranked: {$ir['labrank']})
                    </option>
					<option {$all_select} value='All'>
					    All Four (Have {$ir['all_four']}, Ranked: {$ir['af_rank']})
                    </option>
				</select>
            </div>
    </div>
    <div class='row'>
            <div class='col-sm-3'>
                <h4>Training Duration</h4>
            </div>
            <div class='col-sm'>
                <input type='number' class='form-control' min='1' max='{$ir['energy']}' name='amnt' value='{$ir['energy']}' />
            </div>
    </div>
	<div class='row'>
		<div class='col-sm'>
		    <input type='submit' class='btn btn-primary' value='Train' />
        </div>
	</div>
	<div class='row'>
		<div class='col-sm'>
		    <a href='temple.php?action=energy' class='btn btn-primary'>Refill Energy</a>
        </div>
        <div class='col-sm'>
		    <a href='temple.php?action=will' class='btn btn-primary'>Regen Will</a>
        </div>
	</div>
	    {$code}
</div>
</form>";
$h->endpage();