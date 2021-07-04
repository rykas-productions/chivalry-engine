<?php
require('sglobals.php');
echo "<h3>Staff Moderation</h3>";
if ($ir['user_level'] != "Admin") {
    alert('danger', "Uh Oh!", "You do not have permission to be here.", true, 'index.php');
    die($h->endpage());
}
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "listall":
        home();
        break;
    case "guildwepname":
        guildwepname();
        break;
	case "guildweppic":
        guildweppic();
        break;
    default:
        alert('danger', "Uh Oh!", "Please select a valid action to perform.", true, 'index.php');
        die($h->endpage());
        break;
}

function home()
{
	global $db, $api, $h, $userid;
	$q=$db->query("SELECT * FROM `staff_moderation_board`");
	if ($db->num_rows($q) == 0)
	{
		alert('danger',"Uh Oh!","There's no listings for you to moderate at this time.",true,'index.php');
		die($h->endpage());
	}
	else
	{
		while ($r = $db->fetch_row($q))
		{
			if ($r['mod_type'] == "guild_sword_name")
			{
				$approvelink="&action=guildwepname&do=approve";
				$denylink="&action=guildwepname&do=deny";
			}
			if ($r['mod_type'] == "guild_sword_pic")
			{
				$approvelink="&action=guildweppic&do=approve";
				$denylink="&action=guildweppic&do=deny";
			}
			echo "
			<div class='row'>
				<div class='col-12'>
					<div class='card'>
						<div class='card-body'>
							<div class='row'>
								<div class='col-6 col-md-4 col-xl-3'>
									<a href='../profile.php?user={$r['mod_user']}'>{$api->SystemUserIDtoName($r['mod_user'])}</a> [{$r['mod_user']}]
								</div>
								<div class='col-6 col-md-4 col-xl-3'>
									<i>{$r['mod_type']}</i>
								</div>
								<div class='col-12 col-md-6 col-lg-5'>
									<b>Item:</b> <a href='../iteminfo.php?ID={$r['mod_item']}'>{$api->SystemItemIDtoName($r['mod_item'])}</a>
								</div>
								<div class='col-12 col-md-6 col-lg-12'>
									<b>New:</b> {$r['mod_change']}
								</div>
								<div class='col-6'>
									<a href='?id={$r['mod_id']}{$approvelink}' class='btn btn-success btn-block'><i class='fas fa-check'></i></a>
								</div>
								<div class='col-6'>
									<a href='?id={$r['mod_id']}{$denylink}' class='btn btn-danger btn-block'><i class='fas fa-times'></i></a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>";
		}
	}
}

