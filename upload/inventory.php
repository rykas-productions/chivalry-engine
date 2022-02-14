<?php
/*
	File:		inventory.php
	Created: 	4/5/2016 at 12:14AM Eastern Time
	Info: 		Displays the player's items and equipment, along with
				actions that you can do with the items.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
$tresder = (Random(100, 999));
$primWeap = ($ir['equip_primary'] > 0) ? $api->SystemItemIDtoName($ir['equip_primary']) : "<i>Unarmed</i>";
$primWeapDam = $db->fetch_single($db->query("/*qc=on*/SELECT `weapon` FROM `items` WHERE `itmid` = {$ir['equip_primary']}"));
$secWeap = ($ir['equip_secondary'] > 0) ? $api->SystemItemIDtoName($ir['equip_secondary']) : "<i>Unarmed</i>";
$secWeapDam = $db->fetch_single($db->query("/*qc=on*/SELECT `weapon` FROM `items` WHERE `itmid` = {$ir['equip_secondary']}"));
$armor = ($ir['equip_armor'] > 0) ? $api->SystemItemIDtoName($ir['equip_armor']) : "<i>No armor</i>";
$armorRating = $db->fetch_single($db->query("/*qc=on*/SELECT `armor` FROM `items` WHERE `itmid` = {$ir['equip_armor']}"));
$potion = ($ir['equip_potion'] > 0) ? $api->SystemItemIDtoName($ir['equip_potion']) : "<i>No potion</i>";
$badge = ($ir['equip_badge'] > 0) ? $api->SystemItemIDtoName($ir['equip_badge']) : "<i>No badge</i>";
echo "
<div class='row'>
    <div class='col-12'>
        <div class='card'>
            <div class='card-header'>
                Your Equipment
            </div>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-12 col-sm-4 col-md-3'>
                        <div class='row'>
                            <div class='col-12'>
                                Primary Weapon
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm-8 col-md-5'>
                        <div class='row'>
                            <div class='col-12 col-sm-3 col-md-4'>
                                " . returnIcon($ir['equip_primary'], 2) . "
                            </div>
                            <div class='col-12 col-sm col-md'>
                                {$primWeap}
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm-4 col-md'>
                        <div class='row'>
                            <div class='col-12'>
                                <small>Rating</small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($primWeapDam) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm col-md'>
                        <div class='row'>
                            <div class='col-12'>
                                <small></small>
                            </div>
                            <div class='col-12'>
                                <a href='unequip.php?type=equip_primary' class='btn btn-primary btn-block'>Unequip</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-12 col-sm-4 col-md-3'>
                        <div class='row'>
                            <div class='col-12'>
                                Secondary Weapon
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm-8 col-md-5'>
                        <div class='row'>
                            <div class='col-12 col-sm-3 col-md-4'>
                                " . returnIcon($ir['equip_secondary'], 2) . "
                            </div>
                            <div class='col-12 col-sm col-md'>
                                {$secWeap}
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm-4 col-md'>
                        <div class='row'>
                            <div class='col-12'>
                                <small>Rating</small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($secWeapDam) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm col-md'>
                        <div class='row'>
                            <div class='col-12'>
                                <small></small>
                            </div>
                            <div class='col-12'>
                                <a href='unequip.php?type=equip_secondary' class='btn btn-primary btn-block'>Unequip</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-12 col-sm-4 col-md-3'>
                        <div class='row'>
                            <div class='col-12'>
                                Armor
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm-8 col-md-5'>
                        <div class='row'>
                            <div class='col-12 col-sm-3 col-md-4'>
                                " . returnIcon($ir['equip_armor'], 2) . "
                            </div>
                            <div class='col-12 col-sm col-md'>
                                {$armor}
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm-4 col-md'>
                        <div class='row'>
                            <div class='col-12'>
                                <small>Rating</small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($armorRating) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm col-md'>
                        <div class='row'>
                            <div class='col-12'>
                                <small></small>
                            </div>
                            <div class='col-12'>
                                <a href='unequip.php?type=equip_armor' class='btn btn-primary btn-block'>Unequip</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-12 col-sm-4 col-md-3'>
                        <div class='row'>
                            <div class='col-12'>
                                Potion
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm-8 col-md-5'>
                        <div class='row'>
                            <div class='col-12 col-sm-3 col-md-4'>
                                " . returnIcon($ir['equip_potion'], 2) . "
                            </div>
                            <div class='col-12 col-sm col-md'>
                                {$potion}
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm-4 col-md'>
                        <div class='row'>
                            <div class='col-12'>
                                <small>&nbsp;</small>
                            </div>
                            <div class='col-12'>
                                &nbsp;
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm col-md'>
                        <div class='row'>
                            <div class='col-12'>
                                <small></small>
                            </div>
                            <div class='col-12'>
                                <a href='unequip.php?type=equip_potion' class='btn btn-primary btn-block'>Unequip</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-12 col-sm-4 col-md-3'>
                        <div class='row'>
                            <div class='col-12'>
                                Profile Badge
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm-8 col-md-5'>
                        <div class='row'>
                            <div class='col-12 col-sm-3 col-md-4'>
                                " . returnIcon($ir['equip_badge'], 2) . "
                            </div>
                            <div class='col-12 col-sm col-md'>
                                {$badge}
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm-4 col-md'>
                        <div class='row'>
                            <div class='col-12'>
                                <small>&nbsp;</small>
                            </div>
                            <div class='col-12'>
                                &nbsp;
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm col-md'>
                        <div class='row'>
                            <div class='col-12'>
                                <small></small>
                            </div>
                            <div class='col-12'>
                                <a href='unequip.php?type=equip_badge' class='btn btn-primary btn-block'>Unequip</a>
                            </div>
                        </div>
                    </div>
                </div>";
                $trinkq=$db->query("SELECT * FROM `user_equips` WHERE `userid` = {$userid} AND `itemid` > 0");
                while ($r=$db->fetch_row($trinkq))
                {
                    echo "<div class='row'>
                    <div class='col-12 col-sm-4 col-md-3'>
                        <div class='row'>
                            <div class='col-12'>
                                " . equipSlotParser($r['equip_slot']) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm-8 col-md-5'>
                        <div class='row'>
                            <div class='col-12 col-sm-3 col-md-4'>
                                " . returnIcon($r['itemid'], 2) . "
                            </div>
                            <div class='col-12 col-sm col-md'>
                                {$api->SystemItemIDtoName($r['itemid'])}
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm-4 col-md'>
                        <div class='row'>
                            <div class='col-12'>
                                <small>&nbsp;</small>
                            </div>
                            <div class='col-12'>
                                &nbsp;
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm col-md'>
                        <div class='row'>
                            <div class='col-12'>
                                <small></small>
                            </div>
                            <div class='col-12'>
                                <a href='unequip.php?type={$r['equip_slot']}' class='btn btn-primary btn-block'>Unequip</a>
                            </div>
                        </div>
                    </div>
                </div>";
                }
                                
            echo"
            </div>
        </div>
    </div>
