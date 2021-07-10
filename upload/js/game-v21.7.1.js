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
	$('#cityDeposit').click(function()
	{
		$.post("js/script/city_bank.php", $("#cityBankDeposit").serialize(),  function(response) 
		{   
			 $('#banksuccess').html(response);
		});
		return false;
	});
	$('#cityWithdraw').click(function()
	{
		$.post("js/script/city_bank.php", $("#cityBankWithdraw").serialize(),  function(response) 
		{   
			 $('#banksuccess').html(response);
		});
		return false;
	});
	$('#tokenDeposit').click(function()
	{
		$.post("js/script/token_bank.php", $("#tokenBankDeposit").serialize(),  function(response) 
		{   
			 $('#banksuccess').html(response);
		});
		return false;
	});
	$('#tokenWithdraw').click(function()
	{
		$.post("js/script/token_bank.php", $("#tokenBankWithdraw").serialize(),  function(response) 
		{   
			 $('#banksuccess').html(response);
		});
		return false;
	});
	$('#fedDeposit').click(function()
	{
		$.post("js/script/big_bank.php", $("#fedBankDeposit").serialize(),  function(response) 
		{   
			 $('#banksuccess').html(response);
		});
		return false;
	});
	$('#fedWithdraw').click(function()
	{
		$.post("js/script/big_bank.php", $("#fedBankWithdraw").serialize(),  function(response) 
		{   
			 $('#banksuccess').html(response);
		});
		return false;
	});
	$('#vaultDeposit').click(function()
	{
		$.post("js/script/vault_bank.php", $("#vaultBankDeposit").serialize(),  function(response) 
		{   
			 $('#banksuccess').html(response);
		});
		return false;
	});
	$('#vaultWithdraw').click(function()
	{
		$.post("js/script/vault_bank.php", $("#vaultBankWithdraw").serialize(),  function(response) 
		{   
			 $('#banksuccess').html(response);
		});
		return false;
	});
	$('#estateDeposit').click(function()
	{
		$.post("js/script/estate_bank.php", $("#estateBankDeposit").serialize(),  function(response) 
		{   
			 $('#banksuccess').html(response);
		});
		return false;
	});
	$('#estateWithdraw').click(function()
	{
		$.post("js/script/estate_bank.php", $("#estateBankWithdraw").serialize(),  function(response) 
		{   
			 $('#banksuccess').html(response);
		});
		return false;
	});
	$('#trainNorm').click(function()
	{
		$.post("js/script/gym_train.php", $("#gymTrainNorm").serialize(),  function(response) 
		{   
			 $('#gymsuccess').html(response);
		});
		return false;
	});
	$('#trainChiv').click(function()
	{
		$.post("js/script/chiv_gym_train.php", $("#gymTrainChiv").serialize(),  function(response) 
		{   
			 $('#gymsuccess').html(response);
		});
		return false;
	});
	$('#trainCA').click(function()
	{
		$.post("js/script/ca_gym_train.php", $("#gymTrainCA").serialize(),  function(response) 
		{   
			 $('#gymsuccess').html(response);
		});
		return false;
	});
	$('#trainGuild').click(function()
	{
		$.post("js/script/guild_gym_train.php", $("#gymTrainGuild").serialize(),  function(response) 
		{   
			 $('#gymsuccess').html(response);
		});
		return false;
	});
	$('#gymRefillEnergy').click(function()
	{
		$.get("js/script/temple_quick.php?action=energy",  function(response) 
		{   
			 $('#gymsuccess').html(response);
		});
		return false;
	});
	$('#gymRefillWill').click(function()
	{
		$.get("js/script/temple_quick.php?action=will",  function(response) 
		{   
			 $('#gymsuccess').html(response);
		});
		return false;
	});
	$('#gymFillWill').click(function()
	{
		$.get("js/script/temple_quick.php?action=willall",  function(response) 
		{   
			 $('#gymsuccess').html(response);
		});
		return false;
	});
	$('#gymRefillBrave').click(function()
	{
		$.get("js/script/temple_quick.php?action=brave",  function(response) 
		{   
			 $('#gymsuccess').html(response);
		});
		return false;
	});
	$('#farmWellFillSingle').click(function()
	{
		$.get("js/script/farm_quick.php?action=bucket1",  function(response) 
		{   
			 $('#wellSuccess').html(response);
		});
		return false;
	});
	$('#farmWellFillFive').click(function()
	{
		$.get("js/script/farm_quick.php?action=bucket5",  function(response) 
		{   
			 $('#wellSuccess').html(response);
		});
		return false;
	});
	$('#farmWellFillTen').click(function()
	{
		$.get("js/script/farm_quick.php?action=bucket10",  function(response) 
		{   
			 $('#wellSuccess').html(response);
		});
		return false;
	});
	$('#farmWellFillTwentyFive').click(function()
	{
		$.get("js/script/farm_quick.php?action=bucket25",  function(response) 
		{   
			 $('#wellSuccess').html(response);
		});
		return false;
	});
	$('#farmWellFillFifty').click(function()
	{
		$.get("js/script/farm_quick.php?action=bucket50",  function(response) 
		{   
			 $('#wellSuccess').html(response);
		});
		return false;
	});
	$('#farmWellFillHundred').click(function()
	{
		$.get("js/script/farm_quick.php?action=bucket100",  function(response) 
		{   
			 $('#wellSuccess').html(response);
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
    $("#close-sidebar").click(function() {
        $(".page-wrapper").removeClass("toggled");
			$.post('js/script/menu.php', { value: 1}, 
				function(returnedData){
					 console.log("Disabled sidebar.");
			});
		});
		$("#overlay").click(function() {
        $(".page-wrapper").removeClass("toggled");
			$.post('js/script/menu.php', { value: 1}, 
				function(returnedData){
					 console.log("Disabled sidebar via overlay.");
			});
		});
      $("#show-sidebar").click(function() {
        $(".page-wrapper").addClass("toggled");
		  $.post('js/script/menu.php', { value: 0}, 
				function(returnedData){
					 console.log("Enabled sidebar.");
			});
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
//For the recaptcha system
function enableRecaptchaBtn()
{
   document.getElementById("recaptchabtn").disabled = false;
   document.getElementById("recaptchaForm").submit();
}
function disableRecaptchaBtn()
 {
    document.getElementById("recaptchabtn").disabled = true;
 }