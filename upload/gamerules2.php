<?php
require('globals_nonauth.php');
$currentpage = $_SERVER['REQUEST_URI'];
$cpage = strip_tags(stripslashes($currentpage));
$domain=determine_game_urlbase();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<center>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="TheMasterGeneral">

    <?php echo "<title>{$set['WebsiteName']}</title>"; ?>

    <!-- CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<meta name="theme-color" content="#e7e7e7">
	<link href="css/bs2.css" rel="stylesheet">
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

    <!-- Custom CSS -->
    <style>
    body {
        padding-top: 70px;
        /* Required padding for .navbar-fixed-top. Remove if using .navbar-static-top. Change if height of navigation changes. */
    }
	a {
		color: gray;
	}
    </style>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php"><?php echo $set['WebsiteName'] ?></a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="register.php"> <span class="glyphicon glyphicon-user"></span> <?php echo"{$lang['LOGIN_REGISTER']}"; ?></a>
                    </li>
                    <li>
                        <a href="gamerules2.php"><?php echo"{$lang['LOGIN_RULES']}"; ?></a>
                    </li>
                </ul>
				<ul class="nav navbar-nav navbar-right">
        <li><p class="navbar-text"><?php echo"{$lang['LOGIN_AHA']}"; ?></p></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><b> <span class="glyphicon glyphicon-log-in"></span> <?php echo"{$lang['LOGIN_LOGIN']}"; ?></b> <span class="caret"></span></a>
			<ul id="login-dp" class="dropdown-menu">
				<li>
					 <div class="row">
							<div class="col-md-12">
								 <?php echo"{$lang['LOGIN_LWE']}"; ?>
								 <form class="form" role="form" method="post" action="authenticate.php" accept-charset="UTF-8" id="login-nav">
										<div class="form-group">
											 <label class="sr-only" for="exampleInputEmail2"><?php echo"{$lang['LOGIN_EMAIL']}"; ?></label>
											 <input type="email" class="form-control" id="exampleInputEmail2" placeholder="<?php echo"{$lang['LOGIN_EMAIL']}"; ?>" name="email" required>
										</div>
										<div class="form-group">
											 <label class="sr-only" for="exampleInputPassword2"><?php echo"{$lang['LOGIN_PASSWORD']}"; ?></label>
											 <input type="password" class="form-control" id="exampleInputPassword2" name="password" placeholder="<?php echo"{$lang['LOGIN_PASSWORD']}"; ?>" required>
										</div>
										<?php echo"<input type='hidden' name='page' value='{$cpage}'>"; ?>
										<div class="form-group">
											 <button type="submit" class="btn btn-primary btn-block"><?php echo"{$lang['LOGIN_SIGNIN']}"; ?></button>
										</div>
								 </form>
							</div>
							<div class="bottom text-center">
								<?php echo"{$lang['LOGIN_NH']}"; ?>
							</div>
					 </div>
				</li>
			</ul>
        </li>
      </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>
	<!-- Page Content -->
    <div class="container">
		<noscript>
			<?php
				alert('danger','Javascript Disabled!','You need to enable Javascript to use this website. Loads of features will not work without Javascript.',false);
			?>
		</noscript>

        <div class="row">
            <div class="col-lg-12 text-center">
				<?php
				echo "<h3>{$set['WebsiteName']} {$lang['GAMERULES_TITLE']}</h3>
				<hr />
				{$lang['GAMERULES_TEXT']}<hr />";
				$q=$db->query("SELECT * FROM `gamerules` ORDER BY `rule_id` ASC");
				echo "<ol>";
				while ($r = $db->fetch_row($q))
				{
					echo "<li>{$r['rule_text']}</li><hr />";
				}
				echo"</ol>";
				echo"</div>
					</div>
				</div>
			</div>";
			
			require("footer.php");