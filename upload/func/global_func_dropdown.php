<?php
/**
 * Constructs a drop-down listbox of all the item types in the game to let the user select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the item type which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first item type alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function itemtype_dropdown($ddname = "item_type", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `itmtypeid`, `itmtypename`
    				 FROM `itemtypes`
    				 ORDER BY `itmtypeid` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['itmtypeid']}'";
        if ($selected == $r['itmtypeid'] || $first == 0) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['itmtypename']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the items that are weapons in the game to let the user select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the item which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first item alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function weapon_dropdown($ddname = "weapon", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `itmid`, `itmname`
    				 FROM `items` WHERE `weapon` > 0
    				 ORDER BY `itmid` ASC");
    if ($selected < 1) {
        $ret .= "<option value='0' selected='selected'>-- None --</option>";
    } else {
        $ret .= "<option value='0'>-- None --</option>";
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['itmid']}'";
        if ($selected == $r['itmid']) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['itmname']} [ID: {$r['itmid']}]</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the items that are armor in the game to let the user select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the item which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first item alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function armor_dropdown($ddname = "armor", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `itmid`, `itmname`
    				 FROM `items` WHERE `armor` > 0
    				 ORDER BY `itmid` ASC");
    if ($selected < 1) {
        $ret .= "<option value='0' selected='selected'>-- None --</option>";
    } else {
        $ret .= "<option value='0'>-- None --</option>";
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['itmid']}'";
        if ($selected == $r['itmid']) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['itmname']} [ID: {$r['itmid']}]</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the items in the game to let the user select one, including a "None" option.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the item which should be selected by default.<br />
 * Not specifying this or setting it to a number less than 1 makes "None" selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function item_dropdown($ddname = "item", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `itmid`, `itmname`
    				 FROM `items`
    				 ORDER BY `itmid` ASC");
    if ($selected < 1) {
        $ret .= "<option value='0' selected='selected'>-- None --</option>";
    } else {
        $ret .= "<option value='0'>-- None --</option>";
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['itmid']}'";
        if ($selected == $r['itmid']) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['itmname']} [{$r['itmid']}]</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the academy courses in the game to let the user select one, including a "None" option.
 * @param string $acadname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID number of the academy which should be selected by default.
 * Not specifying this or setting it to a number less than 1 makes "None" selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function academy_dropdown($acadname = "academy", $selected = -1)
{
    global $db;
    $ret = "<select name='$acadname' class='form-control' type='dropdown'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `ac_id`, `ac_name`
    				 FROM `academy`
    				 ORDER BY `ac_id` ASC");
    if ($selected < 1) {
        $ret .= "<option value='0' selected='selected'>-- None --</option>";
    } else {
        $ret .= "<option value='0'>-- None --</option>";
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['ac_id']}'";
        if ($selected == $r['ac_id']) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['ac_name']} [{$r['ac_id']}]</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the locations in the game to let the user select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID number of the location which should be selected by default.
 * Not specifying this or setting it to -1 makes the first item alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function location_dropdown($ddname = "location", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `town_id`, `town_name`, `town_min_level`
    				 FROM `town`
    				 ORDER BY `town_id` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['town_id']}'";
        if ($selected == $r['town_id'] || $first == 0) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['town_name']} (Level {$r['town_min_level']})</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the shops in the game to let the user select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the shop which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first shop alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function shop_dropdown($ddname = "shop", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `shopID`, `shopNAME`
    				 FROM `shops`
    				 ORDER BY `shopID` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['shopID']}'";
        if ($selected == $r['shopID'] || $first == 0) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['shopNAME']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the registered users in the game to let the user select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the user who should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first user alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function user_dropdown($ddname = "user", $selected = -1)
{
    global $db, $ir;
	if ($ir['dropdown'] == 0)
	{
		$ret = "<select name='$ddname' class='form-control' type='dropdown'>";
		$q =
			$db->query(
				"/*qc=on*/SELECT `userid`, `username`
						 FROM `users`
						 ORDER BY `userid` ASC");
		if ($selected == -1) {
			$first = 0;
		} else {
			$first = 1;
		}
		while ($r = $db->fetch_row($q)) {
			$ret .= "\n<option value='{$r['userid']}'";
			if ($selected == $r['userid'] || $first == 0) {
				$ret .= " selected='selected'";
				$first = 1;
			}
			$ret .= ">{$r['username']} [{$r['userid']}]</option>";
		}
		$db->free_result($q);
		$ret .= "\n</select>";
	}
	else
	{
		if ($selected == -1)
			$selected = 0;
		$ret = "<input type='number' value='{$selected}' name='{$ddname}' class='form-control'>";
	}
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the users with user level NPC in the game to let the user select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the user who should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first user alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function user2_dropdown($ddname = "user", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `userid`, `username`
    				 FROM `users`
					 WHERE `user_level` = 'NPC'
    				 ORDER BY `userid` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['userid']}'";
        if ($selected == $r['userid'] || $first == 0) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['username']} [{$r['userid']}]</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the guilds in-game to let the user select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the guild who should be selected by default.
 * Not specifying this or setting it to -1 makes the first guild be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function guilds_dropdown($ddname = "guild", $selected = -1)
{
    global $db;
    $ret = "<select name='{$ddname}' class='form-control' type='dropdown'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `guild_id`, `guild_name`
    				 FROM `guild`
    				 ORDER BY `guild_id` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['guild_id']}'";
        if ($selected == $r['guild_id'] || $first == 0) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['guild_name']} [{$r['guild_id']}]</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the users in the specified guild to let the user select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $guild_id [optional] The ID Number of the guild who should be selected from.
 * @param int $selected [optional] The ID Number of the bot who should be selected by default.
 * Not specifying this or setting it to -1 makes the first bot alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function guild_user_dropdown($ddname = "user", $guild_id, $selected = -1)
{
    global $db;
    $ret = "<select name='{$ddname}' class='form-control' type='dropdown'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `userid`, `username`
    				 FROM `users`
					 WHERE `guild` = {$guild_id}
    				 ORDER BY `userid` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['userid']}'";
        if ($selected == $r['userid'] || $first == 0) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['username']} [{$r['userid']}]</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the challenge bot NPC users in the game to let the user select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the bot who should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first bot alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function npcbot_dropdown($ddname = "bot", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `u`.`userid`, `u`.`username`
                     FROM `botlist` AS `cb`
                     INNER JOIN `users` AS `u`
                     ON `cb`.`botuser` = `u`.`userid`
                     ORDER BY `u`.`userid` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['userid']}'";
        if ($selected == $r['userid'] || $first == 0) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['username']} [{$r['userid']}]</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the users in federal jail in the game to let the user select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the user who should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first user alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function fed_user_dropdown($ddname = "user", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `userid`, `username`
                     FROM `users`
                     WHERE `fedjail` = 1
                     ORDER BY `userid` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['userid']}'";
        if ($selected == $r['userid'] || $first == 0) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['username']} [{$r['userid']}]</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the mail banned users in the game to let the user select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the user who should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first user alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function mailb_user_dropdown($ddname = "user", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
        $db->query("/*qc=on*/SELECT `mbUSER`, `mbID`, `username`
                    FROM `mail_bans` `m`
                    INNER JOIN `users` AS `u`
                    ON `u`.`userid` = `m`.`mbUSER`
                    ORDER BY `mbTIME` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['mbUSER']}'";
        if ($selected == $r['mbUSER'] || $first == 0) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['username']} [{$r['mbUSER']}]</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the forum banned users in the game to let the user select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the user who should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first user alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function forumb_user_dropdown($ddname = "user", $selected = -1)
{
    global $db, $api;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `fb_user`,`fb_id`
                     FROM `forum_bans`
                     ORDER BY `fb_user` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['fb_user']}'";
        if ($selected == $r['fb_user'] || $first == 0) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$api->SystemUserIDtoName($r['fb_user'])} [{$r['fb_user']}]</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the houses in the game to let the user select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the house which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first house alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function estate_dropdown($ddname = "estate", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `house_id`, `house_name`, `house_will`
    				 FROM `estates`
    				 ORDER BY `house_will` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['house_id']}'";
        if ($selected == $r['house_id'] || $first == 0) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['house_name']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the houses in the game to let the user select one.<br />
 * However, the values in the list box return the house's maximum will value instead of its ID.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the house which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first house alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function estate2_dropdown($ddname = "house", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `house_will`, `house_name`
    				 FROM `estates`
    				 ORDER BY `house_will` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['house_will']}'";
        if ($selected == $r['house_will'] || $first == 0) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['house_name']} (Will: {$r['house_will']})</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the crimes in the game to let the user select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the crime which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first crime alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function crime_dropdown($ddname = "crime", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `crimeID`, `crimeNAME`
    				 FROM `crimes`
    				 ORDER BY `crimeNAME` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['crimeID']}'";
        if ($selected == $r['crimeID'] || $first == 0) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['crimeNAME']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the crime groups in the game to let the user select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the crime group which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first crime group alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function crimegroup_dropdown($ddname = "crimegroup", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `cgID`, `cgNAME`
    				 FROM `crimegroups`
    				 ORDER BY `cgNAME` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['cgID']}'";
        if ($selected == $r['cgID'] || $first == 0) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['cgNAME']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}
