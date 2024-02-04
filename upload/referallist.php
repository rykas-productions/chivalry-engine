<?php
require('globals.php');
echo "<h3>Referral List</h3><hr />
This page lists all the players you have referred to the game. This is so you can find them easily at a later date.
You can find their name, level, when you referred them and the time they were last active.
<hr />";
$q=$db->query("/*qc=on*/SELECT * FROM `referals` WHERE `referal_userid` = {$userid}");
if ($db->num_rows($q) == 0)
{
	alert('danger',"Uh Oh!","You have not referred anyone to the game yet.",true,'explore.php');
	die($h->endpage());
}
echo "<div class='card'>
        <div class='card-header'>
            Your Referrals " . createPrimaryBadge($db->num_rows($q)) . "
        </div>
        <div class='card-body'>";
while ($r=$db->fetch_row($q))
{
	$lvl=shortNumberParse($api->UserInfoGet($r['refered_id'],'level'));
	$lastactive=$api->UserInfoGet($r['refered_id'],'laston');
	echo "
    <div class='row'>
        <div class='col-12 col-sm-6 col-md-3'>
            <div class='row'>
                <div class='col-12'>
                    <small><b>Referral</b></small>
                </div>
                <div class='col-12'>
                    <a href='profile.php?user={$r['refered_id']}'>" . parseUsername($r['refered_id']) . "</a> " . parseUserID($r['refered_id']) . "
                </div>
            </div>
        </div>
        <div class='col-12 col-sm-6 col-md-2'>
            <div class='row'>
                <div class='col-12'>
                    <small><b>Level</b></small>
                </div>
                <div class='col-12'>
                    {$lvl}
                </div>
            </div>
        </div>
        <div class='col-12 col-sm-6 col-md'>
            <div class='row'>
                <div class='col-12'>
                    <small><b>Registration Time</b></small>
                </div>
                <div class='col-12'>
                    " . DateTime_Parse($r['time']) . "
                </div>
            </div>
        </div>
        <div class='col-12 col-sm-6 col-md-2'>
            <div class='row'>
                <div class='col-12'>
                    <small><b>Active?</b></small>
                </div>
                <div class='col-12'>
                    " . parseActivity($r['refered_id']) . "
                </div>
            </div>
        </div>
    </div>";
}
echo "</div></div>";

$h->endpage();