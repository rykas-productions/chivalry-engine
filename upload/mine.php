<?php
/*
	File:		mine.php
	Created: 	4/5/2016 at 12:18AM Eastern Time
	Info: 		Allows players to mine for items, and progress
				linearly.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
$energyCost=1.0;
$expMod=1.0;
$macropage = ('mine.php');
require('globals.php');
if ((!isCourseComplete($userid, 23)) && ($userid != 1))
{
	alert('danger', "Uh Oh!", "Please complete the Precious Metals academic course before you first attempt mining.", true, 'explore.php');
    die($h->endpage());
}
$MUS = ($db->fetch_row($db->query("/*qc=on*/SELECT * FROM `mining` WHERE `userid` = {$userid} LIMIT 1")));
mining_levelup();
echo "<h2><i class='game-icon game-icon-mining'></i> Dangerous Mines</h2><hr />";
if ($api->UserStatus($userid, 'infirmary')) {
    alert('danger', "Unconscious!", "You cannot go mining if you're in the infirmary.");
    die($h->endpage());
}
if ($api->UserStatus($userid, 'dungeon')) {
    alert('danger', "Locked Up!", "You cannot go mining if you're in the dungeon.");
    die($h->endpage());
}
if ($MUS['mining_level'] < 10)
	$CostForPower=10;
elseif (($MUS['mining_level'] >= 10) && ($MUS['mining_level'] < 20))
	$CostForPower=15;
elseif (($MUS['mining_level'] >= 20) && ($MUS['mining_level'] < 50))
	$CostForPower=25;
elseif (($MUS['mining_level'] >= 50) && ($MUS['mining_level'] < 75))
	$CostForPower=50;
elseif (($MUS['mining_level'] >= 75) && ($MUS['mining_level'] < 100))
	$CostForPower=75;
elseif (($MUS['mining_level'] >= 100) && ($MUS['mining_level'] < 150))
	$CostForPower=100;
elseif (($MUS['mining_level'] >= 150) && ($MUS['mining_level'] < 200))
	$CostForPower=175;
elseif (($MUS['mining_level'] >= 200) && ($MUS['mining_level'] < 300))
	$CostForPower=325;
