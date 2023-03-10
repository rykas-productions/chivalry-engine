<?php
/*
	File: staff/staff_users.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: Allows admins to interact with users of the game.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
require('sglobals.php');
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "createuser":
        usercreate();
        break;
    case "edituser":
        edituser();
        break;
    case "deleteuser":
        userdelete();
        break;
    case "logout":
        logout();
        break;
    case "changepw":
        changepw();
        break;
    case "masspayment":
        masspay();
        break;
    case "reports":
        preport();
        break;
    case "forcelogin":
        loginasuser();
        break;
    default:
        alert('danger', "Uh Oh!", "Please select a valid action to perform.", true, 'index.php');
        die($h->endpage());
        break;
}
function usercreate()
{
    global $db, $h, $api, $userid;
    if ($api->UserMemberLevelGet($userid, 'Admin') == false) {
        alert('danger', "Uh Oh!", "You do not have permission to be here.");
        die($h->endpage());
    }
    if (!isset($_POST['username'])) {
        $csrf = request_csrf_html('staff_user_1');
        echo "<hr /><h4>Create a User</h4><hr />";
        echo "
			<table class='table table-bordered'>
				<form method='post'>
					<tr>
						<th colspan='2'>
							Create a user by filling out this form.
						</th>
					</tr>
					<tr>
						<th>
							User ID
						</th>
						<td>
							<input type='number' id='userid' class='form-control' name='userid' value='0'>
						</td>
					</tr>
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
							Email Address
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
						<th colspan='2'>
							<b>Basic Info</b>
						</th>
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
							Sex
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
							Copper Coins
						</th>
						<td>
							<input type='number' required='1' class='form-control' min='0' name='prim_currency' value='100'>
						</td>
					</tr>
					<tr>
						<th>
							Chivalry Tokens
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
						<th colspan='2'>
							<b>Stats</b>
						</th>
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
							<b>Phyiscal Information</b>
						</td>
					</tr>
					<tr>
						<th>
							Town
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
							" . weapon_dropdown("primary_weapon", 0) . "
						</td>
					</tr>
					<tr>
						<th>
							Secondary Weapon
						</th>
						<td>
							" . weapon_dropdown("secondary_weapon", 0) . "
						</td>
					</tr>
					<tr>
						<th>
							Armor
						</th>
						<td>
							" . armor_dropdown("armor", 0) . "
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<input type='submit' class='btn btn-primary' value='Create User' />
						</td>
					</tr>
        	{$csrf}
				</form>
			</table>";
    } else {
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_user_1', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "This action was blocked for your security. Please submit the form quickly after opening it.");
            die($h->endpage());
        }
		$uid = (isset($_POST['userid']) && is_numeric($_POST['userid'])) ? abs(intval($_POST['userid'])) : 0;
        $username = (isset($_POST['username']) && is_string($_POST['username'])) ? stripslashes($_POST['username']) : '';
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

        $equip_prim = (isset($_POST['primary_weapon']) && is_numeric($_POST['primary_weapon'])) ? abs(intval($_POST['primary_weapon'])) : 0;
        $equip_sec = (isset($_POST['secondary_weapon']) && is_numeric($_POST['secondary_weapon'])) ? abs(intval($_POST['secondary_weapon'])) : 0;
        $equip_armor = (isset($_POST['armor']) && is_numeric($_POST['armor'])) ? abs(intval($_POST['armor'])) : 0;
        $city = (isset($_POST['city']) && is_numeric($_POST['city'])) ? abs(intval($_POST['city'])) : 1;

        if (!isset($_POST['email']) || !valid_email(stripslashes($_POST['email']))) {
            alert('danger', "Uh Oh!", "You input an invalid email address.");
            die($h->endpage());
        }
        if (!isset($_POST['gender']) || ($_POST['gender'] != 'Male' && $_POST['gender'] != 'Female')) {
            alert('danger', "Uh Oh!", "You input an invalid sex.");
            die($h->endpage());
        }
        if (!isset($_POST['class']) || ($_POST['class'] != 'Warrior' && $_POST['class'] != 'Rogue' && $_POST['class'] != 'Defender')) {
            alert('danger', "Uh Oh!", "You input an invalid class.");
            die($h->endpage());
        }
        if (!isset($_POST['userlevel']) || ($_POST['userlevel'] != 'NPC' && $_POST['userlevel'] != 'Member' &&
                $_POST['userlevel'] != 'Admin' && $_POST['userlevel'] != 'Forum Moderator' &&
                $_POST['userlevel'] != 'Assistant' && $_POST['userlevel'] != 'Web Developer')
        ) {
            alert('danger', "Uh Oh!", "You input an invalid User Level.");
            die($h->endpage());
        }
        if (((strlen($username) > 20) OR (strlen($username) < 3))) {
            alert('danger', "Uh Oh!", "Usernames can only be 3-20 characters in length.");
            die($h->endpage());
        }
        if ($equip_prim > 0) {
            $pwq = $db->query("/*qc=on*/SELECT COUNT(`itmid`) FROM `items` WHERE `itmid` = '{$equip_prim}' AND `weapon` > 0");
            if ($db->fetch_single($pwq) == 0) {
                alert('danger', "Uh Oh!", "You are trying to equip an invalid weapon.");
                die($h->endpage());
            }
        }
        if ($equip_sec > 0) {
            $swq = $db->query("/*qc=on*/SELECT COUNT(`itmid`) FROM `items` WHERE `itmid` = '{$equip_sec}' AND `weapon` > 0");
            if ($db->fetch_single($swq) == 0) {
                alert('danger', "Uh Oh!", "You are trying to equip an invalid weapon.");
                die($h->endpage());
            }
        }
        if ($equip_armor > 0) {
            $aq = $db->query("/*qc=on*/SELECT COUNT(`itmid`) FROM `items` WHERE `itmid` = '{$equip_armor}' AND `armor` > 0");
            if ($db->fetch_single($aq) == 0) {
                alert('danger', "Uh Oh!", "You are trying to equip an invalid armor.");
                die($h->endpage());
            }
        }
        $CityQuery = $db->query("/*qc=on*/SELECT COUNT(`town_id`) FROM `town` WHERE `town_id` = {$city}");
        if ($db->fetch_single($CityQuery) == 0) {
            alert('danger', "Uh Oh!", "You are trying to place the user in an invalid town.");
            die($h->endpage());
        }
        $e_gender = $db->escape(stripslashes($_POST['gender']));
        $e_class = $db->escape(stripslashes($_POST['class']));
        $e_username = $db->escape($username);
        $e_email = $db->escape(stripslashes($_POST['email']));
        $q = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users`  WHERE `username` = '{$e_username}'");
        $q2 = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `email` = '{$e_email}'");
        $u_check = $db->fetch_single($q);
        $e_check = $db->fetch_single($q2);
        $db->free_result($q);
        $db->free_result($q2);
        if ($u_check > 0) {
            alert('danger', "Uh Oh!", "The username you've chosen is already in use.");
            die($h->endpage());
        } else if ($e_check > 0) {
            alert('danger', "Uh Oh!", "The email you've chosen is already in use.");
            die($h->endpage());
        } else if (empty($pw) || empty($pw2)) {
            alert('danger', "Uh Oh!", "Please enter a valid password.");
            die($h->endpage());
        } else if ($pw != $pw2) {
            alert('danger', "Uh Oh!", "Password confirmation failed.");
            die($h->endpage());
        } else {
			if ($uid == 0)
				$insert = 'NULL';
			else
				$insert = $uid;
            $HP = (50) + $_POST['level'] * 50;
            $Energy = (22) + $_POST['level'] * 2;
            $Brave = (8) + $_POST['level'] * 2;
            $time = time();
            $encpsw = encode_password($pw);
            $e_encpsw = $db->escape($encpsw);
			$db->query("INSERT INTO `users` 
			(`userid`, `username`, `user_level`, `email`, `password`, 
			`level`, `gender`, `class`, `lastip`, `loginip`, 
			`registerip`, `laston`, `last_login`, `registertime`, 
			`hp`, `maxhp`, `energy`, `maxenergy`, 
			`brave`, `maxbrave`, `primary_currency`, `secondary_currency`, 
			`vip_days`, `display_pic`, `signature`, `personal_notes`,  
			`staff_notes`, `location`, `description`,
			`equip_primary`, `equip_secondary`, `equip_armor`) 
			VALUES 
			('{$insert}', '{$e_username}', '{$_POST['userlevel']}', '{$e_email}', '{$e_encpsw}', 
			'{$_POST['level']}', '{$_POST['gender']}', '{$_POST['class']}', '127.0.0.1', '127.0.0.1', 
			'127.0.0.1', '{$time}', '{$time}', '{$time}', 
			'{$HP}', '{$HP}', '{$Energy}', '{$Energy}', 
			'{$Brave}', '{$Brave}', '{$Money}', '{$Money2}', 
			'{$VIP}', '', '', '', 
			'', '{$city}', '',
			'{$equip_prim}', '{$equip_sec}', '{$equip_armor}')");
			$i = $db->insert_id();
            $db->query("INSERT INTO `userstats` VALUES($i, {$Strength}, {$Agility}, {$Guard}, {$IQ}, {$Labor}, 100)");
            $db->query("INSERT INTO `infirmary` (`infirmary_user`, `infirmary_reason`, `infirmary_in`, `infirmary_out`) VALUES ('{$i}', 'N/A', '0', '0');");
            $db->query("INSERT INTO `dungeon` (`dungeon_user`, `dungeon_reason`, `dungeon_in`, `dungeon_out`) VALUES ('{$i}', 'N/A', '0', '0');");
            alert('success', "Success!", "You have successfully created the user named {$e_username}.", true, 'index.php');
            $api->SystemLogsAdd($userid, 'staff', "Created user <a href='../profile.php?user={$i}'>{$e_username}</a>.");
        }
    }
}

function edituser()
{
    global $db, $h, $userid, $api;
    if (!isset($_POST['step'])) {
        $_POST['step'] = 0;
    }
    if ($_POST['step'] == 2) {
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_edituser1', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "This action was blocked for your security. Please submit the form quickly after opening it.");
            die($h->endpage());
        }
        $_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs(intval($_POST['user'])) : 0;
        if (empty($_POST['user'])) {
            alert('danger', "Uh Oh!", "Please select the user you wish to edit.");
            die($h->endpage());
        }
        $d = $db->query("/*qc=on*/SELECT `i`.*, `d`.*, `username`,
		`level`, `primary_currency`,`secondary_currency`, `equip_primary`,
		`maxwill`, `bank`, `strength`, `agility`, `guard`, `equip_secondary`,
		`labor`, `IQ`, `location`, `equip_armor`, `email`, `display_pic`, `equip_badge`,
        `equip_potion`
		 FROM `users` AS `u`
		 INNER JOIN `userstats` AS `us`
		 ON `u`.`userid` = `us`.`userid`
		 INNER JOIN `dungeon` AS `d`
		 ON `u`.`userid` = `d`.`dungeon_user`
		 INNER JOIN `infirmary` AS `i`
		 ON `u`.`userid` = `i`.`infirmary_user`
		 WHERE `u`.`userid` = {$_POST['user']}");
        if ($db->num_rows($d) == 0) {
            $db->free_result($d);
            alert('danger', "Uh Oh!", "The user you're trying to edit does not exist.");
            die($h->endpage());
        }
        $itemi = $db->fetch_row($d);
        $db->free_result($d);
        $CurrentTime = time();
        $itemi['infirmary_reason'] = htmlentities($itemi['infirmary_reason'], ENT_QUOTES, 'ISO-8859-1');
		$itemi['display_pic'] = htmlentities($itemi['display_pic'], ENT_QUOTES, 'ISO-8859-1');
        $itemi['email'] = htmlentities($itemi['email'], ENT_QUOTES, 'ISO-8859-1');
        $itemi['dungeon_reason'] = htmlentities($itemi['dungeon_reason'], ENT_QUOTES, 'ISO-8859-1');
        $itemi['username'] = htmlentities($itemi['username'], ENT_QUOTES, 'ISO-8859-1');
        $itemi['infirmary'] = round(($itemi['infirmary_out'] - $CurrentTime) / 60);
        $itemi['dungeon'] = round(($itemi['dungeon_out'] - $CurrentTime) / 60);
        if ($itemi['infirmary'] < 0) {
            $itemi['infirmary'] = 0;
        }
        if ($itemi['dungeon'] < 0) {
            $itemi['dungeon'] = 0;
        }
        $trinkq=$db->query("SELECT * FROM `user_equips` WHERE `userid` = {$_POST['user']}");
        while ($tr = $db->fetch_row($trinkq))
        {
            if (empty($tr['itemid']))
                $tr['itemid'] = 0;
            $itemi[$tr['equip_slot']] = $tr['itemid'];
        }
        $csrf = request_csrf_html('staff_edituser2');
        echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Editing a User
					<input type='hidden' name='userid' value='{$_POST['user']}' />
					<input type='hidden' name='step' value='3' />
				</th>
			</tr>
			<tr>
				<th width='33%'>
					Username
				</th>
				<td>
					<input type='text' class='form-control' required='1' name='username' value='{$itemi['username']}' />
				</td>
			</tr>
			<tr>
				<th>
					Email Address
				</th>
				<td>
					<input type='text' class='form-control' required='1' name='email' value='{$itemi['email']}' />
				</td>
			</tr>
			<tr>
				<th>
					Display Pic
				</th>
				<td>
					<input type='text' class='form-control' required='1' name='display_pic' value='{$itemi['display_pic']}' />
				</td>
			</tr>
			<tr>
				<th>
					Level
				</th>
				<td>
					<input type='number' min='1' class='form-control' required='1' name='level' value='{$itemi['level']}' />
				</td>
			</tr>
			<tr>
				<th>
					Copper Coins
				</th>
				<td>
					<input type='number' min='0' class='form-control' required='1' name='prim_currency' value='{$itemi['primary_currency']}' />
				</td>
			</tr>
			<tr>
				<th>
					Bank Money
				</th>
				<td>
					<input type='number' min='-1' class='form-control' required='1' name='bank' value='{$itemi['bank']}' />
				</td>
			</tr>
			<tr>
				<th>
					Chivalry Tokens
				</th>
				<td>
					<input type='number' min='0' class='form-control' required='1' name='sec_currency' value='{$itemi['secondary_currency']}' />
				</td>
			</tr>
			<tr>
				<th>
					Infirmary Time
				</th>
				<td>
					<input type='number' min='0' class='form-control' required='1' name='infirmary' value='{$itemi['infirmary']}' />
				</td>
			</tr>
			<tr>
				<th>
					Infirmary Reason
				</th>
				<td>
					<input type='text' class='form-control' name='infirmary_reason' value='{$itemi['infirmary_reason']}' />
				</td>
			</tr>
			<tr>
				<th>
					Dungeon Time
				</th>
				<td>
					<input type='number' min='0' class='form-control' required='1' name='dungeon' value='{$itemi['dungeon']}' />
				</td>
			</tr>
			<tr>
				<th>
					Dungoen Reason
				</th>
				<td>
					<input type='text' class='form-control' name='dungeonreason' value='{$itemi['dungeon_reason']}' />
				</td>
			</tr>
			<tr>
				<th>
					Estate
				</th>
				<td>
					" . estate2_dropdown("maxwill", $itemi['maxwill']) . "
				</td>
			</tr>
			<tr>
				<th colspan='2'>
					Stats
				</th>
			</tr>
			<tr>
				<th>
					Strength
				</th>
				<td>
					<input type='number' min='1' class='form-control' required='1' name='strength' value='{$itemi['strength']}' />
				</td>
			</tr>
			<tr>
				<th>
					Agility
				</th>
				<td>
					<input type='number' min='1' class='form-control' required='1' name='agility' value='{$itemi['agility']}' />
				</td>
			</tr>
			<tr>
				<th>
					Guard
				</th>
				<td>
					<input type='number' min='1' class='form-control' required='1' name='guard' value='{$itemi['guard']}' />
				</td>
			</tr>
			<tr>
				<th>
					Labor
				</th>
				<td>
					<input type='number' min='1' class='form-control' required='1' name='labor' value='{$itemi['labor']}' />
				</td>
			</tr>
			<tr>
				<th>
					IQ
				</th>
				<td>
					<input type='number' min='1' class='form-control' required='1' name='IQ' value='{$itemi['IQ']}' />
				</td>
			</tr>
			<tr>
				<th colspan='2'>
					Physical Info
				</th>
			</tr>
			<tr>
				<th>
					Town
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
					" . weapon_dropdown("primary_weapon", $itemi['equip_primary']) . "
				</td>
			</tr>
			<tr>
				<th>
					Secondary Weapon
				</th>
				<td>
					" . weapon_dropdown("secondary_weapon", $itemi['equip_secondary']) . "
				</td>
			</tr>
			<tr>
				<th>
					Armor
				</th>
				<td>
					" . armor_dropdown("armor", $itemi['equip_armor']) . "
				</td>
			</tr>
            <tr>
				<th>
					Profile Badge
				</th>
				<td>
					" . badge_dropdown("badge", $itemi['equip_badge']) . "
				</td>
			</tr>
            <tr>
				<th>
					Combat Potion
				</th>
				<td>
					" . potion_dropdown("potion", $itemi['equip_potion']) . "
				</td>
			</tr>
            <tr>
				<th>
					Primary Ring
				</th>
				<td>
					" . ring_dropdown("ring_primary", $itemi['equip_ring_primary']) . "
				</td>
			</tr>
            <tr>
				<th>
					Secondary Ring
				</th>
				<td>
					" . ring_dropdown("ring_secondary", $itemi['equip_ring_secondary']) . "
				</td>
			</tr>
            <tr>
				<th>
					Necklace
				</th>
				<td>
					" . necklace_dropdown("necklace", $itemi['equip_necklace']) . "
				</td>
			</tr>
            <tr>
				<th>
					Pendant
				</th>
				<td>
					" . pendant_dropdown("pendant", $itemi['equip_pendant']) . "
				</td>
			</tr>
		</table>
    	{$csrf}
    	<input class='btn btn-primary' type='submit' value='Edit User' />
    </form>
       ";
    } elseif ($_POST['step'] == 3) {
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_edituser2', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "This action was blocked for your security. Please submit the form quickly after opening it.");
            die($h->endpage());
        }
        $username = (isset($_POST['username']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $_POST['username']) && ((strlen($_POST['username']) < 20) && (strlen($_POST['username']) >= 3))) ? stripslashes($_POST['username']) : '';
        $email = (isset($_POST['email'])) ? $db->escape(strip_tags(stripslashes($_POST['email']))) : '';
        $infirmaryr = (isset($_POST['infirmary_reason'])) ? $db->escape(strip_tags(stripslashes($_POST['infirmary_reason']))) : 'Hurt';
        $dungeonr = (isset($_POST['dungeonreason'])) ? $db->escape(strip_tags(stripslashes($_POST['dungeonreason']))) : 'Locked Up';
		$displayPic = (isset($_POST['display_pic']) && is_string($_POST['display_pic'])) ? stripslashes($_POST['display_pic']) : '';

        $user = (isset($_POST['userid']) && is_numeric($_POST['userid'])) ? abs(intval($_POST['userid'])) : 0;
        $level = (isset($_POST['level']) && is_numeric($_POST['level'])) ? abs(intval($_POST['level'])) : 1;
        $money2 = (isset($_POST['sec_currency']) && is_numeric($_POST['sec_currency'])) ? abs(intval($_POST['sec_currency'])) : 0;
        $money = (isset($_POST['prim_currency']) && is_numeric($_POST['prim_currency'])) ? abs(intval($_POST['prim_currency'])) : 0;
        $maxwill = (isset($_POST['maxwill']) && is_numeric($_POST['maxwill'])) ? abs(intval($_POST['maxwill'])) : 100;
        $bank = (isset($_POST['bank']) && is_numeric($_POST['bank'])) ? intval($_POST['bank']) : -1;
        $iq = (isset($_POST['IQ']) && is_numeric($_POST['IQ'])) ? abs(intval($_POST['IQ'])) : 1000;
        $strength = (isset($_POST['strength']) && is_numeric($_POST['strength'])) ? abs(intval($_POST['strength'])) : 1000;
        $agility = (isset($_POST['agility']) && is_numeric($_POST['agility'])) ? abs(intval($_POST['agility'])) : 1000;
        $guard = (isset($_POST['guard']) && is_numeric($_POST['guard'])) ? abs(intval($_POST['guard'])) : 1000;
        $labor = (isset($_POST['labor']) && is_numeric($_POST['labor'])) ? abs(intval($_POST['labor'])) : 1000;

        $equip_prim = (isset($_POST['primary_weapon']) && is_numeric($_POST['primary_weapon'])) ? abs(intval($_POST['primary_weapon'])) : 0;
        $equip_sec = (isset($_POST['secondary_weapon']) && is_numeric($_POST['secondary_weapon'])) ? abs(intval($_POST['secondary_weapon'])) : 0;
        $equip_armor = (isset($_POST['armor']) && is_numeric($_POST['armor'])) ? abs(intval($_POST['armor'])) : 0;
        $equip_pot = (isset($_POST['potion']) && is_numeric($_POST['potion'])) ? abs(intval($_POST['potion'])) : 0;
        $equip_badge = (isset($_POST['badge']) && is_numeric($_POST['badge'])) ? abs(intval($_POST['badge'])) : 0;
        $equip_ring_prim = (isset($_POST['ring_primary']) && is_numeric($_POST['ring_primary'])) ? abs(intval($_POST['ring_primary'])) : 0;
        $equip_ring_sec = (isset($_POST['ring_secondary']) && is_numeric($_POST['ring_secondary'])) ? abs(intval($_POST['ring_secondary'])) : 0;
        $equip_necklace = (isset($_POST['necklace']) && is_numeric($_POST['necklace'])) ? abs(intval($_POST['necklace'])) : 0;
        $equip_pendant = (isset($_POST['pendant']) && is_numeric($_POST['pendant'])) ? abs(intval($_POST['pendant'])) : 0;
        
        $city = (isset($_POST['city']) && is_numeric($_POST['city'])) ? abs(intval($_POST['city'])) : 1;

        if (empty($username) || empty($email)) {
            alert('danger', "Uh Oh!", "Please specify an email and username.");
            die($h->endpage());
        }
        $u_exists = $db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `userid` = {$user}");
        if ($db->num_rows($u_exists) == 0) {
            $db->free_result($u_exists);
            alert('danger', "Uh Oh!", "The user you are trying to edit does not exist.");
            die($h->endpage());
        }
        $h_exists = $db->query("/*qc=on*/SELECT COUNT(`house_id`) FROM `estates` WHERE `house_will` = {$maxwill}");
        if ($db->fetch_single($h_exists) == 0) {
            $db->free_result($h_exists);
            alert("danger", "Uh Oh!", "The house you're trying to have this user live in does not exist.");
            die($h->endpage());
        }
        $u = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `username` = '{$username}' AND `userid` != {$user}");
        if ($db->fetch_single($u) != 0) {
            $db->free_result($u);
            alert('danger', "Uh Oh!", "The username for this user is already in use.");
            die($h->endpage());
        }
        $e = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `email` = '{$email}' AND `userid` != {$user}");
        if ($db->fetch_single($e) != 0) {
            $db->free_result($e);
            alert('danger', "Uh Oh!", "The email address input is already in use.");
            die($h->endpage());
        }
        if ($equip_prim > 0) {
            $pwq = $db->query("/*qc=on*/SELECT COUNT(`itmid`) FROM `items` WHERE `itmid` = '{$equip_prim}' AND `weapon` > 0");
            if ($db->fetch_single($pwq) == 0) {
                alert('danger', "Uh Oh!", "The primary weapon selected does not exist.");
                die($h->endpage());
            }
        }
        if ($equip_sec > 0) {
            $swq = $db->query("/*qc=on*/SELECT COUNT(`itmid`) FROM `items` WHERE `itmid` = '{$equip_sec}' AND `weapon` > 0");
            if ($db->fetch_single($swq) == 0) {
                alert('danger', "Uh Oh!", "The secondary weapon selected does not exist.");
                die($h->endpage());
            }
        }
        if ($equip_armor > 0) {
            $aq = $db->query("/*qc=on*/SELECT COUNT(`itmid`) FROM `items` WHERE `itmid` = '{$equip_armor}' AND `armor` > 0");
            if ($db->fetch_single($aq) == 0) {
                alert('danger', "Uh Oh!", "The armor selected does not exist.");
                die($h->endpage());
            }
        }
        if ($equip_badge > 0) {
            $aq = $db->query("/*qc=on*/SELECT COUNT(`itmid`) FROM `items` WHERE `itmid` = '{$equip_badge}' AND `itmtype` = 13");
            if ($db->fetch_single($aq) == 0) {
                alert('danger', "Uh Oh!", "The badge selected does not exist.");
                die($h->endpage());
            }
        }
        if ($equip_pot > 0) {
            $aq = $db->query("/*qc=on*/SELECT COUNT(`itmid`) FROM `items` WHERE `itmid` = '{$equip_badge}' AND `itmtype` = 7 OR `itmtype` = 8");
            if ($db->fetch_single($aq) == 0) {
                alert('danger', "Uh Oh!", "The potion selected does not exist.");
                die($h->endpage());
            }
        }
        if ($equip_ring_prim > 0) {
            $aq = $db->query("/*qc=on*/SELECT COUNT(`itmid`) FROM `items` WHERE `itmid` = '{$equip_ring_prim}' AND `itmtype` = 15");
            if ($db->fetch_single($aq) == 0) {
                alert('danger', "Uh Oh!", "The primary ring selected does not exist.");
                die($h->endpage());
            }
        }
        if ($equip_ring_sec > 0) {
            $aq = $db->query("/*qc=on*/SELECT COUNT(`itmid`) FROM `items` WHERE `itmid` = '{$equip_ring_sec}' AND `itmtype` = 15");
            if ($db->fetch_single($aq) == 0) {
                alert('danger', "Uh Oh!", "The secondary ring selected does not exist.");
                die($h->endpage());
            }
        }
        if ($equip_necklace > 0) {
            $aq = $db->query("/*qc=on*/SELECT COUNT(`itmid`) FROM `items` WHERE `itmid` = '{$equip_necklace}' AND `itmtype` = 16");
            if ($db->fetch_single($aq) == 0) {
                alert('danger', "Uh Oh!", "The necklace selected does not exist.");
                die($h->endpage());
            }
        }
        if ($equip_pendant > 0) {
            $aq = $db->query("/*qc=on*/SELECT COUNT(`itmid`) FROM `items` WHERE `itmid` = '{$equip_pendant}' AND `itmtype` = 18");
            if ($db->fetch_single($aq) == 0) {
                alert('danger', "Uh Oh!", "The pendant selected does not exist.");
                die($h->endpage());
            }
        }
        $CityQuery = $db->query("/*qc=on*/SELECT COUNT(`town_id`) FROM `town` WHERE `town_id` = {$city}");
        if ($db->fetch_single($CityQuery) == 0) {
            alert('danger', "Uh Oh!", "The town you wish the user to be in does not exist.");
            die($h->endpage());
        }
        $db->free_result($u);
        $db->free_result($e);
        $db->free_result($h_exists);
        $oldwill = $db->fetch_single($u_exists);
        $db->free_result($u_exists);
        $will = ($oldwill > $maxwill) ? $maxwill : $oldwill;
        $energy = 22 + ($_POST['level'] * 2);
        $brave = 8 + ($_POST['level'] * 2);
        $hp = 50 + ($_POST['level'] * 50);
        $db->query("UPDATE `users` SET `username` = '{$username}', `level` = {$level}, `primary_currency` = {$money}, `secondary_currency` = {$money2},
		`energy` = {$energy}, `maxenergy` = {$energy}, `brave` = {$brave}, `maxbrave` = {$brave}, `hp` = {$hp}, `maxhp` = {$hp}, `bank` = {$bank},
		 `location` = {$city}, `will`= {$will}, `maxwill` = {$maxwill},
		`email` = '{$email}', `display_pic` = '{$displayPic}' WHERE `userid` = {$user}");
        $db->query("UPDATE `userstats` SET `strength` = {$strength}, `agility` = {$agility}, `guard` = {$guard}, `iq` = {$iq}, `labor` = {$labor} WHERE `userid` = {$user}");
        if ($_POST['infirmary'] > 0) {
            $api->UserStatusSet($user, 'infirmary', $_POST['infirmary'], $infirmaryr);
        }
        if ($_POST['dungeon'] > 0) {
            $api->UserStatusSet($user, 'dungeon', $_POST['dungeon'], $dungeonr);
        }
        equipUserSlot($user, "equip_primary", $equip_prim);
        equipUserSlot($user, "equip_secondary", $equip_sec);
        equipUserSlot($user, "equip_armor", $equip_armor);
        equipUserSlot($user, "equip_potion", $equip_pot);
        equipUserSlot($user, "equip_badge", $equip_badge);
        equipUserSlot($user, "equip_ring_primary", $equip_ring_prim);
        equipUserSlot($user, "equip_ring_secondary", $equip_ring_sec);
        equipUserSlot($user, "equip_necklace", $equip_necklace);
        equipUserSlot($user, "equip_pendant", $equip_pendant);
        alert('success', "Success!", "You have successfully edited {$username}'s account.", true, 'index.php');
        $api->SystemLogsAdd($userid, 'staff', "Edited user <a href='../profile.php?user={$user}'>{$username}</a>.");
    } else {
        $csrf = request_csrf_html('staff_edituser1');
        echo "Editing an User
    <br />
	<table class='table table-bordered'>
		<form method='post'>
			<tr>
				<th colspan='2'>
					Select the user who you wish to edit.
				</th>
			</tr>
			<tr>
				<th>
					User
				</th>
				<td>
					" . user_dropdown('user') . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					{$csrf}
					<input type='hidden' name='step' value='2'>
					<input type='submit' class='btn btn-primary' value='Edit User' />
				</th>
			</tr>
		</form>
		<form method='post'>
			<tr>
				<th colspan='2'>
					Alternatively, you can enter their User ID instead.
				</th>
			</tr>
			<tr>
				<th>
					User
				</th>
				<td>
					<input class='form-control' type='number' min='1' name='user' />
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					{$csrf}
					<input type='hidden' name='step' value='2'>
					<input type='submit' class='btn btn-primary' value='Edit User' />
				</th>
			</tr>
		</form>
	</table>
	";
    }
}

function userdelete()
{
    global $db, $userid, $h, $api, $ir;
    if ($api->UserMemberLevelGet($userid, 'Admin') == false) {
        alert('danger', "Uh Oh!", "You do not have permission to be here.");
        die($h->endpage());
    }
    if (!isset($_GET['step'])) {
        $_GET['step'] = '0';
    }
    switch ($_GET['step']) {
        default:
            $csrf = request_csrf_html('staff_deluser1');
            echo "<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						Select the user you wish to delete.
					</th>
				</tr>
				<form action='?action=deleteuser&step=2' method='post'>
				{$csrf}
				<tr>
					<th>
						User
					</th>
					<td>
						" . user_dropdown('user') . "
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-primary' value='Delete User' />
					</td>
				</tr>
				</form>
				<tr>
					<th colspan='2'>
						Alternatively, you can enter a User's ID
					</th>
				</tr>
				<form action='?action=deleteuser&step=2' method='post'>
				{$csrf}
				<tr>
					<th>
						User
					</th>
					<td>
						<input type='number' class='form-control' required='1' name='user' value='0' />
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-primary' value='Delete User' />
					</td>
				</tr>
				</form>
			</table>";
            break;
        case 2:
            $_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs(intval($_POST['user'])) : 0;
            if (!isset($_POST['verf']) || !verify_csrf_code('staff_deluser1', stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "This action was blocked for your security. Please submit the form quickly after opening it.");
                die($h->endpage());
            }
            if (empty($_POST['user']) || $_POST['user'] == 1 || $_POST['user'] == $ir['userid']) {
                alert('danger', "Uh Oh!", "You cannot delete your account, or the game owner's account.");
                die($h->endpage());
            }
            $d = $db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `userid` = {$_POST['user']}");
            if ($db->num_rows($d) == 0) {
                $db->free_result($d);
                alert('danger', "Uh Oh!", "You cannot delete a non-existent account.");
                die($h->endpage());
            }
            $username = htmlentities($db->fetch_single($d), ENT_QUOTES, 'ISO-8859-1');
            $db->free_result($d);
            $csrf = request_csrf_html('staff_deluser2');
            echo "
			<form action='?action=deleteuser&step=3' method='post'>
			<input type='hidden' name='userid' value='{$_POST['user']}' />
			{$csrf}
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
					 Are you sure you want to delete {$username}'s account? Everything associated with the account will be deleted.
					</th>
				</tr>
				<tr>
					<td>
						<input type='submit' class='btn btn-primary' name='yesorno' value='Yes' />
					</td>
					<td>
						<input type='submit' class='btn btn-primary' name='yesorno' value='No' onclick=\"window.location='staff_users.php?action=deluser';\" />
					</td>
				</tr>
			</table>
			</form>";
            break;
        case 3:
            if (!isset($_POST['verf']) || !verify_csrf_code('staff_deluser2', stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "This action was blocked for your security. Please submit the form quickly after opening it.");
                die($h->endpage());
            }
            $_POST['userid'] = (isset($_POST['userid']) && is_numeric($_POST['userid'])) ? abs(intval($_POST['userid'])) : 0;
            $_POST['yesorno'] = (isset($_POST['yesorno']) && in_array($_POST['yesorno'], array('Yes', 'No'))) ? $_POST['yesorno'] : '';
            if ((empty($_POST['userid']) || empty($_POST['yesorno'])) || $_POST['userid'] == 1 || $_POST['userid'] == $ir['userid']) {
                alert('danger', "Uh Oh!", "You cannot delete your account, the game owner's account, or an unspecified account.");
                die($h->endpage());
            }
            if ($_POST['yesorno'] == 'No') {
                alert('warning', "Success!", "You have not deleted this account.");
                die($h->endpage());
            }
            $d = $db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `userid` = {$_POST['userid']}");
            if ($db->num_rows($d) == 0) {
                alert('danger', "Uh Oh!", "The account you are trying to delete does not exist.");
                die($h->endpage());
            }
            $username = htmlentities($db->fetch_single($d), ENT_QUOTES, 'ISO-8859-1');
			$db->query("DELETE FROM `2fa_table` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `2018_christmas_tree` WHERE `userid_from` = {$_POST['userid']}");
			$db->query("DELETE FROM `2018_christmas_tree` WHERE `userid_to` = {$_POST['userid']}");
			$db->query("DELETE FROM `2018_christmas_wishes` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `2018_halloween_chuck` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `2018_halloween_tot` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `2019_bigbang` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `academy_done` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `achievements_done` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `advent_calender` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `artifacts` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `attack_logs` WHERE `attacked` = {$_POST['userid']}");
			$db->query("DELETE FROM `attack_logs` WHERE `attacker` = {$_POST['userid']}");
			$db->query("DELETE FROM `auto_login` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `bank_investments` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `blocklist` WHERE `blocked` = {$_POST['userid']}");
			$db->query("DELETE FROM `blocklist` WHERE `blocker` = {$_POST['userid']}");
			$db->query("DELETE FROM `botlist_hits` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `bounty_hunter` WHERE `bh_creator` = {$_POST['userid']}");
			$db->query("DELETE FROM `bounty_hunter` WHERE `bh_user` = {$_POST['userid']}");
			$db->query("DELETE FROM `chat` WHERE `chat_user` = {$_POST['userid']}");
			$db->query("DELETE FROM `comments` WHERE `cRECEIVE` = {$_POST['userid']}");
			$db->query("DELETE FROM `comments` WHERE `cSEND` = {$_POST['userid']}");
			$db->query("DELETE FROM `contact_list` WHERE `c_ADDED` = {$_POST['userid']}");
			$db->query("DELETE FROM `contact_list` WHERE `c_ADDER` = {$_POST['userid']}");
			$db->query("DELETE FROM `crime_logs` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `dungeon` WHERE `dungeon_user` = {$_POST['userid']}");
			$db->query("DELETE FROM `enemy` WHERE `enemy_user` = {$_POST['userid']}");
			$db->query("DELETE FROM `enemy` WHERE `enemy_adder` = {$_POST['userid']}");
			$db->query("DELETE FROM `equip_gains` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `farm_data` WHERE `farm_owner` = {$_POST['userid']}");
			$db->query("DELETE FROM `farm_users` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `fedjail` WHERE `fed_userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `fedjail_appeals` WHERE `fja_user` = {$_POST['userid']}");
			$db->query("DELETE FROM `fedjail_appeals` WHERE `fja_responder` = {$_POST['userid']}");
			$db->query("DELETE FROM `forum_bans` WHERE `fb_user` = {$_POST['userid']}");
			$db->query("DELETE FROM `forum_posts` WHERE `fp_poster_id` = {$_POST['userid']}");
			$db->query("DELETE FROM `forum_topics` WHERE `ft_owner_id` = {$_POST['userid']}");
			$db->query("DELETE FROM `forum_tops_rating` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `friends` WHERE `friended` = {$_POST['userid']}");
			$db->query("DELETE FROM `friends` WHERE `friender` = {$_POST['userid']}");
			$db->query("DELETE FROM `guild_applications` WHERE `ga_user` = {$_POST['userid']}");
			$db->query("DELETE FROM `guild_donations` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `infirmary` WHERE `infirmary_user` = {$_POST['userid']}");
			$db->query("DELETE FROM `inventory` WHERE `inv_userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `itemauction` WHERE `ia_adder` = {$_POST['userid']}");
			$db->query("DELETE FROM `itemmarket` WHERE `imADDER` = {$_POST['userid']}");
			$db->query("DELETE FROM `itemrequest` WHERE `imADDER` = {$_POST['userid']}");
			$db->query("DELETE FROM `login_attempts` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `logs` WHERE `log_user` = {$_POST['userid']}");
			$db->query("DELETE FROM `mail` WHERE `mail_to` = {$_POST['userid']}");
			$db->query("DELETE FROM `mail` WHERE `mail_from` = {$_POST['userid']}");
			$db->query("DELETE FROM `mail_bans` WHERE `mbUSER` = {$_POST['userid']}");
			$db->query("DELETE FROM `marriage_tmg` WHERE `proposer_id` = {$_POST['userid']}");
			$db->query("DELETE FROM `marriage_tmg` WHERE `proposed_id` = {$_POST['userid']}");
			$db->query("DELETE FROM `mining` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `missions` WHERE `mission_userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `newspaper_ads` WHERE `news_owner` = {$_POST['userid']}");
			$db->query("DELETE FROM `notepads` WHERE `np_owner` = {$_POST['userid']}");
			$db->query("DELETE FROM `notifications` WHERE `notif_user` = {$_POST['userid']}");
			$db->query("DELETE FROM `permissions` WHERE `perm_user` = {$_POST['userid']}");
			$db->query("DELETE FROM `referals` WHERE `referal_userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `referals` WHERE `refered_id` = {$_POST['userid']}");
			$db->query("DELETE FROM `reports` WHERE `reporter_id` = {$_POST['userid']}");
			$db->query("DELETE FROM `reports` WHERE `reportee_id` = {$_POST['userid']}");
			$db->query("DELETE FROM `russian_roulette` WHERE `challengee` = {$_POST['userid']}");
			$db->query("DELETE FROM `russian_roulette` WHERE `challenger` = {$_POST['userid']}");
			$db->query("DELETE FROM `sec_market` WHERE `sec_user` = {$_POST['userid']}");
			$db->query("DELETE FROM `shortcut` WHERE `sc_userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `smelt_inprogress` WHERE `sip_user` = {$_POST['userid']}");
			$db->query("DELETE FROM `spy_advantage` WHERE `user` = {$_POST['userid']}");
			$db->query("DELETE FROM `spy_advantage` WHERE `spied` = {$_POST['userid']}");
			$db->query("DELETE FROM `steam_account_link` WHERE `steam_linked` = {$_POST['userid']}");
			$db->query("DELETE FROM `thanksgiving_trivia` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `users` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `userdata` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `userstats` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `uservotes` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `user_logging` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `user_settings` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `user_skills` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `votes` WHERE `userid` = {$_POST['userid']}");
			$db->query("DELETE FROM `vip_market` WHERE `vip_user` = {$_POST['userid']}");
			$db->query("DELETE FROM `vote_raffle` WHERE `userid` = {$_POST['userid']}");
            $api->SystemLogsAdd($userid, 'staff', "Deleted user {$username} [{$_POST['userid']}].");
            alert("success", "Success!", "You have deleted {$username}'s account.", true, 'index.php');
            die($h->endpage());
            break;
    }
}

