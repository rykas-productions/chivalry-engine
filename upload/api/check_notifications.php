<?php
/*
	File:		api/check_notifications.php
	Created: 	API endpoint for checking new notifications
	Info: 		Returns notification count and recent notifications
*/
require_once('../globals.php');

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($userid) || !$userid) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Get unread notification count
$notif_count = $db->fetch_single($db->query("
    SELECT COUNT(`notif_id`) 
    FROM `notifications` 
    WHERE `notif_user` = {$userid} 
    AND `notif_status` = 'unread'
"));

// Get unread message count
$mail_count = $db->fetch_single($db->query("
    SELECT COUNT(`mail_id`) 
    FROM `mail` 
    WHERE `mail_to` = {$userid} 
    AND `mail_status` = 'unread'
"));

// Get recent notifications (last 5)
$recent_notifications = [];
$notif_query = $db->query("
    SELECT `notif_text`, `notif_time` 
    FROM `notifications` 
    WHERE `notif_user` = {$userid} 
    ORDER BY `notif_time` DESC 
    LIMIT 5
");

while ($notif = $db->fetch_row($notif_query)) {
    $recent_notifications[] = [
        'text' => $notif['notif_text'],
        'time' => date('Y-m-d H:i', $notif['notif_time']),
        'time_ago' => time_ago($notif['notif_time'])
    ];
}

// Prepare response
$response = [
    'success' => true,
    'new_notifications' => $notif_count,
    'new_messages' => $mail_count,
    'total_new' => $notif_count + $mail_count,
    'recent_notifications' => $recent_notifications,
    'timestamp' => time()
];

echo json_encode($response);

// Helper function to calculate time ago
function time_ago($timestamp) {
    $time_ago = time() - $timestamp;
    
    if ($time_ago < 60) {
        return 'Just now';
    } elseif ($time_ago < 3600) {
        $minutes = round($time_ago / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($time_ago < 86400) {
        $hours = round($time_ago / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } else {
        $days = round($time_ago / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    }
}
?>