<?php
/*
    File: guild_wars.php
    Created: Guild Wars system with territory control
    Info: Manage guild wars, territories, and battles
*/
require_once('globals.php');

// Check if user is in a guild
if (!$ir['guild']) {
    alert('danger', 'No Guild', 'You must be in a guild to participate in guild wars!', true, 'guilds.php');
    die($h->endpage());
}

// Get guild info
$guild = $db->fetch_row($db->query("SELECT * FROM guilds WHERE guild_id = {$ir['guild']}"));

class GuildWarSystem {
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
                   COUNT(DISTINCT w.war_id) as active_battles
            FROM guild_territories t
            LEFT JOIN guilds g ON t.territory_owner = g.guild_id
            LEFT JOIN guild_wars w ON t.territory_id = w.war_territory 
                AND w.war_status = 'active'
            GROUP BY t.territory_id
            ORDER BY t.territory_type DESC, t.territory_name
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            $territories[] = $row;
        }
        
        return $territories;
    }
    
    /**
     * Get active wars for the guild
     */
    public function getActiveWars() {
        return $this->db->query("
            SELECT w.*, 
                   att.guild_name as attacker_name,
                   def.guild_name as defender_name,
                   t.territory_name
            FROM guild_wars w
            INNER JOIN guilds att ON w.war_attacker = att.guild_id
            INNER JOIN guilds def ON w.war_defender = def.guild_id
            LEFT JOIN guild_territories t ON w.war_territory = t.territory_id
            WHERE (w.war_attacker = {$this->guildid} OR w.war_defender = {$this->guildid})
                AND w.war_status IN ('pending', 'active')
            ORDER BY w.war_status DESC, w.war_declared_at DESC
        ");
    }
    
    /**
     * Declare war on another guild
     */
    public function declareWar($target_guild, $territory_id = null, $war_type = 'territory') {
        global $guild;
        
        // Check if guilds are already at war
        $existing = $this->db->fetch_single($this->db->query("
            SELECT COUNT(*) FROM guild_wars 
            WHERE ((war_attacker = {$this->guildid} AND war_defender = {$target_guild})
                OR (war_attacker = {$target_guild} AND war_defender = {$this->guildid}))
                AND war_status IN ('pending', 'active')
        "));
        
        if ($existing > 0) {
            return ['success' => false, 'message' => 'You are already at war with this guild!'];
        }
        
        // Check guild treasury for war costs
        $war_cost = $war_type == 'total' ? 50000 : ($war_type == 'raid' ? 10000 : 25000);
        if ($guild['guild_treasury'] < $war_cost) {
            return ['success' => false, 'message' => "Your guild needs " . number_format($war_cost) . " gold in treasury to declare war!"];
        }
        
        // Territory war specific checks
        if ($war_type == 'territory' && $territory_id) {
            $territory = $this->db->fetch_row($this->db->query("
                SELECT * FROM guild_territories WHERE territory_id = {$territory_id}
            "));
            
            if (!$territory) {
                return ['success' => false, 'message' => 'Invalid territory!'];
            }
            
            if ($territory['territory_owner'] != $target_guild && $territory['territory_owner'] != null) {
                return ['success' => false, 'message' => 'This territory is not owned by the target guild!'];
            }
        }
        
        // Declare war
        $war_starts = time() + 3600; // War starts in 1 hour
        $war_ends = $war_type == 'raid' ? time() + 7200 : time() + 86400; // Raids last 2 hours, others 24 hours
        
        $this->db->query("
            INSERT INTO guild_wars 
            (war_attacker, war_defender, war_territory, war_type, war_starts_at, war_ends_at)
            VALUES 
            ({$this->guildid}, {$target_guild}, " . ($territory_id ?: 'NULL') . ", '{$war_type}',
             FROM_UNIXTIME({$war_starts}), FROM_UNIXTIME({$war_ends}))
        ");
        
        // Deduct from treasury
        $this->db->query("UPDATE guilds SET guild_treasury = guild_treasury - {$war_cost} WHERE guild_id = {$this->guildid}");
        
        // Send notifications
        $target_name = $this->db->fetch_single($this->db->query("SELECT guild_name FROM guilds WHERE guild_id = {$target_guild}"));
        $this->notifyGuildMembers($target_guild, "{$guild['guild_name']} has declared war on your guild!");
        
        return ['success' => true, 'message' => "War declared on {$target_name}! Battle begins in 1 hour."];
    }
    
    /**
     * Participate in a war battle
     */
    public function battle($war_id, $target_user) {
        global $ir;
        
        // Check if war is active
        $war = $this->db->fetch_row($this->db->query("
            SELECT * FROM guild_wars 
            WHERE war_id = {$war_id} 
                AND war_status = 'active'
                AND NOW() BETWEEN war_starts_at AND war_ends_at
        "));
        
        if (!$war) {
            return ['success' => false, 'message' => 'This war is not currently active!'];
        }
        
        // Check if user is in participating guild
        if ($war['war_attacker'] != $this->guildid && $war['war_defender'] != $this->guildid) {
            return ['success' => false, 'message' => 'Your guild is not part of this war!'];
        }
        
        // Check if target is in enemy guild
        $target_guild = $this->db->fetch_single($this->db->query("
            SELECT guild FROM users WHERE userid = {$target_user}
        "));
        
        $enemy_guild = $war['war_attacker'] == $this->guildid ? $war['war_defender'] : $war['war_attacker'];
        
        if ($target_guild != $enemy_guild) {
            return ['success' => false, 'message' => 'Target is not in the enemy guild!'];
        }
        
        // Simulate battle (simplified)
        $attacker_power = $ir['strength'] + $ir['agility'] + rand(1, 100);
        $target_data = $this->db->fetch_row($this->db->query("
            SELECT strength, agility, username FROM userstats 
            INNER JOIN users ON userstats.userid = users.userid
            WHERE users.userid = {$target_user}
        "));
        $defender_power = $target_data['strength'] + $target_data['agility'] + rand(1, 100);
        
        $winner = $attacker_power > $defender_power ? $this->userid : $target_user;
        $damage_dealt = abs($attacker_power - $defender_power);
        
        // Record battle
        $this->db->query("
            INSERT INTO guild_war_battles
            (gwb_war, gwb_attacker, gwb_defender, gwb_winner, gwb_territory, 
             gwb_attacker_damage, gwb_defender_damage, gwb_battle_type)
            VALUES
            ({$war_id}, {$this->userid}, {$target_user}, {$winner}, 
             " . ($war['war_territory'] ?: 'NULL') . ",
             {$attacker_power}, {$defender_power}, 'skirmish')
        ");
        
        // Update participant stats
        $this->updateParticipantStats($war_id, $this->userid, $winner == $this->userid ? 1 : 0, 
                                      $winner != $this->userid ? 1 : 0, $damage_dealt);
        
        // Update war scores
        $score_change = $winner == $this->userid ? 10 : 5;
        if ($war['war_attacker'] == $this->guildid) {
            $this->db->query("UPDATE guild_wars SET war_attacker_score = war_attacker_score + {$score_change} WHERE war_id = {$war_id}");
        } else {
            $this->db->query("UPDATE guild_wars SET war_defender_score = war_defender_score + {$score_change} WHERE war_id = {$war_id}");
        }
        
        if ($winner == $this->userid) {
            return ['success' => true, 'message' => "Victory! You defeated {$target_data['username']} and earned {$score_change} war points!"];
        } else {
            return ['success' => false, 'message' => "Defeated! {$target_data['username']} was too strong. You still earned 5 war points for trying."];
        }
    }
    
    /**
     * Capture or defend a territory
     */
    public function captureTerritory($territory_id) {
        global $ir, $guild;
        
        $territory = $this->db->fetch_row($this->db->query("
            SELECT * FROM guild_territories WHERE territory_id = {$territory_id}
        "));
        
        if (!$territory) {
            return ['success' => false, 'message' => 'Invalid territory!'];
        }
        
        // Check if there's an active war for this territory
        $war = $this->db->fetch_row($this->db->query("
            SELECT * FROM guild_wars 
            WHERE war_territory = {$territory_id}
                AND war_status = 'active'
                AND (war_attacker = {$this->guildid} OR war_defender = {$this->guildid})
        "));
        
        if (!$war) {
            // No war, check if territory is unowned
            if ($territory['territory_owner'] == null) {
                // Claim unowned territory
                $claim_cost = 10000 * $territory['territory_level'];
                
                if ($guild['guild_treasury'] < $claim_cost) {
                    return ['success' => false, 'message' => "Need " . number_format($claim_cost) . " gold in treasury to claim this territory!"];
                }
                
                $this->db->query("
                    UPDATE guild_territories 
                    SET territory_owner = {$this->guildid}, 
                        territory_captured_at = NOW()
                    WHERE territory_id = {$territory_id}
                ");
                
                $this->db->query("
                    UPDATE guilds 
                    SET guild_treasury = guild_treasury - {$claim_cost},
                        guild_territories_owned = guild_territories_owned + 1
                    WHERE guild_id = {$this->guildid}
                ");
                
                return ['success' => true, 'message' => "Territory claimed for {$guild['guild_name']}!"];
            } else {
                return ['success' => false, 'message' => 'You must declare war to capture owned territories!'];
            }
        }
        
        // War capture logic - simplified
        if ($war['war_attacker_score'] > $war['war_defender_score'] && $war['war_attacker'] == $this->guildid) {
            // Attacker wins
            $old_owner = $territory['territory_owner'];
            
            $this->db->query("
                UPDATE guild_territories 
                SET territory_owner = {$this->guildid}, 
                    territory_captured_at = NOW()
                WHERE territory_id = {$territory_id}
            ");
            
            $this->db->query("UPDATE guilds SET guild_territories_owned = guild_territories_owned + 1 WHERE guild_id = {$this->guildid}");
            if ($old_owner) {
                $this->db->query("UPDATE guilds SET guild_territories_owned = guild_territories_owned - 1 WHERE guild_id = {$old_owner}");
            }
            
            return ['success' => true, 'message' => "Territory captured!"];
        } else {
            return ['success' => false, 'message' => 'Your guild needs a higher war score to capture this territory!'];
        }
    }
    
    /**
     * Collect resources from owned territories
     */
    public function collectResources() {
        global $ir;
        
        // Get uncollected resources
        $resources = $this->db->query("
            SELECT tr.*, t.territory_name 
            FROM territory_resources tr
            INNER JOIN guild_territories t ON tr.tr_territory = t.territory_id
            WHERE tr.tr_guild = {$this->guildid} 
                AND tr.tr_collected = 0
        ");
        
        $total_gold = 0;
        $total_crystals = 0;
        $territories = [];
        
        while ($row = $this->db->fetch_row($resources)) {
            if ($row['tr_resource_type'] == 'gold') {
                $total_gold += $row['tr_amount'];
            } elseif ($row['tr_resource_type'] == 'crystals') {
                $total_crystals += $row['tr_amount'];
            }
            $territories[] = $row['territory_name'];
        }
        
        if ($total_gold == 0 && $total_crystals == 0) {
            return ['success' => false, 'message' => 'No resources to collect!'];
        }
        
        // Mark as collected
        $this->db->query("
            UPDATE territory_resources 
            SET tr_collected = 1, 
                tr_collected_by = {$this->userid},
                tr_collected_at = NOW()
            WHERE tr_guild = {$this->guildid} AND tr_collected = 0
        ");
        
        // Add to guild treasury
        $this->db->query("
            UPDATE guilds 
            SET guild_treasury = guild_treasury + {$total_gold}
            WHERE guild_id = {$this->guildid}
        ");
        
        // Give crystals to collector
        if ($total_crystals > 0) {
            $this->db->query("
                UPDATE users 
                SET secondary_currency = secondary_currency + {$total_crystals}
                WHERE userid = {$this->userid}
            ");
        }
        
        $message = "Collected " . number_format($total_gold) . " gold for guild treasury";
        if ($total_crystals > 0) {
            $message .= " and " . $total_crystals . " crystals for yourself";
        }
        $message .= " from " . count(array_unique($territories)) . " territories!";
        
        return ['success' => true, 'message' => $message];
    }
    
    /**
     * Update participant stats in war
     */
    private function updateParticipantStats($war_id, $user_id, $kills, $deaths, $damage) {
        // Check if participant exists
        $exists = $this->db->fetch_single($this->db->query("
            SELECT gwp_id FROM guild_war_participants 
            WHERE gwp_war = {$war_id} AND gwp_user = {$user_id}
        "));
        
        if ($exists) {
            $this->db->query("
                UPDATE guild_war_participants 
                SET gwp_kills = gwp_kills + {$kills},
                    gwp_deaths = gwp_deaths + {$deaths},
                    gwp_damage_dealt = gwp_damage_dealt + {$damage},
                    gwp_contribution_score = gwp_contribution_score + " . ($kills * 10 + 5) . "
                WHERE gwp_war = {$war_id} AND gwp_user = {$user_id}
            ");
        } else {
            $this->db->query("
                INSERT INTO guild_war_participants
                (gwp_war, gwp_user, gwp_guild, gwp_kills, gwp_deaths, gwp_damage_dealt, gwp_contribution_score)
                VALUES
                ({$war_id}, {$user_id}, {$this->guildid}, {$kills}, {$deaths}, {$damage}, " . ($kills * 10 + 5) . ")
            ");
        }
        
        // Update user stats
        $this->db->query("
            UPDATE users 
            SET guild_wars_participated = guild_wars_participated + 1,
                guild_war_kills = guild_war_kills + {$kills},
                guild_war_score = guild_war_score + " . ($kills * 10 + 5) . "
            WHERE userid = {$user_id}
        ");
    }
    
    /**
     * Send notifications to guild members
     */
    private function notifyGuildMembers($guild_id, $message) {
        $members = $this->db->query("SELECT userid FROM users WHERE guild = {$guild_id}");
        while ($member = $this->db->fetch_row($members)) {
            addNotification($member['userid'], $message);
        }
    }
}

// Initialize system
$gws = new GuildWarSystem($db, $userid, $ir['guild'], $api);

// Handle actions
if (isset($_POST['action'])) {
    $result = null;
    
    switch($_POST['action']) {
        case 'declare_war':
            $target = abs((int)$_POST['target_guild']);
            $territory = isset($_POST['territory']) ? abs((int)$_POST['territory']) : null;
            $type = in_array($_POST['war_type'], ['territory', 'raid', 'total']) ? $_POST['war_type'] : 'territory';
            $result = $gws->declareWar($target, $territory, $type);
            break;
            
        case 'battle':
            $war = abs((int)$_POST['war_id']);
            $target = abs((int)$_POST['target_user']);
            $result = $gws->battle($war, $target);
            break;
            
        case 'capture':
            $territory = abs((int)$_POST['territory_id']);
            $result = $gws->captureTerritory($territory);
            break;
            
        case 'collect':
            $result = $gws->collectResources();
            break;
    }
    
    if ($result) {
        alert($result['success'] ? 'success' : 'danger', 
              $result['success'] ? 'Success!' : 'Failed!', 
              $result['message'], false);
    }
}

// Get data for display
$territories = $gws->getTerritories();
$active_wars = $gws->getActiveWars();

?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-danger text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h2 class="mb-0"><i class="fas fa-chess me-2"></i>Guild Wars</h2>
                            <p class="mb-0 mt-2">Fight for territory control and resources!</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h4><?php echo $guild['guild_name']; ?></h4>
                            <div>
                                <span class="badge bg-light text-dark me-2">
                                    <i class="fas fa-star"></i> Rating: <?php echo number_format($guild['guild_war_rating'] ?? 1000); ?>
                                </span>
                                <span class="badge bg-light text-dark me-2">
                                    <i class="fas fa-map"></i> Territories: <?php echo $guild['guild_territories_owned'] ?? 0; ?>
                                </span>
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-coins"></i> Treasury: <?php echo number_format($guild['guild_treasury'] ?? 0); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Active Wars -->
    <?php if ($db->num_rows($active_wars) > 0): ?>
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">
            <h4><i class="fas fa-fire"></i> Active Wars</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Enemy</th>
                            <th>Type</th>
                            <th>Territory</th>
                            <th>Score</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($war = $db->fetch_row($active_wars)): 
                            $is_attacker = $war['war_attacker'] == $ir['guild'];
                            $enemy_name = $is_attacker ? $war['defender_name'] : $war['attacker_name'];
                            $our_score = $is_attacker ? $war['war_attacker_score'] : $war['war_defender_score'];
                            $their_score = $is_attacker ? $war['war_defender_score'] : $war['war_attacker_score'];
                        ?>
                        <tr>
                            <td><strong><?php echo $enemy_name; ?></strong></td>
                            <td><?php echo ucfirst($war['war_type']); ?></td>
                            <td><?php echo $war['territory_name'] ?: 'N/A'; ?></td>
                            <td>
                                <span class="badge <?php echo $our_score > $their_score ? 'bg-success' : 'bg-danger'; ?>">
                                    <?php echo $our_score; ?> - <?php echo $their_score; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($war['war_status'] == 'active'): ?>
                                    <span class="badge bg-danger">ACTIVE</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">PENDING</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($war['war_status'] == 'active'): ?>
                                    <a href="guild_war_battle.php?war=<?php echo $war['war_id']; ?>" class="btn btn-sm btn-danger">
                                        <i class="fas fa-sword"></i> Battle
                                    </a>
                                <?php else: ?>
                                    <small>Starts soon...</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Territory Map -->
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-map"></i> Territory Map</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($territories as $territory): 
                    $owned_by_us = $territory['territory_owner'] == $ir['guild'];
                    $unowned = $territory['territory_owner'] == null;
                ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                    <div class="card h-100 <?php 
                        echo $owned_by_us ? 'border-success' : ($unowned ? 'border-secondary' : 'border-danger'); 
                    ?>">
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo $territory['territory_name']; ?>
                                <?php if ($territory['territory_type'] == 'capital'): ?>
                                    <i class="fas fa-crown text-warning"></i>
                                <?php elseif ($territory['territory_type'] == 'fortress'): ?>
                                    <i class="fas fa-chess-rook text-secondary"></i>
                                <?php endif; ?>
                            </h5>
                            <p class="card-text small"><?php echo $territory['territory_desc']; ?></p>
                            
                            <div class="mb-2">
                                <?php if ($unowned): ?>
                                    <span class="badge bg-secondary">Unowned</span>
                                <?php else: ?>
                                    <span class="badge <?php echo $owned_by_us ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo $territory['guild_name']; ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if ($territory['active_battles'] > 0): ?>
                                    <span class="badge bg-warning">⚔️ Battle Active</span>
                                <?php endif; ?>
                            </div>
                            
                            <small class="text-muted d-block mb-2">
                                <i class="fas fa-gem"></i> <?php echo $territory['territory_resource_type']; ?>
                                (+<?php echo $territory['territory_resource_rate']; ?>/hour)
                            </small>
                            
                            <?php if ($owned_by_us): ?>
                                <button class="btn btn-sm btn-success w-100" disabled>
                                    <i class="fas fa-check"></i> Owned
                                </button>
                            <?php elseif ($unowned): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="capture">
                                    <input type="hidden" name="territory_id" value="<?php echo $territory['territory_id']; ?>">
                                    <?php echo getHtmlCSRF('guild_war_capture'); ?>
                                    <button type="submit" class="btn btn-sm btn-primary w-100">
                                        <i class="fas fa-flag"></i> Claim
                                    </button>
                                </form>
                            <?php else: ?>
                                <button class="btn btn-sm btn-danger w-100" 
                                        onclick="declareWarModal(<?php echo $territory['territory_owner']; ?>, <?php echo $territory['territory_id']; ?>, '<?php echo $territory['guild_name']; ?>')">
                                    <i class="fas fa-chess"></i> Declare War
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Collect Resources Button -->
    <div class="text-center mt-4">
        <form method="POST" class="d-inline">
            <input type="hidden" name="action" value="collect">
            <?php echo getHtmlCSRF('guild_war_collect'); ?>
            <button type="submit" class="btn btn-lg btn-warning">
                <i class="fas fa-coins"></i> Collect Territory Resources
            </button>
        </form>
    </div>
</div>

<!-- Declare War Modal -->
<div class="modal fade" id="declareWarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Declare War</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="declare_war">
                    <input type="hidden" name="target_guild" id="war_target_guild">
                    <input type="hidden" name="territory" id="war_territory">
                    <?php echo getHtmlCSRF('guild_declare_war'); ?>
                    
                    <p>Declare war on <strong id="war_guild_name"></strong>?</p>
                    
                    <div class="form-group">
                        <label>War Type:</label>
                        <select name="war_type" class="form-control">
                            <option value="territory">Territory War (25,000 gold)</option>
                            <option value="raid">Quick Raid (10,000 gold)</option>
                            <option value="total">Total War (50,000 gold)</option>
                        </select>
                    </div>
                    
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle"></i> War will begin in 1 hour after declaration!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Declare War</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function declareWarModal(guildId, territoryId, guildName) {
    document.getElementById('war_target_guild').value = guildId;
    document.getElementById('war_territory').value = territoryId;
    document.getElementById('war_guild_name').textContent = guildName;
    new bootstrap.Modal(document.getElementById('declareWarModal')).show();
}
</script>

<?php
$h->endpage();
?>