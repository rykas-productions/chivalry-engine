<?php
if ((!file_exists('./installer.lock')) && (file_exists('installer.php'))) {
    header("Location: installer.php");
    die();
}
require("globals_nonauth.php");
require('lib/bbcode_engine.php');
$AnnouncementQuery = $db->query("/*qc=on*/SELECT `ann_text`,`ann_time` FROM `announcements` ORDER BY `ann_time` desc LIMIT 1");
$ANN = $db->fetch_row($AnnouncementQuery);
$ANN['ann_text']=substr($ANN['ann_text'], 0, 330);
$parser->parse($ANN['ann_text']);
$last24hr=time()-86400;
$totalplayers=$db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users`"));
$playersonline=$db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `laston` > {$last24hr}"));
$signups=$db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `registertime` > {$last24hr}"));
$currentpage = $_SERVER['REQUEST_URI'];
$cpage = strip_tags(stripslashes($currentpage));
$domain = determine_game_urlbase();
$csrf = request_csrf_html('login');
echo "
<div class='row'>
    <div class='col-md-6 col-lg-5 col-xxl-3'>
        <div class='card'>
            <div class='card-header'>
                <div class='row'>
                <div class='col'>
                        Sign In
                    </div>
                <div class='col'>
                     <a href='pwreset.php'>Forgot Password?</a>
                    </div>
                </div>
            </div>
            <div class='card-body'>
                <form method='post' action='authenticate.php'>
                    {$csrf}
                    <input type='email' name='email' class='form-control' required='true' placeholder='Your email address'><br />
                    <input type='password' name='password' class='form-control' required='true' placeholder='Your password'><br />
                    <input type='submit' class='btn btn-primary btn-block' value='Sign In'><br />
                    New here? <a href='register.php'>Sign up</a> for an account!
                </form>";
				loginbutton("rectangle");
            echo "</div>
        </div>
        <br />
    </div>
    <div class='col-md-6 col-lg-7 col-xl-7 col-xxl-4'>
        <div class='card'>
            <div class='card-header'>
            Warrior with no empathy
            </div>
            <div class='card-body'>
                {$set['Website_Description']}
            </div>
        </div>
        <br />
    </div>
    <div class='col-md-6 col-lg-5 col-xxl-5'>
        <div class='card'>
            <div class='card-header'>
                Highest Ranked Players
            </div>
            <div class='card-body'>";
                $Rank = 0;
                $RankPlayerQuery =
                    $db->query("SELECT u.`userid`, `level`, `username`,
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
                    echo "<div class='row'>
                        <div class='col col-md-2'>
                            {$Rank}
                        </div>
                        <div class='col'>
                            " . parseUsername($pdata['userid']) . " [{$pdata['userid']}]
                        </div>
                        <div class='col col-4'>
                            Level " . number_format($pdata['level']) . "
                        </div>
                    </div>";
                }
                echo"
            </div>
        </div>
        <br />
    </div>
	<div class='col-md-6 col-lg-7 col-xl-7 col-xxl-3'>
        <div class='card'>
            <div class='card-header'>
            Gameplay
            </div>
            <div class='card-body'>
                Players must get stronger in order to defeat those who oppose them. How they get there is entirely up to them!
				Players may commit crimes and become a crimelord, or hit the training grounds to become a well rounded warrior. 
				Estates are available purchase to accomodate yourself growing as the warrior they claim to be. <br />
				Other warriors will attempt you from reaching the number one warrior spot... but are you really going to be stopped?
            </div>
        </div>
        <br />
    </div>
    <div class='col-md-6 col-lg-5 col-xl-5 col-xxl-3'>
        <div class='card'>
            <div class='card-header'>
                No Installation Required!
            </div>
            <div class='card-body'>
                " . number_format($playersonline) . " Players Online Today<br />
                " . number_format($totalplayers) . " Total Players<br />
                " . number_format($signups) . " New Players Today<br />
				Most Users Online: " . number_format($set['mostUsersOn']) . " Users<br />" . DateTime_Parse($set['mostUsersOnTime']) . "
            </div>
        </div>
        <br />
    </div>
	<div class='col-md-6 col-lg-7 col-xl-7 col-xxl-3'>
        <div class='card'>
            <div class='card-header'>
                Latest Announcement
            </div>
            <div class='card-body'>
                " . $parser->getAsHtml() . "<br />
				<small>" . DateTime_Parse($ANN['ann_time']) . "</small>
            </div>
        </div>
        <br />
    </div>";
    $displaypic = "<img src='" . parseDisplayPic($set['random_player_showcase']) . "' class='img-thumbnail' height='75'>";
echo"
    <div class='col-md-6 col-lg-7 col-xl-4 col-xxl-3'>
        <div class='card'>
            <div class='card-header'>
                Player of the Week
            </div>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-12 col-sm-3'>
                        {$displaypic}
                    </div>
                    <div class='col-12 col-sm'>
                        {$api->SystemUserIDtoName($set['random_player_showcase'])} [{$set['random_player_showcase']}]
                        Level: {$api->UserInfoGet($set['random_player_showcase'], "level")}
                    </div>
                </div>
            </div>
        </div>
        <br />
    </div>
</div>";
$h->endpage();