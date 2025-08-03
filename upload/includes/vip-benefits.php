<?php
/*
    File: vip-benefits.php
    Created: VIP benefits system for Chivalry Engine
    Info: Centralized VIP benefits management
*/

class VIPBenefits {
    private $db;
    private $userid;
    private $userVIPDays;
    
    public function __construct($database, $user_id) {
        $this->db = $database;
        $this->userid = $user_id;
        
        // Get user's VIP status
        $result = $this->db->fetch_row($this->db->query("SELECT vip_days FROM users WHERE userid = {$user_id}"));
        $this->userVIPDays = $result ? (int)$result['vip_days'] : 0;
    }
    
    /**
     * Check if user is VIP
     */
    public function isVIP() {
        return $this->userVIPDays > 0;
    }
    
    /**
     * Get VIP days remaining
     */
    public function getVIPDays() {
        return $this->userVIPDays;
    }
    
    /**
     * Apply VIP discount to shop prices
     */
    public function applyShopDiscount($price) {
        if ($this->isVIP()) {
            return floor($price * 0.9); // 10% discount for VIP
        }
        return $price;
    }
    
    /**
     * Apply VIP bonus to job income
     */
    public function applyJobIncomeBonus($income) {
        if ($this->isVIP()) {
            return floor($income * 1.5); // 50% bonus for VIP
        }
        return $income;
    }
    
    /**
     * Apply VIP bonus to training gains
     */
    public function applyTrainingBonus($gain) {
        if ($this->isVIP()) {
            return floor($gain * 1.25); // 25% bonus for VIP
        }
        return $gain;
    }
    
    /**
     * Apply VIP reduction to jail/hospital time
     */
    public function applyTimeReduction($time) {
        if ($this->isVIP()) {
            return floor($time * 0.75); // 25% reduction for VIP
        }
        return $time;
    }
    
    /**
     * Get VIP crime attempt bonus
     */
    public function getCrimeBonusAttempts() {
        return $this->isVIP() ? 5 : 0; // 5 extra crime attempts for VIP
    }
    
    /**
     * Apply VIP bonus to experience gains
     */
    public function applyExperienceBonus($xp) {
        if ($this->isVIP()) {
            return floor($xp * 1.25); // 25% XP bonus for VIP
        }
        return $xp;
    }
    
    /**
     * Get maximum energy bonus for VIP
     */
    public function getMaxEnergyBonus($baseMax) {
        if ($this->isVIP()) {
            return floor($baseMax * 1.1); // 10% bonus to max energy
        }
        return $baseMax;
    }
    
    /**
     * Get maximum HP bonus for VIP
     */
    public function getMaxHPBonus($baseMax) {
        if ($this->isVIP()) {
            return floor($baseMax * 1.1); // 10% bonus to max HP
        }
        return $baseMax;
    }
    
    /**
     * Check if user can access VIP-only features
     */
    public function canAccessVIPFeatures() {
        return $this->isVIP();
    }
    
    /**
     * Get VIP badge HTML
     */
    public function getVIPBadge() {
        if ($this->isVIP()) {
            return "<span class='badge bg-warning text-dark ms-1'><i class='fas fa-crown'></i> VIP ({$this->userVIPDays} days)</span>";
        }
        return "";
    }
    
    /**
     * Get VIP username styling
     */
    public function getVIPUsernameStyle($username) {
        if ($this->isVIP()) {
            return "<span class='text-warning fw-bold'>{$username} <i class='fas fa-crown text-warning' title='VIP Member - {$this->userVIPDays} days remaining'></i></span>";
        }
        return $username;
    }
    
    /**
     * Apply VIP market fee reduction
     */
    public function applyMarketFeeReduction($fee) {
        if ($this->isVIP()) {
            return floor($fee * 0.5); // 50% market fee reduction for VIP
        }
        return $fee;
    }
    
    /**
     * Get VIP benefits summary
     */
    public function getBenefitsSummary() {
        if (!$this->isVIP()) {
            return [];
        }
        
        return [
            'energy_regen' => '2x faster energy regeneration',
            'daily_bonus' => '2x daily login bonus',
            'bank_interest' => '3% daily interest (vs 2% regular)',
            'shop_discount' => '10% discount on all shop purchases',
            'job_income' => '50% bonus to job income',
            'training_bonus' => '25% bonus to training gains',
            'xp_bonus' => '25% bonus to experience gains',
            'max_stats' => '10% bonus to maximum HP and Energy',
            'time_reduction' => '25% reduction in jail/hospital time',
            'crime_attempts' => '5 extra crime attempts per day',
            'market_fees' => '50% reduction in market selling fees',
            'vip_styling' => 'Special VIP name colors and crown icon'
        ];
    }
    
    /**
     * Log VIP benefit usage for tracking
     */
    public function logBenefitUsage($benefit_type, $amount_saved = 0) {
        if ($this->isVIP()) {
            $benefit_type = $this->db->escape($benefit_type);
            $this->db->query("
                INSERT INTO vip_benefit_log (userid, benefit_type, amount_saved, used_at) 
                VALUES ({$this->userid}, '{$benefit_type}', {$amount_saved}, " . time() . ")
                ON DUPLICATE KEY UPDATE 
                    amount_saved = amount_saved + {$amount_saved}, 
                    used_at = " . time()
            );
        }
    }
    
