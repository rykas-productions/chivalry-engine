<?php
/*
	File:		functions/func_config.php
	Created: 	9/29/2019 at 9:40PM Eastern Time
	Author:		TheMasterGeneral
	Website: 	https://github.com/rykas-productions/chivalry-engine
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
function readConfigFromDB($moduleName)
{
	global $db;
	$q=$db->query("SELECT `setting_value` FROM `game_settings` WHERE `setting_name` = '{$moduleName}_config'");
	if ($db->num_rows($q) == 0)
		return false;
	else
		return $db->fetch_single($q);
	
}
function writeConfigToDB($moduleName, $configJson)
{
	global $db;
	$q=$db->query("SELECT `setting_value` FROM `game_settings` WHERE `setting_name` = '{$moduleName}_config'");
	if ($db->num_rows($q) == 0)
		$db->query("INSERT INTO `game_settings` (`setting_name`, `setting_value`) VALUES ('{$moduleName}_config', '{$configJson}')");
	else
		$db->query("UPDATE `game_settings` SET `setting_value` = '{$configJson}' WHERE `setting_name` = '{$moduleName}_config'");
}
function readConfigFromFile($moduleName)
{
	
}
function writeConfigToFile($moduleName, $configJson)
{
	
}
function formatConfig($string)
{
	return json_encode($string, JSON_FORCE_OBJECT);
}
function unformatConfig($json)
{
	return json_decode($json, true);
}
function getConfigForPHP($moduleName)
{
	return unformatConfig(readConfigFromDB($moduleName));
}