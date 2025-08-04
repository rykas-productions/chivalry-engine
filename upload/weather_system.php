<?php
/*
    File: weather_system.php
    Created: Weather System for v3.3
    Info: Dynamic weather that affects gameplay across all activities
*/
require_once('globals.php');

class WeatherSystem {
    private $db;
    private $userid;
    
    // Weather types with their effects
    private $weather_types = [
        'sunny' => [
            'name' => 'Sunny',
            'icon' => '☀️',
            'description' => 'Clear skies and bright sunshine',
            'color' => 'warning',
            'effects' => [
                'energy_regen' => 1.1,      // +10% energy regeneration
                'crop_growth' => 1.15,       // +15% crop growth speed
                'crime_success' => 0.95,     // -5% crime success (too visible)
                'mining_yield' => 1.0,       // Normal mining
                'combat_accuracy' => 1.05,   // +5% accuracy (good visibility)
                'travel_cost' => 0.9         // -10% travel cost
            ]
        ],
        'cloudy' => [
            'name' => 'Cloudy',
            'icon' => '☁️',
            'description' => 'Overcast with thick clouds',
            'color' => 'secondary',
            'effects' => [
                'energy_regen' => 1.0,
                'crop_growth' => 1.0,
                'crime_success' => 1.05,     // +5% crime success (low visibility)
                'mining_yield' => 1.0,
                'combat_accuracy' => 0.98,   // -2% accuracy
                'travel_cost' => 1.0
            ]
        ],
        'rainy' => [
            'name' => 'Rainy',
            'icon' => '🌧️',
            'description' => 'Steady rainfall throughout the region',
            'color' => 'primary',
            'effects' => [
                'energy_regen' => 0.9,       // -10% energy regeneration
                'crop_growth' => 1.25,       // +25% crop growth (water bonus)
                'crime_success' => 1.1,      // +10% crime success
                'mining_yield' => 0.8,       // -20% mining (wet conditions)
                'combat_accuracy' => 0.9,    // -10% accuracy
                'travel_cost' => 1.2,        // +20% travel cost
                'no_water_cost' => true      // Farming doesn't use water
            ]
        ],
        'stormy' => [
            'name' => 'Stormy',
            'icon' => '⛈️',
            'description' => 'Thunder and lightning with heavy rain',
            'color' => 'danger',
            'effects' => [
                'energy_regen' => 0.8,       // -20% energy regeneration
                'crop_growth' => 0.9,        // -10% crop growth (too harsh)
                'crime_success' => 1.15,     // +15% crime success
                'mining_yield' => 0.6,       // -40% mining (dangerous)
                'combat_accuracy' => 0.8,    // -20% accuracy
                'travel_cost' => 1.5,        // +50% travel cost
                'combat_damage' => 1.1,      // +10% damage (chaos bonus)
                'no_farming' => true         // Can't farm in storms
            ]
        ],
        'foggy' => [
            'name' => 'Foggy',
            'icon' => '🌫️',
            'description' => 'Thick fog reduces visibility',
            'color' => 'light',
            'effects' => [
                'energy_regen' => 1.0,
                'crop_growth' => 0.95,       // -5% crop growth
                'crime_success' => 1.2,      // +20% crime success (perfect cover)
                'mining_yield' => 0.9,       // -10% mining (poor visibility)
                'combat_accuracy' => 0.7,    // -30% accuracy
                'travel_cost' => 1.3,        // +30% travel cost
                'stealth_bonus' => 1.5       // +50% stealth activities
            ]
        ],
        'snowy' => [
            'name' => 'Snowy',
            'icon' => '❄️',
            'description' => 'Snow blankets the land',
            'color' => 'info',
            'effects' => [
                'energy_regen' => 0.85,      // -15% energy regeneration
                'crop_growth' => 0,          // Crops don't grow
                'crime_success' => 0.9,      // -10% crime (tracks in snow)
                'mining_yield' => 1.1,       // +10% mining (preserved ores)
                'combat_accuracy' => 0.95,   // -5% accuracy
                'travel_cost' => 1.4,        // +40% travel cost
                'no_farming' => true,        // Can't farm in snow
                'ice_fishing' => true        // Special winter activity
            ]
        ],
        'windy' => [
            'name' => 'Windy',
            'icon' => '💨',
            'description' => 'Strong winds sweep across the land',
            'color' => 'cyan',
            'effects' => [
                'energy_regen' => 0.95,      // -5% energy regeneration
                'crop_growth' => 0.9,        // -10% crop growth
                'crime_success' => 0.95,     // -5% crime (noise)
                'mining_yield' => 1.05,      // +5% mining (dust cleared)
                'combat_accuracy' => 0.85,   // -15% accuracy (wind affects projectiles)
                'travel_cost' => 1.1,        // +10% travel cost
                'sailing_bonus' => 1.5       // +50% sailing speed
            ]
        ],
        'heatwave' => [
            'name' => 'Heatwave',
            'icon' => '🔥',
            'description' => 'Extreme heat affects all activities',
            'color' => 'orange',
            'effects' => [
                'energy_regen' => 0.7,       // -30% energy regeneration
                'crop_growth' => 0.8,        // -20% crop growth (drought)
                'crime_success' => 0.85,     // -15% crime (exhaustion)
                'mining_yield' => 0.9,       // -10% mining
                'combat_accuracy' => 0.9,    // -10% accuracy
                'travel_cost' => 1.3,        // +30% travel cost
                'water_consumption' => 2.0,  // Double water usage
                'fire_damage' => 1.2         // +20% fire damage
            ]
        ]
    ];
    
