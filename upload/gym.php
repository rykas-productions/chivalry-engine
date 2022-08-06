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
$multi=1.0;
$macropage = ('gym.php');
require("globals.php");
$energy = $api->UserInfoGet($userid, 'energy', true);
$will = $api->UserInfoGet($userid, 'will', true);
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
echo "<h3><i class='game-icon game-icon-weight-lifting-down'></i> The Gym</h3>";
if ((isset($_GET['daybonus'])) && (!userHasEffect($userid, effect_daily_gym_bonus)))
{
    $bonus = Random(1,25);
    userGiveEffect($userid, effect_daily_gym_bonus, getNextDayReset(), $bonus);
    alert('success',"","You have received a {$bonus}% training bonus for the day.", false);
}
if (!userHasEffect($userid, effect_daily_gym_bonus))
    alert('warning',"","It appears you have not redeemed your Daily Gym Bonus for the day.",true, "?daybonus", "Redeem Bonus");
else 
    alert('primary',"","You are currently receiving a " . returnEffectMultiplier($userid, effect_daily_gym_bonus) . "% training boost until the end of the day.", false);
echo "<div id='gymsuccess'></div>";
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
if (isset($_COOKIE['lastTrainedStat']))
{
    if ($_COOKIE['lastTrainedStat'] == "strength")
		$str_select = 'selected';
	elseif ($_COOKIE['lastTrainedStat'] == "agility")
		$agl_select = 'selected';
	elseif ($_COOKIE['lastTrainedStat'] == "guard")
		$grd_select = 'selected';
	elseif ($_COOKIE['lastTrainedStat'] == "labor")
		$lab_select = 'selected';
	elseif ($_COOKIE['lastTrainedStat'] == "all")
		$all_select = 'selected';
}
//Grab the user's stat ranks.
$ir['strank'] = get_rank($ir['strength'], 'strength');
$ir['agirank'] = get_rank($ir['agility'], 'agility');
$ir['guarank'] = get_rank($ir['guard'], 'guard');
$ir['labrank'] = get_rank($ir['labor'], 'labor');
$ir['all_four'] = ($ir['labor'] + $ir['strength'] + $ir['agility'] + $ir['guard']);
$ir['af_rank'] = get_rank($ir['all_four'], 'all');
echo "Choose the stat you wish to train, and enter how many times you wish to train it. You can train up to <span id='trainTimesTotal'>" . number_format($ir['energy']) . "</span> times.<br />
The Normal Gym will give you " . number_format($multi*100) . "% the stats you'd gain at the Normal Gym.
<form method='post' id='gymTrainNorm'>
	<div class='card'>
		<div class='card-body'>
			<div class='row'>
				<div class='col-12 col-sm-3 col-lg-2'>
					<b>Stat</b>
				</div>
				<div class='col-12 col-sm-9 col-lg-10'>
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
				</div>
			</div>
			<div class='row'>
				<div class='col-12 col-sm-3 col-lg-2'>
					<b>Energy</b>
				</div>
				<div class='col-12 col-sm-9 col-lg-10'>
					<input type='number' class='form-control' min='1' max='{$ir['energy']}' name='amnt' value='{$ir['energy']}' id='trainTimes' /><br />
				</div>
			</div>
			<div class='row'>
				<div class='col-12 col-sm-6 col-md-2 col-lg-6 col-xl-2'>
					<input type='submit' class='btn btn-success btn-block' value='Train' id='trainNorm' /><br />
				</div>
				<div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4'>
					<a href='#' class='btn btn-primary btn-block' id='gymRefillEnergy'>Refill Energy (<span id='gymEnergy'>{$energy}</span>%)</a><br />
				</div>
				<div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4'>
					<a href='#' class='btn btn-primary btn-block'id='gymRefillWill'>Regen Will (<span id='gymWill'>{$will}</span>%)</a><br />
				</div>
				<div class='col-12 col-sm-6 col-md-2 col-lg-6 col-xl-2'>
					<a href='#' class='btn btn-secondary btn-block'id='gymFillWill'>Fill Will</a><br />
				</div>
			</div>
		</div>
	</div>
</form>";
if ($ir['vip_days'] > 0)
    if (getCurrentUserPref('enableMusic', 'true'))
        $sound->playBGM('traintrance');
$h->endpage();