/*
Code by TheMasterGeneral
Date: 2/8/2016
Filename: register.js
All Rights Reserved.
*/
function CheckPasswords(password)
{
    $.ajax({
        type : "POST",
        url : "js/check.php",
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
        url : "js/checkun.php",
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
        url : "js/outputteam.php",
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
        url : "js/checkem.php",
        data : "email=" + escape(email),
        success : function(resps)
        {
            $("#emailresult").html(resps);
        }
    });
}
function UpdateStats(team)
{
    $.ajax({
        type : "POST",
        url : "js/update_stats.php",
        data : "class=" + escape(team),
        success : function(resps)
        {
            $("#statresult").html(resps);
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
		document.getElementById('pw1').style.backgroundColor = '#dff0d8';
    }
    else
    {
        document.getElementById('pw2').style.backgroundColor = '#f2dede';
		document.getElementById('pw1').style.backgroundColor = '#f2dede';
    }
}
