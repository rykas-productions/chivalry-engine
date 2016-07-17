<?php
require("globals.php");
$CurrentTime=time();
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
function csrf_error($goBackTo)
{
    global $h,$lang;
	echo "<div class='alert alert-danger'> <strong>{$lang['CSRF_ERROR_TITLE']}</strong> 
	{$lang['CSRF_ERROR_TEXT']} {$lang['CSRF_PREF_MENU']} <a href='preferences.php?action={$goBackTo}'>{$lang['GEN_HERE']}.</div>";
    $h->endpage();
    exit;
}
switch ($_GET['action'])
{
case 'buyad':
    news_buy();
    break;
default:
    news_home();
    break;
}
function news_home()
{
	global $db,$h,$lang,$CurrentTime;
	$AdsQuery=$db->query("SELECT * FROM `newspaper_ads` WHERE `news_end` > {$CurrentTime} ORDER BY `news_cost` ASC");
	if ($db->num_rows($AdsQuery) == 0)
	{
		alert("danger","No Ads!","There are current no advertisments listed! Perhaps you should <a href='?action=buyad'>buy one</a>?");
		die($h->endpage());
	}
	echo "<h3>Newspaper</h3>
	<small>Buy an ad <a href='?action=buyad'>here</a>.<hr />";
	echo "
		<table class='table table-bordered'>
			<thead>
				<tr>
					<th width='33%'>
						Ad Info
					</th>
					<th>
						Ad Text
					</th>
				</tr>
			</thead>
			<tbody>
	";
	while ($Ads=$db->fetch_row($AdsQuery))
	{
		$UserName=$db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$Ads['news_owner']}"));
		echo "	<tr>
					<td>
						Posted by: <a href='profile.php?user={$Ads['news_owner']}'>{$UserName}</a> [{$Ads['news_owner']}]<br />
						<small>Start Date: " . date('F j, Y g:i:s a', $Ads['news_start']) . "<br />
						End Date: " . date('F j, Y g:i:s a', $Ads['news_end']) . "</small>
					</td>
					<td>
						{$Ads['news_text']}
					</td>
				</tr>";
	}
	echo "</tbody></table>";
}
function news_buy()
{
	global $db,$userid,$ir,$h,$lang;
	echo "<h3>Buying an Ad</h3>
	" . alert("info","Reminder!","Remember, buying an add is subject to the game rules. If you post something here that will break a game rule, you will be warned and 
	your ad will be removed. If you find someone abusing the news paper, please let an admin know immediately!") . "<hr />";
	echo "
		<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<td width='33%'>
					Initial Cost<br />
					<small>Base ad cost. Higher number means ranked higher on ad list</small>
				</td>
				<td>
					<input type='number' min='750' name='init_cost' required='1' id='init' onkeyup='total_cost();' class='form-control'>
				</td>
			</tr>
			<tr>
				<td>
					Ad Length (Days)<br />
					<small>Each day adds 1,250 Primary Currency</small>
				</td>
				<td>
					<input type='number' min='1' name='ad_length' id='days' onkeyup='total_cost();' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<td>
					Ad Text<br />
					<small>Each character is worth 5 Primary Currency</small>
				</td>
				<td>
					<textarea class='form-control' name='ad_text' id='chars' onkeyup='total_cost();' rows='5' required='1'></textarea>
				</td>
			</tr>
			<tr>
				<td>
					Total Ad Cost
				</td>
				<td>
					<input type='number' name='ad_cost' id='output' readonly='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-default' value='Submit Ad'>
				</td>
			</tr>
		</table>
		</form>";
}
?>
	<script>
	function total_cost()
	{
		var day = (document.getElementById("days").value) * (1250);
		var init = (document.getElementById("init").value);
		var charlength = (document.getElementById("chars").length) * 5;
		var totalcost= (day + init + charlength);
		document.getElementById("output").innerHTML = totalcost;
	}
	</script>
<?php
$h->endpage();