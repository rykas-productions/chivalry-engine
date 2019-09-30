<?php
/*
	File:		quickuse.php
	Created: 	10/18/2017 at 11:26AM Eastern Time
	Info: 		Quickly use medical/dungeon items
	Author:		TheMasterGeneral
	Website: 	http://chivalryisdead.x10.mx/
*/
require('globals.php');
$InfirmaryOut = $db->fetch_single($db->query("/*qc=on*/SELECT `infirmary_out` FROM `infirmary` WHERE `infirmary_user` = {$ir['userid']}"));
$DungeonOut = $db->fetch_single($db->query("/*qc=on*/SELECT `dungeon_out` FROM `dungeon` WHERE `dungeon_user` = {$ir['userid']}"));
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
		$TR=TimeUntil_Parse($DungeonOut-(10*60));
		$api->UserTakeItem($userid, 29, 1);
		$api->UserStatusSet($userid, 'dungeon', -10, 'Placeholder');
		$api->SystemLogsAdd($userid, 'itemuse', "Used Lockpick.");
		alert('success', "Success!", "Lockpick was used successfully. You have {$TR} remaining.", true, 'index.php');
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
		$TR=TimeUntil_Parse($DungeonOut-(30*60));
		$api->UserTakeItem($userid, 30, 1);
		$api->UserStatusSet($userid, 'dungeon', -30, '');
		$api->SystemLogsAdd($userid, 'itemuse', "Used Dungeon Key.");
		alert('success', "Success!", "Dungeon Key was used successfully. You have {$TR} remaining.", true, 'index.php');
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
		$TR=TimeUntil_Parse($DungeonOut-(75*60));
		$api->UserTakeItem($userid, 31, 1);
		$api->UserStatusSet($userid, 'dungeon', -75, '');
		$api->SystemLogsAdd($userid, 'itemuse', "Used Dungeon Key Set.");
		alert('success', "Success!", "Dungeon Key Set was used successfully. You have {$TR} remaining.", true, 'index.php');
		$h->endpage();
	}
    if ($ir['ditem'] == 4)
	{
		if (!$api->UserHasItem($userid, 206, 1)) {
			alert("danger", "Uh Oh!", "You need at least one Negative Begone to use this quick link.", true, 'index.php');
			die($h->endpage());
		}
		if (!$api->UserStatus($userid, 'dungeon')) {
			alert("danger", "Uh Oh!", "Why would you want to use a Negative Begone if you're not in the dungeon?", true, 'index.php');
			die($h->endpage());
		}
		$TR=TimeUntil_Parse(time());
		$api->UserTakeItem($userid, 206, 1);
		$db->query("UPDATE `dungeon` SET `dungeon_out` = 0 WHERE `dungeon_user` = {$userid}");
        $db->query("UPDATE `infirmary` SET `infirmary_out` = 0 WHERE `infirmary_user` = {$userid}");
		$api->SystemLogsAdd($userid, 'itemuse', "Used Negative Begone.");
		alert('success', "Success!", "Negative Begone was used successfully. You have {$TR} remaining.", true, 'index.php');
		$h->endpage();
	}
} 
elseif (isset($_GET['infirmary'])) 
{
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
		$TR=TimeUntil_Parse($InfirmaryOut-(10*60));
		$api->UserTakeItem($userid, 5, 1, '');
		$api->UserStatusSet($userid, 'infirmary', -10, 'Placeholder');
		$api->SystemLogsAdd($userid, 'itemuse', "Used Leech.");
		alert('success', "Success!", "Leech was used successfully. You have {$TR} remaining.", true, 'index.php');
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
		$TR=TimeUntil_Parse($InfirmaryOut-(30*60));
		$api->UserTakeItem($userid, 6, 1);
		$api->UserStatusSet($userid, 'infirmary', -30, '');
		$api->SystemLogsAdd($userid, 'itemuse', "Used Linen Wrap.");
		alert('success', "Success!", "Linen Wrap was used successfully. You have {$TR} remaining.", true, 'index.php');
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
		$TR=TimeUntil_Parse($InfirmaryOut-(75*60));
		$api->UserTakeItem($userid, 100, 1);
        $api->UserInfoSet($userid,'hp',5,true);
		$api->UserStatusSet($userid, 'infirmary', -75, '');
		$api->SystemLogsAdd($userid, 'itemuse', "Used Acupuncture Needle.");
		alert('success', "Success!", "Acupuncture Needle was used successfully. You have {$TR} remaining.", true, 'index.php');
		$h->endpage();
	}
    if ($ir['iitem'] == 4)
	{
		if (!$api->UserHasItem($userid, 98, 1)) {
			alert("danger", "Uh Oh!", "You need at least one Med-go-bye to use this quick link.", true, 'index.php');
			die($h->endpage());
		}
		if (!$api->UserStatus($userid, 'infirmary')) {
			alert("danger", "Uh Oh!", "Why would you want to use a Med-go-bye if you're not in the infirmary?", true, 'index.php');
			die($h->endpage());
		}
		$TR=TimeUntil_Parse($InfirmaryOut-$InfirmaryOut);
		$api->UserTakeItem($userid, 98, 1);
        $api->UserInfoSet($userid,'hp',100,true);
		$api->UserStatusSet($userid, 'infirmary', -75, '');
		$api->SystemLogsAdd($userid, 'itemuse', "Used Med-go-bye.");
		alert('success', "Success!", "Med-go-bye was used successfully. You have {$TR} remaining.", true, 'index.php');
		$h->endpage();
	}
    if ($ir['iitem'] == 5)
	{
		if (!$api->UserHasItem($userid, 207, 1)) {
			alert("danger", "Uh Oh!", "You need at least one Priority Voucher to use this quick link.", true, 'index.php');
			die($h->endpage());
		}
		if (!$api->UserStatus($userid, 'infirmary')) {
			alert("danger", "Uh Oh!", "Why would you want to use a Priority Voucher if you're not in the infirmary?", true, 'index.php');
			die($h->endpage());
		}
        $inc = round((($EndTime - $Time) / 100 * 50) / 60);
		$TR=TimeUntil_Parse($InfirmaryOut-$inc);
		$api->UserTakeItem($userid, 207, 1);
		$api->UserStatusSet($userid, 'infirmary', -75, '');
		$api->SystemLogsAdd($userid, 'itemuse', "Used Priority Voucher");
		alert('success', "Success!", "Priority Voucher was used successfully. You have {$TR} remaining.", true, 'index.php');
		$h->endpage();
	}
    if ($ir['iitem'] == 6)
	{
		if (!$api->UserHasItem($userid, 206, 1)) {
			alert("danger", "Uh Oh!", "You need at least one Negative Begone to use this quick link.", true, 'index.php');
			die($h->endpage());
		}
		if (!$api->UserStatus($userid, 'infirmary')) {
			alert("danger", "Uh Oh!", "Why would you want to use a Negative Begone if you're not in the infirmary?", true, 'index.php');
			die($h->endpage());
		}
		$TR=TimeUntil_Parse($DungeonOut-$DungeonOut);
		$api->UserTakeItem($userid, 206, 1);
        $db->query("UPDATE `dungeon` SET `dungeon_out` = 0 WHERE `dungeon_user` = {$userid}");
        $db->query("UPDATE `infirmary` SET `infirmary_out` = 0 WHERE `infirmary_user` = {$userid}");
		$api->SystemLogsAdd($userid, 'itemuse', "Used Negative Begone.");
		alert('success', "Success!", "Negative Begone was used successfully. You have {$TR} remaining.", true, 'index.php');
		$h->endpage();
	}
	if ($ir['iitem'] == 7)
	{
		if (!$api->UserHasItem($userid, 216, 1)) {
			alert("danger", "Uh Oh!", "You need at least one Medical Package to use this quick link.", true, 'index.php');
			die($h->endpage());
		}
		if (!$api->UserStatus($userid, 'infirmary')) {
			alert("danger", "Uh Oh!", "Why would you want to use a Acupuncture Needle if you're not in the infirmary?", true, 'index.php');
			die($h->endpage());
		}
		$TR=TimeUntil_Parse($InfirmaryOut-(200*60));
		$api->UserTakeItem($userid, 216, 1);
        $api->UserInfoSet($userid,'hp',50,true);
		$api->UserStatusSet($userid, 'infirmary', -200, '');
		$api->SystemLogsAdd($userid, 'itemuse', "Used Medical Package.");
		alert('success', "Success!", "Medical Package was used successfully. You have {$TR} remaining.", true, 'index.php');
		$h->endpage();
	}
} else {
    //Nope
    alert('danger',"Uh Oh!","No action specified.",true,'index.php');
    $h->endpage();
}