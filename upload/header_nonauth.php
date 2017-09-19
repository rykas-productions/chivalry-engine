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
                <meta property="og:image" content=""/>
                <link rel="shortcut icon" href="" type="image/x-icon"/>
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css">
                <meta name="theme-color" content="#e7e7e7">
                <meta name="author" content="<?php echo $set['WebsiteOwner']; ?>">
                <?php echo "<title>{$set['WebsiteName']}</title>"; ?>
        </head>
        <body>
        <?php
        if (!isset($menuhide)) {
            $csrf = request_csrf_html('login');
            ?>
            <!-- Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <a class="navbar-brand" href="index.php"><?php echo $set['WebsiteName']; ?></a>
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
                    </ul>
                    <div class="my-2 my-lg-0">
                        <ul class="navbar-nav mr-auto">
                            <li class="navbar-text">
                                <i class="fa fa-sign-in" aria-hidden="true"></i> <?php echo "Already have an account?"; ?>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown"
                                   aria-haspopup="true" aria-expanded="false">
                                    <?php echo "Log in"; ?>
                                </a>
                                <ul id="login-dp" class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p class="dropdown"><?php echo "Sign In! <a href='pwreset.php'>Forgot Password?</a>"; ?></p>

                                                <form role="form" method="post" action="authenticate.php"
                                                      accept-charset="UTF-8" id="login-nav">
                                                    <?php echo $csrf; ?>
                                                    <div class="form-group">
                                                        <label class="sr-only"
                                                               for="exampleInputEmail2"><?php echo "Email Address"; ?></label>
                                                        <input type="email" class="form-control" id="exampleInputEmail2"
                                                               placeholder="<?php echo "Your Email Address"; ?>"
                                                               name="email" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="sr-only"
                                                               for="exampleInputPassword2"><?php echo "Password"; ?></label>
                                                        <input type="password" class="form-control"
                                                               id="exampleInputPassword2" name="password"
                                                               placeholder="<?php echo "Your Password"; ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="submit"
                                                                class="btn btn-primary btn-block"><?php echo "Sign In"; ?></button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </li>
                                    <div class="bottom text-center">
                                        <?php echo "New here? <a href='register.php'>Sign up</a> for an account!"; ?>
                                    </div>
                                </ul>
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
        $ipq = $db->query("SELECT `ip_id` FROM `ipban` WHERE `ip_ip` = '{$IP}'");
        if ($db->num_rows($ipq) > 0) {
            alert('danger', "Uh Oh!", "You are currently IP Banned. Sorry about that.", false);
            die($h->endpage());
        }
    }

    function endpage()
    {
        global $db, $ir;
        $query_extra = '';
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
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css">
        <link rel="stylesheet" href="css/game.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <!-- jQuery Version 3.2.1 -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js"></script>

        <!-- Other JavaScript -->
        <script src="js/register-min.js" async defer></script>
        <script src='https://www.google.com/recaptcha/api.js' async defer></script>
        </body>
        <footer>
            <p>
                <br/>
                <?php
                echo "<hr />
					Time is now " . date('F j, Y') . " " . date('g:i:s a') . "<br />
					Powered with codes by TheMasterGeneral. View source on <a href='https://github.com/MasterGeneral156/chivalry-engine'>Github</a>.";
                ?>
                &copy; <?php echo date("Y");
                ?>
            </p>
        </footer>
        </html>
    <?php
    }
}