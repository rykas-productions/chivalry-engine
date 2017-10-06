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
        if (empty($_POST['jrPRIMPAY']) && (empty($_POST['jrSECONDARY']))) {
            alert('danger', "Uh Oh!", "Please specify the hourly wage for this job rank.");
            die($h->endpage());
        }
        if (empty($_POST['jACT'])) {
            alert('danger', "Uh Oh!", "Job Rank activity requirement must be at least 1.");
            die($h->endpage());
        }
        $q = $db->query("SELECT `jRANK` from `jobs` WHERE `jNAME` = '{$_POST['jNAME']}'");
        if ($db->num_rows($q) > 0) {
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
        alert('success', "Success!", "You have successfully created the {$_POST['jNAME']} job!", true, 'index.php');
        $api->SystemLogsAdd($userid, 'staff', "Created the {$_POST['jNAME']} job.");
    }
}

function jobedit()
{
    global $db, $userid, $h, $api;
    echo "<h3>Edit Job</h3><hr />";
    if (!isset($_POST['step']))
        $_POST['step'] = 0;
    if ($_POST['step'] == 2) {
        $_POST['job'] = (isset($_POST['job']) && is_numeric($_POST['job'])) ? abs(intval($_POST['job'])) : 0;
        $_POST['jNAME'] = (isset($_POST['jNAME']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                $_POST['jNAME'])) ? $db->escape(strip_tags(stripslashes($_POST['jNAME']))) : '';
        $_POST['jDESC'] = (isset($_POST['jDESC'])) ? $db->escape(strip_tags(stripslashes($_POST['jDESC']))) : '';
        $_POST['jBOSS'] = (isset($_POST['jBOSS']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                $_POST['jBOSS'])) ? $db->escape(strip_tags(stripslashes($_POST['jBOSS']))) : '';
        $_POST['jobrank'] = (isset($_POST['jobrank']) && is_numeric($_POST['jobrank'])) ? abs(intval($_POST['jobrank'])) : 0;
        if (empty($_POST['job'])) {
            alert('danger', "Uh Oh!", "Please specify the job you wish to edit.");
            die($h->endpage());
        }
        if (empty($_POST['jNAME']) || empty($_POST['jDESC']) || empty($_POST['jBOSS'])) {
            alert('danger', "Uh Oh!", "Please fill out all the fields.");
            die($h->endpage());
        }
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_editjob2', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "We have blocked this action for your security. Please submit forms quickly.");
            die($h->endpage());
        }
        $q = $db->query("SELECT * FROM `jobs` WHERE `jRANK` = {$_POST['job']}");
        if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "The job you've chosen to edit does not exist or is invalid.");
            die($h->endpage());
        }
        $db->free_result($q);
        $q = $db->query("SELECT * FROM `job_ranks` WHERE `jrID` = {$_POST['jobrank']}");
        if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "The job rank you've chosen for this job does not exist or is invalid.");
            die($h->endpage());
        }
        $db->free_result($q);
        $q = $db->query("SELECT `jRANK` from `jobs` WHERE `jNAME` = '{$_POST['jNAME']}' AND `jRANK` != {$_POST['job']}");
        if ($db->num_rows($q) > 0) {
            alert('danger', "Uh Oh!", "You may not have the same job name used more than once.");
            die($h->endpage());
        }
        $db->free_result($q);
        $db->query("UPDATE `jobs`
                    SET `jNAME` = '{$_POST['jNAME']}',
                    `jDESC` = '{$_POST['jDESC']}',
                    `jBOSS` = '{$_POST['jBOSS']}',
                    `jSTART` = {$_POST['jobrank']}
                    WHERE `jRANK` = {$_POST['job']}");
        alert('success', "Success!", "You have successfully updated the {$_POST['jNAME']} job.", true, 'index.php');
        $api->SystemLogsAdd($userid, 'staff', "Updated the {$_POST['jNAME']} [{$_POST['job']}] job");
    } elseif ($_POST['step'] == 1) {
        $_POST['job'] = (isset($_POST['job']) && is_numeric($_POST['job'])) ? abs(intval($_POST['job'])) : 0;
        if (empty($_POST['job'])) {
            alert('danger', "Uh Oh!", "Please specify the job you wish to edit.");
            die($h->endpage());
        }
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_editjob1', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "We have blocked this action for your security. Please submit forms quickly.");
            die($h->endpage());
        }
        $q = $db->query("SELECT * FROM `jobs` WHERE `jRANK` = {$_POST['job']}");
        if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "The job you've chosen to edit does not exist or is invalid.");
            die($h->endpage());
        }
        $r = $db->fetch_row($q);
        $db->free_result($q);
        $csrf = request_csrf_html('staff_editjob2');
        $jobname = addslashes($r['jNAME']);
        $jobdesc = addslashes($r['jDESC']);
        $jobowner = addslashes($r['jBOSS']);
        echo "<form method='post'>";
        echo "<table class='table table-bordered'>
            <tr>
				<th colspan='2'>
					Fill out this form completely to edit the job.
				</th>
			</tr>
			<tr>
				<th>
					Job Name
				</th>
				<td>
					<input type='text' name='jNAME' required='1' value='{$jobname}' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					Description
				</th>
				<td>
					<input type='text' name='jDESC' required='1' value='{$jobdesc}' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					Employer's Name
				</th>
				<td>
					<input type='text' name='jBOSS' required='1' value='{$jobowner}' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					First Job Rank
				</th>
				<td>
					" . jobrank_dropdown('jobrank', $r['jSTART']) . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='Edit Job' class='btn btn-primary'>
				</td>
			</tr>
		</table>
		{$csrf}
		<input type='hidden' value='2' name='step'>
        <input type='hidden' value='{$_POST['job']}' name='job'>
		</form>";
    } else {
        $csrf = request_csrf_html('staff_editjob1');
        echo "<form method='post'><table class='table table-bordered'>
        <input type='hidden' value='1' name='step'>
        <tr>
            <th colspan='2'>
                Please select the job you wish to edit.
            </th>
        </tr>
        <tr>
            <th>
                Job
            </th>
            <td>

                " . job_dropdown() . "
            </td>
        </tr>
         <tr>
            <td colspan='2'>
                <input type='submit' value='Edit Job' class='btn btn-primary'>
            </td>
        </tr>
        </table>
        {$csrf}
        </form>";
    }
}

