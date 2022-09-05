<?php
/*
	File:		viewguild.php
	Created: 	4/5/2016 at 12:32AM Eastern Time
	Info: 		Allows users to view their guild and do various actions.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
	
	CREATE TABLE `guild_owned_assets` (
  `owned_id` bigint(11) UNSIGNED NOT NULL,
  `guild_id` bigint(11) UNSIGNED NOT NULL,
  `asset_id` text NOT NULL,
  `asset_data_json` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `guild_owned_assets`
  ADD PRIMARY KEY (`owned_id`);
  
  ALTER TABLE `guild_owned_assets`
  MODIFY `owned_id` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT;
*/
$voterquery = 1;
$multi = 1.0;
require('globals.php');
if (!$ir['guild']) 
{
    alert('danger', "Uh Oh!", "You are not in a guild.", true, 'index.php');
} 
else 
{
    $gq = $db->query("/*qc=on*/SELECT * FROM `guild` WHERE `guild_id` = {$ir['guild']}");
    if ($db->num_rows($gq) == 0) 
    {
        alert('danger', "Uh Oh!", "Your guild's data could not be selected. Please contact an admin immediately.");
        die($h->endpage());
    }
    $gd = $db->fetch_row($gq);
    $db->free_result($gq);
    $wq = $db->query("/*qc=on*/SELECT COUNT(`gw_id`) FROM `guild_wars` WHERE (`gw_declarer` = {$ir['guild']} OR `gw_declaree` = {$ir['guild']}) AND `gw_winner` = 0");
    if ($db->fetch_single($wq) > 0) 
    {
        alert('warning', "Guild Wars in Progress", "Your guild is in {$db->fetch_single($wq)} wars. View active wars <a href='?action=warview'>here</a>.", false);
    }
	$gd['xp_needed'] = round(($gd['guild_level'] + 1) * ($gd['guild_level'] + 1) * ($gd['guild_level'] + 1) * 2.2);
	if ($gd['guild_primcurr'] < 0)
		alert('info',"Guild Debt!","Your guild is in debt. If your debt is not paid off in " . TimeUntil_Parse($gd['guild_debt_time']) . " your guild will be dissolved.",false);
    echo "
	<h3><u>Your Guild, {$gd['guild_name']}</u></h3>
   	";
    if ($ir['guild'] <= 20)
    {
        if (!guildOwnsAsset($ir['guild'], "guild_gym"))
            guildPurchaseAsset($ir['guild'], "guild_gym");
    }
    if ($gd['guild_hasarmory'])
    {
        if (!guildOwnsAsset($ir['guild'], "guild_armory"))
            guildPurchaseAsset($ir['guild'], "guild_armory");
    }
    if (!isset($_GET['action'])) {
        $_GET['action'] = '';
    }
    switch ($_GET['action']) {
        case 'summary':
            summary();
            break;
        case 'donate':
            donate();
            break;
        case "members":
            members();
            break;
        case "kick":
            staff_kick();
            break;
        case "leave":
            leave();
            break;
        case "atklogs":
            atklogs();
            break;
        case "staff":
            staff();
            break;
        case "warview":
            warview();
            break;
        case "armory":
            armory();
            break;
		case "gym":
            gym();
            break;
        case "adonate":
            adonate();
            break;
        case "crimes":
            crimes();
            break;
		case "forums":
            guild_forums();
            break;
		case "viewpolls":
			guild_polls();
			break;
		case "oldpolls":
			guild_oldpolls();
			break;
		case "donatexp":
			guild_donatexp();
			break;
        default:
            home();
            break;
    }
}
function home()
{
    global $db, $userid, $ir, $gd;
    $guildPic = (empty($gd['guild_pic'])) ? "<i>Tell your guild leadership to set a guild profile picture!</i>" : "<img src='" . parseImage($gd['guild_pic']) . "' placeholder='The {$gd['guild_name']} guild picture.' width='300' class='img-fluid' title='The {$gd['guild_name']} guild picture.'>";
    //The main guild index.
    echo "<div class='row'>
        <div class='col-12 col-xl-4'>
            <div class='card'>
                <div class='card-header'>
                    {$gd['guild_name']}
                </div>
                <div class='card-body'>
                    {$guildPic}
                </div>
            </div>
            <br />
        </div>
		<div class='col-12 col-xl-8'>
            <div class='card'>
                <div class='card-body'>
                    <div class='row'>
        				<div class='col-6 col-lg-4 col-xxl-3'>
        					<a class='btn btn-info btn-block' href='?action=summary'>Summary</a>
                            <br />
        				</div>
        				<div class='col-6 col-lg-4 col-xxl-3'>
        					<a class='btn btn-success btn-block' href='?action=donate'>Donate</a>
                            <br />
        				</div>
        				<div class='col-6 col-lg-4 col-xxl-3'>
        					<a class='btn btn-primary btn-block' href='?action=members'>Members</a>
                            <br />
        				</div>
        				<div class='col-6 col-lg-4 col-xxl-3'>
        					<a class='btn btn-warning btn-block' href='?action=crimes'>Crimes</a>
                            <br />
        				</div>
        				<div class='col-6 col-lg-4 col-xxl-3'>
        					<a class='btn btn-danger btn-block' href='?action=leave'>Leave Guild</a>
                            <br />
        				</div>
        				<div class='col-6 col-lg-4 col-xxl-3'>
        					<a class='btn btn-dark btn-block' href='?action=atklogs'>Attack Logs</a>
                            <br />
        				</div>
        				<div class='col-6 col-lg-4 col-xxl-3'>
        					<a class='btn btn-secondary btn-block' href='?action=armory'>Armory</a>
                            <br />
        				</div>
        				<div class='col-6 col-lg-4 col-xxl-3'>
        					<a class='btn btn-primary btn-block' href='?action=forums'>Forums</a>
                            <br />
        				</div>
        				<div class='col-6 col-lg-4 col-xxl-3'>
        					<a class='btn btn-info btn-block' href='?action=viewpolls'>Guild Polls</a>
                            <br />
        				</div>
        				<div class='col-6 col-lg-4 col-xxl-3'>
        					<a class='btn btn-success btn-block' href='?action=gym'>Guild Gym</a>
                            <br />
        				</div>";
        				if (isGuildStaff())
        				{
        					echo "
        						<div class='col'>
        							<a class='btn btn-danger btn-block' href='?action=staff&act2=idx'>Staff Room</a>
        						</div>";
        				}
        				echo"
                    </div>
                </div>
            </div>
		</div>
	</div>
	<hr />";
	if (!empty($gd['guild_announcement']))
	{
		alert('dark','',"<b>Guild Announcement</b><br />{$gd['guild_announcement']}",false);
	}
	$viewCount=getCurrentUserPref('guildNotifView', 10);
    $q = $db->query("/*qc=on*/SELECT * FROM `guild_notifications` WHERE `gn_guild` = {$ir['guild']} ORDER BY `gn_time` DESC  LIMIT {$viewCount}");
    echo "<div class='card'>
        <div class='card-header'>
            Last {$viewCount} Guild Notifications
        </div>
        <div class='card-body'>";
    while ($r = $db->fetch_row($q)) 
	{
		echo "
		<div class='row'>
			<div class='col text-left'>
				 {$r['gn_text']}<br />
				 <small>" . DateTime_Parse($r['gn_time']) . "</small>
			</div>
		</div>
		<hr />";
    }
    echo "</div></div>";
    $db->free_result($q);
}

