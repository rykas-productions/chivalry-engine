<?php
require('globals.php');
//Random player showcase
$cutoff = time() - 86400;
$uq=$db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `userid` != 1 AND `laston` > {$cutoff} ORDER BY RAND() LIMIT 1");
$ur=$db->fetch_single($uq);
$db->query("UPDATE `settings` SET `setting_value` = '{$ur}' WHERE `setting_name` = 'random_player_showcase'");
?>