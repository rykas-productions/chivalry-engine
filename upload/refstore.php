<?php
require('globals.php');
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
    echo "<h3>Referral Points Store</h3><hr />";
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
        alert("danger","Uh Oh!","You have chosen to buy a non-existent listing.",true,'refstore.php');
        die($h->endpage());
    }
    if (($_GET['option'] < 0) || ($_GET['option'] > 1))
    {
        alert("danger","Uh Oh!","You have chosen to buy a non-existent listing.",true,'refstore.php');
        die($h->endpage());
    }
    $cost=1;
    $cost1=array(1);
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
        alert("danger","Uh Oh!","You do not have {$cost} Referral Points to buy this offer. You only have {$ir['ref_points']}.",true,'refstore.php');
        die($h->endpage());
    }
    $db->query("UPDATE `users` SET `ref_points` = `ref_points` - {$cost} WHERE `userid` = {$userid}");
    //10 Chivalry Scrolls
    if ($_GET['option'] == 1)
    {
        $api->UserGiveItem($userid,
    }
    alert("success","Success!","You have successfully traded {$cost} Referral Points for a reward! Check your inventory.",true,'refstore.php');
    die($h->endpage());
}
function home()
{
    global $db,$userid,$api,$h,$ir;
    echo "Welcome to the Vote Point Store. Here you may spend your points, which you gain by voting, on things you may want. <br />
    <b>You currently have {$ir['ref_points']} Vote Points to spend.</b><br />
    <table class='table table-bordered'>
        <tr>
            <th>
                10 Chivalry Scrolls
            </th>
            <td>
                <a href='?action=buy&option=1'>1 Referral Point</a>
            </td>
        </tr>
    </table>";
}
$h->endpage();