<?php
require('globals.php');
if ($api->UserHasItem($userid,68,1))
{
    $effect = constant("invisibility");
    if (userHasEffect($userid, $effect))
	    userUpdateEffect($userid, $effect, (3600*3));
	else
		userGiveEffect($userid, $effect, (3600*3));
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