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
    case "creditguild":
        creditguild();
        break;
}
function viewguild()
{
    global $db, $userid, $api, $h;
    if (isset($_POST['guild'])) {
        //Make sure input is safe.
        $guild = (isset($_POST['guild']) && is_numeric($_POST['guild'])) ? abs(intval($_POST['guild'])) : 0;

        //Validate CSRF check.
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_viewguild', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Go back and submit it quicker!");
            die($h->endpage());
        }

        //Make sure guild is still valid input.
        if (empty($guild)) {
            alert('danger', "Uh Oh!", "You are trying to view an invalid or unspecified guild.");
            die($h->endpage());
        }

        //Select the Guild from database to ensure it exists.
        $q = $db->query("SELECT * FROM `guild` WHERE `guild_id` = {$guild}");
        if ($db->num_rows($q) == 0) {
            alert('danger', "Uh Oh!", "The guild you are trying to view does not exist, or is invalid.");
            die($h->endpage());
        }

        //Assign grabbed information to a variable
        $r = $db->fetch_row($q);

        //Select member count from database.
        $membcount = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `users` WHERE `guild` = {$guild}"));

        //Show the information grabbed.
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

        //Log that the staff member has view this guild's information.
        $api->SystemLogsAdd($userid, 'staff', "Viewed {$r['guild_name']} [{$guild}]'s Guild Info.");
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

function creditguild()
{
    global $db, $userid, $api, $h;
    if (isset($_POST['guild'])) {
        //Make sure all inputs are safe!
        $guild = (isset($_POST['guild']) && is_numeric($_POST['guild'])) ? abs(intval($_POST['guild'])) : 0;
        $prim = (isset($_POST['primary']) && is_numeric($_POST['primary'])) ? abs(intval($_POST['primary'])) : 0;
        $sec = (isset($_POST['secondary']) && is_numeric($_POST['secondary'])) ? abs(intval($_POST['secondary'])) : 0;
        $reason = (isset($_POST['reason'])) ? $db->escape(strip_tags(stripslashes($_POST['reason']))) : '';

        //Validate successful CSRF
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_creditguild', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Go back and submit it quicker!");
            die($h->endpage());
        }

        //Make sure guild is still valid input.
        if (empty($guild)) {
            alert('danger', "Uh Oh!", "You are trying to view an invalid or unspecified guild.");
            die($h->endpage());
        }

        //Make sure the primary/secondary currency is input.
        if ((empty($prim)) && (empty($sec))) {
            alert('danger', "Uh Oh!", "Please input how much Primary Currency and/or Secondary Currency you wish to
            credit to this guild.");
            die($h->endpage());
        }

        //Make sure the reason is input
        if (empty($reason)) {
            alert('danger', "Uh Oh!", "Please input the reason why you are crediting this guild.");
            die($h->endpage());
        }

        //Select the Guild from database to ensure it exists.
        $q = $db->query("SELECT * FROM `guild` WHERE `guild_id` = {$guild}");
        if ($db->num_rows($q) == 0) {
            alert('danger', "Uh Oh!", "The guild you are trying to view does not exist, or is invalid.");
            die($h->endpage());
        }

        //Credit the guild
        $db->query("UPDATE `guild`
                    SET `guild_primcurr` = `guild_primcurr` + {$prim},
                    `guild_seccurr` = `guild_seccurr` + {$sec}
                    WHERE `guild_id` = {$guild}");

        //Put both numbers in a friendly format.
        $secf = number_format($sec);
        $primf = number_format($prim);

        //Notify the guild they've received some cash!
        $api->GuildAddNotification($guild, "The game administration has credited your guild {$primf} Primary Currency
        and/or {$secf} Secondary Currency for reason: {$reason}.");

        //Log the entry
        $api->SystemLogsAdd($userid, 'staff', "Credited Guild ID {$guild} with {$primf} Primary Currency and/or {$secf}
        Secondary Currency with reason '{$reason}'.");

        //Success to the end user.
        alert('success', "Success!", "You have successfully credited Guild ID {$guild} with {$primf} Primary Currency
        and/or {$secf} Secondary Currency with reason '{$reason}'.", true, 'index.php');
        $h->endpage();
    } else {
        //Form to credit a guild.
        $csrf = request_csrf_html('staff_creditguild');
        echo "Select the guild you wish to credit, then enter how much you wish to credit them, and input a reason.
        Submit the form when complete.";
        echo "<form method='post'>
        <table class='table table-bordered'>
        <tr>
            <th>
                Guild
            </th>
            <td>
                " . guilds_dropdown() . "
            </td>
        </tr>
        <tr>
            <th>
                Primary Currency
            </th>
            <td>
                <input type='number' name='primary' value='0' required='1' min='0' class='form-control'>
            </td>
        </tr>
        <tr>
            <th>
                Secondary Currency
            </th>
            <td>
                <input type='number' name='secondary' value='0' required='1' min='0' class='form-control'>
            </td>
        </tr>
        <tr>
            <th>
                Reason
            </th>
            <td>
                <input type='text' name='reason' required='1' class='form-control'>
            </td>
        </tr>
        {$csrf}
        <tr>
            <td colspan='2'>
                <input type='submit' value='Credit Guild' class='btn btn-primary'>
            </td>
        </tr>

        </table>
        </form>";
        $h->endpage();
    }
}
