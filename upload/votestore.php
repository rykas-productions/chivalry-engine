<?php
require('globals.php');
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
    echo "<h3>Vote Point Store</h3><hr />";
switch ($_GET['action']) {
    case "buy":
        buy();
        break;
    default:
        home();
        break;
}
function buy()
{
    global $db,$userid,$api,$h,$ir;
    $_GET['option'] = abs($_GET['option']);
    if (empty($_GET['option']))
    {
        alert("danger","Uh Oh!","You have chosen to buy a non-existent listing.",true,'votestore.php');
        die($h->endpage());
    }
    if (($_GET['option'] < 0) || ($_GET['option'] > 11))
    {
        alert("danger","Uh Oh!","You have chosen to buy a non-existent listing.",true,'votestore.php');
        die($h->endpage());
    }
    $cost=1;
    $cost1=array(1,2,3,4);
    $cost3=array(9);
    $cost5=array(5,11);
    $cost25=array(10);
    $cost50=array(6);
    $cost150=array(7,8);
    if (in_array($_GET['option'],$cost3))
    {
        $cost=3;
    }
    if (in_array($_GET['option'],$cost5))
    {
        $cost=5;
    }
    if (in_array($_GET['option'],$cost25))
    {
        $cost=25;
    }
    if (in_array($_GET['option'],$cost50))
    {
        $cost=50;
    }
    
    if (in_array($_GET['option'],$cost150))
    {
        $cost=150;
    }
    if ($ir['vote_points'] < $cost)
    {
        alert("danger","Uh Oh!","You do not have {$cost} Vote Points to buy this offer. You only have {$ir['vote_points']}.",true,'votestore.php');
        die($h->endpage());
    }
    $db->query("UPDATE `users` SET `vote_points` = `vote_points` - {$cost} WHERE `userid` = {$userid}");
    //100 Chivalry Tokens
    if ($_GET['option'] == 1)
    {
        $db->query("UPDATE `users` SET `secondary_currency` = `secondary_currency` + 100 WHERE `userid` = {$userid}");
    }
    //50 Boxes of Random
    if ($_GET['option'] == 2)
    {
        $api->UserGiveItem($userid,33,50);
    }
    //100,000 Copper Coins
    if ($_GET['option'] == 3)
    {
        $db->query("UPDATE `users` SET `primary_currency` = `primary_currency` + 100000 WHERE `userid` = {$userid}");
    }
    //25 Hexbags
    if ($_GET['option'] == 4)
    {
        if (($ir['hexbags'] + 25) > 255)
        {
            alert("danger","Uh Oh!","You can only have a maximum of 255 Hexbags at a time. Use the ones you have and come back and buy more.",true,'votestore.php');
            die($h->endpage());
        }
        $db->query("UPDATE `users` SET `hexbags` = `hexbags` + 25 WHERE `userid` = {$userid}");
    }
    //CID Admin Gym Access Scroll
    if ($_GET['option'] == 5)
    {
        $api->UserGiveItem($userid,205,1);
    }
    //$1 VIP Pack
    if ($_GET['option'] == 6)
    {
        $api->UserGiveItem($userid,12,1);
    }
    //Auto BOR Opener
    if ($_GET['option'] == 7)
    {
        $api->UserGiveItem($userid,92,1);
    }
    //Auto Hexbag Opener
    if ($_GET['option'] == 8)
    {
        $api->UserGiveItem($userid,91,1);
    }
    //Chivalry Gym Scroll
    if ($_GET['option'] == 9)
    {
        $api->UserGiveItem($userid,18,1);
    }
    //Mining Power
    if ($_GET['option'] == 10)
    {
        $db->query("UPDATE `mining` SET `max_miningpower` = `max_miningpower` + 10 WHERE `userid` = {$userid}");
    }
    //Voting Badge
    if ($_GET['option'] == 11)
    {
        $api->UserGiveItem($userid,209,1);
    }
    alert("success","Success!","You have successfully traded {$cost} Vote Points for a reward! Check your inventory.",true,'votestore.php');
    die($h->endpage());
}
function home()
{
    global $db,$userid,$api,$h,$ir;
    echo "Welcome to the Vote Point Store. Here you may spend your points, which you gain by voting, on things you may want. <br />
    <b>You currently have {$ir['vote_points']} Vote Points to spend.</b><br />
    <table class='table table-bordered'>
        <tr>
            <th>
                100 Chivalry Tokens
            </th>
            <td>
                <a href='?action=buy&option=1'>1 Vote Point</a>
            </td>
        </tr>
        <tr>
            <th>
                50 Boxes of Random
            </th>
            <td>
                <a href='?action=buy&option=2'>1 Vote Point</a>
            </td>
        </tr>
        <tr>
            <th>
                100,000 Copper Coins
            </th>
            <td>
                <a href='?action=buy&option=3'>1 Vote Point</a>
            </td>
        </tr>
        <tr>
            <th>
                25 Hexbags
            </th>
            <td>
                <a href='?action=buy&option=4'>1 Vote Point</a>
            </td>
        </tr>
        <tr>
            <th>
                Chivalry Gym Scroll
            </th>
            <td>
                <a href='?action=buy&option=9'>3 Vote Points</a>
            </td>
        </tr>
        <tr>
            <th>
                CID Admin Gym Access Scroll
            </th>
            <td>
                <a href='?action=buy&option=5'>5 Vote Points</a>
            </td>
        </tr>
        <tr>
            <th>
                Voting Badge
            </th>
            <td>
                <a href='?action=buy&option=11'>5 Vote Points</a>
            </td>
        </tr>
        <tr>
            <th>
                10 Maximum Mining Power
            </th>
            <td>
                <a href='?action=buy&option=10'>25 Vote Points</a>
            </td>
        </tr>
        <tr>
            <th>
                $1 VIP Pack
            </th>
            <td>
                <a href='?action=buy&option=6'>50 Vote Points</a>
            </td>
        </tr>
        <tr>
            <th>
                Auto BOR Opener
            </th>
            <td>
                <a href='?action=buy&option=7'>150 Vote Points</a>
            </td>
        </tr>
        <tr>
            <th>
                Auto Hexbags Opener
            </th>
            <td>
                <a href='?action=buy&option=8'>150 Vote Points</a>
            </td>
        </tr>
    </table>";
}
$h->endpage();