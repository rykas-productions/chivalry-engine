<?php
/*	File:		christmastree.php
	Created: 	Nov 29, 2022; 6:59:08 PM
	Info: 		
	Author:		Ryan
	Website: 	https://chivalryisdeadgame.com/
*/
require('globals.php');						//uncomment if user needs to be auth'd.\
$month = date('n');
$day = date('j');
if ($month != 12 && $month != 1)
{
    alert('danger',"Uh Oh!","You are either too early or too late to the party...",true,'explore.php');
    die($h->endpage());
}
if (!isset($_GET['action'])) 
{
    $_GET['action'] = '';
}
switch ($_GET['action']) 
{
    case "open":
        open();
        break;
    case "wish":
        wish();
        break;
    case "gift":
        gift();
        break;
    default:
        home();
        break;
}

function home()
{
    global $db,$userid,$api,$h,$set;
    echo "<h3>{$set['WebsiteName']} Christmas Tree</h3><hr />";
    echo "Here's the Chivalry is Dead Christmas tree. You may place gifts under the tree for your friends to open on/after Christmas Day. Don't worry, Santa 
    will make sure you have at least one gift for the holidays!<br />
    You may place a gift <a href='?action=gift'>here</a>.<hr />";
    $q=$db->query("SELECT * FROM `2018_christmas_tree`");
    if ($db->num_rows($q) == 0)
    {
        alert('danger',"Uh Oh!","No gifts have been given yet. Why don't you be the first?",false);
    }
    else
    {
        echo "<div class='row'>
                ";
        while ($r=$db->fetch_row($q))
        {
            $bg = cardBGColorRNG();
            echo "<div class='col-12 col-md-6 col-xl-4 col-xxxl-3'>
                    <div class='card {$bg}'>
                        <div class='card-header'>
                            To " . parseUsername($r['userid_to']) . " [{$r['userid_to']}]
                        </div>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-12'>
                                    From " . parseUsername($r['userid_from']) . " [{$r['userid_from']}]
                                </div>";
                                if ($userid == $r['userid_from'])
                                {
                                    echo "<div class='col-12'>
                                            Gift " . shortNumberParse($r['qty']) . " x {$api->SystemItemIDtoName($r['item'])}(s)
                                        </div>";
                                }
                                if ($userid == $r['userid_to'])
                                {
                                    echo "<div class='col-12'>
                                            <a href='?action=open&gift={$r['gift_id']}' class='btn btn-primary btn-block'>Open Gift</a>
                                        </div>";
                                }
            
                            echo "
                            </div>
                        </div>
                        
                    </div>
                    <br />
                    </div>";
        }
        echo "</div>";
    }
    $h->endpage();
}

function gift()
{
    global $db,$userid,$api,$h,$ir,$set;
    echo "<h3>{$set['WebsiteName']} Christmas Gifting</h3><hr />";
    if (isset($_POST['user']))
    {
        $_POST['item'] = (isset($_POST['item']) && is_numeric($_POST['item'])) ? abs($_POST['item']) : '';
        $_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : '';
        $_POST['qty'] = (isset($_POST['qty']) && is_numeric($_POST['qty'])) ? abs($_POST['qty']) : '';
        $m = $db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `userid` = {$_POST['user']} LIMIT 1");
        if ((empty($_POST['item'])) || (empty($_POST['user'])) || (empty($_POST['qty'])))
        {
            alert('danger',"Uh Oh!","Please fill out the previous form completely.");
            die($h->endpage());
        }
        if ($db->num_rows($m) == 0)
        {
            alert('danger',"Uh Oh!","You cannot gift items to a non-existent player.");
            die($h->endpage());
        }
        if ($_POST['user'] == $userid)
        {
            alert('danger',"Uh Oh!","If you have no one giving presents to you this year, let CID Admin know and he'll make sure you have something special under the tree.");
            die($h->endpage());
        }
        if (!$api->UserHasItem($userid,$_POST['item'],$_POST['qty']))
        {
            alert('danger',"Uh Oh!","You either do not have that item, or enough available of that item to gift that many.");
            die($h->endpage());
        }
        if ($api->SystemCheckUsersIPs($userid, $_POST['user']))
        {
            alert('danger',"Uh Oh!","You cannot gift items to players who share the same IP Address as you.");
            die($h->endpage());
        }
        $api->UserTakeItem($userid,$_POST['item'],$_POST['qty']);
        $db->query("INSERT INTO `2018_christmas_tree` (`userid_from`, `userid_to`, `item`, `qty`) VALUES ('{$userid}', '{$_POST['user']}', '{$_POST['item']}', '{$_POST['qty']}')");
        alert('success',"Success!","You have successfully placed your gift under the Christmas tree.",true,'?action=tree');
    }
    else
    {
        echo "
        <form method='post'>
        <div class='card'>
            <div class='card-header'>
                Christmas Gift Form
            </div>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-12'>
                        <div class='row'>
                            <div class='col-12 text-muted text-sm'>
                                <small>Gift Receiver</small>
                            </div>
                            <div class='col-12'>
                                " . user_dropdown() . "
                            </div>
                        </div>
                    </div>
                    <div class='col-12'>
                        <div class='row'>
                            <div class='col-12 text-muted text-sm'>
                                <small>Gift Item</small>
                            </div>
                            <div class='col-12'>
                                " . inventory_dropdown() . "
                            </div>
                        </div>
                    </div>
                    <div class='col-12'>
                        <div class='row'>
                            <div class='col-12 text-muted text-sm'>
                                <small>Gift Quantity</small>
                            </div>
                            <div class='col-12'>
                                <input type='number' min='1' value='1' max='" . PHP_INT_MAX . "' required='1' class='form-control' name='qty'>
                            </div>
                        </div>
                    </div>
                    <div class='col-12'>
                        <div class='row'>
                            <div class='col-12 text-muted text-sm'>
                                &nbsp;
                            </div>
                            <div class='col-12'>
                                <input type='submit' class='btn btn-success btn-block' value='Gift!'>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>";
    }
    $h->endpage();
}

