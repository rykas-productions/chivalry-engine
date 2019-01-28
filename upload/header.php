<?php

/*
	File:		header.php
	Created: 	4/5/2016 at 12:05AM Eastern Time
	Info: 		Class file to load the template in-game.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/

class headers
{
    function startheaders()
    {
        global $ir, $set, $h, $db, $menuhide, $userid, $macropage, $api, $time;
        //Load the meta headers.
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <center>
                <meta charset="utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <meta name="description" content="<?php echo $set['Website_Description']; ?>">
                <meta property="og:title" content="<?php echo $set['WebsiteName']; ?>"/>
                <meta property="og:description" content="<?php echo $set['Website_Description']; ?>"/>
                <meta property="og:image" content=""/>
                <link rel="shortcut icon" href="" type="image/x-icon"/>
                <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
                <link rel="stylesheet" href="//malihu.github.io/custom-scrollbar/jquery.mCustomScrollbar.min.css">
                <link rel="stylesheet" href="css/sidebar-themes.css">
                <meta name="theme-color" content="#e7e7e7">
                <meta name="author" content="<?php echo $set['WebsiteOwner']; ?>">
                <?php echo "<title>{$set['WebsiteName']}</title>"; ?>
        </head>
    <?php
    //If the called script wants the menu hidden.
    if (empty($menuhide))
    {
    //Select count of user's unread messages.
    $ir['mail'] = $db->fetch_single($db->query("SELECT COUNT(`mail_id`) FROM `mail` WHERE `mail_to` = {$ir['userid']} AND `mail_status` = 'unread'"));
    //Select count of user's unread notifications.
    $ir['notifications'] = $db->fetch_single($db->query("SELECT COUNT(`notif_id`) FROM `notifications` WHERE `notif_user` = {$ir['userid']} AND `notif_status` = 'unread'"));
    $energy = $api->UserInfoGet($userid, 'energy', true);
    $brave = $api->UserInfoGet($userid, 'brave', true);
    $will = $api->UserInfoGet($userid, 'will', true);
    $xp = round($ir['xp'] / $ir['xp_needed'] * 100);
    $hp = $api->UserInfoGet($userid, 'hp', true);
    ?>
        <body>
        <div class="page-wrapper default-theme sidebar-bg toggled">
        <a id="show-sidebar" class="btn btn-sm btn-dark" href="#">
            <i class="fas fa-bars"></i>
          </a>
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
                            User Info<br />
                            <?php  
                            echo "Energy {$energy}%<br />
                            Brave {$brave}%<br />
                            Will {$will}%<br />
                            XP {$xp}%<br />
                            HP {$hp}%"; ?>
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
                            <a href="inventory.php">
                                <span class="menu-text">Inventory</span>
                            </a>
                        </li>
                        <li>
                            <a href="explore.php">
                                <span class="menu-text">Explore</span>
                            </a>
                        </li>
                        <li class="header-menu">
                            <span>Activities</span>
                        </li>
                        <li>
                            <a href="gym.php">
                                <span class="menu-text">Gym</span>
                            </a>
                        </li>
                        <li>
                            <a href="criminal.php">
                                <span class="menu-text">Crimes</span>
                            </a>
                        </li>
                        <li>
                            <a href="academy.php">
                                <span class="menu-text">Academy</span>
                            </a>
                        </li>
                        <li>
                            <a href="dungeon.php">
                                <span class="menu-text">Dungeon</span>
                            </a>
                        </li>
                        <li>
                            <a href="infirmary.php">
                                <span class="menu-text">Infirmary</span>
                            </a>
                        </li>
                        <li class="header-menu">
                            <span>Social</span>
                        </li>
                        <li>
                            <a href="forums.php">
                                <span class="menu-text">Forums</span>
                            </a>
                        </li>
                        <li>
                            <a href="newspaper.php">
                                <span class="menu-text">Newspaper</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- sidebar-menu  -->
            </div>
            <!-- sidebar-footer  -->
            <div class="sidebar-footer">
                <div class="dropdown">
                    <a href="notifications.php">
                        <i class="fa fa-bell"></i>
                        <span class="badge badge-pill badge-success notification"><?php echo $ir['notifications']; ?></span>
                    </a>
                </div>
                <div class="dropdown">
                    <a href="inbox.php">
                        <i class="fa fa-envelope"></i>
                        <span class="badge badge-pill badge-success notification"><?php echo $ir['mail']; ?></span>
                    </a>
                </div>
                <div class="dropdown">
                    <a href="preferences.php">
                        <i class="fa fa-cog"></i>
                    </a>
                </div>
                <div>
                    <a href="logout.php">
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
            <?php
            //User doesn't have javascript turned on, so lets tell them.
            alert('info', "Uh Oh!", "Please enable Javascript. Many features of the game will not work without it.", false);
            ?>
        </noscript>
    <?php
    $IP = $db->escape($_SERVER['REMOTE_ADDR']);
    $ipq = $db->query("SELECT `ip_id` FROM `ipban` WHERE `ip_ip` = '{$IP}'");
    //User's IP is banned, so lets stop access.
    if ($db->num_rows($ipq) > 0) {
        alert('danger', "Uh Oh!", "You have been IP banned.", false);
        die($h->endpage());
    }
    $fed = $db->fetch_row($db->query("SELECT * FROM `fedjail` WHERE `fed_userid` = {$userid}"));
    echo "<b><a href='donator.php' class='text-danger'>Donate to {$set['WebsiteName']} and you'll receive many cool perks!</a></b><br />";
    //User's federal jail sentence is completed. Let them play again.
    if ($fed['fed_out'] < $time) {
        $db->query("UPDATE `users` SET `fedjail` = 0 WHERE `userid` = {$userid}");
        $db->query("DELETE FROM `fedjail` WHERE `fed_userid` = {$userid}");
    }
    //User is in federal jail. Stop their access.
    if ($ir['fedjail'] > 0) {
        alert('info', "Federal Dungeon!", "You are locked away in Federal Dungeon for the next
					    " . TimeUntil_Parse($fed['fed_out']) . ". You were placed in here for <b>{$fed['fed_reason']}</b>", false);
        die($h->endpage());
    }
    //Tell user when they have unread messages, when they do.
    if ($ir['mail'] > 0) {
        alert('info', "New Mail!", "You have {$ir['mail']} unread messages.", true, 'inbox.php',"View Inbox");
    }
    //Tell user they have unread notifcations when they do.
    if ($ir['notifications'] > 0) {
        alert('info', "New Notifications!", "You have {$ir['notifications']} unread notifications.", true, 'notifications.php', "View Notifications");
    }
    //Tell user they have unread game announcements when they do.
    if ($ir['announcements'] > 0) {
        alert('info', "New Announcements!", "You have {$ir['announcements']} unread announcements.", true, 'announcements.php', "View Announcements");
    }
    //User is in the infirmary, tell them for how long.
    if ($api->UserStatus($ir['userid'], 'infirmary')) {
        $InfirmaryOut = $db->fetch_single($db->query("SELECT `infirmary_out` FROM `infirmary` WHERE `infirmary_user` = {$ir['userid']}"));
        $InfirmaryRemain = TimeUntil_Parse($InfirmaryOut);
        alert('info', "Unconscious!", "You are in the Infirmary for the next {$InfirmaryRemain}.", true, "inventory.php", "View Inventory");
    }
    //User is in the dungeon, tell them how long.
    if ($api->UserStatus($ir['userid'], 'dungeon')) {
        $DungeonOut = $db->fetch_single($db->query("SELECT `dungeon_out` FROM `dungeon` WHERE `dungeon_user` = {$ir['userid']}"));
        $DungeonRemain = TimeUntil_Parse($DungeonOut);
        alert('info', "Locked Up!", "You are in the dungeon for the next {$DungeonRemain}.", true, "inventory.php", "View Inventory");
    }
    //User needs to reverify with reCaptcha
    if (($ir['last_verified'] < ($time - $set['Revalidate_Time'])) || ($ir['need_verify'] == 1))
    {
    //ReCaptcha public or private key(s) are unspecifed in the game settings.
    if (empty($set['reCaptcha_public']) || empty($set['reCaptcha_private']))
    {
    ?>
        <script>alert('Please add the reCaptcha private and public keys.');</script>
    <?php
    die($h->endpage());
    }
    //Script calls for reCaptcha to be loaded.
    if (isset($macropage))
    {
    //Set User to need verified.
    $db->query("UPDATE `users` SET `need_verify` = 1 WHERE `userid` = {$userid}");
    echo "This is a needed evil. Please confirm you are not a bot."; ?>
        <form action='macro.php' method='post'>
            <center>
                <div class='g-recaptcha' data-theme='light'
                     data-sitekey='<?php echo $set['reCaptcha_public']; ?>'></div>
            </center>
            <input type='hidden' value='<?php echo $macropage; ?>' name='page'>
            <input type='submit' value="<?php echo "Confirm"; ?>" class="btn btn-primary" data-dismiss="modal">
        </form>
        <?php
        die($h->endpage());
    }
    }
        //Set user's timezone.
        date_default_timezone_set($ir['timezone']);
    }
    }

    function userdata($ir, $dosessh = 1)
    {
        global $db, $userid, $api;
        $IP = $db->escape($_SERVER['REMOTE_ADDR']);
        //Update the user as they browse the game.
        $db->query("UPDATE `users`
                    SET `laston` = {$_SERVER['REQUEST_TIME']}, 
                    `lastip` = '{$IP}' 
                    WHERE `userid` = {$userid}");
        //User's account does not have an email address.
        if (!$ir['email']) {
            global $domain;
            die("<body>Your account is broken. Please contact admin@{$domain} for assistance.");
        }
        //If the user's attacking is not stored in session.
        if (!isset($_SESSION['attacking'])) {
            $_SESSION['attacking'] = 0;
        }
        //If user does not end a fight correctly, take their XP and warn them.
        if ($dosessh && ($_SESSION['attacking'] || $ir['attacking'])) {
            $hosptime = Random(10, 50);
            $api->UserStatusSet($userid, 'infirmary', $hosptime, "Ran from a fight");
            alert("warning", "Uh Oh!", "For leaving your previous fight, you were placed in the Infirmary for {$hosptime}
            minutes, and lost all your experience.", false);
            $db->query("UPDATE `users` SET `xp` = 0, `attacking` = 0 WHERE `userid` = $userid");
            $_SESSION['attacking'] = 0;
        }
        $townguild = $db->fetch_single($db->query("SELECT `town_guild_owner` FROM `town` WHERE `town_id` = {$ir['location']}"));
        //User is in a guild, and the guild has control of the current town.
        if (($townguild == $ir['guild']) && ($townguild > 0) && ($ir['guild'] > 0)) {
            $encounterchance = Random(1, 1000);
            //User gets robbed!
            if ($encounterchance == 1) {
                $result = Random(1, 2);
                if ($result == 1) {
                    $infirmtime = Random(20, 60);
                    $api->UserStatusSet($userid, "infirmary", $infirmtime, "Attacked by Bandits");
                    $api->GameAddNotification($userid, "While randomly walking about in this town, you were attacked by
					    a group of bandits as a message to your guild leader.");
                }
                if ($result == 2) {
                    $api->GameAddNotification($userid, "While randomly walking about in this town, you successfully
					    fended off a group of bandits.");
                }
                if ($result == 3) {
                    $api->GameAddNotification($userid, "While randomly walking about in this town, you were attacked by
					    a group of bandits. Luckily, a player nearby was able to fight them off for you.");
                }
            }
        }
    }

    function endpage()
    {
        global $db, $ir, $StartTime, $set;
        $query_extra = '';
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
        <!-- /.row -->

        </div>
        <!-- /.container -->
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css">
        <link rel="stylesheet" href="css/game.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <!-- jQuery Version 3.2.1 -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>

        <!-- Other JavaScript -->
        <script src="js/game.js"></script>
        <script src="js/sidemenu.js"></script>
        <script src='https://www.google.com/recaptcha/api.js' async defer></script>
        <script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js"></script>
        <script src="https://cdn.rawgit.com/tonystar/bootstrap-hover-tabs/v3.1.1/bootstrap-hover-tabs.js" async defer></script>
        <script src="//malihu.github.io/custom-scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
        <script type="text/javascript">
	jQuery(function ($) {
        $(".sidebar-dropdown > a").click(function() {
      $(".sidebar-submenu").slideUp(200);
      if (
        $(this)
          .parent()
          .hasClass("active")
      ) {
        $(".sidebar-dropdown").removeClass("active");
        $(this)
          .parent()
          .removeClass("active");
      } else {
        $(".sidebar-dropdown").removeClass("active");
        $(this)
          .next(".sidebar-submenu")
          .slideDown(200);
        $(this)
          .parent()
          .addClass("active");
      }
    });

    $("#close-sidebar").click(function() {
      $(".page-wrapper").removeClass("toggled");
        localStorage.setItem("toggle", "toggled");
    });
    $("#show-sidebar").click(function() {
      $(".page-wrapper").addClass("toggled");
        localStorage.setItem("toggle", "");
    });
   
});	</script>
        </body>
        <footer>
            <p>
                <br/>
                <?php
                //Print copyright info, Chivalry Engine info, and current time.
                echo "<hr />
					Time is now " . date('F j, Y') . " " . date('g:i:s a') . "<br />
					{$set['WebsiteName']} &copy; " . date("Y") . " {$set['WebsiteOwner']}.";
                if ($ir['user_level'] == 'Admin' || $ir['user_level'] == 'Web Developer')
                    echo "<br/>{$db->num_queries} Queries Executed.{$query_extra}<br />";
                //Profile page loading putting profile in the URL GET.
                if (isset($_GET['profile'])) {
                    $ms = microtime() - $StartTime;
                    echo "Page loaded in {$ms} miliseconds.";
                }
                ?>
            </p>
        </footer>
        </html>
    <?php
    }
}
