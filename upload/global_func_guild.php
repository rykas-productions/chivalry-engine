<?php
function isGuildStaff()
{
	global $gd, $userid;
	if ($gd['guild_owner'] == $userid || $gd['guild_coowner'] == $userid || $gd['guild_app_manager'] == $userid || $gd['guild_vault_manager'] == $userid|| $gd['guild_crime_lord'] == $userid)
		return true;
}

function isGuildLeadership()
{
	global $gd, $userid;
	if (isGuildLeader() || isGuildCoLeader())
		return true;
}

function isGuildAppManager()
{
	global $gd, $userid;
	if (isGuildLeadership() || $gd['guild_app_manager'] == $userid)
		return true;
}

function isGuildCrimeLord()
{
	global $gd, $userid;
	if (isGuildLeadership() || $gd['guild_crime_lord'] == $userid)
		return true;
}

function isGuildVaultManager()
{
	global $gd, $userid;
	if (isGuildLeadership() || $gd['guild_vault_manager'] == $userid)
		return true;
}

function isGuildLeader()
{
	global $gd, $userid;
	if ($gd['guild_owner'] == $userid)
		return true;
}

function isGuildCoLeader()
{
	global $gd, $userid;
	if ($gd['guild_coowner'] == $userid || isGuildLeader())
		return true;
}

function updateDonations($guildid,$userid,$type,$increase)
{
	global $db;
	$q=$db->query("/*qc=on*/SELECT * FROM `guild_donations` WHERE `guildid` = {$guildid} AND `userid` = {$userid}");
	if ($db->num_rows($q) == 0)
	{
		$db->query("INSERT INTO `guild_donations` (`userid`, `guildid`, `copper`, `tokens`, `xp`) VALUES ('{$userid}', '{$guildid}', '0', '0', '0')");
	}
	$db->query("UPDATE `guild_donations` SET `{$type}` = `{$type}` + {$increase} WHERE `userid` = {$userid} AND `guildid` = {$guildid}");
}

function deleteGuild($guild)
{
    global $db;
    $db->query("DELETE FROM `guild` WHERE `guild_id` = {$guild}");
    $db->query("DELETE FROM `guild_alliances` WHERE `alliance_a` = {$guild}");
    $db->query("DELETE FROM `guild_alliances` WHERE `alliance_b` = {$guild}");
    $db->query("DELETE FROM `guild_applications` WHERE `ga_guild` = {$guild}");
    $db->query("UPDATE `guild_armory` SET `gaGUILD` = 1 WHERE `gaGUILD` = {$guild}");
    $db->query("DELETE FROM `guild_crime_log` WHERE `gclGUILD` = {$guild}");
    $db->query("UPDATE `guild_districts` SET `district_owner` = 0 WHERE `district_owner` = {$guild}");
    $db->query("DELETE FROM `guild_district_info` WHERE `guild_id` = {$guild}");
    $db->query("DELETE FROM `guild_donations` WHERE `guildid` = {$guild}");
    $db->query("DELETE FROM `guild_ranks` WHERE `rank_guild` = {$guild}");
    $db->query("DELETE FROM `guild_wars` WHERE `gw_declarer` = {$guild}");
    $db->query("DELETE FROM `guild_wars` WHERE `gw_declaree` = {$guild}");
    $db->query("DELETE FROM `guild_notifications` WHERE `gn_id` = {$guild}");
    $db->query("DELETE FROM `guild_crime_log` WHERE `gclGUILD` = {$guild}");
    $db->query("UPDATE `users` SET `guild` = 0 WHERE `guild` = {$guild}");
    $db->query("UPDATE `town` SET `town_guild_owner` = 0 WHERE `town_guild_owner` = {$guild}");
}

