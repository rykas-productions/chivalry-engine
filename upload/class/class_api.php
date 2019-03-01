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