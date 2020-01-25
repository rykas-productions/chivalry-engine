<?php
require('globals.php');
if (!isset($_GET['item'])) {
    $_GET['item'] = '';
}
switch ($_GET['item']) {
	case 'autohex':
        autohex();
        break;
	case 'autobor':
        autobor();
        break;
    case 'classreset':
        classreset();
        break;
    case 'skillreset':
        skillreset();
        break;
    case 'vipcolor':
        vipcolor();
        break;
	case 'willstim':
        willovercharge();
        break;
    default:
        alert("danger","Uh Oh!","Please specify a valid VIP Item to use!",true,'inventory.php');
		$h->endpage();
        break;
}
function autohex()
{
	global $db,$h,$api, $userid;
	if ($api->UserHasItem($userid,91))
	{
		$db->query("UPDATE `user_settings` SET `autohex` = `autohex` + 6000 WHERE `userid` = {$userid}");
		$api->UserTakeItem($userid,91,1);
		alert("success","Success!","Auto Hexbag Opener has been redeemed to your account.",true,'inventory.php');
		$api->SystemLogsAdd($userid, 'itemuse', "Used Auto Hexbag Opener.");
	}
	else
	{
		alert('danger',"Uh Oh!","You do not have the required item to use this page!",true,'inventory.php');
	}
	$h->endpage();
}
function autobor()
{
	global $db,$h,$api, $userid;
	if ($api->UserHasItem($userid,92))
	{
		$db->query("UPDATE `user_settings` SET `autobor` = `autobor` + 60000 WHERE `userid` = {$userid}");
		$api->UserTakeItem($userid,92,1);
		alert("success","Success!","Auto Boxes of Random Opener has been redeemed to your account.",true,'inventory.php');
		$api->SystemLogsAdd($userid, 'itemuse', "Used Auto Boxes of Random Opener.");
	}
	else
	{
		alert('danger',"Uh Oh!","You do not have the required item to use this page!",true,'inventory.php');
	}
	$h->endpage();
}
function classreset()
{
	global $db,$h,$api, $userid;
	if ($api->UserHasItem($userid,117))
	{
		$db->query("UPDATE `user_settings` SET `skillreset` = `skillreset` - 1 WHERE `userid` = {$userid}");
		$api->UserTakeItem($userid,117,1);
		alert("success","Success!","Class Reset Scroll has been redeemed to your account.",true,'inventory.php');
		$api->SystemLogsAdd($userid, 'itemuse', "Used Class Reset Scroll.");
	}
	else
	{
		alert('danger',"Uh Oh!","You do not have the required item to use this page!",true,'inventory.php');
	}
	$h->endpage();
}
function skillreset()
{
	global $db,$h,$api, $userid;
	if ($api->UserHasItem($userid,122))
	{
		$db->query("UPDATE `user_settings` SET `skillreset` = `skillreset` - 1 WHERE `userid` = {$userid}");
		$api->UserTakeItem($userid,122,1);
		alert("success","Success!","Skill Reset Scroll has been redeemed to your account.",true,'inventory.php');
		$api->SystemLogsAdd($userid, 'itemuse', "Used Skill Reset Scroll.");
	}
	else
	{
		alert('danger',"Uh Oh!","You do not have the required item to use this page!",true,'inventory.php');
	}
	$h->endpage();
}
function vipcolor()
{
    global $db,$userid,$api,$h,$ir;
    echo"<h3>Changing VIP Color</h3><hr />";
    if (!$api->UserHasItem($userid,128))
	{
        alert('danger',"Uh Oh!","You need at least one VIP Color Changer item to use this page.",true,'inventory.php');
        die($h->endpage());
    }
    if (isset($_POST['color']))
    {
        $validcolors=array('text-danger','text-success','text-primary','text-secondary','text-warning','text-info','blue_to_red','indigo_to_pink','green_to_yellow','light_to_dark');
        if (!in_array($_POST['color'],$validcolors))
        {
            alert('danger',"Uh Oh!","You specified an invalid VIP Color.");
            die($h->endpage());
        }
        if ($ir['vipcolor'] == $_POST['color'])
        {
            alert('danger',"Uh Oh!","Why would you want to change your VIP Color the to color you already use?");
            die($h->endpage());
        }
        $db->query("UPDATE `users` SET `vipcolor` = '{$_POST['color']}' WHERE `userid` = {$userid}");
        $api->UserTakeItem($userid,128,1);
        alert('success',"Success!","You have successfully changed your VIP Color!",true,'inventory.php');
        $api->SystemLogsAdd($userid, 'itemuse', "Used VIP Color Changer.");
    }
    else
    {
        echo "This VIP Item allows you to change the color of your name while you have VIP Days. Reminder that this item 
        may only be used once.Your color of choice may look different on other themes.<br />
        <hr />
        <b><u>VIP Colors on Other Themes</u></b><br />
        Default - <span class='text-danger'>{$ir['username']}</span><br />
        Option 1 - <span class='text-success'>{$ir['username']}</span><br />
        Option 2 - <span class='text-primary'>{$ir['username']}</span><br />
        Option 3 - <span class='text-secondary'>{$ir['username']}</span><br />
        Option 4 - <span class='text-warning'>{$ir['username']}</span><br />
        Option 5 - <span class='text-info'>{$ir['username']}</span><br />
        Option 6 - <span class='blue_to_red'>{$ir['username']}</span><br />
        Option 7 - <span class='indigo_to_pink'>{$ir['username']}</span><br />
        Option 8 - <span class='green_to_yellow'>{$ir['username']}</span><br />
        Option 9 - <span class='light_to_dark'>{$ir['username']}</span><br />
        <hr />
        Now what color would you like to use?
        <form method='post'>
            <select name='color' class='form-control' type='dropdown'>
                <option value='text-danger'>Default</option>
                <option value='text-success'>Option 1</option>
                <option value='text-primary'>Option 2</option>
                <option value='text-secondary'>Option 3</option>
                <option value='text-warning'>Option 4</option>
                <option value='text-info'>Option 5</option>
                <option value='blue_to_red'>Option 6</option>
                <option value='indigo_to_pink'>Option 7</option>
                <option value='green_to_yellow'>Option 8</option>
                <option value='light_to_dark'>Option 9</option>
            </select><br />
            <input type='submit' value='Change Color' class='btn btn-primary'>
        </form>";        
    }
    $h->endpage();
}
function willovercharge()
{
	global $db,$h,$api, $userid, $ir;
	if ($api->UserHasItem($userid,263))
	{
		if ($ir['will_overcharge'] < time())
			$startTime=time();
		else
			$startTime=$ir['will_overcharge'];
		$newTime=$startTime + (60*60)*3;
		$db->query("UPDATE `user_settings` SET `will_overcharge` = {$newTime} WHERE `userid` = {$userid}");
		$api->UserTakeItem($userid,263,1);
		alert("success","Success!","Will Stimulant Potion has been used. You have added 3 hours.",true,'inventory.php');
		$api->SystemLogsAdd($userid, 'itemuse', "Used Will Stimulant Potion.");
	}
	else
	{
		alert('danger',"Uh Oh!","You do not have the required item to use this page!",true,'inventory.php');
	}
	$h->endpage();
}