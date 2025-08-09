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

// Add severity levels
define('ERROR_SEVERITY_LOW', 1);
define('ERROR_SEVERITY_MEDIUM', 2);
define('ERROR_SEVERITY_HIGH', 3);
define('ERROR_SEVERITY_CRITICAL', 4);

function error_critical($human_error, $debug_error, $action, $context = array())
{
    global $userid, $domain, $set;
    
    // Check if this is an AJAX request
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
              
    if ($isAjax) {
        // Return JSON response for AJAX requests
        header('Content-Type: application/json');
        echo json_encode([
            'error' => true,
            'message' => DEBUG ? $debug_error : $human_error,
            'action' => $action,
            'severity' => ERROR_SEVERITY_CRITICAL
        ]);
        exit;
    }

    // For regular requests, show HTML
    header("HTTP/1.1 500 Internal Server Error");
    echo "<!DOCTYPE html><html><head>";
    echo "<title>" . htmlspecialchars($set['WebsiteName'] ?? 'Website') . " - Critical Error</title>";
    echo "<style>
        .error-container { font-family: Arial, sans-serif; padding: 20px; }
        .error-title { color: #d32f2f; }
        .error-message { background: #ffebee; padding: 15px; border-radius: 4px; }
        .error-context { background: #f5f5f5; padding: 15px; border-radius: 4px; margin-top: 10px; }
        .error-stack { font-family: monospace; white-space: pre-wrap; }
    </style>";
    echo "</head><body><div class='error-container'>";

    if (isset($set) && is_array($set) && array_key_exists('WebsiteName', $set)) {
        echo "<h1 class='error-title'>" . htmlspecialchars($set['WebsiteName']) . " - Critical Error</h1>";
    } else {
        echo '<h1 class="error-title">Internal Server Error</h1>';
    }

    if (DEBUG) {
        echo '<div class="error-message">';
        echo 'A critical error has occurred, and page execution has stopped. If this issue persists, please notify an admin or web developer right away!<br /><br />';
        echo '<strong>Error Details:</strong><br />';
        echo '<div class="error-stack">' . htmlspecialchars($debug_error) . '</div>';
        echo '<br /><strong>Action taken:</strong> ' . htmlspecialchars($action);
        
        // Add stack trace in debug mode
        $stackTrace = debug_backtrace();
        if (!empty($stackTrace)) {
            echo '<br /><br /><strong>Stack Trace:</strong><pre>';
            foreach ($stackTrace as $index => $trace) {
                echo "#$index ";
                if (isset($trace['file'])) {
                    echo htmlspecialchars($trace['file']) . ":" . $trace['line'];
                }
                if (isset($trace['function'])) {
                    echo " - " . htmlspecialchars($trace['function']) . "()";
                }
                echo "\n";
            }
            echo '</pre>';
        }

        if (is_array($context) && count($context) > 0) {
            echo '<div class="error-context">';
            echo '<strong>Context at error time:</strong><br /><br />';
            echo nl2br(htmlspecialchars(print_r($context, true)));
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<div class="error-message">';
        echo 'A critical error has occurred, and this page cannot be displayed. '
            . 'Try again later. If this error persists, please alert an admin as soon as possible!';
        if (!empty($human_error)) {
            echo '<br />' . htmlspecialchars($human_error);
        }
        echo '</div>';
    }
    echo "</div></body></html>";

    // Improved error logging
    $logMessage = date('[Y-m-d H:i:s] ') . "Critical Error: " . $debug_error;
    if (!empty($_SERVER['REQUEST_URI'])) {
        $logMessage .= "\nURL: " . $_SERVER['REQUEST_URI'];
    }
    if (!empty($userid)) {
        $logMessage .= "\nUser ID: " . $userid;
    }
    $logMessage .= "\nAction: " . $action . "\n";
    error_log($logMessage);
    exit;
}

function error_php($errno, $errstr, $errfile = '', $errline = 0, $errcontext = array())
{
    // Get error severity
    $severity = ERROR_SEVERITY_LOW;
    switch ($errno) {
        case E_ERROR:
        case E_RECOVERABLE_ERROR:
        case E_USER_ERROR:
            $severity = ERROR_SEVERITY_CRITICAL;
            break;
        case E_WARNING:
        case E_USER_WARNING:
            $severity = ERROR_SEVERITY_HIGH;
            break;
        case E_NOTICE:
        case E_USER_NOTICE:
            $severity = ERROR_SEVERITY_LOW;
            break;
    }

    // Check if this is an AJAX request
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

    if ($errno == E_WARNING || $errno == E_RECOVERABLE_ERROR || 
        $errno == E_USER_ERROR || $errno == E_USER_WARNING) {
        
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'error' => true,
                'message' => $errstr,
                'file' => $errfile,
                'line' => $errline,
                'severity' => $severity
            ]);
            exit;
        }

        $errorType = '';
        switch ($errno) {
            case E_WARNING:
                $errorType = 'PHP Warning';
                break;
            case E_RECOVERABLE_ERROR:
                $errorType = 'PHP Recoverable Error';
                break;
            case E_USER_ERROR:
                $errorType = 'Engine Error';
                break;
            case E_USER_WARNING:
                $errorType = 'Engine Warning';
                break;
        }

        error_critical('',
            "<strong>{$errorType}:</strong> " . htmlspecialchars($errstr) . ' (' . $errno . ')',
            'Line executed: ' . htmlspecialchars($errfile) . ':' . $errline,
            $errcontext
        );
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

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'error' => true,
                    'type' => 'notice',
                    'message' => $errstr,
                    'file' => $errfile,
                    'line' => $errline,
                    'severity' => $severity
                ]);
                return;
            }

            echo '<div class="error-notice">';
            echo '<pre>A non-critical error has occurred. Page execution will continue. '
                . 'Below are the details:<br /><strong>' . htmlspecialchars($errname)
                . '</strong>: ' . htmlspecialchars($errstr) . ' (' . $errno . ')'
                . '<br /><br />' . '<strong>Line executed</strong>: '
                . htmlspecialchars($errfile) . ':' . $errline . '<br /><br />';

            if (is_array($errcontext) && count($errcontext) > 0) {
                echo '<strong>Context at error time:</strong> '
                    . '<br /><br />' . nl2br(htmlspecialchars(print_r($errcontext, true)));
            }

            echo "</pre></div>";
        }
    }
}
