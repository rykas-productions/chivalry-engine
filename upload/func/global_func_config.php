<?php
/*	File:		global_func_config.php
	Created: 	Jul 15, 2021; 10:02:52 PM
	Info: 		Functions for module configuration.
	Author:		TheMasterGeneral
	Website: 	https://chivalryisdeadgame.com/
*/
//Back ported from Chivalry Engine V3
function readConfigFromDB(string $moduleName)
{
    global $db;
    $q=$db->query("SELECT `setting_value` FROM `settings` WHERE `setting_name` = '{$moduleName}_config'");
    if ($db->num_rows($q) == 0)
        return false;
    else
        return $db->fetch_single($q);
            
}
//Back ported from Chivalry Engine V3
function writeConfigToDB(string $moduleName, $configJson)
{
    global $db;
    $q=$db->query("SELECT `setting_value` FROM `settings` WHERE `setting_name` = '{$moduleName}_config'");
    if ($db->num_rows($q) == 0)
        $db->query("INSERT INTO `settings` (`setting_name`, `setting_value`) VALUES ('{$moduleName}_config', '{$configJson}')");
    else
        $db->query("UPDATE `settings` SET `setting_value` = '{$configJson}' WHERE `setting_name` = '{$moduleName}_config'");
}
/**
 * @desc Internal function to properly format JSON objects to the string used for the database storage.
 * @param string $string Config string to encode in JSON.
 * @return string Config JSON
 */
//Back ported from Chivalry Engine V3
function formatConfig(array $string)
{
    return json_encode($string, JSON_FORCE_OBJECT);
}

/**
 * @desc Internal function to properly remove JSON formatting.
 * @param array $json json object to turn into a string.
 * @return array Config String
 */
//Back ported from Chivalry Engine V3
function unformatConfig(array $json)
{
    return json_decode($json, true);
}

/**
 * @desc Internal function to properly get module config info for use in PHP.
 * @param string $moduleName Name of the module.
 * @return string $moduleConfig;
 */
//Back ported from Chivalry Engine V3
function getConfigForPHP(string $moduleName)
{
    return unformatConfig(readConfigFromDB($moduleName));
}