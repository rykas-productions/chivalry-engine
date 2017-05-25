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
if ($ir['course'] > 0)
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
    header ("Location: academy.php?action=menu");
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
	$acadq = $db->query("SELECT * FROM `academy` ORDER BY `ac_level` ASC, `ac_id` ASC");
	while ($academy = $db->fetch_row($acadq))
	{
		$cdo = $db->query("SELECT COUNT(`userid`)
                             FROM `academy_done`
                             WHERE `userid` = {$userid}
                             AND `course` = {$academy['ac_id']}");
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
			{$academy['ac_cost']} {$lang['INDEX_PRIMCURR']}
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
	if (empty($_GET['id']))
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['ACA_ERR'],true,'academy.php');
		die($h->endpage());
	}
	$courq = $db->query("SELECT * FROM `academy` WHERE `ac_id` = {$_GET['id']} LIMIT 1");
	if ($db->num_rows($courq) == 0)
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['ACA_ERR1'],true,'academy.php');
		die($h->endpage());
	}
	$course = $db->fetch_row($courq);
	if ($course['ac_level'] > $ir['level'])
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['ACA_ERR2'],true,'academy.php');
		die($h->endpage());
	}
	if ($course['ac_cost'] > $ir['primary_currency'])
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['ACA_ERR3'],true,'academy.php');
		die($h->endpage());
	}
	$cdo = $db->query("SELECT COUNT(`userid`)
                             FROM `academy_done`
                             WHERE `userid` = {$userid}
                             AND `course` = {$_GET['id']}");
	if ($db->fetch_single($cdo) > 0)
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['ACA_ERR4'],true,'academy.php');
		die($h->endpage());
	}
	$completed=time() + ($course['ac_days']*86400);
	$db->query("UPDATE `users` SET `course` = {$_GET['id']}, `course_complete` = {$completed} WHERE `userid` = {$userid}");
	$api->UserTakeCurrency($userid,'primary',$course['ac_cost']);
	alert('success',$lang['ERROR_SUCCESS'],"{$lang['ACA_SUCC']} {$course['ac_name']} {$lang['ACA_SUCC1']} {$course['ac_days']} {$lang['ACA_SUCC2']}",true,'index.php');
}
$h->endpage();