<?php
require('globals.php');
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "tree":
        tree();
        break;
	case "wishing":
        wishing();
        break;
    case "snowcollect":
        snowcollect();
        break;
    case "open":
        open();
        break;
    case "gift":
        gift();
        break;
    case "ticket":
        ticket();
        break;
    default:
        alert('danger',"Uh Oh!","Please specify a valid action.",true,'index.php');
		$h->endpage();
        break;
}
function ticket()
{
	global $h,$db,$api,$userid;
	if (!$api->UserHasItem($userid,276,1))
	{
		alert('danger',"Uh Oh!","You need a 2019 Christmas Scratch Ticket to be here.",true,'inventory.php');
		die($h->endpage());
	}
	if (isset($_GET['scratch']))
	{
		$rng=Random(1,6);
		if ($rng == 1)
		{
			$cash=Random(50000,1000000);
			alert("success","Success!","You scratch this spot off and you win {$cash} Copper Coins. Congratulations!",true,'inventory.php');
			$api->UserGiveCurrency($userid,'primary',$cash);
		}
		elseif ($rng == 2)
		{
			$cash=Random(1000,5000);
			alert("success","Success!","You scratch this spot off and you win {$cash} Chivalry Tokens. Congratulations!",true,'inventory.php');
			$api->UserGiveCurrency($userid,'secondary',$cash);
		}
		elseif ($rng == 3)
		{
            $rng=Random(10,50);
			alert("success","Success!","You scratch this spot off and you win {$rng} Snowballs. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,202,$rng);
		}
		elseif ($rng == 4)
		{
			$cash=Random(15,50);
			alert("success","Success!","You scratch this spot off and you win {$cash} Chivalry Gym Scrolls. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,18,$cash);
		}
		elseif ($rng == 5)
		{
			$cash=Random(5,15);
			alert("success","Success!","You scratch this spot off and you win {$cash} Medium Explosives. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,61,$cash);
		}
		else
		{
			$cash=Random(2,7);
			alert("success","Success!","You scratch this spot off and you win {$cash} VIP Days. Congratulations!",true,'inventory.php');
			$db->query("UPDATE `users` SET `vip_days` = `vip_days` + {$cash} WHERE `userid` = {$userid}");
		}
		$api->UserTakeItem($userid,276,1);
	}
	else
	{
		echo "Select the spot you wish to scratch off. You shall receive rewards.<br />
		<div class='row'>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='assets/img/present-christmas.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='assets/img/present-christmas.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='assets/img/present-christmas.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='assets/img/present-christmas.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='assets/img/present-christmas.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='assets/img/present-christmas.png' class='img-fluid'></a>
			</div>
		</div>";
	}
	$h->endpage();
}
function wishing()
{
	global $db,$userid,$api,$h;
	$q=$db->query("SELECT * FROM `2018_christmas_wishes` WHERE `userid` = {$userid}");
    echo "<h3>Christmas Wish</h3>";
	if ($db->num_rows($q) != 0)
	{
		alert('danger',"Uh Oh!","You've already submitted your wish to Santa for Christmas.",true,'explore.php');
		die($h->endpage());
	}
    else
    {
        if (isset($_POST['wish']))
        {
            $wish = $db->escape(nl2br(htmlentities(stripslashes($_POST['wish']), ENT_QUOTES, 'ISO-8859-1')));
            $db->query("INSERT INTO `2018_christmas_wishes` (`userid`, `wish`) VALUES ({$userid}, '{$wish}')");
            alert('success', "Success!", "You have successfully sent Santa your wish for Christmas!", true, 'explore.php');
            $h->endpage();
        }
        else
        {
            echo "It appears you wish to make a wish for Christmas! Jolly Ol Saint Nick will try to fulfill your request, but if its too much, you might 
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
function tree()
{
    global $db,$userid,$api,$h;
    echo "<h3>Christmas Tree</h3><hr />
    Here's the Chivalry is Dead Christmas tree. You can place gifts under the tree for your friends to open on/after Christmas Day. 
    You may place a gift <a href='?action=gift'>here</a>.<hr />
    <h5>Gifts Under the Tree</h5><hr />";
    $q=$db->query("SELECT * FROM `2018_christmas_tree`");
    if ($db->num_rows($q) == 0)
    {
        alert('danger',"Uh Oh!","There's no gifts under the tree yet...",false);
    }
    else
    {
        echo "<table class='table table-bordered'>
        <tr>
            <th>
                Info
            </th>
            <th>
                Links
            </th>
        </tr>";
        while ($r=$db->fetch_row($q))
        {
            echo "<tr>
                <td>
                    From: <a href='profile.php?user={$r['userid_from']}'>{$api->SystemUserIDtoName($r['userid_from'])}</a> [{$r['userid_from']}]<br />
                    To: <a href='profile.php?user={$r['userid_to']}'>{$api->SystemUserIDtoName($r['userid_to'])}</a> [{$r['userid_to']}]";
                    if ($userid == $r['userid_from'])
                    {
                        echo "<br />Gift: {$r['qty']} x {$api->SystemItemIDtoName($r['item'])}(s)";
                    }
                    echo"
                </td>
                <td>";
                    if (($userid == $r['userid_from']) || ($r['userid_to'] != $userid))
                    {
                        echo "No links for you.";
                    }
                    else
                    {
                        echo "<a href='?action=open&gift={$r['gift_id']}' class='btn btn-primary'>Open Gift</a>";
                    }
                    echo "
                </td>
            </tr>";
        }
        echo "</table>";
    }
    $h->endpage();
}
function open()
{
    global $db,$userid,$api,$h,$ir;
	$xmasTime = 1577250000;
    if (time() < $xmasTime)
    {
        alert('danger',"Uh Oh!","You may only open your gifts on/after Christmas Day.",true,'2018christmas.php?action=tree');
        die($h->endpage());
    }
    $_GET['gift'] = (isset($_GET['gift']) && is_numeric($_GET['gift'])) ? abs($_GET['gift']) : '';
    if (empty($_GET['gift']))
    {
        alert('danger', "Uh Oh!", "Please select a valid gift you wish to open", true, '2018christmas.php?action=tree');
        die($h->endpage());
    }
    $q=$db->query("SELECT * FROM `2018_christmas_tree` WHERE `gift_id` = {$_GET['gift']} AND `userid_to` = {$userid}");
    if ($db->num_rows($q) == 0)
    {
        alert('danger', "Uh Oh!", "Gift either does not exist, or isn't yours to open.", true, '2018christmas.php?action=tree');
        die($h->endpage());
    }
    $r=$db->fetch_row($q);
    alert('success',"Merry Christmas!","You've opened your gift from {$api->SystemUserIDtoName($r['userid_from'])} and received {$r['qty']} x {$api->SystemItemIDtoName($r['item'])}(s)!",true,'2018christmas.php?action=tree');
    $api->UserGiveItem($userid,$r['item'],$r['qty']);
    $log = $db->escape("Sent {$r['qty']} {$api->SystemItemIDtoName($r['item'])}(s) to {$ir['username']} [{$userid}].");
    $api->SystemLogsAdd($r['userid_from'], 'itemsend', $log);
    $api->GameAddNotification($r['userid_from'],"{$ir['username']} [{$userid}] has opened your Christmas Gift of {$r['qty']} x {$api->SystemItemIDtoName($r['item'])}(s)!");
    $db->query("DELETE FROM `2018_christmas_tree` WHERE `gift_id` = {$_GET['gift']}");
    $h->endpage();
}

function gift()
{
    global $db,$userid,$api,$h,$ir;
    echo "<h3>Christmas Gift Form</h3><hr />";
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
            alert('danger',"Uh Oh!","You can't be lonely enough to want to gift yourself something for Christmas.");
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
        alert('success',"Success!","You have successfully placed your gift under the Christmas tree.",true,'2018christmas.php?action=tree');
    }
    else
    {
        echo "Fill out the form completely to send a friend a gift for Christmas.
        <form method='post'>
            <table class='table table-bordered'>
                <tr>
                    <th>
                        Player
                    </th>
                    <td>
                        " . user_dropdown() . "
                    </td>
                </tr>
                <tr>
                    <th>
                        Item
                    </th>
                    <td>
                        " . inventory_dropdown() . "
                    </td>
                </tr>
                <tr>
                    <th>
                        Quantity
                    </th>
                    <td>
                        <input type='number' min='1' max='" . PHP_INT_MAX . "' required='1' class='form-control' name='qty'>
                    </td>
                </tr>
                <tr>
                    <td colspan='2'>
                        <input type='submit' class='btn btn-success' value='Gift!'>
                    </td>
                </tr>
            </table>
        </form>";
    }
    $h->endpage();
}

function snowcollect()
{
    global $db,$userid,$api,$h,$ir;
    echo "<h3>Snow Collection</h3><hr />";
    //Block access if user is in the infirmary.
    if ($api->UserStatus($ir['userid'], 'infirmary')) {
        alert('danger', "Unconscious!", "You cannot collect snowballs while you're in the infirmary.", false);
        die($h->endpage());
    }
    //Block access if user is in the dungeon.
    if ($api->UserStatus($ir['userid'], 'dungeon')) {
        alert('danger', "Locked Up!", "You cannot collect snowballs while you're in the dungeon.");
        die($h->endpage());
    }
    if (isset($_GET['collect']))
    {
        if ($api->UserInfoGet($userid, 'energy', true) < 50)
        {
            alert('danger',"Uh Oh!","You do not have enough energy to attempt to collect snow",true,'explore.php');
            die($h->endpage());
        }
        if ($api->UserInfoGet($userid, 'brave', true) < 20)
        {
            alert('danger',"Uh Oh!","You do not have enough brave to attempt to collect snow",true,'explore.php');
            die($h->endpage());
        }
        if ($api->UserInfoGet($userid, 'will', true) < 10)
        {
            alert('danger',"Uh Oh!","You do not have enough will to attempt to collect snow",true,'explore.php');
            die($h->endpage());
        }
        $api->UserInfoSet($userid,'will',-10,true);
        $api->UserInfoSet($userid,'brave',-20,true);
        $api->UserInfoSet($userid,'energy',-50,true);
        $snowballs=Random(3,7);
        $chance=Random(1,3);
        if ($chance == 1)
        {
            alert('success',"Success!","You have successfully collected {$snowballs} Snowballs. They are in your inventory.",true,'inventory.php');
            $api->UserGiveItem($userid,202,$snowballs);
            die($h->endpage());
        }
        if ($chance == 2)
        {
            $time=Random(1,3)*$ir['level'];
            alert('danger',"Uh Oh!","You spend hours outside trying to find some snow and end up freezing. You wake up in the infirmary...",true,'infirmary.php');
            $api->UserStatusSet($userid,'infirmary',$time,"Frozen Alive");
            die($h->endpage());
        }
        if ($chance == 3)
        {
            alert('danger',"Uh Oh!","You don't find any snow... not sure why.",true,'explore.php');
            die($h->endpage());
        }
    }
    else
    {
        echo "Would you like to collect some snow? It requires 50% energy, 10% Will and 20% Brave.<br />
        <a class='btn btn-primary' href='?action=snowcollect&collect=true'>Collect Snow</a>";
    }
    $h->endpage();
}