function doDailyGuildFee()
{
    global $db, $api, $set;
    $plussevenday = time() + 604800;
    $gdfq=$db->query("/*qc=on*/SELECT * FROM `guild`");
    while ($gfr=$db->fetch_row($gdfq))
    {
        $warquery=$db->query("/*qc=on*/SELECT `gw_id` FROM `guild_wars` WHERE `gw_declarer` = {$gfr['guild_id']} OR `gw_declaree` = {$gfr['guild_id']}");
        if ($db->num_rows($warquery) == 0)
        {
            if ($gfr['guild_primcurr'] < $set['GUILD_PRICE'])
            {
                $debtText = "Your guild has gone into debt. You must pay the debt off in seven days, otherwise your guild will dissolve.";
                $db->query("UPDATE `guild` SET `guild_primcurr` = `guild_primcurr` - {$set['GUILD_PRICE']} WHERE `guild_id` = {$gfr['guild_id']}");
                $db->query("UPDATE `guild` SET `guild_debt_time` = {$plussevenday} WHERE `guild_id` = {$gfr['guild_id']} AND `guild_debt_time` = 0");
                $api->GuildAddNotification($gfr['guild_id'], "Your guild has paid " . shortNumberParse($set['GUILD_PRICE']) . " Copper Coins in upkeep, but has gone into debt.");
                $api->GameAddNotification($gfr['guild_owner'], $debtText);
                $api->GameAddNotification($gfr['guild_coowner'], $debtText);
            }
            else
            {
                $db->query("UPDATE `guild` SET `guild_primcurr` = `guild_primcurr` - {$set['GUILD_PRICE']} WHERE `guild_id` = {$gfr['guild_id']}");
                $api->GuildAddNotification($gfr['guild_id'], "Your guild has paid " . shortNumberParse($set['GUILD_PRICE']) . " Copper Coins in general maintanance and upkeep fees.");
            }
            addToEconomyLog('Guild Upkeep', 'copper', $set['GUILD_PRICE'] * -1);
        }
    }
}

function calculateUpkeep($guild)
{
    global $db, $gd, $set, $ir, $api, $districtConfig;
    //Default starter upkeep. before the districts.
    $upkeepFee = $set['GUILD_PRICE'];
    $q=$db->query("SELECT * FROM `guild_district_info` WHERE `guild_id` = {$guild}");
    while ($r=$db->fetch_row($q))
    {
        $upkeepFee=$upkeepFee;
        $warriors = countDeployedWarriors($guild);
        $archers = countDeployedArchers($guild);
        $generals = countDeployedGenerals($guild);
        if ($warriors > 0)
            $upkeepFee=$upkeepFee + ($warriors * $districtConfig['WarriorCostDaily']);
            if ($archers > 0)
                $upkeepFee=$upkeepFee + ($archers * $districtConfig['ArcherCostDaily']);
                if ($generals > 0)
                    $upkeepFee=$upkeepFee + ($generals * $districtConfig['GeneralCostDaily']);
    }
    return $upkeepFee;
}

