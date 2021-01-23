<?php
/*
	File:		bomb.php
	Created: 	10/18/2017 at 10:49AM Eastern Time
	Info: 		Blow up your opponent.
	Author:		TheMasterGeneral
	Website: 	http://chivalryisdead.x10.mx/
*/
require('globals.php');
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'small':
        small();
        break;
	case 'medium':
        medium();
        break;
	case 'large':
        large();
        break;
    case 'rickroll':
        rickroll();
        break;
	case 'pumpkin':
        pumpkin();
        break;
    case 'snowball':
        snowball();
        break;
    case 'assassin':
        assassin();
        break;
	case 'defense':
        defense();
        break;
    default:
        alert('danger',"Uh Oh!","Please select the bomb you wish to use.",true,'inventory.php');
        break;
}
function small()
{
	global $db,$api,$userid,$h,$ir;
	$bombid = 28;
	$infirmarytime = Random(10, 60);
	$infirmaryreason = $db->escape("Bomb from <a href='profile.php?user={$userid}'>{$ir['username']}</a>");
	$notif = "<a href='profile.php?user={$userid}'>{$ir['username']}</a> bombed you, putting you in the infirmary for {$infirmarytime} minutes.";

	if (!$api->UserHasItem($userid, $bombid, 1)) {
		alert('danger', "Uh Oh!", "It appears you do not have the required item. If this is false, please contact an admin.", true, 'inventory.php');
		die($h->endpage());
	}
	if (isset($_POST['user'])) {
		$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;

		if (!isset($_POST['verf']) || !verify_csrf_code("bomb_form", stripslashes($_POST['verf']))) {
			alert('danger', "Action Blocked!", "Form requests expire fairly quickly. Go back and fill in the form faster next time.");
			die($h->endpage());
		}

		if (empty($_POST['user'])) {
			alert('danger', "Uh Oh!", "You did not fill out the form completely.", true, 'inventory.php');
			die($h->endpage());
		}
		if ($_POST['user'] == $userid) {
			alert('danger', "Uh Oh!", "You cannot bomb yourself.", true, 'inventory.php');
			die($h->endpage());
		}
		$q = $db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `userid` = {$_POST['user']}");
		if ($db->num_rows($q) == 0) {
			alert('danger', "Uh Oh!", "You are trying to bomb a user that does not exist.", true, 'inventory.php');
			die($h->endpage());
		}
		if (userHasEffect($_POST['user'], "basic_protection"))
		{
			alert('danger', "Uh Oh!", "You are trying to bomb a user that has protection.", true, 'inventory.php');
			die($h->endpage());
		}
		put_infirmary($_POST['user'], $infirmarytime, $infirmaryreason);
		$api->UserTakeItem($userid, $bombid, 1);
		$api->GameAddNotification($_POST['user'], $notif);
		$userbomb=$api->SystemUserIDtoName($_POST['user']);
		$db->query("UPDATE `users` SET `hp` = 0 WHERE `userid` = {$_POST['user']}");
		$ublink="<a href='../profile.php?user={$_POST['user']}'>{$userbomb}</a>";
		alert("success", "Success", "You have successfully bombed {$userbomb}.", true, 'inventory.php');
		$api->SystemLogsAdd($userid,'bomb',"Bombed {$ublink} for {$infirmarytime} minutes.");
		$h->endpage();

	} else {
		$csrf = request_csrf_html('bomb_form');
		echo "Please select the player whom you wish to bomb. They will be notified and put into the infirmary.<br />
		<form method='post'>
			" . user_dropdown('user',$userid) . "
			{$csrf}
			<input type='submit' value='Set Charge' class='btn btn-primary'>
		</form>";
		$h->endpage();
	}
}
function medium()
{
	global $db,$api,$userid,$h,$ir;
	$bombid = 61;
	$infirmarytime = Random(100, 600);
	$infirmaryreason = $db->escape("Bomb from <a href='profile.php?user={$userid}'>{$ir['username']}</a>");
	$notif = "<a href='profile.php?user={$userid}'>{$ir['username']}</a> bombed you, putting you in the infirmary for {$infirmarytime} minutes.";

	if (!$api->UserHasItem($userid, $bombid, 1)) {
		alert('danger', "Uh Oh!", "It appears you do not have the required item. If this is false, please contact an admin.", true, 'inventory.php');
		die($h->endpage());
	}
	if (isset($_POST['user'])) {
		$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;

		if (!isset($_POST['verf']) || !verify_csrf_code("bomb_form", stripslashes($_POST['verf']))) {
			alert('danger', "Action Blocked!", "Form requests expire fairly quickly. Go back and fill in the form faster next time.");
			die($h->endpage());
		}

		if (empty($_POST['user'])) {
			alert('danger', "Uh Oh!", "You did not fill out the form completely.", true, 'inventory.php');
			die($h->endpage());
		}
		if ($_POST['user'] == $userid) {
			alert('danger', "Uh Oh!", "You cannot bomb yourself.", true, 'inventory.php');
			die($h->endpage());
		}
		$q = $db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `userid` = {$_POST['user']}");
		if ($db->num_rows($q) == 0) {
			alert('danger', "Uh Oh!", "You are trying to bomb a user that does not exist.", true, 'inventory.php');
			die($h->endpage());
		}
		if (userHasEffect($_POST['user'], "basic_protection"))
		{
			alert('danger', "Uh Oh!", "You are trying to bomb a user that has protection.", true, 'inventory.php');
			die($h->endpage());
		}
		put_infirmary($_POST['user'], $infirmarytime, $infirmaryreason);
		$api->UserTakeItem($userid, $bombid, 1);
		$api->GameAddNotification($_POST['user'], $notif);
		$userbomb=$api->SystemUserIDtoName($_POST['user']);
		$db->query("UPDATE `users` SET `hp` = 0 WHERE `userid` = {$_POST['user']}");
		$ublink="<a href='../profile.php?user={$_POST['user']}'>{$userbomb}</a>";
		alert("success", "Success", "You have successfully bombed {$userbomb}.", true, 'inventory.php');
		$api->SystemLogsAdd($userid,'bomb',"Bombed {$ublink} for {$infirmarytime} minutes.");
		$h->endpage();

	} else {
		$csrf = request_csrf_html('bomb_form');
		echo "Please select the player whom you wish to bomb. They will be notified and put into the infirmary.<br />
		<form method='post'>
			" . user_dropdown('user',$userid) . "
			{$csrf}
			<input type='submit' value='Set Charge' class='btn btn-primary'>
		</form>";
		$h->endpage();
	}
}
function large()
{
	global $db,$api,$userid,$h,$ir;
	$bombid = 62;
	$infirmarytime = Random(7500, 50000);
	$infirmaryreason = $db->escape("Bomb from <a href='profile.php?user={$userid}'>{$ir['username']}</a>");
	$notif = "<a href='profile.php?user={$userid}'>{$ir['username']}</a> bombed you, putting you in the infirmary for {$infirmarytime} minutes.";

	if (!$api->UserHasItem($userid, $bombid, 1)) {
		alert('danger', "Uh Oh!", "It appears you do not have the required item. If this is false, please contact an admin.", true, 'inventory.php');
		die($h->endpage());
	}
	if (isset($_POST['user'])) {
		$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;

		if (!isset($_POST['verf']) || !verify_csrf_code("bomb_form", stripslashes($_POST['verf']))) {
			alert('danger', "Action Blocked!", "Form requests expire fairly quickly. Go back and fill in the form faster next time.");
			die($h->endpage());
		}
        if ($ir['lastbomb'] > time()-604800)
        {
            alert('danger', "Uh Oh!", "You may not use another large explosive so soon. Try again in " . TimeUntil_Parse($ir['lastbomb']+604800) . ".", true, 'inventory.php');
			die($h->endpage());
        }
		if (empty($_POST['user'])) {
			alert('danger', "Uh Oh!", "You did not fill out the form completely.", true, 'inventory.php');
			die($h->endpage());
		}
		if ($_POST['user'] == $userid) {
			alert('danger', "Uh Oh!", "You cannot bomb yourself.", true, 'inventory.php');
			die($h->endpage());
		}
		$q = $db->query("/*qc=on*/SELECT `userid`,`kills`,`hp`,`maxhp` FROM `users` WHERE `userid` = {$_POST['user']}");
		if ($db->num_rows($q) == 0) {
			alert('danger', "Uh Oh!", "You are trying to bomb a user that does not exist.", true, 'inventory.php');
			die($h->endpage());
		}
        $r=$db->fetch_row($q);
        if ($api->UserStatus($_POST['user'],'infirmary'))
        {
            alert('danger',"Uh Oh!","You may not use this bomb on players in the infirmary. The causalities would be too great.",true,'inventory.php');
            die($h->endpage());
        }
        if ($r['hp'] < $r['maxhp'])
        {
            alert('danger',"Uh Oh!","You may only use this bomb on players with full health.",true,'inventory.php');
            die($h->endpage());
        }
        if ($r['kills'] < 100)
        {
            alert('danger',"Uh Oh!","You cannot use this bomb on a player with less than 100 kills.",true,'inventory.php');
            die($h->endpage());
        }
        $time=time();
		put_infirmary($_POST['user'], $infirmarytime, $infirmaryreason);
		$api->UserTakeItem($userid, $bombid, 1);
		$api->GameAddNotification($_POST['user'], $notif);
		$userbomb=$api->SystemUserIDtoName($_POST['user']);
		$db->query("UPDATE `users` SET `hp` = 0 WHERE `userid` = {$_POST['user']}");
		$ublink="<a href='../profile.php?user={$_POST['user']}'>{$userbomb}</a>";
		alert("success", "Success", "You have successfully bombed {$userbomb}.", true, 'inventory.php');
		$api->SystemLogsAdd($userid,'bomb',"Bombed {$ublink} for {$infirmarytime} minutes.");
        $db->query("UPDATE `user_settings` SET `lastbomb` = {$time} WHERE `userid` = {$userid}");
		$h->endpage();

	} else {
		$csrf = request_csrf_html('bomb_form');
		echo "Please select the player whom you wish to bomb. They will be notified and put into the infirmary.<br />
		<form method='post'>
			" . user_dropdown('user',$userid) . "
			{$csrf}
			<input type='submit' value='Set Charge' class='btn btn-primary'>
		</form>";
		$h->endpage();
	}
}
function pumpkin()
{
	global $db,$api,$userid,$h,$ir;
	$bombid = 64;
	$infirmarytime = Random(3, 8);
	$infirmaryreason = $db->escape("Pumpkin from <a href='profile.php?user={$userid}'>{$ir['username']}</a>");
	$notif = "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has tossed a pumpkin at you, putting you in the infirmary for {$infirmarytime} minutes.";

	if (!$api->UserHasItem($userid, $bombid, 1)) {
		alert('danger', "Uh Oh!", "It appears you do not have the required item. If this is false, please contact an admin.", true, 'inventory.php');
		die($h->endpage());
	}
	if (isset($_POST['user'])) {
		$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;

		if (!isset($_POST['verf']) || !verify_csrf_code("bomb_form", stripslashes($_POST['verf']))) {
			alert('danger', "Action Blocked!", "Form requests expire fairly quickly. Go back and fill in the form faster next time.");
			die($h->endpage());
		}

		if (empty($_POST['user'])) {
			alert('danger', "Uh Oh!", "You did not fill out the form completely.", true, 'inventory.php');
			die($h->endpage());
		}
		if ($_POST['user'] == $userid) {
			alert('danger', "Uh Oh!", "You cannot toss a pumpkin at yourself.", true, 'inventory.php');
			die($h->endpage());
		}
		$q = $db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `userid` = {$_POST['user']}");
		if ($db->num_rows($q) == 0) {
			alert('danger', "Uh Oh!", "You are trying to bomb a user that does not exist.", true, 'inventory.php');
			die($h->endpage());
		}
		if (userHasEffect($_POST['user'], "basic_protection"))
		{
			alert('danger', "Uh Oh!", "You are trying to toss a pumpkin at a user that has protection.", true, 'inventory.php');
			die($h->endpage());
		}
		put_infirmary($_POST['user'], $infirmarytime, $infirmaryreason);
		$api->UserTakeItem($userid, $bombid, 1);
		$api->GameAddNotification($_POST['user'], $notif);
		$userbomb=$api->SystemUserIDtoName($_POST['user']);
		$ublink="<a href='../profile.php?user={$_POST['user']}'>{$userbomb}</a>";
		alert("success", "Success", "You have successfully tossed a pumpkin at {$userbomb}.", true, 'inventory.php');
		$api->SystemLogsAdd($userid,'bomb',"Bombed {$ublink} for {$infirmarytime} minutes.");
		$h->endpage();

	} else {
		$csrf = request_csrf_html('bomb_form');
		echo "Please select the player whom you wish to toss a pumpkin at. They will be notified and put into the infirmary.<br />
		<form method='post'>
			" . user_dropdown('user',$userid) . "
			{$csrf}
			<input type='submit' value='Toss Pumpkin' class='btn btn-primary'>
		</form>";
		$h->endpage();
	}
}
function snowball()
{
	global $db,$api,$userid,$h,$ir;
	$bombid = 202;
	$notif = "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has threw a snowball at you and it got under your armor. Brrr!";

	if (!$api->UserHasItem($userid, $bombid, 1)) {
		alert('danger', "Uh Oh!", "It appears you do not have the required item. If this is false, please contact an admin.", true, 'inventory.php');
		die($h->endpage());
	}
	if (isset($_POST['user'])) {
		$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;

		if (!isset($_POST['verf']) || !verify_csrf_code("bomb_form", stripslashes($_POST['verf']))) {
			alert('danger', "Action Blocked!", "Form requests expire fairly quickly. Go back and fill in the form faster next time.");
			die($h->endpage());
		}

		if (empty($_POST['user'])) {
			alert('danger', "Uh Oh!", "You did not fill out the form completely.", true, 'inventory.php');
			die($h->endpage());
		}
		if ($_POST['user'] == $userid) {
			alert('danger', "Uh Oh!", "You cannot toss a snowball at yourself.", true, 'inventory.php');
			die($h->endpage());
		}
		$q = $db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `userid` = {$_POST['user']}");
		if ($db->num_rows($q) == 0) {
			alert('danger', "Uh Oh!", "You are trying to throw a snowball at a user that does not exist.", true, 'inventory.php');
			die($h->endpage());
		}
		$api->UserTakeItem($userid, $bombid, 1);
		$api->GameAddNotification($_POST['user'], $notif);
		$userbomb=$api->SystemUserIDtoName($_POST['user']);
		$ublink="<a href='../profile.php?user={$_POST['user']}'>{$userbomb}</a>";
		alert("success", "Success", "You have successfully tossed a snowball at {$userbomb}.", true, 'inventory.php');
		$api->SystemLogsAdd($userid,'bomb',"Threw snowball at {$ublink}.");
		$h->endpage();

	} else {
		$csrf = request_csrf_html('bomb_form');
		echo "Please select the player whom you wish to toss a snowball at. Hope they don't get cold.<br />
		<form method='post'>
			" . user_dropdown('user',$userid) . "
			{$csrf}
			<input type='submit' value='Toss Snowball' class='btn btn-primary'>
		</form>";
		$h->endpage();
	}
}
function rickroll()
{
	global $db,$api,$userid,$h,$ir;
	$bombid = 149;
	$notif = "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has rick-rolled you.";

	if (!$api->UserHasItem($userid, $bombid, 1)) {
		alert('danger', "Uh Oh!", "It appears you do not have the required item. If this is false, please contact an admin.", true, 'inventory.php');
		die($h->endpage());
	}
	if (isset($_POST['user'])) {
		$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;

		if (!isset($_POST['verf']) || !verify_csrf_code("bomb_form", stripslashes($_POST['verf']))) {
			alert('danger', "Action Blocked!", "Form requests expire fairly quickly. Go back and fill in the form faster next time.");
			die($h->endpage());
		}

		if (empty($_POST['user'])) {
			alert('danger', "Uh Oh!", "You did not fill out the form completely.", true, 'inventory.php');
			die($h->endpage());
		}
		if ($_POST['user'] == $userid) {
			alert('danger', "Uh Oh!", "You cannot rick-roll yourself.", true, 'inventory.php');
			die($h->endpage());
		}
		$q = $db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `userid` = {$_POST['user']}");
		if ($db->num_rows($q) == 0) {
			alert('danger', "Uh Oh!", "You are trying to rick-roll a user that does not exist.", true, 'inventory.php');
			die($h->endpage());
		}
		if (userHasEffect($_POST['user'], "basic_protection"))
		{
			alert('danger', "Uh Oh!", "You are trying to rick-roll a user that has protection.", true, 'inventory.php');
			die($h->endpage());
		}
        if ($r['rickroll'] != 0)
        {
            alert('danger', "Uh Oh!", "This user already has a pending rick-roll. Try again later.", true, 'inventory.php');
			die($h->endpage());
        }
		$api->UserTakeItem($userid, $bombid, 1);
		$api->GameAddNotification($_POST['user'], $notif);
		$userbomb=$api->SystemUserIDtoName($_POST['user']);
		$db->query("UPDATE `user_settings` SET `rickroll` = {$userid} WHERE `userid` = {$_POST['user']}");
		$ublink="<a href='../profile.php?user={$_POST['user']}'>{$userbomb}</a>";
		alert("success", "Success", "You have successfully rick-rolled {$userbomb}. They will receive their torture on their next action.", true, 'inventory.php');
		$api->SystemLogsAdd($userid,'bomb',"Rick-rolled {$ublink}.");
		$h->endpage();

	} else {
		$csrf = request_csrf_html('bomb_form');
		echo "Please select the player whom you wish to rick-roll. They will receive their punishment on their next action.<br />
		<form method='post'>
			" . user_dropdown('user',$userid) . "
			{$csrf}
			<input type='submit' value='Rick-roll' class='btn btn-primary'>
		</form>";
		$h->endpage();
	}
}
function assassin()
{
	global $db,$api,$userid,$h,$ir;
	$bombid = 222;
	$infirmarytime = (Random(75, 175) + floor($ir['level'] / 2)*2);
	$infirmaryreason = $db->escape("Assassinated by <a href='profile.php?user={$userid}'>{$ir['username']}</a>");
	$notif = "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has assassinated you, putting you in the infirmary for {$infirmarytime} minutes.";

	if (!$api->UserHasItem($userid, $bombid, 1)) {
		alert('danger', "Uh Oh!", "It appears you do not have the required item. If this is false, please contact an admin.", true, 'inventory.php');
		die($h->endpage());
	}
	if (isset($_POST['user'])) {
		$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;

		if (!isset($_POST['verf']) || !verify_csrf_code("bomb_form", stripslashes($_POST['verf']))) {
			alert('danger', "Action Blocked!", "Form requests expire fairly quickly. Go back and fill in the form faster next time.");
			die($h->endpage());
		}
		if (empty($_POST['user'])) {
			alert('danger', "Uh Oh!", "You did not fill out the form completely.", true, 'inventory.php');
			die($h->endpage());
		}
		if ($_POST['user'] == $userid) {
			alert('danger', "Uh Oh!", "You cannot assassinate yourself.", true, 'inventory.php');
			die($h->endpage());
		}
		$q = $db->query("/*qc=on*/SELECT `userid`,`kills`,`hp`,`maxhp` FROM `users` WHERE `userid` = {$_POST['user']}");
		if ($db->num_rows($q) == 0) {
			alert('danger', "Uh Oh!", "You are trying to assassinate a user that does not exist.", true, 'inventory.php');
			die($h->endpage());
		}
        $r=$db->fetch_row($q);
        $time=time();
		put_infirmary($_POST['user'], $infirmarytime, $infirmaryreason);
		$api->UserTakeItem($userid, $bombid, 1);
		$api->GameAddNotification($_POST['user'], $notif);
		$userbomb=$api->SystemUserIDtoName($_POST['user']);
		$db->query("UPDATE `users` SET `hp` = 0 WHERE `userid` = {$_POST['user']}");
		$ublink="<a href='../profile.php?user={$_POST['user']}'>{$userbomb}</a>";
		alert("success", "Success", "You have successfully assassinated {$userbomb}. They received {$infirmarytime} minutes in the infirmary.", true, 'inventory.php');
		$api->SystemLogsAdd($userid,'bomb',"Assassinated {$ublink} for {$infirmarytime} minutes.");
		$h->endpage();

	} else {
		$csrf = request_csrf_html('bomb_form');
		echo "Select your mark...<br />
		<form method='post'>
			" . user_dropdown('user',$userid) . "
			{$csrf}
			<input type='submit' value='Assassinate' class='btn btn-primary'>
		</form>";
		$h->endpage();
	}
}

