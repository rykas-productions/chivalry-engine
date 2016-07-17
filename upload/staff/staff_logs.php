<?php
/*
	File: staff/staff_logs.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: Allows staff to view the in-game logs
	Author: TheMasterGeneral
	Website: http://mastergeneral156.pcriot.com/
*/
require('sglobals.php');
echo "<h3>Logs</h3><hr />";
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case "trainlogs":
    trainlogs();
    break;
case "editperm":
    editperm();
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
	echo"
	<table class='table table-bordered'>
		<tr>
			<td>
				<a href='?action=trainlogs'>Training Logs</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='?action=editperm'>Attack Logs</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='?action=resetperm'>Banking Logs</a>
			</td>
		</tr>
	</table>";
}
function trainlogs()
{
	global $h,$lang,$db,$ir;
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
    			<th>User</th>
				<th>Time</th>
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
					<a href='profile.php?user={$r['log_user']}'>{$r['username']}</a> [{$r['log_user']}]
				</td>
				<td>
					" . date("F j, Y, g:i:s a", $r['log_time']) . "
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
$h->endpage();