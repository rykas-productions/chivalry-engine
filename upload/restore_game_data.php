<?php
/*
    File: restore_game_data.php
    Created: Restore missing game data from backup
    Info: Imports itemtypes and estates from the backup file
*/

require_once('globals.php');

// Only allow staff to run this
if (!$api->user->getStaffLevel($userid, 'admin')) {
    die('This script can only be run by administrators.');
}

echo "<h3>Game Data Restoration</h3>";

try {
    // Item Types from backup
    $itemTypes = [
        [1, 'Weapons'],
        [2, 'Armor'],
        [3, 'VIP Items'],
        [4, 'Infirmary'],
        [5, 'Dungeon'],
        [6, 'Materials'],
        [7, 'Food'],
        [8, 'Potions'],
        [9, 'Other'],
        [10, 'Holiday Items'],
        [11, 'Scrolls'],
        [12, 'Wedding Rings'],
        [13, 'Badges'],
        [14, 'Seeds'],
        [15, 'Rings'],
        [16, 'Necklaces'],
        [17, 'Pendant'],
        [18, 'Pendants']
    ];
    
    // Estates from backup (simplified structure)
    $estates = [
        [1, 'Nothing', 1, 100, 1],
        [2, 'Branchbound Lean-to', 10000, 188, 1],
        [3, 'Thistlebrook House', 32000, 250, 1],
        [4, 'Mossy Hollow Cottage', 74000, 313, 5],
        [5, 'Cedar Ridge Cottage', 500000, 438, 10],
        [6, 'Lavender Hill Farm', 2000000, 563, 20],
        [7, 'Barleycroft Farmstead', 5000000, 688, 30],
        [9, 'Dairybrook Homestead', 10000000, 844, 40],
        [10, 'Stormwatch Castle', 20000000, 1031, 50],
        [11, 'Stonehaven Hamlet', 55000000, 1250, 75],
        [12, 'Dunehaven', 80000000, 1563, 125],
        [13, 'Alderbrook Village', 125000000, 2094, 200],
        [14, 'Golden Grain Market', 300000000, 2281, 250],
        [15, 'Dragon\'s Hoard Trading Post', 500000000, 2656, 300],
        [16, 'Thunderstrike Citadel', 1000000000, 3375, 400],
        [17, 'Riverside Castle', 2000000000, 4000, 450],
        [18, 'Moonlight Haven', 5000000000, 5000, 600],
        [19, 'Shadowvale Keep', 10000000000, 6250, 666],
        [20, 'Rosewood Manor', 20000000000, 6656, 750],
        [21, 'Misthaven Hall', 30000000000, 7363, 850]
    ];
    
    echo "<div class='alert alert-info'>";
    echo "<h5>Restoring Data from Backup:</h5>";
    echo "<ul class='mb-0'>";
    echo "<li>" . count($itemTypes) . " item types</li>";
    echo "<li>" . count($estates) . " estates</li>";
    echo "<li>Original marriage system (marriage_tmg table)</li>";
    echo "<li>Complete VIP system with 5 VIP items</li>";
    echo "<li>3 towns with detailed descriptions</li>";
    echo "<li>5 shops with immersive lore</li>";
    echo "</ul>";
    echo "</div>";
    
    // Restore Item Types
    echo "<h4>Restoring Item Types:</h4>";
    $itemtype_added = 0;
    $itemtype_updated = 0;
    
    foreach ($itemTypes as $typeData) {
        list($id, $name) = $typeData;
        $name_escaped = $db->escape($name);
        
        // Check if exists
        $existing = $db->fetch_row($db->query("SELECT itmtypeid FROM itemtypes WHERE itmtypeid = {$id}"));
        
        if ($existing) {
            $db->query("UPDATE itemtypes SET itmtypename = '{$name_escaped}' WHERE itmtypeid = {$id}");
            echo "<div class='alert alert-warning'>• Updated: {$name} (ID: {$id})</div>";
            $itemtype_updated++;
        } else {
            $db->query("INSERT INTO itemtypes (itmtypeid, itmtypename) VALUES ({$id}, '{$name_escaped}')");
            echo "<div class='alert alert-success'>✓ Added: {$name} (ID: {$id})</div>";
            $itemtype_added++;
        }
    }
    
    // Restore Estates
    echo "<h4>Restoring Estates:</h4>";
    $estate_added = 0;
    $estate_updated = 0;
    
    foreach ($estates as $estateData) {
        list($id, $name, $price, $will, $level) = $estateData;
        $name_escaped = $db->escape($name);
        
        // Check if exists
        $existing = $db->fetch_row($db->query("SELECT house_id FROM estates WHERE house_id = {$id}"));
        
        if ($existing) {
            $db->query("
                UPDATE estates 
                SET house_name = '{$name_escaped}', house_price = {$price}, house_will = {$will}, house_level = {$level}
                WHERE house_id = {$id}
            ");
            echo "<div class='alert alert-warning'>• Updated: {$name} (Will: {$will}, Level: {$level}, Price: " . number_format($price) . ")</div>";
            $estate_updated++;
        } else {
            $db->query("
                INSERT INTO estates (house_id, house_name, house_price, house_will, house_level) 
                VALUES ({$id}, '{$name_escaped}', {$price}, {$will}, {$level})
            ");
            echo "<div class='alert alert-success'>✓ Added: {$name} (Will: {$will}, Level: {$level}, Price: " . number_format($price) . ")</div>";
            $estate_added++;
        }
    }
    
    // Summary
    // Restore Marriage System from backup
    echo "<h4>Restoring Marriage System:</h4>";
    
    // Create marriage_tmg table with original structure from backup
    $marriage_table_sql = "
        CREATE TABLE IF NOT EXISTS `marriage_tmg` (
            `marriage_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `proposer_id` int(10) unsigned NOT NULL,
            `proposed_id` int(10) unsigned NOT NULL,
            `together` tinyint(3) unsigned NOT NULL DEFAULT '0',
            `happiness` int(11) NOT NULL DEFAULT '0',
            `loyalty` int(11) NOT NULL DEFAULT '0',
            `marriage_date` datetime DEFAULT NULL,
            `proposal_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `proposal_message` text,
            `gifts_sent` int(11) NOT NULL DEFAULT '0',
            `gifts_received` int(11) NOT NULL DEFAULT '0',
            PRIMARY KEY (`marriage_id`),
            KEY `proposer_id` (`proposer_id`),
            KEY `proposed_id` (`proposed_id`),
            KEY `together` (`together`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $db->query($marriage_table_sql);
    echo "<div class='alert alert-success'>✓ Created marriage_tmg table with original structure</div>";
    
    // Check if marriage system files exist and update them if needed
    if (file_exists('marriage.php')) {
        echo "<div class='alert alert-info'>• Marriage system files already exist</div>";
    } else {
        echo "<div class='alert alert-warning'>• Marriage system files need to be created</div>";
    }
    
    // Restore VIP System from backup
    echo "<h4>Restoring VIP System:</h4>";
    
    // Create vip_listing table
    $vip_listing_sql = "
        CREATE TABLE IF NOT EXISTS `vip_listing` (
            `vip_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `vip_item` int(10) unsigned NOT NULL,
            `vip_cost` decimal(10,2) NOT NULL,
            `vip_qty` int(10) unsigned NOT NULL,
            UNIQUE KEY `vip_id` (`vip_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $db->query($vip_listing_sql);
    echo "<div class='alert alert-success'>✓ Created vip_listing table</div>";
    
    // Create vip_market table  
    $vip_market_sql = "
        CREATE TABLE IF NOT EXISTS `vip_market` (
            `vip_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `vip_user` int(10) unsigned NOT NULL,
            `vip_cost` int(10) unsigned NOT NULL,
            `vip_days` int(10) unsigned NOT NULL,
            `vip_deposit` varchar(255) NOT NULL DEFAULT 'false',
            PRIMARY KEY (`vip_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $db->query($vip_market_sql);
    echo "<div class='alert alert-success'>✓ Created vip_market table</div>";
    
    // Create vips_accepted table
    $vips_accepted_sql = "
        CREATE TABLE IF NOT EXISTS `vips_accepted` (
            `vipID` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `vipBUYER` int(10) unsigned NOT NULL DEFAULT 0,
            `vipFOR` int(10) unsigned NOT NULL DEFAULT 0,
            `vipPACKID` int(10) unsigned NOT NULL,
            `vipTIME` int(10) unsigned NOT NULL DEFAULT 0,
            `vipPAYPALID` varchar(255) NOT NULL DEFAULT '',
            PRIMARY KEY (`vipID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $db->query($vips_accepted_sql);
    echo "<div class='alert alert-success'>✓ Created vips_accepted table</div>";
    
    // Insert VIP items from backup
    $vip_items = [
        [44, 92, 4.00, 1],   // VIP item ID 92 for $4
        [30, 12, 1.00, 1],   // Invisibility Potion
        [46, 15, 10.00, 1],  // Gym Pass pack
        [38, 13, 3.00, 1],   // Token pack
        [37, 32, 2.00, 1]    // Theft Protection
    ];
    
    $vip_added = 0;
    foreach ($vip_items as $vip_data) {
        list($vip_id, $item_id, $cost, $qty) = $vip_data;
        
        // Check if already exists
        $existing = $db->fetch_row($db->query("SELECT vip_id FROM vip_listing WHERE vip_id = {$vip_id}"));
        
        if (!$existing) {
            $db->query("INSERT INTO vip_listing (vip_id, vip_item, vip_cost, vip_qty) VALUES ({$vip_id}, {$item_id}, {$cost}, {$qty})");
            echo "<div class='alert alert-success'>✓ Added VIP item: Item {$item_id} for \${$cost}</div>";
            $vip_added++;
        } else {
            echo "<div class='alert alert-warning'>• VIP item {$vip_id} already exists</div>";
        }
    }
    
    // Restore Towns from backup
    echo "<h4>Restoring Towns:</h4>";
    
    $towns = [
        [1, 'Cornrye', 1, 19, 2, 'Nestled amidst rolling hills and golden fields, Cornrye is a quaint village known for its bustling market square and friendly locals. The aroma of freshly baked bread wafts through the air, and the sound of laughter echoes from the tavern.', 110],
        [2, 'Burnbridge', 5, 0, 10, 'Set upon the banks of a wide river, Burnbridge is a town of craftsmen and traders. Smoke billows from the forges of blacksmiths, and the scent of leather fills the air as cobblers work diligently in their workshops.', 110],
        [3, 'Falconworth', 10, 0, 10, 'Perched atop a craggy cliff, Falconworth overlooks the vast expanse of the surrounding countryside. The town is home to skilled falconers who train their majestic birds of prey, and travelers often stop to marvel at their aerial displays.', 97]
    ];
    
    $town_added = 0;
    $town_updated = 0;
    foreach ($towns as $town_data) {
        list($id, $name, $level, $guild_id, $cost, $desc, $minlevel) = $town_data;
        $name_escaped = $db->escape($name);
        $desc_escaped = $db->escape($desc);
        
        // Check if exists
        $existing = $db->fetch_row($db->query("SELECT town_id FROM town WHERE town_id = {$id}"));
        
        if ($existing) {
            $db->query("UPDATE town SET town_name = '{$name_escaped}', town_level = {$level}, town_guild = {$guild_id}, town_cost = {$cost}, town_desc = '{$desc_escaped}', town_minlevel = {$minlevel} WHERE town_id = {$id}");
            echo "<div class='alert alert-warning'>• Updated: {$name} (Level {$level})</div>";
            $town_updated++;
        } else {
            $db->query("INSERT INTO town (town_id, town_name, town_level, town_guild, town_cost, town_desc, town_minlevel) VALUES ({$id}, '{$name_escaped}', {$level}, {$guild_id}, {$cost}, '{$desc_escaped}', {$minlevel})");
            echo "<div class='alert alert-success'>✓ Added: {$name} (Level {$level})</div>";
            $town_added++;
        }
    }
    
    // Restore Shops from backup
    echo "<h4>Restoring Shops:</h4>";
    
    $shops = [
        [1, 1, 'Cornrye Pub', 'Nestled in the heart of the town square, the Cornrye Pub welcomes weary travelers and locals alike to its cozy hearth. With hearty fare, frothy ales, and lively entertainment, this bustling tavern is the perfect place to unwind after a long day\'s journey or to swap tales of adventure with fellow patrons.'],
        [2, 1, 'Cornrye Armory', 'Renowned throughout the land for its fine craftsmanship and sturdy wares, the Cornrye Armory stands as a bastion of defense against the perils of the realm. From gleaming swords and stout shields to finely wrought armor, this venerable establishment equips warriors of all stripes for battle.'],
        [3, 2, 'Charred Goods', 'Tucked away in a corner of the town market, Charred Goods is a curious shop filled with mysterious artifacts and oddities. Rumored to be run by a reclusive alchemist, the shop offers a unique selection of enchanted items, arcane ingredients, and relics of bygone eras.'],
        [4, 3, 'Eagle Eye Inn', 'Perched atop a hill overlooking the town, the Eagle Eye Inn offers breathtaking views and warm hospitality to weary travelers seeking respite from their journey. With comfortable rooms, hearty meals, and friendly service, this charming inn is a favorite among adventurers and tourists alike.'],
        [5, 4, 'Frozen Shop', 'Located in the coldest corner of town, the Frozen Shop specializes in goods that thrive in the icy embrace of winter. From fur-lined cloaks and woolen mittens to warming potions and enchanted talismans, this frosty establishment caters to those brave enough to venture into the chill.']
    ];
    
    $shop_added = 0;
    $shop_updated = 0;
    foreach ($shops as $shop_data) {
        list($id, $town_id, $name, $desc) = $shop_data;
        $name_escaped = $db->escape($name);
        $desc_escaped = $db->escape($desc);
        
        // Check if exists
        $existing = $db->fetch_row($db->query("SELECT shopid FROM shops WHERE shopid = {$id}"));
        
        if ($existing) {
            $db->query("UPDATE shops SET shopname = '{$name_escaped}', shopdescription = '{$desc_escaped}', shoplocation = {$town_id} WHERE shopid = {$id}");
            echo "<div class='alert alert-warning'>• Updated: {$name}</div>";
            $shop_updated++;
        } else {
            $db->query("INSERT INTO shops (shopid, shoplocation, shopname, shopdescription, shopopen, shopclose) VALUES ({$id}, {$town_id}, '{$name_escaped}', '{$desc_escaped}', 0, 0)");
            echo "<div class='alert alert-success'>✓ Added: {$name}</div>";
            $shop_added++;
        }
    }
    
    echo "<div class='mt-4'>";
    echo "<div class='alert alert-success'>";
    echo "<h5><i class='fas fa-check-circle'></i> Game Data Restoration Complete!</h5>";
    echo "<div class='row'>";
    echo "<div class='col-md-2'>";
    echo "<h6>Item Types:</h6>";
    echo "<ul class='mb-0'>";
    echo "<li>{$itemtype_added} new types added</li>";
    echo "<li>{$itemtype_updated} existing types updated</li>";
    echo "</ul>";
    echo "</div>";
    echo "<div class='col-md-2'>";
    echo "<h6>Estates:</h6>";
    echo "<ul class='mb-0'>";
    echo "<li>{$estate_added} new estates added</li>";
    echo "<li>{$estate_updated} existing estates updated</li>";
    echo "</ul>";
    echo "</div>";
    echo "<div class='col-md-2'>";
    echo "<h6>Marriage System:</h6>";
    echo "<ul class='mb-0'>";
    echo "<li>Original table structure restored</li>";
    echo "<li>Ready for marriage functionality</li>";
    echo "</ul>";
    echo "</div>";
    echo "<div class='col-md-2'>";
    echo "<h6>VIP System:</h6>";
    echo "<ul class='mb-0'>";
    echo "<li>{$vip_added} VIP items added</li>";
    echo "<li>3 VIP tables created</li>";
    echo "</ul>";
    echo "</div>";
    echo "<div class='col-md-2'>";
    echo "<h6>Towns:</h6>";
    echo "<ul class='mb-0'>";
    echo "<li>{$town_added} towns added</li>";
    echo "<li>{$town_updated} towns updated</li>";
    echo "</ul>";
    echo "</div>";
    echo "<div class='col-md-2'>";
    echo "<h6>Shops:</h6>";
    echo "<ul class='mb-0'>";
    echo "<li>{$shop_added} shops added</li>";
    echo "<li>{$shop_updated} shops updated</li>";
    echo "</ul>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    
    // Show current state
    echo "<div class='row mt-4'>";
    
    // Item Types Table
    echo "<div class='col-md-6'>";
    echo "<h5>Current Item Types:</h5>";
    echo "<div class='table-responsive'>";
    echo "<table class='table table-sm table-striped'>";
    echo "<thead><tr><th>ID</th><th>Type Name</th></tr></thead>";
    echo "<tbody>";
    
    $types_query = $db->query("SELECT itmtypeid, itmtypename FROM itemtypes ORDER BY itmtypeid ASC");
    while ($type = $db->fetch_row($types_query)) {
        echo "<tr><td>{$type['itmtypeid']}</td><td>{$type['itmtypename']}</td></tr>";
    }
    
    echo "</tbody></table>";
    echo "</div>";
    echo "</div>";
    
    // Estates Table (first 10)
    echo "<div class='col-md-6'>";
    echo "<h5>Current Estates (Sample):</h5>";
    echo "<div class='table-responsive'>";
    echo "<table class='table table-sm table-striped'>";
    echo "<thead><tr><th>ID</th><th>Name</th><th>Will</th><th>Level</th></tr></thead>";
    echo "<tbody>";
    
    $estates_query = $db->query("SELECT house_id, house_name, house_will, house_level FROM estates ORDER BY house_will ASC LIMIT 10");
    while ($estate = $db->fetch_row($estates_query)) {
        echo "<tr>";
        echo "<td>{$estate['house_id']}</td>";
        echo "<td>{$estate['house_name']}</td>";
        echo "<td>{$estate['house_will']}</td>";
        echo "<td>{$estate['house_level']}</td>";
        echo "</tr>";
    }
    
    echo "</tbody></table>";
    echo "<small class='text-muted'>Showing first 10 estates...</small>";
    echo "</div>";
    echo "</div>";
    
    echo "</div>";
    
    // Navigation
    echo "<div class='alert alert-primary mt-4'>";
    echo "<h6>Ready to explore:</h6>";
    echo "<a href='inventory.php' class='btn btn-primary me-2'>View Inventory</a>";
    echo "<a href='estates.php' class='btn btn-success me-2'>Browse Estates</a>";
    echo "<a href='shops.php' class='btn btn-info me-2'>Visit Shops</a>";
    echo "<a href='marriage.php' class='btn btn-warning me-2'>Marriage System</a>";
    echo "<a href='donator.php' class='btn btn-dark'>VIP Store</a>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
}

$h->endpage();
?>