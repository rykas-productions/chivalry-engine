<?php
/*
	File: 		lib/dev_help.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		A small script to display extra development information.
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
//Set to true to enable developement help!
define('DEV', false);
if (DEV) {
    echo "<div class='alert alert-warning' role='alert'><strong>Warning!</strong> You have development mode on. Please be sure to turn this off when you launch the game.</div>";
    echo "<pre class='pre-scrollable'>";
    echo "Dumping all variables stored in POST.<br />";
    var_dump($_POST);
    echo "<br />Dumping all variables stored in GET.<br />";
    var_dump($_GET);
    echo "<br />Dumping all variables stored in COOKIE.<br />";
    var_dump($_COOKIE);
    echo "<br />Dumping all variables stored in SESSION.<br />";
    var_dump($_SESSION);
    echo "</pre>";
}