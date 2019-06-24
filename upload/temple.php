<?php
/*
	File:		temple.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows players to trade their Secondary Currency for 
				energy/will/brave refills, or IQ.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
	MIT License

	Copyright (c) 2019 TheMasterGeneral

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
*/
require('globals.php');
echo "<h3>Temple of Fortune</h3><hr />";
//Set the GET to nothing if not set.
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
//GET switch to get current action.
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
    //Main index.
    global $set, $_CONFIG;
    echo "Welcome to the Temple of Fortune. Here you may spend your {$_CONFIG['secondary_currency']} as you see fit!";
    echo "<br />
	<a class='btn btn-outline-primary' href='?action=energy'>Refill Energy - " . number_format($set['energy_refill_cost']) . " {$_CONFIG['secondary_currency']}</a><br /><br />
	<a class='btn btn-outline-primary' href='?action=brave'>Regenerate 5% Bravery - " . number_format($set['brave_refill_cost']) . " {$_CONFIG['secondary_currency']}</a><br /><br />
	<a class='btn btn-outline-primary' href='?action=will'>Regenerate 5% Will - " . number_format($set['will_refill_cost']) . " {$_CONFIG['secondary_currency']}</a><br /><br />
	<a class='btn btn-outline-primary' href='?action=iq'>Refill {$_CONFIG['iq_stat']} - " . number_format($set['iq_per_sec']) . " {$_CONFIG['secondary_currency']}</a><br /><br />";
}

function energy()
{
    global $api, $userid, $set, $_CONFIG;
    //User has enough {$_CONFIG['secondary_currency']} to refill their energy.
    if ($api->user->hasCurrency($userid, 'secondary', $set['energy_refill_cost'])) {
        //User's energy is already full.
        if ($api->UserInfoGet($userid, 'energy', true) == 100) {
            alert('danger', "Uh Oh!", "You already have full energy.", true, 'temple.php');
        } else {
            //Refill the user's energy and take their {$_CONFIG['secondary_currency']}.
            $api->user->setInfo($userid, 'energy', 100, true);
            $api->user->takeCurrency($userid, 'secondary', $set['energy_refill_cost']);
            alert('success', "Success!", "You have paid {$set['energy_refill_cost']} {$_CONFIG['secondary_currency']} to refill your energy.", true, 'temple.php');
            $api->game->addLog($userid, 'temple', "Traded {$set['energy_refill_cost']} {$_CONFIG['secondary_currency']} to refill their Energy.");
        }
    } else {
        alert('danger', "Uh Oh!", "You do not have enough {$_CONFIG['secondary_currency']} to refill your energy.", true, 'temple.php');
    }
}

function brave()
{
    global $api, $userid, $set, $_CONFIG;
    //User has enoguh {$_CONFIG['secondary_currency']} to refill their brave
    if ($api->user->hasCurrency($userid, 'secondary', $set['brave_refill_cost'])) {
        //User's brave is already full.
        if ($api->UserInfoGet($userid, 'brave', true) == 100) {
            alert('danger', "Uh Oh!", "You already have full Bravery.", true, 'temple.php');
        } else {
            //Refill the user's bravery by 5% and take their {$_CONFIG['secondary_currency']}.
            $api->user->setInfo($userid, 'brave', 5, true);
            $api->user->takeCurrency($userid, 'secondary', $set['brave_refill_cost']);
            alert('success', "Success!", "You have paid {$set['brave_refill_cost']} to regenerate 5% Bravery.", true, 'temple.php');
            $api->game->addLog($userid, 'temple', "Traded {$set['brave_refill_cost']} {$_CONFIG['secondary_currency']} to regenerate 5% Brave.");
        }
    } else {
        alert('danger', "Uh Oh!", "You do not have enough {$_CONFIG['secondary_currency']} to refill your Bravery.", true, 'temple.php');
    }
}

function will()
{
    global $api, $userid, $set, $_CONFIG;
    //User has enough {$_CONFIG['secondary_currency']} to refill their will.
    if ($api->user->hasCurrency($userid, 'secondary', $set['will_refill_cost'])) {
        //User's will is already at 100%
        if ($api->UserInfoGet($userid, 'will', true) == 100) {
            alert('danger', "Uh Oh!", "You already have full Will.", true, 'temple.php');
        } else {
            //Refill the user's will by 5% and take their {$_CONFIG['secondary_currency']}.
            $api->user->setInfo($userid, 'will', 5, true);
            $api->user->takeCurrency($userid, 'secondary', $set['will_refill_cost']);
            alert('success', "Success!", "You have paid {$set['will_refill_cost']} {$_CONFIG['secondary_currency']} to regenerate 5% Will", true, 'temple.php');
            $api->game->addLog($userid, 'temple', "Traded {$set['will_refill_cost']} {$_CONFIG['secondary_currency']} to regenerate 5% Will.");
        }
    } else {
        alert('danger', "Uh Oh!", "You do have have enough {$_CONFIG['secondary_currency']} to refill your Will.", true, 'temple.php');
    }
}

function iq()
{
    global $db, $api, $userid, $ir, $h, $set, $_CONFIG;
    if (isset($_POST['iq'])) {
        //Make sure the POST is safe to work with.
        $_POST['iq'] = (isset($_POST['iq']) && is_numeric($_POST['iq'])) ? abs($_POST['iq']) : '';

        //POST is empty.
        if (empty($_POST['iq'])) {
            alert('danger', "Uh Oh!", "Please specify how much {$_CONFIG['secondary_currency']} you wish to trade in for {$_CONFIG['iq_stat']}.");
            die($h->endpage());
        }
        //IQ gained is coins exchanged multiplied by game setting for how much IQ per coin.
        $totalcost = $_POST['iq'] * $set['iq_per_sec'];

        //User does not have enough {$_CONFIG['secondary_currency']} to exchange for how much they said they wanted in IQ.
        if ($api->user->hasCurrency($userid, 'secondary', $_POST['iq']) == false) {
            alert('danger', "Uh Oh!", "You do not have enough {$_CONFIG['secondary_currency']} to buy that much {$_CONFIG['iq_stat']}.");
            die($h->endpage());
        }
        //Take the currency and give the user some IQ.
        $api->user->takeCurrency($userid, 'secondary', $_POST['iq']);
        $db->query("UPDATE `userstats` SET `iq` = `iq` + {$totalcost} WHERE `userid` = {$userid}");
        alert('success', "Success!", "You have successfully traded " . number_format($_POST['iq']) . " {$_CONFIG['secondary_currency']} for " . number_format($totalcost) . " IQ Points.", true, 'temple.php');
        $api->game->addLog($userid, 'temple', "Traded {$_POST['iq']} {$_CONFIG['secondary_currency']} for {$totalcost} IQ.");
    } else {
        alert('info', "Information!", "You can trade in your {$_CONFIG['secondary_currency']} for {$_CONFIG['iq_stat']} at a ratio of {$set['iq_per_sec']}
		per {$_CONFIG['secondary_currency']}. You currently have " . number_format($ir['secondary_currency']) . " {$_CONFIG['secondary_currency']}.", false);
        echo "<table class='table table-bordered'>
			<form method='post'>
			<tr>
				<th>
					{$_CONFIG['secondary_currency']}
				</th>
				<td>
					<input type='number' class='form-control' name='iq' min='1' max='{$ir['secondary_currency']}' required='1'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Trade for {$_CONFIG['iq_stat']}'>
				</td>
			</tr>
			</form>
		</table>";
    }
}

$h->endpage();