<?php
/*
	File:		users.php
	Created: 	8/30/2022 at 8:27PM Eastern Time
	Info: 		Lists all currently registered players in-game.
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

require("globals_auth.php");
//Require main functions file.
require "global_func.php";


//Page number
$st = (isset($_GET['st']) && is_numeric($_GET['st'])) ? abs($_GET['st']) : 0;

//Array for acceptable 'orderby' variable
$allowed_by = array('userid', 'username', 'level', 'primaryCurrencyHeld');

//If order by not set, set to userid.
$by = (isset($_GET['by']) && in_array($_GET['by'], $allowed_by, true)) ? $_GET['by'] : 'userid';

//Ascending or descending order?
$allowed_ord = array('asc', 'desc', 'ASC', 'DESC');

//If order not set, set to ascending
$ord = (isset($_GET['ord']) && in_array($_GET['ord'], $allowed_ord, true)) ? $_GET['ord'] : 'ASC';
echo "<h3>Userlist</h3>";
//Select user count
$cnt = $db->query("SELECT COUNT(`userid`) FROM `users_stats`");

$membs = $db->fetch_single($cnt);

//Pagination function!!
echo pagination(100, $membs, $st, "?by={$by}&ord={$ord}&st=");
//Ordering thing
echo "Order By:
<a href='?st={$st}&by=userid&ord={$ord}'>User ID</a>&nbsp;|
<a href='?st={$st}&by=level&ord={$ord}'>Level</a>&nbsp;|
<a href='?st={$st}&by=primaryCurrencyHeld&ord={$ord}'>Primary Currency Held</a>
<br />
<a href='?st={$st}&by={$by}&ord=asc'>Ascending</a> |
<a href='?st={$st}&by={$by}&ord=desc'>Descending</a>
<br /><br />";


//Select the users info
$q = $db->query("SELECT  uc.`username`, uc.`userid`, us.`level`, us.`primaryCurrencyHeld`
                FROM `users_stats` us
				left join `users_core` uc
				on us.userid = uc.userid ORDER BY `{$by}` {$ord}  LIMIT {$st}, 100");
$no1 = $st + 1;
$no2 = min($st + 100, $membs);

echo "
Showing users {$no1} to {$no2} by order of {$by} {$ord}.
<div class='container'>
<div class='row'>
		<div class='col-sm'>
		    <h4>User</h4>
		</div>
		<div class='col-sm'>
		    <h4>Primary Currency Held</h4>
		</div>
		<div class='col-sm'>
		    <h4>Level</h4>
		</div>
</div><hr />";
//Display the users info.

while ($r = $db->fetch_row($q)) {
    echo "	<div class='row'>
				<div class='col-sm'>
					<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
				</div>
				<div class='col-sm'>
					" . number_format($r['primaryCurrencyHeld']) . "
				</div>
				<div class='col-sm'>
					{$r['level']}
				</div>
			</div>
			<hr />";
}
$db->free_result($q);
echo '</div>';
//Pagination function!
echo pagination(100, $membs, $st, "?by={$by}&ord={$ord}&st=");
