<?php
/*
	File:		gym.php
	Created: 	10/6/2019 at 10:43AM Eastern Time
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
$moduleID=('gym');
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
		'statMultiplier' => 1.0,
		'itemRequired' => 0,
		'vipDaysRequired' => false
		);
		$defaultConfig = formatConfig($moduleConfigArray);
		writeConfigToDB($moduleID, $defaultConfig);
		echo "Installing default config...";
		headerRedirect("gym.php");
	}
}
//Check if an item is required to use this gym,
//and then verify if the player has that item.
if ($moduleConfig['itemRequired'] > 0)
{
	//TODO: Add code to check for item in inventory... when inventory is added.
}
$statNames = array(constant("stat_strength") => "strength", constant("stat_agility") => "agility", constant("stat_guard") => "guard", constant("stat_labor") => "labor");
echo "<h3>Gym</h3><br />
Select the stat you wish to train, and enter how many times you would like to train. You may increase your gains by buying better estates.<hr />
";
if (isset($_POST['stat']))
{
	$energyToUse=makeSafeInt($_POST['train']);
	$statToTrain=makeSafeText($_POST['stat']);
	if (!isset($statNames[$statToTrain])) 
	{
        dangerRedirect("You are attempting to train a stat that does not exist.");
        die($h->endHeaders());
    }
	$stat = $statNames[$statToTrain];
	if ($energyToUse > $ir['energy']) 
	{
        danger("You are attempting to train using more energy than you currently have.");
    }
	else
	{
		$gain = simulateGym($userid, $stat, $energyToUse, $moduleConfig['statMultiplier']);
		var_dump($gain);
		if ($gain == -1)
		{
			danger("There's an issue with the gym at the moment.");
			die($h->endHeaders());
		}
		$ir['energy'] -= $energyToUse;
		$ir[$stat] += $gain;
		success("You have trained your {$statToTrain} for " . number_format($energyToUse) . " minutes and 
			gained " . number_format($gain) . ". Your {$statToTrain} is now at 
			" . number_format($ir[$stat]) . " and you have 
			" . number_format($ir['energy']) . " energy remaining.");
	}
}
createFourCols(createPostForm('gym.php',array(array('hidden','stat','',constant("stat_strength")), array('number','train',constant("stat_strength") . ': ' . number_format($ir['strength']),$ir['energy'])), 'Train ' . constant("stat_strength")),
    createPostForm('gym.php',array(array('hidden','stat','',constant("stat_agility")), array('number','train',constant("stat_agility") . ': ' . number_format($ir['agility']),$ir['energy'])), 'Train ' . constant("stat_agility")),
    createPostForm('gym.php',array(array('hidden','stat','',constant("stat_guard")), array('number','train',constant("stat_guard") . ': ' . number_format($ir['guard']),$ir['energy'])), 'Train ' . constant("stat_guard")),
    createPostForm('gym.php',array(array('hidden','stat','',constant("stat_labor")), array('number','train',constant("stat_labor") . ': ' . number_format($ir['labor']),$ir['energy'])), 'Train ' . constant("stat_labor")));
$h->endHeaders();