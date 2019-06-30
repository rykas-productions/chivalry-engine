<?php
/*
	File:		donator.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows a player to view the currently listed VIP Packs, 
				and purchase one using Paypal.
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
require_once('globals.php');
echo "<h3>VIP Packs</h3><hr />If you purchase a VIP Package from below, you will be gifted the following depending on
    the package your purchase. All purchases are final. If you commit fraud, you will be removed from the game permanently.";
echo "<div class='cotainer'>
<div class='row'>
		<div class='col-sm'>
		    <h4>Offer</h4>
		</div>
		<div class='col-sm'>
		    <h4>Contents</h4>
		</div>
		<div class='col-sm'>
		    <h4>PayPal</h4>
		</div>
</div><hr />";
$q = $db->query("SELECT `v`.*, `i`.*
				FROM `vip_listing` `v`
				INNER JOIN `items` AS `i` 
				ON `itmid` = `vip_item`
				ORDER BY `vip_cost` ASC");
//List the donator packages.
while ($r = $db->fetch_row($q)) {
    //Put the VIP Cost in a currency number. (Ex. $1.54)
    $r['vip_cost'] = sprintf("%0.2f", $r['vip_cost']);
    $amount = ($r['vip_qty'] > 1) ? "{$r['vip_qty']} x " : '';
    echo "
	<div class='row'>
		<div class='col-sm'>
		{$amount} {$r['itmname']}<br />
			Cost: \${$r['vip_cost']} USD
		</div>
		<div class='col-sm'>
		";
    //List the item's effects.
    $iterations=count(json_decode($r['itmeffects_toggle']));
    $toggle=json_decode($r['itmeffects_toggle']);
    $stat=json_decode($r['itmeffects_stat']);
    $dir=json_decode($r['itmeffects_dir']);
    $type=json_decode($r['itmeffects_type']);
    $amount=json_decode($r['itmeffects_amount']);
    $usecount=0;
    $uhoh=0;
    while ($usecount != $iterations)
    {
        if ($toggle[$usecount] == 1)
        {
            $uhoh+=1;
            $type[$usecount] = ($type[$usecount] == 'percent') ? '%' : '';
            $dir[$usecount] = ($dir[$usecount] == 'pos') ? 'Increases' : 'Decreases';
            $stats =
                array("energy" => "Energy", "will" => "Will",
                    "brave" => "Bravery", "level" => "Level",
                    "hp" => "Health", "strength" => constant("stat_strength"),
                    "agility" => constant("stat_agility"), "guard" => constant("stat_guard"),
                    "labor" => constant("stat_labor"), "iq" => constant("stat_iq"),
                    "infirmary" => "Infirmary Time", "dungeon" => "Dungeon Time",
                    "primary_currency" => constant("primary_currency"),
                    "secondary_currency" => constant("secondary_currency"),
                    "xp" => "Experience", "vip_days" =>
                    "VIP Days");
            $statformatted = $stats["{$stat[$usecount]}"];
            echo "{$dir[$usecount]} {$statformatted} by " . number_format($amount[$usecount]) . "{$type[$usecount]}.<br />";
        }
        $usecount=$usecount+1;
    }
    if ($uhoh == 0) {
        echo $r['itmdesc'];
    }
    //The form handles a lot of the internals for the pack info.
    //You should only need to change the currency_code.
    //Proceed at your own caution.
    echo "
		</div>
		<div class='col-sm'>
			<form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
			<input type='hidden' name='cmd' value='_xclick' />
			<input type='hidden' name='business' value='{$set['PaypalEmail']}' />
			<input type='hidden' name='item_name' value='{$domain}|VIP|{$r['vip_id']}|{$userid}' />
			<input type='hidden' name='amount' value='{$r['vip_cost']}' />
			<input type='hidden' name='no_shipping' value='1' />
			<input type='hidden' name='return' value='http://{$domain}/donatordone.php?action=done' />
			<input type='hidden' name='cancel_return' value='http://{$domain}/donatordone.php?action=cancel' />
			<input type='hidden' name='notify_url' value='http://{$domain}/donator_ipn.php' />
			<input type='hidden' name='cn' value='Your Player ID' />
			<input type='hidden' name='currency_code' value='USD' />
			<input type='hidden' name='tax' value='0' />
			<input type='hidden' name='rm' value='2'>
			<input type='image' src='https://www.paypal.com/en_US/i/btn/x-click-but21.gif' border='0' name='submit' alt='Make payments with PayPal - it's fast, free and secure!' />
			</form>
		</div>
	</div>
	<hr />";
}
echo "</div><br />
VIP Days disable ads around the game. You'll also receive 16% energy refill instead of 8%. You'll also receive a star by
 your name, and your name will change color.";
$h->endpage();
