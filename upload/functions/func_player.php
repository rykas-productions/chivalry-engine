<?php
/*
	File:		functions/func_player.php
	Created: 	9/30/2019 at 8:07PM Eastern Time
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
/**
 * @desc    Used to add Primary Currency to a user.
 * @param   string $user User UUID to add primary currency onto
 * @param   int $primaryCurrency Amount of primary currency to add to the user
 */
function addPlayerPrimaryCurrency($user, $primaryCurrency)
{
	global $db;
	$db->query("UPDATE `users_stats` 
				SET `primaryCurrencyHeld` = `primaryCurrencyHeld` + {$primaryCurrency} 
				WHERE `userid` = '{$user}'");
}

/**
 * @desc    Used to remove Primary Currency to a user.
 * @param   string $user User UUID to remove primary currency onto
 * @param   int $primaryCurrency Amount of primary currency to add to the user
 */
function removePlayerPrimaryCurrency($user, $primaryCurrency)
{
	global $db;
	$db->query("UPDATE `users_stats` 
				SET `primaryCurrencyHeld` = `primaryCurrencyHeld` - {$primaryCurrency} 
				WHERE `userid` = '{$user}'");
}

/**
 * @desc    Used to fetch a user's current Primary Currency.
 * @param   string $user User UUID to get Primary Currency.
 * @return  int User's current primary currency.
 */
function returnPlayerPrimaryCurrency($userid)
{
    
    global $db;
    $output = 
        $db->fetch_single(
            $db->query("SELECT primaryCurrencyHeld
                        FROM `users_stats`
        				where `userid` = '{$userid}'"));
    return $output;
}

/**
 * @desc    Train the player using the specified inputs.
 * @param   string $userTrain User UUID to train
 * @param   string $statToTrain Which stat to train (valid: strength/agility/guard/labor/iq)
 * @param   number $energyToTrain Amount of energy points to spend while training
 * @param   number $statMultiplier Multiplier for the stats gained
 * @return  number|mixed Stats gained by the player.
 */
function simulateGym($userTrain, $statToTrain, $energyToTrain, $statMultiplier = 1)
{
	global $db;
	$userTrain = makeSafeText($userTrain);
	$statToTrain = makeSafeText($statToTrain);
	$energyToTrain = makeSafeInt($energyToTrain);
	$statMultiplier = makeSafeInt($statMultiplier);
	if (empty($userTrain) || (empty($statToTrain)) || (empty($energyToTrain)))
		return 0;
	$statArray = array("strength", "agility", "guard", "labor", "iq");
	if (!in_array($statToTrain, $statArray))
		return -1;
	$udq = $db->query("SELECT * FROM `users_stats` WHERE `userid` = '{$userTrain}'");
	$userData = $db->fetch_row($udq);
	$gain = 0;
	for ($i = 0; $i < $energyToTrain; $i++) 
	{
		$gain += returnRandomNumber(1, 4) / returnRandomNumber(600, 1000) * returnRandomNumber(500, 1000) * (($userData['will'] + 25) / 175);
		$userData['will'] -= returnRandomNumber(1, 3);
		if ($userData['will'] < 0)
			$userData['will'] = 0;
	}
	$gain *= $statMultiplier;
	$gain = floor($gain);
	$db->query("UPDATE `users_stats` 
				SET `{$statToTrain}` = `{$statToTrain}` + {$gain},
				`will` = {$userData['will']},
				`energy` = `energy` - {$energyToTrain}
				WHERE `userid` = '{$userTrain}'");
	return $gain;
}