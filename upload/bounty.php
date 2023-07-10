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
        echo "<div class='card'>
                <div class='card-header'>
                    Active Bounty Listings
                </div>
                <div class='card-body'>";
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
                $cd['level']=0;
            }
            echo "<div class='row'>
                    <div class='col-12 col-lg-4 col-xxl'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Target</b></small>
                            </div>
                            <div class='col-12'>
                                <div class='row'>
                                    <div class='col-12 col-sm col-lg-12 col-xxl'>
                                        {$ud['display_pic']}
                                    </div>
                                    <div class='col-12 col-sm col-lg-12 col-xxl'>
                                        <a href='profile.php?user={$r['bh_user']}'>{$ud['username']}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-lg-4 col-xxl'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Payout</b></small>
                            </div>
                            <div class='col-12'>
                                " . shortNumberParse($r['bh_bounty']) . " Copper Coins
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-lg-4 col-xxl'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Poster</b></small>
                            </div>
                            <div class='col-12'>
                                <div class='row'>";
                                    if (!empty($cd['display_pic']))
                                    {   echo"
                                        <div class='col-12 col-sm col-lg-12 col-xxl'>
                                            {$cd['display_pic']}
                                        </div>";
                                    }
                                    echo"
                                    <div class='col-12 col-sm col-lg-12 col-xxl'>
                                        <a href='profile.php?user={$r['bh_creator']}'>{$cd['username']}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br />";
        }
        echo "</div></div>";
    }
}
function add_bounty()
{
    global $db,$userid,$api,$h,$ir;
    $minCost = 250000 + (250000 * levelMultiplier($ir['level'], $ir['reset']));
    $maxCost = 1500000000 + (1500000000 * levelMultiplier($ir['level'], $ir['reset']));
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
        if ($_POST['payout'] < $minCost)
        {
            alert('danger',"Uh Oh!","At your level and experience in the game, your bounties must have a minimum payout of at least " . shortNumberParse($minCost) . " Copper Coins.",true);
            die($h->endpage());
        }
        if ($_POST['payout'] > $maxCost)
        {
            alert('danger',"Uh Oh!","At your level and experience in the game, you may only post bounties that have a maximum payout of " . shortNumberParse($maxCost) . " Copper Coins.",true);
            die($h->endpage());
        }
        $cost = $_POST['payout'] + ($_POST['hide'] * calcHideCost());
        $costnumber=shortNumberParse($cost);
        $prim_format=shortNumberParse($ir['primary_currency']);
        if ($ir['primary_currency'] < $cost)
        {
            alert('danger',"Uh Oh!","You do not have enough Copper Coins to create this bounty. You need {$costnumber} Copper Coins, but only have {$prim_format} Copper Coins.",true);
            die($h->endpage());
        }
        if (($_POST['user'] == $userid) && ($_POST['hide'] == 0))
        {
            alert('danger',"Uh Oh!","If you're going to post a bounty on yourself, why don't you at least hide your desperation by anonymizing your hit?",true);
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
        $api->UserTakeCurrency($userid, "primary", $cost);
        if ($_POST['hide'] == 0)
            $id=$userid;
        else
            $id=0;
        $expired=time()+259200;
        $db->query("INSERT INTO `bounty_hunter` 
                    (`bh_creator`, `bh_user`, `bh_time`, `bh_bounty`) 
                    VALUES ('{$id}', '{$_POST['user']}', '{$expired}', '{$_POST['payout']}')");
        alert('success',"Success!","You have successfully posted a {$costnumber} Bounty offer on {$api->SystemUserIDtoName($_POST['user'])}.",true,'bounty.php');
        die($h->endpage());
    }
    else
    {
		$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : $userid;
        $csrf=request_csrf_html('bounty_add');
        echo "
        <form method='post'>
        <div class='card'>
            <div class='card-header'>
                Create Bounty
            </div>
            <div class='card-body'>
                <div class='row'><div class='col-12'>";
                    alert('info',"","You may post a bounty on another player here. At your experience, your minimum bounty posting is " . shortNumberParse($minCost) . " Copper Coins
                         and your maximum is " . shortNumberParse($maxCost) . " Copper Coins.
                        You may hide your information on thie hit for an additional " . shortNumberParse(calcHideCost()) . " Copper Coins. <b>Hits expire and are removed 
                        after 3 days of posting. If this happens to yours, you will not be refunded.</b>",false);
                echo "</div></div>
                <div class='row'>
                    <div class='col-12 col-lg'>
                        <div class='row'>
                            <div class='col-12 col-lg-4'>
                                <b><small>Target</small></b>
                            </div>
                            <div class='col-12'>
                                " . user_dropdown('user', $_GET['user']) . "
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-lg'>
                        <div class='row'>
                            <div class='col-12'>
                                <b><small>Payout</small></b>
                            </div>
                            <div class='col-12'>
                                <input type='number' min='{$minCost}' max='{$maxCost}' name='payout' class='form-control' value='{$minCost}' required='1'>
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-lg'>
                        <div class='row'>
                            <div class='col-12'>
                                <b><small>Anonymous Hit?</small></b>
                            </div>
                            <div class='col-12'>
                                <select name='hide' id='class' class='form-control' type='dropdown'>
                					<option value='0'>Non-Anonymous</option>
                					<option value='1'>Anonymous</option>
                				</select>
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-xxl'>
                        <div class='row'>
                            <div class='col-12'>
                                <b><small>&nbsp;</small></b>
                            </div>
                            <div class='col-12'>
                                <input type='submit' class='btn btn-success btn-block' value='Submit Bounty'>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {$csrf}
        </form>";
    }
}

function calcHideCost()
{
    global $ir;
    $mainCost = 500000;
    $mainCost += ($mainCost * levelMultiplier($ir['level'], $ir['reset']));
    return $mainCost;
}
include('forms/bounty_popup.php');
$h->endpage();