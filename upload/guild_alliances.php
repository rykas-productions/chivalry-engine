<?php
/*
    File: guild_alliances.php
    Created: Guild Alliance System
    Info: Form alliances between guilds for mutual benefit
*/
require_once('globals.php');

// Check if user is in a guild
if (!$ir['guild']) {
    alert('danger', 'No Guild', 'You must be in a guild to manage alliances!', true, 'guilds.php');
    die($h->endpage());
}

// Get guild info and check if user is a leader
$guild = $db->fetch_row($db->query("SELECT * FROM guild WHERE guild_id = {$ir['guild']}"));
$is_leader = ($guild['guild_owner'] == $userid || $guild['guild_coowner'] == $userid);

class GuildAllianceSystem {
    private $db;
    private $userid;
    private $guildid;
    
    public function __construct($db, $userid, $guildid) {
        $this->db = $db;
        $this->userid = $userid;
        $this->guildid = $guildid;
        
        // Create tables if they don't exist
        $this->initializeTables();
    }
    
    /**
     * Initialize alliance tables
     */
    private function initializeTables() {
        // Check if guild_alliances table exists
        $check = $this->db->query("SHOW TABLES LIKE 'guild_alliances'");
        if ($this->db->num_rows($check) == 0) {
            // Table doesn't exist, but it should have been created by the SQL files
            // For now, we'll work with what exists
            return false;
        }
        return true;
    }
    
    /**
     * Get current alliances
     */
    public function getAlliances() {
        $alliances = [];
        
        // Get alliances where we are guild1 or guild2
        $query = $this->db->query("
            SELECT ga.*, 
                   g1.guild_name as guild1_name, g1.guild_level as guild1_level,
                   g2.guild_name as guild2_name, g2.guild_level as guild2_level,
                   (SELECT COUNT(*) FROM users WHERE guild = g1.guild_id) as guild1_members,
                   (SELECT COUNT(*) FROM users WHERE guild = g2.guild_id) as guild2_members
            FROM guild_alliances ga
            INNER JOIN guild g1 ON ga.alliance_a = g1.guild_id
            INNER JOIN guild g2 ON ga.alliance_b = g2.guild_id
            WHERE (ga.alliance_a = {$this->guildid} OR ga.alliance_b = {$this->guildid})
                  AND ga.alliance_true = 1
            ORDER BY ga.alliance_id DESC
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            // Determine which guild is the ally
            if ($row['alliance_a'] == $this->guildid) {
                $row['ally_id'] = $row['alliance_b'];
                $row['ally_name'] = $row['guild2_name'];
                $row['ally_level'] = $row['guild2_level'];
                $row['ally_members'] = $row['guild2_members'];
            } else {
                $row['ally_id'] = $row['alliance_a'];
                $row['ally_name'] = $row['guild1_name'];
                $row['ally_level'] = $row['guild1_level'];
                $row['ally_members'] = $row['guild1_members'];
            }
            
            // Set default values for missing columns
            $row['alliance_formed'] = time(); // Default timestamp
            $row['alliance_message'] = '';
            
            $alliances[] = $row;
        }
        
        return $alliances;
    }
    
