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
	case 'autobum':
        autobum();
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
        willovercharge2();
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
function autobum()
{
	global $db,$h,$api, $userid;
	if ($api->UserHasItem($userid,364))
	{
		$db->query("UPDATE `user_settings` SET `autobum` = `autobum` + 10000 WHERE `userid` = {$userid}");
		$api->UserTakeItem($userid,364,1);
		alert("success","Success!","Auto Street Begger has been redeemed to your account.",true,'inventory.php');
		$api->SystemLogsAdd($userid, 'itemuse', "Used Auto Street Begger.");
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
		preg_match_all("/^#(?>[[:xdigit:]]{3}){1,2}$/", $_POST['color'], $matches);
		$color=implode($matches[0]);
		if (empty($color))
		{
			alert('danger',"Uh Oh!","You have input an invalid HTML Color.");
            die($h->endpage());
		}
        if ($ir['vipcolor'] == $color)
        {
            alert('danger',"Uh Oh!","Why would you want to change your VIP Color the to color you already use?");
            die($h->endpage());
        }
        $db->query("UPDATE `users` SET `vipcolor` = '{$color}' WHERE `userid` = {$userid}");
        $api->UserTakeItem($userid,128,1);
        alert('success',"Success!","You have successfully changed your VIP Color!",true,'inventory.php');
        $api->SystemLogsAdd($userid, 'itemuse', "Used VIP Color Changer.");
    }
    else
    {
        echo "This VIP Item allows you to change the color of your name while you have VIP Days. Reminder that this item 
        may only be used once. Open the color wheel, and select the color you wish to have. Remember, this only accepts valid
		HTML colors.
        <form method='post'>
			<input type='color' name='color' class='form-control'>
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
function willovercharge2()
{
	global $db,$h,$api, $userid, $ir;
	if ($api->UserHasItem($userid,263))
	{
		alert('success','',"We've given you 50,000 Copper Coins, 500 Chivaly Tokens and 4 Chivalry Gym Scrolls for this Will Stim. Thank you!");
		$api->UserTakeItem($userid,263,1);
		$api->UserGiveItem($userid,18,4);
		$api->UserGiveCurrency($userid,'primary',50000);
		$api->UserGiveCurrency($userid,'secondary',500);
	}
	else
	{
		alert('danger',"Uh Oh!","You do not have the required item to use this page!",true,'inventory.php');
	}
	$h->endpage();
}