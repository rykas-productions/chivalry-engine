<?php
require('globals.php');
if (!$ir['guild'])
{
    alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['VIEWGUILD_ERROR1']}");
}
else
{
	$gq = $db->query("SELECT * FROM `guild` WHERE `guild_id` = {$ir['guild']}");
    if ($db->num_rows($gq) == 0)
    {
        alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['VIEWGUILD_ERROR2']}");
        die($h->endpage());
    }
    $gd = $db->fetch_row($gq);
    $db->free_result($gq);
    echo "
	<h3><u>{$lang['VIEWGUILD_TITLE']} {$gd['guild_name']}.</u></h3>
   	";
	if (!isset($_GET['action']))
	{
		$_GET['action'] = '';
	}
	switch ($_GET['action'])
	{
	case 'summary':
		summary();
		break;
	case 'donate':
		donate();
		break;
	default:
		home();
		break;
	}
}
function home()
{
	global $db,$userid,$ir,$gd,$lang,$api;
	echo "
    <table class='table table-bordered'>
    		<tr>
    			<td><a href='?action=summary'>{$lang['VIEWGUILD_HOME_SUMMARY']}</a></td>
    			<td><a href='?action=donate'>{$lang['VIEWGUILD_HOME_DONATE']}</a></td>
    		</tr>
    		<tr>
    			<td><a href='?action=members'>{$lang['VIEWGUILD_HOME_USERS']}</a></td>
    			<td><a href='?action=crimes'>{$lang['VIEWGUILD_HOME_CRIME']}</a></td>
    		</tr>
    		<tr>
    			<td><a href='?action=leave'>{$lang['VIEWGUILD_HOME_LEAVE']}</a></td>
				<td><a href='?action=atklogs'>{$lang['VIEWGUILD_HOME_ATKLOG']}</a></td>
    		</tr>
    		<tr>
    			<td><a href='?action=armory'>{$lang['VIEWGUILD_HOME_ARMORY']}</a></td>
    			<td>
       ";
    if ($gd['guild_owner'] == $userid || $gd['guild_coowner'] == $userid)
    {
        echo "<a href='?action=staff&act2=idx'>{$lang['VIEWGUILD_HOME_STAFF']}</a>";
    }
    else
    {
        echo "&nbsp;";
    }
    echo "
				</td>
			</tr>
	</table>
	<br />
	<table class='table table-bordered'>
		<tr>
			<td>{$lang['VIEWGUILD_HOME_ANNOUNCE']}</td>
		</tr>
		<tr>
			<td>{$gd['guild_announcement']}</td>
		</tr>
	</table>
	<br />
	<b>{$lang['VIEWGUILD_HOME_EVENT']}</b>
	<br />
   	";
    $q = $db->query("SELECT * FROM `guild_notifications` WHERE `gn_guild` = {$ir['guild']} ORDER BY `gn_time` DESC  LIMIT 10");
    echo "
	<table class='table table-bordered'>
		<tr>
			<th>{$lang['VIEWGUILD_HOME_EVENTTIME']}</th>
			<th>{$lang['VIEWGUILD_HOME_EVENTTEXT']}</th>
		</tr>
   	";
    while ($r = $db->fetch_row($q))
    {
        echo "
		<tr>
			<td>" . date('F j Y, g:i:s a', $r['gn_time'])
                . "</td>
			<td>{$r['gn_text']}</td>
		</tr>
   		";
    }
    $db->free_result($q);
    echo "</table>";
}
function summary()
{
	global $db,$userid,$ir,$gd,$lang,$api;
	echo "
	<table class='table table-bordered'>
	<tr>
		<th colspan='2'>
			{$lang['VIEWGUILD_SUMMARY_TITLE']}
		</th>
	</tr>
	<tr>
		<th>
			{$lang['VIEWGUILD_SUMMARY_OWNER']}
		</th>
		<td>
       ";
    $pq = $db->query("SELECT `username` FROM `users` WHERE `userid` = {$gd['guild_owner']}");
    if ($db->num_rows($pq) > 0)
    {
        $ldrnm = $db->fetch_single($pq);
        echo "<a href='profile.php?user={$gd['guild_owner']}'> {$ldrnm} </a>";
    }
    else
    {
        echo "{$lang['VIEWGUILD_NA']}";
    }
	echo"</td>
	</tr>
	<tr>
		<th>
			{$lang['VIEWGUILD_SUMMARY_COOWNER']}
		</th>
		<td>";
    $db->free_result($pq);
    $vpq = $db->query("SELECT `username` FROM `users` WHERE `userid` = {$gd['guild_coowner']}");
    if ($db->num_rows($vpq) > 0)
    {
        $vldrnm = $db->fetch_single($vpq);
        echo "<a href='profile.php?user={$gd['guild_coowner']}'> {$vldrnm} </a>";
    }
    else
    {
        echo "{$lang['VIEWGUILD_NA']}";
    }
	echo"</td>
	</tr>";
    $db->free_result($vpq);
    $cnt = $db->query("SELECT COUNT(`userid`) FROM `users` WHERE `guild` = {$gd['guild_id']}");
    echo "
	<tr>
		<th>
			{$lang['VIEWGUILD_SUMMARY_MEM']}
		</th>
		<td>
			" . $db->fetch_single($cnt) . "/ {$gd['guild_capacity']}
		</td>
	</tr>
	<tr>
		<th>
			{$lang['VIEWGUILD_SUMMARY_LVL']}
		</th>
		<td>
			{$gd['guild_level']}
		</td>
	</tr>
	<tr>
		<th>
			{$lang['INDEX_PRIMCURR']}
		</th>
		<td>
			" . number_format($gd['guild_primcurr']) . "
		</td>
	</tr>
	<tr>
		<th>
			{$lang['INDEX_SECCURR']}
		</th>
		<td>
			" . number_format($gd['guild_seccurr']) . "
		</td>
	</tr>
      </table>";
}
function donate()
{
	global $db,$userid,$ir,$gd,$lang,$api,$h;
	if (isset($_POST['primary']))
	{
		$_POST['primary'] = (isset($_POST['primary']) && is_numeric($_POST['primary'])) ? abs(intval($_POST['primary'])) : 0;
		$_POST['secondary'] = (isset($_POST['secondary']) && is_numeric($_POST['secondary'])) ? abs(intval($_POST['secondary'])) : 0;
		if (!isset($_POST['verf']) || !verify_csrf_code('guild_donate', stripslashes($_POST['verf'])))
		{
			alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
			die($h->endpage());
		}
		if (empty($_POST['primary']) && empty($_POST['secondary']))
		{
			alert('danger',"{$lang["ERROR_GENERIC"]}","{$lang['VIEWGUILD_DONATE_ERR1']}");
			die($h->endpage());
		}
		if ($_POST['primary'] > $ir['primary_currency'])
		{
			alert('danger',"{$lang["ERROR_GENERIC"]}","{$lang['VIEWGUILD_DONATE_ERR2']}");
			die($h->endpage());
		}
		else if ($_POST['secondary'] > $ir['secondary_currency'])
		{
			alert('danger',"{$lang["ERROR_GENERIC"]}","{$lang['VIEWGUILD_DONATE_ERR3']}");
			die($h->endpage());
		}
		else
		{
			$db->query("UPDATE `users`
                 SET `primary_currency` = `primary_currency` - {$_POST['primary']},
                 `secondary_currency` = `secondary_currency` - {$_POST['secondary']}
                 WHERE `userid` = {$userid}");
			$db->query("UPDATE `guild` 
					SET `guild_primcurr` = `guild_primcurr` + {$_POST['primary']},
					`guild_seccurr` = `guild_seccurr` + {$_POST['secondary']}
					WHERE `guild_id` = {$gd['guild_id']}");
			$my_name = htmlentities($ir['username'], ENT_QUOTES, 'ISO-8859-1');
			$event =$db->escape("<a href='profile.php?user={$userid}'>{$my_name}</a> donated 
									" . number_format($_POST['primary']) . " Primary Currency
								and " . number_format($_POST['secondary']) . " Secondary Currency to the guild.");
			 $db->query("INSERT INTO `guild_events`
						VALUES(NULL, {$gd['guild_id']}, " . time() . ", '{$event}')");
		alert('success',"{$lang["ERROR_SUCCESS"]}","{$lang['VIEWGUILD_DONATE_SUCC']}");
		}
	}
	else
	{
		$csrf = request_csrf_html('guild_donate');
		echo "
		<form action='?action=donate' method='post'>
			<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					{$lang['VIEWGUILD_DONATE_TITLE']} " . number_format($ir['primary_currency']) . "
					{$lang['INDEX_PRIMCURR']} and " . number_format($ir['secondary_currency']) . " {$lang['INDEX_SECCURR']}
				</th>
			</tr>
    		<tr>
    			<td>
    				<b>{$lang['INDEX_PRIMCURR']}</b><br />
    				<input type='number' name='primary' value='0' required='1' class='form-control' min='0' />
    			</td>
    			<td>
    				<b>{$lang['INDEX_SECCURR']}</b><br />
    				<input type='number' name='secondary' required='1' class='form-control' value='0' min='0' />
    			</td>
    		</tr>
    		<tr>
    			<td colspan='2' align='center'>
    			    {$csrf}
    				<input type='submit' class='btn btn-default' value='{$lang['VIEWGUILD_DONATE_BTN']}' />
    			</td>
    		</tr>
    	</table>
		</form>";
	}
}
$h->endpage();