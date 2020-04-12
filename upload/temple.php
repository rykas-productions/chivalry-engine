<?php
/*
	File:		temple.php
	Created: 	4/5/2016 at 12:28AM Eastern Time
	Info: 		Allows players to spend their Chivalry Tokens on
				refilling their energy, will, and brave; along with
				spending it on IQ. Values are configurable. Check
				the staff panel.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
if (user_infirmary($userid))
{
    alert('danger',"Uh Oh!","You cannot use the Temple of Fortune if you're in the dungeon.",true,'infirmary.php');
    die($h->endpage());
}
if (user_dungeon($userid))
{
    alert('danger',"Uh Oh!","You cannot use the Temple of Fortune if you're in the dungeon.",true,'dungeon.php');
    die($h->endpage());
}
if (Random(1,50) == 6)
{
    put_infirmary($userid, Random(1,10), 'Fell down the temple stairs.');
    alert('danger',"Uh Oh!","While walking up to the Temple of Fortune, you trip up the stairs and fall all the way down. You need to go to the infirmary.",true,'infirmary.php');
    die($h->endpage());
}
echo "<h3><i class='game-icon game-icon-mayan-pyramid'></i> Temple of Fortune</h3><hr />";
//Set the GET to nothing if not set.
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
//GET switch to get current action.
switch ($_GET['action']) {
    case 'energy':
        energy();
        break;
    case 'brave':
        brave();
        break;
    case 'will':
        will();
        break;
	case 'willall':
        willall();
        break;
	case 'willall2':
        willall2();
        break;
    case 'iq':
        iq();
        break;
	case 'protection':
        protection();
        break;
    case 'coppertotoken':
        coppertotoken();
        break;
	case 'tokentocopper':
        tokentocopper();
        break;
    default:
        home();
        break;
}
function home()
{
    //Main index.
    global $set,$userid;
	$extraiq=(getSkillLevel($userid,12)*5);
    echo "Welcome to the Temple of Fortune. Here you may spend your Chivalry Tokens as you see fit!";
    echo "<br />
	<div class='row'>
		<div class='col-sm'>
			<a href='?action=energy'>Refill Energy<br /> " . number_format($set['energy_refill_cost']) . " Chivalry Tokens</a>
		</div>
		<div class='col-sm'>
			<a href='?action=brave'>Regenerate 5% Bravery<br />" . number_format($set['brave_refill_cost']) . " Chivalry Tokens</a>
		</div>
		<div class='col-sm'>
			<a href='?action=will'>Regenerate 5% Will<br />" . number_format($set['will_refill_cost']) . " Chivalry Tokens</a>
		</div>
		<div class='col-sm'>
			<a href='?action=willall'>Regenerate 100% Will<br />" . number_format($set['will_refill_cost']*20) . " Chivalry Tokens</a>
		</div>
	</div>
	<hr />
	<div class='row'>
		<div class='col-sm'>
			<a href='?action=willall2'>Regenerate 1,000% Will<br />" . number_format($set['will_refill_cost']*225) . " Chivalry Tokens</a>
		</div>
		<div class='col-sm'>
			<a href='?action=iq'>Buy IQ <br />" . number_format($set['iq_per_sec']) . "* IQ Per Token</a>
		</div>
		<div class='col-sm'>
			<a href='?action=protection'>Buy Protection<br />5 Chivalry Tokens per Minute</a><br /><br />
		</div>
	</div>
	<hr />
	<div class='row'>
		<div class='col-sm'>
			<a href='?action=coppertotoken'>50k Copper -> 1 Chivalry Token</a>
		</div>
		<div class='col-sm'>
			<a href='?action=tokentocopper'>1 Chivalry Token -> 1,000 Copper</a>
		</div>
	</div>
	<hr />
	*=You will receive an extra {$extraiq}% IQ per Token because of your skills.";
}

function energy()
{
    global $api, $userid, $set;
    //User has enough Chivalry Tokens to refill their energy.
    if ($api->UserHasCurrency($userid, 'secondary', $set['energy_refill_cost'])) {
        //User's energy is already full.
        if ($api->UserInfoGet($userid, 'energy', true) == 100) {
            alert('danger', "Uh Oh!", "You already have full energy.", true, 'temple.php');
        } else {
			if (calculateLuck($userid))
			{
				//Refill the user's energy and take their Chivalry Tokens.
				$api->UserInfoSet($userid, 'energy', 100, true);
				alert('success', "Success!", "Luck is on your side today! You received a free energy refill.", true, 'temple.php');
				$api->SystemLogsAdd($userid, 'temple', "Traded 0 Chivalry Tokens to refill their Energy.");

			}
			else
			{
				//Refill the user's energy and take their Chivalry Tokens.
				$api->UserInfoSet($userid, 'energy', 100, true);
				$api->UserTakeCurrency($userid, 'secondary', $set['energy_refill_cost']);
				alert('success', "Success!", "You have paid {$set['energy_refill_cost']} Chivalry Tokens to refill your energy.", true, 'temple.php');
				$api->SystemLogsAdd($userid, 'temple', "Traded {$set['energy_refill_cost']} Chivalry Tokens to refill their Energy.");
				addToEconomyLog('Temple of Fortune', 'token', ($set['energy_refill_cost'])*-1);
			}
        }
    } else {
        alert('danger', "Uh Oh!", "You do not have enough Chivalry Tokens to refill your energy.", true, 'temple.php');
    }
}

function brave()
{
    global $api, $userid, $ir, $set, $h;
    //User has enoguh Chivalry Tokens to refill their brave
    if ($api->UserHasCurrency($userid, 'secondary', $set['brave_refill_cost'])) {
        //User's brave is already full.
        if ($api->UserInfoGet($userid, 'brave', true) == 100) {
            alert('danger', "Uh Oh!", "You already have full Bravery.", true, 'temple.php');
        } else {
			if (calculateLuck($userid))
			{
				//Refill the user's bravery by 5% and take their Chivalry Tokens.
				$api->UserInfoSet($userid, 'brave', 5, true);
				alert('success', "Success!", "Luck is on your side today! This bravery regeneration was free.", true, 'temple.php');
				$api->SystemLogsAdd($userid, 'temple', "Traded 0 Chivalry Tokens to regenerate 5% Brave.");
			}
			else
			{
				//Refill the user's bravery by 5% and take their Chivalry Tokens.
				$api->UserInfoSet($userid, 'brave', 5, true);
				$api->UserTakeCurrency($userid, 'secondary', $set['brave_refill_cost']);
				alert('success', "Success!", "You have paid {$set['brave_refill_cost']} to regenerate 5% Bravery.", true, 'temple.php');
				$api->SystemLogsAdd($userid, 'temple', "Traded {$set['brave_refill_cost']} Chivalry Tokens to regenerate 5% Brave.");
				addToEconomyLog('Temple of Fortune', 'token', ($set['brave_refill_cost'])*-1);
			}
        }
    } else {
        alert('danger', "Uh Oh!", "You do not have enough Chivalry Tokens to refill your Bravery.", true, 'temple.php');
    }
}

function will()
{
    global $api, $userid, $set, $ir, $db, $h;
    //User has enough Chivalry Tokens to refill their will.
    if ($api->UserHasCurrency($userid, 'secondary', $set['will_refill_cost'])) {
        //User's will is already at 100%
        if (($api->UserInfoGet($userid, 'will', true) == 100) && ($ir['will_overcharge'] < time())) {
            alert('danger', "Uh Oh!", "You already have full Will.", true, 'temple.php');
        } else {
			if ($ir['will'] > ($ir['maxwill'] * 10))
			{
				alert('danger',"Uh Oh!","You need to take a break.",true,'index.php');
				die($h->endpage());
			}
			if (calculateLuck($userid))
			{
				alert('success', "Success!", "Luck is on your side today! You received a free will regeneration!", true, 'temple.php');
				$api->SystemLogsAdd($userid, 'temple', "Traded 0 Chivalry Tokens to regenerate 5% Will.");
			}
			else
			{
				$api->UserTakeCurrency($userid, 'secondary', $set['will_refill_cost']);
				alert('success', "Success!", "You have paid {$set['will_refill_cost']} Chivalry Tokens to regenerate 5% Will", true, 'temple.php');
				$api->SystemLogsAdd($userid, 'temple', "Traded {$set['will_refill_cost']} Chivalry Tokens to regenerate 5% Will.");
				addToEconomyLog('Temple of Fortune', 'token', ($set['will_refill_cost'])*-1);
			}
			$db->query("UPDATE `users` SET `will` = `will` + (`maxwill`/20) WHERE `userid` = {$userid}");
        }
    } else {
        alert('danger', "Uh Oh!", "You do have have enough Chivalry Tokens to refill your Will.", true, 'temple.php');
    }
}
function willall()
{
    global $api, $userid, $set;
    //User has enough Chivalry Tokens to refill their will.
    if ($api->UserHasCurrency($userid, 'secondary', $set['will_refill_cost']*20)) {
        //User's will is already at 100%
        if ($api->UserInfoGet($userid, 'will', true) == 100) {
            alert('danger', "Uh Oh!", "You already have full Will.", true, 'temple.php');
        } else {
			if (calculateLuck($userid))
			{
				//Refill the user's will by 5% and take their Chivalry Tokens.
				$api->UserInfoSet($userid, 'will', 100, true);
				alert('success', "Success!", "Luck is on your side today! You received a free will regeneration!", true, 'temple.php');
				$api->SystemLogsAdd($userid, 'temple', "Traded 0 Chivalry Tokens to regenerate 100% Will.");
			}
			else
			{
				//Refill the user's will by 5% and take their Chivalry Tokens.
				$api->UserInfoSet($userid, 'will', 100, true);
				$api->UserTakeCurrency($userid, 'secondary', $set['will_refill_cost']*20);
				alert('success', "Success!", "You have paid " . number_format($set['will_refill_cost']*20) . " Chivalry Tokens to regenerate 100% Will", true, 'temple.php');
				$api->SystemLogsAdd($userid, 'temple', "Traded " . number_format($set['will_refill_cost']*20) . " Chivalry Tokens to regenerate 100% Will.");
				addToEconomyLog('Temple of Fortune', 'token', ($set['will_refill_cost']*20)*-1);
			}
        }
    } else {
        alert('danger', "Uh Oh!", "You do have have enough Chivalry Tokens to refill your Will.", true, 'temple.php');
    }
}
function willall2()
{
    global $api, $userid, $set, $ir, $db, $h;
	if ($ir['will_overcharge'] < time())
	{
		alert('danger',"Uh Oh!","You cannot purchase 1,000% Will at this time. Consume a Will Stimulant Potion and try again.",true,'temple.php');
		die($h->endpage());
	}
    //User has enough Chivalry Tokens to refill their will.
    if ($api->UserHasCurrency($userid, 'secondary', $set['will_refill_cost']*225)) {
        //User's will is already at 100%
        if ($api->UserInfoGet($userid, 'will', true) == 1000) {
            alert('danger', "Uh Oh!", "You already have 1,000% Will.", true, 'temple.php');
        } else {
				$api->UserTakeCurrency($userid, 'secondary', $set['will_refill_cost']*225);
				alert('success', "Success!", "You have paid " . number_format($set['will_refill_cost']*225) . " Chivalry Tokens to regenerate 1,000% Will", true, 'temple.php');
				$api->SystemLogsAdd($userid, 'temple', "Traded " . number_format($set['will_refill_cost']*225) . " Chivalry Tokens to regenerate 1,000% Will.");
				$ir['will'] = $ir['maxwill'] * 10;
				$db->query("UPDATE `users` SET `will` = {$ir['will']} WHERE `userid` = {$userid}");
				addToEconomyLog('Temple of Fortune', 'token', ($set['will_refill_cost']*225)*-1);
        }
    } else {
        alert('danger', "Uh Oh!", "You do have have enough Chivalry Tokens to refill your Will.", true, 'temple.php');
    }
}

function iq()
{
    global $db, $api, $userid, $ir, $h, $set;
    if (isset($_POST['iq'])) {
        //Make sure the POST is safe to work with.
        $_POST['iq'] = (isset($_POST['iq']) && is_numeric($_POST['iq'])) ? abs($_POST['iq']) : '';

        //POST is empty.
        if (empty($_POST['iq'])) {
            alert('danger', "Uh Oh!", "Please specify how much Chivalry Tokens you wish to trade in for IQ.");
            die($h->endpage());
        }
        //IQ gained is coins exchanged multiplied by game setting for how much IQ per coin.
        $totalcost = $_POST['iq'] * $set['iq_per_sec'];

        //User does not have enough Chivalry Tokens to exchange for how much they said they wanted in IQ.
        if ($api->UserHasCurrency($userid, 'secondary', $_POST['iq']) == false) {
            alert('danger', "Uh Oh!", "You do not have enough Chivalry Tokens to buy that much IQ.");
            die($h->endpage());
        }
		$specialnumber=((getSkillLevel($userid,12)*5)/100);
		$totalcost=$totalcost+($totalcost*$specialnumber);
		addToEconomyLog('Temple of Fortune', 'token', ($_POST['iq'])*-1);
        //Take the currency and give the user some IQ.
        $api->UserTakeCurrency($userid, 'secondary', $_POST['iq']);
        $db->query("UPDATE `userstats` SET `iq` = `iq` + {$totalcost} WHERE `userid` = {$userid}");
        alert('success', "Success!", "You have successfully traded " . number_format($_POST['iq']) . " Chivalry Tokens for " . number_format($totalcost) . " IQ Points.", true, 'temple.php');
        $api->SystemLogsAdd($userid, 'temple', "Traded {$_POST['iq']} Chivalry Tokens for {$totalcost} IQ.");
    } else {
		$extraiq=(getSkillLevel($userid,12)*5);
        alert('info', "Information!", "You can trade in your Chivalry Tokens for IQ at a ratio of {$set['iq_per_sec']}*
		per Chivalry Tokens. You currently have " . number_format($ir['secondary_currency']) . " Chivalry Tokens.", false);
        echo "<table class='table table-bordered'>
			<form method='post'>
			<tr>
				<th>
					Chivalry Tokens
				</th>
				<td>
					<input type='number' class='form-control' name='iq' min='1' max='{$ir['secondary_currency']}' required='1'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Trade for IQ'>
				</td>
			</tr>
			</form>
		</table>
		*=You will receive an extra {$extraiq}% IQ per Token because of your skills.";
    }
}
function protection()
{
	global $ir,$userid,$api,$h,$db;
	if ($ir['protection'] > time())
	{
		alert('danger',"Uh Oh!","You cannot buy more protection while you already have an existing contract in place.",true,'temple.php');
		die($h->endpage());
	}
	if (isset($_POST['protection']))
	{
		$protection = (isset($_POST['protection']) && is_numeric($_POST['protection'])) ? abs($_POST['protection']) : 0;
		if (!isset($_POST['verf']) || !verify_csrf_code('protection', stripslashes($_POST['verf']))) {
			alert('danger', "Action Blocked!", "Your action has been blocked for security reasons. Form requests expire fairly quickly. Be sure to be quicker next time.");
			die($h->endpage());
		}
		if (empty($protection))
		{
			alert('danger',"Uh Oh!","Please specify how many minutes of protection you wish to purchase.");
			die($h->endpage());
		}
		if ($protection > 60)
		{
			alert('danger',"Uh Oh!","You may only purchase 60 minutes of protection at a time.");
			die($h->endpage());
		}
		$cost=$protection*5;
		if (!($api->UserHasCurrency($userid,'secondary',$cost)))
		{
			alert('danger',"Uh Oh!","You need {$cost} Chivalry Tokens for {$protection} minutes of protection. You only have {$ir['secondary_currency']}.");
			die($h->endpage());
		}
		$endtime=time()+($protection*60);
		$db->query("UPDATE `user_settings` SET `protection` = {$endtime} WHERE `userid` = {$userid}");
		alert('success',"Success!","You have successfully traded {$cost} Chivalry Tokens for {$protection} minutes of protection.",true,'temple.php');
		$api->SystemLogsAdd($userid, 'temple', "Traded {$cost} Chivalry Tokens for {$protection} minutes of protection.");
		$api->UserTakeCurrency($userid,'secondary',$cost);
		addToEconomyLog('Temple of Fortune', 'token', ($cost)*-1);
		$h->endpage();
	}
	else
	{
		$csrf=request_csrf_html('protection');
		echo "Write some checks with your mouth that your ass cannot cash? Buying protection might be for you!
		Protection will make it so you cannot be bombed with small or medium explosives, or be attacked. However, 
		if you attack another player, you will lose your protection. Its quite simple. <br /><b>Each minute of protection 
		will cost you 5 Chivalry Tokens.</b> You may not buy more than 60 minutes at a time. So, how many minutes of 
		protection would you like to buy?<br />
		<form method='post'>
			<input type='number' min='1' name='protection' max='60' class='form-control' required='1'>
			<input type='submit' class='btn btn-primary' value='Buy Protection'>
			{$csrf}
		</form>";
	}
}

function coppertotoken()
{
	global $db,$userid,$api,$h;
	if (isset($_POST['token']))
	{
		$token = (isset($_POST['token']) && is_numeric($_POST['token'])) ? abs($_POST['token']) : 0;
		if (!isset($_POST['verf']) || !verify_csrf_code('token', stripslashes($_POST['verf']))) {
			alert('danger', "Action Blocked!", "Your action has been blocked for security reasons. Form requests expire fairly quickly. Be sure to be quicker next time.");
			die($h->endpage());
		}
		if (empty($token))
		{
			alert('danger',"Uh Oh!","Please specify how many tokens you wish to purchase.");
			die($h->endpage());
		}
		$cost=$token*50000;
		if (!$api->UserHasCurrency($userid,'primary',$cost))
		{
			alert('danger',"Uh Oh!","You do not have enough Copper Coins to exchange for {$token} Chivalry Tokens. You need {$cost} Copper Coins.");
			die($h->endpage());
		}
		addToEconomyLog('Temple of Fortune', 'copper', ($cost)*-1);
		addToEconomyLog('Temple of Fortune', 'token', $token);
		$api->UserTakeCurrency($userid,'primary',$cost);
		$api->UserGiveCurrency($userid,'secondary',$token);
		$api->SystemLogsAdd($userid, 'temple', "Traded {$cost} Copper Coins for {$token} Chivalry Tokens.");
		alert('success',"Success!","You have successfully traded {$cost} Copper Coins for {$token} Chivalry Tokens.",true,'temple.php');
	}
	else
	{
		$csrf=request_csrf_html('token');
		echo "You may convert your Copper Coins to Chivalry Tokens at 50,000 Copper Coins per Token. This is to 
		limit the maximum value of Chivalry Tokens when taking in account for inflation in the game. This price 
		is very likely to change as the game progresses. How many tokens would you like to receive?
		<form method='post'>
			<input type='number' min='1' name='token' class='form-control' required='1'>
			<input type='submit' class='btn btn-primary' value='Convert to Tokens'>
			{$csrf}
		</form>";
	}
}
function tokentocopper()
{
	global $db,$userid,$api,$h,$ir;
	if (isset($_POST['token']))
	{
		$token = (isset($_POST['token']) && is_numeric($_POST['token'])) ? abs($_POST['token']) : 0;
		if (!isset($_POST['verf']) || !verify_csrf_code('copper', stripslashes($_POST['verf']))) {
			alert('danger', "Action Blocked!", "Your action has been blocked for security reasons. Form requests expire fairly quickly. Be sure to be quicker next time.");
			die($h->endpage());
		}
		if (empty($token))
		{
			alert('danger',"Uh Oh!","Please specify how many tokens you wish to exchange for Copper Coins.");
			die($h->endpage());
		}
		$cost=$token*1000;
		if (!$api->UserHasCurrency($userid,'secondary',$token))
		{
			alert('danger',"Uh Oh!","You do not have enough Chivalry Tokens to exchange for {$cost} Copper Coins. You need {$token} Chivalry Tokens.");
			die($h->endpage());
		}
		$api->UserGiveCurrency($userid,'primary',$cost);
		$api->UserTakeCurrency($userid,'secondary',$token);
		addToEconomyLog('Temple of Fortune', 'copper', $cost);
		addToEconomyLog('Temple of Fortune', 'token', ($token)*-1);
		$api->SystemLogsAdd($userid, 'temple', "Traded {$token} Chivalry Tokens for {$cost} Copper Coins.");
		alert('success',"Success!","You have successfully traded {$token} Chivalry Tokens for {$cost} Copper Coins.",true,'temple.php');
	}
	else
	{
		$csrf=request_csrf_html('copper');
		echo "You may convert your Chivalry Tokens to Copper Coins at 1,000 Copper Coins per Token. This is to 
		limit the minimum value of Chivalry Tokens when taking in account for inflation in the game. This price 
		is very likely to change as the game progresses. How many tokens would you like to exchange?
		<form method='post'>
			<input type='number' min='1' name='token' max='{$ir['secondary_currency']}' value='{$ir['secondary_currency']}' class='form-control' required='1'>
			<input type='submit' class='btn btn-primary' value='Convert to Copper'>
			{$csrf}
		</form>";
	}
}

$h->endpage();