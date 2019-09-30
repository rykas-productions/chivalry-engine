<?php
/*
	File:		functions/global_functions.php
	Created: 	9/22/2019 at 4:17PM Eastern Time
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
//A simple function to create forms, to cut down on bloat.
include('functions/func_escape.php');
include('functions/func_account.php');
include('functions/func_alerts.php');
include('functions/func_template.php');
include('functions/func_format.php');
function createForm($method, $action, $inputsArray, $submitButtonName)
{
	echo "<form method='{$method}' action='{$action}'>";
	foreach ($inputsArray as $input) 
	{
		echo "<div class='form-group'>
		<label for='{$input[1]}'>{$input[2]}</label>
		<input type='{$input[0]}' name='{$input[1]}' class='form-control' placeholder='{$input[2]}'>
		</div>";
	}
	echo "<button class='btn btn-primary' type='submit'>{$submitButtonName}</button>
	</form>";
}
function createPostForm($action, $inputsArray, $submitButtonName)
{
	createForm('post', $action, $inputsArray, $submitButtonName);
}
function createGetForm($action, $inputsArray, $submitButtonName)
{
	createForm('get', $action, $inputsArray, $submitButtonName);
}
function returnUnixTimestamp()
{
	return time();
}
function headerRedirect($location)
{
	return header("Location: {$location}");
}
function returnPercentage($min, $max)
{
	return round($min / $max * 100);
}