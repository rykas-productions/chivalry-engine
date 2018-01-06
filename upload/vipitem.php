<?php
require('globals.php');
if (!isset($_GET['item'])) {
    $_GET['item'] = '';
}
switch ($_GET['item']) {
	case 'autohex':
        autohex();
        break;
	case 'autobor':
        autobor();
        break;
    default:
        alert("danger","Uh Oh!","Please specify a valid VIP Item to use!",true,'inventory.php');
		$h->endpage();
        break;
}
function autohex()
{
	global $db,$h,$api, $userid;
	if ($api->UserHasItem($userid,91))
	{
		$db->query("UPDATE `user_settings` SET `autohex` = `autohex` + 3000 WHERE `userid` = {$userid}");
		$api->UserTakeItem($userid,91,1);
		alert("success","Success!","Auto Hexbag Opener has been redeemed to your account.",true,'inventory.php');
		$api->SystemLogsAdd($userid, 'itemuse', "Used a/an Auto Hexbag Opener item.");
	}
	else
	{
		alert('danger',"Uh Oh!","You do not have the required item to use this page!",true,'inventory.php');
	}
	$h->endpage();
}
function autobor()
{
	global $db,$h,$api, $userid;
	if ($api->UserHasItem($userid,92))
	{
		$db->query("UPDATE `user_settings` SET `autobor` = `autobor` + 30000 WHERE `userid` = {$userid}");
		$api->UserTakeItem($userid,92,1);
		alert("success","Success!","Auto Boxes of Random Opener has been redeemed to your account.",true,'inventory.php');
		$api->SystemLogsAdd($userid, 'itemuse', "Used a/an Auto Boxes of Random Opener item.");
	}
	else
	{
		alert('danger',"Uh Oh!","You do not have the required item to use this page!",true,'inventory.php');
	}
	$h->endpage();
}