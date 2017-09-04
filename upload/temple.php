<?php
/*
	File:		temple.php
	Created: 	4/5/2016 at 12:28AM Eastern Time
	Info: 		Allows players to spend their secondary currency on
				refilling their energy, will, and brave; along with
				spending it on IQ. Values are configurable. Check
				the staff panel.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
echo "<h3>Temple of Fortune</h3><hr />";
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'energy':
        energy();
        break;
    case 'brave':
        brave();
        break;
    case 'will':
        will();
        break;
    case 'iq':
        iq();
        break;
    default:
        home();
        break;
}
function home()
{
    global $set;
    echo "Welcome to the Temple of Fortune. Here you may spend your Secondary Currency as you see fit!";
    echo "<br />
	<a href='?action=energy'>Refill Energy - " . number_format($set['energy_refill_cost']) . " Secondary Currency</a><br />
	<a href='?action=brave'>Regenerate 5% Bravery - " . number_format($set['brave_refill_cost']) . " Secondary Currency</a><br />
	<a href='?action=will'>Regenerate 5% Will - " . number_format($set['will_refill_cost']) . " Secondary Currency</a><br />
	<a href='?action=iq'>Refill IQ - " . number_format($set['will_refill_cost']) . " Secondary Currency</a><br />";
}

function energy()
{
    global $api, $userid, $set;
    if ($api->UserHasCurrency($userid, 'secondary', $set['energy_refill_cost'])) {
        if ($api->UserInfoGet($userid, 'energy', true) == 100) {
            alert('danger', "Uh Oh!", "You already have full energy.", true, 'temple.php');
        } else {
            $api->UserInfoSet($userid, 'energy', 100, true);
            $api->UserTakeCurrency($userid, 'secondary', $set['energy_refill_cost']);
            alert('success', "Success!", "You have paid {$set['energy_refill_cost']} Secondary Currency to refill your energy.", true, 'temple.php');
            $api->SystemLogsAdd($userid, 'temple', "Traded {$set['energy_refill_cost']} Secondary Currency to refill their Energy.");
        }
    } else {
        alert('danger', "Uh Oh!", "You do not have enough Secondary Currency to refill your energy.", true, 'temple.php');
    }
}

function brave()
{
    global $api, $userid, $set;
    if ($api->UserHasCurrency($userid, 'secondary', $set['brave_refill_cost'])) {
        if ($api->UserInfoGet($userid, 'brave', true) == 100) {
            alert('danger', "Uh Oh!", "You already have full Bravery.", true, 'temple.php');
        } else {
            $api->UserInfoSet($userid, 'brave', 5, true);
            $api->UserTakeCurrency($userid, 'secondary', $set['brave_refill_cost']);
            alert('success', "Success!", "You have paid {$set['brave_refill_cost']} to regenerate 5% Bravery.", true, 'temple.php');
            $api->SystemLogsAdd($userid, 'temple', "Traded {$set['brave_refill_cost']} Secondary Currency to regenerate 5% Brave.");
        }
    } else {
        alert('danger', "Uh Oh!", "You do not have enough Secondary Currency to refill your Bravery.", true, 'temple.php');
    }
}

function will()
{
    global $api, $userid, $set;
    if ($api->UserHasCurrency($userid, 'secondary', $set['will_refill_cost'])) {
        if ($api->UserInfoGet($userid, 'will', true) == 100) {
            alert('danger', "Uh Oh!", "You already have full Will.", true, 'temple.php');
        } else {
            $api->UserInfoSet($userid, 'will', 5, true);
            $api->UserTakeCurrency($userid, 'secondary', $set['will_refill_cost']);
            alert('success', "Success!", "You have paid {$set['will_refill_cost']} Secondary Currency to regenerate 5% Will", true, 'temple.php');
            $api->SystemLogsAdd($userid, 'temple', "Traded {$set['will_refill_cost']} Secondary Currency to regenerate 5% Will.");
        }
    } else {
        alert('danger', "Uh Oh!", "You do have have enough Secondary Currency to refill your Will.", true, 'temple.php');
    }
}

function iq()
{
    global $db, $api, $userid, $ir, $h, $set;
    if (isset($_POST['iq'])) {
        $_POST['iq'] = (isset($_POST['iq']) && is_numeric($_POST['iq'])) ? abs($_POST['iq']) : '';
        if (empty($_POST['iq'])) {
            alert('danger', "Uh Oh!", "Please specify how much Secondary Currency you wish to trade in for IQ.");
            die($h->endpage());
        }
        $totalcost = $_POST['iq'] * $set['iq_per_sec'];
        if ($api->UserHasCurrency($userid, 'secondary', $_POST['iq']) == false) {
            alert('danger', "Uh Oh!", "You do not have enough Secondary Currency to buy that much IQ.");
            die($h->endpage());
        }
        $api->UserTakeCurrency($userid, 'secondary', $_POST['iq']);
        $db->query("UPDATE `userstats` SET `iq` = `iq` + {$totalcost} WHERE `userid` = {$userid}");
        alert('success', "Success!", "You have successfully traded " . number_format($_POST['iq']) . " Secondary Currency for " . number_format($totalcost) . " IQ Points.", true, 'temple.php');
        $api->SystemLogsAdd($userid, 'temple', "Traded {$_POST['iq']} Secondary Currency for {$totalcost} IQ.");
    } else {
        alert('info', "Information!", "You can trade in your Secondary Currency for IQ at a ratio of {$set['iq_per_sec']}
		per Secondary Currency. You currently have " . number_format($ir['secondary_currency']) . " Secondary Currency.", false);
        echo "<table class='table table-bordered'>
			<form method='post'>
			<tr>
				<th>
					Secondary Currency
				</th>
				<td>
					<input type='number' class='form-control' name='iq' min='1' max='{$ir['secondary_currency']}' required='1'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Trade for IQ'>
				</td>
			</tr>
			</form>
		</table>";
    }
}

$h->endpage();