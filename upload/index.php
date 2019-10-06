<?php
/*
	File:		index.php
	Created: 	9/22/2019 at 4:15PM Eastern Time
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
require('./globals_auth.php');
success("You have logged in as {$ir['username']}! More to come soon...");
createFourCols("Username: {$ir['username']} [{$ir['userid']}]","Email: {$ir['email']}", "Account Access: {$ir['staffLevel']}","IP: {$ir['lastActionIP']}");
echo "<hr />";
createFiveCols("Strength: {$ir['strength']}","Agility: {$ir['agility']}","Guard: {$ir['guard']}","IQ: {$ir['iq']}","Labor: {$ir['labor']}");
echo "<hr />";
createThreeCols("Level: {$ir['level']}", "Experience: {$ir['experience']}","Primary Currency: {$ir['primaryCurrencyHeld']}");
echo "<hr />";
createFourCols("Energy: {$ir['energy']} / {$ir['maxEnergy']}", "Brave: {$ir['brave']} / {$ir['maxBrave']}","Will: {$ir['will']} / {$ir['maxWill']}","HP: {$ir['hp']} / {$ir['maxHP']}");
echo "<hr />";