<?php
require('globals.php');
$api->UserStatusSet(2,'infirmary',60,'Test');
echo $api->UserInfoGet($userid,'energy',true);