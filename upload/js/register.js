/*!
	File: js/register.js
	Created: 3/15/2016 at 10:45AM Eastern Time
	Info: Javascript functions for registering
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
function CheckPasswords(password)
{
    $.ajax({
        type : "POST",
        url : "js/script/check.php",
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
        url : "js/script/checkun.php",
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
        url : "js/script/outputteam.php",
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
        url : "js/script/checkem.php",
        data : "email=" + escape(email),
        success : function(resps)
        {
            $("#emailresult").html(resps);
        }
    });
}

function PasswordMatch()
{
    pwt1 = $("#password").val();
    pwt2 = $("#cpassword").val();
    if (pwt1 == pwt2)
    {
        document.getElementById('cpassword').style.backgroundColor = '#dff0d8';
		document.getElementById('password').style.backgroundColor = '#dff0d8';
    }
    else
    {
        document.getElementById('cpassword').style.backgroundColor = '#f2dede';
		document.getElementById('password').style.backgroundColor = '#f2dede';
    }
}
