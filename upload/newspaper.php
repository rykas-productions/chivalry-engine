<?php
/*
	File:		newspaper.php
	Created: 	4/5/2016 at 12:18AM Eastern Time
	Info: 		Allows players to place ads in a newspaper, and view
				currently running ads.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
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
		alert("danger",$lang['ERROR_GENERIC'],$lang['NP_ERROR'],false);
		die($h->endpage());
	}
	echo "<h3>{$lang['NP_TITLE']}</h3>
	<small>{$lang['NP_AD']} <a href='?action=buyad'>{$lang['GEN_HERE']}</a>.<hr />";
	echo "
		<table class='table table-bordered'>
			<thead>
				<tr>
					<th width='33%'>
						{$lang['NP_ADINFO']}
					</th>
					<th>
						{$lang['NP_ADTEXT']}
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
						{$lang['NP_ADINFO1']} <a href='profile.php?user={$Ads['news_owner']}'>{$UserName}</a> [{$Ads['news_owner']}]<br />
						<small>{$lang['NP_ADSTRT']}: " . DateTime_Parse($Ads['news_start']) . "<br />
						{$lang['NP_ADEND']}: " . date('F j, Y g:i:s a', $Ads['news_end']) . "</small>
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
	echo "<h3>{$lang['NP_BUY']}</h3>
	" . alert("info",$lang['ERROR_INFO'],$lang['NP_BUY_REMINDER'],false) . "<hr />";
	echo "
		<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<td width='33%'>
					{$lang['NP_BUY_TD1']}<br />
					<small>{$lang['NP_BUY_TD5']}</small>
				</td>
				<td>
					<input type='number' min='750' name='init_cost' required='1' id='init' onkeyup='total_cost();' class='form-control'>
				</td>
			</tr>
			<tr>
				<td>
					{$lang['NP_BUY_TD2']}<br />
					<small>{$lang['NP_BUY_TD6']}</small>
				</td>
				<td>
					<input type='number' min='1' name='ad_length' id='days' onkeyup='total_cost();' required='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<td>
					{$lang['NP_BUY_TD3']}<br />
					<small>{$lang['NP_BUY_TD7']}</small>
				</td>
				<td>
					<textarea class='form-control' name='ad_text' id='chars' onkeyup='total_cost();' rows='5' required='1'></textarea>
				</td>
			</tr>
			<tr>
				<td>
					{$lang['NP_BUY_TD4']}
				</td>
				<td>
					<input type='number' name='ad_cost' id='output' readonly='1' class='form-control'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-secondary' value='{$lang['NP_BUY_BTN']}'>
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