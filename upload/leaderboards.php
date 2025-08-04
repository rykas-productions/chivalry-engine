<?php
/*
    File: leaderboards.php
    Created: Leaderboards & Seasons System
    Info: View global rankings and seasonal competitions
*/
require_once('globals.php');

class LeaderboardSystem {
    private $db;
    private $userid;
    
    public function __construct($db, $userid) {
        $this->db = $db;
        $this->userid = $userid;
    }
    
    /**
     * Get leaderboard data for a specific category
     */
    public function getLeaderboard($category, $limit = 100) {
        $leaderboard = [];
        
        switch($category) {
            case 'level':
                $query = $this->db->query("
                    SELECT u.userid, u.username, u.level, u.exp,
                           g.guild_name,
                           (SELECT COUNT(*) FROM users WHERE level > u.level) + 1 as rank
                    FROM users u
                    LEFT JOIN guilds g ON u.guild = g.guild_id
                    ORDER BY u.level DESC, u.exp DESC
                    LIMIT {$limit}
                ");
                break;
                
            case 'money':
                $query = $this->db->query("
                    SELECT u.userid, u.username, u.primary_currency as value,
                           g.guild_name,
                           (SELECT COUNT(*) FROM users WHERE primary_currency > u.primary_currency) + 1 as rank
                    FROM users u
                    LEFT JOIN guilds g ON u.guild = g.guild_id
                    ORDER BY u.primary_currency DESC
                    LIMIT {$limit}
                ");
                break;
                
            case 'stats':
                $query = $this->db->query("
                    SELECT u.userid, u.username, 
                           (us.strength + us.agility + us.guard + us.labor + us.iq) as total_stats,
                           us.strength, us.agility, us.guard,
                           g.guild_name,
                           (SELECT COUNT(*) FROM userstats us2 
                            WHERE (us2.strength + us2.agility + us2.guard + us2.labor + us2.iq) > 
                                  (us.strength + us.agility + us.guard + us.labor + us.iq)) + 1 as rank
                    FROM users u
                    INNER JOIN userstats us ON u.userid = us.userid
                    LEFT JOIN guilds g ON u.guild = g.guild_id
                    ORDER BY total_stats DESC
                    LIMIT {$limit}
                ");
                break;
                
            case 'crimes':
                $query = $this->db->query("
                    SELECT u.userid, u.username, u.crimesuccess as value,
                           g.guild_name,
                           (SELECT COUNT(*) FROM users WHERE crimesuccess > u.crimesuccess) + 1 as rank
                    FROM users u
                    LEFT JOIN guilds g ON u.guild = g.guild_id
                    WHERE u.crimesuccess > 0
                    ORDER BY u.crimesuccess DESC
                    LIMIT {$limit}
                ");
                break;
                
            case 'gym':
                $query = $this->db->query("
                    SELECT u.userid, u.username, u.gym_trains as value,
                           g.guild_name,
                           (SELECT COUNT(*) FROM users WHERE gym_trains > u.gym_trains) + 1 as rank
                    FROM users u
                    LEFT JOIN guilds g ON u.guild = g.guild_id
                    WHERE u.gym_trains > 0
                    ORDER BY u.gym_trains DESC
                    LIMIT {$limit}
                ");
                break;
                
            case 'achievements':
                $query = $this->db->query("
                    SELECT u.userid, u.username, 
                           COUNT(ua.ua_id) as achievements_earned,
                           SUM(a.achievement_points) as total_points,
                           g.guild_name,
                           (SELECT COUNT(DISTINCT ua2.ua_user) FROM user_achievements ua2
                            INNER JOIN achievements a2 ON ua2.ua_achievement = a2.achievement_id
                            GROUP BY ua2.ua_user
                            HAVING SUM(a2.achievement_points) > SUM(a.achievement_points)) + 1 as rank
                    FROM users u
                    LEFT JOIN user_achievements ua ON u.userid = ua.ua_user
                    LEFT JOIN achievements a ON ua.ua_achievement = a.achievement_id
                    LEFT JOIN guilds g ON u.guild = g.guild_id
                    GROUP BY u.userid
                    ORDER BY total_points DESC, achievements_earned DESC
                    LIMIT {$limit}
                ");
                break;
                
            case 'guild_wars':
                $query = $this->db->query("
                    SELECT u.userid, u.username, u.guild_war_score as value,
                           u.guild_war_kills, u.guild_wars_participated,
                           g.guild_name,
                           (SELECT COUNT(*) FROM users WHERE guild_war_score > u.guild_war_score) + 1 as rank
                    FROM users u
                    LEFT JOIN guilds g ON u.guild = g.guild_id
                    WHERE u.guild_war_score > 0
                    ORDER BY u.guild_war_score DESC
                    LIMIT {$limit}
                ");
                break;
                
            case 'pets':
                $query = $this->db->query("
                    SELECT u.userid, u.username,
                           COUNT(p.pet_id) as total_pets,
                           SUM(p.pet_level) as combined_levels,
                           MAX(p.pet_level) as highest_level,
                           g.guild_name,
                           (SELECT COUNT(DISTINCT p2.pet_owner) FROM pets p2
                            GROUP BY p2.pet_owner
                            HAVING SUM(p2.pet_level) > SUM(p.pet_level)) + 1 as rank
                    FROM users u
                    INNER JOIN pets p ON u.userid = p.pet_owner
                    LEFT JOIN guilds g ON u.guild = g.guild_id
                    GROUP BY u.userid
                    ORDER BY combined_levels DESC, total_pets DESC
                    LIMIT {$limit}
                ");
                break;
                
            case 'dungeons':
                $query = $this->db->query("
                    SELECT u.userid, u.username,
                           COUNT(DISTINCT dr.dr_dungeon) as dungeons_completed,
                           SUM(dr.dr_boss_kills) as total_boss_kills,
                           MAX(d.dungeon_difficulty) as highest_difficulty,
                           g.guild_name,
                           (SELECT COUNT(DISTINCT dr2.dr_user) FROM dungeon_runs dr2
                            WHERE dr2.dr_status = 'completed'
                            GROUP BY dr2.dr_user
                            HAVING COUNT(DISTINCT dr2.dr_dungeon) > COUNT(DISTINCT dr.dr_dungeon)) + 1 as rank
                    FROM users u
                    INNER JOIN dungeon_runs dr ON u.userid = dr.dr_user
                    INNER JOIN dungeons d ON dr.dr_dungeon = d.dungeon_id
                    LEFT JOIN guilds g ON u.guild = g.guild_id
                    WHERE dr.dr_status = 'completed'
                    GROUP BY u.userid
                    ORDER BY dungeons_completed DESC, total_boss_kills DESC
                    LIMIT {$limit}
                ");
                break;
                
            case 'guilds':
                $query = $this->db->query("
                    SELECT g.guild_id, g.guild_name, g.guild_level,
                           COUNT(u.userid) as member_count,
                           g.guild_treasury, g.guild_territories_owned,
                           g.guild_war_rating,
                           (SELECT COUNT(*) FROM guilds g2 WHERE g2.guild_level > g.guild_level) + 1 as rank
                    FROM guilds g
                    LEFT JOIN users u ON g.guild_id = u.guild
                    GROUP BY g.guild_id
                    ORDER BY g.guild_level DESC, g.guild_war_rating DESC
                    LIMIT {$limit}
                ");
                break;
        }
        
        while ($row = $this->db->fetch_row($query)) {
            $leaderboard[] = $row;
        }
        
        return $leaderboard;
    }
    
    /**
     * Get current season info
     */
    public function getCurrentSeason() {
        $season = $this->db->fetch_row($this->db->query("
            SELECT * FROM seasons 
            WHERE season_active = 1 
                AND NOW() BETWEEN season_start AND season_end
            LIMIT 1
        "));
        
        if ($season) {
            $season['days_remaining'] = floor((strtotime($season['season_end']) - time()) / 86400);
            $season['progress'] = ((time() - strtotime($season['season_start'])) / 
                                  (strtotime($season['season_end']) - strtotime($season['season_start']))) * 100;
        }
        
        return $season;
    }
    
    /**
     * Get seasonal leaderboard
     */
    public function getSeasonalLeaderboard($season_id, $limit = 100) {
        $leaderboard = [];
        $query = $this->db->query("
            SELECT sp.*, u.username, g.guild_name,
                   (SELECT COUNT(*) FROM season_participants sp2 
                    WHERE sp2.sp_season = sp.sp_season 
                      AND sp2.sp_points > sp.sp_points) + 1 as rank
            FROM season_participants sp
            INNER JOIN users u ON sp.sp_user = u.userid
            LEFT JOIN guilds g ON u.guild = g.guild_id
            WHERE sp.sp_season = {$season_id}
            ORDER BY sp.sp_points DESC, sp.sp_wins DESC
            LIMIT {$limit}
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            $leaderboard[] = $row;
        }
        
        return $leaderboard;
    }
    
    /**
     * Get user's rankings across all categories
     */
    public function getUserRankings($user_id) {
        $rankings = [];
        
        // Level rank
        $rankings['level'] = $this->db->fetch_single($this->db->query("
            SELECT COUNT(*) + 1 FROM users 
            WHERE level > (SELECT level FROM users WHERE userid = {$user_id})
        "));
        
        // Money rank
        $rankings['money'] = $this->db->fetch_single($this->db->query("
            SELECT COUNT(*) + 1 FROM users 
            WHERE primary_currency > (SELECT primary_currency FROM users WHERE userid = {$user_id})
        "));
        
        // Stats rank
        $rankings['stats'] = $this->db->fetch_single($this->db->query("
            SELECT COUNT(*) + 1 FROM userstats us1
            WHERE (strength + agility + guard + labor + iq) > 
                  (SELECT strength + agility + guard + labor + iq FROM userstats WHERE userid = {$user_id})
        "));
        
        // Achievement points rank
        $rankings['achievements'] = $this->db->fetch_single($this->db->query("
            SELECT COUNT(DISTINCT ua.ua_user) + 1
            FROM user_achievements ua
            INNER JOIN achievements a ON ua.ua_achievement = a.achievement_id
            GROUP BY ua.ua_user
            HAVING SUM(a.achievement_points) > (
                SELECT COALESCE(SUM(a2.achievement_points), 0)
                FROM user_achievements ua2
                INNER JOIN achievements a2 ON ua2.ua_achievement = a2.achievement_id
                WHERE ua2.ua_user = {$user_id}
            )
        ")) ?: 'Unranked';
        
        return $rankings;
    }
}

// Initialize system
$lb_system = new LeaderboardSystem($db, $userid);

// Get selected category
$category = $_GET['cat'] ?? 'level';
$valid_categories = ['level', 'money', 'stats', 'crimes', 'gym', 'achievements', 'guild_wars', 'pets', 'dungeons', 'guilds'];
if (!in_array($category, $valid_categories)) {
    $category = 'level';
}

// Get data
$leaderboard = $lb_system->getLeaderboard($category);
$season = $lb_system->getCurrentSeason();
$seasonal_leaderboard = $season ? $lb_system->getSeasonalLeaderboard($season['season_id']) : [];
$user_rankings = $lb_system->getUserRankings($userid);

?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-warning text-dark">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h2 class="mb-0"><i class="fas fa-trophy me-2"></i>Leaderboards</h2>
                            <p class="mb-0 mt-2">Compete for the top rankings!</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="d-inline-block text-center mx-2">
                                <h5 class="mb-0">#<?php echo $user_rankings['level']; ?></h5>
                                <small>Level Rank</small>
                            </div>
                            <div class="d-inline-block text-center mx-2">
                                <h5 class="mb-0">#<?php echo $user_rankings['money']; ?></h5>
                                <small>Wealth Rank</small>
                            </div>
                            <div class="d-inline-block text-center mx-2">
                                <h5 class="mb-0">#<?php echo $user_rankings['stats']; ?></h5>
                                <small>Stats Rank</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Current Season -->
    <?php if ($season): ?>
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5><i class="fas fa-star"></i> <?php echo $season['season_name']; ?></h5>
                    <p class="mb-2"><?php echo $season['season_desc']; ?></p>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-success" style="width: <?php echo $season['progress']; ?>%">
                            <?php echo $season['days_remaining']; ?> days remaining
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#seasonModal">
                        <i class="fas fa-list"></i> View Season Rankings
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Category Tabs -->
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item">
            <a class="nav-link <?php echo $category == 'level' ? 'active' : ''; ?>" href="?cat=level">
                <i class="fas fa-arrow-up"></i> Level
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $category == 'money' ? 'active' : ''; ?>" href="?cat=money">
                <i class="fas fa-coins"></i> Wealth
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $category == 'stats' ? 'active' : ''; ?>" href="?cat=stats">
                <i class="fas fa-chart-line"></i> Stats
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $category == 'crimes' ? 'active' : ''; ?>" href="?cat=crimes">
                <i class="fas fa-mask"></i> Crimes
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $category == 'gym' ? 'active' : ''; ?>" href="?cat=gym">
                <i class="fas fa-dumbbell"></i> Training
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $category == 'achievements' ? 'active' : ''; ?>" href="?cat=achievements">
                <i class="fas fa-award"></i> Achievements
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $category == 'guild_wars' ? 'active' : ''; ?>" href="?cat=guild_wars">
                <i class="fas fa-chess"></i> Guild Wars
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $category == 'pets' ? 'active' : ''; ?>" href="?cat=pets">
                <i class="fas fa-paw"></i> Pets
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $category == 'dungeons' ? 'active' : ''; ?>" href="?cat=dungeons">
                <i class="fas fa-dungeon"></i> Dungeons
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $category == 'guilds' ? 'active' : ''; ?>" href="?cat=guilds">
                <i class="fas fa-users"></i> Guilds
            </a>
        </li>
    </ul>
    
    <!-- Leaderboard Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="60">Rank</th>
                            <th>
                                <?php echo $category == 'guilds' ? 'Guild' : 'Player'; ?>
                            </th>
                            <?php if ($category != 'guilds'): ?>
                            <th>Guild</th>
                            <?php endif; ?>
                            
                            <?php 
                            // Category-specific columns
                            switch($category):
                                case 'level': ?>
                                    <th>Level</th>
                                    <th>Experience</th>
                                    <?php break;
                                case 'money': ?>
                                    <th>Wealth</th>
                                    <?php break;
                                case 'stats': ?>
                                    <th>Total Stats</th>
                                    <th>STR/AGI/GRD</th>
                                    <?php break;
                                case 'crimes': ?>
                                    <th>Successful Crimes</th>
                                    <?php break;
                                case 'gym': ?>
                                    <th>Training Sessions</th>
                                    <?php break;
                                case 'achievements': ?>
                                    <th>Achievements</th>
                                    <th>Points</th>
                                    <?php break;
                                case 'guild_wars': ?>
                                    <th>War Score</th>
                                    <th>Kills</th>
                                    <th>Wars</th>
                                    <?php break;
                                case 'pets': ?>
                                    <th>Total Pets</th>
                                    <th>Combined Levels</th>
                                    <th>Highest</th>
                                    <?php break;
                                case 'dungeons': ?>
                                    <th>Completed</th>
                                    <th>Boss Kills</th>
                                    <th>Max Difficulty</th>
                                    <?php break;
                                case 'guilds': ?>
                                    <th>Level</th>
                                    <th>Members</th>
                                    <th>Treasury</th>
                                    <th>Territories</th>
                                    <th>War Rating</th>
                                    <?php break;
                            endswitch;
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rank = 1;
                        foreach ($leaderboard as $entry): 
                            $is_user = ($category != 'guilds' && $entry['userid'] == $userid) || 
                                      ($category == 'guilds' && $entry['guild_id'] == $ir['guild']);
                        ?>
                        <tr <?php echo $is_user ? 'class="table-primary"' : ''; ?>>
                            <td>
                                <strong>
                                    <?php 
                                    if ($rank == 1) echo '<i class="fas fa-trophy text-warning"></i> ';
                                    elseif ($rank == 2) echo '<i class="fas fa-medal text-secondary"></i> ';
                                    elseif ($rank == 3) echo '<i class="fas fa-medal" style="color: #cd7f32;"></i> ';
                                    echo $rank;
                                    ?>
                                </strong>
                            </td>
                            <td>
                                <?php if ($category == 'guilds'): ?>
                                    <strong><?php echo $entry['guild_name']; ?></strong>
                                <?php else: ?>
                                    <a href="profile.php?user=<?php echo $entry['userid']; ?>">
                                        <?php echo $entry['username']; ?>
                                    </a>
                                    <?php if ($is_user): ?>
                                        <span class="badge bg-primary ms-1">You</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            
                            <?php if ($category != 'guilds'): ?>
                            <td>
                                <?php echo $entry['guild_name'] ?: '<span class="text-muted">None</span>'; ?>
                            </td>
                            <?php endif; ?>
                            
                            <?php 
                            // Category-specific data
                            switch($category):
                                case 'level': ?>
                                    <td><strong><?php echo $entry['level']; ?></strong></td>
                                    <td><?php echo number_format($entry['exp']); ?></td>
                                    <?php break;
                                case 'money': ?>
                                    <td><strong><?php echo number_format($entry['value']); ?></strong></td>
                                    <?php break;
                                case 'stats': ?>
                                    <td><strong><?php echo number_format($entry['total_stats']); ?></strong></td>
                                    <td><?php echo "{$entry['strength']}/{$entry['agility']}/{$entry['guard']}"; ?></td>
                                    <?php break;
                                case 'crimes': ?>
                                    <td><strong><?php echo number_format($entry['value']); ?></strong></td>
                                    <?php break;
                                case 'gym': ?>
                                    <td><strong><?php echo number_format($entry['value']); ?></strong></td>
                                    <?php break;
                                case 'achievements': ?>
                                    <td><strong><?php echo $entry['achievements_earned']; ?></strong></td>
                                    <td><strong><?php echo number_format($entry['total_points']); ?></strong></td>
                                    <?php break;
                                case 'guild_wars': ?>
                                    <td><strong><?php echo number_format($entry['value']); ?></strong></td>
                                    <td><?php echo $entry['guild_war_kills']; ?></td>
                                    <td><?php echo $entry['guild_wars_participated']; ?></td>
                                    <?php break;
                                case 'pets': ?>
                                    <td><strong><?php echo $entry['total_pets']; ?></strong></td>
                                    <td><strong><?php echo $entry['combined_levels']; ?></strong></td>
                                    <td>Lv.<?php echo $entry['highest_level']; ?></td>
                                    <?php break;
                                case 'dungeons': ?>
                                    <td><strong><?php echo $entry['dungeons_completed']; ?></strong></td>
                                    <td><?php echo $entry['total_boss_kills']; ?></td>
                                    <td>
                                        <?php 
                                        $diff_badges = ['Easy' => 'success', 'Medium' => 'warning', 'Hard' => 'danger', 'Nightmare' => 'dark'];
                                        $badge = $diff_badges[$entry['highest_difficulty']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?php echo $badge; ?>"><?php echo $entry['highest_difficulty']; ?></span>
                                    </td>
                                    <?php break;
                                case 'guilds': ?>
                                    <td><strong><?php echo $entry['guild_level']; ?></strong></td>
                                    <td><?php echo $entry['member_count']; ?></td>
                                    <td><?php echo number_format($entry['guild_treasury']); ?></td>
                                    <td><?php echo $entry['guild_territories_owned']; ?></td>
                                    <td><strong><?php echo number_format($entry['guild_war_rating']); ?></strong></td>
                                    <?php break;
                            endswitch;
                            ?>
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
    
    <!-- Hall of Fame -->
    <div class="card mt-4">
        <div class="card-header">
            <h5><i class="fas fa-star"></i> Hall of Fame</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fas fa-crown text-warning" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">Previous Season Winner</h6>
                        <p class="text-muted">Coming Soon</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fas fa-fire text-danger" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">Longest Win Streak</h6>
                        <p class="text-muted">Coming Soon</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fas fa-medal text-primary" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">Most Achievements</h6>
                        <p class="text-muted">Coming Soon</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Season Rankings Modal -->
<?php if ($season): ?>
<div class="modal fade" id="seasonModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-star"></i> <?php echo $season['season_name']; ?> Rankings
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Player</th>
                                <th>Guild</th>
                                <th>Points</th>
                                <th>Wins</th>
                                <th>Losses</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $s_rank = 1;
                            foreach ($seasonal_leaderboard as $player): 
                            ?>
                            <tr <?php echo $player['sp_user'] == $userid ? 'class="table-primary"' : ''; ?>>
                                <td>
                                    <?php 
                                    if ($s_rank == 1) echo '<i class="fas fa-trophy text-warning"></i> ';
                                    elseif ($s_rank == 2) echo '<i class="fas fa-medal text-secondary"></i> ';
                                    elseif ($s_rank == 3) echo '<i class="fas fa-medal" style="color: #cd7f32;"></i> ';
                                    echo $s_rank;
                                    ?>
                                </td>
                                <td>
                                    <?php echo $player['username']; ?>
                                    <?php if ($player['sp_user'] == $userid): ?>
                                        <span class="badge bg-primary">You</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $player['guild_name'] ?: 'None'; ?></td>
                                <td><strong><?php echo number_format($player['sp_points']); ?></strong></td>
                                <td><?php echo $player['sp_wins']; ?></td>
                                <td><?php echo $player['sp_losses']; ?></td>
                            </tr>
                            <?php 
                            $s_rank++;
                            endforeach; 
                            ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i> Season ends in <?php echo $season['days_remaining']; ?> days!
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.1);
}
</style>

<?php
$h->endpage();
?>