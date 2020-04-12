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
$macropage = ('raffle.php');
require("globals.php");
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
	alert('danger',"Uh Oh!","You cannot visit the Raffle while in the dungeon or infirmary.",true,'explore.php');
	die($h->endpage());
}

//Config
	$minimumpot = 250000;			//Minimum pot.
	$costtoplay = 10000;			//Cost to get a ticket.
	$addedtopot = 8500;				//How much, out of per ticket, is added to the pot.
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
	global $db, $ir, $c, $userid, $h, $minimumpotformat, $cost2playformat, $add2potformat, $set, $currentwinnings, $api;
	$csrf=request_csrf_code('lottery_buy');
    $winchance=round((1/$set['raffle_chance'])*100,2);
	echo 
		"<h3>Raffle</h3><hr />
		The pot starts at {$minimumpotformat} Copper Coins. It costs {$cost2playformat} Copper Coins to play. {$add2potformat} Copper Coins is deducted from your 
		ticket and added into the pot. Whoever gets the lucky ticket will get all the cash in the pot, along with a <a href='iteminfo.php?ID=160'>Badge of Luck</a>. 
		<u>You have a {$winchance}% chance to win the raffle.</u> Chances are increased very occasionally as you play.<br />
		<br />
		<b>Current Pot:</b> {$currentwinnings} Copper Coins<br />
        <b>Previous Winner:</b> " . parseUsername($set['raffle_last_winner']) . " [{$set['raffle_last_winner']}]
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
    if ($userid == $set['raffle_last_winner'])
    {
        alert('danger',"Uh Oh!","You cannot participate in the raffle until another player wins.",true,'raffle.php');
        die($h->endpage()); 
    }
	$increase_chance=Random(1,2);
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
	addToEconomyLog('Gambling', 'copper', ($costtoplay - $addedtopot)*-1);
	$csrf=request_csrf_code('lottery_buy');
	if ($chance == 1)	//Winner, winner, chicken dinner!!
	{
		$winnings=number_format($set['lotterycash']);
		$api->UserGiveItem($userid,160,1);
		alert('success',"Success!","You paid the fee and won the raffle's prize pot ({$winnings} Copper Coins)! Congratulations!",true,"?action=play&verf={$csrf}","Play Again");
		//Adds money!
		$api->UserGiveCurrency($userid, 'primary', $set['lotterycash']);
		//Sets the pot back to the minimum.
		$db->query("UPDATE `settings` SET `setting_value` = {$minimumpot} WHERE `setting_id` = {$lotteryid}");
		$text="<a href='profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}] has won the Chivalry is Dead Raffle and pocketed {$winnings} Copper Coins. A new raffle has been opened.";
		$api->GameAddAnnouncement($text);
		$db->query("UPDATE `settings` SET `setting_value` = 1000 WHERE `setting_name` = 'raffle_chance'");
        $db->query("UPDATE `settings` SET `setting_value` = {$userid} WHERE `setting_name` = 'raffle_last_winner'");\
		addToEconomyLog('Gambling', 'copper', $set['lotterycash']);
		addToEconomyLog('Gambling', 'copper', $minimumpot);
	}
	if ($chance >= 2)	//Loser, loser, someone's got a bruiser!
	{
		alert('danger',"Uh Oh!","You paid the fee and did not win the raffle. Better luck next time, champ.",true,"?action=play&verf={$csrf}","Play Again");
	}
}
$h->endpage();