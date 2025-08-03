<?php
/*
    File: cron_scheduler.php
    Created: Separate cron runner to avoid running on every page load
    Info: Run this via actual cron job or AJAX
*/

// This file should be called by:
// 1. A real cron job (every 5 minutes): */5 * * * * php /path/to/cron_scheduler.php
// 2. An AJAX call from JavaScript periodically
// 3. Manual trigger from admin panel

require_once "config.php";
require_once "class/class_db_" . constant("db_driver") . ".php";

$db = new database;
$db->configure(constant("db_host"), constant("db_username"), constant("db_password"), constant("db_database"), 0);
$db->connect();

$time = time();

// Check if we should run crons (don't run too frequently)
$last_run = $db->fetch_single($db->query("SELECT setting_value FROM settings WHERE setting_name = 'last_cron_run'"));
if ($time - $last_run < 60) { // Don't run more than once per minute
    exit("Cron already ran recently");
}

// Update last run time
$db->query("UPDATE settings SET setting_value = {$time} WHERE setting_name = 'last_cron_run'");

// Run the cron files
foreach (glob("crons/*.php") as $filename) {
    include $filename;
}

// Check smelting
$get = $db->query("SELECT `sip_recipe`,`sip_user` FROM `smelt_inprogress` WHERE `sip_time` < {$time}");
if ($db->num_rows($get)) {
    require_once "class/class_api.php";
    $api = new api;
    $api->user = new user;
    
    while ($r = $db->fetch_row($get)) {
        $r2 = $db->fetch_row($db->query("SELECT * FROM `smelt_recipes` WHERE `smelt_id` = {$r['sip_recipe']}"));
        $api->user->giveItem($r['sip_user'], $r2['smelt_output'], $r2['smelt_qty_output']);
        $api->user->addNotification($r['sip_user'], "You have successfully smelted your {$r2['smelt_qty_output']} " . $api->SystemItemIDtoName($r2['smelt_output']) . "(s).");
    }
    $db->query("DELETE FROM `smelt_inprogress` WHERE `sip_time` < {$time}");
}

echo "Cron jobs completed at " . date('Y-m-d H:i:s');
?>