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
	case 'contact':
	    contact();
	    break;
	case 'autominer':
	    autominer();
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
        $api->UserTakeItem($userid, 424, 1);
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

function contact()
{
    global $h,$api, $userid;
    if (!$api->UserHasItem($userid,407))
    {
        alert('danger',"Uh Oh!","You do not have the required item to be here.", true, 'inventory.php');
        die($h->endpage());
    }
    else
    {
        if (isset($_POST['contact']))
        {
            if (empty($_POST['contact']))
            {
                alert('danger',"Uh Oh!","Please fill out the form completely.", true);
                die($h->endpage());
            }
            sendEmergencyEmail($userid, $_POST['contact']);
            alert('success',"Success!","You've successfully contacted CID Admin.", true, 'inventory.php');
        }
        else 
        {
            echo"
				<form method='post'>
                    Use this form to contact CID Admin in a more direct way. Do not abuse this form. Only select players will have access to this form.
				<hr />
				<div class='row'>
					<div class='col-md-2 col-sm-3 col-6'>
						<b>Response</b>
					</div>
					<div class='col-md-10 col-sm-9'>
						<textarea class='form-control' required='1' maxlength='65655' name='contact'></textarea>
					</div>
				</div>
				<br />
				<div class='row'>
					<div class='col'>
						<button class='btn btn-primary btn-block' type='submit'><i class='fas fa-reply'></i> Submit Emergency</button>
					</div>
				</div>
				</form>";
        }
    }
    $h->endpage();
}

function autominer()
{
    global $h, $api, $userid, $db, $ir;
    $itmName = $api->SystemItemIDtoName(424);
    if (!$api->UserHasItem($userid,424))
    {
        alert('danger',"Uh Oh!","You need at least one {$itmName} to use this page.", true, 'inventory.php');
        die($h->endpage());
    }
    else
    {
        if (isset($_POST['mine']))
        {
            $mine = (isset($_POST['mine']) && is_numeric($_POST['mine'])) ? abs(intval($_POST['mine'])) : '';
            if (empty($mine))
            {
                alert('danger',"Uh Oh!","Please fill out the form completely.");
                die($h->endpage());
            }
            $q = $db->query("SELECT * FROM `mining_data` WHERE `mine_id` = {$mine}");
            if ($db->num_rows($q) == 0) 
            {
                
                alert('danger', "Uh Oh!", "You are trying to edit a non-existent mine.");
                die($h->endpage());
            }
            $r = $db->fetch_row($q);
            if (getUserMiningLevel($userid) < $r['mine_level']) 
            {
                alert('danger', "Uh Oh!", "You need a mining level of {$r['mine_level']} before you can place a Powered Miner here.");
                die($h->endpage());
            }
            $iqRequired = calcMineIQ($userid, $mine);
            if ($ir['iq'] < $iqRequired)
            {
                alert('danger', "Uh Oh!", "You need at least " . shortNumberParse($iqRequired) . " IQ to place a Powered Miner here. You only have " . shortNumberParse($ir['iq']) . " IQ.");
                die($h->endpage());
            }
            $q2 = $db->query("SELECT * FROM `mining_auto` WHERE `userid` = {$userid} AND `miner_location` = {$mine}");
            if ($db->num_rows($q2) > 0)
            {
                alert('danger', "Uh Oh!", "You cannot place more than one Powered Miner at a mine.");
                die($h->endpage());
            }
            $db->query("INSERT INTO `mining_auto` (`userid`, `miner_location`, `miner_time`) VALUES ('{$userid}', '{$mine}', '300')");
            alert('success',"Success!","You have succesfully placed a Powered Miner. It will self-destruct in about 5 hours.", true, 'inventory.php');
            $api->UserTakeItem($userid, 424, 1);
        }
        else
        {
            echo"
				<form method='post'>
                    Select the mine you wish to place your {$itmName} at. You may only have one {$itmName} per mine. {$itmName}s only mine once 
                    per minute, and may jam occasionally. If one of any of your {$itmName} jam, they all stop working until the one is unjammed. 
                    This won't give you mining experience, or dungeon and infirmary time. Just resources. {$itmName} last for a total of 5 hours.
                    Note, {$itmName} only lose durability when they mine. If they aren't mining, they won't lose durability...
				<hr />
				<div class='row'>
					<div class='col-md-2 col-sm-3 col-6'>
						<b>Mine</b>
					</div>
					<div class='col-md-10 col-sm-9'>
						" . mines_dropdown("mine") . "
					</div>
				</div>
				<br />
				<div class='row'>
					<div class='col'>
						<button class='btn btn-primary btn-block' type='submit'><i class='fas fa-reply'></i> Submit Emergency</button>
					</div>
				</div>
				</form>";
        }
    }
    $h->endpage();
}