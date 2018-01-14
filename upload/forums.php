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

echo "<h3><i class='far fa-comment-alt'></i> {$set['WebsiteName']} Forums</h3><hr />";
$fb = $db->fetch_row($db->query("SELECT * FROM `forum_bans` WHERE `fb_user` = {$userid}"));
if ($fb['fb_time'] > $time) {
    alert('danger', "Uh Oh!", "You are currently forum banned for the next " . TimeUntil_Parse($fb['fb_time']) . ". You
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
            "SELECT `ff_lp_time`, `ff_id`, `ff_name`, `ff_desc`,
					`ff_lp_t_id`,
                     `ff_lp_poster_id`
                     FROM `forum_forums`
                     WHERE `ff_auth` = 'public'
                     ORDER BY `ff_id` ASC");
    ?>
    <table class='table table-bordered table-hover'>
    <thead>
    <tr>
        <th>
            <?php echo "Category"; ?>
        </th>
        <th class='hidden-xs-down'>
            <?php echo "Posts"; ?>
        </th>
        <th class='hidden-xs-down'>
            <?php echo "Topics"; ?>
        </th>
        <th>
            <?php echo "Latest Post"; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <?php
    while ($r = $db->fetch_row($q)) {
        $t = DateTime_Parse($r['ff_lp_time'], true, true);
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
        echo "<tr>
					<td>
						<a href='?viewforum={$r['ff_id']}' style='font-weight: 800;'>{$r['ff_name']}</a>
						<small class='hidden-xs-down'><br />{$r['ff_desc']}</small>
					</td>
					<td class='hidden-xs-down'>
						{$posts}
					</td>
					<td class='hidden-xs-down'>
						{$topics}
					</td>
					<td>
						{$t}<br />
						In <a href='?viewtopic={$r['ff_lp_t_id']}&lastpost=1' style='font-weight: 800;'>{$topicname}</a><br />
						By <a href='profile.php?user={$r['ff_lp_poster_id']}'>{$username}</a>
					</td>
              </tr>";
    }
    echo "</table>";
    $db->free_result($q);
    if ($api->UserMemberLevelGet($userid, 'forum moderator')) {
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
        <table class='table table-bordered table-hover'>
        <thead>
        <tr>
            <th>
                <?php echo "Category"; ?>
            </th>
            <th class='hidden-xs-down'>
                <?php echo "Posts"; ?>
            </th>
            <th class='hidden-xs-down'>
                <?php echo "Topics"; ?>
            </th>
            <th>
                <?php echo "Latest Post"; ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php
        while ($r = $db->fetch_row($q)) {
            $t = DateTime_Parse($r['ff_lp_time'], true, true);
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
            echo "<tr>
        		<td>
        			<a href='?viewforum={$r['ff_id']}' style='font-weight: 800;'>{$r['ff_name']}</a>
        			<small class='hidden-xs-down'><br />{$r['ff_desc']}</small>
        		</td>
        		<td class='hidden-xs-down'>
					{$posts}
				</td>
        		<td class='hidden-xs-down'>
					{$topics}
				</td>
        		<td>
					{$t}<br />
					In <a href='?viewtopic={$r['ff_lp_t_id']}&lastpost=1' style='font-weight: 800;'>{$topicname}</a><br />
					By <a href='profile.php?user={$r['ff_lp_poster_id']}'>{$username}</a>
                </td>
              </tr>";
        }
        echo "</tbody></table>";
        $db->free_result($q);
    }
}

function viewforum()
{
    global $ir, $db, $h, $userid, $api;
    $_GET['viewforum'] = (isset($_GET['viewforum']) && is_numeric($_GET['viewforum'])) ? abs($_GET['viewforum']) : '';
    if (empty($_GET['viewforum'])) {
        alert('danger', "Uh Oh!", "You must enter a forum category you wish to view.", true, "forums.php");
        die($h->endpage());
    }
    $q =
        $db->query(
            "SELECT *
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
	if (permission("CanCreateThread",$userid))
		$ntl = "&nbsp;[<a href='?act=newtopicform&forum={$_GET['viewforum']}'>New Topic</a>]";
	else
		$ntl = "";
    echo "<ol class='breadcrumb'>
		<li class='breadcrumb-item'><a href='forums.php'>Forums Home</a></li>
		<li class='breadcrumb-item active'>{$r['ff_name']} {$ntl}</li>	
	</ol>";
    $posts_topic = $db->fetch_single($db->query("SELECT COUNT(`ft_id`) 
													FROM `forum_topics` 
													WHERE 
													`ft_forum_id` = {$_GET['viewforum']}"));
    $st = (isset($_GET['st']) && is_numeric($_GET['st'])) ? abs($_GET['st']) : 0;
    echo pagination(20, $posts_topic, $st, "?viewforum={$_GET['viewforum']}&amp;st=");
    ?>
    <table class='table table-bordered table-hover'>
    <thead>
    <tr>
        <th>
            <?php echo "Topic"; ?>
        </th>
        <th class='hidden-xs-down'>
            <?php echo "Post Count"; ?>
        </th>
        <th class='hidden-xs-down'>
            <?php echo "Created"; ?>
        </th>
        <th>
            <?php echo "Latest Post"; ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <?php
    $q =
        $db->query(
            "SELECT `ft_start_time`, `ft_last_time`, `ft_pinned`,
                     `ft_locked`, `ft_id`, `ft_name`, `ft_desc`, `ft_posts`,
                     `ft_owner_id`, `ft_last_id`
                     FROM `forum_topics`
                     WHERE `ft_forum_id` = {$_GET['viewforum']}
                     ORDER BY `ft_pinned` DESC, `ft_last_time` DESC
					 LIMIT {$st}, 20");
    while ($r2 = $db->fetch_row($q)) {
        $t1 = DateTime_Parse($r2['ft_start_time'], true, true);
        $t2 = DateTime_Parse($r2['ft_last_time'], true, true);
        $pt = ($r2['ft_pinned']) ? " <i class='fa fa-thumbtack' aria-hidden='true'></i>" : "" ;
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
        echo "<tr>
        		<td>
					{$pt} <a href='?viewtopic={$r2['ft_id']}&lastpost=1'>{$r2['ft_name']}</a> {$lt}<br />
					<small class='hidden-xs-down'>{$r2['ft_desc']}</small>
				</td>
				<td class='hidden-xs-down'>{$pc}</td>
				<td class='hidden-xs-down'> 
					{$t1}<br />
					By <a href='profile.php?user={$r2['ft_owner_id']}'>{$pn1['username']}</a>
                </td>
                <td>
					{$t2}<br />
                    By <a href='profile.php?user={$r2['ft_last_id']}'>{$pn2['username']}</a>
                </td>
              </tr>\n";
    }
    echo "</tbody></table>";
    echo pagination(20, $posts_topic, $st, "?viewforum={$_GET['viewforum']}&amp;st=");
    $db->free_result($q);
}

function viewtopic()
{
    global $ir, $userid, $parser, $db, $h, $api;
    $code = request_csrf_code('forum_reply');
    $precache = array();
    $_GET['viewtopic'] = (isset($_GET['viewtopic']) && is_numeric($_GET['viewtopic'])) ? abs($_GET['viewtopic']) : '';
    if (empty($_GET['viewtopic'])) {
        alert('danger', "Uh Oh!", "You must enter a topic you wish to view.", true, "forums.php");
        die($h->endpage());
    }
    $q =
        $db->query(
            "SELECT *
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
            "SELECT * FROM `forum_forums`
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
    echo pagination(20, $posts_topic, $st, "?viewtopic={$topic['ft_id']}&st=");
    if (!($ir['user_level'] == 'Member')) {
        $lock = ($topic['ft_locked'] == 0) ? 'Lock Topic' : 'Unlock Topic' ;
        $pin = ($topic['ft_pinned'] == 0) ? 'Pin Topic' : 'Unpin Topic' ;
        echo "
	<form action='?act=move&topic={$_GET['viewtopic']}' method='post'>
    <b>Move Topic To</b> " . forum_dropdown('forum')
            . "
	<input type='submit' value='Move Topic' class='btn btn-primary' />
	</form>
	<br />
	<center>
	<div class='hidden-sm-down'>
	<table>
		<tr>
			<td>
				<form>
					<input type='hidden' value='pin' name='act'>
					<input type='hidden' name='topic' value='{$_GET['viewtopic']}'>
					<input type='submit' class='btn btn-primary' value='{$pin}'>
				</form>
			</td>
			<td align='center'>
				<form>
					<input type='hidden' value='lock' name='act'>
					<input type='hidden' name='topic' value='{$_GET['viewtopic']}'>
					<input type='submit' class='btn btn-primary' value='{$lock}'>
				</form>
			</td>
			<td>
				<form action='?act=deletopic'>
					<input type='hidden' value='deletopic' name='act'>
					<input type='hidden' name='topic' value='{$_GET['viewtopic']}'>
					<input type='submit' class='btn btn-primary' value='Delete'>
				</form>
			</td>
		</tr>
	</table>
	</div>
	<div class='hidden-md-up'>
		<form>
			<input type='hidden' value='pin' name='act'>
			<input type='hidden' name='topic' value='{$_GET['viewtopic']}'>
			<input type='submit' class='btn btn-primary' value='{$pin}'>
		</form>
		<form>
			<input type='hidden' value='lock' name='act'>
			<input type='hidden' name='topic' value='{$_GET['viewtopic']}'>
			<input type='submit' class='btn btn-primary' value='{$lock}'>
		</form>
		<form action='?act=deletopic'>
			<input type='hidden' value='deletopic' name='act'>
			<input type='hidden' name='topic' value='{$_GET['viewtopic']}'>
			<input type='submit' class='btn btn-primary' value='Delete'>
		</form>
	</div><br /> ";
    }
    echo "<table class='table table-bordered'>";
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

        $qlink = "[<a href='?act=quote&viewtopic={$_GET['viewtopic']}&quotename={$r['fp_poster_id']}&fpid={$r['fp_id']}'>Quote</a>]";
        if ($api->UserMemberLevelGet($userid, 'forum moderator') || $userid == $r['fp_poster_id']) {
            $elink =
                "[<a href='?act=edit&post={$r['fp_id']}&topic={$_GET['viewtopic']}'>Edit</a>]";
        } else {
            $elink = "";
        }
        $no++;
        if ($no > 1 and ($api->UserMemberLevelGet($userid, 'forum moderator'))) {
            $dlink =
                "[<a href='?act=delepost&post={$r['fp_id']}'>Delete</a>]";
        } else {
            $dlink = "";
        }
        if ($api->UserMemberLevelGet($userid, 'forum moderator')) {
            $wlink = "[<a href='staff/staff_punish.php?action=forumwarn&user={$r['fp_poster_id']}'>Warn</a>]";
            $blink = "[<a href='staff/staff_punish.php?action=forumban&user={$r['fp_poster_id']}'>Forum Ban</a>]";
        } else {
            $wlink = "";
            $blink = "";
        }
        $t = DateTime_Parse($r['fp_time']);
        $editornameq = $db->query("SELECT `username` FROM `users` WHERE `userid` = {$r['fp_editor_id']} LIMIT 1");
        $editorname = $db->fetch_single($editornameq);
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
            if ($memb['display_pic']) {
                $av = "<img src='{$memb['display_pic']}' class='img-fluid'>";
            } else {
                $av = "";
            }
            $memb['signature'] = $parser->parse($memb['signature']);
            $memb['signature'] = $parser->getAsHtml($memb['signature']);
        }
        $parser->parse($r['fp_text']);
        $r['fp_text'] = $parser->getAsHtml();
        echo "<tr>
				<th width='25%'>Post #{$no}</th>
				<th>
				Posted {$t} {$qlink} {$elink} {$dlink} {$wlink} {$blink}
				</th>
			 </tr>
			 <tr>
				<td valign='top'>";
        if ($memb['userid'] > 0) {
            $userpostsq = $db->query("SELECT COUNT('fp_id') FROM `forum_posts` WHERE `fp_poster_id`={$r['fp_poster_id']}");
            $userposts = $db->fetch_single($userpostsq);

            $usertopicsq = $db->query("SELECT COUNT('ft_id') FROM `forum_topics` WHERE `ft_owner_id`={$r['fp_poster_id']}");
            $usertopics = $db->fetch_single($usertopicsq);
            print
                "<div class='hidden-xs-down'>{$av}</div><a href='profile.php?user={$r['fp_poster_id']}'>{$PN['username']}</a>
                    	[{$r['fp_poster_id']}]<br />
                     <b>Rank:</b> {$memb['user_level']}<br />
					 <b>Post Count:</b> {$userposts}<br />
					 <b>Topic Count:</b> {$usertopics}<br />";
        } else {
            print "<b>Non-existent User</b>";
        }
        print
            "</td>
			   	 <td>
                    {$r['fp_text']}
                    {$edittext}<br />
					<hr />
                    {$memb['signature']}
                 </td>
		</tr>";
    }
    $db->free_result($q3);
    echo "</table>";
    echo pagination(20, $posts_topic, $st, "?viewtopic={$topic['ft_id']}&st=");
    if ($topic['ft_locked'] == 0) {
		if (permission("CanReplyForum",$userid))
		{
			echo "<br />
			<br />
			<form action='?reply={$topic['ft_id']}' method='post'>
				<table class='table'>
					<tr>
						<th>
							Post Response
							</th>
						<td>
							<textarea class='form-control' rows='5' cols='40' id='post' placeholder='You can use BBCode.' name='fp_text' required></textarea>
						</td>
					</tr>
					<tr>
						<td colspan='2'> 
							<input type='submit' value='Submit Reply' class='btn btn-primary'>
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
            "SELECT `ft_forum_id`, `ft_locked`, `ft_name`, `ft_id`
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
            "SELECT *
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
        $_POST['fp_text'] = $db->escape(str_replace("\n", "<br />", strip_tags(stripslashes($_POST['fp_text']))));
        if ((strlen($_POST['fp_text']) > 65535)) {
            alert('danger', "Uh Oh!", "Forum replies can only be, at maximum, 65,535 characters in length.", true, "forums.php?viewtopic={$_GET['reply']}");
            die($h->endpage());
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
		$toq1=$db->fetch_single($db->query("SELECT `ft_owner_id` FROM `forum_topics` WHERE `ft_id` = {$_GET['reply']}"));
		$topicname=$db->fetch_single($db->query("SELECT `ft_name` FROM `forum_topics` WHERE `ft_id` = {$_GET['reply']}"));
		$toq2=$db->fetch_single($db->query("SELECT `forum_alert` FROM `user_settings` WHERE `userid` = {$toq1}"));
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
            "SELECT *
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
				<textarea rows='8' class='form-control' cols='45' placeholder='You can use BBCode!' name='fp_text' id='fp_text' required></textarea>
			</td>
		</tr>
		<tr>
			<td align='center' colspan='2'>
				<input type='submit' class='btn btn-lg btn-primary' value='Post Topic' />
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
            "SELECT *
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
            "SELECT *
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
			<tr>
				<th>
					<label for='fp_text'>Reply</label>
				</th>
				<td>
					<textarea rows='8' class='form-control' cols='45' name='fp_text' id='fp_text' required>[quote=\"{$Who}\"]{$text}[/quote]</textarea>
				</td>
			</tr>
			<tr>
				<td colspan='2' align='center'>
					<input type='submit' class='btn btn-lg btn-primary' value='Submit Reply' />
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
            "SELECT *
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
    $fp_text = strip_tags(stripslashes($post['fp_text']));
    echo <<<EOF
<form action='?act=editsub&topic={$topic['ft_id']}&post={$_GET['post']}' method='post'>
<input type='hidden' name='verf' value='{$edit_csrf}' />
    <table class='table'>
        <tr>
        	<th>
			Editing a post
			</th>
        	<td>
        		<textarea rows='7' class='form-control' cols='40' name='fp_text'>{$fp_text}</textarea>
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
            "SELECT *
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
    if (!(($api->UserMemberLevelGet($userid, 'forum moderator')) || $ir['userid'] == $post['fp_poster_id'])) {
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
	$db->query(
            "UPDATE `forum_posts`
             SET `ff_id` = {$_POST['forum']}
             WHERE `fp_topic_id` = {$_GET['topic']}");
    alert('success', "Success!", "Topic was moved successfully.", true, "forums.php?viewtopic={$_GET['topic']}");
    $api->SystemLogsAdd($userid, 'staff', "Moved Topic {$topic['ft_name']} to {$forum['ff_name']}");
    recache_forum($topic['ft_forum_id']);
    recache_forum($_POST['forum']);
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
        $api->SystemLogsAdd($userid, 'staff', "Unlocked Topic {$r['ft_name']}");
    } else {
        $db->query(
            "UPDATE `forum_topics`
                 SET `ft_locked` = 1
                 WHERE `ft_id` = {$_GET['topic']}");
        alert('success', "Success!", "You have locked this topic.", false);
        $api->SystemLogsAdd($userid, 'staff', "Locked Topic {$r['ft_name']}");
    }
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
        $api->SystemLogsAdd($userid, 'staff', "Unpinned Topic {$r['ft_name']}");
    } else {
        $db->query(
            "UPDATE `forum_topics`
                 SET `ft_pinned` = 1
                 WHERE `ft_id` = {$_GET['topic']}");
        alert('success', "Success!", "You have pinned this topic.", true, "forums.php?viewtopic={$r['ft_id']}");
        $api->SystemLogsAdd($userid, 'staff', "Pinned Topic {$r['ft_name']}");
    }
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
    recache_topic($post['fp_topic_id']);
    recache_forum($post['ff_id']);
    $api->SystemLogsAdd($userid, 'staff', "Deleted post ({$post['fp_id']}) in {$topic['ft_name']}");

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
    recache_forum($topic['ft_forum_id']);
    $api->SystemLogsAdd($userid, 'staff', "Deleted topic {$topic['ft_name']}");
}

$h->endpage();