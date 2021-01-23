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
	if ($deposit > $ir['secondary_currency']) 
	{
		alert('danger', "Uh Oh!", "You are trying to deposit more Chivalry Tokens than you current have.", false);
	}
	else
	{
		$gain = $deposit;
        $ir['tokenbank'] += $gain;
		$ir['secondary_currency'] -= $deposit;
        $api->UserTakeCurrency($userid, 'secondary', $deposit);
        $api->UserInfoSetStatic($userid, "tokenbank", $ir['tokenbank']);
		$api->SystemLogsAdd($userid, 'bank', "[Token Bank] Deposited " . number_format($deposit) . " Chivalry Tokens.");
        alert('success', "", "You hand over " . number_format($deposit) . " Chivalry Tokens to be
        deposited. You now have " . number_format($ir['tokenbank']) . " Chivalry Tokens in your Chivalry Token Bank Account.", false);
		$dojs=true;
	}
}
elseif (isset($_POST['withdraw']))
{
	$withdraw = abs((int) $_POST['withdraw']);
	if ($_POST['withdraw'] > $ir['tokenbank']) 
	{
        alert('danger', "Uh Oh!", "You are trying to withdraw more Chivalry Tokens than you currently have available in your account.", false);
    } 
	else 
	{
		$gain = $withdraw;
		$ir['tokenbank'] -= $gain;
		$ir['secondary_currency'] += $withdraw;
		$api->UserGiveCurrency($userid, 'secondary', $withdraw);
        $api->UserInfoSetStatic($userid, "tokenbank", $ir['tokenbank']);
		$api->SystemLogsAdd($userid, 'bank', "[Token Bank] Withdrew " . number_format($withdraw) . " Chivalry Tokens.");
		alert('success', "", "You have successfully withdrew " . number_format($withdraw) . " Chivalry Tokens from your
		    account. You have now have " . number_format($ir['tokenbank']) . " Chivalry Tokens remaining in your Chivalry Token Bank account..", false);
		$dojs=true;
	}
}
if (isset($dojs))
{
	?>
	<script>
		document.getElementById('wallet').innerHTML = <?php echo "'" . number_format($ir['secondary_currency']) . " Chivlary Tokens'" ?>;
		document.getElementById('bankacc').innerHTML = <?php echo "'" . number_format($ir['tokenbank']) . " Chivlary Tokens'" ?>;
		document.getElementById('bankacc2').innerHTML = <?php echo "'" . number_format($ir['tokenbank']) . "'" ?>;
		document.getElementById("form_bank_acc").value = <?php echo "'{$ir['tokenbank']}'" ?>;
		document.getElementById("form_bank_wallet").value = <?php echo "'{$ir['secondary_currency']}'" ?>;
		document.getElementById('ui_token').innerHTML = <?php echo "'" . number_format($ir['secondary_currency']) . "'" ?>;
	</script>
	<?php
}