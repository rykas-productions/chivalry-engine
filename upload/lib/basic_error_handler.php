<?php
/*
	File: lib/basic_error_handler.php
	Created: 6/17/2016 at 5:32PM Eastern Time
	Info: An error handler that will show human readable error messages in-game.
	Author: TheMasterGeneral
	Website: http://mastergeneral156.pcriot.com/
*/
// Change to true to show the user more information (for development)
define('DEBUG', true);

function error_critical($human_error, $debug_error, $action,
        $context = array())
{
	global $userid;
    // Clear anything that was going to be shown
    ob_get_clean();
    // Setup a new error
    header('HTTP/1.1 500 Internal Server Error');
    // If we can, gracefully show them an error message
    // including the game's name. If not, just say
    // "Internal Server Error"
    global $set;
	?>
	<!DOCTYPE html>
<html lang="en">
<head>
	<center>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
	<meta name="theme-color" content="#000000">
	<noscript>
		<meta http-equiv="refresh" content="0;url=no-script.php">
	</noscript>

    <?php echo "<title>{$set['WebsiteName']} - Critical Error</title>"; ?>

    <!-- CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bs2.css" rel="stylesheet">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

    <!-- Custom CSS -->
    <style>
    body {
        padding-top: 70px;
        /* Required padding for .navbar-fixed-top. Remove if using .navbar-static-top. Change if height of navigation changes. */
    }
	a {
		color: gray;
	}
    </style>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
	
	<?php
    if (isset($set) && is_array($set) && array_key_exists('game_name', $set))
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
        //if (is_array($context) && count($context) > 0)
        //{
        //    echo '<strong>Context at error time:</strong> ' . '<br /><br />'
        //            . nl2br(print_r($context, true));
        //}
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
    exit;
}

function error_php($errno, $errstr, $errfile = '', $errline = 0,
        $errcontext = array())
{
    // What's happened?
    // If it's a PHP warning or user error/warning, don't go further - indicates bad code, unsafe
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
        // Only do anything if DEBUG is on, now
        if (DEBUG)
        {
            // Determine the name to display from the error type
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
                break; // E_DEPRECATED [since 5.3]
            case 16384:
                $errname = 'User Deprecation Notice';
                break; // E_USER_DEPRECATED [since 5.3]
            }
            echo '<pre>A non-critical error has occurred. Page execution will continue. '
                    . 'Below are the details:<br /><strong>' . $errname
                    . '</strong>: ' . $errstr . ' (' . $errno . ')'
                    . '<br /><br />' . '<strong>Line executed</strong>: '
                    . $errfile . ':' . $errline . '<br /><br />';
            // Only uncomment the below if you know what you're doing,
            // for debug purposes.
            //if (is_array($errcontext) && count($errcontext) > 0)
            //{
            //    echo '<strong>Context at error time:</strong> '
            //            . '<br /><br />' . nl2br(print_r($errcontext, true));
            //}
			echo "</pre>";
        }
    }
}