<?php

function benchmarkTask($callback) {
    $startTime = microtime(true);
    $callback();
    $endTime = microtime(true);
    return $endTime - $startTime;
}

// Task 1: Fibonacci sequence calculation
function fibonacci($n) {
    if ($n <= 1) return $n;
    return fibonacci($n - 1) + fibonacci($n - 2);
}

$timeFibonacci = benchmarkTask(function() {
    fibonacci(30); // Change this number for more or less intensity
});

// Task 2: Prime number calculation
function isPrime($n) {
    if ($n <= 1) return false;
    for ($i = 2; $i <= sqrt($n); $i++) {
        if ($n % $i == 0) return false;
    }
    return true;
}

$timePrime = benchmarkTask(function() {
    $count = 0;
    for ($i = 2; $i < 10000; $i++) {
        if (isPrime($i)) $count++;
    }
});

// Task 3: Sorting a large array
$timeSort = benchmarkTask(function() {
    $array = range(1, 100000);
    shuffle($array);
    sort($array);
});

// Task 4: String manipulation
$timeStringManipulation = benchmarkTask(function() {
    $str = str_repeat("Benchmarking PHP ", 100000);
    $reversed = strrev($str);
});

// Total time and scoring
$totalTime = $timeFibonacci + $timePrime + $timeSort + $timeStringManipulation;
$score = max(1000 - ($totalTime * 100), 0);

echo "Fibonacci Task Time: " . round($timeFibonacci, 4) . " seconds\n";
echo "Prime Calculation Task Time: " . round($timePrime, 4) . " seconds\n";
echo "Array Sorting Task Time: " . round($timeSort, 4) . " seconds\n";
echo "String Manipulation Task Time: " . round($timeStringManipulation, 4) . " seconds\n";
echo "Total Benchmark Time: " . round($totalTime, 4) . " seconds\n";
echo "Benchmark Score: " . round($score, 2) . "\n";

?>