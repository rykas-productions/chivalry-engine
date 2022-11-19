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
$potionexclusion=array(17,123,68,138,95,96,148,177,227,286,285,258,287);
if (isset($_POST['itemUse']))
{
    if (!empty($_POST['itemUse']))
    {
        $redir = $db->escape($_POST['itemUse']);
        header("Location: {$redir}");
    }
}
$tresder = (Random(100, 999));
$primWeap = ($ir['equip_primary'] > 0) ? $api->SystemItemIDtoName($ir['equip_primary']) : "<i>Unarmed</i>";
$primWeapDam = 0;
$secWeapDam = 0;
$armorRating = 0;
if ($ir['equip_primary'] > 0)
    $primWeapDam = calcWeaponEffectiveness($ir['equip_primary'], $userid);
$secWeap = ($ir['equip_secondary'] > 0) ? $api->SystemItemIDtoName($ir['equip_secondary']) : "<i>Unarmed</i>";
if ($ir['equip_secondary'] > 0)
    $secWeapDam = calcWeaponEffectiveness($ir['equip_secondary'], $userid);
$armor = ($ir['equip_armor'] > 0) ? $api->SystemItemIDtoName($ir['equip_armor']) : "<i>No armor</i>";
if ($ir['equip_armor'] > 0)
    $armorRating = calcArmorEffectiveness($ir['equip_armor'], $userid);
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
	$itemUse = array();
	echo "
	<div class='card'>
		<div class='card-header bg-transparent' id='heading{$i['itmid']}'>
			<h2 class='mb-0'>
				<button class='btn btn-block btn-block text-left' type='button' data-toggle='collapse' data-target='#collapse{$i['itmid']}' aria-expanded='true' aria-controls='collapse{$i['itmid']}'>
					<div class='row'>
						<div class='col-2 col-md-1'>
							{$icon}
						</div>
						<div class='col-10 col-md-5'>
							{$i['itmname']}";
							if ($i['inv_qty'] > 1) 
							    echo "<b> x " . shortNumberParse($i['inv_qty']) . "</b>";
							echo "
						</div>
						<div class='col'>
							<div class='row'>
								<div class='col-12 col-sm-8 col-md-7 col-xl-8'>
                                    <form method='post'>";
								//Item has a normal use button
									if (($i['effect1_on'] == 'true' || $i['effect2_on'] == 'true' ||  $i['effect3_on'] == 'true') 
										&& ($i['armor'] == 0 && $i['weapon'] == 0 && $i['itmtypename'] != 'Rings' && $i['itmtypename'] != 'Necklaces' 
										    && $i['itmtypename'] != 'Pendants' && $i['itmtypename'] != 'Badges')) 
									{
										array_push($itemUse, array("itemuse.php?item={$i['inv_id']}", "Use {$i['itmname']}"));
									}
									//Box of Random
									if ($i['itmid'] == 33)
									{
										array_push($itemUse, array("bor.php?tresde={$tresder}", "Open {$i['itmname']}"));
										if ($ir['autobor'] > 0)
										{
										    array_push($itemUse, array("autobor.php", "Auto Open {$i['itmname']}"));
										}
									}
									//Bomb
									if ($i['itmid'] == 28)
									{
										array_push($itemUse, array("bomb.php?action=small", "Set Charge"));
									}
									//Medium Bomb
									if ($i['itmid'] == 61)
									{
										array_push($itemUse, array("bomb.php?action=medium", "Set Charge"));
									}
									//Large  bomb
									if ($i['itmid'] == 62)
									{
										array_push($itemUse, array("bomb.php?action=large", "Set Charge"));
									}
									//2017 Halloween Scratch Ticket
									if ($i['itmid'] == 63)
									{
										array_push($itemUse, array("2017halloween.php?action=ticket", "Scratch {$i['itmname']}"));
									}
									//Pumpkin
									if ($i['itmid'] == 64)
									{
										array_push($itemUse, array("bomb.php?action=pumpkin", "Chuck {$i['itmname']}"));
									}
									//Invis Potion
									if ($i['itmid'] == 68)
									{
										array_push($itemUse, array("invispotion.php", "Drink {$i['itmname']}"));
									}
									//2017 Halloween Scratch Ticket
									if ($i['itmid'] == 69)
									{
										array_push($itemUse, array("2017thanksgiving.php?action=ticket", "Scratch {$i['itmname']}"));
									}
									//VIP Scratch Ticket
									if ($i['itmid'] == 89)
									{
										array_push($itemUse, array("vipticket.php", "Scratch {$i['itmname']}"));
									}
									//Auto Hexbag Opener
									if ($i['itmid'] == 91)
									{
										array_push($itemUse, array("vipitem.php?item=autohex", "Redeem {$i['itmname']}"));
									}
									//Auto BOR Opener
									if ($i['itmid'] == 92)
									{
										array_push($itemUse, array("vipitem.php?item=autobor", "Redeem {$i['itmname']}"));
									}
									//Mysterious Potion
									if ($i['itmid'] == 123)
									{
										array_push($itemUse, array("mysteriouspotion.php", "Consume {$i['itmname']}"));
									}
									//VIP Color Changer
									if ($i['itmid'] == 128)
									{
										array_push($itemUse, array("vipitem.php?item=vipcolor", "Change VIP Color"));
									}
									//2018 St Patties Scratch Ticket
									if ($i['itmid'] == 137)
									{
										array_push($itemUse, array("2018stpatties.php?action=ticket", "Scratch {$i['itmname']}"));
									}
									//Rick roll
									if ($i['itmid'] == 149)
									{
										array_push($itemUse, array("bomb.php?action=rickroll", "Use Rickroll"));
									}
									//Mining Herb
									if ($i['itmid'] == 177)
									{
										array_push($itemUse, array("mine.php?action=herb", "Consume {$i['itmname']}"));
									}
									//2018 Halloween Scratch Ticket
									if ($i['itmid'] == 189)
									{
										array_push($itemUse, array("2018halloween.php?action=ticket", "Scratch {$i['itmname']}"));
									}
									//2018 Thanks Giving Scratch Ticket
									if ($i['itmid'] == 195)
									{
										array_push($itemUse, array("2018thanksgiving.php?action=ticket", "Scratch {$i['itmname']}"));
									}
									//Snowball
									if ($i['itmid'] == 202)
									{
										array_push($itemUse, array("bomb.php?action=snowball", "Toss {$i['itmname']}"));
									}
									//2018 Christmas Scratch Ticket
									if ($i['itmid'] == 203)
									{
										array_push($itemUse, array("2018christmas.php?action=ticket", "Scratch {$i['itmname']}"));
									}
									//CID Gym Access Scroll
									if ($i['itmid'] == 205)
									{
										array_push($itemUse, array("gym_ca.php", "Train with {$i['itmname']}"));
									}
									//CID Scratch Ticket
									if ($i['itmid'] == 210)
									{
										array_push($itemUse, array("scratchticket.php?action=cidticket", "Scratch {$i['itmname']}"));
									}
									//Assassination Note
									if ($i['itmid'] == 222)
									{
										array_push($itemUse, array("bomb.php?action=assassin", "File {$i['itmname']}"));
									}
									//Mining Energy Potion
									if ($i['itmid'] == 227)
									{
										array_push($itemUse, array("mine.php?action=potion", "Drink {$i['itmname']}"));
									}
									//2019 Easter Scratch Ticket
									if ($i['itmid'] == 230)
									{
										array_push($itemUse, array("2019easter.php?action=ticket", "Scratch {$i['itmname']}"));
									}
									//Sepll Tome Key
									if ($i['itmid'] == 250)
									{
										array_push($itemUse, array("spellbook.php", "Unlock Tome"));
									}
									//Will Stimulant
									if ($i['itmid'] == 263)
									{
										array_push($itemUse, array("vipitem.php?item=willstim", "Convert {$i['itmname']}"));
									}
									//2019 Halloween Scratch Ticket
									if ($i['itmid'] == 264)
									{
										array_push($itemUse, array("2019halloween.php?action=ticket", "Scratch {$i['itmname']}"));
									}
									//2nd Year Ann Scratch Ticket
									if ($i['itmid'] == 268)
									{
										array_push($itemUse, array("scratchticket.php?action=2ndyearann", "Scratch {$i['itmname']}"));
									}
									//Scroll of the Adminly
									if ($i['itmid'] == 320)
									{
										array_push($itemUse, array("goditem.php", "Eat Potato"));
									}
									//2020 Big Bang Scratch Ticket
									if ($i['itmid'] == 352)
									{
										array_push($itemUse, array("scratchticket.php?action=2020bang", "Scratch {$i['itmname']}"));
									}
									//Auto Street Begger VIP
									if ($i['itmid'] == 364)
									{
										array_push($itemUse, array("vipitem.php?item=autobum", "Redeem {$i['itmname']}"));
									}
									//2020 Halloween Scratch Ticket
									if ($i['itmid'] == 376)
									{
										array_push($itemUse, array("2020halloween.php?action=ticket", "Scratch {$i['itmname']}"));
									}
									//2020 Thanksgiving Scratch Ticket
									if ($i['itmid'] == 391)
									{
										array_push($itemUse, array("2020thanksgiving.php?action=ticket", "Scratch {$i['itmname']}"));
									}
									//CID Admin Contact Prayer to God
									if ($i['itmid'] == 407)
									{
									    array_push($itemUse, array("vipitem.php?item=contact", "Pray to CID Admin"));
									}
									//Powered Miner
									if ($i['itmid'] == 424)
									{
									    array_push($itemUse, array("vipitem.php?item=autominer", "Setup {$i['itmname']}"));
									}
									//2022 Halloween ticket
									if ($i['itmid'] == 449)
									{
									    array_push($itemUse, array("2022halloween.php?action=ticket", "Scratch {$i['itmname']}"));
									}
									//Weapons
									if ($i['weapon'] > 0)
									{
										array_push($itemUse, array("equip.php?slot=weapon&ID={$i['inv_id']}", "Equip {$i['itmname']} as Weapon"));
									}
									//Armor
									if ($i['armor'] > 0)
									{
										array_push($itemUse, array("equip.php?slot=armor&ID={$i['inv_id']}", "Equip {$i['itmname']} as Armor"));
									}
									//Badges
									if ($i['itmtypename'] == 'Badges')
									{
										array_push($itemUse, array("equip.php?slot=badge&ID={$i['inv_id']}", "Equip {$i['itmname']} as Badge"));
									}
									//Rings
									if ($i['itmtypename'] == 'Rings')
									{
										array_push($itemUse, array("equip.php?slot=ring&ID={$i['inv_id']}", "Equip {$i['itmname']} as Ring"));
									}
									//Necklaces
									if ($i['itmtypename'] == 'Necklaces')
									{
										array_push($itemUse, array("equip.php?slot=necklace&ID={$i['inv_id']}", "Equip {$i['itmname']} as Necklace"));
									}
									//Pendants
									if ($i['itmtypename'] == 'Pendants')
									{
										array_push($itemUse, array("equip.php?slot=pendant&ID={$i['inv_id']}", "Equip {$i['itmname']} as Pendant"));
									}
									//Potion equipping.
									if ((($i['itmtypename'] == 'Potions') || ($i['itmtypename'] == 'Food')) && (!in_array($i['itmid'],$potionexclusion)))
									{
									    array_push($itemUse, array("equip.php?slot=potion&ID={$i['inv_id']}", "Equip {$i['itmname']} as Potion"));
									}
									array_push($itemUse, array("itemsend.php?ID={$i['inv_id']}", "Send {$i['itmname']}"));
									array_push($itemUse, array("itemmarket.php?action=add&ID={$i['itmid']}", "List {$i['itmname']} on Market"));
									array_push($itemUse, array("itemsell.php?ID={$i['inv_id']}", "Sell {$i['itmname']}"));
									$options = "";
									foreach ($itemUse as $k => $v)
									{
									    $options .= "<option value='{$v[0]}'>{$v[1]}</option>";
									}
									echo
									"  <select name='itemUse' class='form-control' type='dropdown'>
							                 $options
                                       </select>
								</div>
								<div class='col-12 col-sm-4 col-md-5 col-xl-4'>
									<input type='submit' class='btn btn-primary btn-block' value='Confirm'>
								</div>
                                </form>
							</div>
						</div>
					</div>
				</button>
			</h2>
		</div>
		<div id='collapse{$i['itmid']}' class='collapse' aria-labelledby='heading{$i['itmid']}' data-parent='#inventoryAccordian'>
			<div class='card-body'>
				<div class='row'>
					<div class='col-2 col-lg-1'>
						" . returnIcon($i['itmid'],3.5) . "
					</div>
					<div class='col text-left'>
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
				</div>
				<hr />
				<div class='row'>
					<div class='col-6 col-md'>
						<b>Buy</b><br />
						<small>" . shortNumberParse($i['itmbuyprice']) . " Copper Coins</small>
					</div>
					<div class='col-6 col-md'>
						<b>Sell</b><br />
						<small>" . shortNumberParse($i['itmsellprice']) . " Copper Coins</small>
					</div>
					<div class='col-6 col-md'>
						<b>Total Value</b><br />
						<small>" . shortNumberParse($i['inv_qty_value']) . " Copper Coins</small>
					</div>
					<div class='col-6 col-md'>
						<b>Circulating</b><br />
						<small>" . shortNumberParse($total) . "</small>
					</div>
				</div>";
					if (($i['weapon'] > 0) || ($i['ammo'] > 0) || ($i['armor'] > 0))
			    {
				    echo"
				    <hr />
				    <div class='row'>";
					if ($i['weapon'] > 0)
					{
						echo "
						<div class='col'>
							<b>Weapon</b><br />
							<small>" . shortNumberParse($i['weapon']) . "</small>
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
							<small>" . shortNumberParse($i['armor']) . "</small>
						</div>";
					}
					echo "
				</div>";
			    }
					echo"
			</div>
		</div>
	</div>";
}
echo "</div><br />
<a href='inventdump.php' class='btn btn-block btn-danger'>Dump Inventory</a><br />";
$db->free_result($inv);
$h->endpage();
