<?php
/*
	File:		estates.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows players to buy and sell estates, which increase 
				their will, allowing better gains at the gym.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
	MIT License

	Copyright (c) 2019 TheMasterGeneral

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
*/
require('globals.php');
require_once("includes/vip-benefits.php");

// Initialize VIP benefits
$vipBenefits = getVIPBenefits($db, $userid);
$mpq = $db->query("SELECT * FROM `estates` WHERE `house_will` = {$ir['maxwill']} LIMIT 1");
$mp = $db->fetch_row($mpq);
$db->free_result($mpq);
//User is trying to buy an estate.
if (isset($_GET['property']) && is_numeric($_GET['property'])) {
	$property = filter_input(INPUT_GET, 'property', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    $npq = $db->query("SELECT * FROM `estates` WHERE `house_id` = {$property}");
    //Estate does not exist.
    if ($db->num_rows($npq) == 0) {
        $db->free_result($npq);
        alert('danger', "Uh Oh!", "The estate you are trying to purchase does not exist.", true, 'estates.php');
        die($h->endpage());
    }
    $np = $db->fetch_row($npq);
    $db->free_result($npq);
    //Estate's will is lower than user's current estate.
    if ($np['house_will'] < $mp['house_will']) {
        alert('danger', "Uh Oh!", "The house you are trying to buy is worse than what you currently have.", true, 'estates.php');
        die($h->endpage());
    } //User is trying to buy the same estate.
    else if ($np['house_will'] == $mp['house_will']) {
        alert('danger', "Uh Oh!", "You cannot buy the same house twice.", true, 'estates.php');
        die($h->endpage());
    } //User does not have enoguh primary currency for the new estatte.
    else if ($np['house_price'] > $ir['primary_currency']) {
        alert('danger', "Uh Oh!", "You do not have enough cash to buy this house.", true, 'estates.php');
        die($h->endpage());
    } //User is too low leveled for the estate.
    else if ($np['house_level'] > $ir['level']) {
        alert('danger', "Uh Oh!", "You are not a high level enough to buy this estate.", true, 'estates.php');
        die($h->endpage());
    } //User passes all checks.
    else {
        // Apply VIP discount to estate purchase
        $original_price = $np['house_price'];
        $final_price = $vipBenefits->applyShopDiscount($original_price);
        $discount = $original_price - $final_price;
        
        // Check if user has enough money after discount
        if ($final_price > $ir['primary_currency']) {
            alert('danger', "Uh Oh!", "You do not have enough cash to buy this house.", true, 'estates.php');
            die($h->endpage());
        }
        
        //Update user's max will, remove currency, and set will to 0.
        $db->query("UPDATE `users`
                    SET `primary_currency` = `primary_currency` - {$final_price} ,
                    `will` = 0, `maxwill` = {$np['house_will']}
                    WHERE `userid` = $userid");
        
        // Log VIP benefit usage if discount was applied
        if ($discount > 0) {
            $vipBenefits->logBenefitUsage('estate_purchase', $discount);
        }
        
        $success_message = "You have successfully bought the {$np['house_name']} estate for " . number_format($final_price) . "!";
        if ($discount > 0) {
            $success_message .= " <span class='text-warning'>(VIP Discount: -" . number_format($discount) . ")</span>";
        }
        
        alert('success', "Success!", $success_message, true, 'estates.php');
        die($h->endpage());
    }
} //User wishes to sell their estate.
else if (isset($_GET['sellhouse'])) {
    //User does not own an estate.
    if ($ir['maxwill'] == 100) {
        alert('danger', "Uh Oh!", "You cannot sell your estate if you don't have one!");
    } //User sells estate.
    else {
        // Realistic depreciation - sell for 70% of original price
        $sell_price = floor($mp['house_price'] * 0.7);
        $db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + {$sell_price}, `will` = 0, `maxwill` = 100 WHERE `userid` = $userid");
        alert('success', "Estate Sold!", "You have sold your estate for " . number_format($sell_price) . " " . constant("primary_currency") . " (70% of original price due to depreciation).", true, 'estates.php');
    }
} else {
    // Modern Estate Interface
    echo "<div class='container-fluid'>";
    
    // Page Header
    echo "<div class='row mb-4'>";
    echo "<div class='col-12'>";
    echo "<div class='card bg-gradient-primary text-white'>";
    echo "<div class='card-body'>";
    echo "<h2 class='mb-0'><i class='fas fa-home me-2'></i>Estate Management</h2>";
    echo "<p class='mb-0 mt-2'>Upgrade your living situation to boost your training potential</p>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    
    // Current Estate Info
    echo "<div class='row mb-4'>";
    echo "<div class='col-md-6'>";
    echo "<div class='card'>";
    echo "<div class='card-header bg-success text-white'>";
    echo "<h4><i class='fas fa-house-user'></i> Current Estate</h4>";
    echo "</div>";
    echo "<div class='card-body'>";
    echo "<h5 class='text-primary'>{$mp['house_name']}</h5>";
    echo "<div class='row mb-3'>";
    echo "<div class='col-6'>";
    echo "<div class='text-center'>";
    echo "<h3 class='text-success'>" . number_format($ir['maxwill']) . "</h3>";
    echo "<small class='text-muted'>Max Will</small>";
    echo "</div>";
    echo "</div>";
    echo "<div class='col-6'>";
    echo "<div class='text-center'>";
    echo "<h3 class='text-info'>" . number_format($ir['will']) . "</h3>";
    echo "<small class='text-muted'>Current Will</small>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    
    if ($ir['maxwill'] > 100) {
        $sell_price = floor($mp['house_price'] * 0.7); // 30% depreciation
        echo "<p class='text-muted mb-3'><i class='fas fa-info-circle'></i> Will determines training effectiveness and crime success rates.</p>";
        echo "<a href='?sellhouse' class='btn btn-warning' onclick='return confirm(\"Sell your estate for " . number_format($sell_price) . "? You will lose your current Will level.\")'>";
        echo "<i class='fas fa-tag'></i> Sell Estate (" . number_format($sell_price) . ")";
        echo "</a>";
    } else {
        echo "<p class='text-muted'><i class='fas fa-info-circle'></i> You don't own an estate yet. Purchase one below to increase your training potential!</p>";
    }
    
    echo "</div>";
    echo "</div>";
    echo "</div>";
    
    // VIP Benefits Info
    if ($vipBenefits->isVIP()) {
        echo "<div class='col-md-6'>";
        echo "<div class='card'>";
        echo "<div class='card-header bg-warning text-dark'>";
        echo "<h4><i class='fas fa-crown'></i> VIP Estate Benefits</h4>";
        echo "</div>";
        echo "<div class='card-body'>";
        echo "<div class='alert alert-warning'>";
        echo "<i class='fas fa-percent'></i> <strong>10% Discount</strong> on all estate purchases!";
        echo "</div>";
        echo "<p class='mb-0'>As a VIP member, you receive exclusive discounts on estate purchases.</p>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
    
    echo "</div>";
    
    // Available Estates
    $hq = $db->query("SELECT * FROM `estates` WHERE `house_will` > {$ir['maxwill']} ORDER BY `house_will` ASC");
    
    if ($db->num_rows($hq) > 0) {
        echo "<div class='row mb-4'>";
        echo "<div class='col-12'>";
        echo "<h3><i class='fas fa-store'></i> Available Estate Upgrades</h3>";
        echo "<p class='text-muted'>Click on an estate to purchase it. Higher Will estates provide better training bonuses.</p>";
        echo "</div>";
        echo "</div>";
        
        echo "<div class='row'>";
        
        while ($r = $db->fetch_row($hq)) {
            // Calculate prices with VIP discount
            $original_price = $r['house_price'];
            $display_price = $vipBenefits->applyShopDiscount($original_price);
            $discount_text = '';
            
            if ($vipBenefits->isVIP() && $display_price < $original_price) {
                $discount_text = "<span class='text-muted'><del>" . number_format($original_price) . "</del></span> ";
            }
            
            // Determine affordability
            $can_afford = $display_price <= $ir['primary_currency'];
            $level_req_met = $r['house_level'] <= $ir['level'];
            $card_class = ($can_afford && $level_req_met) ? 'border-success' : 'border-secondary';
            $btn_class = ($can_afford && $level_req_met) ? 'btn-success' : 'btn-secondary';
            
            echo "<div class='col-md-6 col-lg-4 mb-4'>";
            echo "<div class='card {$card_class} h-100'>";
            echo "<div class='card-header bg-light'>";
            echo "<h5 class='mb-0'>{$r['house_name']}</h5>";
            echo "</div>";
            echo "<div class='card-body'>";
            
            echo "<div class='row text-center mb-3'>";
            echo "<div class='col-4'>";
            echo "<div class='border-end'>";
            echo "<h4 class='text-primary'>" . number_format($r['house_will']) . "</h4>";
            echo "<small class='text-muted'>Max Will</small>";
            echo "</div>";
            echo "</div>";
            echo "<div class='col-4'>";
            echo "<div class='border-end'>";
            echo "<h4 class='text-warning'>Lv {$r['house_level']}</h4>";
            echo "<small class='text-muted'>Required</small>";
            echo "</div>";
            echo "</div>";
            echo "<div class='col-4'>";
            echo "<h4 class='text' style='color: " . ($can_afford ? '#28a745' : '#dc3545') . "'>";
            echo "{$discount_text}<span class='fw-bold'>" . number_format($display_price) . "</span>";
            echo "</h4>";
            echo "<small class='text-muted'>Cost</small>";
            echo "</div>";
            echo "</div>";
            
            // Requirements check
            $requirements = [];
            if (!$level_req_met) {
                $requirements[] = "<span class='text-danger'><i class='fas fa-times'></i> Level " . $r['house_level'] . " required</span>";
            }
            if (!$can_afford) {
                $requirements[] = "<span class='text-danger'><i class='fas fa-times'></i> Insufficient funds</span>";
            }
            
            if (!empty($requirements)) {
                echo "<div class='mb-3'>";
                foreach ($requirements as $req) {
                    echo "<div>{$req}</div>";
                }
                echo "</div>";
            }
            
            // Will bonus calculation
            $will_bonus = $r['house_will'] - $ir['maxwill'];
            echo "<div class='alert alert-info mb-3'>";
            echo "<i class='fas fa-arrow-up'></i> <strong>+{$will_bonus} Max Will</strong><br>";
            echo "<small>Better training gains and crime success</small>";
            echo "</div>";
            
            echo "</div>";
            echo "<div class='card-footer'>";
            if ($can_afford && $level_req_met) {
                echo "<a href='?property={$r['house_id']}' class='btn {$btn_class} w-100'>";
                echo "<i class='fas fa-home'></i> Purchase Estate";
                echo "</a>";
            } else {
                echo "<button class='btn {$btn_class} w-100' disabled>";
                echo "<i class='fas fa-lock'></i> Requirements Not Met";
                echo "</button>";
            }
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
        
        echo "</div>";
    } else {
        echo "<div class='alert alert-info'>";
        echo "<h4><i class='fas fa-check-circle'></i> Maximum Estate Reached!</h4>";
        echo "<p class='mb-0'>You already own the best estate available. Your training potential is maximized!</p>";
        echo "</div>";
    }
    
    $db->free_result($hq);
    echo "</div>";
}
$h->endpage();