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
function csrf_error($goBackTo)
{
    global $h,$lang;
	echo "<div class='alert alert-danger'> <strong>{$lang['CSRF_ERROR_TITLE']}</strong> 
	{$lang['CSRF_ERROR_TEXT']} {$lang['CSRF_PREF_MENU']} <a href='{$goBackTo}'>{$lang['GEN_HERE']}</a>.</div>";
    $h->endpage();
    exit;
}
echo "<h3>{$set['WebsiteName']} {$lang['FORUM_FORUMS']}</h3><hr />";
$fb=$db->fetch_row($db->query("SELECT * FROM `forum_bans` WHERE `fb_user` = {$userid}"));
if ($fb['fb_time'] > $time)
{
	 alert('danger',$lang['ERROR_GENERIC'],"{$lang['FORUM_BAN_INFO']} " . TimeUntil_Parse($fb['fb_time']) . " {$lang['FORUM_BAN_INFO1']} {$fb['fb_reason']}. {$lang['FORUM_BAN_INFO2']}",true,'index.php');
	 die($h->endpage());
}
if (!isset($_GET['act']))
{
    $_GET['act'] = '';
}
if (isset($_GET['viewtopic']) && $_GET['act'] != 'quote')
{
    $_GET['act'] = 'viewtopic';
}
if (isset($_GET['viewforum']))
{
    $_GET['act'] = 'viewforum';
}
if (isset($_GET['reply']))
{
    $_GET['act'] = 'reply';
}
if (isset($_GET['empty']) && $_GET['empty'] == 1 && isset($_GET['code'])
        && $_GET['code'] === 'kill' && isset($_SESSION['owner'])
        && $_SESSION['owner'] > 0)
{
    emptyallforums();
}
switch ($_GET['act'])
{
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
    if (isset($_GET['forum']))
    {
        recache_forum($_GET['forum']);
    }
    break;
default:
    idx();
    break;
}		
function idx()
{
    global $ir, $c, $userid, $lang, $db, $api;
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
                <?php echo $lang['FORUM_F_FN']; ?>
            </th>
            <th class='hidden-xs'>
                <?php echo $lang['FORUM_F_PC']; ?>
            </th>
            <th class='hidden-xs'>
                <?php echo $lang['FORUM_F_TC']; ?>
            </th>
            <th>
                <?php echo $lang['FORUM_F_LP']; ?>
            </th>
        </tr>
    </thead>
    <tbody>
	<?php
    while ($r = $db->fetch_row($q))
    {
        $t = DateTime_Parse($r['ff_lp_time'], false, true);
		$pnq=$db->query("SELECT `username`,`vip_days` FROM `users` WHERE `userid` = {$r['ff_lp_poster_id']}");
		$pn=$db->fetch_row($pnq);
		$username = ($pn['vip_days']) ? "<span style='color:red; font-weight:bold;'>{$pn['username']} <span class='glyphicon glyphicon-star' data-toggle='tooltip' title='{$pn['username']} has {$pn['vip_days']} VIP Days remaining.'></span></span>" : $pn['username'];
		$topicsq=$db->query("SELECT COUNT('ft_id') FROM `forum_topics` WHERE `ft_forum_id`={$r['ff_id']}");
			
		$postsq=$db->query("SELECT COUNT('fp_id') FROM `forum_posts` WHERE `ff_id`={$r['ff_id']}");
		$posts=$db->fetch_single($postsq);
		$topics=$db->fetch_single($topicsq);
		
		$topicname=$db->fetch_single($db->query("SELECT `ft_name` FROM `forum_topics` WHERE `ft_forum_id` = {$r['ff_id']} ORDER BY `ft_last_time` DESC"));
         echo "<tr>
					<td>
						<a href='?viewforum={$r['ff_id']}' style='font-weight: 800;'>{$r['ff_name']}</a>
						<small class='hidden-xs'><br />{$r['ff_desc']}</small>
					</td>
					<td class='hidden-xs'>
						{$posts}
					</td>
					<td class='hidden-xs'>
						{$topics}
					</td>
					<td>
						{$t}<br />
						{$lang['FORUM_IN']} <a href='?viewtopic={$r['ff_lp_t_id']}&lastpost=1' style='font-weight: 800;'>{$topicname}</a><br />
						{$lang['FORUM_BY']} <a href='profile.php?user={$r['ff_lp_poster_id']}'>{$username}</a>
					</td>
              </tr>";
    }
    echo "</table>";
    $db->free_result($q);
    if (($ir['user_level'] == 'Admin') || ($ir['user_level'] == 'Forum Moderator') || ($ir['user_level'] == 'Web Developer'))
    {
        echo "<hr /><h3>{$lang['FORUM_STAFFONLY']} {$lang['FORUM_FORUMS']}</h3><hr />";
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
                <?php echo $lang['FORUM_F_FN']; ?>
            </th>
            <th class='hidden-xs'>
                <?php echo $lang['FORUM_F_PC']; ?>
            </th>
            <th class='hidden-xs'>
                <?php echo $lang['FORUM_F_TC']; ?>
            </th>
            <th>
                <?php echo $lang['FORUM_F_LP']; ?>
            </th>
        </tr>
    </thead>
    <tbody>
	<?php
        while ($r = $db->fetch_row($q))
        {
            $t = DateTime_Parse($r['ff_lp_time'], false, true);
			$pnq=$db->query("SELECT `username`,`vip_days` FROM `users` WHERE `userid` = {$r['ff_lp_poster_id']}");
			$pn=$db->fetch_row($pnq);
			$username = ($pn['vip_days']) ? "<span style='color:red; font-weight:bold;'>{$pn['username']} <span class='glyphicon glyphicon-star' data-toggle='tooltip' title='{$pn['username']} has {$pn['vip_days']} VIP Days remaining.'></span></span>" : $pn['username'];
			
			$topicsq=$db->query("SELECT COUNT('ft_id') FROM `forum_topics` WHERE `ft_forum_id`={$r['ff_id']}");
				
			$postsq=$db->query("SELECT COUNT('fp_id') FROM `forum_posts` WHERE `ff_id`={$r['ff_id']}");
			$posts=$db->fetch_single($postsq);
			$topics=$db->fetch_single($topicsq);
			
			$topicname=$db->fetch_single($db->query("SELECT `ft_name` FROM `forum_topics` WHERE `ft_forum_id` = {$r['ff_id']} ORDER BY `ft_last_time` DESC"));
        echo "<tr>
        		<td>
        			<a href='?viewforum={$r['ff_id']}' style='font-weight: 800;'>{$r['ff_name']}</a>
        			<small class='hidden-xs'><br />{$r['ff_desc']}</small>
        		</td>
        		<td class='hidden-xs'>
					{$posts}
				</td>
        		<td class='hidden-xs'>
					{$topics}
				</td>
        		<td>
					{$lang['FORUM_ON']} {$t}<br />
					{$lang['FORUM_IN']} <a href='?viewtopic={$r['ff_lp_t_id']}&lastpost=1' style='font-weight: 800;'>{$topicname}</a><br />
					{$lang['FORUM_BY']} <a href='profile.php?user={$r['ff_lp_poster_id']}'>{$username}</a>
                </td>
              </tr>";
        }
        echo "</tbody></table>";
        $db->free_result($q);
    }
}		
function viewforum()
{
    global $ir, $userid, $lang, $db, $h, $api;
    $_GET['viewforum'] = (isset($_GET['viewforum']) && is_numeric($_GET['viewforum'])) ? abs($_GET['viewforum']) : '';
    if (empty($_GET['viewforum']))
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['ERROR_FORUM_VF'],true,"forums.php");
       die($h->endpage());
    }
    $q =
            $db->query(
                    "SELECT `ff_auth`, `ff_name`
                     FROM `forum_forums`
                     WHERE `ff_id` = '{$_GET['viewforum']}'");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        alert('danger',$lang['FORUM_FORUM_DNE_TITLE'],$lang['FORUM_FORUM_DNE_TEXT'],true,"forums.php");
		die($h->endpage());
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if ($r['ff_auth'] == 'staff')
    {
		if (!in_array($ir['user_level'], array('Admin', 'Forum Moderator', 'Web Developer')))
		{ 
			alert('danger',$lang['ERROR_SECURITY'],$lang['FORUM_NOPERMISSION'],true,"forums.php");
			die($h->endpage());
		}
    }
    $ntl = "&nbsp;[<a href='?act=newtopicform&forum={$_GET['viewforum']}'>New Topic</a>]";
    echo "<big>
    	   <a href='forums.php'>{$lang['FORUM_FORUMSHOME']}</a>
    	   <i class='fa fa-chevron-right' aria-hidden='true'></i> <a href='?viewforum={$_GET['viewforum']}'>{$r['ff_name']}</a>$ntl
    	  </big><br /><br />";
		  ?>
	<table class='table table-bordered table-hover'>
    <thead>
        <tr>
            <th>
                <?php echo $lang['FORUM_TOPICNAME']; ?>
            </th>
            <th class='hidden-xs'>
                <?php echo $lang['FORUM_F_PC']; ?>
            </th>
            <th class='hidden-xs'>
                <?php echo $lang['FORUM_TOPICOPEN']; ?>
            </th>
            <th>
                <?php echo $lang['FORUM_F_LP']; ?>
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
                     ORDER BY `ft_pinned` DESC, `ft_last_time` DESC");
    while ($r2 = $db->fetch_row($q))
    {
        $t1 = DateTime_Parse($r2['ft_start_time']);
        $t2 = DateTime_Parse($r2['ft_last_time']);
        if ($r2['ft_pinned'])
        {
            $pt = "<span class='glyphicon glyphicon-pushpin'></span> ";
        }
        else
        {
            $pt = "";
        }
        if ($r2['ft_locked'])
        {
            $lt = "<span class='glyphicon glyphicon-lock'></span> ";
        }
        else
        {
            $lt = "";
        }
		$pnq1=$db->query("SELECT `username`,`vip_days` FROM `users` WHERE `userid` = {$r2['ft_owner_id']}");
		$pn1=$db->fetch_row($pnq1);
		$pn1['username'] = ($pn1['vip_days']) ? "<span style='color:red; font-weight:bold;'>{$pn1['username']} <span class='glyphicon glyphicon-star' data-toggle='tooltip' title='{$pn1['username']} has {$pn1['vip_days']} VIP Days remaining.'></span></span>" : $pn1['username'];
		$pnq2=$db->query("SELECT `username`,`vip_days` FROM `users` WHERE `userid` = {$r2['ft_last_id']}");
		$pn2=$db->fetch_row($pnq2);
		$pn2['username'] = ($pn2['vip_days']) ? "<span style='color:red; font-weight:bold;'>{$pn2['username']} <span class='glyphicon glyphicon-star' data-toggle='tooltip' title='{$pn2['username']} has {$pn2['vip_days']} VIP Days remaining.'></span></span>" : $pn2['username'];
		$pcq=$db->query("SELECT COUNT(`fp_id`) FROM `forum_posts` WHERE `fp_topic_id` = {$r2['ft_id']}");
		$pc=$db->fetch_single($pcq);
		if (!$pn2)
		{
			$pn2['username']="{$lang['GEN_NEU']}";
		}
		if (!$pn1)
		{
			$pn1['username']="{$lang['GEN_NEU']}";
		}
        echo "<tr>
        		<td>
					{$pt} <a href='?viewtopic={$r2['ft_id']}&lastpost=1'>{$r2['ft_name']}</a> {$lt}<br />
					<small class='hidden-xs'>{$r2['ft_desc']}</small>
				</td>
				<td class='hidden-xs'>{$pc}</td>
				<td class='hidden-xs'> 
					{$t1}<br />
					{$lang['FORUM_BY']} <a href='profile.php?user={$r2['ft_owner_id']}'>{$pn1['username']}</a>
                </td>
                <td>
					{$t2}<br />
                    {$lang['FORUM_BY']} <a href='profile.php?user={$r2['ft_last_id']}'>{$pn2['username']}</a>
                </td>
              </tr>\n";
    }
    echo "</tbody></table>";
    $db->free_result($q);
}

