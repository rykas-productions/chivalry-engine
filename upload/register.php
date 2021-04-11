<?php
/*
	File:		register.php
	Created: 	4/5/2016 at 12:24AM Eastern Time
	Info: 		The registration form.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals_nonauth.php");
$IP = $db->escape($_SERVER['REMOTE_ADDR']);
//Check if someone is already registered on this IP.
/*if ($db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `lastip` = '{$IP}' OR `loginip` = '{$IP}' OR `registerip` = '{$IP}'")) >= 1) {
    alert('danger', "Uh Oh!", "You can only have one account per IP Address. We're going to stop you from registering for now.", true, 'login.php');
    die($h->endpage());

}*/
if (!isset($_GET['REF'])) {
    $_GET['REF'] = 0;
}
$_GET['REF'] = abs($_GET['REF']);
if ($_GET['REF']) {
    $_GET['REF'] = $_GET['REF'];
}
$username = (isset($_POST['username']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])*$/i", $_POST['username'])) ? $db->escape(strip_tags(stripslashes($_POST['username']))) : '';
if (!empty($username)) {
    //If the registration captcha is enabled.
    if ($set['RegistrationCaptcha'] == 'ON') {
        //If the user got the captcha wrong.
        if (!$_SESSION['captcha'] || !isset($_POST['captcha']) || $_SESSION['captcha'] != $_POST['captcha']) {
            unset($_SESSION['captcha']);
            alert('danger', "Uh Oh!", "You have failed the captcha.");
            die($h->endpage());

        }
        unset($_SESSION['captcha']);
    }
    //If the email is inputted, and valid.
    if (!isset($_POST['email']) || !valid_email(stripslashes($_POST['email']))) {
        alert('danger', "Uh Oh!", "You input an invalid email address.");
        die($h->endpage());

    }
    //If the username is empty
    if (empty($username)) {
        alert('danger', "Uh Oh!", "You input an invalid or empty username.");
        die($h->endpage());

    }
    //If the username is less than 3 characters and more than 20.
    if (((strlen($username) > 20) OR (strlen($username) < 3))) {
        alert('danger', "Uh Oh!", "Your username can only be 3 through 20 characters in length.");
        die($h->endpage());

    }
    //Check Gender
    if (!isset($_POST['gender']) || ($_POST['gender'] != 'Male' && $_POST['gender'] != 'Female' && $_POST['gender'] != 'Other')) {
        alert('danger', "Uh Oh!", "You are trying to register as an invalid gender.");
        die($h->endpage());

    }
    //Check class
    if (!isset($_POST['class']) || ($_POST['class'] != 'Warrior' && $_POST['class'] != 'Rogue' && $_POST['class'] != 'Guardian')) {
        alert('danger', "Uh Oh!", "You are trying to register as an invalid class.");
        die($h->endpage());

    }
    $e_gender = $db->escape(stripslashes($_POST['gender']));
    $e_class = $db->escape(stripslashes($_POST['class']));
    $e_username = $db->escape($username);
    $e_email = $db->escape(stripslashes($_POST['email']));
    $q = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `username` = '{$e_username}'");
    $q2 = $db->query("/*qc=on*/SELECT COUNT(`userid`)  FROM `users` WHERE `email` = '{$e_email}'");
    $u_check = $db->fetch_single($q);
    $e_check = $db->fetch_single($q2);
    $db->free_result($q);
    $db->free_result($q2);
    $base_pw = (isset($_POST['password']) && is_string($_POST['password'])) ? stripslashes($_POST['password']) : '';
    $check_pw = (isset($_POST['cpassword']) && is_string($_POST['cpassword'])) ? stripslashes($_POST['cpassword']) : '';
    //Username is in use.
    if ($u_check > 0) {
        alert('danger', "Uh Oh!", "The username you've chosen is already in use.");
    } //Email is in use
    else if ($e_check > 0) {
        alert('danger', "Uh Oh!", "The email you've chosen is already in use.");
    } //Both passwords aren't entered
    else if (empty($base_pw) || empty($check_pw)) {
        alert('danger', "Uh Oh!", "You must specify a password and confirm it.");
    } //The entered passwords match.
    else if ($base_pw != $check_pw) {
        alert('danger', "Uh Oh!", "Your entered passwords did not match.");
    } else {
        $_POST['ref'] = (isset($_POST['ref']) && is_numeric($_POST['ref'])) ? abs($_POST['ref']) : '';
        $IP = $db->escape($_SERVER['REMOTE_ADDR']);
        //If the registrating user was referred to the game by someone.
        if ($_POST['ref']) 
		{
            $q = $db->query("/*qc=on*/SELECT `lastip` FROM `users` WHERE `userid` = {$_POST['ref']}");
            //If referring does not exist.
            if ($db->num_rows($q) == 0) 
			{
                $db->free_result($q);
                alert('danger', "Uh Oh!", "The user who referred you does not exist.");
                die($h->endpage());
            }
            $rem_IP = $db->fetch_single($q);
            $db->free_result($q);
            //If referring user has the same IP as the registering one.
            if ($rem_IP == $_SERVER['REMOTE_ADDR']) 
			{
                alert('danger', "Uh Oh!", "You cannot use a referral ID from someone on your IP.");
                die($h->endpage());
            }
        }
        $encpsw = encode_password($base_pw);    //Encode the password.
        $e_encpsw = $db->escape($encpsw);
        $profilepic = "https://gravatar.com/avatar/" . md5(strtolower(trim($e_email))) . "?s=250.jpg";
        $CurrentTime = time();
        $db->query("INSERT INTO `users`
					(`username`,`email`,`password`,`level`,`gender`,`class`,
					`lastip`,`registerip`,`registertime`,`loginip`,`display_pic`,`vip_days`)
					VALUES ('{$e_username}','{$e_email}','{$e_encpsw}','1','{$e_gender}',
					'{$e_class}','{$IP}','{$IP}','{$CurrentTime}', '{$IP}', 
					'{$profilepic}','3')");
        $i = $db->insert_id();
        $db->query("UPDATE `users` SET `brave`='10',`maxbrave`='10',`hp`='100',
					`maxhp`='100',`maxwill`='100',`will`='100',`energy`='24',
					`maxenergy`='24' WHERE `userid`={$i}");
        if ($e_class == 'Warrior') 
		{
			$api->UserGiveItem($i,365,1);
            $db->query("INSERT INTO `userstats` VALUES({$i}, 1500, 1000, 500, 1000, 1000, 100)");
        }
        if ($e_class == 'Rogue') 
		{
			$api->UserGiveItem($i,366,1);
            $db->query("INSERT INTO `userstats` VALUES({$i}, 500, 1500, 1000, 1000, 1000, 100)");
        }
        if ($e_class == 'Guardian') 
		{
			$api->UserGiveItem($i,367,1);
            $db->query("INSERT INTO `userstats` VALUES({$i}, 1000, 500, 1500, 1000, 1000, 100)");
        }
        if ($_POST['ref']) 
		{
            $api->UserGiveItem($_POST['ref'],18,10);
            $api->UserGiveItem($i,18,10);
            $api->UserGiveItem($_POST['ref'],210,1);
            notification_add($_POST['ref'], "Thank you for referring <a href='profile.php?user={$i}'>{$e_username}</a> to {$set['WebsiteName']}! We have given you 10 Chivalry Gym Scrolls as appreciation.");
            $e_rip = $db->escape($rem_IP);
            $db->query("INSERT INTO `referals`
			VALUES (NULL, {$_POST['ref']}, '{$e_rip}', {$i}, '{$IP}',{$CurrentTime})");
			$db->query("UPDATE `user_settings` SET `ref_count` = `ref_count` + 1 WHERE `userid` = {$_POST['ref']}");
			sendRefferalEmail($_POST['ref'],$i,$e_username);
        }
        $db->query("INSERT INTO `infirmary`
			(`infirmary_user`, `infirmary_reason`, `infirmary_in`, `infirmary_out`) 
			VALUES ('{$i}', 'N/A', '0', '0');");
        $db->query("INSERT INTO `dungeon`
			(`dungeon_user`, `dungeon_reason`, `dungeon_in`, `dungeon_out`) 
			VALUES ('{$i}', 'N/A', '0', '0');");
        //Give starter items.
        $api->UserGiveItem($i,6,50);
        $api->UserGiveItem($i,30,50);
        $api->UserGiveItem($i,33,3000);
        $api->UserGiveCurrency($i,'primary',10000);
        $api->UserGiveCurrency($i,'secondary',50);
        $mail="Welcome to Chivalry is Dead, {$e_username}. We hope you stay a while and hang out. To get started,
        check out the Explore page and visit the [url=hexbags.php]Hexbags[/url] under the Gambling tab. Here you will gain many awesome starter items. Should
        your fortune be unkind, your inventory has 50 Dungeon Keys and 50 Linen Wraps to get you out of the Infirmary and Dungeon when needed.";
        session_regenerate_id();
        $_SESSION['loggedin'] = 1;
        $_SESSION['userid'] = $i;
        $_SESSION['last_login'] = time();
        //Promo code reward
        if (isset($_POST['promo'])) {
            $code = (isset($_POST['promo'])) ? $db->escape(strip_tags(stripslashes($_POST['promo']))) : '';
            if (!empty($code)) {
                $promocodereal = $db->query("/*qc=on*/SELECT * FROM `promo_codes` WHERE `promo_code` = '{$code}'");
                if ($db->num_rows($promocodereal) > 0) {
                    $pcrr = $db->fetch_row($promocodereal);
                    item_add($i, $pcrr['promo_item'], 1);
                    $db->query("UPDATE `promo_codes` SET `promo_use` = `promo_use` + 1 WHERE `promo_code` = '{$code}'");
                    $api->GameAddNotification($i, "Your promotion code was valid! Check your inventory for your {$api->SystemItemIDtoName($pcrr['promo_item'])}!");
                } else {
                    $api->GameAddNotification($i, "Your promotion code was invalid.");
                }
            }
        }
		$db->query("INSERT INTO `user_settings` (`userid`) VALUES ('{$_SESSION['userid']}')");
		$eid = buyEstate($i, 1);
        $randophrase=randomizer();
        $db->query("UPDATE `user_settings` SET `security_key` = '{$randophrase}', `theme` = 7 WHERE `userid` = {$_SESSION['userid']}");
        $api->SystemLogsAdd($_SESSION['userid'], 'login', "Successfully logged in.");
        $db->query("UPDATE `users` 
					SET `loginip` = '$IP', 
					`last_login` = '{$CurrentTime}',
					`laston` = '{$CurrentTime}',
					`estate` = {$eid}
					WHERE `userid` = {$i}");
        //User registered, lets log them in.
        alert('success', "Success!", "You have successfully signed up to play {$set['WebsiteName']}. Click here to <a href='explore.php'>Sign In</a>", false);
        $url=determine_game_urlbase();
        sendRegistrationEmail($e_email);
		$api->GameAddMail($i,"Welcome to Chivalry is Dead",$mail,1);
        die($h->endpage());
    }
    $h->endpage();
} else {
    echo "
    <div class='card'>
    <div class='card-header'>
        {$set['WebsiteName']} Registration Form
    </div>
    <div class='card-body'>
	<form method='post'>
		<div class='row text-left'>
			<div class='col-md-4'>
				<h5>Username</h5>
				<small>This is used to identify yourself around the game.<br />Don't worry, you can change this later.</small>
			</div>
			<div class='col-md-8'>
				<input type='text' class='form-control' id='username' name='username' minlength='3' maxlength='20' placeholder='3-20 characters in length' onkeyup='CheckUsername(this.value);' required>
				<div id='usernameresult' class='invalid-feedback'></div>
			</div>
		</div>
		<hr />
		<div class='row text-left'>
			<div class='col-md-4'>
				<h5>Email</h5>
				<small>Used to sign into the game and receive game updates!<br />
				Your email is used soley for authentication and communications between you and us.</small>
			</div>
			<div class='col-md-8'>
				<input type='email' class='form-control' id='email' name='email' minlength='3' maxlength='256' placeholder='You will use this to sign in' onkeyup='CheckEmail(this.value);' required>
				<div id='emailresult' class='invalid-feedback'></div>
			</div>
		</div>
		<hr />
		<div class='row text-left'>
			<div class='col-md-4'>
				<h5>Password</h5>
				<small>We do our best to keep it safe and secure.<br />
				Make sure you pick something random!</small>
			</div>
			<div class='col-md-8'>
				<input type='password' class='form-control' id='password' name='password' minlength='3' maxlength='256' placeholder='Unique passwords recommended' onkeyup='CheckPasswords(this.value);PasswordMatch();' required>
				<div id='passwordresult'></div>
			</div>
		</div>
		<hr />
		<div class='row text-left'>
			<div class='col-md-4'>
				<h5>Confirm Password</h5>
				<small>Make sure you type it exactly as before.</small>
			</div>
			<div class='col-md-8'>
				<input type='password' class='form-control' id='cpassword' name='cpassword' minlength='3' maxlength='256' placeholder='Confirm password entered previously' onkeyup='PasswordMatch();' required>
				<div id='cpasswordresult'></div>
			</div>
		</div>
		<hr />
		<div class='row text-left'>
			<div class='col-md-4'>
				<h5>Gender</h5>
				<small>What's your player's gender?<br />This can be changed anytime.</small>
			</div>
			<div class='col-md-8'>
				<select name='gender' class='form-control' type='dropdown'>
					<option value='Male'>Male</option>
					<option value='Female'>Female</option>
					<option value='Other'>Other</option>
				</select>
			</div>
		</div>
		<hr />
		<div class='row text-left'>
			<div class='col-md-4'>
				<h5>Fighting Class</h5>
				<small>What best suits your fighting style?</small>
			</div>
			<div class='col-md-8'>
				<select name='class' id='class' class='form-control' onchange='OutputTeam(this)' type='dropdown'>
					<option></option>
					<option value='Warrior'>Brute Force</option>
					<option value='Rogue'>Hit Quicker</option>
					<option value='Guardian'>Damage Tanking</option>
				</select>
				<div id='teamresult'></div>
			</div>
		</div>
		<hr />
		<div class='row text-left'>
			<div class='col-md-4'>
				<h5>Referral Code</h5>
				<small>Did you get a referral code from a friend?</small>
			</div>
			<div class='col-md-8'>
				<input type='number' value='{$_GET['REF']}' class='form-control' id='ref' name='ref' min='0' placeholder='Can be empty. This is a User ID.'>
			</div>
		</div>
		<hr />
		<div class='row text-left'>
			<div class='col-md-4'>
				<h5>Promotional Codes</h5>
				<small>This is optional. Promotional codes give you an extra boost when you sign up.</small>
			</div>
			<div class='col-md-8'>
				<input type='text' class='form-control' id='promo' name='promo' placeholder='Can be empty'>
			</div>
		</div>
		<hr />
		<div class='row align-left text-left'>
		<div class='col-md-9'>";
		alert("dark","","By clicking Register, you accept you have read the <a href='gamerules2.php'>Game Rules</a>
						and our <a href='privacy.php'>Privacy Policy</a>. You also agree that you wish to opt-in to our
						game newsletter. You may opt-out at anytime by checking your in-game settings.",false);
		echo "
		</div>
		<div class='col-md-3 text-center'>
			<input type='submit' class='btn btn-primary btn-block' value='Register' />
		</div>
		</div>
		<hr />
	</form>
	<a href='login.php' class='btn btn-danger btn-block'>Login Page</a>
    </div>
    </div>";
}
$h->endpage();