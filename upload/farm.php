<?php
/*
    File: farm.php
    Created: Farming System
    Info: Plant crops, manage fields, and harvest resources
*/
require_once('globals.php');

class FarmingSystem {
    private $db;
    private $userid;
    private $api;
    public $config;
    
    public function __construct($db, $userid, $api) {
        $this->db = $db;
        $this->userid = $userid;
        $this->api = $api;
        
        // Farming configuration
        $this->config = [
            'startingFields' => 2,
            'maxFields' => 12,
            'wellnessPerTend' => 5,
            'wellnessPerHarvest' => 20,
            'wellnessPerPlant' => 10,
            'waterPerTend' => 1,
            'fieldCost' => 50000,
            'wellCost' => 10000
        ];
        
        // Initialize user farming data
        $this->initializeFarmer();
    }
    
    /**
     * Initialize farming data for user
     */
    private function initializeFarmer() {
        $check = $this->db->query("SELECT * FROM farm_users WHERE userid = {$this->userid}");
        if ($this->db->num_rows($check) == 0) {
            $this->db->query("
                INSERT INTO farm_users (userid, farm_level, farm_xp, xp_needed, farm_water_available, farm_water_max)
                VALUES ({$this->userid}, 1, 0, 100, 0, 0)
            ");
        }
    }
    
    /**
     * Get user's farming data
     */
    public function getFarmData() {
        return $this->db->fetch_row($this->db->query("
            SELECT * FROM farm_users WHERE userid = {$this->userid}
        "));
    }
    
    /**
     * Get user's fields
     */
    public function getFields() {
        $fields = [];
        $query = $this->db->query("
            SELECT f.*, c.crop_name, c.crop_icon, c.grow_time, c.sell_price, c.xp_reward
            FROM farm_fields f
            LEFT JOIN farm_crops c ON f.crop_id = c.crop_id
            WHERE f.userid = {$this->userid}
            ORDER BY f.field_id
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            // Calculate growth progress
            if ($row['planted_at'] > 0 && $row['crop_id'] > 0) {
                $elapsed = time() - $row['planted_at'];
                $row['growth_percent'] = min(100, ($elapsed / $row['grow_time']) * 100);
                $row['time_remaining'] = max(0, $row['grow_time'] - $elapsed);
                $row['can_harvest'] = $row['growth_percent'] >= 100;
            } else {
                $row['growth_percent'] = 0;
                $row['time_remaining'] = 0;
                $row['can_harvest'] = false;
            }
            
            $fields[] = $row;
        }
        
        return $fields;
    }
    
    /**
     * Get available crops
     */
    public function getCrops() {
        $farmData = $this->getFarmData();
        $crops = [];
        
        $query = $this->db->query("
            SELECT * FROM farm_crops 
            WHERE level_required <= {$farmData['farm_level']}
            ORDER BY level_required, crop_name
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            $crops[] = $row;
        }
        
        return $crops;
    }
    
    /**
     * Build well
     */
    public function buildWell() {
        global $ir;
        
        $farmData = $this->getFarmData();
        
        if ($farmData['farm_water_max'] > 0) {
            return ['success' => false, 'message' => 'You already have a well!'];
        }
        
        if ($ir['primary_currency'] < $this->config['wellCost']) {
            return ['success' => false, 'message' => 'You need ' . number_format($this->config['wellCost']) . ' gold to build a well!'];
        }
        
        // Build well
        $this->db->query("
            UPDATE farm_users 
            SET farm_water_available = 50, farm_water_max = 50 
            WHERE userid = {$this->userid}
        ");
        
        $this->db->query("
            UPDATE users 
            SET primary_currency = primary_currency - {$this->config['wellCost']} 
            WHERE userid = {$this->userid}
        ");
        
        // Add starting fields
        for ($i = 0; $i < $this->config['startingFields']; $i++) {
            $this->db->query("
                INSERT INTO farm_fields (userid, field_status)
                VALUES ({$this->userid}, 'empty')
            ");
        }
        
        return ['success' => true, 'message' => 'Well built! You received ' . $this->config['startingFields'] . ' starting fields.'];
    }
    
    /**
     * Buy new field
     */
    public function buyField() {
        global $ir;
        
        $fieldCount = $this->db->fetch_single($this->db->query("
            SELECT COUNT(*) FROM farm_fields WHERE userid = {$this->userid}
        "));
        
        if ($fieldCount >= $this->config['maxFields']) {
            return ['success' => false, 'message' => 'You have reached the maximum number of fields!'];
        }
        
        $cost = $this->config['fieldCost'] * ($fieldCount + 1);
        
        if ($ir['primary_currency'] < $cost) {
            return ['success' => false, 'message' => 'You need ' . number_format($cost) . ' gold!'];
        }
        
        // Buy field
        $this->db->query("
            INSERT INTO farm_fields (userid, field_status)
            VALUES ({$this->userid}, 'empty')
        ");
        
        $this->db->query("
            UPDATE users 
            SET primary_currency = primary_currency - {$cost} 
            WHERE userid = {$this->userid}
        ");
        
        return ['success' => true, 'message' => 'New field purchased!'];
    }
    
    /**
     * Fill well with water
     */
    public function fillWell() {
        global $ir;
        
        $farmData = $this->getFarmData();
        $waterNeeded = $farmData['farm_water_max'] - $farmData['farm_water_available'];
        $cost = $waterNeeded * 100;
        
        if ($waterNeeded <= 0) {
            return ['success' => false, 'message' => 'Your well is already full!'];
        }
        
        if ($ir['primary_currency'] < $cost) {
            return ['success' => false, 'message' => 'You need ' . number_format($cost) . ' gold to fill your well!'];
        }
        
        $this->db->query("
            UPDATE farm_users 
            SET farm_water_available = farm_water_max 
            WHERE userid = {$this->userid}
        ");
        
        $this->db->query("
            UPDATE users 
            SET primary_currency = primary_currency - {$cost} 
            WHERE userid = {$this->userid}
        ");
        
        return ['success' => true, 'message' => 'Well filled with water!'];
    }
    
    /**
     * Plant crop in field
     */
    public function plantCrop($field_id, $crop_id) {
        global $ir;
        
        $farmData = $this->getFarmData();
        
        // Verify field ownership
        $field = $this->db->fetch_row($this->db->query("
            SELECT * FROM farm_fields WHERE field_id = {$field_id} AND userid = {$this->userid}
        "));
        
        if (!$field) {
            return ['success' => false, 'message' => 'Invalid field!'];
        }
        
        if ($field['field_status'] != 'empty') {
            return ['success' => false, 'message' => 'Field is not empty!'];
        }
        
        // Get crop info
        $crop = $this->db->fetch_row($this->db->query("
            SELECT * FROM farm_crops WHERE crop_id = {$crop_id}
        "));
        
        if (!$crop) {
            return ['success' => false, 'message' => 'Invalid crop!'];
        }
        
        if ($crop['level_required'] > $farmData['farm_level']) {
            return ['success' => false, 'message' => 'Your farming level is too low!'];
        }
        
        if ($ir['primary_currency'] < $crop['seed_cost']) {
            return ['success' => false, 'message' => 'You need ' . number_format($crop['seed_cost']) . ' gold for seeds!'];
        }
        
        if ($ir['energy'] < $this->config['wellnessPerPlant']) {
            return ['success' => false, 'message' => 'You need ' . $this->config['wellnessPerPlant'] . ' energy!'];
        }
        
        // Plant crop
        $this->db->query("
            UPDATE farm_fields 
            SET crop_id = {$crop_id}, 
                field_status = 'growing', 
                planted_at = " . time() . ",
                health = 100
            WHERE field_id = {$field_id}
        ");
        
        $this->db->query("
            UPDATE users 
            SET primary_currency = primary_currency - {$crop['seed_cost']},
                energy = energy - {$this->config['wellnessPerPlant']}
            WHERE userid = {$this->userid}
        ");
        
        // Add XP
        $this->addFarmingXP(5);
        
        return ['success' => true, 'message' => "Planted {$crop['crop_name']}!"];
    }
    
    /**
     * Tend to field
     */
    public function tendField($field_id) {
        global $ir;
        
        $farmData = $this->getFarmData();
        
        // Verify field
        $field = $this->db->fetch_row($this->db->query("
            SELECT * FROM farm_fields WHERE field_id = {$field_id} AND userid = {$this->userid}
        "));
        
        if (!$field || $field['field_status'] != 'growing') {
            return ['success' => false, 'message' => 'Cannot tend this field!'];
        }
        
        if ($farmData['farm_water_available'] < $this->config['waterPerTend']) {
            return ['success' => false, 'message' => 'Not enough water!'];
        }
        
        if ($ir['energy'] < $this->config['wellnessPerTend']) {
            return ['success' => false, 'message' => 'Not enough energy!'];
        }
        
        // Tend field
        $this->db->query("
            UPDATE farm_fields 
            SET health = LEAST(100, health + 20),
                last_tended = " . time() . "
            WHERE field_id = {$field_id}
        ");
        
        $this->db->query("
            UPDATE farm_users 
            SET farm_water_available = farm_water_available - {$this->config['waterPerTend']}
            WHERE userid = {$this->userid}
        ");
        
        $this->db->query("
            UPDATE users 
            SET energy = energy - {$this->config['wellnessPerTend']}
            WHERE userid = {$this->userid}
        ");
        
        // Add XP
        $this->addFarmingXP(2);
        
        return ['success' => true, 'message' => 'Field tended successfully!'];
    }
    
    /**
     * Harvest crop
     */
    public function harvestCrop($field_id) {
        global $ir;
        
        // Verify field
        $field = $this->db->fetch_row($this->db->query("
            SELECT f.*, c.crop_name, c.sell_price, c.xp_reward, c.grow_time, c.item_id
            FROM farm_fields f
            INNER JOIN farm_crops c ON f.crop_id = c.crop_id
            WHERE f.field_id = {$field_id} AND f.userid = {$this->userid}
        "));
        
        if (!$field || $field['field_status'] != 'growing') {
            return ['success' => false, 'message' => 'Cannot harvest this field!'];
        }
        
        // Check if ready
        $elapsed = time() - $field['planted_at'];
        if ($elapsed < $field['grow_time']) {
            return ['success' => false, 'message' => 'Crop is not ready yet!'];
        }
        
        if ($ir['energy'] < $this->config['wellnessPerHarvest']) {
            return ['success' => false, 'message' => 'Not enough energy!'];
        }
        
        // Calculate yield based on field health
        $yield_multiplier = $field['health'] / 100;
        $base_yield = rand(1, 3);
        $final_yield = ceil($base_yield * $yield_multiplier);
        
        // Give rewards
        if ($field['item_id'] > 0) {
            // Give item
            $this->api->UserGiveItem($this->userid, $field['item_id'], $final_yield);
            $reward_text = "{$final_yield}x {$field['crop_name']}";
        } else {
            // Give gold
            $gold_reward = $field['sell_price'] * $final_yield;
            $this->db->query("
                UPDATE users 
                SET primary_currency = primary_currency + {$gold_reward}
                WHERE userid = {$this->userid}
            ");
            $reward_text = number_format($gold_reward) . " gold";
        }
        
        // Clear field
        $this->db->query("
            UPDATE farm_fields 
            SET crop_id = 0, 
                field_status = 'empty', 
                planted_at = 0,
                health = 100,
                last_tended = 0
            WHERE field_id = {$field_id}
        ");
        
        $this->db->query("
            UPDATE users 
            SET energy = energy - {$this->config['wellnessPerHarvest']}
            WHERE userid = {$this->userid}
        ");
        
        // Add XP
        $this->addFarmingXP($field['xp_reward']);
        
        return ['success' => true, 'message' => "Harvested {$field['crop_name']}! Received {$reward_text}."];
    }
    
    /**
     * Add farming XP and handle level up
     */
    private function addFarmingXP($amount) {
        $farmData = $this->getFarmData();
        
        // Ensure xp_needed exists
        if (!isset($farmData['xp_needed'])) {
            $farmData['xp_needed'] = $farmData['farm_level'] * 100;
        }
        
        $newXP = $farmData['farm_xp'] + $amount;
        
        // Check for level up
        if ($newXP >= $farmData['xp_needed']) {
            $newLevel = $farmData['farm_level'] + 1;
            $newXPNeeded = $newLevel * 100;
            $leftoverXP = $newXP - $farmData['xp_needed'];
            
            $this->db->query("
                UPDATE farm_users 
                SET farm_level = {$newLevel},
                    farm_xp = {$leftoverXP},
                    xp_needed = {$newXPNeeded},
                    farm_water_max = farm_water_max + 10
                WHERE userid = {$this->userid}
            ");
            
            // Notification
            $this->api->SystemLogsAdd($this->userid, 'farming', "Reached Farming Level {$newLevel}!");
        } else {
            $this->db->query("
                UPDATE farm_users 
                SET farm_xp = {$newXP}
                WHERE userid = {$this->userid}
            ");
        }
    }
}

// Check if v3.2 is installed
$v32_check = $db->query("SELECT setting_value FROM settings WHERE setting_name = 'db_version' LIMIT 1");
$db_version = null;
if ($db->num_rows($v32_check) > 0) {
    $db_version = $db->fetch_single($v32_check);
}

// Check if tables exist
$tables_exist = true;
$check_tables = ['farm_users', 'farm_fields', 'farm_crops'];
foreach ($check_tables as $table) {
    $check = $db->query("SHOW TABLES LIKE '{$table}'");
    if ($db->num_rows($check) == 0) {
        $tables_exist = false;
        break;
    }
}

// If tables don't exist or version is less than 3.2, show upgrade message
if (!$tables_exist || ($db_version && version_compare($db_version, '3.2.0', '<'))) {
    ?>
    <div class="container-fluid">
        <div class="alert alert-warning">
            <h4><i class="fas fa-exclamation-triangle"></i> Feature Not Available</h4>
            <p>The Farming System requires Chivalry Engine v3.2.0 or higher.</p>
            <?php if ($userid == 1): ?>
                <p>Please run the database update to install this feature.</p>
                <a href="uplift_check.php" class="btn btn-primary">
                    <i class="fas fa-download"></i> Run Database Update
                </a>
            <?php else: ?>
                <p>Please contact an administrator to update the game.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php
    $h->endpage();
    exit;
}

if (!$tables_exist) {
    // Create tables
    $db->query("
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
    
    $db->query("
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
    
    $db->query("
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
    
    // Add sample crops
    $db->query("
        INSERT IGNORE INTO `farm_crops` 
        (`crop_name`, `crop_icon`, `level_required`, `seed_cost`, `sell_price`, `grow_time`, `xp_reward`) VALUES
        ('Wheat', '🌾', 1, 100, 200, 300, 5),
        ('Corn', '🌽', 2, 250, 500, 600, 10),
        ('Tomatoes', '🍅', 3, 500, 1000, 900, 15),
        ('Potatoes', '🥔', 4, 750, 1500, 1200, 20),
        ('Carrots', '🥕', 5, 1000, 2000, 1500, 25),
        ('Pumpkins', '🎃', 6, 1500, 3000, 1800, 30),
        ('Grapes', '🍇', 7, 2000, 4000, 2400, 35),
        ('Strawberries', '🍓', 8, 3000, 6000, 3000, 40),
        ('Watermelons', '🍉', 10, 5000, 10000, 3600, 50)
    ");
}

// Initialize system
$farm_system = new FarmingSystem($db, $userid, $api);

// Handle actions
if (isset($_POST['action'])) {
    $result = null;
    
    // Debug info (remove in production)
    error_log("Farm action: " . $_POST['action']);
    
    switch($_POST['action']) {
        case 'build_well':
            $result = $farm_system->buildWell();
            break;
            
        case 'buy_field':
            $result = $farm_system->buyField();
            break;
            
        case 'fill_well':
            $result = $farm_system->fillWell();
            break;
            
        case 'plant':
            $field_id = isset($_POST['field_id']) ? abs((int)$_POST['field_id']) : 0;
            $crop_id = isset($_POST['crop_id']) ? abs((int)$_POST['crop_id']) : 0;
            
            // Validate input
            if ($field_id == 0 || $crop_id == 0) {
                $result = ['success' => false, 'message' => 'Please select a valid field and crop!'];
            } else {
                $result = $farm_system->plantCrop($field_id, $crop_id);
            }
            break;
            
        case 'tend':
            $field_id = isset($_POST['field_id']) ? abs((int)$_POST['field_id']) : 0;
            if ($field_id == 0) {
                $result = ['success' => false, 'message' => 'Invalid field!'];
            } else {
                $result = $farm_system->tendField($field_id);
            }
            break;
            
        case 'harvest':
            $field_id = isset($_POST['field_id']) ? abs((int)$_POST['field_id']) : 0;
            if ($field_id == 0) {
                $result = ['success' => false, 'message' => 'Invalid field!'];
            } else {
                $result = $farm_system->harvestCrop($field_id);
            }
            break;
            
        default:
            $result = ['success' => false, 'message' => 'Invalid action!'];
            break;
    }
    
    if ($result) {
        alert($result['success'] ? 'success' : 'danger',
              $result['success'] ? 'Success!' : 'Failed!',
              $result['message'], false);
    }
}

// Get data
$farmData = $farm_system->getFarmData();
// Ensure xp_needed field exists
if (!isset($farmData['xp_needed'])) {
    $farmData['xp_needed'] = $farmData['farm_level'] * 100;
}
$fields = $farm_system->getFields();
$crops = $farm_system->getCrops();

?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-success text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h2 class="mb-0"><i class="fas fa-tractor me-2"></i>Farming</h2>
                            <p class="mb-0 mt-2">Plant crops, tend your fields, and harvest your bounty!</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="d-inline-block text-center mx-2">
                                <h4 class="mb-0">Level <?php echo $farmData['farm_level']; ?></h4>
                                <small>Farming Level</small>
                            </div>
                            <div class="d-inline-block text-center mx-2">
                                <h4 class="mb-0"><?php echo number_format($farmData['farm_xp']); ?>/<?php echo number_format($farmData['xp_needed']); ?></h4>
                                <small>Experience</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($farmData['farm_water_max'] == 0): ?>
        <!-- Need to build well -->
        <div class="alert alert-info">
            <h4><i class="fas fa-info-circle"></i> Build Your Well</h4>
            <p>You need to build a well before you can start farming. This will cost <?php echo number_format($farm_system->config['wellCost']); ?> gold.</p>
            <form method="POST">
                <input type="hidden" name="action" value="build_well">
                <?php echo getHtmlCSRF('farm_well'); ?>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-hammer"></i> Build Well (<?php echo number_format($farm_system->config['wellCost']); ?> gold)
                </button>
            </form>
        </div>
    <?php else: ?>
        
        <!-- Water Management -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-tint"></i> Water Supply</h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-info" 
                                 style="width: <?php echo ($farmData['farm_water_available'] / $farmData['farm_water_max']) * 100; ?>%">
                                <?php echo $farmData['farm_water_available']; ?> / <?php echo $farmData['farm_water_max']; ?> Buckets
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="action" value="fill_well">
                            <?php echo getHtmlCSRF('farm_fill'); ?>
                            <button type="submit" class="btn btn-info btn-sm">
                                <i class="fas fa-fill"></i> Fill Well
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Crop Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Available Crops</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Crop</th>
                                <th>Level</th>
                                <th>Cost</th>
                                <th>Time</th>
                                <th>Sells For</th>
                                <th>XP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($crops as $crop): ?>
                            <tr class="<?php echo $crop['level_required'] > $farmData['farm_level'] ? 'text-muted' : ''; ?>">
                                <td><?php echo $crop['crop_icon']; ?> <?php echo $crop['crop_name']; ?></td>
                                <td><?php echo $crop['level_required']; ?></td>
                                <td><?php echo number_format($crop['seed_cost']); ?>g</td>
                                <td><?php echo round($crop['grow_time'] / 60); ?>m</td>
                                <td><?php echo number_format($crop['sell_price']); ?>g</td>
                                <td><?php echo $crop['xp_reward']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Fields -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-seedling"></i> Your Fields</h5>
                <?php if (count($fields) < $farm_system->config['maxFields']): ?>
                <form method="POST" class="d-inline">
                    <input type="hidden" name="action" value="buy_field">
                    <?php echo getHtmlCSRF('farm_buy_field'); ?>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Buy New Field
                    </button>
                </form>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($fields as $field): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header">
                                Field #<?php echo $field['field_id']; ?>
                            </div>
                            <div class="card-body text-center">
                                <?php if ($field['field_status'] == 'empty'): ?>
                                    <div class="mb-3" style="font-size: 3rem;">🌱</div>
                                    <p>Empty Field</p>
                                    <form method="POST" class="text-center">
                                        <input type="hidden" name="action" value="plant">
                                        <input type="hidden" name="field_id" value="<?php echo $field['field_id']; ?>">
                                        <?php echo getHtmlCSRF('farm_plant_' . $field['field_id']); ?>
                                        
                                        <select name="crop_id" class="form-select form-select-sm mb-2" required>
                                            <option value="">-- Select Crop --</option>
                                            <?php foreach ($crops as $crop): ?>
                                                <?php if ($crop['level_required'] <= $farmData['farm_level']): ?>
                                                <option value="<?php echo $crop['crop_id']; ?>">
                                                    <?php echo $crop['crop_icon']; ?> <?php echo $crop['crop_name']; ?> (<?php echo number_format($crop['seed_cost']); ?>g)
                                                </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                        
                                        <button type="submit" class="btn btn-primary btn-sm w-100">
                                            <i class="fas fa-seedling"></i> Plant
                                        </button>
                                    </form>
                                    
                                <?php elseif ($field['field_status'] == 'growing'): ?>
                                    <div class="mb-2" style="font-size: 3rem;"><?php echo $field['crop_icon'] ?? '🌾'; ?></div>
                                    <h6><?php echo $field['crop_name']; ?></h6>
                                    
                                    <div class="progress mb-2" style="height: 20px;">
                                        <div class="progress-bar bg-success" 
                                             style="width: <?php echo $field['growth_percent']; ?>%">
                                            <?php echo round($field['growth_percent']); ?>%
                                        </div>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <small>Health: <?php echo $field['health']; ?>%</small>
                                    </div>
                                    
                                    <?php if ($field['can_harvest']): ?>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="harvest">
                                            <input type="hidden" name="field_id" value="<?php echo $field['field_id']; ?>">
                                            <?php echo getHtmlCSRF('farm_harvest'); ?>
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-hand-holding"></i> Harvest
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <p class="small mb-1">Ready in <?php echo round($field['time_remaining'] / 60); ?> min</p>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="tend">
                                            <input type="hidden" name="field_id" value="<?php echo $field['field_id']; ?>">
                                            <?php echo getHtmlCSRF('farm_tend'); ?>
                                            <button type="submit" class="btn btn-info btn-sm">
                                                <i class="fas fa-hand-holding-water"></i> Tend Field
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($fields)): ?>
                    <div class="col-12">
                        <p class="text-muted">You don't have any fields yet.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
    <?php endif; ?>
    
    <!-- Info -->
    <div class="card">
        <div class="card-body">
            <h5><i class="fas fa-info-circle"></i> Farming Guide</h5>
            <ul>
                <li>Plant crops in your fields using seeds</li>
                <li>Tend your fields regularly to maintain crop health</li>
                <li>Harvest crops when they're fully grown</li>
                <li>Higher farming levels unlock better crops</li>
                <li>Field health affects your harvest yield</li>
                <li>Water is required for tending fields</li>
            </ul>
        </div>
    </div>
</div>

<?php
$h->endpage();
?>