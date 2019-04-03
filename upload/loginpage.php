<?php
$menuhide=1;
require('globals_nonauth.php');
$csrf=request_csrf_html('login');
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Chivalry is Dead</title>

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
    <!-- Custom styles for this template -->
    <link href="signin.css" rel="stylesheet">
  </head>
<?php
$csrf=request_csrf_html('login'); 
?>
<link href="https://getbootstrap.com/docs/4.2/examples/sign-in/signin.css" rel="stylesheet">
<form class="form-signin" method="post" action="authenticate.php">
  <img class="mb-4" src="https://res.cloudinary.com/dydidizue/image/upload/v1520819511/logo.png" alt="" width="72" height="72">
  <h1 class="h3 mb-3 font-weight-normal">Chivalry is Dead Sign-In</h1>
  <label for="email" class="sr-only">Email address</label>
  <input type="email" id="email" name="email" class="form-control" placeholder="email@example.com" required autofocus>
  <label for="inputPassword" class="sr-only">Password</label>
  <input type="password" id="inputPassword" class="form-control" name="password" placeholder="Password" required>
  <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
  <?php echo $csrf; ?>
  <hr />
  <a href="register.php">New around here? Sign up!</a><br />
  <a href="pwreset.php">Forgot password?</a>
  <hr />
  <?php loginbutton('rectangle'); ?>
  <br />
  > <a href="login.php">Go Back</a>
</form>