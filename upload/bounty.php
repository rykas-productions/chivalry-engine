<?php
require('globals.php');
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
	alert('danger',"Uh Oh!","You cannot use Bounty Hunter while in the dungeon or infirmary.",true,'explore.php');
	die($h->endpage());
}
echo "<h3><i class='game-icon game-icon-shadow-grasp'></i> Bounty Hunter</h3>[<a href='#' data-toggle='modal' data-target='#bh_info'>Info</a>] || [<a href='?action=addbounty'>Add Bounty</a>]<hr />";
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'addbounty':
        add_bounty();
        break;
    default:
        home();
        break;
}
function home()
{
    global $db;
    $q=$db->query("/*qc=on*/SELECT * FROM `bounty_hunter` ORDER BY `bh_time` ASC");
    if ($db->num_rows($q) == 0)
    {
        alert('danger','',"There doesn't happen to be any open bounties at this time.",false);
    }
    else
    {
        echo "<table class='table table-bordered table-striped'>
        <thead>
            <tr>
                <th>
                    Bounty
                </th>
                <th>
                    Payout
                </th>
                <th>
                    Poster
                </th>
            </tr>
        </thead>
        <tbody>";
        while ($r=$db->fetch_row($q))
        {
            $ud=$db->fetch_row($db->query("/*qc=on*/SELECT `username`, `vip_days`, `display_pic`, `vipcolor`, `level` FROM `users` WHERE `userid` = {$r['bh_user']}"));
            $ud['display_pic'] = ($ud['display_pic']) ? "<img src='" . parseImage($ud['display_pic']) . "' class='img-thumbnail img-fluid' width='75' alt='{$ud['username']}&#39;s display picture' title='{$ud['username']}&#39;s display picture'>" : '';
            $ud['username'] = parseUsername($r['bh_user']);
            if ($r['bh_creator'] != 0)
            {
                $cd=$db->fetch_row($db->query("/*qc=on*/SELECT `username`, `vip_days`, `display_pic`, `vipcolor`, `level` FROM `users` WHERE `userid` = {$r['bh_creator']}"));
                $cd['display_pic'] = ($cd['display_pic']) ? "<img src='" . parseImage($cd['display_pic']) . "' class='img-thumbnail img-fluid' width='75' alt='{$cd['username']}&#39;s display picture' title='{$cd['username']}&#39;s display picture'>" : '';
                $cd['username'] = parseUsername($r['bh_creator']);
            }
            else
            {
                $cd['username']='[Redacted]';
                $cd['display_pic']='';
                $cd['vip_color']='text-danger';
                $cd['level']='Unknown';
            }
            echo "<tr>
            <td align='left'>
                {$ud['display_pic']}<br />
                <a href='profile.php?user={$r['bh_user']}'>{$ud['username']}</a><br />
                Level: {$ud['level']}
            </td>
            <td>
                " . number_format($r['bh_bounty']) . " Copper Coins
            </td>
            <td align='left'>
                {$cd['display_pic']}<br />
                <a href='profile.php?user={$r['bh_creator']}'>{$cd['username']}</a><br />
                Level: {$cd['level']}
            </td>
            </tr>";
        }
        echo"</tbody></table>";
    }
}
function add_bounty()
{
    global $db,$userid,$api,$h,$ir;
    if (isset($_POST['user']))
    {
        $_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs($_POST['user']) : '';
        $_POST['payout'] = (isset($_POST['payout']) && is_numeric($_POST['payout'])) ? abs($_POST['payout']) : '';
        $_POST['hide'] = (isset($_POST['hide']) && is_numeric($_POST['hide'])) ? abs($_POST['hide']) : '';
        if ((empty($_POST['user'])) || (empty($_POST['payout'])))
        {
            alert('danger',"Uh Oh!","You are missing one or more inputs on the previous form.",true);
            die($h->endpage());
        }
        //Verify CSRF check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("bounty_add", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }
        if ($_POST['payout'] < 250000)
        {
            alert('danger',"Uh Oh!","Bounty postings must have a payout of, at least, 250,000 Copper Coins.",true);
            die($h->endpage());
        }
        if ($_POST['payout'] > 1500000000)
        {
            alert('danger',"Uh Oh!","Bounty postings must have a payout of, at most, 1.5B Copper Coins.",true);
            die($h->endpage());
        }
        $cost=$_POST['payout']+($_POST['hide']*500000);
        $costnumber=number_format($cost);
        $prim_format=number_format($ir['primary_currency']);
        if ($ir['primary_currency'] < $cost)
        {
            alert('danger',"Uh Oh!","You do not have enough Copper Coins to create this bounty. You need " . shortNumberParse($costnumber) . ", but only have " . shortNumberParse($prim_format) . ".",true);
            die($h->endpage());
        }
        if ($_POST['user'] == $userid)
        {
            alert('danger',"Uh Oh!","You may not open a bounty on yourself!",true);
            die($h->endpage());
        }
        $q=$db->query("/*qc=on*/SELECT * FROM `bounty_hunter` WHERE `bh_user` = {$_POST['user']}");
        if ($db->num_rows($q) != 0)
        {
            alert('danger',"Uh Oh!","Your target already has a listing opened on them. Targets may only have one listing opened on them at a time.",true);
            die($h->endpage());
        }
        $ue=$db->query("/*qc=on*/SELECT `userid` FROM `users` WHERE `userid` = {$_POST['user']}");
        if ($db->num_rows($ue) == 0)
        {
            alert('danger',"Uh Oh!","You target does not exist.",true);
            die($h->endpage());
        }
        $db->query("UPDATE `users` SET `primary_currency` = `primary_currency` - {$cost} WHERE `userid` = {$userid}");
        if ($_POST['hide'] == 0)
            $id=$userid;
        else
            $id=0;
        $expired=time()+259200;
        $db->query("INSERT INTO `bounty_hunter` 
                    (`bh_creator`, `bh_user`, `bh_time`, `bh_bounty`) 
                    VALUES ('{$id}', '{$_POST['user']}', '{$expired}', '{$_POST['payout']}')");
        alert('success',"Success!","Bounty has been opened successfully. {$costnumber} Copper Coins have been taken from your account.",true,'bounty.php');
        die($h->endpage());
    }
    else
    {
		$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : $userid;
        $csrf=request_csrf_html('bounty_add');
        echo "You may open a bounty on another player here. Bounties must be worth at least 250,000 Copper Coins. A 
        player may only have one bounty opened on their-self at a time. Bounties will be removed after 3 days 
        if the hit is not carried out. You will not receive a refund if this is the case.<br />
        <form method='post'>
            <table class='table table-bordered'>
                <tr>
                    <th>
                        Target
                    </th>
                    <td>
                        " . user_dropdown('user', $_GET['user']) . "
                    </td>
                </tr>
                <tr>
                    <th>
                        Payout
                    </th>
                    <td>
                        <input type='number' min='250000' max='1500000000' name='payout' class='form-control' value='{$ir['primary_currency']}' required='1'>
                    </td>
                </tr>
                <tr>
                    <th>
                        Remove identification?<br />
                        <small>Add 500K Copper Coins</small>
                    </th>
                    <td align='left'>
                        <input type='radio' name='hide' value='0' checked> Don't Remove<br />
                        <input type='radio' name='hide' value='1'> Remove<br />
                    </td>
                </tr>
                <tr>
                    <td colspan='2'>
                        <input type='submit' class='btn btn-success' value='Submit Bounty'>
                    </td>
                </tr>
            </table>
            {$csrf}
        </form>";
    }
}
include('forms/bounty_popup.php');
$h->endpage();