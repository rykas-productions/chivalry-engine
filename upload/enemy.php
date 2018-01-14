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
		enemy_list();
		break;
}
function enemy_list()
{
    global $db, $ir, $userid, $h;
	$ir['friend_count']=$db->fetch_single($db->query("SELECT COUNT(`enemy_id`) FROM `enemy` WHERE `enemy_user` = {$userid}"));
    echo "
<a href='?action=add'>Add an enemy</a><br />
These are the people on your enemy list.
<br />
    {$ir['friend_count']} player(s) have added you to their enemy list.
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
                    "SELECT `comment`, `enemy_id`, `laston`, `vip_days`,
                     `username`, `userid`
                     FROM `enemy` AS `fl`
                     LEFT JOIN `users` AS `u` ON `fl`.`enemy_user` = `u`.`userid`
                     WHERE `fl`.`enemy_adder` = $userid
                     ORDER BY `u`.`username` ASC");
    while ($r = $db->fetch_row($q))
    {
		$laston=time()-900;
        $on =
                ($r['laston'] >= $laston)
                        ? '<span class="text-success">Online</font>'
                        : '<span class="text-danger">Offline</font>';
		$r['username'] = ($r['vip_days']) ? "<span class='text-danger'>{$r['username']} <i class='fas fa-shield-alt'
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
			<td><a href='?action=remove&f={$r['enemy_id']}'>Remove</a></td>
			<td>" . strip_tags($r['comment']) . "<br />[<a href='?action=ccomment&f={$r['enemy_id']}'>Change</a>]</td>
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
                        "SELECT COUNT(`enemy_adder`)
                         FROM `enemy`
                         WHERE `enemy_adder` = $userid
                         AND `enemy_user` = {$_POST['ID']}");
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
            alert('danger',"Uh Oh!","You cannot be so alone that you would want to be your own enemy, right?");
        }
        else if ($db->num_rows($q) == 0)
        {
            alert('danger',"Uh Oh!","You are trying to add a non-existent user to your enemy list. There are no imaginary enemy here.");
        }
        else
        {
            $db->query(
                    "INSERT INTO `enemy`
                     VALUES(NULL,  {$_POST['ID']}, {$userid}, '{$_POST['comment']}')");
			alert('success',"Success!","You have successfully added {$api->SystemUserIDtoName($_POST['ID'])} to your enemy list.",true,'enemy.php');
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
					Select the user you wish to add to your enemy list. Feel free to give them a friendly comment.
				</th>
			</tr>
			<tr>
				<th>
					Enemy
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
					<input type='submit' class='btn btn-primary' value='Add Enemy'>
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
        alert('danger',"Uh Oh!","Invalid use.",true,'enemy.php');
        die($h->endpage());
    }

    $q =
            $db->query(
                    "SELECT `enemy_adder`
                     FROM `enemy`
                     WHERE `enemy_id` = {$_GET['f']} AND `enemy_adder` = $userid");
    if ($db->num_rows($q) == 0)
    {
        alert('danger',"Uh Oh!","You are trying to remove a enemy that isn't listed as your enemy.",true,'enemy.php');
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
    $db->query(
            "DELETE FROM `enemy`
            WHERE `enemy_id` = {$_GET['f']} AND `enemy_adder` = $userid");
   alert('success',"Success!","You have successfully removed this enemy.",true,'enemy.php');
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
                        "SELECT COUNT(`enemy_id`)
                     FROM `enemy`
                     WHERE `enemy_id` = {$_POST['f']} AND `enemy_adder` = $userid");
        if ($db->fetch_single($q) == 0)
        {
            $db->free_result($q);
            alert("danger","Uh Oh!","enemy list listing does not exist.",true,'enemy.php');
            die($h->endpage());
        }
        $db->free_result($q);
        $db->query(
                "UPDATE `enemy`
                 SET `comment` = '{$_POST['comment']}'
                 WHERE `enemy_id` = {$_POST['f']} AND `enemy_adder` = $userid");
        alert("success","Success!","You have successfully edited this enemy's comment.",true,'enemy.php');
    }
    else
    {
        $_GET['f'] = (isset($_GET['f']) && is_numeric($_GET['f'])) ? abs(intval($_GET['f'])) : '';
        if (empty($_GET['f']))
        {
            alert("danger","Uh Oh!","Please select the enemy who you wish to edit their comment.",true,'enemy.php');
			die($h->endpage());
        }
        $q =
                $db->query(
                        "SELECT `comment`
                         FROM `enemy`
                         WHERE `enemy_id` = {$_GET['f']}
                         AND `enemy_adder` = $userid");
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
            alert("danger","Uh Oh!","You can only edit a enemy's comment for enemy who are on your enemy list.",true,'enemy.php');
        }
    }
	$h->endpage();
}