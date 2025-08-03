<?php
/*
    File: realtime_events.php
    Created: Server-Sent Events for real-time updates
    Info: Pushes updates to client without polling
*/

session_name('CEV3');
session_start();

if (!isset($_SESSION['userid'])) {
    http_response_code(401);
    exit('Unauthorized');
}

$userid = $_SESSION['userid'];

// Set headers for SSE
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no'); // Disable Nginx buffering

require_once "config.php";
require_once "class/class_db_" . constant("db_driver") . ".php";

$db = new database;
$db->configure(constant("db_host"), constant("db_username"), constant("db_password"), constant("db_database"), 0);
$db->connect();

$last_event_id = 0;

// Function to send SSE message
function sendSSE($event, $data) {
    echo "event: $event\n";
    echo "data: " . json_encode($data) . "\n\n";
    ob_flush();
    flush();
}

// Send initial connection message
sendSSE('connected', ['status' => 'connected', 'time' => time()]);

// Main event loop
while (true) {
    // Check for new notifications
    $notif_query = $db->query(
        "SELECT * FROM notifications 
         WHERE notif_user = {$userid} 
         AND notif_status = 'unread' 
         AND notif_id > {$last_event_id}
         ORDER BY notif_id DESC 
         LIMIT 5"
    );
    
    while ($notif = $db->fetch_row($notif_query)) {
        sendSSE('notification', [
            'id' => $notif['notif_id'],
            'text' => $notif['notif_text'],
            'time' => $notif['notif_time']
        ]);
        
        $last_event_id = max($last_event_id, $notif['notif_id']);
    }
    
    // Check for resource updates (energy, hp, etc.)
    $user_query = $db->query(
        "SELECT energy, maxenergy, hp, maxhp, will, maxwill, brave, maxbrave,
                primary_currency, secondary_currency
         FROM users 
         WHERE userid = {$userid}"
    );
    
    if ($user = $db->fetch_row($user_query)) {
        sendSSE('resources', [
            'energy' => $user['energy'],
            'maxenergy' => $user['maxenergy'],
            'hp' => $user['hp'],
            'maxhp' => $user['maxhp'],
            'will' => $user['will'],
            'maxwill' => $user['maxwill'],
            'brave' => $user['brave'],
            'maxbrave' => $user['maxbrave'],
            'money' => $user['primary_currency'],
            'crystals' => $user['secondary_currency']
        ]);
    }
    
    // Send heartbeat every 30 seconds
    sendSSE('heartbeat', ['time' => time()]);
    
    // Sleep for 5 seconds before next check
    sleep(5);
    
    // Break after 60 seconds to prevent timeout
    if (time() - $_SERVER['REQUEST_TIME'] > 60) {
        sendSSE('reconnect', ['message' => 'Reconnecting...']);
        break;
    }
}
?>