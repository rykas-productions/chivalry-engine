<?php
function returnIcon($item,$size=1)
{
	global $db, $ir;
	if ($ir['icons'] == 1)
	{
		$q = $db->fetch_row($db->query("/*qc=on*/SELECT `icon`,`color` FROM `items` WHERE `itmid` = {$item}"));
		$imageIcons=array(262,337,322);
		if (empty($q['icon']))
		{
			return "<i class='fas fa-question' style='font-size:{$size}rem;'></i>";
		}
		elseif ($q['color'] == 'img')
		{
			return "<img src='{$q['icon']}' style='width:{$size}rem;'>";
		}
		else
		{
			if (!empty($q['color']))
			{
				return "<i class='{$q['icon']}' style='font-size:{$size}rem; color: {$q['color']};'></i>";
			}
			else
			{
				return "<i class='{$q['icon']}' style='font-size:{$size}rem;'></i>";
			}
			
		}
	}
	else
	{
		return;
	}
}

function parseDungeonItemName($dungItem)
{
	global $api;
	if ($dungItem == 1)
		return $api->SystemItemIDtoName(29);
	elseif ($dungItem == 2)
		return $api->SystemItemIDtoName(30);
	elseif ($dungItem == 3)
		return $api->SystemItemIDtoName(31);
	elseif ($dungItem == 4)
		return $api->SystemItemIDtoName(206);
	elseif ($dungItem == 5)
		return "Bust Self";
	elseif ($dungItem == 6)
		return "Bail Self";
	else
		return "N/A";
}

function parseInfirmaryItemName($infirmItem)
{
	global $api;
	if ($infirmItem == 1)
		return $api->SystemItemIDtoName(5);
	elseif ($infirmItem == 2)
		return $api->SystemItemIDtoName(6);
	elseif ($infirmItem == 3)
		return $api->SystemItemIDtoName(100);
	elseif ($infirmItem == 4)
		return $api->SystemItemIDtoName(98);
	elseif ($infirmItem == 5)
		return $api->SystemItemIDtoName(207);
	elseif ($infirmItem == 6)
		return $api->SystemItemIDtoName(206);
	elseif ($infirmItem == 7)
		return $api->SystemItemIDtoName(216);
	elseif ($infirmItem == 8)
		return "Infirmary Heal";
	else
		return "N/A";
}

/**
 * Give a particular user a particular quantity of some item.
 * @param int $user The user ID who is to be given the item
 * @param int $itemid The item ID which is to be given
 * @param int $qty The item quantity to be given
 * @param int $notid [optional] If specified and greater than zero, prevents the item given database entry combining with inventory id $notid.
 */
