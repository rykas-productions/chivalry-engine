<?php
/*
    File: create_skill_tables.php
    Created: Direct table creation for skill tree system
    Info: Creates the skill tree tables directly
*/
require_once('globals.php');

// Check if user is admin
if ($ir['user_level'] != 'Admin') {
    die("Admin access required!");
}

echo "<h3>Creating Skill Tree System Tables</h3>";

// Create skill_trees table
$sql1 = "CREATE TABLE IF NOT EXISTS `skill_trees` (
    `st_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `st_name` varchar(100) NOT NULL,
    `st_class` enum('warrior','mage','rogue','hybrid') NOT NULL,
    `st_desc` text NOT NULL,
    `st_icon` varchar(50) DEFAULT 'fa-tree',
    `st_max_points` int(11) NOT NULL DEFAULT 50,
    PRIMARY KEY (`st_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$result1 = $db->query($sql1);
echo "Creating skill_trees table... " . ($result1 ? "SUCCESS" : "FAILED: " . $db->error) . "<br>";

// Create skills table
$sql2 = "CREATE TABLE IF NOT EXISTS `skills` (
    `skill_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `skill_tree` int(11) unsigned NOT NULL,
    `skill_name` varchar(100) NOT NULL,
    `skill_desc` text NOT NULL,
    `skill_icon` varchar(50) DEFAULT 'fa-star',
    `skill_type` enum('passive','active','ultimate') NOT NULL DEFAULT 'passive',
    `skill_tier` int(11) NOT NULL DEFAULT 1,
    `skill_max_level` int(11) NOT NULL DEFAULT 5,
    `skill_cost_per_level` int(11) NOT NULL DEFAULT 1,
    `skill_prereq` int(11) unsigned DEFAULT NULL,
    `skill_effect` varchar(50) NOT NULL,
    `skill_value_per_level` decimal(10,2) NOT NULL,
    PRIMARY KEY (`skill_id`),
    KEY `skill_tree` (`skill_tree`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$result2 = $db->query($sql2);
echo "Creating skills table... " . ($result2 ? "SUCCESS" : "FAILED: " . $db->error) . "<br>";

// Create user_skills table
$sql3 = "CREATE TABLE IF NOT EXISTS `user_skills` (
    `us_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `us_user` int(11) unsigned NOT NULL,
    `us_skill` int(11) unsigned NOT NULL,
    `us_level` int(11) NOT NULL DEFAULT 0,
    `us_unlocked_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`us_id`),
    UNIQUE KEY `user_skill` (`us_user`, `us_skill`),
    KEY `us_user` (`us_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$result3 = $db->query($sql3);
echo "Creating user_skills table... " . ($result3 ? "SUCCESS" : "FAILED: " . $db->error) . "<br>";

// Add sample skill trees
echo "<h4>Adding Sample Skill Trees</h4>";

$trees = [
    ['Warrior', 'warrior', 'Master of physical combat and defense'],
    ['Mage', 'mage', 'Wielder of arcane magic and elemental forces'],
    ['Rogue', 'rogue', 'Expert in stealth, speed, and precision']
];

foreach ($trees as $tree) {
    $check = $db->query("SELECT st_id FROM skill_trees WHERE st_name = '{$tree[0]}'");
    if ($db->num_rows($check) == 0) {
        $db->query("INSERT INTO skill_trees (st_name, st_class, st_desc) VALUES ('{$tree[0]}', '{$tree[1]}', '{$tree[2]}')");
        echo "Added {$tree[0]} skill tree<br>";
    } else {
        echo "{$tree[0]} skill tree already exists<br>";
    }
}

// Add sample skills for Warrior tree
echo "<h4>Adding Sample Skills</h4>";

$warrior_skills = [
    ['Blade Master', 'Increases melee damage', 1, 'passive', 'damage_bonus', 5, NULL],
    ['Iron Skin', 'Increases physical defense', 1, 'passive', 'guard_bonus', 3, NULL],
    ['Berserker Rage', 'Increases strength temporarily', 2, 'active', 'strength_bonus', 10, 1],
    ['Shield Wall', 'Massive defense boost', 2, 'active', 'guard_bonus', 20, 2],
    ['Whirlwind', 'Area damage attack', 3, 'ultimate', 'special_attack', 1, 3]
];

// Get warrior tree ID
$warrior_id = $db->fetch_single($db->query("SELECT st_id FROM skill_trees WHERE st_class = 'warrior'"));

if ($warrior_id) {
    foreach ($warrior_skills as $idx => $skill) {
        $check = $db->query("SELECT skill_id FROM skills WHERE skill_name = '{$skill[0]}' AND skill_tree = {$warrior_id}");
        if ($db->num_rows($check) == 0) {
            $prereq = $skill[6] ? $skill[6] : 'NULL';
            $db->query("
                INSERT INTO skills (skill_tree, skill_name, skill_desc, skill_tier, skill_type, skill_effect, skill_value_per_level, skill_prereq)
                VALUES ({$warrior_id}, '{$skill[0]}', '{$skill[1]}', {$skill[2]}, '{$skill[3]}', '{$skill[4]}', {$skill[5]}, {$prereq})
            ");
            echo "Added skill: {$skill[0]}<br>";
        } else {
            echo "Skill {$skill[0]} already exists<br>";
        }
    }
}

// Check if skill_points column exists in users table
$cols = $db->query("SHOW COLUMNS FROM users LIKE 'skill_points'");
if ($db->num_rows($cols) == 0) {
    $db->query("ALTER TABLE users ADD COLUMN skill_points int(11) DEFAULT 5");
    echo "<br>Added skill_points column to users table<br>";
    
    // Give existing users some skill points based on level
    $db->query("UPDATE users SET skill_points = level");
    echo "Granted skill points to all users based on their level<br>";
}

// Check for skill_reset_count column
$cols2 = $db->query("SHOW COLUMNS FROM users LIKE 'skill_reset_count'");
if ($db->num_rows($cols2) == 0) {
    $db->query("ALTER TABLE users ADD COLUMN skill_reset_count int(11) DEFAULT 0");
    echo "Added skill_reset_count column to users table<br>";
}

echo "<br><h4 class='text-success'>Skill Tree System Setup Complete!</h4>";
echo "<a href='skill_tree.php' class='btn btn-primary'>Go to Skill Trees</a>";

$h->endpage();
?>