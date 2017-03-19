<?php
require('sglobals.php');
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case "addrule":
    addrule();
    break;
case "editrule":
    editrule();
    break;
case "delrule":
    delrule();
    break;
default:
    die();
    break;
}
function addrule()
{
	global $db,$userid,$lang,$api,$h;
	if (!isset($_POST['rule']))
	{
		$csrf=request_csrf_html('staff_addrule');
		echo "{$lang['STAFF_RULES_ADD_FORM']}<br />
		<form method='post'>
			<textarea name='rule' rows='5' class='form-control'></textarea>
			<input type='submit' class='btn btn-default' value='{$lang['STAFF_RULES_ADD_BTN']}'>
			{$csrf}
		</form>";
	}
	else
	{
		if (empty($_POST['rule']))
		{
			alert('danger',$lang['ERROR_EMPTY'],$lang['STAFF_RULES_ADD_SUBFAIL']);
			die($h->endpage());
		}
		else
		{
			if (!isset($_POST['verf']) || !verify_csrf_code('staff_addrule', stripslashes($_POST['verf'])))
			{
				alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
				die($h->endpage());
			}
			$time=time();
			$rule = $db->escape(str_replace("\n", "<br />",strip_tags(stripslashes($_POST['rule']))));
			$db->query("INSERT INTO `gamerules` (`rule_id`, `rule_text`) VALUES (NULL, '{$rule}');");
			alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_RULES_ADD_SUBSUCC']);
			$api->SystemLogsAdd($userid,'staff',"Created a new rule.");
		}
	}
}
$h->endpage();