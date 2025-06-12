<?php
/*
	File:		announcements.php
	Created: 	4/4/2016 at 11:51PM Eastern Time
	Info: 		Lists the game announcements for players to read.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
require('lib/bbcode_engine.php');

if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}

switch ($_GET['action']) {
    case "charlist":
        charlist();
        break;
    default:
        index();
        break;
}

function index()
{
    global $db, $api, $userid, $parser;
    //Select all data from the announcements data table.
    $q = $db->query("SELECT * FROM `game_lore` ORDER BY `lore_time` DESC");
    echo "<div class='card'>
        <div class='card-header'>
            {$set['WebsiteName']} Lore
        </div>
        <div class='card-body'>";
    while ($r = $db->fetch_row($q)) {
        //Parse the announcement time into a user friendly timestamp.
        $AnnouncementTime = date('F j, Y', $r['lore_time']);
        //Make the announcement text safe for the users to read, in case of staff panel compromise.
        $parser->parse($r['lore_info']);
        echo "<div class='row'>
                <div class='col-12'>
                    <b>{$r['lore_title']} ({$AnnouncementTime})</b>
                </div>
                <div class='col-12'>
                    <small><i>" . $parser->getAsHtml() . "</i></small>
                </div>
                <div class='col-12'>
                    <hr />
                </div>
            </div>";
    }
    echo "</div></div>";
    $db->free_result($q);
}

function charlist()
{
    global $db, $api, $userid, $parser, $set;
    //Select all data from the announcements data table.
    $q = $db->query("SELECT * FROM `game_lore_characters` ORDER BY `lc_born` DESC");
        while ($r = $db->fetch_row($q)) 
            {
                //Parse the announcement time into a user friendly timestamp.
                $bornTime = date('F j, Y', $r['lc_born']);
                $deathTime = ($r['lc_death'] != 0) ? date('F j, Y', $r['lc_death']) : "N/A";
                //Make the announcement text safe for the users to read, in case of staff panel compromise.
                $parser->parse($r['lc_bio']);
                $displaypic = "<img src='{$r['lc_pic']}' class='img-thumbnail'>";
                
                echo "  <div class='col-12'>
                            <div class='card'>
                                <div class='card-header'>
                                    {$r['lc_name']}
                                </div>
                                <div class='card-body'>
                                    <div class='row'>
                                        <div class='col-12 col-lg-4 col-xl-3 col-xxl-2'>
                                            <div class='row'>
                                                <div class='col-12'>
                                                    <small><b>Image</b></small>
                                                </div>
                                                <div class='col-12'>
                                                    {$displaypic}
                                                </div>
                                            </div>
                                        </div>
                                        <div class='col-12 col-lg-8 col-xl-9 col-xxl-10'>
                                            <div class='row'>
                                                <div class='col-12'>
                                                    <small><b>Bio</b></small>
                                                </div>
                                                <div class='col-12'>
                                                    " . $parser->getAsHtml() . "
                                                </div>
                                            </div>
                                        </div>
                                        <div class='col-12 col-sm-6'>
                                            <div class='row'>
                                                <div class='col-12'>
                                                    <small><b>Born</b></small>
                                                </div>
                                                <div class='col-12'>
                                                    {$bornTime}
                                                </div>
                                            </div>
                                        </div>
                                        <div class='col-12 col-sm-6'>
                                            <div class='row'>
                                                <div class='col-12'>
                                                    <small><b>Death</b></small>
                                                </div>
                                                <div class='col-12'>
                                                    {$deathTime}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>";
        }
        echo "</div></div>";
        $db->free_result($q);
}
$h->endpage();