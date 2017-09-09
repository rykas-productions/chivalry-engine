<?php
/*
	File: js//script/outputteam.php
	Created: 4/4/2017 at 7:10PM Eastern Time
	Info: PHP file for outputting info about the selected team
	when registering
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
$menuhide=1;
if (isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD'])) {
    if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
        // Ignore a GET request
        header('HTTP/1.1 400 Bad Request');
        exit;
    }
}
require_once('../../global_func.php');
if (!is_ajax()) {
    header('HTTP/1.1 400 Bad Request');
    exit;
}

require_once('../../globals_nonauth.php');
$class = $_POST['team'];
if ($class == 'Warrior') {
    alert('info', "Warrior Class!", "A warrior starts with more strength and less guard. Throughout their adventures,
	they'll gain strength way quicker than any other stat, and guard much slower than the others.", false);
} elseif ($class == 'Rogue') {
    alert('info', "Rogue Class!", "A rogue starts with more agility and less strength. Throughout their adventures,
	they'll gain agility much quicker than any other stat, and strength much slower than the others.", false);
} elseif ($class == 'Defender') {
    alert('info', "Defender Class!", "A defender starts with more guard and less agility. Throughout their adventures,
	they'll gain guard much quicker than any other stat, and agility much slower than the others.", false);
} else {
    alert('danger', "Uh Oh!", "Please select a class.", false);
}
