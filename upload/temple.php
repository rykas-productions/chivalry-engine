<?php
require('globals.php');
echo "<h3>{$lang['TEMPLE_TITLE']}</h3><hr />";
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
	case 'energy':
		energy();
		break;
	default:
		home();
		break;
}
function home()
{
	global $lang,$db,$ir,$set;
	echo $lang['TEMPLE_INTRO'];
	echo "<br />
	<a href='?action=energy'>{$lang['TEMPLE_ENERGY']}" . number_format($set['energy_refill_cost']) . " {$lang['INDEX_SECCURR']}</a>";
}
function energy()
{
	global $db,$api,$lang,$userid,$ir,$h,$set;
	if ($api->UserHasCurrency($userid,'secondary',$set['energy_refill_cost']))
	{
		if ($api->UserInfoGet($userid,'energy',true) == 100)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['TEMPLE_ENERGY_ERR1'],true,'temple.php');
		}
		else
		{
			$api->UserInfoSet($userid,'energy',100,true);
			$api->UserTakeCurrency($userid,'secondary',$set['energy_refill_cost']);
			alert('success',$lang['ERROR_SUCCESS'],$lang['TEMPLE_ENERGY_SUCC'],true,'temple.php');
		}
	}
	else
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['TEMPLE_ENERGY_ERR'],true,'temple.php');
	}
}
$h->endpage();