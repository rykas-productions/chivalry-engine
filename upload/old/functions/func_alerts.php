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
/**
 * @desc Function to create all in-game alerts. This is the general function.
 * @param string $alertType         [Danger, Success, Info, Warning, Primary, Secondary]
 * @param string $alertTitle        The alert's title name.
 * @param string $alertText         The text shown in the alert.
 * @param bool $alertRedirect       True/False if you wish to redirect [Default: true]
 * @param string $alertRedirectLink The link you wish to redirect to. (Ex: back, explore.php) [Default: back] (Back goes to the previous page.)
 * @param string $alertRedirectText The clickable link for the alert redirect text.
 * @example alert('success','Success!','You have figured out the success alert',true, 'index.php', 'Go Home');
 */
function alert(string $alertType, string $alertTitle, string $alertText, bool $alertRedirect = true, string $alertRedirectLink = 'back', string $alertRedirectText = 'Back')
{
    //This function is a horrible mess dude..
    if ($alertType == 'danger')
        $alertIcon = "exclamation-triangle";
    elseif ($alertType == 'success')
        $alertIcon = "check-circle";
    elseif ($alertType == 'info')
        $alertIcon = 'info-circle';
    else
        $alertIcon = 'exclamation-circle';
        if ($alertRedirect) {
        $alertRedirectLink = ($alertRedirectLink == 'back') ? $_SERVER['REQUEST_URI'] : $alertRedirectLink;
        echo "<div class='alert alert-{$alertType}'>
				<i class='fa fa-{$alertIcon}' aria-hidden='true'></i>
					<strong>{$alertTitle}</strong> 
						{$alertText} > <a href='{$alertRedirectLink}' class='alert-link'>{$alertRedirectText}</a>
				</div>";
    } else {
        echo "<div class='alert alert-{$alertType}'>
                    <i class='fa fa-{$alertIcon}' aria-hidden='true'></i>
					    <strong>{$alertTitle}</strong>
					        {$alertText}
                </div>";
    }
}

/**
 * @desc Create a simple Success alert.
 * @param string $alertText Text to be shown on alert.
 * @example success("You have successfully done this action.");
 */
function success(string $alertText)
{
	alert('success','Success!',$alertText, false);
}

/**
 * @desc Create a success alert with a redirect.
 * @param string $alertText Text to be shown on alert.
 * @param string $alertRedirectLinkLink Link to go to after alert.
 * @param string $alertRedirectText Friendly name for link.
 * @example successRedirect("Successfully completed action", 'bank.php', 'Go to Bank');
 */
function successRedirect(string $alertText, string $alertRedirectLinkLink = 'back', string $alertRedirectText = 'Back')
{
	alert('success','Success!',$alertText, true, $alertRedirectLinkLink, $alertRedirectText);
}

/**
 * @desc Create a simple danger alert.
 * @param string $alertText Text to be shown on alert.
 * @example danger("This action failed");
 */
function danger(string $alertText)
{
	alert('danger','Uh Oh!',$alertText, false);
}

/**
 * @desc Create a danger alert with a redirect.
 * @param string $alertText Text to be shown on alert.
 * @param string $alertRedirectLinkLink Link to go to after alert.
 * @param string $alertRedirectText Friendly name for link.
 * @example dangerRedirect("Go to jail!", 'dungeon.php', 'Do not collect $200');
 */
function dangerRedirect(string $alertText, string $alertRedirectLinkLink = 'back', string $alertRedirectText = 'Back')
{
	alert('danger','Uh Oh!',$alertText, true, $alertRedirectLinkLink, $alertRedirectText);
}

/**
 * @desc Create a simple info alert.
 * @param string $alertText Text to be shown on alert.
 * @example info("Have some info!");
 */
function info(string $alertText)
{
	alert('info','Information!',$alertText, false);
}

/**
 * @desc Create a danger alert with a redirect.
 * @param string $alertText Text to be shown on alert.
 * @param string $alertRedirectLinkLink Link to go to after alert.
 * @param string $alertRedirectText Friendly name for link.
 * @example infoRedirect("Here's some info. Wanna read more?", '#link_to_more_info', 'Read More');
 */
function infoRedirect(string $alertText, string $alertRedirectLinkLink = 'back', string $alertRedirectText = 'Back')
{
	alert('info','Information!',$alertText, true, $alertRedirectLinkLink, $alertRedirectText);
}

/**
 * @desc Create a simple warning alert.
 * @param string $alertText Text to be shown on alert.
 * @example success("Something happened! Here's a warning!");
 */
function warning(string $alertText)
{
	alert('warning','Warning!',$alertText, false);
}

/**
 * @desc Create a warning alert with a redirect.
 * @param string $alertText Text to be shown on alert.
 * @param string $alertRedirectLinkLink Link to go to after alert.
 * @param string $alertRedirectText Friendly name for link.
 * @example warningRedirect("Have a warning! See the game rules", 'gamerules.php', 'Game Rules');
 */
function warningRedirect(string $alertText, string $alertRedirectLinkLink = 'back', string $alertRedirectText = 'Back')
{
	alert('warning','Warning!',$alertText, true, $alertRedirectLinkLink, $alertRedirectText);
}