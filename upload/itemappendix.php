<?php
/*
	File:		itemappendix.php
	Created: 	8/19/2017 at 6:42PM Eastern Time
	Info: 		Displays all the in-game items, along with the quantity
                of those items in circulation.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");

echo "<h3><i class='fas fa-list'></i> Item Appendix</h3><hr />
This page lists all the items in the game, along with how many are in circulation.
This may be useful for players who do item flipping, or those who are just plain old curious. Hovering over the
item will give you its description. Tapping its name will take you to its info page<br />
<small><a href='?view=all'>View All Items</a> | <a href='searchitem.php'>Search Items</a></small><hr />
<div class='card'><div class='card-body'><div class='row'>";

// Display category badges
$categories = [
    'weapons' => 'Weapons',
    'armor' => 'Armors',
    'rings' => 'Trinkets',
    'badges' => 'Badges',
    'potions' => 'Potions',
    'scrolls' => 'Scrolls',
    'vip items' => 'VIP Items',
    'infirmary' => 'Infirmary',
    'dungeon' => 'Dungeon',
    'materials' => 'Materials',
    'food' => 'Foods',
    'seeds' => 'Seeds',
    'holiday items' => 'Holiday',
    'other' => 'Other'
];

foreach ($categories as $key => $label) {
    echo "<div class='col-auto'>" . createRandomBadge("<a href='?view={$key}'>{$label}</a>") . "</div>";
}

echo "</div></div></div><br />";

// Determine selected view
$view = $_GET['view'] ?? 'weapon';
$allowed_views = array_keys($categories);
$extra_conditions = '';
$order_by = 'itmname ASC';

if (!in_array($view, $allowed_views) && $view !== 'all') {
    alert('danger', "Uh Oh!", "Please select a valid item category type.", true, 'explore.php');
    die($h->endpage());
}

// Build query
if ($view === 'all') {
    $where = "1";
} elseif ($view === 'rings') {
    $itid = $db->fetch_single($db->query("SELECT `itmtypeid` FROM `itemtypes` WHERE `itmtypename` = 'Rings'"));
    $ncid = $db->fetch_single($db->query("SELECT `itmtypeid` FROM `itemtypes` WHERE `itmtypename` = 'Necklaces'"));
    $pnid = $db->fetch_single($db->query("SELECT `itmtypeid` FROM `itemtypes` WHERE `itmtypename` = 'Pendants'"));
    $where = "`itmtype` IN (12, {$itid}, {$ncid}, {$pnid})";
} else {
    // Get the type ID from `itemtypes` if possible
    $view_sanitized = $db->escape(ucfirst($view));
    $typeID = $db->fetch_single($db->query("SELECT `itmtypeid` FROM `itemtypes` WHERE `itmtypename` LIKE '{$view_sanitized}'"));
    if (!$typeID || !is_numeric($typeID)) {
        alert('danger', "Uh Oh!", "Invalid item type detected: {$view_sanitized}", true, 'explore.php');
        die($h->endpage());
    }
    
    if (!$typeID && is_numeric($view)) $typeID = (int)$view;
    $where = "`itmtype` = {$typeID}";
}

$q = $db->query("SELECT * FROM `items` WHERE {$where} AND `itmbuyable` = 'true' ORDER BY {$order_by}");

echo "<div class='accordion' id='inventoryAccordian'>";

// Fetch item types in bulk to avoid N+1 queries
$type_map = [];
$type_q = $db->query("SELECT itmtypeid, itmtypename FROM itemtypes");
while ($type = $db->fetch_row($type_q)) {
    $type_map[$type['itmtypeid']] = $type['itmtypename'];
}

// List each item
while ($r = $db->fetch_row($q)) {
    $type = $type_map[$r['itmtype']] ?? 'Unknown';
    $r['itmdesc'] = htmlentities($r['itmdesc'], ENT_QUOTES);
    $rcon = returnIcon($r['itmid'], 2);
    $total = returnTotalItemCount($r['itmid']);
    $totalbuy = $total * $r['itmbuyprice'];
    $totalsell = $total * $r['itmsellprice'];
    
    echo "
    <div class='card'>
        <div class='card-body' id='heading{$r['itmid']}'>
            <h2 class='mb-0'>
                <button class='btn btn-block text-left' type='button' data-toggle='collapse' data-target='#collapse{$r['itmid']}' aria-expanded='true' aria-controls='collapse{$r['itmid']}'>
                    <div class='row'>
                        <div class='col-md-1'>{$rcon}</div>
                        <div class='col-md'>{$r['itmname']}</div>
                    </div>
                </button>
            </h2>
        </div>
        <div id='collapse{$r['itmid']}' class='collapse' aria-labelledby='heading{$r['itmid']}' data-parent='#inventoryAccordian'>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-md-1'>" . returnIcon($r['itmid'], 3.5) . "</div>
                    <div class='col-md-8 text-left'>
                        <b><a href='iteminfo.php?ID={$r['itmid']}'>{$r['itmname']}</a></b> is a {$type} item.<br />
                        <i>{$r['itmdesc']}</i>";
    
    $start = false;
    for ($enum = 1; $enum <= 3; $enum++) {
        if ($r["effect{$enum}_on"] === 'true') {
            if (!$start) {
                echo "<br /><b>Effect</b> ";
                $start = true;
            }
            $einfo = unserialize($r["effect{$enum}"]);
            $einfo['inc_type'] = $einfo['inc_type'] === 'percent' ? '%' : '';
            $einfo['dir'] = $einfo['dir'] === 'pos' ? '+' : '-';
            $statformatted = statParser($einfo['stat']);
            echo "{$einfo['dir']}" . shortNumberParse($einfo['inc_amount']) . "{$einfo['inc_type']} {$statformatted}. ";
        }
    }
    
    echo "</div></div><hr />
            <div class='row'>
                <div class='col-md'><b>Buy Value</b><br /><small>" . shortNumberParse($totalbuy) . " Copper Coins</small></div>
                <div class='col-md'><b>Sell Value</b><br /><small>" . shortNumberParse($totalsell) . " Copper Coins</small></div>
                <div class='col-md'><b>Circulating</b><br /><small>" . shortNumberParse($total) . "</small></div>
            </div><hr /><div class='row'>";
    
    if ($r['weapon'] > 0) {
        echo "<div class='col-md'><b>Weapon</b><br /><small>" . shortNumberParse($r['weapon']) . "</small></div>";
    }
    if ($r['ammo'] > 0) {
        $ammoName = $api->SystemItemIDtoName($r['ammo']);
        echo "<div class='col-md'><b>Projectile</b><br /><small><a href='iteminfo.php?ID={$r['ammo']}'>{$ammoName}</a></small></div>";
    }
    if ($r['armor'] > 0) {
        echo "<div class='col-md'><b>Armor</b><br /><small>" . shortNumberParse($r['armor']) . "</small></div>";
    }
    
    echo "</div></div></div></div>";
}

echo "</div>";
$h->endpage();
