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
echo "<h3>{$lang['VIP_LIST']}</h3><hr />{$lang['VIP_INFO']}";
echo "
<table class='table table-bordered'>
	<tr>
		<th>
			{$lang['VIP_TABLE_TH1']}
		</th>
		<th>
			{$lang['VIP_TABLE_TH2']}
		</th>
		<th width='25%'>
			{$lang['VIP_TABLE_TH3']}
		</th>
	</tr>";
$q=$db->query("SELECT `v`.*, `i`.* 
				FROM `vip_listing` `v`
				INNER JOIN `items` AS `i` 
				ON `itmid` = `vip_item`
				ORDER BY `vip_cost` ASC");
while ($r=$db->fetch_row($q))
{
	$r['vip_cost']=sprintf("%0.2f",$r['vip_cost']);
	echo "
	<tr>
		<td>
		{$r['itmname']}<br />
			{$lang['CRIME_TABLE_COST']}: \${$r['vip_cost']} USD
		</td>
		<td>
		";
			for ($enum = 1; $enum <= 3; $enum++)
			{
				if ($r["effect{$enum}_on"] == 'true')
				{
					$einfo = unserialize($r["effect{$enum}"]);
					$einfo['inc_type'] = ($einfo['inc_type'] == 'percent') ? '%' : '';
					$einfo['dir'] = ($einfo['dir'] == 'pos') ? 'Increases' : 'Decreases';
					echo "{$einfo['dir']} {$einfo['stat']} {$lang['ITEM_INFO_BY']} {$einfo['inc_amount']}{$einfo['inc_type']}.<br />";
				}
			}
		echo"
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
{$lang['VIP_TABLE_VDINFO']}";
$h->endpage();
