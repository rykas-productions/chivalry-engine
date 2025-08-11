<?php
$hidehdr=true;
require_once("../globals_nonauth.php");

header('Content-Type: application/json');

// API key validation from database
$key = isset($_GET['key']) ? $_GET['key'] : '';
$q = $db->query("SELECT `key_id` FROM `user_api_keys` WHERE `api_key` = '" . $db->escape($key) . "'");
if ($db->num_rows($q) == 0) {
    http_response_code(401);
    die(json_encode(['error' => 'Invalid API key']));
}

// Get player ID from GET
$userid = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$userid) {
    http_response_code(400);
    die(json_encode(['error' => 'Invalid or missing player ID']));
}

// Fetch non-sensitive player data
$q = $db->query("SELECT `userid`, `username`, `level`, `guild`,
                        `hp`, `maxhp`, `location`, `gender`, `class`, 
                        `laston`, `last_login`, `display_pic`
                 FROM `users` WHERE `userid` = {$userid}");

if ($db->num_rows($q) == 0) {
    http_response_code(404);
    die(json_encode(['error' => 'Player not found']));
}

$row = $db->fetch_row($q);

// Format the data, only including non-sensitive information
$player_data = array(
    'id' => (int)$row['userid'],
    'username' => $row['username'],
	'class' => $row['class'],
	'gender' => $row['gender'],
    'level' => (int)$row['level'],
    'guild' => (int)$row['guild'],
    'hp' => array(
        'current' => (int)$row['hp'],
        'maximum' => (int)$row['maxhp']
    ),
	'display_pic' => $row['display_pic'],
    'location' => $row['location'],
    'last_active' => $row['laston'],
    'last_login' => $row['last_login']
);

// Return the JSON response
echo json_encode([
    'success' => true,
    'player' => $player_data
]);
