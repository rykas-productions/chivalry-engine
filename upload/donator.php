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
$percentoff=1;
if (isset($_GET['user']))
{
	$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : $userid;
	if (!$api->SystemUserIDtoName($_GET['user']))
	{
		alert('danger',"Uh Oh!","The user you're trying to donate for does not exist.");
		die($h->endpage());
	}
	echo "<h3>VIP Packs</h3><hr />If you purchase a VIP Package from below, you will be gifted the following depending on
		the package your purchase. All purchases are final. If you commit fraud, you will be removed from the game permanently.<br />
        As of January 9th, 2019, each donation will give a <a href='iteminfo.php?ID=128'>VIP Color Changer</a>.<br />
		Each day you log in with a VIP Day, you will receive 750 Chivalry Tokens automatically to your Token Bank account. Will fallback onto your person if you do not have an account.";
	$goal=25;
	$progress=round(($set['MonthlyDonationGoal']/$goal)*100);
	$bg = ($set['MonthlyDonationGoal'] >= $goal) ? "bg-success" : "" ;
	$set['MonthlyDonationGoal']=round($set['MonthlyDonationGoal'],2);
	echo "
	<br />
	<div class='progress' style='height: 1rem;'>
		<div class='progress-bar {$bg}' role='progressbar' aria-valuenow='{$set['MonthlyDonationGoal']}' aria-valuemin='0' aria-valuemax='{$goal}' style='width: {$progress}%'>
			<span>
				Monthly Donation Goal - \${$set['MonthlyDonationGoal']} / \${$goal}
			</span>
		</div>
	</div>
	<br />";
	if (!isset($count))
		$count=0;
	$q = $db->query("/*qc=on*/SELECT `v`.*, `i`.*
					FROM `vip_listing` `v`
					INNER JOIN `items` AS `i` 
					ON `itmid` = `vip_item`
					ORDER BY `vip_cost` ASC");
	//List the donator packages.
	echo "<div class='row'>";
	while ($r = $db->fetch_row($q)) {
		//Put the VIP Cost in a currency number. (Ex. $1.54)
		$r['vip_cost'] = sprintf("%0.2f", $r['vip_cost']*$percentoff);
		$amount = ($r['vip_qty'] > 1) ? "{$r['vip_qty']} x " : '';
		echo "
				<div class='col-md-6 col-xl-4'>
				<div class='card'>
					<div class='card-header box-shadow'>
						{$amount} <a href='iteminfo.php?ID={$r['vip_item']}'>{$r['itmname']}</a><br />
					</div>
					<div class='card-body'>
						<h1 class='card-title pricing-card-title'>\${$r['vip_cost']} USD</h1>";
		$uhoh = 0;
		//List the item's effects.
		for ($enum = 1; $enum <= 3; $enum++) {
			if ($r["effect{$enum}_on"] == 'true') {
				//Lets make the item's effects more user friendly to read, eh.
				$einfo = unserialize($r["effect{$enum}"]);
				$einfo['inc_type'] = ($einfo['inc_type'] == 'percent') ? '%' : '';
				$einfo['dir'] = ($einfo['dir'] == 'pos') ? "+" : "-";
				$stats =
					array("energy" => "Energy", "will" => "Will",
						"brave" => "Bravery", "level" => "Level",
						"hp" => "Health", "strength" => "Strength",
						"agility" => "Agility", "guard" => "Guard",
						"labor" => "Labor", "iq" => "IQ",
						"infirmary" => "Infirmary Time", "dungeon" => "Dungeon Time",
						"primary_currency" => "Copper Coins", "secondary_currency"
					=> "Chivalry Tokens", "crimexp" => "Experience", "vip_days" =>
						"VIP Days*" , "premium_currency" => "Mutton");
				$statformatted = $stats["{$einfo['stat']}"];
				echo "{$einfo['dir']}" . number_format($einfo['inc_amount']) . "{$einfo['inc_type']} {$statformatted}<br />";
			} //If item has no effects, lets list the description instead.
			else {
				$uhoh++;
			}
			if ($uhoh == 3) {
				echo "{$r['itmdesc']}<br />";
			}
		}
		echo "<b>Enter Quantity</b>";
		//The form handles a lot of the internals for the pack info.
		//You should only need to change the currency_code.
		//Proceed at your own caution.
		echo "
		<form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
		<div class='row'>
			<div class='col-8 col-md-7'>
				<input type='hidden' name='cmd' value='_xclick' />
				<input type='hidden' name='business' value='{$set['PaypalEmail']}' />
				<input type='hidden' name='item_name' value='{$domain}|VIP|{$r['vip_id']}|{$_GET['user']}|{$userid}' />
				<input type='hidden' name='amount' value='{$r['vip_cost']}' />
				<input type='hidden' name='no_shipping' value='1' />
				<input type='hidden' name='return' value='https://{$domain}/donatordone.php?action=done' />
				<input type='hidden' name='cancel_return' value='http://{$domain}/donatordone.php?action=cancel' />
				<input type='hidden' name='notify_url' value='https://{$domain}/donator_ipn.php' />
				<input type='hidden' name='cn' value='Your Player ID' />
				<input type='hidden' name='currency_code' value='USD' />
				<input type='hidden' name='tax' value='0' />
				<input type='hidden' name='rm' value='2'>
				<input type='number' min='1' max='100' value='1' name='quantity' class='form-control' required='1' placeholder='Quantity'>
			</div>
			<div class='col-4 col-md-5'>
				<button class='btn btn-primary btn-block' type='submit'><i class='fab fa-paypal'></i> PayPal</button>
			</div>
		</div>
		</form>
		</div>
		</div>
        <br />
		</div>
		";
	}
	echo "</div><hr /><p class='text-muted'><small>*VIP Days grant the following benefits:<br />
	33% Energy Refill every 5 Minutes; 5% bank interest; Access to Friends List; Access to Enemies List; Access to VIP Logs;
	Better investment rates; Customizable VIP color and badge; 750 Chivalry Tokens daily; More notepads; More shortcuts;</small></p>";
}
else
{
	echo "Please select the user you wish to donate for.<br />
		<form>
		" . user_dropdown('user',$userid) . "
		<input type='submit' value='Continue' class='btn btn-primary'>
		</form>";
}
$h->endpage();
