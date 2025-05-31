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
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
$activeSmelt = $db->fetch_single($db->query("SELECT COUNT(`sip_id`) FROM `smelt_inprogress` WHERE `sip_user` = {$userid}"));
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
    global $db, $userid, $api, $ir, $activeSmelt;
    $StatArray = array('blacksmith', 'brewing', 'processing', 'cooking',
                        'runecrafting','gemcrafting', 'other'
    );
    //Stat is not chosen, set to level.
    if (!isset($_GET['type'])) {
        $_GET['type'] = 'blacksmith';
    }
    //Stat chosen is not a valid stat.
    if (!in_array($_GET['type'], $StatArray)) {
        $_GET['type'] = 'blacksmith';
    }
    //Sanitize and escape the GET.
    $_GET['type'] = $db->escape(strip_tags(stripslashes($_GET['type'])));
    $q = $db->query("/*qc=on*/SELECT * FROM `smelt_recipes` WHERE `smelt_required_mastery` <= {$ir['reset']} AND `smelt_level_required` <= {$ir['level']} AND `smelt_type` = '{$_GET['type']}' ORDER BY `smelt_output` ASC");
    //$q = $db->query("/*qc=on*/SELECT * FROM `smelt_recipes` WHERE `smelt_type` = '{$_GET['type']}' ORDER BY `smelt_output` ASC");
    echo "
    <div class='card'>
        <div class='card-body'>
            <div class='row'>
                <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4 col-xxl-3 col-xxxl-auto'>    
                    <a href='#' class='btn btn-primary btn-block' data-toggle='modal' data-target='#smithing_info'>In Progress - {$activeSmelt} item(s)</a>
                </div>
                <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4 col-xxl-3 col-xxxl-auto'>    
                    <a href='?type=blacksmith' class='btn btn-primary btn-block'>Blacksmith Recipes</a>
                </div>
                <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4 col-xxl-3 col-xxxl-auto'>    
                    <a href='?type=cooking' class='btn btn-primary btn-block'>Cooking Recipes</a>
                </div>
                <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4 col-xxl-3 col-xxxl-auto'>    
                    <a href='?type=brewing' class='btn btn-primary btn-block'>Brewing Recipes</a>
                </div>
                <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4 col-xxl-3 col-xxxl-auto'>    
                    <a href='?type=processing' class='btn btn-primary btn-block'>Processing Recipes</a>
                </div>
                <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4 col-xxl-3 col-xxxl-auto'>    
                    <a href='?type=runecrafting' class='btn btn-primary btn-block'>Runecrafting Recipes</a>
                </div>
                <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4 col-xxl-3 col-xxxl-auto'>    
                    <a href='?type=gemcrafting' class='btn btn-primary btn-block'>Gemcrafting Recipes</a>
                </div>
                <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4 col-xxl-3 col-xxxl-auto'>    
                    <a href='?type=other' class='btn btn-primary btn-block'>Other Recipes</a>
                </div>
            </div>
        </div>
    </div>
    <br />
    <div class='row'>
        <div class='col-12'>
            <div class='card'>
                <div class='card-header'>
                    " . ucfirst($_GET['type']) . " Recipes
                </div>
                <div class='card-body'>";
    while ($r = $db->fetch_row($q)) 
    {
        $output_item = $api->SystemItemIDtoName($r['smelt_output']);
        $items_needed = '';
        $can_craft = TRUE;
        $ex = explode(",", $r['smelt_items']);
        $qty = explode(",", $r['smelt_quantity']);
        $smltTime = ($r['smelt_time'] == 0) ? "" : TimeUntil_Parse(time() + $r['smelt_time']);
        $n = 0;
		$r['hasitem']=0;
		$rankClass = ($ir['reset'] >= $r['smelt_required_mastery']) ? "text-success" : "text-danger";
		foreach ($ex as $i) 
		{
			$do_they_have = $db->query("/*qc=on*/SELECT `inv_itemid` FROM `inventory` WHERE `inv_userid`={$userid} AND `inv_itemid`={$i}");
            if ($db->num_rows($do_they_have) > 0) 
            {
				$r['hasitem']=$r['hasitem']+1;
			}
		}
	    $rcon = returnIcon($r['smelt_output'], 4);
	    echo "
        <div class='row'>
            <div class='col-12 col-lg-4'>
                <div class='row'>
                    <div class='col-12 col-sm-4 col-lg-12 col-xxl-4'>
                        {$rcon}
                    </div>
                    <div class='col-12 col-sm-8 col-lg-12 col-xxl-8'>
                        " . shortNumberParse($r['smelt_qty_output']) . " x <a href='iteminfo.php?ID={$r['smelt_output']}'>{$output_item}</a>
                    </div>
                </div>
            </div>
            <div class='col-12 col-lg-5'>
                <div class='row'>
                    <div class='col-12'>
                        <b>Requirements</b>
                    </div>";
		    $n = 0;
		    foreach ($ex as $i) {
		        $get_items_needed = $db->query("/*qc=on*/SELECT `itmname` FROM `items` WHERE `itmid`={$i}");
		        $t = $db->fetch_row($get_items_needed);
		        
		        $do_they_have = $db->query("/*qc=on*/SELECT `inv_itemid` FROM `inventory` WHERE `inv_userid`={$userid} AND `inv_itemid`={$i} AND `inv_qty`>={$qty[$n]}");
		        if ($db->num_rows($do_they_have) == 0) 
		        {
		            $t['itmname'] = "<span class='text-danger'>" . $t['itmname'] . "</span>";
		            $can_craft = FALSE;
		        }
		        $items_needed .= "<div class='col-12 col-sm-6 col-lg-12 col-xxl-6'>" . shortNumberParse($qty[$n]) . " x <a href='iteminfo.php?ID={$i}'>" .$t['itmname'] . "</a></span></div>";
		        $n++;
		    }
		    unset($n);
		    echo "{$items_needed}";
		    if ($r['smelt_required_mastery'] > 0)
		    {
		        $can_craft = ($ir['reset'] >= $r['smelt_required_mastery']) ? TRUE : FALSE;
		        echo"<div class='col-12 col-sm-6 col-lg-12 col-xxl-6 {$rankClass}'>
                            <b>Mastery Rank:</b> " . shortNumberParse($r['smelt_required_mastery']) . "
                            </div>";
		    }
		    if ($r['smelt_level_required'] > 0)
		    {
		        $can_craft = ($ir['level'] >= $r['smelt_level_required']) ? TRUE : FALSE;
		        echo"<div class='col-12 col-sm-6 col-lg-12 col-xxl-6 {$rankClass}'>
                            <b>Level:</b> " . shortNumberParse($r['smelt_level_required']) . "
                            </div>";
		    }
		    if ($r['smelt_time'] > 0)
		    {
		        echo"<div class='col-12 col-sm-6 col-lg-12 col-xxl-6'>
                            <b>Time:</b> {$smltTime}
                            </div>";
		    }
		    echo"
                </div>
            </div>
            <div class='col-12 col-lg-3'>";
    		    if ($can_craft == TRUE)
    		    {
    		        echo "<a href='?action=smelt&id={$r['smelt_id']}' class='btn btn-block btn-primary'>Craft Item</a>";
    		    }
    		    else
    		    {
    		        echo "<a href='#' class='disabled btn btn-block btn-danger'>Cannot Craft</a>";
    		    }
    		    echo"
                <hr />
            </div>
        </div>";
    }
}

