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
case "description":
    description();
    break;
default:
    header ("Location: academy.php?action=menu");
    break;
}

function menu()
{
	global $db,$h;
	echo "<table class='table table-bordered table-hover'>
		<thead>
			<tr>
				<th>
					Academy
				</th>
				<th>
					Cost
				</th>
				<th>
					Min Level
				</th>
				<th>
					Start Course
				</th>
			</tr>
		</thead>
		<tbody>
	   ";
	$acadq = $db->query("SELECT * FROM `academy`");
	while ($academy = $db->fetch_row($acadq))
	{
		echo "<tr>
		<td>
			<a href='academy.php?action=description&id={$academy['academyid']}'>{$academy['academyname']}</a>
		</td>
		<td>
			{$academy['academycost']}
		</td>
		<td>
			{$academy['academylevel']}
		</td>
		<td>
			<a href='academy.php?action=start&id={$academy['academyid']}'>Start Academic Course</a>
		</td>";
	}
	echo"</tbody></table>";
	$h->endpage();
}

function start()
{
	global $db,$userid,$lang, $ir, $h;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(intval($_GET['id'])) : '';
	$academyid = $_GET['id'];
	$courq = $db->query( "SELECT `a`.* FROM `academy` AS `a` WHERE `a`.`academyid` = {$academyid} LIMIT 1");
	$course = $db->fetch_row($courq);
	if ($ir['course'] == 0) {
		if ($ir['primary_currency'] >= $course['academycost'])
		{
			if ($ir['level'] >= $course['academylevel'])
			{
				$new_currency = $ir['primary_currency'] - $course['academycost'];
				$db->query("update `users` SET `primary_currency`='{$new_currency}' WHERE `userid` = '{$userid}'");
				alert("success","{$lang['ACADEMY_STARTED_COURSE']}","<br />you have started the course {$course['academyname']}.<br />It will finish in {$course['academydays']} day(s)");
				echo "<br /><br /><a href='index.php'>{$lang['ACADEMY_RETURN_HOME']}</a>";
				//$db->query("update `users` SET `course`='{$academyid}' WHERE `userid` = '{$userid}'");
				//$db->query("update `users` SET `days_left`='{$course['academydays']}' WHERE `userid` = '{$userid}'");
			}
			else
			{
				alert("danger", "{$lang['ACADEMY_LOW_LEVEL_1']}<br />", "{$lang['ACADEMY_LOW_LEVEL_2']}");
			}
		}
		else
		{
			alert("danger", "{$lang['ACADEMY_INSUFFICIENT_CURRENCY_1']}<br />", "{$lang['ACADEMY_INSUFFICIENT_CURRENCY_2']}");
		}
	}
	else
	{
		alert("danger", "{$lang['ACADEMY_IN_COURSE_1']}", "{$lang['ACADEMY_IN_COURSE_2']} {$ir['days_left']} {$lang['ACADEMY_IN_COURSE_3']}");
	}
	$h->endpage();
}
function description()
{
	global $db,$lang, $h;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(intval($_GET['id'])) : '';
	$academyid = $_GET['id'];
	if (!$academyid)
	{
		echo 'Invalid ID';
	}
	else
	{
		$q = $db->query("SELECT * FROM `academy` WHERE `academyid` = {$academyid}");
		if ($db->num_rows($q) == 0)
		{
			echo 'Invalid ID';
		}
		else
		{
			$academy_info = $db->fetch_row($q);
			echo "
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						{$lang['ACADEMY_INFO_NAME']} {$academy_info['academyname']}
					</th>
				</tr>
				<tr>
					<th width='33%'>
						{$lang['ACADEMY_INFO_DESC']}
					</th>
					<td>
						{$academy_info['academydesc']}
					</td>
				</tr>
				<tr>
					<th width='33%'>
						{$lang['ACADEMY_INFO_COST']}
					</th>
					<td>
						{$academy_info['academycost']}
					</td>
				</tr>
				<tr>
					<th width='33%'>
						{$lang['ACADEMY_INFO_LEVEL']}
					</th>
					<td>
						{$academy_info['academylevel']}
					</td>
				</tr>
				<tr>
					<th width='33%'>
						{$lang['ACADEMY_INFO_DAYS']}
					</th>
					<td>
						{$academy_info['academydays']}
					</td>
				</tr>";
			for ($enum = 1; $enum <= 4; $enum++)
			{
				if ($academy_info["effect{$enum}_on"] == 'true')
				{
					$einfo = unserialize($academy_info["effect{$enum}"]);
					$einfo['inc_type'] = ($einfo['inc_type'] == 'percent') ? '%' : '';
					$einfo['dir'] = ($einfo['dir'] == 'pos') ? 'Increases' : 'Decreases';
					echo "
					<tr>
						<th>
							{$lang['ACADEMY_INFO_EFFECT']}{$enum}
						</th>
						<td>
							{$lang['ACADEMY_DESCRIPTION_EFFECT_1']}{$einfo['dir']} {$lang['ACADEMY_DESCRIPTION_EFFECT_2']} {$einfo['stat']} {$lang['ACADEMY_DESCRIPTION_EFFECT_3']} {$einfo['inc_amount']}{$einfo['inc_type']}
						</td>
					</tr>";
				}
			}
			echo"
			</table>";
		$db->free_result($q);
		}
	}
	$h->endpage();
}