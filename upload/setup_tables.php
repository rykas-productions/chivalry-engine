<?php
// Direct table setup - run this file in browser
require_once "config.php";

// Direct MySQL connection
$conn = new mysqli(constant("db_host"), constant("db_username"), constant("db_password"), constant("db_database"));

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Setting up Event System Tables</h2>";

// Create user_meta table
$sql1 = "CREATE TABLE IF NOT EXISTS `user_meta` (
    `meta_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `userid` INT(11) UNSIGNED NOT NULL,
    `meta_key` VARCHAR(100) NOT NULL,
    `meta_value` TEXT,
    `updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`meta_id`),
    UNIQUE KEY `user_key` (`userid`, `meta_key`),
    KEY `userid` (`userid`),
    KEY `meta_key` (`meta_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql1) === TRUE) {
    echo "✓ Table 'user_meta' created successfully<br>";
} else {
    echo "✗ Error creating user_meta table: " . $conn->error . "<br>";
}

// Create event_queue table
$sql2 = "CREATE TABLE IF NOT EXISTS `event_queue` (
    `event_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `event_type` VARCHAR(50) NOT NULL,
    `target_user` INT(11) UNSIGNED NOT NULL,
    `event_data` TEXT,
    `process_time` INT(11) UNSIGNED NOT NULL,
    `processed` TINYINT(1) DEFAULT 0,
    `created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`event_id`),
    KEY `target_user` (`target_user`),
    KEY `process_time` (`process_time`),
    KEY `processed` (`processed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql2) === TRUE) {
    echo "✓ Table 'event_queue' created successfully<br>";
} else {
    echo "✗ Error creating event_queue table: " . $conn->error . "<br>";
}

// Create event_log table
$sql3 = "CREATE TABLE IF NOT EXISTS `event_log` (
    `log_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `userid` INT(11) UNSIGNED NOT NULL,
    `event_type` VARCHAR(50) NOT NULL,
    `event_result` TEXT,
    `process_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`log_id`),
    KEY `userid` (`userid`),
    KEY `event_type` (`event_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql3) === TRUE) {
    echo "✓ Table 'event_log' created successfully<br>";
} else {
    echo "✗ Error creating event_log table: " . $conn->error . "<br>";
}

// Add indexes if they don't exist (ignore errors if they already exist)
$indexes = [
    "ALTER TABLE `users` ADD INDEX `laston_idx` (`laston`)",
    "ALTER TABLE `dungeon` ADD INDEX `dungeon_out_idx` (`dungeon_out`)", 
    "ALTER TABLE `infirmary` ADD INDEX `infirmary_out_idx` (`infirmary_out`)"
];

foreach ($indexes as $index) {
    $conn->query($index);
}
echo "✓ Indexes added (if not already present)<br>";

// Check if last_cron_run setting exists
$check = $conn->query("SELECT * FROM settings WHERE setting_name = 'last_cron_run'");
if ($check && $check->num_rows == 0) {
    $conn->query("INSERT INTO settings (setting_name, setting_value) VALUES ('last_cron_run', '0')");
    echo "✓ Added last_cron_run setting<br>";
}

// Check if tables exist for smelting (might not exist)
$check_smelt = $conn->query("SHOW TABLES LIKE 'smelt_inprogress'");
if ($check_smelt && $check_smelt->num_rows > 0) {
    $conn->query("ALTER TABLE `smelt_inprogress` ADD INDEX IF NOT EXISTS `sip_time_idx` (`sip_time`)");
    echo "✓ Smelting table index added<br>";
}

$conn->close();

echo "<br><h3>✓ Setup Complete!</h3>";
echo "<p>The event system is now ready to use.</p>";
echo "<p><a href='index.php'>Continue to game →</a></p>";
?>