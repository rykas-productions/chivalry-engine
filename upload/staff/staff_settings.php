<?php
/*
	File: staff/staff_settings.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: Allows admins to view and change the game settings at will.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/
require('sglobals.php');
echo "<h3>Admin Settings</h3><hr />";
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "basicset":
        basicsettings();
        break;
    case "announce":
        announce();
        break;
    case "diagnostics":
        diagnostics();
        break;
    case "restore":
        restore();
        break;
    case "errlog":
        errlog();
        break;
    case "staff":
        staff();
        break;
    default:
        alert('danger', "Uh Oh!", "Please select a valid action to perform.", true, 'index.php');
        die($h->endpage());
        break;
}
function basicsettings()
{
    global $h, $db, $set, $api, $userid;
    if (!isset($_POST['gamename'])) {
        $csrf = request_csrf_html('staff_sett_1');
        echo "
		<div class='table-responsive'>
		<form method='post'>
		<table class='table table-bordered table-hover'>
			<tr>
				<th>
					Game's Name
				</th>
				<td width='75%'>
					<input type='text' name='gamename' class='form-control' required='1' value='{$set['WebsiteName']}'>
				</td>
			</tr>
			<tr>
				<th>
					Game's Owner
				</th>
				<td>
					<input type='text' name='ownername' class='form-control' required='1' value='{$set['WebsiteOwner']}'>
				</td>
			</tr>
			<tr>
				<th>
					Games Description'
				</th>
				<td>
					<textarea name='gamedesc' required='1' class='form-control' rows='5'>{$set['Website_Description']}</textarea>
				</td>
			</tr>
			<tr>
				<th>
					Referral Award
				</th>
				<td>
					<input type='number' name='refkb' class='form-control' min='1' required='1' value='{$set['ReferalKickback']}'>
				</td>
			</tr>
			<tr>
				<th>
					Attack Energy Usage<br />
					<small>(100 divided by this number)</small>
				</th>
				<td>
					<input type='number' name='attenc' class='form-control' min='1' required='1' value='{$set['AttackEnergyCost']}'>
				</td>
			</tr>
			<tr>
				<th>
					Max Moves per Attack
				</th>
				<td>
					<input type='number' name='attpersess' class='form-control' min='1' required='1' value='{$set['MaxAttacksPerSession']}'>
				</td>
			</tr>
			<tr>
				<th>
					Force SSL<br />
					<small>(Does nothing yet)</small>
				</th>
				<td>
					<input type='text' readonly='1' class='form-control' value='{$set['HTTPS_Support']}'>
				</td>
			</tr>
			<tr>
				<th>
					Password Effort<br />
					<small>Lower is faster and less secure.</small>
				</th>
				<td>
					<input type='number' name='PWEffort' min='5' max='20' class='form-control' value='{$set['Password_Effort']}'>
				</td>
			</tr>
			<tr>
				<th>
					PayPal Email
				</th>
				<td>
					<input type='email' class='form-control' name='ppemail' value='{$set['PaypalEmail']}'>
				</td>
			</tr>
			<tr>
				<th>
					Game Email Address<br />
					<small>This is the email address used when emails are sent from the game.</small>
				</th>
				<td>
					<input type='email' class='form-control' name='sendemail' value='{$set['sending_email']}'>
				</td>
			</tr>
			<tr>
				<th>
					Fraudguard IO Username<br />
					<small>(<a href='https://fraudguard.io/'>http://bit.ly/2apOVX0</a>)</small>
				</th>
				<td>
					<input type='text' class='form-control' name='fgun' value='{$set['FGUsername']}'>
				</td>
			</tr>
			<tr>
				<th>
					Fraudguard IO Password
				</th>
				<td>
					<input type='text' class='form-control' name='fgpw' value='{$set['FGPassword']}'>
				</td>
			</tr>
			<tr>
				<th>
					ReCaptcha Public Key<br />
					<small>(<a href='https://www.google.com/recaptcha/admin'>http://bit.ly/2oJ0Bus</a>)</small>
				</th>
				<td>
					<input type='text' class='form-control' name='rcpublic' value='{$set['reCaptcha_public']}'>
				</td>
			</tr>
			<tr>
				<th>
					ReCaptcha Private Key
				</th>
				<td>
					<input type='text' class='form-control' name='rcprivate' value='{$set['reCaptcha_private']}'>
				</td>
			</tr>
			<tr>
				<th>
					Bank Purchase Fee
				</th>
				<td>
					<input type='number' name='bankbuy' class='form-control' min='1' required='1' value='{$set['bank_cost']}'>
				</td>
			</tr>
			<tr>
				<th>
					Max Bank Deposit Fee
				</th>
				<td>
					<input type='number' name='bankfee' class='form-control' min='1' required='1' value='{$set['bank_maxfee']}'>
				</td>
			</tr>
			<tr>
				<th>
					Bank Deposit Fee
				</th>
				<td>
					<input type='number' name='bankfeepercent' class='form-control' min='1' required='1' value='{$set['bank_feepercent']}'>
				</td>
			</tr>
			<tr>
				<th>
					Guild Level Requirement
				</th>
				<td>
					<input type='number' name='guildlvl' class='form-control' min='1' required='1' value='{$set['GUILD_LEVEL']}'>
				</td>
			</tr>
			<tr>
				<th>
					Guild Cost
				</th>
				<td>
					<input type='number' name='guildcost' class='form-control' min='1' required='1' value='{$set['GUILD_PRICE']}'>
				</td>
			</tr>
			<tr>
				<th>
					Energy Refill Cost
				</th>
				<td>
					<input type='number' name='refillenergy' class='form-control' min='1' required='1' value='{$set['energy_refill_cost']}'>
				</td>
			</tr>
			<tr>
				<th>
					Will Refill Cost
				</th>
				<td>
					<input type='number' name='refillwill' class='form-control' min='1' required='1' value='{$set['will_refill_cost']}'>
				</td>
			</tr>
			<tr>
				<th>
					Bravery Refill Cost
				</th>
				<td>
					<input type='number' name='refillbrave' class='form-control' min='1' required='1' value='{$set['brave_refill_cost']}'>
				</td>
			</tr>
			<tr>
				<th>
					IQ per Secondary Currency
				</th>
				<td>
					<input type='number' name='iqpersec' class='form-control' min='1' required='1' value='{$set['iq_per_sec']}'>
				</td>
			</tr>
			<tr>
				<th>
					ReCaptcha Revalidate Time
				</th>
				<td>
					<select name='recaptchatime' class='form-control' type='dropdown'>
						<option value='300'>5 Minutes</option>
						<option value='900'>15 Minutes</option>
						<option value='3600'>1 Hour</option>
						<option value='86400'>1 Day</option>
						<option value='99999999999'>Never</option>
					</select>
				</td>
			</tr>
		</table>
		</div>";


        echo "{$csrf}
        	<input type='submit' class='btn btn-primary' value='Update Settings' />
        </form>";
        $h->endpage();
    } else {
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_sett_1', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your action was blocked for your security. Please fill out the form quickly next time.");
            die($h->endpage());
        }
        $GameName = (isset($_POST['gamename']) && preg_match("/^[a-z0-9_.]+([\\s]{1}[a-z0-9_.]|[a-z0-9_.])+$/i", $_POST['gamename'])) ? $db->escape(strip_tags(stripslashes($_POST['gamename']))) : '';
        $RefAward = (isset($_POST['refkb']) && is_numeric($_POST['refkb'])) ? abs(intval($_POST['refkb'])) : '';
        $AttackEnergy = (isset($_POST['attenc']) && is_numeric($_POST['attenc'])) ? abs(intval($_POST['attenc'])) : '';
        $sendemail = (isset($_POST['ppemail']) && filter_input(INPUT_POST, 'sendemail', FILTER_VALIDATE_EMAIL)) ? $db->escape(stripslashes($_POST['sendemail'])) : '';
        $Paypal = (isset($_POST['ppemail']) && filter_input(INPUT_POST, 'ppemail', FILTER_VALIDATE_EMAIL)) ? $db->escape(stripslashes($_POST['ppemail'])) : '';
        $GameOwner = (isset($_POST['ownername']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $_POST['ownername'])) ? $db->escape(strip_tags(stripslashes($_POST['ownername']))) : '';
        $GameDesc = (isset($_POST['gamedesc'])) ? $db->escape(strip_tags(stripslashes($_POST['gamedesc']))) : '';
        $FGPW = (isset($_POST['fgpw'])) ? $db->escape(strip_tags(stripslashes($_POST['fgpw']))) : '';
        $FGUN = (isset($_POST['fgun'])) ? $db->escape(strip_tags(stripslashes($_POST['fgun']))) : '';
        $rcpb = (isset($_POST['rcpublic'])) ? $db->escape(strip_tags(stripslashes($_POST['rcpublic']))) : '';
        $rcpr = (isset($_POST['rcprivate'])) ? $db->escape(strip_tags(stripslashes($_POST['rcprivate']))) : '';
        $PasswordEffort = (isset($_POST['PWEffort']) && is_numeric($_POST['PWEffort'])) ? abs(intval($_POST['PWEffort'])) : 10;
        $BankFeePerc = (isset($_POST['bankfeepercent']) && is_numeric($_POST['bankfeepercent'])) ? abs(intval($_POST['bankfeepercent'])) : 10;
        $BankFeeMax = (isset($_POST['bankfee']) && is_numeric($_POST['bankfee'])) ? abs(intval($_POST['bankfee'])) : 5000;
        $BankCost = (isset($_POST['bankbuy']) && is_numeric($_POST['bankbuy'])) ? abs(intval($_POST['bankbuy'])) : 5000;
        $recaptchatime = (isset($_POST['recaptchatime']) && is_numeric($_POST['recaptchatime'])) ? abs(intval($_POST['recaptchatime'])) : 3600;
        $sessiontimeout = (isset($_POST['sessiontimeout']) && is_numeric($_POST['sessiontimeout'])) ? abs(intval($_POST['sessiontimeout'])) : 15;
        $attpersess = (isset($_POST['attpersess']) && is_numeric($_POST['attpersess'])) ? abs(intval($_POST['attpersess'])) : 50;
        $guildcost = (isset($_POST['guildcost']) && is_numeric($_POST['guildcost'])) ? abs(intval($_POST['guildcost'])) : 500000;
        $guildlvl = (isset($_POST['guildlvl']) && is_numeric($_POST['guildlvl'])) ? abs(intval($_POST['guildlvl'])) : 25;
        $refillenergy = (isset($_POST['refillenergy']) && is_numeric($_POST['refillenergy'])) ? abs(intval($_POST['refillenergy'])) : 10;
        $refillbrave = (isset($_POST['refillbrave']) && is_numeric($_POST['refillbrave'])) ? abs(intval($_POST['refillbrave'])) : 10;
        $refillwill = (isset($_POST['refillwill']) && is_numeric($_POST['refillwill'])) ? abs(intval($_POST['refillwill'])) : 5;
        $iqpersec = (isset($_POST['iqpersec']) && is_numeric($_POST['iqpersec'])) ? abs(intval($_POST['iqpersec'])) : 5;
        if (empty($GameName)) {
            alert('danger', "Uh Oh!", "Please specify a game name.");
            die($h->endpage());
        } elseif (empty($Paypal)) {
            alert('danger', "Uh Oh!", "Please specify a PayPal account.");
            die($h->endpage());
        } elseif (empty($GameOwner)) {
            alert('danger', "Uh Oh!", "Please specify a game owner.");
            die($h->endpage());
        } elseif (empty($RefAward)) {
            alert('danger', "Uh Oh!", "Please specify a referral award.");
            die($h->endpage());
        } elseif (empty($GameDesc)) {
            alert('danger', "Uh Oh!", "Please specify a game description.");
            die($h->endpage());
        } elseif (empty($AttackEnergy)) {
            alert('danger', "Uh Oh!", "Please specify the attack energy usage.");
            die($h->endpage());
        } elseif (empty($FGPW)) {
            alert('danger', "Uh Oh!", "Please specify your Fraud Guard IO Password.");
            die($h->endpage());
        } elseif (empty($FGUN)) {
            alert('danger', "Uh Oh!", "Please specify your Fraud Guard IO Username");
            die($h->endpage());
        } elseif (empty($rcpb)) {
            alert('danger', "Uh Oh!", "Please specify your ReCaptcha Public Key.");
            die($h->endpage());
        } elseif (empty($rcpr)) {
            alert('danger', "Uh Oh!", "Please specify your ReCaptcha Private Key.");
            die($h->endpage());
        } elseif (empty($PasswordEffort) || $PasswordEffort < 5 || $PasswordEffort > 20) {
            alert('danger', "Uh Oh!", "Please specify a password effort between 5 and 20.");
            die($h->endpage());
        } elseif ($recaptchatime < 300) {
            alert('danger', "Uh Oh!", "Please specify a ReCaptcha time that isn't less than 5 minutes.");
            die($h->endpage());
        } else {
            $db->query("UPDATE `settings` SET `setting_value` = {$RefAward} WHERE `setting_name` = 'ReferalKickback'");
            $db->query("UPDATE `settings` SET `setting_value` = {$AttackEnergy} WHERE `setting_name` = 'AttackEnergyCost'");
            $db->query("UPDATE `settings` SET `setting_value` = {$PasswordEffort} WHERE `setting_name` = 'Password_Effort'");
            $db->query("UPDATE `settings` SET `setting_value` = '{$GameName}' WHERE `setting_name` = 'WebsiteName'");
            $db->query("UPDATE `settings` SET `setting_value` = '{$GameDesc}' WHERE `setting_name` = 'Website_Description'");
            $db->query("UPDATE `settings` SET `setting_value` = '{$GameOwner}' WHERE `setting_name` = 'Website_Owner'");
            $db->query("UPDATE `settings` SET `setting_value` = '{$Paypal}' WHERE `setting_name` = 'PaypalEmail'");
            $db->query("UPDATE `settings` SET `setting_value` = '{$FGPW}' WHERE `setting_name` = 'FGPW'");
            $db->query("UPDATE `settings` SET `setting_value` = '{$FGUN}' WHERE `setting_name` = 'FGUN'");
            $db->query("UPDATE `settings` SET `setting_value` = '{$BankCost}' WHERE `setting_name` = 'bank_cost'");
            $db->query("UPDATE `settings` SET `setting_value` = '{$BankFeeMax}' WHERE `setting_name` = 'bank_maxfee'");
            $db->query("UPDATE `settings` SET `setting_value` = '{$BankFeePerc}' WHERE `setting_name` = 'bank_feepercent'");
            $db->query("UPDATE `settings` SET `setting_value` = '{$rcpb}' WHERE `setting_name` = 'reCaptcha_public'");
            $db->query("UPDATE `settings` SET `setting_value` = '{$rcpr}' WHERE `setting_name` = 'reCaptcha_private'");
            $db->query("UPDATE `settings` SET `setting_value` = '{$sessiontimeout}' WHERE `setting_name` = 'max_sessiontime'");
            $db->query("UPDATE `settings` SET `setting_value` = '{$recaptchatime}' WHERE `setting_name` = 'Revalidate_Time'");
            $db->query("UPDATE `settings` SET `setting_value` = '{$attpersess}' WHERE `setting_name` = 'MaxAttacksPerSession'");
            $db->query("UPDATE `settings` SET `setting_value` = '{$guildcost}' WHERE `setting_name` = 'GUILD_PRICE'");
            $db->query("UPDATE `settings` SET `setting_value` = '{$guildlvl}' WHERE `setting_name` = 'GUILD_LEVEL'");
            $db->query("UPDATE `settings` SET `setting_value` = '{$refillenergy}' WHERE `setting_name` = 'energy_refill_cost'");
            $db->query("UPDATE `settings` SET `setting_value` = '{$refillbrave}' WHERE `setting_name` = 'brave_refill_cost'");
            $db->query("UPDATE `settings` SET `setting_value` = '{$refillwill}' WHERE `setting_name` = 'will_refill_cost'");
            $db->query("UPDATE `settings` SET `setting_value` = '{$iqpersec}' WHERE `setting_name` = 'iq_per_sec'");
            $db->query("UPDATE `settings` SET `setting_value` = '{$sendemail}' WHERE `setting_name` = 'sending_email'");
            alert('success', "Success!", "You have successfully updated the game settings.", true, 'index.php');
            $api->SystemLogsAdd($userid, 'staff', "Updated game settings.");
        }
        $h->endpage();
    }
}

function announce()
{
    global $db, $userid, $h, $api;
    if (!isset($_POST['announcement'])) {
        $csrf = request_csrf_html('staff_announce');
        echo "Use this form to post an announcement to the game. Please be sure you are clear and concise with your
        wording. Do not spam if possible.<br />
		<form method='post'>
			<textarea name='announcement' rows='5' class='form-control'></textarea>
			<input type='submit' class='btn btn-primary' value='Create Announcement'>
			{$csrf}
		</form>";
    } else {
        if (empty($_POST['announcement'])) {
            alert('danger', "Uh Oh!", "Please enter announcement text.");
            die($h->endpage());
        } else {
            if (!isset($_POST['verf']) || !verify_csrf_code('staff_announce', stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "Your action was blocked for your security. Please fill out the form quickly next time.");
                die($h->endpage());
            }
            $time = time();
            $_POST['announcement'] = $db->escape(str_replace("\n", "<br />", strip_tags(stripslashes($_POST['announcement']))));
            $db->query("INSERT INTO `announcements` (`ann_id`, `ann_text`, `ann_time`, `ann_poster`)
			VALUES (NULL, '{$_POST['announcement']}', '{$time}', '{$userid}');");
            $db->query("UPDATE `users` SET `announcements` = `announcements` + 1");
            alert('success', "Success!", "You have successfully posted an announcement.", true, '../announcements.php');
            $api->SystemLogsAdd($userid, 'staff', "Posted an announcement.");
        }
    }
    $h->endpage();
}

function diagnostics()
{
    global $h, $userid, $api;
    $dir = substr(__DIR__, 0, strpos(__DIR__, "\staff"));
    if (version_compare(phpversion(), '5.5.0') < 0) {
        $pv = "<span style='color: red'>Fail</span>";
    } else {
        $pv = "<span style='color: green'>Success</span>";
    }
    if (is_writable('./')) {
        $wv = "<span style='color: green'>Success</span>";
    } else {
        $wv = "<span style='color: red'>Fail</span>";
    }
    if (function_exists('mysqli_connect')) {
        $dv = "<span style='color: green'>Success</span>";
    } else {
        $dv = "<span style='color: red'>Fail</span>";
    }
    if (extension_loaded('pdo')) {
        $pdv = "<span style='color: green'>Success</span>";
    } else {
        $pdv = "<span style='color: red'>Fail</span>";
    }
    if (function_exists('openssl_random_pseudo_bytes')) {
        $ov = "<span style='color: green'>Success</span>";
    } else {
        $ov = "<span style='color: red'>Fail</span>";
    }
    if (function_exists('password_hash')) {
        $hv = '<span style="color: green">Pass! Using stronger password hash method.</span>';
        $hvf = 1;
    } else {
        $hv = '<span style="color: red">Failed...</span>';
        $hvf = 0;
    }
    if (function_exists('curl_init')) {
        $cuv = "<span style='color: green'>Success</span>";
    } else {
        $cuv = "<span style='color: red'>Fail</span>";
    }
    if (function_exists('fopen')) {
        $fov = "<span style='color: green'>Success</span>";
    } else {
        $fov = "<span style='color: red'>Fail</span>";
    }
    echo "<table class='table table-bordered table-hover'>
    		<tr>
    			<td>Server PHP Version Greater than 5.5</td>
    			<td>{$pv}</td>
    		</tr>
    		<tr>
    			<td>Server Folder Writable</td>
    			<td>{$wv}</td>
    		</tr>
			<tr>
    			<td>PDO Detected?</td>
    			<td>{$pdv}</td>
    		</tr>
    		<tr>
    			<td>MySQLi Detected?</td>
    			<td>{$dv}</td>
    		</tr>
			<tr>
    			<td>Password Function Detected?</td>
    			<td>{$hv}</td>
    		</tr>
			<tr>
    			<td>OpenSSL Detected?</td>
    			<td>{$ov}</td>
    		</tr>
			<tr>
    			<td>cURL Detected?</td>
    			<td>{$cuv}</td>
    		</tr>
			<tr>
    			<td>fopen Detected?</td>
    			<td>{$fov}</td>
    		</tr>
    		<tr>
    			<td>Chivalry Engine Update Checker</td>
    			<td>
        			" . version_json() . "
        		</td>
        	</tr>
    </table>
       ";
    $api->SystemLogsAdd($userid, 'staff', "Viewed game diagnostics.");
    $h->endpage();
}

function restore()
{
    global $db, $h, $api, $userid;
    if (!isset($_POST['restore'])) {
        echo "Click this button to restore everyone's energy/brave/will/etc to 100%, and remove everyone from the
        dungeon/infirmary.<br />
		<form method='post'>
			<input type='submit' name='restore' value='Restore' class='btn btn-primary'>
		</form>";
        $h->endpage();
    } else {
        $db->query("UPDATE `users` SET `hp`=`maxhp`,`energy`=`maxenergy`,`brave`=`maxbrave`,`will`=`maxwill`");
        $db->query("UPDATE `dungeon` SET `dungeon_out` = 0");
        $db->query("UPDATE `infirmary` SET `infirmary_out` = 0");
        $api->SystemLogsAdd($userid, 'staff', "Restored all users.");
        alert('success', "Success!", "You have restored your player base.", true, 'index.php');
        $h->endpage();
    }
}

function errlog()
{
    global $api, $userid, $h;
    $api->SystemLogsAdd($userid, 'staff', "Viewed the error log.");
    echo "
	<textarea class='form-control' rows='20' readonly='1'>" . file_get_contents("../error_log") . "</textarea>";
    $h->endpage();
}

function staff()
{
    global $db, $api, $h;
    if (isset($_POST['user'])) {
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_priv', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your action was blocked for your security. Please fill out the form quickly next time.");
            die($h->endpage());
        }
        $_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs(intval($_POST['user'])) : 0;
        $_POST['priv'] = (isset($_POST['priv'])) ? $db->escape(strip_tags(stripslashes($_POST['priv']))) : 'member';
        $priv_array = array('Member', 'Admin', 'Web Developer', 'Forum Moderator', 'Assistant', 'NPC');
        if (!in_array($_POST['priv'], $priv_array)) {
            alert('danger', "Uh Oh!", "You are trying to give an invalid privilege.");
            die($h->endpage());
        }
        if (!($api->SystemUserIDtoName($_POST['user']))) {
            alert('danger', "Uh Oh!", "Please specify an existent user.");
            die($h->endpage());
        }
        $api->UserInfoSetStatic($_POST['user'], 'user_level', $_POST['priv']);
        alert('success', "Success!", "You have updated {$api->SystemUserIDtoName($_POST['user'])}'s User Level to {$_POST['priv']}.");
        die($h->endpage());
    } else {
        $csrf = request_csrf_html('staff_priv');
        echo "<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						Select the user you wish to change their user level.
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
						User Level
					</th>
					<td>
						<select name='priv' class='form-control'>
							<option value='NPC'>NPC</option>
							<option value='Member'>Member</option>
							<option value='Forum Moderator'>Forum Moderator</option>
							<option value='Assistant'>Assistant</option>
							<option value='Web Developer'>Web Developer</option>
							<option value='Admin'>Administrator</option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-primary' value='Update User Level'>
					</td>
				</tr>
			</table>
			{$csrf}
		</form>";
        $h->endpage();
    }
}