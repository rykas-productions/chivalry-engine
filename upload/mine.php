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

$macropage = ('mine.php');
require('globals.php');
//2021 Halloween event
$month = date('n');
$day = date('j');
if (isHoliday())
    $energyCost -= 0.5;
if ((!isCourseComplete($userid, 23)) && ($userid != 1))
{
	alert('danger', "Uh Oh!", "Please complete the Precious Metals academic course before you first attempt mining.", true, 'explore.php');
    die($h->endpage());
}
if (userHasEffect($userid, effect_mining_fear))
{
    alert('danger', "Uh Oh!", "You are too tripped out to go mining right now. Try again in " . TimeUntil_Parse(returnEffectDone($userid, effect_mining_fear)) . ".", true, 'explore.php');
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
$CostForPower = calculateMinePowerCost($userid);
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
	case 'autominer':
	    autominer();
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
    $amc = countUserAutoMiners($userid);
    if ($amc > 0)
    {
        alert('warning',"","You have {$amc} Powered Miners active at this time.",true,'?action=autominer', 'View here');
    }
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
			" . scaledColorProgressBar($MUS['miningpower'], 0, $MUS['max_miningpower']) . "
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
		$mines['mine_iq'] = calcMineIQ($userid, $mines['mine_id']);
		
		$mininglevel = ($MUS['mining_level'] >= $mines['mine_level']) ? "<span class='text-success'><b>Level:</b> " . shortNumberParse($mines['mine_level']) . "</span>" : "<span class='text-danger'><b>Level:</b> " . shortNumberParse($mines['mine_level']) . "</span>";
		$iq = ($ir['iq'] >= $mines['mine_iq']) ? "<span class='text-success'><b>IQ:</b> " . shortNumberParse($mines['mine_iq']) . "</span>" : "<span class='text-danger'><b>IQ:</b> " . shortNumberParse($mines['mine_iq']) . "</span>";
		$pickaxe = ($api->UserHasItem($ir['userid'],$mines['mine_pickaxe'],1)) ? "<span class='text-success'><b>Pickaxe:</b> " . $api->SystemItemIDtoName($mines['mine_pickaxe']) . "</span>" : "<span class='text-danger'><b>Pickaxe:</b> " . $api->SystemItemIDtoName($mines['mine_pickaxe']) . "</span>";
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
        if (reachedMonthlyDonationGoal())
            $totalcost = round($totalcost / 2);
        if (isHoliday())
            $totalcost/=2;
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
        echo"<div class='row'>
            <div class='col-12'>
            <div class='card'>
                <div class='card-header'>
                    Purchase Mining Power Sets <b>(Available: {$MUS['buyable_power']})</b>
                </div>
                <div class='card-body'>
                    You may increase your maximum mining power by purchasing Mining Power Sets. Each set is equal to 10 Mining Power.
                    More mining sets may be unlocked while mining or other game events. Each Mining Power Set will cost you 
                    " . number_format($CostForPower) . " Chivlary Tokens. How many Mining Power Sets do you wish to buy?
                    <form method='post'>
                        <input type='number' class='form-control' value='{$MUS['buyable_power']}' min='1' max='{$MUS['buyable_power']}' name='sets' required='1'>
                        <br />
                        <input type='submit' class='btn btn-primary btn-block' value='Buy Power'>
                    </form>
                </div>
            </div>
    </div></div>";
    }
}

