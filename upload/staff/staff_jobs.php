<?php
require('sglobals.php');
if ($api->UserMemberLevelGet($userid,'Admin') == false)
{
	alert('danger',"{$lang['ERROR_NOPERM']}","{$lang['GEN_NOPERM']}");
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
	global $db,$userid,$h,$lang;
	echo "<h3>{$lang['STAFF_JOB_CREATE_TITLE']}</h3><hr />";
	if (!isset($_POST['jNAME']))
	{
		$csrf = request_csrf_html('staff_newjob');
		echo "<table class='table table-bordered'>
			<tr>
				<th>
					{$lang['STAFF_JOB_CREATE_FORM_NAME']}
				</th>
				<td>
					<input type='text' name='jNAME' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_JOB_CREATE_FORM_DESC']}
				</th>
				<td>
					<input type='text' name='jDESC' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_JOB_CREATE_FORM_BOSS']}
				</th>
				<td>
					<input type='text' name='jBOSS' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th colspan='2'>
					{$lang['STAFF_JOB_CREATE_FORM_FIRST']}
				</th>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_JOB_CREATE_FORM_RNAME']}
				</th>
				<td>
					<input type='text' name='jRNAME' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_JOB_CREATE_FORM_ACT']}
				</th>
				<td>
					<input type='number' min='1' name='jACT' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th colspan='2'>
					{$lang['STAFF_JOB_CREATE_FORM_PAYS']}
				</th>
			</tr>
			<tr>
				<th>
					{$lang['INDEX_PRIMCURR']}
				</th>
				<td>
					<input type='number' min='0' name='jPRIMPAY' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['INDEX_SECCURR']}
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