<?php
/*
    File: pets.php
    Created: Pet/Companion System
    Info: Manage, train, and battle with your pets
*/
require_once('globals.php');

class PetSystem {
    private $db;
    private $userid;
    private $api;
    
    public function __construct($db, $userid, $api) {
        $this->db = $db;
        $this->userid = $userid;
        $this->api = $api;
    }
    
    /**
     * Get user's pets
     */
    public function getUserPets() {
        $pets = [];
        $query = $this->db->query("
            SELECT up.*, p.*
            FROM user_pets up
            INNER JOIN pets p ON up.up_pet = p.pet_id
            WHERE up.up_user = {$this->userid}
            ORDER BY up.up_active DESC, up.up_level DESC
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            // Calculate next level experience
            $row['next_level_exp'] = $row['up_level'] * 100;
            $row['exp_percent'] = min(100, ($row['up_experience'] / $row['next_level_exp']) * 100);
            $pets[] = $row;
        }
        
        return $pets;
    }
    
    /**
     * Get available pets in shop
     */
    public function getShopPets() {
        $pets = [];
        $query = $this->db->query("
            SELECT * FROM pets 
            WHERE pet_rarity IN ('common', 'uncommon')
            ORDER BY pet_rarity, pet_name
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            // Calculate price based on rarity
            $prices = [
                'common' => 5000,
                'uncommon' => 15000,
                'rare' => 50000,
                'epic' => 150000,
                'legendary' => 500000,
                'mythic' => 1000000
            ];
            $row['price'] = $prices[$row['pet_rarity']];
            $pets[] = $row;
        }
        
        return $pets;
    }
    
