<?php
/*
	File: 		lib/installer_error_handler.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		The error handler for the installer.
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

function error_critical($human_error, $debug_error, $action,
                        $context = array())
{
    require_once('./installer_head.php'); // in case it hasn't been included
    // Setup a new error
    header('HTTP/1.1 500 Internal Server Error');
    echo '<h1>Installer Error</h1>';
    echo 'A critical error has occurred, and installation has stopped. '
        . 'Below are the details:<br />' . $debug_error . '<br /><br />'
        . '<strong>Action taken:</strong> ' . $action . '<br /><br />';
    if (is_array($context) && count($context) > 0) {
        echo '<strong>Context at error time:</strong> ' . '<br /><br />'
            . nl2br(print_r($context, true));
    }
    require_once('./installer_foot.php');
    exit;
}

function error_php($errno, $errstr, $errfile = '', $errline = 0,
                   $errcontext = array())
{
    // What's happened?
    // If it's a PHP warning or user error/warning, don't go further - indicates bad code, unsafe
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
            '<strong>User Error:</strong> ' . $errstr . ' (' . $errno
            . ')', 'Line executed: ' . $errfile . ':' . $errline,
            $errcontext);
    } else if ($errno == E_USER_WARNING) {
        error_critical('',
            '<strong>User Warning:</strong> ' . $errstr . ' (' . $errno
            . ')', 'Line executed: ' . $errfile . ':' . $errline,
            $errcontext);
    } else {
        // Only do anything if DEBUG is on, now
        if (DEBUG) {
            // Determine the name to display from the error type
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
                    break; // E_DEPRECATED [since 5.3]
                case 16384:
                    $errname = 'User Deprecation Notice';
                    break; // E_USER_DEPRECATED [since 5.3]
            }
            require_once('./installer_head.php'); // in case it hasn't been included
            echo 'A non-critical error has occurred. Page execution will continue. '
                . 'Below are the details:<br /><strong>' . $errname
                . '</strong>: ' . $errstr . ' (' . $errno . ')'
                . '<br /><br />' . '<strong>Line executed</strong>: '
                . $errfile . ':' . $errline . '<br /><br />';
            if (is_array($errcontext) && count($errcontext) > 0) {
                echo '<strong>Context at error time:</strong> '
                    . '<br /><br />' . nl2br(print_r($errcontext, true));
            }
        }
    }
}