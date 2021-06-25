<?php
require('globals.php');
$calculatedMax = ceil(1 * levelMultiplier($ir['level']));
if (isset($_GET['terminate']))
{
	$_GET['terminate'] = (isset($_GET['terminate']) && is_numeric($_GET['terminate'])) ? abs($_GET['terminate']) : 0;
	$q=$db->query("/*qc=on*/SELECT * FROM `bank_investments` WHERE `userid` = {$userid} AND `invest_id` = {$_GET['terminate']}");
	if ($db->num_rows($q) == 0)
	{
		alert('danger',"Uh Oh!","This invest does not exist, or does not belong to you.",true,'bank.php');
		die($h->endpage());
	}
	$r=$db->fetch_row($q);
	alert('success',"Success!","Your investment has been terminated. " . number_format($r['amount']) . " Copper Coins have been refunded to your bank account.",true,'bank.php');
	$db->query("UPDATE `users` SET `bank` = `bank` + {$r['amount']} WHERE `userid` = {$userid}");
	$db->query("DELETE FROM `bank_investments` WHERE `userid` = {$userid} AND `invest_id` = {$_GET['terminate']}");
	die($h->endpage());
}
if (!isset($_POST['step']))
	$_POST['step']=0;
$q=$db->query("/*qc=on*/SELECT * FROM `bank_investments` WHERE `userid` = {$userid}");
if ($db->num_rows($q) >= $calculatedMax)
{
	alert('danger',"Uh Oh!","You may only have a maximum of {$calculatedMax} active investments at a time.",true,'bank.php');
	die($h->endpage());
}
$duration=array(5,10,20,30);
if ($_POST['step'] == 0)
{
	$set['5day'] = ($ir['vip_days'] > 0) ? $set['5day'] + 5 : $set['5day'];
	$set['10day'] = ($ir['vip_days'] > 0) ? $set['10day'] + 10 : $set['10day'];
	$set['20day'] = ($ir['vip_days'] > 0) ? $set['20day'] + 20 : $set['20day'];
	$set['30day'] = ($ir['vip_days'] > 0) ? $set['30day'] + 30 : $set['30day'];
	echo "Please select how long you wish to invest your Copper Coins for. The interest rate is shown the button. You receive better rates if you have VIP Days.<br />
	<div class='row'>
		<div class='col-6 col-sm-4 col-lg-3'>
			<form method='post'>
				<input type='hidden' name='step' value='1'>
				<input type='hidden' name='duration' value='5'>
				<input type='submit' class='btn btn-primary' value='5 Days - {$set['5day']}%'>
			</form>
			<br />
		</div>
		<div class='col-6 col-sm-4 col-lg-3'>
			<form method='post'>
				<input type='hidden' name='step' value='1'>
				<input type='hidden' name='duration' value='10'>
				<input type='submit' class='btn btn-primary' value='10 Days - {$set['10day']}%'>
			</form>
			<br />
		</div>
		<div class='col-6 col-sm-4 col-lg-3'>
			<form method='post'>
				<input type='hidden' name='step' value='1'>
				<input type='hidden' name='duration' value='20'>
				<input type='submit' class='btn btn-primary' value='20 Days - {$set['20day']}%'>
			</form>
			<br />
		</div>
		<div class='col-6 col-sm-4 col-lg-3'>
			<form method='post'>
				<input type='hidden' name='step' value='1'>
				<input type='hidden' name='duration' value='30'>
				<input type='submit' class='btn btn-primary' value='30 Days - {$set['30day']}%'>
			</form>
			<br />
		</div>
	</div>";
	$h->endpage();
}
if ($_POST['step'] == 1)
{
	$_POST['duration'] = abs($_POST['duration']);
	$max=10000000*levelMultiplier($ir['level']);
	$maxformat=number_format($max);
	if (!in_array($_POST['duration'],$duration))
	{
		alert('danger',"Uh Oh!","You are trying to invest at an invalid duration.");
		die($h->endpage());
	}
	echo "Now, enter how many Copper Coins you wish to invest. You can only invest 
	what's in your bank account. You may only invest up to {$maxformat} Copper Coins at 
	this time. <b>You currently have " . number_format($ir['bank']) . " Copper Coins 
	in your bank account.</b><br />
	<form method='post'>
		<input type='hidden' value='2' name='step'>
		<input type='hidden' value='{$_POST['duration']}' name='duration'>
		<input type='number' value='{$ir['bank']}' name='invest' min='1' max='{$max}' required='1' class='form-control'>
		<input type='submit' class='btn btn-primary' value='Invest'>
	</form>";
	$h->endpage();
}
if ($_POST['step'] == 2)
{
	$_POST['duration'] = abs($_POST['duration']);
	$_POST['invest'] = abs($_POST['invest']);
	if (!in_array($_POST['duration'],$duration))
	{
		alert('danger',"Uh Oh!","You are trying to invest at an invalid duration.");
		die($h->endpage());
	}
	if ($ir['bank'] < $_POST['invest'])
	{
		alert('danger',"Uh Oh!","You are trying to invest more Copper Coins than you currently have available to you.");
		die($h->endpage());
	}
	$formatted=number_format(10000000*levelMultiplier($ir['level']));
	if ($_POST['invest'] > (10000000*levelMultiplier($ir['level'])))
	{
		alert('danger',"Uh Oh!","You may only invest up to {$formatted} Copper Coins at a time.");
		die($h->endpage());
	}
	if ($_POST['duration'] == 5)
		$percent = ($ir['vip_days'] > 0) ? $set['5day'] + 5 : $set['5day'];
	if ($_POST['duration'] == 10)
		$percent = ($ir['vip_days'] > 0) ? $set['10day'] + 10 : $set['10day'];
	if ($_POST['duration'] == 20)
		$percent = ($ir['vip_days'] > 0) ? $set['20day'] + 20 : $set['20day'];
	if ($_POST['duration'] == 30)
		$percent = ($ir['vip_days'] > 0) ? $set['30day'] + 30 : $set['30day'];
	$db->query("INSERT INTO `bank_investments` 
				(`userid`, `amount`, `interest`, `days_left`) 
				VALUES 
				('{$userid}', '{$_POST['invest']}', '{$percent}', '{$_POST['duration']}')");
	$db->query("UPDATE `users` SET `bank` = `bank` - {$_POST['invest']} WHERE `userid` = {$userid}");
	alert('success',"Success!","You have successfully invested {$_POST['invest']} Copper Coins at 
	{$percent}% interest.  Your investment will finish in {$_POST['duration']} days. If you 
	terminate the investment before its finished, you will only be refunded the cash you invested.",true,'bank.php');
	$h->endpage();
}