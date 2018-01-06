<?php
require('globals.php');
if (isset($_GET['terminate']))
{
	$q=$db->query("SELECT * FROM `bank_investments` WHERE `userid` = {$userid}");
	if ($db->num_rows($q) == 0)
	{
		alert('danger',"Uh Oh!","You do not have an active investment at this time.",true,'bank.php');
		die($h->endpage());
	}
	$r=$db->fetch_row($q);
	alert('success',"Success!","Your investment has been terminated. {$r['amount']} Copper Coins have been refunded to your bank account.",true,'bank.php');
	$db->query("UPDATE `users` SET `bank` = `bank` + {$r['amount']} WHERE `userid` = {$userid}");
	$db->query("DELETE FROM `bank_investments` WHERE `userid` = {$userid}");
}
if (!isset($_POST['step']))
	$_POST['step']=0;
$q=$db->query("SELECT * FROM `bank_investments` WHERE `userid` = {$userid}");
if ($db->num_rows($q) > 0)
{
	alert('danger',"Uh Oh!","You already have an active investment.",true,'bank.php');
	die($h->endpage());
}
$duration=array(5,10,20,30);
if ($_POST['step'] == 0)
{
	echo "Please select how long you wish to invest your Copper Coins for. The interest rate is shown the button.<br />
	<form method='post'>
		<input type='hidden' name='step' value='1'>
		<input type='hidden' name='duration' value='5'>
		<input type='submit' class='btn btn-primary' value='5 Days - {$set['5day']}%'>
	</form>
	<form method='post'>
		<input type='hidden' name='step' value='1'>
		<input type='hidden' name='duration' value='10'>
		<input type='submit' class='btn btn-primary' value='10 Days - {$set['10day']}%'>
	</form>
	<form method='post'>
		<input type='hidden' name='step' value='1'>
		<input type='hidden' name='duration' value='20'>
		<input type='submit' class='btn btn-primary' value='20 Days - {$set['20day']}%'>
	</form>
	<form method='post'>
		<input type='hidden' name='step' value='1'>
		<input type='hidden' name='duration' value='30'>
		<input type='submit' class='btn btn-primary' value='30 Days - {$set['30day']}%'>
	</form>";
	$h->endpage();
}
if ($_POST['step'] == 1)
{
	$_POST['duration'] = abs($_POST['duration']);
	if (!in_array($_POST['duration'],$duration))
	{
		alert('danger',"Uh Oh!","You are trying to invest at an invalid duration.");
		die($h->endpage());
	}
	echo "Now, enter how many Copper Coins you wish to invest. You can only invest 
	what's in your bank account. You may only invest up to 1,000,000 Copper Coins at 
	this time. <b>You currently have " . number_format($ir['bank']) . " Copper Coins 
	in your bank account.</b><br />
	<form method='post'>
		<input type='hidden' value='2' name='step'>
		<input type='hidden' value='{$_POST['duration']}' name='duration'>
		<input type='number' value='{$ir['bank']}' name='invest' min='1' max='1000000' required='1' class='form-control'>
		<input type='submit' class='bnt btn-primary' value='Invest'>
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
	if ($_POST['invest'] > 1000000)
	{
		alert('danger',"Uh Oh!","You may only invest up to 1,000,000 Copper Coins at a time.");
		die($h->endpage());
	}
	if ($_POST['duration'] == 5)
		$percent=$set['5day'];
	if ($_POST['duration'] == 10)
		$percent=$set['10day'];
	if ($_POST['duration'] == 20)
		$percent=$set['20day'];
	if ($_POST['duration'] == 30)
		$percent=$set['30day'];
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