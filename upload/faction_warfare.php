<?php
/*
    File: faction_warfare.php
    Created: Faction Warfare System
    Info: Guild-based territory control and warfare system
*/
require_once('globals.php');

class FactionWarfareSystem {
    private $db;
    private $userid;
    private $guildid;
    private $api;
    
    public function __construct($db, $userid, $guildid, $api) {
        $this->db = $db;
        $this->userid = $userid;
        $this->guildid = $guildid;
        $this->api = $api;
    }
    
    /**
     * Get all territories with their current status
     */
    public function getTerritories() {
        $territories = [];
        $query = $this->db->query("
            SELECT t.*, g.guild_name,
                   (SELECT COUNT(*) FROM guild_war_battles gwb 
                    WHERE gwb.gwb_territory = t.territory_id 
                    AND gwb.gwb_battle_time > DATE_SUB(NOW(), INTERVAL 24 HOUR)) as recent_battles
            FROM guild_territories t
            LEFT JOIN guild g ON t.territory_owner = g.guild_id
            ORDER BY t.territory_type DESC, t.territory_name
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            // Calculate territory value
            $row['territory_value'] = $this->calculateTerritoryValue($row);
            $territories[] = $row;
        }
        
        return $territories;
    }
    
    /**
     * Calculate territory strategic value
     */
    private function calculateTerritoryValue($territory) {
        $base_value = 1000;
        $type_multiplier = [
            'resource' => 1.0,
            'strategic' => 1.5,
            'fortress' => 2.0,
            'capital' => 3.0
        ];
        
        $multiplier = $type_multiplier[$territory['territory_type']] ?? 1.0;
        return intval($base_value * $multiplier * $territory['territory_level']);
    }
    
    /**
     * Get active wars
     */
    public function getActiveWars() {
        $wars = [];
        $query = $this->db->query("
            SELECT w.*, 
                   att.guild_name as attacker_name,
                   def.guild_name as defender_name,
                   t.territory_name,
                   (w.gw_end - UNIX_TIMESTAMP()) as time_remaining,
                   (SELECT COUNT(*) FROM guild_war_battles gwb WHERE gwb.gwb_war = w.gw_id) as total_battles
            FROM guild_wars w
            INNER JOIN guild att ON w.gw_declarer = att.guild_id
            INNER JOIN guild def ON w.gw_declaree = def.guild_id
            LEFT JOIN guild_territories t ON t.territory_owner = w.gw_declaree
            WHERE w.gw_end > UNIX_TIMESTAMP() AND w.gw_winner = 0
            ORDER BY w.gw_end ASC
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            $row['war_progress'] = $this->getWarProgress($row['gw_id']);
            $wars[] = $row;
        }
        
        return $wars;
    }
    