function open()
{
    global $db,$userid,$api,$h,$ir,$month,$day,$set;
    //Check to make sure christmas has come and pass
    if (($month == 12 && $day < 25) || ($month == 1 && $day > 6))
    {
        alert('danger',"Uh Oh!","You may only open your gifts on/after Christmas Day.",true,'?action=tree');
        die($h->endpage());
    }
    $_GET['gift'] = (isset($_GET['gift']) && is_numeric($_GET['gift'])) ? abs($_GET['gift']) : '';
    if (empty($_GET['gift']))
    {
        alert('danger', "Uh Oh!", "Please select a valid gift you wish to open", true, '?action=tree');
        die($h->endpage());
    }
    $q=$db->query("SELECT * FROM `2018_christmas_tree` WHERE `gift_id` = {$_GET['gift']} AND `userid_to` = {$userid}");
    if ($db->num_rows($q) == 0)
    {
        alert('danger', "Uh Oh!", "Gift either does not exist, or isn't yours to open.", true, '?action=tree');
        die($h->endpage());
    }
    $r=$db->fetch_row($q);
    alert('success',"Merry Christmas!","You've opened your gift from {$api->SystemUserIDtoName($r['userid_from'])} and received " . shortNumberParse($r['qty']) . " x {$api->SystemItemIDtoName($r['item'])}(s)!",true,'?action=tree');
    $api->UserGiveItem($userid,$r['item'],$r['qty']);
    $log = $db->escape("Sent {$r['qty']} {$api->SystemItemIDtoName($r['item'])}(s) to {$ir['username']} [{$userid}].");
    $api->SystemLogsAdd($r['userid_from'], 'itemsend', $log);
    $api->GameAddNotification($r['userid_from'],"{$ir['username']} [{$userid}] has opened your Christmas Gift of " . shortNumberParse($r['qty']) . " x {$api->SystemItemIDtoName($r['item'])}(s)!");
    $db->query("DELETE FROM `2018_christmas_tree` WHERE `gift_id` = {$_GET['gift']}");
    $h->endpage();
}

function wish()
{
    global $db,$userid,$api,$h,$set;
    $q=$db->query("SELECT * FROM `2018_christmas_wishes` WHERE `userid` = {$userid}");
    echo "<h3>{$set['WebsiteName']} Christmas Wish</h3><hr />";
    if ($db->num_rows($q) != 0)
    {
        alert('danger',"Uh Oh!","You've already submitted your wish to <s>Santa</s> CID Admin for Christmas.",true,'explore.php');
        die($h->endpage());
    }
    else
    {
        if (isset($_POST['wish']))
        {
            $wish = $db->escape(nl2br(htmlentities(stripslashes($_POST['wish']), ENT_QUOTES, 'ISO-8859-1')));
            $db->query("INSERT INTO `2018_christmas_wishes` (`userid`, `wish`) VALUES ({$userid}, '{$wish}')");
            alert('success', "Success!", "You have successfully sent <s>Santa</s> CID Admin your wish for Christmas!", true, 'explore.php');
            $h->endpage();
        }
        else
        {
            echo "It appears you wish to make a wish for Christmas! Jolly Ol Saint <s>Santa</s> CID Admin will try to fulfill your request, but if its too much, you might
            be added to the Naughty List and only receive a Lump of Coal. That's no fun for anyone.<br />
            So, fill out the form to submit your wish to Santa! Don't ask for too much.<br />
            <form method='post'>
                <textarea class='form-control' name='wish' required='1'>I wish for...</textarea><br />
                <input type='submit' class='btn btn-primary' value='Wish'>
            </form>";
            $h->endpage();
        }
    }
    
}

function cardBGColorRNG()
{
    $color = Random(1,9);
    switch ($color)
    {
        case 1:
            $return = "text-white bg-primary";
            break;
        case 2:
            $return = "text-white bg-secondary";
            break;
        case 3:
            $return = "text-white bg-success";
            break;
        case 4:
            $return = "text-white bg-danger";
            break;
        case 5:
            $return = "text-white bg-warning";
            break;
        case 6:
            $return = "text-white bg-info";
            break;
        case 7:
            $return = "bg-light";
            break;
        case 8:
            $return = "text-white bg-dark";
            break;
        case 9:
            $return = "";
            break;
    }
    return $return;
}