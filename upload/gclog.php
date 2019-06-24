<?php
/*
	File:		gclog.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		The link the guild crime notification links to, allowing 
				the viewer to see the outcome of their guild's crime.
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
//Secure the GET
$gcid = filter_input(INPUT_GET, 'ID', FILTER_SANITIZE_NUMBER_INT) ?: 0;

//Verify that the GET is still set.
if (empty($gcid)) {
    alert('danger', "Uh Oh!", "Please specify the guild crime log you wish to view.", true, 'index.php');
    die($h->endpage());
}

//Select the log from the database.
$q = $db->query("SELECT * FROM `guild_crime_log` WHERE `gclID` = {$gcid}");

//Make sure the crime log exists.
if ($db->num_rows($q) == 0) {
    alert('danger', "Uh Oh!", "The guild crime log you wish to view does not exist.", true, 'index.php');
    die($h->endpage());
}
$r = $db->fetch_row($q);
$db->free_result($q);

//Check that the user is a part of the guild who committed this crime.
if ($ir['guild'] != $r['gclGUILD']) {
    alert('danger', "Uh Oh!", "You cannot view this log as you are not part of the guild who committed it.", true, 'index.php');
    die($h->endpage());
}
$name=$db->fetch_single($db->query("SELECT `gcNAME` FROM `guild_crimes` WHERE `gcID` = {$r['gclCID']}"));
echo "Here is the information on the crime.
<br />
<b>Crime:</b> {$name}
<br />
<b>Time Executed:</b> " . date('F j, Y, g:i:s a', $r['gclTIME']) . "
<br />
{$r['gclLOG']}
<br />
<br />
<b>Result:</b> {$r['gclRESULT']}
<br />
<b>{$_CONFIG['primary_currency']} Earned:</b> " . number_format($r['gclWINNING']);
$h->endpage();