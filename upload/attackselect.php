<?php
/*
    File: attackselect.php
    Created: Player selection interface for attacks
    Info: Shows a list of players that can be attacked
*/
require("globals.php");

echo "<div class='container-fluid'>";
echo "<div class='row mb-4'>";
echo "<div class='col-12'>";
echo "<div class='card bg-gradient-danger text-white'>";
echo "<div class='card-body'>";
echo "<h2 class='mb-0'><i class='fas fa-crosshairs me-2'></i>Attack Player</h2>";
echo "<p class='mb-0 mt-2'>Select a player to attack</p>";
echo "</div>";
echo "</div>";
echo "</div>";
echo "</div>";

// Get players in same location who can be attacked
$location_players = $db->query("
    SELECT u.userid, u.username, u.level, us.strength, us.agility, us.guard, 
           u.hp, u.maxhp, u.location, u.guild, u.laston
    FROM users u
    INNER JOIN userstats us ON u.userid = us.userid
    WHERE u.location = {$ir['location']}
    AND u.userid != {$userid}
    AND u.hp > 0
    ORDER BY u.level DESC, u.laston DESC
    LIMIT 50
");

if ($db->num_rows($location_players) == 0) {
    echo "<div class='alert alert-warning'>";
    echo "<i class='fas fa-exclamation-triangle'></i> There are no players in your location that you can attack.";
    echo "</div>";
} else {
    echo "<div class='card'>";
    echo "<div class='card-header bg-dark text-white'>";
    echo "<h5 class='mb-0'>Players in Your Location</h5>";
    echo "</div>";
    echo "<div class='card-body'>";
    echo "<div class='table-responsive'>";
    echo "<table class='table table-striped table-hover'>";
    echo "<thead class='table-dark'>";
    echo "<tr>";
    echo "<th>Player</th>";
    echo "<th>Level</th>";
    echo "<th>Health</th>";
    echo "<th>Stats</th>";
    echo "<th>Status</th>";
    echo "<th>Action</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    
    while ($player = $db->fetch_row($location_players)) {
        $hp_percent = ($player['hp'] / $player['maxhp']) * 100;
        $hp_color = $hp_percent > 66 ? 'success' : ($hp_percent > 33 ? 'warning' : 'danger');
        
        // Check if online (active in last 15 minutes)
        $online = (time() - $player['laston'] < 900) ? true : false;
        $status_color = $online ? 'success' : 'secondary';
        $status_text = $online ? 'Online' : 'Offline';
        
        // Check if in same guild
        $same_guild = ($player['guild'] > 0 && $player['guild'] == $ir['guild']);
        
        echo "<tr>";
        echo "<td>";
        echo "<a href='viewuser.php?u={$player['userid']}' class='text-decoration-none'>";
        echo "<strong>{$player['username']}</strong>";
        echo "</a>";
        if ($same_guild) {
            echo " <span class='badge bg-info ms-1'>Guild Member</span>";
        }
        echo "</td>";
        echo "<td>Level {$player['level']}</td>";
        echo "<td>";
        echo "<div class='progress' style='height: 20px;'>";
        echo "<div class='progress-bar bg-{$hp_color}' style='width: {$hp_percent}%'>";
        echo number_format($player['hp']) . "/" . number_format($player['maxhp']);
        echo "</div>";
        echo "</div>";
        echo "</td>";
        echo "<td>";
        echo "<small>";
        echo "STR: " . number_format($player['strength']) . " | ";
        echo "AGI: " . number_format($player['agility']) . " | ";
        echo "GRD: " . number_format($player['guard']);
        echo "</small>";
        echo "</td>";
        echo "<td><span class='badge bg-{$status_color}'>{$status_text}</span></td>";
        echo "<td>";
        
        if ($same_guild) {
            echo "<button class='btn btn-sm btn-secondary' disabled>";
            echo "<i class='fas fa-ban'></i> Guild Member";
            echo "</button>";
        } else {
            echo "<a href='attack.php?user={$player['userid']}' class='btn btn-sm btn-danger'>";
            echo "<i class='fas fa-sword'></i> Attack";
            echo "</a>";
        }
        
        echo "</td>";
        echo "</tr>";
    }
    
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
}

// Search for a specific player
echo "<div class='card mt-4'>";
echo "<div class='card-header bg-secondary text-white'>";
echo "<h5 class='mb-0'>Search for Player</h5>";
echo "</div>";
echo "<div class='card-body'>";
echo "<form method='get' action='attack.php'>";
echo "<div class='input-group'>";
echo "<input type='number' name='user' class='form-control' placeholder='Enter Player ID' required>";
echo "<button type='submit' class='btn btn-danger'>";
echo "<i class='fas fa-search'></i> Find & Attack";
echo "</button>";
echo "</div>";
echo "</form>";
echo "</div>";
echo "</div>";

echo "</div>";

$h->endpage();
?>