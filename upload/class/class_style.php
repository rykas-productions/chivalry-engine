<?php
/*
	File:		class_style.php
	Created:	Sep 4, 2022 at 1:22:24 PM Eastern Time
	Author:		TheMasterGeneral
	Website:	https://github.com/rykas-productions/chivalry-engine
	MIT License
	Copyright (c) 2022 TheMasterGeneral
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
if (!defined('MONO_ON')) {
    exit;
}

if (!function_exists('error_critical')) {
    // Umm...
    die('<h1>Error</h1>' . 'Error handler not present');
}

class style
{
    function createCard($cardTitle = null)
    {
        if ($cardTitle != null)
        {
            return "<div class='card'><div class='card-header'>{$cardTitle}</h5></div>
                                        <div class='card-body'><p class='card-text'>";
        }
        else
        {
            return "<div class='card'><div class='card-body'><p class='card-text'>";
        }
    }
    
    function endCard()
    {
        return "</p></div></div>";
    }
}