<?php
/*
	File:		statistics.php
	Created:	Sep 4, 2022 at 7:03:07 PM Eastern Time
	Author:		TheMasterGeneral
	Website:	https://github.com/rykas-productions/chivalry-engine
	MIT License
	Copyright (c) 2022 TheMasterGeneral
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
require('globals_auth.php');
$totalPrimCurrencyOnPlayers = $db->fetch_row($db->query("SELECT SUM(`primaryCurrencyHeld`) FROM `users_stats`"));

$styl->createCard(primary_currency);
echo "test";
$styl->endCard();

$styl->createCard("Players");
$styl->endCard();