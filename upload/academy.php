<?php
/*
	File:		academy.php
	Created: 	6/23/2019 at 6:10PM Eastern Time
	Info: 		The academy, which players can use to take courses and
				increase their stats for currency and waiting.
	Author:		ImJustIsabella
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
require("globals.php");
echo "<h4>Local Academy</h4><hr>";
if ($ir['course'] > 0)  //User is enrolled in a course, so lets tell them and stop them
    //And stop them from taking another.
{
    $cd =
        $db->query(
            "SELECT `ac_name`
    				 FROM `academy`
    				 WHERE `ac_id` = {$ir['course']}");
    $coud = $db->fetch_row($cd);
    $db->free_result($cd);
    echo "You are currently enrolled in the {$coud['ac_name']} course. You will be finished in " . timeUntilParse($ir['course_complete']) . ".";
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
    global $db, $userid, $_CONFIG;
    echo "<div class='cotainer'>
<div class='row'>
		<div class='col-sm'>
		    <h4>Course</h4>
		</div>
		<div class='col-sm'>
		    <h4>Description</h4>
		</div>
		<div class='col-sm'>
		    <h4>Cost</h4>
		</div>
		<div class='col-sm'>
		    <h4>Actions</h4>
		</div>
</div><hr />
	   ";
    //Select the courses from in-game.
    $acadq = $db->query("SELECT * FROM `academy` ORDER BY `ac_level` ASC, `ac_id` ASC");
    while ($academy = $db->fetch_row($acadq)) {
        $cdo = $db->query("SELECT COUNT(`userid`)
                             FROM `academy_done`
                             WHERE `userid` = {$userid}
                             AND `course` = {$academy['ac_id']}");
        //If user has already completed the course.
        if ($db->fetch_single($cdo) > 0) {
            $do = "<i>Graduated</i>";
        } else {
            $do = "<a href='?action=start&id={$academy['ac_id']}'>Attend</a>";
        }
        echo "<div class='row'>
		<div class='col-sm'>
			{$academy['ac_name']}<br />";
            //Hide academy level requirement if there is no requirement.
            if (!empty($academy['ac_level'])) {
                echo "Level: {$academy['ac_level']}";
            }
        echo "
		</div>
		<div class='col-sm'>
			{$academy['ac_desc']}
		</div>
		<div class='col-sm'>
			" . number_format($academy['ac_cost']) . " {$_CONFIG['primary_currency']}
		</div>
		<div class='col-sm'>
			{$do}
		</div>
		</div>
		<hr />";
    }
    echo "</div>";
}

function start()
{
    global $db, $userid, $ir, $h, $api, $_CONFIG;
	$course_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    //If the user doesn't specific a course to take.
    if (empty($course_id)) {
        alert('danger', "Uh Oh!", "You didn't select a valid course to take.", true, 'academy.php');
        die($h->endpage());
    }
    $courq = $db->query("SELECT * FROM `academy` WHERE `ac_id` = {$course_id} LIMIT 1");
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
    //If the user doesn't have enough {$_CONFIG['primary_currency']} for this course.
    if ($course['ac_cost'] > $ir['primary_currency']) {
        alert('danger', "Uh Oh!", "You do not have enough cash to take this course. You need {$course['ac_cost']},
                                yet you only have {$ir['primary_currency']}", true, 'academy.php');
        die($h->endpage());
    }
    $cdo = $db->query("SELECT COUNT(`userid`)
                             FROM `academy_done`
                             WHERE `userid` = {$userid}
                             AND `course` = {$course_id}");
    //If the user has already taken this course.
    if ($db->fetch_single($cdo) > 0) {
        alert('danger', "Uh Oh!", "You have already graduated from this course. No need to enroll again.", true, 'academy.php');
        die($h->endpage());
    }
    $completed = time() + ($course['ac_days'] * 86400); //Current Time + (Academy days * seconds in a day)
    $db->query("UPDATE `users` SET `course` = {$course_id},
                `course_complete` = {$completed} 
                WHERE `userid` = {$userid}");
    //Update user's course, and course completion time.
	$api->user->takeCurrency($userid, 'primary', $course['ac_cost']);
    alert('success', "Success!", "You have successfully enrolled yourself in the {$course['ac_name']} course. It will
	                            completed in {$course['ac_days']} days.", true, 'index.php');
}

$h->endpage();