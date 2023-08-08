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
    /**
     * @internal
     * @desc            Create a card, optionally with a title. Please be 
     *                  sure to call $styl->endCard(); to properly close a card.
     * @param string    $cardTitle Title of card, shown to the player.
     */
    function createCard($cardTitle = null)
    {
        if ($cardTitle != null)
        {
            echo "<div class='card'><div class='card-header'>{$cardTitle}</h5></div>
                                        <div class='card-body'><p class='card-text'>";
        }
        else
        {
            echo "<div class='card'><div class='card-body'><p class='card-text'>";
        }
    }
    
    /**
     * @desc            End a card. Required if calling $styl->createCard($cardTitle);
     * @internal
     */
    function endCard()
    {
        echo "</p></div></div>";
    }
    
    /**
     * @desc            Parses a number into a shorter version of the number.
     * @example         2,010,313 parses to 2.01M
     * @param           number $n
     * @since           8/07/2024
     */
    function number_format($n)
    {
        $symbol="";
        if ($n < 0)
        {
            $neg = 1;
            $n = $n * -1;
            $symbol="-";
        }
        if ($n < 1000)
            $n_format = number_format($n);
            elseif ($n < 10000)
            $n_format = number_format($n / 1000, 2) . "K";
            elseif ($n < 1000000)
            $n_format = number_format($n / 1000, 1) . "K";
            elseif ($n < 1000000000)
            $n_format = number_format($n / 1000000, 1) . "M";
            elseif ($n < 1000000000000)
            $n_format = number_format($n / 1000000000, 1) . "B";
            elseif ($n < 1000000000000000)
            $n_format = number_format($n / 1000000000000, 1) . "T";
            elseif ($n < 1000000000000000000)
            $n_format = number_format($n / 1000000000000000, 1) . " Q";
            elseif ($n < 1000000000000000000000)
            $n_format = number_format($n / 1000000000000000000, 1) . " S";
            elseif ($n < 1000000000000000000000000)
            $n_format = number_format($n / 1000000000000000000000, 1) . " Sextillion";
            else
                $n_format = number_format($n);
        return "<span data-toggle='tooltip' data-placement='top' title='" . number_format($n) . "'>{$symbol}{$n_format}</span>";
    }
}