<?php
/*
    File: battle_royale.php
    Created: Battle Royale PvP Events System
    Info: Join timed battle royale events and be the last player standing
*/
require_once('globals.php');

class BattleRoyaleSystem {
    private $db;
    private $userid;
    private $api;
    
    public function __construct($db, $userid, $api) {
        $this->db = $db;
        $this->userid = $userid;
        $this->api = $api;
    }
    
    /**
     * Get current and upcoming events
     */
    public function getEvents() {
        $events = [];
        $query = $this->db->query("
            SELECT e.*, 
                   COUNT(DISTINCT p.brp_user) as registered_players,
                   (SELECT COUNT(*) FROM battle_royale_participants 
                    WHERE brp_event = e.br_id AND brp_status = 'winner') as has_winner
            FROM battle_royale_events e
            LEFT JOIN battle_royale_participants p ON e.br_id = p.brp_event
            WHERE e.br_status IN ('scheduled', 'registration', 'active')
            GROUP BY e.br_id
            ORDER BY e.br_scheduled_start ASC
            LIMIT 10
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            $events[] = $row;
        }
        
        return $events;
    }
    
    /**
     * Get user's battle royale stats
     */
    public function getUserStats() {
        $stats = $this->db->fetch_row($this->db->query("
            SELECT * FROM battle_royale_stats WHERE brs_user = {$this->userid}
        "));
        
        if (!$stats) {
            // Create default stats
            $this->db->query("
                INSERT INTO battle_royale_stats (brs_user) VALUES ({$this->userid})
            ");
            $stats = [
                'brs_events_joined' => 0,
                'brs_wins' => 0,
                'brs_top_3' => 0,
                'brs_top_10' => 0,
                'brs_total_kills' => 0,
                'brs_rating' => 1000
            ];
        }
        
        return $stats;
    }
    
    /**
     * Register for an event
     */
    public function registerForEvent($event_id) {
        global $ir;
        
        // Get event details
        $event = $this->db->fetch_row($this->db->query("
            SELECT * FROM battle_royale_events WHERE br_id = {$event_id}
        "));
        
        if (!$event) {
            return ['success' => false, 'message' => 'Event not found!'];
        }
        
        if ($event['br_status'] != 'registration') {
            return ['success' => false, 'message' => 'Registration is not open for this event!'];
        }
        
        // Check level requirements
        if ($ir['level'] < $event['br_level_min'] || $ir['level'] > $event['br_level_max']) {
            return ['success' => false, 'message' => "Your level doesn't meet the requirements!"];
        }
        
        // Check entry fee
        if ($event['br_entry_fee'] > 0) {
            if ($ir['primary_currency'] < $event['br_entry_fee']) {
                return ['success' => false, 'message' => "You need " . number_format($event['br_entry_fee']) . " gold to enter!"];
            }
        }
        
        // Check if already registered
        $registered = $this->db->fetch_single($this->db->query("
            SELECT COUNT(*) FROM battle_royale_participants 
            WHERE brp_event = {$event_id} AND brp_user = {$this->userid}
        "));
        
        if ($registered > 0) {
            return ['success' => false, 'message' => 'You are already registered for this event!'];
        }
        
        // Register player
        $this->db->query("
            INSERT INTO battle_royale_participants 
            (brp_event, brp_user, brp_status) 
            VALUES ({$event_id}, {$this->userid}, 'registered')
        ");
        
        // Deduct entry fee
        if ($event['br_entry_fee'] > 0) {
            $this->db->query("
                UPDATE users SET primary_currency = primary_currency - {$event['br_entry_fee']} 
                WHERE userid = {$this->userid}
            ");
            
            // Add to prize pool
            $this->db->query("
                UPDATE battle_royale_events 
                SET br_prize_pool = br_prize_pool + {$event['br_entry_fee']} 
                WHERE br_id = {$event_id}
            ");
        }
        
        return ['success' => true, 'message' => "Successfully registered for {$event['br_name']}!"];
    }
    
    /**
     * Get recent winners
     */
    public function getRecentWinners() {
        $winners = [];
        $query = $this->db->query("
            SELECT p.*, u.username, e.br_name, e.br_prize_pool
            FROM battle_royale_participants p
            INNER JOIN users u ON p.brp_user = u.userid
            INNER JOIN battle_royale_events e ON p.brp_event = e.br_id
            WHERE p.brp_status = 'winner'
            ORDER BY p.brp_eliminated_at DESC
            LIMIT 10
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            $winners[] = $row;
        }
        
        return $winners;
    }
    
    /**
     * Get top players leaderboard
     */
    public function getLeaderboard() {
        $leaders = [];
        $query = $this->db->query("
            SELECT s.*, u.username
            FROM battle_royale_stats s
            INNER JOIN users u ON s.brs_user = u.userid
            WHERE s.brs_events_joined > 0
            ORDER BY s.brs_rating DESC, s.brs_wins DESC
            LIMIT 20
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            $leaders[] = $row;
        }
        
        return $leaders;
    }
}

// Initialize system
$br_system = new BattleRoyaleSystem($db, $userid, $api);

// Handle registration
if (isset($_POST['register_event'])) {
    $event_id = abs((int)$_POST['event_id']);
    $result = $br_system->registerForEvent($event_id);
    
    alert($result['success'] ? 'success' : 'danger', 
          $result['success'] ? 'Registered!' : 'Failed!', 
          $result['message'], false);
}

// Get data
$events = $br_system->getEvents();
$user_stats = $br_system->getUserStats();
$recent_winners = $br_system->getRecentWinners();
$leaderboard = $br_system->getLeaderboard();

?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-danger text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h2 class="mb-0"><i class="fas fa-crown me-2"></i>Battle Royale</h2>
                            <p class="mb-0 mt-2">Fight to be the last player standing!</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="d-inline-block text-center mx-2">
                                <h4 class="mb-0"><?php echo $user_stats['brs_wins']; ?></h4>
                                <small>Wins</small>
                            </div>
                            <div class="d-inline-block text-center mx-2">
                                <h4 class="mb-0"><?php echo $user_stats['brs_total_kills']; ?></h4>
                                <small>Total Kills</small>
                            </div>
                            <div class="d-inline-block text-center mx-2">
                                <h4 class="mb-0"><?php echo number_format($user_stats['brs_rating']); ?></h4>
                                <small>Rating</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Current/Upcoming Events -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-calendar"></i> Upcoming Events</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($events)): ?>
                        <p class="text-muted">No events scheduled. Check back later!</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Event</th>
                                        <th>Type</th>
                                        <th>Start Time</th>
                                        <th>Players</th>
                                        <th>Prize Pool</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($events as $event): 
                                        $can_register = $event['br_status'] == 'registration';
                                        $is_active = $event['br_status'] == 'active';
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo $event['br_name']; ?></strong>
                                            <br><small><?php echo $event['br_desc']; ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo ucfirst($event['br_type']); ?></span>
                                        </td>
                                        <td>
                                            <?php 
                                            $start_time = strtotime($event['br_scheduled_start']);
                                            if ($start_time > time()) {
                                                echo "In " . timeUntilParse($start_time - time());
                                            } else {
                                                echo "Started";
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo $event['registered_players']; ?>/<?php echo $event['br_max_players']; ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-coins text-warning"></i> 
                                            <?php echo number_format($event['br_prize_pool']); ?>
                                        </td>
                                        <td>
                                            <?php if ($is_active): ?>
                                                <span class="badge bg-danger">IN PROGRESS</span>
                                            <?php elseif ($can_register): ?>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="event_id" value="<?php echo $event['br_id']; ?>">
                                                    <input type="hidden" name="register_event" value="1">
                                                    <?php echo getHtmlCSRF('battle_royale_register'); ?>
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="fas fa-sign-in-alt"></i> Register
                                                        <?php if ($event['br_entry_fee'] > 0): ?>
                                                            (<?php echo number_format($event['br_entry_fee']); ?> gold)
                                                        <?php else: ?>
                                                            (Free)
                                                        <?php endif; ?>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Waiting</span>
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
            
            <!-- Recent Winners -->
            <div class="card mt-4">
                <div class="card-header">
                    <h4><i class="fas fa-trophy"></i> Recent Winners</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($recent_winners)): ?>
                        <p class="text-muted">No winners yet!</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($recent_winners as $winner): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-crown text-warning"></i>
                                        <strong><?php echo $winner['username']; ?></strong> won 
                                        <span class="text-info"><?php echo $winner['br_name']; ?></span>
                                        <br>
                                        <small class="text-muted">
                                            <?php echo $winner['brp_kills']; ?> kills • 
                                            Prize: <?php echo number_format($winner['br_prize_pool']); ?> gold
                                        </small>
                                    </div>
                                    <small class="text-muted">
                                        <?php echo timeUntilParse(time() - strtotime($winner['brp_eliminated_at'])); ?> ago
                                    </small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Leaderboard & Stats -->
        <div class="col-lg-4">
            <!-- Your Stats -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5><i class="fas fa-chart-line"></i> Your Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <h4><?php echo $user_stats['brs_events_joined']; ?></h4>
                            <small class="text-muted">Events Played</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h4><?php echo $user_stats['brs_wins']; ?></h4>
                            <small class="text-muted">Victories</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h4><?php echo $user_stats['brs_top_3']; ?></h4>
                            <small class="text-muted">Top 3 Finishes</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h4><?php echo $user_stats['brs_top_10']; ?></h4>
                            <small class="text-muted">Top 10 Finishes</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h4><?php echo $user_stats['brs_total_kills']; ?></h4>
                            <small class="text-muted">Total Eliminations</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h4><?php echo number_format($user_stats['brs_rating']); ?></h4>
                            <small class="text-muted">Battle Rating</small>
                        </div>
                    </div>
                    
                    <?php if ($user_stats['brs_events_joined'] > 0): ?>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-success" style="width: <?php echo min(100, ($user_stats['brs_wins'] / $user_stats['brs_events_joined']) * 100); ?>%">
                            Win Rate: <?php echo round(($user_stats['brs_wins'] / $user_stats['brs_events_joined']) * 100, 1); ?>%
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Top Players -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-medal"></i> Top Players</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php 
                        $rank = 1;
                        foreach ($leaderboard as $player): 
                            $medal = '';
                            if ($rank == 1) $medal = '<i class="fas fa-medal text-warning"></i>';
                            elseif ($rank == 2) $medal = '<i class="fas fa-medal text-secondary"></i>';
                            elseif ($rank == 3) $medal = '<i class="fas fa-medal" style="color: #cd7f32;"></i>';
                        ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="me-2"><?php echo $medal ?: $rank; ?>.</span>
                                <strong><?php echo $player['username']; ?></strong>
                                <?php if ($player['brs_user'] == $userid): ?>
                                    <span class="badge bg-primary">You</span>
                                <?php endif; ?>
                                <br>
                                <small class="text-muted">
                                    <?php echo $player['brs_wins']; ?> wins • 
                                    <?php echo number_format($player['brs_rating']); ?> rating
                                </small>
                            </div>
                        </div>
                        <?php 
                        $rank++;
                        endforeach; 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Event Schedule -->
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-clock"></i> Event Schedule</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center p-3 border rounded">
                        <i class="fas fa-clock text-info" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">Hourly Skirmish</h6>
                        <small>Every hour • 5-25 players • 100 gold entry</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3 border rounded">
                        <i class="fas fa-sun text-warning" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">Daily Mayhem</h6>
                        <small>8 PM Daily • 20-50 players • 500 gold entry</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3 border rounded">
                        <i class="fas fa-star text-danger" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">Weekend War</h6>
                        <small>Saturday 9 PM • 30-100 players • 1,000 gold entry</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center p-3 border rounded">
                        <i class="fas fa-crown text-success" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">Sunday Showdown</h6>
                        <small>Sunday 7 PM • 25-75 players • 750 gold entry</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$h->endpage();
?>