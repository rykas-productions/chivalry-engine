<?php
/*
	File:		hirespy.php
	Created: 	4/5/2016 at 12:10AM Eastern Time
	Info: 		Allows players to hire spies on other players at a cost.
				Spy will fetch stats and equipment.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
//Sanitize GET.
$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : '';
//GET is empty/truncated after sanitation,  don't let player buy a spy.
if (empty($_GET['user'])) {
    alert("danger", "Uh Oh!", "Please specify a user you wish to hire a spy upon.", true, "index.php");
    die($h->endpage());
}
//Current user is in the infirmary, don't let them buy a spy.
if ($api->UserStatus($userid, 'infirmary')) {
    alert('danger', "Unconscious!", "You cannot hire a spy on someone if you're in the infirmary.", true, "profile.php?user={$_GET['user']}");
    die($h->endpage());
}
//Current user is in the dungeon, don't let them buy a spy.
if ($api->UserStatus($userid, 'dungeon')) {
    alert('danger', "Locked Up!", "You cannot hire a spy on someone if you're in the dungeon.", true, "profile.php?user={$_GET['user']}");
    die($h->endpage());
}
//GET is the same player as the current user, do not allow to buy spy.
if ($_GET['user'] == $userid) {
    alert("danger", "Uh Oh!", "Why would you want to hire a spy upon yourself?", true, "profile.php?user={$_GET['user']}");
    die($h->endpage());
}
//Grab GET user's information.
$q = $db->query("SELECT `u`.*, `us`.* FROM `users` `u` INNER JOIN `userstats` AS `us` ON `us`.`userid` = `u`.`userid` WHERE `u`.`userid` = {$_GET['user']}");
//User does not exist, so do not allow spy to be bought.
if ($db->num_rows($q) == 0) {
    alert("danger", "Uh Oh!", "The player you're trying to hire a spy upon does not exist.", true, "profile.php?user={$_GET['user']}");
    die($h->endpage());
}
$r = $db->fetch_row($q);
//GET User is in the same guild as the current player, do not allow spy to be bought.
if (($r['guild'] == $ir['guild']) && ($ir['guild'] != 0)) {
    alert("danger", "Uh Oh!", "You cannot hire a spy on someone in your guild.", true, "profile.php?user={$_GET['user']}");
    die($h->endpage());
}
//Spy has been bought, and all other tests have passed!
if (isset($_POST['do']) && (isset($_GET['user']))) {
    //Random Number Generator to choose what happens.
    $rand = Random(1, 4);
    //Current user does not have the required Copper Coins to buy a spy.
    if ($ir['primary_currency'] < $r['level'] * 500) {
        alert("danger", "Uh Oh!", "You do not have enough Copper Coins to hire a spy to spy on this user.", true, "profile.php?user={$_GET['user']}");
        die($h->endpage());
    }
    //Take the spy cost from the player.
    $api->UserTakeCurrency($userid, 'primary', $r['level'] * 500);
    //RNG equals 1 or 2, the spy has failed.
    if ($rand == 1 || $rand == 2) {
        //Specific event RNG
        $rand2 = Random(1, 3);
        //Spy failed and the person being spied on only knows that /someone/ has made an attempt to spy on them.
        if ($rand2 <= 2) {
            $api->GameAddNotification($_GET['user'], "An unknown user has attempted to spy on you and failed.");
            alert("danger", "Uh Oh!", "Your spy attempts to get information on your target. Your spy is noticed. Your
			    target doesn't get wind of who you are.", true, "profile.php?user={$_GET['user']}");
            $api->SystemLogsAdd($userid, 'spy', "Tried to spy on " . $api->SystemUserIDtoName($_GET['user']) . " and failed.");
            die($h->endpage());
        } //Spy failed and hte person bein spied on now knows who's been attempting to spy.
        else {
            $api->GameAddNotification($_GET['user'], "<a href='profile.php?user={$userid}'>{$ir['username']}</a> has attempted to spy on you and failed.");
            alert("danger", "Uh Oh!", "Your spy attempts to get information on your target. Your spy is noticed and
			    attacked! To save his own life, he name drops you. Your target now knows who sent the agent.", true, 'index.php');
            $api->SystemLogsAdd($userid, 'spy', "Tried to spy on " . $api->SystemUserIDtoName($_GET['user']) . " and failed.");
            die($h->endpage());
        }
    } //RNG equals 3, send current player to the dungeon.
    elseif ($rand == 3) {
        alert("danger", "Uh Oh!", "Your hired spy actually turned out to be a dungeon guard. He arrests you.", true, "profile.php?user={$_GET['user']}");
        $dungtime = Random($ir['level'], $ir['level'] * 3);
        $api->UserStatusSet($userid, 'dungeon', $dungtime, "Stalkerish Tendencies");
        $api->SystemLogsAdd($userid, 'spy', "Tried to spy on " . $api->SystemUserIDtoName($_GET['user']) . " and was sent to the dungeon.");
        die($h->endpage());
    } //RNG equals 4, show the current player the person's stats and weapons.
    else {
        alert("success", "Success!", "You have paid " . number_format(500 * $r['level']) . " to hire a spy upon
		    {$r['username']}. Here is that information.", false);
        echo "<br />
		<table class='table table-bordered'>
			<tr>
				<th>
					Primary Weapon
				</th>
				<td>
					" . $api->SystemItemIDtoName($r['equip_primary']) . "
				</td>
			</tr>
			<tr>
				<th>
					Secondary Weapon
				</th>
				<td>
					" . $api->SystemItemIDtoName($r['equip_secondary']) . "
				</td>
			</tr>
			<tr>
				<th>
					Armor
				</th>
				<td>
					" . $api->SystemItemIDtoName($r['equip_armor']) . "
				</td>
			</tr>
			<tr>
				<th>
					Strength
				</th>
				<td>
					" . number_format($r['strength']) . "
				</td>
			</tr>
			<tr>
				<th>
					Agility
				</th>
				<td>
					" . number_format($r['agility']) . "
				</td>
			</tr>
			<tr>
				<th>
					Guard
				</th>
				<td>
					" . number_format($r['guard']) . "
				</td>
			</tr>
			<tr>
				<th>
					IQ
				</th>
				<td>
					" . number_format($r['iq']) . "
				</td>
			</tr>
			<tr>
				<th>
					Labor
				</th>
				<td>
					" . number_format($r['labor']) . "
				</td>
			</tr>
		</table>";
		echo "Here's their inventory as well.
		<table class='table table-bordered table-striped'>
	    <thead>
		<tr>
			<th>Item (Qty)</th>
			<th class='hidden-xs-down'>Item Cost (Total)</th>
		</tr></thead>";
		$inv =
		$inv=$db->query(
			"SELECT `inv_qty`, `itmsellprice`, `itmid`, `inv_id`,
                 `effect1_on`, `effect2_on`, `effect3_on`,
                 `weapon`, `armor`, `itmtypename`, `itmdesc`
                 FROM `inventory` AS `iv`
                 INNER JOIN `items` AS `i`
                 ON `iv`.`inv_itemid` = `i`.`itmid`
                 INNER JOIN `itemtypes` AS `it`
                 ON `i`.`itmtype` = `it`.`itmtypeid`
                 WHERE `iv`.`inv_userid` = {$_GET['user']}
                 ORDER BY `i`.`itmtype` ASC, `i`.`itmname` ASC");
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
			$icon = returnIcon($i['itmid']);
			echo "<tr>
						<td>
							{$icon} <a href='iteminfo.php?ID={$i['itmid']}' data-toggle='tooltip' data-placement='right' title='{$i['itmdesc']}'>
								{$api->SystemItemIDtoName($i['itmid'])}
							</a>";
			if ($i['inv_qty'] > 1) {
				echo " (" . number_format($i['inv_qty']) . ")";
			}
			echo "</td>
					  <td class='hidden-xs-down'>" . number_format($i['itmsellprice']);
			echo "  (" . number_format($i['itmsellprice'] * $i['inv_qty']) . ")";
			echo "</td>
				</tr>";
		}
		echo "</table>";
        //Save to the log.
        $api->SystemLogsAdd($userid, 'spy', "Successfully spied on " . $api->SystemUserIDtoName($_GET['user']));
		$db->query("INSERT INTO `spy_advantage` (`user`, `spied`) VALUES ('{$userid}', '{$_GET['user']}')");
    }
} //Starting form.
else {
    echo "You are attempting to hire a spy on " . $api->SystemUserIDtoName($_GET['user']) .
        ". Spies cost 500 Copper Coins multiplied by their level. (" . number_format(500 * $r['level']) . "
	in this case.) Success is not guaranteed.<br />
	<form action='?user={$_GET['user']}' method='post'>
		<input type='hidden' name='do' value='yes'>
		<input type='submit' class='btn btn-primary' value='Hire Spy'>
	</form>";
}
$h->endpage();