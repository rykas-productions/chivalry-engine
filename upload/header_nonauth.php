<?php
class headers
{
    function startheaders()
    {
		global $ir,$set,$h,$lang,$db,$menuhide,$userid,$macropage,$api,$time;
		?>
		<!DOCTYPE html>
		<html lang="en">
		<head>
			<center>
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
			<meta name="description" content="<?php echo $set['Website_Description']; ?>">
			<meta property="og:title" content="<?php echo $set['WebsiteName']; ?>"/>
			<meta property="og:description" content="<?php echo $set['Website_Description']; ?>" />
			<meta property="og:image" content="http://vignette1.wikia.nocookie.net/helmet-heroes/images/e/e0/Knight_Helmet.png/revision/latest?cb=20131008030002" />
			<link rel="shortcut icon" href="http://vignette1.wikia.nocookie.net/helmet-heroes/images/e/e0/Knight_Helmet.png/revision/latest?cb=20131008030002" type="image/x-icon" />
			<meta name="author" content="<?php echo $set['WebsiteOwner']; ?>">
			<?php echo "<title>{$set['WebsiteName']}</title>"; ?>
			<!-- CSS -->
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
			<meta name="theme-color" content="#e7e7e7">
			<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css">
			<link rel="stylesheet" href="css/bs2.css">
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
		</head>
		<body>
			<!-- Navigation -->
			<nav class="navbar navbar-light bg-faded navbar-toggleable-md">
				<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#CENGINENav" aria-controls="CENGINENav" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<a class="navbar-brand" href="index.php"><?php echo $set['WebsiteName']; ?></a>
				<div class="collapse navbar-collapse" id="CENGINENav">
					<ul class="navbar-nav mr-auto">
						<li class="nav-item">
							<a class="nav-link" href="register.php"><i class="fa fa-fw fa-user"></i> <?php echo $lang['LOGIN_REGISTER']; ?></a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="gamerules2.php"><i class="fa fa-fw fa-server"></i> <?php echo $lang['LOGIN_RULES']; ?></a>
						</li>
					</ul>
					<div class="my-2 my-lg-0">
						<ul class="navbar-nav mr-auto">
							<li class="navbar-text">
								<i class="fa fa-sign-in" aria-hidden="true"></i> <?php echo"{$lang['LOGIN_AHA']}"; ?>
							</li>
							<li class="nav-item dropdown">
									<a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<?php echo $lang['LOGIN_LOGIN']; ?>
									</a>
									<ul id="login-dp" class="dropdown-menu dropdown-menu-right">
										<li>
											<div class="row">
												<div class="col-md-12">
													<p class="dropdown"><?php echo $lang['LOGIN_LWE']; ?></p>
													<form role="form" method="post" action="authenticate.php" accept-charset="UTF-8" id="login-nav">
													<div class="form-group">
														 <label class="sr-only" for="exampleInputEmail2"><?php echo"{$lang['LOGIN_EMAIL']}"; ?></label>
														 <input type="email" class="form-control" id="exampleInputEmail2" placeholder="<?php echo"{$lang['LOGIN_EMAIL']}"; ?>" name="email" required>
													</div>
													<div class="form-group">
														 <label class="sr-only" for="exampleInputPassword2"><?php echo"{$lang['LOGIN_PASSWORD']}"; ?></label>
														 <input type="password" class="form-control" id="exampleInputPassword2" name="password" placeholder="<?php echo"{$lang['LOGIN_PASSWORD']}"; ?>" required>
													</div>
													<div class="form-group">
														 <button type="submit" class="btn btn-primary btn-block"><?php echo"{$lang['LOGIN_SIGNIN']}"; ?></button>
													</div>
											 </form>
												</div>
											</div>
										</li>
										<div class="bottom text-center">
											<?php echo"{$lang['LOGIN_NH']}"; ?>
										</div>
									</ul>
								</li>
						</ul>
					</div>
				</div>
			</nav>
			<!-- Page Content -->
			<div class="container">
				<div class="row">
					<div class="col-sm-12 text-center">
				<noscript>
					<?php alert('info',$lang['ERROR_INFO'],$lang['HDR_JS'],false); ?>
				</noscript>
				<?php
	}
	function endpage()
    {
        global $db, $ir, $lang;
        $query_extra = '';
        if (isset($_GET['mysqldebug']) && $ir['user_level'] == 'Admin')
        {
			?> <pre class='pre-scrollable'> <?php var_dump($db->queries) ?> </pre> <?php
        }
		?>
		</div>
			</div>
        <!-- /.row -->

			</div>
			<!-- /.container -->

			<!-- jQuery Version 3.1.1 -->
			<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>

			<!-- Bootstrap Core JavaScript -->
			<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
			
			<!-- Other JavaScript -->
			<script src="js/register-min.js"></script>
			<script src="js/game.js"></script>
			<script src='https://www.google.com/recaptcha/api.js'></script>
			<script src="https://cdn.rawgit.com/tonystar/bootstrap-hover-tabs/v3.1.1/bootstrap-hover-tabs.js"></script>
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		</body>
			<footer>
				<p>
					<br />
					<?php 
					echo "<hr />
					{$lang['MENU_TIN']}  
						" . date('F j, Y') . " " . date('g:i:s a') . "<br />
					{$lang['MENU_OUT']}";
					?>
					&copy; <?php echo date("Y");
					echo"<br/>{$db->num_queries} {$lang['MENU_QE']}.{$query_extra}<br />";
					?>
				</p>
			</footer>
		</html>
		<?php
	}
}