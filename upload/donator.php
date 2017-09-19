<?php
/*
	File:		donator.php
	Created: 	4/4/2016 at 11:57PM Eastern Time
	Info: 		Lists the currently setup donator packages for players to
				purchase using Paypal.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require_once('globals.php');
echo "<h3>VIP Packs</h3><hr />If you purchase a VIP Package from below, you will be gifted the following depending on
    the package your purchase. All purchases are final. If you commit fraud, you will be removed from the game permanently.";
echo "
<table class='table table-bordered'>
	<tr>
		<th>
			Pack Offer
		</th>
		<th>
			Pack Contents
		</th>
		<th width='25%'>
			PayPal Link
		</th>
	</tr>";
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
	<tr>
		<td>
		{$amount} {$r['itmname']}<br />
			Cost: \${$r['vip_cost']} USD
		</td>
		<td>
		";
    $uhoh = 0;
    //List the item's effects.
    for ($enum = 1; $enum <= 3; $enum++) {
        if ($r["effect{$enum}_on"] == 'true') {
            //Lets make the item's effects more user friendly to read, eh.
            $einfo = unserialize($r["effect{$enum}"]);
            $einfo['inc_type'] = ($einfo['inc_type'] == 'percent') ? '%' : '';
            $einfo['dir'] = ($einfo['dir'] == 'pos') ? "Increases" : "Decreases";
            $stats =
                array("energy" => "Energy", "will" => "Will",
                    "brave" => "Bravery", "level" => "Level",
                    "hp" => "Health", "strength" => "Strength",
                    "agility" => "Agility", "guard" => "Guard",
                    "labor" => "Labor", "iq" => "IQ",
                    "infirmary" => "Infirmary Time", "dungeon" => "Dungeon Time",
                    "primary_currency" => "Primary Currency", "secondary_currency"
                => "Secondary Currency", "crimexp" => "Experience", "vip_days" =>
                    "VIP Days");
            $statformatted = $stats["{$einfo['stat']}"];
            echo "{$einfo['dir']} {$statformatted} by " . number_format($einfo['inc_amount']) . "{$einfo['inc_type']}.<br />";
        } //If item has no effects, lets list the description instead.
        else {
            $uhoh++;
        }
        if ($uhoh == 3) {
            echo "{$r['itmdesc']}";
        }
    }
    //The form handles a lot of the internals for the pack info.
    //You should only need to change the currency_code.
    //Proceed at your own caution.
    echo "
		</td>
		<td>
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
		</td>
	</tr>";
}
echo "</table><br />
VIP Days disable ads around the game. You'll also receive 16% energy refill instead of 8%. You'll also receive a star by
 your name, and your name will change color.";
$h->endpage();
