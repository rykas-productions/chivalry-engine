<?php
/*
	File:		mine.php
	Created: 	4/5/2016 at 12:18AM Eastern Time
	Info: 		Allows players to mine for items, and progress
				linearly.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
$macropage = ('mine.php');
require('globals.php');
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
    default:
        home();
        break;
}
function home()
{
    global $MUS, $db, $api;
    $mineen = min(round($MUS['miningpower'] / $MUS['max_miningpower'] * 100), 100);
    $minexp = min(round($MUS['miningxp'] / $MUS['xp_needed'] * 100), 100);
	if ($MUS['mine_boost'] > time())
	{
		$xpboostendtime=TimeUntil_Parse($MUS['mine_boost']);
		alert('info',"Experience Boost!","You have increased experience gains while mining for the next {$xpboostendtime}!",false);
	}
    echo "Welcome to the dangerous mines, brainless moron! If you're lucky, you'll strike riches. If not... the mine
        will eat you alive.
    <br />
	<table class='table table-bordered'>
		<tr>
			<th colspan='2'>
				You are mining level {$MUS['mining_level']}.
			</th>
		</tr>
		<tr>
			<th width='33%'>
				Mining Power
			</th>
			<td>
				<div class='progress' style='height: 1rem;'>
					<div class='progress-bar bg-success progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$MUS['miningpower']}' aria-valuemin='0' aria-valuemax='100' style='width:{$mineen}%'></div>
						<span>{$mineen}% ({$MUS['miningpower']} / {$MUS['max_miningpower']})</span>
				</div>
			</td>
		</tr>
		<tr>
			<th>
				Mining Experience
			</th>
			<td>
				<div class='progress' style='height: 1rem;'>
					<div class='progress-bar bg-success progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$MUS['miningxp']}' aria-valuemin='0' aria-valuemax='100' style='width:{$minexp}%'></div>
						<span>{$minexp}% (" . round($MUS['miningxp'],2) . " / {$MUS['xp_needed']})</span>
				</div>
			</td>
		</tr>
	</table>
    <u>Open Mines</u><br />";
    $minesql = $db->query("/*qc=on*/SELECT * FROM `mining_data` ORDER BY `mine_level` ASC");
    while ($mines = $db->fetch_row($minesql)) {
        echo "[<a href='?action=mine&spot={$mines['mine_id']}' data-toggle='tooltip' data-placement='right' title='IQ Required: " . number_format($mines['mine_iq']) . "'>" . $api->SystemTownIDtoName($mines['mine_location']) . " - Level {$mines['mine_level']}</a>]<br />";
    }

    echo "<br />
    [<a href='?action=buypower'>Buy Power Sets</a>]<br />";

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
    global $db, $MUS, $ir, $userid, $api, $h, $CostForPower;
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
                alert('danger', "Uh Oh!", "Your IQ is too low to mine here. You need {$MSI['mine_iq']} IQ.", true, 'mine.php');
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
            if ($unequipped == 4)
            {
                alert('danger', "Uh Oh!", "You do not have the required pickaxe to mine here. You need a " . $api->SystemItemIDtoName($MSI['mine_pickaxe']), true, "mine.php");
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
            if ($Rolls <= 3) {
                $NegRolls = Random(1, 3);
                $NegTime = Random($CostForPower/2, $CostForPower*2);
                if ($NegRolls == 1) {
                    alert('danger', "Uh Oh!", "You begin to mine and touch off a natural gas leak. Kaboom.", false);
                    $api->SystemLogsAdd($userid, 'mining', "Mined at {$api->SystemTownIDtoName($MSI['mine_location'])} [{$MSI['mine_location']}] and was put into the infirmary for {$NegTime} minutes.");
                    $api->UserStatusSet($userid, 'infirmary', $NegTime, "Mining Explosion");
                } elseif ($NegRolls == 2) {
                    alert('danger', "Uh Oh!", "You hit a vein of gems, except a miner nearby gets jealous and tries to take your gems! You knock them out cold, and a guard arrests you. Wtf.", false);
                    $api->SystemLogsAdd($userid, 'mining', "Mined at {$api->SystemTownIDtoName($MSI['mine_location'])} [{$MSI['mine_location']}] and was put into the dungeon for {$NegTime} minutes.");
                    $api->UserStatusSet($userid, 'dungeon', $NegTime, "Mining Selfishness");
                } else {
                    alert('danger', "Uh Oh!", "You failed to mine anything of use.", false);
                    $api->SystemLogsAdd($userid, 'mining', "Mined at {$api->SystemTownIDtoName($MSI['mine_location'])} [{$MSI['mine_location']}] and was unsuccessful.");
                }
            } elseif ($Rolls >= 3 && $Rolls <= 14) {
                $PosRolls = Random(1, 3);
                if ($PosRolls == 1) {
					if (calculateLuck($userid))
						$flakes = Random($MSI['mine_copper_min']+($MSI['mine_copper_min']/4), $MSI['mine_copper_max']+($MSI['mine_copper_max']/4));
                    else
						$flakes = Random($MSI['mine_copper_min'], $MSI['mine_copper_max']);
					$flakes=round($flakes+($flakes*levelMultiplier($ir['level'])));
					$xpgain = ($flakes * 0.35) * $spot;
					if ($MUS['mine_boost'] > time())
						$xpgain = $xpgain*2;
                    alert('success', "Success!", "You have successfully mined up " . number_format($flakes) . " " . $api->SystemItemIDtoName($MSI['mine_copper_item']) . ". You have gained {$xpgain} experience points.", false);
                    $api->UserGiveItem($userid, $MSI['mine_copper_item'], $flakes);
                    $api->SystemLogsAdd($userid, 'mining', "Mined at {$api->SystemTownIDtoName($MSI['mine_location'])} [{$MSI['mine_location']}] and mined {$flakes}x {$api->SystemItemIDtoName($MSI['mine_copper_item'])}.");

                } elseif ($PosRolls == 2) {
					if (calculateLuck($userid))
						$flakes = Random($MSI['mine_silver_min']+($MSI['mine_silver_min']/4), $MSI['mine_silver_max']+($MSI['mine_silver_max']/4));
                    else
						$flakes = Random($MSI['mine_silver_min'], $MSI['mine_silver_max']);
					$flakes=round($flakes+($flakes*levelMultiplier($ir['level'])));
					$xpgain = ($flakes * 0.55) * $spot;
					if ($MUS['mine_boost'] > time())
						$xpgain = $xpgain*2;
                    alert('success', "Success!", "You have successfully mined up " . number_format($flakes) . " " . $api->SystemItemIDtoName($MSI['mine_silver_item']) . ". You have gained {$xpgain} experience points.", false);
                    $api->UserGiveItem($userid, $MSI['mine_silver_item'], $flakes);
                    $api->SystemLogsAdd($userid, 'mining', "Mined at {$api->SystemTownIDtoName($MSI['mine_location'])} [{$MSI['mine_location']}] and mined {$flakes}x {$api->SystemItemIDtoName($MSI['mine_silver_item'])}.");
                } else {
					if (calculateLuck($userid))
						$flakes = Random($MSI['mine_gold_min']+($MSI['mine_gold_min']/4), $MSI['mine_gold_max']+($MSI['mine_gold_max']/4));
                    else
						$flakes = Random($MSI['mine_gold_min'], $MSI['mine_gold_max']);
					$flakes=round($flakes+($flakes*levelMultiplier($ir['level'])));
					$xpgain = ($flakes * 0.75) * $spot;
					if ($MUS['mine_boost'] > time())
						$xpgain = $xpgain*2;
                    alert('success', "Success!", "You have successfully mined up " . number_format($flakes) . " " . $api->SystemItemIDtoName($MSI['mine_gold_item']) . ". You have gained {$xpgain} experience points.", false);
                    $api->UserGiveItem($userid, $MSI['mine_gold_item'], $flakes);
                    $api->SystemLogsAdd($userid, 'mining', "Mined at {$api->SystemTownIDtoName($MSI['mine_location'])} [{$MSI['mine_location']}] and mined {$flakes}x {$api->SystemItemIDtoName($MSI['mine_gold_item'])}.");
                }
            } else {
				$xpgain = 17 * $MUS['mining_level'];
				if ($MUS['mine_boost'] > time())
						$xpgain = $xpgain*2;
                alert('success', "Success!", "You have carefully excavated out a single " . $api->SystemItemIDtoName($MSI['mine_gem_item']) . ". You have gained {$xpgain} experience points.", false);
                $api->UserGiveItem($userid, $MSI['mine_gem_item'], 1);
                $api->SystemLogsAdd($userid, 'mining', "Mined at {$api->SystemTownIDtoName($MSI['mine_location'])} [{$MSI['mine_location']}] and mined 1x {$api->SystemItemIDtoName($MSI['mine_gem_item'])}.");
            }
            echo "<hr />
            [<a href='?action=mine&spot={$spot}'>Mine Again</a>]<br />
            [<a href='mine.php'>Pack it Up</a>]<br />
            <img src='https://res.cloudinary.com/dydidizue/image/upload/v1522516963/2-cave.jpg' class='img-thumbnail img-responsive'>";
            $db->query("UPDATE `mining` SET `miningxp`=`miningxp`+ {$xpgain}, `miningpower`=`miningpower`-'{$MSI['mine_power_use']}' WHERE `userid` = {$userid}");
        }
    }
}

function mining_levelup()
{
    global $db, $userid, $MUS;
    $MUS['xp_needed'] = round(($MUS['mining_level'] + 0.75) * ($MUS['mining_level'] + 0.75) * ($MUS['mining_level'] + 0.75) * 1);
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
	if ($MUS['mine_boost'] > time())
	{
		alert('danger',"Uh Oh!","Please let the affects of the herb wear off before consuming another. Results could be... dangerous...",true,'inventory.php');
		die($h->endpage());
	}
	if ($api->UserHasItem($userid, 177, 1))
	{
		alert('success',"Success!","You've consumed a set of herbs and feel strangely relaxed, but ready to learn more while you mine! The affects will wear off in an hour.",true,'inventory.php');
		$wornofftime=time()+3600;
		$db->query("UPDATE `mining` SET `mine_boost` = {$wornofftime} WHERE `userid` = {$userid}");
		$api->UserTakeItem($userid, 177, 1);
	}
	else
	{
		alert('danger',"Uh Oh!","You do not have the required item to be here.",true,'inventory.php');
		die($h->endpage());
	}
}

$h->endpage();