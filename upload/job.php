<?php
/*
	File:		job.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows the player to work to passively gain riches.
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
$jobquery = 1;
require('globals.php');
$_GET['interview'] = (isset($_GET['interview']) && is_numeric($_GET['interview'])) ? abs(intval($_GET['interview'])) : 0;
if (empty($ir['job'])) {
    if (empty($_GET['interview'])) {
        echo "It appears you are unemployed. A list of businesses hiring are listed below.<br />";
        $q = $db->query("SELECT * FROM `jobs`");
        echo "<table class='table table-bordered'>
        <tr>
            <th>
                Business Name
            </th>
            <th>
                Business Owner
            </th>
            <th>
                Business Description
            </th>
            <th>

            </th>
        </tr>";
        while ($r = $db->fetch_row($q)) {
            echo "<tr>
                <td>
                    {$r['jNAME']}
                </td>
                <td>
                    {$r['jBOSS']}
                </td>
                <td>
                    {$r['jDESC']}
                </td>
                <td>
                    <a href='?interview={$r['jRANK']}'>Attend Interview</a>
                </td>
            </tr>";
        }
        echo "</table>";
        $db->free_result($q);
    } else {
        $q = $db->query("SELECT `j`.*, `jr`.*
                        FROM `jobs` AS `j`
                        INNER JOIN `job_ranks` AS `jr`
                        ON `j`.`jSTART` = `jr`.`jrID`
                        WHERE `j`.`jRANK` = {$_GET['interview']}");
        if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert("danger", "Uh Oh!", "You are trying to attempt the interview for a job that isn't hiring.", true, 'job.php');
            die($h->endpage());
        }
        $r = $db->fetch_row($q);
        $db->free_result($q);
        echo "<b>{$r['jBOSS']}:</b> So, {$ir['username']}, it appears you are wanting to work with our company. Can you tell me a little bit about yourself?<br />
        <b>{$ir['username']}:</b> Of course! I'm level {$ir['level']}, have {$ir['strength']} strength, {$ir['iq']} {$_CONFIG['iq_stat']}, and {$ir['labor']} {$_CONFIG['labor_stat']}. I hope these skills are useful to the company.<br />";
        if ($ir['strength'] >= $r['jrSTR'] && $ir['labor'] >= $r['jrLAB'] && $ir['iq'] >= $r['jrIQ']) {
            $db->query("UPDATE `users`
                        SET `job` = {$_GET['interview']},
                        `jobrank` = {$r['jrID']}
                        WHERE `userid` = {$userid}");
            echo "<b>{$r['jBOSS']}:</b> It appears you fit our basic requirements. Is starting at {$r['jrPRIMPAY']} {$_CONFIG['primary_currency']} and/or {$r['jrSECONDARY']} {$_CONFIG['secondary_currency']} per hour fine with you?<br />
            <b>{$ir['username']}:</b> Yes it is!<br />
            <b>{$r['jBOSS']}:</b> Alright, well, get to work then! Welcome aboard!<br />
            <a href='job.php'>Get to Work</a>";
        } else {
            echo "<b>{$r['jBOSS']}:</b> We apologize, {$ir['username']}, but you do not have the necessary requirements needed to join our company. You'll need";
            if ($ir['strength'] < $r['jrSTR']) {
                $s = $r['jrSTR'] - $ir['strength'];
                echo " $s more {$_CONFIG['strength_stat']}, ";
            }
            if ($ir['labor'] < $r['jrLAB']) {
                $s = $r['jrLAB'] - $ir['labor'];
                echo " $s more labor, ";
            }
            if ($ir['iq'] < $r['jrIQ']) {
                $s = $r['jrIQ'] - $ir['iq'];
                echo " $s more {$_CONFIG['iq_stat']}, ";
            }
            echo "before you'll be able to work here with our company.
			<br />
			&gt; <a href='index.php'>Go Home, Crying</a>";
        }
    }
    $h->endpage();
} else {
    if (!isset($_GET['action'])) {
        $_GET['action'] = '';
    }
    switch ($_GET['action']) {
        case 'quit':
            job_quit();
            break;
        case 'promote':
            job_promote();
            break;
        case 'work':
            job_work();
            break;
        default:
            job_index();
            break;
    }
}
function job_index()
{
    global $db, $ir, $h;
    echo "<h3>Your Job</h3>
    You currently work in the {$ir['jNAME']}! You receive {$ir['jrPRIMPAY']} {$_CONFIG['primary_currency']} and/or
    {$ir['jrSECONDARY']} {$_CONFIG['secondary_currency']} each hour you work as you're required. You've worked {$ir['jobwork']} /
    {$ir['jrACT']} times this hour.
    <table class='table table-bordered'>
    <tr>
        <th>
            Title
        </th>
        <th>
            Hourly Wage
        </th>
        <th>
            Requirements
        </th>
    </tr>";
    $q = $db->query("SELECT * FROM `job_ranks` WHERE `jrJOB` = {$ir['job']} ORDER BY (`jrPRIMPAY` + `jrSECONDARY`) ASC");
    while ($r = $db->fetch_row($q)) {
        echo "
        <tr>
            <td>
                {$r['jrRANK']}
            </td>
            <td>
                {$r['jrPRIMPAY']} Primary<br />
                {$r['jrSECONDARY']} Secondary
            </td>
            <td>
                {$r['jrSTR']} {$_CONFIG['strength_stat']}<br />
                {$r['jrLAB']} Labor<br />
                {$r['jrIQ']} {$_CONFIG['iq_stat']}<br />
                {$r['jrACT']} Work/Hour
            </td>
        </tr>";
    }
    echo "</table>
    &gt; <a href='?action=work'>Begin Work</a>
	<br />
    &gt; <a href='?action=promote'>Try To Get Promoted</a>
	<br />
	&gt; <a href='?action=quit'>Quit Job</a>";
    $h->endpage();
}

function job_quit()
{
    global $db, $h, $userid;
    $db->query("UPDATE `users` SET `job` = 0, `jobrank` = 0, `jobwork` = 0 WHERE `userid` = {$userid}");
    alert('success', "Success!", "You have successfully quit your job.", true, 'job.php');
    $h->endpage();
}

function job_promote()
{
    global $db, $h, $ir, $userid;
    $q = $db->query("SELECT *
                    FROM `job_ranks`
                    WHERE (`jrPRIMPAY` + `jrSECONDARY`) > ({$ir['jrPRIMPAY']} + {$ir['jrSECONDARY']})
                    AND `jrSTR` <= {$ir['strength']}
                    AND `jrLAB` <= {$ir['labor']}
                    AND `jrIQ` <= {$ir['iq']}
                    AND `jrJOB` = {$ir['job']}
                    ORDER BY (`jrPRIMPAY` + `jrSECONDARY`) DESC
                    LIMIT 1");
    if ($db->num_rows($q) == 0) {
        alert("danger", "Uh Oh!", "You cannot be promoted at this time. This might be because you've reached the max rank
        in your current job, or have not yet to meet the requirements of the next level.", true, 'job.php');
    } else {
        $r = $db->fetch_row($q);
        $db->query("UPDATE `users` SET `jobrank` = {$r['jrID']} WHERE `userid` = {$userid}");
        alert("success", "Success!", "Congratulations! You have been promoted to {$r['jrRANK']}!", true, 'job.php');
    }
    $db->free_result($q);
    $h->endpage();
}

function job_work()
{
    global $db, $h, $ir, $userid, $api;
    if (!isset($_GET['dowork'])) {
        if ($ir['jobwork'] >= $ir['jrACT']) {
            alert('danger', "Uh Oh!", "You've already worked the maximum times you are required to this hour. You will not
            gain overtime! Take it easy, bro.", true, 'job.php');
        } else {
            echo "You need to work {$ir['jrACT']} times an hour to get paid. You've only worked {$ir['jobwork']} this hour.
        Each attempt at working will deplete your energy by 10%, and your Will by 5%. Do you wish to work?<br />
        <a class='btn btn-primary' href='?action=work&dowork=1'>Begin Working</a>
        ";
        }
    } else {
        $will = ($api->user->getInfoPercent($userid, 'will', true));
        $energy = ($api->user->getInfoPercent($userid, 'energy', true));
        if ($ir['jobwork'] >= $ir['jrACT']) {
            alert('danger', "Uh Oh!", "You've already worked the maximum times you are required to this hour. You will not
            gain overtime! Take it easy, bro.", true, 'job.php');
            die($h->endpage());
        }
        if ($will < 7) {
            alert("danger", "Uh Oh!", "You need 7% Will to work, you only have {$will}%.", true, '?action=work');
            die($h->endpage());
        }
        if ($energy < 10) {
            alert("danger", "Uh Oh!", "You need 10% Energy to work, you only have {$energy}%.", true, '?action=work');
            die($h->endpage());
        }
        $WorkUnits = randomNumber(0, 2);
        if ($WorkUnits == 0) {
            alert("danger", "Uh Oh!", "You were unable to focus, and thus, you were not able to work as hard as you should have. You have worked {$ir['jobwork']} times.", false);
        } else {
            alert("success", "Success!", "You got to work and knocked out {$WorkUnits} pieces of work. How productive! You have worked {$ir['jobwork']} times.", false);
        }
        $db->query("UPDATE `users` SET `jobwork` = `jobwork` + {$WorkUnits} WHERE `userid` = {$userid}");
        $api->user->setInfoPercent($userid, 'will', -7);
        $api->user->setInfoPercent($userid, 'energy', -10);
        echo "<a class='btn btn-primary' href='?action=work&dowork=1'>Work Again</a><br /><br />
        <a class='btn btn-primary' href='job.php'>Go Back</a>";
    }
    $h->endpage();
}

