<?php
/*
	File:		inventory_modern.php
	Created: 	Modernized inventory with drag-and-drop and better UI
	Info: 		Enhanced inventory management system
*/
require("globals.php");

// Calculate equipment bonuses
$totalStrength = 0;
$totalAgility = 0;
$totalGuard = 0;

// Equipment slots
$equipment_slots = [
    'equip_primary' => ['name' => 'Primary Weapon', 'icon' => 'fa-sword', 'color' => 'danger'],
    'equip_secondary' => ['name' => 'Secondary Weapon', 'icon' => 'fa-shield-alt', 'color' => 'primary'],
    'equip_armor' => ['name' => 'Armor', 'icon' => 'fa-vest', 'color' => 'secondary']
];
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <h2 class="mb-0"><i class="fas fa-backpack me-2"></i>Inventory Management</h2>
                    <p class="mb-0 mt-2">Manage your equipment and items</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Equipment Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-3"><i class="fas fa-shield-alt text-primary"></i> Equipped Items</h4>
        </div>
        
        <?php foreach($equipment_slots as $slot => $info): ?>
        <div class="col-md-4 mb-3">
            <div class="card equipment-card h-100 animate__animated animate__fadeIn">
                <div class="card-header bg-gradient-<?php echo $info['color']; ?> text-white">
                    <i class="fas <?php echo $info['icon']; ?> me-2"></i><?php echo $info['name']; ?>
                </div>
                <div class="card-body text-center">
                    <?php if (!empty($ir[$slot])): 
                        $item_name = $api->game->getItemNameFromID($ir[$slot]);
                        $item_info = $db->fetch_row($db->query("SELECT * FROM `items` WHERE `itmid` = {$ir[$slot]}"));
                    ?>
                        <div class="equipped-item">
                            <i class="fas <?php echo $info['icon']; ?> fa-3x mb-3 text-<?php echo $info['color']; ?>"></i>
                            <h5><?php echo $item_name; ?></h5>
                            <?php if($item_info): ?>
                                <div class="item-stats mt-3">
                                    <?php if($item_info['weapon'] > 0): ?>
                                        <span class="badge bg-danger">
                                            <i class="fas fa-fist-raised"></i> +<?php echo $item_info['weapon']; ?> Power
                                        </span>
                                    <?php endif; ?>
                                    <?php if($item_info['armor'] > 0): ?>
                                        <span class="badge bg-primary">
                                            <i class="fas fa-shield-alt"></i> +<?php echo $item_info['armor']; ?> Defense
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <a href="unequip.php?type=<?php echo $slot; ?>" class="btn btn-sm btn-outline-danger mt-3">
                                <i class="fas fa-times"></i> Unequip
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="empty-slot">
                            <i class="fas <?php echo $info['icon']; ?> fa-3x mb-3 text-muted opacity-25"></i>
                            <p class="text-muted">No <?php echo strtolower($info['name']); ?> equipped</p>
                            <a href="#inventory-items" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-plus"></i> Equip Item
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Inventory Items -->
    <div class="row" id="inventory-items">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-gradient-secondary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-boxes me-2"></i>Your Items</h4>
                    <div class="inventory-controls">
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-light active" data-filter="all">
                                <i class="fas fa-th"></i> All
                            </button>
                            <button class="btn btn-sm btn-light" data-filter="weapon">
                                <i class="fas fa-sword"></i> Weapons
                            </button>
                            <button class="btn btn-sm btn-light" data-filter="armor">
                                <i class="fas fa-shield-alt"></i> Armor
                            </button>
                            <button class="btn btn-sm btn-light" data-filter="consumable">
                                <i class="fas fa-potion"></i> Consumables
                            </button>
                            <button class="btn btn-sm btn-light" data-filter="other">
                                <i class="fas fa-cube"></i> Other
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row" id="inventory-grid">
                        <?php
                        $inv = $db->query("
                            SELECT `inv_qty`, `inv_id`, `inv_equip`, `inv_itemid`,
                                   `itmid`, `itmname`, `itmdesc`, `itmtype`, 
                                   `itmbuyable`, `itmbuyprice`, `itmsellprice`, 
                                   `effect1_on`, `effect2_on`, `effect3_on`,
                                   `weapon`, `armor`
                            FROM `inventory` AS `iv`
                            INNER JOIN `items` AS `i` ON `iv`.`inv_itemid` = `i`.`itmid`
                            WHERE `iv`.`inv_userid` = {$userid}
                            ORDER BY `i`.`itmtype` ASC, `i`.`itmname` ASC
                        ");
                        
                        if ($db->num_rows($inv) == 0) {
                            echo '<div class="col-12 text-center py-5">
                                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                                    <p class="text-muted">Your inventory is empty. Visit the <a href="shops.php">shops</a> to buy items!</p>
                                  </div>';
                        } else {
                            while ($item = $db->fetch_row($inv)) {
                                // Determine item category
                                $category = 'other';
                                if ($item['weapon'] > 0) $category = 'weapon';
                                elseif ($item['armor'] > 0) $category = 'armor';
                                elseif ($item['effect1_on'] || $item['effect2_on'] || $item['effect3_on']) $category = 'consumable';
                                
                                // Determine item rarity based on price
                                $rarity = 'common';
                                if ($item['itmbuyprice'] >= 10000) $rarity = 'legendary';
                                elseif ($item['itmbuyprice'] >= 5000) $rarity = 'epic';
                                elseif ($item['itmbuyprice'] >= 1000) $rarity = 'rare';
                                elseif ($item['itmbuyprice'] >= 500) $rarity = 'uncommon';
                                ?>
                                <div class="col-lg-3 col-md-4 col-sm-6 mb-3 inventory-item" data-category="<?php echo $category; ?>">
                                    <div class="card item-card h-100 rarity-<?php echo $rarity; ?> animate__animated animate__fadeIn">
                                        <div class="card-body d-flex flex-column">
                                            <div class="item-icon text-center mb-2">
                                                <?php
                                                $icon = 'fa-cube';
                                                if ($category == 'weapon') $icon = 'fa-sword';
                                                elseif ($category == 'armor') $icon = 'fa-shield-alt';
                                                elseif ($category == 'consumable') $icon = 'fa-potion';
                                                ?>
                                                <i class="fas <?php echo $icon; ?> fa-2x"></i>
                                            </div>
                                            <h6 class="item-name"><?php echo $item['itmname']; ?></h6>
                                            <?php if($item['inv_qty'] > 1): ?>
                                                <span class="badge bg-primary position-absolute top-0 end-0 m-2">
                                                    x<?php echo number_format($item['inv_qty']); ?>
                                                </span>
                                            <?php endif; ?>
                                            
                                            <p class="item-desc small text-muted mb-2"><?php echo $item['itmdesc']; ?></p>
                                            
                                            <div class="item-stats mb-2">
                                                <?php if($item['weapon'] > 0): ?>
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-fist-raised"></i> +<?php echo $item['weapon']; ?>
                                                    </span>
                                                <?php endif; ?>
                                                <?php if($item['armor'] > 0): ?>
                                                    <span class="badge bg-primary">
                                                        <i class="fas fa-shield-alt"></i> +<?php echo $item['armor']; ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="item-value mb-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-coins text-warning"></i> 
                                                    <?php echo number_format($item['itmsellprice']); ?>
                                                </small>
                                            </div>
                                            
                                            <div class="item-actions">
                                                <!-- Primary Action Button -->
                                                <div class="d-grid mb-2">
                                                    <?php if($item['weapon'] > 0): ?>
                                                        <a href="equip.php?slot=weapon&ID=<?php echo $item['inv_id']; ?>" class="btn btn-success btn-sm">
                                                            <i class="fas fa-hand-rock"></i> Equip Weapon
                                                        </a>
                                                    <?php elseif($item['armor'] > 0): ?>
                                                        <a href="equip.php?slot=armor&ID=<?php echo $item['inv_id']; ?>" class="btn btn-success btn-sm">
                                                            <i class="fas fa-vest"></i> Equip Armor
                                                        </a>
                                                    <?php elseif($item['effect1_on'] || $item['effect2_on'] || $item['effect3_on']): ?>
                                                        <a href="itemuse.php?id=<?php echo $item['inv_id']; ?>" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-flask"></i> Use Item
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="iteminfo.php?id=<?php echo $item['itmid']; ?>" class="btn btn-info btn-sm">
                                                            <i class="fas fa-info"></i> View Details
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <!-- Secondary Actions Row -->
                                                <div class="row g-1">
                                                    <div class="col-4">
                                                        <a href="itemsend.php?id=<?php echo $item['inv_id']; ?>" class="btn btn-outline-secondary btn-sm w-100" title="Send">
                                                            <i class="fas fa-gift"></i>
                                                        </a>
                                                    </div>
                                                    <div class="col-4">
                                                        <a href="itemsell.php?id=<?php echo $item['inv_id']; ?>" class="btn btn-outline-warning btn-sm w-100" title="Sell">
                                                            <i class="fas fa-coins"></i>
                                                        </a>
                                                    </div>
                                                    <div class="col-4">
                                                        <a href="itemmarket.php?action=add&id=<?php echo $item['inv_id']; ?>" class="btn btn-outline-info btn-sm w-100" title="Market">
                                                            <i class="fas fa-store"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-secondary {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.equipment-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.equipment-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.equipped-item, .empty-slot {
    padding: 20px;
}

.item-card {
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.item-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.item-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.item-card:hover::before {
    left: 100%;
}

/* Rarity borders */
.rarity-common {
    border-left: 3px solid #gray;
}

.rarity-uncommon {
    border-left: 3px solid #28a745;
}

.rarity-rare {
    border-left: 3px solid #007bff;
}

.rarity-epic {
    border-left: 3px solid #6f42c1;
}

.rarity-legendary {
    border-left: 3px solid #ffc107;
}

.item-name {
    font-weight: 600;
    margin-bottom: 5px;
}

.item-actions {
    margin-top: auto;
    padding-top: 10px;
}

.item-actions .btn {
    font-size: 11px;
    padding: 4px 8px;
    border-radius: 4px;
}

.item-actions .btn i {
    font-size: 10px;
}

/* Ensure equal button spacing */
.item-actions .row.g-1 {
    --bs-gutter-x: 0.25rem;
}

/* Mobile responsive improvements */
@media (max-width: 768px) {
    .inventory-item {
        margin-bottom: 15px;
    }
    
    .item-card .card-body {
        padding: 12px;
    }
    
    .item-actions .btn {
        font-size: 10px;
        padding: 6px 8px;
    }
    
    .item-name {
        font-size: 14px;
    }
    
    .item-desc {
        font-size: 12px;
        min-height: 35px;
    }
}
}

.item-desc {
    min-height: 40px;
}

.inventory-controls .btn-group .btn {
    transition: all 0.3s ease;
}

.inventory-controls .btn-group .btn.active {
    background-color: var(--primary-color);
    color: white;
}

.inventory-item {
    transition: all 0.3s ease;
}

.inventory-item.hidden {
    display: none !important;
}
</style>

<!-- JavaScript for filtering -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Item filtering
    const filterButtons = document.querySelectorAll('[data-filter]');
    const inventoryItems = document.querySelectorAll('.inventory-item');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter items
            const filter = this.dataset.filter;
            inventoryItems.forEach(item => {
                if (filter === 'all' || item.dataset.category === filter) {
                    item.classList.remove('hidden');
                    item.style.animation = 'fadeIn 0.5s';
                } else {
                    item.classList.add('hidden');
                }
            });
        });
    });
});
</script>

<?php
$h->endpage();
?>