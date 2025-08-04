<?php
/*
    File: events.php
    Created: Live Events Calendar
    Info: View and participate in special game events
*/
require_once('globals.php');

class EventSystem {
    private $db;
    private $userid;
    
    public function __construct($db, $userid) {
        $this->db = $db;
        $this->userid = $userid;
    }
    
    /**
     * Get active events
     */
    public function getActiveEvents() {
        $events = [];
        $query = $this->db->query("
            SELECT e.*,
                   (SELECT COUNT(*) FROM event_participation 
                    WHERE ep_event = e.event_id) as total_participants,
                   (SELECT ep_points FROM event_participation 
                    WHERE ep_event = e.event_id AND ep_user = {$this->userid}) as user_points
            FROM events e
            WHERE e.event_active = 1
                AND NOW() BETWEEN e.event_starts AND e.event_ends
            ORDER BY e.event_starts DESC
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            // Parse multipliers
            $row['multipliers'] = json_decode($row['event_multipliers'], true);
            $row['special_rewards'] = json_decode($row['event_special_rewards'], true);
            $row['time_remaining'] = strtotime($row['event_ends']) - time();
            $events[] = $row;
        }
        
        return $events;
    }
    
    /**
     * Get upcoming events
     */
    public function getUpcomingEvents() {
        $events = [];
        $query = $this->db->query("
            SELECT * FROM events 
            WHERE event_active = 1 
                AND event_starts > NOW()
            ORDER BY event_starts ASC
            LIMIT 10
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            $row['time_until'] = strtotime($row['event_starts']) - time();
            $events[] = $row;
        }
        
        return $events;
    }
    
    /**
     * Get past events
     */
    public function getPastEvents() {
        $events = [];
        $query = $this->db->query("
            SELECT e.*,
                   (SELECT COUNT(*) FROM event_participation 
                    WHERE ep_event = e.event_id) as total_participants
            FROM events e
            WHERE e.event_ends < NOW()
            ORDER BY e.event_ends DESC
            LIMIT 20
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            $events[] = $row;
        }
        
        return $events;
    }
    
