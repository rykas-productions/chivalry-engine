<?php
/*
	File:		global_func.php
	Created: 	4/5/2016 at 12:04AM Eastern Time
	Info: 		Functions used all over the game.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
/*
	Parses the time since the timestamp given.
	@param int $time_stamp for time since.
	@param boolean $ago to display the "ago" after the string. (Default = true)
*/
function dateTimeParse(int $time_stamp, bool $ago = true, bool $override = false)
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
        $unit = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade', 'century');
        $lengths = array(60, 60, 24, 7, 4.35, 12, 10, 10);
        //Go to the largest unit of time as possible.
        for ($i = 0; $time_difference >= $lengths[$i]; $i++) {
            $time_difference = $time_difference / $lengths[$i];
        }
        //For added precision, lets go over 2 decimal places.
        $time_difference = round($time_difference);
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

/*
	Parses how much time until the timestamp given.
	$param int $time_stamp for the timestamp.
*/
function timeUntilParse(int $time_stamp)
{
    //Time difference is Unix Timestamp subtracted from $time_stamp.
    $time_difference = $time_stamp - time();
    $unit = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade', 'century');
    $lengths = array(60, 60, 24, 7, 4.35, 12, 10, 10);
    //Get to the biggest unit type as possible.
    for ($i = 0; $time_difference >= $lengths[$i]; $i++) {
        $time_difference = $time_difference / $lengths[$i];
    }
    //For added precision, lets round to the 2nd decimal place.
    $time_difference = round($time_difference);
    //Add an 's' if needed.
    $date = $time_difference . ' ' . $unit[$i] . (($time_difference > 1 OR $time_difference < 1) ? 's' : '') . '';
    //Return $date
    return $date;
}

/*
	Parses the timestamp into a human friendly number.
*/
function timestampParse(int $time)
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

/*
	The function for testing if a player is in the hospital.
	@param int $user The user who to test for.
*/

