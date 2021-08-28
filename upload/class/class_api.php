<?php
/*
	File: class/class_api.php
	Created: 11/10/2016 at 1:34PM Eastern Time
	Info: Creates a class file to use as an API for modders
	who don't wish to use the main game code!
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
if (!defined('MONO_ON')) {
    exit;
}
//require_once 'lib/DiscordMsg/Msg.php';
//require_once 'lib/DiscordMsg/DiscordMsg.php';
class api
{
    /*
        Returns the API version.
    */
    function SystemReturnAPIVersion()
    {
        return "17.10.4";    //Last Updated 10/13/2017
    }

    /*
        Tests to see if specified user has at least the specified amount of money.
        @param int user = User ID to test for.
        @param int type = Currency type. [Ex. primary or secondary]
        @param int money = Minimum money requied.
        Returns true if user has more cash than required.
        Returns false if user does not exist or does not have the minimum cash requred.
    */
    function UserHasCurrency($user, $type, $minimum)
    {
        global $db;
        $user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
        $minimum = (isset($minimum) && is_numeric($minimum)) ? abs(intval($minimum)) : 0;
        $type = $db->escape(stripslashes(strtolower($type)));
        $userexist = $db->fetch_single($db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `userid` = {$user}"));
        if ($userexist) {
            if ($type == 'primary' || $type == 'secondary') {
                $UserMoney = $db->fetch_single($db->query("/*qc=on*/SELECT `{$type}_currency` FROM `users` WHERE `userid` = {$user}"));
                if ($UserMoney >= $minimum) {
                    return true;
                }
            }
        }
    }

    /*
        Gives the user the specified item and quantity
        @param int user = User ID to test for.
        @param int item = Item ID to give to the user.
        @param int quantity = Quantity of item to give to the user.
        Returns true if item successfully given to the user.
        Returns false if item failed to be given to user.
    */
    function UserGiveItem($user, $item, $quantity)
    {
        global $db;
        $user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
        $item = (isset($item) && is_numeric($item)) ? abs(intval($item)) : 0;
        $quantity = (isset($quantity) && is_numeric($quantity)) ? abs(intval($quantity)) : 0;
        if (item_add($user, $item, $quantity)) {
            return true;
        }
    }

    /*
        Removes an item from the user specified
        @param int user = User ID to test for.
        @param int item = Item ID to take from the user.
        @param int quantity = Quantity of item to remove from the user.
        Returns true if item successfully taken from the user.
        Returns false if item failed to be taken from user.
    */
    function UserTakeItem($user, $item, $quantity)
    {
        global $db;
        $user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
        $item = (isset($item) && is_numeric($item)) ? abs(intval($item)) : 0;
        $quantity = (isset($quantity) && is_numeric($quantity)) ? abs(intval($quantity)) : 0;
        if (item_remove($user, $item, $quantity)) {
            return true;
        }
    }

    /*
        Gives user specified amount of currency type.
        @param int user = User ID to give currency to.
        @param int type = Currency type. [Ex. primary and secondary]
        @param int money = Currency given.
        Returns true if user has received currency.
        Returns false if user does not receive currency.
    */
    function UserGiveCurrency($user, $type, $quantity)
    {
        global $db;
        $user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
        $type = $db->escape(stripslashes(strtolower($type)));
        $quantity = (isset($quantity) && is_numeric($quantity)) ? abs(intval($quantity)) : 0;
        $userexist = $db->fetch_single($db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `userid` = {$user}"));
        if ($userexist) {
            if ($type == 'primary' || $type == 'secondary') {
                $db->query("UPDATE `users` SET `{$type}_currency` = `{$type}_currency` + {$quantity} WHERE `userid` = {$user}");
                return true;
            }
        }
    }

    /*
        Takes qunatity of currency type from the user specified.
        @param int user = User ID to give currency to.
        @param int type = Currency type. [Ex. primary and secondary]
        @param int money = Currency given.
        Returns true if user has lost currency.
        Returns false if user does not lose any currency.
    */
    function UserTakeCurrency($user, $type, $quantity)
    {
        global $db;
        $user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
        $type = $db->escape(stripslashes(strtolower($type)));
        $quantity = (isset($quantity) && is_numeric($quantity)) ? abs(intval($quantity)) : 0;
        $userexist = $db->fetch_single($db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `userid` = {$user}"));
        if ($userexist) {
            if ($type == 'primary' || $type == 'secondary') {
                $db->query("UPDATE `users` SET `{$type}_currency` = `{$type}_currency` - {$quantity} WHERE `userid` = {$user}");
                $db->query("UPDATE `users` SET `{$type}_currency` = 0 WHERE `{$type}_currency` < 0");
                return true;
            }
        }
    }

    /*
        Tests to see what the user has equipped.
        @param int user = User ID to test against.
        @param int slot = Equipment slot to test. [Ex. Primary, Secondary, Armor]
        @param int itemid = Item to test for. -1 = Any Item, 0 = No Item Equipped, >0 = Specific item
        Returns true if user has item equipped
        Returns false if user does not have item equipped.
    */
    function UserEquippedItem($user, $slot, $itemid = -1)
    {
        global $db;
        $user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
        $slot = $db->escape(stripslashes(strtolower($slot)));
        if ($slot == 'primary' || $slot == 'secondary' || $slot == 'armor') {
            //Any item equipped
            if ($itemid == -1) {
                $equipped = $db->fetch_single($db->query("/*qc=on*/SELECT `equip_{$slot}` FROM `users` WHERE `userid` = {$user}"));
                if ($equipped > 0) {
                    return true;
                }
            } //Specific item equipped
            elseif ($itemid > 0) {
                $itemid = (isset($itemid) && is_numeric($itemid)) ? abs(intval($itemid)) : 0;
                $equipped = $db->fetch_single($db->query("/*qc=on*/SELECT `equip_{$slot}` FROM `users` WHERE `userid` = {$user}"));
                if ($equipped == $itemid) {
                    return true;
                }
            } //Nothing equipped
            elseif ($itemid == 0) {
                $equipped = $db->fetch_single($db->query("/*qc=on*/SELECT `equip_{$slot}` FROM `users` WHERE `userid` = {$user}"));
                if ($equipped == 0) {
                    return true;
                }
            }
        }
    }

    /*
        Tests the inputted user to see if they're in the dungeon or infirmary
        @param int user = User ID to test against.
        @param int status = Place to test. [Infirmary or dungeon]
        Returns true if user is in the dungeon/infirmary
        Returns false if user is not in the dungeon/infirmary
    */
    function UserStatus($user, $status)
    {
        global $db;
        $user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
        $status = $db->escape(stripslashes(strtolower($status)));
        if ($status == 'infirmary') {
            return user_infirmary($user);
        } elseif ($status == 'dungeon') {
            return user_dungeon($user);
        }
    }

    /*
        Places or removes dungeon/infirmary time on the specified user.
        @param int user = User ID to test against.
        @param int place = Place to test. [Ex. Dungeon and Infirmary]
        @param int time = Minutes user is in infirmary/dungeon.
        @param text reason = Reason why user is in the infirmary/dungeon.
        Returns true if user is placed in the infirmary/dungeon, or is removed from it.
        Returns false otherwise.
    */
    function UserStatusSet($user, $place, $time, $reason)
    {
        global $db;
        $user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
        $reason = $db->escape(stripslashes($reason));
        $place = $db->escape(stripslashes(strtolower($place)));
        if ($place == 'infirmary') {
            if ($time >= 0) {
                $time = (isset($time) && is_numeric($time)) ? abs(intval($time)) : 0;
                put_infirmary($user, $time, $reason);
                return true;
            } elseif ($time < 0) {
                $time = (isset($time) && is_numeric($time)) ? abs(intval($time)) : 0;
                remove_infirmary($user, $time);
                return true;
            }
        } elseif ($place == 'dungeon') {
            if ($time >= 0) {
                $time = (isset($time) && is_numeric($time)) ? abs(intval($time)) : 0;
                put_dungeon($user, $time, $reason);
                return true;
            } elseif ($time < 0) {
                $time = (isset($time) && is_numeric($time)) ? abs(intval($time)) : 0;
                remove_dungeon($user, $time);
                return true;
            }
        }
    }

    /*
        Adds a notification for the specified user.
        @param int user = User ID to send notification to.
        @param text text = Notification text.
        Returns true always.
    */
    function GameAddNotification($user, $text, $icon = '', $color = '')
    {
        notification_add($user, $text, $icon, $color);
        return true;
    }

    /*
        Adds an in-game message for the player specified.
        @param int user = User ID message is sent to.
        @param text subj = Message subject.
        @param text msg = Message text.
        @param int from = User ID message is from..
        Returns true when message is sent. False if message fails.
    */
    function GameAddMail($user, $subj, $msg, $from)
    {
        global $db;
        $user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
        $from = (isset($from) && is_numeric($from)) ? abs(intval($from)) : 0;
        $subj = $db->escape(stripslashes($subj));
        $msg = $db->escape(stripslashes($msg));
        $time = time();
        $userexist = $db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `userid` =  {$user}");
        if ($db->num_rows($userexist) > 0) {
            $db->free_result($userexist);
            $userexist = $db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `userid` =  {$from}");
            if ($db->num_rows($userexist) > 0) {
                $msg=encrypt_message($msg,$from,$user);
                $db->query("INSERT INTO `mail`
				(`mail_to`, `mail_from`, `mail_status`, `mail_subject`, `mail_text`, `mail_time`) 
				VALUES 
				('{$user}', '{$from}', 'unread', '{$subj}', '{$msg}', '{$time}');");
                return true;
            }
        }
    }

    /*
        Adds an in-game announcement.
        @param text text = Announcement text.
        @param int poster = User ID of poster. Optional. [Defaults = 1]
        Returns true when announcement is made. False if fail.
    */
    function GameAddAnnouncement($text, $poster = 1)
    {
        global $db;
        $text = $db->escape(str_replace("\n", "<br />", stripslashes($text)));
        $poster = (isset($poster) && is_numeric($poster)) ? abs(intval($poster)) : 1;
        $time = time();
        $userexist = $db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `userid` =  {$poster}");
        if ($db->num_rows($userexist) > 0) {
            $db->query("INSERT INTO `announcements`
			(`ann_text`, `ann_time`, `ann_poster`) 
			VALUES 
			('{$text}', '{$time}', '{$poster}');");
            $db->query("UPDATE `users` SET `announcements` = `announcements` + 1");
            return true;
        }
        $msg = new \AG\DiscordMsg("New Announcement from {$api->SystemUserIDtoName($poster)}: {$text}");
        $msg->send();
    }

    /*
        Get the user's member level. Can test for exact member level, or if user is above specified member level.
        @param int user = User to test on.
        @param text level = Member level to test for. [Valid: npc, member, web dev, forum moderator, assistant, admin]
        @param boolean exact = Return true if ranked ONLY specified level. [Default: false]
        Returns true if user is exactly or equal to/above specified member level. False if not.
    */

    //This function needs refactored ASAP
    function UserMemberLevelGet($user, $level, $exact = false)
    {
        global $db;
        $level = $db->escape(stripslashes(strtolower($level)));
        $user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
        if ($user > 0) {
            $userexist = $db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `userid` =  {$user}");
            if ($db->num_rows($userexist) > 0) {
                $ulevel = $db->fetch_single($db->query("/*qc=on*/SELECT `user_level` FROM `users` WHERE `userid` = {$user}"));
                if ($exact == true) {
                    if ($level == $ulevel) {
                        return true;
                    }
                } else {
                    if ($level == 'member') {
                        if ($ulevel == 'Member' || $ulevel == 'Forum Moderator' || $ulevel == 'Assistant'
                            || $ulevel == 'Web Developer' || $ulevel == 'Admin'
                        ) {
                            return true;
                        }
                    } elseif ($level == 'forum moderator') {
                        if ($ulevel == 'Forum Moderator' || $ulevel == 'Assistant' || $ulevel == 'Web Developer' || $ulevel == 'Admin') {
                            return true;
                        }
                    } elseif ($level == 'assistant') {
                        if ($ulevel == 'Assistant' || $ulevel == 'Web Developer' || $ulevel == 'Admin') {
                            return true;
                        }
                    } elseif ($level == 'web dev') {
                        if ($ulevel == 'Web Developer' || $ulevel == 'Admin') {
                            return true;
                        }
                    } elseif ($level == 'npc') {
                        if ($ulevel == 'Member' || $ulevel == 'NPC' || $ulevel == 'Forum Moderator' || $ulevel == 'Assistant'
                            || $ulevel == 'Web Developer' || $ulevel == 'Admin'
                        ) {
                            return true;
                        }
                    } elseif ($level == 'admin') {
                        if ($ulevel == 'Admin') {
                            return true;
                        }
                    }
                }
            }
        }
    }

    /*
        Test to see whether or not the specified user has the item and optionally, an amount of the item.
        @param int user = User to test on.
        @param int item = Item ID to test for.
        @param int qty = Quantity to test for. Optional. [Default: 1]
        Returns true if the user has the item and requried quantity. False if otherwise.

    */
    function UserHasItem($user, $item, $qty = 1)
    {
        global $db;
        $user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
        $item = (isset($item) && is_numeric($item)) ? abs(intval($item)) : 0;
        $qty = (isset($qty) && is_numeric($qty)) ? abs(intval($qty)) : 0;
        if ($user > 0 || $item > 0 || $qty > 0) {
            $i = $db->fetch_single($db->query("/*qc=on*/SELECT `inv_qty` FROM `inventory` WHERE `inv_userid` = {$user} && `inv_itemid` = {$item}"));
            if ($qty == 1) {
                if ($i >= 1) {
                    return true;
                }
            } else {
                if ($i >= $qty) {
                    return true;
                }
            }
        }
    }

    /*
        Returns the specified user's stat, optionally as a percent.
        @param int user = User to test on.
        @param text stat = User's table row to return.
        @param boolean percent = Return as a percent. [Default: false]
        Returns the value in the stat specified, optionally as a percent.
    Throws E_ERROR if attempting to edit a sensitive field (Such as passwords)

    */
    function UserInfoGet($user, $stat, $percent = false)
    {
        global $db;
        $user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
        $stat = $db->escape(stripslashes(strtolower($stat)));
        if (in_array($stat, array('password', 'email', 'lastip', 'loginip', 'registerip', 'personal_notes', 'staff_notes'))) {
            trigger_error("You do not have permission to get the {$stat} on this user.", E_ERROR);
        } else {
            if ($percent == true) {
                $min = $db->fetch_single($db->query("/*qc=on*/SELECT `{$stat}` FROM `users` WHERE `userid` = {$user}"));
                $max = $db->fetch_single($db->query("/*qc=on*/SELECT `max{$stat}` FROM `users` WHERE `userid` = {$user}"));
                return round($min / $max * 100);
            } else {
                return $db->fetch_single($db->query("/*qc=on*/SELECT `{$stat}` FROM `users` WHERE `userid` = {$user}"));
            }
        }
    }

    /*
        Set the specified user's stat to a value, optionally as a percent.
        @param int user = User to test on.
        @param text stat = User's table row to return.
        @param int change = Direction of change
        @param boolean percent = Return as a percent. [Default: false]
        Returns the value in the stat specified, optionally as a percent.
        Throws E_ERROR if attempting to edit a sensitive field (Such as passwords)

    */
    function UserInfoSet($user, $stat, $change, $percent = false, $fixMin = true)
    {
        global $db;
        $user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
        $stat = $db->escape(stripslashes(strtolower($stat)));
        if (in_array($stat, array('password', 'email', 'lastip', 'loginip', 'registerip', 'personal_notes', 'staff_notes'))) {
            trigger_error("You do not have permission to set the {$stat} on this user.", E_ERROR);
        } else {
            if ($change >= 1) {
                $change = (isset($change) && is_numeric($change)) ? abs(intval($change)) : 0;
                if ($percent == true) {
                    $maxstat = $db->fetch_single($db->query("/*qc=on*/SELECT `max{$stat}` FROM `users` WHERE `userid` = {$user}"));
                    $number = ($change / 100) * $maxstat;
                    $db->query("UPDATE users SET `{$stat}`=`{$stat}`+{$number} WHERE `{$stat}` < `max{$stat}` AND `userid` = {$user}");
                    if ($fixMin)
						$db->query("UPDATE users SET `{$stat}` = `max{$stat}` WHERE `{$stat}` > `max{$stat}`");
                    return true;
                } else {
                    $db->query("UPDATE users SET `{$stat}` = `{$stat}` + {$change} WHERE `userid` = {$user}");
					if ($fixMin)
						$db->query("UPDATE users SET `{$stat}` = `max{$stat}` WHERE `{$stat}` > `max{$stat}`");
                    return true;
                }
            } elseif ($change == 0) {
                $db->query("UPDATE users SET `{$stat}` = 0 WHERE `userid` = {$user}");
                return true;
            } else {
                $change = (isset($change) && is_numeric($change)) ? abs(intval($change)) : 0;
                if ($percent == true) {
                    $maxstat = $db->fetch_single($db->query("/*qc=on*/SELECT `max{$stat}` FROM `users` WHERE `userid` = {$user}"));
                    $number = ($change / 100) * $maxstat;
                    $db->query("UPDATE users SET `{$stat}` = `{$stat}` - {$number} WHERE `userid` = {$user}");
                    if ($fixMin)
						$db->query("UPDATE users SET `{$stat}` = 0 WHERE `{$stat}` < 0");
                    return true;
                } else {
                    $db->query("UPDATE users SET `{$stat}` = `{$stat}` - {$change} WHERE `userid` = {$user}");
                    if ($fixMin)
						$db->query("UPDATE users SET `{$stat}` = 0 WHERE `{$stat}` < 0");
                    return true;
                }
            }
        }
    }

    /*
        Adds an entry to the main logging data table.
        @param int user = User who is attached to this log.
        @param text logtype = Log type. Can be whatever. See available logs in staff panel for standardized names.
        @param text input = Text to be entered in the log.
    */
    function SystemLogsAdd($user, $logtype, $input)
    {
        global $db;
        $time = time();
        $IP = $db->escape($_SERVER['REMOTE_ADDR']);
        $user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
        $input = $db->escape(stripslashes($input));
        $logtype = $db->escape(stripslashes(strtolower($logtype)));
        $db->query("INSERT INTO `logs` (`log_id`, `log_type`, `log_user`, `log_time`, `log_text`, `log_ip`) VALUES (NULL, '{$logtype}', '{$user}', '{$time}', '{$input}', '{$IP}');");
    }

    /*
        Returns the username of the user id specified.
        @param int user = User's name we're trying to fetch.
        On success, returns the user id's name, on failure, it returns false.
    */
    function SystemUserIDtoName($user)
    {
        global $db;
        $user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
        $name = $db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `userid` = {$user}");
        if ($db->num_rows($name) > 0) {
            $username = $db->fetch_single($name);
            return $username;
        }
    }

    /*
        Returns the userid  of the username specified.
        @param string name = User's ID we're trying to fetch.
        On success, returns the user's id, on failure, it returns false.
    */
    function SystemUsernametoID($name)
    {
        global $db;
        $name = $db->escape(stripslashes($name));
        $id = $db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `username` = '{$name}'");
        if ($db->num_rows($id) > 0) {
            $usrid = $db->fetch_single($id);
            return $usrid;
        }
    }

    /*
        Returns the item name of the item id specified.
        @param int itemid = Item's name we're trying to fetch.
        On success, returns the item id's name, on failure, it returns false.
    */
    function SystemItemIDtoName($itemid)
    {
        global $db;
        $itemid = (isset($itemid) && is_numeric($itemid)) ? abs(intval($itemid)) : 0;
        $name = $db->query("/*qc=on*/SELECT `itmname` FROM `items` WHERE `itmid` = {$itemid}");
        if ($db->num_rows($name) > 0) {
            $username = $db->fetch_single($name);
            return $username;
        }
    }

    /*
        Returns the item id of the item specified.
        @param string name = Item's ID we're trying to fetch.
        On success, returns the item's id, on failure, it returns false.
    */
    function SystemItemNametoID($name)
    {
        global $db;
        $name = $db->escape(stripslashes($name));
        $id = $db->query("/*qc=on*/SELECT `itmid` FROM `items` WHERE `itmname` = '{$name}'");
        if ($db->num_rows($id) > 0) {
            $itemid = $db->fetch_single($id);
            return $itemid;
        }
    }

    /*
        Returns the town name of the town id specified.
        @param int id = Town ID's name we're trying to getch.
        On success, returns the town's name, on failure, it returns false.
    */
    function SystemTownIDtoName($id)
    {
        global $db;
        $id = (isset($id) && is_numeric($id)) ? abs(intval($id)) : 0;
        $name = $db->query("/*qc=on*/SELECT `town_name` FROM `town` WHERE `town_id` = {$id}");
        if ($db->num_rows($name) > 0) {
            $name = $db->fetch_single($name);
            return $name;
        }
    }

    /*
        Function that does all the hard work when it comes to item buying.
        @param int user = User to give item to, if bought successfully.
        @param int currency = Currency type. [1 = Primary, 2 = Secondary]
        @param int cost = Cost of item.
        @param int qty = Quantity of item to buy.
        Returns true if item was bought, false if the item does not exist, or cannot be bought.
    */
    function GameBuyItem($user, $currency, $cost, $item, $qty = 1)
    {
        global $db, $api;
        $user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
        $currency = (isset($currency) && is_numeric($currency)) ? abs(intval($currency)) : 0;
        $cost = (isset($cost) && is_numeric($cost)) ? abs(intval($cost)) : 0;
        $qty = (isset($qty) && is_numeric($qty)) ? abs(intval($qty)) : 0;
        $item = (isset($item) && is_numeric($item)) ? abs(intval($item)) : 0;
        ($currency == 1) ? $curr = 'primary' : $curr = 'secondary';
        $user_currency = $db->fetch_single($db->query("/*qc=on*/SELECT `{$curr}_currency` FROM `users` WHERE `userid` = {$user}"));
        if ($user_currency > $cost * $qty) {
            if ($api->SystemItemIDtoName($item)) {
                $api->UserGiveItem($user, $item, $qty);
                $db->query("UPDATE `users` SET `{$curr}_currency` = `{$curr}_currency` - {$cost} WHERE `userid` = {$user}");
                return true;
            }
        }
    }

    /*
        Function to return the inputted value with a tax percent added onto it.
        @param int number = Number to add a tax percent onto.
        @param int tax = Tax percentage. Optional. (Number between 0-100)
        Returns the inputted value with a tax percent added onto it.

        If tax is omitted or entered as -1, it'll use the user's current town's guild owner.
    */
    function SystemReturnTax($number, $tax = -1)
    {
        global $db, $ir, $userid;
        $number = (isset($number) && is_numeric($number)) ? abs(intval($number)) : 0;
        $tax = (isset($tax) && is_numeric($tax)) ? intval($tax) : -1;
        if ($tax == -1) {
            $tax = $db->fetch_single($db->query("/*qc=on*/SELECT `town_tax` FROM `town` WHERE `town_id` = {$ir['location']}"));
        }
        $guild = $db->fetch_single($db->query("/*qc=on*/SELECT `town_guild_owner` FROM `town` WHERE `town_id` = {$ir['location']}"));
        if ($ir['guild'] != $guild)
            return $number + ($number * ($tax / 100));
        else
            if (getSkillLevel($userid,19) == 0)
                return $number + ($number * ($tax / 100));
            else
                return $number;
    }

    /*
        Function to return the tax value of the inputted number only (Ex: 10% of 100 is 10).
        @param int number = Number to add a tax percent onto.
        @param int tax = Tax percentage. Optional. (Number between 0-100)
        Returns just the added tax number to the inputted number

        If tax is omitted or entered as -1, it'll use the user's current town's guild owner.
    */
    function SystemReturnTaxOnly($number, $tax = -1)
    {
        global $db, $ir;
        $number = (isset($number) && is_numeric($number)) ? abs(intval($number)) : 0;
        $tax = (isset($tax) && is_numeric($tax)) ? intval($tax) : 0;
        if ($tax == -1) {
            $tax = $db->fetch_single($db->query("/*qc=on*/SELECT `town_tax` FROM `town` WHERE `town_id` = {$ir['location']}"));
        }
        return $number - $number + ($number * ($tax / 100));
    }

    /*
        Function to credit the inputted guild with the inputted number.
        @param int number = Number to credit to the guild
        @param int curr = Currency type. [1 = Primary, 2 = Secondary]
        @param int guild = Guild ID. Optional. If left blank, will use the town's guild owner.
    */
    function SystemCreditTax($number, $curr, $guild = -1)
    {
        global $db, $ir;
        $number = (isset($number) && is_numeric($number)) ? abs(intval($number)) : 0;
        $curr = (isset($curr) && is_numeric($curr)) ? abs(intval($curr)) : 1;
        $guild = (isset($guild) && is_numeric($guild)) ? intval($guild) : -1;
        ($curr == 1) ? $cur = 'prim' : $cur = 'sec';
        if ($guild == -1) {
            $guild = $db->fetch_single($db->query("/*qc=on*/SELECT `town_guild_owner` FROM `town` WHERE `town_id` = {$ir['location']}"));
        }
        $db->query("UPDATE `guild` SET `guild_{$cur}curr` = `guild_{$cur}curr` + {$number} WHERE `guild_id` = {$guild}");
    }

    /*
        Function to fetch all or a specific field of information from the specified guild.
        @param int guild_id = Guild ID to fetch info from.
        @param text field = Data field to return. Optional. If left null/empty, will return all fields.
        Returns all fields if field is empty, otherwise it'll return a single field.
    */
    function GuildFetchInfo($guild_id, $field = null)
    {
        global $db;
        $guild_id = (isset($guild_id) && is_numeric($guild_id)) ? abs(intval($guild_id)) : 0;
        if (isset($guild_id) && $guild_id > 0) {
            $cnt = $db->query("/*qc=on*/SELECT * FROM `guild` WHERE `guild_id` = {$guild_id}");
            if ($db->num_rows($cnt) > 0) {
                if (is_null($field)) {
                    return $db->fetch_row($db->query("/*qc=on*/SELECT * FROM `guild` WHERE `guild_id` = {$guild_id}"));
                } else {
                    $field = $db->escape(stripslashes($field));
                    return $db->fetch_single($db->query("/*qc=on*/SELECT `{$field}` FROM `guild` WHERE `guild_id` = {$guild_id}"));
                }
            }
        }
    }

    /*
        Function to add a guild notification to a guild.
        @param int guild_id = ID of the guild you wish to add a notification to.
        @param text notification = Notification text.
        Returns true if the notification was added successfully, false otherwise.
    */
    function GuildAddNotification($guild_id, $notification)
    {
        global $db;
        $notification = $db->escape(stripslashes($notification));
        $time = time();
        $guild_id = (isset($guild_id) && is_numeric($guild_id)) ? abs(intval($guild_id)) : 0;
        if (isset($guild_id) && $guild_id > 0) {
            $cnt = $db->query("/*qc=on*/SELECT * FROM `guild` WHERE `guild_id` = {$guild_id}");
            if ($db->num_rows($cnt) > 0) {
                $db->query("INSERT INTO `guild_notifications` (`gn_id`, `gn_guild`, `gn_time`, `gn_text`) VALUES (NULL, '{$guild_id}', '{$time}', '{$notification}')");
                return true;
            }
        }
    }

    /*
        Function to set a user's info a static value.
        @param int user = User ID you wish to set a specific stat to.
        @param text stat = Stat to alter.
        @param int state = Value to set the stat to.
        Returns true if the stat was updated, false otherwise.
        Throws E_ERROR if attempting to edit a sensitive field (Such as passwords)
    */
    function UserInfoSetStatic($user, $stat, $state)
    {
        global $db, $api;
        $user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
        $stat = $db->escape(stripslashes(strtolower($stat)));
        if (in_array($stat, array('password', 'email', 'lastip', 'loginip',
            'registerip', 'personal_notes', 'staff_notes'))) {
            trigger_error("You do not have permission to set the {$stat} on this user.", E_ERROR);
        } else {
            if (is_int($state)) {
                $state = (isset($state) && is_numeric($state)) ? abs(intval($state)) : 0;
            } else {
                $state = $db->escape(stripslashes($state));
            }
            if ($user > 0) {
                if (!($api->SystemUserIDtoName($user) == false)) {
                    $db->query("UPDATE `users` SET `{$stat}` = '{$state}' WHERE `userid` = '{$user}'");
                    return true;
                }
            }
        }
    }

    /*
        Function to test if the inputted users share IPs at all.
        @param int user1 = User ID of the first player.
        @param int user2 = User ID of the second player.
        Returns true if the users share an IP, false if not. Will also return false if both variables are equal.
    */
    function SystemCheckUsersIPs($user1, $user2)
    {
        global $db;
        $user1 = (isset($user1) && is_numeric($user1)) ? abs(intval($user1)) : 0;
        $user2 = (isset($user2) && is_numeric($user2)) ? abs(intval($user2)) : 0;
        if (!empty($user1) || !empty($user2)) {
            if ($user1 != $user2) {
                $s = $db->fetch_row($db->query("/*qc=on*/SELECT `lastip`,`loginip`,`registerip` FROM `users` WHERE `userid` = {$user1}"));
                $r = $db->fetch_row($db->query("/*qc=on*/SELECT `lastip`,`loginip`,`registerip` FROM `users` WHERE `userid` = {$user2}"));
                if ($s['lastip'] == $r['lastip'] || $s['loginip'] == $r['loginip'] || $s['registerip'] == $r['registerip']) {
                    return true;
                }
            }
        }
    }

    /*
        Function to fetch item count from a user's inventory.
        @param int userid = User ID of the player to test inventory.
        @param int itemid = Item ID to count.
        Returns the count of Item ID found on the user.
    */
    function UserCountItem($userid, $itemid)
    {
        global $db;
        $userid = (isset($userid) && is_numeric($userid)) ? abs(intval($userid)) : 0;
        $itemid = (isset($itemid) && is_numeric($itemid)) ? abs(intval($itemid)) : 0;
        if (!empty($userid) || !empty($itemid)) {
            $qty = $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`inv_qty`) FROM `inventory` WHERE `inv_itemid` = {$itemid} AND `inv_userid` = {$userid}"));
            if (empty($qty))
                $qty = 0;
            return $qty;
        }
    }

    /*
     * Function to simulate a user training.
     * @param int userid = User ID of the player you wish to simular.
     * @param text stat = Stat you wish for the user to train.
     * @param int times = How much you wish the user to train.
     * Returns stats gained.
     */
    function UserTrain($userid, $stat, $times, $multiplier = 1)
    {
        global $db;
        $userid = (isset($userid) && is_numeric($userid)) ? abs(intval($userid)) : 0;
        $stat = $db->escape(stripslashes(strtolower($stat)));
        $times = (isset($times) && is_numeric($times)) ? abs(intval($times)) : 0;
        //Return empty if the call isn't complete.
        if (empty($userid) || (empty($stat)) || (empty($times))) {
            return 0;
        }
        $StatArray = array("strength", "agility", "guard", "labor", "iq");
        if (!in_array($stat, $StatArray)) {
            return -1;
        }
        $udq = $db->query("/*qc=on*/SELECT * FROM `users` WHERE `userid` = {$userid}");
        $userdata = $db->fetch_row($udq);
        $gain = 0;
		$reset = $db->fetch_single($db->query("SELECT `reset` FROM `user_settings` WHERE `userid` = {$userid}"));
		$optTraining = getSkillLevel($userid, 8);
        //Do while value is less than the user's energy input, then add one to value.
        for ($i = 0; $i < $times; $i++) 
        {
            $gain += mt_rand(1, 4) / mt_rand(600, 1000) * mt_rand(500, 1000) * (($userdata['will'] + 25) / 175);
                if ($optTraining == 0)
                    $userdata['will'] -= mt_rand(1, 3);
                else
                {
                    if (mt_rand(1,2) != 1)
                        $userdata['will'] -= mt_rand(1, 3);
                }
            //User's will ends up negative, set to zero.
            if ($userdata['will'] < 0) {
                $userdata['will'] = 0;
            }
        }
		$modifier=((getSkillLevel($userid,2)*2.5)/100);
		$nmodifier=((getSkillLevel($userid,2)*1)/100);
        //User's class is warrior
        if ($userdata['class'] == 'Warrior') {
            //Trained stat is strength, double its output.
            if ($stat == 'strength') {
                $gain *= (1.5 - $modifier);
            }
            //Trained stat is guard, half its output.
            if ($stat == 'guard') {
                $gain *= (0.5 + $nmodifier);
            }
        }
        //User's class is Rogue.
        if ($userdata['class'] == 'Rogue') {
            //Trained stat is agility, double its output.
            if ($stat == 'agility') {
                $gain *= (1.5 - $modifier);
            }
            //Trained stat is strength, half its output.
            if ($stat == 'strength') {
                $gain *= (0.5 - $nmodifier);
            }
        }
        //User's class is Defender.
        if ($userdata['class'] == 'Guardian') {
            //Trained stat is guard, double its output.
            if ($stat == 'guard') {
                $gain *= (1.5 - $modifier);
            }
            //Trained stat is agility, half its output.
            if ($stat == 'agility') {
                $gain *= (0.5 + $nmodifier);
            }
        }
        //Add multiplier, if needed.
        $gain *= $multiplier;
        //Round the gained stats.
        $gain = floor($gain);
		
		//Lucked out for 5% more stats?
		if (calculateLuck($userid))
		{
			$gain = $gain + ($gain*0.07);
		}
		
		if ($reset > 1)
		{
			$gain = $gain + ($gain * (0.1 * $reset)); 
		}
		
        //Update the user's stats.
        $db->query("UPDATE `userstats`
                    SET `{$stat}` = `{$stat}` + {$gain}
                    WHERE `userid` = {$userid}");
        //Update user's will and energy.
        $db->query("UPDATE `users`
                    SET `will` = {$userdata['will']},
                    `energy` = `energy` - {$times}
                    WHERE `userid` = {$userid}");
        return $gain;
    }

    /*
     * Function to send a game email
     * @param email to = Email address so send email to.
     * @param text body = Body of the email.
     * @param text subject = Subject of the email. [Optional, Default = "Gamename Game Email"]
     * @param email from = Email account sender [Optional, Default = "Game Sending Email"]
     * Returns Email was sent successfully.
    */
    function SystemSendEmail($to, $body, $subject = '', $from = '')
    {
        global $set;
        if (empty($from))
            $from = $set['sending_email'];
        if (empty($subject))
            $subject = "{$set['WebsiteName']} Game Email";
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=iso-8859-1';
        $headers[] = "From: Chivalry is Dead <{$from}>";
		$htmlhdr="<img src='https://res.cloudinary.com/dydidizue/image/upload/v1520819511/logo-optimized.png' width='64' height='64'><br /><h4>Chivalry is Dead</h4><hr />";
		$htmlftr="<hr /> -CID Admin, Chivalry is Dead Developer.<br /><a href='https://www.chivalryisdeadgame.com/'>https://www.chivalryisdeadgame.com/</a>";
		$realBody= $htmlhdr . $body . $htmlftr;
        return mail($to, $subject, $realBody, implode("\r\n", $headers));
    }

    /*
     * API to give an item to a guild.
     * @param int guild = Guild ID to give the item to.
     * @param int item = Item ID to give to the guild.
     * @param int qty = Quantity of item to give to the guild.
     * Returns true if item successfully given to the guild.
     * Returns false if item failed to be given to guild.
     */
    function GuildAddItem($guild, $item, $qty)
    {
        global $db;
        //Select $item's item name.
        $ie = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`itmname`) FROM `items` WHERE `itmid` = {$item}"));
        //If the name returns, continue
        if ($ie > 0) {
            $q = $db->query("/*qc=on*/SELECT `gaID` FROM `guild_armory` WHERE `gaGUILD` = {$guild} AND `gaITEM` = {$item}
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
     * API to remove an item from a guild.
     * @param int guild = Guild ID to remove the item from.
     * @param int item = Item ID to remove.
     * @param int qty = Quantity of item to remove.
     * Returns true if item successfully removed.
     * Returns false if item failed to be taken away.
     */
    function GuildRemoveItem($guild, $item, $qty)
    {
        global $db;
        //Select $item's item name.
        $ie = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`itmname`) FROM `items` WHERE `itmid` = {$item}"));
        //If $itemid actually exists, it'll return a name, so lets continue if that's the case.
        if ($ie > 0) {
            //Select the Armory ID number where $item's is stored for $guild.
            $q = $db->query("/*qc=on*/SELECT `gaID`, `gaQTY` FROM `guild_armory` WHERE `gaGUILD` = {$guild}
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
	function UserBlocked($blocked,$blocker)
	{
		global $db;
		$blocked = (isset($blocked) && is_numeric($blocked)) ? abs(intval($blocked)) : 0;
		$blocker = (isset($blocker) && is_numeric($blocker)) ? abs(intval($blocker)) : 0;
		$q=$db->query("/*qc=on*/SELECT `block_id` FROM `blocklist` WHERE `blocked` = {$blocked} AND `blocker` = {$blocker}");
		if ($db->num_rows($q) > 0)
			return true;
	}
	
	function GuildHasItem($guild, $item, $qty = 1)
    {
        global $db;
		$guild = (isset($guild) && is_numeric($guild)) ? abs(intval($guild)) : 0;
        $item = (isset($item) && is_numeric($item)) ? abs(intval($item)) : 0;
        $qty = (isset($qty) && is_numeric($qty)) ? abs(intval($qty)) : 0;
        if ($guild > 0 || $item > 0 || $qty > 0) {
            $i = $db->fetch_single($db->query("/*qc=on*/SELECT `gaQTY` FROM `guild_armory` WHERE `gaGUILD` = {$guild} && `gaITEM` = {$item} LIMIT 1"));
            if ($qty == 1) {
                if ($i >= 1) {
                    return true;
                }
            } else {
                if ($i >= $qty) {
                    return true;
                }
            }
        }
    }
	function GuildHasXP($guild, $xp)
    {
        global $db;
		$guild = (isset($guild) && is_numeric($guild)) ? abs(intval($guild)) : 0;
        $qty = (isset($qty) && is_numeric($qty)) ? abs(intval($qty)) : 0;
        if ($guild > 0 || $item > 0 || $qty > 0) {
            $i = $db->fetch_single($db->query("/*qc=on*/SELECT `guild_xp` FROM `guild` WHERE `guild_id` = {$guild}"));
            if ($qty == 1) {
                if ($i >= 1) {
                    return true;
                }
            } else {
                if ($i >= $qty) {
                    return true;
                }
            }
        }
    }
	function GuildRemoveXP($guild, $xp)
    {
        global $db;
		$guild = (isset($guild) && is_numeric($guild)) ? abs(intval($guild)) : 0;
        $qty = (isset($qty) && is_numeric($qty)) ? abs(intval($qty)) : 0;
        $db->query("UPDATE `guild` SET `guild_xp` = `guild_xp` - {$xp} WHERE `guild_id` = {$guild}");
    }
	function GuildAddXP($guild, $xp)
    {
        global $db;
		$guild = (isset($guild) && is_numeric($guild)) ? abs(intval($guild)) : 0;
        $qty = (isset($qty) && is_numeric($qty)) ? abs(intval($qty)) : 0;
        $db->query("UPDATE `guild` SET `guild_xp` = `guild_xp` + {$xp} WHERE `guild_id` = {$guild}");
    }
	function GuildHasCurrency($guild, $type, $minimum)
    {
        global $db;
        $guild = (isset($user) && is_numeric($guild)) ? abs(intval($guild)) : 0;
        $minimum = (isset($minimum) && is_numeric($minimum)) ? abs(intval($minimum)) : 0;
        $type = $db->escape(stripslashes(strtolower($type)));
        $userexist = $db->fetch_single($db->query("/*qc=on*/SELECT `guild_id` FROM `guild` WHERE `guild_id` = {$guild}"));
        if ($userexist) 
		{
            if ($type == 'primary' || $type == 'secondary') 
			{
				if ($type == 'primary')
					$typee = 'prim';
				else
					$typee = 'sec';
                $UserMoney = $db->fetch_single($db->query("/*qc=on*/SELECT `guild_{$typee}curr` FROM `guild` WHERE `guild_id` = {$guild}"));
                if ($UserMoney >= $minimum) 
                    return true;
            }
        }
    }
	function GuildGiveCurrency($guild, $type, $quantity)
    {
        global $db;
        $guild = (isset($user) && is_numeric($guild)) ? abs(intval($guild)) : 0;
        $minimum = (isset($minimum) && is_numeric($minimum)) ? abs(intval($minimum)) : 0;
        $type = $db->escape(stripslashes(strtolower($type)));
        $userexist = $db->fetch_single($db->query("/*qc=on*/SELECT `guild_id` FROM `guild` WHERE `guild_id` = {$guild}"));
        if ($userexist) 
		{
            if ($type == 'primary' || $type == 'secondary') 
			{
				if ($type == 'primary')
					$type = 'prim';
				else
					$type = 'sec';
                $db->query("UPDATE `guild` SET `guild_{$type}curr` = `guild_{$type}curr` + {$quantity} WHERE `guild_id` = {$guild}");
                return true;
            }
        }
    }
	function GuildRemoveCurrency($guild, $type, $quantity)
    {
        global $db;
        $guild = (isset($user) && is_numeric($guild)) ? abs(intval($guild)) : 0;
        $minimum = (isset($minimum) && is_numeric($minimum)) ? abs(intval($minimum)) : 0;
        $type = $db->escape(stripslashes(strtolower($type)));
        $userexist = $db->fetch_single($db->query("/*qc=on*/SELECT `guild_id` FROM `guild` WHERE `guild_id` = {$guild}"));
        if ($userexist) 
		{
            if ($type == 'primary' || $type == 'secondary') 
			{
				if ($type == 'primary')
					$type = 'prim';
				else
					$type = 'sec';
                $db->query("UPDATE `guild` SET `guild_{$type}curr` = `guild_{$type}curr` - {$quantity} WHERE `guild_id` = {$guild}");
                return true;
            }
        }
    }
}