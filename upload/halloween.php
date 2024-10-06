<?php
/*	File:		23halloween.php
    Created: 	Oct 22, 2022; 7:59:05 PM
    Info:
    Author:		Ryan
    Website: 	https://chivalryisdeadgame.com/
 */
require('globals.php');						//uncomment if user needs to be auth'd.
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
    case "ticket":
        ticket();
        break;
    case "pumpkin":
        pumpkin();
        break;
    case "tnt":
        trick_or_treat();
        break;
    case "chuck":
        chuck();
        break;
    default:
        alert('danger',"Uh Oh!","Please specify a valid action.",true,'explore.php');
        $h->endpage();
        break;
}

function trick_or_treat()
{
    global $db, $userid, $api, $h;
    $_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : '';
    if (currentMonth() != 10)
    {
        alert('danger',"Uh Oh!", "Fool, its not even October! Don't make me tell CID Admin.", true, "profile.php?user={$userid}");
        die($h->endpage());
    }
    if (empty($_GET['user']))
    {
        alert('danger',"Uh Oh!", "Please select a valid player to visit and treat or treat from.", true, "profile.php?user={$userid}");
        die($h->endpage());
    }
    $q=$db->query("SELECT `username` FROM `users` WHERE `userid` = {$_GET['user']}");
    if ($db->num_rows($q) == 0)
    {
        alert('danger',"Uh Oh!", "You cannot trick or treat from non-existent players.", true, "profile.php?user={$userid}");
        die($h->endpage());
    }
    if ($_GET['user'] == $userid)
    {
        alert('danger',"Uh Oh!", "You cannot trick or treat from yourself...", true, "profile.php?user={$_GET['user']}");
        die($h->endpage());
    }
    $db->free_result($q);
    $q=$db->query("SELECT * FROM `2018_halloween_tot` WHERE `userid` = {$userid} AND `visited` = {$_GET['user']}");
    if ($db->num_rows($q) > 0)
    {
        alert('danger',"Uh Oh!", "You've already visited this player this hour. It'd be a little rude to visit again so soon.", true, "profile.php?user={$_GET['user']}");
        die($h->endpage());
    }
    $candyItems = array(66,139,201,279,282,466,467,468,469);    //add if more candy
    $giftedItem = array_rand($candyItems, 1);
    $candy = $candyItems[$giftedItem];
    $newValue = getCurrentUserPref(currentYear() . "halloweenCandies",0) + 1;
    alert('success',"Trick or Treat!","While visiting {$api->SystemUserIDtoName($_GET['user'])}'s property for some candy, you were given a {$api->SystemItemIDtoName($candy)} and sent along your way. You've collected " . shortNumberParse($newValue) . " candies this Halloween season.", true, "profile.php?user={$_GET['user']}");
    $api->UserGiveItem($userid, $candy, 1);
    $api->SystemLogsAdd($userid, date('Y') . "halloween", "Given {$api->SystemItemIDtoName($candy)} from {$api->SystemUserIDtoName($_GET['user'])} " . parseUserID($_GET['user']));
    setCurrentUserPref(date('Y') . "halloweenCandies", $newValue);
    $api->GameAddNotification($_GET['user'], "<a href='profile.php?user={$userid}'>" . parseUsername($userid) . " " . parseUserID($userid) . "</a> has visited you for Halloween! You gave them a {$api->SystemItemIDtoName($candy)} and sent them along their merry way.");
    doHalloweenVisit($userid, $_GET['user']);
    $h->endpage();
}

function chuck()
{
    global $db,$api,$userid,$h,$ir;
    $maxThrownPerHour = 12;
    $bestThrow = getCurrentUserPref(currentYear() . "halloweenBestThrow", 0);
    $currentThrows = getCurrentUserPref(currentYear() . "halloweenDailyThrow", 0);
    
    if (isset($_GET['throw']))
    {
        $distance=Random(100,10000);
        if (currentMonth() != 10)
        {
            alert('danger',"Uh Oh!", "Fool, its not even October! Don't make me tell CID Admin.", true, "profile.php?user={$userid}");
            die($h->endpage());
        }
        if (!$api->UserHasItem($userid,64,1))
        {
            alert('danger',"Uh Oh!","You need a Pumpkin to even throw one. You may buy one from the Cornrye Pub.",true,'?action=chuck');
            die($h->endpage());
        }
        if ($currentThrows == $maxThrownPerHour)
        {
            alert('danger',"Uh Oh!","You've already given it your best shot, bud. Let others have a chance. You may try again at the top of the hour.",true,'?action=chuck');
            die($h->endpage());
        }
        if ($distance > $bestThrow)
            setCurrentUserPref(currentYear() . "halloweenBestThrow", $distance);
            setCurrentUserPref(currentYear() . "halloweenDailyThrow", $currentThrows + 1);
        alert("success","Success!","You've successfully chucked your pumpkin and achieved a wonderful distance of " . shortNumberParse($distance) . " meters.", true, '?action=chuck');
        $api->UserTakeItem($userid,64,1);
    }
    else
    {
        $hq=$db->query("SELECT * FROM `user_pref` WHERE `value` > 0  AND `preference` = '" . currentYear() . "halloweenBestThrow' ORDER BY `value` desc LIMIT 5");
        echo "
        <div class='card'>
            <div class='card-header'>
                <b>" . currentYear() . " Halloween Pumpkin Chuck Contest</b>
            </div>
            <div class='card-body'>
               Welcome to the Pumpkin Chuck, {$ir['username']}. You may chuck your pumpkin to see how far it'll go. The player who throws the furthest will
	           receive a unique prize. The two runner-ups will receive a small little prize as well! Remember, your only your best throw will be put into the scorebook.
                You may throw up to {$maxThrownPerHour} times per hour.<br />
	           <b>You have thrown {$currentThrows} out of 12 times this hour. Your current max distance thrown is " . shortNumberParse($bestThrow) . " meters.</b><br />
            </div>
        </div>";
        echo "<br />
        <div class='card'>
            <div class='card-header'>
                <b><u>Top 5 Scores</u></b>
            </div>
            <div class='card-body'>";
                while ($r2=$db->fetch_row($hq))
                {
                    echo "  <div class='row'>
                                <div class='col-auto'>
                                    {$api->SystemUserIDtoName($r2['userid'])} " . parseUserID($r2['userid']) . "
                                </div>
                                <div class='col-auto'>
                                    " . shortNumberParse($r2['value']) . " meters
                                </div>
                            </div>";
                }
            echo"</div>
        </div>";
        if (currentMonth() == 10)
        {
            echo "<a class='btn btn-primary' href='?action=chuck&throw=1'>Chuck Pumpkin</a>";
        }
    }
}

function doHalloweenVisit($visitor, $visited)
{
    global $db;
    $db->query("INSERT INTO `2018_halloween_tot` (`userid`, `visited`) VALUES ('{$visitor}', '{$visited}')");
}
$h->endpage();