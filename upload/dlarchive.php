<?php
/*
	File:		dlarchive.php
	Created: 	4/4/2016 at 11:56PM Eastern Time
	Info: 		Allows players to download their inbox/outbox as an HTML file.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
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
			<td>" . decrypt_message($r['mail_text'],$r['userid'],$userid) . "</td>
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
				<td>" . decrypt_message($r['mail_text'],$userid,$r['userid']) . "</td>
			  </tr>";
    }
    $db->free_result($q);
    echo "</table>";
} else {
    header('HTTP/1.1 400 Bad Request');
    exit;
}