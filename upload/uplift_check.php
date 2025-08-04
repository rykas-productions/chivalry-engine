<?php
/*
    File: uplift_check.php
    Created: Version upgrade checker and database updater
    Info: Checks for missing features and updates database schema
    Author: Chivalry Engine v3
*/

// Allow this script to run from CLI or web
if (php_sapi_name() === 'cli') {
    // CLI mode - no session needed
    define('MONO_ON', 1);
    require_once('globals_nonauth.php');
} else {
    // Web mode - require authentication
    require_once('globals.php');
    
    // Only admins can run this
    if (!$api->user->getStaffLevel($userid, 'admin')) {
        die("Access denied. Admin only.");
    }
}

class UpliftChecker {
    private $db;
    private $updates_needed = [];
    private $updates_applied = [];
    private $errors = [];
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Main function to check and apply updates
     */
    public function run() {
        echo $this->getHeader();
        
        // Check current version
        $current_version = $this->getCurrentVersion();
        echo $this->formatMessage("info", "Current database version: " . ($current_version ?: "Unknown"));
        
        // Detect actual installed version based on features
        $actual_version = $this->determineVersion();
        
        // If database version doesn't match actual features, update it
        if ($current_version != $actual_version) {
            echo $this->formatMessage("warning", "Database version mismatch. Records show {$current_version} but detected {$actual_version} based on installed features.");
            echo $this->formatMessage("info", "Updating database version to match installed features...");
            $this->updateVersion($actual_version);
            $current_version = $actual_version;
        }
        
        // Only check for updates newer than current version
        if (version_compare($current_version, '3.0.0', '<')) {
            // Check v3.0 features
            $this->checkAchievementSystem();
            $this->checkDailyRewardsSystem();
            $this->checkGuildWarsSystem();
            $this->checkBattleRoyaleSystem();
        }
        
        if (version_compare($current_version, '3.1.0', '<')) {
            // Check v3.1 features
            $this->checkSkillTreeSystem();
            $this->checkPetSystem();
            $this->checkDungeonSystem();
            $this->checkCraftingSystem();
            $this->checkEventsSystem();
            $this->checkLeaderboardsSystem();
            $this->checkWorldBossSystem();
        }
        
        if (version_compare($current_version, '3.2.0', '<')) {
            // Check v3.2 features
            $this->checkV32Features();
        }
        
        if (version_compare($current_version, '3.3.0', '<')) {
            // Check v3.3 features (Weather System)
            $this->checkV33Features();
        }
        
        // Always check these as they might be added to any version
        $this->checkUserColumns();
        $this->checkVIPSystem();
        $this->checkMarriageSystem();
        $this->checkEstateSystem();
        
        // Show available features based on version
        if (version_compare($current_version, '3.3.0', '>=')) {
            // Already at 3.3 or higher
            if (empty($this->updates_needed)) {
                echo $this->formatMessage("success", "✓ Your database is up to date at version {$current_version}!");
                return;
            }
        } elseif (version_compare($current_version, '3.2.0', '>=')) {
            // At 3.2, show v3.3 features
            echo $this->formatMessage("info", "Version 3.3.0 is available with Weather System!");
            
            // Check v3.3 features if not already checked
            if (!$this->tableExists('weather_current')) {
                $this->updates_needed[] = "Create weather system (Dynamic weather affecting all gameplay)";
            }
        } else {
            // Show what v3.2 offers even if no updates were detected
            echo $this->formatMessage("info", "Version 3.2.0 is available with new features!");
            
            // Manually check v3.2 features if they weren't checked
            if (!$this->tableExists('farm_users')) {
                $this->updates_needed[] = "Create farming system (farm_users, farm_fields, farm_crops tables)";
            }
            if (!$this->tableExists('asset_market')) {
                $this->updates_needed[] = "Create stock market system (asset_market tables)";
            }
            if (!$this->columnExists('users', 'last_regen')) {
                $this->updates_needed[] = "Add percentage-based energy regeneration";
            }
        }
        
        // Display what needs updating
        if (empty($this->updates_needed)) {
            echo $this->formatMessage("success", "✓ Your database is up to date!");
            return;
        }
        
        echo $this->formatMessage("warning", "Found " . count($this->updates_needed) . " updates needed:");
        foreach ($this->updates_needed as $update) {
            echo $this->formatMessage("", "  • " . $update);
        }
        
        // Apply updates if confirmed
        if ($this->shouldApplyUpdates()) {
            echo "\n" . $this->formatMessage("info", "Applying updates...\n");
            $this->applyUpdates();
            
            // Show results
            if (!empty($this->updates_applied)) {
                echo $this->formatMessage("success", "Successfully applied " . count($this->updates_applied) . " updates:");
                foreach ($this->updates_applied as $update) {
                    echo $this->formatMessage("", "  ✓ " . $update);
                }
            }
            
            if (!empty($this->errors)) {
                echo $this->formatMessage("error", "Encountered " . count($this->errors) . " errors:");
                foreach ($this->errors as $error) {
                    echo $this->formatMessage("", "  ✗ " . $error);
                }
            }
            
            // Determine appropriate version based on what's installed
            $new_version = $this->determineVersion();
            $this->updateVersion($new_version);
        }
        
        echo $this->getFooter();
    }
    
