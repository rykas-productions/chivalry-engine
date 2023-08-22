<?php
/*	File:		global_func_style.php
	Created: 	Aug 2, 2021; 7:36:30 PM
	Info: 		
	Author:		MasterGeneral156
	Website: 	https://chivalryisdeadgame.com/
*/

function createProgressBar($barValue, $barMin = 0, $barMax = 100, $barType = 'primary', $hideBonus = false)
{
    $percent = round($barValue / $barMax * 100);
    $txt = ($hideBonus) ? "{$percent}%" : "{$percent}% (" . number_format($barValue) . " / " . number_format($barMax). ")";
    return "<div class='progress' style='height: 1rem;'>
				<div class='progress-bar bg-{$barType} progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$barValue}' style='width:{$percent}%' aria-valuemin='0' aria-valuemax='{$barMax}'>
					<span>
						{$txt}
					</span>
				</div>
			</div>";
}

function successProgressBar($barValue, $barMin = 0, $barMax = 100, $hideBonus = false)
{
    return createProgressBar($barValue, $barMin, $barMax, 'success', $hideBonus);
}

function dangerProgressBar($barValue, $barMin = 0, $barMax = 100, $hideBonus = false)
{
    return createProgressBar($barValue, $barMin, $barMax, 'danger', $hideBonus);
}

function warningProgressBar($barValue, $barMin = 0, $barMax = 100, $hideBonus = false)
{
    return createProgressBar($barValue, $barMin, $barMax, 'warning', $hideBonus);
}

function infoProgressBar($barValue, $barMin = 0, $barMax = 100, $hideBonus = false)
{
    return createProgressBar($barValue, $barMin, $barMax, 'info', $hideBonus);
}

function secondaryProgressBar($barValue, $barMin = 0, $barMax = 100, $hideBonus = false)
{
    return createProgressBar($barValue, $barMin, $barMax, 'secondary', $hideBonus);
}

function lightProgressBar($barValue, $barMin = 0, $barMax = 100, $hideBonus = false)
{
    return createProgressBar($barValue, $barMin, $barMax, 'light', $hideBonus);
}

function darkProgressBar($barValue, $barMin = 0, $barMax = 100, $hideBonus = false)
{
    return createProgressBar($barValue, $barMin, $barMax, 'dark', $hideBonus);
}

function scaledColorProgressBar($barValue, $barMin = 0, $barMax = 100, $hideBonus = false)
{
    $percent = round($barValue / $barMax * 100);
    if ($percent <= 33)
        return dangerProgressBar($barValue, $barMin, $barMax, $hideBonus);
    elseif (($percent > 33) && ($percent <= 66))
        return warningProgressBar($barValue, $barMin, $barMax, $hideBonus);
    else
        return successProgressBar($barValue, $barMin, $barMax, $hideBonus);
}

function loadGamblingAlert()
{
    global $ir;
    alert('info',"","You have won " . shortNumberParse($ir['winnings_this_hour']) . " / " . shortNumberParse((calculateUserMaxBet($ir['userid']) * 15) * 20) . " Copper Coins this hour.", false);
}

function createBadge($text, $theme = 'primary')
{
    return "<span class='badge badge-{$theme}'>{$text}</span>";
}

function createDangerBadge($text)
{
    return createBadge($text, 'danger');
}

function createPrimaryBadge($text)
{
    return createBadge($text, 'primary');
}

function createSecondaryBadge($text)
{
    return createBadge($text, 'secondary');
}

function createWarningBadge($text)
{
    return createBadge($text, 'warning');
}

function createSuccessBadge($text)
{
    return createBadge($text, 'success');
}

function createInfoBadge($text)
{
    return createBadge($text, 'info');
}

function parseUserID($userid)
{
    return createBadge($userid);
}