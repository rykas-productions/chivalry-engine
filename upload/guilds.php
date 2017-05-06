<?php
/*
	File:		guilds.php
	Created: 	4/5/2016 at 12:06AM Eastern Time
	Info: 		Lists all the in-game guilds, and allows a user to
				apply to a guild, or create their own.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case 'create':
    create();
    break;
case 'view':
    view();
    break;
case 'apply':
    apply();
    break;
case 'memberlist':
    memberlist();
    break;
case 'wars':
    wars();
    break;
default:
    menu();
    break;
}
function menu()
{
	global $db,$userid,$api,$lang,$h;
	echo "<h3>{$lang['GUILD_LIST']}</h3>
	<a href='?action=create'>{$lang['GUILD_CREATE']}</a><hr />";
	echo "<table class='table table-bordered table-hover'>
	<tr>
		<th>{$lang['GUILD_LIST_TABLE1']}</th>
		<th>{$lang['GUILD_LIST_TABLE2']}</th>
		<th>{$lang['GUILD_LIST_TABLE3']}</th>
		<th>{$lang['GUILD_LIST_TABLE5']}</th>
		<th>{$lang['GUILD_LIST_TABLE4']}</th>
	</tr>";
	$gq = $db->query(
			"SELECT `guild_id`, `guild_town_id`, `guild_owner`, `guild_name`, 
			`userid`, `username`, `guild_level`, `guild_capacity`
			FROM `guild` AS `g`
			LEFT JOIN `users` AS `u` ON `g`.`guild_owner` = `u`.`userid`
			ORDER BY `g`.`guild_id` ASC");
	while ($gd = $db->fetch_row($gq))
	{
		echo "
		<tr>
			<td>
				<a href='?action=view&id={$gd['guild_id']}'>{$gd['guild_name']}</a>
			</td>
			<td>
				{$gd['guild_level']}
			</td>
			<td>";
				$cnt = number_format($db->fetch_single
										($db->query("SELECT COUNT(`userid`) 
										FROM `users` 
										WHERE `guild` = {$gd['guild_id']}")));
				echo "{$cnt} / {$gd['guild_capacity']}";
			echo "</td>
			<td>
				<a href='profile.php?user={$gd['userid']}'>{$gd['username']}</a>
			</td>
			<td>";
			echo 
				$api->SystemTownIDtoName($gd['guild_town_id']);
			echo"</td>
		</tr>";
	}
	echo"</table>";
	
}
function create()
{
	global $db,$userid,$lang,$api,$ir,$set,$h;
	 echo "<h3>{$lang['GUILD_CREATE']}</h3><hr />";
	$cg_price = $set['GUILD_PRICE'];
	$cg_level = $set['GUILD_LEVEL'];
	if (!($api->UserHasCurrency($userid,'primary',$cg_price)))
	{
		alert("danger",$lang['ERROR_GENERIC'],"{$lang['GUILD_CREATE_ERROR']} " . number_format($cg_price) . ".",true,'index.php');
		die($h->endpage());
	}
	elseif (($api->UserInfoGet($userid,'level',false)) < $cg_level)
	{
		alert("danger",$lang['ERROR_GENERIC'],"{$lang['GUILD_CREATE_ERROR1']} " . number_format($cg_level) . ".",true,'index.php');
		die($h->endpage());
	}
	elseif ($ir['guild'])
	{
		alert("danger",$lang['ERROR_GENERIC'],$lang['GUILD_CREATE_ERROR2'],true,'back');
		die($h->endpage());
	}
	else
	{
		if (isset($_POST['name']))
		{
			if (!isset($_POST['verf']) || !verify_csrf_code('createguild', stripslashes($_POST['verf'])))
			{
				alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
				die($h->endpage());
			}
			$name = $db->escape(htmlentities(stripslashes($_POST['name']), ENT_QUOTES, 'ISO-8859-1'));
			$desc = $db->escape(htmlentities(stripslashes($_POST['desc']), ENT_QUOTES, 'ISO-8859-1'));
			if ($db->num_rows($db->query("SELECT `guild_id` FROM `guild` WHERE `guild_name` = '{$name}'")) > 0)
			{
				alert("danger",$lang['ERROR_GENERIC'],$lang['GUILD_CREATE_ERROR3'],true,'back');
				die($h->endpage());
			}
			$db->query("INSERT INTO `guild` 
						(`guild_town_id`, `guild_owner`, `guild_coowner`, `guild_primcurr`, 
						`guild_seccurr`, `guild_hasarmory`, `guild_capacity`, `guild_name`, `guild_desc`, 
						`guild_level`, `guild_xp`) 
						VALUES ('{$ir['location']}', '{$userid}', '{$userid}', '0', '0', 'false', '5', 
						'{$name}', '{$desc}', '1', '0')");
			$i = $db->insert_id();
			$api->UserTakeCurrency($userid,'primary',$cg_price);
			$db->query("UPDATE `users` SET `guild` = {$i} WHERE `userid` = {$userid}");
			alert('success',$lang['ERROR_SUCCESS'],$lang['GUILD_CREATE_SUCCESS'],true,"viewguild.php");
			$api->SystemLogsAdd($userid,'guilds',"Purchased a guild.");
			$api->SystemLogsAdd($userid,'guilds',"Joined Guild ID {$i}");
		}
		else
		{
			$csrf=request_csrf_html('createguild');
			echo"<form action='?action=create' method='post'>";
			echo "
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						{$lang['GUILD_CREATE_FORM']}
					</th>
				</tr>
				<tr>
					<th>
						{$lang['GUILD_CREATE_FORM1']}
					</th>
					<td>
						<input type='text' required='1' class='form-control' name='name' />
					</td>
				</tr>
				<tr>
					<th>
						{$lang['GUILD_CREATE_FORM2']}
					</th>
					<td>
						<textarea name='desc' required='1' class='form-control' cols='40' rows='7'></textarea>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' value='{$lang['GUILD_CREATE_BTN']}" . number_format($cg_price) . " {$lang['INDEX_PRIMCURR']}' class='btn btn-default'>
					</td>
				</tr>
				{$csrf}
			</table>
			</form>";
		}
	}
}
function view()
{
	global $db,$lang,$h,$userid,$api;
	$_GET['id'] = abs($_GET['id']);
	if (empty($_GET['id']))
	{
		header("Location: guilds.php");
	}
	else
	{
		$gq = $db->query("SELECT * FROM `guild` WHERE `guild_id` = {$_GET['id']}");
		if ($db->num_rows($gq) == 0)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['GUILD_VIEW_ERROR'],true,"guilds.php");
			die($h->endpage());
		}
		$gd = $db->fetch_row($gq);
		echo "<h3>{$gd['guild_name']} {$lang['GUILD_VIEW_GUILD']}</h3>";
		echo "
		<table class='table table-bordered'>
			<tr>
				<th>
					{$lang['GUILD_VIEW_LEADER']}
				</th>
				<td>
					<a href='profile.php?user={$gd['guild_owner']}'> " . $api->SystemUserIDtoName($gd['guild_owner']) . "</a>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['GUILD_VIEW_COLEADER']}
				</th>
				<td>
					<a href='profile.php?user={$gd['guild_coowner']}'> " . $api->SystemUserIDtoName($gd['guild_coowner']) . "</a>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['GUILD_VIEW_LEVEL']}
				</th>
				<td>
					" . number_format($gd['guild_level']) . "
				</td>
			</tr>
			<tr>
				<th>
					{$gd['guild_name']} {$lang['GUILD_VIEW_DESC']}
				</th>
				<td>
					{$gd['guild_desc']}
				</td>
			</tr>
			<tr>
				<th>
					{$lang['GUILD_VIEW_MEMBERS']}
				</th>
				<td>";
					$cnt = number_format($db->fetch_single
										($db->query("SELECT COUNT(`userid`) 
										FROM `users` 
										WHERE `guild` = {$_GET['id']}")));
					echo number_format($cnt) . " / " . number_format($gd['guild_capacity']) . "
				</td>
			</tr>
			<tr>
				<th>
					{$lang['GUILD_VIEW_LOCATION']}
				</th>
				<td>";
				echo $api->SystemTownIDtoName($gd['guild_town_id']) . "
				</td>
			</tr>
			<tr>
				<th>
					<a href='?action=memberlist&id={$_GET['id']}'>{$lang['GUILD_VIEW_USERS']}</a>
				</th>
				<td>
					<a href='?action=apply&id={$_GET['id']}'>{$lang['GUILD_VIEW_APPLY']}</a>
				</td>
			</tr>
		</table>";
	}
}
function memberlist()
{
	global $db,$userid,$ir,$api,$h,$lang;
	$_GET['id'] = abs($_GET['id']);
	if (empty($_GET['id']))
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['GUILD_VIEW_ERROR'],true,"guilds.php");
		die($h->endpage());
	}
	$gq = $db->query("SELECT * FROM `guild` WHERE `guild_id` = {$_GET['id']}");
	if ($db->num_rows($gq) == 0)
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['GUILD_VIEW_ERROR'],true,"guilds.php");
		die($h->endpage());
	}
	$gd = $db->fetch_row($gq);
	echo "<h3>{$lang['GUILD_VIEW_LIST']} {$gd['guild_name']} {$lang['GUILD_VIEW_LIST2']}</h3>
	<table class='table table-bordered'>
		  	<tr>
		  		<th>
					{$lang['STAFF_ITEM_GIVE_FORM_USER']}
				</th>
		  		<th>
					{$lang['INDEX_LEVEL']}
				</th>
		  	</tr>";
	$q =  $db->query("SELECT `userid`, `username`, `level`
                     FROM `users`
                     WHERE `guild` = {$gd['guild_id']}
                     ORDER BY `level` DESC");
	while ($r = $db->fetch_row($q))
    {
        echo "<tr>
        		<td>
					<a href='profile.php?user={$r['userid']}'>{$r['username']}</a>
				</td>
        		<td>
					{$r['level']}
				</td>
			</tr>";
    }
	echo"</table>";
}
function apply()
{
	global $db,$userid,$ir,$api,$h,$lang;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
	if (empty($_GET['id']))
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['GUILD_VIEW_ERROR'],true,"guilds.php");
		die($h->endpage());
	}
	$gq = $db->query("SELECT * FROM `guild` WHERE `guild_id` = {$_GET['id']}");
	if ($db->num_rows($gq) == 0)
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['GUILD_VIEW_ERROR'],true,"guilds.php");
		die($h->endpage());
	}
	$gd = $db->fetch_row($gq);
	if ($ir['guild'] > 0)
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['GUILD_APP_ERROR'],true,"guilds.php?action=view&id={$_GET['id']}");
		die($h->endpage());
	}
	echo "<h3>{$lang['GUILD_APP_TITLE']} {$gd['guild_name']} {$lang['GUILD_VIEW_LIST2']}</h3><hr />";
	if (isset($_POST['application']))
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('guild_apply', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"],true,'back');
			die($h->endpage());
		}
		$cnt=$db->query("SELECT * FROM `guild_applications` WHERE `ga_user` = {$userid} && `ga_guild` = {$_GET['id']}");
		if ($db->num_rows($cnt) > 0)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['GUILD_APP_ERROR1'],true,'back');
			die($h->endpage());
		}
		if ($gd['guild_owner'] == $gd['guild_coowner'])
		{
			$api->GameAddNotification($gd['guild_owner'],"{$ir['username']} has filled and submitted an application to join your guild.");
		}
		else
		{
			$api->GameAddNotification($gd['guild_owner'],"{$ir['username']} has filled and submitted an application to join your guild.");
			$api->GameAddNotification($gd['guild_coowner'],"{$ir['username']} has filled and submitted an application to join your guild.");
		}
		$time=time();
		$application = (isset($_POST['application']) && is_string($_POST['application'])) ? $db->escape(htmlentities(stripslashes($_POST['application']), ENT_QUOTES, 'ISO-8859-1')) : '';
		$db->query("INSERT INTO `guild_applications` VALUES (NULL, {$userid}, {$_GET['id']}, {$time}, '{$application}')");
		$gev = $db->escape("<a href='profile.php?user={$userid}'>{$ir['username']}</a> sent an application to join this guild.");
		$db->query("INSERT INTO `guild_notifications` VALUES (NULL, {$_GET['id']}, " . time() . ", '{$gev}')");
		alert('success',$lang['ERROR_SUCCESS'],$lang['GUILD_APP_SUCC'],true,"guilds.php?action=view&id={$_GET['id']}");
	}
	else
	{
		$csrf = request_csrf_html('guild_apply');
		echo "
		<form action='?action=apply&id={$_GET['id']}' method='post'>
			{$lang['GUILD_APP_INFO']}<br />
			<textarea name='application' class='form-control' required='1' rows='7' cols='40'></textarea><br />
			{$csrf}
			<input type='submit' class='btn btn-default' value='{$lang['GUILD_APP_BTN']}' />
		</form>";
	}
}
function wars()
{
	global $db,$userid,$ir,$api,$h,$lang;
	$time = time();
	echo "<h3>{$lang['GUILD_WAR_TITLE']}</h3><hr />";
	$q=$db->query("SELECT * FROM `guild_wars` WHERE `gw_winner` = 0 AND `gw_end` > {$time} ORDER BY `gw_id` DESC");
	if ($db->num_rows($q) > 0)
	{
		echo "<table class='table table-bordered'>";
		while ($r = $db->fetch_row($q))
		{
			echo "<tr>
				<td>
					<a href='guilds.php?action=view&id={$r['gw_declarer']}'>{$api->GuildFetchInfo($r['gw_declarer'],'guild_name')}</a><br />
						{$lang['GUILD_WAR_TD']}" . number_format($r['gw_drpoints']) . "{$lang['GUILD_WAR_TD1']}
				</td>
				<td>
					{$lang['GUILD_WAR_TD2']}
				</td>
				<td>
					<a href='guilds.php?action=view&id={$r['gw_declaree']}'>{$api->GuildFetchInfo($r['gw_declaree'],'guild_name')}</a><br />
						{$lang['GUILD_WAR_TD']}" . number_format($r['gw_depoints']) . "{$lang['GUILD_WAR_TD1']}
				</td>
			</tr>";
		}
		echo "</table>";
	}
	else
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['GUILD_WAR_ERR'],false);
	}
}
$h->endpage();