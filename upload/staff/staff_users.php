<?php
/*
	File: staff/staff_users.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: Allows admins to interact with users of the game.
	Author: TheMasterGeneral
	Website: http://mastergeneral156.pcriot.com/
*/
require('sglobals.php');
echo"<h3>Users</h3>";
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
	case "createuser":
		createuser();
		break;
	case "editperm":
		editperm();
		break;
	case "resetperm":
		resetperm();
		break;
	default:
		home();
		break;
}
function home()
{
	global $h,$lang;
	echo"
	<table class='table table-bordered'>
		<tr>
			<td>
				<a href='?action=createuser'>Create User</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='?action=edituser'>Edit User</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='?action=deleteuser'>Delete User</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='?action=credituser'>Credit User</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='?action=ipscan'>IP Look Up</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='?action=viewinventory'>View User's Inventory</a>
			</td>
		</tr>
	</table>";
}
function createuser()
{
	global $db,$h,$lang;
	if (!isset($_POST['username']))
	{
		$csrf=request_csrf_html('staff_user_1');
		echo "<hr /><h4>Creating a User</h4><hr />";
		echo "Fill out the form!";
		echo "
				<div id='usernameresult'></div>
				<div id='cpasswordresult'></div>
				<div id='emailresult'></div>
				<div id='teamresult'></div>
				<div id='statresult'></div>
			<table class='table table-bordered'>
				<form method='post'>
					<tr>
						<th>
							Username
						</th>
						<td>
							<input type='text' id='username' required='1' onkeyup='CheckUsername(this.value);' class='form-control' minlength='3' name='username' maxlength='20'>
						</td>
					</tr>
					<tr>
						<th>
							Password
						</th>
						<td>
							<input type='password' id='pw1' required='1' class='form-control' onkeyup='CheckPasswords(this.value);PasswordMatch();' name='password'>
						</td>
					</tr>
					<tr>
						<th>
							Confirm Password
						</th>
						<td>
							<input type='password' id='pw2' required='1' class='form-control' onkeyup='PasswordMatch();' name='cpw'>
						</td>
					</tr>
					<tr>
						<th>
							Email
						</th>
						<td>
							<input type='email' id='email' required='1' class='form-control' onkeyup='CheckEmail(this.value);' name='email'>
						</td>
					</tr>
					<tr>
						<th>
							User Level
						</th>
						<td>
							<select name='userlevel' class='form-control' required='1' type='dropdown'>
								<option>NPC</option>
								<option>Member</option>
								<option>Admin</option>
								<option>Forum Moderator</option>
								<option>Assistant</option>
								<option>Web Developer</option>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<b>Basic Information</b>
						</td>
					</tr>
					<tr>
						<th>
							Level
						</th>
						<td>
							<input type='number' required='1' class='form-control' min='1' name='level' value='1'>
						</td>
					</tr>
					<tr>
						<th>
							Gender
						</th>
						<td>
							<select name='gender' class='form-control' required='1' type='dropdown'>
								<option>Male</option>
								<option>Female</option>
							</select>
						</td>
					</tr>
					<tr>
						<th>
							Class
						</th>
						<td>
							<select name='class' id='class' class='form-control' onchange='UpdateStats(this.value)' required='1' type='dropdown'>
								<option value='Warrior'>Warrior</option>
								<option value='Rogue'>Rogue</option>
								<option value='Defender'>Defender</option>
							</select>
						</td>
					</tr>
					<tr>
						<th>
							Primary Currency
						</th>
						<td>
							<input type='number' required='1' class='form-control' min='0' name='prim_currency' value='100'>
						</td>
					</tr>
					<tr>
						<th>
							Secondary Currency
						</th>
						<td>
							<input type='number' required='1' class='form-control' min='0' value='0' name='sec_currency'>
						</td>
					</tr>
					<tr>
						<th>
							VIP Days
						</th>
						<td>
							<input type='number' required='1' class='form-control' min='0' value='0' name='vip_days'>
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<b>Stats</b>
						</td>
					</tr>
					<tr>
						<th>
							Strength
						</th>
						<td>
							<input type='number' required='1' id='strength' class='form-control' min='10' name='strength' value='1100'>
						</td>
					</tr>
					<tr>
						<th>
							Agility
						</th>
						<td>
							<input type='number' required='1' id='agility' class='form-control' min='10' name='agility' value='1000'>
						</td>
					</tr>
					<tr>
						<th>
							Guard
						</th>
						<td>
							<input type='number' required='1' id='guard' class='form-control' min='10' name='guard' value='900'>
						</td>
					</tr>
					<tr>
						<th>
							Labor
						</th>
						<td>
							<input type='number' required='1' class='form-control' min='10' name='labor' value='1000'>
						</td>
					</tr>
					<tr>
						<th>
							IQ
						</th>
						<td>
							<input type='number' required='1' class='form-control' min='10' name='iq' value='1000'>
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<b>Other</b>
						</td>
					</tr>
					<tr>
						<th>
							City
						</th>
						<td>
							" . location_dropdown("city") . "
						</td>
					</tr>
					<tr>
						<th>
							Primary Weapon
						</th>
						<td>
							" . weapon_dropdown("primary_weapon",0) . "
						</td>
					</tr>
					<tr>
						<th>
							Secondary Weapon
						</th>
						<td>
							" . weapon_dropdown("secondary_weapon",0) . "
						</td>
					</tr>
					<tr>
						<th>
							Armor
						</th>
						<td>
							" . armor_dropdown("armor",0) . "
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<input type='submit' class='btn btn-default' value='Create User!' />
						</td>
					</tr>
        	{$csrf}
				</form>
			</table>";
	}
	else
	{
		staff_csrf_stdverify('staff_user_1', '?action=createuser');
		$username = (isset($_POST['username']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $_POST['username']) && ((strlen($_POST['username']) < 20) && (strlen($_POST['username']) >= 3))) ? stripslashes($_POST['username']) : '';
		$pw = (isset($_POST['password']) && is_string($_POST['password'])) ? stripslashes($_POST['password']) : '';
		$pw2 = (isset($_POST['cpw']) && is_string($_POST['cpw'])) ? stripslashes($_POST['cpw']) : '';
		
		$_POST['level'] = (isset($_POST['level']) && is_numeric($_POST['level'])) ? abs(intval($_POST['level'])) : 1;
		$Money = (isset($_POST['prim_currency']) && is_numeric($_POST['prim_currency'])) ? abs(intval($_POST['prim_currency'])) : 100;
		$Money2 = (isset($_POST['sec_currency']) && is_numeric($_POST['sec_currency'])) ? abs(intval($_POST['sec_currency'])) : 0;
		$VIP = (isset($_POST['vip_days']) && is_numeric($_POST['vip_days'])) ? abs(intval($_POST['vip_days'])) : 0;
		$Strength = (isset($_POST['strength']) && is_numeric($_POST['strength'])) ? abs(intval($_POST['strength'])) : 1100;
		$Agility = (isset($_POST['agility']) && is_numeric($_POST['agility'])) ? abs(intval($_POST['agility'])) : 1000;
		$Guard = (isset($_POST['guard']) && is_numeric($_POST['guard'])) ? abs(intval($_POST['guard'])) : 900;
		$Labor = (isset($_POST['labor']) && is_numeric($_POST['labor'])) ? abs(intval($_POST['labor'])) : 1000;
		$IQ = (isset($_POST['iq']) && is_numeric($_POST['iq'])) ? abs(intval($_POST['iq'])) : 1000;
		
		$equip_prim=(isset($_POST['primary_weapon']) && is_numeric($_POST['primary_weapon'])) ? abs(intval($_POST['primary_weapon'])) : 0;
		$equip_sec=(isset($_POST['secondary_weapon']) && is_numeric($_POST['secondary_weapon'])) ? abs(intval($_POST['secondary_weapon'])) : 0;
		$equip_armor=(isset($_POST['armor']) && is_numeric($_POST['armor'])) ? abs(intval($_POST['armor'])) : 0;
		$city=(isset($_POST['city']) && is_numeric($_POST['city'])) ? abs(intval($_POST['city'])) : 1;
		
		if (!isset($_POST['email']) || !valid_email(stripslashes($_POST['email'])))
		{
			echo "<div class='alert alert-danger' role='alert'><strong>Error!</strong> The email you entered is invalid or does not exist.</div>";
			$h->endpage();
			exit;
		}
		if (!isset($_POST['gender']) || ($_POST['gender'] != 'Male' && $_POST['gender'] != 'Female'))
		{
			echo "<div class='alert alert-danger' role='alert'><strong>Error!</strong> You specified an invalid gender. A user can only be male or female.</div>";
			$h->endpage();
			exit;
		}
		if (!isset($_POST['class']) || ($_POST['class'] != 'Warrior' && $_POST['class'] != 'Rogue' && $_POST['class'] != 'Defender'))
		{
			echo "<div class='alert alert-danger' role='alert'><strong>Error!</strong> You specified an invalid class. A user can only be a warrior, rogue, or a defender.</div>";
			$h->endpage();
			exit;
		}
		if (!isset($_POST['userlevel']) || ($_POST['userlevel'] != 'NPC' && $_POST['userlevel'] != 'Member' && $_POST['userlevel'] != 'Admin' && $_POST['userlevel'] != 'Forum Moderator' && $_POST['userlevel'] != 'Assistant' && $_POST['userlevel'] != 'Web Developer'))
		{
			echo "<div class='alert alert-danger' role='alert'><strong>Error!</strong> You specified an invalid user level. A user can only be an NPC, Admin, Member, Forum Moderator, Assistant, or Web Developer.</div>";
			$h->endpage();
			exit;
		}
		if ($equip_prim > 0)
		{
			$pwq=$db->query("SELECT COUNT(`itmid`) FROM `items` WHERE `itmid` = '{$equip_prim}' AND `weapon` > 0");
			if ($db->fetch_single($pwq) == 0)
			{
				echo "<div class='alert alert-danger' role='alert'><strong>Error!</strong> The weapon you chose either does not exist, or cannot be equipped as a weapon.</div>";
				$h->endpage();
				exit;
			}
		}
		if ($equip_sec > 0)
		{
			$swq=$db->query("SELECT COUNT(`itmid`) FROM `items` WHERE `itmid` = '{$equip_sec}' AND `weapon` > 0");
			if ($db->fetch_single($swq) == 0)
			{
				echo "<div class='alert alert-danger' role='alert'><strong>Error!</strong> The weapon you chose either does not exist, or cannot be equipped as a weapon.</div>";
				$h->endpage();
				exit;
			}
		}
		if ($equip_armor > 0)
		{
			$aq=$db->query("SELECT COUNT(`itmid`) FROM `items` WHERE `itmid` = '{$equip_armor}' AND `armor` > 0");
			if ($db->fetch_single($aq) == 0)
			{
				echo "<div class='alert alert-danger' role='alert'><strong>Error!</strong> The armor you chose either does not exist, or cannot be equipped as a armor.</div>";
				$h->endpage();
				exit;
			}
		}
		$CityQuery=$db->query("SELECT COUNT(`town_id`) FROM `town` WHERE `town_id` = {$city}");
		if ($db->fetch_single($CityQuery) == 0)
		{
			echo "<div class='alert alert-danger' role='alert'><strong>Error!</strong> The town you chose does not exist.</div>";
			$h->endpage();
			exit;
		}
		$e_gender = $db->escape(stripslashes($_POST['gender']));
		$e_class = $db->escape(stripslashes($_POST['class']));
		$e_username = $db->escape($username);
		$e_email = $db->escape(stripslashes($_POST['email']));
		$q =
            $db->query(
                    "SELECT COUNT(`userid`)
                     FROM `users`
                     WHERE `username` = '{$e_username}'");
		$q2 =
            $db->query(
                    "SELECT COUNT(`userid`)
    				 FROM `users`
    				 WHERE `email` = '{$e_email}'");
		$u_check = $db->fetch_single($q);
		$e_check = $db->fetch_single($q2);
		$db->free_result($q);
		$db->free_result($q2);
		if ($u_check > 0)
		{
			echo "<div class='alert alert-danger' role='alert'><strong>Error!</strong> Username in use. Please go back and try again?</div>";
		}
		else if ($e_check > 0)
		{
			echo "<div class='alert alert-danger' role='alert'><strong>Error!</strong> Email in use. Please go back and enter a new email address.</div>";
		}
		else if (empty($pw) || empty($pw2))
		{
			echo "<div class='alert alert-danger' role='alert'><strong>Error!</strong> You must enter a password and confirm it.</div>";
		}
		else if ($pw != $pw2)
		{
			echo "<div class='alert alert-danger' role='alert'><strong>Error!</strong> Passwords did not match. Go back and try again.</div>";
		}
		else
		{
			$HP=(50)+$_POST['level']*50;
			$Energy=(20)+$_POST['level']*4;
			$Brave=(6)+$_POST['level']*4;
			$time=time();
			$encpsw = encode_password($pw);
			$e_encpsw = $db->escape($encpsw);
			$db->query("INSERT INTO `users` 
			(`userid`, `username`, `user_level`, `email`, `password`, `level`, 
			`xp`, `gender`, `class`, `lastip`, `loginip`, `registerip`, `laston`, `last_login`, 
			`registertime`, `will`, `maxwill`, `hp`, `maxhp`, `energy`, `maxenergy`, `brave`, 
			`maxbrave`, `primary_currency`, `secondary_currency`, `bank`, `attacking`, 
			`vip_days`, `force_logout`, `display_pic`, `personal_notes`, `announcements`, `equip_primary`, 
			`equip_secondary`, `equip_armor`, `guild`, `fedjail`, `staff_notes`, `location`, `timezone`) 
			VALUES 
			(NULL, '{$e_username}', '{$_POST['userlevel']}', '{$e_email}', '{$e_encpsw}', '{$_POST['level']}', '0', '{$_POST['gender']}', 
			'{$_POST['class']}', '127.0.0.1', '', '127.0.0.1', '', '', '{$time}', '100', '100', '{$HP}', '{$HP}', '{$Energy}', '{$Energy}', '{$Brave}', '{$Brave}', 
			 '{$Money}', '{$Money2}', '-1', '0', '{$VIP}', 'false', '', '', '', '{$equip_prim}', '{$equip_sec}', '{$equip_armor}', '0', '0', '', '{$city}', 'Europe/London');");
			 $i = $db->insert_id();
			 $db->query("INSERT INTO `userstats` VALUES($i, {$Strength}, {$Agility}, {$Guard}, {$IQ}, {$Labor})");
			 $db->query("INSERT INTO `infirmary` (`infirmary_user`, `infirmary_reason`, `infirmary_in`, `infirmary_out`) VALUES ('{$i}', 'N/A', '0', '0');");
			$db->query("INSERT INTO `dungeon` (`dungeon_user`, `dungeon_reason`, `dungeon_in`, `dungeon_out`) VALUES ('{$i}', 'N/A', '0', '0');");
			echo "<div class='alert alert-success' role='alert'><strong>Success!</strong> You have successfully created a user!</div>";
			stafflog_add("Created user <a href='../profile.php?user={$i}'>{$e_username}</a>.");
		}
	}
	
}
$h->endpage();