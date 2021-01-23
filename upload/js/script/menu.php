<?php
/*
	File: 		js/script/menu.php
	Created: 	7/04/2019 at 4:40PM Eastern Time
	Info: 		Helps change the user's settings regarding hiding the sidebar.
	Author: 	TheMasterGeneral
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
$menuhide = 1;
if (isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD'])) {
    if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
        // Ignore a GET request
        header('HTTP/1.1 400 Bad Request');
        exit;
    }
}
require_once('../../globals.php');
if (!is_ajax()) {
    header('HTTP/1.1 400 Bad Request');
    exit;
}
$_POST['value'] = (isset($_POST['value']) && is_numeric($_POST['value'])) ? abs($_POST['value']) : 0;
echo "Value = {$_POST['value']} for {$userid}.";
if ($_POST['value'] == 1)
{
	$db->query("UPDATE `user_settings` SET `sidemenu` = 1 WHERE `userid` = {$userid}");
	echo "Set to 1.";
	exit;
}
elseif ($_POST['value'] == 0)
{
	$db->query("UPDATE `user_settings` SET `sidemenu` = 0 WHERE `userid` = {$userid}");
	echo "Set to 0.";
	exit;
}
else
{
	exit;
}
$db->free_result($q);
