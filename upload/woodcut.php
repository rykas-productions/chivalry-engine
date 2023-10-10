<?php
//INSERT INTO `settings` (`setting_id`, `setting_name`, `setting_value`) VALUES (NULL, 'cutter_level', '1'), (NULL, 'cutter_upgrade', '0'), (NULL, 'cutter_capacity', '5000'), (NULL, 'cutter_capacity_max', '5000'), (NULL, 'cutter_cost', '1250')
//INSERT INTO `settings` (`setting_id`, `setting_name`, `setting_value`) VALUES (NULL, 'cutter_capacity', '5000'), (NULL, 'cutter_capacity_max', '5000')
//INSERT INTO `settings` (`setting_id`, `setting_name`, `setting_value`) VALUES (NULL, 'cutter_cost', '1250')
$macropage = ('woodcut.php');
require('globals.php');
echo "<h3>Wood Cutter</h3><hr />";
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
    alert('danger',"Uh Oh!","You cannot get your wood cut while at the dungeon or infirmary.",true,'explore.php');
    die($h->endpage());
}
$set['cutter_xp_needed'] = 25000 * (($set['cutter_level'] * 1.1) + ($set['cutter_level'] * 1.1));
$frmeen = min(round($set['cutter_capacity'] / $set['cutter_capacity_max'] * 100), 100);
$frmexp = min(round($set['cutter_upgrade'] / $set['cutter_xp_needed'] * 100), 100);
cutterUpgrade();
echo "<div class='card'>
        <div class='card-body'>
        <div class='row'>
        <div class='col-md-4' align='left'>
			Cutter Capacity - <span id='capacityPercent'>{$frmeen}%</span><br />
		</div>
		<div class='col-md'>
			<div class='progress' style='height: 1rem;'>
				<div class='progress-bar bg-success progress-bar-striped progress-bar-animated' role='progressbar' id='woodCutBar' aria-valuenow='{$set['cutter_capacity']}' aria-valuemin='0' aria-valuemax='100' style='width:{$frmeen}%'>
					<span id='woodCutInfo'>
						{$frmeen}% (" . number_format($set['cutter_capacity']) . " / " . number_format($set['cutter_capacity_max']) . ")
					</span>
				</div>
			</div>
		</div>
        </div>
        <div class='row'>
        <div class='col-md-4' align='left'>
			Cutter Upgrade - Level " . number_format($set['cutter_level']) . "<br />
		</div>
		<div class='col-md'>
			<div class='progress' style='height: 1rem;'>
				<div class='progress-bar bg-success progress-bar-striped progress-bar-animated' role='progressbar' id='woodCutUpgradeBar' aria-valuenow='{$set['cutter_upgrade']}' aria-valuemin='0' aria-valuemax='100' style='width:{$frmexp}%'>
					<span id='woodCutUpgradeInfo'>
						{$frmexp}% (" . number_format($set['cutter_upgrade']) . " / " . number_format($set['cutter_xp_needed']) . ")
					</span>
				</div>
			</div>
		</div>
	</div>
    </div>
    </div>
    <br />";
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'cut':
        cutwood();
        break;
    case 'upgrade':
        upgrade();
        break;
    default:
        home();
        break;
}

function home()
{
    global $db, $userid, $api, $h, $set;
    $logs = 25 + (round($set['cutter_level'] / 10));
    $userLogs = $api->UserCountItem($userid, 410);
    //@todo grid the butyons here.
    echo "<div class='card'>
        <div class='card-body'>
            Here you may chop up your wood into sharpened sticks, if you so desire. You will receive " . shortNumberParse($logs) . " Sharpened Sticks per Log. 
            It currently costs " . shortNumberParse($set['cutter_cost']) . " Copper Coins per log chopped.
            The maximum that can be cut at a time is set by the cutter's capacity. Each log cut takes one capacity away. Every player 
            in the game contributes to this number. Players may all contribute to upgrading this as well.<br />
            <b>How many logs would you like to chop into sharpened sticks?</b>
            <br />
            <form method='post' action='?action=cut'>
                <div class='row'>
                    <div class='col-12'>
                        <input type='number' name='logs' value='{$userLogs}' min='1' max='{$userLogs}' required='1' class='form-control'><br />
                    </div>
                    <div class='col-12'>
                        <input type='submit' value='Cut Logs' class='btn btn-primary btn-block'><br />
                    </div>
                    <div class='col-12'>
                        <a href='?action=upgrade' class='btn btn-block btn-success'>Upgrade Cutter</a>
                    </div>
                </div>
            </form>
        </div>
    </div>";
}

