<?php
/*
	File:		register.php
	Created: 	4/5/2016 at 12:24AM Eastern Time
	Info: 		The registration form.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
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
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="<?php echo $set['Website_Description']; ?>">
	<meta property="og:title" content="<?php echo $set['WebsiteName']; ?>"/>
	<meta property="og:description" content="<?php echo $set['Website_Description']; ?>" />
	<meta property="og:image" content="http://vignette1.wikia.nocookie.net/helmet-heroes/images/e/e0/Knight_Helmet.png/revision/latest?cb=20131008030002" />
	<link rel="shortcut icon" href="http://vignette1.wikia.nocookie.net/helmet-heroes/images/e/e0/Knight_Helmet.png/revision/latest?cb=20131008030002" type="image/x-icon" />
	<meta name="author" content="<?php echo $set['WebsiteOwner']; ?>">
    <?php echo "<title>{$set['WebsiteName']}</title>"; ?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<meta name="theme-color" content="#e7e7e7">
	<link href="css/bs2.css" rel="stylesheet">
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <style>
    body 
	{
        padding-top: 70px;
		font-size: 16px;
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
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container">
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
					<li>
						<p class="navbar-text"><?php echo"{$lang['LOGIN_AHA']}"; ?></p>
					</li>
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
        </div>
    </nav>
    <div class="container">
	<?php
	$IP = $db->escape($_SERVER['REMOTE_ADDR']);
	//Check if someone is already registered on this IP.
	if ($db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `lastip` = '{$IP}' OR `loginip` = '{$IP}' OR `registerip` = '{$IP}'")) >= 1)
	{
		alert('danger',$lang['ERROR_SECURITY'],$lang['REG_MULTIALERT']);
		require('footer.php');
		exit;
	}
	if (!isset($_GET['REF']))
    {
        $_GET['REF'] = 0;
    }
    $_GET['REF'] = abs($_GET['REF']);
    if ($_GET['REF'])
    {
        $_GET['REF']=$_GET['REF'];
    }
	$username = (isset($_POST['username']) && is_string($_POST['username'])) ? stripslashes($_POST['username']) : '';
	if (!empty($username))
	{
		//If the registration captcha is enabled.
		if ($set['RegistrationCaptcha'] == 'ON')
		{
			//If the user got the captcha wrong.
			if (!$_SESSION['captcha'] || !isset($_POST['captcha']) || $_SESSION['captcha'] != $_POST['captcha'])
			{
				unset($_SESSION['captcha']);
				alert('danger',$lang['ERROR_INVALID'],$lang['REG_CAPTCHAERROR']);
				require("footer.php");
				exit;
			}
			unset($_SESSION['captcha']);
		}
		//If the email is inputted, and valid.
		if (!isset($_POST['email']) || !valid_email(stripslashes($_POST['email'])))
		{
			alert('danger',$lang['ERROR_INVALID'],$lang['REG_EMAILERROR']);
			require("footer.php");
			exit;
		}
		//If the username is empty
		if (empty($username))
		{
			alert('danger',$lang['ERROR_INVALID'],$lang['REG_USEREMPTY']);
			require("footer.php");
			exit;
		}
		//If the username is less than 3 characters and more than 20.
		if (((strlen($username) > 20) OR (strlen($username) < 3)))
		{
			alert('danger',$lang['ERROR_LENGTH'],$lang['UNC_LENGTH_ERROR']);
			require("footer.php");
			exit;
		}
		//Check Gender
		if (!isset($_POST['gender']) || ($_POST['gender'] != 'Male' && $_POST['gender'] != 'Female'))
		{
			alert('danger',$lang['ERROR_INVALID'],$lang['REG_GENDERERROR']);
			require("footer.php");
			exit;
		}
		//Check class
		if (!isset($_POST['class']) || ($_POST['class'] != 'Warrior' && $_POST['class'] != 'Rogue' && $_POST['class'] != 'Defender'))
		{
			alert('danger',$lang['ERROR_INVALID'],$lang['REG_CLASSERROR']);
			require("footer.php");
			exit;
		}
		$e_gender = $db->escape(stripslashes($_POST['gender']));
		$e_class = $db->escape(stripslashes($_POST['class']));
		$sm = 100;
		if (isset($_POST['promo']))
		{
			//$sm += 100;
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
		//Username is in use.
		if ($u_check > 0)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['REG_UNIUERROR']);
		}
		//Email is in use
		else if ($e_check > 0)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['REG_EIUERROR']);		
		}
		//Both passwords aren't entered
		else if (empty($base_pw) || empty($check_pw))
		{
			alert('danger',$lang['ERROR_EMPTY'],$lang['REG_PWERROR']);	
		}
		//The entered passwords match.
		else if ($base_pw != $check_pw)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['REG_VPWERROR']);	
		}
		else
		{
			$_POST['ref'] = (isset($_POST['ref']) && is_numeric($_POST['ref'])) ? abs($_POST['ref']) : '';
			$IP = $db->escape($_SERVER['REMOTE_ADDR']);
			//If the registrating user was referred to the game by someone.
			if ($_POST['ref'])
			{
				$q = $db->query("SELECT `lastip` FROM `users` WHERE `userid` = {$_POST['ref']}");
				//If referring does not exist.
				if ($db->num_rows($q) == 0)
				{
					$db->free_result($q);
					alert('danger',$lang['ERROR_NONUSER'],$lang['REG_REFERROR']);
					require("footer.php");
					exit;
				}
				$rem_IP = $db->fetch_single($q);
				$db->free_result($q);
				//If referring user has the same IP as the registering one.
				if ($rem_IP == $_SERVER['REMOTE_ADDR'])
				{
					alert('danger',$lang['ERROR_SECURITY'],$lang['REG_REFMERROR']);
					require("footer.php");
					exit;
				}
			}
			$encpsw = encode_password($base_pw);	//Encode the password.
			$e_encpsw = $db->escape($encpsw);
			$profilepic="https://www.gravatar.com/avatar/" . md5(strtolower(trim($e_email))) . "?s=250.jpg";
			$CurrentTime=time();
			$db->query("INSERT INTO `users`
						(`username`,`email`,`password`,`level`,`gender`,`class`,
						`lastip`,`registerip`,`registertime`,`loginip`,`display_pic`)
						VALUES ('{$e_username}','{$e_email}','{$e_encpsw}','1','{$e_gender}',
						'{$e_class}','{$IP}','{$IP}','{$CurrentTime}', '127.0.0.1', 
						'{$profilepic}')");
			$i = $db->insert_id();
			$db->query("UPDATE `users` SET `brave`='10',`maxbrave`='10',`hp`='100',
						`maxhp`='100',`maxwill`='100',`will`='100',`energy`='24',
						`maxenergy`='24' WHERE `userid`={$i}");
			if ($e_class == 'Warrior')
			{
				$db->query(
						"INSERT INTO `userstats`
						 VALUES({$i}, 1100, 1000, 900, 1000, 1000)");
			}
			if ($e_class == 'Rogue')
			{
				$db->query(
						"INSERT INTO `userstats`
						 VALUES({$i}, 900, 1100, 1000, 1000, 1000)");
			}
			if ($e_class == 'Defender')
			{
				$db->query(
						"INSERT INTO `userstats`
						 VALUES({$i}, 1000, 900, 1100, 1000, 1000)");
			}
			if ($_POST['ref'])
			{
				$db->query("UPDATE `users` SET `secondary_currency` = `secondary_currency` + {$set['ReferalKickback']} WHERE `userid` = {$_POST['ref']}");
				notification_add($_POST['ref'], "For refering $username to the game, you have earned {$set['ReferalKickback']} valuable Secondary Currency(s)!");
				$e_rip = $db->escape($rem_IP);
				$db->query("INSERT INTO `referals`
				VALUES (NULL, {$_POST['ref']}, '{$e_rip}', {$i}, '{$IP}',{$CurrentTime})");
			}
			$db->query("INSERT INTO `infirmary` 
				(`infirmary_user`, `infirmary_reason`, `infirmary_in`, `infirmary_out`) 
				VALUES ('{$i}', 'N/A', '0', '0');");
			$db->query("INSERT INTO `dungeon` 
				(`dungeon_user`, `dungeon_reason`, `dungeon_in`, `dungeon_out`) 
				VALUES ('{$i}', 'N/A', '0', '0');");
			session_regenerate_id();
			$_SESSION['loggedin'] = 1;
			$_SESSION['userid'] = $i;
			$api->SystemLogsAdd($_SESSION['userid'],'login',"Successfully logged in.");
			$db->query("UPDATE `users` SET `loginip` = '$IP', `last_login` = '{$CurrentTime}', `laston` = '{$CurrentTime}' WHERE `userid` = {$i}");
			//User registered, lets log them in.
			alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['REG_SUCCESS']} <a href='loggedin.php'>{$lang['LOGIN_SIGNIN']}</a>",false);
		}
		require('footer.php');
	}
	else
	{
	echo "
<div class='row'>
	<div class='col-lg-12 text-center'>
		<h3>{$set['WebsiteName']} {$lang['REG_FORM']}</h3>
		<div id='usernameresult'></div>
		<div id='cpasswordresult'></div>
		<div id='emailresult'></div>
		<div id='teamresult'></div>
		<table class='table table-bordered'>
			<form method='post'>
				<tr>
					<th>
						{$lang['REG_USERNAME']}
					</th>
					<td>
						<div id='unerror'>
							<input type='text' class='form-control' id='username' name='username' minlength='3' maxlength='20' placeholder='{$lang['REG_UNPLACE']}' onkeyup='CheckUsername(this.value);' required>
						</div>
					</td>
				</tr>
				<tr>
					<th>
						{$lang['REG_EMAIL']}
					</th>
					<td>
						<div id='emerror'>
							<input type='email' class='form-control' id='email' name='email' minlength='3' maxlength='256' placeholder='{$lang['REG_EPLACE']}' onkeyup='CheckEmail(this.value);' required>
						</div>
					</td>
				</tr>
				<tr>
					<th>
						{$lang['REG_PW']}
					</th>
					<td>
						<div id='pwerror'>
							<input type='password' class='form-control' id='password' name='password' minlength='3' maxlength='256' placeholder='{$lang['REG_PWPLACE']}' onkeyup='CheckPasswords(this.value);PasswordMatch();' required>
						</div>
						<div id='passwordresult'></div>
					</td>
					</tr>
					<tr>
						<th>
							{$lang['REG_CPW']}
						</th>
						<td>
							<div id='cpwerror'>
								<input type='password' class='form-control' id='cpassword' name='cpassword' minlength='3' maxlength='256' placeholder='{$lang['REG_PW1PLACE']}' onkeyup='PasswordMatch();' required>
							</div>
						</td>
					</tr>
					<tr>
						<th>
							{$lang['REG_SEX']}
						</th>
						<td>
							<select name='gender' class='form-control' type='dropdown'>
								<option value='Male'>{$lang['SCU_SEX']}</option>
								<option value='Female'>{$lang['SCU_SEX1']}</option>
							</select>
						</td>
					</tr>
					<tr>
						<th>
							{$lang['REG_CLASS']}
						</th>
						<td>
							<select name='class' id='class' class='form-control' onchange='OutputTeam(this)' type='dropdown'>
								<option></option>
								<option value='Warrior'>{$lang['SCU_CLASS']}</option>
								<option value='Rogue'>{$lang['SCU_CLASS1']}</option>
								<option value='Defender'>{$lang['SCU_CLASS2']}</option>
							</select>
						</td>
					</tr>
					<tr>
						<th>
							{$lang['REG_REFID']}
						</th>
						<td>
							<input type='number' value='{$_GET['REF']}' class='form-control' id='ref' name='ref' min='0' placeholder='{$lang['REG_REFPLACE']}'>
						</td>
					</tr>
					<tr>
						<th>
							{$lang['REG_PROMO']}
						</th>
						<td>
							<input type='text' class='form-control' id='promo' name='promo' placeholder='{$lang['REG_PROMOPLACE']}'>
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<input type='submit' class='btn btn-default' value='{$lang["LOGIN_REGISTER"]}' />
						</td>
					</tr>
				</form>
			</table>
		&gt; <a href='login.php'>{$lang["LOGIN_LOGIN"]}</a>";
	}
require("footer.php");