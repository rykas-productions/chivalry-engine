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
$itemActions = [
    28 => [["bomb.php?action=small", "Set Bomb"]],
    33 => [["bor.php?tresde={$tresder}", "Open"]],
    33 => [["autobor.php", "Auto Open"]],
    61 => [["bomb.php?action=medium", "Set Bomb"]],
    62 => [["bomb.php?action=large", "Set Bomb"]],
    63 => [["2017halloween.php?action=ticket", "Scratch"]],
    64 => [["bomb.php?action=pumpkin", "Chuck"]],
    68 => [["invispotion.php", "Drink"]],
    69 => [["2017thanksgiving.php?action=ticket", "Scratch"]],
    89 => [["vipticket.php", "Scratch"]],
    91 => [["vipitem.php?item=autohex", "Redeem"]],
    92 => [["vipitem.php?item=autobor", "Redeem"]],
    123 => [["mysteriouspotion.php", "Drink"]],
    128 => [["vipitem.php?item=vipcolor", "Change VIP Color"]],
    137 => [["2018stpatties.php?action=ticket", "Scratch"]],
    149 => [["bomb.php?action=rickroll", "Set Rick Roll"]],
    177 => [["mine.php?action=herb", "Eat"]],
    189 => [["2018halloween.php?action=ticket", "Scratch"]],
    195 => [["2018thanksgiving.php?action=ticket", "Scratch"]],
    202 => [["bomb.php?action=snowball", "Throw"]],
    203 => [["2018christmas.php?action=ticket", "Scratch"]],
    205 => [["gym_ca.php", "Train"]],
    210 => [["scratchticket.php?action=cidticket", "Scratch"]],
    222 => [["bomb.php?action=assassin", "Place Hit"]],
    227 => [["mine.php?action=potion", "Drink"]],
    230 => [["2019easter.php?action=ticket", "Scratch"]],
    250 => [["spellbook.php", "Unlock Tome"]],
    258 => [["potion.php?potion=poison", "Poison Weapon"]],
    263 => [["vipitem.php?item=willstim", "Convert"]],
    264 => [["2019halloween.php?action=ticket", "Scratch"]],
    268 => [["scratchticket.php?action=2ndyearann", "Scratch"]],
    320 => [["goditem.php", "Eat Potato"]],
    352 => [["scratchticket.php?action=2020bang", "Scratch"]],
    364 => [["vipitem.php?item=autobum", "Redeem"]],
    376 => [["2020halloween.php?action=ticket", "Scratch"]],
    391 => [["2020thanksgiving.php?action=ticket", "Scratch"]],
    407 => [["vipitem.php?item=contact", "Contact CID Admin"]],
    424 => [["vipitem.php?item=autominer", "Configure"]],
    449 => [["2022halloween.php?action=ticket", "Scratch"]],
    514 => [["scratchticket.php?action=24halloween", "Scratch"]]
    // ... Add more here
    ];

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
$primWeap = ($ir['equip_primary'] > 0) ? $api->SystemItemIDtoName($ir['equip_primary']) : "<i>No primary</i>";
$primWeapDam = 0;
$secWeapDam = 0;
$armorRating = 0;
if ($ir['equip_primary'] > 0)
    $primWeapDam = calcWeaponEffectiveness($ir['equip_primary'], $userid);
$secWeap = ($ir['equip_secondary'] > 0) ? $api->SystemItemIDtoName($ir['equip_secondary']) : "<i>No secondary</i>";
if ($ir['equip_secondary'] > 0)
    $secWeapDam = calcWeaponEffectiveness($ir['equip_secondary'], $userid);
$armor = ($ir['equip_armor'] > 0) ? $api->SystemItemIDtoName($ir['equip_armor']) : "<i>No armor</i>";
if ($ir['equip_armor'] > 0)
    $armorRating = calcArmorEffectiveness($ir['equip_armor'], $userid);
