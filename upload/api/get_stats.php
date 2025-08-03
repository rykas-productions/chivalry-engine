<?php
/*
	File:		api/get_stats.php
	Created: 	API endpoint for fetching player stats via AJAX
	Info: 		Returns player stats in JSON format
*/
require_once('../globals.php');

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($userid) || !$userid) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Fetch fresh user data
$userData = $db->fetch_row($db->query("SELECT * FROM `users` WHERE `userid` = {$userid}"));

if (!$userData) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

// Calculate percentages
$hp_percent = round($userData['hp'] / $userData['maxhp'] * 100);
$energy_percent = round($userData['energy'] / $userData['maxenergy'] * 100);
$will_percent = round($userData['will'] / $userData['maxwill'] * 100);
$brave_percent = round($userData['brave'] / $userData['maxbrave'] * 100);
$xp_percent = round($userData['xp'] / $userData['xp_needed'] * 100);

// Prepare response
$response = [
    'success' => true,
    'stats' => [
        'hp' => $userData['hp'],
        'maxhp' => $userData['maxhp'],
        'hp_percent' => $hp_percent,
        'energy' => $userData['energy'],
        'maxenergy' => $userData['maxenergy'],
        'energy_percent' => $energy_percent,
        'will' => $userData['will'],
        'maxwill' => $userData['maxwill'],
        'will_percent' => $will_percent,
        'brave' => $userData['brave'],
        'maxbrave' => $userData['maxbrave'],
        'brave_percent' => $brave_percent,
        'xp' => $userData['xp'],
        'xp_needed' => $userData['xp_needed'],
        'xp_percent' => $xp_percent,
        'level' => $userData['level'],
        'primary_currency' => $userData['primary_currency'],
        'secondary_currency' => $userData['secondary_currency'],
        'strength' => $userData['strength'],
        'agility' => $userData['agility'],
        'guard' => $userData['guard'],
        'labor' => $userData['labor'],
        'iq' => $userData['iq']
    ],
    'timestamp' => time()
];

echo json_encode($response);
?>