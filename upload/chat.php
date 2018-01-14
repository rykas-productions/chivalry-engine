<?php
$menuhide=1;
require('globals.php');
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
if (!isset($_SESSION['tresde'])) {
	$_SESSION['tresde'] = 0;
}
switch ($_GET['action']) {
    default:
        home();
        break;
}
function home()
{
	global $db,$ir,$api,$h,$userid;
	$time = (isset($_GET['time']) && is_numeric($_GET['time'])) ? abs($_GET['time']) : time();
	$tresder = (Random(100, 999));
	$mb = $db->query("SELECT * FROM `mail_bans` WHERE `mbUSER` = {$userid}");
	$mbd=0;
	if ($db->num_rows($mb) != 0)
		$mbd=1;
	if (isset($_POST['msg']))
	{
		$msg = $db->escape(nl2br(htmlentities(stripslashes($_POST['msg']), ENT_QUOTES, 'ISO-8859-1')));
		$_GET['tresde'] = (isset($_GET['tresde']) && is_numeric($_GET['tresde'])) ? abs($_GET['tresde']) : 0;
		if (($_SESSION['tresde'] == $_GET['tresde']) || $_GET['tresde'] < 100) {
			
		}
		elseif (empty($msg))
		{
			echo "Please specify a chat message.";
		}
		elseif (strlen($msg) > 500)
		{
			echo "Chat messages cannot be longer than 500 characters.";
		}
		elseif ($mbd != 0) {
			echo "You cannot send chat messages if you're mail-banned.";
		}
		else
		{
			$_SESSION['tresde'] = $_GET['tresde'];
			$db_time=time();
			$db->query("INSERT INTO `chat` (`chat_user`, `chat_time`, `chat_msg`) VALUES ('{$userid}', '{$db_time}', '{$msg}')");
		}
		$fiveminago=time()-300;
		$l3c=$db->query("SELECT * FROM `chat` WHERE `chat_user` = {$userid} AND `chat_time` >= {$fiveminago}");
		$same=0;
		while ($ltr = $db->fetch_row($l3c))
		{
			if ($ltr['chat_msg'] == $msg)
				$same=$same+1;
		}
		if ($same >= 3)
		{
			$timed=time()+259200;
			$db->query("INSERT INTO `mail_bans`
						(`mbUSER`, `mbREASON`, `mbBANNER`, `mbTIME`) VALUES
						('{$userid}', 'Spamming', '1', '{$timed}')");
			$api->GameAddNotification($userid, "You have been mail-banned for 3 days for the reason: 'Spamming'.");
			staffnotes_entry($userid,"Mail banned for 3 for 'Spamming'.",0);
		}
	}
	echo "<h3><i class='far fa-comment'></i> Chivalry is Dead Chat</h3><br />
	<div id='row'>
		<div id='responsecontainer' class='responsive'></div>
	</div>";
	if ($mbd != 1)
	{
		echo"
		<form method='post' action='?tresde={$tresder}'>
			<span class='input-group-btn'>
				<input type='text' class='form-control' name='msg' placeholder='Chat message' required='1'>
				<input type='submit' class='btn btn-primary'>
			</span>
		</form>";
	}
}
$h->endpage();
echo "
	<script>
		 $(document).ready(function() {
			 $('#responsecontainer').load('chat2.php?action=getchat');
		   var refreshId = setInterval(function() {
			  $('#responsecontainer').load('chat2.php?action=getchat');
		   }, 1000);
		   $.ajaxSetup({ cache: false });
		});
	</script>
	";