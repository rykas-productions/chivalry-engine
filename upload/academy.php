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
    echo "You are currently enrolled in the {$coud['ac_name']} course. You will be finished in " . TimeUntil_Parse($ir['course_complete']) . ".";
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
    global $db, $userid;
    echo "<table class='table table-bordered table-hover'>
		<thead>
			<tr>
				<th>
					Course
				</th>
				<th>
					Description
				</th>
				<th>
					Cost
				</th>
				<th>
                    Action
				</th>
			</tr>
		</thead>
		<tbody>
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
        echo "<tr>
		<td>
			{$academy['ac_name']}<br />";
        //Hide academy level requirement if there is no requirement.
        if (!empty($academy['ac_level'])) {
            echo "Level: {$academy['ac_level']}";
        }
        echo "
		</td>
		<td>
			{$academy['ac_desc']}
		</td>
		<td>
			" . number_format($academy['ac_cost']) . " {$_CONFIG['primary_currency']}
		</td>
		<td>
			{$do}
		</td>";
    }
    echo "</tbody></table>";
}

function start()
{
    global $db, $userid, $ir, $h, $api;
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