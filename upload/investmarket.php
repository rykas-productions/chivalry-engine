<?php
//Copper Coin Stock Market

/*
 * CREATE TABLE `asset_market` ( 
    `am_id` INT(11) UNSIGNED NULL DEFAULT NULL , 
    `am_name` TEXT NOT NULL , 
    `am_min` INT(11) UNSIGNED NOT NULL , 
    `am_max` INT(11) UNSIGNED NOT NULL , 
    `am_start` INT(11) UNSIGNED NOT NULL , 
    `am_cost` INT(11) UNSIGNED NOT NULL , 
    `am_change` INT(11) UNSIGNED NOT NULL , 
    UNIQUE (`am_id`)) ENGINE = MyISAM;
    
    CREATE TABLE `asset_market_owned` ( 
     `userid` INT(11) UNSIGNED NOT NULL , 
     `am_id` INT(11) UNSIGNED NOT NULL , 
     `shares_owned` INT(11) UNSIGNED NOT NULL , 
     `shares_cost` INT(11) UNSIGNED NOT NULL ) 
     ENGINE = MyISAM;
     
     CREATE TABLE `asset_market_history` ( 
        `am_id` INT(11) UNSIGNED NOT NULL , 
        `old_value` INT(11) UNSIGNED NOT NULL , 
        `difference` INT(11) NOT NULL , 
        `new_value` INT(11) UNSIGNED NOT NULL 
        ) ENGINE = MyISAM;
        
     CREATE TABLE `asset_market_profit` ( 
        `userid` BIGINT(11) UNSIGNED NOT NULL , 
        `profit` BIGINT(11) NOT NULL ) 
        ENGINE = MyISAM;
     
     ALTER TABLE `asset_market` CHANGE `am_id` `am_id` INT(11) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT;
     ALTER TABLE `asset_market` ADD `am_risk` TINYINT(11) UNSIGNED NOT NULL AFTER `am_change`;
     ALTER TABLE `asset_market` CHANGE `am_risk` `am_risk` TINYINT(11) UNSIGNED NOT NULL DEFAULT '1';
     ALTER TABLE `asset_market_owned` ADD `amo_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST, ADD UNIQUE (`amo_id`);
     ALTER TABLE `asset_market_history` ADD `amh_id` BIGINT(11) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT FIRST, ADD UNIQUE (`amh_id`);
     ALTER TABLE `asset_market_history` ADD `timestamp` BIGINT(11) UNSIGNED NOT NULL AFTER `new_value`;
    
    DELETE FROM `asset_market_profit` WHERE `userid` = 161
    INSERT INTO `asset_market_profit` (`userid`, `profit`) VALUES ('161', '4431550000')
    
 *
 */
