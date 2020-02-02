<?php
/*
	File:		bank.php
	Created: 	9/29/2019 at 10:50PM Eastern Time
	Author:		TheMasterGeneral
	Website: 	https://github.com/rykas-productions/chivalry-engine
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
$moduleID=('bank');
require('./globals_auth.php');
function initialize()
{
	global $moduleID;
	if (!readConfigFromDB($moduleID))
	{
		$moduleConfigArray=array(
		'moduleID' => $moduleID,
		'moduleAuthor' => 'TheMasterGeneral',
		'moduleURL' => 'https://github.com/rykas-productions/chivalry-engine',
		'moduleVersion' => 1,
		'bankOpeningFee' => 5000,
		'bankWithdrawPercent' => 5,
		'bankWithdrawMaxFee' => 1000
		);
		$defaultConfig = formatConfig($moduleConfigArray);
		writeConfigToDB($moduleID, $defaultConfig);
		echo "Installing default config...";
		headerRedirect("bank.php");
	}
}
if ($ir['primaryCurrencyBank'] == -1)
{
	if (isset($_GET['buy']))
	{
		if (returnPlayerPrimaryCurrency($userid) >= $moduleConfig['bankOpeningFee'])
		{
			removePlayerPrimaryCurrency($userid, $moduleConfig['bankOpeningFee']);
			$db->query("UPDATE `user_stats` SET `primaryCurrencyBank` = 0 WHERE `userid` = {$userid}");
			successRedirect("You have successfully bought a bank account for " . number_format($moduleConfig['bankOpeningFee']) . " " . constant("primary_currency") . ".");
			
		}
		else
		{
			dangerRedirect("You need at least " . number_format($moduleConfig['bankOpeningFee']) . " " . constant("primary_currency") . " to purchase a bank account.");
		}
	}
	else
	{
		echo "Do you wish to buy a bank account? It'll cost you " . number_format($moduleConfig['bankOpeningFee']) . " " . constant("primary_currency") . "<br />
		<a href='?buy'>Yes, Please!</a>";
	}
}
else
{
	if (!isset($_GET['action']))
        $_GET['action'] = '';
	switch ($_GET['action']) 
	{
        case "deposit":
            deposit();
            break;
        case "withdraw":
            withdraw();
            break;
        default:
            home();
            break;
    }
}
function home()
{
	global $ir, $moduleConfig;
	echo "<b>You current have " . number_format($ir['primaryCurrencyBank']) . " in your bank account.</b><br />
				<div class='cotainer'>
					<div class='row'>
						<div class='col-sm'>";
							createPostForm('?action=deposit',array(array('number','deposit','Bank Deposit', $ir['primaryCurrencyHeld'])), 'Deposit');
						echo "</div>
						<div class='col-sm'>";
							createPostForm('?action=withdraw',array(array('number','withdraw','Bank Withdrawal', $ir['primaryCurrencyBank'])), 'Withdraw');
						echo "</div>
					</div>
				</div>";
}

function deposit()
{
	global $ir, $moduleConfig, $db;
	$deposit = makeSafeInt($_POST['deposit']);
	if ($ir['primaryCurrencyHeld'] < $deposit)
	{
		dangerRedirect("You are trying to deposit more cash than you have on your player.","bank.php","Back");
	}
	else
	{
		$ir['primaryCurrencyBank'] += $deposit;
		removePlayerPrimaryCurrency($ir['userid'], $deposit);
		$db->query("UPDATE `users_stats` 
					SET `primaryCurrencyBank` = `primaryCurrencyBank` + {$deposit} 
					WHERE `userid` = {$ir['userid']}");
		successRedirect("You have deposited " . number_format($deposit) . " into your bank account.","bank.php","Back");
	}
}

function withdraw()
{
	global $ir, $moduleConfig, $db;
	$withdraw = makeSafeInt($_POST['withdraw']);
	if ($ir['primaryCurrencyBank'] < $withdraw)
	{
		dangerRedirect("You are trying to withdraw more cash than you have in your bank account.","bank.php","Back");
	}
	else
	{
		$ir['primaryCurrencyBank'] -= $withdraw;
		addPlayerPrimaryCurrency($ir['userid'], $withdraw);
		$db->query("UPDATE `users_stats` 
					SET `primaryCurrencyBank` = `primaryCurrencyBank` - {$withdraw} 
					WHERE `userid` = {$ir['userid']}");
		successRedirect("You have withdrawn " . number_format($withdraw) . " from your bank account.","bank.php","Back");
	}
}