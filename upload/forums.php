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
if ($ir['guild'] > 0) 
{
    $gq = $db->query("/*qc=on*/SELECT * FROM `guild` WHERE `guild_id` = {$ir['guild']}");
	if ($db->num_rows($gq) > 0) 
	{
		$gd = $db->fetch_row($gq);
		$db->free_result($gq);
	}
}
function csrf_error()
{
    global $h;
    alert('danger', "Action Blocked!", "The action you were trying to do was blocked. It was blocked because you loaded
        another page on the game. If you have not loaded a different page during this time, change your password
        immediately, as another person may have access to your account!");
    die($h->endpage());
}

echo "<h3><i class='far fa-comment-alt'></i> {$set['WebsiteName']} Forums</h3><hr />";
$fb = $db->fetch_row($db->query("/*qc=on*/SELECT * FROM `forum_bans` WHERE `fb_user` = {$userid}"));
if ($fb['fb_time'] > $time) {
    alert('danger', "Uh Oh!", "You are currently forum banned for the next " . TimeUntil_Parse($fb['fb_time']) . ". You
	    were banned for {$fb['fb_reason']}. If you feel the ban was unjustified, please contact an admin
	    immediately.", true, 'index.php');
    die($h->endpage());
}
alert('info',"","Read the Forum rules before posting!!", true, '?viewtopic=167&lastpost=1', 'View Rules');
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
	case 'lock2':
        lock2();
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
            recache_forum($_GET['forum']);
        }
        break;
    default:
        idx();
        break;
}
function idx()
{
    global $ir, $db, $api, $userid;
    $q =
        $db->query(
            "/*qc=on*/SELECT `ff_lp_time`, `ff_id`, `ff_name`, `ff_desc`,
					`ff_lp_t_id`,
                     `ff_lp_poster_id`
                     FROM `forum_forums`
                     WHERE `ff_auth` = 'public'
                     ORDER BY `ff_id` ASC");
    while ($r = $db->fetch_row($q)) {
        $t = DateTime_Parse($r['ff_lp_time'], true, true);
        $username = parseUsername($r['ff_lp_poster_id']);

        $topicsq = $db->query("/*qc=on*/SELECT COUNT('ft_id') FROM `forum_topics` WHERE `ft_forum_id`={$r['ff_id']}");

        $postsq = $db->query("/*qc=on*/SELECT COUNT('fp_id') FROM `forum_posts` WHERE `ff_id`={$r['ff_id']}");
        $posts = $db->fetch_single($postsq);
        $topics = $db->fetch_single($topicsq);

        $topicname = $db->fetch_single($db->query("/*qc=on*/SELECT `ft_name` FROM `forum_topics` WHERE `ft_forum_id` = {$r['ff_id']} ORDER BY `ft_last_time` DESC"));
        if (strlen($topicname) > 32)
        {
            $topicname = substr($topicname,0,32);
            $topicname = "{$topicname}...";
        }
        echo "<div class='row'>
					<div class='col-12'>
						<div class='card'>
							<div class='card-body'>
								<div class='col-12'>
									<div class='row'>
										<div class='col-12 col-md-6 col-xl-7'>
											<div class='row'>
												<div class='col-12'>
													<a href='?viewforum={$r['ff_id']}'>{$r['ff_name']}</a>
												</div>
												<div class='col-12'>
													<small><i>{$r['ff_desc']}</i></small>
												</div>
											</div>
										</div>
										<div class='col-4 col-sm-5 col-md-3 col-lg-2'>
											Posts: {$posts}<br />
											Topics: {$topics}
										</div>
										<div class='col-8 col-sm-7 col-md-3 col-lg-4 col-xl-3'>
											Last Post: {$t}<br />
											In: <a href='?viewtopic={$r['ff_lp_t_id']}&lastpost=1'>{$topicname}</a><br />
											By: <a href='profile.php?user={$r['ff_lp_poster_id']}'>{$username}</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>";
    }
    $db->free_result($q);
    if ($api->UserMemberLevelGet($userid, 'forum moderator')) {
        echo "<hr /><h3>Staff Only Forums</h3><hr />";
        $q =
            $db->query(
                "/*qc=on*/SELECT `ff_lp_time`, `ff_id`, `ff_name`, `ff_desc`,
                     `ff_lp_t_id`,
                     `ff_lp_poster_id`
                     FROM `forum_forums`
                     WHERE `ff_auth` = 'staff'
                     ORDER BY `ff_id` ASC");

        while ($r = $db->fetch_row($q)) {
            $t = DateTime_Parse($r['ff_lp_time'], true, true);
            $username = parseUsername($r['ff_lp_poster_id']);

            $topicsq = $db->query("/*qc=on*/SELECT COUNT('ft_id') FROM `forum_topics` WHERE `ft_forum_id`={$r['ff_id']}");

            $postsq = $db->query("/*qc=on*/SELECT COUNT('fp_id') FROM `forum_posts` WHERE `ff_id`={$r['ff_id']}");
            $posts = $db->fetch_single($postsq);
            $topics = $db->fetch_single($topicsq);

            $topicname = $db->fetch_single($db->query("/*qc=on*/SELECT `ft_name` FROM `forum_topics` WHERE `ft_forum_id` = {$r['ff_id']} ORDER BY `ft_last_time` DESC"));
             echo "<div class='row'>
					<div class='col-12'>
						<div class='card'>
							<div class='card-body'>
								<div class='col-12'>
									<div class='row'>
										<div class='col-12 col-md-6 col-xl-7'>
											<div class='row'>
												<div class='col-12'>
													<a href='?viewforum={$r['ff_id']}'>{$r['ff_name']}</a>
												</div>
												<div class='col-12'>
													<small><i>{$r['ff_desc']}</i></small>
												</div>
											</div>
										</div>
										<div class='col-4 col-sm-5 col-md-3 col-lg-2'>
											Posts: {$posts}<br />
											Topics: {$topics}
										</div>
										<div class='col-8 col-sm-7 col-md-3 col-lg-4 col-xl-3'>
											Last Post: {$t}<br />
											In: <a href='?viewtopic={$r['ff_lp_t_id']}&lastpost=1'>{$topicname}</a><br />
											By: <a href='profile.php?user={$r['ff_lp_poster_id']}'>{$username}</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>";
        }
        $db->free_result($q);
    }
}

function viewforum()
{
    global $ir, $db, $h, $userid, $api;
	$topicView=getCurrentUserPref('topicView', 20);
    $_GET['viewforum'] = (isset($_GET['viewforum']) && is_numeric($_GET['viewforum'])) ? abs($_GET['viewforum']) : '';
    if (empty($_GET['viewforum'])) {
        alert('danger', "Uh Oh!", "You must enter a forum category you wish to view.", true, "forums.php");
        die($h->endpage());
    }
    $q =
        $db->query(
            "/*qc=on*/SELECT *
                     FROM `forum_forums`
                     WHERE `ff_id` = '{$_GET['viewforum']}'");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        alert('danger', "Non-existent Forum Category!", "You are attempting to view a non-existent forum category. Check your source and try again.", true, "forums.php");
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if ($r['ff_auth'] == 'staff') {
        if (!$api->UserMemberLevelGet($userid, 'forum moderator')) {
            alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php");
            die($h->endpage());
        }
    }
	if ($r['ff_auth'] == 'guild' && $ir['guild'] != $r['ff_owner']) {
		alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php");
		die($h->endpage());
	}
    if (isset($_GET['rate']))
    {
        $_GET['topic'] = (isset($_GET['topic']) && is_numeric($_GET['topic'])) ? abs($_GET['topic']) : '';
        if ((empty($_GET['topic'])) || (empty($_GET['rate'])))
        {
            alert('danger', "Uh Oh!", "Please use the links to up-vote a thread.", false);
        }
        else
        {
            updateRating($_GET['topic'],$_GET['rate']);
            alert('success','Success!',"You have successfully rated this topic.",false);
        }
    }
	if (permission("CanCreateThread",$userid))
		$ntl = "&nbsp;[<a href='?act=newtopicform&forum={$_GET['viewforum']}'>New Topic</a>]";
	else
		$ntl = "";
    echo "<ol class='breadcrumb'>
		<li class='breadcrumb-item'><a href='forums.php'>Forums Home</a></li>
		<li class='breadcrumb-item active'>{$r['ff_name']} {$ntl}</li>	
	</ol>";
    $posts_topic = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`ft_id`) 
													FROM `forum_topics` 
													WHERE 
													`ft_forum_id` = {$_GET['viewforum']}"));
    $st = (isset($_GET['st']) && is_numeric($_GET['st'])) ? abs($_GET['st']) : 0;
    echo pagination($topicView, $posts_topic, $st, "?viewforum={$_GET['viewforum']}&amp;st=");
    $q =
        $db->query(
            "/*qc=on*/SELECT `ft_start_time`, `ft_last_time`, `ft_pinned`,
                     `ft_locked`, `ft_id`, `ft_name`, `ft_desc`, `ft_posts`,
                     `ft_owner_id`, `ft_last_id`
                     FROM `forum_topics`
                     WHERE `ft_forum_id` = {$_GET['viewforum']}
                     ORDER BY `ft_pinned` DESC, `ft_last_time` DESC
					 LIMIT {$st}, {$topicView}");
    while ($r2 = $db->fetch_row($q)) 
	{
        $t1 = DateTime_Parse($r2['ft_start_time'], true, true);
        $t2 = DateTime_Parse($r2['ft_last_time'], true, true);
        $votes = $db->fetch_single($db->query("/*qc=on*/SELECT SUM(`rating`) FROM `forum_tops_rating` WHERE `topic_id` = {$r2['ft_id']}"));
		$votes = number_format($votes);
        $pt = ($r2['ft_pinned']) ? " <i class='fa fa-thumbtack' aria-hidden='true'></i>" : "" ;
        $lt = ($r2['ft_locked']) ? " <i class='fa fa-lock' aria-hidden='true'></i>" : "" ;
        $pn1['username'] = parseUsername($r2['ft_owner_id']);
        $pn2['username'] = parseUsername($r2['ft_last_id']);
        $pcq = $db->query("/*qc=on*/SELECT COUNT(`fp_id`) FROM `forum_posts` WHERE `fp_topic_id` = {$r2['ft_id']}");
        $pc = $db->fetch_single($pcq);
        if (!$pn2) {
            $pn2['username'] = "Non-existent User";
        }
        if (!$pn1) {
            $pn1['username'] = "Non-existent User";
        }
		$uservote = getUserTopicRating($r2['ft_id']);
		if ($uservote == 1)
			$type='success';
		elseif ($uservote == -1)
			$type='danger';
		elseif ($uservote == 0)
			$type='primary';
		echo "
			<div class='row'>
				<div class='col-12'>
					<div class='card'>
						<div class='card-body'>
							<div class='col-12'>
								<div class='row'>
									<div class='col-1'>
										{$pt}
									</div>
									<div class='col-8 col-sm-7 col-md-4'>
										<div class='row'>
											<a href='?viewtopic={$r2['ft_id']}&lastpost=1'>{$r2['ft_name']}</a>
										</div>
										<div class='row'><small>";
											if (!empty($r2['ft_desc']))
											{
												echo "{$r2['ft_desc']}<br />";
											}
											echo"Rating: <a href='?viewforum={$_GET['viewforum']}&rate=up&topic={$r2['ft_id']}'>+</a> 
												<span class='badge badge-pill badge-{$type}'>
													<a href='?viewforum={$_GET['viewforum']}&rate=none&topic={$r2['ft_id']}' class='text-white'>{$votes}</a>
												</span> 
											<a href='?viewforum={$_GET['viewforum']}&rate=down&topic={$r2['ft_id']}'>-</a></small>
										</div>
									</div>
									<div class='col-1'>
										{$lt}
									</div>
									<div class='col-6 col-md-3'>
										{$t1}<br />
										<small><a href='profile.php?user={$r2['ft_owner_id']}'>{$pn1['username']}</a></small>
									</div>
									<div class='col-6 col-md-3'>
										{$t2}<br />
										<small><a href='profile.php?user={$r2['ft_last_id']}'>{$pn2['username']}</a></small>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>";
    }
    echo pagination($topicView, $posts_topic, $st, "?viewforum={$_GET['viewforum']}&amp;st=");
    $db->free_result($q);
}

function viewtopic()
{
    global $ir, $userid, $parser, $db, $h, $api, $gd;
    $code = request_csrf_code('forum_reply');
	$postView=getCurrentUserPref('postView', 20);
    $precache = array();
    $_GET['viewtopic'] = (isset($_GET['viewtopic']) && is_numeric($_GET['viewtopic'])) ? abs($_GET['viewtopic']) : '';
    if (empty($_GET['viewtopic'])) {
        alert('danger', "Uh Oh!", "You must enter a topic you wish to view.", true, "forums.php");
        die($h->endpage());
    }
    $q =
        $db->query(
            "/*qc=on*/SELECT *
                     FROM `forum_topics`
                     WHERE `ft_id` = {$_GET['viewtopic']}");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        alert('danger', "Forum topic does not exist!", "You are attempting to interact with a topic that does not exist. Check your source and try again.", true, "forums.php");
        die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $q2 =
        $db->query(
            "/*qc=on*/SELECT * FROM `forum_forums`
                    WHERE `ff_id` = {$topic['ft_forum_id']}");
    if ($db->num_rows($q2) == 0) {
        $db->free_result($q2);
        alert('danger', "Non-existent Forum Category", "You are attempting to view a non-existent forum category. Check your source and try again.", true, "forums.php?viewforum={$topic['ft_forum_id']}");
        die($h->endpage());
    }
    $forum = $db->fetch_row($q2);
    $db->free_result($q2);
    if ($forum['ff_auth'] == 'staff') {
        if ($api->UserMemberLevelGet($userid, 'forum moderator') == false) {
            alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php");
            die($h->endpage());
        }
    }
	if ($forum['ff_auth'] == 'guild' && $ir['guild'] != $forum['ff_owner']) {
		alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php");
		die($h->endpage());
	}
    echo "<ol class='breadcrumb'>
		<li class='breadcrumb-item'><a href='forums.php'>Forums Home</a></li>
		<li class='breadcrumb-item'><a href='?viewforum={$forum['ff_id']}'>{$forum['ff_name']}</a></li>
		<li class='breadcrumb-item active'>{$topic['ft_name']}</li>	
	</ol>";
    $posts_topic = $topic['ft_posts'];
    $st = (isset($_GET['st']) && is_numeric($_GET['st'])) ? abs($_GET['st']) : 0;
	if (isset($_GET['lastpost']))
    {
		$postslastpage= floor($posts_topic/$postView);
		$st = $postslastpage*$postView;
    }
    echo pagination($postView, $posts_topic, $st, "?viewtopic={$topic['ft_id']}&st=");
    if ($ir['user_level'] != 'Member') {
        $lock = ($topic['ft_locked'] == 0) ? 'Lock Topic' : 'Unlock Topic' ;
        $pin = ($topic['ft_pinned'] == 0) ? 'Pin Topic' : 'Unpin Topic' ;
        echo "
	<form action='?act=move&topic={$_GET['viewtopic']}' method='post'>
    <b>Move Topic To</b> " . forum_dropdown('forum')
            . "
	<input type='submit' value='Move Topic' class='btn btn-primary' />
	</form>
	<br />
	<div class='row'>
		<div class='col-sm'>
			<form>
				<input type='hidden' value='pin' name='act'>
				<input type='hidden' name='topic' value='{$_GET['viewtopic']}'>
				<input type='submit' class='btn btn-primary' value='{$pin}'>
			</form>
		</div>
		<div class='col-sm'>
			<form>
				<input type='hidden' value='lock' name='act'>
				<input type='hidden' name='topic' value='{$_GET['viewtopic']}'>
				<input type='submit' class='btn btn-primary' value='{$lock}'>
			</form>
		</div>
		<div class='col-sm'>
			<form action='?act=deletopic'>
				<input type='hidden' value='deletopic' name='act'>
				<input type='hidden' name='topic' value='{$_GET['viewtopic']}'>
				<input type='submit' class='btn btn-primary' value='Delete'>
			</form>
		</div>
	</div>
	<br /> ";
    }
	/*if ($ir['guild'] == $forum['ff_owner'])
	{
		if (isGuildLeadership())
		{
			$lock = ($topic['ft_locked'] == 0) ? 'Lock Topic' : 'Unlock Guild Topic' ;
			$pin = ($topic['ft_pinned'] == 0) ? 'Pin Topic' : 'Unpin Guild Topic' ;
			echo "
			<div class='row'>
				<div class='col-sm'>
					<form>
						<input type='hidden' value='pin2' name='act'>
						<input type='hidden' name='topic' value='{$_GET['viewtopic']}'>
						<input type='submit' class='btn btn-primary' value='{$pin}'>
					</form>
				</div>
				<div class='col-sm'>
					<form>
						<input type='hidden' value='lock2' name='act'>
						<input type='hidden' name='topic' value='{$_GET['viewtopic']}'>
						<input type='submit' class='btn btn-primary' value='{$lock}'>
					</form>
				</div>
				<div class='col-sm'>
					<form action='?act=deletopic'>
						<input type='hidden' value='deletopic2' name='act'>
						<input type='hidden' name='topic' value='{$_GET['viewtopic']}'>
						<input type='submit' class='btn btn-primary' value='Delete Guild Topic'>
					</form>
				</div>
			</div>";
		}
	}*/
    $q3 =
        $db->query(
            "/*qc=on*/SELECT `fp_editor_time`, `fp_editor_id`, `fp_edit_count`,
                     `fp_time`, `fp_id`, `fp_poster_id`, `fp_text`
                     FROM `forum_posts`
                     WHERE `fp_topic_id` = {$topic['ft_id']}
                     ORDER BY `fp_time` ASC
                     LIMIT {$st}, {$postView}");
    $no = $st;
    while ($r = $db->fetch_row($q3)) {
        $PN['username'] = parseUsername($r['fp_poster_id']);

        $qlink = "<a class='btn btn-primary btn-block' href='?act=quote&viewtopic={$_GET['viewtopic']}&quotename={$r['fp_poster_id']}&fpid={$r['fp_id']}'><i class='fas fa-quote-right'></i></a>";
        if ($api->UserMemberLevelGet($userid, 'forum moderator') || $userid == $r['fp_poster_id']) {
            $elink =
                "<a class='btn btn-primary btn-block' href='?act=edit&post={$r['fp_id']}&topic={$_GET['viewtopic']}'><i class='fas fa-edit'></i></a>";
        } else {
            $elink = "";
        }
        $no++;
        if ($no > 1 and ($api->UserMemberLevelGet($userid, 'forum moderator'))) {
            $dlink =
                "<a class='btn btn-primary btn-block' href='?act=delepost&post={$r['fp_id']}'><i class='fas fa-trash-alt'></i></a>";
        } else {
            $dlink = "";
        }
        if ($api->UserMemberLevelGet($userid, 'forum moderator')) {
            $wlink = "<a class='btn btn-primary btn-block' href='staff/staff_punish.php?action=forumwarn&user={$r['fp_poster_id']}'><i class='fas fa-exclamation'></i></a>";
            $blink = "<a class='btn btn-primary btn-block' href='staff/staff_punish.php?action=forumban&user={$r['fp_poster_id']}'><i class='fas fa-ban'></i></a>";
        } else {
            $wlink = "";
            $blink = "";
        }
        $t = DateTime_Parse($r['fp_time']);
        $editorname = parseUsername($r['fp_editor_id']);
        if ($r['fp_edit_count'] > 0) {
            $edittext =
                "\n<br /><small><i>Last edited by <a href='profile.php?user={$r['fp_editor_id']}'>{$editorname}</a> at "
                . DateTime_Parse($r['fp_editor_time'])
                . ", edited <b>{$r['fp_edit_count']}</b> times total.</i></small>";
        } else {
            $edittext = "";
        }
        if (!isset($precache[$r['fp_poster_id']])) {
            $membq =
                $db->query(
                    "/*qc=on*/SELECT `userid`,
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
            if ($memb['display_pic']) {
                $av = "<img src='" . parseImage(parseDisplayPic($memb['userid'])) . "' class='img-fluid' width='350' alt='{$memb['username']}&#39;s display picture' title='{$memb['username']}&#39;s display picture'>";
            } else {
                $av = "";
            }
            $memb['signature'] = $parser->parse($memb['signature']);
            $memb['signature'] = $parser->getAsHtml($memb['signature']);
        }
		$rlink="<a class='btn btn-primary btn-block' href='playerreport.php?userid={$r['fp_poster_id']}'><i class='fas fa-flag'></i></a>";
        //$r['fp_text']=replaceMentions($r['fp_text']);
		$r['fp_text']=$parser->parse($r['fp_text']);
        $r['fp_text'] = $parser->getAsHtml();
		echo "<div class='row'>
					<div class='col-12'>
						<div class='card'>
							<div class='card-header'>
								<div class='row'>
									<div class='col-4 col-md-2'>
										Post #{$no}
									</div>
									<div class='col-8 col-md-6 col-lg-4'>
										Posted {$t}
									</div>
									<div class='col-2 col-md-1'>
										{$qlink}
									</div>
									<div class='col-2 col-md-1'>
										{$elink}
									</div>
									<div class='col-2 col-md-1'>
										{$dlink} 
									</div>
									<div class='col-2 col-md-1'>
										{$wlink}
									</div>
									<div class='col-2 col-md-1'>
										{$blink}
									</div>
									<div class='col-2 col-md-1'>
										{$rlink}
									</div>
								</div>
							</div>
						<div class='card-body'>
							<div class='col-12'>
								<div class='row'>";
        if ($memb['userid'] > 0) {
            $userpostsq = $db->query("/*qc=on*/SELECT COUNT('fp_id') FROM `forum_posts` WHERE `fp_poster_id`={$r['fp_poster_id']}");
            $userposts = $db->fetch_single($userpostsq);

            $usertopicsq = $db->query("/*qc=on*/SELECT COUNT('ft_id') FROM `forum_topics` WHERE `ft_owner_id`={$r['fp_poster_id']}");
            $usertopics = $db->fetch_single($usertopicsq);
			$infirm = ($api->UserStatus($r['fp_poster_id'], 'infirmary')) ? "<i class='game-icon game-icon-hospital-cross'></i>" : "" ;
            $dung = ($api->UserStatus($r['fp_poster_id'], 'dungeon')) ? "<i class='game-icon game-icon-cage'></i>" : "" ;
			echo "
				<div class='row'>
				<div class='col-12 col-lg-5 col-xl-4'>
					<div class='row'>
						<div class='col-12'>
							{$av}
						</div>
						<div class='col-6'>
							<a href='profile.php?user={$r['fp_poster_id']}'>{$PN['username']}</a> [{$r['fp_poster_id']}]
						</div>
						<div class='col-6'>
							<b>Rank</b> {$memb['user_level']}
						</div>
						<div class='col-6'>
							<b>Posts</b> {$userposts}
						</div>
						<div class='col-6'>
							<b>Topics</b> {$usertopics}
						</div>
						<div class='col-6'>
						{$dung}
						</div>
						<div class='col-6'>
							{$infirm}
						</div>
					</div>
				</div>";
        } else {
            print "<div class='col-12 col-lg-5 col-xl-4'><b>Deleted user.</b></div>";
        }
		echo "<div class='col-12 col-lg-7 col-xl-8'>
			{$r['fp_text']}
			{$edittext}
			<hr />
			{$memb['signature']}
		</div>";
		echo "	</div>
					</div>
				</div>
			</div>
		</div>";
    }
    $db->free_result($q3);
    echo pagination($postView, $posts_topic, $st, "?viewtopic={$topic['ft_id']}&st=");
    if ($topic['ft_locked'] == 0) {
		if (permission("CanReplyForum",$userid))
		{
			echo "
			<br />
			<form action='?reply={$topic['ft_id']}' method='post'>
				<table class='table'>
					<tr>
						<th>
							Post Response
							</th>
						<td>
							<textarea class='form-control' id='post' placeholder='You can use BBCode.' name='fp_text' required></textarea>
						</td>
					</tr>
					<tr>
						<td colspan='2'> 
							<input type='submit' value='Submit Reply' class='btn btn-primary btn-block'>
						</td>
					</tr>
				</table>
				<input type='hidden' name='verf' value='{$code}' />
			</form>
			";
		}
		else
		{
			echo "<br /><br />You do not have permission to reply to forum topics.";
		}
    } else {
        echo "<br />
		<br />
		<i>This topic is locked, and as a result, you cannot reply to it!</i>";
    }
}

function reply()
{
    global $h, $userid, $db, $api, $ir;
    $_GET['reply'] = (isset($_GET['reply']) && is_numeric($_GET['reply'])) ? abs($_GET['reply']) : '';
    if (!isset($_POST['verf']) || !verify_csrf_code('forum_reply', stripslashes($_POST['verf']))) {
        csrf_error("?viewtopic={$_GET['reply']}");
    }
    if (empty($_GET['reply'])) {
        alert('danger', "Uh Oh!", "You need to enter a reponse.", true, "forums.php");
        die($h->endpage());
    }
    $q =
        $db->query(
            "/*qc=on*/SELECT `ft_forum_id`, `ft_locked`, `ft_name`, `ft_id`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$_GET['reply']}");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        alert('danger', "Forum topic does not exist!", "You are attempting to interact with a topic that does not exist. Check your source and try again.", true, "forums.php");
        die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $q2 =
        $db->query(
            "/*qc=on*/SELECT *
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
        if ($api->UserMemberLevelGet($userid, 'forum moderator') == false) {
            alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php");
            die($h->endpage());
        }
    }
	if ($forum['ff_auth'] == 'guild' && $ir['guild'] != $forum['ff_owner']) {
		alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php");
		die($h->endpage());
	}
	if (!permission("CanReplyForum",$userid))
	{
		alert('danger', "Uh Oh!", "You do not have permission to reply to forum topics.", true, "forums.php");
		die($h->endpage());
	}
    if ($topic['ft_locked'] == 0) {
        $_POST['fp_text'] = $db->escape(str_replace("\n", "<br />", strip_tags(htmlentities(stripslashes($_POST['fp_text'])))));
        if ((strlen($_POST['fp_text']) > 65535)) {
            alert('danger', "Uh Oh!", "Forum replies can only be, at maximum, 65,535 characters in length.", true, "forums.php?viewtopic={$_GET['reply']}");
            die($h->endpage());
        }
		$lastfivemins=time()-300;
		$postcount=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`fp_id`) FROM `forum_posts` WHERE `fp_poster_id` = {$userid} AND `fp_time` > {$lastfivemins}"));
		if ($postcount == 3)
		{
			$api->SystemLogsAdd(1, 'staff', "Forum Warned <a href='../profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}] for 'Spamming'.");
			$api->SystemLogsAdd(1, 'forumwarn', "Forum Warned <a href='../profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}] for 'Spamming'.");
			$api->GameAddNotification($userid, "You have automatically been received a forum warning for the following reason: Spamming.");
			staffnotes_entry($userid,"Forum warned for 'Spamming'.");
		}
		if ($postcount >= 5)
		{
			$endtime = time() + (3 * 86400);
			$db->query("INSERT INTO `forum_bans` VALUES(NULL,  {$userid}, 1, {$endtime}, 'Spamming')");
			$api->SystemLogsAdd(1, 'staff', "Forum banned <a href='../profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}] for 3 days for Spamming.");
			$api->SystemLogsAdd(1, 'forumban', "Forum banned <a href='../profile.php?user={$userid}'>{$ir['username']}</a> [{$userid}] for 3 days for Spamming.");
			$api->GameAddNotification($userid, "You were automtically forum banned you for 3 days for the following reason: 'Spamming'.");
			staffnotes_entry($userid,"Automatically forum banned for 3 days, with reason 'Spamming'.");
		}
        $post_time = time();
        $db->query("
            INSERT INTO `forum_posts` 
			(`fp_id`, `fp_poster_id`, `fp_time`, 
			`fp_topic_id`, `fp_editor_id`, 
			`fp_edit_count`, `fp_editor_time`, 
			`fp_text`, `ff_id`) VALUES 
			(NULL, '$userid', '$post_time', '{$_GET['reply']}', '0', '0', '0', '{$_POST['fp_text']}', '{$forum['ff_id']}');");
        $db->query(
            "UPDATE `forum_topics`
                 SET `ft_last_id` = $userid,
                 `ft_last_time` = {$post_time}, `ft_posts` = `ft_posts` + 1
                 WHERE `ft_id` = {$_GET['reply']}");
        $db->query(
            "UPDATE `forum_forums`
                 SET `ff_lp_time` = {$post_time},
                 `ff_lp_poster_id` = $userid,
                 `ff_lp_t_id` = {$_GET['reply']}
                 WHERE `ff_id` = {$forum['ff_id']}");
		$toq1=$db->fetch_single($db->query("/*qc=on*/SELECT `ft_owner_id` FROM `forum_topics` WHERE `ft_id` = {$_GET['reply']}"));
		$topicname=$db->fetch_single($db->query("/*qc=on*/SELECT `ft_name` FROM `forum_topics` WHERE `ft_id` = {$_GET['reply']}"));
		$toq2=$db->fetch_single($db->query("/*qc=on*/SELECT `forum_alert` FROM `user_settings` WHERE `userid` = {$toq1}"));
		if (($toq2 == 1) && ($userid != $toq1))
		{
			$api->GameAddNotification($toq1,"<a href='profile.php?user={$userid}'>{$ir['username']}</a> has replied to your forum topic, {$topicname}. Click <a href='forums.php?viewtopic={$_GET['reply']}&lastpost=1'>here</a> to read it.");
		}
        alert('success', "Success!", "Your reply has posted successfully.", false);
        echo "<br />";
        $_GET['lastpost'] = 1;
        $_GET['viewtopic'] = $_GET['reply'];
        viewtopic();
    } else {
        echo "This topic is locked. You cannot reply to it.";
    }
}

function newtopicform()
{
    global $userid, $h, $db, $api, $ir;
    $_GET['forum'] = (isset($_GET['forum']) && is_numeric($_GET['forum'])) ? abs($_GET['forum']) : '';
    if (empty($_GET['forum'])) {
        alert('danger', "Uh Oh!", "You must specify a forum you wish to create this topic in.", true, "forums.php");
        die($h->endpage());
    }
    $q =
        $db->query(
            "/*qc=on*/SELECT *
                     FROM `forum_forums`
                     WHERE `ff_id` = '{$_GET['forum']}'");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        alert('danger', "Forum topic does not exist!", "You are attempting to interact with a topic that does not exist. Check your source and try again.", true, "forums.php?viewforum={$_GET['forum']}");
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if ($r['ff_auth'] == 'staff') {
        if ($api->UserMemberLevelGet($userid, 'forum moderator') == false) {
            alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php?viewforum={$_GET['forum']}");
            die($h->endpage());
        }
    }
	if ($r['ff_auth'] == 'guild' && $ir['guild'] != $r['ff_owner']) {
		alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php?viewforum={$_GET['forum']}");
		die($h->endpage());
	}
	if (!permission("CanCreateThread",$userid))
	{
		alert('danger', "Security Issue!", "You do not have permission to create forum topics.", true, "forums.php?viewforum={$_GET['forum']}");
		die($h->endpage());
	}
    $code = request_csrf_code("forums_newtopic_{$_GET['forum']}");
    echo "<ol class='breadcrumb'>
		<li class='breadcrumb-item'><a href='forums.php'>Forums Home</a></li>
		<li class='breadcrumb-item'><a href='?viewforum={$_GET['forum']}'>{$r['ff_name']}</a></li>
		<li class='breadcrumb-item active'>New Topic Form</li>
	</ol>";
    echo <<<EOF
<form method='post' action='?act=newtopic&forum={$_GET['forum']}'>
	<table class='table'>
		<tr>
			<th>
				<label for='ft_name'>Topic Name</label>
			</th>
			<td>
				<input type='text' class='form-control' id='ft_name' name='ft_name' required>
			</td>
		</tr>
		<tr>
			<th>
				<label for='ft_desc'>Topic Description</label>
			</th>
			<td>
				<input type='text' class='form-control' id='ft_desc' name='ft_desc'>
			</td>
		</tr>
		<tr>
			<th>
				<label for='fp_text'>Opening Post</label>
			</th>
			<td>
				<textarea class='form-control' placeholder='You can use BBCode!' name='fp_text' id='fp_text' required></textarea>
			</td>
		</tr>
		<tr>
			<td align='center' colspan='2'>
				<input type='submit' class='btn btn-lg btn-outline-primary' value='Post Topic' />
			</td>
	</table>
	<input type='hidden' name='verf' value='{$code}' />
</form>
EOF;
}

function newtopic()
{
    global $ir, $userid, $h, $db, $api;
    $_GET['forum'] = (isset($_GET['forum']) && is_numeric($_GET['forum'])) ? abs($_GET['forum']) : '';
    if (!isset($_POST['verf']) || !verify_csrf_code("forums_newtopic_{$_GET['forum']}", stripslashes($_POST['verf']))) {
        csrf_error("?act=newtopicform&forum={$_GET['forum']}");
    }
    if (empty($_GET['forum'])) {
        alert('danger', "Uh Oh!", "", true, "forum.php");
        die($h->endpage());
    }
    $q =
        $db->query(
            "/*qc=on*/SELECT *
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
        if ($api->UserMemberLevelGet($userid, 'forum moderator') == false) {
            alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php");
            die($h->endpage());
        }
    }
	if ($r['ff_auth'] == 'guild' && $ir['guild'] != $r['ff_owner']) {
		alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php");
		die($h->endpage());
	}
	if (!permission("CanCreateThread",$userid))
	{
		alert('danger', "Security Issue!", "You do not have permission to create forum topics.", true, "forums.php?viewforum={$_GET['forum']}");
		die($h->endpage());
	}
    $u = htmlentities($ir['username'], ENT_QUOTES, 'ISO-8859-1');
    $u = $db->escape($u);
    $_POST['ft_name'] =
        $db->escape(strip_tags(htmlentities(stripslashes($_POST['ft_name']))));
    if ((strlen($_POST['ft_name']) > 255)) {
        alert('danger', "Uh Oh!", "Topic names can only be, at maximum, 255 characters in length.", true, "back");
        die($h->endpage());
    }
    $_POST['ft_desc'] =
        $db->escape(strip_tags(htmlentities(stripslashes($_POST['ft_desc']))));
    if ((strlen($_POST['ft_desc']) > 255)) {
        alert('danger', "Uh Oh!", "Topic descriptions can only be, at maximum, 255 characters in length.", true, "back");
        die($h->endpage());
    }
    $_POST['fp_text'] = $db->escape(str_replace("\n", "<br />", strip_tags(htmlentities(stripslashes($_POST['fp_text'])))));
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
    global $userid, $h, $db, $api, $ir;
    $code = request_csrf_code('forum_reply');
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
        $db->query("/*qc=on*/SELECT `ft_forum_id`, `ft_name`, `ft_locked`, `ft_id` FROM `forum_topics` WHERE `ft_id` = {$_GET['viewtopic']}");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        alert('danger', "Forum topic does not exist!", "You are attempting to interact with a topic that does not exist. Check your source and try again.", true, "forums.php");
        die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $q2 =
        $db->query(
            "/*qc=on*/SELECT *
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
        if ($api->UserMemberLevelGet($userid, 'forum moderator') == false) {
            alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php?viewtopic={$_GET['viewtopic']}");
            die($h->endpage());
        }
    }
	if ($forum['ff_auth'] == 'guild' && $ir['guild'] != $forum['ff_owner']) {
		alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php?viewtopic={$_GET['viewtopic']}");
		die($h->endpage());
	}
    $q3 = $db->query("/*qc=on*/SELECT `fp_text` FROM `forum_posts` WHERE `fp_id` = {$_GET['fpid']}");
    $text = $db->fetch_single($q3);
    $text = strip_tags(html_entity_decode(stripslashes($text)));
    $q4 = $db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `userid` = {$_GET['quotename']}");
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
			<tr>
				<th>
					<label for='fp_text'>Reply</label>
				</th>
				<td>
					<textarea class='form-control' name='fp_text' id='fp_text' required>[quote=\"{$Who}\"]{$text}[/quote]</textarea>
				</td>
			</tr>
			<tr>
				<td colspan='2' align='center'>
					<input type='submit' class='btn btn-lg btn-outline-primary' value='Submit Reply' />
				</td>
			</tr>
		</table>
		<input type='hidden' name='verf' value='{$code}' />
		</form>";
    } else {
        echo "This topic is locked and you cannot reply to it.";
    }
}

function edit()
{
    global $userid, $h, $db, $api, $ir;
    $_GET['topic'] = (isset($_GET['topic']) && is_numeric($_GET['topic'])) ? abs($_GET['topic']) : '';
    if (empty($_GET['topic'])) {
        alert("danger", "Uh Oh!", "Please specify the topic you wish to view.", true, "forums.php");
        die($h->endpage());
    }
    $q =
        $db->query(
            "/*qc=on*/SELECT `ft_forum_id`, `ft_name`, `ft_id`
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
            "/*qc=on*/SELECT *
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
        if (!$api->UserMemberLevelGet($userid, 'forum moderator')) {
            alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php?viewtopic={$_GET['topic']}");
            die($h->endpage());
        }
    }
	if ($forum['ff_auth'] == 'guild' && $ir['guild'] != $forum['ff_owner']) {
		alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php?viewtopic={$_GET['topic']}");
		die($h->endpage());
	}
    $_GET['post'] = (isset($_GET['post']) && is_numeric($_GET['post'])) ? abs($_GET['post']) : '';
    if (empty($_GET['post'])) {
        alert("danger", "Uh Oh!", "Please specify the post you wish to edit.", true, "forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    $q3 =
        $db->query(
            "/*qc=on*/SELECT `fp_poster_id`, `fp_text`
                     FROM `forum_posts`
                     WHERE `fp_id` = {$_GET['post']}");
    if ($db->num_rows($q3) == 0) {
        $db->free_result($q3);
        alert("danger", "Non-existent Post!", "The post you've chosen does not exist. Check your source and try again.", true, "forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    $post = $db->fetch_row($q3);
    $db->free_result($q3);
    if (!($api->UserMemberLevelGet($userid, 'forum moderator') || $userid == $post['fp_poster_id'])) {
        alert('danger', "Security Issue!", "You do not have permission to edit this post.", true, "forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    echo "<ol class='breadcrumb'>
		<li class='breadcrumb-item'><a href='forums.php'>Forums Home</a></li>
		<li class='breadcrumb-item'><a href='?viewforum={$forum['ff_id']}'>{$forum['ff_name']}</a></li>
		<li class='breadcrumb-item'><a href='?viewtopic={$_GET['topic']}'>{$topic['ft_name']}</a></li>
		<li class='breadcrumb-item active'>Edit Post Form</li>
	</ol>";
    $edit_csrf = request_csrf_code("forums_editpost_{$_GET['post']}");
    $fp_text = strip_tags(html_entity_decode(stripslashes($post['fp_text'])));
    echo <<<EOF
<form action='?act=editsub&topic={$topic['ft_id']}&post={$_GET['post']}' method='post'>
<input type='hidden' name='verf' value='{$edit_csrf}' />
    <table class='table'>
        <tr>
        	<th>
			Editing a post
			</th>
        	<td>
        		<textarea class='form-control' name='fp_text'>{$fp_text}</textarea>
        	</td>
        </tr>
        <tr>
        	<td align='center' colspan='2'>
				<input type='submit' class='btn btn-primary' value='Edit Post'>
			</th>
        </tr>
    </table>
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
    if (!isset($_POST['verf']) || !verify_csrf_code("forums_editpost_{$_GET['post']}", stripslashes($_POST['verf']))) {
        csrf_error("?viewtopic={$_GET['topic']}");
    }
    $q =
        $db->query(
            "/*qc=on*/SELECT `ft_forum_id`
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
            "/*qc=on*/SELECT *
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
        if (!($api->UserMemberLevelGet($userid, 'forum moderator'))) {
            alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php?viewtopic={$_GET['topic']}");
            die($h->endpage());
        }
    }
	if ($forum['ff_auth'] == 'guild' && $ir['guild'] != $forum['ff_owner']) {
		alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php?viewtopic={$_GET['topic']}");
		die($h->endpage());
	}
    $q3 =
        $db->query(
            "/*qc=on*/SELECT `fp_poster_id`
                     FROM `forum_posts`
                     WHERE `fp_id` = {$_GET['post']}");
    if ($db->num_rows($q3) == 0) {
        $db->free_result($q3);
        alert("danger", "Non-existent Post!", "The post you've chosen does not exist. Check your source and try again.", true, "forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    $post = $db->fetch_row($q3);
    $db->free_result($q3);
    if (!(($api->UserMemberLevelGet($userid, 'forum moderator')) || $ir['userid'] == $post['fp_poster_id'])) {
        alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    $_POST['fp_text'] = $db->escape(str_replace("\n", "<br />", strip_tags(htmlentities(stripslashes($_POST['fp_text'])))));
    if ((strlen($_POST['fp_text']) > 65535)) {
        alert('danger', "Uh Oh!", "Posts can only be, at maximum, 65,535 characters in length.", true, "forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    $db->query(
        "UPDATE `forum_posts`
             SET 
             `fp_text` = '{$_POST['fp_text']}', `fp_editor_id` = $userid,
             `fp_editor_id` = '{$userid}',
             `fp_editor_time` = " . time()
        . ",
             `fp_edit_count` = `fp_edit_count` + 1
             WHERE `fp_id` = {$_GET['post']}");

    alert('success', "Success!", "You have edited this post successfully.", false);
    echo "<br />
   ";
    $_GET['viewtopic'] = $_GET['topic'];
    viewtopic();

}

function move()
{
    global $userid, $h, $db, $api;
    if (!($api->UserMemberLevelGet($userid, 'forum moderator'))) {
        alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php");
        die($h->endpage());
    }
    $_GET['topic'] = (isset($_GET['topic']) && is_numeric($_GET['topic'])) ? abs($_GET['topic']) : '';
    $_POST['forum'] = (isset($_POST['forum']) && is_numeric($_POST['forum'])) ? abs($_POST['forum']) : '';
    if (empty($_GET['topic']) || empty($_POST['forum'])) {
        alert('danger', "Uh Oh!", "Please select a topic you wish to view.", true, "forums.php");
        die($h->endpage());
    }
    $q = $db->query("/*qc=on*/SELECT `ft_name`, `ft_forum_id` FROM `forum_topics` WHERE `ft_id` = {$_GET['topic']}");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        alert('danger', "Forum topic does not exist!", "You are attempting to interact with a topic that does not exist. Check your source and try again.", true, "forums.php");
        die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $q2 =
        $db->query(
            "/*qc=on*/SELECT `ff_name`
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
	$db->query(
            "UPDATE `forum_posts`
             SET `ff_id` = {$_POST['forum']}
             WHERE `fp_topic_id` = {$_GET['topic']}");
    alert('success', "Success!", "Topic was moved successfully.", true, "forums.php?viewtopic={$_GET['topic']}");
    $api->SystemLogsAdd($userid, 'staff', "Moved Topic {$topic['ft_name']} to {$forum['ff_name']}");
    recache_forum($topic['ft_forum_id']);
    recache_forum($_POST['forum']);
    $_GET['viewtopic']=$_GET['topic'];
    viewtopic();
}

function lock()
{
    global $userid, $h, $db, $api;
    if (!($api->UserMemberLevelGet($userid, 'forum moderator'))) {
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
            "/*qc=on*/SELECT `ft_name`,`ft_locked`,`ft_forum_id`, `ft_id`
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
        $api->SystemLogsAdd($userid, 'staff', "Unlocked Topic {$r['ft_name']}.");
    } else {
        $db->query(
            "UPDATE `forum_topics`
                 SET `ft_locked` = 1
                 WHERE `ft_id` = {$_GET['topic']}");
        alert('success', "Success!", "You have locked this topic.", false);
        $api->SystemLogsAdd($userid, 'staff', "Locked Topic {$r['ft_name']}.");
    }
    $_GET['viewtopic']=$_GET['topic'];
    viewtopic();
}

function lock2()
{
    global $userid, $h, $db, $api, $ir;
	$_GET['topic'] = (isset($_GET['topic']) && is_numeric($_GET['topic'])) ? abs($_GET['topic']) : '';
    if (empty($_GET['topic'])) 
	{
        alert('danger', "Uh Oh!", "Please select a topic you wish to view.", true, "forums.php");
        die($h->endpage());
    }
	$q =
        $db->query(
            "/*qc=on*/SELECT `ft_name`,`ft_locked`,`ft_forum_id`, `ft_id`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$_GET['topic']}");
    if ($db->num_rows($q) == 0) 
	{
        $db->free_result($q);
        alert('danger', "Forum topic does not exist!", "You are attempting to interact with a topic that does not exist. Check your source and try again.", true, "forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if ($ir['guild'] == $forum['ff_owner'])
	{
		if (isGuildLeadership())
		{
			if ($r['ft_locked'] == 1) 
			{
				$db->query(
					"UPDATE `forum_topics`
						 SET `ft_locked` = 0
						 WHERE `ft_id` = {$_GET['topic']}");
				alert('success', "Success!", "You have unlocked this topic.", false);
				$api->SystemLogsAdd($userid, 'guilds', "Unlocked Topic {$r['ft_name']}.");
			} 
			else 
			{
				$db->query(
					"UPDATE `forum_topics`
						 SET `ft_locked` = 1
						 WHERE `ft_id` = {$_GET['topic']}");
				alert('success', "Success!", "You have locked this topic.", false);
				$api->SystemLogsAdd($userid, 'guilds', "Locked Topic {$r['ft_name']}.");
			}
		}
		alert('danger', "Uh Oh!", "You are not a member of this guild's leadership and cannot lock this.", false);
	}
	else
	{
		alert('danger', "Uh Oh!", "You are not a member of this guild and cannot lock this.", false);
	}
    $_GET['viewtopic']=$_GET['topic'];
    viewtopic();
}

function pin()
{
    global $userid, $h, $db, $api;
    if (!($api->UserMemberLevelGet($userid, 'forum moderator'))) {
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
            "/*qc=on*/SELECT `ft_name`, `ft_pinned`, `ft_forum_id`, `ft_id`
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
        alert('success', "Success!", "You have unpinned this topic.", false);
        $api->SystemLogsAdd($userid, 'staff', "Unpinned Topic {$r['ft_name']}");
    } else {
        $db->query(
            "UPDATE `forum_topics`
                 SET `ft_pinned` = 1
                 WHERE `ft_id` = {$_GET['topic']}");
        alert('success', "Success!", "You have pinned this topic.", false);
        $api->SystemLogsAdd($userid, 'staff', "Pinned Topic {$r['ft_name']}");
    }
    $_GET['viewtopic']=$r['ft_id'];
    viewtopic();
}

function delepost()
{
    global $userid, $h, $db, $api;
    if (!($api->UserMemberLevelGet($userid, 'forum moderator'))) {
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
            "/*qc=on*/SELECT *
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
            "/*qc=on*/SELECT `ft_name`
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
    alert('success', "Success!", "You have deleted this post.", false);
    recache_topic($post['fp_topic_id']);
    recache_forum($post['ff_id']);
    $api->SystemLogsAdd($userid, 'staff', "Deleted post ({$post['fp_id']}) in {$topic['ft_name']}");
    $_GET['viewtopic']=$post['fp_topic_id'];
    viewtopic();

}

function deletopic()
{
    global $userid, $h, $db, $api;
    $_GET['topic'] = (isset($_GET['topic']) && is_numeric($_GET['topic'])) ? abs($_GET['topic']) : '';
    if (!($api->UserMemberLevelGet($userid, 'forum moderator'))) {
        alert('danger', "Security Issue!", "You do not have permission to view this forum category. If you feel this is incorrect, please contact an admin.", true, "forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    if (empty($_GET['topic'])) {
        alert('danger', "Uh Oh!", "Please select a topic you wish to view.", true, 'forums.php');
        die($h->endpage());
    }
    $q = $db->query("/*qc=on*/SELECT `ft_forum_id`, `ft_name` FROM `forum_topics` WHERE `ft_id` = {$_GET['topic']}");
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
    recache_forum($topic['ft_forum_id']);
    $api->SystemLogsAdd($userid, 'staff', "Deleted topic {$topic['ft_name']}");
}

function updateRating($topic,$rating)
{
    global $db,$userid,$api;
    if (($rating != 'up') && ($rating != 'down') && ($rating != 'none'))
    {
        return;
    }
    $q=$db->query("SELECT * FROM `forum_tops_rating` WHERE `userid` = {$userid} AND `topic_id` = {$topic}");
    if ($db->num_rows($q) == 0)
    {
		if ($rating == 'up')
			$db->query("INSERT INTO `forum_tops_rating` (`topic_id`, `rating`, `userid`) VALUES ('{$topic}', 1, '{$userid}')");
		elseif ($rating == 'down')
			$db->query("INSERT INTO `forum_tops_rating` (`topic_id`, `rating`, `userid`) VALUES ('{$topic}', -1, '{$userid}')");
		elseif ($rating == 'none')
			$db->query("INSERT INTO `forum_tops_rating` (`topic_id`, `rating`, `userid`) VALUES ('{$topic}', 0, '{$userid}')");
	}
    else
    {
		if ($rating == 'up')
			$db->query("UPDATE `forum_tops_rating` SET `rating` = 1 WHERE `userid` = {$userid} AND `topic_id` = {$topic}");
		elseif ($rating == 'down')
			$db->query("UPDATE `forum_tops_rating` SET `rating` = -1 WHERE `userid` = {$userid} AND `topic_id` = {$topic}");
		elseif ($rating == 'none')
			$db->query("UPDATE `forum_tops_rating` SET `rating` = 0 WHERE `userid` = {$userid} AND `topic_id` = {$topic}");
	}
}

function getUserTopicRating($topic)
{
	global $db,$userid,$api;
	$q=$db->query("SELECT `rating` FROM `forum_tops_rating` WHERE `userid` = {$userid} AND `topic_id` = {$topic}");
	if ($db->num_rows($q) == 0)
		return 0;
	else
	{
		return $db->fetch_single($q);
	}
}

function replaceMentions($string)
{
	global $api;
    $mentionID=get_string_between($string, "[mention]", "[/mention]");
	if ($api->SystemUserIDtoName($mentionID))
	{
		$count = 1;
		while(strpos($string, $mentionID) != false)
		{
			$str = preg_replace("/{$mentionID}/", "<a href='profile.php?user={$mentionID}'>@{$api->SystemUserIDtoName($mentionID)}</a>", $string, 1);
		}
		return $str;
	}
	else
	{
		return $string;
	}
}

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}
$h->endpage();