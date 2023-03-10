<?php
/*	File:		reocurringmerchant.php
	Created: 	Aug 3, 2022; 10:27:11 PM
	Info: 		
	Author:		Ryan
	Website: 	https://chivalryisdeadgame.com/
*/
/*
 * CREATE TABLE `merchant_user_data` ( 
 *  `mu_user` BIGINT(11) UNSIGNED NOT NULL , 
 *  `mu_stage` TINYINT(11) UNSIGNED NOT NULL DEFAULT '0' , 
 *  `mu_time` BIGINT(11) UNSIGNED NOT NULL DEFAULT '0' , 
 *  `mu_stock` TEXT NOT NULL , UNIQUE (`mu_user`)
 *  ) ENGINE = InnoDB;
 */
//$menuhide = 1								//uncomment if wish to hide menus and css.
$macropage = ('reoccurringmerchant.php');					//uncomment if user must pass macro test
require('globals.php');						//uncomment if user needs to be auth'd.
//require('globals_nonauth.php');		//uncomment if no auth required.
//require('sglobals.php');						//uncomment if in staff panel.

$cooldownInHours = 8;
$tokenPrice = $set['token_minimum'];

$mercQuery = $db->query("SELECT * FROM `merchant_user_data` WHERE `mu_user` = {$userid}");
if ($db->num_rows($mercQuery) == 0)
{
    alert('danger',"Uh Oh!","The Reoccurring Merchant is not available for your account right now...", true, 'explore.php');
    die($h->endpage());
}

$mercData = $db->fetch_row($mercQuery);
if ($mercData['mu_stage'] == 0)
{
    alert('primary',"","The Reoccurring Merchant is in port for the next " . TimeUntil_Parse($mercData['mu_time']) . ".",false);
    echo "<div class='row'>
    <div class='col-12'>
        <div class='card'>
            <div class='card-header'>
                Reoccurring Merchant's Wares
            </div>
        </div>
    </div>
    </div>";
}
elseif ($mercData['mu_stage'] == 1)
{
    
}
else 
{
    
}