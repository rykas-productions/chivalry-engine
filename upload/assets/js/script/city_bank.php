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
if (isset($_POST['deposit']))
{
	$deposit = abs((int) $_POST['deposit']);
	if ($deposit > $ir['primary_currency']) 
	{
		alert('danger', "Uh Oh!", "You are trying to deposit more Copper Coins than you current have.", false);
	}
	else
	{
		$gain = $deposit;
        $ir['bank'] += $gain;
		$ir['primary_currency'] -= $deposit;
		$api->UserTakeCurrency($userid, 'primary', $deposit);
        $api->UserInfoSetStatic($userid, "bank", $ir['bank']);
		//Log bank transaction.
        $api->SystemLogsAdd($userid, 'bank', "[City] Deposited " . shortNumberParse($deposit) . " Copper Coins.");
        alert('success', "", "You hand over " . shortNumberParse($deposit) . " Copper Coins to be deposited. " . shortNumberParse($gain) . " Copper Coins 
		is added to your bank account. You now have " . shortNumberParse($ir['bank']) . " Copper Coins in your City Bank Account.", false);
		$dojs=true;
	}
}
elseif (isset($_POST['withdraw']))
{
	$withdraw = abs((int) $_POST['withdraw']);
	if ($withdraw > $ir['bank']) 
	{
        alert('danger', "Uh Oh!", "You are trying to withdraw more cash than you currently have available in your account.", false);
    } 
	else 
	{
		$gain = $withdraw;
		$ir['bank'] -= $gain;
		$ir['primary_currency'] += $withdraw;
		$api->UserGiveCurrency($userid, 'primary', $withdraw);
        $api->UserInfoSetStatic($userid, "bank", $ir['bank']);
        $api->SystemLogsAdd($userid, 'bank', "[City] Withdrew " . shortNumberParse($withdraw) . " Copper Coins.");
		alert('success', "", "You have successfully withdrew " . shortNumberParse($withdraw) . " Copper Coins from your
		    account. You have now have " . shortNumberParse($ir['bank']) . " Copper Coins left in your City Bank Account.", false);
		$dojs=true;
	}
}
if (isset($dojs))
{
	?>
	<script>
		document.getElementById('wallet').innerHTML = <?php echo "'" . shortNumberParse($ir['primary_currency']) . " Copper Coins'" ?>;
		document.getElementById('bankacc').innerHTML = <?php echo "'" . shortNumberParse($ir['bank']) . " Copper Coins'" ?>;
		document.getElementById('bankacc2').innerHTML = <?php echo "'" . shortNumberParse($ir['bank']) . "'" ?>;
		document.getElementById("form_bank_acc").value = <?php echo "'{$ir['bank']}'" ?>;
		document.getElementById("form_bank_wallet").value = <?php echo "'{$ir['primary_currency']}'" ?>;
		document.getElementById('ui_copper').innerHTML = <?php echo "'" . shortNumberParse($ir['primary_currency']) . "'" ?>;
	</script>
	<?php
}