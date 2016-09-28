<?php
/*
	File: staff/staff_polling.php
	Created: 9/27/2016 at 8:41PM Eastern Time
	Info: Allows staff to create in-game polls.
	Author: TheMasterGeneral
	Website: http://mastergeneral156.pcriot.com/
*/
require('sglobals.php');
echo "<h3>{$lang['STAFF_POLL_TITLE']}</h3><hr />";
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case "addpoll":
    add();
    break;
case "closepoll":
    close();
    break;
default:
    home();
    break;
}
function home()
{
	global $lang,$h;
	echo "
	<table class='table table-bordered'>
		<tr>
			<td>
				<a href='?action=addpoll'>{$lang['STAFF_POLL_TITLES']}</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='?action=closepoll'>{$lang['STAFF_POLL_TITLEE']}</a>
			</td>
		</tr>
	</table>";
	$h->endpage();
}
function add()
{
	global $db,$lang,$h;
	if (isset($_POST['question']))
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_startpoll', stripslashes($_POST['verf'])))
		{
			alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
			die($h->endpage());
		}
		$question =  (isset($_POST['question'])) ? $db->escape(strip_tags(stripslashes($_POST['question']))) : '';
		$choice1 = (isset($_POST['choice1'])) ? $db->escape(strip_tags(stripslashes($_POST['choice1']))) : '';
		$choice2 = (isset($_POST['choice2'])) ? $db->escape(strip_tags(stripslashes($_POST['choice2']))) : '';
		$choice3 = (isset($_POST['choice3'])) ? $db->escape(strip_tags(stripslashes($_POST['choice3']))) : '';
		$choice4 = (isset($_POST['choice4'])) ? $db->escape(strip_tags(stripslashes($_POST['choice4']))) : '';
		$choice5 = (isset($_POST['choice5'])) ? $db->escape(strip_tags(stripslashes($_POST['choice5']))) : '';
		$choice6 = (isset($_POST['choice6'])) ? $db->escape(strip_tags(stripslashes($_POST['choice6']))) : '';
		$choice7 = (isset($_POST['choice7'])) ? $db->escape(strip_tags(stripslashes($_POST['choice7']))) : '';
		$choice8 = (isset($_POST['choice8'])) ? $db->escape(strip_tags(stripslashes($_POST['choice8']))) : '';
		$choice9 = (isset($_POST['choice9'])) ? $db->escape(strip_tags(stripslashes($_POST['choice9']))) : '';
		$choice10 = (isset($_POST['choice10'])) ? $db->escape(strip_tags(stripslashes($_POST['choice10']))) : '';
		$hidden = (isset($_POST['hidden']) && is_numeric($_POST['hidden'])) ? abs(intval($_POST['hidden'])) : '';
		if (empty($question) || empty($choice1) || empty($choice2))
		{
			alert('danger',"{$lang['ERROR_EMPTY']}","{$lang['STAFF_POLL_START_ERROR']}");
			die($h->endpage());
		}
		$db->query("INSERT INTO `polls` (`active`, `question`, `choice1`, 
					`choice2`, `choice3`,`choice4`, `choice5`, `choice6`, 
					`choice7`, `choice8`,`choice9`, `choice10`, `hidden`)
                     VALUES
					 ('1', '$question', '$choice1', '$choice2',
                     '$choice3', '$choice4', '$choice5', '$choice6',
                     '$choice7', '$choice8', '$choice9' ,'$choice10',
                     '{$_POST['hidden']}')");
		alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['STAFF_POLL_START_SUCCESS']}");
		stafflog_add("Started a game poll.");
		$q=$db->query("SELECT `userid`, `username` FROM `users`");
		while ($r = $db->fetch_row($q))
		{
			event_add($r['userid'],"The game administration has added a poll for you to vote in. Please do so by visiting <a href='polling.php'>here</a>.");
		}
		die($h->endpage());
	}
	else
	{
		echo $lang['STAFF_POLL_START_INFO'];
		$csrf = request_csrf_html('staff_startpoll');
		echo "<hr />
		<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th width='33%'>
					{$lang['STAFF_POLL_START_QUESTION']}
				</th>
				<td>
					<input type='text' required='1' class='form-control' name='question' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_POLL_START_CHOICE']}1
				</th>
				<td>
					<input type='text' required='1' class='form-control' name='choice1' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_POLL_START_CHOICE']}2
				</th>
				<td>
					<input type='text' required='1' class='form-control' name='choice2' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_POLL_START_CHOICE']}3
				</th>
				<td>
					<input type='text' class='form-control' name='choice3' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_POLL_START_CHOICE']}4
				</th>
				<td>
					<input type='text' class='form-control' name='choice4' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_POLL_START_CHOICE']}5
				</th>
				<td>
					<input type='text' class='form-control' name='choice5' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_POLL_START_CHOICE']}6
				</th>
				<td>
					<input type='text' class='form-control' name='choice6' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_POLL_START_CHOICE']}7
				</th>
				<td>
					<input type='text' class='form-control' name='choice7' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_POLL_START_CHOICE']}8
				</th>
				<td>
					<input type='text' class='form-control' name='choice8' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_POLL_START_CHOICE']}9
				</th>
				<td>
					<input type='text' class='form-control' name='choice9' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_POLL_START_CHOICE']}10
				</th>
				<td>
					<input type='text' class='form-control' name='choice10' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_POLL_START_HIDE']}
				</th>
				<td>
					<select name='hidden' class='form-control' type='dropdown'>
						<option value='0'>{$lang['GEN_NO']}</option>
						<option value='1'>{$lang['GEN_YES']}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-default' value='{$lang['STAFF_POLL_START_BUTTON']}'>
				</td>
			</tr>
		</table>
		{$csrf}
		</form>";
	}
}
function close()
{
	global $db,$lang,$h;
	$_POST['poll'] = (isset($_POST['poll']) && is_numeric($_POST['poll'])) ? abs(intval($_POST['poll'])) : '';
    if (empty($_POST['poll']))
    {
        $csrf = request_csrf_html('staff_endpoll');
        echo "
        {$lang['STAFF_POLL_END_FORM']}
        <br />
        <form method='post'>
           ";
        $q =
                $db->query(
                        "SELECT `id`, `question`
                         FROM `polls`
                         WHERE `active` = '1'");
		echo "<select name='poll' class='form-control' type='dropdown'>";
        while ($r = $db->fetch_row($q))
        {
			echo "<option value='{$r['id']}'>Poll ID: {$r['id']} - {$r['question']}</option>";
        }
        $db->free_result($q);
        echo "</select>" . $csrf . "
			<br /><input type='submit' class='btn btn-default' value='{$lang['STAFF_POLL_END_BTN']}' />
		</form>
   		";
		$h->endpage();
    }
    else
    {
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_endpoll', stripslashes($_POST['verf'])))
		{
			alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
			die($h->endpage());
		}
        $q = $db->query("SELECT COUNT(`id`) FROM `polls` WHERE `id` = {$_POST['poll']}");
        if ($db->fetch_single($q) == 0)
        {
            $db->free_result($q);
            alert('danger',"{$lang['ERROR_GEN']}","{$lang['STAFF_POLL_END_ERR']}");
            die($h->endpage());
        }
        $db->free_result($q);
        $db->query("UPDATE `polls` SET `active` = '0' WHERE `id` = {$_POST['poll']}");
        alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['STAFF_POLL_END_SUCCESS']}");
		stafflog_add("Closed a game poll.");
		$q=$db->query("SELECT `userid`, `username` FROM `users`");
		while ($r = $db->fetch_row($q))
		{
			event_add($r['userid'],"The game administration has closed a recent poll. View the results <a href='polling.php?action=viewpolls'>here</a>.");
		}
        die($h->endpage());
    }
}