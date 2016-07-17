<?php
/*
	File: staff/staff_settings.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: Allows admins to view and change the game settings at will.
	Author: TheMasterGeneral
	Website: http://mastergeneral156.pcriot.com/
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
				<a href='?action=basicset'>Game Settings</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='?action=announce'>Create an Announcement</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='?action=diagnostics'>Game Diagnostics</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='?action=restore'>Restore All Users</a>
			</td>
		</tr>
	</table>";
}
function basicsettings()
{
	global $h,$ir,$db,$lang,$set;
	if (!isset($_POST['gamename']))
	{
		$csrf=request_csrf_html('staff_sett_1');
		echo "
		<form method='post'>
		<table class='table table-bordered table-hover'>
			<tr>
				<th>
					Game Name
				</th>
				<td>
					<input type='text' name='gamename' class='form-control' required='1' value='{$set['WebsiteName']}'>
				</td>
			</tr>
			<tr>
				<th>
					Owner's Name
				</th>
				<td>
					<input type='text' name='ownername' class='form-control' required='1' value='{$set['WebsiteName']}'>
				</td>
			</tr>
			<tr>
				<th>
					Refferal Award
				</th>
				<td>
					<input type='number' name='refkb' class='form-control' min='1' required='1' value='{$set['ReferalKickback']}'>
				</td>
			</tr>
			<tr>
				<th>
					Energy Cost for Attacking <br />
					<small>(100 divided by this number)</small>
				</th>
				<td>
					<input type='number' name='attenc' class='form-control' min='1' required='1' value='{$set['AttackEnergyCost']}'>
				</td>
			</tr>
			<tr>
				<th>
					HTTPS Redirection<br />
					<small>(Does nothing yet)</small>
				</th>
				<td>
					<input type='text' readonly='1' class='form-control' value='{$set['HTTPS_Support']}'>
				</td>
			</tr>
			<tr>
				<th>
					Password Effort
				</th>
				<td>
					<input type='number' class='form-control' readonly='1' value='{$set['Password_Effort']}'>
				</td>
			</tr>
			<tr>
				<th>
					Paypal Email
				</th>
				<td>
					<input type='email' class='form-control' name='ppemail' value='{$set['PaypalEmail']}'>
				</td>
			</tr>
			<tr>
				<th>
					Fraud Guard I/O Username<br />
					<small>(<a href='https://fraudguard.io/'>https://fraudguard.io/</a>)</small>
				</th>
				<td>
					<input type='text' class='form-control' name='fgun' value='{$set['FGUsername']}'>
				</td>
			</tr>
			<tr>
				<th>
					Fraud Guard I/O Password
				</th>
				<td>
					<input type='text' class='form-control' name='fgpw' value='{$set['FGPassword']}'>
				</td>
			</tr>
			<tr>
				<th>
					Game Description
				</th>
				<td>
					<textarea name='gamedesc' required='1' class='form-control' rows='5'>{$set['Website_Description']}</textarea>
				</td>
			</tr>
		</table>";
		
		
        	echo "{$csrf}
        	<input type='submit' class='btn btn-default' value='Update Settings' />
        </form>";
		$h->endpage();
	}
	else
	{
		$RefAward = (isset($_POST['refkb']) && is_numeric($_POST['refkb'])) ? abs(intval($_POST['refkb'])) : '';
		$GameName= (isset($_POST['gamename'])) ? gpc_cleanup($_POST['gamename']) : '';
		$Paypal = (isset($_POST['ppemail']) && filter_input(INPUT_POST, 'ppemail', FILTER_VALIDATE_EMAIL)) ? gpc_cleanup($_POST['ppemail']) : '';
		$GameOwner=$db->escape(htmlentities($_POST['ownername'], ENT_QUOTES, 'ISO-8859-1'));
		if (empty($GameName))
		{
			alert('danger',"{$lang['ERROR_INVALID']}","Invalid game name specified!");
			die($h->endpage());
		}
		elseif (empty($Paypal))
		{
			alert('danger',"{$lang['ERROR_INVALID']}","Invalid paypal address specified!");
			die($h->endpage());
		}
		elseif (empty($GameOwner))
		{
			alert('danger',"{$lang['ERROR_INVALID']}","Invalid Game Owner Name specified.");
			die($h->endpage());
		}
		elseif (empty($RefAward))
		{
			alert('danger',"{$lang['ERROR_INVALID']}","Invalid Refferal Kickback award specified.");
			die($h->endpage());
		}
		else
		{
			$UserPermissionSelectQuery=$db->query("SELECT `p`.*,`u`.`username`,`u`.`userid` FROM `permissions` AS `p` INNER JOIN `users` AS `u` ON `u`.`userid` = `p`.`perm_user` WHERE `perm_user` = {$_POST['userid']}");
			$UserName=$db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$_POST['userid']}"));
			staff_csrf_stdverify('staff_sett_1', '?action=basicset');
			if ($db->num_rows($UserPermissionSelectQuery) == 0)
			{
				alert('danger',"All Permissions!","This user has all permissions allowed to them! We cannot display users who have all the permissions, only the users who has had their permissions tweaked.");
				die($h->endpage());
			}
			else
			{
				echo
				"Displaying {$UserName}'s Permissions.<br />
				<small>Remember, this will only show permissions if the user's permissions have been tweaked.</small>
				<table class='table table-bordered table-hover'>
					<thead>
						<tr>
							<th>Permission Name</th>
							<th>Disabled?</th>
						</tr>
					</thead>
					<tbody>";
				while ($UserPerm = $db->fetch_row($UserPermissionSelectQuery))
				{
					echo"<tr>
							<td>
								{$UserPerm['perm_name']}
							</td>
							<td>
								{$UserPerm['perm_disable']}
							</td>
						</tr>
							";
				}
				echo "</tbody></table>";
				stafflog_add("Viewed <a href='../profile.php?user={$_POST['userid']}'>{$UserName}</a> [{$_POST['userid']}]'s Permissions.");
			}
		}
		$h->endpage();
	}
}
function announce()
{
	global $db,$ir,$userid,$h;
	$code = request_csrf_code('staff_announce');
	if (!isset($_POST['announcement']))
	{
		echo "Here you may create an announcement. Please make sure whatever you are announcing is clear and concise.<br />
		<form method='post'>
			<textarea name='announcement' rows='5' class='form-control'></textarea>
			<input type='submit' class='btn btn-default' value='Create Announcement'>
		</form>";
	}
	else
	{
		if (empty($_POST['announcement']))
		{
			alert('danger','Empty Input!','You cannot post an announcement without anything written in it...');
			die($h->endpage());
		}
		else
		{
			$time=time();
			$_POST['announcement'] = $db->escape(str_replace("\n", "<br />",strip_tags(stripslashes($_POST['announcement']))));
			$db->query("INSERT INTO `announcements` (`ann_id`, `ann_text`, `ann_time`, `ann_poster`) 
			VALUES (NULL, '{$_POST['announcement']}', '{$time}', '{$userid}');");
			$db->query("UPDATE `users` SET `announcements` = `announcements` + 1");
			alert('success','Success!','You have successfully created an announcement.');
			stafflog_add("Posted an announcement.");
		}
	}
	$h->endpage();
}
function diagnostics()
{
	global $db,$h,$set;
	if (version_compare(phpversion(), '5.5.0') < 0)
    {
        $pv = '<span style="color: red">Failed</span>';
        $pvf = 0;
    }
    else
    {
        $pv = "<span style='color: green'>Pass! PHP Version is " . phpversion();  "!</span>";
        $pvf = 1;
    }
    if (is_writable('./'))
    {
        $wv = '<span style="color: green">Pass! Game folder is writable.</span>';
        $wvf = 1;
    }
    else
    {
        $wv = '<span style="color: red">Fail!</span>';
        $wvf = 0;
    }
    if (function_exists('mysqli_connect'))
    {
        $dv = '<span style="color: green">Pass! MySQLi detected!</span>';
        $dvf = 1;
    }
    else
    {
        $dv = '<span style="color: red">Failed</span>';
        $dvf = 0;
    }
	if (function_exists('openssl_random_pseudo_bytes'))
    {
        $ov = '<span style="color: green">Pass! OpenSSL Random Pseudo Bytes detected!</span>';
        $ovf = 1;
    }
    else
    {
        $ov = '<span style="color: red">Failed...</span>';
        $ovf = 0;
    }
	if (function_exists('password_hash'))
    {
        $hv = '<span style="color: green">Pass! Using stronger password hash method.</span>';
        $hvf = 1;
    }
    else
    {
        $hv = '<span style="color: red">Failed...</span>';
        $hvf = 0;
    }
	echo"<table class='table table-bordered table-hover'>
    		<tr>
    			<td>Is the server's PHP Version greater than 5.5.0?</td>
    			<td>{$pv}</td>
    		</tr>
    		<tr>
    			<td>Is the game folder writable?</td>
    			<td>{$wv}</td>
    		</tr>
    		<tr>
    			<td>Is MySQLi present?</td>
    			<td>{$dv}</td>
    		</tr>
			<tr>
    			<td>Password_Hash avaliable?</td>
    			<td>{$hv}</td>
    		</tr>
			<tr>
    			<td>OpenSSL Random Pseudo Bytes avaliable?</td>
    			<td>{$ov}</td>
    		</tr>
    		<tr>
    			<td>Is Chivalry Engine up to date?</td>
    			<td>
        			<iframe width='100%' height='35' style='border:none' src='http://mastergeneral156.pcriot.com/update-checker.php?version={$set['BuildNumber']}'>Your browser does not support iframes...</iframe>
        		</td>
        	</tr>
    </table>
       ";
	   stafflog_add("Viewed game diagnostics.");
	$h->endpage();
}
function restore()
{
	global $db,$ir,$h;
	if (!isset($_POST['restore']))
	{
		echo "Here you can restore your userbase to 100% HP, Brave and energy. This may only be useful for testing, or if you wish to be nice to your player base.<br />
		<form method='post'>
			<input type='submit' name='restore' value='Restore ALL Users to Full!' class='btn btn-default'>
		</form>";
		$h->endpage();
	}
	else
	{
		$db->query("UPDATE `users` SET `hp`=`maxhp`,`energy`=`maxenergy`,`brave`=`maxbrave`");
		stafflog_add("Restored all users to their fullest.");
		alert('success',"Success!","You have successfully restored all your users to their full health, brave and energy!");
		$h->endpage();
	}
}