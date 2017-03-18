<?php
require('globals.php');
$mpq = $db->query("SELECT * FROM `estates` WHERE `house_will` = {$ir['maxwill']} LIMIT 1");
$mp = $db->fetch_row($mpq);
$db->free_result($mpq);
if (isset($_GET['property']) && is_numeric($_GET['property']))
{
    $_GET['property'] = abs($_GET['property']);
    $npq = $db->query("SELECT * FROM `estates` WHERE `house_id` = {$_GET['property']}");
    if ($db->num_rows($npq) == 0)
    {
        $db->free_result($npq);
		alert('danger',"{$lang["ERROR_GENERIC"]}","{$lang['ESTATES_ERROR1']}");
		die($h->endpage());
    }
    $np = $db->fetch_row($npq);
    $db->free_result($npq);
    if ($np['house_will'] < $mp['house_will'])
    {
        alert('danger',"{$lang["ERROR_GENERIC"]}","{$lang['ESTATES_ERROR2']}");
		die($h->endpage());
    }
	else if ($np['house_will'] == $mp['house_will'])
    {
        alert('danger',"{$lang["ERROR_GENERIC"]}","{$lang['ESTATES_ERROR4']}");
		die($h->endpage());
    }
    else if ($np['house_price'] > $ir['primary_currency'])
    {
        alert('danger',"{$lang["ERROR_GENERIC"]}","{$lang['ESTATES_ERROR3']}");
		die($h->endpage());
    }
	else if ($np['house_level'] > $ir['level'])
	{
		alert('danger',"{$lang["ERROR_GENERIC"]}","{$lang['ESTATES_ERROR6']}");
		die($h->endpage());
	}
    else
    {
        $db->query("UPDATE `users` SET `primary_currency` = `primary_currency` - {$np['house_price']} , `will` = 0, `maxwill` = {$np['house_will']} WHERE `userid` = $userid");
        alert('success',"{$lang["ERROR_SUCCESS"]}","{$lang['ESTATES_SUCCESS1']} {$np['house_name']} {$lang['GEN_FOR_S']} {$np['house_price']} {$lang['INDEX_PRIMCURR']}");
		die($h->endpage());
    }
}
else if (isset($_GET['sellhouse']))
{
    if ($ir['maxwill'] == 100)
    {
        alert('danger',"{$lang["ERROR_GENERIC"]}","{$lang['ESTATES_ERROR5']}");
    }
    else
    {
		$price=round($mp['house_price']*0.75);
        $db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + {$price}, `will` = 0, `maxwill` = 100 WHERE `userid` = $userid");
		alert('success',"{$lang["ERROR_SUCCESS"]}","{$lang['ESTATES_SUCCESS2']}");
    }
}
else
{
    echo "{$lang['ESTATES_START']} <b>{$mp['house_name']}</b><br />
		{$lang['ESTATES_INFO']}<br />";
    if ($ir['maxwill'] > 100)
    {
        echo "<a href='?sellhouse'>{$lang['ESTATES_SELL']}</a><br />";
    }
    $hq = $db->query("SELECT * FROM `estates` WHERE `house_will` > {$ir['maxwill']} ORDER BY `house_will` ASC");
	echo "
	<table class='table table-bordered'>
	<tr>
		<th>
			{$lang['ESTATES_TABLE1']}
		</th>
		<th>
			{$lang['ESTATES_TABLE2']}
		</th>
		<th>
			{$lang['ESTATES_TABLE3']}
		</th>
		<th>
			{$lang['ESTATES_TABLE4']}
		</th>
	</tr>";
    while ($r = $db->fetch_row($hq))
    {
		echo "
		<tr>
			<td>
				<a href='?property={$r['house_id']}'>{$r['house_name']}</a>
			</td>
			<td>
				" . number_format($r['house_level']) . "
			</td>
			<td>
				" . number_format($r['house_price']) . "
			</td>
			<td>
				" . number_format($r['house_will']) . "
			</td>
		</tr>";
    }
	echo"</table>";
    $db->free_result($hq);
}
$h->endpage();