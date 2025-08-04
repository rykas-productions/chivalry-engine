<?php
/*
    File: achievements.php
    Created: Achievement system main page
    Info: View achievements, progress, and leaderboard
*/
require_once('globals.php');
require_once('includes/achievement-system.php');

$achievements = new AchievementSystem($db, $userid, $api);

// Get action
$action = isset($_GET['action']) ? $_GET['action'] : 'view';
$category = isset($_GET['cat']) ? $db->escape($_GET['cat']) : 'all';

// Check for new achievements on page load
$new_achievements = $achievements->getRecentAchievements();

// Display any newly earned achievements as alerts
foreach ($new_achievements as $ach) {
    $rarity_badges = [
        'common' => 'secondary',
        'uncommon' => 'success', 
        'rare' => 'primary',
        'epic' => 'purple',
        'legendary' => 'warning'
    ];
    $badge = $rarity_badges[$ach['ach_rarity']];
    alert('success', "<i class='fas {$ach['ach_icon']}'></i> Achievement Unlocked!", 
          "<span class='badge bg-{$badge}'>{$ach['ach_rarity']}</span> <strong>{$ach['ach_name']}</strong><br>
           {$ach['ach_desc']}<br>
           <small>+{$ach['ach_points']} points earned!</small>", false);
}

if ($action == 'leaderboard') {
    // Show leaderboard
    $leaderboard = $achievements->getLeaderboard(25);
    ?>
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-warning text-white">
                    <div class="card-body">
                        <h2 class="mb-0"><i class="fas fa-trophy me-2"></i>Achievement Leaderboard</h2>
                        <p class="mb-0 mt-2">Top 25 Achievement Hunters</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span>Top Players</span>
                <a href="achievements.php" class="btn btn-sm btn-primary">View Achievements</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Player</th>
                                <th>Level</th>
                                <th>Achievements</th>
                                <th>Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leaderboard as $player): ?>
                            <tr <?php echo $player['userid'] == $userid ? 'class="table-warning"' : ''; ?>>
                                <td>
                                    <?php 
                                    if ($player['rank'] == 1) echo '<i class="fas fa-trophy text-warning"></i> ';
                                    elseif ($player['rank'] == 2) echo '<i class="fas fa-medal text-secondary"></i> ';
                                    elseif ($player['rank'] == 3) echo '<i class="fas fa-medal text-danger"></i> ';
                                    echo "#{$player['rank']}";
                                    ?>
                                </td>
                                <td><a href="profile.php?user=<?php echo $player['userid']; ?>"><?php echo $player['username']; ?></a></td>
                                <td><?php echo $player['level']; ?></td>
                                <td><?php echo number_format($player['achievements_earned']); ?></td>
                                <td><strong><?php echo number_format($player['achievement_points']); ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php
} else {
    // Show achievements view
    $categories = $achievements->getCategories();
    $user_achievements = $achievements->getUserAchievements();
    $total_earned = count($user_achievements);
    $total_points = array_sum(array_column($user_achievements, 'ach_points'));
    
    ?>
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-primary text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="mb-0"><i class="fas fa-trophy me-2"></i>Achievements</h2>
                                <p class="mb-0 mt-2">Track your progress and unlock rewards</p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <h3><?php echo number_format($total_points); ?> <small>points</small></h3>
                                <p class="mb-0"><?php echo $total_earned; ?> achievements earned</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="row mb-3">
            <div class="col-12">
                <a href="achievements.php?action=leaderboard" class="btn btn-warning">
                    <i class="fas fa-chart-line"></i> Leaderboard
                </a>
                <a href="profile.php?user=<?php echo $userid; ?>#achievements" class="btn btn-info">
                    <i class="fas fa-user"></i> My Profile
                </a>
            </div>
        </div>
        
        <!-- Category Overview -->
        <div class="row mb-4">
            <?php foreach ($categories as $cat): ?>
            <div class="col-md-4 col-lg-3 mb-3">
                <div class="card h-100 category-card" onclick="filterCategory('<?php echo $cat['ach_category']; ?>')">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $cat['ach_category']; ?></h5>
                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: <?php echo $cat['percentage']; ?>%"></div>
                        </div>
                        <small class="text-muted">
                            <?php echo $cat['earned']; ?>/<?php echo $cat['total']; ?> earned
                            (<?php echo $cat['percentage']; ?>%)
                        </small>
                        <div class="mt-2">
                            <strong><?php echo number_format($cat['earned_points']); ?>/<?php echo number_format($cat['total_points']); ?></strong> points
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Achievements List -->
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="achievement-tabs">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $category == 'all' ? 'active' : ''; ?>" href="achievements.php?cat=all">All</a>
                    </li>
                    <?php foreach ($categories as $cat): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $category == $cat['ach_category'] ? 'active' : ''; ?>" 
                           href="achievements.php?cat=<?php echo urlencode($cat['ach_category']); ?>">
                            <?php echo $cat['ach_category']; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="card-body">
                <div class="row" id="achievements-container">
                    <?php
                    if ($category == 'all') {
                        // Show all achievements
                        foreach ($categories as $cat) {
                            $cat_achievements = $achievements->getAchievementsByCategory($cat['ach_category']);
                            foreach ($cat_achievements as $ach) {
                                displayAchievement($ach);
                            }
                        }
                    } else {
                        // Show specific category
                        $cat_achievements = $achievements->getAchievementsByCategory($category);
                        foreach ($cat_achievements as $ach) {
                            displayAchievement($ach);
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <style>
    .category-card {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    .achievement-card {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .achievement-card.earned {
        background: linear-gradient(135deg, #f5f5f5 0%, #e8f5e9 100%);
        border-color: #4caf50;
    }
    .achievement-card.earned::before {
        content: '✓';
        position: absolute;
        top: 10px;
        right: 10px;
        color: #4caf50;
        font-size: 24px;
        font-weight: bold;
    }
    .rarity-common { border-left: 4px solid #6c757d; }
    .rarity-uncommon { border-left: 4px solid #28a745; }
    .rarity-rare { border-left: 4px solid #007bff; }
    .rarity-epic { border-left: 4px solid #6f42c1; }
    .rarity-legendary { 
        border-left: 4px solid #ffc107;
        background: linear-gradient(135deg, #fff 0%, #fff8e1 100%);
    }
    .achievement-icon {
        font-size: 2rem;
        margin-bottom: 10px;
    }
    .achievement-progress {
        position: relative;
        margin-top: 10px;
    }
    </style>
    
    <script>
    function filterCategory(category) {
        window.location.href = 'achievements.php?cat=' + encodeURIComponent(category);
    }
    </script>
    <?php
}

function displayAchievement($ach) {
    $rarity_colors = [
        'common' => '#6c757d',
        'uncommon' => '#28a745',
        'rare' => '#007bff',
        'epic' => '#6f42c1',
        'legendary' => '#ffc107'
    ];
    $color = $rarity_colors[$ach['ach_rarity']];
    ?>
    <div class="col-md-6 col-lg-4 mb-3">
        <div class="card achievement-card h-100 rarity-<?php echo $ach['ach_rarity']; ?> <?php echo $ach['is_earned'] ? 'earned' : ''; ?>">
            <div class="card-body">
                <div class="text-center achievement-icon" style="color: <?php echo $color; ?>">
                    <i class="fas <?php echo $ach['ach_icon']; ?>"></i>
                </div>
                <h5 class="card-title text-center"><?php echo $ach['ach_name']; ?></h5>
                <p class="card-text text-center small"><?php echo $ach['ach_desc']; ?></p>
                
                <?php if (!$ach['is_earned']): ?>
                <div class="achievement-progress">
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar" style="width: <?php echo $ach['progress_percent']; ?>%; background-color: <?php echo $color; ?>"></div>
                    </div>
                    <small class="text-muted">
                        Progress: <?php echo number_format($ach['current_progress']); ?>/<?php echo number_format($ach['ach_requirement']); ?>
                        (<?php echo $ach['progress_percent']; ?>%)
                    </small>
                </div>
                <?php else: ?>
                <div class="text-center text-success">
                    <small>Earned: <?php echo date('M j, Y', strtotime($ach['ua_earned'])); ?></small>
                </div>
                <?php endif; ?>
                
                <div class="text-center mt-2">
                    <span class="badge" style="background-color: <?php echo $color; ?>">
                        <?php echo ucfirst($ach['ach_rarity']); ?>
                    </span>
                    <span class="badge bg-secondary">
                        +<?php echo $ach['ach_points']; ?> pts
                    </span>
                </div>
            </div>
        </div>
    </div>
    <?php
}

$h->endpage();
?>