else
	$CostForPower=500;
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'mine':
        mine();
        break;
    case 'buypower':
        buypower();
        break;
	case 'herb':
		mine_item();
		break;
	case 'potion':
		potion();
		break;
    default:
        home();
        break;
}
function home()
{
    global $MUS, $db, $api, $ir, $userid;
    $mineen = min(round($MUS['miningpower'] / $MUS['max_miningpower'] * 100), 100);
    $minexp = min(round($MUS['miningxp'] / $MUS['xp_needed'] * 100), 100);
    echo "<div class='row'>";
    if (userHasEffect($userid, constant("mining_xp_boost")))
	{
	    echo "<div class='col-12 col-xxxl'>";
	    $xpboostendtime=TimeUntil_Parse(returnEffectDone($userid, constant("mining_xp_boost")));
		alert('info',"","You have increased experience gains while mining for the next {$xpboostendtime}!",false);
		echo "</div>";
	}
	if (userHasEffect($userid, constant("holiday_mining_energy")))
	{
	    $perc = (returnEffectMultiplier($userid, constant("holiday_mining_energy")) * 20);
	    echo "<div class='col-12 col-xxxl'>";
	    $xpboostendtime=TimeUntil_Parse(returnEffectDone($userid, constant("holiday_mining_energy")));
	    alert('info',"","You require {$perc}% less mining energy per attempt. This effect wears off in {$xpboostendtime}.",false);
	    echo "</div>";
	}
    echo "</div>
    Welcome to the dangerous mines, brainless moron! If you're lucky, you'll strike riches. If not... the mine
        will eat you alive.
	<hr />
	<div class='row'>
        <div class='col-md-4 col-xl-3'>
			Mining Energy - {$mineen}%<br />
			<small><div class='row'>
				<div class='col'>
					[<a href='?action=buypower'>Buy Power Sets</a>]
				</div>
				<div class='col'>
					[<a href='?action=potion'>Drink Mining Potion</a>]
				</div>
			</div>
			</small>
		</div>
		<div class='col'>
			<div class='progress' style='height: 1rem;'>
				<div class='progress-bar bg-success progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$MUS['miningpower']}' style='width:{$mineen}%' aria-valuemin='0' aria-valuemax='{$MUS['max_miningpower']}'>
					<span>
						{$mineen}% (" . number_format($MUS['miningpower']) . " / " . number_format($MUS['max_miningpower']). ")
					</span>
				</div>
			</div>
		</div>
	</div>
	<hr />
	<div class='row'>
        <div class='col-md-4 col-xl-3'>
			Mining Experience - {$minexp}%<br />
			<small>
				<div class='row'>
					<div class='col'>
						Mining Level {$MUS['mining_level']}
					</div>
					<div class='col'>
						[<a href='?action=herb'>Use Mining Herb</a>]
					</div>
				</div>
			</small>
		</div>
		<div class='col-sm'>
			<div class='progress' style='height: 1rem;'>
				<div class='progress-bar bg-success progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$MUS['miningxp']}' style='width:{$minexp}%' aria-valuemin='0' aria-valuemax='{$MUS['xp_needed']}'>
					<span>
						{$minexp}% (" . number_format($MUS['miningxp']) . " / " . number_format($MUS['xp_needed']). ")
					</span>
				</div>
			</div>
		</div>
	</div>";
    $minesql = $db->query("/*qc=on*/SELECT * FROM `mining_data` ORDER BY `mine_level` ASC");
    while ($mines = $db->fetch_row($minesql)) 
	{
		$specialnumber=((getSkillLevel($ir['userid'],15)*10)/100);
		$mines['mine_iq']=$mines['mine_iq']-($mines['mine_iq']*$specialnumber);
		
		$mininglevel = ($MUS['mining_level'] >= $mines['mine_level']) ? "<span class='text-success'>Level: {$mines['mine_level']}</span>" : "<span class='text-danger'>Level: {$mines['mine_level']}</span>";
		$iq = ($ir['iq'] >= $mines['mine_iq']) ? "<span class='text-success'>IQ: " . number_format($mines['mine_iq']) . "</span>" : "<span class='text-danger'>IQ: " . number_format($mines['mine_iq']) . "</span>";
		$pickaxe = ($api->UserHasItem($ir['userid'],$mines['mine_pickaxe'],1)) ? "<span class='text-success'>Pickaxe: " . $api->SystemItemIDtoName($mines['mine_pickaxe']) . "</span>" : "<span class='text-danger'>Pickaxe: " . $api->SystemItemIDtoName($mines['mine_pickaxe']) . "</span>";
		$town = ($ir['location'] == $mines['mine_location']) ? "<span class='text-success'>" . $api->SystemTownIDtoName($mines['mine_location']) . "</span>" : "<span class='text-danger'>" . $api->SystemTownIDtoName($mines['mine_location']) . "</span>";
		
		echo "<div class='row'>
				<div class='col-12'>
					<div class='card'>
						<div class='card-body'>
							<div class='row'>
								<div class='col-lg-2'>
									<div class='row'>
										<div class='col-8 col-lg-12'>
											{$town}
										</div>
										<div class='col-4 col-lg-12'>
											<small><a href='travel.php?to={$mines['mine_location']}'>Travel</a></small>
										</div>
									</div>
								</div>
								
								<div class='col-lg-8'>
									<div class='row'>
										<div class='col-6 col-lg'>
											{$mininglevel}
										</div>
										<div class='col-6 col-lg'>
											{$iq}
										</div>
										<div class='col-12 col-lg'>
											{$pickaxe}
										</div>
									</div>									
								</div>
								<div class='col-lg-2'>
									<a href='?action=mine&spot={$mines['mine_id']}' class='btn btn-primary btn-block'>Mine</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>";
    }

}

