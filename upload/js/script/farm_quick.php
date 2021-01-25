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
    case 'bucket100':
        bucket(100);
        break;
}

function bucket($howmany)
{
    global $db,$userid,$api,$h,$ir,$farmconfig,$FU;
    if (userHasEffect($userid, "farm_well_cooldown"))
    {
        $nextTime = returnEffectDone($userid, "farm_well_cooldown");
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
        if ($howmany > 1)
        {
            userGiveEffect($userid, "farm_well_cooldown", ($howmany*2));
        }
        $FU['farm_water_available'] = $FU['farm_water_available'] - $howmany;
        $frmeen = min(round($FU['farm_water_available'] / $FU['farm_water_max'] * 100), 100);
        
        $api->UserGiveItem($userid,$api->SystemItemNameToID("Bucket of Water"),1);
        $api->UserTakeItem($userid,$api->SystemItemNameToID("Empty Bucket"),1);
        $db->query("UPDATE `farm_users` SET `farm_water_available` = `farm_water_available` - {$howmany} WHERE `userid` = {$userid}");
        alert('success', "Success!", "You have filled up " . number_format($howmany) . " Bucket(s) with Water.", false);
        ?>
    		<script>
    			document.getElementById('wellPercent').innerHTML = "<?php echo "{$frmeen}%"; ?>";
    			document.getElementById('wellBar').style.width = "<?php echo "{$frmeen}%"; ?>";
    			document.getElementById('wellBarInfo').innerHTML = "<?php echo "{$frmeen}% (" . number_format($FU['farm_water_available']) . " Buckets / " . number_format($FU['farm_water_max']) . " Buckets)"; ?>";
    		</script>
    	<?php
    }
}