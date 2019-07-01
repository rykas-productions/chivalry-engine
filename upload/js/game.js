/*!
	File: 		js/game.js
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Numerous javascript needed specifically for Chivalry Engine.
	Author: 	TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
	
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
$(document).ready(function () {
    $('#btnAdd').click(function () {
        var num = $('.clonedInput').length;
        var newNum = new Number(num + 1);
        var newElem = $('#input' + num).clone().attr('id', 'input' + newNum);
        var newElem1 = $('#otherinput' + num).clone().attr('id', 'otherinput' + newNum);

        newElem1.children(':first').attr('id', 'required_item_qty' + newNum).attr('name', 'required_item_qty' + newNum);
        newElem.children(':first').attr('id', 'required_item' + newNum).attr('name', 'required_item' + newNum);

        $('#otherinput' + num).after(newElem1);
        $('#input' + num).after(newElem);
        $('#btnDel').prop('disabled', false);

        if (newNum == 5)
            $('#btnAdd').attr('disabled', 'disabled');
    });
    $('#btnDel').click(function () {
        var num = $('.clonedInput').length;

        $('#input' + num).remove();
        $('#otherinput' + num).remove();
        $('#btnAdd').attr('disabled', '');

        if (num - 1 == 1)
            $('#btnDel').attr('disabled', 'disabled');
        $('#btnAdd').prop('disabled', false);
    });
    $('#btnDel').attr('disabled', 'disabled');
    var remove = localStorage.getItem("toggle");
    console.log(remove);
    $(".page-wrapper").removeClass(remove);

});
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})
