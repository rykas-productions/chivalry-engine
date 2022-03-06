<?php
require('globals.php');
if (!$api->UserMemberLevelGet($userid, "admin"))
{
    alert('danger',"Uh Oh!","You need to be an admin rank or better or use this item.",true,'inventory.php');
    die($h->endpage());
}
if (!($api->UserHasItem($userid,320,1)))
{
    alert('danger',"Uh Oh!","You do not have a {$api->SystemItemIDtoName(320)} to be here.",true,'inventory.php');
    die($h->endpage());
}
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'level':
        level();
        break;
    case 'backupdb':
        backupdb();
        break;
    case 'bankint':
        bankinterest();
        break;
    case 'givemoves':
        giveMoves();
        break;
    case 'givenpctroops':
        giveNpcTroops();
        break;
    case 'giveyoutroops':
        giveTroops();
        break;
	case 'fixsql':
		fixSQLError();
		break;
    default:
        home();
        break;
}
function home()
{
	echo "<h3>Scroll of the Adminly</h3><hr />
    <div class='row'>
        <div class='col'>
            <a href='?action=level' class='btn btn-primary btn-block'>Set Level</a>
        </div>
        <div class='col'>
            <a href='?action=backupdb' class='btn btn-danger btn-block'>Manual Database Backup</a>
        </div>
        <div class='col'>
            <a href='?action=bankint' class='btn btn-primary btn-block'>Run Bank Interest</a>
        </div>
        <div class='col'>
            <a href='?action=givemoves' class='btn btn-primary btn-block'>Give District Moves</a>
        </div>
        <div class='col'>
            <a href='?action=giveyoutroops' class='btn btn-primary btn-block'>Give Guild Troops</a>
        </div>
        <div class='col'>
            <a href='?action=givenpctroops' class='btn btn-primary btn-block'>Give NPC Guild Troops</a>
        </div>
		<div class='col'>
            <a href='?action=fixsql' class='btn btn-primary btn-block'>Fix SQL Error</a>
        </div>
    </div>
	";
}

function level()
{
	global $userid, $db, $ir, $api;
	if (isset($_POST['level']))
	{
	    $_POST['level'] = (isset($_POST['level']) && is_numeric($_POST['level'])) ? abs($_POST['level']) : $ir['level'];
	    $db->query("UPDATE `users` SET `level` = {$_POST['level']} WHERE `userid` = {$userid}");
	    alert("success","Success!","You have set your level to {$_POST['level']}.",false);
	    $api->SystemLogsAdd($userid, "staff", "Set account level to {$_POST['level']}.");
	    home();
	}
	else
	{
		echo "<form method='post'>
			<input type='number' required='1' class='form-control' value='{$ir['level']}' name='level'>
			<input type='submit' class='btn btn-primary' value='Change Level'>
		</form>";
	}
}
function backupdb()
{
    global $api, $userid;
    backupDatabase();
    alert("success","Success!","You have successfully created a backup of the game's database.",false);
    $api->SystemLogsAdd($userid, "staff", "Manually backed-up database.");
    home();
}

function bankinterest()
{
    global $api, $userid;
    doDailyBankInterest();
    doDailyFedBankInterest();
    doDailyVaultBankInterest();
    alert("success","Success!","You have successfully ran interest on all bank accounts.",false);
    $api->SystemLogsAdd($userid, "staff", "Manually ran interest on bank accounts.");
    home();
}

function giveMoves()
{
    global $api, $userid, $db, $ir;
    $db->query("UPDATE `guild_district_info` SET `moves` = `moves` + 2 WHERE `guild_id` = {$ir['guild']}");
    alert("success","Success!","You have given your guild 2 more district moves.",false);
    $api->SystemLogsAdd($userid, "staff", "Gave guild +2 district moves.");
    home();
}
function giveTroops()
{
    global $api, $userid, $db, $ir;
    $db->query("UPDATE `guild_district_info` 
                SET `barracks_warriors` = `barracks_warriors` + 200000,
                `barracks_archers` = `barracks_archers` + 100000,
                `barracks_generals` = `barracks_generals` + 100,
                `barracks_captains` = `barracks_captains` + 200
                 WHERE `guild_id` = {$ir['guild']}");
    alert("success","Success!","You have your guild 200k Warriors, 100k Archers, 200 Captains and 100 Generals.",false);
    $api->SystemLogsAdd($userid, "staff", "Gave their guild 200k/100k Warriors/Archers and  100/200 Generals/Captains.");
    home();
}

function giveNpcTroops()
{
    global $api, $userid, $db, $ir;
    $db->query("UPDATE `guild_district_info`
                SET `barracks_warriors` = `barracks_warriors` + 200000,
                `barracks_archers` = `barracks_archers` + 100000,
                `barracks_generals` = `barracks_generals` + 100,
                `barracks_captains` = `barracks_captains` + 200
                 WHERE `guild_id` = 16");
    alert("success","Success!","You have the NPC guild 200k Warriors, 100k Archers, 200 Captains and 100 Generals.",false);
    $api->SystemLogsAdd($userid, "staff", "Gave NPC guild 200k/100k Warriors/Archers and  100/200 Generals/Captains.");
    home();
}
function fixSQLError()
{
	global $db, $api, $userid;
	$db->query("set GLOBAL sql_mode='ONLY_FULL_GROUP_BY,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'");
	alert("success","Success!","You have fixed the SQL errors...",false);
    $api->SystemLogsAdd($userid, "staff", "Fixed SQL");
    home();
}