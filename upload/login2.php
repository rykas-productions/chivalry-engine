<?php
$menuhide=1;
require('globals_nonauth.php');
$csrf = request_csrf_html('login');
?>
<form class="form-signin" action="authenticate.php" method="post">
	<h2 class="form-signin-heading">Chivalry is Dead Login</h2>
	<label for="inputEmail" class="sr-only">Email address</label>
	<input type="email" class="form-control" id="email" placeholder="email@example.com" name="email" required>
	<label for="inputPassword" class="sr-only">Password</label>
	<input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
	<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
	<?php echo $csrf; ?>
  </form>
  <hr />
  <a href="register.php">New around here? Sign up</a><br />
	<a href="pwreset.php">Forgot password?</a>
	<hr />
	<a href="login.php">Need more info? Check out the main page!</a>