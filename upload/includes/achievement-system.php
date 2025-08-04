<?php
/*
    File: achievement-system.php
    Created: Complete achievement system with notifications
    Info: Handles all achievement tracking, earning, and rewards
*/

class AchievementSystem {
    private $db;
    private $userid;
    private $api;
    
    public function __construct($db, $userid, $api) {
        $this->db = $db;
        $this->userid = $userid;
        $this->api = $api;
    }
    
    /**
     * Track progress for an achievement type
     */
    public function trackProgress($type, $value = 1, $increment = true) {
        $type = $this->db->escape($type);
        
        if ($increment) {
            // Increment existing value
            $this->db->query("
                INSERT INTO achievement_progress (ap_user, ap_type, ap_value) 
                VALUES ({$this->userid}, '{$type}', {$value})
                ON DUPLICATE KEY UPDATE 
                ap_value = ap_value + {$value},
                ap_updated = NOW()
            ");
        } else {
            // Set absolute value
            $this->db->query("
                INSERT INTO achievement_progress (ap_user, ap_type, ap_value) 
                VALUES ({$this->userid}, '{$type}', {$value})
                ON DUPLICATE KEY UPDATE 
                ap_value = {$value},
                ap_updated = NOW()
            ");
        }
        
        // Check for new achievements
        $this->checkAchievements($type);
    }
    
    /**
     * Check if user has earned new achievements
     */
    public function checkAchievements($type = null) {
        $where = $type ? "WHERE ach_type = '{$type}'" : "";
        
        // Get all achievements user hasn't earned yet
        $query = $this->db->query("
            SELECT a.*, COALESCE(ap.ap_value, 0) as current_progress
            FROM achievements a
            LEFT JOIN user_achievements ua ON a.ach_id = ua.ua_ach AND ua.ua_user = {$this->userid}
            LEFT JOIN achievement_progress ap ON ap.ap_user = {$this->userid} AND ap.ap_type = a.ach_type
            {$where}
            AND ua.ua_id IS NULL
            AND a.ach_active = 1
        ");
        
        $earned = [];
        while ($ach = $this->db->fetch_row($query)) {
            $qualified = false;
            
            // Check different achievement types
            switch($ach['ach_type']) {
                case 'battles_won':
                case 'crimes_done':
                case 'trains_done':
                case 'messages_sent':
                case 'money_saved':
                case 'casino_winnings':
                    $qualified = ($ach['current_progress'] >= $ach['ach_requirement']);
                    break;
                    
                case 'level':
                    $user = $this->db->fetch_row($this->db->query("SELECT level FROM users WHERE userid = {$this->userid}"));
                    $qualified = ($user['level'] >= $ach['ach_requirement']);
                    break;
                    
                case 'married':
                    $married = $this->db->fetch_single($this->db->query("SELECT COUNT(*) FROM marriage_tmg WHERE (proposer_id = {$this->userid} OR proposed_id = {$this->userid}) AND together = 1"));
                    $qualified = ($married > 0);
                    break;
                    
                case 'guild_member':
                    $guild = $this->db->fetch_single($this->db->query("SELECT guild FROM users WHERE userid = {$this->userid}"));
                    $qualified = ($guild > 0);
                    break;
                    
                case 'guild_leader':
                    $leader = $this->db->fetch_single($this->db->query("SELECT COUNT(*) FROM guild WHERE guild_owner = {$this->userid}"));
                    $qualified = ($leader > 0);
                    break;
                    
                case 'friends_count':
                    $friends = $this->db->fetch_single($this->db->query("SELECT COUNT(*) FROM friends WHERE (friend_from = {$this->userid} OR friend_to = {$this->userid})"));
                    $qualified = ($friends >= $ach['ach_requirement']);
                    break;
            }
            
            if ($qualified) {
                $this->earnAchievement($ach);
                $earned[] = $ach;
            }
        }
        
        return $earned;
    }
    
    /**
     * Award an achievement to user
     */
    private function earnAchievement($achievement) {
        // Record achievement
        $this->db->query("
            INSERT INTO user_achievements (ua_user, ua_ach, ua_progress)
            VALUES ({$this->userid}, {$achievement['ach_id']}, {$achievement['ach_requirement']})
        ");
        
        // Update user's achievement points
        $this->db->query("
            UPDATE users 
            SET achievement_points = achievement_points + {$achievement['ach_points']},
                achievements_earned = achievements_earned + 1
            WHERE userid = {$this->userid}
        ");
        
        // Give rewards
        if ($achievement['ach_reward_type'] && $achievement['ach_reward_value']) {
            $this->giveReward($achievement['ach_reward_type'], $achievement['ach_reward_value']);
        }
        
        // Create notification
        $rarity_colors = [
            'common' => '#6c757d',
            'uncommon' => '#28a745',
            'rare' => '#007bff',
            'epic' => '#6f42c1',
            'legendary' => '#ffc107'
        ];
        
        $color = $rarity_colors[$achievement['ach_rarity']];
        $icon = $achievement['ach_icon'];
        
        // Add to notifications
        $notification = "<div style='color: {$color};'><i class='fas {$icon}'></i> <strong>Achievement Unlocked!</strong><br>{$achievement['ach_name']}: {$achievement['ach_desc']}<br>+{$achievement['ach_points']} points</div>";
        
        addNotification($this->userid, $notification);
        
        // Log the achievement
        $this->api->game->addLog($this->userid, 'achievement', "Earned achievement: {$achievement['ach_name']}");
    }
    
    /**
     * Give achievement rewards
     */
    private function giveReward($type, $value) {
        switch($type) {
            case 'money':
                $this->db->query("UPDATE users SET primary_currency = primary_currency + {$value} WHERE userid = {$this->userid}");
                break;
                
            case 'tokens':
                $this->db->query("UPDATE users SET secondary_currency = secondary_currency + {$value} WHERE userid = {$this->userid}");
                break;
                
            case 'energy':
                $this->db->query("UPDATE users SET energy = LEAST(energy + {$value}, maxenergy) WHERE userid = {$this->userid}");
                break;
                
            case 'stats':
                // Add to random stat
                $stats = ['strength', 'agility', 'guard', 'labor', 'iq'];
                $stat = $stats[array_rand($stats)];
                $this->db->query("UPDATE users SET {$stat} = {$stat} + {$value} WHERE userid = {$this->userid}");
                break;
        }
    }
    
    /**
     * Get user's achievements
     */
    public function getUserAchievements($userid = null) {
        if (!$userid) $userid = $this->userid;
        
        $query = $this->db->query("
            SELECT a.*, ua.ua_earned
            FROM user_achievements ua
            JOIN achievements a ON ua.ua_ach = a.ach_id
            WHERE ua.ua_user = {$userid}
            ORDER BY ua.ua_earned DESC
        ");
        
        $achievements = [];
        while ($row = $this->db->fetch_row($query)) {
            $achievements[] = $row;
        }
        
        return $achievements;
    }
    
    /**
     * Get achievement categories with progress
     */
    public function getCategories() {
        $query = $this->db->query("
            SELECT 
                ach_category,
                COUNT(*) as total,
                COUNT(ua.ua_id) as earned,
                SUM(ach_points) as total_points,
                SUM(IF(ua.ua_id IS NOT NULL, ach_points, 0)) as earned_points
            FROM achievements a
            LEFT JOIN user_achievements ua ON a.ach_id = ua.ua_ach AND ua.ua_user = {$this->userid}
            WHERE a.ach_active = 1 AND a.ach_hidden = 0
            GROUP BY ach_category
            ORDER BY ach_category
        ");
        
        $categories = [];
        while ($row = $this->db->fetch_row($query)) {
            $row['percentage'] = $row['total'] > 0 ? round(($row['earned'] / $row['total']) * 100) : 0;
            $categories[] = $row;
        }
        
        return $categories;
    }
    
    /**
     * Get achievements by category
     */
    public function getAchievementsByCategory($category) {
        $category = $this->db->escape($category);
        
        $query = $this->db->query("
            SELECT 
                a.*,
                ua.ua_earned,
                ua.ua_progress,
                COALESCE(ap.ap_value, 0) as current_progress
            FROM achievements a
            LEFT JOIN user_achievements ua ON a.ach_id = ua.ua_ach AND ua.ua_user = {$this->userid}
            LEFT JOIN achievement_progress ap ON ap.ap_user = {$this->userid} AND ap.ap_type = a.ach_type
            WHERE a.ach_category = '{$category}' 
            AND a.ach_active = 1 
            AND a.ach_hidden = 0
            ORDER BY a.ach_points, a.ach_name
        ");
        
        $achievements = [];
        while ($row = $this->db->fetch_row($query)) {
            $row['is_earned'] = !empty($row['ua_earned']);
            $row['progress_percent'] = $row['ach_requirement'] > 0 ? 
                min(100, round(($row['current_progress'] / $row['ach_requirement']) * 100)) : 0;
            $achievements[] = $row;
        }
        
        return $achievements;
    }
    
    /**
     * Get recent achievements (for notifications)
     */
    public function getRecentAchievements($limit = 5) {
        $query = $this->db->query("
            SELECT a.*, ua.ua_earned
            FROM user_achievements ua
            JOIN achievements a ON ua.ua_ach = a.ach_id
            WHERE ua.ua_user = {$this->userid}
            AND ua.ua_notified = 0
            ORDER BY ua.ua_earned DESC
            LIMIT {$limit}
        ");
        
        $achievements = [];
        while ($row = $this->db->fetch_row($query)) {
            $achievements[] = $row;
        }
        
        // Mark as notified
        if (count($achievements) > 0) {
            $ids = array_column($achievements, 'ach_id');
            $ids_str = implode(',', $ids);
            $this->db->query("
                UPDATE user_achievements 
                SET ua_notified = 1 
                WHERE ua_user = {$this->userid} 
                AND ua_ach IN ({$ids_str})
            ");
        }
        
        return $achievements;
    }
    
    /**
     * Get leaderboard
     */
    public function getLeaderboard($limit = 10) {
        $query = $this->db->query("
            SELECT 
                u.userid,
                u.username,
                u.achievement_points,
                u.achievements_earned,
                u.level
            FROM users u
            WHERE u.achievement_points > 0
            ORDER BY u.achievement_points DESC, u.achievements_earned DESC
            LIMIT {$limit}
        ");
        
        $leaderboard = [];
        $rank = 1;
        while ($row = $this->db->fetch_row($query)) {
            $row['rank'] = $rank++;
            $leaderboard[] = $row;
        }
        
        return $leaderboard;
    }
}
?>