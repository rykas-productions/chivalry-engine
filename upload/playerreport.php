<?php
/*
	File:		playerreport.php
	Created: 	4/5/2016 at 12:21AM Eastern Time
	Info: 		Allows players to report other players secretly.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
function csrf_error()
{
    global $h;
    alert('danger', "Action Blocked!", "The action you were trying to do was blocked. It was blocked because you loaded
        another page on the game. If you have not loaded a different page during this time, change your password
        immediately, as another person may have access to your account!");
    die($h->endpage());
}

echo "<h3><i class='far fa-flag'></i> Player Report</h3><hr />";
if (empty($_POST['userid'])) {
	$_GET['userid'] = (isset($_GET['userid']) && is_numeric($_GET['userid'])) ? abs($_GET['userid']) : '';
    $code = request_csrf_code('report_form');
    echo "Know someone who broke the rules, or is just being dishonorable? This is the place to report them. Report the
        user just once. Reporting the same user multiple times will slow down the process. If you are found to be
        abusing the player report system, you will be placed away in federal jail. Information you enter here will
        remain confidential and will only be read by senior staff members. If you wish to confess to a crime, this is
        also a great place to do so.<br />
	 <form method='post'>
	 <table class='table table-bordered'>
		<tr>
			<th>
				User
			</th>
			<td>
				" . user_dropdown('userid',$_GET['userid']) . "
			</td>
		</tr>
		<tr>
			<th>
			    Report Text
			</th>
			<td>
				<textarea class='form-control' required='1' maxlength='1250' name='reason' rows='5'></textarea>
			</td>
		</tr>
		<tr>
			
			<td colspan='2'>
				<input type='submit' value='Submit Report' class='btn btn-primary'>
			</td>
		</tr>
	</table>
	<input type='hidden' name='verf' value='{$code}' />
	</form>";
} else {
    $_POST['reason'] = (isset($_POST['reason']) && is_string($_POST['reason'])) ? $db->escape(strip_tags(stripslashes($_POST['reason']))) : '';
    $_POST['userid'] = (isset($_POST['userid']) && is_numeric($_POST['userid'])) ? abs($_POST['userid']) : '';
    if (!isset($_POST['verf']) || !verify_csrf_code('report_form', stripslashes($_POST['verf']))) {
        csrf_error();
    }
    if (strlen($_POST['reason']) > 1250) {
        alert('danger', "Uh Oh!", "Player reports can only be, at maximum, 1,250 characters in length.");
        die($h->endpage());
    }
    $q = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `userid` = {$_POST['userid']}");
    if ($db->fetch_single($q) == 0) {
        $db->free_result($q);
        alert('danger', "Uh Oh!", "You are trying to report a non-existent user.");
        die($h->endpage());
    }
    $db->free_result($q);
	$adminq=$db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `user_level` = 'Admin'");
	while ($adminr=$db->fetch_row($adminq))
	{
		$api->GameAddNotification($adminr['userid'],"A player report has been filed and submitted. Please read it <a href='staff/staff_users.php?action=reports'>here</a>.");
	}
	$assist=$db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `user_level` = 'Assistant'");
	while ($asr=$db->fetch_row($assist))
	{
		$api->GameAddNotification($asr['userid'],"A player report has been filed and submitted. Please read it <a href='staff/staff_users.php?action=reports'>here</a>.");
	}
    $db->query("INSERT INTO `reports` VALUES(NULL, $userid, {$_POST['userid']}, '{$_POST['reason']}')");
    alert('success', "Success!", "You have successfully reported the user. Staff may send you a message asking
		    questions about the report you just sent. Please answer them to the best of your ability.", true, 'index.php');

}
$h->endpage();