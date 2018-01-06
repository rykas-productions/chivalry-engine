<?php
/**
	Lottery by TheMasterGeneral
	Filename: lottery.php
	Copyright: 2015

SQL:
INSERT INTO `settings` 
(`conf_id`, `conf_name`, `conf_value`) 
VALUES (NULL, 'lotterycash', '100000');
*/
require("globals.php");

//Config
	$minimumpot = 10000;			//Minimum pot.
	$costtoplay = 10000;			//Cost to get a ticket.
	$addedtopot = 9000;				//How much, out of per ticket, is added to the pot.
	$add2potformat = number_format($addedtopot);
	$cost2playformat = number_format($costtoplay);
	$minimumpotformat = number_format($minimumpot);
	$currentwinnings = number_format($set['lotterycash']);
	$lotteryid = 28; 				//The Id of conf_id for lotterycash. (Settings table)
//End config

if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}

switch ($_GET['action'])
{
case 'play':
    lottery_play();
    break;
default:
    lottery_home();
    break;
}
function lottery_home()
{
	global $db, $ir, $c, $userid, $h, $minimumpotformat, $cost2playformat, $add2potformat, $set, $currentwinnings;
	$csrf=request_csrf_code('lottery_buy');
	echo 
		"<h3>Raffle</h3>
		The pot starts at {$minimumpotformat}. It costs {$cost2playformat} to play. {$add2potformat} is deducted from your 
		ticket and added into the pot. Whoever gets the lucky ticket will get all the cash in the pot. You have a 1 and 
		{$set['raffle_chance']} chance to win the raffle. Chances are increased very occasionally as you play.<br />
		<br />
		<b>Current Pot: {$currentwinnings}<br /></b>
		<br />
		[<a href='?action=play&verf={$csrf}'>Buy Ticket</a>]";
		
}
function lottery_play()
{
	global $db, $ir, $c, $userid, $api, $h, $minimumpot, $costtoplay, $addedtopot, $currency, $set, $lotteryid;
	$chance = Random(1,$set['raffle_chance']);
	echo "<h3>Raffle</h3>";
	if (!isset($_GET['verf']) || !verify_csrf_code('lottery_buy', stripslashes($_GET['verf'])))
	{
		alert('danger',"Uh Oh!","Please do not refresh while playing the raffle.",true,'raffle.php');
		die($h->endpage());
	}
	if ($ir['primary_currency'] < $costtoplay)
	{
		alert('danger',"Uh Oh!","You do not have enough Copper Coins to play.",true,'raffle.php');
		die($h->endpage());
	}
	$increase_chance=Random(1,3);
	if ($increase_chance == 2)
	{
		if ($set['raffle_chance'] > 10)
		{
			$db->query("UPDATE `settings` SET `setting_value` = `setting_value` - 1 WHERE `setting_name` = 'raffle_chance'");
		}
	}
	//Pay up, subtracts money from player's cash
	//then adds {$addedtopot} to the pot.
	$db->query("UPDATE `settings` SET `setting_value` = `setting_value` + {$addedtopot} WHERE `setting_id` = {$lotteryid}");
	$api->UserTakeCurrency($userid, 'primary', $costtoplay);
	$csrf=request_csrf_code('lottery_buy');
	if ($chance == 1)	//Winner, winner, chicken dinner!!
	{
		$winnings=number_format($set['lotterycash']);
		alert('success',"Success!","You paid the fee and won the raffle's prize pot ({$winnings} Copper Coins)! Congratulations!",true,"?action=play&verf={$csrf}","Play Again");
		//Adds money!
		$api->UserGiveCurrency($userid, 'primary', $set['lotterycash']);
		//Sets the pot back to the minimum.
		$db->query("UPDATE `settings` SET `setting_value` = {$minimumpot} WHERE `setting_id` = {$lotteryid}");
		$text="<a href='profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}] has won the Chivalry is Dead Raffle and pocketed {$winnings} Copper Coins. A new raffle has been opened.";
		$api->GameAddAnnouncement($text);
		$db->query("UPDATE `settings` SET `setting_value` = 500 WHERE `setting_name` = 'raffle_chance'");
	}
	if ($chance >= 2)	//Loser, loser, someone's got a bruiser!
	{
		alert('danger',"Uh Oh!","You paid the fee and did not win the raffle. Better luck next time, champ.",true,"?action=play&verf={$csrf}","Play Again");
	}
}
$h->endpage();