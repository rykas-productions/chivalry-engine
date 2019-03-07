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
                Top 10 Players
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
</div>";
$h->endpage();