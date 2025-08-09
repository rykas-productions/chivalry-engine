<?php
/*
	File:		global_func.php
	Created: 	4/5/2016 at 12:04AM Eastern Time
	Info: 		Functions used all over the game.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
/**
	Parses the time since the timestamp given.
	@param int $time_stamp for time since.
	@param boolean $ago to display the "ago" after the string. (Default = true)
*/
function DateTime_Parse($time_stamp, $ago = true, $override = false)
{
    //Check if $time_stamp is 0, if true, return N/A
    if ($time_stamp == 0) {
        return "N/A";
    }
    //Time difference is $time_stamp subtracted from current unix time.
    $time_difference = (time() - $time_stamp);
    //If the time difference is less than 1 day, OR if $override is set to true. This will display how long ago the
    //timestamp was in seconds/minutes/hours/days/etc.
    if ($time_difference < 86400 || $override == true) {
        $unit = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year');
        $lengths = array(60, 60, 24, 7, 4.35, 12);
        //Go to the largest unit of time as possible.
        for ($i = 0; $time_difference >= $lengths[$i]; $i++) {
            $time_difference = $time_difference / $lengths[$i];
        }
        //For added precision, lets go over 2 decimal places.
        $time_difference = round($time_difference, 2);
        //If $ago is true, lets add "ago" after our string.
        if ($ago == true) {
            $date = $time_difference . ' ' . $unit[$i] . (($time_difference > 1 OR $time_difference < 1) ? 's' : '') . ' ago';
        } else {
            $date = $time_difference . ' ' . $unit[$i] . (($time_difference > 1 OR $time_difference < 1) ? 's' : '') . '';
        }
    } //If we just want the timestamp in a date format.
    else {
        $date = date('F j, Y, g:i:s a', $time_stamp);
    }
    //Return whatever is output.
    return $date;
}

/**
	Parses how much time until the timestamp given.
	$param int $time_stamp for the timestamp.
*/
function TimeUntil_Parse($time_stamp)
{
    //Time difference is Unix Timestamp subtracted from $time_stamp.
    $time_difference = $time_stamp - time();
    $unit = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year');
    $lengths = array(60, 60, 24, 7, 4.35, 12);
    //Get to the biggest unit type as possible.
    for ($i = 0; $time_difference >= $lengths[$i]; $i++) {
        $time_difference = $time_difference / $lengths[$i];
    }
    //For added precision, lets round to the 2nd decimal place.
    $time_difference = round($time_difference, 2);
    //Add an 's' if needed.
    $date = $time_difference . ' ' . $unit[$i] . (($time_difference > 1 OR $time_difference < 1) ? 's' : '') . '';
    //Return $date
    return $date;
}

/**
	Parses the timestamp into a human friendly number.
*/
function ParseTimestamp($time)
{
    $unit = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year');
    $lengths = array(60, 60, 24, 7, 4.35, 12);
    //Cycle through unit types until we get to the biggest and cannot go any bigger.
    for ($i = 0; $time >= $lengths[$i]; $i++) {
        $time = $time / $lengths[$i];
    }
    //Round to the second decimal place
    $time = round($time, 2);
    //Add an 's' if needed.
    $date = $time . ' ' . $unit[$i] . (($time > 1 OR $time < 1) ? 's' : '') . '';
    //Return date.
    return $date;
}

/**
	The function for testing if a player is in the hospital.
	@param int $user The user who to test for.
*/

