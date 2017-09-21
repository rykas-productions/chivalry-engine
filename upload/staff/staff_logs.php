<?php
/*
	File: staff/staff_logs.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: Allows staff to view the in-game logs
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/
require('sglobals.php');
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "traininglogs":
        logs('training');
        break;
    case "attackinglogs":
        logs('attacking');
        break;
    case "userlogs":
        userlogs();
        break;
    case "loginlogs":
        logs('login');
        break;
    case "itemselllogs":
        logs('itemsell');
        break;
    case "itemsendlogs":
        logs('itemsend');
        break;
    case "equiplogs":
        logs('equip');
        break;
    case "banklogs":
        logs('bank');
        break;
    case "crimelogs":
        logs('crime');
        break;
    case "itemuselogs":
        logs('itemuse');
        break;
    case "itembuylogs":
        logs('itembuy');
        break;
    case "itemmarketlogs":
        logs('imarket');
        break;
    case "stafflogs":
        logs('staff');
        break;
    case "alllogs":
        alllogs();
        break;
    case "verifylogs":
        logs('verify');
        break;
    case "travellogs":
        logs('travel');
        break;
    case "spylogs":
        logs('spy');
        break;
    case "gamblinglogs":
        logs('gambling');
        break;
    case "fedjaillogs":
        logs('fedjail');
        break;
    case "pokes":
        logs('pokes');
        break;
    case "guilds":
        logs('guilds');
        break;
    case "level":
        logs('level');
        break;
    case "guildvault":
        logs('guild_vault');
        break;
    case "temple":
        logs('temple');
        break;
    case "secmarket":
        logs('secmarket');
        break;
    case "mining":
        logs('mining');
        break;
    case "forumwarn":
        logs('forumwarn');
        break;
    case "forumban":
        logs('forumban');
        break;
    case "donatelogs":
        logs('donate');
        break;
    case "rrlogs":
        logs('rr');
        break;
    case "mail":
        maillogs();
        break;
    default:
        alert('danger', "Uh Oh!", "Please select a valid action to perform.", true, 'index.php');
        die($h->endpage());
        break;
}
function logs($name)
{
    global $db, $userid, $api;
    $logname = $name;
    $ParsedName = ucwords($name);
    echo "
	<h3>{$ParsedName} Logs</h3>
	<hr />
 	  ";
    if (!isset($_GET['st'])) {
        $_GET['st'] = 0;
    }
    $st = abs(intval($_GET['st']));
    $app = 100;
    $q = $db->query("SELECT COUNT(`log_id`) FROM `logs` WHERE `log_type` = '{$logname}'");
    $attacks = $db->fetch_single($q);
    $db->free_result($q);
    if ($attacks == 0) {
        alert('danger', "Uh Oh!", "There doesn't appear to be anything in the {$ParsedName} logs.", true, 'index.php');
        return;
    }
    $pages = ceil($attacks / $app);
    echo "<nav>";
    echo "Page <br /><ul class='pagination'>";
    for ($i = 1; $i <= $pages; $i++) {
        $s = ($i - 1) * $app;
        if ($s == $st) {
            echo "<li class='page-item active'>";
        } else {
            echo "<li class='page-item'>";
        }
        echo "<a class='page-link' href='?action={$logname}logs&st={$s}'>{$i}";
        echo "</li></a>&nbsp;";
    }
    echo "
	</ul>
	</nav>
    <br />
    <table class='table table-bordered table-hover table-striped'>
    		<tr>
    			<th>Log Time</th>
    			<th>User</th>
    			<th>Log Content</th>
    		</tr>
       ";
    $q =
        $db->query(
            "SELECT `log_user`, `log_time`, `log_text`, `log_ip`
                     FROM `logs`
					 WHERE `log_type` = '{$logname}'
                     ORDER BY `log_time` DESC
                     LIMIT $st, $app");
    while ($r = $db->fetch_row($q)) {
        $un = $db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$r['log_user']}"));
        echo "
		<tr>
        	<td>" . DateTime_Parse($r['log_time'])
            . "</td>
        	<td><a href='../profile.php?user={$r['log_user']}'>{$un}</a> [{$r['log_user']}]</td>
        	<td>{$r['log_text']}</td>
           ";
        echo '</tr>';
    }
    $db->free_result($q);
    echo "
    </table>
    <center>
	<nav>
   Page <br /><ul class='pagination'>
       ";
    for ($i = 1; $i <= $pages; $i++) {
        $s = ($i - 1) * $app;
        if ($s == $st) {
            echo "<li class='page-item active'>";
        } else {
            echo "<li class='page-item'>";
        }
        echo "<a class='page-link' href='?action={$logname}logs&st={$s}'>{$i}";
        echo "</li></a>&nbsp;";
    }
    echo "</ul></nav>";
    $mypage = floor($_GET['st'] / 100) + 1;
    $api->SystemLogsAdd($userid, 'staff', "Viewed Page #{$mypage} of the {$logname} logs.");
}

function userlogs()
{
    global $h, $db, $api, $userid;
    echo "<h3>User Logs</h3><hr />";
    if (isset($_GET['user'])) {
        $user = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs(intval($_GET['user'])) : 0;
        if (!isset($_GET['st'])) {
            $_GET['st'] = 0;
        }
        if ($user == 0) {
            alert('danger', "Uh Oh!", "Please specify a user you wish to view their logs.", true, 'index.php');
            die($h->endpage());
        }
        $st = abs(intval($_GET['st']));
        $app = 100;
        $q = $db->query("SELECT COUNT(`log_id`)
						 FROM `logs` WHERE `log_user` = {$_GET['user']}");
        $logs = $db->fetch_single($q);
        $db->free_result($q);
        if ($logs == 0) {
            alert("danger", "Uh Oh!", "This user does not have anything logged.", true, 'index.php');
            return;
        }
        $pages = ceil($logs / $app);
        echo "Pages <br /><nav><ul class='pagination'>";
        for ($i = 1; $i <= $pages; $i++) {
            $s = ($i - 1) * $app;
            if ($s == $st) {
                echo "<li class='page-item active'>";
            } else {
                echo "<li class='page-item'>";
            }
            echo "<a class='page-link' href='?action=userlogs&user={$user}&st={$s}'>{$i}";
            echo "</li></a>&nbsp;";
        }
        echo "
		</ul>
		</nav>
		<br />
		<table class='table table-bordered table-hover'>
				<thead>
				<tr>
					<th>Log Time</th>
					<th>User</th>
					<th>Log Content</th>
				</tr>
				</thead>
				<tbody>
		   ";
        $LogsQuery = $db->query("SELECT `log_type`,`log_text`,`log_time`,`username`,`userid`
								FROM `logs` AS `lt`
								INNER JOIN `users` AS `u`
								ON `lt`.`log_user` = `u`.`userid`
								WHERE `log_user` = {$user}
								ORDER BY `log_time` DESC
								LIMIT $st, $app");
        while ($r = $db->fetch_row($LogsQuery)) {

            echo "
				<tr>
					<td>
						" . DateTime_Parse($r['log_time']) . "
					</td>
					<td>
						<a href='../profile.php?user={$user}'>{$r['username']}</a> [{$user}]
					</td>
					<td>
						{$r['log_text']}
					</td>
				</tr>";
        }
        $db->free_result($LogsQuery);
        echo "
		</tbody>
		</table>
		<br />
		<center>
		Pages <nav><ul class='pagination'><br />
		   ";
        for ($i = 1; $i <= $pages; $i++) {
            $s = ($i - 1) * $app;
            if ($s == $st) {
                echo "<li class='page-item active'>";
            } else {
                echo "<li class='page-item'>";
            }
            echo "<a class='page-link' href='?action=userlogs&user={$user}&st={$s}'>{$i}";
            echo "</li></a>&nbsp;";
        }
        echo "</ul></nav>";
        $mypage = floor($_GET['st'] / 100) + 1;
        $api->SystemLogsAdd($userid, 'staff', "Viewed Page #{$mypage} of User ID {$user}'s user logs.");
        $h->endpage();
    } else {
        echo "<table class='table table-bordered'>
		<form action='?action=userlog' method='get'>
			<input type='hidden' name='action' value='userlogs'>
			<tr>
				<th colspan='2'>
					Select a user to view their logs.
				</th>
			</tr>
			<tr>
				<th>
					User
				</th>
				<td>
					" . user_dropdown('user') . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='View Logs' />
				</th>
			</tr>
		</form>
		<form method='get'>
			<input type='hidden' name='action' value='userlogs'>
			<tr>
				<th colspan='2'>
					Alternatively, you may enter a User ID
				</th>
			</tr>
			<tr>
				<th>
					User
				</th>
				<td>
					<input class='form-control' type='number' min='1' name='user' />
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='View Logs' />
				</th>
			</tr>
		</form>
	</table>";
    }
}

function alllogs()
{
    global $db, $userid, $api;
    $logname = 'all';
    echo "
	<h3>All Game Logs</h3>
	<hr />
 	  ";
    if (!isset($_GET['st'])) {
        $_GET['st'] = 0;
    }
    $st = abs(intval($_GET['st']));
    $app = 100;
    $q = $db->query("SELECT COUNT(`log_id`) FROM `logs`");
    $attacks = $db->fetch_single($q);
    $db->free_result($q);
    if ($attacks == 0) {
        alert('danger', "Uh Oh!", "There haven't been any game actions yet.", true, 'index.php');
        return;
    }
    $pages = ceil($attacks / $app);
    echo "<nav>Pages <ul class='pagination'><br />";
    for ($i = 1; $i <= $pages; $i++) {
        $s = ($i - 1) * $app;
        if ($s == $st) {
            echo "<li class='page-item active'>";
        } else {
            echo "<li class='page-item'>";
        }
        echo "<a class='page-link' href='?action={$logname}logs&st={$s}'>{$i}";
        echo "</li></a>&nbsp;";
    }
    echo "
	</ul>
	</nav>
    <br />
    <table class='table table-bordered table-hover table-striped'>
    		<tr>
    			<th>Log Time</th>
    			<th>User</th>
    			<th>Log Content</th>
    		</tr>
       ";
    $q =
        $db->query(
            "SELECT `log_user`, `log_time`, `log_text`, `log_ip`
                     FROM `logs`
                     ORDER BY `log_time` DESC
                     LIMIT $st, $app");
    while ($r = $db->fetch_row($q)) {
        $un = $db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$r['log_user']}"));
        echo "
		<tr>
        	<td>" . DateTime_Parse($r['log_time'])
            . "</td>
        	<td><a href='../profile.php?user={$r['log_user']}'>{$un}</a> [{$r['log_user']}]</td>
        	<td>{$r['log_text']}</td>
           ";
        echo '</tr>';
    }
    $db->free_result($q);
    echo "
    </table>
    <center>Pages <br />
	<nav>
    <ul class='pagination'>
       ";
    for ($i = 1; $i <= $pages; $i++) {
        $s = ($i - 1) * $app;
        if ($s == $st) {
            echo "<li class='page-item active'>";
        } else {
            echo "<li class='page-item'>";
        }
        echo "<a class='page-link' href='?action={$logname}logs&st={$s}'>{$i}";
        echo "</li></a>&nbsp;";
    }
    echo "</nav>";
    $mypage = floor($_GET['st'] / 100) + 1;
    $api->SystemLogsAdd($userid, 'staff', "Viewed Page #{$mypage} of the game logs.");
}

function maillogs()
{
    global $db, $userid, $api;
    $logname = 'Mail';
    $ParsedName = ucwords('Mail');
    echo "
	<h3>{$ParsedName} Logs</h3>
	<hr />
 	  ";
    if (!isset($_GET['st'])) {
        $_GET['st'] = 0;
    }
    $st = abs(intval($_GET['st']));
    $app = 100;
    $q = $db->query("SELECT COUNT(`mail_id`) FROM `mail`");
    $attacks = $db->fetch_single($q);
    $db->free_result($q);
    if ($attacks == 0) {
        alert('danger', "Uh Oh!", "There doesn't appear to be any sent messages yet.", true, 'index.php');
        return;
    }
    $pages = ceil($attacks / $app);
    echo "<nav>";
    echo "Page <br /><ul class='pagination'>";
    for ($i = 1; $i <= $pages; $i++) {
        $s = ($i - 1) * $app;
        if ($s == $st) {
            echo "<li class='page-item active'>";
        } else {
            echo "<li class='page-item'>";
        }
        echo "<a class='page-link' href='?action=mail&st={$s}'>{$i}";
        echo "</li></a>&nbsp;";
    }
    echo "
	</ul>
	</nav>
    <br />
    <table class='table table-bordered table-hover table-striped'>
    		<tr>
    			<th>Time</th>
    			<th>Subject</th>
    			<th>Sender</th>
    			<th>Receiver</th>
    			<th>Message</th>
    		</tr>
       ";
    $q = $db->query("SELECT *
                     FROM `mail`
                     ORDER BY `mail_time` DESC
                     LIMIT $st, $app");
    while ($r = $db->fetch_row($q)) {
        $un = $db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$r['mail_from']}"));
        $un2 = $db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$r['mail_to']}"));
        echo "
		<tr>
        	<td>" . DateTime_Parse($r['mail_time']) . "</td>
        	<td>{$r['mail_subject']}</td>
        	<td><a href='../profile.php?user={$r['mail_from']}'>{$un}</a> [{$r['mail_from']}]</td>
        	<td><a href='../profile.php?user={$r['mail_to']}'>{$un2}</a> [{$r['mail_to']}]</td>
        	<td>{$r['mail_text']}</td>
           ";
        echo '</tr>';
    }
    $db->free_result($q);
    echo "
    </table>
    <center>
	<nav>
   Page <br /><ul class='pagination'>
       ";
    for ($i = 1; $i <= $pages; $i++) {
        $s = ($i - 1) * $app;
        if ($s == $st) {
            echo "<li class='page-item active'>";
        } else {
            echo "<li class='page-item'>";
        }
        echo "<a class='page-link' href='?action=mail&st={$s}'>{$i}";
        echo "</li></a>&nbsp;";
    }
    echo "</ul></nav>";
    $mypage = floor($_GET['st'] / 100) + 1;
    $api->SystemLogsAdd($userid, 'staff', "Viewed Page #{$mypage} of the {$logname} logs.");
}

$h->endpage();