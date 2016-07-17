/*!
	File: js/game.js
	Created: 3/15/2016 at 10:46AM Eastern Time
	Info: Misc. javascript functions for use around the game.
	Author: TheMasterGeneral
	Website: http://mastergeneral156.pcriot.com/
*/
$(document).ready(function()
{
	$('#sendmessage').click(function()
	{
		$.post("js/script/sendmail.php", $("#mailpopupForm").serialize(),  function(response) 
		{   
			 $('#success').html(response);
		});
		return false;
	});

});
$(document).ready(function()
{
    $('[data-toggle="tooltip"]').tooltip(); 
});