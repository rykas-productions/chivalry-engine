<?php
/*
	File: staff/staff_logs.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: Allows staff to view the in-game logs
	Author: TheMasterGeneral
	Website: http://mastergeneral156.pcriot.com/
*/
require('sglobals.php');
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case "trainlogs":
    trainlogs();
    break;
case "attacklogs":
    attacklogs();
    break;
case "userlogs":
    userlogs();
    break;
case "loginlogs":
    loginlogs();
    break;
case "itemselllogs":
    itemselllogs();
    break;
case "equiplogs":
    equiplogs();
    break;
case "banklogs":
    banklogs();
    break;
case "crimelogs":
    crimelogs();
    break;
case "itemuselogs":
    itemuselogs();
    break;
case "itembuylogs":
    itembuylogs();
    break;
default:
    die();
    break;
}
function trainlogs()
{
	global $h,$lang,$db,$ir,$api,$userid;
	echo "<h3>Training Logs</h3><hr />";
    if (!isset($_GET['st']))
    {
        $_GET['st'] = 0;
    }
    $st = abs(intval($_GET['st']));
    $app = 100;
    $q = $db->query("SELECT COUNT(`log_id`)
    				 FROM `logs` WHERE `log_type` = 'training'");
    $logs = $db->fetch_single($q);
    $db->free_result($q);
    if ($logs == 0)
    {
        alert("info","Nothing!","No one has trained yet. When someone does, it will show up here.");
        return;
    }
    $pages = ceil($logs / $app);
    echo '<ul class="pagination">Pages:&nbsp;<br />';
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
		if ($s == $st)
        {
            echo "<li class='active'>";
        }

		else
		{
			echo "<li>";
		}
        echo "<a href='?action=trainlogs&st={$s}'>{$i}";
        echo "</li></a>&nbsp;";
        if ($i % 25 == 0)
        {
            echo "<br /></center>";
        }
    }
    echo "
	</ul>
    <br />
    <table class='table table-bordered table-hover'>
			<thead>
    		<tr>
    			<th>Time</th>
				<th>User</th>
    			<th>Stat Trained</th>
    		</tr>
			</thead>
			<tbody>
       ";
	$LogsQuery=$db->query("SELECT `log_user`,`log_text`,`log_time`,`username`,`userid` 
							FROM `logs` AS `lt`
							INNER JOIN `users` AS `u`
							ON `lt`.`log_user` = `u`.`userid`
							WHERE `log_type` = 'training'
							ORDER BY `log_time` DESC
							LIMIT $st, $app");
    while ($r = $db->fetch_row($LogsQuery))
    {
        
       echo "
			<tr>
				<td>
					" . date("F j, Y, g:i:s a", $r['log_time']) . "
				</td>
				<td>
					<a href='../profile.php?user={$r['log_user']}'>{$r['username']}</a> [{$r['log_user']}]
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
    <ul class='pagination'>Pages:<br />
       ";
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
		if ($s == $st)
        {
            echo "<li class='active'>";
        }

		else
		{
			echo "<li>";
		}
        echo "<a href='?action=trainlogs&st={$s}'>{$i}";
        echo "</li></a>&nbsp;";
        if ($i % 25 == 0)
        {
            echo "<br /></center>";
        }
    }
    $mypage = floor($_GET['st'] / 100) + 1;
	$api->SystemLogsAdd($userid,'staff',"Viewed Page #{$mypage} of the training logs.");
}
function attacklogs()
{
	global $db,$ir,$h,$lang,$userid,$api;
    echo "
	<h3>Attack Logs</h3>
	<hr />
 	  ";
    if (!isset($_GET['st']))
    {
        $_GET['st'] = 0;
    }
    $st = abs(intval($_GET['st']));
    $app = 100;
    $q = $db->query("SELECT COUNT(`log_id`)
    				 FROM `logs` WHERE `log_type` = 'attacking'");
    $attacks = $db->fetch_single($q);
    $db->free_result($q);
    if ($attacks == 0)
    {
        echo 'There have been no attacks yet.';
        return;
    }
    $pages = ceil($attacks / $app);
    echo '<ul class="pagination">Pages:&nbsp;<br />';
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
		if ($s == $st)
        {
            echo "<li class='active'>";
        }

		else
		{
			echo "<li>";
		}
        echo "<a href='?action=attacklogs&st={$s}'>{$i}";
        echo "</li></a>&nbsp;";
        if ($i % 25 == 0)
        {
            echo "<br /></center>";
        }
    }
    echo "
	</ul>
    <br />
    <table class='table table-bordered table-hover table-reponsive'>
    		<tr>
    			<th>Time</th>
    			<th>User</th>
    			<th>What Happened?</th>
    		</tr>
       ";
    $q =
            $db->query(
                    "SELECT `log_user`, `log_time`, `log_text`, `log_ip`
                     FROM `logs`
					 WHERE `log_type` = 'attacking'
                     ORDER BY `log_time` DESC
                     LIMIT $st, $app");
    while ($r = $db->fetch_row($q))
    {
		$un=$db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$r['log_user']}"));
        echo "
		<tr>
        	<td>" . date('F j, Y, g:i:s a', $r['log_time'])
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
    <ul class='pagination'>Pages:<br />
       ";
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
		if ($s == $st)
        {
            echo "<li class='active'>";
        }

		else
		{
			echo "<li>";
		}
        echo "<a href='?action=attacklogs&st={$s}'>{$i}";
        echo "</li></a>&nbsp;";
        if ($i % 25 == 0)
        {
            echo "<br /></center>";
        }
    }
    $mypage = floor($_GET['st'] / 100) + 1;
	$api->SystemLogsAdd($userid,'staff',"Viewed Page #{$mypage} of the attack logs.");
}
function userlogs()
{
	global $h,$lang,$db,$ir,$api,$userid;
	echo "<h3>User Logs</h3><hr />";
	if (isset($_GET['user']))
	{
		$user = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs(intval($_GET['user'])) : 0;
		if (!isset($_GET['st']))
		{
			$_GET['st'] = 0;
		}
		if ($user == 0)
		{
			echo "User does not exist.";
			die($h->endpage());
		}
		$st = abs(intval($_GET['st']));
		$app = 100;
		$q = $db->query("SELECT COUNT(`log_id`)
						 FROM `logs` WHERE `log_type` = 'training'");
		$logs = $db->fetch_single($q);
		$db->free_result($q);
		if ($logs == 0)
		{
			alert("info","Nothing!","This user hasn't done anything yet.");
			return;
		}
		$pages = ceil($logs / $app);
		echo '<ul class="pagination">Pages:&nbsp;<br />';
		for ($i = 1; $i <= $pages; $i++)
		{
			$s = ($i - 1) * $app;
			if ($s == $st)
			{
				echo "<li class='active'>";
			}

			else
			{
				echo "<li>";
			}
			echo "<a href='?action=userlogs&user={$user}&st={$s}'>{$i}";
			echo "</li></a>&nbsp;";
			if ($i % 25 == 0)
			{
				echo "<br /></center>";
			}
		}
		echo "
		</ul>
		<br />
		<table class='table table-bordered table-hover'>
				<thead>
				<tr>
					<th>Time</th>
					<th>User</th>
					<th>Stat Trained</th>
				</tr>
				</thead>
				<tbody>
		   ";
		$LogsQuery=$db->query("SELECT `log_type`,`log_text`,`log_time`,`username`,`userid` 
								FROM `logs` AS `lt`
								INNER JOIN `users` AS `u`
								ON `lt`.`log_user` = `u`.`userid`
								WHERE `log_user` = {$user}
								ORDER BY `log_time` DESC
								LIMIT $st, $app");
		while ($r = $db->fetch_row($LogsQuery))
		{
			
		   echo "
				<tr>
					<td>
						" . date("F j, Y, g:i:s a", $r['log_time']) . "
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
		<ul class='pagination'>Pages:<br />
		   ";
		for ($i = 1; $i <= $pages; $i++)
		{
			$s = ($i - 1) * $app;
			if ($s == $st)
			{
				echo "<li class='active'>";
			}

			else
			{
				echo "<li>";
			}
			echo "<a href='?action=userlogs&user={$user}&st={$s}'>{$i}";
			echo "</li></a>&nbsp;";
			if ($i % 25 == 0)
			{
				echo "<br /></center>";
			}
		}
		$mypage = floor($_GET['st'] / 100) + 1;
		$api->SystemLogsAdd($userid,'staff',"Viewed Page #{$mypage} of User ID {$user}'s user logs.");
	}
	else
	{
		echo "<table class='table table-bordered'>
		<form action='?action=userlog' method='get'>
			<input type='hidden' name='action' value='userlogs'>
			<tr>
				<th colspan='2'>
					{$lang['STAFF_LOGS_USERS_FORM']}
				</th>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_USERS_EDIT_USER']}
				</th>
				<td>
					" . user_dropdown('user') . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-default' value='{$lang['STAFF_LOGS_USERS_FORM_BTN']}' />
				</th>
			</tr>
		</form>
		<form method='get'>
			<input type='hidden' name='action' value='userlogs'>
			<tr>
				<th colspan='2'>
					{$lang['STAFF_USERS_EDIT_ELSE']}
				</th>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_USERS_EDIT_USER']}
				</th>
				<td>
					<input class='form-control' type='number' min='1' name='user' />
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-default' value='{$lang['STAFF_LOGS_USERS_FORM_BTN']}' />
				</th>
			</tr>
		</form>
	</table>";
	}
}
function loginlogs()
{
	global $db,$ir,$h,$lang,$userid,$api;
    echo "
	<h3>Login Logs</h3>
	<hr />
 	  ";
    if (!isset($_GET['st']))
    {
        $_GET['st'] = 0;
    }
    $st = abs(intval($_GET['st']));
    $app = 100;
    $q = $db->query("SELECT COUNT(`log_id`)
    				 FROM `logs` WHERE `log_type` = 'login'");
    $attacks = $db->fetch_single($q);
    $db->free_result($q);
    if ($attacks == 0)
    {
        echo 'There have been no logins yet.';
        return;
    }
    $pages = ceil($attacks / $app);
    echo '<ul class="pagination">Pages:&nbsp;<br />';
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
		if ($s == $st)
        {
            echo "<li class='active'>";
        }

		else
		{
			echo "<li>";
		}
        echo "<a href='?action=loginlogs&st={$s}'>{$i}";
        echo "</li></a>&nbsp;";
        if ($i % 25 == 0)
        {
            echo "<br /></center>";
        }
    }
    echo "
	</ul>
    <br />
    <table class='table table-bordered table-hover table-reponsive'>
    		<tr>
    			<th>Time</th>
    			<th>User</th>
    			<th>What Happened?</th>
    		</tr>
       ";
    $q =
            $db->query(
                    "SELECT `log_user`, `log_time`, `log_text`, `log_ip`
                     FROM `logs`
					 WHERE `log_type` = 'login'
                     ORDER BY `log_time` DESC
                     LIMIT $st, $app");
    while ($r = $db->fetch_row($q))
    {
		$un=$db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$r['log_user']}"));
        echo "
		<tr>
        	<td>" . date('F j, Y, g:i:s a', $r['log_time'])
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
    <ul class='pagination'>Pages:<br />
       ";
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
		if ($s == $st)
        {
            echo "<li class='active'>";
        }

		else
		{
			echo "<li>";
		}
        echo "<a href='?action=loginlogs&st={$s}'>{$i}";
        echo "</li></a>&nbsp;";
        if ($i % 25 == 0)
        {
            echo "<br /></center>";
        }
    }
    $mypage = floor($_GET['st'] / 100) + 1;
	$api->SystemLogsAdd($userid,'staff',"Viewed Page #{$mypage} of the login logs.");
}
function itemselllogs()
{
	global $db,$ir,$h,$lang,$userid,$api;
    echo "
	<h3>Item Selling Logs</h3>
	<hr />
 	  ";
    if (!isset($_GET['st']))
    {
        $_GET['st'] = 0;
    }
    $st = abs(intval($_GET['st']));
    $app = 100;
    $q = $db->query("SELECT COUNT(`log_id`)
    				 FROM `logs` WHERE `log_type` = 'itemsell'");
    $attacks = $db->fetch_single($q);
    $db->free_result($q);
    if ($attacks == 0)
    {
        echo "There haven't been any items sold back to the game yet.";
        return;
    }
    $pages = ceil($attacks / $app);
    echo '<ul class="pagination">Pages:&nbsp;<br />';
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
		if ($s == $st)
        {
            echo "<li class='active'>";
        }

		else
		{
			echo "<li>";
		}
        echo "<a href='?action=itemselllogs&st={$s}'>{$i}";
        echo "</li></a>&nbsp;";
        if ($i % 25 == 0)
        {
            echo "<br /></center>";
        }
    }
    echo "
	</ul>
    <br />
    <table class='table table-bordered table-hover table-reponsive'>
    		<tr>
    			<th>Time</th>
    			<th>User</th>
    			<th>What Happened?</th>
    		</tr>
       ";
    $q =
            $db->query(
                    "SELECT `log_user`, `log_time`, `log_text`, `log_ip`
                     FROM `logs`
					 WHERE `log_type` = 'itemsell'
                     ORDER BY `log_time` DESC
                     LIMIT $st, $app");
    while ($r = $db->fetch_row($q))
    {
		$un=$db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$r['log_user']}"));
        echo "
		<tr>
        	<td>" . date('F j, Y, g:i:s a', $r['log_time'])
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
    <ul class='pagination'>Pages:<br />
       ";
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
		if ($s == $st)
        {
            echo "<li class='active'>";
        }

		else
		{
			echo "<li>";
		}
        echo "<a href='?action=itemselllogs&st={$s}'>{$i}";
        echo "</li></a>&nbsp;";
        if ($i % 25 == 0)
        {
            echo "<br /></center>";
        }
    }
    $mypage = floor($_GET['st'] / 100) + 1;
	$api->SystemLogsAdd($userid,'staff',"Viewed Page #{$mypage} of the login logs.");
}
function equiplogs()
{
	global $db,$ir,$h,$lang,$userid,$api;
    echo "
	<h3>Equip Logs</h3>
	<hr />
 	  ";
    if (!isset($_GET['st']))
    {
        $_GET['st'] = 0;
    }
    $st = abs(intval($_GET['st']));
    $app = 100;
    $q = $db->query("SELECT COUNT(`log_id`)
    				 FROM `logs` WHERE `log_type` = 'equip'");
    $attacks = $db->fetch_single($q);
    $db->free_result($q);
    if ($attacks == 0)
    {
        echo "There haven't been any items equipped yet.";
        return;
    }
    $pages = ceil($attacks / $app);
    echo '<ul class="pagination">Pages:&nbsp;<br />';
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
		if ($s == $st)
        {
            echo "<li class='active'>";
        }

		else
		{
			echo "<li>";
		}
        echo "<a href='?action=equiplogs&st={$s}'>{$i}";
        echo "</li></a>&nbsp;";
        if ($i % 25 == 0)
        {
            echo "<br /></center>";
        }
    }
    echo "
	</ul>
    <br />
    <table class='table table-bordered table-hover table-reponsive'>
    		<tr>
    			<th>Time</th>
    			<th>User</th>
    			<th>What Happened?</th>
    		</tr>
       ";
    $q =
            $db->query(
                    "SELECT `log_user`, `log_time`, `log_text`, `log_ip`
                     FROM `logs`
					 WHERE `log_type` = 'equip'
                     ORDER BY `log_time` DESC
                     LIMIT $st, $app");
    while ($r = $db->fetch_row($q))
    {
		$un=$db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$r['log_user']}"));
        echo "
		<tr>
        	<td>" . date('F j, Y, g:i:s a', $r['log_time'])
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
    <ul class='pagination'>Pages:<br />
       ";
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
		if ($s == $st)
        {
            echo "<li class='active'>";
        }

		else
		{
			echo "<li>";
		}
        echo "<a href='?action=equiplogs&st={$s}'>{$i}";
        echo "</li></a>&nbsp;";
        if ($i % 25 == 0)
        {
            echo "<br /></center>";
        }
    }
    $mypage = floor($_GET['st'] / 100) + 1;
	$api->SystemLogsAdd($userid,'staff',"Viewed Page #{$mypage} of the equipping logs.");
}
function banklogs()
{
	global $db,$ir,$h,$lang,$userid,$api;
	$logname='bank';
    echo "
	<h3>Bank Logs</h3>
	<hr />
 	  ";
    if (!isset($_GET['st']))
    {
        $_GET['st'] = 0;
    }
    $st = abs(intval($_GET['st']));
    $app = 100;
    $q = $db->query("SELECT COUNT(`log_id`)
    				 FROM `logs` WHERE `log_type` = '{$logname}'");
    $attacks = $db->fetch_single($q);
    $db->free_result($q);
    if ($attacks == 0)
    {
        echo "There haven't been any bank transactions yet.";
        return;
    }
    $pages = ceil($attacks / $app);
    echo '<ul class="pagination">Pages:&nbsp;<br />';
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
		if ($s == $st)
        {
            echo "<li class='active'>";
        }

		else
		{
			echo "<li>";
		}
        echo "<a href='?action={$logname}logs&st={$s}'>{$i}";
        echo "</li></a>&nbsp;";
        if ($i % 25 == 0)
        {
            echo "<br /></center>";
        }
    }
    echo "
	</ul>
    <br />
    <table class='table table-bordered table-hover table-reponsive'>
    		<tr>
    			<th>Time</th>
    			<th>User</th>
    			<th>What Happened?</th>
    		</tr>
       ";
    $q =
            $db->query(
                    "SELECT `log_user`, `log_time`, `log_text`, `log_ip`
                     FROM `logs`
					 WHERE `log_type` = '{$logname}'
                     ORDER BY `log_time` DESC
                     LIMIT $st, $app");
    while ($r = $db->fetch_row($q))
    {
		$un=$db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$r['log_user']}"));
        echo "
		<tr>
        	<td>" . date('F j, Y, g:i:s a', $r['log_time'])
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
    <ul class='pagination'>Pages:<br />
       ";
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
		if ($s == $st)
        {
            echo "<li class='active'>";
        }

		else
		{
			echo "<li>";
		}
        echo "<a href='?action={$logname}logs&st={$s}'>{$i}";
        echo "</li></a>&nbsp;";
        if ($i % 25 == 0)
        {
            echo "<br /></center>";
        }
    }
    $mypage = floor($_GET['st'] / 100) + 1;
	$api->SystemLogsAdd($userid,'staff',"Viewed Page #{$mypage} of the {$logname} logs.");
}
function crimelogs()
{
	global $db,$ir,$h,$lang,$userid,$api;
	$logname='crime';
    echo "
	<h3>Crime Logs</h3>
	<hr />
 	  ";
    if (!isset($_GET['st']))
    {
        $_GET['st'] = 0;
    }
    $st = abs(intval($_GET['st']));
    $app = 100;
    $q = $db->query("SELECT COUNT(`log_id`)
    				 FROM `logs` WHERE `log_type` = '{$logname}'");
    $attacks = $db->fetch_single($q);
    $db->free_result($q);
    if ($attacks == 0)
    {
        echo "There haven't been any crime attempts yet.";
        return;
    }
    $pages = ceil($attacks / $app);
    echo '<ul class="pagination">Pages:&nbsp;<br />';
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
		if ($s == $st)
        {
            echo "<li class='active'>";
        }

		else
		{
			echo "<li>";
		}
        echo "<a href='?action={$logname}logs&st={$s}'>{$i}";
        echo "</li></a>&nbsp;";
        if ($i % 25 == 0)
        {
            echo "<br /></center>";
        }
    }
    echo "
	</ul>
    <br />
    <table class='table table-bordered table-hover table-reponsive'>
    		<tr>
    			<th>Time</th>
    			<th>User</th>
    			<th>What Happened?</th>
    		</tr>
       ";
    $q =
            $db->query(
                    "SELECT `log_user`, `log_time`, `log_text`, `log_ip`
                     FROM `logs`
					 WHERE `log_type` = '{$logname}'
                     ORDER BY `log_time` DESC
                     LIMIT $st, $app");
    while ($r = $db->fetch_row($q))
    {
		$un=$db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$r['log_user']}"));
        echo "
		<tr>
        	<td>" . date('F j, Y, g:i:s a', $r['log_time'])
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
    <ul class='pagination'>Pages:<br />
       ";
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
		if ($s == $st)
        {
            echo "<li class='active'>";
        }

		else
		{
			echo "<li>";
		}
        echo "<a href='?action={$logname}logs&st={$s}'>{$i}";
        echo "</li></a>&nbsp;";
        if ($i % 25 == 0)
        {
            echo "<br /></center>";
        }
    }
    $mypage = floor($_GET['st'] / 100) + 1;
	$api->SystemLogsAdd($userid,'staff',"Viewed Page #{$mypage} of the {$logname} logs.");
}
function itemuselogs()
{
	global $db,$ir,$h,$lang,$userid,$api;
	$logname='itemuse';
    echo "
	<h3>Item Use Logs</h3>
	<hr />
 	  ";
    if (!isset($_GET['st']))
    {
        $_GET['st'] = 0;
    }
    $st = abs(intval($_GET['st']));
    $app = 100;
    $q = $db->query("SELECT COUNT(`log_id`)
    				 FROM `logs` WHERE `log_type` = '{$logname}'");
    $attacks = $db->fetch_single($q);
    $db->free_result($q);
    if ($attacks == 0)
    {
        echo "No items have been used yet.";
        return;
    }
    $pages = ceil($attacks / $app);
    echo '<ul class="pagination">Pages:&nbsp;<br />';
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
		if ($s == $st)
        {
            echo "<li class='active'>";
        }

		else
		{
			echo "<li>";
		}
        echo "<a href='?action={$logname}logs&st={$s}'>{$i}";
        echo "</li></a>&nbsp;";
        if ($i % 25 == 0)
        {
            echo "<br /></center>";
        }
    }
    echo "
	</ul>
    <br />
    <table class='table table-bordered table-hover table-reponsive'>
    		<tr>
    			<th>Time</th>
    			<th>User</th>
    			<th>What Happened?</th>
    		</tr>
       ";
    $q =
            $db->query(
                    "SELECT `log_user`, `log_time`, `log_text`, `log_ip`
                     FROM `logs`
					 WHERE `log_type` = '{$logname}'
                     ORDER BY `log_time` DESC
                     LIMIT $st, $app");
    while ($r = $db->fetch_row($q))
    {
		$un=$db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$r['log_user']}"));
        echo "
		<tr>
        	<td>" . date('F j, Y, g:i:s a', $r['log_time'])
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
    <ul class='pagination'>Pages:<br />
       ";
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
		if ($s == $st)
        {
            echo "<li class='active'>";
        }

		else
		{
			echo "<li>";
		}
        echo "<a href='?action={$logname}logs&st={$s}'>{$i}";
        echo "</li></a>&nbsp;";
        if ($i % 25 == 0)
        {
            echo "<br /></center>";
        }
    }
    $mypage = floor($_GET['st'] / 100) + 1;
	$api->SystemLogsAdd($userid,'staff',"Viewed Page #{$mypage} of the item use logs.");
}
function itembuylogs()
{
	global $db,$ir,$h,$lang,$userid,$api;
	$logname='itembuy';
    echo "
	<h3>Item Buy Logs</h3>
	<hr />
 	  ";
    if (!isset($_GET['st']))
    {
        $_GET['st'] = 0;
    }
    $st = abs(intval($_GET['st']));
    $app = 100;
    $q = $db->query("SELECT COUNT(`log_id`)
    				 FROM `logs` WHERE `log_type` = '{$logname}'");
    $attacks = $db->fetch_single($q);
    $db->free_result($q);
    if ($attacks == 0)
    {
        echo "No items have been bought from the game yet.";
        return;
    }
    $pages = ceil($attacks / $app);
    echo '<ul class="pagination">Pages:&nbsp;<br />';
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
		if ($s == $st)
        {
            echo "<li class='active'>";
        }

		else
		{
			echo "<li>";
		}
        echo "<a href='?action={$logname}logs&st={$s}'>{$i}";
        echo "</li></a>&nbsp;";
        if ($i % 25 == 0)
        {
            echo "<br /></center>";
        }
    }
    echo "
	</ul>
    <br />
    <table class='table table-bordered table-hover table-reponsive'>
    		<tr>
    			<th>Time</th>
    			<th>User</th>
    			<th>What Happened?</th>
    		</tr>
       ";
    $q =
            $db->query(
                    "SELECT `log_user`, `log_time`, `log_text`, `log_ip`
                     FROM `logs`
					 WHERE `log_type` = '{$logname}'
                     ORDER BY `log_time` DESC
                     LIMIT $st, $app");
    while ($r = $db->fetch_row($q))
    {
		$un=$db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$r['log_user']}"));
        echo "
		<tr>
        	<td>" . date('F j, Y, g:i:s a', $r['log_time'])
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
    <ul class='pagination'>Pages:<br />
       ";
    for ($i = 1; $i <= $pages; $i++)
    {
        $s = ($i - 1) * $app;
		if ($s == $st)
        {
            echo "<li class='active'>";
        }

		else
		{
			echo "<li>";
		}
        echo "<a href='?action={$logname}logs&st={$s}'>{$i}";
        echo "</li></a>&nbsp;";
        if ($i % 25 == 0)
        {
            echo "<br /></center>";
        }
    }
    $mypage = floor($_GET['st'] / 100) + 1;
	$api->SystemLogsAdd($userid,'staff',"Viewed Page #{$mypage} of the item buy logs.");
}
$h->endpage();