<?php
/*
	File: 		class/class_api_game.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Numerous API calls relating to general game actions.
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

class game
{
    /*
        Returns the API version.
    */
    function returnAPIVersion()
    {
        return "19.3.1";    //Last Updated 3/3/2019
    }
    /*
        Adds an in-game announcement.
        @param text text = Announcement text.
        @param int poster = User ID of poster. Optional. [Defaults = 1]
        Returns true when announcement is made. False if fail.
    */
    function createAnnouncement(string $text, int $poster = 1)
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
    function addLog(int $user, string $logtype, string $input)
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
    function getItemNameFromID(int $itemid)
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
    function getItemIDfromName(string $name)
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
    function getTownNameFromID(int $id)
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
     * Function to send a game email
     * @param email to = Email address so send email to.
     * @param text body = Body of the email.
     * @param text subject = Subject of the email. [Optional, Default = "Gamename Game Email"]
     * @param email from = Email account sender [Optional, Default = "Game Sending Email"]
     * Returns Email was sent successfully.
    */
    function sendEmail(string $to, string $body, string $subject = '', string $from = '')
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