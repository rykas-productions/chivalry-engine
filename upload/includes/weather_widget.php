<?php
/*
    File: includes/weather_widget.php
    Created: Weather Widget for header display
    Info: Shows current weather and applies effects globally
*/

// This file is included from globals.php, so we have access to $db and $userid

class WeatherWidget {
    private $db;
    private $weather_data;
    
    public function __construct($db) {
        $this->db = $db;
        $this->loadCurrentWeather();
    }
    
    private function loadCurrentWeather() {
        // Check if weather table exists first
        $check = $this->db->query("SHOW TABLES LIKE 'weather_current'");
        if ($this->db->num_rows($check) == 0) {
            // Table doesn't exist yet, don't try to load
            $this->weather_data = null;
            return;
        }
        
        // Get current active weather
        $this->weather_data = $this->db->fetch_row($this->db->query("
            SELECT * FROM weather_current 
            WHERE is_active = 1 AND expires_at > " . time() . "
            ORDER BY weather_id DESC 
            LIMIT 1
        "));
    }
    
    public function getWeatherType() {
        return $this->weather_data['weather_type'] ?? 'sunny';
    }
    
    public function getWeatherIcon() {
        $icons = [
            'sunny' => '☀️',
            'cloudy' => '☁️',
            'rainy' => '🌧️',
            'stormy' => '⛈️',
            'foggy' => '🌫️',
            'snowy' => '❄️',
            'windy' => '💨',
            'heatwave' => '🔥'
        ];
        
        return $icons[$this->getWeatherType()] ?? '☀️';
    }
    
    public function getWeatherName() {
        $names = [
            'sunny' => 'Sunny',
            'cloudy' => 'Cloudy',
            'rainy' => 'Rainy',
            'stormy' => 'Stormy',
            'foggy' => 'Foggy',
            'snowy' => 'Snowy',
            'windy' => 'Windy',
            'heatwave' => 'Heatwave'
        ];
        
        return $names[$this->getWeatherType()] ?? 'Unknown';
    }
    
    public function applyEnergyRegenModifier($base_regen) {
        $modifiers = [
            'sunny' => 1.1,
            'cloudy' => 1.0,
            'rainy' => 0.9,
            'stormy' => 0.8,
            'foggy' => 1.0,
            'snowy' => 0.85,
            'windy' => 0.95,
            'heatwave' => 0.7
        ];
        
        $modifier = $modifiers[$this->getWeatherType()] ?? 1.0;
        $intensity = $this->weather_data['weather_intensity'] ?? 1.0;
        
        // Apply intensity to modifier
        $final_modifier = 1 + (($modifier - 1) * $intensity);
        
        return round($base_regen * $final_modifier);
    }
    
    public function getCrimeSuccessModifier() {
        $modifiers = [
            'sunny' => 0.95,
            'cloudy' => 1.05,
            'rainy' => 1.1,
            'stormy' => 1.15,
            'foggy' => 1.2,
            'snowy' => 0.9,
            'windy' => 0.95,
            'heatwave' => 0.85
        ];
        
        return $modifiers[$this->getWeatherType()] ?? 1.0;
    }
    
    public function getMiningYieldModifier() {
        $modifiers = [
            'sunny' => 1.0,
            'cloudy' => 1.0,
            'rainy' => 0.8,
            'stormy' => 0.6,
            'foggy' => 0.9,
            'snowy' => 1.1,
            'windy' => 1.05,
            'heatwave' => 0.9
        ];
        
        return $modifiers[$this->getWeatherType()] ?? 1.0;
    }
    
    public function getCropGrowthModifier() {
        $modifiers = [
            'sunny' => 1.15,
            'cloudy' => 1.0,
            'rainy' => 1.25,
            'stormy' => 0.9,
            'foggy' => 0.95,
            'snowy' => 0,
            'windy' => 0.9,
            'heatwave' => 0.8
        ];
        
        return $modifiers[$this->getWeatherType()] ?? 1.0;
    }
    
    public function isFarmingBlocked() {
        return in_array($this->getWeatherType(), ['stormy', 'snowy']);
    }
    
    public function getWaterCostModifier() {
        // Rain means no water cost
        if ($this->getWeatherType() == 'rainy') {
            return 0;
        }
        // Heatwave doubles water cost
        if ($this->getWeatherType() == 'heatwave') {
            return 2.0;
        }
        return 1.0;
    }
    
    public function renderWidget() {
        if (!$this->weather_data) {
            return '';
        }
        
        $time_remaining = $this->weather_data['expires_at'] - time();
        $hours = floor($time_remaining / 3600);
        $minutes = floor(($time_remaining % 3600) / 60);
        
        return '
        <div class="weather-widget d-flex justify-content-between align-items-center p-2 rounded" style="background: rgba(255,255,255,0.05);">
            <div class="d-flex align-items-center">
                <span class="weather-icon me-2" title="Current Weather: ' . $this->getWeatherName() . '" style="font-size: 1.5rem;">
                    ' . $this->getWeatherIcon() . '
                </span>
                <div>
                    <div class="weather-name small fw-bold">' . $this->getWeatherName() . '</div>
                    <div class="weather-timer text-muted" style="font-size: 0.75rem;" title="Changes in ' . $hours . 'h ' . $minutes . 'm">
                        <i class="far fa-clock"></i> ' . $hours . 'h ' . $minutes . 'm
                    </div>
                </div>
            </div>
            <a href="weather_system.php" class="btn btn-sm btn-outline-light" style="font-size: 0.75rem;">
                <i class="fas fa-info-circle"></i>
            </a>
        </div>';
    }
}

// Create global weather widget instance
if (isset($db)) {
    $GLOBALS['weather_widget'] = new WeatherWidget($db);
}
?>