<?php
/*
	File:		functions/func_escape.php
	Created: 	9/22/2019 at 6:48PM Eastern Time
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
 * @desc Makes the text input safe for use.
 * @param string $text
 * @return string Escaped Text.
 */
function makeSafeText(string $text)
{
	global $db;
	$text = (isset($text) && is_string($text)) ? stripslashes($text) : '';
	return $db->escape(htmlentities($text, ENT_QUOTES, 'ISO-8859-1'));
}

/**
 * @desc Makes the number input safe for use.
 * @param int $int
 * @return int Safe Int
 */
function makeSafeInt(int $int)
{
	return (isset($int) && is_numeric($int)) ? abs($int) : 0;
}

/**
 * @desc Removes all tags and slashes from string.
 * @param string $text
 * @return string
 */
function stripAll(string $text)
{
    return htmlentities(strip_tags(stripslashes($text)), ENT_QUOTES, 'ISO-8859-1');
}