$macropage = ('investmarket.php');
require('globals.php');
if (user_infirmary($userid))
{
    alert('danger',"Uh Oh!","You cannot use the Asset Investment market if you're in the infirmary.",true,'infirmary.php');
    die($h->endpage());
}
if (user_dungeon($userid))
{
    alert('danger',"Uh Oh!","You cannot use the Asset Investment market if you're in the dungeon.",true,'dungeon.php');
    die($h->endpage());
}
echo "<h3>Asset Investing</h3><hr />
    <div class='row'>
        <div class='col-12 col-sm-6 col-md'>
            <a href='#' data-toggle='modal' data-target='#investment_info' class='btn btn-info btn-block'>Info</a>
            <br />
        </div>
        <div class='col-12 col-sm-6 col-md'>
            <a href='investmarket.php' class='btn btn-primary btn-block'>Home</a>
            <br />
        </div>
        <div class='col-12 col-sm col-md'>
            <a href='?action=portfolio' class='btn btn-success btn-block'>Portfolio</a>
            <br />
        </div>";
        if ($ir['user_level'] == 'Admin')
        {
            echo "<div class='col-12 col-sm-6 col-md'>
            <a href='?action=staffadd' class='btn btn-danger btn-block'>Add Stock</a>
            <br />
            </div>";
        }
        echo"
    </div>
    <hr />";
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
    case 'buy':
        buy();
        break;
    case 'history':
        history();
        break;
    case 'sell':
        sell();
        break;
    case 'portfolio':
        portfolio();
        break;
    case 'staffadd':
        staffadd();
        break;
    default:
        home();
        break;
}
function home()
{
    global $db,$ir,$userid,$api,$h;
    alert('warning',"","This is a list of all assets you may purchase stock of. Please read the info page before you invest.", false);
    $q = $db->query("SELECT * FROM `asset_market`");
    echo "<div class='card'>
    <div class='card-body'>";
    while ($r = $db->fetch_row($q))
    {
        $q2=$db->query("SELECT * FROM `asset_market_history` WHERE `am_id` = {$r['am_id']} ORDER BY `timestamp` DESC LIMIT 1");
        $r2=$db->fetch_row($q2);
        $change = ($r2['old_value'] < $r2['new_value']) ? "text-success" : "text-danger";
        echo "<div class='row'>
            <div class='col-6 col-lg-2'>
                {$r['am_name']}
            </div>
            <div class='col-6 col-lg-4'>
                <div class='col-12'>
                    Current Value
                </div>
                <div class='col-12'>
                    <small>" . shortNumberParse($r['am_cost']) . " Copper Coins <span class='{$change}'>(" . number_format($r2['difference']) . ")</span></small>
                </div>
            </div>
            <div class='col-12 col-lg-6'>
                <div class='row'>
                    <div class='col-12 col-sm-4 col-lg'>
                        <a href='?action=buy&id={$r['am_id']}' class='btn btn-success btn-block'>Buy</a><br />
                    </div>
                    <div class='col-12 col-sm-4 col-lg'>
                        <a href='?action=sell&id={$r['am_id']}' class='btn btn-danger btn-block'>Sell</a><br />
                    </div>
                    <div class='col-12 col-sm-4 col-lg'>
                        <a href='?action=history&id={$r['am_id']}' class='btn btn-info btn-block'>History</a><br />
                    </div>
                </div>
            </div>
        </div>";
    }
    echo "</div></div>";
}

function buy()
{
    global $db, $userid, $api, $h, $ir;
    $stock_id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
    if (empty($stock_id))
    {
        alert('danger',"Uh Oh!","Please input a valid asset to view.",true,'investmarket.php');
        die($h->endpage());
    }
    $q=$db->query("SELECT * FROM `asset_market` WHERE `am_id` = {$stock_id}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        alert('danger',"Uh Oh!","You are attempting to purchase an asset that does not exist.",true,'investmarket.php');
        die($h->endpage());
    }
    $maxShares = returnUserMaxShares($userid);
    $r=$db->fetch_row($q);
    $currentShares = returnUserAssetShares($userid, $r['am_id']);
    if (isset($_POST['buy']))
    {
        $purchase_amount = (isset($_POST['buy']) && is_numeric($_POST['buy'])) ? abs($_POST['buy']) : '';
        if (empty($purchase_amount))
        {
            alert('danger',"Uh Oh!","Please input a valid number of shares to purchase.");
            die($h->endpage());
        }
        $totalCost = $purchase_amount * $r['am_cost'];
        if ($ir['primary_currency'] < $totalCost)
        {
            alert('danger', "Uh Oh!", "You need " . shortNumberParse($totalCost) . " Copper Coins to purchase " . number_format($purchase_amount) . " shares of the {$r['am_name']} asset.");
            die($h->endpage());
        }
        //if user has over the maximum shares for their progress.
        if (($purchase_amount + $currentShares) > $maxShares)
        {
            alert('danger', "Uh Oh!", "You are attempting to purchase more shares than allowed with your current progress in CID. You may only own " . number_format($maxShares) . " shares per asset at your current progress.");
            die($h->endpage());
        }
        $api->UserTakeCurrency($userid, "primary", $totalCost);
        setUserShares($userid, $r['am_id'], $r['am_cost'], $purchase_amount);
        $api->SystemLogsAdd($userid, "asset_market", "Purchased " . number_format($purchase_amount) . " shares of the {$r['am_name']} asset for " . number_format($totalCost) . " Copper Coins.");
        alert('success','Success!',"You have successfully purchased " . number_format($purchase_amount) . " shares of the {$r['am_name']} asset for " . shortNumberParse($totalCost) . " Copper Coins.", false);
        addToEconomyLog('Asset Market', 'copper', $totalCost * -1);
        home();
    }
    else
    {
        $maxToBuy = clamp($maxShares - $currentShares, 0, $maxShares);
        echo "<div class='card'>
            <div class='card-body'>
                You are attempting to buy shares of the {$r['am_name']} asset. {$r['am_name']} is a Risk {$r['am_risk']} asset. Shares currently 
                cost " . number_format($r['am_cost']) . " Copper Coins each. You currently have " . shortNumberParse($ir['primary_currency']) . " Copper Coins. Note, you are 
                currently restricted to owning only " . number_format($maxShares) . " shares of an asset at a time. You currently own " . number_format($currentShares) . " shares of this asset.
                How many shares would you like to purchase of {$r['am_name']}?<hr />
                <form method='post'>
                    <div class='row'>
                    <div class='col-12 col-md-6'>
                        <input type='number' min='0' class='form-control' required='1' value='{$maxToBuy}' max='{$maxToBuy}' name='buy' placeholder='Shares to purchase'>
                    </div>
                    <div class='col-12 col-md-6'>
                        <input type='submit' class='btn btn-success btn-block' value='Purchase Shares'>
                    </div>
                </div>
                </form>
        </div>
        </div>";
    }
}