function logout()
{
    global $db, $h, $userid, $api;
    if ($api->UserMemberLevelGet($userid, 'Assistant') == false) {
        alert('danger', "Uh Oh!", "You do not have permission to be here.");
        die($h->endpage());
    }
    $_POST['userid'] = (isset($_POST['userid']) && is_numeric($_POST['userid'])) ? abs(intval($_POST['userid'])) : 0;
    if (!empty($_POST['userid'])) {
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_forcelogout', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "This action was blocked for your security. Please submit the form quickly after opening it.");
            die($h->endpage());
        }
        $d = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `userid` = {$_POST['userid']}");
        if ($db->fetch_single($d) == 0) {
            $db->free_result($d);
            alert('danger', "Uh Oh!", "You are trying to force a non-existent account to log out.");
            die($h->endpage());
        }
        $db->free_result($d);
        $db->query("UPDATE `users` SET `force_logout` = 'true' WHERE `userid` = {$_POST['userid']}");
        $api->SystemLogsAdd($userid, 'staff', "Forced User ID {$_POST['userid']} to log out.");
        alert("success", "Success!", "You have successfully forced User ID {$_POST['userid']} to log out.", true, 'index.php');
    } else {
        $csrf = request_csrf_html('staff_forcelogout');
        echo "
		<form action='?action=logout' method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						Select the user you wish to force log out.
					</th>
				</tr>
				<tr>
					<th>
						User
					</th>
					<td>
						" . user_dropdown('userid') . "
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-primary' value='Force Log Out' />
					</td>
				</tr>
			</table>
			{$csrf}
		</form>";
    }
}

