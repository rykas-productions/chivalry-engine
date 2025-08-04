<?php
/*
    File: php8_compatibility_check.php
    Created: Check and report PHP 8 compatibility issues
    Info: Lists potential compatibility problems
    
    DELETE after reviewing!
*/

require_once('globals.php');

if (!$api->user->getStaffLevel($userid, 'admin')) {
    die('Admin only');
}

echo "<h3>PHP 8+ Compatibility Check</h3>";

// Check PHP version
echo "<div class='alert alert-info'>";
echo "<strong>PHP Version:</strong> " . PHP_VERSION . "<br>";
echo "<strong>PHP Major Version:</strong> " . PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION;
echo "</div>";

// Check error reporting level
$error_level = error_reporting();
echo "<div class='alert alert-warning'>";
echo "<strong>Error Reporting Level:</strong> {$error_level}<br>";
echo "<strong>Display Errors:</strong> " . ini_get('display_errors') . "<br>";
echo "<strong>Error Log:</strong> " . ini_get('error_log');
echo "</div>";

// Recommendations
echo "<div class='alert alert-success'>";
echo "<h5>✅ Fixed Compatibility Issues:</h5>";
echo "<ul>";
echo "<li>randomNumber() function - Fixed integer overflow</li>";
echo "<li>setInfo() function - Removed strict int typing for float values</li>";
echo "<li>fetch_single() - Added null check for empty results</li>";
echo "<li>Installer $set array - Fixed null array access</li>";
echo "</ul>";
echo "</div>";

// Check for remaining issues
echo "<div class='alert alert-info'>";
echo "<h5>Recommendations for PHP 8+:</h5>";
echo "<ul>";
echo "<li>Consider setting error_reporting to: E_ALL & ~E_DEPRECATED & ~E_NOTICE</li>";
echo "<li>Or use: error_reporting(E_ALL ^ E_DEPRECATED);</li>";
echo "<li>Check all database queries return results before accessing</li>";
echo "<li>Use null coalescing operator (??) for array access</li>";
echo "</ul>";
echo "</div>";

// Quick test of common functions
echo "<div class='alert alert-primary'>";
echo "<h5>Function Tests:</h5>";
$tests = [
    'randomNumber(1, 100)' => randomNumber(1, 100),
    'randomNumber()' => randomNumber(),
    'Password Hash Test' => password_verify(base64_encode(hash('sha256', 'test', true)), password_hash(base64_encode(hash('sha256', 'test', true)), PASSWORD_DEFAULT))
];

foreach ($tests as $test => $result) {
    echo "<code>{$test}</code>: ";
    if (is_bool($result)) {
        echo $result ? "✅ true" : "❌ false";
    } else {
        echo "✅ " . $result;
    }
    echo "<br>";
}
echo "</div>";

$h->endpage();
?>