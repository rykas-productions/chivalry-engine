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
	function __construct() {
		include('class_api_user.php');
		include('class_api_guild.php');
	}
    /*
        Returns the API version.
    */
    function SystemReturnAPIVersion()
    {
        return "19.2.1";    //Last Updated 2/8/2019
    }

    /*
        Adds a notification for the specified user.
        @param int user = User ID to send notification to.
        @param text text = Notification text.
        Returns true always.
    */
    function GameAddNotification($user, $text)
    {
        notification_add($user, $text);
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
        $userexist = $db->query("SELECT `userid` FROM `users` WHERE `userid` =  {$poster}");
        if ($db->num_rows($userexist) > 0) {
            $db->query("INSERT INTO `announcements`
			(`ann_text`, `ann_time`, `ann_poster`) 
			VALUES 
			('{$text}', '{$time}', '{$poster}');");
            $db->query("UPDATE `users` SET `announcements` = `announcements` + 1");
            return true;
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
		trigger_error("Deprecation notice.");
        $user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
        $item = (isset($item) && is_numeric($item)) ? abs(intval($item)) : 0;
        $qty = (isset($qty) && is_numeric($qty)) ? abs(intval($qty)) : 0;
        if ($user > 0 || $item > 0 || $qty > 0) {
            $i = $db->fetch_single($db->query("SELECT `inv_qty` FROM `inventory` WHERE `inv_userid` = {$user} && `inv_itemid` = {$item}"));
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
        $name = $db->query("SELECT `username` FROM `users` WHERE `userid` = {$user}");
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
        $id = $db->query("SELECT `userid` FROM `users` WHERE `username` = '{$name}'");
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
        $name = $db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$itemid}");
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
        $id = $db->query("SELECT `itmid` FROM `items` WHERE `itmname` = '{$name}'");
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
        $name = $db->query("SELECT `town_name` FROM `town` WHERE `town_id` = {$id}");
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
        $user_currency = $db->fetch_single($db->query("SELECT `{$curr}_currency` FROM `users` WHERE `userid` = {$user}"));
        if ($user_currency > $cost * $qty) {
            if ($api->SystemItemIDtoName($item)) {
                $api->UserGiveItem($user, $item, $qty);
                $db->query("UPDATE `users` SET `{$curr}_currency` = `{$curr}_currency` - {$cost} WHERE `userid` = {$user}");
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
                $s = $db->fetch_row($db->query("SELECT `lastip`,`loginip`,`registerip` FROM `users` WHERE `userid` = {$user1}"));
                $r = $db->fetch_row($db->query("SELECT `lastip`,`loginip`,`registerip` FROM `users` WHERE `userid` = {$user2}"));
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
            $qty = $db->fetch_single($db->query("SELECT SUM(`inv_qty`) FROM `inventory` WHERE `inv_itemid` = {$itemid} AND `inv_userid` = {$userid}"));
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
        $multiplier = (isset($multiplier) && is_numeric($multiplier)) ? abs(intval($multiplier)) : 1;
        //Return empty if the call isn't complete.
        if (empty($userid) || (empty($stat)) || (empty($times))) {
            return 0;
        }
        $StatArray = array("strength", "agility", "guard", "labor", "iq");
        if (!in_array($stat, $StatArray)) {
            return -1;
        }
        $udq = $db->query("SELECT * FROM `users` WHERE `userid` = {$userid}");
        $userdata = $db->fetch_row($udq);
        $gain = 0;
        //Do while value is less than the user's energy input, then add one to value.
        for ($i = 0; $i < $times; $i++) {
            //(1-4)/(600-1000)*(500-1000)*((User's Will+25)/175)
            $gain +=
                Random(1, 4) / Random(600, 1000) * Random(500, 1000) * (($userdata['will'] + 25) / 175);
            //Subtract a random number from user's will.
            $userdata['will'] -= Random(1, 3);
            //User's will ends up negative, set to zero.
            if ($userdata['will'] < 0) {
                $userdata['will'] = 0;
            }
        }
        //User's class is warrior
        if ($userdata['class'] == 'Warrior') {
            //Trained stat is strength, double its output.
            if ($stat == 'strength') {
                $gain *= 2;
            }
            //Trained stat is guard, half its output.
            if ($stat == 'guard') {
                $gain /= 2;
            }
        }
        //User's class is Rogue.
        if ($userdata['class'] == 'Rogue') {
            //Trained stat is agility, double its output.
            if ($stat == 'agility') {
                $gain *= 2;
            }
            //Trained stat is strength, half its output.
            if ($stat == 'strength') {
                $gain /= 2;
            }
        }
        //User's class is Defender.
        if ($userdata['class'] == 'Defender') {
            //Trained stat is guard, double its output.
            if ($stat == 'guard') {
                $gain *= 2;
            }
            //Trained stat is agility, half its output.
            if ($stat == 'agility') {
                $gain /= 2;
            }
        }
        //Add multiplier, if needed.
        $gain *= $multiplier;
        //Round the gained stats.
        $gain = floor($gain);
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
        $headers[] = "From: {$from}";
        return mail($to, $subject, $body, implode("\r\n", $headers));
    }
}