    /**
     * Check if Achievement System tables exist
     */
    private function checkAchievementSystem() {
        $tables = ['achievements', 'user_achievements', 'achievement_progress'];
        
        foreach ($tables as $table) {
            if (!$this->tableExists($table)) {
                $this->updates_needed[] = "Create table: {$table}";
            }
        }
        
        // Check if sample achievements exist
        if ($this->tableExists('achievements')) {
            $count = $this->db->fetch_single($this->db->query("SELECT COUNT(*) FROM achievements"));
            if ($count == 0) {
                $this->updates_needed[] = "Add sample achievements";
            }
        }
    }
    
    /**
     * Check if Daily Rewards System tables exist
     */
    private function checkDailyRewardsSystem() {
        $tables = ['daily_rewards', 'user_daily_rewards', 'daily_reward_history', 'streak_rewards'];
        
        foreach ($tables as $table) {
            if (!$this->tableExists($table)) {
                $this->updates_needed[] = "Create table: {$table}";
            }
        }
        
        // Check if reward cycle exists
        if ($this->tableExists('daily_rewards')) {
            $count = $this->db->fetch_single($this->db->query("SELECT COUNT(*) FROM daily_rewards"));
            if ($count == 0) {
                $this->updates_needed[] = "Add 30-day reward cycle";
            }
        }
        
        // Check if streak milestones exist
        if ($this->tableExists('streak_rewards')) {
            $count = $this->db->fetch_single($this->db->query("SELECT COUNT(*) FROM streak_rewards"));
            if ($count == 0) {
                $this->updates_needed[] = "Add streak milestone rewards";
            }
        }
    }
    
    /**
     * Check Guild Wars System
     */
    private function checkGuildWarsSystem() {
        $tables = ['guild_territories', 'guild_wars', 'guild_war_participants', 'guild_war_battles'];
        foreach ($tables as $table) {
            if (!$this->tableExists($table)) {
                $this->updates_needed[] = "Create table: {$table}";
            }
        }
    }
    
    /**
     * Check Battle Royale System
     */
    private function checkBattleRoyaleSystem() {
        $tables = ['battle_royale_events', 'battle_royale_participants', 'battle_royale_battles', 'battle_royale_loot'];
        foreach ($tables as $table) {
            if (!$this->tableExists($table)) {
                $this->updates_needed[] = "Create table: {$table}";
            }
        }
    }
    
    /**
     * Check Skill Tree System
     */
    private function checkSkillTreeSystem() {
        $tables = ['skill_trees', 'skills', 'user_skills'];
        $missing = [];
        foreach ($tables as $table) {
            if (!$this->tableExists($table)) {
                $this->updates_needed[] = "Create table: {$table}";
                $missing[] = $table;
            }
        }
        
        // If tables are missing, create them directly
        if (in_array('skill_trees', $missing)) {
            $this->createSkillTreesTables();
        }
        
        // Check if existing players need skill points
        $this->grantRetroactiveSkillPoints();
    }
    
    /**
     * Create skill tree tables directly
     */
    private function createSkillTreesTables() {
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
        
        if ($this->db->query($sql1)) {
            $this->updates_applied[] = "✓ Created table: skill_trees";
        }
        
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
        
        if ($this->db->query($sql2)) {
            $this->updates_applied[] = "✓ Created table: skills";
        }
        
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
        
        if ($this->db->query($sql3)) {
            $this->updates_applied[] = "✓ Created table: user_skills";
        }
    }
    
