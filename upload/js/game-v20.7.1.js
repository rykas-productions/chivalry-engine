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
	$(".updateHoverBtn").mouseover(function() 
	{
		$.post("js/script/hover_update.php", "", function(response) 
		{   
			 $('#socialRow2').html(response);
		});
	});

});
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});

function profileButtonAttack(user, stat)
{
	if (stat == 1)
		document.getElementById("profileTxt").innerHTML = user + " is currently in the infirmary.";
	else if (stat == 2)
		document.getElementById("profileTxt").innerHTML = user + " is currently in the dungeon.";
	else if (stat == 3)
		document.getElementById("profileTxt").innerHTML = "You are in the dungeon and cannot attack " + user + ".";
	else if (stat == 4)
		document.getElementById("profileTxt").innerHTML = "You are in the infirmary and cannot attack " + user + ".";
	else if (stat == 5)
		document.getElementById("profileTxt").innerHTML = user + " has no HP and cannot be attacked.";
	else if (stat == 6)
		document.getElementById("profileTxt").innerHTML = "You have no HP and cannot attack " + user + ".";
	else
		document.getElementById("profileTxt").innerHTML = "Attack " + user + ".";
	
}

function profileButtonCash(user)
{
	document.getElementById("profileTxt").innerHTML = "Send " + user + " some Copper Coins.";
}

function profileButtonMail(user)
{
	document.getElementById("profileTxt").innerHTML = "Send " + user + " an in-game message.";
}

function profileButtonReport(user)
{
	document.getElementById("profileTxt").innerHTML = "Report " + user + " to Chivalry is Dead staff.";
}

function profileButtonSpy(user)
{
	document.getElementById("profileTxt").innerHTML = "Hire a spy on " + user + ".";
}

function profileButtonContact(user)
{
	document.getElementById("profileTxt").innerHTML = "Add " + user + " to your contacts list.";
}

function profileButtonBounty(user)
{
	document.getElementById("profileTxt").innerHTML = "Place bounty on " + user + ".";
}

function profileButtonPoke(user)
{
	document.getElementById("profileTxt").innerHTML = "Poke " + user + ".";
}
function profileButtonFriend(user)
{
	document.getElementById("profileTxt").innerHTML = "Add " + user + " as your friend.";
}
function profileButtonEnemy(user)
{
	document.getElementById("profileTxt").innerHTML = "Add " + user + " as your enemy.";
}
function profileButtonTheft(user)
{
	document.getElementById("profileTxt").innerHTML = "Rob " + user + " of Copper Coins.";
}
function profileButtonBlock(user)
{
	document.getElementById("profileTxt").innerHTML = "Block contact with " + user + ".";
}
 function enableBtn()
{
   document.getElementById("recaptchabtn").disabled = false;
}