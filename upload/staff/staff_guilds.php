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
    global $db, $userid, $api, $h, $set;
    if (isset($_POST['guild'])) {
        //Make sure input is safe.
        $guild = (isset($_POST['guild']) && is_numeric($_POST['guild'])) ? abs(intval($_POST['guild'])) : 0;

        //Validate CSRF check.
        /*if (!isset($_POST['verf']) || !verify_csrf_code('staff_viewguild', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Go back and submit it quicker!");
            die($h->endpage());
        }*/

        //Make sure guild is still valid input.
        if (empty($guild)) {
            alert('danger', "Uh Oh!", "You are trying to view an invalid or unspecified guild.");
            die($h->endpage());
        }

        //Select the Guild from database to ensure it exists.
        $q = $db->query("/*qc=on*/SELECT * FROM `guild` WHERE `guild_id` = {$guild}");
        if ($db->num_rows($q) == 0) {
            alert('danger', "Uh Oh!", "The guild you are trying to view does not exist, or is invalid.");
            die($h->endpage());
        }

        //Assign grabbed information to a variable
        $r = $db->fetch_row($q);
        $wq = $db->query("/*qc=on*/SELECT COUNT(`gw_id`) FROM `guild_wars` WHERE (`gw_declarer` = {$guild} OR `gw_declaree` = {$guild}) AND `gw_winner` = 0");
        //Select member count from database.
        $membcount = countGuildMembers($guild);
        
        $gdi = ($db->fetch_row($db->query("SELECT * FROM `guild_district_info` WHERE `guild_id` = {$guild}")));
        
        $armory = ($r['guild_hasarmory']) ? "<span class='text-success'>Purchased</span>" : "<span class='text-danger'>Unpurchased</span>";
        $recruit = ($r['guild_ba'] == 0) ? "<span class='text-success'>Recruiting</span>" : "<span class='text-danger'>Not recruiting</span>";
        $debt = ($r['guild_primcurr'] > 0) ? "<span class='text-success'>No debt</span>" : "<span class='text-danger'>In debt</span>" ;
        $wars = ($db->fetch_single($wq) == 0) ? "<span class='text-success'>No active wars</span>" : "<span class='text-danger'> " . number_format($db->fetch_single($wq)) . " active wars</span>";
        $gymBonus = calculateGuildGymBonus($guild);
        $gymBonusTime = ($r['guild_bonus_time'] > time()) ? "<span class='text-success'>" . TimeUntil_Parse($r['guild_bonus_time']) . " remain</span>" : "<span class='text-danger'>Not active</span>";
        
        $districtsOwned = countOwnedDistricts($guild);
        $warriorsDeployed = countDeployedWarriors($guild);
        $archersDeployed = countDeployedArchers($guild);
        $generalsDeployed = countDeployedGenerals($guild);
        
        $guildPic = (empty($r['guild_pic'])) ? "<i>No guild pic</i>" : "<img src='" . parseImage($r['guild_pic']) . "' placeholder='The {$r['guild_name']} guild picture.' width='300' class='img-fluid' title='The {$r['guild_name']} guild picture.'>";

        $allyQ = $db->query("/*qc=on*/SELECT * FROM `guild_alliances`
							WHERE (`alliance_a` = {$guild} OR `alliance_b` = {$guild})
							AND `alliance_true` = 1");
        
        //Show the information grabbed.
        echo "<div class='row'>
            <div class='col-12 col-lg-6 col-xxl-4'>
                <div class='card'>
                    <div class='card-header'>
                        Basic Info for Guild ID {$guild}
                    </div>
                    <div class='card-body'>
                        <div class='row'>
                            <div class='col-12 col-md-4 col-lg-6'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Guild Pic</b></small>
                                    </div>
                                    <div class='col-12'>
                                        {$guildPic}
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-md-8 col-lg-4'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Guild Name</b></small>
                                    </div>
                                    <div class='col-12'>
                                        {$r['guild_name']}
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-md-12'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Guild Description</b></small>
                                    </div>
                                    <div class='col-12'>
                                        {$r['guild_desc']}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br />
            </div>
            <div class='col-12 col-lg-6 col-xxl-4'>
                <div class='card'>
                    <div class='card-header'>
                        Staff for {$r['guild_name']}
                    </div>
                    <div class='card-body'>
                        <div class='row'>
                            <div class='col-12 col-sm-6 col-md-4'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Guild Leader</b></small>
                                    </div>
                                    <div class='col-12'>
                                        <a href='../profile.php?user={$r['guild_owner']}'>{$api->SystemUserIDtoName($r['guild_owner'])}</a> [{$r['guild_owner']}]
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-md-4'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Guild Co-Leader</b></small>
                                    </div>
                                    <div class='col-12'>
                                         <a href='../profile.php?user={$r['guild_coowner']}'>{$api->SystemUserIDtoName($r['guild_coowner'])}</a> [{$r['guild_coowner']}]
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-md-4'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Guild App Manager</b></small>
                                    </div>
                                    <div class='col-12'>
                                        <a href='../profile.php?user={$r['guild_app_manager']}'>{$api->SystemUserIDtoName($r['guild_app_manager'])}</a> [{$r['guild_app_manager']}]
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-md-4'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Guild Vault Manager</b></small>
                                    </div>
                                    <div class='col-12'>
                                        <a href='../profile.php?user={$r['guild_vault_manager']}'>{$api->SystemUserIDtoName($r['guild_vault_manager'])}</a> [{$r['guild_vault_manager']}]
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-md-4'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Guild Crime Lord</b></small>
                                    </div>
                                    <div class='col-12'>
                                        <a href='../profile.php?user={$r['guild_crime_lord']}'>{$api->SystemUserIDtoName($r['guild_crime_lord'])}</a> [{$r['guild_crime_lord']}]
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br />
            </div>
            <div class='col-12 col-lg-6 col-xxl-4'>
                <div class='card'>
                    <div class='card-header'>
                        Other Info for {$r['guild_name']}
                    </div>
                    <div class='card-body'>
                        <div class='row'>
                            <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Guild Copper Coins</b></small>
                                    </div>
                                    <div class='col-12'>
                                        " . shortNumberParse($r['guild_primcurr']) . " / " . shortNumberParse(calculateMaxGuildVaultCopper($r['guild_level'])) . "
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Guild Chivalry Tokens</b></small>
                                    </div>
                                    <div class='col-12'>
                                         " . shortNumberParse($r['guild_seccurr']) . " / " . shortNumberParse(calculateMaxGuildVaultTokens($r['guild_level'])) . "
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Guild Members (Capacity)</b></small>
                                    </div>
                                    <div class='col-12'>
                                        " . number_format($membcount) . " (" . number_format(calculateGuildMemberCapacity($guild)) . ")
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Guild Experience (Level)</b></small>
                                    </div>
                                    <div class='col-12'>
                                        " . shortNumberParse($r['guild_xp']) . " (" . number_format($r['guild_level']) . ")
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Guild Has Armory?</b></small>
                                    </div>
                                    <div class='col-12'>
                                        {$armory}
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Guild Recruiting?</b></small>
                                    </div>
                                    <div class='col-12'>
                                        {$recruit}
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Guild Debt?</b></small>
                                    </div>
                                    <div class='col-12'>
                                        {$debt}
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Active Wars?</b></small>
                                    </div>
                                    <div class='col-12'>
                                        {$wars}
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Gym Bonus</b></small>
                                    </div>
                                    <div class='col-12'>
                                        " . $gymBonus * 100 . "%
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Gym Bonus Active?</b></small>
                                    </div>
                                    <div class='col-12'>
                                        {$gymBonusTime}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br />
            </div>
            <div class='col-12 col-lg-6 col-xxl-4'>
                <div class='card'>
                    <div class='card-header'>
                        District Info for {$r['guild_name']}
                    </div>
                    <div class='card-body'>
                        <div class='row'>
                            <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Barracks Warriors</b></small>
                                    </div>
                                    <div class='col-12'>
                                        " . shortNumberParse($gdi['barracks_warriors']) . "
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Barracks Archers</b></small>
                                    </div>
                                    <div class='col-12'>
                                        " . shortNumberParse($gdi['barracks_archers']) . "
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Barracks Generals</b></small>
                                    </div>
                                    <div class='col-12'>
                                        " . shortNumberParse($gdi['barracks_generals']) . "
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Barracks Captains</b></small>
                                    </div>
                                    <div class='col-12'>
                                        " . shortNumberParse($gdi['barracks_captains']) . "
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Warriors Active</b></small>
                                    </div>
                                    <div class='col-12'>
                                        " . shortNumberParse($warriorsDeployed) . "
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Archers Active</b></small>
                                    </div>
                                    <div class='col-12'>
                                        " . shortNumberParse($archersDeployed) . "
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Generals Active</b></small>
                                    </div>
                                    <div class='col-12'>
                                        " . shortNumberParse($generalsDeployed) . "
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Tiles Owned</b></small>
                                    </div>
                                    <div class='col-12'>
                                        " . number_format($districtsOwned) . "
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
                echo "<div class='col-12 col-lg-6 col-xxl-4'>
                        <div class='card'>
                            <div class='card-header'>
                                {$r['guild_name']}'s Allies
                            </div>
                            <div class='card-body'>
                                <div class='row'>";
                                while ($allyR = $db->fetch_row($allyQ))
                                {
                                    $type = ($allyR['alliance_type'] == 1) ? "Traditional" : "Non-aggressive";
                                    if ($allyR['alliance_a'] == $guild)
                                        $otheralliance = $allyR['alliance_b'];
                                    else
                                        $otheralliance = $allyR['alliance_a'];
                                    echo "
                                    <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <small><b>{$type} Alliance</b></small>
                                            </div>
                                            <div class='col-12'>
                                                <a href='../guilds.php?action=view&id={$otheralliance}'>{$api->GuildFetchInfo($otheralliance,'guild_name')}</a>
                                            </div>
                                        </div>
                                    </div>";
                                }
                                echo"
                                </div>
                            </div>
                        </div>
                        <br />
                    </div>";
            }

        echo"
        </div>";

        //Log that the staff member has view this guild's information.
        $api->SystemLogsAdd($userid, 'staff', "Viewed {$r['guild_name']} [{$guild}]'s Guild Info.");
        $h->endpage();

    } else {
        //Basic form to select the guild.
        $csrf = request_csrf_html('staff_viewguild');
        echo "
        <form method='post'>
            <div class='row'>
                <div class='col-12'>
                    <div class='card'>
                        <div class='card-header'>
                            Select the guild you wish to view, then click submit.
                        </div>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-12 col-md'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <b><small>Guild</small></b>
                                        </div>
                                        <div class='col-12'>
                                            " . guilds_dropdown() . "
                                        </div>
                                    </div>
                                </div>
                                <div class='col-12 col-md'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <b><small><br /></small></b>
                                        </div>
                                        <div class='col-12'>
                                            <input type='submit' value='View Guild' class='btn btn-primary btn-block'>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {$csrf}
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
        $xp = (isset($_POST['xp']) && is_numeric($_POST['xp'])) ? abs(intval($_POST['xp'])) : 0;
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

        //Make sure the primary/Chivalry Tokens is input.
        if ((empty($prim)) && (empty($sec)) && (empty($xp))) {
            alert('danger', "Uh Oh!", "Please input how much Copper Coins, Chivalry Tokens or Guild Experience you wish to
            credit to this guild.");
            die($h->endpage());
        }

        //Make sure the reason is input
        if (empty($reason)) {
            alert('danger', "Uh Oh!", "Please input the reason why you are crediting this guild.");
            die($h->endpage());
        }

        //Select the Guild from database to ensure it exists.
        $q = $db->query("/*qc=on*/SELECT * FROM `guild` WHERE `guild_id` = {$guild}");
        if ($db->num_rows($q) == 0) {
            alert('danger', "Uh Oh!", "The guild you are trying to view does not exist, or is invalid.");
            die($h->endpage());
        }

        //Credit the guild
        $db->query("UPDATE `guild`
                    SET `guild_primcurr` = `guild_primcurr` + {$prim},
                    `guild_seccurr` = `guild_seccurr` + {$sec},
                    `guild_xp` = `guild_xp` + {$xp}
                    WHERE `guild_id` = {$guild}");

        //Put both numbers in a friendly format.
        $secf = shortNumberParse($sec);
        $primf = shortNumberParse($prim);
        $xpf = shortNumberParse($xp);

        //Notify the guild they've received some cash!
        $api->GuildAddNotification($guild, "The game administration has credited your guild {$primf} Copper Coins, {$secf} Chivalry Tokens, and {$xpf} Guild Experience for reason: {$reason}.");

        //Log the entry
        $api->SystemLogsAdd($userid, 'staff', "Credited Guild ID {$guild} with {$primf} Copper Coins, {$secf} Chivalry Tokens, and {$xpf} with reason '{$reason}'.");

        //Success to the end user.
        alert('success', "Success!", "You have successfully credited Guild ID {$guild} with {$primf} Copper Coins, {$secf} Chivalry Tokens, and {$xpf} with reason '{$reason}'.", true, 'index.php');
        $h->endpage();
    } else {
        //Form to credit a guild.
        $csrf = request_csrf_html('staff_creditguild');
        echo "<form method='post'>
            <div class='row'>
                <div class='col-12'>
                    <div class='card'>
                        <div class='card-header'>
                            Fill out the form to credit a guild with copper/tokens/experience.
                        </div>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-12 col-md-4 col-xl-3 col-xxl'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <b><small>Guild</small></b>
                                        </div>
                                        <div class='col-12'>
                                            " . guilds_dropdown() . "
                                        </div>
                                    </div>
                                </div>
                                <div class='col-12 col-sm-6 col-md-4 col-xl-3 col-xxl'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <b><small>Copper Coins</small></b>
                                        </div>
                                        <div class='col-12'>
                                            <input type='number' name='primary' value='0' required='1' min='0' class='form-control'>
                                        </div>
                                    </div>
                                </div>
                                <div class='col-12 col-sm-6 col-md-4 col-xl-3 col-xxl'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <b><small>Chivalry Tokens</small></b>
                                        </div>
                                        <div class='col-12'>
                                            <input type='number' name='secondary' value='0' required='1' min='0' class='form-control'>
                                        </div>
                                    </div>
                                </div>
                                <div class='col-12 col-sm-6 col-md-4 col-xl-3 col-xxl'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <b><small>Experience</small></b>
                                        </div>
                                        <div class='col-12'>
                                            <input type='number' name='xp' value='0' required='1' min='0' class='form-control'>
                                        </div>
                                    </div>
                                </div>
                                <div class='col-12 col-sm-6 col-md'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <b><small>Reason</small></b>
                                        </div>
                                        <div class='col-12'>
                                            <input type='text' name='reason' required='1' class='form-control'>
                                        </div>
                                    </div>
                                </div>
                                <div class='col-12'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <b><small><br /></small></b>
                                        </div>
                                        <div class='col-12'>
                                            <input type='submit' value='Credit Guild' class='btn btn-primary btn-block'>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {$csrf}
        </form>";
        $h->endpage();
    }
}

function viewwars()
{
    global $db, $userid, $api, $h;
    echo "<div class='row'>
        <div class='col-12'>
            <div class='card'>
                <div class='card-header'>
                    These are the active guild wars
                </div>
                <div class='card-body'>
                    <div class='row'>";
    //Select wars from database that are active.
    $q = $db->query("/*qc=on*/SELECT * FROM `guild_wars`
                    WHERE `gw_winner` = 0 AND
                    `gw_end` > " . time() . "
                    ORDER BY `gw_id` DESC");
    //If no active wars, tell the user.
    if ($db->num_rows($q) == 0) 
    {
        alert('danger', "Uh Oh!", "There are not any active guild wars at this time.", true, 'index.php');
        die($h->endpage());
    }
    //Request CSRF token
    $csrf = request_csrf_code('staff_guild_end_war');
    //Display the wars to the user!
    while ($r = $db->fetch_row($q)) 
    {
        echo "<div class='col-12 col-sm-6 col-md-4'>
                <div class='row'>
                    <div class='col-12'>
                        <b><small>Declarer</small></b>
                    </div>
                    <div class='col-12'>
                        <a href='../guilds.php?action=view&id={$r['gw_declarer']}'>{$api->GuildFetchInfo($r['gw_declarer'],'guild_name')}</a> (Points: " . number_format($r['gw_drpoints']) . ")
                    </div>
                </div>
            </div>
            <div class='col-12 col-sm-6 col-md-4'>
                <div class='row'>
                    <div class='col-12'>
                        <b><small>War Upon</small></b>
                    </div>
                    <div class='col-12'>
                        <a href='../guilds.php?action=view&id={$r['gw_declaree']}'>{$api->GuildFetchInfo($r['gw_declaree'],'guild_name')}</a> (Points: " . number_format($r['gw_depoints']) . ")
                    </div>
                </div>
            </div>
            <div class='col-12 col-md-4'>
                <div class='row'>
                    <div class='col-12'>
                        <b><small><br /></small></b>
                    </div>
                    <div class='col-12'>
                        <a href='?action=endwar&war={$r['gw_id']}&csrf={$csrf}' class='btn btn-danger btn-block'>End War</a>
                    </div>
                </div>
            </div>";
    }
    //Forget the wars query.
    $db->free_result($q);
    //Log that the wars were viewed.
    $api->SystemLogsAdd($userid, 'staff', "Viewed active guild wars.");
    echo "          </div>
                </div>
            </div>
        </div>
    </div>";
    $h->endpage();
}

function endwar()
{
    global $db, $userid, $api, $h;
    //Sanitize the war to be deleted.
    $_GET['war'] = (isset($_GET['war']) && is_numeric($_GET['war'])) ? abs(intval($_GET['war'])) : 0;
    //Verify the CSRF
    if (!isset($_GET['csrf']) || !verify_csrf_code('staff_guild_end_war', stripslashes($_GET['csrf']))) {
        alert('danger', "Action Blocked!", "Forms expire fairly quickly. Go back and submit it quicker!");
        die($h->endpage());
    }
    //Select the war to be deleted from the database.
    $q = $db->query("/*qc=on*/SELECT * FROM `guild_wars`
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
    $api->SystemLogsAdd($userid, 'staff', $log);
    alert('success', "Success!", "You have ended the war between {$gang1} and {$gang2}!", false);
    viewwars();
}

function editguild()
{
    global $db, $userid, $api, $h, $set;
    //Set the first step so it goes to the correct page.
    if (!isset($_POST['step'])) {
        $_POST['step'] = 0;
    }
    //Selecting the guild to edit.
    if ($_POST['step'] == 0) {
        $csrf = request_csrf_html('staff_editguild_1');
        echo "<form method='post'>
        <div class='row'>
            <div class='col-12'>
                <div class='card'>
                    <div class='card-header'>
                        Please select a guild to edit, then submit the form.
                    </div>
                    <div class='card-body'>
                        <div class='row'>
                            <div class='col-12 col-md'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <b><small>Guild</small></b>
                                    </div>
                                    <div class='col-12'>
                                        " . guilds_dropdown() . "
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-md'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <b><small><br /></small></b>
                                    </div>
                                    <div class='col-12'>
                                        <input type='submit' value='Edit Guild' class='btn btn-primary btn-block'>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type='hidden' value='1' name='step'>
        {$csrf}
        </form>";

    } elseif ($_POST['step'] == 1) {
        //Make sure input is safe.
        $guild = (isset($_POST['guild']) && is_numeric($_POST['guild'])) ? abs(intval($_POST['guild'])) : 0;

        //Validate CSRF check.
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_editguild_1', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Go back and submit it quicker!");
            die($h->endpage());
        }

        //Make sure guild is still valid input.
        if (empty($guild)) {
            alert('danger', "Uh Oh!", "You are trying to view an invalid or unspecified guild.");
            die($h->endpage());
        }

        //Select the Guild from database to ensure it exists.
        $q = $db->query("/*qc=on*/SELECT * FROM `guild` WHERE `guild_id` = {$guild}");
        if ($db->num_rows($q) == 0) {
            alert('danger', "Uh Oh!", "The guild you are trying to view does not exist, or is invalid.");
            die($h->endpage());
        }

        //Assign grabbed information to a variable
        $r = $db->fetch_row($q);

        //Show the information grabbed.

        //Armory select thing.
        $armory = ($r['guild_hasarmory'] == 'true') ?
            "<option value='true'>Purchased</option><option value='false'>Locked</option>" :
            "<option value='false'>Locked</option><option value='true'>Purchased</option>";

        //CSRF request
        $csrf = request_csrf_html('staff_editguild_2');
        $r['guild_max_copper'] = calculateMaxGuildVaultCopper($r['guild_level']);
        $r['guild_max_token'] = calculateMaxGuildVaultTokens($r['guild_level']);
        
        $gdi = ($db->fetch_row($db->query("SELECT * FROM `guild_district_info` WHERE `guild_id` = {$r['guild_id']}")));

        //Load the editing form
        echo "<form method='post'>
                <div class='row'>
                    <div class='col-12'>
                        <div class='card'>
                            <div class='card-header'>
                                Editing '{$r['guild_name']}' Guild (ID: {$guild})
                            </div>
                            <div class='card-body'>
                                <div class='row'>
                                    <div class='col-12 col-md-6'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <b><small>Guild Name</small></b>
                                            </div>
                                            <div class='col-12'>
                                                <input type='text' name='name' class='form-control' value='{$r['guild_name']}'>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-12 col-md-6'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <b><small>Guild Pic</small></b>
                                            </div>
                                            <div class='col-12'>
                                                <input type='text' name='pic' class='form-control' value='{$r['guild_pic']}'>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-12 col-xxl-6'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <b><small>Guild Description</small></b>
                                            </div>
                                            <div class='col-12'>
                                                <textarea name='desc' class='form-control'>{$r['guild_desc']}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-12 col-xxl-6'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <b><small>Guild Announcement</small></b>
                                            </div>
                                            <div class='col-12'>
                                                <textarea name='announcement' class='form-control'>{$r['guild_announcement']}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-12 col-sm-6 col-md-4 col-xxl-2'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <b><small>Guild Leader</small></b>
                                            </div>
                                            <div class='col-12'>
                                                " . guild_user_dropdown('owner', $guild, $r['guild_owner']) . "
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-12 col-sm-6 col-md-4 col-xxl-2'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <b><small>Guild Co-Leader</small></b>
                                            </div>
                                            <div class='col-12'>
                                                " . guild_user_dropdown('coowner', $guild, $r['guild_coowner']) . "
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-12 col-sm-6 col-md-4 col-xxl-2'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <b><small>Guild App Manager</small></b>
                                            </div>
                                            <div class='col-12'>
                                                " . guild_user_dropdown('appman', $guild, $r['guild_app_manager']) . "
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-12 col-sm-6 col-md-4 col-xxl-2'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <b><small>Guild Vault Manager</small></b>
                                            </div>
                                            <div class='col-12'>
                                                " . guild_user_dropdown('vaultman', $guild, $r['guild_vault_manager']) . "
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-12 col-sm-6 col-md-4 col-xxl-2'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <b><small>Guild Crime Lord</small></b>
                                            </div>
                                            <div class='col-12'>
                                                " . guild_user_dropdown('crimelord', $guild, $r['guild_crime_lord']) . "
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-12 col-sm-6 col-md-4 col-xxl-2'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <b><small>Vault Copper Coins</small></b>
                                            </div>
                                            <div class='col-12'>
                                                <input type='number' min='0' name='primary' max='{$r['guild_max_copper']}' class='form-control' value='{$r['guild_primcurr']}'>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-12 col-sm-6 col-md-4 col-xxl-2'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <b><small>Vault Chivalry Tokens</small></b>
                                            </div>
                                            <div class='col-12'>
                                                <input type='number' min='0' name='secondary' class='form-control' max='{$r['guild_max_token']}' value='{$r['guild_seccurr']}'>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-12 col-sm-6 col-md-4 col-xxl-2'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <b><small>Armory</small></b>
                                            </div>
                                            <div class='col-12'>
                                                <select name='armory' class='form-control' type='dropdown'>
                                                    {$armory}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-12 col-sm-6 col-md-4 col-xxl-2'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <b><small>Guild Level</small></b>
                                            </div>
                                            <div class='col-12'>
                                                <input type='number' min='1' name='level' class='form-control' value='{$r['guild_level']}'>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-12 col-sm-6 col-md-4 col-xxl-2'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <b><small>Guild XP</small></b>
                                            </div>
                                            <div class='col-12'>
                                                <input type='number' min='0' name='xp' class='form-control' value='{$r['guild_xp']}'>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-12 col-sm-6 col-md-4 col-xxl-2'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <b><small>Barracks Warriors</small></b>
                                            </div>
                                            <div class='col-12'>
                                                <input type='number' min='0' name='warriors' class='form-control' value='{$gdi['barracks_warriors']}'>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-12 col-sm-6 col-md-4 col-xxl-2'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <b><small>Barracks Archers</small></b>
                                            </div>
                                            <div class='col-12'>
                                                <input type='number' min='0' name='archers' class='form-control' value='{$gdi['barracks_archers']}'>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-12 col-sm-6 col-md-4 col-xxl-2'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <b><small>Barracks Generals</small></b>
                                            </div>
                                            <div class='col-12'>
                                                <input type='number' min='0' name='generals' class='form-control' value='{$gdi['barracks_generals']}'>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-12 col-sm-6 col-md-4 col-xxl-2'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <b><small>Barracks Captains</small></b>
                                            </div>
                                            <div class='col-12'>
                                                <input type='number' min='0' name='captains' class='form-control' value='{$gdi['barracks_captains']}'>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-12 col-sm-6 col-md-4 col-xxl-2'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <b><small>District Moves</small></b>
                                            </div>
                                            <div class='col-12'>
                                                <input type='number' min='0' name='moves' class='form-control' value='{$gdi['moves']}'>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-12'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <b><small><br /></small></b>
                                            </div>
                                            <div class='col-12'>
                                                <input type='submit' value='Edit Guild' class='btn btn-primary btn-block'>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <input type='hidden' value='2' name='step'>
            <input type='hidden' value='{$guild}' name='guild'>
            {$csrf}
            </form>";
    } elseif ($_POST['step'] == 2) {
        //Make sure input is safe.
        $guild = (isset($_POST['guild']) && is_numeric($_POST['guild'])) ? abs(intval($_POST['guild'])) : 0;
        $xp = (isset($_POST['xp']) && is_numeric($_POST['xp'])) ? abs(intval($_POST['xp'])) : 0;
        $lvl = (isset($_POST['level']) && is_numeric($_POST['level'])) ? abs(intval($_POST['level'])) : 0;
        $primary = (isset($_POST['primary']) && is_numeric($_POST['primary'])) ? abs(intval($_POST['primary'])) : 0;
        $secondary = (isset($_POST['secondary']) && is_numeric($_POST['secondary'])) ? abs(intval($_POST['secondary'])) : 0;
        $owner = (isset($_POST['owner']) && is_numeric($_POST['owner'])) ? abs(intval($_POST['owner'])) : 0;
        $coowner = (isset($_POST['coowner']) && is_numeric($_POST['coowner'])) ? abs(intval($_POST['coowner'])) : 0;
        $appman = (isset($_POST['appman']) && is_numeric($_POST['appman'])) ? abs(intval($_POST['appman'])) : 0;
        $vaultman = (isset($_POST['vaultman']) && is_numeric($_POST['vaultman'])) ? abs(intval($_POST['vaultman'])) : 0;
        $crimelord = (isset($_POST['crimelord']) && is_numeric($_POST['crimelord'])) ? abs(intval($_POST['crimelord'])) : 0;
        $name = $db->escape(htmlentities(stripslashes($_POST['name']), ENT_QUOTES, 'ISO-8859-1'));
        $desc = $db->escape(htmlentities(stripslashes($_POST['desc']), ENT_QUOTES, 'ISO-8859-1'));
        $announcement = $db->escape(htmlentities(stripslashes($_POST['announcement']), ENT_QUOTES, 'ISO-8859-1'));
        $armory = $_POST['armory'];
        $npic = (isset($_POST['pic']) && is_string($_POST['pic'])) ? stripslashes($_POST['pic']) : '';
        
        //District stuff
        $barWarriors = (isset($_POST['warriors']) && is_numeric($_POST['warriors'])) ? abs(intval($_POST['warriors'])) : 0;
        $barArchers = (isset($_POST['archers']) && is_numeric($_POST['archers'])) ? abs(intval($_POST['archers'])) : 0;
        $barGenerals = (isset($_POST['generals']) && is_numeric($_POST['generals'])) ? abs(intval($_POST['generals'])) : 0;
        $barCaptains = (isset($_POST['captains']) && is_numeric($_POST['captains'])) ? abs(intval($_POST['captains'])) : 0;
        $moves = (isset($_POST['moves']) && is_numeric($_POST['moves'])) ? abs(intval($_POST['moves'])) : 0;

        //Validate CSRF check.
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_editguild_2', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Go back and submit it quicker!");
            die($h->endpage());
        }

        //Check the guild ID is still set... else we can't change this guild
        if (empty($guild)) {
            alert('danger', "Uh Oh!", "You are trying to view an invalid or unspecified guild.");
            die($h->endpage());
        }

        //Select the Guild from database to ensure it exists.
        $q = $db->query("/*qc=on*/SELECT * FROM `guild` WHERE `guild_id` = {$guild}");
        if ($db->num_rows($q) == 0) {
            alert('danger', "Uh Oh!", "The guild you are trying to view does not exist, or is invalid.");
            die($h->endpage());
        }

        //Check that the owner is in the guild
        $oc = $db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `userid` = {$owner} AND `guild` = {$guild}");
        if ($db->num_rows($oc) == 0) {
            alert('danger', "Uh Oh!", "You are trying to set an invalid owner for this guild.");
            die($h->endpage());
        }

        //Check that the co-owner is in the guild
        $oc = $db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `userid` = {$coowner} AND `guild` = {$guild}");
        if ($db->num_rows($oc) == 0) {
            alert('danger', "Uh Oh!", "You are trying to set an invalid co-owner for this guild.");
            die($h->endpage());
        }
        
        //Check that the app manager is in the guild
        $oc = $db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `userid` = {$appman} AND `guild` = {$guild}");
        if ($db->num_rows($oc) == 0) {
            alert('danger', "Uh Oh!", "You are trying to set an invalid application manager for this guild.");
            die($h->endpage());
        }
        
        //Check that the vault manager is in the guild
        $oc = $db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `userid` = {$vaultman} AND `guild` = {$guild}");
        if ($db->num_rows($oc) == 0) {
            alert('danger', "Uh Oh!", "You are trying to set an invalid vault manager for this guild.");
            die($h->endpage());
        }
        
        //Check that the crime manager is in the guild
        $oc = $db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `userid` = {$crimelord} AND `guild` = {$guild}");
        if ($db->num_rows($oc) == 0) {
            alert('danger', "Uh Oh!", "You are trying to set an invalid crime lord for this guild.");
            die($h->endpage());
        }

        //Check for valid input on armory
        if ($armory != 'false' && $armory != 'true') {
            alert('danger', "Uh Oh!", "A guild can either have or not have an armory.");
            die($h->endpage());
        }
        
        if (!empty($npic)) 
        {
            $sz = get_filesize_remote($npic);
            if ($sz <= 0 || $sz >= 15728640) 
            {
                alert('danger', "Uh Oh!", "You picture's file size is too big. At maximum, picture file size can be 15MB.");
                $h->endpage();
                exit;
            }
            $image = (@isImage($npic));
            if (!$image) 
            {
                alert('danger', "Uh Oh!", "The link you've input is not an image.");
                die($h->endpage());
            }
        }

        //Update the guild
        $db->query("UPDATE `guild`
                    SET `guild_name` = '{$name}', `guild_desc` = '{$desc}', `guild_announcement` = '{$announcement}',
                    `guild_owner` = {$owner}, `guild_coowner` = {$coowner}, `guild_primcurr` = {$primary},
                    `guild_seccurr` = {$secondary}, `guild_level` = {$lvl}, `guild_xp` = {$xp},
                    `guild_hasarmory` = '{$armory}', `guild_pic` = '{$npic}', `guild_app_manager` = {$appman},
                    `guild_vault_manager` = {$vaultman}, `guild_crime_lord` = {$crimelord}
                    WHERE `guild_id` = {$guild}");
        $db->query("UPDATE `guild_district_info` 
                    SET `barracks_warriors` = {$barWarriors},
                    `barracks_archers` = {$barArchers},
                    `barracks_generals` = {$barGenerals},
                    `barracks_captains` = {$barCaptains},
                    `moves` = {$moves}
                    WHERE `guild_id` = {$guild}");
        alert('success', 'Success!', "You have successfully edited the {$name} guild!", true, 'index.php');
        $api->SystemLogsAdd($userid, 'staff', "Edited the <a href='../guilds.php?action=view&id={$guild}'>{$name}</a> Guild.");
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
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_delete_guild', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Go back and submit it quicker!");
            die($h->endpage());
        }

        //Make sure guild is still valid input.
        if (empty($guild)) {
            alert('danger', "Uh Oh!", "You are trying to view an invalid or unspecified guild.");
            die($h->endpage());
        }

        //Select the Guild from database to ensure it exists.
        $q = $db->query("/*qc=on*/SELECT * FROM `guild` WHERE `guild_id` = {$guild}");
        if ($db->num_rows($q) == 0) {
            alert('danger', "Uh Oh!", "The guild you are trying to view does not exist, or is invalid.");
            die($h->endpage());
        }

        //Delete all the things.
        deleteGuild($guild);

        //Alert user and log!
        alert('success', "Success!", "You have successfully deleted Guild ID {$guild}.");
        $api->SystemLogsAdd($userid, 'staff', "Deleted Guild ID {$guild}.");
        $h->endpage();
    } else {
        $csrf = request_csrf_html('staff_delete_guild');
        echo "<form method='post'>
        Please select the guild you wish to delete. This will delete EVERYTHING and cannot be reversed.<br />
        {$csrf}
        " . guilds_dropdown('guild') . "<br />
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
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_create_guild_crime', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Go back and submit it quicker!");
            die($h->endpage());
        }

        //Check to see if the crime's name is in use already.
        $q = $db->query("/*qc=on*/SELECT `gcID` FROM `guild_crimes` WHERE `gcNAME` = '{$name}'");
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
        $api->SystemLogsAdd($userid, 'staff', "Created the {$name} Guild Crime.");
        alert('success', "Success!", "You have successfully created the {$name} Guild Crime.", true, 'index.php');
        $h->endpage();
    } else {
        $csrf = request_csrf_html('staff_create_guild_crime');
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
        if (!isset($_POST['verf']) || !verify_csrf_code("staff_delete_guild_crime", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }

        //Verify crime exists.
        $cq = $db->query("/*qc=on*/SELECT `gcUSERS` from `guild_crimes` WHERE `gcID` = {$_POST['crime']}");
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
        $api->SystemLogsAdd($userid, 'staff', "Delete Guild Crime ID {$_POST['crime']}.");
        alert('success', 'Success!', "You have successfully deleted this guild crime.", true, 'index.php');
        $h->endpage();
    } else {
        //Select the crimes from database, based on how many members the guild has.
        $csrf = request_csrf_html('staff_delete_guild_crime');
        $q = $db->query("/*qc=on*/SELECT * FROM `guild_crimes`");
        echo "/*qc=on*/SELECT the guild crime you wish to delete. Guilds currently planning to commit this crime will have their crime
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