<?php
/*
	Parses the time since the timestamp given.
	@param int $time_stamp for time since.
*/
function DateTime_Parse($time_stamp)
{
    $time_difference = ($_SERVER['REQUEST_TIME'] - $time_stamp);
    $unit =
            array('second', 'minute', 'hour', 'day', 'week', 'month', 'year');
    $lengths = array(60, 60, 24, 7, 4.35, 12);
    for ($i = 0; $time_difference >= $lengths[$i]; $i++)
    {
        $time_difference = $time_difference / $lengths[$i];
    }
    $time_difference = round($time_difference);
    $date =
            $time_difference . ' ' . $unit[$i]
                    . (($time_difference > 1 OR $time_difference < 1) ? 's'
                            : '') . ' ago';
    return $date;
}
/*
	The function for testing a link is a valid image
	@param text $url The link to test for.
	Major thanks to MagicTallGuy!
	http://www.makewebgames.com/member/53425-magictallguy
*/

function isImage($url = null) {
    $url = filter_var($url, FILTER_VALIDATE_URL) ? filter_var($url, FILTER_SANITIZE_URL) : null;
    if(empty($url))
        return false;
    $params = ['http' => ['method' => 'HEAD']];
    $ctx = stream_context_create($params);
    $fp = @checkRemoteFile($url);
    if(!$fp)
        return false;  // Problem with url
    $meta = stream_get_meta_data($fp);
    if ($meta === false) {
        fclose($fp);
        return false;  // Problem reading data from url
    }
    $wrapper_data = $meta['wrapper_data'];
    if(is_array($wrapper_data))
        foreach(array_keys($wrapper_data) as $hh)
            if (substr($wrapper_data[$hh], 0, 19) == "Content-Type: image") {
                fclose($fp);
                return true;
            }
    fclose($fp);
    return false;
}
/*

	The function for testing if a player is in the hospital.
	@param int $user The user who to test for.
*/

function user_infirmary($user)
{
	global $db;
	$CurrentTime=time();
	$query=$db->query("SELECT `infirmary_user` FROM `infirmary` WHERE `infirmary_user` = {$user} AND `infirmary_out` > {$CurrentTime}");
	if ($db->num_rows($query) == 0)
	{
		return false;
	}
	else
	{
		return true;
	}
}
/*
	The function for testing if a player is in the dungeon.
	@param int $user The user who to test for.
*/
function user_dungeon($user)
{
	global $db;
	$CurrentTime=time();
	$query=$db->query("SELECT `dungeon_user` FROM `dungeon` WHERE `dungeon_user` = {$user} AND `dungeon_out` > {$CurrentTime}");
	if ($db->num_rows($query) == 0)
	{
		return false;
	}
	else
	{
		return true;
	}
}
/*
	The function for putting/adding onto someone's infirmary time.
	@param int $user The user to put in the infirmary
	@param int $time The time (in minutes) to add.
	@param text $reason The reason the user is in the infirmary.
*/
function put_infirmary($user,$time,$reason)
{
	global $db;
	$CurrentTime=time();
	$Infirmary=$db->fetch_single($db->query("SELECT `infirmary_out` FROM `infirmary` WHERE `infirmary_user` = {$user}"));
	$TimeMath=$time*60;
	if ($Infirmary <= $CurrentTime)
	{
		$db->query("UPDATE `infirmary` SET `infirmary_out` = {$CurrentTime} + {$TimeMath}, `infirmary_in` = {$CurrentTime}, `infirmary_reason` = '{$reason}'  WHERE `infirmary_user` = {$user}");
	}
	else
	{
		$db->query("UPDATE `infirmary` SET `infirmary_out` = `infirmary_out` + {$TimeMath}, `infirmary_reason` = '{$reason}' WHERE `infirmary_user` = {$user}");
	}
}
/*
	The function for removing someone's infirmary time.
	@param int $user The user to put in the infirmary
	@param int $time The time (in minutes) to remove.
*/
function remove_infirmary($user,$time)
{
	global $db;
	$TimeMath=$time*60;
	$db->query("UPDATE `infirmary` SET `infirmary_out` = `infirmary_out` - '{$TimeMath}' WHERE `infirmary_user` = {$user}");
}
/*
	The function for putting/adding onto someone's dungeon time.
	@param int $user The user to put in the dungeon
	@param int $time The time (in minutes) to add.
	@param text $reason The reason the user is in the dungeon.
*/
function put_dungeon($user,$time,$reason)
{
	global $db;
	$CurrentTime=time();
	$Dungeon=$db->fetch_single($db->query("SELECT `dungeon_out` FROM `dungeon` WHERE `dungeon_user` = {$user}"));
	$TimeMath=$time*60;
	if ($Dungeon <= $CurrentTime)
	{
		$db->query("UPDATE `dungeon` SET `dungeon_out` = {$CurrentTime} + {$TimeMath}, `dungeon_in` = {$CurrentTime}, `dungeon_reason` = '{$reason}'");
	}
	else
	{
		$db->query("UPDATE `dungeon` SET `dungeon_out` = `dungeon_out` + {$TimeMath}, `dungeon_reason` = '{$reason}'");
	}
}
/*
	The function for removing someone's infirmary time.
	@param int $user The user to put in the infirmary
	@param int $time The time (in minutes) to remove.
*/
function remove_dungeon($user,$time)
{
	global $db;
	$TimeMath=$time*60;
	$db->query("UPDATE `dungeon` SET `dungeon_out` = `dungeon_out` - '{$TimeMath}' WHERE `dungeon_user` = {$user}");
}
/*
	The function for testing for a valid email.
	@param text $email The email to test for.
*/
function valid_email($email)
{
    return (filter_var($email, FILTER_VALIDATE_EMAIL) === $email);
}

