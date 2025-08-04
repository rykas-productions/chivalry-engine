<?php
/*
	File:		fedjail.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Shows the players removed from the game. Players in 
				the federal dungeon cannot interact with the game in 
				any way, shape or form until their sentence is complete.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
	MIT License

	Copyright (c) 2019 TheMasterGeneral

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
*/
require('globals.php');
echo "<h3>Federal Dungeon</h3>
	This is where you go if you break the game rules. Be smart, follow the rules!";
$q = $db->query("SELECT * FROM `fedjail` ORDER BY `fed_out` ASC");
echo "<div class='container'>
<div class='row'>
		<div class='col-sm'>
		    <h4>User</h4>
		</div>
		<div class='col-sm'>
		    <h4>Reason</h4>
		</div>
		<div class='col-sm'>
		    <h4>Sentence</h4>
		</div>
		<div class='col-sm'>
		    <h4>Jailer</h4>
		</div>
</div><hr />";
//List all the players in the federal jail.
while ($r = $db->fetch_row($q)) {
    echo "
	<div class='row'>
    	<div class='col-sm'>
    		<a href='profile.php?user={$r['fed_userid']}'>{$api->user->getNamefromID($r['fed_userid'])}</a>
    	</div>
    	<div class='col-sm'>
			" . timeUntilParse($r['fed_out']) . "
		</div>
    	<div class='col-sm'>
			{$r['fed_reason']}
		</div>
    	<div class='col-sm'>
    		<a href='profile.php?user={$r['fed_jailedby']}'>{$api->user->getNamefromID($r['fed_jailedby'])}</a>
    	</div>
    </div><hr />";
}
echo "</div>";
$db->free_result($q);
echo "We have no real good reason to put mail banned players here... but we still did.";
$q = $db->query("SELECT * FROM `mail_bans` ORDER BY `mbTIME` ASC");
echo "<div class='container'>
<div class='row'>
		<div class='col-sm'>
		    <h4>User</h4>
		</div>
		<div class='col-sm'>
		    <h4>Reason</h4>
		</div>
		<div class='col-sm'>
		    <h4>Sentence</h4>
		</div>
		<div class='col-sm'>
		    <h4>Jailer</h4>
		</div>
</div><hr />";
//List all the players who are mail banned
while ($r = $db->fetch_row($q)) {
    echo "
	<div class='row'>
    	<div class='col-sm'>
    		<a href='profile.php?user={$r['mbUSER']}'>{$api->user->getNamefromID($r['mbUSER'])}</a>
    	</div>
    	<div class='col-sm'>
			" . timeUntilParse($r['mbTIME']) . "
		</div>
    	<div class='col-sm'>
			{$r['mbREASON']}
		</div>
    	<div class='col-sm'>
    		<a href='profile.php?user={$r['mbBANNER']}'>{$api->user->getNamefromID($r['mbBANNER'])}</a>
    	</div>
    </div><hr />";
}
echo "</div>";
$db->free_result($q);

echo "The same holds true for forum bans.";
$q = $db->query("SELECT * FROM `forum_bans` ORDER BY `fb_time` ASC");
echo "<div class='container'>
<div class='row'>
		<div class='col-sm'>
		    <h4>User</h4>
		</div>
		<div class='col-sm'>
		    <h4>Reason</h4>
		</div>
		<div class='col-sm'>
		    <h4>Sentence</h4>
		</div>
		<div class='col-sm'>
		    <h4>Jailer</h4>
		</div>
</div><hr />";
//List all the players who are mail banned
while ($r = $db->fetch_row($q)) {
    echo "
	<div class='row'>
    	<div class='col-sm'>
    		<a href='profile.php?user={$r['fb_user']}'>{$api->user->getNamefromID($r['fb_user'])}</a>
    	</div>
    	<div class='col-sm'>
			" . timeUntilParse($r['fb_time']) . "
		</div>
    	<div class='col-sm'>
			{$r['fb_reason']}
		</div>
    	<div class='col-sm'>
    		<a href='profile.php?user={$r['fb_banner']}'>{$api->user->getNamefromID($r['fb_banner'])}</a>
    	</div>
    </div><hr />";
}
echo "</div>";
$db->free_result($q);

$h->endpage();