function updateGuildWars()
{
    global $db, $time;
    $q3 = $db->query("/*qc=on*/SELECT * FROM `guild_wars` WHERE `gw_end` < {$time} AND `gw_winner` = 0");
    if ($db->num_rows($q3) > 0) {
        $r3 = $db->fetch_row($q3);
        //Select guild war declarer's name
        $guild_declare = $db->fetch_single(
            $db->query("/*qc=on*/SELECT `guild_name` FROM `guild` WHERE `guild_id` = {$r3['gw_declarer']}"));
        //Select guild war declaree's name
        $guild_declared = $db->fetch_single(
            $db->query("/*qc=on*/SELECT `guild_name` FROM `guild` WHERE `guild_id` = {$r3['gw_declaree']}"));
        //Guild War declarer has more points than the declaree.
        if ($r3['gw_drpoints'] > $r3['gw_depoints']) {
            //Make the declarer the winner,
            $db->query("UPDATE `guild_wars` SET `gw_winner` = {$r3['gw_declarer']} WHERE `gw_id` = {$r3['gw_id']}");
            guildnotificationadd($r3['gw_declarer'], "Your guild has defeated the {$guild_declared} guild in battle.");
            guildnotificationadd($r3['gw_declaree'], "Your guild was defeated in battle by the {$guild_declare} guild.");
            //Select the town ID where the guilds own.
            $town = $db->fetch_single(
                $db->query("/*qc=on*/SELECT `town_id` FROM `town` WHERE `town_guild_owner` = {$r3['gw_declarer']}"));
            $town2 = $db->fetch_single(
                $db->query("/*qc=on*/SELECT `town_id` FROM `town` WHERE `town_guild_owner` = {$r3['gw_declaree']}"));
            //If the declaree has a town under their control
            if ($town2 > 0) {
                //The declarer guild has no town of their own, so take from the declaree.
                if ($town == 0) {
                    $db->query("UPDATE `town` SET `town_guild_owner` = {$r3['gw_declarer']}  WHERE `town_guild_owner` = {$r3['gw_declaree']}");
                } //The declarer has their own town, so the declaree forfeits their control of their own town.
                else {
                    $db->query("UPDATE `town` SET `town_guild_owner` = 0 WHERE `town_guild_owner` = {$r3['gw_declaree']}");
                }
            }
            
        } //Guild War declaree has more points than the declarer.
        elseif ($r3['gw_drpoints'] < $r3['gw_depoints']) {
            //Make the declaree the winner,
            $db->query("UPDATE `guild_wars` SET `gw_winner` = {$r3['gw_declarer']} WHERE `gw_id` = {$r3['gw_id']}");
            guildnotificationadd($r3['gw_declaree'], "Your guild has defeated the {$guild_declare} guild in battle.");
            guildnotificationadd($r3['gw_declarer'], "Your guild was defeated in battle by the {$guild_declared} guild.");
            //Select the town ID where the guilds own.
            $town = $db->fetch_single(
                $db->query("/*qc=on*/SELECT `town_id` FROM `town` WHERE `town_guild_owner` = {$r3['gw_declarer']}"));
            $town2 = $db->fetch_single(
                $db->query("/*qc=on*/SELECT `town_id` FROM `town` WHERE `town_guild_owner` = {$r3['gw_declaree']}"));
            //If the declarer has a town under their control
            if ($town > 0) {
                //The declaree does not have a town, so take it from the declarer.
                if ($town2 == 0) {
                    $db->query("UPDATE `town` SET `town_guild_owner` = {$r3['gw_declaree']} WHERE `town_guild_owner` = {$r3['gw_declarer']}");
                } //The declaree has their own town, so make the declarer forfeit theirs.
                else {
                    $db->query("UPDATE `town` SET `town_guild_owner` = 0 WHERE `town_guild_owner` = {$r3['gw_declarer']}");
                }
            }
        } //The war was tied. Tell both guilds they tied, and remove the war from the database.
        else {
            $db->query("DELETE FROM `guild_wars` WHERE `gw_id` = {$r3['gw_id']}");
            guildnotificationadd($r3['gw_declaree'], "Your guild has tied the {$guild_declare} guild in battle.");
            guildnotificationadd($r3['gw_declarer'], "Your guild has tied the {$guild_declared} guild in battle.");
        }
        $r3['gw_drpoints']=$r3['gw_drpoints']*3;
        $r3['gw_depoints']=$r3['gw_depoints']*3;
        //Update guild experience, if needed.
        $db->query("UPDATE `guild` SET `guild_xp` = `guild_xp` + {$r3['gw_drpoints']} WHERE `guild_id` = {$r3['gw_declarer']}");
        $db->query("UPDATE `guild` SET `guild_xp` = `guild_xp` + {$r3['gw_depoints']} WHERE `guild_id` = {$r3['gw_declaree']}");
    }
}

