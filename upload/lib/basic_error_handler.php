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
    global $userid, $domain, $set, $h;
    echo "<title>Critical Error</title>";
    if (isset($set) && is_array($set) && array_key_exists('WebsiteName', $set)) {
        echo "<h1>Critical Error</h1>";
    } else {
        echo '<h1>Internal Server Error</h1>';
    }
    if (DEBUG) {
        alert("danger","{$debug_error}","{$action}<hr />Check out Chivalry is 
        Dead on <a href='https://www.facebook.com/officialcidgame/'>Facebook</a> or 
        <a href='https://twitter.com/cidgame'>Twitter</a> for more information if you cannot use the 
        game. ",false);
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
        alert("danger","Critical Error!","It appears you've ran into an error and execution of this script was halted. We're sorry for the inconvenience.<hr />Check out Chivalry is 
        Dead on <a href='https://www.facebook.com/officialcidgame/'>Facebook</a> or 
        <a href='https://twitter.com/cidgame'>Twitter</a> for more information if you cannot use the 
        game.",false);
        if (!empty($human_error)) {
            echo '<br />' . $human_error;
        }
    }
    error_log($debug_error);
    if (isset($h))
        $h->endpage();
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
            alert('warning',"{$errname}!","{$errstr} ({$errno}) on {$errfile}:{$errline}",false);
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