function mine()
{
    global $db, $MUS, $ir, $userid, $api, $h, $CostForPower, $energyCost;
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
                $energyCost = $energyCost - (returnEffectMultiplier($userid, constant("holiday_mining_energy")) * 0.2);
			$MSI['mine_power_use'] = $MSI['mine_power_use'] * $energyCost;
			if (reachedMonthlyDonationGoal())
			    $MSI['mine_power_use'] /= 2;
			if (isHoliday())
			    $MSI['mine_power_use'] /= 2;
			$nextspot=$spot+1;
			$nextmineslevel = $db->fetch_single($db->query("SELECT `mine_level` FROM `mining_data` WHERE `mine_id` = {$nextspot}"));
			/*if ($MSI['mine_level'] >= $nextmineslevel)
			{
				alert('danger',"Uh Oh!","This mine is too easy for you. Leave it for the newbies.",true,'mine.php');
				die($h->endpage());
			}*/
			$MSI['mine_iq'] = calcMineIQ($userid, $spot);
			$laborCost = round($MSI['mine_iq'] * 0.06);
			if (isHoliday())
			    $laborCost*=0.5;
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
                alert('danger', "Uh Oh!", "Your IQ is too low to mine here. You need " . shortNumberParse($MSI['mine_iq']) . " IQ.", true, 'mine.php');
                die($h->endpage());
            }
            elseif ($ir['labor'] < $laborCost)
            {
                alert('danger', "Uh Oh!", "Your labor is too low to mine here. You need " . shortNumberParse($laborCost) . " Labor.", true, 'mine.php');
                die($h->endpage());
            }
            elseif ($MUS['miningpower'] < $MSI['mine_power_use']) 
            {
                alert('danger', "Uh Oh!", "You do not have enough mining power to mine here. You need " . shortNumberParse($MSI['mine_power_use']) . " mining power, but only have " . shortNumberParse($MUS['miningpower']) . ".", true, 'mine.php');
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
                alert('danger', "Uh Oh!", "You do not have the required pickaxe to mine here. You need a " . $api->SystemItemIDtoName($MSI['mine_pickaxe']) . " to mine here.", true, "mine.php");
                die($h->endpage());
            }
            $Rolls = getMineRolls($userid, $MSI['mine_iq']);
			$remainpower = $MUS['miningpower'] - $MSI['mine_power_use'];
			//All the negative events are in here.
            if ($Rolls <= 3) 
            {
                $NegRolls = Random(1, 38);
                $NegTime = Random($CostForPower/2, $CostForPower*2);
                if ($NegRolls <= 12) 
                {
                    alert('danger', "Uh Oh!", "You begin to mine and touch off a natural gas leak. Kaboom. <b>You have {$remainpower} mining power remaining.</b>", false);
                    $api->SystemLogsAdd($userid, 'mining', "[{$api->SystemTownIDtoName($MSI['mine_location'])}] {$NegTime} minutes at Infirmary.");
                    $api->UserStatusSet($userid, 'infirmary', $NegTime, "Mining Explosion");
                } 
                elseif (($NegRolls <= 24) && ($NegRolls > 12))
                {
                    alert('danger', "Uh Oh!", "You hit a vein of gems, except a miner nearby gets jealous and tries to take your gems! You knock them out cold, and a guard arrests you. Wtf. <b>You have {$remainpower} mining power remaining.</b>", false);
                    $api->SystemLogsAdd($userid, 'mining', "[{$api->SystemTownIDtoName($MSI['mine_location'])}] {$NegTime} minutes at Dungeon.");
                    $api->UserStatusSet($userid, 'dungeon', $NegTime, "Mining Selfishness");
                } 
                elseif (($NegRolls <= 36) && ($NegRolls > 24))
                {
                    alert('danger', "Uh Oh!", "You failed to mine anything of use. <b>You have {$remainpower} mining power remaining.</b>", false);
                    $api->SystemLogsAdd($userid, 'mining', "[{$api->SystemTownIDtoName($MSI['mine_location'])}] Unsuccessful.");
                }
                else
                {
                    alert('danger', "Uh Oh!", "While mining away, you have accidentally struck your secondary hand, injuring it in the process. 
                    Your secondary weapon has be unequipped and you will be unable to use a secondary weapon in combat for {$NegTime} minutes.
                    <b>You have {$remainpower} mining power remaining.</b>", false);
                    if ($ir['equip_secondary'] > 0)
                        unequipUserSlot($userid, slot_second_wep);
                    userGiveEffect($userid, effect_injure_sec_wep, $NegTime * 60);
                    $api->SystemLogsAdd($userid, 'mining', "[{$api->SystemTownIDtoName($MSI['mine_location'])}] Ruined secondary hand.");
                }
            }
            //Normal mine drop rolls.
            elseif ($Rolls >= 3 && $Rolls <= 14) 
            {
                $PosRolls = Random(1, 3);
                $dropList = json_decode(getMineDrop($spot, $PosRolls), true);
                $drops = randMineDropCalc($userid, $spot, $PosRolls);
                $xpgain = calcMineXPGains($userid, $spot, $PosRolls, $drops);
                alert('success', "Success!", "You have successfully mined up " . number_format($drops) . " {$api->SystemItemIDtoName($dropList['itemDrop'])}. You have gained " . number_format($xpgain, 2) . " experience points. <b>You have {$remainpower} mining power remaining.</b>", false);
                $api->UserGiveItem($userid, $dropList['itemDrop'], $drops);
                $api->SystemLogsAdd($userid, 'mining', "[{$api->SystemTownIDtoName($MSI['mine_location'])}] Mined " . number_format($drops) . " x {$api->SystemItemIDtoName($dropList['itemDrop'])}.");
                $db->query("UPDATE `mining` SET `miningxp`=`miningxp`+ {$xpgain} WHERE `userid` = {$userid}");
            } 
            else 
            {
                $dropList = json_decode(getMineDrop($spot, 4), true);
                $drops = randMineDropCalc($userid, $spot, 4);
                $xpgain = calcMineXPGains($userid, $spot, 4, $drops);
                alert('success', "Success!", "You have carefully excavated out {$drops} " . $api->SystemItemIDtoName($dropList['itemDrop']) . "(s). You have gained " . number_format($xpgain) . " experience points. <b>You have {$remainpower} mining power remaining.</b>", false);
                $api->UserGiveItem($userid, $dropList['itemDrop'], $drops);
                $api->SystemLogsAdd($userid, 'mining', "[{$api->SystemTownIDtoName($MSI['mine_location'])}] Mined {$api->SystemItemIDtoName($dropList['itemDrop'])}.");
                $db->query("UPDATE `mining` SET `miningxp`=`miningxp`+ {$xpgain} WHERE `userid` = {$userid}");
            }
			echo "
			<div class='row'>
					<div class='col-12 col-sm-6'>
						<a href='?action=mine&spot={$spot}' class='btn btn-primary btn-block'>Mine Again</a>
                        <br />
					</div>
					<div class='col-12 col-sm-6'>
						<a href='mine.php' class='btn btn-danger btn-block'>Pack it Up</a>
                        <br />
					</div>
				</div>";
            echo "
			<img src='https://res.cloudinary.com/dydidizue/image/upload/v1522516963/2-cave.jpg' class='img-thumbnail img-responsive'>";
            $db->query("UPDATE `mining` SET `miningpower`=`miningpower`-'{$MSI['mine_power_use']}' WHERE `userid` = {$userid}");
            changeUserLabor($userid, -1 * $laborCost);
        }
    }
}

