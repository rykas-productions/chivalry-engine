<?php
/*
	File:		fedjail.php
	Created: 	4/5/2016 at 12:01AM Eastern Time
	Info: 		Lists those placed into the federal jail. Players in
				federal jail cannot interact with the game at all.
				Consider it like an in-game ban.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
echo "<h3>Federal Dungeon</h3>
	This is where you go if you break the game rules. Be smart, follow the rules!";
$q = $db->query("SELECT * FROM `fedjail` ORDER BY `fed_out` ASC");
echo "<table class='table table-bordered'>
	<tr>
		<th>
			Player
		</th>
		<th>
			Remaining Time
		</th>
		<th>
			Reason
		</th>
		<th>
			Jailer
		</th>
	</tr>";
//List all the players in the federal jail.
while ($r = $db->fetch_row($q)) {
    echo "
	<tr>
    	<td>
    		<a href='profile.php?user={$r['fed_userid']}'>{$api->user->getNamefromID($r['fed_userid'])}</a>
    	</td>
    	<td>
			" . TimeUntil_Parse($r['fed_out']) . "
		</td>
    	<td>
			{$r['fed_reason']}
		</td>
    	<td>
    		<a href='profile.php?user={$r['fed_jailedby']}'>{$api->user->getNamefromID($r['fed_jailedby'])}</a>
    	</td>
    </tr>";
}
echo "</table>";
$db->free_result($q);
echo "We have no real good reason to put mail banned players here... but we still did.";
$q = $db->query("SELECT * FROM `mail_bans` ORDER BY `mbTIME` ASC");
echo "<table class='table table-bordered'>
	<tr>
		<th>
			Player
		</th>
		<th>
			Remaining Time
		</th>
		<th>
			Reason
		</th>
		<th>
			Banner
		</th>
	</tr>";
//List all the players who are mail banned
while ($r = $db->fetch_row($q)) {
    echo "
	<tr>
    	<td>
    		<a href='profile.php?user={$r['mbUSER']}'>{$api->user->getNamefromID($r['mbUSER'])}</a>
    	</td>
    	<td>
			" . TimeUntil_Parse($r['mbTIME']) . "
		</td>
    	<td>
			{$r['mbREASON']}
		</td>
    	<td>
    		<a href='profile.php?user={$r['mbBANNER']}'>{$api->user->getNamefromID($r['mbBANNER'])}</a>
    	</td>
    </tr>";
}
echo "</table>";
$db->free_result($q);

echo "The same holds true for forum bans.";
$q = $db->query("SELECT * FROM `forum_bans` ORDER BY `fb_time` ASC");
echo "<table class='table table-bordered'>
	<tr>
		<th>
			Player
		</th>
		<th>
			Remaining Time
		</th>
		<th>
			Reason
		</th>
		<th>
			Banner
		</th>
	</tr>";
//List all the players who are mail banned
while ($r = $db->fetch_row($q)) {
    echo "
	<tr>
    	<td>
    		<a href='profile.php?user={$r['fb_user']}'>{$api->user->getNamefromID($r['fb_user'])}</a>
    	</td>
    	<td>
			" . TimeUntil_Parse($r['fb_time']) . "
		</td>
    	<td>
			{$r['fb_reason']}
		</td>
    	<td>
    		<a href='profile.php?user={$r['fb_banner']}'>{$api->user->getNamefromID($r['fb_banner'])}</a>
    	</td>
    </tr>";
}
echo "</table>";
$db->free_result($q);

$h->endpage();