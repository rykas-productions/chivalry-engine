<?php
/*
	File: js//script/outputteam.php
	Created: 4/4/2017 at 7:10PM Eastern Time
	Info: PHP file for outputting info about the selected team
	when registering
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
if (isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD']))
{
    if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST')
    {
        // Ignore a GET request
        header('HTTP/1.1 400 Bad Request');
        exit;
    }
}
require_once('../../global_func.php');
if (!is_ajax())
{
    header('HTTP/1.1 400 Bad Request');
    exit;
}

require_once('../../globals_nonauth.php');
$class=$_POST['team'];
if ($class == 'Warrior')
{
	alert('info',$lang['REG_WARRIORCLASS'],$lang['REG_WARRIORCLASS_INFO'],false);
}
elseif ($class == 'Rogue')
{
	alert('info',$lang['REG_ROGUECLASS'],$lang['REG_ROGUECLASS_INFO'],false);
}
elseif ($class == 'Defender')
{
	alert('info',$lang['REG_DEFENDERCLASS'],$lang['REG_DEFENDERCLASS_INFO'],false);
}
else
{
	alert('danger',$lang['ERROR_GENERIC'],$lang['REG_NOCLASS'],false);
}
