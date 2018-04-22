<?php
require('globals.php');
if ($api->UserHasItem($userid,68,1))
{
	if ($ir['invis'] > time())
	{
		$db->query("UPDATE `user_settings` SET `invis` = `invis` + 10800 WHERE `userid` = {$userid}");
	}
	else
	{
		$time=time()+(3600*3);
		$db->query("UPDATE `user_settings` SET `invis` = {$time} WHERE `userid` = {$userid}");
	}
	alert('success',"Success!","You have successfully used your {$api->SystemItemIDtoName(68)}!",true,'invispotion.php',"Use Another");
	$api->UserTakeItem($userid, 68, 1);
	$api->SystemLogsAdd($userid, 'itemuse', "Used {$api->SystemItemIDtoName(68)}.");
	$h->endpage();
}
else
{
	alert('danger',"Uh Oh!","You do not have the required item.",true,'inventory.php');
	die($h->endpage());
}