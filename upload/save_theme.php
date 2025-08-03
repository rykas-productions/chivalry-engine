<?php
/*
    File: save_theme.php
    Created: Save user theme preference to database
    Info: AJAX endpoint for theme switcher
*/
require("globals.php");

// Check if user is logged in
if (!isset($userid) || $userid == 0) {
    http_response_code(401);
    die(json_encode(['error' => 'Not authenticated']));
}

// Validate theme input
$allowed_themes = ['light', 'dark', 'accessibility'];
$theme = isset($_POST['theme']) ? $_POST['theme'] : 'dark';

if (!in_array($theme, $allowed_themes)) {
    http_response_code(400);
    die(json_encode(['error' => 'Invalid theme']));
}

// Check if theme_preference column exists
$check_column = $db->query("SHOW COLUMNS FROM `users` LIKE 'theme_preference'");
if ($db->num_rows($check_column) == 0) {
    // Add column if it doesn't exist
    $db->query("ALTER TABLE `users` ADD COLUMN `theme_preference` VARCHAR(20) DEFAULT 'dark'");
}

// Update user's theme preference
$theme = $db->escape($theme);
$db->query("UPDATE `users` SET `theme_preference` = '{$theme}' WHERE `userid` = {$userid}");

// Return success
echo json_encode(['success' => true, 'theme' => $theme]);
?>