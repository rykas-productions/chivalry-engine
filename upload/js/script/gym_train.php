<?php
$multi=1.0;
$menuhide=1;
$nohdr=true;
require_once('../../globals.php');
if (isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD']))
{
    if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST')
    {
        // Ignore a GET request
        header('HTTP/1.1 400 Bad Request');
        exit;
    }
}
if (!is_ajax())
{
    header('HTTP/1.1 400 Bad Request');
    exit;
}
//User is in the infirmary
if ($api->UserStatus($ir['userid'], 'infirmary')) {
    alert("danger", "Unconscious!", "You cannot train while you're in the infirmary.", false);
    die($h->endpage());
}
//User is in the dungeon.
if ($api->UserStatus($ir['userid'], 'dungeon')) {
    alert("danger", "Locked Up!", "You cannot train while you're in the dungeon.", false);
    die($h->endpage());
}
$statnames = array("Strength" => "strength", "Agility" => "agility", "Guard" => "guard", "Labor" => "labor", "All" => "all");
if (isset($_POST["stat"]) && $_POST["amnt"]) 
{
	if (!isset($statnames[$_POST['stat']])) 
	{
        alert("danger", "Uh Oh!", "The stat you've chosen to train does not exist or cannot be trained.", false);
    }
	if (empty($_POST["amnt"]))
	{
		alert("danger", "Uh Oh!", "Please input how many times you wish to train.", false);
	}
    $stat = $statnames[$_POST['stat']];
	if ($_POST['amnt'] > $ir['energy']) 
	{
        alert("danger", "Uh Oh!", "You are trying to train using more energy than you currently have.", false);
    }
	else
	{
		$gain = 0;
        $extraecho = '';
        if ($stat == 'all') 
        {
            $strengthSplit=getCurrentUserPref('strengthSplit', 25);
            $agilitySplit=getCurrentUserPref('agilitySplit', 25);
            $guardSplit=getCurrentUserPref('guardSplit', 25);
            $laborSplit=getCurrentUserPref('laborSplit', 25);
            
            $gainstr = $api->UserTrain($userid, 'strength', $_POST['amnt'] / (100 /$strengthSplit), $multi);
            $gainagl = $api->UserTrain($userid, 'agility', $_POST['amnt'] / (100 /$agilitySplit), $multi);
            $gaingrd = $api->UserTrain($userid, 'guard', $_POST['amnt'] / (100 /$guardSplit), $multi);
            $gainlab = $api->UserTrain($userid, 'labor', $_POST['amnt'] / (100 /$laborSplit), $multi);
        } else {
            $gain = $api->UserTrain($userid, $_POST['stat'], $_POST['amnt'], $multi);
        }
        //Update energy left and stat's new count.
        if ($stat != 'all')
            $NewStatAmount = $ir[$stat] + $gain;
        $EnergyLeft = $ir['energy'] - $_POST['amnt'];
        //Strength is chosen stat
        if ($stat == "strength") {
            alert('success', "Success!", "You begin to lift weights. You have gained " . shortNumberParse($gain) . " Strength by completing
			    " . shortNumberParse($_POST['amnt']) . " sets of weights. You now have " . shortNumberParse($NewStatAmount) . " Strength and {$EnergyLeft} Energy left.", false);
            //Have strength selected for the next training.
            $str_select = "selected";
			setcookie('lastTrainedStat', 'strength', time() + 86400);
        } //Agility is the chosen stat.
        elseif ($stat == "agility") {
            alert('success', "Success!", "You begin to run laps. You have gained " . shortNumberParse($gain) . " Agility by completing
			    " . shortNumberParse($_POST['amnt']) . " laps. You now have " . shortNumberParse($NewStatAmount) . " Agility and {$EnergyLeft} Energy left.", false);
            //Have agility selected for the next training.
            $agl_select = "selected";
			setcookie('lastTrainedStat', 'agility', time() + 86400);
        } //Guard is the chosen stat.
        elseif ($stat == "guard") {
            alert('success', "Success!", "You begin swimming in the pool. You have gained " . shortNumberParse($gain) . " Guard by swimming for
			    " . shortNumberParse($_POST['amnt']) . " minutes. You now have " . shortNumberParse($NewStatAmount) . " Guard and {$EnergyLeft} left.", false);
            //Have guard selected for the next training.
            $grd_select = "selected";
			setcookie('lastTrainedStat', 'guard', time() + 86400);
        } //Labor is the chosen stat.
        elseif ($stat == "labor") {
            alert('success', "Success!", "You begin moving boxes around the gym. You have gained " . shortNumberParse($gain) . " Labor by moving
                " . shortNumberParse($_POST['amnt']) . " sets of boxes. You now have " . shortNumberParse($NewStatAmount) . " Labor and {$EnergyLeft} Energy left.", false);
            //Have guard selected for the next training.
            $lab_select = "selected";
			setcookie('lastTrainedStat', 'labor', time() + 86400);
        } elseif ($stat == "all") {
            alert('success', "Success!", "You begin training your Strength, Agility, Guard and Labor all at once. You
                have gained " . shortNumberParse($gainstr) . " Strength, " . shortNumberParse($gainagl) . " Agility,
                " . shortNumberParse($gaingrd) . " Guard and " . shortNumberParse($gainlab) . " Labor. You have
                {$EnergyLeft} Energy left.");
            $all_select = "selected";
			setcookie('lastTrainedStat', 'all', time() + 86400);
        }
        //Log the user's training attempt.
        $api->SystemLogsAdd($userid, 'training', "[Normal Gym] {$_POST['amnt']} energy for " . shortNumberParse($gain) . " {$stat}.");
        echo "<hr />";
        $ir['energy'] -= $_POST['amnt'];
        if ($stat != 'all')
            $ir[$stat] += $gain;
        $newEnergyPerc = $api->UserInfoGet($userid, 'energy', true);
        $newWillPerc = $api->UserInfoGet($userid, 'will', true);
        $newEnergy = $api->UserInfoGet($userid, 'energy');
        $newWill = $api->UserInfoGet($userid, 'will');
		?>
		<script>
			document.getElementById('gymEnergy').innerHTML = <?php echo $newEnergyPerc ?>;
			document.getElementById('gymWill').innerHTML = <?php echo $newWillPerc; ?>;
			document.getElementById('trainTimes').value = <?php echo $newEnergy; ?>;
			document.getElementById('trainTimes').max = <?php echo $newEnergy; ?>;
			document.getElementById('trainTimesTotal').innerHTML = <?php echo $newEnergy; ?>;
			
			document.getElementById('ui_energy_perc').innerHTML = "<?php echo "{$newEnergyPerc}%"; ?>";
			document.getElementById('ui_energy_bar').style.width = "<?php echo "{$newEnergyPerc}%"; ?>";
			document.getElementById('ui_energy_bar_info').innerHTML = "<?php echo "{$newEnergyPerc}% (" . number_format($newEnergy) . " / " . number_format($ir['maxenergy']) . ")"; ?>";

			document.getElementById('ui_will_perc').innerHTML = "<?php echo "{$newWillPerc}%"; ?>";
			document.getElementById('ui_will_bar').style.width = "<?php echo "{$newWillPerc}%"; ?>";
			document.getElementById('ui_will_bar_info').innerHTML = "<?php echo "{$newWillPerc}% (" . number_format($newWill) . " / " . number_format($ir['maxwill']) . ")"; ?>";
		</script>
		<?php
	}
}