function sell()
{
    global $db, $userid, $api, $h, $ir;
    $stock_id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
    if (empty($stock_id))
    {
        alert('danger',"Uh Oh!","Please input a valid asset to view.",true,'investmarket.php');
        die($h->endpage());
    }
    $q=$db->query("SELECT * FROM `asset_market` WHERE `am_id` = {$stock_id}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        alert('danger',"Uh Oh!","You are attempting to sell an asset that does not exist.",true,'investmarket.php');
        die($h->endpage());
    }
    $q2=$db->query("SELECT * FROM `asset_market_owned` WHERE `am_id` = {$stock_id} AND `userid` = {$userid}");
    if ($db->num_rows($q2) == 0)
    {
        $db->free_result($q2);
        alert('danger',"Uh Oh!","You do not own any of this asset, which means you cannot sell it.",true,'investmarket.php');
        die($h->endpage());
    }
    $r=$db->fetch_row($q);  //asset data
    $sharesTotal = returnUserAssetShares($userid, $r['am_id']);
    $totalCost = returnUserAssetCosts($userid, $r['am_id']);
    $avgCost = round($totalCost / $sharesTotal);
    $sellValue = $sharesTotal * $r['am_cost'];
    if (isset($_POST['sell']))
    {
        $sell_amount = (isset($_POST['sell']) && is_numeric($_POST['sell'])) ? abs($_POST['sell']) : '';
        if (empty($sell_amount))
        {
            alert('danger',"Uh Oh!","Please input a valid number of shares to sell.");
            die($h->endpage());
        }
        if ($sell_amount > $sharesTotal)
        {
            alert('danger',"Uh Oh!","You cannot sell " . number_format($sell_amount) . " shares of the {$r['am_name']} asset, 
            as you only have " . number_format($sharesTotal) . " shares.");
            die($h->endpage());
        }
        $marketValue = $sell_amount * $r['am_cost'];
        $profit = $sellValue - $totalCost;
        $toPlayer = $marketValue * 0.98;
        $marketTax = $marketValue * 0.02;
        setUserShares($userid, $r['am_id'], $r['am_cost'], $sell_amount * -1);
        alert('success', "Success!", "You have sold " . shortNumberParse($sell_amount) . " shares of the 
            {$r['am_name']} asset at " . shortNumberParse($r['am_cost']) . " Copper Coins a share, for a total of 
            " . shortNumberParse($toPlayer) . " Copper Coins. " . shortNumberParse($marketTax) . " Copper Coins was 
            taken as a transaction fee.", false);
        $api->UserGiveCurrency($userid, "primary", $toPlayer);
        addToEconomyLog('Market Fees', 'copper', $marketTax * -1);
        addToEconomyLog('Asset Market', 'copper', $toPlayer);
        logAssetProfit($userid, $profit);
        home();
    }
    else
    {
        $text = ($sellValue < $totalCost) ? "text-danger" : "text-success";
        $text2 = ($r['am_cost'] < $avgCost) ? "text-danger" : "text-success";
        echo "<div class='card'>
            <div class='card-body'>
                You are attemping to sell your " . number_format($sharesTotal) . " shares of the {$r['am_name']} asset. {$r['am_name']} shares currently
                cost <span class='{$text2}'>" . shortNumberParse($r['am_cost']) . "</span> Copper Coins. Your share cost average is " . shortNumberParse($avgCost) . " Copper Coins. Your 
                initial investment of " . shortNumberParse($totalCost) . " Copper Coins is now worth <span class='{$text}'>" . shortNumberParse($sellValue) . "</span> Copper Coins 
                in the current market conditions. How many shares would you like to sell? Please note that there is a 2% processing fee for selling assets.<hr />
                <form method='post'>
                    <div class='row'>
                    <div class='col-12 col-md-6'>
                        <input type='number' min='0' max='{$sharesTotal}' value='{$sharesTotal}' class='form-control' required='1' name='sell' placeholder='Shares to sell'>
                    </div>
                    <div class='col-12 col-md-6'>
                        <input type='submit' class='btn btn-success btn-block' value='Sell Shares'>
                    </div>
                </div>
                </form>
        </div>
        </div>";
    }
}

function history()
{
    global $db, $h;
    $stock_id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
    if (empty($stock_id))
    {
        alert('danger',"Uh Oh!","Please input a valid asset to view.",true,'investmarket.php');
        die($h->endpage());
    }
    $q=$db->query("SELECT * FROM `asset_market` WHERE `am_id` = {$stock_id}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        alert('danger',"Uh Oh!","You are attempting to view the history of an asset that does not exist.",true,'investmarket.php');
        die($h->endpage());
    }
    $q2=$db->query("SELECT * FROM `asset_market_history` WHERE `am_id` = {$stock_id} ORDER BY `timestamp` DESC LIMIT 50");
    if ($db->num_rows($q2) == 0)
    {
        $db->free_result($q2);
        alert('danger',"Uh Oh!","This asset does not have any history at this time.",true,'investmarket.php');
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
    $dataPoints = array();
    $x = 0;
    while ($r2 = $db->fetch_row($q2))
    {
        $x--;
        $y = $r2['new_value'];
        array_push($dataPoints, array("x" => $x, "y" => $y));
    }
    ?>
    <script>
        window.onload = function () {
        	
        var data = [{
        		type: "line",                
        		dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
        	}];
        	
        //Better to construct options first and then pass it as a parameter
        var options = {
        	zoomEnabled: true,
        	animationEnabled: true,
        	title: {
        		text: "<?php echo $r['am_name']; ?>"
        	},
        	axisY: {
        		title: "Asset Value (Copper Coins)",
        		lineThickness: 1
        	},
        	axisX: {
        		title: "Market Ticks"
        	},
        	data: data  // random data
        };
         
        var chart = new CanvasJS.Chart("chartContainer", options);
        chart.render();
         
        }
        </script>
        <div class='card'>
        	<div class='card-body'>
            	<div id="chartContainer" style="height: 370px; width: 100%;">
            	</div>
        	</div>
        </div>
        <?php
}

function staffadd()
{
    global $db, $ir, $userid, $api, $h;
    if ($ir['user_level'] != 'Admin')
    {
        alert('danger',"Uh Oh!","Invalid access.",false);
        home();
    }
    if (isset($_POST['name']))
    {
        $stock_name = (isset($_POST['name']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])*$/i", $_POST['name'])) ? $db->escape(strip_tags(stripslashes($_POST['name']))) : '';
        $stock_cost = (isset($_POST['cost']) && is_numeric($_POST['cost'])) ? abs($_POST['cost']) : '';
        $stock_change = (isset($_POST['change']) && is_numeric($_POST['change'])) ? abs($_POST['change']) : '';
        $stock_risk = (isset($_POST['risk']) && is_numeric($_POST['risk'])) ? abs($_POST['risk']) : '';
        $stock_floor = (isset($_POST['floor']) && is_numeric($_POST['floor'])) ? abs($_POST['floor']) : '';
        $stock_ceiling = (isset($_POST['ceiling']) && is_numeric($_POST['ceiling'])) ? abs($_POST['ceiling']) : '';
        if (empty($stock_name)) 
        {
            alert('danger', "Uh Oh!", "Invalid asset name entered. Go back and remove any and all symbols and spaces.");
            die($h->endpage());
        }
        if (empty($stock_cost))
        {
            alert('danger', "Uh Oh!", "Please specify a valid starting cost for this asset.");
            die($h->endpage());
        }
        if (empty($stock_change))
        {
            alert('danger', "Uh Oh!", "Please specify a valid tick change for this asset.");
            die($h->endpage());
        }
        if (empty($stock_risk))
        {
            alert('danger', "Uh Oh!", "Please specify a risk level for this asset.");
            die($h->endpage());
        }
        if (empty($stock_floor))
        {
            alert('danger', "Uh Oh!", "Please specify a valid minimum floor for this asset.");
            die($h->endpage());
        }
        if (empty($stock_ceiling))
        {
            alert('danger', "Uh Oh!", "Please specify a valid maximum ceiling for this asset.");
            die($h->endpage());
        }
        if (($stock_risk < 1) || ($stock_risk > 5))
        {
            alert('danger', "Uh Oh!", "You've specified an invalid risk level for this asset. Maximum is 5, minimum is 1.");
            die($h->endpage());
        }
        if ($stock_floor >= $stock_ceiling)
        {
            alert('danger', "Uh Oh!", "The asset's minimum floor must be less than it's maximum ceiling.");
            die($h->endpage());
        }
        $check_ex = $db->query("SELECT `am_id` FROM `asset_market` WHERE `am_name` = '{$stock_name}'");
        if ($db->num_rows($check_ex) > 0) 
        {
            alert('danger', "Uh Oh!", "The asset name you've chosen is already in use by another asset.");
            die($h->endpage());
        }
        createStockAsset($stock_name, $stock_cost, $stock_change, $stock_risk, $stock_floor, $stock_ceiling);
        alert('success',"Success","Successfully created a new asset to invest in.", false);
        home();
    }
    else
    {
        echo "<form method='post'>
    <div class='card'>
        <div class='card-header'>
                Adding Asset
        </div>
        <div class='card-body'>
            <div class='row'>
                <div class='col-12 col-md-6 col-xl-4 col-xxl-3 col-xxxl'>
                    <div class='row'>
                        <div class='col-12'>
                            <small>Asset Name</small>
                        </div>
                        <div class='col-12'>
                            <input type='text' class='form-control' required='1' name='name' placeholder='Asset name'>
                        </div>
                    </div>
                </div>
                <div class='col-12 col-md-6 col-xl-4 col-xxl-3 col-xxxl'>
                    <div class='row'>
                        <div class='col-12'>
                            <small>Asset Cost</small>
                        </div>
                        <div class='col-12'>
                            <input type='number' class='form-control' required='1' name='cost' placeholder='Asset starting cost' min='0'>
                        </div>
                    </div>
                </div>
                <div class='col-12 col-md-6 col-xl-4 col-xxl-3 col-xxxl'>
                    <div class='row'>
                        <div class='col-12'>
                            <small>Asset Change</small>
                        </div>
                        <div class='col-12'>
                            <input type='number' class='form-control' required='1' name='change' placeholder='Asset tick change' min='0'>
                        </div>
                    </div>
                </div>
                <div class='col-12 col-md-6 col-xl-4 col-xxl-3 col-xxxl'>
                    <div class='row'>
                        <div class='col-12'>
                            <small>Asset Risk</small>
                        </div>
                        <div class='col-12'>
                            <input type='number' class='form-control' required='1' name='risk' placeholder='Asset risk level' min='0'>
                        </div>
                    </div>
                </div>
                <div class='col-12 col-md-6 col-xl-4 col-xxl-3 col-xxxl'>
                    <div class='row'>
                        <div class='col-12'>
                            <small>Asset Floor</small>
                        </div>
                        <div class='col-12'>
                            <input type='number' class='form-control' required='1' name='floor' placeholder='Minimum this asset can drop.' min='0'>
                        </div>
                    </div>
                </div>
                <div class='col-12 col-md-6 col-xl-4 col-xxl-3 col-xxxl'>
                    <div class='row'>
                        <div class='col-12'>
                            <small>Asset Ceiling</small>
                        </div>
                        <div class='col-12'>
                            <input type='number' class='form-control' required='1' name='ceiling' placeholder='Maximum asset can reach.' min='1' max='" . PHP_INT_MAX . "'>
                        </div>
                    </div>
                </div>
                <div class='col-12 col-xxl-6 col-xxxl-12'>
                    <input type='submit' class='btn btn-success btn-block' value='Create Asset'>
                </div>
            </div>
        </div>
    </div>
</form>";
    }
}

function portfolio()
{
    global $db, $api, $userid, $h;
    $totalShares = returnUserAllAssetShares($userid);
    $totalInvested = returnUserAllAssetCosts($userid);
    $totalValue = returnUserCurrentValueAllAsset($userid);
    $totalValueClass = (($totalValue >= $totalInvested) && ($totalInvested > 0)) ? "text-success" : "text-danger";
    $qLifeTimeProfit = $db->query("/*qc=on*/SELECT `profit` FROM `asset_market_profit` WHERE `userid` = {$userid}");
    $lifeTimeProfit = $db->fetch_single($qLifeTimeProfit);
    $totalProfitClass = ($lifeTimeProfit > 0) ? "text-success" : "text-danger";
    echo "<div class='row'>
            <div class='col-12'>
                <div class='card'>
                    <div class='card-header'>
                        Portfolio
                    </div>
                    <div class='card-body'>
                        <div class='row'>
                            <div class='col-12 col-sm-6 col-xl-4 col-xxl-3 col-xxxl'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Total Shares</b></small>
                                    </div>
                                    <div class='col-12'>
                                        " . number_format($totalShares) . "
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-xl-4 col-xxl-3 col-xxxl'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Total Invested</b></small>
                                    </div>
                                    <div class='col-12'>
                                        " . shortNumberParse($totalInvested) . " Copper Coins
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-xl-4 col-xxl-3 col-xxxl'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Portfolio Value</b></small>
                                    </div>
                                    <div class='col-12 {$totalValueClass}'>
                                        " . shortNumberParse($totalValue) . " Copper Coins
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-xl-4 col-xxl-3 col-xxxl'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Lifetime Profit</b></small>
                                    </div>
                                    <div class='col-12 {$totalProfitClass}'>
                                        " . shortNumberParse($lifeTimeProfit) . " Copper Coins
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br />
            <div class='col-12'>
                <div class='card'>
                    <div class='card-header'>
                        Your Assets
                    </div>
                    <div class='card-body'>";
                            $q = $db->query("SELECT * FROM `asset_market`");
                            while ($r = $db->fetch_row($q))
                            {
                                $sharesTotal = returnUserAssetShares($userid, $r['am_id']);
                                $totalCost = returnUserAssetCosts($userid, $r['am_id']);
                                $currentValue = $sharesTotal * $r['am_cost'];
                                
                                $valueClass = (($currentValue >= $totalCost) && ($totalCost > 0)) ? "text-success" : "text-danger";
                                $avgCost = 0;
                                if ($sharesTotal > 0)
                                    $avgCost = round($totalCost / $sharesTotal);
                                echo "
                                <div class='row'>
                                <div class='col-12 col-sm-8 col-xl-4 col-xxl-3 col-xxxl'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <small><b>Asset Name</b></small>
                                        </div>
                                        <div class='col-12'>
                                            <a href='?action=history&id={$r['am_id']}'>{$r['am_name']}</a>
                                        </div>
                                    </div>
                                </div>
                                <div class='col-12 col-sm-4 col-xl-4 col-xxl-3 col-xxxl'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <small><b>Assets Owned</b></small>
                                        </div>
                                        <div class='col-12'>
                                            " . number_format($sharesTotal) . "
                                        </div>
                                    </div>
                                </div>
                                <div class='col-12 col-sm-4 col-xl-4 col-xxl-3 col-xxxl'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <small><b>Avg Cost</b></small>
                                        </div>
                                        <div class='col-12'>
                                            " . shortNumberParse($avgCost) . "
                                        </div>
                                    </div>
                                </div>
                                <div class='col-12 col-sm-4 col-xl-4 col-xxl-3 col-xxxl'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <small><b>Invested</b></small>
                                        </div>
                                        <div class='col-12'>
                                            " . shortNumberParse($totalCost) . "
                                        </div>
                                    </div>
                                </div>
                                <div class='col-12 col-sm-4 col-xl-4 col-xxl-3 col-xxxl'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <small><b>Current Value</b></small>
                                        </div>
                                        <div class='col-12 {$valueClass}'>
                                            " . shortNumberParse($currentValue) . "
                                        </div>
                                    </div>
                                </div>
                                </div>
                                <hr />";
                            }
                            echo"
                    </div>
                </div>
            </div>
    </div>";
}
include('forms/popup_invest.php');
$h->endpage();