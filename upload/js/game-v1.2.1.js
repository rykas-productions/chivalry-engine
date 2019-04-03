/*!
 File: js/game.js
 Created: 3/15/2016 at 10:46AM Eastern Time
 Info: Misc. javascript functions for use around the game.
 Author: TheMasterGeneral
 Website: https://github.com/MasterGeneral156/chivalry-engine
 */
$(document).ready(function () {
    $('#sendcash').click(function()
	{
		$.post("js/script/sendcash.php", $("#cashpopupForm").serialize(),  function(response) 
		{   
			 $('#successcash').html(response);
		});
		return false;
	});
    $('#sendmessage').click(function()
	{
		$.post("js/script/sendmail.php", $("#mailpopupForm").serialize(),  function(response) 
		{   
			 $('#success').html(response);
		});
		return false;
	});
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
    $('#toast').toast('show');

});
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});