function changepw()
{
    global $db, $h, $userid, $api;
    if ($api->UserMemberLevelGet($userid, 'Admin') == false) {
        alert('danger', "Uh Oh!", "You do not have permission to be here.");
        die($h->endpage());
    }
    if ((isset($_POST['user'])) && (isset($_POST['pw']))) {
        $pw = stripslashes($_POST['pw']);
        $user = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs(intval($_POST['user'])) : 0;
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_changepw', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "This action was blocked for your security. Please submit the form quickly after opening it.");
            die($h->endpage());
        }
        if (($user == 1) && ($userid > 1)) {
            alert('danger', "Uh Oh!", "You cannot change the game owner's password.");
            die($h->endpage());
        }
        $ul = $db->fetch_single($db->query("/*qc=on*/SELECT `user_level` FROM `users` WHERE `userid` = {$user}"));
        if (($ul == 'Admin') && ($userid > 1)) {
            alert('danger', "Uh Oh!", "You cannot change an Administrator's password.");
            die($h->endpage());
        }
        $new_psw = $db->escape(encode_password($pw));
        $db->query("UPDATE `users` SET `password` = '{$new_psw}' WHERE `userid` = {$user}");
        alert('success', "Success!", "You have successfully changed User ID {$user}'s password.", true, 'index.php');
        $api->SystemLogsAdd($userid, 'staff', "Changed User ID {$user}'s password.");
    } else {
        $csrf = request_csrf_html('staff_changepw');
        echo "
		<form action='?action=changepw' method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						Use this form to change a user's password.
					</th>
				</tr>
				<tr>
					<th>
						User
					</th>
					<td>
						" . user_dropdown('user') . "
					</td>
				</tr>
				<tr>
					<th>
						New Password
					</th>
					<td>
						<input type='password' class='form-control' name='pw'>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-primary' value='Change Password' />
					</td>
				</tr>
			</table>
			{$csrf}
		</form>";
    }
}

