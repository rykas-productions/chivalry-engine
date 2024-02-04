<?php
/*
	File:		iteminfo.php
	Created: 	4/5/2016 at 12:14AM Eastern Time
	Info: 		Displays detailed information about the item inputted.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
$_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs($_GET['ID']) : '';
$itmid = $_GET['ID'];
if (!$itmid) {
    alert('danger', 'Uh Oh!', 'Invalid or non-existent Item ID.', true, 'inventory.php');
} else {
    $q =
        $db->query(
            "/*qc=on*/SELECT `i`.*, `itmtypename`
                     FROM `items` AS `i`
                     INNER JOIN `itemtypes` AS `it`
                     ON `i`.`itmtype` = `it`.`itmtypeid`
                     WHERE `i`.`itmid` = {$itmid}
                     LIMIT 1");
	$total=returnTotalItemCount($itmid);
    if ($db->num_rows($q) == 0) 
    {
        alert('danger', 'Uh Oh!', 'Invalid or non-existent Item ID.', true, 'inventory.php');
    } 
    else 
    {
        $id = $db->fetch_row($q);
        $txt = "";
        for ($enum = 1; $enum <= 3; $enum++)
        {
            if ($id["effect{$enum}_on"] == 'true')
            {
                $start = $start + 1;
                $einfo = unserialize($id["effect{$enum}"]);
                $einfo['inc_type'] = ($einfo['inc_type'] == 'percent') ? '%' : '';
                $einfo['dir'] = ($einfo['dir'] == 'pos') ? '+' : '-';
                $class = ($einfo['dir'] == '+') ? 'text-success' : 'text-danger';
                $statformatted = statParser($einfo['stat']);
                $txt .= "<span class='{$class}'>{$einfo['dir']}" . shortNumberParse($einfo['inc_amount']) . "{$einfo['inc_type']} {$statformatted}
									</span>";
            }
        }
            
        echo "<div class='row'>
            <div class='col-12'>
                <div class='card'>
                    <div class='card-header'>
                        {$id['itmname']} " . parseUserID($itmid) . "
                    </div>
                    <div class='card-body'>
                        <div class='row'>
                            <div class='col-12 col-sm-auto col-md-3 col-xl-2 col-xxxl-1'>
                                " . returnIcon($itmid, 6) . "
                            </div>
                            <div class='col-12 col-sm'>
                                <div class='row'>
                                    <div class='col-12'>
                                        {$id['itmdesc']}
                                    </div>";
                                    if (!empty($txt))
                                    {
                                        echo"
                                        <div class='col-12'>
                                            ({$txt} when used/equipped)
                                        </div>";
                                    }
                                        echo"
                                    <div class='col-12'>
                                        <i><b>{$id['itmname']} is a/an {$id['itmtypename']} item.</b></i>
                                    </div>";
                                    if ($id['itmbuyprice'] > 0)
                                    {
                                        echo "<div class='col-12 col-sm-6'>
                                            <div class='row'>
                                                <div class='col-12'>
                                                    <small><b>Purchase Price</b></small>
                                                </div>
                                                <div class='col-12'>
                                                " . shortNumberParse($id['itmbuyprice']) . " Copper Coins
                                                </div>
                                            </div>
                                        </div>";
                                    }
                                    
                                    if ($id['itmsellprice'] > 0)
                                    {
                                        echo "<div class='col-12 col-sm-6'>
                                            <div class='row'>
                                                <div class='col-12'>
                                                    <small><b>Sell Price</b></small>
                                                </div>
                                                <div class='col-12'>
                                                " . shortNumberParse($id['itmsellprice']) . " Copper Coins
                                                </div>
                                            </div>
                                        </div>";
                                    }
                                    
                                    if ($id['weapon'] > 0)
                                    {
                                        echo "<div class='col-12 col-sm-6'>
                                            <div class='row'>
                                                <div class='col-12'>
                                                    <small><b>Weapon Rating</b></small>
                                                </div>
                                                <div class='col-12'>
                                                " . shortNumberParse($id['weapon']) . "
                                                </div>
                                            </div>
                                        </div>";
                                    }
                                    
                                    if ($id['armor'] > 0)
                                    {
                                        echo "<div class='col-12 col-sm-6'>
                                            <div class='row'>
                                                <div class='col-12'>
                                                    <small><b>Armor Rating</b></small>
                                                </div>
                                                <div class='col-12'>
                                                " . shortNumberParse($id['armor']) . "
                                                </div>
                                            </div>
                                        </div>";
                                    }
                                    
                                    if ($id['ammo'] > 0)
                                    {
                                        echo "<div class='col-12 col-sm-6'>
                                            <div class='row'>
                                                <div class='col-12'>
                                                    <small><b>Projectile Fired</b></small>
                                                </div>
                                                <div class='col-12'>
                                                <a href='?ID={$id['ammo']}'>{$api->SystemItemIDtoName($id['ammo'])}</a>
                                                </div>
                                            </div>
                                        </div>";
                                    }  
                                echo "<div class='col-12 col-sm-6'>
                                            <div class='row'>
                                                <div class='col-12'>
                                                    <small><b>Circulating</b></small>
                                                </div>
                                                <div class='col-12'>
                                                    " . shortNumberParse($total) . "
                                                </div>
                                            </div>
                                        </div>";
                                $towns='';
                                $sq=$db->query("/*qc=on*/SELECT `sitemSHOP` FROM `shopitems` WHERE `sitemITEMID` = {$_GET['ID']}");
                                if ($db->num_rows($sq) > 0)
                                {
                                    while ($sr=$db->fetch_row($sq))
                                    {
                                        $shop=$db->fetch_single($db->query("/*qc=on*/SELECT `shopLOCATION` FROM `shops` WHERE `shopID` = {$sr['sitemSHOP']}"));
                                        $towns.= createRandomBadge("<a href='travel.php?to={$shop}'>{$api->SystemTownIDtoName($shop)}</a>") . " ";
                                    }
                                    echo"
                                    <div class='col-auto'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <small><b>Town Shops</b></small>
                                            </div>
                                            <div class='col-12'>
                                                {$towns}
                                            </div>
                                        </div>
                                    </div>";
                                }
                                $towns='';
                                $sq=$db->query("/*qc=on*/SELECT `mine_location` FROM `mining_data` WHERE `mine_copper_item` = {$_GET['ID']} OR `mine_silver_item` = {$_GET['ID']} OR `mine_gold_item` = {$_GET['ID']} OR `mine_gem_item` = {$_GET['ID']}");
                                if ($db->num_rows($sq) > 0)
                                {
                                    while ($sr=$db->fetch_row($sq))
                                    {
                                        $shop2=$sr['mine_location'];
                                        $towns.= createRandomBadge("<a href='travel.php?to={$shop2}'>{$api->SystemTownIDtoName($shop2)}</a>") . " ";
                                    }
                                    echo"
                                    <div class='col-auto'>
                                        <div class='row'>
                                            <div class='col-12'>
                                                <small><b>Town Mines</b></small>
                                            </div>
                                            <div class='col-12'>
                                                {$towns}
                                            </div>
                                        </div>
                                    </div>";
                                }
                            echo "</div>";
                            
                            echo"
                        </div>
                    </div>
                </div>
            </div>";
        echo "</div>";
        $chanceDropItems = array(33);
        if (in_array($itmid, $chanceDropItems))
        {
            echo "<div class='col-12'>
                        <div class='card'>
                            <div class='card-header'>
                                Chance Drops
                            </div>
                            <div class='card-body'>
                                <div class='row'>";
                                if ($itmid == 33)
                                {
                                    echo "<div class='col-12 col-sm-6 col-md-4 col-xxl-3 col-xxxl-2'>
                                            <div class='row'>
                                                    <div class='col-12'>
                                                        <small><b>37.9%</b></small>
                                                    </div>
                                                    <div class='col-12'>
                                                        Copper Coins
                                                    </div>
                                                </div>
                                        </div>
                                        <div class='col-12 col-sm-6 col-md-4 col-xxl-3 col-xxxl-2'>
                                            <div class='row'>
                                                    <div class='col-12'>
                                                        <small><b>12.6%</b></small>
                                                    </div>
                                                    <div class='col-12'>
                                                        Chivalry Tokens
                                                    </div>
                                                </div>
                                        </div>
                                        <div class='col-12 col-sm-6 col-md-4 col-xxl-3 col-xxxl-2'>
                                            <div class='row'>
                                                    <div class='col-12'>
                                                        <small><b>5.74%</b></small>
                                                    </div>
                                                    <div class='col-12'>
                                                        Infirmary Visit
                                                    </div>
                                                </div>
                                        </div>
                                        <div class='col-12 col-sm-6 col-md-4 col-xxl-3 col-xxxl-2'>
                                            <div class='row'>
                                                    <div class='col-12'>
                                                        <small><b>5.74%</b></small>
                                                    </div>
                                                    <div class='col-12'>
                                                        Bread
                                                    </div>
                                                </div>
                                        </div>
                                        <div class='col-12 col-sm-6 col-md-4 col-xxl-3 col-xxxl-2'>
                                            <div class='row'>
                                                    <div class='col-12'>
                                                        <small><b>5.74%</b></small>
                                                    </div>
                                                    <div class='col-12'>
                                                        Venison
                                                    </div>
                                                </div>
                                        </div>
                                        <div class='col-12 col-sm-6 col-md-4 col-xxl-3 col-xxxl-2'>
                                            <div class='row'>
                                                    <div class='col-12'>
                                                        <small><b>5.74%</b></small>
                                                    </div>
                                                    <div class='col-12'>
                                                        Small Health Potion
                                                    </div>
                                                </div>
                                        </div>
                                        <div class='col-12 col-sm-6 col-md-4 col-xxl-3 col-xxxl-2'>
                                            <div class='row'>
                                                    <div class='col-12'>
                                                        <small><b>5.74%</b></small>
                                                    </div>
                                                    <div class='col-12'>
                                                        Linen Wraps
                                                    </div>
                                                </div>
                                        </div>
                                        <div class='col-12 col-sm-6 col-md-4 col-xxl-3 col-xxxl-2'>
                                            <div class='row'>
                                                    <div class='col-12'>
                                                        <small><b>5.74%</b></small>
                                                    </div>
                                                    <div class='col-12'>
                                                        Dungeon Keys
                                                    </div>
                                                </div>
                                        </div>
                                        <div class='col-12 col-sm-6 col-md-4 col-xxl-3 col-xxxl-2'>
                                            <div class='row'>
                                                    <div class='col-12'>
                                                        <small><b>3.44%</b></small>
                                                    </div>
                                                    <div class='col-12'>
                                                        Small Explosive
                                                    </div>
                                                </div>
                                        </div>
                                        <div class='col-12 col-sm-6 col-md-4 col-xxl-3 col-xxxl-2'>
                                            <div class='row'>
                                                    <div class='col-12'>
                                                        <small><b>2.29%</b></small>
                                                    </div>
                                                    <div class='col-12'>
                                                        Chivalry Gym Scroll
                                                    </div>
                                                </div>
                                        </div>
                                        <div class='col-12 col-sm-6 col-md-4 col-xxl-3 col-xxxl-2'>
                                            <div class='row'>
                                                    <div class='col-12'>
                                                        <small><b>2.29%</b></small>
                                                    </div>
                                                    <div class='col-12'>
                                                        Distant Attack Scroll
                                                    </div>
                                                </div>
                                        </div>
                                        <div class='col-12 col-sm-6 col-md-4 col-xxl-3 col-xxxl-2'>
                                            <div class='row'>
                                                    <div class='col-12'>
                                                        <small><b>2.29%</b></small>
                                                    </div>
                                                    <div class='col-12'>
                                                        Mysterious Potion
                                                    </div>
                                                </div>
                                        </div>
                                        <div class='col-12 col-sm-6 col-md-4 col-xxl-3 col-xxxl-2'>
                                            <div class='row'>
                                                    <div class='col-12'>
                                                        <small><b>2.29%</b></small>
                                                    </div>
                                                    <div class='col-12'>
                                                        Acupuncture Needle
                                                    </div>
                                                </div>
                                        </div>
                                        <div class='col-12 col-sm-6 col-md-4 col-xxl-3 col-xxxl-2'>
                                            <div class='row'>
                                                    <div class='col-12'>
                                                        <small><b>1.37%</b></small>
                                                    </div>
                                                    <div class='col-12'>
                                                        Nothing
                                                    </div>
                                                </div>
                                            </div>
                                        <div class='col-12 col-sm-6 col-md-4 col-xxl-3 col-xxxl-2'>
                                            <div class='row'>
                                                    <div class='col-12'>
                                                        <small><b>1.14%</b></small>
                                                    </div>
                                                    <div class='col-12'>
                                                        Herb of Enlightened Miner
                                                    </div>
                                                </div>
                                            </div>
                                        <div class='col-12 col-sm-6 col-md-4 col-xxl-3 col-xxxl-2'>
                                            <div class='row'>
                                                    <div class='col-12'>
                                                        <small><b>1.03%</b></small>
                                                    </div>
                                                    <div class='col-12'>
                                                        Rickity Bomb
                                                    </div>
                                                </div>
                                            </div>
                                        <div class='col-12 col-sm-6 col-md-4 col-xxl-3 col-xxxl-2'>
                                            <div class='row'>
                                                    <div class='col-12'>
                                                        <small><b>1.03%</b></small>
                                                    </div>
                                                    <div class='col-12'>
                                                        1-3 Hexbags
                                                    </div>
                                                </div>
                                            </div>";
                                }
                                    
                            echo "</div></div>
                        </div>
                    </div>";
        }
        echo "</div>";
        $db->free_result($q);
    }
}
$h->endpage();