<?php
/*
    File: crafting.php
    Created: Crafting & Enchanting System
    Info: Craft items and enchant equipment
*/
require_once('globals.php');

class CraftingSystem {
    private $db;
    private $userid;
    private $api;
    
    public function __construct($db, $userid, $api) {
        $this->db = $db;
        $this->userid = $userid;
        $this->api = $api;
    }
    
    /**
     * Get user's crafting data
     */
    public function getUserCrafting() {
        $data = $this->db->fetch_row($this->db->query("
            SELECT * FROM user_crafting WHERE uc_user = {$this->userid}
        "));
        
        if (!$data) {
            // Initialize crafting for user
            $this->db->query("
                INSERT INTO user_crafting (uc_user, uc_skill_level, uc_experience)
                VALUES ({$this->userid}, 1, 0)
            ");
            
            $data = [
                'uc_skill_level' => 1,
                'uc_experience' => 0,
                'uc_items_crafted' => 0,
                'uc_legendary_crafted' => 0,
                'uc_recipes_learned' => ''
            ];
        }
        
        // Calculate next level requirements
        $data['next_level_exp'] = $data['uc_skill_level'] * 100;
        $data['exp_percent'] = min(100, ($data['uc_experience'] / $data['next_level_exp']) * 100);
        
        return $data;
    }
    
    /**
     * Get available recipes
     */
    public function getRecipes($category = null) {
        $user_crafting = $this->getUserCrafting();
        
        $where = "WHERE recipe_level_required <= {$user_crafting['uc_skill_level']}";
        if ($category) {
            $where .= " AND recipe_category = '{$category}'";
        }
        
        $recipes = [];
        $query = $this->db->query("
            SELECT r.*,
                   i.itmname as result_name,
                   i.itmtype,
                   (SELECT GROUP_CONCAT(CONCAT(rm.rm_quantity, 'x ', mi.itmname) SEPARATOR ', ')
                    FROM recipe_materials rm
                    INNER JOIN items mi ON rm.rm_item = mi.itmid
                    WHERE rm.rm_recipe = r.recipe_id) as materials_list
            FROM crafting_recipes r
            INNER JOIN items i ON r.recipe_result_item = i.itmid
            {$where}
            ORDER BY r.recipe_level_required, r.recipe_name
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            // Check if user can craft (has materials)
            $row['can_craft'] = $this->checkMaterials($row['recipe_id']);
            $recipes[] = $row;
        }
        
        return $recipes;
    }
    
    /**
     * Check if user has materials for recipe
     */
    private function checkMaterials($recipe_id) {
        global $ir;
        
        // Check gold cost
        $recipe = $this->db->fetch_row($this->db->query("
            SELECT recipe_gold_cost, recipe_energy_cost FROM crafting_recipes WHERE recipe_id = {$recipe_id}
        "));
        
        if ($ir['primary_currency'] < $recipe['recipe_gold_cost']) {
            return false;
        }
        
        if ($ir['energy'] < $recipe['recipe_energy_cost']) {
            return false;
        }
        
        // Check materials
        $materials = $this->db->query("
            SELECT rm_item, rm_quantity FROM recipe_materials WHERE rm_recipe = {$recipe_id}
        ");
        
        while ($mat = $this->db->fetch_row($materials)) {
            $has = $this->api->user->countItem($this->userid, $mat['rm_item']);
            if ($has < $mat['rm_quantity']) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Craft an item
     */
    public function craftItem($recipe_id) {
        global $ir;
        
        $recipe = $this->db->fetch_row($this->db->query("
            SELECT r.*, i.itmname 
            FROM crafting_recipes r
            INNER JOIN items i ON r.recipe_result_item = i.itmid
            WHERE r.recipe_id = {$recipe_id}
        "));
        
        if (!$recipe) {
            return ['success' => false, 'message' => 'Recipe not found!'];
        }
        
        $user_crafting = $this->getUserCrafting();
        
        // Check level requirement
        if ($user_crafting['uc_skill_level'] < $recipe['recipe_level_required']) {
            return ['success' => false, 'message' => "You need crafting level {$recipe['recipe_level_required']}!"];
        }
        
        // Check if can craft
        if (!$this->checkMaterials($recipe_id)) {
            return ['success' => false, 'message' => 'You do not have the required materials!'];
        }
        
        // Remove materials
        $materials = $this->db->query("
            SELECT rm_item, rm_quantity FROM recipe_materials WHERE rm_recipe = {$recipe_id}
        ");
        
        while ($mat = $this->db->fetch_row($materials)) {
            takeItem($this->userid, $mat['rm_item'], $mat['rm_quantity']);
        }
        
        // Deduct costs
        $this->db->query("
            UPDATE users 
            SET primary_currency = primary_currency - {$recipe['recipe_gold_cost']},
                energy = energy - {$recipe['recipe_energy_cost']}
            WHERE userid = {$this->userid}
        ");
        
        // Check success chance
        $success = rand(1, 100) <= $recipe['recipe_success_chance'];
        
        if ($success) {
            // Give item
            addItem($this->userid, $recipe['recipe_result_item'], $recipe['recipe_result_quantity']);
            
            // Gain experience
            $exp_gain = $recipe['recipe_level_required'] * 10;
            $this->db->query("
                UPDATE user_crafting 
                SET uc_experience = uc_experience + {$exp_gain},
                    uc_items_crafted = uc_items_crafted + 1
                WHERE uc_user = {$this->userid}
            ");
            
            // Check for level up
            $new_data = $this->getUserCrafting();
            if ($new_data['uc_experience'] >= $new_data['next_level_exp']) {
                $this->db->query("
                    UPDATE user_crafting 
                    SET uc_skill_level = uc_skill_level + 1,
                        uc_experience = uc_experience - {$new_data['next_level_exp']}
                    WHERE uc_user = {$this->userid}
                ");
                
                $message = "Success! Crafted {$recipe['recipe_result_quantity']}x {$recipe['itmname']} and LEVELED UP!";
            } else {
                $message = "Success! Crafted {$recipe['recipe_result_quantity']}x {$recipe['itmname']}. (+{$exp_gain} exp)";
            }
            
            return ['success' => true, 'message' => $message];
        } else {
            return ['success' => false, 'message' => 'Crafting failed! Materials were lost.'];
        }
    }
    
    /**
     * Get available enchantments
     */
    public function getEnchantments() {
        $enchantments = [];
        $query = $this->db->query("
            SELECT * FROM enchantments 
            ORDER BY ench_rarity, ench_name
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            $enchantments[] = $row;
        }
        
        return $enchantments;
    }
}

// Initialize system
$craft_system = new CraftingSystem($db, $userid, $api);

// Handle actions
if (isset($_POST['action'])) {
    $result = null;
    
    switch($_POST['action']) {
        case 'craft':
            $recipe_id = abs((int)$_POST['recipe_id']);
            $result = $craft_system->craftItem($recipe_id);
            break;
    }
    
    if ($result) {
        alert($result['success'] ? 'success' : 'danger',
              $result['success'] ? 'Success!' : 'Failed!',
              $result['message'], false);
    }
}

// Get data
$user_crafting = $craft_system->getUserCrafting();
$selected_category = $_GET['cat'] ?? 'weapon';
$recipes = $craft_system->getRecipes($selected_category);
$enchantments = $craft_system->getEnchantments();

?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-warning text-dark">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h2 class="mb-0"><i class="fas fa-hammer me-2"></i>Crafting & Enchanting</h2>
                            <p class="mb-0 mt-2">Create powerful items and enhance your equipment!</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="d-inline-block text-center mx-2">
                                <h4 class="mb-0"><?php echo $user_crafting['uc_skill_level']; ?></h4>
                                <small>Crafting Level</small>
                            </div>
                            <div class="d-inline-block text-center mx-2">
                                <h4 class="mb-0"><?php echo $user_crafting['uc_items_crafted']; ?></h4>
                                <small>Items Crafted</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Crafting Progress -->
    <div class="card mb-4">
        <div class="card-body">
            <h5>Crafting Experience</h5>
            <div class="progress">
                <div class="progress-bar bg-warning" style="width: <?php echo $user_crafting['exp_percent']; ?>%">
                    <?php echo $user_crafting['uc_experience']; ?>/<?php echo $user_crafting['next_level_exp']; ?> XP
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#recipes">
                <i class="fas fa-scroll"></i> Recipes
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#enchanting">
                <i class="fas fa-magic"></i> Enchanting
            </a>
        </li>
    </ul>
    
    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Recipes Tab -->
        <div class="tab-pane fade show active" id="recipes">
            <!-- Category Filter -->
            <div class="btn-group mb-3" role="group">
                <a href="?cat=weapon" class="btn btn-sm btn-<?php echo $selected_category == 'weapon' ? 'primary' : 'outline-primary'; ?>">
                    <i class="fas fa-sword"></i> Weapons
                </a>
                <a href="?cat=armor" class="btn btn-sm btn-<?php echo $selected_category == 'armor' ? 'primary' : 'outline-primary'; ?>">
                    <i class="fas fa-shield-alt"></i> Armor
                </a>
                <a href="?cat=consumable" class="btn btn-sm btn-<?php echo $selected_category == 'consumable' ? 'primary' : 'outline-primary'; ?>">
                    <i class="fas fa-flask"></i> Consumables
                </a>
                <a href="?cat=material" class="btn btn-sm btn-<?php echo $selected_category == 'material' ? 'primary' : 'outline-primary'; ?>">
                    <i class="fas fa-gem"></i> Materials
                </a>
            </div>
            
            <!-- Recipes Grid -->
            <div class="row">
                <?php if (empty($recipes)): ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            No recipes available in this category at your level.
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($recipes as $recipe): ?>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><?php echo $recipe['recipe_name']; ?></h6>
                                    <span class="badge bg-secondary">Lvl <?php echo $recipe['recipe_level_required']; ?></span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <strong>Result:</strong> 
                                    <?php echo $recipe['recipe_result_quantity']; ?>x <?php echo $recipe['result_name']; ?>
                                </div>
                                
                                <div class="mb-2">
                                    <strong>Materials:</strong><br>
                                    <small class="text-muted"><?php echo $recipe['materials_list'] ?: 'None'; ?></small>
                                </div>
                                
                                <div class="mb-2">
                                    <strong>Cost:</strong> 
                                    <?php echo number_format($recipe['recipe_gold_cost']); ?> gold, 
                                    <?php echo $recipe['recipe_energy_cost']; ?> energy
                                </div>
                                
                                <?php if ($recipe['recipe_success_chance'] < 100): ?>
                                <div class="mb-2">
                                    <strong>Success Rate:</strong> 
                                    <span class="text-<?php echo $recipe['recipe_success_chance'] >= 75 ? 'success' : ($recipe['recipe_success_chance'] >= 50 ? 'warning' : 'danger'); ?>">
                                        <?php echo $recipe['recipe_success_chance']; ?>%
                                    </span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($recipe['can_craft']): ?>
                                    <form method="POST">
                                        <input type="hidden" name="action" value="craft">
                                        <input type="hidden" name="recipe_id" value="<?php echo $recipe['recipe_id']; ?>">
                                        <?php echo getHtmlCSRF('craft_item'); ?>
                                        <button type="submit" class="btn btn-success btn-sm w-100">
                                            <i class="fas fa-hammer"></i> Craft
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm w-100" disabled>
                                        <i class="fas fa-times"></i> Missing Materials
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Enchanting Tab -->
        <div class="tab-pane fade" id="enchanting">
            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle"></i> Enchanting allows you to add magical properties to your equipment!
            </div>
            
            <div class="row">
                <?php foreach ($enchantments as $ench): 
                    $rarity_colors = [
                        'common' => 'secondary',
                        'uncommon' => 'success',
                        'rare' => 'primary',
                        'epic' => 'purple',
                        'legendary' => 'warning'
                    ];
                    $color = $rarity_colors[$ench['ench_rarity']] ?? 'secondary';
                ?>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card h-100 border-<?php echo $color; ?>">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><?php echo $ench['ench_name']; ?></h6>
                                <span class="badge bg-<?php echo $color; ?>">
                                    <?php echo ucfirst($ench['ench_rarity']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="small"><?php echo $ench['ench_desc']; ?></p>
                            
                            <div class="mb-2">
                                <strong>Effect:</strong> +<?php echo $ench['ench_value']; ?> <?php echo str_replace('_', ' ', $ench['ench_stat']); ?>
                            </div>
                            
                            <div class="mb-2">
                                <strong>Type:</strong> <?php echo ucfirst($ench['ench_type']); ?>
                            </div>
                            
                            <div class="mb-2">
                                <strong>Cost:</strong> <?php echo number_format($ench['ench_cost']); ?> gold
                            </div>
                            
                            <button class="btn btn-sm btn-outline-<?php echo $color; ?> w-100" disabled>
                                <i class="fas fa-magic"></i> Coming Soon
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
.bg-purple { background-color: #6f42c1 !important; }
.border-purple { border-color: #6f42c1 !important; }
.btn-outline-purple { 
    color: #6f42c1; 
    border-color: #6f42c1; 
}
.btn-outline-purple:hover { 
    background-color: #6f42c1; 
    color: white; 
}
</style>

<?php
$h->endpage();
?>