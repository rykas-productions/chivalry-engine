<?php
/*
	File:		login.php
	Created: 	4/5/2016 at 12:17AM Eastern Time
	Info: 		The main page when not logged in.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
if ((!file_exists('./installer.lock')) && (file_exists('installer.php'))) {
    header("Location: installer.php");
    die();
}
require("globals_nonauth.php");
require('lib/bbcode_engine.php');
if ((isset($_COOKIE['uuid'])) && (isset($_COOKIE['token'])))
{
    $uuid = (isset($_COOKIE['uuid']) && is_numeric($_COOKIE['uuid'])) ? abs($_COOKIE['uuid']) : 0;
    $token = $db->escape(strip_tags(stripslashes($_COOKIE['token'])));
    $fetch=$db->query("/*qc=on*/SELECT * FROM `auto_login` WHERE `UUID` = '{$uuid}' AND `Token` = '{$token}'");
    if ($db->num_rows($fetch) > 0)
    {
        $rm=$db->fetch_row($fetch);
        session_regenerate_id();
        $_SESSION['userid'] = $rm['userid'];
        $uade=$db->query("/*qc=on*/SELECT * FROM `user_settings` WHERE `userid` = {$rm['userid']}");
        $uadr=$db->fetch_row($uade);
        if ($uadr['2fa_on'] == 1)
        {
            $encpsw = encode_password($raw_password,$rm['user_level']);
            $e_encpsw = $db->escape($encpsw);
            //Update user's password as an extra security mesaure.
            $db->query("UPDATE `users` SET `password` = '{$e_encpsw}' WHERE `userid` = {$_SESSION['userid']}");
            header("Location: authenication_2f.php");
            exit;
        }
        $_SESSION['loggedin'] = 1;
        $_SESSION['last_login'] = time();
        setcookie('login_expire', time() + 604800, time() + 604800);
        $IP = $db->escape($_SERVER['REMOTE_ADDR']);
        $invis=$db->fetch_single($db->query("/*qc=on*/SELECT `invis` FROM `user_settings` WHERE `userid` = {$rm['userid']}"));
        if ($invis < time())
        {
            $db->query("UPDATE `users`
                  SET `loginip` = '{$IP}',
                  `last_login` = '{$_SESSION['last_login']}',
                  `laston` = '{$_SESSION['last_login']}'
                   WHERE `userid` = {$rm['userid']}");
        }
        else
        {
            $db->query("UPDATE `users`
                  SET `loginip` = '{$IP}'
                   WHERE `userid` = {$rm['userid']}");
        }
        //Generate new auto-login key
        $nuuid=Random();
        $ntoken=hash('sha512',randomizer());
        $db->query("INSERT INTO `auto_login` (`UUID`, `Token`, `userid`) VALUES ('{$nuuid}', '{$ntoken}', '{$rm['userid']}')");
        setcookie('uuid',$nuuid);
        setcookie('token',$ntoken);
        //Remove login attempts for this account.
        $db->query("DELETE FROM `login_attempts` WHERE `userid` = {$_SESSION['userid']}");
        $loggedin_url = 'explore.php';
        //Log that the user logged in successfully.
        $api->SystemLogsAdd($_SESSION['userid'], 'login', "Successfully logged in.");
        //Delete password recovery attempts from DB if they exist for this user.
        $db->query("DELETE FROM `pw_recovery` WHERE `pwr_email` = '{$form_email}'");
        header("Location: {$loggedin_url}");
        $db->query("DELETE FROM `auto_login` WHERE `Token` = '{$token}' AND `UUID` = '{$uuid}'");
        exit;
    }
    else
    {
        setcookie('uuid','');
        setcookie('token','');
        $db->query("DELETE FROM `auto_login` WHERE `UUID` = '{$uuid}'");
        $db->query("DELETE FROM `auto_login` WHERE `Token` = '{$token}'");
    }
}
$currentpage = $_SERVER['REQUEST_URI'];
$cpage = strip_tags(stripslashes($currentpage));
$domain = determine_game_urlbase();
echo "<div class='jumbotron'>
        <div class='container'>
            <h1>
                {$set['WebsiteName']}
            </h1>
            <p>
                {$set['Website_Description']}</p>
            <p>
                <a class='btn btn-primary btn-lg' href='register.php' role='button'>
                    Register &raquo;
                </a>
            </p>
        </div>
    </div>";
$AnnouncementQuery = $db->query("/*qc=on*/SELECT `ann_text`,`ann_time` FROM `announcements` ORDER BY `ann_time` desc LIMIT 1");
$ANN = $db->fetch_row($AnnouncementQuery);
$ANN['ann_text']=substr($ANN['ann_text'], 0, 500);
$parser->parse($ANN['ann_text']);
echo "
<div class='row'>
    <div class='col-sm-4'>
        <div class='card'>
            <div class='card-header'>
                <i class='fas fa-bullhorn'></i> Latest Announcement
            </div>
            <div class='card-body' align='left'>
                " . $parser->getAsHtml() . "
				<hr />
				Posted: " . DateTime_Parse($ANN['ann_time']) . "
            </div>
        </div>
    </div>
    <div class='col-sm-4'>
        <div class='card'>
            <div class='card-header'>
                <i class='game-icon game-icon-podium'></i> Top 10 Players
            </div>
            <div class='card-body' align='left'>";
$Rank = 0;
$RankPlayerQuery =
    $db->query("/*qc=on*/SELECT u.`userid`, `level`, `username`,
                `strength`, `agility`, `guard`, `labor`, `IQ`
                FROM `users` AS `u`
                INNER JOIN `userstats` AS `us`
                 ON `u`.`userid` = `us`.`userid`
                WHERE `u`.`user_level` != 'Admin' AND `u`.`user_level` != 'NPC'
                ORDER BY (`strength` + `agility` + `guard` + `labor` + `IQ`)
                DESC, `u`.`userid` ASC
                LIMIT 10");
while ($pdata = $db->fetch_row($RankPlayerQuery)) {
    $Rank = $Rank + 1;
    echo "{$Rank}) {$pdata['username']} [{$pdata['userid']}] (Level {$pdata['level']})<br />";
}
echo "</div>
        </div>
    </div>
    <div class='col-sm-4'>
        <div class='card' align='center'>
            <div class='card-header'>
                <i class='game-icon game-icon-minions'></i> Random Player Showcase
            </div>";
			$cutoff = time() - 86400;
			$uq=$db->query("/*qc=on*/SELECT * FROM `users` WHERE `userid` = {$set['random_player_showcase']}");
			$ur=$db->fetch_row($uq);
			$displaypic = ($ur['display_pic']) ? "<img src='" . parseImage(parseDisplayPic($ur['userid'])) . "' class='img-thumbnail img-fluid' width='80' alt='{$ur['username']}&#39;s display picture' title='{$r['username']}&#39;s display picture'>" : '';
            echo "<center>{$displaypic}</center><br />
				{$ur['username']} [{$ur['userid']}]<br />
				Level: {$ur['level']}<br />";
				echo ($ur['guild']) ? "Guild: {$api->GuildFetchInfo($ur['guild'],'guild_name')}<br />" : '';
			echo"</div>
        </div>
    </div>
</div>
<br />";
?>
<hr />
<div class="row featurette">
          <div class="col-md-7">
            <h2 class="featurette-heading">The Story</h2>
            <p class="lead">You are a young warrior from the town of Cornrye. You must travel to different towns and meet new people. Will you protect the weak, or destroy them? The choice is yours, but remember, there's always someone waiting for you to slip up and put your guard down.</p>
          </div>
          <div class="col-md-5">
            <img class="featurette-image img-fluid mx-auto" src="https://res.cloudinary.com/dydidizue/image/upload/v1520819397/horse-stable-travel.jpg" alt="Travel agent photo.">
          </div>
        </div>

        <hr class="featurette-divider">

        <div class="row featurette">
          <div class="col-md-7 order-md-2">
            <h2 class="featurette-heading">Making Wealth</span></h2>
            <p class="lead">How will you make wealth? Living and working honorably, or by committing crimes and robbing other citizens? You sculpt your own destiny.</p>
          </div>
          <div class="col-md-5 order-md-1">
            <img class="featurette-image img-fluid mx-auto" src="https://res.cloudinary.com/dydidizue/image/upload/v1520819462/shop.jpg" alt="Trading">
          </div>
        </div>
        <hr class="featurette-divider">
        <div class="row featurette">
          <div class="col-md-7">
            <h2 class="featurette-heading">Become a Legendary Warrior</h2>
            <p class="lead">You will need to travel to many different places outside of your hometown of Cornrye. Will you form a guild to protect the weak, or to extort them? Join now to test whether your gameplay is as sharp as your blade.</p>
          </div>
          <div class="col-md-5">
            <img class="featurette-image img-fluid mx-auto" src="https://res.cloudinary.com/dydidizue/image/upload/v1520819749/logo.png" alt="The Best">
          </div>
        </div>
		<link href="https://cdn.jsdelivr.net/gh/MasterGeneral156/chivalry-is-dead-game-cdn@1.0.6/css/login.css" rel="stylesheet">

<?php
$h->endpage();