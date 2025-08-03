<?php
// Check database fields for user
include('config.php');
$connection = mysqli_connect(db_host, db_username, db_password, db_database);

if ($connection) {
    $result = mysqli_query($connection, "SELECT * FROM users WHERE userid = 1");
    $user = mysqli_fetch_assoc($result);
    
    echo "User fields found:\n";
    echo "================\n";
    
    // Check for commonly needed fields
    $required_fields = ['sidemenu', 'personal_notes', 'vip_days', 'xp', 'xp_needed'];
    
    foreach ($required_fields as $field) {
        if (array_key_exists($field, $user)) {
            echo "✓ $field: " . $user[$field] . "\n";
        } else {
            echo "✗ $field: MISSING\n";
        }
    }
    
    echo "\nAll fields in user table:\n";
    foreach (array_keys($user) as $key) {
        echo "- $key\n";
    }
    
    mysqli_close($connection);
}
?>