/*
	The function for inputting a training log entry
	@param int $user The user who trained
	@param text $stat The stat trained
	@param int $gain The amount the user gained.
*/
function traininglog_add($user,$stat,$gain)
{
	global $db;
	$time=time();
	$db->query("INSERT INTO `logs_training` 
	(`log_user`, `log_stat`, `log_gain`, `log_time`) 
	VALUES ('{$user}', '{$stat}', '{$gain}', '{$time}');");
}

/**
 * Constructs a drop-down listbox of all the item types in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the item type which should be selected by default.<br />
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
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['itmtypeid']}'";
        if ($selected == $r['itmtypeid'] || $first == 0)
        {
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
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the item which should be selected by default.<br />
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
    if ($selected < 1)
    {
        $ret .= "<option value='0' selected='selected'>-- None --</option>";
    }
    else
    {
        $ret .= "<option value='0'>-- None --</option>";
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['itmid']}'";
        if ($selected == $r['itmid'])
        {
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
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the item which should be selected by default.<br />
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
    if ($selected < 1)
    {
        $ret .= "<option value='0' selected='selected'>-- None --</option>";
    }
    else
    {
        $ret .= "<option value='0'>-- None --</option>";
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['itmid']}'";
        if ($selected == $r['itmid'])
        {
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
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the item which should be selected by default.<br />
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
    if ($selected < 1)
    {
        $ret .= "<option value='0' selected='selected'>-- None --</option>";
    }
    else
    {
        $ret .= "<option value='0'>-- None --</option>";
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['itmid']}'";
        if ($selected == $r['itmid'])
        {
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
 * Constructs a drop-down listbox of all the locations in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the location which should be selected by default.<br />
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
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['town_id']}'";
        if ($selected == $r['town_id'] || $first == 0)
        {
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
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the shop which should be selected by default.<br />
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
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['shopID']}'";
        if ($selected == $r['shopID'] || $first == 0)
        {
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
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the user who should be selected by default.<br />
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
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['userid']}'";
        if ($selected == $r['userid'] || $first == 0)
        {
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
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the bot who should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first bot alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function challengebot_dropdown($ddname = "bot", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
            $db->query(
                    "SELECT `u`.`userid`, `u`.`username`
                     FROM `challengebots` AS `cb`
                     INNER JOIN `users` AS `u`
                     ON `cb`.`cb_npcid` = `u`.`userid`
                     ORDER BY `u`.`userid` ASC");
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['userid']}'";
        if ($selected == $r['userid'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['username']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the users in federal jail in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the user who should be selected by default.<br />
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
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['userid']}'";
        if ($selected == $r['userid'] || $first == 0)
        {
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
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the user who should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first user alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function mailb_user_dropdown($ddname = "user", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
            $db->query(
                    "SELECT `userid`, `username`
                     FROM `users`
                     WHERE `mailban` > 0
                     ORDER BY `userid` ASC");
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['userid']}'";
        if ($selected == $r['userid'] || $first == 0)
        {
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
 * Constructs a drop-down listbox of all the forum banned users in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the user who should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first user alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function forumb_user_dropdown($ddname = "user", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
            $db->query(
                    "SELECT `userid`, `username`
                     FROM `users`
                     WHERE `forumban` > 0
                     ORDER BY `userid` ASC");
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['userid']}'";
        if ($selected == $r['userid'] || $first == 0)
        {
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
 * Constructs a drop-down listbox of all the jobs in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
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
                    "SELECT `jID`, `jNAME`
    				 FROM `jobs`
    				 ORDER BY `jNAME` ASC");
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['jID']}'";
        if ($selected == $r['jID'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['jNAME']}</option>";
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
                    "SELECT `jrID`, `jNAME`, `jrNAME`
                     FROM `jobranks` AS `jr`
                     INNER JOIN `jobs` AS `j`
                     ON `jr`.`jrJOB` = `j`.`jID`
                     ORDER BY `j`.`jNAME` ASC, `jr`.`jrNAME` ASC");
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['jrID']}'";
        if ($selected == $r['jrID'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['jNAME']} - {$r['jrNAME']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the houses in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the house which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first house alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function house_dropdown($ddname = "house", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
            $db->query(
                    "SELECT `hID`, `hNAME`
    				 FROM houses
    				 ORDER BY `hNAME` ASC");
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['hID']}'";
        if ($selected == $r['hID'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['hNAME']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the houses in the game to let the user select one.<br />
 * However, the values in the list box return the house's maximum will value instead of its ID.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the house which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first house alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function house2_dropdown($ddname = "house", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
            $db->query(
                    "SELECT `hWILL`, `hNAME`
    				 FROM houses
    				 ORDER BY `hNAME` ASC");
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['hWILL']}'";
        if ($selected == $r['hWILL'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['hNAME']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the courses in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the course which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first course alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function course_dropdown($ddname = "course", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
            $db->query(
                    "SELECT `crID`, `crNAME`
    				 FROM `courses`
    				 ORDER BY `crNAME` ASC");
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['crID']}'";
        if ($selected == $r['crID'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['crNAME']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the crimes in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the crime which should be selected by default.<br />
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
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['crimeID']}'";
        if ($selected == $r['crimeID'] || $first == 0)
        {
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
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the crime group which should be selected by default.<br />
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
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['cgID']}'";
        if ($selected == $r['cgID'] || $first == 0)
        {
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
 * Sends a user an event, given their ID and the text.
 * @param int $userid The user ID to be sent the event
 * @param string $text The event's text. This should be fully sanitized for HTML, but not pre-escaped for database insertion.
 * @return int 1
 */
function event_add($userid, $text)
{
    global $db;
    $text = $db->escape($text);
    $db->query(
            "INSERT INTO `notifications`
             VALUES(NULL, $userid, " . time() . ", 'unread', '$text')");
    return 1;
}
/*
Internal Function: Used to make sure users do not have more energy/brave/hp/etc. than their level allows.
*/
function check_data()
{
	global $db,$ir,$userid;
	if ($ir['energy'] > $ir['maxenergy'])
	{
		$db->query("UPDATE `users` SET `energy` = `maxenergy` WHERE `userid` = {$userid}");
	}
	if ($ir['hp'] > $ir['maxhp'])
	{
		$db->query("UPDATE `users` SET `hp` = `maxhp` WHERE `userid` = {$userid}");
	}
	if ($ir['hp'] > $ir['maxhp'])
	{
		$db->query("UPDATE `users` SET `hp` = `maxhp` WHERE `userid` = {$userid}");
	}
}
/**
 * Internal function: used to see if a user is due to level up, and if so, perform that levelup.
 */
function check_level()
{
    global $ir, $c, $userid, $db, $lang;
    $ir['xp_needed'] = round(($ir['level'] + 3) * ($ir['level'] + 3) * ($ir['level'] + 3) * 2.5);
    if ($ir['xp'] >= $ir['xp_needed'])
    {
        $expu = $ir['xp'] - $ir['xp_needed'];
        $ir['level'] += 1;
        $ir['xp'] = $expu;
        $ir['energy'] += 4;
        $ir['brave'] += 4;
        $ir['maxenergy'] += 4;
        $ir['maxbrave'] += 4;
        $ir['hp'] += 50;
        $ir['maxhp'] += 50;
        $ir['xp_needed'] = round(($ir['level'] + 3) * ($ir['level'] + 3) * ($ir['level'] + 3) * 2.5);
        $db->query("UPDATE `users`  SET `level` = `level` + 1, `xp` = '{$expu}', `energy` = `energy` + 4, `brave` = `brave` + 4,
                 `maxenergy` = `maxenergy` + 4, `maxbrave` = `maxbrave` + 4, `hp` = `hp` + 50, `maxhp` = `maxhp` + 50
                 WHERE `userid` = {$userid}");
		$StatGain=round(($ir['level']*500)/mt_rand(2,6));
		$StatGainFormat=number_format($StatGain);
		if ($ir['class'] == 'Warrior')
		{
			$Stat='strength';
		}
		elseif ($ir['class'] == 'Rogue')
		{
			$Stat='agility';
		}
		else
		{
			$Stat='guard';
		}
		$db->query("UPDATE `userstats` SET `{$Stat}` = `{$Stat}` + {$StatGain} WHERE `userid` = {$userid}");
		event_add($userid, "You have successfully leveled up to level {$ir['level']} and gained {$StatGainFormat} in {$Stat}.");
    }
}

/**
 * Get the "rank" a user has for a particular stat - if the return is n, then the user has the n'th highest value for that stat.
 * @param int $stat The value of the current user's stat.
 * @param string $mykey The stat to be ranked in. Must be a valid column name in the userstats table
 * @return integer The user's rank in the stat
 */
function get_rank($stat, $mykey)
{
    global $db;
    global $ir, $userid, $c;
    $q =
            $db->query(
                    "SELECT count(`u`.`userid`)
                    FROM `userstats` AS `us`
                    LEFT JOIN `users` AS `u`
                    ON `us`.`userid` = `u`.`userid`
                    WHERE {$mykey} > {$stat}
                    AND `us`.`userid` != {$userid} AND `u`.`user_level` != 'Admin' AND `u`.`user_level` != 'NPC'");
    $result = $db->fetch_single($q) + 1;
    $db->free_result($q);
    return $result;
}

/**
 * Give a particular user a particular quantity of some item.
 * @param int $user The user ID who is to be given the item
 * @param int $itemid The item ID which is to be given
 * @param int $qty The item quantity to be given
 * @param int $notid [optional] If specified and greater than zero, prevents the item given's<br />
 * database entry combining with inventory id $notid.
 */
function item_add($user, $itemid, $qty, $notid = 0)
{
    global $db;
    if ($notid > 0)
    {
        $q =
                $db->query(
                        "SELECT `inv_id`
                         FROM `inventory`
                         WHERE `inv_userid` = {$user}
                         AND `inv_itemid` = {$itemid}
                         AND `inv_id` != {$notid}
                         LIMIT 1");
    }
    else
    {
        $q =
                $db->query(
                        "SELECT `inv_id`
                         FROM `inventory`
                         WHERE `inv_userid` = {$user}
                         AND `inv_itemid` = {$itemid}
                         LIMIT 1");
    }
    if ($db->num_rows($q) > 0)
    {
        $r = $db->fetch_row($q);
        $db->query(
                "UPDATE `inventory`
                SET `inv_qty` = `inv_qty` + {$qty}
                WHERE `inv_id` = {$r['inv_id']}");
    }
    else
    {
        $db->query(
                "INSERT INTO `inventory`
                 (`inv_itemid`, `inv_userid`, `inv_qty`)
                 VALUES ({$itemid}, {$user}, {$qty})");
    }
    $db->free_result($q);
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
    $q =
            $db->query(
                    "SELECT `inv_id`, `inv_qty`
                     FROM `inventory`
                     WHERE `inv_userid` = {$user}
                     AND `inv_itemid` = {$itemid}
                     LIMIT 1");
    if ($db->num_rows($q) > 0)
    {
        $r = $db->fetch_row($q);
        if ($r['inv_qty'] > $qty)
        {
            $db->query(
                    "UPDATE `inventory`
                     SET `inv_qty` = `inv_qty` - {$qty}
                     WHERE `inv_id` = {$r['inv_id']}");
        }
        else
        {
            $db->query(
                    "DELETE FROM `inventory`
            		 WHERE `inv_id` = {$r['inv_id']}");
        }
    }
    $db->free_result($q);
}

/**
 * Constructs a drop-down listbox of all the forums in the game to let the user select one.
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the forum w hich should be selected by default.<br />
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
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['ff_id']}'";
        if ($selected == $r['ff_id'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['ff_name']}1</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Constructs a drop-down listbox of all the forums in the game, except gang forums, to let the user select one.<br />
 * @param string $ddname The "name" attribute the &lt;select&gt; attribute should have
 * @param int $selected [optional] The <i>ID number</i> of the forum which should be selected by default.<br />
 * Not specifying this or setting it to -1 makes the first forum alphabetically be selected.
 * @return string The HTML code for the listbox, to be inserted in a form.
 */
function forum2_dropdown($ddname = "forum", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
            $db->query(
                    "SELECT `ff_id`, `ff_name`
                     FROM `forum_forums`
                     WHERE `ff_auth` != 'gang'
                     ORDER BY `ff_name` ASC");
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $ret .= "\n<option value='{$r['ff_id']}'";
        if ($selected == $r['ff_id'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$r['ff_name']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}

/**
 * Records an action by a member of staff in the central staff log.
 * @param string $text The log's text. This should be fully sanitized for HTML, but not pre-escaped for database insertion.
 */
function stafflog_add($text)
{
    global $db, $ir;
    $IP = $db->escape($_SERVER['REMOTE_ADDR']);
    $text = $db->escape($text);
    $db->query(
            "INSERT INTO `stafflogs`
             VALUES(NULL, {$ir['userid']}, " . time() . ", '$text', '$IP')");
}

/**
 * Request that an anti-CSRF verification code be issued for a particular form in the game.
 * @param string $formid A unique string used to identify this form to match up its submission with the right token.
 * @return string The code issued to be added to the form.
 */
function request_csrf_code($formid)
{
    // Generate the token
	$time=time();
	$token=hash('sha512',(openssl_random_pseudo_bytes(32)));
    // Insert/Update it
    $_SESSION["csrf_{$formid}"] =
            array('token' => $token, 'issued' => $time);
    return $token;
}

/**
 * Request that an anti-CSRF verification code be issued for a particular form in the game, and return the HTML to be placed in the form.
 * @param string $formid A unique string used to identify this form to match up its submission with the right token.
 * @return string The HTML for the code issued to be added to the form.
 */
function request_csrf_html($formid)
{
    return "<input type='hidden' name='verf' value='"
            . request_csrf_code($formid) . "' />";
}

/**
 * Check the CSRF code we received against the one that was registered for the form - return false if the request shouldn't be processed...
 * @param string $formid A unique string used to identify this form to match up its submission with the right token.
 * @param string $code The code the user's form input returned.
 * @return boolean Whether the user provided a valid code or not
 */
function verify_csrf_code($formid, $code)
{
    // Lookup the token entry
    // Is there a token in existence?
    if (!isset($_SESSION["csrf_{$formid}"])
            || !is_array($_SESSION["csrf_{$formid}"]))
    {
        // Obviously verification fails
        return false;
    }
    else
    {
        // From here on out we always want to remove the token when we're done - so don't return immediately
        $verified = false;
        $token = $_SESSION["csrf_{$formid}"];
        // Expiry time on a form?
        $expiry = 300; // hacky lol
        if ($token['issued'] + $expiry > time())
        {
            // It's ok, check the contents
            $verified = ($token['token'] === $code);
        } // don't need an else case - verified = false
        // Remove the token before finishing
        unset($_SESSION["csrf_{$formid}"]);
        return $verified;
    }
}

/**
 * Given a password input given by the user and their actual details,
 * determine whether the password entered was correct.
 *
 * @param string $input The input password given by the user.
 * 						Should be without slashes.
 * @param string $pass	The user's encrypted password
 *
 * @return boolean	true for equal, false for not (login failed etc)
 *
 */
function verify_user_password($input, $pass)
{
	global $set;
	$pw=sha1($input);
    if (password_verify($pw, $pass)) 
	{
		return true;
	} 
	else 
	{
		return false;
	}
}

/**
 * Given a password and a salt, encode them to the form which is stored in
 * the game's database.
 *
 * @param string $password 		The password to be encoded
 *
 * @return string	The resulting encoded password.
 */
function encode_password($password)
{
    global $set;
		$options = [
		'cost' => $set['Password_Effort'],
		];
		return password_hash(sha1($password), PASSWORD_BCRYPT, $options);
}

/**
Easily outputs an alert to the client.
Acceptable "type" is success, info, warning, danger.
You can input whatever for the text
*/

function alert($type,$title,$text)
{
	echo "<div class='alert alert-{$type}'>
  <strong>{$title}</strong> {$text}
</div>";
}

/**
 *
 * @return string The URL of the game.
 */
function determine_game_urlbase()
{
    $domain = $_SERVER['HTTP_HOST'];
    $turi = $_SERVER['REQUEST_URI'];
    $turiq = '';
    for ($t = strlen($turi) - 1; $t >= 0; $t--)
    {
        if ($turi[$t] != '/')
        {
            $turiq = $turi[$t] . $turiq;
        }
        else
        {
            break;
        }
    }
    $turiq = '/' . $turiq;
    if ($turiq == '/')
    {
        $domain .= substr($turi, 0, -1);
    }
    else
    {
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

function is_ajax()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && is_string($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])
                    === 'xmlhttprequest';
}

/**
 * Get the file size in bytes of a remote file, if we can.
 *
 * @param string $url	The url to the file
 *
 * @return int			The file's size in bytes, or 0 if we could
 * 						not determine its size.
 */

function get_filesize_remote($url)
{
    // Retrieve headers
    if (strlen($url) < 8)
    {
        return 0; // no file
    }
    $is_ssl = false;
    if (substr($url, 0, 7) == 'http://')
    {
        $port = 80;
    }
    else if (substr($url, 0, 8) == 'https://' && extension_loaded('openssl'))
    {
        $port = 443;
        $is_ssl = true;
    }
    else
    {
        return 0; // bad protocol
    }
    // Break up url
    $url_parts = explode('/', $url);
    $host = $url_parts[2];
    unset($url_parts[2]);
    unset($url_parts[1]);
    unset($url_parts[0]);
    $path = '/' . implode('/', $url_parts);
    if (strpos($host, ':') !== false)
    {
        $host_parts = explode(':', $host);
        if (count($host_parts) == 2 && ctype_digit($host_parts[1]))
        {
            $port = (int) $host_parts[1];
            $host = $host_parts[0];
        }
        else
        {
            return 0; // malformed host
        }
    }
    $request =
            "HEAD {$path} HTTP/1.1\r\n" . "Host: {$host}\r\n"
                    . "Connection: Close\r\n\r\n";
    $fh = fsockopen(($is_ssl ? 'ssl://' : '') . $host, $port);
    if ($fh === false)
    {
        return 0;
    }
    fwrite($fh, $request);
    $headers = array();
    $total_loaded = 0;
    while (!feof($fh) && $line = fgets($fh, 1024))
    {
        if ($line == "\r\n")
        {
            break;
        }
        if (strpos($line, ':') !== false)
        {
            list($key, $val) = explode(':', $line, 2);
            $headers[strtolower($key)] = trim($val);
        }
        else
        {
            $headers[] = strtolower($line);
        }
        $total_loaded += strlen($line);
        if ($total_loaded > 50000)
        {
            // Stop loading garbage!
            break;
        }
    }
    fclose($fh);
    if (!isset($headers['content-length']))
    {
        return 0;
    }
    return (int) $headers['content-length'];
}
function recache_forum($forum)
{
    global $ir, $c, $userid, $h, $bbc, $db;
}

function recache_topic($topic)
{
    global $ir, $c, $userid, $h, $bbc, $db;
}

/* gets the contents of a file if it exists, otherwise grabs and caches */
function get_fg_cache($file,$ip,$hours = 1) 
{
	$current_time = time(); 
	$expire_time = $hours * 60 * 60;
	if(file_exists($file))
	{
		$file_time = filemtime($file);
		if ($current_time - $expire_time < $file_time)
		{
			return file_get_contents($file);
		}
		else
		{
			$content = update_fg_info($ip);
			file_put_contents($file,$content);
			return $content;
		}
	}
	else 
	{
		$content = update_fg_info($ip);
		file_put_contents($file,$content);
		return $content;
	}
}

/* gets content from a URL via curl */
function update_fg_info($ip) {
	global $db,$set;
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => "https://api.fraudguard.io/ip/$ip",
		CURLOPT_USERPWD => "{$set['FGUsername']}:{$set['FGPassword']}",
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_RETURNTRANSFER => true));
	$content = curl_exec($curl);
	curl_close($curl);
	return $content;
}

/**
 * Tests to see if the user's permission is allowed or not.
 *
 * @param string $perm		The permission to test for
 * @param int $user			The user to test on
 *
 * @return bool			Returns true if the user has this,
 *						false if not.
 */
 function permission($perm,$user)
 {
	 global $db;
	 $Query=$db->query("SELECT `perm_disable` FROM `permissions` WHERE `perm_name` = '{$perm}' AND `perm_user` = {$user}");
	 if ($db->num_rows($Query) == 0)
	 {
		 return true;
	 }
	 $q=$db->fetch_single($Query);
	 if ($q == 'true')
	 {
		 //User does not have this permission
		 return false;
	 }
	 else
	 {
		 //User does have this permission
		 return true;
	 }
	 
 }