<?php
/*
    File: world_boss.php
    Created: World Boss Events System
    Info: Cooperative battles against massive bosses
*/
require_once('globals.php');

class WorldBossSystem {
    private $db;
    private $userid;
    private $api;
    
    public function __construct($db, $userid, $api) {
        $this->db = $db;
        $this->userid = $userid;
        $this->api = $api;
    }
    
    /**
     * Get current active world boss
     */
    public function getCurrentBoss() {
        $boss = $this->db->fetch_row($this->db->query("
            SELECT wb.*, 
                   (SELECT COUNT(DISTINCT wbd.wbd_user) FROM world_boss_damage wbd 
                    WHERE wbd.wbd_boss = wb.wb_id) as participants,
                   (SELECT SUM(wbd.wbd_damage) FROM world_boss_damage wbd 
                    WHERE wbd.wbd_boss = wb.wb_id) as total_damage
            FROM world_bosses wb
            WHERE wb.wb_active = 1 
                AND wb.wb_current_hp > 0
                AND NOW() BETWEEN wb.wb_spawn_time AND wb.wb_despawn_time
            LIMIT 1
        "));
        
        if ($boss) {
            $boss['hp_percentage'] = ($boss['wb_current_hp'] / $boss['wb_max_hp']) * 100;
            $boss['time_remaining'] = strtotime($boss['wb_despawn_time']) - time();
            
            // Get user's contribution
            $boss['user_damage'] = $this->db->fetch_single($this->db->query("
                SELECT SUM(wbd_damage) FROM world_boss_damage 
                WHERE wbd_boss = {$boss['wb_id']} AND wbd_user = {$this->userid}
            ")) ?: 0;
            
            // Get user's rank
            if ($boss['user_damage'] > 0) {
                $boss['user_rank'] = $this->db->fetch_single($this->db->query("
                    SELECT COUNT(DISTINCT wbd_user) + 1 FROM world_boss_damage
                    WHERE wbd_boss = {$boss['wb_id']} 
                        AND wbd_damage > {$boss['user_damage']}
                "));
            } else {
                $boss['user_rank'] = 0;
            }
        }
        
        return $boss;
    }
    
    /**
     * Attack the world boss
     */
    public function attackBoss($boss_id) {
        global $ir;
        
        // Check if boss is active
        $boss = $this->db->fetch_row($this->db->query("
            SELECT * FROM world_bosses 
            WHERE wb_id = {$boss_id} 
                AND wb_active = 1 
                AND wb_current_hp > 0
                AND NOW() BETWEEN wb_spawn_time AND wb_despawn_time
        "));
        
        if (!$boss) {
            return ['success' => false, 'message' => 'Boss is not available to attack!'];
        }
        
        // Check energy requirement
        if ($ir['energy'] < 10) {
            return ['success' => false, 'message' => 'You need at least 10 energy to attack the boss!'];
        }
        
        // Check cooldown
        $last_attack = $this->db->fetch_single($this->db->query("
            SELECT wbd_timestamp FROM world_boss_damage 
            WHERE wbd_boss = {$boss_id} AND wbd_user = {$this->userid}
            ORDER BY wbd_timestamp DESC LIMIT 1
        "));
        
        if ($last_attack && (time() - strtotime($last_attack)) < 60) {
            $wait = 60 - (time() - strtotime($last_attack));
            return ['success' => false, 'message' => "You must wait {$wait} seconds before attacking again!"];
        }
        
        // Calculate damage based on user stats
        $user_stats = $this->db->fetch_row($this->db->query("
            SELECT * FROM userstats WHERE userid = {$this->userid}
        "));
        
        $base_damage = $user_stats['strength'] * 2 + $user_stats['agility'];
        $crit_chance = min(50, $user_stats['agility'] / 10);
        $is_crit = rand(1, 100) <= $crit_chance;
        
        if ($is_crit) {
            $damage = $base_damage * 2;
            $message = "Critical Hit! ";
        } else {
            $damage = $base_damage;
            $message = "";
        }
        
        // Apply boss defense
        $damage = max(1, $damage - ($boss['wb_defense'] / 10));
        $damage = round($damage * (rand(80, 120) / 100)); // Add variance
        
        // Cap damage to prevent overflow (max value for INT column)
        $damage = min($damage, 999999);
        
        // Deal damage
        $new_hp = max(0, $boss['wb_current_hp'] - $damage);
        $this->db->query("
            UPDATE world_bosses 
            SET wb_current_hp = {$new_hp} 
            WHERE wb_id = {$boss_id}
        ");
        
        // Record damage
        $this->db->query("
            INSERT INTO world_boss_damage (wbd_boss, wbd_user, wbd_damage, wbd_is_crit)
            VALUES ({$boss_id}, {$this->userid}, {$damage}, " . ($is_crit ? 1 : 0) . ")
        ");
        
        // Deduct energy
        $this->db->query("
            UPDATE users SET energy = energy - 10 WHERE userid = {$this->userid}
        ");
        
        $message .= "You dealt " . number_format($damage) . " damage to {$boss['wb_name']}!";
        
        // Check if boss is defeated
        if ($new_hp == 0) {
            $this->defeatBoss($boss_id);
            $message .= " The boss has been defeated!";
        }
        
        // Random counter-attack chance
        if (rand(1, 100) <= 20) {
            $counter_damage = rand(5, 15);
            $new_user_hp = max(1, $ir['hp'] - $counter_damage);
            $this->db->query("
                UPDATE users SET hp = {$new_user_hp} WHERE userid = {$this->userid}
            ");
            $message .= " The boss counter-attacked for {$counter_damage} damage!";
        }
        
        return ['success' => true, 'message' => $message, 'damage' => $damage];
    }
    
    /**
     * Handle boss defeat and distribute rewards
     */
    private function defeatBoss($boss_id) {
        // Mark boss as defeated
        $this->db->query("
            UPDATE world_bosses 
            SET wb_active = 0, wb_defeated_at = NOW() 
            WHERE wb_id = {$boss_id}
        ");
        
        // Get top contributors
        $contributors = $this->db->query("
            SELECT wbd_user, SUM(wbd_damage) as total_damage
            FROM world_boss_damage
            WHERE wbd_boss = {$boss_id}
            GROUP BY wbd_user
            ORDER BY total_damage DESC
        ");
        
        $rank = 1;
        while ($contributor = $this->db->fetch_row($contributors)) {
            // Calculate rewards based on contribution rank
            $gold_reward = 0;
            $gem_reward = 0;
            $item_reward = null;
            
            if ($rank == 1) {
                $gold_reward = 100000;
                $gem_reward = 100;
                $item_reward = 1; // Legendary item
            } elseif ($rank <= 3) {
                $gold_reward = 50000;
                $gem_reward = 50;
                $item_reward = 2; // Epic item
            } elseif ($rank <= 10) {
                $gold_reward = 25000;
                $gem_reward = 25;
                $item_reward = 3; // Rare item
            } elseif ($rank <= 50) {
                $gold_reward = 10000;
                $gem_reward = 10;
            } else {
                $gold_reward = 5000;
                $gem_reward = 5;
            }
            
            // Give rewards
            $this->db->query("
                UPDATE users 
                SET primary_currency = primary_currency + {$gold_reward},
                    secondary_currency = secondary_currency + {$gem_reward}
                WHERE userid = {$contributor['wbd_user']}
            ");
            
            // Record rewards
            $this->db->query("
                INSERT INTO world_boss_rewards 
                (wbr_boss, wbr_user, wbr_rank, wbr_damage, wbr_gold, wbr_gems, wbr_item)
                VALUES ({$boss_id}, {$contributor['wbd_user']}, {$rank}, 
                        {$contributor['total_damage']}, {$gold_reward}, {$gem_reward}, 
                        " . ($item_reward ?: 'NULL') . ")
            ");
            
            // Send notification
            addNotification($contributor['wbd_user'], 
                "World Boss defeated! Rank #{$rank} - Earned " . number_format($gold_reward) . " gold and {$gem_reward} gems!");
            
            $rank++;
        }
    }
    
    /**
     * Get damage leaderboard for current boss
     */
    public function getLeaderboard($boss_id) {
        $leaderboard = [];
        $query = $this->db->query("
            SELECT u.username, u.userid, 
                   SUM(wbd.wbd_damage) as total_damage,
                   COUNT(wbd.wbd_id) as attacks,
                   MAX(wbd.wbd_damage) as max_hit
            FROM world_boss_damage wbd
            INNER JOIN users u ON wbd.wbd_user = u.userid
            WHERE wbd.wbd_boss = {$boss_id}
            GROUP BY wbd.wbd_user
            ORDER BY total_damage DESC
            LIMIT 20
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            $leaderboard[] = $row;
        }
        
        return $leaderboard;
    }
    
    /**
     * Get boss history
     */
    public function getBossHistory() {
        $history = [];
        $query = $this->db->query("
            SELECT wb.*, 
                   (SELECT COUNT(DISTINCT wbd.wbd_user) FROM world_boss_damage wbd 
                    WHERE wbd.wbd_boss = wb.wb_id) as participants,
                   (SELECT username FROM users u 
                    INNER JOIN world_boss_damage wbd ON u.userid = wbd.wbd_user
                    WHERE wbd.wbd_boss = wb.wb_id
                    GROUP BY wbd.wbd_user
                    ORDER BY SUM(wbd.wbd_damage) DESC
                    LIMIT 1) as mvp
            FROM world_bosses wb
            WHERE wb.wb_defeated_at IS NOT NULL
            ORDER BY wb.wb_defeated_at DESC
            LIMIT 10
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            $history[] = $row;
        }
        
        return $history;
    }
}

// Check if tables exist
$tables_exist = true;
$required_tables = ['world_bosses', 'world_boss_damage', 'world_boss_rewards'];
foreach ($required_tables as $table) {
    $check = $db->query("SHOW TABLES LIKE '{$table}'");
    if ($db->num_rows($check) == 0) {
        $tables_exist = false;
        break;
    }
}

if (!$tables_exist) {
    ?>
    <div class="container-fluid">
        <div class="alert alert-danger">
            <h4><i class="fas fa-exclamation-triangle"></i> World Boss System Not Installed</h4>
            <p>The World Boss system tables are not installed.</p>
            <?php if ($api->user->getStaffLevel($userid, 'admin')): ?>
                <p>Please run the uplift check to install the v3.1 features.</p>
                <a href="uplift_check.php" class="btn btn-warning">
                    <i class="fas fa-download"></i> Run Uplift Check
                </a>
            <?php else: ?>
                <p>Please contact an administrator to install this feature.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php
    $h->endpage();
    exit;
}

// Initialize system
$boss_system = new WorldBossSystem($db, $userid, $api);

// Handle actions
if (isset($_POST['attack'])) {
    $boss_id = abs((int)$_POST['boss_id']);
    $result = $boss_system->attackBoss($boss_id);
    
    alert($result['success'] ? 'success' : 'danger',
          $result['success'] ? 'Attack!' : 'Failed!',
          $result['message'], false);
}

// Get current boss
$current_boss = $boss_system->getCurrentBoss();
$leaderboard = $current_boss ? $boss_system->getLeaderboard($current_boss['wb_id']) : [];
$history = $boss_system->getBossHistory();

// If no boss exists, create one
if (!$current_boss) {
    // Create a sample boss
    $bosses = [
        ['Ancient Dragon', 'A massive dragon terrorizing the realm', 1000000, 500, 'dragon'],
        ['Kraken', 'A tentacled monster from the deep', 800000, 400, 'sea'],
        ['Titan Golem', 'An ancient stone guardian awakened', 1200000, 600, 'earth'],
        ['Shadow Lord', 'A being of pure darkness', 900000, 450, 'shadow'],
        ['Phoenix Emperor', 'A legendary firebird reborn', 1100000, 550, 'fire']
    ];
    
    $boss = $bosses[array_rand($bosses)];
    $spawn_time = date('Y-m-d H:i:s');
    $despawn_time = date('Y-m-d H:i:s', strtotime('+24 hours'));
    
    $db->query("
        INSERT INTO world_bosses 
        (wb_name, wb_description, wb_max_hp, wb_current_hp, wb_defense, wb_type, 
         wb_spawn_time, wb_despawn_time, wb_active)
        VALUES ('{$boss[0]}', '{$boss[1]}', {$boss[2]}, {$boss[2]}, {$boss[3]}, '{$boss[4]}',
                '{$spawn_time}', '{$despawn_time}', 1)
    ");
    
    // Refresh
    $current_boss = $boss_system->getCurrentBoss();
}

?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-danger text-white">
                <div class="card-body">
                    <h2 class="mb-0"><i class="fas fa-dragon me-2"></i>World Boss Event</h2>
                    <p class="mb-0 mt-2">Unite with other players to defeat massive bosses!</p>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($current_boss): ?>
    <!-- Current Boss -->
    <div class="card mb-4 border-danger">
        <div class="card-header bg-danger text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">
                    <i class="fas fa-skull"></i> <?php echo $current_boss['wb_name']; ?>
                </h3>
                <div>
                    <span class="badge bg-light text-dark me-2">
                        <i class="fas fa-users"></i> <?php echo $current_boss['participants']; ?> Fighters
                    </span>
                    <span class="badge bg-warning text-dark">
                        <i class="fas fa-clock"></i> <?php echo timeUntilParse($current_boss['time_remaining']); ?> remaining
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <p class="lead"><?php echo $current_boss['wb_description']; ?></p>
            
            <!-- Boss HP Bar -->
            <div class="mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <strong>Boss Health</strong>
                    <span><?php echo number_format($current_boss['wb_current_hp']); ?> / <?php echo number_format($current_boss['wb_max_hp']); ?> HP</span>
                </div>
                <div class="progress" style="height: 30px;">
                    <div class="progress-bar bg-danger progress-bar-striped progress-bar-animated" 
                         style="width: <?php echo $current_boss['hp_percentage']; ?>%">
                        <?php echo round($current_boss['hp_percentage'], 1); ?>%
                    </div>
                </div>
            </div>
            
            <!-- User Stats -->
            <?php if ($current_boss['user_damage'] > 0): ?>
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="alert alert-info py-2">
                        <i class="fas fa-chart-line"></i> Your Damage: <strong><?php echo number_format($current_boss['user_damage']); ?></strong>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-success py-2">
                        <i class="fas fa-trophy"></i> Your Rank: <strong>#<?php echo $current_boss['user_rank']; ?></strong>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-warning py-2">
                        <i class="fas fa-percentage"></i> Contribution: <strong><?php echo round(($current_boss['user_damage'] / max(1, $current_boss['total_damage'])) * 100, 2); ?>%</strong>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Attack Button -->
            <form method="POST" class="text-center">
                <input type="hidden" name="boss_id" value="<?php echo $current_boss['wb_id']; ?>">
                <input type="hidden" name="attack" value="1">
                <?php echo getHtmlCSRF('boss_attack'); ?>
                <button type="submit" class="btn btn-danger btn-lg" <?php echo $ir['energy'] < 10 ? 'disabled' : ''; ?>>
                    <i class="fas fa-sword"></i> Attack Boss (10 Energy)
                </button>
            </form>
        </div>
    </div>
    
    <!-- Leaderboard -->
    <div class="card mb-4">
        <div class="card-header">
            <h4><i class="fas fa-trophy"></i> Damage Leaderboard</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Player</th>
                            <th>Total Damage</th>
                            <th>Attacks</th>
                            <th>Max Hit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rank = 1;
                        foreach ($leaderboard as $entry): 
                        ?>
                        <tr <?php echo $entry['userid'] == $userid ? 'class="table-primary"' : ''; ?>>
                            <td>
                                <?php 
                                if ($rank == 1) echo '<i class="fas fa-trophy text-warning"></i> ';
                                elseif ($rank == 2) echo '<i class="fas fa-medal text-secondary"></i> ';
                                elseif ($rank == 3) echo '<i class="fas fa-medal" style="color: #cd7f32;"></i> ';
                                echo $rank;
                                ?>
                            </td>
                            <td>
                                <a href="profile.php?user=<?php echo $entry['userid']; ?>">
                                    <?php echo $entry['username']; ?>
                                </a>
                                <?php if ($entry['userid'] == $userid): ?>
                                    <span class="badge bg-primary">You</span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo number_format($entry['total_damage']); ?></strong></td>
                            <td><?php echo $entry['attacks']; ?></td>
                            <td><?php echo number_format($entry['max_hit']); ?></td>
                        </tr>
                        <?php 
                        $rank++;
                        endforeach; 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- No Active Boss -->
    <div class="card">
        <div class="card-body text-center">
            <i class="fas fa-hourglass-half" style="font-size: 4rem; color: #6c757d;"></i>
            <h4 class="mt-3">No Active World Boss</h4>
            <p>The next world boss will spawn soon. Check back later!</p>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Boss History -->
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-history"></i> Previous Bosses</h4>
        </div>
        <div class="card-body">
            <?php if (empty($history)): ?>
                <p class="text-muted">No bosses have been defeated yet.</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Boss</th>
                            <th>Type</th>
                            <th>Defeated</th>
                            <th>Participants</th>
                            <th>MVP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $boss): ?>
                        <tr>
                            <td><strong><?php echo $boss['wb_name']; ?></strong></td>
                            <td>
                                <span class="badge bg-secondary">
                                    <?php echo ucfirst($boss['wb_type']); ?>
                                </span>
                            </td>
                            <td><?php echo timeUntilParse(time() - strtotime($boss['wb_defeated_at'])); ?> ago</td>
                            <td><?php echo $boss['participants']; ?></td>
                            <td>
                                <?php if ($boss['mvp']): ?>
                                    <i class="fas fa-crown text-warning"></i> <?php echo $boss['mvp']; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Rewards Info -->
    <div class="card mt-4">
        <div class="card-header">
            <h5><i class="fas fa-gift"></i> Rewards</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="fas fa-trophy text-warning" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">1st Place</h6>
                        <p class="small">100,000 gold<br>100 gems<br>Legendary Item</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="fas fa-medal text-secondary" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">2nd-3rd Place</h6>
                        <p class="small">50,000 gold<br>50 gems<br>Epic Item</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="fas fa-medal" style="font-size: 2rem; color: #cd7f32;"></i>
                        <h6 class="mt-2">4th-10th Place</h6>
                        <p class="small">25,000 gold<br>25 gems<br>Rare Item</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="fas fa-users text-info" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">All Participants</h6>
                        <p class="small">5,000+ gold<br>5+ gems<br>Based on damage</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$h->endpage();
?>