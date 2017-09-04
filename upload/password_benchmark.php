<?php
/*
	File:		password_benchmark.php
	Created: 	4/5/2016 at 12:20AM Eastern Time
	Info: 		Test how fast your server is, and figure out what
				password hash level is right for your server!
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
echo "<h3>Password Hash Cost Calculator</h3><hr />";
if (isset($_POST['password'])) {
    $mSec = (isset($_POST['ms']) && is_numeric($_POST['ms'])) ? abs($_POST['ms']) : 100;
    $password = $_POST['password'] = (isset($_POST['password']) && is_string($_POST['password'])) ? stripslashes($_POST['password']) : 'Bx^PzAeCp8w?+]Y';
    $password = htmlentities($password, ENT_QUOTES, 'ISO-8859-1');
    echo "Testing BCRYPT hashing the password '{$password}'. We're going to run until the time to generate the hash takes longer than {$mSec}ms<br />";
    $cost = 3;
    do {
        $cost++;
        echo "<br />Testing cost value of {$cost}: ";
        $time = benchmark($password, $cost);
        echo " {$time} milliseconds";
    } while ($time < ($mSec / 1000));
    echo "<br /><br /><b>Ideal cost is {$cost}.</b> Testing cost {$cost} 100 times to check the average...";
    $start = microtime(true);
    $times = [];
    for ($i = 1; $i <= 100; $i++) {
        $times[] = benchmark($password, $cost);
    }
    echo "<br /><br />Benchmark completed in " . (microtime(true) - $start) . " milliseconds.<br />";
    echo "<br />Slowest time: " . max($times) . " milliseconds.";
    echo "<br />Fastest time: " . min($times) . " milliseconds.";
    echo "<br />Average time: " . (array_sum($times) / count($times)) . " milliseconds.";
    echo "<br /><br />Finished. Ideal cost is {$cost}<br />";
} else {
    echo "Password Hash benchmark. Lets figure out the ideal time for your server. The default values are recommended values. Change them if you must.<br />
	<form method='post'>
		Test Password<br />
		<input type='text' name='password' value='Bx^PzAeCp8w?+]Y' required='1'>
		<br />
		Ideal Time (In Milliseconds)
		<br />
		<input type='number' name='ms' value='100' required='1'>
		<br />
		<input type='submit'>
	</form>";
}
function benchmark($password, $cost = 4)
{
    $start = microtime(true);
    password_hash($password, PASSWORD_BCRYPT, ['cost' => $cost]);
    return microtime(true) - $start;
}