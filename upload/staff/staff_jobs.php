<?php
/*
	File: staff/staff_jobs.php
	Created: 4/4/2017 at 7:02PM Eastern Time
	Info: Staff panel for handling/editing/creating the in-game jobs.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/
require('sglobals.php');
if ($api->UserMemberLevelGet($userid, 'Admin') == false) {
    alert('danger', "Uh Oh!", "You do not have permission to be here.");
    die($h->endpage());
}
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
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
        alert('danger', "Uh Oh!", "Please select a valid action to perform.", true, 'index.php');
        die($h->endpage());
        break;
}
function newjob()
{
    global $db, $userid, $h, $api;
    echo "<h3>Create Job</h3><hr />";
    if (!isset($_POST['jNAME'])) {
        $csrf = request_csrf_html('staff_newjob');
        echo "<form method='post'>";
        echo "<table class='table table-bordered'>
            <tr>
				<th colspan='2'>
					Fill out this form completely to add a job to the game.
				</th>
			</tr>
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
					<input type='text' name='jrNAME' required='1' class='form-control'>
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
					Hourly Wage
				</th>
			</tr>
			<tr>
				<th>
					Primary Currency
				</th>
				<td>
					<input type='number' min='0' name='jrPRIMPAY' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					Secondary Currency
				</th>
				<td>
					<input type='number' min='0' name='jrSECONDARY' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th colspan='2'>
					Requirements
				</th>
			</tr>
			<tr>
				<th>
					Strength
				</th>
				<td>
					<input type='number' min='0' name='jSTR' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					Labor
				</th>
				<td>
					<input type='number' min='0' name='jLAB' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					IQ
				</th>
				<td>
					<input type='number' min='0' name='jIQ' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='Create Job' class='btn btn-primary'>
				</td>
			</tr>
		</table>
		{$csrf}
		</form>";
    } else {
        $_POST['jNAME'] = (isset($_POST['jNAME']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                $_POST['jNAME'])) ? $db->escape(strip_tags(stripslashes($_POST['jNAME']))) : '';
        $_POST['jDESC'] = (isset($_POST['jDESC'])) ? $db->escape(strip_tags(stripslashes($_POST['jDESC']))) : '';
        $_POST['jBOSS'] = (isset($_POST['jBOSS']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                $_POST['jBOSS'])) ? $db->escape(strip_tags(stripslashes($_POST['jBOSS']))) : '';
        $_POST['jrNAME'] = (isset($_POST['jrNAME']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                $_POST['jrNAME'])) ? $db->escape(strip_tags(stripslashes($_POST['jrNAME']))) : '';
        $_POST['jrPRIMPAY'] = (isset($_POST['jrPRIMPAY']) && is_numeric($_POST['jrPRIMPAY'])) ? abs(intval($_POST['jrPRIMPAY'])) : 0;
        $_POST['jrSECONDARY'] = (isset($_POST['jrSECONDARY']) && is_numeric($_POST['jrSECONDARY'])) ? abs(intval($_POST['jrSECONDARY'])) : 0;
        $_POST['jSTR'] = (isset($_POST['jSTR']) && is_numeric($_POST['jSTR'])) ? abs(intval($_POST['jSTR'])) : 0;
        $_POST['jLAB'] = (isset($_POST['jLAB']) && is_numeric($_POST['jLAB'])) ? abs(intval($_POST['jLAB'])) : 0;
        $_POST['jIQ'] = (isset($_POST['jIQ']) && is_numeric($_POST['jIQ'])) ? abs(intval($_POST['jIQ'])) : 0;
        $_POST['jACT'] = (isset($_POST['jACT']) && is_numeric($_POST['jACT'])) ? abs(intval($_POST['jACT'])) : 0;

        if (!isset($_POST['verf']) || !verify_csrf_code('staff_newjob', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "We have blocked this action for your security. Please submit forms quickly.");
            die($h->endpage());
        }
        if (empty($_POST['jNAME']) || empty($_POST['jDESC']) || empty($_POST['jBOSS'])) {
            alert('danger', "Uh Oh!", "Please fill out all the fields concerning the job's information.");
            die($h->endpage());
        }
        if (empty($_POST['jrNAME']) || empty($_POST['jIQ']) || empty($_POST['jSTR']) || empty($_POST['jLAB'])) {
            alert('danger', "Uh Oh!", "Please fill out all the fields concerning the job rank's requirements/information.");
            die($h->endpage());
        }
        if (empty($_POST['jrPRIMPAY']) && ($_POST['jrSECONDARY'])) {
            alert('danger', "Uh Oh!", "Please specify the hourly wage for this job rank.");
            die($h->endpage());
        }
        if (empty($_POST['jACT'])) {
            alert('danger', "Uh Oh!", "Job Rank activity requirement must be at least 1.");
            die($h->endpage());
        }
        $q=$db->query("SELECT `jRANK` from `jobs` WHERE `jNAME` = '{$_POST['jNAME']}'");
        if ($db->num_rows($q) > 0)
        {
            alert('danger', "Uh Oh!", "You may not have the same job name used more than once.");
            die($h->endpage());
        }
        $db->query("INSERT INTO `jobs` (`jRANK`, `jNAME`, `jSTART`, `jDESC`, `jBOSS`)
                      VALUES (NULL, '{$_POST['jNAME']}', '0', '{$_POST['jDESC']}', '{$_POST['jBOSS']}')");
        $i = $db->insert_id();
        $db->query("INSERT INTO `job_ranks`
                    (`jrID`, `jrRANK`, `jrJOB`, `jrPRIMPAY`, `jrSECONDARY`, `jrACT`, `jrSTR`, `jrLAB`, `jrIQ`)
                    VALUES (NULL, '{$_POST['jrNAME']}', '{$i}', '{$_POST['jrPRIMPAY']}', '{$_POST['jrSECONDARY']}',
                    '{$_POST['jACT']}', '{$_POST['jSTR']}', '{$_POST['jLAB']}', '{$_POST['jIQ']}')");
        $j = $db->insert_id();
        $db->query("UPDATE `jobs` SET `jSTART` = {$j} WHERE `jRANK` = {$i}");
        alert('success',"Success!","You have successfully created the {$_POST['jNAME']} job!",true,'index.php');
        $api->SystemLogsAdd($userid,'staff',"Created the {$_POST['jNAME']} job.");
    }
}

$h->endpage();