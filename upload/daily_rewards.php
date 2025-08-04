<?php
/*
    File: daily_rewards.php
    Created: Daily login rewards system with streaks
    Info: Claim daily rewards and maintain login streaks
*/
require_once('globals.php');

class DailyRewardsSystem {
    private $db;
    private $userid;
    private $api;
    
    public function __construct($db, $userid, $api) {
        $this->db = $db;
        $this->userid = $userid;
        $this->api = $api;
    }
    
    /**
     * Check and update user's daily login status
     */
    public function checkDailyLogin() {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        
        // Get user's reward data
        $user_data = $this->db->fetch_row($this->db->query("
            SELECT * FROM user_daily_rewards 
            WHERE udr_user = {$this->userid}
        "));
        
        if (!$user_data) {
            // First time user
            $this->db->query("
                INSERT INTO user_daily_rewards 
                (udr_user, udr_last_login, udr_streak, udr_total_days, udr_best_streak, udr_last_reward_day)
                VALUES ({$this->userid}, '{$today}', 1, 1, 1, 1)
            ");
            return ['new_user' => true, 'streak' => 1, 'can_claim' => true, 'day' => 1];
        }
        
        $result = ['new_user' => false, 'streak' => $user_data['udr_streak']];
        
        if ($user_data['udr_last_login'] == $today) {
            // Already logged in today
            $result['can_claim'] = false;
            $result['already_claimed'] = true;
        } elseif ($user_data['udr_last_login'] == $yesterday) {
            // Continuing streak
            $new_streak = $user_data['udr_streak'] + 1;
            $best_streak = max($new_streak, $user_data['udr_best_streak']);
            $total_days = $user_data['udr_total_days'] + 1;
            $reward_day = ($user_data['udr_last_reward_day'] % 30) + 1; // 30-day cycle
            
            $this->db->query("
                UPDATE user_daily_rewards SET
                udr_last_login = '{$today}',
                udr_streak = {$new_streak},
                udr_total_days = {$total_days},
                udr_best_streak = {$best_streak},
                udr_last_reward_day = {$reward_day}
                WHERE udr_user = {$this->userid}
            ");
            
            $result['streak'] = $new_streak;
            $result['can_claim'] = true;
            $result['day'] = $reward_day;
        } else {
            // Streak broken
            $this->db->query("
                UPDATE user_daily_rewards SET
                udr_last_login = '{$today}',
                udr_streak = 1,
                udr_total_days = udr_total_days + 1,
                udr_last_reward_day = 1
                WHERE udr_user = {$this->userid}
            ");
            
            $result['streak'] = 1;
            $result['streak_broken'] = true;
            $result['can_claim'] = true;
            $result['day'] = 1;
        }
        
        return $result;
    }
    
    /**
     * Claim daily reward
     */
    public function claimReward($day) {
        global $ir;
        
        // Get reward for this day
        $reward = $this->db->fetch_row($this->db->query("
            SELECT * FROM daily_rewards WHERE dr_day = {$day}
        "));
        
        if (!$reward) {
            return ['success' => false, 'message' => 'Invalid reward day'];
        }
        
        // Give the reward
        $success = false;
        $message = '';
        
        switch($reward['dr_type']) {
            case 'money':
                $this->db->query("UPDATE users SET primary_currency = primary_currency + {$reward['dr_value']} WHERE userid = {$this->userid}");
                $message = "You received " . number_format($reward['dr_value']) . " Gold!";
                $success = true;
                break;
                
            case 'tokens':
                $this->db->query("UPDATE users SET secondary_currency = secondary_currency + {$reward['dr_value']} WHERE userid = {$this->userid}");
                $message = "You received {$reward['dr_value']} Gems!";
                $success = true;
                break;
                
            case 'energy':
                $this->db->query("UPDATE users SET energy = LEAST(energy + {$reward['dr_value']}, maxenergy) WHERE userid = {$this->userid}");
                $message = "You received {$reward['dr_value']} Energy!";
                $success = true;
                break;
                
            case 'brave':
                $this->db->query("UPDATE users SET brave = LEAST(brave + {$reward['dr_value']}, maxbrave) WHERE userid = {$this->userid}");
                $message = "You received {$reward['dr_value']} Brave!";
                $success = true;
                break;
                
            case 'will':
                $this->db->query("UPDATE users SET will = LEAST(will + {$reward['dr_value']}, maxwill) WHERE userid = {$this->userid}");
                $message = "You received {$reward['dr_value']} Will!";
                $success = true;
                break;
                
            case 'stats':
                $stats = ['strength', 'agility', 'guard', 'labor', 'iq'];
                for ($i = 0; $i < $reward['dr_value']; $i++) {
                    $stat = $stats[array_rand($stats)];
                    $this->db->query("UPDATE userstats SET {$stat} = {$stat} + 1 WHERE userid = {$this->userid}");
                }
                $message = "You received +{$reward['dr_value']} to random stats!";
                $success = true;
                break;
                
            case 'item':
                $this->api->game->addItem($this->userid, $reward['dr_item_id'], $reward['dr_value']);
                $item_name = $this->db->fetch_single($this->db->query("SELECT itmname FROM items WHERE itmid = {$reward['dr_item_id']}"));
                $qty = $reward['dr_value'] > 1 ? "{$reward['dr_value']}x " : "";
                $message = "You received {$qty}{$item_name}!";
                $success = true;
                break;
                
            case 'vip':
                $this->db->query("UPDATE users SET vip_days = vip_days + {$reward['dr_value']} WHERE userid = {$this->userid}");
                $message = "You received {$reward['dr_value']} VIP Day" . ($reward['dr_value'] > 1 ? 's' : '') . "!";
                $success = true;
                break;
        }
        
        if ($success) {
            // Log the reward
            $this->db->query("
                INSERT INTO daily_reward_history 
                (drh_user, drh_day, drh_reward_type, drh_reward_value)
                VALUES ({$this->userid}, {$day}, '{$reward['dr_type']}', {$reward['dr_value']})
            ");
            
            // Check for streak rewards
            $streak_rewards = $this->checkStreakRewards();
            if ($streak_rewards) {
                $message .= "<br><br><strong>🔥 Streak Bonus!</strong><br>" . $streak_rewards;
            }
        }
        
        return ['success' => $success, 'message' => $message, 'reward' => $reward];
    }
    
    /**
     * Check and give streak milestone rewards
     */
    private function checkStreakRewards() {
        $user_data = $this->db->fetch_row($this->db->query("
            SELECT udr_streak FROM user_daily_rewards WHERE udr_user = {$this->userid}
        "));
        
        $streak = $user_data['udr_streak'];
        
        // Check if user hit a milestone
        $milestone = $this->db->fetch_row($this->db->query("
            SELECT * FROM streak_rewards WHERE sr_days = {$streak}
        "));
        
        if (!$milestone) {
            return false;
        }
        
        // Give streak reward
        switch($milestone['sr_type']) {
            case 'money':
                $this->db->query("UPDATE users SET primary_currency = primary_currency + {$milestone['sr_value']} WHERE userid = {$this->userid}");
                break;
            case 'tokens':
                $this->db->query("UPDATE users SET secondary_currency = secondary_currency + {$milestone['sr_value']} WHERE userid = {$this->userid}");
                break;
            case 'stats':
                $stats = ['strength', 'agility', 'guard', 'labor', 'iq'];
                foreach ($stats as $stat) {
                    $this->db->query("UPDATE userstats SET {$stat} = {$stat} + {$milestone['sr_value']} WHERE userid = {$this->userid}");
                }
                break;
            case 'vip':
                $this->db->query("UPDATE users SET vip_days = vip_days + {$milestone['sr_value']} WHERE userid = {$this->userid}");
                break;
        }
        
        return $milestone['sr_description'];
    }
    
    /**
     * Get calendar data for display
     */
    public function getCalendarData() {
        // Get all rewards
        $rewards = [];
        $query = $this->db->query("SELECT * FROM daily_rewards ORDER BY dr_day");
        while ($row = $this->db->fetch_row($query)) {
            $rewards[$row['dr_day']] = $row;
        }
        
        // Get user's current status
        $user_data = $this->db->fetch_row($this->db->query("
            SELECT * FROM user_daily_rewards WHERE udr_user = {$this->userid}
        "));
        
        return [
            'rewards' => $rewards,
            'current_day' => $user_data ? $user_data['udr_last_reward_day'] : 0,
            'streak' => $user_data ? $user_data['udr_streak'] : 0,
            'total_days' => $user_data ? $user_data['udr_total_days'] : 0,
            'best_streak' => $user_data ? $user_data['udr_best_streak'] : 0
        ];
    }
}

// Initialize system
$daily_rewards = new DailyRewardsSystem($db, $userid, $api);

// Handle claiming
if (isset($_POST['claim'])) {
    $status = $daily_rewards->checkDailyLogin();
    
    if ($status['can_claim']) {
        $result = $daily_rewards->claimReward($status['day']);
        if ($result['success']) {
            alert('success', "<i class='fas fa-gift'></i> Daily Reward Claimed!", $result['message'], false);
        } else {
            alert('danger', "Error", $result['message'], false);
        }
    } else {
        alert('warning', "Already Claimed", "You've already claimed today's reward!", false);
    }
}

// Get current status
$status = $daily_rewards->checkDailyLogin();
$calendar = $daily_rewards->getCalendarData();

?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-success text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h2 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Daily Rewards</h2>
                            <p class="mb-0 mt-2">Login every day to earn rewards and build your streak!</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="d-inline-block text-center mx-2">
                                <h3 class="mb-0">🔥 <?php echo $calendar['streak']; ?></h3>
                                <small>Current Streak</small>
                            </div>
                            <div class="d-inline-block text-center mx-2">
                                <h3 class="mb-0">⭐ <?php echo $calendar['best_streak']; ?></h3>
                                <small>Best Streak</small>
                            </div>
                            <div class="d-inline-block text-center mx-2">
                                <h3 class="mb-0">📅 <?php echo $calendar['total_days']; ?></h3>
                                <small>Total Days</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Today's Reward -->
    <?php if ($status['can_claim']): ?>
    <div class="row mb-4">
        <div class="col-md-6 mx-auto">
            <div class="card border-success shadow-lg">
                <div class="card-header bg-success text-white text-center">
                    <h4>Today's Reward - Day <?php echo $status['day']; ?></h4>
                </div>
                <div class="card-body text-center">
                    <?php 
                    $today_reward = $calendar['rewards'][$status['day']];
                    ?>
                    <div class="reward-icon mb-3" style="font-size: 4rem; color: #28a745;">
                        <i class="fas <?php echo $today_reward['dr_icon']; ?>"></i>
                    </div>
                    <h5><?php echo $today_reward['dr_description']; ?></h5>
                    <form method="POST" class="mt-3">
                        <input type="hidden" name="claim" value="1">
                        <?php echo getHtmlCSRF('daily_reward'); ?>
                        <button type="submit" class="btn btn-success btn-lg pulse-animation">
                            <i class="fas fa-gift"></i> Claim Reward!
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php elseif (isset($status['already_claimed'])): ?>
    <div class="row mb-4">
        <div class="col-md-6 mx-auto">
            <div class="card border-secondary">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Today's reward already claimed!</h5>
                    <p>Come back tomorrow to continue your streak!</p>
                    <small class="text-muted">Next reward in: <span id="countdown"></span></small>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Calendar Grid -->
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-calendar"></i> 30-Day Reward Cycle</h4>
            <small class="text-muted">Complete the cycle and start over with bigger rewards!</small>
        </div>
        <div class="card-body">
            <div class="row">
                <?php 
                for ($day = 1; $day <= 30; $day++):
                    $reward = $calendar['rewards'][$day];
                    $is_claimed = $day <= $calendar['current_day'];
                    $is_today = $day == $calendar['current_day'] && !$status['can_claim'];
                    $is_next = $day == $status['day'] && $status['can_claim'];
                ?>
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-3">
                    <div class="card h-100 reward-day <?php 
                        echo $is_claimed ? 'claimed' : '';
                        echo $is_today ? ' today' : '';
                        echo $is_next ? ' next' : '';
                    ?>">
                        <div class="card-body text-center p-2">
                            <div class="day-number">Day <?php echo $day; ?></div>
                            <div class="reward-icon my-2" style="font-size: 1.5rem;">
                                <i class="fas <?php echo $reward['dr_icon']; ?>"></i>
                            </div>
                            <small class="reward-desc"><?php echo $reward['dr_description']; ?></small>
                            <?php if ($is_claimed): ?>
                                <div class="claimed-badge">✓</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
    
    <!-- Streak Milestones -->
    <div class="card mt-4">
        <div class="card-header">
            <h4><i class="fas fa-fire"></i> Streak Milestones</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <?php
                $milestones = $db->query("SELECT * FROM streak_rewards ORDER BY sr_days");
                while ($milestone = $db->fetch_row($milestones)):
                    $achieved = $calendar['best_streak'] >= $milestone['sr_days'];
                ?>
                <div class="col-md-4 col-lg-3 mb-3">
                    <div class="card h-100 <?php echo $achieved ? 'border-success' : 'border-secondary'; ?>">
                        <div class="card-body text-center">
                            <i class="fas <?php echo $milestone['sr_icon']; ?> <?php echo $achieved ? 'text-success' : 'text-muted'; ?>" style="font-size: 2rem;"></i>
                            <h6 class="mt-2"><?php echo $milestone['sr_days']; ?>-Day Streak</h6>
                            <small><?php echo $milestone['sr_description']; ?></small>
                            <?php if ($achieved): ?>
                                <div class="text-success mt-2"><i class="fas fa-check"></i> Achieved!</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<style>
.reward-day {
    transition: all 0.3s ease;
    position: relative;
    cursor: default;
}
.reward-day.claimed {
    background: #e8f5e9;
    opacity: 0.8;
}
.reward-day.today {
    border: 2px solid #28a745;
    box-shadow: 0 0 10px rgba(40, 167, 69, 0.3);
}
.reward-day.next {
    border: 2px solid #ffc107;
    animation: pulse 2s infinite;
}
.claimed-badge {
    position: absolute;
    top: 5px;
    right: 5px;
    color: #28a745;
    font-size: 1.2rem;
    font-weight: bold;
}
.day-number {
    font-weight: bold;
    color: #6c757d;
    font-size: 0.9rem;
}
.reward-desc {
    display: block;
    font-size: 0.75rem;
}
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
.pulse-animation {
    animation: pulse 2s infinite;
}
</style>

<script>
// Countdown timer
function updateCountdown() {
    const now = new Date();
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    tomorrow.setHours(0, 0, 0, 0);
    
    const diff = tomorrow - now;
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
    
    const countdown = document.getElementById('countdown');
    if (countdown) {
        countdown.textContent = `${hours}h ${minutes}m ${seconds}s`;
    }
}

setInterval(updateCountdown, 1000);
updateCountdown();
</script>

<?php
$h->endpage();
?>