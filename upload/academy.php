<?php
/*
	File:		academy.php
	Created: 	4/4/2016 at 11:49PM Eastern Time
	Info: 		The academy, which players can use to take courses and
				increase their stats for currency and waiting.
	Author:		ImJustIsabella
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
	alert('danger',"Uh Oh!","You cannot visit the academy when you're in the infirmary or dungeon.",true,'explore.php');
	die($h->endpage());
}
echo "<h4><i class='game-icon game-icon-diploma'></i> Local Academy</h4><hr>";
if ($ir['course'] > 0)  //User is enrolled in a course, so lets tell them and stop them
    //And stop them from taking another.
{
	$cd =
        $db->query(
            "/*qc=on*/SELECT `ac_name`, `ac_days`, `ac_cost`
    				 FROM `academy`
    				 WHERE `ac_id` = {$ir['course']}");
    $coud = $db->fetch_row($cd);
    $db->free_result($cd);
	$daystoseconds=$coud['ac_days']*86400;
	if (getSkillLevel($userid,18))
	{
	    $iq=round($ir['iq']/5000);
	    if ($iq > 15)
	        $iq=15;
        $iq=$iq/100;
        $daystoseconds=$daystoseconds*(1-$iq);
	}
	$actualReset = $ir['reset'] - 1;
	if ($actualReset > 0)
	{
	    $daystoseconds = $daystoseconds - ($daystoseconds * ($actualReset * 0.08));
	}
	$starttime=time()-($ir['course_complete']-$daystoseconds);
	$percentcomplete=round(($starttime/$daystoseconds)*100);
	if (isset($_GET['dropout']))
	{
		if ($percentcomplete <= 33)
		{
			addToEconomyLog('Academy', 'copper', $coud['ac_cost']);
			$db->query("UPDATE `users` 
						SET `primary_currency` = `primary_currency` + {$coud['ac_cost']}, 
						`course` = 0, 
						`course_complete` = 0 
						WHERE `userid` = {$userid}");
			alert("success","Success!","You have successfully dropped out of your course. You have been refunded " . shortNumberParse($coud['ac_cost']) . " Copper Coins.",true,'academy.php');
			die($h->endpage());
		}
		elseif (($percentcomplete > 33) && ($percentcomplete <= 66))
		{
			$db->query("UPDATE `users` 
						SET `course` = 0, 
						`course_complete` = 0 
						WHERE `userid` = {$userid}");
			alert("success","Success!","You have successfully dropped out of your course.",true,'academy.php');
			die($h->endpage());
		}
		else
		{
			alert("danger","Uh Oh!","You are too far into your course to dropout now.",true,'academy.php');
		}
	}
	echo "<div class='row'>
        <div class='col-12'>
            <div class='card'>
                <div class='card-header'>
                    You are currently enrolled in the {$coud['ac_name']} course.
                </div>
                <div class='card-body'>
                    The course will be completed in " . TimeUntil_Parse($ir['course_complete']) . ".<br />
                    You may dropout of this course. Please note that you can only dropout if you've completed 
                    less than 66% of the course. If you dropout before 33% completion, you will be refunded all 
                    your cash. Otherwise, you will lose all the Copper Coins you used to enroll.<br />
                    <div class='progress' style='height: 1rem;'>
				        <div class='progress-bar bg-success progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$percentcomplete}%' style='width:{$percentcomplete}%' aria-valuemin='0' aria-valuemax='100'>
        					<span>
        						{$percentcomplete}% Completed
        					</span>
				        </div>
			         </div>
                    Do you wish to <a href='?dropout=yes'>dropout</a>?
                </div>
            </div>
        </div>
    </div>";
    die($h->endpage());
}
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "menu":
        menu();
        break;
    case "start":
        start();
        break;
    default:
        header("Location: ?action=menu");
        break;
}

