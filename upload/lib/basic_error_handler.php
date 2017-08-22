<?php
/*
	File: lib/basic_error_handler.php
	Created: 6/17/2016 at 5:32PM Eastern Time
	Info: An error handler that will show human readable error messages in-game.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
// Change to true to show the user more information (for development)
define('DEBUG', true);

function error_critical($human_error, $debug_error, $action, $context = array())
{
	global $userid,$domain,$set;
    echo "<title>{$set['WebsiteName']} - Critical Error</title>";
    if (isset($set) && is_array($set) && array_key_exists('WebsiteName', $set))
    {
        echo "<h1>{$set['WebsiteName']} - Critical Error</h1>";
    }
    else
    {
        echo '<h1>Internal Server Error</h1>';
    }
    if (DEBUG)
    {
        echo 'A critical error has occurred, and page execution has stopped. If this issue persists, please notify an admin or web developer right away!<br />'
                . 'Below are the details:<br /><pre>' . $debug_error
                . '<br /><br />' . '<strong>Action taken:</strong> ' . $action
                . '<br /><br /></pre>';
        // Only uncomment the below if you know what you're doing,
        // for debug purposes.
        /*
		if (is_array($context) && count($context) > 0)
        {
            echo '<strong>Context at error time:</strong> ' . '<br /><br />'
                    . nl2br(print_r($context, true));
        }
		*/
    }
    else
    {
        echo 'A critical error has occurred, and this page cannot be displayed. '
                . 'Try again later. If this error persists, please alert an admin as soon as possible!';
        if (!empty($human_error))
        {
            echo '<br />' . $human_error;
        }
    }
	error_log($debug_error);
    exit;
}

function error_php($errno, $errstr, $errfile = '', $errline = 0, $errcontext = array())
{
    if ($errno == E_WARNING)
    {
        error_critical('',
                '<strong>PHP Warning:</strong> ' . $errstr . ' (' . $errno
                        . ')', 'Line executed: ' . $errfile . ':' . $errline,
                $errcontext);
    }
    else if ($errno == E_RECOVERABLE_ERROR)
    {
        error_critical('',
                '<strong>PHP Recoverable Error:</strong> ' . $errstr . ' ('
                        . $errno . ')',
                'Line executed: ' . $errfile . ':' . $errline, $errcontext);
    }
    else if ($errno == E_USER_ERROR)
    {
        error_critical('',
                '<strong>Engine Error:</strong> ' . $errstr . ' (' . $errno
                        . ')', 'Line executed: ' . $errfile . ':' . $errline,
                $errcontext);
    }
    else if ($errno == E_USER_WARNING)
    {
        error_critical('',
                '<strong>Engine Warning:</strong> ' . $errstr . ' (' . $errno
                        . ')', 'Line executed: ' . $errfile . ':' . $errline,
                $errcontext);
    }
    else
    {
        if (DEBUG)
        {
            $errname = 'Unknown Error';
            switch ($errno)
            {
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