function viewtopic()
{
    global $ir, $userid, $parser, $lang, $db, $h, $api;
	$code = request_csrf_code('forum_reply');
    $precache = array();
    $_GET['viewtopic'] = (isset($_GET['viewtopic']) && is_numeric($_GET['viewtopic'])) ? abs($_GET['viewtopic']) : '';
    if (empty($_GET['viewtopic']))
    {
       alert('danger',$lang['ERROR_GENERIC'],$lang['ERROR_FORUM_VF'],true,"forums.php");
       die($h->endpage());
    }
    $q =
            $db->query(
                    "SELECT `ft_forum_id`, `ft_name`, `ft_posts`, `ft_id`,
                    `ft_locked`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$_GET['viewtopic']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        alert('danger',$lang['FORUM_TOPIC_DNE_TITLE'],$lang['FORUM_TOPIC_DNE_TEXT'],true,"forums.php");
		die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $q2 =
            $db->query(
                    "SELECT `ff_auth`, `ff_id`, `ff_name`
                    FROM `forum_forums`
                    WHERE `ff_id` = {$topic['ft_forum_id']}");
    if ($db->num_rows($q2) == 0)
    {
        $db->free_result($q2);
        alert('danger',$lang['FORUM_FORUM_DNE_TITLE'],$lang['FORUM_FORUM_DNE_TEXT'],true,"forums.php?viewforum={$topic['ft_forum_id']}");
		die($h->endpage());
    }
    $forum = $db->fetch_row($q2);
    $db->free_result($q2);
    if ($forum['ff_auth'] == 'staff')
    {
		if ($api->UserMemberLevelGet($userid,'forum moderator') == false)
		{  
			alert('danger',$lang['ERROR_SECURITY'],$lang['FORUM_NOPERMISSION'],true,"forums.php");
			die($h->endpage());
		}
    }
    echo "<big>
    		<a href='forums.php'>{$lang['FORUM_FORUMSHOME']}</a>
    		<i class='fa fa-chevron-right' aria-hidden='true'></i> <a href='?viewforum={$forum['ff_id']}'>{$forum['ff_name']}</a>
    		<i class='fa fa-chevron-right' aria-hidden='true'></i> <a href='?viewtopic={$_GET['viewtopic']}'>{$topic['ft_name']}</a>
    	  </big>
    	  <br /><br />";
    $posts_per_page = 20;
    $posts_topic = $topic['ft_posts'];
    $pages = ceil($posts_topic / $posts_per_page);
    $st = (isset($_GET['st']) && is_numeric($_GET['st'])) ? abs($_GET['st']) : 0;
    if (isset($_GET['lastpost']))
    {
        if ($pages == 0)
		{
			$st=($pages)*20;
		}
		else
		{
			$st = ($pages - 1) * 20;
		}
    }
    $pst = -20;
	echo "<center><ul class='pagination'>{$lang['FORUM_PAGES']}<br /> ";
    for ($i = 1; $i <= $pages; $i++)
    {
        $pst += 20;
        if ($pst == $st)
        {
            echo "<li class='active'><a href='?viewtopic={$topic['ft_id']}&amp;st=$pst'>";
        }
		else
		{
			echo "<li><a href='?viewtopic={$topic['ft_id']}&amp;st=$pst'>";
		}
        echo $i;
        echo "</li></a>&nbsp;";
        if ($i % 25 == 0)
        {
            echo "</ul><br />";
        }
    }
    echo "</center>";
    if (!($ir['user_level'] == 'Member'))
    {
        echo "
	<form action='?act=move&topic={$_GET['viewtopic']}' method='post'>
    <b>{$lang['FORUM_TOPIC_MTT']}</b> " . forum_dropdown('forum')
                . "
	<input type='submit' value='{$lang['FORUM_TOPIC_MOVE']}' class='btn btn-default' />
	</form>
	<br />
	<center>
	<div class='hidden-xs'>
	<table>
		<tr>
			<td>
				<form>
					<input type='hidden' value='pin' name='act'>
					<input type='hidden' name='topic' value='{$_GET['viewtopic']}'>
					<input type='submit' class='btn btn-default' value='{$lang['FORUM_TOPIC_PIN']}'>
				</form>
			</td>
			<td align='center'>
				<form>
					<input type='hidden' value='lock' name='act'>
					<input type='hidden' name='topic' value='{$_GET['viewtopic']}'>
					<input type='submit' class='btn btn-default' value='{$lang['FORUM_TOPIC_LOCK']}'>
				</form>
			</td>
			<td>
				<form action='?act=deletopic'>
					<input type='hidden' value='deletopic' name='act'>
					<input type='hidden' name='topic' value='{$_GET['viewtopic']}'>
					<input type='submit' class='btn btn-default' value='{$lang['FORUM_TOPIC_DELETE']}'>
				</form>
			</td>
		</tr>
	</table>
	</div>
	<div class='hidden-md-up'>
		<form>
			<input type='hidden' value='pin' name='act'>
			<input type='hidden' name='topic' value='{$_GET['viewtopic']}'>
			<input type='submit' class='btn btn-default' value='{$lang['FORUM_TOPIC_PIN']}'>
		</form>
		<form>
			<input type='hidden' value='lock' name='act'>
			<input type='hidden' name='topic' value='{$_GET['viewtopic']}'>
			<input type='submit' class='btn btn-default' value='{$lang['FORUM_TOPIC_LOCK']}'>
		</form>
		<form action='?act=deletopic'>
			<input type='hidden' value='deletopic' name='act'>
			<input type='hidden' name='topic' value='{$_GET['viewtopic']}'>
			<input type='submit' class='btn btn-default' value='{$lang['FORUM_TOPIC_DELETE']}'>
		</form>
	</div>
	</center><br />
            ";
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
    while ($r = $db->fetch_row($q3))
    {
		$PNQ=$db->query("SELECT `username`,`vip_days` FROM `users` WHERE `userid`={$r['fp_poster_id']}");
		$PN=$db->fetch_row($PNQ);
		$PN['username'] = ($PN['vip_days']) ? "<span style='color:red; font-weight:bold;'>{$PN['username']} <span class='glyphicon glyphicon-star' data-toggle='tooltip' title='{$PN['username']} has {$PN['vip_days']} VIP Days remaining.'></span></span>" : $PN['username'];

		$qlink = "[<a href='?act=quote&viewtopic={$_GET['viewtopic']}&quotename={$r['fp_poster_id']}&fpid={$r['fp_id']}'>{$lang['FORUM_POST_QUOTE']}</a>]";
        if ($api->UserMemberLevelGet($userid,'forum moderator') || $userid == $r['fp_poster_id'])
        {
            $elink =
                    "[<a href='?act=edit&post={$r['fp_id']}&topic={$_GET['viewtopic']}'>{$lang['FORUM_POST_EDIT']}</a>]";
        }
        else
        {
            $elink = "";
        }
        $no++;
        if ($no > 1 and ($api->UserMemberLevelGet($userid,'forum moderator')))
        {
            $dlink =
                    "[<a href='?act=delepost&post={$r['fp_id']}'>{$lang['FORUM_POST_DELETE']}</a>]";
        }
        else
        {
            $dlink = "";
        }
		if ($api->UserMemberLevelGet($userid,'forum moderator'))
		{
			$wlink="[<a href='staff/staff_punish.php?action=forumwarn&user={$r['fp_poster_id']}'>{$lang['FORUM_POST_WARN']}</a>]";
			$blink="[<a href='staff/staff_punish.php?action=forumban&user={$r['fp_poster_id']}'>{$lang['FORUM_POST_BAN']}</a>]";
		}
		else
		{
			$wlink="";
			$blink="";
		}
        $t = DateTime_Parse($r['fp_time']);
		$editornameq=$db->query("SELECT `username` FROM `users` WHERE `userid` = {$r['fp_editor_id']} LIMIT 1");
		$editorname=$db->fetch_single($editornameq);
        if ($r['fp_edit_count'] > 0)
        {
            $edittext =
                    "\n<br /><small><i>{$lang['FORUM_POST_EDIT_1']} <a href='viewuser.php?u={$r['fp_editor_id']}'>{$editorname}</a> {$lang['GEN_AT']} "
                            . DateTime_Parse($r['fp_editor_time'])
                            . ", {$lang['GEN_EDITED']} <b>{$r['fp_edit_count']}</b> {$lang['GEN_TIMES']}</i></small>";
        }
        else
        {
            $edittext = "";
        }
        if (!isset($precache[$r['fp_poster_id']]))
        {
            $membq =
                    $db->query(
                            "SELECT `userid`,
                            `user_level`,`username`,`display_pic`, `signature`
                             FROM `users`
                             WHERE `userid` = {$r['fp_poster_id']}");
            if ($db->num_rows($membq) == 0)
            {
                $memb = array('userid' => 0, 'signature' => '');
            }
            else
            {
                $memb = $db->fetch_row($membq);
            }
            $db->free_result($membq);
            $precache[$memb['userid']] = $memb;
        }
        else
        {
            $memb = $precache[$r['fp_poster_id']];
        }
        if ($memb['userid'] > 0)
        {
			if ($memb['display_pic'])
			{
				$av="<center><img src='{$memb['display_pic']}' class='img-responsive' width='75'></center>";
			}
			else
			{
				$av="";
			}
			$memb['signature'] = $parser->parse($memb['signature']);
			$memb['signature'] = $parser->getAsHtml($memb['signature']);
        }
		$parser->parse($r['fp_text']);
        $r['fp_text']=$parser->getAsHtml();
        echo "<tr>
				<th width='25%'>{$lang['FORUM_POST_POST']} #{$no}</th>
				<th>
				{$lang['FORUM_POST_POSTED']} {$t} {$qlink} {$elink} {$dlink} {$wlink} {$blink}
				</th>
			 </tr>
			 <tr>
				<td valign='top'>";
        if ($memb['userid'] > 0)
        {
			$userpostsq=$db->query("SELECT COUNT('fp_id') FROM `forum_posts` WHERE `fp_poster_id`={$r['fp_poster_id']}");
			$userposts=$db->fetch_single($userpostsq);
			
			$usertopicsq=$db->query("SELECT COUNT('ft_id') FROM `forum_topics` WHERE `ft_owner_id`={$r['fp_poster_id']}");
			$usertopics=$db->fetch_single($usertopicsq);
            print
                    "<div class='hidden-xs'>{$av}</div><a href='profile.php?user={$r['fp_poster_id']}'>{$PN['username']}</a>
                    	[{$r['fp_poster_id']}]<br />
                     <b>{$lang['GEN_RANK']}:</b> {$memb['user_level']}<br />
					 <b>{$lang['FORUM_F_PC']}:</b> {$userposts}<br />
					 <b>{$lang['FORUM_F_TC']}:</b> {$usertopics}<br />";
        }
        else
        {
            print "<b>{$lang['GEN_NEU']}</b>";
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
    $pst = -20;
    echo "<center><ul class='pagination'>{$lang['FORUM_PAGES']}<br /> ";
    for ($i = 1; $i <= $pages; $i++)
    {
        $pst += 20;
        if ($pst == $st)
        {
            echo "<li class='active'><a href='?viewtopic={$topic['ft_id']}&st=$pst'>";
        }
		else
		{
			echo "<li><a href='?viewtopic={$topic['ft_id']}&st=$pst'>";
		}
        echo $i;
        echo "</li></a>&nbsp;";
        if ($i % 25 == 0)
        {
            echo "<br /></center>";
        }
    }
	echo"</ul>";
	if ($topic['ft_locked'] == 0)
	{
		echo"<br />
		<br />
		<form action='?reply={$topic['ft_id']}' method='post'>
			<table class='table table-responsive'>
				<tr>
					<th>
						{$lang['FORUM_POST_REPLY']}
						</th>
					<td>
						<textarea class='form-control' rows='5' cols='40' id='post' placeholder='{$lang['FORUM_POST_REPLY_INFO']}' name='fp_text' required></textarea>
					</td>
				</tr>
				<tr>
					<td colspan='2'> 
						<center><input type='submit' value='{$lang['FORUM_POST_REPLY2']}' class='btn btn-default'></center>
					</td>
				</tr>
			</table>
			<input type='hidden' name='verf' value='{$code}' />
		</form>
		";
	}
	else
	{
		echo "<br />
		<br />
		<i>{$lang['FORUM_POST_TIL']}</i>";
	}
}
function reply()
{
    global $ir, $userid, $lang, $db, $api;
    $_GET['reply'] = (isset($_GET['reply']) && is_numeric($_GET['reply'])) ? abs($_GET['reply']) : '';
	if (!isset($_POST['verf']) || !verify_csrf_code('forum_reply', stripslashes($_POST['verf'])))
	{
		csrf_error("?viewtopic={$_GET['reply']}");
	}
	if (empty($_GET['reply']))
    {
        alert('danger',$lang['ERROR_EMPTY'],$lang['FORUM_EMPTY_REPLY'],true,"forums.php");
        die($h->endpage());
    }
    $q =
            $db->query(
                    "SELECT `ft_forum_id`, `ft_locked`, `ft_name`, `ft_id`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$_GET['reply']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        alert('danger',$lang['FORUM_TOPIC_DNE_TITLE'],$lang['FORUM_TOPIC_DNE_TEXT'],true,"forums.php");
        die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $q2 =
            $db->query(
                    "SELECT `ff_auth`, `ff_id`
                     FROM `forum_forums`
                     WHERE `ff_id` = {$topic['ft_forum_id']}");
    if ($db->num_rows($q2) == 0)
    {
        $db->free_result($q2);
        alert('danger',$lang['FORUM_FORUM_DNE_TITLE'],$lang['FORUM_FORUM_DNE_TEXT'],true,"forums.php");
        die($h->endpage());
    }
    $forum = $db->fetch_row($q2);
    $db->free_result($q2);
    if ($forum['ff_auth'] == 'staff')
    {
		if ($api->UserMemberLevelGet($userid,'forum moderator') == false)
		{  
			alert('danger',$lang['ERROR_SECURITY'],$lang['FORUM_NOPERMISSION'],true,"forums.php");
			die($h->endpage());
		}
    }
    if ($topic['ft_locked'] == 0)
    {
		$_POST['fp_text'] = $db->escape(str_replace("\n", "<br />",strip_tags(stripslashes($_POST['fp_text']))));
        if ((strlen($_POST['fp_text']) > 65535))
        {
			alert('danger',$lang['ERROR_LENGTH'],$lang['FORUM_MAX_CHAR_REPLY'],true,"forums.php?viewtopic={$_GET['reply']}");
            die($h->endpage());
        }
		
		$last_name = $db->escape($topic['ft_name']);
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
        alert('success',"","{$lang['FORUM_REPLY_SUCCESS']}",false);
		echo "<br />";
        $_GET['lastpost'] = 1;
        $_GET['viewtopic'] = $_GET['reply'];
        viewtopic();
    }
    else
    {
        echo $lang['FORUM_POST_TIL'];
    }
}
function newtopicform()
{
    global $ir, $userid, $lang, $h, $db, $api;
    $_GET['forum'] = (isset($_GET['forum']) && is_numeric($_GET['forum'])) ? abs($_GET['forum']) : '';
    if (empty($_GET['forum']))
    {
        alert('danger',"{$lang['ERROR_GENERIC']}","",true,"forums.php");
        die($h->endpage());
    }
    $q =
            $db->query(
                    "SELECT `ff_auth`, `ff_name`
                     FROM `forum_forums`
                     WHERE `ff_id` = '{$_GET['forum']}'");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        alert('danger',$lang['FORUM_TOPIC_DNE_TITLE'],$lang['FORUM_TOPIC_DNE_TEXT'],true,"forums.php?viewforum={$_GET['forum']}");
		die($h->endpage());
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if ($r['ff_auth'] == 'staff')
    {
		if ($api->UserMemberLevelGet($userid,'forum moderator') == false)
		{  
			alert('danger',$lang['ERROR_SECURITY'],$lang['FORUM_NOPERMISSION'],true,"forums.php");
			die($h->endpage());
		}
    }
    $code = request_csrf_code("forums_newtopic_{$_GET['forum']}");
    echo <<<EOF
	<big>
    		<a href='forums.php'>{$lang['FORUM_FORUMSHOME']}</a>
    		<span class='glyphicon glyphicon-chevron-right'></span> <a href='?viewforum={$_GET['forum']}'>{$r['ff_name']}</a>
    		<span class='glyphicon glyphicon-chevron-right'></span> {$lang['FORUM_TOPIC_FORM_PAGE']}
    	  </big>
<form method='post' action='?act=newtopic&forum={$_GET['forum']}'>
	<table class='table'>
		<tr>
			<th>
				<label for='ft_name'>{$lang['FORUM_TOPIC_FORM_TITLE']}</label>
			</th>
			<td>
				<input type='text' class='form-control' id='ft_name' name='ft_name' required>
			</td>
		</tr>
		<tr>
			<th>
				<label for='ft_desc'>{$lang['FORUM_TOPIC_FORM_DESC']}</label>
			</th>
			<td>
				<input type='text' class='form-control' id='ft_desc' name='ft_desc' required>
			</td>
		</tr>
		<tr>
			<th>
				<label for='fp_text'>{$lang['FORUM_TOPIC_FORM_TEXT']}</label>
			</th>
			<td>
				<textarea rows='8' class='form-control' cols='45' placeholder='{$lang['FORUM_POST_REPLY_INFO']}' name='fp_text' id='fp_text' required></textarea>
			</td>
		</tr>
		<tr>
			<td align='center' colspan='2'>
				<input type='submit' class='btn btn-lg btn-default' value='{$lang['FORUM_TOPIC_FORM_BUTTON']}' />
			</td>
	</table>
	<input type='hidden' name='verf' value='{$code}' />
</form>
EOF;
}

function newtopic()
{
    global $ir, $userid, $h, $lang, $db, $api;
    $_GET['forum'] = (isset($_GET['forum']) && is_numeric($_GET['forum'])) ? abs($_GET['forum']) : '';
	if (!isset($_POST['verf']) || !verify_csrf_code("forums_newtopic_{$_GET['forum']}", stripslashes($_POST['verf'])))
	{
		csrf_error("?act=newtopicform&forum={$_GET['forum']}");
	}
    if (empty($_GET['forum']))
    {
        alert('danger',"{$lang['ERROR_GENERIC']}","",true,"forum.php");
        die($h->endpage());
    }
    $q =
            $db->query(
                    "SELECT `ff_auth`, `ff_id`
                     FROM `forum_forums`
                     WHERE `ff_id` = {$_GET['forum']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        alert('danger',$lang['FORUM_TOPIC_DNE_TITLE'],$lang['FORUM_TOPIC_DNE_TEXT'],true,"forums.php");
		die($h->endpage());
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if ($r['ff_auth'] == 'staff')
    {
		if ($api->UserMemberLevelGet($userid,'forum moderator') == false)
		{  
			alert('danger',$lang['ERROR_SECURITY'],$lang['FORUM_NOPERMISSION'],true,"forums.php");
			die($h->endpage());
		}
    }
    $u = htmlentities($ir['username'], ENT_QUOTES, 'ISO-8859-1');
    $u = $db->escape($u);
    $_POST['ft_name'] =
            $db->escape(strip_tags(stripslashes($_POST['ft_name'])));
    if ((strlen($_POST['ft_name']) > 255))
    {
        alert('danger',$lang['ERROR_LENGTH'],$lang['FORUM_TOPIC_FORM_TITLE_LENGTH'],true,"back");
		die($h->endpage());
    }
    $_POST['ft_desc'] =
            $db->escape(strip_tags(stripslashes($_POST['ft_desc'])));
    if ((strlen($_POST['ft_desc']) > 255))
    {
        alert('danger',$lang['ERROR_LENGTH'],$lang['FORUM_TOPIC_FORM_TITLE_LENGTH'],true,"back");
		die($h->endpage());
    }
    $_POST['fp_text'] = $db->escape(str_replace("\n", "<br />",strip_tags(stripslashes($_POST['fp_text']))));
    if ((strlen($_POST['fp_text']) > 65535))
    {
        alert('danger',$lang['ERROR_LENGTH'],$lang['FORUM_MAX_CHAR_REPLY'],true,"back");
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
    
	alert("success",$lang['ERROR_SUCCESS'],$lang['FORUM_TOPIC_FORM_SUCCESS']);
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
    global $ir, $lang, $userid, $h, $db, $api;
	$code = request_csrf_code('forum_reply');
    $_GET['viewtopic'] = (isset($_GET['viewtopic']) && is_numeric($_GET['viewtopic'])) ? abs($_GET['viewtopic']) : '';
	$_GET['fpid'] = (isset($_GET['fpid']) && is_numeric($_GET['fpid'])) ? abs($_GET['fpid']) : '';
	$_GET['quotename'] = (isset($_GET['quotename']) && is_numeric($_GET['quotename'])) ? abs($_GET['quotename']) : '';
    if (empty($_GET['viewtopic']))
    {
        alert("danger",$lang['ERROR_GENERIC'],$lang['ERROR_FORUM_VF'],true,"forums.php");
        die($h->endpage());
    }
    if (!isset($_GET['quotename']) || !isset($_GET['fpid']))
    {
        alert("danger",$lang['ERROR_GENERIC'],$lang['ERROR_FORUM_VF'],true,"forums.php?viewtopic={$_GET['viewtopic']}");
        die($h->endpage());
    }
    $q =
            $db->query("SELECT `ft_forum_id`, `ft_name`, `ft_locked`, `ft_id` FROM `forum_topics` WHERE `ft_id` = {$_GET['viewtopic']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        alert('danger',$lang['FORUM_TOPIC_DNE_TITLE'],$lang['FORUM_TOPIC_DNE_TEXT'],true,"forums.php");
		die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $q2 =
            $db->query(
                    "SELECT `ff_auth`,`ff_id`, `ff_name`
                     FROM `forum_forums`
                     WHERE `ff_id` = {$topic['ft_forum_id']}");
    if ($db->num_rows($q2) == 0)
    {
        $db->free_result($q2);
        alert('danger',$lang['FORUM_FORUM_DNE_TITLE'],$lang['FORUM_FORUM_DNE_TEXT'],true,"forums.php?viewtopic={$_GET['viewtopic']}");
		die($h->endpage());
    }
    $forum = $db->fetch_row($q2);
    $db->free_result($q2);
    if ($forum['ff_auth'] == 'staff')
    {
		if ($api->UserMemberLevelGet($userid,'forum moderator') == false)
		{  
			alert('danger',$lang['ERROR_SECURITY'],$lang['FORUM_NOPERMISSION'],true,"forums.php");
			die($h->endpage());
		}
    }
	$q3 = $db->query("SELECT `fp_text` FROM `forum_posts` WHERE `fp_id` = {$_GET['fpid']}");
	$text = $db->fetch_single($q3);
	$text = strip_tags(stripslashes($text));
	$q4 = $db->query("SELECT `username` FROM `users` WHERE `userid` = {$_GET['quotename']}");
	$Who = $db->fetch_single($q4);
    echo "<big>
    		<a href='forums.php'>{$lang['FORUM_FORUMSHOME']}</a>
    		<span class='glyphicon glyphicon-chevron-right'></span> <a href='?viewforum={$forum['ff_id']}'>{$forum['ff_name']}</a>
    		<span class='glyphicon glyphicon-chevron-right'></span> <a href='?viewtopic={$_GET['viewtopic']}'>{$topic['ft_name']}</a>
    		<span class='glyphicon glyphicon-chevron-right'></span> {$lang['FORUM_QUOTE_FORM_PAGENAME']}
    	  </big>
		  <br />
		  <br />
    ";
    if ($topic['ft_locked'] == 0)
    {
        echo"
		<b>{$lang['FORUM_QUOTE_FORM_INFO']}</b><br />
		<form method='post' action='forums.php?reply={$topic['ft_id']}'>
		<table class='table'>
			<tr>
				<th>
					<label for='fp_text'>{$lang['FORUM_POST_POST']}</label>
				</th>
				<td>
					<textarea rows='8' class='form-control' cols='45' name='fp_text' id='fp_text' required>[quote={$Who}]{$text}[/quote]</textarea>
				</td>
			</tr>
			<tr>
				<td colspan='2' align='center'>
					<input type='submit' class='btn btn-lg btn-default' value='{$lang['FORUM_POST_REPLY2']}' />
				</td>
			</tr>
		</table>
		<input type='hidden' name='verf' value='{$code}' />
		</form>";
    }
    else
    {
        echo "{$lang['FORUM_POST_TIL']}";
    }
}
function edit()
{
    global $ir, $c, $userid, $h, $lang, $db, $api;
    $_GET['topic'] = (isset($_GET['topic']) && is_numeric($_GET['topic'])) ? abs($_GET['topic']) : '';
    if (empty($_GET['topic']))
    {
        alert("danger",$lang['ERROR_GENERIC'],$lang['ERROR_FORUM_VF'],true,"forums.php");
        die($h->endpage());
    }
    $q =
            $db->query(
                    "SELECT `ft_forum_id`, `ft_name`, `ft_id`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$_GET['topic']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        alert('danger',$lang['FORUM_TOPIC_DNE_TITLE'],$lang['FORUM_TOPIC_DNE_TEXT'],true,"forums.php");
		die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $q2 =
            $db->query(
                    "SELECT `ff_auth`, `ff_id`, `ff_name`
                     FROM `forum_forums`
                     WHERE `ff_id` = {$topic['ft_forum_id']}");
    if ($db->num_rows($q2) == 0)
    {
        $db->free_result($q2);
        alert('danger',$lang['FORUM_FORUM_DNE_TITLE'],$lang['FORUM_FORUM_DNE_TEXT'],true,"forums.php?viewtopic={$_GET['topic']}");
		die($h->endpage());
    }
    $forum = $db->fetch_row($q2);
    $db->free_result($q2);
    if ($forum['ff_auth'] == 'staff')
    {
		if (($api->UserMemberLevelGet($userid,'forum moderator')) || (!($userid == $post['fp_poster_id'])))
		{ 
			alert('danger',$lang['ERROR_SECURITY'],$lang['FORUM_NOPERMISSION'],true,"forums.php?viewtopic={$_GET['topic']}");
			die($h->endpage());
		}
    }
    $_GET['post'] = (isset($_GET['post']) && is_numeric($_GET['post'])) ? abs($_GET['post']) : '';
    if (empty($_GET['post']))
    {
        alert("danger",$lang['ERROR_GENERIC'],$lang['ERROR_FORUM_VF'],true,"forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    $q3 =
            $db->query(
                    "SELECT `fp_poster_id`, `fp_text`
                     FROM `forum_posts`
                     WHERE `fp_id` = {$_GET['post']}");
    if ($db->num_rows($q3) == 0)
    {
        $db->free_result($q3);
        alert("danger",$lang['FORUM_POST_DNE_TITLE'],$lang['FORUM_POST_DNE_TEXT'],true,"forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    $post = $db->fetch_row($q3);
    $db->free_result($q3);
    if (!($api->UserMemberLevelGet($userid,'forum moderator') || $userid == $post['fp_poster_id']))
    {
        alert('danger',$lang['ERROR_SECURITY'],$lang['FORUM_EDIT_NOPERMISSION'],true,"forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    echo "<big>
    		<a href='forums.php'>{$lang['FORUM_FORUMSHOME']}</a>
    		<span class='glyphicon glyphicon-chevron-right'></span> <a href='?viewforum={$forum['ff_id']}'>{$forum['ff_name']}</a>
    		<span class='glyphicon glyphicon-chevron-right'></span> <a href='?viewtopic={$_GET['topic']}'>{$topic['ft_name']}</a>
    		<span class='glyphicon glyphicon-chevron-right'></span> {$lang['FORUM_EDIT_FORM_PAGENAME']}
    	  </big><br /><br />
    ";
    $edit_csrf = request_csrf_code("forums_editpost_{$_GET['post']}");
	$fp_text = strip_tags(stripslashes($post['fp_text']));
    echo <<<EOF
<form action='?act=editsub&topic={$topic['ft_id']}&post={$_GET['post']}' method='post'>
<input type='hidden' name='verf' value='{$edit_csrf}' />
    <table class='table'>
        <tr>
        	<th>
			{$lang['FORUM_POST_POST']}:
			</th>
        	<td>
        		<textarea rows='7' class='form-control' cols='40' name='fp_text'>{$fp_text}</textarea>
        	</td>
        </tr>
        <tr>
        	<td align='center' colspan='2'>
				<input type='submit' class='btn btn-default' value='{$lang['FORUM_EDIT_FORM_SUBMIT']}'>
			</th>
        </tr>
    </table>
</form>
EOF;
}

function editsub()
{
    global $ir, $c, $userid, $h, $lang, $db, $api;
    $_GET['post'] = (isset($_GET['post']) && is_numeric($_GET['post'])) ? abs($_GET['post']) : '';
    $_GET['topic'] = (isset($_GET['topic']) && is_numeric($_GET['topic'])) ? abs($_GET['topic']) : '';
    if ((empty($_GET['post']) || empty($_GET['topic'])))
    {
        alert("danger",$lang['ERROR_GENERIC'],$lang['ERROR_FORUM_VF'],true,"forums.php");
        die($h->endpage());
    }
	if (!isset($_POST['verf']) || !verify_csrf_code("forums_editpost_{$_GET['post']}", stripslashes($_POST['verf'])))
	{
		csrf_error("?act=edit&topic={$_GET['topic']}&post={$_GET['post']}");
	}
    $q =
            $db->query(
                    "SELECT `ft_forum_id`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$_GET['topic']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        alert('danger',$lang['FORUM_TOPIC_DNE_TITLE'],$lang['FORUM_TOPIC_DNE_TEXT'],true,"forums.php");
		die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $q2 =
            $db->query(
                    "SELECT `ff_auth`
                     FROM `forum_forums`
                     WHERE `ff_id` = {$topic['ft_forum_id']}");
    if ($db->num_rows($q2) == 0)
    {
        $db->free_result($q2);
        alert('danger',$lang['FORUM_FORUM_DNE_TITLE'],$lang['FORUM_FORUM_DNE_TEXT'],true,"forums.php?viewtopic={$_GET['topic']}");
		die($h->endpage());
    }
    $forum = $db->fetch_row($q2);
    $db->free_result($q2);
    if ($forum['ff_auth'] == 'staff')
    {
		if (!($api->UserMemberLevelGet($userid,'forum moderator')))
		{
			alert('danger',$lang['ERROR_SECURITY'],$lang['FORUM_NOPERMISSION'],true,"forums.php?viewtopic={$_GET['topic']}");
			die($h->endpage());
		}
    }
    $q3 =
            $db->query(
                    "SELECT `fp_poster_id`
                     FROM `forum_posts`
                     WHERE `fp_id` = {$_GET['post']}");
    if ($db->num_rows($q3) == 0)
    {
        $db->free_result($q3);
        alert("danger",$lang['FORUM_POST_DNE_TITLE'],$lang['FORUM_POST_DNE_TEXT'],true,"forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    $post = $db->fetch_row($q3);
    $db->free_result($q3);
    if (!(($api->UserMemberLevelGet($userid,'forum moderator')) || $ir['userid'] == $post['fp_poster_id']))
    {
        alert('danger',$lang['ERROR_SECURITY'],$lang['FORUM_NOPERMISSION'],true,"forums.php?viewtopic={$_GET['topic']}");
		die($h->endpage());
    }
	$_POST['fp_text'] = $db->escape(str_replace("\n", "<br />",strip_tags(stripslashes($_POST['fp_text']))));
    if ((strlen($_POST['fp_text']) > 65535))
    {
        alert('danger',$lang['ERROR_LENGTH'],$lang['FORUM_MAX_CHAR_REPLY'],true,"forums.php?viewtopic={$_GET['topic']}");
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

	alert('success',$lang['ERROR_SUCCESS'],$lang['FORUM_EDIT_SUCCESS'],false);		 
	echo"<br />
   ";
    $_GET['viewtopic'] = $_GET['topic'];
    viewtopic();

}
function move()
{
    global $ir, $c, $userid, $h, $lang, $db, $api;
    if (!($api->UserMemberLevelGet($userid,'forum moderator')))
    {
        alert('danger',$lang['ERROR_SECURITY'],$lang['FORUM_NOPERMISSION'],true,"forums.php");
		die($h->endpage());
    }
    $_GET['topic'] = (isset($_GET['topic']) && is_numeric($_GET['topic'])) ? abs($_GET['topic']) : '';
    $_POST['forum'] = (isset($_POST['forum']) && is_numeric($_POST['forum'])) ? abs($_POST['forum']) : '';
    if (empty($_GET['topic']) || empty($_POST['forum']))
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['ERROR_FORUM_VF'],true,"forums.php");
        die($h->endpage());
    }
    $q = $db->query("SELECT `ft_name`, `ft_forum_id` FROM `forum_topics` WHERE `ft_id` = {$_GET['topic']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        alert('danger',$lang['FORUM_TOPIC_DNE_TITLE'],$lang['FORUM_TOPIC_DNE_TEXT'],true,"forums.php");
		die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $q2 =
            $db->query(
                    "SELECT `ff_name`
                     FROM `forum_forums`
                     WHERE `ff_id` = {$_POST['forum']}");
    if ($db->num_rows($q2) == 0)
    {
        $db->free_result($q2);
        alert('danger',$lang['ERROR_INVALID'],$lang['FORUM_MOVE_TOPIC_DFDNE'],true,"forums.php?viewtopic={$_GET['topic']}");
        die($h->endpage());
    }
    $forum = $db->fetch_row($q2);
    $db->free_result($q2);
    $db->query(
            "UPDATE `forum_topics`
             SET `ft_forum_id` = {$_POST['forum']}
             WHERE `ft_id` = {$_GET['topic']}");
    alert('success',$lang['ERROR_SUCCESS'],$lang['FORUM_MOVE_TOPIC_DONE'],true,"forums.php?viewtopic={$_GET['topic']}");
	$api->SystemLogsAdd($userid,'staff',"Moved Topic {$topic['ft_name']} to {$forum['ff_name']}");
    recache_forum($topic['ft_forum_id']);
    recache_forum($_POST['forum']);
}

function lock()
{
    global $ir, $c, $userid, $h, $bbc, $db, $api, $lang;
    if (!($api->UserMemberLevelGet($userid,'forum moderator')))
    {
        alert('danger',$lang['ERROR_SECURITY'],$lang['FORUM_NOPERMISSION'],true,"forums.php");
		die($h->endpage());
    }
    $_GET['topic'] = (isset($_GET['topic']) && is_numeric($_GET['topic'])) ? abs($_GET['topic']) : '';
    if (empty($_GET['topic']))
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['ERROR_FORUM_VF'],true,"forums.php");
        die($h->endpage());
    }
    $q =
            $db->query(
                    "SELECT `ft_name`,`ft_locked`,`ft_forum_id`, `ft_id`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$_GET['topic']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        alert('danger',$lang['FORUM_TOPIC_DNE_TITLE'],$lang['FORUM_TOPIC_DNE_TEXT'],true,"forums.php?viewtopic={$_GET['topic']}");
		die($h->endpage());
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if ($r['ft_locked'] == 1)
    {
        $db->query(
                "UPDATE `forum_topics`
                 SET `ft_locked` = 0
                 WHERE `ft_id` = {$_GET['topic']}");
		alert('success',$lang['ERROR_SUCCESS'],$lang['FORUM_UNLOCK_DONE'],true,"forums.php?viewtopic={$_GET['topic']}");
		$api->SystemLogsAdd($userid,'staff',"Unlocked Topic {$r['ft_name']}");
    }
    else
    {
        $db->query(
                "UPDATE `forum_topics`
                 SET `ft_locked` = 1
                 WHERE `ft_id` = {$_GET['topic']}");
        alert('success',$lang['ERROR_SUCCESS'],$lang['FORUM_LOCK_DONE'],true,"forums.php?viewtopic={$_GET['topic']}");
		$api->SystemLogsAdd($userid,'staff',"Locked Topic {$r['ft_name']}");
    }
}

function pin()
{
    global $ir, $c, $userid, $h, $bbc, $db, $api, $lang;
    if (!($api->UserMemberLevelGet($userid,'forum moderator')))
    {
        alert('danger',$lang['ERROR_SECURITY'],$lang['FORUM_NOPERMISSION'],true,"forums.php");
		die($h->endpage());
    }
    $_GET['topic'] = (isset($_GET['topic']) && is_numeric($_GET['topic'])) ? abs($_GET['topic']) : '';
    if (empty($_GET['topic']))
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['ERROR_FORUM_VF'],true,"forums.php");
        die($h->endpage());
    }
    $q =
            $db->query(
                    "SELECT `ft_name`, `ft_pinned`, `ft_forum_id`, `ft_id`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$_GET['topic']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        alert('danger',$lang['FORUM_TOPIC_DNE_TITLE'],$lang['FORUM_TOPIC_DNE_TEXT'],true,"forums.php?viewtopic={$_GET['topic']}");
		die($h->endpage());
    }
    $r = $db->fetch_row($q);
    $db->free_result($q);
    if ($r['ft_pinned'] == 1)
    {
        $db->query(
                "UPDATE `forum_topics`
                 SET `ft_pinned` = 0
                 WHERE `ft_id` = {$_GET['topic']}");
        alert('success',$lang['ERROR_SUCCESS'],$lang['FORUM_UNPIN_DONE'],true,"forums.php?viewtopic={$r['ft_id']}");
		$api->SystemLogsAdd($userid,'staff',"Unpinned Topic {$r['ft_name']}");
    }
    else
    {
        $db->query(
                "UPDATE `forum_topics`
                 SET `ft_pinned` = 1
                 WHERE `ft_id` = {$_GET['topic']}");
        alert('success',$lang['ERROR_SUCCESS'],$lang['FORUM_PIN_DONE'],true,"forums.php?viewtopic={$r['ft_id']}");
        $api->SystemLogsAdd($userid,'staff',"Pinned Topic {$r['ft_name']}");
    }
}

function delepost()
{
    global $ir, $c, $userid, $h, $bbc, $db, $api, $lang;
    if (!($api->UserMemberLevelGet($userid,'forum moderator')))
    {
        alert('danger',$lang['ERROR_SECURITY'],$lang['FORUM_NOPERMISSION'],true,"forums.php");
		die($h->endpage());
    }
    $_GET['post'] = isset($_GET['post']) && is_numeric($_GET['post']) ? abs($_GET['post']) : '';
    if (empty($_GET['post']))
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['ERROR_FORUM_VF'],true,"forums.php");
        die($h->endpage());
    }
    $q3 =
            $db->query(
                    "SELECT *
                     FROM `forum_posts`
                     WHERE `fp_id` = {$_GET['post']}");
    if ($db->num_rows($q3) == 0)
    {
        $db->free_result($q3);
        alert('danger',$lang['FORUM_POST_DNE_TITLE'],$lang['FORUM_POST_DNE_TEXT'],true,"forums.php");
		die($h->endpage());
    }
    $post = $db->fetch_row($q3);
    $db->free_result($q3);
    $q =
            $db->query(
                    "SELECT `ft_name`
                     FROM `forum_topics`
                     WHERE `ft_id` = {$post['fp_topic_id']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        alert('danger',$lang['FORUM_TOPIC_DNE_TITLE'],$lang['FORUM_TOPIC_DNE_TEXT'],true,"forums.php?viewtopic={$post['fp_topic_id']}");
		die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $db->query(
            "DELETE FROM `forum_posts`
    		    WHERE `fp_id` = {$post['fp_id']}");
    alert('success',$lang['ERROR_SUCCESS'],$lang['FORUM_DELETE_DONE'],true,"forums.php?viewtopic={$post['fp_topic_id']}");
    recache_topic($post['fp_topic_id']);
    recache_forum($post['ff_id']);
	$api->SystemLogsAdd($userid,'staff',"Deleted post ({$post['fp_id']}) in {$topic['ft_name']}");

}

function deletopic()
{
    global $ir, $c, $userid, $h, $bbc, $db, $api, $lang;
    $_GET['topic'] = (isset($_GET['topic']) && is_numeric($_GET['topic'])) ? abs($_GET['topic']) : '';
    if (!($api->UserMemberLevelGet($userid,'forum moderator')))
    {
        alert('danger',$lang['ERROR_SECURITY'],$lang['FORUM_NOPERMISSION'],true,"forums.php?viewtopic={$_GET['topic']}");
		die($h->endpage());
    }
	if (empty($_GET['topic']))
    {
        alert('danger',$lang['ERROR_GENERIC'],$lang['ERROR_FORUM_VF'],true,'forums.php');
        die($h->endpage());
    }
    $q = $db->query("SELECT `ft_forum_id`, `ft_name` FROM `forum_topics` WHERE `ft_id` = {$_GET['topic']}");
    if ($db->num_rows($q) == 0)
    {
        $db->free_result($q);
        alert('danger',$lang['FORUM_TOPIC_DNE_TITLE'],$lang['FORUM_TOPIC_DNE_TEXT'],true,"forums.php?viewtopic={$_GET['topic']}");
		die($h->endpage());
    }
    $topic = $db->fetch_row($q);
    $db->free_result($q);
    $db->query("DELETE FROM `forum_topics` WHERE `ft_id` = {$_GET['topic']}");
    $db->query("DELETE FROM `forum_posts` WHERE `fp_topic_id` = {$_GET['topic']}");
    alert('success',$lang['ERROR_SUCCESS'],$lang['FORUM_DELETE_TOPIC_DONE'],true,'forums.php');
    recache_forum($topic['ft_forum_id']);
	$api->SystemLogsAdd($userid,'staff',"Deleted topic {$topic['ft_name']}");
}
$h->endpage();