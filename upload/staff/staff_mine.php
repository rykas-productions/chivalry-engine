<?php
/*
	File: 		staff/staff_mine.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows staff to do actions relating to the in-game mines.
	Author: 	TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine/
	
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
require('sglobals.php');
echo "<h2>Staff Mines</h2><hr />";
if (!$api->user->getStaffLevel($userid, 'Admin')) {
    alert('danger', "Uh Oh!", "You do not have permission to be here.");
    die($h->endpage());
}
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'addmine':
        addmine();
        break;
    case 'editmine':
        editmine();
        break;
    case 'delmine':
        delmine();
        break;
    default:
        menu();
        break;
}
function menu()
{
	echo "<h3>Mining Staff Menu</h3><hr />
    <a href='?action=addmine' class='btn btn-primary'>Create Mine</a><br /><br />
    <a href='?action=editmine' class='btn btn-primary'>Edit Mine</a><br /><br />
    <a href='?action=delmine' class='btn btn-primary'>Delete Mine</a><br /><br />";
}
function addmine()
{
    global $db, $userid, $api, $h;
    if (isset($_POST['level']) && (!empty($_POST['level']))) {
        $level = (isset($_POST['level']) && is_numeric($_POST['level'])) ? abs(intval($_POST['level'])) : '';
        $power = (isset($_POST['power']) && is_numeric($_POST['power'])) ? abs(intval($_POST['power'])) : '';
        $iq = (isset($_POST['IQ']) && is_numeric($_POST['IQ'])) ? abs(intval($_POST['IQ'])) : '';
        $pick = (isset($_POST['pick']) && is_numeric($_POST['pick'])) ? abs(intval($_POST['pick'])) : '';
        $city = (isset($_POST['city']) && is_numeric($_POST['city'])) ? abs(intval($_POST['city'])) : '';
        $cflakes = (isset($_POST['cflakes']) && is_numeric($_POST['cflakes'])) ? abs(intval($_POST['cflakes'])) : '';
        $cflakesmin = (isset($_POST['cflakemin']) && is_numeric($_POST['cflakemin'])) ? abs(intval($_POST['cflakemin'])) : '';
        $cflakesmax = (isset($_POST['cflakemax']) && is_numeric($_POST['cflakemax'])) ? abs(intval($_POST['cflakemax'])) : '';
        $sflakes = (isset($_POST['sflakes']) && is_numeric($_POST['sflakes'])) ? abs(intval($_POST['sflakes'])) : '';
        $sflakesmin = (isset($_POST['sflakemin']) && is_numeric($_POST['sflakemin'])) ? abs(intval($_POST['sflakemin'])) : '';
        $sflakesmax = (isset($_POST['sflakemax']) && is_numeric($_POST['sflakemax'])) ? abs(intval($_POST['sflakemax'])) : '';
        $gflakes = (isset($_POST['gflakes']) && is_numeric($_POST['gflakes'])) ? abs(intval($_POST['gflakes'])) : '';
        $gflakesmin = (isset($_POST['gflakemin']) && is_numeric($_POST['gflakemin'])) ? abs(intval($_POST['gflakemin'])) : '';
        $gflakesmax = (isset($_POST['gflakemax']) && is_numeric($_POST['gflakemax'])) ? abs(intval($_POST['gflakemax'])) : '';
        $gem = (isset($_POST['gem']) && is_numeric($_POST['gem'])) ? abs(intval($_POST['gem'])) : '';
        if (empty($level) || empty($iq) || empty($pick) || empty($city) || empty($cflakes)
            || empty($cflakesmin) || empty($cflakesmax) || empty($gflakes) || empty($gflakesmin)
            || empty($gflakesmax) || empty($sflakesmin) || empty($sflakesmax) || empty($sflakes) || empty($gem) || empty($power)
        ) {
            alert('danger', "Uh Oh!", "Please fill out the form completely before submitting it.");
            die($h->endpage());
        } elseif ($level < 1) {
            alert('danger', "Uh Oh!", "The minimum mining level cannot be lower than 1.");
            die($h->endpage());
        } elseif ($cflakesmin == 0 || $cflakesmax == 0 || $gflakesmin == 0 ||
            $gflakesmax == 0 || $sflakesmin == 0 || $sflakesmax == 0
        ) {
            alert('danger', "Uh Oh!", "Please specify item output numbers.");
            die($h->endpage());
        } elseif ($cflakesmin >= $cflakesmax || $sflakesmin >= $sflakesmax || $gflakesmin >= $gflakesmax) {
            alert('danger', "Uh Oh!", "Your output minimums cannot be higher than their maximums.");
            die($h->endpage());
        } else {
            $CitySQL = ($db->query("SELECT `town_name` FROM `town` WHERE `town_id` = {$city}"));
            $PickSQL = ($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$pick}"));
            $CFSQL = ($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$cflakes}"));
            $SFSQL = ($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$sflakes}"));
            $GFSQL = ($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$gflakes}"));
            $GemSQL = ($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$gem}"));

            if ($db->num_rows($CitySQL) == 0) {
                alert('danger', "Uh Oh!", "The town you've chosen does not exist.");
                die($h->endpage());
            } elseif ($db->num_rows($PickSQL) == 0) {
                alert('danger', "Uh Oh!", "The pickaxe item you have chosen does not exist.");
                die($h->endpage());
            } elseif ($db->num_rows($CFSQL) == 0) {
                alert('danger', "Uh Oh!", "The first item you've chosen does not exist.");
                die($h->endpage());
            } elseif ($db->num_rows($SFSQL) == 0) {
                alert('danger', "Uh Oh!", "The second item you've chosen does not exist.");
                die($h->endpage());
            } elseif ($db->num_rows($GFSQL) == 0) {
                alert('danger', "Uh Oh!", "The third item you've chosen does not exist.");
                die($h->endpage());
            } elseif ($db->num_rows($GemSQL) == 0) {
                alert('danger', "Uh Oh!", "The gem item you've chosen does not exist.");
                die($h->endpage());
            } else {
                $db->query("INSERT INTO `mining_data` 
                (`mine_id`, `mine_location`, `mine_level`, 
                `mine_copper_min`, `mine_copper_max`, 
                `mine_silver_min`, `mine_silver_max`, 
                `mine_gold_min`, `mine_gold_max`, 
                `mine_pickaxe`, `mine_iq`, 
                `mine_power_use`, `mine_copper_item`, 
                `mine_silver_item`, `mine_gold_item`, 
                `mine_gem_item`) 
                VALUES 
                (NULL, '{$city}', '{$level}', '{$cflakesmin}', 
                '{$cflakesmax}', '{$sflakesmin}', '{$sflakesmax}', 
                '{$gflakesmin}', '{$gflakesmax}', '{$pick}', '{$iq}', 
                '{$power}', '{$cflakes}', '{$sflakes}', '{$gflakes}', 
                '{$gem}');");
                $api->game->addLog($userid, "staff", "Added a mine in " . $api->game->getTownNameFromID($city));
                alert('success', "Success!", "You have successfully created a mine in " . $api->game->getTownNameFromID($city));
                die($h->endpage());
            }

        }
    } else {
        echo "
        <table class='table table-bordered'>
            <form method='post'>
				<tr>
					<th colspan='2'>
						Create a mine using this form. The name of the mine will be based on its location and level.
					</th>
				</tr>
                <tr>
                    <th>
						Location
                    </th>
                    <td>
                        " . dropdownLocation("city") . "
                    </td>
                </tr>
                <tr>
                    <th>
						Mining Level Requirement
                    </th>
                    <td>
                        <input type='number' class='form-control' name='level' min='1' required='1'> 
                    </td>
                </tr>
                <tr>
                    <th>
						" . constant("stat_iq") . " Requirement
                    </th>
                    <td>
                        <input type='number' class='form-control' name='IQ' min='1' required='1'> 
                    </td>
                </tr>
                <tr>
                    <th>
						Power Exhaust per Attempt
                    </th>
                    <td>
                        <input type='number' class='form-control' name='power' min='1' required='1'> 
                    </td>
                </tr>
                <tr>
                    <th>
						Required Pickaxe
                    </th>
                    <td>
                        " . dropdownItem("pick") . "
                    </td>
                </tr>
                <tr>
                    <th>
						Item #1
                    </th>
                    <td>
                        " . dropdownItem("cflakes") . "
                    </td>
                </tr>
                <tr>
                    <th>
						Item #2
                    </th>
                    <td>
                        " . dropdownItem("sflakes") . "
                    </td>
                </tr>
                <tr>
                    <th>
						Item #3
                    </th>
                    <td>
                        " . dropdownItem("gflakes") . "
                    </td>
                </tr>
                <tr>
                    <th>
						Gem Item
                    </th>
                    <td>
                        " . dropdownItem("gem") . "
                    </td>
                </tr>
                <tr>
                    <th>
						Item #1 Minimum Output
                    </th>
                    <td>
                        <input type='number' class='form-control' name='cflakemin' min='1' required='1'>
                    </td>
                </tr>
                <tr>
                    <th>
						Item #1 Maximum Output
                    </th>
                    <td>
                        <input type='number' class='form-control' name='cflakemax' min='2' required='1'>
                    </td>
                </tr>
                <tr>
                    <th>
                        Item #2 Minimum Output
                    </th>
                    <td>
                        <input type='number' class='form-control' name='sflakemin' min='1' required='1'>
                    </td>
                </tr>
                <tr>
                    <th>
                        Item #2 Maximum Output
                    </th>
                    <td>
                        <input type='number' class='form-control' name='sflakemax' min='2' required='1'>
                    </td>
                </tr>
                <tr>
                    <th>
                        Item #3 Minimum Output
                    </th>
                    <td>
                        <input type='number' class='form-control' name='gflakemin' min='1' required='1'>
                    </td>
                </tr>
                <tr>
                    <th>
                        Item #3 Maximum Output
                    </th>
                    <td>
                        <input type='number' class='form-control' name='gflakemax' min='2' required='1'>
                    </td>
                </tr>
                <tr>
                    <td colspan='2'>
                        <input type='submit' class='btn btn-primary' value='Create Mine'>
                    </td>
                </tr>
            </form>
        </table>";
    }
}

function editmine()
{
    if (!isset($_POST['step'])) {
        $_POST['step'] = 0;
    }
    global $db, $userid, $h, $api;
    if (isset($_POST['level']) && (!empty($_POST['level'])) && $_POST['step'] == 2) {
        $mine = (isset($_POST['mineid']) && is_numeric($_POST['mineid'])) ? abs(intval($_POST['mineid'])) : '';
        $level = (isset($_POST['level']) && is_numeric($_POST['level'])) ? abs(intval($_POST['level'])) : '';
        $power = (isset($_POST['power']) && is_numeric($_POST['power'])) ? abs(intval($_POST['power'])) : '';
        $iq = (isset($_POST['iq']) && is_numeric($_POST['iq'])) ? abs(intval($_POST['iq'])) : '';
        $pick = (isset($_POST['pick']) && is_numeric($_POST['pick'])) ? abs(intval($_POST['pick'])) : '';
        $city = (isset($_POST['city']) && is_numeric($_POST['city'])) ? abs(intval($_POST['city'])) : '';
        $cflakes = (isset($_POST['cflakes']) && is_numeric($_POST['cflakes'])) ? abs(intval($_POST['cflakes'])) : '';
        $cflakesmin = (isset($_POST['cflakemin']) && is_numeric($_POST['cflakemin'])) ? abs(intval($_POST['cflakemin'])) : '';
        $cflakesmax = (isset($_POST['cflakemax']) && is_numeric($_POST['cflakemax'])) ? abs(intval($_POST['cflakemax'])) : '';
        $sflakes = (isset($_POST['sflakes']) && is_numeric($_POST['sflakes'])) ? abs(intval($_POST['sflakes'])) : '';
        $sflakesmin = (isset($_POST['sflakemin']) && is_numeric($_POST['sflakemin'])) ? abs(intval($_POST['sflakemin'])) : '';
        $sflakesmax = (isset($_POST['sflakemax']) && is_numeric($_POST['sflakemax'])) ? abs(intval($_POST['sflakemax'])) : '';
        $gflakes = (isset($_POST['gflakes']) && is_numeric($_POST['gflakes'])) ? abs(intval($_POST['gflakes'])) : '';
        $gflakesmin = (isset($_POST['gflakemin']) && is_numeric($_POST['gflakemin'])) ? abs(intval($_POST['gflakemin'])) : '';
        $gflakesmax = (isset($_POST['gflakemax']) && is_numeric($_POST['gflakemax'])) ? abs(intval($_POST['gflakemax'])) : '';
        $gem = (isset($_POST['gem']) && is_numeric($_POST['gem'])) ? abs(intval($_POST['gem'])) : '';
        if (empty($level) || empty($iq) || empty($pick) || empty($city) || empty($cflakes)
            || empty($cflakesmin) || empty($cflakesmax) || empty($gflakes) || empty($gflakesmin)
            || empty($gflakesmax) || empty($sflakesmin) || empty($sflakesmax) || empty($sflakes) || empty($gem) || empty($power)
        ) {
            alert('danger', "Uh Oh!", "Please fill out the previous form as completely as possible.");
            die($h->endpage());
        } elseif ($level < 1) {
            alert('danger', "Uh Oh!", "The minimum mining level cannot be lower than 1.");
            die($h->endpage());
        } elseif ($cflakesmin == 0 || $cflakesmax == 0 || $gflakesmin == 0 ||
            $gflakesmax == 0 || $sflakesmin == 0 || $sflakesmax == 0
        ) {
            alert('danger', "Uh Oh!", "Please specify the item outputs.");
            die($h->endpage());
        } elseif ($cflakesmin >= $cflakesmax || $sflakesmin >= $sflakesmax || $gflakesmin >= $gflakesmax) {
            alert('danger', "Uh Oh!", "The item minimum outputs cannot be higher than their maximums.");
            die($h->endpage());
        } else {
            $CitySQL = ($db->query("SELECT `town_name` FROM `town` WHERE `town_id` = {$city}"));
            $PickSQL = ($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$pick}"));
            $CFSQL = ($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$cflakes}"));
            $SFSQL = ($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$sflakes}"));
            $GFSQL = ($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$gflakes}"));
            $GemSQL = ($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$gem}"));
            if ($db->num_rows($CitySQL) == 0) {
                alert('danger', "Uh Oh!", "The town you have chosen does not exist.");
                die($h->endpage());
            } elseif ($db->num_rows($PickSQL) == 0) {
                alert('danger', "Uh Oh!", "The pickaxe item you have chosen does not exist.");
                die($h->endpage());
            } elseif ($db->num_rows($CFSQL) == 0) {
                alert('danger', "Uh Oh!", "The item #1 you have chosen does not exist.");
                die($h->endpage());
            } elseif ($db->num_rows($SFSQL) == 0) {
                alert('danger', "Uh Oh!", "The item #2 you have chosen does not exist.");
                die($h->endpage());
            } elseif ($db->num_rows($GFSQL) == 0) {
                alert('danger', "Uh Oh!", "The item #3 you have chosen does not exist.");
                die($h->endpage());
            } elseif ($db->num_rows($GemSQL) == 0) {
                alert('danger', "Uh Oh!", "The gem item you have chosen does not exist.");
                die($h->endpage());
            } else {
                $db->query("UPDATE `mining_data` SET `mine_location` = '{$city}', `mine_level` = '{$level}',
                `mine_copper_item` = '{$cflakes}', `mine_copper_max` = '{$cflakesmax}', `mine_copper_min` = '{$cflakesmin}', 
                `mine_silver_item` = '{$sflakes}', `mine_silver_max` = '{$sflakesmax}', `mine_silver_min` = '{$sflakesmin}', 
                `mine_gold_item` = '{$gflakes}', `mine_gold_max` = '{$gflakesmax}', `mine_gold_min` = '{$gflakesmin}', 
                `mine_pickaxe` = '{$pick}', `mine_iq` = '{$iq}', `mine_gem_item` = '{$gem}', `mine_power_use` = '{$power}' 
                WHERE `mine_id` = {$mine}");
                $api->game->addLog($userid, "staff", "Edited Mine ID #{$mine}");
                alert('success', "Success!", "You have successfully edited Mine ID #{$mine}.", true, 'index.php');
                die($h->endpage());
            }

        }
    } elseif ($_POST['step'] == 1) {
        $mine = (isset($_POST['mine']) && is_numeric($_POST['mine'])) ? abs(intval($_POST['mine'])) : '';
        if ($db->num_rows($db->query("SELECT * FROM `mining_data` WHERE `mine_id` = {$mine}")) == 0) {
            alert('danger', "Uh Oh!", "You are trying to edit a non-existent mine.");
            die($h->endpage());
        } else {
            $mi = $db->fetch_row($db->query("SELECT * FROM `mining_data` WHERE `mine_id` = {$mine}"));
            echo "Edit a mine with this form. The name of the mine will be based on its location and level.<br />
            <table class='table table-bordered'>
                <form method='post'>
                <input type='hidden' value='2' name='step'>
                <input type='hidden' value='{$mine}' name='mineid'>
                    <tr>
                        <th>
							Location
                        </th>
                        <td>
                            " . dropdownLocation("city", $mi['mine_location']) . "
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Mining Level Requirement
                        </th>
                        <td>
                            <input type='number' class='form-control' name='level' min='1' value='{$mi['mine_level']}' required='1'> 
                        </td>
                    </tr>
                    <tr>
                        <th>
                           " . constant("stat_strength") . " Requirement
                        </th>
                        <td>
                            <input type='number' class='form-control' name='iq' min='1' value='{$mi['mine_iq']}' required='1'> 
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Power Exhaust per Attempt
                        </th>
                        <td>
                            <input type='number' class='form-control' name='power' min='1' value='{$mi['mine_power_use']}' required='1'> 
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Pickaxe Item
                        </th>
                        <td>
                            " . dropdownItem("pick", $mi['mine_pickaxe']) . "
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Item #1
                        </th>
                        <td>
                            " . dropdownItem("cflakes", $mi['mine_copper_item']) . "
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Item #2
                        </th>
                        <td>
                            " . dropdownItem("sflakes", $mi['mine_silver_item']) . "
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Item #3
                        </th>
                        <td>
                            " . dropdownItem("gflakes", $mi['mine_gold_item']) . "
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Gem Item
                        </th>
                        <td>
                            " . dropdownItem("gem", $mi['mine_gem_item']) . "
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Item #1 Minimum Output
                        </th>
                        <td>
                            <input type='number' class='form-control' name='cflakemin' value='{$mi['mine_copper_min']}' min='1' required='1'>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Item #1 Maximum Output
                        </th>
                        <td>
                            <input type='number' class='form-control' name='cflakemax' value='{$mi['mine_copper_max']}' min='2' required='1'>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Item #2 Minimum Output
                        </th>
                        <td>
                            <input type='number' class='form-control' name='sflakemin' value='{$mi['mine_silver_min']}' min='1' required='1'>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Item #2 Maximum Output
                        </th>
                        <td>
                            <input type='number' class='form-control' name='sflakemax' value='{$mi['mine_silver_max']}' min='2' required='1'>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Item #3 Minimum Output
                        </th>
                        <td>
                            <input type='number' class='form-control' name='gflakemin' value='{$mi['mine_gold_min']}' min='1' required='1'>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Item #3 Maximum Output
                        </th>
                        <td>
                            <input type='number' class='form-control' name='gflakemax' value='{$mi['mine_gold_max']}' min='2' required='1'>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='2'>
                            <input type='submit' class='btn btn-primary' value='Edit Mine'>
                        </td>
                    </tr>
                </form>
            </table>";
        }
    } else {
        echo "Select the mine you wish to edit.<br />
        <form method='post'>
        <input type='hidden' name='step' value='1'>
        " . mines_dropdown("mine") . "<br />
        <input type='submit' class='btn btn-primary' value='Edit Mine'>
        ";
    }
}

function delmine()
{
    global $db, $userid, $api, $h;
    if (isset($_POST['mine'])) {
        $mine = (isset($_POST['mine']) && is_numeric($_POST['mine'])) ? abs(intval($_POST['mine'])) : '';
        if ($db->num_rows($db->query("SELECT * FROM `mining_data` WHERE `mine_id` = {$mine}")) == 0) {
            alert('danger', "Uh Oh!", "You are trying to delete a non-existent mine.");
            die($h->endpage());
        } else {
            $db->query("DELETE FROM `mining_data` WHERE `mine_id` = {$mine}");
            $api->game->addLog($userid, "staff", "Deleted a mine.");
            alert('success', "Success!", "You have successfully deleted this mine.", true, 'index.php');
            die($h->endpage());
        }
    } else {
        echo "Select the mine you wish to delete. This cannot be undone.<br />
        <form method='post'>
        " . mines_dropdown("mine") . "<br />
        <input type='submit' class='btn btn-primary' value='Delete Mine'>
        ";
    }
}

function mines_dropdown($ddname = "mine", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
        $db->query(
            "SELECT `mine_id`, `mine_location`, `mine_level`
                     FROM `mining_data`
                     ORDER BY `mine_level` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $CityName = $db->fetch_single($db->query("SELECT `town_name` FROM `town` WHERE `town_id` = {$r['mine_location']}"));
        $ret .= "\n<option value='{$r['mine_id']}'";
        if ($selected == $r['mine_id'] || $first == 0) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$CityName} - Level {$r['mine_level']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

$h->endpage();