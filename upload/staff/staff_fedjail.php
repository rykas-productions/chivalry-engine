<?php
require('sglobals.php');
//Check for proper staff privledges
if ($api->UserMemberLevelGet($userid, 'assistant') == false) {
    alert('danger', "Uh Oh!", "You do not have permission to be here.", true, 'index.php');
    die($h->endpage());
}
//Set the GET
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
//Cycle through the actions available for GET
switch ($_GET['action']) {
    case "viewappeal":
        viewappeal();
        break;
    case "respondappeal":
        respondappeal();
        break;
    case "pardon":
        pardon();
        break;
    default:
        alert('danger', "Uh Oh!", "Please select a valid action to perform.", true, 'index.php');
        die($h->endpage());
        break;
}
function viewappeal()
{
	global $db,$userid,$api,$h;
	echo "<h3>Viewing Fedjail Appeals</h3><hr />";
	$q=$db->query("SELECT * FROM `fedjail_appeals` ORDER BY `fja_time` DESC");
	echo "<table class='table table-bordered'>
	<tr>
		<thead>
			<th>
				Case
			</th>
			<th>
				Responder
			</th>
			<th>
				Time
			</th>
			<th>
				Response
			</th>
			<th>
				Actions
			</th>
		</thead>
	</tr>";
	while ($r = $db->fetch_row($q))
	{
		if ($r['fja_responder'] != $r['fja_user'])
			$responder="Staff";
		else
			$responder=$api->SystemUserIDtoName($r['fja_user']);
		echo "<tr>
		<td>
			<a href='../profile.php?user={$r['fja_user']}'>{$api->SystemUserIDtoName($r['fja_user'])}</a> [{$r['fja_user']}]
		</td>
		<td>
			{$responder}
		</td>
		<td>
			" . DateTime_Parse($r['fja_time']) . "
		</td>
		<td>
			{$r['fja_text']}
		</td>
		<td>
			[<a href='?action=respondappeal&case={$r['fja_user']}'>Respond</a>]<br />
			[<a href='?action=pardon&case={$r['fja_user']}'>Pardon</a>]
		</td>
		</tr>";
	}
	echo"</table>";
	$h->endpage();
}
function respondappeal()
{
	global $db,$api,$userid,$h;
	$_GET['case'] = (isset($_GET['case']) && is_numeric($_GET['case'])) ? abs($_GET['case']) : 0;
	if (empty($_GET['case']))
	{
		alert('danger',"Uh Oh!","Please select the case you wish to respond to.",true,'?action=viewappeal');
		die($h->endpage());
	}
	$q=$db->query("SELECT * FROM `fedjail` WHERE `fed_userid` = {$_GET['case']}");
	if ($db->num_rows($q) == 1)
	{
		alert('danger',"Uh Oh!","The user you're trying to respond to does not exist, or is not in federal dungeon.",true,'?action=viewappeal');
		die($h->endpage());
	}
	$q2=$db->query("SELECT * FROM `fedjail_appeals` WHERE `fja_user` = {$_GET['case']} ORDER BY `fja_time` DESC LIMIT 1");
	$r=$db->fetch_row($q2);
	if (isset($_POST['response']))
	{
		$msg = $db->escape(stripslashes($_POST['response']));
		$time=time();
		$db->query("INSERT INTO `fedjail_appeals` (`fja_user`, `fja_responder`, `fja_text`, `fja_time`) VALUES ('{$_GET['case']}', '{$userid}', '{$msg}', '{$time}')");
		alert('success',"Success!","Response posted successfully.",true,'?action=viewappeal');
		die($h->endpage());
	}
	else
	{
		echo "Their Response: {$r['fja_text']}<br />
		Enter your response here.<br />
		<form method='post'>
			<input type='hidden' value='{$_GET['case']}' name='case'>
			<input type='text' required='1' name='response' class='form-control'>
			<input type='submit' name='Submit Response' class='btn btn-primary'>
		</form>";
		$h->endpage();
	}
}
function pardon()
{
	global $db,$api,$userid,$h;
	$_GET['case'] = (isset($_GET['case']) && is_numeric($_GET['case'])) ? abs($_GET['case']) : 0;
	if (empty($_GET['case']))
	{
		alert('danger',"Uh Oh!","Please select the case you wish to respond to.",true,'?action=viewappeal');
		die($h->endpage());
	}
	$q=$db->query("SELECT * FROM `fedjail` WHERE `fed_userid` = {$_GET['case']}");
	if ($db->num_rows($q) == 1)
	{
		alert('danger',"Uh Oh!","The user you're trying to respond to does not exist, or is not in federal dungeon.",true,'?action=viewappeal');
		die($h->endpage());
	}
	$q2=$db->query("SELECT * FROM `fedjail_appeals` WHERE `fja_user` = {$_GET['case']} ORDER BY `fja_time` DESC LIMIT 1");
	$threedays=time()+259200;
	$db->query("UPDATE `fedjail` SET `fed_out` = {$threedays} WHERE `fed_userid` = {$_GET['case']}");
	alert('success',"Success!","User pardoned successfully. They will be let out in 3 days.",true,'?action=viewappeal');
	die($h->endpage());
}