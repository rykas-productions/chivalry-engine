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
    $i = $db->query("/*qc=on*/SELECT `effect1`, `effect2`, `effect3`,  `effect1_on`, `effect2_on`, `effect3_on`, `itmid`, 
                     `itmname`, `inv_itemid`, `weapon`, `armor` FROM `inventory` AS `iv` INNER JOIN `items` AS `i`
                     ON `iv`.`inv_itemid` = `i`.`itmid` WHERE `iv`.`inv_id` = {$_GET['item']}
                     AND `iv`.`inv_userid` = $userid");
    if ($db->num_rows($i) == 0) {
        $db->free_result($i);
        alert('danger', "Uh Oh!", "You are trying to use an item that doesn't exist.", true, 'inventory.php');
    } else {
        $r = $db->fetch_row($i);
        $db->free_result($i);
        if (!$r['effect1_on'] && !$r['effect2_on'] && !$r['effect3_on']) {
            alert('danger', "Uh Oh!", "This item cannot be used as it has no effects.", true, 'inventory.php');
            die($h->endpage());
        }
		if (($r['armor'] > 0) || ($r['weapon'] > 0))
		{
			alert('danger', "Uh Oh!", "You cannot use weapons and armor in this way.", true, 'inventory.php');
            die($h->endpage());
		}
        consumeItem($userid, $r['itmid']);
        if (getSkillLevel($userid,28) != 0)
        {
            if (Random(1,20) == 1)
            {
                $api->UserInfoSet($userid, 'energy', Random(1,5), true);
            }
        }
        alert('success', "Success!", "You have successfully used your {$r['itmname']}!", true, "itemuse.php?item={$_GET['item']}", "Use Another");
      $api->SystemLogsAdd($userid, 'itemuse', "Used {$r['itmname']}.");
    }
    }
$h->endpage();