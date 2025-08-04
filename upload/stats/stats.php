<?php
/*
	File: 		stats/stats.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Logic for the stats page.
	Author: 	TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine/
	
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
//Select count of players with and without a bank account
$NotOwnedBank = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `bank` = '-1' AND `user_level` != 'NPC'"));
$OwnedBank = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `bank` > '-1' AND `user_level` != 'NPC'"));

//Select count of players's gender
$Male = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `gender` = 'Male' AND `user_level` != 'NPC'"));
$Female = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `gender` = 'Female' AND `user_level` != 'NPC'"));

//Select the Total Primary Currency in the game.
$TotalPrimaryCurrency = $db->fetch_single($db->query("SELECT SUM(`primary_currency`) FROM `users` WHERE `user_level` != 'NPC'"));

//Select the total for primary currency in bank.
$TotalBank = $db->fetch_single($db->query("SELECT SUM(`bank`) FROM `users` WHERE `user_level` != 'NPC' AND `bank` > -1"));

//All the primary currency
$TotalBankandPC = $TotalBank + $TotalPrimaryCurrency;

//Select the Total Secondary Currency in the game.
$TotalSecondaryCurrency = $db->fetch_single($db->query("SELECT SUM(`secondary_currency`) FROM `users` WHERE `user_level` != 'NPC'"));

//Select total count of register users.
$TotalUserCount = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `user_level` != 'NPC'"));

//Figure out average primary currency per player by dividing the total primary currency by the total amount of users
//then round up.
$AveragePrimaryCurrencyPerPlayer = round($TotalPrimaryCurrency / $TotalUserCount);
$AverageBank = round($TotalBank / $TotalUserCount);

//Figure out average secondary currency per player by dividing the total secondary currency by the total amount of users
//then round up.
$AverageSecondaryCurrencyPerPlayer = round($TotalSecondaryCurrency / $TotalUserCount);

//Select the total amount of guilds in the game.
$TotalGuildCount = $db->fetch_single($db->query("SELECT COUNT(`guild_id`) FROM `guild`"));
