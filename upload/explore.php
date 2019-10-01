<?php
/*
	File:		explore.php
	Created: 	9/29/2019 at 9:14PM Eastern Time
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
createThreeCols(createCard(
							"Markets and Shops",
							"<a href='#'>Item Market</a><br />
							<a href='#'>Secondary Currency Market</a>"
							),
				createCard(
							"Accounting and Money",
							"<a href='bank.php'>Bank</a><br />
							<a href='#'>Estate Agent</a><br />
							<a href='#'>Travel Agent</a>"
							),
				createCard(
							"Personal Work",
							"<a href='#'>Gym</a><br />
							<a href='#'>Crimes</a><br />
							<a href='#'>Academy</a><br />
							<a href='#'>Work</a>"
							)
						);
createThreeCols(createCard(
							"Game Administration",
							"<a href='#'>Player List</a><br />
							<a href='#'>Game Staff</a><br />
							<a href='#'>Federal Dungeon</a><br />
							<a href='#'>Game Statis</a><br />
							<a href='#'>Player Report</a><br />
							<a href='#'>Announcements</a><br />
							<a href='#'>Item Appendix</a><br />"
							),
				createCard(
							"High Risk Gambling",
							"<a href='#'>Slots</a><br />
							<a href='#'>Roulette</a><br />"
							),
				createCard(
							"Guild Territory",
							"<a href='#'>Known Guilds</a><br />
							<a href='#'>Known Guild Wars</a>"
							)
						);
createThreeCols("",
				createCard(
							"Social",
							"<a href='#'>Dungeon</a><br />
							<a href='#'>Infirmary</a><br />
							<a href='#'>In-Game Forums</a><br />
							<a href='#'>Newspaper</a><br />
							<a href='#'>Hall of Fame</a><br />
							<a href='#'>Polling Center</a><br />
							<a href='#'>Game Tutorial</a><br />"
							), ""
						);