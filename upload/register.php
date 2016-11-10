<?php
require("globals_nonauth.php");
$currentpage = $_SERVER['REQUEST_URI'];
$cpage = strip_tags(stripslashes($currentpage));
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<center>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <?php echo "<title>{$set['WebsiteName']}</title>"; ?>

    <!-- CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
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
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">Chivalry Engine</a>
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
	<?php
	$IP = $db->escape($_SERVER['REMOTE_ADDR']);
	/*if ($db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `lastip` = '{$IP}' OR `loginip` = '{$IP}' OR `registerip` = '{$IP}'")) >= 1)
	{
		alert('danger',"{$lang['ERROR_SECURITY']}","{$lang['REG_MULTIALERT']}");
		require('footer.php');
		exit;
	}*/
	if (!isset($_GET['REF']))
    {
        $_GET['REF'] = 0;
    }
    $_GET['REF'] = abs((int) $_GET['REF']);
    if ($_GET['REF'])
    {
        $_GET['REF']=$_GET['REF'];
    }
	$username =
        (isset($_POST['username']) && preg_match( "/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $_POST['username'])
                && ((strlen($_POST['username']) < 20) && (strlen($_POST['username']) >= 3))) ? stripslashes($_POST['username']) : '';
	if (!empty($username))
	{
		if ($set['RegistrationCaptcha'] == 'ON')
		{
			if (!$_SESSION['captcha'] || !isset($_POST['captcha']) || $_SESSION['captcha'] != $_POST['captcha'])
			{
				unset($_SESSION['captcha']);
				alert('danger',"{$lang['ERROR_INVALID']}","{$lang['REG_CAPTCHAERROR']}");
				require("footer.php");
				exit;
			}
			unset($_SESSION['captcha']);
		}
		if (!isset($_POST['email']) || !valid_email(stripslashes($_POST['email'])))
		{
			alert('danger',"{$lang['ERROR_INVALID']}","{$lang['REG_EMAILERROR']}");
			require("footer.php");
			exit;
		}
		// Check Gender
		if (!isset($_POST['gender']) || ($_POST['gender'] != 'Male' && $_POST['gender'] != 'Female'))
		{
			alert('danger',"{$lang['ERROR_INVALID']}","{$lang['REG_GENDERERROR']}");
			require("footer.php");
			exit;
		}
		if (!isset($_POST['class']) || ($_POST['class'] != 'Warrior' && $_POST['class'] != 'Rogue' && $_POST['class'] != 'Defender'))
		{
			alert('danger',"{$lang['ERROR_INVALID']}","{$lang['REG_CLASSERROR']}");
			require("footer.php");
			exit;
		}
		$e_gender = $db->escape(stripslashes($_POST['gender']));
		$e_class = $db->escape(stripslashes($_POST['class']));
		$sm = 100;
		if (isset($_POST['promo']) && $_POST['promo'] == "CENGINE")
		{
			$sm += 100;
		}
		$e_username = $db->escape($username);
		$e_email = $db->escape(stripslashes($_POST['email']));
		$q = $db->query("SELECT COUNT(`userid`) FROM `users` WHERE `username` = '{$e_username}'");
		$q2 = $db->query("SELECT COUNT(`userid`)  FROM `users` WHERE `email` = '{$e_email}'");
		$u_check = $db->fetch_single($q);
		$e_check = $db->fetch_single($q2);
		$db->free_result($q);
		$db->free_result($q2);
		$base_pw = (isset($_POST['password']) && is_string($_POST['password'])) ? stripslashes($_POST['password']) : '';
		$check_pw = (isset($_POST['cpassword']) && is_string($_POST['cpassword'])) ? stripslashes($_POST['cpassword']) : '';
		if ($u_check > 0)
		{
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['REG_UNIUERROR']}");
		}
		else if ($e_check > 0)
		{
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['REG_EIUERROR']}");		
		}
		else if (empty($base_pw) || empty($check_pw))
		{
			alert('danger',"{$lang['ERROR_EMPTY']}","{$lang['REG_PWERROR']}");	
		}
		else if ($base_pw != $check_pw)
		{
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['REG_VPWERROR']}");	
		}
		else
		{
			$_POST['ref'] = (isset($_POST['ref']) && is_numeric($_POST['ref'])) ? abs(intval($_POST['ref'])) : '';
			$IP = $db->escape($_SERVER['REMOTE_ADDR']);
			if ($_POST['ref'])
			{
				$q = $db->query("SELECT `lastip` FROM `users` WHERE `userid` = {$_POST['ref']}");
				if ($db->num_rows($q) == 0)
				{
					$db->free_result($q);
					alert('danger',"{$lang['ERROR_NONUSER']}","{$lang['REG_REFERROR']}");
					require("footer.php");
					exit;
				}
				$rem_IP = $db->fetch_single($q);
				$db->free_result($q);
				if ($rem_IP == $_SERVER['REMOTE_ADDR'])
				{
					alert('danger',"{$lang['ERROR_SECURITY']}","{$lang['REG_REFMERROR']}");
					require("footer.php");
					exit;
				}
			}
			$encpsw = encode_password($base_pw);
			$e_encpsw = $db->escape($encpsw);
			$profilepic="https://www.gravatar.com/avatar/" . md5(strtolower(trim($e_email))) . "?s=250.jpg";
			$CurrentTime=time();
			$db->query("INSERT INTO `users`
			(`username`,`email`,`password`,`level`,`gender`,`class`,`lastip`,`registerip`,`registertime`,`loginip`,`display_pic`)
			VALUES
			('{$e_username}','{$e_email}','{$e_encpsw}','1','{$e_gender}','{$e_class}','{$IP}','{$IP}','{$CurrentTime}', '127.0.0.1', '{$profilepic}')");
			$i = $db->insert_id();
			$db->query("UPDATE `users` SET `brave`='10',`maxbrave`='10',`hp`='100',`maxhp`='100',`maxwill`='100',`will`='100',`energy`='24',`maxenergy`='24' WHERE `userid`={$i}");
			if ($e_class == 'Warrior')
			{
				$db->query(
						"INSERT INTO `userstats`
						 VALUES($i, 1100, 1000, 900, 1000, 1000)");
			}
			if ($e_class == 'Rogue')
			{
				$db->query(
						"INSERT INTO `userstats`
						 VALUES($i, 900, 1100, 1000, 1000, 1000)");
			}
			if ($e_class == 'Defender')
			{
				$db->query(
						"INSERT INTO `userstats`
						 VALUES($i, 1000, 900, 1100, 1000, 1000)");
			}
			if ($_POST['ref'])
			{
				$db->query("UPDATE `users` SET `secondary_currency` = `secondary_currency` + {$set['ReferalKickback']} WHERE `userid` = {$_POST['ref']}");
				event_add($_POST['ref'],
						"For refering $username to the game, you have earned {$set['ReferalKickback']} valuable Secondary Currency(s)!",
						$c);
				$e_rip = $db->escape($rem_IP);
				$db->query("INSERT INTO `referals`
				VALUES
				(NULL, {$_POST['ref']}, '{$e_rip}', {$i}, '{$IP}',{$CurrentTime})");
			}
			$db->query("INSERT INTO `infirmary` 
				(`infirmary_user`, `infirmary_reason`, `infirmary_in`, `infirmary_out`) 
				VALUES ('{$i}', 'N/A', '0', '0');");
			$db->query("INSERT INTO `dungeon` 
				(`dungeon_user`, `dungeon_reason`, `dungeon_in`, `dungeon_out`) 
				VALUES ('{$i}', 'N/A', '0', '0');");
			
			alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['REG_SUCCESS']}<br />
			<a href='login.php'>{$lang['LOGIN_SIGNIN']}</a>");
		}
		require('footer.php');
	}
	else
	{
	?>
        <div class="row">
            <div class="col-lg-12 text-center">
			<?php
			echo "<h3>{$set['WebsiteName']} {$lang['REG_FORM']}</h3>";
			?>
				<div id='usernameresult'></div>
				<div id='cpasswordresult'></div>
				<div id='emailresult'></div>
				<div id='teamresult'></div>
				<table class='table table-bordered table-hover'>
					<form method='post'>
						<tbody>
							<tr>
								<td><h4><?php echo"{$lang['REG_USERNAME']}"; ?></h4></td>
								<td><input type="text" class="form-control" id="username" name="username" minlength="3" maxlength="20" placeholder="Enter a valid username here." onkeyup='CheckUsername(this.value);' required></td>
							</tr>
							<tr>
								<td><h4><?php echo"{$lang['REG_EMAIL']}"; ?></h4></td>
								<td><input type="email" class="form-control" id="email" name="email" minlength="3" maxlength="256" placeholder="Enter your email! We promise not to spam you!" onkeyup='CheckEmail(this.value);' required></td>
							</tr>
							<tr>
								<td><h4><?php echo"{$lang['REG_PW']}"; ?></h4></td>
								<td><input type="password" class="form-control" id="password" name="password" minlength="3" maxlength="256" placeholder="Enter your desired password!" onkeyup='CheckPasswords(this.value);PasswordMatch();' required>
								<div id='passwordresult'></div></td>
							</tr>
							<tr>
								<td><h4><?php echo"{$lang['REG_CPW']}"; ?></h4></td>
								<td><input type="password" class="form-control" id="cpassword" name="cpassword" minlength="3" maxlength="256" placeholder="Confirm the password you just entered." onkeyup='PasswordMatch();' required></td>
							</tr>
							<tr>
								<td><h4><?php echo"{$lang['REG_SEX']}"; ?></h4></td>
								<td><select name='gender' class='form-control' type='dropdown'><option value='Male'>Male</option><option value='Female'>Female</option></select></td>
							</tr>
							<tr>
								<td><h4><?php echo"{$lang['REG_CLASS']}"; ?></h4></td>
								<td><select name='class' id='class' class='form-control' onchange='OutputTeam(this)' type='dropdown'><option></option><option value='Warrior'>Warrior</option><option value='Rogue'>Rogue</option><option value='Defender'>Defender</option></select></td>
							</tr>
							<tr>
								<td><h4><?php echo"{$lang['REG_REFID']}"; ?></h4></td>
								<?php echo "<td><input type='number' value='{$_GET['REF']}' class='form-control' id='ref' name='ref' min='0' placeholder='Enter the User ID of who referred you. (Optional)'></td>";
							?>
							</tr>
						</tbody>
				
			<?php
    if ($set['RegistrationCaptcha'] == 'ON')
    {
        echo "<tr>
				<td colspan='3'>
					<img src='captcha_verify.php?bgcolor=C3C3C3' /><br />
					<input type='text' name='captcha' />
				</td>
			  </tr>";
    }
    echo "<tr>
				<td colspan='3' align='center'>
					<input type='submit' class='btn btn-default' value='{$lang["LOGIN_REGISTER"]}' />
				</td>
			</tr>
	</table>
	</form>
	<br />
	&gt; <a href='login.php'>{$lang["LOGIN_LOGIN"]}</a>";
	}
require("footer.php");