    /**
     * Create VIP benefit log table if it doesn't exist
     */
    public function createBenefitLogTable() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `vip_benefit_log` (
                `log_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `userid` INT(11) UNSIGNED NOT NULL,
                `benefit_type` VARCHAR(50) NOT NULL,
                `amount_saved` INT(11) DEFAULT 0,
                `used_at` INT(11) NOT NULL,
                PRIMARY KEY (`log_id`),
                UNIQUE KEY `user_benefit` (`userid`, `benefit_type`),
                KEY `userid` (`userid`),
                KEY `used_at` (`used_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }
}

/**
 * Global VIP benefits helper function
 */
function getVIPBenefits($db, $userid) {
    static $vipInstances = [];
    
    if (!isset($vipInstances[$userid])) {
        $vipInstances[$userid] = new VIPBenefits($db, $userid);
    }
    
    return $vipInstances[$userid];
}

/**
 * Quick VIP check function
 */
function isUserVIP($db, $userid) {
    static $vipCache = [];
    
    if (!isset($vipCache[$userid])) {
        $result = $db->fetch_row($db->query("SELECT vip_days FROM users WHERE userid = {$userid}"));
        $vipCache[$userid] = $result && $result['vip_days'] > 0;
    }
    
    return $vipCache[$userid];
}

/**
 * Display VIP benefits page
 */
function displayVIPBenefits($vipBenefits) {
    $isVIP = $vipBenefits->isVIP();
    $benefits = $vipBenefits->getBenefitsSummary();
    
    echo "<div class='card mb-4'>";
    echo "<div class='card-header bg-warning text-dark'>";
    echo "<h4><i class='fas fa-crown'></i> VIP Membership Benefits</h4>";
    echo "</div>";
    echo "<div class='card-body'>";
    
    if ($isVIP) {
        echo "<div class='alert alert-success'>";
        echo "<h5><i class='fas fa-check-circle'></i> You are a VIP Member!</h5>";
        echo "<p>You have <strong>" . $vipBenefits->getVIPDays() . " VIP days</strong> remaining.</p>";
        echo "</div>";
    } else {
        echo "<div class='alert alert-info'>";
        echo "<h5><i class='fas fa-info-circle'></i> Become a VIP Member!</h5>";
        echo "<p><a href='donator.php' class='btn btn-warning'><i class='fas fa-crown'></i> Purchase VIP</a></p>";
        echo "</div>";
    }
    
    echo "<h5>VIP Benefits Include:</h5>";
    echo "<div class='row'>";
    
    $benefitsList = [
        'energy_regen' => ['icon' => 'fa-bolt', 'title' => 'Fast Energy Regen', 'desc' => '2x faster energy regeneration'],
        'daily_bonus' => ['icon' => 'fa-gift', 'title' => 'Double Daily Bonus', 'desc' => '2x daily login rewards'],
        'bank_interest' => ['icon' => 'fa-university', 'title' => 'Premium Interest', 'desc' => '3% daily bank interest'],
        'shop_discount' => ['icon' => 'fa-store', 'title' => 'Shop Discounts', 'desc' => '10% off all purchases'],
        'job_income' => ['icon' => 'fa-briefcase', 'title' => 'Job Income Boost', 'desc' => '50% bonus job income'],
        'training_bonus' => ['icon' => 'fa-dumbbell', 'title' => 'Training Boost', 'desc' => '25% faster stat gains'],
        'xp_bonus' => ['icon' => 'fa-star', 'title' => 'XP Bonus', 'desc' => '25% extra experience'],
        'max_stats' => ['icon' => 'fa-chart-line', 'title' => 'Stat Bonuses', 'desc' => '10% higher max HP/Energy'],
        'time_reduction' => ['icon' => 'fa-clock', 'title' => 'Time Reduction', 'desc' => '25% less jail/hospital time'],
        'crime_attempts' => ['icon' => 'fa-mask', 'title' => 'Extra Crimes', 'desc' => '5 additional crime attempts'],
        'market_fees' => ['icon' => 'fa-balance-scale', 'title' => 'Lower Fees', 'desc' => '50% reduced market fees'],
        'vip_styling' => ['icon' => 'fa-crown', 'title' => 'VIP Status', 'desc' => 'Special name colors and crown']
    ];
    
    foreach ($benefitsList as $key => $benefit) {
        $activeClass = $isVIP ? 'border-success' : 'border-secondary';
        $textClass = $isVIP ? 'text-success' : 'text-muted';
        
        echo "<div class='col-md-6 col-lg-4 mb-3'>";
        echo "<div class='card {$activeClass}' style='height: 100%;'>";
        echo "<div class='card-body text-center'>";
        echo "<i class='fas {$benefit['icon']} fa-2x {$textClass} mb-2'></i>";
        echo "<h6 class='card-title'>{$benefit['title']}</h6>";
        echo "<p class='card-text small'>{$benefit['desc']}</p>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
    
    echo "</div>";
    echo "</div>";
    echo "</div>";
}
?>