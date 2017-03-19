<?php
require('globals.php');
$Time=time();
$_GET['item'] = (isset($_GET['item']) && is_numeric($_GET['item'])) ? abs($_GET['item']) : '';
if (empty($_GET['item']))
{
    alert('danger',$lang['ERROR_GENERIC'],$lang['IU_UI'],true,'inventory.php');
}
else
{
	$i = $db->query("SELECT `effect1`, `effect2`, `effect3`,  `effect1_on`, `effect2_on`, `effect3_on`,
                     `itmname`, `inv_itemid` FROM `inventory` AS `iv` INNER JOIN `items` AS `i`
                     ON `iv`.`inv_itemid` = `i`.`itmid` WHERE `iv`.`inv_id` = {$_GET['item']}
                     AND `iv`.`inv_userid` = $userid");
	if ($db->num_rows($i) == 0)
    {
        $db->free_result($i);
        alert('danger',$lang['ERROR_GENERIC'],$lang['IU_ITEM_NOEXIST'],true,'inventory.php');
    }
	else
	{
		$r = $db->fetch_row($i);
        $db->free_result($i);
		if (!$r['effect1_on'] && !$r['effect2_on'] && !$r['effect3_on'])
        {
            alert('danger',"",$lang['IU_UNUSED_ITEM'],true,'inventory.php');
            die($h->endpage());
        }
		for ($enum = 1; $enum <= 3; $enum++)
        {
			if ($r["effect{$enum}_on"] == 'true')
            {
				$einfo = unserialize($r["effect{$enum}"]);
				if ($einfo['inc_type'] == "percent")
                {
					if (in_array($einfo['stat'], array('energy', 'will', 'brave', 'hp')))
                    {
                        $inc = round($ir['max' . $einfo['stat']] / 100 * $einfo['inc_amount']);
                    }
					elseif (in_array($einfo['stat'], array('dungeon', 'infirmary')))
                    {
						$EndTime=$db->fetch_single($db->query("SELECT `{$einfo['stat']}_out` FROM `{$einfo['stat']}` WHERE `{$einfo['stat']}_user` = {$userid}"));
						$inc = round((($EndTime - $Time) / 100 * $einfo['inc_amount'])/60);
                    }
					else
                    {
                        $inc = round($ir[$einfo['stat']] / 100 * $einfo['inc_amount']);
                    }
				}
				else
				{
					$inc = $einfo['inc_amount'];
				}
				if ($einfo['dir'] == "pos")
				{
					if (in_array($einfo['stat'], array('energy', 'will', 'brave', 'hp')))
					{
						$ir[$einfo['stat']] = min($ir[$einfo['stat']] + $inc, $ir['max' . $einfo['stat']]);
					}
					elseif ($einfo['stat'] == 'infirmary')
					{
						put_infirmary($userid,$inc,'Item Misuse');
					}
					elseif ($einfo['stat'] == 'dungeon')
					{
						put_dungeon($user,$inc,'Item Misuse');
					}
					else
					{
						$ir[$einfo['stat']] += $inc;
					}
				}
				else
				{
					if ($einfo['stat'] == 'infirmary')
					{
						if (user_infirmary($userid) == true)
						{
							remove_infirmary($userid,$inc);
						}
					}
					elseif ($einfo['stat'] == 'dungeon')
					{
						if (user_dungeon($userid) == true)
						{
							remove_dungeon($userid,$inc);
						}
					}
					else
					{
						$ir[$einfo['stat']] = max($ir[$einfo['stat']] - $inc, 0);
					}
				}
				if (!(in_array($einfo['stat'], array('dungeon', 'infirmary'))))
				{
					$upd = $ir[$einfo['stat']];
				}
				if (in_array($einfo['stat'], array('strength', 'agility', 'guard', 'labour', 'IQ')))
				{
					$db->query("UPDATE `userstats` SET `{$einfo['stat']}` = '{$upd}' WHERE `userid` = {$userid}");
				}
				elseif (!(in_array($einfo['stat'], array('dungeon', 'infirmary'))))
				{
					$db->query("UPDATE `users` SET `{$einfo['stat']}` = '{$upd}' WHERE `userid` = {$userid}");
				}
			}
		}
		alert('success',$lang['ERROR_SUCCESS'],"{$r['itmname']} {$lang['IU_SUCCESS']}",true,'inventory.php');
		$api->UserTakeItem($userid, $r['inv_itemid'], 1);
		$api->SystemLogsAdd($userid,'itemuse',"Used a/an {$r['itmname']} item.");
	}
}
$h->endpage();