<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

<?php
/*
	File:		password_benchmark.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		The password benchmark tool to determine what password strength 
				you should use while installing Chivalry Engine.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
	MIT License

	Copyright (c) 2019 TheMasterGeneral

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
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
		<input type='text' class='form-control' name='password' value='Bx^PzAeCp8w?+]Y' required='1'>
		<br />
		Ideal Time (In Milliseconds)
		<br />
		<input type='number' name='ms' class='form-control' value='100' required='1'>
		<br />
		<input class='btn btn-primary' type='submit'>
	</form>";
}
function benchmark($password, $cost = 4)
{
    $start = microtime(true);
    $options = ['cost' => $cost,];
    password_hash(base64_encode(hash('sha256', $password, true)), PASSWORD_BCRYPT, $options);
    return microtime(true) - $start;
}