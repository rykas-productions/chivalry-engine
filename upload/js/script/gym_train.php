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
        if ($stat == 'all') {
            $gainstr = $api->UserTrain($userid, 'strength', $_POST['amnt'] / 4, $multi);
            $gainagl = $api->UserTrain($userid, 'agility', $_POST['amnt'] / 4, $multi);
            $gaingrd = $api->UserTrain($userid, 'guard', $_POST['amnt'] / 4, $multi);
            $gainlab = $api->UserTrain($userid, 'labor', $_POST['amnt'] / 4, $multi);
        } else {
            $gain = $api->UserTrain($userid, $_POST['stat'], $_POST['amnt'], $multi);
        }
        //Update energy left and stat's new count.
        if ($stat != 'all')
            $NewStatAmount = $ir[$stat] + $gain;
        $EnergyLeft = $ir['energy'] - $_POST['amnt'];
        //Strength is chosen stat
        if ($stat == "strength") {
            alert('success', "Success!", "You begin to lift weights. You have gained " . number_format($gain) . " Strength by completing
			    {$_POST['amnt']} sets of weights. You now have " . number_format($NewStatAmount) . " Strength and {$EnergyLeft} Energy left.", false);
            //Have strength selected for the next training.
            $str_select = "selected";
			setcookie('lastTrainedStat', 'strength', time() + 86400);
        } //Agility is the chosen stat.
        elseif ($stat == "agility") {
            alert('success', "Success!", "You begin to run laps. You have gained " . number_format($gain) . " Agility by completing
			    {$_POST['amnt']} laps. You now have " . number_format($NewStatAmount) . " Agility and {$EnergyLeft} Energy left.", false);
            //Have agility selected for the next training.
            $agl_select = "selected";
			setcookie('lastTrainedStat', 'agility', time() + 86400);
        } //Guard is the chosen stat.
        elseif ($stat == "guard") {
            alert('success', "Success!", "You begin swimming in the pool. You have gained " . number_format($gain) . " Guard by swimming for
			    {$_POST['amnt']} minutes. You now have " . number_format($NewStatAmount) . " Guard and {$EnergyLeft} left.", false);
            //Have guard selected for the next training.
            $grd_select = "selected";
			setcookie('lastTrainedStat', 'guard', time() + 86400);
        } //Labor is the chosen stat.
        elseif ($stat == "labor") {
            alert('success', "Success!", "You begin moving boxes around the gym. You have gained " . number_format($gain) . " Labor by moving
                {$_POST['amnt']} sets of boxes. You now have " . number_format($NewStatAmount) . " and {$EnergyLeft} Energy left.", false);
            //Have guard selected for the next training.
            $lab_select = "selected";
			setcookie('lastTrainedStat', 'labor', time() + 86400);
        } elseif ($stat == "all") {
            alert('success', "Success!", "You begin training your Strength, Agility, Guard and Labor all at once. You
                have gained {$gainstr} Strength, {$gainagl} Agility, {$gaingrd} Guard and {$gainlab} Labor. You have
                {$EnergyLeft} Energy left.");
            $all_select = "selected";
			setcookie('lastTrainedStat', 'all', time() + 86400);
        }
        //Log the user's training attempt.
        $api->SystemLogsAdd($userid, 'training', "[Normal Gym] {$_POST['amnt']} energy for " . number_format($gain) . " {$stat}.");
        echo "<hr />";
        $ir['energy'] -= $_POST['amnt'];
        if ($stat != 'all')
            $ir[$stat] += $gain;
		?>
		<script>
			document.getElementById('gymEnergy').innerHTML = <?php echo $api->UserInfoGet($userid, 'energy', true); ?>;
			document.getElementById('gymWill').innerHTML = <?php echo $api->UserInfoGet($userid, 'will', true); ?>;
			document.getElementById('trainTimes').value = <?php echo $api->UserInfoGet($userid, 'energy') ?>;
			document.getElementById('trainTimes').max = <?php echo $api->UserInfoGet($userid, 'energy') ?>;
			document.getElementById('trainTimesTotal').innerHTML = <?php echo number_format($api->UserInfoGet($userid, 'energy')) ?>;
		</script>
		<?php
	}
}