<?php
/*
	File:		contacts.php
	Created: 	4/4/2016 at 11:55PM Eastern Time
	Info: 		Allows players to add and delete users from their contact list.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
echo "
<table class='table table-bordered'>
	<tr>
		<td>
			<a href='inbox.php'>{$lang['MAIL_TH1_IN']}</a>
		</td>
		<td>
			<a href='inbox.php?action=outbox'>{$lang['MAIL_TH1_OUT']}</a>
		</td>
		<td>
			<a href='inbox.php?action=compose'>{$lang['MAIL_TH1_COMP']}</a>
		</td>
		<td>
			<a href='inbox.php?action=delall'>{$lang['MAIL_TH1_DEL']}</a>
		</td>
		<td>
			<a href='inbox.php?action=archive'>{$lang['MAIL_TH1_ARCH']}</a>
		</td>
		<td>
			<a href='contacts.php'>{$lang['MAIL_TH1_CONTACTS']}</a>
		</td>
	</tr>
</table>";
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case "add":
    add();
    break;
case "remove":
    remove();
    break;
default:
    home();
    break;
}
function home()
{
	global $db,$ir,$userid,$lang;
    echo "<a href='?action=add'>{$lang['CONTACT_ADD']}</a><br />
	{$lang['CONTACT_HOME']}
	<br />
	<table class='table table-bordered table-striped'>
		<tr>
			<th>
				{$lang['CONTACT_HOME1']}
			</th>
			<th>
				{$lang['CONTACT_HOME2']}
			</th>
			<th>
				{$lang['CONTACT_HOME3']}
			</th>
		</tr>";
    $q = $db->query("SELECT `c`.`c_ID`, `u`.`vip_days`, `username`, `userid` FROM `contact_list` AS `c`
                     LEFT JOIN `users` AS `u` ON `c`.`c_ADDED` = `u`.`userid` WHERE `c`.`c_ADDER` = $userid
                     ORDER BY `u`.`username` ASC");
    //List the user's contact list.
    while ($r = $db->fetch_row($q))
    {
        $d = '';
        $r['username'] = ($r['vip_days']) ? "<span style='color:red; font-weight:bold;'>{$r['username']}</span>
                        <span class='glyphicon glyphicon-star' data-toggle='tooltip'
                        title='{$r['username']} has {$r['vip_days']} VIP Days remaining.'></span>" : $r['username'];
        echo "
		<tr>
			<td>
				<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
			</td>
			<td>
				<a href='inbox.php?action=compose&user={$r['userid']}'>{$lang['CONTACT_HOME2']}</a>
			</td>
			<td>
				<a href='?action=remove&contact={$r['c_ID']}'>{$lang['CONTACT_HOME3']}</a>
			</td>
		</tr>";
    }
    $db->free_result($q);
    echo '</table>';
}
function add()
{
	global $db,$userid,$lang;
    //User has specifed someone to add to contact list.
	if (isset($_POST['user']))
	{
		$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : '';
		$qc = $db->query("SELECT COUNT(`c_ADDER`) FROM `contact_list` WHERE `c_ADDER` = {$userid} AND `c_ADDED` = {$_POST['user']}");
		$dupe_count = $db->fetch_single($qc);
        $db->free_result($qc);
		$q = $db->query("SELECT `username` FROM `users` WHERE `userid` = {$_POST['user']}");
        //Person specifed already on contact list.
		if ($dupe_count > 0)
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['CONTACT_ADD_ERR']);
        }
        //Person specifed is the current user.
        else if ($userid ==$_POST['user'])
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['CONTACT_ADD_ERR1']);
        }
        //Person specifed does not exist.
        else if ($db->num_rows($q) == 0)
        {
            $db->free_result($q);
            alert('danger',$lang['ERROR_GENERIC'],$lang['CONTACT_ADD_ERR2']);
        }
        //Person is added to contacts list.
		else
		{
			$db->query("INSERT INTO `contact_list` VALUES (NULL, {$_POST['user']}, {$userid})");
			$db->free_result($q);
			alert('success',$lang['ERROR_SUCCESS'],$lang['CONTACT_ADD_SUCC'],true,'contacts.php');
		}
	}
	else
	{
		if (!isset($_GET['user']))
		{
			$_GET['user'] = $userid;
		}
		else
		{
			$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : '';
		}
		echo "<table class='table table-bordered'>
		<form action='?action=add' method='post'>
			<tr>
				<th colspan='2'>
				{$lang['CONTACT_ADD']}
				</th>
			</tr>
			<tr>
				<th>
					{$lang['CONTACT_ADD1']}
				</th>
				<td>
					<input type='number' class='form-control' required='1' min='1' name='user' value='{$_GET['user']}'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='{$lang['CONTACT_ADD_BTN']}' class='btn btn-secondary'>
				</td>
			</tr>
		</form>
		</table>";
	}
}
function remove()
{
	global $db,$userid,$lang,$h;
	$_GET['contact'] = (isset($_GET['contact']) && is_numeric($_GET['contact'])) ? abs($_GET['contact']) : '';
    //User is trying to remove someone from contact list, but didn't specify their ID.
	if (empty($_GET['contact']))
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['CONTACT_REMOVE_ERR'],true,'contacts.php');
		die($h->endpage());
	}
	$qc = $db->query("SELECT COUNT(`c_ADDER`) FROM `contact_list` WHERE `c_ADDER` = {$userid} AND `c_ID` = {$_GET['contact']}");
	$exist_count = $db->fetch_single($qc);
    $db->free_result($qc);
    //Specified person is not on list.
	if ($exist_count == 0)
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['CONTACT_REMOVE_ERR1'],true,'contacts.php');
        die($h->endpage());
    }
    //Remove from list.
	$db->query("DELETE FROM `contact_list` WHERE `c_ID` = {$_GET['contact']} AND `c_ADDER` = {$userid}");
	alert('success',$lang['ERROR_SUCCESS'],$lang['CONTACT_REMOVE_SUCC'],true,'contacts.php');
}
$h->endpage();