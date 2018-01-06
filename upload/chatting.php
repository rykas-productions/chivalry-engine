<?php
$menuhide=1;
require('globals.php');
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'getchat':
        getChat();
        break;
    default:
        home();
        break;
}
function home()
{
	global $db,$ir,$api,$h,$userid;
	$time = (isset($_GET['time']) && is_numeric($_GET['time'])) ? abs($_GET['time']) : time();
	if (isset($_POST['msg']))
	{
		$msg = $db->escape(nl2br(htmlentities(stripslashes($_POST['msg']), ENT_QUOTES, 'ISO-8859-1')));
		if (empty($msg))
		{
			echo "Please specify a chat message.";
		}
		elseif (strlen($msg) > 500)
		{
			echo "Chat messages cannot be longer than 500 characters.";
		}
		else
		{
			$db_time=time();
			$db->query("INSERT INTO `chat` (`chat_user`, `chat_time`, `chat_msg`) VALUES ('{$userid}', '{$db_time}', '{$msg}')");
		}
	}
	echo "<h3>Chivalry is Dead Chat</h3><br />
	<div id='row'>
		<div id='responsecontainer' class='responsive'></div>
	</div>
	<form method='post'>
		<span class='input-group-btn'>
			<input type='text' class='form-control' name='msg' placeholder='Chat message' required='1'>
			<input type='submit' class='btn btn-primary'>
		</span>
	</form>";
}
function getChat()
{
	global $db,$ir,$api,$h,$userid;
	$time = (isset($_GET['time']) && is_numeric($_GET['time'])) ? abs($_GET['time']) : 1;
	$q=$db->query("SELECT * FROM `chat` WHERE `chat_time` >= {$time} ORDER BY `chat_time` ASC LIMIT 8");
	if ($db->num_rows($q) == 0)
	{
		echo "Welcome to the chat, {$ir['username']}.";
	}
	else
	{
		echo "<table class='table'>";
		while ($r=$db->fetch_row($q))
		{
			echo "<tr>
					<td width='10%'>
					" . date('g:i:s a',$r['chat_time']) . "
					</td>
					<td width='10%'>
					{$api->SystemUserIDtoName($r['chat_user'])}
					</td>
					<td>
						" . htmlentities(stripslashes($r['chat_msg']), ENT_QUOTES, 'ISO-8859-1') ."
					</td>
				</tr>";
		}
		echo "</table>";
	}
	exit;
}
$h->endpage();
echo "
	<script>
		 $(document).ready(function() {
			 $('#responsecontainer').load('chatting.php?action=getchat');
		   var refreshId = setInterval(function() {
			  $('#responsecontainer').load('chatting.php?action=getchat');
		   }, 1000);
		   $.ajaxSetup({ cache: false });
		});
	</script>
	";