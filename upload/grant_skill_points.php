<?php
/*
    File: grant_skill_points.php
    Created: Grant skill points to existing players
    Info: One-time script to give skill points based on player level
*/
require_once('globals.php');

// Check if user is admin
if ($ir['user_level'] != 'Admin') {
    die("Admin access required!");
}

echo "<h3>Granting Skill Points to Existing Players</h3>";

// Check current state
$total_users = $db->fetch_single($db->query("SELECT COUNT(*) FROM users"));
$users_with_points = $db->fetch_single($db->query("SELECT COUNT(*) FROM users WHERE skill_points > 0"));

echo "<p>Total users: {$total_users}</p>";
echo "<p>Users with skill points: {$users_with_points}</p>";

if ($users_with_points >= $total_users) {
    echo "<div class='alert alert-info'>All users already have skill points!</div>";
} else {
    // Grant skill points based on level
    echo "<h4>Granting Skill Points...</h4>";
    
    // Base points: 1 per level
    $db->query("
        UPDATE users 
        SET skill_points = level 
        WHERE skill_points = 0 OR skill_points IS NULL
    ");
    $base_affected = $db->affected_rows();
    echo "<p>✓ Granted base skill points (1 per level) to {$base_affected} users</p>";
    
    // Bonus for mid-level players (level 25+): +3 bonus
    $db->query("
        UPDATE users 
        SET skill_points = skill_points + 3 
        WHERE level >= 25 AND level < 50
    ");
    $mid_affected = $db->affected_rows();
    echo "<p>✓ Granted +3 bonus points to {$mid_affected} mid-level players (25-49)</p>";
    
    // Bonus for high-level players (level 50+): +5 bonus
    $db->query("
        UPDATE users 
        SET skill_points = skill_points + 5 
        WHERE level >= 50 AND level < 100
    ");
    $high_affected = $db->affected_rows();
    echo "<p>✓ Granted +5 bonus points to {$high_affected} high-level players (50-99)</p>";
    
    // Bonus for elite players (level 100+): +10 bonus
    $db->query("
        UPDATE users 
        SET skill_points = skill_points + 10 
        WHERE level >= 100
    ");
    $elite_affected = $db->affected_rows();
    echo "<p>✓ Granted +10 bonus points to {$elite_affected} elite players (100+)</p>";
    
    // Show some examples
    echo "<h4>Sample Player Skill Points:</h4>";
    echo "<table class='table table-bordered' style='max-width: 500px;'>";
    echo "<thead><tr><th>Username</th><th>Level</th><th>Skill Points</th></tr></thead>";
    echo "<tbody>";
    
    $examples = $db->query("
        SELECT username, level, skill_points 
        FROM users 
        ORDER BY level DESC 
        LIMIT 10
    ");
    
    while ($user = $db->fetch_row($examples)) {
        echo "<tr>";
        echo "<td>{$user['username']}</td>";
        echo "<td>{$user['level']}</td>";
        echo "<td><strong>{$user['skill_points']}</strong></td>";
        echo "</tr>";
    }
    
    echo "</tbody></table>";
    
    echo "<div class='alert alert-success'>";
    echo "<h4>Success!</h4>";
    echo "<p>Skill points have been granted to all players based on their level:</p>";
    echo "<ul>";
    echo "<li>Level 1-24: 1 point per level</li>";
    echo "<li>Level 25-49: 1 point per level + 3 bonus</li>";
    echo "<li>Level 50-99: 1 point per level + 5 bonus</li>";
    echo "<li>Level 100+: 1 point per level + 10 bonus</li>";
    echo "</ul>";
    echo "</div>";
    
    // Create announcement
    $announcement = "The Skill Tree System is now live! All players have been granted skill points based on their level. Visit the Skill Trees page to customize your character with powerful abilities!";
    $db->query("
        INSERT INTO announcements (ann_text, ann_time, ann_poster)
        VALUES ('{$announcement}', UNIX_TIMESTAMP(), 'System')
    ");
    echo "<p>✓ Announcement created</p>";
}

echo "<hr>";
echo "<a href='skill_tree.php' class='btn btn-primary'>Go to Skill Trees</a> ";
echo "<a href='index.php' class='btn btn-secondary'>Back to Game</a>";

$h->endpage();
?>