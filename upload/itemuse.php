<?php
/*
	File:		itemuse.php
	Created: 	4/5/2016 at 12:10AM Eastern Time
	Info: 		Allows players to use an item.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
$Time = time();
$_GET['item'] = (isset($_GET['item']) && is_numeric($_GET['item'])) ? abs($_GET['item']) : '';
if (empty($_GET['item'])) {
    alert('danger', "Uh Oh!", "Please specify an item to use.", true, 'inventory.php');
} else {
    $i = $db->query("SELECT `itmeffects_toggle`, `itmeffects_stat`, `itmeffects_dir`,
                    `itmeffects_amount`, `itmeffects_type`, `itmname`, `inv_itemid` 
                     FROM `inventory` AS `iv` 
                     INNER JOIN `items` AS `i`
                     ON `iv`.`inv_itemid` = `i`.`itmid` 
                     WHERE `iv`.`inv_id` = {$_GET['item']}
                     AND `iv`.`inv_userid` = $userid");
    if ($db->num_rows($i) == 0) {
        $db->free_result($i);
        alert('danger', "Uh Oh!", "You are trying to use an item that doesn't exist.", true, 'inventory.php');
    } else {
        $r = $db->fetch_row($i);
        $db->free_result($i);
        $iterations=count(json_decode($r['itmeffects_toggle']));
        $toggle=json_decode($r['itmeffects_toggle']);
        $stat=json_decode($r['itmeffects_stat']);
        $dir=json_decode($r['itmeffects_dir']);
        $type=json_decode($r['itmeffects_type']);
        $amount=json_decode($r['itmeffects_amount']);
        if ($iterations == 0) {
            alert('danger', "Uh Oh!", "This item cannot be used as it has no effects.", true, 'inventory.php');
            die($h->endpage());
        }
        $usecount=0;
        while ($usecount != $iterations)
        {
            if ($toggle[$usecount] == 1)
            {
                if ($type[$usecount] == 'percent')
                {
                    if (in_array($stat[$usecount], array('energy', 'will', 'brave', 'hp'))) {
                        $inc = round($ir['max' . $stat[$usecount]] / 100 * $amount[$usecount]);
                    } elseif (in_array($stat[$usecount], array('dungeon', 'infirmary'))) {
                        $EndTime = $db->fetch_single($db->query("SELECT `{$stat[$usecount]}_out` FROM `{$stat[$usecount]}` WHERE `{$stat[$usecount]}_user` = {$userid}"));
                        $inc = round((($EndTime - $Time) / 100 * $amount[$usecount]) / 60);
                    } else {
                        $inc = round($ir[$stat[$usecount]] / 100 * $amount[$usecount]);
                    }
                }
                else
                {
                    $inc = $amount[$usecount];
                }
                if ($dir[$usecount] == 'pos')
                {
                    if (in_array($stat[$usecount], array('energy', 'will', 'brave', 'hp'))) {
                        $ir[$stat[$usecount]] = min($ir[$stat[$usecount]] + $inc, $ir['max' . $stat[$usecount]]);
                    } elseif ($stat[$usecount] == 'infirmary') {
                        put_infirmary($userid, $inc, 'Item Misuse');
                    } elseif ($stat[$usecount] == 'dungeon') {
                        put_dungeon($userid, $inc, 'Item Misuse');
                    } else {
                        $ir[$stat[$usecount]] += $inc;
                    }
                }
                else
                {
                    if ($stat[$usecount] == 'infirmary') {
                        if (user_infirmary($userid)) {
                            remove_infirmary($userid, $inc);
                        }
                    } elseif ($stat[$usecount] == 'dungeon') {
                        if (user_dungeon($userid)) {
                            remove_dungeon($userid, $inc);
                        }
                    } else {
                        $ir[$stat[$usecount]] = max($ir[$stat[$usecount]] - $inc, 0);
                    }
                }
                if (!(in_array($stat[$usecount], array('dungeon', 'infirmary')))) {
                    $upd = $ir[$stat[$usecount]];
                }
                if (in_array($stat[$usecount], array('strength', 'agility', 'guard', 'iq' , 'labor'))) {
                    $db->query("UPDATE `userstats` SET `{$stat[$usecount]}` = '{$upd}' WHERE `userid` = {$userid}");
                } elseif (!(in_array($stat[$usecount], array('dungeon', 'infirmary')))) {
                    $db->query("UPDATE `users` SET `{$stat[$usecount]}` = '{$upd}' WHERE `userid` = {$userid}");
                }
            }
            $usecount++;
        }
        alert('success', "Success!", "You have successfully used your {$r['itmname']}!", true, "itemuse.php?item={$_GET['item']}", "Use Another");
      $api->user->takeItem($userid, $r['inv_itemid'], 1);
      $api->game->addLog($userid, 'itemuse', "Used a/an {$r['itmname']} item.");
    }
    }
$h->endpage();