function item_add($user, $itemid, $qty, $notid = 0)
{
    global $db;
    //Select $itemid's item name.
    $ie = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`itmname`) FROM `items` WHERE `itmid` = {$itemid}"));
    //If the name returns, continue
    if ($ie > 0) {
        //We want $itemid to go into its own stack. Select the inventory ID to make sure this doesn't happen.
        if ($notid > 0) {
            $q = $db->query("/*qc=on*/SELECT `inv_id` FROM `inventory` WHERE `inv_userid` = {$user} AND `inv_itemid` = {$itemid}
							 AND `inv_id` != {$notid} LIMIT 1");
        } //We don't care if the $itemid merges into an existing inventory stack. Let's select the first stack then.
        else {
            $q = $db->query("/*qc=on*/SELECT `inv_id` FROM `inventory` WHERE `inv_userid` = {$user} AND `inv_itemid` = {$itemid}
							 LIMIT 1");
        }
        //If the inventory stack exists, add $qty to it and return true to signify we succeeded at adding the item.
        if ($db->num_rows($q) > 0) {
            $r = $db->fetch_row($q);
            $db->query("UPDATE `inventory` SET `inv_qty` = `inv_qty` + {$qty} WHERE `inv_id` = {$r['inv_id']}");
            return true;
        }
        //The inventory does not exist and/or we don't want $itemid to merge into an inventory stack, so lets create
        //a new one and return true.
        else {
            $db->query("INSERT INTO `inventory` (`inv_itemid`, `inv_userid`, `inv_qty`) VALUES ({$itemid}, {$user}, {$qty})");
            return true;
        }
    }
}

/**
 * Take away from a particular user a particular quantity of some item.<br />
 * If they don't have enough of that item to be taken, takes away any that they do have.
 * @param int $user The user ID who is to lose the item
 * @param int $itemid The item ID which is to be taken
 * @param int $qty The item quantity to be taken
 */
function item_remove($user, $itemid, $qty)
{
    global $db;
    //Select $itemid's item name.
    $ie = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`itmname`) FROM `items` WHERE `itmid` = {$itemid}"));
    //If $itemid actually exists, it'll return a name, so lets continue if that's the case.
    if ($ie > 0) {
        //Select the inventory ID number where $itemid's is stored for $user.
        $q = $db->query("/*qc=on*/SELECT `inv_id`, `inv_qty` FROM `inventory` WHERE `inv_userid` = {$user}
						 AND `inv_itemid` = {$itemid} LIMIT 1");
        //User has an inventory id for $itemid!
        if ($db->num_rows($q) > 0) {
            $r = $db->fetch_row($q);
            //$user's $itemid quantity is greater than $qty, so remove only $qty and return true.
            if ($r['inv_qty'] > $qty) {
                $db->query("UPDATE `inventory` SET `inv_qty` = `inv_qty` - {$qty} WHERE `inv_id` = {$r['inv_id']}");
                return true;
            } //$user's $itemid quantity is lower than $qty, so delete the inventory ID entirely and return true.
            else {
                $db->query("DELETE FROM `inventory` WHERE `inv_id` = {$r['inv_id']}");
                return true;
            }
        }
    }
    $db->free_result($q);
}

function submitToModeration($itmid, $type, $value, $user)
{
	global $db;
	$db->query("INSERT INTO 
				`staff_moderation_board` 
				(`mod_id`, `mod_type`, 
				`mod_change`, `mod_item`, 
				`mod_user`) 
				VALUES 
				(NULL, '{$type}', '{$value}', 
				'{$itmid}', '{$user}')");
}function forceDeleteItem($itmid){	global $db;
	$db->query("DELETE FROM `inventory` WHERE `inv_itemid` = {$itmid}");
	$db->query("UPDATE `crimes` SET `crimeITEMSUC` = 0 WHERE `crimeITEMSUC` = {$itmid}");
	$db->query("DELETE FROM `farm_produce` WHERE `seed_item` = {$itmid}");
	$db->query("DELETE FROM `farm_produce` WHERE `seed_output` = {$itmid}");
	$db->query("UPDATE `farm_data` 
				SET `farm_seed` = 0,
				`farm_stage` = 0,
				`farm_time` = 0
				WHERE `farm_seed` = {$itmid}");
	$db->query("DELETE FROM `guild_armory` WHERE `gaITEM` = {$itmid}");
	$db->query("DELETE FROM `itemauction` WHERE `ia_item` = {$itmid}");
	$db->query("DELETE FROM `itemmarket` WHERE `imITEM` = {$itmid}");
	$db->query("DELETE FROM `itemrequest` WHERE `irITEM` = {$itmid}");
	$db->query("DELETE FROM `shopitems` WHERE `sitemITEMID` = {$itmid}");
	$db->query("DELETE FROM `smelt_recipes` WHERE `smelt_output` = {$itmid}");
	$db->query("DELETE FROM `staff_moderation_board` WHERE `mod_item` = {$itmid}");
	$q=$db->query("SELECT `userid` FROM `users` WHERE `equip_primary` = {$itmid}");
	while ($r=$db->fetch_row($q))
	{
		unequipUserSlot($r['userid'], "equip_primary");
	}
	$db->free_result($q);
	$q=$db->query("SELECT `userid` FROM `users` WHERE `equip_secondary` = {$itmid}");
	while ($r=$db->fetch_row($q))
	{
		unequipUserSlot($r['userid'], "equip_secondary");
	}
	$db->free_result($q);
	$q=$db->query("SELECT `userid` FROM `users` WHERE `equip_armor` = {$itmid}");
	while ($r=$db->fetch_row($q))
	{
		unequipUserSlot($r['userid'], "equip_armor");
	}
	$db->free_result($q);
	$q=$db->query("SELECT `userid` FROM `users` WHERE `equip_potion` = {$itmid}");
	while ($r=$db->fetch_row($q))
	{
		unequipUserSlot($r['userid'], "equip_potion");
	}
	$db->free_result($q);
	$q=$db->query("SELECT `userid` FROM `users` WHERE `equip_badge` = {$itmid}");
	while ($r=$db->fetch_row($q))
	{
		unequipUserSlot($r['userid'], "equip_badge");
	}
	$db->free_result($q);
	
	//Save for last, just in case?
	$db->query("DELETE FROM `items` WHERE `itmid` = {$itmid}");}