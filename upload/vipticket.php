<?php
require('globals.php');
$lootJSON = "items/scratch/vip_ticket";
if (!$api->UserHasItem($userid,89,1))
{
	alert('danger',"Uh Oh!","You need a VIP Scratch ticket to use one.",true,'inventory.php');
	die($h->endpage());
}
$loot = giveUserLoot($userid, $lootJSON);
alert("success","Success!","You scratch this spot off on a {$api->SystemItemIDtoName(89)}. {$loot} Congratulations!",true,'inventory.php');
$api->UserTakeItem($userid,89,1);
$api->SystemLogsAdd($userid, 'itemuse', "Used VIP Scratch Ticket.");
$h->endpage();