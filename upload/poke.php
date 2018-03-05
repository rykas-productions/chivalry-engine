<?php
/*
	File:		poke.php
	Created: 	4/5/2016 at 12:21AM Eastern Time
	Info: 		Allows players to poke other players for fun.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
//I hate Ethan for making this module continue to stay inside the engine.
//Fuck you, dude!
require("globals.php");
$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : '';
if (empty($_GET['user'])) {
    alert('danger', "Uh Oh!", "You cannot poke a user you didn't specify.", true, 'index.php');
    die($h->endpage());
}
if (($_GET['user']) == $userid) {
    alert('danger', "Uh Oh!", "You cannot poke yourself.", true, "profile.php?user={$_GET['user']}");
    die($h->endpage());
}
$q = $db->query("SELECT `username` FROM `users` WHERE `userid` = {$_GET['user']}");
if ($db->num_rows($q) == 0) {
    alert('danger', "Uh Oh!", "You cannot poke a non-existent user.", true, "index.php");
    die($h->endpage());
}
if (isset($_POST['do'])) {
    if (!isset($_POST['verf']) || !verify_csrf_code('poke', stripslashes($_POST['verf']))) {
        alert('danger', "Action Blocked!", "This action has been blocked for your security. Try again and be a little
		    quicker next time.");
        die($h->endpage());
    }
    alert('success', "Success!", "You have poked this user.", true, "profile.php?user={$_GET['user']}");
    $api->SystemLogsAdd($userid, 'pokes', "Poked " . $api->SystemUserIDtoName($_GET['user']) . "[{$_GET['user']}]");
    $api->GameAddNotification($_GET['user'], "You have been poked by <a href='profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}].");
} else {
    echo "You sure you wanna poke " . $api->SystemUserIDtoName($_GET['user']) . "?";
    $csrf = request_csrf_html('poke');
    ?>
    <form method='post'>
        <input type='submit' value='<?php echo "POKE!"; ?>' class='btn btn-primary'>
        <input type='hidden' name='do' value='yes'>
        <?php echo $csrf; ?>
    </form>
<?php
}
$h->endpage();