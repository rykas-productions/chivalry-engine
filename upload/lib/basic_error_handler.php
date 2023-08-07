<?php
/*
	File: 		lib/basic_error_handler.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		The in-game error handler.
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
// Change to true to show the user more information (for development)
define('DEBUG', true);

function error_critical($human_error, $debug_error, $action, $context = array())
{
    echo "<title>Chivalry Engine V3 Error</title>";
        echo '<h1>Internal Engine Error</h1>';
    if (DEBUG) 
    {
        echo 'A critical error has occurred, and page execution has stopped. If this issue persists, please notify an admin or web developer right away!<br />'
            . 'Below are the details:<br /><pre>' . $debug_error
            . '<br /><br />' . '<strong>Action taken:</strong> ' . $action
            . '<br /><br /></pre>';
        displayBacktrace();
        // Only uncomment the below if you know what you're doing,
        // for debug purposes.
        /*
		if (is_array($context) && count($context) > 0)
        {
            echo '<strong>Context at error time:</strong> ' . '<br /><br />'
                    . nl2br(print_r($context, true));
        }
		*/
    } else {
        echo 'A critical error has occurred, and this page cannot be displayed. '
            . 'Try again later. If this error persists, please alert an admin as soon as possible!';
        if (!empty($human_error)) {
            echo '<br />' . $human_error;
        }
    }
    error_log($debug_error);
    $logged_error = $debug_error . " ({$action})\n";
	insertErrorLog($logged_error);
    exit;
}

function error_php($errno, $errstr, $errfile = '', $errline = 0, $errcontext = array())
{
    if ($errno == E_WARNING) {
        error_critical('',
            '<strong>PHP Warning:</strong> ' . $errstr . ' (' . $errno
            . ')', 'Line executed: ' . $errfile . ':' . $errline,
            $errcontext);
    } else if ($errno == E_RECOVERABLE_ERROR) {
        error_critical('',
            '<strong>PHP Recoverable Error:</strong> ' . $errstr . ' ('
            . $errno . ')',
            'Line executed: ' . $errfile . ':' . $errline, $errcontext);
    } else if ($errno == E_USER_ERROR) {
        error_critical('',
            '<strong>Engine Error:</strong> ' . $errstr . ' (' . $errno
            . ')', 'Line executed: ' . $errfile . ':' . $errline,
            $errcontext);
    } else if ($errno == E_USER_WARNING) {
        error_critical('',
            '<strong>Engine Warning:</strong> ' . $errstr . ' (' . $errno
            . ')', 'Line executed: ' . $errfile . ':' . $errline,
            $errcontext);
    } else {
        if (DEBUG) {
            $errname = 'Unknown Error';
            switch ($errno) {
                case E_NOTICE:
                    $errname = 'PHP Notice';
                    break;
                case E_USER_NOTICE:
                    $errname = 'User Notice';
                    break;
                case 8192:
                    $errname = 'PHP Deprecation Notice';
                    break;
                case 16384:
                    $errname = 'User Deprecation Notice';
                    break;
            }
            echo '<pre>A non-critical error has occurred. Page execution will continue. '
                . 'Below are the details:<br /><strong>' . $errname
                . '</strong>: ' . $errstr . ' (' . $errno . ')'
                . '<br /><br />' . '<strong>Line executed</strong>: '
                . $errfile . ':' . $errline . '<br /><br />';
            // Only uncomment the below if you know what you're doing,
            // for debug purposes.
            /*
			if (is_array($errcontext) && count($errcontext) > 0)
            {
				echo '<strong>Context at error time:</strong> '
				. '<br /><br />' . nl2br(print_r($errcontext, true));
			}
			*/
            echo "</pre>";
        }
    }
}

function insertErrorLog($logTxt)
{
    $logged_error = date('m-d-Y, G:i:s', time()) . ": {$logTxt}\n";
    file_put_contents('./error.log', $logged_error, FILE_APPEND);
}