<?php
/*
	File:		dlarchive.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows a player to download their inbox to an HTML file.
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
$nohdr = true;
require('globals.php');
if (!isset($_POST['archive'])) {
    $_POST['archive'] = '';
}
if ($_POST['archive'] == 'inbox') {
    header('Content-type: text/html');
    header('Content-Disposition: attachment; ' . 'filename="inbox_archive_' . $userid . '_' . time() . '.htm"');
    echo "<table width='75%' border='2'>
				<tr style='background:gray;'>
					<th>From</th>
					<th>Subject/Message</th>
				</tr>";
    $q = $db->query("SELECT `mail_time`, `mail_subject`, `mail_text`, `userid`, `username`
					FROM `mail` AS `m`
					LEFT JOIN `users` AS `u` ON `m`.`mail_from` = `u`.`userid`
					WHERE `m`.`mail_to` = $userid
					ORDER BY `mail_time` DESC");
    while ($r = $db->fetch_row($q)) {
        $sent = date('F j, Y, g:i:s a', $r['mail_time']);
        echo "<tr>
				<td>";
        if ($r['userid']) {
            echo "{$r['username']} [{$r['userid']}]";
        } else {
            echo "SYSTEM";
        }
        echo "</td>
			<td>{$r['mail_subject']}</td>
		</tr>
		<tr>
			<td>Sent at: $sent</td>
			<td>{$r['mail_text']}</td>
		</tr>";
    }
    $db->free_result($q);
    echo "</table>";
} else if ($_POST['archive'] == 'outbox') {
    header('Content-type: text/html');
    header(
        'Content-Disposition: attachment; ' . 'filename="outbox_archive_'
        . $userid . '_' . time() . '.htm"');
    echo "<table width='75%' border='2'>
			<tr style='background:gray;'>
				<th>To</th>
				<th>Subject/Message</th>
			</tr>";
    $q =
        $db->query(
            "SELECT `mail_time`, `mail_subject`, `mail_text`,
					`userid`, `username`
					FROM `mail` AS `m`
					LEFT JOIN `users` AS `u` ON `m`.`mail_to` = `u`.`userid`
					WHERE `m`.`mail_from` = $userid
					ORDER BY `mail_time` DESC");
    while ($r = $db->fetch_row($q)) {
        $sent = date('F j, Y, g:i:s a', $r['mail_time']);
        echo "<tr>
				<td>{$r['username']} [{$r['userid']}]</td>
				<td>{$r['mail_subject']}</td>
			  </tr>
			  <tr>
				<td>Sent at: $sent</td>
				<td>{$r['mail_text']}</td>
			  </tr>";
    }
    $db->free_result($q);
    echo "</table>";
} else {
    header('HTTP/1.1 400 Bad Request');
    exit;
}