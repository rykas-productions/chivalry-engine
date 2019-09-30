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
if (!readConfigFromDB($moduleID))
{
	$defaultConfig = formatConfig(array('bankOpeningFee' => 5000, 'bankWithdrawPercent' => 5, 'bankWithdrawMaxFee' => 1000));
	writeConfigToDB($moduleID, $defaultConfig);
	echo "Installing default config...";
	headerRedirect("bank.php");
}
elseif (isset($_GET['config']) && ($ir['staffLevel'] == 2))
{
	echo "<h3>Config for {$moduleID}</h3><hr />";
	if (isset($_POST['change']))
	{
		
	}
	else
	{
		$config=getConfigForPHP($moduleID);
		foreach ($config as $k => $v)
		{
			echo "{$v}";
		}
	}
}
else
{
	$config=getConfigForPHP($moduleID);
	if ($ir['primaryCurrencyBank'] = -1)
	{
		if (isset($_GET['buy']))
		{
			
		}
		else
		{
			echo "Do you wish to buy a bank account? It'll cost you " . number_format($config['bankOpeningFee']) . " " . constant("primary_currency") . "<br />
			<a href='?buy'>Yes, Please!</a>";
		}
	}
	else
	{
	}
}