    /**
     * Get pending alliance requests
     */
    public function getPendingRequests() {
        $requests = [];
        
        // Incoming requests
        $incoming = $this->db->query("
            SELECT ga.*, g.guild_name, g.guild_level,
                   (SELECT COUNT(*) FROM users WHERE guild = g.guild_id) as member_count
            FROM guild_alliances ga
            INNER JOIN guild g ON ga.alliance_a = g.guild_id
            WHERE ga.alliance_b = {$this->guildid}
                  AND ga.alliance_true = 0
            ORDER BY ga.alliance_id DESC
        ");
        
        while ($row = $this->db->fetch_row($incoming)) {
            $row['type'] = 'incoming';
            $row['alliance_requested'] = time(); // Default timestamp
            $row['alliance_message'] = '';
            $requests[] = $row;
        }
        
        // Outgoing requests
        $outgoing = $this->db->query("
            SELECT ga.*, g.guild_name, g.guild_level,
                   (SELECT COUNT(*) FROM users WHERE guild = g.guild_id) as member_count
            FROM guild_alliances ga
            INNER JOIN guild g ON ga.alliance_b = g.guild_id
            WHERE ga.alliance_a = {$this->guildid}
                  AND ga.alliance_true = 0
            ORDER BY ga.alliance_id DESC
        ");
        
        while ($row = $this->db->fetch_row($outgoing)) {
            $row['type'] = 'outgoing';
            $row['alliance_requested'] = time(); // Default timestamp
            $row['alliance_message'] = '';
            $requests[] = $row;
        }
        
        return $requests;
    }
    
    /**
     * Request alliance with another guild
     */
    public function requestAlliance($target_guild, $message = '') {
        // Check if target guild exists
        $target = $this->db->fetch_row($this->db->query("
            SELECT * FROM guild WHERE guild_id = {$target_guild}
        "));
        
        if (!$target) {
            return ['success' => false, 'message' => 'Target guild not found!'];
        }
        
        if ($target_guild == $this->guildid) {
            return ['success' => false, 'message' => 'You cannot form an alliance with your own guild!'];
        }
        
        // Check if alliance already exists or is pending
        $existing = $this->db->fetch_row($this->db->query("
            SELECT * FROM guild_alliances 
            WHERE ((alliance_a = {$this->guildid} AND alliance_b = {$target_guild})
                   OR (alliance_a = {$target_guild} AND alliance_b = {$this->guildid}))
        "));
        
        if ($existing) {
            if ($existing['alliance_true'] == 1) {
                return ['success' => false, 'message' => 'Alliance already exists with this guild!'];
            } else {
                return ['success' => false, 'message' => 'Alliance request already pending!'];
            }
        }
        
        // Check alliance limits (max 5 active alliances)
        $current_alliances = $this->db->fetch_single($this->db->query("
            SELECT COUNT(*) FROM guild_alliances 
            WHERE (alliance_a = {$this->guildid} OR alliance_b = {$this->guildid})
                  AND alliance_true = 1
        "));
        
        if ($current_alliances >= 5) {
            return ['success' => false, 'message' => 'You have reached the maximum number of alliances (5)!'];
        }
        
        // Create alliance request
        $this->db->query("
            INSERT INTO guild_alliances 
            (alliance_a, alliance_b, alliance_type, alliance_true)
            VALUES ({$this->guildid}, {$target_guild}, 1, 0)
        ");
        
        // Notify target guild leader
        $this->notifyGuildLeader($target_guild, "Alliance request from {$target['guild_name']}");
        
        return ['success' => true, 'message' => "Alliance request sent to {$target['guild_name']}!"];
    }
    
    /**
     * Accept alliance request
     */
    public function acceptAlliance($alliance_id) {
        // Verify the alliance request
        $alliance = $this->db->fetch_row($this->db->query("
            SELECT * FROM guild_alliances 
            WHERE alliance_id = {$alliance_id} 
                  AND alliance_b = {$this->guildid}
                  AND alliance_true = 0
        "));
        
        if (!$alliance) {
            return ['success' => false, 'message' => 'Invalid alliance request!'];
        }
        
        // Accept the alliance
        $this->db->query("
            UPDATE guild_alliances 
            SET alliance_true = 1
            WHERE alliance_id = {$alliance_id}
        ");
        
        // Get guild names for notification
        $guild1 = $this->db->fetch_single($this->db->query("
            SELECT guild_name FROM guild WHERE guild_id = {$alliance['alliance_a']}
        "));
        $guild2 = $this->db->fetch_single($this->db->query("
            SELECT guild_name FROM guild WHERE guild_id = {$alliance['alliance_b']}
        "));
        
        // Notify both guilds
        $this->notifyGuildMembers($alliance['alliance_a'], "Alliance formed with {$guild2}!");
        $this->notifyGuildMembers($alliance['alliance_b'], "Alliance formed with {$guild1}!");
        
        return ['success' => true, 'message' => "Alliance accepted!"];
    }
    
    /**
     * Reject alliance request
     */
    public function rejectAlliance($alliance_id) {
        // Verify the alliance request
        $alliance = $this->db->fetch_row($this->db->query("
            SELECT * FROM guild_alliances 
            WHERE alliance_id = {$alliance_id} 
                  AND alliance_b = {$this->guildid}
                  AND alliance_true = 0
        "));
        
        if (!$alliance) {
            return ['success' => false, 'message' => 'Invalid alliance request!'];
        }
        
        // Delete the request
        $this->db->query("DELETE FROM guild_alliances WHERE alliance_id = {$alliance_id}");
        
        return ['success' => true, 'message' => "Alliance request rejected."];
    }
    
    /**
     * Break alliance
     */
    public function breakAlliance($alliance_id) {
        // Verify the alliance
        $alliance = $this->db->fetch_row($this->db->query("
            SELECT * FROM guild_alliances 
            WHERE alliance_id = {$alliance_id} 
                  AND (alliance_a = {$this->guildid} OR alliance_b = {$this->guildid})
                  AND alliance_true = 1
        "));
        
        if (!$alliance) {
            return ['success' => false, 'message' => 'Invalid alliance!'];
        }
        
        // Delete the alliance (or mark as broken)
        $this->db->query("
            DELETE FROM guild_alliances 
            WHERE alliance_id = {$alliance_id}
        ");
        
        // Determine the other guild
        $other_guild = ($alliance['alliance_a'] == $this->guildid) ? 
                       $alliance['alliance_b'] : $alliance['alliance_a'];
        
        // Get guild names
        $our_name = $this->db->fetch_single($this->db->query("
            SELECT guild_name FROM guild WHERE guild_id = {$this->guildid}
        "));
        $their_name = $this->db->fetch_single($this->db->query("
            SELECT guild_name FROM guild WHERE guild_id = {$other_guild}
        "));
        
        // Notify both guilds
        $this->notifyGuildMembers($this->guildid, "Alliance with {$their_name} has been broken.");
        $this->notifyGuildMembers($other_guild, "Alliance with {$our_name} has been broken.");
        
        return ['success' => true, 'message' => "Alliance broken."];
    }
    
    /**
     * Get alliance benefits summary
     */
    public function getAllianceBenefits() {
        $alliance_count = count($this->getAlliances());
        
        $benefits = [
            'defense_bonus' => $alliance_count * 5,  // 5% defense bonus per alliance
            'trade_discount' => $alliance_count * 2, // 2% trade discount per alliance
            'shared_victories' => true,              // Share war victories
            'territory_support' => true,              // Support in territory defense
            'max_alliances' => 5
        ];
        
        return $benefits;
    }
    
    /**
     * Notify guild leader
     */
    private function notifyGuildLeader($guild_id, $message) {
        $leader_id = $this->db->fetch_single($this->db->query("
            SELECT guild_owner FROM guild WHERE guild_id = {$guild_id}
        "));
        
        if ($leader_id) {
            $user_ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $safe_message = $this->db->escape($message);
            $this->db->query("
                INSERT INTO logs (log_user, log_type, log_text, log_time, log_ip)
                VALUES ({$leader_id}, 'guild_alliance', '{$safe_message}', " . time() . ", '{$user_ip}')
            ");
        }
    }
    
    /**
     * Notify all guild members
     */
    private function notifyGuildMembers($guild_id, $message) {
        $members = $this->db->query("SELECT userid FROM users WHERE guild = {$guild_id}");
        $user_ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $safe_message = $this->db->escape($message);
        
        while ($member = $this->db->fetch_row($members)) {
            $this->db->query("
                INSERT INTO logs (log_user, log_type, log_text, log_time, log_ip)
                VALUES ({$member['userid']}, 'guild_alliance', '{$safe_message}', " . time() . ", '{$user_ip}')
            ");
        }
    }
}

// Initialize system
$alliance_system = new GuildAllianceSystem($db, $userid, $ir['guild']);

// Handle actions (only for guild leaders)
if ($is_leader && isset($_POST['action'])) {
    $result = null;
    
    switch($_POST['action']) {
        case 'request_alliance':
            $target_guild = abs((int)$_POST['target_guild']);
            $message = strip_tags($_POST['message'] ?? '');
            $result = $alliance_system->requestAlliance($target_guild, $message);
            break;
            
        case 'accept_alliance':
            $alliance_id = abs((int)$_POST['alliance_id']);
            $result = $alliance_system->acceptAlliance($alliance_id);
            break;
            
        case 'reject_alliance':
            $alliance_id = abs((int)$_POST['alliance_id']);
            $result = $alliance_system->rejectAlliance($alliance_id);
            break;
            
        case 'break_alliance':
            $alliance_id = abs((int)$_POST['alliance_id']);
            $result = $alliance_system->breakAlliance($alliance_id);
            break;
    }
    
    if ($result) {
        alert($result['success'] ? 'success' : 'danger',
              $result['success'] ? 'Success!' : 'Failed!',
              $result['message'], false);
    }
}

// Get data
$alliances = $alliance_system->getAlliances();
$pending_requests = $alliance_system->getPendingRequests();
$benefits = $alliance_system->getAllianceBenefits();

// Get page to display
$page = $_GET['page'] ?? 'overview';

?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-info text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-0"><i class="fas fa-handshake me-2"></i>Guild Alliances</h2>
                            <p class="mb-0 mt-2">Form strategic alliances with other guilds!</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="alliance-nav">
                                <a class="alliance-nav-item <?php echo $page == 'overview' ? 'active' : ''; ?>" href="?page=overview">
                                    <i class="fas fa-home"></i>
                                    <span>Overview</span>
                                </a>
                                <a class="alliance-nav-item <?php echo $page == 'manage' ? 'active' : ''; ?>" href="?page=manage">
                                    <i class="fas fa-cogs"></i>
                                    <span>Manage</span>
                                </a>
                                <?php if ($is_leader): ?>
                                <a class="alliance-nav-item <?php echo $page == 'requests' ? 'active' : ''; ?>" href="?page=requests">
                                    <i class="fas fa-envelope"></i>
                                    <span>Requests</span>
                                    <?php if (count($pending_requests) > 0): ?>
                                    <span class="badge bg-danger"><?php echo count($pending_requests); ?></span>
                                    <?php endif; ?>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($page == 'overview'): ?>
        <!-- Alliance Benefits -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h6>Active Alliances</h6>
                        <h4><?php echo count($alliances); ?> / <?php echo $benefits['max_alliances']; ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h6>Defense Bonus</h6>
                        <h4>+<?php echo $benefits['defense_bonus']; ?>%</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h6>Trade Discount</h6>
                        <h4>-<?php echo $benefits['trade_discount']; ?>%</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h6>Allied Power</h6>
                        <h4><?php 
                            $total_members = 0;
                            foreach ($alliances as $alliance) {
                                $total_members += $alliance['ally_members'];
                            }
                            echo number_format($total_members);
                        ?> members</h4>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Current Alliances -->
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-handshake"></i> Current Alliances</h5>
            </div>
            <div class="card-body">
                <?php if (empty($alliances)): ?>
                    <p class="text-muted">Your guild has no active alliances.</p>
                    <?php if ($is_leader): ?>
                    <a href="?page=manage" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Form Alliance
                    </a>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Allied Guild</th>
                                    <th>Level</th>
                                    <th>Members</th>
                                    <th>Alliance Formed</th>
                                    <th>Benefits</th>
                                    <?php if ($is_leader): ?>
                                    <th>Action</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($alliances as $alliance): ?>
                                <tr>
                                    <td><strong><?php echo $alliance['ally_name']; ?></strong></td>
                                    <td><?php echo $alliance['ally_level']; ?></td>
                                    <td><?php echo $alliance['ally_members']; ?></td>
                                    <td><?php echo date('M j, Y', $alliance['alliance_formed']); ?></td>
                                    <td>
                                        <span class="badge bg-success">+5% Defense</span>
                                        <span class="badge bg-info">-2% Trade</span>
                                    </td>
                                    <?php if ($is_leader): ?>
                                    <td>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to break this alliance?');">
                                            <input type="hidden" name="action" value="break_alliance">
                                            <input type="hidden" name="alliance_id" value="<?php echo $alliance['alliance_id']; ?>">
                                            <?php echo getHtmlCSRF('break_' . $alliance['alliance_id']); ?>
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-times"></i> Break
                                            </button>
                                        </form>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
    <?php elseif ($page == 'manage' && $is_leader): ?>
        <!-- Form New Alliance -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-plus"></i> Request New Alliance</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($alliances) >= $benefits['max_alliances']): ?>
                            <div class="alert alert-warning">
                                You have reached the maximum number of alliances (<?php echo $benefits['max_alliances']; ?>).
                                Break an existing alliance to form a new one.
                            </div>
                        <?php else: ?>
                            <form method="POST">
                                <input type="hidden" name="action" value="request_alliance">
                                <?php echo getHtmlCSRF('request_alliance'); ?>
                                
                                <div class="mb-3">
                                    <label for="target_guild" class="form-label">Select Guild:</label>
                                    <select name="target_guild" id="target_guild" class="form-select" required>
                                        <option value="">-- Choose a guild --</option>
                                        <?php
                                        // Get guilds not already allied
                                        $allied_ids = array_map(function($a) { return $a['ally_id']; }, $alliances);
                                        $allied_ids[] = $ir['guild']; // Exclude own guild
                                        $allied_list = implode(',', $allied_ids);
                                        
                                        $available_guilds = $db->query("
                                            SELECT guild_id, guild_name, guild_level,
                                                   (SELECT COUNT(*) FROM users WHERE guild = g.guild_id) as member_count
                                            FROM guild g
                                            WHERE guild_id NOT IN ({$allied_list})
                                            ORDER BY guild_name
                                        ");
                                        
                                        while ($g = $db->fetch_row($available_guilds)):
                                        ?>
                                        <option value="<?php echo $g['guild_id']; ?>">
                                            <?php echo $g['guild_name']; ?> (Level <?php echo $g['guild_level']; ?>, <?php echo $g['member_count']; ?> members)
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="message" class="form-label">Message (optional):</label>
                                    <textarea name="message" id="message" class="form-control" rows="3" 
                                              placeholder="Explain why you want to form this alliance..."></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Send Alliance Request
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle"></i> Alliance Benefits</h5>
                    </div>
                    <div class="card-body">
                        <h6>Benefits per Alliance:</h6>
                        <ul>
                            <li><strong>+5% Defense Bonus:</strong> In faction warfare and PvP</li>
                            <li><strong>-2% Trade Discount:</strong> When trading with allied guilds</li>
                            <li><strong>Shared Victories:</strong> Gain reputation from ally victories</li>
                            <li><strong>Territory Support:</strong> Allies can help defend territories</li>
                            <li><strong>Joint Operations:</strong> Coordinate attacks and strategies</li>
                        </ul>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-lightbulb"></i> <strong>Tip:</strong> 
                            Choose your allies wisely. Strong alliances can turn the tide of war!
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    <?php elseif ($page == 'requests' && $is_leader): ?>
        <!-- Pending Requests -->
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-envelope"></i> Alliance Requests</h5>
            </div>
            <div class="card-body">
                <?php if (empty($pending_requests)): ?>
                    <p class="text-muted">No pending alliance requests.</p>
                <?php else: ?>
                    <?php foreach ($pending_requests as $request): ?>
                    <div class="card mb-3 <?php echo $request['type'] == 'incoming' ? 'border-primary' : 'border-secondary'; ?>">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6>
                                        <?php if ($request['type'] == 'incoming'): ?>
                                            <span class="badge bg-primary">Incoming</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Outgoing</span>
                                        <?php endif; ?>
                                        <?php echo $request['guild_name']; ?>
                                    </h6>
                                    <p class="mb-0">
                                        Level <?php echo $request['guild_level']; ?> • 
                                        <?php echo $request['member_count']; ?> members • 
                                        Requested <?php echo date('M j, Y', $request['alliance_requested']); ?>
                                    </p>
                                    <?php if (!empty($request['alliance_message'])): ?>
                                    <p class="mt-2 mb-0">
                                        <em>"<?php echo htmlspecialchars($request['alliance_message']); ?>"</em>
                                    </p>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <?php if ($request['type'] == 'incoming'): ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="accept_alliance">
                                            <input type="hidden" name="alliance_id" value="<?php echo $request['alliance_id']; ?>">
                                            <?php echo getHtmlCSRF('accept_' . $request['alliance_id']); ?>
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-check"></i> Accept
                                            </button>
                                        </form>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="reject_alliance">
                                            <input type="hidden" name="alliance_id" value="<?php echo $request['alliance_id']; ?>">
                                            <?php echo getHtmlCSRF('reject_' . $request['alliance_id']); ?>
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">Waiting for response...</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
    <?php elseif (!$is_leader): ?>
        <!-- Non-leader view -->
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Only guild leaders and co-leaders can manage alliances.
        </div>
    <?php endif; ?>
    
    <!-- Info -->
    <div class="card mt-4">
        <div class="card-body">
            <h5><i class="fas fa-info-circle"></i> Alliance System Guide</h5>
            <ul>
                <li><strong>Maximum Alliances:</strong> Each guild can have up to 5 active alliances</li>
                <li><strong>Leadership Required:</strong> Only guild leaders and co-leaders can manage alliances</li>
                <li><strong>Cumulative Benefits:</strong> Benefits stack with each alliance</li>
                <li><strong>Breaking Alliances:</strong> Can be done at any time but may affect reputation</li>
                <li><strong>War Coordination:</strong> Allied guilds cannot declare war on each other</li>
            </ul>
        </div>
    </div>
</div>

<style>
/* Alliance Navigation */
.alliance-nav {
    display: inline-flex;
    background: rgba(255,255,255,0.1);
    border-radius: 12px;
    padding: 4px;
    backdrop-filter: blur(10px);
}

.alliance-nav-item {
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
    position: relative;
}

.alliance-nav-item i {
    font-size: 1.25rem;
    margin-bottom: 4px;
}

.alliance-nav-item span:not(.badge) {
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.alliance-nav-item .badge {
    position: absolute;
    top: 0;
    right: 0;
    font-size: 0.625rem;
}

.alliance-nav-item:hover {
    background: rgba(255,255,255,0.2);
    color: #fff;
    transform: translateY(-2px);
    text-decoration: none;
}

.alliance-nav-item.active {
    background: rgba(255,255,255,0.95);
    color: #17a2b8;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
}

@media (max-width: 768px) {
    .alliance-nav {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 4px;
        width: 100%;
        margin-top: 1rem;
    }
}
</style>

<?php
$h->endpage();
?>