$potion = ($ir['equip_potion'] > 0) ? $api->SystemItemIDtoName($ir['equip_potion']) : "<i>No potion</i>";
$badge = ($ir['equip_badge'] > 0) ? $api->SystemItemIDtoName($ir['equip_badge']) : "<i>No badge</i>";
echo "<div class='row'>
        <div class='col-12'>
            <div class='card'>
                <div class='card-header'>
                    Your Equipment
                </div>
                <div class='card-body text-center'>
                    <div class='row'>
                        <div class='col-12'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Primary Weapon (" . shortNumberParse($primWeapDam) . ")</b></small>
                                </div>
                            </div>
                            <div class='row'>
                                <div class='col-12 col-sm-auto'>
                                    <a href='iteminfo.php?ID={$ir['equip_primary']}'>" . returnIcon($ir['equip_primary'], 4) . "
                                </div>
                                <div class='col-12 col-sm'>
                                    {$primWeap}</a>
                                </div>
                                <div class='col-12 col-sm-auto'>
                                    <a href='unequip.php?type=equip_secondary' class='btn btn-primary btn-block'>Unequip</a>
                                </div>
                            </div>
                        </div>
                        <div class='col-12'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Secondary Weapon (" . shortNumberParse($secWeapDam) . ")</b></small>
                                </div>
                            </div>
                            <div class='row'>
                                <div class='col-auto'>
                                    <a href='iteminfo.php?ID={$ir['equip_secondary']}'>" . returnIcon($ir['equip_secondary'], 4) . "
                                </div>
                                <div class='col'>
                                    {$secWeap}</a>
                                </div>
                                <div class='col-auto'>
                                    <a href='unequip.php?type=equip_secondary' class='btn btn-primary btn-block'>Unequip</a>
                                </div>
                            </div>
                        </div>
                        <div class='col-12'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Armor (" . shortNumberParse($armorRating) . ")</b></small>
                                </div>
                            </div>
                            <div class='row'>
                                <div class='col-auto'>
                                    <a href='iteminfo.php?ID={$ir['equip_armor']}'>" . returnIcon($ir['equip_armor'], 4) . "
                                </div>
                                <div class='col'>
                                    {$armor}</a>
                                </div>
                                <div class='col-auto'>
                                    <a href='unequip.php?type=equip_armor' class='btn btn-primary btn-block'>Unequip</a>
                                </div>
                            </div>
                        </div>
                        <div class='col-12'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Potion</b></small>
                                </div>
                            </div>
                            <div class='row'>
                                <div class='col-auto'>
                                    <a href='iteminfo.php?ID={$ir['equip_potion']}'>" . returnIcon($ir['equip_potion'], 4) . "
                                </div>
                                <div class='col'>
                                    {$potion}</a>
                                </div>
                                <div class='col-auto'>
                                    <a href='unequip.php?type=equip_potion' class='btn btn-primary btn-block'>Unequip</a>
                                </div>
                            </div>
                        </div>
                        <div class='col-12'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Profile Badge</b></small>
                                </div>
                            </div>
                            <div class='row'>
                                <div class='col-auto'>
                                    <a href='iteminfo.php?ID={$ir['equip_badge']}'>" . returnIcon($ir['equip_badge'], 4) . "
                                </div>
                                <div class='col'>
                                    {$badge}</a>
                                </div>
                                <div class='col-auto'>
                                    <a href='unequip.php?type=equip_badge' class='btn btn-primary btn-block'>Unequip</a>
                                </div>
                            </div>
                        </div>";
                        $trinkq=$db->query("SELECT * FROM `user_equips` WHERE `userid` = {$userid} AND `itemid` > 0");
                        while ($r=$db->fetch_row($trinkq))
                        {
                            echo "
                            <div class='col-12'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>" . equipSlotParser($r['equip_slot']) . "</b></small>
                                </div>
                            </div>
                            <div class='row'>
                                <div class='col-auto'>
                                    <a href='iteminfo.php?ID={$r['itemid']}'>" . returnIcon($r['itemid'], 4) . "
                                </div>
                                <div class='col'>
                                    {$api->SystemItemIDtoName($r['itemid'])}</a>
                                </div>
                                <div class='col-auto'>
                                    <a href='unequip.php?type={$r['equip_slot']}' class='btn btn-primary btn-block'>Unequip</a>
                                </div>
                            </div>
                        </div>";
                        }
                                    
                    echo"
                    </div>
                </div>
            </div>
        </div>
    </div>";
            alert('secondary', "", "<h4><i class='fas fa-fw fa-briefcase'></i> Your Inventory</h4>", false);
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
while ($i = $db->fetch_row($inv)) {
    if ($lt != $i['itmtypename']) {
        $lt = $i['itmtypename'];
        echo "<div class='card'><div class='card-body'><h4 class='mb-0'>{$lt}</h4></div></div>";
    }
    
    $i['itmdesc'] = htmlentities($i['itmdesc'], ENT_QUOTES);
    $icon = ($ir['icons'] == 1) ? returnIcon($i['itmid'], 2) : "";
    $i['inv_qty_value'] = $i['inv_qty'] * $i['itmsellprice'];
    $total = returnTotalItemCount($i['itmid']);
    $itemUse = getItemUses($i, $tresder, $ir, $potionexclusion);
    
    $options = "";
    foreach ($itemUse as $v) {
        $options .= "<option value='{$v[0]}'>{$v[1]}</option>";
    }
    
    // Echo the block here (using HEREDOC or output buffer for maintainability)
    echo "
    <div class='card'>
        <div class='card-header bg-transparent' id='heading{$i['itmid']}'>
            <h2 class='mb-0'>
                <button class='btn btn-block text-left' type='button' data-toggle='collapse' data-target='#collapse{$i['itmid']}'>
                    <div class='row'>
                        <div class='col-2 col-md-1'>{$icon}</div>
                        <div class='col-10 col-md-5'>{$i['itmname']}" . ($i['inv_qty'] > 1 ? "<b> x " . shortNumberParse($i['inv_qty']) . "</b>" : "") . "</div>
                        <div class='col'>
                            <form method='post'>
                                <div class='row'>
                                    <div class='col-12 col-sm-8 col-md-7 col-xl-8'>
                                        <select name='itemUse' class='form-control'>$options</select>
                                    </div>
                                    <div class='col-12 col-sm-4 col-md-5 col-xl-4'>
                                        <input type='submit' class='btn btn-primary btn-block' value='Confirm'>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </button>
            </h2>
        </div>
        <div id='collapse{$i['itmid']}' class='collapse' data-parent='#inventoryAccordian'>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-2 col-lg-1'>" . returnIcon($i['itmid'], 3.5) . "</div>
                    <div class='col text-left'>
                        <b><a href='iteminfo.php?ID={$i['itmid']}'>{$i['itmname']}</a></b> is a {$lt} item.<br />
                        <i>{$i['itmdesc']}</i>";
    
    // Effects
    $start = 0;
    for ($enum = 1; $enum <= 3; $enum++) {
        if ($i["effect{$enum}_on"] === 'true') {
            if ($start == 0) {
                echo "<br /><b>Effect</b> ";
                $start = 1;
            }
            $einfo = unserialize($i["effect{$enum}"]);
            $einfo['inc_type'] = ($einfo['inc_type'] === 'percent') ? '%' : '';
            $einfo['dir'] = ($einfo['dir'] === 'pos') ? '+' : '-';
            echo "{$einfo['dir']}" . number_format($einfo['inc_amount']) . "{$einfo['inc_type']} " . statParser($einfo['stat']) . ". ";
        }
    }
    
    echo "
                    </div>
                </div>
                <hr />
                <div class='row'>
                    <div class='col-6 col-md'><b>Buy</b><br /><small>{$i['itmbuyprice']} Copper Coins</small></div>
                    <div class='col-6 col-md'><b>Sell</b><br /><small>{$i['itmsellprice']} Copper Coins</small></div>
                    <div class='col-6 col-md'><b>Total Value</b><br /><small>{$i['inv_qty_value']} Copper Coins</small></div>
                    <div class='col-6 col-md'><b>Circulating</b><br /><small>{$total}</small></div>
                </div>";
    
    // Equip stats
    if ($i['weapon'] || $i['ammo'] || $i['armor']) {
        echo "<hr /><div class='row'>";
        if ($i['weapon']) echo "<div class='col'><b>Weapon</b><br /><small>" . shortNumberParse($i['weapon']) . "</small></div>";
        if ($i['ammo']) echo "<div class='col'><b>Projectile</b><br /><small>{$api->SystemItemIDtoName($i['ammo'])}</small></div>";
        if ($i['armor']) echo "<div class='col'><b>Armor</b><br /><small>" . shortNumberParse($i['armor']) . "</small></div>";
        echo "</div>";
    }
    
    echo "</div></div></div>";
}
echo "</div><br />
<a href='inventdump.php' class='btn btn-block btn-danger'>Dump Inventory</a><br />";
$db->free_result($inv);
$h->endpage();

