<?php
/*
    File: dungeons.php
    Created: Dungeon/Raid System
    Info: Enter dungeons, fight bosses, and earn rewards
*/
require_once('globals.php');

class DungeonSystem {
    private $db;
    private $userid;
    private $api;
    
    public function __construct($db, $userid, $api) {
        $this->db = $db;
        $this->userid = $userid;
        $this->api = $api;
    }
    
    /**
     * Get available dungeons
     */
    public function getDungeons() {
        global $ir;
        $dungeons = [];
        
        $query = $this->db->query("
            SELECT d.*,
                   (SELECT COUNT(*) FROM dungeon_runs 
                    WHERE dr_dungeon = d.dungeon_id 
                    AND dr_user = {$this->userid} 
                    AND dr_status = 'completed') as completions,
                   (SELECT dr_completed_at FROM dungeon_runs 
                    WHERE dr_dungeon = d.dungeon_id 
                    AND dr_user = {$this->userid} 
                    AND dr_status = 'completed'
                    ORDER BY dr_completed_at DESC LIMIT 1) as last_completed
            FROM dungeons d
            WHERE d.dungeon_min_level <= {$ir['level']}
            ORDER BY d.dungeon_recommended_level ASC
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            // Check cooldown
            if ($row['last_completed']) {
                $cooldown_end = strtotime($row['last_completed']) + $row['dungeon_cooldown'];
                $row['on_cooldown'] = time() < $cooldown_end;
                $row['cooldown_remaining'] = max(0, $cooldown_end - time());
            } else {
                $row['on_cooldown'] = false;
                $row['cooldown_remaining'] = 0;
            }
            
            $dungeons[] = $row;
        }
        
        return $dungeons;
    }
    