function masspay()
{
    global $db, $h, $userid, $api;
    if ($api->UserMemberLevelGet($userid, 'assistant') == false) {
        alert('danger', "Uh Oh!", "You do not have permission to be here.");
        die($h->endpage());
    }
    if (isset($_POST['pay'])) {
        $primary = (isset($_POST['pay']) && is_numeric($_POST['pay'])) ? abs(intval($_POST['pay'])) : 0;
        $secondary = (isset($_POST['pay1']) && is_numeric($_POST['pay1'])) ? abs(intval($_POST['pay1'])) : 0;
        if (empty($primary) && empty($secondary)) {
            alert('danger', "Uh Oh", "If you wish to give a mass payment, please give either Copper Coins or Chivalry Tokens.");
            die($h->endpage());
        }
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_masspay', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "This action was blocked for your security. Please submit the form quickly after opening it.");
            die($h->endpage());
        }
        $q = $db->query("/*qc=on*/SELECT `userid`,`username` FROM `users` WHERE `user_level` != 'NPC'");
        while ($r = $db->fetch_row($q)) {
            $api->UserGiveCurrency($r['userid'], 'primary', $primary);
            $api->UserGiveCurrency($r['userid'], 'seconday', $secondary);
            $api->GameAddNotification($r['userid'], "The administration has given a mass payment of {$primary} Copper Coins and/or {$secondary} Chivalry Tokens to the game.");
            echo "Successfully paid {$r['username']}.<br />";
        }
        alert('success', 'Success!', "You have successfully mass paid the game.", true, 'index.php');
        $api->SystemLogsAdd($userid, 'staff', "Sent mass payment of {$primary} Primary Currecny and/or {$secondary} Chivalry Tokens.");
    } else {
        $csrf = request_csrf_html('staff_masspay');
        echo "<table class='table table-bordered'>
        <form method='post'>
        {$csrf}
            <tr>
                <th colspan='2'>
                    Fill out this form to give the game a mass payment.
                </th>
            </tr>
            <tr>
                <th>
                    Copper Coins
                </th>
                <td>
                    <input type='number' required='1' value='0' name='pay' class='form-control'>
                </td>
            </tr>
            <tr>
                <th>
                    Chivalry Tokens
                </th>
                <td>
                    <input type='number' required='1' value='0' name='pay1' class='form-control'>
                </td>
            </tr>
            <tr>
                <td colspan='2'>
                    <input type='submit' class='btn btn-primary' value='Mass Pay'
                </td>
            </tr>
        </form>
        </table>";
    }
}

