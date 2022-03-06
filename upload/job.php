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
if (empty($ir['job'])) 
{
    if (empty($_GET['interview'])) 
    {
        alert('info', "", "It appears you are unemployed. A list of businesses hiring are listed below.", false);
        $q = $db->query("/*qc=on*/SELECT * FROM `jobs`");
        echo "<div class='row'>";
        while ($r = $db->fetch_row($q)) 
        {
            echo "
                    <div class='col-12 col-md-6 col-xl-4 col-xxxl-3'>
                        <div class='card'>
                            <div class='card-header'>
                                {$r['jNAME']}
                            </div>
                            <div class='card-body'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <i>{$r['jDESC']}</i>
                                    </div>
                                    <div class='col-12'>
                                        <b>Boss: {$r['jBOSS']}</b>
                                    </div>
                                    <div class='col-12'>
                                        <a href='?interview={$r['jRANK']}' class='btn btn-block btn-primary'>Attend Interview</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br />
                    </div>";
        }
        echo "</div>";
        $db->free_result($q);
    } 
    else 
    {
        $q = $db->query("/*qc=on*/SELECT `j`.*, `jr`.*
                        FROM `jobs` AS `j`
                        INNER JOIN `job_ranks` AS `jr`
                        ON `j`.`jSTART` = `jr`.`jrID`
                        WHERE `j`.`jRANK` = {$_GET['interview']}");
        if ($db->num_rows($q) == 0) 
        {
            $db->free_result($q);
            alert("danger", "Uh Oh!", "You are trying to attempt the interview for a job that isn't hiring.", true, 'job.php');
            die($h->endpage());
        }
        $r = $db->fetch_row($q);
        $db->free_result($q);
        alert('secondary', $r['jBOSS'], "So, {$ir['username']}, it appears you are wanting to work with our company here at {$r['jNAME']}. Can you tell me a little bit about yourself?", false);
        alert('primary', $ir['username'], "Of course! I am Level " . shortNumberParse($ir['level']) . ", I have " . shortNumberParse($ir['iq']) . " IQ and my Labor is " . shortNumberParse($ir['labor']) . ".", false);
        if ($ir['strength'] >= $r['jrSTR'] && $ir['labor'] >= $r['jrLAB'] && $ir['iq'] >= $r['jrIQ']) 
        {
            $db->query("UPDATE `users`
                        SET `job` = {$_GET['interview']},
                        `jobrank` = {$r['jrID']}
                        WHERE `userid` = {$userid}");
            alert('success',$r['jBOSS'], "You meet our basic requirements. I'll start you off at " . shortNumberParse($r['jrPRIMPAY']) . " Copper Coins and " . shortNumberParse($r['jrSECONDARY']) . " Chivalry Tokens per hour. Do have further questions for me?", false);
            alert('primary', $ir['username'], "No, I do not. Thank you!", false);
            alert('secondary', $r['jBOSS'], "Alright, well, get to work! Welcome aboard!", false);
            echo "<a href='job.php' class='btn btn-block btn-primary'>Get to Work</a>";
        } 
        else 
        {
            $err = "";
            if ($ir['strength'] < $r['jrSTR']) {
                $s = shortNumberParse($r['jrSTR'] - $ir['strength']);
                $err .= " $s more strength, ";
            }
            if ($ir['labor'] < $r['jrLAB']) {
                $s = shortNumberParse($r['jrLAB'] - $ir['labor']);
                $err .= " $s more labor, ";
            }
            if ($ir['iq'] < $r['jrIQ']) {
                $s = shortNumberParse($r['jrIQ'] - $ir['iq']);
                $err .= " $s more IQ, ";
            }
            alert('danger',$r['jBOSS'], "I'm sorry, but you don't even meet our basic requirements. You need {$err} before you can work with us.", false);
            echo "
			<a href='index.php' class='btn btn-danger btn-block'>Go Home, Crying</a>";
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
    echo "<h3><i class='game-icon game-icon-push'></i> Your Job</h3>";
    alert("info","","You currently work as a {$ir['jrRANK']} at the {$ir['jNAME']}! You receive will " . shortNumberParse($ir['jrPRIMPAY']) . " Copper Coins and/or 
    " . shortNumberParse($ir['jrSECONDARY']) . " Chivalry Tokens each hour 
	you work. You will only be paid between 8AM and 6PM gametime. <b>You will gain a 30% bonus if you're online each hour.</b>", false);
    $q = $db->query("/*qc=on*/SELECT * FROM `job_ranks` WHERE `jrJOB` = {$ir['job']} ORDER BY (`jrPRIMPAY` + `jrSECONDARY`) ASC");
    echo "<div class='row'>
            <div class='col-12'>
                <div class='card'>
                    <div class='card-body'>";
    while ($r = $db->fetch_row($q)) 
    {
        $hasStr = ($ir['strength'] >= $r['jrSTR']) ? "text-success" : "text-danger";
        $hasLab = ($ir['labor'] >= $r['jrLAB']) ? "text-success" : "text-danger";
        $hasIq = ($ir['iq'] >= $r['jrIQ']) ? "text-success" : "text-danger";
        $currentJob = ($ir['jobrank'] == $r['jrID']) ? "font-weight-bold" : "";
        echo "<div class='row'>
                    <div class='col-12 col-md'>
                        <div class='row'>
                            <div class='col-12'>
                                <b><small>Rank</small></b>
                            </div>
                            <div class='col-12'>
                                <span class='{$currentJob}'>{$r['jrRANK']}</span>
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm-6 col-md'>
                        <div class='row'>
                            <div class='col-12'>
                                <b><small>Wage</small></b>
                            </div>
                            <div class='col-12'>";
                                if ($r['jrPRIMPAY'])
                                    echo shortNumberParse($r['jrPRIMPAY']) . " Copper Coins<br />";
                                if ($r['jrSECONDARY'])
                                    echo shortNumberParse($r['jrSECONDARY']) . " Chivalry Tokens<br />";
                                echo "
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm-6 col-md'>
                        <div class='row'>
                            <div class='col-12'>
                                <b><small>Requirements</small></b>
                            </div>
                            <div class='col-12'>
                                <span class='{$hasStr}'>" . shortNumberParse($r['jrSTR']) . " Strength<br /></span>
                                <span class='{$hasLab}'>" . shortNumberParse($r['jrLAB']) . " Labor<br /></span>
                                <span class='{$hasIq}'>" . shortNumberParse($r['jrIQ']) . " IQ</span>
                            </div>
                        </div>
                    </div>
                    <div class='col-12'>
                        <hr />
                    </div>
              </div>";
    }
    echo "</div></div></div></div><br />
    <div class='row'>
        <div class='col-12 col-md'>
            <a href='?action=promote' class='btn btn-block btn-primary'>Try To Get Promoted</a>
        </div>
        <div class='col-12 col-md'>
            <a href='?action=quit' class='btn btn-block btn-danger'>Quit Job</a>
        </div>
    </div>";
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