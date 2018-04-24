<?php

/*
	File:		header_nonauth.php
	Created: 	4/5/2016 at 12:05AM Eastern Time
	Info: 		Class file to load the template when outside of the game.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/

class headers
{
    function startheaders()
    {
        global $set, $h, $db, $menuhide;
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
                <meta property="og:description" content="<?php echo $set['Website_Description']; ?>"/>
                <meta http-equiv="Cache-control" content="public">
				<link rel="icon" sizes="192x192" href="https://res.cloudinary.com/dydidizue/image/upload/c_scale,h_192/v1520819749/logo.png">
				<link rel="icon" sizes="128x128" href="https://res.cloudinary.com/dydidizue/image/upload/c_scale,h_128/v1520819749/logo.png">
                <meta property="og:image" content="https://res.cloudinary.com/dydidizue/image/upload/v1520819511/logo-optimized.png"/>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/MasterGeneral156/chivalry-is-dead-game-cdn@1/css/bootstrap-v.1.5.min.css">
                <meta name="theme-color" content="rgba(0, 0, 0, .8)">
                <meta name="author" content="<?php echo $set['WebsiteOwner']; ?>">
                <?php echo "<title>{$set['WebsiteName']} - Free to Play, Text Themed RPG Based in Medieval Europe</title>"; ?>
        </head>
        <body>
        <?php
        if (!isset($menuhide)) {
            $csrf = request_csrf_html('login');
            ?>
            <!-- Navigation -->
            <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark">
                <a class="navbar-brand" href="index.php">
					<?php 
						echo "<img src='https://res.cloudinary.com/dydidizue/image/upload/v1520819511/logo-optimized.png' width='30' height='30' alt=''>
						{$set['WebsiteName']}"; 
					?>
				</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#CENGINENav"
                        aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="CENGINENav">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="register.php"><i
                                    class="fa fa-fw fa-user"></i> <?php echo "Register"; ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="gamerules2.php"><i
                                    class="fa fa-fw fa-server"></i> <?php echo "Game Rules"; ?></a>
                        </li>
						<li class="nav-item">
                            <a class="nav-link" href="privacy.php"><i
                                    class="fa fa-fw fa-user-secret"></i> <?php echo "Privacy Policy"; ?></a>
                        </li>
                    </ul>
                    <div class="my-2 my-lg-0">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item">
                                <div class='nav-link'><i class="fa fa-sign-in-alt" aria-hidden="true"></i> <?php echo "Already have an account?"; ?></div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown"
                                   aria-haspopup="true" aria-expanded="false">
                                    <?php echo "Log in"; ?>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
									<form class="px-4 py-3" method="post" action="authenticate.php">
										<p class="dropdown"><?php echo "Sign In!"; ?></p>
										<div class="form-group">
											<input type="email" class="form-control" id="email1" placeholder="email@example.com" name="email" required>
										</div>
										<div class="form-group">
											<input type="password" class="form-control" id="password1" name="password" placeholder="Password" required>
										</div>
                                        <div class="form-check">
                                            <input type="checkbox" name="remember" class="form-check-input" value="yes"> Remember Me
										</div>
										<button type="submit" class="btn btn-primary btn-block"><i class="fa fa-sign-in-alt" aria-hidden="true"></i> Sign in</button>
										<?php echo $csrf; ?>
									</form>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="register.php">New around here? Sign up!</a>
									<a class="dropdown-item" href="pwreset.php">Forgot password?</a>
								</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        <?php } ?>
        <!-- Page Content -->
        <div class="container">
        <div class="row">
        <div class="col-sm-12 text-center">
        <noscript>
            <?php alert('info', "Information!", "Please enable Javascript.", false); ?>
        </noscript>
        <?php
        $IP = $db->escape($_SERVER['REMOTE_ADDR']);
        $ipq = $db->query("/*qc=on*/SELECT `ip_id` FROM `ipban` WHERE `ip_ip` = '{$IP}'");
        if ($db->num_rows($ipq) > 0) {
            alert('danger', "Uh Oh!", "You are currently IP Banned. Sorry about that.", false);
            die($h->endpage());
        }
		date_default_timezone_set('America/New_York');
    }

    function endpage()
    {
        global $db, $ir, $set, $start;
        $query_extra = '';
		include('forms/analytics.php');
    if (isset($_GET['mysqldebug']) && $ir['user_level'] == 'Admin')
    {
        ?>
        <pre class='pre-scrollable'> <?php var_dump($db->queries) ?> </pre> <?php
    }
    ?>
        </div>
        </div>
        <!-- /.row -->

        </div>
        <!-- /.container -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/MasterGeneral156/chivalry-is-dead-game-cdn@1/css/game-v1.11.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/MasterGeneral156/chivalry-is-dead-game-cdn@1/css/game-icons.min.css">
        <link rel="shortcut icon" href="https://res.cloudinary.com/dydidizue/image/upload/v1520819511/logo-optimized.png" type="image/x-icon"/>
		
        <!-- jQuery Version 3.3.1 -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

        <!-- Other JavaScript -->
        <script src="https://cdn.jsdelivr.net/gh/MasterGeneral156/chivalry-is-dead-game-cdn@1/js/register.min.js" async defer></script>
		<script src="https://cdn.jsdelivr.net/gh/MasterGeneral156/chivalry-is-dead-game-cdn@1/js/clock.min.js"></script>
		<script src="https://use.fontawesome.com/releases/v5.0.4/js/all.js"></script>
		<script type="text/javascript"> 
		  $(document).ready(function(){ 
			customtimestamp = parseInt($("#jqclock").data("time"));
			$("#jqclock").clock({"langSet":"en","timestamp":customtimestamp,"timeFormat":" g:i:s a"}); 
		  }); 
		</script> 
        <footer class='footer'>
            <div class='container'>
				<span>
                <?php
				$timestamp=time()-14400;
                //Print copyright info, Chivalry Engine info, and current time.
                echo "<hr />
					Time is now <span id='jqclock' class='jqclock' data-time='{$timestamp}'>" . date('l, F j, Y g:i:s a') . "</span><br />
					{$set['WebsiteName']} &copy; " . date("Y") . " {$set['WebsiteOwner']}. Game source viewable on <a href='https://github.com/MasterGeneral156/chivalry-engine/tree/chivalry-is-dead-game'>Github</a>.<br />";
                include('forms/include_end.php');
				?>
				</span>
            </div>
        </footer>
		</body>
        </html>
    <?php
    }
}