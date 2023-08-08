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
include('./functions/func_escape.php');
include('./functions/func_account.php');
include('./functions/func_alerts.php');
include('./functions/func_template.php');
include('./functions/func_format.php');
include('./functions/func_config.php');
include('./functions/func_player.php');
include('./functions/func_auth.php');
include('./functions/func_system.php');

/**
 * @param string    $method Valid: GET/POST
 * @param string    $action Page where form is being submitted
 * @param object    $inputsArray Array holding information about the form inputs
 * @param string    $submitButtonName Name of submit button
 */
function createForm($method, $action, $inputsArray, $submitButtonName)
{
	echo "<form method='{$method}' action='{$action}'>";
	foreach ($inputsArray as $input) 
	{
		if (!isset($input[3]))
			$input[3]='';
		echo "<div class='form-group'>
		<label for='{$input[1]}'>{$input[2]}</label>
		<input type='{$input[0]}' name='{$input[1]}' class='form-control' placeholder='{$input[2]}' value='{$input[3]}'>
		</div>";
	}
	echo "
	<input type='hidden' name='formSubmitValue' value='1'>
	<button class='btn btn-primary' type='submit'>{$submitButtonName}</button>
	</form>";
}

/**
 * @desc            Creates a POST form to submit.
 * @param string    $action Page where form is being submitted
 * @param object    $inputsArray Array holding information about the form inputs
 * @param string    $submitButtonName Name of submit button
 */
function createPostForm($action, $inputsArray, $submitButtonName)
{
	createForm('post', $action, $inputsArray, $submitButtonName);
}

/**
 * @desc            Creates a GET form to submit.
 * @param string    $action Page where form is being submitted
 * @param object    $inputsArray Array holding information about the form inputs
 * @param string    $submitButtonName Name of submit button
 */
function createGetForm($action, $inputsArray, $submitButtonName)
{
	createForm('get', $action, $inputsArray, $submitButtonName);
}

/**
 * @desc            Fetches the current Unix Time.
 * @return number   Current time.
 */
function returnUnixTimestamp()
{
	return time();
}

/**
 * @desc            Redirect the user to another page gracefully.
 * @param string    $location Page to redirect to.
 */
function headerRedirect($location)
{
	return header("Location: {$location}");
}

/**
 * @desc            Helper function to generate the current percentage, assuming $min is 
 *                  the current value and $max is the maximum value.
 * @param number    $min Lowest number
 * @param number    $max Highest number
 * @return int
 */
function returnPercentage($min, $max)
{
	return round($min / $max * 100);
}

/**
 * @desc            Generates a random number using $min and $max as the minimum and 
 *                  maximum values. If left empty, will result to a random int between 
 *                  the minimum and maximum constants defined by PHP.
 * @param int       $min Minimum number to randomly generate a number between
 * @param int       $max Maximum number to randomly generate a number between
 * @return number   Randomly generated integer
 */
function returnRandomNumber($min = PHP_INT_MIN, $max = PHP_INT_MAX)
{
	return random_int($min, $max);
}