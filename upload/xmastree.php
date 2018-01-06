<?php
require('globals.php');
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'gift':
        gift();
        break;
	case 'open':
        opengift();
        break;
    default:
        home();
        break;
}
function home()
{
	global $db,$userid,$api,$h;
	echo "<img src='assets/img/christmas/xmastree.png' class='img-fluid'><br />
	This is Chivalry is Dead's Christmas Tree! You may <a href='?action=gift'>gift</a> presents to other players, or 
	you may look through the tree to see if there's any presents for you! Presents not opened by January 1st will be refunded 
	to the gifter.<hr />";
	$q=$db->query("SELECT * FROM `christmas_tree`");
	$count=0;
	echo "<table class='table table-bordered'>";
	while ($r = $db->fetch_row($q))
	{
		if ($r['tree_sender'] == 0)
			$r['tree_sender_name'] = 'Santa';
		else
			$r['tree_sender_name']=$api->SystemUserIDtoName($r['tree_sender']);
		$r['tree_receiver_name']=$api->SystemUserIDtoName($r['tree_receiver']);
		$count=$count+1;
		if ($count == 0)
			echo "<tr>";
		echo "<td>
				<b>From:</b> {$r['tree_sender_name']}<br />
				<b>To:</b> {$r['tree_receiver_name']}<br />
				[<a href='?action=open&id={$r['tree_id']}'>Open</a>]
				</td>";
		if ($count == 6)
		{
			echo "</tr>";
			$count = 0;
		}
	}
	echo"</table>";
	$h->endpage();
}
function opengift()
{
	global $db, $userid, $api, $h, $ir;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
	//GET is empty.
    if (empty($_GET['id'])) {
        alert('danger', "Uh Oh!", "Please select a valid user to accept the challenge from.",true,'xmastree.php');
        die($h->endpage());
    }
    $q = $db->query("SELECT *
                    FROM `christmas_tree`
                    WHERE `tree_id` = {$_GET['id']}");
	$r=$db->fetch_row($q);
	if ($r['tree_receiver'] != $userid)
	{
		alert('danger', "Uh Oh!", "This is NOT your present to open!",true,'xmastree.php');
        die($h->endpage());
	}
	if (time() < 1514178000)
	{
		$xmas=TimeUntil_Parse(1514178000);
		alert('danger', "Uh Oh!", "It is not Chrismtas day. Try again in {$xmas}.",true,'xmastree.php');
        die($h->endpage());
	}
	$api->UserGiveItem($userid,$r['tree_item'],$r['tree_qty']);
	$api->GameAddNotification($r['tree_sender'],"<a href='profile.php?user={$userid}'>{$ir['username']}</a> opened your present of {$r['tree_qty']} {$api->SystemItemIDtoName($r['tree_item'])}(s)!");
	alert('success',"Success!","You open your present and received {$r['tree_qty']} {$api->SystemItemIDtoName($r['tree_item'])}(s)! Merry Christmas!");
	$log = $db->escape("Sent {$r['tree_qty']} {$api->SystemItemIDtoName($r['tree_item'])}(s) to {$ir['username']} [{$userid}].");
	$api->SystemLogsAdd($r['tree_sender'], 'itemsend', $log);
	$db->query("DELETE FROM `christmas_tree` WHERE `tree_id` = {$_GET['id']}");
	$h->endpage();
}
function gift()
{
	global $db,$userid,$ir,$api,$h;
	if (isset($_POST['user']))
	{
		$_POST['ID'] = (isset($_POST['ID']) && is_numeric($_POST['ID'])) ? abs($_POST['ID']) : '';
		$_POST['QTY'] = (isset($_POST['QTY']) && is_numeric($_POST['QTY'])) ? abs($_POST['QTY']) : '';
		$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : '';
		if (!isset($_POST['verf']) || !verify_csrf_code("santa_send", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Form requests expire fairly quickly. Go back and fill in the form faster next time.");
            die($h->endpage());
        }
        $haveitem=$api->UserHasItem($userid,$_POST['ID'],$_POST['QTY']);
        if (!$haveitem)
		{
            alert('danger', "Uh Oh!", "You do not have this item and/or quantity to wrap up as a gift.");
            die($h->endpage());
        }
		$userexist=$api->SystemUserIDtoName($_POST['user']);
		if (!$userexist)
		{
			alert('danger', "Uh Oh!", "You must gift to an existing player.");
            die($h->endpage());
		}
		if ($_POST['user'] == $userid)
		{
			alert('danger', "Uh Oh!", "You cannot gift to yourself.");
            die($h->endpage());
		}
		if ($api->SystemCheckUsersIPs($userid, $_POST['user'])) 
		{
            alert('danger', "Uh Oh!", "You cannot wrap gifts for someone on the same IP Address as you.", true, 'inventory.php');
            die($h->endpage());
        }
		$from = ($_POST['from'] == 'no') ? $userid : 0;
		$db->query("INSERT INTO `christmas_tree` 
					(`tree_sender`, `tree_receiver`, `tree_item`, `tree_qty`) 
					VALUES 
					('{$from}', '{$_POST['user']}', '{$_POST['ID']}', '{$_POST['QTY']}')");
		item_remove($userid, $_POST['ID'], $_POST['QTY']);
		alert('success', "Success!", "You have successfully wrapped this gift, and placed it under the Christmas Tree. The recipient can open it on Christmas Day.", true, 'xmastree.php');
		$h->endpage();
	}
	else
	{
		$csrf = request_csrf_html("santa_send");
        echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Fill out this form completely to wrap up a gift for Christmas.
				</th>
			</tr>
			<tr>
				<th>
					Player
				</th>
				<td>
					" . user_dropdown('user') . "
				</th>
			</tr>
			<tr>
				<th>
					Item
				</th>
				<td>
					" . inventory_dropdown('ID') . "
				</th>
			</tr>
			<tr>
				<th>
					Quantity
				</th>
				<td>
					<input type='number' min='1' required='1' class='form-control' name='QTY'>
				</th>
			</tr>
			<tr>
				<th>
					Send anonymously?<br />
					<small>Will read as 'From: Santa'</small>
				</th>
				<td>
					<select name='from' type='dropdown' class='form-control'>
						<option value='no'>No, from me!</option>
						<option value='yes'>Yes, from Santa!</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Wrap Gift'
				</td>
			</tr>
			{$csrf}
		</table>
		</form>";
		$h->endpage();
	}
}