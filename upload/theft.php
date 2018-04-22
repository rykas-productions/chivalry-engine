<?php
/*
	File:		theft.php
	Created: 	10/18/2017 at 1:37PM Eastern Time
	Info: 		Steal currency from other players.
	Author:		TheMasterGeneral
	Website: 	http://chivalryisdead.x10.mx/
*/
require('globals.php');
$chance = Random(1, 2);
$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : '';
if (empty($_GET['user'])) {
    alert('danger', "Uh Oh!", "Please specify the user you wish to rob.", true, 'index.php');
    die($h->endpage());
}
if ($api->UserInfoGet($userid, 'brave', true) < 25) {
    alert('danger', "Uh Oh!", "You need 25% Brave to rob someone.", true, 'index.php');
    die($h->endpage());
}
$q = $db->query("/*qc=on*/SELECT `primary_currency` FROM `users` WHERE `userid` = {$_GET['user']}");
if ($db->num_rows($q) == 0) {
    alert('danger', "Uh Oh!", "The user you're trying to rob is invalid or does not exist.", true, 'index.php');
    die($h->endpage());
}
$r = $db->fetch_single($q);
if ($_GET['user'] == $userid) {
    alert('danger', "Uh Oh!", "You cannot rob yourself.", true, 'index.php');
    die($h->endpage());
}
if ($api->UserHasItem($_GET['user'], 32, 1)) {
    alert('danger', "Uh Oh!", "This user has theft protection and thus, cannot be robbed.", true, 'index.php');
    die($h->endpage());
}
if ($r < 10) {
    alert('danger', "Uh Oh!", "This user does not have the minimum required cash out to be robbed.", true, 'index.php');
    die($h->endpage());
}
if ($api->UserInfoGet($_GET['user'], 'level') < 10) {
    alert('danger', "Uh Oh!", "You cannot rob players under level 10.", true, 'index.php');
    die($h->endpage());
}
$robbed = $api->SystemUserIDtoName($_GET['user']);
if (isset($_POST['rob'])) {
    if (!isset($_POST['verf']) || !verify_csrf_code("rob_{$_GET['user']}", stripslashes($_POST['verf']))) {
        alert('danger', "Action Blocked!", "Your action has been blocked for security reasons. Form requests expire
        fairly quickly. Be sure to be quicker next time.");
        die($h->endpage());
    }
    $api->UserInfoSet($userid, 'brave', -25, true);
    $minimum = round($r * 0.05);
    $maximum = round($r * 0.1);
	$specialnumber=((getSkillLevel($userid,14)*5)/100);
    $stolen = Random($minimum, $maximum);
	$stolen = $stolen+($stolen*$specialnumber);
	$stolenn = number_format($stolen);
    if ($chance == 1) {
        alert('danger', "Uh Oh!", "You attempted to rob {$robbed}, but got destroyed by their over-protective step mother.", true, 'dungeon.php');
        $api->SystemLogsAdd($userid, 'theft', "Robbed {$robbed} [{$_GET['user']}] but failed.");
        $api->GameAddNotification($_GET['user'], "{$ir['username']} [{$userid}] attempted to rob you, but was stopped by your over-protective step mother.");
        die($h->endpage());
    } else {
        alert("success", "Success!", "You have successfully robbed {$robbed} of {$stolenn} Copper Coins.");
        $api->GameAddNotification($_GET['user'], "<a href='profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}] has robbed you of {$stolenn} Copper Coins.");
        $api->UserTakeCurrency($_GET['user'], 'primary', $stolen);
        $api->UserGiveCurrency($userid, 'primary', $stolen);
        $api->SystemLogsAdd($userid, 'theft', "Robbed {$robbed} [{$_GET['user']}] and stole {$stolenn} Copper Coins.");
    }

} else {
    $csrf = request_csrf_html("rob_{$_GET['user']}");
    echo "Are you sure you want to rob {$robbed}? It will cost you 25% Brave to do so.<br />
    <form method='post'>
        <input type='hidden' name='rob' value='yes'>
        {$csrf}
        <input type='submit' value='Rob {$robbed}' class='btn btn-primary'>
    </form>";
}
$h->endpage();