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
$minimumpot = mt_rand(125000, 350000);			//Minimum pot.
$costtoplay = 10000;			//Cost to get a ticket.
$addedtopot = 8500;				//How much, out of per ticket, is added to the pot.
$add2potformat = shortNumberParse($addedtopot);
$cost2playformat = shortNumberParse($costtoplay);
$minimumpotformat = shortNumberParse($minimumpot);
$currentwinnings = shortNumberParse($set['lotterycash']);
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
    echo "<div class='card'>
        <div class='card-header'>
            {$set['WebsiteName']} Raffle <b>(Chance: {$winchance}%)</b>
        </div>
        <div class='card-body'>
            <div class='row'>
                <div class='col-12'>
                    The raffle's pot begins between 125K and 350K Copper Coins and increases as players play, a portion of their raffle bet 
                    going back into the pot. It costs {$cost2playformat} Copper Coins to play, with {$add2potformat} Copper Coins going 
                    towards the pot. The raffle winner will receive all the Copper Coins in the pot, a <a href='iteminfo.php?ID=160'>Badge of Luck</a>, 
                    and an in-game announcement congratulating them. <u>Note, that winners cannot participate in the {$set['WebsiteName']} Raffle 
                    until another winner is declared.</u>
                </div>
                <div class='col-12 col-sm-6 col-lg-4 col-xl-3'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Current Pot</b></small>
                        </div>
                        <div class='col-12'>
                            {$currentwinnings} Copper Coins
                        </div>
                    </div>
                </div>
                <div class='col-12 col-sm-6 col-lg-4 col-xl'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Previous Winner</b></small>
                        </div>
                        <div class='col-12'>
                            <a href='profile.php?user={$set['raffle_last_winner']}'>"
                                . parseUsername($set['raffle_last_winner']) . "
                            </a> [{$set['raffle_last_winner']}]
                        </div>
                    </div>
                </div>
                <div class='col-12 col-lg-4 col-xl'>
                    <div class='row'>
                        <div class='col-12 col-sm-6 col-lg'>
                            <a href='?action=play&verf={$csrf}' class='btn btn-primary btn-block'>Buy Ticket</a>
                        </div>
                        <div class='col-12 col-sm-6 col-lg'>
                            <a href='explore.php' class='btn btn-danger btn-block'>Explore</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>";
}
function lottery_play()
{
	global $db, $ir, $c, $userid, $api, $h, $minimumpot, $costtoplay, $addedtopot, $currency, $set, $lotteryid;
	$chance = Random(1,$set['raffle_chance']);
	$winchance=round((1/$set['raffle_chance'])*100,2);
	echo "<h3>{$set['WebsiteName']} Raffle <b>(Chance: {$winchance}%)</b></h3>";
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
		if ($set['raffle_chance'] > 10)
			$db->query("UPDATE `settings` SET `setting_value` = `setting_value` - 1 WHERE `setting_name` = 'raffle_chance'");
	//Pay up, subtracts money from player's cash
	//then adds {$addedtopot} to the pot.
	$db->query("UPDATE `settings` SET `setting_value` = `setting_value` + {$addedtopot} WHERE `setting_id` = {$lotteryid}");
	$api->UserTakeCurrency($userid, 'primary', $costtoplay);
	addToEconomyLog('Gambling', 'copper', ($costtoplay - $addedtopot)*-1);
	$csrf=request_csrf_code('lottery_buy');
	if ($chance == 1)	//Winner, winner, chicken dinner!!
	{
	    $winnings=shortNumberParse($set['lotterycash']);
	    $newPot = round($set['lotterycash'] / 100);
	    if ((!$newPot) || ($newPot < $minimumpot))
	        $newPot = $minimumpot;
		$api->UserGiveItem($userid,160,1);
		alert('success',"Success!","You paid the fee and won the raffle's prize pot of {$winnings} Copper Coins! Congratulations!",true,"?action=play&verf={$csrf}","Play Again");
		//Adds money!
		$api->UserGiveCurrency($userid, 'primary', $set['lotterycash']);
		//Sets the pot back to the minimum.
		$db->query("UPDATE `settings` SET `setting_value` = {$newPot} WHERE `setting_id` = {$lotteryid}");
		$userLink="<a href='profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}]";
		$text = "The Chivalry is Dead Raffle has been won by {$userLink}. They received {$winnings} Copper Coins. A new raffle has started, beginning at " . shortNumberParse($newPot) . " Copper Coins. Best of luck to everyone!";
		$api->GameAddAnnouncement($text);
		$db->query("UPDATE `settings` SET `setting_value` = 1000 WHERE `setting_name` = 'raffle_chance'");
        $db->query("UPDATE `settings` SET `setting_value` = {$userid} WHERE `setting_name` = 'raffle_last_winner'");\
		addToEconomyLog('Gambling', 'copper', $set['lotterycash']);
		addToEconomyLog('Gambling', 'copper', $newPot);
	}
	if ($chance >= 2)	//Loser, loser, someone's got a bruiser!
	{
		alert('danger',"Uh Oh!","You paid the fee and did not win the raffle. Better luck next time, champ.",true,"?action=play&verf={$csrf}","Play Again");
	}
}
$h->endpage();