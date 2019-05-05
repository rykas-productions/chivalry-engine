<?php
/*
	File:		forums.php
	Created: 	4/5/2016 at 12:03AM Eastern Time
	Info: 		In-game forums. Players can view and create topics,
				reply to other users, and create discussion. BBCode
				is useable!
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
require('lib/bbcode_engine.php');
function csrf_error()
{
    global $h;
    alert('danger', "Action Blocked!", "The action you were trying to do was blocked. It was blocked because you loaded
        another page on the game. If you have not loaded a different page during this time, change your password
        immediately, as another person may have access to your account!");
    die($h->endpage());
}

echo "<h3>{$set['WebsiteName']} Forums</h3><hr />";
$fb = $db->fetch_row($db->query("SELECT * FROM `forum_bans` WHERE `fb_user` = {$userid}"));
if ($fb['fb_time'] > $time) {
    alert('danger', "Uh Oh!", "You are currently forum banned for the next " . timeUntilParse($fb['fb_time']) . ". You
	    were banned for {$fb['fb_reason']}. If you feel the ban was unjustified, please contact an admin
	    immediately.", true, 'index.php');
    die($h->endpage());
}
if (!isset($_GET['act'])) {
    $_GET['act'] = '';
}
if (isset($_GET['viewtopic']) && $_GET['act'] != 'quote') {
    $_GET['act'] = 'viewtopic';
}
if (isset($_GET['viewforum'])) {
    $_GET['act'] = 'viewforum';
}
if (isset($_GET['reply'])) {
    $_GET['act'] = 'reply';
}
if (isset($_GET['empty']) && $_GET['empty'] == 1 && isset($_GET['code'])
    && $_GET['code'] === 'kill' && isset($_SESSION['owner'])
    && $_SESSION['owner'] > 0
) {
    emptyallforums();
}
switch ($_GET['act']) {
    case 'viewforum':
        viewforum();
        break;
    case 'viewtopic':
        viewtopic();
        break;
    case 'reply':
        reply();
        break;
    case 'newtopicform':
        newtopicform();
        break;
    case 'newtopic':
        newtopic();
        break;
    case 'quote':
        quote();
        break;
    case 'edit':
        edit();
        break;
    case 'move':
        move();
        break;
    case 'editsub':
        editsub();
        break;
    case 'lock':
        lock();
        break;
    case 'delepost':
        delepost();
        break;
    case 'deletopic':
        deletopic();
        break;
    case 'pin':
        pin();
        break;
    case 'f0r(3d1337':
        emptyallforums();
        break;
    case 'recache':
        if (isset($_GET['forum'])) {
            recacheForum($_GET['forum']);
        }
        break;
    default:
        idx();
        break;
}
function idx()
{
    global $ir, $db;
    $q =
        $db->query(
            "SELECT `ff_lp_time`, `ff_id`, `ff_name`, `ff_desc`,
					`ff_lp_t_id`,
                     `ff_lp_poster_id`
                     FROM `forum_forums`
                     WHERE `ff_auth` = 'public'
                     ORDER BY `ff_id` ASC");
    ?>
    <div class='container'>
    
    <div class='row'>
        <div class='col-sm'>
            <h4>Category</h4>
        </div>
        <div class='col-sm'>
            <h4>Posts</h4>
        </div>
        <div class='col-sm'>
            <h4>Topics</h4>
        </div>
        <div class='col-sm'>
            <h4>Last Post</h4>
        </div>
    </div>
    <hr />
    
    <?php
    while ($r = $db->fetch_row($q)) {
        $t = dateTimeParse($r['ff_lp_time'], true, true);
        $pnq = $db->query("SELECT `username`,`vip_days` FROM `users` WHERE `userid` = {$r['ff_lp_poster_id']}");
        $pn = $db->fetch_row($pnq);
        $username = ($pn['vip_days']) ? "<span class='text-danger'>{$pn['username']}
            <i class='fas fa-shield-alt' data-toggle='tooltip' title='{$pn['vip_days']} VIP Days remaining.'></i></span>" :
            $pn['username'];

        $topicsq = $db->query("SELECT COUNT('ft_id') FROM `forum_topics` WHERE `ft_forum_id`={$r['ff_id']}");

        $postsq = $db->query("SELECT COUNT('fp_id') FROM `forum_posts` WHERE `ff_id`={$r['ff_id']}");
        $posts = $db->fetch_single($postsq);
        $topics = $db->fetch_single($topicsq);

        $topicname = $db->fetch_single($db->query("SELECT `ft_name` FROM `forum_topics` WHERE `ft_forum_id` = {$r['ff_id']} ORDER BY `ft_last_time` DESC"));
        echo "<div class='row'>
					<div class='col-sm'>
						<a href='?viewforum={$r['ff_id']}'>{$r['ff_name']}</a>
						<small class='hidden-xs-down'><br />{$r['ff_desc']}</small>
					</div>
					<div class='col-sm'>
						{$posts}
					</div>
					<div class='col-sm'>
						{$topics}
					</div>
					<div class='col-sm'>
						{$t}<br />
						In <a href='?viewtopic={$r['ff_lp_t_id']}&lastpost=1'>{$topicname}</a><br />
						By <a href='profile.php?user={$r['ff_lp_poster_id']}'>{$username}</a>
					</div>
              </div><hr />";
    }
    echo "</div>";
    $db->free_result($q);
    if (($ir['user_level'] == 'Admin') || ($ir['user_level'] == 'Forum Moderator') || ($ir['user_level'] == 'Web Developer')) {
        echo "<hr /><h3>Staff Only Forums</h3><hr />";
        $q =
            $db->query(
                "SELECT `ff_lp_time`, `ff_id`, `ff_name`, `ff_desc`,
                     `ff_lp_t_id`,
                     `ff_lp_poster_id`
                     FROM `forum_forums`
                     WHERE `ff_auth` = 'staff'
                     ORDER BY `ff_id` ASC");
        ?>
        <div class='container'>

        <div class='row'>
            <div class='col-sm'>
                <h4>Category</h4>
            </div>
            <div class='col-sm'>
                <h4>Posts</h4>
            </div>
            <div class='col-sm'>
                <h4>Topics</h4>
            </div>
            <div class='col-sm'>
                <h4>Last Post</h4>
            </div>
        </div>
        <hr />
        
        
        <?php
        while ($r = $db->fetch_row($q)) {
            $t = dateTimeParse($r['ff_lp_time'], true, true);
            $pnq = $db->query("SELECT `username`,`vip_days` FROM `users` WHERE `userid` = {$r['ff_lp_poster_id']}");
            $pn = $db->fetch_row($pnq);
            $username = ($pn['vip_days']) ? "<span class='text-danger'>{$pn['username']}
                <i class='fas fa-shield-alt' data-toggle='tooltip' title='{$pn['vip_days']} VIP Days remaining.'></i></span>" :
                $pn['username'];

            $topicsq = $db->query("SELECT COUNT('ft_id') FROM `forum_topics` WHERE `ft_forum_id`={$r['ff_id']}");

            $postsq = $db->query("SELECT COUNT('fp_id') FROM `forum_posts` WHERE `ff_id`={$r['ff_id']}");
            $posts = $db->fetch_single($postsq);
            $topics = $db->fetch_single($topicsq);

            $topicname = $db->fetch_single($db->query("SELECT `ft_name` FROM `forum_topics` WHERE `ft_forum_id` = {$r['ff_id']} ORDER BY `ft_last_time` DESC"));
            echo "<div class='row'>
        		<div class='col-sm'>
        			<a href='?viewforum={$r['ff_id']}' style='font-weight: 800;'>{$r['ff_name']}</a>
        			<small class='hidden-xs-down'><br />{$r['ff_desc']}</small>
        		</div>
        		<div class='col-sm'>
					{$posts}
				</div>
        		<div class='col-sm'>
					{$topics}
				</div>
        		<div class='col-sm'>
					{$t}<br />
					In <a href='?viewtopic={$r['ff_lp_t_id']}&lastpost=1' style='font-weight: 800;'>{$topicname}</a><br />
					By <a href='profile.php?user={$r['ff_lp_poster_id']}'>{$username}</a>
                </div>
              </div>";
        }
        echo "</div>";
        $db->free_result($q);
    }
}

function viewforum()
{
    global $ir, $db, $h, $userid;
	$forum = filter_input(INPUT_GET, 'viewforum', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    if (empty($forum)) {
        alert('danger', "Uh Oh!", "You must enter a forum category you wish to view.", true, "forums.php");
        die($h->endpage());
    }
    $q =
        $db->query(
            "SELECT `ff_auth`, `ff_name`
                     FROM `forum_forums`
                     WHERE `ff_id` = '{$forum}'");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        alert('danger', "Non-existent Forum Category!", "You are attempting to view a non-existent forum category. Check your source and try again.", true, "forums.php");
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if ($r['ff_auth'] == 'staff') {
        if (!in_array($ir['user_level'], array('Admin', 'Forum Moderator', 'Web Developer'))) {
            alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php");
            die($h->endpage());
        }
    }
    $ntl = "[<a href='?act=newtopicform&forum={$forum}'>New Topic</a>]";
    echo "<ol class='breadcrumb'>
		<li class='breadcrumb-item'><a href='forums.php'>Forums Home</a></li>
		<li class='breadcrumb-item active'>{$r['ff_name']} {$ntl}</li>	
	</ol>";
    $posts_topic = $db->fetch_single($db->query("SELECT COUNT(`ft_id`) 
													FROM `forum_topics` 
													WHERE 
													`ft_forum_id` = {$forum}"));
    $st = (isset($_GET['st']) && is_numeric($_GET['st'])) ? abs($_GET['st']) : 0;
    echo pagination(20, $posts_topic, $st, "?viewforum={$forum}&amp;st=");
    ?>
    <div class='container'>
    
    <div class='row'>
        <div class='col-sm'>
            <h4>Topic</h4>
        </div>
        <div class='col-sm'>
            <h4>Post Count</h4>
        </div>
        <div class='col-sm'>
            <h4>Created</h4>
        </div>
        <div class='col-sm'>
            <h4>Latest Post</h4>
        </div>
    </div><hr />
    
    
    <?php
    $q =
        $db->query(
            "SELECT `ft_start_time`, `ft_last_time`, `ft_pinned`,
                     `ft_locked`, `ft_id`, `ft_name`, `ft_desc`, `ft_posts`,
                     `ft_owner_id`, `ft_last_id`
                     FROM `forum_topics`
                     WHERE `ft_forum_id` = {$forum}
                     ORDER BY `ft_pinned` DESC, `ft_last_time` DESC
					 LIMIT {$st}, 20");
    while ($r2 = $db->fetch_row($q)) {
        $t1 = dateTimeParse($r2['ft_start_time'], true, true);
        $t2 = dateTimeParse($r2['ft_last_time'], true, true);
        $pt = ($r2['ft_pinned']) ? " <i class='fa fa-thumb-tack' aria-hidden='true'></i>" : "" ;
        $lt = ($r2['ft_locked']) ? " <i class='fa fa-lock' aria-hidden='true'></i>" : "" ;
        $pnq1 = $db->query("SELECT `username`,`vip_days` FROM `users` WHERE `userid` = {$r2['ft_owner_id']}");
        $pn1 = $db->fetch_row($pnq1);
        $pn1['username'] = ($pn1['vip_days']) ? "<span class='text-danger'>{$pn1['username']}
            <i class='fas fa-shield-alt' data-toggle='tooltip' title='{$pn1['vip_days']} VIP Days remaining.'></i></span>" :
            $pn1['username'];
        $pnq2 = $db->query("SELECT `username`,`vip_days` FROM `users` WHERE `userid` = {$r2['ft_last_id']}");
        $pn2 = $db->fetch_row($pnq2);
        $pn2['username'] = ($pn2['vip_days']) ? "<span class='text-danger'>{$pn2['username']}
            <i class='fas fa-shield-alt' data-toggle='tooltip' title='{$pn2['vip_days']} VIP Days remaining.'></i></span>" :
            $pn2['username'];
        $pcq = $db->query("SELECT COUNT(`fp_id`) FROM `forum_posts` WHERE `fp_topic_id` = {$r2['ft_id']}");
        $pc = $db->fetch_single($pcq);
        if (!$pn2) {
            $pn2['username'] = "Non-existent User";
        }
        if (!$pn1) {
            $pn1['username'] = "Non-existent User";
        }
        echo "<div class='row'>
        		<div class='col-sm'>
					{$pt} <a href='?viewtopic={$r2['ft_id']}&lastpost=1'>{$r2['ft_name']}</a> {$lt}<br />
					<small class='hidden-xs-down'>{$r2['ft_desc']}</small>
				</div>
				<div class='col-sm'>{$pc}</div>
				<div class='col-sm'> 
					{$t1}<br />
					By <a href='profile.php?user={$r2['ft_owner_id']}'>{$pn1['username']}</a>
                </div>
                <div class='col-sm'>
					{$t2}<br />
                    By <a href='profile.php?user={$r2['ft_last_id']}'>{$pn2['username']}</a>
                </div>
              </div><hr />";
    }
    echo "</div>";
    echo pagination(20, $posts_topic, $st, "?viewforum={$forum}&amp;st=");
    $db->free_result($q);
}

function viewtopic()
{
    global $ir, $userid, $parser, $db, $h, $api;
    $code = getCodeCSRF('forum_reply');
    $precache = array();
	$viewtopic = filter_input(INPUT_GET, 'viewtopic', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    if (empty($viewtopic)) {
        alert('danger', "Uh Oh!", "You must enter a topic you wish to view.", true, "forums.php");
        die($h->endpage());
    }
    $q =
        $db->query(
            "SELECT `ft_forum_id`, `ft_name`, `ft_posts`, `ft_id`,
                    `ft_locked`, `ft_pinned`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$viewtopic}");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        alert('danger', "Forum topic does not exist!", "You are attempting to interact with a topic that does not exist. Check your source and try again.", true, "forums.php");
        die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $q2 =
        $db->query(
            "SELECT `ff_auth`, `ff_id`, `ff_name`
                    FROM `forum_forums`
                    WHERE `ff_id` = {$topic['ft_forum_id']}");
    if ($db->num_rows($q2) == 0) {
        $db->free_result($q2);
        alert('danger', "Non-existent Forum Category", "You are attempting to view a non-existent forum category. Check your source and try again.", true, "forums.php?viewforum={$topic['ft_forum_id']}");
        die($h->endpage());
    }
    $forum = $db->fetch_row($q2);
    $db->free_result($q2);
    if ($forum['ff_auth'] == 'staff') {
        if ($api->user->getStaffLevel($userid, 'forum moderator') == false) {
            alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php");
            die($h->endpage());
        }
    }
    echo "<ol class='breadcrumb'>
		<li class='breadcrumb-item'><a href='forums.php'>Forums Home</a></li>
		<li class='breadcrumb-item'><a href='?viewforum={$forum['ff_id']}'>{$forum['ff_name']}</a></li>
		<li class='breadcrumb-item active'>{$topic['ft_name']}</li>	
	</ol>";
    $posts_topic = $topic['ft_posts'];
    $st = (isset($_GET['st']) && is_numeric($_GET['st'])) ? abs($_GET['st']) : 0;
    echo pagination(20, $posts_topic, $st, "?viewtopic={$topic['ft_id']}&st=");
    if (!($ir['user_level'] == 'Member'))
    {
        $lock = ($topic['ft_locked'] == 0) ? 'Lock Topic' : 'Unlock Topic' ;
        $pin = ($topic['ft_pinned'] == 0) ? 'Pin Topic' : 'Unpin Topic' ;
        echo "
	<form action='?act=move&topic={$viewtopic}' method='post'>
    <b>Move Topic To</b> " . dropdownForum('forum') . "
	<input type='submit' value='Move Topic' class='btn btn-primary' />
	</form>
	<br />
		<div class='row'>
			<div class='col-sm'>
				<form>
					<input type='hidden' value='pin' name='act'>
					<input type='hidden' name='topic' value='{$viewtopic}'>
					<input type='submit' class='btn btn-primary' value='{$pin}'>
				</form>
			</div>
			<div class='col-sm'>
				<form>
					<input type='hidden' value='lock' name='act'>
					<input type='hidden' name='topic' value='{$viewtopic}'>
					<input type='submit' class='btn btn-primary' value='{$lock}'>
				</form>
			</div>
			<div class='col-sm'>
				<form action='?act=deletopic'>
					<input type='hidden' value='deletopic' name='act'>
					<input type='hidden' name='topic' value='{$viewtopic}'>
					<input type='submit' class='btn btn-primary' value='Delete'>
				</form>
			</div>
		</div>
	</div><br /> ";
    }
    echo "<div class='container'>";
    $q3 =
        $db->query(
            "SELECT `fp_editor_time`, `fp_editor_id`, `fp_edit_count`,
                     `fp_time`, `fp_id`, `fp_poster_id`, `fp_text`
                     FROM `forum_posts`
                     WHERE `fp_topic_id` = {$topic['ft_id']}
                     ORDER BY `fp_time` ASC
                     LIMIT {$st}, 20");
    $no = $st;
    while ($r = $db->fetch_row($q3)) {
        $PNQ = $db->query("SELECT `username`,`vip_days` FROM `users` WHERE `userid`={$r['fp_poster_id']}");
        $PN = $db->fetch_row($PNQ);
        $PN['username'] = ($PN['vip_days']) ? "<span class='text-danger'>{$PN['username']}
            <i class='fas fa-shield-alt' data-toggle='tooltip' title='{$PN['vip_days']} VIP Days remaining.'></i></span>" :
            $PN['username'];

        $qlink = "[<a href='?act=quote&viewtopic={$viewtopic}&quotename={$r['fp_poster_id']}&fpid={$r['fp_id']}'>Quote</a>]";
        if ($api->user->getStaffLevel($userid, 'forum moderator') || $userid == $r['fp_poster_id']) {
            $elink =
                "[<a href='?act=edit&post={$r['fp_id']}&topic={$viewtopic}'>Edit</a>]";
        } else {
            $elink = "";
        }
        $no++;
        if ($no > 1 and ($api->user->getStaffLevel($userid, 'forum moderator'))) {
            $dlink =
                "[<a href='?act=delepost&post={$r['fp_id']}'>Delete</a>]";
        } else {
            $dlink = "";
        }
        if ($api->user->getStaffLevel($userid, 'forum moderator')) {
            $wlink = "[<a href='staff/staff_punish.php?action=forumwarn&user={$r['fp_poster_id']}'>Warn</a>]";
            $blink = "[<a href='staff/staff_punish.php?action=forumban&user={$r['fp_poster_id']}'>Forum Ban</a>]";
        } else {
            $wlink = "";
            $blink = "";
        }
        $t = dateTimeParse($r['fp_time']);
        $editornameq = $db->query("SELECT `username` FROM `users` WHERE `userid` = {$r['fp_editor_id']} LIMIT 1");
        $editorname = $db->fetch_single($editornameq);
        if ($r['fp_edit_count'] > 0) {
            $edittext =
                "\n<br /><small><i>Last edited by <a href='viewuser.php?u={$r['fp_editor_id']}'>{$editorname}</a> at "
                . dateTimeParse($r['fp_editor_time'])
                . ", edited <b>{$r['fp_edit_count']}</b> times total.</i></small>";
        } else {
            $edittext = "";
        }
        if (!isset($precache[$r['fp_poster_id']])) {
            $membq =
                $db->query(
                    "SELECT `userid`,
                            `user_level`,`username`,`display_pic`, `signature`
                             FROM `users`
                             WHERE `userid` = {$r['fp_poster_id']}");
            if ($db->num_rows($membq) == 0) {
                $memb = array('userid' => 0, 'signature' => '');
            } else {
                $memb = $db->fetch_row($membq);
            }
            $db->free_result($membq);
            $precache[$memb['userid']] = $memb;
        } else {
            $memb = $precache[$r['fp_poster_id']];
        }
        if ($memb['userid'] > 0) {
            if ($memb['display_pic'])
                $av = "<img src='{$memb['display_pic']}' class='img-fluid'>";
            else
                $av = "";
            $memb['signature'] = $parser->parse($memb['signature']);
            $memb['signature'] = $parser->getAsHtml($memb['signature']);
        }
        $parser->parse($r['fp_text']);
        $r['fp_text'] = $parser->getAsHtml();
        echo "<div class='row'>
				<div class='col-sm-2'><h4>Post #{$no}</h4></div>
				<div class='col-sm'>
				<h4>Posted {$t} {$qlink} {$elink} {$dlink} {$wlink} {$blink}</h4>
				</div>
			 </div><hr />
			 <div class='row'>
				<div class='col-sm-2'>";
                    if ($memb['userid'] > 0) {
                        $userpostsq = $db->query("SELECT COUNT('fp_id') FROM `forum_posts` WHERE `fp_poster_id`={$r['fp_poster_id']}");
                        $userposts = $db->fetch_single($userpostsq);

                        $usertopicsq = $db->query("SELECT COUNT('ft_id') FROM `forum_topics` WHERE `ft_owner_id`={$r['fp_poster_id']}");
                        $usertopics = $db->fetch_single($usertopicsq);
                        print
                            "{$av}<br /><a href='profile.php?user={$r['fp_poster_id']}'>{$PN['username']}</a>
                                    [{$r['fp_poster_id']}]<br />
                                 <b>Rank:</b> {$memb['user_level']}<br />
                                 <b>Post Count:</b> {$userposts}<br />
                                 <b>Topic Count:</b> {$usertopics}<br />";
                    } else {
                        print "<b>Non-existent User</b>";
                    }
        print
            "</div>
                <div class='hidden-md-down'>
                <hr />
                </div>
			   	 <div class='col-sm'>
                    {$r['fp_text']}
                    {$edittext}<br />
					<hr />
                    {$memb['signature']}
                 </div>
		</div><hr />";
    }
    $db->free_result($q3);
    echo "</div>";
    echo pagination(20, $posts_topic, $st, "?viewtopic={$topic['ft_id']}&st=");
    if ($topic['ft_locked'] == 0) {
        echo "<br />
		<br />
		<div class='container'>
		<form action='?reply={$topic['ft_id']}' method='post'>
			<table class='table'>
				<div class='row'>
					<div class='col-sm'>
						Post Response
						</div>
					<div class='col-sm'>
						<textarea class='form-control' id='post' placeholder='You can use BBCode.' name='fp_text' required></textarea>
					</div>
				</div>
				<div class='row'>
					<div class='col-sm'> 
						<input type='submit' value='Submit Reply' class='btn btn-primary'>
					</div>
				</div>
			</div>
			<input type='hidden' name='verf' value='{$code}' />
		</form></div>
		";
    } else {
        echo "<br />
		<br />
		<i>This topic is locked, and as a result, you cannot reply to it!</i>";
    }
}

function reply()
{
    global $h, $userid, $db, $api;
	$reply = filter_input(INPUT_GET, 'reply', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    if (!isset($_POST['verf']) || !checkCSRF('forum_reply', stripslashes($_POST['verf']))) {
        csrf_error("?viewtopic={$reply}");
    }
    if (empty($reply)) {
        alert('danger', "Uh Oh!", "You need to enter a reponse.", true, "forums.php");
        die($h->endpage());
    }
    $q =
        $db->query(
            "SELECT `ft_forum_id`, `ft_locked`, `ft_name`, `ft_id`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$reply}");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        alert('danger', "Forum topic does not exist!", "You are attempting to interact with a topic that does not exist. Check your source and try again.", true, "forums.php");
        die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $q2 =
        $db->query(
            "SELECT `ff_auth`, `ff_id`
                     FROM `forum_forums`
                     WHERE `ff_id` = {$topic['ft_forum_id']}");
    if ($db->num_rows($q2) == 0) {
        $db->free_result($q2);
        alert('danger', "Non-existent Forum Category", "You are attempting to view a non-existent forum category. Check your source and try again.", true, "forums.php");
        die($h->endpage());
    }
    $forum = $db->fetch_row($q2);
    $db->free_result($q2);
    if ($forum['ff_auth'] == 'staff') {
        if ($api->user->getStaffLevel($userid, 'forum moderator') == false) {
            alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php");
            die($h->endpage());
        }
    }
    if ($topic['ft_locked'] == 0) {
        $_POST['fp_text'] = $db->escape(str_replace("\n", "<br />", strip_tags(stripslashes($_POST['fp_text']))));
        if ((strlen($_POST['fp_text']) > 65535)) {
            alert('danger', "Uh Oh!", "Forum replies can only be, at maximum, 65,535 characters in length.", true, "forums.php?viewtopic={$reply}");
            die($h->endpage());
        }

        $post_time = time();
        $db->query("
            INSERT INTO `forum_posts` 
			(`fp_id`, `fp_poster_id`, `fp_time`, 
			`fp_topic_id`, `fp_editor_id`, 
			`fp_edit_count`, `fp_editor_time`, 
			`fp_text`, `ff_id`) VALUES 
			(NULL, '$userid', '$post_time', '{$reply}', '0', '0', '0', '{$_POST['fp_text']}', '{$forum['ff_id']}');");
        $db->query(
            "UPDATE `forum_topics`
                 SET `ft_last_id` = $userid,
                 `ft_last_time` = {$post_time}, `ft_posts` = `ft_posts` + 1
                 WHERE `ft_id` = {$reply}");
        $db->query(
            "UPDATE `forum_forums`
                 SET `ff_lp_time` = {$post_time},
                 `ff_lp_poster_id` = $userid,
                 `ff_lp_t_id` = {$reply}
                 WHERE `ff_id` = {$forum['ff_id']}");
        alert('success', "Success!", "Your reply has posted successfully.", false);
        echo "<br />";
        $_GET['lastpost'] = 1;
        $_GET['viewtopic'] = $reply;
        viewtopic();
    } else {
        echo "This topic is locked. You cannot reply to it.";
    }
}

function newtopicform()
{
    global $userid, $h, $db, $api;
	$forum = filter_input(INPUT_GET, 'forum', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    if (empty($forum)) {
        alert('danger', "Uh Oh!", "You must specify a forum you wish to create this topic in.", true, "forums.php");
        die($h->endpage());
    }
    $q =
        $db->query(
            "SELECT `ff_auth`, `ff_name`
                     FROM `forum_forums`
                     WHERE `ff_id` = '{$forum}'");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        alert('danger', "Forum topic does not exist!", "You are attempting to interact with a topic that does not exist. Check your source and try again.", true, "forums.php?viewforum={$forum}");
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if ($r['ff_auth'] == 'staff') {
        if ($api->user->getStaffLevel($userid, 'forum moderator') == false) {
            alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php");
            die($h->endpage());
        }
    }
    $code = getCodeCSRF("forums_newtopic_{$forum}");
    echo "<ol class='breadcrumb'>
		<li class='breadcrumb-item'><a href='forums.php'>Forums Home</a></li>
		<li class='breadcrumb-item'><a href='?viewforum={$forum}'>{$r['ff_name']}</a></li>
		<li class='breadcrumb-item active'>New Topic Form</li>
	</ol>";
    echo <<<EOF
<form method='post' action='?act=newtopic&forum={$forum}'>
	<table class='table'>
		<div class='row'>
			<div class='col-sm'>
				<label for='ft_name'>Topic Name</label>
			</div>
			<div class='col-sm'>
				<input type='text' class='form-control' id='ft_name' name='ft_name' required>
			</div>
		</div>
		<div class='row'>
			<div class='col-sm'>
				<label for='ft_desc'>Topic Description</label>
			</div>
			<div class='col-sm'>
				<input type='text' class='form-control' id='ft_desc' name='ft_desc'>
			</div>
		</div>
		<div class='row'>
			<div class='col-sm'>
				<label for='fp_text'>Opening Post</label>
			</div>
			<div class='col-sm'>
			    <textarea class='form-control' placeholder='You can use BBCode!' name='fp_text' id='fp_text' required></textarea>
			</div>
		</div>
		<div class='row'>
			<div class='col-sm'>
				<input type='submit' class='btn btn-lg btn-primary' value='Post Topic' />
			</div>
	</div>
	<input type='hidden' name='verf' value='{$code}' />
</form>
EOF;
}

function newtopic()
{
    global $ir, $userid, $h, $db, $api;
    $_GET['forum'] = (isset($_GET['forum']) && is_numeric($_GET['forum'])) ? abs($_GET['forum']) : '';
    if (!isset($_POST['verf']) || !checkCSRF("forums_newtopic_{$_GET['forum']}", stripslashes($_POST['verf']))) {
        csrf_error("?act=newtopicform&forum={$_GET['forum']}");
    }
    if (empty($_GET['forum'])) {
        alert('danger', "Uh Oh!", "", true, "forum.php");
        die($h->endpage());
    }
    $q =
        $db->query(
            "SELECT `ff_auth`, `ff_id`
                     FROM `forum_forums`
                     WHERE `ff_id` = {$_GET['forum']}");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        alert('danger', "Forum topic does not exist!", "You are attempting to interact with a topic that does not exist. Check your source and try again.", true, "forums.php");
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if ($r['ff_auth'] == 'staff') {
        if ($api->user->getStaffLevel($userid, 'forum moderator') == false) {
            alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php");
            die($h->endpage());
        }
    }
    $u = htmlentities($ir['username'], ENT_QUOTES, 'ISO-8859-1');
    $u = $db->escape($u);
    $_POST['ft_name'] =
        $db->escape(strip_tags(stripslashes($_POST['ft_name'])));
    if ((strlen($_POST['ft_name']) > 255)) {
        alert('danger', "Uh Oh!", "Topic names can only be, at maximum, 255 characters in length.", true, "back");
        die($h->endpage());
    }
    $_POST['ft_desc'] =
        $db->escape(strip_tags(stripslashes($_POST['ft_desc'])));
    if ((strlen($_POST['ft_desc']) > 255)) {
        alert('danger', "Uh Oh!", "Topic descriptions can only be, at maximum, 255 characters in length.", true, "back");
        die($h->endpage());
    }
    $_POST['fp_text'] = $db->escape(str_replace("\n", "<br />", strip_tags(stripslashes($_POST['fp_text']))));
    if ((strlen($_POST['fp_text']) > 65535)) {
        alert('danger', "Uh Oh!", "Open posts can only be, at maximum, 65,535 characters in length.", true, "back");
        die($h->endpage());
    }
    $post_time = time();
    $db->query("INSERT INTO `forum_topics` 
		(`ft_id`, `ft_forum_id`, `ft_name`, `ft_desc`, 
		`ft_posts`, `ft_owner_id`, `ft_last_id`, 
		`ft_start_time`, `ft_last_time`, `ft_pinned`, 
		`ft_locked`) 
		VALUES (NULL, '{$_GET['forum']}', '{$_POST['ft_name']}', '{$_POST['ft_desc']}',
		 '0', '$userid', '0', '$post_time', '0', '0', '0');");

    $i = $db->insert_id();

    $db->query("INSERT INTO `forum_posts` 
		(`fp_id`, `fp_poster_id`, `fp_time`, `fp_topic_id`, 
		`fp_editor_id`, `fp_edit_count`, `fp_editor_time`, 
		`fp_text`, `ff_id`) 
		VALUES (NULL, '{$userid}', '{$post_time}', '{$i}', '0', '0', '0', '{$_POST['fp_text']}', '{$r['ff_id']}');");

    $db->query(
        "UPDATE `forum_topics`
             SET `ft_last_id` = $userid,
             `ft_last_time` = {$post_time}
             WHERE `ft_id` = {$i}");
    $db->query(
        "UPDATE `forum_forums`
             SET `ff_lp_time` = {$post_time},
			 `ff_lp_poster_id` = $userid, `ff_lp_t_id` = {$i}
             WHERE `ff_id` = {$r['ff_id']}");

    alert("success", "Success!", "Your topic has been posted successfully", false);
    $_GET['viewtopic'] = $i;
    viewtopic();
}

function emptyallforums()
{
    global $ir, $c, $userid, $h, $bbc, $db, $api;
    //Haven't secured this yet... anyone could do this lol.
    /*$db->query("UPDATE `forum_forums` SET `ff_lp_time` = 0, `ff_lp_poster_id` = 0, `ff_lp_poster_name` = 'N/A', `ff_lp_t_id` = 0, `ff_lp_t_name` = 'N/A'");
    $db->query('TRUNCATE `forum_topics`');
    $db->query('TRUNCATE `forum_posts`');*/
}

