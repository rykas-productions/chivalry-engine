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
if ($api->UserMemberLevelGet($userid, 'Admin') == false) {
    alert('danger', "Uh Oh!", "You do not have permission to be here.");
    die($h->endpage());
}
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
    if (!isset($_POST['WebsiteName'])) {
        $csrf = request_csrf_html('staff_sett_1');
        echo "
		<div class='table-responsive'>
		<form method='post'>
		<table class='table table-bordered table-hover'>
			<tr>
				<th>
					Game Name
				</th>
				<td width='75%'>
					<input type='text' name='WebsiteName' class='form-control' required='1' value='{$set['WebsiteName']}'>
				</td>
			</tr>
			<tr>
				<th>
					Game Owner
				</th>
				<td>
					<input type='text' name='WebsiteOwner' class='form-control' required='1' value='{$set['WebsiteOwner']}'>
				</td>
			</tr>
			<tr>
				<th>
					Game Description
				</th>
				<td>
					<textarea name='Website_Description' required='1' class='form-control' rows='5'>{$set['Website_Description']}</textarea>
				</td>
			</tr>
			<tr>
				<th>
					Referral Award
				</th>
				<td>
					<input type='number' name='ReferalKickback' class='form-control' min='1' required='1' value='{$set['ReferalKickback']}'>
				</td>
			</tr>
			<tr>
				<th>
					Attack Energy Usage<br />
					<small>(100 divided by this number)</small>
				</th>
				<td>
					<input type='number' name='AttackEnergyCost' class='form-control' min='1' required='1' value='{$set['AttackEnergyCost']}'>
				</td>
			</tr>
			<tr>
				<th>
					Max Moves per Attack
				</th>
				<td>
					<input type='number' name='MaxAttacksPerSession' class='form-control' min='1' required='1' value='{$set['MaxAttacksPerSession']}'>
				</td>
			</tr>
			<tr>
				<th>
					Password Effort<br />
					<small>Lower is faster and less secure.</small>
				</th>
				<td>
					<input type='number' name='Password_Effort' min='5' max='20' class='form-control' value='{$set['Password_Effort']}'>
				</td>
			</tr>
			<tr>
				<th>
					PayPal Email
				</th>
				<td>
					<input type='email' class='form-control' name='PaypalEmail' value='{$set['PaypalEmail']}'>
				</td>
			</tr>
			<tr>
				<th>
					Game Email Address<br />
					<small>This is the email address used when emails are sent from the game.</small>
				</th>
				<td>
					<input type='email' class='form-control' name='sending_email' value='{$set['sending_email']}'>
				</td>
			</tr>
			<tr>
				<th>
					Fraudguard IO Username<br />
					<small>(<a href='https://fraudguard.io/'>http://bit.ly/2apOVX0</a>)</small>
				</th>
				<td>
					<input type='text' class='form-control' name='FGUsername' value='{$set['FGUsername']}'>
				</td>
			</tr>
			<tr>
				<th>
					Fraudguard IO Password
				</th>
				<td>
					<input type='text' class='form-control' name='FGPassword' value='{$set['FGPassword']}'>
				</td>
			</tr>
			<tr>
				<th>
					ReCaptcha Public Key<br />
					<small>(<a href='https://www.google.com/recaptcha/admin'>http://bit.ly/2oJ0Bus</a>)</small>
				</th>
				<td>
					<input type='text' class='form-control' name='reCaptcha_public' value='{$set['reCaptcha_public']}'>
				</td>
			</tr>
			<tr>
				<th>
					ReCaptcha Private Key
				</th>
				<td>
					<input type='text' class='form-control' name='reCaptcha_private' value='{$set['reCaptcha_private']}'>
				</td>
			</tr>
			<tr>
				<th>
					Bank Purchase Fee
				</th>
				<td>
					<input type='number' name='bank_cost' class='form-control' min='1' required='1' value='{$set['bank_cost']}'>
				</td>
			</tr>
			<tr>
				<th>
					Max Bank Deposit Fee
				</th>
				<td>
					<input type='number' name='bank_maxfee' class='form-control' min='1' required='1' value='{$set['bank_maxfee']}'>
				</td>
			</tr>
			<tr>
				<th>
					Bank Deposit Fee
				</th>
				<td>
					<input type='number' name='bankfee_percent' class='form-control' min='1' required='1' value='{$set['bankfee_percent']}'>
				</td>
			</tr>
			<tr>
				<th>
					Guild Level Requirement
				</th>
				<td>
					<input type='number' name='GUILD_LEVEL' class='form-control' min='1' required='1' value='{$set['GUILD_LEVEL']}'>
				</td>
			</tr>
			<tr>
				<th>
					Guild Cost
				</th>
				<td>
					<input type='number' name='GUILD_PRICE' class='form-control' min='1' required='1' value='{$set['GUILD_PRICE']}'>
				</td>
			</tr>
			<tr>
				<th>
					Raffle Winner
				</th>
				<td>
					" . user_dropdown('raffle_last_winner', $set['raffle_last_winner']) . "
				</td>
			</tr>
			<tr>
				<th>
					Raffle Cash
				</th>
				<td>
					<input type='number' name='lotterycash' class='form-control' min='1' required='1' value='{$set['lotterycash']}'>
				</td>
			</tr>
			<tr>
				<th>
					Raffle Chance<br />
					<small>1 in X chance.</small>
				</th>
				<td>
					<input type='number' name='raffle_chance' class='form-control' min='1' required='1' value='{$set['raffle_chance']}'>
				</td>
			</tr>
			<tr>
				<th>
					Energy Refill Cost
				</th>
				<td>
					<input type='number' name='energy_refill_cost' class='form-control' min='1' required='1' value='{$set['energy_refill_cost']}'>
				</td>
			</tr>
			<tr>
				<th>
					Will Refill Cost
				</th>
				<td>
					<input type='number' name='will_refill_cost' class='form-control' min='1' required='1' value='{$set['will_refill_cost']}'>
				</td>
			</tr>
			<tr>
				<th>
					Bravery Refill Cost
				</th>
				<td>
					<input type='number' name='brave_refill_cost' class='form-control' min='1' required='1' value='{$set['brave_refill_cost']}'>
				</td>
			</tr>
			<tr>
				<th>
					IQ per Chivalry Tokens
				</th>
				<td>
					<input type='number' name='iq_per_sec' class='form-control' min='1' required='1' value='{$set['iq_per_sec']}'>
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
			<tr>
				<th>
					Game Time Zone
				</th>
				<td>
					<input type='text' name='game_time' class='form-control' required='1' value='{$set['game_time']}'>
				</td>
			</tr>
			<tr>
				<th colspan='2'>
					Version Control
				</th>
			</tr>
			<tr>
				<th>
					Chivalry Engine Version
				</th>
				<td>
					<input type='text' name='Version_Number' class='form-control' required='1' value='{$set['Version_Number']}'>
				</td>
			</tr>
			<tr>
				<th>
					Chivalry Engine Build Number
				</th>
				<td>
					<input type='number' name='BuildNumber' class='form-control' required='1' value='{$set['BuildNumber']}'>
				</td>
			</tr>
			<tr>
				<th>
					Bootstrap
				</th>
				<td>
					<input type='text' name='bootstrap_version' class='form-control' required='1' value='{$set['bootstrap_version']}'>
				</td>
			</tr>
			<tr>
				<th>
					Popper
				</th>
				<td>
					<input type='text' name='popper_version' class='form-control' required='1' value='{$set['popper_version']}'>
				</td>
			</tr>
			<tr>
				<th>
					Font Awesome
				</th>
				<td>
					<input type='text' name='fontawesome_version' class='form-control' required='1' value='{$set['fontawesome_version']}'>
				</td>
			</tr>
			<tr>
				<th>
					Bootstrap Hover Tabs
				</th>
				<td>
					<input type='text' name='bshover_tabs_version' class='form-control' required='1' value='{$set['bshover_tabs_version']}'>
				</td>
			</tr>
			<tr>
				<th>
					Game CSS
				</th>
				<td>
					<input type='text' name='game_css_version' class='form-control' required='1' value='{$set['game_css_version']}'>
				</td>
			</tr>
			<tr>
				<th>
					Game JS
				</th>
				<td>
					<input type='text' name='game_js_version' class='form-control' required='1' value='{$set['game_js_version']}'>
				</td>
			</tr>
            <tr>
				<th>
					Game Audio
				</th>
				<td>
					<input type='text' name='game_audio_version' class='form-control' required='1' value='{$set['game_audio_version']}'>
				</td>
			</tr>
			<tr>
				<th>
					jQuery
				</th>
				<td>
					<input type='text' name='jquery_version' class='form-control' required='1' value='{$set['jquery_version']}'>
				</td>
			</tr>
            <tr>
				<th colspan='2'>
					Version Control
				</th>
			</tr>
            <tr>
				<th>
					Chivalry Token Minimum Price<br />
                    <small>Min. to sell/buy. Caps Temple/Market.</small>
				</th>
				<td>
					<input type='number' name='token_minimum' class='form-control' required='1' value='{$set['token_minimum']}'>
				</td>
			</tr>
            <tr>
				<th>
					Chivalry Token Maximum Price<br />
                    <small>Max to sell/buy. Caps Temple/Market.</small>
				</th>
				<td>
					<input type='number' name='token_maximum' class='form-control' required='1' value='{$set['token_maximum']}'>
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
        $_POST['WebsiteName'] = (isset($_POST['WebsiteName']) && preg_match("/^[a-z0-9_.]+([\\s]{1}[a-z0-9_.]|[a-z0-9_.])+$/i", $_POST['WebsiteName'])) ? $db->escape(strip_tags(stripslashes($_POST['WebsiteName']))) : '';
        $_POST['ReferalKickback'] = (isset($_POST['ReferalKickback']) && is_numeric($_POST['ReferalKickback'])) ? abs(intval($_POST['ReferalKickback'])) : '';
        $_POST['AttackEnergyCost'] = (isset($_POST['AttackEnergyCost']) && is_numeric($_POST['AttackEnergyCost'])) ? abs(intval($_POST['AttackEnergyCost'])) : '';
        $_POST['sending_email'] = (isset($_POST['sending_email']) && filter_input(INPUT_POST, 'sending_email', FILTER_VALIDATE_EMAIL)) ? $db->escape(stripslashes($_POST['sending_email'])) : '';
        $_POST['PaypalEmail'] = (isset($_POST['PaypalEmail']) && filter_input(INPUT_POST, 'PaypalEmail', FILTER_VALIDATE_EMAIL)) ? $db->escape(stripslashes($_POST['PaypalEmail'])) : '';
        $_POST['WebsiteOwner'] = (isset($_POST['WebsiteOwner']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $_POST['WebsiteOwner'])) ? $db->escape(strip_tags(stripslashes($_POST['WebsiteOwner']))) : '';
        $_POST['Website_Description'] = (isset($_POST['Website_Description'])) ? $db->escape(strip_tags(stripslashes($_POST['Website_Description']))) : '';
		$_POST['FGPassword'] = (isset($_POST['FGPassword'])) ? $db->escape(strip_tags(stripslashes($_POST['FGPassword']))) : '';
        $_POST['FGUsername'] = (isset($_POST['FGUsername'])) ? $db->escape(strip_tags(stripslashes($_POST['FGUsername']))) : '';
        $_POST['reCaptcha_public'] = (isset($_POST['reCaptcha_public'])) ? $db->escape(strip_tags(stripslashes($_POST['reCaptcha_public']))) : '';
        $_POST['reCaptcha_private'] = (isset($_POST['reCaptcha_private'])) ? $db->escape(strip_tags(stripslashes($_POST['reCaptcha_private']))) : '';
        $_POST['Password_Effort'] = (isset($_POST['Password_Effort']) && is_numeric($_POST['Password_Effort'])) ? abs(intval($_POST['Password_Effort'])) : 10;
        $_POST['bank_feepercent'] = (isset($_POST['bank_feepercent']) && is_numeric($_POST['bank_feepercent'])) ? abs(intval($_POST['bank_feepercent'])) : 10;
        $_POST['bank_maxfee'] = (isset($_POST['bank_maxfee']) && is_numeric($_POST['bank_maxfee'])) ? abs(intval($_POST['bank_maxfee'])) : 5000;
        $_POST['bank_cost'] = (isset($_POST['bank_cost']) && is_numeric($_POST['bank_cost'])) ? abs(intval($_POST['bank_cost'])) : 5000;
        $_POST['Revalidate_Time'] = (isset($_POST['Revalidate_Time']) && is_numeric($_POST['Revalidate_Time'])) ? abs(intval($_POST['Revalidate_Time'])) : 3600;
        $_POST['sessiontimeout'] = (isset($_POST['sessiontimeout']) && is_numeric($_POST['sessiontimeout'])) ? abs(intval($_POST['sessiontimeout'])) : 15;
        $_POST['MaxAttacksPerSession'] = (isset($_POST['MaxAttacksPerSession']) && is_numeric($_POST['MaxAttacksPerSession'])) ? abs(intval($_POST['MaxAttacksPerSession'])) : 50;
        $_POST['GUILD_PRICE'] = (isset($_POST['GUILD_PRICE']) && is_numeric($_POST['GUILD_PRICE'])) ? abs(intval($_POST['GUILD_PRICE'])) : 500000;
        $_POST['GUILD_LEVEL'] = (isset($_POST['GUILD_LEVEL']) && is_numeric($_POST['GUILD_LEVEL'])) ? abs(intval($_POST['GUILD_LEVEL'])) : 25;
        $_POST['energy_refill_cost'] = (isset($_POST['energy_refill_cost']) && is_numeric($_POST['energy_refill_cost'])) ? abs(intval($_POST['energy_refill_cost'])) : 10;
        $_POST['brave_refill_cost'] = (isset($_POST['brave_refill_cost']) && is_numeric($_POST['brave_refill_cost'])) ? abs(intval($_POST['brave_refill_cost'])) : 10;
        $_POST['will_refill_cost'] = (isset($_POST['will_refill_cost']) && is_numeric($_POST['will_refill_cost'])) ? abs(intval($_POST['will_refill_cost'])) : 5;
        $_POST['iq_per_sec'] = (isset($_POST['iq_per_sec']) && is_numeric($_POST['iq_per_sec'])) ? abs(intval($_POST['iq_per_sec'])) : 5;
        //End norm
		//Start versions
		$_POST['bootstrap_version'] = (isset($_POST['bootstrap_version'])) ? $db->escape(strip_tags(stripslashes($_POST['bootstrap_version']))) : '4.4.1';
		$_POST['popper_version'] = (isset($_POST['popper_version'])) ? $db->escape(strip_tags(stripslashes($_POST['popper_version']))) : '1.16.0';
		$_POST['fontawesome_version'] = (isset($_POST['fontawesome_version'])) ? $db->escape(strip_tags(stripslashes($_POST['fontawesome_version']))) : '5.11.2';
		$_POST['bshover_tabs_version'] = (isset($_POST['bshover_tabs_version'])) ? $db->escape(strip_tags(stripslashes($_POST['bshover_tabs_version']))) : '3.1.1';
		$_POST['game_js_version'] = (isset($_POST['game_js_version'])) ? $db->escape(strip_tags(stripslashes($_POST['game_js_version']))) : '21.1.3';
		$_POST['game_css_version'] = (isset($_POST['game_css_version'])) ? $db->escape(strip_tags(stripslashes($_POST['game_css_version']))) : '20.4.1';
		$_POST['jquery_version'] = (isset($_POST['jquery_version'])) ? $db->escape(strip_tags(stripslashes($_POST['jquery_version']))) : '3.4.1';
		$_POST['game_audio_version'] = (isset($_POST['game_audio_version'])) ? $db->escape(strip_tags(stripslashes($_POST['game_audio_version']))) : '21.1.1';
		
		$_POST['BuildNumber'] = (isset($_POST['BuildNumber'])) ? $db->escape(strip_tags(stripslashes($_POST['BuildNumber']))) : '0';
		$_POST['Version_Number'] = (isset($_POST['Version_Number'])) ? $db->escape(strip_tags(stripslashes($_POST['Version_Number']))) : '1.0.0';
		$_POST['raffle_chance'] = (isset($_POST['raffle_chance']) && is_numeric($_POST['raffle_chance'])) ? abs(intval($_POST['raffle_chance'])) : 1000;
		$_POST['lotterycash'] = (isset($_POST['lotterycash']) && is_numeric($_POST['lotterycash'])) ? abs(intval($_POST['lotterycash'])) : 100000;
		$_POST['raffle_last_winner'] = (isset($_POST['raffle_last_winner']) && is_numeric($_POST['raffle_last_winner'])) ? abs(intval($_POST['raffle_last_winner'])) : 1;
		
		$_POST['game_time'] = (isset($_POST['game_time'])) ? $db->escape(strip_tags(stripslashes($_POST['game_time']))) : 'America/New_York';
		
		$_POST['token_maximum'] = (isset($_POST['token_maximum']) && is_numeric($_POST['token_maximum'])) ? abs(intval($_POST['iq_per_sec'])) : 50000;
		$_POST['token_minimum'] = (isset($_POST['token_minimum']) && is_numeric($_POST['token_minimum'])) ? abs(intval($_POST['token_minimum'])) : 50000;
		
		if (empty($_POST['WebsiteName'])) {
            alert('danger', "Uh Oh!", "Please specify a game name.");
            die($h->endpage());
        } elseif (empty($_POST['PaypalEmail'])) {
            alert('danger', "Uh Oh!", "Please specify a PayPal account.");
            die($h->endpage());
        } elseif (empty($_POST['WebsiteOwner'])) {
            alert('danger', "Uh Oh!", "Please specify a game owner.");
            die($h->endpage());
        } elseif (empty($_POST['ReferalKickback'])) {
            alert('danger', "Uh Oh!", "Please specify a referral award.");
            die($h->endpage());
        } elseif (empty($_POST['Website_Description'])) {
            alert('danger', "Uh Oh!", "Please specify a game description.");
            die($h->endpage());
        } elseif (empty($_POST['AttackEnergyCost'])) {
            alert('danger', "Uh Oh!", "Please specify the attack energy usage.");
            die($h->endpage());
        } elseif (empty($_POST['FGPassword'])) {
            alert('danger', "Uh Oh!", "Please specify your Fraud Guard IO Password.");
            die($h->endpage());
        } elseif (empty($_POST['FGUsername'])) {
            alert('danger', "Uh Oh!", "Please specify your Fraud Guard IO Username");
            die($h->endpage());
        } elseif (empty($_POST['reCaptcha_public'])) {
            alert('danger', "Uh Oh!", "Please specify your ReCaptcha Public Key.");
            die($h->endpage());
        } elseif (empty($_POST['reCaptcha_private'])) {
            alert('danger', "Uh Oh!", "Please specify your ReCaptcha Private Key.");
            die($h->endpage());
        } elseif (empty($_POST['Password_Effort']) || $_POST['Password_Effort'] < 5 || $_POST['Password_Effort'] > 20) {
            alert('danger', "Uh Oh!", "Please specify a password effort between 5 and 20.");
            die($h->endpage());
        } elseif ($_POST['Revalidate_Time'] < 300) {
            alert('danger', "Uh Oh!", "Please specify a ReCaptcha time that isn't less than 5 minutes.");
            die($h->endpage());
        } else {
			foreach ($_POST as $k => $v)
			{
				$db->query(
						"UPDATE `settings`
						 SET `setting_value` = '{$v}'
						 WHERE `setting_name` = '{$k}'");
			}
			alert('success', "Success!", "You have successfully updated the game settings.", true, 'index.php');
            $api->SystemLogsAdd($userid, 'staff', "Updated game settings.");
        }
        $h->endpage();
    }
}

function announce()
{
    global $db, $userid, $h, $api, $ir;
    require_once '../lib/DiscordMsg/Msg.php';
    require_once '../lib/DiscordMsg/DiscordMsg.php';
    if (!isset($_POST['announcement'])) {
        $csrf = request_csrf_html('staff_announce');
        echo "Use this form to post an announcement to the game. Please be sure you are clear and concise with your
        wording. Do not spam if possible.<br />
		<form method='post'>
			<textarea name='announcement' class='form-control'></textarea>
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
            $msg = new \AG\DiscordMsg("New Announcement from {$ir['username']}: {$_POST['announcement']}");
            $msg->send();
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