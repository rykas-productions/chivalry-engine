<?php
/*
    File: event_system.php
    Created: Modern event-driven system to replace cron jobs
    Info: Processes events on-demand when users need them
*/

class EventSystem {
    private $db;
    private $userid;
    private $time;
    
    public function __construct($db, $userid = 0) {
        $this->db = $db;
        $this->userid = $userid;
        $this->time = time();
    }
    
    /**
     * Process events for a specific user when they log in or view relevant pages
     */
    public function processUserEvents($userid) {
        try {
            // First check if user_meta table exists
            $table_check = $this->db->query("SHOW TABLES LIKE 'user_meta'");
            if (!$this->db->num_rows($table_check)) {
                // Table doesn't exist, create it
                $this->createUserMetaTable();
            }
            
            // Check last processing time for this user
            $last_processed = $this->getUserLastProcessed($userid);
            
            // Only process if enough time has passed (prevent spam)
            // Increased to 60 seconds to prevent rapid-fire processing
            if ($this->time - $last_processed < 60) {
                return false;
            }
            
            // Update last processed time
            $this->updateUserLastProcessed($userid);
            
            // Process different event types with error handling
            @$this->processEnergyRegeneration($userid);
            @$this->processWillRegeneration($userid);
            @$this->processBraveRegeneration($userid);
            @$this->processHPRegeneration($userid);
            @$this->processJailTime($userid);
            @$this->processHospitalTime($userid);
            @$this->processSmeltingComplete($userid);
            @$this->processJobIncome($userid);
            @$this->processMiningRegeneration($userid);
            
            return true;
        } catch (Exception $e) {
            // Log error but don't break the page
            error_log("Event System Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Energy regeneration - calculated based on time passed
     */
    private function processEnergyRegeneration($userid) {
        $user = $this->db->fetch_row($this->db->query(
            "SELECT energy, maxenergy, laston, vip_days FROM users WHERE userid = {$userid}"
        ));
        
        if (isset($user['energy']) && isset($user['maxenergy']) && $user['energy'] < $user['maxenergy']) {
            // Calculate energy gained - more reasonable rate: 1 point per minute, capped at reasonable amounts
            $time_passed = $this->time - $user['laston'];
            $minutes_passed = floor($time_passed / 60); // 60 seconds = 1 minute
            
            if ($minutes_passed > 0) {
                // Base regeneration: 1 energy per minute, but cap the offline gain to prevent massive jumps
                $max_offline_minutes = 240; // Max 4 hours of offline regeneration at once
                $effective_minutes = min($minutes_passed, $max_offline_minutes);
                
                // Calculate base gain: 1 energy per minute
                $energy_gain = $effective_minutes;
                
                // VIP BENEFIT: 2x energy regeneration for VIP players
                if ($user['vip_days'] > 0) {
                    $energy_gain = $energy_gain * 2;
                }
                
                $new_energy = min($user['maxenergy'], $user['energy'] + $energy_gain);
                
                // Only update if there's actually a change
                if ($new_energy > $user['energy']) {
                    $this->db->query(
                        "UPDATE users SET energy = {$new_energy} WHERE userid = {$userid}"
                    );
                }
            }
        }
    }
    
    /**
     * Will regeneration
     */
    private function processWillRegeneration($userid) {
        $user = $this->db->fetch_row($this->db->query(
            "SELECT will, maxwill, laston FROM users WHERE userid = {$userid}"
        ));
        
        if (isset($user['will']) && isset($user['maxwill']) && $user['will'] < $user['maxwill']) {
            $time_passed = $this->time - $user['laston'];
            $minutes_passed = floor($time_passed / 60);
            
            if ($minutes_passed > 0) {
                // Will regenerates slower: 1 point per 3 minutes, max 4 hours offline
                $max_offline_minutes = 240;
                $effective_minutes = min($minutes_passed, $max_offline_minutes);
                $will_gain = floor($effective_minutes / 3); // 1 will per 3 minutes
                
                if ($will_gain > 0) {
                    $new_will = min($user['maxwill'], $user['will'] + $will_gain);
                    
                    if ($new_will > $user['will']) {
                        $this->db->query(
                            "UPDATE users SET will = {$new_will} WHERE userid = {$userid}"
                        );
                    }
                }
            }
        }
    }
    
    /**
     * Brave regeneration
     */
    private function processBraveRegeneration($userid) {
        $user = $this->db->fetch_row($this->db->query(
            "SELECT brave, maxbrave, laston FROM users WHERE userid = {$userid}"
        ));
        
        if (isset($user['brave']) && isset($user['maxbrave']) && $user['brave'] < $user['maxbrave']) {
            $time_passed = $this->time - $user['laston'];
            $minutes_passed = floor($time_passed / 60);
            
            if ($minutes_passed > 0) {
                // Brave regenerates slowly: 1 point per 5 minutes, max 4 hours offline
                $max_offline_minutes = 240;
                $effective_minutes = min($minutes_passed, $max_offline_minutes);
                $brave_gain = floor($effective_minutes / 5); // 1 brave per 5 minutes
                
                if ($brave_gain > 0) {
                    $new_brave = min($user['maxbrave'], $user['brave'] + $brave_gain);
                    
                    if ($new_brave > $user['brave']) {
                        $this->db->query(
                            "UPDATE users SET brave = {$new_brave} WHERE userid = {$userid}"
                        );
                    }
                }
            }
        }
    }
    
    /**
     * HP regeneration
     */
    private function processHPRegeneration($userid) {
        $user = $this->db->fetch_row($this->db->query(
            "SELECT hp, maxhp, laston FROM users WHERE userid = {$userid}"
        ));
        
        if (isset($user['hp']) && isset($user['maxhp']) && $user['hp'] < $user['maxhp']) {
            $time_passed = $this->time - $user['laston'];
            $minutes_passed = floor($time_passed / 60);
            
            if ($minutes_passed > 0) {
                // HP regenerates very slowly: 1 point per 10 minutes, max 4 hours offline
                $max_offline_minutes = 240;
                $effective_minutes = min($minutes_passed, $max_offline_minutes);
                $hp_gain = floor($effective_minutes / 10); // 1 HP per 10 minutes
                
                if ($hp_gain > 0) {
                    $new_hp = min($user['maxhp'], $user['hp'] + $hp_gain);
                    
                    if ($new_hp > $user['hp']) {
                        $this->db->query(
                            "UPDATE users SET hp = {$new_hp} WHERE userid = {$userid}"
                        );
                    }
                }
            }
        }
    }
    
    /**
     * Process jail time
     */
    private function processJailTime($userid) {
        $jail = $this->db->fetch_row($this->db->query(
            "SELECT * FROM dungeon WHERE dungeon_user = {$userid} AND dungeon_out > 0 AND dungeon_out <= {$this->time}"
        ));
        
        if ($jail) {
            $this->db->query("UPDATE dungeon SET dungeon_out = 0 WHERE dungeon_user = {$userid}");
            $this->addNotification($userid, "You have been released from the dungeon!", 'fas fa-unlock');
            $this->addLog($userid, 'dungeon', "Released from dungeon after serving time");
        }
    }
    
    /**
     * Process hospital time
     */
    private function processHospitalTime($userid) {
        $hosp = $this->db->fetch_row($this->db->query(
            "SELECT * FROM infirmary WHERE infirmary_user = {$userid} AND infirmary_out > 0 AND infirmary_out <= {$this->time}"
        ));
        
        if ($hosp) {
            $this->db->query("UPDATE infirmary SET infirmary_out = 0 WHERE infirmary_user = {$userid}");
            $this->db->query("UPDATE users SET hp = maxhp WHERE userid = {$userid}");
            $this->addNotification($userid, "You have been discharged from the infirmary with full health!", 'fas fa-heartbeat');
            $this->addLog($userid, 'infirmary', "Discharged from infirmary");
        }
    }
    
    /**
     * Process completed smelting
     */
    private function processSmeltingComplete($userid) {
        $smelts = $this->db->query(
            "SELECT s.*, r.* FROM smelt_inprogress s 
             INNER JOIN smelt_recipes r ON s.sip_recipe = r.smelt_id
             WHERE s.sip_user = {$userid} AND s.sip_time <= {$this->time}"
        );
        
        while ($smelt = $this->db->fetch_row($smelts)) {
            // Give item to user
            $this->giveItem($userid, $smelt['smelt_output'], $smelt['smelt_qty_output']);
            
            // Notify user
            $item_name = $this->getItemName($smelt['smelt_output']);
            $this->addNotification($userid, 
                "Smelting complete! You received {$smelt['smelt_qty_output']} {$item_name}(s).",
                'fas fa-fire'
            );
            
            // Remove from in-progress
            $this->db->query(
                "DELETE FROM smelt_inprogress WHERE sip_user = {$userid} AND sip_recipe = {$smelt['sip_recipe']}"
            );
        }
    }
    
    /**
     * Process job income (hourly)
     */
    private function processJobIncome($userid) {
        $user = $this->db->fetch_row($this->db->query(
            "SELECT u.*, j.* FROM users u 
             LEFT JOIN jobs j ON u.job = j.jRANK 
             WHERE u.userid = {$userid}"
        ));
        
        // Check if user has a job and job data exists
        if (isset($user['job']) && $user['job'] > 0 && isset($user['jPAY']) && $user['jPAY'] > 0) {
            // Check last job payment
            $last_payment = $this->getUserMeta($userid, 'last_job_payment', 0);
            $hours_passed = floor(($this->time - $last_payment) / 3600);
            
            if ($hours_passed > 0) {
                $payment = $user['jPAY'] * $hours_passed;
                
                $this->db->query(
                    "UPDATE users SET primary_currency = primary_currency + {$payment} WHERE userid = {$userid}"
                );
                
                $this->setUserMeta($userid, 'last_job_payment', $this->time);
                
                $this->addNotification($userid, 
                    "Job income: You earned \$" . number_format($payment) . " from your job!",
                    'fas fa-coins'
                );
            }
        }
    }
    
    /**
     * Process mining power regeneration (every 5 minutes)
     */
    private function processMiningRegeneration($userid) {
        // Check if user has mining data
        $mining = $this->db->fetch_row($this->db->query(
            "SELECT * FROM mining WHERE userid = {$userid}"
        ));
        
        if (!$mining) {
            return; // User doesn't have mining data
        }
        
        // Check last mining regeneration
        $last_regen = $this->getUserMeta($userid, 'last_mining_regen', 0);
        $minutes_passed = floor(($this->time - $last_regen) / 300); // 5-minute intervals
        
        if ($minutes_passed >= 1) {
            // Calculate regeneration amount (10% of max every 5 minutes)
            $max_power = (int)$mining['max_miningpower'];
            $current_power = (int)$mining['miningpower'];
            $regen_amount = floor($max_power / 10);
            
            if ($current_power < $max_power) {
                $new_power = min($current_power + ($regen_amount * $minutes_passed), $max_power);
                
                // Update mining power
                $this->db->query(
                    "UPDATE mining SET miningpower = {$new_power} WHERE userid = {$userid}"
                );
                
                // Update last regeneration time
                $this->setUserMeta($userid, 'last_mining_regen', $this->time);
                
                // Notify if significant regeneration occurred
                if ($minutes_passed >= 2) {
                    $gained = $new_power - $current_power;
                    $this->addNotification(
                        $userid,
                        "Mining power regenerated: +{$gained} power (now {$new_power}/{$max_power})",
                        'fas fa-pickaxe',
                        'info'
                    );
                }
            }
        }
    }
    
    /**
     * Process daily events (called once per day per user)
     */
    public function processDailyEvents($userid) {
        // Get last daily bonus time specifically
        $last_daily_bonus = $this->getUserMeta($userid, 'last_daily_bonus_date', '');
        $today = date('Y-m-d', $this->time);
        
        // Check if daily bonus was already given today
        if ($last_daily_bonus === $today) {
            return false; // Already processed today
        }
        
        // Process daily events
        $this->processDailyBonus($userid);
        $this->resetDailyLimits($userid);
        $this->processVIPDecay($userid);
        $this->processBankInterest($userid);
        
        // Mark today as processed
        $this->setUserMeta($userid, 'last_daily_bonus_date', $today);
        
        return true;
    }
    
    /**
     * Daily login bonus
     */
    private function processDailyBonus($userid) {
        // Get user VIP status
        $user = $this->db->fetch_row($this->db->query("SELECT vip_days FROM users WHERE userid = {$userid}"));
        $isVIP = $user && $user['vip_days'] > 0;
        
        $bonus = 100; // Base daily bonus
        
        // Get last bonus date and current streak
        $last_bonus_date = $this->getUserMeta($userid, 'last_daily_bonus_date', '');
        $current_streak = intval($this->getUserMeta($userid, 'login_streak', 0));
        
        // Calculate if streak continues or resets
        if ($last_bonus_date) {
            $last_date = strtotime($last_bonus_date);
            $yesterday = strtotime(date('Y-m-d', $this->time - 86400)); // 86400 = 24 hours
            
            if (date('Y-m-d', $last_date) == date('Y-m-d', $yesterday)) {
                // Last bonus was yesterday, continue streak
                $current_streak++;
            } else if ($last_date < $yesterday) {
                // Missed a day, reset streak
                $current_streak = 1;
            }
            // If same day, don't increment (shouldn't happen due to earlier check)
        } else {
            // First time bonus
            $current_streak = 1;
        }
        
        // Save the streak
        $this->setUserMeta($userid, 'login_streak', $current_streak);
        
        // Calculate bonus based on streak
        if ($current_streak >= 7) $bonus += 50;
        if ($current_streak >= 30) $bonus += 100;
        if ($current_streak >= 100) $bonus += 200;
        
        // VIP BENEFIT: 2x daily bonus for VIP players
        if ($isVIP) {
            $bonus = $bonus * 2;
        }
        
        // Give the bonus
        $this->db->query(
            "UPDATE users SET primary_currency = primary_currency + {$bonus} WHERE userid = {$userid}"
        );
        
        // Log the daily bonus to prevent duplicates
        $today = date('Y-m-d', $this->time);
        $this->db->query(
            "INSERT IGNORE INTO daily_bonus_log (userid, bonus_date, bonus_amount, streak_count) 
             VALUES ({$userid}, '{$today}', {$bonus}, {$current_streak})"
        );
        
        $vipText = $isVIP ? " [VIP 2x Bonus]" : "";
        $this->addNotification($userid, 
            "Daily bonus: \$" . number_format($bonus) . " (Streak: {$current_streak} " . 
            ($current_streak == 1 ? 'day' : 'days') . "){$vipText}",
            'fas fa-gift'
        );
    }
    
    /**
     * Reset daily limits (crimes, etc.)
     */
    private function resetDailyLimits($userid) {
        // Safely check if crimes_done table exists before trying to delete
        try {
            $table_check = @$this->db->query("SHOW TABLES LIKE 'crimes_done'");
            if ($table_check && $this->db->num_rows($table_check)) {
                // Reset crime attempts
                $this->db->query("DELETE FROM crimes_done WHERE userid = {$userid}");
            }
        } catch (Exception $e) {
            // Table doesn't exist, that's okay
        }
        
        // Reset other daily limits as needed
        $this->setUserMeta($userid, 'daily_gym_bonus', 0);
        $this->setUserMeta($userid, 'daily_votes', 0);
    }
    
    /**
     * Process VIP days decay (daily)
     */
    private function processVIPDecay($userid) {
        $user = $this->db->fetch_row($this->db->query(
            "SELECT vip_days FROM users WHERE userid = {$userid}"
        ));
        
        if ($user && $user['vip_days'] > 0) {
            $new_vip_days = max(0, $user['vip_days'] - 1);
            $this->db->query(
                "UPDATE users SET vip_days = {$new_vip_days} WHERE userid = {$userid}"
            );
            
            if ($new_vip_days == 0) {
                $this->addNotification(
                    $userid,
                    "Your VIP membership has expired. Renew to continue enjoying VIP benefits!",
                    'fas fa-crown',
                    'warning'
                );
            } else if ($new_vip_days <= 3) {
                $this->addNotification(
                    $userid,
                    "VIP expiring soon! Only {$new_vip_days} days remaining.",
                    'fas fa-crown',
                    'warning'
                );
            }
        }
    }
    
    /**
     * Process bank interest (daily)
     */
    private function processBankInterest($userid) {
        $user = $this->db->fetch_row($this->db->query(
            "SELECT bank, vip_days FROM users WHERE userid = {$userid}"
        ));
        
        if ($user && $user['bank'] > 0) {
            $isVIP = $user['vip_days'] > 0;
            
            // Base 2% daily interest, VIP gets 3% (50% bonus)
            $interestRate = $isVIP ? 33 : 50; // 1/33 = 3%, 1/50 = 2%
            $interest = floor($user['bank'] / $interestRate);
            $new_balance = $user['bank'] + $interest;
            
            $this->db->query(
                "UPDATE users SET bank = {$new_balance} WHERE userid = {$userid}"
            );
            
            if ($interest > 0) {
                $vipText = $isVIP ? " [VIP 3% Rate]" : " [2% Rate]";
                $this->addNotification(
                    $userid,
                    "Bank interest earned: \$" . number_format($interest) . $vipText . " (new balance: \$" . number_format($new_balance) . ")",
                    'fas fa-university',
                    'success'
                );
            }
        }
    }
    
    /**
     * Helper functions
     */
    private function getUserLastProcessed($userid) {
        $result = $this->db->query(
            "SELECT meta_value FROM user_meta 
             WHERE userid = {$userid} AND meta_key = 'last_event_process'"
        );
        
        if ($this->db->num_rows($result)) {
            return $this->db->fetch_single($result);
        }
        
        return 0;
    }
    
    private function updateUserLastProcessed($userid) {
        $this->db->query(
            "INSERT INTO user_meta (userid, meta_key, meta_value) 
             VALUES ({$userid}, 'last_event_process', '{$this->time}')
             ON DUPLICATE KEY UPDATE meta_value = '{$this->time}'"
        );
    }
    
    private function getUserMeta($userid, $key, $default = null) {
        $result = $this->db->query(
            "SELECT meta_value FROM user_meta 
             WHERE userid = {$userid} AND meta_key = '{$key}'"
        );
        
        if ($this->db->num_rows($result)) {
            return $this->db->fetch_single($result);
        }
        
        return $default;
    }
    
    private function setUserMeta($userid, $key, $value) {
        $key = $this->db->escape($key);
        $value = $this->db->escape($value);
        
        $this->db->query(
            "INSERT INTO user_meta (userid, meta_key, meta_value) 
             VALUES ({$userid}, '{$key}', '{$value}')
             ON DUPLICATE KEY UPDATE meta_value = '{$value}'"
        );
    }
    
    private function addNotification($userid, $text, $icon = 'fas fa-info-circle', $color = 'primary') {
        $text = $this->db->escape($text);
        $icon = $this->db->escape($icon);
        $color = $this->db->escape($color);
        
        // Get notification table structure once
        static $notif_columns = null;
        if ($notif_columns === null) {
            $notif_columns = [];
            $result = $this->db->query("SHOW COLUMNS FROM `notifications`");
            while ($row = $this->db->fetch_row($result)) {
                $notif_columns[$row['Field']] = true;
            }
        }
        
        // Build query based on available columns
        $fields = ['notif_user', 'notif_text', 'notif_time', 'notif_status'];
        $values = [$userid, "'{$text}'", $this->time, "'unread'"];
        
        if (isset($notif_columns['notif_icon'])) {
            $fields[] = 'notif_icon';
            $values[] = "'{$icon}'";
        }
        
        if (isset($notif_columns['notif_color'])) {
            $fields[] = 'notif_color';
            $values[] = "'{$color}'";
        }
        
        if (isset($notif_columns['notif_priority'])) {
            $fields[] = 'notif_priority';
            $values[] = "'normal'";
        }
        
        $query = "INSERT INTO notifications (" . implode(', ', $fields) . ") 
                  VALUES (" . implode(', ', $values) . ")";
        
        $this->db->query($query);
    }
    
    private function addLog($userid, $type, $text) {
        $type = $this->db->escape($type);
        $text = $this->db->escape($text);
        $this->db->query(
            "INSERT INTO logs (log_user, log_type, log_text, log_time)
             VALUES ({$userid}, '{$type}', '{$text}', {$this->time})"
        );
    }
    
    private function giveItem($userid, $itemid, $quantity) {
        $check = $this->db->query(
            "SELECT * FROM inventory WHERE inv_userid = {$userid} AND inv_itemid = {$itemid}"
        );
        
        if ($this->db->num_rows($check)) {
            $this->db->query(
                "UPDATE inventory SET inv_qty = inv_qty + {$quantity} 
                 WHERE inv_userid = {$userid} AND inv_itemid = {$itemid}"
            );
        } else {
            $this->db->query(
                "INSERT INTO inventory (inv_userid, inv_itemid, inv_qty) 
                 VALUES ({$userid}, {$itemid}, {$quantity})"
            );
        }
    }
    
    private function getItemName($itemid) {
        return $this->db->fetch_single(
            $this->db->query("SELECT itmname FROM items WHERE itmid = {$itemid}")
        );
    }
    
    /**
     * Create user_meta table if it doesn't exist
     */
    private function createUserMetaTable() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `user_meta` (
                `meta_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `userid` INT(11) UNSIGNED NOT NULL,
                `meta_key` VARCHAR(100) NOT NULL,
                `meta_value` TEXT,
                `updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`meta_id`),
                UNIQUE KEY `user_key` (`userid`, `meta_key`),
                KEY `userid` (`userid`),
                KEY `meta_key` (`meta_key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }
}

/**
 * Global event processor - call this in globals.php
 */
function processEvents($db, $userid) {
    $events = new EventSystem($db, $userid);
    
    // Process regular events
    $events->processUserEvents($userid);
    
    // Process daily events
    $events->processDailyEvents($userid);
}
?>