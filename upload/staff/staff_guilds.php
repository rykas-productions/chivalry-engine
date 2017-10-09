<?php
/*
	File: staff/staff_guilds.php
	Created: 10/07/2017 at 12:17PM Eastern Time
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
        $guild = (isset($_POST['guild']) && is_numeric($_POST['guild'])) ? abs(intval($_POST['guild'])) : 0;
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_viewguild', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Go back and submit it quicker!");
            die($h->endpage());
        }
        if (empty($guild))
        {
            alert('danger', "Uh Oh!", "You are trying to view an invalid or unspecified guild.");
            die($h->endpage());
        }
        $q=$db->query("SELECT * FROM `guild` WHERE `guild_id` = {$guild}");
        if ($db->num_rows($q) == 0)
        {
            alert('danger', "Uh Oh!", "The guild you are trying to view does not exist, or is invalid.");
            die($h->endpage());
        }
        $r = $db->fetch_row($q);
        $membcount=$db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `guild` = {$guild}"));
        echo "<h3>Viewing Guild Info for Guild ID {$guild}</h3>";
        echo "<table class='table table-bordered'>
        <tr>
            <th>
                Guild Name
            </th>
            <td>
                {$r['guild_name']}
            </td>
        </tr>
        <tr>
            <th>
                Guild Description
            </th>
            <td>
                {$r['guild_desc']}
            </td>
        </tr>
        <tr>
            <th>
                Guild Owner
            </th>
            <td>
                {$api->SystemUserIDtoName($r['guild_owner'])} [{$r['guild_owner']}]
            </td>
        </tr>
        <tr>
            <th>
                Guild Co-Owner
            </th>
            <td>
                {$api->SystemUserIDtoName($r['guild_coowner'])} [{$r['guild_coowner']}]
            </td>
        </tr>
        <tr>
            <th>
                Primary Currency
            </th>
            <td>
                " . number_format($r['guild_primcurr']) . "
            </td>
        </tr>
        <tr>
            <th>
                Secondary Currency
            </th>
            <td>
                " . number_format($r['guild_seccurr']) . "
            </td>
        </tr>
        <tr>
            <th>
                Has Armory?
            </th>
            <td>
                {$r['guild_hasarmory']}
            </td>
        </tr>all
        <tr>
            <th>
                Guild Members / Max Capacity
            </th>
            <td>
                " . number_format($membcount) . " / " . number_format($r['guild_capacity']) . "
            </td>
        </tr>
        <tr>
            <th>
                Guild Level
            </th>
            <td>
                " . number_format($r['guild_level']) . "
            </td>
        </tr>
        <tr>
            <th>
                Guild Experience
            </th>
            <td>
                " . number_format($r['guild_xp']) . "
            </td>
        </tr>
        </table>";
        $api->SystemLogsAdd($userid,'staff',"Viewed {$r['guild_name']} [{$guild}]'s Guild Info.");
        $h->endpage();

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