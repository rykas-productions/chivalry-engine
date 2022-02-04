<?php
/*
	File:		guilds.php
	Created: 	4/5/2016 at 12:06AM Eastern Time
	Info: 		Lists all the in-game guilds, and allows a user to
				apply to a guild, or create their own.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
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
    global $db, $set;
    echo "<h3><i class='game-icon game-icon-dozen'></i> Guild Listing</h3>
	<a href='?action=create'>Create a Guild - " . shortNumberParse($set['GUILD_PRICE']) . " Copper</a><hr />";
    echo "<table class='table table-bordered table-striped'>
    <thead>
	<tr align='left'>
		<th width='15%'>Guild</th>
		<th>Level</th>
		<th>Members</th>
		<th>Leader</th>
        <th>Misc Info</th>
	</tr>
    </thead>
    <tbody>";
    $gq = $db->query(
        "/*qc=on*/SELECT `guild_id`, `guild_town_id`, `guild_owner`, `guild_name`, `guild_debt_time`, `guild_primcurr`,
			`userid`, `username`, `guild_level`, `guild_capacity`, `guild_pic`, `guild_hasarmory`, `guild_ba`
			FROM `guild` AS `g`
			LEFT JOIN `users` AS `u` ON `g`.`guild_owner` = `u`.`userid`
			ORDER BY `g`.`guild_id` ASC");
    //List all the in-game guilds.
    while ($gd = $db->fetch_row($gq)) {
		$gd['guild_capacity']=calculateGuildMemberCapacity($gd['guild_id']);
        $hasarmory = ($gd['guild_hasarmory'] == 'true') ? "<span class='text-success'>Armory</span>" : "<span class='text-danger'>No Armory</span>";
        $appacc = ($gd['guild_ba'] == 0) ? "<span class='text-success'>Accepting Applications</span>" : "<span class='text-danger'>Recruitment Closed.</span>";
        $indebt = ($gd['guild_primcurr'] > 0) ? "" : "<span class='text-danger'>In Debt</span>";
		$gd['guild_pic'] = ($gd['guild_pic']) ? "<img src='" . parseImage($gd['guild_pic']) . "' class='img-fluid' style='max-width: 75px;'>" : '';
        echo "
		<tr align='left'>
			<td>
				{$gd['guild_pic']}<br />
                <a href='?action=view&id={$gd['guild_id']}'>{$gd['guild_name']}</a>
			</td>
			<td>
				{$gd['guild_level']}
			</td>
			<td>";
        $cnt = number_format($db->fetch_single
        ($db->query("/*qc=on*/SELECT COUNT(`userid`)
										FROM `users` 
										WHERE `guild` = {$gd['guild_id']}")));
        echo "{$cnt} / {$gd['guild_capacity']}";
        $gd['username']=parseUsername($gd['userid']);
        echo "</td>
			<td>
				<a href='profile.php?user={$gd['userid']}'>{$gd['username']}</a>
			</td>
            <td>
                {$appacc}<br />
                {$hasarmory}<br />
                {$indebt}
            </td>
		</tr>";
    }
    echo "</tbody></table>";

}

function create()
{
    global $db, $userid, $api, $ir, $set, $h;
    $cg_price = $set['GUILD_PRICE'];
    $cg_level = $set['GUILD_LEVEL'];
    //User does not have the minimum required Copper Coins.
    if (!($api->UserHasCurrency($userid, 'primary', $cg_price))) {
        alert("danger", "Uh Oh!", "You do not have enough cash to create a guild! You need
		    " . number_format($cg_price) . " Copper Coins.", true, 'index.php');
        die($h->endpage());
    } //User level is too low to create a guild.
    elseif (($api->UserInfoGet($userid, 'level', false)) < $cg_level) {
        alert("danger", "Uh Oh!", "You are too low of a level to create a guild. Please level up to Level
		    " . number_format($cg_level) . " and try again.", true, 'index.php');
        die($h->endpage());
    } //User is already in a guild.
    elseif ($ir['guild']) {
        alert("danger", "Uh Oh!", "You are already in a guild. You cannot create a guild while you are in one.", true, 'back');
        die($h->endpage());
    } else {
        if (isset($_POST['name'])) {
            //User fails the CSRF verification.
            if (!isset($_POST['verf']) || !verify_csrf_code('createguild', stripslashes($_POST['verf']))) {
                alert('danger', "CSRF Error!", "The action you were trying to do was blocked. It was blocked because you
				    loaded another page on the game. If you have not loaded a different page during this time, change
				    your password immediately, as another person may have access to your account!");
                die($h->endpage());
            }
            $name = $db->escape(htmlentities(stripslashes($_POST['name']), ENT_QUOTES, 'ISO-8859-1'));
            $desc = $db->escape(htmlentities(stripslashes($_POST['desc']), ENT_QUOTES, 'ISO-8859-1'));
            //Guild name is already in use.
            if ($db->num_rows($db->query("/*qc=on*/SELECT `guild_id` FROM `guild` WHERE `guild_name` = '{$name}'")) > 0) 
            {
                alert("danger", "Uh Oh!", "A guild with the name you've chosen already exists!", true, 'back');
                die($h->endpage());
            }
            $i = createGuild($name, $desc, $userid);
            $api->UserTakeCurrency($userid, 'primary', $cg_price);
            
            //Tell user they've created a guild.
            alert('success', "Success!", "You have successfully created a guild for " . shortNumberParse($cg_price) . " Copper Coins.", true, "viewguild.php", "View Guild");
            //Log the purchase, and that they've joined a guild.
            $api->SystemLogsAdd($userid, 'guilds', "Purchased a guild.");
            $api->SystemLogsAdd($userid, 'guilds', "Joined Guild ID {$i}");
            $api->GuildAddNotification($i, "Your guild was created!");
            addToEconomyLog("Guild Upkeep", "copper", $cg_price * -1);
        } else {
            //Request the CSRF form.
            $csrf = request_csrf_html('createguild');
            echo "<form action='?action=create' method='post'>
                    <div class='row'>
                        <div class='col-12'>
                            <div class='card'>
                                <div class='card-header'>
                                    Creating Guild for " . shortNumberParse($cg_price) . " Copper Coins
                                </div>
                                <div class='card-body'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <div class='row'>
                                                <div class='col-12'>
                                                    <b><small>Guild Name</small></b>
                                                </div>
                                                <div class='col-12'>
                                                    <input type='text' required='1' class='form-control' name='name' />
                                                </div>
                                            </div>
                                        </div>
                                        <div class='col-12'>
                                            <div class='row'>
                                                <div class='col-12'>
                                                    <b><small>Guild Description</small></b>
                                                </div>
                                                <div class='col-12'>
                                                    <textarea name='desc' required='1' class='form-control'></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class='col-12'>
                                            <hr />
                                            <input type='submit' value='Create Guild' class='btn btn-primary btn-block'>
                                            {$csrf}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </form>";
        }
    }
}

function view()
{
    global $db, $h, $api, $ir;
    $_GET['id'] = abs($_GET['id']);
    //Guild ID has not been entered, so redirect them to main guild listing.
    if (empty($_GET['id'])) {
        header("Location: guilds.php");
    } else {
        $gq = $db->query("/*qc=on*/SELECT * FROM `guild` WHERE `guild_id` = {$_GET['id']}");
        //Guild does not exist.
        if ($db->num_rows($gq) == 0) {
            alert('danger', "Uh Oh!", "The guild you are trying to view does not exist.", true, "guilds.php");
            die($h->endpage());
        }
        //List all the guild's information.
        $gd = $db->fetch_row($gq);
        $maxMembers = calculateGuildMemberCapacity($gd['guild_id']);
        $memberCount = countGuildMembers($gd['guild_id']);
        $gymBonus = calculateGuildGymBonus($gd['guild_id']);
        $appDisabled = ($ir['guild'] != 0) ? "disabled" : "";
        $toQuery = $db->query("SELECT * FROM `town` WHERE `town_guild_owner` = {$gd['guild_id']}");
        if ($db->num_rows($toQuery) == 0)
            $townOwned = "N/A";
        else
        {
            $toRow = $db->fetch_row($toQuery);
            $townOwned = "<a href='travel.php?to={$toRow['town_id']}'>{$toRow['town_name']}</a>";
        }
        
        $hasArmory = ($gd['guild_hasarmory'] == 'true') ? "<span class='text-success'>Yes</span>" : "<span class='text-danger'>No</span>";
        $acceptApps = ($gd['guild_ba'] == 0) ? "<span class='text-success'>Open</span>" : "<span class='text-danger'>Closed</span>";
        $inDebt = ($gd['guild_primcurr'] > 0) ? "<span class='text-success'>Good</span>" : "<span class='text-danger'>Bad</span>";
        
        $allyQ=$db->query("/*qc=on*/SELECT *
							FROM `guild_alliances`
							WHERE (`alliance_a` = {$gd['guild_id']} OR `alliance_b` = {$gd['guild_id']})
							AND `alliance_true` = 1");
        echo "<h3>{$gd['guild_name']} Guild</h3>";
        echo "
        <div class='row'>
            <div class='col-12 col-xl-6 col-xxxl-3'>
                <div class='card'>
                    <div class='card-header'>
                        {$gd['guild_name']} Guild
                    </div>
                    <div class='card-body'>
                        <div class='row'>";
                        if (!empty($gd['guild_pic']))
                        {
                            echo "<div class='col-12'>
                                <img src='" . parseImage($gd['guild_pic']) . "' 
                                    placeholder='The {$gd['guild_name']} guild picture.' 
                                    title='The {$gd['guild_name']} guild picture.' class='img-fluid'><br />
                            </div>";
                        }
                        echo "<div class='col-12'>
                                {$gd['guild_desc']}
                            </div>
                            <div class='col-12 col-sm-6'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small>Leader</small>
                                    </div>
                                    <div class='col-12'>
                                        <a href='profile.php?user={$gd['guild_owner']}'> " . parseUsername($gd['guild_owner']) . "</a> [{$gd['guild_owner']}]
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small>Co-leader</small>
                                    </div>
                                    <div class='col-12'>
                                        <a href='profile.php?user={$gd['guild_coowner']}'> " . parseUsername($gd['guild_coowner']) . "</a> [{$gd['guild_coowner']}]
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br />
            </div>
            <div class='col-12 col-xl-6 col-xxxl-4'>
                <div class='card'>
                    <div class='card-header'>
                        {$gd['guild_name']} Information
                    </div>
                    <div class='card-body'>
                        <div class='row'>
                            <div class='col-12 col-sm-4'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small>Level</small>
                                    </div>
                                    <div class='col-12'>
                                        " . number_format($gd['guild_level']) . "
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-4'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small>Members</small>
                                    </div>
                                    <div class='col-12'>
                                        " . number_format($memberCount) . " / " . number_format($maxMembers) . "
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-4'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small>Town Owned</small>
                                    </div>
                                    <div class='col-12'>
                                        {$townOwned}
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-4'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small>Has Armory</small>
                                    </div>
                                    <div class='col-12'>
                                        {$hasArmory}
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-4'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small>Recruitment</small>
                                    </div>
                                    <div class='col-12'>
                                        {$acceptApps}
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-4'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small>Finances</small>
                                    </div>
                                    <div class='col-12'>
                                        {$inDebt}
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-4'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small>Gym Bonus</small>
                                    </div>
                                    <div class='col-12'>
                                        " . $gymBonus * 100 . "%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br />
            </div>";
            if ($db->num_rows($allyQ) > 0)
            {
                echo "
                <div class='col-12 col-xl-6 col-xxxl-3'>
                    <div class='card'>
                        <div class='card-header'>
                            {$gd['guild_name']} Alliances
                        </div>
                        <div class='card-body'>
                            <div class='row'>";
                                while ($r=$db->fetch_row($allyQ))
                                {
                                    $type = ($r['alliance_type'] == 1) ? "Alliance" : "Non-aggressive";
                                    if ($r['alliance_a'] == $_GET['id'])
                                        $otheralliance=$r['alliance_b'];
                                    else
                                        $otheralliance=$r['alliance_a'];
                                    echo "
                                    <div class='col-12 col-sm-6'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <small>{$type}</small>
                                            </div>
                                            <div class='col-12'>
                                                <a href='?action=view&id={$otheralliance}'>{$api->GuildFetchInfo($otheralliance,'guild_name')}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-12 col-sm-6'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <small>Guild Members</small>
                                            </div>
                                            <div class='col-12'>
                                                " . number_format(countGuildMembers($otheralliance)) . "
                                            </div>
                                        </div>
                                    </div>
                                    <hr />";
                                }
                                echo "
                            </div>
                        </div>
                    </div>
                    <br />
                </div>";
            }
        echo"<div class='col-12 col-xl-6 col-xxxl-2'>
                <div class='card'>
                    <div class='card-header'>
                        Actions
                    </div>
                    <div class='card-body'>
                        <div class='row'>
                            <div class='col-12 col-sm-6 col-xxxl-12'>
                                <a href='?action=memberlist&id={$gd['guild_id']}' class='btn btn-primary btn-block'>View Members</a><br />
                            </div>
                            <div class='col-12 col-sm-6 col-xxxl-12'>
                                <a href='?action=apply&id={$gd['guild_id']}' class='btn btn-success btn-block {$appDisabled}'>Send Application</a><br />
                            </div>
                        </div>
                    </div>
                </div>
                <br />
            </div>
        </div>";
    }
}

function memberlist()
{
    global $db, $h;
    $_GET['id'] = abs($_GET['id']);
    //Guild is not specified.
    if (empty($_GET['id'])) {
        alert('danger', "Uh Oh!", "Please specify the guild you wish to view.", true, "guilds.php");
        die($h->endpage());
    }
    $gq = $db->query("/*qc=on*/SELECT * FROM `guild` WHERE `guild_id` = {$_GET['id']}");
    //Guild does not exist.
    if ($db->num_rows($gq) == 0) {
        alert('danger', "Uh Oh!", "You are trying to view a non-existent guild.", true, "guilds.php");
        die($h->endpage());
    }
    $gd = $db->fetch_row($gq);
    echo "<h3>Members Enlisted in the {$gd['guild_name']} guild</h3>
	<table class='table table-bordered'>
		  	<tr>
		  		<th>
					User
				</th>
		  		<th>
					Level
				</th>
		  	</tr>";
    $q = $db->query("/*qc=on*/SELECT `userid`, `username`, `level`
                     FROM `users`
                     WHERE `guild` = {$gd['guild_id']}
                     ORDER BY `level` DESC");
    //List players in the guild.
    while ($r = $db->fetch_row($q)) {
        echo "<tr>
        		<td>
					<a href='profile.php?user={$r['userid']}'>" . parseUsername($r['userid']) . "</a>
				</td>
        		<td>
					{$r['level']}
				</td>
			</tr>";
    }
    echo "</table>";
}

function apply()
{
    global $db, $userid, $ir, $api, $h;
    $_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
    //Guild is not specified.
    if (empty($_GET['id'])) {
        alert('danger', "Uh Oh!", "Please specify the guild you wish to view.", true, "guilds.php");
        die($h->endpage());
    }
    $gq = $db->query("/*qc=on*/SELECT * FROM `guild` WHERE `guild_id` = {$_GET['id']}");
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
	if ($gd['guild_ba'] == 1)
	{
		alert('danger', "Uh Oh!", "This guild is currently blocking all applications.", true, "guilds.php?action=view&id={$_GET['id']}");
        die($h->endpage());
	}
    echo "<h3>Submitting an Application to join the {$gd['guild_name']} guild.</h3><hr />";
    if (isset($_POST['application'])) {
        //User fails CSRF verification
        if (!isset($_POST['verf']) || !verify_csrf_code('guild_apply', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "The action you were trying to do was blocked. It was blocked because you loaded
                another page on the game. If you have not loaded a different page during this time, change your password
                immediately, as another person may have access to your account!", true, 'back');
            die($h->endpage());
        }
        $cnt = $db->query("/*qc=on*/SELECT * FROM `guild_applications` WHERE `ga_user` = {$userid} && `ga_guild` = {$_GET['id']}");
        //User has already submitted an application to this guild.
        if ($db->num_rows($cnt) > 0) {
            alert('danger', "Uh Oh!", "You have already filled out an application to join this guild. Please wait until a
			    response is given.", true, 'back');
            die($h->endpage());
        }
        //Tell the guild's owner and co-owner that the user has sent an application.
        guildSendStaffNotif($_GET['id'], "{$ir['username']} has filled out an app to join your guild.");
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
        $csrf = request_csrf_html('guild_apply');
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
    echo "<h3><i class='game-icon game-icon-mounted-knight'></i> Guild Wars</h3><hr />";
    $q = $db->query("/*qc=on*/SELECT * FROM `guild_wars`
                    WHERE `gw_winner` = 0 AND 
                    `gw_end` > {$time} 
                    ORDER BY `gw_id` DESC");
    //There is at least one active guild war.
    if ($db->num_rows($q) > 0) {
        echo "<table class='table table-bordered'>";
        //List the active guild wars.
        while ($r = $db->fetch_row($q)) {
            echo "<tr>
				<td>
					<a href='guilds.php?action=view&id={$r['gw_declarer']}'>{$api->GuildFetchInfo($r['gw_declarer'],'guild_name')}</a><br />
						(Points: " . number_format($r['gw_drpoints']) . ")
				</td>
				<td>
					VS
				</td>
				<td>
					<a href='guilds.php?action=view&id={$r['gw_declaree']}'>{$api->GuildFetchInfo($r['gw_declaree'],'guild_name')}</a><br />
						(Points: " . number_format($r['gw_depoints']) . ")
				</td>
			</tr>";
        }
        echo "</table>";
    } //No guild wars.
    else {
        alert('danger', "Uh Oh!", "There are currently no guilds warring at this time.", false);
    }
}

$h->endpage();