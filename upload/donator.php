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
$percentoff=$set['viprate'] / 100;
if (isset($_GET['user']))
{
	$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : $userid;
	if (!$api->SystemUserIDtoName($_GET['user']))
	{
		alert('danger',"Uh Oh!","The user you're trying to donate for does not exist.");
		die($h->endpage());
	}
	$goal=$_CONFIG['donationGoal'];
	$progress=round(($set['MonthlyDonationGoal']/$goal)*100);
	$bg = ($set['MonthlyDonationGoal'] >= $goal) ? "bg-success" : "" ;
	$set['MonthlyDonationGoal']=round($set['MonthlyDonationGoal'],2);
	echo "
    <div class='row'>
        <div class='col-12'>
            <div class='card'>
                <div class='card-body'>
                    <div class='row'>
                        This is the monthly donation goal. If we meet/exceed this value, numerous benefits will be unlocked for all players for the remainder of the month!
                    </div>
                    <div class='progress' style='height: 1rem;'>
                		<div class='progress-bar {$bg}' role='progressbar' aria-valuenow='{$set['MonthlyDonationGoal']}' aria-valuemin='0' aria-valuemax='{$goal}' style='width: {$progress}%'>
                			<span>
                				Monthly Donation Goal - \${$set['MonthlyDonationGoal']} / \${$goal}
                			</span>
                		</div>
                	</div>
                </div>
            </div>
            <br />
        </div>
    </div>
    <div class='row'>
        <div class='col-12'>
            <div class='card'>
                <div class='card-header'>
                    {$set['WebsiteName']} VIP Packs
                </div>
                <div class='card-body'>
                    <div class='row'>";
	if (!isset($count))
		$count=0;
	$q = $db->query("/*qc=on*/SELECT `v`.*, `i`.*
					FROM `vip_listing` `v`
					INNER JOIN `items` AS `i` 
					ON `itmid` = `vip_item`
					ORDER BY `vip_cost` ASC");
	while ($r = $db->fetch_row($q)) 
	{
		//Put the VIP Cost in a currency number. (Ex. $1.54)
		$r['vip_cost'] = sprintf("%0.2f", $r['vip_cost']*$percentoff);
		$amount = ($r['vip_qty'] > 1) ? "{$r['vip_qty']} x " : '';
		echo "
        <div class='col-12'>
            <div class='row'>
                <div class='col-12 col-sm-6 col-md-2 col-xxxl-1'>
                    <b>\${$r['vip_cost']} USD</b>
                </div>
                <div class='col-12 col-sm-6 col-md-4 col-xxl-2'>
                    {$amount} <a href='iteminfo.php?ID={$r['vip_item']}'>{$r['itmname']}</a>
                </div>";
		$uhoh = 0;
		$itemInfo = "";
		//List the item's effects.
		for ($enum = 1; $enum <= 3; $enum++) 
		{
			if ($r["effect{$enum}_on"] == 'true') 
			{
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
				$itemInfo .= "{$einfo['dir']}" . number_format($einfo['inc_amount']) . "{$einfo['inc_type']} {$statformatted} ";
			} //If item has no effects, lets list the description instead.
			else 
			{
				$uhoh++;
			}
			if ($uhoh == 3) 
			{
				$itemInfo .= $r['itmdesc'];
			}
		}
		echo "
            <div class='col-12 col-xxl-7 col-xxxl-5'>
                {$itemInfo}
            </div>
	        <div class='col-12 col-xxxl-4'>
                <div class='row'>
                    <div class='col-12'>
                        <small><b>Enter Quantity</b></small>
                    </div>
                    <div class='col-12'>
                        <div class='row'>
                            <div class='col-12 col-sm-6'>
                                <form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
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
                            <div class='col-12 col-sm-6'>
                                <button class='btn btn-primary btn-block' type='submit'><i class='fab fa-paypal'></i> PayPal</button></form>
                            </div>
                        </div>
                    </div>
                </div>
		    </div>
        </div>
        <hr />
        </div>
        ";
	}
	echo "</div>
    </div>
    </div>
    <div class='row'>
        <div class='col-12 col-xl-6'><br />
            <div class='card'>
                <div class='card-header'>
                    VIP Day Benefits
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-7 col-md-5 col-xl-8 col-xxl-6 col-xxxl-4'>
                            *<b>33%</b> Energy Every 5 Minutes
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-xl-7 col-xxl-6 col-xxxl-4'>
                            *<b>5%</b> Daily Bank Interest
                        </div>
                        <div class='col-12 col-sm-4 col-md-3 col-xl-5 col-xxl-3 col-xxxl-2'>
                            *Friends List
                        </div>
                        <div class='col-12 col-sm-4 col-md-3 col-xl-4 col-xxl-3 col-xxxl-3'>
                            *Enemies List
                        </div>
                        <div class='col-12 col-sm-3 col-md-2 col-xl-4 col-xxl-3 col-xxxl-2'>
                            *VIP Logs
                        </div>
                        <div class='col-12 col-sm-7 col-md-5 col-xl-8 col-xxl-6 col-xxxl-4'>
                           *Better Bank Investment Rates
                        </div>
                        <div class='col-12 col-sm-8 col-md-6 col-xl-9 col-xxl-7 col-xxxl-5'>
                           *Customizable VIP color and badge
                        </div>
                        <div class='col-12 col-sm-6 col-md-5 col-xl-7 col-xxl-6 col-xxxl-4'>
                           *750 Chivalry Tokens Daily
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-xl-5 col-xxl-4 col-xxxl-3'>
                           *More Shortcuts
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-xl-5 col-xxl-4 col-xxxl-3'>
                           *More Notepads
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class='col-12 col-xl-6'><br />
            <div class='card'>
                <div class='card-header'>
                    Notes
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12'>
                            All purchases are final. Donation fraud is not tolerated and will be dealt with severely.
                            each donation will give a <a href='iteminfo.php?ID=128'>VIP Color Changer</a>.
                            Each day you log in with a VIP Day, you will receive 750 Chivalry Tokens automatically to 
                            your Token Bank account. Will fallback onto your person if you do not have an account.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>";
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
