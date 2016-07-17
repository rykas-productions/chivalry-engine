/*!
	File: js/register.js
	Created: 3/15/2016 at 10:45AM Eastern Time
	Info: Javascript functions for registering
	Author: TheMasterGeneral
	Website: http://mastergeneral156.pcriot.com/
*/
function CheckPasswords(password)
{
    $.ajax({
        type : "POST",
        url : "check.php",
        data : "password=" + escape(password),
        success : function(resps)
        {
            $("#passwordresult").html(resps);
        }
    });
}
function goBack() { window.history.back(); }
function CheckUsername(name)
{
    $.ajax({
        type : "POST",
        url : "checkun.php",
        data : "username=" + escape(name),
        success : function(resps)
        {
            $("#usernameresult").html(resps);
        }
    });
}
function OutputTeam(team)
{
	var value = team.value;
    $.ajax({
        type : "POST",
        url : "outputteam.php",
        data : "team=" + escape(value),
        success : function(resps)
        {
            $("#teamresult").html(resps);
        }
    });
}

function CheckEmail(email)
{
    $.ajax({
        type : "POST",
        url : "checkem.php",
        data : "email=" + escape(email),
        success : function(resps)
        {
            $("#emailresult").html(resps);
        }
    });
}

function PasswordMatch()
{
    pwt1 = $("#pw1").val();
    pwt2 = $("#pw2").val();
    if (pwt1 == pwt2)
    {
        document.getElementById('pw2').style.backgroundColor = '#dff0d8';
		document.getElementById("submit").className = "btn btn-lg btn-success";
    }
    else
    {
        document.getElementById('pw2').style.backgroundColor = '#f2dede';
		document.getElementById("submit").className = "btn btn-lg btn-danger";
    }
}
