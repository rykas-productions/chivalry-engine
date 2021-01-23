<?php
require('sglobals.php');
echo "<h3>Staff Boss</h3>";
if ($ir['user_level'] != "Admin") {
    alert('danger', "Uh Oh!", "You do not have permission to be here.", true, 'index.php');
    die($h->endpage());
}
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "addboss":
        addboss();
        break;
    case "delboss":
        delboss();
        break;
    default:
        alert('danger', "Uh Oh!", "Please select a valid action to perform.", true, 'index.php');
        die($h->endpage());
        break;
}

function addboss()
{
	global $db, $api, $h, $userid;
    if (isset($_POST['user'])) 
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_boss_add', stripslashes($_POST['verf'])))
		{
			alert('danger', "Action Blocked!", "Forms expire fairly quickly. Try again, but be quicker!");
            die($h->endpage());
		}
		$item = (isset($_POST['item']) && is_numeric($_POST['item'])) ? abs(intval($_POST['item'])) : 0;
		$user = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs(intval($_POST['user'])) : 0;
		$health = (isset($_POST['health']) && is_numeric($_POST['health'])) ? abs(intval($_POST['health'])) : 0;
		$multi = (isset($_POST['multi']) && is_numeric($_POST['multi'])) ? abs(intval($_POST['multi'])) : 100;
		$announce = (isset($_POST['announce']) && is_numeric($_POST['announce'])) ? abs(intval($_POST['announce'])) : 0;
		if (empty($item) || empty($user) || empty($health) || empty($multi)) 
		{
			alert('danger', "Uh Oh!", "Please fill out the form completely.");
			die($h->endpage());
		}
		$q = $db->query("/*qc=on*/SELECT `boss_id` FROM `activeBosses` WHERE `boss_user` = {$user}");
		if ($db->num_rows($q) > 0) 
		{
			$db->free_result($q);
			alert('danger', "Uh Oh!", "You cannot have the same boss listed twice.");
			die($h->endpage());
		}
		$db->free_result($q);
		$q = $db->fetch_single($db->query("/*qc=on*/SELECT `user_level` FROM `users` WHERE `userid` = {$user}"));
		if (!($q == 'NPC')) 
		{
			alert('danger', "Uh Oh!", "You cannot add a non-NPC as a boss.");
			die($h->endpage());
		}
		if (!$api->SystemItemIDtoName($item)) 
		{
			alert('danger', "Uh Oh!", "The item you've chosen for this boss to drop does not exist.");
			die($h->endpage());
		}
		if (($health < 100) || ($health > 1000000000))
		{
			alert('danger', "Uh Oh!", "Boss health must be at least 100, and at most, 1,000,000,000.");
			die($h->endpage());
		}
		if (($multi < 1) || ($multi > 1000000))
		{
			alert('danger', "Uh Oh!", "Boss stat multiplier must be at least 1% and at most, 1,000,000%.");
			die($h->endpage());
		}
		$db->query("INSERT INTO `activeBosses` 
					(`boss_user`, `boss_do_announce`, 
					`boss_stat_scale`, `boss_item_drop`) 
					VALUES ('{$user}', '{$announce}', '{$multi}', '{$item}')");
		$db->query("UPDATE `users` SET `hp` = {$health}, `maxhp` = {$health} WHERE `userid` = {$user}");
		alert('success',"Success!","You have successfully created a boss!!",true,'index.php');
		die($h->endpage());
	}
	else
	{
		$csrf = request_csrf_html('staff_boss_add');
		echo "<form method='post'>
		<div class='card'>
			<div class='row'>
				<div class='col-sm-3'>
					<b>Boss User</b>
				</div>
				<div class='col-sm'>
					" . user_dropdown('user') . "
				</div>
			</div>
			<hr />
			<div class='row'>
				<div class='col-sm-3'>
					<b>Boss Health</b>
				</div>
				<div class='col-sm'>
					<input type='number' min='100' max='1000000000' class='form-control' required='1' name='health' value='100000000'>
				</div>
			</div>
			<hr />
			<div class='row'>
				<div class='col-sm-3'>
					<b>Stat Multiplier %</b>
				</div>
				<div class='col-sm'>
					<input type='number' min='1' max='1000000' class='form-control' required='1' name='multi' value='100'>
				</div>
			</div>
			<hr />
			<div class='row'>
				<div class='col-sm-3'>
					<b>Boss Item Drop</b>
				</div>
				<div class='col-sm'>
					" . item_dropdown('item') . "
				</div>
			</div>
			<hr />
			<div class='row'>
				<div class='col-sm-3'>
					<b>Announce Death?</b>
				</div>
				<div class='col-sm'>
					<select name='announce' class='form-control' type='dropdown'>
						<option value='1'>True</option>
						<option value='0'>False</option>
					</select>
				</div>
			</div>
			<hr />
			<div class='row'>
				<div class='col-sm'>
					<input type='submit' class='btn btn-primary' value='Create Boss'>
				</div>
			</div>
		</div>
		{$csrf}
		</form>";
	}
}