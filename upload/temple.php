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
	case 'brave':
		brave();
		break;
	case 'will':
		will();
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
	<a href='?action=energy'>{$lang['TEMPLE_ENERGY']}" . number_format($set['energy_refill_cost']) . " {$lang['INDEX_SECCURR']}</a><br />
	<a href='?action=brave'>{$lang['TEMPLE_BRAVE']}" . number_format($set['brave_refill_cost']) . " {$lang['INDEX_SECCURR']}</a><br />
	<a href='?action=will'>{$lang['TEMPLE_WILL']}" . number_format($set['will_refill_cost']) . " {$lang['INDEX_SECCURR']}</a><br />";
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
function brave()
{
	global $db,$api,$lang,$userid,$ir,$h,$set;
	if ($api->UserHasCurrency($userid,'secondary',$set['brave_refill_cost']))
	{
		if ($api->UserInfoGet($userid,'brave',true) == 100)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['TEMPLE_BRAVE_ERR1'],true,'temple.php');
		}
		else
		{
			$api->UserInfoSet($userid,'brave',5,true);
			$api->UserTakeCurrency($userid,'secondary',$set['brave_refill_cost']);
			alert('success',$lang['ERROR_SUCCESS'],$lang['TEMPLE_BRAVE_SUCC'],true,'temple.php');
		}
	}
	else
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['TEMPLE_BRAVE_ERR'],true,'temple.php');
	}
}
function will()
{
	global $db,$api,$lang,$userid,$ir,$h,$set;
	if ($api->UserHasCurrency($userid,'secondary',$set['will_refill_cost']))
	{
		if ($api->UserInfoGet($userid,'will',true) == 100)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['TEMPLE_WILL_ERR1'],true,'temple.php');
		}
		else
		{
			$api->UserInfoSet($userid,'will',5,true);
			$api->UserTakeCurrency($userid,'secondary',$set['will_refill_cost']);
			alert('success',$lang['ERROR_SUCCESS'],$lang['TEMPLE_WILL_SUCC'],true,'temple.php');
		}
	}
	else
	{
		alert('danger',$lang['ERROR_GENERIC'],$lang['TEMPLE_WILL_ERR'],true,'temple.php');
	}
}
$h->endpage();