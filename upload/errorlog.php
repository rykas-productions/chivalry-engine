<?php
/*
	File:		errorlog.php
	Created:	Aug 6, 2023 at 9:11:14 PM Eastern Time
	Author:		TheMasterGeneral
	Website:	https://github.com/rykas-productions/chivalry-engine
	MIT License
	Copyright (c) 2023 TheMasterGeneral
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
$logfile = "error.log";
require('globals_nonauth.php');
if (isset($_GET['clearlog']))
{
    if (file_exists($logfile))
    {
        unlink($logfile);
        insertErrorLog("Error log cleared.");
        success("Error log cleared.");
    }
    else
        danger("Error log doesn't exist or is already cleared.");
}
if (isset($_GET['breakpoint']))
{
    insertLogBreakpoint();
    success("Breakpoint inserted in log");
}
$styl->createCard("Error Log");
if (file_exists($logfile))
    $output = file_get_contents($logfile);
else
    $output = info("No errors in the log at this time.");
echo "<pre class='pre-scrollable'>{$output}</pre>";
$col1 = "<a href='?clearlog' class='btn btn-primary btn-block'>Clear Log</a>";
$col2 = "<a href='?breakpoint' class='btn btn-secondary btn-block'>Insert break</a>";
createTwoCols($col1, $col2);
$styl->endCard();