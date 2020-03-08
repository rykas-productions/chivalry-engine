<?php
/*
	File:		functions/func_format.php
	Created: 	9/29/2019 at 10:23PM Eastern Time
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
 * Internal function to format unread mail notification.
 * @return string Formatted mail output
 */
function returnFormattedUnreadMail()
{
	$q=returnUnreadMailCount();
	if ($q == 0)
		return "You have 0 unread messages. View <a href='#'>here</a>.<br />";
	else
		return "<span class='text-danger'>You have {$q} unread messages. View <a href='#'>here</a>.</span><br />";
}

/**
 * Internal function to format the amount of time remaining in the infirmary.
 * @return string Formatted infirmary output
 */
function returnFormattedInfirmary()
{
	if (checkInfirmary())
	{
		$q=returnRemainingInfirmaryTime();
		if ($q == 0)
			return "";
		else
			return "<span class='text-danger'>You are in the infirmary for " . parseTimeUntil($q + returnUnixTimestamp()) . ".</span><br />";
	}
	else
		return "";
}

/**
 * @desc Format the Unix Timestamp into date/time.
 * @param int $timeStamp Unix timestamp
 * @return string Formatted Date/Time.
 */
function parseDateTime(int $timeStamp)
{
	if ($timeStamp == 0)
        return "N/A";
	return date('F j, Y, g:i:s a', $timeStamp);
}

/**
 * @desc Format how long until the Unix Timestamp from the current Unix Timestamp, then format it into date/time.
 * @param int $timestamp Future Unix Timestamp
 * @return string Formatted Future Date/Time.
 */
function parseTimeUntil(int $timestamp)
{
	$time_difference = $timestamp - time();
    $unit = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade', 'century');
    $lengths = array(60, 60, 24, 7, 4.35, 12, 10, 10);

    for ($i = 0; $time_difference >= $lengths[$i]; $i++) {
        $time_difference = $time_difference / $lengths[$i];
    }
	
    $time_difference = round($time_difference);
    $date = $time_difference . ' ' . $unit[$i] . (($time_difference > 1 OR $time_difference < 1) ? 's' : '') . '';
    return $date;
}

/**
 * @desc Format the unread announcements.
 * @return string Formatted Unread Announcements
 */
function returnFormattedAnnouncement()
{
	global $ir;
	if ($ir['unreadAnnouncements'] == 0)
		return "You have 0 unread announcements.<br />";
	else
		return "<span class='text-danger'>You have {$ir['unreadAnnouncements']} unread announcements.</span><br />";
}