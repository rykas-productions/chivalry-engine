<?php
/*
	File: staff/staff_jobs.php
	Created: 4/4/2017 at 7:02PM Eastern Time
	Info: Staff panel for handling/editing/creating the in-game jobs.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/
require('sglobals.php');
if ($api->UserMemberLevelGet($userid,'Admin') == false)
{
	alert('danger',"Uh Oh!","You do not have permission to be here.");
	die($h->endpage());
}
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case 'newjob':
    newjob();
    break;
case 'jobedit':
    jobedit();
    break;
case 'newjobrank':
    newjobrank();
    break;
case 'jobrankedit':
    jobrankedit();
    break;
case 'jobdele':
    jobdele();
    break;
case 'jobrankdele':
    jobrankdele();
    break;
default:
    die($h->endpage());
    break;
}
function newjob()
{
	global $db,$userid,$h;
	echo "<h3>Create Job</h3><hr />";
	if (!isset($_POST['jNAME']))
	{
		$csrf = request_csrf_html('staff_newjob');
		echo "<table class='table table-bordered'>
			<tr>
				<th>
					Job Name
				</th>
				<td>
					<input type='text' name='jNAME' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					Description
				</th>
				<td>
					<input type='text' name='jDESC' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					Employer's Name
				</th>
				<td>
					<input type='text' name='jBOSS' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th colspan='2'>
					First Job Rank
				</th>
			</tr>
			<tr>
				<th>
					Rank Name
				</th>
				<td>
					<input type='text' name='jRNAME' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					Required Activity
				</th>
				<td>
					<input type='number' min='1' name='jACT' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th colspan='2'>
					Daily Payment
				</th>
			</tr>
			<tr>
				<th>
					Primary Currency
				</th>
				<td>
					<input type='number' min='0' name='jPRIMPAY' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					Secondary Currency
				</th>
				<td>
					<input type='number' min='0' name='jSECONDARY' required='1' class='form-control'>
				</td>
			</tr>
		</table>";
	}
	else
	{
		
	}
}
$h->endpage();