function defense()
{
	global $db,$api,$userid,$h,$ir;
	$bombid = 225;

	if (!$api->UserHasItem($userid, $bombid, 1)) {
		alert('danger', "Uh Oh!", "It appears you do not have a {$api->SystemItemIDtoName($bombid)}, which is required to use this form. If this is false, please contact an admin.", true, 'inventory.php');
		die($h->endpage());
	}
	if (isset($_POST['defense'])) {

		if (!isset($_POST['verf']) || !verify_csrf_code("bomb_form", stripslashes($_POST['verf']))) {
			alert('danger', "Action Blocked!", "Form requests expire fairly quickly. Go back and fill in the form faster next time.");
			die($h->endpage());
		}
		if (empty($_POST['defense'])) {
			alert('danger', "Uh Oh!", "You did not fill out the form completely.", true, 'inventory.php');
			die($h->endpage());
		}
		if ($ir['defense_bomb'] > 0)
		{
			alert('danger', "Uh Oh!", "You already have a defensive bomb set.", true, 'inventory.php');
			die($h->endpage());
		}
		$api->UserTakeItem($userid, $bombid, 1);
		$db->query("UPDATE `user_settings` SET `defense_bomb` = 1 WHERE `userid` = {$userid}");
		alert("success", "Success", "You have successfully set up a {$api->SystemItemIDtoName($bombid)}. The next person to attack you will trip it.", true, 'inventory.php');
		$api->SystemLogsAdd($userid,'bomb',"Set defensive bomb.");
		$h->endpage();

	} else {
		$csrf = request_csrf_html('bomb_form');
		echo "It appears you wish to setup a {$api->SystemItemIDtoName($bombid)} for your protection. Just confirm that below.<br />
		<form method='post'>
			<input type='hidden' name='defense' value='1'>
			{$csrf}
			<input type='submit' value='Setup {$api->SystemItemIDtoName($bombid)}' class='btn btn-primary'>
		</form>";
		$h->endpage();
	}
}