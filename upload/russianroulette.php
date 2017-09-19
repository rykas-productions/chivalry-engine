<?php
/*
	File: 		russianroulette.php
	Created: 	5/2/2017 at 12:38PM Eastern Time
	Info: 		Allows players to play a round of russian roulette.
	Author: 	TheMasterGeneral, Original by ImJustIsabella
	Website:	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
echo "<h3>Russian Roulette</h3><hr />";
//Do not allow the user to play Russian Roulette if they're in the dungeon/infirmary.
if ($api->UserStatus($userid,'dungeon'))
{
    alert('danger', "Uh Oh!", "You cannot play Russian Roulette while in the dungeon.");
    die($h->endpage());
}
if ($api->UserStatus($userid,'infirmary'))
{
    alert('danger', "Uh Oh!", "You cannot play Russian Roulette while in the infirmary.");
    die($h->endpage());
}
//Action switch to get the user's action. If not set, redirect to home function.
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'do':
        dorr();
        break;
    case 'dont':
        dontrr();
        break;
    case 'withdraw':
        wdrr();
        break;
    case 'bet':
        bet();
        break;
    default:
        home();
        break;
}
function home()
{
    global $db,$api,$h,$userid;
    echo "
    <form method='post' action='?action=bet'>
    <table class='table table-bordered'>
        <tr>
            <th colspan='2'>
                Welcome to Russian Roulette, good sir! Who would you like to challenge? Remember, the Russian Roulette center
                takes a 10% fee on all winnings.
            </th>
        </tr>
        <tr>
            <th>
                 User
            </th>
            <td>
                " . user_dropdown('user',$userid) . "
            </td>
        </tr>
        <tr>
             <th>
                 Bet
            </th>
            <td>
                <input type='number' name='bet' min='0' value='0' required='1' class='form-control'>
            </td>
        </tr>
        <tr>
            <td colspan='2'>
                <input type='submit' class='btn btn-primary' value='Send Challenge'>
            </td>
        </tr>
    </table>
    </form>
    <h4>Challenges Involving You</h4>";
    echo "<table class='table'>
        <tr>
            <th>
                Challenger
            </th>
            <th>
                Receiver
            </th>
            <th>
                Bet
            </th>
            <th>
                Actions
            </th>
        </tr>";
    //List the challenges the player has sent/received.
   $q=$db->query("SELECT * FROM `russian_roulette` WHERE `challengee` = {$userid} OR `challenger` = {$userid}");
    while ($r=$db->fetch_row($q))
    {
        //Player is the challenger in this challenge. Give them option to withdraw bet.
        if ($userid == $r['challenger'])
            $link = "<a href='?action=withdraw&id={$r['challengee']}'>Withdraw</a>";
        //Player is the receiver, give them option to decline or accept.
        else
            $link = "<a href='?action=do&id={$r['challenger']}'>Accept</a><br />
                        <a href='?action=dont&id={$r['challenger']}'>Decline</a>";
        echo "
        <tr>
            <td>
                {$api->SystemUserIDtoName($r['challenger'])} [{$r['challenger']}]
            </td>
            <td>
                {$api->SystemUserIDtoName($r['challengee'])} [{$r['challengee']}]
            </td>
            <td>
                " . number_format($r['reward']) . " Primary Currency
            </td>
            <td>
                {$link}
            </td>
        </tr>";
    }
    echo"</table>";
    $h->endpage();
}
function bet()
{
    global $db,$api,$h,$userid,$ir;
    $_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : '';
    $_POST['bet'] = (isset($_POST['bet']) && is_numeric($_POST['bet'])) ? abs($_POST['bet']) : '';
    //User to bet is empty.
    if (empty($_POST['user'])) {
        alert('danger', "Uh Oh!", "Please select a valid user to play against.");
        die($h->endpage());
    }
    $q=$db->query("SELECT `userid`
                    FROM `users`
                    WHERE `userid` = {$_POST['user']}");
    //User to challenge does not exist.
    if ($db->num_rows($q) == 0)
    {
        alert('danger', "Uh Oh!", "The user you are trying to bet against does not exist.",true,'russianroulette.php');
        die($h->endpage());
    }
    //User is trying to challenge themselves
    if ($_POST['user'] == $userid)
    {
        alert('danger', "Uh Oh!", "You are not allowed to challenge yourself to Russian Roulette.",true,'russianroulette.php');
        die($h->endpage());
    }
    //Current player does not have enough Primary Currency for their bet.
    if (!$api->UserHasCurrency($userid,'primary',$_POST['bet']))
    {
        alert('danger', "Uh Oh!", "You do not have enough Primary Currency to bet {$_POST['bet']}. You only have {$ir['primary_currency']}.",true,'russianroulette.php');
        die($h->endpage());
    }
    //User has already been challenge by the current player.
    $q2=$db->query("SELECT `challengee`
                    FROM `russian_roulette`
                    WHERE `challengee` = {$_POST['user']}
                    AND `challenger` = {$userid}");
    if ($db->num_rows($q2) > 0)
    {
        alert('danger', "Uh Oh!", "You already have a bet sent to this user. Remind them to accept the challenge, or withdraw it.",true,'russianroulette.php');
        die($h->endpage());
    }
    //All checks pass, so lets add it to the database, logs, and whatnot...
    $db->query("INSERT INTO `russian_roulette`
                (`challengee`, `challenger`, `reward`)
                VALUES ('{$_POST['user']}', '{$userid}', '{$_POST['bet']}');");
    //Alert the receiver
    $NotifText="<a href='profile.php?user={$userid}'>{$api->SystemUserIDtoName($userid)}</a> has challenged you to a round of Russian Roulette. View the challenge <a href='russianroulette.php'>here</a>.";
    $api->GameAddNotification($_POST['user'],$NotifText);
    $api->UserTakeCurrency($userid,'primary',$_POST['bet']);
    alert('success', "Success!", "You have successfully challenged {$api->SystemUserIDtoName($_POST['user'])} to a round of Russian Roulette.",true,'russianroulette.php');
    $api->SystemLogsAdd($userid,'rr',"Challenged <a href='../profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a>. Bet: {$_POST['bet']}.");
    die($h->endpage());
}
function wdrr()
{
    global $db,$api,$h,$userid;
    $_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
    //GET is empty.
    if (empty($_GET['id']))
    {
        alert('danger', "Uh Oh!", "Please select a valid user to withdraw your challenge from.");
        die($h->endpage());
    }
    $q=$db->query("SELECT `userid`
                    FROM `users`
                    WHERE `userid` = {$_GET['id']}");
    //Person to withdraw against does not exist.
    if ($db->num_rows($q) == 0)
    {
        alert('danger', "Uh Oh!", "The user you are trying to withdraw your challenge from does not exist.",true,'russianroulette.php');
        die($h->endpage());
    }
    $q2=$db->query("SELECT `challengee`
                    FROM `russian_roulette`
                    WHERE `challengee` = {$_GET['id']}
                    AND `challenger` = {$userid}");
    //User does not have any challenges from the current player.
    if ($db->num_rows($q2) == 0)
    {
        alert('danger', "Uh Oh!", "You do not have any challenges open with this user.",true,'russianroulette.php');
        die($h->endpage());
    }
    //Checks passed, delete it all.
    $r=$db->fetch_single($db->query("SELECT `reward`
                                    FROM `russian_roulette`
                                    WHERE `challengee` = {$_GET['id']}
                                    AND `challenger` = {$userid}"));
    $api->UserGiveCurrency($userid,'primary',$r);
    $NotifText="<a href='profile.php?user={$userid}'>{$api->SystemUserIDtoName($userid)}</a> has withdrawn their Russian Roulette challenge.";
    $api->GameAddNotification($_GET['id'],$NotifText);
    $db->query("DELETE FROM `russian_roulette`
                WHERE `challengee` = {$_GET['id']}
                AND `challenger` = {$userid}");
    alert('success', "Success!", "You have successfully withdrawn your Russian Roulette challenge against {$api->SystemUserIDtoName($_GET['id'])}.",true,'russianroulette.php');
    $api->SystemLogsAdd($userid,'rr',"Withdrew challenge against <a href='../profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a>. Bet: {$r}.");
    die($h->endpage());
}
function dorr()
{
    global $db,$userid,$api,$h;
    $_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
    //GET is empty.
    if (empty($_GET['id']))
    {
        alert('danger', "Uh Oh!", "Please select a valid user to accept the challenge from.");
        die($h->endpage());
    }
    $q=$db->query("SELECT `userid`
                    FROM `users`
                    WHERE `userid` = {$_GET['id']}");
    //Person to withdraw against does not exist.
    if ($db->num_rows($q) == 0)
    {
        alert('danger', "Uh Oh!", "The user you are trying to accept the challenge from does not exist.",true,'russianroulette.php');
        die($h->endpage());
    }
    $q2=$db->query("SELECT `challengee`
                    FROM `russian_roulette`
                    WHERE `challengee` = {$userid}
                    AND `challenger` = {$_GET['id']}");
    //User does not have any challenges from the current player.
    if ($db->num_rows($q2) == 0)
    {
        alert('danger', "Uh Oh!", "You do not have any challenges open with this user.",true,'russianroulette.php');
        die($h->endpage());
    }
    $r=$db->fetch_single($db->query("SELECT `reward`
                                    FROM `russian_roulette`
                                    WHERE `challengee` = {$userid}
                                    AND `challenger` = {$_GET['id']}"));
    if (!$api->UserHasCurrency($userid,'primary',$r))
    {
        alert('danger', "Uh Oh!", "You do not have enough Primary Currency to accept this bet. You need to have {$r}.",true,'russianroulette.php');
        die($h->endpage());
    }
    //The checks have passed... lets do it!
    $api->UserTakeCurrency($userid,'primary',$r);
    $max=PHP_INT_MAX;
    $half=$max/2;
    $rand=Random(0,$max);
    $actuallywon=(($r*2)*0.9);
    if ($rand <= $half)
    {
        //You win
        alert('success',"Success!","You play a round of Russian Roulette against {$api->SystemUserIDtoName($_GET['id'])}
         and won " . number_format($actuallywon) . " Primary Currency.",true,'index.php');
        $winner=$userid;
        $loser=$_GET['id'];
        $NotifText="You lost a round of Russian Roulette against {$api->SystemUserIDtoName($userid)} and lost {$r} Primary Currency.";
        $api->SystemLogsAdd($userid,'rr',"Won against <a href='../profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a>. Gained: {$actuallywon}.");
        $api->SystemLogsAdd($_GET['id'],'rr',"Lost against <a href='../profile.php?user={$userid}'>{$api->SystemUserIDtoName($userid)}</a>. Lost: {$actuallywon}.");
    }
    else
    {
        //You lose
        alert('danger',"Uh Oh!","You play a round of Russian Roulette against {$api->SystemUserIDtoName($_GET['id'])}
         and lost " . number_format($actuallywon) . " Primary Currency.",true,'index.php');
        $winner=$_GET['id'];
        $loser=$userid;
        $NotifText="You won a round of Russian Roulette against {$api->SystemUserIDtoName($userid)} and gained {$r} Primary Currency.";
        $api->SystemLogsAdd($_GET['id'],'rr',"Won against <a href='../profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a>. Gained: {$actuallywon}.");
        $api->SystemLogsAdd($userid,'rr',"Lost against <a href='../profile.php?user={$userid}'>{$api->SystemUserIDtoName($userid)}</a>. Lost: {$actuallywon}.");
    }
    $api->GameAddNotification($_GET['id'],$NotifText);
    $api->UserGiveCurrency($winner,'primary',$actuallywon);
    $api->UserStatusSet($loser,'infirmary',random(10,35),"Deadly Games");
    $api->UserInfoSetStatic($loser,'hp',0);
    $db->query("DELETE FROM `russian_roulette`
                WHERE `challenger` = {$_GET['id']}
                AND `challengee` = {$userid}");
    $h->endpage();
}
function dontrr()
{
    global $db,$userid,$api,$h;
    $_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
    //GET is empty.
    if (empty($_GET['id']))
    {
        alert('danger', "Uh Oh!", "Please select a valid user to decline a challenge from.");
        die($h->endpage());
    }
    $q=$db->query("SELECT `userid`
                    FROM `users`
                    WHERE `userid` = {$_GET['id']}");
    //Person to withdraw against does not exist.
    if ($db->num_rows($q) == 0)
    {
        alert('danger', "Uh Oh!", "The user you are trying to decline a challenge that does not exist.",true,'russianroulette.php');
        die($h->endpage());
    }
    $q2=$db->query("SELECT `challengee`
                    FROM `russian_roulette`
                    WHERE `challengee` = {$userid}
                    AND `challenger` = {$_GET['id']}");
    //User does not have any challenges from the current player.
    if ($db->num_rows($q2) == 0)
    {
        alert('danger', "Uh Oh!", "You do not have any challenges open with this user.",true,'russianroulette.php');
        die($h->endpage());
    }
    $r=$db->fetch_single($db->query("SELECT `reward`
                                    FROM `russian_roulette`
                                    WHERE `challengee` = {$userid}
                                    AND `challenger` = {$_GET['id']}"));
    //The checks have passed... lets do it!
    $r=$db->fetch_single($db->query("SELECT `reward`
                                    FROM `russian_roulette`
                                    WHERE `challengee` = {$_GET['id']}
                                    AND `challenger` = {$userid}"));
    $api->UserGiveCurrency($_GET['id'],'primary',$r);
    $NotifText="<a href='profile.php?user={$userid}'>{$api->SystemUserIDtoName($userid)}</a> has declined yourr Russian Roulette challenge. Your cash has been returned.";
    $api->GameAddNotification($_GET['id'],$NotifText);
    $db->query("DELETE FROM `russian_roulette`
                WHERE `challenger` = {$_GET['id']}
                AND `challengee` = {$userid}");
    alert('success', "Success!", "You have successfully declined the Russian Roulette challenge from {$api->SystemUserIDtoName($_GET['id'])}.",true,'russianroulette.php');
    $api->SystemLogsAdd($userid,'rr',"Declined <a href='../profile.php?user={$_POST['user']}'>{$api->SystemUserIDtoName($_POST['user'])}</a>'s match.");
    die($h->endpage());
}