function userInInfirmary(int $user)
{
    global $db;
    //Assign current Unix Time to a variable.
    $CurrentTime = time();
    //Select user from infirmary if their exit infirmary time is after the current Unix Timestamp.
    $query = $db->query("SELECT `infirmary_user` FROM `infirmary` WHERE `infirmary_user` = {$user} AND
                        `infirmary_out` > {$CurrentTime}");
    //Return false if they return no rows, true if they do.
    $return = ($db->num_rows($query) == 0) ? false : true;
    return $return;
}

/*
	The function for testing if a player is in the dungeon.
	@param int $user The user who to test for.
*/
function userInDungeon(int $user)
{
    global $db;
    //Assign current Unix Time to a variable.
    $CurrentTime = time();
    //Select user from dungeon if their exit dungeon time is after the current Unix Timestamp.
    $query = $db->query("SELECT `dungeon_user` FROM `dungeon` WHERE `dungeon_user` = {$user} AND
						`dungeon_out` > {$CurrentTime}");
    //Return false if they return no rows, true if they do.
    $return = ($db->num_rows($query) == 0) ? false : true;
    return $return;
}

/*
	The function for putting/adding onto someones infirmary time.
	@param int $user The user to put in the infirmary
	@param int $time The time (in minutes) to add.
	@param text $reason The reason the user is in the infirmary.
*/
function userPutInfirmary(int $user, int $time, string $reason)
{
    global $db;
    //Assign current Unix Timestamp to a variable.
    $CurrentTime = time();
    //Select the $user's current infirmary out time.
    $Infirmary = $db->fetch_single($db->query("SELECT `infirmary_out` FROM `infirmary` WHERE `infirmary_user` = {$user}"));
    //Since the time is in minutes, lets multiply the $time by 60. (Otherwise we would be adding seconds)
    $TimeMath = $time * 60;
    //If $user is currently not in the infirmary, lets add the time! (Their out time is the Unix Timestamp plus $TimeMath)
    if ($Infirmary <= $CurrentTime) {
        $db->query("UPDATE `infirmary` SET `infirmary_out` = {$CurrentTime} + {$TimeMath}, `infirmary_in` = {$CurrentTime},
					`infirmary_reason` = '{$reason}' WHERE `infirmary_user` = {$user}");
    } //If $user is already in the infirmary, lets just add $TimeMath onto their current sentence.
    else {
        $db->query("UPDATE `infirmary` SET `infirmary_out` = `infirmary_out` + {$TimeMath}, `infirmary_reason` = '{$reason}'
					WHERE `infirmary_user` = {$user}");
    }
}

/*
	The function for removing someones infirmary time.
	@param int $user The user to put in the infirmary
	@param int $time The time (in minutes) to remove.
*/
function userRemoveInfirmary(int $user, int $time)
{
    global $db;
    //Multiply $time by 60 since we're dealing with minutes, not seconds.
    $TimeMath = $time * 60;
    //Remove $TimeMath from their stay. $user will be removed from infirmary automatically, if needed.
    $db->query("UPDATE `infirmary` SET `infirmary_out` = `infirmary_out` - '{$TimeMath}' WHERE `infirmary_user` = {$user}");
}

/*
	The function for putting/adding onto someones dungeon time.
	@param int $user The user to put in the dungeon
	@param int $time The time (in minutes) to add.
	@param text $reason The reason the user is in the dungeon.
*/
function userPutDungeon(int $user, int $time, string $reason)
{
    global $db;
    //Assign current Unix Timestamp to a variable.
    $CurrentTime = time();
    //Select $user's dungeon exit time.
    $Dungeon = $db->fetch_single($db->query("SELECT `dungeon_out` FROM `dungeon` WHERE `dungeon_user` = {$user}"));
    //Since we're dealing with minutes, lets multiply $time by 60.
    $TimeMath = $time * 60;
    //If $user is not in the dungeon already, lets set their exit time to $CurrentTime + $TimeMath
    if ($Dungeon <= $CurrentTime) {
        $db->query("UPDATE `dungeon` SET `dungeon_out` = {$CurrentTime} + {$TimeMath}, `dungeon_in` = {$CurrentTime},
					`dungeon_reason` = '{$reason}' WHERE `dungeon_user` = {$user}");
    } //$user is already in the dungeon, so lets just add $TimeMath to their sentence.
    else {
        $db->query("UPDATE `dungeon` SET `dungeon_out` = `dungeon_out` + {$TimeMath}, `dungeon_reason` = '{$reason}'
					WHERE `dungeon_user` = {$user}");
    }
}

/*
	The function for removing someones infirmary time.
	@param int $user The user to put in the infirmary
	@param int $time The time (in minutes) to remove.
*/
function userRemoveDungeon(int $user, int $time)
{
    global $db;
    //Multiply $time by 60 since we're dealing with minutes, not seconds.
    $TimeMath = $time * 60;
    //Remove $TimeMath from $user's dungeon sentence. $user will be automatically removed from the dungeon if needed.
    $db->query("UPDATE `dungeon` SET `dungeon_out` = `dungeon_out` - '{$TimeMath}' WHERE `dungeon_user` = {$user}");
}

/*
	The function for testing for a valid email.
	@param text $email The email to test for.
*/
function validEmail(string $email)
{
    return (filter_var($email, FILTER_VALIDATE_EMAIL) === $email);
}

/**
 * Constructs a drop-down listbox of all the item types in the game to let the user select one.
 * @param string $dropdownname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the item type which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first item type alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function dropdownItemType(string $dropdownname = "item_type", int $selected = -1)
{
    global $db;
    $ret = "<select name='$dropdownname' class='custom-select' type='dropdown'>";
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
 * @param string $dropdownname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the item which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first item alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function dropdownWeapon(string $dropdownname = "weapon", int $selected = -1)
{
    global $db;
    $ret = "<select name='$dropdownname' class='custom-select' type='dropdown'>";
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
 * @param string $dropdownname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the item which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first item alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function dropdownArmor(string $dropdownname = "armor", int $selected = -1)
{
    global $db;
    $ret = "<select name='$dropdownname' class='custom-select' type='dropdown'>";
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
 * @param string $dropdownname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the item which should be selected by default.<br />
 * Not specifying this or setting it to a number less than 1 makes "None" selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function dropdownItem(string $dropdownname = "item", int $selected = -1)
{
    global $db;
    $ret = "<select name='$dropdownname' class='custom-select' type='dropdown'>";
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
function dropdownAcademy(string $acadname = "academy", int $selected = -1)
{
    global $db;
    $ret = "<select name='$acadname' class='custom-select' type='dropdown'>";
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
 * @param string $dropdownname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID number of the location which should be selected by default.
 * Not specifying this or setting it to -1 makes the first item alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function dropdownLocation(string $dropdownname = "location", int $selected = -1)
{
    global $db;
    $ret = "<select name='$dropdownname' class='custom-select' type='dropdown'>";
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
 * @param string $dropdownname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the shop which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first shop alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function dropdownShop(string $dropdownname = "shop", int $selected = -1)
{
    global $db;
    $ret = "<select name='$dropdownname' class='custom-select' type='dropdown'>";
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
 * @param string $dropdownname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the user who should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first user alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function dropdownUser(string $dropdownname = "user", int $selected = -1)
{
    global $db;
    $ret = "<select name='$dropdownname' class='custom-select' type='dropdown'>";
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
 * @param string $dropdownname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the user who should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first user alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function dropdownNPC(string $dropdownname = "user", int $selected = -1)
{
    global $db;
    $ret = "<select name='$dropdownname' class='custom-select' type='dropdown'>";
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
 * @param string $dropdownname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the guild who should be selected by default.
 * Not specifying this or setting it to -1 makes the first guild be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function dropdownGuild(string $dropdownname = "guild", int $selected = -1)
{
    global $db;
    $ret = "<select name='{$dropdownname}' class='custom-select' type='dropdown'>";
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
 * @param string $dropdownname The "name" attribute the <select> attribute should have
 * @param int $guild_id [optional] The ID Number of the guild who should be selected from.
 * @param int $selected [optional] The ID Number of the bot who should be selected by default.
 * Not specifying this or setting it to -1 makes the first bot alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function dropdownGuildUser(string $dropdownname = "user", int $guild_id, int $selected = -1)
{
    global $db;
    $ret = "<select name='{$dropdownname}' class='custom-select' type='dropdown'>";
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
 * @param string $dropdownname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the bot who should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first bot alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function dropdownNPCBot(string $dropdownname = "bot", int $selected = -1)
{
    global $db;
    $ret = "<select name='$dropdownname' class='custom-select' type='dropdown'>";
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
 * @param string $dropdownname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the user who should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first user alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function dropdownFedJailUser(string $dropdownname = "user", int $selected = -1)
{
    global $db;
    $ret = "<select name='$dropdownname' class='custom-select' type='dropdown'>";
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
 * @param string $dropdownname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the user who should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first user alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function dropdownMailbanUser(string $dropdownname = "user", int $selected = -1)
{
    global $db;
    $ret = "<select name='$dropdownname' class='custom-select' type='dropdown'>";
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
 * @param string $dropdownname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the user who should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first user alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function dropdownForumBanUser(string $dropdownname = "user", int $selected = -1)
{
    global $db, $api;
    $ret = "<select name='$dropdownname' class='custom-select' type='dropdown'>";
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
        $ret .= ">{$api->user->getNamefromID($r['fb_user'])} [{$r['fb_user']}]</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the houses in the game to let the user select one.
 * @param string $dropdownname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the house which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first house alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function dropdownEstate(string $dropdownname = "estate", int $selected = -1)
{
    global $db;
    $ret = "<select name='$dropdownname' class='custom-select' type='dropdown'>";
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
 * @param string $dropdownname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the house which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first house alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function dropdownEstateWill(string $dropdownname = "house", int $selected = -1)
{
    global $db;
    $ret = "<select name='$dropdownname' class='custom-select' type='dropdown'>";
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
 * @param string $dropdownname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the crime which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first crime alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function dropdownCrime(string $dropdownname = "crime", int $selected = -1)
{
    global $db;
    $ret = "<select name='$dropdownname' class='custom-select' type='dropdown'>";
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
 * @param string $dropdownname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the crime group which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first crime group alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function dropdownCrimeGroup(string $dropdownname = "crimegroup", int $selected = -1)
{
    global $db;
    $ret = "<select name='$dropdownname' class='custom-select' type='dropdown'>";
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
function addNotification(int $userid, string $text)
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
function checkData()
{
	checkGuildCrimes();
	checkGuildWars();
	checkAcademy();
	removePunishments();
}
function checkGuildCrimes()
{
	global $db, $time;
	//Check guild crimes!
    $guildcrime = $db->query("SELECT * FROM `guild` WHERE `guild_crime` > 0 AND `guild_crime_done` < {$time}");
    while ($r = $db->fetch_row($guildcrime)) {
        $r2 = $db->fetch_row($db->query("SELECT * FROM `guild_crimes` WHERE `gcID` = {$r['guild_crime']}"));
        $suc = randomNumber(0, 1);
        if ($suc == 1) {
            $log = $r2['gcSTART'] . $r2['gcSUCC'];
            $winnings = randomNumber($r2['gcMINCASH'], $r2['gcMAXCASH']);
            $result = 'Success';
        } else {
            $log = $r2['gcSTART'] . $r2['gcFAIL'];
            $winnings = 0;
            $result = 'Failure';
        }
        $xp=randomNumber(1,5);
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
            addNotification($qr['userid'], "Your guild's crime was a complete {$result}! Click <a href='gclog.php?ID=$i'>here</a> to view more information.");
        }
    }
}
function removePunishments()
{
	global $db,$time;
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
}

function checkGuildWars()
{
	global $db, $time;
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
            addGuildNotification($r3['gw_declarer'], "Your guild has defeated the {$guild_declared} guild in battle.");
            addGuildNotification($r3['gw_declaree'], "Your guild was defeated in battle by the {$guild_declare} guild.");
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
            addGuildNotification($r3['gw_declaree'], "Your guild has defeated the {$guild_declare} guild in battle.");
            addGuildNotification($r3['gw_declarer'], "Your guild was defeated in battle by the {$guild_declared} guild.");
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
            addGuildNotification($r3['gw_declaree'], "Your guild has tied the {$guild_declare} guild in battle.");
            addGuildNotification($r3['gw_declarer'], "Your guild has tied the {$guild_declared} guild in battle.");
        }
        //Update guild experience, if needed.
        $db->query("UPDATE `guild` SET `guild_xp` = `guild_xp` + {$r3['gw_drpoints']} WHERE `guild_id` = {$r3['gw_declarer']}");
        $db->query("UPDATE `guild` SET `guild_xp` = `guild_xp` + {$r3['gw_depoints']} WHERE `guild_id` = {$r3['gw_declaree']}");
    }
}

function checkAcademy()
{
	global $db, $time;
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
		SET `u`.`course` = 0, `course_complete` = 0{$upd} WHERE `u`.`userid` = {$r['userid']}");
        //Give the user a notification saying they've completed their course.
        addNotification($r['userid'], "Congratulations, you completed the {$coud['ac_name']} course and gained {$ev}!");
    }
}
/**
 * Internal function: used to see if a user is due to level up, and if so, perform that levelup.
 */
function checkLevel()
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
        $StatGain = round(($ir['level'] * 100) / randomNumber(2, 6));
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
        addNotification($userid, "You have successfully leveled up and gained {$StatGainFormat} in {$Stat}.");
        //Log the level up, along with the stats gained.
        addLog($userid, 'level', "Leveled up to level {$ir['level']} and gained {$StatGainFormat} in {$Stat}.");
    }
}

/**
 * Sends a guild a notification, given their ID and the text.
 * @param int $guild_id The guild ID to be sent the notification
 * @param string $text The notification's text. This should be fully sanitized for HTML, but not pre-escaped for database insertion.
 * @return true
 */
function addGuildNotification(int $guild_id, string $text)
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
function getRank(int $stat, string $mykey)
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
function addItem(int $user, int $itemid, int $qty, int $notid = 0)
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
function takeItem(int $user, int $itemid, int $qty)
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
 * @param string $dropdownname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the forum w hich should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first forum alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function dropdownForum(string $dropdownname = "forum", int $selected = -1)
{
    global $db;
    $ret = "<select name='$dropdownname' class='custom-select' type='dropdown'>";
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
 * Request that an anti-CSRF verification code be issued for a particular form in the game.
 * @param string $formid A unique string used to identify this form to match up its submission with the right token.
 * @return string The code issued to be added to the form.
 */
function getCodeCSRF(string $formid)
{
    global $db;
    //Assign Unix Timestamp to a variable.
    $time = time();
    //Generate the token from getRandomString(), and hash it with sha512.
    $token = getRandomString();
	$IP = $db->escape($_SERVER['REMOTE_ADDR']);
	$user_agent = $db->escape(strip_tags(stripslashes($_SERVER['HTTP_USER_AGENT'])));
    //Store the CSRF Form into $_SESSION.
    $_SESSION["csrf_{$formid}"] = array('token' => $token, 'issued' => $time, 'ip' => $IP, 'useragent' => $user_agent);
    //Return the token.
    return $token;
}

/**
 * Request a randomNumberly generated phrase.
 * Returns the randomNumberly generated phrase.
 */
function getRandomString()
{
        return bin2hex(random_bytes(256));
}

/**
 * Request that an anti-CSRF verification code be issued for a particular form in the game, and return the HTML to be placed in the form.
 * @param string $formid A unique string used to identify this form to match up its submission with the right token.
 * @return string The HTML for the code issued to be added to the form.
 */
function getHtmlCSRF(string $formid)
{
    return "<input type='hidden' name='verf' value='" . getCodeCSRF($formid) . "' />";
}

/**
 * Check the CSRF code we received against the one that was registered for the form - return false if the request shouldn't be processed...
 * @param string $formid A unique string used to identify this form to match up its submission with the right token.
 * @param string $code The code the user's form input returned.
 * @param int $expiry The amount of time the CSRF is valid for. Default 300 seconds.
 * @return boolean Whether the user provided a valid code or not
 */
function checkCSRF(string $formid, string $code, int $expiry = 300)
{
	global $db;
    //User does not have a CSRF Session started for $formid, or its missing information.
    if (!isset($_SESSION["csrf_{$formid}"]) || !is_array($_SESSION["csrf_{$formid}"])) {
        return false;
    } else {
		$IP = $db->escape($_SERVER['REMOTE_ADDR']);
		$user_agent = $db->escape(strip_tags(stripslashes($_SERVER['HTTP_USER_AGENT'])));
        //Set verified to false until we can be sure they have verified successfully.
        $verified = false;
        //Assign the CSRF $formid to a variable.
        $token = $_SESSION["csrf_{$formid}"];
        //Check to see if the token is still valid.
		if ($token['ip'] == $IP)
		{
			if ($token['useragent'] == $user_agent)
			{
				if ($token['issued'] + $expiry > time()) {
					//User becomes verified if the code matches the token that was stored in $_SESSION
					$verified = ($token['token'] === $code);
				}
			}
		}
        //Unset the CSRF $formid from $_SESSION
        unset($_SESSION["csrf_{$formid}"]);
        //Return if the user has verified successfully or not.
        return $verified;
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
 *
 */
function checkUserPassword(string $input, string $pass)
{
    //Check that the password matches or not.
    $return = (password_verify(base64_encode(hash('sha256', $input, true)), $pass)) ? true : false;
    return $return;
}

/**
 * Given a password and a salt, encode them to the form which is stored in
 * the game's database.
 *
 * @param string $password The password to be encoded
 *
 * @return string    The resulting encoded password.
 */
function encodePassword(string $password)
{
    global $set;
	//Set the password cost via settings.
    $options = ['cost' => $set['Password_Effort'],];
    return password_hash(base64_encode(hash('sha256', $password, true)), PASSWORD_BCRYPT, $options);
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

function alert(string $type, string $title, string $text, bool $doredirect = true, string $redirect = 'back', string $redirecttext = 'Back')
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
function getGameURL()
{
    $domain = $_SERVER['HTTP_HOST'];
    $turi = $_SERVER['REQUEST_URI'];
    $turiq = '';
    for ($t = strlen($turi) - 1; $t >= 0; $t--) {
        if ($turi[$t] != '/') {
            $turiq = $turi[$t] . $turiq;
        } else {
            break;
        }
    }
    $turiq = '/' . $turiq;
    if ($turiq == '/') {
        $domain .= substr($turi, 0, -1);
    } else {
        $domain .= str_replace($turiq, '', $turi);
    }
    return $domain;
}

/**
 * Check to see if this request was made via XMLHttpRequest.
 * Uses variables supported by most JS frameworks.
 *
 * @return boolean Whether the request was made via AJAX or not.
 **/

function isAjax()
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

function getRemoteFileSize(string $url)
{
    // Retrieve headers
    if (strlen($url) < 8) {
        return 0; // no file
    }
    $is_ssl = false;
    if (substr($url, 0, 7) == 'http://') {
        $port = 80;
    } else if (substr($url, 0, 8) == 'https://' && extension_loaded('openssl')) {
        $port = 443;
        $is_ssl = true;
    } else {
        return 0; // bad protocol
    }
    // Break up url
    $url_parts = explode('/', $url);
    $host = $url_parts[2];
    unset($url_parts[2]);
    unset($url_parts[1]);
    unset($url_parts[0]);
    $path = '/' . implode('/', $url_parts);
    if (strpos($host, ':') !== false) {
        $host_parts = explode(':', $host);
        if (count($host_parts) == 2 && ctype_digit($host_parts[1])) {
            $port = (int)$host_parts[1];
            $host = $host_parts[0];
        } else {
            return 0; // malformed host
        }
    }
    $request =
        "HEAD {$path} HTTP/1.1\r\n" . "Host: {$host}\r\n"
        . "Connection: Close\r\n\r\n";
    $fh = fsockopen(($is_ssl ? 'ssl://' : '') . $host, $port);
    if ($fh === false) {
        return 0;
    }
    fwrite($fh, $request);
    $headers = array();
    $total_loaded = 0;
    while (!feof($fh) && $line = fgets($fh, 1024)) {
        if ($line == "\r\n") {
            break;
        }
        if (strpos($line, ':') !== false) {
            list($key, $val) = explode(':', $line, 2);
            $headers[strtolower($key)] = trim($val);
        } else {
            $headers[] = strtolower($line);
        }
        $total_loaded += strlen($line);
        if ($total_loaded > 50000) {
            // Stop loading garbage!
            break;
        }
    }
    fclose($fh);
    if (!isset($headers['content-length'])) {
        return 0;
    }
    return (int)$headers['content-length'];
}
//Please use $api->game->addLog(); instead
function addLog(int $user, string $logtype, string $input)
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

function randomNumber(int $min = PHP_INT_MIN, int $max = PHP_INT_MAX)
{
    return random_int($min, $max);
}

/*
	Creates a dropdown for smelting recipes.
*/
function dropdownBlacksmith(string $dropdownname = 'smelt', int $selected = -1)
{
    global $db, $api;
    $ret = "<select name='$dropdownname' class='custom-select' type='dropdown'>";
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
        $itemname = $api->game->getItemNameFromID($r['smelt_output']);
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
function getCachedFile(string $url, string $file, int $hours = 1)
{
    $current_time = time();
    $expire_time = $hours * 60 * 60;
    if (file_exists($file)) {
        $file_time = filemtime($file);
        if ($current_time - $expire_time < $file_time) {
            return file_get_contents($file);
        } else {
            $content = updateFile($url, $file);
            file_put_contents($file, $content);
            return $content;
        }
    } else {
        $content = updateFile($url, $file);
        file_put_contents($file, $content);
        return $content;
    }
}

/* 
	Gets content from a URL via curl 
*/
function updateFile(string $url)
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
function recacheTopic(int $topic)
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
function recacheForum(int $forum)
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

function isImage(string $url)
{
    $params = array('http' => array(
        'method' => 'HEAD'
    ));
    $ctx = stream_context_create($params);
    $fp = @fopen($url, 'rb', false, $ctx);
    if (!$fp)
        return false;  // Problem with url

    $meta = stream_get_meta_data($fp);
    if ($meta === false) {
        fclose($fp);
        return false;  // Problem reading data from url
    }

    $wrapper_data = $meta["wrapper_data"];
    if (is_array($wrapper_data)) {
        foreach (array_keys($wrapper_data) as $hh) {
            if (substr($wrapper_data[$hh], 0, 19) == "Content-Type: image") // strlen("Content-Type: image") == 19
            {
                fclose($fp);
                return true;
            }
        }
    }

    fclose($fp);
    return false;
}

/*
 * Function to fetch current version of Chivalry Engine
 */
function getEngineVersion(string $url = 'https://raw.githubusercontent.com/MasterGeneral156/Version/master/chivalry-engine.json')
{
    global $set;
    $engine_version = $set['Version_Number'];
    $json = json_decode(getCachedFile($url, __DIR__ . "/cache/update_check.txt"), true);
    if (is_null($json))
        return "Update checker failed.";
    if (version_compare($engine_version, $json['latest-v2']) == 0 || version_compare($engine_version, $json['latest']) == 1)
        return "Chivalry Engine is up to date.";
    else
        return "Chivalry Engine update available. Download it <a href='{$json['download-latest']}'>here</a>.";
}

/**
 * Constructs a drop-down listbox of all the items in the user's inventory to let the user select one.
 * @param string $dropdownname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the forum which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first forum alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function dropdownInventory(string $dropdownname = "item", int $selected = -1)
{
    global $db, $userid;
    $ret = "<select name='$dropdownname' type='dropdown' class='custom-select'>";
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
 * @param string $dropdownname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the job which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first job alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function dropdownJob(string $dropdownname = "job", int $selected = -1)
{
    global $db;
    $ret = "<select name='$dropdownname' class='custom-select' type='dropdown'>";
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
 * @param string $dropdownname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the job rank which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first job's first job rank alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function dropdownJobRank(string $dropdownname = "jobrank", int $selected = -1)
{
    global $db;
    $ret = "<select name='$dropdownname' class='custom-select' type='dropdown'>";
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

function pagination(int $perpage, int $total, int $currentpage, string $url)
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
 * @param string $dropdownname The "name" attribute the <select> attribute should have
 * @param int $selected [optional] The ID Number of the forum which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first forum alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function dropdownArmory(string $dropdownname = "item", int $selected = -1)
{
    global $db, $ir;
    $ret = "<select name='$dropdownname' type='dropdown' class='custom-select'>";
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