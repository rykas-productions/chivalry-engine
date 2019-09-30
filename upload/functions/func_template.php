<?php
/*
	File:		functions/func_template.php
	Created: 	9/29/2019 at 7:45PM Eastern Time
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
function createTwoCols($col1, $col2)
{
	echo "
	<div class='row'>
		<div class='col-sm'>
			{$col1}
		</div>
		<div class='col-sm'>
			{$col2}
		</div>
	</div>";
}
function createThreeCols($col1, $col2, $col3)
{
	echo "
	<div class='row'>
		<div class='col-sm'>
			{$col1}
		</div>
		<div class='col-sm'>
			{$col2}
		</div>
		<div class='col-sm'>
			{$col3}
		</div>
	</div>";
}
function createFourCols($col1, $col2, $col3, $col4)
{
	echo "
	<div class='row'>
		<div class='col-sm'>
			{$col1}
		</div>
		<div class='col-sm'>
			{$col2}
		</div>
		<div class='col-sm'>
			{$col3}
		</div>
		<div class='col-sm'>
			{$col4}
		</div>
	</div>";
}
function createFiveCols($col1, $col2, $col3, $col4, $col5)
{
	echo "
	<div class='row'>
		<div class='col-sm'>
			{$col1}
		</div>
		<div class='col-sm'>
			{$col2}
		</div>
		<div class='col-sm'>
			{$col3}
		</div>
		<div class='col-sm'>
			{$col4}
		</div>
		<div class='col-sm'>
			{$col5}
		</div>
	</div>";
}
function createSixCols($col1, $col2, $col3, $col4, $col5, $col6)
{
	echo "
	<div class='row'>
		<div class='col-sm'>
			{$col1}
		</div>
		<div class='col-sm'>
			{$col2}
		</div>
		<div class='col-sm'>
			{$col3}
		</div>
		<div class='col-sm'>
			{$col4}
		</div>
		<div class='col-sm'>
			{$col5}
		</div>
		<div class='col-sm'>
			{$col6}
		</div>
	</div>";
}
function createCard($cardTitle,$cardBody)
{
	return "
	<div class='card'>
		<div class='card-body'>
			<h5 class='card-title'>{$cardTitle}</h5>
			<p class='card-text'><{$cardBody}</p>
		</div>
	</div>";
}
function createTitlelessCard($cardBody)
{
	return "
	<div class='card'>
		<div class='card-body'>
			<p class='card-text'>{$cardBody}</p>
		</div>
	</div>";
}
function createProgressBar($filledPercentage)
{
	return createProgressBarLabel($filledPercentage, "{$filledPercentage}%");
}
function createProgressBarLabel($filledPercentage, $label)
{
	return "
	<div class='progress'>
		<div class='progress-bar' role='progressbar' style='width: {$filledPercentage}%'>{$label}</div>
	</div>";
}