    // Seasonal weather patterns
    private $seasonal_patterns = [
        'spring' => ['sunny' => 30, 'cloudy' => 25, 'rainy' => 30, 'stormy' => 10, 'foggy' => 5],
        'summer' => ['sunny' => 40, 'cloudy' => 20, 'rainy' => 15, 'stormy' => 5, 'heatwave' => 15, 'windy' => 5],
        'autumn' => ['sunny' => 20, 'cloudy' => 30, 'rainy' => 25, 'foggy' => 15, 'windy' => 10],
        'winter' => ['snowy' => 35, 'cloudy' => 30, 'foggy' => 15, 'stormy' => 10, 'sunny' => 10]
    ];
    
    public function __construct($db, $userid) {
        $this->db = $db;
        $this->userid = $userid;
        $this->initializeTables();
    }
    
    /**
     * Initialize weather tables
     */
    private function initializeTables() {
        // Check if weather table exists
        $check = $this->db->query("SHOW TABLES LIKE 'weather_current'");
        if ($this->db->num_rows($check) == 0) {
            // Create weather tables
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `weather_current` (
                    `weather_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `weather_type` varchar(50) NOT NULL,
                    `weather_intensity` float NOT NULL DEFAULT 1.0,
                    `started_at` int(11) NOT NULL,
                    `expires_at` int(11) NOT NULL,
                    `season` varchar(20) NOT NULL,
                    `is_active` tinyint(1) NOT NULL DEFAULT 1,
                    PRIMARY KEY (`weather_id`),
                    KEY `is_active` (`is_active`),
                    KEY `expires_at` (`expires_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `weather_history` (
                    `history_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `weather_type` varchar(50) NOT NULL,
                    `started_at` int(11) NOT NULL,
                    `ended_at` int(11) NOT NULL,
                    `affected_users` int(11) NOT NULL DEFAULT 0,
                    PRIMARY KEY (`history_id`),
                    KEY `started_at` (`started_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `weather_forecasts` (
                    `forecast_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `forecast_time` int(11) NOT NULL,
                    `weather_type` varchar(50) NOT NULL,
                    `probability` int(11) NOT NULL,
                    `created_at` int(11) NOT NULL,
                    PRIMARY KEY (`forecast_id`),
                    KEY `forecast_time` (`forecast_time`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            
            // Initialize with current weather
            $this->generateNewWeather();
        }
    }
    
    /**
     * Get current weather
     */
    public function getCurrentWeather() {
        // Check for active weather
        $current = $this->db->fetch_row($this->db->query("
            SELECT * FROM weather_current 
            WHERE is_active = 1 AND expires_at > " . time() . "
            ORDER BY weather_id DESC 
            LIMIT 1
        "));
        
        if (!$current) {
            // Generate new weather if none exists or expired
            $current = $this->generateNewWeather();
        }
        
        // Add weather details
        if ($current && isset($this->weather_types[$current['weather_type']])) {
            $current['details'] = $this->weather_types[$current['weather_type']];
            $current['time_remaining'] = $current['expires_at'] - time();
        }
        
        return $current;
    }
    
    /**
     * Generate new weather based on season
     */
    private function generateNewWeather() {
        // Archive old weather
        $this->db->query("
            UPDATE weather_current 
            SET is_active = 0 
            WHERE is_active = 1
        ");
        
        // Determine current season
        $month = date('n');
        if ($month >= 3 && $month <= 5) {
            $season = 'spring';
        } elseif ($month >= 6 && $month <= 8) {
            $season = 'summer';
        } elseif ($month >= 9 && $month <= 11) {
            $season = 'autumn';
        } else {
            $season = 'winter';
        }
        
        // Get weather pattern for season
        $pattern = $this->seasonal_patterns[$season];
        
        // Weighted random selection
        $rand = rand(1, 100);
        $cumulative = 0;
        $selected_weather = 'sunny'; // Default
        
        foreach ($pattern as $weather => $chance) {
            $cumulative += $chance;
            if ($rand <= $cumulative) {
                $selected_weather = $weather;
                break;
            }
        }
        
        // Random duration (2-8 hours)
        $duration = rand(2, 8) * 3600;
        $intensity = rand(80, 120) / 100; // 0.8 to 1.2 intensity
        
        // Insert new weather
        $now = time();
        $expires = $now + $duration;
        
        $this->db->query("
            INSERT INTO weather_current 
            (weather_type, weather_intensity, started_at, expires_at, season, is_active)
            VALUES ('{$selected_weather}', {$intensity}, {$now}, {$expires}, '{$season}', 1)
        ");
        
        // Generate forecasts
        $this->generateForecasts();
        
        return [
            'weather_id' => $this->db->insert_id(),
            'weather_type' => $selected_weather,
            'weather_intensity' => $intensity,
            'started_at' => $now,
            'expires_at' => $expires,
            'season' => $season,
            'is_active' => 1
        ];
    }
    
    /**
     * Generate weather forecasts
     */
    private function generateForecasts() {
        // Clear old forecasts
        $this->db->query("DELETE FROM weather_forecasts WHERE forecast_time < " . time());
        
        // Generate forecasts for next 24 hours
        $current_time = time();
        for ($hours = 4; $hours <= 24; $hours += 4) {
            $forecast_time = $current_time + ($hours * 3600);
            
            // Random weather with probability
            $weather_options = array_keys($this->weather_types);
            $weather = $weather_options[array_rand($weather_options)];
            $probability = rand(30, 90);
            
            $this->db->query("
                INSERT INTO weather_forecasts 
                (forecast_time, weather_type, probability, created_at)
                VALUES ({$forecast_time}, '{$weather}', {$probability}, {$current_time})
            ");
        }
    }
    
    /**
     * Get weather forecasts
     */
    public function getForecasts() {
        $forecasts = [];
        $query = $this->db->query("
            SELECT * FROM weather_forecasts 
            WHERE forecast_time > " . time() . "
            ORDER BY forecast_time ASC
            LIMIT 6
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            $row['weather_details'] = $this->weather_types[$row['weather_type']] ?? null;
            $row['time_until'] = $row['forecast_time'] - time();
            $row['time_display'] = date('H:i', $row['forecast_time']);
            $forecasts[] = $row;
        }
        
        return $forecasts;
    }
    
    /**
     * Apply weather effects to an activity
     */
    public function applyWeatherEffects($activity, $base_value) {
        $weather = $this->getCurrentWeather();
        
        if (!$weather || !isset($weather['details']['effects'][$activity])) {
            return $base_value;
        }
        
        $modifier = $weather['details']['effects'][$activity];
        $intensity = $weather['weather_intensity'];
        
        // Apply intensity to modifier
        $final_modifier = 1 + (($modifier - 1) * $intensity);
        
        return round($base_value * $final_modifier);
    }
    
    /**
     * Check if activity is blocked by weather
     */
    public function isActivityBlocked($activity) {
        $weather = $this->getCurrentWeather();
        
        if (!$weather) {
            return false;
        }
        
        $effects = $weather['details']['effects'];
        
        switch ($activity) {
            case 'farming':
                return isset($effects['no_farming']) && $effects['no_farming'];
            case 'sailing':
                return $weather['weather_type'] == 'stormy';
            case 'mining':
                return $weather['weather_type'] == 'stormy' && $weather['weather_intensity'] > 1.1;
            default:
                return false;
        }
    }
    
    /**
     * Get weather bonus description
     */
    public function getWeatherBonuses() {
        $weather = $this->getCurrentWeather();
        
        if (!$weather) {
            return [];
        }
        
        $bonuses = [];
        $effects = $weather['details']['effects'];
        
        foreach ($effects as $effect => $value) {
            if ($value == 1.0 || $value === true || $value === false) continue;
            
            $percentage = round(($value - 1) * 100);
            $type = $percentage > 0 ? 'bonus' : 'penalty';
            $percentage = abs($percentage);
            
            switch ($effect) {
                case 'energy_regen':
                    $bonuses[] = ['type' => $type, 'text' => "{$percentage}% Energy Regeneration"];
                    break;
                case 'crop_growth':
                    $bonuses[] = ['type' => $type, 'text' => "{$percentage}% Crop Growth Speed"];
                    break;
                case 'crime_success':
                    $bonuses[] = ['type' => $type, 'text' => "{$percentage}% Crime Success Rate"];
                    break;
                case 'mining_yield':
                    $bonuses[] = ['type' => $type, 'text' => "{$percentage}% Mining Yield"];
                    break;
                case 'combat_accuracy':
                    $bonuses[] = ['type' => $type, 'text' => "{$percentage}% Combat Accuracy"];
                    break;
                case 'travel_cost':
                    $bonuses[] = ['type' => $type, 'text' => "{$percentage}% Travel Cost"];
                    break;
            }
        }
        
        // Special conditions
        if (isset($effects['no_water_cost'])) {
            $bonuses[] = ['type' => 'bonus', 'text' => 'No water cost for farming'];
        }
        if (isset($effects['no_farming'])) {
            $bonuses[] = ['type' => 'penalty', 'text' => 'Farming disabled'];
        }
        if (isset($effects['stealth_bonus'])) {
            $bonuses[] = ['type' => 'bonus', 'text' => '50% Stealth bonus'];
        }
        
        return $bonuses;
    }
    
    /**
     * Get seasonal events
     */
    public function getSeasonalEvents() {
        $month = date('n');
        $events = [];
        
        // Spring events (March-May)
        if ($month >= 3 && $month <= 5) {
            $events[] = [
                'name' => 'Spring Harvest Festival',
                'description' => 'Crop growth +20%, Market prices +10%',
                'icon' => '🌸'
            ];
        }
        // Summer events (June-August)
        elseif ($month >= 6 && $month <= 8) {
            $events[] = [
                'name' => 'Summer Tournament',
                'description' => 'Combat XP +25%, Arena rewards doubled',
                'icon' => '⚔️'
            ];
        }
        // Autumn events (September-November)
        elseif ($month >= 9 && $month <= 11) {
            $events[] = [
                'name' => 'Autumn Trade Fair',
                'description' => 'Shop discounts 15%, Trading XP +30%',
                'icon' => '🍂'
            ];
        }
        // Winter events (December-February)
        else {
            $events[] = [
                'name' => 'Winter Solstice',
                'description' => 'Mining yield +30%, Crafting speed +20%',
                'icon' => '❄️'
            ];
        }
        
        return $events;
    }
}

// Initialize weather system
$weather_system = new WeatherSystem($db, $userid);

// Handle AJAX requests for weather updates
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['ajax']) {
        case 'current':
            $weather = $weather_system->getCurrentWeather();
            echo json_encode($weather);
            break;
            
        case 'forecast':
            $forecasts = $weather_system->getForecasts();
            echo json_encode($forecasts);
            break;
            
        case 'bonuses':
            $bonuses = $weather_system->getWeatherBonuses();
            echo json_encode($bonuses);
            break;
    }
    exit;
}

// Get current data for display
$current_weather = $weather_system->getCurrentWeather();
$forecasts = $weather_system->getForecasts();
$bonuses = $weather_system->getWeatherBonuses();
$seasonal_events = $weather_system->getSeasonalEvents();

?>

<div class="container-fluid">
    <!-- Weather Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-<?php echo $current_weather['details']['color'] ?? 'primary'; ?> text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-0">
                                <span class="weather-icon" style="font-size: 2rem;">
                                    <?php echo $current_weather['details']['icon'] ?? '☀️'; ?>
                                </span>
                                Weather System
                            </h2>
                            <p class="mb-0 mt-2">
                                Current: <strong><?php echo $current_weather['details']['name'] ?? 'Unknown'; ?></strong> - 
                                <?php echo $current_weather['details']['description'] ?? ''; ?>
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="weather-timer">
                                <small>Changes in</small>
                                <h4 id="weather-timer" class="mb-0">
                                    <?php echo gmdate('H:i:s', $current_weather['time_remaining'] ?? 0); ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Current Effects -->
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-magic"></i> Weather Effects</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($bonuses)): ?>
                        <p class="text-muted">No special weather effects active.</p>
                    <?php else: ?>
                        <ul class="list-unstyled">
                            <?php foreach ($bonuses as $bonus): ?>
                            <li class="mb-2">
                                <?php if ($bonus['type'] == 'bonus'): ?>
                                    <span class="badge bg-success">+</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">-</span>
                                <?php endif; ?>
                                <?php echo $bonus['text']; ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-cloud-sun"></i> Weather Forecast</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($forecasts)): ?>
                        <p class="text-muted">No forecast data available.</p>
                    <?php else: ?>
                        <div class="forecast-list">
                            <?php foreach ($forecasts as $forecast): ?>
                            <div class="forecast-item mb-2 p-2 border rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span style="font-size: 1.5rem;">
                                            <?php echo $forecast['weather_details']['icon'] ?? '❓'; ?>
                                        </span>
                                        <span class="ms-2">
                                            <?php echo $forecast['weather_details']['name'] ?? 'Unknown'; ?>
                                        </span>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted"><?php echo $forecast['time_display']; ?></small><br>
                                        <span class="badge bg-secondary"><?php echo $forecast['probability']; ?>%</span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-calendar-alt"></i> Seasonal Events</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($seasonal_events)): ?>
                        <p class="text-muted">No seasonal events active.</p>
                    <?php else: ?>
                        <?php foreach ($seasonal_events as $event): ?>
                        <div class="event-item mb-3">
                            <h6>
                                <span style="font-size: 1.5rem;"><?php echo $event['icon']; ?></span>
                                <?php echo $event['name']; ?>
                            </h6>
                            <p class="text-muted small mb-0"><?php echo $event['description']; ?></p>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Weather Guide -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-info-circle"></i> Weather System Guide</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($weather_system->weather_types as $type => $details): ?>
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="weather-type-card p-3 border rounded">
                        <h6>
                            <span style="font-size: 1.5rem;"><?php echo $details['icon']; ?></span>
                            <?php echo $details['name']; ?>
                        </h6>
                        <p class="small text-muted mb-2"><?php echo $details['description']; ?></p>
                        <ul class="small list-unstyled mb-0">
                            <?php 
                            $shown = 0;
                            foreach ($details['effects'] as $effect => $value):
                                if ($shown >= 3) break;
                                if ($value == 1.0 || $value === true || $value === false) continue;
                                $percentage = round(($value - 1) * 100);
                                $shown++;
                            ?>
                            <li><?php echo $percentage > 0 ? '+' : ''; ?><?php echo $percentage; ?>% <?php echo ucwords(str_replace('_', ' ', $effect)); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
.weather-timer {
    background: rgba(255,255,255,0.2);
    padding: 10px 20px;
    border-radius: 10px;
    text-align: center;
}

.forecast-item {
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.forecast-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.weather-type-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    transition: all 0.3s ease;
}

.weather-type-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.weather-icon {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
}

.bg-gradient-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.bg-gradient-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
}

.bg-gradient-light {
    background: linear-gradient(135deg, #f8f9fa 0%, #dee2e6 100%);
    color: #333 !important;
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
}

.bg-gradient-cyan {
    background: linear-gradient(135deg, #00bcd4 0%, #0097a7 100%);
}

.bg-gradient-orange {
    background: linear-gradient(135deg, #ff5722 0%, #e64a19 100%);
}
</style>

<script>
// Weather timer countdown
let weatherTimer = <?php echo $current_weather['time_remaining'] ?? 0; ?>;

function updateWeatherTimer() {
    if (weatherTimer > 0) {
        weatherTimer--;
        let hours = Math.floor(weatherTimer / 3600);
        let minutes = Math.floor((weatherTimer % 3600) / 60);
        let seconds = weatherTimer % 60;
        
        document.getElementById('weather-timer').textContent = 
            String(hours).padStart(2, '0') + ':' + 
            String(minutes).padStart(2, '0') + ':' + 
            String(seconds).padStart(2, '0');
    } else {
        // Reload page when weather changes
        location.reload();
    }
}

setInterval(updateWeatherTimer, 1000);

// Auto-refresh weather data every 5 minutes
setInterval(() => {
    fetch('weather_system.php?ajax=current')
        .then(response => response.json())
        .then(data => {
            // Update weather display if changed
            if (data.time_remaining) {
                weatherTimer = data.time_remaining;
            }
        });
}, 300000);
</script>

<?php
$h->endpage();
?>