function jobdele()
{
    global $db, $userid, $api, $h;
    if (isset($_POST['job'])) {
        $_POST['job'] = (isset($_POST['job']) && is_numeric($_POST['job'])) ? abs(intval($_POST['job'])) : 0;
        //Verify CSRF
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_deljob', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "We have blocked this action for your security. Please submit forms quickly.");
            die($h->endpage());
        }
        //Inputted job was cleared because of sanitation.
        if (empty($_POST['job'])) {
            alert('danger', "Uh Oh!", "The job you input is invalid and/or empty.");
            die($h->endpage());
        }
        //Select the job to see if it exists.
        $q = $db->query("SELECT * FROM `jobs` WHERE `jRANK` = {$_POST['job']}");
        if ($db->num_rows($q) == 0) {
            alert('danger', "Uh Oh!", "The job you're trying to delete either does not exist, or was previously deleted.");
            die($h->endpage());
        }
        $db->free_result($q);
        $db->query("DELETE FROM `jobs` WHERE `jRANK` = {$_POST['job']}");
        $db->query("DELETE FROM `job_ranks` WHERE `jrJOB` = {$_POST['job']}");
        $ranksdel = $db->affected_rows();
        $db->query("UPDATE `users` SET `job` = 0, `jobrank` = 0, `jobwork` = 0 WHERE `job` = {$_POST['job']}");
        $unemployed = $db->affected_rows();
        alert('success', "Success!", "You have successfully deleted this job. {$ranksdel} job rank(s) were removed.
            {$unemployed} player(s) are now jobless due to this deletion.", true, 'index.php');
        $api->SystemLogsAdd($userid,'staff',"Deleted Job ID {$_POST['job']}.");
    } else {
        $csrf = request_csrf_html('staff_deljob');
        echo "<form method='post'>
        Please select the form you wish to delete. Users who are currently employed here will have their job data set
        back to default<br />
        " . job_dropdown() . "<br />
        <input type='submit' value='Delete Job' class='btn btn-primary'>
        {$csrf}
        </form>";
    }
}

