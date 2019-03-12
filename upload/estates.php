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
if (isset($property) && is_numeric($property)) {
	$property = filter_input(INPUT_GET, 'property', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    $npq = $db->query("SELECT * FROM `estates` WHERE `house_id` = {$property}");
    //Estate does not exist.
    if ($db->num_rows($npq) == 0) {
        $db->free_result($npq);
        alert('danger', "Uh Oh!", "The estate you are trying to purchase does not exist.", true, 'estates.php');
        die($h->endpage());
    }
    $np = $db->fetch_row($npq);
    $db->free_result($npq);
    //Estate's will is lower than user's current estate.
    if ($np['house_will'] < $mp['house_will']) {
        alert('danger', "Uh Oh!", "The house you are trying to buy is worse than what you currently have.", true, 'estates.php');
        die($h->endpage());
    } //User is trying to buy the same estate.
    else if ($np['house_will'] == $mp['house_will']) {
        alert('danger', "Uh Oh!", "You cannot buy the same house twice.", true, 'estates.php');
        die($h->endpage());
    } //User does not have enoguh primary currency for the new estatte.
    else if ($np['house_price'] > $ir['primary_currency']) {
        alert('danger', "Uh Oh!", "You do not have enough cash to buy this house.", true, 'estates.php');
        die($h->endpage());
    } //User is too low leveled for the estate.
    else if ($np['house_level'] > $ir['level']) {
        alert('danger', "Uh Oh!", "You are not a high level enough to buy this estate.", true, 'estates.php');
        die($h->endpage());
    } //User passes all checks.
    else {
        //Update user's max will, remove currency, and set will to 0.
        $db->query("UPDATE `users`
                    SET `primary_currency` = `primary_currency` - {$np['house_price']} ,
                    `will` = 0, `maxwill` = {$np['house_will']}
                    WHERE `userid` = $userid");
        alert('success', "Success!", "You have successfully bought the {$np['house_name']} estate for
            " . number_format($np['house_price']) . "!", true, 'estates.php');
        die($h->endpage());
    }
} //User wishes to sell their estate.
else if (isset($_GET['sellhouse'])) {
    //User does not own an estate.
    if ($ir['maxwill'] == 100) {
        alert('danger', "Uh Oh!", "You cannot sell your estate if you don't have one!");
    } //User sells estate.
    else {
        //Give user 75% of the estate's cost, set max will to 100, will to 0.
        $price = round($mp['house_price'] * 0.75);
        $db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + {$price}, `will` = 0, `maxwill` = 100 WHERE `userid` = $userid");
        alert('success', "Success!", "You have successfully sold your estate for " . number_format($price) . "!", true, 'estates.php');
    }
} else {
    echo "Your current estate is the <b>{$mp['house_name']}</b>.<br />
		The houses you can buy are listed below. Click a house to buy it. Your Will helps determine how much you
		gain while training, and it helps with committing crimes.<br />";
    //User own an estate.
    if ($ir['maxwill'] > 100) {
        echo "<a href='?sellhouse'>Sell Your Estate</a><br />";
    }
    $hq = $db->query("SELECT * FROM `estates` WHERE `house_will` > {$ir['maxwill']} ORDER BY `house_will` ASC");
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