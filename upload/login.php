<?php
/*
	File:		login.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Main page when a user is not authenticated.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
	MIT License

	Copyright (c) 2019 TheMasterGeneral

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
*/
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
$domain = getGameURL();
$csrf = getHtmlCSRF('login');
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
    <div class='col-sm-8'>
        <div class='card'>
            <div class='card-header bg-dark text-white'>
                Powered by FOSS!
            </div>
            <div class='card-body'>
                This game runs on <a href='https://github.com/MasterGeneral156/chivalry-engine/tree/v2'>Chivalry Engine 
                version {$set['Version_Number']}</a>, created by <a href='https://twitter.com/DaMG156'>MasterGeneral156
                </a>.
            </div>
        </div>
    </div>
</div>";
$h->endpage();