function newjobrank()
{
    global $db,$userid,$api,$h;
    if (isset($_POST['job']))
    {
        $_POST['rank'] = (isset($_POST['rank']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                $_POST['rank'])) ? $db->escape(strip_tags(stripslashes($_POST['rank']))) : '';
        $_POST['str'] = (isset($_POST['str']) && is_numeric($_POST['str'])) ? abs(intval($_POST['str'])) : 0;
        $_POST['lab'] = (isset($_POST['lab']) && is_numeric($_POST['lab'])) ? abs(intval($_POST['lab'])) : 0;
        $_POST['iq'] = (isset($_POST['iq']) && is_numeric($_POST['iq'])) ? abs(intval($_POST['iq'])) : 0;
        $_POST['workunit'] = (isset($_POST['workunit']) && is_numeric($_POST['workunit'])) ?
            abs(intval($_POST['workunit'])) : 0;
        $_POST['primpay'] = (isset($_POST['primpay']) && is_numeric($_POST['primpay'])) ? abs(intval($_POST['primpay'])) : 0;
        $_POST['seccpay'] = (isset($_POST['seccpay']) && is_numeric($_POST['seccpay'])) ? abs(intval($_POST['seccpay'])) : 0;
        $_POST['job'] = (isset($_POST['job']) && is_numeric($_POST['job'])) ? abs(intval($_POST['job'])) : 0;
        //Verify CSRF
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_newjobrank', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "We have blocked this action for your security. Please submit forms quickly.");
            die($h->endpage());
        }
        //Verify we have the minimum required fields.
        if (empty($_POST['rank']) || empty($_POST['str']) || empty($_POST['lab']) || empty($_POST['iq'])) {
            alert('danger', "Uh Oh!", "Please fill out all the fields concerning the job rank's requirements/information.");
            die($h->endpage());
        }
        //Verify we have the wages
        if (empty($_POST['primpay']) && (empty($_POST['seccpay']))) {
            alert('danger', "Uh Oh!", "Please specify the hourly wage for this job rank.");
            die($h->endpage());
        }
        //Verify we have job activity requirements
        if (empty($_POST['workunit'])) {
            alert('danger', "Uh Oh!", "Job Rank activity requirement must be at least 1.");
            die($h->endpage());
        }
        //Verify the job we want to add a rank to exists.
        $q=$db->query("SELECT * FROM `jobs` WHERE `jRANK` = {$_POST['job']}");
        if ($db->num_rows($q) == 0)
        {
            alert('danger', "Uh Oh!", "The job you are tryign to add a rank to does not exist.");
            die($h->endpage());
        }
        $db->free_result($q);
        $db->query("INSERT INTO `job_ranks`
                    (`jrID`, `jrRANK`, `jrJOB`, `jrPRIMPAY`, `jrSECONDARY`, `jrACT`, `jrSTR`, `jrLAB`, `jrIQ`)
                    VALUES (NULL, '{$_POST['rank']}', '{$_POST['job']}', '{$_POST['primpay']}', '{$_POST['seccpay']}',
                    '{$_POST['workunit']}', '{$_POST['str']}', '{$_POST['lab']}', '{$_POST['iq']}')");
        alert('success', "Success!", "You have successfully created the {$_POST['rank']} job rank!", true, 'index.php');
        $api->SystemLogsAdd($userid, 'staff', "Created the {$_POST['rank']} job rank.");
    }
    else
    {
        $csrf = request_csrf_html('staff_newjobrank');
        echo "Fill out this form to add more job ranks to a specific job.";
        echo "<form method='post'>
        <table class='table table-bordered'>
            <tr>
                <th>
                    Rank Name
                </th>
                <td>
                    <input type='text' required='1' name='rank' class='form-control'>
                </td>
            </tr>
            <tr>
                <th>
                    Primary Currency Pay
                </th>
                <td>
                    <input type='number' min='0' value='0' required='1' name='primpay' class='form-control'>
                </td>
            </tr>
            <tr>
                <th>
                    Secondary Currency Pay
                </th>
                <td>
                    <input type='number' min='0' value='0' required='1' name='seccpay' class='form-control'>
                </td>
            </tr>
            <tr>
                <th>
                    Job
                </th>
                <td>
                    " . job_dropdown() . "
                </td>
            </tr>
            <tr>
                <th>
                    Strength Requirement
                </th>
                <td>
                    <input type='number' min='0' value='0' required='1' name='str' class='form-control'>
                </td>
            </tr>
            <tr>
                <th>
                    Labor Requirement
                </th>
                <td>
                    <input type='number' min='0' value='0' required='1' name='lab' class='form-control'>
                </td>
            </tr>
            <tr>
                <th>
                    IQ Requirement
                </th>
                <td>
                    <input type='number' min='0' value='0' required='1' name='iq' class='form-control'>
                </td>
            </tr>
            <tr>
                <th>
                    Work Units Required
                </th>
                <td>
                    <input type='number' min='0' value='0' required='1' name='workunit' class='form-control'>
                </td>
            </tr>
            <tr>
                <td colspan='2'>
                    <input type='submit' value='Create Job Rank' class='btn btn-primary'>
                </td>
            </tr>
        </table>
        {$csrf}
        </form>";
    }
}

$h->endpage();