    /**
     * Join an event
     */
    public function joinEvent($event_id) {
        // Check if event exists and is active
        $event = $this->db->fetch_row($this->db->query("
            SELECT * FROM events 
            WHERE event_id = {$event_id} 
                AND event_active = 1
                AND NOW() BETWEEN event_starts AND event_ends
        "));
        
        if (!$event) {
            return ['success' => false, 'message' => 'Event not found or not active!'];
        }
        
        // Check if already participating
        $participating = $this->db->fetch_single($this->db->query("
            SELECT COUNT(*) FROM event_participation 
            WHERE ep_event = {$event_id} AND ep_user = {$this->userid}
        "));
        
        if ($participating > 0) {
            return ['success' => false, 'message' => 'You are already participating in this event!'];
        }
        
        // Join event
        $this->db->query("
            INSERT INTO event_participation (ep_event, ep_user, ep_points)
            VALUES ({$event_id}, {$this->userid}, 0)
        ");
        
        return ['success' => true, 'message' => "Joined {$event['event_name']}!"];
    }
    
    /**
     * Get event leaderboard
     */
    public function getEventLeaderboard($event_id) {
        $leaders = [];
        $query = $this->db->query("
            SELECT ep.*, u.username
            FROM event_participation ep
            INNER JOIN users u ON ep.ep_user = u.userid
            WHERE ep.ep_event = {$event_id}
            ORDER BY ep.ep_points DESC
            LIMIT 20
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            $leaders[] = $row;
        }
        
        return $leaders;
    }
}

// Initialize system
$event_system = new EventSystem($db, $userid);

// Handle actions
if (isset($_POST['join_event'])) {
    $event_id = abs((int)$_POST['event_id']);
    $result = $event_system->joinEvent($event_id);
    
    alert($result['success'] ? 'success' : 'danger',
          $result['success'] ? 'Joined!' : 'Failed!',
          $result['message'], false);
}

// Get data
$active_events = $event_system->getActiveEvents();
$upcoming_events = $event_system->getUpcomingEvents();
$past_events = $event_system->getPastEvents();

?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-info text-white">
                <div class="card-body">
                    <h2 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Live Events</h2>
                    <p class="mb-0 mt-2">Special events with bonuses and exclusive rewards!</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Active Events -->
    <?php if (!empty($active_events)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <h4><i class="fas fa-fire text-danger"></i> Active Events</h4>
        </div>
        <?php foreach ($active_events as $event): 
            $type_colors = [
                'seasonal' => 'success',
                'weekend' => 'primary',
                'flash' => 'warning',
                'community' => 'info'
            ];
            $color = $type_colors[$event['event_type']] ?? 'secondary';
        ?>
        <div class="col-lg-6 mb-3">
            <div class="card border-<?php echo $color; ?>">
                <div class="card-header bg-<?php echo $color; ?> text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?php echo $event['event_name']; ?></h5>
                        <span class="badge bg-light text-dark">
                            <?php echo ucfirst($event['event_type']); ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <p><?php echo $event['event_desc']; ?></p>
                    
                    <?php if ($event['multipliers']): ?>
                    <div class="mb-3">
                        <strong>Active Bonuses:</strong>
                        <ul class="mb-0">
                            <?php foreach ($event['multipliers'] as $mult => $value): ?>
                            <li><?php echo ucfirst($mult); ?>: <span class="text-success">x<?php echo $value; ?></span></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <strong>Time Remaining:</strong> 
                        <span class="text-danger"><?php echo timeUntilParse($event['time_remaining']); ?></span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Participants:</strong> <?php echo number_format($event['total_participants']); ?>
                    </div>
                    
                    <?php if ($event['user_points'] !== null): ?>
                        <div class="alert alert-success py-2">
                            <i class="fas fa-check"></i> Participating - Your Points: <?php echo number_format($event['user_points']); ?>
                        </div>
                        <a href="?leaderboard=<?php echo $event['event_id']; ?>" class="btn btn-info">
                            <i class="fas fa-trophy"></i> View Leaderboard
                        </a>
                    <?php else: ?>
                        <form method="POST">
                            <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                            <input type="hidden" name="join_event" value="1">
                            <?php echo getHtmlCSRF('join_event'); ?>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-sign-in-alt"></i> Join Event
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <!-- Upcoming Events -->
    <?php if (!empty($upcoming_events)): ?>
    <div class="card mb-4">
        <div class="card-header">
            <h4><i class="fas fa-clock"></i> Upcoming Events</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Type</th>
                            <th>Starts In</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($upcoming_events as $event): ?>
                        <tr>
                            <td>
                                <strong><?php echo $event['event_name']; ?></strong><br>
                                <small class="text-muted"><?php echo $event['event_desc']; ?></small>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?php echo ucfirst($event['event_type']); ?></span>
                            </td>
                            <td><?php echo timeUntilParse($event['time_until']); ?></td>
                            <td>
                                <?php 
                                $duration = strtotime($event['event_ends']) - strtotime($event['event_starts']);
                                echo timeUntilParse($duration);
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Event Leaderboard -->
    <?php if (isset($_GET['leaderboard'])): 
        $event_id = abs((int)$_GET['leaderboard']);
        $leaderboard = $event_system->getEventLeaderboard($event_id);
    ?>
    <div class="card mb-4">
        <div class="card-header">
            <h4><i class="fas fa-trophy"></i> Event Leaderboard</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Player</th>
                            <th>Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rank = 1;
                        foreach ($leaderboard as $player): 
                        ?>
                        <tr <?php echo $player['ep_user'] == $userid ? 'class="table-primary"' : ''; ?>>
                            <td>
                                <?php 
                                if ($rank == 1) echo '<i class="fas fa-trophy text-warning"></i> ';
                                elseif ($rank == 2) echo '<i class="fas fa-medal text-secondary"></i> ';
                                elseif ($rank == 3) echo '<i class="fas fa-medal" style="color: #cd7f32;"></i> ';
                                echo $rank;
                                ?>
                            </td>
                            <td>
                                <?php echo $player['username']; ?>
                                <?php if ($player['ep_user'] == $userid): ?>
                                    <span class="badge bg-primary">You</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo number_format($player['ep_points']); ?></td>
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
    <?php endif; ?>
    
    <!-- Past Events -->
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-history"></i> Past Events</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Type</th>
                            <th>Ended</th>
                            <th>Participants</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($past_events as $event): ?>
                        <tr>
                            <td><?php echo $event['event_name']; ?></td>
                            <td>
                                <span class="badge bg-secondary"><?php echo ucfirst($event['event_type']); ?></span>
                            </td>
                            <td><?php echo timeUntilParse(time() - strtotime($event['event_ends'])); ?> ago</td>
                            <td><?php echo number_format($event['total_participants']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$h->endpage();
?>