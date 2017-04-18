<?php
/*
	File:		mine.php
	Created: 	4/5/2016 at 12:18AM Eastern Time
	Info: 		Allows players to mine for items, and progress
				linearly.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
$macropage=('mine.php');
require('globals.php');
$UIDB=$db->query("SELECT * FROM `mining` WHERE `userid` = {$userid}");
if (!($db->num_rows($UIDB)))
{
    $db->query("INSERT INTO `mining` (`userid`, `max_miningpower`, `miningpower`, `miningxp`, `buyable_power`, `mining_level`) 
    VALUES ('{$userid}', '100', '100', '0', '1', '1');");
}
$MUS=($db->fetch_row($db->query("SELECT * FROM `mining` WHERE `userid` = {$userid} LIMIT 1")));
mining_levelup();
echo "<h2>{$lang['EXPLORE_MINE']}</h2><hr />";
if ($api->UserStatus($userid,'infirmary') == true)
{
	alert('danger',$lang["GEN_INFIRM"],$lang['MINE_INFIRM']);
	die($h->endpage());
}
if ($api->UserStatus($userid,'dungeon') == true)
{
	alert('danger',$lang["GEN_DUNG"],$lang['MINE_DUNGEON']);
	die($h->endpage());
}
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
	case 'mine':
		mine();
		break;
	case 'buypower':
		buypower();
		break;
	default:
		home();
		break;
}
function home()
{
    global $MUS,$db,$h,$lang,$api;
    $mineen = min(round($MUS['miningpower'] / $MUS['max_miningpower'] * 100), 100);
    $minexp = min(round($MUS['miningxp'] / $MUS['xp_needed'] * 100), 100);
    $mineenp = 100 - $mineen;
    $minexpp = 100 - $minexp;
    echo "{$lang['MINE_INFO']}
    <br />
	<table class='table table-bordered'>
		<tr>
			<th colspan='2'>
				{$lang['MINE_LEVEL']} {$MUS['mining_level']}.
			</th>
		</tr>
		<tr>
			<th>
				{$lang['MINE_POWER']}
			</th>
			<td>
				<div class='progress'>
					<div class='progress-bar progress-bar-success' role='progressbar' aria-valuenow='{$MUS['miningpower']}' aria-valuemin='0' aria-valuemax='100' style='width:{$mineen}%'>
						{$mineen}% ({$MUS['miningpower']} / {$MUS['max_miningpower']})
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<th>
				{$lang['MINE_XP']}
			</th>
			<td>
				<div class='progress'>
					<div class='progress-bar progress-bar-success' role='progressbar' aria-valuenow='{$MUS['miningxp']}' aria-valuemin='0' aria-valuemax='100' style='width:{$minexp}%'>
						{$minexp}% ({$MUS['miningxp']} / {$MUS['xp_needed']})
					</div>
				</div>
			</td>
		</tr>
	</table>
    <u>{$lang['MINE_SPOTS']}</u><br />";
    $minesql=$db->query("SELECT * FROM `mining_data` ORDER BY `mine_level` ASC");
    while ($mines = $db->fetch_row($minesql))
    {
        echo"[<a href='?action=mine&spot={$mines['mine_id']}'>" . $api->SystemTownIDtoName($mines['mine_location']) . " - Level {$mines['mine_level']}</a>]<br />";
    }
    
    echo "<br /><br />
    [<a href='?action=buypower'>{$lang['MINE_SETS']}</a>]";

}
function buypower()
{
    global $userid,$db,$ir,$MUS,$h,$lang,$api;
    $CostForPower = $MUS['mining_level']*75+10+$MUS['mining_level']; //Cost formula, in IQ.
    if (isset($_POST['sets']) && ($_POST['sets'] > 0))
    {
        $sets=abs($_POST['sets']);
        $totalcost=$sets*$CostForPower;
        if ($sets > $MUS['buyable_power'])
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['MINE_BUY_ERROR']);
            die($h->endpage());
        }
        elseif (($ir['iq'] < $totalcost))
        {
			alert('danger',$lang['ERROR_GENERIC'],"{$lang['MINE_BUY_ERROR_IQ']}" . number_format($totalcost) . " {$lang['MINE_BUY_ERROR_IQ1']}" . number_format($ir['iq']));
			die($h->endpage());
        
        }
        else
        {
            $db->query("UPDATE `userstats` SET `iq` = `iq` - '{$totalcost}' WHERE `userid` = {$userid}");
            $db->query("UPDATE `mining` SET `buyable_power` = `buyable_power` - '$sets', 
						`max_miningpower` = `max_miningpower` + ($sets*10) 
						WHERE `userid` = {$userid}");
			$api->SystemLogsAdd($userid,'mining',"Exchanged {$totalcost} IQ for {$sets} sets of mining power.");
			alert('success',$lang['ERROR_SUCCESS'],"{$lang['MINE_BUY_SUCCESS']}" . number_format($totalcost) . " {$lang['GEN_IQ']} {$lang['GEN_FOR']} {$sets} {$lang['MINE_BUY_SUCCESS1']}",true,'mine.php');
        }
    }
    else
    {
        echo "{$lang['MINE_BUY_INFO']} {$MUS['buyable_power']} {$lang['MINE_BUY_INFO1']} " . number_format($CostForPower) . " {$lang['MINE_BUY_INFO2']}";
        echo "<br />
        <form method='post'>
            <input type='number' class='form-control' value='{$MUS['buyable_power']}' min='1' max='{$MUS['buyable_power']}' name='sets' required='1'>
            <br />
            <input type='submit' class='btn btn-default' value='{$lang['MINE_BUY_BTN']}'>
        </form>";
    }
}
function mine()
{
    global $db,$MUS,$ir,$userid,$lang,$api,$h;
    if (!isset($_GET['spot']) || empty($_GET['spot']))
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['MINE_DO_ERROR'],true,'mine.php');
		die($h->endpage());
    }
    else
    {
        $spot=abs($_GET['spot']);
        $mineinfo=$db->query("SELECT * FROM `mining_data` WHERE `mine_id` = {$spot}");
        if (!($db->num_rows($mineinfo)))
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['MINE_DO_ERROR1'],true,'mine.php');
			die($h->endpage());
        }
        else
        {
            $MSI=$db->fetch_row($mineinfo);
			$query=$db->query("SELECT `inv_itemid` FROM `inventory` where `inv_itemid` = {$MSI['mine_pickaxe']} && `inv_userid` = {$userid}");
			$i=$db->fetch_row($query);
            if ($MUS['mining_level'] < $MSI['mine_level'])
            {
				alert('danger',$lang['ERROR_GENERIC'],$lang['MINE_DO_ERROR2'] . " {$MSI['mine_level']}",true,'mine.php');
				die($h->endpage());
            }
            elseif ($ir['location'] != $MSI['mine_location'])
            {
                alert('danger',$lang['ERROR_GENERIC'],$lang['MINE_DO_ERROR3'],true,'mine.php');
				die($h->endpage());
            }
            elseif ($ir['iq'] < $MSI['mine_iq'])
            {
                alert('danger',$lang['ERROR_GENERIC'],$lang['MINE_DO_ERROR4'] . " {$MSI['mine_iq']}",true,'mine.php');
				die($h->endpage());
            }
            elseif ($MUS['miningpower'] < $MSI['mine_power_use'])
            {
				alert('danger',$lang['ERROR_GENERIC'],$lang['MINE_DO_ERROR5'] . " {$MSI['mine_power_use']}",true,'mine.php');
				die($h->endpage());
            }
			elseif(!$i['inv_itemid'] == $MSI['mine_pickaxe'])
			{
				alert('danger',$lang['ERROR_GENERIC'],$lang['MINE_DO_ERROR6'] . " " . $api->SystemItemIDtoName($MSI['mine_pickaxe']),true,"mine.php");
				die($h->endpage());
			}
            else
            {
                if (!isset($xpgain))
                {
                    $xpgain = 0;
                }
                if ($ir['iq'] <= $MSI['mine_iq']+($MSI['mine_iq']*.3))
                {
                    $Rolls=Random(1,5);
                }
                elseif ($ir['iq'] >= $MSI['mine_iq']+($MSI['mine_iq']*.3) && ($ir['iq'] <= $MSI['mine_iq']+($MSI['mine_iq']*.6)))
                {
                    $Rolls=Random(1,10);
                }
                else
                {
                    $Rolls=Random(1,15);
                }
                if ($Rolls <= 3)
                {
                    $NegRolls=Random(1,3);
                    $NegTime=Random(25,75)*($MUS['mining_level']*.25);
                    if ($NegRolls == 1)
                    {
                        alert('danger',$lang['ERROR_GENERIC'],$lang['MINE_DO_FAIL'],false);
						$api->SystemLogsAdd($userid,'mining',"Mined at {$api->SystemTownIDtoName($MSI['mine_location'])} [{$MSI['mine_location']}] and was put into the infirmary for {$NegTime} minutes.");
						$api->UserStatusSet($userid,'infirmary',$NegTime,"Mining Explosion");
                    }
                    elseif ($NegRolls == 2)
                    {
                        alert('danger',$lang['ERROR_GENERIC'],$lang['MINE_DO_FAIL1'],false);
						$api->SystemLogsAdd($userid,'mining',"Mined at {$api->SystemTownIDtoName($MSI['mine_location'])} [{$MSI['mine_location']}] and was put into the dungeon for {$NegTime} minutes.");
						$api->UserStatusSet($userid,'dungeon',$NegTime,"Mining Selfishness");
                    }
                    else
                    {
                        alert('danger',$lang['ERROR_GENERIC'],$lang['MINE_DO_FAIL2'],false);
						$api->SystemLogsAdd($userid,'mining',"Mined at {$api->SystemTownIDtoName($MSI['mine_location'])} [{$MSI['mine_location']}] and was unsuccessful.");
                    }
                }
                elseif ($Rolls >= 3 && $Rolls <= 14)
                {
                    $PosRolls=Random(1,3);
                    if ($PosRolls == 1)
                    {
                        $flakes=Random($MSI['mine_copper_min'],$MSI['mine_copper_max']);
						alert('success',$lang['ERROR_SUCCESS'],$lang['MINE_DO_SUCC'] . number_format($flakes) . " " . $api->SystemItemIDtoName($MSI['mine_copper_item']) . $lang['MINE_DO_SUCC1'],false);
                        $api->UserGiveItem($userid,$MSI['mine_copper_item'],$flakes);
						$api->SystemLogsAdd($userid,'mining',"Mined at {$api->SystemTownIDtoName($MSI['mine_location'])} [{$MSI['mine_location']}] and mined {$flakes}x {$api->SystemItemIDtoName($MSI['mine_copper_item'])}.");
                        $xpgain=$flakes*0.1;
                        
                    }
                    elseif ($PosRolls == 2)
                    {
                        $flakes=Random($MSI['mine_silver_min'],$MSI['mine_silver_max']);
                        alert('success',$lang['ERROR_SUCCESS'],$lang['MINE_DO_SUCC'] . number_format($flakes) . " " . $api->SystemItemIDtoName($MSI['mine_silver_item']) . $lang['MINE_DO_SUCC1'],false);
                        $api->UserGiveItem($userid,$MSI['mine_silver_item'],$flakes);
						$api->SystemLogsAdd($userid,'mining',"Mined at {$api->SystemTownIDtoName($MSI['mine_location'])} [{$MSI['mine_location']}] and mined {$flakes}x {$api->SystemItemIDtoName($MSI['mine_silver_item'])}.");
                        $xpgain=$flakes*0.2;
                    }
                    else
                    {
                        $flakes=Random($MSI['mine_gold_min'],$MSI['mine_gold_max']);
                        alert('success',$lang['ERROR_SUCCESS'],$lang['MINE_DO_SUCC'] . number_format($flakes) . " " . $api->SystemItemIDtoName($MSI['mine_gold_item']) . $lang['MINE_DO_SUCC1'],false);
                        $api->UserGiveItem($userid,$MSI['mine_gold_item'],$flakes);
						$api->SystemLogsAdd($userid,'mining',"Mined at {$api->SystemTownIDtoName($MSI['mine_location'])} [{$MSI['mine_location']}] and mined {$flakes}x {$api->SystemItemIDtoName($MSI['mine_gold_item'])}.");
                        $xpgain=$flakes*0.3;
                    }
                }
                else
                {
					alert('success',$lang['ERROR_SUCCESS'],$lang['MINE_DO_SUCC2'] . $api->SystemItemIDtoName($MSI['mine_gem_item']),false);
                    $api->UserGiveItem($userid,$MSI['mine_gem_item'],1);
					$api->SystemLogsAdd($userid,'mining',"Mined at {$api->SystemTownIDtoName($MSI['mine_location'])} [{$MSI['mine_location']}] and mined 1x {$api->SystemItemIDtoName($MSI['mine_gem_item'])}.");
                    $xpgain=1;
                }
                echo"<hr />
                [<a href='?action=mine&spot={$spot}'>{$lang['MINE_DO_BTN1']}</a>]<br />
                [<a href='mine.php'>{$lang['MINE_DO_BTN']}</a>]";
                $db->query("UPDATE `mining` SET `miningxp`=`miningxp`+ {$xpgain}, `miningpower`=`miningpower`-'{$MSI['mine_power_use']}' WHERE `userid` = {$userid}");
            }
        }
    }
}
function mining_levelup()
{
    global $db,$userid,$MUS;
    $MUS['xp_needed'] = round(($MUS['mining_level'] + 1) * ($MUS['mining_level'] + 1) * ($MUS['mining_level'] + 1) * 4.4);
    if ($MUS['miningxp'] >= $MUS['xp_needed'])
    {
        $expu = $MUS['miningxp'] - $MUS['xp_needed'];
        $MUS['mining_level'] += 1;
        $MUS['miningxp'] = $expu;
        $MUS['buyable_power'] += 1;
        $MUS['xp_needed'] =
                round(($MUS['mining_level'] + 1) * ($MUS['mining_level'] + 1) * ($MUS['mining_level'] + 1) * 4.4);
        $db->query("UPDATE `mining` SET `mining_level` = `mining_level` + 1, `miningxp` = {$expu},
                 `buyable_power` = `buyable_power` + 1 WHERE `userid` = {$userid}");
    }
}
$h->endpage();