function menu()
{
    global $db, $userid, $ir;
    //Select the courses from in-game.
    $acadq = $db->query("/*qc=on*/SELECT * FROM `academy` ORDER BY `ac_level` ASC, `ac_cost` ASC");
    while ($academy = $db->fetch_row($acadq)) {
        $cdo = $db->query("/*qc=on*/SELECT COUNT(`userid`)
                             FROM `academy_done`
                             WHERE `userid` = {$userid}
                             AND `course` = {$academy['ac_id']}");
		$graduates = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`)
                             FROM `academy_done`
                             WHERE `course` = {$academy['ac_id']}"));
        //If user has already completed the course.
        if ($db->fetch_single($cdo) > 0)
            $do = "<a href='#' class='disabled btn-success btn btn-block'>Graduated</a>";
        elseif ($ir['level'] < $academy['ac_level'])
            $do = "<a href='#' class='disabled btn-danger btn btn-block'>Level too low</a>";
		elseif ($ir['primary_currency'] < $academy['ac_cost'])
			$do = "<a href='#' class='disabled btn-danger btn btn-block'>Not enough Copper</a>";
		else
            $do = "<a href='?action=start&id={$academy['ac_id']}' class='btn btn-primary btn-block'>Start Course</a>";
		echo "
        <div class='row'>
            <div class='col-12'>
                <div class='card'>
                    <div class='card-body'>
                        <div class='row'>
                            <div class='col-12 col-lg-8 col-xl-9'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <b>{$academy['ac_name']}</b>
                                    </div>
                                    <div class='col-12'>
                                        <small><i>{$academy['ac_desc']}</i></small>
                                    </div>
                                    <div class='col-6 col-md-3 col-lg-6 col-xl-3'>
                                        <small><i>Graduates " . number_format($graduates) . "</i></small>
                                    </div>
                                    <div class='col-6 col-md-3 col-lg-6 col-xl-4'>
                                        <small><i>Course Length " . number_format($academy['ac_days']) . " Days</i></small>
                                    </div>
                                    <div class='col col-md-4 col-lg'>
                                        <small><i>Cost " . shortNumberParse($academy['ac_cost']) . " Copper Coins</i></small>
                                    </div>";
                                    if (!empty($academy['ac_level']))
                                    {
                                        echo "
                                        <div class='col-4 col-sm-6 col-md-2 col-lg-4 col-xxl-2'>
                                            <small><i>Level " . number_format($academy['ac_level']) . "</i></small>
                                        </div>";
                                    }
                                echo"
                                </div>
                            </div>
                            <div class='col-12 col-lg-4 col-xl-3'>
                                {$do}
                            </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>";
    }
}

function start()
{
    global $db, $userid, $ir, $h, $api;
    $_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(intval($_GET['id'])) : 0;
    //If the user doesn't specific a course to take.
    if (empty($_GET['id'])) {
        alert('danger', "Uh Oh!", "You didn't select a valid course to take.", true, 'academy.php');
        die($h->endpage());
    }
    $courq = $db->query("/*qc=on*/SELECT * FROM `academy` WHERE `ac_id` = {$_GET['id']} LIMIT 1");
    //If the course specified does not exist.
    if ($db->num_rows($courq) == 0) {
        alert('danger', "Uh Oh!", "The course you chose does not exist. Check your source and try again.", true, 'academy.php');
        die($h->endpage());
    }
    $course = $db->fetch_row($courq);
    //If the user's level is lower than the course requirement.
    if ($course['ac_level'] > $ir['level']) {
        alert('danger', "Uh Oh!", "Your level is too low to take this course. Come back when you are level
		                        {$course['ac_level']} or above.", true, 'academy.php');
        die($h->endpage());
    }
    //If the user doesn't have enough Copper Coins for this course.
    if ($course['ac_cost'] > $ir['primary_currency']) {
        alert('danger', "Uh Oh!", "You do not have enough Copper Coins to take this course. You need " . number_format($course['ac_cost']) . " Copper Coins 
                                yet you only have " . number_format($ir['primary_currency']) . " Copper Coins.", true, 'academy.php');
        die($h->endpage());
    }
    $cdo = $db->query("/*qc=on*/SELECT COUNT(`userid`)
                             FROM `academy_done`
                             WHERE `userid` = {$userid}
                             AND `course` = {$_GET['id']}");
    //If the user has already taken this course.
    if ($db->fetch_single($cdo) > 0) {
        alert('danger', "Uh Oh!", "You have already graduated from this course. No need to enroll again.", true, 'academy.php');
        die($h->endpage());
    }
	$timestamp=$course['ac_days'] * 86400;
	if (getSkillLevel($userid,18))
	{
		$iq=round($ir['iq']/5000);
		if ($iq > 15)
			$iq=15;
		$iq=$iq/100;
		$timestamp=$timestamp*(1-$iq);
	}
	$actualReset = $ir['reset'] - 1;
	if ($actualReset > 0)
	{
	    $timestamp = $timestamp - ($timestamp * ($actualReset * 0.08));
	}
    $completed = time() + ($timestamp); //Current Time + (Academy days * seconds in a day)
    $db->query("UPDATE `users` SET `course` = {$_GET['id']},
                `course_complete` = {$completed} 
                WHERE `userid` = {$userid}");
    //Update user's course, and course completion time.
    $api->UserTakeCurrency($userid, 'primary', $course['ac_cost']); //Take user's money.
	addToEconomyLog('Academy', 'copper', ($course['ac_cost'])*-1);
    alert('success', "Success!", "You have successfully enrolled yourself in the {$course['ac_name']} course. It will
	                            complete in {$course['ac_days']} days.", true, 'index.php');
}

$h->endpage();