function guildwepname()
{
	global $db, $api, $userid, $ir, $h;
	$fieldToEdit='itmname';
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
	//Is our action set?
	if (!isset($_GET['do']))
	{
		alert('danger',"Uh Oh!","Please go back and ensure you selected the correct link to deny or approve.",true,'?action=listall');
		die($h->endpage());
	}
	//Is our id set?
	if (empty($_GET['id']))
	{
		alert('danger',"Uh Oh!","Invalid listing to moderate.",true,'?action=listall');
		die($h->endpage());
	}
	$q=$db->query("SELECT * FROM `staff_moderation_board` WHERE `mod_id` = {$_GET['id']}");
	//Does this listing exist in the db?
	if ($db->num_rows($q) == 0)
	{
		alert('danger',"Uh Oh!","The listing you are trying to moderate does not exist.",true,'?action=listall');
		die($h->endpage());
	}
	//Assign listing to a variable for us to use.
	$r=$db->fetch_row($q);
	//Get user's guild.
	$userGuild = $db->fetch_single($db->query("SELECT `guild` FROM `users` WHERE `userid` = {$r['mod_user']}"));
	//Approve
	if ($_GET['do'] == 'approve')
	{
	    $r['mod_change']=$db->escape(strip_tags(stripslashes($r['mod_change'])));
		$db->query("UPDATE `items` SET `{$fieldToEdit}` = '{$r['mod_change']}' WHERE `itmid` = {$r['mod_item']}");
		$api->GameAddNotification($r['mod_user'],"Your request to rename your guild's weapon has been approved.");
		if ($userGuild > 0)
		{
			$api->GuildAddNotification($userGuild, "The game adminstration has approved your guild's request to rename your guild's weapon.");
		}
		$db->query("DELETE FROM `staff_moderation_board` WHERE `mod_id` = {$_GET['id']}");
		alert('success',"Success!","You have approved this listing.",true,'?action=listall');
		$api->SystemLogsAdd($userid, 'staff', "Approved moderation listing ID {$_GET['id']}.");
	}
	//Deny
	elseif ($_GET['do'] == 'deny')
	{
		$api->GameAddNotification($r['mod_user'],"Your request to rename your guild's weapon has been denied.");
		if ($userGuild > 0)
		{
			$api->GuildAddNotification($userGuild, "The game adminstration has denied your guild's request to rename your guild's weapon.");
		}
		$db->query("DELETE FROM `staff_moderation_board` WHERE `mod_id` = {$_GET['id']}");
		alert('success',"Success!","You have denied this listing.",true,'?action=listall');
		$api->SystemLogsAdd($userid, 'staff', "Denied moderation listing ID {$_GET['id']}.");
	}
	//Fail
	else
	{
		alert('danger',"Uh Oh!","Invalid action specified.",true,'?action=listall');
		die($h->endpage());
	}
}
function guildweppic()
{
	global $db, $api, $userid, $ir, $h;
	$fieldToEdit='icon';
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
	//Is our action set?
	if (!isset($_GET['do']))
	{
		alert('danger',"Uh Oh!","Please go back and ensure you selected the correct link to deny or approve.",true,'?action=listall');
		die($h->endpage());
	}
	//Is our id set?
	if (empty($_GET['id']))
	{
		alert('danger',"Uh Oh!","Invalid listing to moderate.",true,'?action=listall');
		die($h->endpage());
	}
	$q=$db->query("SELECT * FROM `staff_moderation_board` WHERE `mod_id` = {$_GET['id']}");
	//Does this listing exist in the db?
	if ($db->num_rows($q) == 0)
	{
		alert('danger',"Uh Oh!","The listing you are trying to moderate does not exist.",true,'?action=listall');
		die($h->endpage());
	}
	//Assign listing to a variable for us to use.
	$r=$db->fetch_row($q);
	//Get user's guild.
	$userGuild = $db->fetch_single($db->query("SELECT `guild` FROM `users` WHERE `userid` = {$r['mod_user']}"));
	//Approve
	if ($_GET['do'] == 'approve')
	{
		$db->query("UPDATE `items` SET `{$fieldToEdit}` = '{$r['mod_change']}', `color` = 'img' WHERE `itmid` = {$r['mod_item']}");
		$api->GameAddNotification($r['mod_user'],"Your request to change your guild's weapon picture has been approved.");
		if ($userGuild > 0)
		{
			$api->GuildAddNotification($userGuild, "The game adminstration has approved your guild's request to change your guild's weapon picture.");
		}
		$db->query("DELETE FROM `staff_moderation_board` WHERE `mod_id` = {$_GET['id']}");
		alert('success',"Success!","You have approved this listing.",true,'?action=listall');
		$api->SystemLogsAdd($userid, 'staff', "Approved moderation listing ID {$_GET['id']}.");
	}
	//Deny
	elseif ($_GET['do'] == 'deny')
	{
		$api->GameAddNotification($r['mod_user'],"Your request to change your guild's weapon picture has been denied.");
		if ($userGuild > 0)
		{
			$api->GuildAddNotification($userGuild, "The game adminstration has denied your guild's request to change your guild's weapon picture.");
		}
		$db->query("DELETE FROM `staff_moderation_board` WHERE `mod_id` = {$_GET['id']}");
		alert('success',"Success!","You have denied this listing.",true,'?action=listall');
		$api->SystemLogsAdd($userid, 'staff', "Denied moderation listing ID {$_GET['id']}.");
	}
	//Fail
	else
	{
		alert('danger',"Uh Oh!","Invalid action specified.",true,'?action=listall');
		die($h->endpage());
	}
}
$h->endpage();