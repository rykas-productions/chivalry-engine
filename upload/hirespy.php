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
$q = $db->query("/*qc=on*/SELECT `u`.*, `us`.* FROM `users` `u` INNER JOIN `userstats` AS `us` ON `us`.`userid` = `u`.`userid` WHERE `u`.`userid` = {$_GET['user']}");
//User does not exist, so do not allow spy to be bought.
if ($db->num_rows($q) == 0) {
    alert("danger", "Uh Oh!", "The player you're trying to hire a spy upon does not exist.", true, "profile.php?user={$_GET['user']}");
    die($h->endpage());
}
$r = $db->fetch_row($q);
//Spy has been bought, and all other tests have passed!
if (isset($_POST['do']) && (isset($_GET['user']))) {
    //Random Number Generator to choose what happens.
    $rand = Random(1, 4);
	if ($userid == 1)
		$rand = 4;
    //Current user does not have the required Copper Coins to buy a spy.
    if ($ir['primary_currency'] < $r['level'] * 500) {
        alert("danger", "Uh Oh!", "You do not have enough Copper Coins to hire a spy to spy on this user.", true, "profile.php?user={$_GET['user']}");
        die($h->endpage());
    }
    //Take the spy cost from the player.
    $api->UserTakeCurrency($userid, 'primary', $r['level'] * 500);
	addToEconomyLog('Spy Services', 'copper', ($r['level'] * 500)*-1);
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
        $dungtime = Random($ir['level'], $ir['level'] * Random(2,4));
        $api->UserStatusSet($userid, 'dungeon', $dungtime, "Stalker Tendencies");
        $api->SystemLogsAdd($userid, 'spy', "Tried to spy on " . $api->SystemUserIDtoName($_GET['user']) . " and was sent to the dungeon.");
        die($h->endpage());
    } //RNG equals 4, show the current player the person's stats and weapons.
    else {
		$citybank = ($r['bank'] > -1) ? "<span class='text-success'>Purchased</span>" : "<span class='text-danger'>N/A</span>"; 
		$fedbank = ($r['bigbank'] > -1) ? "<span class='text-success'>Purchased</span>" : "<span class='text-danger'>N/A</span>"; 
		$vaultbank = ($r['vaultbank'] > -1) ? "<span class='text-success'>Purchased</span>" : "<span class='text-danger'>N/A</span>"; 
		$tokenbank = ($r['tokenbank'] > -1) ? "<span class='text-success'>Purchased</span>" : "<span class='text-danger'>N/A</span>"; 
		$strength = ($r['strength'] >= $ir['strength']) ? "<span class='text-danger'>" . shortNumberParse($r['strength']) . "</span>" : "<span class='text-success'>" . shortNumberParse($r['strength']) . "</span>"; 
		$agility = ($r['agility'] >= $ir['agility']) ? "<span class='text-danger'>" . shortNumberParse($r['agility']) . "</span>" : "<span class='text-success'>" . shortNumberParse($r['agility']) . "</span>"; 
        $guard = ($r['guard'] >= $ir['guard']) ? "<span class='text-danger'>" . shortNumberParse($r['guard']) . "</span>" : "<span class='text-success'>" . shortNumberParse($r['guard']) . "</span>"; 
		$labor = ($r['labor'] >= $ir['labor']) ? "<span class='text-danger'>" . shortNumberParse($r['labor']) . "</span>" : "<span class='text-success'>" . shortNumberParse($r['labor']) . "</span>"; 
		$iq = ($r['iq'] >= $ir['iq']) ? "<span class='text-danger'>" . shortNumberParse($r['iq']) . "</span>" : "<span class='text-success'>" . shortNumberParse($r['iq']) . "</span>"; 
		$luck = ($r['luck'] >= $ir['luck']) ? "<span class='text-danger'>" . shortNumberParse($r['luck']) . "%</span>" : "<span class='text-success'>" . shortNumberParse($r['luck']) . "%</span>"; 
		$necklace = getEquippedNecklace($r['userid']);
		$pendant = getEquippedPendant($r['userid']);
		$primary_ring = getEquippedPrimaryRing($r['userid']);
		$secondary_ring = getEquippedSecondaryRing($r['userid']);
		alert("success", "Success!", "You have paid " . shortNumberParse(500 * $r['level']) . " Copper Coins to hire a spy upon
		    {$r['username']}. Here is that information.", false);
			echo "<div class='row'>
				<div class='col-md'>
					<div class='card'>
						<div class='card-header'>
							Primary Weapon
						</div>
						<div class='card-body'>";
							if ($r['equip_primary'] > 0)
							{
								echo returnIcon($r['equip_primary'],4) . "<br />
								" . $api->SystemItemIDtoName($r['equip_primary']);
							}
							else
							{
								echo "<i>No primary weapon equipped.</i>";
							}
						echo "</div>
					</div>
				</div>
				<div class='col-md'>
					<div class='card'>
						<div class='card-header'>
							Secondary Weapon
						</div>
						<div class='card-body'>";
							if ($r['equip_secondary'] > 0)
							{
								echo returnIcon($r['equip_secondary'],4) . "<br />
								" . $api->SystemItemIDtoName($r['equip_secondary']);
							}
							else
							{
								echo "<i>No secondary weapon equipped.</i>";
							}
						echo "
						</div>
					</div>
				</div>
				<div class='col-md'>
					<div class='card'>
						<div class='card-header'>
							Armor
						</div>
						<div class='card-body'>";
							if ($r['equip_armor'] > 0)
							{
								echo returnIcon($r['equip_armor'],4) . "<br />
								" . $api->SystemItemIDtoName($r['equip_armor']);
							}
							else
							{
								echo "<i>No armor equipped.</i>";
							}
						echo "
						</div>
					</div>
				</div>
				<div class='col-md'>
					<div class='card'>
						<div class='card-header'>
							Combat Potion
						</div>
						<div class='card-body'>";
							if ($r['equip_potion'] > 0)
							{
								echo returnIcon($r['equip_potion'],4) . "<br />
								" . $api->SystemItemIDtoName($r['equip_potion']);
							}
							else
							{
								echo "<i>No combat potion equipped.</i>";
							}
						echo "
						</div>
					</div>
				</div>
			</div>
			<br />";
			echo "<div class='row'>
				<div class='col-md'>
					<div class='card'>
						<div class='card-header'>
							Ring
						</div>
						<div class='card-body'>";
							if ($primary_ring > 0)
							{
								echo returnIcon($primary_ring,4) . "<br />
								" . $api->SystemItemIDtoName($primary_ring);
							}
							else
							{
								echo "<i>No ring equipped.</i>";
							}
						echo "</div>
					</div>
				</div>
				<div class='col-md'>
					<div class='card'>
						<div class='card-header'>
							Ring
						</div>
						<div class='card-body'>";
							if ($secondary_ring > 0)
							{
								echo returnIcon($secondary_ring,4) . "<br />
								" . $api->SystemItemIDtoName($secondary_ring);
							}
							else
							{
								echo "<i>No ring equipped.</i>";
							}
						echo "
						</div>
					</div>
				</div>
				<div class='col-md'>
					<div class='card'>
						<div class='card-header'>
							Pendant
						</div>
						<div class='card-body'>";
							if ($pendant > 0)
							{
								echo returnIcon($pendant,4) . "<br />
								" . $api->SystemItemIDtoName($pendant);
							}
							else
							{
								echo "<i>No pendant equipped.</i>";
							}
						echo "
						</div>
					</div>
				</div>
				<div class='col-md'>
					<div class='card'>
						<div class='card-header'>
							Necklace
						</div>
						<div class='card-body'>";
							if ($necklace > 0)
							{
								echo returnIcon($necklace,4) . "<br />
								" . $api->SystemItemIDtoName($necklace);
							}
							else
							{
								echo "<i>No necklace equipped.</i>";
							}
						echo "
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
							Stats
					</div>
					<div class='card-body'>
						<div class='row'>
							<div class='col-md'>
								<b>Strength</b>
							</div>
							<div class='col-md'>
								{$strength}
							</div>
						</div>
						<div class='row'>
							<div class='col-md'>
								<b>Agility</b>
							</div>
							<div class='col-md'>
								{$agility}
							</div>
						</div>
						<div class='row'>
							<div class='col-md'>
								<b>Guard</b>
							</div>
							<div class='col-md'>
								{$guard}
							</div>
						</div>
						<div class='row'>
							<div class='col-md'>
								<b>Labor</b>
							</div>
							<div class='col-md'>
								{$labor}
							</div>
						</div>
						<div class='row'>
							<div class='col-md'>
								<b>IQ</b>
							</div>
							<div class='col-md'>
								{$iq}
							</div>
						</div>
						<div class='row'>
							<div class='col-md'>
								<b>Luck</b>
							</div>
							<div class='col-md'>
								{$luck}
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class='col-md'>
				<div class='card'>
					<div class='card-header'>
							Misc Info
					</div>
					<div class='card-body'>
						<div class='row'>
							<div class='col-md'>
								<b>City Bank</b>
							</div>
							<div class='col-md'>
								{$citybank}
							</div>
						</div>
						<div class='row'>
							<div class='col-md'>
								<b>Federal Bank</b>
							</div>
							<div class='col-md'>
								{$fedbank}
							</div>
						</div>
						<div class='row'>
							<div class='col-md'>
								<b>Vault Bank</b>
							</div>
							<div class='col-md'>
								{$vaultbank}
							</div>
						</div>
						<div class='row'>
							<div class='col-md'>
								<b>Chivalry Token Bank</b>
							</div>
							<div class='col-md'>
								{$tokenbank}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>";
		echo "Here's their inventory as well.<br />";
		$inv = $db->query("/*qc=on*/SELECT `iv`.`inv_qty`, `iv`.`inv_id`,
				`i`.*, `it`.`itmtypename`
                 FROM `inventory` AS `iv`
                 INNER JOIN `items` AS `i`
                 ON `iv`.`inv_itemid` = `i`.`itmid`
                 INNER JOIN `itemtypes` AS `it`
                 ON `i`.`itmtype` = `it`.`itmtypeid`
                 WHERE `iv`.`inv_userid` = {$r['userid']}
                 AND `iv`.`inv_qty` > 0
                 ORDER BY `i`.`itmtype` ASC, `i`.`itmname` ASC");
		$lt = "";
		echo "<div class='accordion' id='inventoryAccordian'>";
		while ($i = $db->fetch_row($inv)) 
		{
			if ($lt != $i['itmtypename']) 
			{
				$lt = $i['itmtypename'];
				echo "<div class='card'>
				<div class='card-body' id='heading{$i['itmid']}'>
					<h4 class='mb-0'>
						{$lt}
					</h4>
				</div>
				</div>";
			}
			$i['itmdesc'] = htmlentities($i['itmdesc'], ENT_QUOTES);
			$icon = returnIcon($i['itmid'],2);
			$i['inv_qty_value']=$i['inv_qty']*$i['itmsellprice'];
			$armory=$db->fetch_single($db->query("/*qc=on*/SELECT SUM(`gaQTY`) FROM `guild_armory` WHERE `gaITEM` = {$i['itmid']} AND `gaGUILD` != 1"));
			$invent=$db->fetch_single($db->query("/*qc=on*/SELECT SUM(`inv_qty`) FROM `inventory` WHERE `inv_itemid` = {$i['itmid']} AND `inv_userid` != 1"));
			$market=$db->fetch_single($db->query("/*qc=on*/SELECT SUM(`imQTY`) FROM `itemmarket` WHERE `imITEM` = {$i['itmid']}"));
			$primary=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`equip_primary`) FROM `users` WHERE `equip_primary` = {$i['itmid']} AND `userid` != 1"));
			$secondary=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`equip_secondary`) FROM `users` WHERE `equip_secondary` = {$i['itmid']} AND `userid` != 1"));
			$armor=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`equip_armor`) FROM `users` WHERE `equip_armor` = {$i['itmid']} AND `userid` != 1"));
			$badge=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`equip_badge`) FROM `users` WHERE `equip_badge` = {$i['itmid']} AND `userid` != 1"));
			$trink=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`equip_slot`) FROM `user_equips` WHERE `itemid` = {$i['itmid']}"));
			$total=$invent+$armory+$market+$primary+$secondary+$armor+$badge+$trink;
			echo "
			<div class='card'>
				<div class='card-header' id='heading{$i['itmid']}'>
					<h2 class='mb-0'>
						<button class='btn btn-block btn-block text-left' type='button' data-toggle='collapse' data-target='#collapse{$i['itmid']}' aria-expanded='true' aria-controls='collapse{$i['itmid']}'>
							<div class='row'>
								<div class='col-md-1'>
									{$icon}
								</div>
								<div class='col-md'>
									{$i['itmname']}";
									if ($i['inv_qty'] > 1) 
										echo "<b> x " . shortNumberParse($i['inv_qty']) . "</b>";
									echo "
								</div>
							</div>
						</button>
					</h2>
				</div>
				<div id='collapse{$i['itmid']}' class='collapse' aria-labelledby='heading{$i['itmid']}' data-parent='#inventoryAccordian'>
					<div class='card-body'>
						<div class='row'>
							<div class='col-md-1'>
								" . returnIcon($i['itmid'],3.5) . "
							</div>
							<div class='col-md-8 text-left'>
								<b><a href='iteminfo.php?ID={$i['itmid']}'>{$i['itmname']}</a></b> is a {$lt} item.<br />
								<i>{$i['itmdesc']}</i>";
								$start=0;
								for ($enum = 1; $enum <= 3; $enum++) 
								{
									if ($i["effect{$enum}_on"] == 'true') 
									{
										if ($start == 0)
										{
											echo "<br /><b>Effect</b> ";
											$start = 1;
										}
										$einfo = unserialize($i["effect{$enum}"]);
										$einfo['inc_type'] = ($einfo['inc_type'] == 'percent') ? '%' : '';
										$einfo['dir'] = ($einfo['dir'] == 'pos') ? '+' : '-';
										$stats =
											array("energy" => "Energy", "will" => "Will",
												"brave" => "Bravery", "level" => "Level",
												"hp" => "Health", "strength" => "Strength",
												"agility" => "Agility", "guard" => "Guard",
												"labor" => "Labor", "iq" => "IQ",
												"infirmary" => "Infirmary minutes", "dungeon" => "Dungeon minutes",
												"primary_currency" => "Copper Coins", "secondary_currency"
											=> "Chivalry Tokens", "crimexp" => "Experience", "vip_days" =>
												"VIP Days", "luck" => "Luck", "premium_currency" => "Mutton");
										$statformatted = $stats["{$einfo['stat']}"];
										echo "{$einfo['dir']}" . number_format($einfo['inc_amount']) . "{$einfo['inc_type']} {$statformatted}.";
									}
								}
					echo "	</div>
						</div>
						<hr />
						<div class='row'>
							<div class='col-md'>
								<b>Buy</b><br />
								<small>" . shortNumberParse($i['itmbuyprice']) . " Copper Coins</small>
							</div>
							<div class='col-md'>
								<b>Sell</b><br />
								<small>" . shortNumberParse($i['itmsellprice']) . " Copper Coins</small>
							</div>
							<div class='col-md'>
								<b>Total Value</b><br />
								<small>" . shortNumberParse($i['inv_qty_value']) . " Copper Coins</small>
							</div>
							<div class='col-md'>
								<b>Circulating</b><br />
								<small>" . shortNumberParse($total) . "</small>
							</div>
						</div>
						<hr />
						<div class='row'>";
							if ($i['weapon'] > 0)
							{
								echo "
								<div class='col-md'>
									<b>Weapon</b><br />
									<small>" . shortNumberParse($i['weapon']) . "</small>
								</div>";
							}
							if ($i['ammo'] > 0)
							{
								echo "
								<div class='col-md'>
									<b>Projectile</b><br />
									<small><a href='iteminfo.php?ID={$i['ammo']}'>{$api->SystemItemIDtoName($i['ammo'])}</a></small>
								</div>";
							}
							if ($i['armor'] > 0)
							{
								echo "
								<div class='col-md'>
									<b>Armor</b><br />
									<small>" . shortNumberParse($i['armor']) . "</small>
								</div>";
							}
							echo "
						</div>
					</div>
				</div>
			</div>";
		}
		echo "</div>";
        //Save to the log.
        $api->SystemLogsAdd($userid, 'spy', "Successfully spied on " . $api->SystemUserIDtoName($_GET['user']));
		$db->query("INSERT INTO `spy_advantage` (`user`, `spied`) VALUES ('{$userid}', '{$_GET['user']}')");
    }
} //Starting form.
else {
    echo "
        <div class='card'>
            <div class='card-header'>
                Hiring Spy on " . $api->SystemUserIDtoName($_GET['user']) . " [{$_GET['user']}]
            </div>
            <div class='card-body'>
                Spies are useful to gain information on your enemies, such as their stats or equipment. As such, a spy's 
                price increases relative to the difficulty of the target. This price is 500 Copper Coins per level the 
                target has. In the case of " . $api->SystemUserIDtoName($_GET['user']) . ", your cost will be 
                " . shortNumberParse(500 * $r['level']) . " Copper Coins. Spies are not guaranteed to be successful, 
                however if are, you will be granted a 25% Strength/Agility buff on your first strike against " . $api->SystemUserIDtoName($_GET['user']) . ".
                <div class='row'>
                    <div class='col-12 col-sm'>
                        <form action='?user={$_GET['user']}' method='post'>
                    		<input type='hidden' name='do' value='yes'>
                    		<input type='submit' class='btn btn-primary btn-block' value='Hire Spy'>
                    	</form>
                    </div>
                    <div class='col-12 col-sm'>
                        <a href='profile.php?user={$_GET['user']}' class='btn btn-danger btn-block'>Go Back</a>
                    </div>
                </div>
            </div>
        </div>";
}
$h->endpage();