function preport()
{
    global $db, $userid, $api, $h;
    if ($api->UserMemberLevelGet($userid, 'assistant') == false) {
        alert('danger', "Uh Oh!", "You do not have permission to be here.");
        die($h->endpage());
    }
    echo "<h3>Player Reports</h3><hr />";
    if (isset($_GET['ID'])) {
        $_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs(intval($_GET['ID'])) : 0;
        if (!isset($_GET['verf']) || !verify_csrf_code('staff_delreport', stripslashes($_GET['verf']))) {
            alert('danger', "Action Blocked!", "This action was blocked for your security. Please submit the form quickly after opening it.", false);
            die($h->endpage());
        } else {
            $q = $db->query("/*qc=on*/SELECT `report_id` FROM `reports` WHERE `report_id` = {$_GET['ID']}");
            if ($db->num_rows($q) == 0) {
                alert('danger', "Uh Oh!", "This report does not exist!", false);
            } else {
                $db->query("DELETE FROM `reports` WHERE `report_id` = {$_GET['ID']}");
                $api->SystemLogsAdd($userid, 'staff', "Cleared Player Report ID #{$_GET['ID']}.");
                alert('success', "Success!", "You have successfully cleared Player Report ID #{$_GET['ID']}.", false);
            }
        }
    }
    $q = $db->query("/*qc=on*/SELECT * FROM `reports`");
    $csrf = request_csrf_code('staff_delreport');
    echo "<table class='table table-bordered'>
    <tr>
        <th>
            Reporter
        </th>
        <th>
            Offender
        </th>
        <th>
            Report
        </th>
        <th>

        </th>
    </tr>";
    while ($r = $db->fetch_row($q)) {
        echo "
        <tr>
            <td>
                <a href='../profile.php?user={$r['reporter_id']}'>{$api->SystemUserIDtoName($r['reporter_id'])}</a> [{$r['reporter_id']}]
            </td>
            <td>
                <a href='../profile.php?user={$r['reportee_id']}'>{$api->SystemUserIDtoName($r['reportee_id'])}</a> [{$r['reportee_id']}]
            </td>
            <td>
                {$r['report_text']}
            </td>
            <td>
                <a href='?action=reports&ID={$r['report_id']}&verf={$csrf}' class='btn btn-primary'>Clear</a>
            </td>
        </tr>";
    }
    echo "</table>";
}

