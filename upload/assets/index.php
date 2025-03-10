<?php
function checkServerStatus($host, $port, $timeout = 2) {
    $status = false;
    $latency = null;
    
    // Start time for measuring latency
    $start_time = microtime(true);
    
    // Attempt to open the socket connection
    $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
    
    if ($socket) {
        // Calculate the latency (time taken to establish connection)
        $latency = round((microtime(true) - $start_time) * 1000); // Convert to milliseconds
        $status = true;
        fclose($socket);
    }
    
    return [$status, $latency]; // Return both status and latency
}

// Define servers to check
$servers = [
    ["name" => "Routed CDN", "ip" => "cdn.chivalryisdeadgame.com", "port" => 443],
    ["name" => "CDN #1", "ip" => "66.23.199.88", "port" => 80], // Website Example
    ["name" => "CDN #2", "ip" => "157.173.212.46", "port" => 80], // API Server Example
    ["name" => "CDN #3", "ip" => "193.23.249.20", "port" => 80],
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Server Status with Latency</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body { background-color: #212529; color: white; }
        .status-online { color: #28a745; font-weight: bold; }
        .status-offline { color: #dc3545; font-weight: bold; }
        .latency { font-weight: bold; }
        .server-box { background: #343a40; padding: 15px; border-radius: 10px; }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Server Status with Latency</h2>

    <div class="row">
        <?php foreach ($servers as $server): 
            list($isOnline, $latency) = checkServerStatus($server["ip"], $server["port"]); 
        ?>
        <div class="col-md-4 mb-3">
            <div class="server-box text-center">
                <h4><?php echo $server["name"]; ?></h4>
                <p class="<?php echo $isOnline ? 'status-online' : 'status-offline'; ?>">
                    <?php echo $isOnline ? '🟢 Online' : '🔴 Offline'; ?>
                </p>
                <?php if ($isOnline): ?>
                    <p class="latency">Latency: <?php echo $latency . ' ms'; ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>