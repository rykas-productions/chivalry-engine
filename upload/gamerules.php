<?php
require('globals.php');
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