/**
 * Constructs a drop-down listbox of all the forums in the game to let the user select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the forum w hich should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first forum alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function forum_dropdown($ddname = "forum", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `ff_id`, `ff_name`
    				 FROM `forum_forums`
    				 ORDER BY `ff_name` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['ff_id']}'";
        if ($selected == $r['ff_id'] || $first == 0) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['ff_name']} [{$r['ff_id']}]</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}
/**
 * Constructs a drop-down listbox of all the smelt recipes in the game to let the user select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the forum which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first forum alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function smelt_dropdown($ddname = 'smelt', $selected = -1)
{
    global $db, $api;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `smelt_id`, `smelt_output`, `smelt_qty_output`
                     FROM `smelt_recipes`
                     ORDER BY `smelt_id` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $itemname = $api->SystemItemIDtoName($r['smelt_output']);
        $ret .= "\n<option value='{$r['smelt_id']}'";
        if ($selected == $r['smelt_id'] || $first == 0) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['smelt_qty_output']} x {$itemname}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}
/**
 * Constructs a drop-down listbox of all the items in the user's inventory to let the user select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the forum which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first forum alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function inventory_dropdown($ddname = "item", $selected = -1)
{
    global $db, $userid;
    $ret = "<select name='$ddname' type='dropdown' class='form-control'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `i`.*, `it`.*
    				 FROM `inventory` AS `i`
    				 INNER JOIN `items` AS `it`
    				 ON `i`.`inv_itemid` = `it`.`itmid`
    				 WHERE `inv_userid` = {$userid}
    				 ORDER BY `itmname` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['itmid']}'";
        if ($selected == $r['itmid'] || $first == 0) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['itmname']} (You Have " . number_format($r['inv_qty']) . ")</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the jobs in the game to let the user select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the job which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first job alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function job_dropdown($ddname = "job", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `jRANK`, `jNAME`
    				 FROM `jobs`
    				 ORDER BY `jRANK` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['jRANK']}'";
        if ($selected == $r['jRANK'] || $first == 0) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['jNAME']} [ID: {$r['jRANK']}]</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the job ranks in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the job rank which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first job's first job rank alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function jobrank_dropdown($ddname = "jobrank", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `jrID`, `jNAME`, `jrRANK`
                     FROM `job_ranks` AS `jr`
                     INNER JOIN `jobs` AS `j`
                     ON `jr`.`jrJOB` = `j`.`jRANK`
                     ORDER BY `jr`.`jrRANK` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['jrID']}'";
        if ($selected == $r['jrID'] || $first == 0) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['jrRANK']} [{$r['jNAME']}]</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}
/**
 * Constructs a drop-down listbox of all the items in the user's guild's to let the user select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the forum which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first forum alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function armory_dropdown($ddname = "item", $selected = -1)
{
    global $db, $ir;
    $ret = "<select name='$ddname' type='dropdown' class='form-control'>";
    $q =
        $db->query(
            "/*qc=on*/SELECT `i`.*, `it`.*
    				 FROM `guild_armory` AS `i`
    				 INNER JOIN `items` AS `it`
    				 ON `i`.`gaITEM` = `it`.`itmid`
    				 WHERE `gaGUILD` = {$ir['guild']}
    				 ORDER BY `itmname` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['itmid']}'";
        if ($selected == $r['itmid'] || $first == 0) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['itmname']} (Armory: {$r['gaQTY']})</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the potion items in the game, and allows the user to select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the forum which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first forum alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function potion_dropdown($ddname = "potion", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
    $db->query(
        "/*qc=on*/SELECT `itmid`, `itmname`
    				 FROM `items` WHERE `itmtype` = 8
                     OR `itmtype` = 7
    				 ORDER BY `itmid` ASC");
    if ($selected < 1) {
        $ret .= "<option value='0' selected='selected'>-- None --</option>";
    } else {
        $ret .= "<option value='0'>-- None --</option>";
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['itmid']}'";
        if ($selected == $r['itmid']) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['itmname']} [ID: {$r['itmid']}]</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the badges in the game, and allows the user to select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the forum which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first forum alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function badge_dropdown($ddname = "badge", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
    $db->query(
        "/*qc=on*/SELECT `itmid`, `itmname`
    				 FROM `items` WHERE `itmtype` = 13
    				 ORDER BY `itmid` ASC");
    if ($selected < 1) {
        $ret .= "<option value='0' selected='selected'>-- None --</option>";
    } else {
        $ret .= "<option value='0'>-- None --</option>";
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['itmid']}'";
        if ($selected == $r['itmid']) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['itmname']} [ID: {$r['itmid']}]</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the ring items in the game to let the user select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the forum which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first forum alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function ring_dropdown($ddname = "ring", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
    $db->query(
        "/*qc=on*/SELECT `itmid`, `itmname`
    				 FROM `items` WHERE `itmtype` = 15
    				 ORDER BY `itmid` ASC");
    if ($selected < 1) {
        $ret .= "<option value='0' selected='selected'>-- None --</option>";
    } else {
        $ret .= "<option value='0'>-- None --</option>";
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['itmid']}'";
        if ($selected == $r['itmid']) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['itmname']} [ID: {$r['itmid']}]</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the necklace items in the game to let the user select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the forum which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first forum alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function necklace_dropdown($ddname = "necklace", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
    $db->query(
        "/*qc=on*/SELECT `itmid`, `itmname`
    				 FROM `items` WHERE `itmtype` = 16
    				 ORDER BY `itmid` ASC");
    if ($selected < 1) {
        $ret .= "<option value='0' selected='selected'>-- None --</option>";
    } else {
        $ret .= "<option value='0'>-- None --</option>";
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['itmid']}'";
        if ($selected == $r['itmid']) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['itmname']} [ID: {$r['itmid']}]</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the pendant items in the game to let the user select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the forum which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first forum alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function pendant_dropdown($ddname = "pendant", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
    $db->query(
        "/*qc=on*/SELECT `itmid`, `itmname`
    				 FROM `items` WHERE `itmtype` = 18
    				 ORDER BY `itmid` ASC");
    if ($selected < 1) {
        $ret .= "<option value='0' selected='selected'>-- None --</option>";
    } else {
        $ret .= "<option value='0'>-- None --</option>";
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['itmid']}'";
        if ($selected == $r['itmid']) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['itmname']} [ID: {$r['itmid']}]</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

function mines_dropdown($ddname = "mine", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
    $db->query(
        "/*qc=on*/SELECT `mine_id`, `mine_location`, `mine_level`
                     FROM `mining_data`
                     ORDER BY `mine_level` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $CityName = $db->fetch_single($db->query("/*qc=on*/SELECT `town_name` FROM `town` WHERE `town_id` = {$r['mine_location']}"));
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

/**
 * Constructs a drop-down listbox of all the NPC bosses in the game to let the user select one.
 * @param string $ddname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the bot who should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first bot alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function npcboss_dropdown($ddname = "bot", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
    $db->query(
        "/*qc=on*/SELECT `u`.`userid`, `u`.`username`
                     FROM `activeBosses` AS `ab`
                     INNER JOIN `users` AS `u`
                     ON `ab`.`boss_user` = `u`.`userid`
                     ORDER BY `u`.`userid` ASC");
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['userid']}'";
        if ($selected == $r['userid'] || $first == 0) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['username']} [{$r['userid']}]</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

function timezone_dropdown($ddname = "timezone", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $timezones = DateTimeZone::listIdentifiers();
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    foreach ($timezones as $tz)
    {
        $ret .= "\n<option value='{$tz}'";
        if (($selected == $tz) || ($first == 0))
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$tz}</option>";
    }
    $ret .= "\n</select>";
    return $ret;
}

function skills_dropdown($ddname = "skill", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
    $db->query("/*qc=on*/SELECT * FROM `user_skills_define` ORDER BY `skID` ASC");
    if ($selected < 1) {
        $ret .= "<option value='0' selected='selected'>-- None --</option>";
    } else {
        $ret .= "<option value='0'>-- None --</option>";
    }
    if ($selected == -1) {
        $first = 0;
    } else {
        $first = 1;
    }
    while ($r = $db->fetch_row($q)) {
        $ret .= "\n<option value='{$r['skID']}'";
        if ($selected == $r['skID'] || $first == 0) {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['skName']} | Cost: {$r['skCost']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}