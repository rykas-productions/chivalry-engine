<?php
/*
	File: class/class_alert.php
	Created: 11/10/2019 at 10:30PM Eastern Time
	Info: Creates a class file to easily use alerts.
	who don't wish to use the main game code!
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
if (!defined('MONO_ON')) {
    exit;
}
class class_alert
{
    function danger(string $text)
    {
        echo "<div class='alert alert-danger'>
                <i class='fa fa-exclamation-triangle' aria-hidden='true'></i>
                    <strong>Uh Oh!</strong>
                        {$text}
                </div>";
    }
    function success(string $text)
    {
        echo "<div class='alert alert-danger'>
                <i class='fa fa-check-circle' aria-hidden='true'></i>
                    <strong>Success!</strong>
                        {$text}
                </div>";
    }
    function info(string $text)
    {
        echo "<div class='alert alert-danger'>
                <i class='fa fa-info-circle' aria-hidden='true'></i>
                    <strong>Heads Up!</strong>
                        {$text}
                </div>";
    }
    function warning(string $text)
    {
        echo "<div class='alert alert-danger'>
                <i class='fa fa-exclamation-circle' aria-hidden='true'></i>
                    <strong>Warning!</strong>
                        {$text}
                </div>";
    }
    function dangerRedirect(string $text, string $redirlink = 'back', string $redirurl = 'Back')
    {
        $redirlink = ($redirlink == 'back') ? $_SERVER['REQUEST_URI'] : $redirlink;
        echo "<div class='alert alert-danger'>
                <i class='fa fa-exclamation-triangle' aria-hidden='true'></i>
                    <strong>Uh Oh!</strong>
                        {$text} > <a href='{$redirlink}' class='alert-link'>{$redirurl}</a>
                </div>";
    }
    function successRedirect(string $text, string $redirlink = 'back', string $redirurl = 'Back')
    {
        $redirlink = ($redirlink == 'back') ? $_SERVER['REQUEST_URI'] : $redirlink;
        echo "<div class='alert alert-danger'>
                <i class='fa fa-check-circle' aria-hidden='true'></i>
                    <strong>Success!</strong>
                        {$text} > <a href='{$redirlink}' class='alert-link'>{$redirurl}</a>
                </div>";
    }
    function infoRedirect(string $text, string $redirlink = 'back', string $redirurl = 'Back')
    {
        $redirlink = ($redirlink == 'back') ? $_SERVER['REQUEST_URI'] : $redirlink;
        echo "<div class='alert alert-danger'>
                <i class='fa fa-info-circle' aria-hidden='true'></i>
                    <strong>Heads Up!</strong>
                        {$text} > <a href='{$redirlink}' class='alert-link'>{$redirurl}</a>
                </div>";
    }
    function warningRedirect(string $text, string $redirlink = 'back', string $redirurl = 'Back')
    {
        $redirlink = ($redirlink == 'back') ? $_SERVER['REQUEST_URI'] : $redirlink;
        echo "<div class='alert alert-danger'>
                <i class='fa fa-exclamation-circle' aria-hidden='true'></i>
                    <strong>Warning!</strong>
                        {$text} > <a href='{$redirlink}' class='alert-link'>{$redirurl}</a>
                </div>";
    }
}