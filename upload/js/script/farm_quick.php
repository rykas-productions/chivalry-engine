<?php
$menuhide=1;
$nohdr=true;
require_once('../../globals.php');
if (isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD']))
{
    if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'GET')
    {
        // Ignore a GET request
        header('HTTP/1.1 400 Bad Request');
        exit;
    }
}
if (!is_ajax())
{
    header('HTTP/1.1 400 Bad Request');
    exit;
}

$q=$db->query("/*qc=on*/SELECT * FROM `farm_users` WHERE `userid` = {$userid}");
if ($db->num_rows($q) == 0)
{
    $db->query("INSERT INTO `farm_users` (`userid`) VALUES ('{$userid}')");
}
$FU = ($db->fetch_row($db->query("/*qc=on*/SELECT * FROM `farm_users` WHERE `userid` = {$userid}")));

switch ($_GET['action'])
{
    case 'bucket1':
        bucket(1);
        break;
    case 'bucket5':
        bucket(5);
        break;
    case 'bucket10':
        bucket(10);
        break;
    case 'bucket25':
        bucket(25);
        break;
    case 'bucket50':
        bucket(50);
        break;
    case 'bucket75':
        bucket(75);
        break;
    case 'bucket100':
        bucket(100);
        break;
    case 'bucket150':
        bucket(150);
        break;
    case 'bucket200':
        bucket(200);
        break;
    case 'bucket500':
        bucket(500);
        break;
}

function bucket($howmany)
{
    global $db,$userid,$api,$h,$ir,$farmconfig,$FU;
    $farmCooldown = (userHasEffect($userid, constant("farm_well_less_cooldown"))) ? 1 : 2; //Seconds, per bucket.
    $noCooldown = (userHasEffect($userid, constant("farm_well_cooldown_cutoff"))) ? 5 : 1; // >= this number gives cooldown.
    if (userHasEffect($userid, constant("farm_well_cooldown")))
    {
        $nextTime = returnEffectDone($userid, constant("farm_well_cooldown"));
        alert('danger', "Uh Oh!", "You cannot drain from your well for another " . TimeUntil_Parse($nextTime) . ".", false);
    }
    elseif ($FU['farm_water_available'] < $howmany)
    {
        alert('danger', "Uh Oh!", "You do not have enough water in your well to collect " . number_format($howmany) . " Bucket(s) of Water.", false);
    }
    elseif (!$api->UserHasItem($userid,$api->SystemItemNameToID("Empty Bucket"),1))
    {
        alert('danger', "Uh Oh!", "You do not have " . number_format($howmany) . " Empty Bucket(s) to fill up with water!", false);
    }
    else
    {
        if ($howmany > $noCooldown)
        {
            userGiveEffect($userid,  constant("farm_well_cooldown"), ($howmany*$farmCooldown));
        }
        $counted = 0;
        while ($counted < $howmany)
        {
            $counted++;
            if (!userHasEffect($userid, constant("farm_well_less_cooldown")))
            {
                if (Random(1,6151) == 2567)
                {
                    userGiveEffect($userid, "farm_well_less_cooldown", PHP_INT_MAX);
                    $api->GameAddNotification($userid, "You've learned how to fill up buckets faster! Your Well cooldown time has decreased to 1 second, from 2 seconds.");
                }
            }
            if (!userHasEffect($userid, constant("farm_well_cooldown_cutoff")))
            {
                if (Random(1,6151) == 2567)
                {
                    userGiveEffect($userid, constant("farm_well_cooldown_cutoff"), PHP_INT_MAX);
                    $api->GameAddNotification($userid, "You've become a regular at the well! You may now fill up to five buckets before you are given a cooldown.");
                }
            }
        }
        $FU['farm_water_available'] = $FU['farm_water_available'] - $howmany;
        $frmeen = min(round($FU['farm_water_available'] / $FU['farm_water_max'] * 100), 100);
        
        $api->UserGiveItem($userid,$api->SystemItemNameToID("Bucket of Water"), $howmany);
        $api->UserTakeItem($userid,$api->SystemItemNameToID("Empty Bucket"), $howmany);
        $db->query("UPDATE `farm_users` SET `farm_water_available` = `farm_water_available` - {$howmany} WHERE `userid` = {$userid}");
        alert('success', "Success!", "You have filled up " . number_format($howmany) . " Bucket(s) with Water.", false);
        $api->SystemLogsAdd($userid, "farm", "Filled " . number_format($howmany) . " Buckets of Water.");
        ?>
    		<script>
    			document.getElementById('wellPercent').innerHTML = "<?php echo "{$frmeen}%"; ?>";
    			document.getElementById('wellBar').style.width = "<?php echo "{$frmeen}%"; ?>";
    			document.getElementById('wellBarInfo').innerHTML = "<?php echo "{$frmeen}% (" . number_format($FU['farm_water_available']) . " Buckets / " . number_format($FU['farm_water_max']) . " Buckets)"; ?>";
    		</script>
    	<?php
    }
}