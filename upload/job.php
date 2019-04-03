<?php
/*
	File:		job.php
	Created: 	9/29/2018 at 12:17AM Eastern Time
	Info: 		The main game job page.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
$jobquery = 1;
require('globals.php');
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
	alert('danger',"Uh Oh!","You cannot visit your job while in the infirmary or dungeon.",true,'index.php');
	die($h->endpage());
}
$_GET['interview'] = (isset($_GET['interview']) && is_numeric($_GET['interview'])) ? abs(intval($_GET['interview'])) : 0;
if (empty($ir['job'])) {
    if (empty($_GET['interview'])) {
        echo "It appears you are unemployed. A list of businesses hiring are listed below.<br />";
        $q = $db->query("/*qc=on*/SELECT * FROM `jobs`");
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
        $q = $db->query("/*qc=on*/SELECT `j`.*, `jr`.*
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
        <b>{$ir['username']}:</b> Of course! I'm level {$ir['level']}, have {$ir['strength']} strength, {$ir['iq']} IQ, and {$ir['labor']} labor. I hope these skills are useful to the company.<br />";
        if ($ir['strength'] >= $r['jrSTR'] && $ir['labor'] >= $r['jrLAB'] && $ir['iq'] >= $r['jrIQ']) {
            $db->query("UPDATE `users`
                        SET `job` = {$_GET['interview']},
                        `jobrank` = {$r['jrID']}
                        WHERE `userid` = {$userid}");
            echo "<b>{$r['jBOSS']}:</b> It appears you fit our basic requirements. Is starting at {$r['jrPRIMPAY']} Copper Coins and/or {$r['jrSECONDARY']} Chivalry Tokens per hour fine with you?<br />
            <b>{$ir['username']}:</b> Yes it is!<br />
            <b>{$r['jBOSS']}:</b> Alright, well, get to work then! Welcome aboard!<br />
            <a href='job.php'>Get to Work</a>";
        } else {
            echo "<b>{$r['jBOSS']}:</b> We apologize, {$ir['username']}, but you do not have the necessary requirements needed to join our company. You'll need";
            if ($ir['strength'] < $r['jrSTR']) {
                $s = $r['jrSTR'] - $ir['strength'];
                echo " $s more strength, ";
            }
            if ($ir['labor'] < $r['jrLAB']) {
                $s = $r['jrLAB'] - $ir['labor'];
                echo " $s more labor, ";
            }
            if ($ir['iq'] < $r['jrIQ']) {
                $s = $r['jrIQ'] - $ir['iq'];
                echo " $s more IQ, ";
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
	$maxpayprim=($ir['jrPRIMPAY']*0.3);
	$maxpaysecc=($ir['jrSECONDARY']*0.3);
    echo "<h3><i class='game-icon game-icon-push'></i> Your Job</h3>
    You currently work in the {$ir['jNAME']}! You receive will " . number_format($ir['jrPRIMPAY']) . " Copper Coins and/or 
    " . number_format($ir['jrSECONDARY']) . " Chivalry Tokens each hour 
	you work. You will only be paid if its Monday through Friday, between 9am and 5pm gametime.
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
    $q = $db->query("/*qc=on*/SELECT * FROM `job_ranks` WHERE `jrJOB` = {$ir['job']} ORDER BY (`jrPRIMPAY` + `jrSECONDARY`) ASC");
    while ($r = $db->fetch_row($q)) {
        echo "
        <tr>
            <td>
                {$r['jrRANK']}
            </td>
            <td>";
				if ($r['jrPRIMPAY'])
					echo number_format($r['jrPRIMPAY']) . " Copper Coins<br />";
				if ($r['jrSECONDARY'])
					echo number_format($r['jrSECONDARY']) . " Chivalry Tokens<br />";
                echo "
            </td>
            <td>
                " . number_format($r['jrSTR']) . " Strength<br />
                " . number_format($r['jrLAB']) . " Labor<br />
                " . number_format($r['jrIQ']) . " IQ
            </td>
        </tr>";
    }
    echo "</table>
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
    $q = $db->query("/*qc=on*/SELECT *
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