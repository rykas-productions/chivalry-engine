<?php
/*
	File:		guilds.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Displays the in-game guilds, and allows the user to 
				select a guild to view, and potentially, join.
	Author:		TheMasterGeneral
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
require("globals.php");
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'create':
        create();
        break;
    case 'view':
        view();
        break;
    case 'apply':
        apply();
        break;
    case 'memberlist':
        memberlist();
        break;
    case 'wars':
        wars();
        break;
    default:
        menu();
        break;
}
function menu()
{
    global $db;
    echo "<h3>Guild Listing</h3>
	<a href='?action=create'>Create a Guild</a><hr />";
    echo "<div class='container'>
<div class='row'>
		<div class='col-sm'>
		    <h4>Guild Info</h4>
		</div>
		<div class='col-sm'>
		    <h4>Members</h4>
		</div>
</div><hr />";
    $gq = $db->query(
        "SELECT `guild_id`, `guild_owner`, `guild_name`,
			`userid`, `username`, `guild_level`, `guild_capacity`
			FROM `guild` AS `g`
			LEFT JOIN `users` AS `u` ON `g`.`guild_owner` = `u`.`userid`
			ORDER BY `g`.`guild_id` ASC");
    //List all the in-game guilds.
    while ($gd = $db->fetch_row($gq)) {
        echo "
		<div class='row'>
			<div class='col-sm'>
				<a href='?action=view&id={$gd['guild_id']}'>{$gd['guild_name']}</a><br />
				Level: {$gd['guild_level']}<br />
				Leader: <a href='profile.php?user={$gd['userid']}'>{$gd['username']}</a>
			</div>
			<div class='col-sm'>";
        $cnt = number_format($db->fetch_single
        ($db->query("SELECT COUNT(`userid`)
										FROM `users` 
										WHERE `guild` = {$gd['guild_id']}")));
        echo "{$cnt} / {$gd['guild_capacity']}";
        echo "</div>
		</div><hr />";
    }
    echo "</div>";

}

function create()
{
    global $db, $userid, $api, $ir, $set, $h;
    echo "<h3>Create a Guild</h3><hr />";
    $cg_price = $set['GUILD_PRICE'];
    $cg_level = $set['GUILD_LEVEL'];
    //User does not have the minimum required primary currency.
    if (!($api->user->hasCurrency($userid, 'primary', $cg_price))) {
        alert("danger", "Uh Oh!", "You do not have enough cash to create a guild! You need
		    " . number_format($cg_price) . ".", true, 'index.php');
        die($h->endpage());
    } //User level is too low to create a guild.
    elseif (($api->user->getInfo($userid, 'level', false)) < $cg_level) {
        alert("danger", "Uh Oh!", "You are too low of a level to create a guild. Please level up to Level
		    " . number_format($cg_level) . " and try again..", true, 'index.php');
        die($h->endpage());
    } //User is already in a guild.
    elseif ($ir['guild']) {
        alert("danger", "Uh Oh!", "You are already in a guild. You cannot create a guild while you are in one.", true, 'back');
        die($h->endpage());
    } else {
        if (isset($_POST['name'])) {
            //User fails the CSRF verification.
            if (!isset($_POST['verf']) || !checkCSRF('createguild', stripslashes($_POST['verf']))) {
                alert('danger', "CSRF Error!", "The action you were trying to do was blocked. It was blocked because you
				    loaded another page on the game. If you have not loaded a different page during this time, change
				    your password immediately, as another person may have access to your account!");
                die($h->endpage());
            }
            $name = $db->escape(htmlentities(stripslashes($_POST['name']), ENT_QUOTES, 'ISO-8859-1'));
            $desc = $db->escape(htmlentities(stripslashes($_POST['desc']), ENT_QUOTES, 'ISO-8859-1'));
            //Guild name is already in use.
            if ($db->num_rows($db->query("SELECT `guild_id` FROM `guild` WHERE `guild_name` = '{$name}'")) > 0) {
                alert("danger", "Uh Oh!", "A guild with the name you've chosen already exists!", true, 'back');
                die($h->endpage());
            }
            $db->query("INSERT INTO `guild`
						(`guild_owner`, `guild_coowner`, `guild_primcurr`, 
						`guild_seccurr`, `guild_hasarmory`, `guild_capacity`, `guild_name`, `guild_desc`, 
						`guild_level`, `guild_xp`) 
						VALUES ('{$userid}', '{$userid}', '0', '0', 'false', '5', 
						'{$name}', '{$desc}', '1', '0')");
            $i = $db->insert_id();
            //Take user's primary currency, and have player join the guild.
            $api->user->takeCurrency($userid, 'primary', $cg_price);
            $db->query("UPDATE `users` SET `guild` = {$i} WHERE `userid` = {$userid}");
            //Tell user they've created a guild.
            alert('success', "Success!", "You have successfully created a guild.", true, "viewguild.php");
            //Log the purchase, and that they've joined a guild.
            $api->game->addLog($userid, 'guilds', "Purchased a guild.");
            $api->game->addLog($userid, 'guilds', "Joined Guild ID {$i}");
        } else {
            //Request the CSRF form.
            $csrf = getHtmlCSRF('createguild');
            echo "<form action='?action=create' method='post'>";
            echo "
			<div class='container'>
				<div class='row'>
					<th colspan='2'>
						Creating a guild. Guilds cost " . number_format($cg_price) . " " . constant("primary_currency") . "
					</div>
				</div><hr />
				<div class='row'>
					<div class='col-sm'>
						Guild Name
					</div>
					<div class='col-sm'>
						<input type='text' required='1' class='form-control' name='name' />
					</div>
				</div><hr />
				<div class='row'>
					<div class='col-sm'>
						Guild Description
					</div>
					<div class='col-sm'>
						<textarea name='desc' required='1' class='form-control' cols='40' rows='7'></textarea>
					</div>
				</div><hr />
				<div class='row'>
					<td colspan='2'>
						<input type='submit' value='Create Guild for " . number_format($cg_price) . "!' class='btn btn-primary'>
					</div>
				</div><hr />
				{$csrf}
			</div>
			</form>";
        }
    }
}

function view()
{
    global $db, $h, $api;
    $guild_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    //Guild ID has not been entered, so redirect them to main guild listing.
    if (empty($_GET['id'])) {
        header("Location: guilds.php");
    } else {
        $gq = $db->query("SELECT * FROM `guild` WHERE `guild_id` = {$guild_id}");
        //Guild does not exist.
        if ($db->num_rows($gq) == 0) {
            alert('danger', "Uh Oh!", "The guild you are trying to view does not exist.", true, "guilds.php");
            die($h->endpage());
        }
        //List all the guild's information.
        $gd = $db->fetch_row($gq);
        echo "<h3>{$gd['guild_name']} Guild</h3>";
        echo "
		<div class='container'>
			<div class='row'>
				<div class='col-sm'>
					Guild Leader
				</div>
				<div class='col-sm'>
					<a href='profile.php?user={$gd['guild_owner']}'> " . $api->user->getNameFromID($gd['guild_owner']) . "</a>
				</div>
			</div><hr />
			<div class='row'>
				<div class='col-sm'>
					Guild Co-Leader
				</div>
				<div class='col-sm'>
					<a href='profile.php?user={$gd['guild_coowner']}'> " . $api->user->getNameFromID($gd['guild_coowner']) . "</a>
				</div>
			</div><hr />
			<div class='row'>
				<div class='col-sm'>
					Guild Level
				</div>
				<div class='col-sm'>
					" . number_format($gd['guild_level']) . "
				</div>
			</div><hr />
			<div class='row'>
				<div class='col-sm'>
					{$gd['guild_name']} Description
				</div>
				<div class='col-sm'>
					{$gd['guild_desc']}
				</div>
			</div><hr />
			<div class='row'>
				<div class='col-sm'>
					Members
				</div>
				<div class='col-sm'>";
        //Count players in this guild.
        $cnt = number_format($db->fetch_single
        ($db->query("SELECT COUNT(`userid`)
										FROM `users` 
										WHERE `guild` = {$_GET['id']}")));
        echo number_format($cnt) . " / " . number_format($gd['guild_capacity']) . "
				</div>
			</div><hr />
			<div class='row'>
				<div class='col-sm'>
					Guild Location
				</div>
				<div class='col-sm'>";
        echo $api->game->getTownNameFromID($gd['guild_town_id']) . "
				</div>
			</div><hr />
			<div class='row'>
				<div class='col-sm'>
					<a href='?action=memberlist&id={$_GET['id']}'>View Members</a>
				</div>
				<div class='col-sm'>
					<a href='?action=apply&id={$_GET['id']}'>Apply</a>
				</div>
			</div><hr />
		</div>";
    }
}

function memberlist()
{
    global $db, $h;
    $guild_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    //Guild is not specified.
    if (empty($guild_id )) {
        alert('danger', "Uh Oh!", "Please specify the guild you wish to view.", true, "guilds.php");
        die($h->endpage());
    }
    $gq = $db->query("SELECT * FROM `guild` WHERE `guild_id` = {$guild_id }");
    //Guild does not exist.
    if ($db->num_rows($gq) == 0) {
        alert('danger', "Uh Oh!", "You are trying to view a non-existent guild.", true, "guilds.php");
        die($h->endpage());
    }
    $gd = $db->fetch_row($gq);
    echo "<h3>Members Enlisted in the {$gd['guild_name']} guild</h3>
	<div class='container'>
		  	<div class='row'>
		  		<div class='col-sm'>
					<h4>User</h4>
				</div>
		  		<div class='col-sm'>
					<h4>Level</h4>
				</div>
		  	</div><hr />";
    $q = $db->query("SELECT `userid`, `username`, `level`
                     FROM `users`
                     WHERE `guild` = {$gd['guild_id']}
                     ORDER BY `level` DESC");
    //List players in the guild.
    while ($r = $db->fetch_row($q)) {
        echo "<div class='row'>
        		<div class='col-sm'>
					<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
				</div>
        		<div class='col-sm'>
					{$r['level']}
				</div>
			</div><hr />";
    }
    echo "</div>";
}

function apply()
{
    global $db, $userid, $ir, $api, $h;
    $guild_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    //Guild is not specified.
    if (empty($guild_id)) {
        alert('danger', "Uh Oh!", "Please specify the guild you wish to view.", true, "guilds.php");
        die($h->endpage());
    }
    $gq = $db->query("SELECT * FROM `guild` WHERE `guild_id` = {$guild_id}");
    //Guild does not exist.
    if ($db->num_rows($gq) == 0) {
        alert('danger', "Uh Oh!", "You are trying to apply to a non-existent guild.", true, "guilds.php");
        die($h->endpage());
    }
    $gd = $db->fetch_row($gq);
    //User is already in a guild, and cannot join another.
    if ($ir['guild'] > 0) {
        alert('danger', "Uh Oh!", "You cannot write applications if you're already in a guild.", true, "guilds.php?action=view&id={$_GET['id']}");
        die($h->endpage());
    }
    echo "<h3>Submitting an Application to join the {$gd['guild_name']} guild.</h3><hr />";
    if (isset($_POST['application'])) {
        //User fails CSRF verification
        if (!isset($_POST['verf']) || !checkCSRF('guild_apply', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "The action you were trying to do was blocked. It was blocked because you loaded
                another page on the game. If you have not loaded a different page during this time, change your password
                immediately, as another person may have access to your account!", true, 'back');
            die($h->endpage());
        }
        $cnt = $db->query("SELECT * FROM `guild_applications` WHERE `ga_user` = {$userid} && `ga_guild` = {$guild_id}");
        //User has already submitted an application to this guild.
        if ($db->num_rows($cnt) > 0) {
            alert('danger', "Uh Oh!", "You have already filled out an application to join this guild. Please wait until a
			    response is given.", true, 'back');
            die($h->endpage());
        }
        //Tell the guild's owner and co-owner that the user has sent an application.
        if ($gd['guild_owner'] == $gd['guild_coowner']) {
            $api->user->addNotification($gd['guild_owner'], "{$ir['username']} has filled and submitted an application to join your guild.");
        } else {
            $api->user->addNotification($gd['guild_owner'], "{$ir['username']} has filled and submitted an application to join your guild.");
            $api->user->addNotification($gd['guild_coowner'], "{$ir['username']} has filled and submitted an application to join your guild.");
        }
        $time = time();
        $application = (isset($_POST['application']) && is_string($_POST['application'])) ? $db->escape(htmlentities(stripslashes($_POST['application']), ENT_QUOTES, 'ISO-8859-1')) : '';
        //Insert the application, notify the guild and tell the user they were successful.
        $db->query("INSERT INTO `guild_applications` VALUES 
                    (NULL, {$userid}, {$_GET['id']}, 
                    {$time}, '{$application}')");
        $gev = $db->escape("<a href='profile.php?user={$userid}'>{$ir['username']}</a>
                                sent an application to join this guild.");
        $db->query("INSERT INTO `guild_notifications` VALUES (NULL, {$_GET['id']}, " . time() . ", '{$gev}')");
        alert('success', "Success!", "You application has been submitted successfully.", true, "guilds.php?action=view&id={$_GET['id']}");
    } else {
        //Request CSRF form.
        $csrf = getHtmlCSRF('guild_apply');
        echo "
		<form action='?action=apply&id={$_GET['id']}' method='post'>
			Write your application to join this guild here. The more information you can provide, the higher chance you
			are to be accepted.<br />
			<textarea name='application' class='form-control' required='1' rows='7' cols='40'></textarea><br />
			{$csrf}
			<input type='submit' class='btn btn-primary' value='Submit Application' />
		</form>";
    }
}

function wars()
{
    global $db, $api;
    $time = time();
    echo "<h3>Known Guild Wars</h3><hr />";
    $q = $db->query("SELECT * FROM `guild_wars`
                    WHERE `gw_winner` = 0 AND 
                    `gw_end` > {$time} 
                    ORDER BY `gw_id` DESC");
    //There is at least one active guild war.
    if ($db->num_rows($q) > 0) {
        echo "<div class='container'>";
        //List the active guild wars.
        while ($r = $db->fetch_row($q)) {
            echo "<div class='row'>
				<div class='col-sm'>
					<a href='guilds.php?action=view&id={$r['gw_declarer']}'>{$api->guild->fetchInfo($r['gw_declarer'],'guild_name')}</a><br />
						(Points: " . number_format($r['gw_drpoints']) . ")
				</div>
				<div class='col-sm'>
					VS
				</div>
				<div class='col-sm'>
					<a href='guilds.php?action=view&id={$r['gw_declaree']}'>{$api->guild->fetchInfo($r['gw_declaree'],'guild_name')}</a><br />
						(Points: " . number_format($r['gw_depoints']) . ")
				</div>
			</div><hr />";
        }
        echo "</div>";
    } //No guild wars.
    else {
        alert('danger', "Uh Oh!", "There are currently no guilds warring at this time.", false);
    }
}

$h->endpage();