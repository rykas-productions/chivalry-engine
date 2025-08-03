<?php
/*
    File: equip_modern.php
    Created: Modern version of equip.php with better UI
    Info: Allows equipping of armor and weapons with modern interface
*/
require('globals.php');

if (!isset($_GET['slot'])) {
    $_GET['slot'] = '';
}

// Add modern CSS
echo '<style>
.equip-form {
    max-width: 600px;
    margin: 0 auto;
}
.equip-card {
    border: none;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    border-radius: 10px;
    overflow: hidden;
}
.equip-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
}
.slot-option {
    padding: 15px;
    margin: 10px 0;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
}
.slot-option:hover {
    border-color: #667eea;
    background: #f8f9ff;
}
.slot-option input[type="radio"] {
    margin-right: 10px;
}
.item-preview {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}
</style>';

switch ($_GET['slot']) {
    case 'weapon':
        weapon();
        break;
    case 'armor':
        armor();
        break;
    default:
        die();
        break;
}

function weapon()
{
    global $db, $h, $userid, $ir, $api;
    $safe_id = filter_input(INPUT_GET, 'ID', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    
    $id = $db->query("SELECT `weapon`, `itmid`, `itmname`, `inv_id`, `inv_itemid`, `weapon`, `armor`
                    FROM `inventory` AS `iv`
                    LEFT JOIN `items` AS `it`
                    ON `iv`.`inv_itemid` = `it`.`itmid`
                    WHERE `iv`.`inv_id` = {$safe_id}
                    AND `iv`.`inv_userid` = {$userid}
                    LIMIT 1");
    
    if ($db->num_rows($id) == 0) {
        $db->free_result($id);
        alert('danger', "Uh Oh!", "This item does not exist.", true, 'inventory.php');
        die($h->endpage());
    } else {
        $r = $db->fetch_row($id);
        $db->free_result($id);
    }
    
    if (!$r['weapon']) {
        alert('danger', "Uh Oh!", "The item you are trying to equip is not a weapon.", true, 'inventory.php');
        die($h->endpage());
    }
    
    if (isset($_POST['type'])) {
        if (!in_array($_POST['type'], array("equip_primary", "equip_secondary"), true)) {
            alert('danger', "Uh Oh!", "You cannot equip a weapon to an invalid slot.", true, 'inventory.php');
            die($h->endpage());
        }
        
        if ($ir[$_POST['type']] > 0) {
            $api->user->giveItem($userid, $ir[$_POST['type']], 1);
            $slot = ($_POST['type'] == 'equip_primary') ? 'Primary Weapon' : 'Secondary Weapon';
            $weapname = $db->fetch_single($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$ir[$_POST['type']]}"));
            $api->game->addLog($userid, 'equip', "Unequipped {$weapname} as their {$slot}");
        }
        
        $slot_name = ($_POST['type'] == "equip_primary") ? "Primary Weapon" : "Secondary Weapon";
        
        $api->user->takeItem($userid, $r['itmid'], 1);
        $db->query("UPDATE `users` SET `{$_POST['type']}` = {$r['itmid']} WHERE `userid` = {$userid}");
        $api->game->addLog($userid, 'equip', "Equipped {$r['itmname']} as their {$slot_name}.");
        
        alert('success', "Success!", "You have successfully equipped {$r['itmname']} as your {$slot_name}. 
            If you had a previous weapon there, it was moved to your inventory.", true, 'inventory.php');
    } else {
        // Modern form design
        echo "<div class='equip-form'>";
        echo "<div class='card equip-card'>";
        echo "<div class='equip-header'>";
        echo "<h3 class='mb-0'><i class='fas fa-sword'></i> Equip Weapon</h3>";
        echo "</div>";
        echo "<div class='card-body'>";
        
        echo "<div class='item-preview'>";
        echo "<h5 class='text-primary'><i class='fas fa-cube'></i> {$r['itmname']}</h5>";
        echo "<p class='mb-0 text-muted'>Select a weapon slot to equip this item</p>";
        echo "</div>";
        
        echo "<form action='?slot=weapon&ID={$safe_id}' method='post'>";
        echo "<div class='mb-3'>";
        
        // Primary weapon option
        echo "<label class='slot-option'>";
        echo "<input type='radio' name='type' value='equip_primary' checked>";
        echo "<i class='fas fa-hand-rock text-danger'></i> <strong>Primary Weapon Slot</strong>";
        if ($ir['equip_primary'] > 0) {
            $current_primary = $db->fetch_single($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$ir['equip_primary']}"));
            echo "<br><small class='text-muted'>Currently equipped: {$current_primary}</small>";
        } else {
            echo "<br><small class='text-success'>Slot is empty</small>";
        }
        echo "</label>";
        
        // Secondary weapon option
        echo "<label class='slot-option'>";
        echo "<input type='radio' name='type' value='equip_secondary'>";
        echo "<i class='fas fa-shield-alt text-primary'></i> <strong>Secondary Weapon Slot</strong>";
        if ($ir['equip_secondary'] > 0) {
            $current_secondary = $db->fetch_single($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$ir['equip_secondary']}"));
            echo "<br><small class='text-muted'>Currently equipped: {$current_secondary}</small>";
        } else {
            echo "<br><small class='text-success'>Slot is empty</small>";
        }
        echo "</label>";
        
        echo "</div>";
        
        echo "<div class='alert alert-info'>";
        echo "<i class='fas fa-info-circle'></i> If you have a weapon already equipped in the selected slot, it will be moved back to your inventory.";
        echo "</div>";
        
        echo "<div class='d-grid gap-2'>";
        echo "<button type='submit' class='btn btn-primary btn-lg'>";
        echo "<i class='fas fa-check'></i> Equip Weapon";
        echo "</button>";
        echo "<a href='inventory.php' class='btn btn-secondary'>";
        echo "<i class='fas fa-arrow-left'></i> Back to Inventory";
        echo "</a>";
        echo "</div>";
        
        echo "</form>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
    $h->endpage();
}

function armor()
{
    global $db, $h, $userid, $ir, $api;
    $safe_id = filter_input(INPUT_GET, 'ID', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    
    $id = $db->query("SELECT `armor`, `itmid`, `itmname`, `inv_id`, `inv_itemid`, `weapon`, `armor`
                    FROM `inventory` AS `iv`
                    LEFT JOIN `items` AS `it`
                    ON `iv`.`inv_itemid` = `it`.`itmid`
                    WHERE `iv`.`inv_id` = {$safe_id}
                    AND `iv`.`inv_userid` = $userid
                    LIMIT 1");
    
    if ($db->num_rows($id) == 0) {
        $db->free_result($id);
        alert('danger', "Uh Oh!", "The item you're trying to equip does not exist.", true, 'inventory.php');
        die($h->endpage());
    } else {
        $r = $db->fetch_row($id);
        $db->free_result($id);
    }
    
    if (!$r['armor']) {
        alert('danger', "Uh Oh!", "The item you're trying to equip cannot be equipped as armor.", true, 'inventory.php');
        die($h->endpage());
    }
    
    if (isset($_POST['type'])) {
        if ($_POST['type'] !== 'equip_armor') {
            alert('danger', "Uh Oh!", "You cannot equip an armor to an invalid slot.", true, 'inventory.php');
            die($h->endpage());
        }
        
        if ($ir['equip_armor'] > 0) {
            $api->user->giveItem($userid, $ir['equip_armor'], 1);
            $armorname = $db->fetch_single($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$ir['equip_armor']}"));
            $api->game->addLog($userid, 'equip', "Unequipped {$armorname} as their armor.");
        }
        
        $api->user->takeItem($userid, $r['itmid'], 1);
        $db->query("UPDATE `users` SET `equip_armor` = {$r['itmid']} WHERE `userid` = {$userid}");
        $api->game->addLog($userid, 'equip', "Equipped {$r['itmname']} as their armor.");
        
        alert('success', "Success!", "You have equipped your {$r['itmname']} into your armor slot. 
            If you had armor there previously, it's been moved to your inventory.", true, 'inventory.php');
    } else {
        // Modern form design
        echo "<div class='equip-form'>";
        echo "<div class='card equip-card'>";
        echo "<div class='equip-header'>";
        echo "<h3 class='mb-0'><i class='fas fa-vest'></i> Equip Armor</h3>";
        echo "</div>";
        echo "<div class='card-body'>";
        
        echo "<div class='item-preview'>";
        echo "<h5 class='text-primary'><i class='fas fa-shield-alt'></i> {$r['itmname']}</h5>";
        echo "<p class='mb-0 text-muted'>Confirm to equip this armor</p>";
        echo "</div>";
        
        echo "<form action='?slot=armor&ID={$safe_id}' method='post'>";
        
        echo "<div class='slot-option mb-3'>";
        echo "<i class='fas fa-vest text-secondary'></i> <strong>Armor Slot</strong>";
        if ($ir['equip_armor'] > 0) {
            $current_armor = $db->fetch_single($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$ir['equip_armor']}"));
            echo "<br><small class='text-muted'>Currently equipped: {$current_armor}</small>";
        } else {
            echo "<br><small class='text-success'>Slot is empty</small>";
        }
        echo "</div>";
        
        echo "<div class='alert alert-info'>";
        echo "<i class='fas fa-info-circle'></i> If you have armor already equipped, it will be moved back to your inventory.";
        echo "</div>";
        
        echo "<input type='hidden' name='type' value='equip_armor' />";
        
        echo "<div class='d-grid gap-2'>";
        echo "<button type='submit' class='btn btn-primary btn-lg'>";
        echo "<i class='fas fa-check'></i> Equip Armor";
        echo "</button>";
        echo "<a href='inventory.php' class='btn btn-secondary'>";
        echo "<i class='fas fa-arrow-left'></i> Back to Inventory";
        echo "</a>";
        echo "</div>";
        
        echo "</form>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
    $h->endpage();
}
?>