<?php
/*
	File: staff/staff_bots.php
	Created: 4/4/2017 at 7:01PM Eastern Time
	Info: Staff panel for handling the NPC Battle Tent.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/
require('sglobals.php');
echo "<h3>Staff Stats</h3>";
if (!$api->UserMemberLevelGet($userid, "Admin")) 
{
    alert('danger', "Uh Oh!", "You do not have permission to be here.", true, 'index.php');
    die($h->endpage());
}
if (!isset($_GET['action'])) 
{
    $_GET['action'] = '';
}
switch ($_GET['action']) 
{
    case "theme":
        theme();
        break;
    case "weapons":
        listweapons();
        break;
    case "weaponscost":
        listweaponsbycost();
        break;
    case "armor":
        listarmor();
        break;
    case "armorcost":
        listarmorbycost();
        break;
    default:
        alert('danger', "Uh Oh!", "Please select a valid action to perform.", true, 'index.php');
        die($h->endpage());
        break;
}

function theme()
{
    global $db;
}

function listarmor()
{
    global $db, $api;
    $q = $db->query("SELECT * FROM `items` WHERE `armor` > 0 ORDER BY `armor` ASC");
    echo "<div class='card'>
            <div class='card-header'>
                Listing all in-game armor [<a href='?action=armorcost'>Sort by Copper/Armor</a>]
            </div>
            <div class='card-body'>";
    while ($r = $db->fetch_row($q))
    {
        echo "<div class='row'>
                <div class='col-auto col-md-3'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Item Name</b></small>
                        </div>
                        <div class='col-12'>
                            <a href='../iteminfo.php?ID={$r['itmid']}'>{$r['itmname']}</a>
                        </div>
                    </div>
                </div>
                <div class='col-auto col-md-3'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Buy (Sell)</b></small>
                        </div>
                        <div class='col-12'>
                            " . shortNumberParse($r['itmbuyprice']) . " (" . shortNumberParse($r['itmsellprice']) . ")
                        </div>
                    </div>
                </div>
                <div class='col-auto col-md-3'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Armor Rating</b></small>
                        </div>
                        <div class='col-12'>
                            " . shortNumberParse($r['armor']) . "
                        </div>
                    </div>
                </div>
                <div class='col-auto col-md-3'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Copper/Armor</b></small>
                        </div>
                        <div class='col-12'>
                            " . shortNumberParse($r['itmbuyprice'] / $r['armor']) . "
                        </div>
                    </div>
                </div>
            </div>";
    }
    echo "</div></div>";
}

function listarmorbycost()
{
    global $db, $api;
    $q = $db->query("SELECT * FROM `items` WHERE `armor` > 0 ORDER BY (`itmbuyprice`/`armor`) ASC");
    echo "<div class='card'>
            <div class='card-header'>
                Listing all in-game armor [<a href='?action=armor'>Sort by Armor</a>]
            </div>
            <div class='card-body'>";
    while ($r = $db->fetch_row($q))
    {
        echo "<div class='row'>
                <div class='col-auto col-md-3'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Item Name</b></small>
                        </div>
                        <div class='col-12'>
                            <a href='../iteminfo.php?ID={$r['itmid']}'>{$r['itmname']}</a>
                        </div>
                    </div>
                </div>
                <div class='col-auto col-md-3'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Buy (Sell)</b></small>
                        </div>
                        <div class='col-12'>
                            " . shortNumberParse($r['itmbuyprice']) . " (" . shortNumberParse($r['itmsellprice']) . ")
                        </div>
                    </div>
                </div>
                <div class='col-auto col-md-3'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Armor Rating</b></small>
                        </div>
                        <div class='col-12'>
                            " . shortNumberParse($r['armor']) . "
                        </div>
                    </div>
                </div>
                <div class='col-auto col-md-3'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Copper/Armor</b></small>
                        </div>
                        <div class='col-12'>
                            " . shortNumberParse($r['itmbuyprice'] / $r['armor']) . "
                        </div>
                    </div>
                </div>
            </div>";
    }
    echo "</div></div>";
}

function listweapons()
{
    global $db, $api;
    $q = $db->query("SELECT * FROM `items` WHERE `weapon` > 0 ORDER BY `weapon` ASC");
    echo "<div class='card'>
            <div class='card-header'>
                Listing all in-game weapons [<a href='?action=weaponscost'>Sort by Copper/Weapon</a>]
            </div>
            <div class='card-body'>";
    while ($r = $db->fetch_row($q))
    {
        $ranged = ($r['ammo'] > 0) ? "*" : "";
        echo "<div class='row'>
                <div class='col-auto col-md-3'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Item Name</b></small>
                        </div>
                        <div class='col-12'>
                            <a href='../iteminfo.php?ID={$r['itmid']}'>{$r['itmname']}</a>{$ranged}
                        </div>
                    </div>
                </div>
                <div class='col-auto col-md-3'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Buy (Sell)</b></small>
                        </div>
                        <div class='col-12'>
                            " . shortNumberParse($r['itmbuyprice']) . " (" . shortNumberParse($r['itmsellprice']) . ")
                        </div>
                    </div>
                </div>
                <div class='col-auto col-md-3'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Weapon Rating</b></small>
                        </div>
                        <div class='col-12'>
                            " . shortNumberParse($r['weapon']) . "
                        </div>
                    </div>
                </div>
                <div class='col-auto col-md-3'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Copper/Weapon</b></small>
                        </div>
                        <div class='col-12'>
                            " . shortNumberParse($r['itmbuyprice'] / $r['weapon']) . "
                        </div>
                    </div>
                </div>
            </div>";
    }
    echo "</div></div>";
}

function listweaponsbycost()
{
    global $db, $api;
    $q = $db->query("SELECT * FROM `items` WHERE `weapon` > 0 ORDER BY (`itmbuyprice`/`weapon`) ASC");
    echo "<div class='card'>
            <div class='card-header'>
                Listing all in-game weapons [<a href='?action=weapons'>Sort by Weapon</a>]
            </div>
            <div class='card-body'>";
    while ($r = $db->fetch_row($q))
    {
        $ranged = ($r['ammo'] > 0) ? "*" : "";
        echo "<div class='row'>
                <div class='col-auto col-md-3'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Item Name</b></small>
                        </div>
                        <div class='col-12'>
                            <a href='../iteminfo.php?ID={$r['itmid']}'>{$r['itmname']}</a>{$ranged}
                        </div>
                    </div>
                </div>
                <div class='col-auto col-md-3'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Buy (Sell)</b></small>
                        </div>
                        <div class='col-12'>
                            " . shortNumberParse($r['itmbuyprice']) . " (" . shortNumberParse($r['itmsellprice']) . ")
                        </div>
                    </div>
                </div>
                <div class='col-auto col-md-3'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Weapon Rating</b></small>
                        </div>
                        <div class='col-12'>
                            " . shortNumberParse($r['weapon']) . "
                        </div>
                    </div>
                </div>
                <div class='col-auto col-md-3'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Copper/Weapon</b></small>
                        </div>
                        <div class='col-12'>
                            " . shortNumberParse($r['itmbuyprice'] / $r['weapon']) . "
                        </div>
                    </div>
                </div>
            </div>";
    }
    echo "</div></div>";
}

$h->endpage();