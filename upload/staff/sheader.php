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
        global $ir, $set, $h, $db, $menuhide, $userid, $time, $_CONFIG, $api;
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <meta name="description" content="<?php echo $set['Website_Description']; ?>">
            <meta property="og:title" content="<?php echo $set['WebsiteName']; ?>"/>
            <meta property="og:description" content="<?php echo $set['Website_Description']; ?>"/>
            <meta property="og:image" content=""/>
            <link rel="shortcut icon" href="" type="image/x-icon"/>
            <!-- CSS -->
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
            <link rel="stylesheet" href="../css/sidebar-themes.css">
            <meta name="theme-color" content="#e7e7e7">
            <meta name="author" content="<?php echo $set['WebsiteOwner']; ?>">
            <?php echo "<title>{$set['WebsiteName']}</title>"; ?>
        </head>
        <?php
        if (empty($menuhide)) {
            $ir['mail'] = $db->fetch_single($db->query("SELECT COUNT(`mail_id`) FROM `mail` WHERE `mail_to` = {$ir['userid']} AND `mail_status` = 'unread'"));
            $ir['notifications'] = $db->fetch_single($db->query("SELECT COUNT(`notif_id`) FROM `notifications` WHERE `notif_user` = {$ir['userid']} AND `notif_status` = 'unread'"));
            $energy = $api->user->getInfoPercent($userid, 'energy');
            $brave = $api->user->getInfoPercent($userid, 'brave');
            $will = $api->user->getInfoPercent($userid, 'will');
            $xp = round($ir['xp'] / $ir['xp_needed'] * 100);
            $hp = $api->user->getInfoPercent($userid, 'hp');
            ?>
        <body>
        <div class="page-wrapper default-theme sidebar-bg toggled">
        <div id="show-sidebar" class="btn btn-sm btn-dark">
            <i class="fas fa-bars"></i>
        </div>
        <nav id="sidebar" class="sidebar-wrapper">
            <div class="sidebar-content">
                <!-- sidebar-brand  -->
                <div class="sidebar-item sidebar-brand">
                    <a href="index.php"><?php echo $set['WebsiteName']; ?></a>
                    <div id='close-sidebar'>
                        <i class='fas fa-times'></i>
                    </div>
                </div>
                <div class=" sidebar-item sidebar-menu">
                    <ul>
                        <li class="header-menu">
                            <span>
                            <?php  
                            echo "{$ir['username']} [{$userid}]<br />
                            Level {$ir['level']}<br />
                            Energy {$energy}%<br />
                            Brave {$brave}%<br />
                            Will {$will}%<br />
                            XP {$xp}%<br />
                            HP {$hp}%<br />
                            {$_CONFIG['primary_currency']}: " . number_format($ir['primary_currency']) . "<br />
                            {$_CONFIG['secondary_currency']}: " . number_format($ir['secondary_currency']); ?>
                            </span>
                        </li>
                    </ul>
                </div>
                <!-- sidebar-menu  -->
                <div class=" sidebar-item sidebar-menu">
                    <ul>
                        <li class="header-menu">
                            <span>General</span>
                        </li>
                        <li>
                            <a href="../index.php">
                                <span class="menu-text">Back to Game</span>
                            </a>
                        </li>
                        <li class="header-menu">
                            <span>Staff Options</span>
                            <?php
                            if ($api->user->getStaffLevel($userid, 'admin')) {
                                ?>
                                <li class="sidebar-dropdown">
                                    <a href="#">
                                        <span class="menu-text">Admin</span>
                                    </a>
                                    <div class="sidebar-submenu">
                                        <ul>
                                            <li>
                                                <a href="staff_settings.php">Game Core</a>
                                            </li>
                                            <li>
                                                <a href="staff_settings.php?action=announce">Create Announcement</a>
                                            </li>
                                            <li>
                                                <a href="staff_academy.php">Academy</a>
                                            </li>
                                            <li>
                                                <a href="staff_criminal.php">Crimes</a>
                                            </li>
                                            <li>
                                                <a href="staff_shops.php">Shops</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="sidebar-dropdown">
                                    <a href="#">
                                        <span class="menu-text">NPCs</span>
                                    </a>
                                    <div class="sidebar-submenu">
                                        <ul>
                                            <li>
                                                <a href='staff_bots.php?action=addbot'>Add NPC Bot</a>
                                            </li>
                                            <li>
                                                <a href='staff_bots.php?action=delbot'>Delete NPC Bot</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="sidebar-dropdown">
                                    <a href="#">
                                        <span class="menu-text">Jobs</span>
                                    </a>
                                    <div class="sidebar-submenu">
                                        <ul>
                                            <li>
                                                <a href='staff_jobs.php?action=newjob'>Create Job</a>
                                            </li>
                                            <li>
                                                <a href='staff_jobs.php?action=jobedit'>Edit Job</a>
                                            </li>
                                            <li>
                                                <a href='staff_jobs.php?action=jobdele'>Delete Job</a>
                                            </li>
                                            <li>
                                                <a href='staff_jobs.php?action=newjobrank'>Create Job Rank</a>
                                            </li>
                                            <li>
                                                <a href='staff_jobs.php?action=jobrankedit'>Edit Job Rank</a>
                                            </li>
                                            <li>
                                                <a href='staff_jobs.php?action=jobrankdele'>Delete Job Rank</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="sidebar-dropdown">
                                    <a href="#">
                                        <span class="menu-text">Towns</span>
                                    </a>
                                    <div class="sidebar-submenu">
                                        <ul>
                                            <li>
                                                <a href='staff_towns.php?action=addtown'>Create Town</a>
                                            </li>
                                            <li>
                                                <a href='staff_towns.php?action=edittown'>Edit Town</a>
                                            </li>
                                            <li>
                                                <a href='staff_towns.php?action=deltown'>Delete Town</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="sidebar-dropdown">
                                    <a href="#">
                                        <span class="menu-text">Estates</span>
                                    </a>
                                    <div class="sidebar-submenu">
                                        <ul>
                                            <li>
                                                <a href='staff_estates.php?action=addestate'>Create Estate</a>
                                            </li>
                                            <li>
                                                <a href='staff_estates.php?action=editestate'>Edit Estate</a>
                                            </li>
                                            <li>
                                                <a href='staff_estates.php?action=delestate'>Delete Estate</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="sidebar-dropdown">
                                    <a href="#">
                                        <span class="menu-text">Mines</span>
                                    </a>
                                    <div class="sidebar-submenu">
                                        <ul>
                                            <li>
                                                <a href='staff_mine.php?action=addmine'>Create Mine</a>
                                            </li>
                                            <li>
                                                <a href='staff_mine.php?action=editmine'>Edit Mine</a>
                                            </li>
                                            <li>
                                                <a href='staff_mine.php?action=delmine'>Delete Mine</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="sidebar-dropdown">
                                    <a href="#">
                                        <span class="menu-text">Promo Codes</span>
                                    </a>
                                    <div class="sidebar-submenu">
                                        <ul>
                                            <li>
                                                <a href='staff_promo.php?action=addpromo'>Create Promo Code</a>
                                            </li>
                                            <li>
                                                <a href='staff_promo.php?action=viewpromo'>View Promo Codes</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="sidebar-dropdown">
                                    <a href="#">
                                        <span class="menu-text">Blacksmith</span>
                                    </a>
                                    <div class="sidebar-submenu">
                                        <ul>
                                            <li>
                                                <a href='staff_smelt.php?action=add'>Create Recipe</a>
                                            </li>
                                            <li>
                                                <a href='staff_smelt.php?action=del'>Delete Recipe</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <?php
                            }
                            if ($api->user->getStaffLevel($userid, 'admin')) 
                            {
                                ?>
                                <li class="sidebar-dropdown">
                                    <a href="#">
                                        <span class="menu-text">Items</span>
                                    </a>
                                    <div class="sidebar-submenu">
                                        <ul>
                                            <?php
                                            if ($api->user->getStaffLevel($userid, 'admin')) 
                                            {
                                                ?>
                                                <li>
                                                    <a href='staff_items.php?action=createitmgroup'>Create Item Group</a>
                                                </li>
                                                <li>
                                                    <a href='staff_items.php?action=create'>Create Item</a>
                                                </li>
                                                <li>
                                                    <a href='staff_items.php?action=edit'>Edit Item</a>
                                                </li>
                                                <li>
                                                   <a href='staff_items.php?action=delete'>Delete Item</a>
                                                </li>
                                                <?php
                                            }
                                            ?>
                                            <li>
                                                <a href='staff_items.php?action=giveitem'>Gift Item</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="sidebar-dropdown">
                                    <a href="#">
                                        <span class="menu-text">Users</span>
                                    </a>
                                    <div class="sidebar-submenu">
                                        <ul>
                                            <?php
                                            if ($api->user->getStaffLevel($userid, 'admin')) 
                                            {
                                                ?>
                                                <li>
                                                    <a href='staff_users.php?action=createuser'>Create User</a>
                                                </li>
                                                <li>
                                                    <a href='staff_users.php?action=edituser'>Edit User</a>
                                                </li>
                                                <li>
                                                    <a href='staff_users.php?action=deleteuser'>Delete User</a>
                                                </li>
                                                <li>
                                                   <a href='staff_users.php?action=changepw'>Change User's Password</a>
                                                </li>
                                                <li>
                                                   <a href='staff_settings.php?action=restore'>Restore Users</a>
                                                </li>
                                                <li>
                                                   <a href='staff_settings.php?action=staff'>Set User Level</a>
                                                </li>
                                                <?php
                                            }
                                            ?>
                                            <li>
                                                <a href='staff_users.php?action=masspayment'>Send Mass Payment</a>
                                            </li>
                                            <li>
                                                <a href='staff_users.php?action=reports'>View Player Reports</a>
                                            </li>
                                            <li>
                                                <a href='staff_users.php?action=logout'>Force Logout User</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="sidebar-dropdown">
                                    <a href="#">
                                        <span class="menu-text">Guilds</span>
                                    </a>
                                    <div class="sidebar-submenu">
                                        <ul>
                                            <li>
                                                <a href='staff_guilds.php?action=viewguild'>View Guild</a>
                                            </li>
                                            <li>
                                                <a href='staff_guilds.php?action=editguild'>Edit Guild</a>
                                            </li>
                                            <li>
                                                <a href='staff_guilds.php?action=delguild'>Delete Guild</a>
                                            </li>
                                            <li>
                                                <a href='staff_guilds.php?action=creditguild'>Credit Guild</a>
                                            </li>
                                            <li>
                                                <a href='staff_guilds.php?action=viewwars'>View Guild Wars</a>
                                            </li>
                                            <li>
                                                <a href='staff_guilds.php?action=addcrime'>Create Guild Crime</a>
                                            </li>
                                            <li>
                                                <a href='staff_guilds.php?action=delcrime'>Delete Guild Crime</a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <?php
                            }
                            ?>
                        </li>
                        <li class="header-menu">
                            <span><?php echo date('F j, Y') . " " . date('g:i:s a'); ?></span>
                        </li>
                    </ul>
                </div>
                <!-- sidebar-menu  -->
            </div>
            <!-- sidebar-footer  -->
            <div class="sidebar-footer">
                <div class="dropdown">
                    <a href="../notifications.php">
                        <i class="fa fa-bell"></i>
                        <span class="badge badge-pill badge-success notification"><?php echo $ir['notifications']; ?></span>
                    </a>
                </div>
                <div class="dropdown">
                    <a href="../inbox.php">
                        <i class="fa fa-envelope"></i>
                        <span class="badge badge-pill badge-success notification"><?php echo $ir['mail']; ?></span>
                    </a>
                </div>
                <div class="dropdown">
                    <a href="../preferences.php">
                        <i class="fa fa-cog"></i>
                    </a>
                </div>
                <div>
                    <a href="../logout.php">
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
            require "../lib/dev_help.php";
            $IP = $db->escape($_SERVER['REMOTE_ADDR']);
            $ipq = $db->query("SELECT `ip_id` FROM `ipban` WHERE `ip_ip` = '{$IP}'");
            if ($db->num_rows($ipq) > 0) {
                alert('danger', "Uh Oh!", "You have been IP Banned. Please contact support.", false);
                die($h->endpage());
            }
            $fed = $db->fetch_row($db->query("SELECT * FROM `fedjail` WHERE `fed_userid` = {$userid}"));
            if ($fed['fed_out'] < $time) {
                $db->query("UPDATE `users` SET `fedjail` = 0 WHERE `userid` = {$userid}");
                $db->query("DELETE FROM `fedjail` WHERE `fed_userid` = {$userid}");
            }
            if ($ir['fedjail'] > 0) {
                alert('info', "Federal Dungeon!", "You have been placed in the Federal Dungeon for
                        " . timeUntilParse($fed['fed_out']) . " You are in for the crime of <b>{$fed['fed_reason']}</b>", false);
                die($h->endpage());
            }
            if ($ir['mail'] > 0) {
                alert('info', "New Mail!", "You have {$ir['mail']} unread messages.", true, "../inbox.php", "View Inbox");
            }
            if ($ir['notifications'] > 0) {
                alert('info', "New Notifications!", "You have {$ir['notifications']} unread notifications.", true, '../notifications.php', "View Notifications");
            }
            if ($ir['announcements'] > 0) {
                alert('info', "New Announcements!", "You have {$ir['announcements']} unread announcements.", true, '../announcements.php', "View Announcements");
            }
            if ($api->user->inInfirmary($ir['userid'])) {
                $InfirmaryOut = $db->fetch_single($db->query("SELECT `infirmary_out` FROM `infirmary` WHERE `infirmary_user` = {$ir['userid']}"));
                $InfirmaryRemain = timeUntilParse($InfirmaryOut);
                alert('info', "Unconscious!", "You are in the infirmary for the next {$InfirmaryRemain}.", true, '../inventory', 'View Inventory');
            }
            if ($api->user->inDungeon($ir['userid'])) {
                $DungeonOut = $db->fetch_single($db->query("SELECT `dungeon_out` FROM `dungeon` WHERE `dungeon_user` = {$ir['userid']}"));
                $DungeonRemain = timeUntilParse($DungeonOut);
                alert('info', "Locked Up!", "You are in the dungeon for the next {$DungeonRemain}.", true, '../inventory', 'View Inventory');
            }
        }
    }

    function userdata($ir, $dosessh = 1)
    {
        global $db, $userid, $api;
        $IP = $db->escape($_SERVER['REMOTE_ADDR']);
        $db->query("UPDATE `users` SET `laston` = {$_SERVER['REQUEST_TIME']}, `lastip` = '{$IP}'  WHERE `userid` = {$userid}");
        if (!$ir['email']) {
            global $domain;
            die("<body>Your account is likely broken. Please contact admin@{$domain} and include your User ID.");
        }
        if (!isset($_SESSION['attacking'])) {
            $_SESSION['attacking'] = 0;
        }
        if ($dosessh && ($_SESSION['attacking'] || $ir['attacking'])) {
            $hosptime = randomNumber(10, 50);
            $api->user->setInfirmary($userid, $hosptime, "Ran from a fight");
            alert("warning", "Uh Oh!", "For leaving your previous fight, you were placed in the Infirmary for {$hosptime}
            minutes, and lost all your experience.", false);
            $db->query("UPDATE `users` SET `xp` = 0, `attacking` = 0 WHERE `userid` = $userid");
            $_SESSION['attacking'] = 0;
        }
    }

    function endpage()
    {
        global $db, $ir;
        $query_extra = '';
        if (isset($_GET['mysqldebug']) && $ir['user_level'] == 'Admin')
        {
            ?>
            <pre class='pre-scrollable'> <?php var_dump($db->queries) ?> </pre> <?php
        }
        ?>
        </div>
        </div>
        <!-- /.row -->

        </div>
        <!-- /.container -->
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css">
        <link rel="stylesheet" href="../css/game.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
        <!-- jQuery Version 3.4.0 -->
        <script src="https://code.jquery.com/jquery-3.4.0.min.js"></script>
        
        <!-- Core Bootstrap Javascript -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

        <!-- Other JavaScript -->
        <script src="../js/game.js"></script>
        <script src="../js/sidemenu.js"></script>
        <script src='https://www.google.com/recaptcha/api.js' async defer></script>
        <script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js"></script>
        <script src="https://cdn.rawgit.com/tonystar/bootstrap-hover-tabs/v3.1.1/bootstrap-hover-tabs.js" async defer></script>
        <script src="https://malihu.github.io/custom-scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
        <script type="text/javascript">
            jQuery(function ($) {
            $("#close-sidebar").click(function() {
              $(".page-wrapper").removeClass("toggled");
                localStorage.setItem("toggle", "toggled");
            });
            $("#show-sidebar").click(function() {
              $(".page-wrapper").addClass("toggled");
                localStorage.setItem("toggle", "");
            });
           
        });	
        </script>
        </body>
        </html>
    <?php
    }
}