function buypower()
{
    global $userid, $db, $ir, $MUS, $h, $api, $CostForPower;
    if (isset($_POST['sets']) && ($_POST['sets'] > 0)) {
        $sets = abs($_POST['sets']);
        $totalcost = $sets * $CostForPower;
        if ($sets > $MUS['buyable_power']) {
            alert('danger', "Uh Oh!", "You are trying to buy more sets of power than you currently have available to you.");
            die($h->endpage());
        } elseif (($ir['secondary_currency'] < $totalcost)) {
            alert('danger', "Uh Oh!", "You need " . number_format($totalcost) . " Chivalry Tokens to buy the amount of sets you want to. You only have " . number_format($ir['secondary_currency']));
            die($h->endpage());

        } else {
			addToEconomyLog('Mining', 'token', ($totalcost)*-1);
            $db->query("UPDATE `users` SET `secondary_currency` = `secondary_currency` - '{$totalcost}' WHERE `userid` = {$userid}");
            $db->query("UPDATE `mining` SET `buyable_power` = `buyable_power` - '$sets', 
						`max_miningpower` = `max_miningpower` + ($sets*10) 
						WHERE `userid` = {$userid}");
            $api->SystemLogsAdd($userid, 'mining', "Exchanged {$totalcost} Chivalry Tokens for {$sets} sets of mining power.");
            alert('success', "Success!", "You have traded " . number_format($totalcost) . " Chivalry Tokens for {$sets} of mining power.", true, 'mine.php');
        }
    } else {
        echo "You can buy {$MUS['buyable_power']} sets of mining power. One set is equal to 10 mining power. You unlock
            more sets by leveling your mining level. Each set will cost you " . number_format($CostForPower) . " Chivalry Tokens.
            How many do you wish to buy?";
        echo "<br />
        <form method='post'>
            <input type='number' class='form-control' value='{$MUS['buyable_power']}' min='1' max='{$MUS['buyable_power']}' name='sets' required='1'>
            <br />
            <input type='submit' class='btn btn-primary' value='Buy Power'>
        </form>";
    }
}

function mine()
{
    global $db, $MUS, $ir, $userid, $api, $h, $CostForPower, $energyCost, $expMod;
    if (!isset($_GET['spot']) || empty($_GET['spot'])) {
        alert('danger', "Uh Oh!", "Please select the mine you wish to mine at.", true, 'mine.php');
        die($h->endpage());
    } else {
        $spot = abs($_GET['spot']);
        $mineinfo = $db->query("/*qc=on*/SELECT * FROM `mining_data` WHERE `mine_id` = {$spot}");
        if (!($db->num_rows($mineinfo))) {
            alert('danger', "Uh Oh!", "The mine you are trying to mine at does not exist.", true, 'mine.php');
            die($h->endpage());
        } else {
            $MSI = $db->fetch_row($mineinfo);
            if (userHasEffect($userid, constant("holiday_mining_energy")))
                $energyCost = $energyCost - (returnEffectMultiplier($userid, constant("holiday_mining_energy") * 0.2));
			$MSI['mine_power_use']=$MSI['mine_power_use']*$energyCost;
			$nextspot=$spot+1;
			$nextmineslevel = $db->fetch_single($db->query("SELECT `mine_level` FROM `mining_data` WHERE `mine_id` = {$nextspot}"));
			/*if ($MSI['mine_level'] >= $nextmineslevel)
			{
				alert('danger',"Uh Oh!","This mine is too easy for you. Leave it for the newbies.",true,'mine.php');
				die($h->endpage());
			}*/
			$specialnumber=((getSkillLevel($userid,15)*10)/100);
			$MSI['mine_iq']=$MSI['mine_iq']-($MSI['mine_iq']*$specialnumber);
            if ($MUS['mining_level'] < $MSI['mine_level']) {
                alert('danger', "Uh Oh!", "You are too low level to mine here. You need mining level {$MSI['mine_level']} to mine here.", true, 'mine.php');
                die($h->endpage());
            } elseif ($ir['location'] != $MSI['mine_location']) {
                alert('danger', "Uh Oh!", "To mine at a mine, you need to be in the same town its located.", true, 'mine.php');
                die($h->endpage());
            } elseif ($ir['iq'] < $MSI['mine_iq']) {
                alert('danger', "Uh Oh!", "Your IQ is too low to mine here. You need " . number_format($MSI['mine_iq']) . " IQ.", true, 'mine.php');
                die($h->endpage());
            } elseif ($MUS['miningpower'] < $MSI['mine_power_use']) {
                alert('danger', "Uh Oh!", "You do not have enough mining power to mine here. You need {$MSI['mine_power_use']}.", true, 'mine.php');
                die($h->endpage());
            }
            $unequipped=0;
            if (!$api->UserHasItem($userid, $MSI['mine_pickaxe'], 1))
                $unequipped++;
            if (!$api->UserEquippedItem($userid, 'primary', $MSI['mine_pickaxe']))
                $unequipped++;
            if (!$api->UserEquippedItem($userid, 'secondary', $MSI['mine_pickaxe']))
                $unequipped++;
            if (!$api->UserEquippedItem($userid, 'armor', $MSI['mine_pickaxe']))
                $unequipped++;
			$itemmulti = 0;
			if (hasNecklaceEquipped($userid,332))
			{
				$itemmulti=20/100;
			}
            if ($unequipped == 4)
            {
                alert('danger', "Uh Oh!", "You do not have the required pickaxe to mine here. You need a " . $api->SystemItemIDtoName($MSI['mine_pickaxe']) . " to mine here.", true, "mine.php");
                die($h->endpage());
            }
            if (!isset($xpgain)) {
                $xpgain = 0;
            }
            if ($ir['iq'] <= $MSI['mine_iq'] + ($MSI['mine_iq'] * .3)) {
                $Rolls = Random(1, 5);
            } elseif ($ir['iq'] >= $MSI['mine_iq'] + ($MSI['mine_iq'] * .3) && ($ir['iq'] <= $MSI['mine_iq'] + ($MSI['mine_iq'] * .6))) {
                $Rolls = Random(2, 10);
            } else {
                $Rolls = Random(3, 15);
            }
			$remainpower = $MUS['miningpower'] - $MSI['mine_power_use'];
            if ($Rolls <= 3) {
                $NegRolls = Random(1, 3);
                $NegTime = Random($CostForPower/2, $CostForPower*2);
                if ($NegRolls == 1) {
                    alert('danger', "Uh Oh!", "You begin to mine and touch off a natural gas leak. Kaboom. <b>You have {$remainpower} mining power remaining.</b>", false);
                    $api->SystemLogsAdd($userid, 'mining', "[{$api->SystemTownIDtoName($MSI['mine_location'])}] {$NegTime} minutes at Infirmary.");
                    $api->UserStatusSet($userid, 'infirmary', $NegTime, "Mining Explosion");
                } elseif ($NegRolls == 2) {
                    alert('danger', "Uh Oh!", "You hit a vein of gems, except a miner nearby gets jealous and tries to take your gems! You knock them out cold, and a guard arrests you. Wtf. <b>You have {$remainpower} mining power remaining.</b>", false);
                    $api->SystemLogsAdd($userid, 'mining', "[{$api->SystemTownIDtoName($MSI['mine_location'])}] {$NegTime} minutes at Dungeon.");
                    $api->UserStatusSet($userid, 'dungeon', $NegTime, "Mining Selfishness");
                } else {
                    alert('danger', "Uh Oh!", "You failed to mine anything of use. <b>You have {$remainpower} mining power remaining.</b>", false);
                    $api->SystemLogsAdd($userid, 'mining', "[{$api->SystemTownIDtoName($MSI['mine_location'])}] Unsuccessful.");
                }
            } elseif ($Rolls >= 3 && $Rolls <= 14) {
                $PosRolls = Random(1, 3);
                if ($PosRolls == 1) {
					if (calculateLuck($userid))
						$flakes = Random($MSI['mine_copper_min']+($MSI['mine_copper_min']/4), $MSI['mine_copper_max']+($MSI['mine_copper_max']/4));
                    else
						$flakes = Random($MSI['mine_copper_min'], $MSI['mine_copper_max']);
					$flakes=round($flakes+($flakes*levelMultiplier($ir['level'])));
					$flakes = $flakes + ($flakes * $itemmulti);
					$xpgain = (($flakes * 0.35) * $spot)*$expMod;
					$flakes = round($flakes);
					if (userHasEffect($userid, constant("mining_xp_boost")))
					{
					    $xpgain = $xpgain * (returnEffectMultiplier($userid, constant("mining_xp_boost")) + 1);
					}
                    alert('success', "Success!", "You have successfully mined up " . number_format($flakes) . " " . $api->SystemItemIDtoName($MSI['mine_copper_item']) . ". You have gained " . number_format($xpgain) . " experience points. <b>You have {$remainpower} mining power remaining.</b>", false);
                    $api->UserGiveItem($userid, $MSI['mine_copper_item'], $flakes);
                    $api->SystemLogsAdd($userid, 'mining', "[{$api->SystemTownIDtoName($MSI['mine_location'])}] Mined {$flakes} x {$api->SystemItemIDtoName($MSI['mine_copper_item'])}.");

                } elseif ($PosRolls == 2) {
					if (calculateLuck($userid))
						$flakes = Random($MSI['mine_silver_min']+($MSI['mine_silver_min']/4), $MSI['mine_silver_max']+($MSI['mine_silver_max']/4));
                    else
						$flakes = Random($MSI['mine_silver_min'], $MSI['mine_silver_max']);
					$flakes=round($flakes+($flakes*levelMultiplier($ir['level'])));
					$flakes = $flakes + ($flakes * $itemmulti);
					$xpgain = (($flakes * 0.55) * $spot)*$expMod;
					$flakes = round($flakes);
					if (userHasEffect($userid, constant("mining_xp_boost")))
					{
						$xpgain = $xpgain * (returnEffectMultiplier($userid, constant("mining_xp_boost")) + 1);
					}
                    alert('success', "Success!", "You have successfully mined up " . number_format($flakes) . " " . $api->SystemItemIDtoName($MSI['mine_silver_item']) . ". You have gained " . number_format($xpgain, 2) . " experience points. <b>You have {$remainpower} mining power remaining.</b>", false);
                    $api->UserGiveItem($userid, $MSI['mine_silver_item'], $flakes);
                    $api->SystemLogsAdd($userid, 'mining', "[{$api->SystemTownIDtoName($MSI['mine_location'])}] Mined {$flakes} x {$api->SystemItemIDtoName($MSI['mine_silver_item'])}.");
                } else {
					if (calculateLuck($userid))
						$flakes = Random($MSI['mine_gold_min']+($MSI['mine_gold_min']/4), $MSI['mine_gold_max']+($MSI['mine_gold_max']/4));
                    else
						$flakes = Random($MSI['mine_gold_min'], $MSI['mine_gold_max']);
					$flakes=round($flakes+($flakes*levelMultiplier($ir['level'])));
					$flakes = $flakes + ($flakes * $itemmulti);
					$xpgain = (($flakes * 0.75) * $spot)*$expMod;
					$flakes = round($flakes);
					if (userHasEffect($userid, constant("mining_xp_boost")))
					{
					    $xpgain = $xpgain * (returnEffectMultiplier($userid, constant("mining_xp_boost")) + 1);
					}
                    alert('success', "Success!", "You have successfully mined up " . number_format($flakes) . " " . $api->SystemItemIDtoName($MSI['mine_gold_item']) . ". You have gained " . number_format($xpgain, 2) . " experience points. <b>You have {$remainpower} mining power remaining.</b>", false);
                    $api->UserGiveItem($userid, $MSI['mine_gold_item'], $flakes);
                    $api->SystemLogsAdd($userid, 'mining', "[{$api->SystemTownIDtoName($MSI['mine_location'])}] Mined {$flakes} x {$api->SystemItemIDtoName($MSI['mine_gold_item'])}.");
                }
            } else {
				$itemgive = 1;
				if (hasNecklaceEquipped($userid,332))
					$itemgive = 2;
				$formula=(14 * $MUS['mining_level'])*$expMod;
				$xpgain = round(Random($formula/2,$formula*2), 2);
				if (userHasEffect($userid, constant("mining_xp_boost")))
				{
				    $xpgain = $xpgain * (returnEffectMultiplier($userid, constant("mining_xp_boost")) + 1);
				}
				if ($MUS['mine_boost'] > time())
						$xpgain = $xpgain*2;
                alert('success', "Success!", "You have carefully excavated out {$itemgive} " . $api->SystemItemIDtoName($MSI['mine_gem_item']) . ". You have gained " . number_format($xpgain, 2) . " experience points. <b>You have {$remainpower} mining power remaining.</b>", false);
                $api->UserGiveItem($userid, $MSI['mine_gem_item'], $itemgive);
                $api->SystemLogsAdd($userid, 'mining', "[{$api->SystemTownIDtoName($MSI['mine_location'])}] Mined {$api->SystemItemIDtoName($MSI['mine_gem_item'])}.");
            }
			echo "
			<div class='row'>
					<div class='col'>
						<a href='?action=mine&spot={$spot}' class='btn btn-primary'>Mine Again</a>
					</div>
					<div class='col'>
						<a href='mine.php' class='btn btn-danger'>Pack it Up</a>
					</div>
				</div>";
            echo "
			<img src='https://res.cloudinary.com/dydidizue/image/upload/v1522516963/2-cave.jpg' class='img-thumbnail img-responsive'>";
            $db->query("UPDATE `mining` SET `miningxp`=`miningxp`+ {$xpgain}, `miningpower`=`miningpower`-'{$MSI['mine_power_use']}' WHERE `userid` = {$userid}");
        }
    }
}

function mining_levelup()
{
    global $db, $userid, $MUS, $ir;
    $MUS['xp_needed'] = (round(($MUS['mining_level'] + 0.67) * ($MUS['mining_level'] + 0.75) * ($MUS['mining_level'] + 0.75) * 1)/$ir['reset']);
    if ($MUS['miningxp'] >= $MUS['xp_needed']) {
        $expu = $MUS['miningxp'] - $MUS['xp_needed'];
        $MUS['mining_level'] += 1;
        $MUS['miningxp'] = $expu;
        $MUS['buyable_power'] += 1;
        $MUS['xp_needed'] =
            round(($MUS['mining_level'] + 0.75) * ($MUS['mining_level'] + 0.75) * ($MUS['mining_level'] + 0.75) * 1);
        $db->query("UPDATE `mining` SET `mining_level` = `mining_level` + 1, `miningxp` = {$expu},
                 `buyable_power` = `buyable_power` + 1 WHERE `userid` = {$userid}");
    }
}

function mine_item()
{
	global $db, $userid, $api, $h, $MUS;
	if (userHasEffect($userid, constant("mining_xp_boost")))
	{
		alert('danger',"Uh Oh!","Please let the affects of the herb wear off before consuming another. Results could be... dangerous...",true,'inventory.php');
		die($h->endpage());
	}
	if ($api->UserHasItem($userid, 177, 1))
	{
		alert('success',"Success!","You've consumed a set of herbs and feel strangely relaxed, but ready to learn more while you mine! The effects will wear off in an hour.",true,'inventory.php');
		userGiveEffect($userid, constant("mining_xp_boost"), 3600);
		$api->UserTakeItem($userid, 177, 1);
	}
	else
	{
		alert('danger',"Uh Oh!","You do not have the required item to be here.",true,'inventory.php');
		die($h->endpage());
	}
}

function potion()
{
	global $db, $userid, $api, $h, $MUS;
	if ($MUS['miningpower'] >= $MUS['max_miningpower'])
	{
		alert('danger',"Uh Oh!","There's no point in drinking a mining potion if you have full energy.",true,'inventory.php');
		die($h->endpage());
	}
	if ($api->UserHasItem($userid, 227, 1))
	{
		alert('success',"Success!","You've drank a Mining Potion and had your mining energy refilled to 100%.",true,'inventory.php');
		$wornofftime=time()+3600;
		$db->query("UPDATE `mining` SET `miningpower` = `max_miningpower` WHERE `userid` = {$userid}");
		$api->UserTakeItem($userid, 227, 1);
	}
	else
	{
		alert('danger',"Uh Oh!","You do not have the required item to be here.",true,'inventory.php');
		die($h->endpage());
	}
}

$h->endpage();