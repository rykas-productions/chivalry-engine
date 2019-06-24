<?php
/*
	File: 		staff/staff_guilds.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows staff to do actions relating to the in-game guilds.
	Author: 	TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine/
	
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
require('sglobals.php');
//Check for proper staff privledges
if (!$api->user->getStaffLevel($userid, 'assistant')) {
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
    case "viewwars":
        viewwars();
        break;
    case "endwar":
        endwar();
        break;
    case "editguild":
        editguild();
        break;
    case "delguild":
        delguild();
        break;
    case "addcrime":
        addcrime();
        break;
    case "delcrime":
        delcrime();
        break;
    default:
        alert('danger', "Uh Oh!", "Please select a valid action to perform.", true, 'index.php');
        die($h->endpage());
        break;
}
function viewguild()
{
    global $db, $userid, $api, $h, $_CONFIG;
    if (isset($_POST['guild'])) {
        //Make sure input is safe.
        $guild = (isset($_POST['guild']) && is_numeric($_POST['guild'])) ? abs(intval($_POST['guild'])) : 0;

        //Validate CSRF check.
        if (!isset($_POST['verf']) || !checkCSRF('staff_viewguild', stripslashes($_POST['verf']))) {
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
                {$api->user->getNamefromID($r['guild_owner'])} [{$r['guild_owner']}]
            </td>
        </tr>
        <tr>
            <th>
                Guild Co-Owner
            </th>
            <td>
                {$api->user->getNamefromID($r['guild_coowner'])} [{$r['guild_coowner']}]
            </td>
        </tr>
        <tr>
            <th>
                {$_CONFIG['primary_currency']}
            </th>
            <td>
                " . number_format($r['guild_primcurr']) . "
            </td>
        </tr>
        <tr>
            <th>
                {$_CONFIG['secondary_currency']}
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
        </tr>
        <tr>
            <th>
                Guild Members / Max Capacity
            </th>
            <td>
                " . number_format($membcount) . " / " . number_format($r['guild_level'] * 5) . "
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
        $api->game->addLog($userid, 'staff', "Viewed {$r['guild_name']} [{$guild}]'s Guild Info.");
        $h->endpage();

    } else {
        //Basic form to select the guild.
        $csrf = getHtmlCSRF('staff_viewguild');
        echo "Select the guild from the dropdown you wish to view, then submit the form.<br />
        <form method='post'>
        " . dropdownGuild() . "
        {$csrf}<br />
        <input type='submit' value='View Guild' class='btn btn-primary'>
        </form>";
        $h->endpage();
    }
}

function creditguild()
{
    global $db, $userid, $api, $h, $_CONFIG;
    if (isset($_POST['guild'])) {
        //Make sure all inputs are safe!
        $guild = (isset($_POST['guild']) && is_numeric($_POST['guild'])) ? abs(intval($_POST['guild'])) : 0;
        $prim = (isset($_POST['primary']) && is_numeric($_POST['primary'])) ? abs(intval($_POST['primary'])) : 0;
        $sec = (isset($_POST['secondary']) && is_numeric($_POST['secondary'])) ? abs(intval($_POST['secondary'])) : 0;
        $reason = (isset($_POST['reason'])) ? $db->escape(strip_tags(stripslashes($_POST['reason']))) : '';

        //Validate successful CSRF
        if (!isset($_POST['verf']) || !checkCSRF('staff_creditguild', stripslashes($_POST['verf']))) {
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
            alert('danger', "Uh Oh!", "Please input how much {$_CONFIG['primary_currency']} and/or {$_CONFIG['secondary_currency']} you wish to
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
        $api->guild->addNotification($guild, "The game administration has credited your guild {$primf} {$_CONFIG['primary_currency']}
        and/or {$secf} {$_CONFIG['secondary_currency']} for reason: {$reason}.");

        //Log the entry
        $api->game->addLog($userid, 'staff', "Credited Guild ID {$guild} with {$primf} {$_CONFIG['primary_currency']} and/or {$secf}
        {$_CONFIG['secondary_currency']} with reason '{$reason}'.");

        //Success to the end user.
        alert('success', "Success!", "You have successfully credited Guild ID {$guild} with {$primf} {$_CONFIG['primary_currency']}
        and/or {$secf} {$_CONFIG['secondary_currency']} with reason '{$reason}'.", true, 'index.php');
        $h->endpage();
    } else {
        //Form to credit a guild.
        $csrf = getHtmlCSRF('staff_creditguild');
        echo "Select the guild you wish to credit, then enter how much you wish to credit them, and input a reason.
        Submit the form when complete.";
        echo "<form method='post'>
        <table class='table table-bordered'>
        <tr>
            <th>
                Guild
            </th>
            <td>
                " . dropdownGuild() . "
            </td>
        </tr>
        <tr>
            <th>
                {$_CONFIG['primary_currency']}
            </th>
            <td>
                <input type='number' name='primary' value='0' required='1' min='0' class='form-control'>
            </td>
        </tr>
        <tr>
            <th>
                {$_CONFIG['secondary_currency']}
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

function viewwars()
{
    global $db, $userid, $api, $h;
    echo "<h3>Viewing Guild Wars</h3>
    <table class='table table-bordered'>";
    //Select wars from database that are active.
    $q = $db->query("SELECT * FROM `guild_wars`
                    WHERE `gw_winner` = 0 AND
                    `gw_end` > " . time() . "
                    ORDER BY `gw_id` DESC");
    //If no active wars, tell the user.
    if ($db->num_rows($q) == 0) {
        alert('danger', "Uh Oh!", "There are not any active guild wars at this time.", true, 'index.php');
        die($h->endpage());
    }
    //Request CSRF token
    $csrf = getCodeCSRF('staff_guild_end_war');
    //Display the wars to the user!
    while ($r = $db->fetch_row($q)) {
        echo "<tr>
				<td>
					<a href='../guilds.php?action=view&id={$r['gw_declarer']}'>{$api->GuildFetchInfo($r['gw_declarer'],'guild_name')}</a><br />
						(Points: " . number_format($r['gw_drpoints']) . ")
				</td>
				<td>
					VS
				</td>
				<td>
					<a href='../guilds.php?action=view&id={$r['gw_declaree']}'>{$api->GuildFetchInfo($r['gw_declaree'],'guild_name')}</a><br />
						(Points: " . number_format($r['gw_depoints']) . ")
				</td>
				<td>
			        <a href='?action=endwar&war={$r['gw_id']}&csrf={$csrf}' class='btn btn-primary'>End War</a>
				</td>
			</tr>";
    }
    //Forget the wars query.
    $db->free_result($q);
    //Log that the wars were viewed.
    $api->game->addLog($userid, 'staff', "Viewed active guild wars.");
    echo "</table>";
    $h->endpage();
}

function endwar()
{
    global $db, $userid, $api, $h;
    //Sanitize the war to be deleted.
    $_GET['war'] = (isset($_GET['war']) && is_numeric($_GET['war'])) ? abs(intval($_GET['war'])) : 0;
    //Verify the CSRF
    if (!isset($_GET['csrf']) || !checkCSRF('staff_guild_end_war', stripslashes($_GET['csrf']))) {
        alert('danger', "Action Blocked!", "Forms expire fairly quickly. Go back and submit it quicker!");
        die($h->endpage());
    }
    //Select the war to be deleted from the database.
    $q = $db->query("SELECT * FROM `guild_wars`
                    WHERE `gw_winner` = 0 AND
                    `gw_end` > " . time() . "
                    AND `gw_id` = {$_GET['war']}
                    ORDER BY `gw_id` DESC");

    if ($db->num_rows($q) == 0) {
        alert('danger', "Uh Oh!", "The war you are trying to delete does not exist.", false);
        viewwars();
        die();
    }
    //Associate query to a result
    $r = $db->fetch_row($q);
    $db->free_result($q);
    //Delete war from database.
    $db->query("DELETE FROM `guild_wars` WHERE `gw_id` = {$_GET['war']}");

    //Associate the guild names to a variable for ease of use.
    $gang1 = "<a href='../guilds.php?action=view&id={$r['gw_declarer']}'>{$api->GuildFetchInfo($r['gw_declarer'],'guild_name')}</a>";
    $gang2 = "<a href='../guilds.php?action=view&id={$r['gw_declaree']}'>{$api->GuildFetchInfo($r['gw_declaree'],'guild_name')}</a>";
    $log = "Ended the war between {$gang1} and {$gang2}.";

    //Log the war being deleted, then tell the user that it was successful.
    $api->game->addLog($userid, 'staff', $log);
    alert('success', "Success!", "You have ended the war between {$gang1} and {$gang2}!", false);
    viewwars();
}

function editguild()
{
    global $db, $userid, $api, $h, $_CONFIG;
    //Set the first step so it goes to the correct page.
    if (!isset($_POST['step'])) {
        $_POST['step'] = 0;
    }
    //Selecting the guild to edit.
    if ($_POST['step'] == 0) {
        $csrf = getHtmlCSRF('staff_editguild_1');
        echo "Please select the guild you wish to edit from the dropdown below.<br />
        <form method='post'>
            <input type='hidden' value='1' name='step'>
            " . dropdownGuild() . "<br />
            {$csrf}
            <input type='submit' value='Edit Guild' class='btn btn-primary'>
        </form>";

    } elseif ($_POST['step'] == 1) {
        //Make sure input is safe.
        $guild = (isset($_POST['guild']) && is_numeric($_POST['guild'])) ? abs(intval($_POST['guild'])) : 0;

        //Validate CSRF check.
        if (!isset($_POST['verf']) || !checkCSRF('staff_editguild_1', stripslashes($_POST['verf']))) {
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

        //Show the information grabbed.

        //Armory select thing.
        $armory = ($r['guild_hasarmory'] == 'true') ?
            "<option value='true'>true</option><option value='false'>false</option>" :
            "<option value='false'>false</option><option value='true'>true</option>";
        echo "<h3>Editing Guild ID {$guild}</h3>";

        //CSRF request
        $csrf = getHtmlCSRF('staff_editguild_2');

        //Load the editing form
        echo "<table class='table table-bordered'><form method='post'>
        <input type='hidden' value='2' name='step'>
        <input type='hidden' value='{$guild}' name='guild'>
        <tr>
            <th>
                Guild Name
            </th>
            <td>
                <input type='text' name='name' class='form-control' value='{$r['guild_name']}'>
            </td>
        </tr>
        <tr>
            <th>
                Guild Description
            </th>
            <td>
                <textarea name='desc' class='form-control'>{$r['guild_desc']}</textarea>
            </td>
        </tr>
        <tr>
            <th>
                Guild Announcement
            </th>
            <td>
                <textarea name='announcement' class='form-control'>{$r['guild_announcement']}</textarea>
            </td>
        </tr>

        <tr>
            <th>
                Guild Owner
            </th>
            <td>
                " . dropdownGuildUser('owner', $guild, $r['guild_owner']) . "
            </td>
        </tr>
        <tr>
            <th>
                Guild Co-Owner
            </th>
            <td>
                " . dropdownGuildUser('coowner', $guild, $r['guild_coowner']) . "
            </td>
        </tr>
        <tr>
            <th>
                {$_CONFIG['primary_currency']}
            </th>
            <td>
                <input type='number' min='0' name='primary' class='form-control' value='{$r['guild_primcurr']}'>
            </td>
        </tr>
        <tr>
            <th>
                {$_CONFIG['secondary_currency']}
            </th>
            <td>
                <input type='number' min='0' name='secondary' class='form-control' value='{$r['guild_seccurr']}'>
            </td>
        </tr>
        <tr>
            <th>
                Has Armory?
            </th>
            <td>
                <select name='armory' class='form-control' type='dropdown'>
                    {$armory}
                </select>
            </td>
        </tr>
        <tr>
            <th>
                Guild Level
            </th>
            <td>
                <input type='number' min='0' name='level' class='form-control' value='{$r['guild_level']}'>
            </td>
        </tr>
        <tr>
            <th>
                Guild Experience
            </th>
            <td>
                <input type='number' min='0' name='xp' class='form-control' value='{$r['guild_xp']}'>
            </td>
        </tr>
        <tr>
            {$csrf}
            <td colspan='2'>
                <input type='submit' value='Edit Guild' class='btn btn-primary'>
            </td>
        </tr>
        </table>";
    } elseif ($_POST['step'] == 2) {
        //Make sure input is safe.
        $guild = (isset($_POST['guild']) && is_numeric($_POST['guild'])) ? abs(intval($_POST['guild'])) : 0;
        $xp = (isset($_POST['xp']) && is_numeric($_POST['xp'])) ? abs(intval($_POST['xp'])) : 0;
        $lvl = (isset($_POST['level']) && is_numeric($_POST['level'])) ? abs(intval($_POST['level'])) : 0;
        $primary = (isset($_POST['primary']) && is_numeric($_POST['primary'])) ? abs(intval($_POST['primary'])) : 0;
        $secondary = (isset($_POST['secondary']) && is_numeric($_POST['secondary'])) ? abs(intval($_POST['secondary'])) : 0;
        $owner = (isset($_POST['owner']) && is_numeric($_POST['owner'])) ? abs(intval($_POST['owner'])) : 0;
        $coowner = (isset($_POST['coowner']) && is_numeric($_POST['coowner'])) ? abs(intval($_POST['coowner'])) : 0;
        $name = $db->escape(htmlentities(stripslashes($_POST['name']), ENT_QUOTES, 'ISO-8859-1'));
        $desc = $db->escape(htmlentities(stripslashes($_POST['desc']), ENT_QUOTES, 'ISO-8859-1'));
        $announcement = $db->escape(htmlentities(stripslashes($_POST['announcement']), ENT_QUOTES, 'ISO-8859-1'));
        $armory = $_POST['armory'];

        //Validate CSRF check.
        if (!isset($_POST['verf']) || !checkCSRF('staff_editguild_2', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Go back and submit it quicker!");
            die($h->endpage());
        }

        //Check the guild ID is still set... else we can't change this guild
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

        //Check that the owner is in the guild
        $oc = $db->query("SELECT `username` FROM `users` WHERE `userid` = {$owner} AND `guild` = {$guild}");
        if ($db->num_rows($oc) == 0) {
            alert('danger', "Uh Oh!", "You are trying to set an invalid owner for this guild.");
            die($h->endpage());
        }

        //Check that the co-owner is in the guild
        $oc = $db->query("SELECT `username` FROM `users` WHERE `userid` = {$coowner} AND `guild` = {$guild}");
        if ($db->num_rows($oc) == 0) {
            alert('danger', "Uh Oh!", "You are trying to set an invalid co-owner for this guild.");
            die($h->endpage());
        }

        //Check for valid input on armory
        if ($armory != 'false' && $armory != 'true') {
            alert('danger', "Uh Oh!", "A guild can either have or not have an armory.");
            die($h->endpage());
        }

        //Update the guild
        $db->query("UPDATE `guild`
                    SET `guild_name` = '{$name}', `guild_desc` = '{$desc}', `guild_announcement` = '{$announcement}',
                    `guild_owner` = {$owner}, `guild_coowner` = {$coowner}, `guild_primcurr` = {$primary},
                    `guild_seccurr` = {$secondary}, `guild_level` = {$lvl}, `guild_xp` = {$xp},
                    `guild_hasarmory` = '{$armory}'
                    WHERE `guild_id` = {$guild}");
        alert('success', 'Success!', "You have successfully edited the {$name} guild!", true, 'index.php');
        $api->game->addLog($userid, 'staff', "Edited the <a href='../guilds.php?action=view&id={$guild}'>{$name}</a> Guild.");
    }
    $h->endpage();
}

function delguild()
{
    global $db, $userid, $api, $h;
    if (isset($_POST['guild'])) {
        //Make sure input is safe.
        $guild = (isset($_POST['guild']) && is_numeric($_POST['guild'])) ? abs(intval($_POST['guild'])) : 0;

        //Validate CSRF check.
        if (!isset($_POST['verf']) || !checkCSRF('staff_delete_guild', stripslashes($_POST['verf']))) {
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

        //Delete all the things.
        $db->query("DELETE FROM `guild` WHERE `guild_id` = {$guild}");
        $db->query("DELETE FROM `guild_applications` WHERE `ga_guild` = {$guild}");
        $db->query("DELETE FROM `guild_armory` WHERE `gaGUILD` = {$guild}");
        $db->query("DELETE FROM `guild_wars` WHERE `gw_declarer` = {$guild}");
        $db->query("DELETE FROM `guild_wars` WHERE `gw_declaree` = {$guild}");
        $db->query("DELETE FROM `guild_notifications` WHERE `gn_id` = {$guild}");
        $db->query("DELETE FROM `guild_crime_log` WHERE `gclGUILD` = {$guild}");
        $db->query("UPDATE `users` SET `guild` = 0 WHERE `guild` = {$guild}");

        //Alert user and log!
        alert('success', "Success!", "You have successfully deleted Guild ID {$guild}.");
        $api->game->addLog($userid, 'staff', "Deleted Guild ID {$guild}.");
        $h->endpage();
    } else {
        $csrf = getHtmlCSRF('staff_delete_guild');
        echo "<form method='post'>
        Please select the guild you wish to delete. This will delete EVERYTHING and cannot be reversed.<br />
        {$csrf}
        " . dropdownGuild() . "<br />
        <input type='submit' value='Delete Guild' class='btn btn-primary'>
        </form>";
        $h->endpage();
    }
}

function addcrime()
{
    global $db, $userid, $api, $h;
    if (isset($_POST['name'])) {
        //Make the POST variables safe to work with.
        $name = $db->escape(htmlentities(stripslashes($_POST['name']), ENT_QUOTES, 'ISO-8859-1'));
        $memb = (isset($_POST['members']) && is_numeric($_POST['members'])) ? abs(intval($_POST['members'])) : 0;
        $min = (isset($_POST['min']) && is_numeric($_POST['min'])) ? abs(intval($_POST['min'])) : 0;
        $max = (isset($_POST['max']) && is_numeric($_POST['max'])) ? abs(intval($_POST['max'])) : 0;
        $start = $db->escape(htmlentities(stripslashes($_POST['start']), ENT_QUOTES, 'ISO-8859-1'));
        $success = $db->escape(htmlentities(stripslashes($_POST['success']), ENT_QUOTES, 'ISO-8859-1'));
        $fail = $db->escape(htmlentities(stripslashes($_POST['fail']), ENT_QUOTES, 'ISO-8859-1'));

        //Validate CSRF check.
        if (!isset($_POST['verf']) || !checkCSRF('staff_create_guild_crime', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Go back and submit it quicker!");
            die($h->endpage());
        }

        //Check to see if the crime's name is in use already.
        $q = $db->query("SELECT `gcID` FROM `guild_crimes` WHERE `gcNAME` = '{$name}'");
        if ($db->num_rows($q) > 0) {
            alert('danger', "Uh Oh!", "You cannot have more than one crime with the same name.");
            die($h->endpage());
        }

        //Make sure member activity count is at least one.
        if ($memb == 0) {
            alert('danger', "Uh Oh!", "All guild crimes must required, at minimum 1 member to be active.");
            die($h->endpage());
        }

        //Check that the minimum number is less tha nthe maximum number.
        if ($min >= $max) {
            alert('danger', "Uh Oh!", "The minimum cash gained cannot be greater than, or equal to, the maximum cash gained.");
            die($h->endpage());
        }
        //Completed checks. Lets add to the database.
        $db->query("INSERT INTO `guild_crimes`
                    (`gcNAME`, `gcUSERS`, `gcSTART`, `gcSUCC`, `gcFAIL`, `gcMINCASH`, `gcMAXCASH`)
                    VALUES ('{$name}', '{$memb}', '{$start}', '{$success}', '{$fail}', '{$min}', '{$max}')");
        $api->game->addLog($userid, 'staff', "Created the {$name} Guild Crime.");
        alert('success', "Success!", "You have successfully created the {$name} Guild Crime.", true, 'index.php');
        $h->endpage();
    } else {
        $csrf = getHtmlCSRF('staff_create_guild_crime');
        echo "<form method='post'>
        Fill out this form completely to add a new guild crime.
        <table class='table table-bordered'>
            <tr>
                <th>
                    Crime Name
                </th>
                <td>
                    <input type='text' class='form-control' name='name' required='1'>
                </td>
            </tr>
            <tr>
                <th>
                    Minimum Members
                </th>
                <td>
                    <input type='number' class='form-control' name='members' min='1' required='1'>
                </td>
            </tr>
            <tr>
                <th>
                    Minimum Cash
                </th>
                <td>
                    <input type='number' class='form-control' name='min' min='0' required='1'>
                </td>
            </tr>
            <tr>
                <th>
                    Maximum Cash
                </th>
                <td>
                    <input type='number' class='form-control' name='max' min='0' required='1'>
                </td>
            </tr>
            <tr>
                <th>
                    Crime Start Text
                </th>
                <td>
                    <textarea class='form-control' name='start' required='1'></textarea>
                </td>
            </tr>
            <tr>
                <th>
                    Crime Success Text
                </th>
                <td>
                    <textarea class='form-control' name='success' required='1'></textarea>
                </td>
            </tr>
            <tr>
                <th>
                    Crime Fail Text
                </th>
                <td>
                    <textarea class='form-control' name='fail' required='1'></textarea>
                </td>
            </tr>
            <tr>
                <td colspan='2'>
                    <input type='submit' value='Create Guild Crime' class='btn btn-primary'>
                </td>
            </tr>
        </table>
        {$csrf}
        </form>";
        $h->endpage();
    }
}

function delcrime()
{
    global $db, $userid, $api, $h;
    if (isset($_POST['crime'])) {
        //Make the POST safe to work with.
        $_POST['crime'] = (isset($_POST['crime']) && is_numeric($_POST['crime'])) ? abs(intval($_POST['crime'])) : 0;

        //Verify CSRF check is successful.
        if (!isset($_POST['verf']) || !checkCSRF("staff_delete_guild_crime", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }

        //Verify crime exists.
        $cq = $db->query("SELECT `gcUSERS` from `guild_crimes` WHERE `gcID` = {$_POST['crime']}");
        if ($db->num_rows($cq) == 0) {
            alert('danger', "Uh Oh!", "You cannot commit a non-existent crime.");
            die($h->endpage());
        }

        //Update guilds to no crime/time if they're committing this crime.
        $db->query("UPDATE `guild`
                    SET `guild_crime` = 0,
                    `guild_crime_done` = 0
                    WHERE `guild_crime` = {$_POST['crime']}");

        //Delete the crime now.
        $db->query("DELETE FROM `guild_crimes` WHERE `gcID` = {$_POST['crime']}");
        $api->game->addLog($userid, 'staff', "Delete Guild Crime ID {$_POST['crime']}.");
        alert('success', 'Success!', "You have successfully deleted this guild crime.", true, 'index.php');
        $h->endpage();
    } else {
        //Select the crimes from database, based on how many members the guild has.
        $csrf = getHtmlCSRF('staff_delete_guild_crime');
        $q = $db->query("SELECT * FROM `guild_crimes`");
        echo "Select the guild crime you wish to delete. Guilds currently planning to commit this crime will have their crime
        set back to nothing.<br />
        <form method='post'>
            <select name='crime' type='dropdown' class='form-control'>";
        //Put the crimes in a dropdown list.
        while ($r = $db->fetch_row($q)) {
            echo "<option value='{$r['gcID']}'>{$r['gcNAME']}</option>\n";
        }
        echo "</select>
            {$csrf}
            <input type='submit' value='Delete Crime' class='btn btn-primary'>
        </form>";
        $h->endpage();
    }
}