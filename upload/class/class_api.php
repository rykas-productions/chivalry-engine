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
}