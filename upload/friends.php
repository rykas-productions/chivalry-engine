<?php
require_once('globals.php');
if ($ir['vip_days'] == 0)
{
    alert('danger',"Uh Oh!","This feature is for players with VIP Days.",true,'index.php');
    die($h->endpage());
}
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
	case "add":
		add_friend();
		break;
	case "remove":
		remove_friend();
		break;
	case "ccomment":
		change_comment();
		break;
	default:
		friends_list();
		break;
}
function friends_list()
{
    global $db, $ir, $userid, $h;
	$ir['friend_count']=$db->fetch_single($db->query("SELECT COUNT(`friend_id`) FROM `friends` WHERE `friended` = {$userid}"));
    echo "
<a href='?action=add'>Add an friend</a><br />
These are the people on your friends list.
<br />
    {$ir['friend_count']} player(s) have added you to their friends list.
<br />
<table class='table table-bordered table-striped'>
		<thead>
		<tr>
			<th>Name</th>
			<th>Mail</th>
			<th>Attack</th>
			<th>Remove</th>
			<th>Comment</th>
			<th>Status</th>
		</tr>
		</thead>
		<tbody>
   ";
    $q =
            $db->query(
                    "SELECT `comment`, `friend_id`, `laston`, `vip_days`,
                     `username`, `userid`
                     FROM `friends` AS `fl`
                     LEFT JOIN `users` AS `u` ON `fl`.`friended` = `u`.`userid`
                     WHERE `fl`.`friender` = $userid
                     ORDER BY `u`.`username` ASC");
    while ($r = $db->fetch_row($q))
    {
		$laston=time()-900;
        $on =
                ($r['laston'] >= $laston)
                        ? '<span class="text-success">Online</font>'
                        : '<span class="text-danger">Offline</font>';
		$r['username'] = ($r['vip_days']) ? "<span class='text-danger'>{$r['username']} <i class='fa fa-shield'
        data-toggle='tooltip' title='{$r['vip_days']} VIP Days remaining.'></i></span>" : $r['username'];
        if (!$r['comment'])
        {
            $r['comment'] = 'N/A';
        }
        echo "
		<tr>
			<td><a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]</td>
			<td><a href='inbox.php?action=compose&user={$r['userid']}'>Mail</a></td>
			<td><a href='attack.php?user={$r['userid']}'>Attack</a></td>
			<td><a href='?action=remove&f={$r['friend_id']}'>Remove</a></td>
			<td>" . strip_tags($r['comment']) . "<br />[<a href='?action=ccomment&f={$r['friend_id']}'>Change</a>]</td>
			<td>$on</td>
		</tr>
   ";
    }
    $db->free_result($q);
    echo "</tbody></table>";
	$h->endpage();
}
function add_friend()
{
    global $db, $ir, $h, $userid, $api;
    if (isset($_POST['ID']))
    {
		$_POST['ID'] =
            (isset($_POST['ID']) && is_numeric($_POST['ID']))
                    ? abs(intval($_POST['ID'])) : '';
		$_POST['comment'] =
            (isset($_POST['comment']) && is_string($_POST['comment']))
                    ? $db->escape(strip_tags(stripslashes($_POST['comment'])))
                    : '';
        $qc =
                $db->query(
                        "SELECT COUNT(`friender`)
                         FROM `friends`
                         WHERE `friender` = $userid
                         AND `friended` = {$_POST['ID']}");
        $dupe_count = $db->fetch_single($qc);
        $db->free_result($qc);
        $q =
                $db->query(
                        "SELECT `username`
                         FROM `users`
                         WHERE `userid` = {$_POST['ID']}");
        if ($dupe_count > 0)
        {
            alert('danger',"Uh Oh!","You cannot add the same person more than once.");
        }
        else if ($userid == $_POST['ID'])
        {
            alert('danger',"Uh Oh!","You cannot be so alone that you would want to be your own friend, right?");
        }
        else if ($db->num_rows($q) == 0)
        {
            alert('danger',"Uh Oh!","You are trying to add a non-existent user to your friends list. There are no imaginary friends here.");
        }
        else
        {
            $db->query(
                    "INSERT INTO `friends`
                     VALUES(NULL,  {$_POST['ID']}, {$userid}, '{$_POST['comment']}')");
			alert('success',"Success!","You have successfully added {$api->SystemUserIDtoName($_POST['ID'])} to your friends list.",true,'friends.php');
			$api->GameAddNotification($_POST['ID'],"{$ir['username']} has added you to their friend's list. You may add them to yours by clicking <a href='friends.php?action=add&ID={$userid}'>here</a>.");
        }
        $db->free_result($q);
    }
    else
    {
        $_GET['ID'] =
                (isset($_GET['ID']) && is_numeric($_GET['ID']))
                        ? abs(intval($_GET['ID'])) : '';
		echo "<table class='table table-bordered'>
		<form method='post'>
			<tr>
				<th colspan='2'>
					Select the user you wish to add to your friends list. Feel free to give them a friendly comment.
				</th>
			</tr>
			<tr>
				<th>
					Friend
				</th>
				<td>
					" . user_dropdown('ID',$_GET['ID']) ."
				</td>
			</tr>
			<tr>
				<th>
					Comment
				</th>
				<td>
					<input type='text' class='form-control' name='comment'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Add Friend'>
				</td>
			</tr>
		</form>
		</table>";
    }
	$h->endpage();
}
function remove_friend()
{
    global $db, $ir, $userid, $h;
    $_GET['f'] =
            (isset($_GET['f']) && is_numeric($_GET['f']))
                    ? abs(intval($_GET['f'])) : '';
    if (empty($_GET['f']))
    {
        alert('danger',"Uh Oh!","Invalid use.",true,'friends.php');
        die($h->endpage());
    }

    $q =
            $db->query(
                    "SELECT `friender`
                     FROM `friends`
                     WHERE `friend_id` = {$_GET['f']} AND `friender` = $userid");
    if ($db->num_rows($q) == 0)
    {
        alert('danger',"Uh Oh!","You are trying to remove a friend that isn't listed as your friend.",true,'friends.php');
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
    $db->query(
            "DELETE FROM `friends`
            WHERE `friend_id` = {$_GET['f']} AND `friender` = $userid");
   alert('success',"Success!","You have successfully unfriended this friend.",true,'friends.php');
   $h->endpage();
}
function change_comment()
{
    global $db, $ir, $c, $userid, $h;
    if (isset($_POST['f']))
    {
		$_POST['f'] =
            (isset($_POST['f']) && is_numeric($_POST['f']))
                    ? abs(intval($_POST['f'])) : '';
		$_POST['comment'] =
            $db->escape(strip_tags(stripslashes($_POST['comment'])));
        $q =
                $db->query(
                        "SELECT COUNT(`friend_id`)
                     FROM `friends`
                     WHERE `friend_id` = {$_POST['f']} AND `friender` = $userid");
        if ($db->fetch_single($q) == 0)
        {
            $db->free_result($q);
            alert("danger","Uh Oh!","Friends list listing does not exist.",true,'friends.php');
            die($h->endpage());
        }
        $db->free_result($q);
        $db->query(
                "UPDATE `friends`
                 SET `comment` = '{$_POST['comment']}'
                 WHERE `friend_id` = {$_POST['f']} AND `friender` = $userid");
        alert("success","Success!","You have successfully edited this friend's comment.",true,'friends.php');
    }
    else
    {
        $_GET['f'] = (isset($_GET['f']) && is_numeric($_GET['f'])) ? abs(intval($_GET['f'])) : '';
        if (empty($_GET['f']))
        {
            alert("danger","Uh Oh!","Please select the friend who you wish to edit their comment.",true,'friends.php');
			die($h->endpage());
        }
        $q =
                $db->query(
                        "SELECT `comment`
                         FROM `friends`
                         WHERE `friend_id` = {$_GET['f']}
                         AND `friender` = $userid");
        if ($db->num_rows($q))
        {
            $r = $db->fetch_row($q);
            $comment =
                    stripslashes(
                            htmlentities($r['comment'], ENT_QUOTES,
                                    'ISO-8859-1'));
            echo "
			<form method='post'>
			<table class='table table-bordered'>
			<input type='hidden' name='f' value='{$_GET['f']}'>
				<thead>
					<tr>
						<th colspan='2'>
						Changing friend's comment...
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>
							Comment
						</th>
						<td>
							<input type='text' name='comment' class='form-control' value='{$comment}'>
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<input type='submit' class='btn btn-primary' value='Change Comment' />
						</td>
					</tr>
				</tbody>
			</table>
			</form>";
        }
        else
        {
            alert("danger","Uh Oh!","You can only edit a friend's comment for friends who are on your friends list.",true,'friends.php');
        }
    }
	$h->endpage();
}