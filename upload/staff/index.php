<?php
/*
	File: 		staff/index.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Landing page for the staff panel.
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
require('sglobals.php');
echo "<h2>Staff Panel Index</h2>
	<hr />";
if ($api->user->getStaffLevel($userid, 'admin')) {
    $versq = $db->query("SELECT VERSION()");
    $MySQLIVersion = $db->fetch_single($versq);
    $db->free_result($versq);
    echo "
<div class='row'>
		<div class='col-sm'>
		    <h4>PHP Version</h4>
		</div>
		<div class='col-sm'>
		    <h4>Database Version</h4>
		</div>
		<div class='col-sm'>
		    <h4>Chivalry Engine Version</h4>
		</div>
		<div class='col-sm'>
		    <h4>Update Checker</h4>
		</div>
		<div class='col-sm'>
		    <h4>CE API Version</h4>
		</div>
</div><hr />
<div class='row'>
		<div class='col-sm'>
		    " . phpversion() . "
		</div>
		<div class='col-sm'>
		    " . $MySQLIVersion . "
		</div>
		<div class='col-sm'>
		    " . $set['Version_Number'] . "
		</div>
		<div class='col-sm'>
		    " . getEngineVersion() . "
		</div>
		<div class='col-sm'>
		    " . $api->game->returnAPIVersion() . "
		</div>
</div><hr />";
}
//Shown to normie staff...
echo "<br />
		<textarea class='form-control' readonly='1' rows='5'>{$set['staff_text']}</textarea>
		<br />";
if ($api->user->getStaffLevel($userid, 'admin')) {
    echo "
    <div class='row'>
            <div class='col-sm'>
                <h4>Timestamp</h4>
            </div>
            <div class='col-sm'>
                <h4>Staff</h4>
            </div>
            <div class='col-sm'>
                <h4>Action</h4>
            </div>
            <div class='col-sm'>
                <h4>IP Address</h4>
            </div>
    </div><hr />";
    $q =
        $db->query(
            "SELECT `log_user`, `log_text`, `log_time`, `log_ip`, `username`
							 FROM `logs` AS `s`
							 INNER JOIN `users` AS `u`
							 ON `s`.`log_user` = `u`.`userid`
							 WHERE `log_type` = 'staff'
							 ORDER BY `s`.`log_time` DESC
							 LIMIT 15");
    while ($r = $db->fetch_row($q)) {
        echo "
         <div class='row'>
            <div class='col-sm'>
                " . dateTimeParse($r['log_time']) . "
            </div>
            <div class='col-sm'>
                <a href='../profile.php?user={$r['log_user']}'>{$r['username']}</a> [{$r['log_user']}]
            </div>
            <div class='col-sm'>
                {$r['log_text']}
            </div>
            <div class='col-sm'>
                {$r['log_ip']}
            </div>
        </div><hr />";
    }
    $db->free_result($q);
}
$h->endpage();