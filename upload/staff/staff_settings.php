<?php
/*
	File: staff/staff_settings.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: Allows admins to view and change the game settings at will.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/
require('sglobals.php');
echo "<h3>Admin</h3><hr />";
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
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
    die();
    break;
}
function basicsettings()
{
	global $h,$ir,$db,$lang,$set,$api,$userid;
	if (!isset($_POST['gamename']))
	{
		$csrf=request_csrf_html('staff_sett_1');
		echo "
		<div class='table-responsive'>
		<form method='post'>
		<table class='table table-bordered table-hover'>
			<tr>
				<th>
					{$lang['SS_GAME']}
				</th>
				<td width='75%'>
					<input type='text' name='gamename' class='form-control' required='1' value='{$set['WebsiteName']}'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['SS_NAME']}
				</th>
				<td>
					<input type='text' name='ownername' class='form-control' required='1' value='{$set['WebsiteOwner']}'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['SS_DESC']}
				</th>
				<td>
					<textarea name='gamedesc' required='1' class='form-control' rows='5'>{$set['Website_Description']}</textarea>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['SS_REF']}
				</th>
				<td>
					<input type='number' name='refkb' class='form-control' min='1' required='1' value='{$set['ReferalKickback']}'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['SS_ENERGY']}<br />
					<small>(100 divided by this number)</small>
				</th>
				<td>
					<input type='number' name='attenc' class='form-control' min='1' required='1' value='{$set['AttackEnergyCost']}'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['SS_ATT']}
				</th>
				<td>
					<input type='number' name='attpersess' class='form-control' min='1' required='1' value='{$set['MaxAttacksPerSession']}'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['SS_HTTPS']}<br />
					<small>(Does nothing yet)</small>
				</th>
				<td>
					<input type='text' readonly='1' class='form-control' value='{$set['HTTPS_Support']}'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['SS_PW']}<br />
					<small>Lower is faster and less secure.</small>
				</th>
				<td>
					<input type='number' name='PWEffort' min='5' max='20' class='form-control' value='{$set['Password_Effort']}'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['SS_PP']}
				</th>
				<td>
					<input type='email' class='form-control' name='ppemail' value='{$set['PaypalEmail']}'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['SS_FGU']}<br />
					<small>(<a href='https://fraudguard.io/'>http://bit.ly/2apOVX0</a>)</small>
				</th>
				<td>
					<input type='text' class='form-control' name='fgun' value='{$set['FGUsername']}'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['SS_FGP']}
				</th>
				<td>
					<input type='text' class='form-control' name='fgpw' value='{$set['FGPassword']}'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['SS_GRPUB']}<br />
					<small>(<a href='https://www.google.com/recaptcha/admin'>http://bit.ly/2oJ0Bus</a>)</small>
				</th>
				<td>
					<input type='text' class='form-control' name='rcpublic' value='{$set['reCaptcha_public']}'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['SS_GRPRIV']}
				</th>
				<td>
					<input type='text' class='form-control' name='rcprivate' value='{$set['reCaptcha_private']}'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['SS_BANKFEE']}
				</th>
				<td>
					<input type='number' name='bankbuy' class='form-control' min='1' required='1' value='{$set['bank_cost']}'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['SS_BANKWFEE']}
				</th>
				<td>
					<input type='number' name='bankfee' class='form-control' min='1' required='1' value='{$set['bank_maxfee']}'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['SS_BANKWPERC']}
				</th>
				<td>
					<input type='number' name='bankfeepercent' class='form-control' min='1' required='1' value='{$set['bank_feepercent']}'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['SS_GUILDLVL']}
				</th>
				<td>
					<input type='number' name='guildlvl' class='form-control' min='1' required='1' value='{$set['GUILD_LEVEL']}'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['SS_GUILDCOST']}
				</th>
				<td>
					<input type='number' name='guildcost' class='form-control' min='1' required='1' value='{$set['GUILD_PRICE']}'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['SS_REFILLE']}
				</th>
				<td>
					<input type='number' name='refillenergy' class='form-control' min='1' required='1' value='{$set['energy_refill_cost']}'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['SS_REFILLW']}
				</th>
				<td>
					<input type='number' name='refillwill' class='form-control' min='1' required='1' value='{$set['will_refill_cost']}'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['SS_REFILLB']}
				</th>
				<td>
					<input type='number' name='refillbrave' class='form-control' min='1' required='1' value='{$set['brave_refill_cost']}'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['SS_IQ']}
				</th>
				<td>
					<input type='number' name='iqpersec' class='form-control' min='1' required='1' value='{$set['iq_per_sec']}'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['SS_TIMEOUT']}
				</th>
				<td>
					<input type='number' name='sessiontimeout' placeholder='0 means no timeout.' class='form-control' min='0' required='1' value='{$set['max_sessiontime']}'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['SS_REVALID']}
				</th>
				<td>
					<select name='recaptchatime' class='form-control' type='dropdown'>
						<option value='300'>{$lang['SS_REVALID1']}</option>
						<option value='900'>{$lang['SS_REVALID2']}</option>
						<option value='3600'>{$lang['SS_REVALID3']}</option>
						<option value='86400'>{$lang['SS_REVALID4']}</option>
						<option value='99999999999'>{$lang['SS_REVALID5']}</option>
					</select>
				</td>
			</tr>
		</table>
		</div>";
		
		
        	echo "{$csrf}
        	<input type='submit' class='btn btn-default' value='{$lang['SS_BTN']}' />
        </form>";
		$h->endpage();
	}
	else
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_sett_1', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		$GameName = (isset($_POST['gamename'])  && preg_match("/^[a-z0-9_.]+([\\s]{1}[a-z0-9_.]|[a-z0-9_.])+$/i", $_POST['gamename'])) ? $db->escape(strip_tags(stripslashes($_POST['gamename']))) : '';
		$RefAward = (isset($_POST['refkb']) && is_numeric($_POST['refkb'])) ? abs(intval($_POST['refkb'])) : '';
		$AttackEnergy = (isset($_POST['attenc']) && is_numeric($_POST['attenc'])) ? abs(intval($_POST['attenc'])) : '';
		$Paypal = (isset($_POST['ppemail']) && filter_input(INPUT_POST, 'ppemail', FILTER_VALIDATE_EMAIL)) ? $db->escape(stripslashes($_POST['ppemail'])) : '';
		$GameOwner = (isset($_POST['ownername']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $_POST['ownername'])) ? $db->escape(strip_tags(stripslashes($_POST['ownername']))) : '';
		$GameDesc =  (isset($_POST['gamedesc'])) ? $db->escape(strip_tags(stripslashes($_POST['gamedesc']))) : '';
		$FGPW =  (isset($_POST['fgpw'])) ? $db->escape(strip_tags(stripslashes($_POST['fgpw']))) : '';
		$FGUN =  (isset($_POST['fgun'])) ? $db->escape(strip_tags(stripslashes($_POST['fgun']))) : '';
		$rcpb =  (isset($_POST['rcpublic'])) ? $db->escape(strip_tags(stripslashes($_POST['rcpublic']))) : '';
		$rcpr =  (isset($_POST['rcprivate'])) ? $db->escape(strip_tags(stripslashes($_POST['rcprivate']))) : '';
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
		if (empty($GameName))
		{
			alert('danger',$lang['ERROR_INVALID'],$lang['SS_ERR1']);
			die($h->endpage());
		}
		elseif (empty($Paypal))
		{
			alert('danger',$lang['ERROR_INVALID'],$lang['SS_ERR1']);
			die($h->endpage());
		}
		elseif (empty($GameOwner))
		{
			alert('danger',$lang['ERROR_INVALID'],$lang['SS_ERR1']);
			die($h->endpage());
		}
		elseif (empty($RefAward))
		{
			alert('danger',$lang['ERROR_INVALID'],$lang['SS_ERR1']);
			die($h->endpage());
		}
		elseif (empty($GameDesc))
		{
			alert('danger',$lang['ERROR_INVALID'],$lang['SS_ERR1']);
			die($h->endpage());
		}
		elseif (empty($AttackEnergy))
		{
			alert('danger',$lang['ERROR_INVALID'],$lang['SS_ERR1']);
			die($h->endpage());
		}
		elseif (empty($FGPW))
		{
			alert('danger',$lang['ERROR_INVALID'],$lang['SS_ERR1']);
			die($h->endpage());
		}
		elseif (empty($FGUN))
		{
			alert('danger',$lang['ERROR_INVALID'],$lang['SS_ERR1']);
			die($h->endpage());
		}
		elseif (empty($rcpb))
		{
			alert('danger',$lang['ERROR_INVALID'],$lang['SS_ERR1']);
			die($h->endpage());
		}
		elseif (empty($rcpr))
		{
			alert('danger',$lang['ERROR_INVALID'],$lang['SS_ERR1']);
			die($h->endpage());
		}
		elseif (empty($PasswordEffort) || $PasswordEffort < 5 || $PasswordEffort > 20)
		{
			alert('danger',$lang['ERROR_INVALID'],$lang['SS_ERR1']);
			die($h->endpage());
		}
		elseif ($recaptchatime <= 0)
		{
			alert('danger',$lang['ERROR_INVALID'],$lang['SS_ERR1']);
			die($h->endpage());
		}
		else
		{
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
			alert('success',$lang['ERROR_SUCCESS'],"Successfully updated the game settings.",true,'index.php');
			$api->SystemLogsAdd($userid,'staff',"Updated game settings.");
		}
		$h->endpage();
	}
}
function announce()
{
	global $db,$ir,$userid,$h,$api,$lang;
	if (!isset($_POST['announcement']))
	{
		$csrf=request_csrf_html('staff_announce');
		echo "{$lang['SS_ANNOUNCE']}<br />
		<form method='post'>
			<textarea name='announcement' rows='5' class='form-control'></textarea>
			<input type='submit' class='btn btn-default' value='{$lang['SS_ANNOUNCE_BTN']}'>
			{$csrf}
		</form>";
	}
	else
	{
		if (empty($_POST['announcement']))
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['SS_ANNOUNCE_ERR']);
			die($h->endpage());
		}
		else
		{
			if (!isset($_POST['verf']) || !verify_csrf_code('staff_announce', stripslashes($_POST['verf'])))
			{
				alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
				die($h->endpage());
			}
			$time=time();
			$_POST['announcement'] = $db->escape(str_replace("\n", "<br />",strip_tags(stripslashes($_POST['announcement']))));
			$db->query("INSERT INTO `announcements` (`ann_id`, `ann_text`, `ann_time`, `ann_poster`) 
			VALUES (NULL, '{$_POST['announcement']}', '{$time}', '{$userid}');");
			$db->query("UPDATE `users` SET `announcements` = `announcements` + 1");
			alert('success',$lang['ERROR_SUCCESS'],$lang['SS_ANNOUNCE_SUCC'],true,'../announcements.php');
			$api->SystemLogsAdd($userid,'staff',"Posted an announcement.");
		}
	}
	$h->endpage();
}
function diagnostics()
{
	global $db,$h,$set,$userid,$api,$lang;
	$dir= substr(__DIR__, 0, strpos(__DIR__, "\staff"));
	if (version_compare(phpversion(), '5.5.0') < 0)
    {
        $pv = "<span style='color: red'>{$lang['SS_DIAG']}</span>";
    }
    else
    {
        $pv = "<span style='color: green'>{$lang['SS_DIAG1']}</span>";
    }
    if (is_writable('./'))
    {
        $wv = "<span style='color: green'>{$lang['SS_DIAG1']}</span>";
    }
    else
    {
        $wv = "<span style='color: red'>{$lang['SS_DIAG']}</span>";
    }
    if (function_exists('mysqli_connect'))
    {
        $dv = "<span style='color: green'>{$lang['SS_DIAG1']}</span>";
    }
    else
    {
        $dv = "<span style='color: red'>{$lang['SS_DIAG']}</span>";
    }
	if (extension_loaded('pdo'))
    {
        $pdv = "<span style='color: green'>{$lang['SS_DIAG1']}</span>";
    }
	else
    {
        $pdv = "<span style='color: red'>{$lang['SS_DIAG']}</span>";
    }
	if (function_exists('openssl_random_pseudo_bytes'))
    {
        $ov = "<span style='color: green'>{$lang['SS_DIAG1']}</span>";
    }
    else
    {
        $ov = "<span style='color: red'>{$lang['SS_DIAG']}</span>";
    }
	if (function_exists('password_hash'))
    {
        $hv = "<span style='color: green'>{$lang['SS_DIAG1']}</span>";
    }
    else
    {
        $hv = "<span style='color: red'>{$lang['SS_DIAG']}</span>";
    }
	echo"<table class='table table-bordered table-hover'>
    		<tr>
    			<td>{$lang['SS_TEST']}</td>
    			<td>{$pv}</td>
    		</tr>
    		<tr>
    			<td>{$lang['SS_WRITE']}</td>
    			<td>{$wv}</td>
    		</tr>
			<tr>
    			<td>{$lang['SS_PDO']}</td>
    			<td>{$pdv}</td>
    		</tr>
    		<tr>
    			<td>{$lang['SS_MYSQLI']}</td>
    			<td>{$dv}</td>
    		</tr>
			<tr>
    			<td>{$lang['SS_HASH']}</td>
    			<td>{$hv}</td>
    		</tr>
			<tr>
    			<td>{$lang['SS_OPENSSL']}</td>
    			<td>{$ov}</td>
    		</tr>
    		<tr>
    			<td>{$lang['SS_UPDATE']}</td>
    			<td>
        			" . get_cached_file("http://mastergeneral156.pcriot.com/update-checker.php?version={$set['BuildNumber']}",$dir . '\cache\update_check.txt') . "
        		</td>
        	</tr>
    </table>
       ";
	   $api->SystemLogsAdd($userid,'staff',"Viewed game diagnostics.");
	$h->endpage();
}
function restore()
{
	global $db,$ir,$h,$api,$userid,$lang;
	if (!isset($_POST['restore']))
	{
		echo "{$lang['SS_RESTORE']}<br />
		<form method='post'>
			<input type='submit' name='restore' value='{$lang['SS_RESTORE_BTN']}' class='btn btn-default'>
		</form>";
		$h->endpage();
	}
	else
	{
		$db->query("UPDATE `users` SET `hp`=`maxhp`,`energy`=`maxenergy`,`brave`=`maxbrave`,`will`=`maxwill`");
		$db->query("UPDATE `dungeon` SET `dungeon_out` = 0");
		$db->query("UPDATE `infirmary` SET `infirmary_out` = 0");
		$api->SystemLogsAdd($userid,'staff',"Restored all users.");
		alert('success',$lang['ERROR_SUCCESS'],$lang['SS_RESTORE_SUCC'],true,'index.php');
		$h->endpage();
	}
}
function errlog()
{
	global $db,$lang,$api,$userid,$h;
	$api->SystemLogsAdd($userid,'staff',"Viewed the error log.");
	echo "
	<textarea class='form-control' rows='20' readonly='1'>" . file_get_contents("error_log") . "</textarea>";
	$h->endpage();
}
function staff()
{
	global $db,$userid,$api,$lang,$h;
	if (isset($_POST['user']))
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_priv', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs(intval($_POST['user'])) : 0;
		$_POST['priv'] =  (isset($_POST['priv'])) ? $db->escape(strip_tags(stripslashes($_POST['priv']))) : 'member';
		$priv_array=array('Member','Admin','Web Developer','Forum Moderator','Assistant','NPC');
		if (!in_array($_POST['priv'],$priv_array))
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_PRIV_ERR']);
			die($h->endpage());
		}
		if (!($api->SystemUserIDtoName($_POST['user'])))
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_PRIV_ERR1']);
			die($h->endpage());
		}
		$api->UserInfoSetStatic($_POST['user'],'user_level',$_POST['priv']);
		alert('success',$lang['ERROR_SUCCESS'],"{$lang['STAFF_PRIV_SUCC']} {$_POST['priv']}.");
		die($h->endpage());
	}
	else
	{
		$csrf = request_csrf_html('staff_priv');
		echo "<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						{$lang['STAFF_PRIV_INFO']}
					</th>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_PRIV_USER']}
					</th>
					<td>
						" . user_dropdown('user') . "
					</td>
				</tr>
				
				<tr>
					<th>
						{$lang['STAFF_PRIV_PRIVLIST']}
					</th>
					<td>
						<select name='priv' class='form-control'>
							<option value='NPC'>{$lang['SCU_UL1']}</option>
							<option value='Member'>{$lang['SCU_UL2']}</option>
							<option value='Forum Moderator'>{$lang['SCU_UL4']}</option>
							<option value='Assistant'>{$lang['SCU_UL5']}</option>
							<option value='Web Developer'>{$lang['SCU_UL6']}</option>
							<option value='Admin'>{$lang['SCU_UL3']}</option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-default' value='{$lang['STAFF_PRIV_PRIVBTN']}'>
					</td>
				</tr>
			</table>
			{$csrf}
		</form>";
	}
}