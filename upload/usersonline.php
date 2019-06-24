<?php
/*
	File:		usersonline.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Lists the players currently in the viewing player's town.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
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
require('globals.php');

//Different options for different time periods. The GET is in minutes.
echo "<h3>Users Online List</h3><hr />
[<a href='?act=5'>5 Minutes</a>]
[<a href='?act=15'>15 Minutes</a>]
[<a href='?act=60'>1 Hour</a>]
[<a href='?act=1440'>1 Day</a>]<hr />";

//Time period isn't set, so set it to 15.
if (!isset($_GET['act'])) {
    $_GET['act'] = 15;
}
$_GET['act'] = (isset($_GET['act']) && is_numeric($_GET['act'])) ? abs($_GET['act']) : 15;
$last_on = time() - ($_GET['act'] * 60);

//Select all players on in the time period set in the GET.
$q = $db->query("SELECT * FROM `users` WHERE `laston` > {$last_on} ORDER BY `laston` DESC");
echo "<table class='table table-bordered table-striped'>
	<tr>
		<th>
			User
		</th>
		<th>
			Last Active
		</th>
	</tr>";
while ($r = $db->fetch_row($q)) {
    echo "<tr>
		<td>
			<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
		</td>
		<td>
			" . dateTimeParse($r['laston']) . "
		</td>
	</tr>";
}
echo "</table>";
$h->endpage();