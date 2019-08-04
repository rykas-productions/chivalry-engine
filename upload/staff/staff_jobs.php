<?php
/*
	File: 		staff/staff_jobs.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows staff to do actions relating to the in-game jobs.
	Author: 	TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine/
	
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
require('sglobals.php');
if (!$api->user->getStaffLevel($userid, 'Admin')) {
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
        menu();
        break;
}
function menu()
{
	echo "<h3>Job Staff Menu</h3><hr />
    <a href='?action=newjob' class='btn btn-primary'>Create Job</a><br /><br />
    <a href='?action=jobedit' class='btn btn-primary'>Edit Job</a><br /><br />
    <a href='?action=jobdel' class='btn btn-primary'>Delete Job</a><br /><br />
	<a href='?action=newjobrank' class='btn btn-primary'>Create Job Rank</a><br /><br />
	<a href='?action=jobrankedit' class='btn btn-primary'>Edit Job Rank</a><br /><br />
	<a href='?action=jobrankdele' class='btn btn-primary'>Delete Job Rank</a><br /><br />";
}
function newjob()
{
    global $db, $userid, $h, $api;
    echo "<h3>Create Job</h3><hr />";
    if (!isset($_POST['jNAME'])) {
        $csrf = getHtmlCSRF('staff_newjob');
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
				<th colspan='2'>
					Hourly Wage
				</th>
			</tr>
			<tr>
				<th>
					" . constant("primary_currency") . "
				</th>
				<td>
					<input type='number' min='0' name='jrPRIMPAY' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					" . constant("secondary_currency") . "
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
					" . constant("stat_strength") . "
				</th>
				<td>
					<input type='number' min='0' name='jSTR' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					" . constant("stat_labor") . "
				</th>
				<td>
					<input type='number' min='0' name='jLAB' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					" . constant("stat_iq") . "
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

        if (!isset($_POST['verf']) || !checkCSRF('staff_newjob', stripslashes($_POST['verf']))) {
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
        $q = $db->query("SELECT `jRANK` from `jobs` WHERE `jNAME` = '{$_POST['jNAME']}'");
        if ($db->num_rows($q) > 0) {
            alert('danger', "Uh Oh!", "You may not have the same job name used more than once.");
            die($h->endpage());
        }
        $db->query("INSERT INTO `jobs` (`jRANK`, `jNAME`, `jSTART`, `jDESC`, `jBOSS`)
                      VALUES (NULL, '{$_POST['jNAME']}', '0', '{$_POST['jDESC']}', '{$_POST['jBOSS']}')");
        $i = $db->insert_id();
        $db->query("INSERT INTO `job_ranks`
                    (`jrID`, `jrRANK`, `jrJOB`, `jrPRIMPAY`, `jrSECONDARY`, `jrSTR`, `jrLAB`, `jrIQ`)
                    VALUES (NULL, '{$_POST['jrNAME']}', '{$i}', '{$_POST['jrPRIMPAY']}', '{$_POST['jrSECONDARY']}',
                    '{$_POST['jSTR']}', '{$_POST['jLAB']}', '{$_POST['jIQ']}')");
        $j = $db->insert_id();
        $db->query("UPDATE `jobs` SET `jSTART` = {$j} WHERE `jRANK` = {$i}");
        alert('success', "Success!", "You have successfully created the {$_POST['jNAME']} job!", true, 'index.php');
        $api->game->addLog($userid, 'staff', "Created the {$_POST['jNAME']} job.");
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
        if (!isset($_POST['verf']) || !checkCSRF('staff_editjob2', stripslashes($_POST['verf']))) {
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
        $api->game->addLog($userid, 'staff', "Updated the {$_POST['jNAME']} [{$_POST['job']}] job");
    } elseif ($_POST['step'] == 1) {
        $_POST['job'] = (isset($_POST['job']) && is_numeric($_POST['job'])) ? abs(intval($_POST['job'])) : 0;
        if (empty($_POST['job'])) {
            alert('danger', "Uh Oh!", "Please specify the job you wish to edit.");
            die($h->endpage());
        }
        if (!isset($_POST['verf']) || !checkCSRF('staff_editjob1', stripslashes($_POST['verf']))) {
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
        $csrf = getHtmlCSRF('staff_editjob2');
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
					" . dropdownJobRank('jobrank', $r['jSTART']) . "
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
        $csrf = getHtmlCSRF('staff_editjob1');
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

                " . dropdownJob() . "
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
    echo "<h3>Delete Job</h3><hr />";
    if (isset($_POST['job'])) {
        $_POST['job'] = (isset($_POST['job']) && is_numeric($_POST['job'])) ? abs(intval($_POST['job'])) : 0;
        //Verify CSRF
        if (!isset($_POST['verf']) || !checkCSRF('staff_deljob', stripslashes($_POST['verf']))) {
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
        $api->game->addLog($userid, 'staff', "Deleted Job ID {$_POST['job']}.");
    } else {
        $csrf = getHtmlCSRF('staff_deljob');
        echo "<form method='post'>
        Please select the form you wish to delete. Users who are currently employed here will have their job data set
        back to default<br />
        " . dropdownJob() . "<br />
        <input type='submit' value='Delete Job' class='btn btn-primary'>
        {$csrf}
        </form>";
    }
}

function newjobrank()
{
    global $db, $userid, $api, $h;
    echo "<h3>Create Job Rank</h3><hr />";
    if (isset($_POST['job'])) {
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
        if (!isset($_POST['verf']) || !checkCSRF('staff_newjobrank', stripslashes($_POST['verf']))) {
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
        $q = $db->query("SELECT * FROM `jobs` WHERE `jRANK` = {$_POST['job']}");
        if ($db->num_rows($q) == 0) {
            alert('danger', "Uh Oh!", "The job you are trying to add a rank to does not exist.");
            die($h->endpage());
        }
        $db->free_result($q);
        $db->query("INSERT INTO `job_ranks`
                    (`jrID`, `jrRANK`, `jrJOB`, `jrPRIMPAY`, `jrSECONDARY`, `jrACT`, `jrSTR`, `jrLAB`, `jrIQ`)
                    VALUES (NULL, '{$_POST['rank']}', '{$_POST['job']}', '{$_POST['primpay']}', '{$_POST['seccpay']}',
                    '{$_POST['workunit']}', '{$_POST['str']}', '{$_POST['lab']}', '{$_POST['iq']}')");
        alert('success', "Success!", "You have successfully created the {$_POST['rank']} job rank!", true, 'index.php');
        $api->game->addLog($userid, 'staff', "Created the {$_POST['rank']} job rank.");
    } else {
        $csrf = getHtmlCSRF('staff_newjobrank');
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
                    " . constant("primary_currency") . " Pay
                </th>
                <td>
                    <input type='number' min='0' value='0' required='1' name='primpay' class='form-control'>
                </td>
            </tr>
            <tr>
                <th>
                    " . constant("secondary_currency") . " Pay
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
                    " . dropdownJob() . "
                </td>
            </tr>
            <tr>
                <th>
                    " . constant("stat_strength") . " Requirement
                </th>
                <td>
                    <input type='number' min='0' value='0' required='1' name='str' class='form-control'>
                </td>
            </tr>
            <tr>
                <th>
                    " . constant("stat_labor") . " Requirement
                </th>
                <td>
                    <input type='number' min='0' value='0' required='1' name='lab' class='form-control'>
                </td>
            </tr>
            <tr>
                <th>
                    " . constant("stat_iq") . " Requirement
                </th>
                <td>
                    <input type='number' min='0' value='0' required='1' name='iq' class='form-control'>
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

function jobrankedit()
{
    global $db, $userid, $api, $h;
    echo "<h3>Edit Job Rank</h3><hr />";
    //Set step to 0 if no step specified.
    if (!isset($_POST['step']))
        $_POST['step'] = 0;
    //Processing and enter into DB
    if ($_POST['step'] == 2) {
        $_POST['rank'] = (isset($_POST['rank']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i",
                $_POST['rank'])) ? $db->escape(strip_tags(stripslashes($_POST['rank']))) : '';
        $_POST['str'] = (isset($_POST['str']) && is_numeric($_POST['str'])) ? abs(intval($_POST['str'])) : 0;
        $_POST['lab'] = (isset($_POST['lab']) && is_numeric($_POST['lab'])) ? abs(intval($_POST['lab'])) : 0;
        $_POST['iq'] = (isset($_POST['iq']) && is_numeric($_POST['iq'])) ? abs(intval($_POST['iq'])) : 0;
        $_POST['primpay'] = (isset($_POST['primpay']) && is_numeric($_POST['primpay'])) ? abs(intval($_POST['primpay'])) : 0;
        $_POST['seccpay'] = (isset($_POST['seccpay']) && is_numeric($_POST['seccpay'])) ? abs(intval($_POST['seccpay'])) : 0;
        $_POST['job'] = (isset($_POST['job']) && is_numeric($_POST['job'])) ? abs(intval($_POST['job'])) : 0;
        $_POST['jobrank'] = (isset($_POST['jobrank']) && is_numeric($_POST['jobrank'])) ? abs(intval($_POST['jobrank'])) : 0;
        //Verify CSRF
        if (!isset($_POST['verf']) || !checkCSRF('staff_editjobrank2', stripslashes($_POST['verf']))) {
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
        //Verify the job we want to add a rank to exists.
        $q = $db->query("SELECT * FROM `jobs` WHERE `jRANK` = {$_POST['job']}");
        if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "The job you are trying to add a rank to does not exist.");
            die($h->endpage());
        }
        $db->free_result($q);
        //Check that the job rank exists.
        $q = $db->query("SELECT * FROM `job_ranks` WHERE `jrID` = {$_POST['jobrank']}");
        if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "The job rank you've chosen to edit does not exist.");
            die($h->endpage());
        }
        $db->free_result($q);
        $db->query("UPDATE `job_ranks` SET
                    `jrRANK` = '{$_POST['rank']}',
                    `jrJOB` = {$_POST['job']},
                    `jrPRIMPAY` = {$_POST['primpay']},
                    `jrSECONDARY` = {$_POST['seccpay']},
                    `jrSTR` = {$_POST['str']},
                    `jrLAB` = {$_POST['lab']},
                    `jrIQ` = {$_POST['iq']}
                    WHERE `jrID` = {$_POST['jobrank']}");
        alert('success', "Success!", "You have successfully edited the {$_POST['rank']} job rank!", true, 'index.php');
        $api->game->addLog($userid, 'staff', "Edited the {$_POST['rank']} Job Rank.");
    }
    //Edit form
    if ($_POST['step'] == 1) {
        $_POST['jobrank'] = (isset($_POST['jobrank']) && is_numeric($_POST['jobrank'])) ? abs(intval($_POST['jobrank'])) : 0;
        //Verify CSRF
        if (!isset($_POST['verf']) || !checkCSRF('staff_jobrankedit', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "We have blocked this action for your security. Please submit forms quickly.");
            die($h->endpage());
        }
        //Check that the job rank is filled.
        if (empty($_POST['jobrank'])) {
            alert('danger', "Uh Oh!", "The job rank you've chosen is invalid.");
            die($h->endpage());
        }
        //Check that the job rank exists.
        $q = $db->query("SELECT * FROM `job_ranks` WHERE `jrID` = {$_POST['jobrank']}");
        if ($db->num_rows($q) == 0) {
            alert('danger', "Uh Oh!", "The job rank you've chosen does not exist.");
            die($h->endpage());
        }
        //If it does, select its data.
        $r = $db->fetch_row($q);
        $csrf = getHtmlCSRF('staff_editjobrank2');
        echo "Fill out this form to edit this jobrank.";
        echo "<form method='post'>
        <input type='hidden' value='2' name='step'>
        <input type='hidden' value='{$_POST['jobrank']}' name='jobrank'>
        <table class='table table-bordered'>
            <tr>
                <th>
                    Rank Name
                </th>
                <td>
                    <input type='text' required='1' name='rank' value='{$r['jrRANK']}' class='form-control'>
                </td>
            </tr>
            <tr>
                <th>
                    " . constant("primary_currency") . " Pay
                </th>
                <td>
                    <input type='number' min='0' value='{$r['jrPRIMPAY']}' required='1' name='primpay' class='form-control'>
                </td>
            </tr>
            <tr>
                <th>
                    " . constant("secondary_currency") . " Pay
                </th>
                <td>
                    <input type='number' min='0' value='{$r['jrSECONDARY']}' required='1' name='seccpay' class='form-control'>
                </td>
            </tr>
            <tr>
                <th>
                    Job
                </th>
                <td>
                    " . dropdownJob('job', $r['jrJOB']) . "
                </td>
            </tr>
            <tr>
                <th>
                    " . constant("stat_strength") . " Requirement
                </th>
                <td>
                    <input type='number' min='0' value='{$r['jrSTR']}' required='1' name='str' class='form-control'>
                </td>
            </tr>
            <tr>
                <th>
                    " . constant("stat_labor") . " Requirement
                </th>
                <td>
                    <input type='number' min='0' value='{$r['jrLAB']}' required='1' name='lab' class='form-control'>
                </td>
            </tr>
            <tr>
                <th>
                    " . constant("stat_iq") . " Requirement
                </th>
                <td>
                    <input type='number' min='0' value='{$r['jrIQ']}' required='1' name='iq' class='form-control'>
                </td>
            </tr>
            <tr>
                <td colspan='2'>
                    <input type='submit' value='Edit Job Rank' class='btn btn-primary'>
                </td>
            </tr>
        </table>
        {$csrf}
        </form>";
    }
    //Select the job rank to edit
    if ($_POST['step'] == 0) {
        $csrf = getHtmlCSRF('staff_jobrankedit');
        echo "Select the job rank you wish to edit.";
        echo "<form method='post'>
        " . dropdownJobRank('jobrank') . "
        <input type='hidden' value='1' name='step'>
        <input type='submit' value='Edit Jobrank' class='btn btn-primary'>
        {$csrf}
        <br />
        </form>";
    }
}

function jobrankdele()
{
    global $db, $userid, $h, $api;
    echo "<h3>Delete Job Rank</h3><hr />";
    if (isset($_POST['jobrank'])) {
        $_POST['jobrank'] = (isset($_POST['jobrank']) && is_numeric($_POST['jobrank'])) ? abs(intval($_POST['jobrank'])) : 0;
        //Verify CSRF
        if (!isset($_POST['verf']) || !checkCSRF('staff_deljobrank', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "We have blocked this action for your security. Please submit forms quickly.");
            die($h->endpage());
        }
        //Is job rank empty?
        if (empty($_POST['jobrank'])) {
            alert('danger', "Uh Oh!", "You have specified an invalid job rank to delete.");
            die($h->endpage());
        }
        $q = $db->query("SELECT * FROM `job_ranks` WHERE `jrID` = {$_POST['jobrank']}");
        if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "You cannot delete a non-existent job rank.");
            die($h->endpage());
        }
        $db->free_result($q);
        $q = $db->query("SELECT * FROM `jobs` WHERE `jSTART` = {$_POST['jobrank']}");
        if ($db->num_rows($q) > 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "You cannot delete a job rank which is the starter rank for a job. Please edit
            that job before attempting to delete this rank again.");
            die($h->endpage());
        }
        $db->free_result($q);
        $db->query("DELETE FROM `job_ranks` WHERE `jrID` = {$_POST['jobrank']}");
        $db->query("UPDATE `users` SET `job` = 0, `jobrank` = 0, `jobwork` = 0 WHERE `job` = {$_POST['jobrank']}");
        $unemployed = $db->affected_rows();
        alert('success', "Success!", "You have successfully deleted Job Rank ID {$_POST['jobrank']}. {$unemployed} players
         need to reapply to their job.", true, 'index.php');
    } else {
        $csrf = getHtmlCSRF('staff_deljobrank');
        echo "Please select the job rank you wish to delete. You can only delete ranks that are not a job's first rank.<br />
        <form method='post'>
            " . dropdownJobRank() . "<br />
            <input type='submit' value='Delete Job Rank' class='btn btn-primary'>
            {$csrf}
        </form>
        ";
    }
}

$h->endpage();