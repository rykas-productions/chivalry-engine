<?php
/*
	File:		fedjail.php
	Created: 	4/5/2016 at 12:01AM Eastern Time
	Info: 		Lists those placed into the federal jail. Players in
				federal jail cannot interact with the game at all.
				Consider it like an in-game ban.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
alert('danger','',"Players in the federal dungeon have broken the rules and have been removed from the game in hopes they fix their ways.", false);
$q = $db->query("/*qc=on*/SELECT * FROM `fedjail` ORDER BY `fed_out` ASC");
echo "
<div class='card'>
    <div class='card-header'>
        {$set['WebsiteName']} Federal Jail
    </div>
    <div class='card-body'>";
//List all the players in the federal jail.
while ($r = $db->fetch_row($q)) {
    echo "
    <div class='row'>
        <div class='col-auto col-sm-6 col-md-3'>
            <div class='row'>
                <div class='col-12'>
                    <small><b>Player</b></small>
                </div>
                <div class='col-12'>
                    <a href='profile.php?user={$r['fed_userid']}'>" . parseUsername($r['fed_userid']) . " " . parseUserID($r['fed_userid']) . "</a>
                </div>
            </div>
        </div>
        <div class='col-auto col-sm-6 col-md-3'>
            <div class='row'>
                <div class='col-12'>
                    <small><b>Reason</b></small>
                </div>
                <div class='col-12'>
                    {$r['fed_reason']}
                </div>
            </div>
        </div>
        <div class='col-auto col-sm-6 col-md-3'>
            <div class='row'>
                <div class='col-12'>
                    <small><b>Sentence</b></small>
                </div>
                <div class='col-12'>
                    " . TimeUntil_Parse($r['fed_out']) . "
                </div>
            </div>
        </div>
        <div class='col-auto col-sm-6 col-md-3'>
            <div class='row'>
                <div class='col-12'>
                    <small><b>Jailer</b></small>
                </div>
                <div class='col-12'>
                    <a href='profile.php?user={$r['fed_jailedby']}'>" . parseUsername($r['fed_jailedby']) . " " . parseUserID($r['fed_jailedby']) . "</a>
                </div>
            </div>
        </div>
    </div><hr />";
}
echo "</div></div><br />";
$db->free_result($q);
alert('warning',"","If you spam or abuse the mail or comment system, you may lose your privlegdes.",false);
$q = $db->query("/*qc=on*/SELECT * FROM `mail_bans` ORDER BY `mbTIME` ASC");
echo "
<div class='card'>
    <div class='card-header'>
        {$set['WebsiteName']} Mail Bans
    </div>
    <div class='card-body'>";
//List all the players who are mail banned
while ($r = $db->fetch_row($q)) {
	echo "
    <div class='row'>
        <div class='col-auto col-sm-6 col-md-3'>
            <div class='row'>
                <div class='col-12'>
                    <small><b>Player</b></small>
                </div>
                <div class='col-12'>
                    <a href='profile.php?user={$r['mbUSER']}'>" . parseUsername($r['mbUSER']) . " " . parseUserID($r['mbUSER']) . "</a>
                </div>
            </div>
        </div>
        <div class='col-auto col-sm-6 col-md-3'>
            <div class='row'>
                <div class='col-12'>
                    <small><b>Reason</b></small>
                </div>
                <div class='col-12'>
                    {$r['mbREASON']}
                </div>
            </div>
        </div>
        <div class='col-auto col-sm-6 col-md-3'>
            <div class='row'>
                <div class='col-12'>
                    <small><b>Ban Time</b></small>
                </div>
                <div class='col-12'>
                    " . TimeUntil_Parse($r['mbTIME']) . "
                </div>
            </div>
        </div>
        <div class='col-auto col-sm-6 col-md-3'>
            <div class='row'>
                <div class='col-12'>
                    <small><b>Jailer</b></small>
                </div>
                <div class='col-12'>
                    <a href='profile.php?user={$r['mbBANNER']}'>" . parseUsername($r['mbBANNER']) . " " . parseUserID($r['mbBANNER']) . "</a>
                </div>
            </div>
        </div>
    </div><hr />";
}
echo "</div></div><br />";
$db->free_result($q);

alert('warning',"","We want the forums to be civlized... if you can't help yourself, we can remove your prilvegdes to the forums.",false);
$q = $db->query("/*qc=on*/SELECT * FROM `forum_bans` ORDER BY `fb_time` ASC");
echo "
<div class='card'>
    <div class='card-header'>
        {$set['WebsiteName']} Forum Bans
    </div>
    <div class='card-body'>";
//List all the players who are mail banned
while ($r = $db->fetch_row($q)) {
	echo "
    <div class='row'>
        <div class='col-auto col-sm-6 col-md-3'>
            <div class='row'>
                <div class='col-12'>
                    <small><b>Player</b></small>
                </div>
                <div class='col-12'>
                    <a href='profile.php?user={$r['fb_user']}'>" . parseUsername($r['fb_user']) . " " . parseUserID($r['fb_user']) . "</a>
                </div>
            </div>
        </div>
        <div class='col-auto col-sm-6 col-md-3'>
            <div class='row'>
                <div class='col-12'>
                    <small><b>Reason</b></small>
                </div>
                <div class='col-12'>
                    {$r['fb_reason']}
                </div>
            </div>
        </div>
        <div class='col-auto col-sm-6 col-md-3'>
            <div class='row'>
                <div class='col-12'>
                    <small><b>Ban Time</b></small>
                </div>
                <div class='col-12'>
                    " . TimeUntil_Parse($r['fb_time']) . "
                </div>
            </div>
        </div>
        <div class='col-auto col-sm-6 col-md-3'>
            <div class='row'>
                <div class='col-12'>
                    <small><b>Jailer</b></small>
                </div>
                <div class='col-12'>
                    <a href='profile.php?user={$r['fb_banner']}'>" . parseUsername($r['fb_banner']) . " " . parseUserID($r['fb_banner']) . "</a>
                </div>
            </div>
        </div>
    </div><hr />";
}
echo "</table>";
$db->free_result($q);

$h->endpage();