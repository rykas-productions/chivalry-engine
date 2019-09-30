<?php
require('globals.php');
if ($userid != 1)
{
	echo "No.";
}
else
{
	if ($_GET['code'] == 'email')
	{
		$q = $db->query("/*qc=on*/SELECT `u`.`userid`,`user_level`,`email`,`username`,`level` 
							FROM `users` AS `u` 
							INNER JOIN `user_settings` AS `uas`
							ON `u`.`userid`=`uas`.`userid`
							WHERE `uas`.`email_optin` = 1 AND `u`.`user_level` != 'NPC'");
		while ($r=$db->fetch_row($q))
		{
			echo "{$r['email']}, ";
		}
		foreach ($ir as $key => $value)
		{
			echo "<br /><b>{$key}:</b> {$value}.";
		}
	}
	if ($_GET['code'] == 'voter')
	{
		$q=$db->query("SELECT * FROM `vote_raffle` WHERE `userid` != 1 ORDER BY RAND() LIMIT 1");
		$r=$db->fetch_row($q);
		echo "Winner of the Month: {$r['userid']}!! They win 25 CID Gym Scrolls.";
	}
	if ($_GET['code'] == 'delvote')
	{
		$db->query("TRUNCATE TABLE `vote_raffle`");
		echo "Votes have been reset.";
	}
}
$h->endpage();