function summary()
{
    global $db, $gd, $ir, $api, $wq;
	$cnt = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `guild` = {$gd['guild_id']}");
	$ldrnm = parseUsername($gd['guild_owner']);
	$vldrnm = parseUsername($gd['guild_coowner']);
	$appnm = parseUsername($gd['guild_app_manager']);
	$vaultnm = parseUsername($gd['guild_vault_manager']);
	$crlonm = parseUsername($gd['guild_crime_lord']);
	$armory = (guildOwnsAsset($gd['guild_id'], "guild_armory")) ? "<span class='text-success'>Purchased</span>" : "<span class='text-danger'>Not purchased</span>";
	$gym = (guildOwnsAsset($gd['guild_id'], "guild_gym")) ? "<span class='text-success'>Purchased</span>" : "<span class='text-danger'>Not purchased</span>";
	$recruit = ($gd['guild_ba'] == 0) ? "<span class='text-success'>Open</span>" : "<span class='text-danger'>Closed</span>";
	$debt = ($gd['guild_primcurr'] > 0) ? "<span class='text-success'>No Debt</span>" : "<span class='text-danger'>In Debt!!</span>" ;
	$wars = ($db->fetch_single($wq) == 0) ? "<span class='text-success'>No active wars</span>" : "<span class='text-danger'> " . number_format($db->fetch_single($wq)) . " active wars</span>";
    //List all the guild's information
	echo "
	<div class='row'>
		<div class='col-md'>
			<div class='card'>
				<div class='card-header'>
					Guild Staff
				</div>
				<div class='card-body text-left'>
					<div class='row'>
						<div class='col'>
							<b>Leader</b>
						</div>
						<div class='col'>
							<a href='profile.php?user={$gd['guild_owner']}'>{$ldrnm}</a>
						</div>
					</div>
					<div class='row'>
						<div class='col'>
							<b>Co-Leader</b>
						</div>
						<div class='col'>
							<a href='profile.php?user={$gd['guild_coowner']}'>{$vldrnm}</a>
						</div>
					</div>
					<div class='row'>
						<div class='col'>
							<b>Application Manager</b>
						</div>
						<div class='col'>
							<a href='profile.php?user={$gd['guild_app_manager']}'>{$appnm}</a>
						</div>
					</div>
					<div class='row'>
						<div class='col'>
							<b>Vault Manager</b>
						</div>
						<div class='col'>
							<a href='profile.php?user={$gd['guild_vault_manager']}'>{$vaultnm}</a>
						</div>
					</div>
					<div class='row'>
						<div class='col'>
							<b>Crime Lord</b>
						</div>
						<div class='col'>
							<a href='profile.php?user={$gd['guild_crime_lord']}'>{$crlonm}</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class='col-md'>
			<div class='card'>
				<div class='card-header'>
					Guild Information
				</div>
				<div class='card-body text-left'>
					<div class='row'>
						<div class='col'>
							<b>Members</b>
						</div>
						<div class='col'>
							" . shortNumberParse($db->fetch_single($cnt)) . " / " . shortNumberParse($gd['guild_level'] * 5) . "
						</div>
					</div>
					<div class='row'>
						<div class='col'>
							<b>Level</b>
						</div>
						<div class='col'>
							" . shortNumberParse($gd['guild_level']) . "
						</div>
					</div>
					<div class='row'>
						<div class='col'>
							<b>XP</b>
						</div>
						<div class='col'>
							" . shortNumberParse($gd['guild_xp']) . " / " . shortNumberParse($gd['xp_needed']) . "
						</div>
					</div>
					<div class='row'>
						<div class='col'>
						[<a href='?action=donatexp'>Donate Experience</a>]
						</div>
					</div>
					<div class='row'>
						<div class='col'>
							<b>Copper Coins*</b>
						</div>
						<div class='col'>
							" . shortNumberParse($gd['guild_primcurr']) . " / " . shortNumberParse(calculateMaxGuildVaultCopper($ir['guild'])) . "
						</div>
					</div>
					<div class='row'>
						<div class='col'>
							<b>Chivalry Tokens</b>
						</div>
						<div class='col'>
							" . shortNumberParse($gd['guild_seccurr']) . " / " . shortNumberParse(calculateMaxGuildVaultTokens($ir['guild'])) . "
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<br />";
	echo "
	<div class='row'>
		<div class='col-md'>
			<div class='card'>
				<div class='card-header'>
					Guild Allies
				</div>
				<div class='card-body text-left'>
					<div class='row'>";
						$q=$db->query("/*qc=on*/SELECT * 
							FROM `guild_alliances` 
							WHERE (`alliance_a` = {$ir['guild']} OR `alliance_b` = {$ir['guild']})
							AND `alliance_true` = 1");
						while ($r=$db->fetch_row($q))
						{
							$type = ($r['alliance_type'] == 1) ? "Traditional" : "Non-aggressive";
							if ($r['alliance_a'] == $ir['guild'])
								$otheralliance=$r['alliance_b'];
							else
								$otheralliance=$r['alliance_a'];
							echo "<a href='guilds.php?action=view&id={$otheralliance}'>{$api->GuildFetchInfo($otheralliance,'guild_name')}</a><br />";
						}
						echo"
					</div>
				</div>
			</div>
		</div>
		<div class='col-md'>
			<div class='card'>
				<div class='card-header'>
					Guild Misc
				</div>
				<div class='card-body text-left'>
					<div class='row'>
						<div class='col'>
							<b>Armory</b>
						</div>
						<div class='col'>
							{$armory}
						</div>
					</div>
                    <div class='row'>
						<div class='col'>
							<b>Gym</b>
						</div>
						<div class='col'>
							{$gym}
						</div>
					</div>
					<div class='row'>
						<div class='col'>
							<b>Recruitment</b>
						</div>
						<div class='col'>
							{$recruit}
						</div>
					</div>
					<div class='row'>
						<div class='col'>
							<b>Finances</b>
						</div>
						<div class='col'>
							{$debt}
						</div>
					</div>
					<div class='row'>
						<div class='col'>
							<b>Active Wars</b>
						</div>
						<div class='col'>
							{$wars}
						</div>
					</div>
					<div class='row'>
						<div class='col'>
							<b>Daily Upkeep</b>
						</div>
						<div class='col'>
							" . shortNumberParse(calculateUpkeep($ir['guild'])) . " Copper Coins
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	  * = Increased every night by 2%.
	  <a href='viewguild.php'>Go Back</a>";
}

function guild_donatexp()
{
	global $db, $gd, $set, $ir, $api, $h, $userid;
	$xpformula = $ir['xp_needed']/65;
	if ($xpformula < 1000)
		$xpformula = 1000;
	$xpformula=round($xpformula);
	
	if (isset($_POST['xp']))
	{
		$_POST['xp'] = (isset($_POST['xp']) && is_numeric($_POST['xp'])) ? abs(intval($_POST['xp'])) : 0;
		if (empty($_POST['xp']))
		{
			alert('danger',"Uh Oh!","You need to input the amount of experience you wish to donate.");
			die($h->endpage());
		}
		if ($_POST['xp'] < $xpformula)
		{
			alert('danger',"Uh Oh!","You need to donate, at minimum, " . number_format($xpformula) . " experience points.");
			die($h->endpage());
		}
		$points=floor($_POST['xp']/$xpformula);
		$xprequired=$points*$xpformula;
		if ($ir['xp'] < $xprequired)
		{
			alert('danger',"Uh Oh!","You do not have the required experience to donate that much.");
			die($h->endpage());
		}
		$db->query("UPDATE `users` SET `xp` = `xp` - {$xprequired} WHERE `userid` = {$userid}");
		updateDonations($gd['guild_id'],$userid,'xp',$xprequired);
		updateDonations($gd['guild_id'],$userid,'guild_xp',$points);
		$db->query("UPDATE `guild` SET `guild_xp` = `guild_xp` + {$points} WHERE `guild_id` = {$gd['guild_id']}");
		$event = "<a href='profile.php?user={$userid}'>{$ir['username']}</a> exchanged " . shortNumberParse($xprequired) . " experience for " . shortNumberParse($points) . " guild experience.";
		$api->GuildAddNotification($gd['guild_id'], $event);
		alert('success',"Success!","You have successfully traded " . shortNumberParse($xprequired) . " experience for " . shortNumberParse($points) . " guild experience.");
	}
	else
	{
	    echo "Here you may donate your experience points to your guild at a ratio of " . shortNumberParse($xpformula) . " experience points for 1 Guild 
		Experience Point. You currently have " . shortNumberParse($ir['xp']) . " experience points which you can donate. <b>This tool will only take even 
		amounts of experience (Only in groups of " . shortNumberParse($xpformula) . ".)</b> How many do you wish to donate to your guild? Experience points donate cannot be given back.<br />
		<form method='post'>
			<input type='number' name='xp' min='{$xpformula}' value='{$ir['xp']}' class='form-control'>
			<input type='submit' class='btn btn-primary' value='Donate XP'>
		</form>";
	}
}

function guild_forums()
{
	global $db, $ir, $userid, $gd;
    $q = $db->query(
                    "/*qc=on*/SELECT *
                     FROM `forum_forums`
                     WHERE `ff_auth` = 'guild'
                     AND `ff_owner` = {$ir['guild']}");
	if ($db->num_rows($q) == 0)
    {
        $gd['guild_name'] = $db->escape($gd['guild_name']);
        $db->query("INSERT INTO `forum_forums` VALUES (NULL, '{$gd['guild_name']}', '', '0', '0', 'guild', '0', '{$ir['guild']}')");
        $r = array();
        $r['ff_id'] = $db->insert_id();
    }
    else
    {
        $r = $db->fetch_row($q);
        if ($r['ff_name'] != $gd['guild_name'])
        {
            $gd['guild_name'] = $db->escape($gd['guild_name']);
            $db->query(
                    "UPDATE `forum_forums`
                     SET `ff_name` = '{$gd['guild_name']}'
                     WHERE `ff_id` = {$r['ff_id']}");
        }
    }
    $db->free_result($q);
	ob_get_clean();
    $forum_url = "forums.php?viewforum={$r['ff_id']}";
    header("Location: {$forum_url}");
    exit;
}

function donate()
{
    global $db, $userid, $ir, $gd, $api, $h, $set;
    if (isset($_POST['primary'])) {

        //Make sure the POST is safe to work with.
        $_POST['primary'] = (isset($_POST['primary']) && is_numeric($_POST['primary'])) ? abs(intval($_POST['primary'])) : 0;
        $_POST['secondary'] = (isset($_POST['secondary']) && is_numeric($_POST['secondary'])) ? abs(intval($_POST['secondary'])) : 0;

        //Verify we passed the CSRF check.
        if (!isset($_POST['verf']) || !verify_csrf_code('guild_donate', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }

        //Make sure the form is filled out.
        if (empty($_POST['primary']) && empty($_POST['secondary'])) {
            alert('danger', "Uh Oh!", "Please fill out the previous form before submitting.");
            die($h->endpage());
        }

        //Trying to donate more primary than user has.
        if ($_POST['primary'] > $ir['primary_currency']) {
            alert('danger', "Uh Oh!", "You are trying to donate more Copper Coins than you currently have.");
            die($h->endpage());
            //Trying to donate more secondary than user has.
        } else if ($_POST['secondary'] > $ir['secondary_currency']) {
            alert('danger', "Uh Oh!", "You are trying to donate more Chivalry Tokens than you currently have.");
            die($h->endpage());
            //Donation amount would fill up the guild's vault.
        } else if ($_POST['primary'] + $gd['guild_primcurr'] > calculateMaxGuildVaultCopper($ir['guild'])) 
		{
		    alert('danger', "Uh Oh!", "Your guild's vault can only hold " . shortNumberParse(calculateMaxGuildVaultCopper($ir['guild'])) . " Copper Coins.");
            die($h->endpage());
        } 
        else if ($_POST['secondary'] + $gd['guild_seccurr'] > calculateMaxGuildVaultTokens($ir['guild']))
		{
		    alert('danger', "Uh Oh!", "Your guild's vault can only hold " . shortNumberParse(calculateMaxGuildVaultTokens($ir['guild'])) . " Chivalry Tokens.");
            die($h->endpage());
        } 
		else {
            //Donate the currencies!
            $api->UserTakeCurrency($userid, 'primary', $_POST['primary']);
            $api->UserTakeCurrency($userid, 'secondary', $_POST['secondary']);
			updateDonations($gd['guild_id'],$userid,'copper',$_POST['primary']);
			updateDonations($gd['guild_id'],$userid,'tokens',$_POST['secondary']);
            $db->query("UPDATE `guild`
                        SET `guild_primcurr` = `guild_primcurr` + {$_POST['primary']},
					    `guild_seccurr` = `guild_seccurr` + {$_POST['secondary']}
					    WHERE `guild_id` = {$gd['guild_id']}");
            $my_name = htmlentities($ir['username'], ENT_QUOTES, 'ISO-8859-1');
            $event = "<a href='profile.php?user={$userid}'>{$my_name}</a> donated
									" . shortNumberParse($_POST['primary']) . " Copper Coins and 
									" . shortNumberParse($_POST['secondary']) . " Chivalry Tokens to the guild.";
            $api->GuildAddNotification($gd['guild_id'], $event);
            $api->SystemLogsAdd($userid, 'guild_vault', "Donated " . shortNumberParse($_POST['primary']) . " Copper and " . shortNumberParse($_POST['secondary']) . " Chivalry Tokens to their guild.");
            alert('success', "Success!", "You have successfully donated " . shortNumberParse($_POST['primary']) . " Copper Coins and " . shortNumberParse($_POST['secondary']) . " Chivalry Tokens to your guild.", true, 'viewguild.php');
        }
    } else {
        $copperCapacity = calculateMaxGuildVaultCopper($ir['guild']) - $gd['guild_primcurr'];
        $tokenCapacity = calculateMaxGuildVaultTokens($ir['guild']) - $gd['guild_seccurr'];
        $maxCopper = ($ir['primary_currency'] > $copperCapacity) ? $copperCapacity : $ir['primary_currency'];
        $maxTokens = ($ir['secondary_currency'] > $tokenCapacity) ? $tokenCapacity : $ir['secondary_currency'];
        $csrf = request_csrf_html('guild_donate');
        echo "<form action='?action=donate' method='post'>
        <div class='row'>
            <div class='col-12'>
                <div class='card'>
                    <div class='card-header'>
                        Donating to Guild
                    </div>
                    <div class='card-body'>
                        <div class='row'>
                            <div class='col-12'>
                                You may donate up to " . shortNumberParse($maxCopper) . " Copper Coins 
                                and " . shortNumberParse($maxTokens) . " Chivalry Tokens to your guild at this time.
                            </div>
                            <div class='col-12 col-md-6 col-xl-4 col-xxl-5'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <b><small>Copper Coins</small></b>
                                    </div>
                                    <div class='col-12'>
                                        <input type='number' name='primary' value='{$maxCopper}' required='1' max='{$maxCopper}' class='form-control' min='0' />
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-md-6 col-xl-4 col-xxl-5'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <b><small>Chivalry Tokens</small></b>
                                    </div>
                                    <div class='col-12'>
                                        <input type='number' name='secondary' required='1' max='{$maxTokens}' class='form-control' value='{$maxTokens}' min='0' />
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-xl-4 col-xxl-2'>
                                <div class='col-12'>
                                    <b><small><br /></small></b>
                                </div>
                                <div class='col-12'>
                                    <input type='submit' class='btn btn-primary btn-block' value='Donate to Vault' />
                                </div>
                            </div>
                            <div class='col-12'>
                                <hr />
                                <div class='row'>
                                    <div class='col-12 col-md-4'>
                                        <a href='viewguild.php' class='btn btn-block btn-danger'>Go Back</a><br />
                                    </div>
                                    <div class='col-12 col-sm-6 col-md-4'>
                                        <a href='?action=donatexp' class='btn btn-primary btn-block'>Donate Experience</a><br />
                                    </div>
                                    <div class='col-12 col-sm-6 col-md-4'>
                                        <a href='?action=adonate' class='btn btn-primary btn-block'>Donate Items</a>
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
    }
}

function members()
{
    global $db, $userid, $gd, $api;
    //List all the guild members. ^_^
	echo "<div class='row'>
			<div class='col-4'>
				 <h3>Player</h3>
			</div>
			<div class='col-4'>
				 <h3>General Info</h3>
			</div>
			<div class='col-4'>
				 <h3>Donations</h3>
			</div>
		</div>
		<hr />";
    $q = $db->query("/*qc=on*/SELECT `userid`, `username`, `level`, `display_pic`, `primary_currency` FROM `users` WHERE `guild` = {$gd['guild_id']} ORDER BY `level` DESC");
    $csrf = request_csrf_html('guild_kickuser');
    while ($r = $db->fetch_row($q)) {
		$r['status'] = '';
		if ($api->UserStatus($r['userid'], 'infirmary'))
			$r['status'] .= "<span class='text-danger'>Injured</span><br />";
		if ($api->UserStatus($r['userid'], 'dungeon'))
			$r['status'] .= "<span class='text-danger'>Locked Up</span><br />";
		if ((!$api->UserStatus($r['userid'], 'dungeon')) && (!$api->UserStatus($r['userid'], 'infirmary')))
			$r['status'] .= "<span class='text-success'>Perfectly Fine</span><br />";
        $r['username2']=parseUsername($r['userid']);
        $r['display_pic']=parseImage(parseDisplayPic($r['userid']));
		$r2=$db->fetch_row($db->query("SELECT * FROM `guild_donations` WHERE `userid` = {$r['userid']} AND `guildid` = {$gd['guild_id']}"));
		   echo "<div class='row'>
			<div class='col-2'>
				 <img src='{$r['display_pic']}' class='img-fluid'>
			</div>
			<div class='col-2'>
				 <a href='profile.php?user={$r['userid']}'>{$r['username2']}</a>
			</div>
			<div class='col-4'>
				 Level: {$r['level']}<br />
				Copper Coins: " . shortNumberParse($r['primary_currency']) . "<br />
				 {$r['status']}";
				 if (isGuildLeadership())
				 {
					 echo "
						<form action='?action=kick' method='post'>
							<input type='hidden' name='ID' value='{$r['userid']}' />
							{$csrf}
							<input type='submit' class='btn btn-primary' value='Kick {$r['username']}' />
						</form>";
				 }
				 echo "
			</div>
			<div class='col-4'>
				 Copper Coins: " . shortNumberParse($r2['copper']) . "<br />
				Chivalry Tokens: " . shortNumberParse($r2['tokens']) . "<br />
				Player XP: " . shortNumberParse($r2['xp']) . "<br />
				Guild XP: " . shortNumberParse($r2['guild_xp']) . "**
			</div>
		</div>
		<hr />";
    }
    $db->free_result($q);
    echo "
	<small>*=Since 10/7/2018 at 5:21PM<br />
	**=Since 4/20/20 @ 4:34PM</small>
	<br />
	<a href='viewguild.php'>Go Back</a>
   	";
}

function staff_kick()
{
    global $db, $userid, $ir, $gd, $api, $h, $wq;
    //Current user is either owner or co-owner
    if (isGuildLeader()) {

        //Verify CSRF check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_kickuser", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.", true, '?action=members');
            die($h->endpage());
        }

        //Make sure POST is safe to work with.
        $_POST['ID'] = (isset($_POST['ID']) && is_numeric($_POST['ID'])) ? abs(intval($_POST['ID'])) : 0;
        $who = $_POST['ID'];

        //Trying to kick the owner.
        if ($who == $gd['guild_owner']) {
            alert('danger', "Uh Oh!", "You cannot kick the guild leader.", true, '?action=members');
            //Trying to kick the co-owner
        } else if ($who == $gd['guild_coowner']) {
            alert('danger', "Uh Oh!", "You cannot kick the guild co-leader.", true, '?action=members');
            //Trying to kick themselves.
        } else if ($who == $gd['guild_app_manager']) {
            alert('danger', "Uh Oh!", "You cannot kick the guild application manager.", true, '?action=members');
            //Trying to kick themselves.
        } else if ($who == $gd['guild_vault_manager']) {
            alert('danger', "Uh Oh!", "You cannot kick the guild vault manager.", true, '?action=members');
            //Trying to kick themselves.
        } else if ($who == $gd['guild_crime_lord']) {
            alert('danger', "Uh Oh!", "You cannot kick the guild crime lord.", true, '?action=members');
            //Trying to kick themselves.
        } else if ($who == $userid) {
            alert('danger', "Uh Oh!", "You cannot kick yourself from the guild.", true, '?action=members');
            //Trying to kick while at war.
        } else if ($db->fetch_single($wq) > 0) {
            alert('danger', "Uh Oh!", "You cannot kick members from your guild while you are at war.", true, '?action=members');
        } else {
            //User to be kicked exists and is in the guild.
            $q = $db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `userid` = $who AND `guild` = {$gd['guild_id']}");
            if ($db->num_rows($q) > 0) 
			{
				$count = $api->UserCountItem($who,$gd['guild_sword_item']);
				if ($count > 0)
				{
					$api->UserTakeItem($who,$gd['guild_sword_item'],$count);
					$api->GuildAddItem($gd['guild_id'], $gd['guild_sword_item'], $count);
				}
				if ($api->UserEquippedItem($who, 'primary', $gd['guild_sword_item']))
				{
					$db->query("UPDATE `users` SET `equip_primary` = 0 WHERE `userid` = {$who}");
					$api->GuildAddItem($gd['guild_id'], $gd['guild_sword_item'], 1);
				}
				if ($api->UserEquippedItem($who, 'secondary', $gd['guild_sword_item']))
				{
					$db->query("UPDATE `users` SET `equip_secondary` = 0 WHERE `userid` = {$who}");
					$api->GuildAddItem($gd['guild_id'], $gd['guild_sword_item'], 1);
				}
                //Kick the user and add the notification.
                $kdata = $db->fetch_row($q);
                $db->query("UPDATE `users` SET `guild` = 0 WHERE `userid` = {$who}");
                $d_username = htmlentities($kdata['username'], ENT_QUOTES, 'ISO-8859-1');
                $d_oname = htmlentities($ir['username'], ENT_QUOTES, 'ISO-8859-1');
                alert('success', "Success!", "You have kicked {$kdata['username']} from the guild.", true, '?action=members');
                $their_event = "You were kicked out of the {$gd['guild_name']} guild by <a href='profile.php?user={$userid}'>{$d_oname}</a>.";
                $api->GameAddNotification($who, $their_event);
                $event = "<a href='profile.php?user={$who}'>{$d_username}</a> was kicked out of the guild by <a href='profile.php?user={$userid}'>{$d_oname}</a>.";
                $api->GuildAddNotification($gd['guild_id'], $event);
            } else {
                alert('danger', "Uh Oh!", "User does not exist, or is not in the guild.", true, '?action=members');
            }
            $db->free_result($q);
        }
    } else {
        alert('danger', "Uh Oh!", "You do not have permission to kick people from the guild.", true, 'viewguild.php');
    }
}

function leave()
{
    global $db, $userid, $ir, $gd, $api, $h, $wq;
    //Make sure person leaving is not a guild owner/co-owner.
    if (isGuildStaff()) {
        alert('danger', "Uh Oh!", "You cannot leave the guild while as a staff member.", true, 'viewguild.php');
        die($h->endpage());
    }
    //Player *does* want to leave.
    if (isset($_POST['submit']) && $_POST['submit'] == 'yes') {

        //Verify CSRF Check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_leave", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }

        //Make sure no deserters during war times.
        if ($db->fetch_single($wq) > 0) {
            alert('danger', "Uh Oh!", "You cannot leave your guild while at war.", true, 'viewguild.php');
            die($h->endpage());
        }
		//Check if player has the guild's special sword.
		if ($api->UserHasItem($userid, $gd['guild_sword_item'], 1))
		{
			alert('danger', "Uh Oh!", "Please donate your {$api->SystemItemIDtoName($gd['guild_sword_item'])} from your inventory to your guild before you leave.", true, 'viewguild.php');
            die($h->endpage());
		}
		if ($api->UserEquippedItem($userid, 'primary', $gd['guild_sword_item']))
		{
			alert('danger', "Uh Oh!", "Please donate your {$api->SystemItemIDtoName($gd['guild_sword_item'])} from your primary equipment slot to your guild before you leave.", true, 'viewguild.php');
            die($h->endpage());
		}
		if ($api->UserEquippedItem($userid, 'secondary', $gd['guild_sword_item']))
		{
			alert('danger', "Uh Oh!", "Please donate your {$api->SystemItemIDtoName($gd['guild_sword_item'])} from your secondary equipment to your guild before you leave.", true, 'viewguild.php');
            die($h->endpage());
		}

        //Allow player to leave.
        $db->query("UPDATE `users` SET `guild` = 0  WHERE `userid` = {$userid}");
        $api->GuildAddNotification($ir['guild'], "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has left the guild.");
        alert('success', "Success!", "You have successfully left your guild.", true, 'index.php');

        //Player *does not* want to leave
    } elseif (isset($_POST['submit']) && $_POST['submit'] == 'no') {
        alert('success', "Success!", "You have chosen to stay in your guild.", true, 'viewguild.php');
    } else {
        $csrf = request_csrf_html('guild_leave');
        echo "Do you really want to leave your guild?
        <form action='?action=leave' method='post'>
            {$csrf}
			<input type='hidden' name='submit' value='yes'>
        	<input type='submit' class='btn btn-primary' value='Yes, leave.' />
		</form><br />
		<form action='?action=leave' method='post'>
			{$csrf}
			<input type='hidden' name='submit' value='no'>
        	<input type='submit' class='btn btn-primary' value='No, stay.' />
        </form>
		<a href='viewguild.php'>Go Back</a>";
    }
}

function atklogs()
{
    global $db, $ir, $api;
    //Select the last 50 attacks involving someone in the guild.
    $atks = $db->query("/*qc=on*/SELECT `a`.*, `u1`.*, `u2`.*
                        FROM `attack_logs` AS `a`
                        INNER JOIN `users` AS `u1`
                        ON `attacker` = `u1`.`userid`
                        INNER JOIN `users` AS `u2`
                        ON `attacked` = `u2`.`userid`
                        WHERE `result` != 'lost'
                        AND (`u1`.`guild` = {$ir['guild']} 
                        OR `u2`.`guild` = {$ir['guild']})
                        ORDER BY `attack_time` DESC
                        LIMIT 50");
    echo "<b>Last 50 attacks involving anyone in your guild</b><br />";
    while ($r = $db->fetch_row($atks)) 
	{
        $rowcolor = ($api->UserInfoGet($r['attacker'],'guild') == $ir['guild']) ? "text-success" : "text-danger";
        $d = DateTime_Parse($r['attack_time']);
        if ($r['result'] == 'xp')
        {
            $didwhat = "<span class='{$rowcolor}'>used</span> <a href='profile.php?user={$r['attacked']}'>{$api->SystemUserIDtoName($r['attacked'])}</a> for experience.";
        }
        if ($r['result'] == 'beatup')
        {
            $didwhat = "<span class='{$rowcolor}'>severely beat up</span> <a href='profile.php?user={$r['attacked']}'>{$api->SystemUserIDtoName($r['attacked'])}</a>.";
        }
        if ($r['result'] == 'mugged')
        {
            $didwhat = "<span class='{$rowcolor}'>mugged</span> <a href='profile.php?user={$r['attacked']}'>{$api->SystemUserIDtoName($r['attacked'])}</a>.";
        }
		echo "
		<div class='row'>
			<div class='col text-left'>
				 <a href='profile.php?user={$r['attacker']}'>{$api->SystemUserIDtoName($r['attacker'])}</a> {$didwhat}<br />
				 <small>{$d}</small>
			</div>
		</div>
		<hr />";
    }
    $db->free_result($atks);
    echo "<a href='viewguild.php'>Go Back</a>";
}

function warview()
{
    global $db, $ir, $api;
    //Select all active wars.
    $wq = $db->query("/*qc=on*/SELECT * FROM `guild_wars` WHERE
					(`gw_declarer` = {$ir['guild']} OR `gw_declaree` = {$ir['guild']}) 
					AND `gw_winner` = 0");
    echo "<b>These are the current wars your guild is participating in.</b><hr />
	<table class='table table-bordered'>
		<tr align='left'>
			<th>
				Declarer
			</th>
			<th>
				Declared Upon
			</th>
			<th>
				War Concludes
			</th>
		</tr>";
    while ($r = $db->fetch_row($wq)) {
        echo "<tr align='left'>
				<td>
					<a href='guilds.php?action=view&id={$r['gw_declarer']}'>{$api->GuildFetchInfo($r['gw_declarer'],'guild_name')}</a><br />
						(Points: " . number_format($r['gw_drpoints']) . ")
				</td>
				<td>
					<a href='guilds.php?action=view&id={$r['gw_declaree']}'>{$api->GuildFetchInfo($r['gw_declaree'],'guild_name')}</a><br />
						(Points: " . number_format($r['gw_depoints']) . ")
				</td>
				<td>
					" . TimeUntil_Parse($r['gw_end']) . "
				</td>
			</tr>";
    }
    echo "</table>";
}

function armory()
{
    global $db, $gd, $h, $api, $ir;
    //Guild has not purchased the armory
    if (!guildOwnsAsset($ir['guild'], "guild_armory")) {
        alert('danger', "Uh Oh!", "Your guild has yet to purchase an armory. Come back after your guild has purchased an armory.", true, 'viewguild.php');
        die($h->endpage());
    } else {
        //List all the armory items.
        echo "Here are the items your guild currently has stockpiled in its armory. You may donate items <a href='?action=adonate'>here</a>.<br />";
        $inv = $db->query("/*qc=on*/SELECT `gaQTY`, `itmsellprice`, `itmid`, `gaID`,
                             `weapon`, `armor`, `itmtypename`, `itmdesc`
                             FROM `guild_armory` AS `iv`
                             INNER JOIN `items` AS `i`
                             ON `iv`.`gaITEM` = `i`.`itmid`
                             INNER JOIN `itemtypes` AS `it`
                             ON `i`.`itmtype` = `it`.`itmtypeid`
                             WHERE `iv`.`gaGUILD` = {$ir['guild']}
                             ORDER BY `i`.`itmtype` ASC, `i`.`itmname` ASC");
        echo "<table class='table table-bordered table-striped'>
	    <thead>
		<tr align='left'>
			<th colspan='2'>
			    Item (Qty)
            </th>
			<th class='hidden-xs-down'>
			    Item Cost (Total)
            </th>
		</tr></thead>";
        $lt = "";
        while ($i = $db->fetch_row($inv)) {
            if ($lt != $i['itmtypename']) {
                $lt = $i['itmtypename'];
                echo "\n<thead><tr>
            			<th colspan='4'>
            				<b>{$lt}</b>
            			</th>
            		</tr></thead>";
            }
            $i['itmdesc'] = htmlentities($i['itmdesc'], ENT_QUOTES);
			$icon = returnIcon($i['itmid'],1.5);
            echo "
            <tr align='left'>
				<td align='center'>
					{$icon}
				</td>
        		<td>
					<a href='iteminfo.php?ID={$i['itmid']}' data-toggle='tooltip' data-placement='right' title='{$i['itmdesc']}'>
						{$api->SystemItemIDtoName($i['itmid'])}
					</a>";
            if ($i['gaQTY'] > 1) {
                echo " (" . shortNumberParse($i['gaQTY']) . ")";
            }
            echo "</td>
        	  <td class='hidden-xs-down'>" . shortNumberParse($i['itmsellprice']);
            echo "  (" .shortNumberParse($i['itmsellprice'] * $i['gaQTY']) . ")</td></tr>";
        }
        echo "</table>";
    }
}

function gym()
{
	global $gd, $h, $api, $ir, $userid, $multi, $ir, $sound, $set, $db;
	$energy = $api->UserInfoGet($userid, 'energy', true);
	$will = $api->UserInfoGet($userid, 'will', true);
	$macropage = ('viewguild.php?action=gym');
	$multiplier = calculateGuildGymBonus($gd['guild_id']);
	$gymCost = $set['GUILD_PRICE'] * 10;
    if (!guildOwnsAsset($ir['guild'], "guild_gym"))
    {
        if (!isset($_GET['purchase']))
        {
            alert('danger',"Uh Oh!","Your guild needs to purchase the guild gym before you can train at it.",true,'viewguild.php');
            
            if (isGuildCoLeader())
            {
                alert('info',"Purchase Guild Gym!","If your guild is level 3 or better, you may purchase a Guild Gym for 
                                " . shortNumberParse($gymCost) . " Copper Coins, taken from the guild vault.",true,'?action=gym&purchase', 'Purchase Gym');
            }
            die($h->endpage());
        }
        else
        {
            if (!isGuildCoLeader())
            {
                alert('danger',"Uh Oh!","You cannot buy a guild gym unless you are ranked co-leader or better.",true,'?action=gym&purchase');
                die($h->endpage());
            }
            elseif ($gd['guild_primcurr'] < $gymCost)
            {
                alert('danger',"Uh Oh!","Your guild vault only has " . shortNumberParse($gd['guild_primcurr']) . " Copper Coins, 
                        but you need " . shortNumberParse($gymCost) . " Copper Coins to purchase the Gym.",true,'viewguild.php');
                die($h->endpage());
            }
            elseif (guildOwnsAsset($ir['guild'], "guild_gym"))
            {
                alert('danger',"Uh Oh!","Your guild already owns the Guild Gym. No reason to purchase it again. ",true,'viewguild.php');
                die($h->endpage());
            }
            alert('success',"Success!","You have purchased the Guild Gym for " . shortNumberParse($gymCost) . " Copper Coins.",true,'viewguild.php');
            $api->GuildAddNotification($ir['guild'], "<a href='profile.php?user={$userid}'>" .parseUsername($userid) . "</a> [{$userid}] has purchased the Guild Gym for " . shortNumberParse($gymCost) . " Copper Coins.");
            guildPurchaseAsset($ir['guild'], "guild_gym");
            $db->query("UPDATE `guild` SET `guild_primcurr` = `guild_primcurr` - {$gymCost} WHERE `guild_id` = {$ir['guild']}");
        }
    }
	//User is in the infirmary
	if ($api->UserStatus($ir['userid'], 'infirmary')) {
		alert("danger", "Unconscious!", "You cannot train while you're in the infirmary.", true, 'index.php');
		die($h->endpage());
	}
	//User is in the dungeon.
	if ($api->UserStatus($ir['userid'], 'dungeon')) {
		alert("danger", "Locked Up!", "You cannot train while you're in the dungeon.", true, 'index.php');
		die($h->endpage());
	}
	//Convert POST values to Stat Names.
	$statnames = array("Strength" => "strength", "Agility" => "agility", "Guard" => "guard", "Labor" => "labor", "All" => "all");
	//Training amount is not set, so set to 0.
	if (!isset($_GET["amnt"])) {
		$_GET["amnt"] = 0;
	}
	$_GET["amnt"] = abs($_GET["amnt"]);
	echo "<h3><i class='game-icon game-icon-weight-lifting-down'></i> Guild Gym</h3>";
	if ((isset($_GET['daybonus'])) && (!userHasEffect($userid, effect_daily_gym_bonus)))
	{
	    $bonus = Random(1,25);
	    userGiveEffect($userid, effect_daily_gym_bonus, getNextDayReset() - time(), $bonus);
	    alert('success',"","You have received a {$bonus}% training bonus for the day.", false);
	}
	if (!userHasEffect($userid, effect_daily_gym_bonus))
	    alert('warning',"","It appears you have not redeemed your Daily Gym Bonus for the day.",true, "?action=gym&daybonus", "Redeem Bonus");
    else
        alert('primary',"","You are currently receiving a " . returnEffectMultiplier($userid, effect_daily_gym_bonus) . "% training boost for the next " . TimeUntil_Parse(returnEffectDone($userid, effect_daily_gym_bonus)) . ".", false);
	echo "<div id='gymsuccess'></div>";
	//Small logic to keep the last trained stat selected.
	if (!isset($str_select)) {
		$str_select = '';
	}
	if (!isset($agl_select)) {
		$agl_select = '';
	}
	if (!isset($grd_select)) {
		$grd_select = '';
	}
	if (!isset($lab_select)) {
		$lab_select = '';
	}
	if (!isset($all_select)) {
		$all_select = '';
	}
	if (isset($_COOKIE['lastTrainedStat']))
	{
		if ($_COOKIE['lastTrainedStat'] == "strength")
			$str_select = 'selected';
		elseif ($_COOKIE['lastTrainedStat'] == "agility")
			$agl_select = 'selected';
		elseif ($_COOKIE['lastTrainedStat'] == "guard")
			$grd_select = 'selected';
		elseif ($_COOKIE['lastTrainedStat'] == "labor")
			$lab_select = 'selected';
		elseif ($_COOKIE['lastTrainedStat'] == "all")
			$all_select = 'selected';
	}
	//Grab the user's stat ranks.
	$ir['strank'] = get_rank($ir['strength'], 'strength');
	$ir['agirank'] = get_rank($ir['agility'], 'agility');
	$ir['guarank'] = get_rank($ir['guard'], 'guard');
	$ir['labrank'] = get_rank($ir['labor'], 'labor');
	$ir['all_four'] = ($ir['labor'] + $ir['strength'] + $ir['agility'] + $ir['guard']);
	$ir['af_rank'] = get_rank($ir['all_four'], 'all');
	echo "Choose the stat you wish to train, and enter how many times you wish to train it. You can train up to <span id='trainTimesTotal'>" . number_format($ir['energy']) . "</span> times.<br />
	Your guild's gym will give you " . number_format($multiplier*100) . "% the stats you'd gain at the Normal Gym.
	<form method='post' id='gymTrainGuild'>
		<div class='card'>
			<div class='card-body'>
				<div class='row'>
					<div class='col-12 col-sm-3 col-lg-2'>
						<b>Stat</b>
					</div>
					<div class='col-12 col-sm-9 col-lg-10'>
						<select type='dropdown' name='stat' class='form-control'>
							<option {$str_select} value='Strength'>
								Strength (Have " . number_format($ir['strength']) . "; Ranked: {$ir['strank']})
							</option>
							<option {$agl_select} value='Agility'>
								Agility (Have " . number_format($ir['agility']) . "; Ranked: {$ir['agirank']})
							</option>
							<option {$grd_select} value='Guard'>
								Guard (Have " . number_format($ir['guard']) . "; Ranked: {$ir['guarank']})
							</option>
							<option {$lab_select} value='Labor'>
								Labor (Have " . number_format($ir['labor']) . "; Ranked: {$ir['labrank']})
							</option>
							<option {$all_select} value='All'>
								All Four (Have " . number_format($ir['all_four']) . "; Ranked: {$ir['af_rank']})
							</option>
						</select>
					</div>
				</div>
				<div class='row'>
					<div class='col-12 col-sm-3 col-lg-2'>
						<b>Energy</b>
					</div>
					<div class='col-12 col-sm-9 col-lg-10'>
						<input type='number' class='form-control' min='1' max='{$ir['energy']}' name='amnt' value='{$ir['energy']}' id='trainTimes' /><br />
					</div>
				</div>
				<div class='row'>
					<div class='col-12 col-sm-6 col-md-2 col-lg-6 col-xl-2'>
						<input type='submit' class='btn btn-success btn-block' value='Train' id='trainGuild' /><br />
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4'>
						<a href='#' class='btn btn-primary btn-block' id='gymRefillEnergy'>Refill Energy (<span id='gymEnergy'>{$energy}</span>%)</a><br />
					</div>
					<div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4'>
						<a href='#' class='btn btn-primary btn-block'id='gymRefillWill'>Regen Will (<span id='gymWill'>{$will}</span>%)</a><br />
					</div>
					<div class='col-12 col-sm-6 col-md-2 col-lg-6 col-xl-2'>
						<a href='#' class='btn btn-secondary btn-block'id='gymFillWill'>Fill Will</a><br />
					</div>
				</div>
			</div>
		</div>
	</form>";
	if ($ir['vip_days'] > 0)
	    if (getCurrentUserPref('enableMusic', 'true'))
	        $sound->playBGM('traintrance');
}

function adonate()
{
    global $api, $userid, $h, $ir, $gd;
    if (!guildOwnsAsset($ir['guild'], "guild_armory")) 
    {
        alert('danger', "Uh Oh!", "Your guild has yet to purchase an armory. Come back after your guild has purchased an armory.", true, 'viewguild.php');
        die($h->endpage());
    } else {
        if (isset($_POST['item'])) {
            //Secure the POST
            $_POST['item'] = (isset($_POST['item']) && is_numeric($_POST['item'])) ? abs($_POST['item']) : 0;
            $_POST['qty'] = (isset($_POST['qty']) && is_numeric($_POST['qty'])) ? abs($_POST['qty']) : 0;

            //Verify CSRF Check has passed.
            if (!isset($_POST['verf']) || !verify_csrf_code("guild_armory_donate", stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
                die($h->endpage());
            }

            //Verify item exists
            if (!$api->SystemItemIDtoName($_POST['item'])) {
                alert('danger', "Uh Oh!", "You are trying to donate a non-existent item.");
                die($h->endpage());
            }

            //Verify user has the item/quantity
            if (!$api->UserHasItem($userid, $_POST['item'], $_POST['qty'])) {
                alert('danger', "Uh Oh!", "You are trying to donate an item you don't have, or an amount you don't have.");
                die($h->endpage());
            }

            //Donation successful!, log everything.
            $item = $api->SystemItemIDtoName($_POST['item']);
            $api->UserTakeItem($userid, $_POST['item'], $_POST['qty']);
            $api->GuildAddItem($gd['guild_id'], $_POST['item'], $_POST['qty']);
            $api->SystemLogsAdd($userid, 'guilds', "Donated {$_POST['qty']} {$item}(s) to their guild's armory.");
            $api->GuildAddNotification($ir['guild'], "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has donated {$_POST['qty']} {$item}(s) to the guild's armory.");
            alert("success", "Success!", "You have successfully donated {$_POST['qty']} {$item}(s) to your guild's armory.", true, "?action=armory");
        } else {
            $csrf = request_csrf_html('guild_armory_donate');
            echo "<form method='post'>
            Fill out the form completely to donate an item to your guild.<br />
            " . inventory_dropdown() . "<br />
            <input type='number' name='qty' placeholder='Quantity' class='form-control'>
            <br />
            {$csrf}
            <input type='submit' value='Donate Item' class='btn btn-primary'>
            </form>";
        }
    }
}

function crimes()
{
    global $gd, $db;
    if ($gd['guild_crime'] > 0) {
        $ttc = TimeUntil_Parse($gd['guild_crime_done']);
        $gcname = $db->fetch_single($db->query("/*qc=on*/SELECT `gcNAME` from `guild_crimes` WHERE `gcID` = {$gd['guild_crime']}"));
        echo "Your guild will be attempting to commit the {$gcname} crime. It will begin in {$ttc}.";
    } else {
        echo "Your guild is not currently planning on committing a crime. Contact your guild's leadership to stage one.";
    }
}

function guild_polls()
{
	global $db, $userid, $ir, $h;
    echo "Cast your vote today!<br />";

    $_POST['poll'] = (isset($_POST['poll']) && is_numeric($_POST['poll'])) ? abs($_POST['poll']) : '';
    $_POST['choice'] = (isset($_POST['choice']) && is_numeric($_POST['choice'])) ? abs($_POST['choice']) : '';
    $ir['voted'] = unserialize($ir['voted']);
    if (!$_POST['choice'] || !$_POST['poll']) {
        echo "<a href='?action=oldpolls'>View Closed Polls</a>";
    }
    if ($_POST['choice'] && $_POST['poll']) {
        if (isset($ir['voted'][$_POST['poll']])) {
            alert('danger', "Uh Oh!", "You have already voted in this poll.");
            die($h->endpage());
        }
        $check_q = $db->query("/*qc=on*/SELECT COUNT(`id`) FROM `polls`  WHERE `active` = '1' AND `id` = {$_POST['poll']} AND `visibility` = {$ir['guild']}");
        if ($db->fetch_single($check_q) == 0) {
            $db->free_result($check_q);
            alert('danger', "Uh Oh!", "Poll does not exist, or is no longer active.");
            die($h->endpage());
        }
        $db->free_result($check_q);
        $ir['voted'][$_POST['poll']] = $_POST['choice'];
        $ser = $db->escape(serialize($ir['voted']));
        $db->query(
            "UPDATE `uservotes`
				 SET `voted` = '$ser'
				 WHERE `userid` = $userid");
        $db->query("UPDATE `polls` SET `voted{$_POST['choice']}` = `voted{$_POST['choice']}` + 1 WHERE `active` = '1' AND `id` = {$_POST['poll']}");
        alert('success', "Success!", "You have successfully submitted your vote.", true, '?action=viewpolls');
    } else {
        $q = $db->query("/*qc=on*/SELECT * FROM `polls` WHERE `active` = '1' AND `visibility` = {$ir['guild']}");
        if (!$db->num_rows($q)) {
            echo "<br />There's no polls open at this time.";
        } else {
            while ($r = $db->fetch_row($q)) {
                $r['votes'] = $r['voted1'] + $r['voted2'] + $r['voted3'] + $r['voted4'] + $r['voted5'] + $r['voted6'] + $r['voted7'] + $r['voted8'] + $r['voted9'] + $r['voted10'];
                if (isset($ir['voted'][$r['id']])) {
                    echo "<div class='card'><div class='card-body'><div class='row'>
								<div class='col-sm-3'>
									<h3>Poll Question</h3>
								</div>
								<div class='col-sm'>
									<h6>{$r['question']}</h6>
								</div>
							</div>
							<hr />";
                    if (!$r['hidden']) {
                        for ($i = 1; $i <= 10; $i++) {
                            if ($r['choice' . $i]) {
                                $k = 'choice' . $i;
                                $ke = 'voted' . $i;
                                if ($r['votes'] != 0) {
                                    $perc = round(($r[$ke] / $r['votes'] * 100));
                                } else {
                                    $perc = 0;
                                }
                                echo "<div class='row'>
										<div class='col-sm-3'>
											{$r[$k]}
										</div>
										<div class='col-sm-1'>
											{$r[$ke]}
										</div>
										<div class='col-sm'>
											<div class='progress' style='height: 1rem;'>
												<div class='progress-bar' role='progressbar' aria-valuenow='{$perc}' style='width:{$perc}%' aria-valuemin='0' aria-valuemax='100'><span>{$perc}%</span></div>
											</div>
										</div>
									</div>
									</div></div></br />";
                            }
                        }
                    } else {
                        echo "<div class='row'>
							<div class='col-sm-3'>
								<h5>Results hidden.</h5>
							</div>
						</div>
						<hr />";
                    }
                    $myvote = $r['choice' . $ir['voted'][$r['id']]];
                    echo "<div class='row'>
							<div class='col-sm-3'>
								<h5>Total Votes</h5>
							</div>
							<div class='col-sm-1'>
								<h6>" . number_format($r['votes']) . "</h6>
							</div>
							<div class='col-sm-3'>
								<h5>Your Vote</h5>
							</div>
							<div class='col-sm-3'>
								<i>{$myvote}</i>
							</div>
						</div>
						<hr />";
                } else {
                    echo "<div class='card'><div class='card-body'>
				<form method='post'>
					<input type='hidden' name='poll' value='{$r['id']}' />
					<div class='row'>
						<div class='col-sm-3'>
							<h3>Poll Question</h3>
						</div>
						<div class='col-sm'>
							<h6>{$r['question']}</h6>
						</div>
					</div>
					<hr />";
                    for ($i = 1; $i <= 10; $i++) {
                        if ($r['choice' . $i]) {
                            $k = 'choice' . $i;
                            if ($i == 1) {
                                $c = "checked='checked'";
                            } else {
                                $c = "";
                            }
                            echo "<div class='row'>
								<div class='col-sm-3'>
									{$r[$k]}
								</div>
								<div class='col-sm'>
										<input type='radio' class='form-control' name='choice' value='{$i}' {$c} />
									</div>
							</div>
							<hr />";
                        }
                    }
                    echo "<div class='row'>
							<div class='col-sm'>
								<input type='submit' class='btn btn-primary' value='Cast Vote' />
							</div>
							</div>
						</form>
						</div></div></br />";
                }
            }
        }
		echo "<br /><a href='viewguild.php'>Go Back</a>";
        $db->free_result($q);
    }
}

function guild_oldpolls()
{
	global $db, $ir;
    echo "<a href='?action=viewpolls'>Cast Your Vote!</a><br />";
    $q =
        $db->query("/*qc=on*/SELECT * FROM `polls` WHERE `active` = '0' AND `visibility` = {$ir['guild']} ORDER BY `id` DESC");
    if (!$db->num_rows($q)) {
        alert('danger', "Uh Oh!", "There are no closed polls.", true, '?action=viewpolls');
    } else {
        while ($r = $db->fetch_row($q)) {
            $r['votes'] = $r['voted1'] + $r['voted2'] + $r['voted3'] + $r['voted4'] + $r['voted5'] + $r['voted6'] + $r['voted7'] + $r['voted8'] + $r['voted9'] + $r['voted10'];
            echo "
			<div class='card'><div class='card-body'><div class='row'>
				<div class='col-sm-3'>
					<h3>Poll Question</h3>
				</div>
				<div class='col-sm'>
					<h6>{$r['question']}</h6>
				</div>
			</div>
			<hr />";
            for ($i = 1; $i <= 10; $i++) {
                if ($r['choice' . $i]) {
                    $k = 'choice' . $i;
                    $ke = 'voted' . $i;
                    if ($r['votes'] != 0) {
                        $perc = round($r[$ke] / $r['votes'] * 100);
                    } else {
                        $perc = 0;
                    }
					echo "<div class='row'>
							<div class='col-sm-3'>
								{$r[$k]}
							</div>
							<div class='col-sm-1'>
								{$r[$ke]}
							</div>
							<div class='col-sm'>
								<div class='progress' style='height: 1rem;'>
									<div class='progress-bar' role='progressbar' aria-valuenow='{$perc}' style='width:{$perc}%' aria-valuemin='0' aria-valuemax='100'><span>{$perc}%</span></div>
								</div>
							</div>
						</div>
						<hr />";
                }
            }
            echo "<div class='row'>
							<div class='col-sm-3'>
								<h5>Total Votes</h5>
							</div>
							<div class='col-sm-1'>
								<h6>" . number_format($r['votes']) . "</h6>
							</div>
						</div>
						</div></div></br />";
        }
		echo "> <a href='viewguild.php'>Go Back</a>";
    }
    $db->free_result($q);
}

function staff()
{
    global $userid, $gd, $h;
    if (isGuildStaff()) {
        if (!isset($_GET['act2'])) {
            $_GET['act2'] = 'idx';
        }
        switch ($_GET['act2']) {
            case "idx":
                staff_idx();
                break;
            case "apps":
                staff_apps();
                break;
            case "vault":
                staff_vault();
                break;
            case "coowner":
                staff_coowner();
                break;
			case "appmngr":
                staff_app_manager();
                break;
			case "vaultmngr":
                staff_vault_manager();
                break;
			case "crimelord":
                staff_crime_lord();
                break;
            case "ament":
                staff_announcement();
                break;
			case "pic":
                staff_pic();
                break;
            case "massmail":
                staff_massmail();
                break;
            case "masspay":
                staff_masspayment();
                break;
            case "desc":
                staff_desc();
                break;
            case "leader":
                staff_leader();
                break;
            case "name":
                staff_name();
                break;
            case "town":
                staff_town();
                break;
            case "untown":
                staff_untown();
                break;
            case "declarewar":
                staff_declare();
                break;
            case "levelup":
                staff_levelup();
                break;
            case "tax":
                staff_tax();
                break;
            case "dissolve":
                staff_dissolve();
                break;
            case "armory":
                staff_armory();
                break;
            case "crimes":
                staff_crimes();
                break;
			case "intromsg":
                staff_intromsg();
                break;
			case "blockapps":
                staff_blockapps();
                break;
			case "doally":
                staff_ally();
                break;
			case "viewrally":
                staff_view_alliance_request();
                break;
			case "viewallies":
                staff_view_alliances();
                break;
			case "addpoll":
                add_poll();
                break;
			case "endpoll":
                end_poll();
                break;
			case "boost":
                staff_bonus();
                break;
			case "swords":
                staff_sword_guild();
                break;
			case "upsword":
                staff_upgrade_sword();
                break;
			case "dsword":
                staff_decommission_sword();
                break;
			case "rsword":
                staff_sword_reroll();
                break;
			case "renamesword":
                staff_sword_rename();
                break;
			case "picsword":
                staff_sword_pic();
                break;
			case "guildassets":
			    staff_asset_management();
			    break;
            default:
                staff_idx();
                break;
        }
    } else {
        alert('danger', "Uh Oh!", "You have no permission to be here.", true, 'viewguild.php');
        die($h->endpage());
    }
}

function staff_idx()
{
    global $db, $userid, $gd;
	//
	if (isGuildStaff())
	{
		echo "<div class='row'>";
		if (isGuildLeader())
		{
			echo "
				<div class='col-12 col-xl-6'>
					<div class='card'>
						<div class='card-header'>
							<div class='row'>
								<div class='col'>
									Guild Leader
								</div>
								<div class='col'>
									<a href='profile.php?user={$gd['guild_owner']}'>" . parseUsername($gd['guild_owner']) . "</a> [{$gd['guild_owner']}]
								</div>
							</div>
						</div>
						<div class='card-body'>
							<div class='row'>
                                <div class='col-6 col-md-4 col-xl-6 col-xxxl-4'>
								    <a href='?action=staff&act2=leader' class='btn btn-block btn-danger'>Transfer Leader</a><br />
                                </div>
                                <div class='col-6 col-md-4 col-xl-6 col-xxxl-4'>
								    <a href='?action=staff&act2=name' class='btn btn-block btn-primary'>Change Name</a><br />
                                </div>
                                <div class='col-12 col-sm-6 col-md-4 col-xl-12 col-xxl-6 col-xxxl-4'>
								    <a href='?action=staff&act2=desc' class='btn btn-block btn-primary'>Change Description</a><br />
                                </div>
                                <div class='col-6 col-md-4 col-xl-6 col-xxxl-4'>
								    <a href='?action=staff&act2=town' class='btn btn-block btn-primary'>Change Town</a><br />
                                </div>";
                    			if ($db->fetch_single($db->query("/*qc=on*/SELECT `town_id` FROM `town` WHERE `town_guild_owner` = {$gd['guild_id']}")) > 0)
                    			{
                    			    echo "
        								<div class='col-6 col-md-4 col-xl-6 col-xxxl-4'>
        									<a href='?action=staff&act2=untown' class='btn btn-block btn-danger'>Surrender Town</a><br />
        								</div>
        								<div class='col-6 col-md-4 col-xl-6 col-xxxl-4'>
        									<a href='?action=staff&act2=tax' class='btn btn-block btn-primary'>Change Tax</a><br />
        								</div>";
                    			}
			                 echo"
    							<div class='col-6 col-md-4 col-xl-6 col-xxxl-4'>
    								<a href='?action=staff&act2=doally' class='btn btn-block btn-primary'>Declare Ally</a><br />
    							</div>
    							<div class='col-12 col-sm-6 col-md-4 col-xl-6 col-xxxl-4'>
    								<a href='?action=staff&act2=viewrally' class='btn btn-block btn-primary'>View Ally Requests</a><br />
    							</div>
    							<div class='col-6 col-md-4 col-xl-6 col-xxxl-4'>
    								<a href='?action=staff&act2=viewallies' class='btn btn-block btn-primary'>View Allies</a><br />
    							</div>
    							<div class='col-6 col-md-4 col-xl-6 col-xxxl-4'>
    								<a href='?action=staff&act2=declarewar' class='btn btn-block btn-primary'>Declare War</a><br />
    							</div>
    							<div class='col-6 col-md-4 col-xl-6 col-xxxl-4'>
    								<a href='?action=staff&act2=dissolve' class='btn btn-block btn-danger'>Dissolve Guild</a><br />
    							</div>
    							<div class='col-6 col-md-4 col-xl-6 col-xxxl-4'>
    								<a href='?action=staff&act2=swords' class='btn btn-block btn-primary'>Guild Weapons</a><br />
    							</div>
    							<div class='col-12'>
    								<a href='?action=staff&act2=dsword' class='btn btn-block btn-danger'>Decommission Guild Weapons</a><br />
    							</div>
                            </div>
						</div>
					</div>
                    <br />
				</div>";
		}
		if (isGuildCoLeader())
		{
			echo "
				<div class='col-12 col-xl-6'>
					<div class='card'>
						<div class='card-header'>
							<div class='row'>
								<div class='col'>
									Guild Co-Leader
								</div>
								<div class='col'>
									<a href='profile.php?user={$gd['guild_coowner']}'>" . parseUsername($gd['guild_coowner']) . "</a> [{$gd['guild_coowner']}]
								</div>
							</div>
						</div>
						<div class='card-body'>
							<div class='row'>
                                <div class='col-12 col-sm-6 col-xl-12 col-xxl-6 col-xxxl-4'>
								    <a href='?action=staff&act2=coowner' class='btn btn-block btn-danger'>Transfer Co-Leader</a><br />
							    </div>
    							<div class='col-12 col-sm-6 col-xl-12 col-xxl-6 col-xxxl-4'>
    								<a href='?action=staff&act2=ament' class='btn btn-block btn-primary'>Change Announcement</a><br />
    							</div>
    							<div class='col-6 col-md-4 col-xl-6 col-xxl-4'>
    								<a href='?action=staff&act2=pic' class='btn btn-block btn-primary'>Change Picture</a><br />
    							</div>
    							<div class='col-6 col-md-4 col-xl-6 col-xxl-4'>
    								<a href='?action=staff&act2=massmail' class='btn btn-block btn-primary'>Mass Mail Guild</a><br />
    							</div>
    							<div class='col-6 col-md-4 col-xl-6 col-xxl-4'>
    								<a href='?action=staff&act2=levelup' class='btn btn-block btn-primary'>Level Up</a><br />
    							</div>
    							<div class='col-6 col-md-4 col-xl-6 col-xxl-4'>
    								<a href='?action=staff&act2=addpoll' class='btn btn-block btn-primary'>Start Poll</a><br />
    							</div>
    							<div class='col-6 col-md-4 col-xl-6 col-xxl-4'>
    								<a href='?action=staff&act2=endpoll' class='btn btn-block btn-primary'>End Poll</a><br />
    							</div>
    							<div class='col-6 col-md-4 col-xl-6 col-xxl-4'>
    								<a href='?action=staff&act2=boost' class='btn btn-block btn-primary'>Enable Boost</a><br />
    							</div>
    							<div class='col-6 col-md-4 col-xl-6 col-xxxl-4'>
    								<a href='?action=staff&act2=upsword' class='btn btn-block btn-primary'>Upgrade Weapon</a><br />
    							</div>
    							<div class='col-6 col-md-4 col-xl-6 col-xxxl-4'>
    								<a href='?action=staff&act2=renamesword' class='btn btn-block btn-primary'>Rename Weapon</a><br />
    							</div>
                                <div class='col-12 col-md-6 col-xl-12 col-xxl-6 col-xxxl-4'>
    								<a href='?action=staff&act2=rsword' class='btn btn-block btn-primary'>Re-Roll Weapon Boost</a><br />
    							</div>
    							<div class='col-12 col-md-6 col-xl-12 col-xxl-6 col-xxxl-4'>
    								<a href='?action=staff&act2=picsword' class='btn btn-block btn-primary'>Change Weapon Image</a><br />
    							</div>
                                <div class='col-12 col-md-6 col-xl-12 col-xxl-6 col-xxxl-4'>
    								<a href='?action=staff&act2=guildassets' class='btn btn-block btn-primary'>Guild Assets</a><br />
    							</div>
                            </div>
						</div>
					</div>
                    <br />
				</div>";
		}
		if (isGuildAppManager())
		{
			echo "
				<div class='col-12 col-lg-6 col-xxxl-4'>
					<div class='card'>
						<div class='card-header'>
							<div class='row'>
								<div class='col'>
									Guild App Manager
								</div>
								<div class='col'>
									<a href='profile.php?user={$gd['guild_app_manager']}'>" . parseUsername($gd['guild_app_manager']) . "</a> [{$gd['guild_app_manager']}]
								</div>
							</div>
						</div>
						<div class='card-body'>
							<div class='row'>
                                <div class='col-6 col-md-4 col-lg-12 col-xl-6'>
								    <a href='?action=staff&act2=apps' class='btn btn-block btn-primary'>Applications</a><br />
                                </div>
    							<div class='col-6 col-md-4 col-lg-12 col-xl-6'>
    								<a href='?action=staff&act2=intromsg' class='btn btn-block btn-primary'>Intro Message</a><br />
    							</div>
    							<div class='col-6 col-md-4 col-lg-12 col-xl-6'>
    								<a href='?action=staff&act2=blockapps' class='btn btn-block btn-danger'>Block Applications</a><br />
    							</div>
    							<div class='col-6 col-md-4 col-lg-12 col-xl-6'>
    								<a href='?action=staff&act2=massmail' class='btn btn-block btn-primary'>Mass Mail Guild</a><br />
    							</div>
    							<div class='col-12 col-md-8 col-lg-12'>
    								<a href='?action=staff&act2=appmngr' class='btn btn-block btn-danger'>Change Application Manager</a><br />
    							</div>
                            </div>
						</div>
					</div>
                    <br />
				</div>";
		}
		if (isGuildVaultManager())
		{
			echo "
				<div class='col-12 col-lg-6 col-xxxl-4'>
					<div class='card'>
						<div class='card-header'>
							<div class='row'>
								<div class='col'>
									Guild Vault Manager
								</div>
								<div class='col'>
									<a href='profile.php?user={$gd['guild_vault_manager']}'>" . parseUsername($gd['guild_vault_manager']) . "</a> [{$gd['guild_vault_manager']}]
								</div>
							</div>
						</div>
						<div class='card-body'>
							<div class='row'>
                                <div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
								    <a href='?action=staff&act2=vault' class='btn btn-block btn-primary'>Vault Management</a><br />
                                </div>
    							<div class='col-12 col-sm-6 col-md-4 col-lg-12 col-xxxl-6'>
    								<a href='?action=staff&act2=armory' class='btn btn-block btn-primary'>Armory Management</a><br />
    							</div>
    							<div class='col-6 col-md-4 col-lg-12 col-xl-6'>
    								<a href='?action=staff&act2=masspay' class='btn btn-block btn-primary'>Mass Pay Guild</a><br />
    							</div>
    							<div class='col-6 col-md-4 col-lg-12 col-xl-6'>
    								<a href='?action=staff&act2=massmail' class='btn btn-block btn-primary'>Mass Mail Guild</a><br />
    							</div>
    							<div class='col-12 col-md-8 col-lg-12'>
    								<a href='?action=staff&act2=vaultmngr' class='btn btn-block btn-danger'>Change Vault Manager</a><br />
    							</div>
                            </div>
						</div>
					</div>
                    <br />
				</div>";
		}
		if (isGuildCrimeLord())
		{
			echo "
				<div class='col-12 col-xxxl-4'>
					<div class='card'>
						<div class='card-header'>
							<div class='row'>
								<div class='col'>
									Guild Crime Lord
								</div>
								<div class='col'>
									<a href='profile.php?user={$gd['guild_crime_lord']}'>" . parseUsername($gd['guild_crime_lord']) . "</a> [{$gd['guild_crime_lord']}]
								</div>
							</div>
						</div>
						<div class='card-body'>
							<div class='row'>
                                <div class='col-6 col-md-4 col-xxxl-6'>
								    <a href='?action=staff&act2=crimes' class='btn btn-block btn-primary'>Guild Crimes</a><br />
                                </div>
    							<div class='col-6 col-md-4 col-xxxl-6'>
    								<a href='?action=staff&act2=massmail' class='btn btn-block btn-primary'>Mass Mail Guild</a><br />
    							</div>
    							<div class='col-12 col-md-4 col-xxxl-12'>
    								<a href='?action=staff&act2=crimelord' class='btn btn-block btn-danger'>Change Crime Lord</a><br />
    							</div>
                            </div>
						</div>
					</div>
                    <br />
				</div>";
		}
	}
    echo "</div>
	<a href='viewguild.php' class='btn btn-primary btn-block'>Go Back</a>";
}

function add_poll()
{
	global $db, $h, $userid, $api, $ir;
	if (!isGuildLeadership())
	{
		alert('danger',"Uh Oh!","You do not have permission to be here.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
    if (isset($_POST['question'])) {
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_startpoll', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "We have blocked this action for your security. Please fill out the form quickly next time.");
            die($h->endpage());
        }
        $question = (isset($_POST['question'])) ? $db->escape(strip_tags(stripslashes($_POST['question']))) : '';
        $choice1 = (isset($_POST['choice1'])) ? $db->escape(strip_tags(stripslashes($_POST['choice1']))) : '';
        $choice2 = (isset($_POST['choice2'])) ? $db->escape(strip_tags(stripslashes($_POST['choice2']))) : '';
        $choice3 = (isset($_POST['choice3'])) ? $db->escape(strip_tags(stripslashes($_POST['choice3']))) : '';
        $choice4 = (isset($_POST['choice4'])) ? $db->escape(strip_tags(stripslashes($_POST['choice4']))) : '';
        $choice5 = (isset($_POST['choice5'])) ? $db->escape(strip_tags(stripslashes($_POST['choice5']))) : '';
        $choice6 = (isset($_POST['choice6'])) ? $db->escape(strip_tags(stripslashes($_POST['choice6']))) : '';
        $choice7 = (isset($_POST['choice7'])) ? $db->escape(strip_tags(stripslashes($_POST['choice7']))) : '';
        $choice8 = (isset($_POST['choice8'])) ? $db->escape(strip_tags(stripslashes($_POST['choice8']))) : '';
        $choice9 = (isset($_POST['choice9'])) ? $db->escape(strip_tags(stripslashes($_POST['choice9']))) : '';
        $choice10 = (isset($_POST['choice10'])) ? $db->escape(strip_tags(stripslashes($_POST['choice10']))) : '';
        $hidden = (isset($_POST['hidden']) && is_numeric($_POST['hidden'])) ? abs(intval($_POST['hidden'])) : '';
        if (empty($question) || empty($choice1) || empty($choice2)) {
            alert('danger', "Uh Oh!", "Please be sure to fill out the question, and two polling options. Thank you.");
            die($h->endpage());
        }
        $db->query("INSERT INTO `polls` (`active`, `question`, `choice1`,
					`choice2`, `choice3`,`choice4`, `choice5`, `choice6`, 
					`choice7`, `choice8`,`choice9`, `choice10`, `hidden`, 
					`visibility`)
                     VALUES
					 ('1', '$question', '$choice1', '$choice2',
                     '$choice3', '$choice4', '$choice5', '$choice6',
                     '$choice7', '$choice8', '$choice9' ,'$choice10',
                     '{$_POST['hidden']}', '{$ir['guild']}')");
        alert('success', "Success!", "You have successfully created a poll for your guild.", true, 'index.php');
        $q = $db->query("/*qc=on*/SELECT `userid`, `username` FROM `users` WHERE `guild` = {$ir['guild']}");
        while ($r = $db->fetch_row($q)) {
            notification_add($r['userid'], "Your guild has started a poll. Vote in it <a href='viewguild.php?action=viewpolls'>here</a>.");
        }
        die($h->endpage());
    } else {
        echo "Start a Poll";
        $csrf = request_csrf_html('staff_startpoll');
        echo "<hr />
		<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th width='33%'>
					Question
				</th>
				<td>
					<input type='text' required='1' class='form-control' name='question' />
				</td>
			</tr>
			<tr>
				<th>
					Choice 1
				</th>
				<td>
					<input type='text' required='1' class='form-control' name='choice1' />
				</td>
			</tr>
			<tr>
				<th>
					Choice 2
				</th>
				<td>
					<input type='text' required='1' class='form-control' name='choice2' />
				</td>
			</tr>
			<tr>
				<th>
					Choice 3
				</th>
				<td>
					<input type='text' class='form-control' name='choice3' />
				</td>
			</tr>
			<tr>
				<th>
					Choice 4
				</th>
				<td>
					<input type='text' class='form-control' name='choice4' />
				</td>
			</tr>
			<tr>
				<th>
					Choice 5
				</th>
				<td>
					<input type='text' class='form-control' name='choice5' />
				</td>
			</tr>
			<tr>
				<th>
					Choice 6
				</th>
				<td>
					<input type='text' class='form-control' name='choice6' />
				</td>
			</tr>
			<tr>
				<th>
					Choice 7
				</th>
				<td>
					<input type='text' class='form-control' name='choice7' />
				</td>
			</tr>
			<tr>
				<th>
					Choice 8
				</th>
				<td>
					<input type='text' class='form-control' name='choice8' />
				</td>
			</tr>
			<tr>
				<th>
					Choice 9
				</th>
				<td>
					<input type='text' class='form-control' name='choice9' />
				</td>
			</tr>
			<tr>
				<th>
					Choice 10
				</th>
				<td>
					<input type='text' class='form-control' name='choice10' />
				</td>
			</tr>
			<tr>
				<th>
					Hide results until poll is closed?
				</th>
				<td>
					<select name='hidden' class='form-control' type='dropdown'>
						<option value='0'>No</option>
						<option value='1'>Yes</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Create Poll'>
				</td>
			</tr>
		</table>
		{$csrf}
		</form>
		<a href='?action=staff&act2=idx'>Go Back</a>";
    }
}

function end_poll()
{
	global $db, $h, $api, $userid, $ir;
	if (!isGuildLeadership())
	{
		alert('danger',"Uh Oh!","You do not have permission to be here.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
    $_POST['poll'] = (isset($_POST['poll']) && is_numeric($_POST['poll'])) ? abs(intval($_POST['poll'])) : '';
    if (empty($_POST['poll'])) {
        $csrf = request_csrf_html('staff_endpoll');
        echo "
        Select the poll you wish to end.
        <br />
        <form method='post'>
           ";
        $q =
            $db->query(
                "/*qc=on*/SELECT `id`, `question`
                         FROM `polls`
                         WHERE `active` = '1' 
						 AND `visibility` = {$ir['guild']}");
        echo "<select name='poll' class='form-control' type='dropdown'>";
        while ($r = $db->fetch_row($q)) {
            echo "<option value='{$r['id']}'>Poll ID: {$r['id']} - {$r['question']}</option>";
        }
        $db->free_result($q);
        echo "</select>" . $csrf . "
			<br /><input type='submit' class='btn btn-primary' value='End Poll' />
		</form>
   		<a href='?action=staff&act2=idx'>Go Back</a>";
    } else {
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_endpoll', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "We have blocked this action for your security. Please fill out the form quickly next time.");
            die($h->endpage());
        }
        $q = $db->query("/*qc=on*/SELECT COUNT(`id`) FROM `polls` WHERE `id` = {$_POST['poll']} AND `visibility` = {$ir['guild']}");
        if ($db->fetch_single($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "This poll does not exist, and thus, cannot be ended.");
            die($h->endpage());
        }
        $db->free_result($q);
        $db->query("UPDATE `polls` SET `active` = '0' WHERE `id` = {$_POST['poll']}");
        alert('success', "Success!", "You have closed this poll to responses.", true, 'index.php');
        $q = $db->query("/*qc=on*/SELECT `userid`, `username` FROM `users` WHERE `guild` = {$ir['guild']}");
        while ($r = $db->fetch_row($q)) {
            notification_add($r['userid'], "Your guild has closed a poll. View the results <a href='viewguild.php?action=viewpolls'>here</a>.");
        }
        die($h->endpage());
    }
}

function staff_apps()
{
    global $db, $userid, $ir, $gd, $api, $h;
	if (!isGuildAppManager())
	{
		alert('danger',"Uh Oh!","You do not have permission to be here.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
    $_POST['app'] = (isset($_POST['app']) && is_numeric($_POST['app'])) ? abs(intval($_POST['app'])) : '';
    $what = (isset($_POST['what']) && in_array($_POST['what'], array('accept', 'decline'), true)) ? $_POST['what'] : '';
    if (!empty($_POST['app']) && !empty($what)) {

        //Verify that the CSRF check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_apps", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }

        //Verify the application exists and belongs to this guild.
        $aq = $db->query("/*qc=on*/SELECT `ga_user`
                         FROM `guild_applications`
                         WHERE `ga_id` = {$_POST['app']}
                         AND `ga_guild` = {$gd['guild_id']}");

        //Application does exist and belong to this guild.
        if ($db->num_rows($aq) > 0) {
            $appdata = $db->fetch_row($aq);

            //User declines the application. Delete the application, and alert the applicant they were declined.
            if ($what == 'decline') {
                $db->query("DELETE FROM `guild_applications` WHERE `ga_id` = {$_POST['app']}");
                $api->GameAddNotification($appdata['ga_user'], "We regret to inform you that your application to join the {$gd['guild_name']} guild was declined.");
                $event = "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has declined <a href='profile.php?user={$appdata['ga_user']}'> " . $api->SystemUserIDtoName($appdata['ga_user']) . "</a>'s  application to join the guild.";
                //Add to guild notifications.
                $api->GuildAddNotification($gd['guild_id'], $event);
                alert('success', "Success!", "You have denied " . $api->SystemUserIDtoName($appdata['ga_user']) . "'s application to join the guild.");
            } else {
                //User is accepted, yay!

                //Make sure the guild has enough capacity to accept this member.
                $cnt = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `guild` = {$gd['guild_id']}");
				$gd['guild_capacity']=$gd['guild_level']*5;
                //Guild does not have enough capacity to accept another member.s
                if ($gd['guild_capacity'] <= $db->fetch_single($cnt)) {
                    $db->free_result($cnt);
                    alert('danger', "Uh Oh!", "Your guild does not have the capacity for another member. Please level up your guild.");
                    die($h->endpage());

                    //Applicant has joined another guild. =/
                } else if ($api->UserInfoGet($appdata['ga_user'], 'guild') != 0) {
                    $db->free_result($cnt);
                    alert('danger', "Uh Oh!", "The applicant has already joined another guild.");
                    die($h->endpage());
                }

                //Select the town level if the guild's got one.
                $townlevel = $db->fetch_single($db->query("/*qc=on*/SELECT `town_min_level` FROM `town` WHERE `town_guild_owner` = {$gd['guild_id']}"));

                //Applicant cannot reach the town the guild owns.
                if ($townlevel > $api->UserInfoGet($appdata['ga_user'], 'level') && $townlevel > 0) {
                    alert('danger', "Uh Oh!", "The applicant cannot reach your guild's town because their level is too low.");
                    die($h->endpage());
                }
                $db->free_result($cnt);

                //Delete the application and put the applicant inside the guild! Woo!
                $db->query("DELETE FROM `guild_applications` WHERE `ga_id` = {$_POST['app']}");
                $api->GameAddNotification($appdata['ga_user'], "Your application to join the {$gd['guild_name']} guild was accepted.");
                $event = "<a href='profile.php?user={$userid}'>{$ir['username']}</a> 
									has accepted <a href='profile.php?user={$appdata['ga_user']}'>
									" . $api->SystemUserIDtoName($appdata['ga_user']) . "</a>'s 
									application to join the guild.";
                $api->GuildAddNotification($gd['guild_id'], $event);
				if (!empty($gd['guild_intromsg']))
				{
					$api->GameAddMail($appdata['ga_user'],"Your New Guild",$gd['guild_intromsg'],$userid);
				}
                $db->query("UPDATE `users` SET `guild` = {$gd['guild_id']} WHERE `userid` = {$appdata['ga_user']}");
                alert('success', "Success!", "You have accepted " . $api->SystemUserIDtoName($appdata['ga_user']) . "'s applicantion to join the guild.");
            }
        } else {
            alert('danger', "Uh Oh!", "You are trying to accept a non-existent application.");
        }
        $db->free_result($aq);
    } else {
        echo "
        <b>Application Management</b>
        <br />
        <table class='table table-bordered table-striped'>
        		<tr align='left'>
        			<th>Filing Time</th>
        			<th>Applicant</th>
					<th>Level</th>
        			<th>Application</th>
        			<th>Actions</th>
        		</tr>
   		";
        $q =
            $db->query(
                "/*qc=on*/SELECT *
                         FROM `guild_applications`
                         WHERE `ga_guild` = {$gd['guild_id']}
						 ORDER BY `ga_time` DESC");
        $csrf = request_csrf_html('guild_staff_apps');
        while ($r = $db->fetch_row($q)) {
            $r['ga_text'] = htmlentities($r['ga_text'], ENT_QUOTES, 'ISO-8859-1', false);
            echo "
            <tr align='left'>
            	<td>
					" . DateTime_Parse($r['ga_time']) . "
            	</td>
            	<td>
					<a href='profile.php?user={$r['ga_user']}'>" . $api->SystemUserIDtoName($r['ga_user']) . "</a>
            		[{$r['ga_user']}]
				</td>
            	<td>
					" . $api->UserInfoGet($r['ga_user'], 'level') . "
				</td>
				<td>
					{$r['ga_text']}
				</td>
            	<td>
            		<form action='?action=staff&act2=apps' method='post'>
            			<input type='hidden' name='app' value='{$r['ga_id']}' />
            			<input type='hidden' name='what' value='accept' />
            			{$csrf}
            			<input class='btn btn-success' type='submit' value='Accept' />
            		</form>
					<br />
            		<form action='?action=staff&act2=apps' method='post'>
            			<input type='hidden' name='app' value='{$r['ga_id']}' />
            			<input type='hidden' name='what' value='decline' />
            			{$csrf}
            			<input class='btn btn-danger' type='submit' value='Decline' />
            		</form>
            	</td>
            </tr>
               ";
        }
        echo "</table>
		<a href='?action=staff&act2=idx'>Go Back</a>";
    }
}

function staff_vault()
{
    global $db, $userid, $gd, $api, $h, $wq;
	if (!isGuildVaultManager())
	{
		alert('danger',"Uh Oh!","You do not have permission to be here.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
	if ($gd['guild_primcurr'] < 0)
	{
		alert('danger',"Uh Oh!","You cannot take money from your guild's vault until your debt is paid off.",true,"?action=staff&act2=idx");
		die($h->endpage());
	}
    if (isset($_POST['primary']) || isset($_POST['secondary'])) {

        //Verify CSRF check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_vault", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }
        //Make sure the POST is safe to work with.
        $_POST['primary'] = (isset($_POST['primary']) && is_numeric($_POST['primary'])) ? abs($_POST['primary']) : 0;
        $_POST['secondary'] = (isset($_POST['secondary']) && is_numeric($_POST['secondary'])) ? abs($_POST['secondary']) : 0;
        $_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;

        //Attempting to give more Copper Coins than the guild currently has.
        if ($_POST['primary'] > $gd['guild_primcurr']) {
            alert('danger', "Uh Oh!", "You are trying to give out more Copper Coins than your guild has in its vault.");
            die($h->endpage());
        }

        //Attempting to give more Chivalry Tokens than the guild currently has.
        if ($_POST['secondary'] > $gd['guild_seccurr']) {
            alert('danger', "Uh Oh!", "You are trying to give out more Chivalry Tokens than your guild has in its vault.");
            die($h->endpage());
        }

        //Didn't fill out how much currency they wanted to give out.
        if ($_POST['primary'] == 0 && $_POST['secondary'] == 0) {
            alert('danger', "Uh Oh!", "Please fill out the form completely before submitting.");
            die($h->endpage());
        }

        //Recipient is on the same IP Address as the sender... stop.
        if ($api->SystemCheckUsersIPs($userid, $_POST['user'])) {
            alert('danger', "Uh Oh!", "You cannot give from the guild's vault if you share the same IP address as the recipient.");
            die($h->endpage());
        }

        //Check that the user to receive the cash is in the guild and/or exists.
        $q = $db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `userid` = {$_POST['user']} AND `guild` = {$gd['guild_id']}");
        if ($db->num_rows($q) == 0) {
            alert('danger', "Uh Oh!", "You are trying to give to a user that does not exist, or is not in the guild.");
            die($h->endpage());
        }

        //Do not allow the transaction to continue if at war.
        if ($db->fetch_single($wq) > 0) {
            alert('danger', "Uh Oh!", "You cannot withdraw from your guild's vault while at war.");
            die($h->endpage());
        }
        $db->free_result($q);
        //Give the currency and log everything.
        $api->UserGiveCurrency($_POST['user'], 'primary', $_POST['primary']);
        $api->UserGiveCurrency($_POST['user'], 'secondary', $_POST['secondary']);
        $db->query("UPDATE `guild` SET `guild_primcurr` = `guild_primcurr` - {$_POST['primary']},
                      `guild_seccurr` = `guild_seccurr` - {$_POST['secondary']} WHERE `guild_id` = {$gd['guild_id']}");
        $api->GameAddNotification($_POST['user'], "You were given " . shortNumberParse($_POST['primary']) . " Copper Coins 
         and " . shortNumberParse($_POST['secondary']) . " Chivalry Tokens from your guild's vault.");
        $api->GuildAddNotification($gd['guild_id'], "<a href='profile.php?user={$userid}'>
            {$api->SystemUserIDtoName($userid)}</a> has given <a href='profile.php?user={$_POST['user']}'>
            {$api->SystemUserIDtoName($_POST['user'])}</a> " . shortNumberParse($_POST['primary']) . "
            Copper Coins and/or " . shortNumberParse($_POST['secondary']) . " Chivalry Tokens from the guild's
            vault.");
        alert('success', "Success!", "You have given {$api->SystemUserIDtoName($_POST['user'])} " . shortNumberParse($_POST['primary']) . " Copper Coins 
         and " . shortNumberParse($_POST['secondary']) . " Chivalry Tokens from your guild's vault.", true, '?action=staff&act2=idx');
        $api->SystemLogsAdd($userid, "guild_vault", "Gave <a href='profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a> " . shortNumberParse($_POST['primary']) . " Copper Coins and/or " . shortNumberParse($_POST['secondary']) . " Chivalry Tokens from their guild's vault.");
    } else {
        $csrf = request_csrf_html('guild_staff_vault');
        echo "<form method='post'>
        <table class='table table-bordered'>
            <tr>
                <th colspan='2'>
                    You may give out currency from your guild's vault. Your vault currently has " . shortNumberParse($gd['guild_primcurr']) . " Copper Coins and
                    " . shortNumberParse($gd['guild_seccurr']) . " Chivalry Tokens.
                </th>
            </tr>
            <tr>
                <th>
                    User
                </th>
                <td>
                    " . guild_user_dropdown('user', $gd['guild_id']) . "
                </td>
            </tr>
            <tr>
                <th>
                    Copper Coins
                </th>
                <td>
                    <input type='number' class='form-control' min='0' max='{$gd['guild_primcurr']}' name='primary'>
                </td>
            </tr>
            <tr>
                <th>
                    Chivalry Tokens
                </th>
                <td>
                    <input type='number' class='form-control' min='0' max='{$gd['guild_seccurr']}' name='secondary'>
                </td>
            </tr>
            <tr>
                <td colspan='2'>
                    <input type='submit' class='btn btn-primary' value='Give'>
                </td>
            </tr>
            {$csrf}
        </table>
        </form>
		<a href='?action=staff&act2=idx'>Go Back</a>";
    }

}

function staff_coowner()
{
    global $db, $userid, $api, $h, $gd;
	if (!isGuildCoLeader())
	{
		alert('danger',"Uh Oh!","You do not have permission to be here.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
    if (isset($_POST['user'])) {

        //Verify CSRF check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_coleader", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }

        //Make sure POST is safe to work with.
        $_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;

        //Verify the user chosen is existent and is in the guild.
        $q = $db->query("/*qc=on*/SELECT `userid`, `username` FROM `users` WHERE `userid` = {$_POST['user']} AND `guild` = {$gd['guild_id']}");
        if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "You cannot give co-leadership abilities to someone that does not exist, or is not
			    in the guild.");
            die($h->endpage());
        }
        $db->free_result($q);

        //Update the guild's leader.
        $db->query("UPDATE `guild` SET `guild_coowner` = {$_POST['user']} WHERE `guild_id` = {$gd['guild_id']}");
        $api->GameAddNotification($_POST['user'], "<a href='profile.php?user={$userid}'>{$api->SystemUserIDtoName($userid)}</a> has transferred you co-leader privileges for the {$gd['guild_name']} guild.");
        $api->GuildAddNotification($gd['guild_id'], "<a href='profile.php?user={$userid}'>{$api->SystemUserIDtoName($userid)}</a> has transferred co-leader privileges to <a href='profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a>.");
        alert('success', "Success!", "You have successfully transferred co-leadership privileges to {$api->SystemUserIDtoName($_POST['user'])}.", true, '?action=staff&act2=idx');
    } else {
        $csrf = request_csrf_html('guild_staff_coleader');
        echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Select the user you wish to give your co-leadership privileges to.
				</th>
			</tr>
			<tr>
				<th>
					User
				</th>
				<td>
					" . guild_user_dropdown('user', $gd['guild_id'], $gd['guild_coowner']) . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Transfer Co-Leader'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>
		<a href='?action=staff&act2=idx'>Go Back</a>";
    }
}

function staff_app_manager()
{
    global $db, $userid, $api, $h, $gd;
	if (!isGuildAppManager())
	{
		alert('danger',"Uh Oh!","You do not have permission to be here.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
    if (isset($_POST['user'])) {

        //Verify CSRF check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_appmngr", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }

        //Make sure POST is safe to work with.
        $_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;

        //Verify the user chosen is existent and is in the guild.
        $q = $db->query("/*qc=on*/SELECT `userid`, `username` FROM `users` WHERE `userid` = {$_POST['user']} AND `guild` = {$gd['guild_id']}");
        if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "You cannot give application manager abilities to someone that does not exist, or is not
			    in the guild.");
            die($h->endpage());
        }
        $db->free_result($q);

        //Update the guild's leader.
        $db->query("UPDATE `guild` SET `guild_app_manager` = {$_POST['user']} WHERE `guild_id` = {$gd['guild_id']}");
        $api->GameAddNotification($_POST['user'], "<a href='profile.php?user={$userid}'>{$api->SystemUserIDtoName($userid)}</a> has transferred you application manager privileges for the {$gd['guild_name']} guild.");
        $api->GuildAddNotification($gd['guild_id'], "<a href='profile.php?user={$userid}'>{$api->SystemUserIDtoName($userid)}</a> has transferred application manager privileges to <a href='profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a>.");
        alert('success', "Success!", "You have successfully transferred application manager privileges to {$api->SystemUserIDtoName($_POST['user'])}.", true, '?action=staff&act2=idx');
    } else {
        $csrf = request_csrf_html('guild_staff_appmngr');
        echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Select the user you wish to give your application manager privileges to.
				</th>
			</tr>
			<tr>
				<th>
					User
				</th>
				<td>
					" . guild_user_dropdown('user', $gd['guild_id'], $gd['guild_app_manager']) . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Transfer Application Manager'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>
		<a href='?action=staff&act2=idx'>Go Back</a>";
    }
}

function staff_crime_lord()
{
    global $db, $userid, $api, $h, $gd;
	if (!isGuildCrimeLord())
	{
		alert('danger',"Uh Oh!","You do not have permission to be here.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
    if (isset($_POST['user'])) {

        //Verify CSRF check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_crime_lord", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }

        //Make sure POST is safe to work with.
        $_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;

        //Verify the user chosen is existent and is in the guild.
        $q = $db->query("/*qc=on*/SELECT `userid`, `username` FROM `users` WHERE `userid` = {$_POST['user']} AND `guild` = {$gd['guild_id']}");
        if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "You cannot give crime lord abilities to someone that does not exist, or is not
			    in the guild.");
            die($h->endpage());
        }
        $db->free_result($q);

        //Update the guild's leader.
        $db->query("UPDATE `guild` SET `guild_crime_lord` = {$_POST['user']} WHERE `guild_id` = {$gd['guild_id']}");
        $api->GameAddNotification($_POST['user'], "<a href='profile.php?user={$userid}'>{$api->SystemUserIDtoName($userid)}</a> has transferred you crime lord privileges for the {$gd['guild_name']} guild.");
        $api->GuildAddNotification($gd['guild_id'], "<a href='profile.php?user={$userid}'>{$api->SystemUserIDtoName($userid)}</a> has transferred crime lord privileges to <a href='profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a>.");
        alert('success', "Success!", "You have successfully transferred crime lord privileges to {$api->SystemUserIDtoName($_POST['user'])}.", true, '?action=staff&act2=idx');
    } else {
        $csrf = request_csrf_html('guild_staff_crime_lord');
        echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Select the user you wish to give your crime lord privileges to.
				</th>
			</tr>
			<tr>
				<th>
					User
				</th>
				<td>
					" . guild_user_dropdown('user', $gd['guild_id'], $gd['guild_crime_lord']) . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Transfer Crime Lord'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>
		<a href='?action=staff&act2=idx'>Go Back</a>";
    }
}

function staff_vault_manager()
{
    global $db, $userid, $api, $h, $gd;
	if (!isGuildVaultManager())
	{
		alert('danger',"Uh Oh!","You do not have permission to be here.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
    if (isset($_POST['user'])) {

        //Verify CSRF check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_vaultmngr", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }

        //Make sure POST is safe to work with.
        $_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;

        //Verify the user chosen is existent and is in the guild.
        $q = $db->query("/*qc=on*/SELECT `userid`, `username` FROM `users` WHERE `userid` = {$_POST['user']} AND `guild` = {$gd['guild_id']}");
        if ($db->num_rows($q) == 0) {
            $db->free_result($q);
            alert('danger', "Uh Oh!", "You cannot give vault manager abilities to someone that does not exist, or is not
			    in the guild.");
            die($h->endpage());
        }
        $db->free_result($q);

        //Update the guild's leader.
        $db->query("UPDATE `guild` SET `guild_vault_manager` = {$_POST['user']} WHERE `guild_id` = {$gd['guild_id']}");
        $api->GameAddNotification($_POST['user'], "<a href='profile.php?user={$userid}'>{$api->SystemUserIDtoName($userid)}</a> has transferred you vault manager privileges for the {$gd['guild_name']} guild.");
        $api->GuildAddNotification($gd['guild_id'], "<a href='profile.php?user={$userid}'>{$api->SystemUserIDtoName($userid)}</a> has transferred vault manager privileges to <a href='profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a>.");
        alert('success', "Success!", "You have successfully transferred vault manager privileges to {$api->SystemUserIDtoName($_POST['user'])}.", true, '?action=staff&act2=idx');
    } else {
        $csrf = request_csrf_html('guild_staff_vaultmngr');
        echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Select the user you wish to give your vault manager privileges to.
				</th>
			</tr>
			<tr>
				<th>
					User
				</th>
				<td>
					" . guild_user_dropdown('user', $gd['guild_id'], $gd['guild_vault_manager']) . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Transfer Vault Manager'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>
		<a href='?action=staff&act2=idx'>Go Back</a>";
    }
}

function staff_announcement()
{
    global $gd, $db, $h;
	if (!isGuildLeadership())
	{
		alert('danger',"Uh Oh!","You do not have permission to be here.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
    if (isset($_POST['ament'])) {

        //Verify CSRF check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_ament", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }

        //Make sure the POST is safe to work with.
        $ament = $db->escape(nl2br(htmlentities(stripslashes($_POST['ament']), ENT_QUOTES, 'ISO-8859-1')));

        //Update the guild's announcement.
        $db->query("UPDATE `guild` SET `guild_announcement` = '{$ament}' WHERE `guild_id` = {$gd['guild_id']}");
        alert('success', "Success!", "You have updated your guild's announcement.", true, '?action=staff&act2=idx');
    } else {
        //Escape the announcement for safety reasons.
        $am_for_area = strip_tags($gd['guild_announcement']);
        $csrf = request_csrf_html('guild_staff_ament');
        echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					You may change your guild's announcement here.
				</th>
			</tr>
			<tr>
				<th>
					Announcement
				</th>
				<td>
					<textarea class='form-control' name='ament'>{$am_for_area}</textarea>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='Change Announcement' class='btn btn-primary'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>
		<a href='?action=staff&act2=idx'>Go Back</a>";
    }
}

function staff_massmail()
{
    global $db, $api, $userid, $h, $gd;
	if (!isGuildStaff())
	{
		alert('danger',"Uh Oh!","You do not have permission to be here.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
    if (isset($_POST['text'])) {

        //Verify the CSRF check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_massmail", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }

        //Escape the message.
        $_POST['text'] = (isset($_POST['text'])) ? $db->escape(htmlentities(stripslashes($_POST['text']), ENT_QUOTES, 'ISO-8859-1')) : '';
        $subj = 'Guild Mass Mail';
        $q = $db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `guild` = {$gd['guild_id']}");
        //Send the mail out to everyone in the guild.
        while ($r = $db->fetch_row($q)) {
            $api->GameAddMail($r['userid'], $subj, $_POST['text'], $userid);
        }
        alert('success', "Success!", "Mass mail has been sent successfully.", true, '?action=staff&act2=idx');
    } else {
        $csrf = request_csrf_html('guild_staff_massmail');
        echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Use this form to send a mass mail to each member of your guild.
				</th>
			</tr>
			<tr>
				<th>
					Message
				</th>
				<td>
					<textarea class='form-control' name='text' rows='7'></textarea>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='Mass Mail' class='btn btn-primary'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>
		<a href='?action=staff&act2=idx'>Go Back</a>";
    }
}

function staff_masspayment()
{
    global $db, $api, $userid, $gd, $h, $wq;
	if (!isGuildVaultManager())
	{
		alert('danger',"Uh Oh!","You do not have permission to be here.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
	if ($gd['guild_primcurr'] < 0)
	{
		alert('danger',"Uh Oh!","You cannot take money from your guild's vault until your debt is paid off.",true,"?action=staff&act2=idx");
		die($h->endpage());
	}
    if (isset($_POST['payment'])) {

        //Verify the CSRF check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_masspay", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }

        //Make sure the POST is safe to work with.
        $_POST['payment'] = (isset($_POST['payment']) && is_numeric($_POST['payment'])) ? abs($_POST['payment']) : 0;
        $cnt = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `guild` = {$gd['guild_id']}"));

        //Make sure there's enough Copper Coins to pay each member of the guild the amount specified.
        if (($_POST['payment'] * $cnt) > $gd['guild_primcurr']) {
            alert('danger', "Uh Oh!", "You do not have enough currency in your vault to give out that much to each member.");
            die($h->endpage());

            //Check that the guild is not at war.
        } else if ($db->fetch_single($wq) > 0) {
            alert('danger', "Uh Oh!", "You cannot mass pay your guild while at war.");
            die($h->endpage());
        } else {
            $q = $db->query("/*qc=on*/SELECT `userid`, `username` FROM `users` WHERE `guild` = {$gd['guild_id']}");
            //Pay each member.
            while ($r = $db->fetch_row($q)) {
                //User shares an IP with the user being paid... stop this.
                if ($api->SystemCheckUsersIPs($userid, $r['userid'])) {
                    alert('danger', "Uh Oh!", "{$r['username']} could not receive their Mass Payment because they share an IP Address with you.");
                } else {
                    //Pay everyone.
                    $gd['guild_primcurr'] -= $_POST['payment'];
                    $api->GameAddNotification($r['userid'], "You were given a mass-payment of " . shortNumberParse($_POST['payment']) . " Copper Coins from your guild.");
                    $api->UserGiveCurrency($r['userid'], 'primary', $_POST['payment']);
                    alert('success', "Success!", "{$r['username']} was paid " . shortNumberParse($_POST['payment']) . " Copper Coins.");
                }
            }
            //Notify the user of the success and log everything.
            $db->query("UPDATE `guild` SET `guild_primcurr` = {$gd['guild_primcurr']} WHERE `guild_id` = {$gd['guild_id']}");
            $notif = $db->escape("A mass payment of " . shortNumberParse($_POST['payment']) . " Copper Coins was sent out to the members of the guild.");
            $api->GuildAddNotification($gd['guild_id'], $notif);
            $api->SystemLogsAdd($userid, 'guilds', "Sent a mass payment of " . shortNumberParse($_POST['payment']) . "to their guild.");
            alert('success', "Success!", "Mass payment complete.", true, '?action=staff&act2=idx');
        }
    } else {
        $csrf = request_csrf_html('guild_staff_masspay');
        echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					You can pay each and every member of your guild using this form
				</th>
			</tr>
			<tr>
				<th>
					Payment
				</th>
				<td>
					<input type='number' min='1' max='{$gd['guild_primcurr']}' class='form-control' name='payment'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='Mass Pay' class='btn btn-primary'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>
		<a href='?action=staff&act2=idx'>Go Back</a>";
    }
}

function staff_desc()
{
    global $gd, $db, $userid, $h;
    //Verify the current user is the guild owner.
    if (isGuildLeader()) {
        if (isset($_POST['desc'])) {

            //Verify CSRF check has passed.
            if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_desc", stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
                die($h->endpage());
            }

            //Make sure the POST is safe to work with.
            $desc = $db->escape(nl2br(htmlentities(stripslashes($_POST['desc']), ENT_QUOTES, 'ISO-8859-1')));

            //Update guild's description.
            $db->query("UPDATE `guild` SET `guild_desc` = '{$desc}' WHERE `guild_id` = {$gd['guild_id']}");
            alert('success', "Success!", "You have updated your guild's description", true, '?action=staff&act2=idx');
        } else {
            //Escape the description for safety reasons.
            $am_for_area = strip_tags($gd['guild_desc']);
            $csrf = request_csrf_html('guild_staff_desc');
            echo "<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						You can use this form to change your guild's description.
					</th>
				</tr>
				<tr>
					<th>
						Description
					</th>
					<td>
						<textarea class='form-control' name='desc' rows='7'>{$am_for_area}</textarea>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' value='Update Description' class='btn btn-primary'>
					</td>
				</tr>
				{$csrf}
			</table>
			</form>
			<a href='?action=staff&act2=idx'>Go Back</a>";
        }
    } else {
        alert('danger', "Uh Oh!", "You can only be here if you're the guild's leader.");
    }
}

function staff_leader()
{
    global $gd, $db, $userid, $api, $h;
    //Verify the current user is the guild owner.
    if (isGuildLeader()) {
        if (isset($_POST['user'])) {

            //Verify CSRF check has passed.
            if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_leader", stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
                die($h->endpage());
            }

            //Make the POST safe to work with it.
            $_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;

            //Select the user from database.
            $q = $db->query("/*qc=on*/SELECT `userid`, `username` FROM `users` WHERE `userid` = {$_POST['user']} AND `guild` = {$gd['guild_id']}");

            //User does not exist, or is not in the guild.
            if ($db->num_rows($q) == 0) {
                $db->free_result($q);
                alert('danger', "Uh Oh!", "You cannot give leadership privileges to a user not in your guild, or that doesn't exist.");
                die($h->endpage());
            }
            $db->free_result($q);

            //Update the guild's leader and log everything.
            $db->query("UPDATE `guild` SET `guild_owner` = {$_POST['user']} WHERE `guild_id` = {$gd['guild_id']}");
            $api->GameAddNotification($_POST['user'], "<a href='profile.php?user={$userid}'>{$api->SystemUserIDtoName($userid)}</a> has transferred you leader privileges for the {$gd['guild_name']} guild.");
            $api->GuildAddNotification($gd['guild_id'], "<a href='profile.php?user={$userid}'>{$api->SystemUserIDtoName($userid)}</a> has transferred leader privileges to <a href='profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a>.");
            alert('success', "Success!", "You have transferred your leadership privileges over to {$api->SystemUserIDtoName($_POST['user'])}.", true, '?action=staff&act2=idx');
        } else {
            $csrf = request_csrf_html('guild_staff_leader');
            echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Select a user to give them your leadership privileges.
				</th>
			</tr>
			<tr>
				<th>
					User
				</th>
				<td>
					" . guild_user_dropdown('user', $gd['guild_id'], $gd['guild_owner']) . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Transfer Leader'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>
		<a href='?action=staff&act2=idx'>Go Back</a>";
        }
    } else {
        alert('danger', "Uh Oh!", "You can only be here if you're the guild's leader.", true, '?action=staff&act2=idx');
    }
}

function staff_name()
{
    global $gd, $db, $userid, $h;

    //Verify the current user is the guild owner.
    if (isGuildLeader()) {
        if (isset($_POST['name'])) {

            //Check that the CSRF check has passed.
            if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_name", stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
                die($h->endpage());
            }
            //Make sure the POST is safe to work with.
            $name = $db->escape(nl2br(htmlentities(stripslashes($_POST['name']), ENT_QUOTES, 'ISO-8859-1')));

            //Select guilds with the same name.
            $cnt = $db->query("/*qc=on*/SELECT `guild_id` FROM `guild` WHERE `guild_name` = '{$name}' AND `guild_id` != {$gd['guild_id']}");

            //If there's a guild with the same name, disallow the name change.
            if ($db->num_rows($cnt) > 0) {
                alert('danger', "Uh Oh!", "The name you have chosen is already in use by another guild.");
                die($h->endpage());
            }

            //Update the guild's name.
            $db->query("UPDATE `guild` SET `guild_name` = '{$name}' WHERE `guild_id` = {$gd['guild_id']}");
            alert('success', "Success!", "You have changed your guild's name to {$name}.", true, '?action=staff&act2=idx');
        } else {
            $am_for_area = strip_tags($gd['guild_name']);
            $csrf = request_csrf_html('guild_staff_name');
            echo "<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th colspan='2'>
						You can change your guild's name here.
					</th>
				</tr>
				<tr>
					<th>
						Name
					</th>
					<td>
						<input type='text' required='1' value='{$am_for_area}' class='form-control' name='name'>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' value='Change Guild Name' class='btn btn-primary'>
					</td>
				</tr>
				{$csrf}
			</table>
			</form>
			<a href='?action=staff&act2=idx'>Go Back</a>";
        }
    } else {
        alert('danger', "Uh Oh!", "You can only be here if you're the guild's leader.", true, '?action=staff&act2=idx');
    }
}

function staff_town()
{
    global $db, $gd, $api, $h, $userid, $wq;

    //Verify current user is the guild owner.
    if (isGuildLeader()) {
        if (isset($_POST['town'])) {

            //Verify CSRF
            if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_town", stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
                die($h->endpage());
            }
            //Make sure POST is safe to work with.
            $town = (isset($_POST['town']) && is_numeric($_POST['town'])) ? abs($_POST['town']) : 0;

            //Make sure current guild doesn't already have a town.
            $cnt = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`town_id`) FROM `town` WHERE `town_guild_owner` = {$gd['guild_id']}"));
            if ($cnt > 0) {
                alert('danger', "Uh Oh!", "Your guild already owns a town. Surrender your current town to own a new one.");
                die($h->endpage());
            }

            //Make sure town claimed exists.
            if ($db->num_rows($db->query("/*qc=on*/SELECT `town_id` FROM `town` WHERE `town_id` = {$town}")) == 0) {
                alert('danger', "Uh Oh!", "The town you wish to own does not exist.");
                die($h->endpage());
            }

            //Check to see if the town is unowned.
            if ($db->fetch_single($db->query("/*qc=on*/SELECT `town_guild_owner` FROM `town` WHERE `town_id` = {$town}")) > 0) {
                alert('danger', "Uh Oh!", "The town you wish to own is already owned by another guild. If you want this town, declare war on them!");
                die($h->endpage());
            }

            //Check to see if current guild is at war, if so, stop them.
            if ($db->fetch_single($wq) > 0) {
                alert('danger', "Uh Oh!", "You may not change your guild's town while at war.");
                die($h->endpage());
            }
            $lowestlevel = $db->fetch_single($db->query("/*qc=on*/SELECT `level` FROM `users` WHERE `guild` = {$gd['guild_id']} ORDER BY `level` ASC LIMIT 1"));
            $townlevel = $db->fetch_single($db->query("/*qc=on*/SELECT `town_min_level` FROM `town` WHERE `town_id` = {$town}"));

            //Verify that everyone in the guild can reach the city.
            if ($townlevel > $lowestlevel) {
                alert('danger', "Uh Oh!", "You cannot own this town as there are members in your guild who cannot access it.");
                die($h->endpage());
            }

            //Update everything. City is now the guild's.
            $db->query("UPDATE `town` SET `town_guild_owner` = {$gd['guild_id']} WHERE `town_id` = {$town}");
            $api->GuildAddNotification($gd['guild_id'], "Your guild has successfully claimed {$api->SystemTownIDtoName($town)}.");
            alert('success', "Success!", "You have successfully claimed {$api->SystemTownIDtoName($town)} for your guild.", true, '?action=staff&act2=idx');
        } else {
            $csrf = request_csrf_html('guild_staff_town');
            echo "
			<form method='post'>
				<table class='table table-bordered'>
					<tr>
						<th colspan='2'>
							You can claim a town for your guild here. This town must be unowned, and must be accessible
							to all your guild members. If it is currently owned, you must declare war on the owning
							guild to get a chance to claim the town as yours.
						</th>
					</tr>
					<tr>
						<th>
							Town
						</th>
						<td>
							" . location_dropdown('town') . "
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<input type='submit' value='Claim Town' class='btn btn-primary'>
						</td>
					</tr>
					{$csrf}
				</table>
			</form>
			<a href='?action=staff&act2=idx'>Go Back</a>";
        }
    } else {
        alert('danger', "Uh Oh!", "You can only be here if you're the guild's leader.", true, '?action=staff&act2=idx');
    }
}

function staff_untown()
{
    global $db, $gd, $api, $h, $userid, $wq;
    //Verify current user is the guild's owner.
    if (isGuildLeader()) {

        //Check to be sure the guild has a town under their control
        $townowned = $db->query("/*qc=on*/SELECT `town_id` FROM `town` WHERE `town_guild_owner` = {$gd['guild_id']}");
        if ($db->num_rows($townowned) == 0) {
            alert('danger', "Uh Oh!", "Your guild doesn't have a town to surrender.", true, '?action=staff&act2=idx');
            die($h->endpage());

            //Check that the guild is not at war.
        } else if ($db->fetch_single($wq) > 0) {
            alert('danger', "Uh Oh!", "You cannot surrender your town while at war.", true, '?action=staff&act2=idx');
            die($h->endpage());
        } elseif (isset($_POST['confirm'])) {

            //Surrender the town.
            $r = $db->fetch_single($townowned);
            alert('success', "Success!", "You have surrendered your guild's town.", true, '?action=staff&act2=idx');
            $db->query("UPDATE `town`
                        SET `town_guild_owner` = 0, `town_tax` = 0 
                        WHERE `town_id` = {$r}");
            $api->GuildAddNotification($gd['guild_id'], "Your guild has willingly given up their town.");
            $api->SystemLogsAdd($userid, 'guilds', "Willingly surrendered {$gd['guild_name']}'s town, {$api->SystemTownIDtoName($r)}.");
        } else {
            echo "Are you sure you wish to surrender your guild's town? This is not reversible.<br />
			<form method='post'>
				<input type='hidden' name='confirm' value='yes'>
				<input type='submit' class='btn btn-success' value='Yes'>
			</form>
			<a href='?action=staff&act2=idx'>Go Back</a>";
        }
    } else {
        alert('danger', "Uh Oh!", "You can only be here if you're the guild's leader.", true, '?action=staff&act2=idx');
    }
}

function staff_declare()
{
    global $db, $gd, $api, $h, $userid, $ir;
    //Verify current user is the guild owner.
    if (isGuildLeader()) {
        if (isset($_POST['guild'])) {

            //Verify POST is safe to work with.
            $_POST['guild'] = (isset($_POST['guild']) && is_numeric($_POST['guild'])) ? abs($_POST['guild']) : 0;

            //Verify CSRF check has passed.
            if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_declarewar", stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
                die($h->endpage());
            }
			
			if ($gd['guild_primcurr'] < 500000)
			{
				alert('danger',"Uh Oh!","Your guild does not have enough Copper Coins to declare war.");
				die($h->endpage());
			}

            //Check if the declared guild is the current guild, and stop them if that's the case.
            if ($_POST['guild'] == $gd['guild_id']) {
                alert('danger', "Uh Oh!", "You cannot declare war on your own guild.");
                die($h->endpage());
            }
			
			if ($_POST['guild'] == 1) {
				alert('danger', "Uh Oh!", "You cannot declare war on the admin guild.");
				die($h->endpage());
			}

            //Verify that the declared guild exists.
            $data_q = $db->query("/*qc=on*/SELECT `guild_name`,`guild_owner`
                                  FROM `guild`
                                  WHERE `guild_id` = {$_POST['guild']}");
            if ($db->num_rows($data_q) == 0) {
                $db->free_result($data_q);
                alert('danger', "Uh Oh!", "You cannot declare war on a non-existent guild.");
                die($h->endpage());
            }

            //Make sure the two guilds are not at war already.
            $time = time();
            $iswarredon = $db->query("/*qc=on*/SELECT `gw_id`
                                      FROM `guild_wars`
                                      WHERE `gw_declarer` = {$gd['guild_id']}
                                      AND `gw_declaree` = {$_POST['guild']}
                                      AND `gw_end` > {$time}");
            if ($db->num_rows($iswarredon) > 0) {
                alert('danger', "Uh Oh!", "You cannot declare war on this guild as your guild has already begun war with them.");
                die($h->endpage());
            }

            //Make sure the two guilds are not at war already.
            $iswarredon1 = $db->query("/*qc=on*/SELECT `gw_id`
                                        FROM `guild_wars`
                                        WHERE `gw_declaree` = {$gd['guild_id']}
                                        AND `gw_declarer` = {$_POST['guild']}
                                        AND `gw_end` > {$time}");
            if ($db->num_rows($iswarredon1) > 0) {
                alert('danger', "Uh Oh!", "You cannot declare war on this guild as they've already started war with your guild!");
                die($h->endpage());
            }

            //Check to see if its been a week since the last war.
            $lastweek = $time - 604800;
            $istoosoon = $db->fetch_single($db->query("/*qc=on*/SELECT `gw_end`
                                                        FROM `guild_wars`
                                                        WHERE `gw_declarer` = {$gd['guild_id']}
                                                        AND `gw_declaree` = {$_POST['guild']}
                                                        ORDER BY `gw_id`DESC
                                                        LIMIT 1"));
            if ($istoosoon > $lastweek) {
                alert('danger', "Uh Oh!", "You must wait at least one week before declaring war on this guild again.");
                die($h->endpage());
            }

            //Check to see if its been a week since the last war.
            $istoosoon1 = $db->fetch_single($db->query("/*qc=on*/SELECT `gw_end`
                                                        FROM `guild_wars`
                                                        WHERE `gw_declaree` = {$gd['guild_id']}
                                                        AND `gw_declarer` = {$_POST['guild']}
                                                        ORDER BY `gw_id` DESC
                                                        LIMIT 1"));
            if ($istoosoon1 > $lastweek) {
                alert('danger', "Uh Oh!", "You must wait at least one week before declaring war on this guild again.");
                die($h->endpage());
            }
            $yourcount = $db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `guild` = {$ir['guild']}");
            $theircount = $db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `guild` = {$_POST['guild']}");

            //Current guild does not have 5 members.
            if ($db->num_rows($yourcount) < 2) {
                alert('danger', "Uh Oh!", "You cannot declare war on another guild if you've got less than 2 members in your own guild.");
                die($h->endpage());
            }

            //Current guild does not have 5 members.
            if ($db->num_rows($theircount) < 2) {
                alert('danger', "Uh Oh!", "You cannot declare war on this guild, as they do not have 2 members currently in their guild.");
                die($h->endpage());
            }
			
			//Are you guys allies?
			$cfaq=$db->query("/*qc=on*/SELECT * 
								FROM `guild_alliances` 
								WHERE 
								(`alliance_a` = {$ir['guild']} AND `alliance_b` = {$_POST['guild']}) 
								OR 
								(`alliance_b` = {$ir['guild']} AND `alliance_a` = {$_POST['guild']})");
			if ($db->num_rows($cfaq) != 0)
			{
				alert('danger',"Uh Oh!","You cannot declare war on an allied guild.");
				die($h->endpage());
			}
            $r = $db->fetch_row($data_q);
            $endtime = time() + (86400 * 7);

            //Start the war, and notify all parties involved.
            $db->query("INSERT INTO `guild_wars` VALUES (NULL, {$gd['guild_id']}, {$_POST['guild']}, 0, 0, {$endtime}, 0)");
            $api->GameAddNotification($r['guild_owner'], "The {$gd['guild_name']} guild has declared war on your guild.");
            $api->GuildAddNotification($_POST['guild'], "The {$gd['guild_name']} guild has declared war on your guild.");
            $api->GuildAddNotification($gd['guild_id'], "Your guild has declared war on {$r['guild_name']}.");
			
			$allyq=$db->query("/*qc=on*/SELECT * FROM `guild_alliances` WHERE (`alliance_a` = {$ir['guild']} OR `alliance_b` = {$ir['guild']})");
			while ($ar=$db->fetch_row($allyq))
			{
				if ($ar['alliance_a'] == $ir['guild'])
					$otheralliance=$ar['alliance_b'];
				else
					$otheralliance=$ar['alliance_a'];
				$allyName = $api->GuildFetchInfo($otheralliance, 'guild_name');
				if ($ar['alliance_type'] == 2)
				{
					$api->GuildAddNotification($ir['guild'],"Your guild has broken the alliance with {$allyName} by declaring war.");
					$api->GuildAddNotification($otheralliance,"{$gd['guild_name']} has broken the alliance with your guild by declaring war.");
					$db->query("DELETE FROM `guild_alliances` WHERE `alliance_id` = {$ar['alliance_id']}");
					guildSendLeadersNotif($otheralliance, "{$gd['guild_name']} has broken the alliance with your guild by declaring war on a 3rd party.");
				}
				else
				{
				    $db->query("INSERT INTO `guild_wars` VALUES (NULL, {$otheralliance}, {$_POST['guild']}, 0, 0, {$endtime}, 0)");
				    
					$api->GuildAddNotification($otheralliance,"{$gd['guild_name']} has declared war on {$r['guild_name']}, bringing in your guild to help fight.");
			
					guildSendLeadersNotif($otheralliance, "{$gd['guild_name']} has declared war on {$r['guild_name']}, bringing in your guild to help fight.");
					guildSendMembersNotif($otheralliance, "Your guild has declared war on {$r['guild_name']}.");
					
					$api->GuildAddNotification($_POST['guild'], "The {$allyName} guild has declared war on your guild.");
					guildSendLeadersNotif($_POST['guild'], "{$allyName} was an ally of  {$gd['guild_name']}, declaring war on your guild.");
					guildSendMembersNotif($_POST['guild'], "{$allyName} has declared war on your guild.");
				}
			}
			
			$allyq2=$db->query("/*qc=on*/SELECT * FROM `guild_alliances` WHERE (`alliance_a` = {$_POST['guild']} OR `alliance_b` = {$_POST['guild']})");
			while ($ar=$db->fetch_row($allyq2))
			{
				if ($ar['alliance_a'] == $_POST['guild'])
					$otheralliance=$ar['alliance_b'];
				else
					$otheralliance=$ar['alliance_a'];
				$allyName = $api->GuildFetchInfo($otheralliance, 'guild_name');
				$api->GuildAddNotification($otheralliance,"{$r['guild_name']} had war declared upon them by {$gd['guild_name']}, bringing in your guild to help fight.");
				guildSendLeadersNotif($otheralliance, "{$gd['guild_name']} has declared war on {$r['guild_name']}, bringing in your guild to help fight.");
				guildSendMembersNotif($otheralliance, "Your guild has declared war on {$r['guild_name']}.");
				
				$api->GuildAddNotification($ir['guild'], "The {$allyName} guild has declared war on your guild.");
				guildSendLeadersNotif($ir['guild'], "{$allyName} was an ally of  {$gd['guild_name']}, declaring war on your guild.");
				guildSendMembersNotif($ir['guild'], "{$allyName} has declared war on your guild.");
				$db->query("INSERT INTO `guild_wars` VALUES (NULL, {$otheralliance}, {$ir['guild']}, 0, 0, {$endtime}, 0)");
			}
			$db->query("UPDATE `guild` SET `guild_primcurr` = `guild_primcurr` - 500000 WHERE `guild_id` = {$ir['guild']}");
			guildSendMembersNotif($ir['guild'], "Your guild has declared war on {$r['guild_name']}.");
            $api->SystemLogsAdd($userid, 'guilds', "Declared war on {$r['guild_name']} [{$_POST['guild']}]");
            alert('success', "Success!", "You have declared war on {$r['guild_name']}.", true, '?action=staff&act2=idx');
        } else {
            $csrf = request_csrf_html('guild_staff_declarewar');
            echo "
			<table class='table table-bordered'>
			<form method='post'>
				<tr>
					<th colspan='2'>
						It costs 500K Copper Coins to declare war on another guild. Wars last a week and your guild will be the winner if you rack up more points against your enemy. 
                        If you have allies, they will come to your aid. Note, however, if your enemy has allies, they will declare war on your guild to protect their alliance. Note that 
                        alliances deemed non-aggressive will dissolve once you declare war.
					</th>
				</tr>
				<tr>
					<th>
						Guild
					</th>
					<td>
						" . guilds_dropdown() . "
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-primary' value='Declare War'>
					</td>
				</tr>
			{$csrf}
			</form>
			</table>
			<a href='?action=staff&act2=idx'>Go Back</a>";
        }
    } else {
        alert('danger', "Uh Oh!", "You can only be here if you're the guild's leader.", true, '?action=staff&act2=idx');
    }
}

function staff_levelup()
{
    global $db, $gd, $api, $userid;
    //Experience required set to variable
    $xprequired = $gd['xp_needed'];
    if (isset($_POST['do'])) {

        //Guild does not have enough experience to level up.
        if ($gd['guild_xp'] < $xprequired) {
            alert('danger', "Uh Oh!", "Your guild does not have enough experience to level up. You can get more experience by going to war.");
        } else {
            //Level the guild up.
            $db->query("UPDATE `guild` SET `guild_level` = `guild_level` + 1,
			`guild_xp` = `guild_xp` - {$xprequired} WHERE `guild_id` = {$gd['guild_id']}");
            alert('success', "Success!", "You have successfully leveled up your guild.", true, '?action=staff&act2=idx');
            $api->SystemLogsAdd($userid, 'guilds', "Leveled up the {$gd['guild_name']} guild.");
            $api->GuildAddNotification($gd['guild_id'], "Your guild has leveled up!");
        }
    } else {
        echo "You may level up your guild. Your guild will need the minimum required Experience to do this. You may gain
        guild Experience by going to war with another guild and gaining points in war. At your guild's level, your
        guild will need " . shortNumberParse($xprequired) . " Guild Experience to level up. Your guild currently has
        " . shortNumberParse($gd['guild_xp']) . " Experience. Do you wish to attempt to level up?<br />
		<form method='post'>
			<input type='hidden' name='do' value='yes'>
			<input type='submit' value='Level Up' class='btn btn-success'>
		</form>
		<a href='?action=staff&act2=idx'>Go Back</a>";
    }
}

function staff_tax()
{
    global $db, $gd, $api, $h, $userid;
    //Check if the user is the owner of the guild.
    if (isGuildLeader()) {
        //Guild does not own a town, so tell them so.
        if (!$db->fetch_single($db->query("/*qc=on*/SELECT `town_id` FROM `town` WHERE `town_guild_owner` = {$gd['guild_id']}")) > 0) {
            alert('danger', "Uh Oh!", "Your guild does not own a town to set a tax rate on.", true, '?action=staff&act2=idx');
            die($h->endpage());
        }
        if (isset($_POST['tax'])) {
            //Verify the variables are safe to work with.
            $_POST['tax'] = (isset($_POST['tax']) && is_numeric($_POST['tax'])) ? abs($_POST['tax']) : 0;

            //Verify CSRF check has passed.
            if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_tax", stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
                die($h->endpage());
            }

            //Make sure tax rate is between 0-10%
            if ($_POST['tax'] < 0 || $_POST['tax'] > 10) {
                alert('danger', "Uh Oh!", "You can only set a tax rate between 0% and 10%");
                die($h->endpage());
            }
            //Update town's tax rate.
            $town_id = $db->fetch_single($db->query("/*qc=on*/SELECT `town_id` FROM `town` WHERE `town_guild_owner` = {$gd['guild_id']}"));
            $db->query("UPDATE `town` SET `town_tax` = {$_POST['tax']} WHERE `town_guild_owner` = {$gd['guild_id']}");
            $api->SystemLogsAdd($userid, 'tax', "Set tax rate to {$_POST['tax']}% in {$api->SystemTownIDtoName($town_id)}.");
            alert('success', "Success!", "You have set the tax rate of {$api->SystemTownIDtoName($town_id)} to {$_POST['tax']}%.", true, '?action=staff&act2=idx');
        } else {
            $csrf = request_csrf_html('guild_staff_tax');
            $current_tax = $db->fetch_single($db->query("/*qc=on*/SELECT `town_tax` FROM `town` WHERE `town_guild_owner` = {$gd['guild_id']}"));
            echo "
			<table class='table table-bordered'>
			<form method='post'>
				<tr>
					<th colspan='2'>
						You may change the tax rate for the town your guild owns here. Please do not type the percent sign.
					</th>
				</tr>
				<tr>
					<th>
						Tax Rate (Percent)
					</th>
					<td>
						<input type='number' name='tax' class='form-control' value='{$current_tax}' min='0' max='20' required='1'>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-primary' value='Change Tax'>
					</td>
				</tr>
			{$csrf}
			</form>
			</table>
			<a href='?action=staff&act2=idx'>Go Back</a>";
        }

    } else {
        alert('danger', "Uh Oh!", "You can only be here if you're the guild's leader.", true, '?action=staff&act2=idx');
    }
}

function staff_dissolve()
{
    global $db, $gd, $api, $h, $userid, $ir, $wq;
    //Check if user is the owner of the guild.
    if (isGuildLeader()) {
        if (isset($_POST['do'])) {
            //Verify CSRF check has passed.
            if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_dissolve", stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
                die($h->endpage());
            }
            //Make sure guild is not at war.
            if ($db->fetch_single($wq) > 0) {
                alert('danger', "Uh Oh!", "You cannot dissolve your guild when you are at war.");
                die($h->endpage());
            }

            //Select all guild members, and tell them what happened to their guild via notification.
            $q = $db->query("/*qc=on*/SELECT `userid`,`username` FROM `users` WHERE `guild` = {$ir['guild']}");
            while ($r = $db->fetch_row($q)) {
                $api->GameAddNotification($r['userid'], "Your guild, {$gd['guild_name']}, has been dissolved by <a href='profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}].");
            }
            //Log the guild being deleted.
            $api->SystemLogsAdd($userid, 'guilds', "Dissolved Guild ID {$ir['guild']}");

            //Delete everything.
            deleteGuild($ir['guild']);
			addToEconomyLog('Guild Fees', 'copper', $gd['guild_primcurr']*-1);
			addToEconomyLog('Guild Fees', 'token', $gd['guild_seccurr']*-1);
            alert("success", "Success!", "You have successfully dissolved your guild.", true, 'index.php');
        } else {
            $csrf = request_csrf_html('guild_staff_dissolve');
            echo "Are you sure you wish to dissolve your guild? This action cannot be undone. Everything in the guild's
            armory and vault will be removed from the game entirely.<br />
            <form method='post'>
            {$csrf}
            <input type='hidden' name='do' value='do'>
            <input type='submit' class='btn btn-primary' value='Dissolve Guild'>
            </form>";
        }
    } else {
        alert('danger', "Uh Oh!", "You can only be here if you're the guild's leader.", true, '?action=staff&act2=idx');
    }

}

function staff_armory()
{
    global $db, $gd, $api, $h, $set, $userid, $ir;
	if (!isGuildVaultManager())
	{
		alert('danger',"Uh Oh!","You do not have permission to be here.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
    //Check to see if the guild has bought the armory.
    if (!guildOwnsAsset($ir['guild'], "guild_armory")) 
    {

        //Set the cost to varaible for ease of use.
        $cost = $set['GUILD_PRICE'] * 4;
        if (isset($_GET['buy'])) {

            //Guild does not have enough Copper Coins to buy the armory.
            if ($gd['guild_primcurr'] < $cost) {
                alert('danger', "Uh Oh!", "Your guild does not have enough Copper Coins to buy an armory.", true, '?action=staff&act2=idx');
                die($h->endpage());
            }
            //Buy the armory and remove the currency.
            $db->query("UPDATE `guild`
                        SET `guild_hasarmory` = 'true',
                        `guild_primcurr` = `guild_primcurr` - {$cost}
                        WHERE `guild_id` = {$gd['guild_id']}");
            //Log
            alert('success', 'Success!', "You have successfully purchased an armory for your guild.");
            $api->SystemLogsAdd($userid, 'guilds', "Purchased guild armory.");
        } 
        else 
        {
            echo "Your guild does not have an armory. It will cost your guild " . shortNumberParse($cost) . " Copper Coins
            to purchase an armory. Do you wish to purchase an armory for your guild?<br />
            <a href='?action=staff&act2=armory&buy=yes' class='btn btn-success'>Yes</a>
            <a href='?action=staff&act2=idx' class='btn btn-danger'>No</a>";
        }
    } 
    else 
    {
        if (isset($_POST['user'])) {
            //Make sure every variable is safe to work with.
            $_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : 0;
            $_POST['item'] = (isset($_POST['item']) && is_numeric($_POST['item'])) ? abs($_POST['item']) : 0;
            $_POST['qty'] = (isset($_POST['qty']) && is_numeric($_POST['qty'])) ? abs($_POST['qty']) : 0;

            //Verify CSRF check is successful.
            if (!isset($_POST['verf']) || !verify_csrf_code("guild_give_item", stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
                die($h->endpage());
            }

            //Verify user chosen is in the guild.
            $q = $db->query("/*qc=on*/SELECT `userid`, `username` FROM `users` WHERE `userid` = {$_POST['user']} AND `guild` = {$gd['guild_id']}");
            if ($db->num_rows($q) == 0) {
                $db->free_result($q);
                alert('danger', "Uh Oh!", "You cannot give items to someone not in your guild.");
                die($h->endpage());
            }
            $db->free_result($q);

            //Verify the item chosen exists.
            $q = $db->query("/*qc=on*/SELECT `itmname` FROM `items` WHERE `itmid` = {$_POST['item']}");
            if ($db->num_rows($q) == 0) {
                $db->free_result($q);
                alert('danger', "Uh Oh!", "You cannot give out non-existent items.");
                die($h->endpage());
            }
            $db->free_result($q);

            //Verify the user is giving at least one item.
            if ($_POST['qty'] <= 0) {
                alert('danger', "Uh Oh!", "You must give out at least one item.");
                die($h->endpage());
            }
			
			if (!($api->GuildHasItem($ir['guild'],$_POST['item'],$_POST['qty'])))
			{
				alert('danger',"Uh Oh!","Your guild does not have " . number_format($_POST['qty']) . " {$api->SystemItemIDtoName($_POST['item'])}(s) in its armory.",true,'guild_district.php');
				die($h->endpage());
			}

            //Check users' IP Address. Returns false if not and/or same user
            if ($api->SystemCheckUsersIPs($userid, $_POST['user'])) {
                alert('danger', "Uh Oh!", "You cannot give items to players who share the same IP Address as you.");
                die($h->endpage());
            }

            //Give items and whatnot.
            $api->GuildRemoveItem($ir['guild'], $_POST['item'], $_POST['qty']);
            $api->UserGiveItem($_POST['user'], $_POST['item'], $_POST['qty']);

            //Resolve item to variable
            $item = $api->SystemItemIDtoName($_POST['item']);
            $user = $api->SystemUserIDtoName($_POST['user']);

            //Notification
            $api->GameAddNotification($_POST['user'], "You have been given " . shortNumberParse($_POST['qty']) . " {$item}(s) from your guild's armory.");
            $api->GuildAddNotification($ir['guild'], "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has given " . shortNumberParse($_POST['qty']) . " {$item}(s) from your guild's armory to <a href='profile.php?user={$_POST['user']}'>{$user}</a>.");
            alert('success', "Success!", "You have successfully given " . shortNumberParse($_POST['qty']) . " {$item}(s) from your guild's armory to {$user}.", true, "?action=staff&act2=idx");
            $api->SystemLogsAdd($userid, 'guilds', "Gave {$user} " . shortNumberParse($_POST['qty']) . " {$item}(s) from their armory.");
        } 
        else 
        {
            //Giving item form.
            $csrf = request_csrf_html('guild_give_item');
            echo "Fill out the form below completely to give out items from your armory.<br />
            <form method='post'>
            " . guild_user_dropdown('user', $ir['guild']) . "<br />
            " . armory_dropdown() . "<br />
            <input type='number' required='1' min='1' name='qty' placeholder='Quantity' class='form-control'><br />
            <input type='submit' value='Give Item' class='btn btn-primary'>
            {$csrf}
            </form>";
        }
    }
}
function staff_bonus()
{
	global $db,$gd,$userid,$api,$ir,$h,$set;
	$cost = $set['GUILD_PRICE'] * 4;
	if (isset($_POST['action']))
	{
		if ($_POST['action'] == 'enable')
		{
			if ($gd['guild_bonus_time'] > time())
			{
				alert('danger',"Uh Oh!","Your guild already has boost activated.");
				die($h->endpage());
			}
			if ($gd['guild_primcurr'] < $cost)
			{
				alert('danger',"Uh Oh!","Your guild does not have enough Copper Coins to enable training Boost.");
				die($h->endpage());
			}
			$bonustime=time()+((60*60)*3);
			$notif = "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has paid " . shortNumberParse($cost) . " Copper Coins to enable training boost in your guild's gym for the next 3 hours.";
			$db->query("UPDATE `guild` SET `guild_bonus_time` = {$bonustime}, `guild_primcurr` = `guild_primcurr` - {$cost} WHERE `guild_id` = {$gd['guild_id']}");
			$api->GuildAddNotification($gd['guild_id'],$notif);
			guildSendMemberNotif($ir['guild'], $notif);
			alert('success',"Success!","You successfully enabled training boost for all your guild members that use your guild's gym. Reminder, that this boost is only affective in your guild's gym, and will end 3 hours from now.",true,'?action=staff&act2=idx');
		}
	}
	else
	{
	    echo "Enable training boost for your guild? It'll cost your guild " . shortNumberParse($cost) . " Copper Coins. The Training Boost is only affective in 
		your Guild's Gym. This boost will also only last for 3 hours.
		<form method='post'>
			<input type='hidden' name='action' value='enable'>
			<input type='submit' class='btn-primary btn' value='Enable Boost'>
		</form>
		<a href='?action=staff&act2=idx'>Go Back</a>";
	}
}
function staff_crimes()
{
    global $db, $userid, $api, $h, $ir, $gd;
	if (!isGuildCrimeLord())
	{
		alert('danger',"Uh Oh!","You do not have permission to be here.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
    //Select the guild's member count.
    $cnt = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `users` WHERE `guild` = {$ir['guild']}");
    $membs = $db->fetch_single($cnt);
    $db->free_result($cnt);
    if (isset($_POST['crime'])) {
        //Make the POST safe to work with.
        $_POST['crime'] = (isset($_POST['crime']) && is_numeric($_POST['crime'])) ? abs(intval($_POST['crime'])) : 0;

        //Verify CSRF check is successful.
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_crimes", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }

        //Check that the guild isn't already planning a crime.
        if ($gd['guild_crime'] != 0) {
            alert('danger', "Uh Oh!", "You guild is already planning a crime.");
            die($h->endpage());
        }
        //Verify crime exists.
        $cq = $db->query("/*qc=on*/SELECT `gcUSERS` from `guild_crimes` WHERE `gcID` = {$_POST['crime']}");
        if ($db->num_rows($cq) == 0) {
            alert('danger', "Uh Oh!", "You cannot commit a non-existent crime.");
            die($h->endpage());
        }
        //Verify guild has enough members to commit this crime.
        $cr = $db->fetch_single($cq);
        if ($cr > $membs) {
            alert('danger', "Uh Oh!", "You cannot commit this crime as you need {$cr} guild members. You only have {$membs}.");
            die($h->endpage());
        }
        //Select time 24 hours from now.
        $ttc = time() + 86400;

        //Set guild's crime.
        $db->query("UPDATE `guild`
                    SET `guild_crime` = {$_POST['crime']},
                    `guild_crime_done` = {$ttc}
                    WHERE `guild_id` = {$ir['guild']}");
        alert('success', "Success!", "You have started to plan this crime. It will take 24 hours to commit.", true, '?action=staff&act2=idx');
    } else {
        //Select the crimes from database, based on how many members the guild has.
        $q = $db->query("/*qc=on*/SELECT *
                         FROM `guild_crimes`
                         WHERE `gcUSERS` <= $membs");

        //If there's crimes the guild can commit.
        if ($db->num_rows($q) > 0) {
            $csrf = request_csrf_html('guild_staff_crimes');
            echo "/*qc=on*/SELECT the crime you wish your guild to commit.<br />
            <form method='post'>
                <select name='crime' type='dropdown' class='form-control'>";
            while ($r = $db->fetch_row($q)) {
                echo "<option value='{$r['gcID']}'>{$r['gcNAME']}
                		({$r['gcUSERS']} members needed)</option>\n";
            }

            echo "</select><br />
                <input type='submit' value='Plan Crime' class='btn btn-primary'>
                {$csrf}
            </form>";
        } //Guild has no crimes they can commit.
        else {
            alert('danger', "Uh Oh!", "You guild cannot commit any crimes at this time.", true, '?action=staff&act2=idx');
        }
    }
}
function staff_pic()
{
	global $db, $userid, $api, $h, $ir, $gd;
	if (!isGuildLeadership())
	{
		alert('danger',"Uh Oh!","You do not have permission to be here.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
	if (isset($_POST['newpic']))
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('guild_changepic', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Your action was blocked for security reasons. Fill out the form quicker next time.");
            die($h->endpage());
        }
        $npic = (isset($_POST['newpic']) && is_string($_POST['newpic'])) ? stripslashes($_POST['newpic']) : '';
        if (!empty($npic)) {
            $sz = get_filesize_remote($npic);
            if ($sz <= 0 || $sz >= 15728640) {
                alert('danger', "Uh Oh!", "You picture's file size is too big. At maximum, picture file size can be 15MB.");
                $h->endpage();
                exit;
            }
            $image = (@isImage($npic));
            if (!$image) {
                alert('danger', "Uh Oh!", "The link you've input is not an image.");
                die($h->endpage());
            }
        }
		$img = htmlentities($_POST['newpic'], ENT_QUOTES, 'ISO-8859-1');
        alert('success', "Success!", "You have successfully updated your display picture to what's shown below.", true, '?action=staff&act2=idx');
        echo "<img src='{$img}' width='250' height='250' class='img-fluid'>";
        $db->query("UPDATE `guild` SET `guild_pic` = '" . $db->escape($npic) . "' WHERE `guild_id` = {$gd['guild_id']}");
	}
	else
	{
		$csrf = request_csrf_html('guild_changepic');
        echo "
		<h3>Change Guild Picture</h3>
		<hr />
		Your images must be externally hosted. Any images that are not 500x500 will be scaled accordingly.<br />
		New Picture Link<br />
		<form method='post'>
			<input type='url' name='newpic' class='form-control' value='{$gd['guild_pic']}' />
				{$csrf}
			<br />
			<input type='submit' class='btn btn-primary' value='Change Guild Pic' />
		</form>
		";
	}
}

function staff_intromsg()
{
	global $gd, $db, $h;
	if (!isGuildAppManager())
	{
		alert('danger',"Uh Oh!","You do not have permission to be here.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
    if (isset($_POST['ament'])) {

        //Verify CSRF check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_intromsg", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }

        //Make sure the POST is safe to work with.
        $ament = $db->escape(nl2br(htmlentities(stripslashes($_POST['ament']), ENT_QUOTES, 'ISO-8859-1')));

        //Update the guild's announcement.
        $db->query("UPDATE `guild` SET `guild_intromsg` = '{$ament}' WHERE `guild_id` = {$gd['guild_id']}");
        alert('success', "Success!", "You have updated your guild's introduction message.", true, '?action=staff&act2=idx');
    } else {
        //Escape the announcement for safety reasons.
        $am_for_area = strip_tags($gd['guild_intromsg']);
        $csrf = request_csrf_html('guild_staff_intromsg');
        echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					This message will be sent to every applicant that gets accepted into the guild.
				</th>
			</tr>
			<tr>
				<th>
					Introductory Message
				</th>
				<td>
					<textarea class='form-control' name='ament'>{$am_for_area}</textarea>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='Change Introductory Message' class='btn btn-primary'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>
		<a href='?action=staff&act2=idx'>Go Back</a>";
    }
}

function staff_blockapps()
{
	global $db,$gd,$userid,$api,$ir,$h;
	if (!isGuildAppManager())
	{
		alert('danger',"Uh Oh!","You do not have permission to be here.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
	if (isset($_POST['action']))
	{
		if ($_POST['action'] == 'block')
		{
			if ($gd['guild_ba'] == 1)
			{
				alert('danger',"Uh Oh!","Your guild is already blocking applications.");
				die($h->endpage());
			}
			$db->query("UPDATE `guild` SET `guild_ba` = 1 WHERE `guild_id` = {$gd['guild_id']}");
			$api->GuildAddNotification($gd['guild_id'],"<a href='profile.php?user={$userid}'>{$ir['username']}</a> has set the guild to no longer accept applications.");
			alert('success',"Success!","You have successfully set your guild to block all incoming applications.",true,'?action=staff&act2=idx');
		}
		else
		{
			if ($gd['guild_ba'] == 0)
			{
				alert('danger',"Uh Oh!","Your guild is already allowing applications.");
				die($h->endpage());
			}
			$db->query("UPDATE `guild` SET `guild_ba` = 0 WHERE `guild_id` = {$gd['guild_id']}");
			$api->GuildAddNotification($gd['guild_id'],"<a href='profile.php?user={$userid}'>{$ir['username']}</a> has set the guild to accept applications.");
			alert('success',"Success!","You have successfully set your guild to allow applications once again.",true,'?action=staff&act2=idx');
		}
	}
	else
	{
		echo "Here you may block guild applications. Players will not be able to send in a guild application unless disabled.
		<form method='post'>
			<input type='hidden' name='action' value='block'>
			<input type='submit' class='btn-outline-danger btn' value='Block Applications'>
		</form>
		<form method='post'>
			<input type='hidden' name='action' value='allow'>
			<input type='submit' class='btn-outline-primary btn' value='Allow Applications'>
		</form>
		<a href='?action=staff&act2=idx'>Go Back</a>";
	}
}
function staff_ally()
{
	global $db,$gd,$userid,$api,$ir,$h;
	//Check if user is the owner of the guild.
    if (isGuildLeader()) 
	{
        if (isset($_POST['guild']))
		{
			$_POST['guild'] = (isset($_POST['guild']) && is_numeric($_POST['guild'])) ? abs($_POST['guild']) : 0;

            //Verify CSRF check has passed.
            if (!isset($_POST['verf']) || !verify_csrf_code("guild_staff_ally_request", stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
                die($h->endpage());
            }
			if ($_POST['guild'] == $ir['guild'])
			{
				alert('danger',"Uh Oh!","You cannot send an alliance request to your own guild.");
				die($h->endpage());
			}
			$gexist=$db->query("/*qc=on*/SELECT * FROM `guild` WHERE `guild_id` = {$_POST['guild']}");
			if ($db->num_rows($gexist) == 0)
			{
				alert('danger',"Uh Oh!","You cannot send an ally request to a non-existent guild.");
				die($h->endpage());
			}
			$exist=$db->query("/*qc=on*/SELECT * 
								FROM `guild_alliances` 
								WHERE 
								(`alliance_a` = {$ir['guild']} OR `alliance_b` = {$ir['guild']}) 
								AND 
								(`alliance_a` = {$_POST['guild']} OR `alliance_b` = {$_POST['guild']})");
			if ($db->num_rows($exist) != 0)
			{
				alert('danger',"Uh Oh!","It appears you already have an outstanding alliance request, or are already allied with this guild.");
				die($h->endpage());
			}
			$txt = "{$gd['guild_name']} has sent an alliance request.";
			$type = ($_POST['type'] == 'traditional') ? 1 : 2;
			$api->GuildAddNotification($ir['guild'],"Your guild has sent an alliance request to {$api->GuildFetchInfo($_POST['guild'],'guild_name')}.");
			$api->GuildAddNotification($_POST['guild'],$txt);
			guildSendLeadersNotif($_POST['guild'], $txt);
			$db->query("INSERT INTO `guild_alliances` (`alliance_a`, `alliance_b`, `alliance_type`, `alliance_true`) VALUES ('{$ir['guild']}', '{$_POST['guild']}', '{$type}', '0')");
			alert('success',"Success!","Alliance request has been sent successfully.",true,'?action=staff&act2=idx');
		}
		else
		{
			$csrf = request_csrf_html('guild_staff_ally_request');
			echo "/*qc=on*/SELECT the guild you wish to form an alliance with, then select the alliance 
			type. Traditional alliance is an alliance where if either guild declares war, 
			the other guild will come to its aid. Non-aggressive alliances are when guilds 
			will only help when their alliance guild has war declared upon it. The alliance will 
			break in an Non-aggressive alliance if either party declares war on a third party.<br />
			<form method='post'>
				" . guilds_dropdown() . "
				<select name='type' class='form-control'>
					<option value='traditional'>Tradtional</option>
					<option value='nap'>Non-aggressive</option>
					{$csrf}
					<input type='submit' value='Request Alliance' class='btn btn-primary'>
				</select>
			</form>
			<a href='?action=staff&act2=idx'>Go Back</a>";
		}
    } 
	else 
	{
        alert('danger', "Uh Oh!", "You can only be here if you're the guild's leader.", true, '?action=staff&act2=idx');
    }
}
function staff_view_alliance_request()
{
	global $db,$gd,$userid,$api,$ir,$h;
	//Check if user is the owner of the guild.
    if (isGuildLeader()) 
	{
		if (isset($_GET['accept']))
		{
			$_GET['accept'] = (isset($_GET['accept']) && is_numeric($_GET['accept'])) ? abs($_GET['accept']) : 0;
			$exist=$db->query("/*qc=on*/SELECT * 
								FROM `guild_alliances` 
								WHERE `alliance_id` = {$_GET['accept']}
								AND `alliance_b` = {$ir['guild']}
								AND `alliance_true` = 0");
			if ($db->num_rows($exist) == 0)
			{
				alert('danger',"Uh Oh!","Request does not exist or does not belong to you.",false);
			}
			else
			{
				$re=$db->fetch_row($exist);
				alert('success',"Success!","Alliance request accepted successfully.",false);
				$db->query("UPDATE `guild_alliances` SET `alliance_true` = 1 WHERE `alliance_id` = {$_GET['accept']}");
				$api->GuildAddNotification($ir['guild'],"Your guild has accepted {$api->GuildFetchInfo($re['alliance_a'],'guild_name')}'s alliance request.");
				$api->GuildAddNotification($re['alliance_a'],"{$gd['guild_name']} has accepted your guild's alliance request.");
			}
		}
		if (isset($_GET['decline']))
		{
			$_GET['decline'] = (isset($_GET['decline']) && is_numeric($_GET['decline'])) ? abs($_GET['decline']) : 0;
			$exist=$db->query("/*qc=on*/SELECT * 
								FROM `guild_alliances` 
								WHERE `alliance_id` = {$_GET['decline']}
								AND `alliance_b` = {$ir['guild']}
								AND `alliance_true` = 0");
			if ($db->num_rows($exist) == 0)
			{
				alert('danger',"Uh Oh!","Request does not exist or does not belong to you.",false);
			}
			else
			{
				$re=$db->fetch_row($exist);
				alert('success',"Success!","Alliance request declined successfully.",false);
				$db->query("DELETE FROM `guild_alliances` WHERE `alliance_id` = {$_GET['decline']}");
				$api->GuildAddNotification($ir['guild'],"Your guild has declined {$api->GuildFetchInfo($re['alliance_a'],'guild_name')}'s alliance request.");
				$api->GuildAddNotification($re['alliance_a'],"{$gd['guild_name']} has declined your guild's alliance request.");
			}
		}
		if (isset($_GET['delete']))
		{
			$_GET['delete'] = (isset($_GET['delete']) && is_numeric($_GET['delete'])) ? abs($_GET['delete']) : 0;
			$exist=$db->query("/*qc=on*/SELECT * 
								FROM `guild_alliances` 
								WHERE `alliance_id` = {$_GET['delete']}
								AND `alliance_a` = {$ir['guild']}
								AND `alliance_true` = 0");
			if ($db->num_rows($exist) == 0)
			{
				alert('danger',"Uh Oh!","Request does not exist or does not belong to you.",false);
			}
			else
			{
				$re=$db->fetch_row($exist);
				alert('success',"Success!","Alliance request was successfully withdrawn.",false);
				$db->query("DELETE FROM `guild_alliances` WHERE `alliance_id` = {$_GET['delete']}");
				$api->GuildAddNotification($ir['guild'],"Your guild has withdrawn their alliance request to {$api->GuildFetchInfo($re['alliance_a'],'guild_name')}.");
				$api->GuildAddNotification($re['alliance_b'],"{$gd['guild_name']} has withdrawn their alliance request.");
			}
		}
		echo "These are the alliance requests your guild has received. Traditional alliance 
		is an alliance where if either guild declares war,the other guild will come to its aid. 
		Non-aggressive alliances are when guilds will only help when their alliance guild has 
		war declared upon it. The alliance will break in an Non-aggressive alliance if either 
		party declares war on a third party.
		<table class='table table-bordered'>
			<tr>
				<th>
					Guild Name
				</th>
				<th>
					Alliance Type
				</th>
				<th>
					Links
				</th>
			</tr>";
			$q=$db->query("/*qc=on*/SELECT * FROM `guild_alliances` WHERE `alliance_b` = {$ir['guild']} AND `alliance_true` = 0");
			while ($r=$db->fetch_row($q))
			{
				$type = ($r['alliance_type'] == 1) ? "Traditional" : "Non-aggressive";
				echo "
				<tr>
					<td>
						{$api->GuildFetchInfo($r['alliance_a'],'guild_name')}
					</td>
					<td>
						{$type}
					</td>
					<td>
						[<a href='?action=staff&act2=viewrally&accept={$r['alliance_id']}'>Accept</a>] || 
						[<a href='?action=staff&act2=viewrally&decline={$r['alliance_id']}'>Decline</a>]
					</td>
				</tr>";
			}
		echo "</table>
		<br />
		These are the alliance requests your guild has sent out.<table class='table table-bordered'>
			<tr>
				<th>
					Guild Name
				</th>
				<th>
					Alliance Type
				</th>
				<th>
					Links
				</th>
			</tr>";
		$q=$db->query("/*qc=on*/SELECT * FROM `guild_alliances` WHERE `alliance_a` = {$ir['guild']} AND `alliance_true` = 0");
		while ($r=$db->fetch_row($q))
		{
			$type = ($r['alliance_type'] == 1) ? "Traditional" : "Non-aggressive";
			echo "
			<tr>
				<td>
					{$api->GuildFetchInfo($r['alliance_b'],'guild_name')}
				</td>
				<td>
					{$type}
				</td>
				<td>
					[<a href='?action=staff&act2=viewrally&delete={$r['alliance_id']}'>Delete</a>]
				</td>
			</tr>";
		}
		echo"</table>
		<a href='?action=staff&act2=idx'>Go Back</a>";
    } 
	else 
	{
        alert('danger', "Uh Oh!", "You can only be here if you're the guild's leader.", true, '?action=staff&act2=idx');
    }
}
function staff_view_alliances()
{
	global $db,$gd,$userid,$api,$ir,$h;
	//Check if user is the owner of the guild.
    if (isGuildLeader()) 
	{
		if (isset($_GET['delete']))
		{
			$_GET['delete'] = (isset($_GET['delete']) && is_numeric($_GET['delete'])) ? abs($_GET['delete']) : 0;
			$exist=$db->query("/*qc=on*/SELECT * 
								FROM `guild_alliances` 
								WHERE `alliance_id` = {$_GET['delete']}
								AND (`alliance_a` = {$ir['guild']} OR `alliance_b` = {$ir['guild']})
								AND `alliance_true` = 1");
			if ($db->num_rows($exist) == 0)
			{
				alert('danger',"Uh Oh!","This alliance does not exist, or does not belong to you.",false);
			}
			else
			{
				$re=$db->fetch_row($exist);
				if ($re['alliance_a'] == $ir['guild'])
				{
					$broked=$re['alliance_b'];
				}
				else
				{
					$broked=$re['alliance_a'];
				}
				$notif = "{$gd['guild_name']} has broke their alliance with your guild.";
				alert('success',"Success!","Alliance was successfully destroyed.",false);
				$db->query("DELETE FROM `guild_alliances` WHERE `alliance_id` = {$_GET['delete']}");
				$api->GuildAddNotification($ir['guild'],"Your guild has broke their alliance with {$api->GuildFetchInfo($re['alliance_a'],'guild_name')}.");
				$api->GuildAddNotification($broked,$notif);
				guildSendLeadersNotif($broked, $notif);
			}
		}
		echo "These are the alliances your guild has. Traditional alliance 
		is an alliance where if either guild declares war,the other guild will come to its aid. 
		Non-aggressive alliances are when guilds will only help when their alliance guild has 
		war declared upon it. The alliance will break in an Non-aggressive alliance if either 
		party declares war on a third party.
		<table class='table table-bordered'>
			<tr>
				<th>
					Guild Name
				</th>
				<th>
					Alliance Type
				</th>
				<th>
					Links
				</th>
			</tr>";
			$q=$db->query("/*qc=on*/SELECT * 
								FROM `guild_alliances` 
								WHERE (`alliance_a` = {$ir['guild']} OR `alliance_b` = {$ir['guild']})
								AND `alliance_true` = 1");
			while ($r=$db->fetch_row($q))
			{
				$type = ($r['alliance_type'] == 1) ? "Traditional" : "Non-aggressive";
				if ($r['alliance_a'] == $ir['guild'])
					$otheralliance=$r['alliance_b'];
				else
					$otheralliance=$r['alliance_a'];
				echo "
				<tr>
					<td>
						{$api->GuildFetchInfo($otheralliance,'guild_name')}
					</td>
					<td>
						{$type}
					</td>
					<td>
						[<a href='?action=staff&act2=viewallies&delete={$r['alliance_id']}'>Break Alliance</a>]
					</td>
				</tr>";
			}
		echo "</table>
		<a href='?action=staff&act2=idx'>Go Back</a>";
    } 
	else 
	{
        alert('danger', "Uh Oh!", "You can only be here if you're the guild's leader.", true, '?action=staff&act2=idx');
    }
}

function staff_sword_guild()
{
	global $db,$gd,$userid,$api,$ir,$h,$set;
	//Check if user is the owner of the guild.
	echo "<h4>Guild Branded Weapons</h4><hr />";
	$cost = $set['GUILD_PRICE'] * 100;
    if (isGuildLeader()) 
	{
		if (isset($_POST['buyswords']))
		{
			//Create the sword item
			$swordID=$gd['guild_sword_item'];
			if ($gd['guild_primcurr'] < $cost)
			{
				alert('danger', "Uh Oh!", "Your guild does not have enough Copper Coins to buy Guild Weapons.", true, '?action=staff&act2=idx');
				die($h->endpage());
			}
			if ($gd['guild_sword_item'] == 0)
			{
				$name="{$gd['guild_name']}-Branded Sword";
				$desc="A weapon created for the {$gd['guild_name']} guild. This weapon was created " . date('l, F j, Y g:i:s a') . ".";
				for ($i = 1; $i <= 3; $i++) 
				{
					$statRND=Random(1,5);
					if ($statRND == 1)
						$stat = 'strength';
					elseif ($statRND == 2)
						$stat = 'agility';
					elseif ($statRND == 3)
						$stat = 'guard';
					elseif ($statRND == 4)
						$stat = 'labor';
					elseif ($statRND == 5)
						$stat = 'iq';
					$statBoostRND=Random(1,7);
					$dmgValue=Random(135,350)*$gd['guild_level'];
					$effects[$i] = $db->escape(serialize(
						array("stat" => "{$stat}",
							"dir" => 'pos',
							"inc_type" => "percent",
							"inc_amount" => $statBoostRND)));
				}
				$db->query(
                "INSERT INTO `items`
						VALUES(NULL, '1', '{$name}', '{$desc}',
                     500000, 250000, 'true', 
					 'true', '{$effects[1]}',
                     'true', '{$effects[2]}',
                     'true', '{$effects[3]}', 
					{$dmgValue}, 0, 'game-icon game-icon-fragmented-sword enchanted_glow', 0, '')");
				$swordID = $db->insert_id();
				$db->query("UPDATE `guild` SET `guild_sword_item` = {$swordID} WHERE `guild_id` = {$gd['guild_id']}");
				addToEconomyLog('Guild Fees', 'copper', $cost * -1);
			}
			$api->GuildAddNotification($gd['guild_id'], "Your leader, <a href='profile.php?user={$userid}'>{$ir['username']}</a>, has spent 10,000,000 Copper Coins and ordered 5 guild weapons to the armory.");
			$api->GuildAddItem($gd['guild_id'],$swordID,5);
			$db->query("UPDATE `guild` set `guild_primcurr` = `guild_primcurr` - {$cost} WHERE `guild_id` = {$gd['guild_id']}");
			alert('success', "Success!", "You have successfully ordered five guild weapons to the guild armory for " . shortNumberParse($cost) . " Copper Coins.", true, '?action=staff&act2=idx');
		}
		else
		{
			echo "Guild weapons aren't meant to be over-powered. They're supposed to be a
			historical relic that your guild existed. If that's worth anything is totally up to how 
			your guild is today. Take pride in your guild with these weapons.<br />
			Guild Weapons are not cheap by any means. It costs your guild " . shortNumberParse($cost) . " Copper Coins to have a 
			set of five weapons created.<br />
			Would you like a set to be created? It'll cost you " . shortNumberParse($cost) . " Copper Coins for 5 weapons. The weapons will 
			deposit into your guild armory.<br />
			<form method='post'>
				<input type='hidden' name='buyswords' value='1'>
				<input type='submit' class='btn btn-success' value='Buy Weapons'>
				<a href='?action=staff' class='btn btn-danger'>No Thanks</a>
			</form>";
		}
	}
	else
	{
		alert('danger', "Uh Oh!", "You can only be here if you're the guild's leader.", true, '?action=staff&act2=idx');
	}
}
function staff_upgrade_sword()
{
    global $db,$gd,$userid,$api,$ir,$h,$set;
	echo "<h4>Upgrade Guild Weapon</h4><hr />";
	$cost = $set['GUILD_PRICE'] * 100;
	if (!isGuildLeader())
	{
		alert('danger',"Uh Oh!","You do not have permission to be here.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
	if ($gd['guild_sword_item'] == 0)
	{
		alert('danger',"Uh Oh!","Your guild does not have a guild weapon to be upgraded.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
	$r=$db->fetch_row($db->query("SELECT * FROM `items` WHERE `itmid` = {$gd['guild_sword_item']}"));
	if ($r['itmbuyprice'] >= 1500000)
	{
		alert('danger',"Uh Oh!","You have upgraded your guild's weapon to the maximum it can be upgraded.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
	if (isset($_POST['upgrade']))
	{
		$wepIncrease = Random(105,115);
		$costIncrease = Random(110,130);
		$newWep = $r['weapon'] * ($wepIncrease / 100);
		$newCost = $r['itmbuyprice'] * ($costIncrease / 100);
		$newSell = $newCost / 2;
		if ($gd['guild_primcurr'] < $cost)
		{
			alert('danger', "Uh Oh!", "Your guild does not have enough Copper Coins to upgrade your guild's weapon right now.", true, '?action=staff&act2=idx');
			die($h->endpage());
		}
		addToEconomyLog('Guild Fees', 'copper', $cost * -1);
		$db->query("UPDATE `guild` set `guild_primcurr` = `guild_primcurr` - {$cost} WHERE `guild_id` = {$gd['guild_id']}");
		$db->query("UPDATE `items` 
					SET `weapon` = '{$newWep}',
					`itmbuyprice` = '{$newCost}',
					`itmsellprice` = '{$newSell}'
					WHERE `itmid` = {$gd['guild_sword_item']}");
		$api->GuildAddNotification($gd['guild_id'], "<a href='profile.php?user={$userid}'>{$ir['username']}</a>, one of your guild owners, has spent " . shortNumberParse($cost) . " Copper Coins to upgrade your guild weapon.");
		alert('success',"Success!","You have successfully upgraded your guild's weapon for " . shortNumberParse($cost) . " Copper Coins.",true,'?action=staff&act2=idx');
	}
	else
	{
		echo "Upgrading your guild weapon will make it stronger and increase its item value. Upgrades will
		affect all weapons that currently exist, and will effect those made in the future. This will not change 
		your creation costs. You may upgrade your guild's weapon until its item buy value reaches 
		1.5M Copper Coins.<br />
		Each upgrade costs " . shortNumberParse($cost) . " Copper Coins.<br />
		Each upgrade will increase your sword's weapon rating by 5-15%.<br />
		Each upgrade will increase your sword's item buy value by 10-30%.<br />
		Do you wish to upgrade your guild's sword?
			<form method='post'>
				<input type='hidden' name='upgrade' value='1'>
				<input type='submit' class='btn btn-success' value='Upgrade Weapons'>
				<a href='?action=staff' class='btn btn-danger'>No Thanks</a>
			</form>";
	}
}
function staff_decommission_sword()
{
    global $db,$gd,$userid,$api,$ir,$h,$set;
    $cost = 500000;
	echo "<h4>Decommission Guild Weapon</h4><hr />";
	if (!isGuildLeader())
	{
		alert('danger',"Uh Oh!","You do not have permission to be here.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
	if ($gd['guild_sword_item'] == 0)
	{
		alert('danger',"Uh Oh!","Your guild does not have a guild weapon to be decommissioned.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
	if (isset($_POST['decommission']))
	{
		if ($gd['guild_primcurr'] < $cost)
		{
			alert('danger', "Uh Oh!", "Your guild does not have enough Copper Coins to decomission your weapons right now.", true, '?action=staff&act2=idx');
			die($h->endpage());
		}
		addToEconomyLog('Guild Fees', 'copper', $cost * -1);
		$db->query("UPDATE `guild` set `guild_primcurr` = `guild_primcurr` - 5000000, `guild_sword_item` = 0 WHERE `guild_id` = {$gd['guild_id']}");
		forceDeleteItem($gd['guild_sword_item']);
		$api->GuildAddNotification($gd['guild_id'], "<a href='profile.php?user={$userid}'>{$ir['username']}</a>, your guild owner, has spent " . shortNumberParse($cost) . " Copper Coins to decommission your guild's weapons.");
		alert('success',"Success!","You have successfully decommissioned your guild's weapon for " . shortNumberParse($cost) . " Copper Coins.",true,'?action=staff&act2=idx');
	}
	else
	{
		echo "You may decommission your guild's current weapon here. Since you are stopping production runs early, the 
		blacksmith who was creating your weapons is charging you a " . shortNumberParse($cost) . " fee to break the deal.<br />
		Decommssioned weapons will be labeled as such, and will have its decommision date noted. Weapons are 100% final once 
		decommissioned. Guild weapons get their power from being a official guild relic. These weapons will be permanently removed 
        from the game.
			<form method='post'>
				<input type='hidden' name='decommission' value='1'>
				<input type='submit' class='btn btn-success' value='Decommission Weapon'>
				<a href='?action=staff' class='btn btn-danger'>No Thanks</a>
			</form>";
	}
}
function staff_sword_reroll()
{
	global $db,$gd,$userid,$api,$ir,$h,$set;
	//Check if user is the owner of the guild.
	echo "<h4>Guild Weapon Stat Re-Roll</h4><hr />";
	$cost = $set['GUILD_PRICE'] * 250;
    if (isGuildLeader()) 
	{
		if ($gd['guild_sword_item'] == 0)
	{
		alert('danger',"Uh Oh!","Your guild does not have a guild weapon to have its stats re-rolled.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
		if (isset($_POST['reroll']))
		{
			//Create the sword item
			$swordID=$gd['guild_sword_item'];
			$r=$db->fetch_row($db->query("SELECT * FROM `items` WHERE `itmid` = {$swordID}"));
			$costIncrease = Random(105,115);
			$newCost = $r['itmbuyprice'] * ($costIncrease / 100);
			$newSell = $newCost / 2;
			if ($gd['guild_primcurr'] < $cost)
			{
				alert('danger', "Uh Oh!", "Your guild does not have enough Copper Coins to re-roll your Guild Weapon's stat boosts.", true, '?action=staff&act2=idx');
				die($h->endpage());
			}
			if ($r['itmbuyprice'] >= 1500000)
			{
				alert('danger',"Uh Oh!","You cannot re-roll the stats on your guild's weapon if its value exceeds over 1,500,000 Copper Coins.",true,'?action=staff&act2=idx');
				die($h->endpage());
			}
			for ($i = 1; $i <= 3; $i++) 
			{
				$statRND=Random(1,5);
				if ($statRND == 1)
					$stat = 'strength';
				elseif ($statRND == 2)
					$stat = 'agility';
				elseif ($statRND == 3)
					$stat = 'guard';
				elseif ($statRND == 4)
					$stat = 'labor';
				elseif ($statRND == 5)
					$stat = 'iq';
				$statBoostRND=Random(1,7);
				$effects[$i] = $db->escape(serialize(
					array("stat" => "{$stat}",
						"dir" => 'pos',
						"inc_type" => "percent",
						"inc_amount" => $statBoostRND)));
			}
			$db->query("UPDATE `items` 
						SET `effect1` = '{$effects[1]}',
						`effect2` = '{$effects[2]}',
						`effect3` = '{$effects[3]}',
						`itmbuyprice` = '{$newCost}',
						`itmsellprice` = '{$newSell}'
						WHERE `itmid` = {$swordID}");
			addToEconomyLog('Guild Fees', 'copper', $cost * -1);
			$api->GuildAddNotification($gd['guild_id'], "Your leader, <a href='profile.php?user={$userid}'>{$ir['username']}</a>, has spent " . shortNumberParse($cost) . " Copper Coins re-roll the stat boosts on your guild's weapons.");
			$db->query("UPDATE `guild` set `guild_primcurr` = `guild_primcurr` - {$cost} WHERE `guild_id` = {$gd['guild_id']}");
			alert('success', "Success!", "You have successfully re-rolled the stat boosts on your guild's weapons for " . shortNumberParse($cost) . " Copper Coins.", true, '?action=staff&act2=idx');
		}
		else
		{
			echo "Don't like the stat boosts you got on your guild's weapon? Need not worry! You can re-roll them for " . shortNumberParse($cost) . " Copper Coins! This will 
			increase the weapon's buy value by 5-15%.
			<form method='post'>
				<input type='hidden' name='reroll' value='1'>
				<input type='submit' class='btn btn-success' value='Re-Roll Stat Boosts'>
				<a href='?action=staff' class='btn btn-danger'>No Thanks</a>
			</form>";
		}
	}
	else
	{
		alert('danger', "Uh Oh!", "You can only be here if you're the guild's leader.", true, '?action=staff&act2=idx');
	}
}
function staff_sword_rename()
{
    global $db,$gd,$userid,$api,$ir,$h;
	$vipitem = 394;
	echo "<h4>Rename Guild Weapon</h4><hr />";
	if (!isGuildLeader())
	{
		alert('danger',"Uh Oh!","You do not have permission to be here.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
	if ($gd['guild_sword_item'] == 0)
	{
		alert('danger',"Uh Oh!","Your guild does not have a guild weapon to be renamed.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
	if (isset($_POST['rename']))
	{
		$itmname = (isset($_POST['rename']) && is_string($_POST['rename'])) ? $db->escape(strip_tags(stripslashes($_POST['rename']))) : '';
		if (empty($itmname))
		{
			alert('danger',"Uh Oh!","You input an invalid item name. Go back and try again.");
			die($h->endpage());
		}
		$inq = $db->query("/*qc=on*/SELECT `itmid` FROM `items` WHERE `itmname` = '{$itmname}'");
        if ($db->num_rows($inq) > 0) 
		{
            alert('danger', "Uh Oh!", "An item with the same name already exists. Go back and try again.");
            die($h->endpage());
        }
		$user="<a href='profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}]";
		if ($api->GuildHasItem($gd['guild_id'], $vipitem))
		{
			$api->GuildRemoveItem($gd['guild_id'], $vipitem, 1);
			$api->GuildAddNotification($gd['guild_id'],"{$user} traded a {$api->SystemItemIDtoName($vipitem)} and submitted a request to have your guild's weapon renamed.");
			alert('success',"Success!","Request was submitted successfully. One {$api->SystemItemIDtoName($vipitem)} has been taken from your guild's armory.",true,'?action=staff&act2=idx');
		}
		elseif ($gd['guild_primcurr'] < 10000000)
		{
			alert('danger', "Uh Oh!", "It costs 10,000,000 Copper Coins to rename your guild's weapon. Go back and try again.");
            die($h->endpage());
		}
		else
		{
			$db->query("UPDATE `guild` SET `guild_primcurr` = `guild_primcurr` - 10000000 WHERE `guild_id` = {$ir['guild']}");
			$api->GuildAddNotification($gd['guild_id'],"{$user} paid 10,000,000 Copper Coins and submitted a request to have your guild's weapon renamed.");
			alert('success',"Success!","Request was submitted successfully. 10,000,000 Copper Coins have been taken from your guild's vault.",true,'?action=staff&act2=idx');
		}
		submitToModeration($gd['guild_sword_item'], "guild_sword_name", $itmname, $userid);
		die($h->endpage());
	}
	else
	{
		$itemname=$db->fetch_single($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$gd['guild_sword_item']}"));
		echo "
		Submit a request to rename your guild's weapon. Requests will go to a staff moderation team 
		and will be approved or denied from there. It costs 10,000,000 Copper Coins, and if your request 
		is denied, you will not be given your Copper Coins back. This is to deter wasting the modeation team's 
		time. If your guild has a {$api->SystemItemIDtoName($vipitem)} in its armory, we'll take that before 
		we take from the vault. <b>Please ensure that whatever you name your weapon, it fits the theme of Medieval Europe. Guns, lasers, 
		and robotics won't be found here. Requests that do not fit Medieval Europe will be declined regardless.</b>
		<hr />
		<form method='post'>
			<div class='row'>
				<div class='col-12 col-lg-6 col-xl-4'>
					What would you like your guild sword to be named?
				</div>
				<div class='col-12 col-lg-6 col-xl-8'>
					<input type='text' class='form-control' name='rename' required='1' value='{$itemname}'>
				</div>
			</div>
			<br />
			<div class='row'>
				<div class='col-12'>
					<input type='submit' class='btn btn-primary btn-block' value='Submit for Approval'>
				</div>
			</div>
		</form>";
	}
}

function staff_sword_pic()
{
    global $db,$gd,$userid,$api,$ir,$h;
	echo "<h4>Guild Weapon Pic</h4><hr />";
	if (!isGuildLeader())
	{
		alert('danger',"Uh Oh!","You do not have permission to be here.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
	if ($gd['guild_sword_item'] == 0)
	{
		alert('danger',"Uh Oh!","Your guild does not have a guild weapon to set a new picture for.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
	if (isset($_POST['rename']))
	{
		$itmname = (isset($_POST['rename']) && is_string($_POST['rename'])) ? $db->escape(strip_tags(stripslashes($_POST['rename']))) : '';
		if (empty($itmname))
		{
			alert('danger',"Uh Oh!","You input an invalid picture. Go back and try again.");
			die($h->endpage());
		}
		if ($gd['guild_primcurr'] < 10000000)
		{
			alert('danger', "Uh Oh!", "It costs 10,000,000 Copper Coins to rename your sword. Go back and try again.");
            die($h->endpage());
		}
		$db->query("UPDATE `guild` SET `guild_primcurr` = `guild_primcurr` - 10000000 WHERE `guild_id` = {$ir['guild']}");
		submitToModeration($gd['guild_sword_item'], "guild_sword_pic", $itmname, $userid);
		$user="<a href='profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}]";
		$api->GuildAddNotification($gd['guild_id'],"{$user} paid 10,000,000 Copper Coins and submitted a request to change the picture of your guild's weapon..");
		alert('success',"Success!","Request was submitted successfully. 10,000,000 Copper Coins have been taken from your guild's vault.",true,'?action=staff&act2=idx');
		die($h->endpage());
	}
	else
	{
		$itemname=$db->fetch_single($db->query("SELECT `icon` FROM `items` WHERE `itmid` = {$gd['guild_sword_item']}"));
		echo "
		Submit a request to change the picture your guild's weapon. Requests will go to a staff moderation team 
		and will be approved or denied from there. It costs 10,000,000 Copper Coins, and if your request 
		is denied, you will not be given your Copper Coins back. This is to deter wasting the modeation team's 
		time. <b>Please ensure that whatever pic you choose for your your weapon, it fits the theme of Medieval Europe. 
		Guns, lasers, and robotics won't be found here. Requests that do not fit Medieval Europe will be declined 
		regardless.</b>
		<hr />
		<form method='post'>
			<div class='row'>
				<div class='col-12 col-lg-6 col-xl-4'>
					URL to Pic
				</div>
				<div class='col-12 col-lg-6 col-xl-8'>
					<input type='text' class='form-control' name='rename' required='1' value='{$itemname}'>
				</div>
			</div>
			<br />
			<div class='row'>
				<div class='col-12'>
					<input type='submit' class='btn btn-primary btn-block' value='Submit for Approval'>
				</div>
			</div>
		</form>";
	}
}

function staff_asset_management()
{
    global $db,$gd,$userid,$api,$ir,$h;
    $gymOwned = (guildOwnsAsset($ir['guild'], "guild_gym")) ? "<span class='text-success'>Owned</span>" : "<span class='text-danger'>Not owned</span>";
    $armoryOwned = (guildOwnsAsset($ir['guild'], "guild_armory")) ? "<span class='text-success'>Owned</span>" : "<span class='text-danger'>Not owned</span>";
    $vaultUpgrade1Owned = (guildOwnsAsset($ir['guild'], "guild_upgrade_vault1")) ? "<span class='text-success'>Owned</span>" : "<span class='text-danger'>Not owned</span>";
    echo "<div class='row'>
            <div class='col-12'>
                <div class='card'>
                    <div class='card-header'>
                        {$gd['guild_name']}'s Ownable Assets
                    </div>
                    <div class='card-body'>
                        <div class='row'>
                            <div class='col'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <b><small>Asset Name</small></b>
                                    </div>
                                    <div class='col-12'>
                                        Guild Gym
                                    </div>
                                </div>
                            </div>
                            <div class='col'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <b><small>Asset Description</small></b>
                                    </div>
                                    <div class='col-12'>
                                        <i>Allows members of the guild access to a new gym, to train more efficiently.</i>
                                    </div>
                                </div>
                            </div>
                            <div class='col'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <b><small>Asset Ownership</small></b>
                                    </div>
                                    <div class='col-12'>
                                        {$gymOwned}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div class='row'>
                            <div class='col'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <b><small>Asset Name</small></b>
                                    </div>
                                    <div class='col-12'>
                                        Guild Armory
                                    </div>
                                </div>
                            </div>
                            <div class='col'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <b><small>Asset Description</small></b>
                                    </div>
                                    <div class='col-12'>
                                        <i>Allows members of the guild to store and share items in a central location.</i>
                                    </div>
                                </div>
                            </div>
                            <div class='col'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <b><small>Asset Ownership</small></b>
                                    </div>
                                    <div class='col-12'>
                                        {$armoryOwned}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div class='row'>
                            <div class='col'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <b><small>Asset Name</small></b>
                                    </div>
                                    <div class='col-12'>
                                        Deeper Vault
                                    </div>
                                </div>
                            </div>
                            <div class='col'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <b><small>Asset Description</small></b>
                                    </div>
                                    <div class='col-12'>
                                        <i>Increases the guild's vault capacity by 8%.</i>
                                    </div>
                                </div>
                            </div>
                            <div class='col'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <b><small>Asset Ownership</small></b>
                                    </div>
                                    <div class='col-12'>
                                        {$vaultUpgrade1Owned}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>";
}
$h->endpage();