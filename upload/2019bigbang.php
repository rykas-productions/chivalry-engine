<?php
require('globals.php');
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    default:
        home();
        break;
}
function home()
{
    global $db,$h,$userid;
    echo "<h3>2019's Big Bang Event</h3><hr />";
    if (isset($_POST['small']))
    {
        $count = (isset($_POST['small']) && is_numeric($_POST['small'])) ? abs($_POST['small']) : '';
        if (empty($count))
        {
            alert('danger',"Uh Oh!","Please fill out the form completely before submitting it.");
            die($h->endpage());
        }
        if (doDonate('small',$count))
        {
            alert('success',"Success!","You have successfully donated {$count} Small Explosives.");
            die($h->endpage());
        }
        else
        {
            alert('danger',"Uh Oh!","You do not have enough Small Explosives to donate that much.");
            die($h->endpage());
        }
    }
    elseif (isset($_POST['medium']))
    {
        $count = (isset($_POST['medium']) && is_numeric($_POST['medium'])) ? abs($_POST['medium']) : '';
        if (empty($count))
        {
            alert('danger',"Uh Oh!","Please fill out the form completely before submitting it.");
            die($h->endpage());
        }
        if (doDonate('medium',$count))
        {
            alert('success',"Success!","You have successfully donated {$count} Medium Explosives.");
            die($h->endpage());
        }
        else
        {
            alert('danger',"Uh Oh!","You do not have enough Medium Explosives to donate that much.");
            die($h->endpage());
        }
    }
    elseif (isset($_POST['large']))
    {
        $count = (isset($_POST['large']) && is_numeric($_POST['large'])) ? abs($_POST['large']) : '';
        if (empty($count))
        {
            alert('danger',"Uh Oh!","Please fill out the form completely before submitting it.");
            die($h->endpage());
        }
        if (doDonate('large',$count))
        {
            alert('success',"Success!","You have successfully donated {$count} Large Explosives.");
            die($h->endpage());
        }
        else
        {
            alert('danger',"Uh Oh!","You do not have enough Large Explosives to donate that much.");
            die($h->endpage());
        }
    }
    else
    {
        $sc=$db->fetch_single($db->query("SELECT SUM(`small`) FROM `2019_bigbang`"));
        $mc=$db->fetch_single($db->query("SELECT SUM(`medium`) FROM `2019_bigbang`"));
        $lc=$db->fetch_single($db->query("SELECT SUM(`large`) FROM `2019_bigbang`"));
        echo "The Big Bang Event is supposed to be our way of reeling in the new year. So, 
        why set off some fireworks? Well... we don't have fireworks, but we do have 
        explosives! Donate your explosives to contribute to the final explosion. After 
        the donations are closed, players active within the last week will be randomly 
        chosen to be our little explosive test dummy. One player will receive all the time 
        for the Small Explosives, another for the Mediums and another for the Large ones.<br />
        While that on its own doesn't sound that amusing, the players who get blown up will 
        receive their choosing of $5 worth of VIP Packs, and 10 <a href='iteminfo.php?ID=205'>CID Admin Gym Access Scrolls</a>.<br />
        <b>Donate Small Explosives (Currently Donated: {$sc})<br /></b>
        <form method='post'>
            <input type='number' min='0' name='small' class='form-control'>
            <input type='submit' value='Donate Small Explosvies' class='btn btn-primary'>
        </form>
        <b>Donate Medium Explosives (Currently Donated: {$mc})<br /></b>
        <form method='post'>
            <input type='number' min='0' name='medium' class='form-control'>
            <input type='submit' value='Donate Medium Explosvies' class='btn btn-primary'>
        </form>
        <b>Donate Large Explosives (Currently Donated: {$lc})<br /></b>
        <form method='post'>
            <input type='number' min='0' name='large' class='form-control'>
            <input type='submit' value='Donate Large Explosvies' class='btn btn-primary'>
        </form>";
    }
    $h->endpage();
}
function doDonate($type,$count)
{
    global $db,$userid,$ir,$api;
    if ($type == 'small')
        $id=28;
    if ($type == 'medium')
        $id=61;
    if ($type == 'large')
        $id=62;
    $q=$db->query("SELECT * FROM `2019_bigbang` WHERE `userid` = {$userid}");
    if ($db->num_rows($q) == 0)
    {
        $db->query("INSERT INTO `2019_bigbang` (`userid`, `small`, `medium`, `large`) VALUES ('{$userid}', '0', '0', '0')");
    }
    if ($api->UserHasItem($userid,$id,$count))
    {
        $db->query("UPDATE `2019_bigbang` SET `{$type}` = `{$type}` + {$count} WHERE `userid` = {$userid}");
        $api->UserTakeItem($userid,$id,$count);
        return true;
    }
    else
        return false;
}