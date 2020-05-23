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
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
	alert('danger',"Uh Oh!","You cannot visit the estate agent while in the infirmary or dungeon.",true,'explore.php');
	die($h->endpage());
}
$mpq = $db->query("/*qc=on*/SELECT * FROM `estates` WHERE `house_will` = {$ir['maxwill']} LIMIT 1");
$mp = $db->fetch_row($mpq);
$db->free_result($mpq);
//User is trying to buy an estate.
if (isset($_GET['property']) && is_numeric($_GET['property'])) {
    $_GET['property'] = abs($_GET['property']);
    $npq = $db->query("/*qc=on*/SELECT * FROM `estates` WHERE `house_id` = {$_GET['property']}");
    //Estate does not exist.
    if ($db->num_rows($npq) == 0) {
        $db->free_result($npq);
        alert('danger', "Uh Oh!", "The estate you are trying to purchase does not exist.", true, 'estates.php');
        die($h->endpage());
    }
    $np = $db->fetch_row($npq);
    $db->free_result($npq);
	$currentprice=$mp['house_price']*1;
	$np['house_price']=$np['house_price']-$currentprice;
    //Estate's will is lower than user's current estate.
    if ($np['house_will'] < $mp['house_will']) {
        alert('danger', "Uh Oh!", "The house you are trying to buy is worse than what you currently have.", true, 'estates.php');
        die($h->endpage());
    } //User is trying to buy the same estate.
    else if ($np['house_will'] == $mp['house_will']) {
        alert('danger', "Uh Oh!", "You cannot buy the same house twice.", true, 'estates.php');
        die($h->endpage());
    } //User does not have enoguh Copper Coins for the new estatte.
    else if ($np['house_price'] > $ir['primary_currency']) {
        alert('danger', "Uh Oh!", "You do not have enough cash to buy this house.", true, 'estates.php');
        die($h->endpage());
    } //User is too low leveled for the estate.
    else if ($np['house_level'] > $ir['level']) {
        alert('danger', "Uh Oh!", "You are not a high level enough to buy this estate.", true, 'estates.php');
        die($h->endpage());
    } 
	//User passes all checks.
    else {
        //Update user's max will, remove currency, and set will to 0.
        $db->query("UPDATE `users`
                    SET `primary_currency` = `primary_currency` - {$np['house_price']} ,
                    `will` = 0, `maxwill` = {$np['house_will']}
                    WHERE `userid` = $userid");
        alert('success', "Success!", "You have successfully bought the {$np['house_name']} estate for
            " . number_format($np['house_price']) . "!", true, 'estates.php');
			addToEconomyLog('Estates', 'copper', $np['house_price']*-1);
        die($h->endpage());
    }
} else {
    echo "Your current estate is the <b>{$mp['house_name']}</b>.<br />
		The houses you can buy are listed below. Click a house to buy it. Your Will helps determine how much you
		gain while training, and it helps with committing crimes.<br />";
    $hq = $db->query("/*qc=on*/SELECT * FROM `estates` WHERE `house_will` > {$ir['maxwill']} ORDER BY `house_will` ASC");
    echo "
	<table class='table table-bordered'>
	<tr>
		<th>
			Estate Name
		</th>
		<th>
			Level Requirement
		</th>
		<th>
			Cost
		</th>
		<th>
			Will Level
		</th>
	</tr>";
    //List all game's estates.
    while ($r = $db->fetch_row($hq)) {
		$currentprice=$mp['house_price'];
		$r['house_price']=$r['house_price']-$currentprice;
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
    echo "</table>";
    $db->free_result($hq);
}
$h->endpage();