    /**
     * Buy a pet
     */
    public function buyPet($pet_id) {
        global $ir;
        
        $pet = $this->db->fetch_row($this->db->query("
            SELECT * FROM pets WHERE pet_id = {$pet_id}
        "));
        
        if (!$pet) {
            return ['success' => false, 'message' => 'Pet not found!'];
        }
        
        // Calculate price
        $prices = [
            'common' => 5000,
            'uncommon' => 15000,
            'rare' => 50000,
            'epic' => 150000,
            'legendary' => 500000,
            'mythic' => 1000000
        ];
        $price = $prices[$pet['pet_rarity']];
        
        if ($ir['primary_currency'] < $price) {
            return ['success' => false, 'message' => 'You cannot afford this pet!'];
        }
        
        // Check if user already has this pet
        $has_pet = $this->db->fetch_single($this->db->query("
            SELECT COUNT(*) FROM user_pets 
            WHERE up_user = {$this->userid} AND up_pet = {$pet_id}
        "));
        
        if ($has_pet > 0) {
            return ['success' => false, 'message' => 'You already own this pet!'];
        }
        
        // Buy the pet
        $this->db->query("
            INSERT INTO user_pets 
            (up_user, up_pet, up_nickname, up_strength, up_agility, up_guard)
            VALUES ({$this->userid}, {$pet_id}, '{$pet['pet_name']}', 
                    {$pet['pet_base_strength']}, {$pet['pet_base_agility']}, {$pet['pet_base_guard']})
        ");
        
        $this->db->query("
            UPDATE users SET primary_currency = primary_currency - {$price} 
            WHERE userid = {$this->userid}
        ");
        
        return ['success' => true, 'message' => "You have adopted {$pet['pet_name']}!"];
    }
    
    /**
     * Set active pet
     */
    public function setActivePet($pet_id) {
        // Check ownership
        $owns = $this->db->fetch_single($this->db->query("
            SELECT COUNT(*) FROM user_pets 
            WHERE up_id = {$pet_id} AND up_user = {$this->userid}
        "));
        
        if ($owns == 0) {
            return ['success' => false, 'message' => 'You do not own this pet!'];
        }
        
        // Deactivate all pets
        $this->db->query("
            UPDATE user_pets SET up_active = 0 WHERE up_user = {$this->userid}
        ");
        
        // Activate selected pet
        $this->db->query("
            UPDATE user_pets SET up_active = 1 WHERE up_id = {$pet_id}
        ");
        
        // Update user's active pet
        $this->db->query("
            UPDATE users SET active_pet = {$pet_id} WHERE userid = {$this->userid}
        ");
        
        return ['success' => true, 'message' => 'Active pet changed!'];
    }
    
    /**
     * Feed pet
     */
    public function feedPet($pet_id) {
        global $ir;
        
        $pet = $this->db->fetch_row($this->db->query("
            SELECT * FROM user_pets WHERE up_id = {$pet_id} AND up_user = {$this->userid}
        "));
        
        if (!$pet) {
            return ['success' => false, 'message' => 'Pet not found!'];
        }
        
        $feed_cost = 100 * $pet['up_level'];
        
        if ($ir['primary_currency'] < $feed_cost) {
            return ['success' => false, 'message' => "You need " . number_format($feed_cost) . " gold to feed your pet!"];
        }
        
        // Feed pet
        $this->db->query("
            UPDATE user_pets 
            SET up_hunger = GREATEST(0, up_hunger - 50),
                up_happiness = LEAST(100, up_happiness + 25),
                up_last_fed = NOW()
            WHERE up_id = {$pet_id}
        ");
        
        $this->db->query("
            UPDATE users SET primary_currency = primary_currency - {$feed_cost} 
            WHERE userid = {$this->userid}
        ");
        
        return ['success' => true, 'message' => 'Your pet has been fed and is happier!'];
    }
    
    /**
     * Train pet
     */
    public function trainPet($pet_id, $stat) {
        global $ir;
        
        $pet = $this->db->fetch_row($this->db->query("
            SELECT * FROM user_pets WHERE up_id = {$pet_id} AND up_user = {$this->userid}
        "));
        
        if (!$pet) {
            return ['success' => false, 'message' => 'Pet not found!'];
        }
        
        if ($ir['energy'] < 10) {
            return ['success' => false, 'message' => 'You need at least 10 energy to train your pet!'];
        }
        
        if ($pet['up_happiness'] < 50) {
            return ['success' => false, 'message' => 'Your pet is too unhappy to train! Feed it first.'];
        }
        
        // Train pet
        $gain = rand(1, 3);
        $exp_gain = rand(10, 25);
        
        $stat_column = '';
        if ($stat == 'strength') $stat_column = 'up_strength';
        elseif ($stat == 'agility') $stat_column = 'up_agility';
        elseif ($stat == 'guard') $stat_column = 'up_guard';
        else return ['success' => false, 'message' => 'Invalid stat!'];
        
        $this->db->query("
            UPDATE user_pets 
            SET {$stat_column} = {$stat_column} + {$gain},
                up_experience = up_experience + {$exp_gain},
                up_happiness = GREATEST(0, up_happiness - 10)
            WHERE up_id = {$pet_id}
        ");
        
        // Check for level up
        $new_pet = $this->db->fetch_row($this->db->query("
            SELECT * FROM user_pets WHERE up_id = {$pet_id}
        "));
        
        $next_level_exp = $new_pet['up_level'] * 100;
        if ($new_pet['up_experience'] >= $next_level_exp) {
            $this->db->query("
                UPDATE user_pets 
                SET up_level = up_level + 1,
                    up_experience = up_experience - {$next_level_exp}
                WHERE up_id = {$pet_id}
            ");
            
            $message = "Training complete! Your pet gained +{$gain} {$stat} and LEVELED UP!";
        } else {
            $message = "Training complete! Your pet gained +{$gain} {$stat} and {$exp_gain} experience.";
        }
        
        $this->db->query("
            UPDATE users SET energy = energy - 10 WHERE userid = {$this->userid}
        ");
        
        return ['success' => true, 'message' => $message];
    }
    
    /**
     * Get battle opponents
     */
    public function getBattleOpponents() {
        $opponents = [];
        $query = $this->db->query("
            SELECT up.*, p.*, u.username
            FROM user_pets up
            INNER JOIN pets p ON up.up_pet = p.pet_id
            INNER JOIN users u ON up.up_user = u.userid
            WHERE up.up_user != {$this->userid}
                AND up.up_active = 1
            ORDER BY RAND()
            LIMIT 10
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            $row['power'] = $row['up_strength'] + $row['up_agility'] + $row['up_guard'];
            $opponents[] = $row;
        }
        
        return $opponents;
    }
}

// Initialize system
$pet_system = new PetSystem($db, $userid, $api);

// Handle actions
if (isset($_POST['action'])) {
    $result = null;
    
    switch($_POST['action']) {
        case 'buy':
            $pet_id = abs((int)$_POST['pet_id']);
            $result = $pet_system->buyPet($pet_id);
            break;
            
        case 'set_active':
            $pet_id = abs((int)$_POST['pet_id']);
            $result = $pet_system->setActivePet($pet_id);
            break;
            
        case 'feed':
            $pet_id = abs((int)$_POST['pet_id']);
            $result = $pet_system->feedPet($pet_id);
            break;
            
        case 'train':
            $pet_id = abs((int)$_POST['pet_id']);
            $stat = $_POST['stat'];
            $result = $pet_system->trainPet($pet_id, $stat);
            break;
    }
    
    if ($result) {
        alert($result['success'] ? 'success' : 'danger',
              $result['success'] ? 'Success!' : 'Failed!',
              $result['message'], false);
    }
}

// Get data
$user_pets = $pet_system->getUserPets();
$shop_pets = $pet_system->getShopPets();

// Add sample pets if none exist
if (empty($shop_pets)) {
    // Check if pets table is empty
    $pet_count = $db->fetch_single($db->query("SELECT COUNT(*) FROM pets"));
    
    if ($pet_count == 0) {
        // Add sample pets
        $sample_pets = [
            ['Wolf Pup', 'wolf', 'common', 'A loyal wolf companion', 15, 10, 12, 'Pack Hunter: +5% damage when fighting alongside other pets'],
            ['House Cat', 'cat', 'common', 'A nimble feline friend', 8, 18, 10, 'Nine Lives: 10% chance to avoid fatal damage'],
            ['Eagle', 'bird', 'uncommon', 'A majestic bird of prey', 20, 15, 8, 'Eagle Eye: +10% critical hit chance'],
            ['Baby Dragon', 'dragon', 'rare', 'A small but fierce dragon', 25, 20, 15, 'Fire Breath: Deal burn damage over time'],
            ['Phoenix', 'bird', 'epic', 'A mythical firebird', 30, 25, 20, 'Rebirth: Resurrect once per battle with 50% health'],
            ['Cerberus Pup', 'dog', 'legendary', 'Three-headed guardian', 35, 30, 30, 'Triple Strike: Attack three times in one turn'],
            ['Unicorn', 'horse', 'mythic', 'A magical unicorn', 40, 40, 40, 'Healing Aura: Restore 10% HP each turn']
        ];
        
        foreach ($sample_pets as $pet) {
            $db->query("
                INSERT IGNORE INTO pets (pet_name, pet_species, pet_rarity, pet_description, 
                                 pet_base_strength, pet_base_agility, pet_base_guard, pet_special_ability)
                VALUES ('{$pet[0]}', '{$pet[1]}', '{$pet[2]}', '{$pet[3]}', 
                        {$pet[4]}, {$pet[5]}, {$pet[6]}, '{$pet[7]}')
            ");
        }
        
        // Refresh shop pets after adding
        $shop_pets = $pet_system->getShopPets();
    }
}

$opponents = $pet_system->getBattleOpponents();

?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-success text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h2 class="mb-0"><i class="fas fa-paw me-2"></i>Pet Companions</h2>
                            <p class="mb-0 mt-2">Train and battle with your loyal companions!</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-paw"></i> <?php echo count($user_pets); ?> Pets Owned
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#my-pets">
                <i class="fas fa-home"></i> My Pets
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#pet-shop">
                <i class="fas fa-store"></i> Pet Shop
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#pet-battles">
                <i class="fas fa-sword"></i> Pet Battles
            </a>
        </li>
    </ul>
    
    <!-- Tab Content -->
    <div class="tab-content">
        <!-- My Pets Tab -->
        <div class="tab-pane fade show active" id="my-pets">
            <?php if (empty($user_pets)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> You don't have any pets yet! Visit the Pet Shop to adopt one.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($user_pets as $pet): 
                        $happiness_color = $pet['up_happiness'] > 75 ? 'success' : ($pet['up_happiness'] > 40 ? 'warning' : 'danger');
                        $hunger_color = $pet['up_hunger'] < 25 ? 'success' : ($pet['up_hunger'] < 60 ? 'warning' : 'danger');
                    ?>
                    <div class="col-lg-6 mb-4">
                        <div class="card <?php echo $pet['up_active'] ? 'border-primary' : ''; ?>">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>
                                        <?php echo $pet['up_nickname'] ?: $pet['pet_name']; ?>
                                        <?php if ($pet['up_active']): ?>
                                            <span class="badge bg-primary">Active</span>
                                        <?php endif; ?>
                                    </h5>
                                    <span class="badge bg-<?php echo $pet['pet_rarity']; ?>">
                                        <?php echo ucfirst($pet['pet_rarity']); ?>
                                    </span>
                                </div>
                                <small class="text-muted"><?php echo $pet['pet_species']; ?> • Level <?php echo $pet['up_level']; ?></small>
                            </div>
                            <div class="card-body">
                                <p class="mb-3"><?php echo $pet['pet_description']; ?></p>
                                
                                <!-- Stats -->
                                <div class="row mb-3">
                                    <div class="col-4 text-center">
                                        <i class="fas fa-sword text-danger"></i>
                                        <strong><?php echo $pet['up_strength']; ?></strong>
                                        <br><small>Strength</small>
                                    </div>
                                    <div class="col-4 text-center">
                                        <i class="fas fa-running text-info"></i>
                                        <strong><?php echo $pet['up_agility']; ?></strong>
                                        <br><small>Agility</small>
                                    </div>
                                    <div class="col-4 text-center">
                                        <i class="fas fa-shield-alt text-secondary"></i>
                                        <strong><?php echo $pet['up_guard']; ?></strong>
                                        <br><small>Guard</small>
                                    </div>
                                </div>
                                
                                <!-- Experience -->
                                <div class="mb-3">
                                    <small>Experience</small>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?php echo $pet['exp_percent']; ?>%">
                                            <?php echo $pet['up_experience']; ?>/<?php echo $pet['next_level_exp']; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Status -->
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small>Happiness</small>
                                        <div class="progress">
                                            <div class="progress-bar bg-<?php echo $happiness_color; ?>" style="width: <?php echo $pet['up_happiness']; ?>%">
                                                <?php echo $pet['up_happiness']; ?>%
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <small>Hunger</small>
                                        <div class="progress">
                                            <div class="progress-bar bg-<?php echo $hunger_color; ?>" style="width: <?php echo 100 - $pet['up_hunger']; ?>%">
                                                <?php echo 100 - $pet['up_hunger']; ?>% Full
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Special Ability -->
                                <?php if ($pet['pet_special_ability']): ?>
                                <div class="alert alert-info py-2">
                                    <i class="fas fa-star"></i> <strong>Special:</strong> <?php echo $pet['pet_special_ability']; ?>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Actions -->
                                <div class="btn-group w-100">
                                    <?php if (!$pet['up_active']): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="set_active">
                                        <input type="hidden" name="pet_id" value="<?php echo $pet['up_id']; ?>">
                                        <?php echo getHtmlCSRF('pet_active'); ?>
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="fas fa-star"></i> Set Active
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="feed">
                                        <input type="hidden" name="pet_id" value="<?php echo $pet['up_id']; ?>">
                                        <?php echo getHtmlCSRF('pet_feed'); ?>
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-bone"></i> Feed (<?php echo number_format(100 * $pet['up_level']); ?> gold)
                                        </button>
                                    </form>
                                    
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-warning dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="fas fa-dumbbell"></i> Train
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <form method="POST">
                                                    <input type="hidden" name="action" value="train">
                                                    <input type="hidden" name="pet_id" value="<?php echo $pet['up_id']; ?>">
                                                    <input type="hidden" name="stat" value="strength">
                                                    <?php echo getHtmlCSRF('pet_train'); ?>
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-sword text-danger"></i> Train Strength
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST">
                                                    <input type="hidden" name="action" value="train">
                                                    <input type="hidden" name="pet_id" value="<?php echo $pet['up_id']; ?>">
                                                    <input type="hidden" name="stat" value="agility">
                                                    <?php echo getHtmlCSRF('pet_train'); ?>
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-running text-info"></i> Train Agility
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST">
                                                    <input type="hidden" name="action" value="train">
                                                    <input type="hidden" name="pet_id" value="<?php echo $pet['up_id']; ?>">
                                                    <input type="hidden" name="stat" value="guard">
                                                    <?php echo getHtmlCSRF('pet_train'); ?>
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-shield-alt text-secondary"></i> Train Guard
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Pet Shop Tab -->
        <div class="tab-pane fade" id="pet-shop">
            <div class="row">
                <?php foreach ($shop_pets as $pet): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><?php echo $pet['pet_name']; ?></h5>
                            <span class="badge bg-<?php echo $pet['pet_rarity']; ?>">
                                <?php echo ucfirst($pet['pet_rarity']); ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <p><?php echo $pet['pet_description']; ?></p>
                            
                            <div class="row mb-3">
                                <div class="col-4 text-center">
                                    <i class="fas fa-sword text-danger"></i> <?php echo $pet['pet_base_strength']; ?>
                                </div>
                                <div class="col-4 text-center">
                                    <i class="fas fa-running text-info"></i> <?php echo $pet['pet_base_agility']; ?>
                                </div>
                                <div class="col-4 text-center">
                                    <i class="fas fa-shield-alt text-secondary"></i> <?php echo $pet['pet_base_guard']; ?>
                                </div>
                            </div>
                            
                            <?php if ($pet['pet_special_ability']): ?>
                            <div class="alert alert-info py-1 mb-2">
                                <small><i class="fas fa-star"></i> <?php echo $pet['pet_special_ability']; ?></small>
                            </div>
                            <?php endif; ?>
                            
                            <form method="POST">
                                <input type="hidden" name="action" value="buy">
                                <input type="hidden" name="pet_id" value="<?php echo $pet['pet_id']; ?>">
                                <?php echo getHtmlCSRF('pet_buy'); ?>
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-coins"></i> Buy for <?php echo number_format($pet['price']); ?> gold
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Pet Battles Tab -->
        <div class="tab-pane fade" id="pet-battles">
            <?php if (empty($user_pets)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> You need a pet to participate in battles!
                </div>
            <?php else: ?>
                <h4>Battle Opponents</h4>
                <p class="text-muted">Challenge other players' pets to battle!</p>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Owner</th>
                                <th>Pet</th>
                                <th>Level</th>
                                <th>Power</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($opponents as $opp): ?>
                            <tr>
                                <td><?php echo $opp['username']; ?></td>
                                <td>
                                    <?php echo $opp['up_nickname'] ?: $opp['pet_name']; ?>
                                    <span class="badge bg-<?php echo $opp['pet_rarity']; ?>">
                                        <?php echo ucfirst($opp['pet_rarity']); ?>
                                    </span>
                                </td>
                                <td>Level <?php echo $opp['up_level']; ?></td>
                                <td><?php echo $opp['power']; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-danger" disabled>
                                        <i class="fas fa-sword"></i> Battle (Coming Soon)
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.bg-common { background-color: #6c757d !important; }
.bg-uncommon { background-color: #28a745 !important; }
.bg-rare { background-color: #007bff !important; }
.bg-epic { background-color: #6f42c1 !important; }
.bg-legendary { background-color: #fd7e14 !important; }
.bg-mythic { background-color: #dc3545 !important; }
</style>

<?php
$h->endpage();
?>