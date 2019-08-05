<?php
/*
	File:		estates.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows players to buy and sell estates, which increase 
				their will, allowing better gains at the gym.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
	MIT License

	Copyright (c) 2019 TheMasterGeneral

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
*/
require('globals.php');
$mpq = $db->query("SELECT * FROM `estates` WHERE `house_will` = {$ir['maxwill']} LIMIT 1");
$mp = $db->fetch_row($mpq);
$db->free_result($mpq);
//User is trying to buy an estate.
if (isset($_GET['property']) && is_numeric($_GET['property'])) {
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
        $price = $mp['house_price'];
        $db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + {$price}, `will` = 0, `maxwill` = 100 WHERE `userid` = $userid");
        alert('success', "Success!", "You have successfully sold your estate for " . number_format($price) . " " . constant("primary_currency") . "!", true, 'estates.php');
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
	<div class='cotainer'>
        <div class='row'>
            <div class='col-sm'>
                <h4>Estate</h4>
            </div>
            <div class='col-sm'>
                <h4>Level</h4>
            </div>
            <div class='col-sm'>
                <h4>Cost</h4>
            </div>
    </div>
    <hr />";
    //List all game's estates.
    while ($r = $db->fetch_row($hq)) {
        echo "
		<div class='row'>
			<div class='col-sm'>
				<a href='?property={$r['house_id']}'>{$r['house_name']}</a><br />
				Will: " . number_format($r['house_will']) . "
			</div>
			<div class='col-sm'>
				" . number_format($r['house_level']) . "
			</div>
			<div class='col-sm'>
				" . number_format($r['house_price']) . "
			</div>
		</div>
		<hr />";
    }
    echo "</div>";
    $db->free_result($hq);
}
$h->endpage();