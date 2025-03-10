<?php
$menuhide=1;
$nohdr=true;
require_once('../../globals.php');
if (isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD']))
{
    if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST')
    {
        // Ignore a GET request
        header('HTTP/1.1 400 Bad Request');
        exit;
    }
}
if (!is_ajax())
{
    header('HTTP/1.1 400 Bad Request');
    exit;
}
$estate=$db->fetch_row($db->query("SELECT * FROM `user_estates` WHERE `ue_id` = {$ir['estate']}"));
$edb=$db->fetch_row($db->query("SELECT * FROM `estates` WHERE `house_id` = {$estate['estate']}"));
if (isset($_POST['deposit']))
{
	$deposit = abs((int) $_POST['deposit']);
	if ($deposit > $ir['primary_currency']) 
	{
		alert('danger', "Uh Oh!", "You are trying to deposit more Copper Coins than you current have.", false);
	}
	elseif (($estate['vault'] + $deposit) > (calcVaultCapacity($estate['vaultUpgrade'], $edb['house_price'])))
	{
		alert('danger', "Uh Oh!", "You are trying to deposit Copper Coins than can actually fit in your vault.", false);
	}
	else
	{
		$gain = $deposit;
        $estate['vault'] += $gain;
		$ir['primary_currency'] -= $deposit;
		$api->UserTakeCurrency($userid, 'primary', $deposit);
        $db->query("UPDATE `user_estates` SET `vault` = {$estate['vault']} WHERE `ue_id` = {$estate['ue_id']}");
		//Log bank transaction.
        $api->SystemLogsAdd($userid, 'bank', "[Estate ID # {$estate['ue_id']}] Deposited " . number_format($deposit) . " Copper Coins.");
		alert('success', "", "You place " . number_format($deposit) . " Copper Coins in your {$edb['house_name']}'s vault. You now have " . number_format($estate['vault']) . " Copper Coins in this vault.", false);
		$dojs=true;
	}
}
elseif (isset($_POST['withdraw']))
{
	$withdraw = abs((int) $_POST['withdraw']);
	if ($withdraw > $estate['vault']) 
	{
        alert('danger', "Uh Oh!", "You are trying to withdraw more cash than you currently have available in your account.", false);
    } 
	else 
	{
		$gain = $withdraw;
		$estate['vault'] -= $gain;
		$ir['primary_currency'] += $withdraw;
		$api->UserGiveCurrency($userid, 'primary', $withdraw);
        $db->query("UPDATE `user_estates` SET `vault` = {$estate['vault']} WHERE `ue_id` = {$estate['ue_id']}");
		$api->SystemLogsAdd($userid, 'bank', "[Estate ID # {$estate['ue_id']}] Withdrew " . number_format($withdraw) . " Copper Coins.");
		alert('success', "", "You have taken " . number_format($withdraw) . " Copper Coins from your {$edb['house_name']}'s vault. You have now have " . number_format($estate['vault']) . " Copper Coins left in this vault.", false);
		$dojs=true;
	}
}
if (isset($dojs))
{
	?>
	<script>
		document.getElementById('wallet').innerHTML = <?php echo "'" . number_format($ir['primary_currency']) . " Copper Coins'" ?>;
		document.getElementById('bankacc').innerHTML = <?php echo "'" . number_format($estate['vault']) . " Copper Coins'" ?>;
		document.getElementById('bankacc2').innerHTML = <?php echo "'" . number_format($estate['vault']) . "'" ?>;
		document.getElementById("form_bank_acc").value = <?php echo "'{$estate['vault']}'" ?>;
		document.getElementById("form_bank_wallet").value = <?php echo "'{$ir['primary_currency']}'" ?>;
		document.getElementById('ui_copper').innerHTML = <?php echo "'" . number_format($ir['primary_currency']) . "'" ?>;
	</script>
	<?php
}