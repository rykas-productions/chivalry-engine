<?php
/*
	File:		slots.php
	Created: 	10/01/2019 at 9:00PM Eastern Time
	Author:		TheMasterGeneral
	Website: 	https://github.com/rykas-productions/chivalry-engine
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
$moduleID=('slots');
require('./globals_auth.php');
function initialize()
{
	global $moduleID;
	if (!readConfigFromDB($moduleID))
	{
		$moduleConfigArray=array(
		'moduleID' => $moduleID,
		'moduleAuthor' => 'TheMasterGeneral',
		'moduleURL' => 'https://github.com/rykas-productions/chivalry-engine',
		'moduleVersion' => 1,
		'maxBetPerLevel' => 750,
		'maxBetHardCap' => 100000,
		'threeSlotWinningMultipler' => 75,
		'twoSlotWinningMultipler' => 35
		);
		$defaultConfig = formatConfig($moduleConfigArray);
		writeConfigToDB($moduleID, $defaultConfig);
		echo "Installing default config...";
		headerRedirect("slots.php");
	}
}
function returnSlotMatchCount($slot1, $slot2, $slot3)
{
	if (($slot1 == $slot2) && ($slot1 == $slot3))
		return 3;
	elseif (($slot1 == $slot2) || ($slot1 == $slot3) || ($slot2 == $slot3))
		return 2;
	else
		return 0;
}
echo "<h3>Slots Machine</h3><hr />";
if (isset($_POST['bet']))
{
	$playerMaxBet=$moduleConfig['maxBetPerLevel'];
	$bet = makeSafeInt($_POST['bet']);
	if ($bet > $ir['primaryCurrencyHeld'])
	{
		dangerRedirect("You do not have enough currency to bet that much.");
		die($h->endHeaders());
	}
	if ($bet > $playerMaxBet)
	{
		dangerRedirect("You do not have cannot bet that high of a bet.");
		die($h->endHeaders());
	}
	if ($bet > $moduleConfig['maxBetHardCap'])
	{
		dangerRedirect("You do not have cannot bet that high of a bet.");
		die($h->endHeaders());
	}
	removePlayerPrimaryCurrency($userid, $bet);
	$slot = array();
	$slot[1] = returnRandomNumber(0, 9);
	$slot[2] = returnRandomNumber(0, 9);
	$slot[3] = returnRandomNumber(0, 9);
	$formatSlots = "{$slot[1]}, {$slot[2]}, {$slot[3]}";
	$starterText = "You place " . number_format($bet) . " into the machine pull the lever. The slots display {$formatSlots}.";
	$matchingSlots=returnSlotMatchCount($slot[1], $slot[2], $slot[3]);
	if ($matchingSlots == 3)
	{
		$gain = $bet * $moduleConfig['threeSlotWinningMultipler'];
		successRedirect("{$starterText} You've lined up 3 numbers and won " . number_format($gain) . ".");
	}
	elseif ($matchingSlots == 2)
	{
		$gain=$bet * $moduleConfig['twoSlotWinningMultipler'];
		successRedirect("{$starterText} You've lined up 2 numbers and won " . number_format($gain) . ".");
	}
	else
	{
		$gain = 0;
		dangerRedirect("{$starterText} No numbers have lined up. You lost " . number_format($bet) . ".");
	}
	if ($gain > 0)
		addPlayerPrimaryCurrency($userid, $gain);
}
else
{
	createPostForm('slots.php',array(array('number','bet','Your Bet')), 'Place Bet');
}