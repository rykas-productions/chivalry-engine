<?php
/*
 *  File: dailyrewardstore.php
 *  Author: MasterGeneral156
 *  Website: https://chivalryisdeadgame.com/
 *  Date: 6/6/2025
 *  License: MIT License 2025
 */
require('globals.php');
$loginPoints = getUserPref($userid, 'loginPoints', 1.1);
alert('info',"","You currently have {$loginPoints} Login Points. Gain more by logging in daily!",false);
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "buy":
        buy();
        break;
    default:
        home();
        break;
}

function home()
{
    global $db, $loginPoints, $userid, $h, $api;
    echo "  <div class='card'>
                <div class='card-header'>
                    These are the current offers you may exchange your Login Points for.
                </div>
                <div class='card-body'>";
                $q = $db->query("SELECT * FROM `daily_reward_shop` ORDER BY `drs_cost` ASC");
                while ($r = $db->fetch_row($q))
                {
                    echo "<div class='row'>
                            <div class='col-12 col-md-6'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Item(s)</b></small>
                                    </div>
                                    <div class='col-12 col-sm-4 col-md-12 col-xl-4'>
                                        <a href='iteminfo.php?ID={$r['drs_item']}'>" . returnIcon($r['drs_item'], 4) . "
                                    </div>
                                    <div class='col-12 col-sm-8 col-md-12 col-xl-8'>
                                        " . shortNumberParse($r['drs_item_qty']) . " x {$api->SystemItemIDtoName($r['drs_item'])}</a>
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-md-4'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Cost</b></small>
                                    </div>
                                    <div class='col'>
                                        {$r['drs_cost']} Login Points
                                    </div>
                                </div>
                            </div>
                            <div class='col-12 col-sm-6 col-md-2'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Action</b></small>
                                    </div>
                                    <div class='col'>
                                        <a href='?action=buy&id={$r['drs_id']}' class='btn btn-primary btn-block'>Buy</a>
                                    </div>
                                </div>
                            </div>
                    </div>";
                }
    
    
            echo"</div></div>";
            $h->endpage();
}

function buy()
{
    global $db, $loginPoints, $userid, $h, $api;
    $_GET['id'] = abs($_GET['id']);
    if (empty($_GET['id']))
    {
        alert("danger","Uh Oh!","You have chosen to buy a non-existent listing.",true,'dailyrewardstore.php');
        die($h->endpage());
    }
    $q = $db->query("SELECT * FROM `daily_reward_shop` WHERE `drs_id` = {$_GET['id']}");
    if ($db->num_rows($q) == 0)
    {
        alert("danger","Uh Oh!","You have chosen to buy a non-existent listing.",true,'dailyrewardstore.php');
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
    if ($loginPoints < $r['drs_cost'])
    {
        alert("danger","Uh Oh!","You do not have enough Login Points to buy this reward. You need {$r['drs_cost']} Login Points but only have {$loginPoints} Login Points.",true,'dailyrewardstore.php');
        die($h->endpage());
    }
    setCurrentUserPref('loginPoints', ($loginPoints - $r['drs_cost']));
    $api->UserGiveItem($userid, $r['drs_item'], $r['drs_item_qty']);
    $api->SystemLogsAdd($userid, "loginreward", "exchanged {$r['drs_cost']} Login Points for {$r['drs_item_qty']} x {$api->SystemItemIDtoName($r['drs_item'])}(s)");
    alert('success','',"You have successfully exchanged {$r['drs_cost']} Login Points for {$r['drs_item_qty']} x {$api->SystemItemIDtoName($r['drs_item'])}(s).",true,"dailyrewardstore.php");
}