function cutwood()
{
    global $db, $userid, $api, $h, $set, $ir;
    $log = (isset($_POST['logs']) && is_numeric($_POST['logs'])) ? abs($_POST['logs']) : '';
    $woodLogID = $api->SystemItemNametoID("Wood Log");
    $woodCount = $api->UserCountItem($userid, $woodLogID);
    if (userHasEffect($userid, constant("wood_cut_cooldown")))
    {
        $nextTime = returnEffectDone($userid, constant("wood_cut_cooldown"));
        alert('danger', "Uh Oh!", "You cannot use the wood cutter for another " . TimeUntil_Parse($nextTime) . ".", false);
        die(home());
    }
    if (empty($log))
    {
        alert('danger',"Uh Oh!","Please input a valid number of logs to chop.", false);
        die(home());
    }
    if ($woodCount < $log)
    {
        alert('danger',"Uh Oh!","You only have " . shortNumberParse($woodCount) . " Wood Logs to cut. You cannot cut " . number_format($log) . " Wood Logs.", false);
        die(home());
    }
    if (($set['cutter_capacity'] - $log) < 0)
    {
        alert('danger',"Uh Oh!","The Wood Cutter does not have enough free capacity to cut " . shortNumberParse($log) . " Wood Logs.", false);
        die(home());
    }
    $totalCost = $set['cutter_cost'] * $log;
    if (!$api->UserHasCurrency($userid, "primary", $totalCost))
    {
        alert('danger',"Uh Oh!","You need " . shortNumberParse($totalCost) . " Copper Coins to cut " . shortNumberParse($log) . " Wood Logs. You only have " . shortNumberParse($ir['primary_currency']) . " Copper Coins.", false);
        die(home());
    }
    $quantity = $log * (25 + (round($set['cutter_level'] / 10)));
    $api->UserTakeItem($userid, $woodLogID, $log);
    $api->UserGiveItem($userid, 1, $quantity);
    $api->UserTakeCurrency($userid, "primary", $totalCost);
    $db->query("UPDATE `settings` SET `setting_value` = `setting_value` - {$log} WHERE `setting_name` = 'cutter_capacity'");
    userGiveEffect($userid,  constant("wood_cut_cooldown"), ($log*15));
    alert('success',"Success!", "You have sucessfully traded " . shortNumberParse($totalCost) . " Copper Coins to chop " . shortNumberParse($log) . " Wood Logs into " . shortNumberParse($quantity) . " Sharpened Sticks.", false);
    addToEconomyLog('Wood Cutter', 'copper', $totalCost * -1);
    home();
}

function upgrade()
{
    global $db, $userid, $api, $h, $set, $ir;
    if (!isset($_POST['tokens']))
    {
        echo "<div class='card'>
            <div class='card-body'>
                All warriors from around the kingdom contribute Chivalry Tokens to the Wood Cutter, to increase its cutting capacity.
                Cutting capacity is what allows warriors to continue to cut wood, as its shared amongs all warriors. At level 
                " . shortNumberParse($set['cutter_level'] + 1) . ", an additional " . shortNumberParse($set['cutter_capacity_max']*0.1256) . " cutting capacity will be gained for the kindom.
                <form method='post'>
                <div class='row'>
                    <div class='col-12'>
                        <div class='row'>
                            <div class='col-12'>
                                <small>Chivalry Tokens</small>
                            </div>
                            <div class='col-12'>
                                <input type='number' class='form-control' required='1' value='{$ir['secondary_currency']}' min='1' max='{$ir['secondary_currency']}' name='tokens'><br />
                            </div>
                        </div>
                    </div>
                    <div class='col-12'>
                        <input type='submit' value='Contribute' class='btn btn-block btn-primary'>
                    </div>
                </div>
                </form>
            </div>
        </div>
        ";
    }
    else
    {
        $tokens = (isset($_POST['tokens']) && is_numeric($_POST['tokens'])) ? abs($_POST['tokens']) : '';
        if (empty($tokens))
        {
            alert('danger',"Uh Oh!","Invalid input detected.", false);
            die(home());
        }
        if ($ir['secondary_currency'] < $tokens)
        {
            alert('danger', "Uh Oh!", "You cannot contribute " . shortNumberParse($tokens) . " Chivalry Tokens as you only have " . shortNumberParse($ir['secondary_currency']) . " Chivalry Tokens.", false);
            die(home());
        }
        $db->query("UPDATE `settings` SET `setting_value` = `setting_value` + {$tokens} WHERE `setting_name` = 'cutter_upgrade'");
        $api->UserTakeCurrency($userid, "secondary", $tokens);
        alert('success',"Success!","You have successfully contributed " . shortNumberParse($tokens) . " Chivalry Tokens to the Wood Cutter.",false);
        addToEconomyLog('Wood Cutter', 'token', $tokens * -1);
        home();
    }
}

function cutterUpgrade()
{
    global $db, $set;
    if ($set['cutter_upgrade'] >= $set['cutter_xp_needed'])
    {
        $db->query("UPDATE `settings` SET `setting_value` = `setting_value` - {$set['cutter_xp_needed']} WHERE `setting_name` = 'cutter_upgrade'");
        $db->query("UPDATE `settings` SET `setting_value` = `setting_value` + 1 WHERE `setting_name` = 'cutter_level'");
        $db->query("UPDATE `settings` SET `setting_value` = `setting_value` + (`setting_value` * 0.1256) WHERE `setting_name` = 'cutter_capacity_max'");
    }
}
$h->endpage();