function loginasuser()
{
    global $db, $userid, $api, $h;
    $CurrentTime = time();
    if ($api->UserMemberLevelGet($userid, 'admin') == false) 
    {
        alert('danger', "Uh Oh!", "You do not have permission to be here.");
        die($h->endpage());
    }
    if (isset($_POST['user']))
    {
        $user = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs(intval($_POST['user'])) : 0;
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_user_login', stripslashes($_POST['verf']))) 
        {
            alert('danger', "Action Blocked!", "This action was blocked for your security. Please submit the form quickly after opening it.");
            die($h->endpage());
        }
        $d = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `userid` = {$user}");
        if ($db->fetch_single($d) == 0) 
        {
            $db->free_result($d);
            alert('danger', "Uh Oh!", "You are trying to log into an account that does not exist. lol.");
            die($h->endpage());
        }
        $api->SystemLogsAdd($userid, 'login', "Successfully logged out.");
        session_unset();
        session_destroy();
        
        session_name('CENGINE');
        @session_start();
        session_regenerate_id();
        $_SESSION['userid'] = $user;
        $_SESSION['loggedin'] = 1;
        $_SESSION['last_login'] = time();
        $invis=$db->fetch_single($db->query("/*qc=on*/SELECT `invis` FROM `user_settings` WHERE `userid` = {$user}"));
        if ($invis < time())
        {
            $db->query("UPDATE `users`
              SET `loginip` = '{$IP}',
              `last_login` = '{$CurrentTime}',
              `laston` = '{$CurrentTime}'
               WHERE `userid` = {$user}");
        }
        else
        {
            $db->query("UPDATE `users`
              SET `loginip` = '{$IP}'
               WHERE `userid` = {$user}");
        }
        
        $api->SystemLogsAdd($_SESSION['userid'], 'login', "Successfully logged in.");
        header("Location: ../explore.php");
    }
    else
    {
        $csrf = request_csrf_html('staff_user_login');
        echo "<div class='card'>
        Select the user you wish to login as. This can be dangerous, but can be done for whatever reason. You will have to log out 
        of whatever account you are in to log back into your own.
        <form method='post'>
            " . user_dropdown('user') . "
        <input type='submit' value='Control' class='btn btn-success btn-block'>
        {$csrf}
        </form>
        </div>";
    }
}
$h->endpage();