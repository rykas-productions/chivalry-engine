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
	global $db;
	echo "<h3>Guild Listing</h3>
	<a href='?action=create'>Create a Guild</a><hr />";
	echo "<table class='table table-bordered table-hover'>
	<tr>
		<th>Guild Name</th>
		<th>Level</th>
		<th>Members</th>
		<th>Leader</th>
	</tr>";
	$gq = $db->query(
			"SELECT `guild_id`, `guild_town_id`, `guild_owner`, `guild_name`, 
			`userid`, `username`, `guild_level`, `guild_capacity`
			FROM `guild` AS `g`
			LEFT JOIN `users` AS `u` ON `g`.`guild_owner` = `u`.`userid`
			ORDER BY `g`.`guild_id` ASC");
    //List all the in-game guilds.
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
		</tr>";
	}
	echo"</table>";
	
}
function create()
{
	global $db,$userid,$api,$ir,$set,$h;
	 echo "<h3>Create a Guild</h3><hr />";
	$cg_price = $set['GUILD_PRICE'];
	$cg_level = $set['GUILD_LEVEL'];
    //User does not have the minimum required primary currency.
	if (!($api->UserHasCurrency($userid,'primary',$cg_price)))
	{
		alert("danger","Uh Oh!","You do not have enough cash to create a guild! You need
		    " . number_format($cg_price) . ".",true,'index.php');
		die($h->endpage());
	}
    //User level is too low to create a guild.
	elseif (($api->UserInfoGet($userid,'level',false)) < $cg_level)
	{
		alert("danger","Uh Oh!","You are too low of a level to create a guild. Please level up to Level
		    " . number_format($cg_level) . " and try again..",true,'index.php');
		die($h->endpage());
	}
    //User is already in a guild.
	elseif ($ir['guild'])
	{
		alert("danger","Uh Oh!","You are already in a guild. You cannot create a guild while you are in one.",true,'back');
		die($h->endpage());
	}
	else
	{
		if (isset($_POST['name']))
		{
            //User fails the CSRF verification.
			if (!isset($_POST['verf']) || !verify_csrf_code('createguild', stripslashes($_POST['verf'])))
			{
				alert('danger',"CSRF Error!","The action you were trying to do was blocked. It was blocked because you
				    loaded another page on the game. If you have not loaded a different page during this time, change
				    your password immediately, as another person may have access to your account!");
				die($h->endpage());
			}
			$name = $db->escape(htmlentities(stripslashes($_POST['name']), ENT_QUOTES, 'ISO-8859-1'));
			$desc = $db->escape(htmlentities(stripslashes($_POST['desc']), ENT_QUOTES, 'ISO-8859-1'));
            //Guild name is already in use.
			if ($db->num_rows($db->query("SELECT `guild_id` FROM `guild` WHERE `guild_name` = '{$name}'")) > 0)
			{
				alert("danger","Uh Oh!","A guild with the name you've chosen already exists!",true,'back');
				die($h->endpage());
			}
			$db->query("INSERT INTO `guild` 
						(`guild_town_id`, `guild_owner`, `guild_coowner`, `guild_primcurr`, 
						`guild_seccurr`, `guild_hasarmory`, `guild_capacity`, `guild_name`, `guild_desc`, 
						`guild_level`, `guild_xp`) 
						VALUES ('{$ir['location']}', '{$userid}', '{$userid}', '0', '0', 'false', '5', 
						'{$name}', '{$desc}', '1', '0')");
			$i = $db->insert_id();
            //Take user's primary currency, and have player join the guild.
			$api->UserTakeCurrency($userid,'primary',$cg_price);
			$db->query("UPDATE `users` SET `guild` = {$i} WHERE `userid` = {$userid}");
            //Tell user they've created a guild.
			alert('success',"Success!","You have successfully created a guild.",true,"viewguild.php");
            //Log the purchase, and that they've joined a guild.
			$api->SystemLogsAdd($userid,'guilds',"Purchased a guild.");
			$api->SystemLogsAdd($userid,'guilds',"Joined Guild ID {$i}");
		}
		else
		{
            //Request the CSRF form.
			$csrf=request_csrf_html('createguild');
			echo"<form action='?action=create' method='post'>";
			echo "
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						Creating a Guild Form
					</th>
				</tr>
				<tr>
					<th>
						Guild Name
					</th>
					<td>
						<input type='text' required='1' class='form-control' name='name' />
					</td>
				</tr>
				<tr>
					<th>
						Guild Description
					</th>
					<td>
						<textarea name='desc' required='1' class='form-control' cols='40' rows='7'></textarea>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' value='Create Guild for" . number_format($cg_price) . "!' class='btn btn-primary'>
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
	global $db,$h,$api;
	$_GET['id'] = abs($_GET['id']);
    //Guild ID has not been entered, so redirect them to main guild listing.
	if (empty($_GET['id']))
	{
		header("Location: guilds.php");
	}
	else
	{
		$gq = $db->query("SELECT * FROM `guild` WHERE `guild_id` = {$_GET['id']}");
        //Guild does not exist.
		if ($db->num_rows($gq) == 0)
		{
			alert('danger',"Uh Oh!","The guild you are trying to view does not exist.",true,"guilds.php");
			die($h->endpage());
		}
        //List all the guild's information.
		$gd = $db->fetch_row($gq);
		echo "<h3>{$gd['guild_name']} Guild</h3>";
		echo "
		<table class='table table-bordered'>
			<tr>
				<th>
					Guild Leader
				</th>
				<td>
					<a href='profile.php?user={$gd['guild_owner']}'> " . $api->SystemUserIDtoName($gd['guild_owner']) . "</a>
				</td>
			</tr>
			<tr>
				<th>
					Guild Co-Leader
				</th>
				<td>
					<a href='profile.php?user={$gd['guild_coowner']}'> " . $api->SystemUserIDtoName($gd['guild_coowner']) . "</a>
				</td>
			</tr>
			<tr>
				<th>
					Guild Level
				</th>
				<td>
					" . number_format($gd['guild_level']) . "
				</td>
			</tr>
			<tr>
				<th>
					{$gd['guild_name']} Description
				</th>
				<td>
					{$gd['guild_desc']}
				</td>
			</tr>
			<tr>
				<th>
					Members
				</th>
				<td>";
                //Count players in this guild.
					$cnt = number_format($db->fetch_single
										($db->query("SELECT COUNT(`userid`) 
										FROM `users` 
										WHERE `guild` = {$_GET['id']}")));
					echo number_format($cnt) . " / " . number_format($gd['guild_capacity']) . "
				</td>
			</tr>
			<tr>
				<th>
					Guild Location
				</th>
				<td>";
				echo $api->SystemTownIDtoName($gd['guild_town_id']) . "
				</td>
			</tr>
			<tr>
				<th>
					<a href='?action=memberlist&id={$_GET['id']}'>View Members</a>
				</th>
				<td>
					<a href='?action=apply&id={$_GET['id']}'>Apply</a>
				</td>
			</tr>
		</table>";
	}
}
function memberlist()
{
	global $db,$h;
	$_GET['id'] = abs($_GET['id']);
    //Guild is not specified.
	if (empty($_GET['id']))
	{
		alert('danger',"Uh Oh!","Please specify the guild you wish to view.",true,"guilds.php");
		die($h->endpage());
	}
	$gq = $db->query("SELECT * FROM `guild` WHERE `guild_id` = {$_GET['id']}");
    //Guild does not exist.
	if ($db->num_rows($gq) == 0)
	{
		alert('danger',"Uh Oh!","You are trying to view a non-existent guild.",true,"guilds.php");
		die($h->endpage());
	}
	$gd = $db->fetch_row($gq);
	echo "<h3>Members Enlisted in the {$gd['guild_name']} guild</h3>
	<table class='table table-bordered'>
		  	<tr>
		  		<th>
					User
				</th>
		  		<th>
					Level
				</th>
		  	</tr>";
	$q =  $db->query("SELECT `userid`, `username`, `level`
                     FROM `users`
                     WHERE `guild` = {$gd['guild_id']}
                     ORDER BY `level` DESC");
    //List players in the guild.
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
	global $db,$userid,$ir,$api,$h;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
    //Guild is not specified.
	if (empty($_GET['id']))
	{
		alert('danger',"Uh Oh!","Please specify the guild you wish to view.",true,"guilds.php");
		die($h->endpage());
	}
	$gq = $db->query("SELECT * FROM `guild` WHERE `guild_id` = {$_GET['id']}");
    //Guild does not exist.
	if ($db->num_rows($gq) == 0)
	{
		alert('danger',"Uh Oh!","You are trying to apply to a non-existent guild.",true,"guilds.php");
		die($h->endpage());
	}
	$gd = $db->fetch_row($gq);
    //User is already in a guild, and cannot join another.
	if ($ir['guild'] > 0)
	{
		alert('danger',"Uh Oh!","You cannot write applications if you're already in a guild.",true,"guilds.php?action=view&id={$_GET['id']}");
		die($h->endpage());
	}
	echo "<h3>Submitting an Application to join the {$gd['guild_name']} guild.</h3><hr />";
	if (isset($_POST['application']))
	{
        //User fails CSRF verification
		if (!isset($_POST['verf']) || !verify_csrf_code('guild_apply', stripslashes($_POST['verf'])))
		{
			alert('danger',"Action Blocked!","The action you were trying to do was blocked. It was blocked because you loaded
                another page on the game. If you have not loaded a different page during this time, change your password
                immediately, as another person may have access to your account!",true,'back');
			die($h->endpage());
		}
		$cnt=$db->query("SELECT * FROM `guild_applications` WHERE `ga_user` = {$userid} && `ga_guild` = {$_GET['id']}");
		//User has already submitted an application to this guild.
        if ($db->num_rows($cnt) > 0)
		{
			alert('danger',"Uh Oh!","You have already filled out an application to join this guild. Please wait until a
			    response is given.",true,'back');
			die($h->endpage());
		}
        //Tell the guild's owner and co-owner that the user has sent an application.
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
		//Insert the application, notify the guild and tell the user they were successful.
        $db->query("INSERT INTO `guild_applications` VALUES 
                    (NULL, {$userid}, {$_GET['id']}, 
                    {$time}, '{$application}')");
		$gev = $db->escape("<a href='profile.php?user={$userid}'>{$ir['username']}</a> 
                                sent an application to join this guild.");
		$db->query("INSERT INTO `guild_notifications` VALUES (NULL, {$_GET['id']}, " . time() . ", '{$gev}')");
		alert('success',"Success!","You application has been submitted successfully.",true,"guilds.php?action=view&id={$_GET['id']}");
	}
	else
	{
        //Request CSRF form.
		$csrf = request_csrf_html('guild_apply');
		echo "
		<form action='?action=apply&id={$_GET['id']}' method='post'>
			Write your application to join this guild here. The more information you can provide, the higher chance you
			are to be accepted.<br />
			<textarea name='application' class='form-control' required='1' rows='7' cols='40'></textarea><br />
			{$csrf}
			<input type='submit' class='btn btn-primary' value='Submit Application' />
		</form>";
	}
}
function wars()
{
	global $db,$api;
	$time = time();
	echo "<h3>Known Guild Wars</h3><hr />";
	$q=$db->query("SELECT * FROM `guild_wars` 
                    WHERE `gw_winner` = 0 AND 
                    `gw_end` > {$time} 
                    ORDER BY `gw_id` DESC");
    //There is at least one active guild war.
	if ($db->num_rows($q) > 0)
	{
		echo "<table class='table table-bordered'>";
        //List the active guild wars.
		while ($r = $db->fetch_row($q))
		{
			echo "<tr>
				<td>
					<a href='guilds.php?action=view&id={$r['gw_declarer']}'>{$api->GuildFetchInfo($r['gw_declarer'],'guild_name')}</a><br />
						(Points: " . number_format($r['gw_drpoints']) . ")
				</td>
				<td>
					VS
				</td>
				<td>
					<a href='guilds.php?action=view&id={$r['gw_declaree']}'>{$api->GuildFetchInfo($r['gw_declaree'],'guild_name')}</a><br />
						(Points: " . number_format($r['gw_depoints']) . ")
				</td>
			</tr>";
		}
		echo "</table>";
	}
    //No guild wars.
	else
	{
		alert('danger',"Uh Oh!","There are currently no guilds warring at this time.",false);
	}
}
$h->endpage();