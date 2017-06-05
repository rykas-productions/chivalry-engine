<?php
/*
	File:		estates.php
	Created: 	4/5/2016 at 12:00AM Eastern Time
	Info: 		Lists the game estates and allows players to buy them
				for an increased will level. At a later date, players
				can sell them back to the game for 75% of its original
				cost.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
$mpq = $db->query("SELECT * FROM `estates` WHERE `house_will` = {$ir['maxwill']} LIMIT 1");
$mp = $db->fetch_row($mpq);
$db->free_result($mpq);
//User is trying to buy an estate.
if (isset($_GET['property']) && is_numeric($_GET['property']))
{
    $_GET['property'] = abs($_GET['property']);
    $npq = $db->query("SELECT * FROM `estates` WHERE `house_id` = {$_GET['property']}");
    //Estate does not exist.
    if ($db->num_rows($npq) == 0)
    {
        $db->free_result($npq);
		alert('danger',$lang["ERROR_GENERIC"],$lang['ESTATES_ERROR1'],true,'estates.php');
		die($h->endpage());
    }
    $np = $db->fetch_row($npq);
    $db->free_result($npq);
    //Estate's will is lower than user's current estate.
    if ($np['house_will'] < $mp['house_will'])
    {
        alert('danger',$lang["ERROR_GENERIC"],$lang['ESTATES_ERROR2'],true,'estates.php');
		die($h->endpage());
    }
    //User is trying to buy the same estate.
	else if ($np['house_will'] == $mp['house_will'])
    {
        alert('danger',$lang["ERROR_GENERIC"],$lang['ESTATES_ERROR4'],true,'estates.php');
		die($h->endpage());
    }
    //User does not have enoguh primary currency for the new estatte.
    else if ($np['house_price'] > $ir['primary_currency'])
    {
        alert('danger',$lang["ERROR_GENERIC"],$lang['ESTATES_ERROR3'],true,'estates.php');
		die($h->endpage());
    }
    //User is too low leveled for the estate.
	else if ($np['house_level'] > $ir['level'])
	{
		alert('danger',$lang["ERROR_GENERIC"],$lang['ESTATES_ERROR6'],true,'estates.php');
		die($h->endpage());
	}
    //User passes all checks.
    else
    {
        //Update user's max will, remove currency, and set will to 0.
        $db->query("UPDATE `users` SET `primary_currency` = `primary_currency` - {$np['house_price']} , `will` = 0, `maxwill` = {$np['house_will']} WHERE `userid` = $userid");
        alert('success',$lang["ERROR_SUCCESS"],"{$lang['ESTATES_SUCCESS1']} {$np['house_name']} {$lang['GEN_FOR_S']} {$np['house_price']} {$lang['INDEX_PRIMCURR']}",true,'estates.php');
		die($h->endpage());
    }
}
//User wishes to sell their estate.
else if (isset($_GET['sellhouse']))
{
    //User does not own an estate.
    if ($ir['maxwill'] == 100)
    {
        alert('danger',"{$lang["ERROR_GENERIC"]}","{$lang['ESTATES_ERROR5']}");
    }
    //User sells estate.
    else
    {
        //Give user 75% of the estate's cost, set max will to 100, will to 0.
		$price=round($mp['house_price']*0.75);
        $db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + {$price}, `will` = 0, `maxwill` = 100 WHERE `userid` = $userid");
		alert('success',$lang["ERROR_SUCCESS"],"{$lang['ESTATES_SUCCESS2']}",true,'estates.php');
    }
}
else
{
    echo "{$lang['ESTATES_START']} <b>{$mp['house_name']}</b><br />
		{$lang['ESTATES_INFO']}<br />";
    //User own an estate.
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
    //List all game's estates.
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