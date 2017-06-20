<?php
/*
	File:		academy.php
	Created: 	4/4/2016 at 11:49PM Eastern Time
	Info: 		The academy, which players can use to take courses and
				increase their stats for currency and waiting.
	Author:		ImJustIsabella
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require ("globals.php");
echo "<h4>{$lang['ACA_NAME']}</h4><hr>";
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
    echo "{$lang['ACA_ALRDYDO']} {$coud['ac_name']} {$lang['ACA_ALRDYDO1']} " . TimeUntil_Parse($ir['course_complete']) .".";
	die($h->endpage());
}
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case "menu":
    menu();
    break;
case "start":
    start();
    break;
default:
    header ("Location: ?action=menu");
    break;
}

function menu()
{
	global $db,$h,$lang,$api,$userid;
	echo "<table class='table table-bordered table-hover'>
		<thead>
			<tr>
				<th>
					{$lang['ACA_ACA']}
				</th>
				<th>
					{$lang['ACA_DESC']}
				</th>
				<th>
					{$lang['ACA_COST']}
				</th>
				<th>
					{$lang['ACA_LINK']}
				</th>
			</tr>
		</thead>
		<tbody>
	   ";
    //Select the courses from in-game.
	$acadq = $db->query("SELECT * FROM `academy` ORDER BY `ac_level` ASC, `ac_id` ASC");
	while ($academy = $db->fetch_row($acadq))
	{
		$cdo = $db->query("SELECT COUNT(`userid`)
                             FROM `academy_done`
                             WHERE `userid` = {$userid}
                             AND `course` = {$academy['ac_id']}");
            //If user has already completed the course.
            if ($db->fetch_single($cdo) > 0)
            {
                $do = "<i>{$lang['ACA_DONE']}</i>";
            }
            else
            {
                $do = "<a href='?action=start&id={$academy['ac_id']}'>{$lang['ACA_ATTEND']}</a>";
            }
		echo "<tr>
		<td>
			{$academy['ac_name']}<br />";
            //Hide academy level requirement if there is no requirement.
			if (!empty($academy['ac_level']))
			{
				echo "{$lang['ACA_LVL']}{$academy['ac_level']}";
			}
			echo"
		</td>
		<td>
			{$academy['ac_desc']}
		</td>
		<td>
			" . number_format($academy['ac_cost']) . " {$lang['INDEX_PRIMCURR']}
		</td>
		<td>
			{$do}
		</td>";
	}
	echo"</tbody></table>";
}

function start()
{
	global $db,$userid,$lang,$ir,$h,$api;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(intval($_GET['id'])) : 0;
    //If the user doesn't specific a course to take.
	if (empty($_GET['id']))
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['ACA_ERR'],true,'academy.php');
		die($h->endpage());
	}
	$courq = $db->query("SELECT * FROM `academy` WHERE `ac_id` = {$_GET['id']} LIMIT 1");
    //If the course specified does not exist.
	if ($db->num_rows($courq) == 0)
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['ACA_ERR1'],true,'academy.php');
		die($h->endpage());
	}
	$course = $db->fetch_row($courq);
    //If the user's level is lower than the course requirement.
	if ($course['ac_level'] > $ir['level'])
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['ACA_ERR2'],true,'academy.php');
		die($h->endpage());
	}
    //If the user doesn't have enough primary currency for this course.
	if ($course['ac_cost'] > $ir['primary_currency'])
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['ACA_ERR3'],true,'academy.php');
		die($h->endpage());
	}
	$cdo = $db->query("SELECT COUNT(`userid`)
                             FROM `academy_done`
                             WHERE `userid` = {$userid}
                             AND `course` = {$_GET['id']}");
    //If the user has already taken this course.
	if ($db->fetch_single($cdo) > 0)
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['ACA_ERR4'],true,'academy.php');
		die($h->endpage());
	}
	$completed=time() + ($course['ac_days']*86400); //Current Time + (Academy days * seconds in a day)
	$db->query("UPDATE `users` SET `course` = {$_GET['id']}, 
                `course_complete` = {$completed} 
                WHERE `userid` = {$userid}");
    //Update user's course, and course completion time.
	$api->UserTakeCurrency($userid,'primary',$course['ac_cost']); //Take user's money.
	alert('success',$lang['ERROR_SUCCESS'],"{$lang['ACA_SUCC']} {$course['ac_name']} {$lang['ACA_SUCC1']} {$course['ac_days']} {$lang['ACA_SUCC2']}",true,'index.php');
}
$h->endpage();