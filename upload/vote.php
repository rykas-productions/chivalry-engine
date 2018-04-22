<?php
/*
	File:		bomb.php
	Created: 	10/18/2017 at 10:49AM Eastern Time
	Info: 		Blow up your opponent.
	Author:		TheMasterGeneral
	Website: 	http://chivalryisdead.x10.mx/
*/
require('globals.php');
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "twg":
        twg();
        break;
    case "top100":
        top100();
        break;
    case "mgpoll":
        mgpoll();
        break;
	case "apex":
		apex();
		break;
	case "dog":
		dog();
		break;
    case "bbogd":
		bbogd();
		break;
    default:
        home();
        break;
}
function home()
{
    global $set, $h, $db, $userid;
    echo "Here you may vote for {$set['WebsiteName']} at various RPG toplists and be rewarded. Whether or not you voted is
	logged. If you scam this system, you will be dealt with severely. If you do not get rewarded, try voting again later.
	<br />";
	$q = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `votes` WHERE `userid` = $userid AND `voted` = 'twg'");
	$vote_count = $db->fetch_single($q);
	$db->free_result($q);
	if ($vote_count > 0)
	{
		$twgvote="<span class='text-danger'>Already voted.</span>";
	}
	else
	{
		$twgvote="<a href='?action=twg'>Vote</a>";
	}
	$q = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `votes` WHERE `userid` = $userid AND `voted` = 'top100'");
	$vote_count = $db->fetch_single($q);
	$db->free_result($q);
	if ($vote_count > 0)
	{
		$thavote="<span class='text-danger'>Already voted.</span>";
	}
	else
	{
		$thavote="<a href='?action=top100'>Vote</a>";
	}
	$q = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `votes` WHERE `userid` = $userid AND `voted` = 'mgp'");
	$vote_count = $db->fetch_single($q);
	$db->free_result($q);
	if ($vote_count > 0)
	{
		$mgpollvote="<span class='text-danger'>Already voted.</span>";
	}
	else
	{
		$mgpollvote="<a href='?action=mgpoll'>Vote</a>";
	}
	$q = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `votes` WHERE `userid` = $userid AND `voted` = 'apex'");
	$vote_count = $db->fetch_single($q);
	$db->free_result($q);
	if ($vote_count > 0)
	{
		$awgvote="<span class='text-danger'>Already voted.</span>";
	}
	else
	{
		$awgvote="<a href='?action=apex'>Vote</a>";
	}
	$q = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `votes` WHERE `userid` = $userid AND `voted` = 'dog'");
	$vote_count = $db->fetch_single($q);
	$db->free_result($q);
	if ($vote_count > 0)
	{
		$dogvote="<span class='text-danger'>Already voted.</span>";
	}
	else
	{
		$dogvote="<a href='?action=dog'>Vote</a>";
	}
    $q = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `votes` WHERE `userid` = $userid AND `voted` = 'bbogd'");
	$vote_count = $db->fetch_single($q);
	$db->free_result($q);
	if ($vote_count > 0)
	{
		$bbogdvote="<span class='text-danger'>Already voted.</span>";
	}
	else
	{
		$bbogdvote="<a href='?action=bbogd'>Vote</a>";
	}
	echo"
	<table class='table table-bordered'>
		<tr>
			<th>
				Voting Website
			</th>
			<th>
				Reward
			</th>
			<th>
				Link
			</th>
		</tr>
		<tr>
			<td>
				Top 100 Arena
			</td>
			<td>
				100 Chivalry Token Voucher
			</td>
			<td>
				{$thavote}
			</td>
		</tr>
		<tr>
			<td>
				MGPoll
			</td>
			<td>
				50 Boxes of Random
			</td>
			<td>
				{$mgpollvote}
			</td>
		</tr>
		<tr>
			<td>
				Apex Web Gaming
			</td>
			<td>
				+25 Hexbags
			</td>
			<td>
				{$awgvote}
			</td>
		</tr>
		<tr>
			<td>
				Directory of Games
			</td>
			<td>
				100,000 Copper Coins
			</td>
			<td>
				{$dogvote}
			</td>
		</tr>
        <tr>
			<td>
				BBOGD
			</td>
			<td>
				75 Chivalry Tokens
			</td>
			<td>
				{$bbogdvote}
			</td>
		</tr>
	</table>";
    $h->endpage();
    /*<tr>
			<td>
				Top Web Games
			</td>
			<td>
				25,000 Copper Coins
			</td>
			<td>
				{$twgvote}
			</td>
		</tr>*/
}
function twg()
{
    global $db,$userid,$api,$h;
    $q = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `votes` WHERE `userid` = $userid AND `voted` = 'twg'");
    $vote_count = $db->fetch_single($q);
    $db->free_result($q);
    if ($vote_count > 0)
    {
        alert('danger',"Uh Oh!","You have already voted at TWG today. If you vote again, you will not be rewarded.",true,"http://www.topwebgames.com/in.aspx?id=11600&uid={$userid}","Vote Again");
        $h->endpage();
    }
    else
    {
        header("Location: http://topwebgames.com/in.aspx?ID=8303&uid={$userid}");
        exit;
    }
}
function top100()
{
    global $db,$userid,$api,$h;
    $q = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `votes` WHERE `userid` = $userid AND `voted` = 'top100'");
    $vote_count = $db->fetch_single($q);
    $db->free_result($q);
    if ($vote_count > 0)
    {
        alert('danger',"Uh Oh!","You have already voted at Top 100 Arena today. If you vote again, you will not be rewarded.",true,"http://www.top100arena.com/in.asp?id=86377&incentive={$userid}","Vote Again");
        $h->endpage();
    }
    else
    {
        header("Location: http://www.top100arena.com/in.asp?id=86377&incentive={$userid}");
        exit;
    }
}

