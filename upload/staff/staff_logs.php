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
case "resetperm":
    resetperm();
    break;
default:
    home();
    break;
}
function home()
{
	global $h,$lang;
	echo "<h3>Game Logs</h3><hr />";
	echo"
	<table class='table table-bordered'>
		<tr>
			<td>
				<a href='?action=trainlogs'>Training Logs</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='?action=attacklogs'>Attack Logs</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='#'>Banking Logs</a>
			</td>
		</tr>
	</table>";
}
function trainlogs()
{
	global $h,$lang,$db,$ir;
	echo "<h3>Training Logs</h3><hr />";
    if (!isset($_GET['st']))
    {
        $_GET['st'] = 0;
    }
    $st = abs(intval($_GET['st']));
    $app = 100;
    $q = $db->query("SELECT COUNT(`log_id`)
    				 FROM `logs_training`");
    $logs = $db->fetch_single($q);
    $db->free_result($q);
    if ($logs == 0)
    {
        alert("info","Nothing (Yet)","No one has trained yet. When someone does, it will show up here.");
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
    			<th>Stain Gain</th>
    		</tr>
			</thead>
			<tbody>
       ";
	$LogsQuery=$db->query("SELECT `log_user`,`log_stat`,`log_gain`,`log_time`,`username`,`userid` 
							FROM `logs_training` AS `lt`
							INNER JOIN `users` AS `u`
							ON `lt`.`log_user` = `u`.`userid`
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
					{$r['log_stat']}
				</td>
				<td>
					{$r['log_gain']}
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
    stafflog_add("Viewed the Training Logs (Page $mypage)");
}
function attacklogs()
{
	global $db,$ir,$h,$lang;
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
    $q = $db->query("SELECT COUNT(`attacker`)
    				 FROM `attacklogs`");
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
    			<th>Attacker</th>
    			<th>Attacked</th>
    			<th>Who Won</th>
    			<th>What Happened</th>
    		</tr>
       ";
    $q =
            $db->query(
                    "SELECT `stole`, `result`, `attacked`, `attacker`, `time`,
                     `u1`.`username` AS `un_attacker`,
                     `u2`.`username` AS `un_attacked`
                     FROM `attacklogs` AS `a`
                     INNER JOIN `users` AS `u1`
                     ON `a`.`attacker` = `u1`.`userid`
                     INNER JOIN `users` AS `u2`
                     ON `a`.`attacked` = `u2`.`userid`
                     ORDER BY `a`.`time` DESC
                     LIMIT $st, $app");
    while ($r = $db->fetch_row($q))
    {
        echo "
		<tr>
        	<td>" . date('F j, Y, g:i:s a', $r['time'])
                . "</td>
        	<td><a href='../profile.php?user={$r['attacker']}'>{$r['un_attacker']}</a> [{$r['attacker']}]</td>
        	<td><a href='../profile.php?user={$r['attacked']}'>{$r['un_attacked']}</a> [{$r['attacked']}]</td>
           ";
        if ($r['result'] == "won")
        {
            echo "
			<td><a href='../profile.php?user={$r['attacker']}'>{$r['un_attacker']}</a></td>
			<td>
   			";
            if ($r['stole'] == -1)
            {
                echo "<a href='../profile.php?user={$r['attacker']}'>{$r['un_attacker']}</a> hospitalized <a href='../profile.php?user={$r['attacked']}'>{$r['un_attacked']}</a>.";
            }
            else if ($r['stole'] == -2)
            {
                echo "<a href='../profile.php?user={$r['attacker']}'>{$r['un_attacker']}</a> attacked <a href='../profile.php?user={$r['attacked']}'>{$r['un_attacked']}</a> and left them.";
            }
            else
            {
                echo "<a href='../profile.php?user={$r['attacker']}'>{$r['un_attacker']}</a> mugged "
                        . money_formatter($r['stole'])
                        . " from <a href='../profile.php?user={$r['attacked']}'>{$r['un_attacked']}</a>.";
            }
            echo '</td>';
        }
        else
        {
            echo "
			<td><a href='../profile.php?user={$r['attacked']}'>{$r['un_attacked']}</a></td>
			<td>Nothing</td>
   			";
        }
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
    stafflog_add("Viewed the attack logs (Page $mypage)");
}
$h->endpage();