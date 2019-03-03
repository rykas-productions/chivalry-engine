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
        include('class_api_game.php');
	}
}