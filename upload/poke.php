<?php
/*
	File:		poke.php
	Created: 	4/5/2016 at 12:21AM Eastern Time
	Info: 		Allows players to poke other players for fun.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : '';
if (empty($_GET['user']))
{
	alert('danger',$lang['ERROR_GENERIC'],$lang['POKE_ERROR1'],true,'index.php');
	die($h->endpage());
}
if (($_GET['user']) == $userid)
{
	alert('danger',$lang['ERROR_GENERIC'],$lang['POKE_ERROR2'],true,"profile.php?user={$_GET['user']}");
	die($h->endpage());
}
$q=$db->query("SELECT `username` FROM `users` WHERE `userid` = {$_GET['user']}");
if ($db->num_rows($q) == 0)
{
	alert('danger',$lang['ERROR_GENERIC'],$lang['POKE_ERROR3'],true,"index.php");
	die($h->endpage());
}
if (isset($_POST['do']))
{
	if (!isset($_POST['verf']) || !verify_csrf_code('poke', stripslashes($_POST['verf'])))
	{
		alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
		die($h->endpage());
	}
	alert('success',$lang['ERROR_SUCCESS'],$lang['POKE_SUCC'],true,"profile.php?user={$_GET['user']}");
	$api->SystemLogsAdd($userid,'pokes',"Poked " . $api->SystemUserIDtoName($_GET['user']) . "[{$_GET['user']}]");
	$api->GameAddNotification($_GET['user'],"You have been poked by <a href='profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}].");
}
else
{
	echo "{$lang['POKE_TITLE']} " . $api->SystemUserIDtoName($_GET['user']) . "{$lang['POKE_TITLE1']}";
	$csrf=request_csrf_html('poke');
	?>
	<form method='post'>
		<input type='submit' value='<?php echo $lang['POKE_BTN']; ?>' class='btn btn-secondary'>
		<input type='hidden' name='do' value='yes'>
		<?php echo $csrf; ?>
	</form>
	<?php
}
$h->endpage();