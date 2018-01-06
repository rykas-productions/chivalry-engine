<?php
$hidehdr=1;
require('globals_nonauth.php');
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'getchat':
        getChat();
        break;
    default:
        getChat();
        break;
}
function getChat()
{
	global $db,$ir,$api,$h,$userid;
	$time = (isset($_GET['time']) && is_numeric($_GET['time'])) ? abs($_GET['time']) : 1;
	$total=$db->fetch_single($db->query("SELECT COUNT(`chat_id`) FROM `chat`"));
	$count=$total-20;
	$q=$db->query("SELECT * FROM `chat` WHERE `chat_id` >= {$count} ORDER BY `chat_time` ASC LIMIT 30");
	if ($db->num_rows($q) == 0)
	{
		echo "Welcome to the chat, {$ir['username']}.";
	}
	else
	{
		while ($r=$db->fetch_row($q))
		{
			echo "<div style='text-align:left'>" . date('g:i:s a',$r['chat_time']) . " | {$api->SystemUserIDtoName($r['chat_user'])} | {$r['chat_msg']}</div>";
		}
	}
	exit;
}