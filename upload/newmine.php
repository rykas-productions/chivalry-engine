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
	case 'potion':
		potion();
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
    [<a href='?action=buypower'>Buy Power Sets</a>]<br />
	[<a href='?action=herb'>Use Mining Herb</a>]<br />
	[<a href='?action=potion'>Drink Mining Potion</a>]<br />";

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
	if (!isset($_GET['spot']) || empty($_GET['spot'])) 
	{
        alert('danger', "Uh Oh!", "Please select the mine you wish to mine at.", true, 'mine.php');
        die($h->endpage());
    }
	$spot = abs($_GET['spot']);
	$mineinfo = $db->query("/*qc=on*/SELECT * FROM `mining_data` WHERE `mine_id` = {$spot}");
	if (!($db->num_rows($mineinfo))) 
	{
		alert('danger', "Uh Oh!", "The mine you are trying to mine at does not exist.", true, 'mine.php');
		die($h->endpage());
	}
	$MSI = $db->fetch_row($mineinfo);
	$specialnumber=((getSkillLevel($userid,15)*10)/100);
	$MSI['mine_iq']=$MSI['mine_iq']-($MSI['mine_iq']*$specialnumber);
	if ($MUS['mining_level'] < $MSI['mine_level']) 
	{
		alert('danger', "Uh Oh!", "You are too low level to mine here. You need mining level {$MSI['mine_level']} to mine here.", true, 'mine.php');
		die($h->endpage());
	} 
	elseif ($ir['location'] != $MSI['mine_location']) 
	{
		alert('danger', "Uh Oh!", "To mine at a mine, you need to be in the same town its located.", true, 'mine.php');
		die($h->endpage());
	} 
	elseif ($ir['iq'] < $MSI['mine_iq']) 
	{
		alert('danger', "Uh Oh!", "Your IQ is too low to mine here. You need {$MSI['mine_iq']} IQ.", true, 'mine.php');
		die($h->endpage());
	} 
	elseif ($MUS['miningpower'] < $MSI['mine_power_use']) 
	{
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
    if (isset($_POST['energy'])
	{
		
	}
	else
	{
		
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
		$db->query("UPDATE `mining` SET `mining_power` = `max_miningpower` WHERE `userid` = {$userid}");
		$api->UserTakeItem($userid, 227, 1);
	}
	else
	{
		alert('danger',"Uh Oh!","You do not have the required item to be here.",true,'inventory.php');
		die($h->endpage());
	}
}

$h->endpage();