<?php
if ((!file_exists('./installer.lock')) && (file_exists('installer.php'))) {
    header("Location: installer.php");
    die();
}
require("globals_nonauth.php");
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
    <div class='col-sm-4'>
        <div class='card'>
            <div class='card-header bg-dark text-white'>
                Sign In <a href='pwreset.php'>Forgot Password?</a>
            </div>
            <div class='card-body'>
                <form method='post' action='authenticate.php'>
                    {$csrf}
                    <input type='email' name='email' class='form-control' required='true' placeholder='Your email address'><br />
                    <input type='password' name='password' class='form-control' required='true' placeholder='Your password'><br />
                    <input type='submit' class='btn btn-primary' value='Sign In'><br />
                    New here? <a href='register.php'>Sign up</a> for an account!
                </form>
            </div>
        </div>
    </div>
    <div class='col-sm-8'>
        <div class='card'>
            <div class='card-header bg-dark text-white'>
            {$set['WebsiteName']} Info
            </div>
            <div class='card-body'>
                {$set['Website_Description']}
            </div>
        </div>
    </div>
</div>
<div class='row'>
    <div class='col-sm-4'>
        <div class='card'>
            <div class='card-header bg-dark text-white'>
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
                    echo "{$Rank}) {$pdata['username']} [{$pdata['userid']}] (Level {$pdata['level']})<br />";
                }
                echo"
            </div>
        </div>
    </div>
	<div class='col-sm-8'>
        <div class='card'>
            <div class='card-header bg-dark text-white'>
            Gameplay
            </div>
            <div class='card-body'>
                Players must get stronger in order to defeat those who oppose them. How they get there is entirely up to them!
				Players may commit crimes and become a crimelord, or hit the training grounds to become a well rounded warrior. 
				Estates are available purchase to accomodate yourself growing as the warrior they claim to be. <br />
				Other warriors will attempt you from reaching the number one warrior spot... but are you really going to be stopped?
            </div>
        </div>
    </div>
</div>
<div class='row'>
    <div class='col-sm-4'>
        <div class='card'>
            <div class='card-header bg-dark text-white'>
                No Installation Required!
            </div>
            <div class='card-body'>
                Free Registration<br />
                " . number_format($playersonline) . " Players Online Today<br />
                " . number_format($totalplayers) . " Total Players<br />
                " . number_format($signups) . " New Players Today
            </div>
        </div>
    </div>
</div>";
$h->endpage();