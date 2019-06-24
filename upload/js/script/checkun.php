<?php
/*
	File: 		js/script/check.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Checks the inputted username to see if its already in use, or invalid.
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
$menuhide = 1;
if (isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD'])) {
    if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
        // Ignore a GET request
        header('HTTP/1.1 400 Bad Request');
        exit;
    }
}
require_once('../../global_func.php');
if (!isAjax()) {
    header('HTTP/1.1 400 Bad Request');
    exit;
}
require_once('../../globals_nonauth.php');
$username = isset($_POST['username']) ? stripslashes($_POST['username']) : '';
$e_username = $db->escape($username);
$q = $db->query("SELECT COUNT(`userid`) FROM users WHERE username = '{$e_username}'");
if (empty($username)) {
    $newclass = 'form-control is-invalid';
    $warning = "Please enter a username.";

} else if ((strlen($username) < 3)) {
    $newclass = 'form-control is-invalid';
    $warning = "Username must be, at least, 3 characters long.";
} else if ((strlen($username) > 21)) {
    $newclass = 'form-control is-invalid';
    $warning = "Username must be, at most, 20 characters long.";
} else if ($db->fetch_single($q)) {
    $newclass = 'form-control is-invalid';
    $warning = "Username already in use.";
} else {
    $newclass = 'form-control is-valid';
    $warning = "";
}
?>
    <script>
        var d = document.getElementById("username");
        var div = document.getElementById("usernameresult");
        d.className = " <?php echo $newclass; ?>";
        div.innerHTML = " <?php echo $warning; ?>";
    </script>
<?php
$db->free_result($q);
