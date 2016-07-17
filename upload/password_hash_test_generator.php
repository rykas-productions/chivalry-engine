<?php
/**
 * Password Hash Cost Calculator
 *
 * Set the ideal time that you want a password_hash() call to take and this
 * script will keep testing until it finds the ideal cost value and let you
 * know what to set it to when it has finished
 */
// Milliseconds that a hash should take (ideally)
$mSec = 100;
$password = 'Bx^PzAeCp8w?+]Y';
echo "<br />Password Hash Cost Calculator<br /><br />";
echo "Testing BCRYPT hashing the password '$password'<br /><br />";
echo "We're going to run until the time to generate the hash takes longer than {$mSec}ms<br />";
$cost = 3;
do {
    $cost++;
    echo "<br />Testing cost value of $cost: ";
    $time = benchmark($password, $cost);
    echo "... took $time";
} while ($time < ($mSec/1000));
echo "<br /><br />Ideal cost is $cost<br />";
echo "<br />Running 100 times to check the average:<br />";
$start = microtime(true);
$times = [];
for ($i=1;$i<=100;$i++) {
    echo "\r$i/100";
    $times[] = benchmark($password, $cost);
}
echo "<br /><br />done benchmarking in ".(microtime(true)-$start)."<br />";
echo "<br />Slowest time: ".max($times);
echo "<br />Fastest time: ".min($times);
echo "<br />Average time: ".(array_sum($times)/count($times));
echo "<br /><br />Finished<br />";
function benchmark($password, $cost=4)
{
    $start = microtime(true);
    password_hash($password, PASSWORD_BCRYPT, ['cost'=>$cost]);
    return microtime(true) - $start;
}