<?php
/*
	File:		stats.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Displays certain in-game statistics to view.
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
require("globals.php");

//Everything's in this file.
require("stats/stats.php");

//This is... messy.
echo "<h3>Statistics Center</h3><hr />
	<table width='50%' class='table table-bordered table-hover table-striped'>
		<thead>
			<tr>
				<th>
					Statistic
				</th>
				<th width='33%'>
					Value
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					Registered Players
				</td>
				<td>
					" . number_format($TotalUserCount) . "
				</td>
			</tr>
			<tr>
				<td>
					Males
				</td>
				<td>
					" . number_format($Male) . "
				</td>
			</tr>
			<tr>
				<td>
					Females
				</td>
				<td>
					" . number_format($Female) . "
				</td>
			</tr>
			<tr>
				<td>
					{$_CONFIG['primary_currency']} Withdrawn
				</td>
				<td>
					" . number_format($TotalPrimaryCurrency) . "
				</td>
			</tr>
			<tr>
				<td>
					{$_CONFIG['primary_currency']} Banked
				</td>
				<td>
					" . number_format($TotalBank) . "
				</td>
			</tr>
			<tr>
				<td>
					Total {$_CONFIG['primary_currency']}
				</td>
				<td>
					" . number_format($TotalBankandPC) . "
				</td>
			</tr>
			<tr>
				<td>
					{$_CONFIG['secondary_currency']} in Circulation
				</td>
				<td>
					" . number_format($TotalSecondaryCurrency) . "
				</td>
			</tr>
			<tr>
				<td>
					Average {$_CONFIG['primary_currency']} per Player
				</td>
				<td>
					" . number_format($AveragePrimaryCurrencyPerPlayer) . "
				</td>
			</tr>
			<tr>
				<td>
					Average {$_CONFIG['secondary_currency']} per Player
				</td>
				<td>
					" . number_format($AverageSecondaryCurrencyPerPlayer) . "
				</td>
			</tr>
			<tr>
				<td>
					Average Bank Balance per Players
				</td>
				<td>
					" . number_format($AverageBank) . "
				</td>
			</tr>
			<tr>
				<td>
					Registered Guilds
				</td>
				<td>
					" . number_format($TotalGuildCount) . "
				</td>
			</tr>
		</tbody>
	</table>";
$h->endpage();
?>