function getItemUses($i, $tresder, $ir, $potionexclusion) {
    global $itemActions;
    
    $uses = [];
    
    // Default effect use
    if (($i['effect1_on'] === 'true' || $i['effect2_on'] === 'true' || $i['effect3_on'] === 'true')
        && $i['armor'] == 0 && $i['weapon'] == 0
        && !in_array($i['itmtypename'], ['Rings', 'Necklaces', 'Pendants', 'Badges'])) {
            $uses[] = ["itemuse.php?item={$i['inv_id']}", "Use {$i['itmname']}"];
        }
        
        // Static mappings
        if (isset($itemActions[$i['itmid']])) {
            foreach ($itemActions[$i['itmid']] as $action) {
                $uses[] = [$action[0], "{$action[1]} {$i['itmname']}"];
            }
        }
        
        // Equip logic
        if ($i['weapon'] > 0) {
            $uses[] = ["equip.php?slot=weapon&ID={$i['inv_id']}", "Equip as Weapon"];
        }
        if ($i['armor'] > 0) {
            $uses[] = ["equip.php?slot=armor&ID={$i['inv_id']}", "Equip as Armor"];
        }
        if ($i['itmtypename'] === 'Badges') {
            $uses[] = ["equip.php?slot=badge&ID={$i['inv_id']}", "Equip as Badge"];
        }
        if ($i['itmtypename'] === 'Rings') {
            $uses[] = ["equip.php?slot=ring&ID={$i['inv_id']}", "Equip as Ring"];
        }
        if ($i['itmtypename'] === 'Necklaces') {
            $uses[] = ["equip.php?slot=necklace&ID={$i['inv_id']}", "Equip as Necklace"];
        }
        if ($i['itmtypename'] === 'Pendants') {
            $uses[] = ["equip.php?slot=pendant&ID={$i['inv_id']}", "Equip as Pendant"];
        }
        if (in_array($i['itmtypename'], ['Potions', 'Food']) && !in_array($i['itmid'], $potionexclusion)) {
            $uses[] = ["equip.php?slot=potion&ID={$i['inv_id']}", "Equip as Potion"];
        }
        
        // Always available actions
        $uses[] = ["itemsend.php?ID={$i['inv_id']}", "Send"];
        $uses[] = ["itemmarket.php?action=add&ID={$i['itmid']}", "List on Market"];
        $uses[] = ["itemsell.php?ID={$i['inv_id']}", "Sell"];
        
        return $uses;
}
