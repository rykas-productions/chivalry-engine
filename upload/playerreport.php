<?php
require("globals.php");
function csrf_error($goBackTo)
{
    global $h,$lang;
	echo "<div class='alert alert-danger'> <strong>{$lang['CSRF_ERROR_TITLE']}</strong> 
	{$lang['CSRF_ERROR_TEXT']} {$lang['CSRF_PREF_MENU']} <a href='playerreport.php'>{$lang['GEN_HERE']}.</div>";
    $h->endpage();
    exit;
}
echo "<h3>{$lang['PR_TITLE']}</h3><hr />";
if (empty($_POST['userid']))
{
	$code = request_csrf_code('report_form');
	echo "{$lang['PR_INTRO']}<br />
	 <form method='post'>
	 <table class='table table-bordered'>
		<tr>
			<th>
				{$lang['PR_USER']}
			</th>
			<td>
				<input type='number' min='1' required='1' name='userid' placeholder='{$lang['PR_USER_PH']}' class='form-control'>
			</td>
		</tr>
		<tr>
			<th>
				{$lang['PR_CATEGORY']}
			</th>
			<td>
				<select name='category' class='form-control'>
					<option value='bugabuse'>{$lang['PR_CAT_1']}</option>
					<option value='harassment'>{$lang['PR_CAT_2']}</option>
					<option value='scamming'>{$lang['PR_CAT_3']}</option>
					<option value='spamming'>{$lang['PR_CAT_4']}</option>
					<option value='erb'>{$lang['PR_CAT_5']}</option>
					<option value='security'>{$lang['PR_CAT_6']}</option>
					<option value='other'>{$lang['PR_CAT_7']}</option>
				</input>
			</td>
		</tr>
		<tr>
			<th>
				{$lang['PR_REASON']}
			</th>
			<td>
				<textarea class='form-control' required='1' maxlength='1250' name='reason' rows='5' placeholder='{$lang['PR_REASON_PH']}'></textarea>
			</td>
		</tr>
		<tr>
			
			<td colspan='2'>
				<input type='submit' value='{$lang['FB_PR']}' class='btn btn-default'>
			</td>
		</tr>
	</table>
	<input type='hidden' name='verf' value='{$code}' />
	</form>";
}
else
{
	$CategoryArray=["bugabuse","harassment","scamming","spamming","erb","security","other"];
	$_POST['reason'] =  (isset($_POST['reason']) && is_string($_POST['reason'])) ? $db->escape(strip_tags(stripslashes($_POST['reason']))) : '';
	$_POST['userid'] =  (isset($_POST['userid']) && is_numeric($_POST['userid'])) ? abs($_POST['userid']) : '';
	if (!isset($_POST['verf']) || !verify_csrf_code('report_form', stripslashes($_POST['verf'])))
	{
		csrf_error('');
	}
	if (!in_array($_POST['category'],$CategoryArray))
	{
		alert('danger',"{$lang['ERROR_INVALID']}","{$lang['PR_CATBAD']}");
	}
	else
	{
		if (strlen($_POST['reason']) > 1250)
		{
			alert('danger',"{$lang['ERROR_LENGTH']}","{$lang['PR_MAXCHAR']}");
			die($h->endpage());
		}
		$q = $db->query("SELECT COUNT(`userid`) FROM `users` WHERE `userid` = {$_POST['userid']}");
		if ($db->fetch_single($q) == 0)
		{
			$db->free_result($q);
			alert('danger',"{$lang['ERROR_NONUSER']}","{$lang['PR_INVALID_USER']}");
			die($h->endpage());
		}
		$db->free_result($q);
		$db->query("INSERT INTO `reports` VALUES(NULL, $userid, {$_POST['userid']}, '{$_POST['reason']}')");
		alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['PR_SUCCESS']}");
		
	}
}
$h->endpage();