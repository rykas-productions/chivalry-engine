<?php
/*
	File:		password_benchmark.php
	Created: 	4/5/2016 at 12:20AM Eastern Time
	Info: 		Test how fast your server is, and figure out what
				password hash level is right for your server!
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
?>
<!DOCTYPE html>
	<html lang="en">
		<head>
			<center>
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
			<title>Chivalry Engine Installer</title>
			<!-- CSS -->
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
			<meta name="theme-color" content="#e7e7e7">
			<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css">
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
		</head>
		<body>
			<div class="container">
				<div class="row">
					<div class="col-sm-12 text-center">
<?php
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
	echo "<br /><br />Benchmark completed in ".(microtime(true)-$start)." seconds.<br />";
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
		<input type='text' class='form-control' name='password' value='Bx^PzAeCp8w?+]Y' required='1'>
		<br />
		Ideal Time (In Milliseconds)
		<br />
		<input type='number' class='form-control' name='ms' value='100' required='1'>
		<br />
		<input type='submit' class='btn btn-primary'>
	</form>";
}
function benchmark($password, $cost=4)
{
    $start = microtime(true);
    password_hash($password, PASSWORD_BCRYPT, ['cost'=>$cost]);
    return microtime(true) - $start;
}
?>
		</div>
	</div>
</div>
<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
<script src="js/register-min.js"></script>
<script src="js/game.js"></script>
<script src='https://www.google.com/recaptcha/api.js'></script>
<script src="https://cdn.rawgit.com/tonystar/bootstrap-hover-tabs/v3.1.1/bootstrap-hover-tabs.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
</body>
<footer>
	<p>
		<hr />Powered with codes by <a href='https://twitter.com/MasterGeneralYT'>TheMasterGeneral</a>. Code viewable on <a href='https://github.com/MasterGeneral156/chivalry-engine'>GitHub</a>. Used with permission.
		&copy; <?php echo date("Y"); ?>
	</p>
</footer>
</html>