    /**
     * Get war progress/score
     */
    private function getWarProgress($war_id) {
        $query = $this->db->query("
            SELECT 
                SUM(CASE WHEN gwb_attacker = (SELECT gw_declarer FROM guild_wars WHERE gw_id = {$war_id}) THEN 1 ELSE 0 END) as attacker_wins,
                SUM(CASE WHEN gwb_defender = (SELECT gw_declaree FROM guild_wars WHERE gw_id = {$war_id}) THEN 1 ELSE 0 END) as defender_wins,
                COUNT(*) as total_battles
            FROM guild_war_battles 
            WHERE gwb_war = {$war_id}
        ");
        
        return $this->db->fetch_row($query);
    }
    
    /**
     * Declare war on another guild
     */
    public function declareWar($target_guild, $war_type = 'territory') {
        global $ir;
        
        // Get guild info
        $guild = $this->db->fetch_row($this->db->query("SELECT * FROM guild WHERE guild_id = {$this->guildid}"));
        $target = $this->db->fetch_row($this->db->query("SELECT * FROM guild WHERE guild_id = {$target_guild}"));
        
        if (!$target) {
            return ['success' => false, 'message' => 'Target guild not found!'];
        }
        
        if ($target_guild == $this->guildid) {
            return ['success' => false, 'message' => 'You cannot declare war on your own guild!'];
        }
        
        // Check if already at war
        $existing_war = $this->db->fetch_row($this->db->query("
            SELECT * FROM guild_wars 
            WHERE ((gw_declarer = {$this->guildid} AND gw_declaree = {$target_guild}) 
                OR (gw_declarer = {$target_guild} AND gw_declaree = {$this->guildid}))
                AND gw_end > UNIX_TIMESTAMP() AND gw_winner = 0
        "));
        
        if ($existing_war) {
            return ['success' => false, 'message' => 'Already at war with this guild!'];
        }
        
        // Check guild requirements
        if ($guild['guild_level'] < 3) {
            return ['success' => false, 'message' => 'Your guild must be level 3 or higher to declare war!'];
        }
        
        // War cost based on guild levels
        $war_cost = 50000 + ($guild['guild_level'] * 10000);
        
        if (($guild['guild_treasury'] ?? 0) < $war_cost) {
            return ['success' => false, 'message' => 'Guild treasury insufficient! Need ' . number_format($war_cost) . ' gold.'];
        }
        
        // War duration (24-72 hours based on type)
        $duration_hours = $war_type == 'skirmish' ? 24 : ($war_type == 'siege' ? 72 : 48);
        $war_end = time() + ($duration_hours * 3600);
        
        // Create war
        $this->db->query("
            INSERT INTO guild_wars (gw_declarer, gw_declaree, gw_drpoints, gw_depoints, gw_end, gw_winner)
            VALUES ({$this->guildid}, {$target_guild}, 0, 0, {$war_end}, 0)
        ");
        
        $war_id = $this->db->insert_id();
        
        // Deduct from treasury (if column exists)
        if ($this->columnExists('guild', 'guild_treasury')) {
            $this->db->query("UPDATE guild SET guild_treasury = guild_treasury - {$war_cost} WHERE guild_id = {$this->guildid}");
        }
        
        // Send notifications
        $this->notifyGuildMembers($target_guild, "{$guild['guild_name']} has declared war on your guild!");
        
        // Log the war declaration
        $user_ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $safe_target_name = $this->db->escape($target['guild_name']);
        $this->db->query("
            INSERT INTO logs (log_user, log_type, log_text, log_time, log_ip)
            VALUES ({$this->userid}, 'guild_war', 'Declared war on {$safe_target_name}', " . time() . ", '{$user_ip}')
        ");
        
        return ['success' => true, 'message' => "War declared on {$target['guild_name']}! War duration: {$duration_hours} hours."];
    }
    
    /**
     * Attack a territory
     */
    public function attackTerritory($territory_id) {
        global $ir;
        
        // Get territory info
        $territory = $this->db->fetch_row($this->db->query("
            SELECT * FROM guild_territories WHERE territory_id = {$territory_id}
        "));
        
        if (!$territory) {
            return ['success' => false, 'message' => 'Territory not found!'];
        }
        
        if ($territory['territory_owner'] == $this->guildid) {
            return ['success' => false, 'message' => 'You already own this territory!'];
        }
        
        // Check if at war with territory owner
        if ($territory['territory_owner'] > 0) {
            $war_check = $this->db->fetch_row($this->db->query("
                SELECT * FROM guild_wars 
                WHERE ((gw_declarer = {$this->guildid} AND gw_declaree = {$territory['territory_owner']}) 
                    OR (gw_declarer = {$territory['territory_owner']} AND gw_declaree = {$this->guildid}))
                    AND gw_end > UNIX_TIMESTAMP() AND gw_winner = 0
            "));
            
            if (!$war_check) {
                return ['success' => false, 'message' => 'You must be at war to attack this territory!'];
            }
            
            $war_id = $war_check['gw_id'];
        } else {
            $war_id = 0; // Neutral territory
        }
        
        // Energy cost based on territory type
        $energy_cost = [
            'resource' => 20,
            'strategic' => 30,
            'fortress' => 50,
            'capital' => 75
        ];
        
        $required_energy = $energy_cost[$territory['territory_type']] ?? 25;
        
        if ($ir['energy'] < $required_energy) {
            return ['success' => false, 'message' => "You need {$required_energy} energy to attack this territory!"];
        }
        
        // Calculate battle outcome
        $attacker_strength = $this->calculateGuildStrength($this->guildid);
        $defender_strength = $territory['territory_owner'] > 0 ? 
            $this->calculateGuildStrength($territory['territory_owner']) + $territory['territory_defense_bonus'] : 
            $territory['territory_defense_bonus'];
        
        $attacker_power = rand(intval($attacker_strength * 0.8), intval($attacker_strength * 1.2));
        $defender_power = rand(intval($defender_strength * 0.8), intval($defender_strength * 1.2));
        
        $victory = $attacker_power > $defender_power;
        $damage_dealt = rand(100, 500);
        $damage_taken = rand(50, 300);
        
        // Record battle
        $this->db->query("
            INSERT INTO guild_war_battles 
            (gwb_war, gwb_attacker, gwb_defender, gwb_winner, gwb_territory, gwb_attacker_damage, gwb_defender_damage, gwb_battle_type)
            VALUES ({$war_id}, {$this->userid}, " . ($territory['territory_owner'] ?: 0) . ", " . 
            ($victory ? $this->userid : ($territory['territory_owner'] ?: 0)) . ", {$territory_id}, {$damage_dealt}, {$damage_taken}, 'siege')
        ");
        
        // Update user energy
        $this->db->query("UPDATE users SET energy = energy - {$required_energy} WHERE userid = {$this->userid}");
        
        // Record participation
        $this->db->query("
            INSERT INTO guild_war_participants (gwp_war, gwp_user, gwp_guild, gwp_kills, gwp_damage_dealt, gwp_damage_taken, gwp_contribution_score)
            VALUES ({$war_id}, {$this->userid}, {$this->guildid}, " . ($victory ? 1 : 0) . ", {$damage_dealt}, {$damage_taken}, " . ($victory ? 100 : 25) . ")
            ON DUPLICATE KEY UPDATE 
                gwp_kills = gwp_kills + " . ($victory ? 1 : 0) . ",
                gwp_damage_dealt = gwp_damage_dealt + {$damage_dealt},
                gwp_damage_taken = gwp_damage_taken + {$damage_taken},
                gwp_contribution_score = gwp_contribution_score + " . ($victory ? 100 : 25) . "
        ");
        
        if ($victory) {
            // Capture territory
            $old_owner = $territory['territory_owner'];
            
            $this->db->query("
                UPDATE guild_territories 
                SET territory_owner = {$this->guildid}, 
                    territory_captured_at = NOW(),
                    territory_under_attack = 0
                WHERE territory_id = {$territory_id}
            ");
            
            // Update guild territory counts
            if ($this->columnExists('guild', 'guild_territories_owned')) {
                $this->db->query("UPDATE guild SET guild_territories_owned = guild_territories_owned + 1 WHERE guild_id = {$this->guildid}");
                if ($old_owner > 0) {
                    $this->db->query("UPDATE guild SET guild_territories_owned = guild_territories_owned - 1 WHERE guild_id = {$old_owner}");
                }
            }
            
            // Log the capture
            $user_ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $safe_territory_name = $this->db->escape($territory['territory_name']);
            $this->db->query("
                INSERT INTO logs (log_user, log_type, log_text, log_time, log_ip)
                VALUES ({$this->userid}, 'guild_war', 'Successfully captured {$safe_territory_name}!', " . time() . ", '{$user_ip}')
            ");
            
            return ['success' => true, 'message' => "Victory! You captured {$territory['territory_name']}!"];
        } else {
            return ['success' => false, 'message' => "Defeat! Your attack on {$territory['territory_name']} failed."];
        }
    }
    
    /**
     * Calculate guild military strength
     */
    private function calculateGuildStrength($guild_id) {
        if ($guild_id <= 0) return 0;
        
        $strength_query = $this->db->query("
            SELECT AVG(s.strength + s.agility + s.guard) as avg_stats, COUNT(*) as member_count
            FROM users u 
            INNER JOIN userstats s ON u.userid = s.userid
            WHERE u.guild = {$guild_id}
        ");
        
        $data = $this->db->fetch_row($strength_query);
        return intval(($data['avg_stats'] ?? 0) * ($data['member_count'] ?? 1));
    }
    
    /**
     * Get leaderboard
     */
    public function getWarLeaderboard() {
        $leaderboard = [];
        
        // Guild leaderboard
        $guild_query = $this->db->query("
            SELECT g.guild_name, g.guild_id,
                   COUNT(DISTINCT t.territory_id) as territories_owned,
                   COALESCE(SUM(gwp.gwp_contribution_score), 0) as total_war_score,
                   COUNT(DISTINCT gwp.gwp_user) as active_warriors
            FROM guild g
            LEFT JOIN guild_territories t ON g.guild_id = t.territory_owner
            LEFT JOIN guild_war_participants gwp ON g.guild_id = gwp.gwp_guild
            GROUP BY g.guild_id
            ORDER BY territories_owned DESC, total_war_score DESC
            LIMIT 20
        ");
        
        while ($row = $this->db->fetch_row($guild_query)) {
            $leaderboard['guilds'][] = $row;
        }
        
        // Player leaderboard
        $player_query = $this->db->query("
            SELECT u.username, u.userid, g.guild_name,
                   gwp.gwp_kills, gwp.gwp_damage_dealt, gwp.gwp_contribution_score
            FROM guild_war_participants gwp
            INNER JOIN users u ON gwp.gwp_user = u.userid
            LEFT JOIN guild g ON u.guild = g.guild_id
            ORDER BY gwp.gwp_contribution_score DESC
            LIMIT 20
        ");
        
        while ($row = $this->db->fetch_row($player_query)) {
            $leaderboard['players'][] = $row;
        }
        
        return $leaderboard;
    }
    
    /**
     * Check if column exists in table
     */
    private function columnExists($table, $column) {
        $query = $this->db->query("SHOW COLUMNS FROM {$table} LIKE '{$column}'");
        return $this->db->num_rows($query) > 0;
    }
    
    /**
     * Notify guild members
     */
    private function notifyGuildMembers($guild_id, $message) {
        $members = $this->db->query("SELECT userid FROM users WHERE guild = {$guild_id}");
        $user_ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        while ($member = $this->db->fetch_row($members)) {
            $safe_message = $this->db->escape($message);
            $this->db->query("
                INSERT INTO logs (log_user, log_type, log_text, log_time, log_ip)
                VALUES ({$member['userid']}, 'guild_war', '{$safe_message}', " . time() . ", '{$user_ip}')
            ");
        }
    }
}

// Check if user is in a guild
if (!$ir['guild']) {
    alert('danger', 'No Guild', 'You must be in a guild to participate in faction warfare!', true, 'guilds.php');
    die($h->endpage());
}

// Check if required tables exist
$tables_exist = true;
$required_tables = ['guild_territories', 'guild_wars', 'guild_war_participants', 'guild_war_battles'];
foreach ($required_tables as $table) {
    $check = $db->query("SHOW TABLES LIKE '{$table}'");
    if ($db->num_rows($check) == 0) {
        $tables_exist = false;
        break;
    }
}

if (!$tables_exist) {
    alert('danger', 'Feature Not Available', 'The Faction Warfare system requires additional database tables. Please contact an administrator.', true, 'guilds.php');
    die($h->endpage());
}

// Get guild info
$guild = $db->fetch_row($db->query("SELECT * FROM guild WHERE guild_id = {$ir['guild']}"));
if (!$guild) {
    alert('danger', 'Guild Error', 'Guild information not found!', true, 'guilds.php');
    die($h->endpage());
}

// Initialize system
$warfare = new FactionWarfareSystem($db, $userid, $ir['guild'], $api);

// Handle actions
if (isset($_POST['action'])) {
    $result = null;
    
    switch($_POST['action']) {
        case 'declare_war':
            $target_guild = abs((int)$_POST['target_guild']);
            $war_type = in_array($_POST['war_type'], ['skirmish', 'siege', 'conquest']) ? $_POST['war_type'] : 'siege';
            $result = $warfare->declareWar($target_guild, $war_type);
            break;
            
        case 'attack_territory':
            $territory_id = abs((int)$_POST['territory_id']);
            $result = $warfare->attackTerritory($territory_id);
            break;
    }
    
    if ($result) {
        alert($result['success'] ? 'success' : 'danger',
              $result['success'] ? 'Success!' : 'Failed!',
              $result['message'], false);
    }
}

// Get page to display
$page = $_GET['page'] ?? 'overview';

// Get data
$territories = $warfare->getTerritories();
$active_wars = $warfare->getActiveWars();
$leaderboard = $warfare->getWarLeaderboard();

?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-danger text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-0"><i class="fas fa-chess-knight me-2"></i>Faction Warfare</h2>
                            <p class="mb-0 mt-2">Conquer territories and dominate the realm!</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="faction-nav">
                                <a class="faction-nav-item <?php echo $page == 'overview' ? 'active' : ''; ?>" href="?page=overview">
                                    <i class="fas fa-home"></i>
                                    <span>Overview</span>
                                </a>
                                <a class="faction-nav-item <?php echo $page == 'territories' ? 'active' : ''; ?>" href="?page=territories">
                                    <i class="fas fa-map"></i>
                                    <span>Territories</span>
                                </a>
                                <a class="faction-nav-item <?php echo $page == 'wars' ? 'active' : ''; ?>" href="?page=wars">
                                    <i class="fas fa-fire"></i>
                                    <span>Wars</span>
                                </a>
                                <a class="faction-nav-item <?php echo $page == 'leaderboard' ? 'active' : ''; ?>" href="?page=leaderboard">
                                    <i class="fas fa-trophy"></i>
                                    <span>Leaders</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($page == 'overview'): ?>
        <!-- Guild Status -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h6>Guild Level</h6>
                        <h4><?php echo $guild['guild_level']; ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h6>Territories Owned</h6>
                        <h4><?php echo count(array_filter($territories, function($t) use ($ir) { return $t['territory_owner'] == $ir['guild']; })); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h6>Active Wars</h6>
                        <h4><?php echo count($active_wars); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h6>War Treasury</h6>
                        <h4><?php echo number_format($guild['guild_treasury'] ?? 0); ?>g</h4>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-history"></i> Recent Warfare Activity</h5>
            </div>
            <div class="card-body">
                <?php
                $recent_battles = $db->query("
                    SELECT gwb.*, u.username, t.territory_name, g1.guild_name as attacker_guild, g2.guild_name as defender_guild
                    FROM guild_war_battles gwb
                    LEFT JOIN users u ON gwb.gwb_attacker = u.userid
                    LEFT JOIN guild_territories t ON gwb.gwb_territory = t.territory_id
                    LEFT JOIN guild g1 ON u.guild = g1.guild_id
                    LEFT JOIN users u2 ON gwb.gwb_defender = u2.userid
                    LEFT JOIN guild g2 ON u2.guild = g2.guild_id
                    WHERE gwb.gwb_battle_time > DATE_SUB(NOW(), INTERVAL 24 HOUR)
                    ORDER BY gwb.gwb_battle_time DESC
                    LIMIT 10
                ");
                
                if ($db->num_rows($recent_battles) > 0):
                ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Battle</th>
                                <th>Territory</th>
                                <th>Result</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($battle = $db->fetch_row($recent_battles)): ?>
                            <tr>
                                <td><?php echo date('H:i', strtotime($battle['gwb_battle_time'])); ?></td>
                                <td><?php echo $battle['attacker_guild']; ?> vs <?php echo $battle['defender_guild']; ?></td>
                                <td><?php echo $battle['territory_name'] ?: 'Unknown'; ?></td>
                                <td>
                                    <?php if ($battle['gwb_winner'] == $battle['gwb_attacker']): ?>
                                        <span class="text-success">Victory</span>
                                    <?php else: ?>
                                        <span class="text-danger">Defeat</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted">No recent warfare activity.</p>
                <?php endif; ?>
            </div>
        </div>
        
    <?php elseif ($page == 'territories'): ?>
        <!-- Territory Map -->
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-map"></i> Territory Control</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($territories as $territory): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                        <div class="card h-100 <?php echo $territory['territory_owner'] == $ir['guild'] ? 'border-success' : ($territory['territory_owner'] > 0 ? 'border-danger' : 'border-secondary'); ?>">
                            <div class="card-header">
                                <strong><?php echo $territory['territory_name']; ?></strong>
                                <span class="badge bg-<?php 
                                    echo $territory['territory_type'] == 'capital' ? 'warning' : 
                                        ($territory['territory_type'] == 'fortress' ? 'danger' : 
                                        ($territory['territory_type'] == 'strategic' ? 'info' : 'secondary')); 
                                ?>"><?php echo ucfirst($territory['territory_type']); ?></span>
                            </div>
                            <div class="card-body">
                                <p class="small"><?php echo $territory['territory_desc']; ?></p>
                                <div class="mb-2">
                                    <strong>Owner:</strong> 
                                    <?php if ($territory['territory_owner'] > 0): ?>
                                        <span class="text-<?php echo $territory['territory_owner'] == $ir['guild'] ? 'success' : 'danger'; ?>">
                                            <?php echo $territory['guild_name']; ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">Neutral</span>
                                    <?php endif; ?>
                                </div>
                                <div class="mb-2">
                                    <strong>Defense:</strong> <?php echo $territory['territory_defense_bonus']; ?>
                                </div>
                                <div class="mb-2">
                                    <strong>Value:</strong> <?php echo number_format($territory['territory_value']); ?>
                                </div>
                                
                                <?php if ($territory['territory_owner'] != $ir['guild']): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="attack_territory">
                                    <input type="hidden" name="territory_id" value="<?php echo $territory['territory_id']; ?>">
                                    <?php echo getHtmlCSRF('attack_' . $territory['territory_id']); ?>
                                    <button type="submit" class="btn btn-danger btn-sm w-100">
                                        <i class="fas fa-sword"></i> Attack
                                    </button>
                                </form>
                                <?php else: ?>
                                <button class="btn btn-success btn-sm w-100" disabled>
                                    <i class="fas fa-shield"></i> Controlled
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
    <?php elseif ($page == 'wars'): ?>
        <!-- Active Wars -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-fire"></i> Active Wars</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($active_wars)): ?>
                            <p class="text-muted">No active wars at this time.</p>
                        <?php else: ?>
                            <?php foreach ($active_wars as $war): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6><?php echo $war['attacker_name']; ?> vs <?php echo $war['defender_name']; ?></h6>
                                            <p class="mb-0">
                                                Territory: <?php echo $war['territory_name'] ?: 'Multiple'; ?> | 
                                                Battles: <?php echo $war['total_battles']; ?> | 
                                                Time Remaining: <?php echo gmdate('H:i:s', $war['time_remaining']); ?>
                                            </p>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="progress">
                                                <div class="progress-bar bg-danger" style="width: <?php echo $war['war_progress']['attacker_wins'] ?? 0; ?>%">
                                                    <?php echo $war['war_progress']['attacker_wins'] ?? 0; ?>
                                                </div>
                                                <div class="progress-bar bg-primary" style="width: <?php echo $war['war_progress']['defender_wins'] ?? 0; ?>%">
                                                    <?php echo $war['war_progress']['defender_wins'] ?? 0; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Declare War -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-bullhorn"></i> Declare War</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($guild['guild_level'] >= 3): ?>
                        <form method="POST">
                            <input type="hidden" name="action" value="declare_war">
                            <?php echo getHtmlCSRF('declare_war'); ?>
                            
                            <div class="mb-3">
                                <label for="target_guild" class="form-label">Target Guild:</label>
                                <select name="target_guild" id="target_guild" class="form-select" required>
                                    <option value="">-- Select Guild --</option>
                                    <?php
                                    $guilds = $db->query("SELECT guild_id, guild_name FROM guild WHERE guild_id != {$ir['guild']} ORDER BY guild_name");
                                    while ($g = $db->fetch_row($guilds)):
                                    ?>
                                    <option value="<?php echo $g['guild_id']; ?>"><?php echo $g['guild_name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="war_type" class="form-label">War Type:</label>
                                <select name="war_type" id="war_type" class="form-select" required>
                                    <option value="skirmish">Skirmish (24h)</option>
                                    <option value="siege" selected>Siege (48h)</option>
                                    <option value="conquest">Conquest (72h)</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-sword"></i> Declare War
                            </button>
                            
                            <div class="alert alert-info mt-3">
                                <small>
                                    <strong>War Cost:</strong> 50,000 + (Guild Level × 10,000) gold<br>
                                    <strong>Your Cost:</strong> <?php echo number_format(50000 + ($guild['guild_level'] * 10000)); ?> gold
                                </small>
                            </div>
                        </form>
                        <?php else: ?>
                        <div class="alert alert-warning">
                            Your guild must be level 3 or higher to declare war.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
    <?php elseif ($page == 'leaderboard'): ?>
        <!-- Leaderboards -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-trophy"></i> Top Guilds</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($leaderboard['guilds'])): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Guild</th>
                                        <th>Territories</th>
                                        <th>War Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $rank = 1; foreach ($leaderboard['guilds'] as $guild_rank): ?>
                                    <tr class="<?php echo $guild_rank['guild_id'] == $ir['guild'] ? 'table-success' : ''; ?>">
                                        <td><?php echo $rank++; ?></td>
                                        <td><?php echo $guild_rank['guild_name']; ?></td>
                                        <td><?php echo $guild_rank['territories_owned']; ?></td>
                                        <td><?php echo number_format($guild_rank['total_war_score']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="text-muted">No guild rankings available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-medal"></i> Top Warriors</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($leaderboard['players'])): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Warrior</th>
                                        <th>Guild</th>
                                        <th>Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $rank = 1; foreach ($leaderboard['players'] as $player_rank): ?>
                                    <tr class="<?php echo $player_rank['userid'] == $userid ? 'table-success' : ''; ?>">
                                        <td><?php echo $rank++; ?></td>
                                        <td><?php echo $player_rank['username']; ?></td>
                                        <td><?php echo $player_rank['guild_name'] ?: 'None'; ?></td>
                                        <td><?php echo number_format($player_rank['gwp_contribution_score']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="text-muted">No warrior rankings available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Info -->
    <div class="card mt-4">
        <div class="card-body">
            <h5><i class="fas fa-info-circle"></i> Faction Warfare Guide</h5>
            <ul>
                <li><strong>Territory Types:</strong> Resource (basic income), Strategic (movement bonus), Fortress (defensive bonus), Capital (massive bonuses)</li>
                <li><strong>War Declaration:</strong> Requires guild level 3+ and treasury funds</li>
                <li><strong>Territory Attacks:</strong> Cost energy, success based on guild strength vs defense</li>
                <li><strong>War Duration:</strong> Skirmish (24h), Siege (48h), Conquest (72h)</li>
                <li><strong>Victory Conditions:</strong> Control key territories or achieve battle objectives</li>
            </ul>
        </div>
    </div>
</div>

<style>
/* Faction Warfare Custom Styles */
.faction-nav {
    display: inline-flex;
    background: rgba(255,255,255,0.1);
    border-radius: 12px;
    padding: 4px;
    backdrop-filter: blur(10px);
}

.faction-nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 8px 16px;
    margin: 0 2px;
    border-radius: 8px;
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 0.875rem;
    min-width: 80px;
    text-align: center;
}

.faction-nav-item i {
    font-size: 1.25rem;
    margin-bottom: 4px;
}

.faction-nav-item span {
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.faction-nav-item:hover {
    background: rgba(255,255,255,0.2);
    color: #fff;
    transform: translateY(-2px);
    text-decoration: none;
}

.faction-nav-item.active {
    background: rgba(255,255,255,0.95);
    color: #dc3545;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.faction-nav-item.active:hover {
    color: #dc3545;
}

@media (max-width: 768px) {
    .faction-nav {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 4px;
        width: 100%;
        margin-top: 1rem;
    }
    
    .faction-nav-item {
        min-width: auto;
    }
}

.bg-gradient-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
}

.card {
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}
</style>

<?php
$h->endpage();
?>