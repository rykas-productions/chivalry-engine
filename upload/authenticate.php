<?php
$menuhide = true;
require_once 'globals_nonauth.php';

// Configuration
define('MAX_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutes

// Helper Functions
function getClientIP(): string {
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function isLockedOut($ip): bool {
    global $db;
    $ipEscaped = $db->escape($ip);
    $timeWindow = time() - LOCKOUT_TIME;
    $query = "SELECT COUNT(*) FROM login_attempts WHERE ip = '{$ipEscaped}' AND timestamp > {$timeWindow}";
    $result = $db->query($query);
    $count = $db->fetch_single($result);
    return $count >= MAX_ATTEMPTS;
}

function logLoginAttempt($ip, $userId): void {
    global $db, $api;
    $db->easy_insert('login_attempts', [
        'ip' => $ip,
        'userid' => $userId,
        'timestamp' => time()
    ]);
    $api->GameAddNotification($userId, "There was a recent failed attempt to log into your account. Please change your password immediately. However, if this was you, ignore this.");
}

function clearLoginAttempts($userId): void {
    global $db;
    $userId = (int) $userId;
    $db->query("DELETE FROM login_attempts WHERE userid = {$userId}");
}

function authenticateUser($email, $password) {
    global $db;
    $emailEscaped = $db->escape(strtolower(trim($email)));
    $result = $db->query("SELECT `userid`, `password`, `user_level` FROM `users` WHERE email = '{$emailEscaped}' LIMIT 1");
    if ($db->num_rows($result)) {
        $user = $db->fetch_row($result);
        if (verify_user_password($password, $user['password'], $user['userid'])) {
            return ['userid' => $user['userid'],
                    'user_level' => $user['user_level']
            ];
        }
    }
    return null;
}

// Main Logic
$ip = getClientIP();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = (array_key_exists('email', $_POST) && is_string($_POST['email'])) ? $_POST['email'] : '';
    $password = (array_key_exists('password', $_POST) && is_string($_POST['password'])) ? $_POST['password'] : '';
    
    if (empty($email) || empty($password)) {
        die("Please provide both email and password.");
    }
    
    if (isLockedOut($ip)) {
        die("Too many login attempts. Please try again later.");
    }
    
    $user = authenticateUser($email, $password);
    
    if ($user) {
        session_regenerate_id(true);
        $_SESSION['userid'] = $user['userid'];
        $uade=$db->query("/*qc=on*/SELECT * FROM `user_settings` WHERE `userid` = {$user['userid']}");
        if ($db->num_rows($uade) == 0)
        {
            $randomPhrase = randomizer();
            $db->query("INSERT INTO `user_settings` (`userid`, `security_key`) VALUES ('{$user['userid']}', '{$randomPhrase}')");
        }
        $_SESSION['loggedin'] = 1;
        $_SESSION['last_login'] = time();
        setcookie('login_expire', time() + 604800, time() + 604800);
        $invis=$db->fetch_single($db->query("/*qc=on*/SELECT `invis` FROM `user_settings` WHERE `userid` = {$user['userid']}"));
        if ($invis < time())
        {
            $db->query("UPDATE `users`
              SET `loginip` = '{$ip}',
              `last_login` = '" . time() . "',
              `laston` = '" . time() . "'
               WHERE `userid` = {$user['userid']}");
        }
        else
        {
            $db->query("UPDATE `users`
              SET `loginip` = '{$ip}'
               WHERE `userid` = {$user['userid']}");
        }
        
        clearLoginAttempts($user['userid']);
        if (Random(1,10) == 6)
        {
            $encpsw = encode_password($password,$user['user_level']);
            $e_encpsw = $db->escape($encpsw);
            $db->query("UPDATE `users` SET `password` = '{$e_encpsw}' WHERE `userid` = {$user['userid']}");
        }
        header("Location: loggedin.php");
        exit;
    } else {
        logLoginAttempt($ip, 0); // Log failed attempt
        die("Login failed."); // Vague error to prevent info leaks
    }
}
?>
