<?php
/*
	File:		usersonline.php
	Created: 	4/5/2016 at 12:31AM Eastern Time
	Info: 		Lists players on within the time period set. The GET
				can be set to any integer value, and it'll check that
				number minutes ago.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');

//Different options for different time periods. The GET is in minutes.
echo "<h3><i class='fas fa-toggle-on'></i> Users Online</h3><hr />
<div class='row'>
	<div class='col-auto'>
		<a href='?act=5' class='btn btn-primary btn-block'>5 minutes</a>
        <br />
	</div>
	<div class='col-auto'>
		<a href='?act=15' class='btn btn-primary btn-block'>15 minutes</a>
        <br />
	</div>
	<div class='col-auto'>
		<a href='?act=60' class='btn btn-primary btn-block'>1 hour</a>
        <br />
	</div>
    <div class='col-auto'>
		<a href='?act=360' class='btn btn-primary btn-block'>6 hours</a>
        <br />
	</div>
    <div class='col-auto'>
		<a href='?act=720' class='btn btn-primary btn-block'>12 hours</a>
        <br />
	</div>
	<div class='col-auto'>
		<a href='?act=1440' class='btn btn-primary btn-block'>24 hours</a>
        <br />
	</div>
	<div class='col-auto'>
		<a href='?act=10080' class='btn btn-primary btn-block'>1 Week</a>
        <br />
	</div>
	<div class='col-auto'>
		<a href='?act=43200' class='btn btn-primary btn-block'>1 Month</a>
        <br />
	</div>
</div>
<hr />";

//Time period isn't set, so set it to 15.
if (!isset($_GET['act'])) {
    $_GET['act'] = 15;
}
$_GET['act'] = (isset($_GET['act']) && is_numeric($_GET['act'])) ? abs($_GET['act']) : 15;
$last_on = time() - ($_GET['act'] * 60);

//Select all players on in the time period set in the GET.
$q = $db->query("/*qc=on*/SELECT * FROM `users` WHERE `laston` > {$last_on} ORDER BY `laston` DESC");
echo "
<div class='card'>
    <div class='card-header'>
        Showing users online in the last " . shortNumberParse($_GET['act']) . " minutes.
    </div>
    <div class='card-body'>";
//Display the users info.
while ($r = $db->fetch_row($q))
{
    $r['username'] = parseUsername($r['userid']);
    $un = $api->SystemUserIDtoName($r['userid']);
    $displaypic = "<img src='" . parseDisplayPic($r['userid']) . "' height='75' class='hidden-sm-down' alt='{$un}&#39;s Display picture.' title='{$un}&#39;s Display picture'>";
    $active = parseActivity($r['userid']);
    echo "
            <div class='row'>
                <div class='col-auto col-md-5 col-xl-5 col-xxl-4'>
                    <div class='row'>
                        <div class='col-12 col-md-auto col-lg-12 col-xl'>
				            {$displaypic}
                        </div>
                        <div class='col-12 col-md-auto col-lg-12 col-xl'>
				            <a href='profile.php?user={$r['userid']}'>{$r['username']}</a> " . parseUserID($r['userid']) . "
                        </div>
                    </div>
                </div>
                <div class='col-auto col-md-2 col-xl'>
					<div class='row'>
                        <div class='col-12'>
				            <small><b>Level</b></small>
                        </div>
                        <div class='col-12'>
				            " . shortNumberParse($r['level']) . "
                        </div>
                    </div>
				</div>
                <div class='col-auto col-md-3 col-xl'>
					<div class='row'>
                        <div class='col-12'>
				            <small><b>Copper Coins</b></small>
                        </div>
                        <div class='col-12'>
				            " . shortNumberParse($r['primary_currency']) . "
                        </div>
                    </div>
				</div>
                <div class='col-auto col-md-2 col-xl'>
					<div class='row'>
                        <div class='col-12'>
				            <small><b>Activity</b></small>
                        </div>
                        <div class='col-12'>
				            {$active}
                        </div>
                    </div>
				</div>
            </div>
            <hr />";
}
echo "</div>
	</div>";
$h->endpage();