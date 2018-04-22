<?php
require('globals.php');
echo "<h3>Farmlands</h3><hr />";
$q=$db->query("/*qc=on*/SELECT * FROM `farm_users` WHERE `userid` = {$userid}");
if ($db->num_rows($q) == 0)
{
    $db->query("INSERT INTO `farm_users` (`userid`) VALUES ('{$userid}')");
}
$FU = ($db->fetch_row($db->query("/*qc=on*/SELECT * FROM `farm_users` WHERE `userid` = {$userid}")));
if ($FU['farm_water_max'] == 0)
{
    if (isset($_GET['buy']))
    {
        if (!$api->UserHasItem($userid,2,500))
        {
            alert('danger',"Uh Oh!","You do not have enough Heavy Rocks to construct your well.",true,'farm.php');
            die($h->endpage());
        }
        else
        {
            $api->UserTakeItem($userid,2,500);
            $db->query("UPDATE `farm_users` SET `farm_water_available` = 5, `farm_water_max` = 5 WHERE `userid` = {$userid}");
            alert('success',"Success!","You have successfully constructed your farm's well and filled it with water.",true,'farm.php','Get Farming!');
            die($h->endpage());
        }
    }
    alert('info','',"You must construct your well before you can tend to your farms. It will cost you 500 Heavy Rocks.",true,'?buy','Construct Well');
    die($h->endpage());
}
echo "<i style='font-size:48px;' class='game-icon game-icon-well'></i><br />
        {$FU['farm_water_available']} / {$FU['farm_water_max']} Buckets Stored.<hr />";
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'buyland':
        buyland();
        break;
    default:
        home();
        break;
}
function home()
{
    global $db,$userid,$api,$h,$ir,$FD;
    echo "Welcome to the farmlands, {$ir['username']}. Tend to your land here.<br />";
    $q=$db->query("/*qc=on*/SELECT * FROM `farm_data` WHERE `farm_owner` = {$userid}");
    if ($db->num_rows($q) == 0)
    {
        alert('info','',"You don't have any farmland!",true,'?action=buyland',"Buy Land");
    }
    else
    {
        while ($r=$db->fetch_row($q))
        {
            if ($r['farm_time'] > time())
                $color='text-info';
            else
                $color='text-success';
        }
    }
    $h->endpage();
}
function buyland()
{
    global $db,$userid,$api,$h,$ir;
}