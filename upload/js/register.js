/*!
	File: 		js/register.js
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Numerous javascript needed specifically for registering.
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
function CheckPasswords(password) {
    $.ajax({
        type: "POST",
        url: "js/script/check.php",
        data: "password=" + escape(password),
        success: function (resps) {
            $("#passwordresult").html(resps);
        }
    });
}
function goBack() {
    window.history.back();
}
function CheckUsername(name) {
    $.ajax({
        type: "POST",
        url: "js/script/checkun.php",
        data: "username=" + escape(name),
        success: function (resps) {
            $("#usernameresult").html(resps);
        }
    });
}
function OutputTeam(team) {
    var value = team.value;
    $.ajax({
        type: "POST",
        url: "js/script/outputteam.php",
        data: "team=" + escape(value),
        success: function (resps) {
            $("#teamresult").html(resps);
        }
    });
}

function CheckEmail(email) {
    $.ajax({
        type: "POST",
        url: "js/script/checkem.php",
        data: "email=" + escape(email),
        success: function (resps) {
            $("#emailresult").html(resps);
        }
    });
}

function PasswordMatch() {
    pwt1 = $("#password").val();
    pwt2 = $("#cpassword").val();
    if (pwt1 == pwt2) {
        document.getElementById('password').className = 'form-control is-valid';
        document.getElementById('cpassword').className = 'form-control is-valid';
        document.getElementById("cpasswordresult").innerHTML = '';
    }
    else {
        document.getElementById('password').className = 'form-control is-invalid';
        document.getElementById('cpassword').className = 'form-control is-invalid';
        document.getElementById("cpasswordresult").innerHTML = 'Passwords do not match.';
    }
}
