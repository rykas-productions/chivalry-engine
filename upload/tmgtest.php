<?php
function benchmark_heavy_task($maxNumber = 10000) {
    $start = microtime(true);
    
    // Heavy computation: find primes up to $maxNumber using a simple sieve
    $primes = [];
    for ($i = 2; $i <= $maxNumber; $i++) {
        $isPrime = true;
        for ($j = 2; $j <= sqrt($i); $j++) {
            if ($i % $j === 0) {
                $isPrime = false;
                break;
            }
        }
        if ($isPrime) {
            $primes[] = $i;
        }
    }
    
    $end = microtime(true);
    $elapsed = $end - $start;
    
    // Assign score inversely proportional to time taken
    // The faster, the higher the score.
    // This formula can be adjusted for your needs.
    $score = round(1000 / max($elapsed, 0.001), 2); // Avoid division by zero
    
    return [
        'time' => $elapsed,
        'score' => $score,
        'primes_found' => count($primes)
    ];
}

function get_cpu_info() {
    $cpu = [
        'name' => 'Unknown',
        'cores' => 0,
        'speed_mhz' => 0
    ];
    
    if (stristr(PHP_OS, 'Linux')) {
        // Detect Android
        $isAndroid = file_exists('/system/build.prop') || shell_exec('getprop ro.build.version.release');
        
        $cpuinfo = @file_get_contents('/proc/cpuinfo');
        
        if ($isAndroid) {
            // Try getprop keys
            $props = [
                'ro.board.platform',
                'ro.soc.manufacturer',
                'ro.hardware',
                'ro.product.board',
                'ro.product.model'
            ];
            
            $chipInfo = [];
            foreach ($props as $key) {
                $value = trim(shell_exec("getprop $key"));
                if ($value) $chipInfo[] = $value;
            }
            
            // Build name string
            $cpu['name'] = implode(' ', array_unique(array_filter($chipInfo)));
            
            // Fallback if empty
            if (!$cpu['name']) {
                preg_match('/Hardware\s+:\s+(.+)/', $cpuinfo, $hw);
                $cpu['name'] = $hw[1] ?? 'Android CPU';
            }
            
            // Cores
            preg_match_all('/^processor\s+:/m', $cpuinfo, $cores);
            $cpu['cores'] = count($cores[0]) ?: intval(shell_exec("nproc 2>/dev/null")) ?: 1;
            
            // Speed (approximate or default)
            preg_match('/cpu MHz\s+:\s+([\d\.]+)/', $cpuinfo, $speed);
            $cpu['speed_mhz'] = $speed[1] ?? 1800; // Most Snapdragon Gen 2 have 1800–3200 MHz base speeds
        } else {
            // Regular Linux
            preg_match('/model name\s+:\s+(.+)/', $cpuinfo, $name);
            preg_match_all('/^processor\s+:/m', $cpuinfo, $cores);
            preg_match('/cpu MHz\s+:\s+([\d\.]+)/', $cpuinfo, $speed);
            
            if (!empty($name[1])) $cpu['name'] = trim($name[1]);
            $cpu['cores'] = count($cores[0]);
            if (!empty($speed[1])) $cpu['speed_mhz'] = floatval($speed[1]);
        }
    } elseif (stristr(PHP_OS, 'WIN')) {
        // Windows code unchanged
        $output = [];
        exec('wmic cpu get Name,NumberOfCores,MaxClockSpeed /format:list', $output);
        foreach ($output as $line) {
            if (stripos($line, 'Name=') === 0) {
                $cpu['name'] = trim(substr($line, 5));
            } elseif (stripos($line, 'NumberOfCores=') === 0) {
                $cpu['cores'] = intval(substr($line, 14));
            } elseif (stripos($line, 'MaxClockSpeed=') === 0) {
                $cpu['speed_mhz'] = floatval(substr($line, 13));
            }
        }
        
        if (empty($cpu['speed_mhz'])) {
            $speedOutput = [];
            exec('powershell -command "Get-WmiObject Win32_Processor | Select-Object -ExpandProperty MaxClockSpeed"', $speedOutput);
            if (isset($speedOutput[0]) && is_numeric($speedOutput[0])) {
                $cpu['speed_mhz'] = floatval($speedOutput[0]);
            }
        }
    } elseif (stristr(PHP_OS, 'Darwin')) {
        // macOS unchanged
        $name = trim(shell_exec("sysctl -n machdep.cpu.brand_string"));
        $cores = intval(trim(shell_exec("sysctl -n hw.physicalcpu")));
        $speed = floatval(trim(shell_exec("sysctl -n hw.cpufrequency")))/1e6;
        
        if ($name) $cpu['name'] = $name;
        if ($cores) $cpu['cores'] = $cores;
        if ($speed) $cpu['speed_mhz'] = $speed;
    }
    
    return $cpu;
}


// Example usage:
$cpuInfo = get_cpu_info();
$phpVersion = PHP_VERSION;
$os = PHP_OS;

$cpu = get_cpu_info();
// Example usage:
$result = benchmark_heavy_task(15000);
echo "Time taken: " . $result['time'] . " seconds<br />";
echo "Benchmark score: " . $result['score'] . "<br />";
echo "Primes found: " . $result['primes_found'] . "<br />";
echo "PHP: " . $phpVersion . "<br />";
echo "OS: " . $os . "<br />";
echo "CPU: {$cpu['cores']} x {$cpu['name']} @ {$cpu['speed_mhz']}<br />";