function smelt()
{
    global $db, $userid, $api, $h, $ir, $activeSmelt;
    $_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : 0;
    $maxActive = ($ir['vip_days'] == 0) ? 8 : 16;
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
    if ($r['smelt_required_mastery'] > 0)
    {
        if ($ir['reset'] >= $r['smelt_required_mastery'])
        {
            alert('danger',"Uh Oh!", "You are not the correct Mastery Rank to craft this. You are currently {$ir['reset']}, and you need to be Mastery Rank {$r['smelt_required_mastery']}.", true, "smelt.php");
            $can_craft = FALSE;
        }
    }
    if ($r['smelt_level_required'] > 0)
    {
        if ($ir['level'] >= $r['smelt_level_required'])
        {
            alert('danger',"Uh Oh!", "You are not the correct Level to craft this. You are currently Level " . shortNumberParse($ir['level']) . ", and you need to be Level " . shortNumberParse($r['smelt_level_required']) . ".", true, "smelt.php");
            $can_craft = FALSE;
        }
    }
    if ($activeSmelt >= $maxActive)
    {
        alert('danger',"Uh Oh!","You can only have a maximum of {$maxActive} crafts in progress at a time. You can increase this to 16 with VIP Days!", true, 'smelt.php');
        $can_craft = FALSE;
    } 
    unset($n);
    if ($can_craft) {
        if ($r['smelt_time'] > 0) 
         {
            $rcomplete = time() + $r['smelt_time'];
            $db->query("INSERT INTO `smelt_inprogress` (
				`sip_user`, `sip_recipe`, `sip_time`) 
				VALUES ('{$userid}', '{$_GET['id']}', '{$rcomplete}');");
            $friendlytime = TimeUntil_Parse($rcomplete);
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

include('forms/smithing_progress_popup.php');
$h->endpage();