function user_infirmary($user) {
    global $db;
    
    // Basic validation
    $user = (int)$user;
    if ($user <= 0) {
        error_log("Invalid user ID in user_infirmary check: " . $user);
        return false;
    }

    $CurrentTime = time();
    
    try {
        $query = $db->query("SELECT `infirmary_user` FROM `infirmary` 
                            WHERE `infirmary_user` = {$user} 
                            AND `infirmary_out` > {$CurrentTime}");
        return ($db->num_rows($query) > 0);
    } catch (Exception $e) {
        error_log("Database error in user_infirmary: " . $e->getMessage());
        return false;
    }
}

/**
	The function for testing if a player is in the dungeon.
	@param int $user The user who to test for.
*/
function user_dungeon($user) {
    global $db;
    
    // Basic validation
    $user = (int)$user;
    if ($user <= 0) {
        error_log("Invalid user ID in user_dungeon check: " . $user);
        return false;
    }

    $CurrentTime = time();
    
    try {
        $query = $db->query("SELECT `dungeon_user` FROM `dungeon` 
                            WHERE `dungeon_user` = {$user} 
                            AND `dungeon_out` > {$CurrentTime}");
        return ($db->num_rows($query) > 0);
    } catch (Exception $e) {
        error_log("Database error in user_dungeon: " . $e->getMessage());
        return false;
    }
}

/**
	The function for putting/adding onto someones infirmary time.
	@param int $user The user to put in the infirmary
	@param int $time The time (in minutes) to add.
	@param text $reason The reason the user is in the infirmary.
*/
function put_infirmary($user, $time, $reason) {
    global $db;
    
    // Input validation
    $user = (int)$user;
    $time = (int)$time;
    if ($user <= 0 || $time <= 0) {
        error_log("Invalid parameters in put_infirmary: user=$user, time=$time");
        return false;
    }

    // Sanitize reason
    $reason = $db->escape(substr(strip_tags($reason), 0, 255));
    if (empty($reason)) {
        $reason = "Unknown reason";
    }
    
    try {
        $CurrentTime = time();
        $TimeMath = $time * 60;

        // Check if user is currently in infirmary
        $Infirmary = $db->fetch_single($db->query("SELECT `infirmary_out` 
                                                  FROM `infirmary` 
                                                  WHERE `infirmary_user` = {$user}"));

        if ($Infirmary <= $CurrentTime) {
            // New stay
            $db->query("UPDATE `infirmary` 
                       SET `infirmary_out` = {$CurrentTime} + {$TimeMath},
                           `infirmary_in` = {$CurrentTime},
                           `infirmary_reason` = '{$reason}'
                       WHERE `infirmary_user` = {$user}");
        } else {
            // Extend current stay
            $db->query("UPDATE `infirmary` 
                       SET `infirmary_out` = `infirmary_out` + {$TimeMath},
                           `infirmary_reason` = '{$reason}'
                       WHERE `infirmary_user` = {$user}");
        }
        return true;
    } catch (Exception $e) {
        error_log("Database error in put_infirmary: " . $e->getMessage());
        return false;
    }
}

/**
	The function for removing someones infirmary time.
	@param int $user The user to put in the infirmary
	@param int $time The time (in minutes) to remove.
*/
function remove_infirmary($user, $time) {
    global $db;
    
    // Input validation
    $user = (int)$user;
    $time = (int)$time;
    if ($user <= 0 || $time <= 0) {
        error_log("Invalid parameters in remove_infirmary: user=$user, time=$time");
        return false;
    }
    
    try {
        $TimeMath = $time * 60;
        
        $db->query("UPDATE `infirmary` 
                   SET `infirmary_out` = CASE 
                       WHEN (`infirmary_out` - {$TimeMath}) < `infirmary_in` 
                       THEN `infirmary_in`
                       ELSE `infirmary_out` - {$TimeMath}
                   END
                   WHERE `infirmary_user` = {$user}");
        
        return true;
    } catch (Exception $e) {
        error_log("Database error in remove_infirmary: " . $e->getMessage());
        return false;
    }
}

/**
	The function for putting/adding onto someones dungeon time.
	@param int $user The user to put in the dungeon
	@param int $time The time (in minutes) to add.
	@param text $reason The reason the user is in the dungeon.
*/
function put_dungeon($user, $time, $reason) {
    global $db;
    
    // Input validation
    $user = (int)$user;
    $time = (int)$time;
    if ($user <= 0 || $time <= 0) {
        error_log("Invalid parameters in put_dungeon: user=$user, time=$time");
        return false;
    }

    // Sanitize reason
    $reason = $db->escape(substr(strip_tags($reason), 0, 255));
    if (empty($reason)) {
        $reason = "Unknown reason";
    }
    
    try {
        $CurrentTime = time();
        $TimeMath = $time * 60;

        // Check if user is currently in dungeon
        $Dungeon = $db->fetch_single($db->query("SELECT `dungeon_out` 
                                                FROM `dungeon` 
                                                WHERE `dungeon_user` = {$user}"));

        if ($Dungeon <= $CurrentTime) {
            // New stay
            $db->query("UPDATE `dungeon` 
                       SET `dungeon_out` = {$CurrentTime} + {$TimeMath},
                           `dungeon_in` = {$CurrentTime},
                           `dungeon_reason` = '{$reason}'
                       WHERE `dungeon_user` = {$user}");
        } else {
            // Extend current stay
            $db->query("UPDATE `dungeon` 
                       SET `dungeon_out` = `dungeon_out` + {$TimeMath},
                           `dungeon_reason` = '{$reason}'
                       WHERE `dungeon_user` = {$user}");
        }
        return true;
    } catch (Exception $e) {
        error_log("Database error in put_dungeon: " . $e->getMessage());
        return false;
    }
}

/**
	The function for removing someones infirmary time.
	@param int $user The user to put in the infirmary
	@param int $time The time (in minutes) to remove.
*/
function remove_dungeon($user, $time) {
    global $db;
    
    // Input validation
    $user = (int)$user;
    $time = (int)$time;
    if ($user <= 0 || $time <= 0) {
        error_log("Invalid parameters in remove_dungeon: user=$user, time=$time");
        return false;
    }
    
    try {
        $TimeMath = $time * 60;
        
        $db->query("UPDATE `dungeon` 
                   SET `dungeon_out` = CASE 
                       WHEN (`dungeon_out` - {$TimeMath}) < `dungeon_in` 
                       THEN `dungeon_in`
                       ELSE `dungeon_out` - {$TimeMath}
                   END
                   WHERE `dungeon_user` = {$user}");
        
        return true;
    } catch (Exception $e) {
        error_log("Database error in remove_dungeon: " . $e->getMessage());
        return false;
    }
}

/**
	The function for testing for a valid email.
	@param text $email The email to test for.
*/
function valid_email($email)
{
    return (filter_var($email, FILTER_VALIDATE_EMAIL) === $email);
}

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
            "SELECT `itmtypeid`, `itmtypename`
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
            "SELECT `itmid`, `itmname`
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
            "SELECT `itmid`, `itmname`
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
            "SELECT `itmid`, `itmname`
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
            "SELECT `ac_id`, `ac_name`
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
            "SELECT `town_id`, `town_name`, `town_min_level`
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
            "SELECT `shopID`, `shopNAME`
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
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
        $db->query(
            "SELECT `userid`, `username`
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
            "SELECT `userid`, `username`
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
            "SELECT `guild_id`, `guild_name`
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
            "SELECT `userid`, `username`
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
            "SELECT `u`.`userid`, `u`.`username`
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
            "SELECT `userid`, `username`
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
        $db->query("SELECT `mbUSER`, `mbID`, `username`
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
            "SELECT `fb_user`,`fb_id`
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
            "SELECT `house_id`, `house_name`, `house_will`
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
            "SELECT `house_will`, `house_name`
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
            "SELECT `crimeID`, `crimeNAME`
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
            "SELECT `cgID`, `cgNAME`
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
 * Sends a user a notification, given their ID and the text.
 * @param int $userid The user ID to be sent the notification
 * @param string $text The notification's text. This should be fully sanitized for HTML, but not pre-escaped for database insertion.
 * @return true
 */
function notification_add($userid, $text)
{
    global $db;
    $text = $db->escape($text);
    $db->query(
        "INSERT INTO `notifications`
             VALUES(NULL, $userid, " . time() . ", 'unread', '$text')");
    return true;
}

/*
	Internal Function: Used to update all sorts of things around the game
*/
function check_data()
{
    global $db, $time;
    $q1 = $db->query("SELECT `fed_userid` FROM `fedjail` WHERE `fed_out` < {$time}");
    //Remove players from federal jail, if needed.
    if ($db->num_rows($q1) > 0) {
        $q2 = $db->fetch_single($q1);
        $db->query("DELETE FROM `fedjail` WHERE `fed_out` < {$time}");
        $db->query("UPDATE `users` SET `fedjail` = 0 WHERE `userid` = {$q2}");
    }
    //Remove players forum bans if needed.
    $db->query("DELETE FROM `forum_bans` WHERE `fb_time` < {$time}");

    //Remove players' mail bans if needed.
    $db->query("DELETE FROM `mail_bans` WHERE `mbTIME` < {$time}");

    $q3 = $db->query("SELECT * FROM `guild_wars` WHERE `gw_end` < {$time} AND `gw_winner` = 0");
    if ($db->num_rows($q3) > 0) {
        $r3 = $db->fetch_row($q3);
        //Select guild war declarer's name
        $guild_declare = $db->fetch_single(
            $db->query("SELECT `guild_name` FROM `guild` WHERE `guild_id` = {$r3['gw_declarer']}"));
        //Select guild war declaree's name
        $guild_declared = $db->fetch_single(
            $db->query("SELECT `guild_name` FROM `guild` WHERE `guild_id` = {$r3['gw_declaree']}"));
        //Guild War declarer has more points than the declaree.
        if ($r3['gw_drpoints'] > $r3['gw_depoints']) {
            //Make the declarer the winner,
            $db->query("UPDATE `guild_wars` SET `gw_winner` = {$r3['gw_declarer']} WHERE `gw_id` = {$r3['gw_id']}");
            guildnotificationadd($r3['gw_declarer'], "Your guild has defeated the {$guild_declared} guild in battle.");
            guildnotificationadd($r3['gw_declaree'], "Your guild was defeated in battle by the {$guild_declare} guild.");
            //Select the town ID where the guilds own.
            $town = $db->fetch_single(
                $db->query("SELECT `town_id` FROM `town` WHERE `town_guild_owner` = {$r3['gw_declarer']}"));
            $town2 = $db->fetch_single(
                $db->query("SELECT `town_id` FROM `town` WHERE `town_guild_owner` = {$r3['gw_declaree']}"));
            //If the declaree has a town under their control
            if ($town2 > 0) {
                //The declarer guild has no town of their own, so take from the declaree.
                if ($town == 0) {
                    $db->query("UPDATE `town` SET `town_guild_owner` = {$r3['gw_declarer']}  WHERE `town_guild_owner` = {$r3['gw_declaree']}");
                } //The declarer has their own town, so the declaree forfeits their control of their own town.
                else {
                    $db->query("UPDATE `town` SET `town_guild_owner` = 0 WHERE `town_guild_owner` = {$r3['gw_declaree']}");
                }
            }

        } //Guild War declaree has more points than the declarer.
        elseif ($r3['gw_drpoints'] < $r3['gw_depoints']) {
            //Make the declaree the winner,
            $db->query("UPDATE `guild_wars` SET `gw_winner` = {$r3['gw_declarer']} WHERE `gw_id` = {$r3['gw_id']}");
            guildnotificationadd($r3['gw_declaree'], "Your guild has defeated the {$guild_declare} guild in battle.");
            guildnotificationadd($r3['gw_declarer'], "Your guild was defeated in battle by the {$guild_declared} guild.");
            //Select the town ID where the guilds own.
            $town = $db->fetch_single(
                $db->query("SELECT `town_id` FROM `town` WHERE `town_guild_owner` = {$r3['gw_declarer']}"));
            $town2 = $db->fetch_single(
                $db->query("SELECT `town_id` FROM `town` WHERE `town_guild_owner` = {$r3['gw_declaree']}"));
            //If the declarer has a town under their control
            if ($town > 0) {
                //The declaree does not have a town, so take it from the declarer.
                if ($town2 == 0) {
                    $db->query("UPDATE `town` SET `town_guild_owner` = {$r3['gw_declaree']} WHERE `town_guild_owner` = {$r3['gw_declarer']}");
                } //The declaree has their own town, so make the declarer forfeit theirs.
                else {
                    $db->query("UPDATE `town` SET `town_guild_owner` = 0 WHERE `town_guild_owner` = {$r3['gw_declarer']}");
                }
            }
        } //The war was tied. Tell both guilds they tied, and remove the war from the database.
        else {
            $db->query("DELETE FROM `guild_wars` WHERE `gw_id` = {$r3['gw_id']}");
            guildnotificationadd($r3['gw_declaree'], "Your guild has tied the {$guild_declare} guild in battle.");
            guildnotificationadd($r3['gw_declarer'], "Your guild has tied the {$guild_declared} guild in battle.");
        }
        //Update guild experience, if needed.
        $db->query("UPDATE `guild` SET `guild_xp` = `guild_xp` + {$r3['gw_drpoints']} WHERE `guild_id` = {$r3['gw_declarer']}");
        $db->query("UPDATE `guild` SET `guild_xp` = `guild_xp` + {$r3['gw_depoints']} WHERE `guild_id` = {$r3['gw_declaree']}");
    }
    //Assign the Unix Timestamp to a variable.
    $time = time();
    //Select a User's ID and Course ID if their completion time is less than the Unix Timestamp, and they still have
    //not been credited from their completion.
    $coursedone = $db->query("SELECT `userid`,`course` FROM `users` WHERE `course` > 0 AND `course_complete` < {$time}");
    $course_cache = array();
    //Loop until no more users have courses left.
    while ($r = $db->fetch_row($coursedone)) {
        //If the course in question is not stored in cache, lets store it.
        if (!array_key_exists($r['course'], $course_cache)) {
            $cd = $db->query("SELECT `ac_str`, `ac_agl`, `ac_grd`, `ac_lab`, `ac_iq`, `ac_name`
							 FROM `academy`
							 WHERE `ac_id` = {$r['course']}");
            $coud = $db->fetch_row($cd);
            $db->free_result($cd);
            $course_cache[$r['course']] = $coud;
        } //Store in cache anyway.
        else {
            $coud = $course_cache[$r['course']];
        }
        //Mark user as have completed this course.
        $db->query("INSERT INTO `academy_done` VALUES({$r['userid']}, {$r['course']})");
        $upd = "";
        $ev = "";
        //Course credits strength, so add onto the query.
        if ($coud['ac_str'] > 0) {
            $upd .= ", us.strength = us.strength + {$coud['ac_str']}";
            $ev .= ", {$coud['ac_str']} Strength";
        }
        //Course credits guard, so add onto the query.
        if ($coud['ac_grd'] > 0) {
            $upd .= ", us.guard = us.guard + {$coud['ac_grd']}";
            $ev .= ", {$coud['ac_grd']} Guard";
        }
        //Course credits labor, so add onto the query.
        if ($coud['ac_lab'] > 0) {
            $upd .= ", us.labor = us.labor + {$coud['ac_lab']}";
            $ev .= ", {$coud['ac_lab']} Labor";
        }
        //Course credits agility, so add onto the query.
        if ($coud['ac_agl'] > 0) {
            $upd .= ", us.agility = us.agility + {$coud['ac_agl']}";
            $ev .= ", {$coud['ac_agl']} Agility";
        }
        //Course credits IQ, so add onto the query.
        if ($coud['ac_iq'] > 0) {
            $upd .= ", us.IQ = us.IQ + {$coud['ac_iq']}";
            $ev .= ", {$coud['ac_iq']} IQ";
        }
        //Merge all $ev into a comma seperated event.
        $ev = substr($ev, 1);
        //Update the user's stats as needed, set their course to 0, and course completion time to 0.
        $db->query("UPDATE `users` AS `u` INNER JOIN `userstats` AS `us` ON `u`.`userid` = `us`.`userid`
		SET `u`.`course` = 0, `course_complete` = 0 WHERE `u`.`userid` = {$r['userid']}");
        //Give the user a notification saying they've completed their course.
        notification_add($r['userid'], "Congratulations, you completed the {$coud['ac_name']} course and gained {$ev}!");
    }
    //Check guild crimes!
    $guildcrime = $db->query("SELECT * FROM `guild` WHERE `guild_crime` > 0 AND `guild_crime_done` < {$time}");
    while ($r = $db->fetch_row($guildcrime)) {
        $r2 = $db->fetch_row($db->query("SELECT * FROM `guild_crimes` WHERE `gcID` = {$r['guild_crime']}"));
        $suc = Random(0, 1);
        if ($suc == 1) {
            $log = $r2['gcSTART'] . $r2['gcSUCC'];
            $winnings = Random($r2['gcMINCASH'], $r2['gcMAXCASH']);
            $result = 'Success';
        } else {
            $log = $r2['gcSTART'] . $r2['gcFAIL'];
            $winnings = 0;
            $result = 'Failure';
        }
        $xp=Random(1,5);
        $db->query("UPDATE `guild`
                    SET `guild_primcurr` = `guild_primcurr` + {$winnings},
                    `guild_crime` = 0,
                    `guild_crime_done` = 0,
                    `guild_xp` = `guild_xp` + {$xp}
                    WHERE `guild_id` = {$r['guild_id']}");
        $db->query("INSERT INTO `guild_crime_log`
                    (`gclCID`, `gclGUILD`, `gclLOG`, `gclRESULT`, `gclWINNING`, `gclTIME`)
                    VALUES
                    ('{$r['guild_crime']}', '{$r['guild_id']}', '{$log}', '{$result}', '{$winnings}', '" . time() . "');");
        $i = $db->insert_id();
        $qm = $db->query("SELECT `userid` FROM `users` WHERE `guild` = {$r['guild_id']}");
        while ($qr = $db->fetch_row($qm)) {
            notification_add($qr['userid'], "Your guild's crime was a complete {$result}! Click <a href='gclog.php?ID=$i'>here</a> to view more information.");
        }
    }
}

/**
 * Internal function: used to see if a user is due to level up, and if so, perform that levelup.
 */
function check_level()
{
    global $ir, $userid, $db;
    $ir['xp_needed'] = round(($ir['level'] + 2.25) * ($ir['level'] + 2.25) * ($ir['level'] + 2.25) * 2);
    if ($ir['xp'] >= $ir['xp_needed']) {
        $expu = $ir['xp'] - $ir['xp_needed'];
        $ir['level'] += 1;
        $ir['xp'] = $expu;
        $ir['energy'] += 2;
        $ir['brave'] += 2;
        $ir['maxenergy'] += 2;
        $ir['maxbrave'] += 2;
        $ir['hp'] += 50;
        $ir['maxhp'] += 50;
        $ir['xp_needed'] = round(($ir['level'] + 2.25) * ($ir['level'] + 2.25) * ($ir['level'] + 2.25) * 2);
        //Increase user's everything.
        $db->query("UPDATE `users` SET `level` = `level` + 1, `xp` = '{$expu}', `energy` = `energy` + 2,
					`brave` = `brave` + 2, `maxenergy` = `maxenergy` + 2, `maxbrave` = `maxbrave` + 2,
					`hp` = `hp` + 50, `maxhp` = `maxhp` + 50 WHERE `userid` = {$userid}");
        //Give the user some stats for leveling up.
        $StatGain = round(($ir['level'] * 100) / Random(2, 6));
        $StatGainFormat = number_format($StatGain);
        //Assign the stat gain to the user's class of choice.
        if ($ir['class'] == 'Warrior') {
            $Stat = 'strength';
        } elseif ($ir['class'] == 'Rogue') {
            $Stat = 'agility';
        } else {
            $Stat = 'guard';
        }
        //Credit the stat gain.
        $db->query("UPDATE `userstats` SET `{$Stat}` = `{$Stat}` + {$StatGain} WHERE `userid` = {$userid}");
        //Tell the user they've gained some stats.
        notification_add($userid, "You have successfully leveled up and gained {$StatGainFormat} in {$Stat}.");
        //Log the level up, along with the stats gained.
        SystemLogsAdd($userid, 'level', "Leveled up to level {$ir['level']} and gained {$StatGainFormat} in {$Stat}.");
    }
}

/**
 * Sends a guild a notification, given their ID and the text.
 * @param int $guild_id The guild ID to be sent the notification
 * @param string $text The notification's text. This should be fully sanitized for HTML, but not pre-escaped for database insertion.
 * @return true
 */
function guildnotificationadd($guild_id, $text)
{
    global $db;
    $text = $db->escape($text);
    $db->query(
        "INSERT INTO `guild_notifications`
             VALUES(NULL, {$guild_id}, " . time() . ", '{$text}')");
    return true;
}

/**
 * Get the "rank" a user has for a particular stat - if the return is n, then the user has the n'th highest value for that stat.
 * @param int $stat The value of the current user's stat.
 * @param string $mykey The stat to be ranked in. Must be a valid column name in the userstats table
 * @return integer The user's rank in the stat
 */
function get_rank($stat, $mykey)
{
    global $db, $userid;
    //Select count of users who have higher $mykey based upon $stat. Excluding the current user, admins and NPCs
    if ($mykey != 'all') {
        $q = $db->query("SELECT count(`u`.`userid`) FROM `userstats` AS `us` LEFT JOIN `users` AS `u`
                    ON `us`.`userid` = `u`.`userid` WHERE {$mykey} > {$stat} AND `us`.`userid` != {$userid}
                    AND `u`.`user_level` != 'Admin' AND `u`.`user_level` != 'NPC'");
    } else {
        $q = $db->query("SELECT count(`u`.`userid`) FROM `userstats` AS `us` LEFT JOIN `users` AS `u`
                    ON `us`.`userid` = `u`.`userid` WHERE `strength`+`agility`+`guard`+`labor`+`iq` > {$stat} AND `us`.`userid` != {$userid}
                    AND `u`.`user_level` != 'Admin' AND `u`.`user_level` != 'NPC'");
    }
    $result = $db->fetch_single($q) + 1;
    $db->free_result($q);
    //Return the count from earlier.
    return $result;
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
    $ie = $db->fetch_single($db->query("SELECT COUNT(`itmname`) FROM `items` WHERE `itmid` = {$itemid}"));
    //If the name returns, continue
    if ($ie > 0) {
        //We want $itemid to go into its own stack. Select the inventory ID to make sure this doesn't happen.
        if ($notid > 0) {
            $q = $db->query("SELECT `inv_id` FROM `inventory` WHERE `inv_userid` = {$user} AND `inv_itemid` = {$itemid}
							 AND `inv_id` != {$notid} LIMIT 1");
        } //We don't care if the $itemid merges into an existing inventory stack. Let's select the first stack then.
        else {
            $q = $db->query("SELECT `inv_id` FROM `inventory` WHERE `inv_userid` = {$user} AND `inv_itemid` = {$itemid}
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
    $ie = $db->fetch_single($db->query("SELECT COUNT(`itmname`) FROM `items` WHERE `itmid` = {$itemid}"));
    //If $itemid actually exists, it'll return a name, so lets continue if that's the case.
    if ($ie > 0) {
        //Select the inventory ID number where $itemid's is stored for $user.
        $q = $db->query("SELECT `inv_id`, `inv_qty` FROM `inventory` WHERE `inv_userid` = {$user}
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
            "SELECT `ff_id`, `ff_name`
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
 * Request that an anti-CSRF verification code be issued for a particular form.
 * @param string $formid A unique string used to identify this form
 * @param int $expiry Optional expiry time in seconds (default 1 hour)
 * @return string The code issued to be added to the form.
 */
function request_csrf_code($formid, $expiry = 3600)
{
    if (empty($formid)) {
        throw new InvalidArgumentException("Form ID cannot be empty");
    }

    $time = time();
    $token = randomizer();
    
    // Add additional entropy
    $entropy = hash('sha256', session_id() . $time . $formid);
    $finalToken = hash('sha512', $token . $entropy);
    
    $_SESSION["csrf_{$formid}"] = [
        'token' => $finalToken,
        'issued' => $time,
        'expiry' => $expiry
    ];
    
    return $finalToken;
}

/**
 * Request that an anti-CSRF verification code be issued for a particular form in the game, and return the HTML to be placed in the form.
 * @param string $formid A unique string used to identify this form to match up its submission with the right token.
 * @return string The HTML for the code issued to be added to the form.
 */
function request_csrf_html($formid)
{
    return "<input type='hidden' name='verf' value='" . request_csrf_code($formid) . "' />";
}


/**
 * Request a cryptographically secure random token
 * @return string A secure random token
 */
function randomizer()
{
    try {
        // Use PHP 7+ random_bytes for best security
        if (function_exists('random_bytes')) {
            return bin2hex(random_bytes(64));
        }
        
        // Fallback to OpenSSL if available
        if (function_exists('openssl_random_pseudo_bytes')) {
            $strong = true;
            $bytes = openssl_random_pseudo_bytes(64, $strong);
            if ($bytes !== false && $strong) {
                return bin2hex($bytes);
            }
        }
        
        // Last resort fallback
        $bytes = '';
        for ($i = 0; $i < 64; $i++) {
            $bytes .= chr(Random(0, 255));
        }
        return bin2hex($bytes);
    } catch (Exception $e) {
        // Log error and fallback to basic random
        error_log("Failed to generate secure random bytes: " . $e->getMessage());
        return hash('sha512', uniqid(mt_rand(), true));
    }
}

/**
 * Check the CSRF code against the registered token
 * @param string $formid Form identifier
 * @param string $code User provided token
 * @param int $expiry Optional override for token expiry
 * @return boolean Whether the token is valid
 */
function verify_csrf_code($formid, $code, $expiry = null) 
{
    if (empty($formid) || empty($code)) {
        return false;
    }

    $sessionKey = "csrf_{$formid}";
    
    if (!isset($_SESSION[$sessionKey]) || !is_array($_SESSION[$sessionKey])) {
        return false;
    }

    try {
        $token = $_SESSION[$sessionKey];
        
        // Use token-specific expiry if set, otherwise use passed expiry
        $tokenExpiry = $expiry ?? $token['expiry'] ?? 3600;
        
        // Validate token age
        if (time() > ($token['issued'] + $tokenExpiry)) {
            throw new Exception('Token expired');
        }

        // Constant-time string comparison
        $valid = hash_equals($token['token'], $code);
        
        // Always clean up used/expired tokens
        unset($_SESSION[$sessionKey]);
        
        return $valid;
    } catch (Exception $e) {
        error_log("CSRF validation failed: " . $e->getMessage());
        unset($_SESSION[$sessionKey]);
        return false;
    }
}

/**
 * Given a password input given by the user and their actual details,
 * determine whether the password entered was correct.
 *
 * @param string $input The input password given by the user.
 *                        Should be without slashes.
 * @param string $pass The user's encrypted password
 *
 * @return boolean    true for equal, false for not (login failed etc)
 */
function verify_user_password($input, $pass)
{
    if (empty($input) || empty($pass)) {
        return false;
    }
    
    // Handle both new format and legacy format
    if (substr($pass, 0, 4) === '$2y$' || substr($pass, 0, 4) === '$2a$') {
        return password_verify(base64_encode(hash('sha256', $input, true)), $pass);
    } else {
        // Legacy format fallback - should upgrade on next login
        return (password_verify(base64_encode(hash('sha256', $input, true)), $pass));
    }
}

/**
 * Given a password, encode it securely for storage in the database.
 *
 * @param string $password The password to be encoded
 * @param int $type The password hashing algorithm to use
 * @return string The resulting encoded password.
 */
function encode_password($password, $type = PASSWORD_DEFAULT) 
{
    global $set;
    
    if (empty($password)) {
        throw new InvalidArgumentException("Password cannot be empty");
    }

    // Generate a cryptographically secure hash
    $hash = base64_encode(hash('sha256', $password, true));
    
    $options = [
        'cost' => isset($set['Password_Effort']) ? (int)$set['Password_Effort'] : 10
    ];

    if ($type === PASSWORD_BCRYPT) {
        // Ensure cost is between 10-31 for bcrypt
        $options['cost'] = max(10, min(31, $options['cost']));
    }

    try {
        return password_hash($hash, $type, $options);
    } catch (Exception $e) {
        // Fallback to default if specified algorithm fails
        return password_hash($hash, PASSWORD_DEFAULT);
    }
}

/**
 * Easily outputs an alert to the client.
 * Text $type = Alert type. [Valid: danger, success, info, warning, primary, secondary, light, dark]
 * Text $title = Alert Title.
 * Text $text = Alert text.
 * Boolean $doredirect = Whether or not to actually redirect. [Default = true]
 * Text $redirect = File Name to redirect to. [Default = back] [back will reload current page]
 * Text $redirecttext = Text to be shown on the redirect link. [Default = Back]
 */

function alert($type, $title, $text, $doredirect = true, $redirect = 'back', $redirecttext = 'Back')
{
    //This function is a horrible mess dude..
    if ($type == 'danger')
        $icon = "exclamation-triangle";
    elseif ($type == 'success')
        $icon = "check-circle";
    elseif ($type == 'info')
        $icon = 'info-circle';
    else
        $icon = 'exclamation-circle';
    if ($doredirect) {
        $redirect = ($redirect == 'back') ? $_SERVER['REQUEST_URI'] : $redirect;
        echo "<div class='alert alert-{$type}'>
				<i class='fa fa-{$icon}' aria-hidden='true'></i>
					<strong>{$title}</strong> 
						{$text} > <a href='{$redirect}' class='alert-link'>{$redirecttext}</a>
				</div>";
    } else {
        echo "<div class='alert alert-{$type}'>
                    <i class='fa fa-{$icon}' aria-hidden='true'></i>
					    <strong>{$title}</strong>
					        {$text}
                </div>";
    }
}

/**
 *
 * @return string The URL of the game.
 */
function determine_game_urlbase()
{
    // Get host with port if non-standard
    $host = $_SERVER['HTTP_HOST'];
    $requestUri = $_SERVER['REQUEST_URI'];
    
    // Basic sanitization
    $host = filter_var($host, FILTER_SANITIZE_URL);
    $requestUri = filter_var($requestUri, FILTER_SANITIZE_URL);
    
    // Extract the path portion up to the last /
    $path = preg_replace('#/[^/]*$#', '', $requestUri);
    
    // Ensure proper URL format
    $proto = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
    
    return rtrim($proto . $host . $path, '/');
}

/**
 * Check to see if this request was made via XMLHttpRequest.
 * Uses variables supported by most JS frameworks.
 *
 * @return boolean Whether the request was made via AJAX or not.
 **/

function is_ajax()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && is_string($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Get the file size in bytes of a remote file, if we can.
 *
 * @param string $url The url to the file
 *
 * @return int            The file's size in bytes, or 0 if we could
 *                        not determine its size.
 */

function get_filesize_remote($url)
{
    try {
        // Input validation
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return 0;
        }

        // Only allow http/https protocols
        if (!preg_match('/^https?:\/\//i', $url)) {
            return 0;
        }

        $parsed = parse_url($url);
        if ($parsed === false || empty($parsed['host'])) {
            return 0;
        }

        // Set up context with timeouts and security options
        $ctx = stream_context_create([
            'http' => [
                'method' => 'HEAD',
                'timeout' => 5,
                'ignore_errors' => true,
                'follow_location' => 0,
                'max_redirects' => 0,
                'protocol_version' => 1.1,
                'header' => [
                    'Connection: close'
                ]
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true
            ]
        ]);

        $headers = @get_headers($url, true, $ctx);
        if ($headers === false) {
            return 0;
        }

        // Look for content-length in a case-insensitive way
        foreach ($headers as $name => $value) {
            if (strcasecmp($name, 'content-length') === 0) {
                return (int)$value;
            }
        }

        return 0;
    } catch (Exception $e) {
        error_log("Error getting remote file size: " . $e->getMessage());
        return 0;
    }
}

/**
	Gets the contents of a file if it exists, otherwise grabs and caches 
*/
function get_fg_cache($file, $ip, $hours = 1)
{
    $current_time = time();
    $expire_time = $hours * 60 * 60;
    if (file_exists($file)) {
        $file_time = filemtime($file);
        if ($current_time - $expire_time < $file_time) {
            return file_get_contents($file);
        } else {
            $content = update_fg_info($ip);
            file_put_contents($file, $content);
            return $content;
        }
    } else {
        $content = update_fg_info($ip);
        file_put_contents($file, $content);
        return $content;
    }
}

/**
	Gets content from a URL via curl 
*/
function update_fg_info($ip)
{
    global $set;
    
    try {
        // Validate IP
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new Exception("Invalid IP address");
        }

        // Validate credentials
        if (empty($set['FGUsername']) || empty($set['FGPassword'])) {
            throw new Exception("Missing FraudGuard credentials");
        }

        $curl = curl_init();
        $options = array(
            CURLOPT_URL => "https://api.fraudguard.io/ip/" . urlencode($ip),
            CURLOPT_USERPWD => $set['FGUsername'] . ":" . $set['FGPassword'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_USERAGENT => "ChivalryEngine/v{$set['Version_Number']}",
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
            CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS
        );
        
        curl_setopt_array($curl, $options);
        
        $content = curl_exec($curl);
        
        if ($content === false) {
            throw new Exception("CURL Error: " . curl_error($curl));
        }
        
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($httpCode !== 200) {
            throw new Exception("HTTP Error: Received code " . $httpCode);
        }
        
        return $content;
        
    } catch (Exception $e) {
        error_log("FraudGuard API error: " . $e->getMessage());
        return false;
    } finally {
        if (isset($curl)) {
            curl_close($curl);
        }
    }
}

/**
 * Tests to see if the user's permission is allowed or not.
 *
 * @param string $perm The permission to test for
 * @param int $user The user to test on
 *
 * @return bool                Returns true if the user has this,
 *                            false if not.
 */
function permission($perm, $user)
{
    global $db;
    $Query = $db->query("SELECT `perm_disable` FROM `permissions` WHERE `perm_name` = '{$perm}' AND `perm_user` = {$user}");
    if ($db->num_rows($Query) == 0)
        return true;
    $q = $db->fetch_single($Query);
    if ($q == 'false')
        //User does have this permission
        return true;
}

/**
   Gets the user's operating system and inserts it into the database.
   @param string $uagent	User agent to test with.
*/
function getOS($uagent)
{
    global $db, $userid;
    $uagent = $db->escape(strip_tags(stripslashes($uagent)));
    $os_platform = "Unknown OS Platform";
    $os_array = array(
        '/windows nt 10/i' => 'Windows 10',
        '/windows nt 6.3/i' => 'Windows 8.1',
        '/windows nt 6.2/i' => 'Windows 8',
        '/windows nt 6.1/i' => 'Windows 7',
        '/windows nt 6.0/i' => 'Windows Vista',
        '/windows nt 5.1/i' => 'Windows XP',
        '/windows phone 8.0/i' => 'Windows Phone',
        '/windows xp/i' => 'Windows XP',
        '/macintosh|mac os x/i' => 'Mac OS X',
        '/mac_powerpc/i' => 'Mac OS 9',
        '/linux/i' => 'Linux',
        '/ubuntu/i' => 'Ubuntu',
        '/iphone/i' => 'iPhone',
        '/ipod/i' => 'iPod',
        '/ipad/i' => 'iPad',
        '/android/i' => 'Android',
        '/blackberry/i' => 'BlackBerry',
        '/webos/i' => 'Mobile'
    );

    foreach ($os_array as $regex => $value) {
        if (preg_match($regex, $uagent)) {
            $os_platform = $value;
        }
    }
    $count = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `userid` = {$userid}"));
    if ($count == 0)
        $db->query("INSERT INTO `userdata` (`userid`, `useragent`, `screensize`, `os`, `browser`) VALUES ({$userid}, '{$uagent}', '', '{$os_platform}', '')");
    else
        $db->query("UPDATE `userdata` SET `useragent` = '{$uagent}', `os` = '{$os_platform}' WHERE `userid` = {$userid}");
}

/**
  Gets the user's browser and inserts it into the database.
  @param string $uagent	User agent to test with.
*/
function getBrowser($uagent)
{
    global $db, $userid;
    $user_agent = $db->escape(strip_tags(stripslashes($uagent)));
    $browser = "Unknown Browser";
    $browser_array = array(
        '/msie/i' => 'Internet Explorer',
        '/firefox/i' => 'Firefox',
        '/safari/i' => 'Safari',
        '/chrome/i' => 'Chrome',
        '/edge/i' => 'Edge',
        '/opera/i' => 'Opera',
        '/netscape/i' => 'Netscape',
        '/maxthon/i' => 'Maxthon',
        '/konqueror/i' => 'Konqueror',
        '/opr/i' => 'Opera',
        '/mobile/i' => 'Handheld Browser',
        '/CEngine-App/i' => 'App'
    );
    foreach ($browser_array as $regex => $value) {
        if (preg_match($regex, $user_agent)) {
            $browser = $value;
        }
    }
    $count = $db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `userdata` WHERE `userid` = {$userid}"));
    if ($count == 0)
        $db->query("INSERT INTO `userdata` (`userid`, `useragent`, `browser`) VALUES ({$userid}, '{$uagent}', '{$broswer}')");
    else
        $db->query("UPDATE `userdata` SET `useragent` = '{$user_agent}', `browser` = '{$browser}' WHERE `userid` = {$userid}");
}

/**
 * Please use $api->SystemLogsAdd(); instead 
 * */
function SystemLogsAdd($user, $logtype, $input)
{
    global $db;
    $time = time();
    $IP = $db->escape($_SERVER['REMOTE_ADDR']);
    $user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
    $input = $db->escape(str_replace("\n", "<br />", strip_tags(stripslashes($input))));
    $logtype = $db->escape(str_replace("\n", "<br />", strip_tags(stripslashes(strtolower($logtype)))));
    $db->query("INSERT INTO `logs`
				(`log_id`, `log_type`, `log_user`, `log_time`, `log_text`, `log_ip`) 
				VALUES 
				(NULL, '{$logtype}', '{$user}', '{$time}', '{$input}', '{$IP}');");
}

/**
 * Generate cryptographically secure random numbers with fallbacks
 * @param int $min Minimum value
 * @param int $max Maximum value
 * @return int A random number between min and max
 */
function Random($min = 0, $max = PHP_INT_MAX)
{
    try {
        // Input validation
        $min = (int)$min;
        $max = (int)$max;
        if ($min > $max) {
            throw new Exception("Minimum value cannot be greater than maximum value");
        }

        // Prefer random_int for cryptographic security
        if (function_exists('random_int')) {
            return random_int($min, $max);
        }
        
        // Fallback to OpenSSL
        if (function_exists('openssl_random_pseudo_bytes')) {
            $range = $max - $min;
            if ($range < 0) {
                $range = PHP_INT_MAX;
            }
            
            $bytes = openssl_random_pseudo_bytes(PHP_INT_SIZE);
            if ($bytes === false) {
                throw new Exception("Failed to generate secure random bytes");
            }
            
            $value = 0;
            for ($i = 0; $i < PHP_INT_SIZE; $i++) {
                $value = ($value << 8) | ord($bytes[$i]);
            }
            $value = abs($value);
            
            // Ensure even distribution
            $max_range = PHP_INT_MAX - (PHP_INT_MAX % ($range + 1));
            if ($value > $max_range) {
                // Try again if we're over the max range to ensure unbiased results
                return Random($min, $max);
            }
            
            return $min + ($value % ($range + 1));
        }
        
        // Last resort fallback with warning
        trigger_error(
            "Cryptographically secure random number generation not available. ".
            "Using less secure fallback method.", 
            E_USER_WARNING
        );
        return mt_rand($min, $max);
        
    } catch (Exception $e) {
        error_log("Random number generation error: " . $e->getMessage());
        // Fallback to mt_rand if everything else fails
        return mt_rand($min, $max);
    }
}

/*
	Creates a dropdown for smelting recipes.
*/
function smelt_dropdown($ddname = 'smelt', $selected = -1)
{
    global $db, $api;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
        $db->query(
            "SELECT `smelt_id`, `smelt_output`, `smelt_qty_output`
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

/*
	Gets the contents of a file if it exists, otherwise grabs and caches 
*/
function get_cached_file($url, $file, $hours = 1)
{
    $current_time = time();
    $expire_time = $hours * 60 * 60;
    if (file_exists($file)) {
        $file_time = filemtime($file);
        if ($current_time - $expire_time < $file_time) {
            return file_get_contents($file);
        } else {
            $content = update_file($url, $file);
            file_put_contents($file, $content);
            return $content;
        }
    } else {
        $content = update_file($url, $file);
        file_put_contents($file, $content);
        return $content;
    }
}

/* 
	Gets content from a URL via curl 
*/
function update_file($url)
{
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "{$url}",
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true));
    $content = curl_exec($curl);
    curl_close($curl);
    return $content;
}

/*
	Function to recache the specified forum topic
*/
function recache_topic($topic)
{
    global $db;
    $topic = abs((int)$topic);
    if ($topic <= 0) {
        return;
    }
    echo "Recaching Topic ID #{$topic} ... ";
    $q =
        $db->query(
            "SELECT `fp_poster_id`, `fp_poster_id`, `fp_time`
                     FROM `forum_posts`
                     WHERE `fp_topic_id` = {$topic}
                     ORDER BY `fp_time` DESC
                     LIMIT 1");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        $db->query(
            "UPDATE `forum_topics`
                 SET `ft_last_id` = 0, `ft_last_time` = 0, `ft_posts` = 0
                 WHERE `ft_id` = {$topic}");
    } else {
        $r = $db->fetch_row($q);
        $db->free_result($q);
        $posts_q =
            $db->query(
                "SELECT COUNT(`fp_id`)
        					   FROM `forum_posts`
        					   WHERE `fp_topic_id` = {$topic}");
        $posts = $db->fetch_single($posts_q);
        $db->free_result($posts_q);
        $db->query(
            "UPDATE `forum_topics`
                 SET `ft_last_id` = {$r['fp_poster_id']},
                 `ft_last_time` = {$r['fp_time']}, `ft_last_id` = '{$r['fp_poster_id']}',
                 `ft_posts` = {$posts}
                 WHERE `ft_id` = {$topic}");
    }
    echo " ... Recaching completed.<br />";
}

/*
	Function to recache the specified forum
*/
function recache_forum($forum)
{
    global $db;
    $forum = abs((int)$forum);
    if ($forum <= 0) {
        return;
    }
    echo "Recaching Forum ID #{$forum} ... ";
    $q =
        $db->query(
            "SELECT `fp_time`, `fp_poster_id`,
                     `ft_name`, `ft_id`
                     FROM `forum_posts` AS `p`
                     LEFT JOIN `forum_topics` AS `t`
                     ON `p`.`fp_topic_id` = `t`.`ft_id`
                     WHERE `p`.`ff_id` = {$forum}
                     ORDER BY `p`.`fp_time` DESC
                     LIMIT 1");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        $db->query(
            "UPDATE `forum_forums`
                 SET `ff_lp_time` = 0, `ff_lp_poster_id` = 0, `ff_lp_t_id` = 0,
                 `ff_lp_t_id` = 0
                  WHERE `ff_id` = {$forum}");
    } else {
        $r = $db->fetch_row($q);
        $db->free_result($q);
        $db->query(
            "UPDATE `forum_forums`
                 SET `ff_lp_time` = {$r['fp_time']},
                 `ff_lp_poster_id` = {$r['fp_poster_id']},
				 `ff_lp_t_id` = {$r['ft_id']}
                 WHERE `ff_id` = {$forum}");
    }
    echo " ... Recaching completed.<br />";
}

function isImage($url) {
    global $set;
    // Validate URL
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }

    // Only allow http and https protocols
    if (!preg_match('/^https?:\/\//i', $url)) {
        return false;
    }

    $params = array('http' => array(
        'method' => 'HEAD',
        'timeout' => 5, // 5 second timeout
        'user_agent' => "ChivalryEngine v{$set['Version_Number']} ImageValidator",
        'follow_location' => 0,  // Don't follow redirects
        'max_redirects' => 0,
        'protocol_version' => 1.1
    ));

    try {
        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if (!$fp) {
            return false;
        }

        $meta = stream_get_meta_data($fp);
        if ($meta === false) {
            fclose($fp);
            return false;
        }

        $wrapper_data = $meta["wrapper_data"];
        if (!is_array($wrapper_data)) {
            fclose($fp);
            return false;
        }

        // Check for valid image MIME types
        $allowedTypes = array(
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/bmp'
        );

        foreach ($wrapper_data as $header) {
            if (strpos(strtolower($header), 'content-type:') === 0) {
                $contentType = trim(substr($header, 13));
                fclose($fp);
                return in_array(strtolower($contentType), $allowedTypes);
            }
        }

        fclose($fp);
        return false;
    } catch (Exception $e) {
        error_log("Image validation error: " . $e->getMessage());
        return false;
    }
}

/*
 * Function to fetch current version of Chivalry Engine
 */
function version_json($url = 'https://raw.githubusercontent.com/MasterGeneral156/Version/master/chivalry-engine.json')
{
    global $set;
    $engine_version = $set['Version_Number'];
    $json = json_decode(get_cached_file($url, __DIR__ . "/cache/update_check.txt"), true);
    if (is_null($json))
        return "Update checker failed.";
    if (version_compare($engine_version, $json['latest']) == 0 || version_compare($engine_version, $json['latest']) == 1)
        return "Chivalry Engine is up to date.";
    else
        return "Chivalry Engine update available. Download it <a href='{$json['download-latest']}'>here</a>.";
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
            "SELECT `i`.*, `it`.*
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
        $ret .= ">{$r['itmname']} (You Have {$r['inv_qty']})</option>";
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
            "SELECT `jRANK`, `jNAME`
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
            "SELECT `jrID`, `jNAME`, `jrRANK`
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

function pagination($perpage, $total, $currentpage, $url)
{
    global $db;
    $pages = ceil($total / $perpage);
    $output = "<ul class='pagination justify-content-center'>";
    if ($currentpage <= 0) {
        $output .= "<li class='page-item disabled'><a class='page-link'>&laquo;</a></li>";
        $output .= "<li class='page-item disabled'><a class='page-link'>Back</a></li>";
    } else {
        $link = $currentpage - $perpage;
        $output .= "<li class='page-item'><a class='page-link' href='{$url}0'>&laquo;</a></li>";
        $output .= "<li class='page-item'><a class='page-link' href='{$url}{$link}'>Back</a></li>";
    }
    for ($i = 1; $i <= $pages; $i++) {
        $s = ($i - 1) * $perpage;
        if (!((($currentpage - 3 * $perpage) > $s) || (($currentpage + 3 * $perpage) < $s))) {
            if ($s == $currentpage) {
                $output .= "<li class='page-item active'>";
            } else {
                $output .= "<li class='page-item'>";
            }
            $output .= "<a class='page-link' href='{$url}{$s}'>{$i}</li></a>";
        }
    }
    $maxpage = ($pages * $perpage) - $perpage;
    if ($currentpage >= $maxpage) {
        $output .= "<li class='page-item disabled'><a class='page-link'>Next</a></li>";
        $output .= "<li class='page-item disabled'><a class='page-link'>&raquo;</a></li>";
    } else {
        $link = $currentpage + $perpage;
        $output .= "<li class='page-item'><a class='page-link' href='{$url}{$link}'>Next</a></li>";
        $output .= "<li class='page-item'><a class='page-link' href='{$url}{$maxpage}'>&raquo;</a></li>";
    }
    $output .= "</ul></nav>";
    return $output;
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
            "SELECT `i`.*, `it`.*
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
 * Sends anonymous usage data with improved security
 * @param string $url The analytics endpoint
 * @return void
 */
function sendData($url = 'https://www.chivalryisdeadgame.com/chivalry-engine-analytics.php')
{
    global $set, $_CONFIG;
    
    try {
        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new Exception("Invalid analytics URL");
        }

        // Only allow HTTPS
        if (!preg_match('/^https:\/\//i', $url)) {
            throw new Exception("Analytics URL must use HTTPS");
        }
        
        // Prepare and validate data
        $data = array(
            'update' => '1',
            'domain' => determine_game_urlbase(),
            'gamename' => isset($set['WebsiteName']) ? $set['WebsiteName'] : '',
            'dbtype' => isset($_CONFIG['driver']) ? $_CONFIG['driver'] : '',
            'version' => isset($set['Version_Number']) ? $set['Version_Number'] : ''
        );
        
        // URL encode all values
        $postdata = http_build_query($data, '', '&', PHP_QUERY_RFC3986);
        
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postdata,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_USERAGENT => "Mozilla/5.0 Chivalry Engine Keep-Alive v{$set['Version_Number']}",
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 0,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
            CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
                'X-Requested-With: XMLHttpRequest'
            )
        );
        
        curl_setopt_array($ch, $options);
        
        $result = curl_exec($ch);
        
        if ($result === false) {
            throw new Exception("CURL Error: " . curl_error($ch));
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode !== 200) {
            throw new Exception("HTTP Error: Received code " . $httpCode);
        }
        
    } catch (Exception $e) {
        error_log("Analytics error: " . $e->getMessage());
    } finally {
        if (isset($ch)) {
            curl_close($ch);
        }
    }
}