function checkGuildCrimes()
{
    global $db;
    $time = time();
    //Check guild crimes!
    $guildcrime = $db->query("/*qc=on*/SELECT * FROM `guild` WHERE `guild_crime` > 0 AND `guild_crime_done` <= {$time}");
    while ($r = $db->fetch_row($guildcrime))
    {
        $last_on = time() - (24 * (60 * 60));
        $q = $db->query("SELECT `userid` FROM `users` WHERE `laston` > {$last_on} AND `guild` = {$r['guild_id']}");
        if ($db->num_rows($q) == 0)
        {
            $log = "Your guild did not even have enough active participants for this crime in the last day. Your guild failed.";
            $winnings = 0;
            $result = 'failure';
        }
        $r2 = $db->fetch_row($db->query("/*qc=on*/SELECT * FROM `guild_crimes` WHERE `gcID` = {$r['guild_crime']}"));
        $suc = Random(0, 1);
        if ($suc <= 3) {
            $log = $r2['gcSTART'] . $r2['gcSUCC'];
            $winnings = Random($r2['gcMINCASH'], $r2['gcMAXCASH']);
            $result = 'success';
        } else {
            $log = $r2['gcSTART'] . $r2['gcFAIL'];
            $winnings = 0;
            $result = 'failure';
        }
        $xp=(Random(1,5) * $r['guild_level']) * $r2['gcUSERS'];
        $db->query("UPDATE `guild`
                    SET `guild_primcurr` = `guild_primcurr` + {$winnings},
                    `guild_crime` = 0,
                    `guild_crime_done` = 0,
                    `guild_xp` = `guild_xp` + {$xp}
                    WHERE `guild_id` = {$r['guild_id']}");
        $db->query("INSERT INTO `guild_crime_log`
                    (`gclCID`, `gclGUILD`, `gclLOG`, `gclRESULT`, `gclWINNING`, `gclTIME`)
                    VALUES
                    ('{$r['guild_crime']}', '{$r['guild_id']}', '{$log}', '{$result}', '{$winnings}', '" . time() . "');");
        $i = $db->insert_id();
        addToEconomyLog('Criminal Activities', 'copper', $winnings);
        $qm = $db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `guild` = {$r['guild_id']}");
        while ($qr = $db->fetch_row($qm)) {
            notification_add($qr['userid'], "Your guild's crime was a complete {$result}! Click <a href='gclog.php?ID=$i'>here</a> to view more information.");
        }
    }
}

function checkGuildDebt()
{
    global $db, $api;
    $time = time();
    $db->query("UPDATE `guild` SET `guild_debt_time` = 0 WHERE `guild_primcurr` > -1");
    //Dissolve guild if debt unpaid.
    $gdup=$db->query("/*qc=on*/SELECT * FROM `guild` WHERE `guild_debt_time` < {$time} AND `guild_debt_time` > 0");
    while ($gdr=$db->fetch_row($gdup))
    {
        $api->GameAddNotification($gdr['guild_owner'], "Your guild was dissolved since it could not pay off its debt.");
        deleteGuild($gdr['guild_id']);
    }
}

function checkGuildVault()
{
    global $db, $set, $ir;
    $q = $db->query("SELECT * FROM `guild`");
    while ($r = $db->fetch_row($q))
    {
        $maxvault = (($r['guild_level'] * $set['GUILD_PRICE']) * 20);
        $maxtoken = (($r['guild_level'] * $set['GUILD_PRICE']) / 125);
        if ($r['guild_primcurr'] > $maxvault)
            $db->query("UPDATE `guild` SET `guild_primcurr` = {$maxvault} WHERE `guild_id` = {$r['guild_id']}");
        if ($r['guild_seccurr'] > $maxtoken)
            $db->query("UPDATE `guild` SET `guild_seccurr` = {$maxtoken} WHERE `guild_id` = {$r['guild_id']}");
    }
}

function guildSendLeadersNotif($guild_id, $notif)
{
    global $db, $api;
    $r = $db->fetch_row($db->query("SELECT * FROM `guild` WHERE `guild_id` = {$guild_id}"));
    $api->GameAddNotification($r['guild_owner'], $notif);
    $api->GameAddNotification($r['guild_coowner'], $notif);
    
}

function guildSendStaffNotif($guild_id, $notif)
{
    global $db, $api;
    $r = $db->fetch_row($db->query("SELECT * FROM `guild` WHERE `guild_id` = {$guild_id}"));
    guildSendLeadersNotif($guild_id, $notif);
    $api->GameAddNotification($r['guild_app_manager'], $notif);
    $api->GameAddNotification($r['guild_vault_manager'], $notif);
    $api->GameAddNotification($r['guild_crime_lord'], $notif);
}

function guildSendMemberNotif($guild_id, $notif)
{
    global $db, $api;
    $q = $db->query("SELECT `userid` FROM `users` WHERE `guild` = {$guild_id}");
    while ($r = $db->fetch_row($q))
    {
        $api->GameAddNotification($r['userid'], $notif);
    }
}