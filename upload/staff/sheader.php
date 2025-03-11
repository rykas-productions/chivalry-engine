<?php

/*
	File: staff/sheader.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: Loads the template, CSS, JS, etc. inside the staff panel.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/

class headers
{
    function startheaders()
    {
        global $ir, $set, $h, $db, $menuhide, $userid, $macropage, $api, $time, $sound;
		cslog('log',"Loading headers for {$set['WebsiteName']}");
        //Load the meta headers.
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
                <center>
                <?php
                //Select count of user's unread messages.
                $ir['mail'] = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`mail_id`) FROM `mail` WHERE `mail_to` = {$ir['userid']} AND `mail_status` = 'unread'"));
                //Select count of user's unread notifications.
                $ir['notifications'] = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`notif_id`) FROM `notifications` WHERE `notif_user` = {$ir['userid']} AND `notif_status` = 'unread'"));
                $title = "{$set['WebsiteName']} - {$ir['username']}";
                echo "<title>{$title}</title>";
				if ($ir['disable_alerts'] == 0)
					$notificon = "fas fa-bell";
				else
					$notificon = "fas fa-bell-slash";
				$this->loadEssentialAssets();
				$this->returnMetadata();
				$this->loadUserTheme($ir['theme']);
				$hdr=$this->getThemeNavbarColor($ir['theme']);
				$sound->loadSystem();
				cslog('warn',"Main assets have loaded successfully. Log entries after this point were created by the game or related modules, not the base engine.");
				?>
				</head>
        <?php
        if ($ir['sidemenu'] == 0)
            $toggle='toggled';
        else
            $toggle='';
        if (empty($menuhide)) {
            $ir['mail'] = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`mail_id`) FROM `mail` WHERE `mail_to` = {$ir['userid']} AND `mail_status` = 'unread'"));
            $ir['notifications'] = $db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`notif_id`) FROM `notifications` WHERE `notif_user` = {$ir['userid']} AND `notif_status` = 'unread'"));
            
			echo"
            <body>
				<div class='page-wrapper default-theme sidebar-bg {$toggle}'>
				<div id='show-sidebar' class='btn btn-md btn-dark'>
					<i class='fas fa-bars'></i>
				</div>
				<nav id='sidebar' class='sidebar-wrapper'>
					<div class='sidebar-content'>
						<!-- sidebar-brand  -->
						<div class='sidebar-item sidebar-brand'>
							<a href='index.php' class='updateHoverBtn'>{$set['WebsiteName']}</a>
							<div id='close-sidebar'>
								<i class='fas fa-times'></i>
							</div>
						</div>
						<!-- sidebar-menu  -->
                        <div class=' sidebar-item sidebar-menu'>
                            <ul>
    								<li>
    									<a href='../index.php' class='updateHoverBtn'>
    										<span class='menu-text'>Back to Game</span>
    									</a>
    								</li>
                                    <li>
    									<a href='index.php' class='updateHoverBtn'>
    										<span class='menu-text'>Staff Index</span>
    									</a>
    								</li>
								</ul>";
			             if ($api->UserMemberLevelGet($userid, "admin"))
			             {
			                 echo"
    							<ul>
    								<li class='header-menu'>
    									<span>Admin Actions</span>
    								</li>
    								<li>
    									<a href='staff_settings.php?action=basicset' class='updateHoverBtn'>
    										<span class='menu-text'>Game Settings</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_settings.php?action=announce' class='updateHoverBtn'>
    										<span class='menu-text'>Create Announcement</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_settings.php?action=diagnostics' class='updateHoverBtn'>
    										<span class='menu-text'>Server Diagnostics</span>
    									</a>
    								</li>
								</ul>
                                <ul>
    								<li class='header-menu'>
    									<span>Game Rules</span>
    								</li>
    								<li>
    									<a href='staff_rules.php?action=addrule' class='updateHoverBtn'>
    										<span class='menu-text'>Add Rule</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_rules.php?action=editrule' class='updateHoverBtn'>
    										<span class='menu-text'>Edit Rule</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_rules.php?action=delrule' class='updateHoverBtn'>
    										<span class='menu-text'>Delete Rule</span>
    									</a>
    								</li>
								</ul>
                                <ul>
    								<li class='header-menu'>
    									<span>VIP Packs</span>
    								</li>
    								<li>
    									<a href='staff_donate.php?action=addpack' class='updateHoverBtn'>
    										<span class='menu-text'>Create VIP Pack</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_donate.php?action=editpack' class='updateHoverBtn'>
    										<span class='menu-text'>Edit VIP Pack</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_donate.php?action=delpack' class='updateHoverBtn'>
    										<span class='menu-text'>Delete VIP Pack</span>
    									</a>
    								</li>
								</ul>
                                <ul>
    								<li class='header-menu'>
    									<span>Promotional Codes</span>
    								</li>
    								<li>
    									<a href='staff_promo.php?action=addpromo' class='updateHoverBtn'>
    										<span class='menu-text'>Create Promo Code</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_promo.php?action=viewpromo' class='updateHoverBtn'>
    										<span class='menu-text'>View Active Codes</span>
    									</a>
    								</li>
								</ul>
                                <ul>
    								<li class='header-menu'>
    									<span>Criminal</span>
    								</li>
    								<li>
    									<a href='staff_criminal.php??action=newcrimegroup' class='updateHoverBtn'>
    										<span class='menu-text'>Create Crime Group</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_criminal.php?action=newcrime' class='updateHoverBtn'>
    										<span class='menu-text'>Create Crime</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_criminal.php?action=editcrime' class='updateHoverBtn'>
    										<span class='menu-text'>Edit Crime</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_criminal.php?action=delcrime' class='updateHoverBtn'>
    										<span class='menu-text'>Remove Crime</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_criminal.php?action=editcrimegroup' class='updateHoverBtn'>
    										<span class='menu-text'>Edit Crime Group</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_criminal.php?action=delcrimegroup' class='updateHoverBtn'>
    										<span class='menu-text'>Remove Crime Group</span>
    									</a>
    								</li>
								</ul>
                                <ul>
    								<li class='header-menu'>
    									<span>Item Shops</span>
    								</li>
    								<li>
    									<a href='staff_shops.php?action=newshop' class='updateHoverBtn'>
    										<span class='menu-text'>Create Item Shop</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_shops.php?action=newitem' class='updateHoverBtn'>
    										<span class='menu-text'>Add Item Shop Stock</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_shops.php?action=delshop' class='updateHoverBtn'>
    										<span class='menu-text'>Remove Shop</span>
    									</a>
    								</li>
								</ul>
                                <ul>
    								<li class='header-menu'>
    									<span>NPC Control</span>
    								</li>
    								<li>
    									<a href='staff_bots.php?action=addbot' class='updateHoverBtn'>
    										<span class='menu-text'>Add NPC to Battle List</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_bots.php?action=delbot' class='updateHoverBtn'>
    										<span class='menu-text'>Remove NPC from Battle List</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_boss.php?action=addboss' class='updateHoverBtn'>
    										<span class='menu-text'>Spawn NPC Boss</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_boss.php?action=delboss' class='updateHoverBtn'>
    										<span class='menu-text'>Despawn NPC Boss</span>
    									</a>
    								</li>
								</ul>
                                <ul>
    								<li class='header-menu'>
    									<span>Towns</span>
    								</li>
    								<li>
    									<a href='staff_towns.php?action=addtown' class='updateHoverBtn'>
    										<span class='menu-text'>Create New Town</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_towns.php?action=edittown' class='updateHoverBtn'>
    										<span class='menu-text'>Edit Town</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_towns.php?action=deltown' class='updateHoverBtn'>
    										<span class='menu-text'>Remove Town</span>
    									</a>
    								</li>
								</ul>
                                <ul>
    								<li class='header-menu'>
    									<span>Academy</span>
    								</li>
    								<li>
    									<a href='staff_academy.php?action=add' class='updateHoverBtn'>
    										<span class='menu-text'>Create New Course</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_academy.php?action=edit' class='updateHoverBtn'>
    										<span class='menu-text'>Edit Course</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_academy.php?action=del' class='updateHoverBtn'>
    										<span class='menu-text'>Remove Course</span>
    									</a>
    								</li>
								</ul>
                                <ul>
    								<li class='header-menu'>
    									<span>Jobs</span>
    								</li>
    								<li>
    									<a href='staff_jobs.php?action=newjob' class='updateHoverBtn'>
    										<span class='menu-text'>Create New Employer</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_jobs.php?action=newjobrank' class='updateHoverBtn'>
    										<span class='menu-text'>Create New Job Rank</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_jobs.php?action=editjob' class='updateHoverBtn'>
    										<span class='menu-text'>Edit Employer</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_jobs.php?action=jobdele' class='updateHoverBtn'>
    										<span class='menu-text'>Remove Employer</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_jobs.php?action=jobrankedit' class='updateHoverBtn'>
    										<span class='menu-text'>Edit Job Rank</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_jobs.php?action=jobrankdele' class='updateHoverBtn'>
    										<span class='menu-text'>Remove Job Rank</span>
    									</a>
    								</li>
								</ul>
                                <ul>
    								<li class='header-menu'>
    									<span>Estates</span>
    								</li>
    								<li>
    									<a href='staff_estates.php?action=addestate' class='updateHoverBtn'>
    										<span class='menu-text'>Create New Estate</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_estates.php?action=editestate' class='updateHoverBtn'>
    										<span class='menu-text'>Edit Estate</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_estates.php?action=delestate' class='updateHoverBtn'>
    										<span class='menu-text'>Remove Estate</span>
    									</a>
    								</li>
								</ul>
                                <ul>
    								<li class='header-menu'>
    									<span>Mining</span>
    								</li>
    								<li>
    									<a href='staff_mine.php?action=addmine' class='updateHoverBtn'>
    										<span class='menu-text'>Create New Mine</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_mine.php?action=editmine' class='updateHoverBtn'>
    										<span class='menu-text'>Edit Mine</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_mine.php?action=delmine' class='updateHoverBtn'>
    										<span class='menu-text'>Remove Mine</span>
    									</a>
    								</li>
								</ul>
                                <ul>
    								<li class='header-menu'>
    									<span>Blacksmith</span>
    								</li>
    								<li>
    									<a href='staff_smelt.php?action=add' class='updateHoverBtn'>
    										<span class='menu-text'>Create New Recipe</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_smelt.php?action=del' class='updateHoverBtn'>
    										<span class='menu-text'>Remove Recipe</span>
    									</a>
    								</li>
								</ul>
                                <ul>
    								<li class='header-menu'>
    									<span>Farming</span>
    								</li>
    								<li>
    									<a href='../farm.php?action=createseed' class='updateHoverBtn'>
    										<span class='menu-text'>Create New Crop</span>
    									</a>
    								</li>
                                    <li>
    									<a href='../farm.php?action=editseed' class='updateHoverBtn'>
    										<span class='menu-text'>Edit Crop</span>
    									</a>
    								</li>
                                    <li>
    									<a href='../farm.php?action=delseed' class='updateHoverBtn'>
    										<span class='menu-text'>Remove Crop</span>
    									</a>
    								</li>
								</ul>
    						";
			             }
			             if ($api->UserMemberLevelGet($userid, "assistant"))
			             {
			                 echo"
    							<ul>
    								<li class='header-menu'>
    									<span>Game Polls</span>
    								</li>
                                    <li>
    									<a href='staff_polling.php?action=addpoll' class='updateHoverBtn'>
    										<span class='menu-text'>Create Poll</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_polling.php?action=closepoll' class='updateHoverBtn'>
    										<span class='menu-text'>End Poll</span>
    									</a>
    								</li>
								</ul>
                                <ul>
    								<li class='header-menu'>
    									<span>Items</span>
    								</li>";
        			                 if ($api->UserMemberLevelGet($userid, "admin"))
        			                 {
            			                     echo"
        								<li>
        									<a href='staff_items.php?action=createitmgroup' class='updateHoverBtn'>
        										<span class='menu-text'>Create Item Group</span>
        									</a>
        								</li>
                                        <li>
        									<a href='staff_items.php?action=create' class='updateHoverBtn'>
        										<span class='menu-text'>Create Item</span>
        									</a>
        								</li>
                                        <li>
        									<a href='staff_items.php?action=edit' class='updateHoverBtn'>
        										<span class='menu-text'>Edit Item</span>
        									</a>
        								</li>
                                        <li>
        									<a href='staff_items.php?action=delete' class='updateHoverBtn'>
        										<span class='menu-text'>Delete Item</span>
        									</a>
        								</li>
                                        <li>
        									<a href='staff_items.php?action=edititmgroup' class='updateHoverBtn'>
        										<span class='menu-text'>Edit Item Group</span>
        									</a>
        								</li>
                                        <li>
        									<a href='staff_items.php?action=delitmgroup' class='updateHoverBtn'>
        										<span class='menu-text'>Delete Item Group</span>
        									</a>
        								</li>";
        			                 }
        			                 echo"
                                    <li>
    									<a href='staff_items.php?action=giveitem' class='updateHoverBtn'>
    										<span class='menu-text'>Gift Item</span>
    									</a>
    								</li>
								</ul>
                                <ul>
    								<li class='header-menu'>
    									<span>Players</span>
    								</li>";
        			                 if ($api->UserMemberLevelGet($userid, "admin"))
        			                 {
            			                     echo"
        								<li>
        									<a href='staff_users.php?action=createuser' class='updateHoverBtn'>
        										<span class='menu-text'>Create Player</span>
        									</a>
        								</li>
                                        <li>
        									<a href='staff_users.php?action=edituser' class='updateHoverBtn'>
        										<span class='menu-text'>Edit Player</span>
        									</a>
        								</li>
                                        <li>
        									<a href='staff_users.php?action=deleteuser' class='updateHoverBtn'>
        										<span class='menu-text'>Delete Player</span>
        									</a>
        								</li>
                                        <li>
        									<a href='staff_users.php?action=changepw' class='updateHoverBtn'>
        										<span class='menu-text'>Change Player Password</span>
        									</a>
        								</li>
                                        <li>
        									<a href='staff_settings.php?action=staff' class='updateHoverBtn'>
        										<span class='menu-text'>Change Member Level</span>
        									</a>
        								</li>
                                        <li>
        									<a href='staff_users.php?action=forcelogin' class='updateHoverBtn'>
        										<span class='menu-text'>Control Player</span>
        									</a>
        								</li>
                                        <li>
        									<a href='staff_estates.php?action=giftestate' class='updateHoverBtn'>
        										<span class='menu-text'>Gift Player Estate</span>
        									</a>
        								</li>";
        			                 }
        			                 echo"
                                    <li>
    									<a href='staff_settings.php?action=restore' class='updateHoverBtn'>
    										<span class='menu-text'>Restore Player Stats</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_users.php?action=masspayment' class='updateHoverBtn'>
    										<span class='menu-text'>Player Mass Payment</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_users.php?action=reports' class='updateHoverBtn'>
    										<span class='menu-text'>View Player Reports</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_punish.php?action=directemail' class='updateHoverBtn'>
    										<span class='menu-text'>Direct Email Player</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_users.php?action=logout' class='updateHoverBtn'>
    										<span class='menu-text'>Force Logout Player</span>
    									</a>
    								</li>
								</ul>
                                <ul>
    								<li class='header-menu'>
    									<span>Player Permissions</span>
    								</li>
                                    <li>
    									<a href='staff_perms.php?action=viewperm' class='updateHoverBtn'>
    										<span class='menu-text'>View Permissions</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_perms.php?action=editperm' class='updateHoverBtn'>
    										<span class='menu-text'>Edit Permissions</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_perms.php?action=resetperm' class='updateHoverBtn'>
    										<span class='menu-text'>Reset Permissions</span>
    									</a>
    								</li>
								</ul>
                                <ul>
    								<li class='header-menu'>
    									<span>Guilds</span>
    								</li>
                                    <li>
    									<a href='staff_guilds.php?action=viewguild' class='updateHoverBtn'>
    										<span class='menu-text'>View Guild</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_guilds.php?action=creditguild' class='updateHoverBtn'>
    										<span class='menu-text'>Credit Guild</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_guilds.php?action=viewwars' class='updateHoverBtn'>
    										<span class='menu-text'>View Guild Wars</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_guilds.php?action=editguild' class='updateHoverBtn'>
    										<span class='menu-text'>Edit Guild</span>
    									</a>
    								</li>
								</ul>
    						";
			             }
			             if ($api->UserMemberLevelGet($userid, "Forum Moderator"))
			             {
			                 echo"
    							<ul>
    								<li class='header-menu'>
    									<span>Forums</span>
    								</li>
                                    <li>
    									<a href='staff_forums.php?action=addforum' class='updateHoverBtn'>
    										<span class='menu-text'>Create Category</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_forums.php?action=editforum' class='updateHoverBtn'>
    										<span class='menu-text'>Edit Category</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_forums.php?action=delforum' class='updateHoverBtn'>
    										<span class='menu-text'>Delete Category</span>
    									</a>
    								</li>
								</ul>
                                <ul>
    								<li class='header-menu'>
    									<span>Punishments</span>
    								</li>
                                    <li>
    									<a href='staff_punish.php?action=fedjail' class='updateHoverBtn'>
    										<span class='menu-text'>Create Fed Dungeon Sentence</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_punish.php?action=forumwarn' class='updateHoverBtn'>
    										<span class='menu-text'>Give Forum Warning</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_punish.php?action=forumban' class='updateHoverBtn'>
    										<span class='menu-text'>Give Forum Ban</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_punish.php?action=spamhammer' class='updateHoverBtn'>
    										<span class='menu-text'>Spam Cleanup</span>
    									</a>
    								</li>";
    			                 if ($api->UserMemberLevelGet($userid, "assistant"))
    			                 {
    			                     echo "<li>
    									<a href='staff_fedjail.php?action=viewappeal' class='updateHoverBtn'>
    										<span class='menu-text'>Fed Dungeon Appeals</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_punish.php?action=editfedjail' class='updateHoverBtn'>
    										<span class='menu-text'>Edit Fed Dungeon Sentence</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_punish.php?action=mailban' class='updateHoverBtn'>
    										<span class='menu-text'>Give Mail Ban</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_punish.php?action=unfedjail' class='updateHoverBtn'>
    										<span class='menu-text'>Remove Fed Dungeon Sentence</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_punish.php?action=unforumban' class='updateHoverBtn'>
    										<span class='menu-text'>Remove Forum Ban</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_punish.php?action=unmailban' class='updateHoverBtn'>
    										<span class='menu-text'>Remove Mail Ban</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_punish.php?action=ipsearch' class='updateHoverBtn'>
    										<span class='menu-text'>IP Address Search</span>
    									</a>
    								</li>";
    			                 }
    			                 if ($api->UserMemberLevelGet($userid, "admin"))
    			                 {
    			                     echo"<li>
    									<a href='staff_punish.php?action=massemail' class='updateHoverBtn'>
    										<span class='menu-text'>Send Mass Email</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_punish.php?action=banip' class='updateHoverBtn'>
    										<span class='menu-text'>Ban IP Address</span>
    									</a>
    								</li>
                                    <li>
    									<a href='staff_punish.php?action=unbanip' class='updateHoverBtn'>
    										<span class='menu-text'>Pardon IP Address</span>
    									</a>
    								</li>";
    			                 }
    			                 echo"
								</ul>";
			             }
                            ?><ul><li class="header-menu">
									<span id='ui_time'><?php echo date('F j, Y') . " " . date('g:i:s a'); ?></span>
								</li></ul></div>
						<!-- sidebar-menu  -->
					</div><!-- sidebar-footer  -->
					<div class="sidebar-footer">
						<div class="dropdown">
							<a href="../notifications.php" class="updateHoverBtn">
								<i class="fa fa-bell"></i>
								<span class="badge badge-pill badge-success notification" id="ui_notif"><?php echo shortNumberParse($ir['notifications']); ?></span>
							</a>
						</div>
						<div class="dropdown">
							<a href="../inbox.php" class="updateHoverBtn">
								<i class="fa fa-envelope"></i>
								<span class="badge badge-pill badge-success notification" id="ui_mail"><?php echo shortNumberParse($ir['mail']); ?></span>
							</a>
						</div>
						<div class="dropdown">
							<a href="../preferences.php" class="updateHoverBtn">
								<i class="fa fa-cog"></i>
							</a>
						</div>
						<div>
							<a href="../logout.php" class="updateHoverBtn">
								<i class="fa fa-power-off"></i>
							</a>
						</div>
						<div class="pinned-footer">
							<a href="#">
								<i class="fas fa-ellipsis-h"></i>
							</a>
						</div>
					</div>
				</nav>

				<!-- Page Content -->
				<main class="page-content pt-2">
					<div id="overlay" class="overlay"></div>
						<div class="container-fluid p-5">
            <noscript>
                <?php alert('info', "Information!", "Please enable Javascript.", false); ?>
            </noscript>
            <?php
			date_default_timezone_set($set['game_time']);
            $IP = $db->escape($_SERVER['REMOTE_ADDR']);
            $ipq = $db->query("/*qc=on*/SELECT `ip_id` FROM `ipban` WHERE `ip_ip` = '{$IP}'");
            if ($db->num_rows($ipq) > 0) {
                alert('danger', "Uh Oh!", "You have been IP Banned. Please contact support.", false);
                die($h->endpage());
            }
            $fed = $db->fetch_row($db->query("/*qc=on*/SELECT * FROM `fedjail` WHERE `fed_userid` = {$userid}"));
            if ($fed['fed_out'] < $time) {
                $db->query("UPDATE `users` SET `fedjail` = 0 WHERE `userid` = {$userid}");
                $db->query("DELETE FROM `fedjail` WHERE `fed_userid` = {$userid}");
            }
            //User is in federal jail. Stop their access.
    if ($ir['fedjail'] > 0) {
		$lasthour=time()-3600;
		$fq2=$db->query("/*qc=on*/SELECT * FROM `fedjail_appeals` WHERE `fja_user` = {$userid} AND `fja_time` >= {$lasthour} LIMIT 1");
		if (isset($_POST['fedappeal']))
		{
			$msg = $db->escape(stripslashes($_POST['fedappeal']));
			$time=time();
			if ($db->num_rows($fq2) != 0)
			{
				echo "<b>You can only submit an appeal once per hour...</b>";
			}
			else
			{
				echo "<b>Response posted. Come back later for a response.</b>";
				$db->query("INSERT INTO `fedjail_appeals` (`fja_user`, `fja_responder`, `fja_text`, `fja_time`) VALUES ('{$userid}', '{$userid}', '{$msg}', '{$time}')");
			}
		}
        alert('info', "Federal Dungeon!", "You are locked away in Federal Dungeon for the next
					    " . TimeUntil_Parse($fed['fed_out']) . ". You were placed in here for <b>{$fed['fed_reason']}</b>.", false);
		$fq=$db->query("/*qc=on*/SELECT * FROM `fedjail_appeals` WHERE `fja_user` = {$userid} ORDER BY `fja_time` ASC");
		echo "<table class='table table-bordered'>";
		while ($fr = $db->fetch_row($fq))
		{
			echo "<tr>
			<th width='33%'>
				{$api->SystemUserIDtoName($fr['fja_responder'])} [{$fr['fja_responder']}]<br />
				" . DateTime_Parse($fr['fja_time']) . "
			</th>
			<td>
				{$fr['fja_text']}
			</td>
			</tr>";
		}
		echo "
		<tr>
			<td colspan='2'>
				<form method='post'>
					Submitting your appeal. You can only respond once an hour, so give as much information as you can. Honesty may be rewarded with a lesser sentence.
					<textarea name='fedappeal' class='form-control'></textarea>
					<input type='submit' value='Submit Appeal' class='btn btn-primary'>
				</form>
			</td>
		</tr>
		</table>";
        die($h->endpage());
    }
		$this->showSocialAlerts();
		$this->showStatusAlerts();
        }
    }
	
	function showSocialAlerts()
	{
		global $ir;
		echo "<div class='row'>";
		if ($ir['mail'] > 0) 
		{
			echo "<div class='col-md'>";
				alert('info', "", "You have {$ir['mail']} unread messages.", true, '../inbox.php', "View");
			echo "</div>";
        }
        //Tell user they have unread notifcations when they do.
        if ($ir['notifications'] > 0) 
		{
			echo "<div class='col-md'>";
				alert('info', "", "You have {$ir['notifications']} unread notifications.", true, '../notifications.php', "View");
			echo "</div>";
        }
		//Tell user they have unread game announcements when they do.
		if ($ir['announcements'] > 0) 
		{
			echo "<div class='col-md'>";
				alert('info', "", "You have {$ir['announcements']} unread announcements.", true, '../announcements.php', "View");
			echo "</div>";
		}
		echo "</div>";
	}
	function showStatusAlerts()
	{
		global $ir, $api, $db;
		echo "<div class='row'>";
		if ($api->UserStatus($ir['userid'], 'infirmary')) 
		{
			$InfirmaryOut = $db->fetch_single($db->query("/*qc=on*/SELECT `infirmary_out` FROM `infirmary` WHERE `infirmary_user` = {$ir['userid']}"));
			$InfirmaryRemain = TimeUntil_Parse($InfirmaryOut);
			echo "<div class='col-sm'>";
				alert('info', "", "You are in the Infirmary for {$InfirmaryRemain}.", true, "quickuse.php?infirmary", "Use " . parseInfirmaryItemName($ir['iitem']));
			echo "</div>";
		}
		//User is in the dungeon, tell them how long.
		if ($api->UserStatus($ir['userid'], 'dungeon')) 
		{
			$DungeonOut = $db->fetch_single($db->query("/*qc=on*/SELECT `dungeon_out` FROM `dungeon` WHERE `dungeon_user` = {$ir['userid']}"));
			$DungeonRemain = TimeUntil_Parse($DungeonOut);
			echo "<div class='col-sm'>";
				alert('info', "", "You are in the dungeon for {$DungeonRemain}.", true, "quickuse.php?dungeon", "Use " . parseDungeonItemName($ir['ditem']));
			echo "</div>";
		}
		echo "</div>";
	}
	
	function loadUserTheme($themeID)
	{
	    global $set;
	    cslog('log',"User Theme ID: {$themeID}.");
	    echo "<link rel='stylesheet' href='https://cdn.chivalryisdeadgame.com/assets/css/sidebar-themes.css'>";
	    if ($themeID == 1)
	    {
	        echo "
			<link rel='stylesheet' href='https://cdn.chivalryisdeadgame.com/assets/css/themes/default-21.2.2.css'>
			<meta name='theme-color' content='#333'>
			<style>
			.default-theme .sidebar-wrapper {
				background-color: #333;
			}
			</style>";
	    }
	    if ($themeID == 2)
	    {
	        echo "
			<link rel='stylesheet' href='https://cdn.chivalryisdeadgame.com/assets/css/themes/darkly-21.2.2.css'>
			<meta name='theme-color' content='#303030'>";
	    }
	    if ($themeID == 3)
	    {
	        echo "
			<link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootswatch/{$set['bootstrap_version']}/slate/bootstrap.min.css'>
			<meta name='theme-color' content='#272B30'>
			<style>
			.default-theme .sidebar-wrapper {
				background-color: #272B30;
			}
			</style>";
	    }
	    if ($themeID == 4)
	    {
	        echo "
			<link rel='stylesheet' href='https://cdn.chivalryisdeadgame.com/assets/css/themes/cyborg-21.2.2.css'>
			<meta name='theme-color' content='#060606'>";
	    }
	    if ($themeID == 5)
	    {
	        echo "
			<link rel='stylesheet' href='https://cdn.chivalryisdeadgame.com/assets/css/themes/united-21.2.2.css'>
			<meta name='theme-color' content='#772953'>";
	    }
	    if ($themeID == 6)
	    {
	        echo "
			<link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootswatch/{$set['bootstrap_version']}/cerulean/bootstrap.min.css'>
			<meta name='theme-color' content='#04519b'>
			<style>
			.default-theme .sidebar-wrapper {
				background-color: #04519b;
			}
			</style>";
	    }
	    if ($themeID == 7)
	    {
	        echo "
			<link rel='stylesheet' href='https://cdn.chivalryisdeadgame.com/assets/css/themes/castle-21.2.1.css'>
			<meta name='theme-color' content='rgba(0, 0, 0, 0.8)'>";
	    }
	    if ($themeID == 8)
	    {
	        echo "
			<link rel='stylesheet' href='https://cdn.chivalryisdeadgame.com/assets/css/themes/sunset-21.2.1.css'>
			<meta name='theme-color' content='rgba(64, 0, 0, 0.8)'>";
	    }
	}
	
	function getThemeNavbarColor($themeID)
	{
	    if ($themeID == 2)
	        return 'navbar-light bg-light';
	        else
        return 'navbar-dark bg-dark';
	}
	
	function loadEssentialAssets()
	{
		global $ir, $set;
		cslog('log',"Essential assets loading now.");
		$this->loadCSS();
		$this->loadEarlyJS();
		cslog('log',"Essential assets loaded successfully.");
		
	}
	
	function loadCSS()
	{
	    global $set;
	    cslog('log',"CSS is loading.");
	    echo "<link rel='stylesheet' href='https://cdn.chivalryisdeadgame.com/assets/css/game-{$set['game_css_version']}.css' async>
				<link rel='stylesheet' href='https://seiyria.com/gameicons-font/css/game-icons.css' async>
				<link rel='stylesheet' href='//malihu.github.io/custom-scrollbar/jquery.mCustomScrollbar.min.css' defer>";
	    
	}
	
	function loadEarlyJS()
	{
		global $set;
		cslog('log',"Essential JS scripts are loading.");
		echo "<script src='https://ajax.googleapis.com/ajax/libs/jquery/{$set['jquery_version']}/jquery.min.js'></script>
		<script src='https://cdn.chivalryisdeadgame.com/assets/js/game-v{$set['game_js_version']}.js' async></script>";
	}
	
	function loadJS()
	{
	    global $ir, $set;
	    cslog('log',"JS is loading.");
	    echo "<script src='https://cdn.jsdelivr.net/npm/popper.js@{$set['popper_version']}/dist/umd/popper.min.js'></script>
        <script src='https://stackpath.bootstrapcdn.com/bootstrap/{$set['bootstrap_version']}/js/bootstrap.min.js'></script>
		<script src='https://cdn.jsdelivr.net/gh/MasterGeneral156/chivalry-is-dead-game-cdn@1/js/register.min.js' defer></script>
		<script src='https://use.fontawesome.com/releases/v{$set['fontawesome_version']}/js/all.js'></script>
		<script src='https://cdn.chivalryisdeadgame.com/assets/js/underscore-min.js' defer></script>
        <script src='https://cdn.rawgit.com/tonystar/bootstrap-hover-tabs/v{$set['bshover_tabs_version']}/bootstrap-hover-tabs.js' defer></script>
		<script async src='https://www.googletagmanager.com/gtag/js?id=UA-69718211-1' defer></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());
		  
		  gtag('config', 'UA-69718211-1');
		</script>";
	    ?>
		<script src="https://cdn.chivalryisdeadgame.com/assets/js/sidemenu.js" async></script>
		<script src="https://malihu.github.io/custom-scrollbar/jquery.mCustomScrollbar.concat.min.js" defer></script>
		<script type="text/javascript">
            jQuery(function ($) {
            $("#close-sidebar").click(function() {
              $(".page-wrapper").removeClass("toggled");
				$.post('js/script/menu.php', { value: 1}, 
					function(returnedData){
						 console.log("Disabled sidebar.");
				});
			});
			$("#overlay").click(function() {
              $(".page-wrapper").removeClass("toggled");
				$.post('js/script/menu.php', { value: 1}, 
					function(returnedData){
						 console.log("Disabled sidebar via overlay.");
				});
			});
            $("#show-sidebar").click(function() {
              $(".page-wrapper").addClass("toggled");
			  $.post('js/script/menu.php', { value: 0}, 
					function(returnedData){
						 console.log("Enabled sidebar.");
				});
            });
        });	
        </script>
        <script src='https://cdn.chivalryisdeadgame.com/assets/js/jquery.canvasjs.min.js' defer></script>
		<?php
	}
	
	function returnMetadata()
	{
	    global $set;
	    cslog('log',"Setting website metadata.");
	    echo "<meta charset='utf-8'>
                <meta http-equiv='X-UA-Compatible' content='IE=edge'>
                <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
				<meta name='author' content='{$set['WebsiteOwner']}'>
                <meta name='description' content='{$set['Website_Description']}'>
                <meta name='keywords' content='medieval europe, mmorpg, text rpg, rpg, multiplayer, game, video game, no download, mobile, free, chivalry is dead, cid'>
                <meta property='og:title' content='" . returnGameTitle() . "'/>
                <meta property='og:description' content='{$set['Website_Description']}'/>
                <meta property='og:image' content='https://cdn.chivalryisdeadgame.com/assets/img/logo/logo512.png'/>
                <meta http-equiv='x-dns-prefetch-control' content='off'>
                <link rel='shortcut icon' href='https://cdn.chivalryisdeadgame.com/assets/img/logo/logo192.png' type='image/x-icon'/>
				<!-- generics -->
				<link rel='icon' href='https://cdn.chivalryisdeadgame.com/assets/img/logo/logo32.png' sizes='32x32'>
				<link rel='icon' href='https://cdn.chivalryisdeadgame.com/assets/img/logo/logo57.png' sizes='57x57'>
				<link rel='icon' href='https://cdn.chivalryisdeadgame.com/assets/img/logo/logo76.png' sizes='76x76'>
				<link rel='icon' href='https://cdn.chivalryisdeadgame.com/assets/img/logo/logo96.png' sizes='96x96'>
				<link rel='icon' href='https://cdn.chivalryisdeadgame.com/assets/img/logo/logo128.png' sizes='128x128'>
				<link rel='icon' href='https://cdn.chivalryisdeadgame.com/assets/img/logo/logo192.png' sizes='192x192'>
				<link rel='icon' href='https://cdn.chivalryisdeadgame.com/assets/img/logo/logo228.png' sizes='228x228'>
				
				<!-- Android -->
				<link rel='shortcut icon' sizes='196x196' href='https://cdn.chivalryisdeadgame.com/assets/img/logo/logo196.png'>
				
				<!-- iOS -->
				<link rel='apple-touch-icon' href='https://cdn.chivalryisdeadgame.com/assets/img/logo/logo120.png' sizes='120x120'>
				<link rel='apple-touch-icon' href='https://cdn.chivalryisdeadgame.com/assets/img/logo/logo152.png' sizes='152x152'>
				<link rel='apple-touch-icon' href='https://cdn.chivalryisdeadgame.com/assets/img/logo/logo180.png' sizes='180x180'>
				
				<!-- Windows 8 IE 10-->
				<meta name='msapplication-TileColor' content='#FFFFFF'>
				<meta name='msapplication-TileImage' content='https://cdn.chivalryisdeadgame.com/assets/img/logo/logo144.png'>
				
				<!— Windows 8.1 + IE11 and above —>
				<meta name='msapplication-config' content='https://cdn.chivalryisdeadgame.com/assets/browserconfig.xml' />";
	}

    function userdata($ir, $dosessh = 1)
    {
        global $db, $userid;;
        $IP = $db->escape($_SERVER['REMOTE_ADDR']);
		$time=time();
		if ($ir['invis'] < time())
		{
			//Update the user as they browse the game.
			$db->query("UPDATE `users`
                    SET `laston` = {$_SERVER['REQUEST_TIME']}, 
                    `lastip` = '{$IP}' 
                    WHERE `userid` = {$userid}");
		}
		else
		{
			//Update the user as they browse the game.
			$db->query("UPDATE `users`
                    SET `lastip` = '{$IP}' 
                    WHERE `userid` = {$userid}");
		}
        if (!$ir['email']) {
            global $domain;
            die("<body>Your account is likely broken. Please contact admin@{$domain} and include your User ID.");
        }
        if (!isset($_SESSION['attacking'])) {
            $_SESSION['attacking'] = 0;
        }
        if ($dosessh && ($_SESSION['attacking'] || $ir['attacking'])) {
            $hosptime = Random(10, 50);
            $api->UserStatusSet($userid, 'infirmary', $hosptime, "Ran from a fight");
            alert("warning", "Uh Oh!", "For leaving your previous fight, you were placed in the Infirmary for {$hosptime}
            minutes, and lost all your experience.", false);
            $db->query("UPDATE `users` SET `xp` = 0, `attacking` = 0 WHERE `userid` = $userid");
            $_SESSION['attacking'] = 0;
        }
    }

    function endpage()
    {
        global $db, $ir, $set, $userid, $api, $start;
        $query_extra = '';
        if (isset($_GET['benchmark']))
            include('forms/include_end.php');   //benchmark data
            $this->loadJS();
            cslog('warn',"Main script has finished executing. Wrapping up now.");
            //Set mysqldebug in the URL to get query debugging as an admin.
            if (isset($_GET['mysqldebug']) && $ir['user_level'] == 'Admin')
            {
                ?>
        <pre class='pre-scrollable'>
                  <?php
                  var_dump($db->queries)
                  ?>
              </pre>
    	<?php
    }
    ?>
        </div>
        </div>
        </div>
        <!-- /.container -->
        <br />
		</body>
        </html>
    <?php
    }
}