</div>
<h3><i class='fas fa-fw fa-briefcase'></i> Your Inventory</h3><hr />";
$inv =
    $db->query(
        "/*qc=on*/SELECT `iv`.`inv_qty`, `iv`.`inv_id`,
				`i`.*, `it`.`itmtypename`
                 FROM `inventory` AS `iv`
                 INNER JOIN `items` AS `i`
                 ON `iv`.`inv_itemid` = `i`.`itmid`
                 INNER JOIN `itemtypes` AS `it`
                 ON `i`.`itmtype` = `it`.`itmtypeid`
                 WHERE `iv`.`inv_userid` = {$userid}
                 AND `iv`.`inv_qty` > 0
                 ORDER BY `i`.`itmtype` ASC, `i`.`itmname` ASC");
$lt = "";
echo "
<div class='accordion' id='inventoryAccordian'>";
while ($i = $db->fetch_row($inv))
{
	if ($lt != $i['itmtypename']) {
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
	$icon = ($ir['icons'] == 1) ? returnIcon($i['itmid'],2) : "";
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
		<div class='card-header bg-transparent' id='heading{$i['itmid']}'>
			<h2 class='mb-0'>
				<button class='btn btn-block btn-block text-left' type='button' data-toggle='collapse' data-target='#collapse{$i['itmid']}' aria-expanded='true' aria-controls='collapse{$i['itmid']}'>
					<div class='row'>
						<div class='col-2 col-sm-1'>
							{$icon}
						</div>
						<div class='col-10 col-sm-5 col-md-7'>
							{$i['itmname']}";
							if ($i['inv_qty'] > 1) 
							    echo "<b> x " . shortNumberParse($i['inv_qty']) . "</b>";
							echo "
						</div>
						<div class='col'>
							<div class='row'>
								<div class='col-4'>";
								//Item has a normal use button
									if (($i['effect1_on'] == 'true' || $i['effect2_on'] == 'true' ||  $i['effect3_on'] == 'true') 
										&& ($i['armor'] == 0 && $i['weapon'] == 0 && 
									$i['itmtypename'] != 'Rings' && $i['itmtypename'] != 'Necklaces' 
										    && $i['itmtypename'] != 'Pendants' && $i['itmtypename'] != 'Badges')) {
												echo "<a class='btn btn-block btn-primary btn-block' data-toggle='tooltip' data-placement='top' title='Use item.' href='itemuse.php?item={$i['inv_id']}'><i class='game-icon game-icon-check-mark'></i></a><br />";
										}
										//Box of Random
										if ($i['itmid'] == 33)
										{
											
											echo "<a title='Open {$i['itmname']}.' href='bor.php?tresde={$tresder}' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
												<i class='game-icon game-icon-open-chest'></i>
											</a><br />";
										}
										//Bomb
										if ($i['itmid'] == 28)
										{
					
											echo "<a title='Set charge.' href='bomb.php?action=small' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-mushroom-cloud'></i>
												</a><br />";
										}
										//Medium Bomb
										if ($i['itmid'] == 61)
										{
					
											echo "<a title='Set charge.' href='bomb.php?action=medium' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-mushroom-cloud'></i>
												</a><br />";
										}
										//Large  bomb
										if ($i['itmid'] == 62)
										{
					
											echo "<a title='Set charge.' href='bomb.php?action=large' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-mushroom-cloud'></i>
												</a><br />";
										}
										//2017 Halloween Scratch Ticket
										if ($i['itmid'] == 63)
										{
					
											echo "<a title='Scratch {$i['itmname']}.' href='2017halloween.php?action=ticket' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-check-mark'></i>
												</a><br />";
										}
										//Pumpkin
										if ($i['itmid'] == 64)
										{
					
											echo "<a title='Chuck {$i['itmname']}.' href='bomb.php?action=pumpkin' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-throwing-nall'></i>
												</a><br />";
										}
										//Invis Potion
										if ($i['itmid'] == 68)
										{
					
											echo "<a title='Drink {$i['itmname']}.' href='invispotion.php' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-pouring-chalice'></i>
												</a><br />";
										}
										//2017 Halloween Scratch Ticket
										if ($i['itmid'] == 69)
										{
					
											echo "<a title='Scratch {$i['itmname']}.' href='2017thanksgiving.php?action=ticket' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-check-mark'></i>
												</a><br />";
										}
										//VIP Scratch Ticket
										if ($i['itmid'] == 89)
										{
					
											echo "<a title='Scratch {$i['itmname']}.' href='vipticket.php' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-open-treasure-chest'></i>
												</a><br />";
										}
										//Auto Hexbag Opener
										if ($i['itmid'] == 91)
										{
					
											echo "<a title='Redeem {$i['itmname']}.' href='vipitem.php?item=autohex' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-open-treasure-chest'></i>
												</a><br />";
										}
										//Auto BOR Opener
										if ($i['itmid'] == 92)
										{
					
											echo "<a title='Redeem {$i['itmname']}.' href='vipitem.php?item=autobor' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-open-treasure-chest'></i>
												</a><br />";
										}
										//Mysterious Potion
										if ($i['itmid'] == 123)
										{
					
											echo "<a title='Drink {$i['itmname']}.' href='mysteriouspotion.php' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-pouring-chalice'></i>
												</a><br />";
										}
										//VIP Color Changer
										if ($i['itmid'] == 128)
										{
					
											echo "<a title='Use {$i['itmname']}.' href='vipitem.php?item=vipcolor' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-open-treasure-chest'></i>
												</a><br />";
										}
										//2018 St Patties Scratch Ticket
										if ($i['itmid'] == 137)
										{
					
											echo "<a title='Scratch {$i['itmname']}.' href='2018stpatties.php?action=ticket' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-check-mark'></i>
												</a><br />";
										}
										//Rick roll
										if ($i['itmid'] == 149)
										{
					
											echo "<a title='Set rickroll.' href='bomb.php?action=rickroll' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-check-mark'></i>
												</a><br />";
										}
										//Mining Herb
										if ($i['itmid'] == 177)
										{
					
											echo "<a title='Consume {$i['itmname']}.' href='mine.php?action=herb' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-flame'></i>
												</a><br />";
										}
										//2018 Halloween Scratch Ticket
										if ($i['itmid'] == 189)
										{
					
											echo "<a title='Scratch {$i['itmname']}.' href='2018halloween.php?action=ticket' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-check-mark'></i>
												</a><br />";
										}
										//2018 Thanks Giving Scratch Ticket
										if ($i['itmid'] == 195)
										{
					
											echo "<a title='Scratch {$i['itmname']}.' href='2018thanksgiving.php?action=ticket' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-check-mark'></i>
												</a><br />";
										}
										//Snowball
										if ($i['itmid'] == 202)
										{
					
											echo "<a title='Toss {$i['itmname']}.' href='bomb.php?action=snowball' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-throwing-ball'></i>
												</a><br />";
										}
										//2018 Christmas Scratch Ticket
										if ($i['itmid'] == 203)
										{
					
											echo "<a title='Scratch {$i['itmname']}.' href='2018christmas.php?action=ticket' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-check-mark'></i>
												</a><br />";
										}
										//CID Gym Access Scroll
										if ($i['itmid'] == 205)
										{
					
											echo "<a title='Train with {$i['itmname']}.' href='gym_ca.php' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-weight-lifting-down'></i>
												</a><br />";
										}
										//CID Scratch Ticket
										if ($i['itmid'] == 210)
										{
					
											echo "<a title='Scratch {$i['itmname']}.' href='scratchticket.php?action=cidticket' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-check-mark'></i>
												</a><br />";
										}
										//Assassination Note
										if ($i['itmid'] == 222)
										{
					
											echo "<a title='Complete {$i['itmname']}.' href='bomb.php?action=assassin' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-quill'></i>
												</a><br />";
										}
										//Mining Energy Potion
										if ($i['itmid'] == 227)
										{
					
											echo "<a title='Consume {$i['itmname']}.' href='mine.php?action=potion' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-pouring-chalice'></i>
												</a><br />";
										}
										//2019 Easter Scratch Ticket
										if ($i['itmid'] == 230)
										{
					
											echo "<a title='Scratch {$i['itmname']}.' href='2019easter.php?action=ticket' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-check-mark'></i>
												</a><br />";
										}
										//Sepll Tome Key
										if ($i['itmid'] == 250)
										{
					
											echo "<a title='Unlock tome.' href='spellbook.php' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-padlock-open'></i>
												</a><br />";
										}
										//Will Stimulant
										if ($i['itmid'] == 263)
										{
					
											echo "<a title='Convert {$i['itmname']}.' href='vipitem.php?item=willstim' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-check-mark'></i>
												</a><br />";
										}
										//2019 Halloween Scratch Ticket
										if ($i['itmid'] == 264)
										{
					
											echo "<a title='Scratch {$i['itmname']}.' href='2019halloween.php?action=ticket' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-check-mark'></i>
												</a><br />";
										}
										//2nd Year Ann Scratch Ticket
										if ($i['itmid'] == 268)
										{
					
											echo "<a title='Scratch {$i['itmname']}.' href='scratchticket.php?action=2ndyearann' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-check-mark'></i>
												</a><br />";
										}
										//Scroll of the Adminly
										if ($i['itmid'] == 320)
										{
					
											echo "<a title='Eat Potato.' href='goditem.php' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-check-mark'></i>
												</a><br />";
										}
										//2020 Big Bang Scratch Ticket
										if ($i['itmid'] == 352)
										{
					
											echo "<a title='Scratch {$i['itmname']}.' href='scratchticket.php?action=2020bang' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-check-mark'></i>
												</a><br />";
										}
										//Auto Street Begger VIP
										if ($i['itmid'] == 364)
										{
					
											echo "<a title='Redeem {$i['itmname']}.' href='vipitem.php?item=autobum' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-check-mark'></i>
												</a><br />";
										}
										//2020 Halloween Scratch Ticket
										if ($i['itmid'] == 376)
										{
					
											echo "<a title='Scratch {$i['itmname']}.' href='2020halloween.php?action=ticket' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-check-mark'></i>
												</a><br />";
										}
										//2020 Thanksgiving Scratch Ticket
										if ($i['itmid'] == 391)
										{
					
											echo "<a title='Scratch {$i['itmname']}.' href='2020thanksgiving.php?action=ticket' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-check-mark'></i>
												</a><br />";
										}
										//CID Admin Contact Prayer to God
										if ($i['itmid'] == 407)
										{
										    
										    echo "<a title='Pray to CID Admin.' href='vipitem.php?item=contact' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-check-mark'></i>
												</a><br />";
										}
										//Powered Miner
										if ($i['itmid'] == 424)
										{
										    
										    echo "<a title='Use powered miner' href='vipitem.php?item=autominer' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-check-mark'></i>
												</a><br />";
										}
										//Weapons
										if ($i['weapon'] > 0)
										{
											echo "<a title='Equip {$i['itmname']} as weapon.' href='equip.php?slot=weapon&ID={$i['inv_id']}' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-all-for-one'></i>
												</a><br />";
										}
										//Armor
										if ($i['armor'] > 0)
										{
											echo "<a title='Equip {$i['itmname']} as armor.' href='equip.php?slot=armor&ID={$i['inv_id']}' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-armor-upgrade'></i>
												</a><br />";
										}
										//Badges
										if ($i['itmtypename'] == 'Badges')
										{
											echo "<a title='Equip {$i['itmname']} as profile badge.' href='equip.php?slot=badge&ID={$i['inv_id']}' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-pin'></i>
												</a><br />";
										}
										//Rings
										if ($i['itmtypename'] == 'Rings')
										{
											echo "<a title='Equip {$i['itmname']} as ring trinket.' href='equip.php?slot=ring&ID={$i['inv_id']}' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-ring'></i>
												</a><br />";
										}
										//Necklaces
										if ($i['itmtypename'] == 'Necklaces')
										{
											echo "<a title='Equip {$i['itmname']} as necklace trinket.' href='equip.php?slot=necklace&ID={$i['inv_id']}' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-emerald-necklace'></i>
												</a><br />";
										}
										//Pendants
										if ($i['itmtypename'] == 'Pendants')
										{
											echo "<a title='Equip {$i['itmname']} as pendant trinket.' href='equip.php?slot=pendant&ID={$i['inv_id']}' class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top'>
													<i class='game-icon game-icon-ribbon-medal'></i>
												</a><br />";
										}
									echo "
								</div>
								<div class='col-4'>
									<a class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top' title='Send item.' href='itemsend.php?ID={$i['inv_id']}'><i class='game-icon game-icon-paper-plane'></i></a><br />
								</div>
								<div class='col-4'>
									<a class='btn btn-block btn-primary' data-toggle='tooltip' data-placement='top' title='Sell item.' href='itemsell.php?ID={$i['inv_id']}'><i class='game-icon game-icon-credits-currency'></i></a><br />
								</div>
								
							</div>
						</div>
					</div>
				</button>
			</h2>
		</div>
		<div id='collapse{$i['itmid']}' class='collapse' aria-labelledby='heading{$i['itmid']}' data-parent='#inventoryAccordian'>
			<div class='card-body'>
				<div class='row'>
					<div class='col-2 col-md-1'>
						" . returnIcon($i['itmid'],3.5) . "
					</div>
					<div class='col-8 text-left'>
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
								$statformatted = statParser($einfo['stat']);
								echo "{$einfo['dir']}" . number_format($einfo['inc_amount']) . "{$einfo['inc_type']} {$statformatted}.";
							}
						}
					echo "</div>
					<div class='col-3'>";
					//Potion equipping.
						$potionexclusion=array(17,123,68,138,95,96,148,177);
						if ((($i['itmtypename'] == 'Potions') || ($i['itmtypename'] == 'Food')) && (!in_array($i['itmid'],$potionexclusion)))
						{
							echo "<a title='Equip {$i['itmname']} as potion.' href='equip.php?slot=potion&ID={$i['inv_id']}' class='btn btn-block btn-info' data-toggle='tooltip' data-placement='top'>
									<i class='game-icon game-icon-check-mark'></i>
								</a><br />";
						}
						//Box of Random
						if ($i['itmid'] == 33)
						{
							if ($ir['autobor'] > 0)
							{
								echo "<a title='Open {$i['itmname']}s automatically.' href='autobor.php' class='btn btn-block btn-info' data-toggle='tooltip' data-placement='top'>
									<i class='game-icon game-icon-check-mark'></i>
								</a><br />";
							}
						}
						echo"
					</div>
				</div>
				<hr />
				<div class='row'>
					<div class='col-6 col-md'>
						<b>Buy</b><br />
						<small>" . shortNumberParse($i['itmbuyprice']) . "</small>
					</div>
					<div class='col-6 col-md'>
						<b>Sell</b><br />
						<small>" . shortNumberParse($i['itmsellprice']) . "</small>
					</div>
					<div class='col-6 col-md'>
						<b>Total Value</b><br />
						<small>" . shortNumberParse($i['inv_qty_value']) . "</small>
					</div>
					<div class='col-6 col-md'>
						<b>Circulating</b><br />
						<small>" . shortNumberParse($total) . "</small>
					</div>
				</div>
				<hr />
				<div class='row'>";
					if ($i['weapon'] > 0)
					{
						echo "
						<div class='col'>
							<b>Weapon</b><br />
							<small>" . number_format($i['weapon']) . "</small>
						</div>";
					}
					if ($i['ammo'] > 0)
					{
						echo "
						<div class='col'>
							<b>Projectile</b><br />
							<small>{$api->SystemItemIDtoName($i['ammo'])}</small>
						</div>";
					}
					if ($i['armor'] > 0)
					{
						echo "
						<div class='col'>
							<b>Armor</b><br />
							<small>" . number_format($i['armor']) . "</small>
						</div>";
					}
					echo "
				</div>
			</div>
		</div>
	</div>";
}
echo "</div><br />
<a href='inventdump.php' class='btn btn-block btn-danger'>Dump Inventory</a><br />";
$db->free_result($inv);
$h->endpage();
