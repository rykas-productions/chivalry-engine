<?php
$menuhide=1;
$nohdr=true;
require_once('../../globals.php');
if (isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD']))
{
    if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'GET')
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
switch ($_GET['action']) 
{
    case 'energy':
        energy();
        break;
    case 'brave':
        brave();
        break;
    case 'will':
        will();
        break;
	case 'willall':
        willall();
        break;
}

function energy()
{
    global $api, $userid, $set, $ir;
    //User has enough Chivalry Tokens to refill their energy.
    if ($api->UserHasCurrency($userid, 'secondary', $set['energy_refill_cost'])) 
	{
        //User's energy is already full.
        if ($api->UserInfoGet($userid, 'energy', true) == 100) 
		{
            alert('danger', "Uh Oh!", "You already have full energy.", false);
        } 
		else 
		{
			if (calculateLuck($userid))
			{
				//Refill the user's energy and take their Chivalry Tokens.
				$api->UserInfoSet($userid, 'energy', 100, true);
				alert('success', "Success!", "Luck is on your side today! You received a free energy refill.", false);
				$api->SystemLogsAdd($userid, 'temple', "Traded 0 Chivalry Tokens to refill their Energy.");

			}
			else
			{
				//Refill the user's energy and take their Chivalry Tokens.
				$api->UserInfoSet($userid, 'energy', 100, true);
				$api->UserTakeCurrency($userid, 'secondary', $set['energy_refill_cost']);
				alert('success', "Success!", "You have paid " . shortNumberParse($set['energy_refill_cost']) . " Chivalry Tokens to refill your energy.", false);
				$api->SystemLogsAdd($userid, 'temple', "Traded " . shortNumberParse($set['energy_refill_cost']) . " Chivalry Tokens to refill their Energy.");
				addToEconomyLog('Temple of Fortune', 'token', ($set['energy_refill_cost'])*-1);
			}
        }
    } 
	else 
	{
        alert('danger', "Uh Oh!", "You do not have enough Chivalry Tokens to refill your energy.", false);
    }
	updateUserInfo();
}

function brave()
{
    global $api, $userid, $ir, $set, $h, $ir;
    //User has enoguh Chivalry Tokens to refill their brave
    if ($api->UserHasCurrency($userid, 'secondary', $set['brave_refill_cost'])) {
        //User's brave is already full.
        if ($api->UserInfoGet($userid, 'brave', true) == 100) {
            alert('danger', "Uh Oh!", "You already have full Bravery.", false);
        } else {
			if (calculateLuck($userid))
			{
				//Refill the user's bravery by 5% and take their Chivalry Tokens.
				$api->UserInfoSet($userid, 'brave', 5, true);
				alert('success', "Success!", "Luck is on your side today! This bravery regeneration was free.", false);
				$api->SystemLogsAdd($userid, 'temple', "Traded 0 Chivalry Tokens to regenerate 5% Brave.");
			}
			else
			{
				//Refill the user's bravery by 5% and take their Chivalry Tokens.
				$api->UserInfoSet($userid, 'brave', 5, true);
				$api->UserTakeCurrency($userid, 'secondary', $set['brave_refill_cost']);
				alert('success', "Success!", "You have paid " . shortNumberParse($set['brave_refill_cost']) . " to regenerate 5% Bravery.", false);
				$api->SystemLogsAdd($userid, 'temple', "Traded " . shortNumberParse($set['brave_refill_cost']) . " Chivalry Tokens to regenerate 5% Brave.");
				addToEconomyLog('Temple of Fortune', 'token', ($set['brave_refill_cost'])*-1);
			}
        }
    } else {
        alert('danger', "Uh Oh!", "You do not have enough Chivalry Tokens to refill your Bravery.", false);
    }
    updateUserInfo();
}

function will()
{
    global $api, $userid, $set, $ir, $db, $h, $ir;
    //User has enough Chivalry Tokens to refill their will.
    if ($api->UserHasCurrency($userid, 'secondary', $set['will_refill_cost'])) {
        //User's will is already at 100%
        if (($api->UserInfoGet($userid, 'will', true) == 100) && ($ir['will_overcharge'] < time())) {
            alert('danger', "Uh Oh!", "You already have full Will.", false);
        } else {
			if ($ir['will'] > ($ir['maxwill'] * 10))
			{
				alert('danger',"Uh Oh!","You need to take a break.",true,'index.php');
				die($h->endpage());
			}
			if (calculateLuck($userid))
			{
				alert('success', "Success!", "Luck is on your side today! You received a free will regeneration!", false);
				$api->SystemLogsAdd($userid, 'temple', "Traded 0 Chivalry Tokens to regenerate 5% Will.");
			}
			else
			{
				$api->UserTakeCurrency($userid, 'secondary', $set['will_refill_cost']);
				alert('success', "Success!", "You have paid " . shortNumberParse($set['will_refill_cost']) . " Chivalry Tokens to regenerate 5% Will. You now have " . number_format($api->UserInfoGet($userid, 'will', true) + 5) . "% Will.", false);
				$api->SystemLogsAdd($userid, 'temple', "Traded " . shortNumberParse($set['will_refill_cost']) . " Chivalry Tokens to regenerate 5% Will.");
				addToEconomyLog('Temple of Fortune', 'token', ($set['will_refill_cost'])*-1);
			}
			$db->query("UPDATE `users` SET `will` = `will` + (`maxwill`/20) WHERE `userid` = {$userid}");
        }
    } else {
        alert('danger', "Uh Oh!", "You do have have enough Chivalry Tokens to refill your Will.", false);
    }
	updateUserInfo();
}

function willall()
{
    global $api, $userid, $set, $ir;
    //User has enough Chivalry Tokens to refill their will.
    if ($api->UserHasCurrency($userid, 'secondary', $set['will_refill_cost']*20)) {
        //User's will is already at 100%
        if ($api->UserInfoGet($userid, 'will', true) == 100) {
            alert('danger', "Uh Oh!", "You already have full Will.", false);
        } else {
			if (calculateLuck($userid))
			{
				$api->UserInfoSet($userid, 'will', 100, true);
				alert('success', "Success!", "Luck is on your side today! You received a free will regeneration!", false);
				$api->SystemLogsAdd($userid, 'temple', "Traded 0 Chivalry Tokens to regenerate 100% Will.");
			}
			else
			{
				//Refill the user's will by 5% and take their Chivalry Tokens.
				$api->UserInfoSet($userid, 'will', 100, true);
				$api->UserTakeCurrency($userid, 'secondary', $set['will_refill_cost']*20);
				alert('success', "Success!", "You have paid " . shortNumberParse($set['will_refill_cost']*20) . " Chivalry Tokens to fill your will.", false);
				$api->SystemLogsAdd($userid, 'temple', "Traded " . shortNumberParse($set['will_refill_cost']*20) . " Chivalry Tokens to regenerate 100% Will.");
				addToEconomyLog('Temple of Fortune', 'token', ($set['will_refill_cost']*20)*-1);
			}
        }
    } else {
        alert('danger', "Uh Oh!", "You do have have enough Chivalry Tokens to refill your Will.", false);
    }
    updateUserInfo();
}

function updateUserInfo()
{
    global $ir, $api, $userid;
    $newEnergyPerc = $api->UserInfoGet($userid, 'energy', true);
    $newEnergy = $api->UserInfoGet($userid, 'energy');
    
    $newWillPerc = $api->UserInfoGet($userid, 'will', true);
    $newWill = $api->UserInfoGet($userid, 'will');
    
    $newBravePerc = $api->UserInfoGet($userid, 'brave', true);
    $newBrave = $api->UserInfoGet($userid, 'brave');
    ?>
	<script>
		document.getElementById('gymEnergy').innerHTML = <?php echo $newEnergyPerc; ?>;
		document.getElementById('gymWill').innerHTML = <?php echo $newWillPerc; ?>;
		document.getElementById('trainTimes').value = <?php echo $newEnergy; ?>;
		document.getElementById('trainTimes').max = <?php echo $newEnergy; ?>;
		document.getElementById('trainTimesTotal').innerHTML = <?php echo number_format($newEnergy); ?>;
		document.getElementById('ui_token').innerHTML = <?php echo "'" . number_format($ir['secondary_currency']) . "'"; ?>;

		document.getElementById('ui_energy_perc').innerHTML = "<?php echo "{$newEnergyPerc}%"; ?>";
		document.getElementById('ui_energy_bar').style.width = "<?php echo "{$newEnergyPerc}%"; ?>";
		document.getElementById('ui_energy_bar_info').innerHTML = "<?php echo "{$newEnergyPerc}% (" . number_format($newEnergy) . " / " . number_format($ir['maxenergy']) . ")"; ?>";

		document.getElementById('ui_will_perc').innerHTML = "<?php echo "{$newWillPerc}%"; ?>";
		document.getElementById('ui_will_bar').style.width = "<?php echo "{$newWillPerc}%"; ?>";
		document.getElementById('ui_will_bar_info').innerHTML = "<?php echo "{$newWillPerc}% (" . number_format($newWill) . " / " . number_format($ir['maxwill']) . ")"; ?>";

		document.getElementById('ui_brave_perc').innerHTML = "<?php echo "{$newBravePerc}%"; ?>";
		document.getElementById('ui_brave_bar').style.width = "<?php echo "{$newBravePerc}%"; ?>";
		document.getElementById('ui_brave_bar_info').innerHTML = "<?php echo "{$newBravePerc}% (" . number_format($newBrave) . " / " . number_format($ir['maxbrave']) . ")"; ?>";		
	</script>
	<?php
}