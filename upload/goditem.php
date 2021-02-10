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