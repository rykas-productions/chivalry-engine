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
    		<a href='profile.php?user={$r['fed_userid']}'>{$api->SystemUserIDtoName($r['fed_userid'])}</a>
    	</td>
    	<td>
			" . TimeUntil_Parse($r['fed_out']) . "
		</td>
    	<td>
			{$r['fed_reason']}
		</td>
    	<td>
    		<a href='profile.php?user={$r['fed_jailedby']}'>{$api->SystemUserIDtoName($r['fed_jailedby'])}</a>
    	</td>
    </tr>";
}
echo "</table>";
$db->free_result($q);
$h->endpage();