    /**
     * Grant retroactive skill points to existing players
     */
    private function grantRetroactiveSkillPoints() {
        // Check if we've already granted skill points
        $check = $this->db->query("SELECT userid FROM users WHERE skill_points > 0 LIMIT 1");
        if ($this->db->num_rows($check) > 0) {
            // Some users already have skill points, skip
            return;
        }
        
        // Grant skill points based on level (1 point per level)
        $result = $this->db->query("
            UPDATE users 
            SET skill_points = level 
            WHERE skill_points = 0 OR skill_points IS NULL
        ");
        
        $affected = $this->db->affected_rows();
        if ($affected > 0) {
            $this->updates_applied[] = "✓ Granted skill points to {$affected} existing players (1 per level)";
            $this->updates_needed[] = "Grant retroactive skill points";
        }
        
        // Give bonus skill points to high level players
        $this->db->query("
            UPDATE users 
            SET skill_points = skill_points + 5 
            WHERE level >= 50
        ");
        
        $this->db->query("
            UPDATE users 
            SET skill_points = skill_points + 10 
            WHERE level >= 100
        ");
        
        // Notify about the update
        $this->db->query("
            INSERT IGNORE INTO announcements (ann_text, ann_time, ann_poster)
            VALUES ('Skill Tree System is now available! You have been granted skill points based on your level. Visit the Skill Trees page to allocate your points!', 
                    UNIX_TIMESTAMP(), 'System')
        ");
    }
    
    /**
     * Check Pet System
     */
    private function checkPetSystem() {
        $tables = ['pets', 'user_pets', 'pet_battles'];
        foreach ($tables as $table) {
            if (!$this->tableExists($table)) {
                $this->updates_needed[] = "Create table: {$table}";
            }
        }
    }
    
    /**
     * Check Dungeon System
     */
    private function checkDungeonSystem() {
        $tables = ['dungeons', 'dungeon_bosses', 'dungeon_loot', 'dungeon_runs'];
        foreach ($tables as $table) {
            if (!$this->tableExists($table)) {
                $this->updates_needed[] = "Create table: {$table}";
            }
        }
    }
    
    /**
     * Check Crafting System
     */
    private function checkCraftingSystem() {
        $tables = ['crafting_recipes', 'recipe_materials', 'user_crafting', 'enchantments'];
        foreach ($tables as $table) {
            if (!$this->tableExists($table)) {
                $this->updates_needed[] = "Create table: {$table}";
            }
        }
    }
    
    /**
     * Check Events System
     */
    private function checkEventsSystem() {
        $tables = ['events', 'event_participation'];
        foreach ($tables as $table) {
            if (!$this->tableExists($table)) {
                $this->updates_needed[] = "Create table: {$table}";
            }
        }
    }
    
    /**
     * Check Leaderboards System
     */
    private function checkLeaderboardsSystem() {
        $tables = ['seasons', 'leaderboards'];
        foreach ($tables as $table) {
            if (!$this->tableExists($table)) {
                $this->updates_needed[] = "Create table: {$table}";
            }
        }
    }
    
    /**
     * Check World Boss System
     */
    private function checkWorldBossSystem() {
        $tables = ['world_bosses', 'world_boss_damage', 'world_boss_rewards'];
        foreach ($tables as $table) {
            if (!$this->tableExists($table)) {
                $this->updates_needed[] = "Create table: {$table}";
            }
        }
    }
    
    /**
     * Check for new user columns
     */
    private function checkUserColumns() {
        $columns = [
            'achievement_points' => 'int(11) DEFAULT 0',
            'achievements_earned' => 'int(11) DEFAULT 0',
            'guild_wars_participated' => 'int(11) DEFAULT 0',
            'guild_war_kills' => 'int(11) DEFAULT 0',
            'guild_war_score' => 'int(11) DEFAULT 0',
            'skill_points' => 'int(11) DEFAULT 0',
            'skill_reset_count' => 'int(11) DEFAULT 0',
            'active_pet' => 'int(11) unsigned DEFAULT NULL',
            'dungeons_completed' => 'int(11) DEFAULT 0',
            'raids_completed' => 'int(11) DEFAULT 0',
            'crafting_level' => 'int(11) DEFAULT 1',
            'season_points' => 'int(11) DEFAULT 0',
            'battle_royale_wins' => 'int(11) DEFAULT 0'
        ];
        
        foreach ($columns as $column => $definition) {
            if (!$this->columnExists('users', $column)) {
                $this->updates_needed[] = "Add column: users.{$column}";
            }
        }
        
        // Check guild columns if guild table exists
        if ($this->tableExists('guild')) {
            $guild_columns = [
                'guild_war_rating' => 'int(11) DEFAULT 1000',
                'guild_territories_owned' => 'int(11) DEFAULT 0',
                'guild_wars_won' => 'int(11) DEFAULT 0',
                'guild_wars_lost' => 'int(11) DEFAULT 0',
                'guild_war_points' => 'int(11) DEFAULT 0',
                'guild_treasury' => 'bigint(20) DEFAULT 0'
            ];
            
            foreach ($guild_columns as $column => $definition) {
                if (!$this->columnExists('guild', $column)) {
                    $this->updates_needed[] = "Add column: guild.{$column}";
                }
            }
        }
    }
    
    /**
     * Check VIP system
     */
    private function checkVIPSystem() {
        // VIP system already exists with tables: vip_listing, vip_market, vips_accepted
        // No need to check for vip_packages as the system uses different tables
    }
    
    /**
     * Check Marriage system
     */
    private function checkMarriageSystem() {
        // Marriage system already exists with tables: marriages, marriage_proposals, marriage_gifts
        // No need for additional checks
    }
    
    /**
     * Check Estate system
     */
    private function checkEstateSystem() {
        if ($this->tableExists('estates')) {
            $count = $this->db->fetch_single($this->db->query("SELECT COUNT(*) FROM estates"));
            if ($count == 0) {
                $this->updates_needed[] = "Add estate data";
            }
        } else {
            $this->updates_needed[] = "Create estates table";
        }
    }
    
    /**
     * Apply all needed updates
     */
    private function applyUpdates() {
        // Add user columns first
        $user_columns = [
            'achievement_points' => 'int(11) DEFAULT 0',
            'achievements_earned' => 'int(11) DEFAULT 0',
            'guild_wars_participated' => 'int(11) DEFAULT 0',
            'guild_war_kills' => 'int(11) DEFAULT 0',
            'guild_war_score' => 'int(11) DEFAULT 0',
            'skill_points' => 'int(11) DEFAULT 0',
            'skill_reset_count' => 'int(11) DEFAULT 0',
            'active_pet' => 'int(11) unsigned DEFAULT NULL',
            'dungeons_completed' => 'int(11) DEFAULT 0',
            'raids_completed' => 'int(11) DEFAULT 0',
            'crafting_level' => 'int(11) DEFAULT 1',
            'season_points' => 'int(11) DEFAULT 0',
            'battle_royale_wins' => 'int(11) DEFAULT 0'
        ];
        
        foreach ($user_columns as $column => $definition) {
            if (in_array("Add column: users.{$column}", $this->updates_needed)) {
                $this->addTableColumn('users', $column, $definition);
            }
        }
        
        // Add v3.2 columns
        if (in_array("Add column: users.last_regen", $this->updates_needed) || 
            in_array("Add percentage-based energy regeneration", $this->updates_needed)) {
            $this->addTableColumn('users', 'last_regen', 'int(11) DEFAULT 0');
        }
        
        // Create v3.2 tables if needed
        if (in_array("Create farming system (farm_users, farm_fields, farm_crops tables)", $this->updates_needed)) {
            $this->createFarmingTables();
        }
        
        if (in_array("Create stock market system (asset_market tables)", $this->updates_needed)) {
            $this->createStockMarketTables();
        }
        
        // Create v3.3 weather system tables
        if (in_array("Create table: weather_current", $this->updates_needed) ||
            in_array("Create table: weather_history", $this->updates_needed) ||
            in_array("Create table: weather_forecasts", $this->updates_needed)) {
            $this->createWeatherTables();
        }
        
        // Add guild columns if needed
        if ($this->tableExists('guild')) {
            $guild_columns = [
                'guild_war_rating' => 'int(11) DEFAULT 1000',
                'guild_territories_owned' => 'int(11) DEFAULT 0',
                'guild_wars_won' => 'int(11) DEFAULT 0',
                'guild_wars_lost' => 'int(11) DEFAULT 0',
                'guild_war_points' => 'int(11) DEFAULT 0',
                'guild_treasury' => 'bigint(20) DEFAULT 0'
            ];
            
            foreach ($guild_columns as $column => $definition) {
                if (in_array("Add column: guild.{$column}", $this->updates_needed)) {
                    $this->addTableColumn('guild', $column, $definition);
                }
            }
        }
        
        // Load SQL files in order
        $sql_files = [
            'wow_features.sql',           // Achievements and Daily Rewards
            'guild_wars.sql',             // Guild Wars system
            'battle_royale.sql',          // Battle Royale system
            'wow_features_complete.sql',  // All other systems
            'world_boss_tables.sql'       // World Boss system
        ];
        
        foreach ($sql_files as $file) {
            $sql_file = __DIR__ . '/' . $file;
            if (file_exists($sql_file)) {
                $this->executeSQLFile($sql_file);
            }
        }
        
        // Add VIP packages if needed
        if (in_array("Add VIP packages", $this->updates_needed)) {
            $this->addVIPPackages();
        }
        
        // Add estate data if needed
        if (in_array("Add estate data", $this->updates_needed)) {
            $this->addEstateData();
        }
    }
    
    /**
     * Add column to any table
     */
    private function addTableColumn($table, $column, $definition) {
        try {
            $this->db->query("ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition}");
            $this->updates_applied[] = "Added column: {$table}.{$column}";
        } catch (Exception $e) {
            if (stripos($e->getMessage(), 'Duplicate column') === false) {
                $this->errors[] = "Failed to add column {$table}.{$column}: " . $e->getMessage();
            }
        }
    }
    
    /**
     * Add column to users table (backward compatibility)
     */
    private function addUserColumn($column, $definition) {
        $this->addTableColumn('users', $column, $definition);
    }
    
    /**
     * Execute SQL file
     */
    private function executeSQLFile($file) {
        $this->updates_applied[] = "Processing SQL file: " . basename($file);
        
        $sql = file_get_contents($file);
        
        // Remove SQL comments but preserve section markers
        $sql = preg_replace('/^--(?!.*======).*$/m', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        
        // Split by semicolon followed by newline or end of string
        // This better handles multi-line statements
        $queries = preg_split('/;\s*$/m', $sql);
        
        $query_count = 0;
        $error_count = 0;
        
        foreach ($queries as $query) {
            $query = trim($query);
            if (empty($query)) continue;
            
            // Skip comment-only lines and section markers
            if (strpos($query, '--') === 0) continue;
            
            try {
                // Add semicolon back if needed
                if (substr($query, -1) !== ';') {
                    $query .= ';';
                }
                
                // Log what we're trying to do
                if (stripos($query, 'CREATE TABLE') !== false) {
                    preg_match('/CREATE TABLE\s+(?:IF NOT EXISTS\s+)?`?([^`\s]+)`?/i', $query, $matches);
                    if (isset($matches[1])) {
                        $table_name = $matches[1];
                        
                        // Check if table already exists
                        $exists = $this->tableExists($table_name);
                        if ($exists) {
                            $this->updates_applied[] = "Table already exists: {$table_name}";
                            continue;
                        }
                    }
                }
                
                $result = $this->db->query($query);
                if (!$result) {
                    $error_count++;
                    // Get the actual error
                    $error_msg = mysqli_error($this->db->connection);
                    
                    // Only log non-duplicate errors
                    if (strpos($error_msg, 'already exists') === false && 
                        strpos($error_msg, 'Duplicate') === false) {
                        $this->updates_applied[] = "Error: " . substr($query, 0, 50) . "... - " . $error_msg;
                    }
                    continue;
                } else {
                    $query_count++;
                    
                    // Determine what was done
                    if (stripos($query, 'CREATE TABLE') !== false) {
                        preg_match('/CREATE TABLE.*?`([^`]+)`/i', $query, $matches);
                        if (isset($matches[1])) {
                            $this->updates_applied[] = "✓ Created table: " . $matches[1];
                        }
                    } elseif (stripos($query, 'ALTER TABLE') !== false) {
                        preg_match('/ALTER TABLE.*?`([^`]+)`.*?ADD.*?`([^`]+)`/i', $query, $matches);
                        if (isset($matches[1]) && isset($matches[2])) {
                            $this->updates_applied[] = "✓ Added column: " . $matches[1] . "." . $matches[2];
                        }
                    } elseif (stripos($query, 'INSERT') !== false) {
                        // Don't log every insert
                        continue;
                    }
                }
            } catch (Exception $e) {
                // Skip errors for already exists
                if (stripos($e->getMessage(), 'already exists') === false && 
                    stripos($e->getMessage(), 'Duplicate entry') === false) {
                    $this->errors[] = "SQL Error: " . $e->getMessage();
                }
            }
        }
        
        $this->updates_applied[] = "Processed {$query_count} queries from " . basename($file);
        if ($error_count > 0) {
            $this->updates_applied[] = "Skipped {$error_count} queries (tables/data may already exist)";
        }
    }
    
    /**
     * Add VIP packages
     */
    private function addVIPPackages() {
        try {
            // Create table if not exists
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `vip_packages` (
                    `vp_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `vp_name` varchar(100) NOT NULL,
                    `vp_days` int(11) NOT NULL,
                    `vp_cost` int(11) NOT NULL,
                    `vp_cost_type` enum('primary','secondary') DEFAULT 'secondary',
                    PRIMARY KEY (`vp_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            
            // Add sample packages
            $this->db->query("
                INSERT IGNORE INTO `vip_packages` (`vp_name`, `vp_days`, `vp_cost`, `vp_cost_type`) VALUES
                ('7 Day VIP', 7, 10, 'secondary'),
                ('30 Day VIP', 30, 35, 'secondary'),
                ('90 Day VIP', 90, 90, 'secondary'),
                ('365 Day VIP', 365, 300, 'secondary')
            ");
            
            $this->updates_applied[] = "Added VIP packages";
        } catch (Exception $e) {
            $this->errors[] = "Failed to add VIP packages: " . $e->getMessage();
        }
    }
    
    /**
     * Add estate data
     */
    private function addEstateData() {
        try {
            // Add sample estates if table exists
            if ($this->tableExists('estates')) {
                $this->db->query("
                    INSERT IGNORE INTO `estates` (`estateID`, `estateName`, `estateLevel`, `estateWill`, `estateMaxWill`) VALUES
                    (1, 'Cardboard Box', 1, 100, 100),
                    (2, 'Tent', 5, 150, 150),
                    (3, 'Trailer', 10, 200, 200),
                    (4, 'Small Apartment', 20, 300, 300),
                    (5, 'Large Apartment', 40, 500, 500),
                    (6, 'Small House', 60, 750, 750),
                    (7, 'Medium House', 80, 1000, 1000),
                    (8, 'Large House', 100, 1500, 1500),
                    (9, 'Small Mansion', 150, 2000, 2000),
                    (10, 'Large Mansion', 200, 3000, 3000)
                ");
                
                $this->updates_applied[] = "Added estate data";
            }
        } catch (Exception $e) {
            $this->errors[] = "Failed to add estate data: " . $e->getMessage();
        }
    }
    
    /**
     * Check if table exists
     */
    private function tableExists($table) {
        $result = $this->db->query("SHOW TABLES LIKE '{$table}'");
        return $this->db->num_rows($result) > 0;
    }
    
    /**
     * Check if column exists
     */
    private function columnExists($table, $column) {
        $result = $this->db->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
        return $this->db->num_rows($result) > 0;
    }
    
    /**
     * Get current database version
     */
    private function getCurrentVersion() {
        if ($this->tableExists('settings')) {
            $version = $this->db->fetch_single($this->db->query("SELECT setting_value FROM settings WHERE setting_name = 'db_version' LIMIT 1"));
            return $version ?: null;
        }
        return null;
    }
    
    /**
     * Determine the appropriate version based on installed features
     */
    private function determineVersion() {
        // Check what features are actually installed
        $has_v30 = false;
        $has_v31 = false;
        $has_v32 = false;
        
        // v3.0 features
        if ($this->tableExists('achievements') && $this->tableExists('daily_rewards') && 
            $this->tableExists('guild_territories') && $this->tableExists('battle_royale_events')) {
            $has_v30 = true;
        }
        
        // v3.1 features
        if ($this->tableExists('skill_trees') && $this->tableExists('world_bosses') && 
            $this->tableExists('pets') && $this->tableExists('dungeons')) {
            $has_v31 = true;
        }
        
        // v3.2 features
        if ($this->tableExists('farm_users') && $this->tableExists('asset_market')) {
            $has_v32 = true;
        }
        
        // v3.3 features
        $has_v33 = false;
        if ($this->tableExists('weather_current') && $this->tableExists('weather_history') && 
            $this->tableExists('weather_forecasts')) {
            $has_v33 = true;
        }
        
        // Return highest version installed
        if ($has_v33) {
            return '3.3.0';
        } elseif ($has_v32) {
            return '3.2.0';
        } elseif ($has_v31) {
            return '3.1.0';
        } elseif ($has_v30) {
            return '3.0.0';
        } else {
            return '2.0.0'; // Base version
        }
    }
    
    /**
     * Check v3.2 features
     */
    private function checkV32Features() {
        // Check if last_regen column exists for percentage-based regeneration
        if (!$this->columnExists('users', 'last_regen')) {
            $this->updates_needed[] = "Add column: users.last_regen";
        }
        
        // Check if farming tables exist
        if (!$this->tableExists('farm_users')) {
            $this->updates_needed[] = "Create table: farm_users";
        }
        if (!$this->tableExists('farm_fields')) {
            $this->updates_needed[] = "Create table: farm_fields";
        }
        if (!$this->tableExists('farm_crops')) {
            $this->updates_needed[] = "Create table: farm_crops";
        }
        
        // Check if stock market tables exist
        if (!$this->tableExists('asset_market')) {
            $this->updates_needed[] = "Create table: asset_market";
        }
        if (!$this->tableExists('asset_market_owned')) {
            $this->updates_needed[] = "Create table: asset_market_owned";
        }
        if (!$this->tableExists('asset_market_history')) {
            $this->updates_needed[] = "Create table: asset_market_history";
        }
        if (!$this->tableExists('asset_market_profit')) {
            $this->updates_needed[] = "Create table: asset_market_profit";
        }
    }
    
    /**
     * Check v3.3 features (Weather System)
     */
    private function checkV33Features() {
        // Check weather system tables
        if (!$this->tableExists('weather_current')) {
            $this->updates_needed[] = "Create table: weather_current";
        }
        if (!$this->tableExists('weather_history')) {
            $this->updates_needed[] = "Create table: weather_history";
        }
        if (!$this->tableExists('weather_forecasts')) {
            $this->updates_needed[] = "Create table: weather_forecasts";
        }
    }
    
    /**
     * Create farming system tables
     */
    private function createFarmingTables() {
        // Create farm_users table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `farm_users` (
                `userid` int(11) unsigned NOT NULL,
                `farm_level` int(11) NOT NULL DEFAULT 1,
                `farm_xp` int(11) NOT NULL DEFAULT 0,
                `xp_needed` int(11) NOT NULL DEFAULT 100,
                `farm_water_available` int(11) NOT NULL DEFAULT 0,
                `farm_water_max` int(11) NOT NULL DEFAULT 0,
                PRIMARY KEY (`userid`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $this->updates_applied[] = "Created table: farm_users";
        
        // Create farm_fields table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `farm_fields` (
                `field_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `userid` int(11) unsigned NOT NULL,
                `crop_id` int(11) unsigned DEFAULT 0,
                `field_status` enum('empty','growing','dead') DEFAULT 'empty',
                `planted_at` int(11) DEFAULT 0,
                `health` int(11) DEFAULT 100,
                `last_tended` int(11) DEFAULT 0,
                PRIMARY KEY (`field_id`),
                KEY `userid` (`userid`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $this->updates_applied[] = "Created table: farm_fields";
        
        // Create farm_crops table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `farm_crops` (
                `crop_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `crop_name` varchar(100) NOT NULL,
                `crop_icon` varchar(50) DEFAULT '🌾',
                `level_required` int(11) NOT NULL DEFAULT 1,
                `seed_cost` int(11) NOT NULL,
                `sell_price` int(11) NOT NULL,
                `grow_time` int(11) NOT NULL,
                `xp_reward` int(11) NOT NULL DEFAULT 10,
                `item_id` int(11) unsigned DEFAULT 0,
                PRIMARY KEY (`crop_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $this->updates_applied[] = "Created table: farm_crops";
        
        // Add sample crops
        $this->db->query("
            INSERT IGNORE INTO `farm_crops` 
            (`crop_name`, `crop_icon`, `level_required`, `seed_cost`, `sell_price`, `grow_time`, `xp_reward`) VALUES
            ('Wheat', '🌾', 1, 100, 200, 300, 5),
            ('Corn', '🌽', 2, 250, 500, 600, 10),
            ('Tomatoes', '🍅', 3, 500, 1000, 900, 15),
            ('Potatoes', '🥔', 4, 750, 1500, 1200, 20),
            ('Carrots', '🥕', 5, 1000, 2000, 1500, 25)
        ");
        $this->updates_applied[] = "Added sample crops";
    }
    
    /**
     * Create stock market system tables
     */
    private function createStockMarketTables() {
        // Create asset_market table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `asset_market` (
                `am_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `am_name` varchar(100) NOT NULL,
                `am_symbol` varchar(10) NOT NULL,
                `am_desc` text,
                `am_min` int(11) unsigned NOT NULL DEFAULT 10,
                `am_max` int(11) unsigned NOT NULL DEFAULT 10000,
                `am_start` int(11) unsigned NOT NULL DEFAULT 100,
                `am_cost` int(11) unsigned NOT NULL DEFAULT 100,
                `am_change` int(11) NOT NULL DEFAULT 0,
                `am_risk` tinyint(1) unsigned NOT NULL DEFAULT 1,
                `am_last_update` int(11) NOT NULL DEFAULT 0,
                PRIMARY KEY (`am_id`),
                UNIQUE KEY `am_symbol` (`am_symbol`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $this->updates_applied[] = "Created table: asset_market";
        
        // Create asset_market_owned table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `asset_market_owned` (
                `amo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `userid` int(11) unsigned NOT NULL,
                `am_id` int(11) unsigned NOT NULL,
                `shares_owned` int(11) unsigned NOT NULL DEFAULT 0,
                `shares_cost` bigint(20) unsigned NOT NULL DEFAULT 0,
                `last_transaction` int(11) NOT NULL,
                PRIMARY KEY (`amo_id`),
                UNIQUE KEY `user_asset` (`userid`, `am_id`),
                KEY `userid` (`userid`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $this->updates_applied[] = "Created table: asset_market_owned";
        
        // Create asset_market_history table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `asset_market_history` (
                `amh_id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
                `am_id` int(11) unsigned NOT NULL,
                `old_value` int(11) unsigned NOT NULL,
                `difference` int(11) NOT NULL,
                `new_value` int(11) unsigned NOT NULL,
                `timestamp` int(11) unsigned NOT NULL,
                PRIMARY KEY (`amh_id`),
                KEY `am_id` (`am_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $this->updates_applied[] = "Created table: asset_market_history";
        
        // Create asset_market_profit table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `asset_market_profit` (
                `userid` int(11) unsigned NOT NULL,
                `total_invested` bigint(20) NOT NULL DEFAULT 0,
                `total_returned` bigint(20) NOT NULL DEFAULT 0,
                `profit` bigint(20) NOT NULL DEFAULT 0,
                PRIMARY KEY (`userid`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $this->updates_applied[] = "Created table: asset_market_profit";
        
        // Add sample stocks
        $stocks = [
            ['Chivalry Mining Corp', 'CMC', 'Leading mining company', 50, 5000, 500, 500, 2],
            ['Royal Bank', 'RBK', 'Kingdom\'s largest bank', 100, 2000, 300, 300, 1],
            ['Dragon Airways', 'DAW', 'Premium air travel', 200, 8000, 1000, 1000, 3],
            ['Peasant Foods Inc', 'PFI', 'Food production giant', 20, 1000, 100, 100, 1]
        ];
        
        foreach ($stocks as $stock) {
            $this->db->query("
                INSERT IGNORE INTO asset_market 
                (am_name, am_symbol, am_desc, am_min, am_max, am_start, am_cost, am_risk, am_last_update)
                VALUES ('{$stock[0]}', '{$stock[1]}', '{$stock[2]}', {$stock[3]}, {$stock[4]}, 
                        {$stock[5]}, {$stock[6]}, {$stock[7]}, " . time() . ")
            ");
        }
        $this->updates_applied[] = "Added sample stocks";
    }
    
    /**
     * Create weather system tables for v3.3
     */
    private function createWeatherTables() {
        // Create weather_current table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `weather_current` (
                `weather_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `weather_type` varchar(50) NOT NULL,
                `weather_intensity` float NOT NULL DEFAULT 1.0,
                `started_at` int(11) NOT NULL,
                `expires_at` int(11) NOT NULL,
                `season` varchar(20) NOT NULL,
                `is_active` tinyint(1) NOT NULL DEFAULT 1,
                PRIMARY KEY (`weather_id`),
                KEY `is_active` (`is_active`),
                KEY `expires_at` (`expires_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $this->updates_applied[] = "Created table: weather_current";
        
        // Create weather_history table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `weather_history` (
                `history_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `weather_type` varchar(50) NOT NULL,
                `started_at` int(11) NOT NULL,
                `ended_at` int(11) NOT NULL,
                `affected_users` int(11) NOT NULL DEFAULT 0,
                PRIMARY KEY (`history_id`),
                KEY `started_at` (`started_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $this->updates_applied[] = "Created table: weather_history";
        
        // Create weather_forecasts table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `weather_forecasts` (
                `forecast_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `forecast_time` int(11) NOT NULL,
                `weather_type` varchar(50) NOT NULL,
                `probability` int(11) NOT NULL,
                `created_at` int(11) NOT NULL,
                PRIMARY KEY (`forecast_id`),
                KEY `forecast_time` (`forecast_time`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $this->updates_applied[] = "Created table: weather_forecasts";
        
        // Initialize with current weather
        $current_time = time();
        $season = $this->getCurrentSeason();
        $this->db->query("
            INSERT INTO weather_current 
            (weather_type, weather_intensity, started_at, expires_at, season, is_active)
            VALUES ('sunny', 1.0, {$current_time}, " . ($current_time + 7200) . ", '{$season}', 1)
        ");
        $this->updates_applied[] = "Initialized weather system with sunny weather";
    }
    
    /**
     * Get current season based on month
     */
    private function getCurrentSeason() {
        $month = date('n');
        if ($month >= 3 && $month <= 5) {
            return 'spring';
        } elseif ($month >= 6 && $month <= 8) {
            return 'summer';
        } elseif ($month >= 9 && $month <= 11) {
            return 'autumn';
        } else {
            return 'winter';
        }
    }
    
    /**
     * Update database version
     */
    private function updateVersion($version) {
        if ($this->tableExists('settings')) {
            // Check if db_version exists
            $exists = $this->db->fetch_single($this->db->query("SELECT COUNT(*) FROM settings WHERE setting_name = 'db_version'"));
            
            if ($exists > 0) {
                $this->db->query("UPDATE settings SET setting_value = '{$version}' WHERE setting_name = 'db_version'");
            } else {
                $this->db->query("INSERT INTO settings (setting_name, setting_value) VALUES ('db_version', '{$version}')");
            }
        }
    }
    
    /**
     * Check if updates should be applied
     */
    private function shouldApplyUpdates() {
        if (php_sapi_name() === 'cli') {
            // CLI mode - ask for confirmation
            echo "\nApply updates? (yes/no): ";
            $handle = fopen("php://stdin", "r");
            $line = fgets($handle);
            fclose($handle);
            return trim(strtolower($line)) === 'yes';
        } else {
            // Web mode - check for POST confirmation
            if (isset($_POST['apply_updates'])) {
                return true;
            }
            
            // Show confirmation form
            echo '<form method="POST" class="mt-3">';
            echo '<button type="submit" name="apply_updates" value="1" class="btn btn-primary">';
            echo '<i class="fas fa-download"></i> Apply Updates</button>';
            echo '</form>';
            return false;
        }
    }
    
    /**
     * Format message based on type
     */
    private function formatMessage($type, $message) {
        if (php_sapi_name() === 'cli') {
            // CLI formatting
            switch($type) {
                case 'success':
                    return "\033[32m{$message}\033[0m\n";
                case 'error':
                    return "\033[31m{$message}\033[0m\n";
                case 'warning':
                    return "\033[33m{$message}\033[0m\n";
                case 'info':
                    return "\033[36m{$message}\033[0m\n";
                default:
                    return "{$message}\n";
            }
        } else {
            // HTML formatting
            switch($type) {
                case 'success':
                    return '<div class="alert alert-success">' . $message . '</div>';
                case 'error':
                    return '<div class="alert alert-danger">' . $message . '</div>';
                case 'warning':
                    return '<div class="alert alert-warning">' . $message . '</div>';
                case 'info':
                    return '<div class="alert alert-info">' . $message . '</div>';
                default:
                    return '<div class="text-muted">' . $message . '</div>';
            }
        }
    }
    
    /**
     * Get header
     */
    private function getHeader() {
        if (php_sapi_name() === 'cli') {
            return "\n" . str_repeat('=', 50) . "\n";
            return "CHIVALRY ENGINE v3 - UPLIFT CHECK\n";
            return str_repeat('=', 50) . "\n\n";
        } else {
            return '
            <div class="container mt-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3><i class="fas fa-sync"></i> Chivalry Engine v3 - Uplift Check</h3>
                    </div>
                    <div class="card-body">
            ';
        }
    }
    
    /**
     * Get footer
     */
    private function getFooter() {
        if (php_sapi_name() === 'cli') {
            return "\n" . str_repeat('=', 50) . "\n";
        } else {
            return '
                    </div>
                </div>
            </div>
            ';
        }
    }
}

// Run the uplift checker
$checker = new UpliftChecker($db);
$checker->run();

// If web mode and not admin, show the page footer
if (php_sapi_name() !== 'cli' && isset($h)) {
    $h->endpage();
}
?>