function quote()
{
    global $userid, $h, $db, $api;
    $code = getCodeCSRF('forum_reply');
    $_GET['viewtopic'] = (isset($_GET['viewtopic']) && is_numeric($_GET['viewtopic'])) ? abs($_GET['viewtopic']) : '';
    $_GET['fpid'] = (isset($_GET['fpid']) && is_numeric($_GET['fpid'])) ? abs($_GET['fpid']) : '';
    $_GET['quotename'] = (isset($_GET['quotename']) && is_numeric($_GET['quotename'])) ? abs($_GET['quotename']) : '';
    if (empty($_GET['viewtopic'])) {
        alert("danger", "Uh Oh!", "Please specify a topic you wish to view.", true, "forums.php");
        die($h->endpage());
    }
    if (!isset($_GET['quotename']) || !isset($_GET['fpid'])) {
        alert("danger", "Uh Oh!", "Please select a post you wish to quote.", true, "forums.php?viewtopic={$_GET['viewtopic']}");
        die($h->endpage());
    }
    $q =
        $db->query("SELECT `ft_forum_id`, `ft_name`, `ft_locked`, `ft_id` FROM `forum_topics` WHERE `ft_id` = {$_GET['viewtopic']}");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        alert('danger', "Forum topic does not exist!", "You are attempting to interact with a topic that does not exist. Check your source and try again.", true, "forums.php");
        die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $q2 =
        $db->query(
            "SELECT `ff_auth`,`ff_id`, `ff_name`
                     FROM `forum_forums`
                     WHERE `ff_id` = {$topic['ft_forum_id']}");
    if ($db->num_rows($q2) == 0) {
        $db->free_result($q2);
        alert('danger', "Non-existent Forum Category", "You are attempting to view a non-existent forum category. Check your source and try again.", true, "forums.php?viewtopic={$_GET['viewtopic']}");
        die($h->endpage());
    }
    $forum = $db->fetch_row($q2);
    $db->free_result($q2);
    if ($forum['ff_auth'] == 'staff') {
        if ($api->user->getStaffLevel($userid, 'forum moderator') == false) {
            alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php");
            die($h->endpage());
        }
    }
    $q3 = $db->query("SELECT `fp_text` FROM `forum_posts` WHERE `fp_id` = {$_GET['fpid']}");
    $text = $db->fetch_single($q3);
    $text = strip_tags(stripslashes($text));
    $q4 = $db->query("SELECT `username` FROM `users` WHERE `userid` = {$_GET['quotename']}");
    $Who = $db->fetch_single($q4);
    echo "<ol class='breadcrumb'>
		<li class='breadcrumb-item'><a href='forums.php'>Forums Home</a></li>
		<li class='breadcrumb-item'><a href='?viewforum={$forum['ff_id']}'>{$forum['ff_name']}</a></li>
		<li class='breadcrumb-item'><a href='?viewtopic={$_GET['viewtopic']}'>{$topic['ft_name']}</a></li>
		<li class='breadcrumb-item active'>Quoting Post Form</li>
	</ol>";
    if ($topic['ft_locked'] == 0) {
        echo "
		<b>Quoting a post</b><br />
		<form method='post' action='forums.php?reply={$topic['ft_id']}'>
		<table class='table'>
			<div class='row'>
				<div class='col-sm'>
					<label for='fp_text'>Reply</label>
				</div>
				<div class='col-sm'>
					<textarea rows='8' class='form-control' cols='45' name='fp_text' id='fp_text' required>[quote={$Who}]{$text}[/quote]</textarea>
				</div>
			</div>
			<div class='row'>
				<div class='col-sm'>
					<input type='submit' class='btn btn-lg btn-primary' value='Submit Reply' />
				</div>
			</div>
		</div>
		<input type='hidden' name='verf' value='{$code}' />
		</form>";
    } else {
        echo "This topic is locked and you cannot reply to it.";
    }
}

function edit()
{
    global $userid, $h, $db, $api;
    $_GET['topic'] = (isset($_GET['topic']) && is_numeric($_GET['topic'])) ? abs($_GET['topic']) : '';
    if (empty($_GET['topic'])) {
        alert("danger", "Uh Oh!", "Please specify the topic you wish to view.", true, "forums.php");
        die($h->endpage());
    }
    $q =
        $db->query(
            "SELECT `ft_forum_id`, `ft_name`, `ft_id`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$_GET['topic']}");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        alert('danger', "Forum topic does not exist!", "You are attempting to interact with a topic that does not exist. Check your source and try again.", true, "forums.php");
        die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $q2 =
        $db->query(
            "SELECT `ff_auth`, `ff_id`, `ff_name`
                     FROM `forum_forums`
                     WHERE `ff_id` = {$topic['ft_forum_id']}");
    if ($db->num_rows($q2) == 0) {
        $db->free_result($q2);
        alert('danger', "Non-existent Forum Category", "You are attempting to view a non-existent forum category. Check your source and try again.", true, "forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    $forum = $db->fetch_row($q2);
    $db->free_result($q2);
    if ($forum['ff_auth'] == 'staff') {
        if (!$api->user->getStaffLevel($userid, 'forum moderator')) {
            alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php?viewtopic={$_GET['topic']}");
            die($h->endpage());
        }
    }
    $_GET['post'] = (isset($_GET['post']) && is_numeric($_GET['post'])) ? abs($_GET['post']) : '';
    if (empty($_GET['post'])) {
        alert("danger", "Uh Oh!", "Please specify the post you wish to edit.", true, "forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    $q3 =
        $db->query(
            "SELECT `fp_poster_id`, `fp_text`
                     FROM `forum_posts`
                     WHERE `fp_id` = {$_GET['post']}");
    if ($db->num_rows($q3) == 0) {
        $db->free_result($q3);
        alert("danger", "Non-existent Post!", "The post you've chosen does not exist. Check your source and try again.", true, "forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    $post = $db->fetch_row($q3);
    $db->free_result($q3);
    if (!($api->user->getStaffLevel($userid, 'forum moderator') || $userid == $post['fp_poster_id'])) {
        alert('danger', "Security Issue!", "You do not have permission to edit this post.", true, "forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    echo "<ol class='breadcrumb'>
		<li class='breadcrumb-item'><a href='forums.php'>Forums Home</a></li>
		<li class='breadcrumb-item'><a href='?viewforum={$forum['ff_id']}'>{$forum['ff_name']}</a></li>
		<li class='breadcrumb-item'><a href='?viewtopic={$_GET['topic']}'>{$topic['ft_name']}</a></li>
		<li class='breadcrumb-item active'>Edit Post Form</li>
	</ol>";
    $edit_csrf = getCodeCSRF("forums_editpost_{$_GET['post']}");
    $fp_text = strip_tags(stripslashes($post['fp_text']));
    echo <<<EOF
<form action='?act=editsub&topic={$topic['ft_id']}&post={$_GET['post']}' method='post'>
<input type='hidden' name='verf' value='{$edit_csrf}' />
    <table class='table'>
        <div class='row'>
        	<div class='col-sm'>
			Editing a post
			</div>
        	<div class='col-sm'>
        		<textarea rows='7' class='form-control' cols='40' name='fp_text'>{$fp_text}</textarea>
        	</div>
        </div>
        <div class='row'>
        	<div class='col-sm'>
				<input type='submit' class='btn btn-primary' value='Edit Post'>
			</div>
        </div>
    </div>
</form>
EOF;
}

function editsub()
{
    global $ir, $userid, $h, $db, $api;
    $_GET['post'] = (isset($_GET['post']) && is_numeric($_GET['post'])) ? abs($_GET['post']) : '';
    $_GET['topic'] = (isset($_GET['topic']) && is_numeric($_GET['topic'])) ? abs($_GET['topic']) : '';
    if ((empty($_GET['post']) || empty($_GET['topic']))) {
        alert("danger", "Uh Oh!", "Please specify a topic you wish to view.", true, "forums.php");
        die($h->endpage());
    }
    if (!isset($_POST['verf']) || !checkCSRF("forums_editpost_{$_GET['post']}", stripslashes($_POST['verf']))) {
        csrf_error("?viewtopic={$_GET['topic']}");
    }
    $q =
        $db->query(
            "SELECT `ft_forum_id`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$_GET['topic']}");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        alert('danger', "Forum topic does not exist!", "You are attempting to interact with a topic that does not exist. Check your source and try again.", true, "forums.php");
        die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $q2 =
        $db->query(
            "SELECT `ff_auth`
                     FROM `forum_forums`
                     WHERE `ff_id` = {$topic['ft_forum_id']}");
    if ($db->num_rows($q2) == 0) {
        $db->free_result($q2);
        alert('danger', "Non-existent Forum Category", "You are attempting to view a non-existent forum category. Check your source and try again.", true, "forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    $forum = $db->fetch_row($q2);
    $db->free_result($q2);
    if ($forum['ff_auth'] == 'staff') {
        if (!($api->user->getStaffLevel($userid, 'forum moderator'))) {
            alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php?viewtopic={$_GET['topic']}");
            die($h->endpage());
        }
    }
    $q3 =
        $db->query(
            "SELECT `fp_poster_id`
                     FROM `forum_posts`
                     WHERE `fp_id` = {$_GET['post']}");
    if ($db->num_rows($q3) == 0) {
        $db->free_result($q3);
        alert("danger", "Non-existent Post!", "The post you've chosen does not exist. Check your source and try again.", true, "forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    $post = $db->fetch_row($q3);
    $db->free_result($q3);
    if (!(($api->user->getStaffLevel($userid, 'forum moderator')) || $ir['userid'] == $post['fp_poster_id'])) {
        alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    $_POST['fp_text'] = $db->escape(str_replace("\n", "<br />", strip_tags(stripslashes($_POST['fp_text']))));
    if ((strlen($_POST['fp_text']) > 65535)) {
        alert('danger', "Uh Oh!", "Posts can only be, at maximum, 65,535 characters in length.", true, "forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    $db->query(
        "UPDATE `forum_posts`
             SET 
             `fp_text` = '{$_POST['fp_text']}', `fp_editor_id` = $userid,
             `fp_editor_id` = '{$userid}',
             `fp_editor_time` = " . time() . ",
             `fp_edit_count` = `fp_edit_count` + 1
             WHERE `fp_id` = {$_GET['post']}");

    alert('success', "Success!", "You have edited this post successfully.", false);
    echo "<br />";
    $_GET['viewtopic'] = $_GET['topic'];
    viewtopic();

}

function move()
{
    global $userid, $h, $db, $api;
    if (!($api->user->getStaffLevel($userid, 'forum moderator'))) {
        alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php");
        die($h->endpage());
    }
    $_GET['topic'] = (isset($_GET['topic']) && is_numeric($_GET['topic'])) ? abs($_GET['topic']) : '';
    $_POST['forum'] = (isset($_POST['forum']) && is_numeric($_POST['forum'])) ? abs($_POST['forum']) : '';
    if (empty($_GET['topic']) || empty($_POST['forum'])) {
        alert('danger', "Uh Oh!", "Please select a topic you wish to view.", true, "forums.php");
        die($h->endpage());
    }
    $q = $db->query("SELECT `ft_name`, `ft_forum_id` FROM `forum_topics` WHERE `ft_id` = {$_GET['topic']}");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        alert('danger', "Forum topic does not exist!", "You are attempting to interact with a topic that does not exist. Check your source and try again.", true, "forums.php");
        die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $q2 =
        $db->query(
            "SELECT `ff_name`
                     FROM `forum_forums`
                     WHERE `ff_id` = {$_POST['forum']}");
    if ($db->num_rows($q2) == 0) {
        $db->free_result($q2);
        alert('danger', "Uh Oh!", "The category you're trying to move the topic to does not exist.", true, "forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    $forum = $db->fetch_row($q2);
    $db->free_result($q2);
    $db->query(
        "UPDATE `forum_topics`
             SET `ft_forum_id` = {$_POST['forum']}
             WHERE `ft_id` = {$_GET['topic']}");
    alert('success', "Success!", "Topic was moved successfully.", true, "forums.php?viewtopic={$_GET['topic']}");
    $api->game->addLog($userid, 'staff', "Moved Topic {$topic['ft_name']} to {$forum['ff_name']}");
    recacheForum($topic['ft_forum_id']);
    recacheForum($_POST['forum']);
}

function lock()
{
    global $userid, $h, $db, $api;
    if (!($api->user->getStaffLevel($userid, 'forum moderator'))) {
        alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php");
        die($h->endpage());
    }
    $_GET['topic'] = (isset($_GET['topic']) && is_numeric($_GET['topic'])) ? abs($_GET['topic']) : '';
    if (empty($_GET['topic'])) {
        alert('danger', "Uh Oh!", "Please select a topic you wish to view.", true, "forums.php");
        die($h->endpage());
    }
    $q =
        $db->query(
            "SELECT `ft_name`,`ft_locked`,`ft_forum_id`, `ft_id`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$_GET['topic']}");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        alert('danger', "Forum topic does not exist!", "You are attempting to interact with a topic that does not exist. Check your source and try again.", true, "forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if ($r['ft_locked'] == 1) {
        $db->query(
            "UPDATE `forum_topics`
                 SET `ft_locked` = 0
                 WHERE `ft_id` = {$_GET['topic']}");
        alert('success', "Success!", "You have unlocked this topic.", false);
        $api->game->addLog($userid, 'staff', "Unlocked Topic {$r['ft_name']}");
    } else {
        $db->query(
            "UPDATE `forum_topics`
                 SET `ft_locked` = 1
                 WHERE `ft_id` = {$_GET['topic']}");
        alert('success', "Success!", "You have locked this topic.", false);
        $api->game->addLog($userid, 'staff', "Locked Topic {$r['ft_name']}");
    }
}

function pin()
{
    global $userid, $h, $db, $api;
    if (!($api->user->getStaffLevel($userid, 'forum moderator'))) {
        alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php");
        die($h->endpage());
    }
    $_GET['topic'] = (isset($_GET['topic']) && is_numeric($_GET['topic'])) ? abs($_GET['topic']) : '';
    if (empty($_GET['topic'])) {
        alert('danger', "Uh Oh!", "Please select a topic you wish to view.", true, "forums.php");
        die($h->endpage());
    }
    $q =
        $db->query(
            "SELECT `ft_name`, `ft_pinned`, `ft_forum_id`, `ft_id`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$_GET['topic']}");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        alert('danger', "Forum topic does not exist!", "You are attempting to interact with a topic that does not exist. Check your source and try again.", true, "forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if ($r['ft_pinned'] == 1) {
        $db->query(
            "UPDATE `forum_topics`
                 SET `ft_pinned` = 0
                 WHERE `ft_id` = {$_GET['topic']}");
        alert('success', "Success!", "You have unpinned this topic.", true, "forums.php?viewtopic={$r['ft_id']}");
        $api->game->addLog($userid, 'staff', "Unpinned Topic {$r['ft_name']}");
    } else {
        $db->query(
            "UPDATE `forum_topics`
                 SET `ft_pinned` = 1
                 WHERE `ft_id` = {$_GET['topic']}");
        alert('success', "Success!", "You have pinned this topic.", true, "forums.php?viewtopic={$r['ft_id']}");
        $api->game->addLog($userid, 'staff', "Pinned Topic {$r['ft_name']}");
    }
}

function delepost()
{
    global $userid, $h, $db, $api;
    if (!($api->user->getStaffLevel($userid, 'forum moderator'))) {
        alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php");
        die($h->endpage());
    }
    $_GET['post'] = isset($_GET['post']) && is_numeric($_GET['post']) ? abs($_GET['post']) : '';
    if (empty($_GET['post'])) {
        alert('danger', "Uh Oh!", "Please select a topic you wish to view.", true, "forums.php");
        die($h->endpage());
    }
    $q3 =
        $db->query(
            "SELECT *
                     FROM `forum_posts`
                     WHERE `fp_id` = {$_GET['post']}");
    if ($db->num_rows($q3) == 0) {
        $db->free_result($q3);
        alert('danger', "Non-existent Post!", "The post you've chosen does not exist. Check your source and try again.", true, "forums.php");
        die($h->endpage());
    }
    $post = $db->fetch_row($q3);
    $db->free_result($q3);
    $q =
        $db->query(
            "SELECT `ft_name`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$post['fp_topic_id']}");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        alert('danger', "Forum topic does not exist!", "You are attempting to interact with a topic that does not exist. Check your source and try again.", true, "forums.php?viewtopic={$post['fp_topic_id']}");
        die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $db->query(
        "DELETE FROM `forum_posts`
    		    WHERE `fp_id` = {$post['fp_id']}");
    alert('success', "Success!", "You have deleted this post.", true, "forums.php?viewtopic={$post['fp_topic_id']}");
    recacheTopic($post['fp_topic_id']);
    recacheForum($post['ff_id']);
    $api->game->addLog($userid, 'staff', "Deleted post ({$post['fp_id']}) in {$topic['ft_name']}");

}

function deletopic()
{
    global $userid, $h, $db, $api;
    $_GET['topic'] = (isset($_GET['topic']) && is_numeric($_GET['topic'])) ? abs($_GET['topic']) : '';
    if (!($api->user->getStaffLevel($userid, 'forum moderator'))) {
        alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    if (empty($_GET['topic'])) {
        alert('danger', "Uh Oh!", "Please select a topic you wish to view.", true, 'forums.php');
        die($h->endpage());
    }
    $q = $db->query("SELECT `ft_forum_id`, `ft_name` FROM `forum_topics` WHERE `ft_id` = {$_GET['topic']}");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        alert('danger', "Forum topic does not exist!", "You are attempting to interact with a topic that does not exist. Check your source and try again.", true, "forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $db->query("DELETE FROM `forum_topics` WHERE `ft_id` = {$_GET['topic']}");
    $db->query("DELETE FROM `forum_posts` WHERE `fp_topic_id` = {$_GET['topic']}");
    alert('success', "Success!", "You have deleted this topic successfully.", true, 'forums.php');
    recacheForum($topic['ft_forum_id']);
    $api->game->addLog($userid, 'staff', "Deleted topic {$topic['ft_name']}");
}

$h->endpage();