<?php
/*
	File:		smelt.php
	Created: 	4/5/2016 at 12:26AM Eastern Time
	Info: 		Allows players to view their possible crafting recipes,
				requirements for those recipes, and create those items.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
	alert('danger',"Uh Oh!","You cannot use the smeltery while in the infirmary or dungeon.",true,'index.php');
	die($h->endpage());
}
echo "<h3><i class='game-icon game-icon-anvil'></i> Blacksmith's Smeltery</h3><hr />";
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
    $q = $db->query("/*qc=on*/SELECT * FROM `smelt_recipes` ORDER BY `smelt_output` ASC");
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
        $smltTime = ($r['smelt_time'] == 0) ? "" : TimeUntil_Parse(time() + $r['smelt_time']);
        $n = 0;
		$r['hasitem']=0;
		foreach ($ex as $i) 
		{
			$do_they_have = $db->query("/*qc=on*/SELECT `inv_itemid` FROM `inventory` WHERE `inv_userid`={$userid} AND `inv_itemid`={$i}");
            if ($db->num_rows($do_they_have) > 0) 
            {
				$r['hasitem']=$r['hasitem']+1;
			}
		}
		if ($r['hasitem'] > 0)
		{
			echo "
			<tr>
				<td>
					<a href='iteminfo.php?ID={$r['smelt_output']}'>{$output_item}</a> x " . number_format($r['smelt_qty_output']);
                    if ($r['smelt_time'] > 0)
                    {
                        echo "<br /><small>Smelt Time: {$smltTime}</small>";
                    }
                    echo"
				</td>
				<td>";
			$n = 0;
			foreach ($ex as $i) {
				$get_items_needed = $db->query("/*qc=on*/SELECT `itmname` FROM `items` WHERE `itmid`={$i}");
				$t = $db->fetch_row($get_items_needed);

				$do_they_have = $db->query("/*qc=on*/SELECT `inv_itemid` FROM `inventory` WHERE `inv_userid`={$userid} AND `inv_itemid`={$i} AND `inv_qty`>={$qty[$n]}");
				if ($db->num_rows($do_they_have) == 0) {
					$t['itmname'] = "<span class='text-danger'>" . $t['itmname'] . "";
					$can_craft = FALSE;
				}
				$items_needed .= "<a href='iteminfo.php?ID={$i}'>" .$t['itmname'] . "</a> x " . number_format($qty[$n]) . " (Have " . number_format($api->UserCountItem($userid, $i)) . ")</span><br />";
				$n++;
			}
			unset($n);
			echo "{$items_needed}
				</td>
				<td>";
			if ($can_craft == TRUE) 
			{
				echo "<a href='?action=smelt&id={$r['smelt_id']}'>Smelt Item</a>";
			} 
			else 
			{
				echo "<span class='text-danger'>Cannot Smelt</span>";
			}
			echo "
				</td>
			</tr>";
		}
    }
    echo "</table>";
}

function smelt()
{
    global $db, $userid, $api, $h;
    $_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : 0;
    $q = $db->query("/*qc=on*/SELECT * FROM `smelt_recipes` WHERE `smelt_id` = {$_GET['id']}");
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
        $get_items_needed = $db->query("/*qc=on*/SELECT `itmname` FROM `items` WHERE `itmid`={$i}");
        $t = $db->fetch_row($get_items_needed);
        $do_they_have = $db->query("/*qc=on*/SELECT `inv_itemid` FROM `inventory` WHERE `inv_userid`={$userid} AND `inv_itemid`={$i} AND `inv_qty`>={$qty[$n]}");
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
            $friendlytime = TimeUntil_Parse(time() + $r['smelttime']);
            alert('success', "Success!", "You have begun to smelt {$api->SystemItemIDtoName($r['smelt_output'])}. It will be complete in {$friendlytime}.", true, "smelt.php");
        } else {
            alert('success', "Success!", "You have successfully smelted {$api->SystemItemIDtoName($r['smelt_output'])}. It is in your inventory.", true, "smelt.php");
            $api->UserGiveItem($userid, $r['smelt_output'], $r['smelt_qty_output']);
        }
        $ex = explode(",", $r['smelt_items']);
        $qty = explode(",", $r['smelt_quantity']);
        $n = 0;
        foreach ($ex as $i) {
            $api->UserTakeItem($userid, $i, $qty[$n]);
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