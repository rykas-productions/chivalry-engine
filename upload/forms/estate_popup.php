<?php
echo "
<div class='modal fade' id='estate_vault' tabindex='-2' role='dialog' aria-labelledby='estate_vault' aria-hidden='true'>
    <div class='modal-dialog modal-lg' role='document'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='addShortcutLabel'>{$edb['house_name']}'s Vault</h5>
                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                    <span aria-hidden='true'>&times;</span>
                </button>
            </div>
            <div class='modal-body'>";
                if ($estate['vaultUpgrade'] == 0)
				{
					alert('danger',"Uh Oh!","You must construct a vault before you can store Copper Coin at this property.",false);
					die($h->endpage());
				}
				echo "<div id='banksuccess'></div>
				<div class='row'>
					<div class='col-12'>
						<div class='card'>
							<div class='card-header'>
								Deposit (<span id='wallet'>" . number_format($ir['primary_currency']) . " Copper Coins</span>)
							</div>
							<div class='card-body'>
								<form method='post' id='estateBankDeposit' name='estateBankDeposit'>
									<div class='row'>
										<div class='col'>
											<input type='number' min='1' max='{$ir['primary_currency']}' class='form-control' id='form_bank_wallet' required='1' name='deposit' value='{$ir['primary_currency']}'>
										</div>
										<div class='col-5 col-sm-4 col-md-3'>
											<input type='submit' value='Deposit' class='btn btn-primary' id='estateDeposit'>
										</div>
									</div>
								</form>
							</div>
						</div>
						<br />
					</div>
					<div class='col-12'>
						<div class='card'>
							<div class='card-header'>
								Withdraw (<span id='bankacc'>" . number_format($estate['vault']) . " Copper Coins</span>)
							</div>
							<div class='card-body'>
								<form method='post' id='estateBankWithdraw' name='estateBankWithdraw'>
									<div class='row'>
										<div class='col'>
											<input type='number' min='1' max='{$estate['vault']}' class='form-control' required='1' id='form_bank_acc' name='withdraw' value='{$estate['vault']}'>
										</div>
										<div class='col-6 col-sm-4 col-md-3'>
											<input type='submit' value='Withdraw' class='btn btn-primary' id='estateWithdraw'>
										</div>
									</div>
										<small>Max: " . number_format(calcVaultCapacity($estate['vaultUpgrade'], $edb['house_price'])) . " Copper Coins</small>
								</form>
							</div>
						</div>
						<br />
					</div>
				</div>
			</div>
        </div>
    </div>
</div>
";