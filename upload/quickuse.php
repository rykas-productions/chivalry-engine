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
			alert("danger", "Uh Oh!", "You need at least one lockpick to use this quick link.", true, 'index.php');
			die($h->endpage());
		}
		if (!$api->UserStatus($userid, 'dungeon')) {
			alert("danger", "Uh Oh!", "Why would you want to use a lockpick if you're not in the dungeon?", true, 'index.php');
			die($h->endpage());
		}
		$api->UserTakeItem($userid, 29, 1);
		$api->UserStatusSet($userid, 'dungeon', -10, 'Placeholder');
		$api->SystemLogsAdd($userid, 'itemuse', "Used a/an Lockpick item.");
		alert('success', "Success!", "Lockpick was used successfully.", true, 'index.php');
		$h->endpage();
	}
	if ($ir['ditem'] == 2)
	{
		if (!$api->UserHasItem($userid, 30, 1)) {
			alert("danger", "Uh Oh!", "You need at least one key to use this quick link.", true, 'index.php');
			die($h->endpage());
		}
		if (!$api->UserStatus($userid, 'dungeon')) {
			alert("danger", "Uh Oh!", "Why would you want to use a key if you're not in the dungeon?", true, 'index.php');
			die($h->endpage());
		}
		$api->UserTakeItem($userid, 30, 1);
		$api->UserStatusSet($userid, 'dungeon', -30, '');
		$api->SystemLogsAdd($userid, 'itemuse', "Used a/an Key item.");
		alert('success', "Success!", "Key was used successfully.", true, 'index.php');
		$h->endpage();
	}
	if ($ir['ditem'] == 3)
	{
		if (!$api->UserHasItem($userid, 31, 1)) {
			alert("danger", "Uh Oh!", "You need at least one key set to use this quick link.", true, 'index.php');
			die($h->endpage());
		}
		if (!$api->UserStatus($userid, 'dungeon')) {
			alert("danger", "Uh Oh!", "Why would you want to use a key set if you're not in the dungeon?", true, 'index.php');
			die($h->endpage());
		}
		$api->UserTakeItem($userid, 31, 1);
		$api->UserStatusSet($userid, 'dungeon', -75, '');
		$api->SystemLogsAdd($userid, 'itemuse', "Used a/an Key Set item.");
		alert('success', "Success!", "Key Set was used successfully.", true, 'index.php');
		$h->endpage();
	}
} elseif (isset($_GET['infirmary'])) {
    if ($ir['iitem'] == 1)
	{
		if (!$api->UserHasItem($userid, 5, 1)) {
			alert("danger", "Uh Oh!", "You need at least one leech to use this quick link.", true, 'index.php');
			die($h->endpage());
		}
		if (!$api->UserStatus($userid, 'infirmary')) {
			alert("danger", "Uh Oh!", "Why would you want to use a leech if you're not in the infirmary?", true, 'index.php');
			die($h->endpage());
		}
		$api->UserTakeItem($userid, 5, 1, '');
		$api->UserStatusSet($userid, 'infirmary', -10, 'Placeholder');
		$api->SystemLogsAdd($userid, 'itemuse', "Used a/an Leech item.");
		alert('success', "Success!", "Leech was used successfully.", true, 'index.php');
		$h->endpage();
	}
	if ($ir['iitem'] == 2)
	{
		if (!$api->UserHasItem($userid, 6, 1)) {
			alert("danger", "Uh Oh!", "You need at least one linen wrap to use this quick link.", true, 'index.php');
			die($h->endpage());
		}
		if (!$api->UserStatus($userid, 'infirmary')) {
			alert("danger", "Uh Oh!", "Why would you want to use a linen wrap if you're not in the infirmary?", true, 'index.php');
			die($h->endpage());
		}
		$api->UserTakeItem($userid, 6, 1);
		$api->UserStatusSet($userid, 'infirmary', -30, '');
		$api->SystemLogsAdd($userid, 'itemuse', "Used a/an Linen Wrap item.");
		alert('success', "Success!", "Linen Wrap was used successfully.", true, 'index.php');
		$h->endpage();
	}
} else {
    //Nope
    alert('danger',"Uh Oh!","No action specified.",true,'index.php');
    $h->endpage();
}