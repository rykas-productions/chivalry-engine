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
        global $ir, $set, $h, $db, $menuhide, $userid, $api, $time;
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
                <!-- CSS -->
                <?php
                if ($ir['theme'] == 1)
                {
                    ?>
                    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css">
                    <meta name="theme-color" content="#e7e7e7">
                <?php
                }
                else
                {
                    ?>
                    <link rel="stylesheet" href="../css/bootstrap-purple-min.css">
                    <meta name="theme-color" content="#2d135d">
                <?php
                }
                ?>
                <meta name="author" content="<?php echo $set['WebsiteOwner']; ?>">
                <?php echo "<title>{$set['WebsiteName']}</title>"; ?>
        </head>
        <?php
        if (empty($menuhide)) {
            $ir['mail'] = $db->fetch_single($db->query("SELECT COUNT(`mail_id`) FROM `mail` WHERE `mail_to` = {$ir['userid']} AND `mail_status` = 'unread'"));
            $ir['notifications'] = $db->fetch_single($db->query("SELECT COUNT(`notif_id`) FROM `notifications` WHERE `notif_user` = {$ir['userid']} AND `notif_status` = 'unread'"));
            ?>
            <body>
            <!-- Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <a class="navbar-brand" href="index.php"><?php echo $set['WebsiteName']; ?></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#CENGINENav"
                        aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="CENGINENav">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item active">
                            <a class="nav-link" href="../index.php"><?php echo "Back to Game"; ?></a>
                        </li>
                    </ul>
                    <div class="my-2 my-lg-0">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item">
                                <a class="nav-link"
                                   href="../inbox.php"><?php echo "Inbox <span class='badge badge-pill badge-primary'>{$ir['mail']}</span>"; ?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link"
                                   href="../notifications.php"><?php echo "Notifications <span class='badge badge-pill badge-primary'>{$ir['notifications']}</span>"; ?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="../inventory.php"><?php echo "Inventory"; ?></a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink"
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?php
                                    //User has a display picture, lets show it!
                                    if ($ir['display_pic']) {
                                        echo "<img src='{$ir['display_pic']}' width='24' height='24'>";
                                    }
                                    echo " Hello, {$ir['username']}!";
                                    ?>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                                    <a class="dropdown-item"
                                       href="../profile.php?user=<?php echo "{$ir['userid']}"; ?>"><i
                                            class="fa fa-fw fa-user"></i> <?php echo "Profile"; ?></a>
                                    <a class="dropdown-item" href="../preferences.php?action=menu"><i
                                            class="fa fa-fw fa-gear"></i><?php echo "Preferences"; ?></a>
                                    <?php
                                    //User is a staff member, so lets show the panel's link.
                                    if (in_array($ir['user_level'], array('Admin', 'Forum Moderator', 'Web Developer', 'Assistant'))) {
                                        ?>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="index.php"><i
                                                class="fa fa-fw fa fa-terminal"></i> <?php echo "Staff Panel"; ?></a>
                                    <?php
                                    }
                                    ?>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="../gamerules.php"><i
                                            class="fa fa-fw fa-server"></i> <?php echo "Game Rules"; ?></a>
                                    <a class="dropdown-item" href="../logout.php"><i
                                            class="fa fa-fw fa-power-off"></i> <?php echo "Log Out"; ?></a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <div class="container">
            <div class="row">
            <div class="col-sm-12 text-center">
            <noscript>
                <?php alert('info', "Information!", "Please enable Javascript.", false); ?>
            </noscript>
            <?php
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
                        " . TimeUntil_Parse($fed['fed_out']) . " You are in for the crime of <b>{$fed['fed_reason']}</b>", false);
                die($h->endpage());
            }
            if ($ir['mail'] > 0) {
                alert('info', "New Mail!", "You have {$ir['mail']} unread messages.", true, "../inbox.php", "View Inbox");
            }
            if ($ir['notifications'] > 0) {
                alert('info', "New Notifications!", "You have {$ir['notifications']} unread notifications.", true, '../notifications', "View Notifications");
            }
            if ($ir['announcements'] > 0) {
                alert('info', "New Announcements!", "You have {$ir['announcements']} unread announcements.", true, '../announcements.php', "View Announcements");
            }
            if ($api->UserStatus($ir['userid'], 'infirmary') == true) {
                $InfirmaryOut = $db->fetch_single($db->query("SELECT `infirmary_out` FROM `infirmary` WHERE `infirmary_user` = {$ir['userid']}"));
                $InfirmaryRemain = TimeUntil_Parse($InfirmaryOut);
                alert('info', "Unconscious!", "You are in the infirmary for the next {$InfirmaryRemain}.", true, '../inventory', 'View Inventory');
            }
            if ($api->UserStatus($ir['userid'], 'dungeon') == true) {
                $DungeonOut = $db->fetch_single($db->query("SELECT `dungeon_out` FROM `dungeon` WHERE `dungeon_user` = {$ir['userid']}"));
                $DungeonRemain = TimeUntil_Parse($DungeonOut);
                alert('info', "Locked Up!", "You are in the dungeon for the next {$DungeonRemain}.", true, '../inventory', 'View Inventory');
            }
            date_default_timezone_set($ir['timezone']);
        }
    }

    function userdata($ir, $dosessh = 1)
    {
        global $db, $userid;;
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
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="../css/bs2.css">

        <!-- jQuery Version 3.2.1 -->
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js"></script>

        <!-- Other JavaScript -->
        <script src="../js/game.js" async defer></script>
        <script src='https://www.google.com/recaptcha/api.js' async defer></script>
        <script src="https://cdn.rawgit.com/tonystar/bootstrap-hover-tabs/v3.1.1/bootstrap-hover-tabs.js" async
                defer></script>
        </body>
        <footer>
            <p>
                <br/>
                <?php
                echo "<hr />
					Time is now " . date('F j, Y') . " " . date('g:i:s a') . "<br />
					Powered with codes by TheMasterGeneral. View source on <a href='https://github.com/MasterGeneral156/chivalry-engine'>Github</a>.";
                ?>
                &copy; <?php echo date("Y");
                echo "<br/>{$db->num_queries} Queries Executed.{$query_extra}<br />";
                ?>
            </p>
        </footer>
        </html>
    <?php
    }
}