    /**
     * Get current/active dungeon run
     */
    public function getCurrentRun() {
        return $this->db->fetch_row($this->db->query("
            SELECT dr.*, d.*
            FROM dungeon_runs dr
            INNER JOIN dungeons d ON dr.dr_dungeon = d.dungeon_id
            WHERE dr.dr_user = {$this->userid} 
            AND dr.dr_status = 'in_progress'
            LIMIT 1
        "));
    }
    
    /**
     * Start a dungeon run
     */
    public function startDungeon($dungeon_id) {
        global $ir;
        
        // Check if already in a dungeon
        $current = $this->getCurrentRun();
        if ($current) {
            return ['success' => false, 'message' => 'You are already in a dungeon!'];
        }
        
        // Get dungeon info
        $dungeon = $this->db->fetch_row($this->db->query("
            SELECT * FROM dungeons WHERE dungeon_id = {$dungeon_id}
        "));
        
        if (!$dungeon) {
            return ['success' => false, 'message' => 'Dungeon not found!'];
        }
        
        // Check level requirement
        if ($ir['level'] < $dungeon['dungeon_min_level']) {
            return ['success' => false, 'message' => "You must be level {$dungeon['dungeon_min_level']} to enter this dungeon!"];
        }
        
        // Check energy
        if ($ir['energy'] < $dungeon['dungeon_energy_cost']) {
            return ['success' => false, 'message' => "You need {$dungeon['dungeon_energy_cost']} energy to enter!"];
        }
        
        // Check cooldown
        $last_run = $this->db->fetch_row($this->db->query("
            SELECT dr_completed_at FROM dungeon_runs 
            WHERE dr_dungeon = {$dungeon_id} 
            AND dr_user = {$this->userid} 
            AND dr_status = 'completed'
            ORDER BY dr_completed_at DESC 
            LIMIT 1
        "));
        
        if ($last_run) {
            $cooldown_end = strtotime($last_run['dr_completed_at']) + $dungeon['dungeon_cooldown'];
            if (time() < $cooldown_end) {
                return ['success' => false, 'message' => 'This dungeon is still on cooldown!'];
            }
        }
        
        // Start the run
        $this->db->query("
            INSERT INTO dungeon_runs 
            (dr_dungeon, dr_user, dr_stage_reached, dr_status)
            VALUES ({$dungeon_id}, {$this->userid}, 1, 'in_progress')
        ");
        
        // Deduct energy
        $this->db->query("
            UPDATE users SET energy = energy - {$dungeon['dungeon_energy_cost']} 
            WHERE userid = {$this->userid}
        ");
        
        return ['success' => true, 'message' => "You have entered {$dungeon['dungeon_name']}!"];
    }
    
    /**
     * Progress through dungeon stage
     */
    public function progressStage($run_id) {
        global $ir;
        
        $run = $this->db->fetch_row($this->db->query("
            SELECT dr.*, d.*
            FROM dungeon_runs dr
            INNER JOIN dungeons d ON dr.dr_dungeon = d.dungeon_id
            WHERE dr.dr_id = {$run_id} 
            AND dr.dr_user = {$this->userid}
            AND dr.dr_status = 'in_progress'
        "));
        
        if (!$run) {
            return ['success' => false, 'message' => 'Dungeon run not found or already completed!'];
        }
        
        // Get boss for current stage
        $boss = $this->db->fetch_row($this->db->query("
            SELECT * FROM dungeon_bosses 
            WHERE boss_dungeon = {$run['dr_dungeon']} 
            AND boss_stage = {$run['dr_stage_reached']}
        "));
        
        // Simulate battle (simplified)
        $player_power = $ir['strength'] + $ir['agility'] + $ir['guard'] + rand(0, 100);
        $boss_power = $boss ? ($boss['boss_attack'] + $boss['boss_defense'] + rand(0, 50)) : rand(50, 150);
        
        $victory = $player_power > $boss_power;
        
        if ($victory) {
            // Check if completed all stages
            if ($run['dr_stage_reached'] >= $run['dungeon_stages']) {
                // Complete dungeon
                $this->completeDungeon($run_id);
                return ['success' => true, 'message' => "Congratulations! You have completed {$run['dungeon_name']}!"];
            } else {
                // Progress to next stage
                $this->db->query("
                    UPDATE dungeon_runs 
                    SET dr_stage_reached = dr_stage_reached + 1 
                    WHERE dr_id = {$run_id}
                ");
                
                $stage_name = $boss ? $boss['boss_name'] : "Stage {$run['dr_stage_reached']}";
                return ['success' => true, 'message' => "You defeated {$stage_name} and advanced to stage " . ($run['dr_stage_reached'] + 1) . "!"];
            }
        } else {
            // Failed
            $this->db->query("
                UPDATE dungeon_runs 
                SET dr_status = 'failed', 
                    dr_completed_at = NOW() 
                WHERE dr_id = {$run_id}
            ");
            
            // Damage player
            $damage = min($ir['hp'] - 1, $boss_power - $player_power);
            $this->db->query("
                UPDATE users SET hp = hp - {$damage} WHERE userid = {$this->userid}
            ");
            
            $boss_name = $boss ? $boss['boss_name'] : "the dungeon";
            return ['success' => false, 'message' => "You were defeated by {$boss_name}! You lost {$damage} HP."];
        }
    }
    
    /**
     * Complete a dungeon and give rewards
     */
    private function completeDungeon($run_id) {
        global $ir;
        
        $run = $this->db->fetch_row($this->db->query("
            SELECT * FROM dungeon_runs WHERE dr_id = {$run_id}
        "));
        
        // Calculate rewards
        $gold_reward = rand(1000, 5000) * $run['dr_stage_reached'];
        $exp_reward = rand(100, 500) * $run['dr_stage_reached'];
        
        // Give rewards
        $this->db->query("
            UPDATE users 
            SET primary_currency = primary_currency + {$gold_reward},
                xp = xp + {$exp_reward},
                dungeons_completed = dungeons_completed + 1
            WHERE userid = {$this->userid}
        ");
        
        // Get loot
        $loot = [];
        $loot_query = $this->db->query("
            SELECT dl.*, i.itmname 
            FROM dungeon_loot dl
            INNER JOIN items i ON dl.dl_item = i.itmid
            WHERE dl.dl_dungeon = {$run['dr_dungeon']}
            AND (dl.dl_is_guaranteed = 1 OR RAND() * 100 < dl.dl_drop_chance)
        ");
        
        while ($item = $this->db->fetch_row($loot_query)) {
            addItem($this->userid, $item['dl_item'], $item['dl_quantity']);
            $loot[] = $item['dl_quantity'] . "x " . $item['itmname'];
        }
        
        // Update run
        $loot_text = !empty($loot) ? implode(', ', $loot) : '';
        $this->db->query("
            UPDATE dungeon_runs 
            SET dr_status = 'completed',
                dr_completed_at = NOW(),
                dr_loot_gained = '{$loot_text}',
                dr_experience_gained = {$exp_reward}
            WHERE dr_id = {$run_id}
        ");
        
        // Add notification
        addNotification($this->userid, "Dungeon completed! Rewards: {$gold_reward} gold, {$exp_reward} XP" . (!empty($loot) ? ", Items: {$loot_text}" : ""));
    }
    
    /**
     * Abandon current run
     */
    public function abandonRun($run_id) {
        $run = $this->db->fetch_row($this->db->query("
            SELECT * FROM dungeon_runs 
            WHERE dr_id = {$run_id} 
            AND dr_user = {$this->userid}
            AND dr_status = 'in_progress'
        "));
        
        if (!$run) {
            return ['success' => false, 'message' => 'No active run found!'];
        }
        
        $this->db->query("
            UPDATE dungeon_runs 
            SET dr_status = 'abandoned',
                dr_completed_at = NOW()
            WHERE dr_id = {$run_id}
        ");
        
        return ['success' => true, 'message' => 'You have abandoned the dungeon.'];
    }
    
    /**
     * Get recent completions
     */
    public function getRecentCompletions() {
        $completions = [];
        $query = $this->db->query("
            SELECT dr.*, d.dungeon_name, u.username
            FROM dungeon_runs dr
            INNER JOIN dungeons d ON dr.dr_dungeon = d.dungeon_id
            INNER JOIN users u ON dr.dr_user = u.userid
            WHERE dr.dr_status = 'completed'
            ORDER BY dr.dr_completed_at DESC
            LIMIT 20
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            $completions[] = $row;
        }
        
        return $completions;
    }
}

// Initialize system
$dungeon_system = new DungeonSystem($db, $userid, $api);

// Handle actions
if (isset($_POST['action'])) {
    $result = null;
    
    switch($_POST['action']) {
        case 'start':
            $dungeon_id = abs((int)$_POST['dungeon_id']);
            $result = $dungeon_system->startDungeon($dungeon_id);
            break;
            
        case 'progress':
            $run_id = abs((int)$_POST['run_id']);
            $result = $dungeon_system->progressStage($run_id);
            break;
            
        case 'abandon':
            $run_id = abs((int)$_POST['run_id']);
            $result = $dungeon_system->abandonRun($run_id);
            break;
    }
    
    if ($result) {
        alert($result['success'] ? 'success' : 'danger',
              $result['success'] ? 'Success!' : 'Failed!',
              $result['message'], false);
    }
}

// Get data
$dungeons = $dungeon_system->getDungeons();
$current_run = $dungeon_system->getCurrentRun();
$recent_completions = $dungeon_system->getRecentCompletions();

?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-dark text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h2 class="mb-0"><i class="fas fa-dungeon me-2"></i>Dungeons & Raids</h2>
                            <p class="mb-0 mt-2">Challenge dangerous dungeons and defeat powerful bosses!</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-trophy"></i> <?php echo $ir['dungeons_completed'] ?? 0; ?> Completed
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($current_run): ?>
    <!-- Active Dungeon Run -->
    <div class="card mb-4 border-danger">
        <div class="card-header bg-danger text-white">
            <h4><i class="fas fa-fire"></i> Active Dungeon: <?php echo $current_run['dungeon_name']; ?></h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <p><?php echo $current_run['dungeon_desc']; ?></p>
                    
                    <div class="mb-3">
                        <strong>Progress: Stage <?php echo $current_run['dr_stage_reached']; ?> / <?php echo $current_run['dungeon_stages']; ?></strong>
                        <div class="progress">
                            <div class="progress-bar bg-danger" style="width: <?php echo ($current_run['dr_stage_reached'] / $current_run['dungeon_stages']) * 100; ?>%">
                                <?php echo $current_run['dr_stage_reached']; ?>/<?php echo $current_run['dungeon_stages']; ?>
                            </div>
                        </div>
                    </div>
                    
                    <p class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Each stage contains enemies and bosses. Defeat them all to complete the dungeon!
                    </p>
                </div>
                <div class="col-md-4">
                    <form method="POST" class="mb-2">
                        <input type="hidden" name="action" value="progress">
                        <input type="hidden" name="run_id" value="<?php echo $current_run['dr_id']; ?>">
                        <?php echo getHtmlCSRF('dungeon_progress'); ?>
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-sword"></i> Fight Next Stage
                        </button>
                    </form>
                    
                    <form method="POST">
                        <input type="hidden" name="action" value="abandon">
                        <input type="hidden" name="run_id" value="<?php echo $current_run['dr_id']; ?>">
                        <?php echo getHtmlCSRF('dungeon_abandon'); ?>
                        <button type="submit" class="btn btn-secondary w-100">
                            <i class="fas fa-door-open"></i> Abandon Dungeon
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Available Dungeons -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-map"></i> Available Dungeons</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($dungeons as $dungeon): 
                            $difficulty_color = '';
                            if ($ir['level'] < $dungeon['dungeon_recommended_level'] - 5) {
                                $difficulty_color = 'danger';
                                $difficulty_text = 'Very Hard';
                            } elseif ($ir['level'] < $dungeon['dungeon_recommended_level']) {
                                $difficulty_color = 'warning';
                                $difficulty_text = 'Hard';
                            } elseif ($ir['level'] > $dungeon['dungeon_recommended_level'] + 10) {
                                $difficulty_color = 'success';
                                $difficulty_text = 'Easy';
                            } else {
                                $difficulty_color = 'info';
                                $difficulty_text = 'Normal';
                            }
                        ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5><?php echo $dungeon['dungeon_name']; ?></h5>
                                    <span class="badge bg-<?php echo $difficulty_color; ?>"><?php echo $difficulty_text; ?></span>
                                    <span class="badge bg-secondary"><?php echo ucfirst($dungeon['dungeon_type']); ?></span>
                                </div>
                                <div class="card-body">
                                    <p class="small"><?php echo $dungeon['dungeon_desc']; ?></p>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-users"></i> Level <?php echo $dungeon['dungeon_min_level']; ?>-<?php echo $dungeon['dungeon_recommended_level'] + 10; ?><br>
                                            <i class="fas fa-door-open"></i> <?php echo $dungeon['dungeon_stages']; ?> Stages<br>
                                            <i class="fas fa-bolt"></i> <?php echo $dungeon['dungeon_energy_cost']; ?> Energy<br>
                                            <i class="fas fa-clock"></i> <?php echo timeUntilParse($dungeon['dungeon_cooldown']); ?> Cooldown<br>
                                            <i class="fas fa-check"></i> Completed: <?php echo $dungeon['completions']; ?> times
                                        </small>
                                    </div>
                                    
                                    <?php if ($current_run): ?>
                                        <button class="btn btn-secondary btn-sm w-100" disabled>
                                            Already in Dungeon
                                        </button>
                                    <?php elseif ($dungeon['on_cooldown']): ?>
                                        <button class="btn btn-warning btn-sm w-100" disabled>
                                            Cooldown: <?php echo timeUntilParse($dungeon['cooldown_remaining']); ?>
                                        </button>
                                    <?php else: ?>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="start">
                                            <input type="hidden" name="dungeon_id" value="<?php echo $dungeon['dungeon_id']; ?>">
                                            <?php echo getHtmlCSRF('dungeon_start'); ?>
                                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                                <i class="fas fa-door-open"></i> Enter Dungeon
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Completions -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-history"></i> Recent Completions</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach ($recent_completions as $completion): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong><?php echo $completion['username']; ?></strong>
                                    <?php if ($completion['dr_user'] == $userid): ?>
                                        <span class="badge bg-primary">You</span>
                                    <?php endif; ?>
                                    <br>
                                    <small class="text-muted">
                                        <?php echo $completion['dungeon_name']; ?> • 
                                        Stage <?php echo $completion['dr_stage_reached']; ?>
                                    </small>
                                </div>
                                <small class="text-muted">
                                    <?php echo timeUntilParse(time() - strtotime($completion['dr_completed_at'])); ?> ago
                                </small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$h->endpage();
?>