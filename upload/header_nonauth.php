<?php

/*
	File:		header_nonauth.php
	Created: 	4/5/2016 at 12:05AM Eastern Time
	Info: 		Class file to load the template when outside of the game.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require ('lib/steamauth/steamauth.php');
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
                <meta property='og:image' content='https://res.cloudinary.com/dydidizue/image/upload/c_scale,h_512/v1520819749/logo.png'/>
                <link rel="stylesheet" href="css/bright-castle.css">
					<meta name="theme-color" content="rgba(0, 0, 0, .8)">
                <meta name="author" content="<?php echo $set['WebsiteOwner']; ?>">
                <?php echo "<title>{$set['WebsiteName']} - Free to Play, Text Themed RPG Based in Medieval Europe</title>"; 
                ?>
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
        $remote = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '127.0.0.1';
        $IP = $db->escape($remote);
        $ipq = $db->query("/*qc=on*/SELECT `ip_id` FROM `ipban` WHERE `ip_ip` = '{$IP}'");
        if ($db->num_rows($ipq) > 0) {
            alert('danger', "Uh Oh!", "You are currently IP Banned. Sorry about that.", false);
            die($h->endpage());
        }
		date_default_timezone_set($set['game_time']);
    }

    function endpage()
    {
        global $db, $ir, $set, $start;
        $query_extra = '';
        include('ads/ad_nonlogin.html');
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
        <link rel="stylesheet" href="css/game-20.4.1.css">
        <link rel="stylesheet" href="https://seiyria.com/gameicons-font/css/game-icons.css">
        <link rel="shortcut icon" href="https://res.cloudinary.com/dydidizue/image/upload/v1520819511/logo-optimized.png" type="image/x-icon"/>
		
        <!-- jQuery Version 3.3.1 -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

        <!-- Other JavaScript -->
        <script src="https://cdn.jsdelivr.net/gh/MasterGeneral156/chivalry-is-dead-game-cdn@1/js/register.min.js" async defer></script>
		<script src="https://cdn.jsdelivr.net/gh/MasterGeneral156/chivalry-is-dead-game-cdn@1/js/clock.min.js"></script>
		<script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js"></script>
        <footer class='footer'>
            <div class='container'>
				<span>
                <?php
                //Print copyright info, Chivalry Engine info, and current time.
                echo "<hr />
					Time is now " . date('l, F j, Y g:i:s a') . "<br />
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