<?php
/*
	File: class/class_api.php
	Created: 11/10/2016 at 1:34PM Eastern Time
	Info: Creates a class file to use as an API for modders
	who don't wish to use the main game code!
	Author: TheMasterGeneral
	Website: http://mastergeneral156.pcriot.com/
*/
if (!defined('MONO_ON'))
{
    exit;
}
class api
{
	/*
		Returns the API version.
	*/
	function SystemReturnAPIVersion()
	{
		return "0.0.29";
	}
	/*
		Tests to see if specified user has at least the specified amount of money.
		@param int user = User ID to test for.
		@param int type = Currency type. 1 for Primary, 2 for secondary
		@param int money = Minimum money requied.
		Returns true if user has more cash than required.
		Returns false if user does not exist or does not have the minimum cash requred.
	*/
	function UserHasCurrency($user,$type,$minimum)
	{
		global $db;
		$user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
		$minimum = (isset($minimum) && is_numeric($minimum)) ? abs(intval($minimum)) : 0;
		$type = (isset($type) && is_numeric($type)) ? abs(intval($type)) : 0;
		$userexist=$db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$user}"));
		if ($userexist)
		{
			if ($type == 1)
			{
				$UserMoney=$db->fetch_single($db->query("SELECT `primary_currency` FROM `users` WHERE `userid` = {$user}"));
				if ($UserMoney < $minimum)
				{
					return false;
				}
				else
				{
					return true;
				}
			}
			elseif ($type == 2)
			{
				$UserMoney=$db->fetch_single($db->query("SELECT `secondary_currency` FROM `users` WHERE `userid` = {$user}"));
				if ($UserMoney < $minimum)
				{
					return false;
				}
				else
				{
					return true;
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	/*
		Tests to see if specified user has at least the specified amount of money.
		@param int user = User ID to test for.
		@param int item = Item ID to give to the user.
		@param int quantity = Quantity of item to give to the user.
		Returns true if item successfully given to the user.
		Returns false if item failed to be given to user.
	*/
	function UserGiveItem($user,$item,$quantity)
	{
		global $db;
		$user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
		$item = (isset($item) && is_numeric($item)) ? abs(intval($item)) : 0;
		$quantity = (isset($quantity) && is_numeric($quantity)) ? abs(intval($quantity)) : 0;
		if (item_add($user,$item,$quantity))
		{
			return true;
		}
		else
		{
			return false;
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
	function UserTakeItem($user,$item,$quantity)
	{
		global $db;
		$user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
		$item = (isset($item) && is_numeric($item)) ? abs(intval($item)) : 0;
		$quantity = (isset($quantity) && is_numeric($quantity)) ? abs(intval($quantity)) : 0;
		if (item_remove($user,$item,$quantity))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	/*
		Gives user specified amount of currency type.
		@param int user = User ID to give currency to.
		@param int type = Currency type. 1 for Primary, 2 for secondary
		@param int money = Currency given.
		Returns true if user has received currency.
		Returns false if user does not receive currency.
	*/
	function UserGiveCurrency($user,$type,$quantity)
	{
		global $db;
		$user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
		$type = (isset($type) && is_numeric($type)) ? abs(intval($type)) : 0;
		$quantity = (isset($quantity) && is_numeric($quantity)) ? abs(intval($quantity)) : 0;
		$userexist=$db->fetch_single($db->query("SELECT `username` FROM `users` WHERE `userid` = {$user}"));
		if ($userexist)
		{
			if ($type == 1)
			{
				$db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + {$quantity} WHERE `userid` = {$user}");
				return true;
			}
			elseif ($type == 2)
			{
				$db->query("UPDATE `users` SET `secondary_currency` = `secondary_currency` + {$quantity} WHERE `userid` = {$user}");
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
		
	}
	/*
		Tests to see what the user has equipped.
		@param int user = User ID to test against.
		@param int slot = Equipment slot to test. 1 = Primary, 2 = Secondary, 3 = Armor
		@param int itemid = Item to test for. -1 = Any Item, 0 = No Item Equipped, >0 = Specific item
		Returns true if user has item equipped
		Returns false if user does not have item equipped.
	*/
	function UserEquippedItem($user,$slot,$itemid=-1)
	{
		global $db;
		$user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
		if ($slot == 1)
		{
			//Any item equipped
			if ($itemid == -1)
			{
				$equipped=$db->fetch_single($db->query("SELECT `equip_primary` FROM `users` WHERE `userid` = {$user}"));
				if ($equipped > 0)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			//Specific item equipped
			elseif ($itemid > 0)
			{
				$itemid = (isset($itemid) && is_numeric($itemid)) ? abs(intval($itemid)) : 0;
				$equipped=$db->fetch_single($db->query("SELECT `equip_primary` FROM `users` WHERE `userid` = {$user}"));
				if ($equipped == $itemid)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			//Nothing equipped
			elseif ($itemid == 0)
			{
				$equipped=$db->fetch_single($db->query("SELECT `equip_primary` FROM `users` WHERE `userid` = {$user}"));
				if ($equipped == 0)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		elseif ($slot == 2)
		{
			//Any item equipped
			if ($itemid == -1)
			{
				$equipped=$db->fetch_single($db->query("SELECT `equip_secondary` FROM `users` WHERE `userid` = {$user}"));
				if ($equipped > 0)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			//Specific item equipped
			elseif ($itemid > 0)
			{
				$itemid = (isset($itemid) && is_numeric($itemid)) ? abs(intval($itemid)) : 0;
				$equipped=$db->fetch_single($db->query("SELECT `equip_secondary` FROM `users` WHERE `userid` = {$user}"));
				if ($equipped == $itemid)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			//Nothing equipped
			elseif ($itemid == 0)
			{
				$equipped=$db->fetch_single($db->query("SELECT `equip_secondary` FROM `users` WHERE `userid` = {$user}"));
				if ($equipped == 0)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		elseif ($slot == 3)
		{
			//Any item equipped
			if ($itemid == -1)
			{
				$equipped=$db->fetch_single($db->query("SELECT `equip_armor` FROM `users` WHERE `userid` = {$user}"));
				if ($equipped > 0)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			//Specific item equipped
			elseif ($itemid > 0)
			{
				$itemid = (isset($itemid) && is_numeric($itemid)) ? abs(intval($itemid)) : 0;
				$equipped=$db->fetch_single($db->query("SELECT `equip_armor` FROM `users` WHERE `userid` = {$user}"));
				if ($equipped == $itemid)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			//Nothing equipped
			elseif ($itemid == 0)
			{
				$equipped=$db->fetch_single($db->query("SELECT `equip_armor` FROM `users` WHERE `userid` = {$user}"));
				if ($equipped == 0)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	/*
		Tests the inputted user to see if they're in the dungeon or infirmary
		@param int user = User ID to test against.
		@param int status = Place to test. 1 = Infirmary, 2 = Dungeon
		Returns true if user is in the dungeon/infirmary
		Returns false if user is not in the dungeon/infirmary
	*/
	function UserStatus($user,$status)
	{
		$user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
		if ($status == 1)
		{
			user_infirmary($user);
		}
		elseif ($status == 2)
		{
			user_dungeon($user);
		}
		else
		{
			return false;
		}
	}
	/*
		Places or removes dungeon/infirmary time on the specified user.
		@param int user = User ID to test against.
		@param int place = Place to test. 1 = Infirmary, 2 = Dungeon
		@param int time = Minutes user is in infirmary/dungeon.
		@param text reason = Reason why user is in the infirmary/dungeon.
		Returns true if user is placed in the infirmary/dungeon, or is removed from it.
		Returns false otherwise.
	*/
	function UserStatusSet($user,$place,$time,$reason)
	{
		global $db;
		$user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
		$reason=$db->escape(str_replace("\n", "<br />", strip_tags(stripslashes($reason))));
		if ($place == 1)
		{
			if ($time >= 0)
			{
				$time = (isset($time) && is_numeric($time)) ? abs(intval($time)) : 0;
				put_infirmary($user,$time,$reason);
				return true;
			}
			elseif ($time < 0)
			{
				$time = (isset($time) && is_numeric($time)) ? abs(intval($time)) : 0;
				remove_infirmary($user,$time);
				return true;
			}
		}
		elseif ($place == 2)
		{
			if ($time >= 0)
			{
				$time = (isset($time) && is_numeric($time)) ? abs(intval($time)) : 0;
				put_dungeon($user,$time,$reason);
				return true;
			}
			elseif ($time < 0)
			{
				$time = (isset($time) && is_numeric($time)) ? abs(intval($time)) : 0;
				remove_dungeon($user,$time);
				return true;
			}
		}
		else
		{
			return false;
		}
	}
	/*
		Adds a notification for the specified user.
		@param int user = User ID to send notification to.
		@param text text = Notification text.
		Returns true always.
	*/
	function GameAddNotification($user,$text)
	{
		event_add($user,$text);
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
	function GameAddMail($user,$subj,$msg,$from)
	{
		global $db;
		$user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
		$from = (isset($from) && is_numeric($from)) ? abs(intval($from)) : 0;
		$subj = $db->escape(str_replace("\n", "<br />",strip_tags(stripslashes($subj))));
		$msg = $db->escape(str_replace("\n", "<br />",strip_tags(stripslashes($msg))));
		$time = time();
		$userexist = $db->query("SELECT `userid` FROM `users` WHERE `userid` =  {$user}");
		if ($db->num_rows($userexist) == 0)
		{
			$db->free_result($userexist);
			return false;
		}
		else
		{
			$db->free_result($userexist);
			$userexist = $db->query("SELECT `userid` FROM `users` WHERE `userid` =  {$from}");
			if ($db->num_rows($userexist) == 0)
			{
				$db->free_result($userexist);
				return false;
			}
			else
			{
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
	function GameAddAnnouncement($text,$poster = 1)
	{
		global $db;
		$text = $db->escape(str_replace("\n", "<br />",strip_tags(stripslashes($text))));
		$poster = (isset($poster) && is_numeric($poster)) ? abs(intval($poster)) : 1;
		$time = time();
		$userexist = $db->query("SELECT `userid` FROM `users` WHERE `userid` =  {$poster}");
		if ($db->num_rows($userexist) == 0)
		{
			$db->free_result($userexist);
			return false;
		}
		else
		{
			$db->query("INSERT INTO `announcements` 
			(`ann_text`, `ann_time`, `ann_poster`) 
			VALUES 
			('{$text}', '{$time}', '{$poster}');");
			$db->query("UPDATE `users` SET `announcements` = `announcements` + 1");
			return true;
		}
	}
	/*
		Get the user's member level. Can test for exact member level, or if user is above specified member level.
		@param int user = User to test on.
		@param text level = Member level to test for. [Valid: npc, member, web dev, forum moderator, assistant, admin]
		@param boolean exact = Return true if ranked ONLY specified level. [Default: false]
		Returns true if user is exactly or equal to/above specified member level. False if not.
	*/
	function UserMemberLevelGet($user,$level,$exact=false)
	{
		global $db;
		$level = $db->escape(str_replace("\n", "<br />",strip_tags(stripslashes(strtolower($level)))));
		$user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
		if ($user == 0)
		{
			return false;
		}
		else
		{
			$userexist = $db->query("SELECT `userid` FROM `users` WHERE `userid` =  {$user}");
			if ($db->num_rows($userexist) == 0)
			{
				$db->free_result($userexist);
				return false;
			}
			else
			{
				$ulevel=$db->fetch_single($db->query("SELECT `user_level` FROM `users` WHERE `userid` = {$user}"));
				if ($exact == true)
				{
					if ($level == $ulevel)
					{
						return true;
					}
				}
				else
				{
					if ($level == 'member')
					{
						if ($ulevel == 'Member' || $ulevel == 'Forum Moderator' || $ulevel == 'Assistant'
							 || $ulevel == 'Web Developer' || $ulevel == 'Admin')
						{
							return true;
						}
					}
					elseif ($level == 'forum moderator')
					{
						if ($ulevel == 'Forum Moderator' || $ulevel == 'Assistant' || $ulevel == 'Web Developer' || $ulevel == 'Admin')
						{
							return true;
						}
					}
					elseif ($level == 'assistant')
					{
						if ($ulevel == 'Assistant' || $ulevel == 'Web Developer' || $ulevel == 'Admin')
						{
							return true;
						}
					}
					elseif ($level == 'web dev')
					{
						if ($ulevel == 'Web Developer' || $ulevel == 'Admin')
						{
							return true;
						}
					}
					elseif ($level == 'npc')
					{
						if ($ulevel == 'Member' || $ulevel == 'NPC' || $ulevel == 'Forum Moderator' || $ulevel == 'Assistant'
							 || $ulevel == 'Web Developer' || $ulevel == 'Admin')
						{
							return true;
						}
					}
					elseif ($level == 'admin')
					{
						if ($ulevel == 'Admin')
						{
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
	function UserHasItem($user,$item,$qty=1)
	{
		global $db;
		$user = (isset($user) && is_numeric($user)) ? abs(intval($user)) : 0;
		$item = (isset($item ) && is_numeric($item)) ? abs(intval($item)) : 0;
		$qty = (isset($qty) && is_numeric($qty)) ? abs(intval($qty)) : 0;
		if ($user == 0 || $item == 0 || $qty == 0)
		{
			return false;
		}
		else
		{
			$i=$db->fetch_single($db->query("SELECT `inv_qty` FROM `inventory` WHERE `inv_userid` = {$user} && `inv_itemid` = {$item}"));
			if ($qty == 1)
			{
				if ($i >= 1)
				{
					return true;
				}
			}
			else
			{
				if ($i >= $qty)
				{
					return true;
				}
			}
		}
	}
}