function mgpoll()
{
    global $db,$userid,$api,$h;
    $q = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `votes` WHERE `userid` = $userid AND `voted` = 'mgp'");
    $vote_count = $db->fetch_single($q);
    $db->free_result($q);
    if ($vote_count > 0)
    {
        alert('danger',"Uh Oh!","You have already voted at MGPoll today. If you vote again, you will not be rewarded.",true,"http://mgpoll.com/vote/260/{$userid}","Vote Again");
        $h->endpage();
    }
    else
    {
        header("Location: http://mgpoll.com/vote/260/{$userid}");
        exit;
    }
}
function apex()
{
    global $db,$userid,$api,$h;
    $q = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `votes` WHERE `userid` = $userid AND `voted` = 'apex'");
    $vote_count = $db->fetch_single($q);
    $db->free_result($q);
    if ($vote_count > 0)
    {
        alert('danger',"Uh Oh!","You have already voted at Apex Web Gaming today. If you vote again, you will not be rewarded.",true,"http://apexwebgaming.com/index.php?a=in&u=TheMasterGeneral&i_id={$userid}","Vote Again");
        $h->endpage();
    }
    else
    {
        header("Location: http://apexwebgaming.com/index.php?a=in&u=TheMasterGeneral&i_id={$userid}");
        exit;
    }
}
function dog()
{
    global $db,$userid,$api,$h;
    $q = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `votes` WHERE `userid` = $userid AND `voted` = 'dog'");
    $vote_count = $db->fetch_single($q);
    $db->free_result($q);
    if ($vote_count > 0)
    {
        alert('danger',"Uh Oh!","You have already voted at Directory of Games today. If you vote again, you will not be rewarded.",true,"http://www.directoryofgames.com/main.php?view=topgames&action=vote&v_tgame=2317&votedef={$userid}","Vote Again");
        $h->endpage();
    }
    else
    {
        header("Location: http://www.directoryofgames.com/main.php?view=topgames&action=vote&v_tgame=2317&votedef={$userid}");
        exit;
    }
}
function bbogd()
{
    global $db,$userid,$api,$h;
    $q = $db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `votes` WHERE `userid` = $userid AND `voted` = 'bbogd'");
    $vote_count = $db->fetch_single($q);
    $db->free_result($q);
    if ($vote_count > 0)
    {
        alert('danger',"Uh Oh!","You have already voted at BBOGD today. If you vote again, you will not be rewarded.",true,"https://bbogd.com/vote/chivalry-is-dead/{$userid}","Vote Again");
        $h->endpage();
    }
    else
    {
        header("Location: https://bbogd.com/vote/chivalry-is-dead/{$userid}");
        exit;
    }
}