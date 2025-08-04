<?php
/*
    File: investmarket.php
    Created: Stock Market Investment System
    Info: Buy and sell shares, manage portfolio, track market trends
*/
require_once('globals.php');

class StockMarketSystem {
    private $db;
    private $userid;
    private $api;
    
    public function __construct($db, $userid, $api) {
        $this->db = $db;
        $this->userid = $userid;
        $this->api = $api;
        
        // Initialize tables if needed
        $this->initializeTables();
    }
    
    /**
     * Initialize stock market tables
     */
    private function initializeTables() {
        // Check if tables exist
        $check = $this->db->query("SHOW TABLES LIKE 'asset_market'");
        if ($this->db->num_rows($check) == 0) {
            // Create tables
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `asset_market` (
                    `am_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `am_name` varchar(100) NOT NULL,
                    `am_symbol` varchar(10) NOT NULL,
                    `am_desc` text,
                    `am_min` int(11) unsigned NOT NULL DEFAULT 10,
                    `am_max` int(11) unsigned NOT NULL DEFAULT 10000,
                    `am_start` int(11) unsigned NOT NULL DEFAULT 100,
                    `am_cost` int(11) unsigned NOT NULL DEFAULT 100,
                    `am_change` int(11) NOT NULL DEFAULT 0,
                    `am_risk` tinyint(1) unsigned NOT NULL DEFAULT 1,
                    `am_last_update` int(11) NOT NULL DEFAULT 0,
                    PRIMARY KEY (`am_id`),
                    UNIQUE KEY `am_symbol` (`am_symbol`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `asset_market_owned` (
                    `amo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `userid` int(11) unsigned NOT NULL,
                    `am_id` int(11) unsigned NOT NULL,
                    `shares_owned` int(11) unsigned NOT NULL DEFAULT 0,
                    `shares_cost` bigint(20) unsigned NOT NULL DEFAULT 0,
                    `last_transaction` int(11) NOT NULL,
                    PRIMARY KEY (`amo_id`),
                    UNIQUE KEY `user_asset` (`userid`, `am_id`),
                    KEY `userid` (`userid`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `asset_market_history` (
                    `amh_id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
                    `am_id` int(11) unsigned NOT NULL,
                    `old_value` int(11) unsigned NOT NULL,
                    `difference` int(11) NOT NULL,
                    `new_value` int(11) unsigned NOT NULL,
                    `timestamp` int(11) unsigned NOT NULL,
                    PRIMARY KEY (`amh_id`),
                    KEY `am_id` (`am_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `asset_market_profit` (
                    `userid` int(11) unsigned NOT NULL,
                    `total_invested` bigint(20) NOT NULL DEFAULT 0,
                    `total_returned` bigint(20) NOT NULL DEFAULT 0,
                    `profit` bigint(20) NOT NULL DEFAULT 0,
                    PRIMARY KEY (`userid`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            
            // Add sample stocks
            $this->addSampleStocks();
        }
    }
    
    /**
     * Add sample stocks to market
     */
    private function addSampleStocks() {
        $stocks = [
            ['Chivalry Mining Corp', 'CMC', 'Leading mining company', 50, 5000, 500, 500, 2],
            ['Royal Bank', 'RBK', 'Kingdom\'s largest bank', 100, 2000, 300, 300, 1],
            ['Dragon Airways', 'DAW', 'Premium air travel', 200, 8000, 1000, 1000, 3],
            ['Peasant Foods Inc', 'PFI', 'Food production giant', 20, 1000, 100, 100, 1],
            ['Armor & Weapons Ltd', 'AWL', 'Military equipment supplier', 150, 6000, 800, 800, 2],
            ['Magical Potions Co', 'MPC', 'Potion manufacturing', 75, 3000, 400, 400, 2],
            ['Castle Construction', 'CCN', 'Building and infrastructure', 300, 10000, 1500, 1500, 3],
            ['Tavern Holdings', 'THG', 'Entertainment and hospitality', 50, 2500, 250, 250, 1]
        ];
        
        foreach ($stocks as $stock) {
            $this->db->query("
                INSERT IGNORE INTO asset_market 
                (am_name, am_symbol, am_desc, am_min, am_max, am_start, am_cost, am_risk, am_last_update)
                VALUES ('{$stock[0]}', '{$stock[1]}', '{$stock[2]}', {$stock[3]}, {$stock[4]}, 
                        {$stock[5]}, {$stock[6]}, {$stock[7]}, " . time() . ")
            ");
        }
    }
    
    /**
     * Get all stocks
     */
    public function getStocks() {
        $stocks = [];
        $query = $this->db->query("
            SELECT am.*,
                   (SELECT COUNT(DISTINCT userid) FROM asset_market_owned WHERE am_id = am.am_id) as investors,
                   (SELECT SUM(shares_owned) FROM asset_market_owned WHERE am_id = am.am_id) as total_shares
            FROM asset_market am
            ORDER BY am_symbol
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            // Calculate percentage change
            if ($row['am_start'] > 0) {
                $row['percent_change'] = round((($row['am_cost'] - $row['am_start']) / $row['am_start']) * 100, 2);
            } else {
                $row['percent_change'] = 0;
            }
            
            // Determine trend
            if ($row['am_change'] > 0) {
                $row['trend'] = 'up';
                $row['trend_color'] = 'success';
                $row['trend_icon'] = '📈';
            } elseif ($row['am_change'] < 0) {
                $row['trend'] = 'down';
                $row['trend_color'] = 'danger';
                $row['trend_icon'] = '📉';
            } else {
                $row['trend'] = 'stable';
                $row['trend_color'] = 'secondary';
                $row['trend_icon'] = '➡️';
            }
            
            $stocks[] = $row;
        }
        
        return $stocks;
    }
    
    /**
     * Get user's portfolio
     */
    public function getPortfolio() {
        $portfolio = [];
        $total_value = 0;
        $total_invested = 0;
        
        $query = $this->db->query("
            SELECT amo.*, am.*
            FROM asset_market_owned amo
            INNER JOIN asset_market am ON amo.am_id = am.am_id
            WHERE amo.userid = {$this->userid} AND amo.shares_owned > 0
            ORDER BY (amo.shares_owned * am.am_cost) DESC
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            $row['current_value'] = $row['shares_owned'] * $row['am_cost'];
            $row['profit_loss'] = $row['current_value'] - $row['shares_cost'];
            $row['profit_percent'] = $row['shares_cost'] > 0 ? 
                round(($row['profit_loss'] / $row['shares_cost']) * 100, 2) : 0;
            
            $total_value += $row['current_value'];
            $total_invested += $row['shares_cost'];
            
            $portfolio[] = $row;
        }
        
        return [
            'holdings' => $portfolio,
            'total_value' => $total_value,
            'total_invested' => $total_invested,
            'total_profit' => $total_value - $total_invested
        ];
    }
    
    /**
     * Buy shares
     */
    public function buyShares($stock_id, $quantity) {
        global $ir;
        
        $quantity = abs((int)$quantity);
        
        if ($quantity <= 0) {
            return ['success' => false, 'message' => 'Invalid quantity!'];
        }
        
        // Get stock info
        $stock = $this->db->fetch_row($this->db->query("
            SELECT * FROM asset_market WHERE am_id = {$stock_id}
        "));
        
        if (!$stock) {
            return ['success' => false, 'message' => 'Invalid stock!'];
        }
        
        $total_cost = $stock['am_cost'] * $quantity;
        
        if ($ir['primary_currency'] < $total_cost) {
            return ['success' => false, 'message' => 'You need ' . number_format($total_cost) . ' gold!'];
        }
        
        // Process purchase
        $this->db->query("
            UPDATE users 
            SET primary_currency = primary_currency - {$total_cost}
            WHERE userid = {$this->userid}
        ");
        
        // Update holdings
        $existing = $this->db->fetch_row($this->db->query("
            SELECT * FROM asset_market_owned 
            WHERE userid = {$this->userid} AND am_id = {$stock_id}
        "));
        
        if ($existing) {
            $new_shares = $existing['shares_owned'] + $quantity;
            $new_cost = $existing['shares_cost'] + $total_cost;
            
            $this->db->query("
                UPDATE asset_market_owned 
                SET shares_owned = {$new_shares},
                    shares_cost = {$new_cost},
                    last_transaction = " . time() . "
                WHERE userid = {$this->userid} AND am_id = {$stock_id}
            ");
        } else {
            $this->db->query("
                INSERT INTO asset_market_owned (userid, am_id, shares_owned, shares_cost, last_transaction)
                VALUES ({$this->userid}, {$stock_id}, {$quantity}, {$total_cost}, " . time() . ")
            ");
        }
        
        // Update profit tracking
        $this->db->query("
            INSERT INTO asset_market_profit (userid, total_invested)
            VALUES ({$this->userid}, {$total_cost})
            ON DUPLICATE KEY UPDATE total_invested = total_invested + {$total_cost}
        ");
        
        // Log transaction
        $this->api->SystemLogsAdd($this->userid, 'stocks', "Bought {$quantity} shares of {$stock['am_symbol']} for " . number_format($total_cost) . " gold");
        
        return ['success' => true, 'message' => "Bought {$quantity} shares of {$stock['am_name']}!"];
    }
    
    /**
     * Sell shares
     */
    public function sellShares($stock_id, $quantity) {
        $quantity = abs((int)$quantity);
        
        if ($quantity <= 0) {
            return ['success' => false, 'message' => 'Invalid quantity!'];
        }
        
        // Get stock and holding info
        $stock = $this->db->fetch_row($this->db->query("
            SELECT * FROM asset_market WHERE am_id = {$stock_id}
        "));
        
        if (!$stock) {
            return ['success' => false, 'message' => 'Invalid stock!'];
        }
        
        $holding = $this->db->fetch_row($this->db->query("
            SELECT * FROM asset_market_owned 
            WHERE userid = {$this->userid} AND am_id = {$stock_id}
        "));
        
        if (!$holding || $holding['shares_owned'] < $quantity) {
            return ['success' => false, 'message' => 'You don\'t own enough shares!'];
        }
        
        $sale_value = $stock['am_cost'] * $quantity;
        
        // Process sale
        $this->db->query("
            UPDATE users 
            SET primary_currency = primary_currency + {$sale_value}
            WHERE userid = {$this->userid}
        ");
        
        // Update holdings
        $new_shares = $holding['shares_owned'] - $quantity;
        $cost_per_share = $holding['shares_cost'] / $holding['shares_owned'];
        $new_cost = $new_shares * $cost_per_share;
        
        if ($new_shares > 0) {
            $this->db->query("
                UPDATE asset_market_owned 
                SET shares_owned = {$new_shares},
                    shares_cost = {$new_cost},
                    last_transaction = " . time() . "
                WHERE userid = {$this->userid} AND am_id = {$stock_id}
            ");
        } else {
            $this->db->query("
                DELETE FROM asset_market_owned 
                WHERE userid = {$this->userid} AND am_id = {$stock_id}
            ");
        }
        
        // Update profit tracking
        $this->db->query("
            UPDATE asset_market_profit 
            SET total_returned = total_returned + {$sale_value},
                profit = total_returned - total_invested
            WHERE userid = {$this->userid}
        ");
        
        // Log transaction
        $this->api->SystemLogsAdd($this->userid, 'stocks', "Sold {$quantity} shares of {$stock['am_symbol']} for " . number_format($sale_value) . " gold");
        
        return ['success' => true, 'message' => "Sold {$quantity} shares for " . number_format($sale_value) . " gold!"];
    }
    
    /**
     * Get stock history
     */
    public function getStockHistory($stock_id, $limit = 50) {
        $history = [];
        
        $query = $this->db->query("
            SELECT * FROM asset_market_history 
            WHERE am_id = {$stock_id}
            ORDER BY timestamp DESC
            LIMIT {$limit}
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            $history[] = $row;
        }
        
        return array_reverse($history);
    }
    
    /**
     * Update stock prices (called by cron)
     */
    public function updatePrices() {
        $stocks = $this->getStocks();
        
        foreach ($stocks as $stock) {
            // Determine price change based on risk level
            $volatility = [
                1 => 5,  // Low risk: ±5%
                2 => 10, // Medium risk: ±10%
                3 => 20  // High risk: ±20%
            ];
            
            $max_change = $stock['am_cost'] * ($volatility[$stock['am_risk']] / 100);
            $change = rand(-$max_change, $max_change);
            
            // Apply market trends (random events)
            if (rand(1, 100) <= 5) { // 5% chance of major event
                $change *= rand(2, 3); // Double or triple the change
            }
            
            $new_price = $stock['am_cost'] + $change;
            
            // Enforce min/max limits
            $new_price = max($stock['am_min'], min($stock['am_max'], $new_price));
            
            // Update price
            $this->db->query("
                UPDATE asset_market 
                SET am_cost = {$new_price},
                    am_change = {$change},
                    am_last_update = " . time() . "
                WHERE am_id = {$stock['am_id']}
            ");
            
            // Record history
            $this->db->query("
                INSERT INTO asset_market_history (am_id, old_value, difference, new_value, timestamp)
                VALUES ({$stock['am_id']}, {$stock['am_cost']}, {$change}, {$new_price}, " . time() . ")
            ");
        }
    }
}

// Check if v3.2 is installed
$v32_check = $db->query("SELECT setting_value FROM settings WHERE setting_name = 'db_version' LIMIT 1");
$db_version = null;
if ($db->num_rows($v32_check) > 0) {
    $db_version = $db->fetch_single($v32_check);
}

// Check if tables exist
$tables_exist = true;
$check_tables = ['asset_market', 'asset_market_owned', 'asset_market_history', 'asset_market_profit'];
foreach ($check_tables as $table) {
    $check = $db->query("SHOW TABLES LIKE '{$table}'");
    if ($db->num_rows($check) == 0) {
        $tables_exist = false;
        break;
    }
}

// If tables don't exist or version is less than 3.2, show upgrade message
if (!$tables_exist || ($db_version && version_compare($db_version, '3.2.0', '<'))) {
    ?>
    <div class="container-fluid">
        <div class="alert alert-warning">
            <h4><i class="fas fa-exclamation-triangle"></i> Feature Not Available</h4>
            <p>The Stock Market System requires Chivalry Engine v3.2.0 or higher.</p>
            <?php if ($userid == 1): ?>
                <p>Please run the database update to install this feature.</p>
                <a href="uplift_check.php" class="btn btn-primary">
                    <i class="fas fa-download"></i> Run Database Update
                </a>
            <?php else: ?>
                <p>Please contact an administrator to update the game.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php
    $h->endpage();
    exit;
}

// Initialize system
$stock_system = new StockMarketSystem($db, $userid, $api);

// Handle actions
if (isset($_POST['action'])) {
    $result = null;
    
    switch($_POST['action']) {
        case 'buy':
            $stock_id = abs((int)$_POST['stock_id']);
            $quantity = abs((int)$_POST['quantity']);
            $result = $stock_system->buyShares($stock_id, $quantity);
            break;
            
        case 'sell':
            $stock_id = abs((int)$_POST['stock_id']);
            $quantity = abs((int)$_POST['quantity']);
            $result = $stock_system->sellShares($stock_id, $quantity);
            break;
    }
    
    if ($result) {
        alert($result['success'] ? 'success' : 'danger',
              $result['success'] ? 'Success!' : 'Failed!',
              $result['message'], false);
    }
}

// Get page to display
$page = $_GET['page'] ?? 'market';

?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-0"><i class="fas fa-chart-line me-2"></i>Stock Market</h2>
                            <p class="mb-0 mt-2">Buy low, sell high, and build your fortune!</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <a href="?page=market" class="btn btn-light btn-sm">Market</a>
                            <a href="?page=portfolio" class="btn btn-light btn-sm">Portfolio</a>
                            <a href="?page=leaderboard" class="btn btn-light btn-sm">Top Investors</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($page == 'market'): ?>
        <!-- Market View -->
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-store"></i> Stock Market</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Symbol</th>
                                <th>Company</th>
                                <th>Price</th>
                                <th>Change</th>
                                <th>% Change</th>
                                <th>Risk</th>
                                <th>Investors</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $stocks = $stock_system->getStocks();
                            foreach ($stocks as $stock): 
                                $risk_badges = [
                                    1 => '<span class="badge bg-success">Low</span>',
                                    2 => '<span class="badge bg-warning">Medium</span>',
                                    3 => '<span class="badge bg-danger">High</span>'
                                ];
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo $stock['am_symbol']; ?></strong>
                                    <span style="font-size: 1.2rem;"><?php echo $stock['trend_icon']; ?></span>
                                </td>
                                <td>
                                    <?php echo $stock['am_name']; ?>
                                    <br><small class="text-muted"><?php echo $stock['am_desc']; ?></small>
                                </td>
                                <td><?php echo number_format($stock['am_cost']); ?> gold</td>
                                <td class="text-<?php echo $stock['trend_color']; ?>">
                                    <?php echo $stock['am_change'] > 0 ? '+' : ''; ?><?php echo number_format($stock['am_change']); ?>
                                </td>
                                <td class="text-<?php echo $stock['trend_color']; ?>">
                                    <?php echo $stock['percent_change'] > 0 ? '+' : ''; ?><?php echo $stock['percent_change']; ?>%
                                </td>
                                <td><?php echo $risk_badges[$stock['am_risk']]; ?></td>
                                <td><?php echo number_format($stock['investors']); ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="buy">
                                        <input type="hidden" name="stock_id" value="<?php echo $stock['am_id']; ?>">
                                        <?php echo getHtmlCSRF('stock_buy_' . $stock['am_id']); ?>
                                        <div class="input-group input-group-sm">
                                            <input type="number" class="form-control" name="quantity" 
                                                   min="1" value="1" style="width: 60px" required>
                                            <button type="submit" class="btn btn-success btn-sm">Buy</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    <?php elseif ($page == 'portfolio'): ?>
        <!-- Portfolio View -->
        <?php 
        $portfolio = $stock_system->getPortfolio();
        ?>
        
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h6>Total Value</h6>
                        <h4><?php echo number_format($portfolio['total_value']); ?> gold</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h6>Total Invested</h6>
                        <h4><?php echo number_format($portfolio['total_invested']); ?> gold</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h6>Profit/Loss</h6>
                        <h4 class="text-<?php echo $portfolio['total_profit'] >= 0 ? 'success' : 'danger'; ?>">
                            <?php echo $portfolio['total_profit'] >= 0 ? '+' : ''; ?>
                            <?php echo number_format($portfolio['total_profit']); ?> gold
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h6>Return %</h6>
                        <h4 class="text-<?php echo $portfolio['total_profit'] >= 0 ? 'success' : 'danger'; ?>">
                            <?php 
                            $return_percent = $portfolio['total_invested'] > 0 ? 
                                round(($portfolio['total_profit'] / $portfolio['total_invested']) * 100, 2) : 0;
                            echo $return_percent >= 0 ? '+' : '';
                            echo $return_percent; 
                            ?>%
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-briefcase"></i> Your Portfolio</h5>
            </div>
            <div class="card-body">
                <?php if (empty($portfolio['holdings'])): ?>
                    <p class="text-muted">You don't own any stocks yet. Visit the market to buy some!</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Stock</th>
                                    <th>Shares</th>
                                    <th>Avg Cost</th>
                                    <th>Current Price</th>
                                    <th>Value</th>
                                    <th>Profit/Loss</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($portfolio['holdings'] as $holding): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo $holding['am_symbol']; ?></strong><br>
                                        <small><?php echo $holding['am_name']; ?></small>
                                    </td>
                                    <td><?php echo number_format($holding['shares_owned']); ?></td>
                                    <td><?php echo number_format($holding['shares_cost'] / $holding['shares_owned']); ?></td>
                                    <td><?php echo number_format($holding['am_cost']); ?></td>
                                    <td><?php echo number_format($holding['current_value']); ?></td>
                                    <td class="text-<?php echo $holding['profit_loss'] >= 0 ? 'success' : 'danger'; ?>">
                                        <?php echo $holding['profit_loss'] >= 0 ? '+' : ''; ?>
                                        <?php echo number_format($holding['profit_loss']); ?>
                                        (<?php echo $holding['profit_percent'] >= 0 ? '+' : ''; ?><?php echo $holding['profit_percent']; ?>%)
                                    </td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="sell">
                                            <input type="hidden" name="stock_id" value="<?php echo $holding['am_id']; ?>">
                                            <?php echo getHtmlCSRF('stock_sell_' . $holding['am_id']); ?>
                                            <div class="input-group input-group-sm">
                                                <input type="number" class="form-control" name="quantity" 
                                                       min="1" max="<?php echo $holding['shares_owned']; ?>" 
                                                       value="<?php echo $holding['shares_owned']; ?>" style="width: 80px" required>
                                                <button type="submit" class="btn btn-danger btn-sm">Sell</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
    <?php elseif ($page == 'leaderboard'): ?>
        <!-- Leaderboard View -->
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-trophy"></i> Top Investors</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Investor</th>
                                <th>Total Profit</th>
                                <th>Total Invested</th>
                                <th>Return %</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = $db->query("
                                SELECT p.*, u.username
                                FROM asset_market_profit p
                                INNER JOIN users u ON p.userid = u.userid
                                WHERE p.profit != 0
                                ORDER BY p.profit DESC
                                LIMIT 50
                            ");
                            
                            $rank = 1;
                            while ($investor = $db->fetch_row($query)):
                                $return_pct = $investor['total_invested'] > 0 ? 
                                    round(($investor['profit'] / $investor['total_invested']) * 100, 2) : 0;
                            ?>
                            <tr>
                                <td><?php echo $rank++; ?></td>
                                <td><?php echo $investor['username']; ?></td>
                                <td class="text-<?php echo $investor['profit'] >= 0 ? 'success' : 'danger'; ?>">
                                    <?php echo $investor['profit'] >= 0 ? '+' : ''; ?>
                                    <?php echo number_format($investor['profit']); ?> gold
                                </td>
                                <td><?php echo number_format($investor['total_invested']); ?> gold</td>
                                <td class="text-<?php echo $return_pct >= 0 ? 'success' : 'danger'; ?>">
                                    <?php echo $return_pct >= 0 ? '+' : ''; ?><?php echo $return_pct; ?>%
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Info -->
    <div class="card mt-4">
        <div class="card-body">
            <h5><i class="fas fa-info-circle"></i> Stock Market Guide</h5>
            <ul>
                <li>Buy shares when prices are low, sell when they're high</li>
                <li>Different stocks have different risk levels - higher risk means more volatility</li>
                <li>Diversify your portfolio to reduce risk</li>
                <li>Stock prices update regularly based on market conditions</li>
                <li>Track your investments in the portfolio section</li>
            </ul>
        </div>
    </div>
</div>

<?php
$h->endpage();
?>