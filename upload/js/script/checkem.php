<?php
/*
	File: js//script/checkem.php
	Created: 4/4/2017 at 7:09PM Eastern Time
	Info: PHP file for checking a user's inputted email
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
$menuhide=1;
if (isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD'])) {
    if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
        // Ignore a GET request
        header('HTTP/1.1 400 Bad Request');
        exit;
    }
}
require_once('../../global_func.php');
if (!is_ajax()) {
    header('HTTP/1.1 400 Bad Request');
    exit;
}

require_once('../../globals_nonauth.php');
$email = isset($_POST['email']) ? stripslashes($_POST['email']) : '';
if (isset($email)) {
    $e_email = $db->escape($email);
    $q = $db->query("SELECT COUNT(`userid`) FROM users WHERE `email` = '{$e_email}'");
    if (empty($email)) {
        $newclass = 'form-control is-invalid';
        $warning="Please specify a valid email.";
    }
    else if (!valid_email($email)) {
        $newclass = 'form-control is-invalid';
        $warning = "Please specify a valid email.";
    }
    else if ($db->fetch_single($q) != 0) {
        $newclass = 'form-control is-invalid';
        $warning = "The email address you've chosen is already in use.";
    } else {
        $newclass = 'form-control is-valid';
        $warning='';
    }
    ?>
    <script>
        var d = document.getElementById("email");
        var div = document.getElementById("emailresult");
        d.className = " <?php echo $newclass; ?>";
        div.innerHTML = " <?php echo $warning; ?>";
    </script>
<?php
}
$db->free_result($q);