function mine_item()
{
	global $db, $userid, $api, $h, $MUS;
	if ($api->UserHasItem($userid, 177, 1))
	{	
		if (userHasEffect($userid, mining_xp_boost))
		{
		    $effectLvl = returnEffectMultiplier($userid, mining_xp_boost);
		    if ($effectLvl > 3)
		    {
		        if (Random(1, 3) == 2)
		        {
		            $api->UserTakeItem($userid, 177, 1);
		            alert('danger', "Uh Oh!", "You consume these herbs and start to trip balls, man. You decide its best to not mine until you clear your head...", true, 'explore.php');
		            userGiveEffect($userid, effect_mining_fear, Random(600,3600));
		            userRemoveEffect($userid, mining_xp_boost);
		            die($h->endpage());
		        }
		    }
		    $newtime = 3600 / ($effectLvl + 1);
		    userUpdateEffect($userid, mining_xp_boost, $newtime, $effectLvl+1);
		}
		else 
		{
		    userGiveEffect($userid, constant("mining_xp_boost"), 3600);
		}
		alert('success',"Success!","You've consumed a set of herbs and feel strangely relaxed, but ready to learn more while you mine! The effects will wear off in an hour.",true,'inventory.php');
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

function autominer()
{
    global $db, $userid, $api, $h, $MUS;
    $q = $db->query("SELECT * FROM `mining_auto` WHERE `userid` = {$userid}");
    if ($db->num_rows($q) == 0)
    {
        alert('danger', "Uh Oh!", "You don't have any Powered Miners active at this time.", true, 'mine.php');
        die($h->endpage());
    }
    echo "<div class='card'>
            <div class='card-header'>
                Active Powered Miners
            </div>
            <div class='card-body'>";
    while ($r = $db->fetch_row($q))
    {
        $townID = $db->fetch_single($db->query("SELECT `mine_location` FROM `mining_data` WHERE `mine_id` = {$r['miner_location']}"));
        $townName = $db->fetch_single($db->query("SELECT `town_name` FROM `town` WHERE `town_id` = {$townID}"));
		$perc = min(round($r['miner_time'] / 300 * 100), 100);
        echo "<div class='row'>
            <div class='col-12 col-sm-6 col-md-4'>
                <div class='row'>
                    <div class='col-12'>
                        <small>Location</small>
                    </div>
                    <div class='col-12'>
                        {$townName}
                    </div>
                </div>  
            </div>
            <div class='col-12 col-sm-6 col-md-8'>
                <div class='row'>
                    <div class='col-12'>
                        <small>Durability</small>
                    </div>
                    <div class='col-12'>
						<div class='progress' style='height: 1rem;'>
							<div class='progress-bar bg-success progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$r['miner_time']}' style='width:{$perc}%' aria-valuemin='0' aria-valuemax='300'>
								<span>
									{$perc}% ({$r['miner_time']} / 300)
								</span>
							</div>
						</div>
                    </div>
                </div>
            </div>
        </div>
        <hr />";
    }
    echo "</div>
    </div>";
}

$h->endpage();