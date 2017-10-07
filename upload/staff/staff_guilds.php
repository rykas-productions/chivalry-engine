<?php
/*
	File: staff/staff_guilds.php
	Created: 10/07/2017 at 5:38PM Eastern Time
	Info: Staff panel for handling/editing guilds
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/
require('sglobals.php');
//Check for proper staff privledges
if ($api->UserMemberLevelGet($userid, 'assistant') == false) {
    alert('danger', "Uh Oh!", "You do not have permission to be here.", true, 'index.php');
    die($h->endpage());
}
//Set the GET
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
//Cycle through the actions available for GET
switch ($_GET['action']) {
    case "viewguild":
        viewguild();
        break;
}
function viewguild()
{
    global $db, $userid, $api, $h;
    if (isset($_POST['guild'])) {

    } else {
        //Basic form to select the guild.
        $csrf = request_csrf_html('staff_viewguild');
        echo "Select the guild from the dropdown you wish to view, then submit the form.<br />
        <form method='post'>
        " . guilds_dropdown() . "
        {$csrf}<br />
        <input type='submit' value='View Guild' class='btn btn-primary'>
        </form>";
        $h->endpage();
    }
}