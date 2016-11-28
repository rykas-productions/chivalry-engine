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
	case "edituser":
		edituser();
		break;
	case "deleteuser":
		deleteuser();
		break;
	default:
		die();
		break;
}
function createuser()
{
	global $db,$h,$lang,$api,$userid;
	if (!isset($_POST['username']))
	{
		$csrf=request_csrf_html('staff_user_1');
		echo "<hr /><h4>Creating a User</h4><hr />";
		echo "Fill out the form!";
		echo "
			<table class='table table-bordered'>
				<form method='post'>
					<tr>
						<th>
							Username
						</th>
						<td>
							<input type='text' id='username' required='1' class='form-control' minlength='3' name='username' maxlength='20'>
						</td>
					</tr>
					<tr>
						<th>
							Password
						</th>
						<td>
							<input type='password' id='pw1' required='1' class='form-control' name='password'>
						</td>
					</tr>
					<tr>
						<th>
							Confirm Password
						</th>
						<td>
							<input type='password' id='pw2' required='1' class='form-control' name='cpw'>
						</td>
					</tr>
					<tr>
						<th>
							Email
						</th>
						<td>
							<input type='email' id='email' required='1' class='form-control' name='email'>
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
							<select name='class' id='class' class='form-control' required='1' type='dropdown'>
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
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_user_1', stripslashes($_POST['verf'])))
		{
			alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
			die($h->endpage());
		}
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
			$api->SystemLogsAdd($userid,'staff',"Created user <a href='../profile.php?user={$i}'>{$e_username}</a>.");
		}
	}
}
function edituser()
{
	global $db,$lang,$h,$userid,$api;
	if (!isset($_POST['step']))
	{
		$_POST['step'] = 0;
	}
	if ($_POST['step'] == 2)
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_edituser1', stripslashes($_POST['verf'])))
		{
			alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
			die($h->endpage());
		}
		$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs(intval($_POST['user'])) : 0;
		if (empty($_POST['user']))
		{
			alert('danger',"{$lang['ERROR_EMPTY']}","{$lang['STAFF_USERS_EDIT_EMPTY']}");
			die($h->endpage());
		}
		$d =  $db->query("SELECT `i`.*, `d`.*, `username`, 
		`level`, `primary_currency`,`secondary_currency`, `equip_primary`,
		`maxwill`, `bank`, `strength`, `agility`, `guard`, `equip_secondary`,
		`labor`, `IQ`, `location`, `equip_armor`, `email`
		 FROM `users` AS `u`
		 INNER JOIN `userstats` AS `us`
		 ON `u`.`userid` = `us`.`userid`
		 INNER JOIN `dungeon` AS `d`
		 ON `u`.`userid` = `d`.`dungeon_user`
		 INNER JOIN `infirmary` AS `i`
		 ON `u`.`userid` = `i`.`infirmary_user`
		 WHERE `u`.`userid` = {$_POST['user']}");
		if ($db->num_rows($d) == 0)
		{
			$db->free_result($d);
			alert('danger',"{$lang['ERROR_NONUSER']}","{$lang['STAFF_USERS_EDIT_DND']}");
			die($h->endpage());
		}
		$itemi = $db->fetch_row($d);
		$db->free_result($d);
		$CurrentTime=time();
		$itemi['hospreason'] = htmlentities($itemi['infirmary_reason'], ENT_QUOTES, 'ISO-8859-1');
		$itemi['email'] = htmlentities($itemi['email'], ENT_QUOTES, 'ISO-8859-1');
		$itemi['jail_reason'] = htmlentities($itemi['dungeon_reason'], ENT_QUOTES, 'ISO-8859-1');
		$itemi['username'] = htmlentities($itemi['username'], ENT_QUOTES, 'ISO-8859-1');
		$itemi['infirmary']= round(($itemi['infirmary_out'] - $CurrentTime) / 60);
		$itemi['dungeon']= round(($itemi['dungeon_out'] - $CurrentTime) / 60);
		if ($itemi['infirmary'] < 0) { $itemi['infirmary'] = 0; }
		if ($itemi['dungeon'] < 0) { $itemi['dungeon'] = 0; }
		$csrf = request_csrf_html('staff_edituser2');
		echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					{$lang['STAFF_USERS_EDIT_FORMTITLE']}
					<input type='hidden' name='userid' value='{$_POST['user']}' />
					<input type='hidden' name='step' value='3' />
				</th>
			</tr>
			<tr>
				<th width='33%'>
					{$lang["REG_USERNAME"]}
				</th>
				<td>
					<input type='text' class='form-control' required='1' name='username' value='{$itemi['username']}' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang["REG_EMAIL"]}
				</th>
				<td>
					<input type='text' class='form-control' required='1' name='email' value='{$itemi['email']}' />
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
				<th>
					{$lang['INDEX_LEVEL']}
				</th>
				<td>
					<input type='number' min='1' class='form-control' required='1' name='level' value='{$itemi['level']}' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['INDEX_PRIMCURR']}
				</th>
				<td>
					<input type='number' min='1' class='form-control' required='1' name='prim_currency' value='{$itemi['primary_currency']}' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['EXPLORE_BANK']}
				</th>
				<td>
					<input type='number' min='-1' class='form-control' required='1' name='bank' value='{$itemi['bank']}' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['INDEX_SECCURR']}
				</th>
				<td>
					<input type='number' min='0' class='form-control' required='1' name='sec_currency' value='{$itemi['secondary_currency']}' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_USERS_EDIT_FORM_INFIRM']}
				</th>
				<td>
					<input type='number' min='0' class='form-control' required='1' name='infirmary' value='{$itemi['infirmary']}' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_USERS_EDIT_FORM_INFIRM_REAS']}
				</th>
				<td>
					<input type='text' class='form-control' name='infirmary_reason' value='{$itemi['infirmary_reason']}' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_USERS_EDIT_FORM_DUNG']}
				</th>
				<td>
					<input type='number' min='0' class='form-control' required='1' name='dungeon' value='{$itemi['dungeon']}' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_USERS_EDIT_FORM_DUNG_REAS']}
				</th>
				<td>
					<input type='text' class='form-control' name='dungeonreason' value='{$itemi['dungeon_reason']}' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_USERS_EDIT_FORM_ESTATE']}
				</th>
				<td>
					" . house2_dropdown("maxwill", $itemi['maxwill']) . "
				</td>
			</tr>
			<tr>
				<th colspan='2'>
					{$lang['STAFF_USERS_EDIT_FORM_STATS']}
				</th>
			</tr>
			<tr>
				<th>
					{$lang['GEN_STR']}
				</th>
				<td>
					<input type='number' min='1' class='form-control' required='1' name='strength' value='{$itemi['strength']}' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['GEN_AGL']}
				</th>
				<td>
					<input type='number' min='1' class='form-control' required='1' name='agility' value='{$itemi['agility']}' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['GEN_GRD']}
				</th>
				<td>
					<input type='number' min='1' class='form-control' required='1' name='guard' value='{$itemi['guard']}' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['GEN_LAB']}
				</th>
				<td>
					<input type='number' min='1' class='form-control' required='1' name='labour' value='{$itemi['labor']}' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['GEN_IQ']}
				</th>
				<td>
					<input type='number' min='1' class='form-control' required='1' name='IQ' value='{$itemi['IQ']}' />
				</td>
			</tr>
			<tr>
				<th colspan='2'>
					Other
				</th>
			</tr>
			<tr>
				<th>
					City
				</th>
				<td>
					" . location_dropdown("city", $itemi['location']) . "
				</td>
			</tr>
			<tr>
				<th>
					Primary Weapon
				</th>
				<td>
					" . weapon_dropdown("primary_weapon",$itemi['equip_primary']) . "
				</td>
			</tr>
			<tr>
				<th>
					Secondary Weapon
				</th>
				<td>
					" . weapon_dropdown("secondary_weapon",$itemi['equip_secondary']) . "
				</td>
			</tr>
			<tr>
				<th>
					Armor
				</th>
				<td>
					" . armor_dropdown("armor",$itemi['equip_armor']) . "
				</td>
			</tr>
		</table>
    	{$csrf}
    	<input class='btn btn-default' type='submit' value='Edit User' />
    </form>
       ";
	}
	elseif ($_POST['step'] == 3)
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_edituser2', stripslashes($_POST['verf'])))
		{
			alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
			die($h->endpage());
		}
		$username = (isset($_POST['username']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $_POST['username']) && ((strlen($_POST['username']) < 20) && (strlen($_POST['username']) >= 3))) ? stripslashes($_POST['username']) : '';
		$email = (isset($_POST['email'])) ? $db->escape(strip_tags(stripslashes($api->SystemFilterInput('email',$_POST['email'])))) : '';
		$userlevel = (isset($_POST['userlevel'])) ? $db->escape(strip_tags(stripslashes($api->SystemFilterInput('text',$_POST['userlevel'])))) : 'Member';
		$infirmaryr = (isset($_POST['infirmary_reason'])) ? $db->escape(strip_tags(stripslashes($api->SystemFilterInput('text',$_POST['infirmary_reason'])))) : 'Hurt';
		$dungeonr = (isset($_POST['dungeonreason'])) ? $db->escape(strip_tags(stripslashes($api->SystemFilterInput('text',$_POST['dungeonreason'])))) : 'Locked Up';
		
		$user = (isset($_POST['userid']) && is_numeric($_POST['userid'])) ? abs(intval($_POST['userid'])) : 0;
		$level = (isset($_POST['level']) && is_numeric($_POST['level'])) ? abs(intval($_POST['level'])) : 1;
		$money2 = (isset($_POST['sec_currency']) && is_numeric($_POST['sec_currency'])) ? abs(intval($_POST['sec_currency'])) : 0;
		$money = (isset($_POST['prim_currency']) && is_numeric($_POST['prim_currency'])) ? abs(intval($_POST['prim_currency'])) : 0;
		$maxwill = (isset($_POST['maxwill']) && is_numeric($_POST['maxwill'])) ? abs(intval($_POST['maxwill'])) : 100;
		$bank = (isset($_POST['int'])) ? $db->escape(strip_tags(stripslashes($api->SystemFilterInput('int',$_POST['bank'])))) : -1;
		$iq=(isset($_POST['IQ']) && is_numeric($_POST['IQ'])) ? abs(intval($_POST['IQ'])) : 1000;
		$strength=(isset($_POST['strength']) && is_numeric($_POST['strength'])) ? abs(intval($_POST['strength'])) : 1000;
		$agility=(isset($_POST['agility']) && is_numeric($_POST['agility'])) ? abs(intval($_POST['agility'])) : 1000;
		$guard=(isset($_POST['guard']) && is_numeric($_POST['guard'])) ? abs(intval($_POST['guard'])) : 1000;
		$labor=(isset($_POST['labor']) && is_numeric($_POST['labor'])) ? abs(intval($_POST['labor'])) : 1000;
		
		$equip_prim=(isset($_POST['primary_weapon']) && is_numeric($_POST['primary_weapon'])) ? abs(intval($_POST['primary_weapon'])) : 0;
		$equip_sec=(isset($_POST['secondary_weapon']) && is_numeric($_POST['secondary_weapon'])) ? abs(intval($_POST['secondary_weapon'])) : 0;
		$equip_armor=(isset($_POST['armor']) && is_numeric($_POST['armor'])) ? abs(intval($_POST['armor'])) : 0;
		$city=(isset($_POST['city']) && is_numeric($_POST['city'])) ? abs(intval($_POST['city'])) : 1;
		
		if (empty($username) || empty($email))
		{
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['STAFF_USERS_EDIT_SUB_MISSINGSTUFF']}");
			die($h->endpage());
		}
		$u_exists = $db->query("SELECT `userid` FROM `users` WHERE `userid` = {$user}");
		if ($db->num_rows($u_exists) == 0)
		{
			$db->free_result($u_exists);
			alert('danger',"{$lang['ERROR_NONUSER']}","{$lang['STAFF_USERS_EDIT_DND']}");
			die($h->endpage());
		}
		if (!isset($_POST['userlevel']) || ($_POST['userlevel'] != 'NPC' && $_POST['userlevel'] != 'Member' && $_POST['userlevel'] != 'Admin' && $_POST['userlevel'] != 'Forum Moderator' && $_POST['userlevel'] != 'Assistant' && $_POST['userlevel'] != 'Web Developer'))
		{
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['STAFF_USERS_EDIT_SUB_ULBAD']}");
			die($h->endpage());
		}
		$h_exists = $db->query("SELECT COUNT(`house_id`) FROM `estates` WHERE `house_will` = {$maxwill}");
		if ($db->fetch_single($h_exists) == 0)
		{
			$db->free_result($h_exists);
			alert("danger","{$lang['ERROR_GENERIC']}","{$lang['STAFF_USERS_EDIT_SUB_HBAD']}");
			die($h->endpage());
		}
		$u = $db->query("SELECT COUNT(`userid`) FROM `users` WHERE `username` = '{$username}' AND `userid` != {$user}");
		if ($db->fetch_single($u) != 0)
		{
			$db->free_result($u);
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['STAFF_USERS_EDIT_SUB_UNIU']}");
			die($h->endpage());
		}
		$e = $db->query("SELECT COUNT(`userid`) FROM `users` WHERE `email` = '{$email}' AND `userid` != {$user}");
		if ($db->fetch_single($e) != 0)
		{
			$db->free_result($e);
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['STAFF_USERS_EDIT_SUB_EIU']}");
			die($h->endpage());
		}
		if ($equip_prim > 0)
		{
			$pwq=$db->query("SELECT COUNT(`itmid`) FROM `items` WHERE `itmid` = '{$equip_prim}' AND `weapon` > 0");
			if ($db->fetch_single($pwq) == 0)
			{
				alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['STAFF_USERS_EDIT_SUB_WDNE']}");
				die($h->endpage());
			}
		}
		if ($equip_sec > 0)
		{
			$swq=$db->query("SELECT COUNT(`itmid`) FROM `items` WHERE `itmid` = '{$equip_sec}' AND `weapon` > 0");
			if ($db->fetch_single($swq) == 0)
			{
				alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['STAFF_USERS_EDIT_SUB_WDNE']}");
				die($h->endpage());
			}
		}
		if ($equip_armor > 0)
		{
			$aq=$db->query("SELECT COUNT(`itmid`) FROM `items` WHERE `itmid` = '{$equip_armor}' AND `armor` > 0");
			if ($db->fetch_single($aq) == 0)
			{
				alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['STAFF_USERS_EDIT_SUB_ADNE']}");
				die($h->endpage());
			}
		}
		$CityQuery=$db->query("SELECT COUNT(`town_id`) FROM `town` WHERE `town_id` = {$city}");
		if ($db->fetch_single($CityQuery) == 0)
		{
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['STAFF_USERS_EDIT_SUB_TDNE']}");
			die($h->endpage());
		}
		$db->free_result($u);
		$db->free_result($e);
		$db->free_result($h_exists);
		$oldwill = $db->fetch_single($u_exists);
		$db->free_result($u_exists);
		$will = ($oldwill > $maxwill) ? $maxwill : $oldwill;
		$energy = 20 + $_POST['level'] * 4;
		$brave = 6 + $_POST['level'] * 4;
		$hp = 50 + $_POST['level'] * 50;
		$db->query("UPDATE `users` SET `username` = '{$username}', `level` = {$level}, `primary_currency` = {$money}, `secondary_currency` = {$money2},
		`energy` = {$energy}, `maxenergy` = {$energy}, `brave` = {$brave}, `maxbrave` = {$brave}, `hp` = {$hp}, `maxhp` = {$hp}, `bank` = {$bank},
		`equip_armor` = {$equip_armor}, `equip_primary` = {$equip_prim}, `equip_secondary` = {$equip_sec}, `location` = {$city}, `will`= {$will}, `maxwill` = {$maxwill},
		`email` = '{$email}', `user_level` = '{$userlevel}' WHERE `userid` = {$user}");
		$db->query("UPDATE `userstats` SET `strength` = {$strength}, `agility` = {$agility}, `guard` = {$guard}, `iq` = {$iq}, `labor` = {$labor} WHERE `userid` = {$user}");
		if ($_POST['infirmary'] > 0)
		{
			$api->UserStatusSet($user,1,$_POST['infirmary'],$infirmaryr);
		}
		if ($_POST['dungeon'] > 0)
		{
			$api->UserStatusSet($user,2,$_POST['dungeon'],$dungeonr);
		}
		alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['STAFF_USERS_EDIT_SUB_SUCCESS']}");
		$api->SystemLogsAdd($userid,'staff',"Edited user <a href='../profile.php?user={$user}'>{$username}</a>.");
	}
	else
	{
		$csrf = request_csrf_html('staff_edituser1');
		echo "{$lang['STAFF_USERS_EDIT_START']}
    <br />
	<table class='table table-bordered'>
		<form method='post'>
			<tr>
				<th colspan='2'>
					{$lang['STAFF_USERS_EDIT_START']}
				</th>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_USERS_EDIT_USER']}
				</th>
				<td>
					" . user_dropdown('user') . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					{$csrf}
					<input type='hidden' name='step' value='2'>
					<input type='submit' class='btn btn-default' value='{$lang['STAFF_USERS_EDIT_BTN']}' />
				</th>
			</tr>
		</form>
		<form method='post'>
			<tr>
				<th colspan='2'>
					{$lang['STAFF_USERS_EDIT_ELSE']}
				</th>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_USERS_EDIT_USER']}
				</th>
				<td>
					<input class='form-control' type='number' min='1' name='user' />
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					{$csrf}
					<input type='hidden' name='step' value='2'>
					<input type='submit' class='btn btn-default' value='{$lang['STAFF_USERS_EDIT_BTN']}' />
				</th>
			</tr>
		</form>
	</table>
	";
	}
}
$h->endpage();