<?php
/*
	File:		functions/func_alerts.php
	Created: 	9/29/2019 at 6:50PM Eastern Time
	Author:		TheMasterGeneral
	Website: 	https://github.com/rykas-productions/chivalry-engine
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
function alert($type, $title, $text, $doredirect = true, $redirect = 'back', $redirecttext = 'Back')
{
    //This function is a horrible mess dude..
    if ($type == 'danger')
        $icon = "exclamation-triangle";
    elseif ($type == 'success')
        $icon = "check-circle";
    elseif ($type == 'info')
        $icon = 'info-circle';
    else
        $icon = 'exclamation-circle';
    if ($doredirect) {
        $redirect = ($redirect == 'back') ? $_SERVER['REQUEST_URI'] : $redirect;
        echo "<div class='alert alert-{$type}'>
				<i class='fa fa-{$icon}' aria-hidden='true'></i>
					<strong>{$title}</strong> 
						{$text} > <a href='{$redirect}' class='alert-link'>{$redirecttext}</a>
				</div>";
    } else {
        echo "<div class='alert alert-{$type}'>
                    <i class='fa fa-{$icon}' aria-hidden='true'></i>
					    <strong>{$title}</strong>
					        {$text}
                </div>";
    }
}

function success($text)
{
	alert('success','Success!',$text, false);
}
function successRedirect($text, $redirectLink, $redirectText)
{
	alert('success','Success!',$text, true, $redirectLink, $redirectText);
}
function danger($text)
{
	alert('danger','Success!',$text, false);
}
function dangerRedirect($text, $redirectLink, $redirectText)
{
	alert('danger','Uh Oh!',$text, true, $redirectLink, $redirectText);
}
function info($text)
{
	alert('info','Information!',$text, false);
}
function infoRedirect($text, $redirectLink, $redirectText)
{
	alert('info','Information!',$text, true, $redirectLink, $redirectText);
}
function warning($text)
{
	alert('warning','Warning!',$text, false);
}
function warningRedirect($text, $redirectLink, $redirectText)
{
	alert('warning','Warning!',$text, true, $redirectLink, $redirectText);
}