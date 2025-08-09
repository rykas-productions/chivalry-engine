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

class api
{
    /** @var array Acceptable currency types */
    var $acceptableCurrencyTypes = ['primary', 'secondary'];

    /** @var array Acceptable equipment slots */
    var $acceptableEquipSlots = ['primary', 'secondary', 'armor'];

    /** @var array Acceptable status locations */
    var $acceptableStatusLocations = ['infirmary', 'dungeon'];

    /** @var array Protected user fields that cannot be modified */
    var $protectedUserFields = [
        'password', 'email', 'lastip', 'loginip',
        'registerip', 'personal_notes', 'staff_notes'
    ];

    /**
     * Returns the API version.
     * @return string API version number
     */
    function SystemReturnAPIVersion(): string
    {
        return "24.4.1";    //Last Updated 4/29/2024
    }

    /**
     * Tests if specified user has at least the specified amount of money.
     * @param int $user User ID to test for
     * @param string $type Currency type ('primary' or 'secondary')
     * @param int $minimum Minimum money required
     * @return bool True if user has more cash than required
     */
    function UserHasCurrency(int $user, string $type, int $minimum): bool
    {
        global $db;
        $user = abs($user);
        $minimum = abs($minimum);
        $type = $db->escape(stripslashes(strtolower($type)));

        if (!in_array($type, $this->acceptableCurrencyTypes)) {
            trigger_error("Unacceptable currency type '{$type}'.");
            return false;
        }

        $userexist = $db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$user}"));
        if ($userexist) {
            $UserMoney = $db->fetch_single($db->query("SELECT `{$type}_currency` FROM `users` WHERE `userid` = {$user}"));
            return ($UserMoney >= $minimum);
        }
        return false;
    }

    /**
     * Shorthand for checking primary currency
     * @param int $user User ID to test for
     * @param int $minimum Minimum money required
     * @return bool True if user has enough primary currency
     */
    function UserHasPrimaryCurrency(int $user, int $minimum): bool
    {
        return $this->UserHasCurrency($user, 'primary', $minimum);
    }

    /**
     * Shorthand for checking secondary currency
     * @param int $user User ID to test for
     * @param int $minimum Minimum money required
     * @return bool True if user has enough secondary currency
     */
    function UserHasSecondaryCurrency(int $user, int $minimum): bool
    {
        return $this->UserHasCurrency($user, 'secondary', $minimum);
    }

    /**
     * Gives the user the specified item and quantity
     * @param int $user User ID to give item to
     * @param int $item Item ID to give
     * @param int $quantity Quantity to give
     * @return bool True if item successfully given
     */
    function UserGiveItem(int $user, int $item, int $quantity): bool
    {
        $user = abs($user);
        $item = abs($item);
        $quantity = abs($quantity);
        
        return item_add($user, $item, $quantity) ? true : false;
    }

    /**
     * Removes an item from the user specified
     * @param int $user User ID to remove item from
     * @param int $item Item ID to remove
     * @param int $quantity Quantity to remove
     * @return bool True if item successfully removed
     */
    function UserTakeItem(int $user, int $item, int $quantity): bool
    {
        $user = abs($user);
        $item = abs($item);
        $quantity = abs($quantity);
        
        return item_remove($user, $item, $quantity) ? true : false;
    }

    /**
     * Gives user specified amount of currency type.
     * @param int $user User ID to give currency to
     * @param string $type Currency type ('primary' or 'secondary')
     * @param int $quantity Amount of currency to give
     * @return bool True if currency successfully given
     */
    function UserGiveCurrency(int $user, string $type, int $quantity): bool
    {
        global $db;
        $user = abs($user);
        $type = $db->escape(stripslashes(strtolower($type)));
        $quantity = abs($quantity);

        if (!in_array($type, $this->acceptableCurrencyTypes)) {
            trigger_error("Unacceptable currency type '{$type}'.");
            return false;
        }

        $userexist = $db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$user}"));
        if ($userexist) {
            $db->query("UPDATE `users` SET `{$type}_currency` = `{$type}_currency` + {$quantity} WHERE `userid` = {$user}");
            return true;
        }
        return false;
    }

    /**
     * Takes quantity of currency type from the user specified.
     * @param int $user User ID to take currency from
     * @param string $type Currency type ('primary' or 'secondary')
     * @param int $quantity Amount of currency to take
     * @return bool True if currency successfully taken
     */
    function UserTakeCurrency(int $user, string $type, int $quantity): bool
    {
        global $db;
        $user = abs($user);
        $type = $db->escape(stripslashes(strtolower($type)));
        $quantity = abs($quantity);

        if (!in_array($type, $this->acceptableCurrencyTypes)) {
            trigger_error("Unacceptable currency type '{$type}'.");
            return false;
        }

        $userexist = $db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$user}"));
        if ($userexist) {
            $db->query("UPDATE `users` SET `{$type}_currency` = `{$type}_currency` - {$quantity} WHERE `userid` = {$user}");
            $db->query("UPDATE `users` SET `{$type}_currency` = 0 WHERE `{$type}_currency` < 0");
            return true;
        }
        return false;
    }

    /**
     * Tests to see what the user has equipped.
     * @param int $user User ID to test against
     * @param string $slot Equipment slot to test ('primary', 'secondary', or 'armor')
     * @param int $itemid Item ID to test for (-1 = Any Item, 0 = No Item Equipped, >0 = Specific item)
     * @return bool True if user has item equipped
     */
    function UserEquippedItem(int $user, string $slot, int $itemid = -1): bool
    {
        global $db;
        $user = abs($user);
        $slot = $db->escape(stripslashes(strtolower($slot)));

        if (!in_array($slot, $this->acceptableEquipSlots)) {
            trigger_error("Unacceptable equipment slot '{$slot}'.");
            return false;
        }

        // Any item equipped
        if ($itemid == -1) {
            $equipped = $db->fetch_single($db->query("SELECT `equip_{$slot}` FROM `users` WHERE `userid` = {$user}"));
            return ($equipped > 0);
        } 
        // Specific item equipped
        elseif ($itemid > 0) {
            $itemid = abs($itemid);
            $equipped = $db->fetch_single($db->query("SELECT `equip_{$slot}` FROM `users` WHERE `userid` = {$user}"));
            return ($equipped == $itemid);
        } 
        // Nothing equipped
        elseif ($itemid == 0) {
            $equipped = $db->fetch_single($db->query("SELECT `equip_{$slot}` FROM `users` WHERE `userid` = {$user}"));
            return ($equipped == 0);
        }

        return false;
    }

    /**
     * Tests the inputted user to see if they're in the dungeon or infirmary
     * @param int $user User ID to test against
     * @param string $status Place to test ('infirmary' or 'dungeon')
     * @return bool True if user is in the specified location
     */
    function UserStatus(int $user, string $status): bool
    {
        global $db;
        $user = abs($user);
        $status = $db->escape(stripslashes(strtolower($status)));

        if (!in_array($status, $this->acceptableStatusLocations)) {
            trigger_error("Unacceptable place type '{$status}'.");
            return false;
        }

        if ($status == 'infirmary') {
            return user_infirmary($user);
        } elseif ($status == 'dungeon') {
            return user_dungeon($user);
        }
        return false;
    }

    /**
     * Places or removes dungeon/infirmary time on the specified user.
     * @param int $user User ID to test against
     * @param string $place Place to test ('dungeon' or 'infirmary')
     * @param int $time Minutes user is in infirmary/dungeon
     * @param string $reason Reason why user is in the infirmary/dungeon
     * @return bool True if user is placed in the infirmary/dungeon, or is removed from it
     */
    function UserStatusSet(int $user, string $place, int $time, string $reason): bool
    {
        global $db;
        $user = abs($user);
        $reason = $db->escape(stripslashes($reason));
        $place = $db->escape(stripslashes(strtolower($place)));

        if (!in_array($place, $this->acceptableStatusLocations)) {
            trigger_error("Unacceptable place type '{$place}'.");
            return false;
        }

        if ($place == 'infirmary') {
            $time = max(0, abs($time));
            put_infirmary($user, $time, $reason);
            return true;
        } elseif ($place == 'dungeon') {
            $time = max(0, abs($time));
            put_dungeon($user, $time, $reason);
            return true;
        }
        return false;
    }

    /**
     * Adds a notification for the specified user.
     * @param int $user User ID to send notification to
     * @param string $text Notification text
     * @return bool True if notification added
     */
    function GameAddNotification(int $user, string $text): bool
    {
        notification_add($user, $text);
        return true;
    }

    /**
     * Adds an in-game message for the player specified.
     * @param int $user User ID message is sent to
     * @param string $subj Message subject
     * @param string $msg Message text
     * @param int $from User ID message is from
     * @return bool True when message is sent
     */
    function GameAddMail(int $user, string $subj, string $msg, int $from): bool
    {
        global $db;
        $user = abs($user);
        $from = abs($from);
        $subj = $db->escape(stripslashes($subj));
        $msg = $db->escape(stripslashes($msg));
        $time = time();

        $userexist = $db->query("SELECT `userid` FROM `users` WHERE `userid` =  {$user}");
        if ($db->num_rows($userexist) > 0) {
            $db->free_result($userexist);
            $userexist = $db->query("SELECT `userid` FROM `users` WHERE `userid` =  {$from}");
            if ($db->num_rows($userexist) > 0) {
                $db->query("INSERT INTO `mail`
				(`mail_to`, `mail_from`, `mail_status`, `mail_subject`, `mail_text`, `mail_time`) 
				VALUES 
				('{$user}', '{$from}', 'unread', '{$subj}', '{$msg}', '{$time}');");
                return true;
            }
        }
        return false;
    }

    /**
     * Adds an in-game announcement.
     * @param string $text Announcement text
     * @param int $poster User ID of poster (default: 1)
     * @return bool True when announcement is made
     */
    function GameAddAnnouncement(string $text, int $poster = 1): bool
    {
        global $db;
        $text = $db->escape(str_replace("\n", "<br />", stripslashes($text)));
        $poster = abs($poster);
        $time = time();

        $userexist = $db->query("SELECT `userid` FROM `users` WHERE `userid` =  {$poster}");
        if ($db->num_rows($userexist) > 0) {
            $db->query("INSERT INTO `announcements`
			(`ann_text`, `ann_time`, `ann_poster`) 
			VALUES 
			('{$text}', '{$time}', '{$poster}');");
            $db->query("UPDATE `users` SET `announcements` = `announcements` + 1");
            return true;
        }
        return false;
    }

    /**
     * Get the user's member level. Can test for exact member level, or if user is above specified member level.
     * @param int $user User to test on
     * @param string $level Member level to test for
     * @param bool $exact Return true if ranked ONLY specified level (default: false)
     * @return bool True if user is exactly or equal to/above specified member level
     */
    function UserMemberLevelGet(int $user, string $level, bool $exact = false): bool
    {
        global $db;
        $level = $db->escape(stripslashes(strtolower($level)));
        $user = abs($user);

        if ($user > 0) {
            $userexist = $db->query("SELECT `userid` FROM `users` WHERE `userid` =  {$user}");
            if ($db->num_rows($userexist) > 0) {
                $ulevel = $db->fetch_single($db->query("SELECT `user_level` FROM `users` WHERE `userid` = {$user}"));
                if ($exact) {
                    return ($level == $ulevel);
                } else {
                    switch ($level) {
                        case 'member':
                            return in_array($ulevel, ['Member', 'Forum Moderator', 'Assistant', 'Web Developer', 'Admin']);
                        case 'forum moderator':
                            return in_array($ulevel, ['Forum Moderator', 'Assistant', 'Web Developer', 'Admin']);
                        case 'assistant':
                            return in_array($ulevel, ['Assistant', 'Web Developer', 'Admin']);
                        case 'web dev':
                            return in_array($ulevel, ['Web Developer', 'Admin']);
                        case 'npc':
                            return in_array($ulevel, ['Member', 'NPC', 'Forum Moderator', 'Assistant', 'Web Developer', 'Admin']);
                        case 'admin':
                            return ($ulevel == 'Admin');
                    }
                }
            }
        }
        return false;
    }

    /**
     * Test to see whether or not the specified user has the item and optionally, an amount of the item.
     * @param int $user User to test on
     * @param int $item Item ID to test for
     * @param int $qty Quantity to test for (default: 1)
     * @return bool True if the user has the item and required quantity
     */
    function UserHasItem(int $user, int $item, int $qty = 1): bool
    {
        global $db;
        $user = abs($user);
        $item = abs($item);
        $qty = abs($qty);

        if ($user > 0 && $item > 0 && $qty > 0) {
            $i = $db->fetch_single($db->query("SELECT `inv_qty` FROM `inventory` WHERE `inv_userid` = {$user} && `inv_itemid` = {$item}"));
            return ($qty == 1) ? ($i >= 1) : ($i >= $qty);
        }
        return false;
    }

    /**
     * Returns the specified user's stat, optionally as a percent.
     * @param int $user User to test on
     * @param string $stat User's table row to return
     * @param bool $percent Return as a percent (default: false)
     * @return mixed Value in the stat specified, optionally as a percent
     */
    function UserInfoGet(int $user, string $stat, bool $percent = false)
    {
        global $db;
        $user = abs($user);
        $stat = $db->escape(stripslashes(strtolower($stat)));

        if (in_array($stat, $this->protectedUserFields)) {
            trigger_error("You do not have permission to get the {$stat} on this user.", E_ERROR);
        } else {
            if ($percent) {
                $min = $db->fetch_single($db->query("SELECT `{$stat}` FROM `users` WHERE `userid` = {$user}"));
                $max = $db->fetch_single($db->query("SELECT `max{$stat}` FROM `users` WHERE `userid` = {$user}"));
                return round($min / $max * 100);
            } else {
                return $db->fetch_single($db->query("SELECT `{$stat}` FROM `users` WHERE `userid` = {$user}"));
            }
        }
        return null;
    }

    /**
     * Set the specified user's stat to a value, optionally as a percent.
     * @param int $user User to test on
     * @param string $stat User's table row to return
     * @param int $change Direction of change
     * @param bool $percent Return as a percent (default: false)
     * @return bool True if stat was successfully set
     */
    function UserInfoSet(int $user, string $stat, int $change, bool $percent = false): bool
    {
        global $db;
        $user = abs($user);
        $stat = $db->escape(stripslashes(strtolower($stat)));

        if (in_array($stat, $this->protectedUserFields)) {
            trigger_error("You do not have permission to set the {$stat} on this user.", E_ERROR);
        } else {
            if ($change >= 1) {
                $change = abs($change);
                if ($percent) {
                    $maxstat = $db->fetch_single($db->query("SELECT `max{$stat}` FROM `users` WHERE `userid` = {$user}"));
                    $number = ($change / 100) * $maxstat;
                    $db->query("UPDATE users SET `{$stat}`=`{$stat}`+{$number} WHERE `{$stat}` < `max{$stat}`");
                    $db->query("UPDATE users SET `{$stat}` = `max{$stat}` WHERE `{$stat}` > `max{$stat}`");
                } else {
                    $db->query("UPDATE users SET `{$stat}` = `{$stat}` + {$change} WHERE `userid` = {$user}");
                    $db->query("UPDATE users SET `{$stat}` = `max{$stat}` WHERE `{$stat}` > `max{$stat}`");
                }
                return true;
            } elseif ($change == 0) {
                $db->query("UPDATE users SET `{$stat}` = 0 WHERE `userid` = {$user}");
                return true;
            } else {
                $change = abs($change);
                if ($percent) {
                    $maxstat = $db->fetch_single($db->query("SELECT `max{$stat}` FROM `users` WHERE `userid` = {$user}"));
                    $number = ($change / 100) * $maxstat;
                    $db->query("UPDATE users SET `{$stat}` = `{$stat}` - {$number} WHERE `userid` = {$user}");
                    $db->query("UPDATE users SET `{$stat}` = 0 WHERE `{$stat}` < 0");
                } else {
                    $db->query("UPDATE users SET `{$stat}` = `{$stat}` - {$change} WHERE `userid` = {$user}");
                    $db->query("UPDATE users SET `{$stat}` = 0 WHERE `{$stat}` < 0");
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Adds an entry to the main logging data table.
     * @param int $user User who is attached to this log
     * @param string $logtype Log type
     * @param string $input Text to be entered in the log
     */
    function SystemLogsAdd(int $user, string $logtype, string $input): void
    {
        global $db;
        $time = time();
        $IP = $db->escape($_SERVER['REMOTE_ADDR']);
        $user = abs($user);
        $input = $db->escape(stripslashes($input));
        $logtype = $db->escape(stripslashes(strtolower($logtype)));

        $db->query("INSERT INTO `logs` (`log_id`, `log_type`, `log_user`, `log_time`, `log_text`, `log_ip`) VALUES (NULL, '{$logtype}', '{$user}', '{$time}', '{$input}', '{$IP}');");
    }

    /**
     * Returns the username of the user id specified.
     * @param int $user User's ID we're trying to fetch
     * @return string|false On success, returns the user id's name, on failure, it returns false
     */
    function SystemUserIDtoName(int $user)
    {
        global $db;
        $user = abs($user);
        $name = $db->query("SELECT `username` FROM `users` WHERE `userid` = {$user}");
        if ($db->num_rows($name) > 0) {
            return $db->fetch_single($name);
        }
        return false;
    }

    /**
     * Returns the userid of the username specified.
     * @param string $name User's ID we're trying to fetch
     * @return int|false On success, returns the user's id, on failure, it returns false
     */
    function SystemUsernametoID(string $name)
    {
        global $db;
        $name = $db->escape(stripslashes($name));
        $id = $db->query("SELECT `userid` FROM `users` WHERE `username` = '{$name}'");
        if ($db->num_rows($id) > 0) {
            return $db->fetch_single($id);
        }
        return false;
    }

    /**
     * Returns the item name of the item id specified.
     * @param int $itemid Item's name we're trying to fetch
     * @return string|false On success, returns the item id's name, on failure, it returns false
     */
    function SystemItemIDtoName(int $itemid)
    {
        global $db;
        $itemid = abs($itemid);
        $name = $db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$itemid}");
        if ($db->num_rows($name) > 0) {
            return $db->fetch_single($name);
        }
        return false;
    }

    /**
     * Returns the item id of the item specified.
     * @param string $name Item's ID we're trying to fetch
     * @return int|false On success, returns the item's id, on failure, it returns false
     */
    function SystemItemNametoID(string $name)
    {
        global $db;
        $name = $db->escape(stripslashes($name));
        $id = $db->query("SELECT `itmid` FROM `items` WHERE `itmname` = '{$name}'");
        if ($db->num_rows($id) > 0) {
            return $db->fetch_single($id);
        }
        return false;
    }

    /**
     * Returns the town name of the town id specified.
     * @param int $id Town ID's name we're trying to getch
     * @return string|false On success, returns the town's name, on failure, it returns false
     */
    function SystemTownIDtoName(int $id)
    {
        global $db;
        $id = abs($id);
        $name = $db->query("SELECT `town_name` FROM `town` WHERE `town_id` = {$id}");
        if ($db->num_rows($name) > 0) {
            return $db->fetch_single($name);
        }
        return false;
    }

    /**
     * Function that does all the hard work when it comes to item buying.
     * @param int $user User to give item to, if bought successfully
     * @param int $currency Currency type (1 = Primary, 2 = Secondary)
     * @param int $cost Cost of item
     * @param int $item Item ID to buy
     * @param int $qty Quantity of item to buy
     * @return bool True if item was bought, false if not
     */
    function GameBuyItem(int $user, int $currency, int $cost, int $item, int $qty = 1): bool
    {
        global $db, $api;
        $user = abs($user);
        $currency = abs($currency);
        $cost = abs($cost);
        $qty = abs($qty);
        $item = abs($item);
        $curr = ($currency == 1) ? 'primary' : 'secondary';

        $user_currency = $db->fetch_single($db->query("SELECT `{$curr}_currency` FROM `users` WHERE `userid` = {$user}"));
        if ($user_currency > $cost * $qty) {
            if ($api->SystemItemIDtoName($item)) {
                $api->UserGiveItem($user, $item, $qty);
                $db->query("UPDATE `users` SET `{$curr}_currency` = `{$curr}_currency` - {$cost} WHERE `userid` = {$user}");
                return true;
            }
        }
        return false;
    }

    /**
     * Function to return the inputted value with a tax percent added onto it.
     * @param int $number Number to add a tax percent onto
     * @param int $tax Tax percentage (default: -1)
     * @return float Value with tax added
     */
    function SystemReturnTax(int $number, int $tax = -1): float
    {
        global $db, $ir;
        $number = abs($number);
        $tax = ($tax == -1) ? intval($db->fetch_single($db->query("SELECT `town_tax` FROM `town` WHERE `town_id` = {$ir['location']}"))) : $tax;
        return $number + ($number * ($tax / 100));
    }

    /**
     * Function to return the tax value of the inputted number only (Ex: 10% of 100 is 10).
     * @param int $number Number to add a tax percent onto
     * @param int $tax Tax percentage (default: -1)
     * @return float Tax amount
     */
    function SystemReturnTaxOnly(int $number, int $tax = -1): float
    {
        global $db, $ir;
        $number = abs($number);
        $tax = ($tax == -1) ? intval($db->fetch_single($db->query("SELECT `town_tax` FROM `town` WHERE `town_id` = {$ir['location']}"))) : $tax;
        return $number - $number + ($number * ($tax / 100));
    }

    /**
     * Function to credit the inputted guild with the inputted number.
     * @param int $number Number to credit to the guild
     * @param int $curr Currency type (1 = Primary, 2 = Secondary)
     * @param int $guild Guild ID (default: -1)
     */
    function SystemCreditTax(int $number, int $curr, int $guild = -1): void
    {
        global $db, $ir;
        $number = abs($number);
        $curr = abs($curr);
        $guild = ($guild == -1) ? intval($db->fetch_single($db->query("SELECT `town_guild_owner` FROM `town` WHERE `town_id` = {$ir['location']}"))) : $guild;
        $cur = ($curr == 1) ? 'prim' : 'sec';

        $db->query("UPDATE `guild` SET `guild_{$cur}curr` = `guild_{$cur}curr` + {$number} WHERE `guild_id` = {$guild}");
    }

    /**
     * Function to fetch all or a specific field of information from the specified guild.
     * @param int $guild_id Guild ID to fetch info from
     * @param string|null $field Data field to return (default: null)
     * @return array|string|false All fields if field is empty, otherwise a single field
     */
    function GuildFetchInfo(int $guild_id, string $field = null)
    {
        global $db;
        $guild_id = abs($guild_id);

        if ($guild_id > 0) {
            if (is_null($field)) {
                return $db->fetch_row($db->query("SELECT * FROM `guild` WHERE `guild_id` = {$guild_id}"));
            } else {
                $field = $db->escape(stripslashes($field));
                return $db->fetch_single($db->query("SELECT `{$field}` FROM `guild` WHERE `guild_id` = {$guild_id}"));
            }
        }
        return false;
    }

    /**
     * Function to add a guild notification to a guild.
     * @param int $guild_id ID of the guild to add a notification to
     * @param string $notification Notification text
     * @return bool True if notification was added successfully
     */
    function GuildAddNotification(int $guild_id, string $notification): bool
    {
        global $db;
        $notification = $db->escape(stripslashes($notification));
        $time = time();
        $guild_id = abs($guild_id);

        if ($guild_id > 0) {
            $cnt = $db->query("SELECT * FROM `guild` WHERE `guild_id` = {$guild_id}");
            if ($db->num_rows($cnt) > 0) {
                $db->query("INSERT INTO `guild_notifications` (`gn_id`, `gn_guild`, `gn_time`, `gn_text`) VALUES (NULL, '{$guild_id}', '{$time}', '{$notification}')");
                return true;
            }
        }
        return false;
    }

    /**
     * Function to set a user's info to a static value.
     * @param int $user User ID to set a specific stat to
     * @param string $stat Stat to alter
     * @param mixed $state Value to set the stat to
     * @return bool True if the stat was updated
     */
    function UserInfoSetStatic(int $user, string $stat, $state): bool
    {
        global $db, $api;
        $user = abs($user);
        $stat = $db->escape(stripslashes(strtolower($stat)));

        if (in_array($stat, $this->protectedUserFields)) {
            trigger_error("You do not have permission to set the {$stat} on this user.", E_ERROR);
        } else {
            if (is_int($state)) {
                $state = abs($state);
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
        return false;
    }

    /**
     * Function to test if the inputted users share IPs at all.
     * @param int $user1 User ID of the first player
     * @param int $user2 User ID of the second player
     * @return bool True if users share an IP
     */
    function SystemCheckUsersIPs(int $user1, int $user2): bool
    {
        global $db;
        $user1 = abs($user1);
        $user2 = abs($user2);

        if ($user1 === 0 || $user2 === 0 || $user1 === $user2) {
            return false;
        }

        $query = "SELECT `lastip`,`loginip`,`registerip` FROM `users` WHERE `userid` IN ({$user1}, {$user2})";
        $result = $db->query($query);
        
        if ($db->num_rows($result) !== 2) {
            return false;
        }

        $ips1 = $db->fetch_row($result);
        $ips2 = $db->fetch_row($result);

        return ($ips1['lastip'] === $ips2['lastip'] ||
                $ips1['loginip'] === $ips2['loginip'] ||
                $ips1['registerip'] === $ips2['registerip']);
    }

    /**
     * Function to fetch item count from a user's inventory.
     * @param int $userid User ID of the player to test inventory
     * @param int $itemid Item ID to count
     * @return int Count of Item ID found on the user
     */
    function UserCountItem(int $userid, int $itemid): int
    {
        global $db;
        $userid = abs($userid);
        $itemid = abs($itemid);

        if ($userid > 0 && $itemid > 0) {
            return $db->fetch_single($db->query("SELECT SUM(`inv_qty`) FROM `inventory` WHERE `inv_itemid` = {$itemid} AND `inv_userid` = {$userid}"));
        }
        return 0;
    }

    /**
     * Function to simulate a user training.
     * @param int $userid User ID of the player to simulate
     * @param string $stat Stat to train
     * @param int $times Number of times to train
     * @param int $multiplier Training multiplier (default: 1)
     * @return int Amount of stats gained
     */
    function UserTrain(int $userid, string $stat, int $times, int $multiplier = 1): int
    {
        global $db;
        $userid = abs($userid);
        $stat = $db->escape(stripslashes(strtolower($stat)));
        $times = abs($times);
        $multiplier = abs($multiplier);

        if ($userid === 0 || empty($stat) || $times === 0) {
            return 0;
        }

        $validStats = ["strength", "agility", "guard", "labor", "iq"];
        if (!in_array($stat, $validStats)) {
            return -1;
        }

        $userdata = $db->fetch_row($db->query("SELECT * FROM `users` WHERE `userid` = {$userid}"));
        if (!$userdata) {
            return 0;
        }

        $gain = $this->calculateTrainingGain($userdata, $stat, $times, $multiplier);

        // Update stats
        $db->query("UPDATE `userstats` SET `{$stat}` = `{$stat}` + {$gain} WHERE `userid` = {$userid}");
        $db->query("UPDATE `users` SET `will` = {$userdata['will']}, `energy` = `energy` - {$times} WHERE `userid` = {$userid}");

        return $gain;
    }

    /**
     * Helper function to calculate training gains
     * @param array $userdata User data array
     * @param string $stat Stat being trained
     * @param int $times Number of times training
     * @param int $multiplier Training multiplier
     * @return int Total gain amount
     */
    private function calculateTrainingGain(array $userdata, string $stat, int $times, int $multiplier): int 
    {
        $gain = 0;
        for ($i = 0; $i < $times; $i++) {
            $gain += Random(1, 4) / Random(600, 1000) * Random(500, 1000) * (($userdata['will'] + 25) / 175);
            $userdata['will'] -= Random(1, 3);
            $userdata['will'] = max(0, $userdata['will']);
        }

        // Apply class modifiers
        switch ($userdata['class']) {
            case 'Warrior':
                if ($stat === 'strength') $gain *= 2;
                if ($stat === 'guard') $gain /= 2;
                break;
            case 'Rogue':
                if ($stat === 'agility') $gain *= 2;
                if ($stat === 'strength') $gain /= 2;
                break;
            case 'Defender':
                if ($stat === 'guard') $gain *= 2;
                if ($stat === 'agility') $gain /= 2;
                break;
        }

        return floor($gain * $multiplier);
    }

    /**
     * Function to send a game email
     * @param string $to Email address to send email to
     * @param string $body Body of the email
     * @param string $subject Subject of the email (default: "Gamename Game Email")
     * @param string $from Email account sender (default: "Game Sending Email")
     * @return bool True if email was sent successfully
     */
    function SystemSendEmail(string $to, string $body, string $subject = '', string $from = ''): bool
    {
        global $set;
        if (empty($from))
            $from = $set['sending_email'];
        if (empty($subject))
            $subject = "{$set['WebsiteName']} Game Email";
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=iso-8859-1';
        $headers[] = "From: {$from}";
        return mail($to, $subject, $body, implode("\r\n", $headers));
    }

    /**
     * API to give an item to a guild.
     * @param int $guild Guild ID to give the item to
     * @param int $item Item ID to give to the guild
     * @param int $qty Quantity of item to give to the guild
     * @return bool True if item successfully given to the guild
     */
    function GuildAddItem(int $guild, int $item, int $qty): bool
    {
        global $db;
        $item = abs($item);
        $qty = abs($qty);

        // Select $item's item name.
        $ie = $db->fetch_single($db->query("SELECT COUNT(`itmname`) FROM `items` WHERE `itmid` = {$item}"));
        // If the name returns, continue
        if ($ie > 0) {
            $q = $db->query("SELECT `gaID` FROM `guild_armory` WHERE `gaGUILD` = {$guild} AND `gaITEM` = {$item} LIMIT 1");
            // If the armory stack exists, add $qty to it and return true to signify we succeeded at adding the item.
            if ($db->num_rows($q) > 0) {
                $r = $db->fetch_row($q);
                $db->query("UPDATE `guild_armory` SET `gaQTY` = `gaQTY` + {$qty} WHERE `gaID` = {$r['gaID']}");
                return true;
            } 
            // The armory item id does not exist, so lets create a new one and return true.
            else {
                $db->query("INSERT INTO `guild_armory` (`gaITEM`, `gaGUILD`, `gaQTY`) VALUES ({$item}, {$guild}, {$qty})");
                return true;
            }
        }
        return false;
    }

    /**
     * API to remove an item from a guild.
     * @param int $guild Guild ID to remove the item from
     * @param int $item Item ID to remove
     * @param int $qty Quantity of item to remove
     * @return bool True if item successfully removed
     */
    function GuildRemoveItem(int $guild, int $item, int $qty): bool
    {
        global $db;
        $item = abs($item);
        $qty = abs($qty);

        // Select $item's item name.
        $ie = $db->fetch_single($db->query("SELECT COUNT(`itmname`) FROM `items` WHERE `itmid` = {$item}"));
        // If $itemid actually exists, it'll return a name, so lets continue if that's the case.
        if ($ie > 0) {
            // Select the Armory ID number where $item's is stored for $guild.
            $q = $db->query("SELECT `gaID`, `gaQTY` FROM `guild_armory` WHERE `gaGUILD` = {$guild} AND `gaITEM` = {$item} LIMIT 1");
            // Guild has an Armory ID for $item!
            if ($db->num_rows($q) > 0) {
                $r = $db->fetch_row($q);
                // $guild's $item quantity is greater than $qty, so remove only $qty and return true.
                if ($r['gaQTY'] > $qty) {
                    $db->query("UPDATE `guild_armory` SET `gaQTY` = `gaQTY` - {$qty} WHERE `gaID` = {$r['gaID']}");
                    return true;
                } 
                // $guild's $item quantity is lower than $qty, so delete the Armory ID entirely and return true.
                else {
                    $db->query("DELETE FROM `guild_armory` WHERE `gaID` = {$r['gaID']}");
                    return true;
                }
            }
        }
        return false;
    }
}
