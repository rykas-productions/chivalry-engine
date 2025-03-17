<?php
// Sample JSON unit configuration
// Blunt > Strong Range, Weak Sharp
// Range > Strong Sharp, Weak Blunt
// Sharp > Strong Blunt, Weak Range
$units_json = '[
    {"name": "Swordsman", "attack_type": "Sharp", "attack_damage": 12, "defense": {"Blunt": 5, "Sharp": 10, "Ranged": 3}, "cost": {"copper": 5, "tokens": 2}},
    {"name": "Archer", "attack_type": "Ranged", "attack_damage": 10, "defense": {"Blunt": 2, "Sharp": 6, "Ranged": 8}, "cost": {"copper": 4, "tokens": 3}},
    {"name": "Cavalry", "attack_type": "Blunt", "attack_damage": 15, "defense": {"Blunt": 7, "Sharp": 3, "Ranged": 10}, "cost": {"copper": 7, "tokens": 3}},
    {"name": "Pikeman", "attack_type": "Sharp", "attack_damage": 14, "defense": {"Blunt": 6, "Sharp": 9, "Ranged": 4}, "cost": {"copper": 6, "tokens": 3}},
    {"name": "Crossbowman", "attack_type": "Ranged", "attack_damage": 12, "defense": {"Blunt": 3, "Sharp": 7, "Ranged": 9}, "cost": {"copper": 5, "tokens": 4}},
    {"name": "Knight", "attack_type": "Blunt", "attack_damage": 20, "defense": {"Blunt": 10, "Sharp": 4, "Ranged": 12}, "cost": {"copper": 10, "tokens": 5}},
    {"name": "Spearman", "attack_type": "Sharp", "attack_damage": 10, "defense": {"Blunt": 8, "Sharp": 7, "Ranged": 5}, "cost": {"copper": 4, "tokens": 2}},
    {"name": "Longbowman", "attack_type": "Ranged", "attack_damage": 14, "defense": {"Blunt": 2, "Sharp": 8, "Ranged": 10}, "cost": {"copper": 6, "tokens": 3}},
    {"name": "Heavy Cavalry", "attack_type": "Blunt", "attack_damage": 18, "defense": {"Blunt": 9, "Sharp": 5, "Ranged": 11}, "cost": {"copper": 9, "tokens": 4}}
]';

$units = json_decode($units_json, true);
$unit_map = [];
foreach ($units as $unit) {
    $unit_map[$unit['name']] = $unit;
}

// Function to simulate combat using a formula similar to Grepolis
function simulate_combat($attackers, $defenders, $unit_map) {
    $battle_result = [
        "attackers" => $attackers,
        "defenders" => $defenders,
        "unit_losses" => []
    ];
    
    // Calculate total attack power per type
    $total_attack = ["Blunt" => 0, "Sharp" => 0, "Ranged" => 0];
    foreach ($attackers as $attacker) {
        $attacker_stats = $unit_map[$attacker['name']];
        $total_attack[$attacker_stats['attack_type']] += $attacker_stats['attack_damage'] * $attacker['quantity'];
    }
    
    // Calculate total defense per type
    $total_defense = ["Blunt" => 0, "Sharp" => 0, "Ranged" => 0];
    foreach ($defenders as $defender) {
        $defender_stats = $unit_map[$defender['name']];
        foreach ($total_defense as $type => &$def_value) {
            $def_value += $defender_stats['defense'][$type] * $defender['quantity'];
        }
    }
    
    // Compute battle ratios
    foreach ($total_attack as $type => $attack_power) {
        if ($attack_power > 0 && $total_defense[$type] > 0) {
            $ratio = $attack_power / $total_defense[$type];
            foreach ($defenders as &$defender) {
                $defender_stats = $unit_map[$defender['name']];
                $losses = floor($defender['quantity'] * $ratio / (1 + $ratio));
                $defender['quantity'] = max(0, $defender['quantity'] - $losses);
                if ($losses > 0) {
                    $battle_result["unit_losses"][] = "$losses {$defender['name']} lost.";
                }
            }
        }
    }
    
    return json_encode($battle_result);
}

// Example combat simulation with multiple units and quantities
$attacking_units = [
    ["name" => "Swordsman", "quantity" => 100],
    ["name" => "Archer", "quantity" => 200]
];

$defending_units = [
    ["name" => "Cavalry", "quantity" => 400],
    ["name" => "Swordsman", "quantity" => 200]
];

echo simulate_combat($attacking_units, $defending_units, $unit_map);
?>