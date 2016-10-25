<?php
/**
 * Password Hash Cost Calculator
 *
 * Set the ideal time that you want a password_hash() call to take and this
 * script will keep testing until it finds the ideal cost value and let you
 * know what to set it to when it has finished
 */
echo "<h3>Password Hash Cost Calculator</h3><hr />";
if (isset($_POST['password']))
{
	$mSec = $_POST['ms'];
	$password = $_POST['password'];
	echo "Testing BCRYPT hashing the password '{$password}'. We're going to run until the time to generate the hash takes longer than {$mSec}ms<br />";
	$cost = 3;
	do
	{
		$cost++;
		echo "<br />Testing cost value of {$cost}: ";
		$time = benchmark($password, $cost);
		echo " {$time} milliseconds";
	}
	while ($time < ($mSec/1000));
	echo "<br /><br /><b>Ideal cost is {$cost}.</b> Testing cost {$cost} 100 times to check the average...";
	$start = microtime(true);
	$times = [];
	for ($i=1;$i<=100;$i++) 
	{
		$times[] = benchmark($password, $cost);
	}
	echo "<br /><br />Benchmark completed in ".(microtime(true)-$start)." milliseconds.<br />";
	echo "<br />Slowest time: ".max($times). " milliseconds.";
	echo "<br />Fastest time: ".min($times). " milliseconds.";
	echo "<br />Average time: ".(array_sum($times)/count($times)). " milliseconds.";
	echo "<br /><br />Finished. Ideal cost is {$cost}<br />";
}
else
{
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
function benchmark($password, $cost=4)
{
    $start = microtime(true);
    password_hash($password, PASSWORD_BCRYPT, ['cost'=>$cost]);
    return microtime(true) - $start;
}