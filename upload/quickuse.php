<?php
/*
	File:		quickuse.php
	Created: 	10/18/2017 at 11:26AM Eastern Time
	Info: 		Quickly use medical/dungeon items
	Author:		TheMasterGeneral
	Website: 	http://chivalryisdead.x10.mx/
*/
require('globals.php');
if (isset($_GET['dungeon'])) {
	if ($ir['ditem'] == 1)
	{
		if (!$api->UserHasItem($userid, 29, 1)) {
			alert("danger", "Uh Oh!", "You need at least one Lockpick to use this quick link.", true, 'index.php');
			die($h->endpage());
		}
		if (!$api->UserStatus($userid, 'dungeon')) {
			alert("danger", "Uh Oh!", "Why would you want to use a Lockpick if you're not in the dungeon?", true, 'index.php');
			die($h->endpage());
		}
		$api->UserTakeItem($userid, 29, 1);
		$api->UserStatusSet($userid, 'dungeon', -10, 'Placeholder');
		$api->SystemLogsAdd($userid, 'itemuse', "Used Lockpick.");
		alert('success', "Success!", "Lockpick was used successfully.", true, 'index.php');
		$h->endpage();
	}
	if ($ir['ditem'] == 2)
	{
		if (!$api->UserHasItem($userid, 30, 1)) {
			alert("danger", "Uh Oh!", "You need at least one Dungeon Key to use this quick link.", true, 'index.php');
			die($h->endpage());
		}
		if (!$api->UserStatus($userid, 'dungeon')) {
			alert("danger", "Uh Oh!", "Why would you want to use a Dungeon Key if you're not in the dungeon?", true, 'index.php');
			die($h->endpage());
		}
		$api->UserTakeItem($userid, 30, 1);
		$api->UserStatusSet($userid, 'dungeon', -30, '');
		$api->SystemLogsAdd($userid, 'itemuse', "Used Dungeon Key.");
		alert('success', "Success!", "Dungeon Key was used successfully.", true, 'index.php');
		$h->endpage();
	}
	if ($ir['ditem'] == 3)
	{
		if (!$api->UserHasItem($userid, 31, 1)) {
			alert("danger", "Uh Oh!", "You need at least one Dungeon Key Set to use this quick link.", true, 'index.php');
			die($h->endpage());
		}
		if (!$api->UserStatus($userid, 'dungeon')) {
			alert("danger", "Uh Oh!", "Why would you want to use a Dungeon Key Set if you're not in the dungeon?", true, 'index.php');
			die($h->endpage());
		}
		$api->UserTakeItem($userid, 31, 1);
		$api->UserStatusSet($userid, 'dungeon', -75, '');
		$api->SystemLogsAdd($userid, 'itemuse', "Used Dungeon Key Set.");
		alert('success', "Success!", "Dungeon Key Set was used successfully.", true, 'index.php');
		$h->endpage();
	}
} elseif (isset($_GET['infirmary'])) {
    if ($ir['iitem'] == 1)
	{
		if (!$api->UserHasItem($userid, 5, 1)) {
			alert("danger", "Uh Oh!", "You need at least one Leech to use this quick link.", true, 'index.php');
			die($h->endpage());
		}
		if (!$api->UserStatus($userid, 'infirmary')) {
			alert("danger", "Uh Oh!", "Why would you want to use a Leech if you're not in the infirmary?", true, 'index.php');
			die($h->endpage());
		}
		$api->UserTakeItem($userid, 5, 1, '');
		$api->UserStatusSet($userid, 'infirmary', -10, 'Placeholder');
		$api->SystemLogsAdd($userid, 'itemuse', "Used Leech.");
		alert('success', "Success!", "Leech was used successfully.", true, 'index.php');
		$h->endpage();
	}
	if ($ir['iitem'] == 2)
	{
		if (!$api->UserHasItem($userid, 6, 1)) {
			alert("danger", "Uh Oh!", "You need at least one Linen Wrap to use this quick link.", true, 'index.php');
			die($h->endpage());
		}
		if (!$api->UserStatus($userid, 'infirmary')) {
			alert("danger", "Uh Oh!", "Why would you want to use a Linen Wrap if you're not in the infirmary?", true, 'index.php');
			die($h->endpage());
		}
		$api->UserTakeItem($userid, 6, 1);
		$api->UserStatusSet($userid, 'infirmary', -30, '');
		$api->SystemLogsAdd($userid, 'itemuse', "Used Linen Wrap.");
		alert('success', "Success!", "Linen Wrap was used successfully.", true, 'index.php');
		$h->endpage();
	}
    if ($ir['iitem'] == 3)
	{
		if (!$api->UserHasItem($userid, 100, 1)) {
			alert("danger", "Uh Oh!", "You need at least one Acupuncture Needle to use this quick link.", true, 'index.php');
			die($h->endpage());
		}
		if (!$api->UserStatus($userid, 'infirmary')) {
			alert("danger", "Uh Oh!", "Why would you want to use a Acupuncture Needle if you're not in the infirmary?", true, 'index.php');
			die($h->endpage());
		}
		$api->UserTakeItem($userid, 100, 1);
        $api->UserInfoSet($userid,'hp',5,true);
		$api->UserStatusSet($userid, 'infirmary', -75, '');
		$api->SystemLogsAdd($userid, 'itemuse', "Used Acupuncture Needle.");
		alert('success', "Success!", "Acupuncture Needle was used successfully.", true, 'index.php');
		$h->endpage();
	}
} else {
    //Nope
    alert('danger',"Uh Oh!","No action specified.",true,'index.php');
    $h->endpage();
}