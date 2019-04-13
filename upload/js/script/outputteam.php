<?php
/*
	File: js//script/outputteam.php
	Created: 4/4/2017 at 7:10PM Eastern Time
	Info: PHP file for outputting info about the selected team
	when registering
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
$menuhide = 1;
if (isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD'])) {
    if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
        // Ignore a GET request
        header('HTTP/1.1 400 Bad Request');
        //exit;
    }
}
require_once('../../global_func.php');
if (!isAjax()) {
    header('HTTP/1.1 400 Bad Request');
    //exit;
}

require_once('../../globals_nonauth.php');
$class = $_POST['team'];
if ($class == 'Warrior') {
    $warning = "Warriors begin with more {$_CONFIG['strength_stat']} and less {$_CONFIG['guard_stat']}.";
    $newclass = 'form-control is-valid';
} elseif ($class == 'Rogue') {
    $warning = "Rogues start with more {$_CONFIG['agility_stat']} and less {$_CONFIG['strength_stat']}.";
    $newclass = 'form-control is-valid';
} elseif ($class == 'Defender') {
    $warning = "Defenders begin with more {$_CONFIG['guard_stat']} and less {$_CONFIG['agility_stat']}.";
    $newclass = 'form-control is-valid';
} else {
    $warning = "Please select a valid class.";
    $newclass = 'form-control is-invalid';
}
?>
    <script>
        var d = document.getElementById("class");
        var div = document.getElementById("teamresult");
        d.className = " <?php echo $newclass; ?>";
        div.innerHTML = " <?php echo $warning; ?>";
    </script>
<?php