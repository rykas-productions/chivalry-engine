<?php
/*
	File:		gamerules2.php
	Created: 	4/5/2016 at 12:03AM Eastern Time
	Info: 		Shows the game rules to users not logged in.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals_nonauth.php');
			echo "<h3>{$set['WebsiteName']} {$lang['GAMERULES_TITLE']}</h3>
			<hr />
			{$lang['GAMERULES_TEXT']}<hr />";
			$q=$db->query("SELECT * FROM `gamerules` ORDER BY `rule_id` ASC");
			echo "<ol>";
			while ($r = $db->fetch_row($q))
			{
				echo "<li>{$r['rule_text']}</li><hr />";
			}
			echo"</ol>";
$h->endpage();