<?php
/*
	File:		gamerules2.php
	Created: 	4/5/2016 at 12:03AM Eastern Time
	Info: 		Shows the game rules to users not logged in.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals_nonauth.php');
//List all the game rules.
echo "<h3>{$set['WebsiteName']} Game Rules</h3>
<hr />
You are expected to follow these rules. You are also expected to check back on these fairly frequently as these rules
may change without notice. Staff will not accept ignorance as an excuse if you break one of these rules.<hr />";
$q = $db->query("SELECT * FROM `gamerules` ORDER BY `rule_id` ASC");
echo "<ol>";
while ($r = $db->fetch_row($q)) {
    echo "<li>{$r['rule_text']}</li><hr />";
}
echo "</ol>";
$h->endpage();