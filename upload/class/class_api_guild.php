<?php
/*
	File: 		class/class_api_guild.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Numerous API calls relating to guilds.
	Author: 	TheMasterGeneral
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
if (!defined('MONO_ON')) {
    exit;
}

class guild
{
	/*
     * API to remove an item from a guild.
     * @param int guild = Guild ID to remove the item from.
     * @param int item = Item ID to remove.
     * @param int qty = Quantity of item to remove.
     * Returns true if item successfully removed.
     * Returns false if item failed to be taken away.
     */
    function takeItem(int $guild, int $item, int $qty)
    {
        global $db;
        //Select $item's item name.
        $ie = $db->fetch_single($db->query("SELECT COUNT(`itmname`) FROM `items` WHERE `itmid` = {$item}"));
        //If $itemid actually exists, it'll return a name, so lets continue if that's the case.
        if ($ie > 0) {
            //Select the Armory ID number where $item's is stored for $guild.
            $q = $db->query("SELECT `gaID`, `gaQTY` FROM `guild_armory` WHERE `gaGUILD` = {$guild}
						 AND `gaITEM` = {$item} LIMIT 1");
            //Guild has an Armory ID for $item!
            if ($db->num_rows($q) > 0) {
                $r = $db->fetch_row($q);
                //$guild's $item quantity is greater than $qty, so remove only $qty and return true.
                if ($r['gaQTY'] > $qty) {
                    $db->query("UPDATE `guild_armory` SET `gaQTY` = `gaQTY` - {$qty} WHERE `gaID` = {$r['gaID']}");
                    return true;
                } //$guild's $item quantity is lower than $qty, so delete the Armory ID entirely and return true.
                else {
                    $db->query("DELETE FROM `guild_armory` WHERE `gaID` = {$r['gaID']}");
                    return true;
                }
            }
        }
        $db->free_result($q);
    }
	/*
     * API to give an item to a guild.
     * @param int guild = Guild ID to give the item to.
     * @param int item = Item ID to give to the guild.
     * @param int qty = Quantity of item to give to the guild.
     * Returns true if item successfully given to the guild.
     * Returns false if item failed to be given to guild.
     */
    function giveItem(int $guild, int $item, int $qty)
    {
        global $db;
        //Select $item's item name.
        $ie = $db->fetch_single($db->query("SELECT COUNT(`itmname`) FROM `items` WHERE `itmid` = {$item}"));
        //If the name returns, continue
        if ($ie > 0) {
            $q = $db->query("SELECT `gaID` FROM `guild_armory` WHERE `gaGUILD` = {$guild} AND `gaITEM` = {$item}
            LIMIT 1");
            //If the armory stack exists, add $qty to it and return true to signify we succeeded at adding the item.
            if ($db->num_rows($q) > 0) {
                $r = $db->fetch_row($q);
                $db->query("UPDATE `guild_armory` SET `gaQTY` = `gaQTY` + {$qty} WHERE `gaID` = {$r['gaID']}");
                return true;
            } //The armory item id does not exist, so lets create a new one and return true.
            else {
                $db->query("INSERT INTO `guild_armory` (`gaITEM`, `gaGUILD`, `gaQTY`) VALUES ({$item}, {$guild}, {$qty})");
                return true;
            }
        }
    }
	/*
        Function to add a guild notification to a guild.
        @param int guild_id = ID of the guild you wish to add a notification to.
        @param text notification = Notification text.
        Returns true if the notification was added successfully, false otherwise.
    */
    function addNotification(int $guild_id, string $notification)
    {
        global $db;
        $notification = $db->escape(stripslashes($notification));
        $time = time();
        $guild_id = (isset($guild_id) && is_numeric($guild_id)) ? abs(intval($guild_id)) : 0;
        if (isset($guild_id) && $guild_id > 0) {
            $cnt = $db->query("SELECT * FROM `guild` WHERE `guild_id` = {$guild_id}");
            if ($db->num_rows($cnt) > 0) {
                $db->query("INSERT INTO `guild_notifications` (`gn_id`, `gn_guild`, `gn_time`, `gn_text`) VALUES (NULL, '{$guild_id}', '{$time}', '{$notification}')");
                return true;
            }
        }
    }
	/*
        Function to fetch all or a specific field of information from the specified guild.
        @param int guild_id = Guild ID to fetch info from.
        @param text field = Data field to return. Optional. If left null/empty, will return all fields.
        Returns all fields if field is empty, otherwise it'll return a single field.
    */
    function fetchInfo(int $guild_id, string $field = null)
    {
        global $db;
        $guild_id = (isset($guild_id) && is_numeric($guild_id)) ? abs(intval($guild_id)) : 0;
        if (isset($guild_id) && $guild_id > 0) {
            $cnt = $db->query("SELECT * FROM `guild` WHERE `guild_id` = {$guild_id}");
            if ($db->num_rows($cnt) > 0) {
                if (is_null($field)) {
                    return $db->fetch_row($db->query("SELECT * FROM `guild` WHERE `guild_id` = {$guild_id}"));
                } else {
                    $field = $db->escape(stripslashes($field));
                    return $db->fetch_single($db->query("SELECT `{$field}` FROM `guild` WHERE `guild_id` = {$guild_id}"));
                }
            }
        }
    }
}