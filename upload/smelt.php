<?php
/*
	File:		smelt.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows players to create in-game items from other items.
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
echo "<h3>Blacksmith's Smeltery</h3><hr />";
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'smelt':
        smelt();
        break;
    default:
        home();
        break;
}
function home()
{
    global $db, $userid, $api;
    $q = $db->query("SELECT * FROM `smelt_recipes` ORDER BY `smelt_id` ASC");
    echo "<table class='table table-bordered table-striped'>
	<tr>
		<th>
			Item
		</th>
		<th>
			Required Materials
		</th>
		<th>
			Action
		</th>
	</tr>";
    while ($r = $db->fetch_row($q)) {
        $output_item = $api->SystemItemIDtoName($r['smelt_output']);
        $items_needed = '';
        $can_craft = TRUE;
        $ex = explode(",", $r['smelt_items']);
        $qty = explode(",", $r['smelt_quantity']);
        $n = 0;
        echo "
		<tr>
			<td>
				{$output_item} x {$r['smelt_qty_output']}
			</td>
			<td>";
        $n = 0;
        foreach ($ex as $i) {
            $get_items_needed = $db->query("SELECT `itmname` FROM `items` WHERE `itmid`={$i}");
            $t = $db->fetch_row($get_items_needed);

            $do_they_have = $db->query("SELECT `inv_itemid` FROM `inventory` WHERE `inv_userid`={$userid} AND `inv_itemid`={$i} AND `inv_qty`>={$qty[$n]}");
            if ($db->num_rows($do_they_have) == 0) {
                $t['itmname'] = "<span style='color:red;'>" . $t['itmname'] . "</span>";
                $can_craft = FALSE;
            }
            $items_needed .= $t['itmname'] . " x " . $qty[$n] . " (Have " . number_format($api->user->countItem($userid, $i)) . ")<br />";
            $n++;
        }
        unset($n);
        echo "{$items_needed}
			</td>
			<td>";
        if ($can_craft == TRUE) {
            echo "<a href='?action=smelt&id={$r['smelt_id']}'>Smelt Item</a>";
        } else {
            echo "<span style='color:red;'>Cannot Smelt</span>";
        }
        echo "
			</td>
		</tr>";
    }
    echo "</table>";
}

function smelt()
{
    global $db, $userid, $api, $h;
    $_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : 0;
    $q = $db->query("SELECT * FROM `smelt_recipes` WHERE `smelt_id` = {$_GET['id']}");
    if ($db->num_rows($q) == 0) {
        alert('danger', "Uh Oh!", "You are trying to smelt a non-existent recipe.", true, "smelt.php");
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
    $can_craft = TRUE;
    $needs = '';
    $items_needed = '';
    $ex = explode(",", $r['smelt_items']);
    $qty = explode(",", $r['smelt_quantity']);
    $n = 0;
    foreach ($ex as $i) {
        $get_items_needed = $db->query("SELECT `itmname` FROM `items` WHERE `itmid`={$i}");
        $t = $db->fetch_row($get_items_needed);
        $do_they_have = $db->query("SELECT `inv_itemid` FROM `inventory` WHERE `inv_userid`={$userid} AND `inv_itemid`={$i} AND `inv_qty`>={$qty[$n]}");
        if ($db->num_rows($do_they_have) == 0) {
            $needs = $t['itmname'] . " x " . $qty[$n];
            $can_craft = FALSE;
        }
        $items_needed .= $needs . ",";
        $n++;
    }
    if (isset($item_needed)) {
        alert('danger', "Uh Oh!", "You do not have the items required for this recipe. You need {$items_needed}.", true, "smelt.php");
        die($h->endpage());
    }
    unset($n);
    if ($can_craft) {
        if ($r['smelt_time'] > 0) {
            $rcomplete = time() + $r['smelt_time'];
            $db->query("INSERT INTO `smelt_inprogress` (
				`sip_user`, `sip_recipe`, `sip_time`) 
				VALUES ('{$userid}', '{$_GET['id']}', '{$rcomplete}');");
            $friendlytime = timeUntilParse(time() + $r['smelttime']);
            alert('success', "Success!", "You have begun to smelt this item. It will be complete in {$friendlytime}.", true, "smelt.php");
        } else {
            alert('success', "Success!", "You have successfully smelted this item. It is in your inventory.", true, "smelt.php");
            $api->user->giveItem($userid, $r['smelt_output'], $r['smelt_qty_output']);
        }
        $ex = explode(",", $r['smelt_items']);
        $qty = explode(",", $r['smelt_quantity']);
        $n = 0;
        foreach ($ex as $i) {
            $api->user->takeItem($userid, $i, $qty[$n]);
            $n++;
        }
        unset($n);
        unset($ex);
        unset($qty);
        die($h->endpage());
    } else {
        alert('danger', "Uh Oh!", "You cannot craft this item at this time.", true, "smelt.php");
        die($h->endpage());
    }
}

$h->endpage();