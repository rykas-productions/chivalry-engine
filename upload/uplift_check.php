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
        
        // Check for missing tables and columns
        $this->checkAchievementSystem();
        $this->checkDailyRewardsSystem();
        $this->checkUserColumns();
        $this->checkVIPSystem();
        $this->checkMarriageSystem();
        $this->checkEstateSystem();
        
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
            
            // Update version
            $this->updateVersion('3.0.0');
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
     * Check for new user columns
     */
    private function checkUserColumns() {
        $columns = [
            'achievement_points' => 'int(11) DEFAULT 0',
            'achievements_earned' => 'int(11) DEFAULT 0'
        ];
        
        foreach ($columns as $column => $definition) {
            if (!$this->columnExists('users', $column)) {
                $this->updates_needed[] = "Add column: users.{$column}";
            }
        }
    }
    
    /**
     * Check VIP system
     */
    private function checkVIPSystem() {
        // Check for VIP packages
        if ($this->tableExists('vip_packages')) {
            $count = $this->db->fetch_single($this->db->query("SELECT COUNT(*) FROM vip_packages"));
            if ($count == 0) {
                $this->updates_needed[] = "Add VIP packages";
            }
        } else {
            $this->updates_needed[] = "Create VIP system tables";
        }
    }
    
    /**
     * Check Marriage system
     */
    private function checkMarriageSystem() {
        if (!$this->tableExists('marriage')) {
            $this->updates_needed[] = "Create marriage system table";
        }
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
        // Add user columns first if needed
        if (in_array("Add column: users.achievement_points", $this->updates_needed)) {
            $this->addUserColumn('achievement_points', 'int(11) DEFAULT 0');
        }
        if (in_array("Add column: users.achievements_earned", $this->updates_needed)) {
            $this->addUserColumn('achievements_earned', 'int(11) DEFAULT 0');
        }
        
        // Load the WOW features SQL
        $sql_file = __DIR__ . '/wow_features.sql';
        if (file_exists($sql_file)) {
            $this->executeSQLFile($sql_file);
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
     * Add column to users table
     */
    private function addUserColumn($column, $definition) {
        try {
            $this->db->query("ALTER TABLE `users` ADD COLUMN `{$column}` {$definition}");
            $this->updates_applied[] = "Added column: users.{$column}";
        } catch (Exception $e) {
            if (stripos($e->getMessage(), 'Duplicate column') === false) {
                $this->errors[] = "Failed to add column users.{$column}: " . $e->getMessage();
            }
        }
    }
    
    /**
     * Execute SQL file
     */
    private function executeSQLFile($file) {
        $sql = file_get_contents($file);
        
        // Remove SQL comments
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        
        // Split by semicolon followed by newline or end of string
        // This better handles multi-line statements
        $queries = preg_split('/;\s*$/m', $sql);
        
        foreach ($queries as $query) {
            $query = trim($query);
            if (empty($query)) continue;
            
            // Skip comment-only lines
            if (strpos($query, '--') === 0) continue;
            
            try {
                // Add semicolon back if needed
                if (substr($query, -1) !== ';') {
                    $query .= ';';
                }
                
                $this->db->query($query);
                
                // Determine what was done
                if (stripos($query, 'CREATE TABLE') !== false) {
                    preg_match('/CREATE TABLE.*?`([^`]+)`/i', $query, $matches);
                    if (isset($matches[1])) {
                        $this->updates_applied[] = "Created table: " . $matches[1];
                    }
                } elseif (stripos($query, 'ALTER TABLE') !== false) {
                    preg_match('/ALTER TABLE.*?`([^`]+)`.*?ADD.*?`([^`]+)`/i', $query, $matches);
                    if (isset($matches[1]) && isset($matches[2])) {
                        $this->updates_applied[] = "Added column: " . $matches[1] . "." . $matches[2];
                    }
                } elseif (stripos($query, 'INSERT') !== false) {
                    if (stripos($query, 'INSERT IGNORE') !== false) {
                        preg_match('/INSERT IGNORE INTO.*?`([^`]+)`/i', $query, $matches);
                    } else {
                        preg_match('/INSERT INTO.*?`([^`]+)`/i', $query, $matches);
                    }
                    if (isset($matches[1])) {
                        // Check if anything was actually inserted
                        if ($this->db->affected_rows() > 0) {
                            $this->updates_applied[] = "Added data to: " . $matches[1];
                        }
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