<?php
header('Content-Type: application/json');
$hidehdr=true;
$time = time();
$last24hr=$time-86400;
$last15min=$time-(60*15);
require("../globals_nonauth.php");
header('Content-Type: application/json');
$return['status']='ok';
if (!isset($_GET['action'])) 
{
    $return['error']='Please set GET [action] to one of the values: [gameInfo] [userInfo]';
	$return['status']='error';
	echo json_encode($return);
	exit;
}
switch ($_GET['action']) 
{
    case 'gameInfo':
        gameInfo();
        break;
    case 'userInfo':
        userInfo();
        break;
	case 'herb':
		mine_item();
		break;
	case 'potion':
		potion();
		break;
}
function gameInfo()
{
	global $db, $return, $time, $last24hr, $last15min;
	
	$totalplayers=$db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users`"));
	$playersonline=$db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `laston` > {$last24hr}"));
	$playersonline15=$db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `laston` > {$last15min}"));
	$signups=$db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `registertime` > {$last24hr}"));
	$dung_count = $db->fetch_single($db->query("SELECT COUNT(`dungeon_user`) FROM `dungeon` WHERE `dungeon_out` > {$time}"));
	$infirm_count = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`infirmary_user`) FROM `infirmary` WHERE `infirmary_out` > {$time}"));
	
	$return['usersRegistered']=$totalplayers;
	$return['playersOnline24Hr']=$playersonline;
	$return['playersOnline15Min']=$playersonline15;
	$return['signUps']=$signups;
	$return['infirm']=$infirm_count;
	$return['dungeon']=$dung_count;
	
	//above this line tho man
	echo json_encode($return);
	exit;
}

function userInfo()
{
	global $db, $return, $time, $last24hr, $last15min;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
	if (empty($_GET['id']))
	{
		$return['error']='Please set GET [id] to the in-game user id of the player you wish to view.';
		$return['status']='error';
		echo json_encode($return);
		exit;
	}
	
	$userInfo = $db->fetch_row($db->query("SELECT `userid`, `username`, `level`, `gender`, `class`, 
	`laston`, `last_login`, `registertime`, `hp`, `maxhp`, `energy`, `maxenergy`, `brave`, `maxbrave`, 
	`primary_currency`, `vip_days`, `vipcolor`, `display_pic`, `guild`, `fedjail`, `location`, `kills`, 
	`deaths`, `busts`
	FROM `users`
	WHERE `userid` = {$_GET['id']}"));
	
	//above this line tho man
	echo json_encode($userInfo);
	exit;
}