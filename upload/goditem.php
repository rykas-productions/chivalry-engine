<?php
require('globals.php');
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'level':
        level();
        break;
    default:
        home();
        break;
}
if (!($api->UserHasItem($userid,320,1))
{
	alert('danger',"Uh Oh!","You do not have the required item.",true,'inventory.php');
	die($h->endpage());
}
function home()
{
	echo "Available actions:<br />
	<a href='?action=level'>Set Level</a>";
}

function level()
{
	global $userid, $db, $ir;
	if (isset($_POST['level']))
	{
		
	}
	else
	{
		echo "<form method='post'>
			<input type='number' required='1' class='form-control' value='{$ir['level']}' name='level'>
			<input type='submit' class='btn btn-primary' value='Change Level'>
		</form>";
	}
}