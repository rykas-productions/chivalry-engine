<?php
/*
	File:		attack.php
	Created: 	4/4/2016 at 11:51PM Eastern Time
	Info: 		File that contains all PVP Logic.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
$atkpage = 1;
require("globals.php");
$votecount=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`voted`) FROM `votes` WHERE `userid` = {$userid}"));
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'attacking':
        attacking();
        break;
    case 'xp':
        xp();
        break;
    case 'mug':
        mug();
        break;
    case 'lost':
        lost();
        break;
    case 'beat':
        beat();
        break;
    case 'homeinvade':
        home_invasion();
        break;
    default:
        landingpage();
        break;
}

function landingpage()
{
    global $db, $userid, $ir, $h, $api, $set, $atkpage, $votecount;
    $menuhide = 1;      //Hide the menu so players cannot load other pages,
                        //and lessens the chance of a misclick and losing XP.
    $tresder = Random(100, 999);    //RNG to prevent refreshing while attacking, thus
                                    //breaking progression of the attack system.
    $_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : '';
    $ref = (isset($_GET['ref']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])*$/i", $_GET['ref'])) ? $db->escape(strip_tags(stripslashes($_GET['ref']))) : 'index';

    if (!$_GET['user']) //If user is not specified.
    {
        alert("danger", "Uh Oh!", "You've chosen to attack a non-existent user. Check your source and try again.", true, "{$ref}.php");
        die($h->endpage());
    } 
    else if ($_GET['user'] == $userid)  //If the user is trying to attack himself.
    {
        alert("danger", "Uh Oh!", "Depressed or not, you cannot attack yourself.", true, "{$ref}.php");
        die($h->endpage());
    }
    $q = $db->query("/*qc=on*/SELECT `u`.`userid`, `hp`, `equip_armor`, `username`,
	       `equip_primary`, `equip_secondary`, `equip_potion`, `guild`, `location`, `maxhp`, `class`,
	       `guard`, `agility`, `strength`, `gender`, `level`, `laston`, `display_pic`
			FROM `users` AS `u`
			INNER JOIN `userstats` AS `us` ON `u`.`userid` = `us`.`userid`
			LEFT JOIN `user_settings` AS `uas` ON `u`.`userid` = `uas`.`userid`
			WHERE `u`.`userid` = {$_GET['user']}
			LIMIT 1");
    //Test for if the specified user is a valid and registered user.
    if ($db->num_rows($q) == 0) {
        alert("danger", "Uh Oh!", "The user you are trying to attack does not exist.", true, "{$ref}.php");
        die($h->endpage());
    }
    
    $odata = $db->fetch_row($q);
    $displaypic = "<img src='" . parseDisplayPic($ir['userid']) . "' width='80' class='img-thumbnail' alt='{$ir['username']}&#39;s display picture' title='{$ir['username']}&#39;s display picture'>";
    $odisplaypic = "<img src='" . parseDisplayPic($odata['userid']) . "' width='80' class='img-thumbnail' alt='{$odata['username']}&#39;s display picture' title='{$odata['username']}&#39;s display picture'>";
    $travelCost = round(15 * levelMultiplier($ir['level'], $ir['reset']));
    $npcquery = $db->query("/*qc=on*/SELECT * FROM `botlist` WHERE `botuser` = {$odata['userid']}");
    if ($travelCost > 50)
        $travelCost=50;
    $energy = $api->UserInfoGet($userid, 'energy', true);
    $hp = $api->UserInfoGet($userid, 'hp', true);
    $ohp = $api->UserInfoGet($odata['userid'], 'hp', true);
    $db->free_result($q);
    $attackable = "Yes";
    $attBool = true;
    $textClass = 'text-success';
    $disableButton = "";
    $energyButton = "disabled";
    $travelButton = "disabled";
    $travelClass = 'text-success';
    $levelClass = ($ir['level'] >= $odata['level']) ? "text-success" : "text-danger";
    if ($_GET['user'] == 20)
    {
        if ($votecount != 3)
        {
            $attackable = "Vote 3 times.";
            $attBool = false;
        }
        elseif ($ir['att_dg'] == 1)
        {
            $attackable = "Attackable once a day";
            $attBool = false;
        }
    }
    elseif (!($ir['energy'] >= $ir['maxenergy'] / $set['AttackEnergyCost']))
    {
        $attackable = "Not enough energy";
        $attBool = false;
        $energyButton = "";
    }
    elseif ($_GET['user'] == 21)
    {
        if (date('n') != 11)
        {
            $attackable = "Can't hunt outside of Novemeber";
            $attBool = false;
        }
    }
    elseif ($odata['hp'] == 1) 
    {
        $attackable = "Unconscious";
        $attBool = false;
    }
    elseif ($ir['hp'] == 1)
    {
        $attackable = "You're unconscious";
        $attBool = false;
    }
    elseif ($api->UserStatus($_GET['user'], 'infirmary'))
    {
        $attackable = "In infirmary";
        $attBool = false;
    }
    elseif ($api->UserStatus($_GET['user'], 'dungeon'))
    {
        $attackable = "In dungeon"; 
        $attBool = false;
    }
    elseif ($api->UserStatus($ir['userid'], 'infirmary'))
    {
        $attackable = "You're in infirmary";
        $attBool = false;
    }
    elseif ($api->UserStatus($ir['userid'], 'dungeon'))
    {
        $attackable = "You're in dungeon";
        $attBool = false;
    }
    elseif (!permission('CanBeAttack', $_GET['user']))
    {
        $attackable = "Invunlerable";
        $attBool = false;
    }
    elseif ($odata['level'] < 3 && $odata['laston'] > $ir['laston'])
    {
        $attackable = "Can't attack active newbies";
        $attBool = false;
    }
    elseif (userHasEffect($_GET['user'], basic_protection))
    {
        $attackable = "Has protection";
        $attBool = false;
    }
    elseif ($db->num_rows($npcquery) > 0)
    {
        $timequery = $db->query("/*qc=on*/SELECT `lasthit` FROM `botlist_hits` WHERE `userid` = {$userid} && `botid` = {$_GET['user']}");
        $time2query = $db->query("/*qc=on*/SELECT `botcooldown` FROM `botlist` WHERE `botuser` = {$_GET['user']}");
        $r2 = $db->fetch_single($timequery);
        $r3 = $db->fetch_single($time2query);
        //Opponent's drop has already been collected and the time hasn't reset.
        //if time <= (last hit + bot cooldown) AND `last hit` > 0
        if ((time() <= ($r2 + $r3)) && ($r2 > 0))
        {
            $attackable = "NPC cooldown";
            $attBool = false;
        }
    }
    elseif ($ir['location'] != $odata['location'])
    {
        $travelClass = 'text-danger';
        $travelButton = "";
    }
    if (!$attBool)
    {
        $textClass = 'text-danger';
        $disableButton = 'disabled';
    }
    echo "<form method='post' id='hiddenQuickTravelForm'>
            <input type='hidden' name='to' value='{$odata['location']}'>
        </form>";
    echo "<div id='gymsuccess'></div>
            <div id='quickTravelResult'></div>
        <div class='row'>
            <div class='col'>
                <div class='card text-center'>
                    <div class='card-body'>
                        <div class='row'>
                            <div class='col-12 col-xl-6'>
                                <div class='row'>
                                    <div class='col-12 col-sm-6 col-xl-3'>
                                        {$displaypic}
                                    </div>
                                    <div class='col-12 col-sm-6 col-xl'>
                                        {$ir['username']} [{$userid}]
                                    </div>
                                </div>
                                <div class='row'>
                                    <div class='col-12 col-xl-6'>
                                        <div class='progress' style='height: 1rem;'>
                                            <div id='ui_energy_bar' class='progress-bar bg-success progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$ir['energy']}' style='width:{$energy}%' aria-valuemin='0' aria-valuemax='{$ir['maxenergy']}'>
                        						<span id='ui_energy_bar_info'>
                        							Energy {$energy}%
                        						</span>
                        					</div>
                                        </div>
                                    </div>
                                    <div class='col-12 col-xl-6'>
                                        <div class='progress' style='height: 1rem;'>
                                            <div id='ui_hp_bar' class='progress-bar bg-danger progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$ir['hp']}' style='width:{$hp}%' aria-valuemin='0' aria-valuemax='{$ir['maxhp']}'>
                                				<span id='ui_hp_bar_info'>
                                					Health {$hp}%
                                				</span>
                                			</div>
                                        </div>
                                    </div>
                                </div>
                                <div class='row'>";
                                    if ($ir['equip_primary'] > 0)
                                    {
                                        $primWeap = $api->SystemItemIDtoName($ir['equip_primary']);
                                        echo"
                                        <div class='col-6 col-xxl-4 col-xxxl'>
                                            <div class='row'>
                                                <div class='col-12'>
                                                    <small>{$primWeap}</small>
                                                </div>
                                                <div class='col-12'>
                                                    " . returnIcon($ir['equip_primary'], 2) . "
                                                </div>
                                            </div>
                                        </div>";
                                    }
                                    if ($ir['equip_secondary'] > 0)
                                    {
                                        $secWeap = $api->SystemItemIDtoName($ir['equip_secondary']);
                                        echo"
                                        <div class='col-6 col-xxl-4 col-xxxl'>
                                            <div class='row'>
                                                <div class='col-12'>
                                                    <small>{$secWeap}</small>
                                                </div>
                                                <div class='col-12'>
                                                    " . returnIcon($ir['equip_secondary'], 2) . "
                                                </div>
                                            </div>
                                        </div>";
                                    }
                                    if ($ir['equip_armor'] > 0)
                                    {
                                        $armor = $api->SystemItemIDtoName($ir['equip_armor']);
                                        echo"<div class='col-6 col-xxl-4 col-xxxl'>
                                            <div class='row'>
                                                <div class='col-12'>
                                                    <small>{$armor}</small>
                                                </div>
                                                <div class='col-12'>
                                                    " . returnIcon($ir['equip_armor'], 2) . "
                                                </div>
                                            </div>
                                        </div>";
                                    }
                                    if ($ir['equip_potion'] > 0)
                                    {
                                        $potion = $api->SystemItemIDtoName($ir['equip_potion']);
                                        echo"<div class='col-6 col-xxl-4 col-xxxl'>
                                            <div class='row'>
                                                <div class='col-12'>
                                                    <small>{$potion}</small>
                                                </div>
                                                <div class='col-12'>
                                                    " . returnIcon($ir['equip_potion'], 2) . "
                                                </div>
                                            </div>
                                        </div>";
                                    }
                                    echo "
                                </div>
                                <div class='row'>
                                    <div class='col-12 col-xxl-6'>
                                        <a href='temple.php?action=energy' class='btn btn-block btn-success {$energyButton}' id='gymRefillEnergy'>Refill Energy - {$set['energy_refill_cost']} Tokens</a>
                                    </div>
                                    <div class='col-12 col-xxl-6'>
                                        <a href='travel.php?to={$odata['location']}' class='btn btn-block btn-primary {$travelButton}' id='quickTravelBtn'>Travel - {$travelCost} Tokens</a>
                                    </div>
                                </div>
                                <br />
                            </div>
                            <div class='col-12 col-xl-6'>
                                <div class='row'>
                                    <div class='col-12 col-sm-6 col-xl-2'>
                                        {$odisplaypic}
                                    </div>
                                    <div class='col-12 col-sm-6 col-xl'>
                                        {$odata['username']} [{$odata['userid']}]
                                    </div>
                                </div>
                                <div class='row'>
                                    <div class='col-12'>
                                        <div class='progress' style='height: 1rem;'>
                                            <div id='ui_hp_bar' class='progress-bar bg-danger progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$odata['hp']}' style='width:{$ohp}%' aria-valuemin='0' aria-valuemax='{$odata['maxhp']}'>
                                				<span id='ui_hp_bar_info'>
                                					Health {$ohp}%
                                				</span>
                                			</div>
                                        </div>
                                    </div>
                                </div>
                                <div class='row'>
                                    <div class='col-6 col-sm-4 col-xxxl'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <small>Town</small>
                                            </div>
                                            <div class='col-12'>
                                                <span class='{$travelClass}'>{$api->SystemTownIDtoName($odata['location'])}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-6 col-sm-4 col-xxxl-1'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <small>Level</small>
                                            </div>
                                            <div class='col-12'>
                                                <span class='{$levelClass}'>" . shortNumberParse($odata['level']) . "</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-6 col-sm-4 col-xxxl-2'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <small>Guild</small>
                                            </div>
                                            <div class='col-12'>
                                                <a href='guilds.php?action=view&id={$odata['guild']}'>{$api->GuildFetchInfo($odata['guild'], "guild_name")}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-6 col-sm'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <small>Attackable?</small>
                                            </div>
                                            <div class='col-12'>
                                                <span class='{$textClass}' {$disableButton}>{$attackable}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class='row'>
                                    <div class='col'>
                                        <a href='attack.php?action=attacking&user={$odata['userid']}' class='btn btn-block btn-danger'>Attack {$odata['username']}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>";
}
function attacking()
{
    global $db, $userid, $ir, $h, $api, $set, $atkpage, $votecount;
	$_SESSION['attack_scroll']=0;
    $menuhide = 1;      //Hide the menu so players cannot load other pages,
                        //and lessens the chance of a misclick and losing XP.
    $atkpage = 1;
    $tresder = Random(100, 999);    //RNG to prevent refreshing while attacking, thus
                                    //breaking progression of the attack system.
    $_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : '';
	$_GET['scroll'] = (isset($_GET['scroll']) && is_numeric($_GET['scroll'])) ? abs($_GET['scroll']) : '';
	$ref = (isset($_GET['ref']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])*$/i", $_GET['ref'])) ? $db->escape(strip_tags(stripslashes($_GET['ref']))) : 'index';
    if (empty($_GET['nextstep']))
        $_GET['nextstep'] = -1;
    if ($_GET['nextstep'] > 0) 
    {
        $_GET['tresde'] = (isset($_GET['tresde']) && is_numeric($_GET['tresde'])) ? abs($_GET['tresde']) : 0;
        //If RNG is not set, set it to 0.
        if (!isset($_SESSION['tresde'])) 
        {
            $_SESSION['tresde'] = 0;
        }
        //If RNG is not the same number stored in session
        if (($_SESSION['tresde'] == $_GET['tresde']) || $_GET['tresde'] < 100) 
        {
            resetAttackStatus();
            alert("danger", "Uh Oh!", "Please do not refresh while attacking. Thank you!", true, "attack.php?user={$_GET['user']}&ref={$ref}");
            die($h->endpage());
        }
		if (userHasEffect($userid, basic_protection))
		{
		    userRemoveEffect($userid, basic_protection);
			alert('warning',"Protection Void!","You've begun attacking {$odata['username']} and your bodyguards have resigned.",false);
		}
        //Set RNG
        $_SESSION['tresde'] = $_GET['tresde'];
    }

    preFightChecks();

    $youdata = $ir;
    $laston = time() - 900;
    $q = $db->query("/*qc=on*/SELECT `u`.`userid`, `hp`, `equip_armor`, `username`,
	       `equip_primary`, `equip_secondary`, `equip_potion`, `guild`, `location`, `maxhp`, `class`, 
	       `guard`, `agility`, `strength`, `gender`, `level`, `laston`, `display_pic`
			FROM `users` AS `u`
			INNER JOIN `userstats` AS `us` ON `u`.`userid` = `us`.`userid`
			LEFT JOIN `user_settings` AS `uas` ON `u`.`userid` = `uas`.`userid`
			WHERE `u`.`userid` = {$_GET['user']}
			LIMIT 1");
    //Test for if the specified user is a valid and registered user.
    if ($db->num_rows($q) == 0) {
        alert("danger", "Uh Oh!", "The user you are trying to attack does not exist.", true, "{$ref}.php");
        die($h->endpage());
    }
    $odata = $db->fetch_row($q);
    $db->free_result($q);
    //Check current user's last attacked user, and see that its the specified user.
    if ($ir['attacking'] && $ir['attacking'] != $_GET['user']) {
        $_SESSION['attacklost'] = 0;
        alert("danger", "Uh Oh!", "An unknown error has occurred. Please try again, or contact the admin team.", true, "{$ref}.php");
        $api->UserInfoSetStatic($userid, "attacking", 0);
        die($h->endpage());
        }

    handleAttackScrollLogic();
    handlePerfectionStatBonuses();
	$npcquery = $db->query("/*qc=on*/SELECT * FROM `botlist` WHERE `botuser` = {$_GET['user']}");
	if ($_GET['user'] == 20 && $_SESSION['attacking'] == 0)
	{
		$db->query("UPDATE `users` 
					SET `hp` = {$ir['maxhp']}, `maxhp` = {$ir['maxhp']}
					WHERE `userid` = 20");
	}
    handleDopplegangerLogic();
    $bossq=$db->query("SELECT * FROM `activeBosses` WHERE `boss_user` = {$_GET['user']}");
	handleBossLogic();
	
    //Check that the opponent has 1 health point.
    if ($odata['hp'] == 1) {
        resetAttackStatus();
        alert("danger", "Uh Oh!", "{$odata['username']} doesn't have health to be attacked.", true, "{$ref}.php");
        die($h->endpage());
    } //Check if the opponent is currently in the infirmary.
    else if ($api->UserStatus($_GET['user'], 'infirmary') == true) {
        resetAttackStatus();
        alert("danger", "Uh Oh!", "{$odata['username']} is currently in the infirmary. Try again later.", true, "{$ref}.php");
        die($h->endpage());
    }
	//Check if opponnent has at least 1/2 health
	else if (($api->UserInfoGet($_GET['user'],'hp',true) < 50) && $ir['attacking'] == 0) 
	{
		if ($db->num_rows($bossq) == 0)
		{
		    resetAttackStatus();
			alert("danger", "Uh Oh!", "{$odata['username']} does not have at least half their health.", true, "{$ref}.php");
			die($h->endpage());
		}
	}
	//Check if the current user is in the infirmary.
    else if ($api->UserStatus($ir['userid'], 'infirmary') == true) {
        resetAttackStatus();
        alert("danger", "Uh Oh!", "You are currently in the infirmary. Try again after you heal out.", true, "{$ref}.php");
        die($h->endpage());
    } //Check if the opponent is in the dungeon.
    else if ($api->UserStatus($_GET['user'], 'dungeon') == true) {
        resetAttackStatus();
        alert("danger", "Uh Oh!", "{$odata['username']} is currently in the dungeon. Try again later.", true, "{$ref}.php");
        die($h->endpage());
    } //Check if the current user is in the dungeon.
    else if ($api->UserStatus($userid, 'dungeon') == true) {
        resetAttackStatus();
        alert("danger", "Uh Oh!", "You are currently in the dungeon. Try again after you've paid your debt to society.", true, "{$ref}.php");
        die($h->endpage());
    } //Check if opponent has permission to be attacked.
    else if (permission('CanBeAttack', $_GET['user']) == false) {
        resetAttackStatus();
        alert("danger", "Uh Oh!", "Your opponent cannot be attacked this way.", true, "{$ref}.php");
        die($h->endpage());
    } //Check if the current player has permission to attack.
    else if (permission('CanAttack', $userid) == false) {
        resetAttackStatus();
        alert("danger", "Uh Oh!", "A magical force keeps you from attacking your opponent. (Or anyone, for that matter)", true, "{$ref}.php");
        die($h->endpage());
    } //Check if the opponent is level 2 or lower, and has been on in the last 15 minutes.
    else if ($odata['level'] < 3 && $odata['laston'] > $laston) {
        resetAttackStatus();
        alert("danger", "Uh Oh!", "You cannot attack online players who are level two or below.", true, "{$ref}.php");
        die($h->endpage());
    }	//User has protection
    else if (userHasEffect($_GET['user'], basic_protection))
	{
	    resetAttackStatus();
        alert("danger", "Uh Oh!", "You cannot attack this player as they have protection.", true, "{$ref}.php");
        die($h->endpage());
	}
	else if ($db->num_rows($npcquery) > 0) 
	{
		$timequery = $db->query("/*qc=on*/SELECT `lasthit` FROM `botlist_hits` WHERE `userid` = {$userid} && `botid` = {$_GET['user']}");
		$time2query = $db->query("/*qc=on*/SELECT `botcooldown` FROM `botlist` WHERE `botuser` = {$_GET['user']}");
		$r2 = $db->fetch_single($timequery);
		$r3 = $db->fetch_single($time2query);
		//Opponent's drop has already been collected and the time hasn't reset.
		//if time <= (last hit + bot cooldown) AND `last hit` > 0
		if ((time() <= ($r2 + $r3)) && ($r2 > 0)) 
		{
		    resetAttackStatus();
			$cooldown = ($r2 + $r3) - time();
			alert('danger',"Uh Oh!","You cannot attack this NPC at this time. Try again in " . ParseTimestamp($cooldown) . ".",true,"{$ref}.php");
			die($h->endpage());
		}
	}
    $_GET['weapon'] = (isset($_GET['weapon']) && is_numeric($_GET['weapon'])) ? abs($_GET['weapon']) : '';
    //If weapon is specified via $_GET, attack!!
    if ($_GET['weapon']) 
    {
        if (!$_GET['nextstep']) 
            $_GET['nextstep'] = 1;
        //Check for if current step is greater than the maximum attacks per session.
        if ($_GET['nextstep'] >= $set['MaxAttacksPerSession']) 
        {
            $_SESSION['attacking'] = 0;
			$_SESSION['attack_scroll'] = 0;
            $ir['attacking'] = 0;
            $api->UserInfoSetStatic($userid, "attacking", 0);
            alert("warning", "Uh Oh!", "Get stronger dude. This fight ends in stalemate.", true, "{$ref}.php");
            die($h->endpage());
        }
        //Check if the attack is currently stored in session.
        if ($_SESSION['attacking'] == 0 && $ir['attacking'] == 0) 
        {
            //Check if the current user has enough energy for this attack.
            if ($youdata['energy'] >= $youdata['maxenergy'] / $set['AttackEnergyCost']) 
            {
                $youdata['energy'] -= floor($youdata['maxenergy'] / $set['AttackEnergyCost']);
                $cost = floor($youdata['maxenergy'] / $set['AttackEnergyCost']);
                $api->UserInfoSet($userid, 'energy', "-{$cost}");
                $_SESSION['attackdmg'] = 0;
            } //If not enough energy, stop the fight.
            else 
            {
                $EnergyPercent = floor(100 / $set['AttackEnergyCost']);
                $UserCurrentEnergy = $api->UserInfoGet($userid,'energy',true);
                alert("danger", "Uh Oh!", "Attacking someone requires you to have {$EnergyPercent}% Energy. You currently
				                        only have {$UserCurrentEnergy}%.", true, "{$ref}.php");
                die($h->endpage());
            }
        }
        setAttackStatus();
        $_GET['nextstep'] = (isset($_GET['nextstep']) && is_numeric($_GET['nextstep'])) ? abs($_GET['nextstep']) : '';
        //Check if the current user is attacking with a weapon that they have equipped.
        if ($_GET['weapon'] != $ir['equip_primary'] && $_GET['weapon'] != $ir['equip_secondary'] && $_GET['weapon'] != $ir['equip_potion']) 
        {
            alert("danger", "Security Issue!", "You cannot attack with a weapon you don't have equipped... You lost your
			                                experience for that.", true, "{$ref}.php");
            die($h->endpage());
        }
        $winfo_sql = "/*qc=on*/SELECT `itmname`, `weapon`, `ammo`, `itmtype`, `effect1`, `effect2`, `effect3`,  `effect1_on`, `effect2_on`, `effect3_on` FROM `items` WHERE `itmid` = {$_GET['weapon']} LIMIT 1";
        $qo = $db->query($winfo_sql);
        //If the weapon chosen is not a valid weapon.
        if ($db->num_rows($qo) == 0) 
        {
            alert("danger", "Uh Oh!", "The weapon you're trying to attack with isn't valid. This likely means the weapon
			                        you chosen doesn't have a weapon value. Contact the admin team.", true, "{$ref}.php");
            die($h->endpage());
        }
        $r1 = $db->fetch_row($qo);
        $db->free_result($qo);
		$spied=$db->query("/*qc=on*/SELECT `user` FROM `spy_advantage` WHERE `user` = {$userid} and `spied` = {$_GET['user']}");
		$r1['weapon'] = calcWeaponEffectiveness($_GET['weapon'], $userid);
		if (($db->num_rows($spied) > 0) && ($_GET['nextstep'] == 1))
		{
			$ir['strength']=$ir['strength']+($ir['strength']*0.25);
			$ir['agility']=$ir['agility']+($ir['agility']*0.25);
		}
		$mydamage = round(($r1['weapon'] * $youdata['strength'] / ($odata['guard'] / 1.5)) * (Random(10000, 12000) / 10000));
		$hitratio = max(10, min(60 * $ir['agility'] / $odata['agility'], 95));
		$ttu='';
        //Item used is a potion... so don't do damage.
        if (($r1['itmtype'] == 8) || ($r1['itmtype'] == 7))
        {
            $dodamage=false;
            $usepotion=true;
        }
        else
        {
            $dodamage=true;
            $usepotion=false;
        }
		if ($r1['ammo'] > 0)
		{
			if (!$api->UserHasItem($userid,$r1['ammo'],1))
			{
				if ($_GET['weapon'] == $ir['equip_primary'])
				{
					$ir['equip_primary'] = 0;
					$slot='equip_primary';
				}
				if ($_GET['weapon'] == $ir['equip_secondary'])
				{
					$ir['equip_secondary'] = 0;
					$slot='equip_secondary';
				}
				unequipUserSlot($userid, $slot);
				alert('danger',"Uh Oh!","You need at least one {$api->SystemItemIDtoName($r1['ammo'])} to use your {$api->SystemItemIDtoName($_GET['weapon'])}. It has been unequipped and moved to your inventory.",false);
				$dodamage=false;
			}
			else
			{
			    //Ammo dispensery skill
			    if (getUserSkill($userid, 7) > 0)
			    {
			        if (Random(1,100) > (getUserSkill($userid, 7) * getSkillBonus(7)))
			            $api->UserTakeItem($userid,$r1['ammo'],1);
			    }
			    else
			        $api->UserTakeItem($userid,$r1['ammo'],1);
				$ttu="You take aim with your {$api->SystemItemIDtoName($_GET['weapon'])} and fire.";
			}
		}
		$missed=0;
        //If the attack attempt was connected.
		if ($dodamage)
		{
			if (Random(1, 100) <= $hitratio) {
				//If the opponent has armor equipped.
				if ($odata['equip_armor'] > 0) {
					$armorinfo_sql = "/*qc=on*/SELECT `armor` FROM `items` WHERE `itmid` = {$odata['equip_armor']} LIMIT 1";
					$q3 = $db->query($armorinfo_sql);
					//Check that the armor is valid.
					if ($db->num_rows($q3) > 0) {
					    //Thickened Skin skill
						$mydamage -= $db->fetch_single($q3) - ($db->fetch_single($q3) * ((getUserSkill($_GET['user'], 6) * getSkillBonus(6)) / 100));
					}
					$db->free_result($q3);
				}
				$theirbeforehp = $odata['hp'];
				//Fix damage...
				if ($mydamage < -100000) {
					$mydamage = abs($mydamage);
				} else if ($mydamage < 1) {
					$mydamage = 1;
				}
				$crit = Random(1, 40);
				//If user makes a critical hit, multiply damage.
				if ($crit == 17) {
					$mydamage *= Random(20, 40) / 10;
				} //If unlucky crit... reduce damage.
				else if ($crit == 25 OR $crit == 8) {
					$mydamage /= (Random(20, 40) / 10);
				}
				if ($mydamage > $theirbeforehp) {
					$mydamage = $odata['hp'];
				}
				$mydamage = round($mydamage);
				$odata['hp'] -= $mydamage;
				if ($odata['hp'] == 1) {
					$odata['hp'] = 0;
					$mydamage += 1;
				}
				if ($r1['ammo'] > 0)
				{
    				//True Shot skill
    				if (getUserSkill($userid, 4) > 0)
    				    $mydamage = $mydamage + ($mydamage * (getUserSkill($userid, 4) * getSkillBonus(4)) / 100);
				}
			}
			else
			{
				$missed=1;
			}
		}
		else
		{
			$mydamage=0;
		}
        if (!$usepotion)
        {
            if ($missed == 1)
            {
                alert('warning', "", "<b>Attempt {$_GET['nextstep']})</b> {$ttu} You attempt to strike {$odata['username']} using your {$api->SystemItemIDtoName($_GET['weapon'])} but missed. Your
                    opponent has " . shortNumberParse($odata['hp']) . " HP Remaining.", false, '', true);
            }
            else
            {
                //Reduce health.
                $db->query("UPDATE `users` SET `hp` = `hp` - {$mydamage} WHERE `userid` = {$_GET['user']}");
                $db->query("DELETE FROM `spy_advantage` WHERE `user` = {$userid} AND `spied` = {$_GET['user']}");
                alert('success', "", "<b>Attempt {$_GET['nextstep']})</b> {$ttu} Using your {$r1['itmname']} you manage to strike
                {$odata['username']} dealing " . shortNumberParse($mydamage) . " damage. Your opponent has " . shortNumberParse($odata['hp']) . " HP remaining.", false, '', true);
                $_SESSION['attackdmg'] += $mydamage;
                user_log($userid,'dmgdone',$mydamage);
				//Check if the attacked user is, in fact, an active boss.
				$bossq=$db->query("SELECT * FROM `activeBosses` WHERE `boss_user` = {$_GET['user']}");
				if ($db->num_rows($bossq) > 0)
				{
					$bossr=$db->fetch_row($bossq);
					logBossDmg($userid,$bossr['boss_id'],$mydamage);
				}
				if (doPoisonLogic($userid, $_GET['user']))
				{
				    toast("","You've successfully poisoned your opponent!!");
				}
            }
        }
        else
        {
            if ($api->UserHasItem($userid,$_GET['weapon'],1))
            {
                consumeItem($userid, $_GET['weapon']);
                alert('success', "", "<b>Attempt {$_GET['nextstep']})</b> {$ttu} You consume your {$r1['itmname']}.", false, '', true);
                $api->UserTakeItem($userid,$_GET['weapon'],1);
            }
            else
            {
                alert('warning',"Warning!","You do not have enough of {$api->SystemItemIDtoName($_GET['weapon'])} to use.",false);
                unequipUserSlot($userid, "equip_potion");
            }
        }
        //Win fight because opponent's health is 0 or lower.
        if ($odata['hp'] <= 0) {
            $odata['hp'] = 0;
            $_SESSION['attackwon'] = $_GET['user'];
            $api->UserInfoSet($_GET['user'], 'hp', 0);
            echo "<br />";
            alert('info',"","You have struck down {$odata['username']}. What do you wish to do to them now?",false);
            if (!userHasEffect($_GET['user'], constant("sleep")))
            {
    			echo"
                <div class='row'>
                    <div class='col-12 col-sm-6 col-md-3 col-xxl-4'>
                        <form action='?action=mug&ID={$_GET['user']}&ref={$ref}' method='post'>
                            <input class='btn btn-primary btn-block' type='submit' value='Rob Them' /><br />
                        </form>
                    </div>
                    <div class='col-12 col-sm-6 col-lg-5 col-xxl-4'>
                        <form action='?action=beat&ID={$_GET['user']}&ref={$ref}' method='post'>
                            <input class='btn btn-danger btn-block' type='submit' value='Increase Infirmary Time' /><br />
                        </form>
                    </div>
                    <div class='col-12 col-md-3 col-lg-4'>
                        <form action='?action=xp&ID={$_GET['user']}&ref={$ref}' method='post'>
                            <input class='btn btn-success btn-block' type='submit' value='Gain Experience' /><br />
                        </form>
                    </div>
                </div>";
            }
            else
            {
                echo"
                <div class='row'>
                    <div class='col-12'>
                        <form action='?action=homeinvade&ID={$_GET['user']}&ref={$ref}' method='post'>
                            <input class='btn btn-danger btn-block' type='submit' value='Home Invasion' /><br />
                        </form>
                    </div>
                </div>";
            }
        } //The opponent is not down... he gets to attack.
        else {
            $eq = $db->query("/*qc=on*/SELECT `itmname`,`weapon`,`ammo`,`itmid`,`itmtype`, `effect1`, `effect2`, `effect3`,  `effect1_on`, `effect2_on`, `effect3_on` FROM  `items` WHERE `itmid` IN({$odata['equip_primary']}, {$odata['equip_secondary']}, {$odata['equip_potion']})");
            //If opponent does not have a valid weapon equipped, make them punch with fists.
            if ($db->num_rows($eq) == 0) {
                $wep = "Fists";
                $dam = round(round((($odata['strength'] / $ir['guard'] / 100)) + 1) * (Random(10000, 12000) / 10000));
                $theydodmg=true;
                $theyusepotion=false;
            } else {
                $cnt = 0;
                while ($r = $db->fetch_row($eq)) {
                    $enweps[] = $r;
                    $cnt++;
                }
                $db->free_result($eq);
                $weptouse = Random(0, $cnt - 1);    //Select opponent weapon to use.
                $wep = $enweps[$weptouse]['itmname'];
                $enweps[$weptouse]['weapon'] = calcWeaponEffectiveness($enweps[$weptouse]['itmid'],$_GET['user']);
                $dam = round(($enweps[$weptouse]['weapon'] * $odata['strength'] / ($youdata['guard'] / 1.5)) * (Random(10000, 12000) / 10000));
				//Item used is a potion... so don't do damage.
                if (($enweps[$weptouse]['itmtype'] == 8) || ($enweps[$weptouse]['itmtype'] == 7))
                {
                    $theydodmg=false;
                    $theyusepotion=true;
                }
                else
                {
                    $theydodmg=true;
                    $theyusepotion=false;
                }
                if ($enweps[$weptouse]['ammo'] > 0)
                {
                    if (!$api->UserHasItem($_GET['user'],$enweps[$weptouse]['ammo'],1))
                    {
                        $theydodmg=false;
                        $ouq=$db->query("/*qc=on*/SELECT `equip_primary`,`equip_secondary`,`equip_potion` FROM `users` WHERE `userid` = {$_GET['user']}");
                        $our=$db->fetch_row($ouq);
                        if ($enweps[$weptouse]['itmid'] == $our['equip_primary'])
                        {
                            $slot = 'equip_primary';
                        }
                        elseif ($enweps[$weptouse]['itmid'] == $our['equip_secondary'])
                        {
                            $slot = 'equip_secondary';
                        }
                        unequipUserSlot($userid, $slot);
						$api->GameAddNotification($_GET['user'],"You have ran out of ammo for one of your weapons during combat. Its been unequipped and return to your inventory.", 'game-icon game-icon-ammo-box', 'red');
                        
					}
					else
					{
						//Ammo dispensery skill
						if (getUserSkill($_GET['user'], 7) > 0)
						{
						    if (Random(1,100) > (getUserSkill($_GET['user'], 7) * getSkillBonus(7)))
						        $api->UserTakeItem($_GET['user'],$r1['ammo'],1);
						}
						else
						    $api->UserTakeItem($_GET['user'],$r1['ammo'],1);
					}
				}
            }
                $miss=0;
                if ($theydodmg)
                {
                    $hitratio = max(10, min(60 * $odata['agility'] / $ir['agility'], 95));
                    //If hit connects with user.
                    if (Random(1, 100) <= $hitratio) {
                        //If user has armor equipped.
                        if ($ir['equip_armor'] > 0) {
                            $q3 = $db->query("/*qc=on*/SELECT `armor` FROM `items` WHERE `itmid` = {$ir['equip_armor']} LIMIT 1");
                            //If user has valid armor equipped.
                            if ($db->num_rows($q3) > 0) {
                                //Thickened Skin Skill
                                $dam -= $db->fetch_single($q3) - ($db->fetch_single($q3) * ((getUserSkill($userid, 6) * getSkillBonus(6)) / 100));
                            }
                            $db->free_result($q3);
                        }
                        $yourbeforehp = $ir['hp'];
                        //If the user doesn't have armor equipped.
                        if ($dam < -100000) {
                            $dam = abs($dam);
                        } //If damage is lower than 1, set to 1.
                        else if ($dam < 1) {
                            $dam = 1;
                        }
                        $crit = Random(1, 40);
                        //RNG for critical damage, multiply damage!
                        if ($crit == 17) {
                            $dam *= Random(20, 40) / 10;
                        } //Unlucky... crit is weak.
                        else if ($crit == 25 OR $crit == 8) {
                            $dam /= (Random(20, 40) / 10);
                        }
                        $dam = round($dam);
                        $youdata['hp'] -= $dam;
                        //If user has 1 hp.
                        if ($youdata['hp'] == 1) {
                            $dam += 1;
                            $youdata['hp'] = 0;
                        }
                        if (($ir['hp'] - $dam) < 0) {
                            $dam = $ir['hp'];
                            $youdata['hp'] = 0;
                        }
                        //If user HP less than 0, set to 0.
                        if ($ir['hp'] < 0) {
                            $ir['hp'] = 0;
                        }
                        if ($dam > $yourbeforehp) {
                            $dam = $ir['hp'];
                        }
						if ($wep != "Fists")
						{
							if ($enweps[$weptouse]['ammo'] > 0)
							{
								//True Shot skill
								if (getUserSkill($userid, 4) > 0)
								    $dam = $dam + ($dam * (getUserSkill($_GET['user'], 4) * getSkillBonus(4)) / 100);
							}
						}
                    } //Opponent misses their hit.
                    else {
                        $miss=1;
                    }
                }
                else
                {
                    $dam=0;
                }
                $ns = $_GET['nextstep'] + 1;
                if (!$theyusepotion)
                {
                    if ($miss == 1)
                    {
                        alert('info', "", "<b>Attempt {$ns})</b> {$odata['username']} attempted to strike you with their {$api->SystemItemIDtoName($enweps[$weptouse]['itmid'])} but missed. You have " . shortNumberParse($youdata['hp']) . " HP remaining.", false);
                    }
                    else
                    {
                        $db->query("UPDATE `users` SET `hp` = `hp` - {$dam} WHERE `userid` = {$userid}");
                        alert('danger', "", "<b>Attempt {$ns})</b> Using their {$wep}, {$odata['username']} managed to strike you dealing
                         " . shortNumberParse($dam) . " damage. You have " . shortNumberParse($youdata['hp']) . " HP remaining.", false, '', true);
                         user_log($_GET['user'],'dmgdone',$dam);
                         if (doPoisonLogic($_GET['user'], $userid))
                         {
                             toast("","Your opponent has successfully poisoned you!!");
                         }
                    }
                }
                else
                {
                    if ($api->UserHasItem($_GET['user'],$enweps[$weptouse]['itmid'],1))
                    {
                        consumeItem($_GET['user'], $enweps[$weptouse]['itmid']);
                        alert('danger', "", "<b>Attempt {$ns})</b> {$odata['username']} consumes {$wep}!", false, '', true);
                        $api->UserTakeItem($_GET['user'],$enweps[$weptouse]['itmid'],1);
                    }
                    else
                    {
                        $api->GameAddNotification($_GET['user'],"You ran out of {$wep} while in combat.", 'fas fa-exclamation-circle', 'red');
                        alert('info', "", "<b>Attempt {$ns})</b> {$odata['username']} attempted to strike you, but missed. You have " . shortNumberParse($youdata['hp']) . " HP remaining.", false);
                        unequipUserSlot($_GET['user'], "equip_potion");
                    }
                }
                //User has no HP left, redirect to loss.
                if ($youdata['hp'] <= 0) {
                    $youdata['hp'] = 0;
                    $_SESSION['attacklost'] = $_GET['user'];
                    $api->UserInfoSet($userid, "hp", 0);
                    echo "<form action='?action=lost&ID={$_GET['user']}&ref={$ref}' method='post'><input type='submit' class='btn btn-primary' value='Lose Fight' />";
                }
            }
	}	//Opponent has less than 5 HP, fight cannot start.
    else if ($odata['hp'] < 5) {
		$_SESSION['attacking'] = 0;
		$_SESSION['attack_scroll'] = 0;
        $ir['attacking'] = 0;
        alert("danger", "Uh Oh!", "{$odata['username']}'s health is too low to be attacked.", true, "{$ref}.php");
        die($h->endpage());
    } //Stop combat if user and opponent are in same guild.
    else if ($ir['guild'] == $odata['guild'] && $ir['guild'] > 0)
	{
		if (!hasPendantEquipped($userid,339))
		{
			alert("danger", "Uh Oh!", "Hit the bong, not your guild mates. {$odata['username']} is in the same guild as you!", true, "{$ref}.php");
			die($h->endpage());
		}
		else
		{
			if (!hasPendantEquipped($_GET['user'],339))
			{
				alert("danger", "Uh Oh!", "{$odata['username']} does not have {$api->SystemItemIDtoName(339)} Pendant equipped and cannot be attacked now.", true, "{$ref}.php");
				die($h->endpage());
			}
		}
    } //If user does not have enough energy.
    else if ($youdata['energy'] < $youdata['maxenergy'] / $set['AttackEnergyCost']) {
        $EnergyPercent = floor(100 / $set['AttackEnergyCost']);
        $UserCurrentEnergy = $api->UserInfoGet($userid,'energy',true);
        alert("danger", "Uh Oh!", "You need to have {$EnergyPercent}% Energy to attack someone. You only have
		                        {$UserCurrentEnergy}%", true, "{$ref}.php");
        die($h->endpage());
    } //If user and opponent are in different towns.
    else if (($youdata['location'] != $odata['location']) && $_SESSION['attack_scroll'] == 0) {
        alert("danger", "Uh Oh!", "You and your opponent are in different towns.", true, "{$ref}.php");
		echo "<div class='row'>";
        if ($api->UserHasItem($userid,90,1))
		{
			echo "<div class='col-md'><a href='?action=attacking&user={$_GET['user']}&scroll=1&ref={$ref}'>" . returnIcon(90,5) . "</a><br />
					[<a href='?action=attacking&user={$_GET['user']}&scroll=1'>Use {$api->SystemItemIDtoName(90)}</a>]
				</div>";
		}
		if ($api->UserHasItem($userid,247,1))
		{
			echo "<div class='col-md'><a href='?action=attacking&user={$_GET['user']}&scroll=2&ref={$ref}'>" . returnIcon(247,5) . "</a><br />
					[<a href='?action=attacking&user={$_GET['user']}&scroll=2'>Use {$api->SystemItemIDtoName(247)}</a>]
				</div>";
		}
		if ($api->UserHasItem($userid,266,1))
		{
			echo "<div class='col-md'><a href='?action=attacking&user={$_GET['user']}&scroll=3&ref={$ref}'>" . returnIcon(266,5) . "</a><br />
					[<a href='?action=attacking&user={$_GET['user']}&scroll=3'>Use {$api->SystemItemIDtoName(266)}</a>]
				</div>";
		}
		echo "</div>";
		die($h->endpage());
    }
    //If opponent or user have no HP.
    if ($youdata['hp'] <= 0 OR $odata['hp'] <= 0) {
        //lol no
    } else {
        $vars['hpperc'] = round($youdata['hp'] / $youdata['maxhp'] * 100);
        $vars['hpopp'] = 100 - $vars['hpperc'];
        $vars2['hpperc'] = round($odata['hp'] / $odata['maxhp'] * 100);
        $vars2['hpopp'] = 100 - $vars2['hpperc'];
        $mw = $db->query("/*qc=on*/SELECT `itmid`,`itmname` FROM  `items`  WHERE `itmid` IN({$ir['equip_primary']}, {$ir['equip_secondary']}, {$ir['equip_potion']})");
		echo "<h3>Choose an item to use in combat</h3><hr />
		<div class='row'>";
        //If user has weapons equipped, allow him to select one.
		$prim = 0;
        if ($db->num_rows($mw) > 0) {
            while ($r = $db->fetch_row($mw)) {
                if (!isset($_GET['nextstep']))
                    $ns = 1;
                else
                    $ns = $_GET['nextstep'] + 2;
				if ($r['itmid'] == $ir['equip_primary'])
						$type = "Primary Weapon";
                elseif ($r['itmid'] == $ir['equip_secondary']) 
				{
                    $type = "Secondary Weapon";
                }
                elseif ($r['itmid'] == $ir['equip_potion']) 
				{
                    $type = "Potion Item";
                }
				echo "<div class='col'>
				<a href='?action=attacking&nextstep={$ns}&user={$_GET['user']}&weapon={$r['itmid']}&tresde={$tresder}&ref={$ref}'><b>{$type}</b><br />
					" . returnIcon($r['itmid'],5) . "</a>
				</div>";
            }
        } //If no weapons equipped, tell him to get back!
        else {
            alert("warning", "Uh Oh!", "Sir, you don't have a weapon equipped. You might wanna go back.", true, "{$ref}.php");
        }
        $db->free_result($mw);
        echo "</div><hr />";
		
		$yourpic = ($ir['display_pic']) ? "<img src='{$ir['display_pic']}' class='img-thumbnail img-responsive' width='125'>" : "";
		$theirpic = ($odata['display_pic']) ? "<img src='{$odata['display_pic']}' class='img-thumbnail img-responsive' width='125'>" : "";
        echo "<div class='row'>
				<div class='col-3'>
					{$yourpic}<br />
					{$ir['username']}
				</div>
				<div class='col'>
					<div class='progress' style='height: 1rem;'>
						<div class='progress-bar bg-danger progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$vars['hpperc']}' style='width:{$vars['hpperc']}%' aria-valuemin='0' aria-valuemax='{$youdata['maxhp']}'>
							<span>{$vars['hpperc']}% (" . shortNumberParse($youdata['hp']) . " / " .  shortNumberParse($youdata['maxhp']) . ")</span>
						</div>
					</div>
				</div>
			</div>
			<hr />
			<div class='row'>
				<div class='col-3'>
					{$theirpic}<br />
					{$odata['username']}
				</div>
				<div class='col'>
					<div class='progress' style='height: 1rem;'>
						<div class='progress-bar bg-danger progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$vars2['hpperc']}' style='width:{$vars2['hpperc']}%' aria-valuemin='0' aria-valuemax='{$odata['maxhp']}'>
							<span>
								{$vars2['hpperc']}% (" . shortNumberParse($odata['hp']) . " / " . shortNumberParse($odata['maxhp']) . ")
							</span>
						</div>
					</div>
				</div>
			</div>
		<hr />";
    }
}

function beat()
{
    global $db, $userid, $ir, $h, $api;
    $_GET['ID'] = isset($_GET['ID']) && ctype_digit($_GET['ID']) ? $_GET['ID'] : 0;
	$ref = (isset($_GET['ref']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])*$/i", $_GET['ref'])) ? $db->escape(strip_tags(stripslashes($_GET['ref']))) : 'index';
	if (userHasEffect($_GET['ID'], constant("sleep")))
	{
	    header("Location: ?action=homeinvade&ID={$_GET['ID']}&ref={$ref}");
	    exit;
	}
	$_SESSION['attacking'] = 0;
	$_SESSION['attack_scroll'] = 0;
    $ir['attacking'] = 0;
    $api->UserInfoSetStatic($userid, "attacking", 0);
    $od = $db->query("/*qc=on*/SELECT * FROM `users` WHERE `userid` = {$_GET['ID']}");
    //User attempts to win a fight they didn't win.
    if (!isset($_SESSION['attackwon']) || $_SESSION['attackwon'] != $_GET['ID']) {
        $api->UserInfoSet($userid, "xp", 0);
        alert("danger", "Security Issue!", "You did not beat this player. You've lost all your experience for that.", true, "{$ref}.php");
        die($h->endpage());
    }
    //The opponent does not exist.
    if (!$db->num_rows($od)) {
        $api->UserInfoSet($userid, "xp", 0);
        alert('danger', "Uh Oh!", "You are trying to beat a non-existent user. You've lost all your experience for that.", true, "{$ref}.php");
        die($h->endpage());
    }
    if ($db->num_rows($od) > 0) {
        $r = $db->fetch_row($od);
        $db->free_result($od);
        //Opponent's HP is 1, meaning the user has already claimed victory.
        if ($r['hp'] == 1) {
            alert('danger', "Uh Oh!", "Your opponent was already beat. Maybe next time.", true, "{$ref}.php");
            die($h->endpage());
        }
        $hosptime = Random(75, 175) + floor($ir['level'] / 2);
        $hospreason = $db->escape("Hospitalized By <a href='profile.php?user={$userid}'>{$ir['username']}</a>");
        //Set opponent's HP to 1. Means fight is over.
        $api->UserInfoSet($r['userid'], "hp", 1);
        //Place opponent in infirmary.
        $api->UserStatusSet($r['userid'], 'infirmary', $hosptime, $hospreason);
        //Give opponent notification that they were attacked.
        $api->GameAddNotification($r['userid'], "You were hospitalized by <a href='profile.php?user=$userid'>{$ir['username']}</a>
                                                    for " . shortNumberParse($hosptime) . " minutes.", 'game-icon game-icon-internal-injury', 'red');
        //Log that the user won the fight.
        $api->SystemLogsAdd($userid, 'attacking', "Hospitalized <a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$_GET['ID']}]
                                                for " . shortNumberParse($hosptime) . " minutes.");
        //Log that the opponent lost the fight.
        $api->SystemLogsAdd($_GET['ID'], 'attacking', "Hospitalized by <a href='profile.php?user={$userid}'>{$ir['username']}</a>
                                                    [{$userid}] for " . shortNumberParse($hosptime) . " minutes.");
        $_SESSION['attackwon'] = false;
        $additionaltext = "";
        //Both players are in a guild.
        if ($ir['guild'] > 0 && $r['guild'] > 0) {
            $oppguild = $db->query("/*qc=on*/SELECT `guild_id` FROM `guild` WHERE `guild_id` = {$r['guild']} LIMIT 1");
            if ($db->num_rows($oppguild) > 0) {
                $warq = $db->query("/*qc=on*/SELECT `gw_id` FROM `guild_wars`
				WHERE `gw_winner` = 0 AND (`gw_declarer` = {$ir['guild']} AND `gw_declaree` = {$r['guild']})
				OR (`gw_declaree` = {$ir['guild']} AND `gw_declarer` = {$r['guild']})");
                //Both players' guilds are at war with each other.
                if ($db->num_rows($warq) > 0) {
                    $wr = $db->fetch_single($warq);
                    $whoswho = $db->fetch_row($db->query("/*qc=on*/SELECT `gw_declarer`, `gw_declaree` FROM `guild_wars` WHERE `gw_id` = {$wr}"));
                    //Give points to user's guild.
                    if ($whoswho['gw_declarer'] == $ir['guild']) {
                        $db->query("UPDATE `guild_wars` SET `gw_drpoints` = `gw_drpoints` + 2 WHERE `gw_id` = {$wr}");
                    } else {
                        $db->query("UPDATE `guild_wars` SET `gw_depoints` = `gw_depoints` + 2 WHERE `gw_id` = {$wr}");
                    }
                    $additionaltext .= "By winning this fight, you've earned your guild two points in the guild war.";
                }
            }

        }
        //Bounty hunter stuff!
        $bhq=$db->query("/*qc=on*/SELECT * FROM `bounty_hunter` WHERE `bh_user` = {$r['userid']}");
        if ($db->num_rows($bhq) > 0)
        {
            $bhr=$db->fetch_row($bhq);
            //Double the infirmary time.
            put_infirmary($r['userid'], $hosptime, $hospreason);
            $api->GameAddNotification($r['userid'], "Your infirmary time was doubled since you had a bounty on your head.", 'game-icon game-icon-coins', '#B87333');
            $api->UserGiveCurrency($userid,'primary',$bhr['bh_bounty']);
            $bounty_format=shortNumberParse($bhr['bh_bounty']);
            $db->query("DELETE FROM `bounty_hunter` WHERE `bh_id` = {$bhr['bh_id']}");
			addToEconomyLog('Bounty Hunter', 'copper', $bhr['bh_bounty']);
            $additionaltext .= " You have received {$bounty_format} Copper Coins for hospitalizing this user while they had a bounty on their head. They also received double infirmary time.";
        }
        //Tell player they won and ended the fight, and if they gained a guild war point.
        alert('success', "You've Bested {$r['username']}!!", "Your actions have caused {$r['username']} " . shortNumberParse($hosptime) . " minutes in the infirmary. {$additionaltext}", true, "{$ref}.php");
		attacklog($userid,$r['userid'],'beatup');
        $db->query("UPDATE `users` SET `kills` = `kills` + 1 WHERE `userid` = {$userid}");
        $db->query("UPDATE `users` SET `deaths` = `deaths` + 1 WHERE `userid` = {$r['userid']}");
		//Mission update
		$am=$db->query("SELECT * FROM `missions` WHERE `mission_userid` = {$userid}");
		if ($db->num_rows($am) > 0)
		{
			$db->query("UPDATE `missions` SET `mission_kill_count` = `mission_kill_count` + 1 WHERE `mission_userid` = {$userid}");
		}
		//Credit badge if needed.
		$ir['kills']=$ir['kills']+1;
		if ($ir['kills'] == 3000)
		{
			$api->UserGiveItem($userid,161,1);
		}
        //Opponent an NPC? Set their HP to 100%, and remove infirmary time.
        if ($r['user_level'] == 'NPC') {
            $db->query("UPDATE `users` SET `hp` = `maxhp` WHERE `userid` = {$r['userid']}");
            $db->query("UPDATE `infirmary` SET `infirmary_out` = 0 WHERE `infirmary_user` ={$r['userid']}");

        }
		if ($r['userid'] == 20)
		{
			$api->UserGiveItem($userid,187,1);
			$api->UserGiveCurrency($userid,'primary',500000);
			$api->UserGiveCurrency($userid,'secondary',500);
			$db->query("UPDATE `user_settings` SET `att_dg` = 1 WHERE `userid` = {$userid}");
			addToEconomyLog('Vote Rewards', 'copper', 500000);
			$api->GameAddNotification($userid,"For slaying Your Doppleganger in battle, you've received a unique badge, 500,000 Copper Coins and 500 Chivalry Tokens.",'game-icon game-icon-backup','purple');
		}
		//Check if the attacked user is, in fact, an active boss.
		$bossq=$db->query("SELECT * FROM `activeBosses` WHERE `boss_user` = {$r['userid']}");
		if ($db->num_rows($bossq) > 0)
		{
			$bossr=$db->fetch_row($bossq);
			if ($bossr['boss_do_announce'])
			{
				$api->GameAddAnnouncement("<a href='profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}] has just slain {$api->SystemUserIDtoName($r['userid'])} in combat and received a {$api->SystemItemIDtoName($bossr['boss_item_drop'])}. Such a high honor!");
			}
			$api->GameAddNotification($userid,"For defeating the {$api->SystemUserIDtoName($r['userid'])} boss in combat, you've received a {$api->SystemItemIDtoName($bossr['boss_item_drop'])}. Thank you for your service.");
			$api->UserGiveItem($userid,$bossr['boss_item_drop'],1);
			$api->SystemLogsAdd($userid, 'boss', "Defeated {$api->SystemUserIDtoName($r['userid'])}, got {$api->SystemItemIDtoName($bossr['boss_item_drop'])}.");
			$db->query("DELETE FROM `activeBosses` WHERE `boss_user` = {$r['userid']}");
			
		}
		if ($r['userid'] == 21)
		{
		    $turkeyKills=getCurrentUserPref(date('Y') . "turkeyKills",0);
			/*$gotBadge=getCurrentUserPref('2022turkeyBadge',0);
			if ($gotBadge == 0)
			{
				$api->UserGiveItem($userid,455,1);
				setCurrentUserPref('2022turkeyBadge',1);
			}*/
		    setCurrentUserPref(date('Y') . "turkeyKills",$turkeyKills+1);
			$feathers=Random(20,100);
			$api->UserGiveItem($userid,197,$feathers);
			$api->GameAddNotification($userid,"For hunting a turkey, you've received {$feathers} Turkey Feathers.");
		}
		doExtraBomb($userid, $r['userid']);
    } else {
        die($h->endpage());
    }
}

function lost()
{
    global $db, $userid, $ir, $h, $api;
    $_GET['ID'] = isset($_GET['ID']) && ctype_digit($_GET['ID']) ? $_GET['ID'] : 0;
	$ref = (isset($_GET['ref']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])*$/i", $_GET['ref'])) ? $db->escape(strip_tags(stripslashes($_GET['ref']))) : 'index';
    $_SESSION['attacking'] = 0;
	$_SESSION['attack_scroll'] = 0;
    $ir['attacking'] = 0;
    //User did not lose fight, or lost fight to someone else.
    if (!isset($_SESSION['attacklost']) || $_SESSION['attacklost'] != $_GET['ID']) {
        $api->UserInfoSet($userid, "xp", 0);
        alert("danger", "Security Issue!", "You did not lose your last fight to this player. You've lost all your
		                                    experience for that.", true, "{$ref}.php");
        die($h->endpage());
    }
    //If the opponent is not specified
    if (!$_GET['ID']) {
        alert('warning', "Security Issue!", "You are trying to lose a fight against non-existent user.", true, "{$ref}.php");
        die($h->endpage());
    }
    $od = $db->query("/*qc=on*/SELECT `username`, `level`, `user_level`, `kills`,
                      `guild`, `xp` FROM `users` WHERE `userid` = {$_GET['ID']}");
    //The opponent does not exist.
    if (!$db->num_rows($od)) {
        echo "The user you've lost to does not exist.";
        die($h->endpage());
    }
    $r = $db->fetch_row($od);
    $db->free_result($od);
    $qe = $ir['level'] * $ir['level'] * $ir['level'];
    $expgain = Random($qe / 4, $qe);
    //User loses XP for losing the fight.
    if ($expgain < 0) {
        $expgain = $expgain * -1;
    }
    $expgainp = $expgain / $ir['xp_needed'] * 100;
    if ($ir['xp'] - $expgain < 0) {
        $api->UserInfoSetStatic($userid, "xp", 0);
    } else {
        $api->UserInfoSet($userid, "xp", "-{$expgain}");
    }
    $api->UserInfoSetStatic($userid, "attacking", 0);
    $hosptime = Random(10, 20) + floor($ir['level'] / 2);
    $hospreason = "Lost a Fight";
    //Place user in infirmary.
    $api->UserStatusSet($userid, 'infirmary', $hosptime, $hospreason);
    //Give winner some XP
    $r['xp_needed'] = round(($r['level'] + 1.5) * ($r['level'] + 1.5) * ($r['level'] + 1.5) * 1.5);
    $qe2 = $r['level'] * $r['level'] * $r['level'];
    $expgain2 = Random($qe2 / 4, $qe2);
    $expgainp2 = $expgain2 / $r['xp_needed'] * 100;
	$api->SystemLogsAdd($_GET['ID'], 'xp_gain', "+" . shortNumberParse($expgain2) . "XP");
	$api->SystemLogsAdd($ir['userid'], 'xp_gain', "-" . shortNumberParse($expgain) . "XP");
    $expperc2 = round($expgainp2 / $r['xp_needed'] * 100);
    //Tell opponent that they were attacked by user, and emerged victorious.
    $api->GameAddNotification($_GET['ID'], "<a href='profile.php?user=$userid'>{$ir['username']}</a>
                                            picked a fight against you and lost. You've gained {$expperc2}% experience.", 'game-icon game-icon-crossed-swords', 'green');
    //Log that the user lost.
    $api->SystemLogsAdd($userid, 'attacking', "Attacked <a href='../profile.php?user={$_GET['ID']}'>{$r['username']}</a> [{$_GET['ID']}] and lost.");
    //Log that the opponent won.
    $api->SystemLogsAdd($_GET['ID'], 'attacking', "Attacked by <a href='../profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}] and won.");
    //Increase opponent's XP for winning.
    $api->UserInfoSetStatic($_GET['ID'], "xp", $r['xp'] + $expgainp2);
    $_SESSION['attacklost'] = 0;
    $additionaltext = "";
    //Both players in a guild.
    if ($ir['guild'] > 0 && $r['guild'] > 0) {
        $oppguild = $db->query("/*qc=on*/SELECT `guild_id` FROM `guild` WHERE `guild_id` = {$r['guild']} LIMIT 1");
        if ($db->num_rows($oppguild) > 0) {
            $warq = $db->query("/*qc=on*/SELECT `gw_id` FROM `guild_wars`
				WHERE `gw_winner` = 0 AND (`gw_declarer` = {$ir['guild']} AND `gw_declaree` = {$r['guild']})
				OR (`gw_declaree` = {$ir['guild']} AND `gw_declarer` = {$r['guild']})");
            //Both players' guilds are at war.
            if ($db->num_rows($warq) > 0) {
                $wr = $db->fetch_single($warq);
                $whoswho = $db->fetch_row($db->query("/*qc=on*/SELECT `gw_declarer`, `gw_declaree` FROM `guild_wars` WHERE `gw_id` = {$wr}"));
                //Give opponent's guild a point.
                if ($whoswho['gw_declarer'] == $ir['guild']) {
                    $db->query("UPDATE `guild_wars` SET `gw_depoints` = `gw_depoints` + 2 WHERE `gw_id` = {$wr}");
                } else {
                    $db->query("UPDATE `guild_wars` SET `gw_drpoints` = `gw_drpoints` + 2 WHERE `gw_id` = {$wr}");
                }
                $additionaltext = "You have gained two points for your opponent's guild.";
            }
        }
    }
	doExtraBomb($_GET['ID'], $userid);
    //Tell user they lost, and if they gave the other guild a point.
    alert('danger', "You lost to {$r['username']}!", "You have lost a fight, and lost " . shortNumberParse($expgainp) . "% experience! {$additionaltext}", true, "{$ref}.php");
    $db->query("UPDATE `users` SET `kills` = `kills` + 1 WHERE `userid` = {$_GET['ID']}");
    $db->query("UPDATE `users` SET `deaths` = `deaths` + 1 WHERE `userid` = {$userid}");
	//Mission update
	$am=$db->query("SELECT * FROM `missions` WHERE `mission_userid` = {$_GET['ID']}");
	if ($db->num_rows($am) > 0)
	{
		$db->query("UPDATE `missions` SET `mission_kill_count` = `mission_kill_count` + 1 WHERE `mission_userid` = {$_GET['ID']}");
	}
	//Credit badge if needed.
	$r['kills']=$r['kills']+1;
	if ($r['kills'] == 3000)
	{
		$api->UserGiveItem($_GET['ID'],161,1);
	}
	attacklog($userid,$_GET['ID'],'lost');
}

function xp()
{
    global $db, $userid, $ir, $h, $api;
    $_GET['ID'] = isset($_GET['ID']) && ctype_digit($_GET['ID']) ? $_GET['ID'] : 0;
	$ref = (isset($_GET['ref']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])*$/i", $_GET['ref'])) ? $db->escape(strip_tags(stripslashes($_GET['ref']))) : 'index';
	if (userHasEffect($_GET['ID'], constant("sleep")))
	{
	    header("Location: ?action=homeinvade&ID={$_GET['ID']}&ref={$ref}");
	    exit;
	}
	$_SESSION['attacking'] = 0;
	$_SESSION['attack_scroll'] = 0;
    $ir['attacking'] = 0;
    $api->UserInfoSetStatic($userid, "attacking", 0);
    $od = $db->query("/*qc=on*/SELECT * FROM `users` WHERE `userid` = {$_GET['ID']}");
    //User did not win the attack, or the attack they won is not against the current opponent.
    if (!isset($_SESSION['attackwon']) || $_SESSION['attackwon'] != $_GET['ID']) {
        $api->UserInfoSet($userid, "xp", 0);
        alert("danger", "Security Issue!", "You didn't win your last fight against this player. You've lost your
		    experience for that.", true, "{$ref}.php");
        die($h->endpage());
    }
    //Opponent does not exist.
    if (!$db->num_rows($od)) {
        alert('danger', "Uh Oh!", "You are trying to win a fight against a non-existent user.", true, "{$ref}.php");
        exit($h->endpage());
    }
    if ($db->num_rows($od) > 0) {
        $r = $db->fetch_row($od);
        $db->free_result($od);
        //Opponent was already beat.
        if ($r['hp'] == 1) {
            $api->UserInfoSet($userid, "xp", 0);
            alert('danger', "Uh Oh!", "You have already beat this user. Trying to win again is how you lose experience...
			    which is what just happened to you.", true, "{$ref}.php");
            exit($h->endpage());
        } else {
            //XP gained is Opponent's Level x Opponent's Level x Opponent's Level.
            $qe = ($r['level'] * $r['level'] * $r['level']);
            //Add 10% bonus if Exp. token is equipped
			if (hasPendantEquipped($userid,93))
			{
			    //Seasoned Warrior Skill
			    if (getUserSkill($userid, 5) > 0)
			        $specialnumber = (getUserSkill($userid, 5) * getSkillBonus(5)) / 100;
				$qe=$qe+($qe*0.1);
				$qe=$qe+($qe*$specialnumber);
			}
            //Make the XP gained a little random...
            $expgain = Random($qe / 2, $qe);
			$xplostequip='';
			$xploststat='';
			$weapons=$db->fetch_row($db->query("/*qc=on*/SELECT `equip_primary`, `equip_secondary`, `equip_armor`
                                                FROM `users`
                                                WHERE `userid` = {$r['userid']}"));
            $ostat=$db->fetch_row($db->query("/*qc=on*/SELECT `strength`,`agility`,`guard` FROM `userstats` WHERE `userid` = {$r['userid']}"));
            $ototal=$ostat['strength']+$ostat['agility']+$ostat['guard'];            
            //Opponent does not have any armor or weapon equipped, dock the user's XP another 75%
            if (empty($weapons['equip_primary']) && empty($weapons['equip_secondary'])
                || empty($weapons['equip_armor']))
            {
                $expgain=$expgain*0.5;
                $xplostequip="Your experience gains were decreased by 50% because your opponent had no equipment.";
            }
            if ($expgain < 1)
                $expgain = 1;
            $expperc = round($expgain / $ir['xp_needed'] * 100);
            $hosptime = Random(5, 15) + floor($ir['level'] / 10);
            //Give user XP.
			attacklog($userid,$_GET['ID'],'xp');
			$api->SystemLogsAdd($ir['userid'], 'xp_gain', "+" . shortNumberParse($expgain) . "XP");
			$expgain=autoDonateXP($userid, $expgain, $ir['guild']);
            $db->query("UPDATE `users` SET `xp` = `xp` + {$expgain} WHERE `userid` = {$userid}");
            $hospreason = $db->escape("Used for Experience by <a href='profile.php?user={$userid}'>{$ir['username']}</a>");
            //Set opponent's HP to 1.
            $api->UserInfoSet($r['userid'], "hp", 1);
            //Place opponent in infirmary.
            $api->UserStatusSet($r['userid'], 'infirmary', $hosptime, $hospreason);
            //Tell opponent they were attacked by the user and lost.
            $api->GameAddNotification($r['userid'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> attacked you
                and used you for experience.", 'game-icon game-icon-crossed-swords', 'red');
            //Log that the user won.
            $api->SystemLogsAdd($userid, 'attacking', "Attacked <a href='../profile.php?user={$_GET['ID']}'>{$r['username']}</a> [{$r['userid']}] and gained {$expperc}% Experience.");
            //Log that the opponent lost.
            $api->SystemLogsAdd($_GET['ID'], 'attacking', "Attacked by <a href='../profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}] and left for experience.");
            $_SESSION['attackwon'] = 0;
            $additionaltext = "";
            //Both players are in a guild.
            if ($ir['guild'] > 0 && $r['guild'] > 0) {
                $oppguild = $db->query("/*qc=on*/SELECT `guild_id` FROM `guild` WHERE `guild_id` = {$r['guild']} LIMIT 1");
                if ($db->num_rows($oppguild) > 0) {
					$warq = $db->query("/*qc=on*/SELECT `gw_id` FROM `guild_wars`
				WHERE `gw_winner` = 0 AND (`gw_declarer` = {$ir['guild']} AND `gw_declaree` = {$r['guild']})
				OR (`gw_declaree` = {$ir['guild']} AND `gw_declarer` = {$r['guild']})");
                    //Both players' guilds are at war with each other.
                    if ($db->num_rows($warq) > 0) {
                        $wr = $db->fetch_single($warq);
                        $whoswho = $db->fetch_row($db->query("/*qc=on*/SELECT `gw_declarer`, `gw_declaree` FROM `guild_wars` WHERE `gw_id` = {$wr}"));
                        //Give user's guild a point.
                        if ($whoswho['gw_declarer'] == $ir['guild']) {
                            $db->query("UPDATE `guild_wars` SET `gw_drpoints` = `gw_drpoints` + 1 WHERE `gw_id` = {$wr}");
                        } else {
                            $db->query("UPDATE `guild_wars` SET `gw_depoints` = `gw_depoints` + 1 WHERE `gw_id` = {$wr}");
                        }
                        $additionaltext = "For winning this fight, you have gained your guild 1 point.";
                    }
                }

            }
            //Tell user they won, and if they received a point or not.
            alert('success', "You've bested {$r['username']}!", "You decide to finish the fight the honorable way. You
			    have gained (" . shortNumberParse($expperc) . "%, " . shortNumberParse($expgain) . ") experience for this. {$xploststat} {$xplostequip} {$additionaltext}", true, "{$ref}.php");

            $db->query("UPDATE `users` SET `kills` = `kills` + 1 WHERE `userid` = {$userid}");
            $db->query("UPDATE `users` SET `deaths` = `deaths` + 1 WHERE `userid` = {$r['userid']}");
			//Mission update
			$am=$db->query("SELECT * FROM `missions` WHERE `mission_userid` = {$userid}");
			if ($db->num_rows($am) > 0)
			{
				$db->query("UPDATE `missions` SET `mission_kill_count` = `mission_kill_count` + 1 WHERE `mission_userid` = {$userid}");
			}
			//Credit badge if needed.
			$ir['kills']=$ir['kills']+1;
			if ($ir['kills'] == 3000)
			{
				$api->UserGiveItem($userid,161,1);
			}
            //Opponent is NPC, so lets refill their HP, and remove infirmary time.
            if ($r['user_level'] == 'NPC') {
                $db->query("UPDATE `users` SET `hp` = `maxhp`  WHERE `userid` = {$r['userid']}");
                $db->query("UPDATE `infirmary` SET `infirmary_out` = 0 WHERE `infirmary_user` ={$r['userid']}");
            }
			$npcquery = $db->query("/*qc=on*/SELECT * FROM `botlist` WHERE `botuser` = {$r['userid']}");
			//Opponent is registered on bot list.
			if ($db->num_rows($npcquery) > 0) {
				$results2 = $db->fetch_row($npcquery);
				$timequery = $db->query("/*qc=on*/SELECT `lasthit` FROM `botlist_hits` WHERE `userid` = {$userid} && `botid` = {$r['userid']}");
				$r2 = $db->fetch_single($timequery);
				//Opponent's drop has already been collected and the time hasn't reset.
				if ((time() <= ($r2 + $results2['botcooldown'])) && ($r2 > 0)) {
					//Nope
				} //Bot's item can be collected.
				else {
					$time = time();
					$exists = $db->query("/*qc=on*/SELECT `botid` FROM `botlist_hits` WHERE `userid` = {$userid} AND `botid` = {$r['userid']}");
					//Place user's hittime into database.
					if ($db->num_rows($exists) == 0) {
						$db->query("INSERT INTO `botlist_hits` (`userid`, `botid`, `lasthit`) VALUES ('{$userid}', '{$r['userid']}', '{$time}')");
					} else {
						$db->query("UPDATE `botlist_hits` SET `lasthit` = {$time} WHERE `userid` = {$userid} AND `botid` = {$r['userid']}");
					}
				}
			}
			if ($r['userid'] == 20)
			{
				$api->UserGiveItem($userid,187,1);
				$api->UserGiveCurrency($userid,'primary',500000);
				$api->UserGiveCurrency($userid,'secondary',500);
				$db->query("UPDATE `user_settings` SET `att_dg` = 1 WHERE `userid` = {$userid}");
				addToEconomyLog('Vote Rewards', 'copper', 500000);
				addToEconomyLog('Vote Rewards', 'token', 500);
				$api->GameAddNotification($userid,"For slaying Your Doppleganger in battle, you've received a unique badge, 500,000 Copper Coins and 500 Chivalry Tokens.",'game-icon game-icon-backup','purple');
			}
			//Check if the attacked user is, in fact, an active boss.
			$bossq=$db->query("SELECT * FROM `activeBosses` WHERE `boss_user` = {$r['userid']}");
			if ($db->num_rows($bossq) > 0)
			{
				$bossr=$db->fetch_row($bossq);
				if ($bossr['boss_do_announce'])
				{
					$api->GameAddAnnouncement("<a href='profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}] has just slain {$api->SystemUserIDtoName($r['userid'])} in combat and received a {$api->SystemItemIDtoName($bossr['boss_item_drop'])}. Such a high honor!");
				}
				$api->GameAddNotification($userid,"For defeating the {$api->SystemUserIDtoName($r['userid'])} boss in combat, you've received a {$api->SystemItemIDtoName($bossr['boss_item_drop'])}. Thank you for your service.");
				$api->UserGiveItem($userid,$bossr['boss_item_drop'],1);
				$api->SystemLogsAdd($userid, 'boss', "Defeated {$api->SystemUserIDtoName($r['userid'])}, got {$api->SystemItemIDtoName($bossr['boss_item_drop'])}.");
				$db->query("DELETE FROM `activeBosses` WHERE `boss_user` = {$r['userid']}");
				
			}
			if ($r['userid'] == 21)
			{
			    $turkeyKills=getCurrentUserPref(date('Y') . "turkeyKills",0);
				/*$gotBadge=getCurrentUserPref('2022turkeyBadge',0);
				if ($gotBadge == 0)
				{
					$api->UserGiveItem($userid,455,1);
					setCurrentUserPref('2022turkeyBadge',1);
				}*/
			    setCurrentUserPref(date('Y') . "turkeyKills",$turkeyKills+1);
				$feathers=Random(20,100);
				$api->UserGiveItem($userid,197,$feathers);
				$api->GameAddNotification($userid,"For hunting a turkey, you've received {$feathers} Turkey Feathers.");
			}
            if ($r['user_level'] != 'NPC') {
                $last5=time()-600;
                $attackedcount=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`attack_id`) FROM `attack_logs` WHERE `attacked` = {$r['userid']} AND `result` = 'xp' AND `attack_time` > {$last5}"));
                if ($attackedcount > 3)
                {
                    $loss=$ototal*0.03;
                    $db->query("UPDATE `userstats` 
                                SET `strength` = `strength` - {$loss},
                                `agility` = `agility` - {$loss},
                                `guard` = `guard` - {$loss}
                                WHERE `userid` = {$r['userid']}");
                    $api->GameAddNotification($r['userid'],"You have lost " . shortNumberParse($loss) . " strength, agility and guard for allowing yourself to be used for experience too frequently.", 'game-icon game-icon-crucifix', 'red');
                }
            }
			doExtraBomb($userid, $r['userid']);
        }
    }
}

function mug()
{
    global $db, $userid, $ir, $h, $api;
    $_GET['ID'] = isset($_GET['ID']) && ctype_digit($_GET['ID']) ? $_GET['ID'] : 0;
	$ref = (isset($_GET['ref']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])*$/i", $_GET['ref'])) ? $db->escape(strip_tags(stripslashes($_GET['ref']))) : 'index';
    $_SESSION['attacking'] = 0;
	$_SESSION['attack_scroll'] = 0;
    $ir['attacking'] = 0;
    $api->UserInfoSetStatic($userid, "attacking", 0);
    $od = $db->query("/*qc=on*/SELECT * FROM `users` WHERE `userid` = {$_GET['ID']}");
    //User did not win fight, or won fight against someone else.
    if (!isset($_SESSION['attackwon']) || $_SESSION['attackwon'] != $_GET['ID']) {
        $api->UserInfoSet($userid, "xp", 0);
        alert("danger", "Security Issue!", "You didn't win the fight against this opponent. You've lost all your
		    experience.", true, "{$ref}.php");
        die($h->endpage());
    }
    //Opponent does not exist.
    if (!$db->num_rows($od)) {
        alert('danger', "Uh Oh!", "You are trying to attack a non-existent user.", true, "{$ref}.php");
        exit($h->endpage());
    }
    if ($db->num_rows($od) > 0) {
        $r = $db->fetch_row($od);
        $db->free_result($od);
        //Opponent's HP is 1, meaning fight has already concluded.
        if ($r['hp'] == 1) {
            $api->UserInfoSet($userid, "xp", 0);
            alert('danger', "Uh Oh!", "Stop trying to abuse bugs, dude. You've lost all your experience.", true, "{$ref}.php");
            exit($h->endpage());
        } else {
			attacklog($userid,$_GET['ID'],'mugged');
			$minimum=round($r['primary_currency']*0.02);
			$maximum=round($r['primary_currency']*0.2);
			$specialnumber = ((getUserSkill($userid, 13) * getSkillBonus(13)) / 100);
            $stole = Random($minimum,$maximum);
			$stole = $stole+($stole*$specialnumber);
            $hosptime = Random(20, 40) + floor($ir['level'] / 6);
            $hospreason = $db->escape("Robbed by <a href='profile.php?user={$userid}'>{$ir['username']}</a>");
            //Set opponent HP to 1.
            $api->UserInfoSet($r['userid'], "hp", 1);
            //Take opponent's Copper Coins and give it to user.
            $api->UserTakeCurrency($r['userid'], 'primary', $stole);
            $api->UserGiveCurrency($userid, 'primary', $stole);
            //Place opponent in infirmary.
            $api->UserStatusSet($r['userid'], 'infirmary', $hosptime, $hospreason);
            //Tell opponent they were mugged, and for how much, by user.
            $api->GameAddNotification($r['userid'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> mugged you and stole " . shortNumberParse($stole) . " Copper Coins.", 'game-icon game-icon-robber', 'red');
            //Log that the user won and stole some Copper Coins, and that
            //the opponent lost and lost Copper Coins.
            $api->SystemLogsAdd($userid, 'attacking', "Mugged <a href='../profile.php?user={$_GET['ID']}'>{$r['username']}</a> [{$r['userid']}] and stole " . shortNumberParse($stole) . " Copper Coins.");
            $api->SystemLogsAdd($_GET['ID'], 'attacking', "Mugged by <a href='../profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}] and lost " . shortNumberParse($stole) . " Copper Coins.");
            $_SESSION['attackwon'] = 0;
            $additionaltext = "";
            //Both players are in a guild.
            if ($ir['guild'] > 0 && $r['guild'] > 0) {
                $oppguild = $db->query("/*qc=on*/SELECT `guild_id` FROM `guild` WHERE `guild_id` = {$r['guild']} LIMIT 1");
                if ($db->num_rows($oppguild) > 0) {
                    $warq = $db->query("/*qc=on*/SELECT `gw_id` FROM `guild_wars`
				    WHERE `gw_winner` = 0 AND (`gw_declarer` = {$ir['guild']} AND `gw_declaree` = {$r['guild']})
				    OR (`gw_declaree` = {$ir['guild']} AND `gw_declarer` = {$r['guild']})");
                    //Both players' guilds are at war with each other.
                    if ($db->num_rows($warq) > 0) {
                        $wr = $db->fetch_single($warq);
                        $whoswho = $db->fetch_row($db->query("/*qc=on*/SELECT `gw_declarer`, `gw_declaree` FROM `guild_wars` WHERE `gw_id` = {$wr}"));
                        //Give user's guild a point.
                        if ($whoswho['gw_declarer'] == $ir['guild']) {
                            $db->query("UPDATE `guild_wars` SET `gw_drpoints` = `gw_drpoints` + 3 WHERE `gw_id` = {$wr}");
                        } else {
                            $db->query("UPDATE `guild_wars` SET `gw_depoints` = `gw_depoints` + 3 WHERE `gw_id` = {$wr}");
                        }
                        $additionaltext = "For winning this fight, you've won your guild 3 points.";
                    }
                }

            }
            $db->query("UPDATE `users` SET `kills` = `kills` + 1 WHERE `userid` = {$userid}");
            $db->query("UPDATE `users` SET `deaths` = `deaths` + 1 WHERE `userid` = {$r['userid']}");
			//Mission update
			$am=$db->query("SELECT * FROM `missions` WHERE `mission_userid` = {$userid}");
			if ($db->num_rows($am) > 0)
			{
				$db->query("UPDATE `missions` SET `mission_kill_count` = `mission_kill_count` + 1 WHERE `mission_userid` = {$userid}");
			}
			//Credit badge if needed.
			$ir['kills']=$ir['kills']+1;
			if ($ir['kills'] == 3000)
			{
				$api->UserGiveItem($userid,161,1);
			}
            //Opponent is NPC, so remove infirmary time and refill HP.
            if ($r['user_level'] == 'NPC') {
                $db->query("UPDATE `users` SET `hp` = `maxhp`  WHERE `userid` = {$r['userid']}");
                $db->query("UPDATE `infirmary` SET `infirmary_out` = 0 WHERE `infirmary_user` ={$r['userid']}");
            }
			if ($r['userid'] == 20)
			{
				$api->UserGiveItem($userid,187,1);
				$api->UserGiveCurrency($userid,'primary',500000);
				$api->UserGiveCurrency($userid,'secondary',500);
				$db->query("UPDATE `user_settings` SET `att_dg` = 1 WHERE `userid` = {$userid}");
				addToEconomyLog('Vote Rewards', 'copper', 500000);
				addToEconomyLog('Vote Rewards', 'token', 500);
				$api->GameAddNotification($userid,"For slaying Your Doppleganger in battle, you've received a unique badge, 500,000 Copper Coins and 500 Chivalry Tokens.",'game-icon game-icon-backup','purple');
			}
			//Check if the attacked user is, in fact, an active boss.
			$bossq=$db->query("SELECT * FROM `activeBosses` WHERE `boss_user` = {$r['userid']}");
			if ($db->num_rows($bossq) > 0)
			{
				$bossr=$db->fetch_row($bossq);
				if ($bossr['boss_do_announce'])
				{
					$api->GameAddAnnouncement("<a href='profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}] has just slain {$api->SystemUserIDtoName($r['userid'])} in combat and received a {$api->SystemItemIDtoName($bossr['boss_item_drop'])}. Such a high honor!");
				}
				$api->GameAddNotification($userid,"For defeating the {$api->SystemUserIDtoName($r['userid'])} boss in combat, you've received a {$api->SystemItemIDtoName($bossr['boss_item_drop'])}. Thank you for your service.");
				$api->UserGiveItem($userid,$bossr['boss_item_drop'],1);
				$api->SystemLogsAdd($userid, 'boss', "Defeated {$api->SystemUserIDtoName($r['userid'])}, got {$api->SystemItemIDtoName($bossr['boss_item_drop'])}.");
				$db->query("DELETE FROM `activeBosses` WHERE `boss_user` = {$r['userid']}");
				
			}
			if ($r['userid'] == 21)
			{
			    $turkeyKills=getCurrentUserPref(date('Y') . "turkeyKills",0);
				/*$gotBadge=getCurrentUserPref('2022turkeyBadge',0);
				if ($gotBadge == 0)
				{
					$api->UserGiveItem($userid,455,1);
					setCurrentUserPref('2022turkeyBadge',1);
				}*/
			    setCurrentUserPref(date('Y') . "turkeyKills",$turkeyKills+1);
				$feathers=Random(20,100);
				$api->UserGiveItem($userid,197,$feathers);
				$api->GameAddNotification($userid,"For hunting a turkey, you've received {$feathers} Turkey Feathers.");
			}
        }
        $npcquery = $db->query("/*qc=on*/SELECT * FROM `botlist` WHERE `botuser` = {$r['userid']}");
        //Opponent is registered on bot list.
        if ($db->num_rows($npcquery) > 0) {
            $results2 = $db->fetch_row($npcquery);
            $timequery = $db->query("/*qc=on*/SELECT `lasthit` FROM `botlist_hits` WHERE `userid` = {$userid} && `botid` = {$r['userid']}");
            $r2 = $db->fetch_single($timequery);
            //Opponent's drop has already been collected and the time hasn't reset.
            if ((time() <= ($r2 + $results2['botcooldown'])) && ($r2 > 0)) {
                //Nope
            } //Bot's item can be collected.
            else {
                //Give user the bot's item.
                $api->UserGiveItem($userid, $results2['botitem'], 1);
                $time = time();
                $exists = $db->query("/*qc=on*/SELECT `botid` FROM `botlist_hits` WHERE `userid` = {$userid} AND `botid` = {$r['userid']}");
                //Place user's hittime into database.
                if ($db->num_rows($exists) == 0) {
                    $db->query("INSERT INTO `botlist_hits` (`userid`, `botid`, `lasthit`) VALUES ('{$userid}', '{$r['userid']}', '{$time}')");
                } else {
                    $db->query("UPDATE `botlist_hits` SET `lasthit` = {$time} WHERE `userid` = {$userid} AND `botid` = {$r['userid']}");
                }
                //Tell user they took an item.
                $api->GameAddNotification($userid, "For mugging the " . $api->SystemUserIDtoName($r['userid']) . " bot, you have gained 1 " . $api->SystemItemIDtoName($results2['botitem']) . ".", 'game-icon game-icon-open-treasure-chest', 'brown');
            }
        }
		//Tell user they won the fight, and how much currency they took.
            alert('success', "You have bested {$r['username']}!", "You have knocked them out and taken out their wallet.
			    You grab their wallet and take " . shortNumberParse($stole) . " Copper Coins! {$additionaltext}", true, "{$ref}.php");
			doExtraBomb($userid, $r['userid']);
	}
}

function home_invasion()
{
    global $db, $userid, $ir, $h, $api;
    $_GET['ID'] = isset($_GET['ID']) && ctype_digit($_GET['ID']) ? $_GET['ID'] : 0;
    $ref = (isset($_GET['ref']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])*$/i", $_GET['ref'])) ? $db->escape(strip_tags(stripslashes($_GET['ref']))) : 'index';
    if (!userHasEffect($_GET['ID'], constant("sleep")))
    {
        header("Location: ?action=mug&ID={$_GET['ID']}&ref={$ref}");
        exit;
    }
    $_SESSION['attacking'] = 0;
    $_SESSION['attack_scroll'] = 0;
    $ir['attacking'] = 0;
    $api->UserInfoSetStatic($userid, "attacking", 0);
    $od = $db->query("/*qc=on*/SELECT * FROM `users` WHERE `userid` = {$_GET['ID']}");
    //User did not win fight, or won fight against someone else.
    if (!isset($_SESSION['attackwon']) || $_SESSION['attackwon'] != $_GET['ID']) {
        $api->UserInfoSet($userid, "xp", 0);
        alert("danger", "Security Issue!", "You didn't win the fight against this opponent. You've lost all your
		    experience.", true, "{$ref}.php");
        die($h->endpage());
    }
    //Opponent does not exist.
    if (!$db->num_rows($od)) {
        alert('danger', "Uh Oh!", "You are trying to attack a non-existent user.", true, "{$ref}.php");
        exit($h->endpage());
    }
    if ($db->num_rows($od) > 0) {
        $r = $db->fetch_row($od);
        $db->free_result($od);
        //Opponent's HP is 1, meaning fight has already concluded.
        if ($r['hp'] == 1) {
            $api->UserInfoSet($userid, "xp", 0);
            alert('danger', "Uh Oh!", "Stop trying to abuse bugs, dude. You've lost all your experience.", true, "{$ref}.php");
            exit($h->endpage());
        } else {
            $userEstate = $db->fetch_single($db->query("SELECT `estate` FROM `users` WHERE `userid` = {$_GET['ID']}"));
            $userVault = $db->fetch_single($db->query("SELECT `vault` FROM `user_estates` WHERE `ue_id` = {$userEstate}"));
            attacklog($userid,$_GET['ID'],'mugged');
            $minimum=round($userVault*0.02);
            $maximum=round($userVault*0.2);
            $specialnumber = ((getUserSkill($userid, 13) * getSkillBonus(13)) / 100);
            $stole = Random($minimum,$maximum);
            $stole = $stole+($stole*$specialnumber);
            $hosptime = Random(40, 95) + floor($ir['level'] / 6);
            $hospreason = $db->escape("Home invasion by <a href='profile.php?user={$userid}'>{$ir['username']}</a>");
            //Set opponent HP to 1.
            $api->UserInfoSet($r['userid'], "hp", 1);
            //Take opponent's Copper Coins and give it to user.
            $db->query("UPDATE `user_estates` SET `vault` = `vault` - {$stole} WHERE `ue_id` = {$userEstate}");
            $api->UserGiveCurrency($userid, 'primary', $stole);
            //Place opponent in infirmary.
            $api->UserStatusSet($r['userid'], 'infirmary', $hosptime, $hospreason);
            //Tell opponent they were mugged, and for how much, by user.
            $api->GameAddNotification($r['userid'], "<a href='profile.php?user=$userid'>{$ir['username']}</a> started a home invasion while you were sleeping. You were harmed in the process and they stole " . shortNumberParse($stole) . " Copper Coins from your estate's vault.", 'game-icon game-icon-robber', 'red');
            //Log that the user won and stole some Copper Coins, and that
            //the opponent lost and lost Copper Coins.
            $api->SystemLogsAdd($userid, 'attacking', "Home invasioned <a href='../profile.php?user={$_GET['ID']}'>{$r['username']}</a> [{$r['userid']}] and stole " . shortNumberParse($stole) . " Copper Coins.");
            $api->SystemLogsAdd($_GET['ID'], 'attacking', "Home invasioned by <a href='../profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}] and lost " . shortNumberParse($stole) . " Copper Coins.");
            $_SESSION['attackwon'] = 0;
            $additionaltext = "";
            //Both players are in a guild.
            if ($ir['guild'] > 0 && $r['guild'] > 0) {
                $oppguild = $db->query("/*qc=on*/SELECT `guild_id` FROM `guild` WHERE `guild_id` = {$r['guild']} LIMIT 1");
                if ($db->num_rows($oppguild) > 0) {
                    $warq = $db->query("/*qc=on*/SELECT `gw_id` FROM `guild_wars`
				    WHERE `gw_winner` = 0 AND (`gw_declarer` = {$ir['guild']} AND `gw_declaree` = {$r['guild']})
				    OR (`gw_declaree` = {$ir['guild']} AND `gw_declarer` = {$r['guild']})");
                    //Both players' guilds are at war with each other.
                    if ($db->num_rows($warq) > 0) {
                        $wr = $db->fetch_single($warq);
                        $whoswho = $db->fetch_row($db->query("/*qc=on*/SELECT `gw_declarer`, `gw_declaree` FROM `guild_wars` WHERE `gw_id` = {$wr}"));
                        //Give user's guild a point.
                        if ($whoswho['gw_declarer'] == $ir['guild']) 
                            $db->query("UPDATE `guild_wars` SET `gw_drpoints` = `gw_drpoints` + 5 WHERE `gw_id` = {$wr}");
                        else
                            $db->query("UPDATE `guild_wars` SET `gw_depoints` = `gw_depoints` + 5 WHERE `gw_id` = {$wr}");
                        $additionaltext = "For winning this fight, you've won your guild 5 point.";
                    }
                }
                
            }
            $db->query("UPDATE `users` SET `kills` = `kills` + 1 WHERE `userid` = {$userid}");
            $db->query("UPDATE `users` SET `deaths` = `deaths` + 1 WHERE `userid` = {$r['userid']}");
            //Mission update
            $am=$db->query("SELECT * FROM `missions` WHERE `mission_userid` = {$userid}");
            if ($db->num_rows($am) > 0)
            {
                $db->query("UPDATE `missions` SET `mission_kill_count` = `mission_kill_count` + 1 WHERE `mission_userid` = {$userid}");
            }
            //Credit badge if needed.
            $ir['kills']=$ir['kills']+1;
            if ($ir['kills'] == 3000)
            {
                $api->UserGiveItem($userid,161,1);
            }
            //Opponent is NPC, so remove infirmary time and refill HP.
            if ($r['user_level'] == 'NPC') {
                $db->query("UPDATE `users` SET `hp` = `maxhp`  WHERE `userid` = {$r['userid']}");
                $db->query("UPDATE `infirmary` SET `infirmary_out` = 0 WHERE `infirmary_user` ={$r['userid']}");
            }
        }
        //Tell user they won the fight, and how much currency they took.
        alert('success', "You have bested {$r['username']}!", "{$r['username']} is asleep while you attack them, 
            so you decide its best to help yourself to their vault, taking " . shortNumberParse($stole) . " Copper Coins! 
            {$additionaltext}", true, "{$ref}.php");
        doExtraBomb($userid, $r['userid']);
    }
}
$h->endpage();
